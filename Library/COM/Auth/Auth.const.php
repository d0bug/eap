<?php
    false == defined('OPEN_LDAP')           && define('OPEN_LDAP', 1);
    false == defined('AD_LDAP')             && define('AD_LDAP', 2);
    false == defined('AUTH_BY_OPENLDAP')    && define('AUTH_BY_OPENLDAP', 1);
    false == defined('AUTH_BY_ADLDAP')      && define('AUTH_BY_ADLDAP', 2);
    false == defined('AUTH_BY_UCENTER')     && define('AUTH_BY_UCENTER', 3);
    false == defined('AUTH_BY_OAUTH')       && define('AUTH_BY_OAUTH', 4);
    false == defined('AUTH_BY_REGISTRY')    && define('AUTH_BY_REGISTRY', 5);
?>