<div class="easyui-layout" fit="true" border="false">
	<div region="north" style="height:26px;background:#eee" >
	<a href="javascript:doAddBatch()" class="easyui-linkbutton" iconCls="icon-save" plain="true">保存</a>
	</div>
	<div region="center">
		<div class="easyui-layout" fit="true" border="false">
			<div region="north" style="height:30px" border="false">
				<div style="height:26px;border:1px solid #ccc;border-top:none;padding-top:3px">
				<?php if($groupInfo['stu_filter']):?>
					<b>&nbsp;单场时间段内</b>：新生人数<input type="text" size="12" class="easyui-validatebox easyui-numberspinner" required="true" name="new_cnt" id="new_cnt" />，老生人数<input type="text" size="12" class="easyui-numberspinner" required="true" name="old_cnt" id="old_cnt" />
				<?php else:?>
					<b>&nbsp;单场时间段内</b>：预约总人数<input type="text" size="12" class="easyui-numberspinner" required="true" id="total_cnt" name="total_cnt"/>
				<?php endif?>
				</div>
			</div>
			<div region="west" style="width:210px">
				<table class="easyui-datagrid" id="bPosGrid" fit="true" rownumbers="true" title="请选择诊断地点" iconCls="icon-redo" border="false" url="<?php echo $jsonPosUrl?>" singleselect="true">
					<thead>
						<tr>
							<th field="pos_id" checkbox="true"></th>
							<th field="pos_caption">诊断地点名称</th>
						</tr>
					</thead>
				</table>
			</div>
			<div region="center">
				<table class="easyui-datagrid" id="bDateGrid" fit="true" rownumbers="true" title="请选择诊断日期" iconCls="icon-redo" border="false">
					<thead>
						<tr>
							<th field="date" checkbox="true"></th>
							<th field="caption">日期</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach($dateArray as $date=>$caption):?>
						<tr>
							<td><?php echo $date?></td>
							<td><?php echo $caption?></td>
						</tr>
					<?php endforeach;?>
					</tbody>
				</table>
			</div>
			<div region="east" style="width:230px">
				<table class="easyui-datagrid" id="bTimeGrid" fit="true" rownumbers="true" title="请选择诊断时间" iconCls="icon-redo" border="false" url="<?php echo $jsonTimeUrl?>">
					<thead>
						<tr>
							<th field="time_id" checkbox="true"></th>
							<th field="time_text" formatter="tFormatter">诊断时间</th>
							<th field="is_weekend" formatter="wFormatter" align="center">周末适用</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
function tFormatter(val) {
	return '<b>' + val + '</b>';
}
function wFormatter(val) {
	if(val == '1') {
		return '<span style="color:blue">是</span>';
	} else {
		return '<span style="color:red">否</span>';
	}
}

function doAddBatch() {
	var isValid = true;
	jQuery('#new_cnt,#old_cnt,#total_cnt').each(function(){
		if(false == jQuery(this).validatebox('isValid')) {
			isValid = false;
		}
	})
	if(false == isValid) {
		alert('请正确填写单场人数限额');
		return;
	}
	var data = {'yGroupId':'<?php echo $yGroupId?>'};
	<?php if($groupInfo['stu_filter']):?>
	data['stu_filter'] = 1;
	data['new_cnt'] = jQuery('#new_cnt').numberspinner('getValue');
	data['old_cnt'] = jQuery('#old_cnt').numberspinner('getValue');
	data['total_cnt'] = Math.abs(data['new_cnt']) + Math.abs(data['old_cnt']);
	<?php else:?>
	data['stu_filter'] = 0;
	data['new_cnt'] = 0;
	data['old_cnt'] = 0;
	data['total_cnt'] = jQuery('#total_cnt').numberspinner('getValue');
	<?php endif?>
	if(0 == data.total_cnt) {
		alert('请正确填写单场人数限额');
		return;
	}
	var selePos = jQuery('#bPosGrid').datagrid('getSelected');
	if(!selePos) {
		alert('请选择诊断地点');
		return;
	} else {
		data['pos_id'] = selePos.pos_id;
	}
	var seleDate = jQuery('#bDateGrid').datagrid('getSelections');
	if(0 == seleDate.length) {
		alert('请选择诊断日期');
		return;
	} else {
		var dtArr = [];
		jQuery(seleDate).each(function(i, dt){
			dtArr.push(dt.date);
		})
		data['date'] = dtArr.join(',');
	}
	var seleTime = jQuery('#bTimeGrid').datagrid('getSelections');
	if(0 == seleTime.length) {
		alert('请选择诊断时间');
		return;
	} else {
		var tmArr = [];
		jQuery(seleTime).each(function(i, tm){
			tmArr.push(tm.time_id);
		})
		data['time'] = tmArr.join(',');
	}
	jQuery.post('<?php echo $addBatchUrl?>', data, function(data){
		if(data.errorMsg) {
			alert(data.errorMsg);
		} else {
			alert('诊断场次保存成功');
			jQuery('#<?php echo $dialog?>').dialog('destroy');
			jQuery('#batchGrid').datagrid('reload');
		}
	}, 'json')
}
</script>