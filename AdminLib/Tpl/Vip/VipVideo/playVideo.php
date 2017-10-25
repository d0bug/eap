<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/swfobject.js"></script> 
</head>
<body>
<div region="center">
	<p id="player1"></p>
	<script type="text/javascript">
			var s1 = new SWFObject("/static/js/flvplayer.swf","single","600","500","7");
			s1.addParam("allowfullscreen","true");
			s1.addVariable("file","<?php echo $videoInfo['video_url_show']?>");
			s1.addVariable("image","<?php echo $videoInfo['video_img_show']?>");
			s1.addVariable("width","600");
			s1.addVariable("height","500");
			s1.write("player1");
	</script>
</div>
</body>
</html>