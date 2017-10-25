<div class="easyui-layout" fit="true">
	<div region="center">
		<table id="prjGrid" rownumbers="true" fit="true">
			<thead>
			<tr>
				<th field="scode" checkbox="true"></th>
				<th field="deptname">部门名称</th>
				<th field="pcode" formatter="prjCode">项目编码</th>
				<th field="sname">项目名称</th>
			</tr>
			</thead>
		</table>
	</div>
</div>
<script type="text/javascript">
	var prjCodes = [];
	jQuery(function(){
		var options = jQuery('#<?php echo $valId?>')[0].options;
		prjCodes = [];
		for(var i=0;i<options.length;i++) {
			options[i].selected = true;
			prjCodes.push(options[i].value);
		}
		jQuery('#prjGrid').datagrid({
			url:'<?php echo $url?>',
			queryParams:{formData:jQuery('#<?php echo $form?>').serialize()},
			onLoadSuccess:function(data){
				var prjCodeStr = prjCodes.join(',');
				var rows = jQuery('#prjGrid').datagrid('getRows');
				for(var i=0;i<rows.length;i++) {
					var re = new RegExp(rows[i].scode);
					if(re.test(prjCodeStr)) {
						jQuery('#prjGrid').datagrid('checkRow', i);
					}
				}
			},
			onCheck:setPrjVal,
			onUncheck:setPrjVal,
			onCheckAll:setPrjVal,
			onUncheckAll:setPrjVal
		})
	})
	function setPrjVal() {
		var rows = jQuery('#prjGrid').datagrid('getChecked');
		var oSelect = jQuery('#<?php echo $valId?>')[0];
		oSelect.length = 0;
		for(var i=0;i<rows.length;i++) {
			jQuery('<option value="' + rows[i].scode + '" selected="true">[' + rows[i].scode + ']' + rows[i].sname + '</option>').appendTo('#<?php echo $valId?>');
		}
		
	}
	
	function prjCode(val, data) {
		return data.scode;
	}
</script>