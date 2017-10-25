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
	<h2>教研上传统计</h2>
	<div id="search">
		<div class="tableTab">
			<ul class="tab">
				<li>
					<a href="/vip/vip_data/jiaoyanUploadStatistic">本周更新</a>
				</li>
				<li class="current">
					<a href="/vip/vip_data/jiaoyanHistoryUploadStatistic">历史统计</a>
				</li>
			</ul>
		</div><br/>
		<form id="search_form" name="search_form" method="POST" action="<?php echo U('Vip/VipData/jiaoyanHistoryUploadStatistic');?>">
		讲义类型：<select id="type" name="type" onchange="get_option(this.value,'subject','<?php echo U('Vip/VipHandouts/get_subject_option',array('ntype'=>0))?>','list')">
			<option value="">全部</option>
			<?php foreach($handoutsType as $key=>$htype):?>
				<option value="<?php echo $key+1;?>" <?php if($type-1==$key):?>selected<?php endif;?> ><?php echo $htype;?></option>
			<?php endforeach?>
		</select>
		<select id="subject" name="subject" onchange="get_option(this.value,'grade','<?php echo U('Vip/VipHandouts/get_grades_option',array('ntype'=>0))?>','list')">
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
		查询时间：<input type="text"  class="Wdate" id="startTime" name="startTime" value="<?php echo $startTime;?>" onClick="WdatePicker()">至
		 <input type="text"  class="Wdate" id="endTime" name="endTime" value="<?php echo $endTime;?>"  onClick="WdatePicker()">&nbsp;&nbsp;&nbsp;<input type="submit" value="确认查看">
		<a href="<?php echo U('Vip/VipData/export_historyData',array('dotype'=>'download','type'=>$type,'startTime'=>$startTime,'endTime'=>$endTime,'subject'=>$handouts_subject,'grade'=>$handouts_grade))?>" class="blue">导出Excle表</a>
		</form>
	</div>
	<div>
		<table width="40%" border="1" style="float:left" id="courseJiangYi">
			<tr bgcolor="#dddddd" height=35>
				<th width = '20%'>学科</th>
				<th width = '20%'>讲义类型</th>
				<th width = '20%'>课程属性</th>
				<th width = '40%'>已有讲义数</th>
			</tr>
			<?php foreach($jiangyiHistory as $key=>$jiangyi){?>
				<tr height=30>
					<td><?php echo $jiangyi['sname'];?></td>
					<td><?php echo $jiangyi['typename'];?></td>
					<td><?php echo $jiangyi['gname'];?></td>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $jiangyi['totalnum'];?>讲 &nbsp;&nbsp;&nbsp;&nbsp; 
						<a style="color:blue;display:block;float:right;margin-right:30px" href="/vip/vip_handouts/main/style/img?canBack=1&subject=<?php echo $jiangyi['sid'];?>&grade=<?php echo $jiangyi['gid'];?>&knowledge=&nid=&starttime=<?php echo $startTime;?>&endtime=<?php echo $endTime;?>&keyword=">查看全部</a>
					</td>
				</tr>
			<?php }?>
		</table>
		
		<table width="40%" border="1" style="float:left;margin-left:30px;"  id="shiTiKu">
			<tr bgcolor="#dddddd" height=35>
				<th width = '20%'>学科</th>
				<th width = '20%'>讲义类型</th>
				<th width = '20%'>题库属性</th>
				<th width = '40%'>已有试题数</th>
			</tr>
			<?php foreach($shitiArray as $key=>$shiti){?>
				<tr height=30>
					<td><?php echo $shiti['sname'];?></td>
					<td><?php echo $shiti['typename'];?></td>
					<td><?php echo $shiti['gname'];?></td>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $shiti['totalnum'];?>讲 &nbsp;&nbsp;&nbsp;&nbsp;
						<a style="color:blue;display:block;float:right;margin-right:30px" href="/vip/vip_handouts/test_paper/style/img?canBack=1&subject=<?php echo $shiti['sid'];?>&grade=<?php echo $shiti['gid'];?>&knowledge=&nid=&starttime=<?php echo $startTime;?>&endtime=<?php echo $endTime;?>&keyword=">查看全部</a>
					</td>
				</tr>
			<?php }?>
		</table>
	</div>
</div>
</div>
</body>
</html>