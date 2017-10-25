<tr>
	<td class="wd_120 alt right"><span class="red">*</span> 题干：</td>
	<td colspan="3"> 
		<textarea id="content{$suffix}" name="content" class="editor">{$question['content']}</textarea>
	</td>
</tr>	
<tr>
	<td class="alt right">
		<span class="red">*</span> 正确答案：
	</td>
	<td class="left" colspan="3">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tbody> 
				<tr>
					<td>
						<label><input type="radio" <if condition="$options[0]['is_answer'] == 1">checked="checked"</if> id="options_answer_flag_{$suffix}_c" name="options_answer_flag[]" value="0" class="isanswer" /></label> <input type="text" id="options_{$suffix}_c" name="options[]" value="<notempty name="options">{$options[0]['ocontent']}<else />对</notempty>" class="input wd_120 options" />
					</td>
				</tr>
				<tr>
					<td>
						<label><input type="radio" <if condition="$options[1]['is_answer'] == 1">checked="checked"</if> id="options_answer_flag_{$suffix}_w" name="options_answer_flag[]" value="1" class="isanswer" /></label> <input type="text" id="options_{$suffix}_w" name="options[]" value="<notempty name="options">{$options[1]['ocontent']}<else />错</notempty>" class="input wd_120 options" />
					</td>
				</tr>
				<tr>
					<td>
						<textarea id="answers{$suffix}" name="answers[]" class="editor">{$answer[0]['content']}</textarea>
					</td>
				</tr>
			</tbody>
		</table>
	</td>
</tr>
<tr>
	<td class="alt right" style="border: 0px">
		<span class="red">*</span> 解析：
	</td>
	<td class="left" colspan="3" style="border: 0px">
		<textarea id="analysis{$suffix}" name="analysis" class="editor">{$question['analysis']}</textarea>
	</td>
</tr>