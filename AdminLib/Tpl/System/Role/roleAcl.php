<?php if(false == $resultScript):?>
<style type="text/css">
.formItem {line-height:18px;padding-top:6px;margin-left:10px}
.formItem span{font-size:15px;font-weight:bold}
.formItem input{width:200px;height:22px;}
</style>
<div class="easyui-layout" id="aclLayout" fit="true" border="false">
    <div region="north" style="height:80px">
        <input type="hidden" name="role_id" value="<?php echo $roleInfo['role_id']?>" />
        <div class="formItem"><label><span>所属应用：</span>
            <input type="text" readonly="true" value="<?php echo $roleInfo['group_name']?>" />
        </label></div>
        <div class="formItem"><label><span>角色名称：</span>
            <input type="text" readonly="true" value="<?php echo $roleInfo['role_caption']?>" />
        </label></div>
    </div>
    <div region="center">
    <ul class="easyui-tree">
    <?php foreach ($aclActions as $groupName=>$groupCfg):?>
    <li state="closed">
        <span><?php if($groupCfg['icon']):?>
        <img src="<?php echo $groupCfg['icon']?>" width="16" height="16" align="absmiddle" />
        <?php else:?>
        <img src="/images/blank.gif" width="16" height="16" align="absmiddle" />
        <?php endif?>
        <?php echo $groupCfg['caption']?></span>
        <ul>
        <?php foreach ($groupCfg['modules'] as $moduleName=>$moduleCfg):?>
            <li>
                <span><?php if($moduleCfg['icon']):?>
                    <img src="<?php echo $moduleCfg['icon']?>" width="16" height="16" align="absmiddle" />
                    <?php else:?>
                    <img src="/images/blank.gif" width="16" height="16" align="absmiddle" />
                    <?php endif?>
                <?php echo $moduleCfg['caption']?></span>
                <ul>
                <?php foreach ($moduleCfg['actions'] as $aclKey=>$action):?>
                    <li><span>
                    <?php if($action['acl_icon']):?>
                    <img src="<?php echo $action['acl_icon']?>" width="16" height="16" align="absmiddle" />
                    <?php else:?>
                    <img src="/images/blank.gif" width="16" height="16" align="absmiddle" />
                    <?php endif?>
                    <?php echo W('StrWidth', array('string'=>$action['acl_caption'], 'width'=>16))?>|
                    <label><input type="radio" name="permValue[<?php echo $aclKey?>]" value="0" <?php if(abs($aclInfo[$groupName][$aclKey]) == 0):?> checked="true"<?php endif?> />禁止</label>
                    <label><input type="radio" name="permValue[<?php echo $aclKey?>]" value="<?php echo $PERM_READ?>" <?php if(abs($aclInfo[$groupName][$aclKey]) == $PERM_READ):?> checked="true"<?php endif?>/>读</label>
                    <?php if($action['acl_value'] & $PERM_WRITE):?>
                    <label><input type="radio" name="permValue[<?php echo $aclKey?>]" value="<?php echo $PERM_READ | $PERM_WRITE?>" <?php if(abs($aclInfo[$groupName][$aclKey]) == abs($PERM_READ | $PERM_WRITE)):?> checked="true"<?php endif?>/>读写</label>
                    <?php endif?>
                    </span></li>
                <?php endforeach;?>
                </ul>
            </li>
        <?php endforeach;?>
        </ul>
    </li>
    <?php endforeach;?>
    </ul>
    </div>
    <div region="south" style="height:32px;padding-top:3px">
    <a href="javascript:void(0)" onclick="saveAcl()" class="easyui-linkbutton" iconCls="icon-save" >保存角色授权</a>
    </div>
</div>
<?php else:?>
<script type="text/javascript">
    <?php if($errorMsg):?>
    
    <?php else:?>
    
    <?php endif?>
</script>
<?php endif?>