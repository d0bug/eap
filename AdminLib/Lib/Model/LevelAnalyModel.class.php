<?php
class LevelAnalyModel {
	protected $dao = null;
	protected $paperQuesTable = 'ex_paper_questions';
	protected $paperTable = 'ex_exam_papers';
	protected $levelCfgTable = 'ex_level_cfg';
	protected $levelAnalyTable = 'ex_level_analys';
	protected $scoreTable = 'ex_exam_scores';
	protected $operator = '';
	
	public function __construct() {
		$this->dao = Dao::getDao();
		if(class_exists('User', false)) {
			$user = User::getLoginUser();
			$this->operator = $user->getUserKey();
		}
	}
	
	public function getLevelQuestions($examId, $subjectCode) {
		$examId = abs($examId);
		$subjectCode = SysUtil::safeString($subjectCode);
		$strQuery = 'SELECT ques.*
					 FROM ' . $this->paperQuesTable . ' ques,
					 	  ' . $this->paperTable . ' paper
					 WHERE paper.paper_id=ques.paper_id
					   AND paper.paper_type=' . $this->dao->quote('real') . '
					   AND ques.exam_id=' . abs($examId) . '
					   AND ques.subject_code=' . $this->dao->quote($subjectCode) . '
					   AND ques.is_remove=0
					   AND ques_level >0
					 ORDER BY ques.paper_id,ques.ques_level';
		$quesList = $this->dao->getAll($strQuery);
		$levelQuestions = array();
		$quesArray = array();
		$startChar = '★';
		foreach ($quesList as $ques) {
			$level = $ques['ques_level'];
			$paperId = $ques['paper_id'];
			$levelQuestions[$level]['level'] = $level;
			$levelQuestions[$level]['caption'] = str_repeat($startChar, $level);
			$levelQuestions[$level]['questions'][$paperId][] = 'ques_score_' . $ques['ques_seq'];
			if(false == isset($quesArray[$ques['ques_id']])) {
				$levelQuestions[$level]['ques_cnt'] ++;
				$levelQuestions[$level]['level_score'] += $ques['ques_score'];
				$quesArray[$ques['ques_id']] = true;
			}
		}
		return $levelQuestions;
	}
	
	public function getLevelStatistics($examId, $subjectCode, $isFront=false) {
		$strQuery = 'SELECT * FROM ' . $this->levelCfgTable . '
					 WHERE exam_id=' . abs($examId) . '
					   AND subject_code=' . $this->dao->quote($subjectCode);
		$dbLevelCfg = $this->dao->getRow($strQuery);
		if($dbLevelCfg && $isFront) {
			return unserialize($dbLevelCfg['level_statistics']);
		}
		$levelQuestions = $this->getLevelQuestions($examId, $subjectCode);
		$paperIds = array();
		$strQuery = 'SELECT COUNT(DISTINCT stu_code) stu_cnt';
		foreach ($levelQuestions as $levelNum=>$levelCfg) {
			$strQuery .= ',SUM(CASE ';
			foreach ($levelCfg['questions'] as $paperId=>$quesFields) {
				$strQuery .= ' WHEN paper_id=' . abs($paperId) . ' THEN ' . implode('+', $quesFields);
				$paperIds[$paperId] = $paperId;
			}
			$strQuery .= ' END) score_' . $levelNum;
		}
		$strQuery .= ' FROM ' . $this->scoreTable . '
					 WHERE paper_id IN (' . implode(',', $paperIds) . ')';
		if(false == $paperIds) {
			return array();
		}
		$statistics = $this->dao->getRow($strQuery);
		foreach ($levelQuestions as $levelNum=>$levelCfg) {
			$average = sprintf('%.2f', $statistics['score_' . $levelNum] / $statistics['stu_cnt']);
			$ratio = sprintf('%.2f', $statistics['score_' . $levelNum] / ($levelCfg['level_score'] * $statistics['stu_cnt']) * 100);
			$levelQuestions[$levelNum]['level_average'] = $average;
			$levelQuestions[$levelNum]['level_ratio'] = $ratio;
		}
		$levelQuestions = array_values($levelQuestions);
		$time = date('Y-m-d H:i:s');
		if($dbLevelCfg) {
			$strQuery = 'UPDATE ' . $this->levelCfgTable . '
						 SET level_statistics=' . $this->dao->quote(serialize($levelQuestions)) . ',
						     update_at=' . $this->dao->quote($time) . '
						 WHERE exam_id=' . abs($examId) . '
						   AND subject_code=' . $this->dao->quote($subjectCode);
		} else {
			$strQuery = 'INSERT INTO ' . $this->levelCfgTable . '
						(exam_id,subject_code,level_statistics,create_at,update_at)
						VALUES (' . abs($examId) . ',
								' . $this->dao->quote($subjectCode) . ',
								' . $this->dao->quote(serialize($levelQuestions)) . ',
								' . $this->dao->quote($time) . ',
								' . $this->dao->quote($time) . ')';
		}
		$this->dao->execute($strQuery);
		return $levelQuestions;
	}
	
