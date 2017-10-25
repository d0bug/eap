<?php
require dirname(__FILE__) . '/config.inc.php';
return array(
	'DB_TYPE' => 'mssql', // 数据库类型
	'DB_HOST' => 'db.gaosiedu.com:11533',
	'DB_NAME' => 'GS',
	'DB_USER' => 'admin', // 用户名
	'DB_PWD' => 'hxj@)!*gsEdu', // 密码
	//'配置项'=>'配置值'
    #默认不开启SESSION，Think内置
    'SESSION_AUTO_START'=>FALSE,
    #加密KEY值
    'ENCRYPT_KEY'   =>  $_SERVER['HTTP_HOST'],
    #Think分组模式，为方便跨组调用，采用原分组模式，Think内置
    'APP_GROUP_MODE'=>  0,
    #应用组列表，Think内置
    'APP_GROUP_LIST'=>  'System,Vip,User,Teacher,Crm,Question,Video,Util,Api,Viptest,Eval,Vipschool' ,
    #ACL授权需排除的应用组，自定义,组名采用骆驼命名法
    'EXCEPT_GROUPS' => array('Util', 'Api'),
    'EXCEPT_ACTIONS'=> array('_empty'),
    #模板文件后缀名，Think内置， 采用PHP方式
    'TMPL_TEMPLATE_SUFFIX'=>'.php',
    #公司目录名称
    'COMPANY'       => 'Gaosi',
    #默认的组，默认模块及默认方法由分组配置自定义
    'DEFAULT_GROUP' =>  'System',
    #Url模式，Pathinfo
    'URL_MODEL'     =>  2,
    #URL地址不区分大小写
    'URL_CASE_INSENSITIVE'=>true,
    #ThinkPHP3.1.3会默认REWRIT成html,清空后缀可避免
    'URL_HTML_SUFFIX'=>'',
    #API DOMAIN
    'API_URL_PREFIX' =>$apiUrlPrefix,
    'API_KEY' => $apiKey,
    #Cookie存活期，自定义
    'COOKIE_LIFE_TIME'  => 30 * 60,
    #Cookie加密值，自定义
    'COOKIE_ENCRYPT_KEY'=> 'gaosi.eap',
    #数据库连接配置，采用PDO方式，自定义
    'DB_CONN'       =>  $dbConfig,
    #默认数据连接
    'DEFAULT_CONN'  => 'MSSQL_CONN',
    #缓存配置
    'CACHE_CONFIG'  => array('cacheType'=>'Memcache', 'host'=>'127.0.0.1', 'port'=>11211, 'expire'=>7200),
    #Ldap相关设置
    'LDAP_HOST'     =>  $adHost,
    'LDAP_BASEDN'   =>  'DC=BJ,DC=GAOSIEDU,DC=COM',
    'LDAP_USERRDN'  =>  'OU=GS,OU=PERSON',
    'LDAP_TYPE'     =>  'AD_LDAP',
    'LDAP_PORT'     =>  389,
    'LDAP_ACCOUNT_PRE' =>  'BJ\\',
    'LDAP_ANONY_USER'  => $ldapAnonyUser,
    'LDAP_ANONY_PASS'  => $ldapAnonyPass,
    'SMS_CONFIG'	=>  $smsConfig,

    #系统相关设置
    'USER_COOKIE_NAME'=> 'userName',
	'COOKIE_DOMAIN'=> '.' . $_SERVER['HTTP_HOST'],
    'EMP_MAIL_SUFFIX' => '@gaosiedu.com',
    'EMP_AUTH_TYPE'   => 'AUTH_BY_ADLDAP',

    'SUPER_USERS'     => array('huxiaojun1','liuxiaohui','huxianjun','liuyuan','zhujie', 'huangrongming', 'xiecuiping', 'leiguanghua','niuxitong','sunxiangjie','wangzhilong','jiangtao','ligang','maoxuesong','zhaohaibing','zhouqing','tianya','shenyanbin','yangjiexin','liyaan'),

    'BAIDU_MAP_KEY' => '855ab12402e5b146f3430dde45c6b827',
    'MAX_UPLOAD_FILE_SIZE'=>1000000,
    'PRINT_DOMAIN'=>$printDomain,
    'EXPORT_PDF_DIR'=>$exportPdfDir,
    'NGINX_SENDFILE_MAP'=>$ngxSendFileMap,
    'REPORT_URL'=>$reportUrl,
    'SCORE_ENCRYPT_KEY'=>$scoreEncryptKey,
    'SCAN_DIR'=>$scanDir,
    'API_GZIP'=>$apiGzip,
    'EXAM_SUPER_USERS' =>$examSuperUsers,
    'OUTPUT_ENCODE'=>false,
    'UFORM_HANDLER_PRE'=>$uformHandlerPre,

	#题库地址
	'TiKu' => $tikuUrl,
	'EDITURL' => $editUrl,
	//'http://gapi.gaosiedu.com/ti/api/',
	//'TiKu' => 'http://klib.gaosiedu.com/ti/api/',
	#组卷地址
	'PAPER_URL' => 'http://paper.vip.gaosiedu.com/teacher/subject/index',
	#备课地址
	'PREPARE_URL' => 'http://jiangyi.vip.gaosiedu.com/jiaoshi/subject/index',
	
	
	#OSS阿里云存储配置
	'OSS_ACCESS_ID'=>'Jc7kCBArGDfPKIEC',
	'OSS_ACCESS_KEY'=>'DTu0BUWAzRdTEgrsTGJwldUmgUGIEs',
	'DEFAULT_OSS_HOST'=>'oss-cn-beijing.aliyuncs.com',
	'DEFAULT_OSS_HOST_SHOW'=>'video.gaosiedu.com',
	'OSS_video_PATH'=>'upload/video/',
	'OSS_IMG_PATH'=>'upload/image/',
	'BUCKET'=>'gaosischool',  
	
);
?>

