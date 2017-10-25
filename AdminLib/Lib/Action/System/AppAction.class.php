<?php
import('COM.Acl.AppGroup');
class AppAction extends SystemCommAction{
    public function main() {
        $this->readCheck();
        $editPerm = Permission::getPermInfo($this->loginUser, $this->getAclKey('editApp'));
        $permValue = $editPerm['permValue'];
        $jsonUrl = $this->getUrl('jsonAppList');
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    /**
     * 应用组模块管理
     */
    public function module(){
        $this->readCheck();
        $editPerm = Permission::getPermInfo($this->loginUser, $this->getAclKey('editModule'));
        $permValue = $editPerm['permValue'];
        $jsonUrl = $this->getUrl('jsonModuleList');
        $sortUrl = $this->getUrl('sortModule');
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    public function action() {
        $moduleUrl = $this->getUrl('jsonModuleList');
        $jsonActionUrl = $this->getUrl('jsonActionList');
        $addActionUrl = $this->getUrl('addAction');
        $editActionUrl = $this->getUrl('editAction');
        $delActionUrl = $this->getUrl('deleteAction');
        $sortActionUrl = $this->getUrl('sortAction');
        $permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey('editAction'));
        $permValue = $permInfo['permValue']; 
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    /**
     * 使用protected避免json方法授权,通过_empty调用
     * @return Json
     */
    protected function jsonAppList() {
        $this->readCheck($this->getAclKey('main'));
        $appGroups = AppGroup::getGroupList($this->loginUser, true);
        echo json_encode(array('total'=>sizeof($appGroups), 'rows'=>array_values($appGroups)));
    }
    
    /**
     * 使用protected 避免json方法授权，通过_empty调用
     * @return Json
     */
    protected function jsonModuleList() {
        $this->readCheck($this->getAclKey('main'));
        $moduleTree = AppGroup::getModuleTree($this->loginUser);
        echo json_encode(array('total'=>sizeof($moduleTree), 'rows'=>$moduleTree));
    }
    
    
    protected function jsonActionList() {
        $this->readCheck($this->getAclKey('action'));
        $module = SysUtil::safeString($_GET['module']);
        $actions = AppGroup::getModuleActions($module);
        echo json_encode(array('total'=>sizeof($actions), 'rows'=>$actions));
    }
    
    /**
     * 修改应用信息
     */
    public function editApp() {
        $groupName = SysUtil::safeString($_GET['app']);
        $this->writeCheck();
        $url = $_SERVER['REQUEST_URI'];
        if($this->isPost()) {
            $resultScript = true;
            $appInfo = $_POST;
            $appInfo['group_icon'] = $_FILES['group_icon'];
            $appInfo['user'] = $this->loginUser;
            if($appInfo['group_name'] != $groupName) {
                $errorMsg = '非法提交';
            } else {
                $saveResult = AppGroup::saveGroup($appInfo);
                $errorMsg = $saveResult['errorMsg'];
            }
        }
        if(false == $resultScript)
            $appInfo = AppGroup::getGroupInfo($groupName);
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    /**
     * 修改模块信息
     */
    public function editModule() {
        $moduleKey = SysUtil::safeString($_GET['module']);
        $this->writeCheck($this->getAclKey('module'));
        $url = $_SERVER['REQUEST_URI'];
        if($this->isPost()) {
            $resultScript = true;
            $moduleInfo = $_POST;
            $moduleInfo['module_icon'] = $_FILES['module_icon'];
            $moduleInfo['user'] = $this->loginUser;
            if($moduleInfo['module_key'] != $moduleKey) {
                $errorMsg = '非法提交';
            } else {
                $saveResult = AppGroup::saveModule($moduleInfo);
                $errorMsg = $saveResult['errorMsg'];
            }
        }
        if(false == $resultScript)
            $moduleInfo = AppGroup::getModuleInfo($moduleKey);
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    /**
     * 排序模块
     */
    protected function sortModule() {
        $this->writeCheck($this->getAclKey('editModule'));
        if($this->isPost()) {
            $sortData = $_POST['sort'];
            $sortResult = AppGroup::sortModule($sortData);
            echo 1;
            exit;
        }
        echo 0;
    }
    
    public function addAction() {
        $url = $this->getUrl('addAction');
        $moduleKey = SysUtil::safeString($_GET['module']);
        $this->writeCheck();
        if($this->isPost()) {
            $aclKey = SysUtil::safeString($_POST['module_key']) . '-' . SysUtil::safeString($_POST['action_name']);
            $_GET['action'] = $aclKey;
            $_POST['acl_key'] = $aclKey;
            return $this->editAction();
        }
        $this->assign(get_defined_vars());
        $this->display('addAction');
    }
    
    public  function editAction() {
        $this->writeCheck();
        $aclKey = SysUtil::safeString($_GET['action']);
        $url = $_SERVER['REQUEST_URI'];
        if($this->isPost()) {
            $resultScript = true;
            $actionInfo = $_POST;
            $actionInfo['acl_icon'] = $_FILES['acl_icon'];
            $actionInfo['user'] = $this->loginUser;
            if($aclKey != $actionInfo['acl_key']) {
                $errorMsg = '非法提交';
            } else {
                $editResult = AppGroup::saveAction($actionInfo);
                $errorMsg = $editResult['errorMsg'];
            }
        }
        if(false == $resultScript)
            $actionInfo = AppGroup::getActionInfo($aclKey);
        $this->assign(get_defined_vars());
        $this->display('editAction');
    }
    
    protected function sortAction() {
        $this->writeCheck($this->getAclKey('editAction'));
        $moduleKey = SysUtil::safeString($_GET['module']);
        if($this->isPost()) {
            $sortData = array('moduleKey'=>$moduleKey);
            $sortData['menus'] = $_POST['menu_sort'];
            $sortResult = AppGroup::sortAction($sortData);
            echo 1;
            exit;
        }
        echo 0;
    }
    
    
    public function deleteAction() {
        $this->writeCheck($this->getAclKey('editAction'));
        $aclKey = SysUtil::safeString($_POST['acl_key']);
        $delResult = AppGroup::deleteAction($aclKey);
        echo abs($delResult['success']);
    }
}

?>
