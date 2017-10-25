<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <link rel="stylesheet" type="text/css" href="/css/gs.670.css">
  
 		
        <script type="text/javascript">
        var tools = [{iconCls:'icon-cancel',
                      handler:function(){
                        jQuery('body').layout('remove', 'east');
                      }}]
        <?php if($permValue & $PERM_WRITE):?>
        /*
		function manage(value, data, index) {
            var op =  '<a class="easyui-linkbutton" href="javascript:showCardGroup(\'' + index + '\')">查xxxx看</a> | ';
			 
				op +=  '<a href="javascript:delPosition(\'' + index + '\',' + data + ')">删除卡组</a>';
			return op;
        }*/
        
        function add() {
            jQuery('body').layout('remove', 'east');
            jQuery('body').layout('add', {
                region:'east',
                title:'添加激活卡信息',
                width:400,
                split:true,
                border:true,
                iconCls:'icon-tip',
                collapsible:false,
                href:'<?php echo $addUrl?>',
                tools:tools
            })
        }
        
		function del(){
			var row = jQuery('#dataGrid').datagrid('getSelected');
			if(null == row){
				$.messager.alert('系统提示', '操作前请选择一条记录');
			}else{
				$.messager.confirm('系统提示', '确定要删除 此记录么', function(r){
					if (r){
						$.ajax({
							url : '<?php echo $delCardUrl;?>',
							type : 'POST',
							data: {id:row.gid},
							dataType : 'json',
							success: function(rs){
								
								if(rs.error){
									$.messager.alert('系统信息', rs.message);	
									
								}else{
									 reloadGrid();
								}
							},
							error:	function(a, b, c){
										$.messager.progress('close');
										$.messager.alert('系统提示','发送请求失败');
								   },
							beforeSend: function(){
										$.messager.progress({'title':'系统提示', 'msg': '', 'text':'处理中,请稍后...'});
								   },
							complete: function(){
									   $.messager.progress('close');
								   }
							
						});
					}
				});
			}
		}
		
		function view(){
			var row = jQuery('#dataGrid').datagrid('getSelected');
			if(null == row){
				$.messager.alert('系统提示', '请选择一条记录');
			}else{
				var title =  row.group_name + ' - 激活卡';
				jQuery('body').layout('remove', 'east');
				jQuery('body').layout('add', {
					region:'east',
					title:title,
					width:400,
					split:true,
					collapsible:false,
					href:'<?php echo $listCodeUrl?>?gid=' + row.gid,
					tools:tools
            	})
			}
		}
		
		
        function reloadGrid() {
            jQuery('body').layout('remove', 'east');
            jQuery('#dataGrid').datagrid('reload');
			$.messager.progress('close');
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
        
		
		function closeProgress(){
			$.messager.progress('close');
		}
		
		function showMessage(message){
			$.messager.alert('系统提示', message);
		}
		
        <?php endif?>
		
		function reloadEast(){
			$('body').layout('panel','east').panel('refresh'); 
 			jQuery('#dataGrid').datagrid('reload');
		}
		
        function showCardGroup(idx, data) {
             var groupCaption= data.group_name;
            <?php if($permValue & $PERM_WRITE):?>
            var title = '编辑卡组信息(' + groupCaption + ')';
            <?php else:?>
            var title = '查看卡组信息(' + groupCaption + ')'
            <?php endif?>
            jQuery('body').layout('remove', 'east');
            jQuery('body').layout('add', {
                region:'east',
                title:title,
                width:400,
				split:true,
                collapsible:false,
                href:'<?php echo $showUrl?>?gid=' + data.gid,
                tools:tools
            })
        }
		
		function pageCode(index, data){
			jQuery('body').layout('remove', 'east');
            jQuery('body').layout('add', {
                region:'east',
                title:data['group_name'],
                width:400,
                collapsible:false,
				split:true,
                href:'<?php echo $listCodeUrl?>/gid/' + data.gid,
                tools:tools
            })
		}
		
        jQuery(function(){
            jQuery('#dataGrid').datagrid({
                onSelect:showCardGroup
            })
        })
        </script>
    </head>
    <body class="easyui-layout" fit="true">
        <div region="center">
            <table id="dataGrid" url="<?php echo $jsonPageCardGroup?>" singleSelect="true" rownumbers="true" pagination="true" border="false" fit="true" toolbar="#toolbar" pageList="[20,30,40,50]">
                <thead>
                    <tr>
                    	<th field="cdate">添加时间</th>
                        <th field="group_name">卡组名称</th>
                        <th field="card_max_num">当前数量</th>
                        <th field="card_pre">卡号前缀</th>
                        <th field="card_length">卡号长度</th>
                        <th field="pass_length">密码长度</th>
      
                    </tr>
                </thead>
            </table>
            <?php if($permValue & $PERM_WRITE):?>
            <div id="toolbar">
                <a class="easyui-linkbutton" href="javascript:add()" plain="true" iconCls="icon-add">添加激活卡</a>
                <a class="easyui-linkbutton" href="javascript:del()" plain="true" iconCls="icon-cancel">删除</a>
                <a class="easyui-linkbutton" href="javascript:view()" plain="true" iconCls="icon-tip">查看</a>
                
            </div>
            <?php endif?>
        </div>
    </body>
</html>