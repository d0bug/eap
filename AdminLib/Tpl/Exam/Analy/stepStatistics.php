<style type="text/css">
.stepUL{margin:0px;padding:0px;float:left;width:500px}
.stepUL li{line-height:24px;width:240px;float:left;padding-left:5px;list-style-type:none}
</style>
<?php if($stepStatistics):?>
<div class="easyui-layout" fit="true">
	<div region="north" style="height:35px;padding:2px ">
		<a href="javascript:void(0)" onclick="saveStepCfg()" iconCls="icon-save" class="easyui-linkbutton" plain="true">保存分档设置</a>
	</div>
	<div region="center">
		<form id="stepCfgForm">
		<input type="hidden" name="exam_id" value="<?php echo $examId?>" />
		<input type="hidden" name="subject_code" value="<?php echo $subjectCode?>" />
		<textarea name="step_statistics" style="display:none"><?php echo $jsonStepStatistics?></textarea>
		<?php foreach ($stepStatistics as $step=>$stepCfg):?>
		<fieldset>
			<legend><b><?php echo $stepCfg['caption']?></b>——(试题难度：<?php echo $stepCfg['levels']?>)</legend>
			<ul class="stepUL">
				<li><label>试题数量：</label><b><?php echo $stepCfg['ques_count']?></b></li>
				<li><label>分档总分：</label><b><?php echo $stepCfg['step_score']?></b></li>
				<li><label>平 均 分：</label><b><?php echo $stepCfg['step_average']?></b></li>
				<li><label>得 分 率：</label><b><?php echo $stepCfg['step_ratio']?>%</b></li>
				<li style="width:400px"><label>优档条件：</label>分档得分&gt;=<input size="6" type="text" name="step_score[<?php echo $step?>]" id="step_score_<?php $step?>" value="<?php echo $stepScores[$step]?>" style="text-ailgn:center" /></li>
			</ul>
		</fieldset>
		<?php endforeach;?>
		</form>
	</div>
</div>
<script type="text/javascript">
function saveStepCfg() {
	var formData = jQuery('#stepCfgForm').serialize();
	jQuery.post('<?php echo $saveStepCfgUrl?>', formData, function(data){
		if(data.errorMsg) {
			alert(data.errorMsg);
		} else {
			alert('分档分数设置成功');
			jQuery('#<?php echo $dialog?>').dialog('destroy');
		}
	}, 'json');
}
</script>
<?php else:?>
<script type="text/javascript">
alert('没有分档数据');
jQuery('#<?php echo $dialog?>').dialog('destroy');
</script>
<?php endif?>