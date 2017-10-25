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
    <a href="<?php echo U('/Camp/News/add','','')?>" class="btn">添加</a>
    <table id="apply_table" class="tableList" width="90%" cellspacing="0" cellpadding="0" border="0">
    <thead>
    	<th>id</th>
    	<th>标题</th>

        <th>编码</th>
        <th>编辑</th>

    </thead>
    <tbody>
        <?php foreach($list as $value):?>
            <tr id="tr_<?php echo $value['id'];?>">
                <td><?php echo $value['id'];?></td>
                <td><?php echo $value['stitle'];?></td>

                <td><?php echo $value['nid'];?></td>
                <td>
                <a href="<?php echo U('/Camp/News/edit',array('id'=>$value['id']),'');?>">编辑</a>
                <a href="javascript:void(0);" onclick="del('<?php echo $value['id'];?>');">删除</a>

                </td>

            </tr>
        <?php endforeach?>
    </tbody>
    </table>
</div>

</div>

<script type="text/javascript">



function del(id){
	if(!confirm('确定要删除此记录么！')){
		return false;
	}
	$.post("<?php echo U('/Camp/News/del');?>",{'id':id},function(rs){
		if(rs.error){
			alert(rs.message);
		}else{
			$("#tr_" + id).remove();
		}
	},'json');
}





</script>
</body>
</html>

