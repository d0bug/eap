<!doctype html>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<link href="/static/css/question.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/static/js/question.js"></script>
</head>
<body style="padding: 5px;">
	<div class="easyui-layout" data-options="fit: true">
		<div region="north" style="height: 38px;" data-options="collapsible: false, border: false">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
				<tr>
					<td class="wd_150">
						<input id="paper-list-gradedept" class="easyui-combobox"  style="width: 135px;" data-options="url: '/Question/Basic/getDictsAllByCategory?cate=grade_dept&limit_role=2', editable: false, valueField: 'code', textField: 'name', onSelect: paper_list_label_search" />
					</td>
					<td class="wd_150">
						<input id="paper-list-subject" class="easyui-combobox"  style="width: 135px;" data-options="url: '/Question/Basic/getDictsAllByCategory?cate=subject&limit_role=2', editable: false, valueField: 'code', textField: 'name', onSelect: paper_list_label_search" />
					</td>
					<td class="wd_200">
						<input id="paper-list-label" class="easyui-combotree"  style="width: 185px;" />
					</td>
					<td class="wd_280">
						上传日期：<input id="paper-list-startdate" class="easyui-datebox" style="width: 100px"> 至  <input id="paper-list-enddate" class="easyui-datebox" style="width: 100px">
					</td>
					<td>
						<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-search'" onclick="javascript: set_values();paper_list_search()"></a>
					</td>
				</tr>
			</table>
		</div>
		<div region="center" data-options="collapsible: false, border: false">
			<div class="easyui-layout" data-options="fit: true">
				<div region="north" style="height: 38px;" data-options="collapsible: false, border: false">
					<div style="background-color: #ddd; height: 20px; padding: 3px 10px 3px 10px; margin-top: 6px;">
						<span style="float: right; text-align: right">
							<span>今日上传：<font class="red"><?php echo $count['today'];?></font>道 </span>
							<span style="padding-left: 50px;">近一周上传：<font class="red"><?php echo $count['week'];?></font>道</span>
							<span style="padding-left: 50px;">近一月上传：<font class="red"><?php echo $count['month'];?></font>道</span>
						</span>
					</div>
				</div>
				<div region="center" data-options="collapsible: false, border: false" >
					<div id="paper-list-panel" class="easyui-panel" style="width:auto;" data-options="href: '/Question/Audit/render_audit_list', cache: true, border: false" ></div>
				</div>
				<div region="south" style="height: 32px;" data-options="collapsible: false, border: true">
					<div id="paper-list-pagination" class="easyui-pagination"></div>
				</div>
			</div>
		</div>
	</div>
	<script language='javascript' type='text/javascript'>
	var g = '';
	var s = '';
	var l = '';
	var sd = '';
	var ed = '';
	var options = {
		total: {$total},
		onSelectPage: function (pageNumber, pageSize) {
			$(this).pagination('loading');
			paper_list_search(pageNumber, pageSize);
			$(this).pagination('loaded');
		}
	};
	$(function() {
		$('#paper-list-pagination').pagination(options);
	});
	function set_values(){
		g = $('#paper-list-gradedept').combobox('getValue');
		s = $('#paper-list-subject').combobox('getValue');
		l = $('#paper-list-label').combobox('getValue');
		sd = $('#paper-list-startdate').datebox('getValue');
		ed = $('#paper-list-enddate').datebox('getValue');
	}
	function paper_list_search(pageNumber, pageSize) {
		options.pageNumber = pageNumber;
		var params = get_paper_list_search_params(pageNumber, pageSize);
		refresh_paper_list(params);
		render_paper_list_pagination(params);
	}
	function refresh_paper_list(params) {
		$('#paper-list-panel').panel('refresh', '/Question/Audit/render_audit_list' + params);
	}
	function render_paper_list_pagination(params) {
		$.ajax({
			url : '/Question/Audit/get_audit_list_count' + params,
			type : 'GET',
			dataType : 'json',
			success: function(count) {
				options.total = count;
				$('#paper-list-pagination').pagination('refresh', options);
			}
		});
	}
	function get_paper_list_search_params(pageNumber, pageSize) {
		/*
		var g = $('#paper-list-gradedept').combobox('getValue');
		var s = $('#paper-list-subject').combobox('getValue');
		var l = $('#paper-list-label').combobox('getValue');
		var sd = $('#paper-list-startdate').datebox('getValue');
		var ed = $('#paper-list-enddate').datebox('getValue');
		*/

		var params = '?page=' + (typeof pageNumber == 'undefined' ? '' : pageNumber) +
					'&rows=' + (typeof pageSize == 'undefined' ? '' : pageSize) +
					'&gradedept=' + g +
					'&subject=' + s +
					'&knowledge=' + l +
					'&startdate=' + sd +
					'&enddate=' + ed;
			
					return params;
	}
	function paper_list_label_search(){
		var g = $('#paper-list-gradedept').combobox('getValue');
		var s = $('#paper-list-subject').combobox('getValue');

		$('#paper-list-label').combotree({
			url: '/Question/Basic/getComboTreeKnowledges?grade=' + g + '&subject=' + s
		});
	}
	</script>
</body>
</html>