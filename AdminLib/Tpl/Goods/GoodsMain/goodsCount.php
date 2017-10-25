<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<script type="text/javascript" src="/static/js/jquery.uploadify-3.1.min.js"></script>
<script type="text/javascript" src="/static/js/goods.js"></script>
<link href="/static/css/uploadify.css" type="text/css" rel="stylesheet" />
<link href="/static/css/mgs.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
function checkspace(checkstr) {
  var str = '';
  for(i = 0; i < checkstr.length; i++) {
    str = str + ' ';
  }
  return (str == checkstr);
}
function goodscheck(){
	if(checkspace(document.add_goods.stockQuantity.value)){
	   document.add_goods.stockQuantity.focus();
       alert("入库数不能为空且必须是数字!");
	   return false;
    }

}

function checknum(obj)
{
 var re = /^-?[0-9]*(.d*)?$|^-?0(.d*|0)?$/; 
     if (!re.test(obj.value))
    {
        alert("非法字符,只能输入数字或负数");
  obj.value="";
        obj.focus();
        return false;
     }
} 

// 新窗口打开函数 
function MM_openBrWindow( url, winName, width, height)   
{  
xposition=0; yposition=0;  
if ((parseInt(navigator.appVersion) >= 4 ))  
{  
xposition = (screen.width - width) / 2;  
yposition = (screen.height - height) / 2;  
}  
theproperty= "width=" + width + ","   
+ "height=" + height + ","   
+ "location=0,"   
+ "menubar=0,"  
+ "resizable=1,"  
+ "scrollbars=0,"  
+ "status=0,"   
+ "titlebar=0,"  
+ "toolbar=0,"  
+ "hotkeys=0,"  
+ "screenx=" + xposition + "," //仅适用于Netscape  
+ "screeny=" + yposition + "," //仅适用于Netscape  
+ "left=" + xposition + "," //IE  
+ "top=" + yposition; //IE   
	window.open( url,winName,theproperty );  
}  

</script>
</head>
<body >
<div region="center" >
<div id="main">
	<h2>校区分配</h2>
    
    
	<table width="85%" border="0" cellpadding="0" cellspacing="0" class="tableList" >
		<tr>
        	<td width="24%" rowspan="6" align="center" valign="middle"><b>礼品编号：</b><?php echo $goodsArr[0]['giftcode'];?></td>
	    	<td width="17%" height="30"><strong>礼品名称：</strong><?php echo $goodsArr[0]['giftname'];?></td>
	    	<td width="59%"><strong>可用库存：</strong><span class="zongkucun"><?php echo $goodsArr[0]['stockquantity'];?></span></td>
      	</tr>
	</table>
 
    <form id="add_goods" name="add_goods" method="POST" action="<?php echo U('Goods/GoodsMain/goodsCount');?>">
    	<input type="hidden" name="action" value="add"><!--标示 执行入库记录-->
        <input type="hidden" name="serial" value="<?php echo $goodsArr[0]['serial'];?>">
	 <table width="85%" border="0" cellpadding="0" cellspacing="0" class="tableList" id="search">
      <tr><td colspan="2"><strong>总库存管理：</strong></td></tr>
      <tr>
	    <td width="20%"><strong>总入库：</strong><span class="zongkucun"><?php echo $goodsZongruku;?></span>&nbsp;&nbsp;&nbsp;&nbsp; <a href="#" onClick="window.open('<?php echo U('Goods/GoodsMain/giftPurHistory')?>/serial/<?php echo $goodsArr[0]['serial'];?>/giftcode/<?php echo $goodsArr[0]['giftcode'];?>','','status=no,scrollbars=no,top=20,left=110,width=650,height=420')"> >>详细 </a></td>
	    <td>新入库：<?php if($permValue & $PERM_WRITE){?><input type="type" name="stockQuantity" size="6" maxlength="5" onkeyup="checknum(this)"><font color="#FF0000">*</font>必须输入数字（可以是负数）&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        
	      <input type="submit" name="button" id="button" value=" 提 交 " onClick="return goodscheck();">
         <?php }else{ echo ' 无权限 ';}?>
         </td>
	    </tr>
    </table>
    </form>
    
    
    
    <br /><!--各校区分配情况-->
    <table width="85%" border="0" cellpadding="0" cellspacing="0" class="tableList" >
    	<tr>
    		<th width="15%">校区</th>
            <th width="8%">总分配量</th>
            <th width="8%">已领取</th>
            <th width="8%">库存</th>
            <th width="8%">已预订</th>
            <th width="8%">实际库存</th>
            <th width="25%">新增</th>
            <th width="20%">备注</th>
    	</tr>
        
        <?php $i = 0; foreach($goodsArea as $var):?>
        <?php 
	  	$i++;
		$tr_ou = $i%2 ? '' : 'tr_ou';
	  ?>
        <tr class="<?php echo $tr_ou;?>">
    		<td><?php echo $var['sname'];?></td>
            <td><?php echo $var['totalquantity'];?></td>
            <td><?php echo $var['sellquantity'];?></td>
            <td><?php echo $var['totalquantity'] - $var['sellquantity'] ;?></td>
            <td><?php echo $var['bookquantity'];?></td>
            <td><?php echo $var['realquantity'];?></td>
            <td>
            <?php if($permValue & $PERM_WRITE){?>
            <form name="<?php echo $var['areacode'] .'_'.$var['id'];?>" method="POST" action="<?php echo U('Goods/GoodsMain/goodsCount');?>">
            	<input type="hidden" name="serial" value="<?php echo $goodsArr[0]['serial'];?>">
                
                <input type="text" name="goodsCount" size="5" onkeyup="checknum(this)" value="0">
            	<input type="hidden" name="action" value="areaadd"><!--执行校区库存修改  入库-->
            	<input type="hidden" name="sid" value="<?php echo $var['id']; ?>">&nbsp;&nbsp;
            	<input type="submit" name="button" id="button" value=" 分 配 ">&nbsp;&nbsp; 
            </form>
           <?php }else{ echo '无权限';}?>
           </td>
            <td><a onClick="MM_openBrWindow('<?php echo U('Goods/GoodsMain/goodsAreaList')?>/sid/<?php echo $var['id'];?>','', 600,400)">操作记录</a></td>
    	</tr>
        <?php endforeach;?>
        
    </table>
    
    

    
    
</div>
</div>
</body>
</html>