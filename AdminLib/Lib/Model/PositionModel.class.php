<?php
class PositionModel {
    private $dao = null;
    private $tableName = '';
    private $posAreaTable = '';
    private $operator = null;
    public function __construct(){
        import('COM.SysUtil');
        $this->dao = Dao::getDao();
        $this->tableName = 'ex_positions';
        if(class_exists('User', false)) {
        	$this->operator = User::getLoginUser(C('USER_COOKIE_NAME'));
        }
        $this->posAreaTable = 'ex_pos_areas';
    }
    
    private function checkInfo(&$posInfo) {
        $needFields = array('pos_code', 'pos_caption', 'pos_area', 'pos_addr', 'pos_telephone');
        foreach ($posInfo as $field=>$value) {
            if(false == is_array($value)) {
                $value = SysUtil::safeString($value);
            }
        	if(false == $value && in_array($field, $needFields)) {
                return array('errorMsg'=>'考点信息不完整');
        	}
        	$posInfo[$field] = $value;
        }
        
        $strQuery = 'SELECT COUNT(1) FROM ' . $this->tableName . '
                     WHERE is_remove=0 AND pos_caption=' . $this->dao->quote($posInfo['pos_caption']);
        if($posInfo['pos_id']) {
            $strQuery .= ' AND pos_code != ' . $this->dao->quote($posInfo['pos_code']);
        } else {
            $strQuery .= ' OR pos_code=' . $this->dao->quote($posInfo['pos_code']);
        }
        $exists = $this->dao->getOne($strQuery) > 0;
        if($exists) {
            return array('errorMsg'=>'同名考点已存在，请检查');
        }
        return true;
    }
    
    public function save($posInfo) {
        $checkResult = $this->checkInfo($posInfo);
        if(is_array($checkResult)) {
           return $checkResult; 
        }
        $time = date('Y-m-d H:i:s');
        $userKey = $this->operator->getUserKey();
        if($posInfo['pos_id']) {
            $strQuery = 'UPDATE ' . $this->tableName . ' SET 
                          pos_caption=' . $this->dao->quote($posInfo['pos_caption']) . ',
                          pos_area=' . $this->dao->quote($posInfo['pos_area']) . ',
                          pos_addr=' . $this->dao->quote($posInfo['pos_addr']) . ',
                          pos_telephone=' . $this->dao->quote($posInfo['pos_telephone']) . ',
                          pos_bus=' . $this->dao->quote($posInfo['pos_bus']) . ',
                          pos_map_position=' . $this->dao->quote($posInfo['pos_map_position']) . ',
                          update_at=' . $this->dao->quote($time) . ',
                          update_user=' . $this->dao->quote($userKey) . '
                         WHERE pos_code=' . $this->dao->quote($posInfo['pos_code']);
            if(false == $this->dao->execute($strQuery)) {
                return array('errorMsg'=>'考点信息修改失败');
            }
        } else {
            $strQuery = 'INSERT INTO ' . $this->tableName . '
                        (pos_code,pos_caption,pos_area,pos_addr,pos_telephone,pos_bus,pos_map_position,update_at,update_user)
                        VALUES
                        (' . $this->dao->quote($posInfo['pos_code']) . ',
                         ' . $this->dao->quote($posInfo['pos_caption']) . ',
                         ' . $this->dao->quote($posInfo['pos_area']) . ',
                         ' . $this->dao->quote($posInfo['pos_addr']) . ',
                         ' . $this->dao->quote($posInfo['pos_telephone']) . ',
                         ' . $this->dao->quote($posInfo['pos_bus']) . ',
                         ' . $this->dao->quote($posInfo['pos_map_position']) . ',
                         ' . $this->dao->quote($time) . ',
                         ' . $this->dao->quote($userKey) . ')';
            
            if(false == $this->dao->execute($strQuery)) {
                
                return array('errorMsg'=>'考点信息添加失败');
            }
        }
        $this->setPosAreas($posInfo['pos_code'], $posInfo['pos_areas']);
        return true;
    }
    
