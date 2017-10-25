<?php
class SmsSender {
    private static $dao = null;
    private static $charset = '';
    public static function getDao() {
        if (null === self::$dao) {
            try{
                $smsConfig = C('SMS_CONFIG');
                self::$dao = new Pdo('mysql:host=' . $smsConfig['host'] . ';dbname=' . $smsConfig['dbname'], $smsConfig['username'], $smsConfig['password']);
                self::$dao->exec('SET NAMES ' . $smsConfig['charset']);
                self::$charset = $smsConfig['charset'];
            } catch(Exception $e) {
                die('Failed to Connect Sms Database');
            }
        }
        return self::$dao;
        
    }

    public static function sendSms($mobile, $contents) {
        self::sendSms2($mobile, $contents);
        return true;
    	$mobile = intval( $mobile );
    	$mobileType = self::judgeMobile($mobile);
    	if ($mobileType == 'chinaUnicom' || $mobileType == 'chinaTelecom') {
    		self::sendSms2($mobile, $contents);
    	}
        $dao = self::getDao();
        if (preg_match('/^1\d{10}$/', $mobile)) {
        
            if ('GBK' == strtoupper(self::$charset)) {
                $contents = mb_convert_encoding($contents, 'CP936', 'UTF-8');
            }
            $strQuery = "INSERT INTO        sms_outbox(sismsid,EXTCODE,DESTADDR,MESSAGECONTENT,REQDELIVERYREPORT,MSGFMT,SENDMETHOD,REQUESTTIME,APPLICATIONID,ECID) 
                        VALUES (UUID(),'','{$mobile}','{$contents}',1,15,1,Now(),'','')";
            $dao->exec($strQuery);
            $errorInfo = $dao->errorInfo();
            if ($errorInfo[0] == '00000') {
                return true;
            }
        }
        return false;
    }
    
    /*新接口，主要用于给联通和电信手机号码发送短信*/
    protected static function sendSms2($mobile, $contents){
    	$flag = 0;
    	// 要post的数据
    	$argv = array (
    			'sn' => 'DXX-ANG-010-00015', // //替换成您自己的序列号
    			'pwd' => strtoupper ( md5 ( 'DXX-ANG-010-00015' . '7@bffb@4' ) ), // 此处密码需要加密 加密方式为 md5(sn+password) 32位大写
    			'mobile' => $mobile, // 手机号 多个用英文的逗号隔开 post理论没有长度限制.推荐群发一次小于等于10000个手机号
    			'content' => iconv ( "UTF-8", "gb2312//IGNORE", $contents . '【高思教育】' ), // 短信内容
    			'ext' => '',
    			'stime' => '', // 定时时间 格式为2011-6-29 11:09:21
    			'rrid' => ''
    	);
    	// 构造要post的字符串
    	foreach ( $argv as $key => $value ) {
    		if ($flag != 0) {
    			$params .= "&";
    			$flag = 1;
    		}
    		$params .= $key . "=";
    		$params .= urlencode ( $value );
    		$flag = 1;
    	}
    	$length = strlen ( $params );
    	// 创建socket连接
    	$fp = fsockopen ( "sdk2.entinfo.cn", 8060, $errno, $errstr, 10 ) or exit ( $errstr . "--->" . $errno );
    	// 构造post请求的头
    	$header = "POST /webservice.asmx/mt HTTP/1.1\r\n";
    	$header .= "Host:sdk2.entinfo.cn\r\n";
    	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    	$header .= "Content-Length: " . $length . "\r\n";
    	$header .= "Connection: Close\r\n\r\n";
    	// 添加post的字符串
    	$header .= $params . "\r\n";
    	// 发送post的数据
    	fputs ( $fp, $header );
    	$inheader = 1;
    	while ( ! feof ( $fp ) ) {
    		$line = fgets ( $fp, 1024 ); // 去除请求包的头只显示页面的返回数据
    		if ($inheader && ($line == "\n" || $line == "\r\n")) {
    			$inheader = 0;
    		}
    		if ($inheader == 0) {
    			// echo $line;
    		}
    	}
    	$line = str_replace ( "<string xmlns=\"http://tempuri.org/\">", "", $line );
    	$line = str_replace ( "</string>", "", $line );
    	$result = explode ( "-", $line );
    	if (count ( $result ) > 1){
    		return false;
//     		echo '发送失败返回值为:' . $line . '。请查看webservice返回值对照表';
    	}else{
    		return true;
//     		echo '发送成功 返回值为:' . $line;
    	}
    }
    
    protected static function judgeMobile($mobile){
    	$mobile = substr ( $mobile, 0, 3 );
    	$chinaMobile =  array(134,135,136,137,138,139,150,151,152,158,159,157,182,183,187,188,147);
    	$chinaUnicom = array(130,131,132,155,156,185,186);
    	$chinaTelecom = array(133,153,180,189,181);
    	$arrMobile = array (
				'chinaMobile' => $chinaMobile,
				'chinaUnicom' => $chinaUnicom,
				'chinaTelecom' => $chinaTelecom 
		);
    	foreach ($arrMobile as $k => $v){
    		$r = in_array(intval ( $mobile ), $v, true);
    		if ($r) {
    			return $k;
    		}
    	}
    	return '';
    }
}
?>