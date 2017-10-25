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

</head>
<body>
<div class="container">
  <div class="row">
    <div class="col-md-10" style="width:95%">
      <h2>手机列表</h2>
      <form class="form-horizontal" role="form" id="addWeixinAccount" action="/weixinautoreply/account/ajax_addWeixinAccount" method="post">
        <div class="form-group" style="margin-top: 30px;">
          <label for="account_id" class="col-md-1 control-label" style="width:100px;"></label>
            <?php if(!empty($mobileList)): ?>
            <div class="col-md-10">
            <table class="table table-bordered">
              <tr>
              	<th style="text-align:center">手机编号</th>
              	<th>名称</th>
                <th>使用状态</th>
                <th>使用人</th>
                <th>使用时间</th>
                <th>归还时间</th>
              </tr>
              <?php $i = 1; foreach($mobileList as $item): ?>
              <tr>
              	<td>{$i}</td>
              	<td>{$item.sname}</td>
              	<td>
              	<?php if ($item['usestatus'] == 1){
              		echo '使用中';
              	}else{
              		echo "无人使用";
              	}?>
              	</td>
                <td>{$item.applyname}</td>
                <td><?php if (!empty($item['starttime']))echo date('Y-m-d H:i',$item['starttime'])?></td>
                <td><?php if (!empty($item['endtime']))echo date('Y-m-d H:i',$item['endtime'])?></td>
              </tr>
              <?php $i++; endforeach; ?>
            </table>
            <div>{$show}</div>
            </div>
          <?php else: ?>
          <div class="col-md-10" style="background-color: #66CCFF; border: 1px solid #6699FF; color: #ffffff; font-size: 16px;width:95%">
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