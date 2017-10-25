<div class="easyui-layout" fit="true" border="false">
    <?php if(false == $paperId):?>
    <div region="north" style="height:28px">
        <div class="datagrid-toolbar">
            <a href="javascript:void(0)" onclick="getPartQuestions(<?php echo $partIdx?>)" class="easyui-linkbutton" iconCls="icon-redo" plain="true">选择试题</a>&nbsp;&nbsp;<span style="color:blue">【选定试题并保存试卷后通过修改试卷信息进行试题排序】</span>
        </div>
    </div>
    <?php endif?>
    <div region="center">
        <table class="easyui-treegrid" id="partQuestions_<?php echo $partIdx?>" fit="true" singleselect="true" border="false" rownumbers="true">
            <thead frozen="true">
                <tr>
                <th field="ques_sumary" <?php if($paperId):?>width="220"<?php endif?> formatter="quesSumary<?php echo $partIdx?>">摘要</th></tr>
            </thead>
            <thead>
                <tr>
                <th align="center" <?php if($paperId):?>width="60"<?php endif?> field="quesType_caption">题型</th>
                <th align="center" <?php if($paperId):?>width="60"<?php endif?> field="ques_num" formatter="quesNumber<?php echo $partIdx?>">题号</th>
                <th align="center" <?php if($paperId):?>width="60"<?php endif?> field="ques_score" formatter="quesScore<?php echo $partIdx?>">分值</th>
                <?php if(false == $paperId):?>
                    
                    <th align="center" field="ques_level" formatter="quesLevel<?php echo $partIdx?>">难度</th>
                    <th field="ques_knowledge" formatter="knowledge<?php echo $partIdx?>">知识点</th>
                    <th align="center" field="manage" formatter="manage<?php echo $partIdx?>">操作</th></tr>
                <?php endif?>
            </thead>
        </table>
    </div>
</div>
<script type="text/javascript">
jQuery(function(){
<?php if($paperId):?>
    var tm_<?php echo $partIdx?> = setInterval(function(){
        if(gridLoaded) {
            gridLoaded = false;
            jQuery('#partQuestions_<?php echo $partIdx?>').treegrid({
                url:"<?php echo $jsonQuesUrl?>",
                idField:'ques_id',
                treeField:'ques_sumary',
                onLoadSuccess:function(){
                    gridLoaded = true;
                    clearInterval(tm_<?php echo $partIdx?>);
                    computeTotal();
                }
            })
        }
    },200);
    
<?php endif?>
})
function quesSumary<?php echo $partIdx?>(val, data) {
    if(data.body_title) return val;
    return '<input type="hidden"  class="quesId" value="' + data.ques_id + '" />&nbsp;' + val;
}

function quesNumber<?php echo $partIdx?>(val, data) {
    if(data.body_title) return '';
    return '<input type="text" size="3" style="height:14px;text-align:center;color:blue" name="quesNumber[<?php echo $partIdx?>][' + data.ques_id + ']" value="' + jQuery.trim(data.ques_seq) + '" class="quesNumber" />';
}
function quesScore<?php echo $partIdx?>(val, data) {
    if(data.body_title) return '';
    return '<input type="text" size="4" <?php if($paperId):?>readonly="true" value="' + data.ques_score + '" <?php else:?>name="quesScore[<?php echo $partIdx?>][' + data.ques_id + ']"<?php endif?> class="quesScore quesScore<?php echo $partIdx?>" style="color:blue;text-align:center;ime-mode:disabled" onkeyup="this.value=this.value.replace(/\D/g,\'\');computeTotal()"  />'
}
<?php if(false == $paperId):?>

function quesLevel<?php echo $partIdx?>(val, data) {
    if(data.body_title) return '';
    var sele = '<select style="color:blue" name="quesLevel[<?php echo $partIdx?>][' + data.ques_id + ']">';
    for(var i=1;i<=5;i++) {
        sele += '<option value="' + i + '"';
        if(i == data.ques_level){
            sele += ' selected="true"';
        }
        sele += '>' + i + '级</option>';
    }
    sele += '</select>';
    return sele;
}

function knowledge<?php echo $partIdx?>(val, data) {
    if(data.body_title) return '';
    sele = '<select style="width:200px;color:blue" name="quesKnowledge[<?php echo $partIdx?>][' + data.ques_id + ']">';
    if(data.knowledgeArray) {
        jQuery.each(data.knowledgeArray, function(idx, knowledge){
            sele += '<option value="' + knowledge.knowledge_code + '"'
            if(knowledge.knowledge_code == data.knowledge_code){
                sele += ' selected="true"';
            }
            sele += '>[' + knowledge.module_caption + ']' + knowledge.knowledge_caption + '</option>';
        })
    }
    sele += '</select>';
    return sele;
}

function manage<?php echo $partIdx?>(v, data) {
    return '<a href="javascript:delQues(<?php echo $partIdx?>, \'' + data.ques_id + '\')">移除</a>';
}
<?php endif?>
</script>