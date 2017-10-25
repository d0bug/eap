<?php
return array(
    'System'=>array(
        'caption'=>'系统管理',
        'icon'=>'',
        'modules'=>array(
            'App'=>array(
                'caption'=>'系统管理',
                'icon'=>'',
                'menus'=>array(
                    array('acl_key'=>'System-App-main',     'menu_caption'=>'应用管理', 'menu_icon'=>'',  'menu_url'=>'/System/App/main', 'acl_value'=>3),
                    array('acl_key'=>'System-App-module',   'menu_caption'=>'模块管理', 'menu_icon'=>'',  'menu_url'=>'/System/App/module', 'acl_value'=>3),
                    array('acl_key'=>'System-App-action',   'menu_caption'=>'功能管理', 'menu_icon'=>'',  'menu_url'=>'/System/App/action', 'acl_value'=>3),
                )
            )
        )
    )
);
?>