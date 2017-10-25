<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<h2>上传明细</h2>
	<div id="search">
	<form id="search_form" name="search_form" method="POST" action="<?php echo U('Vip/VipData/uploadStatistic');?>">
		讲义类型：<select id="type" name="type" onchange="get_option(this.value,'subject','<?php echo U('Vip/VipHandouts/get_subject_option',array('ntype'=>0))?>','list')">
					<option value="">全部</option>
					<?php foreach($handoutsType as $key=>$htype):?>
						<option value="<?php echo $key+1;?>" <?php if($type-1==$key):?>selected<?php endif;?> ><?php echo $htype;?></option>
					<?php endforeach?>
					<option value="3" <?php if($type==3):?>selected<?php endif;?> >共享课程</option>
				 </select>
			     <select id="subject" name="subject" onchange="get_option(this.value,'grade','<?php echo U('Vip/VipHandouts/get_grades_option')?>','list')">
					<option value="">请选择科目</option>
					<?php foreach($subjectArr as $key=>$subject):?>
						<option value="<?php echo $subject['sid'];?>" <?php if($handouts_subject==$subject['sid']):?>selected<?php endif;?> ><?php echo $subject['name'];?></option>
					<?php endforeach?>
				 </select>
	  			 <select id="grade" name="grade">
					 <option value="">请选择课程属性</option>
					 <?php foreach($gradeArr as $key=>$grade):?>
						<option value="<?php echo $grade['gid'];?>" <?php if($handouts_grade && $handouts_grade==$grade['gid']):?>selected<?php endif;?>><?php echo $grade['name'];?></option>
					 <?php endforeach?>
				 </select>
	 上传教师身份：<select id="is_teaching_and_research" name="is_teaching_and_research">
					<option value="">全部</option>
					<option value="1" <?php if($is_teaching_and_research==1):?>selected<?php endif;?>>普通教师</option>
					<option value="2" <?php if($is_teaching_and_research==2):?>selected<?php endif;?>>教研教师</option>
				 </select>
	   教师登录名：<input type="text" id="username" name="username" value="<?php echo $username;?>" placeholder="输入教师登录名" >&nbsp;&nbsp;<div class="h_10"></div>
	     查询时间：<input type="text"  class="Wdate" id="starttime" name="starttime" value="<?php echo $startTime;?>" onClick="WdatePicker()"> 至 <input type="text"  class="Wdate" id="endtime" name="endtime" value="<?php echo $endTime;?>"  onClick="WdatePicker()">&nbsp;&nbsp;<input type="submit" value="确认查看">
		&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo U('Vip/VipData/export_uploadOrDownloadList',array('dotype'=>'upload','type'=>$type,'is_teaching_and_research'=>$is_teaching_and_research,'username'=>$username,'starttime'=>$startTime,'endtime'=>$endTime,'sid'=>$handouts_subject,'gid'=>$handouts_grade))?>" class="blue">导出Excle表</a>
	</form>
	</div>
	<hr>
	<div id="list" class="clearfix">
		<?php if($uploadList):?>
		<table width="70%" border="1">
			<tr bgcolor="#dddddd" height=35>
				<th>教师姓名</th>
				<th>登录名</th>
				<th>上传数量</th>
			</tr>
			<?php foreach($uploadList as $key=>$upload):?>
			<tr height=30>
				<td><a href="#" onclick="testMessageBox_uploadOrDownloadList(event,'<?php echo $upload['user_key'];?>','<?php echo U('Vip/VipData/get_uploadOrDownloadList')?>','<?php echo $upload['user_realname'];?>','<?php echo $type;?>','<?php echo $startTime?>','<?php echo $endTime?>','upload','<?php echo $is_teaching_and_research;?>','<?php echo $handouts_subject;?>','<?php echo $handouts_grade;?>');"><?php echo $upload['user_realname'];?></a></td>
				<td><?php echo $upload['user_name'];?></td>
				<td><?php echo $upload['uploadnum'];?></td>
			</tr>
			<?php endforeach?>
			<tr height=30>
				<td colspan="2">本页上传总计：</td>
				<td><?php echo $page_total_num;?></td>
			</tr>
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