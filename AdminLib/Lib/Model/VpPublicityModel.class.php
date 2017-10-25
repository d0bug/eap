<?php

class VpPublicityModel extends Model {
	public  $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao();
		$this->tableName = 'vp_publicity';
		$this->dao2 = Dao::getDao('MYSQL_CONN2');
		$this->vip_school = 'vip_school';
		$this->so_vip_teacher_style = 'so_vip_teacher_style';
		$this->so_vip_teacher_education = 'so_vip_teacher_education';
	}

	public function update_publicity($arr,$user_key,$sqlAction){
		if(!empty($user_key)){
			$arr['send_word'] = $this->textarea_content_to($arr['send_word']);
			$arr['intro_content'] = $this->textarea_content_to($arr['intro_content']);
			$arr['teach_content'] = $this->textarea_content_to($arr['teach_content']);
			$arr['achievement_content'] = $this->textarea_content_to($arr['achievement_content']);
			$arr['experience_content'] = $this->textarea_content_to($arr['experience_content']);
			$arr['comment'] = $this->textarea_content_to($arr['comment']);
			if($sqlAction == 'INSERT'){
				$sql = "INSERT INTO $this->tableName ([user_key],[avatar] ,[teacher_name],[gender],[subject],[grades],[send_word],[intro_img] ,[intro_content],[teach_img],[teach_content] ,[achievement_content],[experience_img],[experience_content],[comment],[status],[last_updtime],[rank],[edu_id_list],[style_id_list],[school_id_list]) VALUES ('$user_key','$arr[avatar]',".$this->dao->quote($arr['teacher_name']).",'$arr[gender]','$arr[subject]','$arr[grades]',".$this->dao->quote($arr['send_word']).",'$arr[intro_img]',".$this->dao->quote($arr['intro_content']).",'$arr[teach_img]',".$this->dao->quote($arr['teach_content']).",".$this->dao->quote($arr['achievement_content']).",'$arr[experience_img]',".$this->dao->quote($arr['experience_content']).",".$this->dao->quote($arr['comment']).",'$arr[status]','".time()."',".$this->dao->quote($arr['rank']).",".$this->dao->quote($arr['edu_id_list']).",".$this->dao->quote($arr['style_id_list']).",".$this->dao->quote($arr['school_id_list']).")";
			}else{
				$sql = "UPDATE $this->tableName SET [avatar]= '$arr[avatar]' ,[teacher_name]=".$this->dao->quote($arr['teacher_name']).",[gender]='$arr[gender]',[subject]='$arr[subject]',[grades]='$arr[grades]',[send_word]=".$this->dao->quote($arr['send_word']).",[intro_img]='$arr[intro_img]' ,[intro_content]=".$this->dao->quote($arr['intro_content']).",[teach_img]='$arr[teach_img]',[teach_content]=".$this->dao->quote($arr['teach_content'])." ,[achievement_content]=".$this->dao->quote($arr['achievement_content']).",[experience_img]='$arr[experience_img]',[experience_content]=".$this->dao->quote($arr['experience_content']).",[comment]=".$this->dao->quote($arr['comment']).",[status]='$arr[status]',[last_updtime]='".time()."',[rank]=".$this->dao->quote($arr['rank']).",[edu_id_list]=".$this->dao->quote($arr['edu_id_list']).",[style_id_list]=".$this->dao->quote($arr['style_id_list']).",[school_id_list]=".$this->dao->quote($arr['school_id_list'])." WHERE [user_key] = '$user_key'";
			}
			$this->dao->execute($sql);
			if($this->dao->affectRows()){
				return true;
			}
			return false;
		}
		return false;
	}

	public function get_publicity_info($user_key){
		if(!empty($user_key)){
			$row = $this->dao->getAll("SELECT [pid],[user_key],[avatar],[teacher_name] ,[gender],[subject],[grades],[send_word],[intro_img],[intro_content],[teach_img],[teach_content],[achievement_content],[experience_img],[experience_content],[comment],[rank],[edu_id_list],[style_id_list],[school_id_list] FROM $this->tableName WHERE [user_key] = '$user_key'");
			if(!empty($row)){
				$row[0]['send_word'] = $this->to_textarea_content($row[0]['send_word']);
				$row[0]['intro_content'] = $this->to_textarea_content($row[0]['intro_content']);
				$row[0]['teach_content'] = $this->to_textarea_content($row[0]['teach_content']);
				$row[0]['achievement_content'] = $this->to_textarea_content($row[0]['achievement_content']);
				$row[0]['experience_content'] = $this->to_textarea_content($row[0]['experience_content']);
				$row[0]['comment'] = $this->to_textarea_content($row[0]['comment']);
				return $row[0];
			}else{
				return false;
			}
		}
		return false;
	}

	public function get_publicity_list($condition='', $currentPage=1, $pageSize=20){
		$count = $this->get_publicity_count($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT * FROM ' . $this->tableName . ' WHERE 1=1 ';
		if(!empty($condition)) {
			$strQuery .= ' AND ' . $condition;
		}
		$order = ' ORDER BY [status] ASC,[last_updtime] DESC';
		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}

	public function get_publicity_count($condition='') {
		$strQuery = 'SELECT count(*) FROM ' . $this->tableName . ' WHERE 1=1 ';
		if (!empty($condition)) {
			$strQuery .= ' AND ' . $condition;
		}
		return $this->dao->getOne($strQuery);
	}


	public function textarea_content_to($content){
		return str_replace(" ","&nbsp;",str_replace("\r\n","<br>",$content));
	}


	public function to_textarea_content($content){
		return str_replace("&nbsp;"," ",str_replace("<br>","\r\n",$content));
	}

	public function update_status($status,$userKey){
		if(!empty($userKey)){
			return $this->dao->execute("UPDATE ".$this->tableName." SET [is_removed] = '$status' WHERE user_key = '$userKey' ");
		}
		return false;
	}

	public function update_passStatus($arr){
		$this->dao->execute("UPDATE ".$this->tableName." SET [status] = '$arr[status]' WHERE pid = '$arr[pid]' ");
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}
	
	
	public function getVipSchoolList(){
		return $this->dao2->getAll('SELECT id,title FROM '.$this->vip_school);
	}
	
	public function getVipEducationList(){
		return $this->dao2->getAll('SELECT id,title FROM '.$this->so_vip_teacher_education);
	}
	
	public function getVipStyleList(){
		return $this->dao2->getAll('SELECT id,title FROM '.$this->so_vip_teacher_style);
	}
	
	
	public function getuserKeyByTeacherCode($code){
		$teacherInfo = $this->dao->getRow("SELECT sName,sRealName,nType,nKind FROM BS_Teacher WHERE sCode = '$code' AND nKind = 3 ");
		if(!empty($teacherInfo)){
			$strQuery = "SELECT user_key,user_name FROM sys_users WHERE user_realname = '$teacherInfo[sname]' ";
			if($teacherInfo['ntype'] == 1 ||$teacherInfo['ntype'] == 5 ){//全职或者专职
				$strQuery .= " AND is_employee = 1 ";
			}else{//兼职
				$strQuery .= " AND is_teacher = 1 ";
			}
			$userInfo = $this->dao->getRow($strQuery);
			if(!empty($userInfo)){
				return $userInfo['user_key'];
			}
			return false;
		}
		return false;
	}
}
?>