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
var thisurl = "/homework/mid_science/create";
$(function(){
	chooseDept();
	chooseSemester();
	chooseClassType();
	$("#sub-hw").click(function(e) {
		e.preventDefault();
		var classtypecode = $(".choose-classtype").val();
		var classlesson = $(".choose-classlesson").val();
    var semester = $(".choose-semester").val();

		if (typeof(classtypecode) == "undefined" || classtypecode == '0') {
			failedTip('还未选择班型');
			return false;
		}
		if (typeof(classlesson) == "undefined" || classlesson == '0') {
			failedTip('还未选择讲次');
			return false;
		}
		$("#addHomework").find("input[name=form-classtype]").val(classtypecode);
		$("#addHomework").find("input[name=form-classlesson]").val(classlesson);
    $("#addHomework").find("input[name=form-semester]").val(semester);
		$("#addHomework").submit();
	})
})
function chooseDept(){
	$(".choose-dept").find("button").click(function() {
		if ( $(this).hasClass("btn-default") ){
			$(this).removeClass('btn-default');
			$(this).addClass('btn-success');
			$(this).siblings("button").each(function(){
				if ($(this).hasClass('btn-success') && !$(this).hasClass('btn-default')) {
					$(this).addClass('btn-default');
					$(this).removeClass('btn-success');
				}
			})
			var deptcode = $(this).val();
			var semesterid = $(".choose-semester").val();
			window.location.href = thisurl + "/deptcode/" + deptcode + "/semesterid/" + semesterid;
		}
	})
}
function chooseSemester(){
	$(".choose-semester").change(function() {
		var deptcode = $(".choose-dept").find("button.btn-success").val();
		var semesterid = $(this).val();
		window.location.href = thisurl + "/deptcode/" + deptcode + "/semesterid/" + semesterid;
	})
}
function chooseClassType(){
	$(".choose-classtype").change(function() {
		var deptcode = $(".choose-dept").find("button.btn-success").val();
		var semesterid = $(".choose-semester").val();
		var classtypecode = $(this).val();
		window.location.href = thisurl + "/deptcode/" + deptcode + "/semesterid/" + semesterid + "/classtypecode/" + classtypecode;
	})
}


function successTip(msg){
	$('#succeed').jGrowl(msg, { life: 1000, closeTemplate: '' });
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
      <h2>新建作业</h2>
      <form class="form-horizontal" role="form" id="addHomework" action="/homework/mid_science/create_result" method="post">
        <div class="form-group choose-dept">
          <label for="account_id" class="col-md-1 control-label" style="width:110px;">选择部门：</label>
          <div class="col-md-8">
            <?php foreach($deptArr as $dept): ?>
            <button type="button" class="btn <?php if($deptcode == $dept['sdeptcode']): ?>btn-success<?php else: ?>btn-default<?php endif; ?>" value="{$dept.sdeptcode}">{$dept.sname}</button>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="form-group">
          <label for="account_id" class="col-md-1 control-label" style="width:110px;">选择学期：</label>
          <div class="col-md-1" style="width: 80px;">
            <p class="form-control-static"><?php echo date('Y'); ?>年</p>
          </div>
          <div class="col-md-1" style="width:120px;">
            <select class="form-control choose-semester">
              <?php foreach($xueqiArr as $xueqi): ?>
              <option value="{$xueqi.id}"<?php if($semesterid == $xueqi['id']): ?> selected<?php endif; ?>>{$xueqi.sname}</option>
              <?php endforeach; ?>
            </select>
            <input type="hidden" name="form-semester" value="" />
          </div>
        </div>
        <div class="form-group">
          <label for="script_name" class="col-md-1 control-label" style="width:110px;">选择班型：</label>
          <div class="col-md-1" style="width:240px;">
            <select class="form-control choose-classtype"<?php if(empty($classtypeArr)): ?> disabled<?php endif; ?>>
              <option value="0">请选择班型</option>
              <?php foreach($classtypeArr as $banxing): ?>
              <option value="{$banxing.scode}"<?php if($classtypecode == $banxing['scode']): ?> selected<?php endif; ?>>{$banxing.sname}</option>
              <?php endforeach; ?>
            </select>
            <input type="hidden" name="form-classtype" value="" />
          </div>
        </div>
        <div class="form-group">
          <label for="script_name" class="col-md-1 control-label" style="width:110px;">选择讲次：</label>
          <div class="col-md-1" style="width:240px;">
            <select class="form-control choose-classlesson"<?php if(empty($jiangciArr)): ?> disabled<?php endif; ?>>
              <option value="0">请选择讲次</option>
              <?php foreach($jiangciArr as $jiangci): ?>
              <option value="{$jiangci}">{$jiangci}</option>
              <?php endforeach; ?>
            </select>
            <input type="hidden" name="form-classlesson" value="" />
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-offset-1 col-md-1" style="margin-left:110px;">
            <button type="submit" class="btn btn-success btn-lg" id="sub-hw">提交</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<div id="succeed" class="jGrowl top-center" style="background-color:#009900; color:#FFF;"></div>
<div id="failed" class="jGrowl top-center" style="background-color:#F60; color:#FFF;"></div>
</body>
</html>
