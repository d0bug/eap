<?php
class KnowledgeAnalyModel {
	protected $dao = null;
	protected $paperQuesTable = 'ex_paper_questions';
	protected $quesKnowledgeTable = 'ex_ques_knowledges';
	protected $knowledgeCfgTable = 'ex_knowledge_cfg';
	protected $scoreTable = 'ex_exam_scores';
	protected $knowledgeAnalyTable = 'ex_knowledge_analys';
	protected $operator = '';
	
	public function __construct(){
		$this->dao = Dao::getDao();
		if(class_exists('User', false)) {
			$operator = User::getLoginUser();
			$this->operator = $operator->getUserKey();
		}
	}
	
	public function getQuesKnowledge($examId, $subjectCode) {
		static $quesList = null;
		static $knowledgeList = null;
		if(null === $quesList || null === $knowledgeList) {
			$strQuery = 'SELECT distinct exam_id,paper_id,subject_code,ques_id,
								ques_score,ques_knowledge,ques_level,ques_seq 
						 FROM ' . $this->paperQuesTable . '
						 WHERE exam_id=' . abs($examId) . '
						   AND subject_code=' . $this->dao->quote($subjectCode) . '
						   AND is_remove=0
						 ORDER BY paper_id,ques_level,ques_seq';
			$quesList = $this->dao->getAll($strQuery);
			$quesIdArray = array($this->dao->quote('-1'));
			$knowledgeKeys = array();
			$paperQuesArray = array();
			$knowledgeArray = array($this->dao->quote('-1'));
			foreach ($quesList as $ques) {
				$quesIdArray[] = $this->dao->quote($ques['ques_id']);
				if($ques['ques_knowledge']) {
					$knowledgeKey = strtolower($ques['ques_id'] . '-' . $ques['ques_knowledge']);
					$knowledgeArray[] = $this->dao->quote($ques['ques_knowledge']);
					$knowledgeKeys[$knowledgeKey] = true;
				}
			}
			$strQuery = 'SELECT * FROM ' . $this->quesKnowledgeTable . '
						 WHERE ques_id IN (' . implode(',', $quesIdArray) . ')
						   AND knowledge_code IN (' . implode(',', $knowledgeArray) . ')
						 ORDER BY knowledge_code';
			
			$knowledgeList = $this->dao->getAll($strQuery);
			$knowledgeArray = array();
			foreach ($knowledgeList as $knowledge) {
				$knowledgeKey = strtolower($knowledge['ques_id'] . '-' . $knowledge['knowledge_code']);
				if(isset($knowledgeKeys[$knowledgeKey])) {
					$knowledgeArray[] = $knowledge;
				}
			}
		}
		return array($quesList, $knowledgeArray);
	}
	
	public function getKnowledgeQuestions($examId, $subjectCode) {
		list($quesList, $knowledgeList) = $this->getQuesKnowledge($examId, $subjectCode);
		$knowledgeQuesArray = array();
		
		foreach ($knowledgeList as $ques) {
			$knowledgeQuesArray[$ques['ques_id']]['knowledge_code'] = $ques['knowledge_code'];
			$knowledgeQuesArray[$ques['ques_id']]['knowledge_caption'] = '[' . $ques['module_caption'] . ']' . $ques['knowledge_caption'];
		}
		
		$knowledgeQuestions = array();
		$quesIdArray = array();
		foreach ($quesList as $ques) {
			$quesId = $ques['ques_id'];
			if(isset($knowledgeQuesArray[$quesId])) {
				$knowledgeCode = $knowledgeQuesArray[$quesId]['knowledge_code'];
				if(false == isset($knowledgeQuestions[$knowledgeCode])) {
					$knowledgeCaption = $knowledgeQuesArray[$quesId]['knowledge_caption'];
					$knowledgeQuestions[$knowledgeCode]['knowledge_code'] = $knowledgeCode;
					$knowledgeQuestions[$knowledgeCode]['knowledge_caption'] = $knowledgeCaption;
				}
				if(false == isset($quesIdArray[$quesId])) {
					$knowledgeQuestions[$knowledgeCode]['ques_cnt'] ++;
					$knowledgeQuestions[$knowledgeCode]['knowledge_score'] += $ques['ques_score'];
					$quesIdArray[$quesId] = true;
				}
				$knowledgeQuestions[$knowledgeCode]['questions'][$ques['paper_id']][] = 'ques_score_' . $ques['ques_seq'];
			}
		}
		ksort($knowledgeQuestions);
		return $knowledgeQuestions;
	}
	
