<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/essay.js"></script>
<link href="/static/css/essay.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
	<div id="main">
		<h2>优秀作文选&nbsp;&nbsp;&nbsp;&nbsp;
			<span class="font14">
				<?php if($order=='asc'):?>
					<a href="<?php echo U('Essay/Essay/excellentList',array('key_name'=>'thumb_name','order'=>'desc'))?>" >名称<?php if($key_name=='thumb_name'):?><img src="/static/images/asc.png" align="absmiddle"><?php endif;?></a>|
					<a href="<?php echo U('Essay/Essay/excellentList',array('key_name'=>'instime','order'=>'desc'))?>" >时间<?php if($key_name=='instime'):?><img src="/static/images/asc.png" align="absmiddle"><?php endif;?></a>
				<?php else:?>
					<a href="<?php echo U('Essay/Essay/excellentList',array('key_name'=>'thumb_name','order'=>'asc'))?>" >名称<?php if($key_name=='thumb_name'):?><img src="/static/images/desc.png" align="absmiddle"><?php endif;?></a>|
					<a href="<?php echo U('Essay/Essay/excellentList',array('key_name'=>'instime','order'=>'asc'))?>" >时间<?php if($key_name=='instime'):?><img src="/static/images/desc.png" align="absmiddle"><?php endif;?></a>
				<?php endif;?>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="checkbox" name="checkAll" id="checkAll" value="1"  class="checkAll">全选
				<input type="button" value="移出 “优秀作文选”" onclick="do_excellent('<?php echo U('Essay/Essay/do_excellent')?>','delete','','','','<?php echo U('Essay/Essay/excellentList',array('key_name'=>$key_name,'order'=>$order,'p'=>$curPage))?>')">
			</span>
		</h2>
		<?php if(!empty($excellentList)):?>
			<div id="preview" class="preview" style="margin-left:10px;">
				<?php foreach($excellentList as $key=>$excellent):?>
					<li><a href="#" onclick="testMessageBox_show_essayImg(event,'<?php echo U('Essay/Essay/show_essayImg',array('essay_id'=>$excellent['essay_id']));?>')">
							<img src="<?php echo $excellent['avatar'];?>" width="160" height="200">
						</a>
						<div class="img_name"><input type="checkbox" id="excellent_<?php echo $key;?>" name="excellent[]" value="<?php echo $excellent['essay_id'];?>"> 
						<?php echo $excellent['class_name'].'-'.$excellent['speaker_number'].'-'.$excellent['student_name'];?></div>
					</li>
				<?php endforeach?>	
			</div><br>
			<div>
				<span class="font14">
					<input type="checkbox" name="checkAll" id="checkAll" value="1" class="checkAll">全选
					<input type="button" value="移出 “优秀作文选”" onclick="do_excellent('<?php echo U('Essay/Essay/do_excellent')?>','delete','','','<?php echo U('Essay/Essay/excellentList',array('key_name'=>$key_name,'order'=>$order,'p'=>$curPage))?>')">
				</span>
			</div><br>
			<div id="pageStr"><?php echo $showPage;?></div>
		<?php endif;?>
	</div>
</div>
</body>
</html>