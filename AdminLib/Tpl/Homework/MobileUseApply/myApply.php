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
      <h2>我的申请</h2>
      <form class="form-horizontal" role="form" id="addWeixinAccount" action="/weixinautoreply/account/ajax_addWeixinAccount" method="post">
        <div class="form-group" style="margin-top: 30px;">
          <label for="account_id" class="col-md-1 control-label" style="width:100px;"></label>
            <?php if(!empty($myApply)): ?>
            <div class="col-md-10">
            <table class="table table-bordered">
              <tr>
              	<th> </th>
                <th>创建时间<BR>使用时间<BR>预计归还时间</th>
                <th>手机型号</th>
                <th>审批状态</th>
                <th>归还状态</th>
                <th>备注</th>
                <th>伟力说</th>
              </tr>
              <?php foreach($myApply as $item): ?>
              <tr id="{$item.id}">
              	<td>{$item.id}</td>
                <td><?php echo date('Y年m月d日 H:i',$item['createtime'])?><br>
                <?php echo date('Y年m月d日 H:i',$item['starttime'])?><br>
                <?php echo date('Y年m月d日 H:i',$item['endtime'])?>
                </td>
                <td>{$item['sname']}</td>
                <td>
                <?php
                switch ($item['markingstatus']){
                	case 0:
                		echo "等待审批";break;
                	case 1:
                		echo "<font color='#0F3'>已通过</font>";break;
                	case 2:
                		echo "<font color='#f00'>被拒绝</font>";break;
                	default:
                		echo "未知";break;
                } 
                ?>
                </td>
                <td>
                	<?php
                	if ($item['markingstatus'] == 1 && $item['gobackstatus']==1){
                		echo "已归还";
                	}elseif ($item['markingstatus'] == 1 && $item['gobackstatus'] == 0){
                		echo "<font color='#f00'>未归还</font>";
                	}
                	?>
                </td>
                <td>{$item['contents']}</td>
                <td>{$item['weilisay']}</td>
              </tr>
              <?php endforeach; ?>
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