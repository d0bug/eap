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
var thisurl = "/homework/mid_science/manage";
$(function(){
  chooseYear();
  chooseSemester();
  chooseDept();
  chooseClassType();
  chooseClasslesson();
})
function chooseYear(){
  $(".choose-year").change(function() {
    var year = $(this).val();
    var semesterid = $(".choose-semester").val();
    var deptcode = $(".choose-dept").val();
    window.location.href = thisurl + "/year/" + year + "/semesterid/" + semesterid + "/deptcode/" + deptcode;
  })
}
function chooseSemester(){
  $(".choose-semester").change(function() {
    var year = $(".choose-year").val();
    var deptcode = $(".choose-dept").val();
    var semesterid = $(this).val();
    window.location.href = thisurl + "/year/" + year + "/semesterid/" + semesterid + "/deptcode/" + deptcode;
  })
}
function chooseDept(){
  $(".choose-dept").change(function() {
      var year = $(".choose-year").val();
      var deptcode = $(this).val();
      var semesterid = $(".choose-semester").val();
      window.location.href = thisurl + "/year/" + year + "/semesterid/" + semesterid + "/deptcode/" + deptcode;
  })
}
function chooseClassType(){
  $(".choose-classtype").change(function() {
    var year = $(".choose-year").val();
    var deptcode = $(".choose-dept").val();
    var semesterid = $(".choose-semester").val();
    var classtypecode = $(this).val();
    window.location.href = thisurl + "/year/" + year + "/semesterid/" + semesterid + "/deptcode/" + deptcode + "/classtypecode/" + classtypecode;
  })
}
function chooseClasslesson(){
  $(".choose-classlesson").change(function() {
    var year = $(".choose-year").val();
    var deptcode = $(".choose-dept").val();
    var semesterid = $(".choose-semester").val();
    var classtypecode = $(".choose-classtype").val();
    var classlesson = $(".choose-classlesson").val();
    window.location.href = thisurl + "/year/" + year + "/semesterid/" + semesterid + "/deptcode/" + deptcode + "/classtypecode/" + classtypecode + "/classlesson/" + classlesson;
  })

}
</script>
</head>
<body>
<div class="container">
  <div class="row">
    <div class="col-md-10">
      <h2>管理作业</h2>
      <form class="form-horizontal" role="form" id="addWeixinAccount" action="/weixinautoreply/account/ajax_addWeixinAccount" method="post">
        <div class="form-group">
          <label for="account_id" class="col-md-1 control-label" style="width:100px;">筛选：</label>
          <div class="col-md-1" style="width:135px;">
            <select class="form-control choose-year">
              <option value="2014"<?php if($year == 2014): ?> selected<?php endif; ?>>2014</option>
              <option value="2015"<?php if($year == 2015): ?> selected<?php endif; ?>>2015</option>
            </select>
          </div>
          <div class="col-md-1" style="width:135px;">
            <select class="form-control choose-semester">
            <option value="0">请选择学期</option>
              <?php foreach($xueqiArr as $xueqi): ?>
              <option value="{$xueqi.id}"<?php if($semesterid == $xueqi['id']): ?> selected<?php endif; ?>>{$xueqi.sname}</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-1" style="width:135px;">
            <select class="form-control choose-dept">
            <option value="0">请选择部门</option>
            <?php foreach($deptArr as $dept): ?>
            <option<?php if($deptcode == $dept['sdeptcode']): ?> selected<?php endif; ?> value="{$dept.sdeptcode}">{$dept.sname}</option>
            <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-1" style="width:135px;">
            <select class="form-control choose-classtype"<?php if(empty($classtypeArr)): ?> disabled<?php endif; ?>>
              <option value="0">请选择班型</option>
              <?php foreach($classtypeArr as $banxing): ?>
              <option value="{$banxing.scode}"<?php if($classtypecode == $banxing['scode']): ?> selected<?php endif; ?>>{$banxing.sname}</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-1" style="width:135px;">
            <select class="form-control choose-classlesson"<?php if(empty($jiangciArr)): ?> disabled<?php endif; ?>>
              <option value="0">请选择讲次</option>
              <?php foreach($jiangciArr as $jiangci): ?>
              <option value="{$jiangci}"<?php if($classlesson == $jiangci): ?> selected<?php endif; ?>>{$jiangci}</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-1" style="width:135px;">
            <a href="/homework/mid_science/explodestuscore" class="btn btn-default">导出学员成绩</a>
          </div>
        </div>
        <div class="form-group" style="margin-top: 30px;">
          <label for="account_id" class="col-md-1 control-label" style="width:100px;"></label>
            <?php if(!empty($listArr)): ?>
            <div class="col-md-10">
            <table class="table table-bordered">
              <tr>
                <th>学年</th>
                <th>学期</th>
                <th>学科</th>
                <th>班型</th>
                <th>讲次</th>
                <th>操作</th>
              </tr>
              <?php foreach($listArr as $item): ?>
              <tr>
                <td>{$year}</td>
                <td>{$xueqiArr[$semesterid]['sname']}</td>
                <td>{$deptArr[$deptcode]['sname']}</td>
                <td>{$classtypeArr[$classtypecode]['sname']}</td>
                <td>{$item.lesson_no}</td>
                <td><a href="/homework/mid_science/edit/main_subject_id/{$item.id}">编辑</a></td>
              </tr>
              <?php endforeach; ?>
            </table>
            </div>
          <?php else: ?>
          <div class="col-md-10" style="background-color: #66CCFF; border: 1px solid #6699FF; color: #ffffff; font-size: 16px;">
            <div style="width: 200px; margin: 80px auto;">当前筛选条件下没有结果。</div>
          </div>
          <?php endif; ?>
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