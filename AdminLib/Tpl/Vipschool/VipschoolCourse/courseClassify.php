<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/static/js/popup.js"></script>
<script language='javascript' src='/static/js/courseClassify.js'></script>
</head>
<body>
<div region="center">
<div id="main">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
					<tr valign="top">
						<td class="alt" style="width:80px"><font color=red>*</font>学部：</td>
						<td>
							<div id="grade_div" class="selBar">
							<?php foreach($gradeArr as $key=>$grade):?>
								<input type="radio" name="grade" id="grade<?php echo $grade['gid'];?>" value="<?php echo $grade['gid'];?>" title="<?php echo $grade['title'];?>" onclick="select_course_info('subject',this.value,'','','#subject_div','<?php echo U('Vipschool/vipschool_course/get_rel_courseinfo')?>')"><?php echo $grade['title'];?>&nbsp;&nbsp;
							<?php endforeach?>
							</div>
							<div class="btnBar">
								<a href="#" onclick="testMessageBox_addClassify(event,'grade','<?php echo U('Vipschool/vipschool_course/get_add_form')?>')"><img src="/static/images/add.png"></a>&nbsp;&nbsp;&nbsp;
								<a href="#" onclick="return delete_course_info('grade','<?php echo U('Vipschool/vipschool_course/delete_course_category')?>')"><img src="/static/images/delete.png"></a>&nbsp;&nbsp;&nbsp;
								<a href="#" onclick="return testMessageBox_edit_classify(event,'grade','<?php echo U('Vipschool/vipschool_course/edit_course_category')?>')"><img src="/static/images/edit.png"></a>
							</div>
						</td>
					</tr>
					<tr valign="top">
						<td class="alt" style="width:80px"><font color=red>*</font>学科：</td>
						<td>
							<div id="subject_div" class="selBar">
								<font color="red">请先选择学部！</font>
							</div>
							<div class="btnBar">
								<a href="#" onclick="testMessageBox_addClassify(event,'subject','<?php echo U('Vipschool/vipschool_course/get_add_form')?>')"><img src="/static/images/add.png"></a>&nbsp;&nbsp;&nbsp;
								<a href="#" onclick="return delete_course_info('subject','<?php echo U('Vipschool/vipschool_course/delete_course_category')?>')"><img src="/static/images/delete.png"></a>&nbsp;&nbsp;&nbsp;
								<a href="#" onclick="return testMessageBox_edit_classify(event,'subject','<?php echo U('Vipschool/vipschool_course/edit_course_category')?>')"><img src="/static/images/edit.png"></a>
							</div>
						</td>
					</tr>
					<tr valign="top">
						<td class="alt" style="width:80px"><font color=red>*</font>一级分类：</td>
						<td>
							<div id="classify_div" class="selBar">
								<font color="red">请先选择学科！</font>
							</div>
							<div class="btnBar">
								<a href="#" onclick="testMessageBox_addClassify(event,'classify','<?php echo U('Vipschool/vipschool_course/get_add_form')?>')"><img src="/static/images/add.png"></a>&nbsp;&nbsp;&nbsp;
								<a href="#" onclick="return delete_course_info('classify','<?php echo U('Vipschool/vipschool_course/delete_course_category')?>')"><img src="/static/images/delete.png"></a>&nbsp;&nbsp;&nbsp;
								<a href="#" onclick="return testMessageBox_edit_classify(event,'classify','<?php echo U('Vipschool/vipschool_course/edit_course_category')?>')"><img src="/static/images/edit.png"></a>
							</div>
						</td>
					</tr>
					<tr valign="top">
						<td class="alt" style="width:80px"><font color=red>*</font>二级分类：</td>
						<td>
							<div id="twoClassify_div" class="selBar">
								<font color="red">请先选择一级分类！</font>
							</div>
							<div class="btnBar">
								<a href="#" onclick="testMessageBox_addClassify(event,'twoClassify','<?php echo U('Vipschool/vipschool_course/get_add_form')?>')"><img src="/static/images/add.png"></a>&nbsp;&nbsp;&nbsp;
								<a href="#" onclick="return delete_course_info('twoClassify','<?php echo U('Vipschool/vipschool_course/delete_course_category')?>')"><img src="/static/images/delete.png"></a>&nbsp;&nbsp;&nbsp;
								<a href="#" onclick="return testMessageBox_edit_classify(event,'twoClassify','<?php echo U('Vipschool/vipschool_course/edit_course_category')?>')"><img src="/static/images/edit.png"></a>
							</div>
						</td>
					</tr>
					<tr valign="top">
						<td class="alt" style="width:80px"><font color=red>*</font>三级分类：</td>
						<td>
							<div id="threeClassify_div" class="selBar">
								<font color="red">请先选择二级分类！</font>
							</div>
							<div class="btnBar">
								<a href="#" onclick="testMessageBox_addClassify(event,'threeClassify','<?php echo U('Vipschool/vipschool_course/get_add_form')?>')"><img src="/static/images/add.png"></a>&nbsp;&nbsp;&nbsp;
								<a href="#" onclick="return delete_course_info('threeClassify','<?php echo U('Vipschool/vipschool_course/delete_course_category')?>')"><img src="/static/images/delete.png"></a>&nbsp;&nbsp;&nbsp;
								<a href="#" onclick="return testMessageBox_edit_classify(event,'threeClassify','<?php echo U('Vipschool/vipschool_course/edit_course_category')?>')"><img src="/static/images/edit.png"></a>
							</div>
						</td>
					</tr>
					<tr valign="top">
						<td class="alt" style="width:80px"><font color=red>*</font>四级分类：</td>
						<td>
							<div id="fourClassify_div" class="selBar">
								<font color="red">请先选择三级分类！</font>
							</div>
							<div class="btnBar">
								<a href="#" onclick="testMessageBox_addClassify(event,'fourClassify','<?php echo U('Vipschool/vipschool_course/get_add_form')?>')"><img src="/static/images/add.png"></a>&nbsp;&nbsp;&nbsp;
								<a href="#" onclick="return delete_course_info('fourClassify','<?php echo U('Vipschool/vipschool_course/delete_course_category')?>')"><img src="/static/images/delete.png"></a>&nbsp;&nbsp;&nbsp;
								<a href="#" onclick="return testMessageBox_edit_classify(event,'fourClassify','<?php echo U('Vipschool/vipschool_course/edit_course_category')?>')"><img src="/static/images/edit.png"></a>
							</div>
						</td>
					</tr>
			</table>

</div>
</div>
</body>
</html>
