<?php
class ExamAction extends ExamCommAction{
    public function eGroup() {
        $permValue = $this->permValue;
        $jsonGroupUrl = $this->getUrl('jsonGroupList');
        $groupInfoUrl = $this->getUrl('groupInfo');
        $addGroupUrl = $this->getUrl('addGroup');
        $delGroupUrl = $this->getUrl('delGroup');
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    public function main() {
        $permValue = $this->permValue;
        $jsonExamUrl = $this->getUrl('jsonExamList');
        $jsonGroupUrl = $this->getUrl('jsonGroupList');
        $addExamUrl = $this->getUrl('addExam');
        $examInfoUrl = $this->getUrl('examInfo');
        $delExamUrl = $this->getUrl('delExam');
        $gTypeArray = C('EXAM_GROUP_TYPES');
        $jsonExamInfoUrl = $this->getUrl('jsonExamInfo');
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    /**
     * 考试考点管理
     */
    public function ePosition(){
    	$permValue = $this->permValue;
        $jsonExamUrl = $this->getUrl('jsonExamList');
        $jsonGroupUrl = $this->getUrl('jsonGroupList');
        $addExamUrl = $this->getUrl('addExam');
        $examInfoUrl = $this->getUrl('examInfo');
        $delExamUrl = $this->getUrl('delExam');
        $testUrl = $this->getUrl('test');
        
        $setPositionUrl = $this->getUrl('setPosition');
        
        $groupArray = D('Exam')->arrayExamGroup(15);

        $jsonExamInfoUrl = $this->getUrl('jsonExamInfo');
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    
    protected function jsonGroupList() {
        $groupModel = D('ExamGroup');
        $condition = '';
        if($_POST['keyword']) {
            $condition = 'group_caption LIKE ' . $groupModel->dao->quote('%' . SysUtil::safeSearch($_POST['keyword']) . '%');
        } else if ($_POST['groupType']) {
            $condition = 'group_type=' . $groupModel->dao->quote(SysUtil::safeString($_POST['groupType']));
        }
        if($_POST['onlyShow']) {
            if($condition) {
                $condition .= ' AND group_status=1';
            } else {
                $condition = ' group_status=1';
            }
        }
        $total = $groupModel->getGroupCount($condition);
        $currentPage = abs($_POST['page']) ? abs($_POST['page']) : 1;
        $pageSize = abs($_POST['rows']) ? abs($_POST['rows']) : 9999;
        $sort = array();
        if($_POST['sort']) {
            $sort = array('sort'=>$_POST['sort'], 'order'=>$_POST['order']);
        }
        $groupList = $groupModel->getGroupList($condition, $currentPage, $pageSize, $sort);
        echo json_encode(array('total'=>$total, 'rows'=>$groupList));
        exit;
    }
    
    protected function jsonExamList() {
        $this->readCheck($this->getAclKey('main'));
        $groupId = abs($_POST['groupId']);
        $groupType = SysUtil::safeString($_POST['groupType']);
        $status = abs($_POST['status']);
        $examModel = D('Exam');
        if($groupId) {
            $condition = 'group_id=' . $groupId;
        } else if($groupType) {
            $condition = 'group_type=' . $examModel->dao->quote($groupType);
        } else {
            $condition = '1=1';
        }
        if($status) {
        	$condition .= ' AND exam_status=1';
        }
        
        $total = $examModel->count($condition);
        $currentPage = abs($_POST['page']);
        if(false == $currentPage) $currentPage = 1;
        $pageSize = abs($_POST['rows']);
        if(false == $pageSize) $pageSize = 20;
        $examList = $examModel->getExamList($condition, $currentPage, $pageSize);
        $return  = array('total'=>$total, 'rows'=>$examList);
        echo json_encode($return);
        exit;
    }
    
    protected function addGroup() {
        $this->writeCheck($this->getAclKey('eGroup'));
        $url = $_SERVER['REQUEST_URI'];
        $gTypeArray = C('EXAM_GROUP_TYPES');
        if($this->isPost()) {
            $resultScript = true;
            $groupModel = D('ExamGroup');
            $groupInfo = $_POST;
            $saveResult = $groupModel->save($groupInfo);
            $errorMsg = $saveResult['errorMsg'];
        }
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    protected function groupInfo() {
        $gid = abs($_GET['gid']);
        if(false == $gid) die('error');
        $groupModel = D('ExamGroup');
        if($this->isPost()) {
            $this->writeCheck($this->getAclKey('eGroup'));
            $resultScript = true;
            $groupInfo = $_POST;
            $saveResult = $groupModel->save($groupInfo);
            $errorMsg = $saveResult['errorMsg'];
        } else {
            $this->readCheck($this->getAclKey('eGroup'));
            $permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey('eGroup'));
            $permValue = $permInfo['permValue'];
            $groupInfo = $groupModel->find($gid);
        }
        $url = $_SERVER['REQUEST_URI'];
        $gTypeArray = C('EXAM_GROUP_TYPES');
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    protected function delGroup() {
        $groupId = abs($_POST['gid']);
        $groupModel = D('ExamGroup');
        $groupModel->delete($groupId);
        echo 1;
        exit;
    }
    
    protected function addExam() {
        $this->writeCheck($this->getAclKey('main'));
        if($this->isPost()) {
            $examInfo = $_POST;
            $examModel = D('Exam');
            $saveResult = $examModel->save($examInfo);
            echo json_encode($saveResult);
            exit;
        }
        $url = $_SERVER['REQUEST_URI'];
        $dlgId = abs($_GET['dlg']);
        $groupModel = D('ExamGroup');
        $groupId = abs($_GET['gid']);
        $groupInfo = $groupModel->find($groupId);
        $clsTypes = C('WEB_EXCEPT_TYPES');
        if(in_array($groupInfo['group_type'], $clsTypes)) {
            $setStuCls = true;
        } else {
            $checkStuInfo = true;
        }
        if(false == $groupInfo['group_id']) {
            DIE('错误的竞赛组别');
        }
        $gradeModel = D('GradeYear');
        $card = D('Card');
        $gradeOptions = $gradeModel->getGradeYears(7);
        $cardGroupArray = $card->arrayCardGroup(10);
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    protected function examInfo() {
        $examId = abs($_GET['id']);
        $aclKey = $this->getAclKey('main');
        $this->readCheck($aclKey);
        $permInfo = Permission::getPermInfo($this->loginUser, $aclKey);
        $permValue = $permInfo['permValue'];
        $examModel = D('Exam');
        if($this->isPost()) {
            $this->writeCheck($aclKey);
            $examInfo = $_POST;
            $saveResult = $examModel->save($examInfo);
            echo json_encode($saveResult);
            exit;
        }
        $url = $_SERVER['REQUEST_URI'];
        $dlgId = abs($_GET['dlg']);
        $examInfo = $examModel->find($examId);
        $examGroupType = $examInfo['group_type'];
        $clsTypes = C('WEB_EXCEPT_TYPES');
        if(in_array($examGroupType, $clsTypes)) {
            $setStuCls = true;
        } else {
            $checkStuInfo = true;
        }
        $gradeModel = D('GradeYear');
        $card = D('Card');
        $gradeOptions = $gradeModel->getGradeYears(7);
        $cardGroupArray = $card->arrayCardGroup(10);
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    protected function delExam() {
        $examId = abs($_POST['exam']);
        $examModel = D('Exam');
        $result = $examModel->delete($examId);
        echo json_encode($result);
        exit;
    }
    
    protected function setPosition(){
    	
    	$addExamPositionUrl = $this->getUrl('addExamPosition');
    	$jsonExamPositionUrl = $this->getUrl('jsonExamPosition');
    	$editExamPositionUrl = $this->getUrl('editExamPosition');
    	$delExamPositionUrl = $this->getUrl('delExamPosition');
    	
        $this->assign(get_defined_vars());
    	$this->display();
    }
    
    protected function jsonExamPosition(){
    	$rs  = D('Exam')->listExamPosition($this->_post());
    	outPut($rs);
    }
    
    protected function jsonPositionList(){
    	$rs = D('Exam')->listUnSelectPosition($this->_post());
    	outPut($rs);
    }
    protected function addExamPosition(){
    	$jsonPositionUrl = $this->getUrl('jsonPositionList');
    	$saveExamPositionUrl = $this->getUrl('saveExamPosition');
    	
    	
    	$examId = abs($this->_get('id'));
    	$this->assign(get_defined_vars());
    	$this->display();
    }
    protected function saveExamPosition(){
    	$rs = D('Exam')->saveExamPosition($this->_post());
    	outPut($rs);
    }
    protected function editExamPosition(){
    	$rs = D('Exam')->getExamPositionById($this->_get());
 
    	$this->assign('saveRoomSettingUrl', $this->getUrl('saveRoomSetting'));
    	$this->assign($rs);
    	$this->display();
    }
    protected function saveRoomSetting(){
		$rs = D('Exam')->saveRoomSetting($this->_post());
		outPut($rs);   	
    }
    protected function delExamPosition(){
    	$rs = D('Exam')->delExamPosition($this->_post());
    	outPut($rs);
    }
    
    protected function scoreTime() {
    	$groupId = abs($_POST['egid']);
    	$examModel = D('Exam');
    	echo json_encode($examModel->getScoreTimes($groupId));
    }
    
    public function examPosCount() {
    	$gTypeArray = C('EXAM_GROUP_TYPES');
    	$examUrl = $this->getUrl('jsonExamList');
    	$jsonGroupUrl = $this->getUrl('jsonGroupList');
    	$jsonPosCountUrl = $this->getUrl('jsonPosCount');
    	$exportUrl= $this->getUrl('exportCount');
    	$this->assign(get_defined_vars());
    	$this->display();
    }
    
    protected function jsonPosCount() {
    	$groupId = abs($_POST['groupId']);
    	$ePosModel = D('ExamPosition');
    	$countArray = $ePosModel->getGroupPosCount($groupId);
    	echo json_encode(array('rows'=>$countArray));
    }
    
    protected function exportCount() {
    	$ePosModel = D('ExamPosition');
    	$examModel = D('Exam');
    	$groupModel = D('ExamGroup');
    	$groupId = abs($_GET['groupId']);
    	$groupInfo = $groupModel->find($groupId);
    	$exams = $examModel->getExamList('is_remove=0 AND exam_status=1 AND group_id=' . $groupId, 1, 20);
    	$countArray = $ePosModel->getGroupPosCount($groupId);
    	$this->assign(get_defined_vars());
    	$this->display();
    	SysUtil::sendFile( $groupInfo['group_caption'] . '-考点限额表.xls');
    }
}

?>
