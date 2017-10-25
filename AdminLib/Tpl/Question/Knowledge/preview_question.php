<table cellpadding="0" cellspacing="0" border="0" width="100%" class="tableInfo">
	<tr>
		<td class="wd_80 alt right">年部：</td>
		<td class="wd_150">
			<div id="question-preview-grade"></div>
		</td>
		<td class="wd_80 alt right">学科：</td>
		<td class="wd_150">
			<div id="question-preview-subject"></div>
		</td>
		<td class="wd_80 alt right">课程类型：</td>
		<td>
			<div id="question-preview-course-type"></div>
		</td>
	</tr>
	<tr>
		<td class="alt right">主知识点：</td>
		<td>
			<div id="question-preview-knowledge"></div>
		</td>
		<td class="alt right">副知识点：</td>
		<td colspan="3">
			<div id="question-preview-subknowledge"></div>
		</td>
	</tr>
	<tr>
		<td class="alt right">题型：</td>
		<td>
			<div id="question-preview-question-type"></div>
		</td>
		<td class="alt right">难度：</td>
		<td>
			<div id="question-preview-difficulty"></div>
		</td>
		<td class="alt right">适用年级：</td>
		<td>
			<div id="question-preview-grades"></div>
		</td>
	</tr>
</table>
<div class="qt_box mr_t_5">
	<div>
		<div class="content">
			<span id="question-preview-content"></span>
			<span id="question-preview-options"></span>
		</div>
  	</div>
	<div class="answer">
		<div class="action">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tbody>
					<tr>
						<td valign="top" style="width: 55px;">【答案】</td>
						<td>
							<span id="question-preview-answers"></span>
						</td>
					</tr>
					<tr>
						<td valign="top">【解析】</td>
						<td>
							<div id="question-preview-analysis"></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
	function get_option_html(options) {
		var html = '';
		$.each(options, function (i, el) {
			html += '<tr><td style="width: 20px">' + get_option_flag_name(i) + '．</td><td>' + el + '</td></tr>';
		});
		
		return html == '' ? '' : ('<table>' + html + '</table>');
	}
	function get_answer_html(flags, answers) {
		var html = '';
		if (flags.length > 0) {
			var s = '';
			$.each(flags, function (i, el) {
				s += get_option_flag_name(el);
			});
			html = '<tr><td>' + s + '</td></tr>';
		}
		$.each(answers, function (i, el) {
			html += '<tr><td>' + el + '</td></tr>';
		});
		return html == '' ? '' : ('<table>' + html + '</table>');
	}
	$(function() {
		var grade = $('#question-add-grade li.active').text();
		var subject = $('#question-add-subject li.active').text();
		var courseType = $('#question-add-course-type li.active').text();
		var questionType = $('#question-add-question-type li.active').text();

		var knowledge = $('#question-add-knowledge').combobox('getText');
		var subKnowledge = $('#question-add-subknowledge').combobox('getText');
		var grades = $('#question-add-grades').combobox('getText');

		var content = '', analysis = '', options = [], answers = [], flags = [];
		$('div.editor, input.options').each(function(i ,el) {
			var id = $(el).attr('id');
			if (id.indexOf('question_content') == 0) {
				var val = UE.getEditor(id).getContent();
				content = val;
			}
			if (id.indexOf('question_analysis') == 0) {
				var val = UE.getEditor(id).getContent();
				analysis = val;
			}
			if (id.indexOf('options') == 0) {
				var val = UE.getEditor(id).getContent();
				options.push(val);
			}
			if (id.indexOf('question_answers') == 0) {
				var val = UE.getEditor(id).getContent();
				answers.push(val);
			}
		});
		$(':radio.isanswer, :checkbox.isanswer').each(function(i ,el) {
			if ($(el).attr('id').indexOf('options_answer_flag') == 0 && $(el).attr('checked') == 'checked') {
				flags.push($(el).val());
			}
		});
		var optionsHtml = get_option_html(options);
		var answersHtml = get_answer_html(flags, answers);

		$('#question-preview-grade').html(grade);
		$('#question-preview-subject').html(subject);
		$('#question-preview-course-type').html(courseType);
		$('#question-preview-question-type').html(questionType);

		$('#question-preview-knowledge').html(knowledge);
		$('#question-preview-subknowledge').html(subKnowledge);
		$('#question-preview-difficulty').raty({
			number: 3,
			hints: ['容易', '中等', '困难'],
			readOnly: true,
			score: $('#question-add-difficulty').raty('score')
		});
		$('#question-preview-grades').html(grades);

		$('#question-preview-content').html(content);
		$('#question-preview-options').html(optionsHtml);
		$('#question-preview-analysis').html(analysis);
		$('#question-preview-answers').html(answersHtml);
	});
</script>