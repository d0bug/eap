<?php
import('COM.Dao.Dao');
import('COM.Acl.AppGroup');
import('ORG.Util.NCache');
import('COM.SysUtil');
class Menu {
    /**
     * 获取数据库访问独享
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
     * 构造菜单数组，array('groupName'=>array('moduleName'=>array('aclKey'=>$menuInfo)))
     * @param Array $acl
     * @return Array $menuArray
     */
    public static function getMenus($acl) {
        $cacheKey = md5(serialize($acl));
        $cache = NCache::getCache();
        $sortedMenus = $cache->get('Menu', $cacheKey);
        if (false == $sortedMenus) {
            $dao = self::getDao();
            $aclArray = array();
            $groupNameArray = array();
            foreach($acl as $groupName=>$actions) {
                foreach($actions as $aclKey=>$aclValue) {
                    $aclArray[] = $dao->quote($aclKey);
                    $groupNameArray[] = $dao->quote($groupName);
                }
            }
            
            $strQuery = 'SELECT smenu.*,group_caption,group_icon,module_caption,module_icon
                         FROM sys_app_actions smenu
					    LEFT JOIN sys_app_groups sgroup
							ON smenu.app_name=sgroup.app_name
							and smenu.group_name=sgroup.group_name
						LEFT JOIN sys_app_modules smodule
							on smenu.app_name=smodule.app_name
							AND smenu.module_name=smodule.module_name
                           AND smodule.group_name=sgroup.group_name
                         WHERE smenu.app_name=' . $dao->quote(APP_NAME) . '
                           AND smenu.is_menu=1
                           AND smenu.group_name IN (' . implode(',', array_unique($groupNameArray)) . ')
                           AND smenu.acl_key IN (' . implode(',', $aclArray) . ')
                         ORDER BY group_name,smodule.module_seq,smenu.menu_seq';
            $menuList = $dao->getAll($strQuery);
            if(false == $menuList) {
                $menus = self::getCommMenus();
                self::initMenu($menus);
                return $menus;
                
            }
            foreach($menuList as $menu) {
                $menu['menu_caption'] = $menu['acl_caption'];
                $menu['menu_icon'] = $menu['acl_icon'];
                if(false == $menuArray[$menu['group_name']]) {
                    $groupCaption = $menu['group_caption'] ? $menu['group_caption'] : $menu['group_name'];
                    $menuArray[$menu['group_name']] = array('caption' => $groupCaption, 'icon'=>$menu['group_icon']);
                }
                if (false == $menuArray[$menu['group_name']]['modules'][$menu['module_name']]) {
                    $moduleCaption = $menu['module_caption'] ? $menu['module_caption'] :$menu['module_name'];
                    $menuArray[$menu['group_name']]['modules'][$menu['module_name']] = array('caption'=>$moduleCaption, 'icon'=>$menu['module_icon']);
                }
                if (false == $menu['menu_url']) {
                    $menu['menu_url'] = U($menu['group_name'] . '/' . $menu['module_name'] . '/' . $menu['action_name']);
                }
                $menuArray[$menu['group_name']]['modules'][$menu['module_name']]['menus'][$menu['acl_key']] = $menu;
            }
            
            
            #根据配置文件中应用组的顺序进行排序
            $appGroups = AppGroup::getAppGroups();
            $sortedMenus = array();
            foreach($appGroups as $groupName) {
                if($menuArray[$groupName]) {
                    $sortedMenus[$groupName] = $menuArray[$groupName];
                }
            }
            
            if($sortedMenus) {
                $cache->set('Menu', $cacheKey, $sortedMenus);
            }
        }
        
        
        return $sortedMenus;
    }
    
    private static function getCommMenus() {
        static $commMenus = array();
        if(false == $commMenus) {
            $commMenus = require_once(CONF_PATH . '/commMenu.conf.php');
        }
        return $commMenus;
    }
    
