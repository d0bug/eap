<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<div id="search">
	<form id="search_form" name="search_form" method="GET" action="<?php echo U('Vip/VipInfo/my_favorite');?>">
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
		<h2>我的收藏</h2>
		<?php if($favoriteList):?>
		<table width="70%">
			<tr bgcolor="#dddddd" height=35>
				<td>&nbsp;类型</td>
				<td>标题</td>
				<td>收藏时间</td>
				<td>操作</td>
			</tr>
			<?php foreach($favoriteList as $key=>$favorite):?>
			<tr height=30>
				<td>&nbsp;<?php echo ($favorite['htype'] == 1)?'试题库':'课程讲义';?></td>
				<td><a href="#" onclick="testMessageBox_handouts_detail(event,<?php echo $favorite['hid']?>,'<?php echo U('Vip/VipHandouts/show_detail_handouts')?>',1);" title="<?php echo $favorite['title'];?>"><?php echo $favorite['title'];?></a></td>
				<td><?php echo $favorite['instime'];?></td>
				<td>
					<a href="<?php echo U('Vip/VipHandouts/do_favorite',array('act'=>'del','fid'=>$favorite['fid']))?>" class="orange">取消收藏</a>&nbsp;&nbsp;
					<a href="<?php echo U('Vip/VipHandouts/download',array('hid'=>$favorite['hid'],'type'=>$favorite['htype']))?>" class="orange">下载</a>&nbsp;&nbsp;
				</td>
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