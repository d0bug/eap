<script type="text/javascript" src="/static/js/training.js"></script>
<!--script type="text/javascript" src="/static/js/jquery.form.js"></script-->
<!--script type="text/javascript" src="/static/js/jquery-1.9.1.min.js"></script-->
<script language="javascript">
/*
//动态添加文件选择控件
function AddRow() 
{ 
    var eNewRow = tblData.insertRow(); 
    
    for (var i=0;i<1;i++) 
    { 
        var eNewCell = eNewRow.insertCell(); 
        eNewCell.innerHTML = "<tr><td><input type='file' name='filelist[]' size='50'/><input   type='button'   value='删除'   onclick='del(eNewRow)'> </td></tr>"; 
    }
     
}
*/
//jquery实现文件上传的按钮添加和删除
$(function(){
    $("input[type=button]").click(function(){
        var br = $("<br>");
        var input = $("<input type='file' name='filelist[]'/>");
        var button = $("<input type='button' value='删除'/>");
        $("#div1").append(br).append(input).append(button);        
        button.click(function() {
            br.remove();
            input.remove();
            button.remove();
        });
    });
}); 


//全选
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
		<form id="dict-add-form" method="post" novalidate action="/Vip/VipTraining/dict_add_powerpoint"  enctype="multipart/form-data">   
            <input  type="hidden" name="id"  value="<?php echo $powerPointInfo[0]['id'];?>"/>
            <input type="hidden" name="trid" value="<?php echo $powerPointInfo[0]['tr_id'];?>" />
			<table width="95%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">   
            <span style="color: red;">注:请认真填写培训名称、组卷、学科，如有误，请删除记录重新添加！</span>              
				 <tr>
					<td class="alt right" style="width:70px;"> PPT名称：</td>
					<td>
					<input type="text"  name="pt_name" class="easyui-validatebox"  value="<?php echo $powerPointInfo[0]['pt_name'];?>" />
					</td>
				</tr>
				
                <tr>
					<td class="alt right"  style="width:70px;"> 培训名称：</td>
                    <td>
                        <?php if($powerPointInfo[0]['id'] == ''){?>
                        <select name="tr_name">
                        <?php 
                	       if(!empty($trNameInfo)){
                	            if($powerPointInfo[0]['tr_name']){
                	               echo  '<option  selected id="span_level_"'.$powerPointInfo[0]['tr_id'].' value='.$powerPointInfo[0]['tr_name'].'_U_'.$powerPointInfo[0]['id'].'>'.$powerPointInfo[0]['tr_name'].'</option>';   
                	            }                                
                				foreach ($trNameInfo as $key=>$val){                				    
                				?>                                                			
                               <option  id="span_level_<?php echo $key+1;?>"   value="<?php echo $val['tr_name']?>_U_<?php echo $val['id'];?>" ><?php echo $val['tr_name'].' '.$val['tr_start_time'].'/'.$val['tr_end_time']?></option>                               
                			<?php
                				}
                			}
                			?>
                         </select>
                         <?php }else{?>
                         <?php echo $powerPointInfo[0]['tr_name'];?>
                         <?php }?>
                         </td>
                	</td>
				</tr>
                <tr>
					<td class="alt right " style="width: 50px;">学科：</td>
                    <?php if($powerPointInfo[0]['id'] == ''){?>
					<td><input type="checkbox" id="selAll" name="selAll" onclick="selectAll();" />全选&nbsp;&nbsp;<span style="color: red;">如果选择全部学科，请一定要勾选全选按钮！</span>
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
            						<span id="span_module_<?php echo $k+1;?>"><input type="checkbox" id="module_<?php echo $k+1;?>" name="contain_module[]" value="<?php echo $id?>" size="10" <?php echo $chk?>/>&nbsp;&nbsp;<?php echo $name;?></span>&nbsp;&nbsp;
            					<?php
            					}
            				}
            			?>
            		</p>
					</td>
                     <?php }else{?>
                     <td>
                        <?php echo $powerPointInfo[0]['xueke_name'];?>
                     </td>
                     
                     <?php }?>
                    
				</tr>
                <tr>
                <?php
            		$ckd = $chkdd = '' ;            		
            		if((!empty($powerPointInfo) && $powerPointInfo[0]['recommended'] == 1 ))
            			$ckd = 'checked';
            		if(!empty($powerPointInfo) && $powerPointInfo[0]['recommended'] == 2)
            			$chkdd ='checked';		
        		?>
					<td class="alt right " style="width: 50px;"> 是否推送：</td>
					<td>
					   是<input type="radio" name="recommended" value="1"<?php echo $ckd;?>/>
                       否<input type="radio" name="recommended" value="2"<?php echo $chkdd;?>/>
					</td>
				</tr>
                
                <tr>                  
                    <table id="tblData" width="400" border="0" class="tableInfo">
                        
                        <!-- 将上传文件必须用post的方法和enctype="multipart/form-data" --> 
                        <!-- 将本页的网址传给uploadfile.php-->                         
                        <?php if($powerPointInfo[0]['id']){?>
                        <input name="postadd" type="hidden" value="<?php echo "http://".$_SERVER['HTTP_HOST'].$_SERVER["PHP_SELF"]; ?>" />
                        <tr><td>文件上传列表 
                        
                        <?php if($powerPointInfo[0]['ppt_url'] != ''){?>
                        <?php $file = (explode(',',$powerPointInfo[0]['ppt_url']));
                                echo "已上传："."<br/>";
                                foreach($file as $val){
                                  //echo '<span style="color:red" ><img src ="'.$val.'"></span>'.$val."<br/>";  
                                } ?>
                        <?php }?> 
                        
                        <?php
                            foreach($pptFile as $key => $val){
                                $filename = explode('_',$val['fileurl']);                                                             
                                ?>                                
                                <span id="<?php echo $val['id']?>" style="margin-bottom: 5px;"><?php echo $filename[2];?>&nbsp;&nbsp;&nbsp;<a href="#" onclick="delfile('<?php echo U('Vip/VipTraining/delPptFile')?>','<?php echo $val['id']?>','<?php echo $val['ppt_id']?>')">删除</a> </span><br />
                                
                           <?php }
                        
                        
                        ?>
                        
                        
                        <!--input type="hidden" name="fileurl" value="<?=$powerPointInfo[0]['ppt_url']?>"/-->
                        
                        
                        <?php } ?>
                        
                                                 
                        <div id="div1">
                        <span style="font-size: 16px; color:red;">+</span><input type="button" id="btn" value="增加上传列表" onclick="addMore();"/><br />
                        
                        
                        </div>
                        <div id="div2"></div> 
                        
                     </table> 
                       
                </tr>
                
                
                <tr></tr>              
            </table>
            <span style="padding-bottom: 15px;"></span>            
            <span style="padding-left: 200px; margin-top: 5px;"><input style="width: 60px; height: 30px;" type="submit" value="提交"></span>

		</form>
	</div>

</div>
<script type="text/javascript">
function delfile(url,id,ppt_id){    
    if(id!='' && ppt_id != '' ){
		if(confirm('确定要删除此图片吗？')){
			$.post(url,
			{id:id,ppt_id:ppt_id},
			function(data){
				var obj = eval('(' + data + ')');
				alert(obj.msg);
				if(obj.status == 1){
					document.getElementById(id).parentNode.removeChild(document.getElementById(id));
					//location.reload();
				}
			}
			);
		}
	}else{
		$("#adjust_msg").html('无法删除此图片！');
	}
    
}

</script>

