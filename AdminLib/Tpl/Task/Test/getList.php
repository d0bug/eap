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
    <div class="container" style="width:100%">
    <h1>试卷列表</h1>
    <p>
  <button type="button" class="btn btn-primary" data-toggle="modal" href="/Task/Question/stepOne" data-target="#modal">添加试卷</button>

  <a type="button" class="btn btn-primary" href="/Task/Analysis/index" data-target="#modal">分析</a>
   <a type="button" class="btn btn-primary" href="<?php echo urla($data,'process',1);?>" data-target="#modal">导出</a>

    </p>
    	<form class="form-inline" role="form" action="/Task/Test/index" method="get">
		  <div class="form-group">
		    	<input type="text" name="sAliasCode" id="sAliasCode" class="form-control" value="">
		  </div>



		  <div class="form-group">
		   		 <select class="form-control select-inline" name="nType">
				  <?php foreach($types as $key => $value):?>
		    	  	<?php if($key == $data['nType']):?>
		    	  		<option value="<?php echo $key;?>" selected="selected"><?php echo $value;?></option>
		    	  	<?php else:?>
		    	  	<option value="<?php echo $key;?>"><?php echo $value;?></option>
		    	    <?php endif?>
		    	  <?php endforeach?>

				</select>
		  </div>

          <button type="submit" class="btn btn-primary" >提交</button>

		</form>
    	<table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>序号</th>

            <th>时段</th>
            <th>时间</th>

            <th>班型编码</th>


            <th>课节</th>




            <th>题型</th>
            <th>删除</th>


        </tr>

        </thead>
        <tbody>
        <?php $i=1;foreach($list as $value){?>
        <tr>
            <td><?php echo $i;$i++?></td>

            <td><?php echo $value['nyear'],'年 ',seasonName($value['nseason']);?></td>

             <td><?php echo $value['dcreatetime'];?></td>


             <td><?php echo $value['sclasstypecode'];?></td>

            <td><?php echo trim($value['stopic']);?></td>


            <td><?php echo questionTyoe($value['ntype']);?></td>
            <td><a  class="btn btn-primary" href="javascript:void(0)" onclick='del(<?php echo $value['nquestionid'],',"',$value['saliascode'],'",',$value['ntype'];?>)' >删除</a></td>


        </tr>
        <?php }?>
        </tbody>
        </table>

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

    function del(nQuestionid,sAliasCode,nType) {
      //alert(nQuestionid+'-'+sAliasCode+'-'+nType);return false;
        if(confirm('删除试卷会连该试卷下的题目一起删除?')) {
            //alert(id);
          //  return false;
            $.ajax({
             type: "GET",
             url: "/Task/Test/ajax_delete/nQuestionid/"+nQuestionid+'/sAliasCode/'+sAliasCode+'/nType/'+nType,
             dataType: "json",
             async:false,
             success: function(data){
                        if(data >0 ) {
                            location.href="/Task/Test/index";
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
