$(function(){
	var swtval=$(window).width()-60;
	$("#selwidth").width(swtval);
	$("#swt").width(swtval-18);

	$("#seltitle").click(function(){
		$("#diifjbox").toggle();
	});

	$("#cancel").click(function(){
		$("#labelbox").hide();
		$("#cancel").hide();
		$("#answertext").val("");
	});

	$("#upexplanation").click(function(){
		$("#zzblock").show().height($(document.body).outerHeight(true));
		$("#zdplay").show();
	});

	$("#closebut").click(function(){
		$("#zzblock").hide();
		$("#zdplay").hide();
	});

	$("#updatebutton").click(function(){
		$("#pic").focus();
	});

	$('#answertextadd').click(function() {
		var n = $("#answertextbox").children("label").length;
		if( n < 7){
			$("#answertextbox").append("<label class='labelbox'><div class='tar fl wb20 fline'></div><div class='lc6c2ca answertext mt10'>"+
				  				       "<input type='text' name='name' value='' style='height:32px;line-height:32px'/></div>"+
				  			           "<i class='fl w20 cancel' id='cancel' style='margin-top:17px;margin-left:15px'><img src='/images/img/cancel.png' class='imgth' /></i></label>");
		}else{
			alert('您上传的答案太多啦');
		}
	});

	$('.answertextbox .labelbox i').live('click', function() {
		$(this).parent().remove();
	});
})

function showTip(id,type=''){
    var idarr=id.split("input");
	var idval=$("#"+id).val();
	if(type != 'ios'){
		$("#boxul"+idarr[1]).append("<li><div class='tal wb70 ml15'>您选择的图片是：</div><div class='tal ml15'>" + idval +"</div><div class='tal ml15'>点击下方确认上传作业↓↓↓</div></li>");
		//imageBoxul.innerHTML = "<li><div class='tal wb70 ml15'>您选择的图片是：</div><div class='tal ml15'>" + idval +"</div><div class='tal ml15'>点击下方确认上传作业↓↓↓</div></li>";
		$("#button"+idarr[1]).show();
	}
}
/*
function showTip(id){

	$("#img"+id).show().html("<img src="+url+" id=image"+id+" class='imgth'/><img src='img/wrong.png' class='w25 pr zi1 fr' style='top:-40px;right:10px;margin-right:2px'>");
}
*/
//var imgurl = "";
/*
function showTip(node) {
    var imgURL = "";
    var file = null;
    if(node.files && node.files[0] ){
        file = node.files[0];
    }else if(node.files && node.files.item(0)) {
        file = node.files.item(0);
    }

    //这种获取方式支持IE10
    node.select();
    imgURL = document.selection.createRange().text;
    alert(imgURL);


    var textHtml = "<img src='"+imgURL+"'/>";     //创建img标签用于显示图片
    alert(textHtml);
    $(".mark").after(textHtml);
    return imgURL;
}*/
