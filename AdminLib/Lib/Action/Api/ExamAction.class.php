<?php
class ExamAction extends ApiCommAction {
	protected function getScoreUrl($paperId, $scoreKey) {
		$subjectModel = D('Subject');
		return $subjectModel->getScoreUrl($paperId, $scoreKey);
	}
	
	protected function getExamInfo($examId) {
		import('ORG.Util.NCache');
		$cache = NCache::getCache();
		$examInfo = $cache->get('ExamInfo', $examId);
		if(false == $examInfo) {
			$rankModel = D('Rank');
			$examInfo = $rankModel->getExamInfo($examId);
			$cache->set('ExamInfo', $examId, $examInfo);
		}
		return $examInfo;
	}
	
	protected function getStuPaper($examId, $stuCode, $subject) {
		$paperModel = D('Paper');
		return $paperModel->getStuPaper($examId, $stuCode, $subject);
	}
	
	#成绩排名
	protected function getScoreRanks($examId, $scoreData) {
		$rankModel = D('Rank');
		return $rankModel->getScoreRanks($examId, $scoreData);
	}
	
	#成绩数据
	protected function getScoreData($examId, $stuCode) {
		import('ORG.Util.NCache');
		$cache = NCache::getCache();
		$key = md5($examId . '_' . $stuCode);
		$scoreData = $cache->get('ScoreData', $key);
		if(null === $scoreData) {
			$scoreModel = D('Score');
			$scoreData = $scoreModel->getScoreData($examId, $stuCode);
			$cache->set('ScoreData', $key, $scoreData);
		}
		return $scoreData;
	}
	
	#成绩评语
	protected function getScoreAnalys($examId, $scoreData) {
		$analyModel = D('ScoreAnaly');
		return $analyModel->getScoreAnalyData($examId, $scoreData);
	}
	
	protected function getScoreAwards($examId, $scoreData) {
		$awardModel = D('Award');
		$awardData = $awardModel->getAwardData($examId, $scoreData);
		return $awardData;
	}
	
	protected function getScoreClass($examId, $scoreData, $stuCode) {
		import('ORG.Util.NCache');
		$cache = NCache::getCache();
		$key = md5($examId . serialize($scoreData) . '_' . $stuCode);
		$scoreClasses = $cache->get('StuScoreClass', $key);
		if(null === $scoreClasses) {
			$ruleModel = D('ClassRule');
			$scoreClasses = array();
			foreach ($scoreData as $scoreType=>$score) {
				if(strstr($scoreType, '_real')) {
					$scoreClasses[$scoreType] = $ruleModel->getStuClass($examId, $stuCode, $scoreType);
				}
			}
			$cache->set('StuScoreClass', $key, $scoreClasses);
		}
		return $scoreClasses;
	}
	
	protected function getExamScoreInfo($examId, $scoreData, $stuCode) {
		import('ORG.Util.NCache');
		$key = md5($examId . '_' . serialize($scoreData));
		$cache = NCache::getCache();
		$result = $cache->get('examScore', $key);
		if(null === $result) {
			$result = array($this->getScoreRanks($examId, $scoreData),
						 	$this->getScoreAwards($examId, $scoreData),
						 	$this->getScoreAnalys($examId, $scoreData));
			$cache->set('examScore', $key, $result);
		}
		$result[] = $this->getScoreClass($examId, $scoreData, $stuCode);
		return $result;
	}
	
	protected function getPaperScoreInfo($paperId, $stuCode) {
		$paperModel = D('Paper');
		$examModel = D('Exam');
		$scoreModel = D('Score');
		$esModel = D('ExamStudent');
		$paperInfo = $paperModel->find($paperId);
		$examInfo = $examModel->find($paperInfo['exam_id']);
		$scoreData = $scoreModel->getScoreData($examInfo['exam_id'], $stuCode, $paperInfo['subject_code']);
		return $scoreData;
		
	}
	
