/*网络宣传资料图片上传*/
$(function(){
	//教师头像
	$('#file_upload_avatar').uploadify({
	'auto'     : true,
	'removeTimeout' : 1,
	'swf'      : '/static/js/uploadify.swf',
	'uploader' : $("#uploadimg_url").val(),
	'method'   : 'post',
	'formData':{'width':'200','height':'200',type:'img',autocut:1},
	'buttonText' : '选择头像图片(200*200)',
	'width':'200',
	'multi'    : true,
	'fileTypeDesc' : 'Image Files',
	'fileTypeExts' : '*.gif; *.jpg; *.png',
	'fileSizeLimit' : '3072KB',
	'onUploadSuccess':function(file,data,response){
		var obj = eval('(' + data + ')');
		$("#avatar").val(obj.url);
		$("#view_img_one").html("<div class=\"pic\"><img src=\"/upload/"+obj.show_url+"\"></div><a href=\"#none\" onclick=\"del_img('"+obj.url+"','#view_img_one','#avatar','"+obj.delimg_url+"')\">删除</a>");
	}
	});

	//个人简介图片
	$('#file_upload_intro_img').uploadify({
	'auto'     : true,
	'removeTimeout' : 1,
	'swf'      : '/static/js/uploadify.swf',
	'uploader' : $("#uploadimg_url").val(),
	'method'   : 'post',
	'formData':{'width':'190','height':'160',type:'img',autocut:0},
	'buttonText' : '选择个人简介图片(190*160)',
	'width':'200',
	'multi'    : false,
	'fileTypeDesc' : 'Image Files',
	'fileTypeExts' : '*.gif; *.jpg; *.png',
	'fileSizeLimit' : '3072KB',
	'onUploadSuccess':function(file,data,response){
		var obj = eval('(' + data + ')');
		$("#intro_img").val(obj.url);
		$("#view_img_two").html("<div class=\"pic\"><img src=\"/upload/"+obj.show_url+"\"></div><a  href=\"#none\" onclick=\"del_img('"+obj.url+"','#view_img_two','#intro_img','"+obj.delimg_url+"')\">删除</a>");
	}
	});


	//教课风采图片
	$('#file_upload_teach_img').uploadify({
	'auto'     : true,
	'removeTimeout' : 1,
	'swf'      : '/static/js/uploadify.swf',
	'uploader' : $("#uploadimg_url").val(),
	'method'   : 'post',
	'formData':{'width':'190','height':'160',type:'img',autocut:0},
	'buttonText' : '选择教课风采图片(190*160)',
	'width':'200',
	'multi'    : false,
	'fileTypeDesc' : 'Image Files',
	'fileTypeExts' : '*.gif; *.jpg; *.png',
	'fileSizeLimit' : '3072KB',
	'onUploadSuccess':function(file,data,response){
		var obj = eval('(' + data + ')');
		$("#teach_img").val(obj.url);
		$("#view_img_three").html("<div class=\"pic\"><img src=\"/upload/"+obj.show_url+"\"></div><a href=\"#none\" onclick=\"del_img('"+obj.url+"','#view_img_three','#teach_img','"+obj.delimg_url+"')\">删除</a>");
	}
	});


	//教学心得图片
	$('#file_upload_experience_img').uploadify({
	'auto'     : true,
	'removeTimeout' : 1,
	'swf'      : '/static/js/uploadify.swf',
	'uploader' : $("#uploadimg_url").val(),
	'method'   : 'post',
	'formData':{'width':'190','height':'160',type:'img',autocut:0},
	'buttonText' : '选择教学心得图片(190*160)',
	'width':'200',
	'multi'    : false,
	'fileTypeDesc' : 'Image Files',
	'fileTypeExts' : '*.gif; *.jpg; *.png',
	'fileSizeLimit' : '3072KB',
	'onUploadSuccess':function(file,data,response){
		var obj = eval('(' + data + ')');
		$("#experience_img").val(obj.url);
		$("#view_img_four").html("<div class=\"pic\"><img src=\"/upload/"+obj.show_url+"\"></div><a href=\"#none\" onclick=\"del_img('"+obj.url+"','#view_img_four','#experience_img','"+obj.delimg_url+"')\">删除</a>");
	}
	});
});
/*豆先生项目*/
$(function() {
	$('#upload_dou_question').uploadify({
	'auto'     : true,
	'removeTimeout' : 1,
	'swf'      : '/static/js/uploadify.swf',
	'uploader' : $("#uploadimg_url").val(),
	'method'   : 'post',
	'formData':{},
	'buttonText' : '点击上传',
	'width':'200',
	'multi'    : true,
	'fileTypeDesc' : 'Image Files',
	'fileTypeExts' : '*.gif; *.jpg; *.png',
	'fileSizeLimit' : '500KB',
	'onUploadSuccess':function(file,data,response){
		var obj = eval('(' + data + ')');
		//$("#picture").val(obj.url);
		//$("#view_picture").html("<img src=\"/upload/"+obj.show_url+"\">&nbsp;&nbsp;<a href=\"#\" onclick=\"del_img('"+obj.url+"','#view_picture','#picture','"+$("#delimg_url").val()+"')\">删除</a>");


		$('#question').val(obj.savepath+obj.savename);
		// alert(data);
		//alert(data[]);

	}
	});


	$('#upload_dou_picture').uploadify({
	'auto'     : true,
	'removeTimeout' : 1,
	'swf'      : '/static/js/uploadify.swf',
	'uploader' : $("#uploadimg_url").val(),
	'method'   : 'post',
	'formData':{},
	'buttonText' : '点击上传',
	'width':'200',
	'multi'    : true,
	'fileTypeDesc' : 'Image Files',
	'fileTypeExts' : '*.gif; *.jpg; *.png',
	'fileSizeLimit' : '500KB',
	'onUploadSuccess':function(file,data,response){
		var obj = eval('(' + data + ')');
		//$("#picture").val(obj.url);
		//$("#view_picture").html("<img src=\"/upload/"+obj.show_url+"\">&nbsp;&nbsp;<a href=\"#\" onclick=\"del_img('"+obj.url+"','#view_picture','#picture','"+$("#delimg_url").val()+"')\">删除</a>");

		var letter = $('input[name="selectPic"]:checked').val();
		if(letter == '') {
			alert('必须选择一个上传题号');
			return false;
		}
		$('#answers'+letter).val(obj.savepath+obj.savename);
		// alert(data);
		//alert(data[]);

	}
	});
})

