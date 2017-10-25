<div class="easyui-layout" fit="true">
	<div region="north" style="height:33px">
		<div style="padding:3px 0px 0px 4px"><a class="easyui-linkbutton" iconCls="icon-save" href="javascript:void(0)" onclick="doSaveGroup()">保存分组</a></div>
	</div>
	<div region="center">
		<form id="addGroupForm">
		<input type="hidden" name="group_type" value="<?php echo $groupType?>" />
		<input type="hidden" name="type_caption" value="SQL查询组" />
		<table cellpadding="4">
			<thead>
			<tr><th style="width:80px">分组名称：</th><td><input type="text" style="border:1px solid #ddd;width:480px" name="group_title" id="group_title" size="40" /></td></tr>
			</thead>
			<tr><th valign="top">查询语句：</th><td><textarea style="border:1px solid #ddd;width:480px;height:260px;resize:none" name="group_data" id="group_data"></textarea></td></tr>
		</table>
		</form>
	</div>
</div>
<script type="text/javascript">
	function doSaveGroup() {
		var formData = jQuery('#addGroupForm').serialize();
		jQuery.post('<?php echo $addGroupUrl?>', formData, function(data) {
			if(data.errorMsg) {
				alert(data.errorMsg);
			} else {
				alert('筛选组添加成功');
				jQuery('#<?php echo $dialog?>').dialog('destroy');
				jQuery('#groupGrid').datagrid('reload');
			}
		}, 'json');
	}
</script>