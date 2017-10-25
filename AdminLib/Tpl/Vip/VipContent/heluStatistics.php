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
	<form id="heluLogForm" name="heluLogForm" method="POST" action="<?php echo U('Vip/VipContent/heluStatistics')?>">
		学生姓名：<input type="text" id="student_name" name="student_name" value="<?php echo urldecode($student_name);?>" placeholder="学生姓名">&nbsp;&nbsp;
		上课日期：<input type="text"  class="Wdate" id="lesson_date_start" name="lesson_date_start" value="<?php echo $lesson_date_start;?>" placeholder="开始日期" onClick="WdatePicker()">
		至&nbsp;<input type="text"  class="Wdate" id="lesson_date_end" name="lesson_date_end" value="<?php echo $lesson_date_end;?>" placeholder="截止日期" onClick="WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(lesson_date_start)}'});"> &nbsp;
		是否勾选给家长发短信：<select name='is_select_sendsms' id='is_select_sendsms'>
							<?php 
								foreach($is_select_sendsms_array as $key=>$value){
							?>
									<option <?php if($key == $is_select_sendsms){ ?> selected='selected' <?php }?> value="<?php echo $key;?>"><?php echo $value;?></option>
							<?php
								}
							?>
						</select>&nbsp;&nbsp;
		教师姓名：<input type="text" id="teacher_name" name="teacher_name" value="<?php echo urldecode($teacher_name);?>" placeholder="教师姓名">&nbsp;&nbsp;
		<input type="submit" value="查询">&nbsp;&nbsp;
		<?php if(!empty($student_name) || !empty($lesson_date_start) ||!empty($lesson_date_end) ||!empty($is_select_sendsms) ||!empty($teacher_name)):?>
		<a href="<?php echo U('Vip/VipContent/exportHeluLog',array('student_name'=>urldecode($student_name),'lesson_date_start'=>$lesson_date_start,'lesson_date_end'=>$lesson_date_end,'is_select_sendsms'=>$is_select_sendsms,'teacher_name'=>urldecode($teacher_name)));?>" class="blue">导出excel</a>
		<?php else:?>
		<font color="Red">抱歉，由于数据量很大，所以只允许将数据进行筛选后导出excel</font>
		<?php endif;?>
	</form>
	</div>
	<hr>
	<div id="list" class="clearfix">
		<?php if($heluLogList):?>
		<table width="100%" border="1">
			<tr bgcolor="#dddddd" height=30>
				<td width="80">学员姓名</td>
				<td width="100">上课日期</td>
				<td width="80">教师姓名</td>
				<td width="80">核录时间</td>
				<td width="100">是否勾选给家长发短信</td>
				<td width="100">是否触发短信</td>
				<td width="100">是否上传讲义</td>
				<td width="80">家长手机号</td>
				<td width="80">上课主题</td>
				<td width="200">课堂评价</td>
				<td width="80">核录课时状态</td>
			</tr>
			<?php foreach($heluLogList as $key=>$log):?>
			<tr height=30>
				<td><?php echo $log['student_name'];?></td>
				<td><?php echo $log['lesson_date'];?></td>
				<td><?php echo $log['teacher_name'];?></td>
				<td><?php echo $log['helu_time'];?></td>
				<td><?php echo ($log['is_select_sendsms'] == 1)?'<font color=red>是</font>':'否';?></td>
				<td><?php echo ($log['is_trigger_sendsms'] == 1)?'<font color=red>是</font>':'否';?></td>
				<td><?php echo ($log['is_upload_handouts'] == 1)?'<font color=red>是</font>':'否';?></td>
				<td><?php echo $log['to_mobile'];?></td>
				<td><?php echo $log['lesson_topic'];?></td>
				<td><?php echo $log['comment'];?></td>
				<td><?php echo ($log['helu_type'] == 1)?'<font color=red>核录</font>':'修改';?></td>
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