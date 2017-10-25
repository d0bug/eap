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
		<h2>学员列表&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <a href="<?php echo U('/Reserve/Apply/verify',array('id'=>$id),'')?>" class="btn">名额申请</a>
    <a href="<?php echo U('/Reserve/Active/index',array('id'=>$id),'')?>" class="btn">名额管理</a>
     <a href="<?php echo U('/Reserve/Apply/add',array('id'=>$id),'')?>" class="btn">添加学生</a>
     <a href="<?php echo U('/Reserve/Apply/export',array('id'=>$id),'')?>" class="btn">导出</a>
    </h2>
		<table class="tableList" border="0" cellpadding="0" cellspacing="0"  width="90%" id="apply_table">
			 <tr align="center">
           <th> 姓名  </th>
           <th> 学号  </th>
           <th> 电话  </th>
           <?php for($i = 1; $i <= $formFormatArr['class_num']; $i++):?>
           <th><?php echo $formFormatArr['class_name'][$i];?></th>

           <?php endfor ?>
           <th>编辑</th>
       </tr>
			<?php foreach($stuInfoArr as $stucode => $value):?>
          <tr>
          <td align="center"><?php echo $stuInfoArr[$stucode]['sstudentname'];?></td>
          <td align="center"><?php echo $stucode;?></td>
          <td align="center"><?php echo $stuInfoArr[$stucode]['sparents1phone'];?> </td>
          <?php for($i = 1; $i <= $formFormatArr['class_num']; $i++):?>
            <?php $week_id = $data[$stucode][$i];?>
        <?php if(isset($data[$stucode][$i])):?>
          <td align="center"><?php echo createFormat($formInfoArr[$i][$week_id],$week_id,$i,1);?></td>
        <?php else:?>
          <td align="center"><div style="width:85px;height:70px;">无</div></td>
        <?php endif?>

           <?php endfor ?>
           <td align="center"><a href="<?php echo U('/Reserve/Apply/edit','scode='.trim($stucode).'&id='.$id);?>">编辑</a>  <a href="<?php echo U('/Reserve/Apply/delete','scode='.trim($stucode).'&id='.$id);?>">删除</a></td>

           </tr>
       <?php endforeach?>
		</table>

		<p><input type="button" class="btn" onclick="toggle('#apply_table','#flex_btn_apply')" id="flex_btn_apply" value="收起"></p><br>

	</div>

</div>
</body>
</html>
