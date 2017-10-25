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
<script type="text/javascript">
function checkspace(checkstr) {
  var str = '';
  for(i = 0; i < checkstr.length; i++) {
    str = str + ' ';
  }
  return (str == checkstr);
}

function goodscheck(){
	if(checkspace(document.search_form.saliascode.value)){
	   document.search_form.saliascode.focus();
       alert("学号不能为空!");
	   return false;
    }
}

</script>
<link href="/static/css/mgs.css" type="text/css" rel="stylesheet" />

</head>
<body>
<div region="center">
<div id="main">

    <h2>网课赠分  批量导入</h2>
    <div id="search">
    	
    	<form id="add_goods" name="add_goods" method="POST" enctype="multipart/form-data" action="<?php echo U('Goods/GoodsIntegral/excelUpall');?>">
        请选择文件：<input name="execlfile" id="execlfile" type="file" />
        <input type="submit" name="button" id="button" value=" 批量导入 ">
        </form>
       
        <p>&nbsp;&nbsp;</p>
        <h3>使用说明:</h3>
        <ul>
        	<li>1、只能上传后缀为xls格式的Excel文件,请先下载范例文件&nbsp;&nbsp;<a href="/static/images/wkzf_examp.xls"><font color=red> >>范例文件下载</font></a></li>
            <li>2、在范例文件中请保留第一行表头“学员编号”“网课赠分”这一表头信息（如下图）<br></li>
            <li><img src="/static/images/wkzf_examp.gif" alt="使用说明"></li>
            <li>3、学员编号必须全部正确；网课赠分必须全部为数字且大于零</li>
        </ul>
   		
    </div>


</div>
</div>

</body>
</html>