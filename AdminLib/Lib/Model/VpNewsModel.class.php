<?php

class VpNewsModel extends Model {
	public  $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao();
		$this->tableName = 'vp_news';
	}

	public function get_newsList_menu($ntype,$limit=8){
		return $this->dao->getAll("SELECT TOP $limit [nid] ,[user_key],[user_name],[ntype],[title],[ncontent],[instime] FROM ".$this->tableName." WHERE [ntype] = '$ntype' ORDER BY [instime] DESC");
	}

	public function add_news($arr,$user_key){
		if(!empty($user_key)){
			$this->dao->execute('INSERT INTO '.$this->tableName." ([user_key],[user_name],[ntype],[title],[ncontent],[instime]) VALUES('$user_key','$arr[user_name]','$arr[ntype]',".$this->dao->quote($arr['title']).",".$this->dao->quote($arr['ncontent']).",'".time()."')");
			if($this->dao->affectRows()){
				return true;
			}
			return false;
		}
		return false;
	}

	public function get_newsInfo_by_nid($nid){
		return $this->dao->getRow("SELECT [nid],[user_key],[user_name],[ntype],[title],[ncontent],[instime] FROM ".$this->tableName." WHERE [nid] = '$nid'");
	}

	public function get_newsList($condition='', $currentPage=1, $pageSize=20){
		$count = $this->get_newsCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT [nid],[user_key],[user_name],[ntype],[title],[ncontent],[instime] FROM ' . $this->tableName . ' WHERE 1=1 ';
		if($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		$order = ' ORDER BY instime DESC';
		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}

	public function get_newsCount($condition='') {
		$strQuery = 'SELECT count(1) FROM ' . $this->tableName . ' WHERE 1=1 ';
		if ($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		return $this->dao->getOne($strQuery);
	}
	
	public function delete_news($nidStr){
		$this->dao->execute("DELETE FROM ".$this->tableName." WHERE [nid] IN ($nidStr)");
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}
}
?>