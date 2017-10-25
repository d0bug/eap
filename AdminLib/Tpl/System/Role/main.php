<!doctype html>
<html>
<head>
    <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
    <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
    <script type="text/javascript">
    var curGroup = '';
    var tools = [{iconCls:'icon-cancel',
                        handler:function(){
                            jQuery('body').layout('remove', 'east');
                        }}];
    function groupName(groupName, data) {
        if(data.group_caption) {
            return data.group_caption + '(' + data.group_name + ')';
        }
        return data.group_name;
    }
    <?php if($permValue & $PERM_WRITE):?>
    function addRole() {
        if(curGroup) {
            jQuery('body').layout('remove', 'east');
            jQuery('body').layout('add', {
                region:'east',
                width:350,
                href:'<?php echo $addRoleUrl?>/group/' + curGroup,
                title:'添加系统角色（' + curGroup + '）',
                tools:tools,
                collapsible:false
            })
        } else {
            alert('请选择应用组');
        }
    }
    
    function manage(roleId, data) {
        if(false == data.is_system) {
            return str = '<a href="javascript:editRole(\'' + roleId + '\', \'' + data.role_caption + '\')">修改</a> '
                   + '| <a href="javascript:delRole(\'' + roleId + '\', \'' + data.role_caption + '\')">删除</a> '
                   + '| <a href="javascript:editAcl(\'' + roleId + '\', \'' + data.role_caption + '\')">授权</a>';
        }
        return '<a href="javascript:editAcl(\'' + roleId + '\', \'' + data.role_caption + '\')">授权</a>';
    }
    
    function editRole(roleId, roleCaption) {
        jQuery('body').layout('remove', 'east');
        jQuery('body').layout('add', {
            region:'east',
            width:350,
            title:'修改角色信息',
            href:'<?php echo $editRoleUrl?>/role/' + roleId,
            tools:tools,
            collapsible:false
        })
    }
    
    function delRole(roleId, roleCaption) {
        if(confirm('确定要删除角色“' + roleCaption + '”吗？')) {
            jQuery.post('<?php echo $delRoleUrl?>', {role:roleId}, function(data){
                if(data == 1) {
                    alert('角色删除成功');
                    reloadGrid();
                } else {
                    alert('角色删除失败');
                }
            })
        }
    }
    
    function editAcl(roleId, roleCaption) {
        jQuery('body').layout('remove', 'east');
        jQuery('body').layout('add', {
            region:'east',
            width:350,
            title:'角色授权(' + roleCaption + ')',
            href:'<?php echo $aclUrl?>/role/' + roleId,
            tools:tools,
            collapsible:false
        })
    }
    
    function reloadGrid() {
        jQuery('body').layout('remove', 'east');
        jQuery('#roleGrid').datagrid('reload');
    }
    
    function saveAcl() {
        var formData = jQuery('#aclLayout').find('input').serialize();
        jQuery.post('<?php echo $aclUrl?>', formData, function(data) {
            if(data == 1) {
                alert('角色授权成功');
                jQuery('body').layout('remove', 'east')
            } else {
                alert('角色授权失败');
            }
        })
    }
    <?php endif?>
    
    jQuery(function(){
        jQuery('#groupGrid').datagrid({
            onSelect:function(idx, data){
                curGroup = data.group_name;
                jQuery('body').layout('remove', 'east');
                jQuery('#roleGrid').datagrid({
                    view:detailview,
                    url:'<?php echo $groupRoleUrl?>/group/' + curGroup,
                    detailFormatter:function(index,data){
                        return '<div class="role_desc">' + jQuery.trim(data.role_desc) + '</div>';
                    },
                    onExpandRow:function(index,data){
                        jQuery('#roleGrid').datagrid('selectRow',index);
                        $('#roleGrid').datagrid('fixDetailRowHeight',index); 
                        var rowCnt = jQuery('div.role_desc').length;
                        for(var i=0;i<rowCnt;i++) {
                            if(i != index) {
                                jQuery('#roleGrid').datagrid('collapseRow',i);
                            }
                        }
                    }
                })
            }
        })
    })
    </script>
</head>
<body class="easyui-layout" fit="true" border="false">
    <div region="west" style="width:230px">
        <table id="groupGrid" fitColumns="false" fit="true" border="false" url="<?php echo $groupUrl?>" rownumbers="true" singleselect="true">
            <thead>
            <tr>
                <th width="200" field="group_name" formatter="groupName">应用组名称</th>
            </tr>
            </thead>
        </table>
    </div>
    <div region="center">
        <table id="roleGrid" class="easyui-datagrid" fit="true" border="false" rownumbers="true" singleselect="true" toolbar="#toolbar">
            <thead>
                <tr>
                    <th field="group_name">应用组</th>
                    <th field="role_caption" width="140">角色名称</th>
                    <?php if($permValue & $PERM_WRITE):?><th field="role_id" formatter="manage">角色管理</th><?php endif?>
                </tr>
            </thead>
        </table>
    </div>
    <?php if($permValue & $PERM_WRITE):?>
    <div id="toolbar">
    <a href="javascript:addRole()" class="easyui-linkbutton" plain="true" iconCls="icon-add">添加角色</a>
    </div>
    <?php endif;?>
</body>
</html>