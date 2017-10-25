<div id="setPositionLayout" class="easyui-layout" data-options="fit:true">  
    <div data-options="region:'west',split:true,border:false,iconCls:'icon-tip'" title="考点列表" style="width:540px">

        <table id="examPosListGrid"> 
        	<thead data-options="frozen:true">  
                <tr>    
 					<th field='pos_caption' fixed=true >考点名称</th>  
                    <th data-options="field:'pos_code',width:60">考点标识</th>
            		<th field='status' formatter='formatStatus'>状态</th>  
                  </tr>
               </thead>

            <thead>  
                <tr>  
                    <th field='pos_code_pre'>考号前缀</th>
                    <th field='pos_room_count'>考场数</th> 
                    <th field='pos_total_count'>总容纳</th>
                    <th field='stu_count'>已报</th>
                    <th field='stu_free_cnt' formatter="freeCnt">剩余</th>
                      
                    <th field='is_show_num'  formatter='formatSwitch'>显示场号</th>  
                    <th field='is_show_caption'  formatter='formatSwitch'>显示名称</th>  
                    
                </tr>  
            </thead>  
        </table>  
        
        
        <div id="examPosToolbar">
                 <a class="easyui-linkbutton" href="javascript:addPosition(curExamId, curExamCaption)" plain="true" iconCls="icon-add">添加考点</a>
            	
            	<a class="easyui-linkbutton" href="javascript:delPosition(curExamId)" plain="true" iconCls="icon-cancel">删除考点</a>
        </div>
        
    </div>  
    
    
    <div data-options="region:'center',border:false,iconCls:'icon-tip'" title="考点细则"></div>  
</div>  




<script>
	
function formatSwitch(val, roow){
	if('1' == val){
		return '<span style="color:green">开启</span>';
	}else{
		return '<span style="color:red">关闭</span>';
	}	
}	
	
function formatStatus(val, row){
	if('1' == val){
		return '<span style="color:green">正常</span>';
	}else{
		return '<span style="color:red">关闭</span>';
	}
}
	
	
function loadExamPosition(){
	jQuery('#examPosListGrid').datagrid({ 
		url:"<?php echo $jsonExamPositionUrl?>",
		queryParams:{exam_id:curExamId},
		rownumbers:false,
		singleSelect:true,
		toolbar:'#examPosToolbar',
		onSelect:function(idx,data) {
		   setExamPositionDetail(data.id, data.pos_caption);  
		}
	})
}
loadExamPosition();

function setExamPositionDetail(rid, pos_caption){

	jQuery('#setPositionLayout').layout('remove', 'center');
	
	jQuery('#setPositionLayout').layout('add', {
		region:'center',
		title:'考点细则 - ' + pos_caption,
		width:320,
		split:true,
		border:true,
		iconCls:'icon-tip',
		collapsible:false,
		href:'<?php echo $editExamPositionUrl?>/id/' + rid,
		//tools:tools
	});
}


function addPosition(examId, examCaption){

/*	jQuery('#windows').html('');
	var time = (new Date()).getTime();
	time = 1;
	jQuery('<div id="win_' + time + '"></div>').appendTo('#windows');

	
 */
 
 var title="选取考点 - " + examCaption ;
	var iconCls = 'icon-edit';
 var time = 1;
 
	jQuery('#windows').dialog({
		title:title,
		collapsible:false,
		maximizable:true,
		width:740,
		height:520,
		content:'<iframe scrolling="no" frameborder="no" style="width:100%;height:99.8%;margin:-1px" src="<?php echo $addExamPositionUrl?>/id/' + examId + '/dlg/' + time + '"></iframe>',
		iconCls:iconCls,
		modal:true,
		onClose:function(){
			loadExamPosition();
		}
	});
}

function delPosition(examId){
	
	var row = $("#examPosListGrid").datagrid('getSelected');
 
	if(row !== null){
		$.messager.confirm('系统提示', '确定要删除考点么？', function(rs){
			if (rs){
				
				$.ajax({
							url : '<?php echo $delExamPositionUrl;?>',
							type : 'POST',
							data: {id:row.id},
							dataType : 'json',
							success: function(rs){
								if(rs.error){
									$.messager.alert('系统信息', rs.message);	
								}else{
									$("#examPosListGrid").datagrid('deleteRow',$("#examPosListGrid").datagrid('getRowIndex',row));
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
		});		
	}else{
		$.messager.alert('系统提示','请选择一个考点记录!');
	}
}

function freeCnt(val, data) {
	return '<span style="color:blue">' + (data.pos_total_count - data.stu_count) + '</span>';
}
</script>