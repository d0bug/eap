<div id="basic-index-question-layout" class="easyui-layout" data-options="fit: true">
		<div region="north" data-options="title:'条件选择', iconCls:'icon-search', fit: true, split: false, collapsible:false">
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="tableInfo">
			<tr>
				<td class="wd_120 alt right">年部：</td>
				<td>
					<ul id="basic-index-question-grade" class="fliter_box_select"></ul>
				</td>
			</tr>
			<tr>
				<td class="alt right">省：</td>
				<td>
					<ul id="basic-index-question-city" class="fliter_box_select"></ul>
				</td>
			</tr>
			<tr>
				<td class="alt right">市/地区：</td>
				<td>
					<ul id="basic-index-question-country" class="fliter_box_select"></ul>
				</td>
			</tr>
			<tr>
				<td class="alt right">考区：</td>
				<td>
					<ul id="basic-index-question-question-type" class="fliter_box_select"></ul>
					<a href="#" onclick="add_question_type()" class="easyui-linkbutton" title="添加" data-options="plain:true, iconCls:'icon-add'"></a>
					<a href="#" onclick="edit_question_type()" class="easyui-linkbutton" title="编辑" data-options="plain:true, iconCls:'icon-edit'"></a>
				</td>
			</tr>
			</table>
			<input type="hidden" id="basic_index_question_type_grade_id" value="">
			<input type="hidden" id="basic_index_question_type_city_id" value="">
			<input type="hidden" id="basic_index_question_type_country_id" value="">
			<input type="hidden" id="basic_index_question_type_id" value="">
		</div>
	</div>
	<!--Begin 基础属性添加/编辑对话框-->
	<div id="basic-index-test-add-dlg" class="easyui-dialog" data-options="modal:true,closed:true,buttons:'#basic-index-test-add-dlg-buttons'" style="width:500px;height:235px;padding:5px;"></div>
	<div id="basic-index-test-add-dlg-buttons">
		<a href="#" class="easyui-linkbutton" iconCls="icon-ok" onclick="test_add_save()">保存</a>
	</div>

	<!--End 基础属性添加对话框-->
	<!--Begin 基础属性编辑对话框-->
	<div id="basic-index-test-edit-dlg" class="easyui-dialog" data-options="modal:true,closed:true,buttons:'#basic-index-test-edit-dlg-buttons'" style="width:450px;height:155px;padding:5px;"></div>
	<div id="basic-index-test-edit-dlg-buttons">
		<a href="#" class="easyui-linkbutton" iconCls="icon-ok" onclick="test_add_save()">保存</a>
	</div>
	<!--End 基础属性编辑对话框-->
	<script language='javascript' type='text/javascript'>
	var _initGrade1 = function() {
		$.post('/Question/Basic/getGrades', null, function(data) {
			$('#basic-index-question-grade').html('');
			$('#basic-index-question-city').html('');
			$('#basic-index-question-country').html('');
			$('#basic-index-question-question-type').html('');
			
			var gradeId = $('#basic_index_question_type_grade_id').val();
			var index = 0;
			//data = JSON.parse(data);
			$.each(data, function(i, row) {
				var $li = $('<li></li>');
				$li.attr('value', row.id)
					.click(function() {
						$(this).addClass('active').siblings().removeClass('active');
						$('#basic_index_question_type_grade_id').val(row.id);
						_initCity1();
					})
					.html('<a href="#" title=' + row.title + '>' + row.title + '</a>');
				$('#basic-index-question-grade').append($li);
				if (gradeId == row.id) {
					index = i;
				}
			});
			$('#basic-index-question-grade li').eq(index).click();
		},'json');
	}
	var _initCity1 = function() {
		$.post('/Question/Basic/getByCity', function(data) {
			$('#basic-index-question-city').html('');
			$('#basic-index-question-question-type').html('');
			
			var subjectId = $('#basic_index_question_type_city_id').val();
			var index = 0;
			//data = JSON.parse(data);
			if (data.length > 0) {
				$.each(data, function(i, row) {
					var $li = $('<li></li>');
					$li.attr('value', row.id)
						.click(function() {
							$(this).addClass('active').siblings().removeClass('active');
							$('#basic_index_question_type_city_id').val(row.id);
							_initCountry(row.id);
						})
						.html('<a href="#" title=' + row.city + '>' + row.city + '</a>');
					$('#basic-index-question-city').append($li);
					if (subjectId == row.id) {
						index = i;
					}
				});
				$('#basic-index-question-city li').eq(index).click();
			}
			else {
				$('#basic_index_question_type_city_id').val('');
				$('#basic_index_question_type_id').val('');
			}
		},'json');
	}
	var _initCountry = function(cityId) {
		$.post('/Question/Basic/getCountryByCityId', {cityid: cityId}, function(data){
			$('#basic-index-question-country').html('');
			var countryId = $('#basic_index_question_country_id').val();
			var index = 0;
			//data = JSON.parse(data);
			if (data.length > 0) {
				$.each(data, function(i, row) {
					var $li = $('<li></li>');
					$li.attr('value', row.id)
						.click(function() {
							$(this).addClass('active').siblings().removeClass('active');
							$('#basic_index_question_type_country_id').val(row.id);
							var grade_id=$("#basic_index_question_type_grade_id").val();
							var city_id=$("#basic_index_question_type_city_id").val();
							_initTest(grade_id,city_id,row.id);
						})
						.html('<a href="#" title=' + row.city + '>' + row.city + '</a>');
					$('#basic-index-question-country').append($li);
					if (countryId == row.id) {
						index = i;
					}
				});
				$('#basic-index-question-country li').eq(index).click();
			}
			else {
				$('#basic_index_question_country_id').val('');
			}
		},'json');
	}
	var _initTest=function(gradeId,cityId,countryId)
	{
		$.post('/Question/Basic/getTestByParam', {gradeid:gradeId,cityid:cityId,countryid:countryId}, function(data){
			$('#basic-index-question-question-type').html('');
			var testId = $('#basic_index_question_type_id').val();
			var index = 0;
			//data = JSON.parse(data);
			if (data.length > 0) {
				$.each(data, function(i, row) {
					var $li = $('<li></li>');
					$li.attr('value', row.id)
						.click(function() {
							$(this).addClass('active').siblings().removeClass('active');
							$('#basic_index_question_type_id').val(row.id);
						})
						.html('<a href="#" title=' + row.test_name + '>' + row.test_name + '</a>');
					$('#basic-index-question-question-type').append($li);
					if (testId == row.id) {
						index = i;
					}
				});
				$('#basic-index-question-question-type li').eq(index).click();
			}
			else {
				$('#basic_index_question_type_id').val('');
			}
		},'json');
	}
	$(function() {
		_initGrade1();
	});

	function add_question_type(type){
		var params = {
				href: '/Question/Basic/test_add?grade_id=' + $('#basic_index_question_type_grade_id').val()+'&city_id='+$('#basic_index_question_type_city_id').val()+'&country_id='+$('#basic_index_question_type_country_id').val(),
				iconCls: 'icon-add',
				title: '添加',
				width: 650,
				height: 355
			};
		$('#basic-index-test-add-dlg').dialog(params).dialog('open');
		action = '/Question/Basic/test_add_save';
		form = 'test-add-form';
	}
	function edit_question_type() {
		id = $('#basic_index_question_type_id').val();
		if(id == '') {
			$.messager.alert('错误信息', '请选择题型!', 'error');
			return false;
		}
		$('#basic-index-test-edit-dlg').dialog({
			href: '/Question/Basic/test_edit?id=' + id,
			iconCls: 'icon-edit',
			title: '编辑'
		}).dialog('open');
		action = '/Question/Basic/test_edit_save';
		form = 'test-edit-form';
	}
	function test_add_save() {
		$('#' + form).form('submit', {
	        url: action,
	        onSubmit: function () {
	            return $(this).form('validate');
	        },
	        success: function (result) {
	        	var result = JSON.parse(result);
	            if (result.status) {
	            	_initGrade1();
					$('#basic-index-test-add-dlg, #basic-index-test-edit-dlg').dialog('close');
	            } else {
	                $.messager.alert('错误信息', '操作失败!', 'error');
	            }
	        }
	    });
    }
</script>
