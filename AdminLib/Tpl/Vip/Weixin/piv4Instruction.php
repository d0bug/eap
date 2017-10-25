<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0" />
<meta name="format-detection" content="telephone=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<title>PIV4.0流程使用说明</title>
<script src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/static/js/jweixin.js"></script>
<script src='/static/js/hhSwipe.js'></script>
<style type="text/css">
body,p,ul,li,h1,h2,h3,div,img{ margin:0; padding:0; border:0;}
body{ font-family:"微软雅黑"; background:url(/static/images/intro/wx/bg2.jpg) repeat;}
ul,li{ list-style:none;}
img{ width:100%; }
.wrap{padding:30px 0;}
.clear{ clear:both;}
/***************/
.title{ width:90%; margin:0 auto; margin-bottom:10px;}
/********/
.banner{width:100%; border-bottom:2px solid #f1e0cc;border-top:2px solid #f1e0cc; background:rgba(153, 102, 51, 0.2) none repeat scroll 0 0 !important;filter:Alpha(opacity=20); background:#996633; position:relative;}
.scroll{margin:0 auto; position:relative;padding:20px 10px 10px;}
.scroll_box{overflow:hidden;visibility:hidden;position:relative; }

.scroll_wrap{overflow-x:hidden; position:relative;}
.scroll_wrap li{position:relative;width:100%;float:left;}
.scroll_wrap li a{display:block;margin:0 auto;position:relative;}
.scroll_wrap li p{display:block; position:relative; height:30px; margin-bottom:10px; color:#307060;}
.scroll_position{ width:100%; margin:0 auto; text-align:center; float:left;}
.scroll_position li{display:inline-block;width:6px;height:6px;border-radius:6px;background:#a05104;}
.scroll_position li a{font-size:0;}
.scroll_position li.on{background:#a05104;}
.scroll_position_bg{background:#000;position:absolute;bottom:12px;left:42%;padding:0 15px;z-index:380px;height:26px;border-radius:26px;}

/******icon******/
.icon1{ width:100%; margin-top:15px;}
.icon1 a{ width:50%; display:block; float:left;}

.icon2{ width:100%;}
.icon2 a{display:block; float:left; width:33.3%}

@media(min-device-width:721px){
.scroll_wrap li p{ font-size:1.125rem}
} 
@media(max-device-width:720px) {
.scroll_wrap li p{ font-size:0.875rem}
}
</style>
</head>
<body jsInfoUrl="<?php echo $jsInfoUrl?>" pageUrl="<?php echo $currentUrl?>">
<div class="wrap">
	<div class="title"><img src="/static/images/intro/wx/liu.png"></div>
    <div class="banner">
        <div class="scroll">
            <div class="scroll_box" id="scroll_img">
                <ul class="scroll_wrap upimgbox">
                    <li><p>0.登录vip.gaosiedu.com，进入PIV4.0备课并选取学科。</p><img src="<?php echo APP_URL?>/static/images/intro/wx/tu_05.jpg"></li>
                    <li><p>1.1筛选标准化讲义。</p><img src="<?php echo APP_URL?>/static/images/intro/wx/1-1.jpg"></li>
                    <li><p>1.2预览讲义。</p><img src="<?php echo APP_URL?>/static/images/intro/wx/2-1.jpg"></li>
                    <li><p>1.3点击修改讲义。</p><img src="<?php echo APP_URL?>/static/images/intro/wx/3-1.jpg"></li>
                    <li><p>1.3点击修改讲义。</p><img src="<?php echo APP_URL?>/static/images/intro/wx/4.jpg"></li>
                    <li><p>1.4存档讲义。</p><img src="<?php echo APP_URL?>/static/images/intro/wx/5.jpg"></li>
                    <li><p>1.5匹配课程。</p><img src="<?php echo APP_URL?>/static/images/intro/wx/6.jpg"></li>
                    <li><p>1.6下载讲义。</p><img src="<?php echo APP_URL?>/static/images/intro/wx/7.jpg"></li>
                    <li><p>1.6下载讲义。</p><img src="<?php echo APP_URL?>/static/images/intro/wx/8.jpg"></li>
                    <li><p>2.1核录课时。</p><img src="<?php echo APP_URL?>/static/images/intro/wx/2-1-1.png"></li>
                    <li><p>2.2上传轨照。</p><img src="<?php echo APP_URL?>/static/images/intro/wx/2-1-2.png"> </li>
                    <li><p>2.3记录轨迹。</p><img src="<?php echo APP_URL?>/static/images/intro/wx/2-1-3.png"></li>
                    <li><p>2.4写课评。</p><img src="<?php echo APP_URL?>/static/images/intro/wx/2-1-4.png"></li>
                    <li><p>2.5生成报告。</p><img src="<?php echo APP_URL?>/static/images/intro/wx/2-1-5.png"></li>
                </ul>
               	<ul class="scroll_position" id='scroll_position'>
                        <li ><a href="javascript:void(0);">6</a></li>
                        <li ><a href="javascript:void(0);">1</a></li>
                        <li ><a href="javascript:void(0);">1.2预览讲义。</a></li>
                        <li ><a href="javascript:void(0);">1.3点击修改讲义。</a></li>
                        <li ><a href="javascript:void(0);">1.3点击修改讲义。</a></li>
                        <li ><a href="javascript:void(0);">1.4存档讲义。</a></li>
                        <li ><a href="javascript:void(0);">1.5匹配课程。</a></li>
                        <li ><a href="javascript:void(0);">1.6下载讲义。</a></li>
                        <li ><a href="javascript:void(0);">1.6下载讲义。</a></li>
                        <li ><a href="javascript:void(0);">2.1核录课时。</a></li>
                        <li ><a href="javascript:void(0);">2.2上传轨照。</a></li>
                        <li ><a href="javascript:void(0);">4</a></li>
                        <li ><a href="javascript:void(0);">3</a></li>
                        <li ><a href="javascript:void(0);">2</a></li>
                </ul> 
            </div>
        </div>
    </div>
    <!--轮播图-->
    <div class="icon1">
    	<a href="<?php echo U('Vip/Weixin/piv4Present')?>"><img src="/static/images/intro/wx/icon_04.jpg"></a>
        <a href="<?php echo U('Vip/Weixin/piv4Present')?>"><img src="/static/images/intro/wx/icon_05.jpg"></a>
    </div><!--按钮1完成-->
    <div class="icon2">
    	<a href="<?php echo U('Vip/Weixin/piv4Ans')?>"><img src="/static/images/intro/wx/icon_06.jpg"></a>
        <a href="<?php echo U('Vip/Weixin/piv4Class')?>"><img src="/static/images/intro/wx/icon_07.jpg"></a>
        <a href="<?php echo U('Vip/Weixin/piv4Tral')?>"><img src="/static/images/intro/wx/icon_08.jpg"></a>
    </div><!--按钮2完成--->
</div>
</body>
<script>
	var slider = Swipe(document.getElementById('scroll_img'), {
		auto: 5000,
		continuous: true,
		callback: function(pos) {
			var i = bullets.length;
			while (i--) {
				bullets[i].className = ' ';
			}
			bullets[pos].className = 'on';
		}
	});
	var bullets = document.getElementById('scroll_position').getElementsByTagName('li');
	$(function(){
		$('.scroll_position_bg').css({
			width:$('#scroll_position').width()
		});
		bullets[0].className = 'on';
		
		//jssdk验证
		var jsInfoUrl = $('body').attr('jsInfoUrl');
		var pageUrl = $('body').attr('pageUrl');
		$.post(jsInfoUrl,{currentUrl:pageUrl}, function(data){
			if(data.errorMsg) {
				//alert(data.errorMsg);
				alert('微信接口验证失败，请稍后再试');
			} else {
				wx.config({
					debug:false,
					appId:data.appId,
					timestamp:data.timestamp,
					nonceStr:data.nonceStr,
					signature:data.signature,
					jsApiList:[
					 'previewImage'
					]
				})
			}
		}, 'json');
		
		//图片预览
		$('.upimgbox').find('img').unbind('click').click(function(){
			var $curImg = $(this);
			wx.checkJsApi({
				jsApiList:['previewImage'],
				success:function(){
					var curImg = $curImg.attr('src');
					var imgList = [];
					var alerted = false;
					$('.upimgbox').find('img').each(function(){
						var imgSrc = $(this).attr('src');
						imgList.push(imgSrc);
					});
					if(imgList.length > 0) {
						wx.previewImage({
							current: curImg,
							urls: imgList
						})
					}
				}
			})
		});
		
	});
	
</script>
</html>
