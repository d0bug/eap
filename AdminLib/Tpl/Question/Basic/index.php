<!doctype html>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<script type="text/javascript" src="/static/easyui/treegrid-dnd.js"></script>
<link href="/static/css/question.css" type="text/css" rel="stylesheet" />
</head>
<body style="padding: 5px;">
	<div class="easyui-tabs" data-options="border: false, fit: true">
		<div title="知识点" data-options="href: '/Question/Basic/knowledge'" style="padding:5px 0 5px 0"></div>
		<div title="题型" data-options="href: '/Question/Basic/question_type'" style="padding:5px 0 5px 0"></div>
		<div title="考区" data-options="href: '/Question/Basic/test'" style="padding:5px 0 5px 0"></div>
		<div title="四级体系" data-options="href: '/Question/Basic/fourlevel_system'" style="padding:5px 0 5px 0"></div>
	</div>
</body>
</html>