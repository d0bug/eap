<?php

class VpHandoutsModel extends Model {
	public  $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao();
		$this->tableName = 'vp_handouts';
		$this->sysUsers = 'sys_users';
		$this->downloadLog = 'vp_download_log';
		$this->vp_favorite = 'vp_favorite';
		$this->vp_message = 'vp_message';
		$this->vp_publicity = 'vp_publicity';
		$this->vp_subject = 'vp_subject';
		$this->vp_grade = 'vp_grade';
		$this->vp_knowledge = 'vp_knowledge';

		$this->dao2 = Dao::getDao('MYSQL_CONN_KNOWLEDGE');
		$this->vip_lecture_archive = 'vip_lecture_archive';

		$this->vip_dict_subject= "vip_dict_subject";

		$this->vip_dict_course_type = "vip_dict_course_type";
		$this->vip_knowledge= "vip_knowledge";
	}

	public function add_handouts($arr){
		$this->dao->execute("INSERT INTO ".$this->tableName." ( [type],
																[title],
																[picture],
																[sid],
																[gid],
																[kid],
																[nids],
																[introduce],
																[teacher_version],
																[student_version],
																[teacher_version_preview],
																[student_version_preview],
																[instime],
																[updtime],
																[user_key],
																[is_teaching_and_research],
																[IP],
																[is_parttime_visible],
																[status]) 
														VALUES('$arr[type]',".$this->dao->quote($arr['title']).",
																'$arr[picture]',
																'$arr[sid]',
																'$arr[gid]',
																'$arr[kid]',
																'$arr[nids]',
																".$this->dao->quote($arr['introduce']).",
																'$arr[teacher_version]',
																'$arr[student_version]',
																'$arr[teacher_version_preview]',
																'$arr[student_version_preview]',
																'".time()."',
																'',
																'$arr[user_key]',
																'$arr[is_teaching_and_research]',
																'$arr[IP]',
																'$arr[is_parttime_visible]',
																'$arr[status]')");
		if($this->dao->affectRows()){
			return $this->get_last_hid();
		}
		return false;
	}

	public function update_handouts($arr,$hid){
		$this->dao->execute("UPDATE ".$this->tableName." SET [type]='$arr[type]',
															 [title]=".$this->dao->quote($arr['title']).",
															 [picture]='$arr[picture]',
															 [sid]='$arr[sid]',
															 [gid]='$arr[gid]',
															 [kid]='$arr[kid]',
															 [nids]='$arr[nids]',
															 [introduce]=".$this->dao->quote($arr['introduce']).",
															 [teacher_version]='$arr[teacher_version]',
															 [teacher_version_preview]='$arr[teacher_version_preview]',
															 [student_version]='$arr[student_version]',
															 [student_version_preview]='$arr[student_version_preview]',
															 [updtime]='".time()."',
															 [IP] = '$arr[IP]',
															 [is_parttime_visible] = '$arr[is_parttime_visible]',
															 [status] = '$arr[status]',
															 [verifier]='$arr[verifier]' 
															 WHERE [hid] = '$hid'");
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}

	public function get_handoutsList($condition='',$currentPage=1, $pageSize=20,$order = ' ORDER BY instime DESC'){
		$count = $this->get_handoutsCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT * FROM ' . $this->tableName . ' WHERE 1=1 ';
		if($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		$handoutsList = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		if(!empty($handoutsList)){
			foreach ($handoutsList as $key=>$handout){
				//$handoutsList[$key]['teacher_preview'] = (file_exists(APP_DIR.str_replace(end(explode('.',$handout['teacher_version'])),'swf',$handout['teacher_version'])))?1:0;
				//$handoutsList[$key]['student_preview'] = (file_exists(APP_DIR.str_replace(end(explode('.',$handout['student_version'])),'swf',$handout['student_version'])))?1:0;
				$teacher_preview_file = !empty($handout['teacher_version_preview'])?$handout['teacher_version_preview']:str_replace(end(explode('.',$handout['teacher_version'])),'swf',$handout['teacher_version']);
				$student_preview_file = !empty($handout['student_version_preview'])?$handout['student_version_preview']:str_replace(end(explode('.',$handout['student_version'])),'swf',$handout['student_version']);
				$handoutsList[$key]['teacher_preview'] = (file_exists(APP_DIR.$teacher_preview_file))?1:0;
				$handoutsList[$key]['student_preview'] = (file_exists(APP_DIR.$student_preview_file))?1:0;

			}
		}
		return $handoutsList;
	}

	public function get_handoutsCount($condition='') {
		$strQuery = 'SELECT count(1) FROM ' . $this->tableName . ' WHERE 1=1 ';
		if ($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		$count = $this->dao->getOne($strQuery);
		return $count;
	}

	public function get_handoutsInfo_by_hid($hid){
		return $this->dao->getRow("SELECT [hid],
										  [type],
										  [title],
										  [picture],
										  [sid],
										  [gid],
										  [kid],
										  [nids],
										  [introduce],
										  [teacher_version],
										  [student_version],
										  [instime],
										  [updtime],
										  [user_key],
										  [is_teaching_and_research],
										  [is_share],
										  [IP],
										  [is_parttime_visible],
										  [status],
										  [verifier],
										  [is_delete],
										  [is_rename],
										  [teacher_version_preview],
										  [student_version_preview] FROM ".$this->tableName." WHERE [hid] = '$hid'");
	}

	public function do_share_handouts($hid,$status){
		$this->dao->execute("UPDATE ".$this->tableName." SET [is_share] = '$status' WHERE [hid] = '$hid'");
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}


	public function delete_handouts_by_hid($hid){
		$this->dao->execute("UPDATE ".$this->tableName." SET [is_delete] = '1' WHERE [hid] = '$hid'");
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}

	public function get_last_hid(){
		return $this->dao->getOne("SELECT TOP 1 [hid] FROM ".$this->tableName." ORDER BY [hid] DESC");
	}


	/*获取上传统计数据*/
	public function get_uploadOrDownloadList($condition='',$currentPage=1, $pageSize=20,$getType='upload',$isPage=1){
		if($getType == 'upload'){
			$strQuery = 'SELECT count(*) as uploadnum,user_key FROM ' . $this->tableName . ' WHERE 1=1 ';
			if($condition){
				$strQuery .=  $condition;
			}
			$strQuery .= ' GROUP BY [user_key]';
			$order = ' ORDER BY uploadnum DESC';
		}else if($getType == 'download'){
			$strQuery = 'SELECT count(*)as downloadnum,d.user_key FROM ' . $this->downloadLog . ' as d LEFT JOIN ' . $this->tableName . ' as h ON d.hid = h.hid WHERE 1=1 ';
			if($condition){
				$strQuery .=  $condition;
			}
			$strQuery .= ' GROUP BY d.[user_key]';
			$order = ' ORDER BY downloadnum DESC';
		}
		if($isPage == 1){
			$count = $this->get_uploadOrDownloadCount($condition,$getType);
			$pageCount = ceil($count / $pageSize);
			if($currentPage > $pageCount) $currentPage = $pageCount;
			if($currentPage < 1) $currentPage = 1;
			return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		}else{
			return $this->dao->getAll($strQuery.$order);
		}

	}

	public function  get_alluploadOrDownloadList($handouts_subject,$handouts_grade,$handouts_knowledge,$type,$startTime,$endTime,$way='',$notStr=''){
		$strQuery = 'SELECT vph.[sid],vph.[hid],vph.[gid],vph.[hid],vph.[teacher_version_preview],vph.[kid],vph.[type],vph.[title],vps.[name] as sname,vpg.[name] as gname,vpk.[name] as kname FROM ' .$this->tableName. ' as vph
					 LEFT JOIN '. $this->vp_subject. ' as vps on vph.sid = vps.sid  
					 LEFT JOIN '. $this->vp_grade. ' as vpg on vph.gid = vpg.gid 
					 LEFT JOIN '. $this->vp_knowledge .' as vpk on vph.kid = vpk.kid 
					 WHERE 1=1 ';
		$strQuery .= ' AND vph.is_delete = 0 AND vph.sid > 0 AND vph.gid > 0 AND vph.status = 1';
		if(!empty($handouts_subject)){
			if($way == ''){
				$strQuery .= " AND vph.sid = '$handouts_subject'";
			}else{
				$strQuery .= " AND vph.sid in($handouts_subject)";
			}
		}
		if(!empty($handouts_grade)){
			if($way == ''){
				$strQuery .= " AND vph.gid = '$handouts_grade'";
			}else{
				$strQuery .= " AND vph.gid in($handouts_grade)";
			}
		}
		if(!empty($handouts_knowledge)){
			$strQuery .= " AND vph.kid = '$handouts_knowledge' ";
		}
		if(!empty($type)){
			if($type == 1 || $type == 2){
				$strQuery .= " AND vph.[type] = '".($type-1)."' AND vph.[is_teaching_and_research] = '1'";
			}
		}
		if(!empty($startTime)){
			$strQuery .= " AND vph.instime >= '$startTime'";
		}
		if(!empty($endTime)){
			$strQuery .= " AND vph.instime <= '$endTime'";
		}
		if(!empty($way) && !empty($notStr)){
			$strQuery .= $notStr;
		}
		$strQuery .= ' ORDER BY vph.sid,vph.gid,vph.kid asc';
		return  $this->dao->getAll($strQuery);
	}

	public function get_historyUploadCount($handouts_subject,$handouts_grade,$handouts_knowledge,$type,$startTime,$endTime,$way,$notStr=''){
		$strQuery = 'SELECT count(vph.[hid]) as totalnum,vph.[sid],vph.[gid],vph.[type]  FROM ' .$this->tableName. ' as vph
					 WHERE 1=1 AND vph.status = 1';
		$strQuery .= ' AND vph.is_delete = 0 AND vph.sid > 0 AND vph.gid > 0';
		if(!empty($handouts_subject)){
			if($way == ''){
				$strQuery .= " AND vph.sid = '$handouts_subject' ";
			}else{
				$strQuery .= " AND vph.sid in($handouts_subject)";
			}
		}
		if(!empty($handouts_grade)){
			if($way == ''){
				$strQuery .= " AND vph.gid = '$handouts_grade' ";
			}else{
				$strQuery .= " AND vph.gid in($handouts_grade)";
			}
		}
		if(!empty($type)){
			if($type == 1 || $type == 2){
				$strQuery .= " AND vph.[type] = '".($type-1)."' AND vph.[is_teaching_and_research] = '1'";
			}
		}
		if(!empty($startTime)){
			$strQuery .= " AND vph.instime >= '$startTime'";
		}
		if(!empty($endTime)){
			$strQuery .= " AND vph.instime <= '$endTime'";
		}
		if(!empty($way) && !empty($notStr)){
			$strQuery .= $notStr;
		}
		$strQuery .= ' GROUP BY vph.sid,vph.gid,vph.type ORDER BY vph.sid,vph.gid asc';
		return  $this->dao->getAll($strQuery);
	}

	public function get_uploadOrDownloadCount($condition='',$getType='upload') {
		$strQuery = 'SELECT count(*) FROM ';
		if($getType == 'upload'){
			$strQuery.= $this->tableName. ' WHERE 1=1 '.$condition.' GROUP BY [user_key] ';
		}else{
			$strQuery.= $this->downloadLog .' as d LEFT JOIN '.$this->tableName. ' as h ON d.hid = h.hid WHERE 1=1 '.$condition.' GROUP BY d.[user_key] ';
		}
		$arrTemp = $this->dao->getAll($strQuery);
		return count($arrTemp);
	}

	public function get_teacherUploadOrDownloadListAll($condition,$getType){
		if($getType == 'upload'){
			$strQuery = "SELECT hid,title,sid,gid,kid,nids,instime,IP,is_delete FROM ".$this->tableName." WHERE 1=1 ".$condition."";
			$order = '  ORDER BY instime DESC';
		}else{
			$strQuery = "SELECT h.type,h.title,h.sid,h.gid,h.kid,nids,d.hid,d.download_time,d.IP,is_delete FROM ".$this->downloadLog." AS d LEFT JOIN ".$this->tableName." AS h ON d.hid=h.hid WHERE 1=1 ".$condition." ";
			$order = ' ORDER BY download_time DESC';
		}
		return $this->dao->getAll($strQuery.$order);
	}

	public function get_teacherUploadOrDownloadList($condition,$currentPage=1, $pageSize=20,$getType){
		$count = $this->get_teacherUploadOrDownloadCount($condition,$getType);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		if($getType == 'upload'){
			$strQuery = "SELECT hid,title,sid,gid,kid,nids,instime,IP,is_delete FROM ".$this->tableName." WHERE 1=1 ".$condition."";
			$order = '  ORDER BY instime DESC';
		}else{
			$strQuery = "SELECT h.type,h.title,h.sid,h.gid,h.kid,nids,d.hid,d.download_time,d.IP,is_delete FROM ".$this->downloadLog." AS d LEFT JOIN ".$this->tableName." AS h ON d.hid=h.hid WHERE 1=1 ".$condition." ";
			$order = ' ORDER BY download_time DESC';
		}
		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}

	public function get_teacherUploadOrDownloadCount($condition,$getType){
		if($getType == 'upload'){
			$strQuery = "SELECT count(*) FROM ".$this->tableName." WHERE 1=1 ".$condition;
		}else{
			$strQuery = "SELECT count(*) FROM ".$this->downloadLog." AS d LEFT JOIN ".$this->tableName." AS h ON d.hid=h.hid WHERE 1=1 ".$condition;
		}
		return $this->dao->getOne($strQuery);;
	}


	public function add_downloadLog($arr){
		$this->dao->execute("INSERT INTO ".$this->downloadLog."(user_key,hid,htype,download_time,IP) VALUES('$arr[user_key]','$arr[hid]','$arr[htype]','$arr[download_time]','$arr[IP]')");
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}

	public function reviewHandout($hid,$status,$real_name){
		$this->dao->execute("UPDATE ".$this->tableName." SET [status] ='$status', [verifier]='$real_name' WHERE [hid] = '$hid'");
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}

	public function get_downloadNum($arr){
		if($arr['distinct'] == 1){
			$strQuery = 'SELECT distinct hid  FROM '.$this->downloadLog.' WHERE 1=1 ';
		}else{
			$strQuery = 'SELECT hid FROM '.$this->downloadLog.' WHERE 1=1 ';
			if(!empty($arr['hid'])){
				$strQuery .= " AND [hid] = '$arr[hid]' ";
			}
		}
		if(!empty($arr['user_key'])){
			$strQuery .= " AND [user_key] = '$arr[user_key]' ";
		}
		if(!empty($arr['starttime'])){
			$strQuery .= " AND [download_time] > '$arr[starttime]' ";
		}
		$list = $this->dao->getAll($strQuery);
		return count($list);
	}


	public function add_favorite($arr){
		if(empty($arr['hid']) || empty($arr['user_key']) || $arr['htype'] === ''){
			return false;
		}else{
			$this->dao->execute("INSERT INTO ".$this->vp_favorite." ([hid],[htype],[user_key],[instime]) VALUES ('$arr[hid]','$arr[htype]','$arr[user_key]','".date('Y-m-d H:i:s')."')");
			if($this->dao->affectRows()){
				return true;
			}
			return false;
		}
	}

	public function delete_favorite($arr){
		if(empty($arr['fid']) || empty($arr['user_key'])){
			return false;
		}else{
			$this->dao->execute("DELETE FROM ".$this->vp_favorite." WHERE [fid] = '$arr[fid]' AND [user_key] = '$arr[user_key]'");
			if($this->dao->affectRows()){
				return true;
			}
			return false;
		}
	}

	public function get_favorite_info($arr){
		if(empty($arr['hid']) || empty($arr['user_key'])){
			return false;
		}else{
			return $this->dao->getOne("SELECT TOP 1 [fid] FROM ".$this->vp_favorite." WHERE [hid] = '$arr[hid]' AND [user_key] = '$arr[user_key]'");
		}
	}

	/*获取我的收藏*/
	public function get_favoriteList($condition='',$currentPage=1, $pageSize=20){
		$count = $this->get_favoriteListCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT f.*,h.title FROM ' . $this->vp_favorite . ' AS f LEFT JOIN '.$this->tableName.' as h ON f.hid = h.hid WHERE ';
		if($condition){
			$strQuery .=  $condition;
		}
		$order = ' ORDER BY fid DESC';
		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}


	public function get_favoriteListCount($condition='') {
		return $this->dao->getOne('SELECT count(*) FROM '.$this->vp_favorite .' AS f LEFT JOIN '.$this->tableName.' as h ON f.hid = h.hid WHERE '.$condition);
	}


	public function add_message($arr){
		if($arr['is_delete']){
			$this->dao->execute("INSERT INTO ".$this->vp_message."([user_key],[type],[source_id],[message],[instime],[is_delete]) VALUES('$arr[user_key]','$arr[type]','$arr[source_id]','$arr[message]','$arr[instime]','$arr[is_delete]')");
		}else{
			$this->dao->execute("INSERT INTO ".$this->vp_message."([user_key],[type],[source_id],[message],[instime]) VALUES('$arr[user_key]','$arr[type]','$arr[source_id]','$arr[message]','$arr[instime]')");
		}
		return true;
	}

	public function delete_message_by_id($msgId){
		$this->dao->execute("delete from ".$this->vp_message." where [id]=".$this->dao->quote($msgId));
		return true;
	}

	public function get_messageList($user_key){
		$list = $this->dao->getAll('SELECT [id],[user_key],[type],[source_id],[message],[is_delete] FROM '.$this->vp_message.' WHERE [user_key]='.$this->dao->quote($user_key));
		if(!empty($list)){
			foreach ($list as $key=>$val){
				switch ($val['type']){
					case 0:
						$now_status = $this->dao->getOne('SELECT  [status] FROM '.$this->tableName .' WHERE [hid] = '.$this->dao->quote($val['source_id']));
						$list[$key]['url'] = '/Vip/VipJiaoyan/add_handouts/hid/'.$val['source_id'];
						break;
					case 1:
						$now_status = $this->dao->getOne('SELECT  [status] FROM '.$this->tableName .' WHERE [hid] = '.$this->dao->quote($val['source_id']));
						$list[$key]['url'] = '/Vip/VipJiaoyan/add_itembank/hid/'.$val['source_id'];
						break;
					case 2:
						$now_status = $this->dao->getOne('SELECT  [status] FROM '.$this->vp_publicity .' WHERE [pid] = '.$this->dao->quote($val['source_id']));
						$list[$key]['url'] = '/Vip/VipInfo/publicity';
						break;
				}
				if($now_status != 2 && intval($val['is_delete']) != '1'){
					$this->dao->execute('delete from '.$this->vp_message.' WHERE [id] = '.$this->dao->quote($val['id']));
					unset($list[$key]);
				}
			}
		}
		return $list;
	}


	/*批量重命名用*/
	/*public function getHandoutsAll(){
	$strQuery = 'SELECT  top 2  h.hid,
	h.type,
	h.title,
	h.teacher_version,
	h.student_version,
	h.is_teaching_and_research,
	s.name as sname,
	g.name as gname,
	k.name as kname
	FROM '.$this->tableName.' h
	LEFT JOIN '.$this->vp_subject.' s ON h.sid = s.sid
	LEFT JOIN '.$this->vp_grade.' g ON h.gid = g.gid
	LEFT JOIN '.$this->vp_knowledge.' k ON h.kid = k.kid
	WHERE h.is_rename = 0 AND h.is_teaching_and_research = 1 order by hid desc';
	return $this->dao->getAll($strQuery);

	}


	public function updateHandoutsUrl($set,$hid){
	echo ':UPDATE '.$this->tableName.' SET '.trim($set,',').',is_rename = 1 WHERE hid = '.$this->dao->quote($hid).'<br>';
	return $this->dao->execute('UPDATE '.$this->tableName.' SET '.trim($set,',').',is_rename = 1 WHERE hid = '.$this->dao->quote($hid));
	}*/


	public function  get_allCreateLectures($handouts_subject,$handouts_grade,$handouts_knowledge,$type,$startTime,$endTime,$way=''){
		$strQuery = 'SELECT la.id,la.sid,la.course_id_one,la.course_id_two,la.course_id_three,la.title FROM ' .$this->vip_lecture_archive. ' as la
					 WHERE la.status = 1 ';
		if(!empty($handouts_subject)){
			if($way == ''){
				$strQuery .= " AND la.course_id_one = '$handouts_subject'";
			}else{
				$strQuery .= " AND la.course_id_one in ($handouts_subject)";
			}
		}
		if(!empty($handouts_grade)){
			if($way == ''){
				$strQuery .= " AND la.course_id_two = '$handouts_grade'";
			}else{
				$strQuery .= " AND la.course_id_two in ($handouts_grade)";
			}
		}
		if(!empty($handouts_knowledge)){
			if($way == ''){
				$strQuery .= " AND la.course_id_three = '$handouts_knowledge' ";
			}else{
				$strQuery .= " AND la.course_id_three in ($handouts_knowledge)";
			}
		}

		if(!empty($startTime)){
			$strQuery .= " AND la.created_time >= '$startTime'";
		}
		if(!empty($endTime)){
			$strQuery .= " AND la.created_time <= '$endTime'";
		}

		$strQuery .= ' ORDER BY la.course_id_one,la.course_id_two,la.course_id_three asc';
		$list =  $this->dao2->getAll($strQuery);
		if(!empty($list)){
			foreach ($list as $key=>$row){
				$list[$key]['sname'] = $this->dao2->getOne('SELECT title FROM '.$this->vip_dict_subject.' WHERE id = '.$this->dao2->quote($row['course_id_one']));
				$list[$key]['gname'] = $this->dao2->getOne('SELECT title FROM '.$this->vip_dict_course_type.' WHERE id = '.$this->dao2->quote($row['course_id_two']));;
				$list[$key]['kname'] = $this->dao2->getOne('SELECT name FROM '.$this->vip_knowledge.' WHERE  id = '.$this->dao2->quote($row['course_id_three']));;
			}
		}
		return $list;
	}


	public function  get_allCreateLecture($handouts_subject,$handouts_grade,$handouts_knowledge,$type,$startTime,$endTime,$way=''){
		$strQuery = 'SELECT la.id,la.sid,la.subject_id,la.course_type_id,la.knowledge_id,la.title FROM ' .$this->vip_lecture_archive. ' as la
					 WHERE la.status = 1 ';
		if(!empty($handouts_subject)){
			if($way == ''){
				$strQuery .= " AND la.subject_id = '$handouts_subject'";
			}else{
				$strQuery .= " AND la.subject_id in ($handouts_subject)";
			}
		}
		if(!empty($handouts_grade)){
			if($way == ''){
				$strQuery .= " AND la.course_type_id = '$handouts_grade'";
			}else{
				$strQuery .= " AND la.course_type_id in ($handouts_grade)";
			}
		}
		if(!empty($handouts_knowledge)){
			if($way == ''){
				$strQuery .= " AND la.knowledge_id = '$handouts_knowledge' ";
			}else{
				$strQuery .= " AND la.knowledge_id in ($handouts_knowledge)";
			}
		}

		if(!empty($startTime)){
			$strQuery .= " AND la.created_time >= '$startTime'";
		}
		if(!empty($endTime)){
			$strQuery .= " AND la.created_time <= '$endTime'";
		}

		$strQuery .= ' ORDER BY la.sid,la.course_type_id,la.knowledge_id asc';
		$list =  $this->dao2->getAll($strQuery);
		if(!empty($list)){
			foreach ($list as $key=>$row){
				$list[$key]['sname'] = $this->dao->getOne('SELECT name FROM '.$this->vp_subject.' WHERE type = 0 AND sid = '.$this->dao->quote($row['subject_id']));
				$list[$key]['gname'] = $this->dao->getOne('SELECT name FROM '.$this->vp_grade.' WHERE gid = '.$this->dao->quote($row['course_type_id']));;
				$list[$key]['kname'] = $this->dao->getOne('SELECT name FROM '.$this->vp_knowledge.' WHERE  kid = '.$this->dao->quote($row['knowledge_id']));;
			}
		}
		return $list;
	}



	public function get_allHistoryLectureCount($handouts_subject,$handouts_grade,$handouts_knowledge,$type,$startTime,$endTime,$way=''){

		$strQuery = 'SELECT count(la.id) as totalnum,la.sid,la.subject_id,la.course_type_id FROM ' .$this->vip_lecture_archive. ' as la
					 WHERE la.status = 1 ';
		if(!empty($handouts_subject)){
			if($way == ''){
				$strQuery .= " AND la.subject_id = '$handouts_subject'";
			}else{
				$strQuery .= " AND la.subject_id in ($handouts_subject)";
			}
		}
		if(!empty($handouts_grade)){
			if($way == ''){
				$strQuery .= " AND la.course_type_id = '$handouts_grade'";
			}else{
				$strQuery .= " AND la.course_type_id in ($handouts_grade)";
			}
		}
		if(!empty($handouts_knowledge)){
			$strQuery .= " AND la.knowledge_id = '$handouts_knowledge' ";
		}

		if(!empty($startTime)){
			$strQuery .= " AND la.created_time >= '$startTime'";
		}
		if(!empty($endTime)){
			$strQuery .= " AND la.created_time <= '$endTime'";
		}

		$strQuery .= ' GROUP BY la.subject_id,la.course_type_id  ORDER BY la.sid,la.course_type_id,la.knowledge_id asc';
		$list =  $this->dao2->getAll($strQuery);
		if(!empty($list)){
			foreach ($list as $key=>$row){
				$list[$key]['sname'] = $this->dao->getOne('SELECT name FROM '.$this->vp_subject.' WHERE type = 0 AND sid = '.$this->dao->quote($row['subject_id']));
				$list[$key]['gname'] = $this->dao->getOne('SELECT name FROM '.$this->vp_grade.' WHERE type = 0 AND gid = '.$this->dao->quote($row['course_type_id']));;
			}
		}
		return $list;
	}


}
?>