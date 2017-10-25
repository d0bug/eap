<?php

class VpKnowledgeModel extends Model {
	private $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao();
		$this->tableName = 'vp_knowledge';
		$this->knowledge_permission = 'vp_knowledge_permission';
		$this->handouts = 'vp_handouts';
		$this->vp_subject = 'vp_subject';
	}

	/*获取知识点信息*/
	public function get_knowledgeList($type = ''){
		$strQuery = 'SELECT [kid] ,[name],[type] FROM '.$this->tableName.' WHERE 1=1 ';
		if($type!==''){
			$strQuery .= " AND [type] = '$type' ";
		}
		return $this->dao->getAll($strQuery);
	}

	public function get_knowledgePermissionList($condition){
		$sql = 'SELECT p.* FROM '.$this->knowledge_permission.' AS p LEFT JOIN '.$this->vp_subject.' AS s ON p.sid=s.sid '.$condition;
		return $this->dao->getAll($sql);
	}

	/*根据学科id获取知识点信息*/
	public function get_knowledgeList_by_subjectid($sid){
		return $this->dao->getAll('SELECT [kid],[sid],[gid],[nids],[permission] FROM '.$this->knowledge_permission."  WHERE [sid] = '$sid'");
	}


	/*根据年级id获取知识点信息*/
	public function get_knowledgeList_by_gradeid($gid){
		return $this->dao->getAll('SELECT [kid],[sid],[gid],[nids],[permission] FROM '.$this->knowledge_permission."  WHERE [gid] = '$gid'");
	}

	/*根据年级id获取知识点信息*/
	public function get_knowledgeList_by_gradeid_and_subjectid($arr){
		$strQuery = 'SELECT kp.[kid] ,kp.[sid] ,kp.[gid],kp.[permission],k.[name] FROM '.$this->knowledge_permission." kp LEFT JOIN ".$this->tableName." k ON kp.kid = k.kid WHERE 1=1 ";
		if(!empty($arr['sid'])){
			$strQuery .= "AND kp.[sid] = '$arr[sid]' ";
		}
		if(!empty($arr['gid'])){
			$strQuery .= "AND kp.[gid]='$arr[gid]' ";
		}
		if($arr['is_jiaoyan'] == 0){
			$strQuery .= " AND kp.[permission]='0' ";
		}
		return $this->dao->getAll($strQuery);
	}

	/*根据年级id字符串获取知识点信息*/
	public function get_knowledgeList_by_gradeids_and_subjectid($gids,$sid){
		$gids = str_replace(",''","",$gids);
		return $this->dao->getAll('SELECT [kid] ,[sid] ,[gid],[nids],[permission] FROM '.$this->knowledge_permission." WHERE [sid] = '$sid' AND [gid] IN ($gids)");
	}


	public function get_knowledge_by_sid_and_gid_and_kid($sid,$gid,$kid){
		return $this->dao->getOne('SELECT [rid] FROM '.$this->knowledge_permission." WHERE [sid] = '$sid' AND [gid] ='$gid' AND [kid] = '$kid' ");
	}

	public function get_knowledge_by_sid_and_gid_and_kid_and_nid($sid,$gid,$kid,$nids){
		return $this->dao->getOne('SELECT [rid] FROM '.$this->knowledge_permission." WHERE [sid] = '$sid' AND [gid] ='$gid' AND [kid] = '$kid' AND [nids]='$nids'");
	}


	/*设置知识点权限*/
	public function update_permission($arr,$permission,$course_user){
		$strQuery = 'SELECT [rid] FROM '.$this->knowledge_permission.' WHERE 1=1 ';
		foreach ($arr as $key=>$val){
			if($key !='nids'){
				$strQuery .= " AND " .$key." = '$val' ";
			}
		}
		$rid = $this->dao->getOne($strQuery);
		if(!empty($rid)){
			$this->dao->execute('UPDATE '.$this->knowledge_permission." SET permission = '$permission',nids = '$arr[nids]',courseuser = '$course_user' WHERE rid = '$rid'");
		}else{
			$this->dao->execute('INSERT INTO '.$this->knowledge_permission." ([sid],[gid],[kid],[nids],[permission],[courseuser]) VALUES('$arr[sid]','$arr[gid]','$arr[kid]','$arr[nids]','$permission','$course_user')");
		}
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}

	/*添加知识点*/
	public function add_knowledge($arr){
		if($this->dao->execute('INSERT INTO '.$this->tableName.' ([name],[type]) VALUES('.$this->dao->quote($arr['name']).','.$arr['type'].')')){
			$new_insert_id = $this->dao->getOne("SELECT SCOPE_IDENTITY() as newid");
			return $new_insert_id;
		}else{
			return false;
		}
	}

	public function add_knowledge_permission($arr){
		$nianjiArr = C('GRADES');
		foreach ($arr['grade_id'] as $key => $gradeid){
			$this->dao->execute('INSERT INTO '.$this->knowledge_permission." ([kid],[sid],[gid],[nids],[permission]) VALUES('$arr[kid]','$arr[subject_id]','$gradeid','','0')");
		}
		return true;
	}

	public function get_knowledgename_by_kid($kid){
		return $this->dao->getOne('SELECT [name] FROM '.$this->tableName.' WHERE [kid] = \''.$kid.'\'');
	}

	public function get_knowledgeid_by_name($name){
		return $this->dao->getOne('SELECT [kid] FROM '.$this->tableName.' WHERE [name] = '.$this->dao->quote($name));
	}

	public function get_permission_subquery($arr){
		$sql = 'SELECT [sid],[gid],[kid],[nids] FROM '.$this->knowledge_permission." WHERE [permission] = '1' ";
		if(!empty($arr['sid'])){
			$sql .= " AND [sid]='$arr[sid]' ";
		}
		if(!empty($arr['gid'])){
			$sql .= " AND [gid]='$arr[gid]' ";
		}
		$permission_arr = $this->dao->getAll($sql);
		$return = '';
		if(!empty($permission_arr)){
			$return .= ' SELECT [hid] FROM '.$this->handouts.' WHERE ';
			foreach ($permission_arr as $key => $permission){
				$return .= " ([sid]='$permission[sid]' AND [gid] = '$permission[gid]' AND [kid] = '$permission[kid]' ) OR";
			}
			$return = trim($return,'OR');
		}
		return $return;
	}

	public function get_nianjiList($arr){
		return $this->dao->getOne('SELECT [nids] FROM '.$this->knowledge_permission." WHERE [sid]='$arr[sid]' AND [gid]='$arr[gid]' AND [kid]='$arr[kid]' ");
	}
	
	public function get_courseuser_by_handout($sid,$gid,$kid){
		
		$courseuserArray = $this->dao->getRow("SELECT [courseuser] FROM ".$this->knowledge_permission." WHERE [sid] = '$sid' AND [gid] = '$gid' AND [kid] = '$kid' ");
		return $courseuserArray['courseuser'];
	}
	
	public function get_allcourseuser_by_handout($sgkidStr){
		if(!empty($sgkidStr)){
			$list = $this->dao->getAll("SELECT [courseuser],CAST([sid] AS VARCHAR(10))+CAST([gid] AS VARCHAR(10))+CAST([kid] AS VARCHAR(10)) as p FROM ".$this->knowledge_permission." WHERE CAST([sid] AS VARCHAR(10))+CAST([gid] AS VARCHAR(10))+CAST([kid] AS VARCHAR(10)) in(".$sgkidStr.")");
		}else{
			$list = array();
		}
		return  $list;
	}
	
}
?>