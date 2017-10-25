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
		     		<h1>评价列表</h1>
		    	</td>
		    </tr>
		    <tr>		   	
		    </tr>
		</table>
        <div>学员姓名/年-月份：<input type="text" name="search" value="<?php echo $search;?>" placeholder="请输入"><input type="submit" name="submit" value="搜索" />
            <span style="color: red;">* 搜索月份日期时间格式请按 2016-01 </span>
			<span style="margin-left: 50px;"><a href="/vip/vip_address/explodevaluation" >导出评价列表</a></span>
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
		<?php if($vipEvaluationList):?>
		<table width="80%">
			<tr bgcolor="#dddddd" height=35>
				<td>&nbsp;&nbsp;&nbsp;ID号</td>
				<td>学号</td>
				<td>学生姓名</td>
				<td>评价时间</td>
				<td>详情</td>
                
			</tr>
			<?php foreach($vipEvaluationList as $k=>$v):?>
			<tr height=30>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $v['id'];?></td>
                <td><?php echo $v['studentcode'];?></td>
				<td><?php echo $v['studentname'];?></td>				
                <td><?php echo $v['create_time']; ?></td>   	         
                <td>
                    <a href="/vip/vipAddress/evaluationInfo/id/<?php echo $v['id']?>" style="color: #2262B7;" >查看</a>
                    <!--a href="#" onclick="evalInfo('<?php echo $v['id']?>'))" style="color: #2262B7;">查看</a-->                    
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
</body>
</html>

