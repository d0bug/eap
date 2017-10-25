<script type="text/javascript" src="/static/js/training.js"></script>

<div class="easyui-layout" data-options="fit: true">
	<div region="north" data-options="fit: true, collapsible: false, border: false">
		<form id="dict-add-form" method="post" novalidate action="/Vip/VipTraining/dict_add_people">
        <input type="hidden" name="id" value="<?php echo $paperInfo['id'];?>" />
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
                
				 <tr>
					<td class="alt right"><span class="red">*</span> 培训名称：</td>
					<td>
					<input type="text"  name="tr_name" class="easyui-validatebox"  value="<?php echo $paperInfo['tr_name'];?>" data-options="required: true, validType: 'remote[\'/Vip/VipTraining/check_code?1=1\', \'tr_name\']', invalidMessage: '培训名称重复'"/>
					</td>
				</tr>
				<tr>
					<td class="alt right"><span class="red">*</span> 培训开始时间：</td>
					<td>
					    <input type="text" name="tr_start_time" class="input easyui-datebox"  value="<?php echo $paperInfo['tr_start_time'];?>" /> 
                        <!--input type="text" class="easyui-timespinner" name="exam_tr_start_time"  value="<?php 
                        if($paperInfo['exam_tr_start_time']){
                            echo $paperInfo['exam_tr_start_time'];
                            }else{
                                echo "00:00";
                                } ?>" /-->
					</td>
				</tr>
                <tr>
					<td class="alt right"><span class="red">*</span> 培训结束时间：</td>
					<td>
					    <input type="text" name="tr_end_time" class="input easyui-datebox" value="<?php echo $paperInfo['tr_end_time'];?>" /> 
                        <!--input type="text" class="easyui-timespinner" name="exam_tr_end_time"  value="<?php 
                        if($paperInfo['exam_tr_end_time']){
                            echo $paperInfo['exam_tr_end_time'];
                            }else{
                                echo "00:00";
                                } ?>" /-->
					</td>
				</tr>
				<tr>
					<td class="alt right">线下考试次数：</td>
					<td>
						<input type="text" class="easyui-validatebox" name="tr_audit_num" value="<?php echo $paperInfo['tr_audit_num'];?>"  style="width:25px" /> 次
					</td>
				</tr>
               
                
                <tr>
                <span style="color: red;">* 考试时间添加格式：2016-01-01 09:00:00 </span>
    			<div style="border: 1px solid silver;">
                <a href="#" onclick="add_level('#levelSpan')"><img src="/static/images/add.png"></a><div id="levelSpan" style="margin-left:5px;">
                <input type=hidden id="level_num" name="level_num" value="<?php echo count($levelList)?>">
                <?php 
        	       if(!empty($levelList)){
        				foreach ($levelList as $key=>$level){
        				?>
        				<span id="span_level_<?php echo $key+1;?>">
        			   考试时间：<input type="text" id="leveltime_<?php echo $key+1;?>" name="time[]" value="<?php echo $level['time']?>" >&nbsp;&nbsp;
        			   时长：<input type="text" id="levellong_<?php echo $key+1;?>" name="long[]" value="<?php echo $level['long']?>" size="2">&nbsp;&nbsp;
        			   满分：<input type="text" id="levelscore_<?php echo $key+1;?>" name="score[]" value="<?php echo $level['score']?>" size="2">&nbsp;&nbsp;
        			   
        			   <a href="#" onclick="del_level('#span_level_<?php echo $key+1;?>')"><img src="/static/images/delete.png"></a><br></span>
        			<?php
        				}
        			}else{
        			?>
        				<span id="span_level_1">
                	   考试时间：<input type="text" id="leveltime_1" name="time[]"   value="<?php $level['time']?>" >&nbsp;&nbsp;
                	   时长：<input type="text" id="levellong_1" name="long[]" value="<?php echo $level['long']?>" size="2">&nbsp;&nbsp;
                	   满分：<input type="text" id="levelscore_1" name="score[]" value="<?php echo $level['score']?>" size="2">&nbsp;&nbsp;
                	   <a href="#" onclick="del_level('#span_level_1')"><img src="/static/images/delete.png"></a><br>
                       </span>  
        			<?php
        			}
        			?>
                   <!--span id="span_level_1">
                	   考试时间：<input type="text" id="leveltime_1" name="time[]"   value="<?php $level['time']?>" >&nbsp;&nbsp;
                	   时长：<input type="text" id="levellong_1" name="long[]" value="<?php echo $level['long']?>" size="2">&nbsp;&nbsp;
                	   满分：<input type="text" id="levelscore_1" name="score[]" value="<?php echo $level['score']?>" size="2">&nbsp;&nbsp;
                	   <a href="#" onclick="del_level('#span_level_1')"><img src="/static/images/delete.png"></a><br>
                    </span-->               
                  
                </div>
                </tr>
			
            </table>
            
            <span style="padding-left: 200px; margin-top: 5px;"><input style="width: 60px; height: 30px;" type="submit" value="提交"></span>
		</form>
	</div>

</div>
