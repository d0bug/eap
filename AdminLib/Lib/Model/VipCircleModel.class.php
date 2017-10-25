<?php

class VipCircleModel extends Model {
	public  $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao('MSSQL_APP');
		$this->Vip_Circle = 'Vip_Circle';//圈子表
		$this->Vip_Comment = 'Vip_Comment';//回复表
		$this->Vip_Complain = 'Vip_Complain';//投拆表
        //====== sofia
        $this->dao2 = Dao::getDao('MYSQL_CONN_KNOWLEDGE');//250
        $this->vip_complain_reply ='vip_complain_reply';//回复投诉表
        $this->vip_push_message ='vip_push_message';//推送消息表
        
	}
    
	/*public function insert_Circle($title,$uid,$uname,$ip,$grade){
		$time = time();
		$this->dao->execute("INSERT INTO ".$this->Vip_Circle." (title,uid,uname,grade,ip,instime) VALUES('$title',$uid,'$uname','$grade','$ip','$time')");
		if($this->dao->affectRows()){
			return true;
		}

	}*/

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


	public function get_CircleList($condition='', $currentPage=1, $pageSize=20){
		$count = $this->get_CircleCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT  *  FROM ' . $this->Vip_Circle . ' WHERE 1=1 ';
		if($condition) {
			$strQuery .= $condition;
		}
		//echo $strQuery;
		$order = ' ORDER BY is_top DESC,is_recommend DESC,instime DESC';

		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}

	public function get_CircleCount($condition='') {
		$strQuery = 'SELECT id FROM ' . $this->Vip_Circle . ' WHERE 1=1 AND status = 1';
		if ($condition) {
			$strQuery .=  $condition;
		}
		//echo $strQuery;
		$arr = $this->dao->getAll($strQuery);
		return count($arr);
	}

	public function get_CircleInfo($id){
		if(!empty($id))
			return $this->dao->getRow("SELECT * FROM ".$this->Vip_Circle." WHERE id = '$id'");
		else
			 return false;
	}

	public function get_oneCircle($id){
		if(!empty($id))
			return $this->dao->getOne("SELECT title FROM ".$this->Vip_Circle." WHERE id = '$id'");
		else
			 return false;
	}
    

	public function editCircle($arr){
		$arr['instime'] = time();
		$arr['ip'] = $this->get_client_ip();
		$arr['content'] = addslashes($arr['content']);
		//echo 'UPDATE '.$this->Vip_Circle.' SET title='.$this->dao->quote(SysUtil::safeString($arr['title'])).',intro='.$this->dao->quote(SysUtil::safeString($arr['content'])).',uid='.$this->dao->quote(abs($arr['uid'])).',uname='.$this->dao->quote(SysUtil::safeString($arr['username'])).',instime='.$this->dao->quote(abs($arr['instime'])).',ip='.$this->dao->quote($arr['ip']).',is_top='.$this->dao->quote(abs($arr['is_top'])).',is_recommend='.$this->dao->quote(abs($arr['contain_module'])).',status='.$this->dao->quote(abs($arr['is_status'])).' WHERE id= '.$this->dao->quote(abs($arr['id']));exit;
		$this->dao->execute('UPDATE '.$this->Vip_Circle.' SET title='.$this->dao->quote(SysUtil::safeString($arr['title'])).',intro='.$this->dao->quote(SysUtil::safeString($arr['content'])).',uid='.$this->dao->quote($arr['uid']).',uname='.$this->dao->quote(SysUtil::safeString($arr['username'])).',instime='.$this->dao->quote(abs($arr['instime'])).',ip='.$this->dao->quote($arr['ip']).',is_top='.$this->dao->quote(abs($arr['is_top'])).',is_recommend='.$this->dao->quote(abs($arr['is_recommend'])).',status='.$this->dao->quote(abs($arr['is_status'])).' WHERE id= '.$this->dao->quote(abs($arr['id'])));
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}

	public function addCircle($arr){
		$arr['instime'] = time();
		$arr['ip'] = $this->get_client_ip();
		$arr['is_top'] = isset($arr['is_top']) ? $arr['is_top'] :0;
		$arr['is_recommend'] = isset($arr['is_recommend']) ? $arr['is_recommend'] :0;
		$arr['is_status'] = isset($arr['status']) ? $arr['status'] :1;
		$arr['content'] = addslashes($arr['content']);
		//print_R($arr);exit;
		//echo 'INSERT INTO '.$this->Vip_Circle.'(title,intro,uid,uname,instime,ip,is_top,is_recommend,status) VALUES('.$this->dao->quote($arr['title']).','.$this->dao->quote($arr['content']).','.$this->dao->quote(abs($arr['uid'])).','.$this->dao->quote(SysUtil::safeString($arr['username'])).','.$this->dao->quote(abs($arr['instime'])).','.$this->dao->quote($arr['ip']).','.$this->dao->quote(abs($arr['is_top'])).','.$this->dao->quote(abs($arr['is_recommend'])).','.$this->dao->quote(abs($arr['is_status'])).')';exit;
		$this->dao->execute('INSERT INTO '.$this->Vip_Circle.'(title,intro,uid,uname,instime,ip,is_top,is_recommend,status) VALUES('.$this->dao->quote(SysUtil::safeString($arr['title'])).','.$this->dao->quote(SysUtil::safeString($arr['content'])).','.$this->dao->quote($arr['uid']).','.$this->dao->quote(SysUtil::safeString($arr['username'])).','.$this->dao->quote(abs($arr['instime'])).','.$this->dao->quote($arr['ip']).','.$this->dao->quote(abs($arr['is_top'])).','.$this->dao->quote(abs($arr['is_recommend'])).','.$this->dao->quote(abs($arr['is_status'])).')');
		if($this->dao->affectRows()){
			return true;
		}else
			return false;
	}

	public function update_Circle($Circleid){
		$this->dao->execute('UPDATE '.$this->Vip_Circle.' SET is_comment = 1 WHERE id='.$this->dao->quote($Circleid));
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}

	public function update_IncNum($Circleid){
		$this->dao->execute('UPDATE '.$this->Vip_Circle.' SET reading_num = reading_num + 1 WHERE id='.$this->dao->quote($Circleid));
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}

   public function setMyCircleStatus($CircleId,$status,$is_top,$is_recommend){
   	//echo 'UPDATE '.$this->Vip_Circle.' SET status = '.$status.' WHERE id='.$this->dao->quote($CircleId);exit;
		$this->dao->execute('UPDATE '.$this->Vip_Circle.' SET status = '.$status.',is_top= '.$this->dao->quote($is_top).',is_recommend = '.$this->dao->quote($is_recommend).' WHERE id='.$this->dao->quote($CircleId));
		if($this->dao->affectRows()){
			return true;
		}
		return false;
   }



	//插入评论
	public function insert_comment($cid,$content,$uid,$uname){
		$time = time();
		$ip = $this->get_client_ip();
		$content = strip_tags($content);
		$sql ="INSERT INTO ".$this->Vip_Comment."(circle_id,content,uid,uname,ip,instime) VALUES('$cid','$content','$uid','$uname','$ip','$time')";
		$this->dao->execute($sql);
		if($this->dao->affectRows()){
			return true;
		}
	}
	//获取评论的内容
	public function get_comment($condition='', $currentPage=1, $pageSize=20){

		$count = $this->get_commentCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT  *  FROM ' . $this->Vip_Comment . ' WHERE 1=1 ';
		if($condition) {
			$strQuery .= $condition;
		}
		$order = ' ORDER BY instime DESC';
		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}

	public function get_commentCount($condition='') {
		$strQuery = 'SELECT id FROM ' . $this->Vip_Comment . ' WHERE 1=1 ';
		if ($condition) {
			$strQuery .=  $condition;
		}
		$arr = $this->dao->getAll($strQuery);
		return count($arr);
	}

	//删除圈子且圈子对应的评论
	public function Del_circle($cid){
		$flag = true;
		//$this->dao->execute ( 'begin' ); // 事务开启
		if (! empty ( $cid )) {
			$sql = 'DELETE FROM ' . $this->Vip_Circle . ' WHERE id = ' . $this->dao->quote ( $cid );
			$sql2 = 'DELETE FROM ' . $this->Vip_Comment . ' WHERE circle_id = ' . $this->dao->quote ( $cid );
			if ($this->dao->execute ( $sql ) && $this->dao->execute ( $sql2 ))
				$flag = true;
			else
				$flag = false;
		}else{
			$flag = false;
		}
		return $flag;
	}

	//删除圈子对应的评论
	public function Del_circlereply($rid){
		$flag = true;
		if (! empty ( $rid )) {
			$sql = 'DELETE FROM ' . $this->Vip_Comment . ' WHERE id = ' . $this->dao->quote ( $rid );
			if ($this->dao->execute ( $sql ))
				$flag = true;
			else
				$flag = false;
		}else{
			$flag = false;
		}
		return $flag;
	}

	//投拆入库
    public function insert_complaint($uid,$uname,$content){
        $time = time();
        $sql ="INSERT INTO ".$this->Vip_Complain."(uid,uname,content,instime) VALUES('$uid','$uname','$content','$time')";
        $this->dao->execute($sql);
        if($this->dao->affectRows()){
            return true;
        }
    }
    
    //============ sofia
    
    public function get_oneComplain($id){
		if(!empty($id))
			return $this->dao->getOne("SELECT content FROM ".$this->Vip_Complain." WHERE id = '$id'");
		else
			 return false;
	}
    public function get_complainuser($id){
		if(!empty($id))
			return $this->dao->getALL("SELECT * FROM ".$this->Vip_Complain." WHERE id = '$id'");
		else
			 return false;
	}
    //获取评论的内容
	public function get_Complain_comment($condition='', $currentPage=1, $pageSize=20){

		$count = $this->get_ComplainCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT  *  FROM ' . $this->vip_complain_reply . ' WHERE 1=1 ';
        
		if($condition) {
			$strQuery .= $condition;
		}
		$order = ' ORDER BY instime DESC';
		return $this->dao2->getLimit($strQuery, $currentPage, $pageSize, $order);
	}
    public function get_ComplainCount($condition='') {
		$strQuery = 'SELECT id FROM ' . $this->vip_complain_reply . ' WHERE 1=1 ';
        
		if ($condition) {
			$strQuery .=  $condition;
		}
        
		$arr = $this->dao2->getAll($strQuery);
		return count($arr);
	}
    //插入投诉回复
	public function insert_Complain_comment($cid,$ccode,$cname,$ccontent,$uid,$uname,$ucontent){
		$time = time();
		$ip = $this->get_client_ip();
		$content = strip_tags($content);
		$sql ="INSERT INTO ".$this->vip_complain_reply."(complain_id,ccode,cname,ccontent,ucode,uname,ucontent,instime) VALUES('$cid','$ccode','$cname','$ccontent','$uid','$uname','$ucontent','$time')";
        
		$this->dao2->execute($sql);
		if($this->dao2->affectRows()){
			return true;
		}
	}
    //更新已评论
    public function update_Complain($Circleid){
		$this->dao->execute('UPDATE '.$this->Vip_Complain.' SET is_comment = 1 WHERE id='.$this->dao->quote($Circleid));
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}
    //增加投诉回复的推送内容
    public function add_push_complain($cid){
        $getcom ="select * from ".$this->vip_complain_reply." where complain_id='".$cid."' and status =1 order by id desc limit 1";
        $arr = $this->dao2->getAll($getcom);   
        $time = date("Y-m-d H:i:s",time());     
        $message="亲爱的家长您好，针对您对我们提出的意见： ".$arr[0]['ccontent']." ，我们已经做出答复啦,回复内容为： ".$arr[0]['ucontent']." ，感谢您给出的宝贵意见，我们将会继续努力！";
        $strQuery="insert into ".$this->vip_push_message." (ucode,pu_time,pu_message,pu_gx,create_time) values('".$arr[0]['ccode']."','".$arr[0]['instime']."','".$message."',3,'".$time."')";
		$this->dao2->execute($strQuery);
		if($this->dao2->affectRows()){
			return true;
		}
        
    }
    
    //============ s-end
    
    
	public function get_ComplaintList($condition='', $currentPage=1, $pageSize=20){
		$count = $this->get_ComplaintCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT  *  FROM ' . $this->Vip_Complain . ' WHERE 1=1 ';
		if($condition) {
			$strQuery .= $condition;
		}

		$order = ' ORDER BY instime DESC';

		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}

	public function get_ComplaintCount($condition='') {
		$strQuery = 'SELECT id FROM ' . $this->Vip_Complain . ' WHERE 1=1 ';
		if ($condition) {
			$strQuery .=  $condition;
		}
		$arr = $this->dao->getAll($strQuery);
		return count($arr);
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
