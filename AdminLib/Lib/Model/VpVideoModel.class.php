<?php

class VpVideoModel extends Model {
	public  $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao();
		$this->vp_video = 'vp_video';
		$this->vp_video_attribute = 'vp_video_attribute';
		$this->vp_video_favorite = 'vp_video_favorite';
		$this->sys_users = 'sys_users';
	}

	public function add_video($arr,$user_key){
		if(!empty($arr['video_url']) && !empty($arr['title']) && !empty($arr['attribute_one']) && !empty($arr['attribute_two']) && !empty($arr['duration']) && !empty($arr['introduce'])){
			$sql = 'INSERT INTO '.$this->vp_video.' ([video_url],
													 [video_img],
													 [title],
													 [attribute_one],
													 [attribute_two],
													 [duration],
													 [introduce],
													 [user_key] ,
													 [instime]) 
											VALUES ('.$this->dao->quote($arr['video_url']).',
													'.$this->dao->quote($arr['video_img']).',
													'.$this->dao->quote(SysUtil::safeString($arr['title'])).',
													'.$this->dao->quote($arr['attribute_one']).',
													'.$this->dao->quote($arr['attribute_two']).',
													'.$this->dao->quote(SysUtil::safeString($arr['duration'])).',
													'.$this->dao->quote(SysUtil::safeString($arr['introduce'])).',
													'.$this->dao->quote($user_key).',
													'.$this->dao->quote(date('Y-m-d H:i:s')).')';
			if($this->dao->execute($sql)){
				return true;
			}
			return false;
		}
		return false;

	}


	public function update_video($arr,$vid){
		if(!empty($arr['video_url']) && !empty($arr['title']) && !empty($arr['attribute_one']) && !empty($arr['attribute_two']) && !empty($arr['duration']) && !empty($arr['introduce'])){
			$sql = 'UPDATE '.$this->vp_video.' SET   [video_url] = '.$this->dao->quote($arr['video_url']).',
													 [video_img] = '.$this->dao->quote($arr['video_img']).',
													 [title] = '.$this->dao->quote(SysUtil::safeString($arr['title'])).',
													 [attribute_one] = '.$this->dao->quote($arr['attribute_one']).',
													 [attribute_two] = '.$this->dao->quote($arr['attribute_two']).',
													 [duration] = '.$this->dao->quote(SysUtil::safeString($arr['duration'])).',
													 [introduce] = '.$this->dao->quote(SysUtil::safeString($arr['introduce'])).',
													 [updatetime] = '.$this->dao->quote(date('Y-m-d H:i:s')).',
													 [status] = 0   
													 WHERE vid = '.$this->dao->quote($vid);					
			if($this->dao->execute($sql)){
				return true;
			}
			return false;
		}
		return false;
	}


	public function review_video($status, $vid){
		if($this->dao->execute('UPDATE '.$this->vp_video.' SET status = '.$this->dao->quote(abs($status)).' WHERE vid = '.$this->dao->quote($vid))){
			return true;
		}
		return false;
	}


	public function delete_video($vid){
		$videoInfo = $this->get_videoInfo(array('vid'=>$vid));
		if($this->dao->execute('DELETE FROM '.$this->vp_video.' WHERE vid = '.$this->dao->quote($vid))){
			$bucket = C('BUCKET');
			if(!empty($videoInfo['video_url'])){
			//	@unlink(APP_DIR.$videoInfo['video_url']);
				$this->delete_oss_object($bucket,$videoInfo['video_url']);
			}
			if(!empty($videoInfo['video_img'])){
			//	@unlink(APP_DIR.$videoInfo['video_img']);
				$this->delete_oss_object($bucket,$videoInfo['video_img']);
			}
			return true;
		}
		return false;
	}

	public function delete_oss_object($bucket,$object){
		import('ORG.Util.OssSdk');
		$oss_sdk_service = new ALIOSS();
		//设置是否打开curl调试模式
		$oss_sdk_service->set_debug_mode(FALSE);
		$response = $oss_sdk_service->delete_object($bucket,$object);
		$is_object_exist = $oss_sdk_service->is_object_exist($bucket,$object);
		if($is_object_exist->status == 404){
			return true;
		}else{
			return false;
		}
	}
	public function get_attributeList($arr){
		$condition = '';
		if(isset($arr['pid'])){
			$condition .= ' AND  parent_id = '.$this->dao->quote(abs($arr['pid']));
		}
		return $this->dao->getAll('SELECT * FROM '.$this->vp_video_attribute.' WHERE 1=1 '.$condition);
	}


	public function get_videoList($user_key,$condition='',$currentPage=1, $pageSize=20,$order = ' ORDER BY instime DESC'){
		$count = $this->get_videoCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT v.*,
							convert(varchar(20),v.instime,120) as instime2,
							a1.name as attribute_one_name,
							a2.name as attribute_two_name,
							u.user_realname as user_name 
							FROM ' . $this->vp_video . ' v 
							LEFT JOIN '.$this->vp_video_attribute.' a1 ON v.attribute_one = a1.aid 
							LEFT JOIN '.$this->vp_video_attribute.' a2 ON v.attribute_two = a2.aid 
							LEFT JOIN '.$this->sys_users.' u ON v.user_key = u.user_key 
							WHERE 1=1 ';
		if($condition) {
			$strQuery .=  $condition;
		}
		$videoList = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		if(!empty($videoList)){
			foreach ($videoList as $key=>$video){
			//	$videoList[$key]['video_url'] = str_replace('/Upload/','/upload/',$video['video_url']);
			//	$videoList[$key]['video_img'] = str_replace('/Upload/','/upload/',$video['video_img']);
				$videoList[$key]['video_url'] = "http://".C('DEFAULT_OSS_HOST')."/".C('BUCKET')."/".$video['video_url'];
				if(!empty($video['video_img'])){
					$videoList[$key]['video_img'] = "http://".C('DEFAULT_OSS_HOST_SHOW')."/".$video['video_img'];
				}else{
					$videoList[$key]['video_img'] = "http://".C('DEFAULT_OSS_HOST_SHOW')."/upload/image/2014-12-29/default_video.jpg";
				}
				
				$videoList[$key]['type'] = end(explode('.',$video['video_url']));
				$videoList[$key]['is_favorite'] = $this->checkIsFavorite($video['vid'],$user_key);
			}
		}
		return $videoList;
	}


	public function get_videoCount($condition){
		$strQuery = 'SELECT count(1) FROM ' . $this->vp_video . ' v  WHERE 1=1 ';
		if ($condition) {
			$strQuery .=  $condition;
		}
		$count = $this->dao->getOne($strQuery);
		return $count;
	}


	public function get_videoInfo($arr){
		$sqlQuery = 'SELECT *,convert(varchar(20),instime,120) as instime2 FROM ' . $this->vp_video . ' WHERE 1=1 ';
		if(!empty($arr['vid'])){
			$sqlQuery .= ' AND vid = '.$this->dao->quote(abs($arr['vid']));
		}
		$info = $this->dao->getRow($sqlQuery);
		//$info['video_url_show'] = str_replace('/Upload/','/upload/',$info['video_url']);
		$info['video_url_show'] = "http://".C('DEFAULT_OSS_HOST_SHOW')."/".$info['video_url'];
	//	$info['video_img_show'] = str_replace('/Upload/','/upload/',$info['video_img']);
		if(!empty($info['video_img'])){
			$info['video_img_show'] = "http://".C('DEFAULT_OSS_HOST_SHOW')."/".$info['video_img'];
		}else{
			$info['video_img_show'] = "http://".C('DEFAULT_OSS_HOST_SHOW')."/upload/image/2014-12-29/default_video.jpg";
		}
		
		
		return $info;
	}
	
	
	public function do_favorite($arr){
		if(!empty($arr['vid']) && !empty($arr['user_key'])){
			if($arr['act'] == 'add'){
				$strQuery = 'INSERT INTO '.$this->vp_video_favorite.' ([vid],
																			  [user_key],
																			  [instime]) 
																	  VALUES('.$this->dao->quote(abs($arr['vid'])).',
																	  		 '.$this->dao->quote($arr['user_key']).',
																	  		 '.$this->dao->quote(date('Y-m-d H:i:s')).')';
			}else{
				$strQuery = 'DELETE FROM '.$this->vp_video_favorite.' WHERE vid = '.$this->dao->quote(abs($arr['vid'])).' AND user_key = '.$this->dao->quote($arr['user_key']);
			}
			if($this->dao->execute($strQuery)){
				return true;
			}
			return false;
		}
		return false;
	}
	
	
	public function checkIsFavorite($vid, $user_key){
		if($this->dao->getOne('SELECT COUNT(1) FROM '.$this->vp_video_favorite.' WHERE vid = '.$this->dao->quote($vid).' AND user_key = '.$this->dao->quote($user_key))){
			return true;
		}
		return false;
	}
	
	public function get_favoriteList($condition='',$currentPage=1, $pageSize=20,$order = ' ORDER BY instime DESC'){
		$count = $this->get_favoriteCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT f.*,
							convert(varchar(20),f.instime,120) as instime1,
							convert(varchar(20),v.instime,120) as instime2,
							v.title as title,
							v.duration as duration,
							v.introduce as introduce,
							v.video_url as video_url,
							a1.name as attribute_one_name,
							a2.name as attribute_two_name,
							u.user_realname as user_name 
							FROM ' . $this->vp_video_favorite . ' f 
							LEFT JOIN ' . $this->vp_video . ' v ON f.vid = v.vid 
							LEFT JOIN '.$this->vp_video_attribute.' a1 ON v.attribute_one = a1.aid 
							LEFT JOIN '.$this->vp_video_attribute.' a2 ON v.attribute_two = a2.aid 
							LEFT JOIN '.$this->sys_users.' u ON f.user_key = u.user_key 
							WHERE v.title is not NULL ';
		if($condition) {
			$strQuery .=  $condition;
		}
		$list = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		if(!empty($list)){
			foreach ($list as $key=>$row){
				$list[$key]['type'] = end(explode('.',$row['video_url']));
			}
		}
		return $list;
	}


	public function get_favoriteCount($condition){
		$strQuery = 'SELECT count(1) FROM ' . $this->vp_video_favorite . ' f 
									 LEFT JOIN ' . $this->vp_video . ' v ON f.vid = v.vid 
									 LEFT JOIN '.$this->vp_video_attribute.' a1 ON v.attribute_one = a1.aid 
									 LEFT JOIN '.$this->vp_video_attribute.' a2 ON v.attribute_two = a2.aid 
									 LEFT JOIN '.$this->sys_users.' u ON f.user_key = u.user_key 
									 WHERE v.title is not NULL ';
		if ($condition) {
			$strQuery .=  $condition;
		}
		$count = $this->dao->getOne($strQuery);
		return $count;
	}
	
	
	public function add_attribute($arr, $type){
		if(!empty($arr)){
			if($type == 'attribute_one'){
				$strQuery = 'INSERT INTO '.$this->vp_video_attribute.' (name,parent_id) VALUES('.$this->dao->quote($arr['name']).',0)';
			}else{
				$strQuery = 'INSERT INTO '.$this->vp_video_attribute.' (name,parent_id) VALUES('.$this->dao->quote($arr['name']).','.$this->dao->quote($arr['parent_id']).')';
			}
			if($this->dao->execute($strQuery)){
				return true;
			}
			return false;
		}
		return false;
	}
	
	
	public function delete_attribute($type, $attributeId){
		if(!empty($attributeId) && !empty($type)){
			$status = 0;
			$this->dao->begin();
			if($type == 'attribute_one'){
				$success1 = $this->dao->execute('DELETE FROM '.$this->vp_video_attribute.' WHERE aid = '.$this->dao->quote($attributeId));
				$count = $this->dao->getOne('SELECT COUNT(1) FROM '.$this->vp_video_attribute.' WHERE parent_id = '.$this->dao->quote($attributeId));
				if($count>0){
					$success2 = $this->dao->execute('DELETE FROM '.$this->vp_video_attribute.' WHERE parent_id = '.$this->dao->quote($attributeId));
				}else{
					$success2 = true;
				}
				if($success1 && $success2){
					$status = 1;
				}
			}else{
				if($this->dao->execute('DELETE FROM '.$this->vp_video_attribute.' WHERE aid = '.$this->dao->quote($attributeId))){
					$status = 1;
				}
			}
			if($status == 1){
				$this->dao->commit();
				return true;
			}
			$this->dao->rollback();
			return false;
		}
		return false;
	}
	
	
	public function edit_attribute($arr){
		if(!empty($arr['name']) && !empty($arr['aid'])){
			$strQuery = 'UPDATE '.$this->vp_video_attribute.' SET  name = '.$this->dao->quote(SysUtil::safeString($arr['name'])).' WHERE aid = '.$this->dao->quote($arr['aid']);
			if($this->dao->execute($strQuery)){
				return true;
			}
			return false;
		}
		return false;
	}
	
	public function upload_history_video(){
		
		import('ORG.Util.OssSdk');
		
		$oss_sdk_service = new ALIOSS();
		//设置是否打开curl调试模式
		$oss_sdk_service->set_debug_mode(FALSE);
		
		$bucket = C("BUCKET");
		$sql="select * from ".$this->vp_video." where charindex('video',video_url) = 0";
		$videoList = $this->dao->getAll($sql);
		
		foreach($videoList as $key=>$value){
			if($value['video_url'] != ''){
				$videoPath = APP_DIR.$value['video_url'];
				if(file_exists($videoPath)){
					$extenstion = end(explode(".",$value['video_url']));
					$object = C('OSS_video_PATH').date('Y-m-d').'/'.time().".".$extenstion;
					$reponse = $oss_sdk_service->upload_file_by_file($bucket,$object,$videoPath);
					dump($reponse);
					if($reponse->status == 200){
						$sql="update ".$this->vp_video." set video_url = '".$object."' where vid='".$value['vid']."'";
						$this->dao->execute($sql);
					}
				}
			}
			if($value['video_img'] != ''){
				$imgPath = APP_DIR.$value['video_img'];
				$extenstion = end(explode(".",$value['video_img']));
				$object = C('OSS_IMG_PATH').date('Y-m-d').'/'.time().".".$extenstion;
				$reponse = $oss_sdk_service->upload_file_by_file($bucket,$object,$imgPath);
				if($reponse->status == 200){
					$sql="update ".$this->vp_video." set video_img = '".$object."' where vid='".$value['vid']."'";
					$this->dao->execute($sql);
				}
			}
		}
	}
}


?>