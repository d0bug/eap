<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/static/js/DatePicker/WdatePicker.js"></script>
<style type="text/css">
.opacity{margin-top:-30px;height:20px; filter:alpha(Opacity=10);-moz-opacity:0.5;opacity: 0.5;z-index:100; background-color:#000000;color:#ffffff;text-align:right;}
</style>
</head>
<body>
<div region="center">
<div id="main">
	<div id="search">
	<form id="form" name="form" method="get" action="<?php echo U('Vip/VipAchevement/lesson')?>">
		学生姓名：<input type="text" id="student_name" name="student_name" value="<?php if($student_name):?><?php echo urldecode($student_name);?><?php endif;?>" placeholder="学生姓名..." onfocus="javascript:$(this).val('');" size="30"> 
		&nbsp;&nbsp;
		学员状态：<select name="status">
			<option value="">请选择学员状态</option>
			<?php foreach($statusList as $key=>$value):?>
				<option value="<?php echo $key;?>" <?php if($status==$key):?>selected<?php endif;?> ><?php echo $value;?></option>
			<?php endforeach?>
		</select>
		&nbsp;&nbsp; 
		上课日期：<input type="text"  class="Wdate" id="begin_time" name="begin_time" placeholder="开始日期" value="<?php echo $begin_time;?>" onClick="WdatePicker()">
		至 <input type="text"  class="Wdate" id="end_time" name="end_time" placeholder="截止日期" value="<?php echo $end_time;?>" onClick="WdatePicker()">
		&nbsp;
		<input type="submit" value="搜索" >
		&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo U('Vip/VipAchevement/export',array('student_name'=>$student_name,'begin_time'=>$begin_time,'end_time'=>$end_time))?>" class="blue">导出Excle表</a>
	</form>
	</div>
	<div id="list" class="clearfix">
		<?php if($myStudentList):?>
		<table width="100%" border="1">
			<tr bgcolor="#dddddd" height=30>
				<td>学员姓名</td>
				<td>高思学号</td>
				<td>学员状态</td>
				<td>查询时间内累计课时</td>
				<td width="20%">距离结束日期累计课时</td>
				<td width="20%">本月累计课时</td>
				<td width="20%">总累计课时</td>
			</tr>
			<?php foreach($myStudentList as $key=>$myStudent):?>
			<tr height=30>
				<td><a href="<?php echo U('Vip/VipStudents/newStudentProgram2',array('student_code'=>$myStudent['sstudentcode']));?>" class="blue" target="_blank"><?php echo $myStudent['sstudentname'];?></a></td>
				<td><?php echo $myStudent['saliascode'];?></td>
				<td>
				<?php if($myStudent['nstudentproperty'] ==1):?>
					正常
				<?php elseif($myStudent['nstudentproperty'] ==2): ?>
					非正常
				<?php elseif($myStudent['nstudentproperty'] ==3): ?>
					已结课
				<?php else: ?>
					已退费
				<?php endif; ?>
				</td>
				<td><?php echo $myStudent['dhours'];?></td>
				<td><?php echo $myStudent['dendsumhours']; ?></td>
				<td><?php echo $myStudent['dmonthsumhours'];?></td>
				<td><?php echo $myStudent['dsumhours'];?></td>
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