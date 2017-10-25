<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
        <script type="text/javascript" src="/static/kindeditor/kindeditor-min.js"></script>
        <?php if($permValue & PERM_WRITE):?>
        <script type="text/javascript">
        var curFormId = '';
        jQuery(function(){
        	jQuery('#actGrid').datagrid({
        		onSelect:function(idx, data) {
        			curFormId = data.act_id;
        			jQuery.post('<?php echo $jsonActInfoUrl?>', {actId:data.act_id}, function(actInfo){
        				jQuery('#searchFormLayer').find('form').html(actInfo.searchForm)
        				jQuery('#recordGrid').datagrid({
        					url:'<?php echo $jsonRecordUrl?>/id/' + data.act_id,
        					columns:[actInfo.columns]
        				})
        			}, 'json');
        		}
        	})
        	jQuery('#searchForm').submit(function(){
        		hideForm();
        		alert(1);
        	})
        	jQuery('#addAct').click(function(){
        		var _tm = (new Date()).getTime()
        		jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
        		jQuery('#dlg_' + _tm).dialog({
        			modal:true,
        			width:800,
        			height:500,
        			href:'<?php echo $addActUrl?>/dlg/dlg_' + _tm,
        			title:'添加活动表单',
        			iconCls:'icon-add',
        			onClose:function(){
        				jQuery('#dlg_' + _tm).dialog('destroy')
        				jQuery('#actGrid').datagrid('reload');
        			}
        		})
        	})
        	jQuery('#editAct').click(function(){
        		var _tm = (new Date()).getTime();
        		var oSeleAct = jQuery('#actGrid').datagrid('getSelected');
        		if(oSeleAct) {
        			jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
	        		jQuery('#dlg_' + _tm).dialog({
	        			modal:true,
	        			width:800,
	        			height:500,
	        			href:'<?php echo $editActUrl?>/id/' + oSeleAct.act_id + '/dlg/dlg_' + _tm,
	        			title:'修改活动表单',
	        			iconCls:'icon-edit',
	        			onClose:function(){
	        				jQuery('#dlg_' + _tm).dialog('destroy')
	        				jQuery('#actGrid').datagrid('reload');
	        			}
	        		})
        		} else {
        			alert('请选择要编辑的活动表单')
        		}
        	})
        	
        	jQuery('#preview').click(function(){
        		
        	})
        	
        })
        
        function showForm() {
        	if('' == curFormId) {
        		alert('请先选择活动表单');
        		return;
        	}
        	jQuery('#searchFormLayer').show()
        }
        
        function hideForm() {
        	jQuery('#searchFormLayer').hide()
        }
        </script>
        <?php endif;?>
    </head>
    <body class="easyui-layout" fit="true">
    	<?php if($permValue & PERM_WRITE):?>
    	<div id="actToolbar">
    		<a href="javascript:void(0)" id="addAct" class="easyui-linkbutton" iconCls="icon-add" plain="true">添加</a>
    		<a href="javascript:void(0)" id="editAct" class="easyui-linkbutton" iconCls="icon-edit" plain="true">编辑</a>
    		<!--a href="javascript:void(0)" id="delAct" class="easyui-linkbutton" iconCls="icon-cancel" plain="true">删除</a>
    		<a href="javascript:void(0)" id="preview" class="easyui-linkbutton" iconCls="icon-search" plain="true">预览</a-->
    	</div>
    	<?php endif;?>
    	<div region="west" style="width:550px">
	    	<table id="actGrid" title="活动表单列表" iconCls="icon-redo" fit="true" url="<?php echo $actListUrl?>" rownumbers="true" pagination="true" singleselect="true" toolbar="#actToolbar">
	    		<thead>
	    			<tr>
	    				<th field="act_title">活动标题</th>
	    				<th field="act_start">开始日期</th>
	    				<th field="act_end">结束日期</th>
	    			</tr>
	    		</thead>
	    	</table>
    	</div>
    	<div region="center">
    		<div class="easyui-layout" fit="true" border="false">
    			<div region="center">
    				<div id="stuGridToolbar">
    				<a href="javascript:void(0)" onclick="showForm()" class="easyui-linkbutton" iconCls="icon-search" plain="true">查询</a>
    				<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-redo" plain="true">导出</a>
    				<div style="display:none;position:absolute;z-index:100;border:1px solid #ccc;background:#ddd;width:80%;height:50px" id="searchFormLayer">
    				<fieldset>
    				<legend><a href="javascript:void(0)" onclick="hideForm()">收起表单</a></legend>
    				<form method="GET" id="searchForm">
    				
    				</form>
    				</fieldset>
    				</div>
    				</div>
    				<table class="easyui-datagrid" id="recordGrid" fit="true" border="false" rownumbers="true" pagination="true" toolbar="#stuGridToolbar">
    					
    				</table>
    			</div>
    		</div>
    	</div>
    </body>
</html>
