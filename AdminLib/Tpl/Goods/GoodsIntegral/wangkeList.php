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


function jifencheck(){
	if(checkspace(document.addform.wangkejifen.value)){
	   document.addform.wangkejifen.focus();
       alert("积分不能为空!");
	   return false;
    }else{
		var int = document.addform.wangkejifen.value;
		var sname = '<?php echo $stuArr['sname'];?>';
		return confirm("请核对: \n\n您将给 "+sname+" 充值 "+int+"分 \n\n如果确认无误请点\"确定\"   ,如果要修改请点\"取消\"");
	}
}
</script>
<link href="/static/css/mgs.css" type="text/css" rel="stylesheet" />

</head>
<body>
<div region="center">
<div id="main">
	<h2>积分管理 > 网课赠分</h2> 
    <div id="search">
    <form id="search_form" name="search_form" method="GET" action="<?php echo U('Goods/GoodsIntegral/wangkeList');?>">
    	<input type="hidden" name="action"  value="search" />
		<table width="100%">
    		<tr>
        		<td width="8%"><strong>学员查询：<!--测试号：991201015   130306001 --></strong></td>
            	<td width="92%"><input name="saliascode" type="text" id="saliascode" value="请输入完整的学号" onfocus="this.value=''" style=" color:#999;">&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="  查  询  " onClick="return goodscheck();"><?php echo $strings;?></td>
    		</tr>
    	</table>
    </form>
    </div>

<?php if($action == 'search' && $tags == 1){?>  <!--/     ******************** 有记录的操作 *991201015*******************     /-->  
	<table width="100%" class="tableList" style="border:none;">
    	<tr>
        	<td height="32"><strong>您查询到的学员信息如下</strong></td>
    	</tr>
        <tr>
        	<td height="32">
           	  <b>学生姓名：</b><?php echo $stuArr['sname'];?>&nbsp;&nbsp;&nbsp;&nbsp;
              <b>学号：</b><?php echo $stuArr['saliascode'];?>&nbsp;&nbsp;&nbsp;&nbsp;
                <b>联系电话：</b><?php echo $stuArr['smobile'];?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
                <b>网课总赠分：</b><?php echo $integralArr['netclassintegral'];?>&nbsp;&nbsp;&nbsp;&nbsp;
                <b>充值卡总分：</b><?php echo $integralArr['cardintegral'];?>&nbsp;&nbsp;&nbsp;&nbsp;
                <b>课程总积分：</b><?php echo $integralArr['lessonintegral'];?>
          </td>
    	</tr>
	</table>
    
    
    
    <table width="98%" border="0" class="tableList">
	  <tr>
	    <th width="12%">学生姓名</th>
	    <th width="9%">学号</th>
        <th width="17%">联系电话</th>
        <th width="14%">积分类型</th>
	    <th width="15%">积分数额</th>
        <th width="19%">操作时间</th>
        <th width="14%">操作人</th>
      </tr>
      <?php $i = 0; foreach($integralList as $var):?>
      <?php 
	  	$i++;
		$tr_ou = $i%2 ? '' : 'tr_ou';
	  ?>
	  <tr class="<?php echo $tr_ou;?>">
	    <td><?php echo $stuArr['sname'];?></td>
	    <td><?php echo $stuArr['saliascode'];?> </td>
	    <td><?php echo $stuArr['smobile'];?></td>
	    <td><?php if($var['tablename'] == 'a'){echo '充值卡积分';}elseif($var['tablename'] == 'b'){ echo '网课曾分';}?></td>
	    <td><?php echo $var['integralvalue'];?></td>
        <td><?php echo date('Y-m-d H:i:s', $var['time']);?></td>
        <td><?php echo $var['operator'];?></td>
      </tr>
      <?php endforeach?>
	  <tr>
	    <td colspan="7" align="center"><?php echo $showPage;?></td>
      </tr>
    </table>
    
    <div id="search">
    <form id="addform" name="addform" method="GET" action="<?php echo U('Goods/GoodsIntegral/integralAdd');?>">
        <input type="hidden" name="saliascode"  value="<?php echo $stuArr['saliascode'];?>" />
    	<table>
        	<tr>
        		<td height="40"><strong>充值金额：</strong><input type="text" name="wangkejifen" onkeyup="checknum(this)" id="wangkejifen">&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="  确  定  " onclick='return jifencheck();'></td>
    		</tr>
    	</table>
    </form>
    </div>
    

<!--/     ******************** 无记录的操作 ********************     /-->  
<?php }elseif($action == 'search' && $tags == 0 ){?>
	<span style=" font-size:16px; color:#900; font-weight:bold;">查无此学员或此学号无效，请核对你输入的学号！</span>
<?php }?>


</div>
</div>

</body>
</html>