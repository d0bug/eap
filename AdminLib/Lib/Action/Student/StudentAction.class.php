<?php
class StudentAction extends StudentCommAction {
	public function lists() {
		
	}
	
    public function main() {
    	$semesterArray = array('C'=>'春', 'S'=>'署', 'Q'=>'秋', 'H'=>'寒');
    	$curYear = date('y');
    	$yearArray = array();
    	for ($i=$curYear-1;$i<=$curYear+1; $i++) {
    		$yearArray['BJ' . $i] = 'BJ' . $i;
    	}
    	$curYear = 'BJ' . $curYear;
    	$addGroupUrl = $this->getUrl('newGroup');
    	$jsonGroupUrl = $this->getUrl('jsonGroups');
    	$jsonStuUrl = $this->getUrl('jsonStuList');
    	$dlgSubjectUrl = $this->getUrl('subjects', 'Dialog', 'Util');
    	$dlgProjectUrl = $this->getUrl('projects', 'Dialog', 'Util');
    	$dlgClassTypeUrl = $this->getUrl('classTypes', 'Dialog', 'Util');
    	$delGroupUrl = $this->getUrl('delGroup');
    	$groupPerm = Permission::getPermInfo($this->loginUser, $this->getAclKey('addGroup'));
    	$groupPerm = $groupPerm['permValue'];
    	$groupStuUrl = $this->getUrl('groupStudents');
    	$this->assign(get_defined_vars());
        $this->display();
    }
    
    public function newGroup () {
    	if($this->isPost()) {
    		$groupCaption = SysUtil::safeString($_POST['group_title']);
    		$groupType = SysUtil::safeString($_POST['group_type']);
    		$groupData = SysUtil::safeString($_POST['group_data']);
    		$groupDesc = SysUtil::safeString($_POST['type_caption']);
    		$stuGroupModel = D('StuGroup');
    		$saveResult = $stuGroupModel->addGroup($groupData, $groupType, $groupCaption);
    		echo json_encode($saveResult);
    		exit;
    	} else {
    		$groupType = SysUtil::safeString($_GET['gType']);
    		$addGroupUrl = $this->getUrl('newGroup');
    		$dialog = SysUtil::safeString($_GET['dlg']);
    		$this->assign(get_defined_vars());
    		$this->display('group_' . $groupType);
    	}
    }
    
    public function addGroup() {
    	$groupType = SysUtil::safeString($_POST['groupType']);
    	$groupCaption = SysUtil::safeString($_POST['groupCaption']);
    	$url = $_POST['url'];
    	$url = str_ireplace('http://' . $_SERVER['HTTP_HOST'], '', $url);
    	list($url, $queryStr) = explode('?', $url);
    	$ar = explode('/', $url);
    	list(,$groupName, $moduleName, $actionName) = $ar;
    	if(sizeof($ar) > 4) {
    		for ($i=4,$j=sizeof($ar);$i<=$j;$i+=2) {
    			$_GET[$ar[$i]] = $ar[$i+1];
    		}
    	}
    	$_POST = json_decode($_POST['data'],true);
    	require_once(LIB_PATH . '/Action/' . ucfirst($groupName) . '/' . ucfirst($groupName) . 'CommAction.class.php');
    	require_once(LIB_PATH . '/Action/' . ucfirst($groupName) . '/' . ucfirst($moduleName) . 'Action.class.php');
    	$className = ucfirst($moduleName) . 'Action';
    	$module = new $className();
    	$strQuery = $module->callMethod($actionName, true);
    	$groupModel = D('StuGroup');
    	$addResult = $groupModel->addGroup($strQuery, $groupType, $groupCaption);
    	echo json_encode($addResult);
    	exit;
    }
    
    protected function delGroup() {
    	$gid = SysUtil::uuid($_POST['gid']);
    	$groupModel = D('StuGroup');
    	$delResult = $groupModel->delGroup($gid);
    	echo json_encode($delResult);
    	exit;
    }
    
    protected function jsonGroups() {
    	$groupModel = D('StuGroup');
    	$groupCount = $groupModel->getGroupCount();
    	$curPage = abs($_POST['page']);
    	$curPage = $curPage > 0 ? $curPage : 1;
    	$pageSize = abs($_POST['rows']);
    	$pageSize = $pageSize > 0 ? $pageSize : 20;
    	$groupList = $groupModel->getGroupList($curPage, $pageSize);
    	echo json_encode(array('total'=>$groupCount, 'rows'=>$groupList));
    }
    
    protected function jsonStuList($addGroup=false) {
    	
    	echo json_encode(array('rows'=>array()));
    }
    
    protected function groupStudents() {
    	$groupId = SysUtil::uuid($_POST['groupId']);
    	$groupModel = D('StuGroup');
    	$stuCount = $groupModel->getGroupStuCount($groupId);
    	$currentPage = $_POST['page'];
    	$pageSize = $_POST['rows'];
    	$stuList = $groupModel->getGroupStuList($groupId, $currentPage, $pageSize);
    	echo json_encode(array('total'=>$stuCount, 'rows'=>$stuList));
    }
    
    public function resetPwd() {
    	if($this->readCheck($this->getAclKey('resetPwd'))) {
	    	if($this->isPost()) {
	    		$stuCode = $_POST['stu_code'];
	    		$stuModel = D('Student');
	    		$resetResult = $stuModel->resetPwd($stuCode);
	    		echo json_encode($resetResult);
	    		exit;
	    	}
    	}
    	die('access denid');
    }
}
?>