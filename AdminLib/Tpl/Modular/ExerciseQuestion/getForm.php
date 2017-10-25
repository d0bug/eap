<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.validate.js"></script>
<script type="text/javascript" src="/static/js/jquery.uploadify-3.1.min.js"></script>
<!--<script type="text/javascript" src="/static/js/use_uploadify.js"></script>-->
<script type="text/javascript" src="/static/js/vip.js"></script>
<link href="/static/css/uploadify.css" type="text/css" rel="stylesheet" />
<link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
        function plugin0()
        {
            return document.getElementById('plugin0');
        }
        plugin1 = plugin0;
        function addEvent(obj, name, func)
        {
            if (obj.attachEvent) {
                obj.attachEvent("on"+name, func);
            } else {
                obj.addEventListener(name, func, false);
            }
        }

        function load()
        {
            addEvent(plugin1(), 'test', function(){
                alert("Received a test event from the plugin.")
            });

            addEvent(plugin1(), 'wordmsg', function (msg) {
                var wm = JSON.parse(msg);

               // alert(wm.data);
                if (wm["msgName"] == "html") {
                	var html = delHtmlTag(wm.data,wm.eguid);
                   //document.getElementById(wm.eguid).innerHTML = html;
                   document.getElementById(wm.eguid).value =html;
                   //alert('#'+wm.egui);
                   //$('#'+wm.egui).val(html);
                }

                else if (wm["msgName"] == "refresh")
                    document.getElementById(wm.eguid).innerHTML = document.getElementById(wm.eguid).innerHTML;

//                document.getElementById(wm.eguid).innerHTML = "$\sqrt{{{c}^{2}}+{{d}^{2}}}$";
               //document.getElementById("testmath").innerHTML = "\(\sqrt{{{c}^{2}}+{{d}^{2}}}\)";
               // MathJax.Hub.Queue(["Typeset", MathJax.Hub], wm.eguid);
                //MathJax.Hub.Queue(["Typeset", MathJax.Hub], "testmath");
            });
        }
        function pluginLoaded() {
            //alert("Plugin loaded!");
			plugin1().initWordEventListener();
        }

        function addTestEvent()
        {
            addEvent(plugin1(), 'echo', function(txt,count){
                alert(txt+count);
            });
        }

        function testEvent()
        {
            plugin1().testEvent();
        }

        function pluginValid()
        {
            if(plugin1().valid){
                alert(plugin1().echo("This plugin seems to be working!"));
            } else {
                alert("Plugin is not working :(");
            }
        }
        function delHtmlTag(str,idstr)
			{

                 	//return str.replace(/<[^s]|[^>]+>/g,"");//去掉所有的html标记
                 	str = str.replace(/(<\/?(?!img)[^>\/]*)\/?>/gi,'');
                 	return str;
                 	$('#review'+idstr).html(str);
                 	//alert('#review'+idstr);
					var nstr = str.match(/http:\/\/[^\"]{10,}(?=\")/i);
					//alert(nstr);
					if(nstr != null) {
						return nstr;
					} else {
						return str;
					}



			}
    </script>

<script type="text/javascript">
$(document).ready(function() {
	$("#editForm").validate({
		rules: {

			title: {
				required: true,
				maxlength: 80
			},
			/*mod_id: {
				min:10
			},
			difficulty: {
				min:10
			},
			knowledge_id: {
				min:10
			},
			category_id: {
				min:10
			},*/
			question: {
				required: true,
			}
		},
		messages: {

			title: {
				required: '请填写知识点标题',
				maxlength: '知识点标题不能超过80字'
			},
			/*mod_id: {
				min: '必须输入一个题模',
			},
			difficulty: {
				min: '必须选择一个难度',
			},
			knowledge_id: {
				min: '必须选择一个知识点',
			},
			category_id: {
				min: '必须选择一个分类'
			},*/
			question: {
				required: '请输入题干'
			}
		},

		errorPlacement: function(error, element) {
			if (element.is(':radio') || element.is(':checkbox')) {
				var eid = element.attr('name');
				error.appendTo(element.parent());
			} else {
				error.insertAfter(element);
			}
		},
	});
})

</script>
</head>
<body onload="load()" >
<div region="center" >
<div id="main">
	<h2>知识点编辑</h2>
	<input type="hidden" id="uploadimg_url" value="<?php echo U('Modular/ExerciseQuestion/upload')?>">

	<form id="editForm"  method="POST" enctype="multipart/form-data" action="<?php echo $action;?>">

	<table id ="form" width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">

		<tr>
			<td class="alt"><font color="red">*</font>知识点标题： </td>
			<td>
			    <input type="hidden" value="<?php echo $content;?>" name="content">

				<input type="text" id="title" name="title" placeholder="请输入知识点标题..." value="<?php echo $title;?>" size="100"  onkeydown="return check_length('title','titleMsg',100)" onkeyup="return check_length('title','titleMsg',100)"><span id="titleMsg">还可输入100个字</span>
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>题模： </td>
			<td>
				<input type="text" name="mod_id" id="mod_id" value="<?php echo $mod_id;?>">
			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>解题视频： </td>
			<td>
				<input type="text" name="solve_flash" id="solve_flash" value="<?php echo $solve_flash;?>">
			</td>
		</tr>

		<tr>
			<td class="alt"><font color="red">*</font>分类： </td>
			<td>
			<select name="category_id" id="category_id">
				<?php foreach($aCategoryList as  $value):?>
					<?php if($category_id == $value['id']):?>
						<option  selected ="selected" value="<?php echo $value['id'];?>"><?php echo $value['title'];?></option>
					<?php else:?>
						<option   value="<?php echo $value['id'];?>"><?php echo $value['title'];?></option>
					<?php endif?>
				<?php endforeach?>
			</select>

			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>知识点： </td>
			<td>
			<select name="knowledge_id" id="knowledge_id">
				<?php foreach($aKnowledgeList as $key => $value):?>
					<?php if($knowledge_id == $key):?>
						<option  selected ="selected" value="<?php echo $key;?>"><?php echo $value;?></option>
					<?php else:?>
						<option   value="<?php echo $key;?>"><?php echo $value;?></option>
					<?php endif?>
				<?php endforeach?>
			</select>

			</td>
		</tr>
		<tr>
			<td class="alt"><font color="red">*</font>难度： </td>
			<td>

				<?php for($i=1;$i<=5;$i++):?>
					<?php if($difficulty == $i):?>
						<input type="radio" name="difficulty" id="difficulty" value="<?php echo $i;?>" checked="checked"><?php echo $i;?>星&nbsp;&nbsp;
					<?php else:?>
						<input type="radio" name="difficulty" id="difficulty" value="<?php echo $i;?>" ><?php echo $i;?>星&nbsp;&nbsp;
					<?php endif?>
				<?php endfor?>


			</td>
		</tr>
		<tr>
			<td class="alt">排序： </td>
			<td>
				<input type="text" name="sort_order" id="sort_order" value="<?php echo $sort_order;?>">
			</td>
		</tr>


			<td class="alt"><font color="red">*</font>是否启用： </td>
			<td>
				<input type="radio" id="status1" name="status" value="1" <?php if($status == 1):?>checked<?php endif;?>>是&nbsp;&nbsp;
				<input type="radio" id="status0" name="status" value="0" <?php if($status == 0):?>checked<?php endif;?>>否
			</td>
		</tr>
		<td class="alt"><font color="red">*</font>问题： </td>
			<td>
				<textarea ondblclick ="javascript:plugin1().openword(0,'dou', '<?php echo $token;?>','question','')" id ='question' name="question" cols="100"  rows="5"><?php echo $question;?></textarea> [<a href="javascript:void(0)"  onclick="review()">Open</a>]
			</td>
		</tr>
		<tr>
			<td class="alt">选项<font color="red">[<a href="javascript:void(0)" onclick="addOpions()">添加选项</a>]</font>：</td>
			<td id="picanswer">

			    <?php foreach($answers as $letter => $value):?>
			    	<p id="<?php echo $letter;?>">
			    	<label><?php echo $letter;?>: </label>
			    	<textarea cols="100"  rows="2" ondblclick="javascript:plugin1().openword(0,'dou', '<?php echo $token;?>','answers<?php echo $letter;?>','')"  id="answers<?php echo $letter;?>" name="answers[<?php echo $letter;?>]"><?php echo $value;?></textarea>

			    	<input name="answer"  type="radio" <?php if($answer == $letter) echo 'checked="checked"';?> value="<?php echo $letter;?>">正确选项<font color="red">[<a href="javascript:void(0)" onclick="delOpions('<?php echo $letter;?>')">删除</a>]</font>
			    	</p>
			    <?php endforeach?>

			</td>

		</tr>
		<tr>
			<td>预览-问题</td>
			<td><span id='view_question'><?php echo $question;?></span></td>
		</tr>
		<tr >
		<td>预览-选项</td>
		<td id="reviewPic">
		  <span id="reviewquestion"></span>
			<?php foreach($answers as $letter => $value):?>
				<font color="red"><?php echo $letter;?>:</font><span id><?php echo $value;?></span>
			<?php endforeach?>
		</td>
		</tr>



		<tr>
			<td class="alt">&nbsp;</td>
			<td>
			   <button type="submit" class="btn" value="Submit">确认提交</button>


			</td>
		</tr>
	</table>
	</form>
	<div id="remind" class="note">
	<div style="color:red">注意事项：</div>
		1. 注意事项<br>
		2. 注意事项；<br>

	</div>
	<br><br><br><br>
</div>
</div>
<div id="dlg" style="padding:5px;width:400px;height:200px;">
    Dialog Content.
</div>
<object id="plugin0" type="application/x-tkbsplugin" width="1" height="1">
    <param name="onload" value="pluginLoaded" />
</object>
<script type="text/javascript">
    var i = <?php echo count($answers);?>;
	function delOpions(str) {
		$('#'+str).remove();
		$('#reviewanswers'+str).remove();
		i = i -1;
		return false;
	}
	function addOpions() {
        var letter = String.fromCharCode((65+i));
		var html = '<p id="'+letter+'"><label>'+letter+': </label><textarea cols="100"  rows="2" ondblclick="javascript:plugin1().openword(0,\'dou\', \'<?php echo $token;?>\',\'answers'+letter+'\',\'\')" id="answers'+letter+'" name="answers[\''+letter+'\']" ></textarea> <input name="answer"  type="radio" value="'+letter+'">正确选项<font color="red">[<a href="javascript:void(0)" onclick="delOpions(\''+letter+'\')">删除</a>]</font></p>';
		$('#picanswer').append(html);
		html = '<span id="reviewanswers'+letter+'"></span>';
		$('#selectPic').append(html);
		i = i+1;
		return false;
	}
	function review(str) {
		//alert(str);

		$('#review'+str).html($('#'+str).val());
	}



</script>
</body>
</html>
