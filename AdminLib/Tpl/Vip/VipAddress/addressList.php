<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<div id="search">
		<form  action="/vip/vip_address/addressList" method="get">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
			<tr>
		    </tr>
		    <tr>
		   		<td style="text-align:center; margin:0 auto;">
		     		<h1>本公司由申通快递发货</h1>
		    	</td>
		    </tr>
		</table>
        <div>学员姓名/发货日期/物流单号：<input type="text" name="search" value="<?php echo $search;?>" placeholder="请输入"><input type="submit" name="submit" value="搜索" />
            <span style="color: red;">* 搜索发货日期时间格式请按 2016-01-01 </span>
        </div>
		</form>
	</div>
	<div id="search" style="text-align:right; margin:0 auto;">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
			<td>
				   
			</td>
		</table>
	</div>
	<hr>
	<div id="list" class="clearfix">
		<?php if($vipComplaintList):?>
		<table width="80%">
			<tr bgcolor="#dddddd" height=35>
				<td>&nbsp;&nbsp;&nbsp;ID号</td>
				<td>学号</td>
				<td>学员姓名</td>
				<td>奖品名称</td>
				<td>行为</td>
                <td>发货地址</td>
                <td>兑换时间</td>
                <td>发货状态</td>
                <td>发货时间</td>
                <td>物流单号</td>
                <td>操作</td></td>
			</tr>
			<?php foreach($vipComplaintList as $k=>$v):?>
			<tr height=30>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $v['id'];?></td>
				<td><?php echo $v['ucode'];?></td>
				<td><?php echo $v['uname'];?></td>
				<td><?php echo $v['pr_name'];?></a></td>
				<td><?php if($v['dh_cj'] == 1)echo "兑换";elseif($v['dh_cj'] == 2)echo "抽奖";?></td>
                <td><?php echo $v['address_info'];?></td>
                <td><?php echo $v['re_time']; ?></td>
   	            <td><?php if($v['delivery_status'] == 1)echo "<p style='color:red'>未发货</p>";elseif($v['delivery_status'] == 2)echo "<p style='color:blue'>已发货</p>";?></td>
                <?php if($v['de_time'] != NULL):?>               
                <td><?php echo $v['de_time'];?></td>
                <?php else:?>
                <td>暂无</td>
                <?php endif;?>                
                <?php if($v['logistics'] != NULL):?>                
                <td><?php echo $v['logistics'];?></td>
                <?php else:?>
                <td>暂无</td>
                <?php endif;?>
                <td>                    
                    <!--a href="javascript:void(0);" onclick="testMessageBox_CircleInfo(event,'edit','<?php echo U('Vip/vipAddress/editAddress',array('id'=>$v['id']))?>')" style="color: #2262B7;">编辑</a-->
                    <a href="/vip/vipAddress/editAddress/id/<?php echo $v['id']?>" style="color: #2262B7;">编辑</a>
                    
                </td>
			</tr>
			<?php endforeach?>
		</table>
		<div id="pageStr">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $showPage;?></div>
		<?php else:?>
		<div>暂无相关信息</div>
		<?php endif;?>
	</div>
</div>
</div>
<script type="text/javascript">
function testMessageBox_CircleInfo(ev,type,requestUrl){
	var objPos = mousePosition(ev);
	
	if(type == 'edit'){
		title = '编辑';
	}

	messContent="<div class=\"mesWindowsBox\" style=\"height:auto;min-height:500px;\"><iframe src=\""+requestUrl+"\" width=\"750\" height=\"500\" style=\"border:0px\"></iframe></div>";
	showMessageBox(title,messContent,objPos,780,0);
}

</script>
</body>
</html>
