<table id="knowledge-index-treegrid" class="easyui-treegrid" data-options="
											  url: '/Vip/VipJiaoyan/getCourses?knowledgetypeid=<?php echo $knowledgeTypeId;?>',
											  method: 'get',
											  striped: true,
											  border: true,
											  fit: true,
											  idField: 'id',
											  treeField: 'name',
											  rownumbers: true,
											  onSelect: function(row) {
												  $.post('/Vip/VipJiaoyan/getPath',{id: row.id},function(path){
												  	$('#course_add_form_parent_id').val(row.id);
												  	$('#course_add_form_path').html(path);
												  });
											  }">
	<thead>
		<tr>
			<th field="name" width="350">名称</th>
		</tr>
	</thead>
</table>