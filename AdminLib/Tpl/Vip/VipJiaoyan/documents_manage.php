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
	<form id="documentManageForm" name="documentManageForm" method="POST" action="<?php echo U('Vip/VipJiaoyan/documents_manage')?>">
		查询时间：<input type="text"  class="Wdate" id="starttime" name="starttime" value="<?php echo $startTime;?>" onClick="WdatePicker()">
		<input type="text" id="keyword" name="keyword" value="<?php if($keyword):?><?php echo urldecode($keyword);?><?php endif;?>" placeholder="输入课程名称" onfocus="javascript:$(this).val('');"> <input type="submit" value="搜索">
	</form>
	</div>
	<hr>
	<div id="list" class="clearfix">
		<?php if($handoutsList):?>
		<table width="100%" border="1">
			<tr bgcolor="#dddddd" height=30>
				<td>类型</td>
				<td>标题</td>
				<td>所属科目</td>
				<td>课程属性/题库属性</td>
				<td>讲义属性/试题属性</td>
				<td>年级</td>
				<td>是否兼职可见</td>
				<td>教师版</td>
				<td>学生版</td>
				<td>介绍</td>
				<td>上传人</td>
				<td>审核状态</td>
				<td>操作</td>
			</tr>
			<?php foreach($handoutsList as $key=>$handouts):?>
			<tr height=30>
				<td><?php echo ($handouts['type']==1)?'试题库':'课程讲义';?></td>
				<td width="250"><?php echo $handouts['title'];?></td>
				<td><?php echo $handouts['sname'];?></td>
				<td><?php echo $handouts['gname'];?></td>
				<td><?php echo $handouts['kname'];?></td>
				<td width="150"><?php echo $handouts['nnames'];?></td>
				<td width="50"><?php echo ($handouts['is_parttime_visible']==1)?'是':'<font color=red>否</font>';?></td>
				<td><a href="#" onclick="testMessageBox_view_pdf(event,'<?php echo U('Vip/VipHandouts/view_handouts_pdf',array('hid'=>$handouts['hid'],'type'=>'teacher'))?>')"><img src="/static/images/pdf.png"></a></td>
				<td><?php if($handouts['student_version']):?><a href="#" onclick="testMessageBox_view_pdf(event,'<?php echo U('Vip/VipHandouts/view_handouts_pdf',array('hid'=>$handouts['hid'],'type'=>'student'))?>')"><img src="/static/images/word.png"></a><?php endif;?></td>
				<td width="290"><?php echo $handouts['introduce'];?></td>
				<td><?php echo $handouts['user_realname'];?></td>
				<td><?php if($handouts['status']==1):?><font color=green>已通过</font><?php elseif ($handouts['status'] == 2):?><font color=orange>未通过</font><?php else:?><font color=red>待审</font><?php endif;?></td>
				<td>
				<?php if($permInfo['permValue']==3):?>
					<a href="#" onclick="testMessageBox_editHandouts(event,'<?php echo $handouts['hid']?>','<?php echo U('Vip/VipJiaoyan/get_edithandouts_form')?>','<?php echo $handouts['type'];?>')">修改</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="<?php echo U('Vip/VipHandouts/delete_handouts',array('hid'=>$handouts['hid'],'p'=>$curPage,'returnAction'=>'VipJiaoyan','returnFunction'=>'documents_manage'));?>" onclick="return confirm('确定要删除该文档吗？')">删除</a>
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