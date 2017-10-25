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
			<form id="fourlevel-edit-form" method="post" novalidate>
				<input type="hidden" name="id" value="{$fourlevelInfo['id']}" />
				<input type="hidden" name="level" value="{$fourlevelInfo['level']}" />
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
					<tr>
						<td class="alt right wd_120">上级知识点：</td>
						<td>
							<input name="parent_name" class="easyui-combotree" style="width: 300px;" data-options="url: '/Question/Basic/getFourlevelsChilds?subjectid={$subjectid}',value:'<if condition="$fourlevelInfo['parent_id']!=0">{$fourlevelInfo['parent_name']}</if>',method:'get',lines:true" />
							<input type="hidden" id="parent_id" name="parent_id" value="<?php echo $fourlevelInfo['parent_id']?>">
						</td>
					</tr>
					<tr>
						<td class="alt right wd_120"><span class="red">*</span> 名称：</td>
						<td>
							<input type="text" name="name" value="{$fourlevelInfo['name']}" class="easyui-validatebox input wd_400" data-options="required: true" autocomplete="off" maxlength="100" />
						</td>
					</tr>
					<tr>
						<td class="alt right"><span class="red">*</span> 排序：</td>
						<td>
							<input type="text" name="sort" value="{$fourlevelInfo['sort']}" class="easyui-numberbox input wd_50" data-options="required: true, min: 1, precision: 0" autocomplete="off" maxlength="6" />
						</td>
					</tr>
					<tr>
						<td class="alt right">描述：</td>
						<td>
							<textarea name="remark" class="input wd_400">{$fourlevelInfo['remark']}</textarea>
						</td>
					</tr>
					<tr>
						<td class="alt right" valign="top"><?php if($fourlevelInfo['level']==4):?>知识元精讲<?php else:?>知识精讲<?php endif;?>：</td>

                        <td>
                            <textarea id="fourlevel_analysis_{$fourlevelInfo['id']}" name="analysis" class="editor">{$fourlevelInfo['analysis']}</textarea> 
                        </td>

					</tr>
				<?php if($fourlevelInfo['level']==4):?>
					<tr>
						<td class="alt right" valign="top">三点剖析：</td>
                        <td>
                            <textarea id="fourlevel_analysis3_{$fourlevelInfo['id']}" name="analysis3" class="editor">{$fourlevelInfo['analysis3']}</textarea> 
                        </td>
					</tr>
				<?php endif;?>
				</table>
			</form>
		</div>
		<div region="south" style="height: 32px;" data-options="collapsible: false, border: false, split: false">
			<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-save'" onclick="fourlevel_save()">保存</a>
		</div>
	</div>
    <!--<script type="text/javascript" src="/static/ueditor1_4_3/ueditor.config.js"></script>
    <script type="text/javascript" src="/static/ueditor1_4_3/editor_api.js"></script> 编辑器替换为 KindEditor编辑器 2015 03 10-->

	<!--Begin 知识点选择对话框-->
	<div id="fourlevel-add-dlg" class="easyui-dialog" title="知识点" data-options="iconCls:'icon-table',modal:true,closed:true" style="width:600px;height:400px;padding:5px;"></div>
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
			_init();
			$('.editor').each(function(i ,el) {
				UE.getEditor(el.id);
			});
			////UE.getEditor('analysis', {
			//	initialFrameHeight: 100
			//});
		});
		function fourlevel_save() {
			$('#fourlevel-edit-form').form('submit', {
				url: '/Question/Basic/fourlevel_edit_save',
				onSubmit: function () {
					return $(this).form('validate');
				},
				success: function (result) {
					var result = JSON.parse(result);
					if (result.status) {
						$.messager.alert('操作提示', '操作成功!','info',function(){
							parent.$('#basic-index-fourlevel-dlg').dialog('close');
						});
				
					} else {
						$.messager.alert('错误信息', '操作失败!', 'error');
					}
				}
			});
		}
		
		var ctrl = null;
		var uuid = '';
		var path = '';
		function open_word_dialog(ctrlid) {
			var content = '';
			var uid = uuid;
			var euid = '';
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