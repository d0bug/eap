<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<script type="text/javascript" src="/static/js/DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/js/jquery.uploadify-3.1.min.js"></script>
<script type="text/javascript" src="/static/js/use_uploadify.js"></script>
<script type="text/javascript" src="/static/js/essay.js"></script>
<link href="/static/css/uploadify.css" type="text/css" rel="stylesheet" />
<link href="/static/css/essay.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
	<div id="main">
		<h2>上传作文照片</h2>
		<div class="Snav center">
			<li class="hover" ref="model1" id="step1">1. 上传课堂作文</li>
			<li ref="model2" id="step2" <?php if($essayId):?>onclick="javascript:window.location.href='<?php echo U('Essay/Essay/editEssayAttribute',array('id'=>$essayId))?>'"<?php endif;?>>2. 编辑作文属性</li>
		</div>
		<div class="clearit"></div>	
		<div id="main_container" class="center model1 model" style="display:<?php echo $modelhover1[1];?> " style="display:none">
			<div id="search">
				<form method="GET" id="search" action="<?php echo U('Essay/Essay/selectClass')?>">
					<h4>班级搜索：<input type="text"  placeholder="请选择上课日期..." class="Wdate" id="dtDate" name="dtDate" value="<?php echo $dtDate;?>" onClick="WdatePicker()">&nbsp;<input type="submit" value="搜索" onclick="" class="btn2"></h4>
				</form>
			</div>
		<?php if(empty($classList)):?>
			<span class="error">*默认只显示180天内的班级信息,若要查询默认期限外的数据，请进行班级搜索。</span><br><br>
			抱歉，您暂时没有思泉语文相关课程！&nbsp;&nbsp;<a href="<?php echo U('Essay/Essay/selectClass')?>"><font color="red">返回</font></a>
		<?php else:?>
			<form method="post" name="form1" id="form1"  enctype="multipart/form-data" onsubmit="return checkSelectClassForm()">
				<h4>选择班级和讲次：<span class="error">*默认只显示180天内的班级信息,若要查询默认期限外的数据，请进行班级搜索。</span><br></h4>
				<div class="Types">
					<?php foreach($classList as $key=>$class):?>
						<li> <input type="radio"  id="classInfo_<?php echo $class['s_class_code'];?>" name="classInfo" value="<?php echo $class['s_class_name'].'|'.$class['s_class_code'].'|'.$class['s_area_name'].'|'.$class['s_teacher_name'].'|'.$class['dtbegindate'].'|'.$class['dtenddate'].'|'.$class['sprinttime'];?>">&nbsp;<?php echo $class['s_class_name'].'&nbsp;&nbsp;&nbsp;'.$class['s_class_code'].'&nbsp;&nbsp;&nbsp;'.$class['s_area_name'].'&nbsp;&nbsp;&nbsp;'.$class['s_teacher_name'].'<br>&nbsp;&nbsp;&nbsp;开课日期：'.$class['dtbegindate'].'&nbsp;&nbsp;&nbsp;结课日期：'.$class['dtenddate'].'&nbsp;&nbsp;&nbsp;上课时间：'.$class['sprinttime'];?><br>
							<p class="Nums">
							<?php foreach($class['n_lesson_no'] as $key=>$lesson_no):?>
								<span onclick="selectLessonNo('<?php echo $lesson_no['nlessonno'];?>','<?php echo $class['s_class_code'];?>');$(this).addClass('bgcolor');">
								<?php echo $lesson_no['nlessonno'];?></span>
							<?php endforeach?>
							</p>
							<p><input type="hidden" id="speakerNumber_<?php echo $class['s_class_code'];?>" name="speakerNumber_<?php echo $class['s_class_code'];?>" value=""></p>
						</li>
					<?php endforeach?>
				<li style="text-align:center"><input type="submit" class="btn" value="进入班级"><label class="error" id="return_msg"></label></li>
				</div>
			</form>
			<?php endif;?>
		</div>
	</div>
</div>
</body>
</html>