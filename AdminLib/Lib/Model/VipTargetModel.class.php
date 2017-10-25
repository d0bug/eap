<?php
class VipTargetModel extends Model
{
	public function __construct()
	{
		$this->dao=Dao::getDao();

		$this->vp_user_subjects="vp_user_subjects";
		$this->vp_subject="vp_subject";
		$this->sys_roles="sys_roles";
		$this->sys_user_roles="sys_user_roles";
		$this->sys_users="sys_users";

		$this->dao2=Dao::getDao('MYSQL_CONN_KNOWLEDGE');
		$this->vip_target="vip_target";
		$this->vip_lecture_archive="vip_lecture_archive";

		$this->atf_users="atf_users";
		$this->atf_video="atf_video";
		$this->atf_roles="atf_roles";
		$this->atf_user_roles="atf_user_roles";
	}

	/**
	 * 取得所有状态为1的教研员列表
	 * @param  [type]  $date        [description]
	 * @param  integer $currentPage [description]
	 * @param  integer $pageSize    [description]
	 * @return [type]               [description]
	 */
	public function getTargetStausList($date,$user_realname='',$currentPage=1,$pageSize=20)
	{
		$strQuery="select * from ".$this->vip_target." where status = 1";
		if(!empty($user_realname) && !empty($date))
		{
			$strQuery.=" and date='".$date."' and user_realname='".$user_realname."'";
		}elseif(!empty($user_realname) && empty($date)){
			$strQuery.=" and user_realname='".$user_realname."'";
		}elseif(empty($user_realname) && !empty($date)){
			$strQuery.=" and date='".$date."'";
		}else
		{
			$strQuery.=" and date='".date('Y-m')."'";
		}

		$strQuery.=" order by id desc";
		$list=$this->dao2->getLimit($strQuery,$currentPage,$pageSize);
		return $list;
	}

	/**
	 * 取得状态为1的教研员所有数据
	 * @param  [type] $date [description]
	 * @return [type]       [description]
	 */
	public function getTargetAll($date)
	{
		$strQuery="select * from ".$this->vip_target." where status = 1";
		if (!empty($date)) {
			$strQuery.=" and date='".$date."'";
		}else
		{
			$strQuery.=" and date='".date('Y-m')."'";
		}

		return $this->dao2->getAll($strQuery);
	}

	/**
	 * 取得当前时间所有教研员数据
	 * @param  [type] $date [description]
	 * @return [type]       [description]
	 */
	public function getTargetParamsAll($date)
	{
		$strQuery="select user_key,target,status from ".$this->vip_target;
		if(!empty($date))
		{
			$strQuery.=" where date='".$date."'";
		}else{
			$strQuery.=" where date='".date('Y-m')."'";
		}
		$list=$this->dao2->getAll($strQuery);
		$data=array();
		foreach ($list as $key => $value) {
			$data[$value['user_key']]['target']=$value['target'];
			$data[$value['user_key']]['status']=$value['status'];
		}

		return $data;
	}


	/**
	 * 获取总条数
	 * @param  [type] $date [description]
	 * @return [type]       [description]
	 */
	public function getTargetStatuscount($date,$user_realname)
	{
		$strQuery="select count(*) as rownum from ".$this->vip_target." where status = 1";
		if(!empty($user_realname) && !empty($date))
		{
			$strQuery.=" and date='".$date."' and user_realname='".$user_realname."'";
		}elseif(!empty($user_realname) && empty($date)){
			$strQuery.=" and user_realname='".$user_realname."'";
		}elseif(empty($user_realname) && !empty($date)){
			$strQuery.=" and date='".$date."'";
		}else
		{
			$strQuery.=" and date='".date('Y-m')."'";
		}

		return $this->dao2->getOne($strQuery);
	}


