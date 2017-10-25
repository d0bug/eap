<table id="knowledge-index-treegrid" class="easyui-treegrid" data-options="
											  url: '/Question/Basic/getKnowledges?coursetypeid={$coursetypeid}',
											  method: 'get',
											  striped: true,
											  border: true,
											  fit: true,
											  idField: 'id',
											  treeField: 'name',
											  rownumbers: true,
											  onSelect: function(row) {
												  $.post('/Question/Basic/getPath',{id: row.id},function(path){
												  	$('#knowledge_add_form_parent_id').val(row.id);
												  	$('#knowledge_add_form_path').html(path);
												  });
											  }">
	<thead>
		<tr>
			<th field="name" width="350">名称</th>
		</tr>
	</thead>
</table>