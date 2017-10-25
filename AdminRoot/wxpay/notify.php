<?php
/**
 * 通用通知接口demo
 * ====================================================
 * 支付完成后，微信会把相关支付和用户信息发送到商户设定的通知URL，
 * 商户接收回调信息后，根据需要设定相应的处理流程。
 * 
 * 这里举例使用log文件形式记录回调信息。
*/
	//include_once("./WxPayPubHelper.php");
    //使用通用通知接口

	define('APP_DIR', dirname(dirname(dirname(__FILE__))));
	define('LIBRARY_PATH',  APP_DIR . '/Library');
	include_once(LIBRARY_PATH."/COM/WxPayPubHelper/WeChat.class.php");
	
	// //存储微信的回调
	$xmls = $GLOBALS['HTTP_RAW_POST_DATA'];	
	//使用simplexml_load_string() 函数将接收到的XML消息数据载入对象$postObj中。
	if(!empty($xmls)){
		$postObj = simplexml_load_string($xmls, 'SimpleXMLElement', LIBXML_NOCDATA);
		$result_code = $postObj->return_code;
		if($result_code == "SUCCESS") {
				$notify['out_trade_no'] = substr($postObj->out_trade_no,0,-10); //SUCCESS
				$notify['pay_price'] = $postObj->total_fee;
				$notify['return_code'] = $postObj->return_code;
				$notify['transaction_id'] = $postObj->transaction_id;
				$notify['openid'] = $postObj->openid;
				$notify['time_end'] = $postObj->time_end;
				$notify['bank_type'] = $postObj->bank_type;
				$notify['pay_trade_no'] = $postObj->out_trade_no;
				$weixinApi = new \WeChat();
				$weixinApi->notify($notify);
				//file_put_contents('success.txt',"(支付成功)time:".$postObj->time_end,FILE_APPEND);exit;
		}else{
			file_put_contents('error.txt',"(支付失败)time:".date(''),FILE_APPEND);exit;
		}
		
	}


	
    //$OpenId = $postObj->OpenId;  //可以这样获取XML里面的信息

	
	//验证签名，并回应微信。
	//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
	//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
	//尽可能提高通知的成功率，但微信不保证通知最终能成功。
/*	if($notify->checkSign() == FALSE){
		$notify->setReturnParameter("return_code","FAIL");//返回状态码
		$notify->setReturnParameter("return_msg","签名失败");//返回信息
	}else{
		$notify->setReturnParameter("return_code","SUCCESS");//设置返回码
	}
	$returnXml = $notify->returnXml();*/
	//echo $returnXml;
	
	//==商户根据实际情况设置相应的处理流程，此处仅作举例=======
	
	//以log文件形式记录回调信息
	
?>