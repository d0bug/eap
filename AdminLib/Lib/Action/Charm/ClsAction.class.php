<?php
class ClsAction extends CharmCommAction {
	protected function notNeedLogin() {
		return array();
	}
	
	public function lists() {
		$permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey('lists'));
		$permValue = $permInfo['permValue'];
		$groupUrl = $this->getUrl('jsonGroup');
		$addGroupUrl = $this->getUrl('addGroup');
		$editGroupUrl = $this->getUrl('editGroup');
		$delGroupUrl = $this->getUrl('delGroup');
		$classUrl = $this->getUrl('jsonClass');
		$addClassUrl = $this->getUrl('addClass');
		$delClassUrl = $this->getUrl('delClass');
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function jsonGroup() {
		$charmClassModel = D('CharmClass');
		$currentPage = abs($_POST['page']);
		$pageSize = abs($_POST['rows']);
		$recordCount = $charmClassModel->getGroupCount();
		$groupList = $charmClassModel->getGroupList($currentPage, $pageSize);
		echo json_encode(array('total'=>$recordCount, 'rows'=>$groupList));
	}
	
	protected function addGroup() {
		$charmClassModel = D('CharmClass');
		if($this->isPost()) {
			$saveResult = $charmClassModel->saveGroup($_POST);
			echo json_encode($saveResult);
			exit;
		}
		$dialog = SysUtil::safeString($_GET['dlg']);
		$saveGroupUrl = $this->getUrl('addGroup');
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function editGroup() {
		$groupId = abs($_GET['gid']);
		$charmClassModel = D('CharmClass');
		if($this->isPost()) {
			$saveResult = $charmClassModel->saveGroup($_POST);
			echo json_encode($saveResult);
			exit;
		}
		$groupInfo = $charmClassModel->findGroup($groupId);
		$dialog = SysUtil::safeString($_GET['dlg']);
		$saveGroupUrl = $this->getUrl('editGroup');
		$charmClassModel = D('CharmClass');
		$groupId = abs($_GET['groupId']);
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function delGroup() {
		$groupId = abs($_POST['gid']);
		$charmClassModel = D('CharmClass');
		if($this->isPost()) {
			$delResult = $charmClassModel->delGroup($groupId);
			echo json_encode($delResult);
			exit;
		}
	}
	
	protected function jsonClass() {
		$groupId = abs($_POST['gid']);
		$searchArgs = array('groupId'=>$groupId);
		if($_POST['keyword']) {
			$searchArgs['keyword'] = $_POST['keyword'];
		}
		$charmClassModel = D('CharmClass');
		$currentPage = abs($_POST['page']);
		$pageSize = abs($_POST['rows']);
		$recordCount = $charmClassModel->getClassCount($searchArgs);
		$classList = $charmClassModel->getClassList($searchArgs, $currentPage, $pageSize);
		echo json_encode(array('total'=>$recordCount, 'rows'=>$classList));
	}
	
	protected function addClass(){
		$charmClassModel = D('CharmClass');
		if($this->isPost()) {
			$saveResult = $charmClassModel->saveClass($_POST);
			echo json_encode($saveResult);
			exit;
		}
		$saveClassUrl = $this->getUrl('addClass');
		$clsInfoUrl = $this->getUrl('clsInfo');
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function clsInfo() {
		$classCode = SysUtil::safeString($_POST['clsCode']);
		import('COM.Gaosi.GClass');
		$classInfo = GClass::getClassInfoByCode($classCode);
		echo json_encode($classInfo);
	}
	
	protected function formInfo() {
		$groupId = abs($_POST['gid']);
		$charmClassModel = new CharmClassModel();
		$groupAreas = $charmClassModel->getAreas($groupId);
		$groupSubjects = $charmClassModel->getSubjects($groupId);
		echo json_encode(array('areas'=>$groupAreas, 'subjects'=>$groupSubjects));
		exit;
	}
	
	protected function delClass() {
		
	}
}
?>