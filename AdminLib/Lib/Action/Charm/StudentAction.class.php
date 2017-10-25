<?php
class StudentAction extends CharmCommAction {
	protected function notNeedLogin() {
		return array();
	}
	
	public function lists(){
		$permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey('lists'));
		$permValue = $permInfo['permValue'];
		$groupUrl = $this->getUrl('jsonGroup', 'Cls');
		$stuUrl = $this->getUrl('jsonStudents');
		$addStuUrl = $this->getUrl('addStudent');
		$delStuUrl = $this->getUrl('delStudent');
		$classUrl = $this->getUrl('jsonClass', 'Cls');
		$formInfoUrl = $this->getUrl('formInfo', 'Cls');
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function jsonStudents() {
		$charmStudentModel = D('CharmStudent');
		$searchArgs = array('groupId'=>abs($_POST['group_id']));
		$currentPage = abs($_POST['page']);
		$pageSize = abs($_POST['rows']);
		$recordCount = $charmStudentModel->getStuCount($searchArgs);
		$stuList = $charmStudentModel->getStuList($searchArgs, $currentPage, $pageSize);
		echo json_encode(array('total'=>$recordCount, 'rows'=>$stuList));
		exit;
	}
	
	protected function addStudent() {
		
	}
	
	protected function delStudent() {
		
	}
}
?>