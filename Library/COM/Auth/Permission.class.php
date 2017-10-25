<?php
require_once(dirname(__FILE__) . '/Permission.const.php');
import('COM.Acl.Acl');
class Permission {
    /**
     * 读取当前访问ACTION的授权值
     * @param User $user
     * @return Array
     */
    public static function getPermInfo($user, $aclKey='') {
        if(false == $aclKey) {
            $aclKey = GROUP_NAME . '-' . MODULE_NAME . '-' . ACTION_NAME;
        }
        list($groupName, $moduleName, $actionName) = explode('-', $aclKey);
        if(Role::isGroupAdmin($user, $groupName)) {
        	return array('permValue'=>PERM_READ | PERM_WRITE);
        }
        $permissions = self::getPermissions($groupName);
        if (false == isset($permissions[$aclKey])) {
            return array('permValue'=>PERM_READ | PERM_WRITE);
        }
        
        $groupAcl = Acl::getUserAcl($user, $groupName);
        $groupActions = $groupAcl[$groupName];
        $permName = $permissions[$aclKey];
        $permValue = abs($groupActions[$aclKey]);
            
        return array('permValue'=>$permValue, 'permName'=>$permName);
    }
    
    /**
     * 从配置文件中读取组内所有方法的授权名称
     * @param String $groupName
     * @return array
     */
    public static function getPermissions($groupName) {
        static $permissions = array();
        if(false == isset($permissions[$groupName])) {
            $dao = Dao::getDao('MSSQL_CONN');
            $strQuery = 'SELECT * FROM sys_app_actions 
                         WHERE app_name=' . $dao->quote(APP_NAME) . '
                           AND group_name=' . $dao->quote($groupName) . '
                           AND acl_value >0';
            $permList = $dao->getAll($strQuery);
            $permArray = array();
            foreach ($permList as $acl) {
                $permArray[$acl['acl_key']] = $acl['acl_value'];
            }
            $permissions[$groupName] = $permArray;
        }
        
        return $permissions[$groupName];
    }

};
?>