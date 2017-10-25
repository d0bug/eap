<div class="easyui-layout" fit="true" border="false">
    <div region="center">
        <table class="easyui-treegrid" id="pQuesGrid_<?php echo $partIdx?>" singleSelect="false" rownumbers="true" fit="true" border="false">
            <thead frozen="true">
                <tr><th field="ques_id" checkbox="true">ID</th><th field="ques_sumary">摘要</th></tr>
            </thead>
            <thead>
                <tr>
                    <th field="quesType_caption">题型</th>
                    <th field="ques_level" align="center">难度</th>
                </tr>
            </thead>
        </table>
    </div>
    <div region="south" style="height:30px">
        <div style="text-align:center;padding-top:3px">
        <a href="javascript:addPartQues(<?php echo $partIdx?>)" class="easyui-linkbutton" iconCls="icon-ok">确定</a>
        &nbsp;&nbsp;<a href="javascript:closeDlg(<?php echo $partIdx?>,false)" class="easyui-linkbutton" iconCls="icon-cancel">取消</a>
        </div>
    </div>
</div>
<script type="text/javascript">
    
</script>