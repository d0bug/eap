<?php

class ModelTeacherModel extends Model {
	private $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao('MSSQL_CONN');
		$this->teacher = 'BS_Teacher';
		$this->teacherInfo = '[eap].[dbo].[T_teacherInfo]';
	}

	public function getTeacherImageInfo($teacherCode, $teacherName){
		return $this->dao->getRow("SELECT [tPic] FROM ".$this->teacherInfo." WHERE [t_sCode] = '$teacherCode' AND [t_sName] = '$teacherName'");
	}
}
?>