<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<script type="text/javascript" src="/static/js/jquery.uploadify-3.1.min.js"></script>
<script type="text/javascript" src="/static/js/use_uploadify.js"></script>
<link href="/static/css/uploadify.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/static/js/eval.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body >
<div region="center">
<div id="main">
		<form id="addPaper" name="addPaper" method="POST" action="<?php echo U('Eval/EvalPaper/doAddPaper')?>" enctype="multipart/form-data" onsubmit="return check_addPaper()">
		<input type="hidden" id="id" name="id" value="<?php echo $paperInfo['id'];?>">
	  	<input type="hidden" id="upload_document" name="upload_document" value="<?php echo U('Eval/EvalPaper/uploadDocument');?>" >
		<p><font color=red>*</font>试卷名称：<input type="text" id="title" name="title" value="<?php echo $paperInfo['title'];?>" size="50"></p><br />
		<p><font color=red>*</font>试题数量：<input type="text" id="question_num" name="question_num" value="<?php echo $paperInfo['question_num'];?>" style="width:30px;">&nbsp;&nbsp;<font color=red>*</font>
		答卷时间：<input type="text" id="answer_time" name="answer_time" value="<?php echo $paperInfo['answer_time'];?>" style="width:30px;">&nbsp;&nbsp;
		<font color=red>*</font>满分：<input type="text" id="full_mark" name="full_mark" value="<?php echo $paperInfo['full_mark'];?>" style="width:30px;">
		年级：
		<select id="grade" name="grade">
			<option value="一年级" <?php echo $paperInfo['grade'] == '一年级' ? "selected='selected'":''?>>一年级</option>
			<option value="二年级" <?php echo $paperInfo['grade'] == '二年级' ? "selected='selected'":''?>>二年级</option>
			<option value="三年级" <?php echo $paperInfo['grade'] == '三年级' ? "selected='selected'":''?>>三年级</option>
			<option value="四年级"<?php echo $paperInfo['grade'] == '四年级' ? "selected='selected'":''?> >四年级</option>
			<option value="五年级" <?php echo $paperInfo['grade'] == '五年级' ? "selected='selected'":''?>>五年级</option>
			<option value="六年级" <?php echo $paperInfo['grade'] == '六年级' ? "selected='selected'":''?>>六年级</option>
			<option value="初一" <?php echo $paperInfo['grade'] == '初一' ? "selected='selected'":''?>>初一</option>
			<option value="初二" <?php echo $paperInfo['grade'] == '初二' ? "selected='selected'":''?>>初二</option>
			<option value="初三" <?php echo $paperInfo['grade'] == '初三' ? "selected='selected'":''?>>初三</option>
			<option value="高一" <?php echo $paperInfo['grade'] == '高一' ? "selected='selected'":''?>>高一</option>
			<option value="高二" <?php echo $paperInfo['grade'] == '高二' ? "selected='selected'":''?>>高二</option>
			<option value="高三" <?php echo $paperInfo['grade'] == '高三' ? "selected='selected'":''?>>高三</option>
		</select>
		</p>
		<p><font color=red>*</font>评级标准：<input type=hidden id="level_num" name="level_num" value="<?php echo count($levelList)?>">
		<a href="#" onclick="add_level('#levelSpan')"><img src="/static/images/add.png"></a><div id="levelSpan" style="margin-left:5px;">
		<?php 
		if(!empty($levelList)){
				foreach ($levelList as $key=>$level){
				?>
				<span id="span_level_<?php echo $key+1;?>">
			   名称：<input type="text" id="levelname_<?php echo $key+1;?>" name="name[]" value="<?php echo $level['name']?>" size="8">&nbsp;&nbsp;
			   上限：<input type="text" id="levelup_<?php echo $key+1;?>" name="up[]" value="<?php echo $level['up']?>" size="2">&nbsp;&nbsp;
			   下限：<input type="text" id="levellow_<?php echo $key+1;?>" name="low[]" value="<?php echo $level['low']?>" size="2">&nbsp;&nbsp;
			   说明：<input type="text" id="leveldesc_<?php echo $key+1;?>" name="desc[]" value="<?php echo $level['desc']?>" size="15">&nbsp;&nbsp;
			   <a href="#" onclick="del_level('#span_level_<?php echo $key+1;?>')"><img src="/static/images/delete.png"></a><br></span>
			<?php
				}
			}else{
			?>
				<span id="span_level_1">
			   名称：<input type="text" id="levelname_1" name="name[]" value="<?php $level['name']?>" size="8">&nbsp;&nbsp;
			   上限：<input type="text" id="levelup_1" name="up[]" value="<?php echo $level['up']?>" size="2">&nbsp;&nbsp;
			   下限：<input type="text" id="levellow_1" name="low[]" value="<?php echo $level['low']?>" size="2">&nbsp;&nbsp;
			   说明：<input type="text" id="leveldesc_1" name="desc[]" value="<?php echo $level['desc']?>" size="15">&nbsp;&nbsp;
			   <a href="#" onclick="del_level('#span_level_1')"><img src="/static/images/delete.png"></a><br></span>
			<?php
			}
			?>
		</div></p>
		<br /><p><font color=red>*</font>包含模块：</p>
		<p>
			<?php
			if(!empty($moduleInfo)){
					foreach ($moduleInfo as $k=>$module){
						$id = $module['id'];
						$name = $module['name'];
						if(in_array($id,$contain_module))
							$chk = 'checked';
						else
							$chk = '';
						?>
						<span id="span_module_<?php echo $k+1;?>"><input type="checkbox" id="module_<?php echo $k+1;?>" name="contain_module[]" value="<?php echo $id?>" size="10" <?php echo $chk?>>&nbsp;&nbsp;<?php echo $name;?></span>&nbsp;&nbsp;
					<?php
					}
				}
			?>
		</p>
		<p><font color=red>*</font>上传PDF文档：
		<div style="margin-left:60px">
			<span name="file_upload_document" id="file_upload_document"></span><label id="upload_itembank_msg" class="success"></label><br>
			<div id="view_document">
				<?php if($paperInfo['document']):?>
					<!--<a href="#" onclick="btnGetReturnValue_onclick(event,'<?php echo U('Eval/EvalPaper/preview_pdf',array('paper_id'=>$paperInfo['id']))?>');">点击我进行预览</a>-->&nbsp;&nbsp;<br>
					<?php echo $paperInfo['show_url'];?>
					<a href="#" onclick="del_file_document('<?php echo $paperInfo['document'];?>','#view_document','#document','<?php echo U('Eval/EvalPaper/delImg');?>')">删除</a>
				<?php endif?>			
			</div>
		</div>
		<input type="hidden" id="document" name="document" value="<?php echo $paperInfo['document'];?>">
		</p>
		<?php
		$ckd = $chkdd = '' ;
		if(!empty($paperInfo) && $paperInfo['is_download'] == 1 ||empty($_GET['paper_id']))
				 $checked = ' checked';
			else
				 $checked = '';

		if((!empty($paperInfo) && $paperInfo['status'] == 1 )||empty($_GET['paper_id']))
			$ckd = 'checked';
		if(!empty($paperInfo) && $paperInfo['status'] == 0)
			$chkdd ='checked';		
		?>
		<p><input type="checkbox" name="is_download" id="is_download" value="1" <?php echo $checked;?>>允许下载</p>
		<p><font color=red>*</font>试卷状态：<input type="radio" id="status" name="status" value="1" <?php echo $ckd;?>>
		启用&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="status" name="status" value="0" <?php echo $chkdd ;?>>
		停用</p><br />
		<p><input type=submit value="保存" class="btn">
		<?php if(!empty($paperInfo['id'])){?> 
		<a href="<?php echo U('Eval/EvalPaper/deletePaper',array('id'=>$paperInfo['id']))?>" class="blue" onclick="return confirm('删除试卷将同时删除、试题，成绩，答题记录等，\n确认要删除该试卷吗？')">删除该试卷</a>;
		<?php
			}
		?>
		</p>
		</form>
</div>
</div>
</body>
</html>