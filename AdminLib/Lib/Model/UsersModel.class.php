
<?php

class UsersModel extends Model {
	public  $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao();
		$this->tableName = 'sys_users';
		$this->vipUser = 'Vip_User';
		$this->bsTeacher = 'BS_Teacher';
		$this->sys_user_roles = 'sys_user_roles';
		$this->sys_user_relations = 'sys_user_relations';
		$this->sys_roles = 'sys_roles';
		$this->sys_app_admins = 'sys_app_admins';
		$this->V_D_XueKe = 'V_D_XueKe';
		$this->V_D_XueBu = 'V_D_XueBu';
		$this->V_D_Subject = 'V_D_Subject';
	}

	/*获取用户信息*/
	public function get_userInfo($user_key){
		if(!empty($user_key)){
			return $this->dao->getRow("SELECT [id],[user_key],[user_name] ,[user_passwd],[user_realname],[is_employee] ,[is_teacher],[is_school] ,[user_email] ,[user_mobile],[is_removed],[is_teaching_and_research],[department] FROM ".$this->tableName." WHERE [user_key] = '$user_key'");
		}
		return false;
	}

	public function updatePassword($newpwd,$user_key){
		if(!empty($user_key) && !empty($newpwd)){
			$this->dao->execute("UPDATE ".$this->tableName." SET [user_passwd]='".md5($newpwd)."' WHERE [user_key] = '$user_key'");
			if($this->dao->affectRows()){
				return true;
			}
			return false;
		}
		return false;
	}

	public function editUser($info){
		if(empty($info['realname'])) return false;
		if($info['is_employee']!=1 && empty($info['passwd'])) return false;
		$strQuery = "UPDATE ".$this->tableName." SET [user_realname] = '$info[realname]',[is_removed]='$info[isRemoved]'";
		if($info['is_employee'] !=1){
			$strQuery .= " ,[user_passwd]='".md5($info['passwd'])."'";
		}else{
			$strQuery .= ",[is_teaching_and_research]='$info[power]'";
		}
		$strQuery .= " WHERE [user_key] = '$info[userKey]'";
		$this->dao->execute($strQuery);
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}

	/*修改手机号*/
	public function set_mobile($phone,$user_key,$userInfo){
		if(!empty($phone) && !empty($user_key)){
			$this->dao->execute("UPDATE ".$this->tableName." SET [user_mobile] = '$phone' WHERE [user_key] = '$user_key'");
			if($this->dao->affectRows()){
				//更新Vip_User表中的手机号
				$this->dao->execute("UPDATE ".$this->vipUser." SET [Phone] = '$phone' WHERE [LoginName] = '$userInfo[user_name]' and [Name] = '$userInfo[real_name]'");
				return true;
			}else{
				return false;
			}
		}
		return false;
	}

	/*录入系统用户角色表*/
	public function add_sys_user_roles($info){
		//判断该用户角色是否已存在，若存在则不执行写入
		if($this->dao->getOne("SELECT [id] FROM ".$this->sys_user_roles." WHERE [user_key] = '$info[user_key]' AND [role_id] ='$info[role_id]'")){
			return true;
		}else{
			$this->dao->execute("INSERT INTO ".$this->sys_user_roles." ([app_name],[user_key] ,[role_id],[create_user],[create_at]) VALUES('$info[app_name]','$info[user_key]','$info[role_id]','$info[create_user]','".date('Y-m-d H:i:s')."')");
			if($this->dao->affectRows()){
				return true;
			}
		}
		return false;
	}

	/*添加绑定关系*/
	public function addUserRelations($bindInfo){
		$this->dao->execute("INSERT INTO ".$this->sys_user_relations." ([user_key],[rel_user_key],[rel_user_type],[rel_user_name] ,[create_user],[create_at]) VALUES('$bindInfo[user_key]','$bindInfo[rel_user_key]','$bindInfo[rel_user_type]','$bindInfo[rel_user_name]','$bindInfo[create_user]','".date('Y-m-d H:i:s')."')");
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}

	/*获取当前账号绑定的用户信息*/
	public function getRelations($userKey,$userType){
		$sql = "SELECT [user_key],[rel_user_key],[rel_user_type],[rel_user_name],[create_user],[create_at] FROM ".$this->sys_user_relations." WHERE [user_key] = '$userKey' ";
		if(!empty($userType)){
			$sql .= " AND [rel_user_type] = '$userType'";
		}
		return $this->dao->getAll($sql);
	}


	/*解除账号绑定*/
	public function releaseBind($userKey,$relUserKey){
		$this->dao->execute("DELETE FROM ".$this->sys_user_roles." WHERE [create_user] = '".end(explode('-',$userKey))."' AND [user_key] = '$relUserKey'");
		$this->dao->execute("DELETE FROM ".$this->sys_user_relations." WHERE [rel_user_key] = '$relUserKey' AND [user_key] = '$userKey'");
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}

	public function get_userRealName_by_userKey($user_key){
		return $this->dao->getOne("SELECT [user_realname] FROM ".$this->tableName." WHERE [user_key] = '$user_key'");
	}

	public function addTeacherUser($userInfo){
		if($userInfo['user_type'] == 0){
			$userInfo['is_employee'] = 1;
			$userInfo['is_teacher'] = 0;
			$userInfo['is_school'] = 0;
			$userInfo['user_email'] = $userInfo['user_name'].'@gaosiedu.com';
			$strQuery = "INSERT INTO ".$this->tableName." ([user_key],[user_name],[user_realname],[is_employee],[is_teacher],[is_school],[user_email],[is_removed],[create_user],[create_at],[is_teaching_and_research],[department])
					VALUES('$userInfo[user_key]',".$this->dao->quote($userInfo['user_name']).",".$this->dao->quote($userInfo['real_name']).",'$userInfo[is_employee]','$userInfo[is_teacher]','$userInfo[is_school]',".$this->dao->quote($userInfo['user_email']).",'$userInfo[is_removed]','$userInfo[create_user]','".date('Y-m-d H:i:s')."','$userInfo[user_power]','$userInfo[department]')";
		}else{
			$userInfo['is_employee'] = 0;
			$userInfo['is_teacher'] = 1;
			$userInfo['is_school'] = 0;
			$strQuery = "INSERT INTO ".$this->tableName." ([user_key],[user_name],[user_realname],[user_passwd],[is_employee],[is_teacher],[is_school],[is_removed],[create_user],[create_at])
					VALUES('$userInfo[user_key]',".$this->dao->quote($userInfo['user_name']).",".$this->dao->quote($userInfo['real_name']).",".$this->dao->quote($userInfo['user_passwd']).",'$userInfo[is_employee]','$userInfo[is_teacher]','$userInfo[is_school]','$userInfo[is_removed]','$userInfo[create_user]','".date('Y-m-d H:i:s')."')";
		}
		$this->dao->execute($strQuery);
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}

	//获取VIP角色ID
	public function get_roleId($arr){
		return $this->dao->getOne("SELECT [role_id] FROM ".$this->sys_roles." WHERE [role_caption] = ".$this->dao->quote($arr['roleName'])." AND [app_name]='$arr[app_name]' AND [group_name] ='$arr[group_name]'");
	}


	public function get_userInfoFromUserRoles($arr){
		return $this->dao->getOne("SELECT [id] FROM ".$this->sys_user_roles." WHERE [role_id] = '$arr[role_id]' AND [user_key]='$arr[user_key]'");
	}

	public function get_RolesIdByGroupName($group_name){
		return $this->dao->getAll("SELECT [role_id] FROM ".$this->sys_roles." WHERE [group_name] ='$group_name'");
	}


	public function get_rolesUserList($condition,$currentPage=1, $pageSize=20){
		$count = $this->get_rolesUserCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = "SELECT r.user_key,u.id,u.user_name,u.user_realname,u.is_employee,u.is_teacher,u.is_removed,u.is_teaching_and_research FROM ".$this->sys_user_roles." as r JOIN ".$this->tableName." as u ON r.user_key = u.user_key WHERE ".$condition ." ";
		$order = ' ORDER BY id DESC';
		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}
	
	public function get_exportRolesUserList($condition){
		$strQuery = "SELECT r.user_key,u.id,u.user_name,u.user_realname,u.is_employee,u.is_teacher,u.is_removed,u.is_teaching_and_research FROM ".$this->sys_user_roles." as r JOIN ".$this->tableName." as u ON r.user_key = u.user_key WHERE ".$condition."";
		$strQuery .= " ORDER BY id DESC";
		return $this->dao->getAll($strQuery);
	}
	
	public function get_rolesUserCount($condition){
		$user_key_arr = $this->dao->getAll("SELECT distinct r.user_key FROM ".$this->sys_user_roles." as r JOIN ".$this->tableName." as u ON r.user_key = u.user_key WHERE ".$condition);
		return count($user_key_arr);
	}

	public function delete_vipUser($user_key_str,$vip_rolesid_str){
		$this->dao->execute("DELETE FROM ".$this->sys_user_roles." WHERE user_key IN ($user_key_str) AND role_id IN ($vip_rolesid_str)");
		if($this->dao->affectRows()){
			$this->dao->execute("DELETE FROM ".$this->tableName." WHERE user_key IN ($user_key_str) AND [is_employee] = '0' AND [is_teacher] = '1'");
			return true;
		}
		return false;
	}


	public function get_userRoles($user_key,$group_name,$app_name){
		$userRolesArr = $this->dao->getAll("SELECT r.[role_caption] FROM ".$this->sys_user_roles." AS ur LEFT JOIN ".$this->sys_roles." AS r ON ur.role_id = r.role_id WHERE ur.[user_key] = '$user_key' AND r.[group_name] = '$group_name' AND r.[app_name] = '$app_name'");
		$roles = '';
		if(!empty($userRolesArr)){
			foreach ($userRolesArr as $key=>$role){
				$roles .= $role['role_caption'].',';
			}
		}else{
			$roles .= '暂无';
		}
		return trim($roles,',');
	}
	
	public function get_alluserRoles($user_key_str,$group_name,$app_name){
		$userRolesArr = $this->dao->getAll("SELECT r.[role_caption],ur.[user_key] FROM ".$this->sys_user_roles." AS ur LEFT JOIN ".$this->sys_roles." AS r ON ur.role_id = r.role_id WHERE ur.[user_key] in(".$user_key_str.") AND r.[group_name] = '$group_name' AND r.[app_name] = '$app_name'");
		$roles = array();
		foreach ($userRolesArr as $key=>$role){
			$roles[$role['user_key']] .= $role['role_caption'].',';
		}
		return $roles;
	}

	public function get_userKey_by_username($username){
		return $this->dao->getOne("SELECT [user_key] FROM ".$this->tableName." WHERE [user_name] = ".$this->dao->quote($username));
	}

	public function get_teacher_by_userInfo($userInfo,$roleId){
		$strQuery = "SELECT count(*) FROM ".$this->sys_user_roles." WHERE [role_id] = '$roleId' ";
		if($userInfo['user_type']==0){
			$strQuery .= " AND [user_key] = ".$this->dao->quote($userInfo['user_key']);
		}else{
			$strQuery .= " AND [user_key] like ".$this->dao->quote('%-'.$userInfo['user_name']);
		}
		return $this->dao->getOne($strQuery);
	}

	public function update_teacherPower($user_key,$power){
		$this->dao->execute("UPDATE ".$this->tableName." SET [is_teaching_and_research] = '$power' WHERE [user_key] = '$user_key'");
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}

	public function checkIsAdminer($user_key,$group_name){
		return $this->dao->getOne("SELECT [is_admin] FROM ".$this->sys_app_admins." WHERE [user_key] = '$user_key' AND [group_name] = '$group_name' ");
	}

	public function get_userIsAvailable($user_key){
		if(!empty($user_key)){
			$is_removed = $this->dao->getOne("SELECT [is_removed] FROM ".$this->tableName." WHERE [user_key] = '$user_key'");
			if($is_removed == 1){
				return false;
			}else{
				return true;
			}
		}else{
			return false;
		}
	}


	public function update_department($userInfo){
		$this->dao->execute("UPDATE ".$this->tableName." SET [department] = '$userInfo[department]' WHERE [user_key] = '$userInfo[user_key]'");
		return true;
	}


	public function get_teacherCode($userInfo){
		$strQuery = 'SELECT TOP 1 sCode FROM '.$this->bsTeacher.' WHERE bLoginValid = 1 AND [nKind] = 3 AND (sName = '.$this->dao->quote($userInfo['real_name']).' OR sRealName = '.$this->dao->quote($userInfo['real_name']).')';
		if($userInfo['user_type'] == '内部员工'){
			$strQuery .= ' AND ([nType] = 1 OR [nType] = 5 )';//全职和专职教师
		}else if($userInfo['user_type'] == 'VIP社会兼职教师'){//VIP社会兼职教师
			$strQuery .= ' AND ([nType] = 3 OR [nType] = 4 )';
		}
		return $this->dao->getOne($strQuery);
	}

	public function getTeacherXueKeAndSubject($teacherCode){
		return $this->dao->getOne('SELECT sTeachSubject FROM '.$this->bsTeacher.' WHERE sCode = '.$this->dao->quote($teacherCode));
	}


	public function getTeacherXueBuBySubject($subjectStr){
		$result = $this->dao->getAll('SELECT b.id as xuebu,k.id as xueke FROM '.$this->V_D_Subject.' s
									  		 LEFT JOIN '.$this->V_D_XueBu.' b ON b.id = s.nXueBu 
									  		 LEFT JOIN '.$this->V_D_XueKe.' k ON k.id = s.nXueKe 
									  		 WHERE s.sCode IN ('.$subjectStr.')');
		$xueBu = array();
		$gradeMap = C('MAP_GRADE');
		$subjectMap = C('MAP_SUBJECT');
		if(!empty($result)){
			foreach ($result as $key=>$val){
				if(!in_array($val['xuebu'],$xueBu)) $xueBu[] = $gradeMap[$val['xuebu']];
				if(!in_array($val['xueke'],$xueKe)) $xueKe[] = $subjectMap[$val['xueke']];
			}
		}
		return array('xuebu'=>implode(',',array_unique($xueBu)),'xueke'=>implode(',',array_unique($xueKe)));
	}
}

?>