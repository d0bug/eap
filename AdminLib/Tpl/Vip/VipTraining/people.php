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
		<div region="west" style="width: 300px;" data-options="title:'培训', iconCls:'icon-table', split: true, minWidth: 500">
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
						<th field="id" width="20px">序号</th>
						<th field="tr_name" width="50px">名称</th>
                        <th field="tr_time" width="250px">培训时间</th>
                        <th field="tr_audit_num" width="50px">考试次数</th>
                        <th field="zongrenshu" width="50px">总人数</th>
                        <th field="tongguo" width="50px">通过人数</th>
					</tr>
				</thead>
			</table>
		</div>
		<div region="center" data-options="title:'人员', iconCls:'icon-table'">
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
						<th field="te_name" width="50">名称</th>
						<th field="sex_name" width="50">性别</th>
                        <th field="birthday" width="100">生日</th>
                        <th field="school" width="150">毕业学校</th>
                        <th field="professional" width="150">专业</th>
                        <th field="level_school" width="150">最高学历</th>
                        <th field="graduation" width="150">毕业年份</th>
                        <th field="phone" width="150">电话</th>
                        <th field="mail" width="150">邮箱</th>
						<th field="xueke_name" width="150">学科</th>
                        <th field="formal_name" width="80">性质</th>
                        <th field="through_name" width="80">是否通过</th>
                        <th field="status_name" width="80">关闭账号</th>
                        <th field='kaoping' width="80">考评记录</th>
                        <th field='xiangqing' width="80">详情</th>
                        <th field='daochu' width="80">导出老师信息</th>
					</tr>
				</thead>
			</table>
			<div id="basic-dict-data-datagrid-toolbar">
				<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-add'" onclick="javascript: basic_dict_add()">添加</a>
				<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-edit'" onclick="javascript: basic_dict_edit()">编辑</a>
                <a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-edit'" onclick="javascript: basic_all_kaoping()">批量评语</a>
				<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-remove'" onclick="javascript: basic_dict_delete()">删除</a>
                <a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-remove'" onclick="javascript: basic_export_teach_list()">导出</a>


            </div>
            <div id="easyui-datagrid-toolbar">
				<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-add'" onclick="javascript: add_dict('PEIXUN')">添加</a>
				<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-edit'" onclick="javascript: edit_dict()">编辑</a>                
				<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-remove'" onclick="javascript: easyui_delete()">删除</a>
                <a href="/Vip/VipTraining/closeNumber" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-remove'" >关闭账号</a>


                <?php
                if(!empty($dictInfo)){
                    ?>
                    科目:<select name="xueke_name" id="xueke_name">
                        <option value="">请选择</option>
                        <?php

                        foreach ($dictInfo as $k=>$module){
                            $id = $module['id'];
                            $name = $module['nianji'].$module['title'];
                            ?>
                            <option value="<?php echo $id?>" onclick="getxuekeid()" style="width: 50px;"><?php echo $name;?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <?php
                }
                ?>



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
        function getxuekeid(){
            var xuekeid = document.getElementById('xueke_name').value;
            return xuekeid;
        }
		function basic_dict_select(index, row) {
            xuekeid = getxuekeid();
			id = row.id;
			$('#basic-dict-data-datagrid').datagrid({
				url: '/Vip/VipTraining/getDictsByCategory?id=' + row.id +'&xkid=' + xuekeid
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
            minWidth: 350,

        };

        
        //老师--考评记录
        function kaoping(id){
            //alert(id);exit;
             var params = {
                height: 500,
    			href: '/Vip/VipTraining/addKaoPing?id='+id,
    			iconCls: 'icon-add',
    			title: '考评编辑',
                
    		};
    				
    		$('#basic-index-knowledge-add-dlg').dialog(params).dialog('open');
    		//action = '/Vip/VipTraining/dict_add_people';
    		form = 'dict-add-form';
        }
        
        //老师-导出列表
        function export_teach(id){
            window.location.href='/Vip/VipTraining/exportTeachListWord?id=' + id;
            
        }
        
        //老师--详情
        function xiangqing(id){
            var params = {
                height: 400,            
                href:'/Vip/VipTraining/detailsList?id='+id,
                iconCls: 'icon-add',
                title:'详情',
            };
            $('#basic-index-knowledge-add-dlg').dialog(params).dialog('open');
            form ='dict-add-form';
        }
        
        
        //培训--添加-*
        function add_dict(type){
            var params = {
    			href: '/Vip/VipTraining/addPeople',
    			iconCls: 'icon-add',
    			title: '添加'
    		};
    		if (type == 'PEIXUN') {
    			params.height = 400;
    		}		
    		$('#basic-index-knowledge-add-dlg').dialog(params).dialog('open');
    		//action = '/Vip/VipTraining/dict_add_people';
    		form = 'dict-add-form';
  	   }
        //培训--修改-*
       function edit_dict() {        
        var row = $('#easyui-datagrid').datagrid('getSelected');
        
		var params = {
				height: 400,
				iconCls: 'icon-edit',
				title: '编辑'
			};	    
		params.href = '/Vip/VipTraining/addPeople?id=' + row.id;
		$('#basic-index-knowledge-add-dlg').dialog(params).dialog('open');
		//action = '/Vip/VipTraining/dict_add_people';
		form = 'dict-edit-form';
        
	  }
      //培训--删除-*
      function easyui_delete() {          
			var row = $('#easyui-datagrid').datagrid('getSelected');
			if (row) {
				$.messager.confirm('操作提示', '您确实要删除此培训期吗？', function (r) {
					if (r) {
						$.post('/Vip/VipTraining/dict_delete', { id: row.id }, function (result) {
                            if(result.info == 'number'){
                                $.messager.alert('错误信息', '请先删除本培训期内所有老师信息!', 'error');
                                return false;
                            } else if (result.status) {
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
      
      
        //培训-老师-添加
		function basic_dict_add() {		
            var row = $('#easyui-datagrid').datagrid('getSelected');  
            if(row == null){
			 alert('请选择培训期！');exit;
			}      
    		var params = {
    				height: 500,
    				iconCls: 'icon-edit',
    				title: '添加'
    			};	    
    		params.href = '/Vip/VipTraining/addTeach?id=' + row.id;
    		$('#basic-index-knowledge-add-dlg').dialog(params).dialog('open');
    		//action = '/Vip/VipTraining/dict_add_people';
    		form = 'dict-edit-form';
        
		}
        
        //培训-老师-修改
		function basic_dict_edit() {
			var row = $('#basic-dict-data-datagrid').datagrid('getSelected');
			if(row == null){
			 alert('请选择要编辑的人员！');exit;
			}
            var params = {
    				height: 500,
    				iconCls: 'icon-edit',
    				title: '编辑'
    			};	    
    		params.href = '/Vip/VipTraining/editTeach?id=' + row.id;
    		$('#basic-index-knowledge-add-dlg').dialog(params).dialog('open');
    		form = 'dict-edit-form';
	    }
	    //培训-老师-删除
		function basic_dict_delete() {
		    var rowea = $('#easyui-datagrid').datagrid('getSelected');
			var row = $('#basic-dict-data-datagrid').datagrid('getSelected');
            if(rowea == null){
			 alert('请选择要删除的人员！');exit;
			}
			if (row) {
				$.messager.confirm('操作提示', '您确实要删除吗？', function (r) {
					if (r) {
						$.post('/Vip/VipTraining/delTeach', { id: row.id,trid:rowea.id }, function (result) {
							if (result.status) {
				                $('#basic-dict-data-datagrid').datagrid('reload').datagrid('unselectAll');
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
        //老师-批量评语
        function basic_all_kaoping(){
            var row = $('#easyui-datagrid').datagrid('getSelected');            
			if(row == null){
			 alert('请选择培训期！');exit;
			}
            var params = {
    				height: 400,
    				iconCls: 'icon-edit',
    				title: '批量评语'
    			};	    
    		params.href = '/Vip/VipTraining/addTraKaoping?id=' + row.id;
    		$('#basic-index-knowledge-add-dlg').dialog(params).dialog('open');
    		form = 'dict-edit-form'; 
        }
        //老师-导出列表
        function basic_export_teach_list(){
            var row = $('#easyui-datagrid').datagrid('getSelected');            
			if(row == null){
			 alert('请选择要导出的老师！');exit;
			}
            window.location.href='/Vip/VipTraining/exportTeachListExcel?id=' + row.id;
            
        }

        //老师-筛选
        function basic_dict_info(){
            var row = $('#easyui-datagrid').datagrid('getSelected');
            if(row == null){
                alert('请选择培训期！');exit;
            }
            var params = {
                height: 500,
                iconCls: 'icon-edit',
                title: '添加'
            };
            params.href = '/Vip/VipTraining/searchDict?id=' + row.id;

            //$('#basic-dict-data-datagrid').datagrid({
              //  url: '/Vip/VipTraining/searchDict?id=' + row.id;
            //}).datagrid('unselectAll');

            $('#basic-index-knowledge-add-dlg').dialog(params).dialog('open');

        }
        //老师--关闭多个账号
        /*
        function basic_close_number(){
            var row = $('#easyui-datagrid').datagrid('getSelected');
            if(row == null){
                alert('请选择培训期！');exit;
            }
            var params = {
                height: 400,
                iconCls: 'icon-edit',
                title: '关闭多个账号'
            };
            //window.location='/Vip/VipTraining/closeNumber?id=' + row.id;'
            params.href = '/Vip/VipTraining/closeNumber?id=' + row.id;
           $('#basic-index-knowledge-add-dlg').dialog(params).dialog('open');
            form = 'dict-edit-form';

        }*/
        
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
        /*function easyui_add(){
            opts.href = '/Vip/VipTraining/addPeople';
			opts.title = '添加';
			opts.iconCls = 'icon-add';
			open_layout(opts);
        }*/
	</script>
</body>
</html>