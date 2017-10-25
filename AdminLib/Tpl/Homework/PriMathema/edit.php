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
function in_array(search,array){
    for(var i in array){
        if(array[i].toUpperCase() == search.toUpperCase()){
            return true;
        }
    }
    return false;
}
jQuery(function($) {
    jQuery('form').click(function(){
        var oForm = jQuery(this);
        jQuery('form').css('border', '1px solid #ccc');
        oForm.css('border', '1px solid blue');
    })
	$(".submitQuestion").click(function(){
		var arr = Array('A','B','C','D','E','F','G','H');
		var form = $(this).closest("form");
		var _id = $(form).find(".questionID").val();
		var _score = $(form).find("input[name='score']").val().replace(/^(\s|\u00A0)+/,'').replace(/(\s|\u00A0)+$/,'');
		var _type = $(form).find("input[name='type']:checked").val();
		var _qindex = $(form).find("input[name='qindex']").val().replace(/^(\s|\u00A0)+/,'').replace(/(\s|\u00A0)+$/,'');
		if(_type != 3){
			var _answer = $(form).find("[name='answer']").val();
			var _example = $(form).find("select[name='example']").val();
			var _lockType =$(form).find("input[name='lockType']:checked").val();
		}
		if(_qindex == ''){
			alert('题号不允许为空');
			return false;	
		}else if(_score == ''){
			alert('分数不允许为空');
			return false;
		}else if(_type != 3){
			if(_example == 0){
				alert('请选择对应例题');
				return false;
			}else if(_answer == ''){
				alert('答案不允许为空');
				return false;
			}else if(_type == 0 && !in_array(_answer,arr)){
				alert('选择题答案必须为A-H其中一项');
				return false;
			}else if(_type == 1 && _answer.split(',').length != _score.split(',').length){
				alert('填空题答案必须与分数个数一致');
				return false;
			}
		}
		if(_lockType == 'on'){
			_lockType = 1;
		}else{
			_lockType = 0;
		}
		$.post('/homework/pri_mathema/ajaxEditQuestion',{
			soType:<?php echo $_GET['type'];?>,
			id:_id,
			answer:_answer,
			score:_score,
			type:_type,
			example:_example,
			lockType:_lockType,
			qindex : _qindex
		},function(data){
			if(data == 1){
				alert('编辑成功');
			}else{
				alert('编辑失败');
			}
		});
	})
})
function ajaxDeleteList(id,type){
	if(confirm("确定要删除这条数据吗?")){
		$.post('/homework/PriMathema/ajaxdeleteList',{"id":id,"type":type},function(data){
			if(data == 1){
				alert("删除成功");
				$('#edit'+id).remove();
			}else{
				alert(data);
				alert("删除失败");
			}
		});
	}
}
</script>
</head>
<BODY>
<?php if ($type == 1){?>
<?php if($array_questionlist){ ?>
<div class="container">
<h2>管理试卷 &gt; 编辑试题信息&gt;客观题</h2>
<?php $seq=1;?>
<?php foreach ($array_questionlist as $one): ?>
<form method="post" action="" id="edit<?php echo $one['id'];?>" class="form-horizontal" style="background: #eee;border:1px solid #bbb;margin-bottom:15px;padding-top:4px">
<div class="form-group">
	<div class="col-md-offset-2 col-md-2">
	<button type="button" class="btn btn-warning" onclick="ajaxDeleteList(<?php echo $one['id'];?>,1);">↓删除该题↓</button>
	<div style="width:260px; margin-top:10px; display:none;" class="alert alert-info"></div>
	</div>
</div>
<input type="hidden" value="<?php echo $one['id']; ?>" class="questionID" name="id">
<?php if ($one['qtype'] == 3){?>
		<div class="form-group">
			<label class="control-label col-md-2" for="qindex">题号</label>
			<div class="col-md-1">
			<input type="text" class="form-control" name="qindex" id="qindex" value="<?php
            if($one['qindex']) {
                $seq = $one['qindex'];
                echo $seq++;
            } else {
                echo $seq++;
            };
            ?>">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-2">选择试题类型</label>
			<div class="col-md-3">
				<label class="radio-inline"> <input type="radio" name="type" value="3" checked>口述题</label>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-2">请输入分数</label>
			<div class="col-md-2">
			<input type="text" name="score" value="<?php if($one['score']){echo $one['score'];}else{if($isEnglish){echo 1;}} ?>" class="form-control" placeholder="请输入分数">
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-offset-2 col-md-2">
			<button type="button" class="submitQuestion btn btn-success btn-block" name="subt">提交</button>
			<div style="width:260px; margin-top:10px; display:none;" class="alert alert-info"></div>
			</div>
		</div>
<?php }else{ ?>
<div class="form-group">
<label for="qindex" class="control-label col-md-2">题号</label>
<div class="col-md-1">
<input type="text" value="<?php
        if($one['qindex']){
            $seq=$one['qindex'];
            echo $seq++;
        }else {
            echo $seq++;
        }?>" id="qindex" name="qindex"  class="form-control">
</div>
</div>
<div class="form-group">
<div class="col-md-offset-2 col-md-9">
<?php echo $one['contents'];?>
<?php if ($one['ischoice'] == 1){
	foreach ($one['alternative'] as $k=>$o){
		echo $k.'.'.$o.'&nbsp;&nbsp;&nbsp;&nbsp;';
	}
}?>
</div>
</div>
<div class="form-group">
<label class="control-label col-md-2">选择试题类型</label>
<div class="col-md-3">
<?php if ($one['ischoice']){?>
	<label class="radio-inline"> <input <?php if($one['qtype']==0){echo 'checked="checked"'; } ?> value="0" name="type" type="radio" >  单选题</label>
	<label class="radio-inline"> <input <?php if($one['qtype']==4){echo 'checked="checked"'; } ?> value="4" name="type" type="radio" > 多选题</label>
<?php }else{?>
	<label class="radio-inline"> <input <?php if($one['qtype']==1 || $one['qtype'] == 0){echo 'checked="checked"'; } ?> value="1" name="type" type="radio" > 填空题</label>
	<label class="radio-inline"> <input <?php if($one['qtype']==2){echo 'checked="checked"'; } ?> value="2" name="type" type="radio" > 多解填空</label>
<?php }?>
</div>
</div>
<div class="form-group">
<label class="control-label col-md-2">请输入答案</label>
<div class="col-md-4">
<?php if ($one['ischoice'] == 1){?>
<input placeholder="请输入答案" class="form-control" type="text" value="<?php echo $one['answer']; ?>" name="answer" >
<?php }else{?>
<input placeholder="请输入答案" class="form-control" type="text" value="
<?php 
if (is_array($one['answer'])){
	$tmpi=1;
	foreach ($one['answer'] as $v){
		if ($tmpi == 1){
			echo $v;
		}else{
			echo ','.$v;
		}
		$tmpi++;
	}
}else{
	echo $one['answer'];
} ?>" name="answer" >
<?php }?>
<p class="help-block">如果是选择题，请输入A-H；<br>多个答案请用半角逗号( , )分隔；<br>如果是填空题，请直接输入答案</p>
</div>
</div>
<div class="form-group">
<label class="control-label col-md-2">锁定答案类型</label>
<div class="col-md-4">
<label class="checkbox-inline">
<input type="checkbox" id="lockType" <?php if ($one['locktype'] == 1){echo "checked";}?> name="lockType" /> (选中后不再进行分数与小数转换)
</label>
</div>
</div>
<div class="form-group">
<label class="control-label col-md-2">请输入分数</label>
<div class="col-md-2">
<input placeholder="请输入分数" class="form-control" type="text" value="<?php if($one['score']){echo $one['score'];}else{if($isEnglish){echo 1;}}; ?>" name="score">
</div>
</div>

<div class="form-group">
<label class="control-label col-md-2">本题对应例题</label>
<div class="col-md-3">
<select id="example" name="example" class="form-control">
    <?php if(false == $isEnglish):?><option value="0">请选择对应例题</option><?php endif?>
	<?php
		for ($i=1;$i<17;$i++){
			$title = '例题'.$i;
			$select =  $one['example'] == $title ? "selected='seleceted'" : '';
			echo "<option value='".$title."' $select>".$title."</option>";
		} 
		for ($i=1;$i<5;$i++){
			$title = '专属'.$i;
			$select =  $one['example'] == $title ? "selected='seleceted'" : '';
			echo "<option value='".$title."' $select>".$title."</option>";
		}
		for ($i=1;$i<9;$i++){
			$title = '练一练'.$i;
			$select =  $one['example'] == $title ? "selected='seleceted'" : '';
			echo "<option value='".$title."' $select>".$title."</option>";
		}
	?>
</select>
</div>
</div>
<div class="form-group">
<div class="col-md-offset-2 col-md-2">
<button name="subt" class="submitQuestion btn btn-success btn-block" type="button">提交</button>
<div class="alert alert-info" style="width:260px; margin-top:10px; display:none;"></div>
</div>
</div>
<?php }?>
</form>
<?php endforeach; ?>
</div>
<?php }else{ ?>
<p>无试题，请上传试题后再来编辑</p>
<?php } ?>
<?php }else{?>
<?php if($array_questionlist){ ?>
<div class="container">
<h2>管理试卷 &gt; 编辑试题信息&gt;主观题</h2>
<?php foreach ($array_questionlist as $one): ?>
<form method="post" action="" id="edit<?php echo $one['id'];?>" class="form-horizontal" style="background: #eee;border:1px solid #bbb;margin-bottom:15px;padding-top:4px">
<div class="form-group">
	<div class="col-md-offset-2 col-md-2">
	<button type="button" class="btn btn-warning" onclick="ajaxDeleteList(<?php echo $one['id'];?>,2);">↓删除该题↓</button>
	<div style="width:260px; margin-top:10px; display:none;" class="alert alert-info"></div>
	</div>
</div>
<input type="hidden" value="<?php echo $one['id']; ?>" class="questionID" name="id">
<div class="form-group">
<label for="qindex" class="control-label col-md-2">题号</label>
<div class="col-md-1">
<input type="text" value="<?php echo $one['qindex']; ?>" id="qindex" name="qindex"  class="form-control">
</div>
</div>
<div class="form-group">
<div class="col-md-offset-2 col-md-9">
<?php echo $one['contents'];?>
</div>
</div>
<div class="form-group">
<label class="control-label col-md-2">选择试题类型</label>
<div class="col-md-3">
	<label class="radio-inline"> <input <?php if($one['qtype']==1 || $one['qtype'] == 0){echo 'checked="checked"'; } ?> value="1" name="type" type="radio" > 普通题</label>
	<label class="radio-inline"> <input <?php if($one['qtype']==2){echo 'checked="checked"'; } ?> value="2" name="type" type="radio" >思考题</label>
</div>
</div>
<div class="form-group">
<label class="control-label col-md-2">请输入答案</label>
<div class="col-md-4">
<TEXTAREA class="form-control" name="answer" rows="5" cols="250">
<?php
if (is_array($one['answer'])){
	$tmpi=1;
	foreach ($one['answer'] as $v){
		if ($tmpi == 1){
			echo $v;
		}else{
			echo '<br>'.$v;
		}
		$tmpi++;
	}
}else{
	echo $one['answer'];
}
?>
</TEXTAREA>
</div>
<div class="col-md-4">
<?php
if (is_array($one['answer'])){
	$tmpi=1;
	foreach ($one['answer'] as $v){
		if ($tmpi == 1){
			echo $v;
		}else{
			echo '<br>'.$v;
		}
		$tmpi++;
	}
}else{
	echo $one['answer'];
}
?>
</div>
</div>
<div class="form-group">
<label class="control-label col-md-2">请输入分数</label>
<div class="col-md-2">
<input placeholder="请输入分数" class="form-control" type="text" value="<?php echo $one['score']; ?>" name="score">
</div>
</div>

<div class="form-group">
<label class="control-label col-md-2">本题对应例题</label>
<div class="col-md-3">
<select id="example" name="example" class="form-control">
	<option value="0">请选择对应例题</option>
	<?php
		for ($i=1;$i<17;$i++){
			$title = '例题'.$i;
			$select =  $one['example'] == $title ? "selected='seleceted'" : '';
			echo "<option value='".$title."' $select>".$title."</option>";
		} 
		for ($i=1;$i<5;$i++){
			$title = '专属'.$i;
			$select =  $one['example'] == $title ? "selected='seleceted'" : '';
			echo "<option value='".$title."' $select>".$title."</option>";
		}
		for ($i=1;$i<9;$i++){
			$title = '练一练'.$i;
			$select =  $one['example'] == $title ? "selected='seleceted'" : '';
			echo "<option value='".$title."' $select>".$title."</option>";
		}
	?>
</select>
</div>
</div>
<div class="form-group">
<div class="col-md-offset-2 col-md-2">
<button name="subt" class="submitQuestion btn btn-success btn-block" type="button">提交</button>
<div class="alert alert-info" style="width:260px; margin-top:10px; display:none;"></div>
</div>
</div>
</form>
<?php endforeach; ?>
</div>
<?php }else{ ?>
<p>无试题，请上传试题后再来编辑</p>
<?php } ?>
<?php }?>
</div>
</BODY>
</html>