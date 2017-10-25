<div class="easyui-layout" fit="true">
	<div region="center">
		<table id="ctGrid" rownumbers="true" fit="true">
			<thead>
			<tr>
				<th field="scode" checkbox="true"></th>
				<th field="deptname">部门名称</th>
				<th field="prjname">部门名称</th>
				<th field="code" formatter="ctCode">班型编码</th>
				<th field="sname">班型名称</th>
			</tr>
			</thead>
		</table>
	</div>
</div>
<script type="text/javascript">
	var ctCodes = [];
	jQuery(function(){
		var options = jQuery('#<?php echo $valId?>')[0].options;
		ctCodes = [];
		for(var i=0;i<options.length;i++) {
			options[i].selected = true;
			ctCodes.push(options[i].value);
		}
		jQuery('#ctGrid').datagrid({
			url:'<?php echo $url?>',
			queryParams:{formData:jQuery('#<?php echo $form?>').serialize()},
			onLoadSuccess:function(data){
				var ctCodeStr = ctCodes.join(',');
				var rows = jQuery('#ctGrid').datagrid('getRows');
				for(var i=0;i<rows.length;i++) {
					var re = new RegExp(rows[i].scode);
					if(re.test(ctCodeStr)) {
						jQuery('#ctGrid').datagrid('checkRow', i);
					}
				}
			},
			onCheck:setCtVal,
			onUncheck:setCtVal,
			onCheckAll:setCtVal,
			onUncheckAll:setCtVal
		})
	})
	function setCtVal() {
		var rows = jQuery('#ctGrid').datagrid('getChecked');
		var oSelect = jQuery('#<?php echo $valId?>')[0];
		oSelect.length = 0;
		for(var i=0;i<rows.length;i++) {
			jQuery('<option value="' + rows[i].scode + '" selected="true">[' + rows[i].scode + ']' + rows[i].sname + '</option>').appendTo('#<?php echo $valId?>');
		}
	}
	
	function ctCode(val, data) {
		return data.scode;
	}
</script>