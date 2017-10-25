<!doctype html>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<link href="/static/css/question.css" type="text/css" rel="stylesheet" />
</head>
<body style="padding: 5px;">
	<div id="basic-dict-layout" class="easyui-layout" data-options="border: false, fit: true">
		<div region="west" style="width: 300px;" data-options="title:'PPT图片管理', iconCls:'icon-table', split: true, minWidth: 800">
			<table id="easyui-datagrid" class="easyui-datagrid" data-options="url: '/Vip/VipTraining/powerPointInfo',
																	  iconCls: 'icon-table',
																	  striped: true,
                                                                      toolbar: '#easyui-datagrid-toolbar',
																	  border: false,
																	  fit: true,
																	  singleSelect: true,
																	  pagination: false,
																	  idfield: 'id',
																	  rownumbers: true,
																	  onSelect: basic_dict_select">
				<thead>
					<tr>
						<th field="id" width="50px">序号</th>						
                        <th field="pt_name" width="50px">PPT名称</th>
                        <th field="ppt_url_name" width="80px">上传PPT</th>
                        <th field="tr_name" width="100px">培训名称</th>
                        <th field="xueke_name" width="200px">学科</th>
                        <th field="recommended_name" width="80px">是否推送</th>
					</tr>
				</thead>
			</table>
		</div>
		<div region="center" data-options=" iconCls:'icon-table'">
			<table id="basic-dict-data-datagrid" >
			</table>
			
            <div id="easyui-datagrid-toolbar">
				<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-add'" onclick="javascript: add_dict('PEIXUN')">添加</a>
				<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-edit'" onclick="javascript: edit_dict()">编辑</a>
				<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-remove'" onclick="javascript: easyui_delete()">删除</a>
			</div>
		</div>
	</div>
    
    <!--Begin 基础属性添加/编辑对话框-->
<div id="basic-index-knowledge-add-dlg" class="easyui-dialog" data-options="modal:true,closed:true,buttons:'#basic-index-knowledge-add-dlg-buttons'" style="width:550px;height:235px;padding:5px;"></div>	

<!--div id="basic-index-knowledge-add-dlg-buttons">
	<a href="#"  class="easyui-linkbutton" iconCls="icon-ok" onclick="dict_save()">保存</a>
</div-->
<!--End 基础属性添加/编辑对话框-->


	<script language='javascript' type='text/javascript'>
		var id = '';
		function basic_dict_select(index, row) {
			id = row.id;
			$('#basic-dict-data-datagrid').datagrid({
				//url: '/Vip/VipTraining/getDictsByCategory?id=' + row.id
			}).datagrid('unselectAll');
		}
		var opts = {
            region: 'east',
            width: 350,
            collapsible: false,
            split: true,
            tools: [{
            		iconCls: 'panel-tool-close',
                  	handler: function(){
                      	$('#basic-dict-layout').layout('remove', 'east');
                    }}],
            minWidth: 350
        };
        
        
        
        
        //培训--添加-*
        function add_dict(type){
            var params = {
    			href: '/Vip/VipTraining/addPowerPoint',
    			iconCls: 'icon-add',
    			title: '添加'
    		};
    		if (type == 'PEIXUN') {
    			params.height = 500;
    		}		
    		$('#basic-index-knowledge-add-dlg').dialog(params).dialog('open');
    		//action = '/Vip/VipTraining/dict_add_people';
    		form = 'dict-add-form';
  	   }
        //培训--修改-*
       function edit_dict() {        
        var row = $('#easyui-datagrid').datagrid('getSelected');        
		var params = {
				height: 500,
				iconCls: 'icon-edit',
				title: '编辑'
			};	    
		params.href = '/Vip/VipTraining/addPowerPoint?id=' + row.id;
		$('#basic-index-knowledge-add-dlg').dialog(params).dialog('open');
		//action = '/Vip/VipTraining/dict_add_people';
		form = 'dict-edit-form';
        
	  }
      //培训--删除-*
      function easyui_delete() {
			var row = $('#easyui-datagrid').datagrid('getSelected');
			if (row) {
				$.messager.confirm('操作提示', '您确实要删除吗？', function (r) {
					if (r) {
						$.post('/Vip/VipTraining/delPowerPoint', { id: row.id }, function (result) {
							if (result.status) {
				                $('#easyui-datagrid').datagrid('reload').datagrid('unselectAll');
							} else {
								$.messager.alert('错误信息', '操作失败!', 'error');
							}
						}, 'json');
					}
				});
			} else {
				$.messager.alert('提示信息', '请选择要操作的数据!', 'info');
			}
		}
      
        //详情
        function xiangqing(id){
            var params = {
                height: 500,                
                href:'/Vip/VipTraining/testDetailedList?id='+id,
                iconCls: 'icon-add',
                title:'详情',
            };
            $('#basic-index-knowledge-add-dlg').dialog(params).dialog('open');
            form ='dict-add-form';
        }
       
        //-----------------------------
         function open_layout(opts) {
			$('#basic-dict-layout').layout('remove', 'east');
	        $('#basic-dict-layout').layout('add', opts);
		}
        
        function dict_save() {
    		$('#' + form).form('submit', {
    	        url: action,
    	        onSubmit: function () {
    	            return $(this).form('validate');
    	        },
    	        success: function (result) {
    	        	var result = JSON.parse(result);
    	            if (result.status) {
    	            	$('#basic-index-knowledge-add-dlg').dialog('close');
    	                _initGrade();
    	            } else {
    	                $.messager.alert('错误信息', '操作失败!', 'error');
    	            }
    	        }
    	    });
        }
       
	</script>
</body>
</html>