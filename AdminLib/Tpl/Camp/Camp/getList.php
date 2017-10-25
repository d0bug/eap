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
		<a href="<?php echo U('/Camp/Index/index','','')?>" class="btn">班级列表</a>
        <a href="<?php echo U('/Camp/Camp/index','','')?>" class="btn">班级管理</a>





    </h2>
    <a href="<?php echo U('/Camp/Camp/add','','')?>" class="btn">添加</a>
    <table id="apply_table" class="tableList" width="90%" cellspacing="0" cellpadding="0" border="0">
    <thead>
    	<th>班级编码</th>
    	<th>班级名称</th>
        <th>年</th>
        <th>学期</th>
        <th>任课老师</th>
        <th>删除</th>

    </thead>
    <tbody>
        <?php foreach($list as $value):?>
            <tr>
                <td><?php echo $value['scode'];?></td>
                <td><?php echo $value['sname'];?></td>
                <td><?php echo $value['nclassyear'];?></td>
                <td><?php echo $value['nsemester'];?></td>
                <td><?php echo $value['sprintteachers'];?></td>
                <td><a href="javascript:void(0)" onclick="del(<?php echo $value['id'];?>)"> 删除</a></td>

            </tr>
        <?php endforeach?>
    </tbody>
    </table>
</div>

</div>

<script type="text/javascript">

function del(id) {
    if(id < 1) {
        alert('错误');
        return false;
    }
     if(!confirm("确定要删除该班级吗？")){
      return false;
     }

         $.ajax({
         url: "/Camp/Camp/delete",
         type: "get",
         data:{id:id},

         dataType: "json",
         error: function(){
             alert('Error loading XML document');
         },
         success: function(data,status){
            if(data['error'] == 1) {

              alert(data['msg']);



              return false;
            }
            if(data['error'] == 0) {

             alert(data['msg']);
              location.reload();
                return false;
            }


         }
     });
}




</script>
</body>
</html>

