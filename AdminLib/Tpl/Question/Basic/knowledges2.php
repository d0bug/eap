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
												  $('#single_knowledge_id').val(row.id);
												  $('#single_knowledge_path').html(row.name);
											  }">
	<thead>
		<tr>
			<th field="name" width="350">名称</th>
		</tr>
	</thead>
</table>