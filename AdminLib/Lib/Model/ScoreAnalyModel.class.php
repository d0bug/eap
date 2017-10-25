<?php
class ScoreAnalyModel {
	public $dao = null;
	protected $scoreAnalyTable = 'ex_score_analys';
	protected $moduleAnalyTable = 'ex_module_analys';
	protected $knowledgeAnalyTable = 'ex_knowledge_analys';
	protected $levelAnalyTable = 'ex_level_analys';
	protected $scoreTable = 'ex_exam_scores';
	protected $virtualTable = 'ex_exam_virtual';
	protected $operator = '';
	
	public function __construct() {
		$this->dao = Dao::getDao();
		if (class_exists('User', false)) {
			$operator = User::getLoginUser();
			$this->operator = $operator->getUserKey();
		}
	}
	
	public function getScoreAnalys($examId) {
		$paperModel = D('Paper');
		$rankModel = D('Rank');
		$paperCaptions = $paperModel->getPaperCaptions($examId);
		$strQuery = 'SELECT * FROM ' . $this->scoreAnalyTable . '
					 WHERE exam_id=' . abs($examId) . '
					   AND is_remove=0
					 ORDER BY analy_type,analy_score DESC';
		$analyList = $this->dao->getAll($strQuery);
		$analyArray = array();
		$sortedAnalyTypes = array_merge(array('total'=>array('caption'=>'综合成绩')), $paperCaptions);
		$sortedAnalys = array();
		foreach ($analyList as $analy) {
			$rankData = $rankModel->getScoreRanks($examId, array($analy['analy_type']=>$analy['analy_score']));
			$analy['analy_rank'] = $rankData[$analy['analy_type']]['real'];
			$analy['analy_vrank'] = $rankData[$analy['analy_type']]['virtual'];
			$analy['analy_ratio'] = (100 - $rankData[$analy['analy_type']]['rRatio']) . '%';
			$analy['analy_vratio'] = (100 - $rankData[$analy['analy_type']]['vRatio']) . '%';
			$analy['paper_caption'] = $sortedAnalyTypes[$analy['analy_type']]['caption'];
			$analyArray[$analy['analy_type']][] = $analy;
		}
		foreach ($sortedAnalyTypes as $analyType=>$typeCfg) {
			if(isset($analyArray[$analyType])) {
				$sortedAnalys = array_merge($sortedAnalys, $analyArray[$analyType]);
			}
		}
		
		return $sortedAnalys;
	}
	
	public function saveScoreAnaly($analyInfo) {
		$time = date('Y-m-d H:i:s');
		$analyType = SysUtil::safeString($analyInfo['analy_type']);
		$analyScore = abs($analyInfo['analy_score']);
		$analyText = SysUtil::safeString($analyInfo['analy_text']);
		$examId = abs($analyInfo['exam']);
		if ($analyInfo['analy_id']) {
			$analyId = SysUtil::uuid($analyInfo['analy_id']);
			$strQuery = 'UPDATE ' . $this->scoreAnalyTable . '
						 SET analy_type=' . $this->dao->quote($analyType) . ',
						 	 analy_score=' . $analyScore . ',
						 	 analy_text=' . $this->dao->quote($analyText) . ',
						 	 update_user=' . $this->dao->quote($this->operator) . ',
						 	 update_at=' . $this->dao->quote($time) . '
						 WHERE analy_id=' . $this->dao->quote($analyId);
		} else {
			$analyId = SysUtil::uuid();
			$strQuery = 'INSERT INTO ' . $this->scoreAnalyTable . '
						 (analy_id,exam_id,analy_type,analy_score,analy_text,is_remove,create_user,create_at,update_user,update_at)
						 VALUES ( ' . $this->dao->quote($analyId) . ',
						 		  ' . abs($examId) . ',
						 		  ' . $this->dao->quote($analyType) . ',
						 		  ' . $analyScore . ',
						 		  ' . $this->dao->quote($analyText) . ',
						 		  0,
						 		  ' . $this->dao->quote($this->operator) . ',
						 		  ' . $this->dao->quote($time) . ',
						 		  ' . $this->dao->quote($this->operator) . ',
						 		  ' . $this->dao->quote($time) . ')';
		}
		if($this->dao->execute($strQuery)) {
			return array('success'=>true);
		} else {
			return array('errorMsg'=>'成绩评语设置失败');
		}
	}
	
