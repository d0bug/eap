<?php if(false == $resultScript):?>
<style type="text/css">
    #knowledgeForm em{color:red}
    #knowledgeForm div{line-height:18px;padding-top:6px;margin-left:10px}
    #knowledgeForm span{font-size:15px;font-weight:bold}
    #knowledgeForm input{width:260px;height:22px;}
</style>
<form id="knowledgeForm" method="POST" target="hdKnowledgeFrame" action="<?php echo $url?>">
<input type="hidden" name="module_code" value="<?php echo $moduleInfo['module_code']?>" />
<div><label><span>所属学科：</span><br />
        <input type="text"  name="subject_caption" class="easyui-validatebox" readonly="true" value="<?php echo $moduleInfo['subject_caption']?>" />
    </label></div>
<div><label><span>所属模块：</span><br />
        <input type="text"  name="subject_caption" class="easyui-validatebox" readonly="true" value="<?php echo '[' . $moduleInfo['module_code'] . '] ' . $moduleInfo['module_caption']?>" />
    </label></div>
<div><label><span>上级知识点：</span></label><br />
    <?php echo W('KnowledgeTree', array('attr'=>'name="parent_code" id="parent_code" style="width:260px"', 'module'=>$moduleInfo['module_code']))?>
</div>
<div><label><span>知识点名称：</span>必填<br />
        <input type="text"  name="knowledge_caption" class="easyui-validatebox" required="true" value="" />
    </label></div>
<div><label><span>索引编码：</span>必填<br />
        <input type="text"  name="knowledge_code" class="easyui-validatebox" style="ime-mode:disabled" required="true" value="" />
    </label></div>
<div style="display:none"><label><span>知识体系编码：</span>知识体系唯一编码，用于查询试题<br />
        <input type="text"  name="study_code" class="easyui-validatebox" style="ime-mode:disabled" value="" />
    </label></div>
<div><a href="javascript:void(0)" onclick="jQuery('#knowledgeForm').submit()" class="easyui-linkbutton" iconCls="icon-save" >保存知识点</a></div>
</form>
<iframe name="hdKnowledgeFrame" id="hdKnowledgeFrame" style="display:none"></iframe>
<?php else:?>
<script type="text/javascript">
<?php if($errorMsg):?>
    alert('<?php echo $errorMsg?>');
<?php else:?>
    alert('知识点添加成功');
    parent.reloadKnowledge('<?php echo $knowledgeCode?>');
<?php endif?>
</script>
<?php endif?>