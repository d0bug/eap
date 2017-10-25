var isIe= false;
if(!+[1,]){
	isIe= true;
}
//设置select的可见状态
function setSelectState(state){
	var objl=document.getElementsByTagName('select');
	for(var i=0;i<objl.length;i++)
	{
		objl[i].style.visibility=state;
	}
}
function mousePosition(ev){
	if(ev.pageX || ev.pageY)
	{
		return {x:ev.pageX, y:ev.pageY};
	}
	return {
		x:ev.clientX + document.body.scrollLeft - document.body.clientLeft,y:ev.clientY + document.body.scrollTop - document.body.clientTop
	};
}
//弹出方法
function showMessageBox(wTitle,content,pos,wWidth,is_reload){
	closeWindow();
	var bWidth=parseInt(document.documentElement.scrollWidth);
	var bHeight=parseInt(document.documentElement.scrollHeight);
	if(isIe){
		setSelectState('hidden');
	}
	var back=document.createElement("div");
	back.id="back";
	var styleStr="top:0px;left:0px;position:absolute;background:#666;width:"+bWidth+"px;height:"+bHeight+"px;";
	styleStr+=(isIe)?"filter:alpha(opacity=0);":"opacity:0;";
	back.style.cssText=styleStr;
	document.body.appendChild(back);
	showBackground(back,50);
	var mesW=document.createElement("div");
	mesW.id="mesWindow";
	//mesW.className="mesWindow";
	mesW.innerHTML="<div class=\"mesWindowBox\" style=\"width: "+wWidth+"px;\"><div class='mesWindowTop'><table width='100%' height='100%'><tr><td>"+wTitle+"</td><td style='width:10px;'><input type='button' onclick='closeWindow("+is_reload+");' title='关闭窗口' class='close greenbtn' value='关闭' /></td></tr></table></div><div class='mesWindowContent' id='mesWindowContent'>"+content+"</div><div class='mesWindowBottom'></div></div>";
	var v_top=50;
	v_top+=document.documentElement.scrollTop;
	//alert(mesW.clientHeight);
	//return false;
	styleStr="top:15%;position:fixed;width:100%;z-index:9999;";
	mesW.style.cssText=styleStr;
	document.body.appendChild(mesW);
}
//让背景渐渐变暗
function showBackground(obj,endInt){
	if(isIe){
		obj.filters.alpha.opacity+=5;
		if(obj.filters.alpha.opacity<endInt){
			setTimeout(function(){showBackground(obj,endInt)},5);
		}
	}else{
		var al=parseFloat(obj.style.opacity);al+=0.05;
		obj.style.opacity=al;
		if(al<(endInt/100))
		{setTimeout(function(){showBackground(obj,endInt)},5);}
	}
}
//关闭窗口
function closeWindow(is_reload){
	if(document.getElementById('back')!=null){
		document.getElementById('back').parentNode.removeChild(document.getElementById('back'));
	}
	if(document.getElementById('mesWindow')!=null){
		document.getElementById('mesWindow').parentNode.removeChild(document.getElementById('mesWindow'));
		if(is_reload == 1){
			window.parent.frames[0].location.reload();
		}
	}
	if(isIe){
		setSelectState('');
	}
	
}


