<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/bootstrap3/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/static/js/jgrowl/jquery.jgrowl.min.js"></script>
<script type="text/javascript" src="/static/js/uploadify/jquery.uploadify.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.form.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.json-2.4.js"></script>
<link href="/static/bootstrap3/css/bootstrap.min.css" type="text/css" rel="stylesheet" />
<link href="/static/js/jgrowl/jquery.jgrowl.custom.css" rel="stylesheet" />
<link href="/static/js/uploadify/uploadify.css" type="text/css" rel="stylesheet" />
<style type="text/css">
body, h1, h2 {
	font-family: "Microsoft Yahei";
}
@media (min-width: 600px) {
.container {
	width: 95%;
}
}
h2 {
	border-bottom: 3px solid #EEEEEE;
	color: #575765;
	font-size: 24px;
	margin: 40px 0;
	padding-bottom: 10px;
}
#choose-pic .btn {
	padding: 0;
}
.uploads-container {
	display: none;
}
.uploads-container div {
	position:relative;
	display: inline-block;
	background-color: #cccccc;
	padding: 15px;
	margin: 0 10px 10px 0;
}
.uploads-container div img {
	max-width: 370px;
	max-height: 370px;
}
.uploads-container div button.close {
	position: absolute;
	top: 15px;
	right: 15px;
	width: 20px;
	height: 20px;
	padding: 2px;
	background: #003366;
	color: #FFF;
	font-size: 12px;
	opacity: 1;
}
</style>
<script type="text/javascript">
  $(function(){
  		<?php $timestamp = time(); ?>
    	$("#choose-pic").uploadify({
    		'buttonText' : '选择文件',
    		'buttonClass' : 'btn btn-success',
    		'width'			: '82',
    		'height'		: '34',
			'swf'      : '/static/js/uploadify/uploadify.swf',
			'uploader' : '/homework/mid_science/uploadSsolutionPics',
			'fileTypeDesc' : 'Image Files',
			'fileTypeExts' : '*.gif; *.jpg; *.png; *.jpeg',
			'multi'    : true,
			'fileSizeLimit' : '5MB',
			'formData'     : {
				'timestamp'	:	'{$timestamp}',
				'token'	:	'<php>echo md5($timestamp.$uploadSecretStr);</php>'
			},
			'onUploadSuccess' : function(file, data, response) {
				var obj = $.parseJSON(data);
				if(obj.status==1){
					var pic = '<div><button type="button" class="close" onclick="delOnePic(this)">X</button><img src="/upload/temp/solution_'+obj.data.savename+'" /></div>';
					$(".uploads-container").prepend(pic);
					$(".uploads-container").show();
				}else{
					failedTip(obj.info);
				}
			}
		});
    	addOneSubject();
    	delOneSubject();
		  subForm();
  })
  function addOneSubject(){
  	var subjectTmp = '<div style="margin: 0 0 20px 110px; display: table;" class="subject-row"><div class="col-md-1" style="width: 60px;"><p class="form-control-static">题号</p></div><div class="col-md-1" style="width:100px;"><input type="number" class="form-control subject-no" placeholder="题号"></div><div class="col-md-1" style="width: 60px;"><p class="form-control-static">满分</p></div><div class="col-md-1" style="width:100px;"><input type="number" class="form-control subject-score" placeholder="满分"></div> <div class="col-md-1" style="width: 60px;"><p class="form-control-static">题型</p></div><div class="col-md-1" style="width:120px;"><select class="form-control subject-type"><option value="1" selected="selected"> 单选题 </option><option value="2"> 多选题 </option><option value="3"> 主观题 </option></select></div><div class="col-md-1" style="width:100px;"><button type="button" class="btn btn-danger del-subject">删除此题</button></div></div>';
	$(".add-subject").live('click', function() {
    	$(".subject-container").append(subjectTmp);
  	});
  }
  function delOneSubject(){
	$(".del-subject").live('click', function() {
  		$(this).parents(".subject-row").fadeOut("fast",function(){
			$(this).remove();
		});
  	})
  }
  function delOnePic(e){
		$(e).parent().fadeOut("fast",function(){
			$(this).remove();
		});
  }
  function subForm(){
    $("#addHomework").submit(function() {
      $(this).ajaxSubmit({
        dataType: "json",
        beforeSubmit: function(){
          if(checkUploadPics() && checkSubjects()){
            return true;
          }
          return false;
        },
        data: {"upload_pics":function(){
          return getUploadPics();
        },"subjects":function(){
          return getSubjects();
        },"form-classtype": '{$classtypecode}',"form-classlesson":'{$classlesson}',"form-semester":'{$semesterid}'},
        success: function(responseData){
          if(responseData.status==1){

            return false;




            successTip(responseData.info);
            $(".btn-addHomework").attr("disabled", "true");
          }else{
            failedTip(responseData.info);
          }
        }
      })
      return false;
    })
  }
  function checkUploadPics(){
    if($(".uploads-container").html() == ''){
      failedTip('作业解答不能为空。');
      return false;
    }
    return true;
  }
  function checkSubjects(){
    var status = 1;
    $(".subject-row").each(function(){
      var subject_no = $(this).find(".subject-no").val();
      var subject_score = $(this).find(".subject-score").val();
      var subject_type = $(this).find(".subject-type").val();
      if(subject_no.length == 0 || subject_no <= 0 || subject_score.length == 0 || subject_score <= 0 || subject_type.length == 0 || subject_type <= 0){
        status = 0;
      }
    })
    if(status == 0){
      failedTip('已添加的题目不能小于等于0或者空。');
      return false;
    }
    return true;
  }
  function getUploadPics(){
    var pics_arr = new Array();
    if($(".uploads-container").html() != ''){
      $(".uploads-container").find("div").find("img").each(function(){
        if($(this).attr("src") != ''){
          pics_arr.push($(this).attr("src"));
        }
      })
    }
    return pics_arr.join(',');
  }
  function getSubjects(){
    var subjects = new Array();
    $(".subject-row").each(function(){
      var subject_no = $(this).find(".subject-no").val();
      var subject_score = $(this).find(".subject-score").val();
      var subject_type = $(this).find(".subject-type").val();
      var onesubject = new Array();
      onesubject.push(subject_no, subject_score,subject_type);
      subjects.push(onesubject);
    })
    return $.toJSON(subjects);
  }
  function successTip(msg){
	$('#succeed').jGrowl(msg, { life: 3600000, closeTemplate: '' });

  location.href='/homework/mid_science/lessonList';
  }
  function failedTip(msg){
	$('#failed').jGrowl(msg, { life: 1000, closeTemplate: '' });
  }
