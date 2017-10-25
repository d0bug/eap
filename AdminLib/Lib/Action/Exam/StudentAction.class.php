<?php
class StudentAction extends ExamCommAction {
    public function permit() {
        $permValue = $this->permValue;
        $examTypeArray = C('EXAM_GROUP_TYPES');
        $jsonGroupUrl = $this->getUrl('jsonGroupList', 'Exam');
        $jsonExamUrl = $this->getUrl('jsonExamList', 'Exam');
        $applyStatusUrl = $this->getUrl('apply');
        $addGreenUrl = $this->getUrl('addGreen');
        $refuseApplyUrl = $this->getUrl('refuseApply');
        $applyPerm = Permission::getPermInfo($this->loginUser, $this->getAclKey('apply'));
        $applyPerm = $applyPerm['permValue'];
        $greenPerm = Permission::getPermInfo($this->loginUser, $this->getAclKey('green'));
        $greenPerm = $greenPerm['permValue'];
        $greenStatUrl = $this->getUrl('greenStat');
        $jsonApplyUrl = $this->getUrl('jsonApply');
        $jsonGreenUrl = $this->getUrl('jsonGreen');
        $delGreenUrl = $this->getUrl('delGreen');
        $exportGreenUrl = $this->getUrl('exportGreen');
        $userName = $this->loginUser->getUserName();
        $isExamSuperUser = in_array($userName, C('EXAM_SUPER_USERS'));
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    public function apply() {
    	if ($this->isPost()) {
    		$applyModel = D('Apply');
    		$applyId = abs($_POST['applyId']);
    		$status = intval($_POST['status']);
    		$reason = SysUtil::safeString($_POST['reason']);
    		$result = $applyModel->setApplyStatus($applyId, $status, $reason);
    		echo json_encode($result);
    		exit;
    	}
    	die('error');
    }
    
    protected function refuseApply() {
    	$applyModel = D('Apply');
    	if($this->isPost()) {
    		$this->apply();
    	}
    	$applyId = abs($_GET['id']);
    	$applyInfo = $applyModel->findApply($applyId);
    	$refuseApplyUrl = $this->getUrl('refuseApply');
    	$dialog = SysUtil::safeString($_GET['dlg']);
    	$this->assign(get_defined_vars());
    	$this->display();
    }
    
    public function green() {
    	//仅用作权限设置
    }
    
    protected function jsonApply() {
    	$permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey('apply'));
    	$permValue = $permInfo['permValue'];
    	if($permValue){
    		$examId = abs($_POST['exam']);
    		$applyModel = D('Apply');
    		$recordCount = $applyModel->getApplyCount($examId);
    		$currentPage = abs($_POST['page']);
    		$pageSize = abs($rows);
    		if($pageSize < 1) $pageSize = 20;
    		$pageCount = ceil($recordCount / $pageSize);
    		if($pageCount < 1) $pageCount = 1;
    		if($currentPage <1) $currentPage = 1;
    		if($currentPage > $pageCount) $currentPage = $pageCount;
    		$applyList = $applyModel->getApplyList($examId, $currentPage, $pageSize);
    		echo json_encode(array('total'=>$recordCount, 'rows'=>$applyList));
    	} else {
    		echo json_encode(array('total'=>0, 'rows'=>array()));
    	}
    	exit;
    }
    
