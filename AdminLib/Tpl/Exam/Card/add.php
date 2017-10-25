<?php if(false == $resultScript):?>
<script type="text/javascript">

function genCard(){
	var isValid = $("#addForm").form('validate');
	if(!isValid){
		return ;		
	}
	$.messager.progress({'title':'系统提示', 'msg': '', 'text':'处理中,请稍后...'});
	$("#addForm").submit();
}

function genMaxNum(){

	var card_length = $("#card_length").val();
	var card_pre = $("#card_pre").val();
 	
	if('' !== card_length && !isNaN(card_length) ){
		card_length = parseInt(card_length, 10);
		card_pre_len = card_pre.length;

		var len_diff = (card_length - card_pre_len);
		if(len_diff > 0){
			if(len_diff >= 7){
				$("#max_num_tip").html('卡量 >= 1千万');
			}else{
				var maxn = Math.pow(10, len_diff) + '';
 
				var maxnlen = maxn.length;
	 
				var mx = new Array();
				for(var i in maxn){
					if(i%3 == 0 && i != 0){
						mx[maxnlen-i] = ' ' + maxn[maxnlen-i];
					}else{
						mx[maxnlen-i] = maxn[maxnlen-i];
					}
				}
				mx[0] = maxn[0];
				$("#max_num_tip").html('卡量:' + mx.join(''));
			}
			
		}
	}else{
		$("#max_num_tip").html('');
	}
	
}


</script>
<style type="text/css">
    #posInfoForm em{color:red}
    #posInfoForm .item{line-height:15px;padding-top:1px;margin-left:10px}
    #posInfoForm span{font-size:15px;font-weight:bold}
    #posInfoForm input{width:260px;height:18px;}
    #posInfoForm textarea{height:60px;width:95%}
    #posInfoForm .file{height:22px;width:240px;}
    #posInfoForm .appIcon{text-align:center;width:22px;height:22px;float:left;border:1px solid #ddd;line-height:22px;overflow:hidden;margin-left:0px;margin-right:2px;padding:0px}
    #posMap{margin-left:10px;margin-top:3px}
    .input_text{ width:250px;}
    .char{ ime-mode:disabled}
	#max_num_tip{ color:red;}
	#card_length{ width:80px;}
</style>
<div class="demo-tip icon-tip"></div>
<div class="easyui-layout" fit="true" border="false">
    <div region="center">
			<form id="addForm" method="post" action="<?php echo $addUrl?>" target="hdPosFrame" >
				<table id="addTable" border="1" width="100%" class="addTable gs_table" >
				
					<tr>
						<th  class="th" align="right" width="100">&nbsp;卡组名称</th> 
						<td class="td" align="left">&nbsp;<input type="text" name="group_name" id="group_name" class="easyui-validatebox"   data-options="required:true" value="华杯赛" /></td>
					</tr>
					
					
					
					<tr>
						<th  class="th" align="right">&nbsp;卡号前缀</th> 
						<td class="td" align="left">&nbsp;<input type="text" name="card_pre" id="card_pre" class="easyui-validatebox char"   data-options="required:true" value="HBS" onkeyup="genMaxNum();" onchange="genMaxNum();"/></td>
					</tr>
					
					<tr>
						<th  class="th" align="right">&nbsp;卡号长度</th> 
						<td class="td" align="left">&nbsp;<input type="text" name="card_length" id="card_length" class="easyui-validatebox char"   data-options="required:true" value="8" onkeyup="genMaxNum();" onchange="genMaxNum();"//> <span id="max_num_tip"></span></td>
					</tr>
					<tr>
						<th  class="th" align="right">&nbsp;密码长度</th> 
						<td class="td" align="left">&nbsp;<input type="text" name="pass_length" id="pass_length" class="easyui-validatebox char"   data-options="required:true" value="6" /></td>
					</tr>
					
					<tr>
						<th  class="th" align="right">&nbsp;密码组成</th> 
						<td class="td" align="left">&nbsp;<select id="pass_element" name="pass_element" >
							<option value="d">数字</option>
							<option value="c">字母</option>
							<option value="dc">数字+字母</option>
						</select></td>
					</tr>
					
 					<tr>
						<th  class="th" align="right">&nbsp;卡数量</th> 
						<td class="td" align="left">&nbsp;<input type="text" name="group_total" id="group_total" class="easyui-validatebox char"   data-options="required:true" value="200" /></td>
					</tr>
 
				</table>
				</form>   
    </div>
    <iframe id="hdPosFrame" name="hdPosFrame" style="display:none"></iframe>
    <div region="south" style="height:30px">
        <div style="margin-top:3px;padding-left:10px">
            <a href="javascript:genCard()" class="easyui-linkbutton" iconCls="icon-save">开始生成卡号</a>
        </div>
    </div>
</div>
<?php else:?>
<script type="text/javascript">
    <?php if($rs['error']):?>
	 parent.closeProgress();
	 parent.showMessage('<?php echo $rs['message']?>');
    <?php else:?>
    parent.reloadGrid();
    <?php endif?>
</script>
<?php endif?>