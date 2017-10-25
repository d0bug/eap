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
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>

<script type="text/javascript" src="/static/js/jquery.validate.js"></script>

<script type="text/javascript" src="/static/js/jquery.uploadify-3.1.min.js"></script>

<script type="text/javascript" src="/static/js/use_uploadify.js"></script>

<script type="text/javascript" src="/static/js/video.js"></script>
<script src="http://gosspublic.alicdn.com/aliyun-oss-sdk-4.4.4.min.js"></script>
<script type="text/javascript" src="/static/js/video.js"></script>
<script type="text/javascript">
$(function() {
	
	$("#add_video").validate({
		
		rules: {
			
			video_url: {
				
				required: true
			
			},
            
            video_type: {
                required: true
                
            },
			
			title: {
				
				required: true
			
			},
			
			introduce: {
				
				required: true,
				
				maxlength: 2500
			
			}
		
		},
		
		messages: {
			
			video_url: {
				
				required: '请上传视频'
			
			},
            
            video_type: {
				
				required: '请选择视频类型'
			
			},
			
			title: {
				
				required: '请填写视频名称'
			
			},
			
			introduce: {
				
				required: '请填写视频介绍',
				
				maxlength: '视频介绍不超过2500字'
			
			}
		
		}
	
	});
	

})
</script>
<body>
<div id="main">
<form id="add_video" name="add_video" method="POST" enctype="multipart/form-data"  action="<?php echo U('Vip/VipOperation/OperationUpload',array('auto_close'=>$auto_close));?>">

		<h2>视频上传</h2>
		
		<input type="hidden" id="upload_url" name="upload_url" value="<?php echo U('Vip/VipOperation/upload_file')?>">
		<!-- <input type="hidden" id="del_url" name="del_url" value="<?php echo U('Vip/VipOperation/del_file')?>"> -->
		<input type="hidden" id="del_url" name="del_url" value="<?php echo U('Vip/VipOperation/del_object')?>">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
			<tr>
				<td class="alt" valign="top"><font color="red">*</font>选择上传视频： </td>
				<td valign="top">
					<input type="file" id="file" value="" /><span style="color: red;">注：只能上传MP3格式音频和MP4格式视频！</span><br>
					<span id="view_video" class="view_file">
					</span>
					<!--input type="hidden" id="video_url" name="video_url" value=""-->
					<div class="t_right">&nbsp;</div>
				</td>
			</tr>
            <tr>
                <td class="alt"><font color="red">*</font>视频类型</td>
                <td>
                    <select id="video_type" name="video_type">
                        <option value="0">Any</option>
						<?php foreach($videoTypeInfo as $val):?>
							<option value="<?php echo $val['id']?>"><?php echo $val['type_name']?></option>
						<?php endforeach;?>
                    </select>
                </td>
            </tr>
			<tr>
				<td class="alt"><font color="red">*</font>视频名称： </td>
				<td>
					<input type="text" id="title" name="title" placeholder="请输入视频名称..." value="" size="100"  onkeydown="return check_length('title','titleMsg',100)" onkeyup="return check_length('title','titleMsg',100)"><span id="titleMsg">还可输入100个字</span>
				</td>
			</tr>
		
			<tr>
				<td class="alt"><font color="red">*</font>视频介绍:</td>
				<td>
					<textarea id="introduce" name="introduce" cols="70" rows="5" placeholder="请输入视频介绍..."></textarea>(2500字以内)
					<label id="introduce_msg" class="error"></label>
				</td>
			</tr>
			<tr>
				<td class="alt">&nbsp;</td>
				<td>
				    <input type="hidden" name="action" value="insert"><input type="hidden" id="hid" name="hid" value="">
				    <input type="submit" class="btn" value="确认提交">
				</td>
			</tr>
	

</form>
</div>


  
  <script type="text/javascript">  
    var client = new OSS.Wrapper({
      region: '<?=$region?>',
      accessKeyId: '<?=$accessKeyId?>',
      accessKeySecret: '<?=$accessKeySecret?>',
      bucket: '<?=$bucket?>'
    });
    document.getElementById('file').addEventListener('change', function (e) {
      var file = e.target.files[0];   
      var timestamp = Date.parse(new Date());
		timestamp = timestamp / 1000;
      var storeAs =  timestamp +'-'+ file.name;   
      //var storeAs = file.name;
      if(storeAs){
         $("#view_video").html("<span style='color:blue;'>文件较大，正在上传中，请稍等.......</span");
      }
      //console.log(file.name + ' => ' + storeAs);
      client.multipartUpload(storeAs, file).then(function (result) {
        //alert(result.name);exit;
        var video_url = result.name;        
        $("#view_video").html("<span style='color:red;'>上传完成！<input type='hidden' id='video_url' name='video_url' value='"+video_url+"'></span");
        
       // console.log(result);
      }).catch(function (err) {
        //console.log(err);
        alert("上传失败，请重新上传！");
      });
    });
    
  </script>
</body>


