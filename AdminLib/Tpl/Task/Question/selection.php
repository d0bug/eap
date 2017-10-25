<div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">题目详情</h4>
      </div>
      <div class="modal-body">
              <form class="form-horizontal" role="form" id="formq">
               <div class="form-group">
                <label class="col-sm-2 control-label">题目编码</label>
                <div class="col-sm-10">
                  <p class="form-control-static"><font color="blue"><?php echo $nQuestionid;?></font></p>
                  <input  id="nQuestionid" name="nQuestionid" type="hidden" value="<?php echo $nQuestionid;?>" >
                </div>
              </div>




              <div class="form-group">
                <label for="inputPassword" class="col-sm-2 control-label">题目</label>
                <div class="col-sm-10">

                  <textarea class="form-control" name="question" rows="3"><?php echo $aOptionInfo['question'];?></textarea>
                </div>
              </div>

               <div class="form-group">
                <label for="inputPassword" class="col-sm-2 control-label">选项1</label>
                <div class="col-sm-10">

                  <textarea class="form-control" name="option1" rows="1"><?php echo $aOptionInfo['option1'];?></textarea>
                </div>
              </div>

              <div class="form-group">
                <label for="inputPassword" class="col-sm-2 control-label">选项2</label>
                <div class="col-sm-10">

                  <textarea class="form-control" name="option2" rows="1"><?php echo $aOptionInfo['option2'];?></textarea>
                </div>
              </div>

              <div class="form-group">
                <label for="inputPassword" class="col-sm-2 control-label">选项3</label>
                <div class="col-sm-10">

                  <textarea class="form-control" name="option3" rows="1"><?php echo $aOptionInfo['option3'];?></textarea>
                </div>
              </div>

              <div class="form-group">
                <label for="inputPassword" class="col-sm-2 control-label">选项4</label>
                <div class="col-sm-10">

                  <textarea class="form-control" name="option4" rows="1"><?php echo $aOptionInfo['option4'];?></textarea>
                </div>
              </div>

              <div class="form-group">
               <label for="checkbox-inline" class="col-sm-2 control-label">正确选项</label>
              <div class="col-sm-10">
                  <label class="checkbox-inline">
                    <input type="checkbox" id="answer1" name="answer[]" value="1" <?php echo in_array(1, $aOptionInfo['answer'])?'checked="checked"':''?>> A
                  </label>
                  <label class="checkbox-inline">
                    <input type="checkbox" id="answer2" name="answer[]" value="2" <?php echo in_array(2, $aOptionInfo['answer'])?'checked="checked"':''?>> B
                  </label>
                  <label class="checkbox-inline">
                    <input type="checkbox" id="answer3" name="answer[]" value="3" <?php echo in_array(3, $aOptionInfo['answer'])?'checked="checked"':''?>> C
                  </label>
                  <label class="checkbox-inline">
                    <input type="checkbox" id="answer4" name="answer[]" value="4" <?php echo in_array(4, $aOptionInfo['answer'])?'checked="checked"':''?>> D
                  </label>
              </div>
              </div>








              <div class="alert alert-warning fade in hide">
<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
<strong>Holy guacamole!</strong>
Best check yo self, you're not looking too good.
</div>




            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
       <button type="button" class="btn btn-primary" onclick="submitq()">提交</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->




  <script type="text/javascript">
      function getClassCode(value) {
        if(value == 0) {
          alert('you must select a option!');
        }
        $.ajax({
             type: "GET",
             url: "/Task/Question/ajax_getClass/sClassTypeCode/"+value,
             dataType: "json",
             async:false,
             success: function(data){
                        var html = '<option value="0">请选择</option>';
                        for(var i in data) {
                          html += '<option value="'+data[i]['scode']+'">'+data[i]['sname']+'('+data[i]['scode']+')</option>';
                        }
                        $('#sClassCode').html(html);
                        return false;
                      }


         });

      }
      function submitq() {
        //$(".alert").show();
        //alert($('#form').serialize());
         $.ajax({

             type: "POST",

             url: "/Task/Question/ajax_stepFour",

             data: $('#formq').serialize(),

             dataType: "json",
             async:false,

             success: function(data){
                        location.reload();


                      }

         });
      }



  </script>
