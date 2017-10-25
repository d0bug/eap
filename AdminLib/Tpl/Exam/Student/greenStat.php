<style type="text/css">
#statTable{border-left:1px solid #ddd;border-top:none}
#statTable th,#statTable td{border-bottom:1px solid #ddd;border-right:1px solid #ddd;padding:6px;white-space:nowrap}
#statTable th{background:#eee;text-align:center}
</style>
<div class="easyui-layout" fit="true">
	<div region="north" style="height:28px">
		<a href="javascript:void(0)" class="easyui-linkbutton" plain="true" onclick="exportStat<?php echo $groupId?>()" iconCls="icon-redo">导出统计表</a>
	</div>
	<div region="center">
		<div class="easyui-panel" fit="true" border="false" style="overflow:auto">
		<table cellpadding="0" cellspacing="0" id="statTable">
			<thead>
				<tr>
					<th rowspan="2">#</th>
					<?php foreach ($statArray[0] as $field=>$text):?>
						<th <?php if($field != 'area_name'){echo ' colspan="2"';}else{echo ' rowspan="2"';}?>><?php echo $text?></th>
					<?php endforeach;?>
				</tr>
				<tr>
					<?php foreach ($statArray[0] as $field=>$text):?>
						<?php if($field != 'area_name'):?>
						<th>今日</th>
						<th>总计</th>
						<?php endif;?>
					<?php endforeach;?>
				</tr>
			</thead>
			<tbody>
			<?php $i=1;
			foreach ($statArray as $key=>$stat):
				if(trim($key) != '0'):
					echo '<tr>';?>
					<td><?php echo sprintf('%02d', $i)?></td>
					<?php foreach ($stat as $field=>$value):?>
					<td <?php if(strstr($field, 'exam')){echo 'style="text-align:center"';} elseif (strstr($field, 'money')){echo 'style="text-align:right"';}?>>
					<?php echo $value?>
					</td>
					<?php 
					endforeach;
					$i++;
					echo '</tr>';
				endif;
			endforeach;?>
			</tbody>
		</table>
		</div>
	</div>
</div>
<script type="text/javascript">
function exportStat<?php echo $groupId?>() {
	window.open('<?php echo $exportUrl?>/gid/<?php echo $groupId?>');
}
</script>