<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <script type="text/javascript" src="http://api.map.baidu.com/api?v=1.4"></script>
        <script type="text/javascript" src="/static/kindeditor/kindeditor-min.js"></script>
        <script type="text/javascript">
        var tools = [{iconCls:'icon-cancel',
                      handler:function(){
                        jQuery('body').layout('remove', 'east');
                      }}]
        <?php if($permValue & $PERM_WRITE):?>
        function manage(value, data) {
            return '<a href="javascript:delPosition(\'' + data.pos_code + '\',\'' + jQuery.trim(data.pos_caption) + '\')">删除考点</a>'
        }
        
        function addPosition() {
            jQuery('body').layout('remove', 'east');
            jQuery('body').layout('add', {
                region:'east',
                title:'添加考点信息',
                width:400,
                collapsible:false,
                href:'<?php echo $addPosUrl?>',
                tools:tools
            })
        }
        
        function reloadGrid() {
            jQuery('body').layout('remove', 'east');
            jQuery('#posGrid').datagrid('reload');
        }
        
        function delPosition(posCode, posCaption) {
            if(confirm('确定要删除考点“' + posCaption + '”吗？')) {
                jQuery.post('<?php echo $delPosUrl?>', {pos:posCode}, function(data){
                    alert('考点删除成功');
                    reloadGrid();
                }, 'json');
            }
        }
        function savePosition(form) {
            keEditors.pos_bus.sync();
            jQuery('#' + form).submit();
        }
        
        <?php endif?>
        
        function positionInfo(idx, data) {
            posCode= data.pos_code;
            <?php if($permValue & $PERM_WRITE):?>
            var title = '修改考点信息(' + posCode + ')';
            <?php else:?>
            var title = '查看考点信息(' + posCode + ')'
            <?php endif?>
            jQuery('body').layout('remove', 'east');
            jQuery('body').layout('add', {
                region:'east',
                title:title,
                width:400,
                collapsible:false,
                href:'<?php echo $posInfoUrl?>/pos/' + posCode,
                tools:tools
            })
        }
        jQuery(function(){
            jQuery('#posGrid').datagrid({
                onSelect:positionInfo
            })
        })
        </script>
    </head>
    <body class="easyui-layout" fit="true">
        <div region="center">
            <table id="posGrid" url="<?php echo $jsonPosUrl?>" singleSelect="true" rownumbers="true" pagination="true" border="false" fit="true" toolbar="#toolbar" pageList="[20,30,40,50]">
                <thead>
                    <tr>
                        <th field="pos_code">考点标识</th>
                        <th field="pos_caption">考点名称</th>
                        <th field="pos_addr">考点地址</th>
                        <th field="pos_telephone">联系电话</th>
                        <th field="update_user">操作员</th>
                        <th field="update_at">操作时间</th>
                        <?php if($permValue & $PERM_WRITE):?>
                        <th field="manage" formatter="manage">管理</th>
                        <?php endif?>
                    </tr>
                </thead>
            </table>
            <?php if($permValue & $PERM_WRITE):?>
            <div id="toolbar">
                <a class="easyui-linkbutton" href="javascript:addPosition()" plain="true" iconCls="icon-add">添加考点</a>
            </div>
            <?php endif?>
        </div>
    </body>
</html>