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
<div id="main"><?php if($userInfo['real_name']=='解翠萍'):?><a href="<?php echo U('Vip/VipWorks/dealData')?>">处理数据</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo U('Vip/VipWorks/updateFileTitle')?>">更新文档名称</a>&nbsp;&nbsp;&nbsp;<br><br><?php endif;?>
	<?php if(!empty($userInfo['sCode'])):?>
	<h2>待核录学员&nbsp;&nbsp;&nbsp;<span class="error f_12">温馨提示： 敬爱的老师，您有以下学员还未核录课时哦~~</span></h2>
	<div id="list2" class="clearfix">
		<ul>
		<?php if(!empty($waitHeluList)):?>
			<?php foreach($waitHeluList as $key=>$waitHelu):?>
				<li>
					<p><img src="/static/images/tx.png"></p>
					<p><a href="<?php echo U('Vip/VipStudents/studentInfo',array('student_code'=>$waitHelu['sstudentcode'],'kecheng_code'=>$waitHelu['skechengcode'],'lesson'=>$waitHelu['nlessonno']));?>" class="orange" target="_blank"><?php echo $waitHelu['sstudentname'];?></a></p>
					<p><?php echo $waitHelu['dtdatereal'];?></p>
					<p><?php echo $waitHelu['dtlessonbeginreal'];?>~<?php echo $waitHelu['dtlessonendreal'];?></p>
					<p>
						<?php if($waitHelu['overdue'] == 0):?>
							<input type="button" onclick="javascript:window.open('<?php echo U('Vip/VipStudents/recordLessonTrack',array('helu_id'=>$waitHelu['heluid']))?>')" value="核录">&nbsp;&nbsp;&nbsp;
						<?php else:?>
							<input type="button" onclick="do_overdue('<?php echo U('Vip/VipWorks/doOverdue',array('helu_id'=>$waitHelu['heluid']))?>')" value="逾期未核录">&nbsp;&nbsp;&nbsp;
						<?php endif;?>
					</p>
				</li>
			<?php endforeach?>
		<?php endif;?>
		</ul>
	</div>
	
	
	
	<h2>待备课学员&nbsp;&nbsp;&nbsp;<span class="error f_12">温馨提示： 此处仅显示近一周待备课学员，更多待备课学员请到学员课程详情中查看~~</span></h2>
	<div id="list2" class="clearfix">
		<ul>
		<?php if(!empty($waitPrepareList)):?>
			<?php foreach($waitPrepareList as $key=>$waitPrepare):?>
				<li>
					<p><img src="/static/images/tx.png"></p>
					<p><a href="<?php echo U('Vip/VipStudents/newStudentLesson',array('student_code'=>$waitPrepare['sstudentcode']));?>" class="orange" target="_blank"><?php echo $waitPrepare['sstudentname'];?></a></p>
					<p><?php echo $waitPrepare['dtdatereal'];?></p>
					<p><?php echo $waitPrepare['dtlessonbeginreal'];?>~<?php echo $waitPrepare['dtlessonendreal'];?></p>
					<p>
						<input type="button" onclick="javascript:window.location.href='<?php echo C('PREPARE_URL')?>'" value="备课">
					</p>
				</li>
			<?php endforeach?>
		<?php endif;?>
		</ul>
	</div>
	
	
	<h2>待生成辅导方案学员&nbsp;&nbsp;&nbsp;</h2>
	<div id="list2" class="clearfix">
		<ul>
		<?php if(!empty($testCoachList)):?>
			<?php foreach($testCoachList as $key=>$testCoach):?>
				<li>
					<p><img src="/static/images/tx.png"></p>
					<p><a href="<?php echo U('Vip/VipStudents/newStudentLesson',array('student_code'=>$testCoach['sstudentcode']));?>" class="orange" target="_blank"><?php echo $testCoach['studentname'];?></a></p>
					<p><?php echo $testCoach['dtauditenddate'];?></p>
					<p>
						<input type="button"  value="生成" onclick="javascript:window.open('<?php echo U('Vip/VipStudents/newStudentProgram2',array('student_code'=>$testCoach['sstudentcode']))?>')">&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="button"  value="无效" onclick="testMessageBox_program_useless(event,'<?php echo U('Vip/VipStudents/programUseLess',array('id'=>$testCoach['id']))?>')">
					</p>
				</li>
			<?php endforeach?>
		<?php endif;?>
		</ul>
	</div>
	
	
	<?php endif;?>
	<?php if(!empty($messageList)):?>
	<h2>审核未通过&nbsp;&nbsp;&nbsp;<span class="error f_12">温馨提示： 敬爱的老师，您有以下信息未通过审核，请及时修改~~</span></h2>
	<ul>
		<?php foreach($messageList as $key=>$message):?>
			<li><img src="/static/images/ann_icon.gif"> <?php echo $message['message'];?>&nbsp;&nbsp;&nbsp;<a href="<?php echo $message['url'];?>" class="error">修改</a></li>
		<?php endforeach?>
	</ul>
	<?php endif;?>
	<a name='showHistory'></a>
	<h2>标准化讲义统计&nbsp;&nbsp;&nbsp;<span class="error f_12">温馨提示： 复制讲义名，可在PIV4.0-备课-教研标准化讲义 中搜索到相应讲义</span></h2>
	<div class="tableTab">
		<ul class="tab">
			<li >
				<a href="/vip/vip_works/newIndex#showMy">本周更新</a>
			</li>
			<li class="current">
				<a href="/vip/vip_works/newMyworkhistory#showHistory">历史统计</a>
			</li>
		</ul>
	</div><br/>
	
	<form id="search_form" name="search_form" method="POST" action="<?php echo U('Vip/vip_works/newMyworkhistory');?>">
		<select id="grade" name="grade" onchange="get_options(this.value,'grade','<?php echo U('Vip/VipHandouts/get_grade_option')?>')">
			<option value="">请选择年部</option>
			<?php foreach($gradeArr as $key=>$grade):?>
				<option value="<?php echo $grade['id'];?>" <?php if($handouts_subject==$grade['id']):?>selected<?php endif;?> ><?php echo $grade['title'];?></option>
			<?php endforeach?>
		</select>
		<select id="course_id_one" name="course_id_one" onchange="get_options(this.value,'course_id_one','<?php echo U('Vip/VipHandouts/get_course_one_option',array('ntype'=>0))?>','list')">
			<option value="">请选择科目</option>
			<?php foreach($subjectArr as $key=>$subject):?>
				<option value="<?php echo $subject['id'];?>" <?php if($handouts_subject==$subject['id']):?>selected<?php endif;?> ><?php echo $subject['title'];?></option>
			<?php endforeach?>
		</select>
		<select id="knowledge_type" name="knowledge_type" onchange="get_options(this.value,'knowledge_type','<?php echo U('Vip/VipHandouts/get_knowledge_type_option')?>')">
			<option value="">请选择教材</option>
			<?php foreach($knowledge_typeArr as $key=>$type):?>
				<option value="<?php echo $type['id'];?>" <?php if($handouts_subject==$type['id']):?>selected<?php endif;?> ><?php echo $type['title'];?></option>
			<?php endforeach?>
		</select>
		<select name="course_id_two" id="course_id_two" onchange="get_knowledge_options(this.value,'<?php echo U('Vip/VipHandouts/get_knowledge_options')?>');">
				<option value="">请选择课程属性</option>
				<?php foreach($gradesArr as $key=>$grade):?>
					<option value="<?php echo $grade['id'];?>"  <?php if($handouts_grade == $grade['id']):?>selected<?php endif;?>><?php echo $grade['title'];?></option>
				<?php endforeach?>
		</select>&nbsp;&nbsp;
		<select name="course_id_three" id="course_id_three">
			<option value="">请选择讲义属性</option>
			<?php foreach($knowledgeArr as $key=>$knowledge):?>
				<option value="<?php echo $knowledge['kid'];?>"  <?php if($handouts_knowledge == $knowledge['gid']):?>selected<?php endif;?>><?php echo $knowledge['name'];?></option>
			<?php endforeach?>
		</select>&nbsp;&nbsp;
		查询时间：<input type="text"  class="Wdate" id="startTime" name="startTime" value="<?php echo $startTime;?>" onClick="WdatePicker()">至
		 <input type="text"  class="Wdate" id="endTime" name="endTime" value="<?php echo $endTime;?>"  onClick="WdatePicker()">&nbsp;&nbsp;&nbsp;<input type="submit" value="确认查看">
	</form><br/>
	
	<!--
	<form id="search_form" name="search_form" method="POST" action="<?php echo U('Vip/vip_works/newMyworkhistory');?>">
		<select id="course_id_one" name="course_id_one" onchange="get_option(this.value,'grade','<?php echo U('Vip/VipHandouts/get_course_one_option',array('ntype'=>0))?>','list')">
			<option value="">请选择科目</option>
		<?php if($type!=3):?>
			<?php foreach($subjectArr as $key=>$subject):?>
				<option value="<?php echo $subject['sid'];?>" <?php if($handouts_subject==$subject['sid']):?>selected<?php endif;?> ><?php echo $subject['name'];?></option>
			<?php endforeach?>
		<?php endif;?>
		</select>
		<select name="grade" id="grade" onchange="get_knowledge_option(this.value,'<?php echo U('Vip/VipHandouts/get_knowledge_option')?>');">
				<option value="">请选择课程属性</option>
				<?php foreach($gradeArr as $key=>$grade):?>
					<option value="<?php echo $grade['gid'];?>"  <?php if($handouts_grade == $grade['gid']):?>selected<?php endif;?>><?php echo $grade['name'];?></option>
				<?php endforeach?>
		</select>&nbsp;&nbsp;
		<select name="knowledge" id="knowledge" >
			<option value="">请选择讲义属性</option>
			<?php foreach($knowledgeArr as $key=>$knowledge):?>
				<option value="<?php echo $knowledge['kid'];?>"  <?php if($handouts_knowledge == $knowledge['gid']):?>selected<?php endif;?>><?php echo $knowledge['name'];?></option>
			<?php endforeach?>
		</select>&nbsp;&nbsp;
		查询时间：<input type="text"  class="Wdate" id="startTime" name="startTime" value="<?php echo $startTime;?>" onClick="WdatePicker()">至
		 <input type="text"  class="Wdate" id="endTime" name="endTime" value="<?php echo $endTime;?>"  onClick="WdatePicker()">&nbsp;&nbsp;&nbsp;<input type="submit" value="确认查看">
	</form><br/>
