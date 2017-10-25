<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<div id="search">
	<form id="publicityReviewForm" name="publicityReviewForm" method="POST" action="<?php echo U('Vip/VipReview/publicityReview')?>">
		<input type="text" id="user_name" name="user_name" value="<?php if($user_name):?><?php echo $user_name;?><?php endif;?>" placeholder="请输入教师登录名" size="30" onfocus="javascript:$(this).val('')"> 
		<input type="submit" value="搜索" >
	</form>
	</div>
	<hr>
	<div id="list" class="clearfix">
		<?php if($publicityList):?>
		<table width="100%" border="1">
			<tr bgcolor="#dddddd" height=30>
				<td>登录名</td>
				<td>教师姓名</td>
				<td>类别</td>
				<td>修改时间</td>
				<td>审核状态</td>
				<td>操作</td>
			</tr>
			<?php foreach($publicityList as $key=>$publicity):?>
			<tr height=30>
				<td><?php echo end(explode('-',$publicity['user_key']));?></td>
				<td><?php echo $publicity['teacher_name'];?></td>
				<td><?php echo (reset(explode('-',$publicity['user_key']))=='Employee')?'全职教师':'VIP社会兼职教师';?></td>
				<td><?php echo (!empty($publicity['last_updtime']))?date('Y-m-d H:i:s',$publicity['last_updtime']):'';?></td>
				<td><label class="error">待审</label></td>
				<td><a href="<?php echo U('Vip/VipReview/do_publicityReview',array('user_key'=>$publicity['user_key']))?>">审核</a></td>
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