	public function findScoreAnaly($analyId) {
		$strQuery = 'SELECT * FROM ' . $this->scoreAnalyTable . '
				 	 WHERE analy_id=' . $this->dao->quote($analyId);
		$analyInfo = $this->dao->getRow($strQuery);
		$rankModel = D('Rank');
		$rankData = $rankModel->getScoreRanks($analyInfo['exam_id'], array($analyInfo['analy_type']=>$analyInfo['analy_score']));
		$analyInfo['rank'] = $rankData[$analyInfo['analy_type']]['real'];
		$analyInfo['vRank'] = $rankData[$analyInfo['analy_type']]['virtual'];
		$analyInfo['ratio'] = (100 - $rankData[$analyInfo['analy_type']]['rRatio']) . '%';
		$analyInfo['vRatio'] = (100 - $rankData[$analyInfo['analy_type']]['vRatio']) . '%';
		return $analyInfo;
	}
	
	public function delScoreAnaly($analyId) {
		$strQuery = 'UPDATE ' . $this->scoreAnalyTable . '
					 SET is_remove=' . time() . ',
					 	 update_user=' . $this->dao->quote($this->operator) . ',
					 	 update_at=' . $this->dao->quote(date('Y-m-d H:i:s')) . '
					 WHERE analy_id=' . $this->dao->quote($analyId);
		if($this->dao->execute($strQuery)) {
			return array('success'=>true);
		}
		return array('errorMsg'=>'成绩评语删除失败');
	}
	
	public function getScoreAnalyData($examId, $scoreData) {
		$strQuery = 'SELECT * FROM ' . $this->scoreAnalyTable . '
					 WHERE exam_id=' . abs($examId) . '
					   AND is_remove=0
					 ORDER BY analy_score DESC';
		$analyList = $this->dao->getAll($strQuery);
		$analyArray = array();
		foreach ($analyList as $analy) {
			$analyArray[$analy['analy_type']][] = $analy;
		}
		$scoreAnalys = array();
		foreach ($scoreData as $scoreType=>$score) {
			if(isset($analyArray[$scoreType])) {
				foreach ($analyArray[$scoreType] as $analy) {
					if($analy['analy_score'] <= $score) {
						$scoreAnalys[$scoreType] = $analy['analy_text'];
						break;
					}
				}
			}
		}
		return $scoreAnalys;
	}
	
	public function getScoreDist($examId, $subject='') {
		import('ORG.Util.NCache');
		$cache = NCache::getCache();
		$key = $examId . '_' . $subject;
		$nameSpace = 'ScoreDist';
		$distArray = $cache->get($nameSpace, $key);
		if(null  == $distArray) {
			$scores = $this->getScorePartArray($examId, $subject);
			if($subject) {
				$distArray = $this->getSubjectScoreDist($examId, $subject);
			} else {
				$distArray = $this->getTotalScoreDist($examId);
			}
			$cache->set($nameSpace, $key, $distArray);
		}
		return $distArray;
	}
	
	private function getScorePartArray($examId, $subject='') {
		$paperModel = D('Paper');
		$maxScoreArray = $paperModel->getPaperScores($examId);
		$scorePartArray = array();
		if(false == $subject) {
			$subject = 'total';
			$analyType = 'total';
		} else {
			$analyType = $subject . '_real';
		}
		$scorePartArray[] = $maxScoreArray[$subject];
		$strQuery = 'SELECT analy_score FROM ' . $this->scoreAnalyTable . '
					 WHERE analy_type=' . $this->dao->quote($analyType) . '
					   AND exam_id=' . abs($examId) . '
					 ORDER BY analy_score DESC';
		$scoreList = $this->dao->getAll($strQuery);
		foreach ($scoreList as $row) {
			$score = $row['analy_score'];
			if($score >10) {
				$scorePartArray[] = $score;
			}
		}
		$scorePartArray[] = 0;
		return $scorePartArray;
	}
	
