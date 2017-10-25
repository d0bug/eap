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
		<h2>问题列表&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

    <a href="<?php echo U('/Modular/Knowledge/index','','')?>" class="btn">知识点列表</a>
    <a href="<?php echo U('/Modular/Knowledge/insert','','')?>" class="btn">添加知识点</a>
    <a href="<?php echo U('/Modular/ExerciseCategory/index','','')?>" class="btn">分类列表</a>
    <a href="<?php echo U('/Modular/ExerciseCategory/insert','','')?>" class="btn">添加分类</a>
    <a href="<?php echo U('/Modular/ExerciseQuestion/index','','')?>" class="btn">问题列表</a>
    <a href="<?php echo U('/Modular/ExerciseQuestion/insert','','')?>" class="btn">添加问题</a>
    <a href="javascript:void(0)" onclick="submitForm()" class="btn">删除问题</a>



    </h2>
    <form id="form" method="post" action =" <?php echo U('Modular/ExerciseQuestion/delete','','');?>">
     <table id="apply_table" class="tableList" width="90%" cellspacing="0" cellpadding="0" border="0">
       <thead>
       <tr>
       <th></th>
         <th>ID</th>
         <th>名称</th>
         <th>模</th>
         <th>难度</th>
         <th>分类</th>
         <th>知识点</th>
         <th>状态</th>
         <th>排序</th>
         <th>操作</th>
       </tr>
       </thead>
       <?php foreach($List as $value):?>
         <tr>
         <td><input type="checkbox" name="selects[]" value="<?php echo $value['id'];?>"></td>
         <td><?php echo $value['id'];?></td>
         <td><?php echo $value['title'];?></td>
         <td><?php echo $value['mod_id'];?></td>
         <td><?php echo $value['difficulty'];?></td>
         <td><?php echo $value['category_id'],$value['category_name'];?></td>
         <td><?php echo $value['knowledge_id'],$value['knowledge_name'];?></td>
         <td>
             <?php if($value['status'] == 0 ):?>
              <font color="red">已删除</font>
            <?php else:?>
              <font>正常</font>
            <?php endif?>

         </td>
         <td><?php echo $value['sort_order'];?></td>
         <td><a href="<?php echo U('/Modular/ExerciseQuestion/update',array('id'=>$value['id']),'');?>">编辑</a></td>
        <?php endforeach?>
        </tr>


     </table>
     </form>
		<p><?php echo $page;?><p>


		<p><input type="button" class="btn" onclick="toggle('#apply_table','#flex_btn_apply')" id="flex_btn_apply" value="收起"></p><br>

	</div>

</div>

<script type="text/javascript">
   function submitForm(){
      var str= 0 ;
      $("input[name='selects[]']:checkbox").each(function(){
          if($(this).attr("checked")){
              str += parseInt($(this).val());
          }
      })
      if(str == 0) {
        alert('你没有选中任何题目');
        return false;
      }
      $('#form').submit();

   }







</script>
</body>
</html>
