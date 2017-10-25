<?php
class AwardAction extends ExamCommAction {
	public function __construct(){
		parent::__construct();
		$permInfo = Permission::getPermInfo($this->getLoginUser(), $this->getAclKey('lists'));
		$this->assign('permValue', $permInfo['permValue']);
	}
	
	public function lists() {
		$gTypeArray = C('EXAM_GROUP_TYPES');
    	$jsonExamUrl = $this->getUrl('jsonExamList', 'Exam');
        $jsonGroupUrl = $this->getUrl('jsonGroupList', 'Exam');
        $jsonAwardUrl = $this->getUrl('jsonAward');
        $addAwardUrl = $this->getUrl('addAward');
        $editAwardUrl = $this->getUrl('editAward');
        $delAwardUrl = $this->getUrl('delAward');
        $saveAwardUrl = $this->getUrl('saveAward');
        $stuListUrl = $this->getUrl('awardStuList');
        $tplSettingUrl = $this->getUrl('tplSetting');
        $this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function jsonAward() {
		$examId = abs($_POST['exam_id']);
		$awardModel = D('Award');
		$recordCount = $awardModel->getAwardCount($examId);
		$currentPage = abs($_POST['page']);
		$pageSize = abs($_POST['rows']);
		if($currentPage < 1) $currentPage = 1;
		if($pageSize < 1) $pageSize = 20;
		$awardList = $awardModel->getAwardList($examId, $currentPage, $pageSize);
		echo json_encode(array('total'=>$recordCount, 'rows'=>$awardList));
	}
	
	protected function addAward() {
		$examId = abs($_GET['exam']);
		$examModel = D('Exam');
		$examInfo = $examModel->find($examId);
		$awardModel = D('Award');
		$awardTypes = $awardModel->getAwardTypes($examId);
		$typeNameUrl = $this->getUrl('setTypeName');
		$title = '添加竞赛奖项';
		$this->assign(get_defined_vars());
		$this->display('awardInfo');
	}
	
	protected function editAward() {
		$awardModel = D('Award');
		$awardId = SysUtil::uuid($_GET['id']);
		$awardInfo = $awardModel->find($awardId);
		$examId = $awardInfo['exam_id'];
		$examModel = D('Exam');
		$examInfo = $examModel->find($examId);
		$awardModel = D('Award');
		$awardTypes = $awardModel->getAwardTypes($examId);
		$typeNameUrl = $this->getUrl('setTypeName');
		$title = '修改奖项信息';
		$this->assign(get_defined_vars());
		$this->display('awardInfo');
	}
	
	protected function saveAward() {
		$awardInfo = $_POST;
		$awardModel = D('Award');
		$saveResult = $awardModel->save($awardInfo);
		echo json_encode($saveResult);
		exit;
	}
	
	protected function setTypeName() {
		$nameInfo = $_POST;
		$awardModel = D('Award');
		$saveResult = $awardModel->setTypeName($nameInfo);
		echo json_encode($saveResult);
		exit;
	}
	
	protected function delAward() {
		$awardId = SysUtil::uuid($_POST['id']);
		$awardModel = D('Award');
		$delResult = $awardModel->delete($awardId);
		echo json_encode($delResult);
		exit;
	}
	
	protected function awardStuList() {
		$awardId = SysUtil::uuid($_GET['id']);
		$awardModel = D('Award');
		$awardInfo = $awardModel->find($awardId);
		#$awardType = $awardModel->getAwardType($examId, $awardInfo['award_type']);
		$jsonStuList = $this->getUrl('jsonStuList');
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function jsonStuList() {
		$awardId = SysUtil::uuid($_GET['id']);
		$awardModel = D('Award');
		$awardInfo = $awardModel->find($awardId);
		$recordCount = $awardModel->getAwardStuCount($awardInfo);
		$currentPage = abs($_POST['page']);
		$pageSize = abs($_POST['rows']);
		if($currentPage < 1) $currentPage = 1;
		if($pageSize < 1) $pageSize = 10;
		$stuList = $awardModel->getAwardStuList($awardInfo, $currentPage, $pageSize);
		echo json_encode(array('total'=>$recordCount, 'rows'=>$stuList));
		exit;
	}
	
	protected function tplSetting() {
		$examId = abs($_GET['exam']);
		$examModel = D('Exam');
		$awardModel = D('Award');
		$examInfo  = $examModel->find($examId);
		$cfgInfo = $awardModel->findCfg($examId);
		if($cfgInfo) {
			$tplCfg = $cfgInfo['award_cfg'];
		}
		$fontArray = C('FONT_ARRAY');
		$colorArray = C('COLOR_ARRAY');
		$saveCfgUrl = $this->getUrl('saveTplCfg');
		$upTplUrl = $this->getUrl('uploadTpl');
		$previewUrl = $this->getUrl('tplPreview');
		$statusUrl = $this->getUrl('switchStatus');
		$items = $awardModel->getAwardItems();
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function switchStatus() {
		$examId = abs($_POST['examId']);
		$awardModel = D('Award');
		$result = $awardModel->switchStatus($examId);
		echo json_encode($result);
		exit;
	}
	
	protected function uploadTpl() {
		$examId = abs($_POST['exam_id']);
		$awardModel = D('Award');
		$tplInfo = $_FILES['award_file'];
		$uploadResult = $awardModel->uploadTpl($examId, $tplInfo);
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function saveTplCfg() {
		$awardModel = D('Award');
		$saveResult = $awardModel->saveTplCfg($_POST);
		echo json_encode($saveResult);
		exit;
	}
	
	protected function tplPreview() {
		$examId = abs($_POST['examId']);
		$awardModel = D('Award');
		$awardCfg = $awardModel->findCfg($examId);
		if($awardCfg) {
			$awardImg = $this->getUrl('awardImg', MODULE_NAME, GROUP_NAME, array('eid'=>$examId));
		}
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function awardImg() {
		$examId = abs($_GET['eid']);
		import('COM.Gaosi.Exam.AwardImage');
		AwardImage::preview($examId);
	}
}
?>