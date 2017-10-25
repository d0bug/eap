<?php
import('COM.Dao.Dao');
class AwardImage {
	private static function getAwardCfg($examId) {
		$dao = Dao::getDao();
		$strQuery = 'SELECT * FROM ex_award_cfgs WHERE exam_id=' . abs($examId);
		$cfgInfo = $dao->getRow($strQuery);
		if($cfgInfo) {
			$cfgInfo['award_cfg'] = SysUtil::jsonDecode($cfgInfo['award_cfg']);
		}
		return $cfgInfo;
	}
	
	private function getFontArray() {
		static $fontArray = array();
		if(false == $fontArray) {
			$fontDir = C('FONT_DIR');
			$fonts = C('FONT_ARRAY');
			foreach ($fonts as $fontName=>$font) {
				$fontArray[$fontName] = $fontDir . '/' . $fontName . '.ttf';
			}
		}
		return $fontArray;
	}
	
	private function getColorArray(&$im) {
		$colors = C('COLOR_ARRAY');
		foreach ($colors as $colorName=>$color) {
			$colorRgb = $color[1];
			list($red, $green, $blue) = explode(',', $colorRgb);
			$colorArray[$colorName] = imagecolorallocate($im, $red, $green, $blue);
		}
		return $colorArray;
	}
	
	
	public static function preview($examId) {
		$awardInfo = array('exam_id'=>$examId,
						   'stu_name'=>'高小思',
						   'type_caption'=>'综合奖项',
						   'award_caption'=>'二等奖',
						   'exam_code'=>'6HW01001');
		self::getAwardImg($awardInfo, true);
	}
	
	
	public static function getAwardImg($awardInfo, $isPreview=false) {
		$examId = $awardInfo['exam_id'];
		$cfgInfo = self::getAwardCfg($examId);
		if(false == $cfgInfo) {
			die('error');
		}
		$awardFile = C('AWARD_TPL_DIR') . '/' . 'award_' . sprintf('%03d', $examId) . '_' . $cfgInfo['award_file'];
		$imgInfo = getimagesize($awardFile);
		switch ($imgInfo['mime']) {
			case 'image/jpeg':
			case 'image/pjpeg':
				$im = imagecreatefromjpeg($awardFile);
			break;
			case 'image/gif':
				$im = imagecreatefromgif($awardFile);
			break;
			case 'image/png':
				$im = imagecreatefrompng($awardFile);
			break;
			default:
				die('不支持的图像格式');
			break;
		}
		
		if(false == $isPreview) {
			if(false == $cfgInfo['cfg_status']) {
				die('电子版证书暂未设置');
			}
		}
		$fontArray = self::getFontArray();
		$colorArray = self::getColorArray($im);
		$awardCfg = $cfgInfo['award_cfg'];
		
		foreach ($awardCfg as $key=>$cfg) {
			if($key == 'addon_text') {
				$text = $cfg['text'];
			} else {
				$text = $awardInfo[$key];
			}
			$text = self::addBlank($text, $cfg['blankNum']);
			if(false == $cfg['center']) {
				imagettftext($im, $cfg['fontSize'], 0, $cfg['left'], $cfg['top'], $colorArray[$cfg['fontColor']], $fontArray[$cfg['fontFamily']], $text);
			} else {
				$encoding = 'UTF-8';
				$strlen = mb_strlen($text, $encoding);
				$blankNum = 4 - $strlen;
				$text = self::addBlank($text, $blankNum);
				$left = self::getCenterLeft($cfg, $text);
				imagettftext($im, $cfg['fontSize'], 0, $left, $cfg['top'], $colorArray[$cfg['fontColor']], $fontArray[$cfg['fontFamily']], $text);
			}
		}
        ob_clean();
		header('content-type:image/png');
		imagepng($im);
		imagedestroy($im);
	}
	
	private static function getCenterLeft($cfg, $text) {
		
		$fontArray = self::getFontArray();
		$box = imagettfbbox($cfg['fontSize'], 0, $fontArray[$cfg['fontFamily']], $text);
		return $cfg['left'] - ceil(abs($box[2] / 2));
	}
	
	private static function addBlank($text, $blankNum) {
		$encoding = 'UTF-8';
		if($blankNum == 0) return $text;
		$str = '';
		$mbStrLen = mb_strlen($text, $encoding);
		for ($i=0;$i<$mbStrLen;$i++) {
			$str .= mb_substr($text,$i,1,$encoding) . str_repeat(' ', $blankNum);
		}
		return $str;
	}
	
	public static function getTypeCaption($examId, $scoreType) {
		static $awardNames = array();
		if(false == $awardNames) {
			$dao = Dao::getDao();
			$strQuery = 'SELECT * FROM ex_award_names WHERE exam_id=' . abs($examId);
			$nameList = $dao->getAll($strQuery);
			foreach ($nameList as $row) {
				$awardNames[$row['award_type']] = $row['award_name'];
			}
		}
		if(isset($awardNames[$scoreType])) {
			return $awardNames[$scoreType];
		}
		$subjects = array('math_real'=>'数学单项', 'chinese_real'=>'语文单项', 'english_real'=>'英语单项', 'physic_real'=>'物理单项', 'chemistry_real'=>'化学单项', 'total'=>'综合奖项');
		if(isset($subjects[$examId])) {
			return $subjects[$examId];
		}
		return '';
	}
}
?>