/*上传讲义资料*/
$(function() {
	//讲义缩略图
	$('#upload_handouts_picture').uploadify({
	'auto'     : true,
	'removeTimeout' : 1,
	'swf'      : '/static/js/uploadify.swf',
	'uploader' : $("#uploadimg_url").val(),
	'method'   : 'post',
	'formData':{'width':'160','height':'100',type:'img','action':"'"+$("#action").val()+"'",'hid':"'"+$("#hid").val()+"'"},
	'buttonText' : '点击选择缩略图(160*100)',
	'width':'200',
	'multi'    : false,
	'fileTypeDesc' : 'Image Files',
	'fileTypeExts' : '*.gif; *.jpg; *.png',
	'fileSizeLimit' : '500KB',
	'onUploadSuccess':function(file,data,response){
		var obj = eval('(' + data + ')');
		$("#picture").val(obj.url);
		$("#view_picture").html("<img src=\"/upload/"+obj.show_url+"\">&nbsp;&nbsp;<a href=\"#none\" onclick=\"del_img('"+obj.url+"','#view_picture','#picture','"+$("#delimg_url").val()+"')\">删除</a>");
	}
	});


	//教师版讲义
	$('#upload_teacher_version_0').uploadify({
	'auto'     : true,
	'removeTimeout' : 1,
	'swf'      : '/static/js/uploadify.swf',
	'uploader' : $("#uploadimg_url").val(),
	'method'   : 'post',
	'formData':{'preview':1,'width':'160','height':'100',type:'file','action':"'"+$("#action").val()+"'",'hid':"'"+$("#hid").val()+"'",'prename':$("#subject").find("option:selected").text()+""+$("#grade").find("option:selected").text()+"_"+$("#knowledge").find("option:selected").text(),is_realname:1},
	'buttonText' : '点击选择教师版讲义',
	'width':'200',
	'multi'    : false,
	'fileTypeDesc' : 'pdf Files',
	'fileTypeExts' : '*.pdf;*.ppt;*.pptx',
	'fileSizeLimit' : '10240KB',
	'onUploadSuccess':function(file,data,response){
		var obj = eval('(' + data + ')');
		$("#teacher_version_msg_0").html(obj.status);
		$("#teacher_version_0").val(obj.url);
		$("#teacher_version_preview_0").val(obj.preview_url);
		$("#view_teacher_file_0").html("<a href=\"#\">"+obj.show_url+"</a>&nbsp;&nbsp;<a href=\"#none\" onclick=\"del_img('"+obj.url+"','#view_teacher_file_0','#teacher_version_0','"+$("#delimg_url").val()+"')\">删除</a>");
	}
	});

	//学生版讲义
	$('#upload_student_version_0').uploadify({
	'auto'     : true,
	'removeTimeout' : 1,
	'swf'      : '/static/js/uploadify.swf',
	'uploader' : $("#uploadimg_url").val(),
	'method'   : 'post',
	'formData':{'preview':1,'width':'160','height':'100',type:'file','action':"'"+$("#action").val()+"'",'hid':"'"+$("#hid").val()+"'",'prename':$("#subject").find("option:selected").text()+""+$("#grade").find("option:selected").text()+"_"+$("#knowledge").find("option:selected").text(),is_realname:1},
	'buttonText' : '点击选择学生版讲义',
	'width':'200',
	'multi'    : false,
	'fileTypeDesc' : 'pdf Files',
	'fileTypeExts' : '*.doc; *.docx;*.ppt;*.pptx',
	'fileSizeLimit' : '10240KB',
	'onUploadSuccess':function(file,data,response){
		var obj = eval('(' + data + ')');
		$("#student_version_msg_0").html(obj.status);
		$("#student_version_0").val(obj.url);
		$("#student_version_preview_0").val(obj.preview_url);
		$("#student_version_realname_0").val(obj.realname);
		$("#view_student_file_0").html("<a href=\"#\">"+obj.show_url+"</a>&nbsp;&nbsp;<a href=\"#none\" onclick=\"del_img('"+obj.url+"','#view_student_file_0','#student_version_0','"+$("#delimg_url").val()+"')\">删除</a>");
	}
	});

	//试题库文档和教师上传文档
	$('#upload_item_bank').uploadify({
	'auto'     : true,
	'removeTimeout' : 1,
	'swf'      : '/static/js/uploadify.swf',
	'uploader' : $("#uploadimg_url").val(),
	'method'   : 'post',
	'formData':{'preview':$("#is_preview").val(),'width':'160','height':'100',type:'file','action':"'"+$("#action").val()+"'",'hid':"'"+$("#hid").val()+"'",'prename':$("#subject").find("option:selected").text()+''+$("#grade").find("option:selected").text()+'_'+$("#knowledge").find("option:selected").text(),is_realname:1},
	'buttonText' : '点击选择文档',
	'width':'200',
	'multi'    : false,
	'fileTypeDesc' : 'pdf Files',
	'fileTypeExts' : '*.doc;*.docx; *.pdf; *.ppt; *.xls',
	'fileSizeLimit' : '10240KB',
	'onUploadSuccess':function(file,data,response){
		var obj = eval('(' + data + ')');
		$("#upload_itembank_msg").html(obj.status);
		$("#teacher_version").val(obj.url);
		$("#teacher_version_preview").val(obj.preview_url);
		$("#view_teacher_file").html("<a href=\"#none\">"+obj.show_url+"</a>&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"del_img('"+obj.url+"','#view_teacher_file','#teacher_version','"+$("#delimg_url").val()+"')\">删除</a>");
	}
	});

	var z=$("#img_num").val();
	$('#upload_essayImg').uploadify({
	'auto'     : false,
	'removeTimeout' : 1,
	'swf'      : '/static/js/uploadify.swf',
	'uploader' : $("#uploadimg_url").val(),
	'method'   : 'post',
	'formData':{type:'img',is_realname:0},
	'buttonText' : '选择作文照片',
	'width':'200',
	'multi'    : true,
	//'uploadLimit' : 10,
	'fileTypeDesc' : 'Image Files',
	'fileTypeExts' : '*.gif; *.jpg; *.png',
	'fileSizeLimit' : '15360KB',
	'onUploadSuccess':function(file,data,response){
		var obj2 = eval('(' + data + ')');
		$("#essayImgs").val($("#essayImgs").val()+obj2.url+'|');
		$("#preview").html($("#preview").html()+"<li id=\"pre_"+Number(z)+"\"><img src=\"/upload/"+obj2.show_url+"\" width=\"200\" height=\"200\"><div class=\"img_name\">"+obj2.real+"</div><div class=\"delete\"><a href=\"javascript:void(0)\" onclick=\"return del_img('"+obj2.delimg_url+"','"+obj2.url+"','"+Number(z)+"','"+$("#id").val()+"')\">删除</a></div></li>");
		z=z+1;
	},
	'onError': function(event, queueID, fileObj) {
		alert("照片:" + fileObj.name + "上传失败");
	},
	});

	$('#file_student_avatar').uploadify({
	'auto'     : true,
	'removeTimeout' : 1,
	'swf'      : '/static/js/uploadify.swf',
	'uploader' : $("#uploadimg_url").val(),
	'method'   : 'post',
	'formData':{type:'img',width:'160',height:'200',is_realname:0},
	'buttonText' : '选择头像照片',
	'width':'160',
	'multi'    : false,
	'fileTypeDesc' : 'Image Files',
	'fileTypeExts' : '*.gif; *.jpg; *.png',
	'fileSizeLimit' : '15360KB',
	'onUploadSuccess':function(file,data,response){
		var obj2 = eval('(' + data + ')');
		$('#avatar').val(obj2.url);
		$("#pre_avatar").html("<img src=\"/upload/"+obj2.show_url+"\" width=\"160\" height=\"200\"><a href=\"javascript:void(0)\" onclick=\"return del_img_avatar('"+obj2.url+"','#avatar','#preview','"+obj2.delimg_url+"')\">删除</a>");

	},
	'onError': function(event, queueID, fileObj) {
		alert("照片:" + fileObj.name + "上传失败");
	},
	});




	//我的学员：讲义文档
	var x=$("#handouts_count").val();
	$('#upload_handouts').uploadify({
	'auto'     : true,
	'removeTimeout' : 1,
	'swf'      : '/static/js/uploadify.swf',
	'uploader' : $("#uploadimg_url").val(),
	'method'   : 'post',
	'formData':{'preview':1,'width':'800','height':'1000',type:'file','action':"'"+$("#action").val()+"'",'hid':"'"+$("#hid").val()+"'",is_realname:0},
	'buttonText' : '讲义文档',
	'width':'200',
	'multi'    : true,
	'fileTypeDesc' : 'doc Files',
	'fileTypeExts' : '*.doc; *.docx; *.pdf; *.ppt; *.xls;*.jpg;*.gif;*.png',
	'fileSizeLimit' : '10240KB',
	'onUploadSuccess':function(file,data,response){
		var obj = eval('(' + data + ')');
		//$("#view_handouts_file").html("<a href=\"#\">"+obj.show_url+"</a>&nbsp;&nbsp;<a href=\"#none\" onclick=\"del_img('"+obj.url+"','#view_handouts_file','#handouts_url','"+$("#delimg_url").val()+"')\">删除</a>");
		$("#preview_handouts").append('<span id="view_handouts_file_'+x+'" class="view_file"><a href="#">'+obj.show_url+'</a>&nbsp;&nbsp;<a href="#none" onclick="del_img(\''+obj.url+'\',\'#view_handouts_file_'+x+'\',\'#handouts_url_'+x+'\',\''+$("#delimg_url").val()+'\')\">删除</a></span><label id="handouts_msg_'+x+'" class="success"></label><input type="hidden" id="handouts_url_'+x+'" name ="handouts_url[]" value="'+obj.url+'"><br>');
		x=parseInt(x)+1;
		$("#handouts_count").val(x);
	}
	});

	//我的学员：测试卷文档
	var y=$("#itembank_count").val();
	$('#upload_itembank').uploadify({
	'auto'     : true,
	'removeTimeout' : 1,
	'swf'      : '/static/js/uploadify.swf',
	'uploader' : $("#uploadimg_url").val(),
	'method'   : 'post',
	'formData':{'preview':1,'width':'800','height':'1000',type:'file','action':"'"+$("#action").val()+"'",'hid':"'"+$("#hid").val()+"'",is_realname:0},
	'buttonText' : '测试卷文档',
	'width':'200',
	'multi'    : true,
	'fileTypeDesc' : 'pdf Files',
	'fileTypeExts' : '*.doc; *.docx; *.pdf; *.ppt; *.xls; *.jpg; *.gif; *.png',
	'fileSizeLimit' : '10240KB',
	'onUploadSuccess':function(file,data,response){
		var obj = eval('(' + data + ')');
		//$("#itembank_msg").html(obj.status);
		//$("#itembank_url").val(obj.url);
		//$("#view_itembank_file").html("<a href=\"#\">"+obj.show_url+"</a>&nbsp;&nbsp;<a href=\"#none\" onclick=\"del_img('"+obj.url+"','#view_itembank_file','#itembank_url','"+$("#delimg_url").val()+"')\">删除</a>");
		$("#preview_itembank").append('<span id="view_itembank_file_'+y+'" class="view_file"><a href="#">'+obj.show_url+'</a>&nbsp;&nbsp;<a href="#none" onclick="del_img(\''+obj.url+'\',\'#view_itembank_file_'+y+'\',\'#itembank_url_'+y+'\',\''+$("#delimg_url").val()+'\')\">删除</a></span><label id="itembank_msg_'+y+'" class="success"></label><input type="hidden" id="itembank_url_'+y+'" name ="itembank_url[]" value="'+obj.url+'"><br>');
		y=parseInt(y)+1;
		$("#itembank_count").val(y);
	}
	});
});


