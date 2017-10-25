<?php
/*此数据模型为课程属性模型，原一期年级模型*/
class VpGradeModel extends Model {
	private $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao();
		$this->tableName = 'vp_grade';
		$this->relationship_tableName = 'vp_subject_grade_relationship';
		$this->user_subjects = 'vp_user_subjects';
		$this->sys_app_admins = 'sys_app_admins';
	}


	public function get_gradeList($type = '',$userKey){
		$strQuery = "SELECT [gid],[name],[type] FROM ".$this->tableName." WHERE 1=1 ";
		if($type!==''){
			$strQuery .= " AND [type] = '$type' ";
		}
		if($userKey){
			//判断用户是否为管理员
			if(!VipCommAction::checkIsAdmin($userKey)){
				$sidStr = $this->dao->getOne("SELECT [sids] FROM ".$this->user_subjects." WHERE [user_key]='$userKey'");
				$sidStr = "'".implode("','",explode(',',trim($sidStr,',')))."'";
				$gradeIdArr =   $this->dao->getAll("SELECT [gids] FROM ".$this->relationship_tableName." WHERE sid IN ($sidStr)");
				$gradeIdStr = '';
			//	dump($gradeIdArr);
				if(!empty($gradeIdArr)){
					foreach ($gradeIdArr as $key=>$grade){
						if(!empty($grade['gids'])){
							$gradeIdStr .= rtrim($grade['gids'],',').',';
						}
					}
					$gradeIdStr = "'".implode("','",array_unique(explode(',',trim($gradeIdStr,','))))."'";
					$strQuery .= " AND gid IN ($gradeIdStr)";
				}else{
					$strQuery .= " AND gid IN (".$this->dao->quote($gradeIdStr).")";
				}
			}
		}
		$strQuery .= ' ORDER BY type ASC ';
		return $this->dao->getAll($strQuery);
	}


	public function get_gradeList_by_subjectid($subjectid){
		$gradeid_str = $this->dao->getRow("SELECT [gids] FROM ".$this->relationship_tableName." WHERE [sid] = '$subjectid'");
		if(!empty($gradeid_str['gids'])){
			$gradeArr = explode(',',$gradeid_str['gids']);
			$gradeidStr = '';
			foreach ($gradeArr as $gradeid){
				$gradeidStr .= trim($gradeid).',';
			}
			$gradeidStr = "'".implode("','",explode(',',$gradeidStr))."'";
			return $this->dao->getAll("SELECT [gid],[name] FROM ".$this->tableName." WHERE [gid] IN ($gradeidStr)");
		}
		return false;

	}


	public function get_grade($grade_name){
		return $this->dao->getOne('SELECT count(gid) FROM '.$this->tableName.' WHERE [name] = '.$this->dao->quote($grade_name));
	}

	public function add_relationship($arr){
		$this_gradeId = $this->dao->getOne('SELECT [gid] FROM '.$this->tableName.' WHERE [name] = '.$this->dao->quote($arr['name']));
		$relationshipInfo = $this->dao->getRow("SELECT [rid],[gids] FROM ".$this->relationship_tableName." WHERE [sid] = '$arr[subject_id]'");
		$grade_ids = $relationshipInfo['gids'];
		if(!empty($grade_ids)){
			$grade_ids = trim($grade_ids,',').',';
		}
		if($relationshipInfo['rid']){
			if(strpos($grade_ids,$this_gradeId) !== false){
				return '0';
			}else{
				$this->dao->execute("UPDATE ".$this->relationship_tableName." SET [gids] = '".$grade_ids.$this_gradeId.","."' WHERE [sid] = '$arr[subject_id]'");
			}
		}else{
			$this->dao->execute("INSERT INTO  ".$this->relationship_tableName." ([sid],[gids]) VALUES( '$arr[subject_id]','".$this_gradeId.","."')");
		}
		if($this->dao->affectRows()){
			return true;
		}else{
			return false;
		}


	}

	public function add_grade($arr){
		$this->dao->execute('INSERT INTO '.$this->tableName.' ([name],[type]) VALUES('.$this->dao->quote($arr['name']).','.$this->dao->quote($arr['type']).')');
		if($this->dao->affectRows()){
			//更新学科年级关系表
			$new_gradeid = $this->dao->getOne('SELECT [gid] FROM '.$this->tableName.' WHERE [name] = '.$this->dao->quote($arr['name']));
			$relationshipInfo = $this->dao->getRow('SELECT [rid],[gids] FROM '.$this->relationship_tableName.' WHERE [sid] = '.$arr['subject_id']);
			$grade_ids = $relationshipInfo['gids'];
			if(!empty($grade_ids)){
				$grade_ids = trim($grade_ids,',').',';
			}
			if(!empty($relationshipInfo['rid'])){
				$this->dao->execute("UPDATE ".$this->relationship_tableName." SET [gids] = '".$grade_ids.$new_gradeid.","."' WHERE [sid] = '$arr[subject_id]'");
				if($this->dao->affectRows()){
					return true;
				}else{
					return false;
				}
			}else{
				$this->dao->execute("INSERT INTO ".$this->relationship_tableName." ([sid],[gids]) VALUES('$arr[subject_id]','".$new_gradeid.",')");
				if($this->dao->affectRows()){
					return true;
				}else{
					return false;
				}
			}
		}
		return false;
	}


	public function get_gradename_by_gid($gid){
		return $this->dao->getOne("SELECT [name] FROM ".$this->tableName." WHERE [gid] = '$gid'");
	}


	public function get_gradenames_by_gids($gids){
		$gid_str = "'".implode("','",explode(',',$gids))."'";
		$gradeList = $this->dao->getAll("SELECT [name] FROM ".$this->tableName." WHERE [gid] IN ($gid_str)");
		$return = '';
		if(!empty($gradeList)){
			foreach ($gradeList as $key=>$grade){
				$return .= $grade['name'].', ';
			}
		}
		return $return;

	}

}
?>