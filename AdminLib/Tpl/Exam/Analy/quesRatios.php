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
        var analyTools = [{iconCls:'icon-cancel',
	                      handler:function(){
	                        jQuery('body').layout('remove', 'east');
	                      }}];
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
                        loadQuestions();
                    }
                })
            }
        }
        
        function loadQuestions() {
        	if(curSubject.examId >0 && curSubject.subject) {
        		jQuery('#quesGrid').datagrid({
        			url:'<?php echo $jsonQuesUrl?>',
        			queryParams:curSubject,
        			onSelect:function(idx, data) {
        				
        			},
        			onUnselect:function(){
        				jQuery('body').layout('remove', 'east');
        			}
        		})
        	} else {
        		alert('请选择学科');
        	}
        }
        
        function paperType(type) {
        	paperTypes = {'real':'实体卷', 'addon':'附加卷'};
        	return paperTypes[type];
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
            <table id="quesGrid" class="easyui-datagrid" singleselect="true" rownumbers="true" fit="true" title="试题列表" iconCls="icon-redo">
            	<thead>
            		<tr>
            			<th field="paper_type" formatter="paperType">试卷类型</th>
            			<th field="ques_num_text">试题题号</th>
            			<th field="ques_sumary">试题标题</th>
            			<th field="module_caption">所属模块</th>
            			<th field="knowledge_caption">所属知识点</th>
            			<th field="level_caption">难度级别</th>
            			<th field="ques_score">试题分值</th>
            			<th field="ques_average">平均分</th>
            			<th field="ques_ratio">得分率</th>
            		</tr>
            	</thead>
            </table>
        </div>
    </body>
</html>