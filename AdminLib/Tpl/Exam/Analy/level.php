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
                        loadLevels();
                    }
                })
            }
        }
        
        function loadLevels() {
        	if(curSubject.examId >0 && curSubject.subject) {
        		jQuery('#levelGrid').datagrid({
        			url:'<?php echo $jsonLevelUrl?>',
        			queryParams:curSubject,
        			onSelect:function(idx, data) {
        				<?php if($permValue & PERM_WRITE):?>
        				jQuery('body').layout('remove', 'east');
        				jQuery('body').layout('add', {
		        			region:'east',
		        			title:'等级分析维护 - [' + data.caption + ']',
		        			iconCls:'icon-edit',
		        			href:'<?php echo $levelAnalyUrl?>/exam/' + curSubject.examId + '/subject/' + curSubject.subject + '/level/' + data.level,
		        			width:450,
		        			collapsible:false,
		        			tools:analyTools
		        		})
		        		<?php endif?>
        			},
        			onUnselect:function(){
        				jQuery('body').layout('remove', 'east');
        			}
        		})
        	} else {
        		alert('请选择学科');
        	}
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
            <table id="levelGrid" class="easyui-datagrid" singleselect="true" rownumbers="true" fit="true" title="等级列表" iconCls="icon-redo">
            	<thead>
            		<tr>
            			<th field="caption">等级</th>
            			<th field="ques_cnt">试题数</th>
            			<th field="level_score">总分</th>
            			<th field="level_average">平均分</th>
            			<th field="level_ratio">得分率</th>
            		</tr>
            	</thead>
            </table>
        </div>
    </body>
</html>