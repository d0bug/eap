<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
	<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
	<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
	<script type="text/javascript" src="/static/js/essay.js"></script>
	<link href="/static/css/bootstrap.css" type="text/css" rel="stylesheet" />
	<link href="/static/css/bootstrap-responsive.css" type="text/css" rel="stylesheet" />
  </head>
  <body>
    <div id="myCarousel" class="carousel slide">
      <div class="carousel-inner">
      <?php for($i=$count-1;$i>=0;$i--):?>
        <div class="item <?php if($i == ($count-1)):?>active<?php endif;?>">
          <img src="<?php echo $essayImgArr[($i)];?>" alt="" width="1000" >
          <div class="container">
            <div class="carousel-caption">
              <p class="lead"><?php echo ($count-$i);?>/<?php echo $count;?></p>
              <a class="lead" href="#">
              	<?php echo $essayInfo['class_name']?>(<?php echo $essayInfo['class_code']?>)第<?php echo $essayInfo['speaker_number']?>讲&nbsp;&nbsp;
              	学生:<?php echo $essayInfo['student_name']?>&nbsp;的作文图片</a>
            </div>
          </div>
        </div>
        <?php endfor;?>
      </div>
      <a class="left carousel-control" href="#myCarousel" data-slide="prev">&lsaquo;</a>
      <a class="right carousel-control" href="#myCarousel" data-slide="next">&rsaquo;</a>
    </div>
    </div>
    <script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/static/js/bootstrap-carousel.js"></script>
    <script>
    !function ($) {
    	$(function(){
    		$('#myCarousel').carousel()
    	})
    }(window.jQuery)
    </script>
  </body>
</html>