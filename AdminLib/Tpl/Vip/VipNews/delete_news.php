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
	<form id="newsSearch" method="GET" action="<?php echo U('Vip/VipNews/delete_news')?>">
	<input type="text" id="keyword" name="keyword" value="<?php echo urldecode($keyword);?>" placeholder="输入标题关键词..." size="30">&nbsp;&nbsp;<input type="submit" value=" 搜索 ">
	</form>
	</div>
	<?php if($newsList):?>
		<form id="deleteFrom" name="deleteFrom" method="POST" action="<?php echo U('Vip/VipNews/delete_news',array('p'=>$curPage,'keyword'=>urldecode($keyword)))?>">
			<table width="80%" border="1">
				<tr height="40" bgcolor="#dddddd">
					<td align="center">操作</td>
					<td>标题</td>
					<td>类型</td>
					<td>发布人</td>
					<td>发布时间</td>
				</tr>
				<?php foreach($newsList as $key=>$new):?>
				<tr height="30">
					<td align="center"><input type="checkbox" name="is_delete[]" id="is_delete_<?php echo $key;?>" value="<?php echo $new['nid'];?>"></td>
					<td><a href="<?php echo U('Vip/VipNews/index',array('nid'=>$new['nid']))?>" title="<?php echo $new['title'];?>"><?php echo $new['title'];?></a></td>
					<td><?php echo $new['ntype'];?></td>
					<td><?php echo $new['user_name'];?></td>
					<td><?php echo $new['instime'];?></td>
				</tr>
				<?php endforeach?>
				<tr height="30" bgcolor="#cccccc">
					<td colspan="5">
						<div id="pageStr"><input type="checkbox" name="checkAll" id="checkAll" value="1" >全选&nbsp;&nbsp;&nbsp;<?php if($permInfo['permValue']==3):?><input type="submit" name="delete" value="删除选中资讯" class="btn"><?php endif;?>　　　　<?php echo $showPage;?></div>
					</td>
				</tr>
			</table>
		</form>
	<?php else:?>
		<div>暂无相关资讯信息</div>
	<?php endif;?>
</div>
</div>
</body>
</html>