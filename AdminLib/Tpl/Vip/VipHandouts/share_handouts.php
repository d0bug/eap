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
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
	<div id="search">
	<form id="search_form" name="search_form" method="GET" action="<?php echo U('Vip/VipHandouts/share_handouts',array('style'=>$list_style));?>">
		<input type="text" id="keyword" name="keyword" size="40" value="<?php if($handouts_keyword):?><?php echo urldecode($handouts_keyword);?><?php endif;?>" placeholder="输入讲义名称">&nbsp;&nbsp;<input type="submit" value="  搜索  ">
	</form>
	</div>
	<div id="list">
		<h2>共享讲义&nbsp;&nbsp;&nbsp;&nbsp;
			<span>
				<a href="<?php echo U('Vip/VipHandouts/share_handouts',array('style'=>'list','keyword'=>urldecode($handouts_keyword),'p'=>$curPage));?>" class="f_14 <?php if($list_style=='list'):?>bold<?php endif;?>">列表</a> | 
				<a href="<?php echo U('Vip/VipHandouts/share_handouts',array('style'=>'img','keyword'=>urldecode($handouts_keyword),'p'=>$curPage));?>" class="f_14 <?php if($list_style=='img'):?>bold<?php endif;?>">图标</a>
			</span>
		</h2>
		<?php if($handoutsList):?>
			<?php if($list_style == 'img'):?>
				<ul>
					<?php foreach($handoutsList as $key=>$handouts):?>
					<li>
						<div class="pic"><a href="#" onclick="testMessageBox_handouts_detail(event,<?php echo $handouts['hid']?>,'<?php echo U('Vip/VipHandouts/show_detail_handouts')?>',1);"><img src="<?php if($handouts['picture']):?><?php echo $handouts['picture'];?><?php else:?>/static/images/default.gif<?php endif;?>" style="border:0px;height:100px;"></a></div>
						<div class="txt"><a href="#" onclick="testMessageBox_handouts_detail(event,<?php echo $handouts['hid']?>,'<?php echo U('Vip/VipHandouts/show_detail_handouts')?>',1)" title="<?php echo $handouts['title'];?>"><?php echo $handouts['title'];?><a></div>
						<div class="ac">
							<?php if($handouts['teacher_preview']==1):?>
								<a href="#" onclick="testMessageBox_handouts_preview(event,'<?php echo U('Vip/VipHandouts/view_handouts_pdf',array('hid'=>$handouts['hid'],'type'=>'teacher'))?>');">预览讲义</a>
							<?php else:?>
								<a href="#" style="color:#cccccc">无预览版</a>
							<?php endif;?><em> | </em>
							<a href="<?php echo U('Vip/VipHandouts/download',array('hid'=>$handouts['hid'],'type'=>1));?>">下载讲义</a>
						</div>
					</li>
					<?php endforeach?>
				</ul>
			<?php else:?>
				<table width="80%" border="1">
					<tr bgcolor="#dddddd" height=35>
						<td width="50%">讲义标题</td>
						<td>讲义文档</td>
						<td>上传时间</td>
						<td>操作</td>
					</tr>
					<?php foreach($handoutsList as $key=>$handouts):?>
					<tr height=30>
						<td><a href="#" onclick="testMessageBox_handouts_detail(event,<?php echo $handouts['hid']?>,'<?php echo U('Vip/VipHandouts/show_detail_handouts')?>',1);" title="<?php echo $handouts['title'];?>"><?php echo $handouts['title'];?></a></td>
						<td >
							<?php if(!empty($handouts['teacher_version'])):?>
								<a href="#" onclick="testMessageBox_handouts_preview(event,'<?php echo U('Vip/VipHandouts/view_handouts_pdf',array('hid'=>$handouts['hid'],'type'=>'teacher'))?>');" class="orange">预览讲义</a>
							<?php else:?>
								<a href="#" style="color:#cccccc">无预览版</a>
							<?php endif;?><em> | </em>
							<a href="<?php echo U('Vip/VipHandouts/download',array('hid'=>$handouts['hid'],'type'=>1));?>" class="orange">下载讲义</a>
						</td>
						<td><?php echo date('Y-m-d H:i:s',$handouts['instime']);?></td>
						<td>
							<a href="#" onclick="testMessageBox_handouts_detail(event,<?php echo $handouts['hid']?>,'<?php echo U('Vip/VipHandouts/show_detail_handouts')?>',1);" title="<?php echo $handouts['title'];?>" >查看</a>&nbsp;&nbsp;&nbsp;&nbsp;
							<a href="<?php echo U('Vip/VipHandouts/do_favorite',array('act'=>'add','hid'=>$handouts['hid'],'type'=>$handouts['type'],'style'=>$list_style));?>" class="orange">收藏</a>
						</td>
					</tr>
					<?php endforeach?>
				</table>
			<?php endif;?>
	<?php else:?>
		<div>暂无相关讲义信息</div>
	<?php endif;?>
	<div id="pageStr"><?php echo $showPage;?></div>
	</div>
</div>
</div>
</body>
</html>