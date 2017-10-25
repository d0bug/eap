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
	<form id="videoManageForm" name="videoManageForm" method="POST" action="<?php echo U('Vip/VipVideo/videoManage')?>">
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
	<hr>
	<div id="list" class="clearfix">
		<?php if($videoList):?>
		<table width="100%" border="1">
			<tr bgcolor="#dddddd" height=30>
				<td width="50">ID</td>
				<td width="120">视频属性</td>
				<td width="350">视频名称</td>
				<td width="110">视频类型</td>
				<td width="110">视频版本</td>
				<td width="120">上传人</td>
				<td width="150">上传时间</td>
				<td width="80">审核状态</td>
				<td>操作</td>
			</tr>
			<?php foreach($videoList as $key=>$video):?>
			<tr height=30>
				<td><?php echo $video['vid'];?></td>
				<td>
					<a href="#none" onclick="testMessageBox_viewVideo(event,'<?php echo $video['vid']?>','<?php echo $video['title']?>','<?php echo $video['attribute_one_name']?>','<?php echo $video['attribute_two_name']?>','<?php echo $video['duration']?>','<?php echo $video['introduce']?>','<?php echo $video['type']?>','<?php echo $video['user_name']?>','<?php echo $video['instime2']?>')">
						<?php echo $video['attribute_one_name'];?>
					</a>
				</td>
				<td>
					<a href="#none" onclick="testMessageBox_viewVideo(event,'<?php echo $video['vid']?>','<?php echo $video['title']?>','<?php echo $video['attribute_one_name']?>','<?php echo $video['attribute_two_name']?>','<?php echo $video['duration']?>','<?php echo $video['introduce']?>','<?php echo $video['type']?>','<?php echo $video['user_name']?>','<?php echo $video['instime2']?>')">
						<?php echo $video['title'];?>
					</a>
				</td>
				<td><?php echo $video['attribute_two_name'];?></td>
				<td><?php echo $video['type'];?></td>
				<td><?php echo $video['user_name'];?></td>
				<td><?php echo $video['instime2'];?></td>
				<td>
					<?php if($video['status']==1):?>
						<font color=green>已通过</font>
					<?php elseif ($video['status'] == 2):?>
						<font color=orange>未通过</font>
					<?php else:?>
						<font color=red>待审核</font>
					<?php endif;?>
				</td>
				<td>
					<a href="<?php echo U('Vip/VipVideo/updateVideo',array('vid'=>$video['vid']))?>">修改</a>&nbsp;&nbsp;&nbsp;&nbsp;
				<?php if($permInfo['permValue']==3):?>
					<a href="<?php echo U('Vip/VipVideo/deleteVideo',array('vid'=>$video['vid']))?>" onclick="javascript:return confirm('确定要删除此视频吗？')">删除</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<?php if($video['status']==0):?>
						<a href="#" onclick="testMessageBox_reviewVideo(event,'<?php echo $video['vid']?>','<?php echo $video['title']?>','<?php echo $video['attribute_one_name']?>','<?php echo $video['attribute_two_name']?>','<?php echo $video['duration']?>','<?php echo $video['introduce']?>','<?php echo $video['type']?>','<?php echo $video['user_name']?>','<?php echo $video['instime2']?>','<?php echo U('Vip/VipVideo/reviewVideo')?>')">审核</a>
					<?php endif;?>
				<?php endif;?>
				</td>
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
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.uploadify-3.1.min.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/video.js"></script>
</html>