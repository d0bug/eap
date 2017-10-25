<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
        <script type="text/javascript">
        var curExamId = 0;
        function loadGroups() {
            jQuery('#stu_name').val('');
            jQuery('#groupGrid').datagrid({
                url:'<?php echo $jsonGroupUrl?>',
                queryParams:{groupType:jQuery('#groupType').val(),page:1,rows:10},
                onSelect:function(idx,data){
                    loadExams(data.group_id)
                }
            })
        }
        
        function loadExams(groupId) {
            jQuery('#examGrid').datagrid({
                url:'<?php echo $jsonExamUrl?>',
                queryParams:{groupId:groupId},
                onSelect:function(idx, data){
                    curExamId = data.exam_id
                    loadAnalys();
                }
            })
        }
        
        function loadAnalys() {
        	if(curExamId > 0) {
        		jQuery('#analyGrid').datagrid({
        			url:'<?php echo $jsonAnalyUrl?>/exam/' + curExamId,
        			view:detailview,
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
        		alert('请选择考试');
        	}
        }
        
        function addAnaly() {
        	if(curExamId) {
	        	var _tm = (new Date()).getTime();
	        	jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
	        	jQuery('#dlg_' + _tm).dialog({
	        		title:'添加竞赛成绩综述',
	        		href:'<?php echo $addScoreAnalyUrl?>/exam/' + curExamId + '/dlg/dlg_' +_tm,
	        		width:500,
	        		height:300,
	        		modal:true,
	        		iconCls:'icon-add',
	        		onClose:function(){
	        			jQuery('#dlg_' + _tm).dialog('destroy');
	        		}
	        	})
        	} else {
        		alert('请选择考试');
        	}
        }
        
        function editAnaly() {
        	var selectRow = jQuery('#analyGrid').datagrid('getSelected');
        	if(selectRow) {
        		var _tm = (new Date()).getTime();
	        	jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
	        	jQuery('#dlg_' + _tm).dialog({
	        		title:'修改竞赛成绩评语',
	        		href:'<?php echo $editScoreAnalyUrl?>/aid/' + selectRow.analy_id + '/dlg/dlg_' +_tm,
	        		width:500,
	        		height:300,
	        		modal:true,
	        		iconCls:'icon-edit',
	        		onClose:function(){
	        			jQuery('#dlg_' + _tm).dialog('destroy');
	        		}
	        	})
        	} else {
        		alert('请选择要修改的成绩评语');
        	}
        }
        
        function delAnaly() {
        	var selectRow = jQuery('#analyGrid').datagrid('getSelected');
        	if(selectRow) {
        		jQuery.post('<?php echo $delScoreAnalyUrl?>', {aid:selectRow.analy_id}, function(data){
        			alert('成绩评语删除成功');
        			jQuery('#analyGrid').datagrid('reload');
        		})
        	}
        }
        
        
        jQuery(function(){
            loadGroups();
        })
        </script>
    </head>
    <body class="easyui-layout" fit="true">
    	<div region="west" title="竞赛筛选" style="width:290px">
            <div class="easyui-layout" fit="true" border="false">
                <div region="north" style="height:230px">
                    <div class="datagrid-toolbar" id="examToolbar">
                        竞赛筛选：<?php echo W('ArraySelect', array('options'=>array_merge(array('0'=>'==选择竞赛类别=='), $examTypeArray), 
                                                         'attr'=>'id="groupType" name="groupType" onchange="loadGroups()"')
                                    )?>
                    </div>
                    <table id="groupGrid" fit="true" singleselect="true" rownumbers="true" toolbar="#examToolbar" border="false">
                        <thead>
                            <tr>
                                <th field="group_type">竞赛组类别</th>
                                <th field="group_caption">竞赛组名称</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div region="center" title="竞赛列表">
                    <table id="examGrid" class="easyui-datagrid" singleselect="true" rownumbers="true" border="false">
                        <thead>
                            <tr>
                                <th field="group_caption">竞赛组名称</th>
                                <th field="exam_caption">竞赛名称</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div region="center">
        	<div id="mainToolbar">
        		<a href="javascript:addAnaly()" class="easyui-linkbutton" iconCls="icon-add" plain="true">添加成绩评语</a>
        		<a href="javascript:editAnaly()" class="easyui-linkbutton" iconCls="icon-edit" plain="true">修改选定评语</a>
        		<a href="javascript:delAnaly()" class="easyui-linkbutton" iconCls="icon-cancel" plain="true">删除选定评语</a>
        	</div>
        	<table id="analyGrid" class="easyui-datagrid" fit="true" border="false" singleselect="true" rownumbers="true" toolbar="#mainToolbar">
	        	<thead>
		        	<tr>
		        		<th field="paper_caption">试卷名称</th>
		        		<th field="analy_score" align="center">试卷分数</th>
		        		<th field="analy_rank" align="center">真实排名</th>
		        		<th field="analy_vrank" align="center">虚排名</th>
		        		<th field="analy_ratio" align="center">真实排名比例</th>
		        		<th field="analy_vratio" align="center">虚排名比例</th>
		        		<th field="update_at">操作时间</th>
		        		<th field="update_user">操作员</th>
		        	</tr>
	        	</thead>
        	</table>
        </div>
    </body>
</html>