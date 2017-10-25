<!doctype html>
<html>
    <head>

        <link rel="stylesheet" href="http://cdn.bootcss.com/twitter-bootstrap/3.0.3/css/bootstrap.min.css">
         <link href="/static/Ueditor/themes/default/css/umeditor.css" type="text/css" rel="stylesheet">

        <link rel="stylesheet" href="http://cdn.bootcss.com/twitter-bootstrap/3.0.3/css/bootstrap-theme.min.css">
         <link href="/static/Ueditor/themes/default/css/umeditor.css" type="text/css" rel="stylesheet">

        <script type="text/javascript" src="/static/Ueditor/third-party/jquery.min.js"></script>
        <script type="text/javascript" charset="utf-8" src="/static/Ueditor/umeditor.config.js"></script>
        <script type="text/javascript" charset="utf-8" src="/static/Ueditor/umeditor.min.js"></script>
        <script type="text/javascript" src="/static/Ueditor/lang/zh-cn/zh-cn.js"></script>

    </head>

    <body>
    <div class="container">
      <h2><?php echo $aQustionInfo['sname'];?><small><?php echo $aQustionInfo['sclasstypecode'];?></small><?php echo $aQustionInfo['stopic'];?><?php echo $aQustionInfo['nyear'],'-',$aQustionInfo['nseason'];?>学期</h2>


        <form class="form-horizontal" role="form" id="formq" method="post" action="/Task/Index/multyUpdate">

                  <input  id="nQuestionid" name="nQuestionid" type="hidden" value="<?php echo $nQuestionid;?>" >


              <?php foreach($aOptionList as $key => $value){?>
              <h3><small>题目：</small><font color="red"><?php echo $value['sort'];?></font></h3>
              <input  id="nQuestionid" name="options[<?php echo $value['sort'];?>][nQuestionid]" type="hidden" value="<?php echo $nQuestionid;?>" >
              <input  id="nQuestionid" name="options[<?php echo $value['sort'];?>][sort]" type="hidden" value="<?php echo $value['sort'];?>" >



              <div class="form-group">
                <label for="inputPassword" class="col-sm-2 control-label">题目</label>
                <div class="col-sm-10">

                  <textarea style="width:960px;height:240px;" id='myEditor<?php echo $value['sort'];?>' class="form-control"  name="options[<?php echo $value['sort'];?>][question]" rows="10"><?php echo base64_decode($value['question']);?></textarea>
                </div>
              </div>

               <div class="form-group">
                <label for="inputPassword" class="col-sm-2 control-label">选项1</label>
                <div class="col-sm-10">

                  <textarea id="option1<?php echo $value['sort'];?>" class="form-control" name="options[<?php echo $value['sort'];?>][option1]" rows="1"><?php echo base64_decode($value['option1']);?></textarea>
                </div>
              </div>

              <div class="form-group">
                <label for="inputPassword" class="col-sm-2 control-label">选项2</label>
                <div class="col-sm-10">

                  <textarea id="option2<?php echo $value['sort'];?>" class="form-control" name="options[<?php echo $value['sort'];?>][option2]" rows="1"><?php echo base64_decode($value['option2']);?></textarea>
                </div>
              </div>

              <div class="form-group">
                <label for="inputPassword" class="col-sm-2 control-label">选项3</label>
                <div class="col-sm-10">

                  <textarea id="option3<?php echo $value['sort'];?>" class="form-control" name="options[<?php echo $value['sort'];?>][option3]" rows="1"><?php echo base64_decode($value['option3']);?></textarea>
                </div>
              </div>

              <div class="form-group">
                <label for="inputPassword" class="col-sm-2 control-label">选项4</label>
                <div class="col-sm-10">

                  <textarea id="option4<?php echo $value['sort'];?>" class="form-control" name="options[<?php echo $value['sort'];?>][option4]" rows="1"><?php echo base64_decode($value['option4']);?></textarea>
                </div>
              </div>

              <div class="form-group">
               <label for="checkbox-inline" class="col-sm-2 control-label">正确选项</label>
              <div class="col-sm-10">
                  <label class="checkbox-inline">
                    <input type="checkbox" id="answer1" name="options[<?php echo $value['sort'];?>][answer][]" value="1" <?php echo in_array(1, explode('|', $value['answer']))?'checked="checked"':''?>> A
                  </label>
                  <label class="checkbox-inline">
                    <input type="checkbox" id="answer2" name="options[<?php echo $value['sort'];?>][answer][]" value="2" <?php echo in_array(2, explode('|', $value['answer']))?'checked="checked"':''?>> B
                  </label>
                  <label class="checkbox-inline">
                    <input type="checkbox" id="answer3" name="options[<?php echo $value['sort'];?>][answer][]" value="3" <?php echo in_array(3, explode('|', $value['answer']))?'checked="checked"':''?>> C
                  </label>
                  <label class="checkbox-inline">
                    <input type="checkbox" id="answer4" name="options[<?php echo $value['sort'];?>][answer][]" value="4" <?php echo in_array(4, explode('|', $value['answer']))?'checked="checked"':''?>> D
                  </label>
              </div>
              </div>


              <div class="form-group">
                <label for="nValue" class="col-sm-2 control-label">分值</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="nValue" name="options[<?php echo $value['sort'];?>][nValue]" value="<?php echo $value['nvalue'];?>" placeholder="分值">
                </div>
              </div>
              <div class="form-group">
                <label for="inputPassword" class="col-sm-2 control-label">题目解析</label>
                <div class="col-sm-10">

                  <textarea style="width:960px;height:240px;" id="point<?php echo $value['sort'];?>" class="form-control" name="options[<?php echo $value['sort'];?>][sPoint]" rows="3"><?php echo base64_decode($value['spoint']);?></textarea>
                </div>
              </div>

              <hr>
              <script type="text/javascript">
               UM.getEditor('myEditor<?php echo $value['sort'];?>');
               UM.getEditor('option1<?php echo $value['sort'];?>');
                UM.getEditor('option2<?php echo $value['sort'];?>');
               UM.getEditor('option3<?php echo $value['sort'];?>');
               UM.getEditor('option4<?php echo $value['sort'];?>');
                UM.getEditor('point<?php echo $value['sort'];?>');

              </script>
              <?php $nOptionNum = $value['sort'];?>
              <?php } ?>


              <a class="btn btn-primary" id='add' onclick="addOption()">新增题目</a>

               <button type="submit" class="btn btn-primary" >提交</button>







            </form>
    </div>

    <!-- Modal -->
 <div id='modal' class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-body"></div>
 </div>


    </body>



