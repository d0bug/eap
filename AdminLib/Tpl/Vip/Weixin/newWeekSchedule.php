<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0" />
<meta name="format-detection" content="telephone=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<title>高思教师系统</title>
<link href="/static/css/vip-weixin.css" rel="stylesheet" />
<script src="/static/js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.blockUI.js"></script>
<script src="/static/js/iscroll.js"></script>
<script src="/static/js/DatePicker/WdatePicker.js"></script>
<script src="/static/js/vip_wx.js?1"></script>
</head>
<body>
<header class="header">
	<h1>我的课表</h1>
	<div class="arr"></div>
</header>
<article class="wrap">
	<ul class="modTab">
		<li class="current"><a href="<?php echo U('Vip/Weixin/newWeekSchedule',array('openid'=>$userInfo['openId']))?>">周课表</a><i></i></li>
		<li><a href="<?php echo U('Vip/Weixin/newMonthSchedule',array('openid'=>$userInfo['openId']))?>">月课表</a><i></i></li>
	</ul>
	<div class="courseHd">
		<label class="selbar">
		<?php if(!empty($selectData)):?>
			<select id="month" name="month" onchange="changeWeek(this.value)">
			<?php foreach($selectData as $key=>$data):?>
				<option value="<?php echo $data['start']?>_<?php echo $data['end']?>" <?php if($start==$data['start'] && $end==$data['end']):?>selected<?php endif;?>><?php echo $data['name']?></option>
			<?php endforeach?>
			</select>
		<?php endif;?>
			<div class="arr"><span></span></div>
			<div class="txt"></div>
		</label>
		<div class="addCourse">
			<a href="javascript:void(0)" onclick="addKecheng('<?php echo U('Vip/Weixin/getAllStudents',array('jieke'=>1))?>')">加课</a>
		</div>
	</div>
	<div class="courseList">
		<p style=" padding: 0 0.4rem; margin: 0.5rem 1.6rem 0 0;background: #9ed486; color: #fff;border-radius:0.2rem">1对1:<?=$oneToOne?>h，1对2:<?=$oneToTwo?>h，<?php if($groupClassMoney):?>小班课酬:<?=$groupClassMoney?>元<?php endif;?></p>
		<ul>
		<?php if(!empty($weekData)):?>
			<?php foreach($weekData as $key=>$data):?>	
			<li class="row">
				<h4><i></i><span></span><?php echo $data['name']?> <?php echo $data['weekStr']?><br></h4>
				<?php if(!empty($data['lesson'])):?>
					<?php foreach($data['lesson'] as $kk=>$lesson):?>
					<div class="stu <?php if($lesson['is_end']==1):?>finish<?php endif;?>"> 
						<span class="time"><?php echo $lesson['timeStr']?><br/><?php echo $lesson['sareaname']?></span>
						<a href="<?php echo U('Vip/Weixin/studentInfo',array('student_code'=>$lesson['sstudentcode'],'kecheng_code'=>$lesson['skechengcode'],'lesson'=>$lesson['nlessonno']));?>" class="name"><?php echo $lesson['sstudentname']?></a>
						<?php if(!empty($lesson['lesson_topic'])&&!empty($lesson['comment'])&&!empty($lesson['module_answer'])&&!empty($lesson['practise_answer'])&&!empty($lesson['dimension_level'])&&!empty($lesson['lesson_report_url_wx'])):?>
							<a href="javascript:void(0)" class="b b2">已核录</a>
						<?php else:?>
							<?php if($lesson['is_end']==1):?>
								<?php if($lesson['dtdatereal'] >= C('BIOCLOCK_START') && $lesson['nstatus']==2):?>
										<?php if($lesson['overdue']==0):?>
											<a href="<?php echo U('Vip/Weixin/recordLessonTrack',array('helu_id'=>$lesson['id']));?>" class="b b1">核录</a>
										<?php else:?>
											<a href="javascript:void(0)" onclick="do_overdue('<?php echo U('Vip/Weixin/doOverdue',array('helu_id'=>$lesson['id']))?>','#popWindow3','#title3','#error_msg3',<?php if(empty($lesson['is_overdue'])):?>1<?php else:?>0<?php endif;?>)" class="b b4" >逾期</a>
										<?php endif;?>
								<?php endif;?>
							<?php else:?>
								<a href="javascript:void(0)" onclick="adjustKecheng('<?php echo date('Y-m-d',strtotime($lesson['dtdatereal']))?>&nbsp;<?php echo $lesson['timeStr']?>','<?php echo $lesson['id']?>','<?php echo $lesson['sstudentname']?>','<?php echo $lesson['skechengname'].'('.$lesson['skechengcode'].')'?>')" class="b b3">调课</a>
							<?php endif;?>
						<?php endif;?>
					</div>
					<?php endforeach?>
				<?php endif;?>
			</li>
			<?php endforeach?>
		<?php endif;?>
		</ul>
	</div>
</article>

