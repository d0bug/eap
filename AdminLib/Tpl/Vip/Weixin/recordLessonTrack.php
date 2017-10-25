<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0" />
<meta name="format-detection" content="telephone=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<title>高思教师系统</title>
<link href="/static/css/vip-weixin2.css" rel="stylesheet" />
<script src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.blockUI.js"></script>
<script src="/static/js/iscroll.js"></script>
<script src="/static/js/vip_wx.js"></script>
</head>
<body>
<header class="header">
	<h1>核录<?php echo $heluInfo['sstudentname']?>同学</h1>
	<div class="arr"></div>
</header>

<article class="wrap">
	<div class="heluInfo">
		<form action="">
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
			<h3 class="modTit">课时基本信息：</h3>
			<div class="modCon">
				<dl class="modInfo">
					<dt>学员姓名：</dt>
					<dd><?php echo $heluInfo['sstudentname']?></dd>
					<dt>上课时间：</dt>
					<dd><?php echo date('Y-m-d',strtotime($heluInfo['dtdatereal']))?> <?php echo date('H:i',strtotime($heluInfo['dtlessonbeginreal']))?>~<?php echo date('H:i',strtotime($heluInfo['dtlessonendreal']))?> </dd>
					<dt>课次：</dt>
					<dd><?php echo $heluInfo['nlessonno']?></dd>
				</dl>
			</div>
			
			<h3 class="modTit">课堂掌握情况：</h3>
		<?php if(empty($heluInfo['lecture_info'])):?>
			<div class="modCon">无法获取讲义内容~</div>
		<?php else:?>
			<div class="modCon">
				<div class="answerInfo">
					<div id="J_FixedTitle" class="answerFixedTitle">
						<div class="answerTitle">
							<span onclick="changeTab('0','JKDiv_',2)" class="JKDiv_0_btn on">例 题</span>
							<span onclick="changeTab('1','JKDiv_',2)" class="JKDiv_1_btn" >随堂练习</span>
							<span onclick="changeTab('2','JKDiv_',2)" class="JKDiv_2_btn">上次作业</span>										
						</div>
						<div class="answerTable">
							<div id="JKDiv_0_table"  class="JKDiv_table">
								<table width="100%" >
									<tr>
										<th width="12%">题序</th>
										<th width="22%"><label for="QQ-1"><input type="checkbox" id="checkAll_module_0_f" name="checkAll_module_0_f" value="" > 对</label></th>
										<th width="22%"><label for="QQ-2"><input type="checkbox" id="checkAll_module_1_f" name="checkAll_module_1_f" value="" > 错</label></th>
										<th width="22%"><label for="QQ-3"><input type="checkbox" id="checkAll_module_2_f" name="checkAll_module_2_f" value="" > 部分对</label></th>
										<th width="22%"><label for="QQ-4"><input type="checkbox" id="checkAll_module_3_f" name="checkAll_module_3_f" value="" > 未用</label></th>
									</tr>
								</table>
							</div>
							<div id="JKDiv_1_table" style="display:none" class="JKDiv_table">
								<table width="100%" >
									<tr>
										<th width="12%">题序</th>
										<th width="22%"><label for="QQ2-1"><input type="checkbox" id="checkAll_practise_0_f" name="checkAll_practise_0_f" value="" > 对</label></th>
										<th width="22%"><label for="QQ2-2"><input type="checkbox" id="checkAll_practise_1_f" name="checkAll_practise_1_f" value="" > 错</label></th>
										<th width="22%"><label for="QQ2-3"><input type="checkbox" id="checkAll_practise_2_f" name="checkAll_practise_2_f" value="" > 部分对</label></th>
										<th width="22%"><label for="QQ2-4"><input type="checkbox" id="checkAll_practise_3_f" name="checkAll_practise_3_f" value="" > 未用</label></th>
									</tr>
								</table>
							</div>
							<div id="JKDiv_2_table" style="display:none" class="JKDiv_table">
								<table width="100%" >
									<tr>
										<th width="12%">题序</th>
										<th width="22%"><label for="QQ3-1"><input type="checkbox" id="checkAll_lastwork_0_f" name="checkAll_lastwork_0_f" value="" > 对</label></th>
										<th width="22%"><label for="QQ3-2"><input type="checkbox" id="checkAll_lastwork_1_f" name="checkAll_lastwork_1_f" value="" > 错</label></th>
										<th width="22%"><label for="QQ3-3"><input type="checkbox" id="checkAll_lastwork_2_f" name="checkAll_lastwork_2_f" value="" > 部分对</label></th>
										<th width="22%"><label for="QQ3-4"><input type="checkbox" id="checkAll_lastwork_3_f" name="checkAll_lastwork_3_f" value="" > 未用</label></th>
									</tr>
								</table>
							</div>
						</div>
						
					</div>
					<div class="answerTitle">
						<span onclick="changeTab('0','JKDiv_',2)" class="JKDiv_0_btn on">例 题</span>
						<span onclick="changeTab('1','JKDiv_',2)" class="JKDiv_1_btn" >随堂练习</span>
						<span onclick="changeTab('2','JKDiv_',2)" class="JKDiv_2_btn">上次作业</span>				
					</div>
					<div id="JKDiv_0" style="display: block;">
						<table width="100%">
							<tbody>
						<?php if(!empty($heluInfo['lecture_info']['question_list']['module'])):?>
							<tr>
								<th width="12%">题序</th>
								<th width="22%"><label for="QQ-1"><input type="checkbox" id="checkAll_module_0" name="checkAll_module_0" value="" > 对</label></th>
								<th width="22%"><label for="QQ-2"><input type="checkbox" id="checkAll_module_1" name="checkAll_module_1" value="" > 错</label></th>
								<th width="22%"><label for="QQ-3"><input type="checkbox" id="checkAll_module_2" name="checkAll_module_2" value="" > 部分对</label></th>
								<th width="22%"><label for="QQ-4"><input type="checkbox" id="checkAll_module_3" name="checkAll_module_3" value="" > 未用</label></th>
							</tr>
						<?php foreach ($heluInfo['lecture_info']['question_list']['module_question'] as $key=>$question):?>
							<tr class="<?php if($key%2==0):?>odd<?php else:?>even<?php endif;?>">
								<td ><?php echo ($key+1)?></td>
								<td ><label for="Q<?php echo ($key+1)?>-1"><input type="radio" name="module_<?php echo $key?>_[]" id="module_<?php echo $key?>_0" value="2" <?php if($heluInfo['module_answer'][$key] === '2'):?>checked<?php endif;?> ></td>
								<td ><label for="Q<?php echo ($key+1)?>-2"><input type="radio" name="module_<?php echo $key?>_[]" id="module_<?php echo $key?>_1" value="0" <?php if($heluInfo['module_answer'][$key] === '0'):?>checked<?php endif;?>></label></td>
								<td ><label for="Q<?php echo ($key+1)?>-3"><input type="radio" name="module_<?php echo $key?>_[]" id="module_<?php echo $key?>_2" value="1" <?php if($heluInfo['module_answer'][$key] === '1'):?>checked<?php endif;?>></label></td>
								<td ><label for="Q<?php echo ($key+1)?>-4"><input type="radio" name="module_<?php echo $key?>_[]" id="module_<?php echo $key?>_3" value="-1" <?php if($heluInfo['module_answer'][$key] === '-1'):?>checked<?php endif;?>></label></td>
							</tr>
						<?php endforeach;?>
					<?php endif;?>	
						<input type="hidden" id="module_count" name="module_count" value="<?php echo count($heluInfo['lecture_info']['question_list']['module_question'])?>">
															
						</tbody></table>
					</div>
					<div style="display: none;" id="JKDiv_1">
						<table width="100%">
							<tbody>
						<?php if(!empty($heluInfo['lecture_info']['question_list']['practise'])):?>
							<tr>
								<th width="15%">题序</th>
								<th width="20%"><label for="QQ2-1"><input type="checkbox" id="checkAll_practise_0" name="checkAll_practise_0" value="" > 对</label></th>
								<th width="20%"><label for="QQ2-2"><input type="checkbox" id="checkAll_practise_1" name="checkAll_practise_1" value="" > 错</label></th>
								<th width="25%"><label for="QQ2-3"><input type="checkbox" id="checkAll_practise_2" name="checkAll_practise_2" value="" > 部分对</label></th>
								<th width="20%"><label for="QQ2-4"><input type="checkbox" id="checkAll_practise_3" name="checkAll_practise_3" value="" > 未用</label></th>
							</tr>
							<?php foreach ($heluInfo['lecture_info']['question_list']['practise'] as $key=>$question):?>
							<tr class="<?php if($key%2==0):?>odd<?php else:?>even<?php endif;?>">
								<td ><?php echo ($key+1)?></td>
								<td ><label for="Q2<?php echo ($key+1)?>-1"><input type="radio" name="practise_<?php echo $key?>_[]" id="practise_<?php echo $key?>_0" value="2" <?php if($heluInfo['practise_answer'][$key] === '2'):?>checked<?php endif;?> ></label></td>
								<td ><label for="Q2<?php echo ($key+1)?>-2"><input type="radio" name="practise_<?php echo $key?>_[]" id="practise_<?php echo $key?>_1" value="0" <?php if($heluInfo['practise_answer'][$key] === '0'):?>checked<?php endif;?> ></label></td>
								<td ><label for="Q2<?php echo ($key+1)?>-3"><input type="radio" name="practise_<?php echo $key?>_[]" id="practise_<?php echo $key?>_2" value="1" <?php if($heluInfo['practise_answer'][$key] === '1'):?>checked<?php endif;?> ></label></td>
								<td ><label for="Q2<?php echo ($key+1)?>-4"><input type="radio" name="practise_<?php echo $key?>_[]" id="practise_<?php echo $key?>_3" value="-1" <?php if($heluInfo['practise_answer'][$key] === '-1'):?>checked<?php endif;?> ></label></td>
							</tr>
							<?php endforeach;?>
						<?php endif;?>
						<input type="hidden" id="practise_count" name="practise_count" value="<?php echo count($heluInfo['lecture_info']['question_list']['practise'])?>">	
							
						</tbody></table>
					</div>
					<div style="display: none;" id="JKDiv_2">
						<table width="100%">
							<tbody>
						<?php if(!empty($last_lesson_heluInfo['lecture_info']['question_list']['work'])):?>
							<tr>
								<th width="15%">题序</th>
								<th width="20%"><label for="QQ3-1"><input type="checkbox" id="checkAll_lastwork_0" name="checkAll_lastwork_0" value="" > 对</label></th>
								<th width="20%"><label for="QQ3-2"><input type="checkbox" id="checkAll_lastwork_1" name="checkAll_lastwork_1" value="" > 错</label></th>
								<th width="25%"><label for="QQ3-3"><input type="checkbox" id="checkAll_lastwork_2" name="checkAll_lastwork_2" value="" > 部分对</label></th>
								<th width="20%"><label for="QQ3-4"><input type="checkbox" id="checkAll_lastwork_3" name="checkAll_lastwork_3" value="" > 未用</label></th>
							</tr>
							<?php foreach ($last_lesson_heluInfo['lecture_info']['question_list']['work'] as $key=>$question):?>
							<tr class="<?php if($key%2==0):?>odd<?php else:?>even<?php endif;?>">
								<td ><?php echo ($key+1)?></td>
								<td ><label for="Q3<?php echo ($key+1)?>-1"><input type="radio" name="lastwork_<?php echo $key?>_[]" id="lastwork_<?php echo $key?>_0" value="2" <?php if($last_lesson_heluInfo['work_answer'][$key] === '2'):?>checked<?php endif;?> ></label></td>
								<td ><label for="Q3<?php echo ($key+1)?>-2"><input type="radio" name="lastwork_<?php echo $key?>_[]" id="lastwork_<?php echo $key?>_1" value="0" <?php if($last_lesson_heluInfo['work_answer'][$key] === '0'):?>checked<?php endif;?> ></label></td>
								<td ><label for="Q3<?php echo ($key+1)?>-3"><input type="radio" name="lastwork_<?php echo $key?>_[]" id="lastwork_<?php echo $key?>_2" value="1" <?php if($last_lesson_heluInfo['work_answer'][$key] === '1'):?>checked<?php endif;?> ></label></td>
								<td ><label for="Q3<?php echo ($key+1)?>-4"><input type="radio" name="lastwork_<?php echo $key?>_[]" id="lastwork_<?php echo $key?>_3" value="-1" <?php if($last_lesson_heluInfo['work_answer'][$key] === '-1'):?>checked<?php endif;?> ></label></td>
							</tr>
							<?php endforeach;?>	
						<?php endif;?>
						<input type="hidden" id="lastwork_count" name="lastwork_count" value="<?php echo count($last_lesson_heluInfo['lecture_info']['question_list']['work'])?>">									
						</tbody></table>
					</div>
				</div>	
				
			</div>
			<div class="button">
				<a href="javascript:void(0)" class="btn center" onclick="return save_form1('<?php echo U('Vip/Weixin/savePartOne')?>')">保存答题情况</a>				
			</div>
		<?php endif;?>	
		</form>
	</div>
