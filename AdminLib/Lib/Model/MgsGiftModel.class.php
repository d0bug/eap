<?php
class MgsGiftModel extends Model {	
	private $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao();
		$this->operator = User::getLoginUser();  //获取操作者
		$this->tableName = 'MGS_Gift';
		$this->tableHistory = 'MGS_GiftPurHistory';		//
	}

	//礼品卡列表
	public function get_goodsList($condition='', $currentPage=1, $pageSize=10){
		$count = $this->get_goodsCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		
		//return $this->dao->getAll("SELECT [serial], [giftCode], [giftName], [costValue], [stockQuantity], [isValid] FROM " . $this->tableName. " ORDER BY [serial] DESC");  //dao->getAll(sql语句)； 获取所有
		$strSql = "SELECT [serial], [giftCode], [giftName], [costValue], [stockQuantity], [isValid] FROM " . $this->tableName. " WHERE 1=1 ";
		if($condition){
			$strSql .= "AND " .$condition;	
		}
		$order ='ORDER BY serial DESC';
		return $this->dao->getLimit($strSql, $currentPage, $pageSize, $order);
	}
	
	
	public function add_goods($arr){
		$this->dao->execute("INSERT INTO ".$this->tableName."
		                    ([giftName], [giftCode], [giftImage], [costValue], [isValid], [giftDetail], [stockQuantity],[present_id])
		VALUES('$arr[giftName]', '$arr[giftCode]', '$arr[giftImage]', '$arr[costValue]', '$arr[isValid]', '$arr[giftDetail]', '0','$arr[present_id]')");
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}
	//修改记录
	public function edit_goods($arr, $serial){
		$this->dao->execute("UPDATE ".$this->tableName." SET
			[giftName] = '".$arr['giftName']."' , 
			[giftCode] = '".$arr['giftCode']."' , 
			[giftImage] = '".$arr['giftImage']."' , 
			[costValue] = '".$arr['costValue']."' , 
			[isValid] = '".$arr['isValid']."' ,
			[present_id]='".$arr['present_id']."',
			[giftDetail] = '".$arr['giftDetail']."' WHERE [serial] = ".$serial." "); 	
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}
	
	//修改 库存数
	public function update_stockQuantity($stockQuantity, $giftCode){
		$this->dao->execute("UPDATE ".$this->tableName." SET [stockQuantity] = [stockQuantity] + '".$stockQuantity."'  WHERE [giftCode] = '".$giftCode."' "); 		//记入日志表 serial  giftCode  purchaseQuantity  operator  time  remark
		$userKey = $this->operator->getUserKey();
		$operator = trim(str_replace('', '',$this->dao->quote($userKey)));
		$remark = $stockQuantity >=0 ? '执行了新增操作' : '执行了减少操作'; 
		$this->dao->execute("INSERT INTO ".$this->tableHistory." ([giftCode], [purchaseQuantity], [operator], [time], [remark]) VALUES('".$giftCode."', '".$stockQuantity."', ".$this->dao->quote($userKey).", '".time()."', '".$remark."')");
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}
	//获取 一个礼品的 总入库数 
	public function get_goodsZongruku($giftCode){
		return $this->dao->getOne("SELECT sum(purchaseQuantity) FROM " . $this->tableHistory . " WHERE giftCode ='".$giftCode."' ");
	}
	
	
	//获取一条记录的信息
	public function get_goodsOne($serial){
		return $this->dao->getAll("SELECT TOP 1 * FROM " . $this->tableName . " WHERE serial =".$serial." ");
	}
	// 删除记录
	public function del_goods($serial){
		$this->dao->execute(" DELETE FROM ".$this->tableName." WHERE serial = ".$serial." ");	
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}
	
	//获取满足条件的记录总条数  在分页查询时用
	public function get_goodsCount($condition='') {
		$strQuery = 'SELECT count(1) FROM ' . $this->tableName . ' WHERE 1=1 ';
		if ($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		$count = $this->dao->getOne($strQuery);
		return $count;
	}
	
	public function get_handoutsCount($condition='') {
		$strQuery = 'SELECT count(1) FROM ' . $this->tableName . ' WHERE 1=1 ';
		if ($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		$count = $this->dao->getOne($strQuery);
		return $count;
	}
	
	public function get_manager(){
		$userKey = $this->operator->getUserKey();
		$operator = trim(str_replace('', '',$this->dao->quote($userKey)));
		return	$this->dao->quote($userKey);
	}
	
	//所有礼品卡列表  不分页（共下载使用）
	public function get_goodsListAll($condition='', $currentPage=1, $pageSize=100){
		$count = $this->get_goodsCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		
		//return $this->dao->getAll("SELECT [serial], [giftCode], [giftName], [costValue], [stockQuantity], [isValid] FROM " . $this->tableName. " ORDER BY [serial] DESC");  //dao->getAll(sql语句)； 获取所有
		$strSql = "SELECT [serial], [giftCode], [giftName] FROM " . $this->tableName. " WHERE 1=1 ";
		if($condition){
			$strSql .= "AND " .$condition;	
		}
		$order ='ORDER BY serial ASC';
		return $this->dao->getLimit($strSql, $currentPage, $pageSize, $order);
	}

}