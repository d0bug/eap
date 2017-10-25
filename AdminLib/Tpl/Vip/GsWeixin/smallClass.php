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
	<script src="/static/js/jquery-2.1.1.min.js"></script>
	<script src="/static/js/jquery.cookie.js"></script>    
	<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
	<style>
		body{ margin-bottom: 60px;}
	</style>
	<?php 
		$t= mktime();
		$signature = sha1('jsapi_ticket='.$lang['jsapi_ticket'].'&noncestr='.$lang['nonceStr'].'&timestamp='.$t.'&url='.$lang['url']);
	?>
	<script type="text/javascript">
		var latitude = "<?php echo empty($_GET['latitude'])?$_SESSION['latitude']:$_GET['latitude'];?>";
		var longitude = "<?php echo empty($_GET['longitude'])?$_SESSION['longitude']:$_GET['longitude'];?>";
		var noLocation = $.cookie("Location");
		if(noLocation == null){
			wx.config({
			    debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
			    appId: '<?php echo $lang['appid'];?>', // 必填，公众号的唯一标识
			    timestamp: '<?php echo $t;?>', // 必填，生成签名的时间戳
			    nonceStr: '<?php echo $lang['nonceStr'];?>', // 必填，生成签名的随机串
			    signature: '<?php echo $signature;?>',// 必填，签名，见附录1
			    jsApiList: ['getLocation'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
			});
			wx.ready(function () {
				wx.getLocation({
				    type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
				    success: function (res) {
				        if(latitude == '' || longitude == ''){
				        	window.location.href="/vip/gs_weixin/smallClass/latitude/"+res.latitude+"/longitude/"+res.longitude+'/'; 	
				        }
				    },
				    cancel: function (){
				    	var cookietime = new Date(); 
						cookietime.setTime(date.getTime() + (30 * 30 * 1000));//coockie保存半小时
						$.cookie("Location", "no",{expires:cookietime}); 
				    }
				});
			});
		}
		
	</script>
</head>
<body>
<div class="seach auto fixed">
	<form id="search" action="" method="post">
		<input type="text" value="<?php echo $_POST['ClassName'];?>" name="ClassName" placeholder="请输入班课或教师名称" class="seacher">
		<input type="button" value="搜索" class="btn_yell btn_seach">
	</form>
</div>
<div class="down_option top56 pt10">
        <div id="search-bd" class="down_con">
            <ul>
            	<?php if(empty($_GET['SubjectName'])){
            		echo '<li class="w33">科目<i></i></li>';
            	}else{
            		foreach($subjectList as $subject){
            			if($subject['number'] == $_GET['SubjectName']){
            				echo '<li class="w33">'.$subject['subject_name'].'<i></i></li>';
            			}
            		}
            	}?>
                
                <?php if(empty($_GET['FitClass'])){
            		echo '<li class="w33">年级<i></i></li>';
            	}else{
            		if($_GET['FitClass'] > 10){
            			$FitClass = $_GET['FitClass']-2;
            		}else{
            			$FitClass = $_GET['FitClass']-1;
            		}
            		echo '<li class="w33">'.$gradeList[$FitClass]['caption'].'<i></i></li>';
            	}?>
                <?php if(empty($_GET['DeptName'])){
            		echo '<li class="w33">校区<i></i></li>';
            	}else{
            		echo '<li class="w33">'.$_GET['DeptName'].'<i></i></li>';
            	}?>
            </ul>
        </div>
        <div id="search-hd" class="search-hd">
            <ul class="downzhan top96 pholder hide">
                 <li><a href="<?php echo U('Vip/GsWeixin/smallClass',array('DeptName'=>$_GET['DeptName'],'FitClass'=>$_GET['FitClass']));?>">全学科</a></li>
				<?php foreach($subjectList as $subject){?>
					<li>
						<a <?php if($_GET['SubjectName'] == $subject['number']){ echo 'class="c-green"';}?> href="<?php echo U('Vip/GsWeixin/smallClass',array('DeptName'=>$_GET['DeptName'],'FitClass'=>$_GET['FitClass'],'SubjectName'=>$subject['number']));?>">
							<?php echo $subject['subject_name']?>
						</a>
					</li>
				<?php }?>
             </ul>
             <!--科目结束-->
              <div class="downzhan top96 pholder hide">
                <div class="downzhan_con bb_open">
                	<span class="dz_icon <?php if(empty($_GET['FitClass'])){ echo 'on_green';}?>">
                		<a <?php if(empty($_GET['FitClass'])){ echo 'class="c-fff"';}?> href="<?php echo U('Vip/GsWeixin/smallClass',array('DeptName'=>$_GET['DeptName'],'SubjectName'=>$_GET['SubjectName']));?>">全部</a>
                	</span>
                </div>
				<div class="downzhan_con bb_open">
                	<h3>小学</h3>
                    <div class="classlist">
                    	<?php 
                    		foreach($gradeList as $grade){
                    			if($grade['sort_id'] <=6){
                    	?>
                    		<span class="dz_icon <?php if($_GET['FitClass'] == $grade['sort_id']){ echo 'on_green';}?>">
                    			<a <?php if($_GET['FitClass'] == $grade['caption']){ echo 'class="c-fff"';}?> href="<?php echo U('Vip/GsWeixin/smallClass',array('DeptName'=>$_GET['DeptName'],'FitClass'=>$grade['sort_id'],'SubjectName'=>$_GET['SubjectName']));?>">
                    				<?php echo $grade['caption']?>
                    			</a>
                    		</span>
                      	<?php }}?>
                    </div>
                </div>
                <div class="downzhan_con bb_open">
                	<h3>初中</h3>
                    <div class="classlist">
                    	<?php 
                    		foreach($gradeList as $grade){
                    			if($grade['sort_id'] > 6 && $grade['sort_id'] <=9){
                    	?>
                    		<span class="dz_icon <?php if($_GET['FitClass'] == $grade['sort_id']){ echo 'on_green';}?>">
                    			<a <?php if($_GET['FitClass'] == $grade['caption']){ echo 'class="c-fff"';}?> href="<?php echo U('Vip/GsWeixin/smallClass',array('DeptName'=>$_GET['DeptName'],'FitClass'=>$grade['sort_id'],'SubjectName'=>$_GET['SubjectName']));?>">
                    				<?php echo $grade['caption']?>
                    			</a>
                    		</span>
                      	<?php }}?>
                    </div>
                </div>
                 <div class="downzhan_con">
                	<h3>高中</h3>
                    <div class="classlist">
                    	<?php 
                    		foreach($gradeList as $grade){
                    			if($grade['sort_id'] > 9 && $grade['sort_id'] <=14){
                    	?>
                    		<span class="dz_icon <?php if($_GET['FitClass'] == $grade['sort_id']){ echo 'on_green';}?>">
                    			<a <?php if($_GET['FitClass'] == $grade['caption']){ echo 'class="c-fff"';}?> href="<?php echo U('Vip/GsWeixin/smallClass',array('DeptName'=>$_GET['DeptName'],'FitClass'=>$grade['sort_id'],'SubjectName'=>$_GET['SubjectName']));?>">
                    				<?php echo $grade['caption']?>
                    			</a>
                    		</span>
                      	<?php }}?>
                    </div>
                </div>
             </div>
             <!--年级结束-->
             <div class="downzhan top96 pholder hide">
                 <div class="school_nav" id="school_nav">
                 	<span class="school_title <?php if(empty($_GET['DeptName'])){ echo 'b-fff';}?>"><a href="<?php echo U('Vip/GsWeixin/smallClass',array('FitClass'=>$_GET['FitClass'],'SubjectName'=>$_GET['SubjectName']));?>">全部</a></span>
                 	<span class="school_title">西城区</span>
                    <span class="school_title">海淀区</span>
                    <span class="school_title">朝阳区</span>
                    <span class="school_title">东城区</span>
                    <span class="school_title">房山区</span>
                 </div>
                 <div class="school_con" id="school_con">
                 	<div class="schooldown">
                      
                    </div>
                 	<div class="schooldown hide">
                        <span class="schoolname"><a <?php if($_GET['DeptName'] =='阜成门'){ echo 'class="c-green"';}?> href="<?php echo U('Vip/GsWeixin/smallClass',array('DeptName'=>'阜成门','FitClass'=>$_GET['FitClass'],'SubjectName'=>$_GET['SubjectName']));?>">阜成门校区</a></span>
                    </div>
                    <div class="schooldown hide">
                        <span class="schoolname"><a <?php if($_GET['DeptName'] =='理想'){ echo 'class="c-green"';}?> href="<?php echo U('Vip/GsWeixin/smallClass',array('DeptName'=>'理想','FitClass'=>$_GET['FitClass'],'SubjectName'=>$_GET['SubjectName']));?>">理想校区</a></span>
                        <span class="schoolname"><a <?php if($_GET['DeptName'] =='金源'){ echo 'class="c-green"';}?> href="<?php echo U('Vip/GsWeixin/smallClass',array('DeptName'=>'金源','FitClass'=>$_GET['FitClass'],'SubjectName'=>$_GET['SubjectName']));?>">金源校区</a></span>
                        <span class="schoolname"><a <?php if($_GET['DeptName'] =='公主坟'){ echo 'class="c-green"';}?> href="<?php echo U('Vip/GsWeixin/smallClass',array('DeptName'=>'公主坟','FitClass'=>$_GET['FitClass'],'SubjectName'=>$_GET['SubjectName']));?>">公主坟校区</a></span>
                        <span class="schoolname"><a <?php if($_GET['DeptName'] =='高思大厦'){ echo 'class="c-green"';}?> href="<?php echo U('Vip/GsWeixin/smallClass',array('DeptName'=>'高思大厦','FitClass'=>$_GET['FitClass'],'SubjectName'=>$_GET['SubjectName']));?>">高思大厦校区</a></span>
                        <span class="schoolname"><a <?php if($_GET['DeptName'] =='玉泉路'){ echo 'class="c-green"';}?> href="<?php echo U('Vip/GsWeixin/smallClass',array('DeptName'=>'玉泉路','FitClass'=>$_GET['FitClass'],'SubjectName'=>$_GET['SubjectName']));?>">玉泉路校区</a></span>
                        <span class="schoolname"><a <?php if($_GET['DeptName'] =='上地'){ echo 'class="c-green"';}?> href="<?php echo U('Vip/GsWeixin/smallClass',array('DeptName'=>'上地','FitClass'=>$_GET['FitClass'],'SubjectName'=>$_GET['SubjectName']));?>">上地校区</a></span>
                        <span class="schoolname"><a <?php if($_GET['DeptName'] =='中鼎'){ echo 'class="c-green"';}?> href="<?php echo U('Vip/GsWeixin/smallClass',array('DeptName'=>'中鼎','FitClass'=>$_GET['FitClass'],'SubjectName'=>$_GET['SubjectName']));?>">中鼎校区</a></span>
                        <span class="schoolname"><a <?php if($_GET['DeptName'] =='五道口'){ echo 'class="c-green"';}?> href="<?php echo U('Vip/GsWeixin/smallClass',array('DeptName'=>'五道口','FitClass'=>$_GET['FitClass'],'SubjectName'=>$_GET['SubjectName']));?>">五道口校区</a></span>
                    </div>
                    <div class="schooldown  hide">
                        <span class="schoolname"><a <?php if($_GET['DeptName'] =='朝阳'){ echo 'class="c-green"';}?> href="<?php echo U('Vip/GsWeixin/smallClass',array('DeptName'=>'朝阳','FitClass'=>$_GET['FitClass'],'SubjectName'=>$_GET['SubjectName']));?>">朝阳校区</a></span>
                    </div>
                    <div class="schooldown  hide">
                        <span class="schoolname"><a <?php if($_GET['DeptName'] =='广渠门'){ echo 'class="c-green"';}?> href="<?php echo U('Vip/GsWeixin/smallClass',array('DeptName'=>'广渠门','FitClass'=>$_GET['FitClass'],'SubjectName'=>$_GET['SubjectName']));?>">广渠门校区</a></span>
                    </div>
                    <div class="schooldown  hide">
                        <span class="schoolname"><a <?php if($_GET['DeptName'] =='良乡'){ echo 'class="c-green"';}?> href="<?php echo U('Vip/GsWeixin/smallClass',array('DeptName'=>'良乡','FitClass'=>$_GET['FitClass'],'SubjectName'=>$_GET['SubjectName']));?>">良乡校区</a></span>
                    </div>
                 </div>
                 
              </div>
              <!--校区结束-->
              
         </div>
</div>
<script>
    $(function(){
        //通用头部搜索切换
        $('#search-bd li').click(function(){
            var index = $(this).index();
            $('#search-hd .pholder').eq(index).slideToggle("slow").siblings('.pholder').hide(0);
            $(this).toggleClass('selected').siblings().removeClass('selected');	
        });
		
		$('#school_nav span').click(function(){
            var index = $(this).index();
            $('#school_con .schooldown').eq(index).slideToggle("slow").siblings('.schooldown').hide(0);
            $(this).toggleClass('b-fff').siblings().removeClass('b-fff');	
        });
		
    })
</script>
<div class="classList" style="margin-top:104px;" >
	<?php foreach($latelyClassList->AppendData as $class){?>
		<div class="course auto">
			<a href="<?php echo U('Vip/GsWeixin/classDetails',array('id'=>$class->Id))?>">
				<div class="shctitle f14 c-red"><?php echo $class->ClassName;?><span><i class="c-fff b-red">热</i></span></div>
			</a>
			<figure class="tour">
				<a href="<?php echo U('Vip/GsWeixin/classDetails',array('id'=>$class->Id))?>">
					<img src="/static/images/class.jpg" class="w43">
				</a>
				<figcaption class="w57">
					<a href="<?php echo U('Vip/GsWeixin/classDetails',array('id'=>$class->Id))?>">
						<p><i class="c-green w25">老师姓名</i><span class="w73"><?php echo $class->TeacherName;?></span></p>
						<p><i class="c-green w25">开课日期</i><span class="w73"><?php echo substr($class->BeginOn,0,10);?>(<?php echo $class->week;?>)</span></p>
						<p><i class="c-green w25">课次节数</i><span class="w73"><?php echo $class->LessonNum;?>节</span></p>
						<p><i class="c-green w25">上课校区</i><span class="w73"><?php echo $class->DeptName;?></span></p>
						<?php if($class->is_sign != 1){?>
							<p><i class="c-green w25">剩余人数</i><span class="w73"><?php $num = $class->NowNum+$class->orderNum; echo ($class->LimitNum)-$num;?></span></p>
						<?php }?>
					</a>
					<?php if($class->is_sign == 1){?>
						<a href="javascript:;" class="btn_gray btn_cou">班级已满</a>
					<?php 
						}else{
							if($class->is_order == 1){
								echo '<a href="javascript:;" class="btn_red btn_cou">已报名</a>';
							}else{
					?>
						<a href="<?php echo U('Vip/GsWeixin/classEnroll',array('id'=>$class->Id))?>" class="btn_red btn_cou">立即报名</a>
					<?php }}?>
				</figcaption>
				<div class="clear"></div>
			</figure>
		</div>
	<?php }?>
	<?php
		if(!empty($classList->PagedList)){ 
			foreach($classList->PagedList as $class){
	?>
	<div class="course auto">
		<a href="<?php echo U('Vip/GsWeixin/classDetails',array('id'=>$class->Id))?>">
			<div class="shctitle f14 c-red"><?php echo $class->ClassName;?><span><i class="c-fff b-red">热</i></span></div>
		</a>
		<figure class="tour">
			<a href="<?php echo U('Vip/GsWeixin/classDetails',array('id'=>$class->Id))?>">
				<img src="/static/images/class.jpg" class="w43">
			</a>
			<figcaption class="w57">
				<a href="<?php echo U('Vip/GsWeixin/classDetails',array('id'=>$class->Id))?>">
					<p><i class="c-green w25">老师姓名</i><span class="w73"><?php echo $class->TeacherName;?></span></p>
					<p><i class="c-green w25">开课日期</i><span class="w73"><?php echo substr($class->BeginOn,0,10);?>(<?php echo $class->week;?>)</span></p>
					<p><i class="c-green w25">课次节数</i><span class="w73"><?php echo $class->LessonNum;?>节</span></p>
					<p><i class="c-green w25">上课校区</i><span class="w73"><?php echo $class->DeptName;?></span></p>
					<?php if($class->is_sign != 1){?>
						<p><i class="c-green w25">剩余人数</i><span class="w73"><?php $num = $class->NowNum+$class->orderNum; echo ($class->LimitNum)-$num;?></span></p>
					<?php }?>
				</a>
				<?php if($class->is_sign == 1){?>
					<a href="javascript:;" class="btn_gray btn_cou">班级已满</a>
				<?php 
					}else{
						if($class->is_order == 1){
							echo '<a href="javascript:;" class="btn_red btn_cou">已报名</a>';
						}else{
				?>
					<a href="<?php echo U('Vip/GsWeixin/classEnroll',array('id'=>$class->Id))?>" class="btn_red btn_cou">立即报名</a>
				<?php }}?>
			</figcaption>
			<div class="clear"></div>
		</figure>
	</div>
	<?php }
		}else{
			echo '<div class="course auto"><div class="shctitle f14 c-red">暂无任何课程信息...</div></div>';
		}?>
</div>
<div class="dropload-load"></div>
<!--nav-->
<div class="home"><a href="<?php echo U('Vip/GsWeixin/index');?>"></a></div>
<div class="nav">
	<ul>
		<li><a href="<?php echo U('Vip/GsWeixin/smallClass')?>" class="xb_green"><span class="c-green">小班课</span></a></li>
		<li><a href="<?php echo U('Vip/GsWeixin/oneToOne')?>" class="vip">1对1</a></li>
		<li><a href="<?php echo U('Vip/GsWeixin/doActivity');?>" class="hd">活动</a></li>
		<li><a href="<?php echo U('Vip/GsWeixin/makeDiagnosis');?>" class="zhd">预约诊断</a></li>
	</ul>
</div>
<script type="text/javascript">
	var high = 0;
	var isrun = true;
	var tops = 0;
	function page_init(){
		high = $('.classList').height();
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
						U(
							"Vip/GsWeixin/ajaxClass",
							array(
								"latitude"=>$latitude,
								"longitude"=>$longitude,
								"school"=>$latelyClassList->school,
								"ClassName"=>$_POST["ClassName"],
								"DeptName"=>$_GET["DeptName"],
								"FitClass"=>$_GET["FitClass"],
								"SubjectName"=>$_GET["SubjectName"],
								"TotalPageCount"=>$classList->TotalPageCount
							)
						);
					?>',
					{tops:tops,high:high},function(data){ 
	
				if(data !=""){
					$('.dropload-load').html("");
					tops += high;
					$('.classList').append(data);
					isrun = true;
				}else{
					isrun = true;
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

		$('.btn_seach').click(function(){
			// var ClassName = $("input[name='ClassName']").val();
			// if(ClassName !== ''){
				$('#search').submit();
			// }
		})
	});
</script>
</body>
</html>