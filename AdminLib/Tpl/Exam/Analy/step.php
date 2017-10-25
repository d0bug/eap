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
                        loadAnalys();
                    }
                })
            }
        }
        
        function loadAnalys() {
        	if(curSubject.examId >0  && curSubject.subject) {
        		jQuery('#analyGrid').datagrid({
        			url:'<?php echo $jsonStepAnalysUrl?>',
        			view:detailview,
        			queryParams:{examId:curSubject.examId,subjectCode:curSubject.subject},
        			detailFormatter:function(index,data){
	                    return '<div class="analy_text analy_text_' + index + '">' + jQuery.trim(data.analy_text) + '</div>';
	                },
	                onExpandRow:function(index,data){
	                    jQuery('#analyGrid').datagrid('selectRow',index);
	                    $('#analyGrid').datagrid('fixDetailRowHeight',index); 
	                    var rowCnt = jQuery('div.analy_text').length;
	                    for(var i=0;i<rowCnt;i++) {
	                        if(i != index) {
	                            jQuery('#analyGrid').datagrid('collapseRow',i);
	                        }
	                    }
	                }
        		})
        	} else {
        		alert('请选择考试科目');
        	}
        }
        
        function viewStatistics() {
        	if(curSubject.examId >0  && curSubject.subject) {
        		var _tm = (new Date()).getTime();
        		jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
        		jQuery('#dlg_' + _tm).dialog({
        			title:'试题分档统计',
        			width:700,
        			height:390,
        			modal:true,
        			iconCls:'icon-sum',
        			href:'<?php echo $stepStatisticsUrl?>/exam/' + curSubject.examId + '/subject/' + curSubject.subject + '/dlg/dlg_' + _tm,
        			onClose:function(){
        				jQuery('#dlg_' + _tm).dialog('destroy');
        			}
        		})
        	} else {
        		alert('请选择考试科目');
        	}
        }
        
        function addAnaly() {
        	if(curSubject.examId >0  && curSubject.subject) {
        		var _tm = (new Date()).getTime();
        		jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
        		jQuery('#dlg_' + _tm).dialog({
        			title:'添加分档话术',
        			width:500,
        			height:280,
        			modal:true,
        			iconCls:'icon-add',
        			href:'<?php echo $addAnalyUrl?>/exam/' + curSubject.examId + '/subject/' + curSubject.subject + '/dlg/dlg_' + _tm,
        			onClose:function(){
        				jQuery('#dlg_' + _tm).dialog('destroy');
        			}
        		})
        	} else {
        		alert('请选择科目');
        	}
        }
        
        function editAnaly() {
        	var analy = jQuery('#analyGrid').datagrid('getSelected');
        	if(analy) {
        		var _tm = (new Date()).getTime();
        		jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
        		jQuery('#dlg_' + _tm).dialog({
        			title:'修改分档话术',
        			width:500,
        			height:280,
        			modal:true,
        			iconCls:'icon-edit',
        			href:'<?php echo $editAnalyUrl?>/id/' + analy.id + '/dlg/dlg_' + _tm,
        			onClose:function(){
        				jQuery('#dlg_' + _tm).dialog('destroy');
        			}
        		})
        	} else {
        		alert('请选择要修改的分档话术');
        	}
        }
        
        function delAnaly() {
        	var analy = jQuery('#analyGrid').datagrid('getSelected');
        	if(analy) {
        		if(confirm('确定要删除选定分档话术吗？')) {
        			jQuery.post('<?php echo $delAnalyUrl?>',{id:analy.id}, function(data){
        				alert('分档话术删除成功');
        				jQuery('#analyGrid').datagrid('reload'); 
        			}, 'json');
        		}
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
        	<div id="stepToolbar">
        		<a href="javascript:void(0)" onclick="viewStatistics()" class="easyui-linkbutton" iconCls="icon-sum" plain="true">分档统计数据</a>
        		<a href="javascript:void(0)" onclick="addAnaly()" class="easyui-linkbutton" iconCls="icon-add" plain="true">添加分档话术</a>
        		<a href="javascript:void(0)" onclick="editAnaly()" class="easyui-linkbutton" iconCls="icon-edit" plain="true">修改分档话术</a>
        		<a href="javascript:void(0)" onclick="delAnaly()" class="easyui-linkbutton" iconCls="icon-cancel" plain="true">删除分档话术</a>
        	</div>
			<table id="analyGrid" class="easyui-datagrid" rownumbers="true" singleselect="true" toolbar="#stepToolbar" fit="true" title="分档话术设置" iconCls="icon-redo">
            	<thead>
            		<tr>
            			<th field="level_text">分档等级</th>
            			<th field="update_user">操作员</th>
            			<th field="update_at">操作时间</th>
            		</tr>
            	</thead>
            </table>
        </div>
    </body>
</html>