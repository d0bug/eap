<?php if (false == $analyLevels):?>
<script type="text/javascript">
alert('没有更多分档');
jQuery('#<?php echo $dialog?>').dialog('destroy');
</script>
<?php else:?>
<div class="easyui-layout" fit="true">
	<div region="north" style="height:28px">
		<a href="javascript:void(0)" onclick="doAddStepAnaly()" class="easyui-linkbutton" iconCls="icon-save" plain="true">保存</a>
	</div>
	<div region="center">
		<form id="analyForm" name="analyForm" style="padding:20px">
		<input type="hidden" name="exam_id" value="<?php echo $examId?>" />
		<input type="hidden" name="subject_code" value="<?php echo $subjectCode?>" />
		<div><label>选择分析级别：</label><select name="analy_level">
			<?php foreach ($analyLevels as $analyLevel=>$levelText):?>
			<option value="<?php echo $analyLevel?>"><?php echo $levelText?></option>
			<?php endforeach;?>
			</select></div>
		<div><label>分析内容：</label><br /><textarea name="analy_text" style="width:400px;height:80px;resize:none"></textarea></div>
		</form>
	</div>
</div>
<script type="text/javascript">
function doAddStepAnaly() {
	var formData = jQuery('#analyForm').serialize();
	jQuery.post('<?php echo $addAnalyUrl?>', formData, function(data){
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
<?php endif?>