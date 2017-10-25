<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<link href="/static/css/uploadify.css" type="text/css" rel="stylesheet" />
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>

<script src="http://gosspublic.alicdn.com/aliyun-oss-sdk-4.4.4.min.js"></script>
<script type="text/javascript" src="/static/js/video.js"></script>
<body>
<form id="add_video" name="add_video" method="POST" enctype="multipart/form-data"  action="<?php echo U('Vip/VipManagement/trainTestStatus',array('auto_close'=>$auto_close));?>">
<div id="main">
		<h2>考核状态</h2>
		
		<input type="hidden" id="upload_url" name="upload_url" value="<?php echo U('Vip/VipManagement/upload_file')?>">
		<input type="hidden" id="del_url" name="del_url" value="<?php echo U('Vip/VipManagement/del_object')?>">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
			<tr>
				<td class="alt" valign="top">开始考核
                    <?php if($teInfo['kh_status'] == '1'){?>
                    <input type="radio" name="kaohe" id="kaohe" value="1" checked  disabled="disabled">
                    <?php }else{?>
                        <input type="radio" name="kaohe" id="kaohe" value="1" >
                    <?php }?>
                </td>
                <td class="alt" valign="top">结束考核
                    <input type="radio" name="kaohe" id="kaohe" value="2"> </td>
			</tr>
			<tr>
				<td class="alt">&nbsp;</td>
				<td>
                    <input type="hidden" name="tid" id="tid" value="<?php echo $teInfo['id']?>">
					<input type="hidden" name="kid" id="kid" value="<?php echo $kid?>">
                    <input type="hidden" name="ktitle" id="ktitle" value="<?php echo $title?>">
				    <input type="hidden" name="action" value="insert">
                    <button class="btn   js-insertBtn" type="button">保存</button>

				</td>
			</tr>

        </table>
	</div>
</form>
<script>
    var $mEdit=$('.js-insertBtn');
    //状态更改
    $mEdit.on('click',function (e) {
        var kid = $("#kid").val();
        //var ktitle = $("#ktitle").val();
        var tid = $("#tid").val();
        obj = document.getElementsByName("kaohe");
        check_val = [];
        for(k in obj){
            if(obj[k].checked)
                check_val.push(obj[k].value);
        }
        var kaohe = check_val;
        if(kaohe == '1' ){
            if(!confirm("<?php echo $ktitle;?>确认后微信端即可进行答题")){
                return false;
            }
        }else if(kaohe == '2' ){
            if(!confirm("<?php echo $ktitle;?>停止考核后微信端不可在答题，成绩单不在接收！")){
                return false;
            }
        }
        var url = '/vip/vip_management/trainTestStatus';
        $.ajax({
            type:'POST',
            url:url,
            data:{kaohe:kaohe,kid:kid,tid:tid},
            dataType: 'json',
            success:function(data) {
                if(data.status ==1 ){
                    alert(data.msg);
                    window.location.reload();
                }else if(data.status==0 || data.status==2){
                    alert(data.msg);
                } else{
                    alert('出现错误！');
                }
            },
            error : function() {
                alert("异常！");
            }
        });


    });
</script>
</body>


