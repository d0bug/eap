<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="format-detection" content="telephone=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<title>1对1</title>
	<link rel="stylesheet" type="text/css" href="/static/css/weixinBase.css">
	<script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
	<style>
		body{ margin-bottom: 60px;}
	</style>
</head>
<body>
<div class="auto courseinfo">
	<figure class="tour pb8">
		<a href="javascript:;">
			<img src="
				<?php 
					if($detail->thumb  =='course_default.png')
					{ 
						echo '/static/images/vip.png';
					}else{
						echo 'http://www.gaosivip.com'.$detail->thumb;
					}
				?>"
			class="w38">
		</a>
		<figcaption class="w60">
			<a href="javascript:;">
				<h2 class="c-red f14 left wellipsis pb5"><?php echo $detail->title;?></h2>
				<p class="f10 w100 left"><i class="c-green  pr10">年级</i><?php echo $detail->grade_caption;?></p><p class="f10 w100 left"><i class="c-green pr10">科目</i><?php echo $detail->subject_caption;?></p>
			</a>
		</figcaption>
		<div class="clear"></div>
	</figure>

	<div class="courseinfo_bottom">
		<a href="http://ali142b.looyu.com/chat/chat/p.do?c=33741&f=83285&n=gaosihujiao2" class="an anbg1">客服</a>
		<span class="xian"></span>

		<?php if($detail->is_store == 0){?>
			<a href="javascript:;" class="an anbg"><span onclick="getCollection(this,'<?php echo $detail->uid;?>',2)">收藏</span><i class="hide" onclick="quxiao(this,'<?php echo $detail->uid;?>',2)">取消</i></a>
		<?php }else{?>
			<a href="javascript:;" class="an anbg"><span onclick="getCollection(this,'<?php echo $detail->uid;?>',2)" class="hide">收藏</span><i onclick="quxiao(this,'<?php echo $detail->uid;?>',2)">取消</i></a>
		<?php }?>
		<a href="<?php echo U('Vip/GsWeixin/courseMake',array('id'=>$detail->uid));?>" class="btn_red btn_bm">立即预约</a>
	</div>
</div>
<div class="auto tab_nav ma_top170 h65">
	<ul id="pagenavi" class="page">
		<li class="w50 active">详情</li>
        <li class="w50" style="border-right:1px solid #fff;border-radius:0 3px 0 0;">相关课程</li>
	</ul>
	<div id="slider" class="swipe">
		<ul class="auto tab_list">
			<li class="li_list li_content" style="width:430pt">
				<?php echo $detail->content;?>
				
			</li>
			<!--详情-->
			<li class="li_list hide">
				<p class="other">浏览此课程的用户还看了以下课程</p>
				<?php
					if( !empty($relevant) ){ 
					foreach($relevant as $key=>$rows){
						if($key < 5){
				?>
				<div class="course auto">
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
								<h2 class="c-red f14 wellipsis pb5"><?php echo $rows->title?></h2>
								<p class="w50 f10 left">
									<i class="c-green pr10">年级</i><?php echo $rows->grade_caption?>
								</p>
								<p class="w50 f10 left"><i class="c-green pr10">科目</i><?php echo $rows->subject_caption?></p>
							</a>
							<a href="<?php echo U('Vip/GsWeixin/courseMake',array('id'=>$rows->uid));?>" class="btn_red btn_cou">立即预约</a>
						</figcaption>
						<div class="clear"></div>
					</figure>
				</div>
				<?php 
						}
					}
					}else{
						echo '<div class="course auto">暂无相关课程信息...</div>';
					}
				?>
			</li>
			<!--相关课程-->
		</ul>
	</div>
</div>



<!--nav-->
<div class="home"><a href="<?php echo U('Vip/GsWeixin/index');?>"></a></div>
<div class="nav">
	<ul>
		<li><a href="<?php echo U('Vip/GsWeixin/smallClass')?>" class="xb"><span>小班课</span></a></li>
		<li><a href="<?php echo U('Vip/GsWeixin/oneToOne')?>" class="vip_green"><span class="c-green">1对1</span></a></li>
		<li><a href="<?php echo U('Vip/GsWeixin/doActivity');?>" class="hd"><span>活动</span></a></li>
		<li><a href="<?php echo U('Vip/GsWeixin/makeDiagnosis');?>" class="zhd"><span>预约诊断</span></a></li>
	</ul>
</div>


<script type="text/javascript">
	$(function(){
	
        $('#pagenavi li').click(function(){
            var index = $(this).index();
            $('#slider .li_list').eq(index).show().siblings('.li_list').hide(0);
            $(this).addClass('active').siblings().removeClass('active');
           
        });
    })
    function getCollection(obj,info_id,type){
		if(info_id != ''){
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
        	if(info_id != ''){
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
</body>
</html>