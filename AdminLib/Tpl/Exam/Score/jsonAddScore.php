<script type="text/javascript">
var quesYesno = {'danxuan':true, 'panduan':true, 'duoxuan':false, 'jieda':false, 'tiankong':false}
var questions = {};
function ifnull(varValue, defaultValue) {
	if(!varValue) {
		return defaultValue;
	}
	return varValue;
}

function changePos(noSearch) {
	var posInfo = jQuery('#posCode').val();
	if(posInfo) {
		var ar = posInfo.split('~');
		jQuery('select[name=pos_code]').val(ar[0]);
	} else {
		var ar=['',''];
	}
	var posCode = ar[0];
	var posCodePre = ar[1];
	jQuery('#code_suffix').val('');
	jQuery('#pos_code').val(posCode);
	jQuery('#code_pre').val(posCodePre.toUpperCase());
	if(true !== noSearch) {
		doSearch('code');
	}
	initGrid();
}

function doSearch(type) {
	var args = {exam_id:<?php echo $examId?>, subject_code:'<?php echo $subjectCode?>'}
    switch(type) {
    	case 'code':
    		args['type'] = 'code';
    		args['pos_code'] = jQuery('#pos_code').val();
    		args['code_pre'] = jQuery('#code_pre').val();
    		args['code_suffix'] = jQuery('#code_suffix').val();
    		args['paper_char'] = jQuery('#paper_char').val();
    		jQuery('#stu_name').val('');
    		jQuery('#stu_mobile').val('');
    	break;
    	case 'name':
    		args['type'] = 'name';
    		if(false == jQuery('#stu_name').val()) {
    			alert('请输入学生姓名');
    			return;
    		}
    		args['stu_name'] = jQuery('#stu_name').val();
    		jQuery('#stu_mobile').val('');
    		jQuery('#code_suffix').val('');
    	break;
    	case 'mobile':
    		args['type'] = 'mobile';
    		if(false == jQuery('#stu_mobile').val()) {
    			alert('请输入学生电话');
    			return;
    		}
    		jQuery('#stu_name').val('');
    		jQuery('#code_suffix').val('');
    		args['stu_mobile'] = jQuery('#stu_mobile').val();
    	break;
    }
    jQuery('.ques_score').val('');
    jQuery('.part_score').html(0);
    jQuery('.paper_score').html(0);
    jQuery.post('<?php echo $searchUrl?>', args, function(data){
    	if(type  =='code' && data.length ==1) {
    		var stuInfo = data[0];
    		var examCode = stuInfo.exam_code.toUpperCase();
    		var codeSuffix = examCode.replace(jQuery('#code_pre').val().toUpperCase(), '');
    		jQuery('#code_suffix').val(codeSuffix);
    		jQuery('#stu_code').val(stuInfo.stu_code);
    		jQuery('#pos_code').val(stuInfo.pos_code);
    		jQuery('#stu_name').val(stuInfo.stu_name);
    		jQuery('#stu_mobile').val(stuInfo.stu_mobile);
    		setStuScore(examCode);
    	} else if(data) {
    		if(data.length ==0) {
    			alert('没有查询到考生信息');
    		} else {
	    		jQuery('<div id="seleStudent"></div>').appendTo('body');
	    		var tpl = jQuery('#tpl-stuList').html();
	    		juicer.register('ifnull', ifnull);
	    		var html = juicer(tpl,{stuList:data});
	    		jQuery('#seleStudent').dialog({
	    			title:'选择考生',
	    			iconCls:'icon-redo',
	    			width:600,
	    			height:300,
	    			modal:true,
	    			content:html,
	    			onClose:function(){
	    				jQuery('#seleStudent').dialog('destroy');
	    			}
	    		})
    		}
    	} else {
    		alert('没有查询到考生信息');
    	}
    }, 'json');
}


