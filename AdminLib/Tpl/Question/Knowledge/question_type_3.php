<tr>
	<td class="alt right wd_120"><span class="red">*</span> 题干：</td>
	<td colspan="3">
		<textarea id="question_content_{$suffix}" name="content" class="editor">{$question['content']}</textarea>
	</td>
</tr>	
<tr>
	<td class="alt right">
		<span class="red">*</span> 正确答案：
	</td>
	<td class="left" colspan="3">
		<textarea id="question_answers_{$suffix}" name="answers[]" class="editor">{$answer[0]['content']}</textarea>
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