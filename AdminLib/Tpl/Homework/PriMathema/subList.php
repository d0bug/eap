<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/bootstrap3/js/bootstrap.min.js"></script>
<link type="text/css" href="/static/bootstrap3/css/bootstrap.min.css" rel="stylesheet"/>
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
function ajaxDeleteList(id){
	if(confirm("确定要删除这条数据吗?")){
		$.post('/homework/PriMathema/ajaxdeleteList',{"id":id,"type":1},function(data){
			if(data == 1){
				alert("删除成功");
				$('#'+id).remove();
			}else{
				alert(data);
				alert("删除失败");
			}
		});
	}
}
</script>
</head>
<body>
<div class="container">
  <div class="row">
    <div class="col-md-10" style="width:95%">
      <h2>{$info.classname}-第{$info.explainno}讲：客观作业</h2>
      	已经有{$count}人答该作业
      <form class="form-horizontal" role="form" id="addWeixinAccount" action="/weixinautoreply/account/ajax_addWeixinAccount" method="post">
        <div class="form-group" style="margin-top: 30px;">
          <label for="account_id" class="col-md-1 control-label" style ="width:100px;"></label>
            <?php if(!empty($list)): ?>
            <div class="col-md-10">
            <table class="table table-bordered">
              <tr>
                <th>题号</th>
                <th>分数</th>
                <th>答案</th>
                <th>题目类型</th>
                <th>对应例题</th>
                <th>操作</th>
              </tr>
              <?php foreach($list as $item): ?>
              <tr id="{$item.id}">
              	<td>{$item.qindex}</td>
              	<td>{$item.score}</td>
              	<td>{$item.answer}</td>
              	<td><?php switch ($item['qtype']){
              		case 0:
              			echo "单选题";break;
              		case 1:
              			echo "填空题";break;
              		case 2:
              			echo "多解题";break;
              		case 3:
              			echo "口述题";break;
              		case 4:
              			echo "多解题";break;
              	}?>{$item['type']}</td>
              	<td>{$item.example}</td>
              	<td>
              		<a title="编辑该题" href="/homework/pri_mathema/editObjtive/id/{$item.id}"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;&nbsp;&nbsp;
              		<a title="删除该题" href="javascript:void(0)" onclick="ajaxDeleteList({$item.id})"><i class="glyphicon glyphicon-trash"></i></a>
              	</td>
              </tr>
              <?php endforeach; ?>
            </table>
            </div>
          <?php else: ?>
          <div class="col-md-10" style="background-color: #66CCFF; border: 1px solid #6699FF; color: #ffffff; font-size: 16px;width:95%">
            <div style="width: 200px; margin: 80px auto;">还没有主观题目</div>
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