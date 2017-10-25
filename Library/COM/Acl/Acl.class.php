<?php
import('COM.Acl.AppGroup');
import('COM.Acl.Role');
import('COM.Dao.Dao');
import('ORG.Util.NCache');
import('COM.SysUtils');
import('COM.Auth.Permission');
class Acl {
    /**
     * 获取数据库访问对象
     * @return Dao
     */
    private static function getDao() {
        static $dao = null;
        if (null === $dao) {
            $dao = Dao::getDao('MSSQL_CONN');
        }
        return $dao;
    }


    /**
     * 获取用户ACL列表，返回值举例 
     * array('groupName'=>array('GROUP1-MODULE1-ACTION1'=>1, 
     *                          'GROUP1-MODULE2-ACTION2'=>3)), 
     * 值说明： 1:read,2:write
     * @staticvar array $aclArray
     * @param User $user
     * @param String $groupName
     * @return Array
     */
    public static function getUserAcl($user, $groupName = '') {
        $isSuper = abs(Role::isSuper($user));
        $cacheKey = 'ACL_' . md5(serialize($user->getInformation())) . '_' . $groupName . '_' . $isSuper;
        static $aclArray = array();
        #从静态变量读取
        $acl = $aclArray[$cacheKey];
        if (false == $acl) {
            $cache = NCache::getCache();
            #从缓存读取
            $acl = $cache->get('ACL', $cacheKey);
            if (false == $acl) {
                if ($groupName) {
                    #获取组内ACTION，若为超级管理员或组管理员，取得组内所有ACTION，否则取得授权ACTION
                    if ($isSuper || Role::isGroupAdmin($user, $groupName)) {
                        $acl = array($groupName=>self::getGroupAcls($groupName));
                    } else {
                        $acl = array($groupName=>self::getUGroupAcls($groupName, $user));
                    }
                    if (false == $acl[$groupName]) {
                        $acl = array();
                    }
                } else {
                    #首先取得用户所有有权限的组列表，后递归取得组内授权ACTION
                    $acl = array();
                    if ($isSuper) {
                        $groups = AppGroup::getAppGroups();
                    } else {
                        $groups = AppGroup::getUAppGroups($user);
                    }
                    foreach($groups as $groupName) {
                        $acl = array_merge($acl, self::getUserAcl($user, $groupName));
                    }
                    #设置缓存
                    if($acl) {
                        $cache->set('ACL', $cacheKey, $acl);
                    }
                }
            }
            #设置静态变量
            $aclArray[$cacheKey] = $acl;
        }
        if(Role::hasAdminGroup($user) && $groupName == 'System') {
        	$acl['System']['System-Role-main'] = 3;
        	$acl['System']['System-Role-userList'] = 3;
        }

        return $acl;
    }
    
    
    
    /**
     * 通用访问列表，不需授权的列表
     * @staticvar array $commAcl
     * @return type
     */
    private static function getCommAclArray() {
        static $commAcl = array();
        if (false == $commAcl) {
            $dao = self::getDao();
            $strQuery = 'SELECT group_name,acl_key
                         FROM sys_app_actions
                         WHERE app_name=' . $dao->quote(APP_NAME) . '
                           AND acl_value=0';
            $aclList = $dao->getAll($strQuery);
            foreach ($aclList as $acl) {
                $commAcl[$acl['group_name']][$acl['acl_key']] = PERM_READ | PERM_WRITE;
            }
        }
        return $commAcl;
    }
    
    /**
     * 角色授权访问列表
     * @param Array $roles
     * @return Array
     */
    public static function getRAclArray($roles) {
        $dao = self::getDao();
        $aclArray = array();
        if ($roles) {
            if (is_array($roles)) {
                $roleIdArray = array();
                foreach($roles as $roleId=>$roleCaption) {
                    $roleIdArray[] = $dao->quote($roleId);
                }
            } else {
                $roleIdArray[] = $dao->quote($roles);
            }
            
            $strQuery = 'SELECT DISTINCT group_name,module_name,action_name,racl.acl_key,racl.acl_value 
                         FROM sys_role_acls racl,sys_app_actions action
                         WHERE racl.app_name=' . $dao->quote(APP_NAME) . '
                           AND racl.app_name=action.app_name
                           AND racl.acl_key=action.acl_key 
                           AND role_id IN (' . implode(',', $roleIdArray) . ')';
            
            $aclList = $dao->getAll($strQuery);
            #根据应用组重组ACL列表
            foreach($aclList as $acl) {
                $aclArray[$acl['group_name']][$acl['acl_key']] = $acl['acl_value'];
            }
        }
        
        return $aclArray;
    }
    
