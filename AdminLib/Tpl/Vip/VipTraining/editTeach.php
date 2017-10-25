
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
</script>
<div class="easyui-layout" data-options="fit: true">
	<div region="north" data-options="fit: true, collapsible: false, border: false">
		<form id="dict-add-form" method="post" novalidate action="/Vip/VipTraining/dict_edit_teach">
        <input type="hidden" name="te_id" value="<?php echo $teachInfo['id'];?>" />
			<table width="90%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">                
				 <tr>
					<td class="alt right " style="width: 50px;">姓名：</td>
					<td>
					<input type="text"  name="te_name" class="easyui-validatebox"  value="<?php echo $teachInfo['te_name'];?>"style="width:250px" />
					</td>
				</tr>
                <?php
            		$ckd = $chkdd = '' ;            		
            		if((!empty($teachInfo) && $teachInfo['sex'] == 1 ))
            			$ckd = 'checked';
            		if(!empty($teachInfo) && $teachInfo['sex'] == 2)
            			$chkdd ='checked';		
        		?>
				<tr>
					<td class="alt right " style="width: 50px;" > 性别：</td>
					<td>                    
					   男<input type="radio" name="sex" value="1" <?php echo $ckd;?>/>
                       女<input type="radio" name="sex" value="2" <?php echo $chkdd;?>/>                    
					</td>
				</tr>
                <tr>
					<td class="alt right  " style="width: 50px;">生日</td>
					<td>
					    <input type="text" name="birthday" class="input easyui-datebox" value="<?php echo $teachInfo['birthday'];?>" /> 
					</td>
				</tr>
				<tr>
					<td class="alt right  " style="width: 60px;">毕业学校</td>
					<td>
						<input type="text" class="easyui-validatebox" name="school" value="<?php echo $teachInfo['school'];?>"  style="width:250px" />
					</td>
				</tr>
                <tr>
					<td class="alt right  " style="width: 50px;">专业</td>
					<td>
						<input type="text" class="easyui-validatebox" name="professional" value="<?php echo $teachInfo['professional'];?>"  style="width:250px" />
					</td>
				</tr>
                <tr>
					<td class="alt right " style="width: 60px;">最高学历</td>
					<td>
						<input type="text" class="easyui-validatebox" name="level_school" value="<?php echo $teachInfo['level_school'];?>"  style="width:250px" />
					</td>
				</tr>
                <tr>
					<td class="alt right " style="width: 60px;">毕业年份</td>
					<td>
						<input type="text" class="easyui-validatebox" name="graduation" value="<?php echo $teachInfo['graduation'];?>"  style="width:250px" />
					</td>
				</tr>
                <tr>
					<td class="alt right " style="width: 70px;"><span class="red">*</span> 联系电话</td>
					<td>
						<input type="text" class="easyui-validatebox" name="phone" value="<?php echo $teachInfo['phone'];?>"  style="width:250px"  data-options="required: true, validType: 'remote[\'/Vip/VipTraining/check_phone?1=1\', \'phone\']', invalidMessage: '联系电话重复'"/>
					</td>
				</tr>
                <tr>
					<td class="alt right  " style="width: 50px;">邮箱</td>
					<td>
						<input type="text" class="easyui-validatebox" name="mail" value="<?php echo $teachInfo['mail'];?>"  style="width:250px" />
					</td>
				</tr>
                <tr>
					<td class="alt right " style="width: 50px;">学科</td>
					<td>                    
                    <input type="checkbox" id="selAll" onclick="selectAll();" />全选
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
            		</p>                    
					</td>
				</tr>
                <?php
            		$ckd = $chkdd = '' ;            		
            		if((!empty($teachInfo) && $teachInfo['formal'] == 1 ))
            			$ckd = 'checked';
            		if(!empty($teachInfo) && $teachInfo['formal'] == 2)
            			$chkdd ='checked';		
        		?>
                <tr>
					<td class="alt right " style="width: 50px;"><span class="red">*</span> 性质：</td>
					<td>
					   全职<input type="radio" name="formal" value="1"<?php echo $ckd;?>/>
                       兼职<input type="radio" name="formal" value="2"<?php echo $chkdd;?>/>
					</td>
				</tr>
               <hr />
               <!--修改 -->
                <?php
            		$ckd = $chkdd = '' ;            		
            		if((!empty($teachInfo) && $teachInfo['through'] == 1 ))
            			$ckd = 'checked';
            		if(!empty($teachInfo) && $teachInfo['through'] == 2)
            			$chkdd ='checked';		
        		?>
               <tr>
					<td class="alt right " style="width: 50px;"><span class="red">*</span> 是否通过：</td>
					<td>
					   是<input type="radio" name="through" value="1" <?php echo $ckd;?> />
                       否<input type="radio" name="through" value="2" <?php echo $chkdd;?>/>
					</td>
				</tr>
                <?php
            		$ckd = $chkdd = '' ;            		
            		if((!empty($teachInfo) && $teachInfo['status'] == 0 ))
            			$ckd = 'checked';
            		if(!empty($teachInfo) && $teachInfo['status'] == 1)
            			$chkdd ='checked';		
        		?>
                <tr>
					<td class="alt right " style="width: 50px;"><span class="red">*</span> 关闭账号</td>
					<td>
					   是<input type="radio" name="status" value="0"<?php echo $chk;?> />
                       否<input type="radio" name="status" value="1" <?php echo $chkdd;?>/>
					</td>
				</tr>          
			
            </table>
            
           <span style="padding-left: 200px; margin-top: 5px;"><input style="width: 60px; height: 30px;" type="submit" value="提交"></span>
		</form>
	</div>

</div>
