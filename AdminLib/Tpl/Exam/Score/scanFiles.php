<!doctype html>
<html>
	<head>
	<style type="text/css">
	dl{margin:0px}
	dt{border:1px solid #ddd;background:#62B7ED;padding:5px;font-weight:bold;color:white}
	dd{clear:both;height:260px;padding:0px;}
	a {display:block;float:left}
	img {margin:5px;border:1px solid #ccc;width:180px;height:240px;display:block}
	</style>
	</head>
	<body>
	<div style="margin:0px auto;width:600px;border:1px solid #ddd">
	<dl>
		<?php 
		foreach ($subjects as $subject=>$subjectName):
		if(isset($fileArray[$subject])):
		?>
		<dt><?php echo $subjectName?></dt>
		<dd>
		<?php foreach ($fileArray[$subject] as $file):?>
		<a href="<?php echo $file?>" target="_blank"><img src="<?php echo $file?>" /></a>
		<?php endforeach;?>
		</dd>
		<?php
		endif;
		endforeach;
		?>
	</dl>
	</div>
	</body>
</html>