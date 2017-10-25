<?php
import('COM.Acl.Acl');
import('COM.Acl.Role');
import('COM.Acl.Menu');
import('COM.SysUtil');
class AppGroup {
    /**
     * 取得数据库访问对象
     * @return Dao
     */
    private static function getDao() {
        static $dao = null;
        if(null == $dao) {
            $dao = Dao::getDao('MSSQL_CONN');
        }
        return $dao;
    }
    
    /**
     * 取得系统所有组名数组
     * @staticvar array $groupArray
     * @return Array
     */
    public static function getAppGroups() {
        static $groupArray = array();
        $groups = explode(',', C('APP_GROUP_LIST'));
        $exceptGroups = C('EXCEPT_GROUPS');
        if (false == $exceptGroups) {
            $exceptGroups = array();
        }
        $groupArray = array();
        foreach($groups as $group) {
            $group = ucfirst(trim($group));
            if (false == in_array($group, $exceptGroups)) {
                $groupArray[] = $group;
            }
        }
        
        return $groupArray;
    }

    /**
     * 取得用户所有应用组的名称数组，通用 + 授权
     * @param type $user
     */
    public static function getUAppGroups($user) {
        $uAclArray = Acl::getAclArray($user);
        $uAppGroups = array();
        
        #组名根据配置文件中的顺序进行排序
        $appGroups = self::getAppGroups();
        foreach($appGroups as $groupName) {
            if(Role::isGroupAdmin($user, $groupName)){
            	$uAppGroups[] = $groupName;
            } else if($uAclArray[$groupName]) {
                $uAppGroups[] = $groupName;
            }
        }
        return $uAppGroups;
    }
    /**
     * 从获取所有应用组列表，包含应用组名称设置
     * @return array
     */
    public static function getGroupList($user, $forManage=false) {
        $dao = self::getDao();
        $userAcls = Acl::getUserAcl($user);
        #$apps = self::getAppGroups();
        $apps  = array_keys($userAcls);
        $appNames = array();
        $appList = array();
        foreach ($apps as $app) {
        	if(false == $forManage || Role::isGroupAdmin($user, $app)) {
	            $appNames[] = $dao->quote($app);
	            $appList[$app] = array('group_name'=>$app);
        	}
        }
        
        if(Role::isSuper($user)) {
            #清除无效应用组
            $strQuery = 'DELETE FROM sys_app_groups WHERE group_name NOT IN (' . implode(',', $appNames) . ')';
            #$dao->execute($strQuery);
            Acl::updateCache();
            Menu::updateCache();
        }
        
        $strQuery = 'SELECT * FROM sys_app_groups 
                     WHERE app_name=' . $dao->quote(APP_NAME) . '
                       AND group_name IN (' . implode(',', $appNames) . ')';
        $dbAppList = $dao->getAll($strQuery);
        foreach($dbAppList as $app) {
            $appList[$app['group_name']] = $app;
        }
        return $appList;
    }
    
