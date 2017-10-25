<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
        <script type="text/javascript">
        
        
        </script>
    </head>
    <body class="easyui-layout" fit="true" border="false">
    	<div id="gridToolbar">
    		&nbsp;&nbsp;选择志愿校：<input type="text" name="schoolNames" id="schoolNames" size="20" /><input type="hidden" name="schoolIds" id="schoolIds" />
    		<a class="easyui-linkbutton" iconCls="icon-search" href="javascript:void(0)">查询</a>
    		<a class="easyui-linkbutton" iconCls="icon-tip" href="javascript:void(0)">导出</a>
    	</div>
    	<table class="easyui-datagrid" fit="true" border="false" toolbar="#gridToolbar" rownumbers="true">
    		<thead>
    			<tr>
    				<th>学员姓名</th><th>学员编码</th><th>高思学号</th><th>学籍号</th><th>学籍所在区</th><th>户籍所在区</th><th>联系电话一</th><th>联系电话二</th><th>一志愿</th><th>二志愿</th><th>三志愿</th><th>学员自述</th><th>简历下载</th>
    			</tr>
    		</thead>
    	</table>
    </body>
</html>