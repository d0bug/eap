<?php if( !empty($kcList->AppendData) ){
			foreach($kcList->AppendData as $rows){
	?>
	<div class="course auto">
		<figure class="tour">
			<a href="<?php echo U('Vip/GsWeixin/courseDetail',array('id'=>$rows->uid));?>">
				<img src="
					<?php 
						if($rows->thumb  =='course_default.png')
						{ 
							echo '/static/images/vip.png';
						}else{
							echo 'http://www.gaosivip.com'.$rows->thumb;
						}
					?>" 
				class="w38">
			</a>
			<figcaption class="w60">
				<a href="<?php echo U('Vip/GsWeixin/courseDetail',array('id'=>$rows->uid));?>">
					<h2 class="c-red f14 wellipsis pb5"><?php echo $rows->title;?></h2>
					<p class="w50 f10 left">
						<i class="c-green pr10">年级</i>
						<?php echo $rows->grade_caption;?>
					</p>
					<p class="w50 f10 left">
						<i class="c-green pr10">科目</i>
						<?php echo $rows->subject_caption;?>
					</p>
				</a>
				<a href="<?php echo U('Vip/GsWeixin/courseMake',array('id'=>$rows->uid));?>" class="btn_red btn_cou">立即预约</a>
			</figcaption>
			<div class="clear"></div>
		</figure>
	</div>
	<?php }}?>