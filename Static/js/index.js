var banner_index = 2,prv_banner_index=1;
var banner_num ;
var s ;
var delayTime ;
var obj ;
$(document).ready(function(){
	
	$(".banner_ctrl a").hover(function(){
		$(this).fadeTo(100,1);
		},function(){
		$(this).fadeTo(100,.5);
	});
			
	var tNum=$(".flow_img .listflow li").length-1;
	var nNum=0;		
	$(".banner_ctrl .prve").click(function(){
		(nNum-1)<0?n2=tNum:n2=nNum-1;
		bSwitch(nNum,n2);
		nNum=n2;
	});	
	$(".banner_ctrl .next").click(function(){
		(nNum+1)>tNum?n2=0:n2=nNum+1;
		bSwitch(nNum,n2);
		nNum=n2;
	});
	function bSwitch(nNum,n2){
		$(".flow_img .listflow li:eq("+nNum+")").fadeOut();
		$(".flow_img .listflow li:eq("+n2+")").fadeIn();
	};
							
	$(function(){
		var switchTime;
	 	$(".flow_img").hover(function(){
			clearInterval(switchTime);
		},function(){
		switchTime = setInterval(function(){
			(nNum+1)>tNum?n2=0:n2=nNum+1;
			bSwitch(nNum,n2);
			nNum=n2;
		},8000);
		}).trigger("mouseleave");
	});
	
	$(".notice_box").animate({bottom:0});
	$(".notice_box_t .close").click(function(){
		$(".notice_box").fadeOut();
	});

});

function delayHide(){
	$(obj).children(".s_nav").removeClass("fade_in");
	if(delayTime)
		clearTimeout(delayTime);
	delayTime = null;
	obj = null;
}