    /** 
     * 从数据库取得组信息
     * @param type $groupName
     * @return type
     */
    public static function getGroupInfo($groupName) {
        $dao = self::getDao();
        $strQuery = 'SELECT * FROM sys_app_groups 
                     WHERE app_name=' . $dao->quote(APP_NAME) . '
                       AND group_name=' . $dao->quote($groupName);
        $groupInfo = $dao->getRow($strQuery);
        if(false == $groupInfo) {
            $groupInfo['group_name'] = $groupName;
        }
        return $groupInfo;
    }
    
    
    /**
     * 取得所有组模块数组并排序，从文件系统读取，并从数据库更新模块信息
     * @return Array
     */
    public static function getModuleTree($user) {
        $dao = self::getDao();
        $groups = self::getGroupList($user,true);
        $groupModules  = array();
        $groupArray = array();
        foreach($groups as $group) {
            $groupName = ucfirst($group['group_name']);
            $groupArray[$groupName] = $group;
            $modules = self::getGroupModules($group);
            if($modules) {
                $groupModules[$groupName] = $modules;
            }
        }
        
        $moduleKeys = array($dao->quote(time()));
        foreach($groupModules as $group=>$modules){
            foreach($modules as $module) {
                $moduleKeys[] = $dao->quote($module['module_key']);
            }
        }
        #清除无效模块
        $strQuery = 'DELETE FROM sys_app_modules 
                     WHERE app_name=' . $dao->quote(APP_NAME) . ' 
                       AND module_key NOT IN (' . implode(',', $moduleKeys) . ')';
        #$dao->execute($strQuery);
        Acl::updateCache();
        Menu::updateCache();
        $strQuery = 'SELECT * FROM sys_app_modules 
                     WHERE app_name=' . $dao->quote(APP_NAME) . ' 
                     ORDER BY group_name,module_seq';
        
        $dbModules = $dao->getAll($strQuery);
        
        $dbSortModules = array();
        #从文件模块列表中排除数据库中存在的模块项
        foreach($dbModules as $module) {
            $module['_parentId'] = $module['group_name'];
            unset($groupModules[$module['group_name']][$module['module_key']]);
            $dbSortModules[$module['group_name']][$module['module_key']] = $module;
        }
        
        $moduleTree  = array();
        #合并文件中模块列表到数据库模块列表完成排序
        foreach($groupModules as $groupName=>$modules) {
            $group = $groupArray[$groupName];
            $groupCaption = $group['group_caption'] ? $group['group_caption'] . '(' . $groupName . ')' : $groupName;
            #添加模块树中应用组信息
            $modules = array(array('module_key'=>$groupName, 'module_name'=>$groupCaption));
            if($dbSortModules[$groupName]) $modules = array_merge($modules, $dbSortModules[$groupName]);
            if($groupModules[$groupName]) $modules = array_merge($modules, $groupModules[$groupName]);
            $moduleTree = array_merge($moduleTree, array_values($modules));
        }
        
        return $moduleTree;
    }
    
    /**
     * 取得指定组的模块列表
     * @param Array $group
     * @return Array
     */
    public static function getGroupModules($group) {
        $actionDir = LIB_PATH . '/Action';
        $groupName = ucfirst($group['group_name']);
        $groupCaption = $group['group_caption'] ? $group['group_caption'] . '(' . $groupName . ')' : $groupName;
        $groupDir = $actionDir . '/' . $groupName;
        $modules = glob($groupDir . '/*Action.class.php');
        $moduleList = array();
        foreach($modules as $module) {
            $moduleName = basename($module);
            $moduleName = str_replace('Action.class.php', '', $moduleName);
            if(false == preg_match('/Comm$/i', $moduleName)) {
                $moduleKey = $groupName . '-' . $moduleName;
                $moduleList[$moduleKey] = array('app_name'=>APP_NAME, 'group_name'=>$groupName, 'group_caption'=>$groupCaption, 'module_name'=>$moduleName, 'module_key'=>$moduleKey, '_parentId'=>$groupName);
            }
        }
        return $moduleList;
    }
    
    /**
     * 获取模块信息
     *
     * @param String $moduleKey
     * @return array
     */
    public static function getModuleInfo($moduleKey) {
        $dao = self::getDao();
        $strQuery = 'SELECT * FROM sys_app_modules 
                     WHERE app_name=' . $dao->quote(APP_NAME) . ' 
                       AND module_key=' . $dao->quote($moduleKey);
        $moduleInfo = $dao->getRow($strQuery);
        if(false == $moduleInfo) {
            list($groupName, $moduleName) = explode('-', $moduleKey);
            $strQuery = 'SELECT MAX(module_seq) AS seq 
                         FROM sys_app_modules
                         WHERE app_name=' . $dao->quote(APP_NAME) . ' 
                           AND group_name=' . $dao->quote($groupName);
            
            $nextSeq = abs($dao->getOne($strQuery)) + 1;
            $moduleInfo = array('app_name'=>APP_NAME, 'group_name'=>$groupName, 'module_name'=>$moduleName, 'module_key'=>$moduleKey, 'module_seq'=>$nextSeq);
        }
        return $moduleInfo;
    }
    
