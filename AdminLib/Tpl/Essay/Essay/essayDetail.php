<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<script type="text/javascript" src="/static/js/essay.js"></script>
<link href="/static/css/essay.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
	<div id="main">
		<h4>班级信息：<br></h4>
		<div class="Types">
		<?php if($essayInfo):?>
			<li>&nbsp;&nbsp;<?php echo $essayInfo['class_name'].'&nbsp;&nbsp;&nbsp;'.$essayInfo['class_code'].'&nbsp;&nbsp;&nbsp;'.$essayInfo['campus_name'].'&nbsp;&nbsp;&nbsp;&nbsp;'.$essayInfo['teacher_name'].'<br>&nbsp;&nbsp;开课日期：'.$essayInfo['dtbegindate'].'&nbsp;&nbsp;&nbsp;结课日期：'.$essayInfo['dtenddate'].'&nbsp;&nbsp;&nbsp;上课时间：'.$essayInfo['sprinttime'];?>
				<input type="hidden"  id="classInfo" name="classInfo" value="<?php echo $essayInfo['class_name'].'|'.$essayInfo['class_code'].'|'.$essayInfo['campus_name'].'|'.$essayInfo['teacher_name'].'|'.$essayInfo['dtbegindate'].'|'.$essayInfo['dtenddate'].'|'.$essayInfo['sprinttime'];?>" checked="checked">
				<br>
				<p class="Nums">
					<?php foreach($essayInfo['n_lesson_no'] as $key=>$lesson_no):?>
						<span onclick="show_students('<?php echo $essayInfo['class_code']?>','<?php echo $lesson_no['nlessonno'];?>','<?php echo U('Essay/Essay/showStudents')?>');$(this).addClass('bgcolor');">
						<?php echo $lesson_no['nlessonno'];?></span>
					<?php endforeach?>
				</p>
			</li>
		<?php else:?>
			非法操作！
		<?php endif;?>
		</div>
		<div class="forms">
			<h4>学生名册：<br></h4>
			<div class="Students">
				请先选择讲次
			</div>
		</div>
	</div>
</div>
</body>
</html>