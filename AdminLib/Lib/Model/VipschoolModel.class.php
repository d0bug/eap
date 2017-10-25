<?php
class VipschoolModel extends Model {
	public  $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao();
		$this->tableName = 'gss_users';
		$this->gss_focus = 'gss_focus';
		$this->gss_announcement = 'gss_announcement';
		$this->gss_grade = 'gss_grade';
		$this->gss_subject = 'gss_subject';
		$this->gss_category = 'gss_category';
		$this->gss_category_subject_rs = 'gss_category_subject_rs';
		$this->gss_teachers = 'gss_teachers';
		$this->gss_studyCard = 'gss_studyCard';
		$this->gss_rechargeCard = 'gss_rechargeCard';
		$this->gss_express = 'gss_express';
		$this->gss_order = 'gss_order';
		$this->gss_course = 'gss_course';
		$this->gss_video_rel_course = 'gss_video_rel_course';
		$this->gss_handout_rel_course = 'gss_handout_rel_course';
		$this->gss_course_pack = 'gss_course_pack';
		$this->gss_help = 'gss_help';
		$this->gss_help_type = 'gss_help_type';
		$this->gss_user_course = 'gss_user_course';
		$this->gss_emails = 'gss_emails';
	}


	public function get_userList($condition='', $currentPage=1, $pageSize=20){
		$count = $this->get_userCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT * FROM ' . $this->tableName . ' WHERE 1=1 ';
		if($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		$order = ' ORDER BY id DESC';
		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}


	public function get_userCount($condition){
		$strQuery = 'SELECT count(1) FROM ' . $this->tableName . ' WHERE 1=1 ';
		if ($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		return $this->dao->getOne($strQuery);
	}


	public function get_userInfo($arr){
		if(!empty($arr)){
			$strQuery = 'SELECT * FROM ' . $this->tableName . ' WHERE 1=1 ';
			if(!empty($arr['uid'])){
				$strQuery .= ' AND id = '.$this->dao->quote(abs($arr['uid']));
			}
			$row = $this->dao->getRow($strQuery);
			if(!empty($row)){
				$row['avatar_show'] = 'http://'.C('DEFAULT_OSS_HOST').'/'.C('BUCKET').'/'.$row['avatar'];
			}
			return $row;
		}
		return false;
	}


	public function add_focus($img, $link, $bgcolor){
		if(!empty($img) && !empty($link)){
			if($this->dao->execute('INSERT INTO '.$this->gss_focus.' (url,link,bg_color) VALUES ('.$this->dao->quote($img).','.$this->dao->quote($link).','.$this->dao->quote($bgcolor).')')){
				return true;
			}
			return false;
		}
		return false;
	}


	public function get_focusList(){
		$list = $this->dao->getAll('SELECT * FROM '.$this->gss_focus.' ORDER BY sort ASC ');
		if(!empty($list)){
			foreach ($list as $key=>$row){
				//$list[$key]['show_url'] = str_replace('/Upload/','/upload/',$row['url']);
				$list[$key]['show_url'] = 'http://'.C('DEFAULT_OSS_HOST').'/'.C('BUCKET').'/'.$row['url'];
			}
		}
		return $list;
	}

	public function update_focus($arr){
		if(!empty($arr['id']) && !empty($arr['url']) && !empty($arr['link']) ){
			if($this->dao->execute('UPDATE '.$this->gss_focus.' SET url = '.$this->dao->quote($arr['url']).',link = '.$this->dao->quote($arr['link']).' WHERE id = '.$this->dao->quote($arr['id']))){
				return true;
			}
			return false;
		}
		return false;
	}


	public function empty_focus(){
		if($this->dao->execute('TRUNCATE TABLE '.$this->gss_focus)){
			return true;
		}
		return false;
	}



	public function add_announcement($arr){
		if(!empty($arr['title']) && !empty($arr['focus']) && !empty($arr['ncontent'])){
			if($this->dao->execute('INSERT INTO '.$this->gss_announcement.' (title,img,keywords,description,content,instime) VALUES ('.$this->dao->quote(SysUtil::safeString($arr['title'])).','.$this->dao->quote($arr['focus'][0]).','.$this->dao->quote(str_replace('，',',',$arr['keywords'])).','.$this->dao->quote(SysUtil::safeString($arr['description'])).','.$this->dao->quote($arr['ncontent']).','.$this->dao->quote(date('Y-m-d H:i:s')).')')){
				return true;
			}
			return false;
		}
		return false;
	}


	public function get_announcementList($condition='', $currentPage=1, $pageSize=20){
		$count = $this->get_announcementCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT * FROM ' . $this->gss_announcement . ' WHERE 1=1 ';
		if($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		$order = ' ORDER BY instime DESC';
		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}


	public function get_announcementCount($condition){
		$strQuery = 'SELECT count(1) FROM ' . $this->gss_announcement . ' WHERE 1=1 ';
		if ($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		return $this->dao->getOne($strQuery);
	}


	public function get_announcementInfo($aid){
		if(!empty($aid)){
			$row = $this->dao->getRow('SELECT * FROM ' . $this->gss_announcement . ' WHERE id = '.$this->dao->quote($aid));
			if(!empty($row)){
				//$row['show_img'] = str_replace('/Upload/','/upload/',$row['img']);
				$row['show_img'] = 'http://'.C('DEFAULT_OSS_HOST').'/'.C('BUCKET').'/'.$row['img'];
			}
			return $row;
		}
		return false;
	}


	public function update_announcement($arr){
		if(!empty($arr['aid']) && !empty($arr['title']) && !empty($arr['focus']) && !empty($arr['ncontent'])){
			if($this->dao->execute('UPDATE '.$this->gss_announcement.' SET title = '.$this->dao->quote(SysUtil::safeString($arr['title'])).',img = '.$this->dao->quote($arr['focus'][0]).' ,keywords='.$this->dao->quote(str_replace('，',',',$arr['keywords'])).',description='.$this->dao->quote(SysUtil::safeString($arr['description'])).',content = '.$this->dao->quote($arr['ncontent']).',updtime = '.$this->dao->quote(date('Y-m-d H:i:s')).' WHERE id = '.$this->dao->quote($arr['aid']))){
				return true;
			}
			return false;
		}
		return false;
	}


	public function get_gradeList(){
		return $this->dao->getAll('SELECT gid,title,alias FROM '.$this->gss_grade.' WHERE status = 1 ORDER BY sort ASC,gid ASC ');
	}


	public function get_subjectList($gid){
		if(!empty($gid)){
			return $this->dao->getAll('SELECT sid,title,alias FROM '.$this->gss_subject.' WHERE status = 1 AND gid = '.$this->dao->quote($gid).' ORDER BY sort ASC,gid ASC ');
		}
		return false;
	}


	public function add_teacher($arr){
		if(!empty($arr['realname']) && !empty($arr['focus']) && !empty($arr['grade']) && !empty($arr['subject']) && !empty($arr['send_word']) && !empty($arr['of_educate_age']) && !empty($arr['intro_content']) && !empty($arr['teaching_style']) && !empty($arr['experience_content']) && !empty($arr['comment'])){
			//$arr['send_word'] = $this->textarea_content_to($arr['send_word']);
			$arr['intro_content'] = $this->textarea_content_to($arr['intro_content']);
			$arr['teaching_style'] = $this->textarea_content_to($arr['teaching_style']);
			$arr['experience_content'] = $this->textarea_content_to($arr['experience_content']);
			$arr['comment'] = $this->textarea_content_to($arr['comment']);
			if($this->dao->execute('INSERT INTO '.$this->gss_teachers.' ( realname,
																		  img,
																		  gid,
																		  sid,
																		  send_word,
																		  of_educate_age,
																		  intro_content,
																		  teaching_style,
																		  experience_content,
																		  comment,
																		  instime,
																		  is_onjob) 
																VALUES ('.$this->dao->quote(SysUtil::safeString($arr['realname'])).',
																	    '.$this->dao->quote($arr['focus'][0]).',
																	    '.$this->dao->quote($arr['grade']).',
																	    '.$this->dao->quote($arr['subject']).',
																	    '.$this->dao->quote(SysUtil::safeString($arr['send_word'])).',
																	    '.$this->dao->quote(abs($arr['of_educate_age'])).',
																	    '.$this->dao->quote(SysUtil::safeString($arr['intro_content'])).',
																	    '.$this->dao->quote(SysUtil::safeString($arr['teaching_style'])).',
																	    '.$this->dao->quote(SysUtil::safeString($arr['experience_content'])).',
																	    '.$this->dao->quote(SysUtil::safeString($arr['comment'])).',
																	    '.$this->dao->quote(date('Y-m-d H:i:s')).',
																	    '.$this->dao->quote($arr['is_onjob']).')')){
			return true;
																	    }
																	    return false;
		}
		return false;
	}


	public function update_teacher($arr){
		if(!empty($arr['realname']) && !empty($arr['focus']) && !empty($arr['grade']) && !empty($arr['subject']) && !empty($arr['send_word']) && !empty($arr['of_educate_age']) && !empty($arr['intro_content']) && !empty($arr['teaching_style']) && !empty($arr['experience_content']) && !empty($arr['comment'])){
			$arr['intro_content'] = $this->textarea_content_to($arr['intro_content']);
			$arr['teaching_style'] = $this->textarea_content_to($arr['teaching_style']);
			$arr['experience_content'] = $this->textarea_content_to($arr['experience_content']);
			$arr['comment'] = $this->textarea_content_to($arr['comment']);
			$strQuery = 'UPDATE '.$this->gss_teachers.' SET realname = '.$this->dao->quote(SysUtil::safeString($arr['realname'])).',
																		  img = '.$this->dao->quote($arr['focus'][0]).',
																		  gid = '.$this->dao->quote($arr['grade']).',
																		  sid = '.$this->dao->quote($arr['subject']).',
																		  send_word = '.$this->dao->quote(SysUtil::safeString($arr['send_word'])).',
																		  of_educate_age = '.$this->dao->quote(abs($arr['of_educate_age'])).',
																		  intro_content = '.$this->dao->quote(SysUtil::safeString($arr['intro_content'])).',
																		  teaching_style = '.$this->dao->quote(SysUtil::safeString($arr['teaching_style'])).',
																		  experience_content = '.$this->dao->quote(SysUtil::safeString($arr['experience_content'])).',
																		  comment = '.$this->dao->quote(SysUtil::safeString($arr['comment'])).',
																		  updtime = '.$this->dao->quote(date('Y-m-d H:i:s')).',
																		  is_onjob = '.$this->dao->quote($arr['is_onjob']).' 
																   	WHERE tid = '.$this->dao->quote(abs($arr['tid']));


			if($this->dao->execute($strQuery)){
				return true;
			}
			return false;
		}
		return false;
	}


	public function get_teacherList($condition='', $currentPage=1, $pageSize=20){
		$count = $this->get_teacherCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT t.*,g.title as grade,s.title as subject FROM ' . $this->gss_teachers . ' t
																  LEFT JOIN '.$this->gss_grade.' g ON t.gid = g.gid 
																  LEFT JOIN '.$this->gss_subject.' s ON t.sid = s.sid 
																  WHERE 1=1 ';
		if($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		$order = ' ORDER BY tid ASC';
		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}

	public function get_teacher_by_gid_sid($gid,$sid){
		$subject_alias2 = $this->dao->getOne('SELECT  alias2 FROM '.$this->gss_subject.' WHERE sid = '.$this->dao->quote($sid));
		$subjectArr = $this->dao->getAll('SELECT sid FROM '.$this->gss_subject.' WHERE alias2 = '.$this->dao->quote($subject_alias2));
		$sid_str = '';
		if(!empty($subjectArr)){
			foreach ($subjectArr as $key=>$subject){
				$sid_str .= $subject['sid'].',';
			}
			$sid_str = "'".implode("','",explode(',',trim($sid_str,',')))."'";
		}
		$strQuery = 'SELECT realname,tid FROM ' . $this->gss_teachers .' where  sid IN ('.$sid_str.') ORDER BY CONVERT(realname USING gbk) ';
		return $this->dao->getAll($strQuery);
	}
	public function get_teacherCount($condition){
		$strQuery = 'SELECT count(1) FROM ' . $this->gss_teachers . ' WHERE 1=1 ';
		if ($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		return $this->dao->getOne($strQuery);
	}


	public function get_teacherInfo($tid){
		if(!empty($tid)){
			$row = $this->dao->getRow('SELECT t.*,
											  g.title as grade,
											  s.title as subject 
											  FROM '.$this->gss_teachers.' t 
											  LEFT JOIN '.$this->gss_grade.' g ON t.gid = g.gid 
											  LEFT JOIN '.$this->gss_subject.' s On t.sid = s.sid 
											  WHERE t.tid = '.$this->dao->quote(abs($tid)));
			if(!empty($row)){
				//$row['show_img'] = str_replace('/Upload/','/upload/',$row['img']);
				$row['show_img'] = 'http://'.C('DEFAULT_OSS_HOST').'/'.C('BUCKET').'/'.$row['img'];
				//$arr['send_word'] = $this->to_textarea_content($row['send_word']);
				$row['intro_content'] = $this->to_textarea_content($row['intro_content']);
				$row['teaching_style'] = $this->to_textarea_content($row['teaching_style']);
				$row['experience_content'] = $this->to_textarea_content($row['experience_content']);
				$row['comment'] = $this->to_textarea_content($row['comment']);
			}
			return $row;
		}
		return false;
	}

	public function textarea_content_to($content){
		return str_replace(" ","&nbsp;",str_replace("\r\n","<br>",$content));
	}


	public function to_textarea_content($content){
		return str_replace("&nbsp;"," ",str_replace("<br>","\r\n",$content));
	}


	public function recommend_teacher($tid){
		if($this->dao->execute('UPDATE '.$this->gss_teachers.' SET is_recommend = 1 WHERE tid = '.$this->dao->quote(abs($tid)))){
			return true;
		}
		return false;
	}


	public function add_studyCard($cardList,$arr){
		if(!empty($cardList) && !empty($arr['course_id'])&& !empty($arr['course_name']) && !empty($arr['num'])&& !empty($arr['endtime'])&& !empty($arr['limit_day'])){
			$strQuery = '';
			foreach ($cardList as $key=>$card){
				$strQuery .= 'INSERT INTO '.$this->gss_studyCard.' (card_code,card_pwd,course_id,course_name,endtime,limit_day,instime) VALUES('.$this->dao->quote($card['code']).','.$this->dao->quote($card['pwd']).','.$this->dao->quote($arr['course_id']).','.$this->dao->quote($arr['course_name']).','.$this->dao->quote($arr['endtime']).','.$this->dao->quote(abs($arr['limit_day'])).','.$this->dao->quote(date('Y-m-d H:i:s')).');';
			}
			if($this->dao->execute($strQuery)){
				return true;
			}
			return false;
		}
		return false;
	}


	public function get_studyCardList($condition='', $currentPage=1, $pageSize=20){
		$count = $this->get_studyCardCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT c.*,u.username FROM ' . $this->gss_studyCard . ' c LEFT JOIN '. $this->tableName .' u ON c.uid = u.id WHERE 1=1 ';
		if($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		$order = ' ORDER BY c.id ASC';
		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}


	public function get_studyCardCount($condition){
		$strQuery = 'SELECT count(1) FROM ' . $this->gss_studyCard . ' c LEFT JOIN '. $this->tableName .' u ON c.uid = u.id WHERE 1=1 ';
		if ($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		return $this->dao->getOne($strQuery);
	}


	public function get_studyCardAll($condition){
		$strQuery = 'SELECT c.*,u.username FROM ' . $this->gss_studyCard . ' c LEFT JOIN '. $this->tableName .' u ON c.uid = u.id WHERE 1=1 ';
		if($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		$strQuery .= ' ORDER BY c.id ASC';
		return $this->dao->getAll($strQuery);
	}


	public function add_rechargeCard($cardList,$arr){
		if(!empty($cardList) && !empty($arr['money']) && !empty($arr['num'])&& !empty($arr['endtime'])){
			$strQuery = '';
			foreach ($cardList as $key=>$card){
				$strQuery .= 'INSERT INTO '.$this->gss_rechargeCard.' (card_code,card_pwd,money,endtime,instime) VALUES('.$this->dao->quote($card['code']).','.$this->dao->quote($card['pwd']).','.$this->dao->quote(abs($arr['money'])).','.$this->dao->quote($arr['endtime']).','.$this->dao->quote(date('Y-m-d H:i:s')).');';
			}
			if($this->dao->execute($strQuery)){
				return true;
			}
			return false;
		}
		return false;
	}


	public function get_rechargeCardList($condition='', $currentPage=1, $pageSize=20){
		$count = $this->get_rechargeCardCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT c.*,u.username FROM ' . $this->gss_rechargeCard . ' c LEFT JOIN '. $this->tableName .' u ON c.uid = u.id WHERE 1=1 ';
		if($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		$order = ' ORDER BY c.id ASC';
		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}


	public function get_rechargeCardCount($condition){
		$strQuery = 'SELECT count(1) FROM ' . $this->gss_rechargeCard . ' c LEFT JOIN '. $this->tableName .' u ON c.uid = u.id WHERE 1=1 ';
		if ($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		return $this->dao->getOne($strQuery);
	}


	public function get_rechargeCardAll($condition){
		$strQuery = 'SELECT c.*,u.username FROM ' . $this->gss_rechargeCard . ' c LEFT JOIN '. $this->tableName .' u ON c.uid = u.id WHERE 1=1 ';
		if($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		$strQuery .= ' ORDER BY c.id ASC';
		return $this->dao->getAll($strQuery);
	}



	public function get_expressList($condition='', $currentPage=1, $pageSize=20){
		$count = $this->get_expressCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT e.*,u.username,o.paytime FROM ' . $this->gss_express . ' e
												     LEFT JOIN '. $this->tableName .' u ON e.uid = u.id 
												     LEFT JOIN '. $this->gss_order .' o ON e.order_number = o.order_number  
												     WHERE 1=1 ';
		if($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		$order = ' ORDER BY e.status ASC , e.eid ASC';
		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}


	public function get_expressCount($condition){
		$strQuery = 'SELECT count(1) FROM ' . $this->gss_express . ' e
									 LEFT JOIN '. $this->tableName .' u ON e.uid = u.id 
									 LEFT JOIN '. $this->gss_order .' o ON e.order_number = o.order_number  
									 WHERE 1=1 ';
		if ($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		return $this->dao->getOne($strQuery);
	}


	public function send_express($eid, $arr){
		if(!empty($eid) && !empty($arr['express_company']) && !empty($arr['express_number'])){
			if($this->dao->execute('UPDATE ' . $this->gss_express . ' SET status = 1,sendtime='.$this->dao->quote(date('Y-m-d H:i:s')).', express_company = '.$this->dao->quote(SysUtil::safeSearch($arr['express_company'])).',express_number= '.$this->dao->quote(SysUtil::safeSearch($arr['express_number'])).' WHERE eid = '.$this->dao->quote($eid))){
				return true;
			}
			return false;
		}
		return false;
	}


	public function  get_expressInfo($eid){
		if(!empty($eid)){
			return $this->dao->getRow('SELECT e.*,u.username,o.paytime FROM ' . $this->gss_express . ' e
																       LEFT JOIN '. $this->tableName .' u ON e.uid = u.id 
																       LEFT JOIN '. $this->gss_order .' o ON e.order_number = o.order_number  
																       WHERE eid='.$this->dao->quote($eid));
		}
		return false;
	}


	public function get_expressAll($condition){
		$strQuery = 'SELECT e.*,u.username,o.paytime FROM ' . $this->gss_express . ' e
												     LEFT JOIN '. $this->tableName .' u ON e.uid = u.id 
												     LEFT JOIN '. $this->gss_order .' o ON e.order_number = o.order_number  
												     WHERE 1=1 ';
		if($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		$strQuery .= ' ORDER BY e.status ASC , e.eid ASC';
		return $this->dao->getAll($strQuery);
	}


	/*zhaohaibing 开始 */
	public function get_grade(){

		$gradeList = $this->dao->getAll('SELECT gid,title,alias FROM '.$this->gss_grade.' where status = 1 ORDER BY sort ASC ');
		return $gradeList;
	}
	public function get_grade_by_gid($gid){
		$strQuery = 'SELECT gid,title,alias FROM '.$this->gss_grade.' where status = 1 and gid='.$this->dao->quote($gid);
		return $this->dao->getRow($strQuery);
	}
	public function get_subject_by_sid($sid){
		$strQuery = 'SELECT sid,title,alias FROM '.$this->gss_subject.' where status = 1 and sid='.$this->dao->quote($sid);
		return $this->dao->getRow($strQuery);
	}
	public function addCourseInfo($type,$arr){
		if(empty($arr)){
			return false;
		}
		if($type == 'grade'){
			$strQuery = "INSERT INTO ".$this->gss_grade."(title,alias,sort,status) select ".$this->dao->quote($arr['name']).",".$this->dao->quote($arr['alias']).",IFNULL(max(sort),0)+1,1 from ".$this->gss_grade;
		}else if($type == 'subject'){
			$strQuery = "INSERT INTO ".$this->gss_subject."(gid,title,alias,alias2,sort,status) select ".$arr['grade'].",".$this->dao->quote($arr['name']).",".$this->dao->quote($arr['alias']).",".$this->dao->quote($arr['alias2']).",IFNULL(max(sort),0)+1,1 from ".$this->gss_subject." where gid = ".$arr['grade'];
		}else if($type == 'classify'){
			$this->dao->execute("begin");
			$strQuery = "SELECT count(1) from ".$this->gss_category_subject_rs." where sid = '".$arr['subject']."'";
			$maxSort = $this->dao->getOne($strQuery);
			$strQuery = "INSERT INTO ".$this->gss_category."(title,sort,status,keywords,`desc`) values('".$arr['name']."','".($maxSort+1)."',1,'".$arr['alias']."','".$arr['des']."')";

			$success1 = $this->dao->execute($strQuery);
			$strQuery = "select max(cid) from ".$this->gss_category;
			$maxCid = $this->dao->getOne($strQuery);
			$strQuery = "INSERT INTO ".$this->gss_category_subject_rs."(sid,cid) values('".$arr['subject']."','".$maxCid."')";
			$success2 = $this->dao->execute($strQuery);
			if($success1 && $success2){
				$this->dao->execute("commit");
				return true;
			}else{
				$this->dao->execute("rollback");
				return false;
			}
		}else if($type == 'twoClassify'){
			$strQuery = "INSERT INTO ".$this->gss_category."(title,sort,status,keywords,`desc`,parent_cid) values('".$arr['name']."','".($maxSort+1)."',1,'".$arr['alias']."','".$arr['des']."','".$arr['classify']."')";
		}else if($type == 'threeClassify'){
			$strQuery = "INSERT INTO ".$this->gss_category."(title,sort,status,keywords,`desc`,parent_cid) values('".$arr['name']."','".($maxSort+1)."',1,'".$arr['alias']."','".$arr['des']."','".$arr['twoClassify']."')";
		}else if($type == 'fourClassify'){
			$strQuery = "INSERT INTO ".$this->gss_category."(title,sort,status,keywords,`desc`,parent_cid) values('".$arr['name']."','".($maxSort+1)."',1,'".$arr['alias']."','".$arr['des']."','".$arr['threeClassify']."')";
		}

		if($this->dao->execute($strQuery)){
			return true;
		}
	}
	public function get_subject_by_gid($gid){
		$strQuery = "select sid as id,title,alias from ".$this->gss_subject." where status = 1 and gid = ".$this->dao->quote($gid)." order by sort asc";
		return $this->dao->getAll($strQuery);
	}
	public function get_cate_list(){
		$strQuery = "select cid,title,sort,status from ".$this->gss_category." where status = 1 and parent_cid=0 order by CONVERT(title USING GBK) asc";
		return $this->dao->getAll($strQuery);
	}
	public function get_cate_by_sid($sid){
		$strQuery = "select gc.cid as id,gc.title,gc.keywords from ".$this->gss_category." as gc
												  left join ".$this->gss_category_subject_rs." as gcr on gc.cid = gcr.cid 
												   where gc.status = 1 and gcr.sid = ".$this->dao->quote($sid)." order by gc.sort asc";
		return $this->dao->getAll($strQuery);
	}
	public function get_twocate_by_sid($cid){
		$strQuery = "select cid as id,title,keywords from ".$this->gss_category."
												   where status = 1 and parent_cid='".$cid."' order by sort asc";
		return $this->dao->getAll($strQuery);
	}
	public function get_threecate_by_sid($cid){
		$strQuery = "select cid as id,title,keywords from ".$this->gss_category."
												   where status = 1 and parent_cid='".$cid."' order by sort asc";
		return $this->dao->getAll($strQuery);
	}
	public function get_fourcate_by_sid($cid){
		$strQuery = "select cid as id,title,keywords from ".$this->gss_category."
												   where status = 1 and parent_cid='".$cid."' order by sort asc";
		return $this->dao->getAll($strQuery);
	}
	public function delete_grade_by_id($gid){
		if(!empty($gid)){
			$status = 0;

			$this->dao->execute('begin');
			$success1 = $this->dao->execute('update '.$this->gss_grade.' set status = 0 WHERE gid = '.$this->dao->quote($gid));

			$sidArr = $this->dao->getAll('SELECT sid FROM '.$this->gss_subject.' WHERE gid = '.$this->dao->quote($gid));
			$sidStr = '';

			if(count($sidArr)>0){
				$success2 = $this->dao->execute('update '.$this->gss_subject.' set status = 0 WHERE gid = '.$this->dao->quote($gid));
				foreach($sidArr as $key=>$sid){
					$sidStr .= $sid['sid'].',';
				}
				$sidStr = trim($sidStr,',');
			}else{
				$success2 = true;
			}

			if($sidStr == ''){
				$success3 = true;
				$success4 = true;
			}else{
				$cidArr = $this->dao->getAll('SELECT cid FROM '.$this->gss_category_subject_rs.' WHERE sid in('.$sidStr.')');
				if(count($cidArr)>0){
					$cidStr = '';
					foreach($cidArr as $key=>$cid){
						$cidStr .= $cid['cid'].',';
					}
					$cidStr = trim($cidStr,',');
					$success3 = $this->dao->execute('DELETE FROM '.$this->gss_category_subject_rs.' WHERE sid in('.$sidStr.')');
					$success4 = $this->dao->execute('update  '.$this->gss_category.' set status = 0 WHERE cid in('.$cidStr.')');
					$success5 = $this->dao->execute('update  '.$this->gss_category.' set status = 0 WHERE parent_cid in('.$cidStr.')');
				}else{
					$success3 = true;
					$success4 = true;
					$success5 = true;
				}
			}

			if($success1 && $success2 && $success3 && $success4 && $success5){
				$status = 1;
			}

			if($status == 1){
				$this->dao->execute('commit');
				return true;
			}
			$this->dao->execute('rollback');
			return false;
		}
		return false;
	}

	public function delete_subject_by_id($sid){
		if(!empty($sid)){
			$status = 0;
			$this->dao->execute('begin');
			$success1 = $this->dao->execute('update '.$this->gss_subject.' set status = 0 WHERE sid = '.$this->dao->quote($sid));

			$cidArr = $this->dao->getAll('SELECT cid FROM '.$this->gss_category_subject_rs.' WHERE sid = '.$this->dao->quote($sid));
			$cidStr = '';

			if(count($cidArr)>0){
				$success2 = $this->dao->execute('DELETE FROM '.$this->gss_category_subject_rs.' WHERE sid = '.$this->dao->quote($sid));
				foreach($cidArr as $key=>$cid){
					$cidStr .= $cid['cid'].',';
				}
				$cidStr = trim($cidStr,',');
				$success3 = $this->dao->execute('update '.$this->gss_category.' set status = 0 WHERE cid in('.$cidStr.')');
				$success4 = $this->dao->execute('update '.$this->gss_category.' set status = 0 WHERE parent_cid in('.$cidStr.')');
			}else{
				$success2 = true;
				$success3 = true;
				$success4 = true;
			}

			if($success1 && $success2 && $success3 && $success4){
				$status = 1;
			}

			if($status == 1){
				$this->dao->execute('commit');
				return true;
			}
			$this->dao->execute('rollback');
			return false;
		}
		return false;
	}
	public function delete_classify_by_id($cid){
		if(!empty($cid)){
			$status = 0;
			$this->dao->execute('begin');
			$success1 = $this->dao->execute('update '.$this->gss_category.' set status = 0 WHERE cid = '.$this->dao->quote($cid));
			$success2 = $this->dao->execute('DELETE FROM '.$this->gss_category_subject_rs.' WHERE cid = '.$this->dao->quote($cid));
			$success3 = $this->dao->execute('update '.$this->gss_category.' set status = 0 WHERE parent_cid = '.$this->dao->quote($cid));
			if($success1 && $success2){
				$status = 1;
			}

			if($status == 1){
				$this->dao->execute('commit');
				return true;
			}
			$this->dao->execute('rollback');
			return false;
		}
		return false;
	}
	public function delete_twoClassify_by_id($cid){
		$status = 0;
		$this->dao->execute('begin');
		$success1 = $this->dao->execute('update '.$this->gss_category.' set status = 0 WHERE cid = '.$this->dao->quote($cid));
		$success2 = $this->dao->execute('update '.$this->gss_category.' set status = 0 WHERE parent_cid = '.$this->dao->quote($cid));
		if($success1){
			$status = 1;
		}

		if($status == 1){
			$this->dao->execute('commit');
			return true;
		}
		$this->dao->execute('rollback');
		return false;
	}

	public function delete_threeClassify_by_id($cid){
		if(!empty($cid)){
			return	 $this->dao->execute('update  '.$this->gss_category.' set status = 0 WHERE cid = '.$this->dao->quote($cid));
		}
		return false;
	}

	public function delete_fourClassify_by_id($cid){
		if(!empty($cid)){
			return	 $this->dao->execute('update  '.$this->gss_category.' set status = 0 WHERE cid = '.$this->dao->quote($cid));
		}
		return false;
	}

	public function edit_classify($arr){
		if(!empty($arr['name']) && !empty($arr['typeId']) && !empty($arr['type']) ){
			switch($arr['type']){
				case 'grade':
					$strQuery = 'UPDATE '.$this->gss_grade.' SET  title = '.$this->dao->quote(SysUtil::safeString($arr['name'])).' WHERE gid = '.$this->dao->quote($arr['typeId']);
					break;
				case 'subject':
					$strQuery = 'UPDATE '.$this->gss_subject.' SET  title = '.$this->dao->quote(SysUtil::safeString($arr['name'])).' WHERE sid = '.$this->dao->quote($arr['typeId']);
					break;
				case 'classify':
					$strQuery = 'UPDATE '.$this->gss_category.' SET  title = '.$this->dao->quote(SysUtil::safeString($arr['name'])).' WHERE cid = '.$this->dao->quote($arr['typeId']);
					break;
				case 'twoClassify':
					$strQuery = 'UPDATE '.$this->gss_category.' SET  title = '.$this->dao->quote(SysUtil::safeString($arr['name'])).' WHERE cid = '.$this->dao->quote($arr['typeId']);
					break;
				case 'threeClassify':
					$strQuery = 'UPDATE '.$this->gss_category.' SET  title = '.$this->dao->quote(SysUtil::safeString($arr['name'])).' WHERE cid = '.$this->dao->quote($arr['typeId']);
					break;
				case 'fourClassify':
					$strQuery = 'UPDATE '.$this->gss_category.' SET  title = '.$this->dao->quote(SysUtil::safeString($arr['name'])).' WHERE cid = '.$this->dao->quote($arr['typeId']);
					break;
			}
			if($this->dao->execute($strQuery)){
				return true;
			}
			return false;
		}
		return false;
	}

	public function get_courseList($condition='',$currentPage=1, $pageSize=20){
		$count = $this->get_courseCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT * FROM ' . $this->gss_course . ' WHERE 1=1 ';
		if($condition){
			$strQuery .= $condition;
		}
		$order = ' ORDER BY instime DESC';
		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);

	}
	public function get_courseCount($condition=''){
		$strQuery = 'SELECT count(1) FROM ' . $this->gss_course . ' WHERE 1=1 ';
		if($condition){
			$strQuery .= $condition;
		}
		return $this->dao->getOne($strQuery);
	}
	public function add_course($arr){
		if($arr['course_name'] != ''){
			if($arr['is_free'] == 1){
				$arr['price'] = 0;
			}
			$strQuery = 'INSERT INTO '.$this->gss_course.' ( course_name,
																		  tid,
																		  keywords,
																		  `desc`,
																		  gid,
																		  sid,
																		  cid,
																		  cid_two,
																		  cid_three,
																		  cid_four,
																		  endtime,
																		  price,
																		  is_free,
																		  course_img,
																		  instime) 
																VALUES ('.$this->dao->quote(SysUtil::safeString($arr['course_name'])).',
																	    '.$this->dao->quote($arr['tid']).',
																	    '.$this->dao->quote(SysUtil::safeString(str_replace('，',',',$arr['keywords']))).',
																	    '.$this->dao->quote(SysUtil::safeString($arr['desc'])).',
																	    '.$this->dao->quote(abs($arr['grade'])).',
																	    '.$this->dao->quote(abs($arr['subject'])).',
																	    '.$this->dao->quote(abs($arr['classify'])).',
																	    '.$this->dao->quote(abs($arr['twoClassify'])).',
																	    '.$this->dao->quote(abs($arr['threeClassify'])).',
																	    '.$this->dao->quote(abs($arr['fourClassify'])).',
																	    '.$this->dao->quote($arr['endtime']).',
																	    '.$this->dao->quote(intval($arr['price'])).',
																	    '.$this->dao->quote($arr['is_free']).',
																	    '.$this->dao->quote($arr['course_img']).',
																	    '.$this->dao->quote(date('Y-m-d H:i:s')).')';
			$this->dao->execute($strQuery);
			return $this->dao->getOne("SELECT max(id) from ".$this->gss_course);
		}else{
			return false;
		}
	}
	public function update_course($arr,$id){
		if($id != ''){
			if($arr['is_free'] == 1){
				$arr['price'] = 0;
			}
			$strQuery = 'update '.$this->gss_course.'  set  course_name = '.$this->dao->quote(SysUtil::safeString($arr['course_name'])).',
																tid = '.$this->dao->quote($arr['tid']).',
																keywords = '.$this->dao->quote(SysUtil::safeString(str_replace('，',',',$arr['keywords']))).',
																`desc` = '.$this->dao->quote(SysUtil::safeString($arr['desc'])).',
																gid	= '.$this->dao->quote(abs($arr['grade'])).',
																sid = '.$this->dao->quote(abs($arr['subject'])).',
																cid = '.$this->dao->quote(abs($arr['classify'])).',
																cid_two = '.$this->dao->quote(abs($arr['twoClassify'])).',
																cid_three = '.$this->dao->quote(abs($arr['threeClassify'])).',
																cid_four = '.$this->dao->quote(abs($arr['fourClassify'])).',
																endtime = '.$this->dao->quote($arr['endtime']).',
																price = '.$this->dao->quote(intval($arr['price'])).',
																is_free	= '.$this->dao->quote($arr['is_free']).',
																course_img	= '.$this->dao->quote($arr['course_img']).',
																updtime  = '.$this->dao->quote(date('Y-m-d H:i:s')).' 
															where id='.$this->dao->quote($id);
			return $this->dao->execute($strQuery);
		}else{
			return false;
		}
	}
	public function get_courseInfo_by_id($id){
		$strQuery = "SELECT gsc.course_name,gsc.instime,gsc.updtime,gsc.keywords,gsc.desc,gsc.endtime,gsc.price,gst.realname,gsg.title as grade,gss.title as subject,gss.alias2 as subject_alias,gsca.title as classify,gscb.title AS twoClassify,gscc.title AS threeClassify,gscd.title AS fourClassify from ".$this->gss_course." as gsc
								left join ".$this->gss_teachers." as gst on gsc.tid = gst.tid  
								left join ".$this->gss_grade." as gsg on gsc.gid = gsg.gid  
								left join ".$this->gss_subject." as gss on gsc.sid = gss.sid 
								left join ".$this->gss_category." as gsca on gsc.cid = gsca.cid 
								left join ".$this->gss_category." gscb ON gsc.cid_two = gscb.cid 
								left join ".$this->gss_category." gscc ON gsc.cid_three = gscc.cid  
								left join ".$this->gss_category." gscd ON gsc.cid_four = gscd.cid 
								where gsc.id=".$this->dao->quote($id)."";
		return $this->dao->getRow($strQuery);
	}

	public function get_course_by_id($id){
		$strQuery = "SELECT *  from ".$this->gss_course."
								where id=".$this->dao->quote($id)."";
		return $this->dao->getRow($strQuery);
	}

	public function get_course_list_order_name(){

		//$strQuery = "SELECT id,course_name,price  from ".$this->gss_course." ORDER BY CONVERT(course_name USING gbk) ";
		$strQuery = "SELECT id,course_name,price  from ".$this->gss_course." ORDER BY id DESC ";
		return $this->dao->getAll($strQuery);
	}

	public function add_video_handout($course_id,$arr){
		$this->dao->execute('begin');
		if($arr['video_name'] != ''){
			$strQuery = 'INSERT INTO '.$this->gss_video_rel_course.' ( course_id,
																			  video_name,
																			  cc_vid,
																			  allow_try,
																			  try_time,
																			  instime,
																			  knowlege_name,
																			  knowlege_id) 
																	VALUES ('.$this->dao->quote($course_id).',
																			'.$this->dao->quote(SysUtil::safeString($arr['video_name'])).',
																		    '.$this->dao->quote(SysUtil::safeString($arr['cc_vid'])).',
																		    '.$this->dao->quote($arr['allow_try']).',
																		    '.$this->dao->quote($arr['try_time']).',
																		    '.$this->dao->quote(date("Y-m-d H:i:s")).',
																		    '.$this->dao->quote($arr['knowlege_name']).',
																		    '.$this->dao->quote($arr['knowlege']).')';
			$success1 = $this->dao->execute($strQuery);
		}else{
			$success1 = true;
		}
		if($arr['handout_name'] != ''){
			$strQuery = 'INSERT INTO '.$this->gss_handout_rel_course.' (course_id,
																			  handout_name,
																			  instime,
																			  handout_url) 
																	VALUES ('.$this->dao->quote($course_id).',
																			'.$this->dao->quote(SysUtil::safeString($arr['handout_name'])).',
																		    '.$this->dao->quote(date("Y-m-d H:i:s")).',
																			'.$this->dao->quote(SysUtil::safeString($arr['handout_url'])).')';
			$success2 = $this->dao->execute($strQuery);
		}else{
			$success2 = true;
		}
		if($success1 && $success2){
			$this->dao->execute('commit');
			return true;
		}
		$this->dao->execute("rollback");
		return false;
	}

	public function add_course_pack($arr){
		$course_id_str ='';
		foreach($arr['course_id'] as $key=>$value){
			$course_id_str .= $value.",";
		}
		$course_id_str = trim($course_id_str,',');
		$arr['course_num'] = ($arr['ptype'] == 1)?$arr['course_num']:count($arr['course_id']);
		$strQuery = 'INSERT INTO '.$this->gss_course_pack.' ( pname,
															course_id_str,
															ptype,
															price,
															coupon_type,
															coupon_value,
															instime,
															course_num,
															cid,
															is_give_book,
															introduce) 
															 VALUES ('.$this->dao->quote(SysUtil::safeString($arr['pname'])).',
																		'.$this->dao->quote(SysUtil::safeString($course_id_str)).',
																		'.$this->dao->quote($arr['ptype']).',
																		'.$this->dao->quote(SysUtil::safeString($arr['price'])).',
																	    '.$this->dao->quote($arr['coupon_type']).',
																	    '.$this->dao->quote(SysUtil::safeString($arr['coupon_value'][$arr['coupon_type']])).',
																	    '.$this->dao->quote(date("Y-m-d H:i:s")).',
																	    '.$this->dao->quote($arr['course_num']).',
																	    '.$this->dao->quote($arr['cid']).',
																	    '.$this->dao->quote($arr['is_give_book']).',
																	    '.$this->dao->quote($arr['introduce']).')';
		return  $this->dao->execute($strQuery);
	}

	public function get_packList($condition='',$currentPage=1, $pageSize=20){
		$count = $this->get_packCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT * FROM ' . $this->gss_course_pack . ' WHERE 1=1 ';
		if($condition){
			$strQuery .=  $condition ;
		}
		$order = ' ORDER BY instime DESC';
		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}

	public function get_packCount($condition){
		$strQuery = 'SELECT count(1) FROM ' . $this->gss_course_pack . ' WHERE 1=1 ';
		if($condition){
			$strQuery .=  $condition ;
		}
		return $this->dao->getOne($strQuery);
	}
	public function get_pack_by_pid($pid){
		$strQuery = 'SELECT * FROM '.$this->gss_course_pack.' where pid='.$this->dao->quote($pid);
		$row = $this->dao->getRow($strQuery);
		if(!empty($row)){
			$row['real_price'] = $row['coupon_type'] == 0?$row['price']-$row['coupon_value']:$row['price']*$row['coupon_value']/100;
		}
		return $row;
	}
	public function get_course_list_by_idStr($idStr,$not=0,$cid=0){
		$strQuery = 'SELECT id,course_name FROM '.$this->gss_course.' where id in('.$idStr.')';
		if($not == 1){
			$strQuery = 'SELECT id,course_name FROM '.$this->gss_course.' where cid = '.$cid.' AND not id in('.$idStr.')';
		}
		$strQuery .= ' ORDER BY id DESC';
		return $this->dao->getAll($strQuery);
	}
	public function update_course_pack($arr,$pid){
		$course_id_str ='';
		foreach($arr['course_id'] as $key=>$value){
			$course_id_str .= $value.",";
		}
		$course_id_str = trim($course_id_str,',');
		$arr['course_num'] = ($arr['ptype'] == 1)?$arr['course_num']:count($arr['course_id']);
		$strQuery = 'update '.$this->gss_course_pack.' set  pname = '.$this->dao->quote(SysUtil::safeString($arr['pname'])).',
																	course_id_str = '.$this->dao->quote(SysUtil::safeString($course_id_str)).',
																	ptype = '.$this->dao->quote($arr['ptype']).',
																	price = '.$this->dao->quote(SysUtil::safeString($arr['price'])).',
																	coupon_type = '.$this->dao->quote($arr['coupon_type']).',
																	coupon_value = '.$this->dao->quote(SysUtil::safeString($arr['coupon_value'][$arr['coupon_type']])).',
																	updatetime = '.$this->dao->quote(date("Y-m-d H:i:s")).',
																	cid = '.$this->dao->quote($arr['cid']).',
																	course_num = '.$this->dao->quote($arr['course_num']).',
																	is_give_book = '.$this->dao->quote($arr['is_give_book']).',
																	introduce = '.$this->dao->quote($arr['introduce']).' 
																	  where pid='.$this->dao->quote($pid);
		return  $this->dao->execute($strQuery);
	}
	public function get_courseVideoNum($courseList){
		$courseIdStr = '';
		foreach($courseList as $key=>$course){
			$courseIdStr .= $course['id'].',';
		}
		$courseIdStr = trim($courseIdStr,',');
		if($courseIdStr){
			$strQuery = 'select count(*) as num,course_id from '.$this->gss_video_rel_course.' where course_id in('.$courseIdStr.') group by course_id ';
			$courseNum = $this->dao->getAll($strQuery);
			$courseNumArr = array();
			foreach($courseNum as $key=>$num){
				$courseNumArr[$num['course_id']] = $num['num'];
			}
		}
		return $courseNumArr;
	}
	public function get_courseHandoutNum($courseList){
		$courseIdStr = '';
		foreach($courseList as $key=>$course){
			$courseIdStr .= $course['id'].',';
		}
		$courseIdStr = trim($courseIdStr,',');
		if($courseIdStr){
			$strQuery = 'select count(*) as num,course_id from '.$this->gss_handout_rel_course.' where course_id in('.$courseIdStr.') group by course_id ';
			$courseNum = $this->dao->getAll($strQuery);
			$courseNumArr = array();
			foreach($courseNum as $key=>$num){
				$courseNumArr[$num['course_id']] = $num['num'];
			}
		}
		return $courseNumArr;
	}
	public function get_videoList($course_id){
		$strQuery = 'select * from '.$this->gss_video_rel_course.' where course_id ='.$this->dao->quote($course_id).' order by instime desc';
		return $this->dao->getAll($strQuery);
	}
	public function get_video_by_id($vid){
		$strQuery = 'select * from '.$this->gss_video_rel_course.' where vid ='.$this->dao->quote($vid);
		return $this->dao->getRow($strQuery);
	}
	public function get_handoutList($course_id){
		$strQuery = 'select hid,course_id,handout_name,handout_url,instime,updtime from '.$this->gss_handout_rel_course.' where course_id ='.$this->dao->quote($course_id).' order by instime desc';
		return $this->dao->getAll($strQuery);
	}
	public function get_handout_by_id($hid){
		$strQuery = 'select hid,course_id,handout_name,handout_url,instime,updtime from '.$this->gss_handout_rel_course.' where hid ='.$this->dao->quote($hid);
		return $this->dao->getRow($strQuery);
	}
	public function update_video_by_id($vid,$course_id,$arr){
		$strQuery = 'UPDATE '.$this->gss_video_rel_course.' set video_name = '.$this->dao->quote(SysUtil::safeString($arr['video_name'])).',
																	cc_vid = '.$this->dao->quote(SysUtil::safeString($arr['cc_vid'])).',
																	allow_try = '.$this->dao->quote($arr['allow_try']).',
																	 try_time = '.$this->dao->quote($arr['try_time']).',
																	 knowlege_name = '.$this->dao->quote($arr['knowlege_name']).',
																	 knowlege_id = '.$this->dao->quote($arr['knowlege']).',
																	  updtime = '.$this->dao->quote(date("Y-m-d H:i:s")).'
																	   where course_id = '.$this->dao->quote($course_id).'
																	    and vid ='.$this->dao->quote($vid);
		return  $this->dao->execute($strQuery);
	}
	public function update_handout_by_id($hid,$course_id,$arr){
		$strQuery = 'UPDATE '.$this->gss_handout_rel_course.' set handout_name = '.$this->dao->quote(SysUtil::safeString($arr['handout_name'])).',
																	handout_url = '.$this->dao->quote(SysUtil::safeString($arr['handout_url'])).',
																	 updtime = '.$this->dao->quote(date("Y-m-d H:i:s")).'
																	   where course_id = '.$this->dao->quote($course_id).'
																	    and hid ='.$this->dao->quote($hid);
		return  $this->dao->execute($strQuery);
	}
	/*zhaohaibing 结束 */



	public function get_orderList($condition='', $currentPage=1, $pageSize=20){
		$count = $this->get_orderCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT o.*,u.username FROM ' . $this->gss_order . ' o
										   LEFT JOIN '. $this->tableName .' u ON o.uid = u.id   
										   WHERE 1=1 ';
		if($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		$order = ' ORDER BY o.oid DESC';
		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}


	public function get_orderCount($condition){
		$strQuery = 'SELECT count(1) FROM ' . $this->gss_order . ' o
									 LEFT JOIN '. $this->tableName .' u ON o.uid = u.id 
									 WHERE 1=1 ';
		if ($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		return $this->dao->getOne($strQuery);
	}


	public function get_orderAll($condition){
		$strQuery = 'SELECT o.*,u.username FROM ' . $this->gss_order . ' o
										   LEFT JOIN '. $this->tableName .' u ON o.uid = u.id 
										   WHERE 1=1 ';
		if($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		$strQuery .= ' ORDER BY o.oid DESC';
		return $this->dao->getAll($strQuery);
	}


	public function  get_orderInfo($oid){
		if(!empty($oid)){
			$row = $this->dao->getRow('SELECT o.*,u.username FROM ' . $this->gss_order . ' o
															 LEFT JOIN '. $this->tableName .' u ON o.uid = u.id 
															 WHERE o.oid='.$this->dao->quote($oid));

			if(!empty($row['course_ids'])){
				$course_arr = explode(',',trim($row['course_ids'],','));
				$course_id_str = "'".implode("','",$course_arr)."'";
				$row['course_arr'] = $this->dao->getAll('SELECT * FROM ' . $this->gss_course . ' WHERE id IN ('.$course_id_str.')');
			}

			if(!empty($row['pack_ids'])){
				$pack_arr = explode(',',trim($row['pack_ids'],','));
				$pack_id_str = "'".implode("','",$pack_arr)."'";
				$strQuery = 'SELECT pname,pid,course_id_str,price,coupon_type,coupon_value,is_give_book from '.$this->gss_course_pack.' where pid in('.$pack_id_str.')';
				$packList = $this->dao->getAll($strQuery);

				foreach($packList as $key=>$value){
					$tempRealPrice = $value['coupon_type'] == 0?$value['price']-$value['coupon_value']:$value['price']*$value['coupon_value']/100;
					$packPrice += $tempRealPrice;
					$packList[$key]['real_price'] = $tempRealPrice;
				}

				$row['pack_arr'] = $packList;
			}

			return $row;
		}
		return false;
	}

	public function importUser($arr){
		if(!empty($arr)){
			$strQuery = 'INSERT INTO '.$this->tableName.' (username,
									  password,
									  email,
									  nickname,
									  phone,
									  student_name 
							) VALUES ('.$this->dao->quote($arr[0]).',
									  '.$this->dao->quote(md5('12345678')).',
									  '.$this->dao->quote($arr[2]).',
									  '.$this->dao->quote(mb_convert_encoding($arr[1],'utf-8','gb2312')).',
									  '.$this->dao->quote($arr[4]).',
									  '.$this->dao->quote(mb_convert_encoding($arr[3],'utf-8','gb2312')).')';
			if($this->dao->execute($strQuery)){
				return true;
			}
			return false;
		}
		return false;
	}


	public function importAccount($arr){
		if(!empty($arr[3])){
			if($this->dao->execute('UPDATE '.$this->tableName.' SET account_money = '.$this->dao->quote($arr[3]) .' WHERE username = '.$this->dao->quote($arr[0]))){
				return true;
			}
			return false;
		}
		return false;
	}


	public function getCourseInfoByName($course_name){
		if(!empty($course_name)){
			return $this->dao->getRow('SELECT * FROM '.$this->gss_course.' WHERE course_name = '.$this->dao->quote($course_name));
		}
		return false;
	}


	public function getHelpList(){
		return $this->dao->getAll('SELECT h.*,ht.name as type_name FROM '.$this->gss_help.' h LEFT JOIN '.$this->gss_help_type.' ht ON h.type_id = ht.type_id ');
	}


	public function getHelpInfo($hid){
		if(!empty($hid)){
			return $this->dao->getRow('SELECT * FROM '.$this->gss_help.' WHERE hid = '.$this->dao->quote($hid));
		}
		return false;

	}


	public function updateHelpInfo($arr){
		if(!empty($arr['hid']) && !empty($arr['title']) && !empty($arr['content'])){
			if($this->dao->execute('UPDATE '.$this->gss_help.' SET title = '.$this->dao->quote(SysUtil::safeString($arr['title'])).' , content = '.$this->dao->quote($arr['content']).' WHERE hid = '.$this->dao->quote($arr['hid']))){
				return true;
			}
			return false;
		}
		return false;
	}


	public function importCourse($arr){
		if(!empty($arr['course_name'])){
			$is_free = 0;
			if($arr['price'] == 0){
				$is_free = 1;
			}
			if($this->dao->execute('INSERT INTO '.$this->gss_course.' (course_name,price,is_free,instime) VALUES ('.$this->dao->quote($arr['course_name']).','.$this->dao->quote($arr['price']).','.$this->dao->quote($is_free).','.$this->dao->quote($arr['instime']).') ')){
				return true;
			}
			return false;
		}
		return false;
	}


	public function importTeacher($arr){
		if(!empty($arr)){
			$arr['gid'] = $this->dao->getOne('SELECT gid FROM '.$this->gss_grade.' WHERE title = '.$this->dao->quote($arr['grade']));
			$arr['sid'] = $this->dao->getOne('SELECT sid FROM '.$this->gss_subject.' WHERE gid = '.$this->dao->quote($arr['gid']).' AND alias2 = '.$this->dao->quote($arr['subject']));
			$strQuery = 'INSERT INTO '.$this->gss_teachers.' (  realname,
																gid ,                             
																sid,                            
																send_word,                        
																of_educate_age,                 
																intro_content,                         
																teaching_style  ,                        
																experience_content,                       
																comment,
																instime) 
														VALUES ('.$this->dao->quote($arr['realname']).',
																'.$this->dao->quote($arr['gid']).',
																'.$this->dao->quote($arr['sid']).',
																'.$this->dao->quote($arr['send_word']).',
																'.$this->dao->quote($arr['of_educate_age']).',
																'.$this->dao->quote($arr['intro_content']).',
																'.$this->dao->quote($arr['teaching_style']).',
																'.$this->dao->quote($arr['experience_content']).',
																'.$this->dao->quote($arr['comment']).',
																'.$this->dao->quote($arr['instime']).' 
																)';
			if($this->dao->execute($strQuery)){
				return true;
			}
			return false;
		}
		return false;
	}


	public function openCourse($arr,$user_key){
		if(!empty($arr['course_id']) && !empty($arr['uid']) && !empty($arr['endtime']) && !empty($user_key)){
			$strQuery = 'INSERT INTO '.$this->gss_user_course.' (uid,course_id,endtime,instime,operator) VALUES ('.$this->dao->quote($arr['uid']).','.$this->dao->quote($arr['course_id']).','.$this->dao->quote($arr['endtime'].' 23:59:59').','.$this->dao->quote(date('Y-m-d H:i:s')).','.$this->dao->quote($user_key).')';
			if($this->dao->execute($strQuery)){
				return true;
			}
			return false;
		}
		return false;
	}


	public function checkCCVidIsExist($ccVid, $courseId = 0){
		$cid = $this->dao->getOne('SELECT course_id FROM '.$this->gss_video_rel_course.' WHERE cc_vid = '.$this->dao->quote($ccVid));
		if($cid){
			if($courseId != 0 && $cid == $courseId){
				return false;
			}
			return true;
		}
		return false;
	}


	public function get_course_list_by_cid($cid){
		$strQuery = "SELECT id,course_name,price  from ".$this->gss_course." WHERE cid = '$cid'
								ORDER BY id DESC ";
		return $this->dao->getAll($strQuery);
	}


	public function checkPackIsExist($pname,$pid = 0){
		$pack_id = $this->dao->getOne('SELECT pid FROM '.$this->gss_course_pack.' WHERE pname = '.$this->dao->quote($pname));
		if($pack_id){
			if($pid != 0 && $pack_id == $pid){
				return false;
			}
			return true;
		}
		return false;
	}

	public function checkCourseIsExist($course_name,$course_id = 0){
		$cid = $this->dao->getOne('SELECT id FROM '.$this->gss_course.' WHERE course_name = '.$this->dao->quote($course_name));
		if($cid){
			if($course_id != 0 && $course_id == $cid){
				return false;
			}
			return true;
		}
		return false;
	}


	public function dimission_teacher($tid){
		if($this->dao->execute('UPDATE '.$this->gss_teachers.' SET is_onjob = 0 WHERE tid = '.$this->dao->quote(abs($tid)))){
			return true;
		}
		return false;
	}


	public function delete_video($vid){
		if(!empty($vid)){
			if($this->dao->execute('DELETE FROM '.$this->gss_video_rel_course.' WHERE vid = '.$this->dao->quote($vid))){
				return true;
			}
			return false;
		}
		return false;
	}


	public function delete_handout($hid){
		if($this->dao->execute('DELETE FROM '.$this->gss_handout_rel_course.' WHERE hid = '.$this->dao->quote($hid))){
			return true;
		}
		return false;
	}
	
	
	public function addEmail($email){
		if(!empty($email)){
			if($this->dao->execute('INSERT INTO '.$this->gss_emails.' (email,instime) VALUES('.$this->dao->quote($email).','.$this->dao->quote(date('Y-m-d H:i:s')).')')){
				return true;
			}
			return false;
		}
		return false;
	}
	
	public function get_emailList($condition='', $currentPage=1, $pageSize=20){
		$count = $this->get_emailCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT * FROM ' . $this->gss_emails . ' WHERE 1=1 ';
		if($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		$order = ' ORDER BY instime DESC';
		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}


	public function get_emailCount($condition){
		$strQuery = 'SELECT count(1) FROM ' . $this->gss_emails . ' WHERE 1=1 ';
		if ($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		return $this->dao->getOne($strQuery);
	}
	
	
	public function deleteEmail($eid){
		if(!empty($eid)){
			if($this->dao->execute('DELETE FROM '.$this->gss_emails.' WHERE eid = '.$this->dao->quote($eid))){
				return true;
			}
			return false;
		}
		return false;
	}
}
?>