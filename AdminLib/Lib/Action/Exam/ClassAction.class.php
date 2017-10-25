<?php
class ClassAction extends ExamCommAction {
	public function main() {
		$gTypeArray = C('EXAM_GROUP_TYPES');
        $jsonExamUrl = $this->getUrl('jsonExamList', 'Exam');
        $jsonSubjectUrl = $this->getUrl('jsonExamSubjects', 'Paper');
        $addRuleUrl = $this->getUrl('addRule');
        $editRuleUrl = $this->getUrl('editRule');
        $delRuleUrl = $this->getUrl('delRule');
        $jsonRuleUrl = $this->getUrl('jsonRules');
        $permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey('main'));
        $permValue = $permInfo['permValue'];
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function jsonRules() {
		$examId = abs($_GET['exam']);
		$subjectCode = SysUtil::safeString($_GET['subject']);
		$ruleModel = D('ClassRule');
		$currentPage = abs($_POST['page']);
		$pageSize = abs($_POST['rows']);
		$recordCount = $ruleModel->getRuleCount($examId, $subjectCode);
		$ruleList = $ruleModel->getRuleList($examId, $subjectCode, $currentPage, $pageSize);
		echo json_encode(array('total'=>$recordCount, 'rows'=>$ruleList));
	}
	
	protected function addRule() {
		$defaultSemester = 'BJ';
		$year = date('y');
		$month = date('n');
		if($month <=3) {
			$semester = 'C';
		} else if($month <=6) {
			$semester = 'S';
		} else if ($month <= 10){
			$semester = 'Q';
		} else {
			$year += 1;
			$semester = 'H';
		}
		$defaultSemester .= $year . $semester;
		
		$stuGroupModel = D('StuGroup');
		$groupList = $stuGroupModel->getGroupList(1, 100);
		$examId = abs($_GET['exam']);
		$subjectCode = SysUtil::safeString($_GET['subject']);
		$xuebukeArray = C('SUBJECT_XUEBUKE');
		$xuebuke = $xuebukeArray[$subjectCode];
		$jsonClsUrl = $this->getUrl('jsonClsList');
		$saveRuleUrl = $this->getUrl('saveRule');
		$dlg = SysUtil::safeString($_GET['dlg']);
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function editRule() {
		$ruleId = SysUtil::uuid($_GET['id']);
		$ruleModel = D('ClassRule');
		$stuGroupModel = D('StuGroup');
		$groupList = $stuGroupModel->getGroupList(1, 100);
		$ruleInfo = $ruleModel->findRule($ruleId);
		$subjectCode = $ruleInfo['subject_code'];
		$xuebukeArray = C('SUBJECT_XUEBUKE');
		$xuebuke = $xuebukeArray[$subjectCode];
		$jsonClsUrl = $this->getUrl('jsonClsList');
		$saveRuleUrl = $this->getUrl('saveRule');
		$dlg = SysUtil::safeString($_GET['dlg']);
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function saveRule() {
		$ruleData = $_POST;
		$ruleModel = D('ClassRule');
		$saveResult = $ruleModel->saveRule($ruleData);
		echo json_encode($saveResult);
		exit;
	}
	
	protected function delRule() {
		$id = SysUtil::uuid($_POST['id']);
		$ruleModel = D('ClassRule');
		$delResult = $ruleModel->deleteRule($id);
		echo json_encode($delResult);
		exit;
	}
	
	protected function jsonClsList() {
		$codePre = SysUtil::safeString($_POST['semester']);
		$clsLevel = abs($_POST['clsLevel']);
		$xuebuke = abs($_POST['xuebuke']);
		import('COM.Gaosi.GClass');
		$clsList = GClass::getUniqClassByLevel($clsLevel, $codePre, $xuebuke);
		echo json_encode($clsList);
		exit;
	}
	
	protected function stuCls() {
		
	}
}
?>