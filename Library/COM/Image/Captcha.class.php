<?php
import('ORG.Util.Image');
class Captcha {
    public static function getCaptchaKey($sessName, $width, $height, $length=4) {
        import('ORG.Crypt.Xxtea');
        $encryptKey = C('ENCRYPT_KEY');
        session_start();
        $sessId = session_id();
        $keyCfg = array('sessid'=>$sessId, 'captcha'=>$sessName, 'width'=>$width, 'height'=>$height, 'length'=>$length);
        $key = Xxtea::encrypt(serialize($keyCfg), $encryptKey);
        return $key;
    }

    public static function checkCaptcha($sessName, $captcha) {
        session_start();
        return Image::checkVerify($sessName, trim($captcha));
    }
};
?>