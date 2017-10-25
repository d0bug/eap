<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/modular.js"></script>
<link href="/static/css/modular.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
  <div id="main">
    <h2>在线预约&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

    <a href="<?php echo U('/Reserve/Apply/index',array('id'=>$id),'')?>" class="btn">学员列表</a>
    <a href="<?php echo U('/Reserve/Apply/verify',array('id'=>$id),'')?>" class="btn">名额申请</a>
    <a href="<?php echo U('/Reserve/Active/index',array('id'=>$id),'')?>" class="btn">名额管理</a>


    </h2>
    <?php if(!empty($stuInfoArr)):?>
    <p>姓名: <?php echo $stuInfoArr['name'];?>电话:<?php echo $stuInfoArr['phone'];?> 学号:<?php echo $stuInfoArr['stucode'];?></p>
  <?php else:?>
    <p>学号:<input type="text" id="condition" /><button onclick="searchStuInfo()">查询</button><span id="stuInfo"></span></p>

  <?php endif?>
    <form method="post" action="<?php echo $action;?>">
   <p> <input type="hidden" id="stucode" name ="scode" value="<?php echo $stucode;?>"></p>

     <?php for($i = 1; $i <= $aFormStruct['class_num']; $i++):?>
          <P>  <?php echo $aFormStruct['class_name'][$i];?>
          <select class="selects" name = "selects[<?php echo $i;?>]">
          <option   value="0">请选择</option>
          <?php for($j = 1; $j <= $aFormStruct['week_num']; $j++):?>

            <?php if(in_array($j, $data[$i])):?>
              <option  selected="selected" value="<?php echo $j;?>"><?php echo $aFormStruct['week_name'][$j],createFormat($aFormInfo[$i][$j],$j,$i,2)?></option>

            <?php else:?>




          <option value="<?php echo $j;?>"><?php echo $aFormStruct['week_name'][$j],createFormat($aFormInfo[$i][$j],$j,$i,2)?></option>
        <?php endif?>
        <?php endfor?>
        </select>
        </P>
           <?php endfor ?>

    <input type="submit" value="提交">


   </form>
    <p><p>


    <p><input type="button" class="btn" onclick="toggle('#apply_table','#flex_btn_apply')" id="flex_btn_apply" value="收起"></p><br>

  </div>

</div>
<script type="text/javascript">

  function searchStuInfo() {
    $.ajax({
    url: '<?php echo U('/Reserve/Apply/searchStuInfo',array('id'=>$id),'');?>',

    type: 'POST',

    data:{condition:$('#condition').val()},

    dataType: 'json',

    timeout: 1000,

    error: function(){alert('Error loading PHP document');},

    success: function(result) {
      if (result['error'] == 1) {
         alert(result['msg']);
      }
      if (result['error'] == 0) {
         var stuInfo = '姓名：<b>'+result['name']+'</b>  手机：<b>'+result['phone']+'</b>  学号：<b>'+result['stuid']+'</b>  学员编码：<b>'+result['stucode']+'</b>';
         $('#stuInfo').html(stuInfo);
         $('#stucode').val(result['stucode']);
      }

      }

  });
  }
</script>
</body>
</html>
