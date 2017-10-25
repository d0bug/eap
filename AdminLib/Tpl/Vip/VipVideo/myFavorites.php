<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<div id="search">
	<form id="videoListForm" name="videoListForm" method="POST" action="<?php echo U('Vip/VipVideo/myFavorites')?>">
		<select id="attribute_one" name="attribute_one" onchange="get_attributeTwoList(this.value,'<?php echo U('Vip/VipVideo/getAttributeList')?>')" >
			<option value="">请选择视频属性</option>
		<?php if(!empty($attributeOneList)):?>
			<?php foreach ($attributeOneList as $key=>$attribute):?>
			<option value="<?php echo $attribute['aid']?>" <?php if($attribute_one == $attribute['aid']):?>selected<?php endif;?> ><?php echo $attribute['name']?></option>
			<?php endforeach;?>
		<?php endif;?>
		</select>
		<select id="attribute_two" name="attribute_two" >
			<option value="">请选择视频类别</option>
		<?php if(!empty($attributeTwoList)):?>
			<?php foreach ($attributeTwoList as $key=>$attribute):?>
			<option value="<?php echo $attribute['aid']?>" <?php if($attribute_two == $attribute['aid']):?>selected<?php endif;?> ><?php echo $attribute['name']?></option>
			<?php endforeach;?>
		<?php endif;?>
		</select>
		<input type="text" id="keyword" name="keyword" value="<?php if($keyword):?><?php echo urldecode($keyword);?><?php endif;?>" placeholder="输入视频名称关键词..." onfocus="javascript:$(this).val('');" size="30">
		<input type="submit" value="搜索" >
	</form>
	</div>
	<div id="list" class="clearfix">
		<h2>我的收藏</h2>
		<?php if($favoriteList):?>
				<table width="80%" border="1">
					<tr bgcolor="#dddddd" height=35>
						<td >视频属性</td>
						<td width="50%">视频名称</td>
						<td >视频分类</td>
						<td>视频时长</td>
						<td>收藏时间</td>
						<td>操作</td>
					</tr>
					<?php foreach($favoriteList as $key=>$video):?>
					<tr height=30>
						<td><?php echo $video['attribute_one_name'];?></td>
						<td>
							<a href="#" onclick="testMessageBox_viewVideo(event,'<?php echo $video['vid']?>','<?php echo $video['title']?>','<?php echo $video['attribute_one_name']?>','<?php echo $video['attribute_two_name']?>','<?php echo $video['duration']?>','<?php echo $video['introduce']?>','<?php echo $video['type']?>','<?php echo $video['user_name']?>','<?php echo $video['instime2']?>')" title="<?php echo $video['title'];?>">
								<?php echo $video['title'];?>
							</a>
						</td>
						<td><?php echo $video['attribute_two_name'];?></td>
						<td><?php echo $video['duration'];?></td>
						<td><?php echo $video['instime1'];?></td>
						<td>
							<a href="#none" onclick="testMessageBox_playVideo(event,'<?php echo $video['title']?>','<?php echo U('Vip/VipVideo/playVideo',array('vid'=>$video['vid']))?>')">播放</a> &nbsp;&nbsp;&nbsp;&nbsp;
							<a href="#none" onclick="testMessageBox_viewVideo(event,'<?php echo $video['vid']?>','<?php echo $video['title']?>','<?php echo $video['attribute_one_name']?>','<?php echo $video['attribute_two_name']?>','<?php echo $video['duration']?>','<?php echo $video['introduce']?>','<?php echo $video['type']?>','<?php echo $video['user_name']?>','<?php echo $video['instime2']?>')">查看</a> &nbsp;&nbsp;&nbsp;&nbsp;
							<a href="<?php echo U('Vip/VipVideo/doFavorite',array('act'=>'del','vid'=>$video['vid']));?>">取消收藏</a>
						</td>
					</tr>
					<?php endforeach?>
				</table>
		<?php else:?>
		<div>暂无相关讲义信息</div>
		<?php endif;?>
	</div>
	<div id="pageStr"><?php echo $showPage;?></div>
</div>
</div>
</body>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/video.js"></script>
</html>