<?php
class SiquanUser extends User {
	private $dao = null;
	private $bs_teacher = 'BS_Teacher';
	private $sys_user_roles = 'sys_user_roles';
	private $sys_roles = 'sys_roles';
	private $sys_users = 'sys_users';
	private $teacherInfo = array();
	private $_userInfo = array();


	protected function __construct($userInfo) {
		parent::__construct($userInfo);
		$this->dao = Dao::getDao();
	}
	/**
     * 返回用户类型
     */
	protected function getType() {
		if($this->userInfo['user_type'] == 'SiquanZJ'){
			return USER_TYPE_SIQUAN_ZJ;
		}else{
			return USER_TYPE_SIQUAN_JS;
		}

	}

	/**
     * 返回用户名
     */
	protected function getName() {
		$teacherInfo = $this->getTeacherInfo();
		return $teacherInfo['sname'];
	}

	/**
     * 获取用户详细信息
     */
	public function getInformation() {
		if(false == $this->_userInfo) {
			$userInfo = $this->userInfo;
			$teacherInfo = $this->getTeacherInfo();
			$userInfo['user_key'] = $this->getUserKey();
			$userInfo['real_name'] = $teacherInfo['real_name'];
			$userInfo['mail'] = $teacherInfo['semail'];
			$userInfo['scode'] = $teacherInfo['scode'];
			$userInfo['nkind'] = $teacherInfo['nkind'];
			$userInfo['sphone'] = $teacherInfo['sphone'];
			$userInfo['bvalid'] = $teacherInfo['bvalid'];
			unset($userInfo['user_pass']);
			$this->_userInfo = $userInfo;
		}
		return $this->_userInfo;
	}

	private function getTeacherInfo() {
		if(false == $this->teacherInfo) {
			$strQuery = 'SELECT [sName],[sCode],[sPassword],[sRealName] as real_name,[bValid],[nKind],[bLoginValid],[sDeptCode],[sPhone],[sEmail] FROM ' . $this->bs_teacher . '
                         WHERE [sName]=' . $this->dao->quote($this->userInfo['user_name']);
			if($this->userInfo['user_type'] == 'SiquanZJ'){
				$strQuery .= ' AND [nKind] = 1';
			}else{
				$strQuery .= ' AND [nKind] = 2';
			}
			$this->teacherInfo = $this->dao->getRow($strQuery);
		}
		return $this->teacherInfo;
	}

	/**
     * 思泉语文教师/助教登录
     */
	protected function _login() {
		$teacherInfo = $this->getTeacherInfo();
		if(false == $teacherInfo || $teacherInfo['bvalid'] != 1 || $teacherInfo['bloginvalid'] != 1 || ($teacherInfo['nkind'] != 1 && $teacherInfo['nkind'] != 2)) {
			return  false;
		}
		$md5Pwd = strtoupper(md5($this->userInfo['user_pass']));
		if(strtoupper($teacherInfo['spassword']) != $md5Pwd) {
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
                         (user_key,user_name,user_realname,is_employee,is_teacher,is_school,is_siquan,user_email,last_ip,last_time,create_user,create_at)
                         VALUES (
                         ' . $dao->quote($userKey) . ',
                         ' . $dao->quote($this->getUserName()) . ',
                         ' . $dao->quote($userInfo['real_name']) . ',
                         ' . abs($isEmployee) . ',
                         ' . abs($isTeacher) . ',0,
                         ' . abs($userInfo['nKind']) . ',
                         ' . $dao->quote($userInfo['email']) . ',
                         ' . $dao->quote($ip) . ',
                         ' . $dao->quote($time) . ',
                         ' . $dao->quote($userKey) . ',
                         ' . $dao->quote($time) . ')';
		}

		$dao->execute($strQuery);
		if(!$dbUserInfo){
			$this->add_sys_user_roles(array('roleName'=>'高思作文用户','app_name'=>APP_NAME,'group_name'=>'Essay'));
		}
	}

	//给教师/助教赋予高思作文系统角色权限
	protected function add_sys_user_roles($arr){
		$roleId = $this->dao->getOne('SELECT [role_id] FROM '.$this->sys_roles.' WHERE [role_caption] ='.$this->dao->quote($arr['roleName']).' AND [app_name]='.$this->dao->quote($arr['app_name']).' AND [group_name] ='.$this->dao->quote($arr['group_name']));
		if(!empty($roleId)){
			$userKey = $this->getUserKey();
			$isExist = $this->dao->getOne('SELECT COUNT(*) FROM '.$this->sys_user_roles.' WHERE [user_key] = '.$this->dao->quote($userKey).' AND [role_id] = '.$this->dao->quote($roleId));
			if($isExist == 0){
				$this->dao->execute('INSERT INTO '.$this->sys_user_roles.' ([app_name],[user_key] ,[role_id],[create_user],[create_at]) VALUES ('.$this->dao->quote(APP_NAME).','.$this->dao->quote($userKey).','.$this->dao->quote($roleId).','.$this->dao->quote('').','.$this->dao->quote(date('Y-m-d H:i:s')).')');
			}
		}
	}

	protected function _findUser($keyword) {

	}
}
?>