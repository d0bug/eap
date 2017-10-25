<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?> 
        <script type="text/javascript">
        var curModule = '';
        function searchModule() {
            jQuery('#moduleGrid').datagrid({
                url:'<?php echo $jsonModuleUrl?>',
                queryParams:{subject:jQuery('#module_subject').val()},
                onSelect:function(idx,data){
                    curModule=data.module_code;
                    searchKnowledge();
                }
            })
        }
        
        function searchKnowledge() {
            jQuery('#knowledgeGrid').treegrid({
                url:'<?php echo $jsonKnowledgeUrl?>',
                idField:'knowledge_code',
                treeField:'knowledge_caption',
                queryParams:{subject:jQuery('#module_subject').val(), module:curModule, 'keyword':jQuery('#keyword').val()}
            })
            jQuery('#keyword').val('');
        }
        
        <?php if($permValue & $PERM_WRITE):?>
        function manage(val, data) {
            return '<a href="javascript:editKnowledge(\'' + data.knowledge_code + '\', \'' + data.knowledge_caption + '\')">修改</a> | <a href="javascript:delKnowledge(\'' + data.knowledge_id + '\', \'' + data.knowledge_caption + '\', \'' + data.sub_cnt + '\', \'' + data.parent_code + '\')">删除</a>';
        }
        function addKnowledge() {
            if(false == curModule) {
                alert('请选择知识模块');
                return 
            }
            jQuery('body').layout('remove', 'east');
            jQuery('body').layout('add', {
                region:'east',
                width:320,
                title:'添加知识点',
                href:'<?php echo $addKnowledgeUrl?>/module/' + curModule,
                iconCls:'icon-add',
                tools:[{
                    iconCls:'icon-cancel',
                    handler:function(){
                        jQuery('body').layout('remove', 'east');
                    }
                }],
                collapsible:false
            })
        }
        
        function editKnowledge(id, caption) {
            jQuery('body').layout('remove', 'east');
            jQuery('body').layout('add', {
                region:'east',
                width:320,
                title:'修改知识点信息(' + caption + ')',
                href:'<?php echo $knowledgeInfoUrl?>/id/' + id,
                iconCls:'icon-edit',
                tools:[{
                    iconCls:'icon-cancel',
                    handler:function(){
                        jQuery('body').layout('remove', 'east');
                    }
                }],
                collapsible:false
            })
        }
        
        function delKnowledge(id, caption, subCnt, parentCode) {
            var message = '确定要删除知识点“' + caption + '”';
            if(subCnt > 0) {
                message += '以及其子知识点吗？';
            } else {
                message += '吗？';
            }
            if(confirm(message)) {
                jQuery.post('<?php echo $delKnowledgeUrl?>', {id:id}, function(data){
                    if(data == 1) {
                        alert('知识点删除成功');
                        reloadKnowledge(parentCode);
                    }
                })
            }
        }
        
        function reloadKnowledge(knowledgeCode) {
            jQuery('body').layout('remove', 'east');
            jQuery('#knowledgeGrid').treegrid('reload', knowledgeCode);
        }
        <?php endif?>
        
        jQuery(function(){
            searchModule();
            searchKnowledge();
        })
        </script>
        <style type="text/css">
        .layout-panel-west .pagination-info{display:none}
        </style>
    </head>
    <body class="easyui-layout" fit="true">
        <div region="west" title="知识模块选择" style="width:320px" collapsible="false" split="true">
            <table id="moduleGrid" rownumbers="true" singleselect="true" pagination="true" toolbar="#moduleToolbar" fit="true" border="false">
                <thead>
                    <tr>
                        <th field="subject_caption" width="80">所属学科</th>
                        <th field="module_code">模块编码</th>
                        <th field="module_caption" width="140">模块名称</th>
                    </tr>
                </thead>
            </table>
            <div id="moduleToolbar">
                模块筛选：<?php echo W('ArraySelect', array('options'=>$subjectArray, 'attr'=>'name="module_subject" id="module_subject"'))?><a href="javascript:searchModule()" class="easyui-linkbutton" plain="true" iconCls="icon-search">确定</a>
            </div>
        </div>
        <div region="center">
            <table id="knowledgeGrid" rownumbers="true" singleselect="true" toolbar="#knowledgeToolbar" fit="true" border="false">
                <thead frozen="true">
                    <th field="knowledge_caption">知识点名称</th>
                    <th field="knowledge_code">知识点编码</th>
                </thead>
                <thead>
                    <tr>
                        <th field="subject_caption">所属学科</th>
                        <th field="module_caption">所属模块</th>
                        <th field="study_code">知识体系编码</th>
                        <th field="update_user">操作人</th>
                        <th field="update_at">操作时间</th>
                        <?php if($permValue & $PERM_WRITE):?>
                        <th field="manage" formatter="manage">管理</th>
                        <?php endif?>
                    </tr>
                </thead>
            </table>
            <div id="knowledgeToolbar">
                <?php if($permValue & $PERM_WRITE):?>
                <a href="javascript:addKnowledge()" class="easyui-linkbutton" iconCls="icon-add" plain="true">添加知识点</a>
                <span class="datagrid-btn-separator"></span>
                <?php endif?>
                快速查询：<input type="text" name="keyword" id="keyword" placeholder="关键词" />
                <a href="javascript:searchKnowledge()" class="easyui-linkbutton" iconCls="icon-search">搜索</a>
            </div>
        </div>
    </body>
</html>