/*早培神测*/
$(function() {
	$('#file_upload_img').uploadify({
	'auto'     : true,
	'removeTimeout' : 1,
	'swf'      : '/static/js/uploadify.swf',
	'uploader' : $("#upload_url").val(),
	'method'   : 'post',
	'formData':{'width':'250','height':'200',type:'img'},
	'buttonText' : '选择图片',
	'width':'200',
	'multi'    : false,
	'fileTypeDesc' : 'Image Files',
	'fileTypeExts' : '*.gif; *.jpg; *.png',
	'fileSizeLimit' : '3072KB',
	'onUploadSuccess':function(file,data,response){
		var obj = eval('(' + data + ')');
		$("#img").val(obj.url);
		$("#view_img").html("<div class=\"pic\"><img src=\"/Upload/"+obj.show_url+"\" width=\"300\" height=\"250\"></div><a href=\"#none\" onclick=\"del_img('"+obj.url+"','#view_img','#img','"+obj.delimg_url+"')\">删除</a>");
	}
	});
})


$(function() {
	//上传视频
	$('#upload_video').uploadify({
		'auto'     : true,
		'removeTimeout' : 1,
		'swf'      : '/static/js/uploadify.swf',
		'uploader' : $("#upload_url").val(),
		'method'   : 'post',
		'formData':{'preview':0,type:'file',is_realname:1,id:new Date()},
		'buttonText' : '点击上传视频',
		'width':'200',
		'multi'    : false,
		'fileTypeDesc' : 'video Files',
		'fileTypeExts' : '*.flv;',
		'fileSizeLimit' : '512000KB',
		'successTimeout' : 66660,
		'onUploadSuccess':function(file,data,response){
			var obj = eval('(' + data + ')');
			$("#upload_video_msg").html(obj.status);
			$("#video_url").val(obj.url);
			$("#view_video").html("<a href=\"#none\">"+obj.show_url+"</a>&nbsp;&nbsp;<a href=\"#none\" onclick=\"del_url2('"+obj.url+"','#view_video','#video_url','"+$("#del_url").val()+"')\">删除</a>");
		}
	});
	
	//视频缩略图
	$('#upload_video_img').uploadify({
		'auto'     : true,
		'removeTimeout' : 1,
		'swf'      : '/static/js/uploadify.swf',
		'uploader' : $("#upload_url").val(),
		'method'   : 'post',
		'formData':{'width':'160','height':'100',type:'img',is_realname:1,id:new Date()},
		'buttonText' : '点击上传视频缩略图(160*100)',
		'width':'200',
		'multi'    : false,
		'fileTypeDesc' : 'Image Files',
		'fileTypeExts' : '*.gif; *.jpg; *.png',
		'fileSizeLimit' : '3072KB',
		'onUploadSuccess':function(file,data,response){
			var obj = eval('(' + data + ')');
			$("#video_img").val(obj.url);
			$("#view_video_img").html("<img src="+obj.show_url+" width=\"200\" height=\"100\">&nbsp;&nbsp;<a href=\"#none\" onclick=\"del_url2('"+obj.url+"','#view_video_img','#video_img','"+$("#del_url").val()+"')\">删除</a>");
		}
	});
});