-->
	<div>
		<!--
		<table width="35%" border="1" style="float:left" id="courseJiangYi">
			<tr bgcolor="#dddddd" height=35>
				<th width = '20%'>学科</th>
				<th width = '20%'>课程属性</th>
				<th width = '40%'>已有讲义数</th>
			</tr>
			<?php foreach($lectureList as $key=>$lecture):?>
				<tr height=30>
					<td><?php echo $lecture['sname'];?></td>
					<td><?php echo $lecture['gname'];?></td>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $lecture['totalnum'];?>讲 &nbsp;&nbsp;&nbsp;&nbsp; 
					</td>
				</tr>
			<?php endforeach;?>
		</table>-->
	<?php if(!empty($_POST)):?>
		<?php if(count($lectureList) > 0):?>
			<table width="85%" border="1" id="courseJiangYiTable">
				<tr bgcolor="#dddddd" height=35>
					<th width = '10%'>学科</th>
					<th width = '10%'>课程属性</th>
					<th width = '10%'>讲义属性</th>
					<th width = '40%'>共（<?php echo count($lectureList);?>）讲</th>
				</tr>
				<?php foreach($lectureList as $key=>$lecture): ?>
				<tr height=30>
					<td><?php echo $lecture['sname'];?></td>
					<td><?php echo $lecture['gname'];?></td>
					<td><?php echo $lecture['kname'];?></td>
					<td>
						<!--<a  href="<?php echo U('Vip/VipWorks/previewLecture',array('lecture_id'=>$lesson['lecture_id']))?>" target="_blank" class="blue"><?php echo $lecture['title'];?></a>-->
						<?php echo $lecture['title'];?>
					</td>
				</tr>
				<?php endforeach;?>
			</table>
		<?php else:?>
			<p>暂无标准化讲义</p>
		<?php endif;?>
	<?php else:?>
		<p><font color="red">请先选择统计条件</font></p>
	<?php endif;?>
	</div>	
</div>
</div>
</body>
</html>