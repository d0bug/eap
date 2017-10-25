
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
        pingyu = document.getElementById("pingyu").value;
         if(pingyu == false){
            alert("请填写评语");
            return false;            
         }
}
</script>
<div class="easyui-layout" data-options="fit: true">
	<div region="north" data-options="fit: true, collapsible: false, border: false">
		<form id="dict-add-form" method="post" novalidate action="/Vip/VipTraining/dict_add_trakaoping" onSubmit="return myCheck()">
        <input type="hidden" name="tr_id" value="<?php echo $id;?>" />
			<table width="90%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">                
				
                <tr>
					<td class="alt right  " style="width: 50px;">培训期</td>
					<td>
					    <span><?php echo $trInfo['tr_name']?></span>
					</td>
				</tr>
				</tr>
                <tr>
					<td class="alt right " style="width: 50px;">学科</td>
					<td>
                    <input type="checkbox" id="selAll" name="selAll" onclick="selectAll();" />全选 
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
					<td class="alt right  " style="width: 60px;">评语</td>
					<td>
						<textarea name="pingyu" id="pingyu" rows="5" cols="50" value=''></textarea>
					</td>
				</tr>
               
			
            </table>
            
           <span style="padding-left: 200px; margin-top: 5px;"><input style="width: 60px; height: 30px;" type="submit" value="提交"></span>

		</form>
	</div>

</div>
