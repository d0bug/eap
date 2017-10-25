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
    <?php foreach($aStuList as $stucode =>$arr):?>
      <dl>
      <dt><b>姓名：<?php echo $aStuInfo[$stucode]['name']?>学号：<?php echo $aStuInfo[$stucode]['stucode']?>电话：<?php echo $aStuInfo[$stucode]['phone']?></b></dt>
      <?php foreach($arr as $class_id => $week_id):?>
        <dd>
          <?php echo $aFormStruct['class_name'][$class_id];?>  <?php echo $aFormStruct['week_name'][$week_id];?><?php echo createFormat($aFormInfo[$class_id][$week_id],$week_id,$class_id,3);?>
           <?php if($aStatus[$stucode][$class_id] == 2) {echo '<font color="blue">已经批准</font>';}
                 if($aStatus[$stucode][$class_id] == 3) {echo '<font color="red">已经拒绝</font>';}
                 if($aStatus[$stucode][$class_id] == 0)
                  {
                     echo '<span id="id_'.$id.'_'.$stucode.'_'.$class_id.'"><font color="red">待批准</font>';
                     echo '<button onclick=\'process('.$id.',"'.$stucode.'",'.$class_id.',2,'.$aStuList[$stucode][$class_id].')\' id="allow_'.$id.'_'.$stucode.'_'.$class_id.'">同意</button>';
                     echo '<button onclick=\'process('.$id.',"'.$stucode.'",'.$class_id.',3,'.$aStuList[$stucode][$class_id].')\' id="deny_'.$id.'_'.$stucode.'_'.$class_id.'">拒绝</button></span>';
                  }?>

        </dd>
      <?php endforeach ?>
     </dl>
    <?php endforeach?>
		<p><p>


		<p><input type="button" class="btn" onclick="toggle('#apply_table','#flex_btn_apply')" id="flex_btn_apply" value="收起"></p><br>

	</div>

</div>

<script type="text/javascript">
  function process(list_id,stucode,class_id,status,week_id) {
    if(status == 2) {
      var html = '<font color="blue">已经批准</font>';
    } else {
      var html = '<font color="red">已经拒绝</font>';
    }

    $.post("/Reserve/Apply/confirm", {'id': list_id, 'stucode': stucode,'class_id':class_id,'status':status,'week_id':week_id},
          function(data){

            if(data) {
             // alert('#id_'+list_id+'_'+stucode+'_'+class_id);
               $('#id_'+list_id+'_'+stucode+'_'+class_id).html(html);
            }
   });
  }








</script>
</body>
</html>
