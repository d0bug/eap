<div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">批量添加<?php echo $sClassTypeCode;?></h4>
      </div>
      <div class="modal-body">
              <form class="form-horizontal" role="form" id="formq" method="post" action="/Task/Index/multyUpdate">
               <div class="form-group">
                <label class="col-sm-2 control-label">题目编码</label>
                <div class="col-sm-10">
                  <p class="form-control-static"><font color="blue"><?php echo $nQuestionid;?></font></p>
                  <input  id="nQuestionid" name="nQuestionid" type="hidden" value="<?php echo $nQuestionid;?>" >
                </div>
              </div>




              <div class="form-group">
                <label for="inputPassword" class="col-sm-2 control-label">年</label>
                <div class="col-sm-10">
                  <select class="form-control" id="nYear" name="nYear" >
                    <?php for($i = $nYear-1;$i<=$nYear+3;$i++){?>
                    <?php if($i == $nYear){?>
                     <option value="<?php echo $i;?>" selected = 'selected'><?php echo $i;?>年</option>
                    <?php } else {?>
                    <option value="<?php echo $i;?>"><?php echo $i;?>年</option>

                    <?php }?>

                    <?php }?>


                  </select>
                </div>
              </div>


              <div class="form-group">
                <label for="inputPassword" class="col-sm-2 control-label">季节</label>
                <div class="col-sm-10">
                 <?php foreach($aSeasons as $key => $value) {?>
                 <?php if($key == $nSeason){?>
                 <label class="radio-inline"><input type="radio" name="nSeason"  value="<?php echo $key;?>" checked><?php echo $value;?></label>
               <?php }else {?>
                 <label class="radio-inline"><input type="radio" name="nSeason"  value="<?php echo $key;?>"><?php echo $value;?></label>
               <?php }?>
                 <?php }?>


                </div>
              </div>
              <div class="form-group">
                <label for="inputPassword" class="col-sm-2 control-label">题型</label>
                <div class="col-sm-10">
                 <label class="radio-inline"><input type="radio" name="nType"  value="1" checked>选择题</label>

                  <label class="radio-inline"><input type="radio" name="nType"   value="2">主观题</label>


                </div>
              </div>
               <div class="form-group">
                <label for="inputPassword" class="col-sm-2 control-label">班型</label>
                <div class="col-sm-10">
                  <select class="form-control" id="sClassTypeCode" name="sClassTypeCode" onchange="getClassCode(this.value)" >
                  <option value="0">请选择</option>
                    <?php foreach($aClassType as $value){?>
                    <?php if($value['scode'] == $sClassTypeCode){?>
                            <option value="<?php echo $value['scode'];?>" selected="selected"><?php echo $value['sname'];?></option>
                    <?php } else { ?>
                            <option value="<?php echo $value['scode'];?>"><?php echo $value['sname'];?></option>
                    <?php }?>


                    <?php } ?>

                  </select>
                </div>
              </div>

              <div class="form-group">
                <label for="inputPassword" class="col-sm-2 control-label">课节</label>
                <div class="col-sm-10">
                  <select class="form-control" id="sTopic" name="sTopic" >
                  <option value="0">请选择</option>


                  </select>
                </div>
              </div>








            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
       <button type="submit" class="btn btn-primary" >提交</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->




  <script type="text/javascript">



      function getClassCode(value) {
        //alert($('#formq').serialize());
        if(value == 0) {
          alert('you must select a option!');
        }
        $.ajax({
             type: "POST",
             url: "/Task/Question/ajax_getClass/sClassTypeCode/"+value,
             dataType: "json",
             data: $('#formq').serialize(),
             async:false,
             success: function(data){
                        var html = '<option value="0">请选择</option>';
                        for(var i in data) {
                          html += '<option value="'+data[i]['topic']+'">'+data[i]['topic']+'</option>';
                        }
                        $('#sTopic').html(html);
                        return false;
                      }


         });

      }
      function submits() {
        var nType = $('input[name="nType"]:checked').val();
        var nQuestionid = $('#nQuestionid').val();
         $.ajax({

             type: "POST",

             url: "/Task/Question/ajax_stepTwo/n/2",
              data: $('#formq').serialize(),



             dataType: "json",
             async:false,

             success: function(data){

                        if(data >0) {
                          location.reload();

                          }

                      }

         });
      }


  </script>
