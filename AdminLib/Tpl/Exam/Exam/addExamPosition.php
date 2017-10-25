<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
	  </head>
    <body class="easyui-layout" fit="true" border="false">
        <div region="center"   data-options="iconCls:'icon-tip'">
                
        <table id="posListGrid">  
            <thead>  
                <tr>  
                    <th field="ck" checkbox="true"></th>  
                    <th data-options="field:'pos_code',width:80">考点标识</th>  
 					<th field='pos_caption'>考点名称</th>  
                    <th field='pos_addr'>考点地址</th>  
                    <th field='pos_telephone'>联系电话</th>  
                </tr>  
            </thead>  
        </table>  
        
        <div id="posToolbar">
        	<a class="easyui-linkbutton" href="javascript:addPosition()" plain="true" iconCls="icon-ok">确定</a>
        </div>
       
 </div>
 

<script>
function loadPosition(){
	jQuery('#posListGrid').datagrid({ 
		url:"<?php echo $jsonPositionUrl?>",
		queryParams:{exam_id:'<?php echo $_GET['id']?>'},
		rownumbers:false,
		singleSelect:false,
		CheckOnSelect:true,
		SelectOnCheck:true,
		toolbar:'#posToolbar',
		onSelect:function(idx,data) {
		   //setExamPosition(data.exam_id, data.exam_caption);  
		}
	})
}
loadPosition();

function addPosition(){
	var posList = jQuery('#posListGrid').datagrid('getSelections');
	
	var posIdArray = new Array;
	for(var i in posList){
		posIdArray.push(posList[i]['pos_id']);
	}
	
	var posIdList = posIdArray.join(",");
 
	
	$.ajax({
		url : '<?php echo $saveExamPositionUrl;?>',
		type : 'POST',
		data: {posIds:posIdList, examId:'<?php echo $examId;?>'},
		dataType : 'json',
		success: function(rs){
			$.messager.progress('close');
			if(rs.error){
				$.messager.alert('系统信息', rs.message);	
				
			}else{
				parent.closeDialog();
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
    </body>
</html>