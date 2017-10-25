<?php
class TestMobileModel extends Model {
	public $dao = null;
	public $user = '';
	public function __construct() {
		$this->dao = Dao::getDao ();
		if (class_exists ( 'User', false )) {
			$operator = User::getLoginUser ();
			if ($operator)
				$this->userKey = $operator->getUserKey ();
		}
		$userInfo = $operator->getInformation();
		$this->user = $userInfo['user_name'];
	}
	//当前帐号
	public function getUser(){
		$operator = User::getLoginUser ();
		$userInfo = $operator->getInformation();
		return $userInfo['user_name'];
	}//当前用户名
	public function getRealName(){
		$operator = User::getLoginUser ();
		$userInfo = $operator->getInformation();
		return $userInfo['real_name'];
	}
	//我的申请
	public function getMyApply(){
		$sql = "select a.*,b.sName from NCS_EAP_applyList a left join NCS_EAP_mobileType b on a.mobileid = b.id where a.applyName = '".$this->user."/".$this->getRealName()."'";
		$list = $this->dao->getAll($sql);
		return $list;
	}
	//添加申请
	public function addApply($arr){
		if ($arr){
			$sql = "insert into NCS_EAP_applyList(mobileid,createTime,startTime,endTime,contents,applyName) values(".$arr['mobile'].",".time().",".$arr['startTime'].",".$arr['endTime'].",'".$arr['contents']."','".$this->user.'/'.$this->getRealName()."')";
			$result = $this->dao->execute($sql);
			if($result){
				return true;
			}
		}
	}
	//修改状态
	//return 0手机不存在，1正确，2修改错误
	public function updateMarking($id,$val){
		$r = $this->getOneRecord('NCS_EAP_applyList',array('id'=>$id),'mobileid');
		$arr = array();
		if ($r['mobileid'] > 0){
			$m = $this->getOneRecord('NCS_EAP_mobileType',array('id'=>$r['mobileid']));
			if ($m && ( ($m['useid'] == null && $val == 1) || $val == 2) ){
				$status = true;
				$this->dao->begin();
				$sqllist = "update NCS_EAP_applyList set markingStatus = $val,markingTime = ".time()." where id = ".$id;
				$r1 = $this->dao->execute($sqllist);
				if (false !== $r1){
					if ($val == 1){
						$sqlMobile = "update NCS_EAP_mobileType set useStatus = 1,useid = $id where id = ".$r['mobileid'];
						$r2 = $this->dao->execute($sqlMobile);
						if ($r2 == false){
							$status = false;
						}
					}
				}else{
					$status = false;
				}
				if ($status === true){
					$arr['status'] = 1;
					$arr['msg'] = '修改成功';
					$this->dao->commit();
				}else{
					$arr['status'] = 0;
					$arr['msg'] = '修改失败';
					$this->dao->rollback();
				}
			}else{
				$arr['status'] = 0;
				$arr['msg'] = '手机已经有人在使用';
			}
		}else{
			$arr['status'] = 0;
			$arr['msg'] = '该手机不存在';
		}
		return $arr;
		
	}
	public function updateGoBack($id,$val){
		$list = $this->getOneRecord('NCS_EAP_applyList',array('id'=>$id),'mobileid,markingStatus');
		if ($list['mobileid'] && $list['markingstatus'] == 1){
			$mobile = $this->getOneRecord('NCS_EAP_mobileType',array('id'=>$list['mobileid']));
			if ($mobile['useid'] == $id || ($val == 0 && $mobile['useid'] == null)){
				if ($val == 1){
					$sql1 = "update NCS_EAP_applyList set gobackStatus = 1,gobackTime = ".time()." where id = ".$id;
					$sql2 = "update NCS_EAP_mobileType set useStatus = 0,useid = null where id = ".$list['mobileid'];
				}else{
					$sql1 = "update NCS_EAP_applyList set gobackStatus = 0,gobackTime = null where id = ".$id;
					$sql2 = "update NCS_EAP_mobileType set useStatus = 1,useid = ".$id." where id = ".$list['mobileid'];
				}
				$this->dao->begin();
				$r1 = $this->dao->execute($sql1);
				$r2 = $this->dao->execute($sql2);
				if ($r1 == false || $r2 == false){
					$this->dao->rollback();
					return 0;
				}else{
					$this->dao->commit();
					return 1;
				}
			}
		}
	}
	public function getMobileList(){
		$sql = "select * from NCS_EAP_mobileType a left join NCS_EAP_applyList b on a.useid = b.id";
		return $this->dao->getAll($sql);
	}
	//所有申请
	public function getAllApply(){
		$sql = "select a.*,b.sName from NCS_EAP_applyList a left join NCS_EAP_mobileType b on a.mobileid = b.id where applyStatus = 1 order by a.id desc ";
		$list = $this->dao->getAll($sql);
		return $list;
	}
	
