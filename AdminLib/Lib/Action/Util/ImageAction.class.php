<?php
class ImageAction extends Action {
    public function captcha() {
        import('ORG.Crypt.Xxtea');
        import('ORG.Util.Image');
        $encryptKey = C('ENCRYPT_KEY');
        $keyCfg = unserialize(Xxtea::decrypt($_GET['key'], $encryptKey));
        session_id($keyCfg['sessid']);
        session_start();
        Image::buildImageVerify($keyCfg['length'], 1, 'png', $keyCfg['width'], $keyCfg['height'], $keyCfg['captcha']);
    }
};
?>