<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/bootstrap3/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/static/js/jgrowl/jquery.jgrowl.min.js"></script>
<link href="/static/bootstrap3/css/bootstrap.min.css" type="text/css" rel="stylesheet" />
<link href="/static/js/jgrowl/jquery.jgrowl.custom.css" rel="stylesheet" />

<script type="text/javascript" src="/static/bootstrap3/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="/static/bootstrap3/js/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
<link href="/static/bootstrap3/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">

<script>
jQuery(function($) {
	$("#sub-hw").live('click', function(e) {
		var startTime = $("#startTime").val();
		var endTime = $("#endTime").val();
		var mobile = $("#mobile").val();
		if(startTime == ''){
			alert('请选择使用时间');
			return false;
		}else if(endTime == ''){
			alert('请选择归还时间');
			return false;
		}else if(mobile == 0){
			alert('请选择要借用的手机型号');
			return false;
		}else{
			$("#addApply").submit();
		}
	})
})
</script>
</head>
<body>
<div class="container">
  <div class="row">
    <div class="col-md-10">
      <h2>测试机申请</h2>
      <form class="form-horizontal" role="form" id="addApply" action="" method="post">
        <div class="form-group">
          <label for="account_id" class="col-md-1 control-label" style="width:120px;">使用时间：</label>
          		<div class="input-group date form_datetime col-md-5" data-date="1979-09-16T05:25:07Z" data-date-format="yyyy-MM-dd hh:ii" data-link-field="dtp_input1">
                    <input class="form-control" name="startTime" id="startTime" size="16" type="text" value="" readonly>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
					<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                </div>
        </div>
        <div class="form-group">
          <label for="account_id" class="col-md-1 control-label" style="width:120px;">归还时间：</label>
          <div class="input-group date form_datetime col-md-5" data-date="1979-09-16T05:25:07Z" data-date-format="yyyy-MM-dd hh:ii" data-link-field="dtp_input1">
                    <input class="form-control" name="endTime" id="endTime" size="16" type="text" value="" readonly>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
					<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                </div>
        </div>
        
        <div class="form-group">
          <label for="script_name" class="col-md-1 control-label" style="width:120px;">选择手机：</label>
          <div class="col-md-1" style="width:270px;">
            <select class="form-control choose-classtype" name="mobile" id="mobile">
              <option value="0">请选择手机型号</option>
              <?php foreach($mobileType as $one): ?>
              <option value="{$one.id}">{$one.sname}</option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        
        <div class="form-group">
          <label for="script_name" class="col-md-1 control-label" style="width:120px;">备注信息：</label>
          <div class="col-md-1" style="width:200px;">
            <TEXTAREA rows="5" cols="50" name="contents"></TEXTAREA>
          </div>
        </div>
        
        <div class="form-group">
          <div class="col-md-offset-1 col-md-1" style="margin-left:120px;">
            <button type="submit" class="btn btn-success btn-lg" id="sub-hw">提交</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
    $('.form_datetime').datetimepicker({
        language:  'zh-CN',
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		forceParse: 0,
        showMeridian: 1
    });
	$('.form_date').datetimepicker({
        language:  'zh-CN',
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		minView: 2,
		forceParse: 0
    });
	$('.form_time').datetimepicker({
        language:  'zh-CN',
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 1,
		minView: 0,
		maxView: 1,
		forceParse: 0
    });
</script>
</body>
</html>