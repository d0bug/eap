<!doctype html>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<link href="/static/css/question.css" type="text/css" rel="stylesheet" />
<link href="/static/ueditor1_4_3/themes/default/_css/ueditor.css" type="text/css" rel="stylesheet">
</head>
<body style="padding: 5px">
	<div class="easyui-layout" data-options="fit: true">
		<div region="north" style="height: 27px;" data-options="collapsible: false, border: false">
			<input class="easyui-searchbox" data-options="prompt:'请输入试题ID...', searcher: do_search_question" style="width:250px;" />
		</div>
		<div region="center" data-options="collapsible: false, border: false">
			<div id="question-single-view-panel" class="easyui-panel" style="width:auto; " data-options="cache: true, border: false"></div>
		</div>
	</div>
	<script language='javascript' type='text/javascript'>
		function do_search_question(val) {
			$('#question-single-view-panel').panel('refresh', '/Question/Knowledge/render_question_single?id=' + val);
		
		}
	</script>
</body>
</html>