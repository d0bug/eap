<!doctype html>
<html>
<head>
    <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
    <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
    <script type="text/javascript">
    jQuery(function(){
    	jQuery('#examGrid').datagrid({
    		onSelect:function(idx,data){
    			jQuery.post('<?php echo $dispatchUrl?>', {examId:data.exam_id}, function(url){
    				jQuery('#mainFrm').attr('src', url);
    			})
    		}
    	})
    })
    </script>
    <style type="text/css">
		fieldset{height:120px;}
		ul{margin:0px;padding:0px}
		li{font-weight:bold;padding:5px;border-bottom:1px dashed #ccc;width:45%;float:left;margin-left:10px;list-style-type:none}
		li span{font-weight:normal}
		div{font-size:12px}
	</style>
</head>
<body class="easyui-layout" fit="true" border="false">
	<div region="north" title="竞赛报名" style="height:200px" iconCls="icon-redo" border="false">
		<div class="easyui-layout" fit="true" border="false">
			<div region="north" style="height:30px">
			<div class="operator" style="line-height:30px;padding-left:20px;background:#eee">
				操作员：<b><?php echo $operInfo['sname']?></b>
			</div>
			</div>
			<div region="west" style="width:450px" border="false">
			<fieldset>
				<legend>学员信息</legend>
				<ul>
					<li><span>姓名：</span><?php echo $stuInfo['sname']?></li>
					<li><span>编码：</span><?php echo $stuInfo['scode']?></li>
					<li><span>学号：</span><?php echo $stuInfo['saliascode']?></li>
					<li><span>密码：</span><?php echo $stuPwd ? $stuPwd : '学员自设，无法读取'?></li>
					<li><span>年级：</span><?php echo $stuInfo['sgrade']?></li>
				</ul>
			</fieldset>
			</div>
			<div region="center" border="false">
			<fieldset>
				<legend>可报竞赛列表</legend>
				<div class="easyui-layout" fit="true" border="false">
					<div region="center" border="false">
					<table id="examGrid" class="easyui-datagrid" fit="true" singleselect="true" rownumbers="true">
						<thead>
						<tr>
						<th field="exam_id" checkbox="true"></th><th field="exam_caption">竞赛名称</th><th field="signup_status">报名状态</th>
						</tr>
						</thead>
						<tbody>
						<?php foreach ($stuExams as $exam):?>
						<td><?php echo $exam['exam_id']?></td>
						<td><?php echo $exam['exam_caption']?></td>
						<td><?php echo $exam['signup_status']?></td>
						<?php endforeach;?>
						</tbody>
					</table>
					</div>
					<div region="south" style="height:10px;background:none" border="false"></div>
				</div>
			</fieldset>	
			</div>
		</div>
	</div>
	<div region="center">
		<iframe width="100%" height="100%" id="mainFrm" name="mainFrm" frameborder="no" style="border:none"></iframe>
	</div>
</body>
</html>