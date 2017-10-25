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
/**
 * 格式化输出
 * @param array   $arr    表格的基本信息，res_class_info里的信息
 * @param integer $x      周
 * @param integer $y      课程
 * @param integer $format 标号
 * @return [type]  [description]
 */
function createFormat($arr = array(),$x = 0,$y = 0,$format =0) {


		$time_name_arr = C('TIME_TYPE');
        $id = $arr['id'];
		if(isset($arr['begin_time'])) {
			$begin_time = date('H:i',$arr['begin_time']);
		} else {
			$begin_time = date('H:i');
		}
		if(isset($arr['end_time'])) {
			$end_time = date('H:i',$arr['end_time']);
		} else {
			$end_time = date('H:i');
		}
        if(isset($arr['time_type'])) {
        	$time_type = (int)$arr['time_type'];
        } else {
        	$time_type = '';
        }
        if(isset($arr['date'])) {
        	$time_name = '星期'.str2week($arr['date']);
        	//$time_name = '###'.strtotime($arr['date']);

        } else {
        	$time_name = '待定';
        }
        if(isset($arr['apply_num'])) {
        	$apply_num = (int)$arr['apply_num'];
        } else {
        	$apply_num = 0;
        }
        if(isset($arr['allow_num'])) {
        	$allow_num = (int)$arr['allow_num'];
        } else {
        	$allow_num = 0;
        }
        //echo $arr['date'];
        if(!empty($arr['date'])) {
        	$date = date('Y-m-d',$arr['date']);
        } else {
        	$date = '';
        }

        //默认的是后台添加修改课程的模式
        if (empty($format)) {
		$select = '<select name="time_type[x][y]">';
		foreach($time_name_arr as $key => $value) {
			if($key === $time_type)
			   $select .= '<option value='.$key.'  selected = "selected">'.$value.'</option>';
		    else
		       $select .= '<option  value='.$key.'>'.$value.'</option>';

		}
		$select .= '</select>';

		$format = <<<EOF
		<div style="width:180px;height:80px;">
		<input type="hidden" size="2" name ='id[x][y]' id= "id" value="%d">
						<p>%s
						<span>%d</span> /
						<span><input type="text" size="2" name ='allow_num[x][y]' id= "allow_num" value="%d"></span>
						</p>
						<p><input size="13" id="dtDate" class="Wdate" type="text" onclick="WdatePicker()" value="%s" name="date[x][y]" placeholder="上课日期...">
						%s
						</br>
						<input  size ="8" class="easyui-timespinner timespinner-f spinner-text spinner-f validatebox-text" type="text" value="%s" name="begin_time[x][y]" >
						 -
						<input  size ="8" class="easyui-timespinner timespinner-f spinner-text spinner-f validatebox-text" type="text" value="%s" name="end_time[x][y]" >
						</p>
		           </div>
EOF;

		$str = sprintf($format,$id,$time_name,$apply_num,$allow_num,$date,$select,$begin_time,$end_time);
		return str_replace('[x][y]', '['.$x.']['.$y.']', $str);
	}

    /*-----------------------------------------------------------------------------------------*/
	if($format == 1) {

		if(!empty($arr['date'])) {
        	$date = date('m月d日',$arr['date']);
        	if($arr['date'] < time()) {
        		$pass = 'border:solid 1px;';
        	}
        } else {
        	$date = '--';
        }
		$format = <<<EOF
		<div style="width:85px;height:70px; $pass">
						$time_name {$time_name_arr[$time_type]}
						</br>
						$date
						</br>
						$begin_time
						 -
						$end_time
		           </div>
EOF;
        return $format;
	}
	/*-------------------------------------------------------------------------------------*/
	if($format == 2) {
		if(!empty($arr['date'])) {
        	$date = date('(m月d日)',$arr['date']);
        } else {
        	$date = '--';
        }
		$format = $time_name.'   '.$time_name_arr[$time_type].$date;
		return $format;
	}
	/*--------------------------------------------------------------------------------------*/
	if($format == 3) {
		if(!empty($arr['date'])) {
        	$date = date('(m月d日)',$arr['date']);
        } else {
        	$date = '--';
        }
        $format =  $time_name.'   '.$time_name_arr[$time_type].'('.$date.')';
		return $format;
	}

}//func end
