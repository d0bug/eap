<!doctype html>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<link href="/static/css/question.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/static/js/question.js"></script>
<script type="text/x-mathjax-config">
		MathJax.Hub.Config({
		  config: ["MMLorHTML.js"],
		  jax: ["input/TeX","input/MathML","output/HTML-CSS","output/HTML-CSS"],
		  extensions: ["tex2jax.js","mml2jax.js","MathMenu.js","MathZoom.js"],
		  TeX: {
			
		    extensions: ["AMSmath.js","AMSsymbols.js","noErrors.js","noUndefined.js"]
		  },
		tex2jax: {
		      inlineMath: [ ['$','$'], ['$$','$$'], ["\[","\]"]],
		      displayMath: [ ["\(","\)"] ],
		      processEscapes: true,
		processEnvironments: true,
		displaystyle: true,
		    },
		     CommonHTML: { linebreaks: { automatic: true } },
		     "HTML-CSS": { linebreaks: { automatic: true } },
		         SVG: { linebreaks: { automatic: true } }
		});
		 MathJax.Hub.Configured();
</script>
<script type="text/javascript" src="/static/js/mathjax/MathJax.js?config=TeX-AMS-MML_HTMLorMML&delayStartupUntil=configured"></script>
</head>
<body style="padding: 5px">
	<div class="easyui-layout" data-options="fit: true">
		<div region="north" style="height: 180px;" data-options="title:'请选择属性', iconCls: 'icon-search'">
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="tableInfo">
				<tr>
					<td class="wd_120 alt right">年部：</td>
					<td>
						<ul id="question-list-grade" class="fliter_box_select"></ul>
					</td>
				</tr>
				<tr>
					<td class="alt right">学科：</td>
					<td>
						<ul id="question-list-subject" class="fliter_box_select"></ul>
					</td>
				</tr>
				<tr>
					<td class="alt right">教材版本：</td>
					<td>
						<ul id="question-list-knowledge-type" class="fliter_box_select"></ul>
					</td>
				</tr>
				<tr>
					<td class="alt right" style="border: 0px">课程类型：</td>
					<td style="border: 0px">
						<ul id="question-list-course-type" class="fliter_box_select"></ul>
					</td>
				</tr>
			</table>
		</div>
		<div region="center" data-options="collapsible: false, border: false">
			<div class="easyui-layout" data-options="fit: true">
				<div region="north" style="height: 38px;" data-options="collapsible: false, border: false">
					<div style="background-color: #ddd; height: 20px; padding: 3px 10px 3px 10px; margin-top: 6px;">
						<span style="float: left; text-align: left">
							<a href="#" onclick="query_all()" title="查看所有试题">所有</a>
							<a href="#" id="query_class" style="margin-left: 15px;" onclick="query_class(this)" title="查看“大班”导入的试题">大班</a>
							<a href="#" id="query_vip" style="margin-left: 5px;" onclick="query_vip(this)" title="查看“VIP”导入的试题">VIP</a>
							<a href="#" id="query_classic" style="margin-left: 15px;" onclick="query_classic(this)" title="查看“经典题”">经典题</a>
							<a href="#" id="query_content_error" style="margin-left: 15px;" onclick="query_content_error(this)" title="查看“题干有问题”的试题">题干有问题</a>
						</span>
						<span style="float: right; text-align: right">
						<!-- onclick="show_paper_list()"-->
							<!--<span>套卷数：<a href="/Question/Knowledge/paper_detail_list" target="_blank" style="color: red; text-decoration: underline;"><strong>{$statistics['paper_count']}</strong></a>份 </span>
							<span style="padding-left: 40px;">总题数：<font class="red">{$statistics['total']}</font>道</span>
							<span style="padding-left: 40px;">今日上传：<font class="red">{$statistics['today']}</font>道 </span>
							<span style="padding-left: 40px;">近一周上传：<font class="red">{$statistics['week']}</font>道</span>
							<span style="padding-left: 40px;">近一月上传：<font class="red">{$statistics['month']}</font>道</span>-->
						</span>
					</div>
				</div>
				<div region="center" data-options="collapsible: false, border: false">
					<div id="question-list-panel" class="easyui-panel" style="width:auto;" data-options="cache: true, border: false"></div>
				</div>
				<div region="south" style="height: 32px;" data-options="collapsible: false, border: true">
					<div id="question-list-pagination" class="easyui-pagination"></div>
				</div>
			</div>
		</div>
	</div>
	<!--Begin 套卷列表-->
	<div id="question-list-paper-dlg" class="easyui-dialog" data-options="modal:true,closed:true" style="width:900px;height:400px;padding:5px;"></div>
	<!--End 套卷列表-->
	 <script type="text/javascript">MathJax.Hub.Configured()</script>
	<script language='javascript' type='text/javascript'>
		var courseTypeId = '';
		var department = '';
		var isClassic = 0;
		var isContentError = 0;

		var options = {
			total: '{$total}',
			onSelectPage: function (pageNumber, pageSize) {
				$(this).pagination('loading');
				question_list_search(pageNumber, pageSize);
				$(this).pagination('loaded');
			}
		};
		function question_list_search(pageNumber, pageSize) {
			options.pageNumber = pageNumber;
			var params = get_question_list_search_params(pageNumber, pageSize);
			refresh_question_list(params);
			render_question_list_pagination(params);
		}
		function refresh_question_list(params) {
			$('#question-list-panel').panel('refresh', '/Question/Knowledge/render_question_list' + params);
		}
		function render_question_list_pagination(params) {
			$.ajax({
				url : '/Question/Knowledge/get_question_list_count' + params,
				type : 'GET',
				dataType : 'json',
				success: function(count) {
					options.total = count;
					$('#question-list-pagination').pagination('refresh', options);
					MathJax.Hub.Queue(["Typeset", MathJax.Hub, 'word-subject']);
				}
			});
		}
		function get_question_list_search_params(pageNumber, pageSize) {
			var params = '?page=' + (typeof pageNumber == 'undefined' ? '' : pageNumber) +
						 '&rows=' + (typeof pageSize == 'undefined' ? '' : pageSize) +
						 '&coursetypeid=' + courseTypeId + 
						 '&department=' + department + 
						 '&isclassic=' + isClassic + 
						 '&iscontenterror=' + isContentError;

			 return params;
		}
		function setValues(id) {
			courseTypeId = id;
		}
		function query_all() {
			department = '';
			isClassic = 0;
			isContentError = 0;
			$('#query_class, #query_vip, #query_classic, #query_content_error').removeClass('search');
			question_list_search();
		}
		function query_class(o) {
			department = 'CLASS';
			$('#query_vip').removeClass('search');
			$(o).addClass('search');
			question_list_search();
		}
		function query_vip(o) {
			department = 'VIP';
			$('#query_class').removeClass('search');
			$(o).addClass('search');
			question_list_search();
		}
		function query_classic(o) {
			isClassic = isClassic == 0 ? 1 : 0;
			$(o).toggleClass('search');
			question_list_search();
		}
		function query_content_error(o) {
			isContentError = isContentError == 0 ? 1 : 0;
			$(o).toggleClass('search');
			question_list_search();
		}
		var _initGrade = function() {
			$.post('/Question/Basic/getGrades', null, function(data){
				data = JSON.parse(data);
				$.each(data, function(i, row) {
					$('#question-list-grade').append('<li onclick="_initSubject(' + row.id + ');_reRenderStyle(this);"><a href="#">' + row.title + '</a></li>');
				});
				$('#question-list-grade li:first').click();
			});
		}
		var _initSubject = function(gradeId) {
			$.post('/Question/Basic/getSubjectsByGradeId', {gradeid: gradeId}, function(data){
				$('#question-list-subject, #question-list-knowledge-type, #question-list-course-type').html('');
				data = JSON.parse(data);
				$.each(data, function(i, row) {
					$('#question-list-subject').append('<li onclick="_initKnowledgeType(' + row.id + ');_reRenderStyle(this);"><a href="#">' + row.title + '</a></li>');
				});
				$('#question-list-subject li:first').click();
			});
		}
		var _initKnowledgeType = function(subjectId) {
			$.post('/Question/Basic/getKnowledgeTypes', {subjectid: subjectId,is_gaosi:1}, function(data){
				$('#question-list-knowledge-type, #question-list-course-type').html('');
				data = JSON.parse(data);
				$.each(data, function(i, row) {
					$('#question-list-knowledge-type').append('<li onclick="_initCourseType('+subjectId+',' + row.id + ');_reRenderStyle(this);"><a href="#">' + row.title + '</a></li>');
				});
				$('#question-list-knowledge-type li:first').click();
			});
		}
		var _initCourseType = function(subjectId,knowledgeTypeId) {
			$.post('/Question/Basic/getCourseTypesBySubjectIdAndKnowledgeTypeId', {subjectid: subjectId, knowledgeTypeId:knowledgeTypeId}, function(data){
				$('#question-list-course-type').html('');
				data = JSON.parse(data);
				$.each(data, function(i, row) {
					$('#question-list-course-type').append('<li onclick="setValues(' + row.id + ');question_list_search();_reRenderStyle(this);"><a href="#">' + row.title + '</a></li>');
				});
				$('#question-list-course-type li:first').click();
			});
		}
		var _reRenderStyle = function(o) {
			$(o).addClass('active').siblings().removeClass('active');
		}
		window.onload=function(){
			_initGrade();
			$('#question-list-pagination').pagination(options);
		}
		// $(function() {
		// 	_initGrade();
		// 	$('#question-list-pagination').pagination(options);
		// });
		function show_paper_list() {
			$('#question-list-paper-dlg').dialog({
				href: '/Question/Knowledge/paper_detail_list',
				iconCls: 'icon-table',
				title: '套卷'
			}).dialog('open');
		}
	</script>
</body>
</html>