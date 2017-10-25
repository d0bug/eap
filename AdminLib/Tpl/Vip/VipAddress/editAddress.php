<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script src="/static/kindeditor/kindeditor-min.js" type="text/javascript"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<script type="text/javascript" src="/static/js/DatePicker/WdatePicker.js"></script>
<link href="/static/kindeditor/themes/default/default.css" rel="stylesheet">
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<h1><?php echo $one_Circleinfo;?>  &nbsp;&nbsp;<a href="/vip/vipAddress/addressList">返回列表</a></h1>
    <br />
    <form id="CircleOperate" name="CircleOperate" method="POST"  action="<?php echo U('Vip/VipAddress/doEditAddress');?>">
	<div id="list" class="clearfix" style="border:1px solid #dddddd;padding:10px 10px 10px 10px; width:600px;" >
    
		<?php if($recordInfo):?>
		<table >
			<tr bgcolor="#dddddd" height=20 >
				<td>&nbsp;&nbsp;&nbsp;订单信息</td>			
			</tr>
			<?php foreach($recordInfo as $key=>$val):?>
			<tr height=30><td >学号：&nbsp<?php echo $val['ucode'];?></td></tr>
            
			<tr height=30><td>学生姓名：&nbsp<?php echo $val['uname'];?></td></tr>
			
            	<tr height=30><td>商品名称：&nbsp<?php echo $val['pr_name'];?></td></tr>
                <tr height=30><td>行为：&nbsp<?php if($val['dh_cj'] == 1)echo "兑换";elseif($val['dh_cj'] == 2)echo "抽奖";?></td></tr>
				<tr height=30><td>兑换时间：&nbsp<?php echo $val['re_time'];?></td></tr>                
			
			
         </table>
         <table>   
            <tr height=30  >
    			
    			<td><font color="red">*</font>发货状态 :&nbsp
    				<input type="radio" id="delivery_status" name="delivery_status" value="1" <?php if($val['delivery_status'] == 1):?>checked<?php endif;?>>未发货&nbsp;&nbsp;
    				<input type="radio" id="delivery_status" name="delivery_status" value="2" <?php if($val['delivery_status'] == 2):?>checked<?php endif;?>>已发货
    			</td>
		  </tr>
          
          <tr height=30  >
            <td>
                时间：
                <input type="text" id="time" name="time" value="<?php echo $val['de_time']?>" class="Wdate" onClick="WdatePicker()">
            </td>
          </tr>
          <tr height=30>
            <td>
            物流单号：<input type="text" name="danhao" value="<?php echo $val['logistics']?>" style="width: 200px;">
            </td>
          </tr>
          </table>
          <br />
		   <?php if($addressInfo == true):?>
          <table>          
          <tr bgcolor="#dddddd" height=20 ><td>&nbsp;&nbsp;&nbsp;收货地址</td></tr>
          <tr height=30  >
    			<td class="alt"><font color="red">*</font>是否修改发货地址:&nbsp </td>
    			<td>
    				<input type="radio" id="newup" name="newup" value="1" <?php if($val['newup'] == 1):?>checked<?php endif;?>>修改&nbsp;&nbsp;
    				<input type="radio" id="newup" name="newup" value="2" <?php if($val['newup'] == 2):?>checked<?php endif;?>>不修改
    			</td>
                <td><font color="red">* 注：默认为不修改，如果修改用户原有地址 请选 修改！</font></td>
		  </tr>
          </table>
		 
          <table>
          <?php if($val['newup'] == 2):?>
          <?php foreach($addressInfo as $k=>$v):?>
          <tr height=30><!--不修改-->
            <td>
            姓名：&nbsp<input type="text" name="newname" value="<?php echo $v['recipient']?>" style="width: 400px;">
            </td>
          </tr>
          
          <tr height=30>
            <td>
            电话：&nbsp<input type="text" name="newphone" value="<?php echo $v['re_phone']?>" style="width: 400px;">
            </td>
          </tr>
          
          <tr height=30>
            <td>
            收货地址：&nbsp<input type="text" name="newaddress" value="<?php echo $v['re_city'].$v['re_address'];?>" style="width: 400px;">
            </td>
          </tr>
          <?php endforeach?>
          <?php elseif($val['newup'] == 1):?>
          <tr height=30><!--修改-->
            <td>
            姓名：&nbsp<input type="text" name="newname" value="<?php echo $val['new_name']?>" style="width: 400px;">
            </td>
          </tr>
          
          <tr height=30>
            <td>
            电话：&nbsp<input type="text" name="newphone" value="<?php echo $val['new_phone']?>" style="width: 400px;">
            </td>
          </tr>
          
          <tr height=30>
            <td>
            收货地址：&nbsp<input type="text" name="newaddress" value="<?php echo $val['new_address'];?>" style="width: 400px;">
            </td>
          </tr>
          <?php endif?>
          </table>
		  
		  <?php endif?>
		  
          <table>
          <tr height=60 >
			<td style="padding-left: 50px;">&nbsp;</td>
			<td><input type="hidden" id="id" name="id" value="<?php echo $val['id']?>">
			    <input type="submit" class="btn" value="确认提交"></td>
	 	 </tr>
			<?php endforeach?>
		</table>
		
		<?php else:?>
		<div>暂无信息</div>
		<?php endif;?>	
    </div>
    </form>
    
</div>
</div>

</body>
</html>