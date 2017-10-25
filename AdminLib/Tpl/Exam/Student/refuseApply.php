<div class="easyui-layout" fit="true">
	<div region="center">
		<fieldset>
		<legend>申请原因</legend>
		<span><?php echo SysUtil::safeString($applyInfo['apply_reason'])?></span>
		</fieldset>
		<fieldset>
		<legend>拒绝原因</legend>
		<textarea id="refuse_reason" style="width:100%;height:80px"></textarea>
		</fieldset>
		<div style="text-align:center;padding-top:8px">
			<a href="javascript:void(0)" onclick="doRefuseApply()" class="easyui-linkbutton" iconCls="icon-save">拒绝申请</a>
			<a href="javascript:void(0)" class="easyui-linkbutton closeDlg" iconCls="icon-cancel">关闭窗口</a>
		</div>
	</div>
</div>
<script type="text/javascript">
jQuery(function(){
	jQuery('.closeDlg').click(function(){
		jQuery('#<?php echo $dialog?>').dialog('destroy');
	})
})
function doRefuseApply(){
	jQuery.post('<?php echo $refuseApplyUrl?>', {applyId:<?php echo $applyId?>, status:-1, reason:jQuery('#refuse_reason').val()}, function(data){
		if(data.errorMsg) {
			alert(data.errorMsg);
		} else {
			alert('拒绝操作执行成功');
			jQuery('#<?php echo $dialog?>').dialog('destroy');
			jQuery('#applyGrid').datagrid('reload');
		}
	}, 'json');
}
</script>