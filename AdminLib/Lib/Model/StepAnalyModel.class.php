<?php
class StepAnalyModel{
	protected $dao = null;
	protected $tableName = 'ex_step_analys';
	protected $examQuesTable = 'ex_exam_questions';
	protected $paperQuesTable = 'ex_paper_questions';
	protected $scoreTable = 'ex_exam_scores';
	protected $stepCfgTable = 'ex_step_cfg';
	protected $operator = '';
	public function __construct() {
		$this->dao = Dao::getDao();
		if (class_exists('User', false)) {
			$operator = User::getLoginUser();
			$this->operator = $operator->getUserKey();
		}
	}
	
	public function getAnalyLevels() {
		return array('高档（差），中档（差），低档（差）', #0
					 '高档（差），中档（差），低档（好）', #1
					 '高档（差），中档（好），低档（差）', #2
					 '高档（差），中档（好），低档（好）', #3
					 '高档（好），中档（差），低档（差）', #4
					 '高档（好），中档（差），低档（好）', #5
					 '高档（好），中档（好），低档（差）', #6
					 '高档（好），中档（好），低档（好）', #7
					 );
	}
	
	/**
	 * 1,2 => 1
	 * 3,4 => 2
	 * 5   => 3
	 */
	public function getQuestions($examId, $subjectCode) {
		static $stepQuestions = array();
		if(false == $stepQuestions) {
			$strQuery = 'SELECT distinct exam_id,paper_id,subject_code,ques_id,
								ques_score,ques_knowledge,ques_level,ques_seq 
						 FROM ' . $this->paperQuesTable . '
						 WHERE exam_id=' . abs($examId) . '
						   AND subject_code=' . $this->dao->quote($subjectCode) . '
						   AND is_remove=0
						 ORDER BY paper_id,ques_level,ques_seq';
			$quesList = $this->dao->getAll($strQuery);
			#$stepQuestions = array(1=>array(), 2=>array(), 3=>array());
			$stepQuestions = array();
			$levelSteps = array(1=>1, 2=>1, 3=>2, 4=>2, 5=>3);
			$stepLevels = array(1=>'1级, 2级', 2=>'3级, 4级', 3=>'5级');
			$stepCaptions = array('', '低档题', '中档题', '高档题');
			foreach ($quesList as $ques) {
				if($ques['ques_level']) {
					$step = $levelSteps[abs($ques['ques_level'])];
					if(false == isset($stepQuestions[$step])) {
						$stepQuestions[$step] = array();
					}
					$stepQuestions[$step]['levels'] = $stepLevels[$step];
					$stepQuestions[$step]['caption'] = $stepCaptions[$step];
					if(false == isset($stepQuestions[$step]['ques_id'][$ques['ques_id']])) {
						$stepQuestions[$step]['step_score'] += $ques['ques_score'];
						$stepQuestions[$step]['ques_count'] += 1;
					}
					$stepQuestions[$step]['ques_id'][$ques['ques_id']] = 1;
					$stepQuestions[$step]['paper'][$ques['paper_id']][] = 'ques_score_' . abs($ques['ques_seq']);
				}
			}
		}
		return $stepQuestions;
	}
	
	public function getStatistics($examId, $subjectCode) {
		$stepQuestions = $this->getQuestions($examId, $subjectCode);
		if($stepQuestions) {
			$strQuery = 'SELECT COUNT(DISTINCT stu_code) stu_cnt';
			foreach ($stepQuestions as $step=>$stepCfg){
				if(false == $stepCfg['paper']) {
					$strQuery .= ',0 step_score_' . $step;
				} else {
					$strQuery .= ',SUM(CASE ';
					foreach ($stepCfg['paper'] as $paperId=>$quesFields) {
						$strQuery .= ' WHEN paper_id=' . $paperId . ' THEN ' . implode('+', $quesFields) . ' ';
					}
					$strQuery .= ' END) step_score_' . $step;
				}
			}
			$strQuery .= ' FROM ' . $this->scoreTable . '
						  WHERE exam_id=' . abs($examId) . '
						    AND subject_code=' . $this->dao->quote($subjectCode);
			$stepResult = $this->dao->getRow($strQuery);
			$stepRatios = array();
			foreach ($stepQuestions as $step=>$stepCfg) {
				$stepQuestions[$step]['step_ratio'] = sprintf('%.2f', $stepResult['step_score_' . $step] / ($stepResult['stu_cnt'] * $stepCfg['step_score']) * 100);
				$stepQuestions[$step]['step_average'] = $stepResult['step_score_' . $step] / $stepResult['stu_cnt'];
			}
		}
		return $stepQuestions;
	}
	
