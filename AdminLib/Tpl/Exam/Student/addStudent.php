<style type="text/css">
.formItem{margin-top:6px;}
ul.stuInfo{padding:5px;margin:0px}
ul.stuInfo li{float:left;width:200px;list-style-type:none}
ul.posList{margin:0px;padding:2px}
ul.posList li{float:left;width:48%;line-height:23px;list-style-type:none}
</style>
<?php if(false == $isSuper && $examInfo['exam_skip_grade']):?>
<script type="text/javascript">
alert('收费考试禁止后台报名');
jQuerry('#<?php echo $dialog?>').dialog('destroy');
</script>
<?php 
exit;
endif?>
<?php if(false == $freePosList):?>
<script type="text/javascript">
alert('没有空闲考点');
jQuerry('#<?php echo $dialog?>').dialog('destroy');
</script>
<?php else:?>
<div class="easyui-layout" fit="true">
	<div region="center">
		<div style="padding:5px">
		<div class="formItem"><label>竞赛名称：</label><b><?php echo '[' . $examInfo['group_caption'] . '] ' . $examInfo['exam_caption']?></b></div>
		<div class="formItem"><label>考生学号：</label><input style="ime-mode:disabled" onblur="javascript:disAddBtn()" type="text" name="saliascode" id="saliascode" />
			<a href="javascript:void(0)" onclick="doViewStuInfo()" class="easyui-linkbutton" iconCls="icon-search">查看</a>
		</div>
		<hr />
		<div>
			<fieldset>
			<legend>考生信息</legend>
			<input type="hidden" name="stu_code" id="stu_code" />
			<ul class="stuInfo">
				<li><label>学生姓名：</label><input type="text" id="sname" name="stu_name" size="8"></li>
				<li><label>当前年级：</label><select id="stu_grade" name="stu_grade">
				<?php foreach ($gradeArray as $gradeYear=>$gradeText):?>
				<option value="<?php echo $gradeYear?>"><?php echo $gradeText?></option>
				<?php endforeach;?>
				</select></li>
				<li style="width:400px;margin-top:5px"><label>联系电话：</label><input type="text" id="stu_mobile" name="stu_mobile" size="17">
				<?php if($resetPwdPerm):?>
				<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" onclick="doResetPwd()">重置密码</a>
				<?php endif?>
				</li>
				
			</ul>
			</fieldset>
		</div>
		<div>
		<fieldset>
		<legend>选择考点</legend>
		<ul class="posList">
			<?php foreach ($freePosList as $pos):?>
			<li><label><input class="posCode" type="radio" name="pos_code" value="<?php echo $pos['pos_code']?>" />[<?php echo $pos['pos_code_pre']?>]<?php echo $pos['pos_caption']?></label></li>
			<?php endforeach;?>
		</ul>
		</fieldset>
		</div>
		<div style="text-align:center">
		<a href="javascript:void(0)" id="addBtn" onclick="doAddStudent()" class="easyui-linkbutton" iconCls="icon-save">确定报名</a>
		</div>
		</div>
	</div>
</div>
<script type="text/javascript">
var enableAdd = false;
function doViewStuInfo() {
	var saliascode = jQuery.trim(jQuery('#saliascode').val());
	if(saliascode) {
		var examId = '<?php echo $examInfo['exam_id']?>';
		jQuery.post('<?php echo $jsonEsInfoUrl?>', {exam:examId, stuCode:saliascode}, function(data){
			if(data.errorMsg) {
				enableAdd = false;
				alert(data.errorMsg);
			} else {
				jQuery('#stu_code').val(data.scode);
				jQuery('#sname').val(data.sname);
				jQuery('#stu_grade').val(data.ngrade1year);
				jQuery('#stu_mobile').val(data.sparents1phone);
				enableAdd = true;
			}
		}, 'json');
	} else {
		alert('请输入学生学号');
	}
}

function disAddBtn() {
	enableAdd = false;
}

function doAddStudent() {
	if(false == enableAdd) {
		alert('请先执行学员信息校验');
		return;
	}
	if(0 == jQuery('.posCode:checked').length) {
		alert('请选择报名考点')
	} else {
		var signupData = {exam:'<?php echo $examInfo['exam_id']?>', 'stu_code':jQuery('#stu_code').val(), 'stu_mobile':jQuery('#stu_mobile').val(), pos_code:jQuery('.posCode:checked').val()}
		jQuery.post('<?php echo $signupUrl?>', signupData, function(data){
			enableAdd = false;
			if(data.errorMsg) {
				alert(data.errorMsg)
			} else {
				alert('报名成功,请注意查收短信');
				jQuery('#<?php echo $dialog?>').dialog('destroy');
				jQuery('#stuGrid').datagrid('reload');
			}
		}, 'json');
	}
	
}

<?php if($resetPwdPerm):?>
	function doResetPwd() {
		if(false == enableAdd) {
			alert('请先执行学员信息校验');
			return;
		}
		var stuCode = jQuery('#stu_code').val();
		var stuName = jQuery('#sname').val();
		if(confirm('确定要重置学员“' + stuName + '”的登陆密码吗？')) {
			jQuery.post('<?php echo $resetPwdUrl?>', {stu_code:stuCode}, function(data){
				if(data.errorMsg) {
					alert(data.errorMsg)
				} else {
					alert('密码重置成功，新密码为：“' + data.pwd + '”');
				}
			}, 'json');
		}
	}
<?php endif;?>

</script>
<?php endif;?>