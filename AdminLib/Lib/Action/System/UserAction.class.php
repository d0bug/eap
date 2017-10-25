<?php
import('COM.User.User');
import('COM.SysUtil');
class UserAction extends SystemCommAction {
    protected $autoCheckPerm = false;

    protected function notNeedLogin() {
        return array('SYSTEM-USER-LOGIN',
                     'SYSTEM-USER-LOSTPWD',
                     'SYSTEM-USER-RESETPWD',
                     'SYSTEM-USER-TEST');
    }

    /**
     * 系统首页，如果没有登录，则跳转到登陆页（跳转功能通过构造函数实现）
     */
    public function index() {
        import('COM.Acl.Acl');
        import('COM.Acl.Menu');
        $actions = Acl::getUserAcl($this->loginUser);
        $menus = Menu::getMenus($actions);
        $userInfo = $this->loginUser->getInformation();
        $userKey = $this->loginUser->getUserKey();
        $userInfo['real_name'] = D('Users')->get_userRealName_by_userKey($userKey);
        //当VIP账号禁用时暂时先去掉VIP模块和系统管理模块
        if(!D('Users')->get_userIsAvailable($userKey)){
        	unset($menus['System']);
        	unset($menus['Vip']);
        }
        //当该登录用户不属于VIP初级用户且不是因绑定用户获得的VIP模块权限时不显示VIP模块
        if(!systemCommAction::checkIsVipPrimaryUser($userKey) && !systemCommAction::checkIsBeBindTo($userKey)){
        	unset($menus['Vip']);
        }
        $logoutUrl = $this->getUrl('logout');
        $this->assign(get_defined_vars());
        $this->display();
    }

    /**
     * 用户登录功能
     */
    public function login() {
        import('COM.Image.Captcha');
        $sessName = 'verifyCode';
        $errors = array();
        if ($this->isPost()) {
            if (Captcha::checkCaptcha($sessName, $_POST['captcha'])) {
                $userName = SysUtil::getUserName($_POST['uName']);
                $userPass = SysUtil::getUserPass($_POST['uPass']);
                $userType = SysUtil::safeString($_POST['user_type']);
                if(false == $userType) {
                    $userType = USER_TYPE_EMPLOYEE;
                }
                $userInfo = array('user_name'=>$userName, 'user_pass'=>$userPass, 'user_type'=>$userType);
                $loginUser = User::getUser($userInfo);
                if (false == $loginUser->login($this->cookieName)) {
                    $errors[] = '登录失败，用户名或密码错误';
                } else {
                    $this->redirect(U('/System/User/index'));
                }
            } else {
                $errors[] = '登录失败，验证码错误';
            }
        }
        $tabPos = 0;
        if ($errors) {
            $tabPos = abs($_POST['tabPos']);
        }
        $userTypes = User::getAdminUserTypes();
        unset($userTypes[USER_TYPE_EMPLOYEE]);
        $captchaKey = Captcha::getCaptchaKey($sessName, 60, 20);
        $this->assign(get_defined_vars());
        $this->display();
    }


    public function logout() {
        $this->loginUser->logout($this->cookieName);
        $userInfo = $this->loginUser->getInformation();
        $this->redirect(U('/System/User/login'));
    }
};
?>
