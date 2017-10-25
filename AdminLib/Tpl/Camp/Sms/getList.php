<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/modular.js"></script>
<link href="/static/css/modular.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div region="center">
	<div id="main">
		<h2>问题列表&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="<?php echo U('/Camp/News/index','','')?>" class="btn">资讯列表</a>






    </h2>
    <a href="<?php echo U('/Camp/Sms/add','','')?>" class="btn">添加</a>
    <table id="apply_table" class="tableList" width="90%" cellspacing="0" cellpadding="0" border="0">
    <thead>
    	<th>id</th>
    	<th>内容</th>

        <th>时间</th>
        <th>数量</th>

    </thead>
    <tbody>
        <?php foreach($list as $value):?>
            <tr>
                <td><?php echo $value['id'];?></td>
                <td><?php echo $value['smessage'];?></td>

                <td><?php echo date('Y-m-d H:i:s',$value['nid']);?></td>
                 <td><?php echo $value['nnumber'];?></td>

            </tr>
        <?php endforeach?>
    </tbody>
    </table>
</div>

</div>

<script type="text/javascript">
</script>
</body>
</html>

