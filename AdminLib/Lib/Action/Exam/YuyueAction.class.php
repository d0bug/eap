<?php
class YuyueAction extends ExamCommAction {
	public function main() {
		$gTypeArray = C('EXAM_GROUP_TYPES');
        $jsonGroupUrl = $this->getUrl('jsonGroupList', 'Exam');
        $jsonYyGroupUrl = $this->getUrl('jsonYyGroup');
        $addGroupUrl = $this->getUrl('addGroup');
        $editGroupUrl = $this->getUrl('editGroup');
        $delGroupUrl = $this->getUrl('delGroup');
        $addBatchUrl = $this->getUrl('addBatch');
        $editBatchUrl = $this->getUrl('editBatch');
        $delBatchUrl = $this->getUrl('delBatch');
        $jsonBatchUrl = $this->getUrl('jsonBatch');
        $posUrl = $this->getUrl('position');
        $timeUrl = $this->getUrl('time');
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	
	protected function jsonYyGroup() {
		$eGroupId = abs($_POST['gid']);
		$yuyueModel = D('Yuyue');
		$yGroupList = $yuyueModel->getYGroupList($eGroupId);
		echo json_encode(array('rows'=>$yGroupList));
	}
	
	protected function jsonBatch() {
		$yGroupId = SysUtil::uuid($_POST['yGroupId']);
		$yuyueModel = D('Yuyue');
		$currentPage = abs($_POST['page']);
		$pageSize = abs($_POST['rows']);
		$recordCount = $yuyueModel->getBatchCount($yGroupId);
		$batchList = $yuyueModel->getBatchList($yGroupId, $currentPage, $pageSize);
		echo json_encode(array('total'=>$recordCount, 'rows'=>$batchList));;
	}
	
	
	protected function addGroup() {
		$yuyueModel = D('Yuyue');
		$eGroupId = abs($_GET['gid']);
		if($this->isPost()) {
			$saveResult = $yuyueModel->saveYGroup($_POST);
			echo json_encode($saveResult);
			exit;
		} else {
			$addGroupUrl = $this->getUrl('addGroup');
			$examList = $yuyueModel->getYFreeExams($eGroupId);
			$dialog = SysUtil::safeString($_GET['dlg']);
			$this->assign(get_defined_vars());
			$this->display();
		}
	}
	
	protected function editGroup() {
		$yGroupId = SysUtil::uuid($_GET['ygid']);
		$yuyueModel = D('Yuyue');
		if($this->isPost()) {
			$saveResult = $yuyueModel->saveYGroup($_POST);
			echo json_encode($saveResult);
			exit;
		} else {
			$editGroupUrl = $this->getUrl('editGroup');
			$yGroupInfo = $yuyueModel->findGroup($yGroupId);
			$examList = $yuyueModel->getYFreeExams($yGroupInfo['exam_group_id'], $yGroupId);
			$dialog = SysUtil::safeString($_GET['dlg']);
			$this->assign(get_defined_vars());
			$this->display();
		}
	}
	
	protected function delGroup() {
		$yGroupId = SysUtil::uuid($_POST['yGroupId']);
		$yuyueModel = D('Yuyue');
		$delResult = $yuyueModel->delYGroup($yGroupId);
		echo json_encode($delResult);
		exit;
	}
	
	protected function time() {
		$yGroupId = SysUtil::uuid($_GET['ygid']);
		$jsonTimeUrl = $this->getUrl('jsonTimeList');
		$saveTimeUrl = $this->getUrl('saveTime');
		$delTimeUrl = $this->getUrl('delTime');
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function position() {
		$yGroupId = SysUtil::uuid($_GET['ygid']);
		$jsonPosUrl = $this->getUrl('jsonPosList');
		$addPosUrl = $this->getUrl('addPos');
		$editPosUrl = $this->getUrl('editPos');
		$delPosUrl = $this->getUrl('delPos');
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function jsonPosList() {
		$yGroupId = SysUtil::uuid($_GET['ygid']);
		$yuyueModel = D('Yuyue');
		$posList = $yuyueModel->getYPosList($yGroupId);
		echo json_encode(array('rows'=>$posList));
	}
	
	protected function jsonTimeList() {
		$yGroupId = SysUtil::uuid($_GET['ygid']);
		$yuyueModel = D('Yuyue');
		$timeList = $yuyueModel->getTimeList($yGroupId);
		echo json_encode(array('rows'=>$timeList));
	}
	
	protected function addPos() {
		$yGroupId = SysUtil::uuid($_GET['ygid']);
		$yuyueModel = D('Yuyue');
		if($this->isPost()) {
			$saveResult = $yuyueModel->savePosition($_POST);
			echo json_encode($saveResult);
			exit;
		} else {
			$ePosList = $yuyueModel->getEPosList($yGroupId);
			$addPosUrl = $this->getUrl('addPos');
			$dialog = SysUtil::safeString($_GET['dlg']);
			$this->assign(get_defined_vars());
			$this->display();
		}
	}
	
	protected function editPos() {
		$posId = SysUtil::uuid($_GET['pid']);
		$yuyueModel = D('Yuyue');
		if($this->isPost()){
			$saveResult = $yuyueModel->savePosition($_POST);
			echo json_encode($saveResult);
			exit;
		} else {
			$posInfo = $yuyueModel->findPos($posId);
			$ePosList = $yuyueModel->getEPosList($posInfo['ygroup_id']);
			$editPosUrl = $this->getUrl('editPos');
			$dialog = SysUtil::safeString($_GET['dlg']);
			$this->assign(get_defined_vars());
			$this->display();
		}
	}
	
	protected function delPos() {
		$posId = SysUtil::uuid($_POST['pos_id']);
		$yuyueModel = D('Yuyue');
		if($this->isPost()) {
			$delResult = $yuyueModel->delPos($posId);
			echo json_encode($delResult);
			exit;
		}
		die('access denid');
	}
	
	protected function saveTime() {
		$yGroupId = SysUtil::uuid($_GET['ygid']);
		$yuyueModel = D('Yuyue');
		if($this->isPost()) {
			$saveResult = $yuyueModel->saveTime($_POST);
			echo json_encode($saveResult);
			exit;
		} else {
			$this->assign(get_defined_vars());
			$this->display();
		}
		
	}
	
	protected function delTime() {
		$timeId = SysUtil::uuid($_POST['tid']);
		$yuyueModel = D('Yuyue');
		if($this->isPost()) {
			$delResult = $yuyueModel->delTime($timeId);
			echo json_encode($delResult);
			exit;
		}
	}
	
	protected function addBatch() {
		$yGroupId = SysUtil::uuid($_GET['ygid']);
		$yuyueModel = D('Yuyue');
		if($this->isPost()) {
			$saveResult = $yuyueModel->addBatch($_POST);
			echo json_encode($saveResult);
			exit;
		} else {
			$addBatchUrl = $this->getUrl('addBatch');
			$groupInfo = $yuyueModel->findGroup($yGroupId);
			$jsonPosUrl = $this->getUrl('jsonPosList') . '/ygid/' . $yGroupId;
			$jsonTimeUrl = $this->getUrl('jsonTimeList') . '/ygid/' . $yGroupId;
			$addBatchUrl = $this->getUrl('addBatch');
			$dialog = SysUtil::safeString($_GET['dlg']);
			$dateArray = $yuyueModel->getDateArray($groupInfo);
			$this->assign(get_defined_vars());
			$this->display();
		}
	}
	
	protected function editBatch() {}
	
	protected function delBatch() {
		$batchId = SysUtil::uuid($_POST['bid']);
		$yuyueModel = D('Yuyue');
		$delResult = $yuyueModel->delBatch($batchId);
		echo json_encode($delResult);
		exit;
	}
	
	public function student(){
		$permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey('student'));
		$permValue = $permInfo['permValue'];
		$gTypeArray = C('EXAM_GROUP_TYPES');
        $jsonGroupUrl = $this->getUrl('jsonGroupList', 'Exam');
        $jsonYyGroupUrl = $this->getUrl('jsonYyGroup');
        $jsonStuUrl = $this->getUrl('jsonStuList');
        $addStudentUrl = $this->getUrl('addStudent');
        $scoreTimeUrl = $this->getUrl('scoreTime', 'Exam');
        $initFormUrl = $this->getUrl('initForm');
        $printUrl = $this->getUrl('printGroup');
        $reportUrl = C('REPORT_URL');
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function initForm() {
		$ygid = SysUtil::uuid($_POST['ygid']);
		$yuyueModel = D('Yuyue');
		$yGroupInfo = $yuyueModel->findGroup($ygid);
		$posList = $yuyueModel->getPosList($yGroupInfo);
		$dateArray = $yuyueModel->getDateArray($yGroupInfo);
		$dateList = array();
		foreach ($dateArray as $date=>$dateText) {
			$dateList[] = array('date'=>$date, 'dateText'=>$dateText);
		}
		echo json_encode(array('posList'=>$posList, 'dateList'=>$dateList));
	}
	
	protected function addStudent() {
		$this->writeCheck($this->getAclKey('student'));
		$yuyueModel = D('Yuyue');
		if($this->isPost()) {
			$saveResult = $yuyueModel->saveYuyueInfo($_POST);
			if(false == $saveResult['errorMsg']) {
				import('COM.MsgSender.SmsSender');
				$stuCode = $_POST['stu_code'];
				$stuModel = D('Student');
				$stuInfo = $stuModel->getStuInfo($stuCode);
				if($stuInfo['sparents1phone']) {
					$smsContent = $yuyueModel->getYuyueSms($_POST, $stuInfo);
					SmsSender::sendSms($stuInfo['sparents1phone'], $smsContent);
				}
			}
			echo json_encode($saveResult);
			exit;
		}
		$searchStuUrl = $this->getUrl('searchStudents');
		$jsonBatchUrl = $this->getUrl('loadBatch');
		$saveYuyueInfoUrl = $this->getUrl('addStudent');
		$dialog = SysUtil::safeString($_GET['dlg']);
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function jsonStuList() {
		$yuyueModel = D('Yuyue');
		$currentPage = abs($_POST['page']);
		$pageSize = abs($_POST['rows']);
		$currentPage = $currentPage > 0 ? $currentPage : 1;
		$pageSize = $pageSize > 0 ? $pageSize : 20;
		$searchArgs = array('ygid'=>SysUtil::uuid($_POST['ygid']));
		if($_POST['keyword']) {
			$searchArgs['keyword'] = SysUtil::safeString($_POST['keyword']);
		} else {
			$searchArgs = $_POST;
		}
		$stuCount = $yuyueModel->getStuCount($searchArgs);
		$stuList = $yuyueModel->getStuList($searchArgs, $currentPage, $pageSize);
		echo json_encode(array('total'=>$stuCount, 'rows'=>$stuList));
	}
	
	protected function searchStudents() {
		$yuyueModel = D('Yuyue');
		$searchArgs['egid'] = abs($_POST['egid']);
		$searchArgs['keyword'] = SysUtil::safeString($_POST['keyword']);
		$stuList = $yuyueModel->searchStudents($searchArgs);
		echo json_encode(array('rows'=>$stuList));
	}
	
	protected function loadBatch() {
		$yuyueModel = D('Yuyue');
		$yGroupInfo = $yuyueModel->getYGroupInfo($_POST);
		echo json_encode($yGroupInfo);
	}
	
	protected function printGroup() {
		$yuyueModel = D('Yuyue');
		$yuyueModel->doPrint($_GET);
	}
	
	public function downPrint() {
		$yuyueModel = D('Yuyue');
		if($_GET['gid']) {
			$printGroupInfo = $yuyueModel->findPrintGroup($_GET['gid']);
			$zipFile = $yuyueModel->zipGroup($printGroupInfo);
			SysUtil::sendFile(basename($zipFile), 'application/octet-stream', array('filepath'=>$zipFile));
		} else {
			$printGroups = $yuyueModel->getPrintGroups();
			$this->assign(get_defined_vars());
			$this->display();
		}
	}

	public function areaPrint() {
		$permValue = $this->permValue;
        $examTypeArray = C('EXAM_GROUP_TYPES');
        $jsonGroupUrl = $this->getUrl('jsonGroupList', 'Exam');
        $jsonExamUrl = $this->getUrl('jsonExamList', 'Exam');
        $jsonPosUrl = $this->getUrl('jsonPrintPositions');
		$addPrintUrl = $this->getUrl('addAreaPrint');
		$this->assign(get_defined_vars());
		$this->display();
	}

	protected function jsonPrintPositions() {
		$examId = abs($_POST['exam']);
		$printModel = D('Print');
		$posList = $printModel->getPrintPositions($examId);
		echo json_encode(array('rows'=>$posList));
	}

	protected function addAreaPrint() {
		$examId = abs($_POST['examId']);
		$posCode = SysUtil::safeString($_POST['posCode']);
		$totalCount = abs($_POST['cnt']);
		$printModel = D('Print');
		$addResult = $printModel->addPrint($examId, $posCode, $totalCount);
		echo json_encode($addResult);
	}
}
?>