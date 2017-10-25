<empty name="question">
	<div class="red" style="text-align: center; font-size: 16px;">没有满足条件的记录！</div>
<else /> 
	<div class="qt_box mr_b_5">
		<div class="info" style="padding: 5px 0 0 10px;">
			<span style="float:left" class="red blod">{$question['file_name']}</span>
		</div>
		<div style="padding: 8px 0 0 8px;">
			<table cellpadding="0" cellspacing="0" style="width:95%;">
				<tr>
					<td colspan="4" style="height: 35px;">
						<a href="#" class="easyui-linkbutton" data-options="plain:true, iconCls:'icon-save'" onclick="edit_save_classic_question({$question['question_id']})">提交</a>
						<a href="#" class="easyui-linkbutton" style="margin-left: 25px;" data-options="plain:true, iconCls:'icon-add'" onclick="skip_question2({$question['question_id']})">跳过</a>
					</td>
				</tr>
			</table>
			<form id="question-edit-classic-form" method="post" novalidate>
				<input type="hidden" id="id" name="id" value="{$question['question_id']}" />
				<input type="hidden" id="course_type_id" name="course_type_id" value="{$question['course_type_id']}" />
				<input type="hidden" id="question_type_id" name="question_type_id" value="{$question['question_type_id']}" />
				<table style="margin-top:10px;" class="tableInfo">
					<tr>
						<td align="left" colspan="2">
							<label>ID：{$question['question_id']}</label>
							<label style="margin-left: 25px">题号：{$question['number']}</label>
						</td>
					</tr>
					<tbody id="html"></tbody>
				</table>
			</form>
	  	</div>
	</div>
</empty>
<script language='javascript' type='text/javascript'>
	$(function() {
		$('.editor').each(function(i ,el) {
			UE.getEditor(el.id);
		});
		_changeQuestionType('{$question["question_type_code"]}', '{$question["question_id"]}');
		_init();
	});
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
				}
			} else if (wm.msgName == 'refresh') {
				var content = '';
				var ctrl = get_ue_ctrl(ctrlid);
				if (ctrl != null) {
					content = ctrl.getContent();
					ctrl.setContent(content, 0);
					ctrl.setEnabled();
				}
			}
			else {
				alert('未知类型');
			}
		});*/
	}
	function skip_question2(id) {
		$.messager.confirm('信息提示', '跳转至下一题？', function(r){
			 if (r){
				$.post("/Question/Knowledge/skip_question2", {id: id}, function(rs) {
					rs = JSON.parse(rs);
					if (rs.status) {
						$('#question-classic-list-panel').panel('refresh', '/Question/Knowledge/render_edit_question_classic_list?coursetypeid={$coursetypeid}');
					}
					else {
						alert('保存失败！');
					}
				});
			 }
		 });
	}
	function edit_save_classic_question(id) {
		$('#question-edit-classic-form').form('submit', {
			url: '/Question/Knowledge/edit_save_classic_question',
			onSubmit: function () {
				return $(this).form('validate');
			},
			success: function (result) {
				result = JSON.parse(result);
				if(result.status){
					//获取统计数
					$.post('/Question/Knowledge/getQuestionStatisticsByCourseTypeId2', {coursetypeid: courseTypeId}, function(data){
						data = JSON.parse(data);
						$('#left_non_edit_question_count').html(data.left_non_edit_question_count);
						$('#total_question_count').html(data.total_question_count);
						$('#my_op_question_count').html(data.my_op_question_count);
					});
					
					$.messager.confirm('信息提示', '修改成功，跳转至下一题？', function(r){
						if (r){
							$('#question-classic-list-panel').panel('refresh', '/Question/Knowledge/render_edit_question_classic_list?coursetypeid={$coursetypeid}');
						}
					});
				}else{
					$.messager.alert('提示信息', '修改失败!', 'info');
				}
				
			}
		});
    }
	function _changeQuestionType(code, id) {
		var tmplName = getTmplByQuestionTypeCode(code);
		$.get("/Question/Knowledge/" + tmplName , {id: id }, function(result){
			$('#html').empty().html(result);
			$('.editor').each(function(i ,el) {
				UE.getEditor(el.id);
			});
		});
	}
</script>