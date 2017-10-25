<?php
class PartAnalyModel{
	protected $dao = null;
	protected $operator = '';
	protected $paperPartTable = 'ex_paper_parts';
	protected $paperQuesTable = 'ex_paper_questions';
	protected $partCfgTable = 'ex_part_cfg';
	protected $partAnalyTable = 'ex_part_analys';
	protected $paperTable = 'ex_exam_papers';
	protected $scoreTable = 'ex_exam_scores';
	
	
	public function __construct() {
		$this->dao = Dao::getDao();
		if(class_exists('User', false)) {
			$operator = User::getLoginUser();
			$this->operator = $operator->getUserKey();
		}
	}
	
	public function getPartQuestions($examId, $subjectCode) {
		$examId = abs($examId);
		$subjectCode = SysUtil::safeString($subjectCode);
		$strQuery = 'SELECT distinct ques.*,part.part_caption 
					 FROM ' . $this->paperQuesTable . ' ques,
					 	  ' . $this->paperPartTable . ' part,
					 	  ' . $this->paperTable . ' paper
					 WHERE paper.paper_id=ques.paper_id
					   AND paper.paper_type=' . $this->dao->quote('real') . '
					   AND ques.paper_id=part.paper_id
					   AND paper.paper_id=part.paper_id
					   AND ques.part_id=part.part_id
					   AND ques.exam_id=' . abs($examId) . '
					   AND ques.subject_code=' . $this->dao->quote($subjectCode) . '
					   AND ques.is_remove=0
					 ORDER BY ques.paper_id,ques.part_id,ques.ques_seq';
		$quesList = $this->dao->getAll($strQuery);
		$partQuestions = array();
		$quesIdArray = array();
		foreach ($quesList as $ques) {
			if(false == isset($partQuestions[$ques['part_id']])) {
				$partQuestions[$ques['part_id']]['part_num'] = $ques['part_id'];
				$partQuestions[$ques['part_id']]['part_caption'] = $ques['part_caption'];
				$partQuestions[$ques['part_id']]['questions'] = array();
				$partQuestions[$ques['part_id']]['ques_cnt'] = 0;
				$partQuestions[$ques['part_id']]['part_score'] = 0;
			}
			$partQuestions[$ques['part_id']]['questions'][$ques['paper_id']][] = 'ques_score_' . $ques['ques_seq'];
			if(false == isset($quesIdArray[$ques['ques_id']])) {
				$partQuestions[$ques['part_id']]['ques_cnt'] ++;
				$partQuestions[$ques['part_id']]['part_score'] += $ques['ques_score'];
				$quesIdArray[$ques['ques_id']] = true;
			}
		}
		
		return $partQuestions;
	}
	
	public function getPartStatistics($examId, $subjectCode, $isFront=false) {
		$strQuery = 'SELECT part_statistics FROM ' . $this->partCfgTable . '
					 WHERE exam_id=' . abs($examId) . '
					   AND subject_code=' . $this->dao->quote($subjectCode);
		
		$dbPartStatistics = $this->dao->getOne($strQuery);
		if($isFront && $dbPartStatistics) {
			return unserialize($dbPartStatistics);
		}
		$partQuestions = $this->getPartQuestions($examId, $subjectCode);
		
		$strQuery = 'SELECT count(distinct stu_code) stu_cnt';
		$paperIdArray = array(0);
		foreach ($partQuestions as $partId=>$partCfg) {
			$strQuery .= ',SUM(CASE ';
			foreach ($partCfg['questions'] as $paperId=>$quesFields) {
				$strQuery .= ' WHEN paper_id=' . $paperId . ' THEN ' . implode('+', $quesFields);
				$paperIdArray[$paperId] = $paperId;
			}
			$strQuery .= ' END) score_' . $partId;
		}
		$strQuery .= ' FROM ' . $this->scoreTable . '
					 WHERE paper_id IN (' . implode(',', $paperIdArray) . ')';
		$scoreRow = $this->dao->getRow($strQuery);
		foreach ($partQuestions as $partId=>$partCfg) {
			$partQuestions[$partId]['part_average'] = $scoreRow['score_' . $partId] / $scoreRow['stu_cnt'];
			$partQuestions[$partId]['part_ratio'] = sprintf('%.2f', $scoreRow['score_' . $partId] / ($partQuestions[$partId]['part_score'] * $scoreRow['stu_cnt']) * 100);
		}
		
		$time = date('Y-m-d H:i:s');
		if($dbPartStatistics) {
			$strQuery = 'UPDATE ' . $this->partCfgTable . '
						 SET part_statistics=' . $this->dao->quote(serialize($partQuestions)) . ',
						 	 update_at=' . $this->dao->quote($time) . '
						 WHERE exam_id=' . abs($examId) . '
						   AND subject_code=' . $this->dao->quote($subjectCode);
		} else {
			$strQuery = 'INSERT INTO ' . $this->partCfgTable . '
						 (exam_id,subject_code,part_statistics,create_at,update_at)
						 VALUES (' . abs($examId) . ',
						 		 ' . $this->dao->quote($subjectCode) . ',
						 		 ' . $this->dao->quote(serialize($partQuestions)) . ',
						 		 ' . $this->dao->quote($time) . ',
						 		 ' . $this->dao->quote($time) . ')';
		}
		
		$this->dao->execute($strQuery);
		return $partQuestions;
	}
	
