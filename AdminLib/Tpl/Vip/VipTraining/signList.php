<!doctype html>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<script type="text/javascript" src="/static/js/DatePicker/WdatePicker.js"></script>
<link href="/static/css/question.css" type="text/css" rel="stylesheet" />
</head>
<body style="padding: 5px;">
	<div id="basic-dict-layout" class="easyui-layout" data-options="border: false, fit: true">
		<!--div region="west" style="width: 300px;" data-options="title:'签到管理', iconCls:'icon-table', split: true, minWidth: 500">
			<table id="easyui-datagrid" class="easyui-datagrid" data-options="url: '/Vip/VipTraining/signArrangingInfo',
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
						<th field="id" width="20px">序号</th>
						<th field="tr_name" width="150px">培训期名称</th>
                        <th field="ar_name" width="250px">课程名称</th>
                        <th field="tr_time" width="50px">考试时间</th>
                        <th field="zongrenshu" width="50px">总人数</th>
					</tr>
				</thead>
			</table>
		</div-->
        <div region="west" style="width: 300px;" data-options="title:'签到管理', iconCls:'icon-table', split: true, minWidth: 500">
			<table id="easyui-datagrid" class="easyui-datagrid" data-options="url: '/Vip/VipTraining/peopleList',
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
						<th field="id" width="40px">序号</th>
						<th field="tr_name" width="250px">培训名称</th>
                        <th field="tr_time" width="250px">培训时间</th>
					</tr>
				</thead>
			</table>
		</div>
		<div region="center" data-options="title:'签到人员', iconCls:'icon-table'">
			
            <!--table id="basic-dict-data-datagrid" class="easyui-datagrid" data-options="
																	  method: 'get',
																	  iconCls: 'icon-table',
																	  striped: true,
																	  toolbar: '#basic-dict-data-datagrid-toolbar',
																	  border: false,
																	  fit: true,
																	  singleSelect: true,
																	  pagination: true,
																	  idField: 'id',
																	  rownumbers: true">
				<thead>
					<tr>						
						<th field="tr_name" width="150">培训名称</th>
						<th field="te_name" width="80">姓名</th>
                        <th field="recommended_name" width="100">状态</th>
                        <th field="create_date" width="150">日期</th>
                        <th field="create_time" width="150">时间</th>
					</tr>
				</thead>
			</table-->
             <table id="basic-dict-data-datagrid" class="easyui-datagrid" data-options="
																	  method: 'get',
																	  iconCls: 'icon-table',
																	  striped: true,
																	  toolbar: '#basic-dict-data-datagrid-toolbar',
																	  border: false,
																	  fit: true,
																	  singleSelect: true,
																	  pagination: true,
																	  idField: 'id',
																	  rownumbers: true">
				<thead>
					<tr>
                        <th field="tr_name" width="200">培训名称</th>						
						<th field="ar_name" width="150">课程名称</th>
						<th field="te_name" width="80">姓名</th>
                        <th field="recommended_name" width="100">状态</th>
                        <th field="create_date" width="150">签到日期</th>
                        <th field="create_time" width="150">签到时间</th>
                        <th field="shangke" width="200">上课时间</th>
                        <th  width="20"></th>
					</tr>
				</thead>
			</table>
            
			<div id="basic-dict-data-datagrid-toolbar">				
				<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-remove'" onclick="javascript: basic_export_sign_list()">导出excel表</a>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			</div>
            <div id="easyui-datagrid-toolbar">
                课程名称：<input type="text" id="kename" name="kename" value="" /> &nbsp;&nbsp;&nbsp;老师姓名：<input type="text" id="tename" name="tename" value="" /> &nbsp;&nbsp;&nbsp;<br/>课程日期：<input type="text" id="tetime" name="tetime" size="24" onClick='WdatePicker({dateFmt:"yyyy-MM-dd"});' class="Wdate" onfocus="javascript:this.blur()"/>
                <span style="color: blue;">输入搜索条件后点击培训名称即可查询</span>
            </div>
            
            <!--div id="easyui-datagrid-toolbar">
				<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-add'" onclick="javascript: add_dict('PEIXUN')">添加</a>
				<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-edit'" onclick="javascript: edit_dict()">编辑</a>
				<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-remove'" onclick="javascript: easyui_delete()">删除</a>
			</div-->
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
		  var kename = document.getElementById("kename").value;
		  var tename = document.getElementById("tename").value;
          var tetime = document.getElementById("tetime").value;
          ser=''; 
          if(kename){
             ser +=  '&kename='+kename;
          }
          if(tename){
             ser +=  '&tename='+tename;
          } 
          if(tetime){
             ser +=  '&tetime='+tetime;
          }        
			id = row.id;
			$('#basic-dict-data-datagrid').datagrid({
				url: '/Vip/VipTraining/getTeachSignList?id=' + row.id + ser
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
        //签到-导出
        function basic_export_sign_list(){
            var row = $('#easyui-datagrid').datagrid('getSelected');            
			if(row == null){
			 alert('请选择培训期！');exit;
			}
            window.location.href='/Vip/VipTraining/exportSignListExcel?id=' + row.id;
        }
       
	</script>
</body>
</html>