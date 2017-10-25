<style type="text/css">
.clsFormTbl{border-left:1px solid #ddd;border-top:1px solid #ddd;margin:5px auto}
.clsFormTbl td{border-right:1px solid #ddd;border-bottom:1px solid #ddd;height:28px}
td label{display:block;float:left;width:100px;text-align:right}
</style>
<div class="easyui-layout" fit="true">
	<div region="north" style="height:29px;padding:2px">
	<a href="javascript:void(0)" onclick="doAddRule()" class="easyui-linkbutton" iconCls="icon-save">保存分班规则</a>
	</div>
	<div region="center">
		<form id="clsRuleForm">
		<input type="hidden" name="exam_id" value="<?php echo $examId?>" />
		<input type="hidden" name="subject_code" value="<?php echo $subjectCode?>" />
		<table class="clsFormTbl" cellpadding="0" cellspacing="0" >
			<tr>
				<td style="width:340px"><label>班级编码前缀：</label><input type="text" value="<?php echo $defaultSemester?>" style="ime-mode:disabled;text-transform:uppercase" name="class_semester" id="class_semester" /></td>
				<td rowspan="4" style="text-align:center;font-weight:bold">匹配班级<br />
				<select id="class_list" style="width:300px;margin:0px 5px" size="7"></select></td>
			</tr>
			<tr>
				<td><label>报班级别：</label><input type="text" class="easyui-numberspinner" min="0" name="class_level" id="class_level" /></td>
			</tr>
			<tr>
				<td><label>班型编码：</label><input type="text" name="class_type" id="class_type" /></td>
			</tr>
			<tr>
				<td><label>分配班级：</label><input type="text" name="class_name" id="class_name" /></td>
			</tr>
			<tr><td><label>学员筛选组：</label><select name="stu_group_id" id="stu_group_id" style="width:200px">
			<option value="0">请选择学员筛选组</option>
			<?php foreach ($groupList as $group):?>
			<option value="<?php echo $group['group_id']?>"><?php echo $group['group_title']?></option>
			<?php endforeach;?>
			</select></td><td>&nbsp;优先级别：<input type="text" size="8" class="easyui-numberspinner" name="class_weight" id="class_weight" />(数字越小,越优先匹配)</td></tr>
			<tr><td colspan="2"><label>搜课地址：</label><input type="text" name="class_search_link" style="width:500px" /></td></tr>
			<tr><td colspan="2"><label>班型介绍地址：</label><input type="text" name="class_info_link" style="width:500px" /></td></tr>
			<tr><td colspan="2"><label>班型介绍摘要：</label></td></tr>
			<tr><td colspan="2"><textarea style="resize:none;width:650px;height:50px" name="class_desc"></textarea></td></tr>
		</table>
		</form>
	</div>
</div>
<script type="text/javascript">
jQuery(function(){
	jQuery('#class_semester').blur(getClassList);
	jQuery('#class_level').numberspinner({
		onSpinUp:function(){
			getClassList()
		},
		onSpinDown:function(){
			getClassList()
		}
	})
	jQuery('#class_list').change(function(){
		jQuery('#class_type').val(this.value);
		var className = this.options[this.selectedIndex].text.replace(/[幼一二三四五]升[一二三四五六]年级/, '').replace(/[初高一二三四五六]年级/, '');
		jQuery('#class_name').val(className);
	})
})

function getClassList() {
	var semester = jQuery.trim(jQuery('#class_semester').val());
	var clsLevel = Math.abs(jQuery('#class_level').val());
	if(!semester) return;
	var data = {semester:semester, clsLevel:clsLevel, xuebuke:'<?php echo $xuebuke?>'}
	var oClsList = jQuery('#class_list').get(0);
	oClsList.length = 0 ;
	jQuery('#class_type').val('');
	jQuery('#class_name').val('');
	jQuery.post('<?php echo $jsonClsUrl?>', data,function(data){
		jQuery.each(data, function(k, cls){
			oClsList.options.add(new Option(cls.sclassname, cls.sclasstypecode));
		})
	}, 'json');
}

function doAddRule() {
	if(!jQuery('#class_semester').val() || !jQuery('#class_level').val() || !jQuery('#class_type').val() || !jQuery('#class_name').val()) {
		alert('分班信息不完整');
		return;
	}
	if ('0' == jQuery('#stu_group_id').val()) {
		alert('请选择学员筛选组');
		return;
	}
	if(!jQuery('#class_weight').val()) {
		alert('分班规则优先级必须录入');
		return;
	}
	var formData = jQuery('#clsRuleForm').serialize()
	jQuery.post('<?php echo $saveRuleUrl?>', formData, function(data){
		if(data.errorMsg) {
			alert(data.errorMsg)
		} else {
			alert('分班规则录入成功');
			jQuery('#ruleGrid').datagrid('reload');
			jQuery('#<?php echo $dlg?>').dialog('destroy');
		}
	}, 'json');
}
</script>