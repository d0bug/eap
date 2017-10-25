<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="format-detection" content="telephone=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<title>活动报名信息</title>
	<link rel="stylesheet" type="text/css" href="/static/css/weixinBase.css">
	
</head>
<body>
<style type="text/css">
	.mybg td{text-align: center;}
</style>
<div class="auto mybg">
	<table cellpadding="0" cellspacing="0">
		<tr class="thear">
			<td class="c-green" width="38%">活动名称</td>
			<td class="c-green" width="16%">姓名</td>
			<td class="c-green" width="26%">上课校区</td>
            <td class="c-green" width="20%">预约时间</td>
		</tr>
		<?php if(!empty($signList)){ foreach($signList as $sign){?>
			<tr>
				<td><?php echo $sign->hdtitle;?></td>
				<td><?php echo $sign->name;?></td>
				<td><?php echo $sign->campus;?></td>
	            <td><?php echo date('Y-m-d',$sign->datetime);?></td>
			</tr>
		<?php  }}else{
			echo '<tr>
					<td colSpan="4" style="text-align:center">暂无任何报名信息...</td>
				 </tr>';
		}?>
	</table>
	
</div>

<!--nav-->
<div class="home" style="bottom:8px;"><a href="<?php echo U('Vip/GsWeixin/index');?>"></a></div>

</body>
</html>