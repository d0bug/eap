<!doctype html>
<html>
    <head>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <script type="text/javascript">
        jQuery(function(){
	        <?php if($uploadResult === true):?>
	        	parent.loadImg();
	        <?php else:?>
	        	alert('<?php echo $uploadResult['errorMsg']?>');
	        <?php endif?>
        })
        </script>
    </head>
    <body></body>
</html>