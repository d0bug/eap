<?php if (false == $resultScript):?>
<style type="text/css">
    #roleInfoForm em{color:red}
    #roleInfoForm div{line-height:18px;padding-top:6px;margin-left:10px}
    #roleInfoForm span{font-size:15px;font-weight:bold}
    #roleInfoForm input{width:260px;height:22px;}
    #roleInfoForm textarea{height:60px;width:260px}
    #roleInfoForm .file{height:22px;width:240px;}
    #roleInfoForm .appIcon{text-align:center;width:22px;height:22px;float:left;border:1px solid #ddd;line-height:22px;overflow:hidden;margin-left:0px;margin-right:2px;padding:0px}
</style>
<form id="roleInfoForm" method="POST" action="<?php echo $url?>" target="ifRoleInfo">
<input type="hidden" name="group_name" value="<?php echo $groupName?>"/>
<input type="hidden" name="role_id" value="<?php echo $roleId?>" />
<div><label><span>所属应用：</span>所属应用标识<br />
            <input type="text" disabled="true" value="<?php echo $roleInfo['group_name']?>" />
        </label></div>
<div><label><span>角色名称：</span>模块的中文名称<br />
            <input type="text" class="easyui-validatebox" required="true" name="role_caption" value="<?php echo $roleInfo['role_caption']?>" />
        </label></div>
<div><label><span>角色介绍：</span>模块的中文名称<br />
            <textarea name="role_desc" id="role_desc"><?php echo $roleInfo['role_desc']?></textarea>
        </label></div>
        <div><a href="javascript:void(0)" onclick="jQuery('#roleInfoForm').submit()" class="easyui-linkbutton" iconCls="icon-save" >保存角色信息</a></div>
</form>
<iframe name="ifRoleInfo" id="ifRoleInfo" style="display:none"></iframe>
<?php else:?>
    <script type="text/javascript">
    <?php if($errorMsg):?>
        alert('<?php echo $errorMsg?>')    
    <?php else:?>
        parent.reloadGrid();
    <?php endif?>
    </script>
<?php endif?>
