<?php
abstract class SystemCommAction extends AppCommAction {
	public function __construct() {
		parent::__construct();
	}

	protected function notNeedLogin() {
		return array();
	}

	/*判断当前登录用户是否为vip用户*/
	public function checkIsVipPrimaryUser($userKey){
		if(!$userKey){
			$userKey = $this->loginUser->getUserKey();
		}
		$userModel = D('Users');
		$roleId= $userModel->get_roleId(array('roleName'=>'VIP初级用户','app_name'=>APP_NAME,'group_name'=>'Vip'));
		$is_vipPrimaryUser = $userModel->get_userInfoFromUserRoles(array('role_id'=>$roleId,'user_key'=>$userKey));
		if($is_vipPrimaryUser){
			return true;
		}
		if(in_array(end(explode('-',$userKey)),C('SUPER_USERS'))){
			return true;
		}
		return false;
	}


	public function checkIsBeBindTo($userKey){
		if(!$userKey){
			$userKey = $this->loginUser->getUserKey();
		}
		return D('Users')->getRelations($userKey);
	}
};
?>
