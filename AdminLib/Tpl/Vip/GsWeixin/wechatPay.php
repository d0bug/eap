<html>
    <head>
        <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <title>微信支付</title>
        <script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
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
        <br/>
        <font color="#9ACD32"><b>该笔订单支付金额为<span ><?php echo $orderInfo['order_price']?></font><br/><br/>
        <div align="center">
 
            <button class="weixinPayBtn" data-order_id="<?php echo $orderInfo['id']?>" style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" >立即支付</button>
        </div>
    </body>
</html>