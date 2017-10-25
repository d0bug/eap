<!doctype html>
<html>
    <head>
        <link rel="stylesheet" href="http://cdn.bootcss.com/twitter-bootstrap/3.0.3/css/bootstrap.min.css">


        <link rel="stylesheet" href="http://cdn.bootcss.com/twitter-bootstrap/3.0.3/css/bootstrap-theme.min.css">

    </head>
    <body>
    <div class="container">
    <h1>班型列表</h1>
    <p>
  <button type="button" class="btn btn-primary" data-toggle="modal" href="/Task/Question/stepOne" data-target="#modal">添加题目</button>
  <button type="button" class="btn btn-default">添加班次</button>
    </p>
    	<table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>序号</th>
            <th>时段</th>
            <th>班级编码</th>
            <th>班级名称</th>
            <th>班型编码</th>
            <th>班型名称</th>
            <th>题型</th>
            <th>修改</th>

        </tr>
        </thead>
        <tbody>
        <?php foreach($aQuestionList as $value){?>
        <tr>
            <td><?php echo $value['id'];?></td>
            <td><?php echo $value['nyear'],'年 ',seasonName($value['nseason']);?></td>
            <td><?php echo trim($value['sclasscode']);?></td>
            <td><?php echo trim($value['sclassname']);?></td>
            <td><?php echo $value['sclasstypecode'];?></td>
            <td><?php echo $value['sclasstypename'];?></td>
            <td><?php echo questionTyoe($value['ntype']);?></td>
            <td><div class="btn-group">
                     <button type="button" class="btn btn-default" data-toggle="modal" href="/Task/Question/ajax_stepThree/nType/<?php echo $value['ntype'];?>/nQuestionid/<?php echo $value['nquestionid'];?>" data-target="#modal">编辑题目</button>

                      <button type="button" class="btn btn-default" onclick="del(<?php echo $value['id'];?>)">删除</button>
                </div>
            </td>

        </tr>
        <?php }?>
        </tbody>
        </table>
        <p><?php echo $page;?></p>
    </div>
    <!-- Modal -->
 <div id='modal' class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-body"></div>
 </div>
 <div id='modal1' class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="myModalLabe2" aria-hidden="true">
              <div class="modal-body">

              </div>
 </div>
    </body>
<script src="http://cdn.bootcss.com/jquery/1.10.2/jquery.min.js"></script>


<script src="http://cdn.bootcss.com/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
<script type="text/javascript">
    $('.modal').on('hidden.bs.modal',function(e){

$(this).removeData('bs.modal');

});
    function del(id) {
        if(confirm('are you sure?')) {
            $.ajax({
             type: "GET",
             url: "/Task/Question/ajax_delete/id/"+id,
             dataType: "json",
             async:false,
             success: function(data){
                        if(data >0 ) {
                            location.reload();
                            return false;
                        }
                        alert('delete error!');
                        return false;
                    }


            });

        }
        return false;

    }
</script>
</html>
