<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
        <script type="text/javascript">
        var curEGroup = '';
        var curYGroup = '';
        var groupCaption = '';
        function loadGroup() {
            jQuery('#groupGrid').datagrid({
                queryParams:{groupType:jQuery('#group_type').val(), sort:'group_id', order:'desc'},
                onSelect:function(idx,data) {
                    curEGroup = data.group_id;
                    loadYuyueGroup();
                }
            })
        }
        
        function loadYuyueGroup() {
        	if(curEGroup) {
        		jQuery('#yGroupGrid').datagrid({
        			url:'<?php echo $jsonYyGroupUrl?>',
        			queryParams:{gid:curEGroup},
        			onSelect:function(idx,data){
        				curYGroup = data.ygroup_id;
        				groupCaption = data.ygroup_caption;
        				loadBatch();
        			}
        		});
        	}
        }
        
        function loadBatch() {
        	if(curYGroup) {
        		jQuery('#batchGrid').datagrid({
        			url:'<?php echo $jsonBatchUrl?>',
        			queryParams:{yGroupId:curYGroup}
        		})
        	} else {
        		alert('请选择诊断组');
        	}
        }
        
        function posManage() {
        	if(curYGroup) {
        		var _tm = (new Date()).getTime();
        		jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
        		jQuery('#dlg_' + _tm).dialog({
        			title:'诊断地点管理',
        			iconCls:'icon-redo',
        			width:650,
        			height:450,
        			modal:true,
        			href:'<?php echo $posUrl?>/ygid/' + curYGroup,
        			onClose:function(){
        				jQuery('#dlg_' + _tm).dialog('destroy');
        			}
        		});
        	} else {
        		alert('请选择诊断组');
        	}
        }
        
        function timeManage() {
        	if(curYGroup) {
        		var _tm = (new Date()).getTime();
        		jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
        		jQuery('#dlg_' + _tm).dialog({
        			title:'诊断时间管理',
        			iconCls:'icon-redo',
        			width:650,
        			height:400,
        			modal:true,
        			href:'<?php echo $timeUrl?>/ygid/' + curYGroup,
        			onClose:function(){
        				jQuery('#dlg_' + _tm).dialog('destroy');
        			}
        		});
        	} else {
        		alert('请选择诊断组');
        	}
        }
        
        function addGroup() {
        	if(curEGroup) {
        		var _tm = (new Date()).getTime();
        		jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
        		jQuery('#dlg_' + _tm).dialog({
        			title:'添加诊断预约组',
        			width:440,
        			height:400,
        			iconCls:'icon-add',
        			modal:true,
        			href:'<?php echo $addGroupUrl?>/gid/' + curEGroup + '/dlg/dlg_' + _tm,
        			onClose:function(){
        				jQuery('#dlg_' + _tm).dialog('destroy');
        			}
        		})
        	} else {
        		alert('请选择竞赛组');
        	}
        }
        
        function editGroup() {
        	var _tm = (new Date()).getTime();
        	var select = jQuery('#yGroupGrid').datagrid('getSelected');
        	if(!select) {
        		alert('请选择预约组');
        		return;
        	}
        	var yGroupId = select.ygroup_id;
        	jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
        		jQuery('#dlg_' + _tm).dialog({
        			title:'修改诊断预约组',
        			width:440,
        			height:400,
        			iconCls:'icon-edit',
        			modal:true,
        			href:'<?php echo $editGroupUrl?>/ygid/' + yGroupId + '/dlg/dlg_' + _tm,
        			onClose:function(){
        				jQuery('#dlg_' + _tm).dialog('destroy');
        			}
        		})
        }
        
        function delGroup() {
        	var select = jQuery('#yGroupGrid').datagrid('getSelected');
        	if(select) {
        		if(confirm('确定要删除选定诊断组“' + select.ygroup_caption + '”吗？')) {
        			jQuery.post('<?php echo $delGroupUrl?>', {yGroupId:select.ygroup_id}, function(data){
        				if(data.errorMsg) {
        					alert(data.errorMsg);
        				} else {
        					alert('选定组删除成功');
        					jQuery('#yGroupGrid').datagrid('reload');
        				}
        			}, 'json');
        		}
        	}
        }
        
        function addBatch() {
        	if(curYGroup) {
	        	var _tm = (new Date()).getTime();
	        	jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
	        	jQuery('#dlg_' + _tm).dialog({
	        		title:'添加诊断场次(' + groupCaption + ')',
	        		iconCls:'icon-add',
	        		width:650,
	        		height:350,
	        		modal:true,
	        		href:'<?php echo $addBatchUrl?>/ygid/' + curYGroup + '/dlg/dlg_' + _tm,
	        		onClose:function(){
	        			jQuery('#dlg_' + _tm).dialog('destroy');
	        		}
	        	})
        	} else {
        		alert('请选择诊断组');
        	}
        }
        
        function curCnt(val, data) {
        	if(0 == data.stu_filter) {
        		return val;
        	} else {
        		if(0 == val) return val;
        		return val + '(' + data.cur_new + '/' + data.cur_old + ')';
        	}
        }
        
        function batchManage(val, data) {
        	return '<a href="javascript:delBatch(\'' + data.bid + '\')">删除</a>';
        }
        
        function delBatch(bid) {
        	if(confirm('确定要删除选定场次吗？')) {
        		jQuery.post('<?php echo $delBatchUrl?>', {bid:bid}, function(data){
        			alert('选定场次删除成功');
        			jQuery('#batchGrid').datagrid('reload');
        		})
        	}
        }
        
        jQuery(function(){
        	loadGroup();
        })
        </script>
    </head>
    <body class="easyui-layout" fit="true">
    	<div region="west" style="width:450px">
    		<div class="easyui-layout" fit="true">
    			<div region="north" style="height:200px" title="选择竞赛组" iconCls="icon-redo">
	    			<div id="groupToolbar">
	    			&nbsp;选择竞赛组：<?php echo W('ArraySelect', array('options'=>$gTypeArray, 'attr'=>'id="group_type" onchange="loadGroup()"'))?>
	    			</div>
    				<table id="groupGrid"  singleSelect="true" url="<?php echo $jsonGroupUrl?>" fit="true" rownumbers="true" toolbar="#groupToolbar">
		                <thead>
		                    <tr>
		                    	<th field="group_type">竞赛类别</th>
		                        <th field="group_caption" width="200">竞赛组名称</th>
		                    </tr>
		                </thead>
		            </table>
    			</div>
    			<div region="center" title="预约组管理" iconCls="icon-redo">
    				<div id="yuyueGroupToolbar">
    					<a href="javascript:addGroup()" class="easyui-linkbutton" iconCls="icon-add" plain="true">添加</a>
    					<a href="javascript:editGroup()" class="easyui-linkbutton" iconCls="icon-edit" plain="true">编辑</a>
    					<a href="javascript:delGroup()" class="easyui-linkbutton" iconCls="icon-cancel" plain="true">删除</a>
    				</div>
    				<table id="yGroupGrid" class="easyui-datagrid" fit="true" border="false" rownumbers="true" singleselect="true" toolbar="#yuyueGroupToolbar">
    					<thead>
    						<tr>
    							<th field="ygroup_caption">预约组名称</th>
    							<th field="ygroup_time_start">开始时间</th>
    							<th field="ygroup_time_end">结束时间</th>
    						</tr>
    					</thead>
    				</table>
    			</div>
    		</div>
    	</div>
    	<div region="center" title="诊断场次管理" iconCls="icon-redo">
    	<div id="batchToolbar">
    		<a href="javascript:addBatch()" class="easyui-linkbutton" iconCls="icon-add" plain="true">增加场次</a>
    		<span class="datagrid-btn-separator"></span>
    		<a href="javascript:posManage()" class="easyui-linkbutton" iconCls="icon-edit" plain="true">诊断地点管理</a>
    		<span class="datagrid-btn-separator"></span>
    		<a href="javascript:timeManage()" class="easyui-linkbutton" iconCls="icon-edit" plain="true">诊断时间管理</a>
    	</div>
    	<table id="batchGrid" class="easyui-datagrid" fit="true" border="false" rownumbers="true" pagination="true" singleselected="true" toolbar="#batchToolbar" pageList='[20,50,100]'>
    		<thead>
    			<tr>
    				<th field="ygroup_caption">预约组</th>
    				<th field="date" align="center">预约日期</th>
    				<th field="pos_caption">预约地点</th>
    				<th field="time_text">时间段</th>
    				<th field="total_cnt" align="center">满额人数</th>
    				<th field="cur_total" formatter="curCnt" align="center">已报人数</th>
    				<th field="update_user">操作员</th>
    				<th field="update_at">操作时间</th>
    				<th field="manage" align="center" formatter="batchManage">管理</th>
    			</tr>
    		</thead>
    	</table>
    	</div>
    </body>
</html>