    /**
     * 用户的特殊授权
     * @param User $user
     * @return Array
     */
    public static function getUAclArray($user) {
        $dao = self::getDao();
        $aclArray = array();
        $userKey = $user->getUserKey();
        $strQuery = 'SELECT group_name,module_name,action_name,uacl.acl_key,uacl.acl_value 
                     FROM sys_user_acls uacl, sys_app_actions acl
                     WHERE uacl.app_name=' . $dao->quote(APP_NAME) . '
                       and uacl.app_name=acl.app_name
                       AND uacl.acl_key=acl.acl_key 
                       AND user_key=' . $dao->quote($userKey) . '
                     ORDER BY acl.acl_key';
        $aclList = $dao->getAll($strQuery);
        #根据应用组重组ACL列表
        foreach($aclList as $acl) {
            $aclArray[$acl['group_name']][$acl['acl_key']] = $acl['acl_value'];
        }
        return $aclArray; 
    }
    
    /**
     * 根据用户获取访问列表
     * @param User $user
     * @return Array 根据应用组划分的访问列表
     */
    public static function getAclArray($user) {
        static $aclPools = array();
        $userKey = $user->getUserKey();
        $aclArray = $aclPools[$userKey];
        if(false == $aclArray) {
            $commAclArray = self::getCommAclArray();
            $roles = Role::getUserRoles($user);
            $roleAcls = self::getRAclArray($roles);
            $userAcls = self::getUAclArray($user);
            $aclArray = array_merge($commAclArray, $roleAcls);
            foreach($userAcls as $aclGroup=>$aclList) {
                foreach($aclList as $aclKey=>$aclValue) {
                    if($aclValue >0) {
                        $aclArray[$aclGroup][$aclKey] = $aclValue;
                    } else {
                        unset($aclArray[$aclGroup][$aclKey]);
                    }
                }
            }
            foreach($aclArrary as $aclGroup=>$aclList) {
                if(false == $aclList) {
                    unset($aclArray[$aclGroup]);
                }
            }
            $aclPools[$userKey] = $aclArrary;
        }
        return $aclArray;
    }

    
    /**
     * 取得组下所有模块的所有ACTION
     * @param type $groupName
     */
    public static function getGroupAcls($groupName) {
        $dao = self::getDao();
        $actionDir = LIB_PATH . '/Action';
        $groupDir = $actionDir . '/' . $groupName;
        $actionArray = array();
        if (is_dir($groupDir)) {
            $modules = glob($groupDir . '/*Action.class.php');
            foreach($modules as $moduleFile) {
                if (false == preg_match('/commAction/i', $moduleFile)) {
                    $actions = SysUtil::getModuleActions($groupName, $moduleFile);
                    foreach($actions as $action) {
                        $actionArray[$action] = PERM_READ | PERM_WRITE;
                    }
                }
            }
        }
        $strQuery = 'SELECT acl_key,acl_value FROM sys_app_actions 
                     WHERE app_name=' . $dao->quote(APP_NAME) . ' 
                       AND group_name=' . $dao->quote($groupName);
        $dbAclList = $dao->getAll($strQuery);
        foreach ($dbAclList as $acl) {
            $actionArray[$acl['acl_key']] = $acl['acl_value'];
        }
        return $actionArray;
    }
    
    #取得用户在该组下所有被授权的ACTION 通用  + 授权
    public static function getUGroupAcls($groupName, $user) {
        $aclArray = self::getAclArray($user);
        return (array)$aclArray[$groupName];
    }
    
    public static function updateCache() {
        $cache = NCache::getCache();
        $cache->delete('ACL');
    }
};
?>