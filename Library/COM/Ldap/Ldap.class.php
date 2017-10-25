<?php
require_once(dirname(__FILE__) . '/Ldap.const.php');
class Ldap {
    private $conn = null;
    private $ldapType = AD_LDAP;
    private $baseDN = '';
    private static $ldapPools = array();
    public $binded = false;
    
    /**
     * 静态方法获取LDAP类实例,默认使用配置文件中的ldap设置
     * @return Ldap
     */
    public static function getLdap($ldapCfg = array()) {
        $ldapKey = md5(serialize($ldapCfg));
        if (null  == self::$ldapPools[$ldapKey]) {
            $ldapHost = $ldapCfg['LDAP_HOST']   ? $ldapCfg['LDAP_HOST']   : C('LDAP_HOST');
            $baseDn   = $ldapCfg['LDAP_BASEDN'] ? $ldapCfg['LDAP_BASEDN'] : C('LDAP_BASEDN');
            $ldapPort = $ldapCfg['LDAP_PORT']   ? $ldapCfg['LDAP_PORT']   : C('LDAP_PORT');
            $ldapType = $ldapCfg['LDAP_TYPE']   ? $ldapCfg['LDAP_TYPE']   : C('LDAP_TYPE');
            $ldapPort = $ldapPort ? $ldapPort : 389;

            try{
                $ldap = Ldap::connect($ldapHost, $baseDn, $ldapPort);
                eval('$ldapType=' . $ldapType . ';');
                $ldap->setLdapType($ldapType);
                self::$ldapPools[$ldapKey] = $ldap;
            } catch (Exception $e){
                throw_exception($e->getMessage());
            }
        }
        return self::$ldapPools[$ldapKey];
    }
    
    /**
     * 构造函数私有化，禁止外部调用，开放connect静态方法给外部调用，连接时创建类实例
     */
    private function __construct($ldapHost, $baseDn, $ldapPort=389) {
        $ldapPort = $ldapPort ? $ldapPort : 389;
        $this->conn = ldap_connect($ldapHost, $ldapPort) or throw_exception('LDAP CONNECT FAILD');
        @ldap_set_option($this->conn, LDAP_OPT_PROTOCOL_VERSION, 3);
        @ldap_set_option($this->conn, LDAP_OPT_SIZELIMIT, 3000);
        $this->setBaseDn($baseDn);
    }
    
    /**
     * LDAP实例化方法
     */
    public static function connect($ldapHost, $baseDn, $ldapPort=389) {
        static $ldaps = array();
        $addr = $ldapHost . ':' . ($ldapPort ? $ldapPort :389);
        if (false == isset($addrs[$addr])) {
            $ldaps[$addr] = new Ldap($ldapHost, $baseDn, $ldapPort);
        }
        return $ldaps[$addr];
    }

    public function getConnection() {
        return $this->conn;
    }

    public function setLdapType($ldapType=AD_LDAP) {
        if (is_string($ldapType)) {
            $ldapType = $ldapType == 'AD_LDAP' ? AD_LDAP : 'OPEN_LDAP';
        }
        $this->ldapType = $ldapType;
    }

    public function getLdapType() {
        return $this->ldapType;
    }

    public function setBaseDN($baseDN) {
        $this->baseDN = $baseDN;
    }

    public function getBaseDN() {
        return $this->baseDN;
    }
    
    public function bindUser($userName, $userPwd) {
        import('COM.Ldap.LdapUser');
        $userDN = LdapUser::getUserDN($this, $userName);
        if (ldap_bind($this->conn, $userDN, $userPwd)) {
            $this->binded = true;
            return true;
        }
        $this->binded = false;
        return false;
    }
    
    public function bindLoginUser() {
        if(false == $this->binded) {
            $cookieName = C('USER_COOKIE_NAME');
            $loginUser = User::getLoginUser($cookieName);
            if(USER_TYPE_EMPLOYEE != $loginUser->getUserType()) {
                return false;
            }
            $loginUser->login($cookieName);
        }
    }
    
    public function firstEntry($entryArray) {
        $entryArray = array_values($entryArray);
        return $entryArray[0];
    }

    private function getEntryArray($resource) {
        $entries = ldap_get_entries($this->conn, $resource);
        unset($entries['count']);
        $entryArray = array();
        foreach($entries as $entry) {
            unset($entry['count']);
            foreach($entry as $attrName=>$attrArray) {
                if (is_numeric($attrName)) {
                    unset($entry[$attrName]);
                } else if ($attrName != 'dn'){
                    $entry[$attrName]=$attrArray[0];
                }
            }
            $entryArray[] = $entry;
        }
        return $entryArray;
    }

    public function searchSub($filter, $dn='', $fields=array()) {
        $dn = $dn ? $dn : $this->baseDN;
        if ($dn && false == preg_match('/' . $this->baseDN . '$/', $dn)) {
            $dn .= ',' . $this->baseDN;
        }
        $resource = ldap_search($this->conn, $dn, $filter, array_values($fields));
        if ($resource) {
            return $this->getEntryArray($resource);
        }
        
        return array();
    }

    public function searchOne($filter, $dn='', $fields=array()) {
        $dn = $dn ? $dn : $this->baseDN;
        $resource = ldap_list($this->conn, $dn, $filter, $fields);
    }

    public function searchBase($filter, $dn='', $fields=array()) {
        $dn = $dn ? $dn : $this->baseDN;
        $resource = ldap_read($this->conn, $dn, $filter, $fields);
    }
};
?>