	/**
	 * 统计指定月说导视频
	 * @param  [type] $date [description]
	 * @return [type]       [description]
	 */
	public function getAtfVideoNum($date,$user_email_str)
	{
		$strQuery="select user_name from ".$this->atf_users." where user_email in (".$user_email_str.")";
		$userList=$this->dao2->getAll($strQuery);
		if(empty($userList))
		{
			$count=0;
		}else{
		$str='';
		foreach ($userList as $key => $value) {;
			$str.="'".$value['user_name']."',";
		}
		$str=trim($str,',');	
		if(empty($date))
		{
			$date=date('Y-m');
		}
		$strQuery2="select count(*) as num,b.user_name,b.user_email from ".$this->atf_video." as a inner join ".$this->atf_users." as b on b.user_name=a.update_user inner join ".$this->atf_user_roles." as c on c.uid=b.uid inner join ".$this->atf_roles." as d on d.role_id=c.role_id where  FROM_UNIXTIME(a.create_time,'%Y-%m')='".$date."' and a.update_user in (".$str.") and d.role_caption='课程库专员' group by a.update_user";
		$videoList=$this->dao2->getAll($strQuery2);
		$strQuery3="select user_key,user_email from ".$this->vip_target." group by user_key";
		$targetAll=$this->dao2->getAll($strQuery3);
		$targetArr=array();
		foreach ($targetAll as $key => $value) {
			$targetArr[$value['user_email']]=$value['user_key'];
		}
		$count=array();
		foreach ($videoList as $key => $value) {
			$count[$value['user_email']]['num']=$value['num'];
			$count[$value['user_email']]['user_key']=$targetArr[$value['user_email']];
		}
		}
		/*$strQuery="select count(*) as num,c.user_key from ".$this->atf_video." as a inner join ".$this->atf_users." as b on a.update_user=b.user_name inner join ".$this->vip_target." as c on b.user_email=c.user_email where FROM_UNIXTIME(create_time,'%Y-%m')='".$date."' and  b.user_email in (".$user_email_str.") group by c.user_key";
		$count=$this->dao2->getAll($strQuery);*/
		return $count;


	}

	/**
	 * 统计指定月的说课或者导课视频数
	 * @param  [type]  $date [description]
	 * @param  integer $type [description]
	 * @return [type]        [description]
	 */
	public function getAtfVideoTypeNum($dateTime,$type=0,$user_email_str)
	{
		$strQuery="select user_name from ".$this->atf_users." where user_email in (".$user_email_str.")";
		$userList=$this->dao2->getAll($strQuery);
		if(empty($userList))
		{
			$count=0;
		}else{
		$str='';
		foreach ($userList as $key => $value) {;
			$str.="'".$value['user_name']."',";
		}
		$str=trim($str,',');	
		if (empty($dateTime)) {
			$dateTime=date('Y-m-d');
		}
		$strQuery2="select count(*) as num,b.user_name,b.user_email from ".$this->atf_video." as a inner join ".$this->atf_users." as b on b.user_name=a.update_user inner join ".$this->atf_user_roles." as c on c.uid=b.uid inner join ".$this->atf_roles." as d on d.role_id=c.role_id  where  FROM_UNIXTIME(a.create_time,'%Y-%m-%d')='".$dateTime."' and a.type=".$type." and a.update_user in (".$str.") and d.role_caption='课程库专员' group by a.update_user";
		$videoList=$this->dao2->getAll($strQuery2);
		$strQuery3="select user_key,user_email from ".$this->vip_target." group by user_key";
		$targetAll=$this->dao2->getAll($strQuery3);
		$targetArr=array();
		foreach ($targetAll as $key => $value) {
			$targetArr[$value['user_email']]=$value['user_key'];
		}
		$count=array();
		foreach ($videoList as $key => $value) {
			$count[$value['user_email']]['num']=$value['num'];
			$count[$value['user_email']]['user_key']=$targetArr[$value['user_email']];
		}
		}
		/*var_dump($userLista);exit;
		$strQuery="select count(*) as num,c.user_key from ".$this->atf_video." as a inner join ".$this->atf_users." as b on a.update_user=b.user_name inner join ".$this->vip_target." as c on b.user_email=c.user_email where FROM_UNIXTIME(a.create_time,'%Y-%m-%d')='".$dateTime."' and a.type=".$type." and  b.user_email in (".$user_email_str.") group by c.user_key";
		$count=$this->dao2->getAll($strQuery);*/
		return $count;
	}

