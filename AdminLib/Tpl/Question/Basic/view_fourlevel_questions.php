<empty name="questions['rows']">
	<div class="red" style="text-align: center; font-size: 16px;">没有满足条件的记录！</div>
<else />
	<volist name="questions['rows']" id="question">
	<div class="qt_box mr_b_5">
		<div class="info" style="padding: 5px 0 0 10px;">
			<span style="float:left" class="red blod">{$question['file_name']}</span>
			<span style="float:right; margin-right: 5px" class="blod"><a href="#" onclick="copy_question_content_word('{$question['sdate']}', '{$question['uid']}')">复制题干</a></span>
		</div>
		<div class="info" style="padding: 5px 0 0 10px;">
			<span style="float:right; padding-right: 5px;">
				<label><input type="checkbox" onclick="set_question_classic({$question['question_id']}, {$question['is_classic']})" <if condition="$question['is_classic'] eq 1">checked="checked"</if>>经典题</label>
			</span>
			<span style="float:right; margin-right: 25px;" class="blod">题号：{$question['number']}</span>
			<span style="float:right; margin-right: 25px;" class="blod">ID：{$question['question_id']}</span>
		</div>
		<div style="padding: 5px 0 0 5px;">
			<div>
				<div class="content">
					<table>
						<tr>
							<td>{$question['content']}</td>
						</tr>
					</table>
					<notempty name="question['options']">
					<table>
						<volist name="question['options']" id="option">
						<tr>
							<td style="width: 20px; height: 20px;">{$i-1 | get_option_flag_name}．</td>
							<td>{$option.content}</td>
						</tr>
						</volist>
					</table>
					</notempty>
				</div>
			</div>
			<div class="answer">
			<div class="action">
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tbody>
						<tr>
							<td valign="middle" style="height: 28px;">【答案】</td>
						</tr>
						<tr>
							<td style="height: 25px;">
								<notempty name="question['options']">
								<volist name="question['options']" id="option">
									<if condition="($option.is_answer eq 1)"><strong>{$i-1 | get_option_flag_name}</strong></if>
								</volist>
								</notempty>
								<notempty name="question['answers']">
								<table>
									<volist name="question['answers']" id="answer">
									<tr>
										<td><span>{$answer.content}</span></td>
									</tr>
									</volist>
								</table>
								</notempty>
							</td>
						</tr>
						<tr>
							<td valign="middle" style="height: 28px;">【解析】</td>
						</tr>
						<tr>
							<td><empty name="question['analysis']">无<else />{$question['analysis']}</empty></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	  	</div>
	</div>
	</volist>
	<!--Begin 试题预览-->
	<div id="question-simple-my-questions" class="easyui-dialog" style="width: 800px; height: 500px; padding: 5px;" title="我的操作" data-options="iconCls:'icon-table',modal:true, closed:true"></div>
	<!--End 试题预览-->
</empty>
<object id="tiku_plugin" type="application/x-tkbsplugin" width="1" height="1" style="width:0px;height:0; overflow:hidden;">
    <param name="onload" value="pluginLoaded" />
