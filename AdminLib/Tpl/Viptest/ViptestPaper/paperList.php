<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/viptest.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<h2>试卷管理</h2>
	<div id="search">
		<form method="POST" action="">
		试卷：<select id="paper_id" name="paper_id" onchange="get_moduleList(this.value,'<?php echo $getModuleUrl?>')" style="width:130px;">
				<option value="">请选择试卷</option>
				<?php foreach($paperList as $key=>$paper):?>
				<option value="<?php echo $paper['id']?>" <?php if($paper['id']==$_REQUEST['paper_id']):?>selected<?php endif;?>><?php echo $paper['title']?></option>
				<?php endforeach?>
			</select>&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="#" onclick="testMessageBox_paperForm(event,'add','<?php echo $addPaperUrl?>');" class="blue">添加试卷</a>&nbsp;&nbsp;&nbsp;&nbsp;
			<span id="paper_edit"></span>
			<br><br>
		模块：<select id="module_id" name="module_id" onchange="get_module_edit(this.value,'<?php echo $addModuleUrl?>')" style="width:130px;">
				<option value="">请选择模块</option>
				<?php foreach($moduleList as $key=>$module):?>
				<option value="<?php echo $module['id']?>" <?php if($module['id']==$_REQUEST['module_id']):?>selected<?php endif;?>><?php echo $module['name']?></option>
				<?php endforeach?>
			</select>&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="#" onclick="testMessageBox_moduleForm(event,'add','<?php echo $addModuleUrl?>');" class="blue">添加模块</a>&nbsp;&nbsp;&nbsp;&nbsp;
			<span id="module_edit"></span>
			<br><br>
			<input type="submit" value="搜索" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="button" value="添加试题" onclick="testMessageBox_questionForm(event,'add','<?php echo $addQuestionUrl?>');" class="btn">
		</form>	
	</div>
	<hr>
	<div id="list" class="clearfix">
		<?php if($questionList):?>
		<table width="70%" border="1">
			<tr bgcolor="#dddddd" height=35>
				<th>编号</th>
				<th>题干</th>
				<th>所属试卷</th>
				<th>所属模块</th>
				<th>选项数量</th>
				<th>答案</th>
				<th>规定时间</th>
				<th>虚拟正确率</th>
				<th>排序</th>
				<th>操作</th>
			</tr>
			<?php foreach($questionList as $key=>$question):?>
			<tr height=30>
				<td align="center"><?php echo $question['id']?></td>
				<td align="center"><a href="#" onclick="testMessageBox_showImg(event,'<?php echo $question['show_url'];?>','<?php echo $question['img_width']?>','<?php echo $question['img_height']?>')"><img src="<?php echo $question['show_url'];?>" width="200" height="150"></a></td>
				<td align="center"><?php echo $question['paper_title'];?></td>
				<td align="center"><?php echo $question['module_name'];?></td>
				<td align="center"><?php echo $question['option_num'];?></td>
				<td align="center"><?php echo $question['answer'];?></td>
				<td align="center"><?php echo $question['time_limit'];?>秒</td>
				<td align="center"><?php echo $question['accuracy'];?>%</td>
				<td align="center"><?php echo $question['seq'];?></td>
				<td align="center">
					<a href="#" onclick="testMessageBox_statisticInfo(event,'<?php echo U('Viptest/ViptestPaper/statisticInfo',array('id'=>$question['id']))?>')" class="blue">查看统计</a>&nbsp;&nbsp; |&nbsp;&nbsp; 
					<a href="#" onclick="testMessageBox_questionForm(event,'edit','<?php echo U('Viptest/ViptestPaper/addQuestion',array('id'=>$question['id']))?>')" class="blue">修改</a>&nbsp;&nbsp; | &nbsp;&nbsp;
					<a href="<?php echo U('Viptest/ViptestPaper/deleteQuestion',array('id'=>$question['id']))?>" class="blue" onclick="return confirm('删除该试题将同时删除该试题的所有答题记录，\n确认要删除该试题吗？')">删除</a>
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