<!-- 弹出层 -->
<div id="popWindow">
	<div class="popCourse">
		<div class="popHd">加课</div>
		<div class="popBd">
				<ul>
					<li>
						<span class="alt">选择学员：</span>
						<label class="selbar">
							<select id="student_code" name="student_code" onchange="get_kechengList(this.value,'<?php echo $userInfo['sCode']?>','<?php echo U('Vip/Weixin/getKechengList')?>');">
							<option value="">请选择学员</option>
							</select>
						</label>
					</li>
					<li>
						<span class="alt">选择课程：</span>
						<label class="selbar">
							<select id="kecheng_code" name="kecheng_code">
								<option value="">请选择课程</option>
							</select>
						</label>
					</li>
					<li>
						<span class="alt">上课时间：</span>
						<label class="selbar">
							<select id="date2" name="date2" >
								<?php foreach($dateData as $key=>$date):?>
								<option value="<?php echo $date?>"><?php echo $date?></option>
								<?php endforeach?>
							</select>
						</label>
					</li>
					<li class="liLast">
						<label class="selbar">
							<select id="start2" name="start2" >
								<?php foreach($timeArr as $key=>$time):?>
								<option value="<?php echo $time?>"><?php echo $time?></option>
								<?php endforeach?>
							</select>
						</label>
						<span class="s">至</span>
						<label class="selbar">
							<select id="end2" name="end2" >
								<?php foreach($timeArr as $key=>$time):?>
								<option value="<?php echo $time?>"><?php echo $time?></option>
								<?php endforeach?>
							</select>
						</label>
					</li>
				</ul>
				<div class="button">
					<input type="hidden" id="helu_id" name="helu_id" value="">
					<input type="button" value="提交" class="btn b1" onclick="doAddKecheng('<?php echo U('Vip/Weixin/addKecheng',array('teacher_code'=>$userInfo['sCode']))?>')">
					<input type="button" value="取消" class="btn b2"><br>
					<span id="add_msg" class="error"></span>
				</div>
		</div>
	</div>
</div>
<div id="popWindow_return">
	<div class="popHelu">
		<div class="popHd" id="title">加课成功！</div>
		<div class="popBd">
			<p id="error_msg"></p>
			<div class="button">
				<button type="button" class="btn" id="button">我知道了</button>
			</div>
		</div>
	</div>
</div>
<div id="popWindow2">
	<div class="popCourse">
		<div class="popHd">调课</div>
		<div class="popBd">
				<ul>
					<li>
						<span class="alt">学员姓名：</span>
						<span id="student_name" style="font-size:.875rem;"></span>	
					</li>
					<li>
						<span class="alt">课程名称：</span>
						<span id="kecheng_name" style="font-size:.875rem;"></span>						
					</li>
					<li>
						<span class="alt">排课时间：</span>
						<span id="timeStr" style="font-size:.875rem;"></span>
					</li>
					<li>
						<span class="alt">调课时间：</span>
						<label >
							<?php echo $nowDate?>
							<input type="hidden" id="date" name="date" value="<?php echo $nowDate?>">
						</label>
					</li>
					<li class="liLast">
						<label class="selbar">
							<select id="start" name="start" >
								<?php foreach($timeArr as $key=>$time):?>
								<option value="<?php echo $time?>"><?php echo $time?></option>
								<?php endforeach?>
							</select>
						</label>
						<span class="s">至</span>
						<label class="selbar">
							<select id="end" name="end" >
								<?php foreach($timeArr as $key=>$time):?>
								<option value="<?php echo $time?>"><?php echo $time?></option>
								<?php endforeach?>
							</select>
						</label>
					</li>
				</ul>
				<div class="button">
					<input type="hidden" id="helu_id" name="helu_id" value="">
					<input type=button value="确定调课" class="btn  b1" onclick="doAdjust('<?php echo U('Vip/Weixin/adjustKecheng')?>')">
					<input type="button" value="取消" class="btn b2"><br>
					<span id="adjust_msg" class="error"></span>
				</div>
		</div>
	</div>
</div>

<div id="popWindow3" style="display:none;">
	<div class="popHelu">
		<div class="popHd" id="title3"></div>
		<div class="popBd">
			<p id="error_msg3"></p>
			<div class="button">
				<button type="button" class="btn" id="button">知道了</button>
			</div>
		</div>
	</div>
</div>
<div id="popBg"></div>
<!-- // 弹出层 -->
<script>
$(function() {
	$('#popWindow .b2').on('touchstart', function() {
		$('#popWindow').hide(function() {
			$('#popBg').hide()
		});
	});
	$('#popWindow2 .b2').on('touchstart', function() {
		$('#popWindow2, #popBg').hide(function() {
			$('#popBg').hide()
		});
	});
	
	$('#popWindow3 .btn').on('touchstart', function() {
		$('#popWindow3, #popBg').hide();
	});
});

function changeWeek(key){
	window.location.href="<?php echo U('Vip/Weixin/newWeekSchedule')?>/key/"+key;
}
</script>
</body>
</html>
