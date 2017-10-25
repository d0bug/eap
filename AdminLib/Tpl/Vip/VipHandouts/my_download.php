<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<div id="search">
	<form id="search_form" name="search_form" method="GET" action="<?php echo U('Vip/VipHandouts/my_download');?>">
		<select name="type" id="type" >
			<option value="" >请选择文档类型</option>
			<?php foreach($handoutsType as $key=>$type):?>
				<option value="<?php echo $key;?>" <?php if($htype == $key && $htype !== ''):?>selected<?php endif;?>><?php echo $type;?></option>
			<?php endforeach?>
		</select>
		<input type="text" id="keyword" name="keyword" value="<?php if($keyword):?><?php echo $keyword;?><?php endif;?>" placeholder="输入标题关键词">&nbsp;&nbsp;<input type="submit" value="  搜索  ">
	</form>
	</div>
	<div id="list" class="clearfix">
		<h2>我的下载历史</h2>
		<?php if($myDownloadList):?>
		<table width="65%">
			<tr bgcolor="#dddddd" height=35>
				<td>&nbsp;类型</td>
				<td>标题</td>
				<td>下载时间</td>
				<td>操作</td>
			</tr>
			<?php foreach($myDownloadList as $key=>$myDownload):?>
			<tr height=30>
				<td>&nbsp;<?php echo ($myDownload['type'] == 1)?'试题库':'课程讲义';?></td>
				<td><a href="#" onclick="testMessageBox_handouts_detail(event,<?php echo $myDownload['hid']?>,'<?php echo U('Vip/VipHandouts/show_detail_handouts')?>',1);" title="<?php echo $myDownload['title'];?>"><?php echo $myDownload['title'];?></a></td>
				<td><?php echo $myDownload['download_time'];?></td>
				<td><a href="<?php echo U('Vip/VipHandouts/download',array('hid'=>$myDownload['hid'],'type'=>$myDownload['type']))?>" class="orange">下载</a></td>
			</tr>
			<?php endforeach?>
		</table>
		<?php else:?>
		<div>暂无相关信息</div>
		<?php endif;?>
	</div>
	<div id="pageStr"><?php echo $showPage;?></div>
</div>
</div>
</body>
</html>