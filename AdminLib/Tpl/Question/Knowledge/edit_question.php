<empty name="question['question_type_id']">
<div style="color: #ff0000; font-size: 16px; text-align: center">题型为空，请在“试题查询”页面编辑该题题型。</div>
<else />
<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<link href="/static/css/question.css" type="text/css" rel="stylesheet" />
<link href="/static/ueditor1_4_3/themes/default/_css/ueditor.css" type="text/css" rel="stylesheet">
<style>
<!--
.success {
	background-color: #DFF0D8;	
} 
-->
</style>
</head>
<body>
	<div class="easyui-layout" data-options="fit: true">
		<div region="center" data-options="collapsible: false, border: false">
			<form id="question-edit-form" method="post" novalidate>
				<input type="hidden" id="id" name="id" value="{$question['id']}" />
				<input type="hidden" id="course_type_id" name="course_type_id" value="{$question['course_type_id']}" />
				<input type="hidden" id="question_type_id" name="question_type_id" value="{$question['question_type_id']}" />
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
					<tbody id="html"></tbody>
				</table>
			</form>
		</div>
		<div region="south" style="height: 32px;" data-options="collapsible: false, split: false, border: false">
			<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-save'" onclick="edit_save_question()">提交</a>
		</div>
	</div>
	<!--Begin 子题编辑对话框-->
	<div id="question-edit-sub-dlg" class="easyui-dialog" title="编辑" data-options="modal:true,closed:true,cache:false,iconCls:'icon-edit'" style="width:850px;height:400px;padding:5px;">
	    <iframe scrolling="auto" id="question-edit-sub-dlg-iframe" frameborder="0" src="" style="width:100%;height:100%;"></iframe>
	</div>
	<!--End 子题编辑对话框-->
	<object id="tiku_plugin" type="application/x-tkbsplugin" width="1" height="1" style="width:0px;height:0; overflow:hidden;">
	    <param name="onload" value="pluginLoaded" />
	</object>
	<script type="text/javascript" src="/static/ueditor1_4_3/ueditor.config.js"></script>
	<script type="text/javascript" src="/static/ueditor1_4_3/editor_api.js"></script>
	<script type="text/javascript" src="/static/js/ue.ext.openinword.js"></script>
	<script type="text/javascript" src="/static/js/question.js"></script>
	<script type="text/javascript" src="/static/js/jquery.raty.min.js"></script>
	<script type="text/javascript">
		function edit_save_question(){
			$('#question-edit-form').form('submit', {
				url: '/Question/Knowledge/edit_save_question',
				onSubmit: function () {
					return $(this).form('validate');
				},
				success: function (result) {
					result = JSON.parse(result);
					if(result.status){
						parent.do_search_question('{$question['id']}');
						parent.close_dlg();
					}else{
						$.messager.alert('提示信息', '修改失败!', 'warning');
					}
					
				}
			});
		}
		jQuery(document).ready(function(){
			var courseTypeId = {$question["course_type_id"]};
			var question_type_id = {$question["question_type_id"]};
			var id = {$question["id"]};
			$.post('/Question/Basic/getQuestionTypeByID', {id: question_type_id}, function(data){
				data = JSON.parse(data);
				_changeQuestionType(data.question_type_code, id);
			});		
		}); 
		$(function() {
			_init();
		});
		var ctrl = null;
		function open_word_dialog(ctrlid) {
			var content = '';
			var uid = '{$question["uid"]}';
			var euid = '';
			var date = '{$question["sdate"]}';
			var params = ctrlid;
			//id
			var strs = ctrlid.split('_');
			if (strs.length > 1)
				euid = strs[1];
			var ctrl = get_ue_ctrl(ctrlid);
			if (ctrl != null)
				content = ctrl.getContent();
			var isExist = '' == content ? 0 : 1;
			//alert(isExist + ' - ' + date + ' - ' + uid + ' - ' + euid);
			UE.getEditor(ctrlid).setDisabled('在WORD中编辑');
			//UE.getEditor('editor').setDisabled('fullscreen');
			var b = new Base64();
			//gs_tiku_plugin().openword(isExist, date, uid, euid, b.encode(params));
			var timestamp=new Date().getTime();
			window.ws.send('openword,'+isExist+',\\\\.\\pipe\\tiku_'+timestamp+','+date+','+uid+','+euid+','+b.encode(params)+',');
		}
		function _init() {
			var wsImpl = window.WebSocket || window.MozWebSocket;
			window.ws = new wsImpl('ws://localhost:12059/');
			var b = new Base64();
			// when data is comming from the server, this metod is called
            ws.onmessage = function (evt) {
                //inc.innerHTML += evt.data + '<br/>'; //调试用
                //收到word发送过来的消息
                msg = evt.data;
                if (msg[0] == '{') {
                    var wm = JSON.parse(msg);
                    var ctrlid = b.decode(wm.otherparams);
                    //alert(wm.data);
                    if (wm["msgName"] == "html"){ //收到html
                        //document.getElementById(ctrlid).innerHTML = wm.data;
                        var ctrl = get_ue_ctrl(ctrlid);
						if (ctrl != null) {
							ctrl.setContent(wm.data, 0);
							ctrl.setEnabled();
							$('#' + ctrlid).find('div[class~=edui-editor-iframeholder]').addClass('success');
						}
                    }else if (wm["msgName"] == "refresh"){ //html引用的图像上传完成，刷新显示图像
                        //document.getElementById(ctrlid).innerHTML = document.getElementById(wm.eguid).innerHTML;
                        var content = '';
						var ctrl = get_ue_ctrl(ctrlid);
						if (ctrl != null) {
							content = ctrl.getContent();
							ctrl.setContent(content, 0);
							ctrl.setEnabled();
							$('#' + ctrlid).find('div[class~=edui-editor-iframeholder]').addClass('success');
						}
                    }
                }
            };

            // when the connection is established, this method is called
            ws.onopen = function () {
                //inc.innerHTML += '.. connection open<br/>';
            };

            // when the connection is closed, this method is called
            ws.onclose = function () {
                //inc.innerHTML += '.. connection closed<br/>';
            }
			/*addEvent(gs_tiku_plugin(), 'wordmsg', function (msg) {
				var wm = JSON.parse(msg);
				var b = new Base64();
				var ctrlid = b.decode(wm.otherparams);
				if(wm.msgName == 'html'){
					var ctrl = get_ue_ctrl(ctrlid);
					if (ctrl != null) {
						ctrl.setContent(wm.data, 0);
						ctrl.setEnabled();
						$('#' + ctrlid).find('div[class~=edui-editor-iframeholder]').addClass('success');
					}
				} else if (wm.msgName == 'refresh') {
					var content = '';
					var ctrl = get_ue_ctrl(ctrlid);
					if (ctrl != null) {
						content = ctrl.getContent();
						ctrl.setContent(content, 0);
						ctrl.setEnabled();
						$('#' + ctrlid).find('div[class~=edui-editor-iframeholder]').addClass('success');
					}
				} else if (wm.msgName == 'nochange') {
					var ctrl = get_ue_ctrl(ctrlid);
					if (ctrl != null) {
						ctrl.setEnabled();
					}
				}
				else {
					alert('未知返回类型');
				}
			});*/
		}
		function _changeQuestionType(code,id) {
			var tmplName = getTmplByQuestionTypeCode(code);
			$.get("/Question/Knowledge/" + tmplName , {id: id }, function(result){
				$('#html').empty().html(result);
				$('.editor').each(function(i ,el) {
					UE.getEditor(el.id);
				});
			});
		}	
	</script>
</body>
</html>
</empty>