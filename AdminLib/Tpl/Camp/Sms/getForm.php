<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>

<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/uploadify.css" type="text/css" rel="stylesheet" />
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />

</head>
<body >
<div region="center" >
<div id="main">
	<h2>添加班级</h2>
	<form id="form"  method="POST" enctype="multipart/form-data" action="/Camp/Sms/insert">

	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
	<tr>
		<td class="alt"><font color="red">*</font>年：</td>
		<td>
			<input type="radio" value="2014" name="nYear" onclick="chengeYear(this.value)" <?Php if($data['nClassYear'] == 2014) echo 'checked';?>>2014
			<input type="radio" value="2015" name="nYear" onclick="chengeYear(this.value)" <?Php if($data['nClassYear'] == 2015) echo 'checked';?>>2015


		</td>
	</tr>
	<tr>
		<td class="alt"><font color="red">*</font>学期：</td>
		<td>
			<input type="radio" value="3" name="nSeason" onclick="chengeSeason(this.value)" <?Php if($data['nSemester'] == 3) echo 'checked';?>>春
			<input type="radio" value="4" name="nSeason" onclick="chengeSeason(this.value)" <?Php if($data['nSemester'] == 4) echo 'checked';?>>夏
			<input type="radio" value="1" name="nSeason" onclick="chengeSeason(this.value)" <?Php if($data['nSemester'] == 1) echo 'checked';?>>秋
			<input type="radio" value="2" name="nSeason" onclick="chengeSeason(this.value)" <?Php if($data['nSemester'] == 2) echo 'checked';?>>冬


		</td>
	</tr>
	<tr>
		<td></td>
		<td><a href="javascript:void(0)" onclick="sch()" class="btn">筛选</a></td>
	</tr>


	<tr>
		<td class="alt"><font color="red">*</font>班级：</td>
		<td>
		<input type="hidden" name="nId" value="<?php echo $nId;?>">
		<!-- <input type="checkbox" onclick="selectall()">全选 -->
		<?php foreach($classList as $value):?>
			<input type="checkbox" onclick="getPhoneList('<?php echo trim($value['sclasscode']);?>')" class="sClassCode" id="<?php echo trim($value['sclasscode']);?>" value="<?php echo trim($value['sclasscode']);?>"  ><?php echo $value['sclassname'];?>

		<?php endforeach?>
		<hr>
		<p id="phones"></p>

		</td>

	</tr>
	<tr>
			<td class="alt"><font color="red">*</font>手机号： </td>
			<td>


				<textarea name="sPhones" id="sMessage" cols="32" rows="3"></textarea><font color="red">用逗号隔开</font>
			</td>
		</tr>

		<tr>
			<td class="alt"><font color="red">*</font>内容： </td>
			<td>


				<textarea name="sMessage" id="sMessage" cols="32" rows="3"></textarea><font color="red">请尽量保证发送内容在64字以内，短信末尾，系统自带【高思教育】，无需再添加。</font>
			</td>
		</tr>




		<tr>
			<td class="alt">&nbsp;</td>
			<td>
			   <button type="submit" class="btn">确认提交</button></td>
		</tr>
	</table>
	</form>
	<div id="remind" class="note">
	<div style="color:red">注意事项：</div>
		1. 注意事项<br>
		2. 注意事项；<br>

	</div>
	<br><br><br><br>
</div>
</div>
<script type="text/javascript">
	var i = 1;
	function selectall() {
		if(i == 1) {
			$(".sClassCode").attr("checked",true);
		}
		if(i == -1) {
			$(".sClassCode").attr("checked",false);
		}
		i = -1*i;

	}
	function getPhoneList(sClassCode) {
		var t = 3;
	//	alert($('#'+sClassCode).val());
		 result = $('#'+sClassCode).attr("checked")=="checked";
		//var result = $(this).checked;
		//alert(result);
		if(result == false) {

			deletePhoneList(sClassCode);
			return false;
		}

		//return false;
		 $.ajax({
                 url:'/Camp/Sms/getPhoneList',
                 type:'post',
                 data:{sClassCode:sClassCode},
                 dataType:'json',
                 success:function(data){
                 	//alert(data);return false;
                   var html = '<p  class="'+sClassCode+'">';
                   for(var i in data) {
                   	html += '<span ><input type="checkbox"   name="phones['+sClassCode+'][]" value="'+data[i]['sparents1phone']+'"  checked >'+data[i]['sstudentname']+'&nbsp</span>';

                   }
                   html += '</p>';
                   $('#phones').append(html);


                   return false;

                 }
        });
	}

	function deletePhoneList(sClassCode) {
		$('.'+sClassCode).remove();
		return false;
	}
	var nSeason = <?php echo $data['nSemester'];?>;
	var nYear = <?php echo $data['nClassYear'];?>;
	function chengeSeason(value) {
		nSeason = value;
		//alert(nSeason);
	}
	function chengeYear(value) {
		nYear = value;

	}
	function sch() {
		location.href = '/Camp/Sms/add/nYear/'+nYear+'/nSeason/'+nSeason;
	}
</script>
</body>
</html>
