<?php
//namespace Lib\COM\MsgSender;
class XySmsSender { 
    public static function sendSms($mobile, $contents) {
    	$post_data = array();
		$post_data['username'] = "bjatf";//用户名
		$post_data['password'] = "bjatf2016";//密码
		$post_data['mobile'] = $mobile;//手机号，多个号码以分号分隔，如：13407100000;13407100001;13407100002
		$post_data['content'] = urlencode('【高思一对一】'. $contents );//内容，如为中文一定要使用一下urlencode函数
		$post_data['extcode'] = "";//扩展号，可选 
		$post_data['senddate'] = "";//发送时间，格式：yyyy-MM-dd HH:mm:ss，可选
		$post_data['batchID'] = "";//批次号，可选
		$url='http://116.213.72.20/SMSHttpService/send.aspx';
		$o="";
		foreach ($post_data as $k=>$v)
		{
		    $o.= "$k=".$v."&";
		}
		$post_data=substr($o,0,-1);
		$this_header = array("content-type: application/x-www-form-urlencoded;charset=UTF-8");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$this_header);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);//返回相应的标识，具体请参考我方提供的短信API文档
		curl_close($ch);
		
		if ( $result == 0 ) {
			return true;
		}
		return false;
	}
}
?>