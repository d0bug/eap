<!DOCTYPE HTML>
<html lang="zh-cn" xmlns="http://www.w3.org/1999/html">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
<style type="text/css">
    #box{width:100%;font-size:14px;padding-bottom: 10px;}
    #box ul{margin:0;padding:0;list-style:none}
    #box #tab{height:26px;border-bottom:1px solid #dfdfdf;padding-bottom: 10px;padding-left: 100px;}
    #box #tab li{width:100px;height:18px;padding-top:8px;margin-right:50px;text-align:center;float:left;cursor:pointer;position:relative;top:1px;}
    #box #tab li.on{color:#00f;background-position:-110px 0}
    #box #tab_con{border:1px solid #dfdfdf;border-top:none;padding:20px}
    #box #tab_con li{display:none}
    #box #tab_con #tab_con_1{display:block}
</style>
</head>
<body>
<div id="box" style="margin-bottom: 10px;">
    <ul id="tab">
        <li ><a href="/vip/vip_address/visitCountList">学员活跃量统计</a></li>
        <li ><a href="/vip/vip_address/oldVisitList" style="color: #0000ff;">历史月份数据</a></li>
    </ul>
</div>
<div style="padding-left: 20px;">
    <div style="margin-bottom: 20px; margin-left: 100px;"><h2>学员APP活跃量统计表</h2></div>
    <ul>
         <li >
            <div>
                <table border="1px" style="width: 30%;line-height: 25px;" >
                    <tr style="background-color: #b7b7b7;">
                        <td>序号</td>
                        <td>日期</td>
                        <td>操作</td>
                    </tr>
                    <?php foreach ($oldVisitList as $key=>$val):?>
                    <tr>
                        <td><?=$val['id']?></td>
                        <td><?php echo mb_substr($val['title'],0,8 );?></td>
                        <td><a href="/vip/vip_address/previewVisit?id=<?=$val['id']?>">预览</a>&nbsp;&nbsp;&nbsp;<a href="/vip/vip_address/exportVisit?id=<?=$val['id']?>" style="color: #277ab7">下载</a></td>
                    </tr>
                    <?php endforeach?>
                </table>
                <div id="pageStr"><?php echo $showPages;?></div>
            </div>
         </li>
    </ul>
</div>

</body>
</html>
