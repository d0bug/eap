<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/modular.js"></script>
<link href="/static/css/modular.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
	<div id="main">
		<h2>在线报名、预约模块化后台——统计数据</h2>
		<p><form id="search" name="search" method="GET" action="<?php echo U('Modular/ModularApply/preview',array('mid'=>$mid))?>">
			所属活动：<?php echo $mname?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			关键词搜索：<input type="text" name="keyword" id="keyword" value="<?php echo $keyword;?>"> 
					  <input type="hidden" name="mid" value="<?php echo $mid;?>">
					  <input type="submit" value=" 搜索 ">
			</form>
			
		</p>
		<form id="deleteForm" name="deleteForm" method="POST" action="<?php echo U('Modular/ModularApply/deleteData',array('mid'=>$mid))?>">
		<table class="tableList" border="0" cellpadding="0" cellspacing="0"  width="90%" id="apply_table">
			<tr>
				<th>序号</th>
				<th>姓名</th>
				<th>性别</th>
				<th>学校</th>
				<th>年级</th>
				<th>学科</th>
				<th>Email</th>
				<th>手机号</th>
				<th>留言</th>
				<th>添加时间</th>
				<?php 
				/****获取场次属性名称*******/
				if(!empty($Attrname)){
					foreach ($Attrname as $k=>$v){
						echo '<th>'.'预约'."<span style='color:red;'>".$v['title2']."</span>".'</th>';
					}
				}
				?>
			</tr>
			<?php foreach($dataList as $key=>$data):?>
			<tr>
				<td><input type="checkbox" name="deleteId[]" value="<?php echo $data['id']?>">&nbsp;<?php echo $key+1?></td>
				<td>&nbsp;<?php echo $data['name']?></td>
				<td>&nbsp;<?php echo $sexArr[$data['sex']]?></td>
				<td>&nbsp;<?php echo $data['school']?></td>
				<td>&nbsp;<?php echo $gradeArr[$data['grade']]?></td>
				<td>&nbsp;<?php echo $subjectArr[$data['dept']]?></td>
				<td>&nbsp;<?php echo $data['email']?></td>
				<td>&nbsp;<?php echo $data['phone']?></td>
				<td>&nbsp;<?php echo $data['message']?></td>
				<td>&nbsp;<?php echo date('Y-m-d H:i:s',$data['instime']);?></td>
				<?php 
				$atrr = unserialize($data['attrname']);
				$attrname = explode('_',$atrr);
				foreach($attrname as $name){
				?>
				<td>&nbsp;<?php echo $name;?>	</td>
				<?php 
				}
				?>
			</tr>
			<?php endforeach?>
		</table>
		<?php if($dataList):?><div id="pageStr">&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="checkAll_data" id="checkAll_data" value="1" >全选<input type="submit" value="确定删除" onclick="return confirm('确定要删除选中记录吗？')">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $showPage;?><a href="<?php echo U('Modular/ModularApply/export_excel',array('mid'=>$mid,'keyword'=>$keyword));?>" class="btn">下载</a></div><?php endif;?>
		</form>
	</div>
</div>
</body>
</html>