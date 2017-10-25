<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" /> 
<title>文档上传</title> 
</head> 
<body> 
<script language="javascript">
//动态添加文件选择控件
function AddRow() 
{ 
var eNewRow = tblData.insertRow(); 
for (var i=0;i<1;i++) 
{ 
var eNewCell = eNewRow.insertCell(); 
eNewCell.innerHTML = "<tr><td><input type='file' name='filelist[]' size='50'/></td></tr>"; 
} 
} 
</script> 
<form name="myform" method="post" action="/vip/vipTraining/dict_add_powerpoint_img" enctype="multipart/form-data" > 

<table id="tblData" width="400" border="0"> 

<!-- 将上传文件必须用post的方法和enctype="multipart/form-data" --> 
<!-- 将本页的网址传给uploadfile.php--> 

<input name="postadd" type="hidden" value="<?php echo "http://".$_SERVER['HTTP_HOST'].$_SERVER["PHP_SELF"]; ?>" /> 
<input  name="id" type="hidden" value="<?=$id?>"/>

<tr><td>文件上传列表 

<input type="button" name="addfile" onclick="AddRow()" value="添加列表" /></td></tr> 

<!-- filelist[]必须是一个数组--> 

<tr><td><input type="file" name="filelist[]" size="50" /></td></tr> 

</table> 

<input type="submit" name="submitfile" value="提交文件" /> 

</form> 

</body>
</html>
