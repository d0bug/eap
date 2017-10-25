<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/static/js/vip.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("#handouts_attribute").validate({
		rules: {
			subject: {
				required: true
			},
			'grade[]': {
				required: true
			},
			'knowledge[]': {
				required: true
			},
			permission: {
				required: true
			},
		},
		messages: {
			subject: {
				required: '请选择科目'
			},
			'grade[]': {
				required: '请选择课程属性'
			},
			'knowledge[]': {
				required: '请选择讲义属性'
			},
			permission: {
				required: '请选择权限'
			},
		},

		errorPlacement: function(error, element) {
			if (element.is(':radio') || element.is(':checkbox')) {
				var eid = element.attr('name');
				error.appendTo(element.parent());
			} else {
				error.insertAfter(element);
			}
		},
	});
})
</script>
<script type="text/javascript">

function select_grades(subjectid,grades_divid,knowledge_divid){
	if(subjectid != ''){
		$.get("<?php echo U('Vip/VipHandouts/change_grades_view')?>",
		{subjectid:subjectid,kdiv:knowledge_divid, _tm:(new Date()).getTime()},
		function(data){
			var obj = eval("("+data+")");
			$(grades_divid).html(obj.grade_str);
			if(knowledge_divid != ''){
				$(knowledge_divid).html(obj.knowledge_str);
			}
		}
		);
	}
}

function select_knowledge(gradeid,knowledge_divid){
	if(gradeid != '' && $('input[@name=subject][checked]').val()!=''){
		var grade_str="";
		$("input[name='grade[]']:checkbox").each(function(){
			if($(this).attr("checked")){
				grade_str += $(this).val()+",";
			}
		})
		var subjectid = $('input[@name=subject][checked]').val();
		$.get("<?php echo U('Vip/VipHandouts/change_knowledge_view')?>",
		{grade_str:grade_str,sid:subjectid, _tm:(new Date()).getTime()},
		function(data){
			$(knowledge_divid).html(data);
		}
		);
	}else{
		alert('请先选择科目和课程属性');
	}
}


</script>
</head>
<body>
<div region="center" >
<div id="main">
<?php if($is_jiaoyan == 1):?>
<div id="setAttribute" style="display:<?php if($permissionList == 1):?>none<?php else:?>block;<?php endif;?>">
<h2>讲义上传区目录添加</h2>
<div id="popup"></div>
<form id="handouts_attribute" name="handouts_attribute" method="POST" action="<?php echo U('Vip/VipJiaoyan/manage_attribute')?>" >
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
	<tr valign="top">
		<td class="alt" style="width:80px"><font color=red>*</font>科目：</td>
		<td>
			<div id="subject_div" class="selBar">
			<?php foreach($subjectArr as $key=>$subject):?>
				<input type="radio" name="subject" id="subject_<?php echo $subject['sid'];?>" value="<?php echo $subject['sid'];?>" title="<?php echo $subject['name'];?>" onclick="select_grades(this.value,'#grades_div','#knowledge_div')"><?php echo $subject['name'];?>&nbsp;&nbsp;
			<?php endforeach?>
			</div>
			<div class="btnBar">
			<?php if($permInfo['permValue']==3):?>
				<a href="#" onclick="add_item('subject',0,'<?php echo U('Vip/VipJiaoyan/get_form')?>')"><img src="/static/images/add.png"></a>&nbsp;&nbsp;&nbsp;
				<a href="#" onclick="return deleteAttribute('subject',0,'<?php echo U('Vip/VipJiaoyan/delete_attribute')?>')"><img src="/static/images/delete.png"></a>&nbsp;&nbsp;&nbsp;
				<a href="#" onclick="return testMessageBox_editAttribute(event,'subject','<?php echo U('Vip/VipJiaoyan/edit_attribute',array('ntype'=>0))?>')"><img src="/static/images/edit.png"></a>
			<?php endif;?>
			</div>
		</td>
	</tr>

	<tr valign="top">
		<td class="alt" style="width:80px"><font color=red>*</font>课程属性：</td>
		<td>
			<div id="grades_div" class="selBar">
			<?php foreach($gradeArr as $key=>$grade):?>
				<input type="checkbox" name="grade[]" id="grade_<?php echo $grade['gid'];?>" value="<?php echo $grade['gid'];?>" title="<?php echo $grade['name'];?>" onclick="select_knowledge(this.value,'#knowledge_div')" ><?php echo $grade['name'];?>&nbsp;&nbsp;
			<?php endforeach?>
			</div>
			<div class="btnBar">
			<?php if($permInfo['permValue']==3):?>
				<a href="#" onclick="add_item('grade',0,'<?php echo U('Vip/VipJiaoyan/get_form')?>')"><img src="/static/images/add.png"></a>&nbsp;&nbsp;&nbsp;
				<a href="#" onclick="return deleteAttribute('grade',0,'<?php echo U('Vip/VipJiaoyan/delete_attribute')?>')"><img src="/static/images/delete.png"></a>&nbsp;&nbsp;&nbsp;
				<a href="#" onclick="return testMessageBox_editAttribute(event,'grade','<?php echo U('Vip/VipJiaoyan/edit_attribute',array('ntype'=>0))?>')"><img src="/static/images/edit.png"></a>
			<?php endif;?>
			</div>
		</td>
	</tr>

	<tr valign="top">
		<td class="alt" style="width:80px"><font color=red>*</font>讲义属性：</td>
		<td>
			<div id="knowledge_div" class="selBar">
			<?php foreach($knowledgeArr as $key=>$knowledge):?>
				<input type="checkbox" name="knowledge[]" id="knowledge_<?php echo $knowledge['kid'];?>" value="<?php echo $knowledge['kid'];?>" title="<?php echo $knowledge['name'];?>"><?php echo $knowledge['name'];?>&nbsp;&nbsp;
			<?php endforeach?>
			</div>
			<div class="btnBar">
			<?php if($permInfo['permValue']==3):?>
				<a href="#" onclick="add_item('knowledge',0,'<?php echo U('Vip/VipJiaoyan/get_form')?>')"><img src="/static/images/add.png"></a>&nbsp;&nbsp;&nbsp;
				<a href="#" onclick="return deleteAttribute('knowledge',0,'<?php echo U('Vip/VipJiaoyan/delete_attribute')?>')"><img src="/static/images/delete.png"></a>&nbsp;&nbsp;&nbsp;
				<a href="#" onclick="return testMessageBox_editAttribute(event,'knowledge','<?php echo U('Vip/VipJiaoyan/edit_attribute',array('ntype'=>0))?>')"><img src="/static/images/edit.png"></a>
			<?php endif;?>
			</div>
		</td>
	</tr>
	
	<tr valign="top">
		<td class="alt" style="width:80px"> 年级属性：</td>
		<td>
			<div id="nianji_div" class="selBar">
			<?php foreach($nianjiArr as $key=>$nianji):?>
				<label><input type="checkbox" name="nianji[]" id="nianji_<?php echo $key;?>" value="<?php echo $key;?>"><?php echo $nianji;?></label>
			<?php endforeach?>
			</div>
		</td>
	</tr>
	
	<tr valign="top">
		<td class="alt" style="width:80px"> 课程用途：</td>
		<td>
			<div id="nianji_div" class="selBar">
				<textarea  cols="100" name="course_user" id="course_user_text" rows="8" onkeyup = "check_length('course_user_text','course_user_text_surplus',200)">请在此输入课程的用途、使用时间、针对学生类型等……</textarea>您<span id="course_user_text_surplus">还可以输入200个字</span>
			</div>
		</td>
	</tr>
	
	<tr valign="top">
		<td class="alt" style="width:80px"><font color=red>*</font>权&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;限：</td>
		<td>
			<div id="permission_div" class="selBar">
			<?php foreach($permissionArr as $key=>$permission):?>
				<label><input type="radio" name="permission" id="permission" value="<?php echo $key;?>" <?php if($key==0):?>checked<?php endif;?>><?php echo $permission;?>&nbsp;&nbsp;&nbsp;&nbsp;</label>
			<?php endforeach?>
			</div>
		</td>
	</tr>
	<tr valign="top">
		<td class="alt" style="width:80px">&nbsp;</td>
		<td><?php if($permInfo['permValue']==3):?><button type="submit" class="btn">提交</button><?php endif;?>&nbsp;</td>
	</tr>
