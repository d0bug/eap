<!doctype html>
<html>
    <head>

        <!-- 新 Bootstrap 核心 CSS 文件 -->
<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap.min.css">

<!-- 可选的Bootstrap主题文件（一般不用引入） -->
<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
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

                //alert(wm.data);
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
					//alert(str);

                 	//return str.replace(/<[^s]|[^>]+>/g,"");//去掉所有的html标记
                 	str = str.replace(/(<\/?(?!img|br)[^>\/]*)\/?>/gi,'');
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

    <body  onload="load()">
    <div class="container">
      <h2><?php echo $lessonInfo['sname'];?><small><?php echo $lessonInfo['classtype_code'];?></small>第<?php echo $lessonInfo['lesson_no'];?>讲<?php echo $lessonInfo['classyear'],'-',seasonName($lessonInfo['semester_id']);?></h2>
      <hr/>
      <div class="btn-group">
      			<?php foreach($actionList as $key =>$value):?>
				<a class="btn btn-default " data-toggle="modal" href="<?php echo U('/Homework/MidScience/addQuestion',array('main_subject_id'=>$main_subject_id,'id'=>$key),'');?>" data-target="#modal">
				  <span class="glyphicon glyphicon glyphicon glyphicon-plus"></span> <?php echo $value;?>
				</a>
				<?php endforeach?>

			  </div>
		<hr/>

      <?php foreach($questionList as $value):?>
			<div class="panel panel-primary">
			  <div class="panel-heading">

			  <a href="javascript:void(0)" class="btn btn-danger"><?php echo $value['subject_no'];?></a>
			  <div class="btn-group">
				<a class="btn btn-default " data-toggle="modal" href="<?php echo U('/Homework/MidScience/editQuestion',array('id'=>$value['id']),'');?>" data-target="#modal">
				  <span class="glyphicon glyphicon glyphicon-pencil"></span> 编辑
				</a>
				<button type="button" class="btn btn-default " onclick="deleteQuestion(<?php echo $value['id'];?>)">
				  <span class="glyphicon glyphicon-trash"></span> 删除
				</button>
			  </div>


			  </div>
			  <div class="panel-body">
			    <?php if(empty($value['aQuestion'])){
			    echo '该题目为空';
			    echo
			   '</div>
			</div>';

			  	continue;
			  }?>





                    <div class="panel panel-default">
                      <div class="panel-heading">
                        <h3 class="panel-title">题目</h3>
                      </div>
                      <div class="panel-body">
                        <?php echo $value['aQuestion']['question'];?>
                      </div>
                    </div>









                <?php if(isset($value['aQuestion']['options'])):?>
			  <ul class="list-group ">

			  <?php foreach($value['aQuestion']['options'] as $k=>$v):?>

			  	<li class="list-group-item"><span class="label <?php if(in_array($k, explode('|', $value['corrent_answer']))) {echo 'label-success';}else { echo 'label-default';}?>"><?php echo chr(65+$k);?></span>  <small><?php echo $v;?></small></li>
			  <?php endforeach?>
			  </ul>
            <?php else:?>
                <h3><small>答案</small></h3>
                 <div class="jumbotron">

                  <p>
                    <?php echo $value['corrent_answer'];?>
                  </p>
                </div>
            <?php endif?>

			  <h3><small>解析</small></h3>
				 <div class="jumbotron">

				  <p>
				    <?php echo $value['aQuestion']['point'];?>
				  </p>
				</div>
			  </div>
			</div>

	<?php endforeach?>




    </div>

    <!-- Modal -->
    <!--
 <div id='modal' class="modal fade bs-example-modal-lg"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-body"></div>
 </div>
-->
<!-- Large modal -->


<div id='modal' class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

    </div>
  </div>
</div>
<object id="plugin0" type="application/x-tkbsplugin" width="1" height="1">
    <param name="onload" value="pluginLoaded" />
</object>

<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>

<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="http://cdn.bootcss.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<script type="text/javascript">




    $('.modal').on('hidden.bs.modal',function(e){

		$(this).removeData('bs.modal');

});
    function edit(id) {
    	alert(id);
    	return false;
    }
    function del(id) {
    	alert(id);
    	return false;
    }
    function sub(id) {
        if(confirm('确定提交')) {
            $.ajax({
             type: "post",
             url: $('#url').val(),
             dataType: "json",
             data:$('#formq').serialize(),
             async:false,
             success: function(data){
                      //return false;
             			if(data['error']  == 0 ) {
             				 alert(data['msg']);
                            location.reload();
                            return false;
                        }

                        alert(data['msg']);

                        return false;
                    }


            });

        }
        return false;

    }

   function deleteQuestion(id) {
   			 if(confirm('你确定要删除该问题吗')) {
            $.ajax({
             type: "post",
             url: '/Homework/MidScience/deleteQuestion',
             dataType: "json",
             data:{id:id},
             async:false,
             success: function(data){
             	      //return false;
             			if(data['error']  == 0 ) {
             				 alert(data['msg']);
                            location.reload();
                            return false;
                        }

                        alert(data['msg']);

                        return false;
                    }


            });

        }
        return false;
   }
   function addOpions(type) {

		var con  = '';
		var letter = 'Z';
		for(var chr = 0;chr <=8;chr++ ) {
			letter = String.fromCharCode((65+chr));
			con = $('#'+letter).text();
			//alert(con.length);return false;
			if(con.length == 0 && chr >0) {
			var html = creatOption(chr,type);
			$('#'+String.fromCharCode((64+chr))).after(html);
			return false;
			}
			if(con.length == 0 && chr ==0) {
			var html = creatOption(chr,type);
			$('#option0').after(html);
			return false;
			}

		}
		return false;

	}
	function removeOption(letter) {
		$('#'+letter).remove();
		return false;

	}
	function creatOption(chr,type) {
		if(type == 1) {
			var input = 'radio';
			var inputname = 'corrent_answer';
		} else {
			var input = 'checkbox';
			var inputname = 'corrent_answer[]';
		}
		var timestamp = Date.parse(new Date());
		timestamp = timestamp/1000;

		var letter = String.fromCharCode((65+chr));
		var html = '<div class="form-group" id="'+letter+'"><label for="inputPassword" class="col-sm-2 control-label">'+letter+' <input type="'+input+'" name="'+inputname+'" value="'+chr+'"><a href="javascript:void(0)" onclick="removeOption(\''+letter+'\')"><span class="glyphicon glyphicon glyphicon-trash"></span></a></label><div class="col-sm-10"><textarea  ondblclick ="javascript:plugin1().openword(0,\'middcience\', \''+timestamp+'\',\'option'+letter+'\',\'\')" id="option'+letter+'"  name="options['+chr+']" class="form-control" rows="1"></textarea></div></div>';
		/*var html = '<p id="'+letter+'"><label>'+letter+': </label><textarea cols="100"  rows="2" ondblclick="javascript:plugin1().openword(0,\'dou\', \'<?php echo $token;?>\',\'answers'+letter+'\',\'\')" id="answers'+letter+'" name="answers['+chr+']" ></textarea> <input name="answer"  type="radio" value="'+chr+'">正确选项<font color="red">[<a href="javascript:void(0)" onclick="delOpions(\''+letter+'\')">删除</a>]</font></p>';*/
		return html;
	}
</script>
    </body>





</html>