</article>

<script type="text/javascript">
function changeTab(divId,divName,zDivCount){ 
		for(i=0;i<=zDivCount;i++){
			document.getElementById(divName+i).style.display="none";
		}
		if (divId=="0") {
			$(".JKDiv_1_btn,.JKDiv_2_btn").removeClass("on");
			$(".JKDiv_0_btn").addClass("on");
			$(".JKDiv_table").css("display","none");
			$("#JKDiv_0_table").css("display","block");				
		};
		if (divId=="1") {
			$(".JKDiv_0_btn,.JKDiv_2_btn").removeClass("on");
			$(".JKDiv_1_btn").addClass("on");
			$(".JKDiv_table").css("display","none");
			$("#JKDiv_1_table").css("display","block");
		};
		if (divId=="2") {
			$(".JKDiv_1_btn,.JKDiv_0_btn").removeClass("on");
			$(".JKDiv_2_btn").addClass("on");
			$(".JKDiv_table").css("display","none");
			$("#JKDiv_2_table").css("display","block");
		};
		document.getElementById(divName+divId).style.display="block"; 
		//显示当前层 
	}

$(window).scroll( function() {
	if($(window).scrollTop()>350){
		$("#J_FixedTitle").addClass("fixedTitle");
	}else{
		$("#J_FixedTitle").removeClass("fixedTitle");
	}
});

