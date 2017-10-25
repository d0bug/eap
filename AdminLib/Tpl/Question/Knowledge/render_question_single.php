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
<div class="mr_b_5">
	<a href="#" onclick="edit_single_question({$question['id']})" class="easyui-linkbutton" title="编辑题干" data-options="plain:true, iconCls:'icon-edit'">编辑题干</a>
</div>
<div class="qt_box mr_b_5">
	<notempty name="question['file_name']">
	<div class="info">
		<span style="float: left">{$question['grade_name']} {$question['subject_name']} {$question['course_type_name']}</span>
		<if condition="$question['is_classic'] eq 1"><span style="float:left; padding-left: 25px;" class="red blod">经典题</span></if>
		<span class="red" style="float:left; padding-left: 25px;">{$question['file_name']}</span>
	</div>
	</notempty>
	<div style="padding: 8px 0 0 8px;">
		<form id="question-simple-list-form" method="post" novalidate>
			<table cellpadding="0" cellspacing="0" style="width:95%;">
				<tr>
					<td colspan="2" style="height: 35px;">
						<span style="float:left">ID：<em class="blod">{$question['id']}</em></span>
						<span style="padding-left: 20px;float:left">题号：<em class="red blod">{$question['number']}</em></span>
						<span style="padding-left: 20px;float:left">分值：<em class="red blod">{$question['score']}</em></span>
						<span style="float:left; padding-left: 20px;">
							题型：<input class="easyui-combobox" style="width: 100px;" id="single_question_type_id" value="{$question['question_type_id']}" data-options="url: '/Question/Basic/getQuestionTypesBySubjectId?subjectid={$question['subject_id']}', valueField:'id', textField:'title', method: 'get', editable: false, panelHeight:'auto'">
						</span>
						<span style="padding-left: 20px;float:left">
							难度：<input class="easyui-combobox" style="width: 80px;" id="single_difficulty_id" value="{$question['difficulty']}" data-options="url: '/Question/Basic/getDifficulties', valueField:'id', textField:'text', method: 'get', editable: false, panelHeight:'auto'">
						</span>
						<span style="padding-left: 20px;float:left">
							适用年级：<input class="easyui-combobox" style="width: 120px;" id="single_grades" data-options="url: '/Question/Basic/getGradesByGroup', editable: false, multiple: true, valueField: 'id', textField: 'title', value: [{$question['grades']}]" />
						</span>
						<span style="padding-left: 20px;float:left">
							<a href="#" onclick="question_single_select()" class="easyui-linkbutton" title="保存" data-options="plain:true, iconCls:'icon-save'">保存</a><label id="update_status"></label>
						</span>
					</td>
				</tr>
				<tr>
					<td style="height: 26px;"><input class="easyui-searchbox" data-options="prompt:'请输入知识点名称...', searcher: do_search5" style="width:400px" /></td>
					<td></td>
				</tr>
				<tr>
					<td valign="top" style="width: 400px;">
						<ul id="knowledge-index-tree3" class="easyui-tree" validType="checkKnode" data-options="url: '/Question/Basic/getKnowledgesChilds1?coursetypeid={$question['course_type_id']}', method: 'get', lines: true, onSelect: do_select5, formatter: tree_node_formatter1"></ul>
					</td>
					<td valign="top">
						<input type="hidden" id="single_knowledge_ids" value="">
						<ul id="single_knowledge_names" style="margin: 0 0 0 25px;"></ul>
					</td>
				</tr>
			</table>
		</form>
  	</div>
	<div style="margin-top: 5px">
		<div class="content">
			<table>
				<tr>
					<td>{$question['content']}</td>
				</tr>
			</table>
			<notempty name="question['options']">
			<table>
				<volist name="question['options']" id="option">
				<tr>
					<td style="width: 20px; height: 20px;">{$i-1 | get_option_flag_name}．</td>
					<td>{$option.content}</td>
				</tr>
				</volist>
			</table>
			</notempty>
		</div>
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
							<if condition="($question['question_type_name'] eq '选择题' || $question['question_type_name'] eq '单选题' || $question['question_type_name'] eq '多选题')">
								<notempty name="question['options']">
								<volist name="question['options']" id="option">
									<if condition="($option.is_answer eq 1)"><strong>{$i-1 | get_option_flag_name}</strong></if>
								</volist>
								</notempty>
							<else />	
								<notempty name="question['answers']">
								<table>
									<volist name="question['answers']" id="answer">
									<tr>
										<td><span>{$answer.content}</span></td>
									</tr>
									</volist>
								</table>
								</notempty>
							</if>
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
<!--Begin 试题编辑对话框-->
<div id="question-single-edit-dlg" class="easyui-dialog" title="编辑" data-options="modal:true,closed:true,cache:false,iconCls:'icon-edit'" style="width:1000px;height:500px;padding:5px;">
    <iframe scrolling="auto" id="question-single-edit-dlg-iframe" frameborder="0" src="" style="width:100%;height:100%;"></iframe>