	//获取分页总数
	public function countQRI($condition){
		$strQuery = "SELECT count(1) FROM NCS_EAP_applyList WHERE 1=1 ".$condition;
		return $this->dao->getOne ( $strQuery );
	}
	//每页内容
	public function getQRI($condition='', $currentPage=1, $pageSize=10) {
		$sql = "select a.*,b.sName from NCS_EAP_applyList a left join NCS_EAP_mobileType b on a.mobileid = b.id where 1=1 and applyStatus = 1";
		$order ='ORDER BY id DESC';
		$fun = 'countQRI'; //调用countQRI函数
		return $this->getPage($sql,$order,$condition,$fun,$currentPage,$pageSize);
	}
	//分页显示
	public function getPage($sql,$order='',$condition='',$fun,$currentPage=1,$pageSize=10){
		$count = $this->$fun($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strSql = $sql;
		if($condition){
			$strSql = $sql.$condition;
		}
		return $this->dao->getLimit($strSql, $currentPage, $pageSize, $order);
	}
//查询一个表的单条记录
   	public function getOneRecord($table,$where=array(),$column='*'){
   		if ($where){
   			$w = '';
   			$index = 1;
   			foreach ($where as $k=>$v){
   				if ($index == 1){
   					$w = " $k = '".$v."'";
   				}else{
   					$w .= " AND $k = '".$v."'";
   				}
   				$index++;
   			}
   			$sql = "select  $column from $table where $w";
   		}else{
   			$sql = "select  $column from $table";
   		}
   		//echo $sql.'<br>';
   		$result = $this->dao->getRow($sql);
   		if ($result){
   			return $result;
   		}else{
   			return false;
   		}
   	}
   	
   	//查询一个表的多条记录
   	public function getAllRecord($table,$where=array(),$column='*',$limit='',$order=''){
   		if ($limit){
   			$limit = "top ".intval($limit);
   		}
   		if ($order){
   			$order = 'order by '.$order;
   		}
   		if ($where){
   			$w = '';
   			$index = 1;
   			foreach ($where as $k=>$v){
   				if ($index == 1){
   					$w = "$k = '".$v."'";
   				}else{
   					$w = " AND $k = '".$v."'";
   				}
   				$index++;
   			}
   			$sql = "select $limit $column from $table where $w $order";
   		}else{
   			$sql = "select $limit $column from $table $order";
   		}
   		$result = $this->dao->getAll($sql);
   		if ($result){
   			return $result;
   		}else{
   			return false;
   		}
   	}
   	//更新一张表数据
   	public function updateRecord($table,$s,$w){
   		if (empty($table) || empty($w) || !is_array($w) || empty($s) || !is_array($s)){
   			return false;exit;
   		}
   		$i = 0;
   		foreach ($w as $k=>$v){
   			if ($i==0){
   				$where = " $k = '".$v."'";
   			}else{
   				$where .= " AND $k = '".$v."'";
   			}
   			$i++;
   		}
   		$m = 0;
   		foreach ($s as $kk=>$vv){
   			if ($m==0){
   				$set = " $kk = '".$vv."'";
   			}else{
   				$set .= ",$kk = '".$vv."'";
   			}
   			$m++;
   		}
   		 
   		$sql = "update $table SET $set WHERE $where";
   		$result = $this->dao->execute($sql);
   		if ($result) {
   			return true;
   		}else{
   			return false;
   		}
   	}
}
?>