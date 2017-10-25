<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="UTF-8">
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<script type="text/javascript" src="/static/js/modular.js"></script>
<link href="/static/css/modular.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
	<div id="main">
		<div class="Snav center">
			<li ref="model1" id="step1">1. 上传教师列表资料</li>
			<li ref="model2" id="step2" class="hover" >2. 获取最终表单和代码</li>
		</div><br>
		<p><b>教师信息列表如下：</b></p>
		<div class="clearit"></div>	
		<div id="main_container" class="center model1 model" >
			<table width="100%" border="1" class="tableForm" cellpadding="0" cellspacing="0">
		      <?php foreach($arrTeacherData as $key=>$teacher):?>
			      <?php if(!empty($teacher)):?>
			      <tr>
			      <?php for ($temp = 0;$temp<$count;$temp++):?>
			        <td width="8%">&nbsp;<?php echo $teacher[$temp];?></td>
			      <?php endfor;?>
			      </tr>
			      <?php endif;?>
		      <?php endforeach?>
		    </table><br>
			<a href="<?php echo U('Modular/ModularTeacher/export_excel',array('filename'=>$newFilename,'folder'=>$folder));?>" class="btn" >下载</a>
		</div>
		<br>
		<p><b>html代码如下：</b></p>
		<div class="clearit"></div>	
		<div id="html_container" class="center model1 model" >
			<textarea cols="180" rows="30" id="teacherlist_html"><?php echo $teacherListHtml;?></textarea><br>
			<button class="btn"  onclick="copy_clip('#teacherlist_html')">复制</button>
		</div>
		<div class="clearit"></div><br>
		<div id="html_container" class="center" ><a href="<?php echo U('Modular/ModularTeacher/main')?>" class="btn">重新配置</a></div>
	</div>
</div>
</body>
</html>