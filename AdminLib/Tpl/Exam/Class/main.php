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
                        loadRules();
                    }
                })
            }
        }
        
        function loadRules(){
        	if(curSubject.examId > 0 && curSubject.subject) {
	        	jQuery('#ruleGrid').datagrid({
	        		url:'<?php echo $jsonRuleUrl?>/exam/' + curSubject.examId + '/subject/' + curSubject.subject,
	        		view:detailview,
	        		detailFormatter:function(index,data){
	        			var searchLink = jQuery.trim(data.class_search_link);
	        			var infoLink = jQuery.trim(data.class_info_link);
	        			var classDesc = jQuery.trim(data.class_desc);
	                    return '<div class="rule_desc rule_desc_' + index + '"><div>搜课地址：<a href="' + searchLink + '" target="_blank">' + searchLink + '</a></div><div>简章地址：<a href="' + infoLink + '" target="_blank">' + infoLink + '</a></div><div>班型摘要：' + classDesc + '</div></div>';
	                },
	                onExpandRow:function(index,data){
	                    jQuery('#ruleGrid').datagrid('selectRow',index);
	                    $('#ruleGrid').datagrid('fixDetailRowHeight',index); 
	                    var rowCnt = jQuery('div.rule_desc').length;
	                    for(var i=0;i<rowCnt;i++) {
	                        if(i != index) {
	                            jQuery('#ruleGrid').datagrid('collapseRow',i);
	                        }
	                    }
	                }
	        	})
        	} else {
        		alert('请选择学科')
        	}
        }
        
        <?php if($permValue & $PERM_WRITE):?>
        function addRule(){
        	if(curSubject.examId > 0 && curSubject.subject) {
        		var _tm = (new Date()).getTime();
        		jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
        		jQuery('#dlg_' + _tm).dialog({
        			title:'添加分班规则',
        			href:'<?php echo $addRuleUrl?>/exam/' + curSubject.examId + '/subject/' + curSubject.subject + '/dlg/dlg_' +_tm, 
        			width:680,
        			height:400,
        			iconCls:'icon-add',
        			modal:true,
        			onClose:function(){
        				jQuery('#dlg_' +_tm).dialog('destroy');
        			}
        		})
        	} else {
        		alert('请选择学科');
        	}
        }
        
        function editRule() {
        	var seleRow = jQuery('#ruleGrid').datagrid('getSelected');
        	if(seleRow) {
        		var _tm = (new Date()).getTime();
        		jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
        		jQuery('#dlg_' + _tm).dialog({
        			title:'添加分班规则',
        			href:'<?php echo $editRuleUrl?>/id/'+ seleRow.id + '/dlg/dlg_' +_tm, 
        			width:680,
        			height:400,
        			iconCls:'icon-add',
        			modal:true,
        			onClose:function(){
        				jQuery('#dlg_' +_tm).dialog('destroy');
        			}
        		})
        	} else {
        		alert('请选择要修改的规则');
        	}
        }
        
        function delRule(){
        	var seleRow = jQuery('#ruleGrid').datagrid('getSelected');
        	if(seleRow) {
        		if(confirm('确定要删除选定的分班规则吗？')) {
        			jQuery.post('<?php echo $delRuleUrl?>', {id:seleRow.id}, function(data){
        				if(data.errorMsg) {
        					alert(data.errorMsg);
        				} else {
        					alert('选定规则删除成功');
        					jQuery('#ruleGrid').datagrid('reload');
        				}
        			}, 'json');
        		}
        	}
        }
        <?php endif?>
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
            <div id="classToolbar">
            <?php if($permValue & PERM_WRITE):?>
            <a class="easyui-linkbutton" href="javascript:void(0)" onclick="addRule()" iconCls="icon-add" plain="true">添加分班规则</a>
            <a class="easyui-linkbutton" href="javascript:void(0)" onclick="editRule()" iconCls="icon-edit" plain="true">修改分班规则</a>
            <a class="easyui-linkbutton" href="javascript:void(0)" onclick="delRule()" iconCls="icon-cancel" plain="true">删除分班规则</a>
            <span class="datagrid-btn-separator"></span>
            <?php endif;?>&nbsp;
            考生分班查询：<input type="text"  name="stu_keyword" placeholder="姓名/学号/准考证号" /><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search" plain="true">查询</a>
            </div>
            <table id="ruleGrid" class="easyui-datagrid" pagination="true" singleselect="true" rownumbers="true" toolbar="#classToolbar" fit="true">
            	<thead>
            		<tr>
            			<th field="subject_name">学科</th>
            			<th field="class_codepre">学期</th>
            			<th field="class_name">分班名称</th>
            			<th field="class_level">报班级别</th>
            			<th field="class_weight">录取优先级</th>
            			<th field="group_title">筛选组</th>
            		</tr>
            	</thead>
            </table>
        </div>
    </body>
</html>