<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.uploadify-3.1.min.js"></script>
<script type="text/javascript" src="/static/js/use_uploadify.js"></script>
<script type="text/javascript" src="/static/js/essay.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<link href="/static/css/uploadify.css" type="text/css" rel="stylesheet" />
<link href="/static/css/essay.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<form name="changeAvatar" method="POST" action="<?php echo U('Essay/Essay/changeAvatar',array('student_code'=>$student_code))?>">
		<input type="hidden" name="act" id="act" value="<?php if($avatar['is_default']==0):?>update<?php else:?>add<?php endif;?>"/>
		<input type="hidden" id="uploadimg_url" name="uploadimg_url" value="<?php echo U('Essay/Essay/do_upload_essayImg')?>">
		<div id="pre_avatar">
			<?php if($avatar):?>
				<div class="pic"><img src="<?php echo $avatar['avatar'];?>" width="160" height="200"></div>
			<?php endif?>
		</div>
		<input type="file" name="file_student_avatar" id="file_student_avatar" />
		<input type="hidden" name="avatar" id="avatar" value="<?php if($publicityInfo['avatar']):?><?php echo $publicityInfo['avatar'];?><?php endif?>">
		<input type="submit" value="保存头像" class="btn" > 
		<span class="error" id="avatar_msg"></span>
	</form>
</div>
</body>
</html>