	public function getAnalyList($examId, $subjectCode, $levelNum=null) {
		$strQuery = 'SELECT * FROM ' . $this->levelAnalyTable . '
					 WHERE exam_id=' . abs($examId) . '
					   AND subject_code=' . $this->dao->quote($subjectCode) . '
					   AND is_remove=0 ';
		if(null !== $levelNum) {
			$strQuery .= ' AND level_num=' . abs($levelNum);
		}
		$strQuery .= ' ORDER BY level_num, level_score DESC';
		return $this->dao->getAll($strQuery);
	}
	
	public function saveAnaly($analyInfo) {
		$examId = abs($analyInfo['examId']);
		$subjectCode = SysUtil::safeString($analyInfo['subjectCode']);
		$levelNum = abs($analyInfo['levelNum']);
		$levelScore = abs($analyInfo['levelScore']);
		$levelAnaly = SysUtil::safeString($analyInfo['levelAnaly']);
		$time = date('Y-m-d H:i:s');
		if($analyInfo['analyId']) {
			$analyId = SysUtil::uuid($analyInfo['analyId']);
			$strQuery = 'UPDATE ' . $this->levelAnalyTable . '
						 SET level_score=' . $levelScore . ',
						 	 level_analy=' . $this->dao->quote($levelAnaly) . ',
						 	 update_user=' . $this->dao->quote($this->operator) . ',
						 	 update_at=' . $this->dao->quote($time) . '
						 WHERE id=' . $this->dao->quote($analyId);
		} else {
			$analyId = SysUtil::uuid();
			$strQuery = 'INSERT INTO ' . $this->levelAnalyTable . '
						 (id,exam_id,subject_code,level_num,level_score,level_analy,
						  is_remove,create_user,create_at,update_user,update_at)
						 VALUES (' . $this->dao->quote($analyId) . ',
						 		 ' . $examId . ',
						 		 ' . $this->dao->quote($subjectCode) . ',
						 		 ' . $levelNum . ',
						 		 ' . $levelScore . ',
						 		 ' . $this->dao->quote($levelAnaly) . ',0,
						 		 ' . $this->dao->quote($this->operator) . ',
						 		 ' . $this->dao->quote($time) . ',
						 		 ' . $this->dao->quote($this->operator) . ',
						 		 ' . $this->dao->quote($time) . ')';
		}
		if ($this->dao->execute($strQuery)) {
			return array('success'=>true);
		}
		return array('errorMsg'=>'试题等级分析保存失败');
	}
	
	public function delAnaly($analyId) {
		$strQuery = 'UPDATE ' . $this->levelAnalyTable . '
					 SET is_remove=' . time() . ',
					 	 update_user=' . $this->dao->quote($this->operator) . ',
					 	 update_at=' . $this->dao->quote(date('Y-m-d H:i:s')) . '
					 WHERE id=' . $this->dao->quote($analyId);
		if($this->dao->execute($strQuery)) {
			return array('success'=>true);
		}
		return array('errorMsg'=>'等级分析删除失败');
	}
	
	public function getLevelAnaly($stuScoreArray) {
		foreach ($stuScoreArray as $paperId=>$paperCfg) {
			if($paperCfg['paper_type'] == 'real') {
				$examId = $paperCfg['exam_id'];
				$subjectCode = $paperCfg['subject_code'];
				$levelStatistics = $this->getLevelStatistics($examId, $subjectCode, true);
				break;
			}
		}
		$analyData = array();
		if($levelStatistics) {
			foreach ($levelStatistics as $levelCfg) {
				$levelNum = $levelCfg['level'];
				$analyData[$levelNum]['level_num'] = $levelNum;
				$analyData[$levelNum]['level_caption'] = $levelCfg['caption'];
				$analyData[$levelNum]['level_total'] = $levelCfg['level_score'];
				$analyData[$levelNum]['level_average'] = $levelCfg['level_average'];
				$analyData[$levelNum]['level_ratio'] = $levelCfg['level_ratio'];
				foreach ($levelCfg['questions'] as $paperId=>$fields) {
					if(isset($stuScoreArray[$paperId])) {
						foreach ($fields as $field) {
							$analyData[$levelNum]['stu_score'] += $stuScoreArray[$paperId]['score_info'][$field];
						}
					}
				}
				$analyData[$levelNum]['stu_ratio'] = sprintf('%.2f', $analyData[$levelNum]['stu_score'] / $analyData[$levelNum]['level_total'] * 100);
			}
		}
		$analyList = $this->getAnalyList($examId, $subjectCode);
		foreach ($analyList as $analy) {
			$levelNum = $analy['level_num'];
			if(false == isset($analyData[$levelNum]['level_analy'])) {
				if(floatval($analyData[$levelNum]['stu_score']) >= floatval($analy['level_score'])) {
					$analyData[$levelNum]['level_analy'] = $analy['level_analy'];
				}
			}
		}
		
		return $analyData;
	}
}
?>