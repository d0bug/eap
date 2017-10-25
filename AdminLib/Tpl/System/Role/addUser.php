<?php if(false == $resultScript):?>
<script type="text/javascript">
function findUsers() {
    var userType = jQuery('#user_type').val();
    var userName = jQuery('#user_name').val();
    jQuery('#uListGrid').datagrid({
        url:'<?php echo $jsonFindResult?>',
        queryParams:{userType:userType, userName:userName}
    })
}

function addRoleUser() {
    var checkedRows = jQuery('#uListGrid').datagrid('getChecked');
    var userKeys = [];
    jQuery.each(checkedRows, function(k, v){
        userKeys.push(v.user_key + "\t" + v.user_name + "\t" + v.real_name + "\t" + v.mail);
    })
    userKeys = userKeys.join('\n');
    if(userKeys.length > 0) {
        jQuery.post('<?php echo $addUserUrl?>', {role:'<?php echo $roleId?>', users:userKeys}, function(data){
            jQuery('body').layout('remove', 'east');
            alert('添加成功');
            reloadUsers();
        })
    } else {
        alert('请选择要添加的用户');
    }
}
</script>
<style type="text/css">
    #addUserForm em{color:red;}
    #addUserForm div{line-height:18px;padding-top:6px;margin-left:10px}
    #addUserForm span{font-size:15px;font-weight:bold}
    #addUserForm input{width:260px;height:22px;}
    #addUserForm .appIcon{text-align:center;width:22px;height:22px;float:left;border:1px solid #ddd;line-height:22px;overflow:hidden;margin-left:0px;margin-right:2px;padding:0px}
</style>
<div class="easyui-layout" fit="true" border="false">
    <div region="north" style="height:140px">
        <form id="addUserForm" method="POST" action="<?php echo $addUserUrl?>">
            <input type="hidden" name="roleId" value="<?php echo $roleId?>" />
            <div><label><span>用户基本类型：</span><br /><select id="user_type">
            <?php foreach($userTypes as $userType=>$typeName):?>
            <option value="<?php echo $userType?>"><?php echo $typeName?></option>
            <?php endforeach;?>
            </select></label></div>
            <div><label><span>用户名/姓名：</span><br /><input type="text" id="user_name"/></label>
            </div>
            <div><a href="javascript:findUsers()" class="easyui-linkbutton" iconCls="icon-search">查询</a></div>
        </form>
    </div>
    <div region="center">
        <table id="uListGrid" class="easyui-datagrid" rownumbers="true" fit="true" border="false">
        <thead>
            <tr>
                <th field="user_key"  checkbox="true">标识</th>
                <th field="real_name">姓名</th>
                <th field="user_type">用户类型</th>
                <th field="user_name">用户名</th>
            </tr>
        </thead>
        </table>
    </div>
    <div region="south" style="height:32px;padding:3px 0px 0px 3px">
    <a href="javascript:addRoleUser()" class="easyui-linkbutton" iconCls="icon-add">添加选定用户</a>
    </div>
</div>

<?php else:?>
<script  type="text/javascript">

</script>
<?php endif?>