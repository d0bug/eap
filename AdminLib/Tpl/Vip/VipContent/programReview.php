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
		<form id="documentManageForm" name="documentManageForm" method="POST" action="<?php echo U('Vip/VipContent/programReview')?>">
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
				<!--<?php foreach($studentList as $key=>$student):?>
					<option value="<?php echo $student['sstudentcode'];?>" <?php if($studentCode==$student['sstudentcode']):?>selected<?php endif;?> ><?php echo $student['sstudentname'];?></option>
				<?php endforeach?>-->
			</select>&nbsp;&nbsp;
			时间：<input type="text"  class="Wdate" id="starttime" name="starttime" value="<?php echo $startTime;?>" placeholder="开始时间" onClick="WdatePicker()"> 至 <input type="text"  class="Wdate" id="endtime" name="endtime" value="<?php echo $endTime;?>" placeholder="结束时间" onClick="WdatePicker()">&nbsp;&nbsp;
			教师姓名：<input type="text" id="teacherName" name="teacherName" value="<?php echo urldecode($teacherName);?>" placeholder="教师姓名">&nbsp;&nbsp;
			<input type="submit" value="查询">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo U('Vip/VipContent/exportProgramData',array('deptCode'=>$deptCode,'teacherCode'=>$teacherCode,'studentCode'=>$studentCode,'starttime'=>$startTime,'endtime'=>$endTime,'teacherName'=>urldecode($teacherName)));?>" class="blue">导出excel</a>
		</form>
	</div>
	<hr>
	<div id="list" class="clearfix">
		<?php if($programList):?>
		<table width="100%" border="1">
			<tr bgcolor="#dddddd" height=30>
				<th width="10%">上传时间</th>
				<th width="5%">教师所属校区</th>
				<th width="5%">教师</th>
				<th width="5%">学员</th>
				<th width="5%">课程编码</th>
				<th width="5%">课程名称</th>
				<th width="10%">上传方式</th>
				<th width="30%">辅导方案</th>
				<th width="5%">文件数量</th>
			</tr>
			<?php foreach($programList as $key=>$program):?>
			<tr height=30>
				<td><?php echo $program['instime2'];?></td>
				<td><?php echo $program['dept_name'];?></td>
				<td><?php echo $program['teacher_name'];?></td>
				<td><?php echo $program['student_name'];?></td>
				<td><?php echo $program['kecheng_code'];?></td>
				<td><?php echo $program['kecheng_name'];?></td>
				<td><?php echo ($program['from_type']==1)?'微信':'PC';?></td>
				<td>
				<?php if($program['program_arr']):?>
					<?php foreach ($program['program_arr'] as $k=>$val):?>
						<p><?php echo $val['url']?>&nbsp;&nbsp;
						   <?php if($val['is_download']==1):?>
							   	<a href="<?php echo U('Vip/VipStudents/download',array('id'=>$program['id'],'type'=>3,'order'=>$k))?>" class="blue">下载</a>
							   <?php endif;?>&nbsp;&nbsp;
							   <?php if($val['is_preview']==1):?>
							   		<a href="#" class="blue" onclick="testMessageBox_view_file(event,'/vip/vip_content/view_file/url/<?php echo str_replace('/','|',str_replace('.','_',$val['preview_url']))?>')">预览</a>
							   <?php endif;?>
							</p>
					<?php endforeach;?>
				<?php endif;?>
				</td>
				<td><?php echo (count($program['program_arr'])==0)?'<font color=red>0</font>':count($program['program_arr']);?></td>
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