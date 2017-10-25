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
				<li class="current">
					<a href="/vip/vip_data/jiaoyanUploadStatistic">本周更新</a>
				</li>
				<li >
					<a href="/vip/vip_data/jiaoyanHistoryUploadStatistic">历史统计</a>
				</li>
			</ul>
		</div><br/>
		<form id="search_form" name="search_form" method="POST" action="<?php echo U('Vip/VipData/jiaoyanUploadStatistic');?>">
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
						<option value="<?php echo $grade['gid'];?>" <?php if($handouts_grade && $handouts_grade==$grade['gid']):?>selected<?php endif;?>><?php echo $grade['name'];?></option>
					 <?php endforeach?>
		</select>&nbsp;&nbsp;
		<select id="knowledge" name="knowledge"  >
			<option value="">请选择讲义属性</option>
		<?php if($type!=3):?>
			<?php if($handouts_knowledge){?>
				<option value="<?php $handouts_knowledge;?>" selected='selected' ><?php echo $knowledgeName;?></option>
			<?php }?>
		<?php endif;?>
		</select>&nbsp;&nbsp;
		查询周期：<input type="text"  class="Wdate" id="searchPerion" name="searchPerion" value="<?php echo $handouts_searchPerion;?>" onClick="WdatePicker()"> &nbsp;&nbsp;&nbsp;
		<input type="submit" value="确认查看">
		<a href="<?php echo U('Vip/VipData/export_benzhouData',array('dotype'=>'download','type'=>$type,'searchPerion'=>$handouts_searchPerion,'subject'=>$handouts_subject,'grade'=>$handouts_grade,'knowledge'=>$handouts_knowledge))?>" class="blue">导出Excle表</a>
		</form>
	</div>
	<div>
		<h3>课程讲义</h3>
	<?php if(count($jiangyiArray) > 0){?>
		<table width="85%" border="1" id="courseJiangYiTable">
			<tr bgcolor="#dddddd" height=35>
				<th width = '10%'>学科</th>
				<th width = '10%'>课程属性</th>
				<th width = '10%'>讲义属性</th>
				<th width = '30%'>课程用途</th>
				<th width = '40%'>本周更新（<?php echo count($jiangyiArray);?>讲）</th>
			</tr>
			<?php foreach($jiangyiArray as $key=>$jiangyi){ ?>
			<tr height=30>
				<td><?php echo $jiangyi['sname'];?></td>
				<td><?php echo $jiangyi['gname'];?></td>
				<td><?php echo $jiangyi['kname'];?></td>
				<td><?php echo $jiangyi['courseuser'];?></td>
				<td>
					<a style="color:blue;" href="javascript:void(0);" onclick="testMessageBox_handouts_preview(event,'<?php echo U('Vip/VipHandouts/view_handouts_pdf',array('hid'=>$jiangyi['hid'],'type'=>'teacher'))?>');"><?php echo $jiangyi['title'];?></a>
				</td>
			</tr>
			<?php }?>
		</table>
	<?php }else{?>	
		<p>暂无最新上传到的讲义</p>
	<?php }?>
		<h3>试题库</h3>
	<?php if(count($shitiArray) > 0){?>	
		<table width="85%" border="1" id="shiTiKuTable">
			<tr bgcolor="#dddddd" height=35>
				<th width = '20%'>学科</th>
				<th width = '20%'>题库属性</th>
				<th width = '20%'>试题属性</th>
				<th width = '40%'>本周更新（<?php echo count($shitiArray);?>套）</th>
			</tr>
			<?php foreach($shitiArray as $key=>$shiti){?>
				<tr height=30>
					<td><?php echo $shiti['sname'];?></td>
					<td><?php echo $shiti['gname'];?></td>
					<td><?php echo $shiti['kname'];?></td>
					<td>
						<a style="color:blue;" onclick="testMessageBox_handouts_preview(event,'<?php echo U('Vip/VipHandouts/view_handouts_pdf',array('hid'=>$shiti['hid'],'type'=>'teacher'))?>');" href="javascript:void(0);"><?php echo $shiti['title'];?></a>
					</td>
				</tr>
			<?php }?>
		</table>
	<?php }else{?>	
		<p>暂无最新上传到的试题库</p>
	<?php }?>
	</div>
</div>
</div>
</body>
</html>