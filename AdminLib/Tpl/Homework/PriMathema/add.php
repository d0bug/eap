<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/bootstrap3/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/static/js/jgrowl/jquery.jgrowl.min.js"></script>
<link href="/static/bootstrap3/css/bootstrap.min.css" type="text/css" rel="stylesheet" />
<link href="/static/js/jgrowl/jquery.jgrowl.custom.css" rel="stylesheet" />
<script type="text/javascript">
    jQuery(function(){
        jQuery('.ckAll').click(function(){
            var subClass = '.sub_' + jQuery(this).attr('qid');
            jQuery(subClass).attr('checked', this.checked);
        })

        jQuery('.checkAll').click(function(){
            var bchecked = this.checked;
            jQuery('.btn-ckAll').attr('checked', bchecked);
            jQuery('.qTikuId').attr('checked', bchecked);
        })
    })
</script>
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
      <h2>添加{$title}作业</h2>
		<form role="form" method="post">
			<input type="hidden" name="infoid" value="{$_GET['infoid']}">
			<button type="submit" class="btn btn-default">提交</button>
            <label>&nbsp;&nbsp;<input type="checkbox"  class="checkAll" />全选</label>
		  <DIV id="question"></DIV>
		  <?php foreach($question as $one): ?>
            <div style="background: #eee;border:1px solid #ccc;padding:5px;margin-bottom:10px">
		  	<div class="checkbox">
                <?php if($one->qsn_type != 7):?>
			    <label style="font-weight:bold">
			      题目id:<?php echo $one->qsn_id;?>：<input class="qTikuId" type="checkbox" name="tikuId[]" value="<?php echo $one->qsn_id;?>">
			    </label>
			    <?php echo $one->qsn_content;?>
                <?php else:?>
                    <?php echo $one->qsn_content;?>
                    <div><label><input type="checkbox" class="ckAll" qid="<?php echo $one->qsn_id?>" />全选</label></div>
                    <ul style="padding-left:30px">
                    <?php foreach($one->sub as $ques):?>
                        <li style="list-style-type: none">
                            <label style="font-weight:bold">
                                题目id:<?php echo $ques->qsn_id;?>：<input class="qTikuId" sub_<?php echo $one->qsn_id?>" type="checkbox" name="tikuId[]" value="<?php echo $ques->qsn_id;?>">
                            </label>
                            <?php echo $ques->qsn_content;?>
                        </li>
                    <?php endforeach?>
                    </ul>
                <?php endif?>
			</div>
            </div>

      	  <?php endforeach; ?>
		  <button type="submit" class="btn btn-default">提交</button>
            <label>&nbsp;&nbsp;<input type="checkbox"  class="checkAll" />全选</label>
		</form>
    </div>
  </div>
</div>
</body>
</html>