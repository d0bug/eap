
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
                 <textarea class="form-control" ondblclick ="javascript:plugin1().openword(0,'middcience', '<?php echo $token;?>','question','')" id="question" name="question" rows="3"><?php echo $aQuestionInfo['sQuestion']['question'];?></textarea>


                </div>
              </div>
              <?php if(isset($aQuestionInfo['sQuestion']['options'])):?>
               <div class="form-group">
                <label for="inputPassword" class="col-sm-2 control-label">选项[<a href="javascript:void(0)" onclick="addOpions(1)">+</a>]</label>
                <div class="col-sm-10">
                <div id="option0" style="display:none;"></div>
                			<?php foreach($aQuestionInfo['sQuestion']['options'] as $chr=>$value):?>
						   <div class="form-group" id="<?php echo chr(65+$chr);?>">
			                <label for="inputPassword" class="col-sm-2 control-label"><?php echo chr(65+$chr);?>
			                <input id="corrent_answer" name="corrent_answer" value="<?php echo $chr;?>" <?php  if($chr == (int)$aQuestionInfo['corrent_answer'] ) echo 'checked';?> type="radio">

							<a href="javascript:void(0)" onclick="removeOption('<?php echo chr(65+$chr);?>')"><span class="glyphicon glyphicon glyphicon-trash"></span></a>

			                </label>
			                <div class="col-sm-10">
			                 <textarea class="form-control" ondblclick ="javascript:plugin1().openword(0,'middcience', '<?php echo $token;?>','option<?php echo chr(65+$chr);?>','')" id="option<?php echo chr(65+$chr);?>" name="options[<?php echo $chr;?>]" rows="1"><?php echo $value;?></textarea>


			                </div>
			              </div>
			          		<?php endforeach?>


                </div>
              </div>
          <?php endif?>

              <div class="form-group">
                <label for="inputPassword" name="sPoint" class="col-sm-2 control-label">解析</label>
                <div class="col-sm-10">
                  <textarea class="form-control"  ondblclick ="javascript:plugin1().openword(0,'middcience', '<?php echo $token;?>','point','')" id="point" name="point" rows="1"><?php echo $aQuestionInfo['sQuestion']['point'];?></textarea>
                </div>
              </div>








            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
       <button type="submit" onclick="sub()" class="btn btn-primary" >提交</button>
      </div>

