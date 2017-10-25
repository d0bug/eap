<style type="text/css">
#clsInfoUL{margin:0px;padding:2px 0px 0px 5px}
#clsInfoUL li{list-style-type:none;line-height:34px;}
#clsInfoUL li span{font-weight:bold}
#clsInfoUL li a span{margin-top:-5px}
#clsInfoUL input{padding:2px;height:22px;color:blue}
</style>
<div class="easyui-layout" fit="true">
	<div region="north" style="height:40px">
		<div style="padding:5px">
		<b>&nbsp;&nbsp;班级编码:</b>
		<input onfocus="this.select()" style="padding:2px;height:22px;ime-mode:disabled;text-transform:uppercase" type="text" id="search_class_code" size="10" maxlength="9" />
		<a href="javascript:void(0)" id="searchClassBtn" class="easyui-linkbutton" iconCls="icon-search" plain="true">确定</a>
		</div>
	</div>
	<div region="center">
		<fieldset>
			<legend><b>班级信息</b></legend>
			<form id="clsInfoForm">
			<input type="hidden" name="sgrade" id="sgrade" />
			<input type="hidden" name="charm_group" id="charm_group" />
			<input type="hidden" name="sprintaddress" id="sprintaddress" />
			<input type="hidden" name="sareacode" id="sareacode" />
			<input type="hidden" name="sdeptcode" id="sdeptcode" />
			<input type="hidden" name="sclasstypecode" id="sclasstypecode" />
			<input type="hidden" name="dfee" id="dfee" />
			<ul id="clsInfoUL">
				<li><span>班级编码：</span><input type="text" name="sclasscode" size="30" id="scode" readonly="true" /></li>
				<li><span>班级名称：</span><input type="text" name="sclassname" size="30" id="sname" readonly="true" /></li>
				<li><span>上课地点：</span><input type="text" name="sareaname" size="30" id="sareaname" readonly="true" /></li>
				<li><span>开课日期：</span><input type="text" name="sbegindate" size="30" id="sbegindate" readonly="true" /></li>
				<li><span>结课日期：</span><input type="text" name="senddate" size="30" id="senddate" readonly="true" /></li>
				<li><span>上课时间：</span><input type="text" name="sprinttime" size="30" id="sprinttime" readonly="true" /></li>
				<li><span>授课教师：</span><input type="text" name="sprintteachers" size="30" id="sprintteachers" readonly="true" /></li>
				<li><span>满班人数：</span><input type="text" name="nnormalcount" size="30" id="nnormalcount" readonly="true" /></li>
				<li><span>上课年级：</span><input type="text" name="sgradestudent" size="30" id="sgradestudent" readonly="true" /></li>
				<li><a style="line-height:24px" href="javascript:void(0)" onclick="saveClassInfo()" class="easyui-linkbutton" iconCls="icon-save">保存班级信息</a></li>
			</ul>
			</form>
		</fieldset>
	</div>
</div>
<script type="text/javascript">
jQuery(function(){
	jQuery('#searchClassBtn').click(function(){
		var searchClassCode = jQuery.trim(jQuery('#search_class_code').val().toUpperCase());
		if(searchClassCode) {
			if(false == /^BJ\d{2}[CSQH]\d{4}$/i.test(searchClassCode)) {
				alert('班级编码不合法');
				return;
			}
			jQuery.post('<?php echo $clsInfoUrl?>', {clsCode:searchClassCode}, function(data){
				jQuery('#charm_group').val(curGroup);
				jQuery.each(data, function(k,v){
					jQuery('#clsInfoForm').find('#' + k).val(v);
				})
			}, 'json');
		}
	})
})

function saveClassInfo() {
	if(jQuery('#sname').val()) {
		var formData = jQuery('#clsInfoForm').serialize();
		jQuery.post('<?php echo $saveClassUrl?>', formData, function(data){
			if(data.errorMsg) {
				alert(data.errorMsg);
			} else {
				alert('试听班级添加成功');
				jQuery('body').layout('remove', 'east');
				jQuery('#classGrid').datagrid('reload');
			}
		}, 'json')
	} else {
		alert('班级信息不完整');
	}
}
</script>