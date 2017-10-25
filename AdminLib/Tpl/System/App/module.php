<!doctype html>
<html>
    <head>
        <title><?php echo APP_NAME?></title>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <script type="text/javascript">
        function sorter(val, data) {
            if(data._parentId){
                return '<input class="sorter module_sorter" type="text" name="sort['+ data.module_key  +']" value="' + jQuery.trim(data.module_seq) + '" style="width:30px" />';
            }
            return '';
        }
        
        function icon(val,data) {
            if (jQuery.trim(data.module_icon).length >0) {
                var icon = data.module_icon +'?' + (new Date()).getTime();;
            } else {
                var icon = '<?php echo $DEFAULT_MODULE_ICON?>';
            }
            return '<img width="18" height="18" src="' + icon + '" />';
        }        
    <?php if($permValue & $PERM_WRITE):?>
        function manage(val,data) {
                if(data._parentId) {
                    return '<a onclick="editModule(this);return false" moduleKey="' + data.module_key + '" href="/System/App/editModule/module/' + data.module_key + '">修改模块信息</a>';
                }
                return '';
        }
        function editModule(link) {
            var moduleKey = jQuery(link).attr('moduleKey');
            var url = jQuery(link).attr('href');
            jQuery('body').layout('remove','east');
            jQuery('body').layout('add', {
                region:'east',
                width:400,
                title:'修改模块信息(' + moduleKey + ')',
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
        function doEditModule(form){
            var form=jQuery(form);
            submit = true;
            form.find('.easyui-validatebox').each(function(){
                if(false == jQuery(this).validatebox('isValid')) {
                    submit = false;
                }
            })
            return submit;
        }
        
        function sortModule(){
            var sortData = jQuery('input.module_sorter').serialize();
            jQuery.post('<?php echo $sortUrl?>', sortData, function(data){
                if(data == 1) {
                    reloadGrid();
                }
            }, 'json');
        }
        
        function reloadGrid(){
            jQuery('body').layout('remove', 'east');
            jQuery('#moduleGrid').treegrid('reload');
        }
        <?php endif?>
            
        jQuery(function(){
            var gridOptions = {
               treeField:'module_name',
               idField:'module_key',
               collapseAll:true
            };
            <?php if($permValue & $PERM_WRITE):?>

            gridOptions.toolbar = [{text:'修改模块排序', 
                         iconCls:'icon-save',
                         handler:sortModule}];
            <?php endif?>
                
            jQuery('#moduleGrid').treegrid(gridOptions);
        })
        </script>
        <style type="text/css">
            .group_desc{padding:4px 2px;border:1px solid blue;margin:2px}
        </style>
    </head>
    <body class="easyui-layout">
        <div region="center">
        <table id="moduleGrid"  rownumbers="true" fit="true" singleselect="true" fitcolumns="true" url="<?php echo $jsonUrl;?>"  border="false" >
            <thead>
                <tr>
                    <th field="module_name" width="100">标识</th>
                    <th field="module_seq" width="30" formatter="sorter">排序</th>
                    <th field="module_icon" align="center" width="30" formatter="icon">图标</th>
                    <th field="module_caption" width="200">名称</th>
                    <?php if($permValue & $PERM_WRITE):?><th field="edit_module" width="100" formatter="manage">管理</th><?php endif?>
                </tr>
            </thead>
        </table>
        </div>
    </body>
</html>