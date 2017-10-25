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
		<h2>试卷列表&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h2>
    <p>

      <select id="classyear" onchange="changeSelection('classyear',this.value)">
        <?php foreach($years as $key => $value):?>
        <option <?php if($key == $classyear) echo 'selected="selected"';?> value="<?php echo $key;?>" ><?php echo $value;?></option>
        <?php endforeach?>

      </select>

      <select id="semester_id" onchange="changeSelection('semester_id',this.value)">
        <?php foreach($sems as $key => $value):?>
        <option <?php if($key == $semester_id) echo 'selected="selected"';?> value="<?php echo $key;?>"><?php echo $value;?></option>
        <?php endforeach?>

      </select>
      <select id="deps" onchange="changeSelection('deps',this.value)">
        <?php foreach($deps as $key => $value):?>
        <option <?php if($key == $deptcode) echo 'selected="selected"';?> value="<?php echo $key;?>"><?php echo $value;?></option>
        <?php endforeach?>

      </select>
      <select id="classtype_code" onchange="changeSelection('classtype_code',this.value)">
        <?php foreach($ctypes as $key => $value):?>
        <option <?php if($key == $classtype_code) echo 'selected="selected"';?> value="<?php echo $key;?>"><?php echo $value;?></option>
        <?php endforeach?>

      </select>

    <a href="javascript:void(0)" onclick="searchs()" class="btn">筛选</a>
  <a href="<?php echo U('/homework/mid_science/create','','')?>" class="btn">添加试卷</a>



    </p>
    <table id="apply_table" class="tableList" width="90%" cellspacing="0" cellpadding="0" border="0">
    <thead>
    	<th>#</th>
    	 <th>年</th>
    	 <th>学期</th>
       <th>学科</th>
    	<th>班型名称</th>



        <th>课节</th>
        <th>删除</th>

    </thead>
    <tbody>
        <?php foreach($list as $value):?>
            <tr>
                <td><?php echo $value['id'];?></td>
                <td><?php echo $value['classyear'];?></td>
                <td><?php echo seasonName($value['semester_id']);?></td>
                <td><?php echo $value['depname'];?></td>
                <td><?php echo $value['sname'];?></td>



                <td><?php echo $value['lesson_no'];?></td>
                <!-- <td><a href="javascript:void(0)" onclick="del(<?php echo $value['id'];?>)"> 删除</a></td> -->
                <td><a href="javascript:void(0)" onclick="del(<?php echo $value['id'];?>)"> 删除</a>  |  <a href="<?php echo U('/homework/mid_science/questionList',array('id'=>$value['id']),'');?>">查看</a></td>

            </tr>
        <?php endforeach?>
    </tbody>
    </table>
    <p><?php echo $page;?></p>
</div>

</div>

<script type="text/javascript">

function del(id) {
    if(id < 1) {
        alert('错误');
        return false;
    }
   // alert('试卷只能修改，不能删除'); return false;
     if(!confirm("确定要删除该试卷吗？")){
      return false;
     }


         $.ajax({
         url: "/homework/mid_science/delPaper",
         type: "get",
         data:{id:id},

         dataType: "json",
         error: function(){
             alert('Error loading XML document');
         },
         success: function(data,status){
           //return false;
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

function searchs() {

    var urls = '/homework/mid_science/lessonList';
    var classyear = $('#classyear').val();
    var semester_id = $('#semester_id').val();
    var classtype_code = $('#classtype_code').val();
    var deps = $('#deps').val();

    if(classyear != 0) {
        urls += '/classyear/'+classyear;
    }
    if(semester_id != 0) {
        urls += '/semester_id/'+semester_id;
    }
    if(deps != 0) {
        urls += '/deps/'+deps;
    }
    if(classtype_code != 0) {
        urls += '/classtype_code/'+classtype_code;
    }


    //alert(urls);
    location.href=urls;
    return false;
}
function changeSelection(keys,values) {
    return false;
     if(values != 0 && values != '0') {
    eval(keys+'=\''+values+'\'');
    }
    return false;
}



</script>
</body>
</html>


