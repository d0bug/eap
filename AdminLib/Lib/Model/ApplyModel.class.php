<?php
class ApplyModel {
	protected $tableName  = 'ex_exam_applys';
	protected $stuTable = 'bs_student';
	protected $examTable = 'ex_exams';
	protected $dao = null;
	protected $operator = '';
	private $applyStatus = array('0'=>array('value'=>0, 'text'=>'待审','css'=>'wait'),
								 '-1'=>array('value'=>-1, 'text'=>'拒绝','css'=>'deny'),
								 '1'=>array('value'=>1, 'text'=>'通过','css'=>'pass'));
	
	public function __construct() {
		$this->dao = Dao::getDao();
		$user = User::getLoginUser();
		$this->operator = $user->getUserKey();
	}
	
	public function findApply($applyId) {
		$strQuery = 'SELECT * FROM ' . $this->tableName . '
					 WHERE id=' . abs($applyId);
		return $this->dao->getRow($strQuery);
	}
	
	public function getApplyCount($examId) {
		static $count = null;
		if(null === $count) {
			$strQuery = 'SELECT COUNT(1) FROM ' . $this->tableName . '
						 WHERE exam_id=' . abs($examId);
			$count = $this->dao->getOne($strQuery);
		}
		return $count;
	}
	
	public function getApplyList($examId, $currentPage=1, $pageSize=20) {
		$recordCount = $this->getApplyCount($examId);
		$pageCount = ceil($recordCount / $pageSize);
		$currentPage = abs($currentPage);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$gradeModel = D('GradeYear');
		$grades = $gradeModel->getGradeYears();
		$strQuery = 'SELECT stu.sname,stu.scode,stu.saliascode,stu.sparents1phone,
							stu.ngrade1year,apply.id,apply.apply_time,apply.status,apply.apply_remark,
							apply_reason,refuse_reason,apply.op_user_id,apply.op_time
					 FROM ' . $this->stuTable . ' stu,
					 ' . $this->tableName . ' apply
					 WHERE stu.scode=apply.stu_code
					 AND apply.exam_id=' . abs($examId);
		$order = 'ORDER BY apply_time DESC';
		$applyList = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		foreach ($applyList as $key=>$apply) {
			$applyList[$key]['grade_text'] = $grades[$apply['ngrade1year']];
			$applyList[$key]['status'] = $this->applyStatus[$apply['status']];
		}
		return $applyList;
	}
	
	public function setApplyStatus($applyId, $status, $reason='') {
		$time = date('Y-m-d H:i:s');
		$strQuery = 'UPDATE ' . $this->tableName . '
					 SET status=' . intval($status) . ',
					 	 refuse_reason=' . $this->dao->quote($reason) . ',
					  	 op_time=' . $this->dao->quote($time) . ',
					  	 op_user_id=' . $this->dao->quote($this->operator) . '
					 WHERE id=' . $applyId;
		if($this->dao->execute($strQuery)) {
			return array('success'=>true);
		}
		return array('success'=>false);
	}
}
?>