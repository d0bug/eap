<style type="text/css">
.analyUL{margin:0px;padding:4px 20px}
.analyUL li{list-style-type:none}
pre{margin:0px;padding:4px}
</style>
<div class="easyui-layout" fit="true">
	<div region="north" style="height:150px">
		<input type="hidden" id="analy_id" /> 
		<ul class="analyUL">
			<li><span style="font-weight:bold">等级总成绩：</span>等级成绩大于等于该分数<br /><input type="text" id="level_score" style="border:1px solid #ddd;height:26px;width:400px;ime-mode:disabled" onkeyup="this.value=this.value.replace(/[^\d\.]/g, '')" /></li>
			<li><span style="font-weight:bold">分析内容：</span>分析内容<br /><textarea id="level_analy" style="border:1px solid #ddd;height:50px;width:400px;resize:none"></textarea></li>
			<li><a href="javascript:void(0)" class="easyui-linkbutton saveAnalyBtn" iconCls="icon-save">保存等级分析</a></li>
		</ul>
	</div>
	<div region="center" style="padding:2px 15px">
		<?php foreach ($analyList as $analy):?>	
		<fieldset>
		<legend>成绩大于等于：<b style="font-size:15px"><?php echo $analy['level_score']?></b>&nbsp;
				<input class="editAnalyBtn" type="button" value="修改" score="<?php echo $analy['level_score']?>" analyId="<?php echo $analy['id']?>" />
				<input class="delAnalyBtn" type="button" value="删除" analyId="<?php echo $analy['id']?>" />
				(<?php echo $analy['update_user']?>)</legend>
		<pre class="level_analy_<?php echo $analy['id']?>"><?php echo $analy['level_analy']?></pre>
		</fieldset>
		<?php endforeach;?>
	</div>
</div>
<script type="text/javascript">
jQuery('.saveAnalyBtn').click(function(){
	var formData = {
					examId:<?php echo $examId?>,
					subjectCode:'<?php echo $subjectCode?>',
					levelNum:'<?php echo $levelNum?>',
					analyId:jQuery.trim(jQuery('#analy_id').val()),
					levelScore:Math.abs(jQuery('#level_score').val()),
					levelAnaly:jQuery.trim(jQuery('#level_analy').val())
					}
	jQuery.post('<?php echo $levelAnalyUrl?>', formData, function(data){
		if(data.errorMsg) {
			alert(data.errorMsg);
		} else {
			alert('等级分析保存成功');
			jQuery('body').layout('panel','east').panel('refresh');
		}
	}, 'json');
})
jQuery('.editAnalyBtn').click(function(){
	jQuery('#analy_id').val(jQuery(this).attr('analyId'));
	jQuery('#level_score').val(jQuery(this).attr('score'));
	jQuery('#level_analy').val(jQuery('.level_analy_' + jQuery('#analy_id').val()).text());
})
jQuery('.delAnalyBtn').click(function(){
	if(confirm('确定要删除选定等级分析吗？')) {
		jQuery.post('<?php echo $delAnalyUrl?>', {analyId:jQuery(this).attr('analyId')}, function(data){
			if(data.errorMsg) {
				alert(data.errorMsg);
			} else {
				alert('等级分析删除成功');
				jQuery('body').layout('panel', 'east').panel('refresh');
			}
		}, 'json');
	}
})
</script>