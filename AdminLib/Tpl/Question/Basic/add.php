<form id="dict-add-form" method="post" novalidate>
	<input type="hidden" name="category" value="{$cate}" />
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
	<if condition="($cate eq 'SUBJECT') OR ($cate eq 'COURSE_TYPE') OR ($cate eq 'KNOWLEDGE_TYPE')">
		<tr>
			<td class="alt right"><span class="red">*</span> 年部：</td>
			<td>
				<input type="text" name="grade_id" style="width:205px;" class="easyui-combobox" data-options="required: true, url: '/Question/Basic/getComboboxData?cate=GRADE_DEPT', value:{$gradeId}, valueField: 'grade_id', textField: 'title', onSelect: function(row) {$('#subject_id').combobox({value:'',url: '/Question/Basic/getComboboxData?cate=SUBJECT&grade_id=' + row.grade_id});}" />
			</td>
		</tr>
	</if>
	<if condition="($cate eq 'COURSE_TYPE') OR ($cate eq 'QUESTION_TYPE') OR ($cate eq 'KNOWLEDGE_TYPE')">
		<tr>
			<td class="alt right"><span class="red">*</span> 学科：</td>
			<td>
				<input type="text" id="subject_id" name="subject_id" style="width:205px;" class="easyui-combobox" data-options="required: true, url: '/Question/Basic/getComboboxData?cate=SUBJECT&grade_id={$gradeId}', value:{$subjectId}, valueField: 'subject_id',textField: 'title'" />
			</td>
		</tr>
	</if>
	<if condition="($cate eq 'COURSE_TYPE') ">
		<tr>
			<td class="alt right"><span class="red">*</span> 知识点版本：</td>
			<td>
				<input type="text" id="knowledge_type_id" name="knowledge_type_id" style="width:205px;" class="easyui-combobox" data-options="required: true, url: '/Question/Basic/getComboboxData?cate=KNOWLEDGE_TYPE&subject_id={$subjectId}', value:{$knowledgeTypeId}, valueField: 'knowledge_type_id',textField: 'title'" />
			</td>
		</tr>
	</if>
		<tr>
			<td class="alt right wd_120"><span class="red">*</span> 名称：</td>
			<td>
				<input type="text" name="title" id="dict-add-form-title" value="{$dict['title']}" class="easyui-validatebox input wd_150" data-options="required: true" autocomplete="off" maxlength="50" />
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
		$('#dict-add-form-title').focus();
	});
</script>