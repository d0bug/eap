<?php
import('COM.User.User');
import('COM.Dao.Dao');
class Role {
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
     * 判断是否超级管理员
     * @param User $user
     * @return boolean
     */
    public static function isSuper($user) {
        $userType = $user->getUserType();
        if (USER_TYPE_EMPLOYEE == $userType) {
            $userName = $user->getUserName();
            $superUsers = self::getSuperUsers();
            return in_array($userName, $superUsers);
        }
        
        return false;
    }
    
    public static function getSuperUsers() {
        $superUsers = C('SUPER_USERS');
        
        return $superUsers;
    }
    
    /**
     * 从数据库中获取组管理员列表
     * @param String $moduleName
     * @return Array Description
     */
    public static function getAdmins($groupName) {
        static $gAdmins = array();
        $key = 'ga_' . $groupName;
        $adminArray = $gAdmins[$key] ;
        if(false == $adminArray) {
            $dao = self::getDao();
            $strQuery = 'SELECT group_name,user_key FROM sys_app_admins
                         WHERE app_name=' . $dao->quote(APP_NAME) . '
                           AND group_name=' . $dao->quote($groupName);
            $adminList = $dao->getAll($strQuery);
            $adminArray = array();
            foreach($adminList as $admin) {
                $adminArray[$admin['group_name']][] = $admin['user_key'];
            }
            $gAdmins[$key] = $adminArray;
        }
        return $adminArray;
    }
    
    /**
     * 设置系统管理员，$moduleName为空时设置超级管理员
     * @param String $adminName
     * @param String $groupName
     * @return boolean
     */
    public static function setAdmin($adminName, $groupName='') {
        
    }

    /**
     * 判断是否为分组管理员
     * @param User $user
     * @param String $groupName
     */
    public static function isGroupAdmin($user, $groupName) {
    	if(Role::isSuper($user)) return true;
        if(USER_TYPE_EMPLOYEE == $user->getUserType()) {
            $userKey = $user->getUserKey();
            $groupAdmins = self::getAdmins($groupName);
            return in_array($userKey, $groupAdmins[$groupName]);
        }
        return false;
    }
    
    public static function hasAdminGroup($user) {
    	$dao = self::getDao();
    	$userKey = $user->getUserKey();
    	$strQuery = 'SELECT count(1) FROM sys_app_admins
    				 WHERE user_key=' . $dao->quote($userKey);
    	return $dao->getOne($strQuery) >0;
    }
    
    #
    public static function getRoleArray($groupName='') {
        
    }

    #
    public static function addRole($roleInfo) {
        
    }
    
    /**
     * 获取用户角色组数组
     * @param User $user
     * @return Array
     */
    public static function getUserRoles($user, $groupName='') {
        $dao = self::getDao();
        $userKey = $user->getUserKey();
        $userType = $user->getUserType();
        if($groupName) {
            $condition = ' AND group_name=' . $dao->quote($groupName);
        }
        $userKeys = array($dao->quote($userKey));
        $userTypes = array($dao->quote($userType));
        $strQuery = 'SELECT rel_user_key FROM sys_user_relations 
                     WHERE user_key=' . $dao->quote($userKey);
        $relList = $dao->getAll($strQuery);
        foreach ($relList as $relUser) {
            $uKey = $relUser['rel_user_key'];
            list($uType, $uName) = explode('-', $uKey);
            $userKeys[] = $dao->quote($uKey);
            $userTypes[] = $dao->quote($uType);
        }
        $strQuery = 'SELECT role_id,role_caption FROM sys_roles 
                     WHERE app_name ='. $dao->quote(APP_NAME) .'
                       AND is_removed=0 
                       ' . $condition . '
                       AND (role_id IN ( ' . implode(',', $userTypes) . ') OR  role_id IN (
                        SELECT DISTINCT role_id FROM sys_user_roles
                        WHERE app_name='. $dao->quote(APP_NAME) .' 
                          AND user_key IN (' . implode(',', $userKeys) . ')
                     ))';
        $roleList = $dao->getAll($strQuery);
        $roleArray = array();
        foreach($roleList as $role) {
            $roleArray[$role['role_id']] = $role['role_caption'];
        }
        return $roleArray;
    }
    
