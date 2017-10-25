<?php foreach($orderList as $order){?>
	<div class="course auto" id="order<?php echo $order['id']?>">
        <div class="ordertitle f14 <?php if($order['order_status'] != '2'){ echo 'c-red';}?>">
            <?php if($order['order_status'] == 1){ 
                echo '报名成功';
            }else if($order['order_status'] == 2){
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
<?php }?>