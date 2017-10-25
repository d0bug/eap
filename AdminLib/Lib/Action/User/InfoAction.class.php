<?php
class InfoAction extends UserCommAction {
	public function view() {
		$userKey = $this->loginUser->getUserKey();
		$userInfo = $this->loginUser->getInformation();
		$userInfo['real_name'] = D('Users')->get_userRealName_by_userKey($userKey);
		$userInfo['user_key'] = $userKey;
		if(!$userInfo['user_type']){
			$userInfo['user_type'] = $this->loginUser->getUserType();
		}
		$userInfo = $this->getUserOtherInfo($userInfo);
		$usersModel = D('Users');
		$myRelations = $usersModel->getRelations($userKey,'');
		$TypeArray = $this->loginUser->getAdminUserTypes();
		
		$this->assign(get_defined_vars());
		$this->display();
	}

	protected  function modify() {

	}

	public function passwd(){

	}

	/*账号绑定*/
	public function bind() {
		$userInfo = $this->loginUser->getInformation();
		$userType = $this->loginUser->getUserType();
		$userRoles = $this->loginUser->getAdminUserTypes();
		if($_POST['to_bind']){
			$toBind_way = isset($_POST['tobind_way'])?$_POST['tobind_way']:'';
			$toBind_loginname = isset($_POST['tobind_loginname'])?SysUtil::getUserName($_POST['tobind_loginname']):'';
			$toBind_password = isset($_POST['tobind_password'])?SysUtil::getUserPass($_POST['tobind_password']):'';
			if(!empty($toBind_way) && !empty($toBind_loginname) && !empty($toBind_password)){
				$bindInfo = array('user_name'=>$toBind_loginname, 'user_pass'=>$toBind_password, 'user_type'=>$toBind_way);
				$loginUser = User::getUser($bindInfo);
				if (false == $loginUser->valid($this->cookieName)) {
					$this->error('绑定账号错误,无法绑定');
				} else {
					$userKey = $this->loginUser->getUserKey();
					$userModel = D('Users');
					$myRelations = $userModel->getRelations($userKey,$toBind_way);
					$myRelations = $myRelations[0];
					if(!empty($myRelations)){
						$this->error('对不起，该账号类型下您已经绑定了账号：'.$myRelations['rel_user_name']);
					}else{
						if($userModel->add_sys_user_roles(array('app_name'=>APP_NAME,'user_key'=>$toBind_way.'-'.$toBind_loginname,'role_id'=>$toBind_way,'create_user'=>$userInfo['user_name']))){
							$bindRelationsInfo = array('user_key'=>$userKey,'rel_user_key'=>$toBind_way.'-'.$toBind_loginname,'rel_user_type'=>$toBind_way,'rel_user_name'=>$toBind_loginname,'create_user'=>$userInfo['user_name']);
							if($userModel->addUserRelations($bindRelationsInfo)){
								$this->success('账号绑定成功');
							}else{
								$this->error('账号绑定失败');
							}
						}
					}
				}
			}else{
				$this->error('请将绑定信息填写完整');
			}
		}else{
			$this->assign(get_defined_vars());
			$this->display();
		}
	}
	
	/*解除单个账号绑定*/
	public function release_bind(){
		$userKey = isset($_GET['user_key'])?urldecode($_GET['user_key']):'';
		$relUserKey = isset($_GET['rel_user_key'])?urldecode($_GET['rel_user_key']):'';
		if(!empty($userKey) && !empty($relUserKey)){
			$userModel = D('Users');
			if($userModel->releaseBind($userKey,$relUserKey)){
				$this->success('解除绑定成功');
			}else{
				$this->error('解除绑定失败');
			}
		}else{
			$this->error('非法操作');
		}
	}
}
?>