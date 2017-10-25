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
	<h1><?php echo $one_Circleinfo;?>  &nbsp;&nbsp;<a href="javascript:history.go(-1);">返回</a></h1>
    <br />
<div id="main" style="">
	
	<div id="list" style="border: 1px solid silver; padding-top:10px; padding-left:10px; width:500px;" >
		<?php if($evalInfo):?>
	       <table>
           <span  style="font-size: 20px;color: #2262B7;">评价时间：<?php echo $evalInfo['create_time']?></span>
            <br /><br />
            <span style="font-size: 20px;color: #2262B7;">上课校区：</span>
            <tr>                
                <div><p style="font-size: 16px;">校区环境：<?php echo $evalInfo['environment_img']?></p></div>
                <div><p style="font-size: 16px;">校区服务：<?php echo $evalInfo['service_img']?></p></div>
                <div><p style="font-size: 16px;">校区活动：<?php echo $evalInfo['activity_img']?></p></div>
            </tr>
            <br />
            <span style="margin-left: 50px;">------------------------------------------------------------------</span>
            <br />
            
            <span style="font-size: 20px;color: #2262B7;">学管师：</span>
            <tr >                
                <div><p style="font-size: 16px;">沟通能力：<?php echo $evalInfo['communication_img']?></p></div>
                <div><p style="font-size: 16px;">专业知识：<?php echo $evalInfo['professional_img']?></p></div>
                <div><p style="font-size: 16px;">解决问题：<?php echo $evalInfo['solve_img']?></p></div>              
            
            </tr>
            <br />
            <span style="margin-left: 50px;">------------------------------------------------------------------</span>
            <br />
            <span style="font-size: 20px;color: #2262B7;">留言：</span>
            <tr>                
                <div><p style="font-size: 16px;"><?php echo $evalInfo['content']?></p></div>            
            </tr>
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

