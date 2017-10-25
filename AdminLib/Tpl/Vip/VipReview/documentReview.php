<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<script type="text/javascript" src="/static/js/jquery.uploadify-3.1.min.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<div id="search">
	<form id="documentManageForm" name="documentManageForm" method="POST" action="<?php echo U('Vip/VipReview/documentReview')?>">
		<select id="type" onchange="get_option(this.value,'subject','/vip/vip_handouts/get_subject_option/ntype/0','list')" name="type">
			<option value="">请选择类型</option>
			<option value="1" <?php if($type==1):?>selected<?php endif;?>>课程讲义</option>
			<option value="2" <?php if($type==2):?>selected<?php endif;?>>试题库</option>
		</select>
		<select id="subject" name="subject" onchange="get_option(this.value,'grade','<?php echo U('Vip/VipHandouts/get_grades_option')?>','list')">
			<option value="">请选择科目</option>
			<?php foreach($subjectArr as $key=>$subject):?>
				<option value="<?php echo $subject['sid'];?>" <?php if($handouts_subject==$subject['sid']):?>selected<?php endif;?> ><?php echo $subject['name'];?></option>
			<?php endforeach?>
		</select>
		<select id="grade" name="grade" onchange="get_option(this.value,'knowledge','<?php echo U('Vip/VipHandouts/get_knowledge_option')?>','list')">
			<option value="">请选择课程属性</option>
			<?php foreach($gradeArr as $key=>$grade):?>
			<option value="<?php echo $grade['gid'];?>" <?php if($handouts_grade && $handouts_grade==$grade['gid']):?>selected<?php endif;?>><?php echo $grade['name'];?></option>
		<?php endforeach?>
		</select>
		<select id="knowledge" name="knowledge">
			<?php if($knowledgeArr):?>
			<option value="">请选择讲义属性</option>
			<?php foreach($knowledgeArr as $key=>$knowledge):?>
				<option value="<?php echo $knowledge['kid'];?>" <?php if($handouts_knowledge && $handouts_knowledge==$knowledge['kid']):?>selected<?php endif;?>><?php echo $knowledge['name'];?></option>
			<?php endforeach?>
		<?php else:?>
			<?php if($handouts_knowledge):?>
			<option value="<?php echo $handouts_knowledge;?>"><?php echo $handouts_knowledge_name;?></option>
			<?php endif;?>
			<option value="">请选择讲义属性</option>
		<?php endif;?>
		</select>
		<select id="nianji" name="nianji">
			<option value="">请选择年级属性</option>
			<?php foreach($nianjiArr as $key=>$nianji):?>
				<option value="<?php echo $key;?>" <?php if($handouts_nianji==$key):?>selected<?php endif;?> ><?php echo $nianji;?></option>
			<?php endforeach?>
		</select>
		<input type="text" id="keyword" name="keyword" value="<?php if($keyword):?><?php echo urldecode($keyword);?><?php endif;?>" placeholder="输入讲义名称..." onfocus="javascript:$(this).val('');" size="30">
		<input type="submit" value="搜索" >
	</form>
	</div>
	<hr>
	<div id="list" class="clearfix">
		<?php if($handoutsList):?>
		<table width="100%" border="1">
			<tr bgcolor="#dddddd" height=30>
				<td width="50">类型</td>
				<td width="100">标题</td>
				<td width="70">所属科目</td>
				<td width="130">课程属性/题库属性</td>
				<td width="130">讲义属性/试题属性</td>
				<td width="70">年级</td>
				<td width="20">是否兼职可见</td>
				<td width="20">教师版</td>
				<td width="20">学生版</td>
				<td width="80">介绍</td>
				<td width="50">上传人</td>
				<td width="80">上传时间</td>
				<td width="50">审核人</td>
				<td width="60">审核状态</td>
				<td width="50">操作</td>
			</tr>
			<?php foreach($handoutsList as $key=>$handouts):?>
			<tr height=30>
				<td><?php echo ($handouts['type']==1)?'试题库':'课程讲义';?></td>
				<td width="250"><?php echo $handouts['title'];?></td>
				<td><?php echo $handouts['sname'];?></td>
				<td><?php echo $handouts['gname'];?></td>
				<td><?php echo $handouts['kname'];?></td>
				<td width="120"><?php echo $handouts['nnames'];?></td>
				<td width="50"><?php echo ($handouts['is_parttime_visible']==1)?'是':'<font color=red>否</font>';?></td>
				<td><a href="#" onclick="testMessageBox_view_pdf(event,'<?php echo U('Vip/VipHandouts/view_handouts_pdf',array('hid'=>$handouts['hid'],'type'=>'teacher'))?>')"><img src="/static/images/pdf.png"></a></td>
				<td><?php if($handouts['student_version']):?><a href="#" onclick="testMessageBox_view_pdf(event,'<?php echo U('Vip/VipHandouts/view_handouts_pdf',array('hid'=>$handouts['hid'],'type'=>'student'))?>')"><img src="/static/images/word.png"></a><?php endif;?></td>
				<td width="290"><?php echo $handouts['introduce'];?></td>
				<td width="60"><?php echo $handouts['user_realname'];?></td>
				<td width="80"><?php echo date('Y-m-d H:i:s',$handouts['instime']);?></td>
				<td width="60"><?php echo $handouts['verifier'];?></td>
				<td><?php if($handouts['status']==1):?><font color=green>已通过</font><?php elseif ($handouts['status'] == 2):?><font color=orange>未通过</font><?php else:?><font color=red>未审核</font><?php endif;?></td>
				<td>
				<?php if($permInfo['permValue']==3):?>
					<a href="javascript:void(0);" onclick="testMessageBox_editHandouts(event,'<?php echo $handouts['hid']?>','<?php echo U('Vip/VipJiaoyan/get_edithandouts_form')?>','<?php echo $handouts['type'];?>')">修改</a><br/>
					<a href="javascript:void(0);" onclick="testMessageBox_reviewHandouts(event,'<?php echo $handouts['hid']?>','<?php echo U('Vip/VipReview/reviewHandouts')?>','<?php echo $handouts['type'];?>','<?php echo $handouts['title'];?>','<?php echo $handouts['user_key'];?>')">审核</a><br/>
					<a href="javascript:void(0);" onclick="testMessageBox_deleteHandouts(event,'<?php echo $handouts['hid']?>','<?php echo U('Vip/VipReview/delete_handouts')?>','<?php echo $handouts['type'];?>','<?php echo $handouts['user_key'];?>','<?php echo $handouts['title'];?>','<?php echo $userInfo['real_name'];?>')">删除</a>
				<?php endif;?>
				</td>
			</tr>
			<?php endforeach?>
		</table>
		<div id="pageStr"><?php echo $showPage;?></div>
		<?php else:?>
		<div>暂无相关信息</div>
		<?php endif;?>
	</div>
</div>
</div>
</body>

</html>