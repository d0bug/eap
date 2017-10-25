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
		选择试卷：<select id="paper_id" name="paper_id" style="width:130px;">
				<option value="">请选择试卷</option>
				<?php foreach($paperList as $key=>$paper):?>
				<option value="<?php echo $paper['id']?>" <?php if($paper['id']==$_REQUEST['paper_id']):?>selected<?php endif;?>><?php echo $paper['title']?></option>
				<?php endforeach?>
			</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="submit" value="搜索">&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="<?php echo U('Viptest/ViptestUser/exportExcel',array('type'=>1,'paper_id'=>$_REQUEST['paper_id']))?>" class="blue">导出Excel</a>
		</form>
	</div>
	<hr>
	<div id="list" class="clearfix">
	<?php if($_REQUEST['paper_id']):?>
		<?php if($resultList):?>
		<table width="70%" border="1">
			<tr bgcolor="#dddddd" height=35>
				<th>序号</th>
				<th>学生姓名</th>
				<th>试卷名称</th>
				<th>正确率</th>
				<th>评级</th>
				<th>排名</th>
				<th>记录时间</th>
				<th>操作</th>
			</tr>
			<?php foreach($resultList as $key=>$result):?>
			<tr height=30>
				<td align="center"><?php echo $result['id']?></td>
				<td align="center"><?php echo $result['uname'];?></td>
				<td align="center"><?php echo $result['title'];?></td>
				<td align="center"><?php echo sprintf('%.2f',($result['accuracy_count']/$result['question_num'])*100);?>%</td>
				<td align="center"><?php echo $result['level'];?></td>
				<td align="center"><?php echo $result['rank'];?></td>
				<td align="center"><?php echo $result['instime'];?></td>
				<td align="center">
					<a href="http://www.gaosivip.com/IQtest/index.php/Test/view/id/<?php echo $result['paper_id']?>/uid/<?php echo $result['uid']?>.html" class="blue" target="_blank">查看</a>
					<!-- | <a href="<?php echo U('Viptest/ViptestUser/reportInfo',array('result_id'=>$result['id']))?>" class="blue" target="_parent">查看2</a>-->
				</td>
			</tr>
			<?php endforeach?>
		</table>
		<div id="pageStr"><?php echo $showPage;?></div>
		<?php else:?>
		<div>暂无相关信息</div>
		<?php endif;?>
	<?php else:?>
		请先选择试卷。。。。
	<?php endif;?>
	</div>
</div>
</div>
</body>
</html>