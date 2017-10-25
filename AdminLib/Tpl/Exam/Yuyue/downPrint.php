<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
        <script type="text/javascript">
        </script>
    </head>
    <body class="easyui-layout" fit="true">
    <table class="easyui-datagrid" rownumbers="true" fit="true">
    	<thead>
    		<tr>
    			<th field="ygroup_caption">诊断组</th>
    			<th field="group_caption">打印任务名称</th>
    			<th field="total_cnt">考生总数</th>
    			<th field="print_status" align="center">打印状态</th>
    			<th field="ytime_start">预约起始时间</th>
    			<th field="ytime_end">预约结束时间</th>
    			<th field="download">下载</th>
    		</tr>
    	</thead>
    	<tbody>
    	<?php foreach ($printGroups as $group):?>
    		<tr>
    			<td><?php echo $group['ygroup_caption']?></td>
    			<td><?php echo $group['group_caption']?></td>
    			<td><?php echo $group['total_cnt']?></td>
    			<td><?php echo $group['print_cnt'] == $group['total_cnt'] ? '已完成' : '未完成'?></td>
    			<td><?php echo $group['ytime_start']?></td>
    			<td><?php echo $group['ytime_end']?></td>
    			<td><a href="?gid=<?php echo $group['group_id']?>" target="_blank">下载</a></td>
    		</tr>
    	<?php endforeach;?>
    	</tbody>
    </table>
    </body>
</html>