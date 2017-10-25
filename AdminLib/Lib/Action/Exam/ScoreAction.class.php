<?php
class ScoreAction extends ExamCommAction {
    public function addScore() {
        $gTypeArray = C('EXAM_GROUP_TYPES');
        $jsonExamUrl = $this->getUrl('jsonExamList', 'Exam');
        $jsonSubjectUrl = $this->getUrl('jsonExamSubjects', 'Paper');
        $addScoreUrl = $this->getUrl('jsonAddScore');
        $subjectModel = D('Subject');
        $subjectArray = $subjectModel->getSubjectArray();
        $jsonSubjectArray = json_encode($subjectArray);
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    public function lists() {
    	$permValue = $this->permValue;
        $gTypeArray = C('EXAM_GROUP_TYPES');
        $jsonExamUrl = $this->getUrl('jsonExamList', 'Exam');
        $jsonGroupUrl = $this->getUrl('jsonGroupList', 'Exam');
        $jsonSubjectUrl = $this->getUrl('jsonExamSubjects', 'Paper');
        $addGroupUrl = $this->getUrl('addGroup','Student', 'Student');
        $jsonExamInfo = $this->getUrl('jsonExamInfo');
        $jsonScoreGrid = $this->getUrl('jsonScoreGrid');
        $exportPerm = Permission::getPermInfo($this->loginUser, $this->getAclKey('export'));
        $exportPerm = $exportPerm['permValue'];
        $groupPerm = Permission::getPermInfo($this->loginUser, $this->getAclKey('addGroup', 'Student', 'Student'));
        $groupPerm = $groupPerm['permValue'];
        $exportUrl = $this->getUrl('export');
        $reportUrl = C('REPORT_URL');
        $scanUrl = $this->getUrl('scanFiles');
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    protected  function jsonExamInfo() {
    	$examId = abs($_POST['exam_id']);
    	if($examId) {
	    	$examModel = D('Exam');
	    	$paperModel = D('Paper');
	    	$examInfo = $examModel->find($examId);
	    	$paperArray = array();
	    	$paperList = $paperModel->getPaperList($examId);
	    	$awardModel = D('Award');
	    	$awardList = $awardModel->getAwardList($examId);
	    	$awardArray = array();
	    	$examAwards = array();
	    	foreach ($awardList as $award) {
	    		$awardArray[$award['award_type']] = array('field'=>$award['award_type']);
	    		if($award['award_type'] != 'total') {
	    			$awardCaption = '[' . $awardModel->getAwardType($examId, $award['award_type']) . ']  ' . $award['award_caption'];
	    			$examAwards[] = array('id'=>$award['id'], 'caption'=>$awardCaption);
	    		}
	    	}
	    	foreach ($awardList as $award) {
	    		if($award['award_type'] == 'total') {
	    			$awardCaption = '[综合奖项]  ' . $award['award_caption'];
	    			$examAwards[] = array('id'=>$award['id'], 'caption'=>$awardCaption);
	    		}
	    	}
	    	$typeCaptions = array('real'=>'卷', 'addon'=>'附加卷');
	    	$awards = array();
	    	foreach ($paperList as $paper) {
	    		if($paper['paper_type'] != 'virtual') {
	    			$key = $paper['subject'] . '_' . $paper['paper_type'];
	    			$paperArray[$key] = array('type'=>$key, 
	    									  'field'=>$key . '_score', 
	    									  'title'=>$paper['subject_caption'] . $typeCaptions[$paper['paper_type']]);
	    			if($awardArray[$key]) {
	    				$awardArray[$key]['title'] = $awardArray[$key]['title'] = $awardModel->getAwardType($examId, $key);
	    			}
	    		} else {
	    			$key = $paper['subject'] . '_' . $paper['paper_type'] . '_' . $paper['paper_id'];
	    			$paperArray[$key] = array('type'=>$key, 
	    									  'field'=>$key . '_score', 
	    									  'title'=>$paper['subject_caption'] . $typeCaptions[$paper['paper_type']] . '(' . $paper['paper_caption'] . ')');
	    			if($awardArray[$key]) {
	    				$awardArray[$key]['title'] = $awardModel->getAwardType($examId, $key);
	    			}
	    		}
	    	}
	    	if($paperList) {
	    		$paperArray['total_score'] = array('type'=>'total', 'field'=>'total_score', 'title'=>'总成绩');
	    		if($awardArray['total']) {
	    			$awardArray['total']['title'] = $awardModel->getAwardType($examId, 'total');
	    		}
	    	}
	    	$examInfo['awards'] = $awardArray;
	    	$examInfo['examAwards'] = $examAwards;
	    	$examInfo['papers'] = array_values($paperArray);
    	} else {
    		$examInfo = array();
    	}
    	echo json_encode($examInfo);exit;
    }
    
    protected  function jsonScoreGrid($addGroup=false) {
    	$examId = abs($_GET['exam']);
    	if($examId) {
    		$scoreModel = D('Score');
    		$searchArgs = $_POST;
    		$currentPage = abs($_POST['page']);
    		$currentPage = $currentPage >1 ? $currentPage : 1;
    		$pageSize = abs($_POST['rows']);
    		$pageSize = $pageSize > 0 ? $pageSize : 99999;
    		$sort = $_POST['sort'] ? $_POST['sort'] : 'total_score';
    		$order = $_POST['order'] ? $_POST['order'] : 'DESC';
    		if($addGroup) {
    			return $scoreModel->getSearchQuery($examId, $searchArgs);
    		} else {
    			$scoreCnt = $scoreModel->getExamScoreCount($examId, $searchArgs);
    			$scoreList = $scoreModel->getExamScoreList($examId, $searchArgs, $currentPage, $pageSize, $sort, $order);
	    		echo json_encode(array('total'=>$scoreCnt, 'rows'=>$scoreList));
	    		exit;
    		}
    	} else {
    		echo json_encode(array());
    	}
    }
    
    public function export() {
    	set_time_limit(0);
        $examId = abs($_POST['exam']);
        if($examId) {
        	$examModel = D('Exam');
        	$examInfo = $examModel->find($examId);
        	$scoreModel = D('Score');
    		$searchArgs = $_POST;
    		$currentPage = abs($_POST['page']);
    		$currentPage = $currentPage >1 ? $currentPage : 1;
    		$pageSize = abs($_POST['rows']);
    		$pageSize = $pageSize > 0 ? $pageSize : 99999;
    		$sort = $_POST['sort'] ? $_POST['sort'] : 'total_score';
    		$order = $_POST['order'] ? $_POST['order'] : 'DESC';
    		$scoreList = $scoreModel->getExamScoreList($examId, $searchArgs, $currentPage, $pageSize, $sort, $order);
    		$exportHtml = $scoreModel->getExportHtml($scoreList);
    		echo $exportHtml;
    		$fileName = $examInfo['group_caption'] . '-' . $examInfo['exam_caption'] . '-成绩导出表.xls';
    		SysUtil::sendFile($fileName,'application/octet-stream');
        } else {
        	die(1);
        }
    }
    
    public function virtual() {
    	$gTypeArray = C('EXAM_GROUP_TYPES');
    	$jsonExamUrl = $this->getUrl('jsonExamList', 'Exam');
        $jsonGroupUrl = $this->getUrl('jsonGroupList', 'Exam');
        $jsonVirtualUrl = $this->getUrl('jsonVirtual');
        $addVirtualUrl = $this->getUrl('addVirtual');
        $delVirtualUrl = $this->getUrl('delVirtual');
        $jsonExamInfo = $this->getUrl('jsonExamInfo');
        $saveVirtualUrl = $this->getUrl('saveVirtual');
        $this->assign(get_defined_vars());
		$this->display();
    }
    
    protected function jsonVirtual() {
    	$examId = abs($_GET['exam']);
    	$virtualModel = D('Virtual');
    	$currentPage = abs($_POST['page']);
    	if($currentPage <1) $currentPage = 1;
    	$pageSize = abs($_POST['rows']);
    	if($pageSize < 1) $pageSize = 999999;
    	$recordCount = $virtualModel->getVirtualCount($examId);
    	$virtualList = $virtualModel->getVirtualList($examId, $currentPage, $pageSize);
    	echo json_encode(array('total'=>$recordCount, 'rows'=>$virtualList));
    	exit;
    }
    
    protected function addVirtual() {
    	$examId = abs($_GET['exam']);
    	$virtualType = SysUtil::safeString($_GET['type']);
    	$vRankUrl = $this->getUrl('vRank');
    	$saveVirtualUrl = $this->getUrl('saveVirtual');
    	$virtualModel = D('Virtual');
    	$virtualTotal = $virtualModel->getVirtualTotal($examId, $virtualType);
    	if($_GET['id']) {
    		$virtualInfo = $virtualModel->find(SysUtil::uuid($_GET['id']));
    	}
    	$this->assign(get_defined_vars());
    	$this->display();
    }
    
    protected function delVirtual() {
    	$vid = SysUtil::uuid($_POST['id']);
    	$virtualModel = D('Virtual');
    	$virtualModel->delVirtual($vid);
    	echo json_encode(array('success'=>true));
    }
    
    
    protected function vRank() {
    	$examId = abs($_POST['exam_id']);
    	$virtualType = SysUtil::safeString($_POST['virtual_type']);
    	$rpcClient = SysUtil::getRpcClient('Exam');

    	$scoreData = array($virtualType=>abs($_POST['score']));
    	$rankData = $rpcClient->getScoreRanks($examId, $scoreData);
		$rankData = $rankData[$virtualType];
    	echo json_encode($rankData);
    	exit;
    }
    
    protected function saveVirtual() {
    	$virtualModel = D('Virtual');
    	$saveResult = $virtualModel->saveVirtual($_POST);
    	echo json_encode($saveResult);
    	exit;
    }
    
    protected function jsonAddScore() {
        $examId = abs($_GET['exam']);
        $subjectCode = SysUtil::safeString($_GET['subject']);
        $paperChar = strtoupper(SysUtil::safeString($_GET['pchar'])) == 'B' ? 'B' : 'A';
        $examModel = D('Exam');
        $subjectModel = D('Subject');
        $paperModel = D('Paper');
        $examInfo = $examModel->find($examId);
        $subjectInfo = $subjectModel->find($subjectCode);
        $paperArray = $paperModel->getScorePapers($examId,$subjectCode);
        $examStudentModel = D('ExamStudent');
        $posArray = $examStudentModel->getPositions($examId);
        $searchUrl = $this->getUrl('search');
        $stuScoreUrl = $this->getUrl('stuScore');
        $stuQuesUrl = $this->getUrl('stuQues');
        $saveScoreUrl = $this->getUrl('saveScore');
        $saveQuesUrl = $this->getUrl('saveQues');
        $tmpSignupUrl = $this->getUrl('tmpSignup');
        $jsonSubjectScoreUrl = $this->getUrl('jsonSubjectScore',MODULE_NAME, GROUP_NAME, array('exam'=>$examId, 'subject'=>$subjectCode));
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    protected function tmpSignup() {
    	$examId = abs($_POST['examId']);
    	$posCode = SysUtil::safeString($_POST['posCode']);
    	$stuCode = SysUtil::safeString($_POST['stuCode']);
    	$stuMobile = preg_replace('/\D/', '', $_POST['stuMobile']);
    	$esModel = D('ExamStudent');
    	$signupInfo = $esModel->signupTemp($examId, $stuCode, $posCode, $stuMobile);
    	echo json_encode($signupInfo);
    }
    
    protected function jsonSubjectScore() {
    	$examId = abs($_GET['exam']);
    	$subjectCode = $_GET['subject'];
    	$scoreModel = D('Score');
    	$posCode = $_POST['posCode'];
    	$currentPage = abs($_POST['page']);
    	$pageSize = abs($_POST['rows']);
    	$sort = $_POST['sort'] ? $_POST['sort'] : 'update_at';
    	$order = $_POST['order'] ? $_POST['order'] : 'DESC';
    	$count = $scoreModel->getSubjectScoreCount($examId, $subjectCode, $posCode);
    	$scoreList = $scoreModel->getSubjectScoreList($examId, $subjectCode, $posCode, $currentPage, $pageSize, $sort, $order);
    	echo json_encode(array('total'=>$count, 'rows'=>$scoreList));
    	exit;
    }
    
    protected function stuScore() {
    	$examId = abs($_POST['exam_id']);
    	$subjectCode = SysUtil::safeString($_POST['subject_code']);
    	$examCode = SysUtil::safeString($_POST['exam_code']);
    	$scoreModel = D('Score');
    	$stuScore = $scoreModel->getStuScore($examId,$subjectCode, $examCode);
		echo json_encode($stuScore);
		exit;
    }
    
    protected function stuQues() {
    	$paperId = abs($_POST['paper_id']);
    	$quesId = SysUtil::safeString($_POST['ques_id']);
    	$stuCode = SysUtil::safeString($_POST['stu_code']);
    	$scoreModel = D('Score');
    	$quesAnswer = $scoreModel->getStuQuesAnswer($paperId, $quesId, $stuCode);
    	echo json_encode($quesAnswer);
    	exit;
    }
    
    protected function search(){
    	$searchOptions = $_POST;
    	$examStudentModel = D('ExamStudent');
    	$stuList = $examStudentModel->searchStudents($searchOptions);
    	echo json_encode($stuList);
    	exit;
    }
    
    protected function saveQues() {
    	$scoreModel = D('Score');
    	if(false == $_POST['stu_code'] || false == $_POST['exam_id'] || false == $_POST['ques_id'] || false == $_POST['stu_answer']) {
    		echo json_encode(array('errorMsg'=>'试题信息不完整'));
    		exit;
    	}
    	$saveResult = $scoreModel->saveQues($_POST);
    	if(is_array($saveResult) && $saveResult['errorMsg']) {
    		echo json_encode($saveResult);
    		exit;
    	}
    	echo json_encode(array('success'=>true));
    }
    
    protected function saveScore() {
    	$scoreModel = D('Score');
    	$esModel = D('ExamStudent');
    	$step = abs($_POST['step']);
    	$examId = abs($_POST['exam_id']);
    	$examCode = SysUtil::safeString(strtoupper(trim($_POST['code_pre']) . trim($_POST['code_suffix'])));
    	$stuCode = SysUtil::safeString($_POST['stu_code']);
		
    	if(false == $stuCode || false == trim($_POST['code_pre']) || false == trim($_POST['code_suffix'])) {
    		echo json_encode(array('errorMsg'=>'考生信息不完整'));
    		exit;
    	} else {
	    	if(false == $_GET['absence']) {
	    		$scoreInfo = $_POST;
	    		$scoreInfo['exam_code'] = $examCode;
	    		$scoreInfo['stu_code'] = $stuCode;
	    		$saveResult = $scoreModel->saveScore($scoreInfo);
	    		if($saveResult['errorMsg']) {
	    			echo json_encode($saveResult);
	    			exit;
	    		}
	    	}
	    	if($_GET['absence']) {
	    		$subject = SysUtil::safeString($_POST['subject_code']);
	    		$scoreModel->delScore($examId, $stuCode, $subject);
	    	}
	    	$nextStuInfo = $esModel->findNextStudent($examId, $examCode, $scoreInfo['step']);
	    	if(false == $nextStuInfo) {
	    		echo json_encode(array('errorMsg'=>'当前考生成绩保存成功，没有查询到下一考生信息'));
	    		exit;
	    	}
	    	echo json_encode($nextStuInfo);
	    	exit;
    	}
    }
    
    protected function scanFiles() {
    	$examId = abs($_GET['exam']);
    	$examCode = SysUtil::safeString($_GET['examCode']);
    	$scanDir = C('SCAN_DIR');
    	$fileList = glob($scanDir . '/' . $examId . '/*/*'  . $examCode . '*');
    	$subjects = array('math'=>'数学', 'chinese'=>'语文', 'english'=>'英语', 'physic'=>'物理', 'chemistry'=>'化学');
    	$fileArray = array();
    	foreach ($fileList as $file) {
    		$file = str_replace(C('SCAN_DIR'), '/', $file);
    		$file = str_replace('//', '/', $file);
    		preg_match('/\/\d+\/([^\/]+)\/.+/', $file, $ar);
    		$subject = $ar[1];
    		$fileArray[$subject][] = '/Scan' . $file;
    	}
    	$this->assign(get_defined_vars());
    	$this->display();
    }
};
?>