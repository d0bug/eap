<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<div id="search">
	<form id="documentManageForm" name="documentManageForm" method="POST" action="<?php echo U('Vip/VipContent/lessonReview')?>">
		校区：<select id="deptCode" name="deptCode" onchange="get_teacherList(this.value,'<?php echo U('Vip/VipContent/getTeacherList')?>')">
			<option value="">请选择校区</option>
			<?php foreach($deptList as $key=>$dept):?>
				<option value="<?php echo $dept['scode'];?>" <?php if($deptCode==$dept['scode']):?>selected<?php endif;?> ><?php echo $dept['sname'];?></option>
			<?php endforeach?>
		</select>&nbsp;&nbsp;
		教师：<select id="teacherCode" name="teacherCode" onchange="get_studentList(this.value,'<?php echo U('Vip/VipContent/getStudentList')?>')">
			<option value="">请选择教师</option>
			<?php foreach($teacherList as $key=>$teacher):?>
				<option value="<?php echo $teacher['scode'];?>" <?php if($teacherCode==$teacher['scode']):?>selected<?php endif;?> ><?php echo $teacher['sname'];?></option>
			<?php endforeach?>
		</select>&nbsp;&nbsp;
		学员：<select id="studentCode" name="studentCode">
			<option value="">请选择学员</option>
			<?php foreach($studentList as $key=>$student):?>
				<option value="<?php echo $student['sstudentcode'];?>" <?php if($studentCode==$student['sstudentcode']):?>selected<?php endif;?> ><?php echo $student['sstudentname'];?></option>
			<?php endforeach?>
		</select>&nbsp;&nbsp;
		时间：<input type="text"  class="Wdate" id="starttime" name="starttime" value="<?php echo $startTime;?>" placeholder="开始时间" onClick="WdatePicker()"> 至 <input type="text"  class="Wdate" id="endtime" name="endtime" value="<?php echo $endTime;?>" placeholder="结束时间" onClick="WdatePicker()">&nbsp;&nbsp;
		讲义上传：<select id="is_upload" name="is_upload">
					<option value="" <?php if($is_upload==''):?>selected<?php endif;?>>全部</option>
					<option value="1" <?php if($is_upload==1):?>selected<?php endif;?>>是</option>
					<option value="2" <?php if($is_upload==2):?>selected<?php endif;?>>否</option>
				</select>&nbsp;&nbsp;
		教师姓名：<input type="text" id="teacherName" name="teacherName" value="<?php echo urldecode($teacherName);?>" placeholder="教师姓名">&nbsp;&nbsp;
		学员姓名：<input type="text" id="studentName" name="studentName" value="<?php echo urldecode($studentName);?>" placeholder="学员姓名">&nbsp;&nbsp;
		<input type="submit" value="查询">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo U('Vip/VipContent/exportFileData',array('deptCode'=>$deptCode,'teacherCode'=>$teacherCode,'studentCode'=>$studentCode,'starttime'=>$startTime,'endtime'=>$endTime,'is_upload'=>$is_upload,'teacherName'=>urldecode($teacherName),'studentName'=>urldecode($studentName)));?>" class="blue">导出excel</a>
	</form>
	</div>
	<hr>
	<div id="list" class="clearfix">
		<?php if($lessonHeluList):?>
		<table width="100%" border="1">
			<tr bgcolor="#dddddd" height=30>
				<td width="5%">上课时间</td>
				<td width="3%">教师所属校区</td>
				<td width="5%">教师</td>
				<td width="5%">学员</td>
				<td width="3%">课次时长(h)</td>
				<td width="5%">课堂主题</td>
				<td width="10%">课堂评价</td>
				<td width="2%">讲义数量</td>
				<td width="3%">讲义上传方式</td>
				<td width="22%">讲义预览</td>
				<td width="2%">测试卷数量</td>
				<td width="2%">测试卷上传方式</td>
				<td width="22%">测试卷预览</td>
				<td width="12%">课节报告</td>
			</tr>
			<?php foreach($lessonHeluList as $key=>$lessonHelu):?>
			<tr height=30>
				<td><?php echo $lessonHelu['dtdatereal'].'<br>'.$lessonHelu['dtlessonbeginreal'].'-'.$lessonHelu['dtlessonendreal'];?></td>
				<td><?php echo $lessonHelu['sareaname'];?></td>
				<td><?php echo $lessonHelu['steachername'];?></td>
				<td><?php echo $lessonHelu['sstudentname'];?></td>
				<td><?php echo $lessonHelu['nhoursreal'];?></td>
				<td><?php echo !empty($lessonHelu['lesson_topic'])?$lessonHelu['lesson_topic']:'';?></td>
				<td><?php echo $lessonHelu['comment'];?></td>
				<td><?php echo (count($lessonHelu['handouts_arr'])==0)?'<font color=red>0</font>':count($lessonHelu['handouts_arr']);?></td>
				<td><?php if($lessonHelu['handouts_arr']):?><?php if($lessonHelu['handouts_from_type']==1):?>微信<?php else:?>PC<?php endif;?><?php endif;?></td>
				<td>
					<?php if(!empty($lessonHelu['recordimg_arr'])):?>
						<?php if($lessonHelu['recordimg_arr']):?>
							<?php foreach ($lessonHelu['recordimg_arr'] as $k=>$recordimg):?>
								<p><?php echo $recordimg['url']?>&nbsp;&nbsp;
								   <?php if($recordimg['is_download']==1):?>
								   		<a href="<?php echo U('Vip/VipContent/downloadFile',array('name'=>$lessonHelu['lesson_topic'].'_'.$lessonHelu['sstudentname'].'_讲义文件'.'_'.$k,'file_type'=>end(explode('.',$recordimg['file_url'])),'url'=>str_replace('/','|',str_replace('.','=',$recordimg['file_url']))))?>" class="blue" >下载</a>
								   <?php endif;?>&nbsp;&nbsp;
								   <?php if($recordimg['is_preview']==1):?>
								   		<a href="#" class="blue" onclick="testMessageBox_view_file(event,'/vip/vip_content/view_file/url/<?php echo str_replace('/','|',$recordimg['preview_url'])?>/file_type/<?php $recordimg['file_type']?>')">预览</a>
								   <?php endif;?>
								</p>
							<?php endforeach;?>
						<?php endif;?>
					<?php else:?>
						<?php if($lessonHelu['handouts_arr']):?>
							<?php foreach ($lessonHelu['handouts_arr'] as $k=>$handout):?>
								<p><?php echo $handout['url']?>&nbsp;&nbsp;
								   <?php if($handout['is_download']==1):?>
								   		<!--<a href="<?php echo U('Vip/VipContent/downloadFile',array('name'=>$lessonHelu['lesson_topic'].'_'.$lessonHelu['sstudentname'].'_讲义文件'.'_'.$k,'url'=>str_replace('/','|',str_replace('.','_',$handout['url']))))?>" class="blue">下载</a>-->
								   		<a href="<?php echo U('Vip/VipStudents/download',array('id'=>$lessonHelu['helu_info_id'],'type'=>1,'order'=>$k))?>" class="blue">下载</a>
								   <?php endif;?>&nbsp;&nbsp;
								   <?php if($handout['is_preview']==1):?>
								   		<a href="#" class="blue" onclick="testMessageBox_view_file(event,'/vip/vip_content/view_file/url/<?php echo str_replace('/','|',$handout['preview_url'])?>/file_type/<?php $handout['file_type']?>')">预览</a>
								   <?php endif;?>
								</p>
							<?php endforeach;?>
						<?php endif;?>
					<?php endif;?>
					
				</td>
				<td><?php echo (count($lessonHelu['itembank_arr'])==0)?'<font color=red>0</font>':count($lessonHelu['itembank_arr']);?></td>
				<td><?php if($lessonHelu['itembank_arr']):?><?php if($lessonHelu['itembank_from_type']==1):?>微信<?php else:?>PC<?php endif;?><?php endif;?></td>
				<td>
					<?php if($lessonHelu['itembank_arr']):?>
						<?php foreach ($lessonHelu['itembank_arr'] as $k=>$itembank):?>
							<p><?php echo $itembank['url']?>&nbsp;&nbsp;
							   <?php if($itembank['is_download']==1):?>
							   		<a href="<?php echo U('Vip/VipStudents/download',array('id'=>$lessonHelu['helu_info_id'],'type'=>2,'order'=>$k))?>" class="blue">下载</a>
							   <?php endif;?>&nbsp;&nbsp;
							   <?php if($itembank['is_preview']==1):?>
							   		<a href="#" class="blue" onclick="testMessageBox_view_file(event,'/vip/vip_content/view_file/url/<?php echo str_replace('/','|',$itembank['preview_url'])?>/file_type/<?php $itembank['file_type']?>')">预览</a>
							   <?php endif;?>
							</p>
						<?php endforeach;?>
					<?php endif;?>
				</td>
				<td>
					<?php if(!empty($lessonHelu['lesson_report_url'])):?>
						<?php echo $lessonHelu['lesson_report_url']?><br><a href="<?php echo $lessonHelu['lesson_report_url_show']?>" target="_blank" class="blue">预览</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="blue">下载</a>
					<?php endif;?>
				</td>
			</tr>
			<?php endforeach?>
		</table>
		<div id="pageStr"><?php echo $showPage;?></div>
		<?php else:?>
		<div>暂无相关信息</div>
		<?php endif;?>
	</div>
</div>
</div>
</body>
</html>