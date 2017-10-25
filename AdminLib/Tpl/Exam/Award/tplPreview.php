<?php if($awardImg):?>
<img src="<?php echo $awardImg?>?<?php echo time()?>" style="border:1px solid blue" />
<?php else:?>
<h1 style="color:red">请先设置电子版证书模板</h1>
<?php endif?>