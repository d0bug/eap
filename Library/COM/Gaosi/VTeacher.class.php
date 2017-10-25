<?php
class VTeacher extends User {
    private $dao = null;
    private static $userTable = 'vip_user';
    private $teacherTable = 'bs_teacher';
    private $vipUserInfo = array();
    private $_userInfo = array();
    
    
    protected function __construct($userInfo) {
        parent::__construct($userInfo);
        $this->dao = Dao::getDao();
    }
    /**
     * 返回用户类型
     */
    protected function getType() {
        return USER_TYPE_VTEACHER;
    }
    
    /**
     * 返回用户名
     */
    protected function getName() {
        $teacherInfo = $this->getInformation();
        return $teacherInfo['user_name'];
    }
    
    /**
     * 获取用户详细信息
     */
    public function getInformation() {
        if(false == $this->_userInfo) {
            $userInfo = $this->userInfo;
            $vipUserInfo = $this->getVipUserInfo();
            $userInfo['real_name'] = $vipUserInfo['user_realname'];
            $userInfo['mail'] = $vipUserInfo['email'];
            /*if($vipUserInfo['teacherid']) {
                $teacherInfo = $this->getTeacherInfo($vipUserInfo['teacherid']);
                $userInfo['teacher_info'] = $teacherInfo;
                if(false == $userInfo['mail']) {
                    $userInfo['mail'] = $teacherInfo['semail'];
                }
            }*/
            $this->_userInfo = $userInfo;
        }
        
        return $this->_userInfo;
    }
    
    /*private function getTeacherInfo($teacherId) {
        $strQuery = 'SELECT * FROM ' . $this->teacherTable . '
                     WHERE id=' . abs($teacherId);
        return $this->dao->getRow($strQuery);
    }*/
    
    private function getVipUserInfo() {
        if(false == $this->vipUserInfo) {
            $strQuery = 'SELECT * FROM sys_users
                         WHERE is_employee=0 AND is_teacher = 1 AND user_name=' . $this->dao->quote($this->userInfo['user_name']);
            $this->vipUserInfo = $this->dao->getRow($strQuery);
        }
        return $this->vipUserInfo;
    }
    
    /**
     * VIP兼职教师登录
     */
    protected function _login() {
        $vTeacherInfo = $this->getVipUserInfo();
        if(false == $vTeacherInfo || $vTeacherInfo['is_removed'] == 1) {
            return  false;
        }
        $md5Pwd = strtoupper(md5($this->userInfo['user_pass']));
        if(strtoupper($vTeacherInfo['user_passwd']) != $md5Pwd) {
            return false;
        }
        return true;
    }
    
    /**
     * 更新用户信息，登录日志等
     */
    protected function _updateInfo() {
        $dao = $this->dao;
        $userKey = $this->getUserKey();
        $userInfo = $this->getInformation();
        $userType = $this->getUserType();
        $isEmployee = 0;
        $isTeacher = 1;
        $strQuery = 'SELECT * FROM sys_users 
                     WHERE user_key=' . $dao->quote($userKey);
        $dbUserInfo = $dao->getOne($strQuery);
        $ip = $_SERVER['REMOTE_ADDR'];
        $time = date('Y-m-d H:i:s');
        if($dbUserInfo) {
            $strQuery = 'UPDATE sys_users 
                         SET last_ip=' . $dao->quote($ip) . ',
                             last_time=' . $dao->quote($time) . '
                         WHERE user_key=' . $dao->quote($userKey);
        } else {
            $strQuery = 'INSERT INTO sys_users
                         (user_key,user_name,user_realname,is_employee,is_teacher,is_school,user_email,last_ip,last_time,create_user,create_at)
                         VALUES (
                         ' . $dao->quote($userKey) . ',
                         ' . $dao->quote($this->getUserName()) . ',
                         ' . $dao->quote($userInfo['real_name']) . ',
                         ' . abs($isEmployee) . ',
                         ' . abs($isTeacher) . ',0,
                         ' . $dao->quote($userInfo['mail']) . ',
                         ' . $dao->quote($ip) . ',
                         ' . $dao->quote($time) . ',
                         ' . $dao->quote($userKey) . ',
                         ' . $dao->quote($time) . ')';
        }
        
        $dao->execute($strQuery);
    }
    
    protected function _findUser($keyword) {
    	$dao = Dao::getDao();
        $strQuery = 'SELECT * from sys_users 
        			 WHERE is_employee =0 AND is_teacher =1 AND user_name=' . $dao->quote($keyword) . ' 
        			   OR user_realname=' . $dao->quote($keyword);
        $userList = $dao->getAll($strQuery);
        foreach ($userList as $key=>$user) {
        	$userList[$key]['user_type'] = USER_TYPE_VTEACHER;
        	$userList[$key]['user_key'] = 'VTeacher-' . $user['user_name'];
        	$userList[$key]['real_name'] = $user['user_realname'];
        	$userList[$key]['user_name'] = $user['user_name'];
        }
        return $userList;
    }
}
?>