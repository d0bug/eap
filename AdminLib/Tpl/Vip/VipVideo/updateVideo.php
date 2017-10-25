
<!DOCTYPE HTML>

<html lang="zh-cn">

<head>

<meta charset="utf-8" />

<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>

<?php include TPL_INCLUDE_PATH . '/easyui.php'?>

<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>

<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>

<script type="text/javascript" src="/static/js/jquery.validate.js"></script>

<script type="text/javascript" src="/static/js/jquery.uploadify-3.1.min.js"></script>

<script type="text/javascript" src="/static/js/use_uploadify.js"></script>

<script type="text/javascript" src="/static/js/video.js"></script>

<link href="/static/css/uploadify.css" type="text/css" rel="stylesheet" />

<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />

<script type="text/javascript">

$(function() {
	
	$("#add_video").validate({
		
		rules: {
			
			video_url: {
				
				required: true
			
			},
			
			title: {
				
				required: true
			
			},
			
			attribute_one: {
				
				required: true
			
			},
			
			attribute_two: {
				
				required: true,
			
			},
			
			duration:{
				
				required: true,
			
			},
			
			introduce: {
				
				required: true,
				
				maxlength: 2500
			
			}
		
		},
		
		messages: {
			
			video_url: {
				
				required: '请上传视频'
			
			},
			
			title: {
				
				required: '请填写视频名称'
			
			},
			
			attribute_one: {
				
				required: '请选择视频属性'
			
			},
			
			attribute_two: {
				
				required: '请选择视频类别'
			
			},
			
			duration:{
				
				required: '请填写视频时长'
			
			},
			
			introduce: {
				
				required: '请填写视频介绍',
				
				maxlength: '视频介绍不超过2500字'
			
			}
		
		}
	
	});
	

})


</script>

</head>

<body >

<div region="center" >
	
	<div id="main">
		
		<h2><?php if($hid):?>编辑视频信息<?php else:?>视频上传<?php endif;?></h2>
		
		<form id="add_video" name="add_video" method="POST" enctype="multipart/form-data"  action="<?php echo U('Vip/VipVideo/updateVideo',array('vid'=>$vid));?>">
		
			<input type="hidden" id="upload_url" name="upload_url" value="<?php echo U('Vip/VipVideo/upload_file')?>">
		
			<input type="hidden" id="del_url" name="del_url" value="<?php echo U('Vip/VipVideo/del_file')?>">
		
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
			
				<tr>
				
					<td class="alt" valign="top"><font color="red">*</font>选择上传视频： </td>
				
					<td valign="top">
					
						<span id="upload_video"></span><label id="upload_video_msg" class="success"></label><br>
					
						<span id="view_video" class="view_file">
					
						<?php if(!empty($videoInfo['video_url'])):?>
						
							<a href="#none"><?php echo $videoInfo['video_url']?></a>&nbsp;&nbsp;
							<div onclick="del_url('<?php echo $videoInfo['video_url']?>','#view_video','#video_url','<?php echo U('Vip/VipVideo/del_file')?>')">删除</div>
					
						<?php endif;?>
					
						</span>
					
						<input type="hidden" id="video_url" name="video_url" value="<?php echo $videoInfo['video_url']?>">
					
						<div class="t_right">&nbsp;</div>
				
					</td>
			
				</tr>
			
				<tr>
				
					<td class="alt"><font color="red">*</font>视频名称： </td>
				
					<td>
					
						<input type="text" id="title" name="title" placeholder="请输入视频名称..." value="<?php echo $videoInfo['title']?>" size="100"  onkeydown="return check_length('title','titleMsg',100)" onkeyup="return check_length('title','titleMsg',100)">
						<span id="titleMsg">还可输入100个字</span>
				
					</td>
			
				</tr>
			
				<tr>
				
					<td class="alt"><font color="red">*</font>视频属性： </td>
				
					<td>
					
						<select name="attribute_one" id="attribute_one" onchange="get_attributeTwoList(this.value,'<?php echo U('Vip/VipVideo/getAttributeList')?>')">
						
							<option value="">请选择视频属性</option>
					
							<?php if(!empty($attributeOneList)):?>
						
								<?php foreach ($attributeOneList as $key=>$attribute):?>
						
									<option value="<?php echo $attribute['aid']?>" <?php if($videoInfo['attribute_one'] == $attribute['aid']):?>selected<?php endif;?> ><?php echo $attribute['name']?></option>
						
								<?php endforeach;?>
					
							<?php endif;?>
					
						</select>
					
						<label id="attribute_one_msg" class="error"></label>
				
					</td>
			
				</tr>
			
				<tr>
				
					<td class="alt"><font color="red">*</font>视频类别： </td>
				
					<td>
					
						<select name="attribute_two" id="attribute_two" onchange="">
						
							<option value="">请选择视频类别</option>
					
							<?php if(!empty($attributeTwoList)):?>
						
								<?php foreach ($attributeTwoList as $key=>$attribute):?>
						
									<option value="<?php echo $attribute['aid']?>" <?php if($videoInfo['attribute_two'] == $attribute['aid']):?>selected<?php endif;?> ><?php echo $attribute['name']?></option>
						
								<?php endforeach;?>
					
							<?php endif;?>
					
						</select>
					
						<label id="attribute_two_msg" class="error"></label>
				
					</td>
			
				</tr>
			
				<tr>
				
					<td class="alt"><font color="red">*</font>视频时长： </td>
				
					<td>
					
						<input type="text" id="duration" name="duration" placeholder="请输入视频时长..." value="<?php echo $videoInfo['duration']?>" size="10"  >
				
					</td>
			
				</tr>
			
				<tr>
				
					<td class="alt" valign="top">视频缩略图： </td>
				
					<td  valign="top">
					
						<span id="upload_video_img"></span>
					
						<span id="view_video_img">
					
							<?php if(!empty($videoInfo['video_img'])):?>
						
								<a href="#none"><?php echo $videoInfo['video_img']?></a>&nbsp;&nbsp;<a href="javascript:void(0)" onclick="del_url('<?php echo $videoInfo['video_img']?>','#view_video_img','#video_img','<?php echo U('Vip/VipVideo/del_file')?>')">删除</a>
					
							<?php endif;?>
					
						</span>
					
						<input type="hidden" name="video_img" id="video_img" value="<?php echo $videoInfo['video_img']?>">
				
					</td>
			
				</tr>
			
				<tr>
				
					<td class="alt"><font color="red">*</font>视频介绍:</td>
				
					<td>
					
						<textarea id="introduce" name="introduce" cols="70" rows="5" placeholder="请输入视频介绍..."><?php echo $videoInfo['introduce']?></textarea>(2500字以内)
					
						<label id="introduce_msg" class="error"></label>
				
					</td>
			
				</tr>
			
				<tr>
				
					<td class="alt">&nbsp;</td>
				
					<td>
				    
						<input type="hidden" name="action" value="insert"><input type="hidden" id="hid" name="hid" value="">
				    
						<input type="submit" class="btn" value="确认提交">
				
					</td>
			
				</tr>
	
		</div>

	</div>

</body>
</html>