/*竞赛测评试题图片*/
$(function() {
	$('#file_upload_evalimg').uploadify({
	'auto'     : true,
	'removeTimeout' : 1,
	'swf'      : '/static/js/uploadify.swf',
	'uploader' : $("#upload_url").val(),
	'method'   : 'post',
	'formData':{'width':'250','height':'200',type:'img'},
	'buttonText' : '选择图片',
	'width':'200',
	'multi'    : false,
	'fileTypeDesc' : 'Image Files',
	'fileTypeExts' : '*.gif; *.jpg; *.png',
	'fileSizeLimit' : '3072KB',
	'onUploadSuccess':function(file,data,response){
		var obj = eval('(' + data + ')');
		$("#img").val(obj.url);
		$("#view_img").html("<div class=\"pic\"><img src=\"/upload/"+obj.show_url+"\" width=\"300\" height=\"250\"></div><a href=\"#none\" onclick=\"del_img('"+obj.url+"','#view_img','#img','"+obj.delimg_url+"')\">删除</a>");
	}
	});
});

/*竞赛测评上传PDF文档*/
$(function() {
	$('#file_upload_document').uploadify({
		'auto'     : true,
		'removeTimeout' : 1,
		'swf'      : '/static/js/uploadify.swf',
		'uploader' : $("#upload_document").val(),
		'method'   : 'post',
		'formData':{'preview':0,type:'file',is_realname:1},
		'buttonText' : '选择PDF文档',
		'width':'200',
		'multi'    : true,
		'fileTypeDesc' : 'document Files',
		'fileTypeExts' : '*.doc;*.docx; *.pdf;*.PDF; *.ppt; *.xls',
		'fileSizeLimit' : '102400KB',
		'onUploadSuccess':function(file,data,response){
			var obj = eval('(' + data + ')');
			$("#upload_itembank_msg").html(obj.status);
			$("#document").val(obj.url);
			$("#view_document").html("<a href=\"#none\">"+obj.url+"</a>&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"del_file_document('"+obj.url+"','#view_document','#document','"+obj.delimg_url+"')\">删除</a>");
		}
	});
});
/*竞赛测评解析图片*/
$(function() {
	$('#file_upload_analysis').uploadify({
		'auto'     : true,
		'removeTimeout' : 1,
		'swf'      : '/static/js/uploadify.swf',
		'uploader' : $("#upload_analysis").val(),
		'method'   : 'post',
		'formData':{'width':'250','height':'200',type:'img'},
		'buttonText' : '选择图片',
		'width':'200',
		'multi'    : false,
		'fileTypeDesc' : 'Image Files',
		'fileTypeExts' : '*.gif; *.jpg; *.png',
		'fileSizeLimit' : '3072KB',
		'onUploadSuccess':function(file,data,response){
			var obj = eval('(' + data + ')');
			$("#analysis").val(obj.url);
			$("#view_analysis").html("<div class=\"pic\"><img src=\"/upload/"+obj.show_url+"\" width=\"300\" height=\"250\"></div><a href=\"#none\" onclick=\"del_img('"+obj.url+"','#view_analysis','#analysis','"+obj.delimg_url+"')\">删除</a>");
		}
	});
});



