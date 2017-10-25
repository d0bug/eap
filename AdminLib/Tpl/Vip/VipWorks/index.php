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
					<p><a href="<?php echo U('Vip/VipStudents/studentInfo',array('student_code'=>$waitHelu['sstudentcode'],'kecheng_code'=>$waitHelu['skechengcode'],'lesson'=>$waitHelu['nlessonno']));?>" class="orange" target="_blank"><?php echo $waitHelu['sstudentname'];?></a></p>
					<p><?php echo $waitHelu['dtdatereal'];?></p>
					<p><?php echo $waitHelu['dtlessonbeginreal'];?>~<?php echo $waitHelu['dtlessonendreal'];?></p>
					<p>
						<?php if($waitHelu['overdue'] == 0):?>
							<?php if($waitHelu['lesson_topic']!=''):?>
								<input type="button" onclick="testMessageBox_handouts_helu(event,'<?php echo U('Vip/VipStudents/keChengHeLu',array('act'=>'update','helu_id'=>$waitHelu['heluid'],'student_code'=>$waitHelu['sstudentcode'],'student_name'=>$waitHelu['sstudentname'],'kecheng_code'=>$waitHelu['skechengcode'],'lesson_no'=>$waitHelu['nlessonno'],'lesson_date'=>$waitHelu['dtdatereal'],'lesson_begin'=>$waitHelu['dtlessonbeginreal'],'lesson_end'=>$waitHelu['dtlessonendreal']));?>');" value="核录">&nbsp;&nbsp;&nbsp;
							<?php else:?>
								<input type="button" onclick="testMessageBox_handouts_helu(event,'<?php echo U('Vip/VipStudents/keChengHeLu',array('act'=>'add','helu_id'=>$waitHelu['heluid'],'student_code'=>$waitHelu['sstudentcode'],'student_name'=>$waitHelu['sstudentname'],'kecheng_code'=>$waitHelu['skechengcode'],'lesson_no'=>$waitHelu['nlessonno'],'lesson_date'=>$waitHelu['dtdatereal'],'lesson_begin'=>$waitHelu['dtlessonbeginreal'],'lesson_end'=>$waitHelu['dtlessonendreal']));?>');" value="核录">&nbsp;&nbsp;&nbsp;
							<?php endif;?>
						<?php else:?>
							<input type="button" onclick="do_overdue('<?php echo U('Vip/VipWorks/doOverdue',array('helu_id'=>$waitHelu['heluid']))?>')" value="逾期未核录">&nbsp;&nbsp;&nbsp;
						<?php endif;?>
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
	<h2>教研上传统计<span class="error f_12">温馨提示： 点击讲义名称，进入教师版预览</span></h2>
	<div class="tableTab">
		<ul class="tab">
			<li class="current">
				<a href="/vip/vip_works/index#showMy">本周更新</a>
			</li>
			<li >
				<a href="/vip/vip_works/myworkhistory#showHistory">历史统计</a>
			</li>
		</ul>
	</div><br/>
		<h3>课程讲义</h3>
	<?php if(count($jiangyiArray) > 0){?>
		<table width="85%" border="1" id="courseJiangYiTable">
			<tr bgcolor="#dddddd" height=35>
				<th width = '10%'>学科</th>
				<th width = '10%'>课程属性</th>
				<th width = '10%'>讲义属性</th>
				<th width = '30%'>课程用途</th>
				<th width = '40%'>本周更新（<?php echo count($jiangyiArray);?>讲）</th>
			</tr>
			<?php foreach($jiangyiArray as $key=>$jiangyi){ ?>
			<tr height=30>
				<td><?php echo $jiangyi['sname'];?></td>
				<td><?php echo $jiangyi['gname'];?></td>
				<td><?php echo $jiangyi['kname'];?></td>
				<td><?php echo $jiangyi['courseuser'];?></td>
				<td>
					<a style="color:blue" href="javascript:void(0);" onclick="testMessageBox_handouts_preview(event,'<?php echo U('Vip/VipHandouts/view_handouts_pdf',array('hid'=>$jiangyi['hid'],'type'=>'teacher'))?>');"><?php echo $jiangyi['title'];?></a>
				</td>
			</tr>
			<?php }?>
		</table>
	<?php }else{?>	
		<p>暂无最新上传到的讲义</p>
	<?php }?>
		<h3>试题库</h3>
	<?php if(count($shitiArray) > 0){?>	
		<table width="85%" border="1" id="shiTiKuTable">
			<tr bgcolor="#dddddd" height=35>
				<th width = '20%'>学科</th>
				<th width = '20%'>题库属性</th>
				<th width = '20%'>试题属性</th>
				<th width = '40%'>本周更新（<?php echo count($shitiArray);?>套）</th>
			</tr>
			<?php foreach($shitiArray as $key=>$shiti){?>
				<tr height=30>
					<td><?php echo $shiti['sname'];?></td>
					<td><?php echo $shiti['gname'];?></td>
					<td><?php echo $shiti['kname'];?></td>
					<td>
						<a style="color:blue" onclick="testMessageBox_handouts_preview(event,'<?php echo U('Vip/VipHandouts/view_handouts_pdf',array('hid'=>$shiti['hid'],'type'=>'teacher'))?>');" href="javascript:void(0);"><?php echo $shiti['title'];?></a>
					</td>
				</tr>
			<?php }?>
		</table>
	<?php }else{?>	
		<p>暂无最新上传到的试题库</p>
	<?php }?>	
</div>
</div>
</body>
</html>