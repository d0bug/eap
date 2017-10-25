<?php
class CharmStudentModel {
	private $dao = null;
	private $operator = '';
	private $tableName = 'charm_student';
	private $cClassTable = 'charm_class';
	private $lessonTable = 'bs_lesson';
	
	public function __construct() {
		$this->dao = Dao::getDao();
		if(class_exists('User', false)) {
			$operator = User::getLoginUser();
			$this->operator = $operator->getLoginUser();
		}
	}
	
	private function getSearchCondition($searchArgs) {
		$keyword = $searchArgs['keyword'];
		if($keyword) {
			$condition = ' AND (';
			$condition .= 'sclassname LIKE ' . $this->dao->quote('%' . $keyword . '%') . '
						OR sclasscode=' . $this->dao->quote($keyword) . '
						OR stu_name=' . $this->dao->quote($keyword);
			$condition .= ')';
		}
	}
	
	public function getStuCount($searchArgs) {
		static $stuCount = null;
		if(null === $stuCount) {
			$strQuery = 'SELECT count(1) FROM ' . $this->tableName . '
						 WHERE group_id=' . abs($searchArgs['groupId']);
			$strQuery .= $this->getSearchCondition($searchArgs);
			
			$stuCount = $this->dao->getOne($strQuery);
		}
		return $stuCount;
	}
	
	public function getStuList($searchArgs, $currentPage, $pageSize) {
		$recordCount = $this->getStuCount($searchArgs);
		$pageSize = abs($pageSize);
		$pageSize = $pageSize > 0 ? $pageSize:20;
		$pageCount = ceil($recordCount / $pageSize);
		$currentPage = abs($currentPage);
		$currentPage = $currentPage > $pageCount ? $pageCount : $currentPage;
		$currentPage = $currentPage > 0 ? $currentPage : 1;
		$strQuery = 'SELECT * FROM ' . $this->tableName . '
					 WHERE group_id=' . abs($searchArgs['groupId']);
		$strQuery .= $this->getSearchCondition($searchArgs);
		$order = 'ORDER BY create_at DESC';
		$stuList = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		return $stuList;
	}
	
	public function saveStuInfo($stuInfo) {
		
	}
	
	public function delStudent($stuInfo) {
		
	}
}
?>