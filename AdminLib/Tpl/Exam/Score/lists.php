<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php' ?>
        <?php include TPL_INCLUDE_PATH . '/juicer.php' ?>
        <?php include TPL_INCLUDE_PATH . '/json.php'?>
        <script type="text/javascript">
        var curGroup = '';
        var curExam = 0;
        var papers = [];
        var awards = [];
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
                	loadScores(data.exam_id)
                }
            })
        }
        
        
        function loadScores(examId) {
        	curExam = examId;
        	var columns = [/*{field:'pos_code',title:'考点编码',sortable:true},
			        	   {field:'school_name',title:'所在学校'}*/];
        	jQuery.post('<?php echo $jsonExamInfo?>', {exam_id:examId}, function(data){
        		if(data.papers){
					papers = data.papers;
        			jQuery('#exportMenu').remove();
        			var title = data.group_caption + '-' + data.exam_caption + '- 成绩列表';
	        		jQuery(data.papers).each(function(key,paperCfg){
	        			columns.push({field:paperCfg['field'], title:paperCfg['title'],sortable:true});
	        			columns.push({field:paperCfg['type'] + '_rank', title: '排名'});
	        			columns.push({field:paperCfg['type'] + '_vrank', title: '虚排名'});
	        			if(data.awards[paperCfg['type']]) {
	        				columns.push({field:data.awards[paperCfg['type']]['field'] + '_award', title:data.awards[paperCfg['type']]['title']});
	        			}
	        		})
	        		columns.push({field:'report', title:'成绩报告', formatter:function(val, data) {
			        	var subjects = {'math':'数学', 'chinese':'语文', 'english':'英语', 'physic':'物理', 'chemistry':'化学'};
			        	if(data.total_score >0) {
			        		var returnStr = '';
			        		jQuery.each(data, function(k,v){
			        			if(/_real_score$/.test(k) && data[k] > 0) {
			        				subject = k.replace(/_real_score$/, '');
			        				returnStr += '<a href="<?php echo $reportUrl?>/exam/' + data.exam_id + '/subject/' + subject + '/stuCode/' + data.stu_code + '" target="_blank">' + subjects[subject] + '</a>&nbsp;';
			        			}
			        		})
			        		return returnStr;
			        	} else {
			        		return '——';
			        	}
			        }});
			        columns.push({field:'scan', title:'试卷扫描',align:'center', formatter:function(val, data) {
			        	if(data.total_score >0 && data.scan_files) {
			        		return '<a href="<?php echo $scanUrl?>/exam/' + data.exam_id + '/examCode/' + data.exam_code + '" target="_blank">试卷扫描</a>';
			        	} else {
			        		return '——';
			        	}
			        }});
        		} else {
        			papers = [];
        			title="竞赛成绩列表"
        			jQuery('#exportMenu').html('');
        		}
        		awards = data.examAwards;
        		
        		initSearch();
        		jQuery('#scoreGrid').datagrid({
        			url:"<?php echo $jsonScoreGrid?>/exam/" + Math.abs(examId),
        			title:title,
	        		frozenColumns:[[
	        		{field:'sname',title:'考生姓名'},
	        		{field:'stu_code',title:'考生编码'},
	        		{field:'saliascode',title:'考生学号'},
	        		{field:'exam_code',title:'准考证号',sortable:true}
	        		]],
	        		columns:[columns]
	        	})
        	},'json');
        }
        
        function getSearchArgs() {
        	var searchType = jQuery('#search_type').val();
        	if(searchType == 'award') {
        		var keyword = jQuery('#award').val();
        	} else if(searchType == 'between') {
        		var keyword = jQuery('#paper_type').val() + '^' + Math.abs(jQuery('#min').val()) + '^' + Math.abs(jQuery('#max').val());
        	} else {
        		var keyword = jQuery('#keyword').val();
        	}
        	var args = {
        		exam:curExam,
        		type:searchType, 
        		keyword:keyword
        	}
        	return args;
        }
        
        function searchScore() {
        	var args = getSearchArgs();
        	jQuery('#scoreGrid').datagrid('reload', args)
        }
        
        function addGroup() {
        	if(curExam == 0) return;
        	var args = getSearchArgs();
        	var groupCaption = prompt('请输入筛选组名称');
        	if(groupCaption) {
        		args['groupCaption'] = groupCaption;
        		var options = jQuery('#scoreGrid').datagrid('options');
        		args['url'] = options.url
        		args['data'] = JSON.stringify(options.queryParams);
        		args['groupType'] = '竞赛筛选组';
	        	jQuery.post('<?php echo $addGroupUrl?>', args, function(data){
	        		if(data.errorMsg) {
	        			alert(data.errorMsg)
	        		} else {
	        			alert('筛选组添加成功');
	        		}
	        	}, 'json');
        	} else {
        		return;
        	}
        }
        
        function updateRank() {
        	if(curExam == 0) return;
        }
        
        function initSearch() {
        	var tpl = jQuery('#search-tpl').html();
        	var searchType = jQuery('#search_type').val();
        	var html = juicer(tpl, {searchType:searchType});
        	jQuery('#search_cfg').html(html);
        	if(searchType == 'award') {
        		if(awards) {
        			var oSele = jQuery('#award')[0];
	        		jQuery.each(awards, function(k,v){
	        			oSele.options.add(new Option(v.caption,v.id));
	        		})
        		}
        	} else if(searchType =='between') {
        		if(papers.length >0) {
	        		jQuery(papers).each(function(k,paper){
	        			jQuery('#paper_type').append('<option value="' + paper.field + '">' + paper.title + '</option>');
	        		})
        		} else {
        			jQuery('#paper_type').append('<option value="0">请选择</option>');
        		}
        	}
        }
        function exportScore(){
        	var args = getSearchArgs();
        	var formTpl = jQuery('#export-form-tpl').html();
        	var html = juicer(formTpl, {args:args});
        	jQuery('<form id="exportForm" target="hdFrame" method="post" action="<?php echo $exportUrl?>">' + html + '</form>').appendTo('body');
        	jQuery('#exportForm').submit();
        	jQuery('#exportForm').remove();
        }
        
    	jQuery(function(){
            loadGroup();
            loadExams();
            loadScores(0);
            initSearch();
            jQuery('#search_type').change(initSearch);
        })
        </script>
    </head>
    <body class="easyui-layout" fit="true" border="false" split="false">
    	<div region="west" style="width:270px" iconCls="icon-redo" title="选择竞赛">
    		<div class="easyui-layout" fit="true" border="false">
    			<div id="groupToolbar">
    			竞赛筛选：<?php echo W('ArraySelect', array('options'=>$gTypeArray, 'attr'=>'id="group_type" onchange="loadGroup()"'))?>
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
    		<div id="scoreToolbar" style="background:#efefef;line-height:22px">
    			<?php if($groupPerm & PERM_WRITE):?>
    			<a class="easyui-linkbutton" iconCls="icon-add" href="javascript:addGroup()" plain="true">添加为筛选组</a>
    			<span class="datagrid-btn-separator"></span>
    			<?php endif?>
    			<?php if($exportPerm & PERM_READ):?>
    			<a href="javascript:exportScore()" class="easyui-linkbutton" plain="true" id="exportBtn" iconCls="icon-save">导出报表</a>
    			<?php endif?>
    			<script type="text/template" id="menu-tpl">
    			{@each papers as paper}
    				<div href="javascript:void(0)" iconCls="icon-redo" exam_id="${exam_id}" paper_key="${paper.field}">${paper.title}成绩导出表</div>
    			{@/each}
    			</script>
    			<span class="datagrid-btn-separator"></span>
    			
    			&nbsp;
    			<b>考生查询:</b>
    			<select id="search_type">
    				<option value="stu_name">考生姓名</option>
    				<option value="stu_code">考生学号</option>
    				<option value="exam_code">准考证号</option>
    				<option value="award">获奖情况</option>
    				<option value="between">成绩区间</option>
    			</select> 
    			<span id="search_cfg">
    				
    			</span>
    			<script type="text/template" id="search-tpl">
    				{@if searchType == 'award'}
    					<select name="award" id="award"></select>
    				{@else if searchType == 'between'}
    					<select name="paper_type" id="paper_type"></select> <input type="text" size="3" name="min" id="min" />—<input type="text" size="3" name="max" id="max" />
    				{@else}
    					<input type="text" name="keyword" id="keyword" size="8" />
    				{@/if}
    			</script>
    			<a class="easyui-linkbutton" iconCls="icon-search" href="javascript:searchScore()" plain="true" title="aaaaa">查询</a>
    			<!--span style="font-size:10px;color:blue">成绩区间默认为大于等于且小于等于,如想排除等于,可在数值中添加小数位,如:<b style="color:red"> 95.1</b></span-->
    		</div>
    		<table id="scoreGrid" iconCls="icon-search" rownumbers="true" singleselect="true" title="竞赛成绩列表" fit="true" border="false" pagination="true" pageList="[20,30,50,100]" toolbar="#scoreToolbar">
    		</table>
    		<script type="text/template" id="export-form-tpl">
    		{@each args as val,key}
    		<input type="hidden" name="${key}" value="${val}" />
    		{@/each}
    		</script>
    		<iframe id="hdFrame" name="hdFrame" style="display:none"></iframe>
    	</div>
    </body>
</html>