    /**
     * 根据模块获取功能列表
     *
     * @param String $moduleKey  模块名称
     * @return Array
     */
    public static function getModuleActions($moduleKey) {
        $dao = self::getDao();
        list($groupName, $moduleName) = explode('-', $moduleKey);
        $moduleFile = LIB_PATH . '/Action/' . $groupName . '/' . ucfirst($moduleName) . 'Action.class.php';
        $actions = SysUtil::getModuleActions($groupName, $moduleFile);
        $aclArray = array($dao->quote(time()));
        $sortedActions = array();
        foreach($actions as $action) {
            $aclArray[] = $dao->quote($action);
            $sortedActions[$action] = array('acl_key'=>$action);
        }
        $condition = 'app_name=' . $dao->quote(APP_NAME) . '
                       AND group_name=' . $dao->quote($groupName) . '
                       AND module_name=' . $dao->quote($moduleName);
        $strQuery = 'DELETE FROM sys_app_actions 
                     WHERE ' . $condition . '
                       AND menu_url =' . $dao->quote('') . '
                       AND acl_key NOT IN (' . implode(',', $aclArray) . ')';
        #$dao->execute($strQuery);
        if($dao->affectRows() > 0) {
            Acl::updateCache();
            Menu::updateCache();
        }
        
        $strQuery = 'SELECT * FROM sys_app_actions 
                     WHERE ' . $condition . '
                     ORDER BY is_menu DESC,menu_seq,action_name';
        
        
        $actionList = $dao->getAll($strQuery);
        
        $dbSortActions = array();
        foreach($actionList as $action) {
            unset($sortedActions[$action['acl_key']]);
            $dbSortActions[$action['acl_key']] = $action;
        }
        
        return array_values(array_merge($dbSortActions, $sortedActions));
    }
    
    /**
     * 获取功能信息
     *
     * @param String $aclKey
     * @return array
     */
    public static function getActionInfo($aclKey) {
        $aclKey = SysUtil::safeString($aclKey);
        $dao = self::getDao();
        $strQuery = 'SELECT * FROM sys_app_actions 
                     WHERE app_name=' . $dao->quote(APP_NAME) . '
                       AND acl_key=' . $dao->quote($aclKey);
        $actionInfo = $dao->getRow($strQuery);
        if(false == $actionInfo) {
            $actionInfo = array('acl_key'=>$aclKey);
        }
        return $actionInfo;
    }
    
