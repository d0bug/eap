<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
 
        <script type="text/javascript">
        var tools = [{iconCls:'icon-cancel',
                      handler:function(){
                        jQuery('body').layout('remove', 'east');
                      }}]
        <?php if($permValue & $PERM_WRITE):?>
        function manage(value, data, index) {
            var op =  '<a href="javascript:showCardGroup(\'' + index + '\',' + data + ')">查看</a> | ';
				op +=  '<a href="javascript:showCardGroup(\'' + index + '\',' + data + ')">查看</a> | ';
				op +=  '<a href="javascript:delPosition(\'' + index + '\',' + data + ')">删除卡组</a>';
			return op;
        }
        
      
        function reloadGrid() {
            jQuery('body').layout('remove', 'east');
            jQuery('#posGrid').datagrid('reload');
        }
        
        <?php endif?>
        
        function showCardGroup(idx, data) {
            var groupCaption= data.group_name;
            <?php if($permValue & $PERM_WRITE):?>
            var title = '修改考点信息(' + groupCaption + ')';
            <?php else:?>
            var title = '查看考点信息(' + groupCaption + ')'
            <?php endif?>
            jQuery('body').layout('remove', 'east');
            jQuery('body').layout('add', {
                region:'east',
                title:title,
                width:400,
                collapsible:false,
                href:'<?php echo $showUrl?>/gid/' + data.gid,
                tools:tools
            })
        }
		
		function pageCode(index, data){
			jQuery('body').layout('remove', 'east');
            jQuery('body').layout('add', {
                region:'east',
                title:title,
                width:400,
                collapsible:false,
                href:'<?php echo $pageCodeUrl?>/gid/' + data.gid,
                tools:tools
            })
		}
		
        jQuery(function(){
            jQuery('#dataGrid').datagrid({
                 
            })
        })
        </script>
    </head>
    <body class="easyui-layout" fit="true">
        <div region="center">
            <table id="dataGrid" url="<?php echo $jsonCodeUrl?>" singleSelect="true" rownumbers="true" pagination="true" border="false" fit="true" toolbar="#toolbar" pageList="[20,30,40,50]">
                <thead>
                    <tr>
                    	<th field="cdate">添加时间</th>
                        <th field="group_name">卡组名称</th>
                        <th field="card_max_num">当前数量</th>
                        <th field="card_pre">卡号前缀</th>
                        <th field="card_length">卡号长度</th>
                        <th field="pass_length">密码长度</th>
                        
                        <?php if($permValue & $PERM_WRITE):?>
                        <th field="manage" formatter="manage">管理</th>
                        <?php endif?>
                    </tr>
                </thead>
            </table>
            <?php if($permValue & $PERM_WRITE):?>
            <div id="toolbar">
                <a class="easyui-linkbutton" href="javascript:add()" plain="true" iconCls="icon-add">添加激活卡</a>
                <a class="easyui-linkbutton" href="javascript:appendCode()" plain="true" iconCls="icon-add">追加卡号</a>
                <a class="easyui-linkbutton" href="javascript:exportCode()" plain="true" iconCls="icon-add">导出卡号</a>
                
            </div>
            <?php endif?>
        </div>
    </body>
</html>