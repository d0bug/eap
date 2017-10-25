<input class="easyui-searchbox" data-options="prompt:'请输入知识点名称...', searcher: do_search" style="width:405px" />
<table id="knowledge-index-treegrid" class="easyui-treegrid" style="height: 222px" data-options="
											  method: 'get',
											  striped: true,
											  border: true,
											  idField: 'id',
											  treeField: 'name',
											  rownumbers: true,
											  onSelect: do_select">
	<thead>
		<tr>
			<th field="name" width="350">名称</th>
		</tr>
	</thead>
</table>
<script language='javascript' type='text/javascript'>
	$(function() {
		$('#knowledge-index-treegrid').treegrid({
			url: '/Question/Basic/getKnowledges?coursetypeid={$coursetypeid}'
		});
	});
	function do_search(val) {
		var url = '/Question/Basic/getKnowledgesSearch?coursetypeid={$coursetypeid}&kw=' + val;
		if (val == '')
			url = '/Question/Basic/getKnowledges?coursetypeid={$coursetypeid}';
		$('#knowledge-index-treegrid').treegrid({
			url: url
		});
	}
    function do_select(row) {
        var len = $('#sub_knowledge_names li').length;
        var ids = $('#sub_knowledge_ids').val();
        ids = (ids == '') ? row.id : ids + ',' + row.id;
		$('#sub_knowledge_ids').val(ids);
		$('#sub_knowledge_names').append('<li onclick="remove_item(' + len + ')" title="点击删除" style="cursor: pointer;" id="' + (len) + '"><a href="#" style="display: block;"><em>' + (len + 1) + '</em>．' + row.name + '</a></li>');
	}
	function remove_item(id) {
		$.each($('#sub_knowledge_names li'),
	            function(n, v) {
	        		if (v.id == id) {
	        			var strs = $('#sub_knowledge_ids').val();
	        			var ids = strs.split(',');
	        			ids.splice(n, 1);
	        			$('#sub_knowledge_ids').val(ids.join(','));
		        		$(v).remove();
		        		return;
	        		}
				}
			);
		re_bind_index();
	}
	function re_bind_index() {
		$.each($('#sub_knowledge_names li'), 
            function(n, v) {
        		$(v).find('em').html(n + 1);
			}
		);
	}
</script>