    /**
     * 获取应用组所有角色数组
     */
    public static function getAppGroupRoles($groupName='') {
        $dao = self::getDao();
        $strQuery = 'SELECT role.*,grp.group_caption FROM sys_roles role
                     LEFT JOIN sys_app_groups grp
                       ON grp.app_name=role.app_name
                        AND grp.group_name=role.group_name
                     WHERE role.app_name=' . $dao->quote(APP_NAME) . '
                       AND role.group_name=' . $dao->quote($groupName) . '
                       AND role.is_removed=0
                     ORDER BY role.group_name,role.update_at';
        $roleList = $dao->getAll($strQuery);
        $sysRoleCnt = 0;
        $dbSysRoles = array();
        foreach ($roleList as $role) {
        	if($role['is_system']) {
        	   $sysRoleCnt ++;
        	   $dbSysRoles[$role['role_id']] = true;
        	}
        }
        $sysRoles = array(USER_TYPE_EMPLOYEE=>'内部员工', 
                          USER_TYPE_TEACHER=>'兼职教师(大班)', 
                          USER_TYPE_VTEACHER=>'兼职教师(VIP)', 
                          USER_TYPE_SCHOOL=>'学校用户');
        if(strtoupper($groupName) == 'SYSTEM' && $sysRoleCnt != sizeof($sysRoles)) {
            $time = date('Y-m-d H:i:s');
            $userName = 'System';
            foreach ($sysRoles as $roleId=>$roleCaption) {
                if(false == $dbSysRoles[$roleId]) {
                    $strQuery = 'INSERT INTO sys_roles 
                                 (role_id,app_name,role_caption,group_name,role_desc,is_system,update_user,update_at)
                                 VALUES 
                                 (' . $dao->quote($roleId) . ',
                                  ' . $dao->quote(APP_NAME) . ',
                                  ' . $dao->quote($roleCaption) . ',
                                  ' . $dao->quote($groupName) . ',
                                  ' . $dao->quote($roleCaption) . ',1,
                                  ' . $dao->quote($userName) . ',
                                  ' . $dao->quote($time) . ')';
                    $dao->execute($strQuery);
                }
            }
            return self::getAppGroupRoles($groupName);
        }
        return $roleList;
    }
    
    public function getInfo($roleId) {
        $dao = self::getDao();
        $strQuery = 'SELECT * FROM sys_roles
                     WHERE app_name=' . $dao->quote(APP_NAME) . '
                       AND role_id=' . $dao->quote($roleId);
        return $dao->getRow($strQuery);
    }
    
    public function saveRole($roleInfo) {
        $dao = self::getDao();
        $groupName = SysUtil::safeString($roleInfo['group_name']);
        $roleCaption = SysUtil::safeString($roleInfo['role_caption']);
        $roleDesc = SysUtil::safeString($roleInfo['role_desc']);
        $userKey = $roleInfo['user']->getUserKey();
        $time = date('Y-m-d H:i:s');
        if($roleInfo['role_id']) {
            $strQuery = 'UPDATE sys_roles 
                         SET role_caption=' . $dao->quote($roleCaption) . ',
                            role_desc=' . $dao->quote($roleDesc) . ',
                          update_user=' . $dao->quote($userKey) . ',
                          update_at=' . $dao->quote($time) . '
                         WHERE app_name=' . $dao->quote(APP_NAME) . '
                           AND role_id=' . $dao->quote($roleInfo['role_id']);
        } else {
            $strQuery = 'INSERT INTO sys_roles
                         (app_name,group_name,role_caption, role_desc,update_at,update_user)
                         VALUES (' . $dao->quote(APP_NAME) . ',
                         ' . $dao->quote($groupName) . ',
                         ' . $dao->quote($roleCaption) . ',
                         ' . $dao->quote($roleDesc) . ',
                         ' . $dao->quote($time) . ',
                         ' . $dao->quote($userKey) . ')';
        }
        
        if($dao->execute($strQuery)) {
            return array('success'=>true);
        }
        return array('errorMsg'=>'角色信息保存失败');
    }
    
    public function deleteRole($roleId) {
        $dao = self::getDao();
        $strQuery = 'UPDATE sys_roles 
                     set is_removed=' . time() . '
                     WHERE app_name=' . $dao->quote(APP_NAME) . '
                       AND role_id=' . $dao->quote($roleId) . '
                       AND is_system=0';
        $delResult = $dao->execute($strQuery);
        if($dao->affectRows() == 0) {
            return false;
        }
        if($delResult) {
            import('COM.Acl.Acl');
            import('COM.Acl.Menu');
            Acl::updateCache();
            Menu::updateCache();
        }
        return $delResult;
    }
    
