<!doctype html>
<html>
<head>
    <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
    <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
    <script type="text/javascript" src="/static/kindeditor/kindeditor-min.js"></script>
    <script type="text/javascript">
    var keEditors = {};
    function saveExam() {
        for(var editor in keEditors){
            KindEditor.sync('#' + editor);
        }
        var formData = jQuery('input,select,textarea').serialize();
        jQuery.post('<?php echo $url?>', formData, function(data){
            if(data.errorMsg) {
                alert(data.errorMsg);
            } else {
                alert('竞赛信息修改成功');
                parent.closeWin('<?php echo $dlgId?>');
                parent.loadExams();
            }
        }, 'json');
    }
    jQuery(function(){
        jQuery('input,select,textarea').attr('disabled', true);
        <?php if($permValue & $PERM_WRITE):?>
        jQuery('#enableEdit').click(function(){
            jQuery('input,select,textarea').attr('disabled',false);
            jQuery(this).hide();
            jQuery('.datebox').datebox();
            jQuery('.timespinner').timespinner();
            jQuery('#saveButton').show();
        })
        <?php endif?>
    })
    </script>
    <style type="text/css">
    .item {line-height:25px;list-style-type:none;margin-bottom:2px;clear:both}
    .item div{float:left;width:120px;font-weight:bold}
    .item .input,.item textarea{height:19px;width:300px;float:left;margin-bottom:2px;border:1px solid #95B8E7;margin-top:2px}
    .item lable{clear:both}
    .item label .radio{widht:20px;clear:left}
    .item p{width:550px;float:left;margin:0px;padding:0px}
    input.datebox{width:153px;border:1px solid #95B8E7}
    input.timespinner{width:130px;border:1px solid #95B8E7}
    </style>
</head>
<body style="overflow:hidden;margin:0px -1px -1px 0px"  class="easyui-layout" fit="true" border="false">
    <div region="north" style="height:180px">
        <?php if($permValue & $PERM_WRITE):?>
        <div class="datagrid-toolbar">
            <a href="javascript:void(0)" id="enableEdit" class="easyui-linkbutton" iconCls="icon-edit" plain="true">启用编辑</a><a style="display:none" href="javascript:void(0)" onclick="saveExam()" id="saveButton" class="easyui-linkbutton" iconCls="icon-save" plain="true">保存竞赛信息</a>
            <input type="hidden" name="exam_id" value="<?php echo $examInfo['exam_id']?>" />
            <input type="hidden" name="group_id" value="<?php echo $examInfo['group_id']?>" />
            <input type="hidden" name="group_type" value="<?php echo $examInfo['group_type']?>" />
        </div>
        <?php endif?>
        <ul>
            <li class="item"><div>竞赛组别：</div><input class="input" type="text" readonly="true" name="group_caption" id="group_caption" value="<?php echo $examInfo['group_caption']?>" /></li>
            <li class="item"><div>竞赛名称：</div><input class="input easyui-validatebox" required="true" type="text" name="exam_caption" id="exam_caption" value="<?php echo $examInfo['exam_caption']?>" />(例如:高思杯(四年级组))</li>
            <li class="item"><div>竞赛状态：</div><select name="exam_status" id="exam_status">
                <option value="1" <?php if($examInfo['exam_status'] == 1):?> selected="true"<?php endif?>>显示</option>
                <option value="0" <?php if($examInfo['exam_status'] == 0):?> selected="true"<?php endif?>>隐藏</option>
            </select>(默认显示，通过竞赛组状态控制显示隐藏)</li>
            <li class="item"><div>年级选择：</div><p><?php echo W('Checkbox', array('name'=>'exam_grade[]', 'items'=>$gradeOptions, 'value'=>$examInfo['exam_grade']))?></p></li>    
        </ul>
    </div>
    <div region="center">
        <div class="easyui-tabs" fit="true" border="false">
            <div title="竞赛属性">
                <ul>
                    <li class="item"><div>考试起止时间：</div><input type="text" name="exam_time_area" class="input easyui-validatebox" required="true" value="<?php echo $examInfo['exam_time_area']?>" />（文字格式）</li>
                    
                    <li class="item"><div>考试开始时间：</div><input type="text" name="exam_time_start_date" class="datebox" required="true" value="<?php echo $examInfo['exam_time_start_date']?>" /> <input type="text" class="timespinner" name="exam_time_start_time"  value="<?php echo $examInfo['exam_time_start_time']?>" /> （时间格式）</li>
                    
                    <li class="item"><div>报名开始时间：</div><input type="text" name="exam_signup_start_date" class="datebox" required="true" value="<?php echo $examInfo['exam_signup_start_date']?>" /> <input type="text" class="timespinner" name="exam_signup_start_time"  value="<?php echo $examInfo['exam_signup_start_time']?>" /></li>
                    <li class="item"><div>报名停止时间：</div><input type="text" name="exam_signup_stop_date" class="datebox" required="true" value="<?php echo $examInfo['exam_signup_stop_date']?>" /> <input type="text" class="timespinner" name="exam_signup_stop_time"  value="<?php echo $examInfo['exam_signup_stop_time']?>" /></li>
                    <li class="item"><div>成绩查询时间：</div><input type="text" class="datebox" name="exam_score_date" value="<?php echo $examInfo['exam_score_date']?>" /> <input type="text" class="timespinner" name="exam_score_time" value="<?php echo $examInfo['exam_score_time']?>" /></li>
                    <li class="item"><div>是否显示综合排名：</div><?php echo W('Radio',array('name'=>'exam_show_rank', 'items'=>array('1'=>'显示', '0'=>'不显示'), 'value'=>$examInfo['exam_show_rank'], 'class'=>'radio'))?></li>
                    <li class="item"><div>是否设置综合奖项：</div><?php echo W('Radio',array('name'=>'exam_show_award', 'items'=>array('1'=>'显示', '0'=>'不显示'), 'value'=>$examInfo['exam_show_award'], 'class'=>'radio'))?></li>
                    
                    <li class="item"><div>特别提醒：</div><input type="text" name="exam_special_tip" class="input easyui-validatebox"   value="<?php echo $examInfo['exam_special_tip']?>" /> </li>
                </ul>
            </div>
            <div title="参赛条件">
            <ul>
                <li class="item"><b style="color:red">竞赛激活方式：激活卡，在线缴费，前台操作员工号及密码</b></li>
                <li class="item"><div>收费金额：</div><input type="text" name="exam_money" class="easyui-numberbox" value="<?php echo $examInfo['exam_money']?>" />元</li>
                <li class="item"><div>激活卡组别：</div><?php echo W('ArraySelect', array('attr'=>'name="exam_card_group" id="exam_card_group"', 'options'=>array_merge(array('0'=>'不设激活卡组'), $cardGroupArray), 'value'=>$examInfo['exam_card_group'] . '_'))?></li>
                <?php if($setStuCls):?>
                <li class="item"><div>班级筛选：</div></li>
                <?php else:?>
                <li class="item"><div>学籍卡必填:</div>
                <?php echo W('Radio',array('name'=>'require_xueji_code', 'items'=>array('1'=>'必填', '0'=>'不必填'), 'value'=>$examInfo['require_xueji_code'], 'class'=>'radio'))?>
                </li>
                <?php endif?>
                <li class="item"><div>初赛名单筛选组：</div>功能待开发</li>
                <li class="item"><div>跳过年级检测：</div><input type="checkbox" name="exam_skip_grade" value="1" <?php if($examInfo['exam_skip_grade']):?> checked="true"<?php endif;?> /></li>
            </ul>
            </div>
            <div title="竞赛介绍">
            <textarea name="exam_intro" id="exam_intro" style="width:99%;height:250px"><?php echo $examInfo['exam_intro']?></textarea>
                <?php echo W('Editor', array('id'=>'exam_intro'))?>
            </div>
            <div title="竞赛摘要">
                <textarea name="exam_sumary" id="exam_sumary" style="margin:2px;height:250px;width:99%"><?php echo $examInfo['exam_sumary']?></textarea>
                <?php echo W('Editor', array('id'=>'exam_sumary'))?>
            </div>
            <div title="考辅班信息">
            	<textarea name="exam_class" id="exam_class" style="margin:2px;height:250px;width:99%"><?php echo $examInfo['exam_class']?></textarea>
                <?php echo W('Editor', array('id'=>'exam_class'))?>
            </div>
            <div title="准考证信息">
                <ul>
                <li class="item"><div>准考证标题：</div><input class="input easyui-validatebox" required="true" type="text" style="width:200px" name="exam_card_caption" value="<?php echo $examInfo['exam_card_caption']?>" /><div style="display:none">长度：<input type="text" name="exam_card_len" class="easyui-numberspinner" value="<?php echo $examInfo['exam_card_len']?>" style="width:60px" /></div></li>
                <li class="item"><div>参赛须知：</div>
                <textarea name="exam_notice" id="exam_notice" class="easyui-validatebox" required="true"  style="margin:2px;resize: none; width:600px; height:100px;"><?php echo $examInfo['exam_notice']?></textarea>
                </li>
                <li class="item">
                <span style="color:red;">注意：为了避免参赛须知超出准考证边界范围，请将参赛须知内容控制在4行以内，每行字数请不要超过36字符。</span>
                </li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>