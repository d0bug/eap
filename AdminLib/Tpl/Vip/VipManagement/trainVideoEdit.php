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
<form id="add_video" name="add_video" method="POST" enctype="multipart/form-data"  action="<?php echo U('Vip/VipManagement/trainVideoEdit',array('auto_close'=>$auto_close));?>">
<div id="main">
		<h2>视频上传</h2>
		
		<input type="hidden" id="upload_url" name="upload_url" value="<?php echo U('Vip/VipManagement/upload_file')?>">
		<input type="hidden" id="del_url" name="del_url" value="<?php echo U('Vip/VipManagement/del_object')?>">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
			<tr>
				<td class="alt" valign="top"><font color="red">*</font>选择上传视频： </td>
				<td valign="top">
					<input type="file" id="file" value="" /> <span style="color: red;">注：只能上传MP4格式视频！</span><br>
                    <span id="old_video">
                        原上传视频：<?php echo $videoInfo['video_url'];?>
                        <input type="hidden" name="id" value="<?php echo $vid;?>">
                        <input type="hidden" name="old_video" value="<?php echo $videoInfo['video_url'];?>">
                        <input type="hidden" name="old_one_video" value="<?php echo $videoInfo['one_video_url'];?>">
                    </span>
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
                        <option value="<?php echo $videoInfo['video_type']?>" ><?php echo $videoInfo['video_type'];?></option>
                        <option value="0">Any</option>
                        <option value="培训">培训</option>
                    </select>
                </td>
            </tr>
			<tr>
				<td class="alt"><font color="red">*</font>视频名称： </td>
				<td>
					<input type="text" id="title" name="title" placeholder="请输入视频名称..." value="<?php echo $videoInfo['title']?>" size="100">
				</td>
			</tr>
			<tr>
				<td class="alt">&nbsp;</td>
				<td>
				    <input type="hidden" name="action" value="insert"><input type="hidden" id="hid" name="hid" value="">
				    <input type="submit" class="btn" value="确认提交">
				</td>
			</tr>
	</div>
</form>

  <script type="text/javascript">  
    var client = new OSS.Wrapper({
      region: '<?=$region?>',
      accessKeyId: '<?=$accessKeyId?>',
      accessKeySecret: '<?=$accessKeySecret?>',
      bucket: '<?=$bucket?>'
    });
    document.getElementById('file').addEventListener('change', function (e) {
      var file = e.target.files[0];      
      var storeAs = file.name;
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


