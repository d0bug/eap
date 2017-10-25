<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php' ?>
        <?php include TPL_INCLUDE_PATH . '/easyuiMove.php' ?>
        <?php include TPL_INCLUDE_PATH . '/juicer.php' ?>
        <style type="text/css">
		a{text-decoration:none}
		a:hover{text-decoration:underline}
		</style>
        <script type="text/javascript">
    	var curGroup = null;
    	var curExam = 0;
    	var examCaption = '';
    	var curAwardType  = '';
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
                             groupId:curGroup},
                onSelect:function(idx,data) {
                	curExam = data.exam_id;
                	examCaption = data.group_caption  + ' - ' +  data.exam_caption;
                	loadAwards();
                }
            })
        }
        
        function loadAwards() {
        	jQuery('#awardGrid').datagrid({
        		url:'<?php echo $jsonAwardUrl?>',
        		queryParams:{exam_id:curExam}
        	})
        	
        }
        
        <?php if($permValue & PERM_WRITE):?>
        function addAward() {
        	if(curExam ==0) {
        		alert('请选择竞赛');
        		return;
        	}
        	var url = '<?php echo $addAwardUrl?>/exam/' + curExam;
        	var title="添加竞赛奖项";
        	var iconCls = 'icon-add';
        	
        	showAwardDlg(title, url, iconCls);
        }
        
        function editAward(id) {
        	var url = '<?php echo $editAwardUrl?>/id/' + id;
        	var title = "修改奖项信息";
        	var iconCls = "icon-save";
        	
        	showAwardDlg(title, url, iconCls);
        }
        
        
        function showAwardDlg(title, url, iconCls) {
        	var _tm = (new Date()).getTime();
        	jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
        	jQuery('#dlg_' + _tm).dialog({
        		title:title,
        		href:url,
        		width:500,
        		height:300,
        		iconCls:iconCls,
        		modal:true,
        		buttons:[{
        			text:"保存",
        			iconCls:'icon-save',
        			handler:function(){
        				if(false == jQuery.trim(jQuery('#award_caption').val()) || false == jQuery('#award_score').val()) {
        					alert('奖项信息不完整');
        					return;
        				}
        				var formData = jQuery('#awardForm').serialize();
        				jQuery.post('<?php echo $saveAwardUrl?>', formData, function(data){
        					if(data.errorMsg) {
        						alert(data.errorMsg);
        					} else {
        						alert(data.message);
        						jQuery('#dlg_' + _tm).dialog('destroy');
        						jQuery('#awardGrid').datagrid('reload');
        					}
        				}, 'json');
        			}
        		}, {
        			text:'取消',
        			iconCls:'icon-cancel',
        			handler:function(){
        				jQuery('#dlg_' + _tm).dialog('destroy');
        			}
        		}],
        		onClose:function(){
        			jQuery('#dlg_' + _tm).dialog('destroy');
        		}
        	})
        }
        
        function delAward(id) {
        	if(confirm('确定要删除选定奖项设置吗？')) {
        		jQuery.post('<?php echo $delAwardUrl?>', {id:id}, function(data){
        			alert('选定奖项删除成功');
        			jQuery('#awardGrid').datagrid('reload');
        		},'json');
        	}
        }
        
        
        function tplSetting() {
        	if(curExam ==0) {
        		alert('请选择竞赛');
        		return;
        	}
        	top.jQuery('#mainTab').tabs('close', '奖项模板设置');
        	top.jQuery('#mainTab').tabs('add', {
        		title:'奖项模板设置',
        		content:'<iframe width="100%" height="99%" frameborder="no" scrolling="yes" style="overflow-x:hidden;" src="<?php echo $tplSettingUrl?>/exam/' + curExam + '"></iframe>',
        		closable:true
        	})
        }
        
        function manage(val, data) {
        	return  '<a href="javascript:editAward(\'' + data.id + '\')">修改</a> | <a href="javascript:delAward(\'' + data.id + '\')">删除</a>';
        }
        <?php endif?>
        
        function exCaption(val, data) {
        	return  examCaption;
        }
        
        function stuCount(val, data) {
        	return '<a href="javascript:showStuList(\'' + data.id + '\', \'' + data.type_name + " - " + data.award_caption + '\')">' + val + '&nbsp;人</a>';
        }
        
        function showStuList(awardId, awardCaption) {
        	var _tm = (new Date()).getTime();
        	jQuery('<div id="dlg_' + _tm + '" ></div>').appendTo('body');
        	jQuery('#dlg_' + _tm).dialog({
        		title:awardCaption + ' - 获奖名单',
        		href:'<?php echo $stuListUrl?>/id/' + awardId,
        		width:700,
        		height:400,
        		modal:true,
        		iconCls:'icon-redo',
        		onClose:function(){
        			jQuery('#dlg_' + _tm).dialog('destroy');
        		}
        	})
        }
        
        
        
        jQuery(function(){
            loadGroup();
            loadExams();
        })
        </script>
    </head>
    <body class="easyui-layout" fit="true" border="false">
    	<div region="west" style="width:300px" title="选择竞赛">
    		<div class="easyui-layout" border="false" fit="true">
    			<div id="groupToolbar">
    			&nbsp;竞赛筛选：<?php echo W('ArraySelect', array('options'=>$gTypeArray, 'attr'=>'id="group_type" onchange="loadGroup()"'))?>
    			</div>
    			<div region="north" style="height:200px">
    				<table id="groupGrid"  singleSelect="true" url="<?php echo $jsonGroupUrl?>" fit="true" rownumbers="true" toolbar="#groupToolbar">
		                <thead>
		                    <tr>
		                    	<th field="group_type">竞赛类别</th>
		                        <th field="group_caption" width="200">竞赛组名称</th>
		                    </tr>
		                </thead>
		            </table>
	            </div>
	            <div region="center">
	            	<table id="examGrid"  singleSelect="true" url="<?php echo $jsonExamUrl?>" fit="true" rownumbers="true">
			            <thead>
			                <tr>
			                    <th field="exam_id" align="center">竞赛ID</th>
			                    <th field="exam_caption">竞赛名称</th>
			                </tr>
			            </thead>
			        </table>
	            </div>
	        </div>
    	</div>
    	<div region="center">
    		<?php if($permValue & PERM_WRITE):?>
    		<div id="awardToolbar">
    			<a href="javascript:addAward()" class="easyui-linkbutton" plain="true" iconCls="icon-add">添加奖项</a>
    			<span class="datagrid-btn-separator"></span>
    			<a href="javascript:tplSetting()" class="easyui-linkbutton" plain="true" iconCls="icon-redo">奖项模板设置</a>
    		</div>
    		<?php endif?>
    		<table class="easyui-datagrid" toolbar="#awardToolbar" id="awardGrid" fit="true" border="false" pagination="true" rownumbers="true" singleselect="true">
    		<thead>
    			<tr>
    				<th field="exam_caption" formatter="exCaption">竞赛名称</th>
    				<th field="type_name">奖项类别</th>
    				<th field="award_caption">奖项级别</th>
    				<th field="award_score" align="center">奖项分数</th>
    				<th field="stu_count" align="center" formatter="stuCount">获奖人数</th>
    				<?php if($permValue & PERM_WRITE):?>
    				<th field="manage" formatter="manage">奖项管理</th>
    				<?php endif?>
    			</tr>
    		</thead>
    		</table>
    	</div>
    </body>
</html>