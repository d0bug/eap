<?php if(false == $resultScript):?>
<style type="text/css">
    #moduleForm em{color:red}
    #moduleForm div{line-height:18px;padding-top:6px;margin-left:10px}
    #moduleForm span{font-size:15px;font-weight:bold}
    #moduleForm input{width:260px;height:22px;}
</style>
<form id="moduleForm" method="POST" action="<?php echo $url?>" target="hdModuleFrame">
<input type="hidden" name="module_id" id="module_id" value="<?php echo $moduleInfo['module_id']?>" />
<input type="hidden" name="module_subject" id="module_subject" value="<?php echo $moduleInfo['module_subject']?>" />
<div><label><span>所属学科：</span>不可修改<br />
        <input type="text"  name="subject_caption" disabled="true" class="easyui-validatebox" required="true" value="<?php echo $moduleInfo['subject_caption']?>" />
    </label></div>
<div><label><span>模块标题：</span>知识模块中文名称<br />
        <input type="text"  name="module_caption" class="easyui-validatebox" required="true" value="<?php echo $moduleInfo['module_caption']?>" />
    </label></div>
<div><label><span>模块编码：</span>知识模块编码<br />
        <input type="text"  name="module_code" class="easyui-validatebox" style="ime-mode:disabled" required="true" value="<?php echo $moduleInfo['module_code']?>" />
    </label></div>
<div><a href="javascript:void(0)" onclick="jQuery('#moduleForm').submit()" class="easyui-linkbutton" iconCls="icon-save" >保存知识模块</a></div>
</form>
<iframe id="hdModuleFrame" name="hdModuleFrame" style="display:none"></iframe>
<?php else:?>
<script type="text/javascript">
    <?php if($errorMsg):?>
    alert('<?php echo $errorMsg?>');
    <?php else:?>
    alert('模块信息修改成功');
    parent.loadModules();
    <?php endif?>
</script>
<?php endif?>