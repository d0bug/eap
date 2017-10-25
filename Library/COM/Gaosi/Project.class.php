<?php
import('COM.Dao.Dao');
import('COM.Gaosi.GClass');
class Project {
    public static function getProjectList($args) {
    	$dao = Dao::getDao('MSSQL_CONN');
    	$semesterCondition = GClass::getSemesterCondition($args);
    	$subjectCodition = GClass::getSubjectCondition($args);
    	$strQuery = 'SELECT distinct sprojectcode 
    				 FROM viewclassforweb 
    				 WHERE 1=1 ';
    	$strQuery .= $semesterCondition;
    	$strQuery .= $subjectCodition;
    	$strQuery = 'SELECT prj.*,dept.sname deptname 
    				 FROM bs_project prj,s_dept dept
    				 WHERE prj.sdeptcode=dept.scode
    				   AND prj.scode IN (' . $strQuery . ')
    				 ORDER BY prj.scode';
    	$projectList = $dao->getAll($strQuery);
    	return $projectList;
    }
};
?>