    public function saveRoleAcl($roleId, $aclArray, $user) {
        $dao = self::getDao();
        $roleId = SysUtil::uuid($roleId);
        $delAcls = array();
        $userKey = $user->getUserKey();
        $time = date('Y-m-d H:i:s');
        foreach ($aclArray as $aclKey=>$aclValue) {
            if($aclValue == 0) {
                $delAcls[] = $dao->quote($aclKey);
            }
        }
        if($delAcls) {
            $strQuery = 'DELETE FROM sys_role_acls 
                         WHERE app_name=' . $dao->quote(APP_NAME) . '
                           AND role_id=' . $dao->quote($roleId) . '
                           AND acl_key IN (' . implode(',', $delAcls) . ')';
            $dao->execute($strQuery);
        }
        $strQuery = 'SELECT * FROM sys_role_acls 
                     WHERE app_name=' . $dao->quote(APP_NAME) . '
                       AND role_id=' . $dao->quote($roleId);
        $dbAclList = $dao->getAll($strQuery);
        $dbAclArray = array();
        foreach ($dbAclList as $acl) {
            $dbAclArray[$acl['acl_key']] = $acl['acl_value'];
        }
        foreach ($aclArray as $aclKey=>$aclValue) {
            if($aclValue) {
                if(isset($dbAclArray[$aclKey]) && $dbAclArray[$aclKey] != $aclValue) {
                    $strQuery = 'UPDATE sys_role_acls
                                 SET acl_value=' . abs($aclValue) . ',
                                   update_user=' . $dao->quote($userKey) . ',
                                   update_at=' . $dao->quote($time) . '
                                 WHERE app_name=' . $dao->quote(APP_NAME) . '
                                   AND role_id=' . $dao->quote($roleId) . '
                                   AND acl_key=' . $dao->quote($aclKey);
                } else if (false == isset($dbAclArray[$aclKey])) {
                    $strQuery = 'INSERT INTO sys_role_acls
                                 (app_name,role_id,acl_key, acl_value, update_user,update_at)
                                 VALUES (
                                    ' . $dao->quote(APP_NAME) . ',
                                    ' . $dao->quote($roleId) . ',
                                    ' . $dao->quote($aclKey) . ',
                                    ' . abs($aclValue) . ',
                                    ' . $dao->quote($userKey) . ',
                                    ' . $dao->quote($time) . '
                                 )';
                }
                
                $dao->execute($strQuery);
            }
        }
        import('COM.Acl.Acl');
        import('COM.Acl.Menu');
        Acl::updateCache();
        Menu::updateCache();
        return true;
    }
    
    public static function saveUserAcl() {
        
    }
    