	/**
	 * 统计当日新搭建讲义
	 * @param  [type] $dateTime       [description]
	 * @param  [type] $user_email_str [description]
	 * @return [type]                 [description]
	 */
	public function getLectureArchive($dateTime,$user_email_str)
	{
		$strQuery="select user_name from ".$this->atf_users." where user_email in (".$user_email_str.")";
		$userList=$this->dao2->getAll($strQuery);
		if(empty($userList))
		{
			$count=0;
		}else{
		$str='';
		foreach ($userList as $key => $value) {;
			$str.="'".$value['user_name']."',";
		}
		$str=trim($str,',');	
		if(empty($dateTime))
		{
			$dateTime=date('Y-m-d');
		}
		$strQuery2="select count(*) as num,b.user_name,b.user_email from ".$this->vip_lecture_archive." as a inner join ".$this->atf_users." as b on b.user_name=a.user_name inner join ".$this->atf_user_roles." as c on c.uid=b.uid inner join ".$this->atf_roles." as d on d.role_id=c.role_id  where  FROM_UNIXTIME(a.created_time,'%Y-%m-%d')='".$dateTime."' and a.user_name in (".$str.") and d.role_caption='课程库专员' group by a.user_name";
		$videoList=$this->dao2->getAll($strQuery2);
		$strQuery3="select user_key,user_email from ".$this->vip_target." group by user_key";
		$targetAll=$this->dao2->getAll($strQuery3);
		$targetArr=array();
		foreach ($targetAll as $key => $value) {
			$targetArr[$value['user_email']]=$value['user_key'];
		}
		$count=array();
		foreach ($videoList as $key => $value) {
			$count[$value['user_email']]['num']=$value['num'];
			$count[$value['user_email']]['user_key']=$targetArr[$value['user_email']];
		}
		}
		/*$strQuery="select count(*) as num,c.user_key from ".$this->vip_lecture_archive." as a inner join ".$this->atf_users." as b on a.user_name=b.user_name  inner join ".$this->vip_target." as c on b.user_email=c.user_email where FROM_UNIXTIME(created_time,'%Y-%m-%d')='".$dateTime."' and b.user_email in (".$user_email_str.") group by c.user_key";*/
		return $count;
	}

	/**
	 * 执行添加编辑操作
	 * @param [type] $data [description]
	 */
	public function addTarget($data)
	{
		if(!empty($data)){
		$this->dao2->execute ( 'begin' );
		foreach ($data as $key => $value) {
			if(!empty($value['username']) && !empty($value['user_key']) && !empty($value['user_realname'])){
			//查询用户key和当前月是否存在记录，如果存在则执行更新操作，如果不存在进行添加操作
			$strQuery="select * from ".$this->vip_target." where user_key='".$value['user_key']."' and date='".$value['date']."'";

			$findInfo=$this->dao2->getOne($strQuery);
			//如果存在进行编辑操作
			if ($findInfo) {
				$updateQuery="update ".$this->vip_target." set target='".$value['target']."',status='".$value['status']."',user_email='".$value['user_email']."' where user_key='".$value['user_key']."' and date='".$value['date']."'";
				$queryResult=$this->dao2->execute($updateQuery);
			}else{
				$inserQuery="insert into ".$this->vip_target." (user_key,user_email,username,user_realname,target,status,date) values ('".$value['user_key']."','".$value['user_email']."','".$value['user_name']."','".$value['user_realname']."','".$value['target']."','".$value['status']."','".$value['date']."')";
				$queryResult=$this->dao2->execute($inserQuery);
			}
			}
		}
		if ($queryResult !== false) {
			$this->dao2->execute ('commit');
			return true;
		}else
		{
			$this->dao2->execute('rollback');
			return false;
		}
		}
		return false;
	}
}

 ?>