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
		<div class="clearit"></div>	
		<div id="main_container" class="center model1 model" style="display:<?php echo $modelhover1[1];?> " style="display:none">
			<form method="post" name="form1" id="form1" action="<?php echo U('Essay/Essay/addEssay')?>" enctype="multipart/form-data" onsubmit="return checkAddEssayForm()">
				<input type="hidden" id="id" name="id" value="<?php echo $essayId;?>">
				<input type="hidden" id="img_num" name="img_num" value="<?php echo $essayInfo['img_num'];?>">
				<h4>班级和讲次：<br></h4>
				<div class="Types">
				<?php if($essayInfo):?>
					<li><input type="radio"  id="classInfo_<?php echo $essayInfo['class_code'];?>" name="classInfo" value="<?php echo $essayInfo['class_name'].'|'.$essayInfo['class_code'].'|'.$essayInfo['campus_name'].'|'.$essayInfo['teacher_name'].'|'.$essayInfo['dtbegindate'].'|'.$essayInfo['dtenddate'].'|'.$essayInfo['sprinttime'];?>" checked="checked">&nbsp;<?php echo $essayInfo['class_name'].'&nbsp;&nbsp;&nbsp;'.$essayInfo['class_code'].'&nbsp;&nbsp;&nbsp;'.$essayInfo['campus_name'].'&nbsp;&nbsp;&nbsp;&nbsp;'.$essayInfo['teacher_name'].'<br>&nbsp;&nbsp;开课日期：'.$essayInfo['dtbegindate'].'&nbsp;&nbsp;&nbsp;结课日期：'.$essayInfo['dtenddate'].'&nbsp;&nbsp;&nbsp;上课时间：'.$essayInfo['sprinttime'];?><br>
						<p class="Nums">
							<?php foreach($speakerList as $key=>$speaker):?>
							<span <?php if($essayInfo['speaker_number']==$speaker['nlessonno']):?>class="bgcolor"<?php endif;?>><?php echo $speaker['nlessonno'];?></span>
							<?php endforeach?>
						</p>
						<input type="hidden" id="speakerNumber_<?php echo $essayInfo['class_code'];?>" name="speakerNumber_<?php echo $essayInfo['class_code'];?>" value="<?php echo $essayInfo['speaker_number'];?>">
					</li>
				<?php else:?>
					请先去选择班级
				<?php endif;?>
				</div>
				<div class="forms">
					<h4>学生名册：<br></h4>
					<div class="Students">
					<?php if($studentList):?>
						<?php foreach($studentList as $key=>$student):?>
						<li onclick="selectType('student','<?php echo $student['sstudentcode'].'|'.$student['sname'];?>');$(this).addClass('bgcolor');" <?php if($essayInfo['student_code'] && $essayInfo['student_code'] == $student['sstudentcode']):?>class="bgcolor"<?php endif;?>><?php if($student['is_upload']):?><font color="#ff8400"><?php echo $student['sname'];?></font><?php else:?><?php echo $student['sname'];?><?php endif;?></li>
						<?php endforeach?>
					<?php else:?>
						暂无学生
					<?php endif;?>	
					</div><br>
					<div style="margin-left:90px;">
						添加学生姓名：<input type="text" id="new_student" name="new_student">
						<input type="button" value="添加" onclick="add_student('<?php echo $essayInfo['class_code'];?>','<?php echo $essayInfo['speaker_number'];?>','<?php echo U('Essay/Essay/addStudent');?>')">
						<label class="error" id="add_student_msg"></label></div>
					<input type="hidden" id="studentInfo" name="studentInfo" value="<?php if($essayId || !empty($essayInfo['is_exist'])):?><?php echo $essayInfo['student_code'];?>|<?php echo $essayInfo['student_name'];?><?php endif;?>">
				</div>
				<div class="forms">
				<h4>上传作文照片：</h4>
						<input type="hidden" id="uploadimg_url" name="uploadimg_url" value="<?php echo U('Essay/Essay/do_upload_essayImg')?>">
						<div class="file"><input type="file" name="upload_essayImg" id="upload_essayImg"/></div>
						<div class="file_button">
							<input type="button" onclick="javascript:$('#upload_essayImg').uploadify('upload','*');" class="btn" value="上传">&nbsp;&nbsp;
							<input type="button" onclick="javascript:$('#upload_essayImg').uploadify('cancel','*');" class="btn" value="取消">
						</div>
						<div class="remind">(提醒：一次可选择多张照片批量上传，每张图片均不可超过15M。)</div>
						<input type="hidden" id="essayImgs" name="essayImgs" value="<?php echo (!empty($essayId))?$essayInfo['essay_imgs']:"";?>">
						<div id="preview" class="preview"><?php if(!empty($previewHtml)):?><?php echo $previewHtml;?><?php endif;?></div>
				</div>
				<div><input type="submit" class="btn" value=" 保存 "><label class="error" id="return_msg"></label></div>
			</form>
		</div>
	</div>
</div>
</body>
</html>