<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<div id="search">
	<form id="search_form" name="search_form" method="GET" action="<?php echo U('Vip/VipHandouts/test_paper',array('style'=>$list_style));?>">
		<select id="subject" name="subject" onchange="get_option(this.value,'grade','<?php echo U('Vip/VipHandouts/get_grades_option',array('ntype'=>1))?>','list')">
			<option value="">请选择科目</option>
			<?php foreach($subjectArr as $key=>$subject):?>
				<option value="<?php echo $subject['sid'];?>" <?php if($handouts_subject==$subject['sid']):?>selected<?php endif;?> ><?php echo $subject['name'];?></option>
			<?php endforeach?>
		</select>
		<select id="grade" name="grade" onchange="get_option(this.value,'knowledge','<?php echo U('Vip/VipHandouts/get_knowledge_option',array('ntype'=>1))?>','list')">
			<option value="">请选择题库属性</option>
		<?php foreach($gradeArr as $key=>$grade):?>
			<option value="<?php echo $grade['gid'];?>" <?php if($handouts_grade && $handouts_grade == $grade['gid']):?>selected<?php endif;?> ><?php echo $grade['name'];?></option>
		<?php endforeach?>
		</select>
		<select id="knowledge" name="knowledge">
		<?php if($knowledgeArr):?>
			<option value="">请选择试题属性</option>
			<?php foreach($knowledgeArr as $key=>$knowledge):?>
				<option value="<?php echo $knowledge['kid'];?>" <?php if($handouts_knowledge && $handouts_knowledge==$knowledge['kid']):?>selected<?php endif;?>><?php echo $knowledge['name'];?></option>
			<?php endforeach?>
		<?php else:?>
			<?php if($handouts_knowledge):?>
			<option value="<?php echo $handouts_knowledge;?>"><?php echo $handouts_knowledge_name;?></option>
			<?php endif;?>
			<option value="">请选择试题属性</option>
		<?php endif;?>
		</select>
		<select id="nid" name="nid" >
			<option value="">请选择年级</option>
			<?php foreach($nianjiArr as $key=>$nianji):?>
				<option value="<?php echo $key;?>" <?php if($handouts_nianji==$key):?>selected<?php endif;?> ><?php echo $nianji;?></option>
			<?php endforeach?>
		</select>
		上传时间：<input type="text"  class="Wdate" id="starttime" name="starttime" value="<?php echo $starttime;?>" onClick="WdatePicker()"> 至 <input type="text"  class="Wdate" id="endtime" name="endtime" value="<?php echo $endtime;?>"  onClick="WdatePicker()">&nbsp;&nbsp;
		<input type="text" id="keyword" name="keyword" value="<?php if($handouts_keyword):?><?php echo urldecode($handouts_keyword);?><?php endif;?>" placeholder="输入课程名称">&nbsp;&nbsp;<input type="submit" value="  搜索  ">
		<?php if($canBack == 1){?>
		&nbsp;&nbsp;<input type="submit" onclick="javascript:history.back(-1);return false;" value="返回">
		<?php }?>
	</form>
	</div>
	<div id="list">
		<h2>试题库&nbsp;&nbsp;&nbsp;&nbsp;
			<span>
				<a href="<?php echo U('Vip/VipHandouts/test_paper',array('style'=>'list','subject'=>$handouts_subject,'grade'=>$handouts_grade,'knowledge'=>$handouts_knowledge,'nid'=>$handouts_nianji,'keyword'=>urldecode($handouts_keyword),'starttime'=>$starttime,'endtime'=>$endtime,'p'=>$curPage));?>" class="f_14 <?php if($list_style=='list'):?>bold<?php endif;?>">列表</a> | 
				<a href="<?php echo U('Vip/VipHandouts/test_paper',array('style'=>'img','subject'=>$handouts_subject,'grade'=>$handouts_grade,'knowledge'=>$handouts_knowledge,'nid'=>$handouts_nianji,'keyword'=>urldecode($handouts_keyword),'starttime'=>$starttime,'endtime'=>$endtime,'p'=>$curPage));?>" class="f_14 <?php if($list_style=='img'):?>bold<?php endif;?>">图标</a>
			</span>
		</h2>
		<?php if($handoutsList):?>
			<?php if($list_style == 'img'):?>
				<ul>
					<?php foreach($handoutsList as $key=>$handouts):?>
					<li>
						<div class="pic"><a href="#" onclick="testMessageBox_handouts_detail(event,<?php echo $handouts['hid']?>,'<?php echo U('Vip/VipHandouts/show_detail_handouts')?>',1)"><img src="<?php if($handouts['picture']):?><?php echo $handouts['picture'];?><?php else:?>/static/images/default.gif<?php endif;?>" style="border:0px;height:100px;"></a></div>
						<div class="txt"><a href="#" onclick="testMessageBox_handouts_detail(event,<?php echo $handouts['hid']?>,'<?php echo U('Vip/VipHandouts/show_detail_handouts')?>',1)" title="<?php echo $handouts['title'];?>"><?php echo $handouts['title'];?><a></div>
						<div class="ac">
							<?php if($handouts['teacher_preview']==1):?>
								<a href="#" onclick="testMessageBox_handouts_preview(event,'<?php echo U('Vip/VipHandouts/view_handouts_pdf',array('hid'=>$handouts['hid'],'type'=>'teacher'))?>');">预览试题</a>
							<?php else:?>
								<a href="#" style="color:#cccccc">无预览版</a>
							<?php endif;?><em> | </em>
							<a href="<?php echo U('Vip/VipHandouts/download',array('hid'=>$handouts['hid'],'type'=>$handouts['type']));?>">下载试题</a><em> | </em>
							<a href="<?php echo U('Vip/VipHandouts/do_favorite',array('act'=>'add','hid'=>$handouts['hid'],'type'=>$handouts['type']));?>">收藏</a></div>
					</li>
					<?php endforeach?>
				</ul>
			<?php else:?>
				<table width="80%" border="1">
					<tr bgcolor="#dddddd" height=35>
						<td width="50%">试题标题</td>
						<td>试题文档</td>
						<td>上传时间</td>
						<td>操作</td>
					</tr>
					<?php foreach($handoutsList as $key=>$handouts):?>
					<tr height=30>
						<td><a href="#" onclick="testMessageBox_handouts_detail(event,<?php echo $handouts['hid']?>,'<?php echo U('Vip/VipHandouts/show_detail_handouts')?>',1);" title="<?php echo $handouts['title'];?>"><?php echo $handouts['title'];?></a></td>
						<td>
							<?php if($handouts['teacher_preview']==1):?>
								<a href="#" onclick="testMessageBox_handouts_preview(event,'<?php echo U('Vip/VipHandouts/view_handouts_pdf',array('hid'=>$handouts['hid'],'type'=>'teacher'))?>');" class="orange">预览试题</a>
							<?php else:?>
								<a href="#" style="color:#cccccc">无预览版</a>
							<?php endif;?>
							<em> | </em>
							<a href="<?php echo U('Vip/VipHandouts/download',array('hid'=>$handouts['hid'],'type'=>$handouts['type']));?>" class="orange">下载试题</a>
						</td>
						<td><?php echo date('Y-m-d H:i:s',$handouts['instime']);?></td>
						<td>
							<a href="#" onclick="testMessageBox_handouts_detail(event,<?php echo $handouts['hid']?>,'<?php echo U('Vip/VipHandouts/show_detail_handouts')?>',1);" title="<?php echo $handouts['title'];?>">查看</a>&nbsp;&nbsp;&nbsp;&nbsp;
							<a href="<?php echo U('Vip/VipHandouts/do_favorite',array('act'=>'add','hid'=>$handouts['hid'],'type'=>$handouts['type'],'style'=>$list_style));?>" class="orange">收藏</a>
						</td>
					</tr>
					<?php endforeach?>
				</table>
			<?php endif;?>
	<?php else:?>
		<div>暂无相关讲义信息</div>
	<?php endif;?>
	<div id="pageStr"><?php echo $showPage;?></div>
	</div>
</div>
</div>
</body>
</html>