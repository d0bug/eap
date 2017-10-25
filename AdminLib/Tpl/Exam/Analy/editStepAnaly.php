<?php if (false == $analyLevels):?>

<?php endif?>
<div class="easyui-layout" fit="true">
	<div region="north" style="height:28px">
		<a href="javascript:void(0)" onclick="doEditStepAnaly()" class="easyui-linkbutton" iconCls="icon-save" plain="true">保存</a>
	</div>
	<div region="center">
		<form id="analyForm" name="analyForm" style="padding:20px">
		<input type="hidden" name="analyId" value="<?php echo $analyInfo['id']?>" />
		<input type="hidden" name="exam_id" value="<?php echo $analyInfo['exam_id']?>" />
		<input type="hidden" name="subject_code" value="<?php echo $analyInfo['subject_code']?>" />
		<div><label>选择分析级别：</label><select name="analy_level">
			<?php foreach ($analyLevels as $analyLevel=>$levelText):?>
			<option value="<?php echo $analyLevel?>"<?php if($analyLevel == $analyInfo['analy_level']):?> selected="true"<?php endif?>><?php echo $levelText?></option>
			<?php endforeach;?>
			</select></div>
		<div><label>分析内容：</label><br /><textarea name="analy_text" style="width:400px;height:80px;resize:none"><?php echo $analyInfo['analy_text']?></textarea></div>
		</form>
	</div>
</div>
<script type="text/javascript">
function doEditStepAnaly() {
	var formData = jQuery('#analyForm').serialize();
	jQuery.post('<?php echo $editAnalyUrl?>', formData, function(data){
		if(data.errorMsg) {
			alert(data.errorMsg)
		} else {
			alert('分档话术保存成功');
			jQuery('#<?php echo $dialog?>').dialog('destroy');
			jQuery('#analyGrid').datagrid('reload');
		}
	}, 'json')
}
</script>