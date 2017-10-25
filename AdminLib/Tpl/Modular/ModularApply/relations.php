<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<script type="text/javascript" src="/static/js/modular.js"></script>
<link href="/static/css/modular.css" type="text/css" rel="stylesheet" />	
</head>
<body>
<div region="center">
	<div id="main">
	<h2>设置属性对应关系：</h2>
	<div class="Snav center">
		<li ref="model1" id="step1" <?php if($mid):?>onclick="javascript:window.location.href='<?php echo U('Modular/ModularApply/step_one',array('mid'=>$mid))?>'"<?php endif;?>>1. 设置用户需填写的信息</li>
		<li  ref="model2" id="step2" <?php if($mid):?>onclick="javascript:window.location.href='<?php echo U('Modular/ModularApply/step_two',array('mid'=>$mid))?>'"<?php endif;?>>2. 设置模块属性</li>
		<li class="hover" ref="model3" id="step3" >3. 设置场次属性</li>
		<li ref="model4" id="step4" <?php if($mid):?>onclick="javascript:window.location.href='<?php echo U('Modular/ModularApply/step_four',array('mid'=>$mid))?>'"<?php endif;?>>4. 获取代码</li>
	</div>		
	<div class="clearit"></div>	
	<form name="formattr" action="<?php echo U('Modular/ModularApply/saverelations')?>" method="post">
	<input type="hidden" name="model3" value="true">
	<input type="hidden" id="mid" name="mid" value="<?php echo $mid?>">
	<?php 
	if(!empty($attrlist)){
	?>
	<table width="100%" border="1" class="tableForm" cellpadding="0" cellspacing="0">
		<tr>
		<?php 
			$rowCnt = 0;
			foreach($attrlist as $data) {
		?>
			 <td><?php echo $data['title'];?></td>
			<?php
			    if (!$rowCnt) { 
			        $rowCnt = count($data['attribute']);
			    }
			?>
			<?php } ?>
				    <!--<td>显示形式</td> -->
		 </tr>
			<?php 
			for($ii=0; $ii<$rowCnt; $ii++) { 
			    $i = 0;
			?>
		 <tr>
			<?php foreach($attrlist as $data) { ?>
			<?php if ($i == 0 ) { ?>
			      <td><?php echo $data['attribute'][$ii]['name'];?><input type="hidden"  name="title1[<?php echo $ii ?>]" value="<?php echo $data['attribute'][$ii]['aid'] ?>" /></td>
			<?php } else { ?>
			        <td>
			        <?php foreach($data['attribute'] as $attribute) { ?>
			        <input type="checkbox" name="display[<?php echo $data['fid']?>][<?php echo $ii ?>][]" id="display<?php echo $attribute['aid'] ?>"  value="<?php echo $attribute['aid'] ?>" onclick="display_to_require(this.id,0)">&nbsp; <?php echo $attribute['name'];?>
			        <?php } ?>
			        </td>
			    <?php } ?>
			<?php $i++; ?>
			<?php } ?>
			        </tr>
			<?php
			} 
			?>
		</table>
		<p>&nbsp;</p>
		<p><button class="btn">保存配置</button><span id="model1_msg"><?php echo $error;?></span></p>
		<?php 
			}
		?>
	</form>	
	</div>
</div>
</div>
</body>
</html>	    
