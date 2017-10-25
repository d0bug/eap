<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
        <script type="text/javascript">
        var curGroup = 0;
        jQuery(function(){
        	jQuery('#groupGrid').datagrid({
        		url:'<?php echo $groupUrl?>',
        		onSelect:function(idx,data){
        			curGroup = data.group_id;
        			loadStudents();
        		}
        	})
        })
        function loadStudents() {
        	if(curGroup) {
        		initForm();
	        	searchClass();
	        	searchStudents();
        	} else {
        		alert('请选择试听组');
        	}
        }
        
        function initForm() {
        	jQuery('#area').html('<option value="0">=选择校区=</option>');
        	jQuery('#xueke').html('<option value="0">=选择学科=</option>');
        	jQuery.post('<?php echo $formInfoUrl?>', {gid:curGroup}, function(data){
        		jQuery.each(data.subjects, function(k, xueke){
        			jQuery('#xueke').html(jQuery('#xueke').html() + '<option value="' + xueke.id + '">' + xueke.sname + '</option>');
        		})
        		jQuery.each(data.areas, function(k, area){
        			jQuery('#area').html(jQuery('#area').html() + '<option value="' + area.sareacode + '">' + area.sprintarea + '</option>');
        		})
        	}, 'json');
        }
        
        function searchClass() {
        	if(curGroup){
        		var searchArgs = {gid:curGroup}
        		jQuery('#classGrid').datagrid({
        			url:'<?php echo $classUrl?>',
        			queryParams:searchArgs,
        			onSelect:function(idx,data){
        				searchStudents();
        			}
        		})
        	} else {
        		jQuery('#classGrid').datagrid();
        	}
        }
        
        function searchStudents() {
        	if(curGroup) {
        		var searchArgs = {gid:curGroup}
        		var seleClass = jQuery('#classGrid').datagrid('getSelected');
        		if(seleClass) {
        			searchArgs['class_code'] = seleClass.sclasscode;
        		}
        		searchArgs['keyword'] = jQuery.trim(jQuery('#stuKeyword').val());
        		jQuery('#stuGrid').datagrid({
        			url:'<?php echo $stuUrl?>',
        			queryParams:searchArgs
        		})
        	} else {
        		jQuery('#stuGrid').datagrid();
        	}
        }
        </script>
    </head>
    <body class="easyui-layout" fit="true" border="false">
    	<div region="west" style="width:230px" iconCls="icon-redo" collapsible="false">
    		<table id="groupGrid" rownumbers="true" singleselect="true" fit="true" border="false" iconCls="icon-redo" title="选择试听组">
    			<thead>
    				<tr>
    					<th field="group_title">试听组标题</th>
    					<th field="min_lesson" align="center">最小课节</th>
    					<th field="max_lesson" align="center">最大课节</th>
    				</tr>
    			</thead>
    		</table>
    	</div>
    	<div region="center" iconCls="icon-redo" >
    		<div id="searchClsForm" style="background:#eee">
    			<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search" plain="true">班级查询</a>
    			<span><select name="xueke" id="xueke"><option value="0">=选择学科=</option></select></span>
				<span><select name="area" id="area"><option value="0">=上课地点=</option></select></span>
				<span>上课日期：<input type="text" class="easyui-datebox" size="12" /></span>
				<br /><span style="margin-left:26px">关 键 词：</span><input type="text" id="classKeyword" size="50" style="margin-top:2px;border:1px solid #ccc;padding:2px"  />
				<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search" plain="true">查询</a>
    		</div>
    		<table id="classGrid" class="easyui-datagrid" singleselect="true" pagination="true" rownumbers="true" fit="true" border="false" toolbar="#searchClsForm">
    			<thead>
    				<tr>
    					<th field="sclasscode">班级编码</th>
    					<th field="sclassname">班级名称</th>
    					<th field="sprintarea">上课地点</th>
    					<th field="sprinttime">上课时间</th>
    					<th field="sprintteachers">授课教师</th>
    				</tr>
    			</thead>
    		</table>
    	</div>
    	
    	<div region="east" style="width:450px" iconCls="icon-redo">
    		<?php if($permValue & PERM_WRITE):?>
    		<div id="stuToolbar">
    		<a href="javascript:void(0)" id="addStudent" class="easyui-linkbutton" plain="true" iconCls="icon-add">添加学员</a>
    		<a href="javascript:void(0)" id="delStudent" class="easyui-linkbutton" plain="true" iconCls="icon-cancel">删除学员</a>
    		<span class="datagrid-btn-separator"></span>
    		搜索:<input type="text" id="stuKeyword" />
    		<a href="javascript:void(0)" id="searchBtn" class="easyui-linkbutton" plain="true" iconCls="icon-search">查询</a>
    		</div>
    		<?php endif?>
    		<table id="stuGrid" class="easyui-datagrid" fit="true" pagination="true" rownumbers="true" border="false" toolbar="#stuToolbar" title="预约学生名单" iconCls="icon-redo">
    			<thead>
    				<tr>
    					<th field="stu_name">学生姓名</th>
    					<th field="stu_mobile">联系电话</th>
    				</tr>
    			</thead>
    		</table>
    	</div>
    </body>
</html>