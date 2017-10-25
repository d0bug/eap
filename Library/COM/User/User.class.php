<?php
require_once(dirname(__FILE__) . '/User.const.php');
import('COM.Debug.Debugger');
Debugger::addErrors('User', dirname(__FILE__) . '/User.errors.php');
import('ORG.Util.Cookie');
abstract class User {
    protected $userInfo = '';
    protected $userType = '';
    protected $userName = '';
    
    protected $loginExpire = '';
    
    
    private static $users = array();

    protected function __construct($userInfo){
        $this->userInfo = $userInfo;
    }
    
    protected function setUserName($userInfo) {
        $userType = $this->getUserType($userInfo);
        $userName = $userInfo['user_name'];
        $this->userName = $userName;
    }
    
    public function setLoginExpire($loginExpire){
    	$this->loginExpire = $loginExpire;
    }
    
    /**
     * 从用户信息中提取用户类型,私有方法，供getUserType调用,因为前台后台用户角色不同，所以使用抽象方法获取
     */
    abstract protected function getType();
    
    /**
     * 从用户信息中提取用户名，当类型为EMPLOYEE时需根据用户名解析具体类型(员工，兼职教师，学校用户)，后两种需要数据库支持
     */
    abstract protected function getName();
    
    /**
     * 获取用户详细信息
     */
    abstract public function getInformation();
    
    /**
     * 抽象登录方法，由子类实现
     */
    abstract protected function _login();
    
    /**
     * 抽象方法，更新用户信息，登录日志等
     */
    abstract protected function _updateInfo();

    
    /**
     * 用户登录并设置COOKIE
     * @param String $cookieName
     * @return boolean
     */
    public function login($cookieName) {
        if ($this->_login()) {
            $this->_updateInfo();
            $this->setLoginUser($cookieName, $this->userInfo, $this->loginExpire);
            return true;
        }
        return false;
    }
    
    /**
     * 检查绑定账号信息是否正确
     * @param String 
     * @return boolean
     */
    public function valid($cookieName){
    	return $this->_login();
    }

    /**
     * 获取实际用户类型，员工，教师，家长，学生，学校用户
     * @param Array $userInfo
     * @return Integer
     */
    public function getUserType() {
        if (false == $this->userType) {
            $this->userType = $this->getType();
        }
        return $this->userType;
    }
    
    public static function getAdminUserTypes() {
        return array(USER_TYPE_EMPLOYEE=>'内部员工',
                     USER_TYPE_TEACHER=>'兼职教师(大班)',
                     USER_TYPE_VTEACHER=>'VIP社会兼职教师',
                     USER_TYPE_SCHOOL=>'学校用户',
                     USER_TYPE_SIQUAN_JS=>'思泉语文(教师)',
                     USER_TYPE_SIQUAN_ZJ=>'思泉语文(助教)');
        
    }
    
    /**
     * 获取登陆名
     * @return type
     */
    public function getUserName() {
        if (false == $this->userName) {
            $this->userName = $this->getName();
        }
        return $this->userName;
    }
    
    /**
     * 获取用户名唯一KEY值，
     * @return String
     */
    public function getUserKey() {
        return $this->getUserType() . '-' . $this->getUserName();
    }

    #设置登录用户的Cookie信息
    private static function setLoginUser($cookieName, $userInfo, $loginExpire = '') {
		self::checkInfoMapper ( $userInfo );
		$cookieValue = $userInfo ['user_type'] . "\t" . $userInfo ['user_name'] . "\t" . $userInfo ['user_pass'];
		Cookie::set ( $cookieName, $cookieValue, $loginExpire);
	}
    
    public function logout($cookieName) {
        Cookie::delete($cookieName);
        return true;
    }
    
    #从Cookie信息中取得用户信息
    public static function getLoginUser($cookieName='') {
        if(false == $cookieName) {
            $cookieName = C('USER_COOKIE_NAME');
        }
        $cookieValue = Cookie::get($cookieName);
        if ($cookieValue) {
            list($userType, $userName, $userPass) = explode("\t", $cookieValue);
            $userInfo = array('user_name'=>$userName, 'user_pass'=>$userPass, 'user_type'=>$userType);
            return self::getUser($userInfo);
        }
        return null;
    }
    
