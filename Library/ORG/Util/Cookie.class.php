<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id: Cookie.class.php 2702 2012-02-02 12:35:01Z liu21st $

/**
 +------------------------------------------------------------------------------
 * Cookie管理类
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id: Cookie.class.php 2702 2012-02-02 12:35:01Z liu21st $
 +------------------------------------------------------------------------------
 */
class Cookie {
    static function getEncryptKey() {
        static $encryptKey = null;
        if (null !== $encryptKey) return $encryptKey;

        if (defined('COOKIE_ENCRYPT_KEY')) {
            $encryptKey = COOKIE_ENCRYPT_KEY;
        } else {
            $encryptKey = C('COOKIE_ENCRYPT_KEY');
        }
        if (!$encryptKey) $encryptKey = $_SERVER['HTTP_HOST'];
        return $encryptKey;
    }
    // 判断Cookie是否存在
    static function is_set($name) {
        return isset($_COOKIE[C('COOKIE_PREFIX').$name]);
    }

    // 获取某个Cookie值
    static function get($name) {
        import('ORG.Crypt.Xxtea');
        $value   = $_COOKIE[C('COOKIE_PREFIX').$name];
        $value   =  unserialize(Xxtea::decrypt($value, self::getEncryptKey()));
        return $value;
    }

    // 设置某个Cookie值
    static function set($name,$value,$expire='',$path='',$domain='') {
        import('ORG.Crypt.Xxtea');
        if($expire=='') {
            $expire =   C('COOKIE_EXPIRE');
        }
        if(empty($path)) {
            $path = C('COOKIE_PATH');
        }
        if(empty($domain)) {
            $domain =   C('COOKIE_DOMAIN');
        }
        $expire =   !empty($expire)?    time()+$expire   :  0;
        $value   =  Xxtea::encrypt(serialize($value), self::getEncryptKey());
        setcookie(C('COOKIE_PREFIX').$name, $value,$expire,$path,$domain,false,true);
        $_COOKIE[C('COOKIE_PREFIX').$name]  =   $value;
    }

    // 删除某个Cookie值
    static function delete($name) {
        Cookie::set($name,'',-3600);
        unset($_COOKIE[C('COOKIE_PREFIX').$name]);
    }

    // 清空Cookie值
    static function clear() {
        unset($_COOKIE);
    }
}