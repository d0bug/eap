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
<link href="/static/css/mgs.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<h2>礼品卡列表</h2>   
    
    <a href="<?php echo U('Goods/GoodsMain/downAll')?>">>>下载所有校区分配情况</a>
    
	<table width="95%" border="0" class="tableList">
	  <tr>
	    <th width="11%">礼品编号</th>
	    <th width="22%">礼品名称</th>
        <th width="13%">所需积分</th>
        <th width="12%">上架状态</th>
	    <th width="12%">库存数量</th>
	    <th colspan="3">操作</th>
      </tr>
      <?php $i = 0; foreach($goodsList as $var):?>
      <?php 
	  	$i++;
		$tr_ou = $i%2 ? '' : 'tr_ou';
	  ?>
	  <tr class="<?php echo $tr_ou;?>">
	    <td><?php echo $var['giftcode'];?></td>
	    <td><?php echo $var['giftname'];?></td>
	    <td><?php echo $var['costvalue'];?></td>
	    <td><?php echo $var['isvalid'] == 1 ?  '上架': '<font color="#FF0000">下架</font>';?></td>
	    <td><?php echo $var['stockquantity'];?></td>
        <td width="15%">
		
        <a href="<?php echo U('Goods/GoodsMain/goodsCount')?>/action/info/serial/<?php echo $var['serial']?>">分配到校区</a>
		&nbsp;&nbsp;&nbsp;
        <a href="<?php echo U('Goods/GoodsMain/goodsCount')?>/action/down/serial/<?php echo $var['serial']?>">报表下载</a>
        
        </td>
        
	    <td width="7%"><a href="<?php echo U('Goods/GoodsMain/goodsEdit')?>/action/info/serial/<?php echo $var['serial']?>">修改</a></td>
	    <td width="8%"><a href="<?php echo U('Goods/GoodsMain/goodsDel')?>/serial/<?php echo $var['serial']?>" onclick=" return confirm('一旦删除不可恢复!确实要删除吗?')">删除</a></td>
      </tr>
      <?php endforeach?>
	  <tr>
	    <td colspan="8" align="center"><?php echo $showPage;?></td>
      </tr>
    </table>
	</div>
    
    
</div>
</div>

</body>
</html>