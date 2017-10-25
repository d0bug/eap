<?php if(false == $resultScript): ?>
<style type="text/css">
    #editModuleForm em{color:red}
    #editModuleForm div{line-height:18px;padding-top:6px;margin-left:10px}
    #editModuleForm label span{font-size:15px;font-weight:bold}
    #editModuleForm input{width:260px;height:22px;}
    #editModuleForm textarea{height:60px;width:260px}
    #editModuleForm .file{height:22px;width:240px;}
    #editModuleForm .appIcon{text-align:center;width:22px;height:22px;float:left;border:1px solid #ddd;line-height:22px;overflow:hidden;margin-left:0px;margin-right:2px;padding:0px}
</style>
<form method="post" id="editModuleForm" action="<?php echo $url?>" enctype="multipart/form-data" target="ifEditModule">
    <input type="hidden" name="module_key" value="<?php echo $moduleInfo['module_key'] ?>" />
    <div><label><span>模块标识：</span>模块的英文标识，有开发组人员确定，不可修改<br />
            <input type="text"  disabled="true" value="<?php echo $moduleInfo['module_key']?>" />
        </label></div>
    <div><label><span>模块名称：</span>模块的中文名称<br />
            <input type="text" class="easyui-validatebox" required="true" name="module_caption" value="<?php echo $moduleInfo['module_caption']?>" />
        </label></div>
    <div><label><span>模块图标：</span>模块图标,宽高不超过100px,允许类型（*.png,*.jpg,*.gif）<br />
            <div class="appIcon">
            <?php if($moduleInfo['module_icon']):?>
            <img src="<?php echo $moduleInfo['module_icon']?>?<?php echo time()?>" width="18" height="18"/>    
            <?php endif?>
            </div>
            <input type="file" class="file" name="module_icon" />
        </label></div>
    <div><label><span>模块排序：</span>模块在应用组内的显示顺序<br />
            <input type="text" name="module_seq" size="5" style="width:70px" value="<?php echo $moduleInfo['module_seq']?>" />
        </label></div>
    <div><a href="javascript:void(0)" onclick="jQuery('#editModuleForm').submit()" class="easyui-linkbutton" iconCls="icon-save" >保存模块信息</a></div>
</form>
<iframe id="ifEditModule" name="ifEditModule" style="display:none"></iframe>
<?php else:?>
    <script type="text/javascript">
    <?php if($errorMsg):?>
        alert('<?php echo $errorMsg?>');
    <?php else:?>
        alert('模块信息修改成功');
        parent.reloadGrid();
    <?php endif;?>
    </script>
<?php endif?>