</object>
<script type="text/javascript" src="/static/js/question.js"></script>
<script language='javascript' type='text/javascript'>
	$(function() {
		_init();
		$.extend($.fn.validatebox.defaults.rules, {
		    checkKnode: {
		        validator: function (value, param) {
		            return $('#knowledge_ids').val() != '';
		        },
		        message: '请选择知识点'
		    }
		});
	});
	function copy_question_content_word(date, uid) {
		//gs_tiku_plugin().openword(2, date, uid, 'content', '');
		/*var content = '';
		var uid = uid;
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
		//gs_tiku_plugin().openword(isExist, date, uid, euid, b.encode(params));*/
		var timestamp=new Date().getTime();
		window.ws.send('openword,1,\\\\.\\pipe\\tiku_'+timestamp+','+date+','+uid+',content,');
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
	function set_question_classic(id) {
		$.post("/Question/Knowledge/set_question_classic", {question_id: id}, function(rs) {
			rs = JSON.parse(rs);
			if (rs.status) {
				alert('设置成功！');
				//var html = $('#question_' + id).html();
				//html = html == '' ? '经典题' : '';
				//$('#question_' + id).html(html);
			}
			else {
				alert('保存失败！');
			}
		});
	}
	/*function my_edit_questions() {
		$('#question-simple-my-questions').dialog({
			href: '/Question/Knowledge/my_edit_questions'
		}).dialog('open');
	}
	function skip_question(id) {
		$.messager.confirm('信息提示', '跳转至下一题？', function(r){
			 if (r){
				$.post("/Question/Knowledge/skip_question", {id: id}, function(rs) {
					rs = JSON.parse(rs);
					if (rs.status) {
						$('#question-smiple-list-panel').panel('refresh', '/Question/Knowledge/render_question_simple_list?coursetypeid={$coursetypeid}');
					}
					else {
						alert('保存失败！');
					}
				});
			 }
		 });
	}
	function edit_simple_question(id) {
		if (!$('#question-simple-list-form').form('validate')) {
			return;
		}
		var isContentError = $('#is_content_error').attr("checked") == 'checked' ? 1 : 0;
		var isClassic = $('#is_classic').attr("checked") == 'checked' ? 1 : 0;		
		var knowledgeId = '';
		var subknowledgeIds = '';
		var tmpIds = [];
		
		var knowledgeIds = $('#knowledge_ids').val();
		var ids = knowledgeIds.split(',');
		//最后一个知识点为主知识点，其余为副知识点
		if (ids.length == 1) {
			knowledgeId = ids[0];
		}
		else if (ids.length > 1){
			knowledgeId = ids[ids.length - 1];
			for(var i = 0 ; i < ids.length-1 ; i++) {
				tmpIds.push(ids[i]);
			}
			subknowledgeIds = tmpIds.join(',');
		}
		var difficulty = $('#difficulty').combobox('getValue');
		var msg = '';
		if(difficulty == '') {
			msg = '请选择难度！<br/>';
		}
		if (knowledgeId　== '') {
			msg += '请选择知识点！';
		}
		if(msg != '') {
			 $.messager.show({
				 title:'信息提示',
				 msg:msg,
				 showType:'show',
				 style:{
				 right:'',
				 top:document.body.scrollTop+document.documentElement.scrollTop,
				 bottom:''
				 }
				 });
			return;
		}
		
		$.post("/Question/Knowledge/save_simple_question", {id: id, is_content_error: isContentError, is_classic: isClassic, knowledge_id: knowledgeId, sub_knowledge_id: subknowledgeIds, difficulty: difficulty}, function(rs) {
			rs = JSON.parse(rs);
			if (rs.status) {
				 $.messager.confirm('信息提示', '修改成功，跳转至下一题？', function(r){
					 if (r){
						 $('#question-smiple-list-panel').panel('refresh', '/Question/Knowledge/render_question_simple_list?coursetypeid={$coursetypeid}');
					 }
				 });
			}
			else {
				alert('保存失败！');
			}
		});
    }
    function do_search(val) {
		var url = '/Question/Basic/getKnowledgesSearch?coursetypeid={$coursetypeid}&kw=' + val;
		if (val == '')
			url = '/Question/Basic/getKnowledgesChilds?coursetypeid={$coursetypeid}';
		$('#knowledge-index-tree').tree({
			url: url
		});
	}
    function do_select(node) {
    	if($(this).tree('isLeaf', node.target)){
            var len = $('#knowledge_names li').length;
            var ids = $('#knowledge_ids').val();
            ids = (ids == '') ? node.id : ids + ',' + node.id;
    		$('#knowledge_ids').val(ids);
    		$('#knowledge_names').append('<li onclick="remove_item(' + len + ')" title="点击删除" style="cursor: pointer;" id="' + (len) + '"><a href="#" style="display: block;"><em></em>．' + node.text + '</a></li>');
    		re_bind_list();
    	}
    	else {
    		$(this).tree('toggle', node.target);
    	}
    }
	function remove_item(id) {
		$.each($('#knowledge_names li'),
            function(n, v) {
        		if (v.id == id) {
        			var strs = $('#knowledge_ids').val();
        			var ids = strs.split(',');
        			ids.splice(n, 1);
        			$('#knowledge_ids').val(ids.join(','));
	        		$(v).remove();
	        		return;
        		}
			}
		);
		re_bind_list();
	}
	function re_bind_list() {
		var len = $('#knowledge_names li').length;
		$.each($('#knowledge_names li'), 
            function(n, v) {
				$(v).find('em').html(n + 1);
			}
		);
	}*/
</script>