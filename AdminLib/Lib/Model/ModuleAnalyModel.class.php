<?php
class ModuleAnalyModel {
	protected $dao = null;
	protected $operator = '';
	protected $paperQuesTable = 'ex_paper_questions';
	protected $scoreTable = 'ex_exam_scores';
	protected $moduleCfgTable = 'ex_module_cfg';
	protected $moduleAnalyTable = 'ex_module_analys';
	
	public function __construct() {
		$this->dao = Dao::getDao();
		if(class_exists('User', false)) {
			$user = User::getLoginUser();
			$this->operator = $user->getUserKey();
		}
	}
	
	public function getModuleQuestions($examId, $subjectCode) {
		$knowledgeAnalyModel = D('KnowledgeAnaly');
		list($quesList, $knowledgeList) = $knowledgeAnalyModel->getQuesKnowledge($examId, $subjectCode);
		$moduleQuesArray = array();
		
		foreach ($knowledgeList as $ques) {
			$moduleQuesArray[$ques['ques_id']]['module_code'] = $ques['module_code'];
			$moduleQuesArray[$ques['ques_id']]['module_caption'] = $ques['module_caption'];
		}
		
		$moduleQuestions = array();
		$quesIdArray = array();
		foreach ($quesList as $ques) {
			$quesId = $ques['ques_id'];
			if(isset($moduleQuesArray[$quesId])) {
				$moduleCode = $moduleQuesArray[$quesId]['module_code'];
				if(false == isset($moduleQuestions[$moduleCode])) {
					$moduleCaption = $moduleQuesArray[$quesId]['module_caption'];
					$moduleQuestions[$moduleCode]['module_code'] = $moduleCode;
					$moduleQuestions[$moduleCode]['module_caption'] = $moduleCaption;
				}
				if(false == isset($quesIdArray[$quesId])) {
					$moduleQuestions[$moduleCode]['ques_cnt'] ++;
					$moduleQuestions[$moduleCode]['module_score'] += $ques['ques_score'];
					$quesIdArray[$quesId] = true;
				}
				$moduleQuestions[$moduleCode]['questions'][$ques['paper_id']][] = 'ques_score_' . $ques['ques_seq'];
			}
		}
		
		ksort($moduleQuestions);
		return $moduleQuestions;
	}
	
	public function getModuleStatistics($examId, $subjectCode, $isFront = false) {
		#查询数据库中是否存在模块分析缓存数据
		$strQuery = 'SELECT module_statistics FROM ' . $this->moduleCfgTable . '
					 WHERE exam_id=' . abs($examId) . '
					   AND subject_code=' . $this->dao->quote($subjectCode);
		$dbModuleStatistics = $this->dao->getOne($strQuery);
		#若为前台调用，则始终从数据库读取缓存，读不到时进行计算
		if($isFront) {
			if($dbModuleStatistics) {
				$moduleStatistics = unserialize($dbModuleStatistics);
				return $moduleStatistics;
			}
		}
		#后台调用实时更新
		$moduleQuestions = $this->getModuleQuestions($examId, $subjectCode);
		$paperIdArray = array(0);
		if(false == $moduleQuestions) {
			return array();
		}
		
		$strQuery = 'SELECT count(distinct stu_code) stu_cnt';
		foreach ($moduleQuestions as $moduleCode=>$moduleCfg) {
			$strQuery .= ',SUM(CASE ';
			foreach ($moduleCfg['questions'] as $paperId=>$quesIds) {
				$strQuery .= ' WHEN paper_id=' . $paperId . ' THEN ' . implode('+', $quesIds);
				$paperIdArray[$paperId] = $paperId;
			}
			$strQuery .= ' END) score_' . $moduleCode;
		}
		$strQuery .= ' FROM ' . $this->scoreTable . '
				WHERE paper_id IN (' . implode(',', $paperIdArray) . ')';
		if(false == $paperIdArray) return array();
		$statisticsRow = $this->dao->getRow($strQuery);
		foreach ($moduleQuestions as $moduleCode=>$moduleCfg) {
			$scoreField = strtolower('score_' . $moduleCode);
			$moduleQuestions[$moduleCode]['module_average'] = sprintf('%.2f',$statisticsRow[$scoreField] / $statisticsRow['stu_cnt']);
			$moduleQuestions[$moduleCode]['module_ratio'] = sprintf('%.2f', $statisticsRow[$scoreField] / ($moduleCfg['module_score'] * $statisticsRow['stu_cnt']) * 100);
		}
		$moduleStatistics = $moduleQuestions;
		$time = date('Y-m-d H:i:s');
		if($dbModuleStatistics) {
			$strQuery = 'UPDATE ' . $this->moduleCfgTable . '
						 SET module_statistics=' . $this->dao->quote(serialize($moduleStatistics)) . ',
						     update_at=' . $this->dao->quote($time) . '
						 WHERE exam_id=' . abs($examId) . '
						   AND subject_code=' . $this->dao->quote($subjectCode);
		} else {
			$strQuery = 'INSERT INTO ' . $this->moduleCfgTable . '
						 (exam_id,subject_code,module_statistics,create_at,update_at)
						 VALUES (' . abs($examId) . ',
						 		 ' . $this->dao->quote($subjectCode) . ',
						 		 ' . $this->dao->quote(serialize($moduleStatistics)) . ',
						 		 ' . $this->dao->quote($time) . ',
						 		 ' . $this->dao->quote($time) . ')';
		}
		$this->dao->execute($strQuery);
		return $moduleStatistics;
	}
	
