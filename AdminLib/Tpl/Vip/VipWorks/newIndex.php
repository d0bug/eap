<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main"><?php if($userInfo['real_name']=='解翠萍'):?><a href="<?php echo U('Vip/VipWorks/dealData')?>">处理数据</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo U('Vip/VipWorks/updateFileTitle')?>">更新文档名称</a>&nbsp;&nbsp;&nbsp;<br><br><?php endif;?>
	<?php if(!empty($userInfo['sCode'])):?>
	<h2>待核录学员&nbsp;&nbsp;&nbsp;<span class="error f_12">温馨提示： 敬爱的老师，您有以下学员还未核录课时哦~~</span></h2>
	<div id="list2" class="clearfix">
		<ul>
		<?php if(!empty($waitHeluList)):?>
			<?php foreach($waitHeluList as $key=>$waitHelu):?>
				<li>
					<p><img src="/static/images/tx.png"></p>
					<p><a href="<?php echo U('Vip/VipStudents/newStudentLesson',array('student_code'=>$waitHelu['sstudentcode']));?>" class="orange" target="_blank"><?php echo $waitHelu['sstudentname'];?></a></p>
					<p><?php echo $waitHelu['dtdatereal'];?></p>
					<p><?php echo $waitHelu['dtlessonbeginreal'];?>~<?php echo $waitHelu['dtlessonendreal'];?></p>
					<p>
						<?php if($waitHelu['overdue'] == 0):?>
							<input type="button" onclick="javascript:window.open('<?php echo U('Vip/VipStudents/recordLessonTrack',array('helu_id'=>$waitHelu['heluid']))?>')" value="核录">&nbsp;&nbsp;&nbsp;
						<?php else:?>
							<input type="button" onclick="do_overdue('<?php echo U('Vip/VipWorks/doOverdue',array('helu_id'=>$waitHelu['heluid']))?>')" value="逾期未核录">&nbsp;&nbsp;&nbsp;
						<?php endif;?>
					</p>
				</li>
			<?php endforeach?>
		<?php endif;?>
		</ul>
	</div>
	
	<h2>待备课学员&nbsp;&nbsp;&nbsp;<span class="error f_12">温馨提示： 此处仅显示近一周待备课学员，更多待备课学员请到学员课程详情中查看~~</span></h2>
	<div id="list2" class="clearfix">
		<ul>
		<?php if(!empty($waitPrepareList)):?>
			<?php foreach($waitPrepareList as $key=>$waitPrepare):?>
				<li>
					<p><img src="/static/images/tx.png"></p>
					<p><a href="<?php echo U('Vip/VipStudents/newStudentLesson',array('student_code'=>$waitPrepare['sstudentcode']));?>" class="orange" target="_blank"><?php echo $waitPrepare['sstudentname'];?></a></p>
					<p><?php echo $waitPrepare['dtdatereal'];?></p>
					<p><?php echo $waitPrepare['dtlessonbeginreal'];?>~<?php echo $waitPrepare['dtlessonendreal'];?></p>
					<p>
						<input type="button" onclick="javascript:window.location.href='<?php echo C('PREPARE_URL')?>'" value="备课">
					</p>
				</li>
			<?php endforeach?>
		<?php endif;?>
		</ul>
	</div>
	
	
	<h2>待生成辅导方案学员&nbsp;&nbsp;&nbsp;</h2>
	<div id="list2" class="clearfix">
		<ul>
		<?php if(!empty($testCoachList)):?>
			<?php foreach($testCoachList as $key=>$testCoach):?>
				<li>
					<p><img src="/static/images/tx.png"></p>
					<p><a href="<?php echo U('Vip/VipStudents/newStudentLesson',array('student_code'=>$testCoach['sstudentcode']));?>" class="orange" target="_blank"><?php echo $testCoach['studentname'];?></a></p>
					<p><?php echo $testCoach['dtauditenddate'];?></p>
					<p>
						<input type="button"  value="生成" onclick="javascript:window.open('<?php echo U('Vip/VipStudents/newStudentProgram2',array('student_code'=>$testCoach['sstudentcode']))?>')">&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="button"  value="无效" onclick="testMessageBox_program_useless(event,'<?php echo U('Vip/VipStudents/programUseLess',array('id'=>$testCoach['id']))?>')">
					</p>
				</li>
			<?php endforeach?>
		<?php endif;?>
		</ul>
	</div>
	
	
	<?php endif;?>
	<?php if(!empty($messageList)):?>
	<h2>审核消息&nbsp;&nbsp;&nbsp;<span class="error f_12">温馨提示： 敬爱的老师，您有以下信息未通过审核，请及时修改~~</span></h2>
	<ul>
		<?php foreach($messageList as $key=>$message):?>
			<?php if($message['is_delete']){ ?>
				<li id="msgConfirmLi_<?php echo $message['id'];?>"><img src="/static/images/ann_icon.gif"><?php echo $message['message'];?>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="error"  onclick="hideDeleteMsg(<?php echo $message['id'];?>,'<?php echo U('Vip/VipWorks/deleteMsg',array('msgId'=>$message['id']));?>')">确定</a></li>
			<?php }else{?>
				<li><img src="/static/images/ann_icon.gif"><?php echo $message['message'];?>&nbsp;&nbsp;&nbsp;<a href="<?php echo $message['url'];?>" class="error">修改</a></li>
			<?php }?>	
		<?php endforeach?>
	</ul>
	<?php endif;?>
	<a name='showMy'></a>
	<h2>标准化讲义统计&nbsp;&nbsp;&nbsp;<span class="error f_12">温馨提示： 复制讲义名，可在PIV4.0-备课-教研标准化讲义 中搜索到相应讲义</span></h2>
	<div class="tableTab">
		<ul class="tab">
			<li class="current">
				<a href="/vip/vip_works/newIndex#showMy">本周更新</a>
			</li>
			<li >
				<a href="/vip/vip_works/newMyworkhistory#showHistory">历史统计</a>
			</li>
		</ul>
	</div><br/>
		<?php if(count($lectureList) > 0):?>
		<table width="85%" border="1" id="courseJiangYiTable">
			<tr bgcolor="#dddddd" height=35>
				<th width = '10%'>学科</th>
				<th width = '10%'>课程属性</th>
				<th width = '10%'>讲义属性</th>
				<th width = '40%'>本周更新（<?php echo count($lectureList);?>讲）</th>
			</tr>
			<?php foreach($lectureList as $key=>$lecture): ?>
			<tr height=30>
				<td><?php echo $lecture['sname'];?></td>
				<td><?php echo $lecture['gname'];?></td>
				<td><?php echo $lecture['kname'];?></td>
				<td>
					<!--<a  href="<?php echo U('Vip/VipWorks/previewLecture',array('lecture_id'=>$lesson['lecture_id']))?>" target="_blank" class="blue"><?php echo $lecture['title'];?></a>-->
					<?php echo $lecture['title'];?>
				</td>
			</tr>
			<?php endforeach;?>
		</table>
	<?php else:?>	
		<p>暂无最新搭建的标准化讲义</p>
	<?php endif;?>
</div>
</div>
</body>
</html>