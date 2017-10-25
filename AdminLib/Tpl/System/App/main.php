<!doctype html>
<html>
    <head>
        <title><?php echo APP_NAME?></title>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <script type="text/javascript">
        function icon(val,data){
            if (jQuery.trim(data.group_icon).length >0) {
                var icon = data.group_icon +'?' + (new Date()).getTime();
            } else {
                var icon = '<?php echo $DEFAULT_GROUP_ICON?>';
            }
            return '<img width="18" height="18" src="' + icon + '" />';
        }
        <?php if($permValue & $PERM_WRITE):?>
        function manage(val,data) {
                return '<a onclick="editApp(this);return false" groupName="' + data.group_name + '" href="/System/App/editApp/app/' + data.group_name + '">修改应用信息</a>';
        }
        function editApp(link) {
            var groupName = jQuery(link).attr('groupName');
            var url = jQuery(link).attr('href');
            jQuery('body').layout('remove','east');
            jQuery('body').layout('add', {
                region:'east',
                width:400,
                title:'修改应用信息(' + groupName + ')',
                href:url,
                collapsible:false,
                tools:[{iconCls:'icon-cancel',
                        handler:function(){
                            jQuery('body').layout('remove','east');
                        }
                    }]
            });
            return false;
        }
        function doEditApp(form){
            var form=jQuery(form);
            submit = true;
            form.find('.easyui-validatebox').each(function(){
                if(false == jQuery(this).validatebox('isValid')) {
                    submit = false;
                }
            })
            return submit;
        }
        function reloadGrid(){
            jQuery('body').layout('remove', 'east');
            jQuery('#appGrid').datagrid('reload');
        }
        <?php endif?>
        jQuery(function(){
            jQuery('#appGrid').datagrid({
                view:detailview,
                detailFormatter:function(index,data){
                    return '<div class="group_desc group_desc_' + index + '">' + jQuery.trim(data.group_desc) + '</div>';
                },
                onExpandRow:function(index,data){
                    jQuery('#appGrid').datagrid('selectRow',index);
                    $('#appGrid').datagrid('fixDetailRowHeight',index); 
                    var rowCnt = jQuery('div.group_desc').length;
                    for(var i=0;i<rowCnt;i++) {
                        if(i != index) {
                            jQuery('#appGrid').datagrid('collapseRow',i);
                        }
                    }
                    
                }
            });
        })
        </script>
        <style type="text/css">
            .group_desc{padding:4px 2px;border:1px solid blue;margin:2px}
        </style>
    </head>
    <body class="easyui-layout">
        <div region="center">
        <table id="appGrid" fit="true" rownumbers="true" singleselect="true" fitcolumns="true" url="<?php echo $jsonUrl;?>" border="false" >
            <thead>
                <tr>
                    <th field="group_icon" width="20" formatter="icon">图标</th>
                    <th field="group_name" width="100">标识</th>
                    <th field="group_caption" width="200">名称</th>
                    <?php if($permValue & $PERM_WRITE):?><th field="edit_group" width="100" formatter="manage">管理</th><?php endif?>
                </tr>
            </thead>
        </table>
        </div>
    </body>
</html>