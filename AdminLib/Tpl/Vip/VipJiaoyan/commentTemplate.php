<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
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
			<li >
				<a href="<?php echo U('Vip/VipJiaoyan/wordsManage')?>" >课堂评价（客观）</a>
			</li>
			<li class="current">
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
					<input type="radio" id="sid<?php echo $subject['sid'];?>" name="sid" value="<?php echo $subject['sid'];?>" onclick="return show_template(this.value,'<?php echo U('Vip/VipJiaoyan/getTemplateList')?>')"><?php echo $subject['name'];?>&nbsp;&nbsp;&nbsp;&nbsp;
					<?php endforeach;?>
				<?php endif;?>
			</td>
		</tr>
		
		<tr valign="top">
			<td class="alt">模板： </td>
			<td >
				<div>
					<font color=orange>请按照以下规则进行填写，在需要调用教师叮咛的地方填写指定格式的文本。<br>学员姓名请用“XXX”代替，评价内容用“[评价:维度名称]”来代替，如：[评价:注意力度]、[评价:笔记习惯]、[评价:做题规范]、[评价:师生互动]。</font>
				</div><br>
				<div style="width:300px;" >
					<input type="button" value="添加" onclick="return testMessageBox_add_commentTemplate(event,'<?php echo U('Vip/VipJiaoyan/add_commentTemplate')?>')" class="btn">
				</div><br>
				<div id="templateHtml">
					
				</div>
			</td>
		</tr>
	</table>
</div>
</div>
</body>
</html>