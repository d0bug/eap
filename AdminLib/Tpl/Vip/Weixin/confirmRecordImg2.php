<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0" />
<meta name="format-detection" content="telephone=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<title>高思教师系统</title>
<link href="/static/css/vip-weixin.css" rel="stylesheet" />
<script src="/static/js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.blockUI.js"></script>
<script src="/static/js/lightbox.js"></script>
<link href="/static/css/lightbox.css" rel="stylesheet" />
<script src="/static/js/iscroll.js"></script>
<script src="/static/js/vip_wx.js"></script>
<link type="text/css" rel="stylesheet" href="/static/css/dialog.css">
<script type="text/javascript" charset="utf-8" src="/static/js/dialog-min.js"></script>
<script type="text/javascript" charset="utf-8" src="/static/js/jweixin.js"></script>
</head>
<body jsInfoUrl="<?php echo $jsInfoUrl?>" saveImgUrl="<?php echo $saveImageUrl?>" pageUrl="<?php echo $currentUrl?>">
<header class="header">
	<h1>上传讲义</h1>
	<div class="arr"></div>
</header>
<article class="wrap">
	<form id="confirm" name="confirm" method="POST" action="<?php echo U('Vip/Weixin/doConfirmRecordImg',array('return'=>'confirmRecordImg2'))?>">
		<div class="upPhoto">
			<h3 class="modTit">请选择本次上传图片所对应的课次：</h3>
			<div class="modCon">
					<dl>
						<dt>请选择学员姓名：</dt>
						<dd>
							<span class="selbar">
								<select id="student_code" name="student_code" onchange="get_kechengList(this.value,'<?php echo $userInfo['sCode']?>','<?php echo U('Vip/Weixin/getKechengList')?>');">
									<option value="">请选择学员</option>
								</select>
							</span>
						</dd>
						<dt>请选择课程：</dt>
						<dd>
							<span class="selbar">
								<select id="kecheng_code" name="kecheng_code" onchange="get_lessonList(this.value,'<?php echo $userInfo['sCode']?>','<?php echo U('Vip/Weixin/get_lessonList',array('selectAll'=>3))?>')">
								<?php if(!empty($arr['kecheng_code'])):?>
									<option value="<?php echo $arr['kecheng_code']?>"><?php echo $arr['kecheng_name']?></option>
								<?php else:?>
									<option value="">请选择课程</option>
								<?php endif;?>
								</select>
							</span>
						</dd>
						<dt>请选择上课时间：</dt>
						<dd>
							<span class="selbar">
								<select id="helu_id" name="helu_id">
									<?php if(!empty($arr['helu_id'])):?>
										<option value="<?php echo $arr['helu_id']?>"><?php echo $arr['timeStr']?></option>
									<?php else:?>
										<option value="">请选择上课时间</option>
									<?php endif;?>
								</select>
							</span>
						</dd>
					</dl>
					
				
			</div>
			<h3 class="modTit">您上传的图片如下：</h3>
			<div class="upimgbox" id="J_upimgbox">
				<div id="img_msg"><font color=red>提醒：如果图片数量不符，请稍后再确认哦~</font></div>
				<ul class="clearfix">
					<li><div class="upimgbox-btn addImageBtn">+</div></li>
				<?php if($wxImgList):?>
					<?php foreach($wxImgList as $key=>$wxImg):?>
						<li>
							<a href="<?php echo $wxImg['serviceurl_show']?>" rel="lightbox" id="ShowLightBox"><img src="<?php echo $wxImg['serviceurl_show']?>" /></a>
							<!--<a href="javascript:void(0)" onclick="del_wxImg('<?php echo U('Vip/Weixin/delWxImg',array('id'=>$wxImg['id']))?>')">删除照片</a>-->
							<span onclick="del_wxImg('<?php echo U('Vip/Weixin/delWxImg',array('id'=>$wxImg['id']))?>')">×</span>
							<input type="hidden" name="upImages[]" value="<?php echo $wxImg['serviceurl']?>" />
						</li>
					<?php endforeach?>
				<?php endif;?>
				</ul>
			</div>
			<div class="modCon">
				<div class="button">
					<input type="button" class="btn" value="确认上传" onclick="return confirmSubmit()">
					<span id="confirm_msg" class="error"></span>
				</div>
			</div>
		</div>
	</form>
