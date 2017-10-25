<?php
include APP_PATH."../Library/ORG/Util/PHPMailer/class.phpmailer.php";
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

/**
 * 发送邮件
 * @param  [type] $to         [description]
 * @param  string $subject    [description]
 * @param  string $body       [description]
 * @param  [type] $attachment [description]
 * @return [type]             [description]
 */
 function sendMail($frommail,$tomail, $ccmail='', $subject = '', $body = '')
 {
	$mail = new PHPMailer(); 
	$mail->IsSMTP();                           		// 使用SMTP发送邮件
	$mail->CharSet    = 'UTF-8'; 					//设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->Port       = 25;//SMTP端口号
    $mail->Host     = "smtp.163.com";           	// SMTP 服务器  
    $mail->SMTPAuth = true;                     	// 打开SMTP 认证  
    $mail->Username = "gaosi1vs1@163.com";     		// 用户名  
    $mail->Password = "123456789a";              		// 密码  
    $mail->From     = $frommail;         			// 发信人  
    $mail->FromName = "高思1对1";                  	// 发信人别名  
    foreach($tomail as $m){
       	$mail->AddAddress($m);                 		// 收信人  
    }
    if(!empty($ccmail)){  
    	foreach ($ccmail as $v) {
    		$mail->AddCC($v);                  		// cc抄送人
    	}
    }
	$mail->Subject  = $subject;
	$mail->AltBody    = "请使用HTML方式查看邮件。"; 
	$mail->Body     = $body;
	$mail->WordWrap = 50;
	$mail->IsHTML(true); 
	return $mail->Send();
 }
?>