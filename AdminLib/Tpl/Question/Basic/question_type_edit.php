<form id="questiontype-edit-form" method="post" novalidate>
	<input type="hidden" name="id" value="{$questiontype['id']}" />
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
		<tr>
			<td class="alt right wd_120">别名：</td>
			<td>
				<input type="text" name="title" value="{$questiontype['title']}" class="input wd_150" autocomplete="off" maxlength="100" />
			</td>
		</tr>
		<tr>
			<td class="alt right"><span class="red">*</span> 排序：</td>
			<td>
				<input type="text" name="sort" value="{$questiontype['sort']}" class="easyui-numberbox input wd_50" data-options="required: true, min: 1, precision: 0" autocomplete="off" maxlength="6" />
			</td>
		</tr>
	</table>
</form>