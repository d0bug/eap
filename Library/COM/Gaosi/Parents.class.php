<?php
import ( "COM.Auth.UcAuth" );
import ( 'COM.User.User' );
class Parents extends User {
	private $parentsInfo = array ();
	/**
	 * 返回用户类型
	 */
	protected function getType() {
		return USER_TYPE_PARENTS;
	}
	
	/**
	 * 家长账号登录
	 */
	protected function _login() {
		if (false == $this->userInfo ['user_pass']) {
			die ( '登录用户信息数组中必须包含“user_name”以及“user_pass”键值' );
		}
		$UcAuth = Auth::getAuth ( AUTH_BY_UCENTER );
		$authResult = $UcAuth->auth ( $this->userInfo ['user_name'], $this->userInfo ['user_pass'] );
		if ($authResult ['uid'] > 0) {
			$this->userInfo ['bbs_uid'] = $authResult ['uid']; // 存储uc返回来的uid
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 更新用户登录信息，登录日志等
	 */
	protected function _updateInfo() {
		$User = new UserSetModel ();
		// 添加或更新用户登录信息
		$User->addUserG ( $this->userInfo ['bbs_uid'] );
	}
	
	/**
	 * 返回登录时用的用户名
	 */
	protected function getName() {
		$parentsInfo = $this->getInformation ();
		return $parentsInfo ['uname'];
	}
	
	/**
	 * 获取用户详细信息
	 *
	 * @return
	 */
	public function getInformation() {
		if (false == $this->parentsInfo) {
			// 除了登录，再次从uc获取登录数据
			$UcAuth = Auth::getAuth ( AUTH_BY_UCENTER );
			$authResult = $UcAuth->auth ( $this->userInfo ['user_name'], $this->userInfo ['user_pass'] );
			// 再次核实用户身份，如果用户的账号和密码更改，退出到登录页面
			if ($authResult ['uid'] < 0) {
				$this->logout ( C ( 'USER_COOKIE_NAME' ) );
			}
			$UserSet = new UserSetModel ();
			$stuBase = $UserSet->getStuBase ( $authResult ['uid'] );
			$this->parentsInfo = array (
					'uid' => $authResult ['uid'],
					'uname' => $authResult ['username'],
					'is_guardian' => '1',
					'is_student' => '0',
					'guardianInfo' => array (
							'uid' => $authResult ['uid'],
							'username' => $authResult ['username'],
							'email' => $authResult ['email'] 
					),
					'studentInfo' => $stuBase 
			);
		}
		return $this->parentsInfo;
	}
	
	/**
	 * 获取用户详细信息
	 *
	 * @return
	 */
	public function checkUserStatus() {
		// 除了登录，再次从uc获取登录数据
		$UcAuth = Auth::getAuth ( AUTH_BY_UCENTER );
		$authResult = $UcAuth->auth ( $this->userInfo ['user_name'], $this->userInfo ['user_pass'] );
		// 再次核实用户身份，如果用户的账号和密码更改，退出到登录页面
		if ($authResult ['uid'] > 0) {
			return $authResult ['uid'];
		} else {
			return 0;
		}
	}
	
	protected function _findUser($keyword) {
	}
}
?>