<!doctype html>
<html>
    <head>
    <title><?php echo APP_NAME?></title>
    <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
    <script type="text/javascript" src="/static/js/jquery.tools.min.js"></script>
    <script type="text/javascript">
        jQuery(function(){
            window.onbeforeunload=function(){
                return false;
            }
            jQuery('a.icon').each(function(){
                if(jQuery(this).attr('icon')) {
                    jQuery(this).css('background-image','url(' + jQuery(this).attr('icon') + ')');
                }
            })
            jQuery('.groupTab').tabs('.groupPane>div',{effect:'fade',tabs:'li'});
            jQuery('.moduleTab').each(function(){
                jQuery(this).tabs(jQuery(this).siblings('.modulePane').find('ul'), {effect:'fade',tabs:'li'});
            })
        })
    </script>
    
    <style type="text/css">
        .group,.module{display:none}
        .groupTab{position:fixed;bottom:0px}
        li{list-style-type: none;white-space: nowrap;margin-right:30px;float:left}
        ul{float:left;}
        .container{text-align:center}
        .icon{text-align:center;padding-top:65px;display:block;background:url('/icon/icon1.png') no-repeat top center;cursor: pointer}
    </style>
    </head>
    <body>
        <div><?php echo $userInfo['real_name'];?></div>
        <div class="container">
            <div class="groupPane">
                <?php foreach($menus as $group=>$groupCfg): ?>
                <div class="group">
                    <ul class="moduleTab">
                        <?php foreach($groupCfg['modules'] as $module=>$moduleCfg): ?>
                        <li ><a class="icon" href="javascript:void(0)" icon="<?php echo $moduleCfg['icon']?>"><?php echo $moduleCfg['caption']?></a></li>
                        <?php endforeach ?>
                    </ul>
                    <div style="clear:both"></div>
                    <div class="modulePane">
                        <?php foreach($groupCfg['modules'] as $module=>$moduleCfg):?>
                        <ul class="module">
                            <?php foreach($moduleCfg['menus'] as $menu):?>
                            <li><a id="<?php echo $menu['acl_key']?>" width="900" height="600" lock="true" class="winLink icon" icon="<?php echo $menu['menu_icon']?>" href="<?php echo $menu['menu_url']?>" dlgTitle="<?php echo $menu['menu_caption']?>"><?php echo $menu['menu_caption']?></a></li>
                            <?php endforeach?>
                        </ul>
                        <?php endforeach?>
                        <div style="clear:both"></div>
                    </div>
                    <div style="clear:both"></div>
                </div>
                <?php endforeach; ?>
            </div>
            <ul class="groupTab">
                <?php foreach($menus as $group=>$groupCfg): ?>
                <li icon="<?php echo $groupCfg['icon']?>"><a href="javascript:void(0)"><?php echo $groupCfg['caption']?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </body>
</html>