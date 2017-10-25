选择目标位置：<br>
<div class="m_left_20">
	<span id="loader2"></span>
	<ul id="tree2"  class="easyui-tree" data-options="url:'<?php echo $menuTreeUrl;?>',onClick:function(node){get_filter_url(node.attributes);},onBeforeLoad:function(node, param){$('#loader2').html('<img src=/static/images/loading.gif/> 正在加载菜单，请稍后...')},onLoadSuccess:function(node, data){$('#loader2').html('')}"></ul>
</div><br>
<form method="post" id="moveModuleForm"  enctype="multipart/form-data">
选择目标课程：
<div class="m_left_20">
	<select id="kecheng_code" name="kecheng_code" onchange="get_lessonList(this.value)">
		<option value="">请选择目标课程</option>
	</select>
</div>
选择目标讲次：
<div class="m_left_20">
	<select id="lesson_no" name="lesson_no">
		<option value="">请选择目标讲次</option>
	</select>
</div>
	<input type="hidden" id="idStr" name="idStr" value="<?php echo $idStr?>" size="100"><br>
	<input type="hidden" id="filter_str" name="filter_str" value="" size="100"></span>
    <div><a href="javascript:void(0)" onclick="check_moveModuleForm();" class="easyui-linkbutton" iconCls="icon-save" >确定移动文档</a></div>
</form>
<script type="text/javascript">
function check_moveModuleForm(){
	if($('#filter_str').val()==''){
		alert('请选择目标位置');
		return false;
	}
	if($('#kecheng_code').val()==''){
		alert('请选择目标课程');
		return false;
	}
	if($('#lesson_no').val()==''){
		alert('请选择目标讲次');
		return false;
	}
	$.post('<?php echo $doMoveUrl;?>'+$('#filter_str').val(),
	{idStr:$('#idStr').val(),kechengCode:$('#kecheng_code').val(),lessonNo:$('#lesson_no').val()},
	function(data){
		var obj = eval('(' + data + ')');
		alert(obj.msg);
		if(obj.status == 1){
			jQuery('#fileGrid').datagrid('reload');
		}
	}
	);
}

function get_filter_url(url){
	jQuery('#kecheng_code').val('');
	jQuery('#kecheng_code').html('<option value="">请选择目标课程</option>');
	jQuery('#lesson_no').val('');
	jQuery('#lesson_no').html('<option value="">请选择目标讲次</option>');
	jQuery('#filter_str').val(url);
	$.get('<?php echo $getKechengUrl;?>'+$('#filter_str').val(),
	function(data){
		var obj = eval('(' + data + ')');
		if(obj.status == 1){
			$('#kecheng_code').html(obj.html);
		}
	}
	);
}

function get_lessonList(kecheng_code){
	if(kecheng_code != '' && $('#filter_str').val()!=''){
		$.get('<?php echo $getLessonNoUrl;?>'+$('#filter_str').val(),
		{kechengCode:kecheng_code},
		function(data){
			var obj = eval('(' + data + ')');
			if(obj.status == 1){
				$('#lesson_no').html(obj.html);
			}
		}
		);
	}
}

</script>