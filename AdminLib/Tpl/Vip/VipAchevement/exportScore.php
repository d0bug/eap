<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/static/js/DatePicker/WdatePicker.js"></script>
<style type="text/css">
.opacity{margin-top:-30px;height:20px; filter:alpha(Opacity=10);-moz-opacity:0.5;opacity: 0.5;z-index:100; background-color:#000000;color:#ffffff;text-align:right;}
</style>
</head>
<body>
<div region="center">
<div id="main">
	<?php if($userInfo['real_name'] == '王啸宇' || $userInfo['real_name'] == '史云蕾' || in_array($userInfo['user_name'],C('SUPER_USERS'))):?>
	<a href="<?php echo U('Vip/VipAchevement/doExportScore')?>" class="blue">导出中考成绩表</a>
	<?php else:?>
	您没有导出权限！
	<?php endif;?>
</div>
</div>
</body>
</html>