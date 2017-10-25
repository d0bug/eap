<!doctype html>
<html>
    <head>
        <link rel="stylesheet" href="http://cdn.bootcss.com/twitter-bootstrap/3.0.3/css/bootstrap.min.css">


        <link rel="stylesheet" href="http://cdn.bootcss.com/twitter-bootstrap/3.0.3/css/bootstrap-theme.min.css">

    </head>
    <style type="text/css">
    #editor { width: 600px; overflow:scroll; max-height:300px}
    </style>
    <body>
    <div class="container">
    <h1>试卷列表</h1>
    <p>
  <button type="button" class="btn btn-primary" data-toggle="modal" href="/Task/Question/stepOne" data-target="#modal">添加试卷</button>

  <a type="button" class="btn btn-primary" href="/Task/Analysis/index" data-target="#modal">分析</a>
   <a type="button" class="btn btn-primary" href="/Task/Test/index" data-target="#modal">测试</a>

    </p>
    	<form class="form-inline" role="form">
		  <div class="form-group">
		    	<select class="form-control select-inline" onchange="local(this.value)">
		    	  <?php foreach($years as $key => $value):?>
		    	  	<?php if($key == $data['nYear']):?>
		    	  		<option value="<?php echo url($data,'nYear',$key);?>" selected="selected"><?php echo $value;?></option>
		    	  	<?php else:?>
		    	  	<option value="<?php echo url($data,'nYear',$key);?>"><?php echo $value;?></option>
		    	    <?php endif?>
		    	  <?php endforeach?>

				</select>
		  </div>
		  <div class="form-group">
		   		 <select class="form-control select-inline" onchange="local(this.value)">
				  <?php foreach($seasons as $key => $value):?>
		    	  	<?php if($key == $data['nSeason']):?>
		    	  		<option value="<?php echo url($data,'nSeason',$key);?>" selected="selected"><?php echo $value;?></option>
		    	  	<?php else:?>
		    	  	<option value="<?php echo url($data,'nSeason',$key);?>"><?php echo $value;?></option>
		    	    <?php endif?>
		    	  <?php endforeach?>


				</select>
		  </div>

		   <div class="form-group">
		   		 <select class="form-control select-inline" onchange="local(this.value)">
				  <?php foreach($classTypeCodes as $key => $value):?>
		    	  	<?php if($key == $data['sClassTypeCode']):?>
		    	  		<option value="<?php echo url($data,'sClassTypeCode',$key);?>" selected="selected"><?php echo $value;?></option>
		    	  	<?php else:?>
		    	  	<option value="<?php echo url($data,'sClassTypeCode',$key);?>"><?php echo $value;?></option>
		    	    <?php endif?>
		    	  <?php endforeach?>



				</select>
		  </div>
		  <div class="form-group">
		   		 <select class="form-control select-inline" onchange="local(this.value)">
				  <?php foreach($types as $key => $value):?>
		    	  	<?php if($key == $data['nType']):?>
		    	  		<option value="<?php echo url($data,'nType',$key);?>" selected="selected"><?php echo $value;?></option>
		    	  	<?php else:?>
		    	  	<option value="<?php echo url($data,'nType',$key);?>"><?php echo $value;?></option>
		    	    <?php endif?>
		    	  <?php endforeach?>

				</select>
		  </div>

		</form>
    	<table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>序号</th>
            <th>时段</th>

            <th>班型编码</th>
            <th>班型名称</th>

            <th>课节</th>
            <th>题型</th>
            <th>修改</th>

        </tr>

        </thead>
        <tbody>
        <?php foreach($aQuestionList as $value){?>
        <?php if(empty($value['nsign'])):?>
            <tr style="color:red">
            <?php else:?>
               <tr>
            <?php endif?>

            <td>

                <?php echo $value['id'];?>



            </td>
            <td><?php echo $value['nyear'],'年 ',seasonName($value['nseason']);?></td>


            <td><?php echo $value['sclasstypecode'];?></td>
            <td><?php echo $value['sclasstypename'];?></td>
            <td><?php echo trim($value['stopic']);?></td>
            <td><?php echo questionTyoe($value['ntype']);?></td>
            <td><div class="btn-group">

                     <a  class="btn btn-primary" href="/Task/Index/multyAdd/nQuestionid/<?php echo $value['nquestionid'];?>" >编辑</a>


                </div>
                <div class="btn-group">

                     <a  class="btn btn-primary" href="javascript:void(0)" onclick="del(<?php echo $value['id'];?>)" >删除</a>


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
