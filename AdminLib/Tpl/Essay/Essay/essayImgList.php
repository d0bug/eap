<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/essay.js"></script>
<link href="/static/css/essay.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
	<div id="main">
		<div class="preview">
			<li style="border:0;">
				<img src="<?php echo $essayInfo['avatar'];?>" width="160" height="200"><br>
				<a href="#" onclick="testMessageBox_changeAvatar(event,'<?php echo U('Essay/Essay/changeAvatar',array('student_code'=>$essayInfo['student_code']));?>')" >上传/修改头像</a>
			</li>
			<li style="border:0;text-align:left;">
				<p>学生姓名：<?php echo $essayInfo['student_name'];?></p>
				<p>学生编号：<?php if($essayInfo['is_extra_student']==0):?><?php echo $essayInfo['student_code'];?><?php else:?>无<?php endif;?></p>
				<p>作文属性：<?php echo $essayInfo['type_one'];?><?php if(!empty($essayInfo['type_two'])):?>-><?php echo $essayInfo['type_two'];?><?php if($essayInfo['theme_name']):?>(<?php echo $essayInfo['theme_name'];?>)<?php endif;?><?php endif;?><?php if(!empty($essayInfo['type_three'])):?>-><?php echo $essayInfo['type_three'];?><?php endif;?><?php if(!empty($essayInfo['type_four'])):?>-><?php echo $essayInfo['type_four'];?><?php endif;?></p>
				<p class="right2">
					<?php if(!empty($essayImgsList)):?>
					<a href="<?php echo U('Essay/Essay/addEssay',array('id'=>$essayInfo['id']))?>" class="bold">续传</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="<?php echo U('Essay/Essay/editEssayAttribute',array('id'=>$essayInfo['id']))?>" class="bold">修改属性</a>
					<?php else:?>
					<a href="<?php echo U('Essay/Essay/addEssay',array('classInfoStr'=>$essayInfo['class_name'].'|'.$essayInfo['class_code'].'|'.$essayInfo['campus_name'].'|'.$essayInfo['teacher_name'].'|'.$essayInfo['dtbegindate'].'|'.$essayInfo['dtenddate'].'|'.$essayInfo['sprinttime'].'|'.$essayInfo['speaker_number'],'student_code'=>$essayInfo['student_code'],'student_name'=>$essayInfo['student_name']))?>" class="bold">上传</a>
					<?php endif;?>
				</p>
			</li>
		</div><br>
		<div class="Types">
			<li><input type="radio"  id="classInfo_<?php echo $essayInfo['class_code'];?>" name="classInfo" value="<?php echo $essayInfo['class_name'].'|'.$essayInfo['class_code'].'|'.$essayInfo['campus_name'].'|'.$essayInfo['teacher_name'].'|'.$essayInfo['dtbegindate'].'|'.$essayInfo['dtenddate'].'|'.$essayInfo['sprinttime'];?>" checked="checked">&nbsp;<?php echo $essayInfo['class_name'].'&nbsp;&nbsp;&nbsp;'.$essayInfo['class_code'].'&nbsp;&nbsp;&nbsp;'.$essayInfo['campus_name'].'&nbsp;&nbsp;&nbsp;&nbsp;'.$essayInfo['teacher_name'].'<br>&nbsp;&nbsp;开课日期：'.$essayInfo['dtbegindate'].'&nbsp;&nbsp;&nbsp;结课日期：'.$essayInfo['dtenddate'].'&nbsp;&nbsp;&nbsp;上课时间：'.$essayInfo['sprinttime'];?><br>
				<p class="Nums">
				<?php foreach($speakerList as $key=>$speaker):?>
					<a href="<?php echo U('Essay/Essay/essayImgList',array('class_info'=>$essayInfo['class_name'].'|'.$essayInfo['class_code'].'|'.$essayInfo['campus_name'].'|'.$essayInfo['teacher_name'].'|'.$essayInfo['dtbegindate'].'|'.$essayInfo['dtenddate'].'|'.$essayInfo['sprinttime'],'speaker_number'=>$speaker['nlessonno'],'student_code'=>$essayInfo['student_code'],'student_name'=>$essayInfo['student_name']))?>">
						<span onclick="selectLessonNo('<?php echo $speaker['nlessonno'];?>','<?php echo $essayInfo['class_code'];?>');$(this).addClass('bgcolor');" <?php if($essayInfo['speaker_number']==$speaker['nlessonno']):?>class="bgcolor"<?php endif;?>>
							<?php echo $speaker['nlessonno'];?>
						</span>
					</a>
				<?php endforeach?>
				</p>
				<input type="hidden" id="speakerNumber_<?php echo $essayInfo['class_code'];?>" name="speakerNumber_<?php echo $essayInfo['class_code'];?>" value="<?php echo $essayInfo['speaker_number'];?>">
			</li>
		</div><br><br>
		<div class="forms">
			<h2>
				<span class="font14">
					<!--<input type="checkbox" name="checkAll" id="checkAll" value="1"  class="checkAll">全选-->
					<?php if(!empty($essayImgsList)):?>
						<input type="button" value="添加到 “优秀作文选”" onclick="do_excellent('<?php echo U('Essay/Essay/do_excellent')?>','add','<?php echo $essayInfo['class_code'];?>','<?php echo $essayInfo['speaker_number'];?>','<?php echo $essayInfo['id'];?>')">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<?php if($order=='asc'):?>
							<a href="<?php echo U('Essay/Essay/essayImgList',array('key_name'=>'thumb_name','order'=>'desc','class_code'=>$essayInfo['class_code'],'speaker_number'=>$essayInfo['speaker_number'],'student_code'=>$essayInfo['student_code'],'student_name'=>$essayInfo['student_name']))?>" >名称<?php if($key_name=='thumb_name'):?><img src="/static/images/asc.png" align="absmiddle"><?php endif;?></a>|
							<a href="<?php echo U('Essay/Essay/essayImgList',array('key_name'=>'instime','order'=>'desc','class_code'=>$essayInfo['class_code'],'speaker_number'=>$essayInfo['speaker_number'],'student_code'=>$essayInfo['student_code'],'student_name'=>$essayInfo['student_name']))?>" >时间<?php if($key_name=='instime'):?><img src="/static/images/asc.png" align="absmiddle"><?php endif;?></a>
						<?php else:?>
							<a href="<?php echo U('Essay/Essay/essayImgList',array('key_name'=>'thumb_name','order'=>'asc','class_code'=>$essayInfo['class_code'],'speaker_number'=>$essayInfo['speaker_number'],'student_code'=>$essayInfo['student_code'],'student_name'=>$essayInfo['student_name']))?>" >名称<?php if($key_name=='thumb_name'):?><img src="/static/images/desc.png" align="absmiddle"><?php endif;?></a>|
							<a href="<?php echo U('Essay/Essay/essayImgList',array('key_name'=>'instime','order'=>'asc','class_code'=>$essayInfo['class_code'],'speaker_number'=>$essayInfo['speaker_number'],'student_code'=>$essayInfo['student_code'],'student_name'=>$essayInfo['student_name']))?>" >时间<?php if($key_name=='instime'):?><img src="/static/images/desc.png" align="absmiddle"><?php endif;?></a>
						<?php endif;?>
					<?php else:?>
						&nbsp;
					<?php endif;?>
				</span>
			</h2>
			<?php if(!empty($essayImgsList)):?>
				<div id="preview" class="preview" style="margin-left:10px;">
					<?php foreach($essayImgsList as $key=>$essayImg):?>
						<li><a href="#" onclick="testMessageBox_show_essayImg(event,'<?php echo $essayImg['url'];?>','<?php echo $essayImg['show_width'];?>','<?php echo $essayImg['show_height'];?>')"><img src="<?php echo $essayImg['url'];?>" width="<?php echo $essayImg['thumb_width'];?>" height="<?php echo $essayImg['thumb_height'];?>"></a>
							<div class="img_name"><!--<input type="checkbox" id="img_<?php echo $key;?>" name="img[]" value="<?php echo $essayImg['url'];?>"> -->
							<?php echo $essayImg['thumb_name'];?></div>
						</li>
					<?php endforeach?>	
				</div><br>
				<div>
					<span class="font14">
						<!--<input type="checkbox" name="checkAll" id="checkAll" value="1" class="checkAll">全选-->
						<input type="button" value="添加到 “优秀作文选”" onclick="do_excellent('<?php echo U('Essay/Essay/do_excellent')?>','add','<?php echo $essayInfo['class_code'];?>','<?php echo $essayInfo['speaker_number'];?>','<?php echo $essayInfo['id'];?>')">
					</span>
				</div><br>
			<?php else:?>
				&nbsp;&nbsp;&nbsp;&nbsp;暂无作文照片
			<?php endif;?>
		</div>
	</div>
</div>
</body>
</html>