function setStudent(posCode, examCode){
	var oPosCodes = jQuery('#posCode')[0];
	for(var i=0;i<oPosCodes.length;i++) {
		var regexp = new RegExp('^' + posCode + '~')
		if(regexp.test(oPosCodes[i].value)) {
			oPosCodes[i].selected=true;
			changePos(true);
			jQuery('#code_suffix').val(examCode.toUpperCase().replace(jQuery('#code_pre').val(), ''))
			doSearch('code');
			jQuery('#seleStudent').dialog('destroy');
		}
	}
}

function tmpStudent(stuCode,stuMobile) {
	var posCode = jQuery('#pos_code').val();
	var examId = <?php echo $examId?>;
	jQuery.post('<?php echo $tmpSignupUrl?>', {examId:examId, stuCode:stuCode, posCode:posCode, stuMobile:stuMobile}, function(data){
		if(data.errorMsg) {
			alert(data.errorMsg);
		} else {
			setStudent(data.pos_code, data.exam_code);
		}
	}, 'json');
	
}

function changePaper() {
    paperChar = jQuery('#paper_char').val();
    loadScoreTab();
}

function rightPart(partId) {
	jQuery('#partUL_' + partId).find('.ques_score').each(function(){
		jQuery(this).val(jQuery(this).attr('score'));
	})
	computeScore(partId);
}

function wrongPart(partId) {
    jQuery('#partUL_' + partId).find('.ques_score').each(function(){
		jQuery(this).val(0);
	})
	computeScore(partId);
}

function setStuScore(examCode){
	var args = {exam_id:<?php echo $examId?>, subject_code:'<?php echo $subjectCode?>', 'stu_code':jQuery('#stu_code').val(),exam_code:examCode}
	
	jQuery.post('<?php echo $stuScoreUrl?>', args, function(stuScore){
		var partIds = [];
		jQuery('.ques_score').each(function(){
				var id = jQuery(this).attr('id');
			if(false == isNaN(stuScore[id])) {
				jQuery(this).val(parseFloat(stuScore[id]));
				if(partIds[partIds.length-1] != jQuery(this).attr('part_id')){
					partIds.push(jQuery(this).attr('part_id'));
				}
			}
		})
		
		jQuery(partIds).each(function(k,v){
			computeScore(v)
		})
	}, 'json');
}

function setScore(oInput) {
    var oInput = jQuery(oInput);
    var val = oInput.val();
	if(/y/i.test(val)) {
		oInput.val(oInput.attr('score'));
	} else if(/[a-z]/i.test(val)) {
		oInput.val(0);
	} else {
		val = val.replace(/[^\d\.]/g,'');
		if(/\./.test(val)) {
			var ar=val.split('.');
			ar[0] = parseInt(ar[0], 10);
			if(ar[1]) {
				oInput.val(ar[0] + '.5');
			}else {
				oInput.val(val);
			}
		} else {
			val  = parseFloat(val);
			if(isNaN(val)) {
				oInput.val(0);
			} else if(Math.abs(val) > Math.abs(oInput.attr('score'))){
				alert('分值输入错误,最大分值'  + oInput.attr('score'));
				oInput.val('');
			} else {
				oInput.val(val);
			}
		}
	}
	
	var curPart = oInput.attr('part_id');
	computeScore(curPart);
}

function computeScore(partId) {
	var totalScore = 0;
	var partScore = 0;
	var paperScore = 0;
	if(jQuery('#total_score').siblings('.panel-header').find('.panel-title').html() == '成绩录入') {
		jQuery('#total_score').siblings('.panel-header').find('.panel-title').html('成绩录入&nbsp;【总:<b class="paper_score" id="paper_total_score">0</b>分】');
	}
	jQuery('.ques_score').each(function(){
		if(jQuery(this).val()) {
			totalScore += Math.abs(jQuery(this).val());
			if(jQuery(this).attr('part_id') != 'addon') {
				paperScore += Math.abs(jQuery(this).val());
			}
			if(jQuery(this).attr('part_id') == partId) {
				partScore+= Math.abs(jQuery(this).val());
			}
		}
	})
	jQuery('#paperScore').html(paperScore);
	jQuery('#paper_total_score').html(totalScore);
	jQuery('#part_score_' + partId).html(partScore);
}

