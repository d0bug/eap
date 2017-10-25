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
                    loadPositions(curExamId);
                }
            })
        }

		function loadPositions() {
			jQuery('#posGrid').datagrid({
				url:'<?php echo $jsonPosUrl?>',
				queryParams:{exam:curExamId}
			})
		}

		function printMenu(val, data) {
			if(data.is_print == 1 && Math.abs(data.cur_count) < Math.abs(data.score_cnt)) {
				return '已打印<b>' + data.cur_count + '</b>份';
			} else if (Math.abs(data.cur_count) >= Math.abs(data.score_cnt)) {
				return '<a href="<?php echo $downPrintUrl?>?exam=' + data.exam_id + '&pos=' + data.pos_code + '">下载诊断报告</a>';
			} else if(1 != data.is_print){
				return '<a href="javascript:void(0)" onclick="addAreaPrint(' + data.exam_id + ', \'' + data.pos_code + '\', ' + data.score_cnt + ')">添加打印任务</a>';
			}
		}

		function addAreaPrint(examId, posCode, scoreCnt) {
			jQuery.post('<?php echo $addPrintUrl?>', {examId:examId, posCode:posCode, cnt:scoreCnt}, function(data){
				if(data.errorMsg) {
					alert(data.errorMsg);
				} else {
					jQuery('#posGrid').datagrid('reload');
				}
			}, 'json')
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
        	<table id="posGrid" class="easyui-datagrid" fit="true" border="false" singleselect="true" rownumbers="true">
	        	<thead>
		        	<tr>
		        		<th field="pos_caption">考点名称</th>
		        		<th field="pos_code" align="center">考点编码</th>
		        		<th field="score_cnt" align="center">报告总数</th>
						<th field="print_menu" formatter="printMenu">执行操作</th>
		        	</tr>
	        	</thead>
        	</table>
        </div>
    </body>
</html>