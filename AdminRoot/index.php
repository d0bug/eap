<?php
ob_start();
header('content-type:text/html;charset=utf-8');
define('APP_NAME', 'EAP');
define('APP_CAPTION', 'PIV4.0云讲义打造高效课堂');
define('APP_DIR', dirname(dirname(__FILE__)));
define('APP_PATH', APP_DIR . '/AdminLib/');
define('DATA_PATH', APP_DIR . '/AdminLib/data');
define('UPLOAD_PATH', APP_DIR.'/Upload/');
define('HTML_PATH', APP_DIR.'/Html/');
define('APP_URL', 'http://vip.gaosiedu.com');
define('APP_DEBUG', true);
$includePath = dirname(APP_DIR) . '/include';
define('LIBRARY_PATH',  APP_DIR . '/Library');
define('THINK_MODE','PHPRPC');

#兼容服务器上的nginx+fpm设置
if (preg_match('/^\/index\.php$/', $_SERVER['PATH_INFO'])) {
	$_SERVER['PATH_INFO'] = '';
}

require $includePath . '/ThinkPHP/ThinkPHP.php';

?>
