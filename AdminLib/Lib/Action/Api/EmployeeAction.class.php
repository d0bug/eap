<?php
class EmployeeAction extends ApiCommAction {
	protected function getEmpList($userName='') {
		import('COM.Ldap.LdapUser');
		$user = LdapUser::getUser(C('LDAP_ANONY_USER'));
		$user->auth(C('LDAP_ANONY_PASS'));
		return $user->findUsers($userName);
	}
	
	protected function getDeptEmps($deptName) {
		import('COM.Ldap.LdapUser');
		$user = LdapUser::getUser(C('LDAP_ANONY_USER'));
		$user->auth(C('LDAP_ANONY_PASS'));
		$userRdn = C('LDAP_USERRDN');
		$baseDn = C('LDAP_BASEDN');
		$filter = '(&(objectClass=person)(department=*' . $deptName . '*))';
		return $user->findUsers($filter, true);
	}
}
?>