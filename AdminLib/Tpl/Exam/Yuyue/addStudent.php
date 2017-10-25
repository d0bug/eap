<div class="easyui-layout addYStuForm" fit="true">
	<div region="north" style="height:150px" border="false" border="false">
		<div id="formToolbar">
			&nbsp;&nbsp;考生查询：<input type="text" size="30" name="keyword" id="stuKeyword" value="" placeholder="姓名/准考证号/学号/电话" />
			<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search" onclick="searchStudent()" plain="true">查询</a>
		</div>
		<table id="stuSearchGrid" class="easyui-datagrid" toolbar="#formToolbar" rownumbers="true" singleselect="true" title="根据考生查询预约组" iconCls="icon-search" fit="true">
			<thead>
			<tr>
				<th field="sname">姓名</th>
				<th field="saliascode">考生学号</th>
				<th field="sgrade">考生年级</th>
				<th field="exam_caption">参加竞赛</th>
				<th field="exam_code">准考证号</th>
				<th field="ygroup_caption">预约组</th>
			</tr>
			</thead>
		</table>
	</div>
	<div region="center" border="false" title="预约信息" iconCls="icon-tip">
		<div class="easyui-layout" fit="true">
			<div region="west" style="width:300px">
				<fieldset>
					<legend style="font-size:14px;font-weight:bold">考生信息</legend>
					<table width="100%" cellpadding="2">
						<tr><th>考生姓名</th><td><input type="text" class="sname" size="15" readonly="true" /></td></tr>
						<tr><th>考生编码</th><td><input type="text" class="scode" size="15" readonly="true" /></td></tr>
						<tr><th>考生学号</th><td><input type="text" class="saliascode" size="15" readonly="true" /></td></tr>
						<tr><th>学生年级</th><td><input type="text" class="sgrade" size="15" readonly="true" /></td></tr>
						<tr><th>参加考试</th><td><input type="text" class="exam_caption" size="15" readonly="true" /></td></tr>
						<tr><th>准考证号</th><td><input type="text" class="exam_code" size="15" readonly="true" /></td></tr>
						<!--tr><th>电&nbsp;话&nbsp;一</th><td><input type="text" class="sparents1phone" size="15" readonly="true" /></td></tr>
						<tr><th>电&nbsp;话&nbsp;二</th><td><input type="text" class="sparents2phone" size="15" readonly="true" /></td></tr-->
					</table>
				</fieldset>
			</div>
			<div region="center">
				<fieldset>
					<legend style="font-size:14px;font-weight:bold">预约信息</legend>
					<form id="yStuForm">
						<input type="hidden" name="stu_code" class="scode" id="stu_code" />
						<input type="hidden" name="ygid" class="ygroup_id" id="ygid" />
						<input type="hidden" name="isnew" id="isnew" class="is_new" />
						<input type="hidden" name="exam_id" id="exam_id" class="exam_id" />
						<table width="100%" cellpadding="5">
							<tr><th>预 约 组</th><td><input type="text" style="width:225px" class="ygroup_caption" size="15" readonly="true" /></td></tr>
							<tr><th>诊断地点</th><td><select style="width:230px" name="pos_id" id="pos_id" onchange="initYForm()"></select></td></tr>
							<tr><th>诊断日期</th><td><select style="width:230px" name="yuyue_date" id="yuyue_date" onchange="initYForm()"></select></td></tr>
							<tr><th>诊断时间</th><td><select style="width:230px" name="batch_id" id="batch_id"></select></td></tr>
							<tr><td colspan="2"><a href="javascript:void(0)" onclick="saveYuyueInfo()" class="easyui-linkbutton" iconCls="icon-ok">保存预约信息</a></td></tr>
						</table>
					</form>
				</fieldset>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
var ajaxFormInfo = true;
function searchStudent() {
	var keyword = jQuery.trim(jQuery('#stuKeyword').val());
	if(keyword) {
		jQuery('#stuSearchGrid').datagrid({
			url:'<?php echo $searchStuUrl?>',
			queryParams:{egid:curEGroup, keyword:keyword},
			onSelect:function(idx, data) {
				jQuery.each(data, function(k, v){
					jQuery('.addYStuForm').find('.' + k).val(v);
				})
				initYForm();
			},
			onLoadSuccess:function(data){
				if(data.rows.length == 0) {
					alert('考生没有报名');
				}
			}
		})
	} else {
		alert('请输入查询关键词');
	}
}

function initYForm() {
	if(false == ajaxFormInfo) return;
	var formData = jQuery('#yStuForm').serialize();
	
	jQuery('#yuyue_date').html('');
	jQuery('#batch_id').html('');
	jQuery.post('<?php echo $jsonBatchUrl?>', formData, function(data){
		ajaxFormInfo = false;
		if(data.errorMsg) {
			ajaxFormInfo = true;
			alert(data.errorMsg)
		} else {
			if(data.posList) {
				jQuery('#pos_id').html('');
				jQuery.each(data.posList, function(k,pos){
					jQuery('<option value="' + pos.pos_id + '">' + pos.pos_caption + '</option>').appendTo('#pos_id');
				})
				jQuery('#pos_id').val(data.pos_id);
			}
			if(data.dateList){
				jQuery.each(data.dateList, function(date, dateCaption){
					jQuery('<option value="' + date + '">' + dateCaption + '</option>').appendTo('#yuyue_date');
				})
				jQuery('#yuyue_date').val(data.yuyueDate);
			}
			if(data.timeList) {
				jQuery.each(data.timeList, function(k, time){
					jQuery('<option value="' + time.bid + '">' + time.time_text + '</option>').appendTo('#batch_id');
				})
			}
			ajaxFormInfo = true;
		}
	},'json')
}

function saveYuyueInfo() {
	var formData = jQuery('#yStuForm').serialize();
	if(jQuery('#batch_id').val() && jQuery('#stu_code').val()){
		jQuery.post('<?php echo $saveYuyueInfoUrl?>', formData, function(data){
			if(data.errorMsg) {
				alert(data.errorMsg);
			} else {
				alert('预约成功');
				curYGroup = jQuery('#ygid').val();
				loadStudent();
				jQuery('#<?php echo $dialog?>').dialog('destroy');
			}
		}, 'json');
	} else {
		alert('预约信息不完整');
	}
}
</script>