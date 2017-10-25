<script type="text/x-mathjax-config">
		MathJax.Hub.Config({
		  config: ["MMLorHTML.js"],
		  jax: ["input/TeX","input/MathML","output/HTML-CSS","output/HTML-CSS"],
		  extensions: ["tex2jax.js","mml2jax.js","MathMenu.js","MathZoom.js"],
		  TeX: {
			
		    extensions: ["AMSmath.js","AMSsymbols.js","noErrors.js","noUndefined.js"]
		  },
		tex2jax: {
		      inlineMath: [ ['$','$'], ['$$','$$'], ["\[","\]"]],
		      displayMath: [ ["\(","\)"] ],
		      processEscapes: true,
		processEnvironments: true,
		displaystyle: true,
		    },
		     CommonHTML: { linebreaks: { automatic: true } },
		     "HTML-CSS": { linebreaks: { automatic: true } },
		         SVG: { linebreaks: { automatic: true } }
		});

</script>

<script type="text/javascript" src="/static/js/mathjax/MathJax.js?config=TeX-AMS-MML_HTMLorMML&delayStartupUntil=configured"></script>
<empty name="question">
	<div class="red" style="text-align: center; font-size: 16px;">没有满足条件的记录！</div>
<else /> 
	<div class="qt_box mr_b_5">
		<div class="info" style="padding: 5px 0 0 10px;">
			<span style="float:left" class="red blod">{$question['file_name']}</span>
		</div>
		<div style="padding: 8px 0 0 8px;">
			<form id="question-simple-list-form" method="post" novalidate>
				<table cellpadding="0" cellspacing="0" style="width:95%;">
					<tr>
						<td colspan="4" style="height: 35px;">
							<label class="red">[{$question['department'] | get_department}]</label>
							<label style="margin-left: 25px">ID：{$question['question_id']}</label>
							<label style="margin-left: 25px">题号：{$question['number']}</label>
							<label style="margin-left: 25px"><input type="checkbox" id="is_content_error" /> 题干有问题</label>
							<label style="margin-left: 40px"><input type="checkbox" id="is_classic" /> 经典题</label>
							<label style="margin-left: 40px">难度：
								<input class="easyui-combobox" style="width: 100px;" id="difficulty" name="difficulty" data-options="url: '/Question/Basic/getDifficulties', valueField:'id', textField:'text', method: 'get', editable: false, panelHeight:'auto',value:{$question['difficulty']}">
							</label>
							<a href="#" class="easyui-linkbutton" style="margin-left: 75px;" data-options="plain:true, iconCls:'icon-save'" onclick="edit_simple_question({$question['question_id']})">提交</a>
							<a href="#" class="easyui-linkbutton" style="margin-left: 50px;" data-options="plain:true, iconCls:'icon-add'" onclick="skip_question({$question['question_id']})">跳过</a>
						</td>
					</tr>
					<tr>
						<td valign="top" style="width: 400px;">
							<input class="easyui-searchbox" data-options="prompt:'请输入知识点名称...', searcher: do_search" style="width:400px" />
							<ul id="knowledge-index-tree" class="easyui-tree" validType="checkKnode" data-options="url: '/Question/Basic/getKnowledgesChilds1?coursetypeid={$coursetypeid}', method: 'get', lines: true, onSelect: do_select, formatter: tree_node_formatter"></ul>
						</td>
						<td valign="top" colspan="3">
							<input type="hidden" id="knowledge_ids" name="" value="">
							<ul id="knowledge_names" style="margin: 25px 0 0 36px;"></ul>
						</td>
					</tr>
				</table>
			</form>
			<table style="margin-top:10px;">
				<tr>
					<td>{$question['content']}</td>
				</tr>
			</table>
			<notempty name="question['options']">
			<table style="margin-top:5px;">
				<volist name="question['options']" id="option">
				<tr>
					<td style="width: 20px; height: 20px;">{$i-1 | get_option_flag_name}．</td>
					<td>{$option.content}</td>
				</tr>
				</volist>
			</table>
			</notempty>
	  	</div>
	  	<div class="answer">
			<div class="action">
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tbody>
						<tr>
							<td valign="middle" style="height: 28px;">【答案】</td>
						</tr>
						<tr>
							<td style="height: 25px;">
								<notempty name="question['options']">
								<volist name="question['options']" id="option">
									<if condition="($option.is_answer eq 1)"><strong>{$i-1 | get_option_flag_name}</strong></if>
								</volist>
								</notempty>
								<notempty name="question['answers']">
								<table>
									<volist name="question['answers']" id="answer">
									<tr>
										<td><span>{$answer.content}</span></td>
									</tr>
									</volist>
								</table>
								</notempty>
							</td>
						</tr>
						<tr>
							<td valign="middle" style="height: 28px;">【解析】</td>
						</tr>
						<tr>
							<td><empty name="question['analysis']">无<else />{$question['analysis']}</empty></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</empty>
