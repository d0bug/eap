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
	<script src="/static/js/jquery-2.1.1.min.js"></script>
	<style>
		body{ margin-bottom: 60px; overflow:hidden;}
		.seach{display: none;}
	</style>
</head>
<body>
<div class="seach auto">
	<input type="text" value="" placeholder="请输入课程名称/年级/科目" class="seacher">
</div>

<div class="down_option top0">
	<div id="search-bd" class="down_con">
        <ul>

            <?php if(!empty($_GET['subject'])){foreach($kcList->Subject_list as $subject){?>
					<?php if($_GET['subject'] == $subject->id){?>
						<li class="w50"><?php echo $subject->caption?><i></i></li>
					<?php }?>
			<?php }}else{?>
				<li class="w50">学科<i></i></li>
			<?php }?>
			<?php if(!empty($_GET['grade'])){foreach($kcList->Grade_list as $grade){?>
            	<?php if($_GET['grade'] == $grade->id){?>
					<li class="w50"><?php echo $grade->caption?><i></i></li>
				<?php } ?>	
			<?php }}else{?>
				<li class="w50">年级<i></i></li>
			<?php }?>	
        </ul>
    </div>
	<div id="search-hd" class="search-hd">
        <!-- <ul class="downzhan top40 pholder hide">
			<li><a href="<?php echo U('Vip/GsWeixin/oneToOne',array('subject'=>$_GET['subject']));?>">全年级</a></li>
			<?php foreach($kcList->Grade_list as $grade){?>
				<li>
					<a href="<?php echo U('Vip/GsWeixin/oneToOne',array('grade'=>$grade->id,'subject'=>$_GET['subject']));?>">
						<?php echo $grade->caption?>
					</a>
				</li>
			<?php }?>
		</ul> -->
		<ul class="downzhan top40 pholder hide">
			<li><a href="<?php echo U('Vip/GsWeixin/oneToOne',array('grade'=>$_GET['grade']));?>">全学科</a></li>
			<?php foreach($kcList->Subject_list as $subject){?>
				<li>
					<a href="<?php echo U('Vip/GsWeixin/oneToOne',array('grade'=>$_GET['grade'],'subject'=>$subject->id));?>">
						<?php echo $subject->caption?>
					</a>
				</li>
			<?php }?>
		</ul>
		<div class="downzhan top40 pholder hide">
            <div class="downzhan_con bb_open">
            	<span class="dz_icon <?php if(empty($_GET['grade'])){echo 'on_green';}?>">
            		<a <?php if(empty($_GET['grade'])){echo 'class="c-fff"';}?> href="<?php echo U('Vip/GsWeixin/oneToOne',array('subject'=>$_GET['subject']));?>">全部</a>
            	</span>
            </div>
			
			<div class="downzhan_con bb_open">
            	<h3>小学</h3>

                <div class="classlist">
	                <?php 
	                	foreach($kcList->Grade_list as $grade){
	                		if($grade->sort_id <=6){
	                ?>
	                	<span class="dz_icon <?php if($_GET['grade'] == $grade->id){echo 'on_green';}?>">
	                		<a <?php if($_GET['grade'] == $grade->id){echo 'class="c-fff"';}?> href="<?php echo U('Vip/GsWeixin/oneToOne',array('grade'=>$grade->id,'subject'=>$_GET['subject']));?>">
								<?php echo $grade->caption?>
							</a>
						</span>
					<?php }}?>	
                </div>
            </div>

            <div class="downzhan_con bb_open">
            	<h3>初中</h3>
                <div class="classlist">
                	<?php 
	                	foreach($kcList->Grade_list as $grade){
	                		if($grade->sort_id > 6 && $grade->sort_id <=9){
	                ?>
	                	<span class="dz_icon">
	                		<a href="<?php echo U('Vip/GsWeixin/oneToOne',array('grade'=>$grade->id,'subject'=>$_GET['subject']));?>">
								<?php echo $grade->caption?>
							</a>
						</span>
					<?php }}?> 
                </div>
            </div>
             <div class="downzhan_con">
            	<h3>高中</h3>
                <div class="classlist">
                	<?php 
	                	foreach($kcList->Grade_list as $grade){
	                		if($grade->sort_id > 9 && $grade->sort_id <=14){
	                ?>
	                	<span class="dz_icon">
	                		<a href="<?php echo U('Vip/GsWeixin/oneToOne',array('grade'=>$grade->id,'subject'=>$_GET['subject']));?>">
								<?php echo $grade->caption?>
							</a>
						</span>
					<?php }}?>
                </div>
            </div>
         </div>
	</div>
</div>
<div class="courseList">
	<?php if( !empty($kcList->AppendData) ){
			foreach($kcList->AppendData as $rows){
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
				<a href="<?php echo U('Vip/GsWeixin/courseMake',array('id'=>$rows->uid));?>" class="btn_red btn_cou">立即预约</a>
			</figcaption>
			<div class="clear"></div>
		</figure>
	</div>
	<?php 	}
		}else{
	?>
	<div class="course auto">
		<span style="font-size:12px;text-align:center;display:block;marign-top:10px;margin-top:10px">对不起！暂无课程信息....</span>
	</div>
	<?php }?>
</div>
<div class="dropload-load"></div>

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
    //通用头部搜索切换
    $('#search-bd li').click(function(){
        var index = $(this).index();
        $('#search-hd .pholder').eq(index).slideToggle("slow").siblings('.pholder').hide(0);
        $(this).toggleClass('selected').siblings().removeClass('selected');
      
    }
		);
  
})
</script>
<script type="text/javascript">
	var high = 0;
	var isrun = true;
	var tops = 0;
	function page_init(){
		high = $('.courseList').height();
		tops = parseInt(high);
	}
	//获取滚动条当前的位置 
	function getScrollTop() { 
	var scrollTop = 0; 
	if (document.documentElement && document.documentElement.scrollTop) { 
	scrollTop = document.documentElement.scrollTop; 
	} 
	else if (document.body) { 
	scrollTop = document.body.scrollTop; 
	} 
	return scrollTop; 
	} 

	//获取当前可是范围的高度 
	function getClientHeight() { 
	var clientHeight = 0; 
	if (document.body.clientHeight && document.documentElement.clientHeight) { 
	clientHeight = Math.min(document.body.clientHeight, document.documentElement.clientHeight); 
	} 
	else { 
	clientHeight = Math.max(document.body.clientHeight, document.documentElement.clientHeight); 
	} 
	return clientHeight; 
	} 

	//获取文档完整的高度 
	function getScrollHeight() { 
	return Math.max(document.body.scrollHeight, document.documentElement.scrollHeight); 
	}

	window.onscroll = function () {

	if (getScrollTop() + getClientHeight() == getScrollHeight()) { 
		//tops = getScrollHeight();
		
		$('.dropload-load').html('<span class="loading"></span>加载中...');
		setTimeout(function(){
			isrun = true;
		},15000);
		if(isrun==true){
			isrun = false;
			$.post(
					'<?php 
						echo 
						U("Vip/GsWeixin/ajaxCourse",
							array(
								"grade"=>$_GET["grade"],
								"subject"=>$_GET["subject"]
								)
						);
					?>',
					{tops:tops,high:high},function(data){ 
				if(data !=""){
					$('.dropload-load').html("");
					tops += high;
					$('.courseList').append(data);
					isrun = true;
				}else{
					$('.dropload-load').html("");
				}
			})
		}else{
			$('.dropload-load').html("");
		}
	} 
	}

	$(document).ready(function(){
		page_init();
	});
</script>

</body>
</html>

