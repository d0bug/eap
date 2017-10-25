<?php
class PrintModel {
	private $dao = null;
	private $printTable = 'ex_area_print';
	private $scoreTable = 'ex_exam_scores';
	private $posTable = 'ex_positions';

	public function __construct() {
		$this->dao = Dao::getDao();
	}

	public function getPrintPositions($examId) {
		$strQuery = 'SELECT pos.pos_caption, score.*,pr.is_print,pr.cur_count
					FROM ' . $this->posTable . ' pos,
					(SELECT exam_id,pos_code, count(1) score_cnt 
					 FROM ' . $this->scoreTable . ' score 
					 WHERE exam_id=' . abs($examId) . '
					   AND paper_total_score >0
					 GROUP BY pos_code,exam_id
					 HAVING count(1) >0) score
					 LEFT JOIN ' . $this->printTable . ' pr
					 ON pr.exam_id=score.exam_id 
					   AND pr.pos_code=score.pos_code
					 WHERE pos.pos_code=score.pos_code
					   AND pos.is_remove=0';
		$posList = $this->dao->getAll($strQuery);
		return $posList;
	}

	public function addPrint($examId, $posCode, $totalCount) {
		$strQuery = 'SELECT * FROM ' . $this->printTable . '
					 WHERE exam_id=' . abs($examId) . '
					   AND pos_code=' . $this->dao->quote($posCode);
		$printInfo = $this->dao->getRow($strQuery);
		if($printInfo) {
			if($totalCount == $printInfo['total_count']) {
				return array('errorMsg'=>'打印任务已存在，请不要重复添加');
			} else {
				$strQuery = 'UPDATE ' . $this->printTable . '
							 SET total_count=' . abs($totalCount) . '
							 WHERE exam_id=' . abs($examId) . '
							   AND pos_code=' . $this->dao->quote($stuCode);
				$this->dao->execute($strQuery);
				return array('success'=>true);
			}
		} else {
			$strQuery = 'INSERT INTO ' . $this->printTable . '
						 (exam_id,pos_code,is_print,total_count,cur_count)
						 VALUES (' . abs($examId) . ',
							     ' . $this->dao->quote($posCode) . ',
								 1, 
								 ' . abs($totalCount) . ',
								 0)';
			if($this->dao->execute($strQuery)) {
				$this->startPrint($examId, $posCode);
				return array('success'=>true);
			}
		}
	}
	

	public function startPrint($examId, $posCode) {
		$dir = APP_PATH . '/Runtime/Data/print';
		if(false == is_dir($dir)) {
			@mkdir($dir, 0777, true);
		}
		$file = $dir . '/' . $examId . '_' . $posCode . '.txt';
		$lockFile = $dir . '/' . $examId . '_' . $posCode . '.lock';
		if(false == file_exists($file) && false == file_exists($lockFile)) {
			file_put_contents($file, $examId . ',' . $posCode);
		}
	}
	
};
?>