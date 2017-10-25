<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>高思教育VIP一对一学生报告</title>
	<meta name="keywords" content="" />
	<meta name="description" content=""/>
	<link href="/static/css/base.css" rel="stylesheet" media="screen">
</head>
<body>
	<div class="w980 baogao" >
		<div class="student-top">
			<div class="student-name"><span><?php echo $heluInfo['sstudentname']?><i class="">同学 & </i></span><span class="baogao-icon student-baogaotext"></span></div>
			<div class="student-ketime clear">课次主题：<?php echo $heluInfo['lesson_topic']?><span></span>上课时间：<?php echo $lessonTime?></div>
		</div>
		<!-- <div class="student-title"><span class="baogao-icon student-con1-icon student-con-icon"></span>服务流程图</div>
		<div class="student-con1 student-con">
			<div class="fuwubox fuwuover">
				<div class="fuwubox-title center"><p class="f22 p5">备课</p><p class="f16 arial">2015-02-26 15:00</p></div>
				<ul class="fuwubox-conli">
					<li><i>●</i> 研究学习轨迹/备资讯</li>
					<li><i>●</i> 确定课次主题</li>
					<li><i>●</i> 在线搭个性化讲义</li>
					<li><i>●</i> 打印讲义</li>
				</ul>
				<div class="geline baogao-icon"></div>
				<div class="fuwuimg  baogao-icon fuwuimg1"></div>
			</div>
			<div class="fuwubox fuwuover">
				<div class="fuwubox-title center"><p class="f22 p5">上课</p><p class="f16 arial">2015-02-26 15:00</p></div>
				<ul class="fuwubox-conli">
					<li><i>●</i> 带学员打卡</li>
					<li><i>●</i> 批改上次作业</li>
					<li><i>●</i> 教授新知识/内化</li>
					<li><i>●</i> 交流资讯/布置作业</li>
				</ul>
				<div class="geline baogao-icon"></div>
				<div class="fuwuimg baogao-icon fuwuimg2"></div>
			</div>
			<div class="fuwubox fuwuover on">
				<div class="fuwubox-title center"><p class="f22 p5">记录轨迹</p><p class="f16 arial">2015-02-26 15:00</p></div>
				<ul class="fuwubox-conli">
					<li><i>●</i> 错题存储</li>
					<li><i>●</i> 好题推荐</li>
					<li><i>●</i> 拍轨照</li>
					<li><i>●</i> 教师叮咛</li>
				</ul>
				<div class="geline baogao-icon"></div>
				<div class="fuwuimg baogao-icon fuwuimg3 "></div>
			</div>
			<div class="fuwubox ">
				<div class="fuwubox-title center "><p class="f22 p5">师生互评</p><p class="f16 arial">2015-02-26 15:00</p></div>
				<ul class="fuwubox-conli">
					<li><i>●</i> 师生互评</li>
					<li><i>●</i> 生成学习报告</li>
					<li><i>●</i> 复习/完成作业</li>
					<li><i>●</i> 梳理疑惑点</li>
				</ul>
				<div class="geline baogao-icon"></div>
				<div class="fuwuimg baogao-icon fuwuimg4 "></div>
			</div>
			<div class="clear"></div>
			<div class="linebox">
					<div class="baogao-icon lineboxfeiji"></div>
			</div>
		</div> -->


		<div class="student-title"><span class="baogao-icon student-con2-icon student-con-icon"></span>课节信息</div>
		<div class="student-con2 student-con">
			<table class="usertable">
				<tr>
					<td width="20%" class="b-qblue">科目</td>
					<td width="30%"><?php echo $heluInfo['skechengname']?></td>
					<td width="20%" class="b-qblue">课次</td>
					<td width="30%">第<?php echo $heluInfo['nlessonno']?>次</td>
				</tr>
				<tr>
					<td class="b-qblue">教师</td>
					<td><?php echo $userInfo['real_name']?></td>
					<td class="b-qblue">班主任</td>
					<td><?php echo $heluInfo['sclassadvisername']?></td>
				</tr>
				<!-- <tr>
					<td class="b-qblue">共6个知识点</td>
					<td align="left" colspan="3">
					</td>				
				</tr> -->
			</table>
		</div>

		<div class="student-title"><span class="baogao-icon student-con3-icon student-con-icon"></span>课节评价</div>
		<div class="student-con3 student-con">
			<div class="con3teachersay">
				<!-- <p class="lh24 c-f60"><i class="f22">{ </i>老师还要给其他小伙伴们上课，课评正在路上！48小时内发送到你手机，请登录学员APP查收哦~<i class="f22"> }</i></p> -->
				<div class="con3teacherstar  clearfix">
					<div class="fl con3teacherstar-left">
						<ul>
							<?php echo $dimensionCommentHtml?>
						</ul>
					</div>
					<div class="fl con3teacherstar-right ">
						<p class="mb10 con3teachersaytitle"><span class="baogao-icon"></span>老师说</p>
						<i>“</i><?php echo $heluInfo['comment']?>
						<i>”</i>
					</div>
				</div>
			</div>
		</div>

		<div class="student-title"><span class="baogao-icon student-con4-icon student-con-icon"></span>本节知识点</div>
		<div class="student-con3 student-con clearfix">
			<ul class="zsdlist br1">
			<?php echo $knowledgeListHtml1; ?>
			</ul>
			<ul class="zsdlist">
			<?php echo $knowledgeListHtml2;?>
			</ul>
		</div>

		
		
		
		<div class="clear"></div>

		



<!-- 		<div class="student-title"><span class="baogao-icon student-con7-icon student-con-icon"></span>本次照片</div>
		<div class="center student-con7  student-con">
			<?php echo $reportImgHtml?>
		</div> -->
	</div>
	<div class="baogao-printer baogao-icon"  onclick="print_preview(1)"></div>
</body>
<script type="text/javascript">
//打印页面部分内容
function print_preview(oper) {
	if (oper < 10){
		bdhtml=window.document.body.innerHTML;//获取当前页的html代码
		sprnstr="<!--startprint"+oper+"-->";//设置打印开始区域
		eprnstr="<!--endprint"+oper+"-->";//设置打印结束区域
		prnhtml=bdhtml.substring(bdhtml.indexOf(sprnstr)+18); //从开始代码向后取html

		prnhtml=prnhtml.substring(0,prnhtml.indexOf(eprnstr));//从结束代码向前取html
		window.document.body.innerHTML=prnhtml;
		window.print();
		window.document.body.innerHTML=bdhtml;

	} else{
		window.print();
	}

}


</script>
</html>