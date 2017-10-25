<?php
class AdLdapUser extends LdapUser {
    protected $userFilter = '(&(objectClass=person)
                               (|
                                 (samaccountname={userName})
                                 (mail={userName}) 
                                )
                              )';
        
    
    protected function fieldMaps() {
        return array('user_name'=>'samaccountname', 'real_name'=>'cn', 'mail'=>'mail', 'dn'=>'dn');
    }
    
    protected function searchFilter($keyword) {
        return '(&    (objectClass=person)
                      (|
                          (samaccountname=' . $keyword . '*)
                          (cn=' . $keyword . '*)
                      )
                 )';
    }
};
?>