	protected function getPaperAnalys($paperId, $stuCode) {
		import('ORG.Util.NCache');
		$key = md5($paperId . '_' . $stuCode);
		$cache = NCache::getCache();
		$analyArray = $cache->get('PaperAnalys', $key);
		if($analyArray) return $analyArray;
		
		$paperModel = D('Paper');
		$scoreModel = D('Score');
		$scoreAnalyModel = D('ScoreAnaly');
		$partAnalyModel = D('PartAnaly');
		$moduleAnalyModel = D('ModuleAnaly');
		$knowledgeAnalyModel = D('KnowledgeAnaly');
		$stepAnalyModel = D('StepAnaly');
		$levelAnalyModel = D('LevelAnaly');
		$paperId = abs($paperId);
		$paperInfo = $paperModel->find($paperId);
		$examId = abs($paperInfo['exam_id']);
		$analyArray = array();
		$analyArray['paperInfo'] = $paperInfo;
		$quesArray = $cache->get('paperQuestions', 'paperQuestions_' . $paperId);
		if(false == $quesArray) {
			$quesArray = $paperModel->getPaperQuestions($paperInfo);
			$cache->set('paperQuestions', 'paperQuestions_' . $paperId, $quesArray);
		}
		$analyArray['quesArray'] = $quesArray;
		$stuScoreArray = $scoreModel->getScoreInfo($paperInfo, $stuCode);
		$quesIdArray = array();
		$scoreData = array();
		foreach ($stuScoreArray as $paperId=>$paperCfg) {
			foreach ($paperCfg['ques_ratios'] as $ques) {
				$quesIdArray[$ques['ques_id']] = $ques['ques_id'];
			}
			$scoreData[$paperCfg['subject'] . '_' . $paperCfg['paper_type']] = $paperCfg['score_info']['paper_total_score'];
		}
		#$stuAnswers = $scoreModel->getStuAnswers($stuCode, $quesIdArray);
		$analyArray['stuScore'] = $stuScoreArray;
		#$analyArray['stuAnswers'] = $stuAnswers;
		$scoreInfo = $this->getExamScoreInfo($examId, $scoreData, $stuCode);
		$analyArray['scoreRanks'] = $scoreInfo[0];
		$analyArray['scoreAward'] = $scoreInfo[1];
		$analyArray['scoreAnaly'] = $scoreInfo[2];
		$analyArray['classInfo'] = $scoreInfo[3];
		$analyArray['partAnaly'] = $partAnalyModel->getPartAnaly($stuScoreArray);
		$analyArray['moduleAnaly'] = $moduleAnalyModel->getModuleAnaly($stuScoreArray);
		#$analyArray['knowledgeRatios'] = $knowledgeAnalyModel->getKnowledgeRatios($stuScoreArray);
		$analyArray['stepAnaly'] = $stepAnalyModel->getStepAnaly($stuScoreArray);
		$analyArray['levelAnaly'] = $levelAnalyModel->getLevelAnaly($stuScoreArray);
		
		$cache->set('PaperAnalys', $key, $analyArray);
		return $analyArray;
	}
	
	protected function scoreData($examId, $stuCode, $subject) {
		import('ORG.Util.NCache');
		$cache = NCache::getCache();
		$key = md5($examId . '_' . $stuCode . '_' . $subject);
		$examInfo = $this->getExamInfo($examId);
		$scoreData = $cache->get('ScoreData', $key);
		$scoreModel = D('Score');
		if(false == $examInfo['exam_score_at'] || ($examInfo['exam_score_at'] && strtotime($examInfo['exam_score_at']) > time())) {
			$scoreModel->updateCache();
		} else {
			if($scoreData) return $scoreData;
		}
		
		$examModel = D('Exam');
		$paperModel = D('Paper');
		$stuModel = D('Student');
		$awardModel = D('Award');
		$scoreAnalyModel = D('ScoreAnaly');
		
		$scoreData = array();
		
		$scoreData['examInfo']  = $examInfo;
		
		$paperInfo = $paperModel->getStuPaper($examId, $stuCode, $subject);
		$scoreData['paperInfo'] = $paperInfo;
		$paperId = $paperInfo['paper_id'];
		
		$paperExamCount = $paperModel->getPaperExamCount($examId, $subject);
		/*$distInfo = array();
		$distInfo['signup_count'] = $examInfo['virtual_count']['signup_count'];
		$distInfo['exam_count'] = $paperExamCount;
		$distArray = $scoreAnalyModel->getScoreDist($examId, $subject);
		foreach ($distArray as $key=>$distCfg) {
			$distArray[$key]['percent'] = sprintf('%.2f', $distCfg['count'] / $distInfo['exam_count'] * 100);
		}
		$distInfo['distArray'] = $distArray;
		$scoreData['distInfo'] = $distInfo;
		*/
		
		$awardCount = $awardModel->awardVCount($examId);
		$scoreData['paperExamCount'] = $paperExamCount;
		$scoreData['awardCount'] = $awardCount;
		
		//学生信息添加准考证号
		$stuInfo = $stuModel->getStuInfo($stuCode);
		$realPaperScore = $scoreModel->getRealPaperScore($examId, $stuCode, $subject);
		$stuInfo['exam_code'] = $realPaperScore['exam_code'];
		$scoreData['stuInfo'] = $stuInfo;
		
		$analyData = $this->getPaperAnalys($paperId, $stuCode);
		$stepModuleRatios = $paperModel->getStepModuleRatios($paperInfo);
		$scoreData['stepModuleRatios'] = $stepModuleRatios;
		$scoreData['analyData'] = $analyData;
		$cache->set('ScoreData', $key, $scoreData);
		return $scoreData;
	}
}
?>