<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
        <script type="text/javascript">
        var curEGroup = '';
        var curYGroup = '';
        var groupCaption = '';
        var scoreTimes = {};
        function loadGroup() {
            jQuery('#groupGrid').datagrid({
                queryParams:{groupType:jQuery('#group_type').val(), sort:'group_id', order:'desc'},
                onSelect:function(idx,data) {
                    curEGroup = data.group_id;
                    getExamScoreTime(curEGroup);
                    loadYuyueGroup();
                }
            })
        }
        
        function getExamScoreTime(curEGroup) {
        	jQuery.post('<?php echo $scoreTimeUrl?>', {egid:curEGroup}, function(data){
        		scoreTimes = data;
        	}, 'json');
        }
        
        function loadYuyueGroup() {
        	if(curEGroup) {
        		jQuery('#yGroupGrid').datagrid({
        			url:'<?php echo $jsonYyGroupUrl?>',
        			queryParams:{gid:curEGroup},
        			onSelect:function(idx,data){
        				curYGroup = data.ygroup_id;
        				groupCaption = data.ygroup_caption;
        				loadStudent();
        			}
        		});
        	}
        }
        
        function loadStudent(keyword) {
        	if(curYGroup){
        		var searchArgs = {ygid:curYGroup};
        		jQuery('#ySearchPos,#ySearchDate').html('<option value="0">不限</option>');
        		jQuery.post('<?php echo $initFormUrl?>', {ygid:curYGroup}, function(data){
        			jQuery.each(data.posList, function(k,pos){
        				jQuery('<option value="' + pos.pos_id + '">' + pos.pos_caption + '</option>').appendTo('#ySearchPos');
        			})
        			jQuery.each(data.dateList, function(k,dt){
        				jQuery('<option value="' + dt.date + '">' + dt.dateText + '</option>').appendTo('#ySearchDate');
        			})
        		}, 'json');
	        	jQuery('#stuGrid').datagrid({
	        		url:'<?php echo $jsonStuUrl?>',
	        		queryParams:searchArgs
	        	})
        	} else {
        		alert('请选择预约组');
        	}
        }
        
        function addYuyueStudent() {
        	if(curEGroup) {
        		var _tm = (new Date()).getTime();
        		jQuery('<div id="dlg_' + _tm + '"></div>').appendTo('body');
        		jQuery('#dlg_' + _tm).dialog({
        			title:'&nbsp;电话预约',
        			href:'<?php echo $addStudentUrl?>/egid/' + curEGroup + '/dlg/dlg_' + _tm,
        			iconCls:'icon-add',
        			modal:true,
        			width:700,
        			height:450,
        			onClose:function(){
        				jQuery('#dlg_' + _tm).dialog('destroy');
        			}
        		})
        	} else {
        		alert('请选择预约组');
        	}
        }
        
        function score(val, data) {
        	key = 'exam_' + data.exam_id;
        	if(scoreTimes[key]) {
        		if(data.time_now >scoreTimes[key]['score_at']) {
        			var str = '';
        			jQuery.each(scoreTimes[key]['subjects'], function(k,v){
        				str+= '<a href="javascript:void(0)" onclick="viewScore(' + data.exam_id + ',  \'' + data.scode + '\', \'' + k + '\')">' + v + '</a>&nbsp;';
        			})
        			return str;
        		}
        	}
        	return '未开始';
        }
        
        function searchYStudent(isPrint) {
        	var searchArgs = {ygid:curYGroup}
        	if(jQuery('#ySearchPos').val() != '0') {
        		searchArgs['yuyue_pos'] = jQuery('#ySearchPos').val();
        	}
        	if(isPrint) {
        		searchArgs['print'] = 1;
        	}
        	if(jQuery('#ySearchDate').val() != '0') {
        		searchArgs['yuyue_date'] = jQuery('#ySearchDate').val();
        	}
        	if(jQuery('#yTimeStart').datetimebox('getValue')) {
        		searchArgs['ytime_start'] = jQuery('#yTimeStart').datetimebox('getValue');
        	}
        	if(jQuery('#yTimeEnd').datetimebox('getValue')) {
        		searchArgs['ytime_end'] = jQuery('#yTimeEnd').datetimebox('getValue');
        	}
        	if(jQuery('#yStuKeyword').val()) {
        		searchArgs['keyword'] = jQuery('#yStuKeyword').val();
        	}
        	jQuery('#stuGrid').datagrid({
        		url:'<?php echo $jsonStuUrl?>',
	        	queryParams:searchArgs,
	        	onLoadSuccess:function(data){
	        		var rows = data.rows;
	        		if(rows.length >0) {
	        			groupId = rows[0].print_group;
                        if(groupId) {
	        			    startPrint(groupId)
                        }
	        		}
	        	}
        	})
        }
        
        function startPrint(groupId) {
        	jQuery('#printFrame').attr('src', "<?php echo $printUrl?>/gid/" +groupId);
			jQuery('#printLayer').show();
        }

		function finishPrint() {
			jQuery('#printLayer').hide();
		}
        
        function viewScore(examId, stuCode, subject) {
        	window.open('<?php echo $reportUrl?>/exam/' + examId + '/stuCode/' + stuCode + '/subject/' + subject);
        }
        
        jQuery(function(){
        	loadGroup();
        })
        </script>
    </head>
    <body class="easyui-layout" fit="true">
		<div id="printLayer" style="background:#fff;border:1px solid #ccc;width:400px;height:200px;position:absolute;top:20%;left:20%;z-index:10;display:none">
		<iframe id="printFrame" style="padding:0px;border:1px solid #ccc;width:400px;height:200px;margin-bottom:-7px" frameborder="no" scrolling="no"></iframe>
		</div>
    	<div region="west" style="width:450px">
    		<div class="easyui-layout" fit="true">
    			<div region="north" style="height:200px" title="选择竞赛组" iconCls="icon-redo">
	    			<div id="groupToolbar">
	    			&nbsp;选择竞赛组：<?php echo W('ArraySelect', array('options'=>$gTypeArray, 'attr'=>'id="group_type" onchange="loadGroup()"'))?>
	    			</div>
    				<table id="groupGrid"  singleSelect="true" url="<?php echo $jsonGroupUrl?>" fit="true" rownumbers="true" toolbar="#groupToolbar">
		                <thead>
		                    <tr>
		                    	<th field="group_type">竞赛类别</th>
		                        <th field="group_caption" width="200">竞赛组名称</th>
		                    </tr>
		                </thead>
		            </table>
    			</div>
    			<div region="center" title="选择预约组" iconCls="icon-redo">
    				<table id="yGroupGrid" class="easyui-datagrid" fit="true" border="false" rownumbers="true" singleselect="true" border="false">
    					<thead>
    						<tr>
    							<th field="ygroup_caption">预约组名称</th>
    							<th field="ygroup_time_start">开始时间</th>
    							<th field="ygroup_time_end">结束时间</th>
    						</tr>
    					</thead>
    				</table>
    			</div>
    		</div>
    	</div>
    	<div region="center">
    		<div id="stuToolbar">
    			<a href="javascript:void(0)" onclick="addYuyueStudent()" class="easyui-linkbutton" iconCls="icon-add" plain="true">电话预约</a>
    			<span class="datagrid-btn-separator"></span>
    			诊断地点：<select id="ySearchPos" name="yuyue_pos">
    				<option value="0">不限</option>
    				<?php foreach ($posArray as $posId=>$posCaption):?>
    				<option value="<?php echo $posId?>"><?php echo $posCaption?></option>
    				<?php endforeach;?>
    			</select>诊断日期<select id="ySearchDate" name="yuyue_date"><option value="0">不限</option></select>
    			关键词:<input type="text" size="30" style="padding:3px" id="yStuKeyword" placeholder="姓名/电话/学号/编码/准考证号" />
    			<br />&nbsp;&nbsp;&nbsp;&nbsp;预约时间介于：<input type="text" name="yTimeStart" id="yTimeStart" class="easyui-datetimebox" />-<input name="yTimeEnd" id="yTimeEnd" type="text" class="easyui-datetimebox" />&nbsp;<a id="searchBtn" href="javascript:void(0)" onclick="searchYStudent()" class="easyui-linkbutton" iconCls="icon-search" plain="true">查询</a><a plain="true" href="javascript:void(0)" onclick="searchYStudent(true)" class="easyui-linkbutton" iconCls="icon-print" id="printBtn">打印</a>
    		</div>
    		<table id="stuGrid" class="easyui-datagrid" title="预约考生管理" iconCls="icon-redo" rownumbers="true" singleselect="true" pagination="true" fit="true" border="false" toolbar="#stuToolbar">
    			<thead>
    				<tr>
    					<th field="exam_caption">竞赛</th>
    					<th field="sname">考生姓名</th>
    					<th field="scode">学生编码</th>
    					<th field="saliascode">考生学号</th>
                        <th field="sparents1phone">联系电话</th>
    					<th field="exam_code">准考证号</th>
    					<th field="pos_caption">预约地点</th>
    					<th field="date">诊断日期</th>
    					<th field="time_text">诊断时间</th>
    					<th field="yuyue_time">预约时间</th>
    					<th field="score" formatter="score">成绩查询</th>
    				</tr>
    			</thead>
    		</table>
    	</div>
    </body>
</html>