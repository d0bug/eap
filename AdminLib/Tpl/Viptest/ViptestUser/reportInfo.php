<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
	<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
	<title><?php echo $resultInfo['uname']?>的早培神测报告</title>
	<link rel="stylesheet" href="/static/css/baogao.css">
	<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
	<script src="http://code.highcharts.com/highcharts.js"></script>
	<script src="http://code.highcharts.com/highcharts-more.js"></script>
	<script src="http://code.highcharts.com/modules/exporting.js"></script>
	<script type="text/javascript">
	$(function () {
		$('#spiderweb').highcharts({
			chart: {
				polar: true,
				type: 'line'
			},

			title: {
				text: '',
				x: -80
			},

			pane: {
				size: '80%'
			},

			xAxis: {
				categories: [<?php echo $moduleList['nameStr']?>],
				tickmarkPlacement: 'on',
				lineWidth: 0
			},

			yAxis: {
				gridLineInterpolation: 'polygon',
				lineWidth: 0,
				min: 0
			},

			tooltip: {
				shared: true,
				pointFormat: '<span style="color:{series.color}">{series.name}: <b>{point.y:,.0f}%</b><br/>'
			},

			legend: {
				align: 'right',
				verticalAlign: 'top',
				y: 70,
				layout: 'vertical'
			},

			series: [{
				name: '平均得分率',
				data: [<?php echo $moduleList['AccuracyAvgStr']?>],
				pointPlacement: 'on',
			}, {
				name: '个人得分率',
				data: [<?php echo $moduleList['AccuracySelfStr']?>],
				pointPlacement: 'on'
			}]

		});

		<?php foreach($moduleList['data'] as $key=>$module):?>
		$('#lineChart_'+<?php echo $key?>).highcharts({
			title: {
				text: 'Monthly Average Temperature',
				x: -20 //center
			},
			subtitle: {
				text: '试卷成绩心跳图',
				x: -20
			},
			xAxis: {
				categories: [<?php foreach ($module['myRecordList'] as $kk=>$record):?>'第<?php echo $record['seq']?>题',<?php endforeach?>]
			},
			yAxis: {
				title: {
					text: 'Temperature (°C)'
				},
				plotLines: [{
					value: 0,
					width: 1,
					color: '#808080'
				}]
			},
			tooltip: {
				valueSuffix: '°C'
			},
			legend: {
				layout: 'vertical',
				align: 'right',
				verticalAlign: 'middle',
				borderWidth: 0
			},
			series: [{
				name: '平均得分率',
				data: [<?php foreach ($module['myRecordList'] as $kk=>$record):?><?php echo $record['accuracy_avg']?>,<?php endforeach?>]
			}, {
				name: '个人得分率',
				data: [<?php foreach ($module['myRecordList'] as $kk=>$record):?><?php echo ($record['is_correct']==1)?100:0?>,<?php endforeach?>]
			}]
		});
		<?php endforeach?>
	});
	</script>
