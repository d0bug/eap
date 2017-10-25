<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<table border="0">
		<tr>
			<td><img src="/static/images/default_avatar.jpg" width="80" height="80"></td>
			<td valign="top">
				<p>&nbsp;&nbsp;<font class="f_20"><?php echo $studentInfo['sstudentname']?></font>&nbsp;|&nbsp;<?php echo $studentInfo['sschool']?></p>
				<p>&nbsp;&nbsp;<?php echo $studentInfo['gradename']?></p>
			</td>
		</tr>
	</table><br>
	<div class="tableTab">
		<ul class="tab">
			<li class="current">
				<a href="<?php echo U('Vip/VipStudents/newStudentLesson',array('student_code'=>$student_code))?>">学员课程</a>
			</li>
			<li >
				<a href="<?php echo U('Vip/VipStudents/newStudentProgram2',array('student_code'=>$student_code))?>">辅导方案</a>
			</li>
			<li >
				<a href="<?php echo U('Vip/VipStudents/newStudentMessage',array('student_code'=>$student_code))?>">留言板</a>
			</li>
			<li >
				<a href="<?php echo U('Vip/VipStudents/newStudentInfo',array('student_code'=>$student_code))?>">学员详情</a>
			</li>
			<li >
				<a href="<?php echo U('Vip/VipStudents/newStudentErrorQuestion',array('student_code'=>$student_code))?>">错题书包</a>
			</li>
			<li >
				<a href="<?php echo U('Vip/VipStudents/vipProgramList',array('student_code'=>$student_code))?>">课程规划</a>
			</li>
		</ul>
	</div><br/>
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableInfo">
				<tr bgcolor="#DDDDDD" height=35>
					<td width="5%">操作</td>
					<td width="10%">上课时间</td>
					<td width="10%">所属课程</td>
					<td width="5%">课次</td>
					<td width="8%">课次主题</td>
					<td width="23%">讲义</td>
					<td width="9%">测试卷</td>
					<td width="30%">课堂评价</td>
					
				</tr>
		<?php if(!empty($lessonList)):?>
			<?php foreach($lessonList as $key=>$lesson):?>
				<tr height=30>
					<td>
						<?php if(!empty($lesson['lecture_id'])):?>
							<?php if(!empty($lesson['lesson_report_url'])):?>
								已上课<br><br>
								<a href="<?php echo U('Vip/VipStudents/recordLessonTrack',array('helu_id'=>$lesson['heluid']));?>" class="blue" target="_blank">修改</a>
							<?php else:?>
								<?php if($lesson['overdue']==1):?>
									逾期未核录
								<?php else:?>
									<?php if(strtotime($lesson['dtdatereal'].' '.$lesson['dtlessonendreal']) <= time()): ?>
									<a href="<?php echo U('Vip/VipStudents/recordLessonTrack',array('helu_id'=>$lesson['heluid']));?>" class="blue" target="_blank">核录</a><br><br>
									<?php endif; ?>
									<a href="#" onclick="clear_lecture('<?php echo U('Vip/VipStudents/clearLecture')?>','<?php echo $lesson['heluid']?>')" class="blue">清空</a>
								<?php endif;?>
							<?php endif;?>
							
							
						<?php else:?>
							<?php if(empty($lesson['lecture_id'])):?>
								<input type="button" value="备课" onclick="javascript:window.open('<?php echo C('PREPARE_URL')?>')">
							<?php endif;?>
						<?php endif;?>
							
						<?php if(!empty($lesson['lesson_report_url'])):?>
							<br><br>
							<a href="javascript:void(0)" onclick="show_report('<?php echo APP_URL.$lesson['show_lesson_report_url']?>')" class="blue" >查看学习报告</a>
							<!--<?php if($lesson['lesson_report_img_is_download']==1):?>&nbsp;&nbsp;&nbsp;&nbsp;
								<a href="<?php echo U('Vip/VipStudents/downloadReport',array('helu_id'=>$lesson['heluid']))?>"  class="blue" >下载学习报告</a>
							<?php endif;?>-->
						<?php endif;?>
						
					</td>
					<td><?php echo $lesson['dtdatereal'].' '.$lesson['dtlessonbeginreal'].'~'.$lesson['dtlessonendreal'];?></td>
					<td><?php echo $lesson['skechengname'];?>（<?php echo $lesson['skechengcode'];?>）</td>
					<td><?php echo $lesson['nlessonno'];?></td>
					<td><?php echo $lesson['lesson_topic'];?></td>
						
					<td><?php if($lesson['handouts']):?>
							<?php foreach($lesson['handouts'] as $k=>$file):?>
								<a href="<?php if($file['is_exist']==1):?><?php echo U('Vip/VipStudents/download',array('id'=>$lesson['id'],'type'=>'0','order'=>$k));?><?php else:?>#none<?php endif;?>" <?php if($file['is_exist']==0):?>title="文件不存在" onclick="javascript:alert('文件不存在');"<?php endif;?> >
								<img src="/static/images/<?php if($file['filetype']=='pdf'):?>pdf.gif<?php elseif($file['filetype']=='doc'||$file['filetype']=='docx'):?>doc.gif<?php elseif($file['filetype']=='ppt'||$file['filetype']=='pptx'):?>ppt.png<?php elseif($file['filetype']=='xls'||$file['filetype']=='xlsx'):?>xls.png<?php elseif($file['filetype']=='jpg'||$file['filetype']=='jpeg'||$file['filetype']=='gif'||$file['filetype']=='png'):?>img.png<?php else:?>file.png<?php endif;?>"><?php echo $lesson['handouts_title'].'('.$k.')'.'.'.$file['filetype'];?></a>&nbsp;&nbsp;
								<?php if($file['filetype']=='jpg'||$file['filetype']=='jpeg'||$file['filetype']=='gif'||$file['filetype']=='png'):?>
									<a href="#none" onclick="<?php if($file['is_exist']==0):?>javascript:alert('文件不存在');<?php else:?>testMessageBox_previewImg(event,'<?php echo APP_URL.$file['url_show'];?>')<?php endif;?>" class="blue">预览</a>
								<?php endif;?><br>
							<?php endforeach?>
						<?php elseif(!empty($lesson['lecture_title'])):?>
							<a href="<?php echo U('Vip/VipStudents/previewLecture',array('lecture_id'=>$lesson['lecture_id'],'helu_id'=>$lesson['heluid']))?>" target="_blank"><?php echo $lesson['lecture_title']?></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo U('Vip/VipStudents/previewLecture',array('lecture_id'=>$lesson['lecture_id'],'helu_id'=>$lesson['heluid']))?>" target="_blank" class="blue">预览</a>&nbsp;&nbsp;
							
							<!--<a href="#" onclick="return testMessageBox_downloadLecture(event,'<?php echo $lesson['lecture_id']?>','<?php echo U('Vip/VipStudents/downloadLecture')?>')" class="blue">下载</a>-->
						<?php endif;?><br>
					</td>
					<td>
						<?php if($lesson['itembank']):?>
							<?php foreach($lesson['itembank'] as $k=>$file):?>
								<a href="<?php if($file['is_exist']==1):?><?php echo U('Vip/VipStudents/download',array('id'=>$lesson['id'],'type'=>'1','order'=>$k));?><?php else:?>#none<?php endif;?>" <?php if($file['is_exist']==0):?>title="文件不存在" onclick="javascript:alert('文件不存在');"<?php endif;?> >
								<img src="/static/images/<?php if($file['filetype']=='pdf'):?>pdf.gif<?php elseif($file['filetype']=='doc'||$file['filetype']=='docx'):?>doc.gif<?php elseif($file['filetype']=='ppt'||$file['filetype']=='pptx'):?>ppt.png<?php elseif($file['filetype']=='xls'||$file['filetype']=='xlsx'):?>xls.png<?php elseif($file['filetype']=='jpg'||$file['filetype']=='jpeg'||$file['filetype']=='gif'||$file['filetype']=='png'):?>img.png<?php else:?>file.png<?php endif;?>"><?php echo $lesson['itembank_title'].'('.$k.')'.'.'.$file['filetype'];?></a>&nbsp;&nbsp;
								<?php if($file['filetype']=='jpg'||$file['filetype']=='jpeg'||$file['filetype']=='gif'||$file['filetype']=='png'):?>
									<a href="#none" onclick="<?php if($file['is_exist']==0):?>javascript:alert('文件不存在');<?php else:?>testMessageBox_previewImg(event,'<?php echo APP_URL.$file['url_show'];?>')<?php endif;?>" class="blue">预览</a>
								<?php endif;?>
								<br>
							<?php endforeach?>
						<?php endif;?>
					</td>
					<td><?php echo $lesson['comment'];?></td>
					
				</tr>
			<?php endforeach?>
		<?php endif;?>
	</table>
	<div id="pageStr"><?php echo $showPage;?></div>
</div>
</div>
</body>
</html>