</div>
<!--End 试题编辑对话框-->
<script type="text/javascript">MathJax.Hub.Configured()</script>
<script language='javascript' type='text/javascript'>
	$(function(){
		MathJax.Hub.Queue(["Typeset", MathJax.Hub, 'word-subject']);
		var id = "{$question['knowledge_id']}";
		var subIds = "{$question['sub_knowledge_id']}";
		var ids = (subIds == '' ? [] : subIds.split(','));
		if (id != '0' && id != '')
			ids.push(id);

		if (id.length == 0) {
			$('#single_knowledge_ids').val('');
			$('#single_knowledge_names').html('');
		}
		else {
			$('#single_knowledge_ids').val(ids.join(','));
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
					var len = $('#single_knowledge_names li').length;
					$('#single_knowledge_names').append('<li onclick="remove_item1(' + len + ')" title="点击删除" style="cursor: pointer;" id="' + (len) + '"><a href="#" style="display: block;"><em></em>．' + name + '</a></li>');
				});
				re_bind_list1();
			});
		}
	});
	function do_search5(val) {
		var url = '/Question/Basic/getKnowledgesSearch?coursetypeid={$question['course_type_id']}&kw=' + val;
		if (val == '')
			url = '/Question/Basic/getKnowledgesChilds1?coursetypeid={$question['course_type_id']}';
		$('#knowledge-index-tree3').tree({
			url: url
		});
	}
	function tree_node_formatter1(node, rec) {
		if (node.is_leaf == 1) {
			return node.text + '(<span class="red">' + node.knode_classic_question_num + '</span>/<span class="red">' + node.knode_question_num + '</span>)';
		}
		return node.text;
	}
	function do_select5(node) {
    	if($(this).tree('isLeaf', node.target)){
    		var len = $('#single_knowledge_names li').length;
            var ids = $('#single_knowledge_ids').val();
            ids = (ids == '') ? node.id : ids + ',' + node.id;
    		$('#single_knowledge_ids').val(ids);
    		$('#single_knowledge_names').append('<li onclick="remove_item1(' + len + ')" title="点击删除" style="cursor: pointer;" id="' + (len) + '"><a href="#" style="display: block;"><em></em>．' + node.text + '</a></li>');
    		re_bind_list1();
    	}
    	else {
    		$(this).tree('toggle', node.target);
    	}
	}
	function edit_single_question(id) {
		$('#question-single-edit-dlg-iframe')[0].src = '/Question/Knowledge/edit_question?id=' + id;
		$('#question-single-edit-dlg').dialog('open');
    }
    function close_dlg() {
    	$('#question-single-edit-dlg').dialog('close');
    }
    function question_single_select() {
    	var knowledgeId = '';
		var subknowledgeIds = '';
		var tmpIds = [];
		
        var questionId = {$question["id"]};
        var questionTypeId = $('#single_question_type_id').combobox('getValue');
        var difficulty = $('#single_difficulty_id').combobox('getValue');
        var grades = $('#single_grades').combobox('getValues');
        var knowledgeIds = $('#single_knowledge_ids').val();
        
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

    	$.post('/Question/Knowledge/updateQuestion', {question_id: questionId, question_type_id: questionTypeId, difficulty: difficulty, grades: grades, knowledge_id: knowledgeId, sub_knowledge_id: subknowledgeIds}, function(result) {
    		result = JSON.parse(result);
    		if (result.status) {
    			//$.messager.alert('提示信息', '修改成功!', 'info');
    			$('#update_status').html('<font color=green>修改成功</font>');
        	}else{
        		$('#update_status').html('<font color=red>修改失败</font>');
        	}
		});
	}
    function remove_item1(id) {
		$.each($('#single_knowledge_names li'),
            function(n, v) {
        		if (v.id == id) {
        			var strs = $('#single_knowledge_ids').val();
        			var ids = strs.split(',');
        			ids.splice(n, 1);
        			$('#single_knowledge_ids').val(ids.join(','));
	        		$(v).remove();
	        		return;
        		}
			}
		);
		re_bind_list1();
	}
    function re_bind_list1() {
		$.each($('#single_knowledge_names li'), 
            function(n, v) {
				$(v).find('em').html(n + 1);
			}
		);
	}
</script>
