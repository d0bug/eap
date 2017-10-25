<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <script type="text/javascript">
        var paperCheckResult = false;
        var gridLoaded = false;
        function checkPaper() {
            jQuery('#pQuesGrid_1').treegrid('loadData', []);
            jQuery.post('<?php echo $paperCheckUrl?>', {exam:<?php echo $examId?>,type:'<?php echo $paperType?>', 'subject':jQuery('#subject_code').val(), 'pchar':jQuery('#paper_char').val()}, function(data){
                if(data.errorMsg) {
                    alert(data.errorMsg);
                    paperCheckResult = false;
                    jQuery('#saveBtn').linkbutton('disable');
                    clearPart();
                } else {
                    paperCheckResult  = true;
                    jQuery('#saveBtn').linkbutton('enable');
                }
            }, 'json');
        }
        
        /*function setPartNum(){
            if(false == paperCheckResult) {
                alert('试卷设置错误，请更改');
                return;
            }
            var partNum = Math.abs(jQuery('#part_num').val());
            var tbody = jQuery('#partList').find('tbody')
            var curNum = tbody.find('tr').length;
            if(partNum >= curNum) {
                for(var i=curNum,firstTab=i;i<partNum;i++) {
                    jQuery('<tr><td><b>第<span style="color:red;">' + partStr[i] + '</span>大题</b></td><td><input size="18" type="text" name="part_caption[' + (i+1) + ']" id="part_caption_' + (i+1) + '" /></td><td><input type="text" size="4" style="text-align:center" onkeyup="setQuesScore(' + (i+1) + ')" name="part_ques_score[' + (i+1) + ']" id="ques_score_'+ (i+1) +'" /></td></tr>').appendTo(tbody);
                    tabLoaded = false;
                    jQuery('#partTabs').tabs('add', {
                        title:partStr[i] + '大题',
                        iconCls:'icon-redo',
                        href:'<?php echo $partQuesUrl?>/type/<?php echo $paperType?>/exam/<?php echo $examId?>/subject/' + jQuery('#subject_code').val() + '/pchar/' + jQuery('#paper_char').val() + '/part/' + (i+1)
                    })
                }
                gridLoaded = true;
            } else {
                var curIdx = curNum - 1;
                for(var i=curIdx;i>=partNum;i--) {
                    tbody.find('tr:last').remove();
                    jQuery('#partTabs').tabs('close', partStr[i] + '大题');
                }
            }
        }
        */
        
        function getPartQuestions(partIdx) {
            jQuery('<div id="dlg_' + partIdx + '"></div>').appendTo('body');
            jQuery('#dlg_' + partIdx).dialog({
                title:'选择试题',
                width:600,
                height:400,
                href:'<?php echo $seleQuesUrl?>/part/' + partIdx,
                modal:true,
                iconCls:'icon-redo',
                onClose:function(){
                    jQuery('#dlg_' + partIdx).dialog('destroy');
                },
                onLoad:function(){
                    var curQuestions = [];
                    jQuery('.quesId').each(function(){curQuestions.push(jQuery(this).val())})
                    jQuery('#pQuesGrid_' + partIdx).treegrid({
                        url:'<?php echo $seleQuesUrl?>',
                        queryParams:{exam:<?php echo $examId?>, subject:jQuery('#subject_code').val(),questions:curQuestions.join(',')},
                        idField:'ques_id',
                        treeField:'ques_sumary'
                    })
                }
            })
        }
        
        function closeDlg(dlgId) {
            jQuery('#dlg_' + dlgId).dialog('destroy');
        }
        
        function addPartQues(partIdx) {
            var questions = jQuery('#pQuesGrid_' + partIdx).treegrid('getSelections');
            var quesIds = [];
            jQuery.each(questions,function(k,ques){quesIds.push(ques.ques_id)});
            var rows = jQuery('#partQuestions_' + partIdx).treegrid('getData');
            if(rows) {
                jQuery.each(rows,function(k,ques){quesIds.push(ques.ques_id)});
            }
            jQuery('#partQuestions_' + partIdx).treegrid({
                url:'<?php echo $setQuesUrl?>',
                queryParams:{quesIds:quesIds.join(',')},
                idField:'ques_id',
                treeField:'ques_sumary',
                onLoadSuccess:function(){
                    setQuesScore(partIdx)
                    setQuesNumber();
                    if(confirm('试题添加成功,是否关闭选题窗口？')) {
                        jQuery('#dlg_' + partIdx).dialog('destroy');
                    }
                }
            })
        }
        
        function setQuesScore(partIdx) {
            if(jQuery('#paper_char').val() == 'A') {
                jQuery('.quesScore' + partIdx).val(jQuery('#ques_score_' + partIdx).val());
                computeTotal();
            }
        }
        
        function computeTotal() {
            var paperScore = 0;
            jQuery('.quesScore').each(function(){
                paperScore += Math.abs(this.value);
            })
            jQuery('#paper_score').val(paperScore);
        }
        
        function setQuesNumber() {
            jQuery('.quesNumber').each(function(k,v){
                jQuery(this).val(k + 1);
            })
        }
        
        function savePaper() {
            var formData = jQuery('input,select').serialize()
            jQuery.post('<?php echo $savePaperUrl?>', formData, function(data){
                if(data.errorMsg) {
                    alert(data.errorMsg);
                } else {
                    alert('试卷添加成功');
                    parent.reloadPaper(<?php echo $dlgId?>);
                }
            }, 'json');
        }
        
        function delQues(partIdx, quesId) {
            var rows = jQuery('#partQuestions_' + partIdx).treegrid('getData');
            jQuery('#partQuestions_' + partIdx).treegrid('remove', quesId);
            setQuesNumber();
            computeTotal();
        }
        
        function quesSumary(val, data) {
            if(data.body_title) return val;
            return '<input type="hidden"  class="quesId" value="' + data.ques_id + '" />&nbsp;' + val;
        }
        
        function quesNumber(val, data) {
            if(data.body_title) return '';
            return '<input type="text" size="3" style="height:14px;text-align:center;color:blue" name="quesNumber[1][' + data.ques_id + ']" value="' + jQuery.trim(data.ques_seq) + '" class="quesNumber" />';
        }
        function quesScore(val, data) {
            if(data.body_title) return '';
            return '<input type="text" size="4" name="quesScore[1][' + data.ques_id + ']" class="quesScore quesScore1" style="color:blue;text-align:center;ime-mode:disabled" onkeyup="this.value=this.value.replace(/\D/g,\'\');computeTotal()"  />'
        }
        
        function quesLevel(val, data) {
            if(data.body_title) return '';
            var sele = '<select style="color:blue" name="quesLevel[1][' + data.ques_id + ']">';
            for(var i=1;i<=5;i++) {
                sele += '<option value="' + i + '"';
                if(i == data.ques_level){
                    sele += ' selected="true"';
                }
                sele += '>' + i + '级</option>';
            }
            sele += '</select>';
            return sele;
        }
        
        function knowledge(val, data) {
            if(data.body_title) return '';
            sele = '<select style="width:200px;color:blue" name="quesKnowledge[1][' + data.ques_id + ']">';
            if(data.knowledgeArray) {
                jQuery.each(data.knowledgeArray, function(idx, knowledge){
                    sele += '<option value="' + knowledge.knowledge_code + '"'
                    if(knowledge.knowledge_code == data.knowledge_code){
                        sele += ' selected="true"';
                    }
                    sele += '>[' + knowledge.module_caption + ']' + knowledge.knowledge_caption + '</option>';
                })
            }
            sele += '</select>';
            return sele;
        }
        
        function manage(v, data) {
            return '<a href="javascript:delQues(1, \'' + data.ques_id + '\')">移除</a>';
        }
        
        jQuery(function(){
            checkPaper();
            jQuery('#subject_code').change(checkPaper);
        })
        </script>
    </head>
    <body class="easyui-layout" fit="true" border="false">
        <div region="north" style="height:150px">
            <div class="datagrid-toolbar">
                <a href="javascript:void(0)" id="saveBtn" onclick="savePaper()" class="easyui-linkbutton" iconCls="icon-save" plain="true">保存试卷信息</a>
                <input type="hidden" id="exam_id" name="exam_id" value="<?php echo $examId?>" />
                <input type="hidden" name="paper_type" value="<?php echo $paperType?>" />
            </div>
            <table id="paperCfg" cellpadding="5" cellspacing="0" width="90%" align="center">
                <tr>
                    <th style="width:20px;overflow:hidden;display:block;white-space:nowrap">竞赛名称：</th><td><input type="text" name="exam_caption" readonly="true" value="<?php echo $examInfo['group_caption'] . '(' .$examInfo['exam_caption'] . ')'?>" /></td>
                    <th>试卷科目：</th><td>
                    <?php echo W('ArraySelect', array('options'=>$sbjArray, 'attr'=>'name="subject_code" id="subject_code"'))?>
                    </td>
                </tr>
                <tr>
                    <th>试卷名称：</th><td><input type="text" name="paper_caption" id="paper_caption" /></td>
                    <th>试卷类型：</th><td><input type="text" value="<?php echo $typeCaption?>" /></td>
                </tr>
                <tr><th>试卷总分</th><td colspan="3"><input type="text" id="paper_score" readonly="true" value="0" /></td></tr>
            </table>
        </div>
        <div region="center">
            <div class="datagrid-toolbar" id="pQuesToolbar">
            <a href="javascript:void(0)" onclick="getPartQuestions(1)" class="easyui-linkbutton" iconCls="icon-redo" plain="true">选择试题</a>&nbsp;&nbsp;<span style="color:blue">【选定试题并保存试卷后通过修改试卷信息进行试题排序】</span>
        </div>
            <table class="easyui-treegrid" id="partQuestions_1" fit="true" singleselect="true" border="false" rownumbers="true" toolbar="pQuesToolbar">
            <thead frozen="true">
                <tr>
                <th field="ques_sumary" formatter="quesSumary">摘要</th></tr>
            </thead>
            <thead>
                <tr>
                <th align="center" field="quesType_caption">题型</th>
                <th align="center" field="ques_num" formatter="quesNumber">题号</th>
                <th align="center" field="ques_score" formatter="quesScore">分值</th>
                <th align="center" field="ques_level" formatter="quesLevel">难度</th>
                <th field="ques_knowledge" formatter="knowledge">知识点</th>
                <th align="center" field="manage" formatter="manage">操作</th></tr>
            </thead>
        </table>
        </div>
    </body>
</html>