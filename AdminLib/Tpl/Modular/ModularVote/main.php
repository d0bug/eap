<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/modular.js"></script>
<link href="/static/css/modular.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
	<div id="main">
		<h2>投票列表&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo U('Modular/ModularVote/add')?>" class="btn">添加新项目</a></h2>
		<table class="tableList" border="0" cellpadding="0" cellspacing="0"  width="90%" id="teacher_table">
			<tr>
				<th>序号</th>
				<th>投票项目</th>
				<th>开始日期</th>
				<th>结束日期</th>
				<th>投票人数</th>
				<th>数据统计</th>
			</tr>
			<?php $i=1; foreach($list as $k=>$v):?>
			<tr>
				<td><?php echo $i;?></td>
				<td><a href="<?php echo U('Modular/ModularVote/edit').'/id/'.$v['id'];?>"><?php echo $v['vote_name'];?></a></td>
				<td><?php echo date('Y年m月d日',$v['begintime']);?></td>
				<td><?php echo $v['endtime']==0 ? '不结束' :date('Y年m月d日',$v['endtime']);?></td>
				<td><?php echo 123;?></td>
				<td><a href="<?php echo U('Modular/ModularVote/show').'/voteid/'.$v['id'].'/display/1';?>">预览</a></td>
				
			</tr>
			<?php
			$i++;
			endforeach?>
		</table>
		<p><input type="button" class="btn" onclick="toggle('#teacher_table','#flex_btn_teacher')" id="flex_btn_teacher" value="收起"></p><br>
	</div>
</div>
</body>
</html>