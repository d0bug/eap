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
