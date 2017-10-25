<?php
/*高思网校*/
import ( 'COM.User.User' );
import('COM.Dao.Dao');
class VipSchool extends User {
	private $dao = null;
    private $_userInfo = array();
    private $vipSchoolUserInfo = array();
	
    protected function __construct($userInfo) {
        parent::__construct($userInfo);
        $this->dao = Dao::getDao();
    }
    
	/**
	 * 返回用户类型
	 */
	protected function getType() {
		return USER_TYPE_VIPSCHOOL;
	}
	
	/**
	 * 网校学员账号登录
	 */
	protected function _login() {
		if (false == $this->userInfo ['user_pass']) {
			die ( '登录用户信息数组中必须包含“user_name”以及“user_pass”键值' );
		}
		$vipSchoolUserInfo = $this->getInformation();
        if(false == $vipSchoolUserInfo) {
            return  false;
        }
        $md5Pwd = strtoupper(md5($this->userInfo['user_pass']));
        if(strtoupper($vipSchoolUserInfo['password']) != $md5Pwd) {
            return false;
        }
        return true;
	}
	
	/**
	 * 更新用户登录信息，登录日志等
	 */
	/*protected function _updateInfo() {
		$User = new UserSetModel ();
		// 添加或更新用户登录信息
		$User->addUserG ( $this->userInfo ['bbs_uid'] );
	}*/
	
	/**
	 * 返回登录时用的用户名
	 */
	protected function getName() {
		$userInfo = $this->getInformation ();
		return $userInfo ['username'];
	}
	
	/**
	 * 获取用户详细信息
	 *
	 * @return
	 */
	public function getInformation() {
		if (false == $this->_userInfo) {
			 $vipSchoolUserInfo = $this->getVipSchoolUserInfo();
			 $this->_userInfo = $vipSchoolUserInfo;
		}
		 return $this->_userInfo;
	}
	
	public function getVipSchoolUserInfo(){
		if(false == $this->vipSchoolUserInfo) {
            $strQuery = "SELECT * FROM gss_users  WHERE username='" . $this->userInfo['user_name'] . "'";
            $this->vipSchoolUserInfo = $this->dao->getRow($strQuery);
        }
        return $this->vipSchoolUserInfo;
	}
	
	
	
	/**
     * 更新用户信息，登录日志等
     */
    protected function _updateInfo() {
        $dao = $this->dao;
        $userInfo = $this->getInformation();
        $userType = $this->getUserType();

        $strQuery = 'SELECT * FROM gss_users 
                     WHERE username=' . $dao->quote($userInfo['username']);
        $dbUserInfo = $dao->getRow($strQuery);
        $ip = $_SERVER['REMOTE_ADDR'];
        $time = date('Y-m-d H:i:s');
        if($dbUserInfo) {
            $strQuery = 'UPDATE gss_users 
                         SET lastip=' . $dao->quote($ip) . ',
                             lasttime=' . $dao->quote($time) . '
                         WHERE username=' . $dao->quote($dbUserInfo['username']);
            $strQuery2 = "INSERT INTO gss_login_logs (username,instime,ip) VALUES ('$dbUserInfo[username]','$time','$ip')";
            $dao->execute($strQuery);
            $dao->execute($strQuery2);
            
        } 
    }
	
	protected function _findUser($keyword) {
    	$dao = Dao::getDao();
        $strQuery = 'SELECT * from gss_users WHERE username=' . $dao->quote($keyword); 
        $userList = $dao->getAll($strQuery);
        foreach ($userList as $key=>$user) {
        	$userList[$key]['user_type'] = USER_TYPE_VIPSCHOOL;
        	$userList[$key]['student_name'] = $user['student_name'];
        	$userList[$key]['username'] = $user['username'];
        }
        return $userList;
    }
}
?>