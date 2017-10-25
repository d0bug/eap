<?php
import('ORG.Util.NCache');
class RankModel {
	public $dao = null;
	protected $scoreTable = 'ex_exam_scores';
	protected $virtualTable = 'ex_exam_virtual';
	protected $cachePrefix = 'ExamRank';
	protected $esCountTable = 'ex_exam_count';
	protected $examTable = 'ex_exams';
	protected $groupTable = 'ex_exam_groups';
	protected $esTable = 'ex_exam_students';
	
	public function __construct(){
		$this->dao = Dao::getDao();
	}
	
	public function getExamInfo($examId) {
		$examId = abs($examId);
		$strQuery = 'SELECT e.*,g.group_caption
					 FROM ' . $this->examTable . ' e,
					 	  ' . $this->groupTable . '	g 
					 WHERE g.group_id=e.group_id
					   AND e.exam_id=' . $examId;
		$examInfo = $this->dao->getRow($strQuery);
		$examInfo['exam_caption'] = $examInfo['group_caption'] . ' [' . $examInfo['exam_caption'] . ']';
		$signupCnt = $this->getRealSignupCnt($examId);
		$strQuery = 'SELECT COUNT(DISTINCT stu_code) 
					 FROM ' . $this->scoreTable . '
					 WHERE exam_id=' . $examId; 
		$examCnt = $this->dao->getOne($strQuery);
		$examInfo['real_count'] = array('signup_count'=>$signupCnt, 'exam_count'=>$examCnt);
		$examInfo['virtual_count'] = $examInfo['real_count'];
		$strQuery = 'SELECT signup_count,exam_count 
					 FROM ' . $this->esCountTable . '
					 WHERE exam_id=' . $examId . '
					   AND subject=' . $this->dao->quote('total');
		$virtualCnt = $this->dao->getRow($strQuery);
		if($virtualCnt) {
			$examInfo['virtual_count']['signup_count'] = $virtualCnt['signup_count'];
			$examInfo['exam_count'] = $virtualCnt['exam_count'];
		} else {
			$strQuery = 'SELECT sum(score_cnt) 
						 FROM ' . $this->virtualTable . '
						 WHERE exam_id=' . $examId . '
						   AND virtual_type=' . $this->dao->quote('total');
			$examInfo['virtual_count']['exam_count'] += $this->dao->getOne($strQuery);
		}
		return $examInfo;
	}
	
	public function getScoreRanks($examId, $scoreData) {
		static $cacheDataArray = array();
		if(false == isset($cacheDataArray[$examId])) {
			$cache = NCache::getCache();
			$cacheData = $cache->get($this->cachePrefix, $this->cachePrefix . $examId);
			
			foreach ($scoreData as $key=>$score) {
				if(false == is_array($cacheData) || false == isset($cacheData[$key])) {
					$cacheData = $this->getScoreCache($examId);
					break;
				}
			}
			$cacheDataArray[$examId] = $cacheData;
		}
		if(false == $cacheData) {
			$cacheData = $cacheDataArray[$examId];
		}

		$rankData = array();
		foreach ($scoreData as $key=>$score) {
			$rankData[$key] = $this->getRank($examId, $cacheData, $key, $score);
		}
		return $rankData;
	}
	
	private function getRank($examId, $cacheData, $key, $score) {
		$rRank = 0;
		$vRank = 0;
		#高于本人分数的人数统计数组
		$countArray = array();
		foreach ($cacheData[$key]['real'] as $scoreCfg) {
			if($scoreCfg['score'] > $score) {
				$rRank += $scoreCfg['count'];
				$countArray['real'] += $scoreCfg['count'];
			}
			
		}
		$vRank = $rRank;
		$countArray['virtual'] = $countArray['real'];
		foreach ($cacheData[$key]['virtual'] as $scoreCfg) {
			if($scoreCfg['score'] > $score) {
				$vRank += $scoreCfg['count'];
				$countArray['virtual'] += $scoreCfg['count'];
			}
		}
		list($realCount, $virtualCount) = $this->getExamStudentCount($examId, $key);
		$realRatio = sprintf('%.2f', ($realCount['exam_count'] - $countArray['real']) / $realCount['exam_count'] *  100);
		$virtualRatio = sprintf('%.2f', ($virtualCount['exam_count'] - $countArray['virtual']) / $virtualCount['exam_count'] * 100);
		return array('score'=>$score, 'real'=>$rRank + 1, 'virtual'=>$vRank + 1, 'rRatio'=>$realRatio, 'vRatio'=>$virtualRatio);
	}
	
	#实际报名人数
	private function getRealSignupCnt($examId) {
		static $realSignupCnt = null;
		if(null !== $realSignupCnt) return $realSignupCnt;
		$strQuery = 'SELECT count(1) FROM ' . $this->esTable . '
					 WHERE exam_id=' . $examId . '
					   AND is_cancel=0';
		$realSignupCnt = $this->dao->getOne($strQuery);
		return $realSignupCnt;
	}
	
