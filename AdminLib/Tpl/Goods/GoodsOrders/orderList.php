<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/goods.js"></script>
<script type="text/javascript" src="/static/js/goods_rili.js"></script>
<link href="/static/css/mgs.css" type="text/css" rel="stylesheet" />

</head>
<body>
<div region="center">
<div id="main">
	<div id="search">
    <form id="search_form" name="search_form" method="GET" action="<?php echo U('Goods/GoodsOrders/orderList');?>">
	<table width="100%">
    	<tr>
        	<td width="10%"><strong>订单检索：</strong></td>
            <td width="90%">
       <select name="status">
			<option value="">显示全部</option>
			<option value="1" <?php if($status == 1){echo 'selected="selected"';}?>>未领取</option>
			<option value="2" <?php if($status == 2){echo 'selected="selected"';}?>>已领取</option>
  	    </select>
        <select name="giftcode">
        	<option value="">选择礼品</option>
            <?php foreach($giftList as $var):?>
            <option value="<?php echo $var['giftcode'];?>" <?php if($giftcode == $var['giftcode']) echo 'selected="selected"';?>><?php echo $var['giftname'];?></option>
            <?php endforeach?>
        </select>
        <select name="areacode">
        	<option value="">选择校区</option>
        	<?php foreach($areaList as $var):?>
            <option value="<?php echo $var['scode'];?>" <?php if($areacode == $var['scode']) echo 'selected="selected"';?>><?php echo $var['sname'];?></option>
            <?php endforeach?>
        </select>&nbsp;&nbsp;
        
        下单日期：从<input name="date_start" type="text" id="date_start" size="10" maxlength="10" value="<?php echo $date_start;?>" onclick="new Calendar().show(this);" readonly />至<input name="date_end" type="text" id="date_end" size="10" value="<?php echo $date_end;?>" maxlength="10" onclick="new Calendar().show(this);" readonly />
        <span class="tishiyu">（如果选择日期，第二个日期必须大于第一个）</span>
        	</td>
        </tr>
        <tr>
        	<td>&nbsp;</td>
            <td>
            订单号：<input name="orderid" type="text" id="orderid" size="15" value="<?php echo $orderid;?>">&nbsp;&nbsp;
            姓名：<input name="sname" type="text" id="sname" size="12" value="<?php echo $sname;?>">&nbsp;&nbsp;
            学号：<input name="saliascode" type="text" id="saliascode" size="12" value="<?php echo $saliascode;?>">&nbsp;&nbsp;
            手机号：<input name="stumobile" type="text" id="stumobile" size="12" value="<?php echo $stumobile;?>">&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="  搜 索  "></td>
        </tr>
    </table>
    </form>
	</div>

	<h2>订单列表</h2>   
    
	<table width="100%" border="0" class="tableList">
	  <tr>
	    <th width="6%">预约单号</th>
	    <th width="10%">下单时间</th>
        <th width="13%">礼品名称</th>
        <th width="4%">数量</th>
	    <th width="8%">收货人</th>
	    <th width="9%">学号</th>
        <th width="10%">联系电话</th>
        <th width="10%">校区</th>
        <th width="6%">订单状态</th>
        <th width="11%">领取时间</th>
		<th width="13%">操作</th>
      </tr>
      <?php $i = 0; foreach($ordersList as $var):?>
      <?php 
	  	$i++;
		$tr_ou = $i%2 ? '' : 'tr_ou';
	  ?>
	  <tr class="<?php echo $tr_ou;?>">
	    <td><?php echo $var['orderid'];?></td>
	    <td><?php echo date('Y-m-d H:i:s',$var['begintime']);?></td>
	    <td><?php echo $var['giftname'];?></td>
	    <td><?php echo $var['giftquantity'];?></td>
	    <td><?php echo $var['sname'];?></td>
        <td><?php echo $var['saliascode'];?></td>
        
	    <td width="10%"><?php echo $var['stumobile'];?></td>
	    <td width="10%"><?php echo $var['aname'];?></td>
        <td><?php echo $var['status'] == 1 ?  '未领': '<font color="#FF0000">已领</font>';?></td>
        <td><?php echo $var['status'] == 1 ?  '未领': date('Y-m-d H:i:s',$var['endtime']);?></td>
        
        <td><?php if($var['status'] == 1){?>
        	<a href="<?php echo U('Goods/GoodsOrders/ordersUpdate');?>/giftcode/<?php echo $var['giftcode']?>/areacode/<?php echo $var['areacode']?>/serial/<?php echo $var['serial']?>/status/2/giftquantity/<?php echo $var['giftquantity'];?>" onclick=" return confirm('确定发货吗？')">确认发货</a>
            &nbsp;&nbsp;
            <a href="<?php echo U('Goods/GoodsOrders/ordersDel');?>/giftcode/<?php echo $var['giftcode']?>/areacode/<?php echo $var['areacode'];?>/serial/<?php echo $var['serial']?>/status/1/giftquantity/<?php echo $var['giftquantity'];?>/stucode/<?php echo $var['stucode'];?>" onclick=" return confirm('一旦撤销就无法恢复，您确定要撤销吗？')">撤销此订单</a>
            <?php }else{?>
            <?php if($permValue & $PERM_WRITE):?>
            <a href="<?php echo U('Goods/GoodsOrders/ordersUpdate')?>/giftcode/<?php echo $var['giftcode']?>/areacode/<?php echo $var['areacode']?>/serial/<?php echo $var['serial']?>/status/1/giftquantity/<?php echo $var['giftquantity'];?>" onclick=" return confirm('确定改为未领？')">改为未领</a>
            <?php endif?>
            <?php }?>
        </td>
      
      </tr>
      <?php endforeach?>
	  <tr>
	    <td colspan="11" align="center"><?php echo $showPage;?> <span style="float:right; margin-right:10px; font-size:14px; font-weight:bold; color:# F00;"><a href="<?php echo U('Goods/GoodsOrders/orderDown');?>"> >>下载报表</a></span></td>
      </tr>
    </table>
	</div>
</div>
</div>

</body>
</html>