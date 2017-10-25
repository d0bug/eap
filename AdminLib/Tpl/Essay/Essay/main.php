<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/essay.js"></script>
<link href="/static/css/essay.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
	<div id="main">
		<h2>班级作文列表</h2>
		<div id="search">
		<form id="search_form" name="search_form" method="GET" action="<?php echo U('Essay/Essay/main');?>">
		<select id="className" name="className" onchange="change_classCode(this.value,'<?php echo U('Essay/Essay/getClassCodeList');?>')">
			<option value="">请选择班级名称</option>
			<?php if(!empty($classNameArr)):?>
				<?php foreach($classNameArr as $key=>$className):?>
				<option value="<?php echo $className;?>" <?php if($_GET['className'] == $className):?>selected<?php endif;?>><?php echo $className;?></option>
				<?php endforeach?>
			<?php endif;?>
		</select>
		<select id="classCode" name="classCode">
			<option value="">请选择班级编码</option>
			<?php if(!empty($classList)):?>
				<?php foreach($classList as $key=>$class):?>
				<option value="<?php echo $class['s_class_code'];?>" <?php if($_GET['classCode'] == $class['s_class_code']):?>selected<?php endif;?>><?php echo $class['s_class_code'];?></option>
				<?php endforeach?>
			<?php endif;?>
		</select>
		<input type="submit" value="搜索" class="btn2">
		</form>
		<table class="tableList" border="0" cellpadding="0" cellspacing="0"  width="90%" id="apply_table">
			<tr>
				<th>班型</th>
				<th>班级编码</th>
				<th>已上传作文数量</th>
			</tr>
			<?php foreach($essayList as $key=>$essay):?>
			<tr>
				<td><a href="<?php echo U('Essay/Essay/essayDetail',array('class_code'=>$essay['class_code']))?>"><?php echo $essay['class_name']?></a></td>
				<td><?php echo $essay['class_code']?></td>
				<td><?php echo $essay['count_num']?></td>
			</tr>
			<?php endforeach?>
		</table>
		<div id="pageStr"><?php echo $showPage;?></div>
	</div>
</div>
</body>
</html>