<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="format-detection" content="telephone=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<title>小班课</title>
	<link rel="stylesheet" type="text/css" href="/static/css/weixinBase.css">
	<script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
	<style>
		body{ margin-bottom: 60px;}
	</style>
</head>
<body>
<div class="auto courseinfo">
	<figure class="tour pb8">
			<img src="<?php if(!empty($teacherInfo->thumb)){ echo $teacherInfo->thumb; }else{ echo '/static/images/classDetail.jpg';}?>" class="w30 bor-green">
			<figcaption class="w70">
				<h2 class="c-red f14 wellipsis"><?php echo $details->ClassName;?></h2>
				<span class="c-green" style="display:none;font-size:12px">(<?php echo $details->ClassCode?>)</span>
				<p class="money">
					<i>¥<?php echo $details->RealPrice;?></i>
					<del class="f10 c-gray" style="text-decoration:none;">&nbsp;<!-- ¥<?php echo $details->OriginalPrice;?> --></del>
				</p>
				<p><i class="c-green w20">老师姓名</i><span class="w73"><?php echo $details->TeacherName;?></span></p>
				<p><i class="c-green w20">年级</i><span class="w73"><?php echo $details->FitClass;?></span></p>
				<p><i class="c-green w20">开课日期</i><span class="w73"><?php echo substr($details->BeginOn,0,10);?></span></p>
				<p><i class="c-green w20">课次节数</i><span class="w73"><?php echo $details->LessonNum;?>节</span></p>
				<p><i class="c-green w20">上课校区</i><span class="w73"><?php echo $details->DeptName;?></span></p>
				<?php if($details->is_sign == 0){ ?>
					<p><i class="c-green w20">剩余人数</i><span class="w73"><?php $num =$details->NowNum+$details->orderNum; echo ($details->LimitNum)-$num;?></span></p>
				<?php }?>
			</figcaption>
			<div class="clear"></div>
	</figure>
	<div class="courseinfo_bottom">
		<a href="http://ali142b.looyu.com/chat/chat/p.do?c=33741&f=83285&n=gaosihujiao2" class="an anbg1">客服</a>
		<span class="xian"></span>
		
		<?php if($details->is_store == 0){?>
			<a href="javascript:;" class="an anbg"><span onclick="getCollection(this,<?php echo $details->Id ?>,1)">收藏</span><i class="hide" onclick="quxiao(this,<?php echo $details->Id ?>,1)">取消</i></a>
		<?php }else{?>
			<a href="javascript:;" class="an anbg anbgen"><span  onclick="getCollection(this,<?php echo $details->Id ?>,1)" class="hide">收藏</span><i class="hide" style="display: inline;" onclick="quxiao(this,<?php echo $details->Id ?>,1)">取消</i></a>
		<?php }?>
		
		<?php 
			if($details->is_sign == 0){
				if($details->is_order == 1){
					echo '<a href="javascript:;" class="btn_red btn_bm">已报名</a>';
				}else{
		?>
			<a href="<?php echo U('Vip/GsWeixin/classEnroll',array('id'=>$details->Id))?>" class="btn_red btn_bm">立即报名</a>
		<?php 
			}}else{	
		?>
			<a href="javascript:;" class="btn_gray btn_bm">班级已满</a>
		<?php }?>
	</div>
