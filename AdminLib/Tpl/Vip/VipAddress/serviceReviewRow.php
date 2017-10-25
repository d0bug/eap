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
<div style="padding-left: 20px;" id="box">
    <ul>
         <li >
            <div>
                <table  style="width: 70%;line-height: 25px; " >
                    <tr >
                        <td>学号:  <?php echo $result['ucode'];?></td>
                    </tr>
                    <tr>
                        <td>学员姓名:  <?php echo $result['uname']?></td>
                    </tr>
                    <tr>
                        <td>电话:  <?php echo $result['phone'];?></td>
                    </tr>
                    <tr>
                        <td>填写时间:  <?php echo $result['create_time'];?></td>
                    </tr>

                    <tr>
                        <table border="1px" style="line-height: 25px;text-align: center">
                            <tr>
                                <td>勾选内容</td>
                                <td>服务内容</td>
                            </tr>
                            <?php foreach($result['list_arr'] as $key=>$val):?>
                                <tr>

                                    <td><?php if($val['msg'] == 1){echo "✔️";}elseif($val['msg']== 0){echo "X";}?></td>
                                    <td><?php echo $val['title'];?></td>

                                </tr>
                            <?php endforeach;?>
                        </table>
                    </tr>

                </table>
            </div>
         </li>
    </ul>
</div>

</body>
</html>