	public function saveStepCfg($stepCfg) {
		$strQuery = 'SELECT count(1) FROM ' . $this->stepCfgTable . '
					 WHERE exam_id=' . abs($stepCfg['exam_id']) . '
					   AND subject_code=' . $this->dao->quote($stepCfg['subject_code']);
		$dbStepCfg = $this->dao->getOne($strQuery);
		$time = date('Y-m-d H:i:s');
		if($dbStepCfg) {
			$strQuery = 'UPDATE ' . $this->stepCfgTable . '
						 SET step_statistics=' . $this->dao->quote($stepCfg['step_statistics']) . ',
						 	 step_score=' . $this->dao->quote(json_encode($stepCfg['step_score'])) . ',
						 	 update_user=' . $this->dao->quote($this->operator) . ',
						 	 update_at=' . $this->dao->quote($time) . '
						 WHERE exam_id=' . abs($stepCfg['exam_id']) . '
						   AND subject_code=' . $this->dao->quote($stepCfg['subject_code']);
		} else {
			$id = SysUtil::uuid();
			$strQuery = 'INSERT  INTO ' . $this->stepCfgTable . '
						 (id,exam_id,subject_code,step_statistics,step_score,create_user,create_at,update_user,update_at)
						 VALUES (' . $this->dao->quote($id) . ',
						 		 ' . abs($stepCfg['exam_id']) . ',
						 		 ' . $this->dao->quote($stepCfg['subject_code']) . ',
						 		 ' . $this->dao->quote($stepCfg['step_statistics']) . ',
						 		 ' . $this->dao->quote(json_encode($stepCfg['step_score'])) . ',
						 		 ' . $this->dao->quote($this->operator) . ',
						 		 ' . $this->dao->quote($time) . ',
						 		 ' . $this->dao->quote($this->operator) . ',
						 		 ' . $this->dao->quote($time) . ')';
		}
		if ($this->dao->execute($strQuery)) {
			return array('success'=>true);
		}
		return array('errorMsg'=>'分档分数设置失败');
	}
	
	
	public function findStepCfg($examId, $subjectCode, $isFront = false) {
		$strQuery = 'SELECT * FROM ' . $this->stepCfgTable . '
					 WHERE exam_id=' . abs($examId) . '
					   AND subject_code=' . $this->dao->quote($subjectCode);
		$stepCfg = $this->dao->getRow($strQuery);
		$update = $stepCfg ? true :false;
		if($stepCfg && $isFront) {
			$stepCfg['step_score'] = json_decode($stepCfg['step_score'], true);
			$stepCfg['step_statistics'] = SysUtil::jsonDecode($stepCfg['step_statistics']);
			return $stepCfg;
		} else {
			$stepCfg = $this->getStatistics($examId, $subjectCode);
			$stepScore = array();
			if($stepCfg['step_score']) {
				$stepScore = SysUtil::jsonDecode($stepCfg['step_score']);
			} else {
				foreach ($stepCfg as $step=>$cfg) {
					$stepScore[$step] = $cfg['step_average'];
				}
			}
			$saveData = array();
			$saveData['exam_id'] = $examId;
			$saveData['subject_code'] = $subjectCode;
			$saveData['step_statistics'] = SysUtil::jsonEncode($stepCfg);
			$saveData['step_score'] = $stepScore;
			$this->saveStepCfg($saveData);
			$saveData['step_statistics'] = $stepCfg;
			return $saveData;
		}
	}
	
	
	public function getAnalys($examId, $subjectCode, $stepValue=null) {
		$strQuery = 'SELECT *
					 FROM ' . $this->tableName . '
					 WHERE exam_id=' . abs($examId) . '
					   AND subject_code=' . $this->dao->quote($subjectCode) . '
					   AND is_remove=0';
		if(null !== $stepValue) {
			$strQuery .= ' AND analy_level=' . abs($stepValue);
		}
		$strQuery .= ' ORDER BY analy_level';
		$analys =  $this->dao->getAll($strQuery);
		$analyLevels = $this->getAnalyLevels();
		foreach ($analys as $key=>$analy) {
			$analys[$key]['level_text'] = $analyLevels[$analy['analy_level']];
		}
		return $analys;
	}
	
