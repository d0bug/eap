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
    	<form class="form-inline" role="form">
		  <div class="form-group">
		    	<select class="form-control select-inline" onchange="local(this.value)">
		    	  <?php foreach($years as $key => $value):?>
		    	  	<?php if($key == $data['nYear']):?>
		    	  		<option value="<?php echo urla($data,'nYear',$key);?>" selected="selected"><?php echo $value;?></option>
		    	  	<?php else:?>
		    	  	<option value="<?php echo urla($data,'nYear',$key);?>"><?php echo $value;?></option>
		    	    <?php endif?>
		    	  <?php endforeach?>

				</select>
		  </div>
		  <div class="form-group">
		   		 <select class="form-control select-inline" onchange="local(this.value)">
				  <?php foreach($seasons as $key => $value):?>
		    	  	<?php if($key == $data['nSeason']):?>
		    	  		<option value="<?php echo urla($data,'nSeason',$key);?>" selected="selected"><?php echo $value;?></option>
		    	  	<?php else:?>
		    	  	<option value="<?php echo urla($data,'nSeason',$key);?>"><?php echo $value;?></option>
		    	    <?php endif?>
		    	  <?php endforeach?>


				</select>
		  </div>

		   <div class="form-group">
		   		 <select class="form-control select-inline" onchange="local(this.value)">
				  <?php foreach($classTypeCodes as $key => $value):?>
		    	  	<?php if($key == $data['sClassTypeCode']):?>
		    	  		<option value="<?php echo urla($data,'sClassTypeCode',$key);?>" selected="selected"><?php echo $value;?></option>
		    	  	<?php else:?>
		    	  	<option value="<?php echo urla($data,'sClassTypeCode',$key);?>"><?php echo $value;?></option>
		    	    <?php endif?>
		    	  <?php endforeach?>



				</select>
		  </div>

          <div class="form-group">
                 <select class="form-control select-inline" onchange="local(this.value)">
                  <?php foreach($classCodes as $key => $value):?>
                    <?php if($key == $data['sClassCode']):?>
                        <option value="<?php echo urla($data,'sClassCode',$key);?>" selected="selected"><?php echo $value;?></option>
                    <?php else:?>
                    <option value="<?php echo urla($data,'sClassCode',$key);?>"><?php echo $value;?></option>
                    <?php endif?>
                  <?php endforeach?>



                </select>
          </div>


		  <div class="form-group">
		   		 <select class="form-control select-inline" onchange="local(this.value)">
				  <?php foreach($types as $key => $value):?>
		    	  	<?php if($key == $data['nType']):?>
		    	  		<option value="<?php echo urla($data,'nType',$key);?>" selected="selected"><?php echo $value;?></option>
		    	  	<?php else:?>
		    	  	<option value="<?php echo urla($data,'nType',$key);?>"><?php echo $value;?></option>
		    	    <?php endif?>
		    	  <?php endforeach?>

				</select>
		  </div>

          <font color="red"><?php echo $nTotal;?></font>

		</form>
    	<table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>序号</th>

            <th>时段</th>

           <!--  <th>班型编码</th> -->
            <th>班型名称</th>
            <th>班级编码</th>
            <th>课节</th>
            <th>任课老师</th>
            <th>提交人数</th>
            <th>出勤人数</th>
            <th>提交率</th>



            <th>题型</th>


        </tr>

        </thead>
        <tbody>
        <?php $i=1;foreach($aAnalysisList as $value){?>
        <tr>
            <td><?php echo $i;$i++?></td>

            <td><?php echo $value['nyear'],'年 ',seasonName($value['nseason']);?></td>


            <!-- <td><?php echo $value['sclasstypecode'];?></td> -->
            <td><?php echo $value['sname'];?></td>
            <td><a href="<?php echo urla($data,'sClassCode',$value['sclasscode']);?>"><?php echo $value['sclasscode'];?></a></td>
            <td><?php echo trim($value['stopic']);?></td>
            <td><?php echo $value['sTeacher'];?></td>
             <td><?php echo $value['num'];?></td>
            <td><?php echo $value['totalNum'];?></td>
            <td><font color="red">[<?php echo round($value['num']*100/$value['totalNum'],1);?>%]</font></td>

            <td><?php echo questionTyoe($value['ntype']);?></td>


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
