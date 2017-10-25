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





    </h2>
    <p>
    <?php for($i = -1;$i<2;$i++):?>
         <input type="radio" name="nYear" value="<?php echo $nCurrYear+$i;?>" onclick="chengeYear(this.value)" <?php if($data['nClassYear'] == $nCurrYear+$i)echo 'checked'; ?>> <?php echo $nCurrYear+$i;?> 年
    <?php endfor?>

    </p>
    <p>
    <input type="radio" name="nSemester" onclick="chengeSeason(this.value)" value="3" <?php if($data['nSemester'] == 3)echo 'checked'; ?>> 春
    <input type="radio" name="nSemester" onclick="chengeSeason(this.value)" value="4" <?php if($data['nSemester'] == 4)echo 'checked'; ?>> 夏
    <input type="radio" name="nSemester" onclick="chengeSeason(this.value)" value="1" <?php if($data['nSemester'] == 1)echo 'checked'; ?>> 秋
    <input type="radio" name="nSemester" onclick="chengeSeason(this.value)" value="2" <?php if($data['nSemester'] == 2)echo 'checked'; ?>> 冬
    </p>
    <table id="apply_table" class="tableList" width="90%" cellspacing="0" cellpadding="0" border="0">
    <thead >
        <tr >
        <th>班级编码</th>
        <th>班级名称</th>
        <?php for($i = 1;$i <= 16; $i++):?>
            <th><?php echo $i;?></th>

        <?php endfor?>
        </tr>
    </thead>
    <tbody>

        <?php foreach($list as $value):?>
            <?php $sClassCode = trim($value['scode']);?>
            <tr>
            <td><?php echo $sClassCode;?></td>
            <td><?php echo $value['sname'];?></td>
            <?php for($i = 1;$i <= 16; $i++):?>
                <?php if(empty($numberList[$sClassCode][$i])):?>
                     <td><a href="/Camp/Index/add/sClassCode/<?php echo $sClassCode;?>/nLessonid/<?php echo $i;?>">上传</a></td>
                <?php else:?>
                     <td><a href="/Camp/Index/paperList/sClassCode/<?php echo $sClassCode;?>/nLessonid/<?php echo $i;?>"><?php echo $numberList[trim($value['scode'])][$i];?></a></td>
                <?php endif?>


            <?php endfor?>
            </tr>
        <?php endforeach?>


    </tbody>
    </table>
</div>

</div>

<script type="text/javascript">
var nYear = <?php echo $data['nClassYear'];?>;
var nSeason = <?php echo $data['nSemester'];?>;

function local() {
    //alert(nYear);
    if(nYear < 2000 || nYear > 2020) {
        alert('请选择年份');
        return false;
    }
    if(nSeason < 1 || nSeason >4) {
        alert('请选择学期');
        return false;
    }
    var url = '/Camp/Index/index/nYear/'+nYear+'/nSeason/'+nSeason;
    location.href=url;
}
function chengeYear(value) {
    nYear = value;

    local();


}
function chengeSeason(value) {
    nSeason = value;
    local();
}




</script>
</body>
</html>

