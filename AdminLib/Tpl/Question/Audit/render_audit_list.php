<volist name="questions['rows']" id="question">
	<div class="qt_box mr_b_5">
		<div class="info">
			<span style="float:left">编号：{$question.id}</span>
			<span style="padding-left: 20px;float:left">题型：{$question.question_type_name}</span>
			<span style="padding-left: 20px;float:left">年部：{$question.grade_dept_name}</span>
			<span style="padding-left: 20px;float:left">学科：{$question.subject_name}</span>
			<span style="padding-left: 20px;float:left">知识点：{$question.knowledge_name}</span>
			<span style="padding-left: 20px;float:left">标签：常规题</span>
			<span style="padding-left: 20px;float:left">难度级别：{$question['difficulty'] | formatDifficultyName}</span>
			<span style="padding-left: 20px;float:left">上传人：{$question.create_user_name}</span>
			<span style="padding-left: 20px;float:left">上传日期：{$question.create_time}</span>
			<span style="padding-left: 20px;float:left">使用次数：{$question.use_number}</span>
		</div>
		<div class="title2" onclick="flex_content(<?php echo $question['id']?>)">
			<div class="content pointer" onclick="$(this).parent().next().children(0).toggle()">
				<?php if($question['question_type_code'] == 'QT1006')://作文题?>
					<b>作文要求：</b>
					<div class="p_left_50">
						<?php echo $question['composition_ask'];?>
					</div>
					<b>作文范文：</b>
					<div class="p_left_50">
						<?php echo $question['composition_model'];?>
					</div>
				<?php else: ?>
					<?php echo $question['question_content'];?>
				<?php endif;?>
				
				<?php if(!empty($question['option_content'])):?>
					<?php echo $question['option_content'];?>
				<?php endif;?>
				<?php if(!empty($question['child_content'])):?>
					<?php echo $question['child_content'];?>
				<?php endif;?>
			</div>
			<div class="opeate">
				<?php if($question['status']==1):?>
					<span class="gray blod">已审</span>
				<?php elseif($question['status']==2):?>
					<span class="orange blod">已打回</span><br>
					<span class="red">{$question['reason']}</span>
				<?php else:?>
					<span class="red blod">待审</span><br>
					<span>
						<a href="javascript:audit_question(<?php echo $question['id']?>,1)" class="blue">通过</a>&nbsp;&nbsp;&nbsp;&nbsp;
						<a href="#" onclick="javascript:$('#alert').window('open');$('#qsn_id').val(<?php echo $question['id']?>)"class="blue">打回</a>
					</span>
				<?php endif;?>
			</div>
		</div>
		<div class="answer">
			<span style="display:none;">
				<table>
					<tbody>
						<tr>
							<td valign="top"><b>【正确答案】</b></td>
							<td><?php echo ($question['answer_content']) ? $question['answer_content'] : '无';?></td>
						</tr>
						<tr>
							<td valign="top"><b>【题目解析】</b></td>
							<td><?php echo $question['question_analysis_all']?></td>
						</tr>
					</tbody>
				</table>
			</span>
		</div>
	</div>
</volist>
<div id="alert" class="easyui-window" title="打回原因" style="width:400px;height:230px;padding:20px;" data-options="iconCls:'icon-save',modal:true,closed:true"> 
		<input type="hidden" id="qsn_id" name="qsn_id" value="">
    	<textarea id="reason" name="reason" cols="50" rows="3"></textarea>
    	<div><br><a id="btn" href="#" class="easyui-linkbutton" onclick="audit_question('',2);$('#alert').window('close');$('#alert').window('refresh')">确认打回</a></div>
</div>
<script type="text/javascript">
function audit_question(qid,status) {
	if(qid == ''){
		qid = $('#qsn_id').val();
	}
	if (qid) {
		var reason = '';
		if(status == 2){
			reason = $('#reason').val();
			if(reason == ''){
				$.messager.alert('提示信息', '打回原因不能为空', 'info');
				return false;
			}
		}
		$.messager.confirm('操作提示', '您确定要进行审核操作吗？', function (r) {
			if (r) {
				$.post('/Question/Audit/auditQuestion', { id:qid,status:status,reason:reason }, function (result) {
					if (result.status) {
						$.messager.alert('正确信息', '审核操作成功。','sucess');
						var $panel = $('#paper-list-panel');
						$panel.panel('refresh');
					} else {
						$.messager.alert('错误信息', '审核操作失败，请重新操作。', 'error');
					}
				}, 'json');
			}
		});
	} else {
		$.messager.alert('提示信息', '获取参数错误，审核操作失败', 'info');
	}
}
</script>