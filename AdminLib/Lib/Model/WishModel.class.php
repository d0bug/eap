<?php
class WishModel {
	private $tableName = 'Mgs_stu_wishes';
	private $csgTable = 'Mgs_stu_csg';
	private $xscTable = 'Mgs_stu_xsc';
	private $stuInfoTable = 'Mgs_stu_info';
	private $stuJianliTable = 'Mgs_stu_jianli';
	
	public function __construct() {
		$this->dao = Dao::getDao();
	}
	
	public function createTimeBegin() {
		$month = date('n');
		if($month < 9) {
			$date = (date('Y') - 1) . '-09-01';
		} else {
			$date = date('Y') . '09-01';
		}
		return $date;
	}
	
	public function searchWishes($wishType, $schoolIds, $currentPage, $pageSize) {
		
	}
}
?>