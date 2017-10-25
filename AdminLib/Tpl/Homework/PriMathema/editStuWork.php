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
@media (max-width: 1000px) {
.container {
	width: 100%;
}
}
.form-horizontal {
	margin-left: -50px;
}
h2 {
	border-bottom: 3px solid #EEEEEE;
	color: #575765;
	font-size: 24px;
	margin: 40px 0;
	padding-bottom: 10px;
}
</style>
<script>
<?php if (!empty($success) && $success == 1){?>
alert('修改成功');
<?php }?>
function in_array(search,array){
    for(var i in array){
        if(array[i].toUpperCase() == search.toUpperCase()){
            return true;
        }
    }
    return false;
}
jQuery(function($) {
	$(".submitQuestion").click(function(){
		var form = $(this).closest("form");
		var answer = $(form).find("input[name='studentanswer']").val().replace(/^(\s|\u00A0)+/,'').replace(/(\s|\u00A0)+$/,'');
		var score = $(form).find("input[name='lscore']").val().replace(/^(\s|\u00A0)+/,'').replace(/(\s|\u00A0)+$/,'');
		if(answer == ''){
			alert('学生的答案不可以为空喔');
			return false;
		}else if(score == ''){
			alert('学生的分数不可以为空');
			return false;
		}
	})
})
</script>
</head>
<BODY>
<?php if($stuWork){ ?>
<div class="container">
<h2>管理学生作业 &gt; 编辑客观题&gt;{$studentInfo['sname']}</h2>
<h3>{$studentInfo['sclassname']} 第{$studentInfo['nlessonno']}讲</h3>
<?php foreach ($stuWork as $one){ ?>
<form method="post" action="" id="edit<?php echo $one['id'];?>" class="form-horizontal">
<input type="hidden" value="<?php echo $one['id']; ?>" name="id">
<input type="hidden" value="<?php echo $one['infoid']; ?>"  name="infoid">
		<div class="form-group">
			<label class="control-label col-md-2">题号</label>
			<div class="col-md-1">
			<label class="control-label col-md-2">{$one.nquestionindex}</label>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-2">正确答案</label>
			<div class="col-md-3">
				<label class="control-label">{$one.answer}</label>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-2">学生答案</label>
			<div class="col-md-2">
			<input type="text" name="studentanswer" value="<?php echo $one['studentanswer']; ?>" class="form-control" placeholder="请输入答案">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-2">学生得分</label>
			<div class="col-md-2">
			<input type="text" name="lscore" value="<?php echo $one['lscore']; ?>" class="form-control" placeholder="请输入得分"/>
			该题满分
			<?php
			   if (strpos($one['nscore'],',')){
			 		$tmpArr = explode(',',$one['nscore']);
			 		echo array_sum($tmpArr).'分，其中：';
			 		foreach ($tmpArr as $k=>$v){
						$k = $k+1;
			 			echo "第".$k."题".$v.'分；';
			 		}
			 	}else{
			 		echo $one['nscore'].'分';
			 	}
			?>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-2">该题状态</label>
			<div class="col-md-2">
			<select name="title" class="form-control">
			<option value="0" <?php if ($one['title'] == 0){echo 'selected="selected"';}?>>错误</option>
			<option value="1" <?php if ($one['title'] == 1){echo 'selected="selected"';}?>>正确</option>
			<option value="2" <?php if ($one['title'] == 2){echo 'selected="selected"';}?>>半对</option>
			</select>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-offset-2 col-md-2">
			<button type="submit" class="submitQuestion btn btn-success btn-block" name="subt">提交</button>
			<div style="width:260px; margin-top:10px; display:none;" class="alert alert-info"></div>
			</div>
		</div>
		<hr>
</form>
<?php } ?>
</div>
<?php }else{ ?>
<h1>无试题，请等待学生上传试题后再来编辑</h1>
<?php }?>
</div>
</BODY>
</html>