$(function(){
	var Numb = $("#listcount").val();
	$("#addbtn").click(function(){
		Numb = parseInt(Numb) + 1;
		$("#vote").append('<div class="vote'+Numb+'"><br><input type="button" value="删除该组" class="addv"><li><label>第'+Numb+'组配置:</label><label>单选<input type="radio" name="vote[No'+Numb+'][type]" value="1" checked="checked"/></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label>多选<input type="radio" name="vote[No'+Numb+'][type]" value="2"/></label></li><li><label>标题:</label><label><input type="text" name="vote[No'+Numb+'][title]" class="hidd"/>*</label></li><li><label>选项:</label><label><input type="text" name="vote[No'+Numb+'][voptions]" class="hidd"/>*请将选项用逗号隔开</label></li></div>');
	})
	$('.addv').live('click', function() {
		$(this).parent().remove();
		Numb = Numb - 1;
	});
	$("#sub").click(function(){
		/*获取单选按钮的值*/
		var len = $("#len").val();
		var arr = [];
		for(var a=0;a<len;a++){
			var mm = $("input[name='modular_vote"+a+"']").attr('type');
			var listid = $("#voteid"+a).val();
			//var arr[a] = new Array();
			 arr.push([]);
			if(mm == 'radio'){
				var valradio = $("input[name='modular_vote"+a+"']:checked").val();
				arr[a][1] = valradio;
				arr[a][0] = listid;
			}else if(mm == 'checkbox'){
				var s = '';
				var valradio = $("input[name='modular_vote"+a+"']:checked").each(function(){
					if($(this).val() == 'undefined' || $(this).val() == ''){}else{
						s += $(this).val();
						s += ',';
						arr[a][1] = s.substring(0,s.length-1);
						arr[a][0] = listid;
					}
				});
			}
		}
		var infoid = $('#infoid').val();
		if(infoid<1){alert('提交错误!')}else{
			$.ajax({
	  	        url:"http://eap.local/modular/modular_vote/voteadd",  
	  	        dataType:'jsonp',  
	  	        data:{'_post':arr,'_infoid':infoid},
	  	        jsonp:'callback',  
	  	        success:function(result) { 
	  	        	alert(result);
	  	        },
	  	    });
		}
	})
});
	function mysubmit(){
		var inp = $("input[class='hidd']");
		var status = true;
		for (var i=0;i<inp.length;i++){
			if(inp.eq(i).val() == ''){
				status = false;
				alert('带*号选项为必填项,请正确填写');
				return false;
			}
		}
	}
	//复制
	function copyUrl2(){
		var Url2=document.getElementById("biao1");
		Url2.select();
		document.execCommand("Copy");alert("已复制好，可贴粘。");
	}