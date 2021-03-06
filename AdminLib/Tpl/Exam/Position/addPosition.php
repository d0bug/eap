<?php if(false == $resultScript):?>
<script type="text/javascript">
var keEditors = {};
var bdMaps = {};
</script>
<style type="text/css">
    #posInfoForm em{color:red}
    #posInfoForm .item{line-height:15px;padding-top:1px;margin-left:10px}
    #posInfoForm span{font-size:15px;font-weight:bold}
    #posInfoForm input{width:260px;height:18px;}
    #posInfoForm textarea{height:60px;width:95%}
    #posMap{margin-left:10px;margin-top:3px}
</style>
<div class="easyui-layout" fit="true" border="false">
    <div region="center">
        <form id="posInfoForm" method="POST" action="<?php echo $url?>" target="hdPosFrame">
        <div class="item"><label><span>考点标识：</span>英文输入，不可重复<br />
            <input type="text" name="pos_code" style="ime-mode:disabled" value="" id="pos_code" />
        </label></div>
        <div class="item"><label><span>考点名称：</span>考点中文名，不可重复<br />
            <input type="text" name="pos_caption" value="" id="pos_caption" />
        </label></div>
        <div class="item"><label><span>所在区：</span><br />
            <?php echo W('ArraySelect', array('options'=>$areaArray, 'attr'=>'name="pos_area" id="pos_area"'))?>
        </label></div>
        <div class="item"><label><span>考点地址：</span><br />
            <input type="text" name="pos_addr" value="" id="pos_addr" />
        </label></div>
        <div class="item"><label><span>联系电话：</span><br />
            <input type="text" name="pos_telephone" value="" id="pos_telephone" />
        </label></div>
        <div class="item"><label><span>周边教学点：</span><br /></label>
        <?php echo W('AreaSelect', array('id'=>'pos_areas', 'name'=>'pos_areas[]', 'multi'=>true, 'showCode'=>true, 'size'=>8))?>
        </div>
        <div class="item"><label><span>乘车路线：</span><br />
            <textarea name="pos_bus" id="pos_bus" style="width:95%;height:60px"></textarea>
            <?php echo W('Editor', array('id'=>'pos_bus', 'layout'=>'simple'))?>
        </label></div>
        <div class="item"><label><span>定位考点：</span><br />
            <input type="text" id="pos_map_position" name="pos_map_position" readonly="true" />
        </label></div>   
        <div id="posMap" style="width:95%;height:240px"></div>
        <?php echo W('BdMap', array('addListener'=>true, 'enableMark'=>true, 'div'=>'posMap', 'posField'=>'pos_map_position'))?>
        </form>
    </div>
    <div region="south" style="height:30px">
        <div style="margin-top:3px;padding-left:10px">
            <a href="javascript:savePosition('posInfoForm')" class="easyui-linkbutton" iconCls="icon-save">添加考点信息</a>
        </div>
    </div>
</div>
<iframe id="hdPosFrame" name="hdPosFrame" style="display:none"></iframe>
<?php else:?>
<script type="text/javascript">
    <?php if($errorMsg):?>
    alert('<?php echo $errorMsg?>');
    <?php else:?>
    parent.reloadGrid();
    <?php endif?>
</script>
<?php endif?>