<div class="easyui-layout" fit="true">
<div region="center">
<table id="sbjGrid" rownumbers="true" fit="true">
	<thead>
		<tr>
			<th field="scode" checkbox="true"></th>
			<th field="sbjCode" formatter="sbjCode">部门编码</th>
			<th field="sname">部门名称</th>
		</tr>
	</thead>
</table>
</div>
</div>
<script type="text/javascript">
	function sbjCode(val, data) {
		return data.scode;
	}
	
	jQuery('#sbjGrid').datagrid({
		url:'<?php echo $url?>',
		onLoadSuccess:function(data){
			var sbjCodes = jQuery.trim(jQuery('#<?php echo $valId?>').val());
			var rows =jQuery('#sbjGrid').datagrid('getRows');
			for(var i=0;i<rows.length;i++) {
				var reg = new RegExp(rows[i].scode);
				if(reg.test(sbjCodes)) {
					jQuery('#sbjGrid').datagrid('checkRow', i);
				}
			}
		},
		onCheck:setSbjVal,
		onUncheck:setSbjVal,
		onCheckAll:setSbjVal,
		onUncheckAll:setSbjVal
	})
	function setSbjVal(){
		var rows = jQuery('#sbjGrid').datagrid('getChecked');
			var codeAr = [];
			var nameAr = [];
			for(var i=0;i<rows.length;i++) {
				codeAr.push(rows[i].scode);
				nameAr.push(rows[i].sname);
			}
			jQuery('#<?php echo $nameId?>').val(nameAr.join(','));
			jQuery('#<?php echo $valId?>').val(codeAr.join(','));
	}
</script>