</table>
</form>
<br />
<input type="button" class="btn" value=" 知识点权限信息一览表 " onclick="javascript:window.location.href='<?php echo U('Vip/VipJiaoyan/handouts_attribute',array('is_permissionList'=>1))?>';">
<br />
</div>
<div id="attributeList" style="display:<?php if($permissionList == 1):?>block<?php else:?>none;<?php endif;?>">
<h2>知识点权限信息一览表：</h2>
<div id="search">
<form id="permission_form" name="permission_form" action="<?php echo U('Vip/VipJiaoyan/handouts_attribute',array('is_permissionList'=>1))?>" method="GET">
	科目：<select id="sid" name="sid"  onchange="get_option(this.value,'gid','<?php echo U('Vip/VipHandouts/get_grades_option')?>','attr')">
			<option value="">请选择科目</option>
			<?php foreach($subjectArr as $key=>$subject):?>
				<option value="<?php echo $subject['sid']?>" ><?php echo $subject['name']?></option>
			<?php endforeach?>
		 </select>
	课程属性：<select id="gid" name="gid"  onchange="get_option(this.value,'kid','<?php echo U('Vip/VipHandouts/get_knowledge_option')?>','attr')">
			<option value="">请选择课程属性</option>
			<?php foreach($gradeArr as $key=>$grade):?>
				<option value="<?php echo $grade['gid']?>" ><?php echo $grade['name']?></option>
			<?php endforeach?>
		 </select>
	讲义属性：<select id="kid" name="kid">
			<option value="">请选择讲义属性</option>
			<?php foreach($knowledgeArr as $key=>$knowledge):?>
				<option value="<?php echo $knowledge['kid'];?>" ><?php echo $knowledge['name'];?></option>
			<?php endforeach?>
		   </select>
	年级：<select id="nid" name="nid">
			<option value="">请选择年级</option>
			<?php foreach($nianjiArr as $key=>$nianji):?>
				<option value="<?php echo $key;?>" ><?php echo $nianji;?></option>
			<?php endforeach?>
		 </select>
	<input type="submit" name="submit" value=" 查找 ">
</form>
</div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableList">
	<tr><th>科目</th><th>课程属性</th><th>讲义属性</th><th>权限</th><th width="25%">年级</th></tr>
	<?php foreach($knowledgePermissionArr as $key=>$permission):?>
	<tr><td><?php echo $permission['sname'];?></td><td><?php echo $permission['gname'];?></td><td><?php echo $permission['kname'];?></td><td><?php if($permission['permission']==1):?><font color=red>仅教研人员可见</font><?php else:?><font color=green>所有人可见</font><?php endif;?></td><td><?php echo $permission['nnames'];?></td></tr>
	<?php endforeach?>
</table><br>
<input type="button" class="btn" value=" 关闭 " onclick="javascript:window.location.href='<?php echo U('Vip/VipJiaoyan/handouts_attribute')?>'">
<br>
</div>
<?php else:?>
抱歉，您无权操作此功能！
<?php endif;?>
</div>
</div>
</body>
</html>