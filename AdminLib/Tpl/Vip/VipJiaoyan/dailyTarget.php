<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
<style type="text/css">
	.clearfix table tr td{
		text-align: center;
	}
</style>
</head>
<body>
<div region="center">
<div id="main">
	<div class="tableTab">
		<ul class="tab">
			<li >
				<a href="<?php echo U('Vip/VipJiaoyan/dailyManage')?>">教研员日报</a>
			</li>
			<li class="current">
				<a href="#none">教研目标</a>
			</li>
		</ul>
	</div>

	<div id="calendar">
	<form id="documentManageForm" name="documentManageForm" method="GET" action="<?php echo U('Vip/VipJiaoyan/dailyTarget')?>">
		查询时间：<input type="text"  class="Wdate" id="date" name="date" value="<?php  echo $dateTime; ?>" onClick="WdatePicker({ dateFmt:'yyyy-MM'})">
		<input type="text" id="user_realname" name="user_realname" value="<?php echo $user_realname; ?>" placeholder="教研员姓名" onfocus="javascript:$(this).val('');"> <input type="submit" value="搜索">
		<input type="button" value="添加编辑目标" onclick="javascript:add_target();">
	</form>
	
	</div>
	<hr>

	<!--Begin 添加/编辑对话框-->
	<div id="basic-index-target-dlg" class="easyui-dialog" data-options="modal:true,closed:true,cache:false" style="width:970px;height:580px;padding:5px;">
	    <iframe scrolling="auto" id="basic-index-target-dlg-iframe" frameborder="0" src="" style="width:100%;height:100%;"></iframe>
	</div>

	<div id="list" class="clearfix">
		<?php if($userList):?>
		<table width="100%" border="1">
			<tr bgcolor="#dddddd" height=30>
				<td width="10%">姓名</td>
				<td>授课科目</td>
				<td width="5%">月份</td>
				<td>本月说导视频</td>
				<td>本月说导目标</td>
				<td>本月说导完成率</td>
			</tr>
			<?php foreach($userList as $key=>$handouts):?>
			<tr height=30>
				<td><?php echo $handouts['user_realname']?></td>
				<td><?php echo $handouts['subjectAccredit'];?></td>
				<td><?php echo $handouts['date'];?></td>
				<td><?php echo $handouts['num'];?></td>
				<td><?php echo $handouts['target'];?></td>
				<td><?php echo $handouts['rate'];?></td>
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
<script type="text/javascript">

/**
 * 添加教研目标
 * @param {[type]} ev         [description]
 * @param {[type]} requestUrl [description]
 */
function add_target()
{
	var params = {
			title: '添加'
	};
	$("#basic-index-target-dlg-iframe")[0].src = "/Vip/VipJiaoyan/dailyTarget_add";
	$("#basic-index-target-dlg").dialog(params).dialog('open');
}
</script>