</article>
</body>
</html>
<script type="text/javascript">
var images = {
	localId: [],
	serverId: []
};
$(function () {
	$.get('<?php echo U('Vip/Weixin/getAllStudents',array('jieke'=>0,'type'=>3))?>',
	function(data){
		$('#student_code').html(data);
		$('#student_code').val('<?php echo $arr['student_code']?>');
	});

	setTimeout(function() {
		window.scrollTo(0, 1)
	}, 0);
	var jsInfoUrl = $('body').attr('jsInfoUrl');
	var pageUrl = $('body').attr('pageUrl');
	$.post(jsInfoUrl,{currentUrl:pageUrl}, function(data){
		if(data.errorMsg) {
			//alert(data.errorMsg);
			alert('微信上传图片接口验证失败，请稍后再试');
		} else {
			wx.config({
				debug:false,
				appId:data.appId,
				timestamp:data.timestamp,
				nonceStr:data.nonceStr,
				signature:data.signature,
				jsApiList:[
				'chooseImage', 'previewImage', 'uploadImage'
				]
			})
		}
	}, 'json');


	//选取图片
	$('.addImageBtn').click(function(){
		wx.checkJsApi({
			jsApiList:['chooseImage', 'uploadImage'],
			success:function(res2){
				wx.chooseImage({
					sourceType: ['album'],
					success:function(res){
						var localIds = res.localIds;
						images.localId = res.localIds;
						$.each(localIds, function(idx, img){
							//var html = '<li><input type="hidden" class="wxImage" name="upImages[]" value="" /><input type="hidden" class="imgLocalId" value="'+img+'" /><img src="'+img+'" /><span>X</span></li>';
							var html = '<li><input type="hidden" class="imgLocalId" value="'+img+'" /><img src="'+img+'" /><span>X</span></li>';
							$('.upimgbox').find('ul').append(html);
						})
						bindImgEvent();
					}
				})
			}
		})
	})

	bindImgEvent();
});

function bindImgEvent() {
	$('.upimgbox').find('span').unbind('click').click(function(){
		$(this).parent().remove();
	})
	$('.upimgbox').find('img').unbind('click').click(function(){
		var $curImg = $(this);
		wx.checkJsApi({
			jsApiList:['previewImage'],
			success:function(){
				var curImg = $curImg.attr('src');
				if(curImg.substring(0,7) == 'wxLocal') {
					var d = dialog({
						title:'请先执行保存操作',
						content:'<div style="height:50px;line-height:50px;width:300px;text-align:center">没有上传的本地图片不支持预览!</div>',
						cancel:false,
						ok:function(){},
						onclose:function(){}
					})
					d.showModal();
					return;
				}
				var imgList = [];
				var alerted = false;
				$('.upimgbox').find('img').each(function(){
					var imgSrc = $(this).attr('src');
					if(imgSrc.substring(0, 7) != 'wxLocal') {
						imgList.push(imgSrc);
					} else {
						if(false == alerted) {
							alert('没有上传的本地图片不支持预览功能!');
						}
						alerted = true;
					}
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
}


function uploadImg() {
	var saveImgUrl = $('body').attr('saveImgUrl');
	var i = 0, length = images.localId.length;
	if( length > 0){
		images.serverId = [];
		var d = dialog({
			content: '<div id="dialogNotice" style="height:50px;line-height:50px;width:300px;text-align:center">正在上传微信图片(' + i + '/' + length + '),请稍候!</div>',
			onshow:function(){
				function upload() {
					wx.uploadImage({
						localId: images.localId[i],
						success: function (res) {
							var serverId = res.serverId;
							$.post(saveImgUrl, {imgServerId:serverId}, function(data){
								if(data.errorMsg) {
									//alert(data.errorMsg);
									alert('图片上传失败，请重新上传');
								} else {
									var html = '<input type="hidden" class="wxImage" name="upImages[]" value="'+data.imgLocalUrl+'" />';
									$('.upimgbox').find('ul').append(html);
									i++;
									$('#dialogNotice').html('正在上传微信图片(' + i + '/' + length + '),请稍候!');
									if(i == length) {
										$('#dialogNotice').html('图片上传完毕,正在提交核录数据');
										d.remove();
										$('#confirm').submit();
									}
									if (i < length) {
										upload();
									}
								}
							}, 'json');

						},
						fail: function (res) {
							alert(JSON.stringify(res));
						}
					});
				}
				upload();
			}
		});
		d.showModal();
	}else{
		$('#img_msg').html('<font color=red>请先上传图片</font>');
		return false;
	}

}


function confirmSubmit(){
	var status = 1;
	if($('#student_code').val() == ''){
		status = 0;
		$('#confirm_msg').html('<font color=red>请选择学员</font>');
		return false;
	}
	if($('#kecheng_code').val() == ''){
		status = 0;
		$('#confirm_msg').html('<font color=red>请选择课程</font>');
		return false;
	}
	if($('#helu_id').val() == ''){
		status = 0;
		$('#confirm_msg').html('<font color=red>请选择上课时间</font>');
		return false;
	}
	if(status == 1){
		uploadImg();
	}
}
</script>