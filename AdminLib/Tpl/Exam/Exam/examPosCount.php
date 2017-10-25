<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
        <script type="text/javascript">
        var curGroup = '';
        function loadGroup() {
            jQuery('#groupGrid').datagrid({
                queryParams:{groupType:jQuery('#group_type').val(), sort:'group_id', order:'desc'},
                onSelect:function(idx,data) {
                    curGroup = data.group_id;
                    loadPosCount();
                }
            })
        }
        function loadPosCount() {
        	if(curGroup > 0) {
        		jQuery.post('<?php echo $examUrl?>', {groupId:curGroup,status:1} ,function(data){
        			var exams = data.rows;
        			var columns = [{field:'pos_caption', title:'考点名称', rowspan:2}];
        			var numCols = [];
        			jQuery.each(exams, function(k,ex){
        				columns.push({ title:ex.exam_caption, colspan:3, align:'center'});
        				numCols.push({field:'exam_' + ex.exam_id, title:'总额'})
        				numCols.push({field:'exam_' + ex.exam_id + '_signup', title:'已报', formatter:function(val){return '<span style="color:green">' + val + '</span>'}})
        				numCols.push({field:'exam_' + ex.exam_id + '_left', title:'剩余', formatter:function(val){return '<span style="color:red">' + val + '</span>'}})
        			})
        			jQuery('#countGrid').datagrid({
	        			url:'<?php echo $jsonPosCountUrl?>',
	        			queryParams:{groupId:curGroup},
	        			columns:[columns,numCols]
	        		})
        		}, 'json');
        	} else {
        		alert('请选择竞赛组');
        	}
        }
        function exportExcel() {
        	if(curGroup > 0) {
        		window.open('<?php echo $exportUrl?>?groupId=' + curGroup);
        	} else {
        		alert('请选择竞赛组');
        	}
        }
        jQuery(function(){
            loadGroup();
        })
        </script>
    </head>
    <body class="easyui-layout" fit="true">
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
    	<div id="gridToolbar">
    		<a href="javascript:void(0)" onclick="exportExcel();" class="easyui-linkbutton" iconCls="icon-print" plain="true">导出Excel</a>
    	</div>
    	<table id="countGrid" singleselect="true" class="easyui-datagrid" fit="true" rownumbers="true" toolbar="#gridToolbar">
    		<thead>
    			<tr><th field="pos_caption" width="200">考点</th></tr>
    		</thead>
    	</table>
    	</div>
    </body>
</html>