<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="format-detection" content="telephone=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<title>收藏夹</title>
	<link rel="stylesheet" type="text/css" href="/static/css/weixinBase.css">
	<script src="/static/js/jquery-2.1.1.min.js"></script>
	
</head>
<body>
<?php 
	if(!empty($collectRow)){
	if(!empty($collectRow['class'])){

		foreach($collectRow['class'] as $class){
?>
	<div class="course auto">
		
		<div class="ordertitle f14 ">
			<a href="<?php echo U('Vip/GsWeixin/classDetails',array('id'=>$class->Id))?>">
				<div class="shctitle f14 c-red"><?php echo $class->ClassName;?></div>
			</a>
            <a onclick="quxiao(this,<?php echo $class->Id;?>,1)" class="J_homeworkcon-queqin del"></a>
        </div>
		<figure class="tour">
			<a href="<?php echo U('Vip/GsWeixin/classDetails',array('id'=>$class->Id))?>">
				<img src="/static/images/class.jpg" class="w43">
			</a>
			<figcaption class="w57">
				<a href="<?php echo U('Vip/GsWeixin/classDetails',array('id'=>$class->Id))?>">
					<p><i class="c-green w25">老师姓名</i><span class="w73"><?php echo $class->TeacherName;?></span></p>
					<p><i class="c-green w25">开课日期</i><span class="w73"><?php echo substr($class->BeginOn,0,10);?></span></p>
					<p><i class="c-green w25">课次节数</i><span class="w73"><?php echo $class->LessonNum;?>节</span></p>
					<p><i class="c-green w25">上课校区</i><span class="w73"><?php echo $class->DeptName;?></span></p>
				</a>
			</figcaption>
			<div class="clear"></div>
		</figure>
	</div>
<?php 
	}}
	if(!empty($collectRow['oneToOne'])){
		foreach ($collectRow['oneToOne']  as $rows) {
			
?>
	<div class="course auto" id="class">
		<div class="ordertitle f14 ">
            <a onclick="quxiao(this,'<?php echo $rows->uid;?>',2)" class="J_homeworkcon-queqin del"></a>
        </div>
		<figure class="tour">
			<a href="<?php echo U('Vip/GsWeixin/courseDetail',array('id'=>$rows->uid));?>">
				<img src="
					<?php 
						if($rows->thumb  =='course_default.png')
						{ 
							echo '/static/images/vip.png';
						}else{
							echo 'http://www.gaosivip.com'.$rows->thumb;
						}
					?>" 
				class="w38">
			</a>
			<figcaption class="w60">
				<a href="<?php echo U('Vip/GsWeixin/courseDetail',array('id'=>$rows->uid));?>">
					<h2 class="c-red f14 wellipsis pb5"><?php echo $rows->title;?></h2>
					<p class="w50 f10 left">
						<i class="c-green pr10">年级</i>
						<?php echo $rows->grade_caption;?>
					</p>
					<p class="w50 f10 left">
						<i class="c-green pr10">科目</i>
						<?php echo $rows->subject_caption;?>
					</p>
				</a>			
			</figcaption>
			<div class="clear"></div>
		</figure>
	</div>
<?php }}}else{?>
	<div class="course auto">
		<figure class="tour">
			暂无收藏信息~
		</figure>
	</div>
<?php } ?>
<!--nav-->
<div class="home" style="bottom:8px;"><a href="<?php echo U('Vip/GsWeixin/index');?>"></a></div>
<script type="text/javascript">
	function quxiao(obj,info_id,type){

    	if(info_id > 0 || info_id !=''){
			$.post(
					"<?php echo U('Vip/GsWeixin/ajaxCancel');?>",
					{info_id:info_id,type:type},
					function(data){
						if(data.status == 1){
							$(obj).parents('.course').fadeOut();
						}else{
							alert(data.Message);
						}
			},'json')
    	}else{
    		alert('删除失败');
    	}
    }
</script>
</body>
</html>