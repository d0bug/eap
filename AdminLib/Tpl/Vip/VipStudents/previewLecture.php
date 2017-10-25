<!DOCTYPE html>
<html>
<head>
	<title>讲义预览</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/static/js/jquery.blockUI.js"></script>
	<script type="text/javascript" src="/static/js/jquery.uploadify-3.1.min.js"></script>
	<script type="text/javascript" src="/static/js/jquery.raty.min2.js"></script>
	<script type="text/javascript" src="/static/js/vip.js"></script>
</head>

<style type="text/css">
html, body, ul, li, ol, dl, dd, dt, p, h1, h2, h3, div, h5, h6, form, fieldset, legend, img { margin:0; padding:0; }
fieldset, img { border:none; }
ul, ol { list-style:none; }
em, i {font-style:normal}
img {vertical-align:middle}
input { padding-top:0; padding-bottom:0; font-family: Arial,"微软雅黑";}
input::-moz-focus-inner { border:none; padding:0; }
select, input { vertical-align:middle; }
select, input, textarea { font-size:14px; margin:0; }
input[type="text"], input[type="password"], textarea { outline-style:none; -webkit-appearance:none; }
textarea { resize:none; }

table { border-collapse:collapse; }
body { color:#646482; font:12px/22px "微软雅黑"; background:#fff;/* overflow-y:scroll;*/ }
address, caption, cite, code, dfn, th, var { font-style:normal; font-weight:normal; }

a { color:#646482; text-decoration:none; }
a:visited { color:#646482; }
a:hover, a:active, a:focus { color:#F60; text-decoration:none; }

.clearfix:after { content:"."; display:block; height:0; visibility:hidden; clear:both; }
.clearfix { zoom:1; }
.clearit { clear:both; height:0; font-size:0; overflow:hidden; }
.clear{clear: both;}


/*页眉和标题*/
.panel{width:1000px; font-size: 14px;margin:0 auto;font-family: "微软雅黑";color: #333;background: url(images/M_YW_mack.jpg); }
.panel .minh800{min-height:800px;}
.panel .top{height: 120px;background: url(/static/images/M_HX_01.jpg) no-repeat; }
.panel .header{padding-top: 20px;height:22px;font-size:12px;color: #999;}
.panel .header .left{float: left;padding-left: 22px;}
.panel .header .right{float:right;padding-right:22px;}
.panel .topline {height:20px}
.panel .topline div,.panel .bottomline div{ border: 1px solid #ccc; height: 16px;width: 22px;}
.panel .topline .left{float: left;  border-left: 0; border-top: 0;}
.panel .topline .right{float: right; border-right: 0 ; border-top: 0;}
.panel .bottomline{height: 22px;margin-top: 30px}
.panel .bottomline .left{float: left;  border-left: 0; border-bottom: 0;}
.panel .bottomline .right{float: right; border-right: 0 ; border-bottom: 0;}
.panel .top .title{clear:both;text-align: center;font-size: 26px;height:40px;padding-top:20px;font-weight: bold;}
.panel .top .subtitle{text-align: center;padding-left: 50px;color: #999;font-size: 16px }
.panel .oneline{height:40px;border-bottom: 2px dashed #999;clear: both;text-align: center;margin-bottom: 20px;}
.panel .oneline p{font-size: 12px;color: #999;}
.panel .con{padding: 0 22px}
.panel .con .ctitle{height: 60px}
.panel .con .ctitle .intro{width: auto;height:50px;overflow:hidden;text-indent:80px;font: 24px/60px "微软雅黑";margin-top:30px;margin-left: 10px}
.panel .con .background{background: url(/static/images/M_HX_02.jpg) no-repeat;}
.panel .con .cBg_1 .background{background-position: 0 0;width:auto;}
.panel .con .cBg_2 .background{background-position: 0 -57px;}
.panel div.hide {display: none;}

.handoutline{border: 5px solid #0aa4df;}
.handoutshow{width:1000px;margin: 0 auto }
.handoutshow .panel-left{margin: 0;float: left;}
.handoutshow .panel-right{float: right;border: 1px solid #ddd;border-right: 1px solid #ddd;width: 330px;padding:0 20px;background: #f5f5f5;font-size: 14px;}
.panel-right-title{background:#0aa4df;margin-top: 20px;font-size: 16px;color: #fff;padding:10px 15px ;border-radius: 4px}
.panel-right-title .input{line-height:24px;border: 1px solid #3399cc;width: 200px;padding: 2px 7px;}
.panel-right-title .mb5{margin-bottom: 5px;}
.panel-right-Qtitle{border-left:4px solid #13bf17;color:#03b903;font-weight: bold; line-height:24px;padding-bottom:3px; font-size:19px;padding-left:10px;margin:40px 0 20px 0;}
.panel-right-Q1title{border: 1px solid #0aa4df; border-radius: 50px; height: 36px; line-height: 36px;margin-bottom: 20px;width: 327px;background: #fff}
.panel-right-Q1title span{cursor: pointer;display: block;float: left;height: 36px;text-align: center;width: 109px;}
.panel-right-Q1title span.on{background:#0aa4df;color: #fff }
.panel-right-Q1title span.bltopbottom4{border-top-left-radius: 50px;border-bottom-left-radius: 50px;}
.panel-right-Q1title span.brtopbottom5{border-top-right-radius:50px;border-bottom-right-radius:50px;}

.panel-right-con1 table {border: medium none; border-collapse: collapse;  font-size:14px;line-height: 24px;width: 100%;text-align: center;margin-bottom: 20px;}
.panel-right-con1 table th{color: #0aa4df}
.panel-right-con1 .even td{background:#e8e8e8}
.panel-right-con1 .odd td{background:#f2f2f2}
.panel-right-con1 label{display: inline-block;width: 100%;height:100%;padding: 5px 0;cursor: pointer; }
.panel-right-bluebtn,.panel-right-redbtn{width: 150px;line-height:36px;cursor: pointer; font-size: 16px;color: #fff; border-radius: 6px;background: #0aa4df;box-shadow: 0 4px 0 #1087b5;margin: 0 auto;text-align: center;}
.panel-right-bluebtn:hover{background: #f90;box-shadow: 0 4px 0 #f60;}
.panel-right-redbtn{background: #f90;box-shadow: 0 4px 0 #f60;margin-bottom: 50px;}
.baogao-icon{background: url(/static/images/baogao-icon.png) no-repeat;}
.panel-right-con2{margin:0 15px;}
.panel-right-con2 li{height: 40px;line-height: 40px;padding:5px 10px 5px 20px;border-bottom: 1px dashed #ddd}
.panel-right-con2 span{display: inline-block;width: 106px;height: 32px;overflow: hidden; vertical-align:middle}
.panel-right-con2 li span img{display:block;float:left;margin-right:3px;}
.panel-right-con2 .getstar1{background-position:-90px -185px; }
.panel-right-con2 .getstar2{background-position:-90px -224px; }
.panel-right-con2 .getstar3{background-position:-90px -261px; }
.panel-right-con2 .getstar4{background-position:-90px -296px; }
.panel-right-con2 .saytitle{font-size: 16px;color: #339900;margin:20px 0 10px 0}
.panel-right-con2 .saytitle span{background-position: -255px -137px;display: inline-block;vertical-align: middle;height: 30px;width: 50px;padding-left: 50px;padding-top: 5px; }
.panel-right-con2 textarea{width: 286px;height:80px;border: 1px solid #ddd;padding: 6px;font-size: 12px;font-family: arial;line-height: 24px;}
.panel-right-con2 .textnumber{color: #999;font-size: 12px;text-align: right;margin-bottom: 20px;}
.panel-right-con2 .textnumber i{color: #f60}
.panel-right-con3 .imglist{margin-top: 20px}
.panel-right-con3 .imglist li{margin-bottom:10px;padding-bottom:10px; border-bottom: 1px dashed #ddd;width:275px;padding-right:60px;padding-left: 10px;position: relative; }
.panel-right-con3 .imglist li span{display: block;position: absolute;top: 10px;right: 10px;background: #0aa4df;width: 26px;height: 26px;text-align: center; border-radius:40px;color: #fff;cursor: pointer;font:bold 22px/28px arial;overflow: hidden; }
.panel-right-con3 .imglist li span:hover{background: #f60}
.panel-right-con3 .imglisttext{color: #f60;margin-bottom: 30px;}

.question-box .title {
    background: none repeat scroll 0 0 #dfdfdf;
    height: 36px;
    overflow: hidden;
}
.question-box .title li {
    float: left;
    padding: 9px 5px;
}
.question-box .title li.fr {
    float: right;
}
.question-box .title li.btn {
    padding: 7px 5px;
}
.question-box .title li.exp {
    padding: 10px 5px;
}
.question-box .title li.err {
    float: right;
    left: 15px;
    padding-top: 10px;
}
.question-box .title li.Source em {
    color: #ff7800;
}
.question-empty {
    padding: 10px;
    text-align: center;
}
.fl {
    float: left;
}
.mr_20 {
    margin-right: 20px;
}
.cf::after {
    clear: both;
    content: "";
    display: table;
}
.question-box .bd {
    padding: 15px;
}
.question-box .bd .con {
    cursor: pointer;
}
.question-box .answer {
    display: none;
}
.question-box .box {
    background: url("../images/box_bg.png") repeat-x scroll left top #ffffff;
    border: 1px solid #dfdfdf;
    border-radius: 3px;
    margin-top: 20px;
    padding: 10px 20px;
}
.question-box .box h4 {
    font-size: 12px;
    margin-top: 10px;
}
.question-box .box h4 label {
    color: #ff7800;
}
.question-box .box li {
    border-bottom: 1px dashed #d8d8d8;
    overflow: hidden;
    padding: 12px 0 12px 75px;
}
.question-box .box li strong {
    background: url("../images/main_img.png") no-repeat scroll 0 -357px rgba(0, 0, 0, 0);
    display: inline-block;
    margin-left: -75px;
    padding-left: 18px;
    width: 53px;
}
.question-box .box .text {
    padding-top: 10px;
}
.question-box .box .text strong {
    margin-left: 18px;
}
.uploadify{width: 150px;line-height:36px;cursor: pointer; font-size: 16px;color: #fff; border-radius: 6px;background: #0aa4df;box-shadow: 0 4px 0 #1087b5;margin: 0 auto;text-align: center;}
.uploadify:hover{background: #f90;box-shadow: 0 4px 0 #f60;}
.baogao-printer{background-position: 0 -365px;cursor: pointer;height: 53px;margin-left:-90px;bottom:0;left:50%;position: fixed;width: 178px; }
.baogao-icon{background-images: url("/static/images/baogao-icon.png") no-repeat;}
.panel .minh800 .con2 img{max-width:500px}
</style>
<body>
<div class="handoutline"></div>
<div class="handoutshow">
	<div class="panel">
		<div class="header">
			<div class="left"><?php echo $heluInfo['lecture_info']['config']['struct']['header']['title']['text'];?></div>
			<div class="right"><img src="/static/images/m-logo.png"></div>
		</div>
		<div class="topline">
			<div class="left"> </div>
			<div class="right"> </div>
		</div>
		<div class="minh800">
			<div class="top">
				<!--主标题-->
				<div class="title">
					<?php if($heluInfo['lecture_info']['config']['struct']['header']['title']['visible']==1):?>
						<?php echo $heluInfo['lecture_info']['config']['struct']['header']['title']['text'];?>
					<?php endif;?>
				</div>
				<!--副标题-->
				<div class="subtitle">
					<?php if($heluInfo['lecture_info']['config']['struct']['header']['subtitle']['visible']==1):?>
					——  <?php echo $heluInfo['lecture_info']['config']['struct']['header']['subtitle']['text'];?>
					<?php endif;?>
				</div>
			</div>
			
			<!--知识点-->
			<?php if($heluInfo['lecture_info']['config']['struct']['header']['knowledge']['visible']==1):?>
			<div class="con con2">
				<div class="ctitle cBg_1">
					<div class="background intro"><?php echo $heluInfo['lecture_info']['config']['struct']['header']['knowledge']['text'];?></div>
				</div>
				<p>
				<?php if($heluInfo['lecture_info']['config']['struct']['body']['special']['types']):?>
					<?php foreach ($heluInfo['lecture_info']['config']['struct']['body']['special']['types'] as $key=>$knowledge):?>
						<div><?php echo $numberKey[$key];?>、<?php echo $knowledge['title'];?></div>
						<div style="padding:20px;"><?php echo $knowledge['tips'];?></div>
					<?php endforeach;?>
				<?php endif;?>
				</p>
			</div>
			<?php endif;?>
			
			<!--前言-->
			<?php if($heluInfo['lecture_info']['config']['struct']['header']['introduction']['visible']==1):?>
			<div class="con  con2">
				<div class="ctitle cBg_1">
					<div class="background intro"><?php echo $heluInfo['lecture_info']['config']['struct']['header']['introduction']['text'];?></div>
				</div>
				<p>
				<?php echo $heluInfo['lecture_info']['config']['struct']['header']['introduction']['content'];?>
				</p>
			</div>
			<?php endif;?>
			<!--前言2-->
			<?php if($heluInfo['lecture_info']['config']['struct']['header']['introduction1']['visible']==1):?>
			<div class="con  con2">
				<div class="ctitle cBg_1">
					<div class="background intro"><?php echo $heluInfo['lecture_info']['config']['struct']['header']['introduction1']['text'];?></div>
				</div>
				<p>
				<?php echo $heluInfo['lecture_info']['config']['struct']['header']['introduction1']['content'];?>
				</p>
			</div>
			<?php endif;?>
			<!--前言3-->
			<?php if($heluInfo['lecture_info']['config']['struct']['header']['introduction2']['visible']==1):?>
			<div class="con  con2">
				<div class="ctitle cBg_1">
					<div class="background intro"><?php echo $heluInfo['lecture_info']['config']['struct']['header']['introduction2']['text'];?></div>
				</div>
				<p>
				<?php echo $heluInfo['lecture_info']['config']['struct']['header']['introduction2']['content'];?>
				</p>
			</div>
			<?php endif;?>
			
			<!--题型-->
			<?php if($heluInfo['lecture_info']['config']['struct']['body']['question_type']['visible'] == 1):?>
			<div class="con">
				<div class="ctitle cBg_2">
					<div class="background intro"><?php echo $heluInfo['lecture_info']['config']['struct']['body'][$heluInfo['lecture_info']['cart']['cart']['sort']]['title']?></div>
				</div>
				<?php if(!empty($heluInfo['lecture_info']['question_list']['module'])):?>
					<?php $num = 1;?>
					<?php $num2 = 0;?>
					<?php foreach ($heluInfo['lecture_info']['question_list']['module'] as $key=>$question_type):?>
					<?php if($question_type['visible']==1):?>
					<p>
						<div><?php echo $numberKey[$num2]?>、<?php echo $question_type['title']?></div>
						
							<?php foreach ($question_type['question_list'] as $key=>$question):?>
								<p>
									<div onclick="javascript:$('#answer_<?php echo $num?>').toggle()" style="cursor:pointer">
										<?php echo $num;?>、<?php echo $question['content']?><br>
										<?php if(!empty($question['question_option'])):?>
										<table>
											<?php foreach ($question['question_option'] as $k=>$option):?>
												<tr>
													<td><?php echo $optionKeyArr[$k]?>.</td><td><?php echo $option['content']?></td>
												</tr>
											<?php endforeach?>
										</table>
										<?php endif;?>
									</div>
									<div id="answer_<?php echo $num?>" style="cursor:pointer;display:none" onclick="javascript:$(this).hide()" >
										【答案】<br>
										<?php echo $question['answer_content']?><br>
										【解析】<br>
										<?php echo $question['analysis']?><br>
									</div>
								</p><br><br>
								<?php $num ++;?>
							<?php endforeach;?>
					</p>
					<?php $num2 ++;?>
					<?php endif;?>
					<?php endforeach;?>
				<?php endif;?>
			</div>
			<?php endif;?>
			
			
			<!--总结-->
			<?php if($heluInfo['lecture_info']['config']['struct']['header']['summary']['visible']==1):?>
			<div class="con  con2">
				<div class="ctitle cTBg_1">
					<div class="background intro"><?php echo $heluInfo['lecture_info']['config']['struct']['header']['summary']['text'];?></div>
				</div>
				<p>
				<?php echo $heluInfo['lecture_info']['config']['struct']['header']['summary']['content'];?>
				</p>
			</div>
			<?php endif;?>
			
			<!--其它-->
			<?php if($heluInfo['lecture_info']['config']['struct']['header']['other']['visible']==1):?>
			<div class="con  con2">
				<div class="ctitle cTBg_1">
					<div class="background intro"><?php echo $heluInfo['lecture_info']['config']['struct']['header']['other']['text'];?></div>
				</div>
				<p>
				<?php echo $heluInfo['lecture_info']['config']['struct']['header']['other']['content'];?>
				</p>
			</div>
			<?php endif;?>
		</div>

		<div class="bottomline">
				<div class="left"> </div>
				<div class="right"> </div>
		</div>
		<div class="oneline"><p>试卷第1页，总3页</p></div>
		<div class="header">
				<div class="left"><?php echo $heluInfo['lecture_info']['config']['struct']['header']['title']['text'];?></div>
				<div class="right"><img src="/static/images/m-logo.png"></div>
		</div>
		<div class="topline">
				<div class="left"> </div>
				<div class="right"> </div>
		</div>

		<div class="minh800">
			
			<div class="con">
				<div class="ctitle cBg_2">
					<div class="background intro">随堂练习</div>
				</div>
				<?php if(!empty($heluInfo['lecture_info']['question_list']['practise'])):?>
					<?php foreach ($heluInfo['lecture_info']['question_list']['practise'] as $key=>$question):?>
					<p>
						<div onclick="javascript:$('#answer2_<?php echo $key?>').toggle()" style="cursor:pointer">
							<?php echo ($key+1)?>、<?php echo $question['content']?><br><br>
							<?php if(!empty($question['question_option'])):?>
							<table>
								<?php foreach ($question['question_option'] as $k=>$option):?>
								<tr>
									<td><?php echo $optionKeyArr[$k]?>.</td><td><?php echo $option['content']?></td>
								</tr>
								<?php endforeach?>
							</table>
							<?php endif;?>
						</div>
						<div id="answer2_<?php echo $key?>" style="cursor:pointer;display:none" onclick="javascript:$(this).hide()" >
							【答案】<br>
							<?php echo $question['answer_content']?><br>
							【解析】<br>
							<?php echo $question['analysis']?><br>
						</div>
					</p><br><br>
					<?php endforeach;?>
				<?php endif;?>

			</div>
			<div class="con">
				<div class="ctitle cBg_2">
					<div class="background intro">课后作业</div>
				</div>
				<?php if(!empty($heluInfo['lecture_info']['question_list']['work'])):?>
					<?php foreach ($heluInfo['lecture_info']['question_list']['work'] as $key=>$question):?>
					<p>
						<div onclick="javascript:$('#answer3_<?php echo $key?>').toggle()" style="cursor:pointer">
							<?php echo ($key+1)?>、<?php echo $question['content']?><br><br>
							<?php if(!empty($question['question_option'])):?>
							<table>
								<?php foreach ($question['question_option'] as $k=>$option):?>
									<tr>
										<td><?php echo $optionKeyArr[$k]?>.</td><td><?php echo $option['content']?></td>
									</tr>
								<?php endforeach?>
							</table>
							<?php endif;?>
						</div>
						<div id="answer3_<?php echo $key?>" style="cursor:pointer;display:none" onclick="javascript:$(this).hide()" >
							【答案】<br>
							<?php echo $question['answer_content']?><br>
							【解析】<br>
							<?php echo $question['analysis']?><br>
						</div>
					</p><br><br>
					<?php endforeach;?>
				<?php endif;?>

			</div>
		</div>
			<div class="bottomline">
					<div class="left"> </div>
					<div class="right"> </div>
			</div>
			<div class="oneline"><p>试卷第2页，总3页</p></div>
		</div>
	<!--讲义内容结束-->
	
</div>
<br><br><br><br>
<!--<div class="baogao-printer baogao-icon" onclick="window.print();"></div>-->
</body>
</html>