<table id="fourlevel-index-treegrid" class="easyui-treegrid" data-options="
											  url: '/Question/Basic/getFourlevels?subjectid={$subjectid}',
											  method: 'get',
											  striped: true,
											  border: true,
											  fit: true,
											  idField: 'id',
											  treeField: 'name',
											  rownumbers: true,
											  onSelect: function(row) {
												  $.post('/Question/Basic/getPathForFourlevel',{id: row.id},function(path){
												  	$('#fourlevel_add_form_parent_id').val(row.id);
												  	$('#fourlevel_add_form_path').html(path);
												  });
											  }">
	<thead>
		<tr>
			<th field="name" width="350">名称</th>
		</tr>
	</thead>
</table>