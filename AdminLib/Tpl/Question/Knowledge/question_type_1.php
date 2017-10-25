<tr>
	<td class="alt right wd_120">
		<span class="red">*</span> 题干：
	</td>
	<td class="left" colspan="3">
		<textarea id="question_content_{$suffix}" name="content" class="editor">{$question['content']}</textarea>
	</td>
</tr>
<tr>
	<td class="alt right">
		<span class="red">*</span> 选项：
	</td>
	<td class="left" colspan="3">
		<a href="#" onclick="appendTR('', '', '', '')">添加选项</a>
		<table id="question_option_table_{$suffix}" width="100%" border="0" cellpadding="0" cellspacing="0" class="question_option_table">
			<tbody class="options"></tbody>
		</table>
	</td>
</tr>
<tr>
	<td class="alt right" style="border: 0px">
		<span class="red">*</span> 解析：
	</td>
	<td class="left" colspan="3" style="border: 0px">
		<textarea id="question_analysis_{$suffix}" name="analysis" class="editor">{$question['analysis']}</textarea>
	</td>
</tr>
<script type="text/javascript">
	$(function() {
		var num = {$count};
		if(num <= 0) {
			for (var i = 0; i <= 3; i++) {
				appendTR('', '', '', '');
			}	
		} else {
		 	var data = {$options | json_encode};
		 	$.each(data, function(index, d){
		 		var id = d.oid;
		 		var euid = d.uid;
			  	var content = d.ocontent;
			  	var is_answer = d.is_answer;
			 	appendTR(id, euid, content, is_answer);
			});
		}
	});
	function appendTR(id, euid, content, is_answer) {
		var len = $('#question_option_table_{$suffix} tbody.options tr.options').length;
		var radioId = 'options_answer_flag_' + len + '_{$suffix}';
		var euid = euid != '' ? euid : get_random_str(16);
		var textareaId = 'options_' + euid + '_{$suffix}';
		var val = len;
		var id = id;
		var checked = is_answer == '1' ? 'checked=\"checked\"' : '';
		var content = content == undefined ? '' : content;
		var html = '<tr class="options">' +
						'<td style="width: 50px; text-align: center; border: 0px">' +
							'<div class="answer_opbt"></div>' +
							'<label><input type="radio" id="' + radioId + '" name="options_answer_flag[]" class="isanswer" value="' + val + '" ' + checked + ' /> <span class="ft_16 options_answer_flag">' + get_option_flag_name( len ) + '</span></label>' +
							'<div class="answer_opbt"><a href="#" onclick="removeTR(this)">删除</a></div>' +
						'</td>' +
						'<td style="border: 0px">' +
							'<input type="hidden" name="euids[]" value="' + euid + '">' +
							'<input type="hidden" name="oid[]" value="' + id + '">' +
							'<textarea id="' + textareaId + '" name="options[]" class="editor">' + content + '<\/textarea>' +
						'</td>' +
					'</tr>';
		$('#question_option_table_{$suffix} tbody.options').append(html);
		UE.getEditor(textareaId, {
			initialFrameHeight: 50
		});
	}
	function removeTR(o) {
		$(o).parent().parent().parent().remove();
		reBindOptionsFlagName();
	}
	function reBindOptionsFlagName() {
		$('#question_option_table_{$suffix} tbody tr.options').each(function(i, el) {
			$(el).find('.options_answer_flag').html(get_option_flag_name( i ));
			$(el).find('.isanswer').val( i );
		});
	}
</script>