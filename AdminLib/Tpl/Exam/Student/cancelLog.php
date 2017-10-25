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
                    loadLogs();
                }
            })
        }
        
        function loadLogs(isSearch) {
        	if(curExamId >0) {
        		data = {exam:curExamId}
        		if(isSearch) {
        			data['keyword'] = jQuery.trim(jQuery('#keyword').val());
        		}
        		jQuery('#logGrid').datagrid('reload', data);
        	}
        }
        
        function operator(val, data) {
        	if(/Employee/.test(val)) {
        		return val
        	} 
        	return data.sname;
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
        	<div id="logToolbar">
        		&nbsp;&nbsp;快捷搜索：<input type="text" size="40" name="keyword" id="keyword" placeholder="姓名/学号/电话/准考证号/考点编码/操作员" /><a href="javascript:loadLogs(true)" class="easyui-linkbutton" iconCls="icon-search">确定</a>
        	</div>
        	<table title="取消报名记录" class="easyui-datagrid" id="logGrid" fit="true" rownumbers="true" singleselect="true" pagination="true" toolbar="#logToolbar" iconCls="icon-search" url="<?php echo $jsonLogUrl?>">
        		<thead>
        			<tr>
        				<th field="sname">考生姓名</th>
        				<th field="saliascode">学号</th>
        				<th field="exam_code">准考证号</th>
        				<th field="pos_code">考点编码</th>
        				<th field="signup_time">报名时间</th>
        				<th field="cancel_at">取消时间</th>
        				<th field="cancel_ip">取消IP</th>
        				<th field="operator" formatter="operator">操作员</th>
        			</tr>
        		</thead>
        	</table>
        </div>
    </body>
</html>