<?php
import('COM.Ldap.Ldap');
class LdapAuth extends Auth {
    private $ldap = null;

    public function auth($userName, $userPass) {
        $ldap = Ldap::getLdap();
        $ldapType = AUTH_BY_ADLDAP == $this->authType ? AD_LDAP : OPEN_LDAP;
        $ldap->setLdapType($ldapType);
        return $ldap->bindUser($userName, $userPass);
    }
};
?>