<?php

class VipmyaskModel extends Model {
	public  $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao('MSSQL_APP');
		$this->Vip_ask = 'Vip_Ask';
		$this->Vip_reply = 'Vip_Reply';
	}
	public function insert_ask($title,$uid,$uname,$ip,$grade){
		$time = time();
		$title = addslashes($title);
		//echo 'INSERT INTO '.$this->Vip_ask.' (title,uid,uname,grade,ip,instime) VALUES('.$this->dao->quote(SysUtil::safeString($title)).','.$this->dao->quote($uid).','.$this->dao->quote($uname).','.$this->dao->quote(abs($grade)).','.$this->dao->quote($ip).','.$this->dao->quote($time).')';exit;
		$this->dao->execute('INSERT INTO '.$this->Vip_ask.' (title,uid,uname,grade,ip,instime) VALUES('.$this->dao->quote(SysUtil::safeString($title)).','.$this->dao->quote($uid).','.$this->dao->quote($uname).','.$this->dao->quote(abs($grade)).','.$this->dao->quote($ip).','.$this->dao->quote($time).')');
		if($this->dao->affectRows()){
			return true;
		}

	}

	public function insert_reply($askid,$content,$uid,$uname){
		$time = time();
		$ip = $this->get_client_ip();
		$content = strip_tags($content);
		$sql ="INSERT INTO ".$this->Vip_reply."(askid,content,reply_uid,reply_uname,ip,instime) VALUES('$askid','$content','$uid','$uname','$ip','$time')";
		$this->dao->execute($sql);
		if($this->dao->affectRows()){
			return true;
		}
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


	public function get_myaskList($condition='', $currentPage=1, $pageSize=20){
		$count = $this->get_myaskCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT  *  FROM ' . $this->Vip_ask . ' WHERE 1=1 ';
		if($condition) {
			$strQuery .= $condition;
		}

		$order = ' ORDER BY instime DESC,is_reply ASC';

		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}

	public function get_myaskCount($condition='') {
		$strQuery = 'SELECT id FROM ' . $this->Vip_ask . ' WHERE 1=1 ';
		if ($condition) {
			$strQuery .=  $condition;
		}
		$arr = $this->dao->getAll($strQuery);
		return count($arr);
	}

	public function get_askInfo($id){
		if(!empty($id))
			return $this->dao->getRow("SELECT * FROM ".$this->Vip_ask." WHERE id = '$id'");
		else
			 return false;
	}

	public function get_oneInfo($id){
		if(!empty($id))
			return $this->dao->getOne("SELECT title FROM ".$this->Vip_ask." WHERE id = '$id'");
		else
			 return false;
	}

	//获取回复的内容
	public function get_reply($condition='', $currentPage=1, $pageSize=20){

		$count = $this->get_myreplyCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT  *  FROM ' . $this->Vip_reply . ' WHERE 1=1 ';
		if($condition) {
			$strQuery .= $condition;
		}
		$order = ' ORDER BY instime DESC';
		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}

	public function get_myreplyCount($condition='') {
		$strQuery = 'SELECT id FROM ' . $this->Vip_reply . ' WHERE 1=1 ';
		if ($condition) {
			$strQuery .=  $condition;
		}
		$arr = $this->dao->getAll($strQuery);
		return count($arr);
	}

	public function update_askstatus($askid){
		$this->dao->execute('UPDATE '.$this->Vip_ask.' SET is_reply = 1 WHERE id='.$this->dao->quote($askid));
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}

	public function update_replystatus($askid){
		$this->dao->execute('UPDATE '.$this->Vip_ask.' SET is_reply = 0 WHERE id='.$this->dao->quote($askid));
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}


	public function update_IncNum($askid){
		$this->dao->execute('UPDATE '.$this->Vip_ask.' SET visit_num = visit_num + 1 WHERE id='.$this->dao->quote($askid));
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}

   public function setMyaskStatus($askId,$status){
   	//echo 'UPDATE '.$this->Vip_ask.' SET status = '.$status.' WHERE id='.$this->dao->quote($askId);
		$this->dao->execute('UPDATE '.$this->Vip_ask.' SET status = '.$status.' WHERE id='.$this->dao->quote($askId));
		if($this->dao->affectRows()){
			return true;
		}
		return false;
   }



 	//获取一条回复的内容
	public function get_OneReply($id){
		if(!empty($id)){
			$strQuery = 'SELECT  *  FROM ' . $this->Vip_reply . ' WHERE id= '.$this->dao->quote($id);
			return $this->dao->getRow($strQuery);
		}else
			return false;
	}  


	public function update_reply($arr){
		$arr['time'] = time();
		$arr['ip'] = $this->get_client_ip();
		$sql ='UPDATE '.$this->Vip_reply.' SET content ='.$this->dao->quote(SysUtil::safeString($arr['content'])).',reply_uid='.$this->dao->quote($arr['uid']).',reply_uname ='.$this->dao->quote($arr['uname']).',ip='.$this->dao->quote($arr['ip']).',instime ='.$this->dao->quote($arr['time']).',status='.$this->dao->quote($arr['is_status']).' WHERE id='.$this->dao->quote($arr['id']);
		//echo $sql;exit;
		$this->dao->execute($sql);
		if($this->dao->affectRows()){
			return true;
		}
	}

	//删除回复
	public function Del_Reply($rid){
		$flag = true;
		if (! empty ( $rid )) {
			$sql = 'DELETE FROM ' . $this->Vip_reply . ' WHERE id = ' . $this->dao->quote ( $rid );
			if ($this->dao->execute ( $sql ))
				$flag = true;
			else
				$flag = false;
		}else{
			$flag = false;
		}
		return $flag;
	}



	//获取回复的内容
	public function MyAskReplyInfo($condition='', $currentPage=1, $pageSize=20){

		$count = $this->get_MyAskReplyCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery ='SELECT a.title as atitle,r.* FROM ' .$this->Vip_ask .' a LEFT JOIN '.$this->Vip_reply. ' r ON a.id = r.askid WHERE 1 = 1';
		if($condition) {
			$strQuery .= $condition;
		}
		$order = ' ORDER BY instime DESC';
		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}


	public function get_MyAskReplyCount($condition='') {
		$strQuery ='SELECT r.id FROM ' .$this->Vip_ask .' a LEFT JOIN '.$this->Vip_reply. ' r ON a.id = r.askid WHERE 1 = 1';
		if ($condition) {
			$strQuery .=  $condition;
		}
		$arr = $this->dao->getAll($strQuery);
		return count($arr);
	}

	public function get_MyRep($id){
		$str ='SELECT id FROM  '.$this->Vip_reply.' WHERE 1 =1 AND askid= '.$this->dao->quote ( $id );
		return $this->dao->getOne($str);
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
}
?>