function setQuesInfo(oInput) {
    var oInput = jQuery(oInput);
    oInput.select();
    var quesId = oInput.attr('ques_id')
    var quesInfo = questions[quesId];
    quesInfo.stu_code=jQuery('#stu_code').val();
    var quesType = quesInfo.ques_type;
    var tpl = jQuery('#tpl-' + quesType).html();
    var html = juicer(tpl,quesInfo);
    jQuery('#quesInfo').html(html);
    jQuery('#quesInfo').find('.linkbutton').linkbutton();
    if(jQuery('#stu_code').val()) {
	    jQuery.post('<?php echo $stuQuesUrl?>', {paper_id:quesInfo.paper_id,ques_id:quesId, stu_code:jQuery('#stu_code').val()}, function(answer){
	    	switch(quesType) {
	    		case 'danxuan':
	    			jQuery('#stu_answer').val(answer.stu_answer);
	    		break;
	    		case 'tiankong':
	    			jQuery(answer.stu_answer).each(function(k,blkAnswer){
	    				if(k != 0) {
	    					jQuery('#stu_answer_' + k).val()
	    				}
	    			})
	    		break;
	    	}
	    }, 'json');
    }
}

function saveQuesInfo() {
	jQuery('#ques_form').find('input[name=stu_code]').val(jQuery('#stu_code').val());
	var quesData = jQuery('#ques_form').serialize();
	jQuery.post('<?php echo $saveQuesUrl?>', quesData, function(data){
		if(data.errorMsg) {
			alert(data.errorMsg);
			return;
		} else {
			alert('考生答案保存成功');
		}
	}, 'json');
}

function saveScore(absence) {
	var saveScoreUrl = '<?php echo $saveScoreUrl?>';
	if(absence) {
		saveScoreUrl += '?absence=1';
		if(false == confirm('确定保存缺考成绩吗?')) {
			return
		}
	}
	var scoreData = jQuery('#scoreForm').find('input,select').serialize();
	jQuery.post(saveScoreUrl, scoreData, function(data){
		jQuery('#scoreGrid').datagrid('reload');
		if(data.errorMsg) {
			alert(data.errorMsg);
			return;
		} else {
			alert('成绩保存成功');
			jQuery('#code_suffix').val(data.exam_code.replace(jQuery('#code_pre').val(), ''));
			doSearch('code');
		}
	}, 'json');
}

function posCaption(val, data) {
	return '[' + data.pos_code.toUpperCase() + '] ' + val;
}

function initGrid() {
	jQuery('#scoreGrid').datagrid({
    	url:'<?php echo $jsonSubjectScoreUrl?>',
    	queryParams:{posCode:jQuery('select[name=pos_code]').val()}
    })
}


