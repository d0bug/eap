<?php
require_once(dirname(__FILE__) . '/Auth.const.php');
abstract class Auth {
    private static $authes = array();
    protected $authType = null;


    #单态模式获取认证实例
    public static function getAuth($authType = '') {
        if ($authType == '') {
            $authType = C('AUTH_BY');
        }
        if (false == $authType) $authType = AUTH_BY_ADLDAP;
        if (null  == self::$authes[$authType]) {
            switch($authType) {
                case AUTH_BY_OPENLDAP:
                case AUTH_BY_ADLDAP:
                    import('COM.Auth.LdapAuth');
                    self::$authes[$authType] = new LdapAuth();
                break;
                case AUTH_BY_UCENTER:
                    import('COM.Auth.UcAuth');
                    self::$authes[$authType] = new UcAuth();
                break;
                case AUTH_BY_OAUTH:
                    import('COM.Auth.OpenAuth');
                    self::$authes[$authType] = new OpenAuth();
                break;
                case AUTY_BY_REGISTRY:
                    import('COM.Auth.RegAuth');
                    self::$authes[$authType] = new RegAuth();
                break;
            }
            self::$authes[$authType]->authType = $authType;
        }
        
        return self::$authes[$authType];
    }
    
    #实例化认证对象，禁止外部调用
    protected function __construct() {}
    
    #取得当前对象认证方法
    public function getAuthType() {
        return $this->authType;
    }
    
    #抽象方法执行用户认证,必须被子类继承
    abstract public function auth($userName, $userPwd);
};
?>