    /**
     * 保存应用组信息
     * @param array $groupInfo
     * @return array
     */
    public static function saveGroup($groupInfo) {
        import('COM.SysUtil');
        $dao = self::getDao();
        $groupName = $groupInfo['group_name'];
        $appGroups = self::getAppGroups();
        if(false == $groupName || false == in_array($groupName, $appGroups)) {
            return array('errorMsg'=>'非法提交');
        }
        $strQuery = 'SELECT * FROM sys_app_groups 
                     WHERE app_name=' . $dao->quote(APP_NAME) . ' 
                       AND group_name=' . $dao->quote($groupName);
        $dbGroupInfo = $dao->getRow($strQuery);
        $groupCaption = SysUtil::safeString($groupInfo['group_caption']);
        $groupDesc = SysUtil::safeString($groupInfo['group_desc']);
        $userKey = $groupInfo['user']->getUserKey();
        $groupIcon = '';
        if($groupInfo['group_icon']['name']) {
            $iconConfig = C('ICON_CONFIG');
            import('ORG.Net.UploadFile');
            $uploader = new UploadFile($iconConfig);
            $uploader->subDir = 'group';
            $uploader->thumbPath = $iconConfig['savePath'] . '/group/';
            $uploader->thumbFile = $groupName;
            $saveResult = $uploader->uploadOne($groupInfo['group_icon']);
            if(false == $saveResult) {
                return array('errorMsg'=>$uploader->getErrorMsg());
            }
            $groupIcon = preg_replace('/\/+/', '/', $iconConfig['url_prefix'] . '/group/' . $groupName . '.gif');
        }
        if($dbGroupInfo) {
            if(false == $groupIcon) $groupIcon = $dbGroupInfo['group_icon'];
            $strQuery = 'UPDATE sys_app_groups 
                         SET group_caption=' . $dao->quote($groupCaption) . ',
                             group_icon=' . $dao->quote($groupIcon) . ',
                             group_desc=' . $dao->quote($groupDesc) . ',
                             update_at=' . $dao->quote(date('Y-m-d H:i:s')) . ',
                             update_user=' . $dao->quote($userKey) . '
                         WHERE app_name=' . $dao->quote(APP_NAME) . '
                           AND group_name=' . $dao->quote($groupName);
        } else {
            $strQuery = 'INSERT sys_app_groups 
                         (app_name,group_name,group_caption,group_icon,group_desc,update_at,update_user)
                         VALUES
                         (' . $dao->quote(APP_NAME) . ','
                            . $dao->quote($groupName) .','
                            . $dao->quote($groupCaption) .','
                            . $dao->quote($groupIcon) .','
                            . $dao->quote($groupDesc) .','
                            . $dao->quote(date('Y-m-d H:i:s')) . ','
                            . $dao->quote($userKey) .')';
        }
        
        if(false == $dao->execute($strQuery)) {
            return array('errorMsg'=>'应用组信息修改失败');
        }
        Acl::updateCache();
        Menu::updateCache();
        return array('success'=>true);
    }
    
    /**
     * 保存模块信息
     *
     * @param array $moduleInfo
     * @return array
     */
    public static function saveModule($moduleInfo) {
        import('COM.SysUtil');
        $dao = self::getDao();
        $moduleKey = $moduleInfo['module_key'];
        $strQuery = 'SELECT * FROM sys_app_modules 
                     WHERE app_name=' . $dao->quote(APP_NAME) . '
                       AND module_key=' . $dao->quote($moduleKey);
        $dbModuleInfo = $dao->getRow($strQuery);
        $moduleCaption = SysUtil::safeString($moduleInfo['module_caption']);
        $moduleSeq = abs($moduleInfo['module_seq']);
        $userKey = $moduleInfo['user']->getUserKey();
        $time = date('Y-m-d H:i:s');
        $moduleIcon = '';
        if($moduleInfo['module_icon']['name']) {
        	import('ORG.Net.UploadFile');
        	$iconConfig = C('ICON_CONFIG');
        	$uploader = new UploadFile($iconConfig);
        	$uploader->subDir = 'module';
        	$uploader->thumbPath = $iconConfig['savePath'] . '/module/';
            $uploader->thumbFile = $moduleKey;
        	$saveResult = $uploader->uploadOne($moduleInfo['module_icon']);
        	if(false == $saveResult) {
        	   return array('errorMsg'=>$uploader->getErrorMsg());
        	}
        	$moduleIcon = preg_replace('/\/+/', '/', $iconConfig['url_prefix'] . '/module/' . $moduleKey . '.gif');
        }
        if($dbModuleInfo) {
            if(false == $moduleIcon) {
                $moduleIcon = $dbModuleInfo['module_icon'];
            }
            $strQuery = 'UPDATE sys_app_modules 
                         SET module_caption=' . $dao->quote($moduleCaption) . ',
                             module_icon=' . $dao->quote($moduleIcon) . ',
                             module_seq=' . abs($moduleSeq) . ',
                             update_user=' . $dao->quote($userKey) . ',
                             update_at=' . $dao->quote($time) . '
                         WHERE app_name=' . $dao->quote(APP_NAME) . '
                           AND module_key=' . $dao->quote($moduleKey);
        } else {
            list($groupName, $moduleName) = explode('-', $moduleKey);
            $strQuery = 'INSERT INTO sys_app_modules 
                        (app_name,group_name,module_name,module_key,module_caption,
                         module_icon,module_seq,update_user,update_at)  
                         VALUES (' . $dao->quote(APP_NAME) . ',
                                 ' . $dao->quote($groupName) . ',
                                 ' . $dao->quote($moduleName) . ',
                                 ' . $dao->quote($moduleKey) . ',
                                 ' . $dao->quote($moduleCaption) . ',
                                 ' . $dao->quote($moduleIcon) . ',
                                 ' . abs($moduleSeq) . ',
                                 ' . $dao->quote($userKey) . ',
                                 ' . $dao->quote($time) . ')';
        }
        if(false == $dao->execute($strQuery)) {
            return array('errorMsg'=>'模块信息修改失败');
        }
        Acl::updateCache();
        Menu::updateCache();
        return array('success'=>true);
    }
    
    /**
     * 保存功能信息
     *
     * @param array $actionInfo
     * @return array
     */
    public static function saveAction($actionInfo) {
        import('COM.SysUtil');
        $dao = self::getDao();
        $aclKey = $actionInfo['acl_key'];
        list($groupName, $moduleName, $actionName) = explode('-', $aclKey);
        $isAdd = $actionInfo['addAction'];
        $strQuery = 'SELECT * FROM sys_app_actions 
                     WHERE app_name=' . $dao->quote(APP_NAME) . '
                       AND acl_key=' . $dao->quote($aclKey);
        $dbAclInfo = $dao->getRow($strQuery);
        if($dbAclInfo && $isAdd) {
            return  array('errorMsg'=>'功能标识冲突,请更换');
        }
        
        $aclCaption = SysUtil::safeString($actionInfo['acl_caption']);
        $actionDesc = SysUtil::safeString($actionInfo['action_desc']);
        $userKey = $actionInfo['user']->getUserKey();
        $time = date('Y-m-d H:i:s');
        $isMenu = abs($actionInfo['is_menu']);
        $menuUrl = SysUtil::safeString($actionInfo['menu_url']);
        $menuSeq = $isMenu ? abs($dbAclInfo['menu_seq']) : 999;
        $aclValue = abs($actionInfo['acl_value']);
        if($menuSeq == 0) {
            $strQuery = 'SELECT max(menu_seq) FROM sys_app_actions 
                         WHERE app_name=' . $dao->quote(APP_NAME) . '
                           AND is_menu=1 
                           AND group_name=' . $dao->quote($groupName) . '
                           AND module_name=' . $dao->quote($moduleName);
            $menuSeq = abs($dao->getOne($strQuery)) + 1;
        }
        
        $aclIcon = '';
        if($actionInfo['acl_icon']['name']) {
            import('ORG.Net.UploadFile');
            $iconConfig = C('ICON_CONFIG');
        	$uploader = new UploadFile($iconConfig);
        	$uploader->subDir = 'action';
        	$uploader->thumbPath = $iconConfig['savePath'] . '/action/';
            $uploader->thumbFile = $aclKey;
        	$saveResult = $uploader->uploadOne($actionInfo['acl_icon']);
        	if(false == $saveResult) {
        	   return array('errorMsg'=>$uploader->getErrorMsg());
        	}
        	$aclIcon = preg_replace('/\/+/', '/', $iconConfig['url_prefix'] . '/action/' . $aclKey . '.gif');
        }
        if($dbAclInfo) {
            if(false == $aclIcon && $isMenu) {
                $aclIcon = $dbAclInfo['acl_icon'];
            }
            $strQuery = 'UPDATE sys_app_actions
                         SET acl_caption=' . $dao->quote($aclCaption) . ',
                             acl_icon=' . $dao->quote($aclIcon) . ',
                             acl_value=' . abs($aclValue) . ',
                             is_menu=' . abs($isMenu) . ',
                             menu_seq=' . abs($menuSeq) . ',
                             menu_url=' . $dao->quote($menuUrl) . ',
                             action_desc=' . $dao->quote($actionDesc) . ',
                             update_user=' . $dao->quote($userKey) . ',
                             update_at=' . $dao->quote($time) . '
                         WHERE app_name=' . $dao->quote(APP_NAME) . '
                           AND acl_key=' . $dao->quote($aclKey);
        } else {
            $strQuery = 'INSERT INTO sys_app_actions
                        (app_name,group_name,module_name,action_name,acl_key,acl_caption,acl_value,
                        acl_icon,is_menu,menu_seq,menu_url,action_desc,update_user,update_at)  
                         VALUES (' . $dao->quote(APP_NAME) . ',
                                 ' . $dao->quote($groupName) . ',
                                 ' . $dao->quote($moduleName) . ',
                                 ' . $dao->quote($actionName) . ',
                                 ' . $dao->quote($aclKey) . ',
                                 ' . $dao->quote($aclCaption) . ',
                                 ' . $aclValue . ',
                                 ' . $dao->quote($aclIcon) . ',
                                 ' . abs($isMenu) . ',
                                 ' . abs($menuSeq) . ',
                                 ' . $dao->quote($menuUrl) . ',
                                 ' . $dao->quote($actionDesc) . ',
                                 ' . $dao->quote($userKey) . ',
                                 ' . $dao->quote($time) . ')';
        }
        if(false == $dao->execute($strQuery)) {
            if($actionInfo['addAction']) {
                $errorMsg = '功能信息添加失败';
            } else {
                $errorMsg = '功能信息修改失败';
            }
            return array('errorMsg'=>$errorMsg);
        }
        Acl::updateCache();
        Menu::updateCache();
        return array('success'=>true);
    }
    
    /**
     * 模块排序
     *
     * @param Array $sortData
     * @return boolean
     */
    public static function sortModule($sortData) {
        $dao = self::getDao();
        $strQuery = 'SELECT module_key,module_seq FROM sys_app_modules
                     WHERE app_name=' . $dao->quote(APP_NAME);
        $moduleList = $dao->getAll($strQuery);
        $dbModules = array();
        foreach($moduleList as $module) {
            $dbModules[$module['module_key']] = $module['module_seq'];
        }
        foreach ($sortData as $moduleKey=>$moduleSeq) {
            if($moduleSeq != $dbModules[$moduleKey]) {
                $strQuery = 'UPDATE sys_app_modules SET module_seq=' . abs($moduleSeq) . ' 
                             WHERE app_name=' . $dao->quote(APP_NAME) . '
                               AND module_key=' . $dao->quote($moduleKey);
                $dao->execute($strQuery);
            }
        }
        Acl::updateCache();
        Menu::updateCache();
        return true;
    }
    
    /**
     * 排序功能
     * @param array $sortData
     * @return boolean
     */
    public static function sortAction($sortData) {
        $dao = self::getDao();
        $moduleKey = $sortData['moduleKey'];
        list($groupName, $moduleName) = explode('-', $moduleKey);
        $strQuery = 'SELECT acl_key,menu_seq FROM sys_app_actions 
                     WHERE app_name=' . $dao->quote(APP_NAME) . '
                       AND group_name=' . $dao->quote($groupName) . '
                       AND module_name=' . $dao->quote($moduleName) . '
                       AND is_menu=1 
                     ORDER BY menu_seq';
        $menuList = $dao->getAll($strQuery);
        $menuArray = array();
        foreach ($menuList as $menu) {
            $menuArray[$menu['acl_key']] = $menu['menu_seq'];
        }
        foreach ($sortData['menus'] as $aclKey=>$menuSeq) {
            if(isset($menuArray[$aclKey]) && abs($menuSeq) != abs($menuArray[$aclKey])) {
                $strQuery = 'UPDATE sys_app_actions 
                             SET menu_seq=' . abs($menuSeq) . '
                             WHERE app_name=' . $dao->quote(APP_NAME) . '
                               AND acl_key=' . $dao->quote($aclKey);
                $dao->execute($strQuery);
            }
        }
        return true;
    }
    
    /**
     * 删除外部功能
     * @param string $aclKey
     * @return array
     */
    public static function deleteAction($aclKey) {
        $dao = self::getDao();
        $strQuery = 'DELETE FROM sys_app_actions 
                     WHERE app_name=' . $dao->quote(APP_NAME) . '
                       AND acl_key=' . $dao->quote($aclKey) . '
                       AND menu_url !=' . $dao->quote('') . '
                       AND menu_url IS NOT NULL';
        
        if($dao->execute($strQuery)) {
            return array('success'=>true);
        }
        return array('success'=>false);
    }
    
    /**
     * 需授权的功能列表
     * @return array
     */
    public static function getAclActions($user) {
        $dao = self::getDao();
        $aclActions = array();
        $appGroups = self::getGroupList($user,true);
        $groupNames = array($dao->quote(time()));
        foreach ($appGroups as $groupName=>$group) {
            $groupNames[] = $dao->quote($groupName);
            $aclActions[$groupName]['caption'] = $group['group_caption'] ? $group['group_caption'] . '(' . $groupName . ')' : $groupName;
            if($group['group_icon']) {
                $aclActions[$groupName]['icon'] = $group['group_icon'];
            }
        }
        $strQuery = 'SELECT actions.* FROM sys_app_actions actions
                     LEFT JOIN sys_app_modules modules
                        ON  actions.app_name=modules.app_name
                        AND actions.module_name=modules.module_name
                        AND actions.group_name=modules.group_name
                     WHERE actions.app_name=' . $dao->quote(APP_NAME) . '
                       AND actions.acl_value>0
                       AND actions.group_name IN (' . implode(',', $groupNames) . ')
                     ORDER BY actions.group_name,
                              modules.module_seq,
                              is_menu DESC,
                              menu_seq';
        $actionList = $dao->getAll($strQuery);
        $moduleKeys = array($dao->quote(time()));
        foreach ($actionList as $action) {
            $moduleKeys[] = $dao->quote($action['group_name'] . '-' . $action['module_name']);
        }
        
        $strQuery = 'SELECT * FROM sys_app_modules 
                     WHERE app_name=' . $dao->quote(APP_NAME) . '
                       AND module_key IN (' . implode(',', $moduleKeys) . ')';
        $moduleList = $dao->getAll($strQuery);
        $moduleArray = array();
        foreach ($moduleList as $module) {
            $moduleArray[$module['module_key']] = $module;
        }
        foreach ($actionList as $action) {
            $groupName = $action['group_name'];
            $moduleName = $action['module_name'];
            $moduleKey = $groupName . '-' . $moduleName;
            $actionName = $action['action_name'];
            if(false == $aclActions[$groupName]['modules'][$moduleName]) {
                $aclActions[$groupName]['modules'][$moduleName]['caption'] = $moduleArray[$moduleKey] ? $moduleArray[$moduleKey]['module_caption'] . '(' . $moduleName . ')' : $moduleName;
                if($moduleArray[$moduleKey]['module_icon']) {
                    $aclActions[$groupName]['modules'][$moduleName]['icon'] = $moduleArray[$moduleKey]['module_icon'];
                }
            }
            $aclActions[$groupName]['modules'][$moduleName]['actions'][$action['acl_key']] = $action;
        }
        foreach ($aclActions as $groupName=>$groupCfg) {
            if(false == $groupCfg['modules']) {
                unset($aclActions[$groupName]);
            }
        }
        
        return $aclActions;
    }
};
?>