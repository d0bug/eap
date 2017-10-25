<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
        <script type="text/javascript">
        var examId = 0;
        var paperId = 0;
        var paperCaption ='';
        var curPaper;
        
        function loadExams() {
            examId = 0;
            paperId = 0;
            jQuery('#examGrid').datagrid({
                queryParams:{groupType:jQuery('#group_type').val()},
                onSelect:function(idx, data) {
                    examId = data.exam_id;
                    loadPapers();
                }
            })
        }
        
        function loadPapers() {
            paperId = 0;
            jQuery('#paperGrid').datagrid('loadData', []);
            jQuery('#quesGrid').treegrid('loadData', []);
            if(examId > 0) {
                jQuery('#paperGrid').datagrid({
                    url:'<?php echo $jsonPaperUrl?>',
                    queryParams:{exam:examId},
                    onSelect:function(idx, data) {
                        paperId = data.paper_id;
                        curPaper = data;
                        paperCaption = data.paper_caption;
                        loadPaperQuestion(data.paper_type);
                    }
                })
            }
        }
        
        function loadPaperQuestion(paperType) {
            if(paperId>0)  {
                if(paperType != 'virtual') {
                    jQuery('#quesGrid').treegrid({
                        <?php if($permValue & $PERM_WRITE):?>
                        toolbar:'#paperManage',
                        <?php endif?>
                        url:'<?php echo $jsonPQuesUrl?>/paper/' + paperId,
                        queryParams:{pid:paperId},
                        idField:'ques_id',
                        treeField:'ques_sumary'
                    })
                } else {
                    var _tm = (new Date()).getTime();
                    jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
                    jQuery('#dlg_' + _tm).dialog({
                        onClose:function(){
                            jQuery('#dlg_' + _tm).dialog('destroy');
                        }
                    })
                }
            }
        }
        
        function quesSumary() {
            
        }
        
        <?php if($permValue & $PERM_WRITE):?>        
        function delPaper() {
            var confirmStr = '确定要删除试卷“' + paperCaption + '”吗？';
            if(curPaper.paper_char == 'A') {
                confirmStr += '\n\n注意：选定试卷为A卷，若包含B卷，则两卷同时删除';
            }
            if(confirm(confirmStr)) {
                jQuery.post('<?php echo $delPaperUrl?>', {exam:curPaper.exam_id, 
                                                          paper:paperId, 
                                                          type:curPaper.paper_type}, function(data){
                    if(data.errorMsg) {
                        alert(data.errorMsg);
                    } else {
                        alert('试卷删除成功');
                        jQuery('#quesGrid').treegrid('loadData', []);
                        jQuery('#paperGrid').datagrid('reload');
                    }
                }, 'json');
            }
        }
        
        function addPaper(paperType) {
            if(examId > 0) {
                var _tm = (new Date()).getTime();
                jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
                jQuery('#dlg_' + _tm).dialog({
                    title:'添加竞赛试卷',
                    width:800,
                    height:500,
                    resizable:true,
                    maximizable:true,
                    iconCls:'icon-add',
                    modal:true,
                    onClose:function(){
                        jQuery('#dlg_' + _tm).dialog('destroy');
                    },
                    content:'<iframe scrolling="no" frameborder="no" style="width:100%;height:99.8%;margin:-1px" src="<?php echo $addPaperUrl?>/exam/' + examId + '/type/' + paperType + '/dlg/' + _tm + '"></iframe>'
                })
            } else {
                alert('请选择竞赛');
            }
        }
        
        function reloadPaper(dlgId) {
            jQuery('#dlg_' + dlgId).dialog('destroy');
            jQuery('#paperGrid').datagrid('reload');
        }
        
        function doModify(type) {
            var cntDict = {};
            var modify = true;
            if(type  == 'seq') {
                jQuery('.seq').each(function(){
                    seq = Math.abs(jQuery(this).val());
                    if(seq == 0) {
                        alert('小题题号必须设置值');
                    }
                    cntDict['q_' + seq] = cntDict['q_' + seq] + 1 || 1;
                    if(cntDict['q_' + seq] > 1) {
                        alert('小题题号不能重复');
                        modify = false;
                        return;
                    }
                })
            }
            if(false == modify) return;
            var formData = jQuery('.' + type).serialize();
            jQuery.post('<?php echo $modifyUrl?>/type/' + type, formData, function(data){
                if(data.errorMsg) {
                    alert(data.errorMsg);
                } else {
                    alert('试卷修改成功');
                }
            }, 'json');
        }
        
        <?php endif?>
        <?php if($permValue & $PERM_WRITE):?>
        
        function seq(val, data) {
            if(!data.ques_score) return '';
            return '<input type="text" style="width:22px;text-align:center;color:blue" class="seq" name="seq[' + data.paper_id + '][' + data.ques_id + ']" value="' + jQuery.trim(val) + '" />';
        }
        
        function score(val, data) {
            if(!data.ques_score) return '';
            return '<input type="text" style="width:22px;text-align:center;color:blue" class="score" name="score[' + data.paper_id + '][' + data.ques_id + ']" value="' + jQuery.trim(val) + '" />';
        }
        
        function level(val,data) {
            if(!data.ques_score) return '';
            var str= '<select class="level" name="level[' + data.paper_id + '][' + data.ques_id + ']">';
            for(var i=1;i<=5;i++) {
                str+= '<option value="' + i + '"';
                if(i == jQuery.trim(val)) {
                    str += ' selected="true"';
                }
                str+= '> ' + i + ' 级' + '</option>';
            }
            str += '</select>';
            return str;
        }
        <?php endif?>
        
        function sumary(val, data) {
            <?php if($permValue & $PERM_WRITE):?>
            if(data.is_paper) {
                return '<input type="text" class="paper_caption" name="paper_caption[' + data.paper_id + ']" style="width:176px" value="' + val + '" /><input onclick="doModify(\'paper_caption\')" type="button" value="修改试卷标题" style="border:1px solid #666;height:17px" />';
            } else if(data.is_part) {
                return data.part_prefix + '<input type="text" class="part_caption" style="width:100px;" name="part_caption[' + data.paper_id + '][' + data.part_id + ']" value="' + val + '" /><input type="button" onclick="doModify(\'part_caption\')" style="border:1px solid #666;height:17px" value="修改大题名称" />'
            } else {
                return val;
            }
            <?php else:?>
            if(data.is_part) {
                return data.part_prefix + data.part_caption;
            } else {
                return val;
            }
            <?php endif?>
        }
        
        function knowledge(val,data) {
            if(!data.ques_score) return '';
            <?php if($permValue & $PERM_WRITE):?>
                var str = '<select style="width:140px" class="knowledge" name="knowledge[' + data.paper_id + '][' + data.ques_id + ']">';
                if(data.knowledge_array) {
                    jQuery.each(data.knowledge_array, function(k,v){
                        str += '<option value="' + v.knowledge_code + '"';
                        if(v.knowledge_code == data.ques_knowledge) {
                            str += ' selected="true"';
                        }
                        str += '>[' + v.module_caption + '] ' + v.knowledge_caption + '</option>';
                    })
                }
                str += '</select>';
                return str;
            <?php else:?>
                if('' == jQuery.trim(data.ques_knowledge)) return '';
                return data.knowledge_array[data.ques_knowledge];
            <?php endif?>
        }
        
        function operator(val,data) {
            if(!data.ques_score) return '';
            var parts = jQuery.trim(val).split('-');
            return parts[1];
        }
        
        jQuery(function(){
            loadExams();
        })
        </script>
    </head>
    <body class="easyui-layout" fit="true" border="false">
        <div region="west" style="width:370px">
            <div class="easyui-layout" fit="true" border="false">
                    <div region="north" style="height:220px" >
                        <div id="examToolbar">
                        &nbsp;竞赛筛选:<?php echo W('ArraySelect', array('options'=>array_merge(array('0'=>'==选择竞赛类别=='), $gTypeArray), 'attr'=>'name="group_type" id="group_type" onchange="loadExams()"'))?>
                        </div>
                        <table id="examGrid" url="<?php echo $jsonExamUrl?>" fit="true" border="false" singleselect="true" rownumbers="true" toolbar="#examToolbar" title="选择竞赛" iconCls="icon-redo">
                            <thead>
                                <tr>
                                    <th field="group_caption">竞赛组</th>
                                    <th field="exam_caption">竞赛名称</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div region="center" title="选择竞赛试卷">
                    <?php if($permValue & $PERM_WRITE):?>
                        <div id="paperToolbar">
                            <a href="javascript:void(0)" class="easyui-menubutton" iconCls="icon-add" plain="true" menu="#paperTypes">添加试卷</a>
                            <div id="paperTypes">
                                <?php foreach($paperTypes as $typeName=>$typeCaption):?>
                                <div iconCls="icon-redo" href="javascript:addPaper('<?php echo $typeName?>', '<?php echo $typeCaption?>')" typeName="<?php echo $typeName?>"><?php echo $typeCaption?></div>
                                <?php endforeach;?>
                            </div>
                        </div>
                        <?php endif?>
                        <table id="paperGrid" class="easyui-datagrid" singleselect="true" rownumbers="true" toolbar="#paperToolbar">
                            <thead frozen="true">
                                <tr>
                                    <th field="paper_caption">试卷名称</th>
                                </tr>
                            </thead>
                            <thead>
                                <tr>
                                    <th field="subject_caption">试卷科目</th>
                                    <th field="type_caption">试卷类别</th>
                                    <th field="paper_score">总分</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
            </div>
        </div>
        <div region="center">
        <?php if($permValue & $PERM_WRITE):?>
        <div id="paperManage" style="display:none">
            <a class="easyui-linkbutton" plain="true" href="javascript:delPaper()" iconCls="icon-cancel">删除试卷</a>
            <span class="datagrid-btn-separator"></span>
            <a class="easyui-linkbutton" plain="true" href="javascript:doModify('seq')" iconCls="icon-reload">提交题号设置</a>
            <span class="datagrid-btn-separator"></span>
            <a class="easyui-linkbutton" plain="true" href="javascript:doModify('score')" iconCls="icon-edit">提交分值设置</a>
            <span class="datagrid-btn-separator"></span>
            <a class="easyui-linkbutton" plain="true" href="javascript:doModify('level')" iconCls="icon-tip">提交难易级别设置</a>
            <span class="datagrid-btn-separator"></span>
            <a class="easyui-linkbutton" plain="true" href="javascript:doModify('knowledge')" iconCls="icon-ok">提交知识点设置</a>
        </div>
        <?php endif?>
        <table id="quesGrid" fit="true" class="easyui-treegrid" border="false" rownumbers="true" singleselect="true">
                    <thead frozen="true">
                        <tr>
                            <?php if($permValue & $PERM_WRITE):?>
                            <th field="ques_seq" formatter="seq">题号</th>
                            <?php endif;?>
                            <th field="ques_sumary" formatter="sumary">试题描述</th>
                        </tr>
                    </thead>
                    <thead>
                        <tr>
                        <th field="quesType_caption" align="center">试题类型</th>
                        <th field="ques_score" align="center" <?php if($permValue & $PERM_WRITE):?>formatter="score"<?php endif;?>>试题分值</th>
                        <th field="ques_knowledge" formatter="knowledge">所属知识点</th>
                        <th field="ques_level" align="center" <?php if($permValue & $PERM_WRITE):?>formatter="level"<?php endif;?>>难易级别</th>
                        <!--th field="update_user" align="center" <?php if($permValue & $PERM_WRITE):?>formatter="operator"<?php endif;?>>操作员</th-->
                        </tr>
                    </thead>
                </table>
        </div>
       
    </body>
</html>