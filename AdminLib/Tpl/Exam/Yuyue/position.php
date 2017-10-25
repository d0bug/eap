<div class="easyui-layout" fit="true" border="false">
	<div region="center">
		<div id="posGridToolbar">
			<a href="javascript:addPos()" class="easyui-linkbutton" plain="true" iconCls="icon-add">添加地点</a>
			<a href="javascript:editPos()" class="easyui-linkbutton" plain="true" iconCls="icon-edit">修改地点</a>
			<a href="javascript:delPos()" class="easyui-linkbutton" plain="true" iconCls="icon-cancel">删除地点</a>
		</div>
		<table id="posGrid" fit="true" border="false" rownumbers="true" singleselect="true" toolbar="#posGridToolbar">
			<thead>
				<tr>
					<th field="pos_caption">地点名称</th>
					<th field="pos_telephone">联系电话</th>
					<th field="pos_addr">详细地址</th>
				</tr>
			</thead>
		</table>
	</div>
</div>
<script type="text/javascript">
var yGroupId = '<?php echo $yGroupId?>';
jQuery(function(){
	jQuery('#posGrid').datagrid({
		url:'<?php echo $jsonPosUrl?>/ygid/<?php echo $yGroupId?>',
	    view:detailview,
	    detailFormatter:function(index,data){
	        return '<div class="epos_list epos_list_' + index + '">关联考点：<b style="color:red">' + jQuery.trim(data.pos_captions) + '</b></div>';
	    },
	    onExpandRow:function(index,data){
	        jQuery('#posGrid').datagrid('selectRow',index);
	        $('#posGrid').datagrid('fixDetailRowHeight',index); 
	        var rowCnt = jQuery('div.epos_list').length;
	        for(var i=0;i<rowCnt;i++) {
	            if(i != index) {
	                jQuery('#posGrid').datagrid('collapseRow',i);
	            }
	        }
	    }
	});
})

function addPos() {
	if(yGroupId.length != '36') {
		alert('非法操作');
		return;
	}
	var _tm = (new Date()).getTime();
	jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
	jQuery('#dlg_' + _tm).dialog({
		title:'添加诊断地点',
		width:550,
		height:400,
		modal:true,
		iconCls:'icon-add',
		href:'<?php echo $addPosUrl?>/ygid/' + yGroupId + '/dlg/dlg_' + _tm,
		onClose:function(){
			jQuery('#dlg_'+ _tm).dialog('destroy');
		}
	})
}
function editPos() {
	var _tm = (new Date()).getTime();
	jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
	var selePos = jQuery('#posGrid').datagrid('getSelected');
	if(selePos) {
		var posId = selePos.pos_id;
	} else {
		alert('请选择诊断地点');
		return;
	}
	jQuery('#dlg_' + _tm).dialog({
		title:'修改诊断地点信息',
		width:550,
		height:400,
		modal:true,
		iconCls:'icon-add',
		href:'<?php echo $editPosUrl?>/pid/' + posId + '/dlg/dlg_' + _tm, 
		onClose:function(){
			jQuery('#dlg_'+ _tm).dialog('destroy');
		}
	})
}

function delPos() {
	var selePos = jQuery('#posGrid').datagrid('getSelected');
	if(selePos) {
		if(confirm('确定要删除诊断地点“' + selePos.pos_caption + '”吗？若预约地点')) {
			jQuery.post('<?php echo $delPosUrl?>', {pos_id:selePos.pos_id}, function(data){
				if(data.errorMsg) {
					alert(data.errorMsg)
				} else {
					alert('诊断地点删除成功');
					jQuery('#posGrid').datagrid('reload');
				}
			}, 'json');
		}
	}
}
</script>