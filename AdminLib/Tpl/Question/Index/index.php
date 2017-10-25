<!doctype html>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
<?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
<link href="/static/css/question.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/static/js/question.js"></script>
</head>
<body style="padding: 5px;">
<div class="easyui-layout" data-options="fit: true">
	<div region="north" style="height: 32px;" data-options="collapsible: false, border: false">
		<div align="center">
			<font face="Arial, Helvetica, Sans Serif" size="3" color="#0000FF"><b>
					<span id="clock">
							<SCRIPT LANGUAGE="JavaScript">
							<!-- Begin
							var dayarray=new Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday")
							var montharray=new Array("January","February","March","April","May","June","July","August","September","October","November","December")
							function getthedate(){
								var mydate=new Date()
								var year=mydate.getYear()
							if (year < 1000)
								year+=1900
								var day=mydate.getDay()
								var month=mydate.getMonth()
								var daym=mydate.getDate()
							if (daym<10)
								daym="0"+daym
								var hours=mydate.getHours()
								var minutes=mydate.getMinutes()
								var seconds=mydate.getSeconds()
								var dn="AM"
							if (hours>=12)
								dn="PM"
								if (hours>12){
								hours=hours-12
							}
							{
							 d = new Date();
							 Time24H = new Date();
							 Time24H.setTime(d.getTime() + (d.getTimezoneOffset()*60000) + 3600000);
							 InternetTime = Math.round((Time24H.getHours()*60+Time24H.getMinutes()) / 1.44);
							 if (InternetTime < 10) InternetTime = '00'+InternetTime;
							 else if (InternetTime < 100) InternetTime = '0'+InternetTime;
							}
							if (hours==0)
								hours=12
								if (minutes<=9)
								minutes="0"+minutes
							if (seconds<=9)
								seconds="0"+seconds
							//change font size here
								var cdate=dayarray[day]+", "+montharray[month]+" "+daym+" "+year+" | "+hours+":"+minutes+":"+seconds+" "+dn+""
							if (document.all)
								document.all.clock.innerHTML=cdate
							else if (document.getElementById)
								document.getElementById("clock").innerHTML=cdate
							else
								document.write(cdate)
							}
							if (!document.all&&!document.getElementById)
								getthedate()
							function goforit(){
							if (document.all||document.getElementById)
								setInterval("getthedate()",1000)
							}
							window.onload=goforit
						//  End -->
						</script>
					</span>
				</b>
			</font>
		</div>
	</div>
	<div region="center" data-options="href: '/Question/Index/not_through_question_list', collapsible: false, border: false"></div>
</div>
</body>
</html>