	public function getAnalyList($examId, $subjectCode, $partNum=0) {
		$strQuery = 'SELECT * FROM ' . $this->partAnalyTable . '
					 WHERE exam_id=' . abs($examId) . '
					   AND subject_code=' . $this->dao->quote($subjectCode);
		if($partNum > 0) {
			$strQuery .= ' AND part_num=' . abs($partNum);
		}
		$strQuery .=  ' AND is_remove=0
					 ORDER BY part_num, part_score DESC';
		$analyList = $this->dao->getAll($strQuery);
		return $analyList;
	}
	
	public function saveAnaly($analyInfo) {
		$examId = abs($analyInfo['examId']);
		$subjectCode = SysUtil::safeString($analyInfo['subjectCode']);
		$partNum = abs($analyInfo['partNum']);
		$partScore = abs($analyInfo['partScore']);
		$partAnaly = SysUtil::safeString($analyInfo['partAnaly']);
		$time = date('Y-m-d H:i:s');
		if($analyInfo['analyId']) {
			$analyId = SysUtil::uuid($analyInfo['analyId']);
			$strQuery = 'UPDATE ' . $this->partAnalyTable . '
						 SET part_score=' . abs($partScore) . ',
					     	 part_analy=' . $this->dao->quote($partAnaly) . ',
					     	 update_user=' . $this->dao->quote($this->operator) . ',
					     	 update_at=' . $this->dao->quote($time) . '
					     WHERE id=' . $this->dao->quote($analyId);
		} else {
			$analyId = SysUtil::uuid();
			$strQuery = 'INSERT INTO ' . $this->partAnalyTable . '
						 (id,exam_id,subject_code,part_num,part_score,part_analy,
						  is_remove,create_user,create_at,update_user,update_at)
						 VALUES (' . $this->dao->quote($analyId) . ',
						 		 ' . $examId . ',
						 		 ' . $this->dao->quote($subjectCode) . ',
						 		 ' . $partNum . ',
						 		 ' . $partScore . ',
						 		 ' . $this->dao->quote($partAnaly) . ',0,
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
		return array('errorMsg'=>'大题分档分析设置失败');
	}
	
	public function delAnaly($analyId) {
		$analyId = SysUtil::uuid($analyId);
		$strQuery = 'UPDATE ' . $this->partAnalyTable . '
					 SET is_remove=' . time() . ',
					 	 update_user=' . $this->dao->quote($this->operator) . ',
					 	 update_at=' . $this->dao->quote(date('Y-m-d H:i:s')) . '
					 WHERE id=' . $this->dao->quote($analyId);
		if($this->dao->execute($strQuery)) {
			return array('success'=>true);
		}
		return array('errorMsg'=>'大题分档分析删除失败');
	}
	
	public function getPartAnaly($stuScoreArray) {
		foreach ($stuScoreArray as $paperId=>$paperCfg) {
			if($paperCfg['paper_type'] == 'real') {
				$examId = $paperCfg['exam_id'];
				$subjectCode = $paperCfg['subject_code'];
				$partStatistics = $this->getPartStatistics($examId, $subjectCode, true);
				break;
			}
		}
		$analyData = array();
		if($partStatistics) {
			foreach ($partStatistics as $partId=>$partCfg) {
				$analyData[$partId] = array('part_num'=>$partId,
											'part_caption'=>$partCfg['part_caption'],
											'part_score'=>0, 
											'part_total'=>$partCfg['part_score'],
											'ques_cnt'=>$partCfg['ques_cnt'], 
											'part_average'=>$partCfg['part_average'],
											'part_ratio'=>$partCfg['part_ratio']);
				
				foreach ($partCfg['questions'][$paperId] as $scoreField) {
					$analyData[$partId]['part_score'] += $paperCfg['score_info'][$scoreField];
				}
				$analyData[$partId]['stu_ratio'] = sprintf('%.2f', $analyData[$partId]['part_score'] / $analyData[$partId]['part_total'] * 100);
			}
		}
		$analyList = $this->getAnalyList($examId, $subjectCode);
		
		foreach ($analyList as $analy) {
			$partId = $analy['part_num'];
			if(false == isset($analyData[$partId]['part_analy'])) {
				if(floatval($analyData[$partId]['part_score']) >= floatval($analy['part_score'])) {
					$analyData[$partId]['part_analy'] = $analy['part_analy'];
				}
			}
		}
		return $analyData;
	}
}
?>