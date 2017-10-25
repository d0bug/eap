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
                	//var html = '正在上传';
                   //document.getElementById(wm.eguid).innerHTML = html;
                   document.getElementById(wm.eguid).value =html;
                   //alert('#'+wm.egui);
                   //$('#'+wm.egui).val(html);
                }

                else if (wm["msgName"] == "refresh")
                    /*document.getElementById(wm.eguid).innerHTML = document.getElementById(wm.eguid).innerHTML;*/
                	var html = delHtmlTag(wm.data,wm.eguid);



                   document.getElementById(wm.eguid).value =html;
					alert('上传成功');
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

</head>
<body onload="load()" >
<div region="center" >
<div id="main">
	<h2>题目</h2>
	<p>





	</p>

	<form id="editForm"  method="POST" enctype="multipart/form-data" action="<?php echo $action;?>">

	<table id ="form" width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">



	<tr><td  colspan="2">
		<input type="radio" value="1" name="nType" onclick="selection(this.value)" <?php if($nType == 1)echo 'checked' ;?>>选择题
	<input type="radio" value="2" name="nType" onclick="selection(this.value)" <?php if($nType == 2)echo 'checked' ;?>>填空题
	<input type="radio" value="3" name="nType" onclick="selection(this.value)" <?php if($nType == 3)echo 'checked' ;?>>简答


	</td></tr>



		<tr>
			<td class="alt">题号： </td>
			<td>

				<input type="text" name="nSort" id="nSort" value="<?php echo $nSort;?>">
				- <input type="text" name="nSubSort" id="nSubSort" value="<?php echo $nSubSort;?>">

				<input type="radio" name="nSign" <?php if($nSign == 1) echo 'checked'; ?> value="1">练习
				<input type="radio" name="nSign" <?php if($nSign == 2) echo 'checked'; ?>  value="2">作业
			</td>
		</tr>


		<tr id="ques">
		<td class="alt"><font color="red">*</font>问题： </td>
			<td>
				<textarea ondblclick ="javascript:plugin1().openword(0,'camp', '<?php echo $token;?>','question','')" id ='question' name="question" cols="100"  rows="5"><?php echo base64_decode($sQuestion['question']);?></textarea> [<a href="javascript:void(0)"  onclick="review()">Open</a>]
			</td>
		</tr>
		<?php if($nType == 1):?>
		<tr id="op">
			<td class="alt">选项<font color="red">[<a href="javascript:void(0)" onclick="addOpions()">添加选项</a>]</font>：</td>
			<td id="picanswer">

			    <?php foreach($sQuestion['answers'] as $letter => $value):?>
			    	<p id="<?php echo chr(65+$letter);?>" class ='letter'>
			    	<label><?php echo chr(65+$letter);?>: </label>
			    	<textarea cols="100"  rows="2" ondblclick="javascript:plugin1().openword(0,'camp', '<?php echo $token;?>','answers<?php echo chr(65+$letter);?>','')"  id="answers<?php echo chr(65+$letter);?>" name="answers[<?php echo $letter;?>]"><?php echo base64_decode($value) ;?></textarea>

			    	<input name="answer"  type="radio" <?php if($sQuestion['answer'] == $letter) echo 'checked="checked"';?> value="<?php echo $letter;?>">正确选项<font color="red">[<a href="javascript:void(0)" onclick="delOpions('<?php echo chr(65+$letter);?>')">删除</a>]</font>
			    	</p>
			    <?php endforeach?>

			</td>

		</tr>
	<?php else:?>
			<tr id="answ">
		<td class="alt"><font color="red">*</font>答案： </td>
			<td>
				<textarea ondblclick ="javascript:plugin1().openword(0,'camp', '<?php echo $token;?>','answer','')" id ='answer' name="answer" cols="100"  rows="5"><?php echo base64_decode($sQuestion['answer']);?></textarea> [<a href="javascript:void(0)"  onclick="review()">Open</a>]
			</td>
		</tr>
	<?php endif?>

		<td class="alt"><font color="red">*</font>解析： </td>
			<td>
				<textarea ondblclick ="javascript:plugin1().openword(0,'camp', '<?php echo $token;?>','sPoint','')" id ='sPoint' name="sPoint" cols="100"  rows="5"><?php echo $sPoint;?></textarea> [<a href="javascript:void(0)"  onclick="review()">Open</a>]
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

		var con  = '';
		var letter = 'Z';
		for(var chr = 0;chr <=8;chr++ ) {
			letter = String.fromCharCode((65+chr));
			con = $('#'+letter).text();
			if(con.length == 0 && chr >1) {
			var html = creatOption(chr);
			$('#'+String.fromCharCode((64+chr))).after(html);
			return false;
			}
			if(con.length == 0 && chr ==1) {
			var html = creatOption(chr);
			$('#'+String.fromCharCode((66+chr))).before(html);
			return false;
			}

		}
		return false;

	}
	function creatOption(chr) {
		var letter = String.fromCharCode((65+chr));
		var html = '<p id="'+letter+'"><label>'+letter+': </label><textarea cols="100"  rows="2" ondblclick="javascript:plugin1().openword(0,\'dou\', \'<?php echo $token;?>\',\'answers'+letter+'\',\'\')" id="answers'+letter+'" name="answers['+chr+']" ></textarea> <input name="answer"  type="radio" value="'+chr+'">正确选项<font color="red">[<a href="javascript:void(0)" onclick="delOpions(\''+letter+'\')">删除</a>]</font></p>';
		return html;
	}
	function review(str) {
		//alert(str);

		$('#review'+str).html($('#'+str).val());
	}


	function selection(value) {
		if(value>1) {
			$('#op').remove();
			$('#answ').remove();
			var html = '<tr id="answ">';
		        html +='<td class="alt"><font color="red">*</font>答案： </td>';
			    html += '<td>';
				html += '<textarea ondblclick ="javascript:plugin1().openword(0,\'camp\', \'<?php echo $token;?>\',\'answer\',\'\')" id =\'answer\' name="answer" cols="100"  rows="5"></textarea> [<a href="javascript:void(0)"  onclick="review()">Open</a>]';
			    html += '</td>';
		        html += '</tr>';
		$('#ques').after(html);
		}
		if(value == 1) {
			$('#op').remove();
			$('#answ').remove();
			var html = '<tr id="op">';
			html += '<td class="alt">选项<font color="red">[<a href="javascript:void(0)" onclick="addOpions()">添加选项</a>]</font>：</td>';
			html += '<td id="picanswer">'
			for(var i =0 ;i<4;i++) {
				html += creatOption(i);
			}


			html += '</td>';
			html += '</tr>';
			//alert(html);

			$('#ques').after(html);

		}
	}



</script>
</body>
</html>
