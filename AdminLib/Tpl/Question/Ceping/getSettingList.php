<!doctype html>
<html>
    <head>
        <link rel="stylesheet" href="http://cdn.bootcss.com/twitter-bootstrap/3.0.3/css/bootstrap.min.css">


        <link rel="stylesheet" href="http://cdn.bootcss.com/twitter-bootstrap/3.0.3/css/bootstrap-theme.min.css">

    </head>

    <body>
    <div class="container">
    <h1>选项设置</h1>
    <p>

    <a type="button" class="btn btn-primary" href="<?php echo U('/Question/Ceping/settingInsert',array('type'=>$type),'');?>" >添加</a>



    </p>

    	<table class="table table-bordered table-hover">
        <thead>
        <tr>
            <!-- <th>#</th> -->
            <th>序号</th>

            <th>名称</th>
            <th>类型</th>

            <th>状态</th>
            <th>排序</th>
            <th>编辑</th>


        </tr>

        </thead>
        <tbody>
        <?php foreach($list as $value){?>

               <tr>


           <!--  <td>

                <?php echo $value['id'];?>



            </td> -->
            <td><?php echo $value['key'];?></td>


            <td><?php echo $value['value'];?></td>
            <td><?php echo $value['type'];?></td>
            <td><?php echo $value['status'];?></td>
            <td><?php echo $value['sort'];?></td>
            <td><div class="btn-group">

                     <a  class="btn btn-primary" href="<?php echo U('/Question/Ceping/settingIndex',array('type'=>$value['key']),'');?>" >进入</a>


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
 <script type="text/javascript">
	function local(url) {
		location.href = url;
	}
</script>
<script src="http://cdn.bootcss.com/jquery/1.10.2/jquery.min.js"></script>


<script src="http://cdn.bootcss.com/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
<script src="http://tiyee.qiniudn.com/bootstrap-wysiwyg.js"></script>

<script type="text/javascript">
    $('.modal').on('hidden.bs.modal',function(e){

$(this).removeData('bs.modal');


});

    function del(id) {
        if(confirm('删除试卷会连该试卷下的题目一起删除?')) {
            //alert(id);
          //  return false;
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