	public function getKnowledgeStatistics($examId, $subjectCode, $isFront = false) {
		$strQuery = 'SELECT knowledge_statistics FROM ' . $this->knowledgeCfgTable . '
					 WHERE exam_id=' . abs($examId) . '
					   AND subject_code=' . $this->dao->quote($subjectCode);
		$dbKnowledgeStatistics = $this->dao->getOne($strQuery);
		if($isFront && $dbKnowledgeStatistics) {
			return unserialize($dbKnowledgeStatistics);
		}
		$knowledgeQuestions = $this->getKnowledgeQuestions($examId, $subjectCode);
		$paperIdArray = array(0);
		if(false == $knowledgeQuestions) {
			return array();
		}
		$strQuery = 'SELECT count(distinct stu_code) stu_cnt';
		foreach ($knowledgeQuestions as $knowledgeCode=>$knowledgeCfg) {
			$strQuery .= ',SUM(CASE ';
			foreach ($knowledgeCfg['questions'] as $paperId=>$quesIds) {
				$strQuery .= ' WHEN paper_id=' . $paperId . ' THEN ' . implode('+', $quesIds);
				$paperIdArray[$paperId] = $paperId;
			}
			$strQuery .= ' END) score_' . $knowledgeCode . '';
		}
		$strQuery .= ' FROM ' . $this->scoreTable . '
				WHERE paper_id IN (' . implode(',', $paperIdArray) . ')';
		if(false == $paperIdArray) return array();
		$statisticsRow = $this->dao->getRow($strQuery);
		foreach ($knowledgeQuestions as $knowledgeCode=>$knowledgeCfg) {
			$scoreField = strtolower('score_' . $knowledgeCode);
			$knowledgeQuestions[$knowledgeCode]['knowledge_average'] = sprintf('%.2f',$statisticsRow[$scoreField] / $statisticsRow['stu_cnt']);
			$knowledgeQuestions[$knowledgeCode]['knowledge_ratio'] = sprintf('%.2f', $statisticsRow[$scoreField] / ($knowledgeCfg['knowledge_score'] * $statisticsRow['stu_cnt']) * 100);
		}
		$knowledgeStatistics = $knowledgeQuestions;
		$time = date('Y-m-d H:i:s');
		if($dbKnowledgeStatistics) {
			$strQuery = 'UPDATE ' . $this->knowledgeCfgTable . '
						 SET knowledge_statistics=' . $this->dao->quote(serialize($knowledgeStatistics)) . ',
						     update_at=' . $this->dao->quote($time) . '
						 WHERE exam_id=' . abs($examId) . '
						   AND subject_code=' . $this->dao->quote($subjectCode);
		} else {
			$strQuery = 'INSERT INTO ' . $this->knowledgeCfgTable . '
						 (exam_id,subject_code,knowledge_statistics,create_at,update_at)
						 VALUES (' . abs($examId) . ',
						 		 ' . $this->dao->quote($subjectCode) . ',
						 		 ' . $this->dao->quote(serialize($knowledgeStatistics)) . ',
						 		 ' . $this->dao->quote($time) . ',
						 		 ' . $this->dao->quote($time) . ')';
		}
		$this->dao->execute($strQuery);
		return $knowledgeStatistics;
	}
	
	public function getAnalyList($examId, $subjectCode, $knowledgeCode='') {
		$strQuery = 'SELECT * FROM ' . $this->knowledgeAnalyTable . '
					 WHERE exam_id=' . abs($examId) . '
					   AND subject_code=' . $this->dao->quote($subjectCode) . '
					   AND is_remove=0';
		if($knowledgeCode) {
			$strQuery .= ' AND knowledge_code=' . $this->dao->quote($knowledgeCode);
		}
		$strQuery .= ' ORDER BY knowledge_code,knowledge_score DESC';
		return $this->dao->getAll($strQuery);
	}
	
