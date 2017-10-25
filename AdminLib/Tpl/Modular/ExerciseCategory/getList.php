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
		<h2>分类管理&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <a href="<?php echo U('/Modular/ExerciseQuestion/index','','')?>" class="btn">问题列表</a>
    <a href="<?php echo U('/Modular/ExerciseCategory/index','','')?>" class="btn">知识点列表</a>
    <a href="<?php echo U('/Modular/ExerciseCategory/insert','','')?>" class="btn">添加知识点</a>



    </h2>
     <table id="apply_table" class="tableList" width="90%" cellspacing="0" cellpadding="0" border="0">
       <thead>
       <tr>
         <th>ID</th>
         <th>名称</th>
         <th>排序</th>
         <th>状态</th>
         <th>是否显示</th>
         <th>操作</th>
       </tr>
       </thead>
       <?php foreach($List as $value):?>
         <tr>
         <td><?php echo $value['id'];?></td>
         <td><?php echo $value['title'];?></td>
         <td><?php echo $value['sort_order'];?></td>
         <td><?php echo $value['status'];?></td>
         <td><?php echo $value['is_show'];?></td>
         <td><a href="<?php echo U('/Modular/ExerciseCategory/update',array('id'=>$value['id']),'');?>">编辑</a></td>
        <?php endforeach?>
        </tr>


     </table>
		<p><p>


		<p><input type="button" class="btn" onclick="toggle('#apply_table','#flex_btn_apply')" id="flex_btn_apply" value="收起"></p><br>

	</div>

</div>

<script type="text/javascript">








</script>
</body>
</html>
