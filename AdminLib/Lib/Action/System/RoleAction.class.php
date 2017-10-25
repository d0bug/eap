<?php
import('COM.Acl.Role');
class RoleAction extends SystemCommAction {
    /**
     * 角色管理
     */
    public function main() {
        $permValue = $this->permValue;
        $groupUrl = $this->getUrl('jsonAppList', 'App');
        $groupRoleUrl = $this->getUrl('jsonGroupRoles');
        $addRoleUrl = $this->getUrl('addRole');
        $editRoleUrl = $this->getUrl('editRole');
        $delRoleUrl = $this->getUrl('deleteRole');
        $aclUrl = $this->getUrl('roleAcl');
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    /**
     * 添加新角色
     */
    protected function addRole() {
        $groupName = SysUtil::safeString($_GET['group']);
        $this->writeCheck($this->getAclKey('main'));
        if($this->isPost()) {
            $resultScript  = true;
            $roleInfo = $_POST;
            $roleInfo['user'] = $this->loginUser;
            $saveResult = Role::saveRole($roleInfo);
            $errorMsg = $saveResult['errorMsg'];
        }
        $url = $_SERVER['REQUEST_URI'];
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    
    protected function editRole() {
        $roleId = SysUtil::uuid($_GET['role']);
        $this->writeCheck($this->getAclKey('main'));
        if($this->isPost()) {
            $resultScript  = true;
            $roleInfo = $_POST;
            $roleInfo['user'] = $this->loginUser;
            $saveResult = Role::saveRole($roleInfo);
            $errorMsg = $saveResult['errorMsg'];
        } else {
            $url = $_SERVER['REQUEST_URI'];
            $roleInfo = Role::getInfo($roleId);
        }
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    protected function deleteRole() {
        $roleId = SysUtil::uuid($_POST['role']);
        $this->writeCheck($this->getAclKey('main'));
        $delResult = Role::deleteRole($roleId);
        echo abs($delResult);
    }
    
    
    /**
     * 角色授权管理
     */
    protected function roleAcl() {
        $roleId = SysUtil::uuid($_GET['role']);
        if($this->isPost()) {
            $this->writeCheck($this->getAclKey('main'));
            $roleId = SysUtil::uuid($_POST['role_id']);
            $saveResult = Role::saveRoleAcl($roleId, $_POST['permValue'], $this->loginUser);
            echo abs($saveResult);
            exit;
        }
        $url = $_SERVER['REQUEST_URI'];
        $roleInfo = Role::getInfo($roleId);
        $aclInfo = Acl::getRAclArray($roleId);
        $aclActions = AppGroup::getAclActions($this->loginUser);
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    /**
     *角色用户管理
     */
    public function userList() {
        $permValue = $this->permValue;
        $roleName = SysUtil::safeString($_GET['role_id']);
        $jsonRoleUrl = $this->getUrl('jsonGroupRoles');
        $jsonUserUrl = $this->getUrl('jsonUsers');
        $addUserUrl = $this->getUrl('addUser');
        $delUserUrl = $this->getUrl('delUser');
        $userAclUrl = $this->getUrl('userAcl');
        $groups = AppGroup::getGroupList($this->loginUser,true);
        foreach ($groups as $groupName=>$group) {
            break;
        }
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    /**
     * 添加角色用户
     */
    protected function addUser() {
        $this->writeCheck($this->getAclKey('userList'));
        $roleId = SysUtil::safeString($_GET['role']);
        $roleInfo = Role::getInfo($roleId);
        $userTypes = Employee::getTypeArray();
        $addUserUrl = $_SERVER['REQUEST_URI'];
        $jsonFindResult = $this->getUrl('jsonFindUser');
        if($this->isPost()) {
            $roleId = SysUtil::safeString($_POST['role']);
            $users = SysUtil::safeString($_POST['users']);
            $users = explode(',', $users);
            if(Role::addUser($roleId, $users, $this->loginUser)) {
                echo 1;
            } else {
                echo 0;
            }
            exit;
        }
        $this->assign(get_defined_vars());
        $this->display();
    }
    
    protected function jsonFindUser() {
        $userType = SysUtil::safeString($_POST['userType']);
        $keyword = SysUtil::safeString($_POST['userName']);
        if(false == $keyword) echo json_encode(array('total'=>0));
        $userList = User::findUser($userType, $keyword);
        echo json_encode(array('total'=>sizeof($userList), 'rows'=>$userList));
        exit;
    }
    
    protected function delUser() {
        $this->writeCheck($this->getAclKey('userList'));
        $userKey = SysUtil::safeString($_POST['user']);
        $roleId = SysUtil::safeString($_POST['role']);
        Role::deleteUser($roleId, $userKey);
        echo 1;
    }
    
    protected function userAcl() {
        $this->writeCheck($this->getAclKey('userList'));
        
    }
    
    /**
     * 应用组角色列表
     */
    protected function jsonGroupRoles() {
        $groupName = SysUtil::safeString($_REQUEST['group']);
        $roleList = Role::getAppGroupRoles($groupName);
        echo json_encode(array('total'=>sizeof($roleList), 'rows'=>$roleList));
        exit;
    }
    
    protected function jsonUsers() {
        $currentPage = abs($_POST['page']);
        $pageSize = abs($_POST['rows']);
        $userList = array();
        $groupName = SysUtil::safeString($_POST['group']);
        $keyword = SysUtil::safeString($_POST['keyword']);
        $role = SysUtil::uuid($_POST['role']);
        list($total, $userList) = Role::getUsers($groupName, $role, $keyword, $currentPage, $pageSize);
        echo json_encode(array('total'=>$total, 'rows'=>$userList));
    }
    
}

?>
