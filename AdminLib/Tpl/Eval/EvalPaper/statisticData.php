<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/viptest.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
<script src="http://code.highcharts.com/highcharts.js"></script>
<script src="http://code.highcharts.com/modules/exporting.js"></script>
<script type="text/javascript">
$(function () {
	$('#pie_chart').highcharts({
		chart: {
			plotBackgroundColor: null,
			plotBorderWidth: null,
			plotShadow: false
		},
		title: {
			text: ''
		},
		tooltip: {
			pointFormat: ''
		},
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: true,
					format: '<b>{point.name}</b>: {point.percentage:.1f} %',
					style: {
						color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
					}
				}
			}
		},
		series: [{
			type: 'pie',
			name: 'Browser share',
			data: [
			<?php foreach ($levelArr as $key=>$level):?>
			['<?php echo $level['name']?>',   <?php echo $level['percentage']?>],
			<?php endforeach?>
			]
		}]
	});
	$('#bar_chart').highcharts({
		chart: {
			type: 'column'
		},
		title: {
			text: ''
		},
		subtitle: {
			text: ''
		},
		xAxis: {
			type: 'category',
			labels: {
				rotation: -45,
				align: 'right',
				style: {
					fontSize: '13px',
					fontFamily: 'Verdana, sans-serif'
				}
			}
		},
		yAxis: {
			min: 0,
			title: {
				text: '正确率（%）'
			}
		},
		legend: {
			enabled: false
		},
		tooltip: {
			pointFormat: '',
		},
		series: [{
			name: 'Population',
			data: [
			<?php foreach ($moduleArr as $key=>$module):?>
			['<?php echo $module['name']?>', <?php echo sprintf('%.2f',$module['correct']/($module['correct']+$module['error'])*100)?>],
			<?php endforeach?>
			],
			dataLabels: {
				enabled: true,
				rotation: -90,
				color: '#FFFFFF',
				align: 'right',
				x: 4,
				y: 10,
				style: {
					fontSize: '13px',
					fontFamily: 'Verdana, sans-serif',
					textShadow: '0 0 3px black'
				}
			}
		}]
	});
});
</script>
</head>
<body >
<div region="center" >
<div id="main">
	<h2>统计数据</h2>
	<div id="search">
		<form method="POST" action="">
		试卷：<select id="paper_id" name="paper_id" style="width:130px;">
				<option value="">请选择试卷</option>
				<?php foreach($paperList as $key=>$paper):?>
				<option value="<?php echo $paper['id']?>" <?php if($paper['id']==$_REQUEST['paper_id']):?>selected<?php endif;?>><?php echo $paper['title']?></option>
				<?php endforeach?>
			</select>&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="submit" value="搜索" >
		</form>
	</div>
	<?php if($_REQUEST['paper_id']):?>
		<h1><?php echo $paperInfo['title']?></h1><br>
		<h3>试卷数据：</h3>
		<p class="m_left_70">做过该试卷的人数：<?php echo $total?>人(完成)</p>
		<p class="m_left_70">平均正确率：<?php echo $accuracy_avg?>%</p><br><br>
		<h3>各等级人数：</h3><br>
		<table width="80%">
			<tr>
				<td width="40%"><div id="pie_chart" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto">圆饼图</div></td>
				<td width="10%">&nbsp;</td>
				<td >
					<table width="60%" border="1">
					<?php foreach ($levelArr as $key=>$level):?>
						<tr>
							<td><?php echo $level['name']?></td>
							<td><?php echo $level['count']?>人</td>
							<td><?php echo $level['percentage']?>%</td></tr>
					<?php endforeach?>
					</table>	
				</td>
			</tr>
		</table><br><br>
		<h3>各模块数据：</h3><br>
		<table width="50%">
			<tr>
				<td width="40%"><div id="bar_chart">柱状图</div></td>
			</tr>
			<tr>
				<td width="40%">
					<table border="1" width="70%">
						<tr><th>模块名称</th><th>正确率</th><th>包含题目</th></tr>
					<?php foreach ($moduleArr as $key=>$module):?>
						<tr>
							<td align="center"><?php echo $module['name']?></td>
							<td align="center"><?php echo sprintf('%.2f',$module['correct']/($module['correct']+$module['error'])*100)?>%</td>
							<td align="center"><?php echo $module['question_num']?>道</td></tr>
					<?php endforeach?>
					</table>
				</td>
			</tr>
		</table>
	<?php else:?>
		请先选择试卷。。。。。
	<?php endif;?>
</div>
</div>
</body>
</html>