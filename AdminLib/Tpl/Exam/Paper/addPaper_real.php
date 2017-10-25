<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <script type="text/javascript">
        var paperCheckResult = false;
        var partStr = ['一', '二', '三', '四', '五', '六', '七', '八', '九', '十'];
        var gridLoaded = false;
        function checkPaper() {
            jQuery('#part_num').val(1);
            jQuery('#part_num').numberspinner('enable');
            jQuery.post('<?php echo $paperCheckUrl?>', {exam:<?php echo $examId?>,type:'<?php echo $paperType?>', 'subject':jQuery('#subject_code').val(), 'pchar':jQuery('#paper_char').val()}, function(data){
                if(data.errorMsg) {
                    alert(data.errorMsg);
                    paperCheckResult = false;
                    jQuery('#saveBtn').linkbutton('disable');
                    clearPart();
                } else {
                    paperCheckResult  = true;
                    jQuery('#saveBtn').linkbutton('enable');
                    clearPart();
                    if(data.part_count) {
                        jQuery('#part_num').val(data.part_count)
                        jQuery('#part_num').numberspinner('disable');
                        setPartNum();
                        jQuery.each(data.part_list, function(k,part){
                            jQuery('#part_caption_' + part.part_id).val(part.part_caption);
                            jQuery('#ques_score_' + part.part_id).val(part.part_ques_score).attr('readonly',true);
                        })
                    }
                }
            }, 'json');
        }
        
        function setPartNum(){
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
        
        function clearPart() {
            var tbody = jQuery('#partList').find('tbody')
            var curNum = tbody.find('tr').length;
            for(var i=curNum-1;i>=0;i--) {
                tbody.find('tr:last').remove();
                jQuery('#partTabs').tabs('close', i);
            }
            if(paperCheckResult) {
                setPartNum();
            }
        }
        
        function getPartQuestions(partIdx) {
            if(jQuery('#paper_char').val() == 'B') {
                alert('B卷不允许选择试题，默认从A卷派生');
                return;
            }
            jQuery('<div id="dlg_' + partIdx + '"></div>').appendTo('body');
            jQuery('#dlg_' + partIdx).dialog({
                title:'选择试题[<b style="color:red">第' + partStr[partIdx-1] + '大题</b>]',
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
        
        jQuery(function(){
            jQuery('#part_num').numberspinner({onChange:setPartNum})
            checkPaper();
            jQuery('#subject_code,#paper_char').change(checkPaper);
            jQuery('#partList').find('tr').not(jQuery('#partList').find('tr')[0])
                                        .mouseover(function(){this.style.backgroundColor='#eee'})
                                        .mouseout(function(){this.style.backgroundColor='#fff'})
        })
        </script>
    </head>
    <body class="easyui-layout" fit="true" border="false">
        <div region="north" style="height:170px">
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
                <tr>
                    <th>大题数量：</th><td><input type="text" class="part_num" id="part_num" value="1" min="1" max="10" /></td>
                    <th>试卷标识：</th><td><?php echo W('ArraySelect', array('attr'=>'name="paper_char" id="paper_char"', 'options'=>array('A'=>'A卷', 'B'=>'B卷')))?></td>
                </tr>
                <tr><th>试卷总分</th><td colspan="3"><input type="text" id="paper_score" readonly="true" value="0" /></td></tr>
            </table>
        </div>
        <div region="west" style="width:300px">
            <table id="partList" width="100%" cellpadding="5" cellspacing="0">
                <thead>
                    <tr style="background:#ddd"><th>题号</th><th>大题名称</th><th>每题分值</th></tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div region="center">
            <div id="partTabs" class="easyui-tabs" fit="true">
                
            </div>
        </div>
    </body>
</html>