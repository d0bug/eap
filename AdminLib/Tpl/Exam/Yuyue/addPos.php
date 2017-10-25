<style type="text/css">
	.posItem{line-height:25px;height:25px;margin-top:3px}
	.posItem b{display:block;width:70px;float:left}
</style>
<div class="easyui-layout" fit="true" border="false">
	<div region="north" style="height:28px">
		<a href="javascript:doAddPos()" class="easyui-linkbutton" iconCls="icon-save" plain="true">保存地点信息</a>
	</div>
	<div region="center" border="false">
		<div class="easyui-layout" fit="true" border="false">
			<div region="north" style="height:90px" border="false">
				<div class="posItem"><b>&nbsp;地点名称</b>：<input type="text" name="pos_caption" id="pos_caption" class="easyui-validatebox" required="true" /></div>
				<div class="posItem"><b>&nbsp;联系电话</b>：<input type="text" name="pos_caption" id="pos_telephone" class="easyui-validatebox" required="true" /></div>
				<div class="posItem"><b>&nbsp;地址</b>：<input size="40" type="text" name="pos_caption" id="pos_addr" class="easyui-validatebox" required="true" /></div>
			</div>
			<div region="center">
				<table id="ePosGrid" fit="true" title="选择关联考点" border="false">
					<thead>
						<tr>
							<th field="pos_code" checkbox="true"></th>
							<th field="pos_caption">考点名称</th>
							<th field="pos_telephone">联系电话</th>
							<th field="pos_addr">考点地址</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($ePosList as $pos):?>
						<tr>
							<td><?php echo $pos['pos_code']?></td>
							<td><?php echo $pos['pos_caption']?></td>
							<td><?php echo $pos['pos_telephone']?></td>
							<td><?php echo $pos['pos_addr']?></td>
						</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
jQuery(function(){
	jQuery('#ePosGrid').datagrid({
		onSelect:function(idx, data){
			if('' == jQuery.trim(jQuery('#pos_caption').val())) {
				jQuery('#pos_caption').val(data.pos_caption);
			}
			if('' == jQuery.trim(jQuery('#pos_telephone').val())) {
				jQuery('#pos_telephone').val(data.pos_telephone);
			}
			if('' == jQuery.trim(jQuery('#pos_addr').val())) {
				jQuery('#pos_addr').val(data.pos_addr);
			}
		}
	})
})
function doAddPos() {
	var valid = true;
	jQuery('.easyui-validatebox').each(function(){
		valid = valid && jQuery(this).validatebox('isValid');
	})
	if(valid) {
		var selections = jQuery('#ePosGrid').datagrid('getSelections');
		if(selections.length ==0) {
			alert('请选择关联考点');
		} else {
			var posArray = [];
			jQuery.each(selections,function(k,pos){
				posArray.push(pos.pos_code);
			})
			var posList = posArray.join(",");
			var data = {yGroupId:'<?php echo $yGroupId?>',
						posCaption:jQuery.trim(jQuery('#pos_caption').val()),
						posTelephone:jQuery.trim(jQuery('#pos_telephone').val()),
						posAddr:jQuery.trim(jQuery('#pos_addr').val()),
						posList:posList}
			jQuery.post('<?php echo $addPosUrl?>', data, function(data){
				if(data.errorMsg) {
					alert(data.errorMsg);
				} else {
					alert('诊断地点信息录入成功');
					jQuery('#posGrid').datagrid('reload');
					jQuery('#<?php echo $dialog?>').dialog('destroy');
				}
			}, 'json');
		}
	} else {
		alert('诊断地点信息不完整');
	}
}
</script>