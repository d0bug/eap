<?php foreach($classList->PagedList as $class){?>
	<div class="course auto">
		<a href="xbkechengxiangqing.html">
			<div class="shctitle f14 c-red"><?php echo $class->ClassName;?><span><i class="c-fff b-red">热</i></span></div>
		</a>
		<figure class="tour">
			<a href="<?php echo U('Vip/GsWeixin/classDetails',array('id'=>$class->Id))?>">
				<img src="/static/images/class.jpg" class="w43">
			</a>
			<figcaption class="w57">
				<a href="<?php echo U('Vip/GsWeixin/classDetails',array('id'=>$class->Id))?>">
					<p><i class="c-green w25">老师姓名</i><span class="w73"><?php echo $class->TeacherName;?></span></p>
					<p><i class="c-green w25">开课日期</i><span class="w73"><?php echo substr($class->BeginOn,0,10);?></span></p>
					<p><i class="c-green w25">课次小时</i><span class="w73"><?php echo $class->LessonNum;?>小时</span></p>
					<p><i class="c-green w25">上课校区</i><span class="w73"><?php echo $class->DeptName;?></span></p>
					<p><i style='color:#339933;margin-left:8px'>可报:</i><span><?php echo $class->LimitNum;?>人</span><i style='color:#339933;margin-left:15px'>已报:</i><span><?php echo $class->NowNum+$class->orderNum;?>人</span></p>
				</a>
				<?php if($class->is_sign == 1){?>
					<a href="javascript:;" class="btn_gray btn_cou">班级已满</a>
				<?php 
					}else{
						if($class->is_order == 1){
							echo '<a href="javascript:;" class="btn_red btn_cou">已购买</a>';
						}else{
				?>
					<a href="<?php echo U('Vip/GsWeixin/classEnroll',array('id'=>$class->Id))?>" class="btn_red btn_cou">立即报名</a>
				<?php }}?>
			</figcaption>
			<div class="clear"></div>
		</figure>
	</div>
<?php }?>