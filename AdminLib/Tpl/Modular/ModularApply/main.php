<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/modular.js"></script>
<link href="/static/css/modular.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
	<div id="main">
		<h2>在线预约报名&nbsp;&nbsp;（服务于<?php echo $moduleCount;?>项目）&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo U('Modular/ModularApply/step_one')?>" class="btn">添加新项目</a></h2>
		<table class="tableList" border="0" cellpadding="0" cellspacing="0"  width="90%" id="apply_table">
			<tr>
				<th>序号</th>
				<th>模块标题</th>
				<th>需求频道</th>
				<th>使用人次</th>
				<th>添加时间</th>
				<th>统计报表</th>
			</tr>
			<?php foreach($moduleList as $key=>$module):?>
			<tr>
				<td><?php echo $module['id']?></td>
				<td><a href="<?php echo U('Modular/ModularApply/step_one',array('mid'=>$module['id']))?>"><?php echo $module['name']?></a></td>
				<td><?php echo $channelArr[$module['channel']]?></td>
				<td><?php echo $module['used_num']?></td>
				<td><?php echo (!empty($module['instime']))?date('Y-m-d',$module['instime']):'—';?></td>
				<td><a href="<?php echo U('Modular/ModularApply/preview',array('mid'=>$module['id'],'mname'=>$module['name']))?>">预览</a>&nbsp;&nbsp;
					<a href="<?php echo U('Modular/ModularApply/export_excel',array('mid'=>$module['id']));?>">下载</a>
				</td>
			</tr>
			<?php endforeach?>
		</table>
		<p><input type="button" class="btn" onclick="toggle('#apply_table','#flex_btn_apply')" id="flex_btn_apply" value="收起"></p><br>
		
	</div>
</div>
</body>
</html>