<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="utf-8" />
		<title>高思教育应用管理平台(EAP)</title>
        <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
        <?php include TPL_INCLUDE_PATH . '/easyuiMove.php' ?>
        <script type="text/javascript">
            var curApp = '';
            jQuery(function(){
                if(false == jQuery.browser.msie) {
                    window.onbeforeunload=function(){
                        return false;
                    }
                }
                curApp = jQuery('#appIcons').find('li:first').attr('appName');
                showMenu(curApp);
                jQuery('.sideMenu').find('li').click(function(){
                    createTab(this);
                })

				// 菜单修改
				jQuery('.menuTitle').click(function() {
					$(this).parent().toggleClass('selected').siblings().removeClass('selected');
				});
            })

            function showMenu(appName) {
                curApp = appName;
                jQuery('.sideMenu').hide();
                jQuery('.sideMenu').each(function(){
                    if(jQuery(this).attr('appName') == curApp) {
                        jQuery(this).show();
						jQuery(this).children("div:first").addClass('selected');
                        //jQuery(this).accordion({fit:true})
                    }
                })

            }

            function createTab(action) {
                id = jQuery(action).attr('action');
                var tabs = jQuery('#mainTab').tabs('tabs');
                var tabExists = false;
                jQuery.each(tabs, function(k,v) {
                    if(id == jQuery(v).attr('id')) {
                        jQuery(v).find('iframe').attr('src', jQuery(v).find('iframe').attr('src'));
                        tabExists = true;
                        idx = k;
                    }
                })
                if(false == tabExists) {
                    jQuery('#mainTab').tabs('add', {
                        id:jQuery(action).attr('action'),
                        title:jQuery(action).attr('caption'),
                        content:'<iframe width="100%" height="99%" frameborder="no" scrolling="yes" style="overflow-x:hidden;" src="' + jQuery(action).attr('url') + '"></iframe>',
                        closable:true
                    })

                } else {
                    jQuery('#mainTab').tabs('select', idx);
                }
                return false;
            }

        </script>
		<link rel="stylesheet" type="text/css" href="/static/css/main.css" />
		<style type="text/css">
        #appIcons{width:65%;float:left;;height:78px;overflow:auto}
        #appIcons li{margin-bottom:10px;width:75px}
        </style>
    </head>
    <body class="easyui-layout" fit="true" border="true">
    <div region="north" style="height:80px">
        <div class="sysHeader">
			<h1 class="sysCaption"><?php echo APP_CAPTION?></h1>
			<div id="appIcons">
			<ul class="sysNav">
				<?php foreach ($menus as $groupName=>$groupCfg):?>
				<li appName="<?php echo $groupName?>"><a href="javascript:showMenu('<?php echo $groupName?>')"><img  src="<?php if($groupCfg['icon']):echo $groupCfg['icon'];else: echo '/images/default_group.png'; endif;?>" align="absmiddle" width="32" height="32"/><br /><?php echo $groupCfg['caption']?></a></li>
				<?php endforeach;?>
			</ul>
			</div>
			<div class="sysUser">操作员：<span class="u"><?php echo $userInfo['real_name'];?></span> [<a href="<?php echo $logoutUrl?>">退出登录</a>]</div>
		</div>
    </div>
    <div region="west" style="width:200px" title="功能系统功能菜单"  split="true" border="false" resizable="false">

        <?php foreach($menus as $groupName=>$groupCfg):?>
		<div appname="<?php echo $groupName?>" class="sideMenu">
			<?php foreach($groupCfg['modules'] as $moduleName=>$moduleCfg):?>
			<div class="menuPanel">
				<div class="menuTitle">
					<div class="title"><?php echo $moduleCfg['caption']?></div>
					<div class="tool"><a href="javascript:void(0)"></a></div>
				</div>
				<div class="menuBox">
					<ul class="menuList">
						<?php foreach ($moduleCfg['menus'] as $menu):?>
						<?php if ( $menu['menu_url'] == 'http://jiangyi.eap.gaosiedu.com/' || $menu['menu_caption'] == '搭标准化讲义' || $menu['menu_caption'] == '备课' ||$menu['menu_caption'] == '组卷' || $menu['menu_caption'] == '我的讲义库' || $menu['menu_caption'] == '使用说明'|| $menu['menu_caption'] == '知识元管理'|| $menu['menu_caption'] == '版本体系管理'){ ?>
						<li caption="<?php echo $menu['menu_caption']?>"><a href="<?php echo $menu['menu_url']?>" target="_blank"><?php echo $menu['menu_caption'] ?></a></li>
						<?php } else { ?>
						<li caption="<?php echo $menu['menu_caption']?>" url="<?php echo $menu['menu_url']?>" action="<?php echo $menu['acl_key']?>"><a href="javascript:void(0)" ><?php echo $menu['menu_caption'] ?></a></li>
						<?php } ?>
						<?php endforeach;?>
					</ul>
				</div>
			</div>
			<?php endforeach;?>
		</div>
		<?php endforeach;?>

    </div>
    <div region="center" data-options="border: false">
    <div class="easyui-tabs" id="mainTab" fit="true" border="true">
        <div title="首页">
        </div>
    </div>

    </div>
    </body>
</html>
<script>
	$(function(){
		//window.frames["iframe1"]
		$("#Vip-VipWorks-index").find(".abc").live('click',function(){
			alert('ss');
		})
	})
</script>