<?php if (false == $resultScript):?>
<style type="text/css">
    #editAppForm em{color:red}
    #editAppForm div{line-height:18px;padding-top:6px;margin-left:10px}
    #editAppForm label span{font-size:15px;font-weight:bold}
    #editAppForm input{width:260px;height:22px;}
    #editAppForm textarea{height:60px;width:260px}
    #editAppForm .file{height:22px;width:240px;}
    #editAppForm .appIcon{text-align:center;width:22px;height:22px;float:left;border:1px solid #ddd;line-height:22px;overflow:hidden;margin-left:0px;margin-right:2px;padding:0px}
</style>
<form method="post" enctype="multipart/form-data" action="<?php echo $url?>" target="ifEditApp" id="editAppForm" onsubmit="return doEditApp(this)">
    <input type="hidden" name="group_name" value="<?php echo $appInfo['group_name'] ?>" />
    <div><label><span>应用标识：</span>应用程序组的英文标识，有开发组人员确定，不可修改<br />
            <input type="text"  disabled="true" value="<?php echo $appInfo['group_name']?>" />
        </label></div>
    
    <div><label><span>应用名称(<em>*</em>)：</span>应用程序组的中文名称<br />
            <input type="text" class="easyui-validatebox" required="true" name="group_caption" value="<?php echo $appInfo['group_caption']?>" />
        </label></div>
    <div><label><span>应用图标：</span>应用程序图标,宽高不超过100px,允许类型（*.png,*.jpg,*.gif）<br />
            <div class="appIcon">
            <?php if($appInfo['group_icon']):?>
            <img src="<?php echo $appInfo['group_icon']?>?<?php echo time()?>" width="18" height="18"/>    
            <?php endif?>
            </div>
            <input type="file" class="file" name="group_icon" />
        </label></div>
    <div><label><span>应用介绍：</span>应用程序文字介绍<br />
            <textarea name="group_desc"><?php echo $appInfo['group_desc']?></textarea>
        </label></div>
    <div><a href="javascript:void(0)" onclick="jQuery('#editAppForm').submit()" class="easyui-linkbutton" iconCls="icon-save" >保存应用信息</a></div>
</form>
<iframe name="ifEditApp" id="ifEditApp" style="display:none"></iframe>
<?php else:?>
    <script type="text/javascript">
    <?php if(false == $errorMsg):?>
        parent.reloadGrid();
    <?php else:?>
        alert('<?php echo $errorMsg?>');
    <?php endif?>
    </script>
<?php endif?>