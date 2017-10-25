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
		<form  method="get">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
			<tr>
		   		<td style="text-align:center; margin:0 auto;">
		     		<h1>推荐列表</h1>
		    	</td>
		    </tr>
		    <tr>		   	
		    </tr>
		</table>
        <div>学员姓名：<input type="text" name="search" value="<?php echo $search;?>" placeholder="请输入"><input type="submit" name="submit" value="搜索" />
		<span style="margin-left: 50px;"><a href="/vip/vip_address/explodrommended" >导出推荐列表</a></span>
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
		<?php if($vipRecommendedList):?>
		<table width="80%">
			<tr bgcolor="#dddddd" height=35>
				<td>&nbsp;&nbsp;&nbsp;ID号</td>
				<td>推荐人id</td>
                <td>推荐人姓名</td>  
                <td>推荐人推荐码</td>               
                <td>推荐时间</td>
				<td>学生姓名</td>
				<td>联系电话</td>
				<td>预辅导课程</td>
                <td>期望上课校区</td>
			</tr>
			<?php foreach($vipRecommendedList as $k=>$v):?>
			<tr height=30>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $v['id'];?></td>
                <td><?php echo $v['uid'];?></td>
				<td><?php echo $v['uname'];?></td>
                <td><?php echo $v['urecode'];?></td>					
                <td><?php echo $v['create_time']; ?></td>   	         
                <td><?php echo $v['sname']; ?></td>
                <td><?php echo $v['sphone']; ?></td>
                <td><?php echo $v['scourse']; ?></td>
                <td><?php echo $v['scampus']; ?></td>
               
			</tr>
			<?php endforeach?>
		</table>
		<div id="pageStr">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $showPage;?></div>
		<?php else:?>
		<div>暂无相关信息</div>
		<?php endif;?>
	</div>
    <div class="test" style="display:none;">
    dsfsfsfsf;
    </div>
</div>
</div>
</body>
</html>

