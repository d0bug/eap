<!DOCTYPE html>
<html>
<head>
	<title><?php echo $videoInfo['title']?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/video.js"></script>
</head>
<body>
<video width="100%" height="100%" controls >
  <source src="<?php echo $videoInfo['video_url_show']?>" type="video/mp4">  
  </object> 
</video>
</body>
</body>
</html>