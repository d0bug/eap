<?php if(false == $resultScript):?>
<script type="text/javascript">
var keEditors = {};
var bdMaps = {};
jQuery(function(){
    jQuery('#posInfoForm').find('input,select').attr('disabled',true);
    var tm_<?php echo $_time?> = setInterval(function(){
        if(keEditors.pos_bus) {
           keEditors.pos_bus.readonly(true);
           clearInterval(tm_<?php echo $_time?>);
        }
    }, 100)
    jQuery('#enableBtn').click(function(){
        jQuery('#posInfoForm').find('input,select').attr('disabled',false);
        jQuery('#submitBtn').show();
        enableMark = true;
        keEditors.pos_bus.readonly(false);
        jQuery(this).hide();
    })
})
</script>
<style type="text/css">
    #posInfoForm em{color:red}
    #posInfoForm .item{line-height:15px;padding-top:1px;margin-left:10px}
    #posInfoForm span{font-size:15px;font-weight:bold}
    #posInfoForm input{width:260px;height:18px;}
    #posInfoForm textarea{height:60px;width:95%}
    #posInfoForm .file{height:22px;width:240px;}
    #posInfoForm .appIcon{text-align:center;width:22px;height:22px;float:left;border:1px solid #ddd;line-height:22px;overflow:hidden;margin-left:0px;margin-right:2px;padding:0px}
    #posMap{margin-left:10px;margin-top:3px}
</style>
<div class="easyui-layout" fit="true" border="false">
    <div region="center">
        <form id="posInfoForm" method="POST" action="<?php echo $url?>" target="hdPosFrame">
        <input type="hidden" name="pos_id" value="<?php echo $posInfo['pos_id']?>" />
        <div class="item"><label><span>考点标识：</span>英文标识<br />
            <input type="text" name="pos_code" readonly="true" style="ime-mode:disabled" value="<?php echo $posInfo['pos_code']?>" id="pos_code" />
        </label></div>
        <div class="item"><label><span>考点名称：</span>考点中文名<br />
            <input type="text" name="pos_caption" value="<?php echo $posInfo['pos_caption']?>" id="pos_caption" />
        </label></div>
        <div class="item"><label><span>所在区：</span><br />
            <?php echo W('ArraySelect', array('options'=>$areaArray, 'value'=>$posInfo['pos_area'], 'attr'=>'name="pos_area" id="pos_area"'))?>
        </label></div>
        <div class="item"><label><span>考点地址：</span><br />
            <input type="text" name="pos_addr" value="<?php echo $posInfo['pos_addr']?>" id="pos_addr" />
        </label></div>
        <div class="item"><label><span>联系电话：</span><br />
            <input type="text" name="pos_telephone" value="<?php echo $posInfo['pos_telephone']?>" id="pos_telephone" />
        </label></div>
        <div class="item"><label><span>周边教学点：</span><br /></label>
        <?php echo W('AreaSelect', array('areas'=>$posInfo['pos_areas'], 'id'=>'pos_areas', 'name'=>'pos_areas[]', 'multi'=>true, 'showCode'=>true, 'size'=>8))?>
        </div>
        <div class="item"><label><span>乘车路线：</span><br />
            <textarea name="pos_bus" id="pos_bus" style="width:95%;height:60px"><?php echo $posInfo['pos_bus']?></textarea>
            <?php echo W('Editor', array('id'=>'pos_bus', 'layout'=>'simple'))?>
        </label></div>
        <div class="item"><label><span>定位考点：</span><br />
            <input type="text" id="pos_map_position" name="pos_map_position" value="<?php echo $posInfo['pos_map_position']?>" readonly="true" />
        </label></div>   
        <div id="posMap" style="width:95%;height:240px"></div>
        <?php echo W('BdMap', array('addListener'=>true, 'enableMark'=>false, 'div'=>'posMap', 'posField'=>'pos_map_position', 'position'=>$posInfo['pos_map_position']))?>
        </form>
    </div>
    <?php if($permValue & $PERM_WRITE):?>
    <div region="south" style="height:30px">
        <div style="margin-top:3px;padding-left:10px">
            <a id="enableBtn" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit">启用编辑</a>
            <a style="display:none" id="submitBtn" href="javascript:savePosition('posInfoForm')" class="easyui-linkbutton" iconCls="icon-save">修改考点信息</a>
        </div>
    </div>
    <?php endif?>
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