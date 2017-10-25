<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo APP_CAPTION?></title>
<script type="text/javascript" src="/jsLib/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/jsLib/myLib.js"></script>
<script type="text/javascript">
var desktopCnt = 1;
$(function(){
    myLib.progressBar();
});
$.include(['/themes/default/css/desktop.css', '/jsLib/jquery-ui-1.8.18.custom.css', '/jsLib/jquery-smartMenu/css/smartMenu.css' , '/jsLib/jquery-ui-1.8.18.custom.min.js', '/jsLib/jquery.winResize.js', '/jsLib/jquery-smartMenu/js/mini/jquery-smartMenu-min.js', '/jsLib/desktop.js']);
$(window).load(function(){
		myLib.stopProgress();
 			
		//存储桌面布局元素的jquery对象
		myLib.desktop.desktopPanel();
 		
		//初始化桌面背景
		myLib.desktop.wallpaper.init("/themes/default/images/blue_glow.jpg");
		
		//初始化任务栏
		myLib.desktop.taskBar.init();
			
		//初始化桌面图标
		deskIconData = {};
		jQuery('.desktop_icon').each(function(){
			iconId = jQuery(this).attr('id');
			url = '/index.php/' + iconId.replace(/\-/g,'/');
			deskIconData[iconId] = {'title':'<strong>' + jQuery(this).text()  + '</strong> — ' + jQuery(this).attr('groupName'), 'url':url};
		})
		myLib.desktop.deskIcon.init(deskIconData);
			
		//初始化桌面导航栏
		myLib.desktop.navBar.init({});
			
		//初始化侧边栏
		var lrBarIconData = {};
		myLib.desktop.lrBar.init(lrBarIconData);
        jQuery('.logout_btn').parent().click(function(){
            location='/index.php/Admin/System/logout';
        })
        var deskTopMenu=[
					[{
					  text:"退出登录",
					  func:function(){
                        location='/index.php/Admin/System/logout';
                      } 
					  }]
					];
        myLib.desktop.contextMenu($('#desktopPanel'),deskTopMenu,"body",10);
})
</script>
</head>
<body>
<div id="wallpapers"></div>
<?php if (sizeof($groupNames)>1):?>
<div id="navBar">
<?php $i=1;
foreach($groupNames as $groupName=>$groupCaption):
?>
<a href="#" <?php if ($i++ == 1):?>class="currTab"<?php endif?> title="<?php echo $groupCaption?>"></a>
<?php endforeach?>
</div><?php endif?>

<div id="desktopPanel">
<div id="desktopInnerPanel">
	<?php $i=1;
		foreach($groupNames as $groupName=>$groupCaption):
	?>
	<ul class="deskIcon<?php if($i++==1):?> currDesktop<?php endif?>">
		<?php foreach($userMenus[$groupName] as $menuId=>$menu):?>
		<li class="desktop_icon" groupName="<?php echo $groupCaption?>" id="<?php echo $groupName. '-' . $menuId?>"><span class="icon"><img src="<?php echo $menu['icon'] ? $menu['icon'] : '/icon/icon6.png'?>" /></span><div class="text"><?php echo $menu['caption']?><s></s></div></li>
		<?php endforeach?>
	</ul>
	<?php endforeach ?>
	
</div>
</div>

<div id="taskBarWrap">
<div id="taskBar">
  <div id="leftBtn"><a href="#" class="upBtn"></a></div>
  <div id="rightBtn"><a href="#" class="downBtn"></a> </div>
  <div id="task_lb_wrap"><div id="task_lb"></div></div>
</div>
</div>

<div id="lr_bar">
  <div id="start_block">
	<a title="开始" id="start_btn"></a>
	<div id="start_item">
		<ul class="item admin">
		  <li><span class="adminImg"></span><?php echo $userInfo['user_name']?></li>
		</ul>
		<ul class="item">
		  <!--li><span class="about_btn"></span>关于我们</li-->
		  <li><span class="logout_btn"></span>退出系统</li>
		</ul>
	 </div>
</div>
</div>
</div>

</body>
</html>
