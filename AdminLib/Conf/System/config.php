<?php
return array(
    'DEFAULT_MODULE'=>'User',
    'DEFAULT_ACTION'=>'index',
    'ICON_CONFIG'     => array('allowTypes'=>array('image/jpeg', 'image/pjpeg', 'image/png', 'image/gif'),
                               'thumb'=>true,
                               'thumbMaxWidth'=>128,
                               'thumbMaxHeight'=>128,
                               'thumbPrefix'=>'icon_',
                               'thumbRemoveOrigin'=>true,
                               'autoSub'=>true,
                               'subType'=>'custom',
                               'subDir'=>'',//这里自定义子目录名称
                               'thumbPath'=>'',
                               'thumbExt'=>'gif',
                               'savePath'=>APP_DIR . '/AdminRoot/icon/',
                               'uploadReplace'=>true,
                               'url_prefix'=>'/icon/'),
);
?>