<style type="text/css">
.analyUL{margin:0px;padding:4px 20px}
.analyUL li{list-style-type:none}
pre{margin:0px;padding:4px}
</style>
<div class="easyui-layout" fit="true">
	<div region="north" style="height:150px">
		<input type="hidden" id="analy_id" /> 
		<ul class="analyUL">
			<li><span style="font-weight:bold">知识点成绩：</span>知识点成绩大于等于该分数<br /><input type="text" id="knowledge_score" style="border:1px solid #ddd;height:26px;width:400px;ime-mode:disabled" onkeyup="this.value=this.value.replace(/[^\d\.]/g, '')" /></li>
			<li><span style="font-weight:bold">知识点析内容：</span>分析内容<br /><textarea id="knowledge_analy" style="border:1px solid #ddd;height:50px;width:400px;resize:none"></textarea></li>
			<li><a href="javascript:void(0)" class="easyui-linkbutton saveAnalyBtn" iconCls="icon-save">保存知识点分析</a></li>
		</ul>
	</div>
	<div region="center">
		<?php foreach ($analyList as $analy):?>	
		<fieldset>
		<legend>成绩大于等于：<b style="font-size:15px"><?php echo $analy['knowledge_score']?></b>&nbsp;
				<input class="editAnalyBtn" type="button" value="修改" score="<?php echo $analy['knowledge_score']?>" analyId="<?php echo $analy['id']?>" />
				<input class="delAnalyBtn" type="button" value="删除" analyId="<?php echo $analy['id']?>" />
				(<?php echo $analy['update_user']?>)</legend>
		<pre class="knowledge_analy_<?php echo $analy['id']?>"><?php echo $analy['knowledge_analy']?></pre>
		</fieldset>
		<?php endforeach;?>
	</div>
</div>
<script type="text/javascript">
jQuery('.saveAnalyBtn').click(function(){
	var formData = {
					examId:<?php echo $examId?>,
					subjectCode:'<?php echo $subjectCode?>',
					knowledgeCode:'<?php echo $knowledgeCode?>',
					analyId:jQuery.trim(jQuery('#analy_id').val()),
					knowledgeScore:Math.abs(jQuery('#knowledge_score').val()),
					knowledgeAnaly:jQuery.trim(jQuery('#knowledge_analy').val())
					}
	jQuery.post('<?php echo $knowledgeAnalyUrl?>', formData, function(data){
		if(data.errorMsg) {
			alert(data.errorMsg);
		} else {
			alert('知识点分析保存成功');
			jQuery('body').layout('panel','east').panel('refresh');
		}
	}, 'json');
})
jQuery('.editAnalyBtn').click(function(){
	jQuery('#analy_id').val(jQuery(this).attr('analyId'));
	jQuery('#knowledge_score').val(jQuery(this).attr('score'));
	jQuery('#knowledge_analy').val(jQuery('.knowledge_analy_' + jQuery('#analy_id').val()).text());
})
jQuery('.delAnalyBtn').click(function(){
	if(confirm('确定要删除选定知识点分析吗？')) {
		jQuery.post('<?php echo $delAnalyUrl?>', {analyId:jQuery(this).attr('analyId')}, function(data){
			if(data.errorMsg) {
				alert(data.errorMsg);
			} else {
				alert('知识点分析删除成功');
				jQuery('body').layout('panel', 'east').panel('refresh');
			}
		}, 'json');
	}
})
</script>