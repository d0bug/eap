function ImgUp(){
	$.gsmodel({
		html: '<div style="background:url(/images/loading_31x31.gif) no-repeat 50%; width:100px; height:70px;"></div>',
		overlayClose: false,
		closeButton: false,
		title: '图片上传中，请勿关闭窗口',
		transition: 'none'
	});
	$('#picForm').submit();
}
function checkNull(type){
	var status = true;
	switch(type){
		case '0':
			var answer = $('input[name="answer"]:checked').val().replace(/^(\s|\u00A0)+/,'').replace(/(\s|\u00A0)+$/,'');
			if(!answer){
				status = false;
			}
			break;
		case '1':
			if($('#answer').val().replace(/^(\s|\u00A0)+/,'').replace(/(\s|\u00A0)+$/,'') == ''){
				status = false;
			}
			break;
		case '11':
			var inp = $("input[class='inp']");
			for (var i=0;i<inp.length;i++){
				if(inp.eq(i).val().replace(/^(\s|\u00A0)+/,'').replace(/(\s|\u00A0)+$/,'') == ''){
					status = false;
					break;
				}
			}
			break;
		case '2':
			var inp = $("input[class='inp']");
			for (var i=0;i<inp.length;i++){
				if(inp.eq(i).val().replace(/^(\s|\u00A0)+/,'').replace(/(\s|\u00A0)+$/,'') == ''){
					status = false;
					break;
				}
			}
			break;
		case '3':
			var answer = $('input[name="answer"]:checked').val();
			if(!answer){
				status = false;
			}
			break;
		default:
			alert('default');
			return false;
	}
	return status;
}
$(function() {
	$('.selectWrap select, .selectLesson select').change(function() {
		var thisval = $(this).find('option:selected').text();
		$(this).next().html(thisval);
	})
	/*
	// 头部切换
	$('.headerTitle').click(function() {
		$('#header .mselect').toggle();
	});
	// 浮动导航触发
	$('.menuIcon').click(function() {
		$('#floatMenu .subMenu').show().animate({
			opacity: 1, right: '55px'
		},300);
	});
	$('html').click(function(event) {
		morehide(event);
	});
*/
	// 选择讲次
	$('#Lesson').change(function() {
		var thisval = $(this).find('option:selected').text();
		var nLessonCode = $(this).find('option:selected').val();
		var nSemester = $('#nSemester').val();
		var nLessonNo = $(this).find('option:selected').attr('dataid');
		var weixinId = $('#weixinId').val();
		var sClassTypeCode = $('#sClassTypeCode').val();
		$('#lessonNo').val(nLessonNo);
		$(this).next().html(thisval);
		$.get("/WeiXin/Homework/ajaxIndex/weixinid/"+weixinId+'/sClassTypeCode/'+sClassTypeCode+'/nLessonCode/'+nLessonCode+'/nSemester/'+nSemester+'/nLessonNo/'+nLessonNo,{},function(data){
			if(data){
				$('#objectiveResult').html('');
				$('#subjectiveResult').html('');
				var result  = $.parseJSON(data);
				var objective = result['objective'];
				var subjective = result['subjective'];
				switch(objective['status']){
					case 0:
						$('#objectiveResult').append('<h2>客观题：<span class="total">该讲次不需要在线提交作业</span></h2>');break;
					case 1:
						$('#objectiveResult').append('<h2>客观题： <button type="button" class="btn btn-orange" id="subAnswer">上传答案</button></h2>	<p>您本讲作业未在网上提交，请点击提交</p>');break;
					case 2:
						$('#objectiveResult').append('<h2>客观题：<span class="total">本讲次您没有在网上提交答案</span></h2>');break;
					case 3:
						$('#objectiveResult').append('<h2>客观题： <span class="total">总分：'+objective['cScore']+'分 &nbsp; 得分：'+objective['rollInfo']['score']+'分</span></h2><p>同班型平均分：'+objective['am']['avgscore']+'分  /  最高分：'+objective['am']['maxscore']+'分</p><div class="button"><button type="button" class="btn btn-green" id="Details">查看详情</button></div>');break;
					default:
						alert('参数错误');
					}
				switch(subjective['status']){
				case 0:
					$('#subjectiveResult').append('<h2>主观题：</h2>	<p>本讲次主观题作业不需在网上提交。</p>');break;
				case 1:
					$('#subjectiveResult').append('<h2>主观题： <span class="total">总分：'+subjective['counScore']+'分 &nbsp; 得分：'+subjective['subScore']+'分</span></h2><p>同班型平均分：'+subjective['avgScore']+'分  /  最高分：'+subjective['maxScore']+'分</p><div class="button"><button type="button" class="btn btn-green" id="subDetails">查看详情</button></div>');break;
				case 2:
					$('#subjectiveResult').append('<h2>主观题：</h2>	<p>您已上传'+subjective['countNumber']+'张照片，请耐心等待老师批阅。</p>');break;
				case 3:
					$('#subjectiveResult').append('<h2>主观题：</h2>	<p>请点击左上角“返回”，把作业照片发给高豆豆。</p>');break;
				case 4:
					$('#subjectiveResult').append('<h2>主观题：</h2>	<p>您本讲次作业未在网上提交</p>');break;
				}
			}else{
				$('#objectiveResult').html('<h2>客观题：该讲次不需要在线提交作业</h2>');
			}
		});
	})
	
	//客观作业选择讲次
	$('#selectResult').change(function() {
		var thisval = $(this).find('option:selected').text();
		var nLessonNo = $(this).find('option:selected').val();
		var weixinId = $('#weixinId').val();
		var sClassTypeCode = $('#sClassTypeCode').val();
		var nSemester = $('#nSemester').val();
		$(this).next().html(thisval);
		$('#nLessonNo').val(nLessonNo);
		$.get("/WeiXin/Homework/ajaxResult/weixinid/"+weixinId+'/sClassTypeCode/'+sClassTypeCode+'/nSemester/'+nSemester+'/nLessonNo/'+nLessonNo,{},function(data){
			if(data){
				$('.myWork_detail').attr('style','display:block');
				$('#content').html('');
				var result  = $.parseJSON(data);
				$('#counScore').html(parseFloat(result.countScore)+'分');
				$('#yourScore').html(result.rollInfo.score+'分');
				$('#avgScore').html(result.am.avgscore+'分');
				$('#maxScore').html(result.am.maxscore+'分');
				$('#timeOrder').html(result.myTimeOrder.order_by);
				$('#scoreOrder').html(result.myScoreOrder.order_by);
				$('#result').html('');
				var list = result['rollList'];
				var arr = {'a':'优','b':'良','c':'中','d':'有待提高','e':'没有完成','A':'优','B':'良','C':'中','D':'有待提高','E':'没有完成'};
				for(var a=0;a<list.length;a++){
					var tmptype = '';
					if(list[a]['type'] == '3'){
						tmptype = '.'+arr[list[a]['StudentAnswer']];
					}
					//$('#result').append('<h2>第'+list[a]['nQuestionIndex']+'题： <span class="total">满分：'+list[a]['tmpScore']+'分　得分 ');
					if(list[a]['tmpScore']>list[a]['lscore']){
						$('#result').append('<h2>第'+list[a]['nQuestionIndex']+'题： <span class="total">满分：'+list[a]['tmpScore']+'分　得分 <font color="#FF0000">'+list[a]['lscore']+'</font>分</span></h2><p>对应讲次：'+list[a]['example']+'</p><p>正确答案：'+list[a]['Answer']+'</p><p class="cRed">你的答案：'+list[a]['StudentAnswer']+tmptype+'</p>');
					}else{
						$('#result').append('<h2>第'+list[a]['nQuestionIndex']+'题： <span class="total">满分：'+list[a]['tmpScore']+'分　得分 '+list[a]['lscore']+'分</span></h2><p>对应讲次：'+list[a]['example']+'</p><p>正确答案：'+list[a]['Answer']+'</p><p>你的答案：'+list[a]['StudentAnswer']+tmptype+'</p>');
					}
					$('#result').append('<div class="button"><a href="/WeiXin/Homework/preview/weixinid/'+weixinId+'/img/'+list[a]['baseUrl']+'" class="btn btn-green">查看原题</a></div>')
					//$('#result').append('分</span></h2><p>对应讲次：'+list[a]['example']+'</p><p>正确答案：'+list[a]['Answer']+'</p><p class="cRed">你的答案：'+list[a]['StudentAnswer']+'</p>')
				}
			}else{
				$('#content').html('该讲次不需要上传作业，或您未提交');
				$('.myWork_detail').attr('style','display:none');
			}
		});
	})
	//查看客观详情
	$('#Details').live('click',function(){
		var weixinId = $('#weixinId').val();
		var nSemester = $('#nSemester').val();
		var sClassTypeCode = $('#sClassTypeCode').val();
		var nLessonNo = $('#lessonNo').val();
		if(!weixinId || !sClassTypeCode || !nLessonNo){
			alert('参数有误，请刷新页面后重新尝试');
		}else{
			window.location.href="/WeiXin/Homework/myHomework/weixinid/"+weixinId+"/sClassTypeCode/"+sClassTypeCode+'/nSemester/'+nSemester+'/nLessonNo/'+nLessonNo+'/t/1';
		}
	})
	//主观作业选择讲次
	$('#subSelectLesson').change(function() {
		var thisval = $(this).find('option:selected').text();
		var nLessonNo = $(this).find('option:selected').val();
		var weixinId = $('#weixinId').val();
		var sClassTypeCode = $('#sClassTypeCode').val();
		var nSemester = $('#nSemester').val();
		$(this).next().html(thisval);
		$('#nLessonNo').val(nLessonNo);
		$.get("/WeiXin/Homework/ajaxResult/weixinid/"+weixinId+'/sClassTypeCode/'+sClassTypeCode+'/nSemester/'+nSemester+'/nLessonNo/'+nLessonNo+'/t/2',{},function(data){
			if(data){
				$('.myWork_detail').attr('style','display:block');
				$('#content').html('');
				var result  = $.parseJSON(data);
				$('#counScore').html(parseFloat(result.countScore)+'分');
				$('#yourScore').html(result.rollInfo.subScore+'分');
				$('#avgScore').html(result.am.avgscore+'分');
				$('#maxScore').html(result.am.maxscore+'分');
				$('#timeOrder').html(result.myTimeOrder.order_by);
				$('#scoreOrder').html(result.myScoreOrder.order_by);
				$('#result').html('');
				var list = result['rollList'];
				for(var a=0;a<list.length;a++){
					$('#result').append('<h2>第'+list[a]['nQuestionIndex']+'题： <span class="total">满分：'+list[a]['tmpScore']+'分　得分 '+list[a]['lscore']+'分</span></h2><p>对应讲次：'+list[a]['example']+'</p><p>正确答案：'+list[a]['Answer']+'</p><p class="cRed">教师点评：'+list[a]['content']+'</p>')
				}
			}else{
				$('#content').html('该讲次不需要上传作业，或您未提交');
				$('.myWork_detail').attr('style','display:none');
			}
		});
	})
	//查看主观详情
	$('#subDetails').live('click',function(){
		var weixinId = $('#weixinId').val();
		var sClassTypeCode = $('#sClassTypeCode').val();
		var nSemester = $('#nSemester').val();
		var nLessonNo = $('#lessonNo').val();
		if(!weixinId || !sClassTypeCode || !nLessonNo){
			alert('参数有误，请刷新页面后重新尝试');
		}else{
			window.location.href="/WeiXin/Homework/myHomework/weixinid/"+weixinId+"/sClassTypeCode/"+sClassTypeCode+'/nSemester/'+nSemester+'/nLessonNo/'+nLessonNo+'/t/2';
		}
	})
	//答题
	$('#subAnswer').live('click',function(){
		var weixinId = $('#weixinId').val();
		var sClassTypeCode = $('#sClassTypeCode').val();
		window.location.href="/WeiXin/Homework/subAnswer/weixinid/"+weixinId+"/sClassTypeCode/"+sClassTypeCode;
	})
	//选择题号判断是否需要提交
	$("#diifjbox").find("a").click(function(){
		var type = $('#type').val();
		var status = checkNull(type);
		if(status === true){
			var hf = $(this).attr('href');
			var hfarr = hf.split('/');
			$('#go').val(hfarr[hfarr.length-1]);
			$(this).attr('href','javascript:void(0)');
			$('#myForm').submit();
		}
	})
	//提交
	$('#btonsumb').click(function(){
		var type = $('#type').val();
		var status = checkNull(type);
		if(status === true){
			$('#myForm').submit();
		}else{
			alert('请先将答案填写完整');
		}
	})

});
/*
function morehide(event) {
	var a = event.target.getAttribute("class");
	if (a != 'headerTitle' || (a != 'headerTitle' && a == 'menuIcon')) {
		$('#header .mselect').hide();
	}
	if (a != 'menuIcon' || (a == 'headerTitle' && a != 'menuIcon')) {
		$('#floatMenu .subMenu').animate({
			opacity: 0, right: '80px'
		},200, function() {
			$('#floatMenu .subMenu').hide();
		});
	}
};
*/