    private static function initMenu($commMenus) {
        $dao = self::getDao();
        $time = date('Y-m-d H:i:s');
        foreach($commMenus as $groupName=>$groupCfg) {
            $strQuery = 'SELECT count(1) FROM sys_app_groups 
                         WHERE app_name=' . $dao->quote(APP_NAME) . ' 
                           AND group_name=' . $dao->quote($groupName);
            $gCount = $dao->getOne($strQuery);
            if($gCount == 0) {
                $strQuery = 'INSERT INTO sys_app_groups 
                             (app_name, group_name, group_caption, group_icon,group_desc, update_user, update_at)
                             VALUES 
                             (' . $dao->quote(APP_NAME) . ',
                              ' . $dao->quote($groupName) . ',
                              ' . $dao->quote($groupCfg['caption']) . ',
                              ' . $dao->quote($groupCfg['icon']) . ',
                              ' . $dao->quote($groupCfg['caption']) . ',
                              ' . $dao->quote('init') . ',
                              ' . $dao->quote($time) . ')';
                $dao->execute($strQuery);
            }
            $moduleSeq = 0;
            foreach ($groupCfg['modules'] as $moduleName=>$moduleCfg) {
                $moduleSeq ++;
                $moduleKey = $groupName . '-' . $moduleName;
                $strQuery = 'SELECT count(1) FROM sys_app_modules 
                             WHERE app_name=' . $dao->quote(APP_NAME) . ' 
                               AND module_key=' . $dao->quote($moduleKey);
                $mCount = $dao->getOne($strQuery);
                if($mCount == 0) {
                    $strQuery = 'INSERT INTO sys_app_modules
                                (app_name,group_name,module_name,module_key, module_caption,module_icon,module_seq,update_user,update_at)
                                VALUES 
                                (' . $dao->quote(APP_NAME) . ',
                                 ' . $dao->quote($groupName) . ',
                                 ' . $dao->quote($moduleName) . ',
                                 ' . $dao->quote($moduleKey) . ',
                                 ' . $dao->quote($moduleCfg['caption']) . ',
                                 ' . $dao->quote($moduleCfg['icon']) . ',
                                 ' . $dao->quote($moduleSeq) . ', 
                                 ' . $dao->quote('init') . ',
                                 ' . $dao->quote($time) . ')';
                    $dao->execute($strQuery);
                }
                foreach ($moduleCfg['menus'] as $menu) {
                    list($groupName, $moduleName, $actionName) = explode('-', $menu['acl_key']);
                    $strQuery = 'SELECT count(1) FROM sys_app_actions 
                                 WHERE app_name=' . $dao->quote(APP_NAME) . '
                                   AND acl_key=' . $dao->quote($menu['acl_key']);
                    $aCount = $dao->getOne($strQuery);
                    if($aCount == 0) {
                        $strQuery = 'INSERT INTO sys_app_actions 
                                     (app_name,group_name,module_name,action_name,acl_key,
                                      acl_caption,acl_value,acl_icon, is_menu,menu_seq,action_desc,update_user,update_at)
                                     VALUES (' . $dao->quote(APP_NAME) . ',
                                             ' . $dao->quote($groupName) .  ',
                                             ' . $dao->quote($moduleName) . ',
                                             ' . $dao->quote($actionName) . ',
                                             ' . $dao->quote($menu['acl_key']) . ',
                                             ' . $dao->quote($menu['menu_caption']) . ',
                                             ' . abs($menu['acl_value']) . ',
                                             ' . $dao->quote($menu['menu_icon']) . ',
                                             ' . 1 . ',
                                             ' . abs($menu['menu_seq']) . ',
                                             ' . $dao->quote($menu['menu_caption']) . ',
                                             ' . $dao->quote('init') . ',
                                             ' . $dao->quote($time) . ')';
                        $dao->execute($strQuery);
                    }
                }
            }
        }
    }
    
    public static function updateCache() {
        $cache = NCache::getCache();
        $cache->delete('Menu');
    }
    
    public static function update() {
        
    }
    
    
};
?>