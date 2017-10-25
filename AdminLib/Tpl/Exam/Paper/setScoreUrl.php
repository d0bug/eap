<div class="easyui-layout" fit="true">
	<div region="center" style="padding:10px">
		<div style="padding:4px;background:#eee;border:1px solid #ddd;margin-bottom:5px;line-height:26px;font-size:14px;font-weight:bold">
			本功能用于自定义学科成绩查询地址，对于学科有特殊成绩查询需求时需要进行设置，如不设置则采用默认查询样式。
		</div>
		<div style="padding:4px;background:#eee;border:1px solid #ddd;margin-bottom:5px;line-height:33px;font-size:14px">URL变量：参数key<b>{key}</b>，(竞赛ID，试卷学科，学生编码的加密字串)</div>
		<form id="scoreUrlForm">
			<div style="padding:4px;background:#eee;border:1px solid #ddd;margin-bottom:5px;line-height:33px;font-size:14px">
			成绩查询地址：<input type="text" name="score_url" size="42" id="score_url" value="<?php echo $scoreUrl?>" style="ime-mode:disabled" />
			<input type="button" iconCls="icon-save" onclick="saveScoreUrl()" value="保存URL设置" />
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
function saveScoreUrl() {
	var formData = {examId:'<?php echo $examId?>', subjectCode:'<?php echo $subjectCode?>', scoreUrl:jQuery.trim(jQuery('#score_url').val())}
	jQuery.post('<?php echo $setScoreUrl?>', formData, function(data){
		if(data.errorMsg) {
			alert(data.errorMsg);
		} else {
			alert('查询地址设置成功');
			jQuery('#<?php echo $dialog?>').dialog('destroy');
			jQuery('#subjectGrid').datagrid('reload');
		}
	}, 'json');
}
</script>