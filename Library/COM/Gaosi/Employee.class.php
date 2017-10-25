<?php 
import('COM.Auth.Auth');
import('COM.User.User');
import('COM.SysUtil');
import('COM.Dao.Dao');

class Employee extends User {
    private $_userType = false;
    private $empInfo = array();
    public $instance = null;
    
    /**
     * 获取数据访问实例
     * @return Dao
     */
    private static function getDao() {
        static $dao = null;
        if(null === $dao) {
            $dao = Dao::getDao();
        }
        return $dao;
    }
    
    public static function getTypeArray() {
        return array(USER_TYPE_EMPLOYEE=>'内部员工', USER_TYPE_TEACHER=>'兼职教师', USER_TYPE_SCHOOL=>'学校用户', USER_TYPE_VTEACHER=>'VIP兼职教师');
    }
    
    /**
     * 获取用户类型
     * @return Int
     */
    protected function getType() {
        if (false == $this->_userType) {
            $userName = $this->userInfo['user_name'];
            if (SysUtil::isEmail($userName)) {
                #可能为学校用户或内部员工使用高思邮箱登陆
                $empMailSuffix = C('EMP_MAIL_SUFFIX');
                if (stristr($userName, $empMailSuffix)) {
                    $this->_userType = USER_TYPE_EMPLOYEE;
                } else {
                    $this->_userType = USER_TYPE_SCHOOL;
                }
            } else if (preg_match('/^[\x{4e00}-\x{9fa5}]+/u', $userName)) {
                #教师用户,通过中文名登录
                $this->_userType = USER_TYPE_TEACHER;
            } else if (preg_match('/^VP\d+/')) {
                #VIP兼职教师
                $this->_userType = USER_TYPE_VTEACHER;
            } else {
                #内部员工
                $this->_userType = USER_TYPE_EMPLOYEE;
            }
        }
        
        return $this->_userType;
    }
    
    /**
     * 获取用户名
     * @return String
     */
    protected function getName() {
        $empInfo = $this->getInformation();
        return $this->empInfo['user_name'];
    }
    
    /**
     * 获取用户详细信息
     * @return Array
     */
    public function getInformation() {
        if (false == $this->empInfo) {
            $instance = $this->getInstance();
            $empInfo = $instance->getUserInfo();
            /*todo:这里可以获取一下用户的角色或者等级信息，
                 以便用户组变更时权限自动更新,后续根据需求确定*/
            $this->empInfo = $empInfo;
        }
        
        return $this->empInfo;
    }


    /**
     * 获取用户实例
     * @return Employee
     */
    public function getInstance() {
        if(null == $this->instance) {
            $userType = $this->getUserType();
            if (USER_TYPE_EMPLOYEE == $userType) {
                import('COM.Ldap.LdapUser');
                $this->instance = LdapUser::getUser($this->userInfo['user_name']);
                if($this->userInfo['user_pass']) {
                    $this->instance->auth($this->userInfo['user_pass']);
                } else {
                    $this->instance->getLdap()->bindLoginUser();
                }
            } else if (USER_TYPE_TEACHER == $userType){
                import('COM.Gaosi.Teacher');
                $this->instance = new Teacher();
            } else if (USER_TYPE_SCHOOL == $userType) {
                import('COM.Gaosi.SchoolUser');
                $this->instance = new SchoolUser();
            }
        }
        return $this->instance;
    }
    
    /**
     * 操作员登陆功能
     * @return Boolean
     */
    protected function _login() {
        $userType = $this->getUserType();
        if(false == $this->userInfo['user_pass']) {
            die('登录用户信息数组中必须包含“user_name”以及“user_pass”键值');
        }
        if (USER_TYPE_EMPLOYEE == $userType) {
            $loginResult = $this->empLogin();
        } else {
            $loginResult = $this->userLogin();
        }
        return $loginResult;
    }
    
    /**
     * 内部员工登录，当类型为USER_TYPE_EMPLOYEE时
     * @return Boolean
     */
    private function empLogin() {
        $authType = preg_replace('/[^a-z0-9_-]/i', '', C('EMP_AUTH_TYPE'));
        eval('$authType=' . $authType . ';');
        $auth = Auth::getAuth($authType);
        $authResult  = $auth->auth($this->userInfo['user_name'], $this->userInfo['user_pass']);
        return $authResult;
    }
    
    /**
     * 添加登录日志，最后登录时间
     * @return void
     */
    protected  function _updateInfo() {
        $dao = self::getDao();
        $userKey = $this->getUserKey();
        $userInfo = $this->getInformation();
        $userType = $this->getUserType();
        $isEmployee = $userType == USER_TYPE_EMPLOYEE;
        $isTeacher = $userType == USER_TYPE_TEACHER;
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
    
    /**
     * 兼职教师或学校用户登录
     * @return Boolean
     */
    private function userLogin() {
        //todo:
    }
    
    protected function _findUser($keyword) {
        $cookieName = C('USER_COOKIE_NAME');
        $loginUser = User::getLoginUser($cookieName);
        $userInfo = $loginUser->getInformation();
        $instance = $loginUser->getInstance();
        $userList = $instance->findUsers($keyword);
        foreach ($userList as $k=>$v) {
            $userList[$k]['user_type'] = USER_TYPE_EMPLOYEE;
            $userList[$k]['user_key'] = USER_TYPE_EMPLOYEE . '-' . $v['user_name'];
        }
        
        return $userList;
    }
    
    

};
?>