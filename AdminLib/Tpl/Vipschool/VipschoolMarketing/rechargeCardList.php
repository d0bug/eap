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
	<h2>充值卡管理<span style="float:right;margin-right:100px" ><a href="<?php echo U('Vipschool/VipschoolMarketing/addRechargeCard')?>" class="f_14 blue">生成充值卡</a></span></h2>
	<div id="search">
		<form id="searchForm" method="POST" action="">
		充值卡号：<input type="text" id="card_code" name="card_code" value="<?php echo urldecode($card_code);?>" size="25" placeholder="请输入充值卡号">&nbsp;&nbsp;&nbsp;&nbsp;
		使用情况：<select id="type" name="type">
					<option value="">全部</option>
					<option value="1" <?php if($type==1):?>selected<?php endif;?>>已使用</option>
					<option value="2" <?php if($type==2):?>selected<?php endif;?>>未使用</option>
				</select>&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="submit" value="搜索" class="btn2">&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="<?php echo U('Vipschool/VipschoolMarketing/export_rechargeCardList',array('card_code'=>$card_code,'type'=>$type))?>" class="blue">导出Excel表</a>
		</form>
	</div>
	<hr>
	<div id="list" class="clearfix">
		<?php if($rechargeCardList):?>
		<table width="100%">
			<tr bgcolor="#dddddd" height=35>
				<td>&nbsp;<!--<input type="checkbox" id="checkAll" name="checkAll" value="">--> 编号</td>
				<td>卡号</td>
				<td>密码</td>
				<td>充值金额</td>
				<td>失效时间</td>
				<td>生成时间</td>
				<td>使用时间</td>
				<td>使用者</td>
				<td>操作</td>
			</tr>
			<?php foreach($rechargeCardList as $key=>$rechargeCard):?>
			<tr height=30>
				<td>&nbsp;<!--<input type="checkbox" id="checkAll" name="checkAll" value="<?php echo $rechargeCard['id'];?>">--> <?php echo $rechargeCard['id'];?></td>
				<td><?php echo $rechargeCard['card_code'];?></td>
				<td><?php echo $rechargeCard['card_pwd'];?></td>
				<td><?php echo $rechargeCard['money'];?>元</td>
				<td><?php echo $rechargeCard['endtime'];?></td>
				<td><?php echo $rechargeCard['instime'];?></td>
				<td><?php echo $rechargeCard['use_time'];?></td>
				<td><?php echo $rechargeCard['username'];?></td>
				<td><a href="#" onclick="copyToClipBoard('<?php echo $rechargeCard['card_code'];?>')" class="blue">复制</a></td>
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