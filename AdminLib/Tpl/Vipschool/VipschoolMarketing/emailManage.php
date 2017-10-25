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
<script type="text/javascript" src="/static/js/vipschool.js"></script>
<link href="/static/css/vipschool.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<h2>通知邮件管理<span style="float:right;margin-right:100px" ><a href="#" onclick="testMessageBox_addEmail(event,'<?php echo U('Vipschool/VipschoolMarketing/addEmail')?>')" class="f_14 blue">添加通知邮件</a></span></h2>
	<div id="search">
		
	</div>
	<hr>
	<div id="list" class="clearfix">
		<?php if($emailList):?>
		<table width="100%">
			<tr bgcolor="#dddddd" height=35>
				<td>添加时间</td>
				<td>邮件地址</td>
				<td>操作</td>
			</tr>
			<?php foreach($emailList as $key=>$email):?>
			<tr height=30>
				<td><?php echo $email['instime'];?></td>
				<td><?php echo $email['email'];?></td>
				<td>
					<a href="<?php echo U('Vipschool/VipschoolMarketing/deleteEmail',array('eid'=>$email['eid']))?>"  class="blue">删除</a>
				</td>
			</tr>
			<?php endforeach?>
		</table>
		<div id="pageStr">&nbsp;&nbsp;&nbsp;<?php echo $showPage;?></div>
		<?php else:?>
		<div>暂无相关信息</div>
		<?php endif;?>
	</div>
</div>
</div>
</body>
</html>