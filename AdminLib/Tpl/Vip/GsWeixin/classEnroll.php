<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="format-detection" content="telephone=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<title>小班报名—支付</title>
	<link rel="stylesheet" type="text/css" href="/static/css/weixinBase.css">
	<script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
	<style>
		body{ margin-bottom: 60px;}
	</style>
</head>
<body>
	<script type="text/javascript">
		(function(g){
		    var siteUrl = g.siteUrl || location.hostname;
		    
		    // 初始化
		    init();
		    
		    function init(){
		        // 显示微信安全支付的字样
		        if(location.href.indexOf('showwxpaytitle=1') == -1) {
		          location.href = location.href.indexOf('?') == -1 ? location.href + '?showwxpaytitle=1' : location.href + '&showwxpaytitle=1';
		          return;
		        }
		        
		        $(function(){
		          confirmPayBtn();
		        });
		    }
		    
		    // 支付方式
		    var playMethod = 'weixin';
		     
		    // 发起支付
		    var confirmPayBtn = function(){
		        $('.weixinPayBtn').click(function(){
		            if (playMethod == 'weixin') {
		            	if(!isWeixin()) {
		                    alert("请在微信端进行支付");
		                    return false;
		                }
		                              
		                var thisBtn = $(this);
		                //获取订单id
		                var orderId = thisBtn.data('order_id');
		                //设置微信安全支付目录
		                var safe_pay_dir = '<?php echo U("Vip/GsWeixin/pay")?>';
		                //拼接请求地址
		                var pay_url = safe_pay_dir+'/order_id/'+orderId;
		                
		                if(!orderId) {
		                    alert("没有订单信息");
		                   return;
		                }
		                
		                if(thisBtn.data('disabled') == 'yes') {
		                    return;
		                }
		                thisBtn.data('disabled', 'yes');
		                
		               // 获取订单json数据
		                $.getJSON(pay_url, function(json){

		                	//return;
		                    if(json.status != 'ok') {
		                        thisBtn.data('disabled', 'no');
		                        alert(json.error_msg);
								return;
		                    } else {
		                    //alert(JSON.stringify(json.wxconf));//json弹出
		                    WeixinJSBridge.invoke('getBrandWCPayRequest', json.wxconf, function(res){
		                        thisBtn.data('disabled', 'no');
		                        if(res.err_msg == 'get_brand_wcpay_request:cancel') {
		                          	alert("您已取消了此次支付");
		                          	return;
		                        } else if(res.err_msg == 'get_brand_wcpay_request:fail') {
		                          	alert("支付失败，请重新尝试");
		                          	return;
		                        } else if(res.err_msg == 'get_brand_wcpay_request:ok') {
		                          	location.href = "/vip/gs_weixin/classOrder/status/1";
		                        } else {
		                          	alert("未知错误"+res.error_msg);
								  	return;
		                        }
		                      });
		                  	}
			            }).error(function(){
		                    alert(JSON.stringify(arguments));
		                    //alert("网络错误，请刷新页面重试11");
		                    thisBtn.data('disabled', 'no');
			            });

		            } 
		        })
		    }
		    function isWeixin(){
		       return /MicroMessenger/.test(navigator.userAgent);
		    }    

	})(window);
	</script>
<div class="auto pay1">
	<img src="/static/images/payClass.png">
	<div class="auto paycon">
		<h2>¥<?php echo $class->RealPrice;?><span>价格<!-- <del>¥<?php echo $class->OriginalPrice;?></del> --></span></h2>
		<ul class="classinfo">
			<li><i class="c-green">班级名称</i><?php echo $class->ClassName;?></li>
			<li><i class="c-green">老师姓名</i><?php echo $class->TeacherName;?></li>
			<li><i class="c-green">开课日期</i><?php echo substr($class->BeginOn,0,10);?></li>
			<li><i class="c-green">课次/节数</i><?php echo $class->LessonNum;?>节</li>
			<li><i class="c-green">上课校区</i><?php echo $class->DeptName;?></li>
		</ul>
	</div>
	<a href="javascript:;" data-order_id="<?php echo $orderFind['id']?>" class="btn_red btn_zc weixinPayBtn">立即支付</a>
</div>

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