	public function findAnaly($analyId){
		$strQuery = 'SELECT *
					 FROM ' . $this->tableName . '
					 WHERE id=' . $this->dao->quote($analyId);
		return $this->dao->getRow($strQuery);
	}
	
	public function saveAnaly($analyInfo) {
		$analyLevel = abs($analyInfo['analy_level']);
		$analyText = SysUtil::safeString($analyInfo['analy_text']);
		$time = date('Y-m-d H:i:s');
		if($analyInfo['analyId']) {
			$analyId = SysUtil::uuid($analyInfo['analyId']);
			$strQuery = 'UPDATE ' . $this->tableName . '
						 SET analy_level=' . abs($analyLevel) . ',
						     analy_text=' . $this->dao->quote($analyText) . ',
						     update_user=' . $this->dao->quote($this->operator) . ',
						     update_at=' . $this->dao->quote($time) . '
						 WHERE id=' . $this->dao->quote($analyId);
		} else {
			$analyId = SysUtil::uuid();
			$strQuery = 'INSERT INTO ' . $this->tableName . '
						 (id,exam_id,subject_code,analy_level,analy_text,is_remove,create_user,create_at,update_user,update_at)
						 VALUES (' . $this->dao->quote($analyId) . ',
						 		 ' . abs($analyInfo['exam_id']) . ',
						 		 ' . $this->dao->quote($analyInfo['subject_code']) . ',
						 		 ' . abs($analyLevel) . ',
						 		 ' . $this->dao->quote($analyText) . ',0,
						 		 ' . $this->dao->quote($this->operator) . ',
						 		 ' . $this->dao->quote($time) . ',
						 		 ' . $this->dao->quote($this->operator) . ',
						 		 ' . $this->dao->quote($time) . ')';
		}
		$scoreModel = D('Score');
		$scoreModel->updateCache();
		if($this->dao->execute($strQuery)) {
			return array('success'=>true);
		}
		return array('errorMsg'=>'分档话术设置失败,请检查是否重复设置');
	}
	
	public function delAnaly($analyId) {
		$strQuery = 'UPDATE ' . $this->tableName . '
					 SET is_remove=' . time() . ',
					 	 update_user=' . $this->dao->quote($this->operator) . ',
					 	 update_at=' . $this->dao->quote(date('Y-m-d H:i:s')) . '
					 WHERE id=' . $this->dao->quote($analyId);
		return $this->dao->execute($strQuery);
	}
	
	public function getStepAnaly($stuScoreArray) {
		foreach ($stuScoreArray as $paperId=>$paperCfg) {
			$examId = $paperCfg['exam_id'];
			$subjectCode = $paperCfg['subject_code'];
			$stepStatistics = $this->findStepCfg($examId, $subjectCode, true);
			break;
		}
		$analyData = array();
		$levelValues = array('1'=>1, '2'=>2, '3'=>4);
		if($stepStatistics) {
			$stepValue = 0;
			foreach ($stepStatistics['step_statistics'] as $stepLevel=>$stepCfg) {
				$analyData['step_levels'][$stepLevel]['step_level'] = $stepLevel;
				$analyData['step_levels'][$stepLevel]['step_caption'] = $stepCfg['caption'];
				$analyData['step_levels'][$stepLevel]['step_total'] = $stepCfg['step_score'];
				$analyData['step_levels'][$stepLevel]['ques_count'] = $stepCfg['ques_count'];
				$analyData['step_levels'][$stepLevel]['level_score'] = $stepStatistics['step_score'][$stepLevel];
				foreach ($stepCfg['paper'] as $paperId=>$fields) {
					if(isset($stuScoreArray[$paperId])) {
						foreach ($fields as $field) {
							$analyData['step_levels'][$stepLevel]['stu_score'] += $stuScoreArray[$paperId]['score_info'][$field];
						}
					}
				}
				if($analyData['step_levels'][$stepLevel]['stu_score'] >= $stepStatistics['step_score'][$stepLevel]) {
					$stepValue |= $levelValues[$stepLevel];
					$analyData['step_levels'][$stepLevel]['level_text'] = '好';
				} else {
					$analyData['step_levels'][$stepLevel]['level_text'] = '差';
				}
			}
			
			
			$analyList = $this->getAnalys($examId, $subjectCode, $stepValue);
			$analyData['step_value'] = $stepValue;
			$analyData['step_analy'] = $analyList[0]['analy_text'];
		}
		
		return $analyData;
	}
}
?>