$(function () {
		var module_count = $('#module_count').val();
		var practise_count = $('#practise_count').val();
		var lastwork_count = $('#lastwork_count').val();
		
		$("#checkAll_module_0").click(function () {
			if(this.checked){
				$('#checkAll_module_0').attr("checked",true);
				$('#checkAll_module_0_f').attr("checked",true);
				for(var i=0; i< module_count; i++){
					$('#module_'+i+'_0').attr("checked",true);
				}
				$('#checkAll_module_1').attr("checked",false);
				$('#checkAll_module_2').attr("checked",false);
				$('#checkAll_module_3').attr("checked",false);
				$('#checkAll_module_1_f').attr("checked",false);
				$('#checkAll_module_2_f').attr("checked",false);
				$('#checkAll_module_3_f').attr("checked",false);
			}else{
				$('#checkAll_module_0').attr("checked",false);
				$('#checkAll_module_0_f').attr("checked",false);
				for(var i=0; i< module_count; i++){
					$('#module_'+i+'_0').attr("checked",false);
				}
			}
		});
		$("#checkAll_module_0_f").click(function () {
			if(this.checked){
				$('#checkAll_module_0').attr("checked",true);
				$('#checkAll_module_0_f').attr("checked",true);
				for(var i=0; i< module_count; i++){
					$('#module_'+i+'_0').attr("checked",true);
				}
				$('#checkAll_module_1').attr("checked",false);
				$('#checkAll_module_2').attr("checked",false);
				$('#checkAll_module_3').attr("checked",false);
				$('#checkAll_module_1_f').attr("checked",false);
				$('#checkAll_module_2_f').attr("checked",false);
				$('#checkAll_module_3_f').attr("checked",false);
			}else{
				$('#checkAll_module_0').attr("checked",false);
				$('#checkAll_module_0_f').attr("checked",false);
				for(var i=0; i< module_count; i++){
					$('#module_'+i+'_0').attr("checked",false);
				}
			}
		});
		$("#checkAll_module_1").click(function () {
			if(this.checked){
				$('#checkAll_module_1').attr("checked",true);
				$('#checkAll_module_1_f').attr("checked",true);
				for(var i=0; i< module_count; i++){
					$('#module_'+i+'_1').attr("checked",true);
				}
				$('#checkAll_module_0').attr("checked",false);
				$('#checkAll_module_2').attr("checked",false);
				$('#checkAll_module_3').attr("checked",false);
				$('#checkAll_module_0_f').attr("checked",false);
				$('#checkAll_module_2_f').attr("checked",false);
				$('#checkAll_module_3_f').attr("checked",false);
			}else{
				$('#checkAll_module_1').attr("checked",false);
				$('#checkAll_module_1_f').attr("checked",false);
				for(var i=0; i< module_count; i++){
					$('#module_'+i+'_1').attr("checked",false);
				}
			}
		});
		$("#checkAll_module_1_f").click(function () {
			if(this.checked){
				$('#checkAll_module_1').attr("checked",true);
				$('#checkAll_module_1_f').attr("checked",true);
				for(var i=0; i< module_count; i++){
					$('#module_'+i+'_1').attr("checked",true);
				}
				$('#checkAll_module_0').attr("checked",false);
				$('#checkAll_module_2').attr("checked",false);
				$('#checkAll_module_3').attr("checked",false);
				$('#checkAll_module_0_f').attr("checked",false);
				$('#checkAll_module_2_f').attr("checked",false);
				$('#checkAll_module_3_f').attr("checked",false);
			}else{
				$('#checkAll_module_1').attr("checked",false);
				$('#checkAll_module_1_f').attr("checked",false);
				for(var i=0; i< module_count; i++){
					$('#module_'+i+'_1').attr("checked",false);
				}
			}
		});
		$("#checkAll_module_2").click(function () {
			if(this.checked){
				$('#checkAll_module_2').attr("checked",true);
				$('#checkAll_module_2_f').attr("checked",true);
				for(var i=0; i< module_count; i++){
					$('#module_'+i+'_2').attr("checked",true);
				}
				$('#checkAll_module_0').attr("checked",false);
				$('#checkAll_module_1').attr("checked",false);
				$('#checkAll_module_3').attr("checked",false);
				$('#checkAll_module_0_f').attr("checked",false);
				$('#checkAll_module_1_f').attr("checked",false);
				$('#checkAll_module_3_f').attr("checked",false);
			}else{
				$('#checkAll_module_2').attr("checked",false);
				$('#checkAll_module_2_f').attr("checked",false);
				for(var i=0; i< module_count; i++){
					$('#module_'+i+'_2').attr("checked",false);
				}
			}
		});
		$("#checkAll_module_2_f").click(function () {
			if(this.checked){
				$('#checkAll_module_2').attr("checked",true);
				$('#checkAll_module_2_f').attr("checked",true);
				for(var i=0; i< module_count; i++){
					$('#module_'+i+'_2').attr("checked",true);
				}
				$('#checkAll_module_0').attr("checked",false);
				$('#checkAll_module_1').attr("checked",false);
				$('#checkAll_module_3').attr("checked",false);
				$('#checkAll_module_0_f').attr("checked",false);
				$('#checkAll_module_1_f').attr("checked",false);
				$('#checkAll_module_3_f').attr("checked",false);
			}else{
				$('#checkAll_module_2').attr("checked",false);
				$('#checkAll_module_2_f').attr("checked",false);
				for(var i=0; i< module_count; i++){
					$('#module_'+i+'_2').attr("checked",false);
				}
			}
		});
		$("#checkAll_module_3").click(function () {
			if(this.checked){
				$('#checkAll_module_3').attr("checked",true);
				$('#checkAll_module_3_f').attr("checked",true);
				for(var i=0; i< module_count; i++){
					$('#module_'+i+'_3').attr("checked",true);
				}
				$('#checkAll_module_0').attr("checked",false);
				$('#checkAll_module_1').attr("checked",false);
				$('#checkAll_module_2').attr("checked",false);
				$('#checkAll_module_0_f').attr("checked",false);
				$('#checkAll_module_1_f').attr("checked",false);
				$('#checkAll_module_2_f').attr("checked",false);
			}else{
				$('#checkAll_module_3').attr("checked",false);
				$('#checkAll_module_3_f').attr("checked",false);
				for(var i=0; i< module_count; i++){
					$('#module_'+i+'_3').attr("checked",false);
				}
			}
		});
		
		$("#checkAll_module_3_f").click(function () {
			if(this.checked){
				$('#checkAll_module_3').attr("checked",true);
				$('#checkAll_module_3_f').attr("checked",true);
				for(var i=0; i< module_count; i++){
					$('#module_'+i+'_3').attr("checked",true);
				}
				$('#checkAll_module_0').attr("checked",false);
				$('#checkAll_module_1').attr("checked",false);
				$('#checkAll_module_2').attr("checked",false);
				$('#checkAll_module_0_f').attr("checked",false);
				$('#checkAll_module_1_f').attr("checked",false);
				$('#checkAll_module_2_f').attr("checked",false);
			}else{
				$('#checkAll_module_3').attr("checked",false);
				$('#checkAll_module_3_f').attr("checked",false);
				for(var i=0; i< module_count; i++){
					$('#module_'+i+'_3').attr("checked",false);
				}
			}
		});

		$("#checkAll_practise_0").click(function () {
			if(this.checked){
				$('#checkAll_practise_0').attr("checked",true);
				$('#checkAll_practise_0_f').attr("checked",true);
				for(var i=0; i< practise_count; i++){
					$('#practise_'+i+'_0').attr("checked",true);
				}
				$('#checkAll_practise_1').attr("checked",false);
				$('#checkAll_practise_2').attr("checked",false);
				$('#checkAll_practise_3').attr("checked",false);
				$('#checkAll_practise_1_f').attr("checked",false);
				$('#checkAll_practise_2_f').attr("checked",false);
				$('#checkAll_practise_3_f').attr("checked",false);
			}else{
				$('#checkAll_practise_0').attr("checked",false);
				$('#checkAll_practise_0_f').attr("checked",false);
				for(var i=0; i< practise_count; i++){
					$('#practise_'+i+'_0').attr("checked",false);
				}
			}
		});
		$("#checkAll_practise_0_f").click(function () {
			if(this.checked){
				$('#checkAll_practise_0').attr("checked",true);
				$('#checkAll_practise_0_f').attr("checked",true);
				for(var i=0; i< practise_count; i++){
					$('#practise_'+i+'_0').attr("checked",true);
				}
				$('#checkAll_practise_1').attr("checked",false);
				$('#checkAll_practise_2').attr("checked",false);
				$('#checkAll_practise_3').attr("checked",false);
				$('#checkAll_practise_1_f').attr("checked",false);
				$('#checkAll_practise_2_f').attr("checked",false);
				$('#checkAll_practise_3_f').attr("checked",false);
			}else{
				$('#checkAll_practise_0').attr("checked",false);
				$('#checkAll_practise_0_f').attr("checked",false);
				for(var i=0; i< practise_count; i++){
					$('#practise_'+i+'_0').attr("checked",false);
				}
			}
		});
		$("#checkAll_practise_1").click(function () {
			if(this.checked){
				$('#checkAll_practise_1').attr("checked",true);
				$('#checkAll_practise_1_f').attr("checked",true);
				for(var i=0; i< practise_count; i++){
					$('#practise_'+i+'_1').attr("checked",true);
				}
				$('#checkAll_practise_0').attr("checked",false);
				$('#checkAll_practise_2').attr("checked",false);
				$('#checkAll_practise_3').attr("checked",false);
				$('#checkAll_practise_0_f').attr("checked",false);
				$('#checkAll_practise_2_f').attr("checked",false);
				$('#checkAll_practise_3_f').attr("checked",false);
			}else{
				$('#checkAll_practise_1').attr("checked",false);
				$('#checkAll_practise_1_f').attr("checked",false);
				for(var i=0; i< practise_count; i++){
					$('#practise_'+i+'_1').attr("checked",false);
				}
			}
		});
		$("#checkAll_practise_1_f").click(function () {
			if(this.checked){
				$('#checkAll_practise_1').attr("checked",true);
				$('#checkAll_practise_1_f').attr("checked",true);
				for(var i=0; i< practise_count; i++){
					$('#practise_'+i+'_1').attr("checked",true);
				}
				$('#checkAll_practise_0').attr("checked",false);
				$('#checkAll_practise_2').attr("checked",false);
				$('#checkAll_practise_3').attr("checked",false);
				$('#checkAll_practise_0_f').attr("checked",false);
				$('#checkAll_practise_2_f').attr("checked",false);
				$('#checkAll_practise_3_f').attr("checked",false);
			}else{
				$('#checkAll_practise_1').attr("checked",false);
				$('#checkAll_practise_1_f').attr("checked",false);
				for(var i=0; i< practise_count; i++){
					$('#practise_'+i+'_1').attr("checked",false);
				}
			}
		});
		$("#checkAll_practise_2").click(function () {
			if(this.checked){
				$('#checkAll_practise_2').attr("checked",true);
				$('#checkAll_practise_2_f').attr("checked",true);
				for(var i=0; i< practise_count; i++){
					$('#practise_'+i+'_2').attr("checked",true);
				}
				$('#checkAll_practise_0').attr("checked",false);
				$('#checkAll_practise_1').attr("checked",false);
				$('#checkAll_practise_3').attr("checked",false);
				$('#checkAll_practise_0_f').attr("checked",false);
				$('#checkAll_practise_1_f').attr("checked",false);
				$('#checkAll_practise_3_f').attr("checked",false);
			}else{
				$('#checkAll_practise_2').attr("checked",false);
				$('#checkAll_practise_2_f').attr("checked",false);
				for(var i=0; i< practise_count; i++){
					$('#practise_'+i+'_2').attr("checked",false);
				}
			}
		});
		$("#checkAll_practise_2_f").click(function () {
			if(this.checked){
				$('#checkAll_practise_2').attr("checked",true);
				$('#checkAll_practise_2_f').attr("checked",true);
				for(var i=0; i< practise_count; i++){
					$('#practise_'+i+'_2').attr("checked",true);
				}
				$('#checkAll_practise_0').attr("checked",false);
				$('#checkAll_practise_1').attr("checked",false);
				$('#checkAll_practise_3').attr("checked",false);
				$('#checkAll_practise_0_f').attr("checked",false);
				$('#checkAll_practise_1_f').attr("checked",false);
				$('#checkAll_practise_3_f').attr("checked",false);
			}else{
				$('#checkAll_practise_2').attr("checked",false);
				$('#checkAll_practise_2_f').attr("checked",false);
				for(var i=0; i< practise_count; i++){
					$('#practise_'+i+'_2').attr("checked",false);
				}
			}
		});
		$("#checkAll_practise_3").click(function () {
			if(this.checked){
				$('#checkAll_practise_3').attr("checked",true);
				$('#checkAll_practise_3_f').attr("checked",true);
				for(var i=0; i< practise_count; i++){
					$('#practise_'+i+'_3').attr("checked",true);
				}
				$('#checkAll_practise_0').attr("checked",false);
				$('#checkAll_practise_1').attr("checked",false);
				$('#checkAll_practise_2').attr("checked",false);
				$('#checkAll_practise_0_f').attr("checked",false);
				$('#checkAll_practise_1_f').attr("checked",false);
				$('#checkAll_practise_2_f').attr("checked",false);
			}else{
				$('#checkAll_practise_3').attr("checked",false);
				$('#checkAll_practise_3_f').attr("checked",false);
				for(var i=0; i< practise_count; i++){
					$('#practise_'+i+'_3').attr("checked",false);
				}
			}
		});
		$("#checkAll_practise_3_f").click(function () {
			if(this.checked){
				$('#checkAll_practise_3').attr("checked",true);
				$('#checkAll_practise_3_f').attr("checked",true);
				for(var i=0; i< practise_count; i++){
					$('#practise_'+i+'_3').attr("checked",true);
				}
				$('#checkAll_practise_0').attr("checked",false);
				$('#checkAll_practise_1').attr("checked",false);
				$('#checkAll_practise_2').attr("checked",false);
				$('#checkAll_practise_0_f').attr("checked",false);
				$('#checkAll_practise_1_f').attr("checked",false);
				$('#checkAll_practise_2_f').attr("checked",false);
			}else{
				$('#checkAll_practise_3').attr("checked",false);
				$('#checkAll_practise_3_f').attr("checked",false);
				for(var i=0; i< practise_count; i++){
					$('#practise_'+i+'_3').attr("checked",false);
				}
			}
		});

		$("#checkAll_lastwork_0").click(function () {
			if(this.checked){
				$('#checkAll_lastwork_0').attr("checked",true);
				$('#checkAll_lastwork_0_f').attr("checked",true);
				for(var i=0; i< lastwork_count; i++){
					$('#lastwork_'+i+'_0').attr("checked",true);
				}
				$('#checkAll_lastwork_1').attr("checked",false);
				$('#checkAll_lastwork_2').attr("checked",false);
				$('#checkAll_lastwork_3').attr("checked",false);
				$('#checkAll_lastwork_1_f').attr("checked",false);
				$('#checkAll_lastwork_2_f').attr("checked",false);
				$('#checkAll_lastwork_3_f').attr("checked",false);
			}else{
				$('#checkAll_lastwork_0').attr("checked",false);
				$('#checkAll_lastwork_0_f').attr("checked",false);
				for(var i=0; i< lastwork_count; i++){
					$('#lastwork_'+i+'_0').attr("checked",false);
				}
			}
		});
		$("#checkAll_lastwork_0_f").click(function () {
			if(this.checked){
				$('#checkAll_lastwork_0').attr("checked",true);
				$('#checkAll_lastwork_0_f').attr("checked",true);
				for(var i=0; i< lastwork_count; i++){
					$('#lastwork_'+i+'_0').attr("checked",true);
				}
				$('#checkAll_lastwork_1').attr("checked",false);
				$('#checkAll_lastwork_2').attr("checked",false);
				$('#checkAll_lastwork_3').attr("checked",false);
				$('#checkAll_lastwork_1_f').attr("checked",false);
				$('#checkAll_lastwork_2_f').attr("checked",false);
				$('#checkAll_lastwork_3_f').attr("checked",false);
			}else{
				$('#checkAll_lastwork_0').attr("checked",false);
				$('#checkAll_lastwork_0_f').attr("checked",false);
				for(var i=0; i< lastwork_count; i++){
					$('#lastwork_'+i+'_0').attr("checked",false);
				}
			}
		});
		$("#checkAll_lastwork_1").click(function () {
			if(this.checked){
				$('#checkAll_lastwork_1').attr("checked",true);
				$('#checkAll_lastwork_1_f').attr("checked",true);
				for(var i=0; i< lastwork_count; i++){
					$('#lastwork_'+i+'_1').attr("checked",true);
				}
				$('#checkAll_lastwork_0').attr("checked",false);
				$('#checkAll_lastwork_2').attr("checked",false);
				$('#checkAll_lastwork_3').attr("checked",false);
				$('#checkAll_lastwork_0_f').attr("checked",false);
				$('#checkAll_lastwork_2_f').attr("checked",false);
				$('#checkAll_lastwork_3_f').attr("checked",false);
			}else{
				$('#checkAll_lastwork_1').attr("checked",false);
				$('#checkAll_lastwork_1_f').attr("checked",false);
				for(var i=0; i< lastwork_count; i++){
					$('#lastwork_'+i+'_1').attr("checked",false);
				}
			}
		});
		$("#checkAll_lastwork_1_f").click(function () {
			if(this.checked){
				$('#checkAll_lastwork_1').attr("checked",true);
				$('#checkAll_lastwork_1_f').attr("checked",true);
				for(var i=0; i< lastwork_count; i++){
					$('#lastwork_'+i+'_1').attr("checked",true);
				}
				$('#checkAll_lastwork_0').attr("checked",false);
				$('#checkAll_lastwork_2').attr("checked",false);
				$('#checkAll_lastwork_3').attr("checked",false);
				$('#checkAll_lastwork_0_f').attr("checked",false);
				$('#checkAll_lastwork_2_f').attr("checked",false);
				$('#checkAll_lastwork_3_f').attr("checked",false);
			}else{
				$('#checkAll_lastwork_1').attr("checked",false);
				$('#checkAll_lastwork_1_f').attr("checked",false);
				for(var i=0; i< lastwork_count; i++){
					$('#lastwork_'+i+'_1').attr("checked",false);
				}
			}
		});
		$("#checkAll_lastwork_2").click(function () {
			if(this.checked){
				$('#checkAll_lastwork_2').attr("checked",true);
				$('#checkAll_lastwork_2_f').attr("checked",true);
				for(var i=0; i< lastwork_count; i++){
					$('#lastwork_'+i+'_2').attr("checked",true);
				}
				$('#checkAll_lastwork_0').attr("checked",false);
				$('#checkAll_lastwork_1').attr("checked",false);
				$('#checkAll_lastwork_3').attr("checked",false);
				$('#checkAll_lastwork_0_f').attr("checked",false);
				$('#checkAll_lastwork_1_f').attr("checked",false);
				$('#checkAll_lastwork_3_f').attr("checked",false);
			}else{
				$('#checkAll_lastwork_2').attr("checked",false);
				$('#checkAll_lastwork_2_f').attr("checked",false);
				for(var i=0; i< lastwork_count; i++){
					$('#lastwork_'+i+'_2').attr("checked",false);
				}
			}
		});
		$("#checkAll_lastwork_2_f").click(function () {
			if(this.checked){
				$('#checkAll_lastwork_2').attr("checked",true);
				$('#checkAll_lastwork_2_f').attr("checked",true);
				for(var i=0; i< lastwork_count; i++){
					$('#lastwork_'+i+'_2').attr("checked",true);
				}
				$('#checkAll_lastwork_0').attr("checked",false);
				$('#checkAll_lastwork_1').attr("checked",false);
				$('#checkAll_lastwork_3').attr("checked",false);
				$('#checkAll_lastwork_0_f').attr("checked",false);
				$('#checkAll_lastwork_1_f').attr("checked",false);
				$('#checkAll_lastwork_3_f').attr("checked",false);
			}else{
				$('#checkAll_lastwork_2').attr("checked",false);
				$('#checkAll_lastwork_2_f').attr("checked",false);
				for(var i=0; i< lastwork_count; i++){
					$('#lastwork_'+i+'_2').attr("checked",false);
				}
			}
		});
		$("#checkAll_lastwork_3").click(function () {
			if(this.checked){
				$('#checkAll_lastwork_3').attr("checked",true);
				$('#checkAll_lastwork_3_f').attr("checked",true);
				for(var i=0; i< lastwork_count; i++){
					$('#lastwork_'+i+'_3').attr("checked",true);
				}
				$('#checkAll_lastwork_0').attr("checked",false);
				$('#checkAll_lastwork_1').attr("checked",false);
				$('#checkAll_lastwork_2').attr("checked",false);
				$('#checkAll_lastwork_0_f').attr("checked",false);
				$('#checkAll_lastwork_1_f').attr("checked",false);
				$('#checkAll_lastwork_2_f').attr("checked",false);
			}else{
				$('#checkAll_lastwork_3').attr("checked",false);
				$('#checkAll_lastwork_3_f').attr("checked",false);
				for(var i=0; i< lastwork_count; i++){
					$('#lastwork_'+i+'_3').attr("checked",false);
				}
			}
		});
		$("#checkAll_lastwork_3_f").click(function () {
			if(this.checked){
				$('#checkAll_lastwork_3').attr("checked",true);
				$('#checkAll_lastwork_3_f').attr("checked",true);
				for(var i=0; i< lastwork_count; i++){
					$('#lastwork_'+i+'_3').attr("checked",true);
				}
				$('#checkAll_lastwork_0').attr("checked",false);
				$('#checkAll_lastwork_1').attr("checked",false);
				$('#checkAll_lastwork_2').attr("checked",false);
				$('#checkAll_lastwork_0_f').attr("checked",false);
				$('#checkAll_lastwork_1_f').attr("checked",false);
				$('#checkAll_lastwork_2_f').attr("checked",false);
			}else{
				$('#checkAll_lastwork_3').attr("checked",false);
				$('#checkAll_lastwork_3_f').attr("checked",false);
				for(var i=0; i< lastwork_count; i++){
					$('#lastwork_'+i+'_3').attr("checked",false);
				}
			}
		});

	});
</script>

</body>
</html>