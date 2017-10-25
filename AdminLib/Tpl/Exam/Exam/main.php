<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
        <script type="text/javascript" src="/static/kindeditor/kindeditor-min.js"></script>
        <script type="text/javascript">
        var curGroup = '';
        function loadGroup() {
            jQuery('#groupGrid').datagrid({
                queryParams:{groupType:jQuery('#group_type').val(), sort:'group_id', order:'desc'},
                onSelect:function(idx,data) {
                    curGroup = data.group_id;
                    loadExams();
                }
            })
        }
        
        function loadExams() {
            jQuery('#examGrid').datagrid({
                queryParams:{groupType:jQuery('#group_type').val(),
                             groupId:curGroup}
            })
        }
        
        function number(cnt, data) {
            if(cnt >0) {
                return cnt;
            }
            return ' — ';
        }
        
        function manage(val, data) {
            <?php if($permValue & $PERM_WRITE):?>
            var str = '<a href="javascript:examInfo(' + data.exam_id + ', \'' + data.exam_caption + '\')">修改</a>';
            if(Math.abs(data.signup_cnt) == 0) {
                str += ' | <a href="javascript:delExam(' + data.exam_id + ', \'' + data.exam_caption + '\')">删除</a>';
            }
            <?php else:?>
            var str = '<a href="javascript:examInfo(' + data.exam_id + ', \'' + data.exam_caption + '\')">查看</a>';
            <?php endif?>
            return str;
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
                }}],
                onClose:function(){
                	jQuery('#win_' + time).dialog('destroy');
                }
            });
        }
        
        <?php if($permValue & $PERM_WRITE):?>
        function closeWin(id) {
            jQuery('#win_' + id).dialog('destroy');
        }
        
        function delExam(examId, examCaption) {
            if(confirm('确定要删除竞赛“' + examCaption + '”吗？')) {
                jQuery.post('<?php echo $delExamUrl?>', {exam:examId}, function(data){
                    if(!data.errorMsg) {
                        alert('竞赛删除成功');
                        loadExams();
                    } else {
                        alert(data.errorMsg);
                    }
                })
            }
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
                }}],
                onClose:function(){
                	jQuery('#win_' + time).dialog('destroy');
                }
                
            });
        }
        <?php endif?>
        
        jQuery(function(){
            loadGroup();
            loadExams();
        })
        </script>
        <style type="text/css">
        .layout-panel-west .pagination-info{display:none}
        </style>
    </head>
    <body class="easyui-layout" fit="true" border="false">
        <div region="west" style="width:320px" title="竞赛组别">
            <table id="groupGrid" pagination="true" singleSelect="true" url="<?php echo $jsonGroupUrl?>" fit="true" rownumbers="true" toolbar="#groupToolbar" pageList="[20,30,40,50]">
                <thead>
                    <tr>
                        <th field="group_caption" width="200">竞赛组名称</th>
                    </tr>
                </thead>
            </table>
            <div id="groupToolbar">
                竞赛组类别：<?php echo W('ArraySelect', array('options'=>$gTypeArray, 'attr'=>'style="width:200px" id="group_type" onchange="loadGroup()"'))?>
            </div>
        </div>
        <div region="center">
        <table id="examGrid" pagination="true" singleSelect="true" url="<?php echo $jsonExamUrl?>" fit="true" rownumbers="true" toolbar="#examToolbar" pageList="[20,30,40,50]">
            <thead>
                <tr>
                    <th field="exam_id" align="center">竞赛ID</th>
                    <th field="exam_caption">竞赛名称</th>
                    <th field="exam_signup_start">报名开始时间</th>
                    <th field="exam_signup_stop">报名停止时间</th>
                    <th field="signup_cnt" formatter="number" align="center">报名人数</th>
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
        </div>
    </body>
</html>