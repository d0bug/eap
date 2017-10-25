<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<div id="search">
	<?php if($permInfo['permValue']==3):?>
		<input type="button" class="btn" value="添加高思教师" onclick="testMessageBox_addTeacher(event,1,'<?php echo U('Vip/VipPermission/checkThisUserIsExist')?>','<?php echo U('Vip/VipPermission/doAddTeacher')?>')">
		<input type="button" class="btn" value="添加社会兼职教师" onclick="testMessageBox_addTeacher(event,2,'<?php echo U('Vip/VipPermission/checkThisUserIsExist')?>','<?php echo U('Vip/VipPermission/doAddTeacher')?>')">
		<!--<input type="button" class="btn" value="编辑">-->
		<input type="button" class="btn" value="删除" onclick="return deleteTeacher('<?php echo U('Vip/VipPermission/deleteTeacher',array('p'=>$curPage))?>');">
	<?php endif;?>
	<input type="button" class="btn" value="查询" onclick="testMessageBox_selectTeacher(event,'<?php echo U('Vip/VipPermission/userManage')?>');">
	<a href="<?php echo U('Vip/VipPermission/userManage')?>"><input type="button" class="btn" value="查看全部"></a>
	<input type="button" class="btn" value="刷新" onclick="javascript:window.parent.frames[0].location.reload();">
	<?php if($permInfo['permValue']==3):?>
		<input type="button" class="btn" value="科目授权" onclick="testMessageBox_subjectAccredit(event,'<?php echo U('Vip/VipPermission/subjectAccredit',array('p'=>$curPage))?>')">
		<input type="button" class="btn" value="导出Excel表" onclick="window.location.href='<?php echo U('Vip/VipPermission/exportExcel',array('selectField'=>$selectField,'selectValue'=>$selectValue)); ?>'">
	<?php endif;?>
	
	</div>
	<hr>
	<div id="list" class="clearfix">
		<?php if($vipUserList):?>
		<table width="80%">
			<tr bgcolor="#dddddd" height=35>
				<td>&nbsp;&nbsp;&nbsp;用户登录名</td>
				<td>用户姓名</td>
				<td>教师类型</td>
				<td>教师身份</td>
				<td>拥有角色</td>
				<td>科目权限</td>
				<td>账号启用状态</td>
				<td>详细信息</td>
			</tr>
			<?php foreach($vipUserList as $key=>$vipUser):?>
			<tr height=30>
				<td>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="is_delete[]" id="user_key<?php echo $key?>" value="<?php echo $vipUser['user_key'];?>">&nbsp;<?php echo $vipUser['user_name'];?></td>
				<td><?php echo $vipUser['user_realname'];?></td>
				<td><?php if($vipUser['is_employee']):?>全职教师<?php endif;?><?php if($vipUser['is_teacher']):?>兼职教师<?php endif;?></td>
				<td><?php if($vipUser['is_teaching_and_research']==='1'):?>教研教师<?php elseif($vipUser['is_teaching_and_research']==='0'):?>校区教师<?php else:?>无<?php endif;?></td>
				<td><?php echo $vipUser['roles']; ?></td>
				<td><?php echo $vipUser['subjectAccredit']; ?></td>
				<td><?php if($vipUser['is_removed']==1):?><font color=red>已禁用</font><?php else:?><font color="green">已启用</font><?php endif;?></td>
				<td><a href="#" onclick="testMessageBox_vipUserInfo(event,'<?php echo U('Vip/VipPermission/vipUserInfo',array('user_key'=>$vipUser['user_key']))?>')">查看</a>&nbsp;&nbsp;&nbsp;&nbsp;
				<?php if($permInfo['permValue']==3):?>
					<a href="#" onclick="testMessageBox_editTeacher(event,'<?php echo $vipUser['user_key'];?>',<?php echo $vipUser['is_employee'];?>,'<?php echo U('Vip/VipPermission/getTeacherInfo');?>','<?php echo U('Vip/VipPermission/doEditTeacher');?>','<?php echo $curPage;?>')">编辑</a>
				<?php endif;?>
				</td>
			</tr>
			<?php endforeach?>
		</table>
		<div id="pageStr">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="checkAll" id="checkAll" value="1" >全选&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $showPage;?></div>
		<?php else:?>
		<div>暂无相关信息</div>
		<?php endif;?>
	</div>
</div>
</div>
</body>
</html>