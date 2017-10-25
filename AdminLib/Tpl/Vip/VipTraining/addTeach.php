
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
        phone = document.getElementById("phone").value;
        birthday = document.getElementById("birthday").value;
        te_name = document.getElementById("te_name").value;
         if(phone == false){
            alert("请填写联系电话");
            return false;            
         }else if(te_name == false){
             alert("请填写姓名");
            return false;
         }else{
            return true;
         }
}
</script>
<div class="easyui-layout" data-options="fit: true">
	<div region="north" data-options="fit: true, collapsible: false, border: false">
		<form id="dict-add-form" method="post" novalidate action="/Vip/VipTraining/dict_add_teach" onSubmit="return myCheck()">
        <input type="hidden" name="tr_id" value="<?php echo $id;?>" />
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">                
				 <tr>
					<td class="alt right " style="width: 50px;">姓名：</td>
					<td>
					<input type="text" id="te_name"  name="te_name" class="easyui-validatebox"  value=""style="width:250px" />
					</td>
				</tr>
				<tr>
					<td class="alt right " style="width: 50px;" > 性别：</td>
					<td>
					   男<input type="radio" name="sex" value="1">
                       女<input type="radio" name="sex" value="2">
					</td>
				</tr>
                <tr>
					<td class="alt right  " style="width: 50px;">生日</td>
					<td>
					    <input type="text" id="birthday" name="birthday" class="input easyui-datebox" value="" /> 
					</td>
				</tr>
				<tr>
					<td class="alt right  " style="width: 60px;">毕业学校</td>
					<td>
						<input type="text" class="easyui-validatebox" name="school" value=""  style="width:250px" />
					</td>
				</tr>
                <tr>
					<td class="alt right  " style="width: 50px;">专业</td>
					<td>
						<input type="text" class="easyui-validatebox" name="professional" value=""  style="width:250px" />
					</td>
				</tr>
                <tr>
					<td class="alt right " style="width: 60px;">最高学历</td>
					<td>
						<input type="text" class="easyui-validatebox" name="level_school" value=""  style="width:250px" />
					</td>
				</tr>
                <tr>
					<td class="alt right " style="width: 60px;">毕业年份</td>
					<td>
						<input type="text" class="easyui-validatebox" name="graduation" value=""  style="width:250px" />
					</td>
				</tr>
                <tr>
					<td class="alt right " style="width: 70px;"><span class="red">*</span> 联系电话</td>
					<td>
						<input type="text" id="phone" class="easyui-validatebox" name="phone" value=""  style="width:250px"  data-options="required: true, validType: 'remote[\'/Vip/VipTraining/check_phone?1=1\', \'phone\']', invalidMessage: '联系电话重复'"/>
					</td>
				</tr>
                <tr>
					<td class="alt right  " style="width: 50px;">邮箱</td>
					<td>
						<input type="text" class="easyui-validatebox" name="mail" value=""  style="width:250px" />
					</td>
				</tr>
                <tr>
					<td class="alt right " style="width: 50px;">学科</td>
					<td>
                        <input type="checkbox" id="selAll" onclick="selectAll();" />全选&nbsp;&nbsp;<span style="color: red;">如果选择全部学科，请一定要勾选全选按钮！</span>
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
                <tr>
					<td class="alt right " style="width: 50px;"> 性质：</td>
					<td>
					   全职<input type="radio" name="formal" value="1">
                       兼职<input type="radio" name="formal" value="2">
					</td>
				</tr>
               
			
            </table>
            
            <span style="padding-left: 200px; margin-top: 5px;"><input style="width: 60px; height: 30px;" type="submit" value="提交"></span>
		</form>
	</div>

</div>
