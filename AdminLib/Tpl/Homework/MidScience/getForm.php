
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
        <h4 class="modal-title" id="myModalLabel"><?php echo $action;?></h4>
      </div>
      <div class="modal-body">
              <form class="form-horizontal" role="form" id="formq" method="post" onsubmit="return false" action="">
               <div class="form-group">
                <label class="col-sm-2 control-label">题号</label>
                <div class="col-sm-2">
                  <!-- <p class="form-control-static"><font color="blue"></font></p> -->
                  <input class="form-control"  id="subject_no" name="subject_no" type="number" value="<?php echo $aQuestionInfo['subject_no'];?>" >
                </div>
              </div>
              		<input type="hidden" name="main_subject_id" value="<?php echo $aQuestionInfo['main_subject_id'];?>">
                  <input type="hidden" name="type" value="<?php echo $aQuestionInfo['type'];?>">
                  <input type="hidden"  id="url" value="<?php echo $url;?>">



              <div class="form-group">
                <label for="inputPassword" class="col-sm-2 control-label">年</label>
                <div class="col-sm-2">
                  <input class="form-control" id="disabledInput" type="text" placeholder="<?php echo $aPaperInfo['classyear'];?>" disabled>
                </div>

                <div class="col-sm-2">
                  <input class="form-control" id="disabledInput" type="text" placeholder="<?php echo $aPaperInfo['semester_id'];?>" disabled>
                </div>

              </div>


              <div class="form-group">
                <label for="inputPassword" class="col-sm-2 control-label">满分</label>
                <div class="col-sm-2">
                  <input class="form-control"  id="fullscore" name="fullscore" type="number" value="<?php echo $aQuestionInfo['fullscore'];?>" >
                </div>
              </div>
              <div class="form-group">
                <label for="inputPassword" class="col-sm-2 control-label">题目</label>
                <div class="col-sm-10">
                 <textarea class="form-control" ondblclick ="javascript:plugin1().openword(0,'middcience', '<?php echo $token;?>','question','')" id="question"  name="question" rows="3"><?php echo $aQuestionInfo['sQuestion']['question'];?></textarea>


                </div>
              </div>

              <div class="form-group">
                <label for="inputPassword" class="col-sm-2 control-label">答案</label>
                <div class="col-sm-10">
                 <textarea class="form-control"  ondblclick ="javascript:plugin1().openword(0,'middcience', '<?php echo $token;?>','correntanswer','')" id="correntanswer" name="corrent_answer" rows="3"><?php echo $aQuestionInfo['corrent_answer'];?></textarea>


                </div>
              </div>

              <div class="form-group">
                <label for="inputPassword" name="sPoint" class="col-sm-2 control-label">解析</label>
                <div class="col-sm-10">
                  <textarea class="form-control"  ondblclick ="javascript:plugin1().openword(0,'middcience', '<?php echo $token;?>','point','')" id="point"  name="point" rows="1"><?php echo $aQuestionInfo['sQuestion']['point'];?></textarea>
                </div>
              </div>








            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
       <button type="submit" onclick="sub()" class="btn btn-primary" >提交</button>
      </div>

