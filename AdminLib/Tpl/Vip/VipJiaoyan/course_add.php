<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<link href="/static/css/question.css" type="text/css" rel="stylesheet" />
<link href="/static/ueditor1_4_3/themes/default/_css/ueditor.css" type="text/css" rel="stylesheet">
</head>
<body>
	<div id="basic-index-question-layout" class="easyui-layout" fit="true">
		<div region="center" data-options="fit: true, split: false, collapsible: false, border: false">
			<form id="course-add-form" method="post" novalidate>
				<input type="hidden" name="level" value="<?php echo $level?>" />
				<input type="hidden" name="knowledgetypeid" value="<?php echo $knowledgetypeid?>" />
				<input type="hidden" name="parent_id" id="course_add_form_parent_id" value="<?php echo $parentId?>" />
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
					<tr>
						<td class="alt right wd_120">上级知识点：</td>
						<td>
							<a href="#" id="course_add_form_path">{$path}</a>
						</td>
					</tr>
					<tr>
						<td class="alt right"><span class="red">*</span> 名称：</td>
						<td>
							<input type="text" name="name" value="" class="easyui-validatebox input wd_400" data-options="required: true" autocomplete="off" maxlength="100" />
						</td>
					</tr>
					<tr>
						<td class="alt right"><span class="red">*</span> 排序：</td>
						<td>
							<input type="text" name="sort" value="" class="easyui-numberbox input wd_50" data-options="required: true, min: 1, precision: 0" autocomplete="off" maxlength="6" />
						</td>
					</tr>
				</table>
			</form>
		</div>
		<div region="south" style="height: 32px;" data-options="collapsible: false, border: false, split: false">
			<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-save'" onclick="course_save()">保存</a>
		</div>
	</div>
	<!--Begin 知识点选择对话框-->
	<div id="knowledge-add-dlg" class="easyui-dialog" title="课程体系" data-options="iconCls:'icon-table',modal:true,closed:true" style="width:600px;height:350px;padding:5px;"></div>
	<!--End 知识点选择对话框-->
	<script language='javascript' type='text/javascript'>
		$(function() {
			//UE.getEditor('analysis', {
			//	initialFrameHeight: 100
			//});
			$('#course_add_form_path').tooltip({
				content: $('<div></div>'),
				showEvent: 'click',
				onUpdate: function(content){
					content.panel({
						width: 405,
						height: 200,
						border: false,
						cache: false,
						href: '/Vip/VipJiaoyan/courses?knowledgetypeid=<?php echo $knowledgetypeid?>'
					});
				},
				onShow: function(){
					var t = $(this);
						t.tooltip('tip').unbind().bind('mouseenter', function(){
						t.tooltip('show');
						}).bind('mouseleave', function(){
						t.tooltip('hide');
					});
				},
				onPosition: function(){
					$(this).tooltip('tip').css('left', $(this).offset().left);
					$(this).tooltip('arrow').css('left', 20);
				}
			});
		});
		function course_save() {
			$('#course-add-form').form('submit', {
				url: '/Vip/VipJiaoyan/course_add_save',
				onSubmit: function () {
					return $(this).form('validate');
				},
				success: function (result) {
					var result = eval('(' + result + ')');
					if (result.status) {
						parent._initCourse('<?php echo $knowledgetypeid?>');
						parent.$('#basic-index-knowledge-dlg').dialog('close');
					} else {
						$.messager.alert('错误信息', result.message, 'error');
					}
				}
			});
		}
	</script>
</body>
</html>