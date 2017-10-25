<!doctype html>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<link href="/static/css/question.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/static/js/question.js"></script>
</head>
<body >
<!--Begin 套卷列表-->
<div id="question-list-paper-dlg" data-options="modal:true" title="套卷"style="padding: 5px">
<table>
  <tr>
  	<td valign="top"><a href="#" class="easyui-linkbutton" style="margin-right:10px;" data-options="iconCls:'icon-edit'" style="float:left" onclick="show_paper_edit()">编辑</a></td>
    <td valign="top"><input class="easyui-combobox" style="width: 130px;" id="grade" data-options="valueField:'id', textField:'title', method: 'get', editable: false, panelHeight:'auto'"></td>
    <td valign="top"><input class="easyui-combobox" style="width: 130px;" id="subject" data-options="url: '/Question/Basic/getSubjectsFullByGradeId', valueField:'id', textField:'title', method: 'get', editable: false, panelHeight:'auto'"></td>
      <td valign="top"><input class="easyui-combobox" style="width: 130px;" id="source" data-options="url: '/Question/Knowledge/getTypeName', valueField:'id', textField:'name', method: 'get', editable: false, panelHeight:'auto'"></td>

	  <td valign="top"><input class="easyui-combobox" style="width: 130px;" id="city" data-options="valueField:'id', textField:'city', method: 'get', editable: false, panelHeight:'auto'"></td>
	  <td valign="top"><input class="easyui-combobox" style="width: 130px;" id="country" data-options="url: '/Question/Basic/getCountryByCityId', valueField:'id', textField:'city', method: 'get', editable: false, panelHeight:'auto'"></td>
      <td valign="top"><input class="easyui-combobox" id="years" data-options="valueField:'id',textField:'time', method: 'get', editable: false, panelHeight:'auto'" name="years"> </td>

    <td><input class="easyui-searchbox" data-options="prompt:'请输入套卷名称...', searcher: do_search_paper" style="width:500px" /></td>
  </tr>
</table>
<table class="easyui-datagrid" id="paper_detail_list" data-options="url: '/Question/Knowledge/getPapersDetail',
											  method: 'get',
											  striped: true,
											  singleSelect: true,
											  height: 800,
											  idField: 'id',
											  sortName: 'created_time',
											  sortOrder: 'desc',
											  pagination: true,
											  rownumbers: true">

	<thead>
		<tr>
			<th field="grade_name" width="100">年部</th>
			<th field="subject_name" width="100">学科</th>
			<th field="file_name" sortable="true" width="990">名称(排序)</th>
			<th field="created_time" sortable="true" width="150">导入时间(排序)</th>
			<th field="last_updated_time" sortable="true" width="150">最后修改时间(排序)</th>
			<th field="last_updated_user_name" width="100">最后修改人</th>
			<th field="question_count" width="80">试题数</th>
            <th field="tishu" >符合条件的题数</th>
            <th field="taojuanshu" >符合条件的套卷数</th>
		</tr>
	</thead>
</table>
</div>
<!--Begin 套卷编辑-->
<div id="show-paper-dlg" class="easyui-dialog" data-options="modal:true,closed:true,buttons:'#show-paper-dlg-buttons'" style="width:600px;height:580px;padding:5px;"></div>
<div id="show-paper-dlg-buttons">
	<a href="#" class="easyui-linkbutton" iconCls="icon-ok" onclick="show_paper_save()">保存</a>
</div>
<!--End 套卷编辑-->
<script language='javascript' type='text/javascript'>
	$(function() {
		$('#grade').combobox({
			url: '/Question/Basic/getGradesFull',
			onSelect: function(node) {
				var $grid = $('#paper_detail_list');
				var params = $grid.datagrid('options').queryParams;
				params.grade_id = node.id;
				params.subject_id = '';
				$grid.datagrid({ url: '/Question/Knowledge/getPapersDetail', pageNumber: 1 });

				$('#subject').combobox({
					url: '/Question/Basic/getSubjectsFullByGradeId?gradeid=' + node.id,
					onSelect: function(subject) {
						var $grid = $('#paper_detail_list');
						var params = $grid.datagrid('options').queryParams;
						params.subject_id = subject.id;
                        $grid.datagrid({ url: '/Question/Knowledge/getPapersDetail', pageNumber: 1 });

                           $('#source').combobox({
                                url: '/Question/Knowledge/getTypeName?grade_id=' + node.id + '&subject_id=' + params.subject_id,
                                onSelect: function (source) {
                                    var $grid = $('#paper_detail_list');
                                    var params = $grid.datagrid('options').queryParams;
                                    params.source_id = source.id;
                                    $grid.datagrid({url: '/Question/Knowledge/getPapersDetail', pageNumber: 1});
                                }
                            });

					}
				});

                $('#source').combobox({
                    url: '/Question/Knowledge/getTypeName?grade_id=' + node.id ,
                    onSelect: function (node) {
                        var $grid = $('#paper_detail_list');
                        var params = $grid.datagrid('options').queryParams;
                        params.source_id = node.id;
                        $grid.datagrid({url: '/Ques' +
                        'tion/Knowledge/getPapersDetail', pageNumber: 1});
                    }
                });



			}
		});
	});

    $(function() {
        $('#city').combobox({
            url: '/Question/Basic/getByCity',
            onSelect: function(node) {
                var $cityid = $('#paper_detail_list');
                var params = $cityid.datagrid('options').queryParams;
                params.city_id = node.id;
                params.country_id = '';
                $cityid.datagrid({ url: '/Question/Knowledge/getPapersDetail', pageNumber: 1 });

                $('#country').combobox({
                    url: '/Question/Basic/getCountryByCityId?cityid=' + node.id,
                    onSelect: function(node) {
                        var $contryid = $('#paper_detail_list');
                        var params = $contryid.datagrid('options').queryParams;
                        params.country_id = node.id;
                        $cityid.datagrid({ url: '/Question/Knowledge/getPapersDetail', pageNumber: 1 });
                    }
                });
            }
        });
    });

    $(function() {
        $('#years').combobox({
            url: '/Question/Knowledge/getSearchYear',
            onSelect: function(node) {
                var $timeid = $('#paper_detail_list');
                var params = $timeid.datagrid('options').queryParams;
                params.se_time = node.time;
                $timeid.datagrid({ url: '/Question/Knowledge/getPapersDetail', pageNumber: 1 });
            }
        });
    });

    function do_search_paper(val) {
    	var $grid = $('#paper_detail_list');
		var params = $grid.datagrid('options').queryParams;
		params.kw = val;
		$grid.datagrid({ url: '/Question/Knowledge/getPapersDetail', pageNumber: 1 });
    }
    
    var action, form;
	function show_paper_edit() {
		var row = $('#paper_detail_list').datagrid('getSelected');
		if (row) {
			$('#show-paper-dlg').dialog({
				href: '/Question/Knowledge/paper_detail_edit?id=' + row.id,
				iconCls: 'icon-edit',
				title: '编辑'
			}).dialog('open');
			action = '/Question/Knowledge/paper_detail_save';
			form = 'paper-edit-form';
		} else {
			$.messager.alert('提示信息', '请选择要操作的数据！', 'info');
		}
	}
	function show_paper_save() {
		$('#' + form).form('submit', {
			url: action,
			onSubmit: function () {
				return $(this).form('validate');
			},
			success: function (result) {
				result = JSON.parse(result);
				if ( result.status ) {
					$('#show-paper-dlg').dialog('close');
					$('#paper_detail_list').datagrid('reload');
				} else {
					$('#show-paper-dlg').dialog('close');
				}
			}
		});
	}




</script>
</body>
</html>