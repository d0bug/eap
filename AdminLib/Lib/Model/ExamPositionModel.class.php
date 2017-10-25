<?php
class ExamPositionModel{
	protected $dao = null;
	protected $operator = '';
	protected $tableName = 'ex_exam_positions';
	protected $posTable = 'ex_positions';
	protected $examStudentTable = 'ex_exam_students';
	
	public function __construct() {
		$this->dao = Dao::getDao();
		
	}
	
	public function getFreePosList($examId) {
		$strQuery = 'SELECT p.pos_caption,p.pos_code,p.pos_telephone,p.pos_addr,ep.pos_code_pre,ep.pos_total_count 
					 FROM ' . $this->tableName . ' ep,
					 	  ' . $this->posTable . ' p
					 WHERE p.pos_code=ep.pos_code 
					   AND ep.exam_id=' . abs($examId) . '
					   AND ep.is_deleted=' . $this->dao->quote('') . '
					   AND p.is_remove=0
					 ORDER BY ep.pos_code_pre';
		$posList = $this->dao->getAll($strQuery);
		$posArray = array();
		foreach ($posList as $pos) {
			$posArray[$pos['pos_code']] = $pos;
		}
		$strQuery = 'SELECT pos_code,count(1) stu_cnt
					 FROM ' . $this->examStudentTable . '
					 WHERE exam_id=' . abs($examId) . '
					   AND is_cancel=0
					 GROUP BY pos_code';
		$posCntList = $this->dao->getAll($strQuery);
		foreach ($posCntList as $pos) {
			if($pos['stu_cnt'] >= $posArray[$pos['pos_code']]['pos_total_count']) {
				unset($posArray[$pos['pos_code']]);
			}
		}
		return $posArray;
	}
	
	public function getPosInfo($examId, $posCode) {
		$strQuery = 'SELECT * FROM ' . $this->tableName . '
					 WHERE exam_id=' . abs($examId) . '
					   AND pos_code=' . $this->dao->quote($posCode) . '
					   AND is_deleted=' . $this->dao->quote('');
		return $this->dao->getRow($strQuery);
	}
	
	public function getGroupPosCount($groupId) {
		$examModel = D('Exam');
		$examList = $examModel->getExamList('is_remove=0 AND exam_status=1 AND group_id=' . abs($groupId), 1, 100);
		$examIds = array(0);
		foreach ($examList as $exam) {
			$examIds[] = $exam['exam_id'];
		}
		$strQuery = 'SELECT exam_id,p.pos_caption,ep.pos_code,pos_total_count 
					 FROM ' . $this->tableName . ' ep,
					 	  ' . $this->posTable . ' p
					 WHERE ep.pos_code=p.pos_code 
					   AND ep.is_deleted=' . $this->dao->quote('') . '
					   AND ep.exam_id IN (' . implode(',', $examIds) . ')';
		$countList = $this->dao->getAll($strQuery);
		$countArray = array();
		foreach ($countList as $row) {
			if(false == isset($countArray[$row['pos_count']])) {
				$countArray[$row['pos_code']]['pos_caption'] = $row['pos_caption'];
			}
			foreach ($examList as $exam) {
				if(false == isset($countArray[$row['pos_code']]['exam_' . $exam['exam_id']])) {
					$countArray[$row['pos_code']]['exam_' . $exam['exam_id']] = 0;
					$countArray[$row['pos_code']]['exam_' . $exam['exam_id'] . '_signup'] = 0;
					$countArray[$row['pos_code']]['exam_' . $exam['exam_id'] . '_left'] = 0;
				}
			}
			$countArray[$row['pos_code']]['exam_' . $row['exam_id']] = $row['pos_total_count'];
			$countArray[$row['pos_code']]['exam_' . $row['exam_id'] . '_left'] = $row['pos_total_count'];
		}
		$esModel = D('ExamStudent');
		$signupCountArray = $esModel->getSignupCountArray($groupId);
		
    	foreach ($signupCountArray as $posCode=>$posCount) {
    		foreach ($examList as $exam) {
				$countArray[$posCode]['exam_' . $exam['exam_id'] . '_signup'] = abs($posCount['exam_' . $exam['exam_id']]);
				$countArray[$posCode]['exam_' . $exam['exam_id'] . '_left'] = $countArray[$posCode]['exam_' . $exam['exam_id']] -  abs($posCount['exam_' . $exam['exam_id']]);
    		}
    	}
		
		return array_values($countArray);
	}
	
	public function getRoomNames($examId, $posCode) {
    	$strQuery = 'SELECT room_name_setting FROM ' . $this->tableName . '
    				 WHERE exam_id=' . abs($examId) . '
    				   AND pos_code=' . $this->dao->quote($posCode) . '
    				   AND status=1
    				   AND is_deleted=' . $this->dao->quote(''); 
    	$nameSetting = $this->dao->getOne($strQuery);
    	$nameSetting = preg_replace('/^\[\'/', '', $nameSetting);
    	$nameSetting = preg_replace('/\'\]$/', '', $nameSetting);
    	$names = explode("','", $nameSetting);
    	$nameArray = array();
    	foreach ($names as $idx=>$name) {
    		if($name) {
    			$nameArray[$idx+1] = $name;
    		}
    	}
    	return $nameArray;
    }
}
?>