	#根据成绩类型获取实际报名人数，以及实际考试人数,均以数组形式返回
	private function getExamStudentCount($examId, $key='') {
		if($key == 'total') {
			$examInfo = $this->getExamInfo($examId);
			return array($examInfo['real_count'],$examInfo['virtual_count']);
		}
		$ar = explode('_', $key);
		$subject = $ar[0];
		$realCntArray['signup_count'] = $this->getRealSignupCnt($examId);
		
		$strQuery = 'SELECT count(DISTINCT stu_code) stu_count
					 FROM ' . $this->scoreTable . '
					 WHERE exam_id=' . abs($examId) . '
					   AND paper_subject=' . $this->dao->quote($subject);
		$realCntArray['exam_count'] = $this->dao->getOne($strQuery);
		
		$virtualCntArray = $realCntArray;
		$strQuery = 'SELECT signup_count,exam_count 
					 FROM ' . $this->esCountTable . '
					 WHERE exam_id=' . abs($examId) . '
					   AND subject=' . $this->dao->quote($subject);
		$vCntInfo = $this->dao->getRow($strQuery);
		if($vCntInfo) {
			$virtualCntArray['signup_count'] = $vCntInfo['signup_count'];
			$virtualCntArray['exam_count'] = $vCntInfo['exam_count'];
		} else {
			$strQuery = 'SELECT sum(score_cnt) 
						 FROM ' . $this->virtualTable . '
						 WHERE exam_id=' . $examId . '
						   AND virtual_type=' . $this->dao->quote($key);
			$virtualCntArray['exam_count'] += $this->dao->getOne($strQuery);
		}
		return array($realCntArray, $virtualCntArray);
	}
	
	private function getScoreCache($examId) {
		$cache = NCache::getCache();
		$cacheData = array();
		$strQuery = 'SELECT distinct paper_subject, paper_type FROM ' . $this->scoreTable . '
					 WHERE exam_id=' . abs($examId);
		$typeList = $this->dao->getAll($strQuery);
		#单科
		foreach ($typeList as $row) {
			$subject = $row['paper_subject'];
			$paperType = $row['paper_type'];
			$key = $subject . '_' . $paperType;
			$strQuery = 'SELECT paper_total_score,count(1) cnt
						 FROM ' . $this->scoreTable . '
						 WHERE exam_id=' . abs($examId) . '
						   AND paper_subject=' . $this->dao->quote($subject) . '
						   AND paper_type=' . $this->dao->quote($paperType) . '
						 GROUP BY paper_total_score
						 ORDER BY paper_total_score DESC';
			$countList = $this->dao->getAll($strQuery);
			$countArray = array();
			foreach ($countList as $row) {
				$countArray['count_' . sprintf('%04d', $row['paper_total_score']* 10)] = array('score'=>$row['paper_total_score'], 'count'=>$row['cnt']);
			}
			$cacheData[$key]['real'] = $countArray;
			$strQuery = 'SELECT score,score_cnt 
						 FROM ' . $this->virtualTable . '
						 WHERE exam_id=' . abs($examId) . '
						   AND virtual_type=' . $this->dao->quote($key) . '
						 ORDER BY score DESC';
			$virtualList = $this->dao->getAll($strQuery);
			$virtualArray = array();
			foreach ($virtualList as $row) {
				$virtualArray['count_' . sprintf('%04d', $row['score'] * 10)]['score'] = $row['score'];
				$virtualArray['count_' . sprintf('%04d', $row['score'] * 10)]['count'] += abs($row['score_cnt']);
			}
			$cacheData[$key]['virtual'] = $virtualArray;
		}
		
		#综合
		$strQuery = 'SELECT exam_code,sum(CASE WHEN paper_type =' . $this->dao->quote('real') . ' THEN paper_total_score ELSE 0 END) score
					 FROM ' . $this->scoreTable . '
					 WHERE exam_id=' . abs($examId) . '
					 GROUP BY exam_code
					 ORDER BY score DESC';
		$countList = $this->dao->getAll($strQuery);
		$countArray = array();
		foreach ($countList as $row) {
			$countArray['count_' . sprintf('%04d', $row['score'] * 10)]['score'] = $row['score'];
			$countArray['count_' . sprintf('%04d', $row['score'] * 10)]['count'] += 1;
		}
		$cacheData['total']['real'] = $countArray;
		$strQuery = 'SELECT score,score_cnt FROM ' . $this->virtualTable . '
					 WHERE exam_id=' . abs($examId) . '
					   AND virtual_type=' . $this->dao->quote('total') . '
					 ORDER BY score DESC';
		$virtualList = $this->dao->getAll($strQuery);
		$virtualArray = array();
		foreach ($virtualList as $row) {
			$virtualArray['count_' . sprintf('%04d', $row['score'] * 10)]['score'] = $row['score'];
			$virtualArray['count_' . sprintf('%04d', $row['score'] * 10)]['count'] += abs($row['score_cnt']);
		}
		$cacheData['total']['virtual'] = $virtualArray;
		
		$cache->set($this->cachePrefix, $this->cachePrefix . $examId, $cacheData);
		
		return $cacheData;
	}
	
	public function getScoreRank($examId, $scoreKey, $score) {
		static $ranks = array();
		$key = md5($examId . '_' . $scoreKey . '_' . $score);
		if(false == isset($ranks[$key])) {
			$scoreData = array($scoreKey=>$score);
			$scoreRanks = $this->getScoreRanks($examId, $scoreData);
			$ranks[$key] = $scoreRanks[$scoreKey];
		}
		return $ranks[$key];
	}
	
	public function updateRank($examId) {
		$scoreModel = D('Score');
		$scoreModel->updateCache();
		$cache = NCache::getCache();
		$cache->delete($this->cachePrefix,  $this->cachePrefix . $examId);
	}
}
?>