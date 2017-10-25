<?php
import('COM.Dao.Dao');
class Teacher extends User {
	private static $teachers = array();
    /**
     * 返回用户类型
     */
    protected function getType() {
        
    }
	
    /**
     * 返回用户名
     */
    protected function getName() {
        return $this->userInfo['userName'];
    }
    
    public static function getTeacher($teacherCode) {
    	$teacherCode = strtoupper($teacherCode);
    	if(false == isset(self::$teachers[$teacherCode])) {
    		self::$teachers[$teacherCode] = new Teacher(array('userName'=>$teacherCode));
    	}
    	return self::$teachers[$teacherCode];
    }
    
    /**
     * 获取用户详细信息
     */
    public function getInformation() {
        import('ORG.Util.NCache');
        $cache = NCache::getCache();
        $teacherCode = $this->getName();
        $teacherInfo = $cache->get('tInfo', 'tInfo_' . $teacherCode);
        if(false == $teacherInfo) {
        	$teacherInfo = array();
        	$dao = Dao::getDao();
        	$strQuery = 'SELECT * FROM t_teacherinfo 
        				 WHERE t_scode=' . $dao->quote($teacherCode);
        	$dbTeacherInfo = $dao->getRow($strQuery);
        	if($dbTeacherInfo) {
        		$tPicArray = explode(',', $dbTeacherInfo['tpic']);
        		$teacherInfo['teacherName'] = $dbTeacherInfo['t_sname'];
        		$teacherInfo['teacherPic'] = 'http://www.gaosiedu.com/teacher/upload/' . $tPicArray[0];
        		$teacherInfo['teacherLink'] = 'http://www.gaosiedu.com/teacher/view/' . $teacherCode;
        	} else {
        		#$myDao = Dao::getDao('MYSQL_CONN');
        	}
        	$cache->set('tInfo', 'tInfo_' . $teacherCode, $teacherInfo);
        }
        return $teacherInfo;
    }
    
    /**
     * 大班兼职教师登录
     */
    protected function _login() {
        
    }
    
    /**
     * 更新用户信息，登录日志等
     */
    protected function _updateInfo() {
        
    }
    
    protected function _findUser($keyword) {
        
    }
}
?>