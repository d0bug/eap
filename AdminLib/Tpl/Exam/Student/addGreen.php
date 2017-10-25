<style type="text/css">
#addGreenDL dt, #addGreenDL dd{margin:0px;padding-left:15px;line-height:26px}
dt span, dd span{font-weight:bold}
</style>
<div class="easyui-layout" fit="true" border="false">
	<div region="center">
		<div id="greenSearchToolbar">
		&nbsp;&nbsp;查询:<input type="text" id="green_keyword" placeholder="姓名/电话/学号/编号" />
		<a href="javascript:searchGreen()" class="easyui-linkbutton" iconCls="icon-search">查询</a>
		</div>
		<table id="stuSearchGrid" fit="true" url="<?php echo $jsonSearchUrl?>" singleselect="true" rownumbers="true" toolbar="#greenSearchToolbar" border="false" >
			<thead>
				<tr>
					<th field="sname" width="60">姓名</th>
					<th field="saliascode">性别</th>
					<th field="stu_birth">生日</th>
					<th field="grade_text">年级</th>
					<th field="sparents1phone">电话1</th>
					<th field="sparents2phone">电话2</th>
				</tr>
			</thead>
		</table>
	</div>
	<div region="east" style="width:300px">
		<dl id="addGreenDL">
			<form method="POST" action="<?php echo $addGreenUrl?>">
			<fieldset>
			<legend style="font-size:14px;font-weight:bold;color:blue"><?php echo $examInfo['group_caption'] . '-' . $examInfo['exam_caption']?></legend>
			<input type="hidden" name="exam_id" id="green_exam_id" value="<?php echo $examInfo['exam_id']?>" />
			<input type="hidden" name="stu_code" id="green_stu_code" value="" />
			<dd>学生姓名：<input type="text" id="green_stu_name" /></dd>
			<dd>学生学号：<input type="text" id="saliascode" /></dd>
			<dd>准考证号：<input type="text" id="exam_code" name="exam_code" /></dd>
			<dd>报名前台：<select name="area_code" id="area_code">
			<option value="0">选择报名前台</option>
			<?php foreach ($areaOptions as $areaCode=>$areaCaption):?>
			<option value="<?php echo $areaCode?>">[<?php echo $areaCode?>] <?php echo $areaCaption?></option>
			<?php endforeach;?>
			<option value="ADMIN">后台管理</option>
			</select></dd>
			<dd>&nbsp;</dd>
			<dd><a href="javascript:saveGreen()" class="easyui-linkbutton" iconCls="icon-save">保存绿色通道考生</a></dd>
			</fieldset>
			</form>
		</dl>
	</div>
</div>
<script type="text/javascript">
jQuery('#stuSearchGrid').datagrid({
	onSelect:function(idx, data){
		jQuery('#green_stu_code').val(data.scode);
		jQuery('#green_stu_name').val(data.sname);
		jQuery('#saliascode').val(data.saliascode);
	}
})
function searchGreen(){
	var data = {keyword:jQuery.trim(jQuery('#green_keyword').val())}
	jQuery('#stuSearchGrid').datagrid('reload', data)
}

function saveGreen() {
	var examId = Math.abs(jQuery('#green_exam_id').val());
	var stuCode = jQuery.trim(jQuery('#green_stu_code').val());
	var areaCode = jQuery.trim(jQuery('#area_code').val())
	var examCode = jQuery.trim(jQuery('#exam_code').val())
	if(examId == 0) {
		alert('非法操作');
	} else if (stuCode.length == 0) {
		alert('请先筛选并选择学生信息');
	} else if(areaCode == 0){
		alert('请选择缴费前台');
	}else {
		if(confirm('确定要添加“' + jQuery('#green_stu_name').val() + '”为竞赛【<?php echo $examInfo['group_caption'] . '-' . $examInfo['exam_caption']?>】的绿色通道考生吗？')) {
			jQuery.post('<?php echo $addGreenUrl?>', {examId:examId, stuCode:stuCode, areaCode:areaCode, examCode:examCode}, function(data){
				if(data.errorMsg) {
					alert(data.errorMsg);
				} else {
					alert('绿色通道考生添加成功！');
					jQuery('#greenGrid').datagrid('reload');
					jQuery('#dlg_<?php echo $dlgId?>').dialog('destroy');
				}
			},'json');
		}
	}
}
</script>