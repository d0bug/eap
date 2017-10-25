<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php' ?>
        <script type="text/javascript">
        
        
        function save(saveType) {
        	jQuery('#saveType').val(saveType);
        	var formData = jQuery('#cfgForm').serialize();
        	jQuery.post('<?php echo $saveCfgUrl?>', formData, function(data){
        		if(data.errorMsg) {
        			alert(data.errorMsg);
        		} else {
        			loadImg();
        		}
        	}, 'json');
        }
        
        
        function loadImg() {
        	jQuery.post('<?php echo $previewUrl?>', {examId:<?php echo abs($examId)?>}, function(html){
        		jQuery('.ruler').html(html);
        	})
        }
        
        function switchStatus() {
        	jQuery.post('<?php echo $statusUrl?>', {examId:<?php echo abs($examId)?>}, function(data){
        		if(data.errorMsg) {
        			alert(data.errorMsg);
        		} else {
        			alert('证书模板状态设置成功');
        			jQuery('#statusButton').val(data.status);
        		}
        	}, 'json');
        }
        
        jQuery(function(){
        	loadImg()
        })
        </script>
        <style type="text/css">
		form{margin:2px}
		legend{font-weight:bold;color:blue;}
		.examCaption{margin-bottom:3px;font-weight:bold;background:#ddd;padding:3px;border:1px solid #ccc;font-size:17px;padding-left:10px;color:red}
		.ruler{padding-top:28px;padding-left:28px;background:url(/images/ruler.png) no-repeat;height:740px}
		</style>
    </head>
    <body class="easyui-layout" fit="true" border="false">
    	<div region="west" style="width:400px">
    		<div class="easyui-layout" fit="true" border="false">
    			<div region="north" title="竞赛信息" style="height:150px" iconCls="icon-redo">
    				<form method="POST" enctype="multipart/form-data" action="<?php echo $upTplUrl?>" target="hdFrame">
    					<input type="hidden" name="exam_id" value="<?php echo $examId?>" />
    					<div class="examCaption"><?php echo $examInfo['group_caption']?> - <?php echo $examInfo['exam_caption']?></div>
    					<fieldset>
    					<legend>证书模板上传</legend>
    					<input type="file" name="award_file" />(jpg,gif,png)<br />
    					<input type="submit" value="上传" />&nbsp;
    					<input type="button" onclick="save(0)" value="预览" />&nbsp;
    					<input type="button" onclick="save(1)"  value="保存" />
    					<?php if($tplCfg):?>
    					<input type="button" id="statusButton" onclick="switchStatus()" value="<?php echo $tplCfg['cfg_status'] ? '停用' : '启用'?>" />
    					<?php endif?>
    					</fieldset>
    				</form>
    				<iframe name="hdFrame" id="hdFrame" style="display:none"></iframe>
    			</div>
    			<div region="center" title="参数设置" iconCls="icon-redo">
    				<form id="cfgForm">
    				<input type="hidden" name="exam_id" value="<?php echo $examId?>" />
    				<input type="hidden" id="saveType" name="saveType" value="preview" />
    				<?php foreach($items as $item=>$caption):?>
					<fieldset>
						<legend><input type="checkbox" name="<?php echo $item?>" value="1" <?php if(isset($tplCfg[$item])):?>checked="true"<?php endif?> /> <?php echo $caption?></legend>
						<?php if($item == 'addon_text'):?><div>内&nbsp;&nbsp;容：<input type="text" name="addonText" value="<?php echo $tplCfg[$item]['text']?>" /></div><?php endif?>
						<div>字&nbsp;&nbsp;体：<?php echo W('ArraySelect', array('attr'=>'name="fontFamily[' . $item . ']"', 'options'=>$fontArray, 'value'=>$tplCfg[$item]['fontFamily']))?>
							 字&nbsp;&nbsp;号：<input type="text" name="fontSize[<?php echo $item?>]" class="easyui-numberspinner" size="6" min="12" max="45" value="<?php echo $tplCfg[$item]['fontSize']?>" />
							 
							 <?php if($item == 'stu_name'):?>
							 <b>中线偏移:</b><input type="checkbox" name="center" value="1" <?php if($tplCfg[$item]['center']):?>checked="true"<?php endif?> />
							 <?php else:?>
							 字间空格:<input type="text" name="blankNum[<?php echo $item?>]" class="easyui-numberspinner" size="6" min="0" max="3" value="<?php echo abs($tplCfg[$item]['blankNum'])?>" />
							 <?php endif;?>
						</div>
						<div>上边距：<input type="text" name="top[<?php echo $item?>]" class="easyui-numberbox" size="6" value="<?php echo $tplCfg[$item]['top']?>" />
							 左边距：<input type="text" name="left[<?php echo $item?>]" class="easyui-numberbox" size="6" value="<?php echo $tplCfg[$item]['left']?>" />
							 颜&nbsp;&nbsp;色:<select name="fontColor[<?php echo $item?>]">
							 	<?php foreach ($colorArray as $key=>$color):?>
							 		<option value="<?php echo $key?>" <?php if($key == $tplCfg[$item]['fontColor']){echo 'selected="true"';}?> style="font-weight:bold;color:<?php echo $color[2]?>"><?php echo $color[0]?></option>
							 	<?php endforeach;?>
							 </select>
					    </div>
					</fieldset>
					<?php endforeach;?>
					</form>
    			</div>
    		</div>
    	</div>
    	<div region="center" title="效果图预览" iconCls="icon-redo">
    		<div class="ruler"></div>
    	</div>
    </body>
</html>