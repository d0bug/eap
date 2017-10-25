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
	<h2><?php echo $goodsArr[0]['giftname'];?>  -- 入库详细记录</h2>   
    
	<table width="98%" border="0" class="tableList">
	  <tr>
	    <th width="10%">ID</th>
	    <th width="10%">数量</th>
        <th width="30%">说明</th>
        <th width="25%">操作人</th>
	    <th width="25%">时间</th>
      </tr>
      <?php $i = 0; foreach($giftPurList as $var):?>
      <?php 
	  	$i++;
		$tr_ou = $i%2 ? '' : 'tr_ou';
	  ?>
	  <tr class="<?php echo $tr_ou;?>">
	    <td><?php echo $var['serial'];?></td>
	    <td><?php echo $var['purchasequantity'];?></td>
	    <td><?php echo $var['remark'];?></td>
	    <td><?php echo $var['operator'];?></td>
	    <td><?php echo date('Y-m-d H:i:s', $var['time']);?></td>
      </tr>
      <?php endforeach?>
	  <tr>
	    <td colspan="5" align="center"><?php echo $showPage;?></td>
      </tr>
    </table>
	</div>
    
    
</div>
</div>

</body>
</html>