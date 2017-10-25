<style type="text/css">
.item{height:25px;line-height:25px;margin-top:3px}
.item b{display:block;float:left;width:100px}
</style>
<div class="easyui-layout" fit="true" border="false">
	<div region="north" style="height:130px">
		<div class="item"><b>&nbsp;预约组名称</b>：
		<input type="text" size="20" name="ygroup_caption" id="ygroup_caption" class="easyui-validatebox" required="true" value="<?php echo $yGroupInfo['ygroup_caption']?>"  />
		(中文填写，仅后台可见)
		</div>
		<div class="item"><b>&nbsp;预约开始时间</b>：
		<input type="text" name="start_time" id="start_time" size="20" class="easyui-datetimebox" required="true" value="<?php echo $yGroupInfo['ygroup_time_start']?>" />
		</div>
		<div class="item"><b>&nbsp;预约结束时间</b>：
		<input type="text" name="end_time" id="end_time" size="20" class="easyui-datetimebox" required="true" value="<?php echo $yGroupInfo['ygroup_time_end']?>" />
		</div>
		<div class="item"><b>&nbsp;区分新老生<input type="checkbox" id="stu_filter" name="stu_filter" value="1" <?php if($yGroupInfo['stu_filter']):?>checked="true"<?php endif?> /></b>：
		报班日期区间：<input type="text" size="10" name="study_date" id="study_date_start" class="stu_filter easyui-datebox" value="<?php echo $yGroupInfo['study_date_start']?>" /> - <input type="text" size="10" name="study_date_end" id="study_date_end"  value="<?php echo $yGroupInfo['study_date_end']?>" class="stu_filter easyui-datebox" />
		</div>
	</div>
	<div region="center">
		<table id="yExamGrid" class="easyui-datagrid" fit="true" border="false">
			<thead>
				<tr>
					<th field="exam_id" checkbox="true">竞赛ID</th>
					<th field="group_caption">竞赛组名称</th>
					<th field="exam_caption">竞赛名称</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($examList as $exam):?>
				<tr>
					<td><?php echo $exam['exam_id']?></td>
					<td><?php echo $exam['group_caption']?></td>
					<td><?php echo $exam['exam_caption']?></td>
				</tr>
				<?php endforeach;?>
			</tbody>
		</table>
	</div>
	<div region="south" style="height:30px">
		<a href="javascript:doEditGroup()" class="easyui-linkbutton" iconCls="icon-save" style="margin-top:3px">保存设置</a>
	</div>
</div>
<script type="text/javascript">
function doEditGroup() {
	var yGroupCaption = jQuery.trim(jQuery('#ygroup_caption').val());
	var startTime = jQuery.trim(jQuery('#start_time').datetimebox('getValue'));
	var endTime = jQuery.trim(jQuery('#end_time').datetimebox('getValue'));
	var stuFilter = jQuery('#stu_filter').attr('checked') ? 1 : 0;
	if(stuFilter) {
		var studyDateStart = jQuery('#study_date_start').datebox('getValue');
		var studyDateEnd = jQuery('#study_date_end').datebox('getValue');
	} else {
		var studyDateStart = '';
		var studyDateEnd = '';
	}
	if('' == yGroupCaption) {
		alert('请输入预约组名称');
	} else if('' == startTime) {
		alert('请选择预约开始时间');
	} else if('' == endTime) {
		alert('请选择预约结束时间');
	}else {
		if(stuFilter) {
			if('' == studyDateStart) {
				alert('请选择报班开始日期');
				return
			} else if('' == studyDateEnd) {
				alert('请选择报班结束日期')
			}
		}
		var seleExams = jQuery('#yExamGrid').datagrid('getSelections');
		if(0 == seleExams.length) {
			alert('请选择竞赛');
		} else {
			var eids = [];
			for(var idx in seleExams) {
				eids.push(seleExams[idx].exam_id);
			}
			data = {yGroupId:'<?php echo $yGroupId?>',
					eGroupId:'<?php echo $yGroupInfo['exam_group_id']?>', 
					yGroupCaption:yGroupCaption, 
					eids:eids.join(','),
					startTime:startTime,
					endTime:endTime,
					stuFilter:stuFilter,
					studyDateStart:studyDateStart,
					studyDateEnd:studyDateEnd}
			jQuery.post('<?php echo $editGroupUrl?>', data, function(data){
				if(data.errorMsg) {
					alert(data.errorMsg)
				} else {
					alert('预约组修改成功');
					jQuery('#yGroupGrid').datagrid('reload');
					jQuery('#<?php echo $dialog?>').dialog('destroy');
				}
			}, 'json');
		}
	}
}
var eids = ',<?php echo $yGroupInfo['exam_list']?>,';
jQuery('#yExamGrid').datagrid({
	onLoadSuccess:function(){
		var rows = jQuery('#yExamGrid').datagrid('getRows');
		for(var idx in rows) {
			var row = rows[idx];
			var patn = new RegExp(',' + row.exam_id + ',');
			if(patn.test(eids)) {
				jQuery('#yExamGrid').datagrid('selectRow',idx);
			}
		}
	}
})
</script>