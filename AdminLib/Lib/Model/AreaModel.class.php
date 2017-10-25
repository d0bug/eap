<?php
class AreaModel {
    private $dao = null;
    private $tableName = '';
    public function __construct() {
        $this->dao = Dao::getDao();
        $this->tableName = 'bs_area';
    }
    
    public function getAreaList() {
        static $areaList = null;
        if(null === $areaList) {
            $strQuery = 'SELECT scode,sname FROM ' . $this->tableName . '
            			 WHERE bvalid=1 AND naudit=1
                         ORDER BY scode';
            $areaList= $this->dao->getAll($strQuery);
        }
        return $areaList;
    }
    
    public function getAreaOptions() {
        static $areaOptions = null;
        if($areaOptions === null) {
            $areaOptions = array();
            $areaList = $this->getAreaList();
            foreach ($areaList as $area) {
                $areaOptions[$area['scode']] = $area['sname'];
            }
        }
        return $areaOptions;
    }
}
?>