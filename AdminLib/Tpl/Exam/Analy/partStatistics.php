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
                        loadParts();
                    }
                })
            }
        }
        
        function loadParts() {
        	if(curSubject.examId >0 && curSubject.subject) {
        		jQuery('#partGrid').datagrid({
        			url:'<?php echo $jsonPartUrl?>',
        			queryParams:curSubject,
        			onSelect:function(idx,data) {
        				<?php if($permValue & PERM_WRITE):?>
        				jQuery('body').layout('remove', 'east');
        				jQuery('body').layout('add', {
		        			region:'east',
		        			title:'大题综述分档 - [' + data.part_caption + ']',
		        			iconCls:'icon-edit',
		        			href:'<?php echo $partAnalyUrl?>/exam/' + curSubject.examId + '/subject/' + curSubject.subject + '/part/' + data.part_num,
		        			width:450,
		        			collapsible:false,
		        			tools:analyTools
		        		});
		        		<?php endif?>
        			},
        			onUnselect:function() {
        				jQuery('body').layout('remove', 'east');
        			}
        		})
        	} else {
        		alert('请选择竞赛科目');
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
            <table id="partGrid" class="easyui-datagrid" pagination="true" singleselect="true" rownumbers="true" fit="true" title="试卷大题列表" iconCls="icon-redo">
            	<thead>
            		<tr>
            			<th field="part_num" align="center">大题号</th>
            			<th field="part_caption">大题名称</th>
            			<th field="ques_cnt" align="center">试题数目</th>
            			<th field="part_score" align="center">成绩总分</th>
            			<th field="part_average" align="center">平均分</th>
            			<th field="part_ratio" align="center">得分率</th>
            		</tr>
            	</thead>
            </table>
        </div>
    </body>
</html>