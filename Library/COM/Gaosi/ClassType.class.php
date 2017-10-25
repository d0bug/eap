<?php
import('COM.Dao.Dao');
import('COM.Gaosi.GClass');
class ClassType {
    public static function getClassTypeList($args) {
    	$dao = Dao::getDao('MSSQL_CONN');
    	$strQuery = 'SELECT DISTINCT sclasstypecode 
    				 FROM viewclassforweb 
    				 WHERE 1=1 ';
    	$strQuery .= GClass::getSemesterCondition($args);
    	$prjCondition = GClass::getPrjCondition($args);
    	if($prjCondition) {
    		$strQuery .= $prjCondition;
    	} else {
    		$strQuery .= GClass::getSubjectCondition($args);
    	}
    	$strQuery = 'SELECT ct.*,dept.sname deptname,prj.sname prjname
    				 FROM bs_classtype ct,bs_project prj,s_dept dept
    				 WHERE ct.sprojectcode=prj.scode 
    				   AND prj.sdeptcode=dept.scode
    				   AND ct.scode IN (' . $strQuery . ')
    				 ORDER BY ct.scode';
    	$ctList = $dao->getAll($strQuery);
    	return $ctList;
    }
};
?>