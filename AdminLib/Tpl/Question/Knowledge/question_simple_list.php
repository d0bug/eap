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
						<ul id="question-smiple-list-grade" class="fliter_box_select"></ul>
					</td>
				</tr>
				<tr>
					<td class="alt right">学科：</td>
					<td>
						<ul id="question-smiple-list-subject" class="fliter_box_select"></ul>
					</td>
				</tr>
				<tr>
					<td class="alt right">教材版本：</td>
					<td>
						<ul id="question-smiple-list-knowledge-type" class="fliter_box_select"></ul>
					</td>
				</tr>
				<tr>
					<td class="alt right" style="border: 0px">课程类型：</td>
					<td style="border: 0px">
						<ul id="question-smiple-list-course-type" class="fliter_box_select"></ul>
					</td>
				</tr>
			</table>
		</div>
		<div region="center" data-options="collapsible: false, border: false">
			<div class="easyui-layout" data-options="fit: true">
				<div region="north" style="height: 23px;" data-options="collapsible: false, border: false">
					<a href="#" style="float:left; padding:5px;" onclick="my_edit_questions()">我的操作</a>
					<span style="float:left; padding:5px 5px 5px 25px;">锁定<em class="red" id="lock_question_count">{$left_non_edit_question_count}</em>道，剩余<em class="red" id="left_non_edit_question_count">{$left_non_edit_question_count}</em>道/共<em class="red" id="total_question_count">{$total_question_count}</em>道</span>
					<span style="float:left; padding:5px 5px 5px 25px;">我完成了<em class="red" id="my_op_question_count">{$my_op_question_count}</em>道</span>
				</div>
				<div region="center" data-options="collapsible: false, border: false">
					<div id="question-smiple-list-panel" class="easyui-panel" style="width:auto; margin-top: 6px;" data-options="cache: true, border: false"></div>
				</div>
			</div>
		</div>
	</div>
	<!--Begin 我的操作-->
	<div id="question-simple-my-questions" class="easyui-dialog" style="width: 850px; height: 500px; padding: 5px;" title="我的操作" data-options="iconCls:'icon-table',modal:true, closed:true"></div>
	<!--End 我的操作-->
	<script language='javascript' type='text/javascript'>
		function my_edit_questions() {
			$('#question-simple-my-questions').dialog({
				href: '/Question/Knowledge/my_edit_questions'
			}).dialog('open');
		}
		var courseTypeId = '';
		function question_simple_list_search(pageNumber, pageSize) {
			var params = get_question_simple_list_search_params();
			refresh_question_list(params);
		}
		function refresh_question_list(params) {
			$('#question-smiple-list-panel').panel('refresh', '/Question/Knowledge/render_question_simple_list' + params);
		}
		function get_question_simple_list_search_params() {
			var params = '?coursetypeid=' + courseTypeId;
			return params;
		}
		function reset_question_list() {
			var params = get_question_simple_list_search_params();
			//获取统计数
			$.post('/Question/Knowledge/getQuestionStatisticsByCourseTypeId', {coursetypeid: courseTypeId}, function(data){
				data = JSON.parse(data);
				$('#lock_question_count').html(data.lock_question_count);
				$('#left_non_edit_question_count').html(data.left_non_edit_question_count);
				$('#total_question_count').html(data.total_question_count);
				$('#my_op_question_count').html(data.my_op_question_count);
			});
			
			$.post('/Question/Knowledge/getQuestionCurrentEdit', {coursetypeid: courseTypeId}, function(result){
				if (result == 1) {
					$('#question-smiple-list-panel').panel('refresh', '/Question/Knowledge/render_question_simple_list' + params);
				}
				else {
					$('#question-smiple-list-panel').panel('refresh', '/Question/Knowledge/content');
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
					$('#question-smiple-list-grade').append('<li onclick="_initSubject(' + row.id + ');_reRenderStyle(this);"><a href="#">' + row.title + '</a></li>');
				});
				$('#question-smiple-list-grade li:first').click();
			});
		}
		var _initSubject = function(gradeId) {
			$.post('/Question/Basic/getSubjectsByGradeId', {gradeid: gradeId}, function(data){
				$('#question-smiple-list-subject, #question-smiple-list-knowledge-type, #question-smiple-list-course-type').html('');
				data = JSON.parse(data);
				$.each(data, function(i, row) {
					$('#question-smiple-list-subject').append('<li onclick="_initKnowledgeType(' + row.id + ');_reRenderStyle(this);"><a href="#">' + row.title + '</a></li>');
				});
				$('#question-smiple-list-subject li:first').click();
			});
		}
		var _initKnowledgeType = function(subjectId) {
			$.post('/Question/Basic/getKnowledgeTypes', {subjectid: subjectId,is_gaosi:1}, function(data){
				$('#question-smiple-list-knowledge-type, #question-smiple-list-course-type').html('');
				data = JSON.parse(data);
				$.each(data, function(i, row) {
					$('#question-smiple-list-knowledge-type').append('<li onclick="_initCourseType('+subjectId+',' + row.id + ');_reRenderStyle(this);"><a href="#">' + row.title + '</a></li>');
				});
				$('#question-smiple-list-knowledge-type li:first').click();
			});
		}
		var _initCourseType = function(subjectId,knowledgeTypeId) {
			$.post('/Question/Basic/getCourseTypesBySubjectIdAndKnowledgeTypeId', {subjectid: subjectId, knowledgeTypeId:knowledgeTypeId}, function(data){
				$('#question-smiple-list-course-type').html('');
				data = JSON.parse(data);
				$.each(data, function(i, row) {
					$('#question-smiple-list-course-type').append('<li onclick="setValues(' + row.id + ');reset_question_list();_reRenderStyle(this);"><a href="#">' + row.title + '</a></li>');
				});
				$('#question-smiple-list-course-type li:first').click();
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