<script type="text/javascript">MathJax.Hub.Configured()</script>
<script language='javascript' type='text/javascript'>
	$(function() {
		MathJax.Hub.Queue(["Typeset", MathJax.Hub, 'word-subject']);
		var id = "{$question['knowledge_id']}";
		var subIds = "{$question['sub_knowledge_id']}";
		var ids = (subIds == '' ? [] : subIds.split(','));
		if (id != '0' && id != '')
			ids.push(id);

		if (id.length == 0) {
			$('#knowledge_ids').val('');
			$('#knowledge_names').html('');
		}
		else {
			$('#knowledge_ids').val(ids.join(','));
			$.post('/Question/Basic/getKnowledgeByIDs', {ids: ids.join(',')}, function(data){
				data = JSON.parse(data);
				$(ids).each(function(i, e){
					var name = '';
					$(data).each(function(ii, ee){
						if (ee.id == e) {
							name = ee.name;
							return;
						}
					});
					var len = $('#knowledge_names li').length;
					$('#knowledge_names').append('<li onclick="remove_item(' + len + ')" title="点击删除" style="cursor: pointer;" id="' + (len) + '"><a href="#" style="display: block;"><em></em>．' + name + '</a></li>');
				});
				re_bind_list();
			});
		}
		
		$.extend($.fn.validatebox.defaults.rules, {
		    checkKnode: {
		        validator: function (value, param) {
		            return $('#knowledge_ids').val() != '';
		        },
		        message: '请选择知识点'
		    }
		});
		refresh_question_statistics();
	});
	function refresh_question_statistics() {
		$.post('/Question/Knowledge/getQuestionStatisticsByCourseTypeId', {coursetypeid: courseTypeId}, function(data){
			data = JSON.parse(data);
			$('#lock_question_count').html(data.lock_question_count);
			$('#left_non_edit_question_count').html(data.left_non_edit_question_count);
			$('#total_question_count').html(data.total_question_count);
			$('#my_op_question_count').html(data.my_op_question_count);
		 });
	}
	function tree_node_formatter(node, rec) {
		if (node.is_leaf == 1) {
			return node.text + '(<span class="red">' + node.knode_classic_question_num + '</span>/<span class="red">' + node.knode_question_num + '</span>)';
		}
		return node.text;
	}
	function skip_question(id) {
		$.messager.confirm('信息提示', '跳转至下一题？', function(r){
			 if (r){
				$.post("/Question/Knowledge/skip_question", {id: id}, function(rs) {
					rs = JSON.parse(rs);
					if (rs.status) {
						$('#question-smiple-list-panel').panel('refresh', '/Question/Knowledge/render_question_simple_list?coursetypeid={$coursetypeid}');
					}
					else {
						alert('跳题失败！');
					}
				});
			 }
		 });
	}
	function edit_simple_question(id) {
		var isContentError = $('#is_content_error').attr("checked") == 'checked' ? 1 : 0;
		var isClassic = $('#is_classic').attr("checked") == 'checked' ? 1 : 0;
		var difficulty = $('#difficulty').combobox('getValue');
		
		var knowledgeId = '';
		var subknowledgeIds = '';
		var tmpIds = [];
		
		var knowledgeIds = $('#knowledge_ids').val();
		var ids = knowledgeIds.split(',');
		//最后一个知识点为主知识点，其余为副知识点
		if (ids.length == 1) {
			knowledgeId = ids[0];
		}
		else if (ids.length > 1){
			knowledgeId = ids[ids.length - 1];
			for(var i = 0 ; i < ids.length-1 ; i++) {
				tmpIds.push(ids[i]);
			}
			subknowledgeIds = tmpIds.join(',');
		}
		//题干有问题 难度 知识点为非必填
		if (isContentError != 1) {
			if(difficulty == '') {
				$.messager.alert('信息提示','请选择难度！');
				return;
			}
			if (knowledgeId　== '') {
				$.messager.alert('信息提示','请选择知识点！');
				return;
			}
		}
		
		$.post("/Question/Knowledge/save_simple_question", {id: id, is_content_error: isContentError, is_classic: isClassic, knowledge_id: knowledgeId, sub_knowledge_id: subknowledgeIds, difficulty: difficulty}, function(rs) {
			var courseTypeId = {$coursetypeid};
			rs = JSON.parse(rs);
			if (rs.status) {
				refresh_question_statistics();
				$.messager.confirm('信息提示', '修改成功，跳转至下一题？', function(r){
					if (r){
						$('#question-smiple-list-panel').panel('refresh', '/Question/Knowledge/render_question_simple_list?coursetypeid=' + courseTypeId);
					}
				});
			}
			else {
				alert('保存失败！');
			}
		});
    }
    function do_search(val) {
		var url = '/Question/Basic/getKnowledgesSearch?coursetypeid={$coursetypeid}&kw=' + val;
		if (val == '')
			url = '/Question/Basic/getKnowledgesChilds1?coursetypeid={$coursetypeid}';
		$('#knowledge-index-tree').tree({
			url: url
		});
	}
    function do_select(node) {
    	if($(this).tree('isLeaf', node.target)){
            var len = $('#knowledge_names li').length;
            var ids = $('#knowledge_ids').val();
            ids = (ids == '') ? node.id : ids + ',' + node.id;
    		$('#knowledge_ids').val(ids);
    		$('#knowledge_names').append('<li onclick="remove_item(' + len + ')" title="点击删除" style="cursor: pointer;" id="' + (len) + '"><a href="#" style="display: block;"><em></em>．' + node.text + '</a></li>');
    		re_bind_list();
    	}
    	else {
    		$(this).tree('toggle', node.target);
    	}
    }
	function remove_item(id) {
		$.each($('#knowledge_names li'),
            function(n, v) {
        		if (v.id == id) {
        			var strs = $('#knowledge_ids').val();
        			var ids = strs.split(',');
        			ids.splice(n, 1);
        			$('#knowledge_ids').val(ids.join(','));
	        		$(v).remove();
	        		return;
        		}
			}
		);
		re_bind_list();
	}
	function re_bind_list() {
		var len = $('#knowledge_names li').length;
		$.each($('#knowledge_names li'), 
            function(n, v) {
				$(v).find('em').html(n + 1);
			}
		);
	}
</script>