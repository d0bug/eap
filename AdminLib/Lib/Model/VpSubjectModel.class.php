<?php

class VpSubjectModel extends Model {
	public  $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao();
		$this->tableName = 'vp_subject';
		$this->gradeTable = 'vp_grade';
		$this->relationshipTable = 'vp_subject_grade_relationship';
		$this->knowledgeTable = 'vp_knowledge';
		$this->knowledgePermissionTable = 'vp_knowledge_permission';
		$this->user_subjects = 'vp_user_subjects';
		$this->sys_app_admins = 'sys_app_admins';
		$this->vp_handouts = 'vp_handouts';


		$this->vp_comment_dimension = 'vp_comment_dimension';
		$this->vp_comment_level = 'vp_comment_level';
		$this->vp_comment_text = 'vp_comment_text';
		$this->vp_subject_dimension_rs = 'vp_subject_dimension_rs';
		$this->vp_words = 'vp_words';

		$this->dao2 = Dao::getDao('MYSQL_CONN_KNOWLEDGE');
		$this->vip_course = 'vip_courses';
		$this->vip_knowledgetype_course_rs = 'vip_knowledgetype_course_rs';
		$this->vip_dict_subject="vip_dict_subject";
	}


	public function get_subjectLists($gradeid = '',$userKey){
		$strQuery = "SELECT [sid],[name],[type] FROM ".$this->tableName." WHERE 1=1 ";
		if($type!==''){
			$strQuery .= " AND [type] = '$type' ";
		}
		if($userKey){
			//判断用户是否为管理员
			if(!VipCommAction::checkIsAdmin($userKey)){
				$sidStr = $this->get_thisuser_sidsStr($userKey);
				$strQuery .= " AND sid IN ($sidStr)";
			}
		}
		$strQuery .= " ORDER BY type ASC ";
		$subjectArrList=$this->dao->getAll($strQuery);

		$str='';
		foreach($subjectArrList as $k => $v)
		{
			$str.="'".$v['sid']."',";
		}
		$str=trim($str,',');

		$sql="select id,title from ".$this->vip_dict_subject." where eap_subject_id in (".$str.")";
		if(!empty($gradeid))
		{
			$sql.=" and grade_id=".$gradeid;
		}

		$sql.=" order by sort asc";
		return $this->dao2->getAll($sql); 
	}

	public function get_subjectList($type = '',$userKey){
		$strQuery = "SELECT [sid],[name],[type] FROM ".$this->tableName." WHERE 1=1 ";
		if($type!==''){
			$strQuery .= " AND [type] = '$type' ";
		}
		if($userKey){
			//判断用户是否为管理员
			if(!VipCommAction::checkIsAdmin($userKey)){
				$sidStr = $this->get_thisuser_sidsStr($userKey);
				$strQuery .= " AND sid IN ($sidStr)";
			}
		}
		$strQuery .= " ORDER BY type ASC ";
		return $this->dao->getAll($strQuery);
	}

	public function add_subject($arr){
		$this->dao->execute('INSERT INTO '.$this->tableName.' ([name],[type]) VALUES('.$this->dao->quote($arr[name]).','.$arr['type'].')');
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}

	public function get_subjectname_by_sid($sid){
		return $this->dao->getOne("SELECT [name] FROM ".$this->tableName." WHERE [sid] = '$sid'");
	}

	public function get_count($condition){
		$strQuery = 'SELECT count(1) FROM ' . $this->tableName . ' WHERE 1=1 ';
		if ($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		$count = $this->dao->getOne($strQuery);
		return $count;
	}

	public function add_userSubject($userKeyArr,$sidStr){
		if(!empty($userKeyArr)){
			foreach ($userKeyArr as $key=>$userKey){
				if($this->dao->getOne("SELECT [id] FROM ".$this->user_subjects." WHERE [user_key] = '$userKey'")){
					$this->dao->execute("UPDATE ".$this->user_subjects." SET [sids] = '$sidStr' WHERE [user_key] = '$userKey'");
				}else{
					$this->dao->execute("INSERT INTO ".$this->user_subjects." ([user_key],[sids]) VALUES('$userKey','$sidStr')");
				}
				$this->addSyncTask($userKey);
			}
			if($this->dao->affectRows()){
				return true;
			}
			return false;
		}
		return false;
	}

	protected function addSyncTask($userKey) {
		static $redis;
		if(false == $redis) {
			$redis = new Redis();
			$redis->connect('127.0.0.1', 6379);
			$redis->select(5);
		}
		$key = 'TEACHER_SYNC_TASK';
		$redis->rpush($key, $userKey);
	}

	/*删除科目*/
	public function delete_subject($subjectIdArr){
		foreach ($subjectIdArr as $key=>$val){
			$childGradeStr = $this->dao->getOne('SELECT gids FROM '.$this->relationshipTable.' WHERE sid = '.$val);
			if(!empty($childGradeStr)){
				$childGradeStr = "'".implode("','",explode(',',trim($childGradeStr,',')))."'";
			}
			$childKnowledgeArr = $this->dao->getAll('SELECT kid FROM '.$this->knowledgePermissionTable.' WHERE sid = '.$val);
			//删除下属知识点
			if(!empty($childKnowledgeArr)){
				$childKnowledgeStr = '';
				foreach ($childKnowledgeArr as $kk=>$knowledge){
					$childKnowledgeStr .= $knowledge['kid'].',';
				}
				$childKnowledgeStr =  "'".implode("','",explode(',',trim($childKnowledgeStr,',')))."'";
				if(!empty($childKnowledgeStr)){
					$this->dao->execute('DELETE FROM '.$this->knowledgeTable.' WHERE kid IN ('.$childKnowledgeStr.')');
				}
			}

			//删除知识点权限关系
			$this->dao->execute('DELETE FROM '.$this->knowledgePermissionTable.' WHERE sid = '.$val);
			//删除下属课程属性
			if(!empty($childGradeStr)){
				$this->dao->execute('DELETE FROM '.$this->gradeTable.' WHERE gid IN ('.$childGradeStr.')');
			}
			//删除该科目相关的科目、课程属性关系
			$this->dao->execute('DELETE FROM '.$this->relationshipTable.' WHERE sid = '.$val);

			//删除该科目下的讲义
			$this->dao->execute("UPDATE ".$this->vp_handouts." SET [is_delete] = '1' WHERE sid = '$val'");

			//删除该科目
			$this->dao->execute("DELETE FROM ".$this->tableName." WHERE sid = '$val'");

		}
		if($this->dao->affectRows()){
			return 1;
		}
		return 0;
	}

	/*删除课程属性*/
	public function delete_grade($gradeIdArr,$sid=''){
		foreach ($gradeIdArr as $key=>$gradeId){
			//删除科目与课程属性关系中相关此课程属性的信息
			$strQuery = "SELECT rid,gids FROM ".$this->relationshipTable." WHERE gids LIKE '%".$gradeId.",%'";
			if(!empty($sid)){
				$strQuery .= " AND sid = '$sid' ";
			}
			$relationshipGidsArr = $this->dao->getAll($strQuery);
			if(!empty($relationshipGidsArr)){
				foreach ($relationshipGidsArr as $kk=>$gids){
					if(strpos(','.$gids['gids'],','.$gradeId.',')!==false){
						$newGidsStr = str_replace(','.$gradeId.',',',',','.$gids['gids']);
						$newGidsStr = ltrim($newGidsStr,',');
						$strQuery4 = "UPDATE ".$this->relationshipTable." SET gids = '$newGidsStr' WHERE rid = '$gids[rid]'";
						if(!empty($sid)){
							$strQuery4 .= " AND sid = '$sid'";
						}
						$this->dao->execute($strQuery4);
					}
				}
			}

			//删除相关课程讲义
			$strQuery3 = "UPDATE ".$this->vp_handouts." SET is_delete = '1' WHERE gid = '$gradeId' ";
			if(!empty($sid)){
				$strQuery3 .= " AND sid = '$sid' ";
			}
			$this->dao->execute($strQuery3);

			$relationshipGidsArr = $this->dao->getAll("SELECT rid,gids FROM ".$this->relationshipTable." WHERE gids LIKE '%".$gradeId.",%'");
			if(empty($relationshipGidsArr)){
				//删除课程属性信息
				$this->dao->execute("DELETE FROM ".$this->gradeTable." WHERE gid = '$gradeId'");
			}

			//删除知识点权限关系，但不删除对应知识点(因：一个知识点可对应多个课程属性)
			$strQuery2 = "DELETE FROM ".$this->knowledgePermissionTable." WHERE gid = '$gradeId'";
			if(!empty($sid)){
				$strQuery2 .= " AND sid = '$sid' ";
			}
			$this->dao->execute($strQuery2);

		}
		return 1;

	}

	/*删除讲义属性*/
	public function delete_knowledge($knowledgeIdArr,$sid='',$gidArr){
		foreach ($knowledgeIdArr as $key=>$knowledgeId){
			//删除知识点权限关系
			$strQuery = "DELETE FROM ".$this->knowledgePermissionTable." WHERE kid = '$knowledgeId' ";
			if(!empty($sid)){
				$strQuery .= " AND sid = '$sid' ";
			}
			if(!empty($gidArr)){
				$gidStr = "'".implode("','",$gidArr)."'";
				$strQuery .= " AND gid IN ($gidStr) ";
			}
			$this->dao->execute($strQuery);

			//删除相关讲义
			$strQuery2 = "UPDATE ".$this->vp_handouts." SET is_delete = '1' WHERE kid = '$knowledgeId' ";
			if(!empty($sid)){
				$strQuery2 .= " AND sid = '$sid' ";
			}
			if(!empty($gidArr)){
				$gidStr = "'".implode("','",$gidArr)."'";
				$strQuery2 .= " AND gid IN ($gidStr) ";
			}
			$this->dao->execute($strQuery2);

			//删除知识点
			$count = $this->dao->getOne("SELECT COUNT(*) FROM ".$this->knowledgePermissionTable." WHERE kid = '$knowledgeId' ");
			if((empty($sid) && empty($gidArr)) || $count < 1){
				$this->dao->execute("DELETE FROM ".$this->knowledgeTable." WHERE kid = '$knowledgeId'");
			}
		}
		if($this->dao->affectRows()){
			return 1;
		}
		return 0;
	}

	/*编辑名称（科目、课程属性、讲义属性）*/
	public function editName($type,$name,$id){
		switch ($type){
			case 'subject':
				$strQuery = "UPDATE ".$this->tableName." SET [name]=".$this->dao->quote($name)." WHERE [sid] = '$id'";
				break;
			case 'grade':
				$strQuery = "UPDATE ".$this->gradeTable." SET [name]=".$this->dao->quote($name)." WHERE [gid] = '$id'";
				break;
			case 'knowledge':
				$strQuery = "UPDATE ".$this->knowledgeTable." SET [name]=".$this->dao->quote($name)." WHERE [kid] = '$id'";
				break;
		}
		$this->dao->execute($strQuery);
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}

	public function get_thisuser_sidsStr($userKey){
		$sidStr = $this->dao->getOne("SELECT [sids] FROM ".$this->user_subjects." WHERE [user_key]='$userKey'");
		$sidStr = "'".implode("','",explode(',',trim($sidStr,',')))."'";
		return $sidStr;
	}

	public function get_subjectNameList_by_sids($sidsStr){
		$returnStr = '';
		$subjectNameList = $this->dao->getAll("SELECT [name] FROM ".$this->tableName." WHERE [sid] IN ($sidsStr)");
		if(!empty($subjectNameList)){
			foreach ($subjectNameList as $key=>$subject){
				$returnStr .= $subject['name'].',';
			}
		}else{
			$returnStr .= '暂无授权';
		}
		return trim($returnStr,',');
	}

	public function get_all_subjectName($user_key_str){
		$subjectNameList = $this->dao->getAll("SELECT [name],[sid] FROM ".$this->tableName);
		$subject_all_array=array();
		foreach($subjectNameList as $key=>$value){
			$subject_all_array[$value['sid']] = $value['name'];
		}
		$subject_name = $this->dao->getAll("SELECT [sids],[user_key] FROM ".$this->user_subjects." WHERE [user_key] in(".$user_key_str.")");
		$subject_name_array=array();
		foreach($subject_name as $key=>$value){
			if($value['sids'] == ''){
				$subject_name_array[$value['user_key']] = '暂无授权';
			}else{
				foreach(explode(',',$value['sids']) as $k=>$val){
					$subject_name_array[$value['user_key']] .= $subject_all_array[$val].',';
				}
			}
		}
		return 	$subject_name_array;
	}

	public function get_subjectType_by_sid($sid){
		return $this->dao->getOne("SELECT [type] FROM ".$this->tableName." WHERE [sid]='$sid'");
	}



	//教师端云讲义话术管理===============================================================================================================
	public function get_dimensionList($arr = array()){
		if(empty($arr)){
			$strQuery = 'SELECT id as dimension_id,title as dimension_name  FROM '.$this->vp_comment_dimension ;
		}else{
			$strQuery = 'SELECT r.*,d.title as dimension_name FROM '.$this->vp_subject_dimension_rs.' r LEFT JOIN '.$this->vp_comment_dimension.' d ON r.dimension_id = d.id WHERE 1 = 1 ' ;
			if(!empty($arr['sid'])){
				$strQuery .= ' AND r.sid = '.$this->dao->quote(abs($arr['sid']));
			}
		}

		return $this->dao->getAll($strQuery);
	}


	public function get_levelList(){
		return $this->dao->getAll('SELECT * FROM '.$this->vp_comment_level);
	}


	public function get_commentTextList($arr){
		$strQuery = 'SELECT * FROM '.$this->vp_comment_text.' WHERE 1=1 ';
		if(!empty($arr['sid'])){
			$strQuery .= ' AND sid = '.$this->dao->quote($arr['sid']);
		}
		if(!empty($arr['dimension_id'])){
			$strQuery .= ' AND dimension_id = '.$this->dao->quote($arr['dimension_id']);
		}
		if(!empty($arr['level_id'])){
			$strQuery .= ' AND level_id = '.$this->dao->quote($arr['level_id']);
		}
		return $this->dao->getAll($strQuery);
	}


	public function delete_commentText(){
		if(!empty($_POST['comment_id_str'])){
			$commentIdStr = "'".implode("','",explode(',',trim($_POST['comment_id_str'],',')))."'";
			if($this->dao->execute("DELETE FROM ".$this->vp_comment_text." WHERE id IN ($commentIdStr)")){
				return true;
			}
			return false;
		}
		return false;
	}


	public function add_commentText(){
		if(!empty($_POST['text']) && !empty($_POST['sid']) && !empty($_POST['dimension_id']) && !empty($_POST['level_id'])){
			if($this->dao->execute("INSERT INTO ".$this->vp_comment_text." ([sid],[dimension_id],[level_id],[text]) VALUES ('$_POST[sid]','$_POST[dimension_id]','$_POST[level_id]','$_POST[text]')")){
				return true;
			}
			return false;
		}
		return false;
	}


	public function edit_CommentType(){
		if(!empty($_POST['name']) && !empty($_POST['id']) && !empty($_POST['type'])){
			$strQuery = 'UPDATE ';
			if($_POST['type'] == 'dimension'){
				$strQuery .= $this->vp_comment_dimension;
			}else{
				$strQuery .= $this->vp_comment_level;
			}
			$strQuery .= ' SET title = '.$this->dao->quote(SysUtil::safeString($_POST['name']));
			if($_POST['type'] == 'level'){
				$strQuery .= ' , rank = '.$this->dao->quote(abs($_POST['rank']));
			}
			$strQuery .= ' WHERE id = '.$this->dao->quote(abs($_POST['id']));
			if($this->dao->execute($strQuery)){
				return true;
			}
			return false;
		}
		return false;
	}


	public function get_templateList(){
		$strQuery = 'SELECT * FROM '.$this->vp_words.' WHERE 1 = 1 ';
		if(!empty($_GET['sid'])){
			$strQuery .= ' AND sid = '.$this->dao->quote(abs($_GET['sid']));
		}
		return $this->dao->execute($strQuery);
	}


	public function delete_commentTemplate($template_id){
		if(!empty($template_id)){
			if($this->dao->execute('DELETE FROM '.$this->vp_words.' WHERE id = '.$this->dao->quote(abs($template_id)))){
				return true;
			}
			return false;
		}
		return false;
	}


	public function add_commentTemplate(){
		if(!empty($_POST['sid']) && !empty($_POST['text'])){
			if($this->dao->execute('INSERT INTO '.$this->vp_words.' ([text],[sid]) VALUES ('.$this->dao->quote($_POST['text']).','.$this->dao->quote($_POST['sid']).')')){
				return true;
			}
			return false;
		}
		return false;
	}


	public function get_templatePreview($template_id,$level_arr){
		if(!empty($template_id)){
			$templateInfo = $this->dao->getRow('SELECT * FROM '.$this->vp_words.' WHERE id = '.$this->dao->quote($template_id));
			if(!empty($templateInfo)){
				//preg_match_all('/(\[)(.*)(\])/',$templateInfo['text'],$matchArr);
				$dimensionArr = $this->get_dimensionList();
				if(!empty($level_arr)){
					if(!empty($dimensionArr)){
						foreach ($dimensionArr as $key=>$dimension){
							$fromStr = '[评价:'.trim($dimension['dimension_name']).']';
							$toStr = $this->dao->getOne('SELECT TOP 1 text FROM '.$this->vp_comment_text.' WHERE sid = '.$this->dao->quote($templateInfo['sid']).' AND dimension_id = '.$this->dao->quote($dimension['dimension_id']).' AND level_id = '.$this->dao->quote($level_arr[$key]).' ORDER BY NewID()');
							$templateInfo['text'] = str_replace($fromStr,$toStr,$templateInfo['text']);
						}
						return $templateInfo['text'];
					}
					return false;
				}else{
					$maxLevelId = $this->dao->getOne('SELECT TOP 1 id FROM '.$this->vp_comment_level.' ORDER BY rank DESC,id DESC');
					if(!empty($dimensionArr)){
						foreach ($dimensionArr as $key=>$dimension){
							$fromStr = '[评价:'.trim($dimension['dimension_name']).']';
							$toStr = $this->dao->getOne('SELECT TOP 1 text FROM '.$this->vp_comment_text.' WHERE sid = '.$this->dao->quote($templateInfo['sid']).' AND dimension_id = '.$this->dao->quote($dimension['dimension_id']).' AND level_id = '.$this->dao->quote($maxLevelId).' ORDER BY NewID()');
							$templateInfo['text'] = str_replace($fromStr,$toStr,$templateInfo['text']);
						}
						return $templateInfo['text'];
					}
					return false;
				}

			}
			return false;
		}
		return false;
	}



	public function add_commentType($arr){
		if(!empty($arr['sid']) && !empty($arr['title'])){
			$dimension_id = $this->get_dimensionIdByTitle($arr['title']);
			if(empty($dimension_id)){
				$this->dao->execute('INSERT INTO '.$this->vp_comment_dimension.' (title) VALUES ('.$this->dao->quote(trim($arr['title'])).')');
				$dimension_id = $this->get_dimensionIdByTitle($arr['title']);
			}
			if(!empty($dimension_id)){
				$count = $this->dao->getOne('SELECT COUNT(1) FROM '.$this->vp_subject_dimension_rs.' WHERE sid = '.$this->dao->quote($arr['sid']).' AND dimension_id = '.$this->dao->quote($dimension_id));
				if($count>0){
					return false;
				}else{
					if($this->dao->execute('INSERT INTO '.$this->vp_subject_dimension_rs.' (sid,dimension_id) VALUES ('.$this->dao->quote($arr['sid']).','.$this->dao->quote($dimension_id).')')){
						return true;
					}
					return false;
				}
			}
			return false;
		}
		return false;
	}


	public function get_dimensionIdByTitle($title){
		return $this->dao->getOne('SELECT id FROM '.$this->vp_comment_dimension.' WHERE title = '.$this->dao->quote(trim($title)));
	}



	public function add_level($arr){
		$count = $this->dao->getOne('SELECT COUNT(1) FROM '.$this->vp_comment_level.' WHERE title = '.$this->dao->quote($arr['title']));
		if($count==0){
			return $this->dao->execute('INSERT INTO '.$this->vp_comment_level.' (title,rank) VALUES ('.$this->dao->quote($arr['title']).','.$this->dao->quote($arr['rank']).')');
		}
		return false;

	}


	public function delete_commentType($arr){
		if(!empty($arr['id']) && !empty($arr['type']) ){
			if($arr['type'] == 'dimension'){
				$strQuery = 'DELETE FROM '.$this->vp_comment_dimension.' WHERE id = '.$this->dao->quote($arr['id']);
				$strQuery2 = 'DELETE FROM '.$this->vp_subject_dimension_rs.' WHERE dimension_id = '.$this->dao->quote($arr['id']);
				$strQuery3 = 'DELETE FROM '.$this->vp_comment_text.' WHERE dimension_id = '.$this->dao->quote($arr['id']);
				$this->dao->begin();
				$success1 = (boolean)$this->dao->execute($strQuery);
				$success2 = (boolean)$this->dao->execute($strQuery2);
				$success3 = (boolean)$this->dao->execute($strQuery3);
				if($success1 == true && $success2 == true && $success3 == true ){
					$this->dao->commit();
					return true;
				}
				$this->dao->rollback();
				return false;
			}else if($arr['type'] == 'level'){
				$strQuery = 'DELETE FROM '.$this->vp_comment_level.' WHERE id = '.$this->dao->quote($arr['id']);
				$strQuery2 = 'DELETE FROM '.$this->vp_comment_text.' WHERE level_id = '.$this->dao->quote($arr['id']);
				$this->dao->begin();
				$success1 = (boolean)$this->dao->execute($strQuery);
				$success2 = (boolean)$this->dao->execute($strQuery2);
				if($success1 == true && $success2 == true ){
					$this->dao->commit();
					return true;
				}
				$this->dao->rollback();
				return false;
			}
			return false;
		}
		return false;
	}



	public function edit_commentTemplate($id, $content){
		if(!empty($id) && !empty($content)){
			if($this->dao->execute('UPDATE '.$this->vp_words.' SET text = '.$this->dao->quote($content).' WHERE id = '.$this->dao->quote(abs($id)))){
				return true;
			}
			return false;
		}
		return false;
	}




	//piv4.0新课程体系====start================================================================================================
	public function getPath($id, $type = 'course') {
		$path = array ();
		if ($type == 'course') {
			$nav = $this->getCourseByID ( $id );
		}
		$path [] = $nav;
		if ($nav ['parent_id'] > 0) {
			$path = array_merge ( $this->getPath ( $nav ['parent_id'] ), $path );
		}

		return $path;
	}

	public function getCourseByID($id) {
		return $this->dao2->getRow ( 'SELECT a.`id`,
											a.`name`,
											a.`sort`,
											a.`parent_id`,
											a.`status`,
											a.is_leaf,
											a.level,
											b.`name` as parent_name  
									FROM ' . $this->vip_course . ' a 
									LEFT JOIN  ' . $this->vip_course . ' b ON a.parent_id = b.id 
									WHERE a.id = ' . $this->dao->quote ( $id ) );
	}


	public function getCoursesByParentId($parentId, $knowledgeTypeId) {
		$where = '';
		if (empty ( $parentId )) {
			$where = ' AND a.parent_id = 0';
			if (! empty ( $knowledgeTypeId )) {
				$where .= ' AND rs.knowledge_type_id = ' . $this->dao->quote ( $knowledgeTypeId );
			} else {
				return array ();
			}
		} else {
			$where = ' AND a.parent_id = ' . $this->dao->quote ( $parentId );
		}

		return $this->dao2->getAll ( 'SELECT a.`id`,
											a.`name`,
											a.`sort`,
											a.`parent_id`,
											a.`status`,
											(case `a`.`is_leaf` when 0 then \'closed\' else \'\' end) AS `state`,
											a.`is_leaf`,
											a.`level` 
									FROM ' . $this->vip_course . ' a 
									LEFT JOIN '.$this->vip_knowledgetype_course_rs. ' rs ON a.id = rs.course_id 
									WHERE a.status = 1 ' . $where . ' ORDER BY a.sort, a.id' );
	}


	public function getCoursesByParentId1($parentId, $knowledgeTypeId) {
		$where = '';
		if (empty ( $parentId )) {
			$where = ' AND a.parent_id = 0';
			if (! empty ( $knowledgeTypeId )) {
				$where .= ' AND rs.knowledge_type_id = ' . $this->dao->quote ( $knowledgeTypeId );
			} else {
				return array ();
			}
		} else {
			$where = ' AND a.parent_id = ' . $this->dao->quote ( $parentId );
		}


		return $this->dao2->getAll ( 'SELECT a.`id`,
											a.`name`,
											a.`sort`,
											a.`parent_id`,
											a.`status`,
											(case `a`.`is_leaf` when 0 then \'closed\' else \'\' end) AS `state`,
											a.is_leaf,
											a.level 
									FROM ' . $this->vip_course . ' a 
									LEFT JOIN '.$this->vip_knowledgetype_course_rs. ' rs ON a.id = rs.course_id 
									WHERE a.status = 1 ' . $where . ' ORDER BY a.sort, a.id' );
	}



	public function addCourse($course = array()) {
		$parentId = $course ['parent_id'];
		$flag = true;
		$this->dao2->execute ( 'begin' ); // 事务开启
		if (! empty ( $parentId )) {
			$sql = 'UPDATE ' . $this->vip_course . ' SET is_leaf = 0 WHERE id = ' . $this->dao->quote ( $parentId ) . ' AND is_leaf = 1';
			if ($this->dao2->execute ( $sql ))
			$flag = true;
			else
			$flag = false;
		}

		if ($flag == true) {
			$sql2 = 'INSERT INTO ' . $this->vip_course . ' (name,  parent_id,  sort, is_leaf, level) VALUES (' . $this->dao->quote ( $course ['name'] ) . ', ' . $this->dao->quote ( $parentId ) . ', ' . $this->dao->quote ( $course ['sort'] ) . ', 1, ' . $this->dao->quote ( $course ['level'] ).')' ;
			if ($this->dao2->execute ( $sql2 )) {
				$id = $this->dao2->lastInsertId ();
				$flag = true;
			} else
			$flag = false;
		}

		// 如为父节点则插入课程体系属性表
		if (empty ( $parentId ) && $flag == true) {
			$sql3 = 'INSERT INTO ' . $this->vip_knowledgetype_course_rs . ' ( course_id, knowledge_type_id ) VALUES (' . $this->dao->quote ( $id ) . ', ' . $this->dao->quote ( $course ['knowledgetypeid'] ) . ')';

			if ($this->dao2->execute ( $sql3 ))
			$flag = true;
			else
			$flag = false;
		}

		if ($flag === false)
		$this->dao2->execute ( 'rollback' ); // 事务回滚
		else
		$this->dao2->execute ( 'commit' ); // 事务提交

		return $flag;
	}


	public function getCoursesByParentIdChild($parentId, $knowledgeTypeId) {
		$where = '';
		if (empty ( $parentId )) {
			$where = ' AND a.parent_id = 0';
			if (! empty ( $knowledgeTypeId )) {
				$where .= ' AND rs.knowledge_type_id = ' . $this->dao->quote ( $knowledgeTypeId );
			} else {
				return array ();
			}
		} else {
			$where = ' AND a.parent_id = ' . $this->dao->quote ( $parentId );
		}

		return $this->dao2->getAll ( 'SELECT a.`id`,
											a.`name` as text,
											a.`status`,
											a.`state`,
											a.`is_leaf`
									FROM ' . $this->vip_course . ' a 
									LEFT JOIN '.$this->vip_knowledgetype_course_rs. ' rs ON a.id = rs.course_id 
									WHERE a.status = 1 ' . $where . ' ORDER BY a.sort, a.id' );
	}



	public function updateCourse($course = array()) {
		$courseId = $course ['id'];
		$parentId = $course ['parent_id'];
		$row = $this->dao2->getRow ( 'SELECT fn_vip_get_course_child_list(' . $this->dao->quote ( $courseId ) . ') AS sub_course_ids' );

		if ($row) {
			if (in_array ( $parentId, str2arr ( $row ['sub_course_ids'], ',' ) )) {
				return false;
			}
		}

		if (! empty ( $courseId )) {
			$before_parentId = $this->dao2->getRow ( 'SELECT `id`,`parent_id` FROM ' . $this->vip_course . ' WHERE id = ' . $this->dao->quote ( $courseId ) ); // 查找修改前的父目录的ID
			if (! empty ( $before_parentId ['parent_id'] )) {
				if ($before_parentId ['parent_id'] != $parentId) {
					$row = $this->dao2->getRow ( 'SELECT `id`,`parent_id` FROM ' . $this->vip_course . ' WHERE id != ' . $this->dao->quote ( $courseId ) . ' AND  parent_id = ' . $this->dao->quote ( $before_parentId ['parent_id'] ) );
					if (empty ( $row )) { // 查找对应的父节点除了此节点之外还有没有子节点，如果无则修改叶子节点
						$sql = 'UPDATE ' . $this->vip_course . ' SET is_leaf = 1 WHERE id = ' . $this->dao->quote ( $before_parentId ['parent_id'] );
						$this->dao2->execute ( $sql );
					}
				}
			}
		}

		if (! empty ( $parentId )) {
			$sql = 'UPDATE ' . $this->vip_course . ' SET is_leaf = 0 WHERE id = ' . $this->dao->quote ( $parentId ) . ' AND is_leaf = 1';
			$this->dao2->execute ( $sql );
		}

		return $this->dao2->execute ( 'UPDATE ' . $this->vip_course . ' SET name = ' . $this->dao->quote ( $course ['name'] ) . ', sort = ' . $this->dao->quote ( $course ['sort'] ) . ', parent_id = ' . $this->dao->quote ( $course ['parent_id'] ) . ' WHERE id = ' . $this->dao->quote ( $course ['id'] ) );
	}


	public function deleteCourseByID($courseId, $knowledgeTypeId){
		$flag = true;
		if(!empty($courseId) && !empty($knowledgeTypeId)){
			$courseInfo = $this->getCourseByID($courseId);
			$this->dao2->execute ( 'begin' ); // 事务开启
			$result = $this->dao2->execute('UPDATE '.$this->vip_course.' SET status = -1 WHERE id = '.$this->dao->quote($courseId));
			if(!$result){
				$flag = false;
			}
			if($courseInfo['parent_id']!=0){
				$count = $this->dao2->getOne('SELECT COUNT(1) FROM '.$this->vip_course.' WHERE status = 1 AND parent_id = '.$this->dao->quote($courseInfo['parent_id']));
				if($count == 0){
					$result2 = $this->dao2->execute('UPDATE '.$this->vip_course.' set is_leaf = 1 WHERE id = '.$this->dao->quote($courseInfo['parent_id']));
					if(!$result2){
						$flag = false;
					}
				}
			}else{
				$result2 = $this->dao2->execute('DELETE FROM  '.$this->vip_knowledgetype_course_rs.' WHERE course_id = '.$this->dao->quote($courseId).' AND knowledge_type_id = '.$this->dao->quote($knowledgeTypeId));
				if(!$result2){
					$flag = false;
				}
			}
			if($flag===false){
				$this->dao2->execute ( 'rollback' ); // 事务回滚
			}else{
				$this->dao2->execute ( 'commit' ); // 事务提交
			}
		}else{
			$flag = false;
		}
		return $flag;
	}
}
?>