<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
        <script type="text/javascript">
        var isGroup = false;
        function loadGroups() {
        	jQuery('#groupGrid').datagrid({
        		onSelect:function(idx, data){
        			isGroup = true;
        			jQuery('#stuGrid').datagrid({
        				url:'<?php echo $groupStuUrl?>',
        				queryParams:{groupId:data.group_id}
        			})
        		}/*
        		view:detailview,
        		detailFormatter:function(index,data){
                    return '<div class="group_desc group_desc_' + index + '">' + jQuery.trim(data.group_desc) + '</div>';
                },
                onExpandRow:function(index,data){
                    jQuery('#groupGrid').datagrid('selectRow',index);
                    $('#groupGrid').datagrid('fixDetailRowHeight',index); 
                    var rowCnt = jQuery('div.group_desc').length;
                    for(var i=0;i<rowCnt;i++) {
                        if(i != index) {
                            jQuery('#groupGrid').datagrid('collapseRow',i);
                        }
                    }
                }
                */
        	})
        }
        
        function seleSubjects() {
        	var _tm = (new Date()).getTime();
        	jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
        	jQuery('#dlg_' + _tm).dialog({
        		title:'选择所属学科',
        		iconCls:'icon-redo',
        		href:'<?php echo $dlgSubjectUrl?>/form/searchForm/nameId/sbjNames/valId/sbjCodes/dlg/dlg_' + _tm,
        		width:600,
        		height:400,
        		modal:true,
        		onClose:function(){
        			jQuery('#dlg_' + _tm).dialog('destroy');
        		}
        	})
        }
        
        function seleProject() {
        	var _tm = (new Date()).getTime();
        	jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
        	jQuery('#dlg_' + _tm).dialog({
        		title:'选择教学项目',
        		iconCls:'icon-redo',
        		href:'<?php echo $dlgProjectUrl?>/form/searchForm/valId/prjCodes/dlg/dlg_' + _tm,
        		width:600,
        		height:400,
        		modal:true,
        		onClose:function(){
        			jQuery('#dlg_' + _tm).dialog('destroy');
        		}
        	})
        }
        
        function seleClassType() {
        	var _tm = (new Date()).getTime();
        	jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
        	jQuery('#dlg_' + _tm).dialog({
        		title:'选择班级类型',
        		iconCls:'icon-redo',
        		href:'<?php echo $dlgClassTypeUrl?>/form/searchForm/valId/ctCodes/dlg/dlg_' + _tm,
        		width:600,
        		height:400,
        		modal:true,
        		onClose:function(){
        			jQuery('#dlg_' + _tm).dialog('destroy');
        		}
        	})
        }
        
        function searchStudent() {
        	isGroup = false;
        	jQuery('#stuGrid').datagrid({
        		url:'<?php echo $jsonStuUrl?>',
        		queryParams:{formData:jQuery('#searchForm').serialize()},
        	})
        }
        
        <?php if($groupPerm & PERM_WRITE):?>
        function addGroup(type) {
    		var _tm = (new Date()).getTime();
    		var typeCaptions = {'dynamic':'动态组', 'static':'静态组'};
    		var typeCaption = typeCaptions[type];
        	jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
        	jQuery('#dlg_' + _tm).dialog({
        		title:'添加学员筛选组(' + typeCaption + ')',
        		iconCls:'icon-add',
        		href:'<?php echo $addGroupUrl?>/gType/' + type + '/dlg/dlg_' + _tm,
        		width:600,
        		height:400,
        		modal:true,
        		onClose:function(){
        			jQuery('#dlg_' + _tm).dialog('destroy');
        		}
        	})
        }
        
        function saveAsGroup() {
        	if(isGroup){
        		alert('当前列表为筛选组结果,不允许添加');
        		return;
        	} else {
        		
        	}
        }
        
        function delGroup() {
        	var seleGroup = jQuery('#groupGrid').datagrid('getSelected');
        	if(!seleGroup) {
        		alert('请选择要删除的筛选组');
        	} else {
        		jQuery.post('<?php echo $delGroupUrl?>', {gid:seleGroup.group_id}, function(data){
        			alert('筛选组删除成功');
        			jQuery('#groupGrid').datagrid('reload');
        		}, 'json');
        	}
        }
        <?php endif?>
        
        jQuery(function(){
        	loadGroups();
        	jQuery('#sbjNames').click(function(){
        		seleSubjects();
        	})
        	jQuery('#prjCodes').click(function(){
        		jQuery(this).find('option').selected = true;
        		seleProject();
        		return false;
        	})
        	jQuery('#ctCodes').click(function(){
        		jQuery('#prjCodes').find('option').selected= true;
        		jQuery(this).find('option').selected = true;
        		seleClassType();
        		return false;
        	})
        })
        </script>
        <style type="text/css">
        ul{margin:0px 0px -5px 10px;padding:3px}
        li{float:left;width:350px;overflow:hidden;line-height:30px}
        .bigLabel{display:block;float:left;line-height:30px}
        #sbjNames{margin-left:-5px;width:300px}
        </style>
    </head>
    <body class="easyui-layout" fit="true">
    	<div region="west" style="width:450px" border="false">
    		<?php if($groupPerm & $PERM_WRITE):?>
    		<div id="groupMenu">
    			<div iconCls="icon-redo" href="javascript:addGroup('dynamic')">动态组（SQL）</div>
    			<div iconCls="icon-redo" href="javascript:addGroup('static')">静态组</div>
    		</div>
    		<div id="groupToolbar">
    			<a href="javascript:void(0)" class="easyui-menubutton" menu="#groupMenu" iconCls="icon-add" plain="true">添加筛选组</a>
    			<a href="javascript:void(0)" class="easyui-linkbutton" onclick="delGroup()" iconCls="icon-cancel" plain="true">删除选定筛选组</a>
    		</div>
    		<?php endif?>
    		<table id="groupGrid" url="<?php echo $jsonGroupUrl?>" pagination="true" rownumbers="true" singleselect="true" fit="true" title="学员筛选组列表" iconCls="icon-redo" toolbar="#groupToolbar">
    			<thead>
    				<tr>
    					<th field="group_title">筛选组名称</th>
    					<th field="create_user">创建人</th>
    					<th field="create_at">创建时间</th>
    				</tr>
    			</thead>
    		</table>
    	</div>
    	<div region="center" iconCls="icon-redo">
    		<div class="easyui-layout" fit="true">
    			<div region="north" style="height:110px" title="" iconCls="icon-add" border="false">
    			<form style="margin:0px;padding:0px" id="searchForm">
    			<ul style="float:left">
    				<li><b class="smallLabel">学年：</b><select id="year" name="year">
    				<?php foreach ($yearArray as $year):?>
    				<option value="<?php echo $year?>"<?php if($year == $curYear):?> selected="true"<?php endif?>><?php echo $year?></option>
    				<?php endforeach;?>
    				</select>
    				<b>学期：</b><?php foreach ($semesterArray as $sm=>$smt):?>
    					<label><input type="checkbox" name="semester[]" value="<?php echo $sm?>" /><?php echo $smt?></label>
    					<?php endforeach;?>
    				</li>
    				<li><b>科目：</b><input type="hidden" name="sbjCodes" id="sbjCodes" />
    						 <input type="text" id="sbjNames" class="easyui-validatebox" valueId="sbjCodes" size="30" style="padding:4px 2px">
    				</li>
    				<li><b class="bigLabel">项目：</b><!--input type="hidden" name="prjCodes" id="prjCodes" />
    						 <textarea id="prjNames" readonly="true" id="prjNames" valueId="prjCodes" ></textarea-->
    						<select name="prjCodes[]" id="prjCodes" multiple="true" size="4" style="width:308px"></select>
    				</li>
    				<li><b class="bigLabel">班型：</b><!--input type="hidden" name="ctCodes" id="ctCodes" />
    						 <textarea id="ctNames" readonly="true" id="ctNames"></textarea-->
    						 <select name="ctCodes[]" id="ctCodes" multiple="true" size="4" style="width:308px"></select>
    				</li>
    			</ul>
    			</form>
    			</div>
    			<div region="center" border="false">
    				<div id="stuToolbar" style="border-top:2px solid #ccc;border-bottom:2px solid #ccc">
    					<a href="javascript:void(0)" onclick="searchStudent()" class="easyui-linkbutton" iconCls="icon-search" plain="true">提交查询</a>
    					<?php if($groupPerm & PERM_WRITE):?>
    					<a href="javascript:void(0)" onclick="saveAsGroup()" class="easyui-linkbutton" iconCls="icon-add" plain="true">添加为筛选组</a>
    					<?php endif?>
    				</div>
    				<table id="stuGrid" class="easyui-datagrid" pagination="true" rownumbers="true" singleselect="true" fit="true" iconCls="icon-redo" border="false" toolbar="#stuToolbar" pageList="[20,50,100]">
    				<thead>
    					<tr>
    						<th field="sname">姓名</th>
    						<th field="scode">编码</th>
    						<th field="saliascode">学号</th>
    						<th field="sgender">性别</th>
    						<th field="sbirthday">生日</th>
    						<th field="sgrade">年级</th>
    						<th field="school_name">所在学校</th>
    					</tr>
    				</thead>
    				</table>
    			</div>
    		</div>
    	</div>
    </body>
</html>