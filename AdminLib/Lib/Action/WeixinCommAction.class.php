<?php
import('ORG.Util.Session');
Session::start();
require_once CONF_PATH . '/const.php';
import ( "COM.SysUtil" );
import('ORG.Util.Cookie');
import ( "COM.User.User" );
import('COM.Auth.Permission');
import('COM.Logger.AppLogger');
abstract class WeixinCommAction extends Action {
	protected $weixinId = '';
	protected $openId = '';
	protected $wxUser = array();
	protected $userInfo = array();
	protected $loginUser = null;
	protected $cookieName = '';
	protected $aclKey = '';
	protected $ua;
	public function __construct() {
		$this->ua = $_SERVER['HTTP_USER_AGENT'];
		$this->weixinId = trim($_GET['weixinid']);
		$this->openId = trim($_GET['openid']);
		$this->cookieName = C('USER_COOKIE_NAME');
		$this->aclKey = GROUP_NAME . '-' . MODULE_NAME . '-' . ACTION_NAME;
	}


	public function  checkWeixinInfo(){
		$userModel = D('Users');
		$weixinModel = D('WeixinVip');
		if($this->openId) {
			$_SESSION['openId'] = $this->openId;
		} else {
			$this->openId = $_SESSION['openId'];
		}
		if(!$this->openId){
			die('非法操作，微信id丢失，请重新<a href="'.U('/Vip/Weixin/login').'">登录</a>');
		}else{
			$wxUser = $weixinModel->findBindUserInfo(C('appId'), $this->openId);
			$this->wxUser = $wxUser;
			if(empty($wxUser)) {
				$this->redirect(U('/Vip/Weixin/login', array('openId'=>$this->openId), false));
			}else{
				$userInfo = $userModel->get_userInfo($wxUser['user_key']);
				$userInfo['user_type_key'] = reset(explode('-',$wxUser['user_key']));
				$userInfo['user_type'] = $this->get_userTypeName($userInfo['user_type_key']);
				$userInfo['sCode'] = $wxUser['teachercode'];
				$userInfo['real_name'] = $userInfo['user_realname'];
				$userInfo['openId'] = $this->openId;
			}
			$this->checkTeacherCode($userInfo['sCode']);
			$this->userInfo = $userInfo;
		}
		return $userInfo;
	}

	

	/**
     * 重载框架的DISPLAY方法， 所有自定义常量赋值为模板变量
     * @param String $templateFile
     * @param String $charset
     * @param String $contentType
     * @param String $content
     * @param String $prefix
     */
	public function display($templateFile='',$charset='',$contentType='',$content='',$prefix='') {
		$allConstants = get_defined_constants(true);
		$userConstants = $allConstants['user'];
		$this->assign($userConstants);
		$time = time();
		$this->assign('_time', $time);
		parent::display($templateFile,$charset,$contentType,$content,$prefix);
	}


	public function get_userKey($userInfo){
		return $userInfo['user_type'].'-'.$userInfo['user_name'];
	}


	public function get_userTypeName($type){
		switch($type){
			case 'Employee':
				$typeName = '内部员工';break;
			case 'Teacher':
				$typeName = '兼职教师(大班)';break;
			case 'VTeacher':
				$typeName = 'VIP社会兼职教师';break;
		}
		return $typeName;
	}


	public function get_currentUserInfo($userInfo=array()){
		$userModel = D('Users');
		$userInfo['user_key'] = $this->get_userKey($userInfo);
		$userInfo['user_type_key'] = $userInfo['user_type'];
		$userInfo['user_type'] = $this->get_userTypeName($userInfo['user_type']);
		$userInfo['real_name'] = $userModel->get_userRealName_by_userKey($userInfo['user_key']);
		$userInfo['sCode'] =$userModel->get_teacherCode($userInfo);
		return  $userInfo;
	}

	public function checkTeacherCode($teacherCode){
		if(empty($teacherCode)){
			echo '对不起，无法匹配到您的教师编码，请联系管理员';die;
		}
		return true;
	}
	
	
	/*获取客户端IP*/
	public function getClientIp(){
		if(!empty($_SERVER["HTTP_CLIENT_IP"])){//当客户端使用的是自己的服务器时
			$cip = $_SERVER["HTTP_CLIENT_IP"];
		}else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){//当客户端使用的是代理的服务器时
			$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}else if(!empty($_SERVER["REMOTE_ADDR"])){
			$cip = $_SERVER["REMOTE_ADDR"];
		}else{
			$cip = '';
		}
		$cips=array();
		preg_match('/[\d\.]{7,15}/', $cip, $cips);
		$cip = isset($cips[0]) ? $cips[0] : 'unknown';
		unset($cips);
		return $cip;
	}
	
	
	public function object2array(&$object) {
		$object =  json_decode( json_encode( $object),true);
		return  $object;
	}
}
?>