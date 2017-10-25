<?php
class StudentAction extends ApiCommAction {
	protected function getStuInfoByCode($stuCode) {
		$stuModel = D('Student');
		$stuCode = preg_replace('/[^a-z0-9]/i', '', $stuCode);
		return $stuModel->getStuInfo($stuCode);
	}
	
	protected function findConflict($stuName, $stuBirth, $stuMobile) {
		$stuModel = D('Student');
		$stuList = $stuModel->findConflict($stuName, $stuBirth, $stuMobile);
		if(false == $stuList) {
			return array();
		}
		return $stuList;
	}
}
?>