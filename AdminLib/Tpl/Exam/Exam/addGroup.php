<?php if(false == $resultScript):?>
<script type="text/javascript">
function saveGroup() {
    keEditors.group_intro.sync();
    jQuery('#groupInfoForm').submit();
}
</script>
<style type="text/css">
#groupInfoForm em{color:red}
#groupInfoForm .item{line-height:18px;padding-top:5px;margin-left:10px}
#groupInfoForm span{font-size:15px;font-weight:bold}
#groupInfoForm .input{width:260px;height:18px;}
.indent{margin-left:20px}
</style>
<div class="easyui-layout" fit="true">
    <div region="center">
        <form id="groupInfoForm" method="POST" target="hdGroupFrame" action="<?php echo $url?>">
            <div class="item"><label><span>竞赛组名称：</span>应包含届别，不可重复<br />
                <input class="input" type="text" name="group_caption" value="" id="group_caption" />
            </label></div>
            <div class="item"><label><span>竞赛大分类：</span></label>
            <?php echo W('ArraySelect', array('options'=>$gTypeArray, 'attr'=>'name="group_type" id="group_type"'))?>
            </div>
            <!--div class="item"><span>成绩录入：</span>
                <label><input type="radio" name="if_score" value="1" checked="true" />需要</label>
                <label><input type="radio" name="if_score" value="0"  />不需要</label>
            </div-->
            <div class="item"><span>竞赛组状态：</span>
                <select name="group_status" id="group_status">
                    <option value="0">隐藏</option>
                    <option value="1">显示</option>
                </select>
            </div>
            <div class="item"><span>专题页地址：</span><br />
                <input class="input" type="text" name="special_url" />
            </div>
            <div class="item"><span>竞赛整体介绍</span>
            <textarea id="group_intro" name="group_intro" style="width:95%;height:300px"></textarea>
            <?php echo W('Editor', array('id'=>'group_intro', 'layout'=>'simple'))?>
            </div>
        </form>
        <iframe id="hdGroupFrame" name="hdGroupFrame" style="display:none"></iframe>
    </div>
    <div region="south" style="height:30px">
        <div style="padding-top:2px;padding-left:10px"><a href="javascript:saveGroup()" class="easyui-linkbutton" iconCls="icon-add">添加竞赛组</a></div>
    </div>
</div>
<?php else:?>
<script type="text/javascript">
    <?php if($errorMsg):?>
    alert('<?php echo $errorMsg?>');
    <?php else:?>
    alert('竞赛组添加成功');
    parent.reloadGrid();
    <?php endif?>
</script>
<?php endif?>