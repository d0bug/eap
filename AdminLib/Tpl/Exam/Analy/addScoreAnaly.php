<style type="text/css">
.item{papdding:3px;margin:2px}
</style>
<div class="easyui-layout" fit="true">
	<div region="north" style="height:34px;background:#eee;padding:3px">
		<div class="datagrid-toolbar"><a href="javascript:void(0)" onclick="doAddAnaly()" class="easyui-linkbutton" iconCls="icon-save" plain="true">保存</a></div>
	</div>
	<div region="center" style="padding:4px 10px">
		<form id="analyForm">
		<div class="item"><span>成绩类型：</span><select id="analy_type" name="analy_type">
		<?php foreach ($paperCaptions as $paperType=>$paper):?>
		<option value="<?php echo $paperType?>"><?php echo $paper['caption']?></option>
		<?php endforeach;?>
		</select>&nbsp;
		<span>成绩分值(<b style="color:red">&gt;=</b>)：</span><input type="text" size="10" class="easyui-numberbox" id="analy_score" name="analy_score"  onblur="getRank()" />
		<!--a href="javascript:void(0)" onclick="getRank()" class="easyui-linkbutton" iconCls="icon-search">查询排名</a-->
		</div>
		<hr />
		<div class="item"><span>成绩排名：</span>真实：<input disabled="true" size="10" type="text" id="score_rank" />&nbsp;宣传：<input disabled="true" size="10" type="text" id="score_vrank" /></div>
		<div class="item"><span>排名比例：</span>真实：<input disabled="true" size="10" type="text" id="score_ratio" />&nbsp;宣传：<input disabled="true" size="10" type="text" id="score_vratio" /></div>
		<hr />
		<div class="item">
			<fieldset>
				<legend>成绩评语</legend>
				<textarea id="analy_text" name="analy_text" style="width:100%;height:75px;resize:none;"></textarea>
			</fieldset>
		</div>
		</form>
	</div>
</div>
<script type="text/javascript">
function getRank() {
	var analyScore = parseFloat(jQuery('#analy_score').val());
	if(analyScore <= 0) {
		alert('请输入成绩分值');
		return;
	}
	var data= {exam:<?php echo $examId?>, analy_type:jQuery('#analy_type').val(), 'analy_score':analyScore}
	jQuery.post('<?php echo $scoreRankUrl?>', data, function(data){
		jQuery('#score_rank').val(data.real);
		jQuery('#score_vrank').val(data.virtual);
		jQuery('#score_ratio').val(100 - data.rRatio) + '%';
		jQuery('#score_vratio').val(100 - data.vRatio) + '%';
	}, 'json');
}
function doAddAnaly() {
	var analyScore = parseFloat(jQuery('#analy_score').val());
	var analyText = jQuery.trim(jQuery('#analy_text').val());
	if(analyScore <= 0) {
		alert('请输入成绩分值');
		return;
	}
	if ('' == analyText) {
		alert('请输入成绩评语');
		return;
	}
	var data= {exam:<?php echo $examId?>, analy_type:jQuery('#analy_type').val(), 'analy_score':analyScore,'analy_text':analyText}
	jQuery.post('<?php echo $addScoreAnalyUrl?>', data, function(data){
		if(data.errorMsg) {
			alert(data.errorMsg)
		} else {
			alert('成绩评语设置成功');
			jQuery('#analyGrid').datagrid('reload');
			jQuery('#<?php echo $dlg?>').dialog('destroy');
		}
	}, 'json');
}
</script>