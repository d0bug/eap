<?php
class MgsGiftOrderModel extends Model {	
	private $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao();
		$this->operator = User::getLoginUser();  //获取操作者
		$this->tableName = 'MGS_GiftOrder';
		$this->tableGift = 'MGS_Gift'; // 用于调取礼品名称
		$this->tableBsStu = 'BS_Student'; // 用于调取收货人和序号
		$this->tableBsArea = 'BS_Area';  //用于获取校区名称
		$this->tableSP = 'MGS_StockPile';  //用于订单状态改变时更改 礼品 -校区 分配数量情况
		
	}

	//礼品卡列表
	public function get_ordersList($condition='', $currentPage=1, $pageSize=10){
		$count = $this->get_ordersCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		
		$strSql = "SELECT o.*, g.giftName, s.sName, s.sAliasCode, a.sName as aname FROM 
			" . $this->tableName. " as o, 
			".$this->tableGift." as g, 
			".$this->tableBsStu." as s, 
			".$this->tableBsArea." as a 
			WHERE o.giftCode = g.giftCode AND o.stuCode = s.sCode AND o.areaCode = a.sCode ";
		if($condition){
			$strSql .= "AND " .$condition;	
		}
		$order ='ORDER BY serial DESC';
		//echo $strSql;
		session_start();   // 这里把SQL语句保存到SESSION里是为了下面的 下载报表功能使用
		$_SESSION['order_sql'] = $strSql;
		
		return $this->dao->getLimit($strSql, $currentPage, $pageSize, $order);
	}	
	//获取满足条件的记录总条数  在分页查询时用
	public function get_ordersCount($condition='') {
		//$strQuery = 'SELECT count(1) FROM ' . $this->tableName . ' as o WHERE 1=1 ';  //因为是联合多表查询，这里要修改
		$strQuery = "SELECT count(1) FROM   
			" . $this->tableName. " as o, 
			".$this->tableGift." as g, 
			".$this->tableBsStu." as s, 
			".$this->tableBsArea." as a 
			WHERE o.giftCode = g.giftCode AND o.stuCode = s.sCode AND o.areaCode = a.sCode ";
		
		if ($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		$count = $this->dao->getOne($strQuery);
		return $count;
	}
	
	//修改订单状态
	public function ordersUpdate($serial, $status,$giftquantity, $giftcode, $areacode){ # 2013 11 22  16:30 增加一个 时间[endTime] = '".time()."'
		//修改状态
		$this->dao->execute("UPDATE ".$this->tableName." SET [status] = '".$status."', [endTime] = '".time()."'  WHERE [serial] = '".$serial."' ");
		
		if($status == 2){ //当由状态1(未领取) 更新为状态2（已领取）时，  已领取字段增加， 已预订字段减少
			$this->dao->execute("UPDATE ".$this->tableSP." SET [sellQuantity] = [sellQuantity] + '".$giftquantity."', [bookQuantity] = [bookQuantity] - '".$giftquantity."'  WHERE [giftCode] = '".$giftcode."' AND [areaCode] = '".$areacode."' ");
		}else{  //当由状态2(已领取) 更新为状态1（未领取）时，  已领取字段减少， 已预订字段增加
			$this->dao->execute("UPDATE ".$this->tableSP." SET [sellQuantity] = [sellQuantity] - '".$giftquantity."', [bookQuantity] = [bookQuantity] + '".$giftquantity."'  WHERE [giftCode] = '".$giftcode."' AND [areaCode] = '".$areacode."' ");
		}
		
		if($this->dao->affectRows()){
			return true;
		}
		return false;	
	}
	
	//撤销订单	#2014 0523  180005
	public function ordersDel($serial, $status,$giftquantity, $giftcode, $areacode, $stucode){
		#第一步 还原校区分配库存信息  已预订bookQuantity  与实际库存realQuantity
		$this->dao->execute("UPDATE ".$this->tableSP." SET [bookQuantity] = [bookQuantity] - '".$giftquantity."', [realQuantity] = [realQuantity] + '".$giftquantity."'  WHERE [giftCode] = '".$giftcode."' AND [areaCode] = '".$areacode."' ");
		
		#第二步	获取此礼品的所需积分，然后把相关积分返回给此用户的积分总额中。
		$giftInfo = array();
		$giftInfo = $this->get_giftInfo($giftcode);
		$costValue = intval($giftInfo[0]['costvalue']) ;
		$jifenSum = $costValue*$giftquantity;
		$this->dao->execute("UPDATE mgs_integral SET [totalintegral]= [totalintegral] + ".$jifenSum." WHERE stuCode='".$stucode."'");
		
		#第三步	删除这个订单
		$this->dao->execute("DELETE FROM ".$this->tableName." WHERE [serial] = '".$serial."' ");
		if($this->dao->affectRows()){
			return true;
		}
		return false;	
	}
	public function get_giftInfo($giftCode){	#2014 0523
		return $this->dao->getAll("SELECT TOP 1 * FROM ".$this->tableGift." WHERE giftCode='".$giftCode."'");
	}
	
	//获取所有校区供查询使用
	public function get_areaList(){
		return $this->dao->getAll("SELECT [sCode], [sName] FROM " . $this->tableBsArea. " WHERE bValid = 1 ORDER BY [id] ASC");  //dao->getAll(sql语句)	
	}
	//获取所有礼品供查询使用
	public function get_giftList(){
		return $this->dao->getAll("SELECT [giftCode], [giftName] FROM " . $this->tableGift. " ORDER BY [serial] DESC");  //dao->getAll(sql语句)	
	}
	
	public function get_orderDownList(){
		session_start();
		return $this->dao->getAll($_SESSION['order_sql']);
	}
	
	

}