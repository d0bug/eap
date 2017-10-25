<div class="easyui-layout" fit="true" border="false">
	<div region="center">
		<div id="timeToolbar" style="background:#eee">
		<!--a href="javascript:addTime()" class="easyui-linkbutton" plain="true" iconCls="icon-add">添加时间</a>
		<a href="javascript:addTime()" class="easyui-linkbutton" plain="true" iconCls="icon-edit">修改时间</a>
		<a href="javascript:addTime()" class="easyui-linkbutton" plain="true" iconCls="icon-cancel">删除时间</a>
		<span class="datagrid-btn-separator"></span-->
		<form id="tm_form">
		<input type="hidden" name="time_id" id="time_id" />
		<input type="hidden" name="ygroup_id" value="<?php echo $yGroupId?>" />
		<a href="javascript:void(0)" id="actButton" class="easyui-linkbutton" plain="true" iconCls="icon-add">快捷添加:</a>
		诊断时间：
		<input type="text" class="easyui-validatebox easyui-timespinner" id="time_start" name="time_start" required="true" size="10" />
		—
		<input type="text" class="easyui-validatebox easyui-timespinner" id="time_end" name="time_end" required="true" size="10" />
		<label><input type="checkbox" name="is_weekend" id="is_weekend" value="1" style="margin-top:3px" />仅周末适用</label>
		<a href="javascript:doSaveTime()" class="easyui-linkbutton" iconCls="icon-save" plain="false">保存</a>
		<a href="javascript:resetForm()" id="resetButton" class="easyui-linkbutton" iconCls="icon-cancel" style="display:none" plain="false">取消</a>
		</a>
		</div>
		<table id="timeGrid" class="easyui-datagrid" fit="true" rownumbers="true" singleselect="true" border="false" url="<?php echo $jsonTimeUrl?>/ygid/<?php echo $yGroupId?>" toolbar="#timeToolbar">
			<thead>
				<tr>
					<th field="time_text" formatter="timeText">时间段</th>
					<th field="time_start">开始时间</th>
					<th field="time_end">结束时间</th>
					<th field="is_weekend" formatter="bFormatter" align="center">仅周末</th>
					<th field="manage" formatter="manage" align="center">管理</th>
				</tr>
			</thead>
		</table>
	</div>
</div>
<script type="text/javascript">
function resetForm() {
	jQuery('#actButton').linkbutton({text:'快捷添加',iconCls:'icon-add'})
	jQuery('#time_id').val('');
	jQuery('#time_start').val('');
	jQuery('#time_end').val('');
	jQuery('#resetButton').hide();
}

function doEditTime(tid,tStart,tEnd,tWeekend) {
	jQuery('#actButton').linkbutton({text:'快捷修改',iconCls:'icon-edit'})
	jQuery('#time_id').val(tid);
	jQuery('#time_start').val(tStart);
	jQuery('#time_end').val(tEnd);
	jQuery('#is_weekend').attr('checked', tWeekend == '1');
	jQuery('#resetButton').show();
}

function doDelTime(tid, tText) {
	if(confirm('确定删除诊断时间“' + tText + '”吗？')) {
		jQuery.post('<?php echo $delTimeUrl?>', {tid:tid}, function(data){
			if(data) {
				alert('诊断时间删除成功');
				jQuery('#timeGrid').datagrid('reload');
			} else {
				alert('诊断时间删除失败');
			}
		}, 'json');
	}
}

function doSaveTime() {
	var bIsValid = true;
	jQuery('#tm_form').find('.easyui-validatebox').each(function(){
		if(false ==jQuery(this).validatebox('isValid')) {
			bIsValid = false;
		}
	})
	if(false == bIsValid) {
		alert('诊断时间信息不完整');
	}
	var formData = jQuery('#tm_form').serialize();
	jQuery.post('<?php echo $saveTimeUrl?>', formData, function(data){
		if(data.errorMsg) {
			alert(data.errorMsg);
		} else {
			alert('诊断时间保存成功');
			if(jQuery('#time_id').val()) {
				resetForm();
			}
			jQuery('#timeGrid').datagrid('reload');
		}
	}, 'json');
}

function manage(val, data) {
	return '<a href="javascript:doEditTime(\'' + data.time_id + '\', \'' + data.time_start + '\', \'' + data.time_end + '\',\'' + data.is_weekend + '\')">修改</a> | <a href="javascript:doDelTime(\'' + data.time_id + '\', \'' + data.time_text + '\')">删除</a>';
}


function timeText(val) {
	return '<b style="color:blue">' + val + '</b>'
}

function bFormatter(val, data) {
	if(val == 1) {
		return '<span style="color:green">是</span>';
	} else {
		return '<span>否</span>'
	}
}
</script>