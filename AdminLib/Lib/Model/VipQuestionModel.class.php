<?php

class VipQuestionModel extends Model {
	public  $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao('MSSQL_CONN');
		$this->vp_T_BigGrade = 'vp_T_BigGrade';
		$this->vp_T_BigSubject = 'vp_T_BigSubject';
		$this->vp_T_Tag = 'vp_T_Tag';
		$this->vp_T_BigSubject = 'vp_T_BigSubject';
		$this->vp_T_KnowledgePoint = 'vp_T_KnowledgePoint';
		$this->vp_T_Program = 'vp_T_Program';
		$this->vp_T_TagRelationship = 'vp_T_TagRelationship';
		$this->vp_T_KnowledgePointRelationship = 'vp_T_KnowledgePointRelationship';
	}

	public function get_bigGradeList(){
		return $this->dao->getAll('SELECT * FROM '.$this->vp_T_BigGrade);
	}


	public function get_bigSubjectList($arr = array()){
		if(empty($arr)){
			$strQuery = 'SELECT * FROM '.$this->vp_T_BigSubject;
		}else{
			$strQuery = 'SELECT r.sid,MAX(s.name) AS name FROM '.$this->vp_T_KnowledgePointRelationship.' AS r
											 LEFT JOIN '.$this->vp_T_BigSubject.' AS s ON r.sid = s.sid 
											 WHERE r.yid = '.$this->dao->quote($arr['yid']).'
											 GROUP BY r.sid ORDER BY r.sid ASC ';
		}
		return $this->dao->getAll($strQuery);
	}


	public function get_tagList($arr=array()){
		$strQuery = 'SELECT * FROM '.$this->vp_T_Tag.' WHERE 1=1 ';
		if(isset($arr['type'])){
			$strQuery .= ' AND type = '.$this->dao->quote($arr['type']);
		}
		return $this->dao->getAll($strQuery);
	}


	public function get_programList($arr=array()){
		return array(array('pid'=>1,'name'=>'同步课程'),array('pid'=>2,'name'=>'竞赛课程'));
	}


	public function get_knowledgeNode($arr=array()){
		$list = array(array('kid'=>1,'name'=>'身边的化学物质','seq'=>0,'ishave_child'=>1),
					  array('kid'=>2,'name'=>'几何形状','seq'=>1,'ishave_child'=>1),
					  array('kid'=>3,'name'=>'行程','seq'=>2,'ishave_child'=>0)
		);

		return $list;
	}

}
?>