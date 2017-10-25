<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
<style type="text/css">
.opacity{margin-top:-30px;height:20px; filter:alpha(Opacity=10);-moz-opacity:0.5;opacity: 0.5;z-index:100; background-color:#000000;color:#ffffff;text-align:right;}
</style>
</head>
<body>
<div region="center">
<div id="main">
	<div id="search">
	<form id="videoListForm" name="videoListForm" method="POST" action="<?php echo U('Vip/VipVideo/videoList')?>">
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
		<h2>视频列表&nbsp;&nbsp;&nbsp;&nbsp;
			<span>
				<a href="<?php echo U('Vip/VipVideo/videoList',array('style'=>'list','attribute_one'=>$attribute_one,'attribute_two'=>$attribute_two,'keyword'=>urldecode($handouts_keyword),'p'=>$curPage));?>" class="f_14 <?php if($list_style=='list'):?>bold<?php endif;?>">列表</a> | 
				<a href="<?php echo U('Vip/VipVideo/videoList',array('style'=>'img','attribute_one'=>$attribute_one,'attribute_two'=>$attribute_two,'keyword'=>urldecode($handouts_keyword),'p'=>$curPage));?>" class="f_14 <?php if($list_style=='img'):?>bold<?php endif;?>">图标</a>
			</span>
		</h2>
		<?php if($videoList):?>
			<?php if($list_style == 'img'):?>
				<ul>
					<?php foreach($videoList as $key=>$video):?>
					<li>
						<div class="pic"><a href="#" onclick="testMessageBox_viewVideo(event,'<?php echo $video['vid']?>','<?php echo $video['title']?>','<?php echo $video['attribute_one_name']?>','<?php echo $video['attribute_two_name']?>','<?php echo $video['duration']?>','<?php echo $video['introduce']?>','<?php echo $video['type']?>','<?php echo $video['user_name']?>','<?php echo $video['instime2']?>')" title="<?php echo $video['title'];?>"><img src="<?php if($video['video_img']):?><?php echo $video['video_img'];?><?php else:?>/static/images/default.gif<?php endif;?>" style="width:180px;border:0px;height:100px;"></a></div>
						<div class="opacity" ><?php echo $video['duration']?>&nbsp;&nbsp;</div>
						<div class="txt"><a href="#" onclick="testMessageBox_viewVideo(event,'<?php echo $video['vid']?>','<?php echo $video['title']?>','<?php echo $video['attribute_one_name']?>','<?php echo $video['attribute_two_name']?>','<?php echo $video['duration']?>','<?php echo $video['introduce']?>','<?php echo $video['type']?>','<?php echo $video['user_name']?>','<?php echo $video['instime2']?>')" title="<?php echo $video['title'];?>"><?php echo $video['title'];?></a></div>
						<div class="ac">
							<a href="#none" onclick="testMessageBox_playVideo(event,'<?php echo $video['title']?>','<?php echo U('Vip/VipVideo/playVideo',array('vid'=>$video['vid']))?>')">播放</a> &nbsp;&nbsp;
							<em> | </em> &nbsp;&nbsp;
							<a href="#none" onclick="testMessageBox_viewVideo(event,'<?php echo $video['vid']?>','<?php echo $video['title']?>','<?php echo $video['attribute_one_name']?>','<?php echo $video['attribute_two_name']?>','<?php echo $video['duration']?>','<?php echo $video['introduce']?>','<?php echo $video['type']?>','<?php echo $video['user_name']?>','<?php echo $video['instime2']?>')">查看</a> &nbsp;&nbsp;
							<em> | </em> &nbsp;&nbsp;
						<?php if($video['is_favorite']):?>
							<span class="gray">已收藏</span>
						<?php else:?>
							<a href="<?php echo U('Vip/VipVideo/doFavorite',array('act'=>'add','vid'=>$video['vid']));?>">收藏</a>
						<?php endif;?>
						</div>
					</li>
					<?php endforeach?>
				</ul>
			<?php else:?>
				<table width="80%" border="1">
					<tr bgcolor="#dddddd" height=35>
						<td width="50%">视频名称</td>
						<td>视频时长</td>
						<td>上传时间</td>
						<td>操作</td>
					</tr>
					<?php foreach($videoList as $key=>$video):?>
					<tr height=30>
						<td>
							<a href="#" onclick="testMessageBox_viewVideo(event,'<?php echo $video['vid']?>','<?php echo $video['title']?>','<?php echo $video['attribute_one_name']?>','<?php echo $video['attribute_two_name']?>','<?php echo $video['duration']?>','<?php echo $video['introduce']?>','<?php echo $video['type']?>','<?php echo $video['user_name']?>','<?php echo $video['instime2']?>')" title="<?php echo $video['title'];?>">
								<?php echo $video['title'];?>
							</a>
						</td>
						<td><?php echo $video['duration'];?></td>
						<td><?php echo $video['instime2'];?></td>
						<td>
							<a href="#none" onclick="testMessageBox_playVideo(event,'<?php echo $video['title']?>','<?php echo U('Vip/VipVideo/playVideo',array('vid'=>$video['vid']))?>')">播放</a> &nbsp;&nbsp;&nbsp;&nbsp;
							<a href="#none" onclick="testMessageBox_viewVideo(event,'<?php echo $video['vid']?>','<?php echo $video['title']?>','<?php echo $video['attribute_one_name']?>','<?php echo $video['attribute_two_name']?>','<?php echo $video['duration']?>','<?php echo $video['introduce']?>','<?php echo $video['type']?>','<?php echo $video['user_name']?>','<?php echo $video['instime2']?>')">查看</a> &nbsp;&nbsp;&nbsp;&nbsp;
						<?php if($video['is_favorite']):?>
							<span class="gray">已收藏</span>
						<?php else:?>
							<a href="<?php echo U('Vip/VipVideo/doFavorite',array('act'=>'add','vid'=>$video['vid']));?>">收藏</a>
						<?php endif;?>
						</td>
					</tr>
					<?php endforeach?>
				</table>
			<?php endif;?>
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