</head>
<body>
<div id="wrapper">
	<div id="header" class="shuxue"> <!-- 样式名为学科的全拼 -->
		<h2>高思Vip早培神测&nbsp;&nbsp;&nbsp;<?php echo $resultInfo['title']?></h2>
		<h1><?php echo $resultInfo['uname']?> 的早培神测报告</h1>
		<div class="line"></div>
	</div>
	<div id="main">
		<!-- 成绩总体评价 -->
		<div class="module mod01">
			<div class="modHd">
				<h2>成绩总体评价
				</h2>
			</div>
			<div class="modBd clearfix">
				<div class="medalBox">
					<div class="medal">
						<span><?php echo $resultInfo['level']?></span>
					</div>
					<!-- 无奖项显示：
					<div class="medal noMedal">
						<span>无奖项</span>
					</div> -->
					<div class="score">
						做对<?php echo $resultInfo['accuracy_count']?>道题
						<span>排名：第<?php echo $resultInfo['rank']?>名</span>
					</div>
					<div class="class">
						<dl>
							<dt><?php echo $resultInfo['level_desc']?></dt>
							<!--<dd><a href="#">备考夏令营内容</a></dd>
							<dd><a href="#">备考夏令营内容</a></dd>-->
						</dl>
						<!-- <div class="btns">
							<button type="button">报班凭证</button>
						</div> -->
					</div>
				</div>
				<div class="rankingBox">
					<div class="barGraph">
						<div class="bar" style="height:<?php echo (($resultInfo['accuracy_count']/$resultInfo['question_num'])*100)?>%">
							<div class="tip">你在这里</div>
						</div>
					</div>
					<div class="label1">
						<ul>
						<?php foreach($resultInfo['level_arr'] as $key=>$level):?>
							<li>
								<div class="bg">
									<em class="num">
									<?php if($key==0):?>:>
										<?php echo $resultInfo['question_num']?>
									<?php else:?>
										<?php echo $resultInfo['level_arr'][$key-1]['low']?>
									<?php endif;?>题
									</em>
									<div class="bd" style="height:<?php echo sprintf('%.0f',(207/$resultInfo['level_count']))?>px; line-height:51px">
										<span class="top"></span>
										<p class="con"><?php echo $level['name']?></p>
										<span class="bot"></span>
									</div>
								</div>
							</li>
						<?php endforeach?>
							<li>
								<div class="bg">
									<em class="num">0题</em>
								</div>
							</li>
						</ul>
					</div>
					
				</div>
			</div>
		</div>

		<!-- 模块分析 -->
		<div class="module mod03">
			<div class="modHd">
				<h2>模块分析
				</h2>
			</div>
			<div class="modBd clearfix">
				<div class="resultDiff">
					<ul>
						<?php foreach($moduleList['data'] as $key=>$module):?>
						<li class="item i1">
							<div class="barBox">
								<div class="label"><span><?php echo $module['name']?></span>共<?php echo $module['question_num']?>题</div>
							</div>
							<div class="bar">
								<div style="height:<?php echo $module['accuracy_self']?>%" class="bar2"></div>
								<div style="top:<?php echo floor(100-$module['accuracy_avg'])?>%" class="average">
									<div class="line"></div>
									<div class="tip">平均对<br><span class="cBlue"><?php echo $module['correct_avg']?>道</span></div>
								</div>
							</div>
							<div class="you">你做对了<span class="cOrange">：<?php echo $module['myself_correct_total']?>道</span></div>
						</li>
						<?php endforeach?>
					</ul>
					<div class="msg">注：瓶子里的水位代表你的得分</div>
				</div>
				
			</div>
		</div>

		<!-- 孩子情况分析 -->
		<div class="module mod04">
			<div class="modHd">
				<h2>孩子情况分析
				</h2>
			</div>
			<div class="modBd clearfix">
				<div class="pic">
					<div class="barGraph" >
						<div id="spiderweb" style="min-width: 380px; max-width: 380px; height: 320px;"></div>
					</div>
				</div>
				<div class="txt">
					<div class="tit">
						<h3><span>孩子情况</span><i></i></h3>
					</div>
					<dl>
					<?php foreach($moduleList['data'] as $key=>$module):?>
						<dt>孩子的 <span><?php echo $module['name']?></span></dt>
						<dd><?php echo $module['excellent_status']?></dd>
					<?php endforeach?>
					</dl>
				</div>
			</div>
		</div>

		<!-- 各题回答情况 -->
		<div class="module mod05">
			<div class="modHd">
				<h2>各题回答情况
				</h2>
			</div>
			<div class="modBd clearfix">
				<div class="barGraph lineChart">
					<?php foreach($moduleList['data'] as $key=>$module):?>
					<div id="lineChart_<?php echo $key?>" style="min-width: 820px; max-width: 820px; height: 350px;<?php if($key!=0):?>display:none;<?php endif;?>" >图表：820x350_<?php echo $key?></div>
					<?php endforeach?>
				</div>
				
				<table width="820" border="0" cellspacing="0" cellpadding="1" class="tiTab">
					<tr>
						<th class="first"></th>
						<?php foreach($moduleList['data'] as $key=>$module):?>
						<td <?php if($key==0):?>class="current"<?php endif;?>><div><?php echo $module['name']?></div></td>
						<?php endforeach?>
						<th class="last"></th>
					</tr>
				</table>
				<div class="tiBox">
				<?php foreach($moduleList['data'] as $key=>$module):?>
					<div class="xtItem" <?php if($key!=0):?>style="display:none;"<?php endif;?>>				
						<div class="row"><span class="alt">题目类型：  </span><?php echo $module['name']?></div>
						<div class="row"><span class="alt">题目数量：</span><?php echo $module['question_num']?>道 <span>个人做对：</span><?php echo $module['myself_correct_total']?>道 <span>平均正确率：</span><?php echo $module['accuracy_avg']?>% <span>个人正确率：</span><?php echo $module['accuracy_self']?>%</div>
						<div class="row"><span class="alt">得分详情</span></div>
						<ul class="list">
							<?php foreach($module['myRecordList'] as $kk=>$record):?>
							<li>
								<strong>第<?php echo $record['seq']?>小题</strong>
								<span>正确答案：<em class="cOrange"><?php echo $answerArr[$record['correct_answer']]?></em></span><span>你的答案：<em class="cGreen"><?php echo $answerArr[$record['answer']]?></em></span><span>平均正确率：<em class="cBlue"><?php echo $record['accuracy_avg']?>%</em></span> </span> <span><div class="jdt"><p style="width:<?php echo $record['accuracy_avg']?>%"></p></div></span> <a href="<?php echo $record['img_url']?>" title="01小题" class="m myti"><span>查看原题</span></a>
							</li>
							<?php endforeach?>
						</ul>
					</div>
					<?php endforeach?>
				</div>
			</div>
		</div>

	</div>


	<div id="footer">
		<button type="button">打印神测报告</button>
		<button type="button">下载神测报告</button>
	</div>
</div>


<link href="http://img.gaosiedu.com/www/js/gstools/gstools.css" rel="stylesheet" />
<script src="http://img.gaosiedu.com/www/js/jquery.gstools.min.js"></script>
<script type="text/javascript">
$(function() {
	$('.tiTab td').click(function() {
		var index = $(this).index();
		$(this).addClass('current').siblings().removeClass('current');
		$('.tiBox > div').eq(index-1).show().siblings().hide();
		$('.lineChart > div').eq(index-1).show().siblings().hide();
	});
	$('.myti').gsmodel();
});
</script>
</body>
</html>