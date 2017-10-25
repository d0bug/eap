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
<div id="box">
    <ul id="tab"  >
        <li  ><a href="/vip/vip_address/visitCountList" style="color: #0000ff;">学员活跃量统计</a></li>
        <li  ><a href="/vip/vip_address/oldVisitList">历史月份数据</a></li>
    </ul>
</div>
<div id="tab_con_1" style="padding-left: 20px;">
    <ul id="tab_con">
         <li >
             <form id="search_form" name="search_form" method="GET" action="<?php echo U('Vip/VipAddress/visitCountList');?>">
            <div style="margin-bottom: 10px;">
                查询时间：<input type="text"  class="Wdate" id="beginDate" name="beginDate" value="<?php echo $formData['beginDate'];?>" onClick="WdatePicker()" onfocus="WdatePicker({minDate:'%y-%M-01',maxDate:'%y-%M-%ld'})" > 至 <input type="text"  class="Wdate" id="endDate" name="endDate" value="<?php echo $formData['endDate'];?>"  onClick="WdatePicker()" onfocus="WdatePicker({minDate:'%y-%M-01',maxDate:'%y-%M-%ld'})"  >
                <input type="submit" name="submit" value="查询">&nbsp;&nbsp;

                <a  href="/vip/vip_address/visitCountList?type=1"  style="color: #d58512; font-size: 14px; border: 1px solid #d0e9c6; padding: 5px" >本周</a>&nbsp;&nbsp;
                <a  href="/vip/vip_address/visitCountList?type=2" style="color: #3d8cce; font-size: 14px; border: 1px solid #d0e9c6; padding: 5px">本月</a>
                    <input type="submit" name="submit2" value="导出">
            </div>
             </form>
            <div>
                 <table border="1px" style="width: 30%;line-height: 25px;" >
                    <tr style="background-color: #b7b7b7; ">
                    <td>学号</td>
                    <td>学员姓名</td>
                    <td>学管师姓名</td>
                    <td>所属校区</td>
                    <td>是否是新签</td>
                    <td>活跃量(一天只记录一次)</td>
                    </tr>
                    <?php foreach($visitCountList as $key=>$val):?>
                    <tr>
                    <td><?=$val['uid']?></td>
                    <td><?=$val['sstudentname']?></td>
                    <td><?=$val['manage_teacher']?></td>
                    <td><?=$val['attribute_school']?></td>
                    <td><?=$val['sign_status']?></td>
                    <td><?=$val['number']?></td>
                    </tr>
                    <?php endforeach?>

                </table>
                <div id="pageStr"><?php echo $showPage;?></div>
            </div>
         </li>
        </ul>
</div>


</body>
</html>
