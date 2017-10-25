<?php
class AnalyAction extends ExamCommAction {	
	public function step() {
		$permValue = $this->permValue;
        $gTypeArray = C('EXAM_GROUP_TYPES');
        $jsonExamUrl = $this->getUrl('jsonExamList', 'Exam');
        $jsonSubjectUrl = $this->getUrl('jsonExamSubjects', 'Paper');
        $jsonStepAnalysUrl = $this->getUrl('jsonStepAnalys');
        $stepStatisticsUrl = $this->getUrl('stepStatistics');
        $addAnalyUrl = $this->getUrl('addStepAnaly');
        $editAnalyUrl = $this->getUrl('editStepAnaly');
        $delAnalyUrl = $this->getUrl('delStepAnaly');
        $saveStepCfgUrl = $this->getUrl('saveStepCfg');
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function jsonStepAnalys() {
		$quesStepModel = D('StepAnaly');
		$examId = abs($_POST['examId']);
		$subjectCode = SysUtil::safeString($_POST['subjectCode']);
		$analyList = $quesStepModel->getAnalys($examId, $subjectCode);
		echo json_encode(array('rows'=>$analyList));
	}
	
	protected function addStepAnaly() {
		$quesStepModel = D('StepAnaly');
		$examId = abs($_GET['exam']);
		$subjectCode = SysUtil::safeString($_GET['subject']);
		$analyList = $quesStepModel->getAnalys($examId, $subjectCode);
		$analyLevels = $quesStepModel->getAnalyLevels();
		foreach ($analyList as $analy) {
			unset($analyLevels[$analy['analy_level']]);
		}
		if($this->isPost()) {
			$saveResult = $quesStepModel->saveAnaly($_POST);
			echo  json_encode($saveResult);
			exit;
		}
		$addAnalyUrl = $this->getUrl('addStepAnaly');
		$dialog = SysUtil::safeString($_GET['dlg']);
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function editStepAnaly() {
		$quesStepModel = D('StepAnaly');
		if($this->isPost()) {
			$saveResult = $quesStepModel->saveAnaly($_POST);
			echo  json_encode($saveResult);
			exit;
		}
		$analyId = SysUtil::uuid($_GET['id']);
		$analyInfo = $quesStepModel->findAnaly($analyId);
		$analyList = $quesStepModel->getAnalys($analyInfo['exam_id'], $analyInfo['subject_code']);
		$analyLevels = $quesStepModel->getAnalyLevels();
		foreach ($analyList as $analy) {
			if($analy['analy_level'] != $analyInfo['analy_level']) {
				unset($analyLevels[$analy['analy_level']]);
			}
		}
		$editAnalyUrl = $this->getUrl('editStepAnaly');
		$dialog = SysUtil::safeString($_GET['dlg']);
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function delStepAnaly() {
		$quesStepModel = D('StepAnaly');
		$analyId = SysUtil::uuid($_POST['id']);
		if($analyId) {
			$delResult = $quesStepModel->delAnaly($analyId);
		}
		echo json_encode(array('success'=>true));
	}
	
	protected function stepStatistics() {
		$stepModel = D('StepAnaly');
		$examId = abs($_GET['exam']);
		$subjectCode = SysUtil::safeString($_GET['subject']);
		$stepCfg = $stepModel->findStepCfg($examId, $subjectCode);
		$stepStatistics = $stepCfg['step_statistics'];
		$jsonStepStatistics = SysUtil::jsonEncode($stepStatistics);
		$stepScores = $stepCfg['step_score'];
		
		$saveStepCfgUrl = $this->getUrl('saveStepCfg');
		$dialog = SysUtil::safeString($_GET['dlg']);
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function saveStepCfg() {
		$stepModel = D('StepAnaly');
		$saveResult = $stepModel->saveStepCfg($_POST);
		echo json_encode($saveResult);
		exit;
	}
	
	public function module () {
		$permValue = $this->permValue;
        $gTypeArray = C('EXAM_GROUP_TYPES');
        $jsonExamUrl = $this->getUrl('jsonExamList', 'Exam');
        $jsonSubjectUrl = $this->getUrl('jsonExamSubjects', 'Paper');
        $jsonModuleUrl = $this->getUrl('jsonModuleList');
        $moduleAnalyUrl = $this->getUrl('moduleAnalys');
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	public function knowledge() {
		$permValue = $this->permValue;
        $gTypeArray = C('EXAM_GROUP_TYPES');
        $jsonExamUrl = $this->getUrl('jsonExamList', 'Exam');
        $jsonSubjectUrl = $this->getUrl('jsonExamSubjects', 'Paper');
        $jsonKnowledgeUrl = $this->getUrl('jsonKnowledgeList');
        $knowledgeAnalyUrl = $this->getUrl('knowledgeAnalys');
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	public function level() {
		$permValue = $this->permValue;
        $gTypeArray = C('EXAM_GROUP_TYPES');
        $jsonExamUrl = $this->getUrl('jsonExamList', 'Exam');
        $jsonSubjectUrl = $this->getUrl('jsonExamSubjects', 'Paper');
        $jsonLevelUrl = $this->getUrl('jsonLevelList');
        $levelAnalyUrl = $this->getUrl('levelAnalys');
        
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	public function score() {
		$permValue = $this->permValue;
        $examTypeArray = C('EXAM_GROUP_TYPES');
        $jsonGroupUrl = $this->getUrl('jsonGroupList', 'Exam');
        $jsonExamUrl = $this->getUrl('jsonExamList', 'Exam');
        $jsonAnalyUrl = $this->getUrl('jsonAnaly');
        $addScoreAnalyUrl = $this->getUrl('addScoreAnaly');
        $editScoreAnalyUrl = $this->getUrl('editScoreAnaly');
        $delScoreAnalyUrl = $this->getUrl('delScoreAnaly');
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function jsonAnaly() {
		$examId = abs($_GET['exam']);
		$analyModel = D('ScoreAnaly');
		$analyList = $analyModel->getScoreAnalys($examId);
		echo json_encode(array('rows'=>$analyList));
		exit;
	}
	
	protected function scoreRank() {
		$rankModel = D('Rank');
		$examId = abs($_POST['exam']);
		$scoreData = array($_POST['analy_type']=>abs($_POST['analy_score']));
		$rankData = $rankModel->getScoreRanks($examId, $scoreData);
		echo json_encode($rankData[$_POST['analy_type']]);
		exit;
	}
	
	protected function addScoreAnaly() {
		if($this->isPost()) {
			$analyModel = D('ScoreAnaly');
			$addResult = $analyModel->saveScoreAnaly($_POST);
			echo json_encode($addResult);
			exit;
		}
		$examId = abs($_GET['exam']);
		$addScoreAnalyUrl = $this->getUrl('addScoreAnaly');
		$scoreRankUrl = $this->getUrl('scoreRank');
		$paperModel = D('Paper');
		$paperCaptions = $paperModel->getPaperCaptions($examId);
		$paperCaptions = array_merge(array('total'=>array('caption'=>'综合成绩')), $paperCaptions);
		$dlg = $_GET['dlg'];
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function editScoreAnaly() {
		$analyModel = D('ScoreAnaly');
		if ($this->isPost()) {
			$editResult = $analyModel->saveScoreAnaly($_POST);
			echo json_encode($editResult);
			exit;
		}
		$analyId = SysUtil::uuid($_GET['aid']);
		$analyInfo = $analyModel->findScoreAnaly($analyId);
		$examId = $analyInfo['exam_id'];
		$editScoreAnalyUrl = $this->getUrl('editScoreAnaly');
		$scoreRankUrl = $this->getUrl('scoreRank');
		$paperModel = D('Paper');
		$paperModel = D('Paper');
		$paperCaptions = $paperModel->getPaperCaptions($examId);
		$paperCaptions = array_merge(array('total'=>array('caption'=>'综合成绩')), $paperCaptions);
		$dlg = $_GET['dlg'];
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function delScoreAnaly() {
		if($_POST['aid']) {
			$analyId = SysUtil::uuid($_POST['aid']);
			$analyModel = D('ScoreAnaly');
			$delResult = $analyModel->delScoreAnaly($analyId);
			echo json_encode($delResult);
			exit;
		}
	}
	
	protected function moduleAnalys() {
		$analyModel = D('ModuleAnaly');
		if($this->isPost()) {
			$saveResult = $analyModel->save($_POST);
			echo json_encode($saveResult);
			exit;
		}
		$examId = abs($_GET['exam']);
		$subjectCode = SysUtil::safeString($_GET['subject']);
		$moduleCode = SysUtil::safeString($_GET['mdl']);
		$moduleAnalyUrl = $this->getUrl('moduleAnalys');
		$delAnalyUrl = $this->getUrl('delModuleAnaly');
		$analyList = $analyModel->getAnalyList($examId, $subjectCode, $moduleCode);
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function jsonModuleList() {
		$examId = abs($_POST['examId']);
		$subjectCode = SysUtil::safeString($_POST['subject']);
		$analyModel = D('ModuleAnaly');
		$moduleStatistics = $analyModel->getModuleStatistics($examId, $subjectCode);
		echo json_encode(array('rows'=>array_values($moduleStatistics)));
		exit;
	}
	
	protected function delModuleAnaly() {
		$analyId = SysUtil::uuid($_POST['analyId']);
		$analyModel = D('ModuleAnaly');
		$delResult = $analyModel->delAnaly($analyId);
		echo json_encode($delResult);
		exit;
	}
	
	protected function jsonKnowledgeList() {
		$examId = abs($_POST['examId']);
		$subjectCode = SysUtil::safeString($_POST['subject']);
		$analyModel = D('KnowledgeAnaly');
		$knowledgeStatistics = $analyModel->getKnowledgeStatistics($examId, $subjectCode);
		echo json_encode(array('rows'=>array_values($knowledgeStatistics)));
		exit;
	}
	
	protected function knowledgeAnalys() {
		$analyModel = D('KnowledgeAnaly');
		if($this->isPost()) {
			$saveResult = $analyModel->saveAnaly($_POST);
			echo json_encode($saveResult);
			exit;
		}
		$examId = abs($_GET['exam']);
		$subjectCode = SysUtil::safeString($_GET['subject']);
		$knowledgeCode = SysUtil::safeString($_GET['knowledge']);
		$knowledgeAnalyUrl = $this->getUrl('knowledgeAnalys');
		$delAnalyUrl = $this->getUrl('delKnowledgeAnaly');
		$analyList = $analyModel->getAnalyList($examId, $subjectCode, $knowledgeCode);
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function delKnowledgeAnaly() {
		$analyModel = D('KnowledgeAnaly');
		$delResult = $analyModel->delAnaly($_GET['id']);
		echo json_encode($delResult);
		exit;
	}
	
	public function partStatistics() {
		$permValue = $this->permValue;
        $gTypeArray = C('EXAM_GROUP_TYPES');
        $jsonExamUrl = $this->getUrl('jsonExamList', 'Exam');
        $jsonSubjectUrl = $this->getUrl('jsonExamSubjects', 'Paper');
        $jsonPartUrl = $this->getUrl('jsonPartList');
        $partAnalyUrl = $this->getUrl('partAnalys');
        
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function jsonPartList() {
		$analyModel = D('PartAnaly');
		$examId = abs($_POST['examId']);
		$subjectCode = SysUtil::safeString($_POST['subject']);
		$partStatistics = $analyModel->getPartStatistics($examId, $subjectCode);
		echo json_encode(array('rows'=>array_values($partStatistics)));
		exit;
	}
	
	protected function partAnalys() {
		$analyModel = D('PartAnaly');
		if($this->isPost()) {
			$saveResult = $analyModel->saveAnaly($_POST);
			echo json_encode($saveResult);
			exit;
		}
		$examId = abs($_GET['exam']);
		$subjectCode = SysUtil::safeString($_GET['subject']);
		$partNum = abs($_GET['part']);
		$partAnalyUrl = $this->getUrl('partAnalys');
		$delAnalyUrl = $this->getUrl('delPartAnaly');
		$analyList = $analyModel->getAnalyList($examId, $subjectCode, $partNum);
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function delPartAnaly() {
		$analyModel = D('PartAnaly');
		$analyId = SysUtil::uuid($_POST['analyId']);
		$delResult = $analyModel->delAnaly($analyId);
		echo json_encode($delResult);
		exit;
	}
	
	protected function jsonLevelList() {
		$analyModel = D('LevelAnaly');
		$examId = abs($_POST['examId']);
		$subjectCode = SysUtil::safeString($_POST['subject']);
		$levelStatistics = $analyModel->getLevelStatistics($examId, $subjectCode);
		echo json_encode($levelStatistics);
		exit;
	}
	
	protected function levelAnalys() {
		$analyModel = D('LevelAnaly');
		if($this->isPost()) {
			$saveResult = $analyModel->saveAnaly($_POST);
			echo json_encode($saveResult);
			exit;
		}
		$examId = abs($_GET['exam']);
		$subjectCode = SysUtil::safeString($_GET['subject']);
		$levelNum = abs($_GET['level']);
		$analyList = $analyModel->getAnalyList($examId, $subjectCode, $levelNum);
		$levelAnalyUrl = $this->getUrl('levelAnalys');
		$delAnalyUrl = $this->getUrl('delLevelAnaly');
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function delLevelAnaly() {
		$analyModel = D('LevelAnaly');
		$analyId = SysUtil::uuid($_POST['analyId']);
		$delResult = $analyModel->delAnaly($analyId);
		echo  json_encode($delResult);
		exit;
	}
	
	public function quesRatios() {
		$permValue = $this->permValue;
        $gTypeArray = C('EXAM_GROUP_TYPES');
        $jsonExamUrl = $this->getUrl('jsonExamList', 'Exam');
        $jsonSubjectUrl = $this->getUrl('jsonExamSubjects', 'Paper');
		$jsonQuesUrl = $this->getUrl('jsonQuesList');
		
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function jsonQuesList() {
		$scoreModel = D('Score');
		$examId = abs($_POST['examId']);
		$subjectCode = SysUtil::safeString($_POST['subject']);
		$quesRatios = $scoreModel->getQuesRatios($examId, $subjectCode);
		$quesRatios = array_values($quesRatios);
		echo json_encode(array('rows'=>$quesRatios));
	}
}
?>