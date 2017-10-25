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
			<form id="knowledge-edit-form" method="post" novalidate>
				<input type="hidden" name="id" value="<?php echo $course['id']?>" />
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
					<tr>
						<td class="alt right wd_120">上级知识点：</td>
						<td>
							<input name="parent_name" class="easyui-combotree" style="width: 300px;" data-options="url: '/Vip/VipJiaoyan/getCourseChilds?knowledgetypeid=<?php echo $knowledgetypeid;?>',value:'<?php if($course['parent_id']!=0):?><?php echo $course['parent_name'];?><?php endif;?>',method:'get',lines:true" />
							<input type="hidden" id="parent_id" name="parent_id" value="<?php echo $course['parent_id']?>">
						</td>
					</tr>
					<tr>
						<td class="alt right wd_120"><span class="red">*</span> 名称：</td>
						<td>
							<input type="text" name="name" value="<?php echo $course['name'];?>" class="easyui-validatebox input wd_400" data-options="required: true" autocomplete="off" maxlength="100" />
						</td>
					</tr>
					<tr>
						<td class="alt right"><span class="red">*</span> 排序：</td>
						<td>
							<input type="text" name="sort" value="<?php echo $course['sort'];?>" class="easyui-numberbox input wd_50" data-options="required: true, min: 1, precision: 0" autocomplete="off" maxlength="6" />
						</td>
					</tr>
				</table>
			</form>
		</div>
		<div region="south" style="height: 32px;" data-options="collapsible: false, border: false, split: false">
			<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-save'" onclick="course_save()">保存</a>
		</div>
	</div>
    <!--<script type="text/javascript" src="/static/ueditor1_4_3/ueditor.config.js"></script>
    <script type="text/javascript" src="/static/ueditor1_4_3/editor_api.js"></script> 编辑器替换为 KindEditor编辑器 2015 03 10-->

	<!--Begin 知识点选择对话框-->
	<div id="knowledge-add-dlg" class="easyui-dialog" title="课程体系" data-options="iconCls:'icon-table',modal:true,closed:true" style="width:600px;height:400px;padding:5px;"></div>
	<!--End 知识点选择对话框-->
	<script language='javascript' type='text/javascript'>
		$(function() {
			////UE.getEditor('analysis', {
			//	initialFrameHeight: 100
			//});
		});
		function course_save() {
			$('#knowledge-edit-form').form('submit', {
				url: '/Vip/VipJiaoyan/course_edit_save',
				onSubmit: function () {
					return $(this).form('validate');
				},
				success: function (result) {
					var result = JSON.parse(result);
					if (result.status) {
						parent._initCourse('<?php echo $knowledgetypeid?>');
						parent.$('#basic-index-knowledge-dlg').dialog('close');
					} else {
						$.messager.alert('错误信息', '操作失败!', 'error');
					}
				}
			});
		}
	</script>
</body>
</html>