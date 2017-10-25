<?php
class StrWidthWidget extends Widget {
    public function render($data) {
        $str = $data['string'];
        $width = $data['width'];
        $encoding = $data['encoding'] ? $data['encoding'] : 'UTF-8';
        $strWidth = mb_strwidth($str, $encoding);
        if($strWidth >= $width) {
            return $str;
        }
        if($strWidth % 2 == 1) {
            $str .= ' ';
        }
        for($i=0;$i<$width-$strWidth;$i+=2) {
            $str .= '　';
        }
        return $str;
    }
}
?>