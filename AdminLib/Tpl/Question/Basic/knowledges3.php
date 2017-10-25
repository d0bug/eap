<div  class="easyui-layout"  data-options="fit:true" >
	<div region="north" style="height:60px;padding:0px;margin:0px;" data-options="title:'请选择属性', iconCls:'icon-search', split: false,fit:false">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="tableInfo" >
			<tr>
				<td class="wd_80 alt left">课程类型：</td>
				<td class="left">
					<ul id="basic-index-knowledge-course-type" class="fliter_box_select">
					<?php foreach ($basecoursetypes as $key=>$courseType):?>
						<li id="ctype_<?php echo $key?>" <?php if($key==0):?>class="active"<?php endif;?> onclick="reloadKnowledge('ctype_<?php echo $key?>','<?php echo $courseType['id']?>')"><a href="#" title="<?php echo $courseType['title']?>"><?php echo $courseType['title']?></a></li>&nbsp;&nbsp;&nbsp;&nbsp;
					<?php endforeach;?>
					</ul>
				</td>
			</tr>
		</table>
		<input type="hidden" id="subject_id" name="subject_id" value="{$subjectid}">
		<input type="hidden" id="course_type_id" name="course_type_id" value="">
		<input type="hidden" id="is_gaosi" name="is_gaosi" value="0">
	</div>
	<div region="center"  data-options="title:'知识点', iconCls:'icon-table', split: true,fit:true" id="match_knowledge">
		<table id="knowledge-index-treegrid" class="easyui-treegrid" data-options="
													  method: 'get',
													  striped: true,
													  border: true,
													  toolbar: '#knowledge-index-treegrid-toolbar',
													  fit: false,
													  idField: 'id',
													  treeField: 'name',
													  rownumbers: true,
													  onSelect: function(row) {
													  	if(row.is_leaf==1 || row.level==5){
													  		$('#origin_knowledge_id').val(row.id);
														  	$('#name').val(row.name);
														  	$('#knowledge_name').html(row.name);
														  	$('#remark').val(row.remark);
														  	$('#analysis').val(row.analysis);
													  	}
														  
													  }">
			<thead>
				<tr>
					<th field="name" width="350">名称</th>
				</tr>
			</thead>
		</table>
		<div id="knowledge-index-treegrid-toolbar">
			<input type="text" id="keyword" name="keyword" value="" placeholder="请输入知识点名称">
			<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-search'" onclick="knowledge_search()">搜索</a>
		</div>
	</div>

</div>

<script type="text/javascript">
var _initCourseType = function() {
	var subjectId = $('#subject_id').val();
	var knowledgeTypeId = $('#knowledge_type_id').val();
	$.post('/Question/Basic/getCourseTypesBySubjectId2', {subjectid:subjectId,is_gaosi:1 }, function(data) {
		$('#basic-index-knowledge-course-type').html('');
		var index = 0;
		data = JSON.parse(data);
		if (data.length > 0) {
			$.each(data, function(i, row) {
				var $li = $('<li></li>');
				$li.attr('value', row.id)
				.click(function() {
					$(this).addClass('active').siblings().removeClass('active');
					$('#course_type_id').val(row.id);
					_initKnowledge(row.id);
				})
				.html('<a href="#" title=' + row.title + '>' + row.title + '</a>');
				$('#basic-index-knowledge-course-type').append($li);
				if (subjectId == row.id) {
					index = i;
				}
			});
			$('#basic-index-knowledge-course-type li').eq(index).click();
		}else {
			$('#course_type_id').val('');
			_initKnowledge('');
		}
	});
}
function _initKnowledge(courseTypeId) {
	var $grid = $('#knowledge-index-treegrid');
	var params = $grid.treegrid('options').queryParams;
	params.id = '';
	params.coursetypeid = courseTypeId;
	params.knowledgetypeid = $('#knowledge_type_id').val();
	params.keyword = $('#keyword').val();
	$grid.treegrid({
		url: '/Question/Basic/getKnowledges',
		onClickRow:function(row){
		},
		onExpand:function(row){
			//alert(row.id);
			//alert(row.level);
		}
	});
	
}

function _initKnowledge2(courseTypeId,keyword) {
	var $grid = $('#knowledge-index-treegrid');
	var params = $grid.treegrid('options').queryParams;
	params.id = '';
	params.coursetypeid = courseTypeId;
	params.knowledgetypeid = $('#knowledge_type_id').val();
	params.kw = keyword;
	$grid.treegrid({
		url: '/Question/Basic/getKnowledgesSearch3',
		onClickRow:function(row){
		},
		onExpand:function(row){
			//alert(row.id);
			//alert(row.level);
		}
	});
	
}

$(function() {
	_initCourseType();
	
});

function reloadKnowledge(id,courseTypeId){
	$('#'+id).addClass('active').siblings().removeClass('active');
	var $grid = $('#knowledge-index-treegrid');
	var params = $grid.treegrid('options').queryParams;
	params.coursetypeid = courseTypeId;

	$grid.treegrid({url: '/Question/Basic/getKnowledges'});
}

function knowledge_search(){
	var keyword = $('#keyword').val();
	var course_type_id = $('#course_type_id').val();
	_initKnowledge2(course_type_id,keyword);
}
</script>
