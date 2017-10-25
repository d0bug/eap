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
                <table  style="width: 100%;line-height: 25px; " >
                    <tr >
                        <td>视频名称:  <?php echo $result['title'];?></td>
                        <td>上传人:  <?php echo $result['create_name'];?></td>
                        <td>上传时间:  <?php echo $result['create_time'];?></td>
                    </tr>
                    <tr>
                        <video width="500" height="300" controls > <source src="<?php echo $result['one_video_url']?>" type='video/mp4 codecs="avc1.42E01E, mp4a.40.2"'> </object></video>
                    </tr>

                </table>
            </div>
         </li>
    </ul>
</div>

</body>
</html>
