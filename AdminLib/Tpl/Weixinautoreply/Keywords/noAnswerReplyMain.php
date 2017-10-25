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
<script type="text/javascript" src="/static/chosen/chosen.jquery.min.js"></script>
<link href="/static/bootstrap3/css/bootstrap.min.css" type="text/css" rel="stylesheet" />
<link href="/static/chosen/chosen.min.css" type="text/css" rel="stylesheet" />
<style type="text/css">
body, h1, h2, h3, h4 {
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
	display: none;
	-moz-user-select: none;
	background-image: none;
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
.panel-group {
	margin-top: 20px;
}
.modal-dialog {
	margin: 90px auto 30px 280px;
}
.modal-footer {
	margin-top: 0;
}
.chosen-container-multi .chosen-choices li.search-field input[type="text"] {
	height: 25px;
}
</style>
<script type="text/javascript">
$(function(){
  $(".account_id").chosen({width:"100%",height:"30px"});
  $(".sub-addNoAnswerReply").click(function(){
    $("#addNoAnswerReply").submit();
  })
  $("#addNoAnswerReply").validate({
    rules: {
      reply_content: {
        required: true,
        minlength: 1
      }
    },
    messages: {
      reply_content: {
        required: '必须填写回复内容。',
        minlength: '回复内容少于1个字符。'
      }
    },
    submitHandler: function(form) {
      $(form).ajaxSubmit({
        dataType: "json",
        success:  function(responseData){
      if(responseData.status==1){
        window.location.reload();
      }else{
        $("#msg").html(responseData.info).show();
      }
        }
      });
    }
  })

  $(".sub-del").bind('click', function(event){
    event.preventDefault();
    var nodeItem = $(this).parents(".node-item");
    var account_id = nodeItem.find("input[name=account_id]").val();
    $.post('/weixinautoreply/Keywords/ajax_delnoAnswerReply', {account_id: account_id}, function(responseData) {
      if(responseData.status==1){
        nodeItem.slideUp(300, function() {
          nodeItem.remove();
        });
      }else{
        var msgObj = nodeItem.find(".msg").html(responseData.info);
        msgObj.fadeOut(function() {
          msgObj.fadeIn();
        });
      }
    }, 'json')
  })

  $(".sub-update").bind('click', function(event){
    event.preventDefault();
    var editForm = $(this).parents(".cls-editNoAnswerReply");
    editForm.validate({
      rules: {
        reply_content: {
          required: true,
          minlength: 1
        }
      },
      messages: {
        reply_content: {
          required: '必须填写回复内容。',
          minlength: '回复内容少于1个字符。'
        }
      },
      submitHandler: function(form) {
        $(form).ajaxSubmit({
          dataType: "json",
          success:  function(responseData){
            if(responseData.status==1){
              var msgObj = $(form).find(".msg").html(responseData.info);
              msgObj.fadeOut(function() {
                msgObj.fadeIn();
              });
            }else{
              var msgObj = $(form).find(".msg").html(responseData.info);
              msgObj.fadeOut(function() {
                msgObj.fadeIn();
              });
            }
          }
        });
      }
    })
    editForm.submit();
  })

})
</script>
</head>
<body>
<div class="container">
  <div class="row">
    <div class="col-md-10">
      <h2>无匹配关键词的消息自动回复规则</h2>
      <button class="btn btn-default" data-toggle="modal" data-backdrop="false" data-keyboard="false" data-target="#editNoAnswerReplyModal">新增回复</button>
      <?php if($arrNoAnswerReply): ?>
      <div class="panel-group" id="accordion">
        <?php foreach ($arrNoAnswerReply as $i => $item): ?>
        <div class="panel panel-default node-item">
          <div class="panel-heading">
            <h4 class="panel-title"> <a data-toggle="collapse" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $i; ?>"> <span style="font-size: 12px; font-weight: normal; color: #888888;"></span> <?php echo $item['account_name']; ?> </a> </h4>
          </div>
          <div id="collapse<?php echo $i; ?>" class="panel-collapse collapse">
            <div class="panel-body">
              <form class="form-horizontal cls-editNoAnswerReply" role="form" action="/weixinautoreply/Keywords/ajax_editNoAnswerReply" method="post">
                <div class="form-group">
                  <label for="name" class="col-md-1 control-label">微信号</label>
                  <div class="col-md-5">
                    <label for="name" class="control-label"><?php echo $item['account_name']; ?></label>
                  </div>
                </div>
                <div class="form-group">
                  <label for="reply_content" class="col-md-1 control-label">回复</label>
                  <div class="col-md-5">
                    <textarea class="form-control" rows="3" id="reply_content" name="reply_content" placeholder="回复 "><?php echo $item['reply_content']; ?></textarea>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-offset-1 col-md-5 msg failed"></div>
                </div>
                <div class="form-group">
                  <div class="col-md-offset-1 col-md-5">
                    <input type="hidden" name="account_id" value="<?php echo $item['account_id']; ?>" />
                    <button class="btn btn-default sub-del" type="button">删除</button>
                    <button class="btn btn-success sub-update" type="submit" style="margin-left:8px;">保存</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<div class="modal fade" id="editNoAnswerReplyModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">添加规则...</h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" role="form" id="addNoAnswerReply" action="/weixinautoreply/Keywords/ajax_addNoAnswerReply" method="post">
          <input type="hidden" name="content_type" value="1" />
          <div class="form-group">
            <label for="name" class="col-md-2 control-label">微信号</label>
            <div class="col-md-7">
              <select class="chosen-select account_id" name="account_id[]" data-placeholder="请选择微信号" multiple>
                <?php foreach($arrWeixinAccount as $accountItem): ?>
                <option value="<?php echo $accountItem['account_id']; ?>"><?php echo $accountItem['account_name']; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="reply_content" class="col-md-2 control-label">文字</label>
            <div class="col-md-7">
              <textarea class="form-control" rows="3" id="reply_content" name="reply_content" placeholder="回复文本"><?php echo $item['reply_content']; ?></textarea>
            </div>
          </div>
          <div class="form-group">
            <div id="msg" class="col-md-offset-2 col-md-4 msg failed"></div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary sub-addNoAnswerReply">保存</button>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
  <!-- /.modal-dialog --> 
</div>
<!-- /.modal -->
</body>
</html>