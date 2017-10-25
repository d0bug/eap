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
    if(checkspace(document.add_goods.giftName.value)){
	   document.add_goods.giftName.focus();
       alert("礼品名称不能为空!");
	   return false;
    }
    if(checkspace(document.add_goods.giftCode.value)){
	   document.add_goods.giftCode.focus();
       alert("编号不能为空!");
	   return false;
    }
	if(checkspace(document.add_goods.costValue.value)){
	   document.add_goods.costValue.focus();
       alert("积分不能为空，且必须是数字!");
	   return false;
    }
	if(checkspace(document.add_goods.giftDetail.value)){
	   document.add_goods.giftDetail.focus();
       alert("介绍不能为空!");
	   return false;
    }
	
}
</script>
</head>
<body >
<div region="center" >
<div id="main">

	<h2>添加礼品卡</h2>
    
    <form id="add_goods" name="add_goods" method="POST" enctype="multipart/form-data" action="<?php echo U('Goods/GoodsMain/goodsAdd');?>">
    	<input type="hidden" name="action" value="add">
	<table width="95%" border="0" class="tableList" id="search">
	  <tr>
	    <td width="12%" height="30">礼品名称：</td>
	    <td width="88%">
	      <input name="giftName" type="text" size="30"><font color="#FF0000">*</font></td>
      </tr>
	  <tr>
	    <td height="36">礼品卡编号：</td>
	    <td>
	      <input type="text" name="giftCode" value="<?php echo 'sn'.date('YmdHis')?>"><font color="#FF0000">*</font>
	      若不填写系统会默认使用一个值
	    </td>
      </tr>
	  <tr>
	    <td height="29">需要积分：</td>
	    <td><input name="costValue" type="text" size="6" maxlength="5" onKeyUp="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">
	    <font color="#FF0000">*</font>只能输入数字</td>
      </tr>
        <tr>
            <td height="29">赠品ID：</td>
            <td><input name="present_id" type="text" size="6" style="ime-mode:disabled">
                <font color="#FF0000">*</font>请输入赠品ID</td>
        </tr>
        <tr>
	  <tr>
	    <td height="36">显示状态：</td>
	    <td><input type="radio" name="isValid"  value="1" >
上架  &nbsp;&nbsp;
<input type="radio" name="isValid"  value="2" checked>
下架</td>
      </tr>
	  <tr>
	    <td height="30">上传图片：</td>
	    <td><input name="giftImage" id="image" type="file" /></td>
	    </tr>
	  <tr>
	    <td height="30">礼品介绍：</td>
	    <td>&nbsp;</td>
      </tr>
	  <tr>
	    <td height="37" colspan="2">
	      <textarea name="giftDetail" cols="80" rows="8" ></textarea><font color="#FF0000">*</font></td>
	    </tr>
	  <tr>
	    <td>&nbsp;</td>
	    <td><input type="submit" name="button" id="button" value=" 提 交 " onClick="return goodscheck();"></td>
      </tr>
    </table>
    </form>
    
</div>
</div>
</body>
</html>