<!doctype html>
<html>
<head>
    <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
    <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
	<script type="text/javascript">
	jQuery(function(){
		jQuery('#signupButton').click(function(){
			var selePos = jQuery('input[type=radio]:checked');
			if(0 == selePos.length) {
				alert('请选择要报名的考点');
			} else {
				var posCaption = selePos.attr('pos_caption');
				var posCode = selePos.val();
				var channel = jQuery('#channelName').val();
				if(confirm('确定要报名考点“' + posCaption + '”吗？')) {
					jQuery.post('<?php echo $signupExamUrl?>', {examId:<?php echo $examId?>, posCode:posCode, channel:channel}, function(data){
						if(data.errorMsg) {
							alert(data.errorMsg);
						} else {
							alert('报名成功');
							location="<?php echo $signupInfoUrl?>";
						}
					}, 'json');
				}
			}
		})
	})
	</script>

<style type="text/css">
dl{padding:0px;}
dd{border:1px solid #ddd;padding:5px;float:left;width:260px;height:70px;margin:3px 10px}
dd label{display:block;width:100%;height:100%}
div{font-size:12px}
</style>
<body>
<input type="button" id="signupButton" value="报名选定考点" style="width:50%;height:50px;font-weight:bold;color:red;font-size:17px" />
<select id="channelName" style="margin-top:-2px;padding:10px;font-size:20px;font-weight:bold;background:yellow">
<option value="陌拜电话">陌拜电话</option>
<option value="前台报名">前台报名</option>
</select>
<dl>
<?php foreach ($freePosList as $pos):?>
<dd><label>
<b><input type="radio" pos_caption="<?php echo $pos['pos_caption']?>" value="<?php echo $pos['pos_code']?>" name="pos_code" id="pos_<?php echo $pos['pos_code']?>" /><?php echo $pos['pos_caption']?></b>
<div style="padding-left:14px;margin-top:6px"><?php echo $pos['pos_addr']?></div>
</label>
</dd>
<?php endforeach;?>
</dl>

</body>
</html>