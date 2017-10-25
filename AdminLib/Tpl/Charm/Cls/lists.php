<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
        <script type="text/javascript">
        var curGroup = 0;
        var tools = [{iconCls:'icon-cancel', handler:function(){
        	jQuery('body').layout('remove', 'east');
        }}];
        jQuery(function(){
        	jQuery('#addGroup,#editGroup').click(function(){
        		var _tm = (new Date()).getTime();
        		jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
        		if(jQuery(this).attr('id') == 'addGroup') {
        			var title = "添加试听组";
        			var iconCls = "icon-add";
        			var url = '<?php echo $addGroupUrl?>/dlg/dlg_' + _tm;
        		} else {
        			var title = "修改试听组";
        			var iconCls = "icon-edit";
        			var seleGroup = jQuery('#groupGrid').datagrid('getSelected');
        			if(!seleGroup) {
        				alert('请选择试听组');
        				return;
        			}
        			var groupId = seleGroup.group_id;
        			var url = '<?php echo $editGroupUrl?>/gid/' + groupId + '/dlg/dlg_' + _tm;
        		}
        		jQuery('#dlg_' + _tm).dialog({
        			title:title,
        			href:url,
        			iconCls:iconCls,
        			height:200,
        			width:400,
        			modal:true,
        			onClose:function(){
        				jQuery('#dlg_' + _tm).dialog('destroy');
        			}
        		})
        	})
        	
        	jQuery('#delGroup').click(function(){
        		var seleGroup = jQuery('#groupGrid').datagrid('getSelected');
    			if(seleGroup) {
    				var groupId = seleGroup.group_id;
    				if(confirm('确定要删除选定试听组吗?')) {
	    				jQuery.post('<?php echo $delGroupUrl?>', {gid:groupId}, function(data){
	    					if(data.errorMsg) {
	    						alert(data.errorMsg);
	    					} else {
	    						alert('试听组删除成功');
	    						curGroup = 0;
	    						jQuery('#groupGrid').datagrid('reload');
	    					}
	    				}, 'json');
    				}
    			}
        	})
        	
        	jQuery('#groupGrid').datagrid({
        		onSelect:function(idx,data){
        			var groupId = data.group_id;
        			curGroup = groupId;
        			loadClass(groupId);
        		}
        	})
        	
        	jQuery('#addClass').click(function(){
        		if(curGroup>0) {
	        		jQuery('body').layout('remove', 'east');
	        		jQuery('body').layout('add', {
	        			title:'添加试听班级',
	        			region:'east',
	        			iconCls:'icon-add',
	        			href:'<?php echo $addClassUrl?>',
	        			width:370,
	        			collapsible:false,
	        			tools:tools
	        		})
        		} else {
        			alert('请选择试听组');
        		}
        	})
        	
        	jQuery('#delClass').click(function(){
        		var seleClasses = jQuery('#classGrid').datagrid('getSelections');
        		if(seleClasses.length == 0) {
        			alert('请选择要删除的试听班级');
        			return;
        		}
        		if(false == confirm('确定要删除选定班级吗？')) {
        			return;
        		}
        		var clsCodes = [];
        		jQuery.each(seleClasses, function(k, cls) {
        			clsCodes.push(cls.sclasscode)
        		})
        		clsCodes = clsCodes.join(',');
        		jQuery.post('<?php echo $delClassUrl?>', {clsCode:clsCodes}, function(data){
        			if(data.errorMsg) {
        				alert(data.errorMsg);
        			} else {
        				alert('选定试听班级删除成功');
        				jQuery('#classGrid').datagrid('reload');
        			}
        		}, 'json');
        	})
        	
        	
        	loadClass();
        })
        
        function searchClass() {
        	var searchArgs = {gid:curGroup, keyword:jQuery.trim(jQuery('#keyword').val())};
        	jQuery('#classGrid').datagrid({
        		url:'<?php echo $classUrl?>',
        		queryParams:searchArgs
        	})
        }
        
        function loadClass(groupId) {
        	jQuery('#keyword').val('');
        	if(groupId){
        		jQuery('#classGrid').datagrid({
        			url:"<?php echo $classUrl?>",
        			queryParams:{gid:groupId}
        		});
        	} else {
        		jQuery('#classGrid').datagrid();
        	}
        	
        }
        
        function clsName(val, data) {
        	return '[' + data.sclasscode + ']' + data.sclassname;
        }
        </script>
    </head>
    <body class="easyui-layout" fit="true" border="false">
    	<div region="west" style="width:240px" title="试听组管理" iconCls="icon-redo" collapsible="false">
    		<?php if($permValue & PERM_WRITE):?>
    		<div id="groupToolbar">
    		<a href="javascript:void(0)" id="addGroup" class="easyui-linkbutton" plain="true" iconCls="icon-add">添加</a>
    		<a href="javascript:void(0)" id="editGroup" class="easyui-linkbutton" plain="true" iconCls="icon-edit">编辑</a>
    		<a href="javascript:void(0)" id="delGroup" class="easyui-linkbutton" plain="true" iconCls="icon-cancel">删除</a>
    		</div>
    		<?php endif?>
    		<table id="groupGrid" url="<?php echo $groupUrl?>" rownumbers="true" singleselect="true" fit="true" toolbar="#groupToolbar" border="false">
    			<thead>
    				<tr>
    					<th field="group_title">组别名称</th>
    					<th field="min_lesson" align="center">最小课节</th>
    					<th field="max_lesson" align="center">最大课节</th>
    				</tr>
    			</thead>
    		</table>
    	</div>
    	<div region="center" title="试听班级管理" iconCls="icon-redo" >
    		<?php if($permValue & PERM_WRITE):?>
    		<div id="classToolbar">
    		<a href="javascript:void(0)" id="addClass" class="easyui-linkbutton" plain="true" iconCls="icon-add">添加试听班级</a>
    		<a href="javascript:void(0)" id="delClass" class="easyui-linkbutton" plain="true" iconCls="icon-cancel">删除选定班级</a>
    		<span class="datagrid-btn-separator"></span>
    		搜索:<input type="text" name="keyword" id="keyword" />
    		<a href="javascript:void(0)" onclick="searchClass()" id="searchBtn" class="easyui-linkbutton" plain="true" iconCls="icon-search">查询</a>
    		</div>
    		<?php endif?>
    		<table id="classGrid" class="easyui-datagrid" pagination="true" rownumbers="true" fit="true" toolbar="#classToolbar" border="false">
    			<thead>
    				<tr>
    					<th field="sclasscode" checkbox="true">班级编码</th>
    					<th field="sclassname" formatter="clsName">班级名称</th>
    					<th field="sprinttime">上课时间</th>
    					<th field="sprintarea">上课地点</th>
    					<th field="sprintteachers">授课教师</th>
    					<th field="create_user">操作员</th>
    					<th field="create_at">添加时间</th>
    				</tr>
    			</thead>
    		</table>
    	</div>
    </body>
</html>