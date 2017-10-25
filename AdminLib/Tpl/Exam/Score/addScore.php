<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php' ?>
        <?php include TPL_INCLUDE_PATH . '/easyuiMove.php' ?>
        <?php include TPL_INCLUDE_PATH . '/juicer.php' ?>
        <script type="text/javascript">
        var examId = 0;
        var curSubject = {examId:0, subject:'', subjectName:''};
        var subjectArray = <?php echo $jsonSubjectArray;?>;
        var editSubjects = {};
        var paperChar ='A'
        
        function loadExams() {
            examId = 0;
            curSubject = {examId:0,subject:''};
            jQuery('#examGrid').datagrid({
                queryParams:{groupType:jQuery('#group_type').val()},
                onSelect:function(idx, data) {
                    examId = data.exam_id;
                    curSubject.examId = examId;
                    loadSubjects();
                }
            })
        }
        
        function loadSubjects() {
            jQuery('#subjectGrid').datagrid('loadData', []);
            if(examId > 0) {
                jQuery('#subjectGrid').datagrid({
                    url:'<?php echo $jsonSubjectUrl?>',
                    queryParams:{exam:examId},
                    onSelect:function(idx, data) {
                        curSubject = {examId:examId, subject:data.subject_code, subjectName:data.subject_name};
                        loadScoreTab();
                    }
                    
                })
            }
        }
        
        function loadScoreTab() {
            jQuery('#scoreTabs').tabs('close', 0);
            jQuery('#scoreTabs').tabs('add', {
                title:'竞赛成绩录入（' + curSubject.subjectName + '）',
                href:'<?php echo $addScoreUrl?>/exam/' + curSubject.examId + '/subject/' + curSubject.subject + '/pchar/' + paperChar,
                iconCls:'icon-add'
            });
        }
        
        jQuery(function(){
            loadExams();
        })
        </script>
    </head>
    <body class="easyui-layout" fit="true" border="false">
        <div region="west" style="width:270px">
            <div class="easyui-layout" fit="true" border="false">
                <div region="north" style="height:300px" >
                <div id="examToolbar">
                &nbsp;竞赛筛选:<?php echo W('ArraySelect', array('options'=>array_merge(array('0'=>'==选择竞赛组=='), $gTypeArray), 'attr'=>'name="group_type" id="group_type" onchange="loadExams()"'))?>
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
                <div region="center">
                <?php if($permValue & $PERM_WRITE):?>
                <div id="subjectToolbar" style="padding:3px 0px">
                    &nbsp;选择科目：<?php echo W('ArraySelect', array('attr'=>'style="padding:4px;" name="subject_code" id="subject_code"', 'options'=>$subjectArray))?>
                    <a href="javascript:addSubject()" class="easyui-linkbutton" iconCls="icon-add">添加</a>
                </div>
                <?php endif?>
                <table id="subjectGrid" class="easyui-datagrid" fit="true" border="false" singleselect="true" rownumbers="true" toolbar="#subjectToolbar" title="选择竞赛科目" iconCls="icon-redo">
                    <thead>
                        <tr>
                            <th field="subject_name" width="200">竞赛科目</th>
                        </tr>
                    </thead>
                </table>
                </div>
            </div>
        </div>
        <div region="center">
            <div class="easyui-tabs" id="scoreTabs" border="false" fit="true">
                
            </div>
        </div>
    </body>
</html>