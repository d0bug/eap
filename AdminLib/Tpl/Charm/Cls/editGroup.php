<style type="text/css">
dl{margin:0px;padding-left:20px}
dt,dd{padding:2px;margin:0px;}
dt{font-weight:bold;font-size:14px;margin-top:5px}
</style>
<div class="easyui-layout" fit="true">
	<div region="north" style="height:32px;padding:2px 0px 0px 2px">
		<a href="javascript:void(0)" id="saveGroupBtn" class="easyui-linkbutton" iconCls="icon-save" plain="true">保存试听组信息</a>
	</div>
	<div region="center">
		<form id="charmGroupForm">
		<input type="hidden" name="group_id" id="group_id" value="<?php echo $groupInfo['group_id']?>" />
		<dl>
			<dt>试听组标题</dt>
			<dd><input type="text" id="group_title" name="group_title" class="easyui-validatebox" value="<?php echo $groupInfo['group_title']?>" required="true" /></dd>
			<dt>课节范围：</dt>
			<dd><input type="text" id="min_lesson" name="min_lesson" class="easyui-validatebox easyui-numberspinner" size="9" min="1" maxlength="20" value="<?php echo $groupInfo['min_lesson']?>" required="true" /> — 
				<input type="text" id="max_lesson" name="max_lesson" class="easyui-validatebox easyui-numberspinner" size="9" min="1" maxlength="20" value="<?php echo $groupInfo['max_lesson']?>" required="true" /></dd>
		</dl>
		</form>
	</div>
</div>
<script type="text/javascript">
jQuery(function(){
	var groupInfo = {};
	jQuery('#saveGroupBtn').click(function(){
		var saveGroup = true;
		jQuery('#charmGroupForm').find('.easyui-validatebox').each(function(){
			if(false == jQuery(this).validatebox('isValid')){
				saveGroup = false;
			}
		})
		if(false == saveGroup) {
			alert('试听组信息不完整');
			return;
		}
		if(Math.abs(jQuery('#min_lesson').val()) > Math.abs(jQuery('#max_lesson').val())) {
			alert('最小课节数不能大于最大课节数');
			return;
		}
		if(saveGroup) {
			var formData = jQuery('#charmGroupForm').serialize();
			jQuery.post('<?php echo $saveGroupUrl?>', formData, function(data){
				if(data.errorMsg) {
					alert(data.errorMsg);
				} else {
					alert('试听组修改成功');
					jQuery('#groupGrid').datagrid('reload');
					jQuery('#<?php echo $dialog?>').dialog('destroy');
				}
			}, 'json');
		}
	})
})
</script>