<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<link href="/static/css/question.css" type="text/css" rel="stylesheet" />
<link href="/static/ueditor1_4_3/themes/default/_css/ueditor.css" type="text/css" rel="stylesheet">
</head>
<body>
	<div id="basic-index-question-layout" class="easyui-layout" fit="true">
		<div region="center" data-options="fit: true, split: false, collapsible: false, border: false">
			<form id="fourlevel-add-form" method="post" novalidate>
				<input type="hidden" name="level" value="{$level}" />
				<input type="hidden" name="subjectid" value="{$subjectid}" />
				<input type="hidden" name="parent_id" id="fourlevel_add_form_parent_id" value="{$parentId}" />
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
					<tr>
						<td class="alt right wd_120">上级知识点：</td>
						<td>
							<a href="#" id="fourlevel_add_form_path">{$path}</a>
						</td>
					</tr>
					<tr>
						<td class="alt right"><span class="red">*</span> 名称：</td>
						<td>
							<span id="fourlevel_name"></span>
							<input type="hidden" id="name" name="name" value="" data-options="required: true" autocomplete="off" maxlength="100" />&nbsp;&nbsp;
							<input type="hidden" id="origin_knowledge_id" name="origin_knowledge_id" value="">
							<a href="#" id="knowledge_select2" class="blue ">选取五级知识点</a>
						</td>
					</tr>
					<tr>
						<td class="alt right"><span class="red">*</span> 排序：</td>
						<td>
							<input type="text" name="sort" value="" class="easyui-numberbox input wd_50" data-options="required: true, min: 1, precision: 0" autocomplete="off" maxlength="6" />
						</td>
					</tr>
					<tr>
						<td class="alt right">描述：</td>
						<td>
							<textarea id="remark" name="remark" class="input wd_400"></textarea>
						</td>
					</tr>
					<tr>
						<td class="alt right">讲解：</td>
                        
						<td>
                            <textarea id="fourlevel_analysis_{$nextFourlevelId}" name="analysis" class="editor analysis"></textarea> <!--编辑器替换为KindEditor编辑器 2015 03 10-->  
						</td>
					</tr>
				</table>
			</form>
		</div>
		<div region="south" style="height: 32px;" data-options="collapsible: false, border: false, split: false">
			<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-save'" onclick="fourlevel_match_save()">保存</a>
		</div>
	</div>
	<!--<script type="text/javascript" src="/static/ueditor1_4_3/ueditor.config.js"></script>
	<script type="text/javascript" src="/static/ueditor1_4_3/editor_api.js"></script> 编辑器替换为 KindEditor编辑器 2015 03 10-->

	<!--Begin 知识点选择对话框-->
	<div id="fourlevel-add-dlg" class="easyui-dialog" title="知识点" data-options="iconCls:'icon-table',modal:true,closed:true" style="width:600px;height:350px;padding:5px;"></div>
	<!--End 知识点选择对话框-->
	<object id="tiku_plugin" type="application/x-tkbsplugin" width="1"
		height="1" style="width: 0px; height: 0; overflow: hidden;">
		<param name="onload" value="pluginLoaded" />
	</object>
	<script type="text/javascript" src="/static/ueditor1_4_3/ueditor.config.js"></script>
	<script type="text/javascript" src="/static/ueditor1_4_3/editor_api.js"></script>
	<script type="text/javascript" src="/static/js/ue.ext.openinword.js"></script>
	<script type="text/javascript" src="/static/js/question.js"></script>
	<script language='javascript' type='text/javascript'>
		$(function() {
			//UE.getEditor('analysis', {
			//	initialFrameHeight: 100
			//});
			_init();
			$('.editor').each(function(i ,el) {
				UE.getEditor(el.id);
			});
			$('#fourlevel_add_form_path').tooltip({
				content: $('<div></div>'),
				showEvent: 'click',
				onUpdate: function(content){
					content.panel({
						width: 405,
						height: 360,
						border: false,
						cache: false,
						href: '/Question/Basic/fourlevels?subjectid={$subjectid}'
					});
				},
				onShow: function(){
					var t = $(this);
						t.tooltip('tip').unbind().bind('mouseenter', function(){
						t.tooltip('show');
						}).bind('mouseleave', function(){
						t.tooltip('hide');
					});
				},
				onPosition: function(){
					$(this).tooltip('tip').css('left', $(this).offset().left);
					$(this).tooltip('arrow').css('left', 20);
				}
			});
			
			$('#knowledge_select2').tooltip({
				content: $('<div></div>'),
				showEvent: 'click',
				onUpdate: function(content){
					content.panel({
						width: 455,
						height: 410,
						border: false,
						cache: false,
						href: '/Question/Basic/fourlevels3?subjectid={$subjectid}&is_gaosi=1'
					});
				},
				onShow: function(){
					var t = $(this);
						t.tooltip('tip').unbind().bind('mouseenter', function(){
						t.tooltip('show');
						}).bind('mouseleave', function(){
						t.tooltip('hide');
					});
				},
				onPosition: function(){
					$(this).tooltip('tip').css('left', $(this).offset().left);
					$(this).tooltip('arrow').css('left', 20);
				}
			});
		});
		function fourlevel_match_save() {
			$('#fourlevel-add-form').form('submit', {
				url: '/Question/Basic/fourlevel_match_save',
				onSubmit: function () {
					return $(this).form('validate');
				},
				success: function (result) {
					var result = JSON.parse(result);
					if (result.status) {
						parent._initFourlevel('{$subjectid}');
						parent.$('#basic-index-fourlevel-match-dlg').dialog('close');
					} else {
						$.messager.alert('错误信息', result.message, 'error');
					}
				}
			});
		}
		
		
	var ctrl = null;
		var uuid = '';
		var path = '';
		function open_word_dialog(ctrlid) {
			var content = '';
			var uid = '';
			var euid = '';
			var date = path;
			var params = ctrlid;
			var strs = ctrlid.split('_');
			if (strs.length > 1){
				date = strs[0];
				uid = strs[1];
				euid = strs[2];
			}
			var ctrl = get_ue_ctrl(ctrlid);
			if (ctrl != null)
				content = ctrl.getContent();
			var isExist = '' == content ? 0 : 1;
			//alert( isExist+':'+date + ' /' + uid + ' /' + euid);
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
	}
	</script>
</body>
</html>