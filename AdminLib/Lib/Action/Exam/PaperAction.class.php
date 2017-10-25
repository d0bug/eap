<?php
class PaperAction extends ExamCommAction {
    
    public function modules() {
        $permValue = $this->permValue;
        $jsonSubjectUrl = $this->getUrl('jsonSubject');
        $jsonModuleUrl = $this->getUrl('jsonModuleList');
        $addModuleUrl = $this->getUrl('addModule');
        $editModuleUrl = $this->getUrl('editModule');
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    public function knowledge() {
        $permValue = $this->permValue;
        $jsonModuleUrl = $this->getUrl('jsonModuleList');
        $jsonKnowledgeUrl = $this->getUrl('jsonKnowledgeList');
        $addKnowledgeUrl = $this->getUrl('addKnowledge');
        $knowledgeInfoUrl = $this->getUrl('knowledgeInfo');
        $delKnowledgeUrl = $this->getUrl('delKnowledge');
        $subjectModel = D('Subject');
        $subjectArray = $subjectModel->getSubjectArray();
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    public function lists() {
        $permValue = $this->permValue;
        $gTypeArray = C('EXAM_GROUP_TYPES');
        $jsonExamUrl = $this->getUrl('jsonExamList', 'Exam');
        $jsonPaperUrl = $this->getUrl('jsonPaperList');
        $jsonPQuesUrl = $this->getUrl('jsonPaperQuestion');
        $addPaperUrl = $this->getUrl('addPaper');
        $modifyUrl = $this->getUrl('modify');
        $delPaperUrl = $this->getUrl('delPaper');
        $paperModel = D('Paper');
        $paperTypes = $paperModel->getPaperTypes();
        $this->assign(get_defined_vars());
        $this->display('paperList');
    }
    
    public function questions() {
        $permValue = $this->permValue;
        $urlPerm = Permission::getPermInfo($this->loginUser, $this->getAclKey('setScoreUrl'));
        $urlPerm = $urlPerm['permValue'];
        $gTypeArray = C('EXAM_GROUP_TYPES');
        $jsonExamUrl = $this->getUrl('jsonExamList', 'Exam');
        $jsonSubjectUrl = $this->getUrl('jsonExamSubjects');
        $jsonQuesUrl = $this->getUrl('jsonQuesList');
        $addSubjectUrl = $this->getUrl('addSubject');
        $delSubjectUrl = $this->getUrl('delSubject');
        $addQuesUrl = $this->getUrl('addQuestion');
        $delQuesUrl = $this->getUrl('delQuestion');
        $quesInfoUrl = $this->getUrl('questionInfo');
        $setScoreUrl = $this->getUrl('setScoreUrl');
        $subjectModel = D('Subject');
        $quesModel = D('Question');
        $subjectArray = $subjectModel->getSubjectArray();
        $jsonSubjectArray = json_encode($subjectArray);
        $quesTypeArray = $quesModel->getQuesTypeArray();
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    protected function addSubject() {
        $subjectModel = D('Subject');
        $examId = abs($_POST['exam']);
        $subjectCode = SysUtil::safeString($_POST['subject']);
        $addResult = $subjectModel->saveExamSubject($examId, $subjectCode);
        if(is_array($addResult) && $addResult['errorMsg']) {
            $return = $addResult;
        }
        $return = array('success'=>true);
        echo json_encode($return);
        exit;
    }
    
    protected function delSubject() {
        $subjectModel = D('Subject');
        $examId = abs($_POST['exam']);
        $subjectCode = SysUtil::safeString($_POST['subject']);
        $delResult = $subjectModel->delExamSubject($examId, $subjectCode);
        if(is_array($delResult) && $delResult['errorMsg']) {
            $return = $delResult;
        }
        $return = array('success'=>true);
        echo json_encode($return);
        exit;
    }
    
    protected function jsonSubject() {
        $subjectModel = D('Subject');
        $subjectList = $subjectModel->getSubjectList();
        $return = array('total'=>sizeof($subjectList), 'rows'=>$subjectList);
        echo json_encode($return);
        exit;
    }
    
    protected function jsonExamSubjects() {
        $subjectModel = D('Subject');
        $examId = abs($_POST['exam']);
        $subjectList = $subjectModel->getExamSubjects($examId);
        $return = array('total'=>sizeof($subjectList), 'rows'=>$subjectList);
        echo json_encode($return);
        exit;
    }
    
    protected function setScoreUrl() {
    	$subjectModel = D('Subject');
    	if($this->isPost()) {
    		$saveResult = $subjectModel->setScoreUrl($_POST['examId'], $_POST['subjectCode'], $_POST['scoreUrl']);
    		echo json_encode($saveResult);
    		exit;
    	}
    	$examId = abs($_GET['exam']);
    	$subjectCode = SysUtil::safeString($_GET['subject']);
    	$subjectInfo = $subjectModel->examSubjectInfo($examId, $subjectCode);
    	$scoreUrl = $subjectInfo['score_url'];
    	$setScoreUrl = $this->getUrl('setScoreUrl');
    	$dialog = SysUtil::safeString($_GET['dlg']);
    	$this->assign(get_defined_vars());
    	$this->display();
    }
    
    protected function jsonModuleList() {
        $knowledgeModel = D('Knowledge');
        $subject = SysUtil::safeString($_POST['subject']);
        $keyword = SysUtil::safeSearch($_POST['keyword']);
        $condition = '1=1';
        $subjectModel = D('Subject');
        $subjectArray = $subjectModel->getSubjectArray();
        if($subject) {
            $condition .= ' AND module_subject=' . $knowledgeModel->dao->quote($subject);
        }
        if($keyword) {
            $condition .= ' AND module_caption LIKE ' . $knowledgeModel->dao->quote('%' . $keyword . '%');
        }
        $total = $knowledgeModel->countModule($condition);
        $moduleList = $knowledgeModel->getModuleList($condition, abs($_POST['page']), abs($_POST['rows']));
        foreach ($moduleList as $key=>$module) {
            $moduleList[$key]['subject_caption'] = $subjectArray[$module['module_subject']];
        }
        $return = array('total'=>$total, 'rows'=>$moduleList);
        echo json_encode($return);
        exit;
    }
    
    protected function jsonKnowledgeList() {
        $knowledgeModel = D('Knowledge');
        $dao = $knowledgeModel->dao;
        $condition = 'is_remove=0';
        if($_POST['subject']) {
            $condition .= ' AND module_subject=' . $dao->quote(SysUtil::safeString($_POST['subject']));
        }
        if($_POST['module']) {
            $condition .= ' AND knowledge.module_code=' . $dao->quote(SysUtil::safeString($_POST['module']));
        }
        if ($_POST['id']) {
            $condition .= ' AND parent_code=' . $dao->quote(SysUtil::safeString($_POST['id']));
        } else {
            if(false == $_POST['keyword']) {
                $condition .= ' AND parent_code=' . $dao->quote(0);
            } else  {
                $condition .= ' AND knowledge_caption LIKE ' . $dao->quote('%' . SysUtil::safeSearch($_POST['keyword']) . '%');
            }
        }
        
        $knowledgeList = $knowledgeModel->getList($condition);
        if(false == $_POST['keyword']) {
            foreach ($knowledgeList as $key=>$knowledge) {
            	if($knowledge['sub_cnt'] > 0) {
            	   $knowledgeList[$key]['state'] = 'closed';
            	} else {
            	   $knowledgeList[$key]['state'] = 'open';
            	}
            }
        }
        echo json_encode($knowledgeList);
        exit;
    }
    
    protected function jsonQuesList() {
        $quesModel = D('Question');
        $quesList = $quesModel->getQuesList($_POST);
        $return = array('rows'=>$quesList);
        echo json_encode($return);
    }
    
    protected function jsonPaperList() {
        $examId = abs($_POST['exam']);
        $paperModel = D('Paper');
        $paperList = $paperModel->getPaperList($examId);
        $return  = array('rows'=>$paperList);
        echo json_encode($return);
    }
    
    protected function jsonPaperQuestion() {
        $paperId = abs($_POST['pid']);
        $paperModel = D('Paper');
        $quesList = $paperModel->getPaperQuestion($paperId);
        $return  = array('rows'=>$quesList);
        echo json_encode($return);
        exit;
    }
    
    protected function addModule() {
        $subjectId = SysUtil::safeString($_GET['sbj']);
        $subjectModel = D('Subject');
        $subjectArray = $subjectModel->getSubjectArray();
        if($this->isPost()) {
            $resultScript = true;
            $knowledgeModel = D('Knowledge');
            $moduleInfo = $_POST;
            $saveResult = $knowledgeModel->saveModule($moduleInfo);
            if(is_array($saveResult) && $saveResult['errorMsg']) {
                $errorMsg = $saveResult['errorMsg'];
            }
        } else {
            $subjectCaption = $subjectArray[$subjectId];
            $url = $_SERVER['REQUEST_URI'];
        }
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    protected function editModule() {
        $moduleId = SysUtil::uuid($_GET['module']);
        $knowledgeModel = D('Knowledge');
        if($this->isPost()) {
            $resultScript = true;
            $moduleInfo = $_POST;
            $saveResult = $knowledgeModel->saveModule($moduleInfo);
            if(is_array($saveResult) && $saveResult['errorMsg']) {
                $errorMsg = $saveResult['errorMsg'];
            }
        } else {
            $moduleInfo = $knowledgeModel->findModule($moduleId);
            $url = $_SERVER['REQUEST_URI'];
        }
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    protected function addKnowledge() {
        $knowledgeModel = D('Knowledge');
        if($this->isPost()) {
            $resultScript = true;
            $knowledgeInfo = $_POST;
            $saveResult = $knowledgeModel->save($knowledgeInfo);
            if(is_array($saveResult) && $saveResult['errorMsg']) {
                $errorMsg = $saveResult['errorMsg'];
            }
            $knowledgeCode = SysUtil::safeString($knowledgeInfo['parent_code']);
        } else {
            $module = SysUtil::safeString($_GET['module']);
            $moduleInfo = $knowledgeModel->findModule($module,true);
        }
        $url = $_SERVER['REQUEST_URI'];
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    protected function knowledgeInfo() {
        $knowledgeModel = D('Knowledge');
        $knowledgeCode = SysUtil::safeString($_GET['id']);
        if($this->isPost()) {
            $resultScript = true;
            $knowledgeInfo = $_POST;
            $saveResult = $knowledgeModel->save($knowledgeInfo);
            if(is_array($saveResult) && $saveResult['errorMsg']) {
                $errorMsg = $saveResult['errorMsg'];
            }
            $knowledgeCode = SysUtil::safeString($knowledgeInfo['parent_code']);
        }
        $url = $_SERVER['REQUEST_URI'];
        $knowledgeInfo = $knowledgeModel->find($knowledgeCode);
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    
    protected function delKnowledge() {
        $knowledgeId = SysUtil::uuid($_POST['id']);
        $knowledgeModel = D('Knowledge');
        $knowledgeModel->delete($knowledgeId);
        echo 1;
    }
    
    protected function addQuestion() {
        $this->writeCheck($this->getAclKey('questions'));
        $examId = abs($_GET['exam']);
        $dlgId = SysUtil::safeString($_GET['dlg']);
        $subjectCode = SysUtil::safeString($_GET['subject']);
        $quesType = SysUtil::safeString($_GET['quesType']);
        $quesModel = D('Question');
        if($this->isPost()) {
            $quesInfo = $_POST;
            $saveResult = $quesModel->save($quesInfo);
            echo json_encode($saveResult);
            exit;
        }
        $knowledgeModel = D('Knowledge');
        $knowledgeCnt = $knowledgeModel->countKnowledge('module_subject=' . $knowledgeModel->dao->quote($subjectCode));
        if(0 == $knowledgeCnt) {
            echo '<script type="text/javascript">
                alert("该学科不存在知识点数据，请在添加知识点后添加试题");
                parent.closeDlg("' . $dlgId . '");
            </script>';
        }
        $examModel = D('Exam');
        $subjectModel = D('Subject');
        
        $examInfo = $examModel->find($examId);
        $subjectInfo = $subjectModel->find($subjectCode);
        $quesTypeArray = $quesModel->getQuesTypeArray();
        $quesBodyArray = $quesModel->getQuesBodyArray($examId, $subjectCode);
        $url = $_SERVER['REQUEST_URI'];
        $bodyInfoUrl = $this->getUrl('bodyInfo');
        $this->assign(get_defined_vars());
        $this->display('addQuestion_' . $quesType);
    }
    
    protected function delQuestion() {
    	$this->writeCheck($this->getAclKey('questions'));
    	$quesId = SysUtil::uuid($_POST['quesId']);
    	$quesModel = D('Question');
    	$delResult = $quesModel->delQuestion($quesId);
    	echo json_encode($delResult);
    	exit;
    }
    
    protected function bodyInfo() {
        $quesModel = D('Question');
        if($this->isPost()) {
            $bodyInfo = $_POST;
            $bodyId = SysUtil::safeString($_POST['body_id']);
            if(strlen($bodyId) <= 1) {
                unset($bodyInfo['body_id']);
            }
            $saveResult = $quesModel->saveBody($bodyInfo);
            if(is_array($saveResult) && $saveResult['errorMsg']) {
                echo json_encode($saveResult);
                exit;
            } else {
                $bodyArray = $quesModel->getQuesBodyArray($bodyInfo['exam_id'], $bodyInfo['subject_code']);
                echo json_encode($bodyArray);
                exit;
            }
        } else {
            $bodyId = SysUtil::safeString($_GET['bid']);
            $bodyInfo = $quesModel->findBody($bodyId);
            echo json_encode($bodyInfo);
            exit;
        }
    }
    
    protected function questionInfo() {
        $this->readCheck($this->getAclKey('questions'));
        $permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey('questions'));
        $permValue = $permInfo['permValue'];
        $quesModel = D('Question');
        $examModel = D('Exam');
        $subjectModel = D('Subject');
        if($this->isPost()) {
            $this->writeCheck($this->getAclKey('questions'));
            $quesInfo = $_POST;
            $saveResult = $quesModel->save($quesInfo);
            echo json_encode($saveResult);
            exit;
        }
        $dlgId = SysUtil::safeString($_GET['dlg']);
        $quesTypeArray = $quesModel->getQuesTypeArray();
        $quesId = SysUtil::uuid($_GET['ques']);
        $quesInfo = $quesModel->find($quesId);
        $subjectCode = $quesInfo['subject_code'];
        $quesType = $quesInfo['ques_type'];
        $examInfo = $examModel->find($quesInfo['exam_id']);
        $subjectInfo = $subjectModel->find($quesInfo['subject_code']);
        $quesBodyArray = $quesModel->getQuesBodyArray($quesInfo['exam_id'], $quesInfo['subject_code']);

        $chars = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $url = $_SERVER['REQUEST_URI'];
        $bodyInfoUrl = $this->getUrl('bodyInfo');
        $this->assign(get_defined_vars());
        $this->display('quesInfo_' . $quesType);
    }
    
    protected function addPaper(){
        $this->writeCheck($this->getAclKey('lists'));
        $examId = abs($_GET['exam']);
        $dlgId  = abs($_GET['dlg']);
        $paperType = SysUtil::safeString($_GET['type']);
        $examModel = D('Exam');
        $paperModel = D('Paper');
        if($this->isPost()) {
            $paperInfo = $_POST;
            $saveResult = $paperModel->save($paperInfo);
            echo json_encode($saveResult);
            exit;
        }
        
        $examInfo = $examModel->find($examId);
        $subjectModel = D('Subject');
        $subjectArray = $subjectModel->getExamSubjects($examId);
        $sbjArray = array();
        foreach ($subjectArray as $subject) {
            $sbjArray[$subject['subject_code']] = $subject['subject_name'];
        }
        $paperTypes = $paperModel->getPaperTypes();
        $typeCaption = $paperTypes[$paperType];
        $partQuesUrl = $this->getUrl('partQues');
        $paperCheckUrl = $this->getUrl('checkPaper');
        $seleQuesUrl = $this->getUrl('seleQues');
        $setQuesUrl = $this->getUrl('setPartQuestion');
        $savePaperUrl = $_SERVER['REQUEST_URI'];
        
        $this->assign(get_defined_vars());
        $this->display('addPaper_' . $paperType);
    }
    
    protected function checkPaper() {
        $this->writeCheck($this->getAclKey('lists'));
        $paperModel = D('Paper');
        $examId = abs($_POST['exam']);
        $subject = SysUtil::safeString($_POST['subject']);
        $paperType = SysUtil::safeString($_POST['type']);
        $paperChar = SysUtil::safeString($_POST['pchar']);
        $checkResult = $paperModel->ifEnableAdd(array('exam_id'=>$examId, 'subject_code'=>$subject, 'paper_type'=>$paperType, 'paper_char'=>$paperChar));
        echo json_encode($checkResult);
        exit;
    }
    
    protected function partQues() {
        $this->writeCheck($this->getAclKey('lists'));
        $paperId = abs($_GET['paper']);
        $partIdx = abs($_GET['part']);
        $examId = abs($_GET['exam']);
        $paperType = SysUtil::safeString($_GET['type']);
        $subjectCode = SysUtil::safeString($_GET['subject']);
        $paperChar = SysUtil::safeString($_GET['pchar']);
        $paperChar = $paperChar[0];
        $paperModel = D('Paper');
        if(false == $paperId && $paperType == 'real' && $paperChar == 'B') {
            $paperA = $paperModel->findPaperA($examId, $subjectCode, $paperChar);
            if($paperA) {
                $paperId = $paperA['paper_id'];
            }
        }
        if($paperId) {
            $jsonQuesUrl = $this->getUrl('jsonPartQuesList', MODULE_NAME, GROUP_NAME, array('paper'=>$paperId, 'pid'=>$partIdx));
        }
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    protected function jsonPartQuesList() {
        $paperId = abs($_GET['paper']);
        $partIdx = abs($_GET['pid']);
        $searchArgs = array('paperId'=>$paperId, 'partIdx'=>$partIdx);
        $paperModel = D('Paper');
        $quesList = $paperModel->getQuesList($searchArgs);
        $return = array('rows'=>$quesList);
        echo json_encode($return);
        exit;
    }
    
    protected function seleQues() {
        $this->writeCheck($this->getAclKey('lists'));
        if($this->isPost()) {
            $quesModel = D('Question');
            $examId = abs($_POST['exam']);
            $subjectCode = SysUtil::safeString($_POST['subject']);
            $excludes = SysUtil::safeString($_POST['questions']);
            $paperModel = D('Paper');
            $quesIdArray = $paperModel->getSubjectQuestions($examId, $subjectCode);
            if($quesIdArray) {
                if ($excludes) $excludes .= ',';
                $excludes .= implode(',', $quesIdArray);
            }
            $quesList = $quesModel->getQuesList(array('examId'=>$examId, 'subject'=>$subjectCode), $excludes);
            $return  = array('rows'=>$quesList);
            echo json_encode($return);
            exit;
        } else {
            $partIdx = abs($_GET['part']);
            $dlgId = abs($_GET['dlg']);
            $this->assign(get_defined_vars());
            $this->display();
        }
    }
    
    protected function setPartQuestion() {
        $this->writeCheck($this->getAclKey('lists'));
        if($this->isPost()) {
            $paperModel = D('Paper');
            $searchArgs = array();
            $searchArgs['quesIds'] = SysUtil::safeString($_POST['quesIds']);
            $quesList = $paperModel->getQuesList($searchArgs);
            $return = array('rows'=>$quesList);
            echo json_encode($return);
            exit;
        } else {
            die('error');
        }
    }
    
    protected function modify() {
        $paperModel = D('Paper');
        $formData = $_POST;
        $modifyType = SysUtil::safeString($_GET['type']);
        if(false == preg_match('/caption/i', $modifyType)) {
            $modifyResult = $paperModel->modify($modifyType, $formData);
        } else {
            $modifyResult = $paperModel->modifyCaption($modifyType, $formData);
        }
        echo json_encode($modifyResult);
        exit;
    }
    
    public function delPaper() {
        $examId = abs($_POST['exam']);
        $paperId = abs($_POST['paper']);
        $paperType = SysUtil::safeString($_POST['type']);
        $paperModel = D('Paper');
        $delResult = $paperModel->delete($examId, $paperId, $paperType);
        echo json_encode($delResult);
        exit;
    }
    
};
?>