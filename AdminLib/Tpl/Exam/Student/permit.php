<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
        <script type="text/javascript">
        var curExamId = 0;
        var arrExamIds = new Array();
        function loadGroups() {
            jQuery('#stu_name').val('');
            jQuery('#groupGrid').datagrid({
                url:'<?php echo $jsonGroupUrl?>',
                queryParams:{onlyShow:1,groupType:jQuery('#groupType').val(),page:1,rows:10},
                onSelect:function(idx,data){
                    loadExams(data.group_id)
                }
            })
        }
        
        function loadExams(groupId) {
            arrExamIds = new Array();
            jQuery('#examGrid').datagrid({
                url:'<?php echo $jsonExamUrl?>',
                queryParams:{groupId:groupId},
                onLoadSuccess:function(data){
                	if (data.total > 0) {
                		$.each(data.rows, function(i, n){
	                		arrExamIds.push(n.exam_id);
	                	})
                	};
                },
                onSelect:function(idx, data){
                    curExamId = data.exam_id;
                    refreshTab();
                }
            })
        }
        
        function loadStudents() {
        	var strExamIds = arrExamIds.join(',');
            // var args = {exam:curExamId,keyword:'',examids:strExamIds};
            var args = {exam:strExamIds,keyword:''};
            var stuName = jQuery.trim(jQuery('#stu_name').val());
            if(stuName.length>0) {
                args.keyword = stuName
            }
            jQuery('#greenGrid').datagrid({
                url:'<?php echo $jsonGreenUrl?>',
                queryParams:args,
                toolbar:'#greenToolbar'
            })
        }
        
        function refreshTab(title) {
            if(!title) {
                var tab = jQuery('#permit_tab').tabs('getSelected');
                var idx = jQuery('#permit_tab').tabs('getTabIndex', tab);
                jQuery('#permit_tab').tabs('select', idx);
                return;
            }
            var tabs = [
                <?php if($applyPerm):?>
                ['越级考试申请', 'applyGrid'],
                <?php endif?>
                <?php if($greenPerm):?>
                ['报名绿色通道', 'greenGrid'],
                <?php endif?>
                [false, false]
            ]
            if(curExamId) {
                for(var idx in tabs){
                    if(tabs[idx][0] == title) {
                        jQuery('#' + tabs[idx][1]).datagrid('reload', {exam:curExamId});
                    }
                }
            }
        }
        
        <?php if($applyPerm):?>
        function applyStatus(val, data) {
        	return '<span class="' + data.status.css + '">' + data.status.text + '</span>';
        }
        
        function manageApply(applyId, data) {
        	if(data.status.value == 0) {
        		return '<a href="javascript:setApplyStatus(' + applyId + ', 1, \'' + data.sname + '\')">通过</a> | <a href="javascript:setApplyStatus(' + applyId + ', -1, \'' + data.sname + '\')">拒绝</a>';
        	} else {
        		//这里可以放备注信息或短信通知功能
        		return '&nbsp;';
        	}
        }
        
        function setApplyStatus(applyId, status, stuName) {
        	if(status == 1) {
	        	if(confirm('确定要【通过】"' + stuName + '"的竞赛考试申请吗')) {
	        		jQuery.post('<?php echo $applyStatusUrl?>', {applyId:applyId, status:status}, function(data){
	        			alert('申请处理成功');
	        			jQuery('#applyGrid').datagrid('reload');
	        		}, 'json');
	        	}
        	} else {
        		var _tm = (new Date()).getTime();
        		jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
        		jQuery('#dlg_' + _tm).dialog({
        			title:'拒绝越级申请',
        			width:400,
        			height:280,
        			iconCls:'icon-cancel',
        			modal:true,
        			href:'<?php echo $refuseApplyUrl?>/id/' + applyId + '/dlg/dlg_' + _tm
        		})
        	}
        }
        <?php endif?>
        
        <?php if($greenPerm):?>
        function addGreen() {
        	if(curExamId > 0) {
        		var _tm = (new Date()).getTime();
        		jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
        		jQuery('#dlg_' + _tm).dialog({
        			title:'添加绿色通道考生',
        			href:'<?php echo $addGreenUrl?>/exam/' + curExamId + '/dlg/' + _tm,
        			iconCls:'icon-add',
        			width:800,
        			height:250,
        			modal:true,
        			onClose:function(){
        				jQuery('#dlg_' + _tm).dialog('destroy')
        			}
        		})
        	} else {
        		alert('请选择考试');
        	}
        }
        
        function greenStat() {
        	var _tm = (new Date()).getTime();
        	if(curExamId > 0) {
	        	jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
	        	jQuery('#dlg_' + _tm).dialog({
	        		title:'绿色通道报名统计',
	        		iconCls:'icon-redo',
	        		width:800,
	        		height:500,
	        		modal:true,
	        		href:'<?php echo $greenStatUrl?>/exam/' + curExamId + '/dlg/dlg_' + _tm,
	        		onClose:function(){
	        			jQuery('#dlg_' + _tm).dialog('destroy');
	        		}
	        	})
        	} else {
        		alert('请先任意选择一个本组竞赛');
        	}
        }
        <?php endif?>
        
        <?php if($isExamSuperUser):?>
        function exportGreen() {
        	if(curExamId > 0) {
        		jQuery('#hdFrame').attr('src', '<?php echo $exportGreenUrl?>/exam/' + curExamId);
        	} else {
        		alert('请先选择竞赛')
        	}
        }
        
        function delGreen(v, data) {
        	return '<a href="javascript:void(0);" onclick="doDelGreen(\'' + v + '\', \'' + data.signup_time + '\')">取消资格</a>';
        }
        function doDelGreen(id, signupTime) {
        	if(signupTime && signupTime != 'null') {
        		alert('考生已报名，请先执行取消报名操作');
        	} else {
        		if(confirm('确定要取消考生报名资格吗？')) {
        			jQuery.post('<?php echo $delGreenUrl?>', {id:id}, function(data){
        				if(data.errorMsg) {
        					alert(data.errorMsg)
        				} else {
        					alert('报名资格取消成功');
        					jQuery('#greenGrid').datagrid('reload');
        				}
        			}, 'json')
        		}
        	}
        }
        <?php endif?>
        
        jQuery(function(){
            loadGroups();
            jQuery('#applyGrid').datagrid({
            	 url:"<?php echo $jsonApplyUrl?>",
            	 view:detailview,
            	 detailFormatter:function(index,data){
            	 	var contents = '<div class="apply_reason apply_reason_' + index + '">';
            	 	if(data.apply_reason) 
            	 		contents += '<div style="border:1px solid #ddd;padding:3px"><b>申请原因：</b>' + jQuery.trim(data.apply_reason) + '</div>';
            	 	if(data.refuse_reason)
            	 		contents += '<div style="border:1px solid #ddd;padding:3px"><b>拒绝原因：</b>' + jQuery.trim(data.refuse_reason) + '</div>';
                    contents += '</div>';
                    return contents;
                },
                onExpandRow:function(index,data){
                    jQuery('#applyGrid').datagrid('selectRow',index);
                    $('#applyGrid').datagrid('fixDetailRowHeight',index); 
                    var rowCnt = jQuery('div.apply_reason').length;
                    for(var i=0;i<rowCnt;i++) {
                        if(i != index) {
                            jQuery('#applyGrid').datagrid('collapseRow',i);
                        }
                    }
                }
            })
            jQuery('#permit_tab').tabs({
            	fit:true,
                onSelect:refreshTab
            })
            
        })
        </script>
        <style type="text/css">
        	.wait{color:orange}
        	.deny{color:red}
        	.pass{color:green}
        </style>
    </head>
    <body class="easyui-layout" fit="true" border="false">
    	<iframe style="display:none" id="hdFrame" name="hdFrame"></iframe>
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
                    <table id="examGrid" class="easyui-datagrid" singleselect="true" rownumbers="true" border="false" fit="true">
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
            <div id="permit_tab">
            	<?php if($applyPerm):?>
                <div id="order" title="越级考试申请" iconCls="icon-redo">
                	<table class="easyui-datagrid" id="applyGrid" rownumbers="true" pagination="true" singleselect="true" fit="true">
                		<thead>
                			<tr><th field="sname">学生姓名</th><th field="saliascode">学号</th><th field="grade_text">当前年级</th><th field="sparents1phone">联系电话</th><th field="apply_time">申请时间</th><th field="status_text" formatter="applyStatus">状态</th><th field="op_user_id">操作员</th><th field="op_time">处理时间</th><!--th field="apply_remark">备注</th--><th field="id" formatter="manageApply">管理</th></tr>
                		</thead>
                	</table>
                </div>
                <?php endif?>
                <?php if($greenPerm):?>
                <div id="greenToolbar">
                <a href="javascript:greenStat()" class="easyui-linkbutton" iconCls="icon-redo" plain="true">收费统计</a>
                <span class="datagrid-btn-separator"></span>
                <a href="javascript:addGreen()" class="easyui-linkbutton" iconCls="icon-add" plain="true">添加考生</a> 
                <span class="datagrid-btn-separator"></span>
                <?php if($isExamSuperUser):?>
                <a href="javascript:exportGreen()" class="easyui-linkbutton" iconCls="icon-redo" plain="true">导出报名统计表</a>
                <?php endif?>
                姓名：<input type="text" size="14" name="stu_name" id="stu_name" placeholder="姓名/学号" />
                <a href="javascript:loadStudents()" class="easyui-linkbutton" iconCls="icon-search" plain="true">查询</a>
                </div>
                <div id="green" title="报名绿色通道" iconCls="icon-redo">
                	<table class="easyui-datagrid" id="greenGrid" rownumbers="true" pagination="true" singleselect="true" fit="true" url="<?php echo $jsonGreenUrl?>" toolbar="#greenToolbar">
                		<thead>
                			<tr><th field="sname">学生姓名</th><th field="saliascode">学号</th><th field="scode">学员编码</th><th field="grade_text">当前年级</th><th field="exam_caption">竞赛名称</th><th field="rcode">准考证号</th><th field="sparents1phone">联系电话</th><th field="area_name">缴费前台</th><th field="signup_time">缴费时间</th><th field="operator">操作员</th><th field="create_at">操作时间</th>
                			<?php if($isExamSuperUser):?>
                			<th field="id" formatter="delGreen">取消资格</th>
                			<?php endif?>
                			</tr>
                		</thead>
                	</table>
                </div>
                <?php endif?>
            </div>
        </div>
        
    </body>
</html>