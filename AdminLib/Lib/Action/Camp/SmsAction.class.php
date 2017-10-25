<?php
/**
 * 语文作业管理
 *
 */
class SmsAction extends AppCommAction{

	protected  $model = null;
	public function __construct() {
		parent::__construct();
		$this->model = D('CampNews');
	}
	public function index() {

		$this->getList();
	}

	public function add() {
		$sClassCode = array();
		$nId = time();

		if(empty($_GET['nYear'])) {
			$data['nClassYear'] = (int)date('Y');
		} else {
			$data['nClassYear'] = (int)$_GET['nYear'];
		}
		if(empty($_GET['nSeason'])) {
			$data['nSemester'] = season((int)date('m'));
		} else {
			$data['nSemester'] = (int)$_GET['nSeason'];
		}




		$classList = $this->model->getClassList($data);
		$this->assign(get_defined_vars());

		$this->display('getForm');
	}
	public function insert() {

		if(empty($_POST['nId'])) {
			$this->error('errer');
		} else {
			$nId = (int)$_POST['nId'];
		}
		if(empty($_POST['sMessage'])) {
			$this->error('errer');
		} else {
			$sMessage = trim($_POST['sMessage']);
		}

		if(empty($_POST['phones']) && empty($_POST['sPhones']) ) {
			$this->error('errer');
		}
		$smsData = array('sMessage'=>$sMessage,'nId'=>$nId);
		$data = array();
		$mobile = array();
		$nNumber = 0;
		$nStatus = 1;
		foreach($_POST['phones'] as $sClassCode => $value) {
			$mobile[] = implode(',', $value);
			$nNumber += count($value);

		}
		$sPhones = implode(',', $mobile);
		if(!empty($_POST['sPhones'])) {
			$postPhones = trim($_POST['sPhones']);
			$postPhones = str_replace('，', ',', $postPhones);
			$sPhones .= $postPhones;
			$nNumber += count(explode(',', $postPhones));
		}
		$data = array(
			'sMessage' => $sMessage,
			'nId' => $nId,
			'sPhones' => $sPhones,
			'nNumber' => $nNumber,
			'nStatus' => $nStatus
			);
		//echo '<pre>';print_r($data);exit();
		if($this->sendSms2($sPhones, $sMessage)) {
			$this->model->_insert($data,'Camp_sms');
			$this->success('发送成功');
		} else {
			$this->error('发送失败');
		}


	}

	private function getList() {

		$results = $this->model->getSmsList();
		$list = array();
		foreach($results as $value) {
			$list[] = $value;
		}

		$this->assign(get_defined_vars());

		$this->display('getList');
	}

	public function getPhoneList() {
		if(!empty($_POST['sClassCode'])) {
			$sClassCode = preg_replace('|[^a-zA-Z0-9]|', '', $_POST['sClassCode']);
		} else {
			echo 0;
			exit();
		}
		$results = $this->model->getPhoneList($sClassCode);


		echo json_encode($results);
		exit();

	}


	/**
     * 不需要登录的方法名称数组，名称需大写
     * @return Array
     */
	protected function notNeedLogin(){
		return array('UPLOADER');
	}


	private function sendSms2($mobile, $contents){
    	$flag = 0;
    	//$mobile = '13146201950,13811780160';
    	//$contents .= '[高思教育]';
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
	$params = '';
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



}

