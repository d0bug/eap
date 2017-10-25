<script type="text/javascript">
function awCaption(){
	return '<?php echo $awardInfo['award_caption']?>';
}
</script>
<table class="easyui-datagrid" url="<?php echo $jsonStuList?>/id/<?php echo $awardId?>" fit="true" pagination="true" border="false" singleselect="true" rownumbers="true">
	<thead>
		<tr>
			<th field="sname">考生姓名</th>
			<th field="stu_code">考生编码</th>
			<th field="saliascode">考生学号</th>
			<th field="exam_code">准考证号</th>
			<th field="pos_code">考点编码</th>
			<th field="stu_mobile">联系电话</th>
			<th field="<?php echo $awardInfo['award_type']?>_score">获奖成绩</th>
			<th field="award_caption" formatter="awCaption">获得奖项</th>
		</tr>
	</thead>
</table>