// Header && FloatMenu
var touchend = function(e) {
	var e=window.event || event;
    if (e.target.className === 'headerTitle') {
		$('#header .mselect').slideDown(200);
		$('#floatMenu .subMenu').animate( { opacity: 0, right: '80px' } ,200, function() { $('#floatMenu .subMenu').hide(); } );
    }
    else if (e.target.className === 'menuIcon') {
    	$('#header .mselect').slideUp(100);
		$('#floatMenu .subMenu').show().animate({
			opacity: 1, right: '55px'
		},300);
    }
    else if (e.target.className === 'floatLink' || e.target.className === 'headerLink') {
    }
    else {
    	$('#header .mselect').slideUp(100);
    	$('#floatMenu .subMenu').animate( { opacity: 0, right: '80px' } ,200, function() {	$('#floatMenu .subMenu').hide(); } );
    }
};
document.addEventListener('touchend', touchend);

/*
// iScroll
var myScroll;
function loaded() {
	myScroll = new iScroll('wrapper', {
		useTransform: false,
		onBeforeScrollStart: function (e) {
			var target = e.target;
			while (target.nodeType != 1) target = target.parentNode;

			if (target.tagName != 'SELECT' && target.tagName != 'INPUT' && target.tagName != 'TEXTAREA')
				e.preventDefault();
		}
	});
}
document.addEventListener('touchmove', function (e) { e.preventDefault(); }, false);
document.addEventListener('DOMContentLoaded', function () { setTimeout(loaded, 200); }, false);

*/