    public function delete($posCode) {
        $strQuery = 'UPDATE ' . $this->tableName . '
                     SET is_remove=' . time() . ',
                         update_user=' . $this->dao->quote($this->operator->getUserKey) . ',
                         update_at=' . $this->dao->quote(date('Y-m-d H:i:s')) . '
                     WHERE pos_code=' . $this->dao->quote(SysUtil::safeString($posCode));
        return $this->dao->execute($strQuery);
    }
    
    
    public function find($posCode) {
        static $posArray = array();
        if(false == isset($posArray[$posCode])) {
            $strQuery = 'SELECT * FROM ' . $this->tableName . ' 
                         WHERE pos_code=' . $this->dao->quote($posCode);
            $posInfo = $this->dao->getRow($strQuery);
            $posInfo['pos_areas'] = $this->getPosAreas($posCode);
            $posArray[$posCode] = $posInfo;
        }
        return $posArray[$posCode];
    }
    
    public function getPosList($condition='', $currentPage=1, $pageSize=20) {
        $count = $this->getPosCount($condition);
        $pageCount = ceil($count / $pageSize);
        if($currentPage > $pageCount) $currentPage = $pageCount;
        if($currentPage < 1) $currentPage = 1;
        $strQuery = 'SELECT * FROM ' . $this->tableName . '
                     WHERE is_remove=0 ';
        if($condition) {
            $strQuery .= ' AND ' . $condition;
        }
        $order = ' ORDER BY update_at DESC';
        return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
    }
    
    public function getPosCount($condition='') {
        static $counts = array();
        $key = md5(serialize($condition));
        if(false == isset($counts[$key])) {
            $strQuery = 'SELECT count(1) FROM ' . $this->tableName . ' 
                         WHERE is_remove=0';
            if ($condition) {
                $strQuery .= ' AND ' . $condition;
            }
            
            $count = $this->dao->getOne($strQuery);
            $counts[$key] = $count;
        }
        return $counts[$key];
    }
    
    public function getPosAreas($posCode) {
        $strQuery = 'SELECT * FROM ' . $this->posAreaTable . ' 
                     WHERE pos_code=' . $this->dao->quote($posCode) . '
                     ORDER BY area_code';
        $areaList = $this->dao->getAll($strQuery);
        $areaArray = array();
        foreach ($areaList as $area) {
            $areaArray[$area['area_code']] = $area['area_code'];
        }
        return $areaArray;
    }
    
    public function setPosAreas($posCode, $posAreas) {
        $userKey = $this->operator->getUserKey();
        $time = date('Y-m-d H:i:s');
        $dbAreaCodes = $this->getPosAreas($posCode);
        foreach ($posAreas as $areaCode) {
            if(false == isset($dbAreaCodes[$areaCode])) {
            	$strQuery = 'INSERT INTO ' . $this->posAreaTable . '
            	             (pos_code,area_code,update_user,update_at)
            	             VALUES (
            	             ' . $this->dao->quote($posCode) . ',
            	             ' . $this->dao->quote(SysUtil::safeString($areaCode)) . ',
            	             ' . $this->dao->quote($userKey) . ',
            	             ' . $this->dao->quote($time) . ')';
            	$this->dao->execute($strQuery);
            }
            unset($dbAreaCodes[$areaCode]);
        }
        foreach ($dbAreaCodes as $areaCode) {
        	$strQuery = 'DELETE FROM ' . $this->posAreaTable . '
        	             WHERE pos_code=' . $this->dao->quote($posCode) . '
        	               AND area_code=' . $this->dao->quote($areaCode);
        	$this->dao->execute($strQuery);
        }
    }
    
    public function listPosition(){
    	$sql = "
    		SELECT pos_id, pos_code, pos_caption, pos_addr, pos_telephone
    			FROM $this->tableName
    		WHERE is_remove = '0'
    		ORDER BY pos_code ASC
    	";
    	
    	$list = $this->dao->getAll($sql);
    	$total = count($list);
    	return array('total' => $total, 'rows' => $list);
    }
    
    public function arrayPosition(){
    	$list = $this->listPosition();
    	$list = $list['rows'];
    	$data = array();
    	for($i = 0, $n = count($list); $i < $n; $i++ ){
    		$data[$list[$i]['pos_id']] = $list[$i];
    	}
   
    	return $data;
    }
    
}
?>