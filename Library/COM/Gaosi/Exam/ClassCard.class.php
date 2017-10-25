<?php
class ClassCard {
	
	public static function getClsCard($cardInfo) {
		$tplFile = TMPL_PATH . '/Public/clsCard.png';
		$fontDir = C('FONT_DIR');
		header('content-type:image/png');
		$im = imagecreatefrompng($tplFile);
		$black = imagecolorallocate($im, 0, 0, 0);
		imagettftext($im, 15, 0, 135, 103, $black, $fontDir . '/msyhbd.ttf', $cardInfo['examCaption']);
		imagettftext($im, 15, 0, 135, 136, $black, $fontDir . '/msyhbd.ttf', $cardInfo['stuName'] . '[' . $cardInfo['stuCode'] . ']');
		imagettftext($im, 15, 0, 135, 170, $black, $fontDir . '/msyhbd.ttf', $cardInfo['examCode']);
		imagettftext($im, 15, 0, 135, 204, $black, $fontDir . '/msyhbd.ttf', $cardInfo['className']);
		imagepng($im);
		imagedestroy($im);
	}
}
?>