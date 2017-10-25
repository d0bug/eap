<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
        <script type="text/javascript">
        var curExamId = 0;
        var hasCardGroup = false;
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
                    hasCardGroup = data.exam_card_group > 0;
                    loadStudents();
                }
            })
        }
        
        function loadStudents() {
            var args = {exam:curExamId};
            var oSele = jQuery('#pos_code')[0];
            if(oSele.length == 1 || jQuery('#curExamId').val() != curExamId) {
                jQuery('#stu_name').val('');
                oSele.length = 1;
                jQuery.post('<?php echo $examPosUrl?>', args, function(data){
                    jQuery('#curExamId').val(curExamId);
                    jQuery.each(data, function(posCode, pos){
                        jQuery('<option value="' + posCode + '">' + pos.pos_caption + '[' + pos.pos_cnt + '人]</option>').appendTo(jQuery('#pos_code'));
                    })
                }, 'json');
            }
            var posCode = jQuery.trim(jQuery('#pos_code').val());
            var stuName = jQuery.trim(jQuery('#stu_name').val());
            if(posCode.length >1) {
                args.pos_code = posCode;
            }
            if(stuName.length>0) {
                args.stu_name = stuName
            }
            jQuery('#stuGrid').datagrid({
                url:'<?php echo $jsonStudentUrl?>',
                queryParams:args,
                toolbar:'#studentToolbar',
                onLoadSuccess:function(data){
                	if(hasCardGroup) {
                		jQuery('#stuGrid').datagrid('showColumn', 'card_num');
                	} else {
                		jQuery('#stuGrid').datagrid('hideColumn', 'card_num');
                	}
                }
            })
        }
        
        <?php if($exportPerm):?>
        function doExport() {
            if(false == jQuery('#curExamId').val()) {
                alert('请选择考试');
            } else {
                jQuery('#stu_name').val('');
                jQuery('#exportForm').submit()
                jQuery('#exportForm').reset();
            }
        }
        <?php endif?>
        
        <?php if($pdfPerm):?>
        function doExportPdf() {
        	if(false == jQuery('#curExamId').val()) {
                alert('请选择考试');
            } else if('0' == jQuery('#pos_code').val()) {
            	alert('请选择考点');
            } else {
            	var examId = jQuery('#curExamId').val();
            	var posCode = jQuery('#pos_code').val();
            	window.open('<?php echo $exportPdfUrl?>/exam/' + examId + '/pos/' + posCode, '_blank');
            }
        }
        <?php endif?>
        
        <?php if($posPdfPerm):?>
        function doExportPosPdf(){
        	if(false == jQuery('#curExamId').val()) {
                alert('请选择考试');
            } else if('0' == jQuery('#pos_code').val()) {
            	alert('请选择考点');
            } else {
            	var examId = jQuery('#curExamId').val();
            	var posCode = jQuery('#pos_code').val();
            	window.open('<?php echo $posPdfUrl?>/exam/' + examId + '/pos/' + posCode, '_blank');
            }
        }
        <?php endif?>
        
        <?php if($cancelPerm):?>
        function cancelLink(val, data) {
            return '<a href="javascript:doCancel(\'' + data.stu_code + '\' ,\'' + data.id + '\', \'' +  data.stu_name + '\', \'' + data.pos_caption + '\')">取消报名</a>';
        }
        
        function doCancel(stuCode, signupId, stuName, posCaption) {
            if(confirm('确定要取消“' + stuName + '”在“' + posCaption + '”的报名信息吗？')) {
                jQuery.post('<?php echo $cancelUrl?>', {stuCode:stuCode, id:signupId}, function(data){
                    if(data.errorMsg) {
                        alert(data.errorMsg);
                    } else {
                        alert('取消报名成功');
                        jQuery('#stuGrid').datagrid('reload');
                    }
                }, 'json');
            }
        }
        
        <?php endif?>
        
        <?php if($permValue & PERM_WRITE):?>
        function addStudent() {
        	if(false == curExamId) {
                alert('请选择考试');
            } else {
                var _tm = (new Date()).getTime();
                jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
                jQuery('#dlg_' + _tm).dialog({
                	modal:true,
                	title:'考生报名竞赛',
                	iconCls:'icon-add',
                	width:500,
                	height:440,
                	iconCls:'icon-add',
                	href:'<?php echo $addStudentUrl?>/exam/' + curExamId + '/dlg/dlg_' + _tm,
                	onClose:function(){
                		jQuery('#dlg_' + _tm).dialog('destroy');
                	}
                })
            }
        }
        <?php endif;?>
        
        function channelName(channel) {
        	return channel ? channel : '其他';
        }
        
        function operator(uid, data) {
        	if(/\-/.test(uid)) {
        		return uid;
        	} else {
        		return data.stu_name
        	}
        }
        
        jQuery(function(){
            loadGroups();
        })
        </script>
    </head>
    <body class="easyui-layout" fit="true" border="false">
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
            <?php if($permValue & PERM_READ):?>
            <div id="studentToolbar" class="datagrid-toolbar" style="display:none">
                <form id="exportForm" action="<?php echo $exportUrl?>" target="hdFrame" method="POST">
                <?php if($permValue & PERM_WRITE):?>
                <a href="javascript:void(0)" onclick="addStudent()" class="easyui-linkbutton" iconCls="icon-add" plain="true">考生报名</a>
                <span class="datagrid-btn-separator"></span>
                <?php endif?>
                <span class="easyui-linkbutton" iconCls="icon-tip">&nbsp;&nbsp;&nbsp;</span>
                考生筛选:<select name="pos_code" id="pos_code">
                        <option value="0">===不限考点===</option>
                    </select>
                <input type="hidden" id="curExamId" name="examId" />
                姓名：<input type="text" size="14" name="stu_name" id="stu_name" placeholder="姓名/学号" />
                <a href="javascript:loadStudents()" class="easyui-linkbutton" iconCls="icon-search" plain="true">查询</a>
                <?php if($exportPerm):?>
                <a href="javascript:doExport()" class="easyui-linkbutton" iconCls="icon-undo" plain="true">导出</a>
                <?php endif?>
                <?php if($pdfPerm):?>
                <a href="javascript:doExportPdf()" class="easyui-linkbutton" iconCls="icon-print" plain="true">导出考场名单</a>
                <?php endif?>
                <?php if($posPdfPerm):?>
                <a href="javascript:doExportPosPdf()" class="easyui-linkbutton" iconCls="icon-print" plain="true">导出考场统计表</a>
                <?php endif?>
                </form>
                <iframe name="hdFrame" id="hdFrame" style="display:none"></iframe>
            </div>
            <?php endif?>
            <table id="stuGrid" class="easyui-datagrid" singleselect="true" rownumbers="true" fit="true" pagination="true" border="false" pageList="[20,50,100]">
                <thead frozen="true">
                    <tr>
                        <th field="stu_name">姓名</th>
                        <th field="stu_code" align="center">编码</th>
                        <th field="saliascode" align="center">学号</th>
                        <th field="exam_code" align="center" sortable="true">准考证号</th>
                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th field="stu_gender" align="center">性别</th>
                        <th field="pos_caption">所在考点</th>
                        <th field="room_num" align="center">考场号</th>
                        <th field="seat_num" align="center">座位号</th>
                        <th field="stu_mobile" align="center">联系电话</th>
                        <th field="signup_time" align="center" sortable="true">报名时间</th>
                        <th field="create_user_id" formatter="operator">报名员</th>
                        <th field="card_num" align="center">激活卡号</th>
                        <th field="channel_name" formatter="channelName">来源渠道</th>
                        <?php if($cancelPerm):?>
                        <th field="stu_cancel" formatter="cancelLink">取消报名</th>
                        <?php endif?>
                    </tr>
                </thead>
            </table>
        </div>
    </body>
</html>