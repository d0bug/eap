<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<script src="/static/kindeditor/kindeditor-min.js" type="text/javascript"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/kindeditor/themes/default/default.css" rel="stylesheet">
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
	function check_opercircle()	{
		var title = $("#title").val();
		if(title == ''){
			alert('标题不能为空');
			return false;
		}
	}
</script>
</head>
<body >
<div region="center" >
<div id="main">
	<h2>圈子操作</h2>
	<form id="CircleOperate" name="CircleOperate" method="POST"  action="<?php echo U('Vip/VipCircle/DoCircleOperate');?>">

	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr>
			<td class="alt"><font color="red">*</font>标题： </td>
			<td>
				<input type="text" id="title" name="title" placeholder="请输入圈子标题..." value="<?php echo $CircleInfo['title'];?>" size="200"  onkeydown="return check_length('title','titleMsg',200)" onkeyup="return check_length('title','titleMsg',200)"><label id="title_msg" class="error"></label><span id="titleMsg">还可输入200个字</span>
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>介绍： </td>
				<td><textarea name="content" id="content" style="width:60%;height:400px" placeholder="输入圈子介绍..."><?php echo stripslashes($CircleInfo['intro']);?></textarea><span class="error"></span>
		        </td>
		</tr>

		<tr>
			<td class="alt"><font color="red">*</font>是否置顶： </td>
			<td>
				<input type="radio" id="is_top" name="is_top" value="1" <?php if($CircleInfo['is_top'] == 1):?>checked<?php endif;?>>是&nbsp;&nbsp;
				<input type="radio" id="is_top" name="is_top" value="0" <?php if($CircleInfo['is_top'] == 0):?>checked<?php endif;?>>否
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>是否推荐： </td>
			<td>
			<?php
				$chek1 =$chek2 =  '' ;
				if($CircleInfo['is_recommend'] == 1){
					$chek1= 'checked';
					$chek2 = '';
				}
				if($CircleInfo['is_recommend'] == 0){
					$chek2= 'checked';
					$chek1 = '';
				}

			?>

				<input type="radio" id="is_recommend" name="is_recommend" value="1" <?php echo $chek1;?>>是&nbsp;&nbsp;
				<input type="radio" id="is_recommend" name="is_recommend" value="0" <?php echo $chek2;?>>否
			</td>
		</tr>		
		<tr>
			<td class="alt"><font color="red">*</font>是否前台显示： </td>
			<td>
			<?php
				$chk1 =$chk2 =  '' ;
				if($CircleInfo['status'] == 1){
					$chk1= 'checked';
					$chk2 = '';
				}
				if($CircleInfo['status'] == 0){
					$chk2= 'checked';
					$chk1 = '';
				}

			?>
				<input type="radio" id="is_status" name="is_status" value="1" <?php echo $chk1;?>>是&nbsp;&nbsp;
				<input type="radio" id="is_status" name="is_status" value="0" <?php echo $chk2;?>>否
			</td>
		</tr>
		<tr>
			<td class="alt">&nbsp;</td>
			<td><input type="hidden" id="id" name="id" value="<?php echo $CircleInfo['id']?>">
			    <input type="submit" class="btn" onclick="return check_opercircle()" value="确认提交"></td>
		</tr>
	</table>
	</form>
	<br><br><br><br>
</div>
</div>
</body>
</html>