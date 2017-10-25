<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/bootstrap3/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/static/js/jgrowl/jquery.jgrowl.min.js"></script>
<link href="/static/bootstrap3/css/bootstrap.min.css" type="text/css" rel="stylesheet" />
<link href="/static/js/jgrowl/jquery.jgrowl.custom.css" rel="stylesheet" />
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
</style>
<script type="text/javascript">
jQuery(function($) {
	$("#addHomework").submit(function() {
		var score = $("#score").val().replace(/^(\s|\u00A0)+/,'').replace(/(\s|\u00A0)+$/,'');;
		var qindex = $("#qindex").val().replace(/^(\s|\u00A0)+/,'').replace(/(\s|\u00A0)+$/,'');;
		if(score == '' || qindex == ''){
			failedTip('请完整填写');
			return false;
		}
	})
})
  function successTip(msg){
	$('#succeed').jGrowl(msg, { life: 3600000, closeTemplate: '' });
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
      <h2>{$info.classname}-第{$info.explainno}讲</h2>
      <form class="form-horizontal" id="addHomework" action="" method="POST">
      	<input type="hidden" name="infoid" value="<?php echo $_GET['infoid']; ?>"/>
        <div class="form-group subject-container">
          <label for="account_id" class="col-md-1 control-label" style="width:110px;">添加口述：</label>
          <div style="margin: 0 0 20px 110px; display: table;" class="subject-row">
            <div class="col-md-1" style="width: 60px;">
              <p class="form-control-static">题号</p>
            </div>
            <div class="col-md-1" style="width:100px;">
              <input type="number" name="qindex" id="qindex" class="form-control subject-no" placeholder="题号">
            </div>
            <div class="col-md-1" style="width: 60px;">
              <p class="form-control-static">满分</p>
            </div>
            <div class="col-md-1" style="width:100px;">
              <input type="number" name="score" id="score" class="form-control subject-score" placeholder="满分">
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-offset-1 col-md-1" style="margin-left:110px;">
            <button id="sub" type="submit" class="btn btn-success btn-lg btn-addHomework">提交</button>
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