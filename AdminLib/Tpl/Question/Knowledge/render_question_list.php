<empty name="questions['rows']">
	<div class="red" style="text-align: center; font-size: 16px;">没有满足条件的记录！</div>
<else />
<volist name="questions['rows']" id="question">
	<div class="qt_box mr_b_5">
		<div class="info">
			<span class="red" style="float: left">[{$question['department'] | get_department}]</span>
			<span style="float:left; padding-left: 10px">{$question['grade_name']} {$question['subject_name']} {$question['course_type_name']}</span>
			<span class="red" style="float:left; padding-left: 25px;">{$question['file_name']}</span>
		</div>
		<div class="info">
			<span style="float:left" class="red blod">{$question['question_type_name']}</span>
			<if condition="$question['is_classic'] eq 1"><span style="padding-left: 20px;float:left;" class="red blod">经典题</span></if>
			<if condition="$question['is_content_error'] eq 1"><span style="padding-left: 20px;float:left;" class="red blod">题干有问题</span></if>
			<span style="padding-left: 20px;float:left">ID：<em class="blod">{$question['id']}</em></span>
			<if condition="$question['department'] eq 'CLASS'">
			<span style="padding-left: 20px;float:left">题号：<em class="red blod">{$question['number']}</em></span>
			<span style="padding-left: 20px;float:left">分值：<em class="red blod">{$question['score']}</em></span>
			</if>
			<span style="padding-left: 20px;float:left">主知识点：{$question.knowledge_name}</span>
			<span style="padding-left: 20px;float:left">难度：{$question['difficulty'] | get_difficulty_name}</span>
			<span style="padding-left: 20px;float:left">适用年级：{$question['grade_names']}</span>
			<span style="padding-left: 20px;float:left">上传日期：{$question['created_time'] | format_date} {$question['created_user_name']}</span>
		</div>
		<div>
			<div class="content pointer" onclick="$(this).parent().next().toggle()">
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
			<div <if condition="($question['course_type_id'] neq 29)"> style="display: none"</if> class="absolute">
				<ul class="clearfix">
					<li><a href="#" onclick="edit_question({$question['id']})" class="easyui-linkbutton" title="编辑" data-options="plain:true, iconCls:'icon-edit'"></a></li>
					<li><a href="#" onclick="$(this).parent().parent().parent().parent().next().toggle()" class="easyui-linkbutton" title="查看" data-options="plain:true, iconCls:'icon-table'"></a></li>
				</ul>
		    </div>
	  	</div>
		<div class="answer pointer" style="display: none" onclick="$(this).hide()">
			<div class="action">
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tbody>
						<tr>
							<td valign="middle" style="height: 28px;">【答案】</td>
						</tr>
						<tr>
							<td style="height: 25px;">
								<empty name="question['options']">
									<empty name="question['answers']">
										无
									</empty>
								</empty>
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
</volist>
</empty>
<!--Begin 试题编辑对话框-->
<div id="question-edit-dlg" class="easyui-dialog" title="编辑" data-options="modal:true,closed:true,cache:false,iconCls:'icon-edit'" style="width:1000px;height:500px;padding:5px;">
    <iframe scrolling="auto" id="question-edit-dlg-iframe" frameborder="0" src="" style="width:100%;height:100%;"></iframe>
</div>
<!--End 试题编辑对话框-->
<script language='javascript' type='text/javascript'>
	function edit_question(id) {
		$('#question-edit-dlg-iframe')[0].src = '/Question/Knowledge/edit_question?id=' + id;
		$('#question-edit-dlg').dialog('open');
    }
</script>