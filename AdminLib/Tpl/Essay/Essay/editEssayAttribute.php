<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/popup.js"></script>
<script type="text/javascript" src="/static/js/essay.js"></script>
<link href="/static/css/modular.css" type="text/css" rel="stylesheet" />
<link href="/static/css/essay.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
	<div id="main">
		<h2>编辑作文属性</h2>
		<div id="main_container" class="center model1 model"><br>
			<form method="post" name="form1" id="form1" action="<?php echo U('Essay/Essay/editEssayAttribute')?>" onsubmit="return checkInfo('<?php echo $essayInfo['essay_length'];?>','<?php echo $essayInfo['type_one'];?>','<?php echo $essayInfo['type_two'];?>','<?php echo $essayInfo['type_three'];?>','<?php echo $essayInfo['type_four'];?>','<?php echo $essayInfo['theme_name'];?>')"><br>
				<input type="hidden" id="classInfo" name="classInfo" value="<?php echo $essayInfo['class_name'].'|'.$essayInfo['class_code'].'|'.$essayInfo['campus_name'].'|'.$essayInfo['teacher_name'].'|'.$essayInfo['dtbegindate'].'|'.$essayInfo['dtenddate'].'|'.$essayInfo['sprinttime'].'|'.$essayInfo['speaker_number'];?>">	
				<input type="hidden" id="essayId" name="essayId" value="<?php echo $essayId;?>">
				<?php if(!empty($essayImgsThumb)):?>
				<div class="forms">
					<div id="preview" class="preview">
					<?php foreach($essayImgsThumb as $key=>$thumb):?>
						<li><a href="#" onclick="testMessageBox_show_essayImg(event,'<?php echo $thumb['url'];?>','<?php echo $thumb['show_width'];?>','<?php echo $thumb['show_height'];?>')"><img src="<?php echo $thumb['url'];?>" width="<?php echo $thumb['thumb_width'];?>" height="<?php echo $thumb['thumb_height'];?>"></a><div class="img_name"><?php echo $thumb['thumb_name'];?></div></li>
					<?php endforeach?>
					</div>
				</div>
				<?php endif;?>
				<h4>篇&nbsp;&nbsp;幅：</h4>
				<div class="parentTypes" id="legnth">
					<?php if(!empty($essayLengthArr)):?>
						<?php foreach($essayLengthArr as $key=>$essayLength):?>
							<li onclick="selectType(8,'<?php echo $essayLength;?>');$(this).addClass('bgcolor');" <?php if($essayInfo['essay_length'] == $essayLength || $session['essayLength']==$essayLength):?>class="bgcolor"<?php endif;?>><?php echo $essayLength;?></li>
						<?php endforeach?>
					<?php endif;?>
				</div>
				<div class="dashed"></div>
				<h4>类&nbsp;&nbsp;型：</h4>
				<div class="parentTypes" id="parent">
				<?php if(!empty($essayTypeList)):?>
					<?php foreach($essayTypeList as $key=>$essayType):?>
						<li onclick="selectType(1,'<?php echo $essayType['name'];?>');getChildren(this,'<?php echo $essayType['id'];?>','#child','<?php echo $essayType['top_id'];?>','<?php echo U('Essay/Essay/getChildren');?>',1);$(this).addClass('bgcolor');" <?php if((!empty($essayInfo['type_one']) && $essayType['name'] == $essayInfo['type_one']) ||(empty($essayInfo['type_one']) && $session['typeOne'] == $essayType['name'])):?>class="bgcolor"<?php endif;?>><?php echo $essayType['name'];?></li>
					<?php endforeach?>
				<?php endif;?>
				</div>
				<div class="dashed"></div>
				<div class="childTypes" id="child" <?php if(!empty($session['typeTwo'])):?>style="display:block"<?php endif;?>>
				<?php if(!empty($essayTypeTwoList) || !empty($session['typeTwo'])):?>
					<div style="width:120px;float:left;font-weight:bold"><?php if($essayInfo['type_one']=='记事的'):?>A、按内容分类：<?php else:?>&nbsp;<?php endif;?></div>
					<?php foreach($essayTypeTwoList as $key=>$essayTypeTwo):?>
						<li onclick="selectType(2,'<?php echo $essayTypeTwo['name'];?>');getChildren(this,'<?php echo $essayTypeTwo['id'];?>','#grandson','<?php echo $essayTypeTwo['top_id'];?>','<?php echo U('Essay/Essay/getChildren');?>',2);$(this).addClass('bgcolor');" <?php if($essayTypeTwo['name'] == $essayInfo['type_two'] ):?>class="bgcolor"<?php endif;?>><?php echo $essayTypeTwo['name'];?></li>
					<?php endforeach?>
					
				<?php endif;?>
				</div>
				<div class="dashed"></div>
				<div class="grandsonTypes" id="grandson">
				<?php if(!empty($essayTypeThreeList)||!empty($session['typeThree'])):?>
					<?php foreach($essayTypeThreeList as $key=>$essayTypeThree):?>
						<li onclick="selectType(3,'<?php echo $essayTypeThree['name'];?>');getChildren(this,'<?php echo $essayTypeThree['id'];?>','#four','<?php echo $essayTypeThree['top_id'];?>','<?php echo U('Essay/Essay/getChildren');?>',3);$(this).addClass('bgcolor');" <?php if($essayTypeThree['name'] == $essayInfo['type_three']):?>class="bgcolor"<?php endif;?>><?php echo $essayTypeThree['name'];?></li>
					<?php endforeach?>
				<?php endif;?>
				</div>
				<div class="dashed"></div>
				<div class="fourTypes" id="four">
				<?php if(!empty($essayTypeFourList) || !empty($session['typeFour'])):?>
					<?php foreach($essayTypeFourList as $key=>$essayTypeFour):?>
						<li onclick="selectType(4,'<?php echo $essayTypeFour['name'];?>');$(this).addClass('bgcolor');" <?php if($essayTypeFour['name'] == $essayInfo['type_four']):?>class="bgcolor"<?php endif;?>><?php echo $essayTypeFour['name'];?></li>
					<?php endforeach?>
				<?php endif;?>
				</div>
				<div class="dashed"></div>
				<div id="theme" style="clear:both;">
					<?php if(!empty($essayThemeList)):?>
					<div style="width:120px;float:left;font-weight:bold">B、按主题分类：</div>
					<div style="margin-left:50px;">
						<?php foreach($essayThemeList as $key=>$essayTheme):?>
							<li onclick="selectType(9,'<?php echo $essayTheme['name'];?>');$(this).addClass('bgcolor');" <?php if($essayTheme['name'] == $essayInfo['theme_name']):?>class="bgcolor"<?php endif;?>><?php echo $essayTheme['name'];?></li>
						<?php endforeach?>
					
					</div>
					<?php endif;?>
				</div>
				<div class="dashed"></div>
				<div>已选篇幅：<input type="input" id="essayLength" name="essayLength" value="<?php echo !empty($essayInfo['essay_length'])?$essayInfo['essay_length']:$session['essayLength'];?>"></div>
				<div class="dashed"></div>
				<div id="input_type">已选类型：<input type="input" id="typeOne" name="typeOne" value="<?php echo !empty($essayInfo['type_one'])?$essayInfo['type_one']:$session['typeOne'];?>">
					<input type="input" id="typeTwo" name="typeTwo" value="<?php echo !empty($essayInfo['type_two'])?$essayInfo['type_two']:'';?>">
					<input type="input" id="typeThree" name="typeThree" value="<?php echo !empty($essayInfo['type_three'])?$essayInfo['type_three']:'';?>">
					<input type="input" id="typeFour" name="typeFour" value="<?php echo !empty($essayInfo['type_four'])?$essayInfo['type_four']:'';?>">
				</div>
				<div id="input_theme" <?php if($essayInfo['theme_name']):?>style="display:block;"<?php endif;?>>已选主题：<input type="input" id="themeName" name="themeName" value="<?php echo $essayInfo['theme_name'];?>"></div>
				<div class="dashed"></div>
				<input type="submit" value=" 确定 " class="btn"><label class="error" id="return_msg"></label>
			</form>
		</div>
	</div>
</div>
</body>
</html>