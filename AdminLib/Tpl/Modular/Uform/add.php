<style type="text/css">
#formInfoDiv li{list-style-type:none;line-height:27px}
#formInfoDiv dl{padding:0px;margin:0px;margin-top:-22px;margin-left:45px}
#formInfoDiv dl dd{margin:0px;float:left;width:90px;line-height:15px}
</style>

<div id="formInfoDiv" class="easyui-layout" fit="true">
	<div region="north" style="height:30px">
		<div class="datagrid-toolbar" style="background:#eee">
			<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-save" plain="true" id="doAddAct">保存</a>
		</div>
	</div>
	<div region="west" style="width:350px">
		<div class="easyui-layout" fit="true">
			<div region="north" border="false" style="height:190px">
				<div class="datagrid-toolbar" style="background:#eee;"><span style="font-weight:bold;font-size:13px">&nbsp;活动基本信息：</span></div>
				<ul style="margin:0px;padding-left:10px">
					<li>
					<span>标题：</span>
					<input type="text" name="act_title" id="act_title" required="true" style="width:190px" /></li>
					<li>
					<span>时间：</span>
					<input type="text" id="act_start" onkeydown="return false" size="10" name="act_start" class="easyui-datebox" /> — 
					<input type="text" id="act_end" onkeydown="return false" size="10" name="act_end" class="easyui-datebox" />
					</li>
					<li>
					<span>模板：</span>
					<input type="text" name="act_tpl" id="act_tpl" style="width:130px;ime-mode:disabled" />(非必填项)</li>
					<li>
					<span>年级：</span>
					<dl>
					<?php 
					$i=0;
					foreach ($gradeYears as $gradeYear=>$grade):
					if($i >0):
					?>
					<dd><label><input type="checkbox" class="act_grade" value="<?php echo $gradeYear?>" />
					<?php echo $grade?></label></dd>
					<?php 
					endif;
					$i++;
					endforeach;?>
					</dl>
					</li>
				</ul>
			</div>
			<div region="center" border="false" style="border-top:1px solid #ccc;overflow:hidden">
				<div style="font-weight:bold;font-size:13px;background:#eee">&nbsp;活动说明：</div>
				<textarea id="act_content" style="width:100%;height:235px"></textarea>
				<?php echo W('Editor', array('id'=>'act_content', 'layout'=>'simple', 'resizeType'=>0))?>
			</div>
		</div>
	</div>
	<div region="center">
		<div class="easyui-layout" fit="true">
			<div region="north" style="height:55px;border-bottom:1px solid #ccc" border="false">
				<div class="datagrid-toolbar" style="background:#eee;"><span style="font-weight:bold;font-size:13px">&nbsp;表单字段管理：</span></div>
				<div id="addItem" style="padding-top:5px">
					&nbsp;字段标识：<input type="text" style="width:60px;ime-mode:disabled" id="attr_name" />(英文),
					类型：<select id="attr_type">
					<?php foreach ($dTypeArray as $key=>$dt):?>
					<option value="<?php echo $key?>"><?php echo $dt?></option>
					<?php endforeach;?>
					</select><a href="javascript:void(0)" id="addFormItem" class="easyui-linkbutton" plain="true" iconCls="icon-add">添加字段</a>
				</div>
			</div>
			<div region="center" border="false">
				<form id="attrList">
			
				</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
jQuery(function(){
	jQuery('#addFormItem').click(function(){
		var attrInfo = {attrName:jQuery.trim(jQuery('#attr_name').val()), attrType:jQuery('#attr_type').val()}
		if('' == attrInfo.attrName) {
			alert('请填写字段英文标识')
		} else {
			if(jQuery('.item_' + attrInfo.attrName).length > 0) {
				alert('字段名不能重复')
			} else {
				jQuery.post('<?php echo $addAttrUrl?>', attrInfo, function(data){
					if(data.errorMsg) {
						alert(data.errorMsg)
					} else {
						jQuery('#attrList').append(data.html)
						jQuery('.attrSeq:last').val(jQuery('.attrSeq').length)
					}
				}, 'json');
			}
		}
	})
	jQuery('#doAddAct').click(function(){
		keEditors.act_content.sync();
		actInfo = {
					actTitle:jQuery.trim(jQuery('#act_title').val()), 
					actStart:jQuery.trim(jQuery('#act_start').datebox('getValue')),
					actEnd:jQuery.trim(jQuery('#act_end').datebox('getValue')),
					actContent:jQuery.trim(keEditors.act_content.html())
		}
		for(var key in actInfo) {
			if('' == actInfo[key]) {
				alert('活动信息不完整');
				return;
			} 
		}
		actInfo['actTemplate'] = jQuery.trim(jQuery('#act_tpl').val())
		var grades = [];
		jQuery('.act_grade').each(function(){
			if(this.checked) {
				grades.push(this.value)
			}
		})
		if(grades.length == 0) {
			alert('活动年级未设定');
			return;
		} else {
			actInfo['actGrades'] = grades.join(',');
		}
		
		var attrLength = jQuery('.attrCaption').length;
		if(attrLength >0) {
			formData = jQuery('#attrList').serialize();
			jQuery.each(actInfo, function(k, item){
				formData+= '&' + k + '=' + encodeURIComponent(item)
			})
		} else {
			formData = actInfo;
		}
		jQuery.post('<?php echo $addActUrl?>', formData, function(data) {
			if(data.errorMsg) {
				alert(data.errorMsg);
			} else {
				alert('微信活动创建成功');
				jQuery('#<?php echo $dialog?>').dialog('close');
			}
		}, 'json');
		
	})
})
function removeAttr(attrName) {
	jQuery('.item_' + attrName).remove()
}
</script>