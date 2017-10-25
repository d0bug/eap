<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php' ?>
        <?php include TPL_INCLUDE_PATH . '/easyuiMove.php' ?>
        <?php include TPL_INCLUDE_PATH . '/juicer.php' ?>
        
        <script type="text/javascript">
        	var curGroup = null;
        	var curExam = 0;
        	var examCaption = '';
        	var papers = [];
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
                	loadVirtual(data.exam_id)
                }
            })
        }
        
        function loadVirtual(examId) {
        	curExam = examId;
        	papers = [];
        	jQuery('#virtualMenu').remove();
        	jQuery.post('<?php echo $jsonExamInfo?>', {exam_id:examId}, function(data){
        		examCaption = data.examCaption;
        		var columns = [
        						{field:'exam_caption',title:'竞赛名称',formatter:function(){return examCaption}},
        					  ];
        		if(data.papers) {
        			papers = data.papers;
        			var tpl = jQuery('#vMenu-tpl').html();
        			var html = juicer(tpl, {papers:papers});
        			jQuery('<div id="virtualMenu">' + html + '</div>').appendTo('body');
        		}
        		
        		jQuery('#virtualGrid').datagrid({
        			url:'<?php echo $jsonVirtualUrl?>/exam/' + examId,
        		})
        		jQuery('#vMenuButton').menubutton({
    				menu:'#virtualMenu'
    			});
        	}, 'json');
        }
        
        function manage(val, data) {
        	return '<a href="javascript:addVirtual(\'' + data.virtual_type + '\', \'' + data.vtype_caption + '\', \'' + data.id + '\')">修改虚拟人数</a> | <a href="javascript:delVirtual(\'' + data.id + '\')">删除虚拟分数</a>';
        }
        
        function delVirtual(id) {
        	if(confirm('确定要删除选定虚拟分数吗？')) {
        		jQuery.post('<?php echo $delVirtualUrl?>', {id:id}, function(data){
        			alert('宣传数据删除成功');
        			jQuery('#virtualGrid').datagrid('reload');
        		}, 'json');
        	}
        }
        
        function addVirtual(virtualType, virtualCaption, vTypeId) {
        	if(vTypeId) {
        		var vid = '/id/' + vTypeId;
        		var opCaption = '修改';
        	} else {
        		var vid = '';
        		var opCaption = '添加';
        	}
        	if(curExam >0) {
        		if(papers.length == 0) {
        			alert('本考试暂未添加试卷');
        			return;
        		}
	        	var tm = (new Date()).getTime();
	        	jQuery('<div id="dlg_' + tm + '"></div>').appendTo('body');
	        	jQuery('#dlg_' + tm).dialog({
	        		href:'<?php echo $addVirtualUrl?>/exam/' + curExam + '/type/' + virtualType + vid,
	        		width:500,
	        		height:200,
	        		modal:true,
	        		title:opCaption + virtualCaption + '宣传数据',
	        		iconCls:'icon-add',
	        		buttons:[{text:'保存', iconCls:'icon-save', handler:function(){
	        			saveVirtual(virtualType, 'dlg_' + tm);
	        		}},
	        		{text:'关闭', iconCls:'icon-cancel', handler:function(){
	        			jQuery('#dlg_' + tm).dialog('destroy');
	        		}}],
	        		onClose:function(){
	        			jQuery('#dlg_' + tm).dialog('destroy');
	        		}
	        	});
        	} else {
        		alert('请选择竞赛');
        	}
        }
        
        function saveVirtual(virtualType, dlgId) {
        	var formData = jQuery('#' + virtualType+ '_form').serialize();
        	jQuery.post('<?php echo $saveVirtualUrl?>', formData, function(data){
        		if(data.errorMsg) {
        			alert(data.errorMsg);
        		} else {
        			alert('宣传数据录入成功');
        			jQuery('#' + dlgId).dialog('destroy');
        			jQuery('#virtualGrid').datagrid('reload');
        		}
        	}, 'json');
        }
        
        function vTypeCaption(val, data){
        	return data.vtype_caption
        }
        
        jQuery(function(){
            loadGroup();
            loadExams();
            loadVirtual(0);
        })
		</script>
	</head>
	<body class="easyui-layout" fit="true" border="false" split="false">
    	<div region="west" style="width:270px" iconCls="icon-redo" title="选择竞赛">
    		<div class="easyui-layout" fit="true" border="false">
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
    		<script type="text/template" id="vMenu-tpl">
    		{@each papers as paper,key}
    			<div iconCls="icon-redo" onclick="javascript:addVirtual('${paper.type}', '${paper.title}')">${paper.title}宣传数据</div>
    		{@/each}
    		</script>
    		<div id="virtualToolbar">
    			<a href="javascript:addVirtual()" id="vMenuButton" class="easyui-menubutton" menu="#virtualMenu" iconCls="icon-add" plain="true">添加宣传数据</a>
    			<!--span class="datagrid-btn-separator"></span>
    			<a class="easyui-linkbutton" iconCls="icon-add" href="javascript:updateRank()" plain="true">更新排名</a-->
    		</div>
    		<table id="virtualGrid" fit="true" border="false" toolbar="#virtualToolbar" pagination="false" rownumbers="true" singleselect="true">
    			<thead>
    				<tr><th field="virtual_type" formatter="vTypeCaption">虚拟成绩类型</th><th field="score">成绩分值</th><th field="rank">排名</th><th field="score_cnt">人数</th><th field="update_user">操作员</th><th field="update_at">操作时间</th><th field="manage" formatter="manage">管理</th></tr>
    			</thead>
    		</table>
    	</div>
	</body>
</html>