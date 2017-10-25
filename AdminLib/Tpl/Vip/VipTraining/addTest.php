<script type="text/javascript" src="/static/js/training.js"></script>
<script type="text/javascript">
var selAll = document.getElementById("selAll");
function selectAll()
{
  var obj = document.getElementsByName("contain_module[]");
  console.log(obj);
  if(document.getElementById("selAll").checked == false)
  {
      for(var i=0; i<obj.length; i++){
        obj[i].checked=false;
      }
  }else{
          for(var i=0; i<obj.length; i++){  
            obj[i].checked=true;
          }
  }
 
}
function myCheck(){
        ts_name = document.getElementById("ts_name").value;
         if(ts_name == false){
            alert("请填写测试名称");
            return false;            
         }else{
            return true;
         }
}

</script>
<div class="easyui-layout" data-options="fit: true">
	<div region="north" data-options="fit: true, collapsible: false, border: false">
		<form id="dict-add-form" class="validate" method="post" id="myForm" novalidate action="/Vip/VipTraining/dict_add_test" onSubmit="return myCheck()">
        <input type="hidden" name="id" value="<?php echo $testInfo['id'];?>" />
        <input type="hidden" name="trid" value="<?php echo $testInfo['tr_id'];?>" />
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
                <span style="color: red;">注:请认真填写培训名称、组卷、学科，如有误，请删除记录重新添加！</span>                
				 <tr>                    
					<td class="alt right"><span class="red">*</span> 测试名称：</td>
					<td>                    
					<input type="text"  name="ts_name" id="ts_name" class="easyui-validatebox"  value="<?php echo $testInfo['ts_name'];?>" />                
					</td>
				</tr>                
				
                <tr>
					<td class="alt right"  style="width:70px;"> 培训名称：</td>
                    <td>
                    <?php if($testInfo['id'] != ''){?>
                            <?php echo $testInfo['tr_name'];?>
                    <?php }else{?>
                        <select name="tr_name">
                        <?php 
                	       if(!empty($trNameInfo)){
                				foreach ($trNameInfo as $key=>$val){
                				?>     
                                           			
                               <option  id="span_level_<?php echo $key+1;?>"  value="<?php echo $val['tr_name']?>_U_<?php echo $val['id'];?>" ><?php echo $val['tr_name'].' '.$val['tr_start_time'].'/'.$val['tr_end_time']?></option>                               
                			<?php
                				}
                			}
                			?>
                         </select>
                         
                   <?php }?>
                   
                         </td>
                	</td>
				</tr>
                <tr>
                    <td class="alt right">组卷</td>
                    <?php if($testInfo['zujuan'] != ''){?>
                        <td><span style="color: blue;"><?php echo $testInfo['zujuan_name']?></span></td>
                    <?php }else{?>
                    <td>
                        <div style="overflow-y: auto; height: 200px; width:95%">
                        <?php foreach($zujuanInfo as $key=>$val){
                                $id = $val['id'];
                                if(in_array($id,$zujuan_module))
                            	    $chk = 'checked';
        						else
       							    $chk = '';
                            ?>
                            <span id="span_module_<?php echo $k+1;?>"><input type="radio" id="zujuan_<?php echo $k+1;?>" name="zujuan_module[]" value="<?php echo $val['id']?>" size="10" <?php echo $chk?>>&nbsp;&nbsp;<?php echo $val['title'];?></span>&nbsp;&nbsp;<br />
                        <?php } ?> 
                        </div>
                    </td>
                     <?php }?>
                </tr>
                <tr>
					<td class="alt right " style="width: 50px;">学科：</td>
                    
					<td>
                    <?php if($testInfo['id'] == ''){?>
                    <input type="checkbox" id="selAll" name="selAll" onclick="selectAll();" />全选
                    &nbsp;&nbsp;<span style="color: red;">如果选择全部学科，请一定要勾选全选按钮！</span>
						<p>
            			<?php
            			if(!empty($dictInfo)){
            					foreach ($dictInfo as $k=>$module){
            						$id = $module['id'];
            						$name = $module['nianji'].$module['title'];
            						if(in_array($id,$contain_module))
            							$chk = 'checked';
            						else
            							$chk = '';
            						?>
                                    
            						<span id="span_module_<?php echo $k+1;?>"><input type="checkbox" id="module_<?php echo $k+1;?>" name="contain_module[]" value="<?php echo $id?>" size="10" <?php echo $chk?>>&nbsp;&nbsp;<?php echo $name;?></span>&nbsp;&nbsp;
                                    
            					<?php
            					}
            				}
            			?>
                    <?php }else{?>
                    <?php echo $testInfo['xueke_name'];?>
                    <?php }?>
                        
            		</p>
					</td>
				</tr>
                 <?php
            		$ckd = $chkdd = '' ;            		
            		if((!empty($testInfo) && $testInfo['recommended'] == 1 ))
            			$ckd = 'checked';
            		if(!empty($testInfo) && $testInfo['recommended'] == 2)
            			$chkdd ='checked';		
        		?>                
				<tr>
					<td class="alt right " style="width: 50px;"> 是否推送：</td>
					<td>
					   是<input type="radio" name="recommended" value="1"<?php echo $ckd;?>/>
                       否<input type="radio" name="recommended" value="2"<?php echo $chkdd;?>/>
					</td>
				</tr>
               
                
                
			
            </table>
            <span style="padding-left: 200px; margin-top: 5px;"><input style="width: 60px; height: 30px;" type="submit"  value="提交"></span>

		</form>
	</div>

</div>