	private function getTotalScoreDist($examId) {
		$scoreParts = $this->getScorePartArray($examId);
		$distArray = array();
		foreach ($scoreParts as $idx=>$score) {
			if(isset($scoreParts[$idx + 1])) {
				$key = 'dist_' . abs(intval($score)) . '_' . abs(intval($scoreParts[$idx + 1]));
				$distArray[$key] = array('min'=>$scoreParts[$idx + 1], 'max'=>$scoreParts[$idx]);
			}
		}
		$virtualType = 'total';
		$strQuery = 'SELECT ';
		foreach ($distArray as $key => $distCfg) {
			$strQuery .= ' SUM(CASE WHEN total_score <= ' . abs($distCfg['max']) . ' 
								 AND total_score >' . abs($distCfg['min']) . ' 
						   THEN 1 ELSE 0 END) ' . $key . ',';
		}
		$strQuery = substr($strQuery,0, -1);
		$strQuery .= ' FROM (
						SELECT stu_code,sum(paper_total_score) total_score
						FROM ' . $this->scoreTable . '
						WHERE exam_id=' . abs($examId) . '
						GROUP BY stu_code
					 ) scores';
		
		$distRow = $this->dao->getRow($strQuery);
		foreach ($distArray as $key=>$distCfg) {
			$distArray[$key]['count'] = abs($distRow[$key]);
		}
		$strQuery = 'SELECT ';
		foreach ($distArray as $key=>$distCfg) {
			$strQuery .= ' SUM(CASE WHEN score <=' . abs($distCfg['max']) . '
			 			 	    AND score >' . abs($distCfg['min']) . '
			 			   THEN score_cnt ELSE 0 END) ' . $key . ',';
		}
		$strQuery = substr($strQuery, 0, -1);
		$strQuery .= ' FROM ' . $this->virtualTable . '
					  WHERE exam_id=' . abs($examId) . '
					    AND virtual_type=' . $this->dao->quote($virtualType);
		$distRow = $this->dao->getRow($strQuery);
		foreach ($distArray as $key=>$distCfg) {
			$distArray[$key]['count'] += abs($distRow[$key]);
		}
		return $distArray;
	}
	
	private function getSubjectScoreDist($examId, $subject) {
		$scoreParts = $this->getScorePartArray($examId, $subject);
		$virtualType = $subject . '_real';
		$distArray = array();
		foreach ($scoreParts as $idx=>$score) {
			if(isset($scoreParts[$idx + 1])) {
				$key = 'dist_' . abs(intval($score)) . '_' . abs(intval($scoreParts[$idx + 1]));
				$distArray[$key] = array('min'=>$scoreParts[$idx + 1], 'max'=>$scoreParts[$idx]);
			}
		}
		$strQuery = 'SELECT ';
		foreach ($distArray as $key=>$distCfg) {
			$strQuery .= 'SUM(CASE WHEN paper_total_score <=' . abs($distCfg['max']) . '
						 		    AND paper_total_score >' . abs($distCfg['min']) . '
						 	  THEN 1 ELSE 0 END) ' . $key . ',';
		}
		$strQuery = substr($strQuery, 0, -1);
		$strQuery .= ' FROM ' . $this->scoreTable . '
					 WHERE exam_id=' . abs($examId) . '
					   AND paper_subject=' . $this->dao->quote($subject);
		$distRow = $this->dao->getRow($strQuery);
		foreach ($distArray as $key=>$distCfg) {
			$distArray[$key]['count'] = abs($distRow[$key]);
		}
		$strQuery = 'SELECT ';
		foreach ($distArray as $key=>$distCfg) {
			$strQuery .= ' SUM(CASE WHEN score <=' . abs($distCfg['max']) . '
			 			 	    AND score >' . abs($distCfg['min']) . '
			 			   THEN score_cnt ELSE 0 END) ' . $key . ',';
		}
		$strQuery = substr($strQuery, 0, -1);
		$strQuery .= ' FROM ' . $this->virtualTable . '
					  WHERE exam_id=' . abs($examId) . '
					    AND virtual_type=' . $this->dao->quote($virtualType);
		$distRow = $this->dao->getRow($strQuery);
		foreach ($distArray as $key=>$distCfg) {
			$distArray[$key]['count'] += abs($distRow[$key]);
		}
		return $distArray;
	}
}
?>