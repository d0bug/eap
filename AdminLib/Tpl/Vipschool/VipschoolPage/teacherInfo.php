<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<link href="/static/css/vipschool.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div>
	<?php if(!empty($teacherInfo)):?>
	<table width="80%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr>
			<td class="alt">教师姓名：</td>
			<td><?php echo $teacherInfo['realname']?></td>
		</tr>
		<tr>
			<td class="alt">教师图片：</td>
			<td>
				<img src="<?php echo $teacherInfo['show_img']?>" >
			</td>
		</tr>
		<tr>
			<td class="alt">主讲年级：</td>
			<td ><?php echo $teacherInfo['grade']?></td>
	    </tr>
	    <tr>
			<td class="alt">主讲学科：</td>
			<td ><?php echo $teacherInfo['subject']?></td>
	    </tr>
	    <tr>
			<td class="alt">教师寄语：</td>
			<td ><?php echo $teacherInfo['send_word']?></td>
	    </tr>
	    <tr>
			<td class="alt">教&nbsp;&nbsp;&nbsp;&nbsp;龄：</td>
			<td ><?php echo $teacherInfo['of_educate_age']?>年</td>
	    </tr>
	    <tr>
			<td class="alt">教师简介：</td>
			<td ><?php echo $teacherInfo['intro_content']?></td>
	    </tr>
	    <tr>
			<td class="alt">授课风格：</td>
			<td ><?php echo $teacherInfo['teaching_style']?></td>
	    </tr>
	    <tr>
			<td class="alt">教学心得：</td>
			<td ><?php echo $teacherInfo['experience_content']?></td>
	    </tr>
	    <tr>
			<td class="alt">家长评价：</td>
			<td ><?php echo $teacherInfo['comment']?></td>
	    </tr>
	    <tr>
			<td class="alt">是否推荐：</td>
			<td ><?php echo ($teacherInfo['is_recommend'] == 1)?'已推荐':'未推荐';?></td>
	    </tr>
	     <tr>
			<td class="alt">是否离职：</td>
			<td ><?php echo ($teacherInfo['is_onjob'] == 1)?'在职':'已离职';?></td>
	    </tr>
	    <tr>
			<td class="alt">添加时间：</td>
			<td ><?php echo $teacherInfo['instime']?></td>
	    </tr>
	</table>
	<?php else:?>
		暂无相关信息！
	<?php endif;?>
</div>
</div>
</body>
</html>