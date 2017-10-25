<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="format-detection" content="telephone=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<title>订单中心</title>
	<link rel="stylesheet" type="text/css" href="/static/css/weixinBase.css">
	<script src="/static/js/jquery-2.1.1.min.js"></script>
</head>
<body>
<div class="seach auto fixed">
    <form id="search" action="" method="get">
        <input type="text" value="<?php echo $_GET['class_name'];?>" name="class_name" placeholder="请输入班课名称" class="seacher">
        <input type="button" value="搜索" class="btn_yell btn_seach">
    </form>
</div>

<div class="auto tab_nav mt8 mt58" style="height:100%;" >
	<ul id="pagenavi" class="page">
		<li class="w33 <?php if(empty($_GET['status'])){ echo 'active';}?>">
            <a href="<?php echo U('Vip/GsWeixin/classOrder');?>">待付款</a>
        </li>
      	<li class="w33 <?php if($_GET['status'] == 1){ echo 'active';}?>">
            <a href="<?php echo U('Vip/GsWeixin/classOrder',array('status'=>1));?>">已报名</a>
        </li>
        <li class="w33 <?php if($_GET['status'] == 2){ echo 'active';}?>">
            <a href="<?php echo U('Vip/GsWeixin/classOrder',array('status'=>2));?>">已退款</a>
        </li>
	</ul>
	<div id="slider" class="swipe">
		<ul class="auto tab_list">
			<li class="li_list">
                <?php if(!empty($orderList)){foreach($orderList as $order){?>
    				<div class="course auto" id="order<?php echo $order['id']?>">
                        <div class="ordertitle f14 <?php if($order['order_status'] != '2'){ echo 'c-red';}?>">
                            <?php if($order['order_status'] == 1){ 
                                echo '报名成功';
                            }else if($order['order_status'] == 2) {
                                echo '已退款';     
                            }else{
                                echo '待付款';
                            }?>
                            <span class="c-gray"><?php echo date('Y-m-d H:i:s',$order['order_time'])?></span>
                            <?php if($order['order_status'] != '1'){?>
                            <a orderId="<?php echo $order['id'];?>" class="J_homeworkcon-queqin del"></a>
                            <?php }?>
                        </div>
                        <figure class="tour">
                            <a href="<?php echo U('Vip/GsWeixin/classDetails',array('id'=>$order['class_id']));?>"><img src="/static/images/class.jpg" class="w43"></a>
                            <figcaption class="w57">
                                <a href="<?php echo U('Vip/GsWeixin/classDetails',array('id'=>$order['class_id']));?>">
                                    <p><i class="c-green w25">班课名称</i><span class="w73"><?php echo $order['class_name'];?></span></p>
                                    <p>&nbsp;</p>
                                    <p><i class="c-green w25">老师姓名</i><span class="w73"><?php echo $order['teacher_name'];?></span></p>
                                    <p class="money"><i class="c-red w25">¥<?php echo $order['order_price'];?></i></p>
                                </a>
                                <?php if(empty($order['order_status'])){?>
                                <a href="<?php echo U('Vip/GsWeixin/classEnroll',array('id'=>$order['class_id']));?>" class="btn_red btn_cou">去付款</a>
                                <?php }?>
                            </figcaption>
                            <div class="clear"></div>
                        </figure>
                    </div>
                <?php }}else{?>
                    <div class="course auto">
                        您还没有订单~
                    </div>  
                <?php }?>
			</li>
		</ul>
        <div class="dropload-load"></div>
	</div>
</div>

<!--退出登录弹框-->
<div class="queqinbox hide" id="J_g_queqinbox">
	<div class="g-windowcon">
		<div class="b_wenzi f16 c-333">确认删除吗？</div>
		<div class="b_btn">
			<span class="btn_red f16 confirm">确定</span>
			<span class="btn_grayxian f16 win-closebtn">取消</span>
            <input type="hidden" value="" id="orderId" />
		</div>
	</div>
</div>

<script type="text/javascript"> 

$('.btn_seach').click(function(){
    var ClassName = $("input[name='class_name']").val();
    if(ClassName !== ''){
        $('#search').submit();
    }
})

/*****缺勤弹出框***/
$(".J_homeworkcon-queqin").click(function(){
    var id = $(this).attr('orderId');
    if(id > 0){
        $("#J_g_queqinbox").show();
        $('#orderId').val(id); 
    }else{
        alert('订单获取失败');
        return false;
    }	   
});

$('.confirm').click(function(){
    id = $('#orderId').val();
    if(id > 0){
        $.post('<?php echo U("Vip/GsWeixin/delOrder")?>',{order_id:id},function(data){            
            if(data == '1'){
                $("#J_g_queqinbox").hide();
                $('#order'+id).fadeOut();
            }else{
                alert('删除失败');
            }
        })
    }else{
        alert('订单获取失败');
        return false;
    }    
})

/*弹窗关闭事件*/
$(".win-closebtn").click(function(){
	$("#J_g_queqinbox").hide();
});

</script>

<script type="text/javascript">
    var high = 0;
    var isrun = true;
    var tops = 0;
    function page_init(){
        high = $('.tab_list').height();
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
                        U("Vip/GsWeixin/ajaxOrder",
                            array(
                                "user_id"=>$user["user_id"],
                                "status"=>$_GET["status"]
                                )
                        );
                    ?>',
                    {tops:tops,high:high},function(data){    
                if(data !=""){
                    $('.dropload-load').html("");
                    tops += high;
                    $('.tab_list').append(data);
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


<!--nav-->
<div class="home" style="bottom:8px;"><a href="<?php echo U('Vip/GsWeixin/index');?>"></a></div>


</body>
</html>