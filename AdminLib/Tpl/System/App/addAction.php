<?php if(false == $resultScript):?>
<style type="text/css">
    #addActionForm em{color:red}
    #addActionForm div{line-height:18px;padding-top:6px;margin-left:10px}
    #addActionForm span{font-size:15px;font-weight:bold}
    #addActionForm input{width:260px;height:22px;}
    #addActionForm textarea{height:60px;width:260px}
    #addActionForm .file{height:22px;width:240px;}
    #addActionForm .appIcon{text-align:center;width:22px;height:22px;float:left;border:1px solid #ddd;line-height:22px;overflow:hidden;margin-left:0px;margin-right:2px;padding:0px}
</style>
<form id="addActionForm" method="POST" action="<?php echo $url?>" enctype="multipart/form-data" target="ifActionFrame"  onsubmit="return doSaveAction(this)">
    <input type="hidden" name="addAction" value="1" />
    <div><label><span>功能标识：</span>功能英文标识，不可修改<br />
        <input type="hidden" name="module_key" value="<?php echo $moduleKey?>" />
        <?php echo $moduleKey?>-<input type="text"  name="action_name" style="width:50px" class="easyui-validatebox" required="true" value="" />
    </label></div>
    <div><label><span>功能名称：</span>功能名称<br />
        <input type="text" class="easyui-validatebox" name="acl_caption" required="true" value="" />
    </label></div>
    <input type="hidden" name="is_menu" value="1" />
    <div><label><span>功能URL：</span>访问路径（以/或http://开头）<br />
        <input type="text" class="easyui-validatebox" name="menu_url" required="true" value="" />
    </label></div>
    
    <div><label><span>功能图标：</span>功能图标(仅菜单功能需要),宽高不超过100px,允许类型（*.png,*.jpg,*.gif）<br />
            <input type="file" class="file" name="acl_icon" id="acl_icon" />
        </label></div>
    <div><span>授权值：</span>公开，只读，读写<br />
        <?php echo W('Radio', array('name'=>'acl_value', 
                                    'style'=>'width:auto',
                                    'class'=>'acl_value',
                                    'value'=>abs($actionInfo['acl_value']), 
                                    'items'=>array(0=>'公开', 1=>'读', 3=>'读写')));
        ?>
    </div>
    <div><label><span>功能介绍：</span>功能介绍内容<br />
        <textarea name="action_desc" id="action_desc"><?php echo $actionInfo['action_desc']?></textarea>
    </label></div>
    <div><a href="javascript:void(0)" onclick="jQuery('#addActionForm').submit()" class="easyui-linkbutton" iconCls="icon-save" >保存功能信息</a></div>
</form>
<iframe id="ifActionFrame" name="ifActionFrame" style="display:none"></iframe>
<?php else:?>
    <script type="text/javascript">
    <?php if(false == $errorMsg):?>
    
        parent.reloadGrid();
    <?php else:?>
        alert('<?php echo $errorMsg?>');
    <?php endif?>
    </script>
<?php endif?>