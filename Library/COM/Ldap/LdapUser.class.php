<?php
import('COM.Ldap.Ldap');
abstract class LdapUser {
    protected $ldap = null;
    private static $users = array();
    protected $userName = '';
    protected $userRdn = '';

    public static function getUserDN($ldap, $userName) {
        #当为ADLDAP时并且用户名包含@或\时直接返回
        if (preg_match('/\\/', $userName)) {
            return $userName;
        } else {
            if(preg_match('/([^@]+)@.+/', $userName, $ar)) {
                $userName = $ar[1];
            }
            switch($ldap->getLdapType()){
                case AD_LDAP:
                    preg_match('/^dc=([^,]+)/i', $ldap->getBaseDN(), $ar);
                    return $ar[1] . '\\' . $userName;
                break;
                case OPEN_LDAP:
                    return 'UID=' . $userName . ',' . $ldap->getBaseDN();
                break;
            }
        }
    }

    protected function __construct($userName='') {
        $this->userName = $userName;
        $rdn = C('LDAP_USERRDN') ? C('LDAP_USERRDN') : '';
        $this->rdn = $rdn;
    }

    private function setLdap($ldap) {
        $this->ldap = $ldap;
    }
    
    public function getLdap() {
        return $this->ldap;
    }

    public function auth($userPass) {
        return $this->ldap->bindUser($this->userName, $userPass);
    }

    public static function getUser($userName) {
        if (false == isset(self::$users[$userName])) {
            $ldap = Ldap::getLdap();
            switch($ldap->getLdapType()) {
                case AD_LDAP:
                    import('COM.Ldap.LdapUser.AdLdapUser');
                    $user = new AdLdapUser($userName);
                    $user->setLdap($ldap);
                    self::$users[$userName] = $user;
                break;
                case OPEN_LDAP:
                    import('COM.Ldap.LdapUser.OpenLdapUser');
                    $user = new OpenLdapUser($userName);
                    $user->setLdap($ldap);
                    self::$users[$userName] = $user;
                break;
            }
        }
        
        return self::$users[$userName];
    }

    public function getUserInfo($userName = '') {
        $userName = $userName ? $userName : $this->userName;
        $filter = str_replace('{userName}', trim($userName), $this->userFilter);
        $infoFields = $this->infoFields();
        $userEntry = $this->ldap->firstEntry($this->ldap->searchSub($filter, $this->rdn, $infoFields));
        $userInfo = array();
        foreach($infoFields as $userAttr=>$entryAttr) {
            $userInfo[$userAttr] = $userEntry[$entryAttr];
        }
        return $userInfo;
    }

    abstract protected function fieldMaps();

    protected function infoFields() {
        $fields = array('user_name', 'real_name', 'mail', 'dn');
        $fieldMaps = $this->fieldMaps();
        if (false == $fieldMaps || false == is_array($fieldMaps)) {
            #throw new Exception('fieldMaps方法必须返回"user_name", "real_name","mail"为键值的关联数组');
            $fieldMaps = array();
        }
        $infoFields = array();
        foreach($fields as $field){
            if (false == $fieldMaps[$field]) {
                throw new Exception('fieldMaps方法必须返回"user_name", "real_name","mail"为键值的关联数组');
            }
        }
        return $fieldMaps;
    }
    
    abstract protected  function searchFilter($keyword);
    
    public function findUsers($keyword='', $isFilter=false) {
        $fields = $this->infoFields();
        $rdn = $this->rdn;
        if(false == $isFilter) {
        	$filter = $this->searchFilter($keyword);
        } else {
        	$filter = $keyword;
        }
        $searchResults = $this->getLdap()->searchSub($filter, $rdn, $fields);
        $userList = array();
        foreach ($searchResults as $user) {
            $item = array();
            foreach ($fields as $key=>$field) {
                $item[$key] = $user[$field];
            }
            $userList[$item['user_name']] = $item;
        }
        ksort($userList);
        return array_values($userList);
    }
};
?>