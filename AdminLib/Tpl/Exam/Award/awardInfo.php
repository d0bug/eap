<style type="text/css">
#awardInfo li{list-style-type:none;line-height:25px;}
#awardInfo li b{font-size:14px}
#awardInfo li input{height:22px}
</style>
<div class="easyui-layout" fit="true" border="false">
	<div region="center">
		<form id="awardForm">
		<input type="hidden" name="exam_id" value="<?php echo $examId?>" />
		<?php if($awardInfo):?>
		<input type="hidden" name="id" value="<?php echo $awardId	?>" />
		<?php endif;?>
		<ul id="awardInfo">
			<li><b>奖项类别</b>：</li>
			<li><select name="award_type" id="award_type">
				<?php foreach ($awardTypes as $typeKey=>$typeCaption):?>
				<option value="<?php echo $typeKey?>" <?php if($awardInfo && $awardInfo['award_type']==$typeKey):?> selected="true"<?php endif?>><?php echo $typeCaption?></option>
				<?php endforeach?>
				</select> <a href="javascript:editTypeName()" class="easyui-linkbutton" iconCls="icon-edit">修改类别名称</a></li>
			<li><b>奖项分数</b>：</li>
			<li><input type="text" name="award_score" id="award_score" style="ime-mode:disabled" value="<?php if($awardInfo): echo $awardInfo['award_score'];?><?php endif;?>" onkeyup="this.value=this.value.replace(/[^\d\.]/g, '')" />
			(大于等于此分数时显示奖项名称)
			</li>
			<li><b>奖项名称</b>：</li>
			<li><input type="text" name="award_caption" id="award_caption" value="<?php if($awardInfo): echo $awardInfo['award_caption'];?><?php endif;?>" />
				(奖项显示文字)
			</li>
			
		</ul>
		</form>
	</div>
</div>
<script type="text/javascript">
jQuery(function(){
	jQuery('#award_type').change(function(){
		curAwardType = jQuery(this).val();
	})
	<?php if(false == $awardInfo):?>
	jQuery('#award_type').val(curAwardType);
	<?php endif;?>
})

function editTypeName() {
	var examId = '<?php echo $examId?>';
	var awardType = jQuery('#award_type').val();
	var typeCaption = prompt('请输入类别名称');
	if(typeCaption && jQuery.trim(typeCaption)) {
		var formData = {examId:examId, awardType:awardType,typeCaption:typeCaption}
		jQuery.post('<?php echo $typeNameUrl?>', formData, function(data){
			if(data) {
				alert('类别名称设置成功');
				var oSele = jQuery('#award_type')[0];
				oSele.options[oSele.selectedIndex].text = typeCaption;
				jQuery('#awardGrid').datagrid('reload');
			} else {
				alert('类别名称设置失败');
			}
		}, 'json');
	}
}
</script>