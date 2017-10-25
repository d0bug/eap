<style type="text/css">
.analyUL{margin:0px;padding:4px 20px}
.analyUL li{list-style-type:none}
pre{margin:0px;padding:4px}
</style>
<div class="easyui-layout" fit="true">
	<div region="north" style="height:150px">
		<input type="hidden" id="analy_id" /> 
		<ul class="analyUL">
			<li><span style="font-weight:bold">模块成绩：</span>模块成绩大于等于该分数<br /><input type="text" id="module_score" style="border:1px solid #ddd;height:26px;width:400px;ime-mode:disabled" onkeyup="this.value=this.value.replace(/[^\d\.]/g, '')" /></li>
			<li><span style="font-weight:bold">模块分析内容：</span>分析内容<br /><textarea id="module_analy" style="border:1px solid #ddd;height:50px;width:400px;resize:none"></textarea></li>
			<li><a href="javascript:void(0)" class="easyui-linkbutton saveAnalyBtn" iconCls="icon-save">保存模块分析</a></li>
		</ul>
	</div>
	<div region="center" style="padding:2px 15px">
		<?php foreach ($analyList as $analy):?>	
		<fieldset>
		<legend>成绩大于等于：<b style="font-size:15px"><?php echo $analy['module_score']?></b>&nbsp;
				<input class="editAnalyBtn" type="button" value="修改" score="<?php echo $analy['module_score']?>" analyId="<?php echo $analy['id']?>" />
				<input class="delAnalyBtn" type="button" value="删除" analyId="<?php echo $analy['id']?>" />
				(<?php echo $analy['update_user']?>)</legend>
		<pre class="module_analy_<?php echo $analy['id']?>"><?php echo $analy['module_analy']?></pre>
		</fieldset>
		<?php endforeach;?>
	</div>
</div>
<script type="text/javascript">
jQuery('.saveAnalyBtn').click(function(){
	var formData = {
					examId:<?php echo $examId?>,
					subjectCode:'<?php echo $subjectCode?>',
					moduleCode:'<?php echo $moduleCode?>',
					analyId:jQuery.trim(jQuery('#analy_id').val()),
					moduleScore:Math.abs(jQuery('#module_score').val()),
					moduleAnaly:jQuery.trim(jQuery('#module_analy').val())
					}
	jQuery.post('<?php echo $moduleAnalyUrl?>', formData, function(data){
		if(data.errorMsg) {
			alert(data.errorMsg);
		} else {
			alert('模块分析保存成功');
			jQuery('body').layout('panel','east').panel('refresh');
		}
	}, 'json');
})
jQuery('.editAnalyBtn').click(function(){
	jQuery('#analy_id').val(jQuery(this).attr('analyId'));
	jQuery('#module_score').val(jQuery(this).attr('score'));
	jQuery('#module_analy').val(jQuery('.module_analy_' + jQuery('#analy_id').val()).text());
})
jQuery('.delAnalyBtn').click(function(){
	if(confirm('确定要删除选定模块分析吗？')) {
		jQuery.post('<?php echo $delAnalyUrl?>', {analyId:jQuery(this).attr('analyId')}, function(data){
			if(data.errorMsg) {
				alert(data.errorMsg);
			} else {
				alert('模块分析删除成功');
				jQuery('body').layout('panel', 'east').panel('refresh');
			}
		}, 'json');
	}
})
</script>