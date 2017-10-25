<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <script type="text/javascript">
        var currModule = '';
        function moduleName(val,data) {
            if(data._parentId) {
                var icon = moduleText = '';
                if(data.module_icon) icon = '<img src="' + data.module_icon + '" width="16" height="16" align="absmiddle" />';
                if(data.module_caption) {
                    moduleText = '(' + data.module_name + ')&nbsp;' + data.module_caption;
                } else {
                    moduleText = data.module_name;
                }
                return icon + moduleText;
            } else {
                return data.module_name;
            }
        }
        
        function icon(icon, data) {
            if (jQuery.trim(icon).length >0) {
                var icon = icon +'?' + (new Date()).getTime();
            } else {
                var icon = '<?php echo $DEFAULT_ACTION_ICON?>';
            }
            return '<img width="18" height="18" src="' + icon + '" />';
        }
        
        function isMenu(val) {
            if(Math.abs(val) == 1) {
                return '<b>是</b>';
            }
            return '否';
        }
        
        function Permission(val) {
            val = Math.abs(val);
            val = isNaN(val) ? 0 : val;
            if(val == 0) return '<b style="color:green">公开</b>';
            if(val == 1) return '<b style="color:orange">读</b>';
            if(val == 3) return '<b style="color:red">读写</b>';
            return 'ERROR';
        }
        
        <?php if($permValue & $PERM_WRITE):?>
        function sorter(seq, data) {
            if(Math.abs(data.is_menu)) {
                return '<input type="input" name="menu_sort[' + data.acl_key + ']" class="sorter menu_sort" value="' + seq + '" />';
            }
            return '#';
        }
        
        function manage(aclKey, data) {
            if(jQuery.trim(data.menu_url)) {
                return '<a href="javascript:editAction(\'' + data.acl_key + '\')">修改</a> | <a href="javascript:deleteAction(\'' + data.acl_key + '\',\'' + data.acl_caption + '\')">删除</a>';
            }
            return '<a href="javascript:editAction(\'' + data.acl_key + '\')">修改</a>';
        }
        
        function addAction() {
            if(currModule) {
                jQuery('body').layout('remove', 'east');
                jQuery('body').layout('add', {
                    region:'east',
                    href:'<?php echo $addActionUrl?>/module/' + currModule,
                    width:300,
                    collapsible:false,
                    title:'添加外部功能',
                    tools:[{iconCls:'icon-cancel',
                            handler:function(){
                                jQuery('body').layout('remove','east');
                            }
                        }]
                })
            } else {
                alert('请选择相应模块');
            }
        }
        
        function editAction(aclKey) {
            jQuery('body').layout('remove', 'east');
            jQuery('body').layout('add', {
                region:'east',
                href:'<?php echo $editActionUrl?>/action/' + aclKey,
                width:300,
                collapsible:false,
                title:'修改功能信息(' + aclKey + ')',
                tools:[{iconCls:'icon-cancel',
                        handler:function(){
                            jQuery('body').layout('remove','east');
                        }
                    }]
            })
        }
        
        
        function deleteAction(aclKey, aclCaption) {
            if(confirm("确定要删除外部功能“" + aclCaption + "”吗？")) {
                jQuery.post('<?php echo $delActionUrl?>', {acl_key:aclKey}, function(data){
                    if(data == 1) {
                        alert('删除成功');
                        reloadGrid();
                    } else {
                        alert('删除失败');
                    }
                })
            }
        }
        
        function doSort() {
            if(currModule) {
                var sortData = jQuery('.menu_sort').serialize();
                jQuery.post('<?php echo $sortActionUrl?>/module/' + currModule, sortData, function(data){
                    if(data == 1) {
                        alert('排序成功');
                        reloadGrid();
                    }
                })
            } else {
                alert('请选择相应模块');
            }
        }
        
        function doSaveAction(form) {
            var form=jQuery(form);
            submit = true;
            form.find('.easyui-validatebox').each(function(){
                if(false == jQuery(this).validatebox('isValid')) {
                    submit = false;
                }
            })
            return submit;
        }
        
        function reloadGrid() {
            jQuery('body').layout('remove', 'east');
            jQuery('#actionGrid').datagrid('reload');
        }
        <?php endif;?>
        
        function showModuleAction(moduleKey) {
            currModule = moduleKey;
            jQuery('#actionGrid').datagrid({
                view:detailview,
                url:'<?php echo $jsonActionUrl?>/module/' + moduleKey,
                detailFormatter:function(index,data){
                    return '<div class="action_desc action_desc_' + index + '">' + jQuery.trim(data.action_desc) + '</div>';;
                },
                onExpandRow:function(index,data){
                    jQuery('#actionGrid').datagrid('selectRow',index);
                    $('#actionGrid').datagrid('fixDetailRowHeight',index); 
                    var rowCnt = jQuery('div.action_desc').length;
                    for(var i=0;i<rowCnt;i++) {
                        if(i != index) {
                            jQuery('#actionGrid').datagrid('collapseRow',i);
                        }
                    }
                    
                }
            })
        }
        
        jQuery(function(){
            jQuery('#moduleGrid').treegrid({
               treeField:'module_name',
               idField:'module_key',
               onSelect:function(data){
                if(data._parentId) {
                    showModuleAction(data.module_key);
                }
               }
            });
        })
        </script>
    </head>
    <body class="easyui-layout" fit="true">
        <div region="west" style="width:280px">
        <table id="moduleGrid"  rownumbers="true" fit="true" singleselect="true" fitcolumns="true" url="<?php echo $moduleUrl;?>"  border="false">
        <thead>
                <tr>
                    <th field="module_name" width="300" formatter="moduleName">选择模块</th>
                </tr>
            </thead>
        </table>
        </div>
        <div region="center">
            <table id="actionGrid" class="easyui-datagrid" rownumbers="true" fit="true" singleselect="true" fitcolumns="true"  border="false" toolbar="#toolbar">
                <thead>
                    <tr>
                        <?php if($permValue & $PERM_WRITE):?><th field="menu_seq" formatter="sorter" align="center">排序</th><?php endif;?>
                        <th field="acl_icon" formatter="icon" align="center">图标</th>
                        <th field="acl_key">功能标识</th>
                        <th field="acl_caption">功能名称</th>
                        <th field="is_menu" align="center" formatter="isMenu">是否菜单</th>
                        <th field="acl_value" align="center" formatter="Permission">授权项</th>
                        <?php if($permValue & $PERM_WRITE):?><th field="id" align="center" formatter="manage">信息维护</th><?php endif?>
                    </tr>
                </thead>
            </table>
        </div>
        <?php if($permValue & $PERM_WRITE):?>
        <div id="toolbar">
        <a href="javascript:doSort()" class="easyui-linkbutton" plain="true" icon="icon-save">排序</a>
        <span class="datagrid-btn-separator"></span>
        <a href="javascript:addAction()" id="addMenu" class="easyui-linkbutton" plain="true" icon="icon-add">添加外部功能</a>
        <span class="datagrid-btn-separator"></span>
        </div>
        <?php endif?>
    </body>
</html>