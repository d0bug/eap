<?php
function guid($prefix = '') {
	return strtoupper ( $prefix . uniqid ( mt_rand ( 1, 1000 ) ) );
}
function uid() {
	return md5 ( uniqid ( rand (), true ) );
}
function get_difficulty_name($difficulty) {
	$difficultyLevelName = '';
	if ($difficulty == 1) {
		$difficultyLevelName = '容易';
	} else if ($difficulty == 2) {
		$difficultyLevelName = '中等';
	} else if ($difficulty == 3) {
		$difficultyLevelName = '困难';
	}
	return $difficultyLevelName;
}
function arr2nav($arr, $delimit = ' > ', $field = 'name') {
	$path = '';
	for($i = 0; $i < count ( $arr ); $i ++) {
		$path .= $arr [$i] [$field] . ($i == count ( $arr ) - 1 ? '' : $delimit);
	}
	
	return $path;
}
function str2arr($str, $glue = ',') {
	return explode ( $glue, $str );
}
function arr2str($arr, $glue = ',') {
	return implode ( $glue, $arr );
}
function strrpos_num($haystack, $needle, $num) {
	$len = strlen ( $haystack );
	$tick = 1;
	for($i = $len - 1; $i >= 0; $i --) {
		if ($haystack [$i] == $needle) {
			if ($tick == $num) {
				return $i;
			} else {
				$tick ++;
			}
		}
	}
	return false;
}
function format_date($timestamp, $format = 'Y-m-d H:i:s') {
	return date ( $format, $timestamp );
}
function get_status_name($status = null) {
	if (! isset ( $status )) {
		return false;
	}
	switch ($status) {
		case - 1 :
			return '已删除';
			break;
		case 0 :
			return '禁用';
			break;
		case 1 :
			return '正常';
			break;
		case 2 :
			return '待审核';
			break;
		case 3 :
			return '已打回';
			break;
		default :
			return false;
			break;
	}
}
function get_option_flag_name($index) {
	$flags = array (
			'A',
			'B',
			'C',
			'D',
			'E',
			'F',
			'G',
			'H',
			'I',
			'J',
			'K',
			'L',
			'M',
			'N',
			'O',
			'P',
			'Q',
			'R',
			'S',
			'T',
			'U',
			'V',
			'W',
			'X',
			'Y',
			'Z',
			'A2',
			'B2',
			'C2',
			'D2',
			'E2',
			'F2',
			'G2',
			'H2',
			'I2',
			'J2',
			'K2',
			'L2',
			'M2',
			'N2',
			'O2',
			'P2',
			'Q2',
			'R2',
			'S2',
			'T2',
			'U2',
			'V2',
			'W2',
			'X2',
			'Y2',
			'Z2' 
	);
	
	return $flags [$index];
}
function get_uuid() {
	$uid = String::uuid ();
	$uid = str_replace ( '{', '', $uid );
	$uid = str_replace ( '}', '', $uid );
	$uid = str_replace ( '-', '', $uid );
	
	return $uid;
}
function get_upload_dir() {
	// $year = date ( 'Y', time () );
	// return $year . intval ( date ( 'W', time () ) );
	$r = '';
	$c = 'abcdefghijklmn';
	$n = '0123456789';
	$r .= $c [rand ( 0, strlen ( $c ) - 1 )];
	$r .= $n [rand ( 0, strlen ( $n ) - 1 )];
	return $r;
}
function get_content_error_names($strs) {
	$errors = array (
			array (
					'id' => 1,
					'text' => '图片不显示' 
			),
			array (
					'id' => 2,
					'text' => '图片其他问题' 
			),
			array (
					'id' => 3,
					'text' => '题干缺失' 
			),
			array (
					'id' => 4,
					'text' => '选项缺失' 
			),
			array (
					'id' => 5,
					'text' => '编辑完成后仍为图片' 
			) 
	);
	$strs = str2arr ( $strs, ',' );
	$errs = array ();
	foreach ( $strs as $key => $value ) {
		foreach ( $errors as $k => $v ) {
			if ($v ['id'] == $value) {
				$errs [] = $v ['text'];
				break;
			}
		}
	}
	return arr2str ( $errs, ',' );
}
function get_department($dept) {
	$dept = strtolower ( $dept );
	if ($dept == 'vip') {
		return 'VIP';
	} else if ($dept == 'class') {
		return '大班';
	} else if ($dept == 'match') {
		return '竞赛';
	} else if ($dept == 'origin') {
		return '老题库';
	} else {
		return '';
	}
}