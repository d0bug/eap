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
		<h2>在线预约报名&nbsp;&nbsp;（服务于<?php echo $moduleCount;?>项目）&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" class="btn">添加新项目</a></h2>
		<table class="tableList" border="0" cellpadding="0" cellspacing="0"  width="90%" id="apply_table">
			 <tr>
           <th> 姓名  </th>
           <th> 学 号  </th>
           <th> T E L  </th>
           <?php for($i = 1; $i <= $formFormatArr['class_num']; $i++):?>
           <th><?php echo $formFormatArr['class_name'][$i];?></th>

           <?php endfor ?>
           <th>编辑</th>
       </tr>
			<?php foreach($data as $stucode => $value):?>
          <tr>
          <td><?php echo $stuInfoArr[$stucode]['name'];?></td>
          <td><?php echo $stucode;?></td>
          <td><?php echo $stuInfoArr[$stucode]['phone'];?> </td>
          <?php for($i = 1; $i <= $formFormatArr['class_num']; $i++):?>
          <td><?php echo createFormat($formInfoArr[$i][$value[$i]],$value[$i],$i,1);?></td>

           <?php endfor ?>
           <td><a href="<?php echo U('/Reserve/Apply/edit','scode='.trim($stucode).'&id='.$id);?>">编辑</a></td>
           </tr>
       <?php endforeach?>
		</table>
		<p><input type="button" class="btn" onclick="toggle('#apply_table','#flex_btn_apply')" id="flex_btn_apply" value="收起"></p><br>

	</div>

</div>
</body>
</html>
