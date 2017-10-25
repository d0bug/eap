<!doctype html>
<html>
	<head>
	<style type="text/css">
	th,td{text-align:center;}
	</style>
	</head>
	<body>
	<h2>【<?php echo $examInfo['exam_caption']?>】 - <?php echo $posInfo['pos_caption']?> — <?php echo sprintf('%02d', $roomNum)?>考场名单</h2>
	<table bgcolor="#000000" width="100%" cellspacing="1" cellpadding="5">
		<thead>
			<tr bgcolor="#eeeeee"><th>考生姓名</th><th>学号</th><th>考生年级</th><th>准考证号</th><th>考场号</th><th>座位号</th><th>缺考标识</th></tr>
		</thead>
		<tbody>
			<?php foreach ($stuArray as $stu):?>
			<tr bgcolor="#ffffff">
				<td><?php echo $stu['stu_name']?></td>
				<td><?php echo $stu['saliascode']?></td>
				<td><?php echo $gradeArray[$stu['ngrade1year']]?></td>
				<td><?php echo $stu['exam_code']?></td>
				<td><?php echo sprintf('%02d', $stu['room_num'])?></td>
				<td><?php echo sprintf('%02d', $stu['seat_num'])?></td>
				<td>&nbsp;</td>
			</tr>
			<?php endforeach;?>
		</tbody>
	</table>
	</body>
</html>