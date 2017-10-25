<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/vip.js"></script>
<script type="text/javascript" src="/static/js/jquery.qrcode.js"></script>
<script type="text/javascript" src="/static/js/qrcode.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<div id="search">
		<form  method="get">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
			<tr>
		    </tr>
		    <tr>
		   		<td style="text-align:center; margin:0 auto;">
		     		<h1>运营视频列表</h1>
		    	</td>
		    </tr>
		</table>
        <div>
            <input type="text" name="search" width="150px" value="<?php echo $search;?>" placeholder="输入视频名称关键词">
			<select id="video_type" name="video_type">
						<option value="<?php echo $seVideo_type['id'];?>" ><?php echo $seVideo_type['type_name'];?></option>
                        	<option value="0">Any</option>
						<?php foreach($videoTypeInfo as $val):?>
							<option value="<?php echo $val['id']?>"><?php echo $val['type_name']?></option>
						<?php endforeach;?>
                    </select>
            <input type="submit" name="submit" value="搜索" />
        </div>
		</form>
	</div>
	<div id="search" style="text-align:right; margin:0 auto;">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
			<td>
				   
			</td>
		</table>
	</div>
	<hr>
	<div id="list" class="clearfix">
		<?php if($vipOperationList):?>
		<table width="80%">
			<tr bgcolor="#dddddd" height=35>
				<td>&nbsp;&nbsp;&nbsp;ID号</td>	
                <td>视频类型</td>			
				<td>视频名称</td>
				<td>上传日期</td>
				<td>上传人</td>
                <td>视频简介</td>                
                <td>操作</td></td>
			</tr>
			<?php foreach($vipOperationList as $k=>$v):?>
			<tr height=30>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $v['id'];?></td>
                <td><span style="color: red;"><?php echo $v['video_type_name']?></span></td>
				<td><?php echo $v['title'];?></td>
				<td><?php echo $v['create_time'];?></td>
				<td><?php echo $v['create_name'];?></a></td>
				<td><?php echo  mb_substr($v['introduce'],0,20,'utf8')."....";?></td>               
                <td>
                    <a href="/vip/vipOperation/updateVideo/id/<?php echo $v['id']?>" style="color: #2262B7;">编辑</a>&nbsp;&nbsp;&nbsp;&nbsp;
                     <a href="#none" onclick="videoDelete(event,'<?php echo $v['id']?>')" style="color: #2262B7;">删除</a> &nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="#none" onclick="testMessageBox_playVideo(event,'<?php echo $v['id']?>','<?php echo $v['title']?>','<?php echo $v['introduce']?>','<?php echo $v['create_name']?>','<?php echo $v['create_time']?>','<?php echo $v['one_video_url']?>','<?php echo $v['up_type']?>' )" style="color: #2262B7;">查看</a> &nbsp;&nbsp;&nbsp;&nbsp;
                    <!--a href="#none" onclick="erweima(event,'<?php echo $v['video_url']?>')" style="color: #2262B7;">下载二维码</a-->
                    <a href="/vip/vipOperation/erweima/id/<?php echo $v['id']?>" style="color: #2262B7;">下载二维码</a>&nbsp;&nbsp;&nbsp;&nbsp;
                    <span id="code"></span>
                    
                </td>
			</tr>          
			<?php endforeach?>
		</table>
		<div id="pageStr">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $showPage;?></div>
		<?php else:?>
		<div>暂无相关信息</div>
		<?php endif;?>
	</div>
</div>
</div>
<script type="text/javascript">

function testMessageBox_playVideo(ev,id,title,introduce,create_name,create_time,one_video_url,up_type){
	var objPos = mousePosition(ev);
	messContent ="<div class=\"mesWindowsBox\" style=\"height:600px;\"><br>";
	messContent+="  <div class=\"left orange\">视频名称："+title+"</div>";
    messContent+="  <div class=\"left\">视频介绍："+introduce+"</div>";
	messContent+="  <div class=\"left\">上&nbsp;&nbsp;传&nbsp;&nbsp;人："+create_name+"</div>";
	messContent+="  <div class=\"left\">上传时间："+create_time+"</div>";
	//messContent+="  <iframe width=\"760\" height=\"550\" src=\""+one_video_url+"\" style=\"border:0px;\"></iframe>";
    //messContent+="<video width=\"700\" height=\"400\" controls > <source src=\""+one_video_url+"\" type='video/mp4 codecs=\"avc1.42E01E, mp4a.40.2\"'> </object></video>";
    if(up_type == 'mp3' || up_type == 'MP3') {
		messContent+="<embed autoplay=\"true\" src=\"" + one_video_url + "\"   width=\"500\" height=\"65\" />";
	}else if(up_type == 'mp4' || up_type == 'MP4'){
		messContent += "<video width=\"700\" height=\"400\" controls > <source src=\"" + one_video_url + "\" type='video/mp4 codecs=\"avc1.42E01E, mp4a.40.2\"'> </object></video>";
	}

	messContent+="</div>";
	showMessageBox("视频播放窗口",messContent,objPos,780,0);
}

function videoDelete(ev,id){    
		if(!confirm("您确定要删除吗？")){
			 return false;
		}
		var id = id;		
		var url = '/vip/vip_operation/delOperation';
		$.ajax({
		    type:'POST',
		    url:url,
		    data:{id:id},
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
}


</script>
</body>
</html>
