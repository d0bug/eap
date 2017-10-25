<!doctype html>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<link href="/static/css/question.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/static/js/question.js"></script>
</head>
<body style="padding: 5px">
	<div class="easyui-layout" data-options="fit: true">
		<div region="north" style="height: 180px;" data-options="title:'请选择属性', iconCls: 'icon-search'">
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="tableInfo">
				<tr>
					<td class="wd_120 alt right">年部：</td>
					<td>
						<ul id="question-classic-list-grade" class="fliter_box_select"></ul>
					</td>
				</tr>
				<tr>
					<td class="alt right">学科：</td>
					<td>
						<ul id="question-classic-list-subject" class="fliter_box_select"></ul>
					</td>
				</tr>
				<tr>
					<td class="alt right">教材版本：</td>
					<td>
						<ul id="question-classic-list-knowledge-type" class="fliter_box_select"></ul>
					</td>
				</tr>
				<tr>
					<td class="alt right" style="border: 0px">课程类型：</td>
					<td style="border: 0px">
						<ul id="question-classic-list-course-type" class="fliter_box_select"></ul>
					</td>
				</tr>
			</table>
		</div>
		<div region="center" data-options="collapsible: false, border: false">
			<div class="easyui-layout" data-options="fit: true">
				<div region="north" style="height: 23px;" data-options="collapsible: false, border: false">
					<span style="float:left; padding:5px 5px 5px 5px;">剩余<em class="red" id="left_non_edit_question_count">{$left_non_edit_question_count}</em>道/共<em class="red" id="total_question_count">{$total_question_count}</em>道</span>
					<span style="float:left; padding:5px 5px 5px 25px;">我完成了<em class="red" id="my_op_question_count">{$my_op_question_count}</em>道</span>
				</div>
				<div region="center" data-options="collapsible: false, border: false">
					<div id="question-classic-list-panel" class="easyui-panel" style="width:auto; margin-top: 6px;" data-options="cache: true, border: false"></div>
				</div>
			</div>
		</div>
	</div>
	<object id="tiku_plugin" type="application/x-tkbsplugin" width="1" height="1" style="width:0px;height:0; overflow:hidden;">
	    <param name="onload" value="pluginLoaded" />
	</object>
	<script type="text/javascript" src="/static/ueditor1_4_3/ueditor.config.js"></script>
	<script type="text/javascript" src="/static/ueditor1_4_3/editor_api.js"></script>
	<script type="text/javascript" src="/static/js/ue.ext.openinword.js"></script>
	<script type="text/javascript" src="/static/js/question.js"></script>
	<script language='javascript' type='text/javascript'>
		var courseTypeId = '';
		function question_classic_list_search(pageNumber, pageSize) {
			var params = get_question_classic_list_search_params();
			refresh_question_list(params);
		}
		function refresh_question_list(params) {
			$('#question-classic-list-panel').panel('refresh', '/Question/Knowledge/render_edit_question_classic_list' + params);
		}
		function get_question_classic_list_search_params() {
			var params = '?coursetypeid=' + courseTypeId;
			return params;
		}
		function reset_question_list() {
			var params = get_question_classic_list_search_params();
			//获取统计数
			$.post('/Question/Knowledge/getQuestionStatisticsByCourseTypeId2', {coursetypeid: courseTypeId}, function(data){
				data = JSON.parse(data);
				$('#left_non_edit_question_count').html(data.left_non_edit_question_count);
				$('#total_question_count').html(data.total_question_count);
				$('#my_op_question_count').html(data.my_op_question_count);
			});
			
			$.post('/Question/Knowledge/getQuestionCurrentEdit2', {coursetypeid: courseTypeId}, function(result){
				if (result == 1) {
					$('#question-classic-list-panel').panel('refresh', '/Question/Knowledge/render_edit_question_classic_list' + params);
				}
				else {
					$('#question-classic-list-panel').panel('refresh', '/Question/Knowledge/content2');
				}
			});
		}
		function setValues(id) {
			courseTypeId = id;
		}
		var _initGrade = function() {
			$.post('/Question/Basic/getGrades', null, function(data){
				data = JSON.parse(data);
				$.each(data, function(i, row) {
					$('#question-classic-list-grade').append('<li onclick="_initSubject(' + row.id + ');_reRenderStyle(this);"><a href="#">' + row.title + '</a></li>');
				});
				$('#question-classic-list-grade li:first').click();
			});
		}
		var _initSubject = function(gradeId) {
			$.post('/Question/Basic/getSubjectsByGradeId', {gradeid: gradeId}, function(data){
				$('#question-classic-list-subject, #question-classic-list-knowledge-type, #question-classic-list-course-type').html('');
				data = JSON.parse(data);
				$.each(data, function(i, row) {
					$('#question-classic-list-subject').append('<li onclick="_initKnowledgeType(' + row.id + ');_reRenderStyle(this);"><a href="#">' + row.title + '</a></li>');
				});
				$('#question-classic-list-subject li:first').click();
			});
		}
		var _initKnowledgeType = function(subjectId) {
			$.post('/Question/Basic/getKnowledgeTypes', {subjectid: subjectId,is_gaosi:1}, function(data){
				$('#question-classic-list-knowledge-type, #question-classic-list-course-type').html('');
				data = JSON.parse(data);
				$.each(data, function(i, row) {
					$('#question-classic-list-knowledge-type').append('<li onclick="_initCourseType('+subjectId+',' + row.id + ');_reRenderStyle(this);"><a href="#">' + row.title + '</a></li>');
				});
				$('#question-classic-list-knowledge-type li:first').click();
			});
		}
		var _initCourseType = function(subjectId,knowledgeTypeId) {
			$.post('/Question/Basic/getCourseTypesBySubjectIdAndKnowledgeTypeId', {subjectid: subjectId, knowledgeTypeId:knowledgeTypeId}, function(data){
				$('#question-classic-list-course-type').html('');
				data = JSON.parse(data);
				$.each(data, function(i, row) {
					$('#question-classic-list-course-type').append('<li onclick="setValues(' + row.id + ');reset_question_list();_reRenderStyle(this);"><a href="#">' + row.title + '</a></li>');
				});
				$('#question-classic-list-course-type li:first').click();
			});
		}
		var _reRenderStyle = function(o) {
			$(o).addClass('active').siblings().removeClass('active');
		}
		$(function() {
			_initGrade();
		});
	</script>
</body>
</html>