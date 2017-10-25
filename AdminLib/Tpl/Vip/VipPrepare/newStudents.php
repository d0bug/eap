<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<div id="search">
	<form id="form" name="form" method="POST" action="<?php echo U('Vip/VipPrepare/newStudents')?>">
		下次课时间：<input type="text"  class="Wdate" id="start" name="start" value="<?php echo $start;?>" placeholder="开始日期" onClick="WdatePicker()">
		至&nbsp;<input type="text"  class="Wdate" id="end" name="end" value="<?php echo $end;?>" placeholder="截止日期" onClick="WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(start)}'});"> &nbsp;
		上课校区：<select id="dept_code" name="dept_code">
			<option value="">请选择上课校区</option>
			<?php foreach($deptList as $key=>$dept):?>
				<option value="<?php echo $dept['scode'];?>" <?php if($dept_code==$dept['scode']):?>selected<?php endif;?> ><?php echo $dept['sname'];?></option>
			<?php endforeach?>
		</select>
		&nbsp;&nbsp;
		学员姓名：<input type="text" id="student_name" name="student_name" value="<?php if($student_name):?><?php echo urldecode($student_name);?><?php endif;?>" placeholder="请输入学员姓名" onfocus="javascript:$(this).val('');">&nbsp;&nbsp;
		<input type="submit" value="搜索">
	</form>
	</div>
	<div id="list" class="clearfix">
		<?php if($myStudentList):?>
		<table width="100%" border="1">
			<tr bgcolor="#dddddd" height=30>
				
			<?php if($order=='asc'):?>
				<td><a href="<?php echo U('Vip/VipPrepare/newStudents',array('key_name'=>'sStudentName','order'=>'desc'))?>">学员姓名<?php if($key_name=='sStudentName'):?><img src="/static/images/asc.png" align="absmiddle"><?php elseif($key_name!='sStudentName'):?><img src="/static/images/sort.png" align="absmiddle"><?php endif;?></a></td>
				<td><a href="<?php echo U('Vip/VipPrepare/newStudents',array('key_name'=>'CurrentGrade','order'=>'desc'))?>">年级<?php if($key_name=='CurrentGrade'):?><img src="/static/images/asc.png" align="absmiddle"><?php elseif($key_name!='CurrentGrade'):?><img src="/static/images/sort.png" align="absmiddle"><?php endif;?></a></td>
				<td><a href="<?php echo U('Vip/VipPrepare/newStudents',array('key_name'=>'sClassAdviserCode','order'=>'desc'))?>">班主任<?php if($key_name=='sClassAdviserCode'):?><img src="/static/images/asc.png" align="absmiddle"><?php elseif($key_name!='sClassAdviserCode'):?><img src="/static/images/sort.png" align="absmiddle"><?php endif;?></a></td>
				<td><a href="<?php echo U('Vip/VipPrepare/newStudents',array('key_name'=>'deptname','order'=>'desc'))?>">校区<?php if($key_name=='deptname'):?><img src="/static/images/asc.png" align="absmiddle"><?php elseif($key_name!='deptname'):?><img src="/static/images/sort.png" align="absmiddle"><?php endif;?></a></td>
			<?php else:?>
				<td><a href="<?php echo U('Vip/VipPrepare/newStudents',array('key_name'=>'sStudentName','order'=>'asc'))?>">学员姓名<?php if($key_name=='sStudentName'):?><img src="/static/images/desc.png" align="absmiddle"><?php elseif($key_name!='sStudentName'):?><img src="/static/images/sort.png" align="absmiddle"><?php endif;?></a></td>
				<td><a href="<?php echo U('Vip/VipPrepare/newStudents',array('key_name'=>'CurrentGrade','order'=>'asc'))?>">年级<?php if($key_name=='CurrentGrade'):?><img src="/static/images/desc.png" align="absmiddle"><?php elseif($key_name!='CurrentGrade'):?><img src="/static/images/sort.png" align="absmiddle"><?php endif;?></a></td>
				<td><a href="<?php echo U('Vip/VipPrepare/newStudents',array('key_name'=>'sClassAdviserCode','order'=>'asc'))?>">班主任<?php if($key_name=='sClassAdviserCode'):?><img src="/static/images/desc.png" align="absmiddle"><?php elseif($key_name!='sClassAdviserCode'):?><img src="/static/images/sort.png" align="absmiddle"><?php endif;?></a></td>
				<td><a href="<?php echo U('Vip/VipPrepare/newStudents',array('key_name'=>'deptname','order'=>'asc'))?>">校区<?php if($key_name=='deptname'):?><img src="/static/images/desc.png" align="absmiddle"><?php elseif($key_name!='deptname'):?><img src="/static/images/sort.png" align="absmiddle"><?php endif;?></a></td>
			<?php endif;?>
				
				<td>已上/未上课次</td>
			<?php if($order=='asc'):?>
				<td><a href="<?php echo U('Vip/VipPrepare/newStudents',array('key_name'=>'next_lesson_begin','order'=>'desc'))?>">下次课时间<?php if($key_name=='next_lesson_begin'):?><img src="/static/images/asc.png" align="absmiddle"><?php elseif($key_name!='next_lesson_begin'):?><img src="/static/images/sort.png" align="absmiddle"><?php endif;?></a></td>
			<?php else:?>
				<td><a href="<?php echo U('Vip/VipPrepare/newStudents',array('key_name'=>'next_lesson_begin','order'=>'asc'))?>">下次课时间<?php if($key_name=='next_lesson_begin'):?><img src="/static/images/desc.png" align="absmiddle"><?php elseif($key_name!='next_lesson_begin'):?><img src="/static/images/sort.png" align="absmiddle"><?php endif;?></a></td>
			<?php endif;?>
				<td width="20%">课时状态</td>
			</tr>
			<?php foreach($myStudentList as $key=>$myStudent):?>
			<tr height=30>
				<td><a href="<?php echo U('Vip/VipStudents/newStudentLesson',array('student_code'=>$myStudent['sstudentcode']));?>" class="blue" target="_blank"><?php echo $myStudent['sstudentname'];?></a></td>
				<td><?php echo $myStudent['gradename'];?></td>
				<td><?php echo $myStudent['sclassadvisername'];?></td>
				<td><?php echo $myStudent['deptname'];?></td>
				<td><?php echo $myStudent['end_count'];?>/<?php echo $myStudent['nobegin_count'];?></td>
				<td><?php echo $myStudent['next_lesson_begin'];?></td>
				<td>
					<?php if(!empty($myStudent['next_lesson_begin'])):?>
						<?php if(!empty($myStudent['next_lesson_lecture_id'])):?>
							<?php if(!empty($myStudent['next_lesson_report'])):?>
								已上课
							<?php else:?>
								<font color="green">待上课</font>
							<?php endif;?>
						<?php else:?>
							<font color="red">未备课</font>
						<?php endif;?>
					<?php else:?>
						<?php if($myStudent['nobegin_count']==0):?>
							<span class="gray"></span>
						<?php endif;?>
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