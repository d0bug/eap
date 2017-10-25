<?php
require_once APP_PATH . '/uc_client/client.php';
class UcService {
	public function __construct() {
		
	}
	
	// 在UCenter注册用户信息
	public function uc_register($username, $password, $email) {
		$uid = uc_user_register ( $username, $password, $email );
		return $uid;
	}
	
	// 在UCenter登录登录
	public function uc_login($username, $password) {
		if (preg_match ( "/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $username )) {
			$isuid = 2;
		} else {
			$isuid = 0;
		}
		list ( $uid, $username, $password, $email ) = uc_user_login ( $username, $password, $isuid );
		return array (
				'uid' => $uid,
				'username' => $username,
				'password' => $password,
				'email' => $email 
		);
	}
	
	// 获取用户信息
	public function uc_getUser($username, $isuid ) {
		if ($data = uc_get_user ( $username, $isuid )) {
			list ( $uid, $username, $email ) = $data;
			return array (
					'uid' => $uid,
					'username' => $username,
					'email' => $email 
			);
		} else {
			return array (
					'uid' => - 1,
					'username' => '',
					'email' => '' 
			);
		}
	}
	public function uc_synlogin($uid) {
		return uc_user_synlogin ( $uid );
	}
	
	public function uc_resetPwd($username, $oldpassword, $newpassword, $email, $ignoreoldpw) {
		return uc_user_edit ( $username, $oldpassword, $newpassword, $email, $ignoreoldpw );
	}
	
	public function uc_getUsername($uid) {
		if ($data = uc_get_user ( $uid, 1 )) {
			list ( $uid, $username, $email ) = $data;
			return array (
					'uid' => $uid,
					'username' => $username,
					'email' => $email
			);
		} else {
			return array (
					'uid' => - 1,
					'username' => '',
					'email' => ''
			);
		}
	}
	
	public function uc_uploadAvatar($uid) {
		return uc_avatar ( $uid );
	}
	
}
?>