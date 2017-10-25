<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
<style type="text/css">
.clearfix{
	text-align: center;margin:0 auto;margin-top:10px;
}
.clearfix table{
	margin:0 auto;
}
.button{
	text-align: center;
	margin-top: 10px;
}
</style>
</head>
<body>
<div region="center">
<div id="main">
	<form id="date_form" action="<?php echo U('Vip/VipJiaoyan/dailyTarget_add')?>" method="GET">
	<div id="search" style="text-align: center;">
	<div id="error"></div>
		选择年月：<input type="text"  class="Wdate" id="date" name="date" value="<?php  echo $date; ?>" onClick="WdatePicker({ dateFmt:'yyyy-MM'})">
		&nbsp;&nbsp;<input type="submit" value="搜索">
	</div>
	</form>
	<hr>
	<form id="search_form" name="search_form" method="POST" action="">
	<input type="hidden" id="datetime" name="date" value="">
	<div id="list" class="clearfix">
		<?php if($userList[1]):?>
		<table width="80%" border="1" >
			<tr bgcolor="#dddddd" height=35>
				<th>姓名</th>
				<th>本月说导目标</th>
				<th>是否有效</th>
			</tr>
			<?php $i=1;foreach($userList[1] as $key=>$v):?>
			<tr height=30>
				<input type="hidden" name="user_email<?php  echo $i; ?>"  value="<?php echo $v['user_email'] ?>" >
				<input type="hidden" name="username<?php  echo $i; ?>"  value="<?php echo $v['user_name'] ?>">
				<input type="hidden" name="user_realname<?php  echo $i; ?>"  value="<?php echo $v['user_realname'] ?>">
				<input type="hidden" name="user_key<?php  echo $i; ?>"  value="<?php echo $v['user_key'] ?>">
				<td><?php echo $v['user_realname'] ?></td>
				<td><input type="text" name="target<?php echo $i; ?>" value="<?php echo $v['targetList']['target'] ?>"></td>
				<td>
				<select name="status<?php  echo $i; ?>">

				<option value="1" <?php if($v['targetList']['status'] == 1):?>selected<?php endif;?>>是</option>
				<option value="2" <?php if($v['targetList']['status'] == 2):?>selected<?php endif;?>>否</option>
				</select>
				</td>
			</tr>
			<?php $i++;endforeach?>
		</table>
		<div id="pageStr"><?php echo $showPage;?></div>
		<?php else:?>
		<div>暂无相关信息</div>
		<?php endif;?>
	</div>
	<div class="button">
	<input type="submit" value="保存">
	</div>
</form>
</div>
</div>
</body>
</html>
<script type="text/javascript">
$(document).ready(function() {
	$("#date_form").validate({
		errorPlacement:function(error,element)
		{
			error.appendTo($("#error")); 
		},
		rules: {
			date:{
				required:true
			},
		},
		messages:{
			date:{
				required:"选择年月"
			},
		}
	});
	$("#search_form").validate({
		rules: {
			target1: {
				required: true,
				number:true,
				min:0,
			},
			target2: {
				required: true,
				number:true,
				min:0,
			},
			target3: {
				required: true,
				number:true,
				min:0,
			},
			target4: {
				required: true,
				number:true,
				min:0,
			},
			target5: {
				required: true,
				number:true,
				min:0,
			},
			target6: {
				required: true,
				number:true,
				min:0,
			},
			target7: {
				required: true,
				number:true,
				min:0,
			},
			target8: {
				required: true,
				number:true,
				min:0,
			},
			target1: {
				required: true,
				number:true,
				min:0,
			},
			target9: {
				required: true,
				number:true,
				min:0,
			},
			target10: {
				required: true,
				number:true,
				min:0,
			},
		},
		messages: {
			target1: {
				required: '必填项',
				number:'必须是数字',
				min:'输入值不能小于0'
			},
			target2: {
				required: '必填项',
				number:'必须是数字',
				min:'输入值不能小于0'
			},
			target3: {
				required: '必填项',
				number:'必须是数字',
				min:'输入值不能小于0'
			},
			target4: {
				required: '必填项',
				number:'必须是数字',
				min:'输入值不能小于0'
			},
			target5: {
				required: '必填项',
				number:'必须是数字',
				min:'输入值不能小于0'
			},
			target6: {
				required: '必填项',
				number:'必须是数字',
				min:'输入值不能小于0'
			},
			target7: {
				required: '必填项',
				number:'必须是数字',
				min:'输入值不能小于0'
			},
			target8: {
				required: '必填项',
				number:'必须是数字',
				min:'输入值不能小于0'
			},
			target9: {
				required: '必填项',
				number:'必须是数字',
				min:'输入值不能小于0'
			},
			target10: {
				required: '必填项',
				number:'必须是数字',
				min:'输入值不能小于0'
			}
		},
		submitHandler: function(form) {
			var date=$("#date").val();
			$("#datetime").val(date);
			$.post("<?php echo U('vip/VipJiaoyan/dailyTarget_save')?>",
			$("#search_form").serialize(),
			function(data){
				var obj = eval('(' + data + ')');
				if(obj.status == 1){
					alert('保存成功');
					window.parent.closeWindow(1);
				}else{
					alert('保存失败');
				}
			}
			);
		}
	});
});

</script>