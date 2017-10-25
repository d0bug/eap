<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <script type="text/javascript" src="/static/kindeditor/kindeditor-min.js"></script>
        <script type="text/javascript">
         var chars = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
         var quesEditors = {};
         var editorOptions = {cssPath : ['/static/kindeditor/plugins/code/prettify.css',
                                         '/static/kindeditor/plugins/jmeditor/extra/mathquill-0.9.1/mathquill.css'],
            resizeType:1,
            width:350,
            height:50,
            minWidth:350,
            minHeight:50,
			uploadJson : '<?php echo U('Util/Editor/upload_json')?>',
			fileManagerJson : '<?php echo U('Util/Editor/file_manager_json')?>',
			allowFileManager : true,
			items:['fontname', 'fontsize', 'forecolor', 'hilightcolor', 'bold','italic', 'underline', 'image', 'table', 'jmeditor', 'source'],
			afterCreate : function() {
				var self = this;
				KindEditor.ctrl(document, 13, function() {
					self.sync();
				});
				KindEditor.ctrl(self.edit.doc, 13, function() {
					self.sync();
				});
		 }}
        <?php if($permValue & $PERM_WRITE):?>
        function saveBody() {
            var bodyInfo = {exam_id:jQuery.trim(jQuery('#exam_id').val()),
                            subject_code:jQuery.trim(jQuery('#subject_code').val()),
                            body_id:jQuery.trim(jQuery('#body_id').val()),
                            body_title:jQuery.trim(jQuery('#body_title').val()),
                            body_content:jQuery.trim(jQuery('#body_content').val())};
            jQuery.post('<?php echo $bodyInfoUrl?>', bodyInfo, function(data){
                if(data.errorMsg) {
                    alert(data.errorMsg);
                } else {
                    jQuery('#body_id').html('<option value="0">请选择大题题干</option>');
                    jQuery.each(data,function(k,v){
                        jQuery('<option value="' + k + '">' + v + '</option>').appendTo('#body_id');
                    })
                }
            }, 'json');
            
        }
        
        function saveQues() {
            if('' == jQuery.trim(jQuery('#ques_sumary').val())) {
                jQuery('#ques_sumary').validatebox('validate');
                return;
            }
            for(var editor in quesEditors){quesEditors[editor].sync()}
            var formData = jQuery('input,select,textarea').serialize();
            jQuery.post('<?php echo $url?>', formData, function(data) {
               if(data.errorMsg) {
                alert(data.errorMsg)
               } else {
                alert('试题修改成功');
                parent.closeDlg('<?php echo $dlgId?>');
               }
            }, 'json')
        }
        
        function enableEdit() {
            jQuery('#enableEdit').hide();
            jQuery('#saveBtn').show();
            for(var k in keEditors) keEditors[k].readonly(false);
            jQuery('input,select,textarea').attr('disabled', false);
            jQuery('.readonly').attr('disabled',true);
            jQuery('#ques_level').numberspinner('enable');
        }
        <?php endif?>
        
        function quesBodyBtn(){
            var bodyId = jQuery.trim(jQuery('#body_id').val());
            <?php if($permValue & $PERM_WRITE):?>
            var text = bodyId.length > 10 ? '提交修改' : '添加大题题干';
            jQuery('#quesBody_btn').linkbutton({
                text:text
            })
            <?php endif?>
            setTimeout(function(){
                getBodyInfo(bodyId);
            }, 200)
            
        }
        
        function getBodyInfo(bodyId) {
            if(bodyId.length >10) {
                jQuery.getJSON('<?php echo $bodyInfoUrl?>', {bid:bodyId}, function(data){
                    jQuery('#body_title').val(data.body_title);
                    keEditors.body_content.html(data.body_content);
                    
                })
            } else {
                jQuery('#body_title').val('');
                keEditors.body_content.html('');
            }
        }
         
        jQuery(function(){
             quesBodyBtn();
             jQuery('#body_id').change(quesBodyBtn);
             jQuery('input,select,textarea').attr('disabled', true);
             jQuery('#ques_level').numberspinner('disable');
         })
        </script>
        <style type="text/css">
        .quesInfo{border:1px solid #ddd;border-bottom:none;margin-top:5px}
        .quesInfo th, .quesInfo td{border-bottom:1px solid #ddd;text-align:left;font-weight:bold}
        .quesInfo th{text-align:right;}
        #ques_items th{background:#ddd;color:red;font-size:20px;text-align;center;padding:5px}
        </style>
    </head>
    <body class="easyui-layout" fit="true" border="false">
        <div region="north" style="height:155px;">
            <?php if($permValue & $PERM_WRITE):?>
            <div class="datagrid-toolbar">
                <a id="enableEdit" href="javascript:void(0)" onclick="enableEdit()" class="easyui-linkbutton" iconCls="icon-edit">启用编辑</a>
                <a style="display:none" id="saveBtn" href="javascript:void(0)" onclick="saveQues()" class="easyui-linkbutton" iconCls="icon-save">保存试题信息</a>
                <input type="hidden" id="ques_id" name="ques_id" value="<?php echo $quesInfo['ques_id']?>" />
                <input type="hidden" id="exam_id" name="exam_id" value="<?php echo $quesInfo['exam_id']?>" />
                <input type="hidden" id="subject_code" name="subject_code" value="<?php echo $quesInfo['subject_code']?>" />
                <input type="hidden" name="ques_type" value="<?php echo $quesInfo['ques_type']?>" />
            </div>
            <?php endif?>
            <table class="quesInfo" width="90%" align="center" cellpadding="2" cellspacing="0">
                <tr><th>竞赛名称：</th><td colspan="3"><input type="text" class="readonly" disabled="true" value="<?php echo $examInfo['exam_caption']?>" /></td></tr>
                <tr><th width="10%">竞赛科目：</th><td width="40%"><input class="readonly" disabled="true" type="text" value="<?php echo $subjectInfo['subject_name']?>" /></td>
                    <th width="15%">试题类型：</th><td><?php echo W('ArraySelect', array('attr'=>'class="readonly" disabled="true" name="ques_type"', 'options'=>$quesTypeArray, 'value'=>$quesType))?></td></tr>
                    <tr><th>试题描述：</th><td colspan="3"><input style="width:90%;border:1px solid #ccc" class="easyui-validatebox" required="true" type="text" name="ques_sumary" id="ques_sumary" value="<?php echo $quesInfo['ques_sumary']?>" /></td></tr>
                <tr><th>难度级别：</th><td colspan="3"><input type="text" value="<?php echo $quesInfo['ques_level']?>" class="easyui-numberspinner" min="1" max="5" id="ques_level" name="ques_level" /></td></tr>
            </table>
        </div>
        <div region="center">
            <div class="easyui-tabs" fit="true" border="false">
                <div title="小题题干">
                <textarea id="ques_content" name="ques_content" style="width:99%;height:245px"><?php echo $quesInfo['ques_content']?></textarea>
        <?php echo W('Editor', array('id'=>'ques_content', 'readonly'=>true));?>
                </div>
                <div title="试题答案&错题分析">
                    <table class="quesInfo" width="100%" cellpadding="4" cellspacing="0">
                        <tr><td>&nbsp;试题答案</td><td>&nbsp;错题分析</td></tr>
                        <tr><td><textarea id="ques_answer" name="ques_answer" style="width:99%;height:230px"><?php echo $quesInfo['ques_answer']?></textarea></td>
                        <td><textarea id="ques_analy" name="ques_analy" style="width:99%;height:230px"><?php echo $quesInfo['ques_analy']?></textarea></td></tr>
                    </table>
                    <?php echo W('Editor', array('id'=>'ques_answer','layout'=>'simple', 'readonly'=>true))?>
                    <?php echo W('Editor', array('id'=>'ques_analy','layout'=>'simple', 'readonly'=>true))?>
                </div>
                <div title="考查内容">
                    <table class="quesInfo" width="100%" height="98%">
                        <tr style="background:#eee">
                            <th height="20" width="200">选择知识点(ctrl,shift多选)</th><th style="text-align:left">考查内容说明</th>
                        </tr>
                        <tr>
                            <td valign="top"><?php echo W('KnowledgeTree', array('subject'=>$subjectCode, 'attr'=>'multiple="true" size="12" name="ques_knowledge[]" id="ques_knowledge"', 'value'=>array_keys($quesInfo['ques_knowledge'])))?></td>
                            <td valign="top">
                            <textarea id="ques_analy_text" name="ques_analy_text" style="width:100%;height:200px"><?php echo $quesInfo['ques_analy_text']?></textarea>
                            <?php echo W('Editor', array('id'=>'ques_analy_text', 'readonly'=>true))?></td>
                        </tr>
                    </table>
                    
                </div>
                <div title="大题题干(非必填）">
                    <div class="easyui-layout" fit="true" border="false">
                        <div region="west" style="width:180px" title="请选择">
                        <?php echo W('ArraySelect', array('attr'=>' id="body_id" name="body_id" size="13" style="width:175px"', 'options'=>array_merge(array('0'=>'请选择大题题干'), $quesBodyArray), 'value'=>$quesInfo['body_id']))?>
                        </div>
                        <div region="center">
                            <div class="datagrid-toolbar">
                                <a href="javascript:saveBody()" class="easyui-linkbutton" iconCls="icon-save" id="quesBody_btn">保存大题题干</a>
                                <span>当有多道小题需要共用一个题干时，必须首先创建大题题干</span>
                            </div>
                            <table width="100%">
                                <tr><th style="width:90px">题干标题：</th><td><input type="text" id="body_title" name="body_title" /></td></tr>
                                <tr><td colspan="2">
                                <textarea name="body_content" id="body_content" style="width:96%;height:160px"></textarea>
                                <?php echo W('Editor', array('id'=>'body_content', 'readonly'=>true))?>
                                </td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>