    public static function getUsers($groupName, $roleId, $keyword='', $currentPage = 1, $pageSize=20) {
        $dao = self::getDao();
        $placeHolder = '%_placeholder_';
        $strQuery = 'SELECT ' . $placeHolder . ' 
                     FROM sys_users u
                     LEFT JOIN sys_user_roles ur
                        ON ur.user_key=u.user_key 
                     WHERE 1=1';
        if ($keyword) {
            $strQuery .= ' AND (u.user_realname LIKE ' . $dao->quote('%' . $keyword . '%') . ' 
                             OR u.user_name LIKE ' . $dao->quote('%' . $keyword . '%') . ')';
        }
        if($roleId) {
            $strQuery .= ' AND (ur.role_id=' . $dao->quote($roleId) . '
                                OR u.user_key LIKE ' . $dao->quote($roleId .  '%') . ')';
        } else {
            $strQuery .= ' AND ur.role_id IN (
                 SELECT role_id FROM sys_roles 
                 WHERE app_name=' . $dao->quote(APP_NAME) . '
                   AND group_name=' . $dao->quote($groupName) . '
               )';
        }
        $countQuery = str_replace($placeHolder, 'count(1)', $strQuery);
        $recordCount = $dao->getOne($countQuery);
        $pageCount = ceil($recordCount / $pageSize);
        if($currentPage > $pageCount) $currentPage = $pageCount;
        if($currentPage < 1) $currentPage = 1;
        $order = ' ORDER BY create_at DESC';
        $strQuery .= $order;        
        $strQuery = str_replace($placeHolder, 'u.*,ur.create_user creator,' . $dao->quote($roleId) . ' user_role ', $strQuery);
        $userList = $dao->getLimit($strQuery, $currentPage, $pageSize, $order);
        return array($recordCount, $userList);
    }
    
    public static function getSysRoles($userType) {
        
    }
    
    public static function addUser($roleId, $users, $operator) {
        $dao = self::getDao();
        $dbUsers = array();
        $dbRoleUsers = array();
        $time = date('Y-m-d H:i:s');
        $operator = $operator->getUserKey();
        if($users) {
            $ukList = array($dao->quote(time()));
            $uInfoArray = array();
            foreach ($users as $userInfo) {
                list($userKey,$userName, $realName, $email) = explode("\t", $userInfo);
                list($userType, $uName) = explode('-', $userKey);
                $uTypes = array('is_employee'=>0, 'is_teacher'=>0, 'is_school'=>0);
                if(USER_TYPE_EMPLOYEE == $userType) {
                    $uTypes['is_employee'] = 1;
                }
                if(USER_TYPE_TEACHER == $userType) {
                    $uTypes['is_teacher'] = 1;
                }
                if(USER_TYPE_SCHOOL == $userType) {
                    $uTypes['is_school'] = 1;
                }
                $userInfo = array('user_type'=>$userType, 'user_key'=>$userKey, 'user_name'=>$userName, 'real_name'=>$realName, 'email'=>$email);
                $userInfo = array_merge($userInfo, $uTypes);
                $uInfoArray[$userKey] = $userInfo;
                $ukList[] = $dao->quote($userKey);
            }
            
            $strQuery = 'SELECT user_key FROM sys_users 
                         WHERE user_key IN (' . implode(',', $ukList) . ')';
            $dbUserList = $dao->getAll($strQuery);
            foreach ($dbUserList as $user) {
                $dbUsers[$user['user_key']] = true;
            }
            $strQuery = 'SELECT user_key FROM sys_user_roles
                         WHERE app_name=' . $dao->quote(APP_NAME) . '
                           AND role_id=' . $dao->quote($roleId);
            $dbRUserList = $dao->getAll($strQuery);
            foreach ($dbRUserList as $user) {
            	$dbRoleUsers[$user['user_key']] = true;
            }
            
            foreach ($uInfoArray as $userKey=>$user) {
            	if(false == $dbUsers[$userKey]) {
            	   $strQuery = 'INSERT INTO sys_users 
            	               (user_key,user_name,user_realname,is_employee,is_teacher,is_school,user_email,create_user,create_at)
            	               VALUES 
            	               (' . $dao->quote($userKey) . ',
            	                ' . $dao->quote($user['user_name']) . ',
            	                ' . $dao->quote($user['real_name']) . ',
            	                ' . abs($user['is_employee']) . ',
            	                ' . abs($user['is_teacher']) . ',
            	                ' . abs($user['is_school']) . ',
            	                ' . $dao->quote($user['email']) . ',
            	                ' . $dao->quote($operator) . ',
            	                ' . $dao->quote($time) . ')';
            	   $dao->execute($strQuery);
            	}
            	if(false == $dbRoleUsers[$userKey]) {
            	   $strQuery = 'INSERT INTO sys_user_roles 
            	                (app_name,user_key,role_id,create_user,create_at)
            	                VALUES 
            	                (' . $dao->quote(APP_NAME) . ',
            	                 ' . $dao->quote($userKey) . ',
            	                 ' . $dao->quote($roleId) . ',
            	                 ' . $dao->quote($operator) . ',
            	                 ' . $dao->quote($time) . ')';
            	   $dao->execute($strQuery);
            	}
            }
        }
        return true;
    }
    
    public static function deleteUser($roleId, $userKey) {
        $dao = self::getDao();
        $strQuery = 'DELETE FROM sys_user_roles
                     WHERE app_name=' . $dao->quote(APP_NAME) . '
                       AND user_key=' . $dao->quote($userKey) . '
                       AND role_id=' . $dao->quote($roleId);
        $dao->execute($strQuery);
        return true;
    }
};
?>