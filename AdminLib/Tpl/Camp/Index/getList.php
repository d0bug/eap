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
		<a href="<?php echo U('/Camp/Index/index','','')?>" class="btn">班级列表</a>
        <a href="<?php echo U('/Camp/Camp/index','','')?>" class="btn">班级管理</a>
        <a href="<?php echo U('/Camp/Index/add',array('sClassCode'=>$sClassCode,'nLessonid'=>$nLessonid),'')?>" class="btn">添加题目</a>





    </h2>
    <h3><?php echo $aClassInfo['sname'];?><font color="red">[<?php echo $nLessonid;?>]讲次</font></h3>
    <?php foreach($aQuestionList as  $value):?>
        <p><font color="blue">
        -=<?php echo $value['nsort'];?>
        <?php if(!empty($value['nsubsort'])):?>
            第<?php echo $value['nsubsort'];?>小题
        <?php endif?>


        =-
        <font color="red">[<?php echo ($value['nsign'] == 1)?'练习':'作业';?>]</font>[<?php echo type2name($value['ntype']);?>]</font><?php echo base64_decode($value['sQuestion']['question']);?></p>
        <p>
        <?php if($value['ntype'] == 1):?>
        <?php foreach($value['sQuestion']['answers'] as $k =>$v):?>
            <b><?php echo chr(65+$k);?>:</b><?php echo base64_decode($v);?>
        <?php endforeach?>
    <?php endif?>
        </p>
        <p>正确答案：<?php echo ($value['ntype'] == 1)?chr(65+$value['sQuestion']['answer']):base64_decode($value['sQuestion']['answer']);?></p>
        <p>答案解析：<?php echo base64_decode($value['spoint']);?></p>
        <a href="<?php echo U('/Camp/Index/edit',array('sClassCode'=>$sClassCode,'id'=>$value['id'],'nLessonid'=>$nLessonid),'')?>" class="btn">修改</a>


        <hr>
    <?php endforeach?>
</div>

</div>

<script type="text/javascript">







</script>
</body>
</html>

