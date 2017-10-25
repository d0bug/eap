<?php
class ExamAction extends OperatorCommAction {
	public function signup() {
		$stuModel = D('Student');
		$stuPwd = $stuModel->getStuPasswd($this->student);
		$examModel = D('Exam');
		$stuExams = $examModel->getStuExams($this->student);
		$dispatchUrl = $this->getUrl('dispatch');
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	public function dispatch() {
		$examId = abs($_POST['examId']);
		$examModel = D('Exam');
		$examInfo = $examModel->find($examId);
		if($examInfo['exam_money'] > 0) {
			echo $this->getUrl('pay', MODULE_NAME, GROUP_NAME, array('exam'=>$examId, 'tm'=>time()));
		} else {
			$esModel = D('ExamStudent');
			if($esModel->getSignupInfo($examId, $this->student['scode'])) {
				echo $this->getUrl('signupInfo', MODULE_NAME, GROUP_NAME, array('exam'=>$examId, 'tm'=>time()));
			} else {
				echo $this->getUrl('posList', MODULE_NAME, GROUP_NAME, array('exam'=>$examId, 'tm'=>time()));
			}
		}
	}
	
	public function pay() {
		die('对收费考试的支持暂未开发');
		if($this->isPost()) {
			
		}
		$this->assign(get_defined_vars());
		$this->display('pay');
	}
	
	public function signupInfo() {
		$examId = abs($_GET['exam']);
		$esModel = D('ExamStudent');
		$signupInfo = $esModel->getSignupInfo($examId, $this->student['scode']);
		$posModel = D('Position');
		$posInfo = $posModel->find($signupInfo['pos_code']);
		$this->assign(get_defined_vars());
		$this->display('signupInfo');
	}
	
	public function posList() {
		$examId = abs($_GET['exam']);
		$posModel = D('ExamPosition');
		$freePosList = $posModel->getFreePosList($examId);
		$signupExamUrl = $this->getUrl('signupExam');
		$signupInfoUrl = $this->getUrl('signupInfo', MODULE_NAME, GROUP_NAME, array('exam'=>$examId));
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	public function signupExam() {
		if($this->isPost()) {
			$examId = abs($_POST['examId']);
	    	$posCode = SysUtil::safeString($_POST['posCode']);
	    	$stuCode = $this->student['scode'];
	    	$stuMobile = $this->student['sparents1phone'];
	    	$channel = SysUtil::safeString($_POST['channel']);
	    	$esModel = D('ExamStudent');
	    	$signupResult = $esModel->signup($examId, $stuCode, $posCode, $stuMobile, $channel,$this->operator['scode']);
	    	if(false == $signupResult['errorMsg']) {
	    		$esModel->sendSms($signupResult);
	    	}
	    	echo json_encode($signupResult);
		}
	}
	
	
}
?>