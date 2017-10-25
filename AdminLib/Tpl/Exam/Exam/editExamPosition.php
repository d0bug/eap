<style type="text/css" media="screen">
	#epDiv{ padding:10px;}
	#editExamPositionForm table th{text-align: right}
	.room_div{ height:200px; overflow:auto;}
	.room_c{ width:60px;}
	#room_list tr td{text-align:center}
	#room_list tr th{text-align:center}
</style>

<div id="epDiv">
<form id="editExamPositionForm" method="post">
    <table>
        <tr>
            <th>考点名称:</th>
            <td><?php echo $pos_caption;?></td>
        </tr>
        
        <tr>
            <th>考点标识:</th>
            <td><?php  echo $pos_code;?></td>
        </tr>
        
        <tr>
            <th>状态:</th>
            <td><select name="status" id="status"   onchange="" size="1">
            	<option value="1" style="color:green" <?php if('1' == $status){echo  'selected="true"';};?>>开启</option>
            	<option value="0" style="color:red" <?php if('0' == $status){echo  'selected="true"';};?>>关闭</option>
            </select></td>
        </tr>
 
        
        <tr>
            <th>准考证号前缀:</th>
            <td><input class="easyui-validatebox" type="text" name="pos_code_pre" id="pos_code_pre" data-options="required:true" value="<?php echo $pos_code_pre;?>"></input></td>
        </tr>
        
        
        <tr>
            <th>显示考场编号:</th>
            <td>
            <select name="is_show_num" id="is_show_num"   onchange="" size="1">
            	<option value="1" style="color:green" <?php if('1' == $is_show_num){echo  'selected="true"';};?>>是</option>
            	<option value="0" style="color:red" <?php if('0' == $is_show_num){echo  'selected="true"';};?>>否</option>
            </select>
             </td>
        </tr>
        	
        <tr>
            <th>显示教室名称:</th>
            <td>
            <select name="is_show_caption" id="is_show_caption"   onchange="" size="1">
            	<option value="1" style="color:green" <?php if('1' == $is_show_caption){echo  'selected="true"';};?>>是</option>
            	<option value="0" style="color:red" <?php if('0' == $is_show_caption){echo  'selected="true"';};?>>否</option>
            </select>
            </td>
        </tr>

        <tr>
            <th>总容纳数:</th>
            <td id="total_count"><?php echo $pos_total_count;?></td>
        </tr>
        	
 
        <tr>
            <th>考场数:</th>
            <td><input id="pos_room_count" class="easyui-validatebox" type="text" name="pos_room_count" data-options="required:true" value="<?php echo $pos_room_count;?>" onchange="setRoom(this);"></input></td>
        </tr>
        	
        <tr>
        	 
        	<td colspan="2" align="center">
        		<div id="room_div">
 					
        		</div>
        	</td>
        </tr>
        
       	<tr>
 			<td colspan="2">
 				 <a href="javascript:void(0)" class="easyui-linkbutton" onclick="saveRoomInfo()" iconcls="icon-ok">保存</a>
            	
 			</td>
       	</tr>
    </table>
    <input id="id" class="" name="id" type="hidden" value="<?php echo $id;?>" />
</form>

</div>
	
<script type="text/javascript" charset="utf-8">

initRoom();
function initRoom(){
	<?php if($room_num_setting):?>
	var room_num = <?php echo $room_num_setting;?>;
	var room_name = <?php echo $room_name_setting;?>
	
	var roomList = '<table  id="room_list" class="" border="1" width="250"><tr><th>考场</th> <th>名称</th> <th>容量</th></tr>';
	var n = 1;
	for (var i in room_num) {
 			var m = n < 10 ? ('0' + n) : n;
 			roomList += '<tr id="tr_' + n + '"><td>' + m + '</td> <td><input class="room_c room_caption" type="text" name="room_caption['+ n +']" value="'+ room_name[i] +'" /> </td> <td><input class="room_c" type="text" name="room_num['+ n +']" value="'+ room_num[i] +'" /> </td></tr>';
 			n++;
 		};
 	roomList += '</table>';
 	$("#room_div").html(roomList);
 	
	<?php endif?>
}

function setRoom(obj){
	
	var roomDiv = $.trim($("#room_div").html());
	var roomNum = parseInt($(obj).val(), 10);
	
 
	if('' == roomDiv){
		
		var roomList = '<table id="room_list" class="" border="1" width="250"><tr><th>考场</th> <th>名称</th> <th>容量</th></tr>';
	 	for (var i=1; i <= roomNum; i++) {
	 			var n = i < 10 ? ('0' + i) : i;
	 			roomList += '<tr id="tr_' + i + '"><td>' + n + '</td> <td><input class="room_c room_caption" type="text" name="room_caption['+ i +']" /> </td> <td><input class="room_c" type="text" name="room_num['+ i +']" /> </td></tr>';
	 		};
	 	roomList += '</table>';
	 	$("#room_div").html(roomList);	
	}else{
		var existRoomNum = $(".room_caption").length;
		if(existRoomNum > roomNum){
			for(var i = roomNum+1; i <= existRoomNum; i++){
				$("#tr_" + i).remove();
			}
		}else{
			for(var i = existRoomNum+1; i <= roomNum; i++){
				var n = i < 10 ? ('0' + i) : i;
	 			var roomList  = '<tr id="tr_' + i + '"><td>' + n + '</td> <td><input class="room_c room_caption" type="text" name="room_caption['+ i +']" /> </td> <td><input class="room_c" type="text" name="room_num['+ i +']" /> </td></tr>';
				 $("#room_list").append(roomList);
			}
		}
	}
	

}

function saveRoomInfo(){
	
	
	if( '' == $.trim($('#pos_code_pre').val()) ){
		alert('准考证号前缀 不能为空');
		$('#pos_code_pre').focus();
		return false;
	}

	
	var formData = $("#editExamPositionForm").serialize();
 
	$.ajax({
				url : '<?php echo $saveRoomSettingUrl?>',
				type : 'POST',
				data: formData,
				dataType : 'json',
				success: function(rs){
					
					if(rs.error){
						$.messager.alert('系统信息', rs.message);	
					}else{
						$("#examPosListGrid").datagrid('reload');
						$("#total_count").html(rs['data']['total']);
					}
				},
				error:	function(a, b, c){
							$.messager.progress('close');
	                        $.messager.alert('系统提示','发送请求失败');
	                   },
				beforeSend: function(){
							$.messager.progress({'title':'系统提示', 'msg': '', 'text':'处理中,请稍后...'});
	                   },
				complete: function(){
	                       $.messager.progress('close');
	                   }
				
			});
}




</script>