    protected function addGreen() {
    	$permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey('green'));
    	$permValue = $permInfo['permValue'];
        $examModel = D('Exam');
    	if ($permValue & PERM_WRITE) {
    		if($this->isPost()) {
    			$greenModel = D('Green');
    			$examId = abs($_POST['examId']);
                $examInfo = $examModel->find($examId);
                if(false == $examInfo['exam_skip_grade'] && false == abs($examInfo['exam_money']) > 0) {
                    //免费考试
                    $examSuperUsers = C('EXAM_SUPER_USERS');
                    $userName = strtolower($this->loginUser->getUserName());
                    if(false == in_array($userName, $examSuperUsers)) {
                        echo json_encode(array('errorMsg'=>'免费考试不允许添加报名资格！'));
                        exit;
                    }

                }
    			$stuCode = preg_replace('/[^a-z0-9]/i', '', $_POST['stuCode']);
    			$areaCode = preg_replace('/[^a-z0-9]/i', '', $_POST['areaCode']);
    			$examCode = preg_replace('/[^a-z0-9]/i', '', $_POST['examCode']);
    			$result = $greenModel->save($examId, $stuCode, $areaCode, $examCode);
    			echo json_encode($result);
    			exit;
    		}
    		$dlgId = trim($_GET['dlg']);
    		$examId = abs($_GET['exam']);
    		$examInfo = $examModel->find($examId);
    		$addGreenUrl = $this->getUrl('addGreen');
    		$jsonSearchUrl = $this->getUrl('jsonSearchStu');
    		$areaModel = D('Area');
    		$areaOptions = $areaModel->getAreaOptions();
    		$this->assign(get_defined_vars());
    		$this->display();
    	} else {
    		die('Permission Denied');
    	}
    }
    
    protected function jsonGreen() {
    	$permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey('green'));
    	$permValue = $permInfo['permValue'];
    	if($permValue) {
    		$greenModel = D('Green');
    		if($_POST['keyword']) {
                $strExamIds = isset($_POST['exam']) ? SysUtil::safeSearch($_POST['exam']) : '';
                $arrExamIds = explode(',', $strExamIds);
                $currentPage = abs($_POST['page']);
                $pageSize = abs($_POST['rows']);
    			$keyword = SysUtil::safeSearch($_POST['keyword']);
                $recordCount = $greenModel->getGreenCount($arrExamIds, $keyword);
    			$recordList = $greenModel->getGreenList($arrExamIds, $currentPage, $pageSize, $keyword);
    			echo json_encode(array('total'=>$recordCount, 'rows'=>$recordList));
    		} else {
    			$examId = abs($_POST['exam']);
    			$recordCount = $greenModel->getGreenCount($examId);
    			$currentPage = abs($_POST['page']);
    			$pageSize = abs($_POST['rows']);
    			$greenList = $greenModel->getGreenList($examId, $currentPage, $pageSize);
    			echo json_encode(array('total'=>$recordCount, 'rows'=>$greenList));
    		}
    	} else {
    		echo json_encode(array('total'=>0, 'rows'=>array()));
    	}
    	exit;
    }

    protected function jsonSearchStu(){
    	$permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey('green'));
    	$permValue = $permInfo['permValue'];
    	if($permValue) {
    		$greenModel = D('Green');
    		if($_POST['keyword']) {
				$keyword = SysUtil::safeSearch($_POST['keyword']);
    			$recordList = $greenModel->searchStudents($keyword);
    			echo json_encode(array('rows'=>$recordList));
    		}
    	}else {
    		echo json_encode(array('total'=>0, 'rows'=>array()));
    	}
    	exit;
    }
    
    protected function greenStat() {
    	$examId = abs($_GET['exam']);
    	$examModel = D('Exam');
    	$examInfo = $examModel->find($examId);
    	$groupId = $examInfo['group_id'];
    	$greenModel = D('Green');
    	$statArray = $greenModel->getGreenStat($groupId);
    	$exportUrl = $this->getUrl('exportStat');
    	$this->assign(get_defined_vars());
    	$this->display();
    }
    
    protected function exportStat() {
    	$groupId = abs($_GET['gid']);
    	$greenModel = D('Green');
    	$groupModel = D('ExamGroup');
    	$groupInfo = $groupModel->find($groupId);
    	$statArray = $greenModel->getGreenStat($groupId);
    	$this->assign(get_defined_vars());
    	$this->display();
    	SysUtil::sendFile($groupInfo['group_caption'] . '-前台报名统计表.xls');
    }
    
    public function main() {
        $permValue = $this->permValue;
        $permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey('export'));
        $exportPerm = $permInfo['permValue'];
        $permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey('cancel'));
        $cancelPerm = $permInfo['permValue'];
        $permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey('exportPdf'));
        $pdfPerm = $permInfo['permValue'];
        $permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey('exportPosPdf'));
        $posPdfPerm = $permInfo['permValue'];
        $examTypeArray = C('EXAM_GROUP_TYPES');
        $jsonGroupUrl = $this->getUrl('jsonGroupList', 'Exam');
        $jsonExamUrl = $this->getUrl('jsonExamList', 'Exam');
        $jsonStudentUrl = $this->getUrl('jsonStuList');
        $addStudentUrl = $this->getUrl('addStudent');
        $examPosUrl = $this->getUrl('posList');
        $exportUrl = $this->getUrl('export');
        $exportPdfUrl = $this->getUrl('exportPdf');
        $posPdfUrl = $this->getUrl('exportPosPdf');
        $cancelUrl = $this->getUrl('cancel');
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    protected function posList() {
        $examStudentModel = D('ExamStudent');
        $examId = abs($_POST['exam']);
        $posArray = $examStudentModel->getPositions($examId);
        echo json_encode($posArray);
        exit;
    }
    
    protected function jsonStuList() {
        $examStudentModel = D('ExamStudent');
        $searchArgs = array();
        $searchArgs['examId'] = abs($_POST['exam']);
        if(0 == $searchArgs['examId']) {
            return json_encode(array('rows'=>array()));
        }
        $posCode = SysUtil::safeString(trim($_POST['pos_code']));
        if(strlen($posCode) > 1) {
            $searchArgs['posCode'] = $posCode;
        }
        $stuName = SysUtil::safeSearch(trim($_POST['stu_name']));
        if($stuName) {
            $searchArgs['stuName'] = $stuName;
        }
        
        $currentPage = abs($_POST['page']);
        if(false == $currentPage) $currentPage = 1;
        $pageSize = abs($_POST['rows']);
        if(false == $pageSize) $pageSize = 20;
        
        $studentCount = $examStudentModel->getStudentCount($searchArgs);
        $studentList = $examStudentModel->getStudentList($searchArgs, $currentPage, $pageSize);
        echo json_encode(array('total'=>$studentCount, 'rows'=>$studentList));
        exit;
    }
    
    public function export() {
        $examStudentModel = D('ExamStudent');
        $examId = abs($_POST['examId']);
        $examModel = D('Exam');
        $examInfo = $examModel->find($examId);
        $searchArgs['examId'] = $examId;
        if(0 == $searchArgs['examId']){
            echo '<script type="text/javascript">alert("请选择竞赛")</script>';
            exit;
        }
        $posCode = SysUtil::safeString($_POST['pos_code']);
        if($posCode) {
            $posModel = D('Position');
            $posInfo = $posModel->find($posCode);
            $searchArgs['posCode'] = $posCode;
        }
        $studentList = $examStudentModel->getStudentList($searchArgs);
        $title = $examInfo['group_caption'] . '考生名单【' . $examInfo['exam_caption'] . '】';
        if($posInfo) {
            $title .= '【' . $posInfo['pos_caption'] . '】';
        }
        $this->assign(get_defined_vars());
        $this->display('exp_examStudent');
        $fileName .= $title . '.xls';
        SysUtil::sendFile($fileName);
    }
    
    public function exportPdf() {
    	$this->readCheck();
    	import('ORG.Util.PdfCreator');
    	$pdfCreator = new PdfCreator();
    	$examId = abs($_GET['exam']);
    	$posCode = SysUtil::safeString($_GET['pos']);
    	$searchArgs = array('examId'=>$examId, 'posCode'=>$posCode);
    	$examModel = D('Exam');
    	$posModel = D('Position');
    	$gradeModel = D('GradeYear');
    	$gradeArray = $gradeModel->getGradeYears();
    	$examStudentModel = D('ExamStudent');
    	$examInfo = $examModel->find($examId);
    	$posInfo = $posModel->find($posCode);
    	$stuList = $examStudentModel->getStudentList($searchArgs);
    	$roomNum = 1;
    	$stuCount = sizeof($stuList);
    	$stuArray = array();
    	foreach ($stuList as $key=>$stu) {
    		if($stu['order_status'] != 1) {
	    		if($stu['room_num'] == $roomNum) {
	    			$stuArray[] = $stu;
	    		} else {
	    			$this->assign(get_defined_vars());
	    			$html = $this->fetch();
	    			$pdfCreator->addPage($html);
	    			$stuArray = array($stu);
	    			$roomNum = $stu['room_num'];
	    		}
    		}
    	}
    	$this->assign(get_defined_vars());
    	$html = $this->fetch();
    	$pdfCreator->addPage($html);
    	
    	$pdfDir = C('EXPORT_PDF_DIR') . '/' . $examId;
    	$pdfName = $posInfo['pos_caption'] . '-考场名单.pdf';
    	$pdfFile = $pdfDir . '/' . $pdfName;
    	@mkdir($pdfDir, 0777, true);
    	$pdfCreator->savePdf($pdfFile);
    	SysUtil::sendFile($pdfName, 'application/octet-stream', array('filepath'=>$pdfFile));
    }
    
    public function exportPosPdf() {
    	$this->readCheck();
    	import('ORG.Util.PdfCreator');
    	$pdfCreator = new PdfCreator('B5');
    	$examId = abs($_GET['exam']);
    	$posCode = SysUtil::safeString($_GET['pos']);
    	$searchArgs = array('examId'=>$examId, 'posCode'=>$posCode);
    	$examModel = D('Exam');
    	$posModel = D('Position');
    	$groupModel = D('ExamGroup');
    	$examStudentModel = D('ExamStudent');
    	$examInfo = $examModel->find($examId);
    	$posInfo = $posModel->find($posCode);
    	$groupInfo = $groupModel->find($examInfo['group_id']);
    	$eposModel = D('ExamPosition');
    	$roomNames = $eposModel->getRoomNames($examId, $posCode);
    	$roomCountList = $examStudentModel->getRoomCountArray($examId, $posCode);
    	$this->assign(get_defined_vars());
    	$html = $this->fetch('posPdf');
    	$pdfCreator->addPage($html);
    	$pdfDir = C('EXPORT_PDF_DIR') . '/' . $examId;
    	$pdfName = $posInfo['pos_caption'] . '-考生统计表.pdf';
    	$pdfFile = $pdfDir . '/' . $pdfName;
    	@mkdir($pdfDir, 0777, true);
    	$pdfCreator->savePdf($pdfFile);
    	SysUtil::sendFile($pdfName, 'application/octet-stream', array('filepath'=>$pdfFile));
    }
    
    public function cancel() {
        if($this->isPost()) {
            $examStudentModel = D('ExamStudent');
            $stuCode = SysUtil::safeString($_POST['stuCode']);
            $signupId = SysUtil::safeString($_POST['id']);
            $cancelResult = $examStudentModel->cancelSignup($stuCode, $signupId);
        } else {
            $cancelResult = array('errorMsg'=>'非法请求');
        }
        
        echo json_encode($cancelResult);
        exit;
    }
    
    public function cancelLog() {
    	$permValue = $this->permValue;
        $examTypeArray = C('EXAM_GROUP_TYPES');
        $jsonGroupUrl = $this->getUrl('jsonGroupList', 'Exam');
        $jsonExamUrl = $this->getUrl('jsonExamList', 'Exam');
        $jsonLogUrl = $this->getUrl('jsonLog');
    	$this->assign(get_defined_vars());
    	$this->display();
    }
    
    protected function jsonLog() {
    	$esModel = D('ExamStudent');
    	$examId = abs($_POST['exam']);
    	if($examId) {
	    	$searchArgs = array('examId'=>$examId, 'keyword'=>SysUtil::safeSearch($_POST['keyword']));
	    	$recordCount = $esModel->countCancelLog($searchArgs);
	    	$currentPage = abs($_POST['page']);
	    	$pageSize = abs($_POST['rows']);
	    	$logList = $esModel->cancelLogList($searchArgs, $currentPage, $pageSize);
	    	echo json_encode(array('total'=>$recordCount, 'rows'=>$logList));
    	} else {
    		echo json_encode(array('total'=>0, 'rows'=>array()));
    	}
    }
    
    protected function addStudent() {
    	$examId = abs($_GET['exam']);
    	$examModel = D('Exam');
    	$examInfo = $examModel->find($examId);
    	$posModel = D('ExamPosition');
    	$gradeModel = D('GradeYear');
    	$gradeArray = $gradeModel->getGradeYears();
    	$examSuperUsers = C('EXAM_SUPER_USERS');
    	$userName = strtolower($this->loginUser->getUserName());
    	if(in_array($userName, $examSuperUsers)) {
    		$isSuper = true;
    	}
    	if($isSuper || false == $examInfo['exam_skip_grade']) {
    		$freePosList = $posModel->getFreePosList($examId);
    		$jsonEsInfoUrl = $this->getUrl('jsonEsInfo');
	    	$signupUrl = $this->getUrl('signup');
	    	$resetPwdPerm = Permission::getPermInfo($this->loginUser, $this->getAclKey('resetPwd', 'Student', 'Student'));
	    	$resetPwdPerm = $resetPwdPerm['permValue'];
	    	$resetPwdUrl = $this->getUrl('resetPwd', 'Student', 'Student');
    	}
    	
    	
    	$dialog = SysUtil::safeString($_GET['dlg']);
    	$this->assign(get_defined_vars());
    	$this->display();
    }
    
    protected function jsonEsInfo() {
    	$examId = abs($_POST['exam']);
    	$stuCode = SysUtil::safeSearch($_POST['stuCode']);
    	$esModel = D('ExamStudent');
    	$esInfo = $esModel->getStuInfo($examId, $stuCode);
    	echo json_encode($esInfo);
    }
    
    protected  function signup() {
    	$examId = abs($_POST['exam']);
    	$posCode = SysUtil::safeString($_POST['pos_code']);
    	$stuCode = SysUtil::safeString($_POST['stu_code']);
    	$stuMobile = SysUtil::safeString($_POST['stu_mobile']);
    	$esModel = D('ExamStudent');
    	$signupResult = $esModel->signup($examId, $stuCode, $posCode, $stuMobile);
    	if(false == $signupResult['errorMsg']) {
    		$esModel->sendSms($signupResult);
    	}
    	echo json_encode($signupResult);
    }
    
    protected function signupTemp() {
    	$examId = abs($_POST['examId']);
    	$posCode = SysUtil::safeString($_POST['posCode']);
    	$stuCode = SysUtil::safeString($_POST['stuCode']);
    	$stuMobile = SysUtil::safeString($_POST['stuMobile']);
    	$esModel = D('ExamStudent');
    	$signupInfo = $esModel->signupTemp($examId, $stuCode, $posCode, $stumobile);
    	echo json_encode($signupInfo);
    }
    
    protected function delGreen() {
    	$id = SysUtil::uuid($_POST['id']);
    	$greenModel = D('Green');
    	$delResult = $greenModel->delGreen($id);
    	echo json_encode($delResult);
    }
    
    protected function exportGreen() {
    	$examId = abs($_GET['exam']);
    	$examModel = D('Exam');
    	$examInfo = $examModel->find($examId);
    	$greenModel = D('Green');
    	$greenList = $greenModel->getGreenList($examId, 1, 9999);
    	$title = $examInfo['group_caption'] . '报名统计表';
        $this->assign(get_defined_vars());
        $this->display('exp_examGreen');
        $fileName .= $title . '.xls';
        SysUtil::sendFile($fileName);
    }
};
?>