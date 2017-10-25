<?php

class VipAddressModel extends Model {
	public  $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao('MSSQL_APP');		
        //====== sofia
        $this->dao2 = Dao::getDao('MYSQL_CONN_KNOWLEDGE');//250
        $this->vip_exchange_record ='vip_exchange_record';//兑换记录表
        $this->vip_exchange_address ='vip_exchange_address';//收货地址表
        $this->vip_month_evaluation = 'vip_month_evaluation';//月评价
        $this->vip_recommended = 'vip_recommended';//推荐学员
        $this->vip_user_visit_count = 'vip_user_visit_count';//学员统计数据
        $this->vip_visit_json = 'vip_visit_json';//学员统计每月json
        $this->vip_service_review = 'vip_service_review';//服务点评
	}
  
	public function get_client_ip()
	{
		if ($_SERVER['REMOTE_ADDR']) {
			$cip = $_SERVER['REMOTE_ADDR'];
		} elseif (getenv("REMOTE_ADDR")) {
			$cip = getenv("REMOTE_ADDR");
		} elseif (getenv("HTTP_CLIENT_IP")) {
			$cip = getenv("HTTP_CLIENT_IP");
		} else {
			$cip = "unknown";
		}
		return $cip;
	}

    public function get_AddressList($condition='', $currentPage=1, $pageSize=20){
        $count = $this->get_AddressCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT  *  FROM ' . $this->vip_exchange_record . ' WHERE 1=1 ';
		if($condition) {
			$strQuery .= $condition." ";
		}
		$order = ' ORDER BY id DESC';
		return $this->dao2->getLimit($strQuery, $currentPage, $pageSize, $order); 
    }
    
    public function get_AddressInfo($addressId){
         return $this->dao2->getRow("select * from ".$this->vip_exchange_address." where id = ".$addressId);
    }   
     public function getAllRecommended(){
        return $this->dao2->getAll("select * from ".$this->vip_recommended." where status = 1 ");
    }
    
    public function getAllEvaluation(){
        return $this->dao2->getAll("select * from ".$this->vip_month_evaluation." where status = 1 ");
    }
    
    public function get_AddressCount($condition='') {
		$strQuery = 'SELECT id FROM ' . $this->vip_exchange_record . ' WHERE 1=1 ';
		if ($condition) {
			$strQuery .=  $condition;
		}
		$arr = $this->dao2->getAll($strQuery);
		return count($arr);
	}

    public function get_record_one($id){
        if(!empty($id)){
			return $this->dao2->getAll("SELECT * FROM ".$this->vip_exchange_record." WHERE id = '$id'");
        }else{
			 return false;
        }
    }
    
    public function edit_Address_one($id,$delivery_status,$time,$danhao,$newup,$newname,$newphone,$newadress){   
        if($newup == 2){//不修改
            $strQuery = "update ".$this->vip_exchange_record." set delivery_status =".$delivery_status.", de_time ='".$time."',logistics = '".$danhao."' where id = ".$id;    
        }
        if($newup == 1){//修改
            $strQuery = "update ".$this->vip_exchange_record." set delivery_status =".$delivery_status.", de_time ='".$time."',logistics = '".$danhao."',newup = 1,new_name ='".$newname."',new_phone='".$newphone."',new_address= '".$newadress."' where id = ".$id;
        }
                
        $success = (boolean)$this->dao2->execute($strQuery);
        if($success == false){
				$this->dao2->rollback();
				return false;
	   }else{
			   return true;
	   }
    }
    
    public function get_address($addressid){
        if(!empty($addressid)){
			return $this->dao2->getAll("SELECT * FROM ".$this->vip_exchange_address." WHERE id = '$addressid'");
        }else{
			 return false;
        }
    }
    
    public function get_EvaluationList($condition='', $currentPage=1, $pageSize=20){
        $count = $this->get_EvaluationCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT  *  FROM ' . $this->vip_month_evaluation . ' WHERE 1=1 ';
		if($condition) {
			$strQuery .= $condition." ";
		}

		$order = ' ORDER BY id DESC';       
		return $this->dao2->getLimit($strQuery, $currentPage, $pageSize, $order);
    }
    
    public function get_EvaluationCount($condition='') {
		$strQuery = 'SELECT id FROM ' . $this->vip_month_evaluation . ' WHERE 1=1 ';
		if ($condition) {
			$strQuery .=  $condition;
		}
		$arr = $this->dao2->getAll($strQuery);
		return count($arr);
	}
    
    public function getEvaluationInfo($eid){
        return $this->dao2->getRow("select * from ".$this->vip_month_evaluation." where id = ".$eid." and status = 1");
    }
    
    public function get_RecommendedList($condition='', $currentPage=1, $pageSize=20){
        $count = $this->get_RecommendedCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT  *  FROM ' . $this->vip_recommended . ' WHERE 1=1 ';
		if($condition) {
			$strQuery .= $condition." ";
		}

		$order = ' ORDER BY id DESC';

		return $this->dao2->getLimit($strQuery, $currentPage, $pageSize, $order);
    }
    
    public function get_RecommendedCount($condition='') {
		$strQuery = 'SELECT id FROM ' . $this->vip_recommended . ' WHERE 1=1 ';
		if ($condition) {
			$strQuery .=  $condition;
		}
		$arr = $this->dao2->getAll($strQuery);
		return count($arr);
	}
    
    public function getVisitCountList($condition='', $currentPage=1, $pageSize=3){
		$count = $this->getVisitCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT * FROM ' . $this->vip_user_visit_count . ' WHERE 1=1' ;
		if($condition) {
			$strQuery .= $condition." ";
		}
		$order = ' ORDER BY id DESC';
        
		return $this->dao2->getLimit($strQuery, $currentPage, $pageSize, $order);
	}
	public function getVisitCount($condition=''){
		$strQuery = 'SELECT id FROM ' . $this->vip_user_visit_count . ' WHERE 1=1 ';
		if ($condition) {
			$strQuery .=  $condition;
		}
		$arr = $this->dao2->getAll($strQuery);
		return count($arr);
	}
	public function getAllVisitList($condition='', $currentPage=1, $pageSize=3){
        $count = $this->getVisitCount($condition);
        $pageCount = ceil($count / $pageSize);
        if($currentPage > $pageCount) $currentPage = $pageCount;
        if($currentPage < 1) $currentPage = 1;
        $strQuery = 'SELECT * FROM ' . $this->vip_visit_json . ' WHERE 1=1 and status = 1 ' ;
        if($condition) {
            $strQuery .= $condition." ";
        }
        $order = ' ORDER BY id DESC';

        return $this->dao2->getLimit($strQuery, $currentPage, $pageSize, $order);
	}
    public function getAllVisitCount($condition=''){
        $strQuery = 'SELECT id FROM ' . $this->vip_visit_json . ' WHERE 1=1 ';
        if ($condition) {
            $strQuery .=  $condition;
        }
        $arr = $this->dao2->getAll($strQuery);
        return count($arr);
    }
    public function get_visit_json($visitJsonId){
        return $this->dao2->getRow("select * from ".$this->vip_visit_json." where id = ".$visitJsonId." and status = 1 ");
    }

    public function export_visit_json($Bdate,$Edate){
		return $this->dao2->getAll("select * from ".$this->vip_user_visit_count." where create_date >= '".$Bdate."' and create_date <= '".$Edate."' and status =1 order by id desc");
	}

    public function getServiceReviewList($search_value){
		$where ='';
        if($search_value != ''){
            $where .= ' and uname= "'.$search_value.'" ';
        }

        $result = $this->dao2->getAll('select * from '.$this->vip_service_review.' where status =1 '.$where.' order by id desc');

        foreach($result as $key=>$val){
            $result[$key]['chakan'] = '<a onclick="javascript: xiangqing('.$val['id'].')" style="color:blue;" >查看</a>';
        }
        if(!empty($result)){
            return $result;
        }else{
            return false;
        }

	}
    public function getServiceReviewRow($id){
        return $this->dao2->getRow("select * from ".$this->vip_service_review." where id = ".$id." and status =1 ");
    }

	protected function StripHTML($str){
		$str=preg_replace("/\s+/", " ", $str); //过滤多余回车
		$str=preg_replace("/<[ ]+/si","<",$str); //过滤<__("<"号后面带空格)
		$str=preg_replace("/<\!–.*?–>/si","",$str); //注释
		$str=preg_replace("/<(\!.*?)>/si","",$str); //过滤DOCTYPE
		$str=preg_replace("/<(\/?html.*?)>/si","",$str); //过滤html标签
		$str=preg_replace("/<(\/?br.*?)>/si","",$str); //过滤br标签
		$str=preg_replace("/<(\/?head.*?)>/si","",$str); //过滤head标签
		$str=preg_replace("/<(\/?meta.*?)>/si","",$str); //过滤meta标签
		$str=preg_replace("/<(\/?body.*?)>/si","",$str); //过滤body标签
		$str=preg_replace("/<(\/?link.*?)>/si","",$str); //过滤link标签
		$str=preg_replace("/<(\/?form.*?)>/si","",$str); //过滤form标签
		$str=preg_replace("/cookie/si","COOKIE",$str); //过滤COOKIE标签
		$str=preg_replace("/<(applet.*?)>(.*?)<(\/applet.*?)>/si","",$str); //过滤applet标签
		$str=preg_replace("/<(\/?applet.*?)>/si","",$str); //过滤applet标签
		$str=preg_replace("/<(style.*?)>(.*?)<(\/style.*?)>/si","",$str); //过滤style标签
		$str=preg_replace("/<(\/?style.*?)>/si","",$str); //过滤style标签
		$str=preg_replace("/<(title.*?)>(.*?)<(\/title.*?)>/si","",$str); //过滤title标签
		$str=preg_replace("/<(\/?title.*?)>/si","",$str); //过滤title标签
		$str=preg_replace("/<(object.*?)>(.*?)<(\/object.*?)>/si","",$str); //过滤object标签
		$str=preg_replace("/<(\/?objec.*?)>/si","",$str); //过滤object标签
		$str=preg_replace("/<(noframes.*?)>(.*?)<(\/noframes.*?)>/si","",$str); //过滤noframes标签
		$str=preg_replace("/<(\/?noframes.*?)>/si","",$str); //过滤noframes标签
		$str=preg_replace("/<(i?frame.*?)>(.*?)<(\/i?frame.*?)>/si","",$str); //过滤frame标签
		$str=preg_replace("/<(\/?i?frame.*?)>/si","",$str); //过滤frame标签
		$str=preg_replace("/<(script.*?)>(.*?)<(\/script.*?)>/si","",$str); //过滤script标签
		$str=preg_replace("/<(\/?script.*?)>/si","",$str); //过滤script标签
		$str=preg_replace("/javascript/si","Javascript",$str); //过滤script标签
		$str=preg_replace("/vbscript/si","Vbscript",$str); //过滤script标签
		$str=preg_replace("/on([a-z]+)\s*=/si","On\\1=",$str); //过滤script标签
		$str=preg_replace("/&#/si","&＃",$str); //过滤script标签，如javAsCript:alert

		return $str;

	 }
	//过滤掉一些html标签,js代码,css样式标签: 
	protected function uh($str) { 
		$str = preg_replace( "@<script(.*?)</script>@is", "", $str ); 
		$str = preg_replace( "@<iframe(.*?)</iframe>@is", "", $str ); 
		$str = preg_replace( "@<style(.*?)</style>@is", "", $str ); 
		$str = preg_replace( "@<(.*?)>@is", "", $str ); 
	    return $str; 
	} 

	protected function filtersh($str){
		$str=preg_replace("/(\s)alt=[^\s]*/","",$str); 		
		return $str;
	}
}
?>
