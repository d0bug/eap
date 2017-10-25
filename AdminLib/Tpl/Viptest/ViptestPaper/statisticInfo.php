<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/viptest.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body >
<div region="center" >
<div id="main">
		<table class="tableInfo" width="80%">
			<tr>
				<td align="right" width="30%">该题目做过人数：</td>
				<td><?php echo $total?>人</td>
			</tr>
			<tr>
				<td align="right">做对人数：</td>
				<td><?php echo $correct_num?>人&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $correct_percentage?>%</td>
			</tr>
			<tr>
				<td align="right">做错人数：</td>
				<td><?php echo $error_num?>人&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $error_percentage?>%</td>
			</tr>
			<tr>
				<td align="right" valign="top">错误选项：</td>
				<td>
					<?php foreach($newAnswerArr as $key=>$newAnswer):?>
						<p><?php echo $newAnswer['val']?>：&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $newAnswer['count']?>人
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<?php echo sprintf('%.2f',($newAnswer['count']/$total)*100)?>%
						</p>
					<?php endforeach?>
				</td>
			</tr>
		</table>
</div>
</div>
</body>
</html>