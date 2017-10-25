<!doctype html>
<html>
	<head>
	<title><?php echo APP_CAPTION?>登录</title>
	<?php include(TMPL_PATH . '/Include/headerCommon.php');?>
        <?php include(TMPL_PATH . '/Include/bootstrap.php');?>
	<script type="text/javascript">
	$(function(){
		var myTab=$('#myTab span');
		myTab.click(function (e) {
			e.preventDefault();
			$(this).tab('show');
		})
		$('.captcha').click(function(){
			$('img.captcha').attr('src', $('img.captcha').attr('src').replace(/\?\d+$/, '') + '?' + (new Date()).getTime());
		})
	})
	</script>
	<style type="text/css">
	/*.topAdBox {margin-bottom: 15px;}
	.appBox {border-top:1px solid #E5E5E5;margin-top:10px;padding-top:20px;}
	.appBox table td {padding: 5px 0;}
	.appBox table a {margin: 0 5px;}
	fieldset .offset1 {font:bold 25px/50px 微软雅黑;}*/
	
	#loginbg{background:url(/static/images/eap_loginbg.jpg)  center 0; height:780px;padding-top: 300px;}
	#loginbox .btn-info{background:#f86c6b}
	#loginbox .btn-info:hover{background:#ec5756}
	#loginbox{width:600px;margin:0 auto ;background:#fff;border-radius:4px;padding:30px 0 20px 0;font-size: 16px;position: relative;}
	#loginbox .nav-tabs,#loginbox .nav-tabs li span{border:0}	
	#loginbox .nav-tabs li{width:50%;border-bottom:2px solid #ddd;text-align: center;font-size:24px;height:40px;color:#666;cursor:pointer;margin-top:10px}
	#loginbox .nav-tabs li.active{border-bottom:2px solid #39c;}
	#loginbox .nav-tabs li.active span{color:#39c}
    #loginbox .nav-tabs li span{display: block;}
	#loginbox .offset1 {margin-left: 75px;margin-top:30px}
	#loginbox .control-label{font-size:16px;}
	#loginbox .control-group .span2,#loginbox .control-group .span4,#loginbox .control-group .add-on{height:28px;line-height:28px;color:#333}
	#loginbox .control-group .add-on strong{font-size:14px;letter-spacing:1px;padding-right:24px;color:#666}
	#loginbox #user_type option{font-size:14px}
	#loginbox #usrName{width:132px}
	#loginboxtitle{width:760px;position:absolute;top: -95px;left:-75px;}
	#friendlink{width:580px;margin:300px auto 0  ;}
	#friendlink a{display:inline-block;margin:5px 20px 5px 5px;position: relative;cursor: pointer;}
	#friendlink a img{position: absolute;top:-130px;left:-4px; max-width:none;border-radius:4px;border:1px solid #ccc;display:none;}
	#friendlink a:hover img{display: block;}
	#friendlink a.selbar:hover::after,#friendlink .selbar:hover::before{position:absolute;right:40%;top:-8px;content:"";font-size:0;height:0;width:0;border-width:10px;border-style:solid;border-color:#CCC transparent transparent}
	.alert-danger, .alert-error {background-color: #f2dede;border-color: #eed3d7;color: #b94a48;margin:10px;}
	</style>
	</head>
	<body>
		<div id="loginbg">
			<div id="loginbox">
				<div id="loginboxtitle"><img src="/static/images/eap_loginbg2.png"></div>
				<ul class="nav nav-tabs" id="myTab">
		            <li class="active"><span href="#home">高思员工登录</span></li>
		            <li class=""><span href="#profile">兼职教师登录</span></li>
		        </ul>
		        <?php if($errors):?>
				<div class="alert alert-error">
					<a class="close" data-dismiss="alert">×</a>
					<ul>
					<?php foreach($errors as $errorMsg):?>
		               <li><?php echo $errorMsg?></li>
					<?php endforeach?>
					</ul>
				</div>
				<?php endif ?>
		        <div class="tab-content">
		            <div class="tab-pane active" id="home">
		                <form id="loginForm" class="form-horizontal" method="post" action="<?php echo U('System/User/login')?>">
		                <input name="tabPos" value="0" type="hidden">
		                <div class="control-group">
		                    <label class="control-label span2">邮件地址：</label>
		                        <div class="input-append">
		                                <input class="span2" name="uName" id="empName" required="true" placeholder="企邮用户名" type="text">
		                                <span class="add-on"><strong>@gaosiedu.com</strong></span>
		                        </div>
		                </div>
		                <div class="control-group">
		                    <label class="control-label span2">登录密码：</label>
		                    <input class="span4" name="uPass" required="true" placeholder="企邮密码" type="password">
		
		                </div>
		                <div class="control-group">
		                    <label class="control-label span2">验证码：</label>
		                        <div class="input-append">
		                            <input name="captcha" class="span2" required="true" placeholder="验证码" type="text">
		                            <span class="add-on">
		                                <img class="captcha" src="<?php echo U('Util/Image/captcha', array('key'=>$captchaKey))?>" /> <a href="javascript:void(0)" class="captcha">看不清？</a>
		                            </span>
		                        </div>
		                </div>
		                <div class="constrol-group offset1">
		                    <input class="btn btn-info btn-large span6" value="登 录" type="submit">
		                </div>
		                </form>
		            </div>
		            <div class="tab-pane" id="profile">
		                <form id="loginForm" class="form-horizontal" method="post" action="<?php echo U('System/User/login')?>">
		                <input name="tabPos" value="1" type="hidden">
		                <div class="control-group">
		                    <label class="control-label span2">用户账户：</label>
		                        <div class="input">
		                                <select name="user_type" id="user_type" class="span2" style="height:38px">
		                                    <?php foreach ($userTypes as $userType=>$typeName):?>
                                				<option value="<?php echo $userType?>"><?php echo $typeName?></option>
                                			<?php endforeach;?>
		                                </select>&nbsp;&nbsp;
		                                <input class="span2" name="uName" id="usrName" required="true" type="text">
		                        </div>
		                </div>
		                <div class="control-group">
		                    <label class="control-label span2">登录密码：</label>
		                    <input class="span4" name="uPass" required="true" placeholder="登录密码" type="password">
		                </div>
		                <div class="control-group">
		                    <label class="control-label span2">验证码：</label>
		                        <div class="input-append">
		                            <input name="captcha" class="span2" autocomplete="off" required="true" placeholder="验证码" type="text">
		                            <span class="add-on">
		                                <img class="captcha" src="<?php echo U('Util/Image/captcha', array('key'=>$captchaKey))?>" /> <a href="javascript:void(0)" class="captcha">看不清？</a>
		                            </span>
		                        </div>
		                </div>
		                <div class="constrol-group offset1">
		                    <input class="btn btn-info btn-large span6" value="登 录" type="submit">
		                </div>
		                </form>
		            </div>
		        </div>
			</div>
		
			<div id="friendlink">
				友情链接：
				<a class="selbar">高思1对1教师在线<img src="/static/images/vipteacher.jpg"></a>
				<a class="selbar">高思1对1学员APP<img src="/static/images/vipstudentapp.jpg"></a>
				<a target="_blank" href="http://www.gaosivip.com/">高思1对1官网</a>
				<a target="_blank" href="http://www.aitifen.com">爱提分官网</a>
			</div>
		
		</div>

	
	
	
	
	
	
	
	
	
	<!--<div style="width:800px;margin:15px auto 0">
		<div class="topAdBox"><a href="http://x.gaosiedu.com" target="_blank"><img src="/static/images/login_800x120.png" width="800" height="100" alt="" /></a></div>

		<fieldset>
		<legend><div class="offset1"><?php echo APP_CAPTION?></div></legend>

		<?php if($errors):?>
		<div class="alert alert-error">
			<a class="close" data-dismiss="alert">×</a>
			<ul>
			<?php foreach($errors as $errorMsg):?>
                            <li><?php echo $errorMsg?></li>
			<?php endforeach?>
			</ul>
		</div>
		<?php endif ?>
        <ul class="nav nav-tabs" id="myTab" style="margin-left:100px">
            <li class="active"><a href="#home">高思员工登录</a></li>
            <li><a href="#profile">兼职教师登录</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="home">
                <form id="loginForm" class="form-horizontal" method="post" action="<?php echo U('System/User/login')?>">
                <input type="hidden" name="tabPos" value="0" />
                <div class="control-group">
                    <label class="control-label span2">邮件地址：</label>
                        <div class="input-append">
                                <input class="span2" name="uName" id="empName" type="text" required="true" placeholder="企邮用户名">
                                <span class="add-on"><strong style="letter-spacing:3px">@gaosiedu.com</strong></span>
                        </div>
                </div>
                <div class="control-group">
                    <label class="control-label span2">登录密码：</label>
                    <input class="span4" name="uPass" type="password" required="true" placeholder="企邮密码">

                </div>
                <div class="control-group">
                    <label class="control-label span2">验证码：</label>
                        <div class="input-append">
                            <input name="captcha" class="span2" type="text" required="true" placeholder="验证码">
                            <span class="add-on">
                                <img class="captcha" src="<?php echo U('Util/Image/captcha', array('key'=>$captchaKey))?>" /> <a href="javascript:void(0)" class="captcha">看不清？</a>
                            </span>
                        </div>
                </div>
                <div class="constrol-group offset1">
                    <input type="submit" class="btn btn-info btn-large span6" value="登 录 系 统" />
                </div>
                </form>
            </div>
            <div class="tab-pane" id="profile">
                <form id="loginForm" class="form-horizontal" method="post" action="<?php echo U('System/User/login')?>">
                <input type="hidden" name="tabPos" value="1" />
                <div class="control-group">
                    <label class="control-label span2">用户账户：</label>
                        <div class="input">
                                <select name="user_type" id="user_type" class="span2">
                                <?php foreach ($userTypes as $userType=>$typeName):?>
                                <option value="<?php echo $userType?>"><?php echo $typeName?></option>
                                <?php endforeach;?>
                                </select>&nbsp;&nbsp;
                                <input class="span2" name="uName" type="text" id="usrName" required="true">
                        </div>
                </div>
                <div class="control-group">
                    <label class="control-label span2">登录密码：</label>
                    <input class="span4" name="uPass" type="password" required="true" placeholder="登录密码">
                </div>
                <div class="control-group">
                    <label class="control-label span2">验证码：</label>
                        <div class="input-append">
                            <input name="captcha" class="span2" type="text" autocomplete="off" required="true" placeholder="验证码">
                            <span class="add-on">
                                <img class="captcha" src="<?php echo U('Util/Image/captcha', array('key'=>$captchaKey))?>" /> <a href="javascript:void(0)" class="captcha">看不清？</a>
                            </span>
                        </div>
                </div>
                <div class="constrol-group offset1">
                    <input type="submit" class="btn btn-info btn-large span6" value="登 录 系 统" />
                </div>
                </form>
            </div>
        </div>



		</form>
		</fieldset>
		<div class="appBox">
			<p>我们为家长开发了很多有趣并实用的应用，希望能给家长带来更多的乐趣和便利。大家快来试用，并分享给好友吧！</p>
			<table>
				<tr>
					<td><strong>新浪应用：</strong></td>
					<td><a href="http://apps.weibo.com/gaosiedu" target="_blank">小升初日程表</a> |
						<a href="http://apps.weibo.com/haizidezy" target="_blank">子女未来职业测试</a> |
						<a href="http://apps.weibo.com/xinlingjuli" target="_blank">测试父母与子女的心灵距离</a>
					</td>
				</tr>
				<tr>
					<td><strong>官网工具：</strong></td>
					<td><a href="http://www.gaosiedu.com/paiwei/" target="_blank">小升初派位查询</a> |
						<a href="http://360.gaosiedu.com" target="_blank">小数在线入学测试</a>
					</td>
				</tr>
			</table>
		</div>

	</div>
	-->
	
	
	
	</body>
</html>
