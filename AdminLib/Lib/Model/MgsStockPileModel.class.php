<?php
class MgsStockPileModel extends Model {	
	private $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao();
		$this->operator = User::getLoginUser();  //获取操作者
		$this->tableName = 'MGS_StockPile';
		
		$this->tableBsArea = 'BS_Area';  //校区表
		$this->tableHistory = 'MGS_StockPileHistory';   //礼品-校区-详细记录表
		$this->tableGPHistory = 'MGS_giftPurHistory';   //礼品入库 详细记录表
		$this->tableMG = 'MGS_Gift';  //礼品表（修改可用库存使用）
	}


	//获取 获取一个礼品是否 已经把校区初始化到 这个校区分配表里
	public function get_goodsFenpei($giftCode){
        return 0;
		return $this->dao->getOne("SELECT count(1) FROM " . $this->tableName . " WHERE giftCode ='".$giftCode."' ");
	}
	
	//循环把各个 各个校区插入到 表中
	public function AddGoodsArea($giftCode){
		$AreasCode = array();
		$AreasCode = $this->dao->getAll("SELECT [sCode] FROM ".$this->tableBsArea." WHERE scode NOT IN (SELECT areacode FROM " . $this->tableName . " WHERE giftcode=" . $this->dao->quote($giftCode) . ")");
		for($i=0; $i<count($AreasCode); $i++){
			$this->dao->execute("INSERT INTO ".$this->tableName." ([areaCode], [giftCode], [totalQuantity], [sellQuantity], [bookQuantity], [realQuantity], [warnThreshold]) VALUES('".$AreasCode[$i]['scode']."', '".$giftCode."', '0', '0', '0', '0', '0')");		
		}
		if($this->dao->affectRows()){
			return true;
		}	
		return false;
	}
	//获取各校区分配情况列表
	public function get_goodsAreaList($giftCode){ //联合这两个表查询
		return $this->dao->getAll("SELECT a.*, b.sName FROM " . $this->tableName . " as a, ".$this->tableBsArea." as b WHERE a.giftCode ='".$giftCode."' and a.areaCode = b.sCode and b.bValid = 1");
	}
	
	
	//修改 礼品校区分配库存数
	public function update_totalQuantity($goodsCount, $serial, $sid){
		$this->dao->execute("UPDATE ".$this->tableMG." SET [stockQuantity] = [stockQuantity] - '".$goodsCount."'  WHERE [serial] = '".$serial."' ");  //礼品表的可用库存要减少
		$this->dao->execute("UPDATE ".$this->tableName." SET [totalQuantity] = [totalQuantity] + '".$goodsCount."', [realQuantity] = [realQuantity] + '".$goodsCount."'  WHERE [id] = '".$sid."' ");  //礼品-校区库存要增加, 实际库存也要增加（尽管实际库存 等于总分配量 - 已领取量 - 已预订量）

		$userKey = $this->operator->getUserKey();
		$operator = trim(str_replace('', '',$this->dao->quote($userKey)));
		$remark = $goodsCount >= 0 ? '执行了追加操作' : '执行了减少操作'; 
		$this->dao->execute("INSERT INTO ".$this->tableHistory." ([stockPileID], [changeValue], [operator], [time], [remark]) VALUES('".$sid."', '".$goodsCount."', ".$this->dao->quote($userKey).", '".time()."', '".$remark."')");
		
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}
	
	//礼品卡 -- 校区 日志列表
	public function get_AreaHistoryList($condition='', $currentPage=1, $pageSize=10){
		$count = $this->get_areaCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		
		$strSql = "SELECT * FROM " . $this->tableHistory. " WHERE 1=1 ";
		if($condition){
			$strSql .= "AND stockPileID = " .$condition;	
		}
		$order ='ORDER BY serial DESC';
		return $this->dao->getLimit($strSql, $currentPage, $pageSize, $order);
	}
	//获取满足条件的记录总条数  在分页查询时用
	public function get_areaCount($condition='') {
		$strQuery = 'SELECT count(1) FROM ' . $this->tableHistory . ' WHERE 1=1 ';
		if ($condition) {
			$strQuery .= ' AND stockPileID = ' . $condition;
		}
		$count = $this->dao->getOne($strQuery);
		return $count;
	}
	
	//获取一条记录的信息
	public function get_areaOne($sid){
		return $this->dao->getAll("SELECT TOP 1 * FROM " . $this->tableName . " WHERE id =".$sid." ");
	}
	
	
	
	/*礼品 入库 操作 日志列表 */
	public function get_giftPurHistory($condition='', $currentPage=1, $pageSize=10){
		$count = $this->get_giftPurCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		
		$strSql = "SELECT * FROM " . $this->tableGPHistory. " WHERE 1=1 ";
		if($condition){
			$strSql .= "AND giftCode = '" .$condition."'";	
		}
		$order ='ORDER BY serial DESC';
		return $this->dao->getLimit($strSql, $currentPage, $pageSize, $order);
	}
	public function get_giftPurCount($condition='') {
		$strQuery = 'SELECT count(1) FROM ' . $this->tableGPHistory . ' WHERE 1=1 ';
		if ($condition) {
			$strQuery .= "AND giftCode = '" .$condition."'";
		}
		$count = $this->dao->getOne($strQuery);
		return $count;
	}
	

	
}
?>