	public function getAnalyList($examId, $subjectCode, $moduleCode='') {
		$strQuery = 'SELECT * FROM ' . $this->moduleAnalyTable . '
					 WHERE exam_id=' . abs($examId) . '
					   AND subject_code=' . $this->dao->quote($subjectCode) . '
					   AND is_remove=0';
		if ($moduleCode) {
			$strQuery .= ' AND module_code=' . $this->dao->quote($moduleCode);
		}
		$strQuery .= ' ORDER BY module_code, module_score DESC';
		$analyList = $this->dao->getAll($strQuery);
		return $analyList;
	}
	
	public function save($analyInfo) {
		$time = date('Y-m-d H:i:s');
		$examId = abs($analyInfo['examId']);
		$subjectCode = SysUtil::safeString($analyInfo['subjectCode']);
		$moduleCode = SysUtil::safeString($analyInfo['moduleCode']);
		$moduleScore = abs($analyInfo['moduleScore']);
		$moduleAnaly = SysUtil::safeString($analyInfo['moduleAnaly']);
		if($analyInfo['analyId']) {
			$analyId = SysUtil::uuid($analyInfo['analyId']);
			$strQuery = 'UPDATE ' . $this->moduleAnalyTable . '
					 	 SET module_score=' . abs($moduleScore) . ',
					 	 	 module_analy=' . $this->dao->quote($moduleAnaly) . ',
					 	 	 update_user=' . $this->dao->quote($this->operator) . ',
					 	 	 update_at=' . $this->dao->quote($time) . '
					 	 WHERE id=' . $this->dao->quote($analyId);
		} else {
			$analyId = SysUtil::uuid();
			$strQuery = 'INSERT INTO ' . $this->moduleAnalyTable . '
						 (id,exam_id,subject_code,module_code,module_score,module_analy,
						  is_remove,create_user,create_at,update_user,update_at)
						  VALUES (' . $this->dao->quote($analyId) . ',
						  		  ' . abs($examId) . ',
						  		  ' . $this->dao->quote($subjectCode) . ',
						  		  ' . $this->dao->quote($moduleCode) . ',
						  		  ' . abs($moduleScore) . ',
						  		  ' . $this->dao->quote($moduleAnaly) . ',0,
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
		return array('errorMsg'=>'模块分析保存失败');
	}
	
	public function delAnaly($analyId) {
		$strQuery = 'UPDATE ' . $this->moduleAnalyTable . '
					 SET is_remove=' . time() . ',
					 	 update_user=' . $this->dao->quote($this->operator) . ',
					 	 update_at=' . $this->dao->quote(date('Y-m-d H:i:s')) . '
					 WHERE id=' . $this->dao->quote($analyId);
		if($this->dao->execute($strQuery)) {
			return array('success'=>true);
		}
		return array('errorMsg'=>'模块分析删除失败');
	}
	
	public function getModuleAnaly($stuScoreArray) {
		foreach ($stuScoreArray as $paperId=>$paperCfg) {
			$examId = $paperCfg['exam_id'];
			$subjectCode = $paperCfg['subject_code'];
			$moduleStatistics = $this->getModuleStatistics($examId, $subjectCode, true);
			break;
		}
		$analyData = array();
		if($moduleStatistics) {
			foreach ($moduleStatistics as $moduleCode=>$moduleCfg) {
				$analyData[$moduleCode] = array('module_code'=>$moduleCode,
												'module_caption'=>$moduleCfg['module_caption'],
												'module_score'=>0, 
												'module_total'=>$moduleCfg['module_score'],
												'ques_cnt'=>$moduleCfg['ques_cnt'], 
												'module_average'=>$moduleCfg['module_average'],
												'module_ratio'=>$moduleCfg['module_ratio']);
				
				foreach ($moduleCfg['questions'] as $paperId=>$scoreFields) {
					foreach ($scoreFields as $field) {
						if(isset($stuScoreArray[$paperId])) {
							$analyData[$moduleCode]['module_score'] += $stuScoreArray[$paperId]['score_info'][$field];
						}
					}
				}
				$analyData[$moduleCode]['stu_ratio'] = sprintf('%.2f', $analyData[$moduleCode]['module_score'] / $analyData[$moduleCode]['module_total'] * 100);
			}
		}
		$analyList = $this->getAnalyList($examId, $subjectCode);
		
		foreach ($analyList as $analy) {
			$moduleCode = $analy['module_code'];
			if(false == isset($analyData[$moduleCode]['module_analy'])) {
				if(floatval($analyData[$moduleCode]['module_score']) >= floatval($analy['module_score'])) {
					$analyData[$moduleCode]['module_analy'] = $analy['module_analy'];
				}
			}
		}
		return $analyData;
	}
}
?>