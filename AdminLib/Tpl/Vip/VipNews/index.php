<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
<div id="main">
<h2>资讯列表</h2>
<div class="left_newsList">
<?php foreach($articleType as $key=>$type):?>
	<div id="<?php echo $key;?>">
		<h3><?php echo $type;?>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo U('Vip/VipNews/index',array('ntype'=>$key))?>">更多</a></h3>
		<ul>
		<?php foreach($newslist[$key] as $k=>$new):?>
			<li>· <a href="<?php echo U('Vip/VipNews/index',array('nid'=>$new['nid']))?>"><?php echo $new['title'];?></a><span><?php echo $new['instime'];?></span></li>
		<?php endforeach?>
		</ul>
	</div>
<?php endforeach?>
</div>

<div class="right_newsContent">
	<div class="newsBox">
<?php  if(empty($ntype)):?>
	<?php if(!empty($detail_news)):?>
		<div class="title" class="title center"><?php echo $detail_news['title']?></div>
		<div class="instime" class="center">来源：高思教育&nbsp;&nbsp;&nbsp;&nbsp;作者：<?php echo $detail_news['user_name']?>&nbsp;&nbsp;&nbsp;&nbsp;发布时间：<?php echo $detail_news['instime'];?></div>
		<div class="content" style="word-break: break-all;">
			<?php echo $detail_news['ncontent'];?>
		</div>
	<?php else:?>
		暂无相关资讯信息
	<?php endif;?>
<?php else:?>
	<?php if($detail_newsList):?>
		<div class="list">
			<h3><?php echo $articleType[$ntype];?></h3>
			<ul>
				<?php foreach($detail_newsList as $key=>$new):?>
				<li>· <a href="<?php echo U('Vip/VipNews/index',array('nid'=>$new['nid']))?>"><?php echo $new['title'];?></a><span><?php echo $new['instime'];?></span></li>
				<?php endforeach?>
			</ul>
		</div>
	<?php else:?>
		<div>暂无相关资讯信息</div>
	<?php endif;?>
		<div id="pageStr"><?php echo $showPage;?></div>
<?php endif;?>
	</div>
</div>
</div>

</div>
</body>
</html>