</script>
<style type="text/css">
dl,dd,ul{padding:0px;margin:0px;text-align:left}
.score-stu-info {padding:5px 10px;margin:0px}
.score-stu-info li{list-style-type:none;line-height:22px;font-weight:bold;margin-bottom:2px}
.score-stu-info li span{color:#666}
dt{height:23px;font-weight:bold;font-size:14px;line-height:23px;background:#eee;padding:3px}
legend{font-weight:bold;color:red;font-size:15px}
.questions li{list-style-type:none;width:65px;float:left;padding:3px 0px;margin-right:4px}
.questions input{width:35px;text-align:center;ime-mode:disabled}
.questions .clear{width:0px;overflow:hidden;clear:both;margin:0px}
#quesInfo table{width:100%}
#quesInfo th{width:15%;padding:3px;border-bottom:1px dashed #ccc}
#quesInfo td{width:35%;padding:3px;border-bottom:1px dashed #ccc}
.datagrid-toolbar{height:24px;padding:3px;border-bottom:1px solid #ccc;background:#eee;line-height:22px}
.part_score{font-size:14px;color:red;font-weight:bold}
#paper_total_score{font-size:15px;color:red;font-weight:bold}
.ques-notice{color:blue}
</style>

<div class="easyui-layout" fit="true" border="false" split="false">
    <div region="west" style="width:400px" id="scoreForm">
        <div class="easyui-layout" fit="true" border="false" split="false">
            <div region="north" style="height:240px" title="考生信息" iconCls="icon-redo" collapsible=false split="false">
                <input type="hidden" name="exam_id" value="<?php echo $examId?>" />
                <input type="hidden" name="subject_code" value="<?php echo $subjectCode?>" />
                <input type="hidden" name="stu_code" id="stu_code" value="" /> 
                <input type="hidden" name="pos_code" id="pos_code" value="" />
                <ul class="score-stu-info">
                    <li><span>竞赛名称：</span><?php echo $examInfo['group_caption'] . '（' . $examInfo['exam_caption'] . '）'?></li>
                    <li><span>试卷科目：</span><?php echo $subjectInfo['subject_name']?>
                    <?php if(sizeof($paperArray['real']) > 1):?>
                    <select name="paper_char" id="paper_char" onchange="changePaper()">
                    <option value="A"<?php if($paperChar == 'A'){ echo ' selected="true"';}?>>A卷</option>
                    <option value="B"<?php if($paperChar == 'B'){ echo ' selected="true"';}?>>B卷</option>
                    </select>
                    步长：<select name="step"><option value="1">1</option><option value="2">2</option></select>
                    <?php else:?>
                    <input type="hidden" id="paper_char" name="paper_char" value="A" />
                    <input type="hidden" id="step" name="step" value="1" />
                    <?php endif?>
                    </li>
                    <li><span>选择考点：</span><select id="posCode">
                    <?php foreach ($posArray as $pos) :?>
                        <option value="<?php echo $pos['pos_code'] . '~' . $pos['pos_code_pre']?>">[<?php echo $pos['pos_code_pre']?>] <?php echo $pos['pos_caption']?></option>
                    <?php endforeach;?>
                    </select></li>
                    <li><span>准考证号：</span><input size="6" readonly="true" type="text" name="code_pre" id="code_pre" />&nbsp;<input type="text" name="code_suffix" id="code_suffix" size="10" onkeyup="this.value=this.value.replace(/\D/g, '');if(13==event.keyCode){doSearch('code')}"/>&nbsp;<a href="javascript:doSearch('code')" class="easyui-linkbutton" iconCls="icon-search">查询</a></li>
                    <li><span>考生姓名：</span><input type="text" name="stu_name" id="stu_name" onkeyup="if(13==event.keyCode){doSearch('name')}"/><a href="javascript:doSearch('name')" class="easyui-linkbutton" iconCls="icon-search">查询</a></li>
                    <li><span>联系电话：</span><input type="text" name="stu_mobile" id="stu_mobile" onkeyup="if(13==event.keyCode){doSearch('mobile')}" /><a href="javascript:doSearch('mobile')" class="easyui-linkbutton" iconCls="icon-search">查询</a></li>
                    <li>
                        <a href="javascript:saveScore()" class="easyui-linkbutton" iconCls="icon-save">保存成绩</a>
                        <a href="javascript:saveScore(true)" class="easyui-linkbutton" iconCls="icon-save">考生缺考</a>
                    </li>
                </ul>
            </div>
            <div region="center" id="total_score" title="成绩录入" iconCls="icon-redo">
                <?php 
                if(false == is_array($paperArray['real'])) $paperArray['real'] = array();
                $paperInfo = $paperArray['real'][$paperChar];
                if($paperInfo):
                $partChars = array('', '一','二', '三','四', '五', '六');
                ?>
                <fieldset>
                    <legend><?php echo $paperInfo['paper_caption']?>【<span style="color:blue" class="paper_score" id="paperScore">0</span>分】</legend>
                    <dl>
                        <?php foreach ($paperInfo['parts'] as $part):?>
                        <dt>
                            <div style="font-size:11px;float:left;width:220px;height:22px;overflow:hidden">
                                <?php echo $partChars[$part['part_id']]?>大题：<?php echo $part['part_caption']?>【<span class="part_score" id="part_score_<?php echo $part['part_id']?>">0</span>分】
                            </div>
                            <div style="float:right">
                                <a href="javascript:rightPart(<?php echo $part['part_id']?>)" class="easyui-linkbutton" iconCls="icon-ok" plain="true">全对</a>
                                <a href="javascript:wrongPart(<?php echo $part['part_id']?>)" class="easyui-linkbutton" iconCls="icon-cancel" plain="true">全错</a>
                            </div>
                        </dt>
                        <dd><ul class="questions" id="partUL_<?php echo $part['part_id']?>">
                        <?php 
                        $i = 0;
                        foreach ($part['questions'] as $ques):?>
                        <li><b><?php echo sprintf('%02d', $ques['ques_seq'])?>.</b><input type="text" class="ques_score part_ques_<?php echo $part['part_id']?>" paper_id="<?php echo $paperInfo['paper_id']?>" part_id="<?php echo $part['part_id']?>" score="<?php echo $ques['ques_score']?>" ques_id="<?php echo $ques['ques_id']?>" id="ques_<?php echo $ques['paper_id']?>_<?php echo $ques['ques_seq']?>" name="ques[<?php echo $ques['paper_id']?>][<?php echo $ques['ques_seq']?>]" ques_type="<?php echo $ques['ques_type']?>" onfocus="setQuesInfo(this)" onkeyup="setScore(this)" /></li>
                        <?php 
                        $quesArray[$ques['ques_id']] = $ques;
                        $i++;
                        if($i % 5 ==0){
                            echo '<li class="clear"></li>';
                        }
                        endforeach;?>
                        </ul>
                        <div style="clear:both"></div>
                        </dd>
                        <?php endforeach;?>
                        
                    </dl>
                </fieldset>
                <?php endif;?>
                <?php if($paperArray['addon']):
                    $paperInfo = $paperArray['addon']['A'];
                    $part = $paperInfo['parts'][1];
                ?>
                <fieldset>
                    <legend><?php echo $paperInfo['paper_caption']?>【<span style="color:blue" class="paper_score" id="part_score_addon">0</span>分】</legend>
                    <dl>
                        <dd><ul class="questions" id="partUL_<?php echo $part['part_id']?>">
                        <?php 
                        $i = 0;
                        foreach ($part['questions'] as $ques):?>
                        <li><b><?php echo sprintf('%02d', $ques['ques_seq'])?>.</b><input type="text" class="ques_score part_ques_addon" paper_id="<?php echo $paperInfo['paper_id']?>" part_id="addon" score="<?php echo $ques['ques_score']?>" ques_id="<?php echo $ques['ques_id']?>" id="ques_<?php echo $paperInfo['paper_id']?>_<?php echo $ques['ques_seq']?>" name="ques[<?php echo $paperInfo['paper_id']?>][<?php echo $ques['ques_seq']?>]" ques_type="<?php echo $ques['ques_type']?>" onfocus="setQuesInfo(this)" onkeyup="setScore(this)" /></li>
                        <?php 
                        $quesArray[$ques['ques_id']] = $ques;
                        $i++;
                        if($i % 5 ==0){
                            echo '<li class="clear"></li>';
                        }
                        endforeach;?>
                        </ul></dd>
                    </dl>
                </fieldset>
                <?php endif?>
            </div>
        </div>
    </div>
    
    <div region="center">
        <div class="easyui-layout" fit="true" border="false">
            <div region="north" style="height:180px">
                <div class="easyui-tabs" border="false">
                    <div title="试题信息">
                    	
                        <script type="text/template" id="tpl-danxuan">
                        	<div class="datagrid-toolbar"><a class="linkbutton" href="javascript:void(0)" plain="true" iconCls="icon-save" onclick="saveQuesInfo()">保存答题信息</a><span class="ques-notice">仅错误答案需要保存</span></div>
                        	<form id="ques_form">
                        		<input type="hidden" name="ques_type" value="danxuan" />
                        		<input type="hidden" name="exam_id" value="<?php echo $examId?>" />
                        		<input type="hidden" name="paper_id" value="${paper_id}" />
                        		<input type="hidden" name="stu_code" value="" />
                        		<input type="hidden" name="ques_seq" value="${ques_seq}" />
                        		<input type="hidden" name="ques_id" value="${ques_id}" />
                            <table>
                                <tr><th>试题摘要：</th><td colspan="3">${ques_sumary}</td></tr>
                                <tr><th>试题类型：</th><td>单项选择</td><th>试题难度：</th><td>${ques_level}级</td></tr>
                                <tr><th>试题分值：</th><td>${ques_score}</td><th>知识点：</th><td>${knowledge_caption}</td></tr>
                                <tr><th>考生答案：</th><td><select name="stu_answer" id="stu_answer">
                                {@each ques_answer_items as text,item}
                                {@if item !=ques_answer}
                                <option value="${item}">[${item}]${text}</option>
                                {@/if}
                                {@/each}
                                </select></td><th>标准答案：</th><td>${ques_answer}</td></tr>
                            </table>
                            </form>
                        </script>
                        <script type="text/template" id="tpl-duoxuan"></script>
                        <script type="text/template" id="tpl-tiankong">
                        	<div class="datagrid-toolbar"><a class="linkbutton" href="javascript:void(0)" plain="true" iconCls="icon-save" onclick="saveQuesInfo()">保存答题信息</a><span class="ques-notice">仅错误答案需要保存</span></div>
                        	<form id="ques_form">
	                        	<input type="hidden" name="ques_type" value="tiankong" />
	                        	<input type="hidden" name="exam_id" value="<?php echo $examId?>" />
                        		<input type="hidden" name="paper_id" value="${paper_id}" />
                        		<input type="hidden" name="stu_code" value="" />
                        		<input type="hidden" name="ques_seq" value="${ques_seq}" />
                        		<input type="hidden" name="ques_id" value="${ques_id}" />
                            <table>
                                <tr><th>试题摘要：</th><td colspan="3">${ques_sumary}</td></tr>
                                <tr><th>试题类型：</th><td>填空题</td><th>试题难度：</th><td>${ques_level}级</td></tr>
                                <tr><th>试题分值：</th><td>${ques_score}分</td><th>知识点：</th><td>${knowledge_caption}</td></tr>
                                <tr><th>考生答案：</th><td colspan="3">
                                {@each ques_answer as items,blankNum}
                                第${blankNum}空：
                                <select name="stu_answer[${blankNum}]" class="stu_answer" blank_id="${blankNum}" id="stu_answer_${blankNum}" style="width:200px">
                                	<option value="">请选择考生答案</option>
                                	{@each items as item}
	                                		<option value="1${item}">[正确]${item}</option>
                                	{@/each}
                                	<option value="1其他">[正确]其他正确答案</option>
                                	{@each ques_answer_items as wItem,wNum}
	                                	{@if wNum==blankNum}
		                                	{@each wItem as item}
			                                	<option value="0${item.item}">[错误]${item.item}</option>
		                                	{@/each}
	                                	{@/if}
                                	{@/each}
                                	<option value="0其他">[错误]其他错误答案</option>
                                </select><br />
                                {@/each}
                                </td></tr>
                            </table>
                            </form>
                        </script>
                        <script type="text/template" id="tpl-panduan"></script>
                        <script type="text/template" id="tpl-jieda">
                        	<form id="ques_form">
	                        	<input type="hidden" name="ques_type" value="jieda" />
	                        	<input type="hidden" name="exam_id" value="<?php echo $examId?>" />
                        		<input type="hidden" name="paper_id" value="${paper_id}" />
                        		<input type="hidden" name="stu_code" value="" />
                        		<input type="hidden" name="ques_seq" value="${ques_seq}" />
                        		<input type="hidden" name="ques_id" value="${ques_id}" />
                            <table>
                                <tr><th>试题摘要：</th><td colspan="3">${ques_sumary}</td></tr>
                                <tr><th>试题类型：</th><td>解答题</td><th>试题难度：</th><td>${ques_level}级</td></tr>
                                <tr><th>试题分值：</th><td>${ques_score}分</td><th>知识点：</th><td>${knowledge_caption}</td></tr>
                                <tr><td colspan="4">解答题不需设置考生答案</td></tr>
                            </table>
                            </form>
                        </script>
                        <script type="text/template" id="tpl-stuList">
                        <table width="100%" class="easyui-datagrid" fit="true" singleselect="true">
                        <thead>
                        <tr><th field="stu_name">学生姓名</th><th field="stu_code">学生编码</th><th field="saliascode">高思学号</th><th field="stu_grade">当前年级</th><th field="exam_code">准考证号</th><th field="stu_mobile">联系电话</th><th field="pos_caption">所在考点</th><th field="manage">选择考生</th></tr>
                        </thead>
                        <tbody>
                        {@each stuList as stu}
                        	<tr><td>${stu.stu_name}</td>
                        		<td>${stu.stu_code}</td>
                        		<td>${stu.saliascode}</td>
                        		<td>${stu.stu_grade|ifnull,''}</td>
                        		<td>${stu.exam_code|ifnull,''}</td>
                        		<td>${stu.sparents1phone|ifnull,''}{@if stu.sparents2phone},${stu.sparents2phone}{@/if}</td>
                        		<td>${stu.pos_caption|ifnull,''}</td><td>{@if stu.exam_code}<a href="javascript:void(0)" onclick="setStudent('${stu.pos_code}', '${stu.exam_code}')">选择考生</a>{@else}<a href="javascript:void(0)" onclick="tmpStudent('${stu.stu_code}', '${stu.sparents1phone|ifnull,stu.sparents2phone}')">临时考生</a>{@/if}</td></tr>
                        {@/each}
                        </tbody>
                        </table>
                        </script>
                        <div id="quesInfo" class="easyui-pannel" border="false"></div>
                    </div>
                </div>
            </div>
            <div region="center">
            	<div id="scoreToolbar">选择考点：<select name="pos_code" onchange="initGrid()">
            		<option value="0">不限考点</option>
                    <?php foreach ($posArray as $pos) :?>
                        <option value="<?php echo $pos['pos_code']?>">[<?php echo $pos['pos_code_pre']?>] <?php echo $pos['pos_caption']?></option>
                    <?php endforeach;?>
                    </select></div>
                <table class="easyui-datagrid" id="scoreGrid" fit="true" rownumbers="true" border="false" toolbar="#scoreToolbar" pagination="true">
	                <thead>
	                	<tr><th field="sname">姓名</th>
	                	<th field="saliascode">学号</th><th field="exam_code" sortable="true">准考证号</th><th field="pos_caption" formatter="posCaption">所在考点</th><th field="paper_char" align="center">标识</th><th field="real_score" sortable="true">实体卷</th><?php if($paperArray['addon']):?>
	                		<th field="addon_score" sortable="true">附加卷</th>
	                	<?php endif?>
	                		<th field="update_user">操作员</th>
	                		<th field="update_at" sortable="true">操作时间</th>
	                	</tr>
	                </thead>
                </table>
            </div>
        </div>
    </div>
    
</div>
<script type="text/javascript">
jQuery(function(){
    questions = <?php echo SysUtil::jsonEncode($quesArray)?>;
    
    changePos();
    jQuery('#posCode').change(changePos);
})
</script>