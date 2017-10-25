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
	<?php if($myStudentList):?><input type="button" value="重置排序" class="btn" onclick="javascript:location.href='<?php echo U('Vip/VipStudents/index');?>'"><?php endif;?>
	<hr>
	<div id="list" class="clearfix">
		<?php if($myStudentList):?>
		<table width="100%" border="1">
			<tr bgcolor="#dddddd" height=30>
				<td>学员姓名</td>
			<?php if($order=='asc'):?>
				<td><a href="<?php echo U('Vip/VipStudents/index',array('key_name'=>'nGrade','order'=>'desc'))?>">年级<?php if($key_name=='nGrade'):?><img src="/static/images/asc.png" align="absmiddle"><?php elseif($key_name!='nGrade'):?><img src="/static/images/sort.png" align="absmiddle"><?php endif;?></a></td>
				<td><a href="<?php echo U('Vip/VipStudents/index',array('key_name'=>'sClassAdviserCode','order'=>'desc'))?>">班主任<?php if($key_name=='sClassAdviserCode'):?><img src="/static/images/asc.png" align="absmiddle"><?php elseif($key_name!='sClassAdviserCode'):?><img src="/static/images/sort.png" align="absmiddle"><?php endif;?></a></td>
			<?php else:?>
				<td><a href="<?php echo U('Vip/VipStudents/index',array('key_name'=>'nGrade','order'=>'asc'))?>">年级<?php if($key_name=='nGrade'):?><img src="/static/images/desc.png" align="absmiddle"><?php elseif($key_name!='nGrade'):?><img src="/static/images/sort.png" align="absmiddle"><?php endif;?></a></td>
				<td><a href="<?php echo U('Vip/VipStudents/index',array('key_name'=>'sClassAdviserCode','order'=>'asc'))?>">班主任<?php if($key_name=='sClassAdviserCode'):?><img src="/static/images/desc.png" align="absmiddle"><?php elseif($key_name!='sClassAdviserCode'):?><img src="/static/images/sort.png" align="absmiddle"><?php endif;?></a></td>
			<?php endif;?>
				<td>最新课次主题</td>
				<td>课次/课次总数</td>
				<td>排课时间</td>
				<td width="20%">核录课时</td>
				<td width="20%">课时状态</td>
			</tr>
			<?php foreach($myStudentList as $key=>$myStudent):?>
			<tr height=30>
				<td><a href="<?php echo U('Vip/VipStudents/studentInfo',array('student_code'=>$myStudent['sstudentcode'],'kecheng_code'=>$myStudent['skechengcode'],'lesson'=>$myStudent['nlessonno']));?>" class="blue" target="_blank"><?php echo $myStudent['sstudentname'];?></a></td>
				<td><?php echo $myStudent['gradename'];?></td>
				<td><?php echo $myStudent['sclassadvisername'];?></td>
				<td><?php echo $myStudent['lesson_topic'];?></td>
				<td><?php echo $myStudent['nlessonno'];?>/<?php echo $myStudent['nobegin_count'];?></td>
				<td><?php echo $myStudent['dtdatereal'].'&nbsp;&nbsp;&nbsp;'.$myStudent['dtlessonbeginreal'].'-'.$myStudent['dtlessonendreal'];?></td>
				<td width="50">
					<?php if($myStudent['overdue']==0 || $myStudent['comment']!=''):?>
						<?php if($myStudent['lesson_topic']!=''):?>
							<a href="#none" class="success" onclick="testMessageBox_handouts_helu(event,'<?php echo U('Vip/VipStudents/keChengHeLu',array('act'=>'update','helu_id'=>$myStudent['heluid'],'student_code'=>$myStudent['sstudentcode'],'kecheng_code'=>$myStudent['skechengcode'],'lesson_no'=>$myStudent['nlessonno'],'lesson_date'=>$myStudent['dtdatereal'],'lesson_begin'=>$myStudent['dtlessonbeginreal'],'lesson_end'=>$myStudent['dtlessonendreal']));?>');">修改</a>
						<?php else:?>
							<a href="#none" class="orange" onclick="testMessageBox_handouts_helu(event,'<?php echo U('Vip/VipStudents/keChengHeLu',array('act'=>'add','helu_id'=>$myStudent['heluid'],'student_code'=>$myStudent['sstudentcode'],'student_name'=>$myStudent['sstudentname'],'kecheng_code'=>$myStudent['skechengcode'],'lesson_no'=>$myStudent['nlessonno'],'lesson_date'=>$myStudent['dtdatereal'],'lesson_begin'=>$myStudent['dtlessonbeginreal'],'lesson_end'=>$myStudent['dtlessonendreal']));?>');">核录</a>
						<?php endif;?>
					<?php else:?>
						<span style="color:red;cursor:pointer" onclick="<?php if(empty($myStudent['is_overdue'])):?>do_overdue('<?php echo U('Vip/VipWorks/doOverdue',array('helu_id'=>$myStudent['heluid']))?>')<?php else:?>javascript:alert('超过48小时的课次不能核录');<?php endif;?>">逾期未核录</span>
					<?php endif;?>
				</td>
				<td>
					<?php if($myStudent['naudit']==0):?>
						<?php if($myStudent['nstatus']==2):?>
							已考勤
						<?php elseif($myStudent['nstatus']==3):?>
							已缺勤
						<?php else:?>
							未考勤
						<?php endif;?>
					<?php elseif($myStudent['naudit']==1):?>
						已审核
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