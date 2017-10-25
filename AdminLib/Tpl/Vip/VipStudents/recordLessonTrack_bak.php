<!DOCTYPE html>
<html>
<head>
	<title>记录上课轨迹</title>
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
.panel{width:610px; font-size: 14px;margin:0 auto;font-family: "微软雅黑";color: #333;background: url(images/M_YW_mack.jpg); }
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
.panel-right-con2 textarea{width: 286px;height:500px;border: 1px solid #ddd;padding: 6px;font-size: 12px;font-family: arial;line-height: 24px;}
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
.uploadify{width: 150px;line-height:36px;cursor: pointer; font-size: 16px;color: #fff; border-radius: 6px;background: #0aa4df;box-shadow: 0 4px 0 #1087b5;margin: 0 auto;text-align: center;position: relative;}
.uploadify:hover{background: #f90;box-shadow: 0 4px 0 #f60;}
.swfupload{left:0px}
.panel .minh800 .con2 img{max-width:500px}
</style>
<body>
<div class="handoutline"></div>
<div class="handoutshow">
	<div class="panel panel-left">
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
			<div class="con  con2">
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
					
					<p>
						<?php if($question_type['visible']==1):?><div><?php echo $numberKey[$num2]?>、<?php echo $question_type['title']?></div><?php endif;?>
						
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

	<div class="panel-right">
		<form id="form1" method="post" action="">
		<div class="panel-right-title">
			<P class="mb5">上课时间：<?php echo date('Y-m-d',strtotime($heluInfo['dtdatereal']))?> <?php echo date('H:i',strtotime($heluInfo['dtlessonbeginreal']))?>-<?php echo date('H:i',strtotime($heluInfo['dtlessonendreal']))?></P>
			<P>课次主题：<input type="text" class="input" id="lesson_topic" name="lesson_topic" value="<?php echo $heluInfo['lesson_topic']?>"/></P>
			<input type="hidden" id="act" name="act" value="<?php if(!empty($heluInfo['comment'])):?>update<?php else:?>add<?php endif;?>">
			<input type="hidden" id="helu_id" name="helu_id" value="<?php echo $heluInfo['helu_id']?>">
			<input type="hidden" id="student_code" name="student_code" value="<?php echo $heluInfo['sstudentcode']?>">
			<input type="hidden" id="student_name" name="student_name" value="<?php echo $heluInfo['sstudentname']?>">
			<input type="hidden" id="kecheng_code" name="kecheng_code" value="<?php echo $heluInfo['skechengcode']?>">
			<input type="hidden" id="kecheng_name" name="kecheng_name" value="<?php echo $heluInfo['skechengname']?>">
			<input type="hidden" id="lesson_no" name="lesson_no" value="<?php echo $heluInfo['nlessonno']?>">
			<input type="hidden" id="lesson_date" name="lesson_date" value="<?php echo $heluInfo['dtdatereal']?>">
			<input type="hidden" id="lesson_begin" name="lesson_begin" value="<?php echo date('H:i',strtotime($heluInfo['dtlessonbeginreal']))?>">
			<input type="hidden" id="lesson_end" name="lesson_end" value="<?php echo date('H:i',strtotime($heluInfo['dtlessonendreal']))?>">
			<input type="hidden" id="last_helu_id" name="last_helu_id" value="<?php echo $last_lesson_heluInfo['helu_id']?>">
		</div>


		<div class="panel-right-Qtitle">课堂掌握情况</div>
		<div class="panel-right-con1">
			<div class="panel-right-Q1title ">
				<span id="JKDiv_0_btn" class="on bltopbottom4"  onclick="changeTab('0','JKDiv_',2)">例 题</span>
				<span id="JKDiv_1_btn" onclick="changeTab('1','JKDiv_',2)">随堂练习</span>
				<span id="JKDiv_2_btn" class="brtopbottom4" onclick="changeTab('2','JKDiv_',2)">上次作业</span>
			</div>
			<div id="JKDiv_0">
				<table width="100%" >
					<?php if(!empty($heluInfo['lecture_info']['question_list']['module'])):?>
					<tr>
						<th  width="15%">题序</th>
						<th  width="20%"><label for="QQ-1"><input type="checkbox" id="checkAll_module_0" name="checkAll_module_0" value="" > 对</label></th>
						<th  width="20%"><label for="QQ-2"><input type="checkbox" id="checkAll_module_1" name="checkAll_module_1" value="" > 错</label></th>
						<th  width="25%"><label for="QQ-3"><input type="checkbox" id="checkAll_module_2" name="checkAll_module_2" value="" > 部分对</label></th>
						<th  width="20%"><label for="QQ-4"><input type="checkbox" id="checkAll_module_3" name="checkAll_module_3" value="" > 未用</label></th>
					</tr>
						<?php foreach ($heluInfo['lecture_info']['question_list']['module_question'] as $key=>$question):?>
								<tr class="<?php if($key%2==0):?>odd<?php else:?>even<?php endif;?>">
									<td ><?php echo ($key+1)?></td>
									<td ><label for="Q1-1"><input type="radio" name="module_<?php echo $key?>_[]" id="module_<?php echo $key?>_0" value="2" <?php if($heluInfo['module_answer'][$key] === '2'):?>checked<?php endif;?> ></td>
									<td ><label for="Q1-2"><input type="radio" name="module_<?php echo $key?>_[]" id="module_<?php echo $key?>_1" value="0" <?php if($heluInfo['module_answer'][$key] === '0'):?>checked<?php endif;?>></label></td>
									<td ><label for="Q1-3"><input type="radio" name="module_<?php echo $key?>_[]" id="module_<?php echo $key?>_2" value="1" <?php if($heluInfo['module_answer'][$key] === '1'):?>checked<?php endif;?>></label></td>
									<td ><label for="Q1-4"><input type="radio" name="module_<?php echo $key?>_[]" id="module_<?php echo $key?>_3" value="-1" <?php if($heluInfo['module_answer'][$key] === '-1'):?>checked<?php endif;?>></label></td>
								</tr>
								<?php $module_count++;?>
						<?php endforeach;?>
					<?php endif;?>	
					<input type="hidden" id="module_count" name="module_count" value="<?php echo $module_count?>">					
				</table>
			</div>
			<div id="JKDiv_1" style="display:none">
				<table width="100%" >
					<?php if(!empty($heluInfo['lecture_info']['question_list']['practise'])):?>
					<tr>
						<th  width="15%">题序</th>
						<th  width="20%"><label for="QQ2-1"><input type="checkbox" id="checkAll_practise_0" name="checkAll_practise_0" value="" > 对</label></th>
						<th  width="20%"><label for="QQ2-2"><input type="checkbox" id="checkAll_practise_1" name="checkAll_practise_1" value="" > 错</label></th>
						<th  width="25%"><label for="QQ2-3"><input type="checkbox" id="checkAll_practise_2" name="checkAll_practise_2" value="" > 部分对</label></th>
						<th  width="20%"><label for="QQ2-4"><input type="checkbox" id="checkAll_practise_3" name="checkAll_practise_3" value="" > 未用</label></th>
					</tr>
						<?php foreach ($heluInfo['lecture_info']['question_list']['practise'] as $key=>$question):?>
							<tr class="<?php if($key%2==0):?>odd<?php else:?>even<?php endif;?>">
								<td ><?php echo ($key+1)?></td>
								<td ><label for="Q21-1"><input type="radio" name="practise_<?php echo $key?>_[]" id="practise_<?php echo $key?>_0" value="2" <?php if($heluInfo['practise_answer'][$key] === '2'):?>checked<?php endif;?> ></label></td>
								<td ><label for="Q21-2"><input type="radio" name="practise_<?php echo $key?>_[]" id="practise_<?php echo $key?>_1" value="0" <?php if($heluInfo['practise_answer'][$key] === '0'):?>checked<?php endif;?> ></label></td>
								<td ><label for="Q21-3"><input type="radio" name="practise_<?php echo $key?>_[]" id="practise_<?php echo $key?>_2" value="1" <?php if($heluInfo['practise_answer'][$key] === '1'):?>checked<?php endif;?> ></label></td>
								<td ><label for="Q21-4"><input type="radio" name="practise_<?php echo $key?>_[]" id="practise_<?php echo $key?>_3" value="-1" <?php if($heluInfo['practise_answer'][$key] === '-1'):?>checked<?php endif;?> ></label></td>
							</tr>
							<?php $practise_count++?>
						<?php endforeach;?>
					<?php endif;?>
					<input type="hidden" id="practise_count" name="practise_count" value="<?php echo $practise_count?>">				
				</table>
			</div>
			
			<div id="JKDiv_2" style="display:none">
			
				<table width="100%" >
					
				<?php if(!empty($last_lesson_heluInfo['lecture_info']['question_list']['work'])):?>
					<tr>
						<th  width="15%">题序</th>
						<th  width="20%"><label for="QQ2-1"><input type="checkbox" id="checkAll_lastwork_0" name="checkAll_lastwork_0" value="" > 对</label></th>
						<th  width="20%"><label for="QQ2-2"><input type="checkbox" id="checkAll_lastwork_1" name="checkAll_lastwork_1" value="" > 错</label></th>
						<th  width="25%"><label for="QQ2-3"><input type="checkbox" id="checkAll_lastwork_2" name="checkAll_lastwork_2" value="" > 部分对</label></th>
						<th  width="20%"><label for="QQ2-4"><input type="checkbox" id="checkAll_lastwork_3" name="checkAll_lastwork_3" value="" > 未用</label></th>
					</tr>
					<?php foreach ($last_lesson_heluInfo['lecture_info']['question_list']['work'] as $key=>$question):?>
						<tr class="<?php if($key%2==0):?>odd<?php else:?>even<?php endif;?>">
							<td ><?php echo ($key+1)?></td>
							<td ><label for="Q31-1"><input type="radio" name="lastwork_<?php echo $key?>_[]" id="lastwork_<?php echo $key?>_0" value="2" <?php if($last_lesson_heluInfo['work_answer'][$key] === '2'):?>checked<?php endif;?> ></label></td>
							<td ><label for="Q31-2"><input type="radio" name="lastwork_<?php echo $key?>_[]" id="lastwork_<?php echo $key?>_1" value="0" <?php if($last_lesson_heluInfo['work_answer'][$key] === '0'):?>checked<?php endif;?> ></label></td>
							<td ><label for="Q31-3"><input type="radio" name="lastwork_<?php echo $key?>_[]" id="lastwork_<?php echo $key?>_2" value="1" <?php if($last_lesson_heluInfo['work_answer'][$key] === '1'):?>checked<?php endif;?> ></label></td>
							<td ><label for="Q31-4"><input type="radio" name="lastwork_<?php echo $key?>_[]" id="lastwork_<?php echo $key?>_3" value="-1" <?php if($last_lesson_heluInfo['work_answer'][$key] === '-1'):?>checked<?php endif;?> ></label></td>
						</tr>
						<?php $work_count++;?>
					<?php endforeach;?>	
				<?php endif;?>
					<input type="hidden" id="lastwork_count" name="lastwork_count" value="<?php echo $work_count?>">									
				</table>
			
			</div>
			
		</div>
		<div class="panel-right-bluebtn" onclick="return save_form1('<?php echo U('Vip/VipStudents/savePartOne')?>')">保 存</div>
		</form>


		<div class="panel-right-Qtitle">课堂评价</div>
		<form id="form2" name="form2" method="POST" >
			<div class="panel-right-con2">
				<ul>
					<?php if($heluInfo['dimension']):?>
						<?php foreach ($heluInfo['dimension'] as $key=>$dimension):?>
							<li><?php echo $dimension['title']?>：<span id="raty_<?php echo $dimension['id']?>" ></span>
								<input type="hidden" name="dimension_id[]" value="<?php echo $dimension['id']?>">
								<input type="hidden" name="dimension_title[]" value="<?php echo $dimension['title']?>">
							</li>
							<script type="text/javascript">
							var _initRaty<?php echo $dimension['id']?> = function() {
								$('#raty_<?php echo $dimension['id']?>').raty({
									<?php if($dimension['level']):?>score:<?php echo $dimension['level']?>,<?php endif;?>
									number: <?php echo count($levelArr)?> ,
									hints: [
									<?php foreach ($levelArr as $key=>$level):?>
									'<?php echo $level['title']?>'<?php if($key != count($levelArr)-1):?>,<?php endif;?>
									<?php endforeach;?>
									],
								});

							}
							$(function() {
								_initRaty<?php echo $dimension['id']?>();
							})
							</script>
						<?php endforeach;?>
					<?php endif;?>
					<input type="hidden" id="dimension_count" name="dimension_count" value="<?php echo count($heluInfo['dimension'])?>">
				</ul>
				<p class="saytitle"><span class="baogao-icon">老师说</span></p>
				<textarea id="comment" name="comment" ><?php echo date('Y/m/d',strtotime($heluInfo['lesson_date'])); ?> <?php echo $heluInfo['steachername'] ?>老师的课堂评价：<?php echo $heluInfo['comment']?></textarea>
				<div class="textnumber" id="textnumber">最少需输入<i>70</i>字</div>
				<div>
					<!--<?php if($heluInfo['is_send_sms']==0):?>
						<input type="checkbox" id="is_send_sms" name="is_send_sms" value="1" > 是否发送课评短信
					<?php else:?>
						<font color=red>课评短信只能发送一次，您已经发送过课评短信了，不能再次发送！</font>
					<?php endif;?>-->
					<input type="hidden" id="is_send_sms" name="is_send_sms" value="0" >
				</div><br>
			</div>
			<div class="panel-right-bluebtn" id="save_form2" onclick="return save_form2('<?php echo U('Vip/VipStudents/savePartTwo')?>')" data-clipboard-target="comment">保存并复制</div>
		</form>
		<br>
		<br>
<!-- 		<div class="panel-right-Qtitle">上传照片</div>
		<div class="panel-right-con3">
			<input type="hidden" id="record_count" name="record_count" value="<?php echo !empty($heluInfo['lesson_record_img'])?count($heluInfo['lesson_record_img']):0;?>">
			<input type="hidden" id="uploadrecord_url" name="uploadrecord_url" value="<?php echo U('Vip/VipStudents/uploadRecord')?>">
			<div class="panel-right-bluebtn" id="upload_lessonrecord">上传照片</div>
			<ul class="imglist" id="imglist">
				<?php if(!empty($heluInfo['lesson_record_img'])):?>
					<?php foreach ($heluInfo['lesson_record_img'] as $k=>$img):?>
						<li id="record_<?php echo $k?>">
							<a target="_blank" href="<?php echo str_replace('/Upload/','/upload/',$img)?>"> <?php echo $img?></a>
							<span onclick="del_recordimg('<?php echo $img?>','#record_<?php echo $k?>','<?php echo $heluInfo['helu_id']?>','<?php echo U('Vip/VipStudents/del_img')?>')">×</span>
						</li>
					<?php endforeach;?>
				<?php endif;?>
				
			</ul>
			<div class="imglisttext">请按照要求上传至少3张轨照，留存孩子学习轨迹！ </div>
			<input type="hidden" id="record_url" name="record_url" value="">
		</div> -->

		<div class="panel-right-redbtn" onclick="return createLessonReport('<?php echo $heluInfo['helu_id']?>','<?php echo U('Vip/VipStudents/createLessonReport')?>')">生成学习报告</div>

	</div>
	<!--讲义右侧内容结束-->
	<script type="text/javascript" src="/static/js/ZeroClipboard.js"></script>
	<script type="text/javascript">
	$(function () {
		var module_count = $('#module_count').val();
		var practise_count = $('#practise_count').val();
		var lastwork_count = $('#lastwork_count').val();

		$("#checkAll_module_0").click(function () {
			if(this.checked){
				$('#checkAll_module_0').attr("checked",true);
				for(var i=0; i< module_count; i++){
					$('#module_'+i+'_0').attr("checked",true);
				}
				$('#checkAll_module_1').attr("checked",false);
				$('#checkAll_module_2').attr("checked",false);
				$('#checkAll_module_3').attr("checked",false);
			}else{
				$('#checkAll_module_0').attr("checked",false);
				for(var i=0; i< module_count; i++){
					$('#module_'+i+'_0').attr("checked",false);
				}
			}
		});
		$("#checkAll_module_1").click(function () {
			if(this.checked){
				$('#checkAll_module_1').attr("checked",true);
				for(var i=0; i< module_count; i++){
					$('#module_'+i+'_1').attr("checked",true);
				}
				$('#checkAll_module_0').attr("checked",false);
				$('#checkAll_module_2').attr("checked",false);
				$('#checkAll_module_3').attr("checked",false);
			}else{
				$('#checkAll_module_1').attr("checked",false);
				for(var i=0; i< module_count; i++){
					$('#module_'+i+'_1').attr("checked",false);
				}
			}
		});
		$("#checkAll_module_2").click(function () {
			if(this.checked){
				$('#checkAll_module_2').attr("checked",true);
				for(var i=0; i< module_count; i++){
					$('#module_'+i+'_2').attr("checked",true);
				}
				$('#checkAll_module_0').attr("checked",false);
				$('#checkAll_module_1').attr("checked",false);
				$('#checkAll_module_3').attr("checked",false);
			}else{
				$('#checkAll_module_2').attr("checked",false);
				for(var i=0; i< module_count; i++){
					$('#module_'+i+'_2').attr("checked",false);
				}
			}
		});
		$("#checkAll_module_3").click(function () {
			if(this.checked){
				$('#checkAll_module_3').attr("checked",true);
				for(var i=0; i< module_count; i++){
					$('#module_'+i+'_3').attr("checked",true);
				}
				$('#checkAll_module_0').attr("checked",false);
				$('#checkAll_module_1').attr("checked",false);
				$('#checkAll_module_2').attr("checked",false);
			}else{
				$('#checkAll_module_3').attr("checked",false);
				for(var i=0; i< module_count; i++){
					$('#module_'+i+'_3').attr("checked",false);
				}
			}
		});

		$("#checkAll_practise_0").click(function () {
			if(this.checked){
				$('#checkAll_practise_0').attr("checked",true);
				for(var i=0; i< practise_count; i++){
					$('#practise_'+i+'_0').attr("checked",true);
				}
				$('#checkAll_practise_1').attr("checked",false);
				$('#checkAll_practise_2').attr("checked",false);
				$('#checkAll_practise_3').attr("checked",false);
			}else{
				$('#checkAll_practise_0').attr("checked",false);
				for(var i=0; i< practise_count; i++){
					$('#practise_'+i+'_0').attr("checked",false);
				}
			}
		});
		$("#checkAll_practise_1").click(function () {
			if(this.checked){
				$('#checkAll_practise_1').attr("checked",true);
				for(var i=0; i< practise_count; i++){
					$('#practise_'+i+'_1').attr("checked",true);
				}
				$('#checkAll_practise_0').attr("checked",false);
				$('#checkAll_practise_2').attr("checked",false);
				$('#checkAll_practise_3').attr("checked",false);
			}else{
				$('#checkAll_practise_1').attr("checked",false);
				for(var i=0; i< practise_count; i++){
					$('#practise_'+i+'_1').attr("checked",false);
				}
			}
		});
		$("#checkAll_practise_2").click(function () {
			if(this.checked){
				$('#checkAll_practise_2').attr("checked",true);
				for(var i=0; i< practise_count; i++){
					$('#practise_'+i+'_2').attr("checked",true);
				}
				$('#checkAll_practise_0').attr("checked",false);
				$('#checkAll_practise_1').attr("checked",false);
				$('#checkAll_practise_3').attr("checked",false);
			}else{
				$('#checkAll_practise_2').attr("checked",false);
				for(var i=0; i< practise_count; i++){
					$('#practise_'+i+'_2').attr("checked",false);
				}
			}
		});
		$("#checkAll_practise_3").click(function () {
			if(this.checked){
				$('#checkAll_practise_3').attr("checked",true);
				for(var i=0; i< practise_count; i++){
					$('#practise_'+i+'_3').attr("checked",true);
				}
				$('#checkAll_practise_0').attr("checked",false);
				$('#checkAll_practise_1').attr("checked",false);
				$('#checkAll_practise_2').attr("checked",false);
			}else{
				$('#checkAll_practise_3').attr("checked",false);
				for(var i=0; i< practise_count; i++){
					$('#practise_'+i+'_3').attr("checked",false);
				}
			}
		});

		$("#checkAll_lastwork_0").click(function () {
			if(this.checked){
				$('#checkAll_lastwork_0').attr("checked",true);
				for(var i=0; i< lastwork_count; i++){
					$('#lastwork_'+i+'_0').attr("checked",true);
				}
				$('#checkAll_lastwork_1').attr("checked",false);
				$('#checkAll_lastwork_2').attr("checked",false);
				$('#checkAll_lastwork_3').attr("checked",false);
			}else{
				$('#checkAll_lastwork_0').attr("checked",false);
				for(var i=0; i< lastwork_count; i++){
					$('#lastwork_'+i+'_0').attr("checked",false);
				}
			}
		});
		$("#checkAll_lastwork_1").click(function () {
			if(this.checked){
				$('#checkAll_lastwork_1').attr("checked",true);
				for(var i=0; i< lastwork_count; i++){
					$('#lastwork_'+i+'_1').attr("checked",true);
				}
				$('#checkAll_lastwork_0').attr("checked",false);
				$('#checkAll_lastwork_2').attr("checked",false);
				$('#checkAll_lastwork_3').attr("checked",false);
			}else{
				$('#checkAll_lastwork_1').attr("checked",false);
				for(var i=0; i< lastwork_count; i++){
					$('#lastwork_'+i+'_1').attr("checked",false);
				}
			}
		});
		$("#checkAll_lastwork_2").click(function () {
			if(this.checked){
				$('#checkAll_lastwork_2').attr("checked",true);
				for(var i=0; i< lastwork_count; i++){
					$('#lastwork_'+i+'_2').attr("checked",true);
				}
				$('#checkAll_lastwork_0').attr("checked",false);
				$('#checkAll_lastwork_1').attr("checked",false);
				$('#checkAll_lastwork_3').attr("checked",false);
			}else{
				$('#checkAll_lastwork_2').attr("checked",false);
				for(var i=0; i< lastwork_count; i++){
					$('#lastwork_'+i+'_2').attr("checked",false);
				}
			}
		});
		$("#checkAll_lastwork_3").click(function () {
			if(this.checked){
				$('#checkAll_lastwork_3').attr("checked",true);
				for(var i=0; i< lastwork_count; i++){
					$('#lastwork_'+i+'_3').attr("checked",true);
				}
				$('#checkAll_lastwork_0').attr("checked",false);
				$('#checkAll_lastwork_1').attr("checked",false);
				$('#checkAll_lastwork_2').attr("checked",false);
			}else{
				$('#checkAll_lastwork_3').attr("checked",false);
				for(var i=0; i< lastwork_count; i++){
					$('#lastwork_'+i+'_3').attr("checked",false);
				}
			}
		});


	});

	$(function() {
		// 定义一个新的复制对象
		var clip = new ZeroClipboard( document.getElementById("save_form2"), {
			moviePath: "/static/js/ZeroClipboard.swf"
		} );
		// 复制内容到剪贴板成功后的操作
		clip.on( 'complete', function(client, args) {
			alert("课堂评价内容复制成功");
			return save_form2('<?php echo U('Vip/VipStudents/savePartTwo')?>');
		} );


		var w = parseInt($("#record_count").val());
		$('#upload_lessonrecord').uploadify({
		'auto'     : true,
		'removeTimeout' : 1,
		'swf'      : '/static/js/uploadify.swf',
		'uploader' : $("#uploadrecord_url").val(),
		'method'   : 'post',
		'formData':{'helu_id':'<?php echo $heluInfo['helu_id']?>','width':'860','height':'485',type:'img',autocut:1},
		'buttonText' : '上传照片',
		'width':'150',
		'multi'    : true,
		'fileTypeDesc' : 'Image Files',
		'fileTypeExts' : '*.gif; *.jpg; *.png',
		'fileSizeLimit' : '3072KB',
		'onUploadSuccess':function(file,data,response){
			var obj = eval('(' + data + ')');
			$("#imglist").html($("#imglist").html()+"<li id=\"record_"+w+"\"><a target=\"_blank\" href=\"/upload"+obj.show_url+"\">"+obj.url+"</a><span onclick=\"del_recordimg('"+obj.url+"','#record_"+w+"','<?php echo $heluInfo['helu_id']?>','"+obj.delimg_url+"')\">×</span></li>");
			w = w + 1;
			$("#record_count").val(w);
		}
		});
	});


	function changeTab(divId,divName,zDivCount){
		for(i=0;i<=zDivCount;i++){
			document.getElementById(divName+i).style.display="none";
		}
		if (divId=="0") {
			document.getElementById("JKDiv_0_btn").className="on bltopbottom4";
			document.getElementById("JKDiv_1_btn").className="";
			document.getElementById("JKDiv_2_btn").className="brtopbottom5";
		};
		if (divId=="1") {
			document.getElementById("JKDiv_0_btn").className="bltopbottom4";
			document.getElementById("JKDiv_1_btn").className="on";
			document.getElementById("JKDiv_2_btn").className="brtopbottom5";
		};
		if (divId=="2") {
			document.getElementById("JKDiv_0_btn").className="bltopbottom4";
			document.getElementById("JKDiv_1_btn").className="";
			document.getElementById("JKDiv_2_btn").className="on brtopbottom5";
		};

		document.getElementById(divName+divId).style.display="block";
		//显示当前层
	}
	</script>

</div>
</body>
</html>