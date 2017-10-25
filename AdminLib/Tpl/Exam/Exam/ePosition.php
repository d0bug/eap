<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
        
        <script type="text/javascript">
        var curGroup = '';
		var curExamId = 0;
		var curExamCaption = '';
 
        
		function loadExamByGroup(){
			loadExams();
		}
		
        function loadExams() {
            jQuery('#examGrid').datagrid({
                queryParams:{groupId:jQuery('#group_id').val()},
				onSelect:function(idx,data) {
                   setExamPosition(data.exam_id, data.exam_caption);  
                }
            })
        }
        
		
		
		function setExamPosition(examId, examCaption){
			curExamId = examId;
			curExamCaption = examCaption;
			//jQuery('body').layout('resize', 'center');
			
			var west = jQuery('body').layout('panel','west');
			west.panel('resize', {width: '260'});
			
			jQuery('body').layout('remove', 'center');
			 
			
            jQuery('body').layout('add', {
                region:'center',
                title:'' ,
                width:400,
                split:true,
                border:true,
                iconCls:'icon-tip',
                collapsible:false,
                href:'<?php echo $setPositionUrl?>',
                //tools:tools
            });
			
 
		}
		
        function number(cnt, data) {
            if(cnt >0) {
                return cnt;
            }
            return ' — ';
        }
 
        
        function examInfo(examId, examCaption) {
            jQuery('#windows').html('');
            var time = (new Date()).getTime();
            jQuery('<div id="win_' + time + '"></div>').appendTo('#windows');
            <?php if($permValue & $PERM_WRITE):?>
            var title="修改竞赛信息(" + examCaption + ")";
            var iconCls = 'icon-edit';
            <?php else:?>
            var title="查看竞赛信息(" + examCaption + ")";
            var iconCls = 'icon-view';
            <?php endif?>
            jQuery('#win_' + time).dialog({
                title:title,
                collapsible:false,
                maximizable:true,
                width:740,
                height:520,
                content:'<iframe scrolling="no" frameborder="no" style="width:100%;height:99.8%;margin:-1px" src="<?php echo $examInfoUrl?>/id/' + examId + '/dlg/' + time + '"></iframe>',
                iconCls:iconCls,
                modal:true,
                buttons:[{text:'取消', iconCls:'icon-cancel', handler:function(){
                    jQuery('#win_' + time).dialog('destroy');
                }}]
            });
        }
        
        <?php if($permValue & $PERM_WRITE):?>
        function closeWin(id) {
            jQuery('#win_' + id).dialog('destroy');
        }
        
        function delExam(examId, examCaption) {
            
        }
        
        function addExam() {
            if('' == curGroup) {
                alert('请先选择竞赛组别');
                return;
            } 
            jQuery('#windows').html('');
            var time = (new Date()).getTime();
            jQuery('<div id="win_' + time + '"></div>').appendTo('#windows');
            jQuery('#win_' + time).dialog({
                title:'添加竞赛考试',
                collapsible:false,
                maximizable:true,
                width:740,
                height:520,
                content:'<iframe scrolling="no" frameborder="no" style="width:100%;height:99.8%;margin:-1px" src="<?php echo $addExamUrl?>/gid/' + curGroup + '/dlg/' + time + '"></iframe>',
                iconCls:'icon-add',
                modal:true,
                buttons:[{text:'取消', iconCls:'icon-cancel', handler:function(){
                    jQuery('#win_' + time).dialog('destroy');
                }}]
            });
        }
        
        
        function closeDialog(){
		 	jQuery('#windows').dialog('close');;
		}
        
        
        
        <?php endif?>
        
        jQuery(function(){
            loadExams();
        })
        </script>
        <style type="text/css">
        .layout-panel-west .pagination-info{display:none}
        </style>
    </head>
    <body class="easyui-layout" fit="true" border="false">
        <div region="west"  title="竞赛列表" data-options="iconCls:'icon-tip'">
            <!--table id="examGrid" pagination="true" singleSelect="true" url="<?php echo $jsonGroupUrl?>" fit="true" rownumbers="true" toolbar="#groupToolbar" pageList="[20,30,40,50]">
                <thead>
                    <tr>
                        <th field="group_caption" width="200">竞赛组名称</th>
                    </tr>
                </thead>
            </table-->
            
            <table id="examGrid" pagination="true" singleSelect="true" url="<?php echo $jsonExamUrl?>" fit="true" rownumbers="false" toolbar="#groupToolbar" pageList="[20,30,40,50]">
            <thead>
                <tr>
                    <th field="exam_id" align="center">ID</th>
                    <th field="exam_caption">竞赛名称</th>
                    <th field="update_at" width="100">操作时间</th>
                </tr>
            </thead>
        </table>
            
            <div id="groupToolbar">
                <a class="easyui-linkbutton"  iconCls="icon-blank" href="javascript:void();" plain="true">竞赛组：</a><?php echo W('ArraySelect', array('options'=>$groupArray, 'attr'=>'style="width:160px" id="group_id" onchange="loadExamByGroup()"'))?>
            </div>
        </div>
        <!--div region="center">
        <table id="examGrid2" pagination="true" singleSelect="true" url="<?php echo $jsonExamUrl?>" fit="true" rownumbers="true" toolbar="#examToolbar" pageList="[20,30,40,50]">
            <thead>
                <tr>
                    <th field="exam_id" align="center">竞赛ID</th>
                    <th field="exam_caption">竞赛名称</th>
                    <th field="sign_cnt" formatter="number" align="center">报名人数</th>
                    <th field="score_cnt" formatter="number" align="center">考试人数</th>
                    <th field="update_user">操作员</th>
                    <th field="update_at">操作时间</th>
                    <th field="manage" formatter="manage">操作</th>
                </tr>
            </thead>
        </table>
        <?php if($permValue & $PERM_WRITE):?>
        <div id="examToolbar">
            <a href="javascript:addExam()" class="easyui-linkbutton" plain="true" iconCls="icon-add">添加竞赛</a>
        </div>
        <div id="windows"></div>
        <?php endif?>
        </div-->
        <div id="windows"></div>
    </body>
</html>