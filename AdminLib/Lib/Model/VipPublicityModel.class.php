<?php

class VipPublicityModel extends Model {
	private $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao();
		$this->tableName = 'vip_publicity';
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
				$sql = "INSERT INTO $this->tableName ([user_key],[avatar] ,[teacher_name],[gender],[subject],[grades],[send_word],[intro_img] ,[intro_content],[teach_img],[teach_content] ,[achievement_content],[experience_img],[experience_content],[comment]) VALUES ('$user_key','$arr[avatar]','$arr[teacher_name]','$arr[gender]','$arr[subject]','$arr[grades]','$arr[send_word]','$arr[intro_img]','$arr[intro_content]','$arr[teach_img]','$arr[teach_content]','$arr[achievement_content]','$arr[experience_img]','$arr[experience_content]','$arr[comment]')";
			}else{
				$sql = "UPDATE [eap].[dbo].[vip_publicity] SET [avatar]= '$arr[avatar]' ,[teacher_name]='$arr[teacher_name]',[gender]='$arr[gender]',[subject]='$arr[subject]',[grades]='$arr[grades]',[send_word]='$arr[send_word]',[intro_img]='$arr[intro_img]' ,[intro_content]='$arr[intro_content]',[teach_img]='$arr[teach_img]',[teach_content]='$arr[teach_content]' ,[achievement_content]='$arr[achievement_content]',[experience_img]='$arr[experience_img]',[experience_content]='$arr[experience_content]',[comment]='$arr[comment]' WHERE [user_key] = '$user_key'";
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
			$row = $this->dao->getAll("SELECT [pid],[user_key],[avatar],[teacher_name] ,[gender],[subject],[grades],[send_word],[intro_img],[intro_content],[teach_img],[teach_content],[achievement_content],[experience_img],[experience_content],[comment] FROM $this->tableName WHERE [user_key] = '$user_key'");
			$row[0]['send_word'] = $this->to_textarea_content($row[0]['send_word']);
			$row[0]['intro_content'] = $this->to_textarea_content($row[0]['intro_content']);
			$row[0]['teach_content'] = $this->to_textarea_content($row[0]['teach_content']);
			$row[0]['achievement_content'] = $this->to_textarea_content($row[0]['achievement_content']);
			$row[0]['experience_content'] = $this->to_textarea_content($row[0]['experience_content']);
			$row[0]['comment'] = $this->to_textarea_content($row[0]['comment']);
			return $row[0];
		}
		return false;
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
}
?>