	public function saveAnaly($analyInfo) {
		$time = date('Y-m-d H:i:s');
		$examId = abs($_POST[$analyInfo['examId']]);
		$subjectCode = SysUtil::safeString($analyInfo['subjectCode']);
		$knowledgeCode = SysUtil::safeString($analyInfo['knowledgeCode']);
		$knowledgeScore = abs($analyInfo['knowledgeScore']);
		$knowledgeAnaly = SysUtil::safeString($analyInfo['knowledgeAnaly']);
		if($analyInfo['id']) {
			$analyId = SysUtil::uuid($analyInfo['id']);
			$strQuery = 'UPDATE ' . $this->knowledgeAnalyTable . '
						 SET knowledge_score=' . abs($knowledgeScore) . ',
						 	 knowledge_analy=' . $this->dao->quote($knowledgeAnaly) . ',
						 	 update_user=' . $this->dao->quote($this->operator) . ',
						 	 update_at=' . $this->dao->quote($time) . '
						 WHERE id=' . $this->dao->quote($analyId);
		} else {
			$analyId = SysUtil::uuid();
			$strQuery = 'INSERT INTO ' . $this->knowledgeAnalyTable . '
						 (id,exam_id,subject_code,knowledge_code, knowledge_score,knowledge_analy,
						  is_remove,create_user,create_at,update_user,update_at)
						  VALUES (' . $this->dao->quote($analyId) . ',
						  		  ' . abs($examId) . ',
						  		  ' . $this->dao->quote($subjectCode) . ',
						  		  ' . $this->dao->quote($knowledgeCode) . ',
						  		  ' . abs($knowledgeScore) . ',
						  		  ' . $this->dao->quote($knowledgeAnaly) . ',0,
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
		return array('errorMsg'=>'知识点分析设置失败');
	}
	
	public function delAnaly($analyId) {
		$analyId = SysUtil::uuid($analyId);
		$strQuery = 'UPDATE ' . $this->knowledgeAnalyTable . '
					 SET is_remove=' . time() . ',
					 	 update_user=' . $this->dao->quote($this->operator) . ',
					 	 update_at=' . $this->dao->quote(date('Y-m-d H:i:s')) . '
					 WHERE id=' . $this->dao->quote($analyId);
		if($this->dao->execute($strQuery)) {
			return array('success'=>true);
		}
		return array('errorMsg'=>'知识点分析删除失败');
	}
	
	public function getKnowledgeRatios($stuScoreArray) {
		foreach ($stuScoreArray as $paperId=>$paperCfg) {
			$examId = $paperCfg['exam_id'];
			$subjectCode = $paperCfg['subject_code'];
			$knowledgeStatistics = $this->getKnowledgeStatistics($examId, $subjectCode, true);
			break;
		}
		$analyData = array();
		if($knowledgeStatistics) {
			foreach ($knowledgeStatistics as $knowledgeCode=>$knowledgeCfg) {
				$analyData[$knowledgeCode] = array('knowledge_code'=>$knowledgeCode,
												'knowledge_caption'=>$knowledgeCfg['knowledge_caption'],
												'knowledge_score'=>0, 
												'knowledge_total'=>$knowledgeCfg['knowledge_score'],
												'ques_cnt'=>$knowledgeCfg['ques_cnt'], 
												'knowledge_average'=>$knowledgeCfg['knowledge_average'],
												'knowledge_ratio'=>$knowledgeCfg['knowledge_ratio']);
				
				foreach ($knowledgeCfg['questions'] as $paperId=>$scoreFields) {
					foreach ($scoreFields as $field) {
						if(isset($stuScoreArray[$paperId])) {
							$analyData[$knowledgeCode]['knowledge_score'] += $stuScoreArray[$paperId]['score_info'][$field];
						}
					}
				}
				$analyData[$knowledgeCode]['stu_ratio'] = sprintf('%.2f', $analyData[$knowledgeCode]['knowledge_score'] / $analyData[$knowledgeCode]['knowledge_total'] * 100);
			}
		}
		return $analyData;
	}
}
?>