</script>
</head>
<body>
<div class="container">
  <div class="row">
    <div class="col-md-10">
      <h2>{$classtypeArr.sname}第{$classlesson}讲</h2>
      <form class="form-horizontal" role="form" id="addHomework" action="/homework/mid_science/create_result" method="post">
        <div class="form-group">
          <label for="account_id" class="col-md-1 control-label" style="width:110px;">作业解答：</label>
          <div class="col-md-10">
            <button type="button" id="choose-pic" class="btn btn-success">选择文件</button>
            <div id="queue"></div>
            <div class="uploads-container"></div>
          </div>
        </div>
        <div class="form-group subject-container">
          <label for="account_id" class="col-md-1 control-label" style="width:110px;">添加题目：</label>
          <div style="margin: 0 0 20px 110px; display: table;" class="subject-row">
            <div class="col-md-1" style="width: 60px;">
              <p class="form-control-static">题号</p>
            </div>
            <div class="col-md-1" style="width:100px;">
              <input type="number" class="form-control subject-no" placeholder="题号">
            </div>
            <div class="col-md-1" style="width: 60px;">
              <p class="form-control-static">满分</p>
            </div>
            <div class="col-md-1" style="width:100px;">
              <input type="number" class="form-control subject-score" placeholder="满分">
            </div>
            <div class="col-md-1" style="width: 60px;">
              <p class="form-control-static">题型</p>
            </div>
            <div class="col-md-1" style="width:120px;">

              <select class="form-control subject-type">
              	<option value="1" selected="selected"> 单选题 </option>
              	<option value="2"> 多选题 </option>
              	<option value="3"> 主观题 </option>
              </select>
            </div>
            <div class="col-md-1" style="width:100px;">
              <button type="button" class="btn btn-success add-subject">添加题目</button>
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-offset-1 col-md-1" style="margin-left:110px;">
            <button type="submit" class="btn btn-success btn-lg btn-addHomework">提交</button>
          </div>
        </div>
        <div class="form-group">
          <div id="msg" class="col-md-offset-1 col-md-4 msg failed"></div>
        </div>
      </form>
    </div>
  </div>
</div>
<div id="succeed" class="jGrowl top-center" style="background-color:#009900; color:#FFF;"></div>
<div id="failed" class="jGrowl top-center" style="background-color:#F60; color:#FFF;"></div>
</body>
</html>
