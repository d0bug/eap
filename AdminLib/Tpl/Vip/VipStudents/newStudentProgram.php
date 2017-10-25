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
<link href="/static/css/uploadify.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
<style type="text/css">
.tableInfo td { border: 1px solid #ddd; color: #666; padding: 10px 5px;}
</style>
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
			<li >
				<a href="<?php echo U('Vip/VipStudents/newStudentLesson',array('student_code'=>$student_code))?>">学员课程</a>
			</li>
			<li class="current">
				<a href="<?php echo U('Vip/VipStudents/newStudentProgram',array('student_code'=>$student_code))?>">辅导方案</a>
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
	<table class="tableInfo" border="0" width="80%">
					<tr>
						<td valign="top" width="100">培养方案： </td>
						<td valign="top">
							<input type="hidden" id="uploadimg_url" name="uploadimg_url" value="<?php echo U('Vip/VipInfo/upload_img')?>">
							<input type="hidden" id="delimg_url" name="delimg_url" value="<?php echo U('Vip/VipInfo/del_img')?>">
							<input type="hidden" id="is_preview" name="is_preview" value="1">
							<input type="hidden" id="from_type" name="from_type" value="0">
							<input type="hidden" id="teacher_code" name="teacher_code" value="<?php echo $userInfo['sCode']?>">
							<input type="hidden" id="teacher_name" name="teacher_name" value="<?php echo $userInfo['real_name']?>">
							<select id="kecheng_code" name="kecheng_code">
								<option value="">请选择相应课程</option>
							<?php if($kechengList):?>
								<?php foreach ($kechengList as $key=>$kecheng):?>
								<option value="<?php echo $kecheng['skechengcode']?>"><?php echo $kecheng['skechengname']?>(<?php echo $kecheng['skechengcode']?>)</option>
								<?php endforeach;?>
							<?php endif;?>
							</select><br><br>
							<input type="file" id="upload_item_bank" value="上传"></span>
							<label id="upload_itembank_msg" class="success"></label><br>
							<span id="view_teacher_file"></span><br>
							<input type="hidden" id="teacher_version" name="teacher_version" value="">
							<input type="button" value="保存" class="btn" onclick="add_trainingProgram('<?php echo $student_code?>','<?php echo $studentInfo['sstudentname']?>','<?php echo U('Vip/VipStudents/addTrainingProgram')?>')">
						</td>
					</tr>
				</table><br>
				<div id="programList">
					<table border="1" width="80%">
						<tr>
							<th align="center">上传时间</th>
							<th align="center">所属课程</th>
							<th align="center">辅导方案</th>
							<th align="center">上传方式</th>
							<th align="center">操作</th>
						</tr>
					<?php if(!empty($programList)):?>
						<?php foreach($programList as $key=>$program):?>
							<tr id="p_<?php echo $program['id'];?>">
								<td ><?php echo $program['instime'];?></td>
								<td ><?php echo $program['kecheng_name'];?></td>
								<td >
								<!--<img src="/static/images/<?php if($program['file_type']=='pdf'):?>pdf.gif<?php elseif($program['file_type']=='doc'||$program['file_type']=='docx'):?>doc.gif<?php elseif($program['file_type']=='ppt'||$program['file_type']=='pptx'):?>ppt.png<?php elseif($program['file_type']=='xls'||$program['file_type']=='xlsx'):?>xls.png<?php else:?>file.png<?php endif;?>">
									<a href="<?php if($program['is_exist']==1):?><?php echo U('Vip/VipStudents/download',array('id'=>$program['id'],'type'=>'3'));?><?php else:?>#none<?php endif;?>" <?php if($program['is_exist']==0):?>title="文件不存在" onclick="javascript:alert('文件不存在');"<?php endif;?>><?php echo $program['student_name'].'培养方案'.$program['instime']?></a>&nbsp;&nbsp;&nbsp;-->
								<?php if($program['program_arr']):?>
									<?php foreach ($program['program_arr'] as $k=>$val):?>
										<p><?php echo $val['url']?>&nbsp;&nbsp;
										   <?php if($val['is_download']==1):?>
											   	<a href="<?php echo U('Vip/VipStudents/download',array('id'=>$program['id'],'type'=>3,'order'=>$k))?>" class="blue">下载</a>
											   <?php endif;?>&nbsp;&nbsp;
											   <!--<?php if($val['is_preview']==1):?>
											   		<a href="#" class="blue" onclick="testMessageBox_view_file(event,'/vip/vip_content/view_file/url/<?php echo str_replace('/','|',str_replace('.','_',$val['preview_url']))?>')">预览</a>
											   <?php endif;?>-->
											</p>
									<?php endforeach;?>
								<?php endif;?>
									
								</td>
								<td><?php if($program['from_type']==1):?>微信<?php else:?>PC<?php endif;?></td>
								<td><a href="#none" onclick="del_program('#p_<?php echo $program['id'];?>','<?php echo U('Vip/VipStudents/del_program',array('id'=>$program['id']));?>')" class="blue">删除</a></td>
							</tr>
						<?php endforeach?>
					<?php endif;?>
					</table>
				</div>
</div>
</div>
</body>
</html>