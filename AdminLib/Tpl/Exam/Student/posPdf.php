<!doctype html>
<html>
	<head>
	<style type="text/css">
	table{border-top:1px solid #000;border-left:1px solid #000}
	th,td{border-right:1px solid #000;border-bottom:1px solid #000;padding:8px;text-align:center}
	</style>
	</head>
	<body>
		<h2 style="text-align:center">
		【<?php echo $groupInfo['group_caption']?> - <?php echo $examInfo['exam_caption']?>】 - <?php echo $posInfo['pos_caption']?> - 考生统计表
		</h2>
		<table width="100%" cellpadding="1" cellspacing="0">
			<thead>
				<tr>
					<th>考场编号</th>
					<th>应到人数</th>
					<th>科目</th>
					<th>缺考人数</th>
					<th>临时考生</th>
					<th>实到人数</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($roomCountList as $cntInfo):?>
				<tr>
					<td rowspan="4" valign="middle"><br /><br /><?php if(false == $roomNames[$cntInfo['room_num']]):?><br /><?php endif?>第<b><?php echo sprintf('%02d', $cntInfo['room_num'])?></b>考场
					<?php if($roomNames[$cntInfo['room_num']]):?>
					<br />(<?php echo $roomNames[$cntInfo['room_num']]?>)
					<?php endif?></td>
					<td rowspan="4" valign="middle"><br /><br /><br /><b><?php echo $cntInfo['cnt']?></b></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr><td style="border:none" colspan="6">&nbsp;</td></tr>
				<?php endforeach;?>
			</tbody>
		</table>
	</body>
</html>