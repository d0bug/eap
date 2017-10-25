<?php
class VirtualModel {
	public $dao = null;
	protected $tableName = 'ex_exam_virtual';
	private $operator = null;
	
	public function __construct() {
		$this->dao = Dao::getDao();
		$operator = User::getLoginUser();
		$this->operator = $operator->getUserKey();
	}
	
	public function find($virtualId) {
		$strQuery = 'SELECT * FROM ' . $this->tableName . '
					 WHERE id=' . $this->dao->quote($virtualId);
		return $this->dao->getRow($strQuery);
	}
	
	#获取虚拟总人数
	public function getVirtualTotal($examId, $virtualType) {
		$strQuery = 'SELECT SUM(score_cnt) cnt 
					 FROM ' . $this->tableName . '
					 WHERE exam_id=' . abs($examId) . '
					   AND virtual_type=' . $this->dao->quote($virtualType);
		return abs($this->dao->getOne($strQuery));
	}
	
	#获取虚拟成绩记录数
	public function getVirtualCount($examId) {
		static $count = null;
		if(null === $count) {
			$strQuery = 'SELECT count(1)  
						 FROM ' . $this->tableName . '
						 WHERE exam_id=' . abs($examId);
			$count = $this->dao->getOne($strQuery);
		}
		return $count;
	}
	
	public function getVirtualList($examId, $currentPage, $pageSize) {
		$recordCount = $this->getVirtualCount($examId);
		$pageCount = ceil($recordCount / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT * FROM ' . $this->tableName . '
					 WHERE exam_id=' . abs($examId);
		
		$order = 'ORDER BY virtual_type ASC,score DESC';
		$virtualList = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		$paperModel = D('Paper');
		$rankModel = D('Rank');
		foreach ($virtualList as $key=>$row) {
			$virtualList[$key]['vtype_caption'] = $paperModel->getVTypeCaption($examId, $row['virtual_type']);
			$rankData = $rankModel->getScoreRank($examId, $row['virtual_type'], $row['score']);
			$virtualList[$key]['rank'] = $rankData['virtual'];
		}
		return $virtualList;
	}
	
	public function saveVirtual($virtualData) {
		$rankModel = D('Rank');
		$examId = abs($virtualData['exam_id']);
		$virtualType = SysUtil::safeString($virtualData['virtual_type']);
		$score = abs($virtualData['score']);
		$scoreCnt = abs($virtualData['count']);
		$time = date('Y-m-d H:i:s');
		
		if($scoreCnt == 0) {
			return array('errorMsg'=>'添加失败：虚拟人数为零');
		}
		$condition = 'exam_id=' . $examId . '
					   AND virtual_type=' . $this->dao->quote($virtualType) . '
					   AND score=' . $score;
		
		$strQuery = 'SELECT count(1) FROM ' . $this->tableName . '
					 WHERE ' . $condition;
		if($this->dao->getOne($strQuery) > 0) {
			$strQuery = 'UPDATE ' . $this->tableName . '
						 SET score_cnt=' . $scoreCnt . ',
						 	 update_user=' . $this->dao->quote($this->operator) . ',
						 	 update_at=' . $this->dao->quote($time) . '
						 WHERE ' . $condition;
		} else {
			$strQuery = 'INSERT INTO ' . $this->tableName . '(
				exam_id,virtual_type,score,score_cnt,create_user,create_at,update_user,update_at
			) VALUES (
				' . $examId . ',
				' . $this->dao->quote($virtualType) . ',
				' . $score . ',
				' . $scoreCnt . ',
				' . $this->dao->quote($this->operator) . ',
				' . $this->dao->quote($time) . ',
				' . $this->dao->quote($this->operator) . ',
				' . $this->dao->quote($time) . '
			)';
		}
		if($this->dao->execute($strQuery)) {
			$scoreModel = D('Score');
			$scoreModel->updateCache();
			$rankModel->updateRank($examId);
			return array('success'=>true);
		}
		return array('errorMsg'=>'宣传数据录入失败');
	}
	
	public function delVirtual($vid) {
		$strQuery = 'DELETE FROM ' . $this->tableName . '
				 	 WHERE id=' . $this->dao->quote($vid);
		$this->dao->execute($strQuery);
		$scoreModel = D('Score');
		$scoreModel->updateCache();
	}
}
?>