<?php if(false == $resultScript):?>
<script type="text/javascript">
jQuery(function(){
    jQuery('#acl_icon').attr('disabled', jQuery('.isMenu:checked').val() == 0);
    jQuery('.isMenu').click(function(){
        jQuery('#acl_icon').attr('disabled', jQuery('.isMenu:checked').val() == 0);    
    })
})
</script>
<style type="text/css">
    #editActionForm em{color:red}
    #editActionForm div{line-height:18px;padding-top:6px;margin-left:10px}
    #editActionForm span{font-size:15px;font-weight:bold}
    #editActionForm input{width:260px;height:22px;}
    #editActionForm textarea{height:60px;width:260px}
    #editActionForm .file{height:22px;width:240px;}
    #editActionForm .appIcon{text-align:center;width:22px;height:22px;float:left;border:1px solid #ddd;line-height:22px;overflow:hidden;margin-left:0px;margin-right:2px;padding:0px}
</style>
<form id="editActionForm" method="POST" action="<?php echo $url?>" enctype="multipart/form-data" target="ifActionFrame"  onsubmit="return doSaveAction(this)">
    <input type="hidden" name="acl_key" value="<?php echo $actionInfo['acl_key'] ?>" />
    <div><label><span>功能标识：</span>功能英文标识，不可修改<br />
        <input type="text"  disabled="true" value="<?php echo $actionInfo['acl_key']?>" />
    </label></div>
    <div><label><span>功能名称：</span>功能名称<br />
        <input type="text" class="easyui-validatebox" name="acl_caption" required="true" value="<?php echo $actionInfo['acl_caption']?>" />
    </label></div>
    <?php if($actionInfo['menu_url']):?>
    <input type="hidden" name="is_menu" value="1" />
    <div><label><span>功能URL：</span>功能访问地址(以/或http://开头)<br />
        <input type="text" class="easyui-validatebox" name="menu_url" required="true" value="<?php echo $actionInfo['menu_url']?>" />
    </label></div>
    <?php else:?>
    <div><span>是否菜单：</span>是否为功能菜单<br />
        <?php echo W('Radio', array('name'=>'is_menu', 
                                    'style'=>'width:auto',
                                    'class'=>'isMenu',
                                    'value'=>abs($actionInfo['is_menu']), 
                                    'items'=>array(1=>'是', 0=>'否')));
        ?>
    </div>
    <?php endif?>
    <div><label><span>功能图标：</span>功能图标(仅菜单功能需要),宽高不超过100px,允许类型（*.png,*.jpg,*.gif）<br />
            <div class="appIcon">
            <?php if($actionInfo['acl_icon']):?>
            <img src="<?php echo $actionInfo['acl_icon']?>?<?php echo time()?>" width="18" height="18"/>    
            <?php endif?>
            </div>
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
    <div><a href="javascript:void(0)" onclick="jQuery('#editActionForm').submit()" class="easyui-linkbutton" iconCls="icon-save" >保存功能信息</a></div>
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