</div>
<div class="auto tab_nav ma_top210 h56">
	<ul id="pagenavi" class="page">
		<li class="w33 active">详情</li>
      	<li class="w33">教师简介</li>
        <li class="w33">相关课程</li>
	</ul>
	<div id="slider" class="swipe">
		<ul class="auto tab_list">
			<li class="li_list">
				<?php if(!empty($details->Description) || !empty($details->CourseInfos)){?>
				<div class="auto list_con">
					<?php 
						$descr = explode('；', $details->Description);
						foreach($descr as $val){

							echo '<p class="wz">'.$val.'</p>';
						}
					?>
					<div class="detail">
						<?php foreach($details->CourseInfos as $info){?>
						<div class="kctime">
							<div class="time">
								<?php echo substr($info->StartOn,0,10)?>
								<br />
								<?php echo substr($info->dtStart,11,5)?>-<?php echo substr($info->dtEnd,11,5)?>
							</div> 
							<div class="kctime_con">
								<div class="crile"></div>
								<h3><?php echo $info->CourseNo?></h3>
								<p class="tm"><?php echo $info->Outline?></p>
							</div>
							<div class="clear"></div>
						</div>
						<?php }?>
					</div>	
				</div>
				<?php }else{?>
					<div class="auto list_con">
						<p class="wz">
							暂无任何详情信息...
						</p>
					</div>	
				<?php }?>
			</li>
			<!--详情-->
			<li class="li_list hide">
				<div class="auto list_con">
					<p class="wz">
						<?php echo empty($teacherInfo->info)?'':$teacherInfo->info;?>
					</p>
				</div>
			</li>
			<!--教师介绍-->
			<li class="li_list hide">
				<div class="auto list_con">
					<p class="other">猜你感兴趣的  相关课程</p>
				</div>
				<?php foreach($relevantClass as $key=>$class){
						if($key < 6){
				?>
				<div class="course auto">
					<a href="<?php echo U('Vip/GsWeixin/classDetails',array('id'=>$class->Id))?>">
						<div class="shctitle f14 c-red">
							<?php echo $class->ClassName;?>
							<span><i class="c-fff b-red">热</i></span>
						</div>
					</a>
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
								<a href="<?php echo U('Vip/GsWeixin/classEnroll',array('id'=>$class->Id))?>" class="btn_red btn_cou">立即报名</a>
						</figcaption>
						<div class="clear"></div>
					</figure>
				</div>
				<?php }}?>
			</li>
			<!--相关课程-->
		</ul>
	</div>
</div>

  <script>
        $(function(){
            $('#pagenavi li').click(function(){
                var index = $(this).index();
                $('#slider .li_list').eq(index).show().siblings('.li_list').hide(0);
                $(this).addClass('active').siblings().removeClass('active');
               
            });
			$('.wellipsis').click(function(){
				$(this).next('span').slideToggle("slow"); 
			})
        })

        function getCollection(obj,info_id,type){
        	if(info_id > 0){

    			$.post(
    					"<?php echo U('Vip/GsWeixin/ajaxCollect');?>",
    					{info_id:info_id,type:type},
    					function(data){
    						if(data.status == 1){
    							$(obj).hide();
    							$(obj).next('i').show();
    							$(obj).parents('a').addClass('anbgen').siblings().removeClass('anbgen');
    							alert(data.Message);
    						}else{
    							alert(data.Message);
    						}
    			},'json')
        	}else{
        		alert('收藏失败');
        	}
        }
        function quxiao(obj,info_id,type){
        	if(info_id > 0){

    			$.post(
    					"<?php echo U('Vip/GsWeixin/ajaxCancel');?>",
    					{info_id:info_id,type:type},
    					function(data){
    						if(data.status == 1){
    							$(obj).hide();
    							$(obj).prev('span').show();
    							$(obj).parents('a').removeClass('anbgen');
    							alert(data.successMsg);
    						}else{
    							alert(data.Message);
    						}
    			},'json')
        	}else{
        		alert('取消失败');
        	}
        }
   </script>

<!--nav-->
<div class="home"><a href="<?php echo U('Vip/GsWeixin/index');?>"></a></div>
<div class="nav">
	<ul>
		<li><a href="<?php echo U('Vip/GsWeixin/smallClass')?>" class="xb_green"><span class="c-green">小班课</span></a></li>
		<li><a href="<?php echo U('Vip/GsWeixin/oneToOne')?>" class="vip"><span>1对1</span></a></li>
		<li><a href="<?php echo U('Vip/GsWeixin/doActivity');?>" class="hd"><span>活动</span></a></li>
		<li><a href="<?php echo U('Vip/GsWeixin/makeDiagnosis');?>" class="zhd"><span>预约诊断</span></a></li>
	</ul>
</div>

</body>
</html>