<style type="text/css">
#virtualUL {margin:10px 4px}
#virtualUL li{list-style-type:none;line-height:25px}
</style>
<div style="padding-left:40px;font-weight:bold;font-size:20px">现存虚拟人数：<b style="color:red"><?php echo $virtualTotal?></b>人</div>
<form id="<?php echo $virtualType?>_form" method="POST">
<input type="hidden" name="exam_id" value="<?php echo $examId?>" />
<input type="hidden" name="virtual_type" value="<?php echo $virtualType?>" />
<ul id="virtualUL">
	<li>成绩分值：<input size="18" class="vscore" type="text" name="score" id="score-<?php echo $key?>" style="ime-mode:disabled" onkeyup="clearRank();this.value=this.value.replace(/[^\d\.]/g, '')" value="<?php echo $virtualInfo['score']?>" /> <input type="button" onclick="doViewRank()" value="查看排名" /></li>
	<li>成绩排名：<input size="18" type="text" id="rank" value="" readonly="true" /></li>
	<li>虚拟人数：<input size="18" type="text" id="count" value="<?php echo $virtualInfo['score_cnt']?>" name="count" /></li>
</ul>
</form>
<script type="text/javascript">
function clearRank() {
	jQuery('#rank').val('');
}

function doViewRank(){
	var formData = jQuery('#<?php echo $virtualType?>_form').serialize();
	jQuery.post('<?php echo $vRankUrl?>', formData, function(data) {
		jQuery('#rank').val(data.virtual)
	}, 'json');
	
}
</script>