<?php

function sqlLastInsertId(){
	return "SELECT @@IDENTITY AS 'Identity";
}

function dumps($data){
	echo '<pre>';
	print_r($data);
	echo '</pre>';
}

function ckUid($value){
	return ckInput(substr(str_replace(" ",'',urldecode($value)), 0, 32));
}

function ckInput($value)
{
	if('filter_exp' == C('VAR_FILTERS') OR '' == C('VAR_FILTERS')){
		$value = trim($value);;
		// 去除斜杠
		if (get_magic_quotes_gpc())
		{
			$value = stripslashes($value);
		}
		// 如果不是数字则加引号
		if (!is_numeric($value))
		{
			$value =   str_replace(array("'", '"'), array('’', '”'), $value) ;
		}

	}
	return $value;
}

function outPut($data){
	if (is_array($data)) {
			echo json_encode($data);
		}else{
			echo $data;
		}
}
function str2week($str) {

	return substr("日一二三四五六",date("w",$str)*3,3);
	/* $weekarray=array('日','一','二','三','四','五','六');
	 return $weekarray[date("w",$str)];*/
}
function season($nMonth) {
	if($nMonth > 11 || $nMonth < 3) {
			$nSeason = 2;
		} elseif($nMonth <6) {
			$nSeason = 3;
		} elseif ($nMonth < 9) {
			$nSeason = 4;
		} else {
			$nSeason = 1;
		}
	if($n == 0) {
		return $nSeason;
	}


}
function seasonName($nSeason) {
	$aSeasons = array(3=>'春',4=>'夏',1=>'秋',2=>'冬');
	return $aSeasons[$nSeason];
}
function questionTyoe($n) {
	$arr = array(1=>'选择题',2=>'主观题',3=>'填空题');
	return $arr[$n];
}
function url($array,$key,$value) {
		$array['sClassTypeCode'] = '';
		$array['page'] = null;
		$array[$key] = $value;
		$arr = array();
		foreach($array as $k=>$v) {
			if(empty($v)) continue;
			$arr[$k] = $k.'/'.$v;
		}

		if(empty($arr)) {
			return '/task/index/index';
		}
		return '/Task/Index/index/'.implode('/', $arr);
}
function urla($array,$key,$value) {
		//$array['sClassTypeCode'] = '';

		$array['page'] = null;
		$array[$key] = $value;
		$arr = array();
		foreach($array as $k=>$v) {
			if(empty($v)) continue;
			$arr[$k] = $k.'/'.$v;
		}

		if(empty($arr)) {
			return '/task/Analysis/index';
		}
		return '/Task/Analysis/index/'.implode('/', $arr);
}
function type2name($nType) {
	$arr = array(
		1=>'选择题',
		2=>'填空题',
		3=>'简答题'
		);
	return $arr[$nType];
}