    #设置登录Cookie所必须的字段
    private static function getInfoMapper() {
        return array('user_name', 'user_pass', 'user_type');
    }
    
    #检查登录Cookie信息是否符合要求
    protected static function checkInfoMapper($userInfo) {
        if (false == is_array($userInfo)) {
            $userInfo = array();
        }
        $infoMapper = self::getInfoMapper();
        foreach($infoMapper as $key) {
            if ('' == $userInfo[$key]) {
                Debugger::trace('User', '1001');
            }
        }
    }

    /**
     * 获取用户实例（前台：学生， 家长，教师；后台：员工）
     * @return User
     */
    public static function getUser($userInfo) {
        $userType = $userInfo['user_type'];
        if(false == isset($userInfo['user_name']) || false == isset($userInfo['user_type'])) {
            die('用户信息数组必须包含“user_name”以及“user_type”键');
        }
        $userKey = md5($userInfo['user_type'] . "\t" . $userInfo['user_name']);
        if ($userType == USER_TYPE_STUDENT) {
            #前台学生用户
            $className = 'Student';
        } else if ($userType == USER_TYPE_PARENTS) {
            #前台家长用户
            $className = 'Parents';
        } else if ($userType == USER_TYPE_TEACHER){
            #前台教师用户
            $className = 'Teacher';
        } else if($userType == USER_TYPE_VTEACHER) {
            $className = 'VTeacher';
        } else if($userType == USER_TYPE_SIQUAN_JS || $userType == USER_TYPE_SIQUAN_ZJ){
        	$className = 'SiquanUser';
        }else if($userType == USER_TYPE_VIPSCHOOL) {
            $className = 'VipSchool';
        }else {
            #后台管理员，教师，学校用户
            $userType = USER_TYPE_EMPLOYEE;
            $className = 'Employee';
        }
        if (false == self::$users[$userType][$userKey]) {
            $company = C('COMPANY');
            import('COM.' . $company . '.' . $className);
            $user = new $className($userInfo);
            self::$users[$userType][$userKey] = $user;
        }
        return self::$users[$userType][$userKey];
    }
    
    public static function findUser($userType, $keyword) {
        $company = C('COMPANY');
        switch ($userType) {
            case USER_TYPE_STUDENT:
                $className = 'Student';
                import('COM.' . $company . '.' . $className);
                return Student::_findUser($keyword);
            break;
            case USER_TYPE_PARENTS:
                $className = 'Parents';
                import('COM.' . $company . '.' . $className);
                return Parents::_findUser($keyword);
            break;
            case USER_TYPE_EMPLOYEE:
                $className = 'Employee';
                import('COM.' . $company . '.' . $className);
                return Employee::_findUser($keyword);
            break;
            case USER_TYPE_TEACHER:
                $className = 'Teacher';
                import('COM.' . $company . '.' . $className);
                return Teacher::_findUser($keyword);
            break;
            case USER_TYPE_VTEACHER:
                $className = 'VTeacher';
                import('COM.' . $company . '.' . $className);
                return VTeacher::_findUser($keyword);
            break;
            case USER_TYPE_SCHOOL:
                $className = 'SchoolUser';
                import('COM.' . $company . '.' . $className);
                return SchoolUser::_findUser($keyword);
            break;
            case USER_TYPE_SIQUAN:
                $className = 'SiquanUser';
                import('COM.' . $company . '.' . $className);
                return SiquanUser::_findUser($keyword);
            break;
            case USER_TYPE_VIPSCHOOL:
                $className = 'VipSchool';
                import('COM.' . $company . '.' . $className);
                return VipSchool::_findUser($keyword);
            break;
        }
    }
    
    /**
     * 非静态定义的抽象静态方法
     * @param String $keyword
     * @return array
     */
    abstract protected function _findUser($keyword);

};
?>