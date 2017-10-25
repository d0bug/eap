<!doctype html>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<link href="/static/css/question.css" type="text/css" rel="stylesheet" />
</head>
<body style="padding: 5px"><br>
	<p>
		<span>套卷数：<a href="/Question/Knowledge/paper_detail_list" target="_blank" style="color: red; text-decoration: underline;"><strong>{$statistics['paper_count']}</strong></a>份 </span>
		<span style="padding-left: 40px;">总题数：<font class="red">{$statistics['total']}</font>道</span>
		<span style="padding-left: 40px;">今日上传：<font class="red">{$statistics['today']}</font>道 </span>
		<span style="padding-left: 40px;">近一周上传：<font class="red">{$statistics['week']}</font>道</span>
		<span style="padding-left: 40px;">近一月上传：<font class="red">{$statistics['month']}</font>道</span>
	</p>
</body>
</html>