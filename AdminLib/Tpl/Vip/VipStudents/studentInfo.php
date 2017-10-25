<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.uploadify-3.1.min.js"></script>
<script type="text/javascript" src="/static/js/use_uploadify.js"></script>
<link href="/static/css/uploadify.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
	<div id="main">
		<table border="0">
			<tr>
				<td><img src="/static/images/default_avatar.jpg" width="80" height="80"></td>
				<td valign="top">
					<p>&nbsp;&nbsp;<font class="f_20"><?php echo $studentInfo['sstudentname']?></font>&nbsp;|&nbsp;<?php echo $studentInfo['sschool']?></p>
					<p>&nbsp;&nbsp;<?php echo $studentInfo['gradename']?></p>
				</td>
			</tr>
		</table>
		<div id="tt" class="easyui-tabs" data-options="tools:'#tab-tools'" style="width:900;">
		<div title="已上课程" data-options="tools:'#p-tools'" style="padding:20px;">
			<!--已上课程-->
			<table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableInfo">
				<tr bgcolor="#DDDDDD" height=35>
					<td width="15%">课次主题</td>
					<td width="10%">课次结束时间</td>
					<td width="30%">讲义</td>
					<td width="30%">测试卷</td>
					<td width="15%">课堂评价</td>
				</tr>
			<?php if(!empty($heluList)):?>
				<?php foreach($heluList as $key=>$helu):?>
					<tr height=30>
						<td ><?php echo $helu['lesson_topic'];?></td>
						<td ><?php echo $helu['lesson_date'].' '.$helu['lesson_end'];?></td>
						<td ><?php if($helu['handouts']):?>
								<?php foreach($helu['handouts'] as $k=>$file):?>
								<a href="<?php if($file['is_exist']==1):?><?php echo U('Vip/VipStudents/download',array('id'=>$helu['id'],'type'=>'1','order'=>$k));?><?php else:?>#none<?php endif;?>" <?php if($file['is_exist']==0):?>title="文件不存在" onclick="javascript:alert('文件不存在');"<?php endif;?> >
								<img src="/static/images/<?php if($file['filetype']=='pdf'):?>pdf.gif<?php elseif($file['filetype']=='doc'||$file['filetype']=='docx'):?>doc.gif<?php elseif($file['filetype']=='ppt'||$file['filetype']=='pptx'):?>ppt.png<?php elseif($file['filetype']=='xls'||$file['filetype']=='xlsx'):?>xls.png<?php elseif($file['filetype']=='jpg'||$file['filetype']=='jpeg'||$file['filetype']=='gif'||$file['filetype']=='png'):?>img.png<?php else:?>file.png<?php endif;?>"><?php echo $helu['handouts_title'].'('.$k.')'.'.'.$file['filetype'];?></a><br>
								<?php endforeach?>
							<?php endif;?><br>
							
							<!--<?php if($helu['itembank_url']):?>
								<a href="<?php if($helu['is_exist_itembank']==1):?><?php echo U('Vip/VipStudents/download',array('id'=>$helu['id'],'type'=>'2','order'=>$k));?><?php else:?>#none<?php endif;?>" <?php if($helu['is_exist_itembank']==0):?>title="文件不存在" onclick="javascript:alert('文件不存在');"<?php endif;?> >
								<img src="/static/images/<?php if($helu['itembank_type']=='pdf'):?>pdf.gif<?php elseif($helu['itembank_type']=='doc'||$helu['itembank_type']=='docx'):?>doc.gif<?php elseif($helu['itembank_type']=='ppt'||$helu['itembank_type']=='pptx'):?>ppt.png<?php elseif($helu['itembank_type']=='xls'||$helu['itembank_type']=='xlsx'):?>xls.png<?php else:?>file.png<?php endif;?>"><?php echo $helu['itembank_title'].'.'.$helu['itembank_type'];?></a>
							<?php endif;?>-->
						</td>
						<td>
							<?php if($helu['itembank']):?>
								<?php foreach($helu['itembank'] as $k=>$file):?>
								<a href="<?php if($file['is_exist']==1):?><?php echo U('Vip/VipStudents/download',array('id'=>$helu['id'],'type'=>'1','order'=>$k));?><?php else:?>#none<?php endif;?>" <?php if($file['is_exist']==0):?>title="文件不存在" onclick="javascript:alert('文件不存在');"<?php endif;?> >
								<img src="/static/images/<?php if($file['filetype']=='pdf'):?>pdf.gif<?php elseif($file['filetype']=='doc'||$file['filetype']=='docx'):?>doc.gif<?php elseif($file['filetype']=='ppt'||$file['filetype']=='pptx'):?>ppt.png<?php elseif($file['filetype']=='xls'||$file['filetype']=='xlsx'):?>xls.png<?php elseif($file['filetype']=='jpg'||$file['filetype']=='jpeg'||$file['filetype']=='gif'||$file['filetype']=='png'):?>img.png<?php else:?>file.png<?php endif;?>"><?php echo $helu['itembank_title'].'('.$k.')'.'.'.$file['filetype'];?></a><br>
								<?php endforeach?>
							<?php endif;?>
						</td>
						<td ><?php echo $helu['comment'];?></td>
					</tr>
				<?php endforeach?>
			<?php endif;?>
			</table>
			<div id="pageStr"><?php echo $showPage;?></div>
		</div>
		<div title="阶段培养方案" data-options="" style="padding:20px;">
			<!--阶段培养方案-->
				<table class="tableInfo" border="0" width="80%">
					<tr>
						<td valign="top" width="100">培养方案： </td>
						<td valign="top">
							<input type="hidden" id="uploadimg_url" name="uploadimg_url" value="<?php echo U('Vip/VipInfo/upload_img')?>">
							<input type="hidden" id="delimg_url" name="delimg_url" value="<?php echo U('Vip/VipInfo/del_img')?>">
							<input type="hidden" id="is_preview" name="is_preview" value="1">
							<input type="hidden" id="from_type" name="from_type" value="0">
							<input type="hidden" id="teacher_code" name="teacher_code" value="<?php echo $userInfo['sCode']?>">
							<input type="hidden" id="teacher_name" name="teacher_name" value="<?php echo $userInfo['real_name']?>">
							<select id="kecheng_code" name="kecheng_code">
								<option value="">请选择相应课程</option>
							<?php if($kechengList):?>
								<?php foreach ($kechengList as $key=>$kecheng):?>
								<option value="<?php echo $kecheng['skechengcode']?>"><?php echo $kecheng['skechengname']?>(<?php echo $kecheng['skechengcode']?>)</option>
								<?php endforeach;?>
							<?php endif;?>
							</select><br><br>
							<input type="file" id="upload_item_bank" value="上传"></span>
							<label id="upload_itembank_msg" class="success"></label><br>
							<span id="view_teacher_file"></span><br>
							<input type="hidden" id="teacher_version" name="teacher_version" value="">
							<input type="button" value="保存" class="btn" onclick="add_trainingProgram('<?php echo $student_code?>','<?php echo $studentInfo['sstudentname']?>','<?php echo U('Vip/VipStudents/addTrainingProgram')?>')">
						</td>
					</tr>
				</table><br>
				<div id="programList">
					<table border="1" width="80%">
						<tr>
							<th align="center">上传时间</th>
							<th align="center">所属课程</th>
							<th align="center">辅导方案</th>
							<th align="center">上传方式</th>
							<th align="center">操作</th>
						</tr>
					<?php if(!empty($programList)):?>
						<?php foreach($programList as $key=>$program):?>
							<tr id="p_<?php echo $program['id'];?>">
								<td ><?php echo $program['instime'];?></td>
								<td ><?php echo $program['kecheng_name'];?></td>
								<td >
								<!--<img src="/static/images/<?php if($program['file_type']=='pdf'):?>pdf.gif<?php elseif($program['file_type']=='doc'||$program['file_type']=='docx'):?>doc.gif<?php elseif($program['file_type']=='ppt'||$program['file_type']=='pptx'):?>ppt.png<?php elseif($program['file_type']=='xls'||$program['file_type']=='xlsx'):?>xls.png<?php else:?>file.png<?php endif;?>">
									<a href="<?php if($program['is_exist']==1):?><?php echo U('Vip/VipStudents/download',array('id'=>$program['id'],'type'=>'3'));?><?php else:?>#none<?php endif;?>" <?php if($program['is_exist']==0):?>title="文件不存在" onclick="javascript:alert('文件不存在');"<?php endif;?>><?php echo $program['student_name'].'培养方案'.$program['instime']?></a>&nbsp;&nbsp;&nbsp;-->
								<?php if($program['program_arr']):?>
									<?php foreach ($program['program_arr'] as $k=>$val):?>
										<p><?php echo $val['url']?>&nbsp;&nbsp;
										   <?php if($val['is_download']==1):?>
											   	<a href="<?php echo U('Vip/VipStudents/download',array('id'=>$program['id'],'type'=>3,'order'=>$k))?>" class="blue">下载</a>
											   <?php endif;?>&nbsp;&nbsp;
											   <!--<?php if($val['is_preview']==1):?>
											   		<a href="#" class="blue" onclick="testMessageBox_view_file(event,'/vip/vip_content/view_file/url/<?php echo str_replace('/','|',str_replace('.','_',$val['preview_url']))?>')">预览</a>
											   <?php endif;?>-->
											</p>
									<?php endforeach;?>
								<?php endif;?>
									
								</td>
								<td><?php if($program['from_type']==1):?>微信<?php else:?>PC<?php endif;?></td>
								<td><a href="#none" onclick="del_program('#p_<?php echo $program['id'];?>','<?php echo U('Vip/VipStudents/del_program',array('id'=>$program['id']));?>')" class="blue">删除</a></td>
							</tr>
						<?php endforeach?>
					<?php endif;?>
					</table>
				</div>
				
		</div>
		<div title="留言板" data-options="closable:false" style="padding:20px;">
			<!--留言板-->
			<?php if(!empty($heluInfo['comment'])):?>
			<p>
				<span class="blue"><?php echo $userInfo['real_name'];?>老师：</span> <?php echo $heluInfo['comment'];?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="gray"><?php echo $heluInfo['lasttime'];?></span>
			</p>
			<?php else:?>
				<span class="gray">暂无留言~</span>
			<?php endif;?>
		</div>
		<div title="学员信息" data-options="closable:false" style="text-align:center;"><br>
			<!--学员信息-->
			<p class="f_20">(<span class="error"><?php echo $studentInfo['sdeptname'];?></span>)学员情况登记</p>
			<div id="content" class="center">
			<table border="1" cellpadding="0" cellspacing="0" width="90%" class="tableInfo">
				<tr>
					<td width="15%">签约日期：</td><td width="18%"><?php echo $studentInfo['dtdate']?></td>
					<td width="15%">咨询师：</td><td width="19%"><?php echo $studentInfo['soperatorname'];?></td>
					<td width="15%">电话：</td><td width="18%"></td>
				</tr>
				<tr>
					<td >学生基本信息：</td>
					<td colspan="5" align="left">
						<p>姓名：<?php echo $studentInfo['sstudentname'];?></p>
						<p>性别：<?php echo ($studentInfo['ngender']==1)?'男':'女';?></p>
						<p>年级：<?php echo $studentInfo['gradename'];?></p>
						<p>所在学校：<?php echo $studentInfo['sschool'];?></p>
						<p>出生日期：<?php echo $studentInfo['dtbirthday'];?></p>
						<p>兴趣爱好：<?php echo $studentInfo['shobby'];?></p>
						<p>学员性格：<?php echo $studentInfo['scharacter'];?></p>
						<p>学员班级排名：<?php echo $studentInfo['nrank'];?></p>
						<p>学员空闲时间：<?php echo $studentInfo['sfeetime'];?></p>
						<p>家长姓氏/电话1：<?php echo $studentInfo['sparents1name']?>
							<?php if($studentInfo['nparents1relation']):?>(<?php echo $studentInfo['nparents1relation']?>)<?php endif;?>
							<?php echo $studentInfo['sparents1phone'];?></p>
						<p>家长姓氏/电话2：<?php echo $studentInfo['sparents2name']?>
							<?php if($studentInfo['nparents2relation']):?>(<?php echo $studentInfo['nparents2relation']?>)<?php endif;?>
							<?php echo $studentInfo['sparents2phone'];?></p>
						<p>家长邮箱：<?php echo $studentInfo['semail'];?></p>
					</td>
				</tr>
				<tr>
					<td >课程信息：</td>
					<td colspan="5" align="left">
						<p>辅导科目：<?php echo $studentInfo['skechengname'];?></p>
						<p>辅导方式：一对一</p>
						<p>班主任：<?php echo $studentInfo['sclassadvisername'];?></p>
						<p>入学成绩测试：<?php echo $studentInfo['stestscores'];?></p>
						<p>教材版本：<?php echo $studentInfo['stextbookversion'];?></p>
						<p>目前学习情况：<?php echo $studentInfo['scurrentlylearning'];?></p>
						<p>首次课日期：<?php echo $studentInfo['dtfristlessondate'];?></p>
						<p>首次课首选时间段：<?php echo $studentInfo['sfristlessontime1'];?></p>
						<p>首次课次选时间段：<?php echo $studentInfo['sfristlessontime2'];?></p>
						<p>首次课内容建议：<?php echo $studentInfo['sfristlessonsuggested'];?></p>
					</td>
				</tr>
				<tr>
					<td >家长期望/建议/要求：</td>
					<td colspan="5" align="left">
						<p>家长建议辅导计划：<?php echo $studentInfo['sparentssuggested'];?></p>
						<p>家长期望目标：<?php echo $studentInfo['sparentsexpect'];?></p>
						<p>对老师要求：<?php echo $studentInfo['sparentsrequest'];?></p>
					</td>
				</tr>
			</table>
			</div>
		</div>
	</div>
	</div>
</div>
</body>
</html>