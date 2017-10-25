<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
        <script type="text/javascript">
        var examId = 0;
        var curSubject = {examId:0, subject:''};
        var subjectArray = <?php echo $jsonSubjectArray;?>;
        var editSubjects = {};
        
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
                    <?php if($permValue & $PERM_WRITE):?>
                    onLoadSuccess:function(){
                        loadQuestion();
                    },
                    <?php endif?>
                    onSelect:function(idx, data) {
                        curSubject = {examId:examId, subject:data.subject_code};
                        loadQuestion();
                    }
                    
                })
            }
        }
        
        function loadQuestion() {
            if(examId >0) {
                jQuery('#quesGrid').treegrid({
                    url:'<?php echo $jsonQuesUrl?>',
                    queryParams:curSubject,
                    idField:'ques_id',
                    treeField:'ques_sumary'
                });
            }
        }
        <?php if($permValue & $PERM_WRITE):?>
        function addSubject() {
            if(false == examId) {
                alert('请选择竞赛');
            } else {
                var seleSbj = jQuery('#subject_code').val();
                var rows = jQuery('#subjectGrid').datagrid('getRows');
                var sbjExists = false;
                for(var i=0;i<rows.length;i++) {
                    if(seleSbj == rows[i].subject_code) {
                        sbjExists = true;
                    }
                }
                if(false == sbjExists) {
                    jQuery.post('<?php echo $addSubjectUrl?>', {exam:examId, subject:seleSbj}, function(data){
                        if(data.success) {
                            alert('竞赛科目添加成功');
                            jQuery('#subjectGrid').datagrid('reload');
                        } else {
                            alert(data.errorMsg);
                        }
                    }, 'json');
                } else {
                    alert('科目已存在，请不要重复添加');
                }
            }
        }
        
        function delSubject(subjectCode, subjectCaption) {
            if(confirm('确定要删除竞赛科目“' + subjectCaption + '”吗？')) {
                jQuery.post('<?php echo $delSubjectUrl?>', {exam:examId, subject:subjectCode}, function(data){
                    if(data.success) {
                        alert('竞赛科目删除成功');
                        jQuery('#subjectGrid').datagrid('reload');
                        if(subjectCode == curSubject.subject) {
                            curSubject.subject = '';
                            loadQuestion();
                        }
                    } else {
                        alert(data.errorMsg);
                    }
                }, 'json');
            }
        }
        
        function sbjManage(val, data) {
            return '<a href="javascript:delSubject(\'' + data.subject_code + '\', \'' + data.subject_name + '\')">删除</a>';
        }
        
        function addQuestion(quesType, quesTypeCaption) {
            if('' == curSubject.subject) {
                alert('请选择试题科目');
                return;
            }
            var _tm = (new Date()).getTime();
            jQuery('<div id="dialog_' + _tm + '"></div>').appendTo('body');
            jQuery('#dialog_' + _tm).dialog({
                title:'&nbsp;添加竞赛试题（' + quesTypeCaption + '）',
                width:930,
                height:500,
                resizable:true,
                maximizable:true,
                iconCls:'icon-add',
                modal:true,
                onClose:function(){
                    jQuery('#dlg_' + _tm).dialog('destroy');
                },
                content:'<iframe scrolling="no" frameborder="no" style="width:100%;height:99.8%;margin:-1px" src="<?php echo $addQuesUrl?>/exam/' + examId + '/subject/' + curSubject.subject + '/' + 'quesType/' + quesType + '/dlg/' + _tm + '"></iframe>'
                
            })
        }
        
        function delQuestion(quesId, quesCaption) {
        	if(confirm('确定要删除试题“' + quesCaption + '”吗？该操作不可恢复，建议使用修改试题的功能')) {
        		jQuery.post('<?php echo $delQuesUrl?>', {quesId:quesId}, function(data){
        			alert('试题删除成功');
        			jQuery('#subjectGrid').datagrid('reload')
        			jQuery('#quesGrid').treegrid('reload');
        		}, 'json');
        	}
        }
        
        function quesInfo(quesId, quesSumary) {
            var _tm = (new Date()).getTime();
            <?php if($permValue & $PERM_WRITE):?>
            var title = "修改试题信息（" + quesSumary + "）";
            var iconCls = 'icon-edit';
            <?php else:?>
            var title = "查看试题信息（" + quesSumary + "）";
            var iconCls = 'icon-view';
            <?php endif?>
            jQuery('<div id="dialog_' + _tm + '"></div>').appendTo('body');
            jQuery('#dialog_' + _tm).dialog({
                title:title,
                width:930,
                height:500,
                resizable:true,
                maximizable:true,
                iconCls:iconCls,
                modal:true,
                onClose:function(){
                    jQuery('#dlg_' + _tm).dialog('destroy');
                },
                content:'<iframe scrolling="no" frameborder="no" style="width:100%;height:99.8%;margin:-1px" src="<?php echo $quesInfoUrl?>/ques/' + quesId + '/dlg/' + _tm + '"></iframe>'
                
            })
        }
        
        <?php endif?>
        
        <?php if($urlPerm):?>
        function scoreUrl(val,data) {
        	if(val) {
        		val = '<b style="color:red">自定义</b>';
        	} else {
        		val = '默认';
        	}
        	return '<a href="javascript:setScoreUrl(\'' + data.subject_code + '\')">' + val + '</a>';
        }
        
        function setScoreUrl(subjectCode) {
        	var _tm = (new Date()).getTime();
        	jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
        	jQuery('#dlg_' + _tm).dialog({
        		title:'成绩查询地址设置',
        		height:240,
        		width:600,
        		top:50,
        		iconCls:'icon-edit',
        		modal:true,
        		href:'<?php echo $setScoreUrl?>/exam/' + examId + '/subject/' + subjectCode + '/dlg/dlg_' + _tm,
        		onClose:function(){
        			jQuery('#dlg_' + _tm).dialog('destroy');
        		}
        	})
        }
        <?php endif?>
        
        function quesManage(val, data) {
            if(jQuery.trim(data.body_title)) return '';
            <?php if($permValue & PERM_WRITE):?>
                return '<a href="javascript:quesInfo(\'' + data.ques_id + '\', \'' + data.ques_sumary + '\')">修改</a> | <a href="javascript:delQuestion(\'' + data.ques_id + '\', \'' + data.ques_sumary + '\')">删除</a>';
            <?php else:?>
                return '<a href="javascript:quesInfo(\'' + data.ques_id + '\', \'' + data.ques_smary + '\')">查看</a>';
            <?php endif?>
        }
        
        function closeDlg(dlgId) {
            jQuery('#dialog_' + dlgId).dialog('destroy');
            jQuery('#quesGrid').treegrid({
                    url:'<?php echo $jsonQuesUrl?>',
                    queryParams:curSubject,
                    idField:'ques_id',
                    treeField:'ques_sumary'
            });
        }
        
        function quesSumary(val, data) {
            val = jQuery.trim(val);
            <?php if($permValue & $PERM_WRITE):?>
                return '<input type="text" style="text-align:center;width:20px;border:1px solid #ccc" name="ques_seq[' + data.ques_id + ']" value="' + jQuery.trim(data.ques_seq) + '" />&nbsp;' + val;
            <?php else:?>
                return val;
            <?php endif?>
        }
        
        jQuery(function(){
            loadExams();
            <?php if($permValue & $PERM_WRITE):?>
            jQuery('#quesTypes .quesType').click(function(){
                addQuestion(jQuery(this).attr('quesType'), jQuery(this).text());
            })
            <?php endif?>
        })
        </script>
    </head>
    <body class="easyui-layout" fit="true" border="false">
        <div region="west" style="width:350px">
            <div class="easyui-layout" fit="true" border="false">
                <div region="north" style="height:220px" >
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
                            <th field="subject_name">竞赛科目</th>
                            <th field="ques_cnt" align="center">试题数量</th>
                            <?php if($urlPerm):?>
                            <th field="score_url" formatter="scoreUrl" align="center">查询地址</th>
                            <?php endif?>
                            <?php if($permValue & $PERM_WRITE):?>
                            <th field="manage" formatter="sbjManage">删除</th>
                            <?php endif?>
                        </tr>
                    </thead>
                </table>
                </div>
            </div>
        </div>
        <div region="center">
        <?php if($permValue & $PERM_WRITE):?>
        <div id="quesToolbar">
            <a href="javascript:void(0)" class="easyui-menubutton" iconCls="icon-add" plain="true" menu="#quesTypes">添加试题</a>
            <span class="datagrid-btn-separator"></span>
            <a href="javascript:sortQuestion()" class="easyui-linkbutton" iconCls="icon-reload" plain="true">排序</a>
            <span class="datagrid-btn-separator"></span>
            &nbsp;查询：<input type="text" placeholder="请输入关键词" />
            <a href="javascript:sortQuestion()" class="easyui-linkbutton" iconCls="icon-search">搜索</a>
        </div>
        <div id="quesTypes">
            <?php foreach($quesTypeArray as $quesType=>$typeCaption):?>
            <div iconCls="icon-redo" class="quesType" quesType="<?php echo $quesType?>"><?php echo $typeCaption?></div>
            <?php endforeach;?>
        </div>
        <?php endif?>
        <table id="quesGrid" class="easyui-datagrid" fit="true" border="false" singleselect="true" rownumbers="true" toolbar="#quesToolbar">
        <thead frozen="true">
            <tr>
                <th field="ques_sumary" formatter="quesSumary">试题描述</th>
            </tr>
        </thead>
        <thead>
            <tr>
                <th field="subject_caption">所属学科</th>
                <th field="quesType_caption">试题类型</th>
                <th field="knowledge_caption">知识点</th>
                <th field="ques_level" align="center">难易级别</th>
                <th field="update_user">操作员</th>
                <th field="update_at">操作时间</th>
                <?php if($permValue & $PERM_WRITE):?>
                <th field="manage" formatter="quesManage">管理</th>
                <?php endif?>
            </tr>
        </thead>
        </table>
        </div>
    </body>
</html>