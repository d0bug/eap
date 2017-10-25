<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?> 
        <script type="text/javascript">
        var curSubject = '';
        jQuery(function(){
            jQuery('#sbjGrid').datagrid({
                url:'<?php echo $jsonSubjectUrl?>',
                onSelect:function(index, data){
                        curSubject = data.subject_code;
                        loadModules()
                }
            })
        })
        
        function loadModules() {
            jQuery('body').layout('remove', 'east');
            keyword = jQuery('#keyword').val();
            jQuery('#moduleGrid').datagrid({
                queryParams:{subject:curSubject, keyword:keyword}
            })
        }
        <?php if($permValue & $PERM_WRITE):?>
        var tools = [{iconCls:'icon-cancel',
                      handler:function(){
                        jQuery('body').layout('remove', 'east');
                      }
                    }]
        function addModule() {
            if(false == curSubject) {
                alert('请选择模块所属学科');
                return;
            }
            jQuery('body').layout('remove', 'east');
            jQuery('body').layout('add', {
                region:'east',
                title:'添加知识模块',
                iconCls:'icon-add',
                href:'<?php echo $addModuleUrl?>/sbj/' + curSubject,
                width:300,
                collapsible:false,
                tools:tools
            })
        }
        
        function editModule(moduleId,moduleCaption) {
            jQuery('body').layout('remove', 'east');
            jQuery('body').layout('add', {
                region:'east',
                title:'修改知识模块(' + moduleCaption + ')',
                iconCls:'icon-edit',
                href:'<?php echo $editModuleUrl?>/module/' + moduleId,
                width:300,
                collapsible:false,
                tools:tools
            })
        }
        
        function manage(val, data) {
            return '<a href="javascript:editModule(\'' + data.module_id + '\', \'' + data.module_caption + '\')">修改</a> '
               + '| <a href="javascript:delModule(\'' + data.module_id + '\', \'' + data.module_caption + '\')">删除</a>';
        }
        <?php endif?>
        </script>
    </head>
    <body class="easyui-layout" fit="true" border="false">
        <div region="west" style="width:200px" split="true">
            <table id="sbjGrid" rownumbers="true" singleselect="true" fit="true" border="false">
                <thead>
                    <tr>
                        <th field="short_code" width="50">编码</th>
                        <th field="subject_name" width="100">学科</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div region="center">
            <table id="moduleGrid" class="easyui-datagrid" fit="true" url="<?php echo $jsonModuleUrl?>" rownumbers="true" border="false" pagination="true" toolbar="#moduleToolbar">
                <thead>
                    <tr>
                        <th field="subject_caption">所属学科</th>
                        <th field="module_code">模块编码</th>
                        <th field="module_caption">模块名称</th>
                        <th field="update_user">操作员</th>
                        <th field="update_at">操作时间</th>
                        <?php if($permValue & $PERM_WRITE):?>
                        <th field="manage" formatter="manage">管理</th>
                        <?php endif;?>
                    </tr>
                </thead>
            </table>
            <div id="moduleToolbar">
            <?php if($permValue & $PERM_WRITE):?>
            <a href="javascript:addModule()" class="easyui-linkbutton" iconCls="icon-add" plain="true">添加模块</a>
            <span class="datagrid-btn-separator"></span>
            <?php endif?>&nbsp;
            快速查询：<input type="text" name="keyword" id="keyword" /><a href="javascript:loadModules()" id="searchBtn" class="easyui-linkbutton" iconCls="icon-search" plain="true">查询</a>
            </div>
        </div>
    </body>
</html>