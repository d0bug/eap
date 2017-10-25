<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/bootstrap3/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.form.min.js"></script>
<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<link href="/static/bootstrap3/css/bootstrap.min.css" type="text/css" rel="stylesheet" />
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
	color: #575765; /*317EAC 575765*/
	font-size: 24px;
	margin: 40px 0;
	padding-bottom: 10px;
}
.error {
	line-height: 2;
	color: #DD4B39;
}
.page {
	margin: 10px 0 20px 0;
}
.page a, .page span {
	-moz-user-select: none;
	background-image: none;
	border: 1px solid rgba(0, 0, 0, 0);
	border-radius: 4px;
	cursor: pointer;
	display: inline-block;
	font-size: 14px;
	font-weight: normal;
	line-height: 1.42857;
	margin: 0 1.5px;
	padding: 4px 8px;
	text-align: center;
	vertical-align: middle;
	white-space: nowrap;
}
.page a {
	background-color: #5BC0DE;
	border-color: #46B8DA;
	color: #FFFFFF;
}
.page span.current {
	background-color: #ffffff;
	border-color: #46B8DA;
	color: #000000;
}
#msg {
	display: none;
}
.msg {
	-moz-user-select: none;
	background-image: none;
	display: inline-block;
	font-size: 14px;
	font-weight: bold;
	line-height: 1.42857;
	margin-bottom: 0;
	padding: 6px 12px;
	vertical-align: middle;
	white-space: nowrap;
}
.success {
	color: #009966;
}
.failed {
	color: #dd4b39;
}
</style>
<script type="text/javascript">
  $(function(){
    $("#addWeixinAccount").validate({
      rules: {
        account_id: {
          required: true,
          minlength: 2
        },
        account_name: {
          required: true,
          minlength: 2
        },
        script_name: {
          required: true,
          minlength: 5
        }
      },
      messages: {
        account_id: {
          required: '必须添写微信号。',
          minlength: '微信号少于2个字符。'
        },
        account_name: {
          required: '必须添写微信名称。',
          minlength: '微信名称少于2个字符。'
        },
        script_name: {
          required: '必须添写脚本名称。',
          minlength: '脚本名称少于5个字符。'
        }
      },
      submitHandler: function(form) {
        $(form).ajaxSubmit({
          dataType: "json",
          resetForm: true,
          success:	function(responseData){
			if(responseData.status==1){
				window.location.reload();
			}else{
				$("#msg").html(responseData.info).show();
			}
          }
        });
      }
    })

  })
</script>
</head>
<body>
<div class="container">
  <div class="row">
    <?php if(!empty($arrWeixinAccount)): ?>
    <div class="col-md-10">
      <h2>微信列表</h2>
      <div class="col-md-8">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>微信号</th>
              <th>微信名称</th>
              <th>脚本位置</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($arrWeixinAccount as $k => $v): ?>
            <tr>
              <td><?php echo $v['account_id']; ?></td>
              <td><?php echo $v['account_name']; ?></td>
              <td><?php echo $v['script_name']; ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <div class="page"> <?php echo $pageshow; ?> </div>
      </div>
    </div>
    <?php endif; ?>
    <div class="col-md-10">
      <h2>添加微信号</h2>
      <form class="form-horizontal" role="form" id="addWeixinAccount" action="/weixinautoreply/account/ajax_addWeixinAccount" method="post">
        <div class="form-group">
          <label for="account_id" class="col-md-1 control-label">微信号</label>
          <div class="col-md-3">
            <input type="text" class="form-control" id="account_id" name="account_id" placeholder="微信号">
          </div>
        </div>
        <div class="form-group">
          <label for="account_name" class="col-md-1 control-label">微信名称</label>
          <div class="col-md-3">
            <input type="text" class="form-control" id="account_name" name="account_name" placeholder="微信名称">
          </div>
        </div>
        <div class="form-group">
          <label for="script_name" class="col-md-1 control-label">脚本名称</label>
          <div class="col-md-3">
            <input type="text" class="form-control" id="script_name" name="script_name" placeholder="脚本名称">
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-offset-1 col-md-1">
            <button type="submit" class="btn btn-default">添加</button>
          </div>
        </div>
        <div class="form-group">
          <div id="msg" class="col-md-offset-1 col-md-4 msg failed"></div>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>