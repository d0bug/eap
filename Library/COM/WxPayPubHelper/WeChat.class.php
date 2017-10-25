<?php 

/*
	2016-8-31
	dengjun
	高思1对1微信支付返回
*/

class WeChat{

	function notify($postData = array()){

		$http_url = 'http://vip.gaosiedu.com/vip/gs_weixin/notify';
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $http_url );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 100 );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postData );
		$content = curl_exec ( $ch );
		$error = curl_error ( $ch );
		curl_close ( $ch );
		if ($error != "") {
			header ( "HTTP/1.1 404 Not Found" );
			exit ();
		}
		file_put_contents('success.txt',"(支付成功)time:".$content,FILE_APPEND);exit;
		return $content;
	}

}
?>