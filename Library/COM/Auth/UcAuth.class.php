<?php
import ( "COM.Auth.Auth" );
import ( "COM.Auth.UcService" );
class UcAuth extends Auth {
	public function auth($userName, $userPwd) {
		$UcService = new UcService ();
		return $UcService->uc_login ( $userName, $userPwd );
	}
	public function getUser($username, $isuid = 0) {
		$UcService = new UcService ();
		return $UcService->uc_getUser ( $username, $isuid );
	}
	// 注册
	public function register($username, $password, $email) {
		$UcService = new UcService ();
		$uid = $UcService->uc_register ( $username, $password, $email );
		$msg = '';
		if ($uid <= 0) {
			if ($uid == - 1) {
				$msg = '您好！您输入的用户名有误，请重新输入用户名。';
			} elseif ($uid == - 2) {
				$msg = '您好！您的输入的注册信息包含不允许注册的词语。';
			} elseif ($uid == - 3) {
				$msg = '您好！您输入的用户名已经存在,请重新输入用户名。';
			} elseif ($uid == - 4) {
				$msg = '您好！您输入的 Email 格式有误,请重新输入Email。';
			} elseif ($uid == - 5) {
				$msg = '您好！您输入的 Email 不允许注册,请重新输入Email。';
			} elseif ($uid == - 6) {
				$msg = '您好！该 Email 已经被注册,请重新输入Email。';
			} else {
				$msg = '您好！在注册时发生错误,请重试。';
			}
		} else {
			$msg = '注册成功';
		}
		return array (
				'uid' => $uid,
				'msg' => $msg 
		);
	}
	
	// 修改密码
	public function resetPwd($uid, $oldpassword, $newpassword, $email, $ignoreoldpw = 0) {
		$UcService = new UcService ();
		$arr_user = $this->getUser ( $uid, 1 );
		if ($arr_user ['uid'] < 1) {
			return array (
					'status' => '-100',
					'msg' => '用户不存在' 
			);
		}
		$r = $UcService->uc_resetPwd ( $arr_user ['username'], $oldpassword, $newpassword, $email, $ignoreoldpw );
		switch ($r) {
			case '1' :
				$msg = '更新成功';
				break;
			case '0' :
				$msg = '没有做任何修改';
				break;
			case '-1' :
				$msg = '旧密码不正确';
				break;
			case '-4' :
				$msg = 'Email 格式有误';
				break;
			case '-5' :
				$msg = 'Email 不允许注册';
				break;
			case '-6' :
				$msg = '该 Email 已经被注册';
				break;
			case '-7' :
				$msg = '没有做任何修改';
				break;
			case '-8' :
				$msg = '该用户受保护无权限更改';
				break;
			default :
				$msg = '未知错误';
				break;
		}
		return array (
				'status' => $r,
				'msg' => $msg 
		);
	}
	
	// 上传头像
	public function uploadAvatar($uid) {
		$UcService = new UcService ();
		return $UcService->uc_uploadAvatar ( $uid );
	}
	
	// 修改邮箱
	public function modifyEmail($uid, $email) {
		$UcService = new UcService ();
		$arr_user = $this->getUser ( $uid, 1 );
		if ($arr_user ['uid'] < 1) {
			return array (
					'status' => '-100',
					'msg' => '用户不存在。' 
			);
		}
		$r = $UcService->uc_resetPwd ( $arr_user ['username'], '', '', $email, '1' );
		switch ($r) {
			case '1' :
				$msg = '更新成功';
				break;
			case '0' :
				$msg = '没有做任何修改';
				break;
			case '-1' :
				$msg = '旧密码不正确';
				break;
			case '-4' :
				$msg = 'Email 格式有误';
				break;
			case '-5' :
				$msg = 'Email 不允许注册';
				break;
			case '-6' :
				$msg = '该 Email 已经被注册';
				break;
			case '-7' :
				$msg = '没有做任何修改';
				break;
			case '-8' :
				$msg = '该用户受保护无权限更改';
				break;
			default :
				$msg = '未知错误';
				break;
		}
		return array (
				'status' => $r,
				'msg' => $msg 
		);
	}
	
	// 修改密码
	public function modifyPassword($uid, $oldpassword, $newpassword) {
		$UcService = new UcService ();
		$arr_user = $this->getUser ( $uid );
		if ($arr_user ['uid'] < 1) {
			return array (
					'status' => '-100',
					'msg' => '用户不存在。' 
			);
		}
		$r = $UcService->uc_resetPwd ( $arr_user ['username'], $oldpassword, $newpassword, '', '0' );
		switch ($r) {
			case '1' :
				$msg = '更新成功';
				break;
			case '0' :
				$msg = '没有做任何修改';
				break;
			case '-1' :
				$msg = '旧密码不正确';
				break;
			case '-4' :
				$msg = 'Email 格式有误';
				break;
			case '-5' :
				$msg = 'Email 不允许注册';
				break;
			case '-6' :
				$msg = '该 Email 已经被注册';
				break;
			case '-7' :
				$msg = '没有做任何修改';
				break;
			case '-8' :
				$msg = '该用户受保护无权限更改';
				break;
			default :
				$msg = '未知错误';
				break;
		}
		return array (
				'status' => $r,
				'msg' => $msg 
		);
	}

	//同步登陆
	public function synlogin($uid){
		$UcService = new UcService ();
		return $UcService -> uc_synlogin($uid);
	}
}
?>