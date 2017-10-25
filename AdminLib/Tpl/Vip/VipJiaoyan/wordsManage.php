<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
<style type="text/css">
.tableInfo .alt { width: 60px;}
</style>
</head>
<body>
<div region="center">
<div id="main">
	<div class="tableTab">
		<ul class="tab">
			<li class="current">
				<a href="<?php echo U('Vip/VipJiaoyan/wordsManage')?>" >课堂评价（客观）</a>
			</li>
			<li >
				<a href="<?php echo U('Vip/VipJiaoyan/commentTemplate')?>">课堂评价（话术）</a>
			</li>
		</ul>
	</div>
	
	<br>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr valign="top">
			<td class="alt">科目： </td>
			<td>
				<?php if(!empty($subjectArr)):?>
					<?php foreach ($subjectArr as $key=>$subject):?>
					<input type="radio" id="sid<?php echo $subject['sid'];?>" name="sid" value="<?php echo $subject['sid'];?>" onclick="return change_dimesion(this.value,'<?php echo U('Vip/VipJiaoyan/getDimesionList')?>')" title="<?php echo $subject['name'];?>"><?php echo $subject['name'];?>&nbsp;&nbsp;&nbsp;&nbsp;
					<?php endforeach;?>
				<?php endif;?>
			</td>
		</tr>
		<tr valign="top">
			<td class="alt">维度： </td>
			<td>
				<div id="dimesionHtml" class="selBar">
				<?php if(!empty($dimensionArr)):?>
					<?php foreach ($dimensionArr as $key=>$dimension):?>
					<input type="radio" id="dimension_id<?php echo $dimension['dimension_id'];?>" name="dimension_id" value="<?php echo $dimension['dimension_id'];?>" onclick="clear_level()" title="<?php echo $dimension['dimension_name'];?>"><?php echo $dimension['dimension_name'];?>&nbsp;&nbsp;&nbsp;&nbsp;
					<?php endforeach;?>
				<?php else:?>
					<font color=red>请先选择科目</font>
				<?php endif;?>
				</div>
				<div class="btnBar">
					<a href="#" onclick="return testMessageBox_addCommentType(event,'dimension','<?php echo U('Vip/VipJiaoyan/add_CommentType')?>')"><img src="/static/images/add.png"></a>&nbsp;&nbsp;
					<a href="#" onclick="return testMessageBox_editCommentType(event,'dimension','<?php echo U('Vip/VipJiaoyan/edit_CommentType')?>')"><img src="/static/images/edit.png"></a>&nbsp;&nbsp;
					<a href="#" onclick="return testMessageBox_deleteCommentType(event,'dimension','<?php echo U('Vip/VipJiaoyan/delete_CommentType')?>')"><img src="/static/images/delete.png"></a>
				</div>
			</td>
		</tr>
		<tr valign="top">
			<td class="alt">级别： </td>
			<td>
				<div id="levelHtml" class="selBar">
				<?php if(!empty($levelArr)):?>
					<?php foreach ($levelArr as $key=>$level):?>
					<input type="radio" id="level_id<?php echo $level['id'];?>" name="level_id" value="<?php echo $level['id'];?>" onclick="return change_comment_text(this.value,'<?php echo U('Vip/VipJiaoyan/getCommentText')?>')" title="<?php echo $level['title'];?>" alt="<?php echo $level['rank'];?>"><?php echo $level['title'];?>&nbsp;&nbsp;&nbsp;&nbsp;
					<?php endforeach;?>
				<?php endif;?>
				</div>
				<div class="btnBar">
					<a href="#" onclick="return testMessageBox_addLevel(event,'<?php echo U('Vip/VipJiaoyan/addLevel')?>')"><img src="/static/images/add.png"></a>&nbsp;&nbsp;
					<a href="#" onclick="return testMessageBox_editCommentType(event,'level','<?php echo U('Vip/VipJiaoyan/edit_CommentType')?>')"><img src="/static/images/edit.png"></a>&nbsp;&nbsp;
					<a href="#" onclick="return testMessageBox_deleteCommentType(event,'level','<?php echo U('Vip/VipJiaoyan/delete_CommentType')?>')"><img src="/static/images/delete.png"></a>
				</div>
			</td>
		</tr>
		<tr valign="top">
			<td class="alt">评语： </td>
			<td >
				<div style="width:400px;line-height:40px;border-bottom:1px solid #dddddd;padding-right:10px" >
					<input type="checkbox" id="selectAll" name="selectAll" value="" >全选
					<span style="float:right">
						<input type="button" value="删除" onclick="delete_comment('<?php echo U('Vip/VipJiaoyan/delete_commentText')?>')" class="btn">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="button" value="添加" onclick="testMessageBox_add_comment(event,'<?php echo U('Vip/VipJiaoyan/add_commentText')?>')" class="btn">
					</span>
				</div><br>
				<div id="commentHtml">
					<?php if(!empty($commentArr)):?>
						<?php foreach ($commentArr as $key=>$comment):?>
						<p><input type="checkbox" id="comment_id" name="comment_id[]" value="<?php echo $comment['id']?>" ><?php echo $comment['text']?></p>
						<?php endforeach;?>
					<?php else:?>
						<font color=red>请先选择科目、维度、级别</font>
					<?php endif;?>
				</div>
			</td>
		</tr>
	</table>
</div>
</div>
</body>
<script type="text/javascript">
$(function () {
	$("#selectAll").click(function () {
		if(this.checked){
			$("input[name='comment_id[]']:checkbox").attr("checked", true);
		}else{
			$("input[name='comment_id[]']:checkbox").attr("checked", false);
		}
	});
});
</script>
</html>