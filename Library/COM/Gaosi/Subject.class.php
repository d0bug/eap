<?php
import('COM.Dao.Dao');
class Subject {
    public static function getSubjectArray() {
        $depts = self::getSubjectList();
        $subArray = array();
        foreach($depts as $dept) {
            $subArray[$dept['scode']] = $dept['sname'];
        }
        return $subArray;
    }
    
    public static function getSubjectList() {
    	$dao = Dao::getDao('MSSQL_CONN');
    	$strQuery = 'SELECT scode,sname FROM ' . $dao->tableName('s_dept') . ' WHERE bteachingdept=1 ORDER BY scode';
    	$sbjList = $dao->getAll($strQuery);
    	return $sbjList;
    }
    
    public static function getXuebukeInfo($xuebuke) {
    	$dao = Dao::getDao('MSSQL_CONN');
    	$strQuery = 'SELECT nxuebu,nxueke FROM d_xuebuke 
    				 WHERE id=' . abs($xuebuke);
    	return array_values($dao->getRow($strQuery));
    }
};
?>