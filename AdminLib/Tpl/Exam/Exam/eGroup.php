<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <script type="text/javascript" src="/static/kindeditor/kindeditor-min.js"></script>
        <script type="text/javascript">
        <?php if($permValue & $PERM_WRITE):?>
        function addGroup() {
            jQuery('body').layout('remove', 'east');
            jQuery('body').layout('add', {
                region:'east',
                title:'添加竞赛组',
                iconCls:'icon-add',
                width:450,
                href:'<?php echo $addGroupUrl?>',
                collapsible:false,
                tools:[{
                    iconCls:'icon-cancel',
                    handler:function(){
                        jQuery('body').layout('remove', 'east');
                    }
                }]
            })
        }
        
        function delGroup(groupId, groupCaption) {
            if(confirm('确定要删除竞赛组“' + groupCaption + '”吗？')) {
                jQuery.post('<?php echo $delGroupUrl?>', {gid:groupId},function(data){
                    if(data == 1) {
                        alert('删除成功');
                        reloadGrid();
                    } else {
                        alert('删除失败');
                    }
                })
            }
        }
        
        <?php endif;?>
        function manage(val, data) {
            <?php if($permValue & $PERM_WRITE):?>
            var title="修改"
            <?php else:?>
            var title="查看"
            <?php endif?>
            var str = '<a href="javascript:groupInfo(\'' + data.group_id + '\', \'' + data.group_caption + '\')">' + title + '</a>';
            <?php if($permValue & $PERM_WRITE):?>
                if(data.cnt ==0) {
                    str += ' | <a href="javascript:delGroup(\'' + data.group_id + '\', \'' + data.group_caption + '\')">删除</a>';
                }
            <?php endif?>
            return str;
        }
        function reloadGrid() {
            jQuery('body').layout('remove', 'east');
            jQuery('#eGroupGrid').datagrid('reload');
        }
        
        function status(val, data) {
            return data.group_status == '1' ? '<b style="color:green">显示</b>' : '<b style="color:red">隐藏</b>';
        }
        
        function doSearch(){
            var keyword = jQuery('#keyword').val();
            jQuery('#eGroupGrid').datagrid({
                queryParams:{'keyword':keyword}
            })
        }
        
        function groupInfo(groupId, groupCaption) {
            <?php if($permValue & $PERM_WRITE):?>
            var title="修改竞赛组信息(" + groupCaption + ")"
            <?php else:?>
            var title="查看竞赛组信息(" + groupCaption + ")"
            <?php endif?>
            jQuery('body').layout('remove', 'east');
            jQuery('body').layout('add', {
                region:'east',
                title:title,
                width:500,
                icon:'icon-edit',
                href:'<?php echo $groupInfoUrl?>/gid/' + groupId,
                collapsible:false,
                tools:[{
                    iconCls:'icon-cancel',
                    handler:function(){
                        jQuery('body').layout('remove', 'east');
                    }
                }]
            })
        }
        </script>
    </head>
    <body class="easyui-layout" fit="true">
        <div region="center">
        <table class="easyui-datagrid" id="eGroupGrid" url="<?php echo $jsonGroupUrl?>" rownumbers="true" singleSelect="true" fit="true" pagination="true" toolbar="#toolbar" pageList="[20,30,40,50]">
            <thead>
                <tr>
                    <th field="group_id" sortable="true">组ID</th>
                    <th field="group_type" sortable="true">大类别</th>
                    <th field="group_caption">组名称</th>
                    <th field="group_status" formatter="status">组状态</th>
                    <th field="cnt">竞赛数量</th>
                    <th field="update_user">操作员</th>
                    <th field="update_at" sortable="true">操作时间</th>
                    <th field="manage" formatter="manage">信息维护</th>
                </tr>
            </thead>
        </table>
        
        <div id="toolbar">
        <?php if($permValue & $PERM_WRITE):?>
            <a href="javascript:addGroup()" class="easyui-linkbutton" plain="true" iconCls="icon-add">添加竞赛组</a> 
            <span class="datagrid-btn-separator"></span>&nbsp;
        <?php endif?> 
            查询:<input type="text" id="keyword" name="keyword" placeholder="请输入关键词" />
            <a href="javascript:doSearch()" class="easyui-linkbutton" iconCls="icon-search" plain="true">查询</a>
        </div>
        
        </div>
    </body>
</html>