<script src="http://cdn.bootcss.com/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
<script src="http://tiyee.qiniudn.com/bootstrap-wysiwyg.js"></script>

<script type="text/javascript">
var nLastNum = <?php echo $nOptionNum+1;?>;
//实例化编辑器
    var um = UM.getEditor('myEditor');
    um.addListener('blur',function(){
        $('#focush2').html('编辑器失去焦点了')
    });
    um.addListener('focus',function(){
        $('#focush2').html('')
    });
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
    function addOption() {

    	/*var html = '';
    	html = $('#addmodal').html();
    	var reg=new RegExp("@@","g");
    	html = html.replace(reg, nLastNum);
    	$("#add").before(html);
    	var um = UM.getEditor('myEditor'+nLastNum);
    	nLastNum += 1;*/
    	  $.ajax({
             type: "GET",
             url: "/Task/Question/ajax_addOption/nQuestionid/<?php echo $nQuestionid;?>/sort/"+nLastNum,
             dataType: "json",
             async:false,
             success: function(data){
                        if(data >0 ) {
                            location.reload();
                            //location.href='Task/Index/multyAdd/nQuestionid/<?php echo $nQuestionid;?>';
                            return false;
                        }
                        alert('add error!');
                        return false;
                    }


            });



    }
</script>

</html>
