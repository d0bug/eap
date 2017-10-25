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
	<h2>成绩管理</h2>
	<div id="search">
		<form method="POST" action="">
		姓名：<input type="text" name="name" id="name" value="<?php echo $name;?>">&nbsp;&nbsp;&nbsp;手机号码：<input type="text" name="mobile" id="mobile" value="<?php echo $mobile;?>">&nbsp;&nbsp;
			<input type="submit" value="搜索">&nbsp;&nbsp;&nbsp;&nbsp;
		</form>
	</div>	
	<hr>
	<div id="list" class="clearfix">
	
		<?php if($user):?>
		<table width="70%" border="1">
			<tr bgcolor="#dddddd" height=35>
				<th>序号</th>
				<th>学生姓名</th>
				<th>手机号</th>
				<th>做对</th>
				 <?php if(!empty($moduleList)):?>
				 	<?php 
				 	foreach($moduleList as $k=>$r):?>
				 		<th><?php echo $r['name'];?></th>
				 	<?php endforeach?>
				 <?php endif;?>	
				<th>排名</th>
				<th>查看详细</th>		
			</tr>
			<?php foreach($user as $key=>$result):?>
			<tr height=30>
				<td align="center"><?php echo $result['id']?></td>
				<td align="center"><?php echo $result['name'];?></td>
				<td align="center"><?php echo $result['phone'];?></td>
				<td align="center"><?php echo $result['my_correct'].'/'.$result['total']; echo '<br>'; $s = sprintf('%.1f',($result['my_correct']/$result['total'])*100).'%'; echo $s;?></td>
				<?php foreach($result['modulename'] as $kk=>$v):?>
					<td align="center">
					<?php 
						echo $v['moduleCorrectcount'].'/'.$v['modulecount'];
						echo '<br>';
						$r = sprintf('%.1f',($v['moduleCorrectcount']/$v['modulecount'])*100).'%'; 
						echo $r;
						echo '<br>';
					?>
					</td>
				<?php endforeach?>
				<td align="center"><?php echo $result['rank'];?></td>
				<td align="center"><a href="http://www.gaosivip.com/Eval/index.php/Test/view/uid/<?php echo $result['id']?>.html" class="blue" target="_blank">查看</a></td>
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