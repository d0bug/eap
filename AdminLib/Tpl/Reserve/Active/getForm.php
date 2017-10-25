<!doctype html>
<html>
<head>
    <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
    <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
    <script type="text/javascript" src="/static/kindeditor/kindeditor-min.js"></script>

     <script type="text/javascript" src="/static/js/DatePicker/WdatePicker.js"></script>
    <script type="text/javascript">

    function saveExam() {
        for(var editor in keEditors){
            KindEditor.sync('#' + editor);
        }
        var formData = jQuery('input,select,textarea').serialize();
        jQuery.post('<?php echo $url?>', formData, function(data){
            if(data.errorMsg) {
                alert(data.errorMsg);
            } else {
                alert('竞赛信息添加成功');
                parent.closeWin('<?php echo $dlgId?>');
                parent.loadExams();
            }
        }, 'json');
    }
    jQuery(function(){

    })
    </script>
    <link rel="stylesheet" type="text/css" href="/static/css/mgs.css">
    <style type="text/css">
    .item {line-height:25px;list-style-type:none;margin-bottom:2px;clear:both}
    .item div{float:left;width:120px;font-weight:bold}
    .item .input,.item textarea{height:19px;width:300px;float:left;margin-bottom:2px;border:1px solid #95B8E7;margin-top:2px}
    .item lable{clear:both}
    .item label .radio{widht:20px;clear:left}
    .item p{width:550px;float:left;margin:0px;padding:0px}
    </style>
</head>
<body style="overflow:hidden;margin:0px -1px -1px 0px"  class="easyui-layout" fit="true" border="false">
<form method="post" action="<?php echo $action;?>">
   <table class="tableList">
   <tbody>
       <tr>
           <th> 课程</th>
           <?php foreach($active_info['week_name'] as $week => $week_value):?>
           <th><?php echo $week_value;?></th>
           <?php endforeach ?>


       </tr>
       <?php for($j = 1;$j<=$active_info['class_num'];$j++){?>
       <tr>
       <td><?php echo $active_info['class_name'][$j];?></td>
       <?php for($i = 1;$i<=$active_info['week_num'];$i++):?>
            <?php if(isset($data[$i][$j])):?>
            <td>
          <?php else:?>
           <td bgcolor="#F5F5DC">
          <?php endif?>
            <?php echo createFormat($data[$i][$j],$i,$j);?></td>

       <?php endfor?>

       </tr>
       <?php } ?>
    </tbody>
   </table>
    <input type="submit" value="提交">


   </form>




</body>
</html>
