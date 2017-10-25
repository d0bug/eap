<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.uploadify-3.1.min.js"></script>
<link href="/static/css/uploadify.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/static/js/vipschool.js"></script>
<link href="/static/css/vipschool.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<h2>焦点图管理</h2>
	<form id="focus_form" method="POST" action="" onsubmit="return check_focus_form()">
	<input type="hidden" id="autocut" name="autocut" value="0">
	<input type="hidden" id="new_width" name="new_width" value="">
	<input type="hidden" id="new_height" name="new_height" value="">
	<input type="hidden" id="num" name="num" value="<?php echo $focusCount?>">
	<input type="hidden" id="upload_url" name="upload_url" value="<?php echo U('Vipschool/VipschoolPage/uploadFile')?>">
	<input type="hidden" id="delete_url" name="delete_url" value="<?php echo U('Vipschool/VipschoolPage/deleteObject')?>">
	<input type="hidden" id="img_width" name="img_width" value="100%">
	<div id="focus_list" class="tableInfo">
	<?php if(!empty($focusList)):?>
		<?php foreach ($focusList as $key=>$focus):?>
			<div id="divfocus<?php echo $key+1;?>" class="focus">
			    焦点图<?php echo $key+1;?>、<br>
				<div style="padding:10px 30px;">
					<div >
						图片：
						<div style="padding-left:40px">
							<span id="upload_focus_<?php echo $key+1;?>" class="upload"></span>
							 <span id="view_focus_<?php echo $key+1;?>" class="view_file">
							 <?php if(!empty($focus['url'])):?>
							 	<img src="<?php echo $focus['show_url']?>" width="750" height="235"> <a href="#" onclick="del_file('<?php echo $focus['url']?>','#view_focus_<?php echo $key+1;?>','#focus_<?php echo $key+1;?>','#upload_focus_<?php echo $key+1;?>_msg','<?php echo U('Vipschool/VipschoolPage/deleteObject')?>')">删除图片</a>
							 <?php endif;?>
							 </span>
							 <input type="hidden" id="focus_<?php echo $key+1;?>" name="focus[]" value="<?php echo $focus['url']?>">
							 <div id="upload_focus_<?php echo $key+1;?>_msg"></div>
						</div>
					</div><br>
					<div>
						链接：<input type="text" id="link_<?php echo $key+1;?>" name="link[]" value="<?php echo $focus['link']?>" size="100">
					</div><br>
					<div>
						背景颜色值：<input type="text" id="bg_color_<?php echo $key+1;?>" name="bg_color[]" value="<?php echo $focus['bg_color']?>" size="30">
					</div>
				</div>
				<div id="focus_msg_<?php echo $key+1;?>"></div>
				<div class="delete"><a href="#" onclick="delete_focus(<?php echo $key+1;?>)">删除</a></div>
				<input type="hidden" id="fid" name="fid[]" value="<?php echo $focus['id']?>"> 
			</div>
		<?php endforeach?>
	<?php else:?>
		<div id="divfocus1" class="focus">
		    焦点图1、<br>
			<div style="padding:10px 30px;">
				<div >
					图片：
					<div style="padding-left:40px">
						<span id="upload_focus_1" class="upload"></span>
						 <span id="view_focus_1" class="view_file"></span>
						 <input type="hidden" id="focus_1" name="focus[]" value="">
						 <div id="upload_focus_1_msg"></div>
					</div>
				</div><br>
				<div>
					链接：<input type="text" id="link_1" name="link[]" value="" size="100">
				</div><br>
				<div>
					背景颜色值：<input type="text" id="bg_color_1" name="bg_color[]" value="" size="30">
				</div>
			</div>
			<div id="focus_msg_1"></div>
			<div class="delete"><a href="#" onclick="delete_focus(1)">删除</a></div>
			<input type="hidden" id="fid" name="fid[]" value=""> 
		</div>
		<div id="divfocus2" class="focus">
		    焦点图2、<br>
			<div style="padding:10px 30px;">
				<div >
					图片：
					<div style="padding-left:40px">
						<span id="upload_focus_2" class="upload"></span>
						 <span id="view_focus_2" class="view_file"></span>
						 <input type="hidden" id="focus_2" name="focus[]" value="">
						 <div id="upload_focus_2_msg"></div>
					</div>
				</div><br>
				<div>
					链接：<input type="text" id="link_2" name="link[]" value="" size="100">
				</div><br>
				<div>
					背景颜色值：<input type="text" id="bg_color_2" name="bg_color[]" value="" size="30">
				</div>
			</div>
			<div id="focus_msg_2"></div>
			<div class="delete"><a href="#" onclick="delete_focus(2)">删除</a></div>
			<input type="hidden" id="fid" name="fid[]" value=""> 
		</div>
		<div id="divfocus3" class="focus">
		    焦点图3、<br>
			<div style="padding:10px 30px;">
				<div>
					图片：
					<div style="padding-left:40px">
						<span id="upload_focus_3" class="upload"></span>
						 <span id="view_focus_3" class="view_file"></span>
						 <input type="hidden" id="focus_3" name="focus[]" value="">
						 <div id="upload_focus_3_msg"></div>
					</div>
				</div><br>
				<div>
					链接：<input type="text" id="link_3" name="link[]" value="" size="100">
				</div><br>
				<div>
					背景颜色值：<input type="text" id="bg_color_3" name="bg_color[]" value="" size="30">
				</div>
			</div>
			<div id="focus_msg_3"></div>
			<div class="delete"><a href="#" onclick="delete_focus(3)">删除</a></div>
			<input type="hidden" id="fid" name="fid[]" value="">  
		</div>
		<?php endif;?>
	</div>
	<input type="button" value="添加焦点图" onclick="add_focus()"><br><br>
	<input type="submit" value="保存" class="btn">
	</form>
</div>
</div>
</body>
</html>