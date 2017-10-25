<form id="test-add-form" method="post" novalidate>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr>
			<td class="alt right"><span class="red">*</span> 年部：</td>
			<td>
				<input type="text" name="grade_id" style="width:205px;" class="easyui-combobox" data-options="required: true, url: '/Question/Basic/getComboboxData?cate=GRADE_DEPT', value:{$gradeId}, valueField: 'grade_id', textField: 'title'" />
			</td>
		</tr>
		<tr>
			<td class="alt right"><span class="red">*</span> 省：</td>
			<td>
				<input type="text" id="city_id" name="city_id"  style="width:205px;" class="easyui-combobox" data-options="required: true, url: '/Question/Basic/getByCity', value:'{$cityId}' ,valueField: 'id',textField: 'city',onSelect: function(row) {$('#country_id').combobox({value:'',url: '/Question/Basic/getCountryNameByCityId?cityid=' + row.id});}" />
			</td>
		</tr>
		<tr>
			<td class="alt right"><span class="red">*</span> 市/地区：</td>
			<td>
				<input type="text" id="country_id" name="country_id"  style="width:205px;" class="easyui-combobox" data-options="required: true, url: '/Question/Basic/getCountryNameByCityId?cityid={$cityId}', value:'{$countryId}' ,valueField: 'id',textField: 'city'" />
			</td>
		</tr>
		<tr>
			<td class="alt right wd_120"><span class="red">*</span> 考区名称：</td>
			<td>
				<input type="text" name="test_name" id="test-add-form-title" value="{$dict['test_name']}" class="easyui-validatebox input wd_150" data-options="required: true" autocomplete="off" maxlength="50" />
			</td>
		</tr>
		<tr>
			<td class="alt right"><span class="red">*</span> 排序：</td>
			<td>
				<input type="text" name="sort" value="{$dict['sort']}" class="easyui-numberbox input wd_50" data-options="required: true, min: 1, precision: 0" autocomplete="off" maxlength="6" />
			</td>
		</tr>
	</table>
</form>
<script language='javascript' type='text/javascript'>
	$(function() {
		$('#test-add-form-title').focus();
	});
</script>