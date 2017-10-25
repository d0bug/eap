<!doctype html>
<html>
<head>
    <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
    <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
    
    <script type="text/javascript">
    var curGroup = '<?php echo $groupName?>';
    var curRole = '';
    var tools = [{iconCls:'icon-cancel',
                  handler:function(){
                    jQuery('body').layout('remove', 'east');
                  }}]
    function groupCaption(groupName, data) {
        var groupCaption = jQuery.trim(data.group_caption);
        groupName = jQuery.trim(groupName);
        if(groupCaption) {
            return groupCaption + '(' + groupName + ')';
        }
        return groupName;
    }
    
    function userType(uType, data) {
        var utParts = data.user_key.split('-');
        return utParts[0];
    }
    
    function creator(uKey,data) {
        utParts = data.user_key.split('-');
        if(utParts[0] == data.user_role) {
            return '默认授权';
        }
        uParts = uKey.split('-');
        return uParts[1];
    }
    
    
    <?php if($permValue & $PERM_WRITE):?>
    function manage(userKey, data) {
        var manage = '';
        if(creator(data.creator, data) != '默认授权') {
            manage += '<a href="javascript:delUser(\'' + userKey + '\', \'' + data.user_realname + '\')">从角色中删除</a> |';
        }
        manage += '<a href="javascript:addAcl(\'' + userKey + '\',\'' + data.user_realname + '\')">特殊授权</a>';
        return manage;
    }
    
    function addUser() {
        if(curRole) {
            var seleRole = jQuery('#roleGrid').datagrid('getSelected');
            var roleCaption = seleRole.role_caption;
            jQuery('body').layout('remove', 'east');
            jQuery('body').layout('add', {
                region:'east',
                title:'添加角色用户(' + roleCaption + ')',
                width:300,
                href:'<?php echo $addUserUrl?>/role/' + curRole,
                collapsible:false,
                tools:tools
            })
        } else {
            alert('请选择相应角色');
        }
    }
    
    function delUser(uKey, uName) {
        if(confirm('确定要从本角色组中删除用户“' + uName + '”吗？')) {
            jQuery.post('<?php echo $delUserUrl?>', {role:curRole, user:uKey}, function(data) {
                if(data ==1) {
                    alert('用户删除成功');
                    reloadUsers();
                } else {
                    alert('用户删除失败');
                }
            })
        }
    }
    
    function addAcl(uKey) {
        jQuery('body').layout('remove', 'east');
        jQuery('body').layout('add', {
            region:'east',
            title:'添加角色用户',
            width:300,
            href:'<?php echo $userAclUrl?>',
            collapsible:false,
            tools:tools
        })
    }
    
    <?php endif?>
    function filterRoles() {
        var groupName = jQuery('#group_name').val();
        if(curGroup != groupName) {
            curGroup = groupName;
            curRole = '';
            reloadRoles();
            reloadUsers();
        }
    }
    
    function filterUsers() {
        reloadUsers();
    }
    
    function reloadRoles() {
        jQuery('#roleGrid').datagrid({
            queryParams:{group:curGroup},
            onSelect:function(index,data){
                curRole = data.role_id;
                reloadUsers();
            }
        });
    }
    
    function reloadUsers() {
        jQuery('#userGrid').datagrid({
            queryParams:{group:curGroup,role:curRole, keyword:jQuery('#keyword').val()}
        })
    }
    
    jQuery(function(){
        reloadRoles();
        reloadUsers();
    })
    </script>
    <style type="text/css">
    .pagination-info{display:none}
    select{padding:4px;}
    </style>
</head>
<body class="easyui-layout" fit="true" border="false">
    <div region="west" style="width:350px">
        <table id="roleGrid" border="false" url="<?php echo $jsonRoleUrl?>" fit="true" toolbar="#frToolbar" singleselect="true" rownumbers="true">
        <thead>
        <tr>
            <th field="group_name" formatter="groupCaption">所属应用组</th>
            <th field="role_caption">角色名称</th>
        </tr>
        </thead>
        </table>
    </div>
    <div region="center">
    <table id="userGrid"  border="false" url="<?php echo $jsonUserUrl?>" fit="true" toolbar="#usrToolbar" singleselect="true" rownumbers="true" pagination="true" pageList="[20,30,50]">
    <thead>
        <tr>
            <th field="user_type" formatter="userType">用户类型</th>
            <th field="user_name">用户名</th>
            <th field="user_realname">真实姓名</th>
            <th field="creator" formatter="creator">授权人</th>
            <?php if($permValue & $PERM_WRITE):?><th field="user_key" formatter="manage">授权管理</th><?php endif?>
        </tr>
    </thead>
    </table>
    </div>
    <div id="frToolbar">
    &nbsp;筛选:<select class="group_name" id="group_name">
    <?php foreach ($groups as $gName=>$group):?>
    <option value="<?php echo $gName?>" <?php if($groupName == $gName):?> selected="true"<?php endif?>><?php echo $group['group_caption'] ? $group['group_caption'] . '(' . $gName . ')' : $gName?></option>
    <?php endforeach;?>
    </select><a href="javascript:filterRoles()" class="easyui-linkbutton" iconCls="icon-search" plain="true">确定</a>
    </div>
    <div id="usrToolbar">
    <?php if($permValue & $PERM_WRITE):?>
    <a href="javascript:addUser()" class="easyui-linkbutton" iconCls="icon-add" plain="true">添加用户</a>
    <span class="datagrid-btn-separator"></span>
    查询用户:<input type="text" name="keyword" id="keyword" placeholder="用户名或真实姓名" style="width:120px" />
    <a href="javascript:filterUsers()" class="easyui-linkbutton" iconCls="icon-search" plain="true">查询</a>
    </div>
    <?php endif?>
</body>
</html>
    