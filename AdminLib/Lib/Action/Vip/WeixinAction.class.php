<?php
/*微信版教师系统*/
import('COM.MsgSender.SmsSender');
class WeixinAction extends WeixinCommAction {

	//	protected function notNeedLogin() {
	//		return array('VIP-WEIXIN-INDEX','VIP-WEIXIN-FROMURL','VIP-WEIXIN-LOGIN','VIP-WEIXIN-NEWLIST','VIP-WEIXIN-HOTLIST');
	//	}

	public function index(){
		define("TOKEN", "gaosivip");
		$this->valid();
	}


	public function post($url, $jsonData){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS,$jsonData);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}


	public function valid(){
		$echoStr = $_GET["echostr"];
		if($this->checkSignature()){
			echo $echoStr;
			$this->responseMsg();
			exit;
		}
	}


	public function responseMsg(){
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		$baseUrl = 'http://vip.gaosiedu.com';
		if (!empty($postStr)){
			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$RX_TYPE = trim($postObj->MsgType);
			$fromUsername = $postObj->FromUserName;
			$toUsername = $postObj->ToUserName;
			$keyword = trim($postObj->Content);
			$clickKey = trim($postObj->EventKey);
			$keyStyle = trim($postObj->Event);  //获取事件类型：subscribe(订阅)、unsubscribe(取消订阅)、CLICK(自定义菜单点击事件)
			$picurl = $postObj->PicUrl;
			$time = time();
			//默认回复消息
			$textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						</xml>";

			//$TokenUrl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".C('appID')."&secret=".C('appsecret');
			//$TokenData = json_decode(file_get_contents($TokenUrl),true);
			//$AccessToken=$TokenData['access_token'];

			$AccessToken = $this->getToken();

			$msgType = "text";
			//页面跳转用
			$forward = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".C('appID')."&response_type=code&scope=snsapi_base&redirect_uri=";
			$fromUrl = $baseUrl.'/Vip/Weixin/fromUrl';
			//自定义菜单开始
			$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$AccessToken;
			
			/*
			$data = '{
						"button":[
						{
							"type":"click",
							"name":"PIV3.0",
							"sub_button":[
									{
									"type":"view",
									"name":"核录课时",
									"url":"'.$forward.urlencode($fromUrl).'&state=waitHelu#wechat_redirect"
									},
									{
									"type":"view",
									"name":"我的学员",
									"url":"'.$forward.urlencode($fromUrl).'&state=myStudents#wechat_redirect"
									},
									{
									"type":"view",
									"name":"周课表",
									"url":"'.$forward.urlencode($fromUrl).'&state=weekSchedule#wechat_redirect"
									},
									{
									"type":"view",
									"name":"月课表",
									"url":"'.$forward.urlencode($fromUrl).'&state=monthSchedule#wechat_redirect"
									},
									{
									"type":"click",
									"name":"退出登录",
									"key":"VIPweixin_03_01"
									}]
							},
							{
								"type":"click",
								"name":"中考成绩",
								"sub_button":[
									{
										"type":"view",
										"name":"中考成绩",
										"url":"'.$forward.urlencode($fromUrl).'&state=saveScore#wechat_redirect"
									},
									{
									"type":"view",
									"name":"累计课时",
									"url":"'.$forward.urlencode($fromUrl).'&state=myLessonHours#wechat_redirect"
									}
								]
							},
							{
								"type":"click",
								"name":"PIV4.0",
								"sub_button":[
									{
									"type":"view",
									"name":"核录课时",
									"url":"'.$forward.urlencode($fromUrl).'&state=newWaitHelu#wechat_redirect"
									},
									{
									"type":"view",
									"name":"传测试卷",
									"url":"'.$forward.urlencode($fromUrl).'&state=confirmItembank2#wechat_redirect"
									},
									{
									"type":"view",
									"name":"我的学员",
									"url":"'.$forward.urlencode($fromUrl).'&state=newMyStudents#wechat_redirect"
									},
									{
									"type":"view",
									"name":"我的课表",
									"url":"'.$forward.urlencode($fromUrl).'&state=newWeekSchedule#wechat_redirect"
									},
									{
									"type":"click",
									"name":"退出登录",
									"key":"VIPweixin_03_01"
									}]
							}]
					}';
			*/
			//$data = '{}';
			//$this->post($url,$data);
			//自定义菜单结束

			if($keyStyle == 'subscribe'){//关注
				$msgType = "text";
				if(empty($clickKey)){
					$clickKey = '0';
				}
				$c = str_replace('qrscene_','',$clickKey);
				$tmpStr = $this->str_md5($fromUsername.$c.$time);
				$tmpUrl = "http://vip.gaosiedu.com/Vip/Weixin/qrAttention/openid/".$fromUsername."/c/".$tmpStr."/k/".$c."/t/".$time;
				file_get_contents($tmpUrl);
				//$contentStr = str_replace('aid',$fromUsername,"您好，欢迎关注高思1对1教师在线！请在点击下方“我的课程”菜单中的任意项之后，按照页面提示进行登录操作。");
				$contentStr = str_replace('aid',$fromUsername,"您好，欢迎关注高思1对1教师在线！");
				$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
				echo $resultStr;
			}

			if(!empty($keyword)){
				$msgType = "text";
				//$contentStr = "欢迎使用高思VIP教师系统，上传讲义图片请直接发送图片，其他功能请点击下方菜单。";
				$contentStr = "您好，此公众号已暂停使用，谢谢！";
				$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
				echo $resultStr;
			}

			if(!empty($clickKey)){
				$msgType = "text";
				if($clickKey=="VIPweixin_03_01"){
					$uu = $baseUrl."/Vip/Weixin/logout/weixinid/".$fromUsername;
					$return = file_get_contents($uu);
					if($return == 1){
						$contentStr = "您已退出登录";
					}else{
						$contentStr = "退出登录失败，请检查您是否绑定了教师账号";
					}
					$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
					echo $resultStr;
				}else if($clickKey=="VIPnews_150413"){
					$contentStr = "功能维护中......";
					$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
					echo $resultStr;
				}
			}

			if(!empty($picurl)){
				/*$mediaId = $postObj->MediaId;
				$msgType = "text";
				$tmphUrl = $baseUrl."/Vip/Weixin/wxAddImg/openid/".$fromUsername."/pic/".base64_encode($picurl);
				$return = file_get_contents($tmphUrl);
				//$contentStr = $return;
				if(!empty($return)){
				$contentStr .= "<a href=\"".$baseUrl.U('Vip/Weixin/confirmImg2',array('openid'=>$fromUsername))."\">上传讲义</a>\r\n\r\n<a href=\"".$baseUrl.U('Vip/Weixin/confirmItembank2',array('openid'=>$fromUsername))."\">上传测试卷</a>\r\n\r\n<a href=\"".$baseUrl.U('Vip/Weixin/confirmProgram2',array('openid'=>$fromUsername))."\">上传辅导方案</a>\r\n\r\n<a href=\"".$baseUrl.U('Vip/Weixin/confirmRecordImg2',array('openid'=>$fromUsername))."\">上传轨照（4.0试用）</a>";
				}else{
				$contentStr .= "上传图片失败，请重新上传";
				}*/
				$msgType = "text";
				$contentStr .= "核录课时请在 PIV3.0/4.0-核录课时 中进行";
				$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
				echo $resultStr;
			}

		}else{
			echo "";
			exit;
		}
	}




	public function fromUrl(){
		$result = file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=".C('appID')."&secret=".C('appsecret')."&code=".$_GET['code']."&grant_type=authorization_code");
		$info = json_decode($result);
		if(!empty($info->openid)){
			$openId = $info->openid;
			file_get_contents('http://vip.gaosiedu.com/Vip/Weixin/addAttention/openId/'.$openId.'/k/'.md5('gsvip'.$openId.'wzl'));
			$baseUrl = "http://vip.gaosiedu.com/Vip/Weixin/";
			$url = $baseUrl.$_GET['state']."/openid/".$openId;
			header("Location:".$url);
		}
	}

	private function str_md5($str){
		return md5('gsvip'.$str.'wzl');
	}

	private function checkSignature(){
		$signature = $_GET["signature"];
		$timestamp = $_GET["timestamp"];
		$nonce = $_GET["nonce"];
		$token = TOKEN;
		$tmpArr = array($timestamp,$nonce,$token);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		if( ($tmpStr == $signature) || ( $tmpStr2 == $signature) ){
			return true;
		}else{
			return false;
		}
	}

	private function postData($http_url, $post_data = array()){
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $http_url );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 100 );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
		$content = curl_exec ( $ch );
		$error = curl_error ( $ch );
		curl_close ( $ch );
		if ($error != "") {
			header ( "HTTP/1.1 404 Not Found" );
			exit ();
		}
		return $content;
	}



	public function login() {
		$openId = $_GET['openId'];
		import('COM.Image.Captcha');
		$sessName = 'verifyCode';
		$captchaKey = Captcha::getCaptchaKey($sessName, 60, 20);
		$errors = array();
		if ($_POST) {
			if (Captcha::checkCaptcha($sessName, $_POST['captcha'])) {
				$userName = SysUtil::getUserName($_POST['uName']);
				$userPass = SysUtil::getUserPass($_POST['uPass']);
				$userType = SysUtil::safeString($_POST['user_type']);
				if(false == $userType) {
					$userType = USER_TYPE_EMPLOYEE;
				}
				$userInfo = array('user_name'=>$userName, 'user_pass'=>$userPass, 'user_type'=>$userType);
				$loginUser = User::getUser($userInfo);
				if (false == $loginUser->login(C('USER_COOKIE_NAME'))) {
					$errors[] = '登录失败，用户名或密码错误';
				} else {
					$weixinModel = D('WeixinVip');
					$userInfo = $this->get_currentUserInfo($userInfo);
					$result=$weixinModel->bindUser(array('user_key'=>$userInfo['user_key'],'teacherCode'=>$userInfo['sCode'],'appId'=>C('appId'),'openId'=>$_GET['openId']));
					if($result){
						$this->redirect(U('/Vip/Weixin/waitHelu',array('openId'=>$_GET['openId'])));
					}else{
						$errors[] = '登录失败，绑定教师账号失败';
					}
				}
			} else {
				$errors[] = '登录失败，验证码错误';
			}
		}
		$userTypes = User::getAdminUserTypes();
		unset($userTypes[USER_TYPE_EMPLOYEE]);
		$this->assign(get_defined_vars());
		$this->display ();
	}

	public function logout() {
		$_SESSION['openId'] = '';
		$result = D('WeixinVip')->delBind(array('openId'=>$_GET['weixinid']));
		if($result!=false){
			echo 1;
		}else{
			echo 0;
		}
		die;
	}

	/*最新活动*/
	public function newList(){
		$weixinModel = D('WeixinVip');
		$newList = $weixinModel->get_newsList();
		$this->assign(get_defined_vars());
		$this->display();
	}

	/*热点推荐*/
	public function hotList(){
		$weixinModel = D('WeixinVip');
		$hotList = $weixinModel->get_hotList();
		$this->assign(get_defined_vars());
		$this->display();
	}


	//二维码关注
	public function qrAttention(){
		$code = $_GET['c'];//校验码
		$key = empty($_GET['k']) ? 000 : trim($_GET['k']);//关注二维码的代码
		$time = $_GET['t'];//时间
		$tmpStr = $this->str_md5($_GET['openid'].$key.$time);
		if ($tmpStr == $code){
			D('WeixinVip')->addAttention($_GET['openid'],$key,$time);
		}
	}


	public function addAttention(){
		$userInfo = $this->checkWeixinInfo();
		$openId = $_GET['openid'];
		$key = empty($_GET['k']) ? 000 : trim($_GET['k']);//关注二维码的代码
		if ($key == $this->str_md5($openId)){
			D('WeixinVip')->addAttention($openId,$key,time());
		}
	}


	public function deleteAttention(){
		$code = $_GET['c'];//校验码
		$key = empty($_GET['k']) ? 000 : trim($_GET['k']);//关注二维码的代码
		$time = $_GET['t'];//时间
		$tmpStr = $this->str_md5($_GET['openid'].$key.$time);
		if ($tmpStr == $code){
			D('WeixinVip')->deleteAttention($_GET['openid'],$key,$time);
		}
	}

	public function waitHelu(){
		$userInfo = $this->checkWeixinInfo();
		$weixinModel = D('WeixinVip');
		if($userInfo['sCode']){
			$waitHeluList = $weixinModel->get_myStudentList(array('teacherCode'=>$userInfo['sCode'],'now'=>date('Y-m-d H:i:s'),'userTypeKey'=>$userInfo['user_type_key'],'overdue'=>1),0);
		}
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function keChengHeLu(){
		$userInfo = $this->checkWeixinInfo();
		$weixinModel = D('WeixinVip');
		$act = $_GET['act'];
		if($act == 'add'){
			$heluInfo = $weixinModel->get_heluInfo(array('helu_id'=>$_GET['helu_id'],'kecheng_code'=>$_GET['kecheng_code'],'lesson_no'=>$_GET['lesson_no'],'student_code'=>$_GET['student_code']));
			$heluInfo['helu_id'] = abs($_GET['helu_id']);
			$heluInfo['student_code'] = trim($_GET['student_code']);
			$heluInfo['student_name'] = urldecode($_GET['student_name']);
			$heluInfo['kecheng_code'] = trim($_GET['kecheng_code']);
			$heluInfo['lesson_no'] = abs($_GET['lesson_no']);
			$heluInfo['lesson_date'] = date('Y-m-d',$_GET['lesson_date']);
			$heluInfo['lesson_begin'] = date('H:i',$_GET['lesson_begin']);
			$heluInfo['lesson_end'] = date('H:i',$_GET['lesson_end']);
		}else{
			$heluInfo = $weixinModel->get_heluInfo(array('helu_id'=>$_GET['helu_id'],'kecheng_code'=>$_GET['kecheng_code'],'lesson_no'=>$_GET['lesson_no'],'student_code'=>$_GET['student_code']));
			$heluInfo['comment'] = str_replace("<br>","\n",str_replace("&nbsp;"," ",$heluInfo['comment']));
		}
		$wxImgNum = $weixinModel->get_heluFilesCount($_GET['helu_id'],0);
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function doHelu(){
		$userInfo = $this->checkWeixinInfo();
		$weixinModel = D('WeixinVip');
		$heluInfo = $weixinModel->get_heluInfo(array('helu_id'=>$_POST['helu_id'],'kecheng_code'=>$_POST['kecheng_code'],'lesson_no'=>$_POST['lesson_no'],'student_code'=>$_POST['student_code']));
		$studentsModel = D('VpStudents');
		if($weixinModel->do_helu($userInfo)){
			$status = 1;
			$msg = ($_POST['act']=='again'||$_POST['act']=='add')?'课时核录成功':'课时核录信息修改成功';
			if($_POST['is_sendsms']==1 && empty($_POST['is_send_sms'])){
				//给家长发短信
				$studentInfo = $studentsModel->get_studentContractInfo($_POST['student_code']);
				$to_mobile = !empty($studentInfo['sparents1phone'])?$studentInfo['sparents1phone']:$studentInfo['sparents2phone'];
				if(!empty($to_mobile)){
					//$to_mobile = '18210424918';
					$smsObj = new SmsSender();
					$smsContent = "家长您好，您的孩子".$_POST['student_name']."本次上课时间".date('Y/m/d',strtotime($_POST['lesson_date']))." ".$_POST['lesson_begin']."-".$_POST['lesson_end']."。本讲内容是".$_POST['lesson_topic']."，".$userInfo['real_name']."老师课堂评价如下：“".$_POST['comment']."”。感谢您对高思1对1的支持！";
					$smsReturn = $smsObj->sendSms($to_mobile,$smsContent);
				}
			}
			//录入课评统计
			$arr['student_name'] = $_POST['student_name'];
			$arr['lesson_date'] = $_POST['lesson_date'];
			$arr['lesson_topic'] = $_POST['lesson_topic'];
			$arr['teacher_name'] = $userInfo['real_name'];
			$arr['comment'] = $_POST['comment'];
			$arr['helu_time'] = date('Y-m-d H:i:s');
			$arr['helu_type'] = ($_POST['act']=='again'||$_POST['act']=='add')?1:2;
			$arr['is_select_sendsms'] = !empty($_POST['is_sendsms'])?1:0;
			$arr['is_trigger_sendsms'] = ($smsReturn==true)?1:0;
			$arr['is_upload_handouts'] = 0;
			$arr['to_mobile'] = $to_mobile;
			$studentsModel->addHeluLog($arr);
		}else{
			$status = 0;
			$msg = ($_POST['act']=='again'||$_POST['act']=='add')?'课时核录失败':'课时核录信息修改失败';
		}
		echo json_encode(array('status'=>$status,'msg'=>$msg));
	}


	public  function adjustKecheng(){
		$weixinModel = D('WeixinVip');
		$userInfo = $this->checkWeixinInfo();
		$kechengInfo = $weixinModel->get_kechengInfo(array('id'=>$_POST['helu_id']));
		$kechengInfo['dtdatereal'] = $_POST['date'].' 00:00:00';
		$kechengInfo['dtlessonbeginreal'] = date('Y-m-d H:i:s',strtotime($_POST['date'].' '.$_POST['start']));
		$kechengInfo['dtlessonendreal'] = date('Y-m-d H:i:s',strtotime($_POST['date'].' '.$_POST['end']));
		if($kechengInfo['dtlessonbeginreal']<=date('Y-m-d H:i:s')){
			$status = 0;
			$msg = '选择的时间已过期，请选择有效的时间段';
		}else{
			if($kechengInfo['dtlessonbeginreal']<$kechengInfo['dtlessonendreal']){
				if(!$weixinModel->checkIsCanOperate($kechengInfo,1)){
					$status = 0;
					$msg = '学员上课时间存在交叉，调课失败！';
				}else{
					ini_set("soap.wsdl_cache_enabled", "0");
					$param = array('id'=>abs($_POST['helu_id']),
					'sTeacherCode'=>$userInfo['sCode'],
					'dtDateReal'=>strtotime($kechengInfo['dtdatereal']),
					'dtLessonBeginReal'=>strtotime($kechengInfo['dtlessonbeginreal']),
					'dtLessonEndReal'=>strtotime($kechengInfo['dtlessonendreal'])
					);
					try {
						$soap = new SoapClient(C('aspxWebService'));
						$result = $soap->doTeacherUpdateLessonDate($param);
						$resultArr = WeixinCommAction::object2array($result);
						if(empty($resultArr['doTeacherPerPKResult'])){
							$status = 1;
							$msg = '调课成功';
						}else{
							$status = 0;
							$msg = '调课失败'.$resultArr['doTeacherPerPKResult'];
						}
					}catch(SoapFault $fault){
						$status = 0;
						$msg= "调课失败: ".$fault->faultstring."(".$fault->faultcode.")";
					}catch(Exception $e){
						$status = 0;
						$msg= "调课失败: ".$e->getMessage();
					}
				}
			}else{
				$status = 0;
				$msg = '上课结束时间必须大于开始时间！';
			}
		}
		echo json_encode(array('status'=>$status,'msg'=>$msg));
	}


	/*获取该教师的所有学生*/
	public function getAllStudents(){
		$studentHtml = '<option value="">请选择学员</option>';
		$userInfo = $this->checkWeixinInfo();
		if(!empty($userInfo['sCode'])){
			$students = D('WeixinVip')->getAllStudents(array('is_jieke'=>$_GET['jieke'],'type'=>$_GET['type'],'teacherCode'=>$userInfo['sCode']));
			if(!empty($students)){
				foreach ($students as $key=>$stu){
					$studentHtml .= '<option value="'.$stu['sstudentcode'].'">'.$stu['sstudentname'].'</option>';
				}
			}
		}
		echo $studentHtml;
	}


	public  function addKecheng(){
		$weixinModel = D('WeixinVip');
		$userInfo = $this->checkWeixinInfo();
		$kechengInfo = $weixinModel->get_kechengInfo(array('kecheng_code'=>$_POST['kecheng_code'],'student_code'=>$_POST['student_code'],'teacher_code'=>$userInfo['sCode']));
		$kechengInfo['dtdatereal'] = $_POST['date'].' 00:00:00';
		$kechengInfo['dtlessonbeginreal'] = date('Y-m-d H:i:s',strtotime($_POST['date'].' '.$_POST['start']));
		$kechengInfo['dtlessonendreal'] = date('Y-m-d H:i:s',strtotime($_POST['date'].' '.$_POST['end']));
		if($kechengInfo['dtlessonbeginreal']<=date('Y-m-d H:i:s')){
			$status = 0;
			$msg = '选择的时间已过期，请选择有效的时间段';
		}else{
			if($kechengInfo['dtlessonbeginreal']<$kechengInfo['dtlessonendreal']){
				if(!$weixinModel->checkIsCanOperate($kechengInfo,1)){
					$status = 0;
					$msg = '学员上课时间存在交叉，加课失败';
				}else{
					ini_set("soap.wsdl_cache_enabled", "0");
					$param = array('nRosterInfoId'=>abs($kechengInfo['nrosterinfoid']),
					'sTeacherCode'=>$userInfo['sCode'],
					'dtDateReal'=>strtotime($kechengInfo['dtdatereal']),
					'dtLessonBeginReal'=>strtotime($kechengInfo['dtlessonbeginreal']),
					'dtLessonEndReal'=>strtotime($kechengInfo['dtlessonendreal']),
					'nPrePKNum'=>1
					);
					try {
						$soap = new SoapClient(C('aspxWebService'));
						$result = $soap->doTeacherPerPK($param);
						$resultArr = WeixinCommAction::object2array($result);
						if(empty($resultArr['doTeacherPerPKResult'])){
							$status = 1;
							$msg = '加课成功';
						}else{
							$status = 0;
							$msg = '加课失败'.$resultArr['doTeacherPerPKResult'];
						}
					}catch(SoapFault $fault) {
						$status = 0;
						$msg= "加课失败: ".$fault->faultstring."(".$fault->faultcode.")";
					}catch(Exception $e) {
						$status = 0;
						$msg= "加课失败: ".$e->getMessage();
					}
				}
			}else{
				$status = 0;
				$msg = '上课结束时间必须大于开始时间！';
			}
		}

		echo json_encode(array('status'=>$status,'msg'=>$msg));
	}

	//学员信息
	public function studentInfo(){
		$userInfo = $this->checkWeixinInfo();
		$student_code = isset($_GET['student_code'])?$_GET['student_code']:'';
		$kecheng_code = isset($_GET['kecheng_code'])?$_GET['kecheng_code']:'';
		$lesson = isset($_GET['lesson'])?abs($_GET['lesson']):0;
		if(!empty($student_code)){
			$weixinModel = D('WeixinVip');
			$studentInfo = $weixinModel->get_studentContractInfo($student_code);
		}
		$this->assign(get_defined_vars());
		$this->display();
	}

	//已上课程
	public function kechengList(){
		$userInfo = $this->checkWeixinInfo();
		$student_code = isset($_GET['student_code'])?$_GET['student_code']:'';
		$kecheng_code = isset($_GET['kecheng_code'])?$_GET['kecheng_code']:'';
		$lesson = isset($_GET['lesson'])?abs($_GET['lesson']):0;
		$student_name = isset($_GET['student_name'])?urldecode($_GET['student_name']):'';
		$grade = isset($_GET['grade'])?urldecode($_GET['grade']):'';
		if(!empty($student_code)){
			$weixinModel = D('WeixinVip');
			$heluList = $weixinModel->get_heluListAll(array('student_code'=>$student_code,'kecheng_code'=>$kecheng_code));
		}
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function myStudents(){
		$userInfo = $this->checkWeixinInfo();
		$weixinModel = D('WeixinVip');
		if($userInfo['sCode']){
			$key_name = isset($_GET['key_name'])?trim($_GET['key_name']):'';
			$order = isset($_GET['order'])?strtolower(trim($_GET['order'])):'asc';
			$myStudentList = $weixinModel->get_myStudentAll(array('teacherCode'=>$userInfo['sCode'],'key_name'=>$key_name,'order'=>$order,'now'=>date('Y-m-d H:i:s')));
		}
		$this->assign(get_defined_vars());
		$this->display();
	}

	public function monthSchedule(){
		$userInfo = $this->checkWeixinInfo();
		$year = !empty($_GET['key'])?reset(explode('_',$_GET['key'])):date('Y');
		$month = !empty($_GET['key'])?end(explode('_',$_GET['key'])):date('n');
		$day = date('j');
		$totalHours = 0;
		$studentArr = array();
		import('ORG.Util.Calendar');
		$calendar = new Calendar($year,$month,$day);
		$selectData = $calendar->monthSelect();
		$monthData = $calendar->monthData();
		if(!empty($monthData)){
			foreach ($monthData as $key=>$data){
				$lessonList = D('WeixinVip')->get_lessonList($userInfo['sCode'],strtotime($data['year'].'-'.$data['month'].'-'.$data['day']),'','','',1);
				$monthData[$key]['lesson'] = $lessonList['list'];
				$totalHours += $lessonList['total_hours'];
				if(!empty($lessonList['student_arr'])){
					$studentArr = array_merge($lessonList['student_arr'],$studentArr);
				}
			}
		}
		$timeArr = C('timeArr');
		$dateData = $calendar->dateSelect();
		$totalStudents = count(array_unique($studentArr));
		$this->assign(get_defined_vars());
		$this->display();
	}

	public function weekSchedule(){
		$userInfo = $this->checkWeixinInfo();
		$now = time();
		$w=strftime('%u',$now);
		$start = !empty($_GET['key'])?reset(explode('_',$_GET['key'])):date('Y-m-d',$now-($w-1)*86400);
		$end = !empty($_GET['key'])?end(explode('_',$_GET['key'])):date('Y-m-d',$now+(7-$w)*86400);
		$totalHours = 0;
		$studentArr = array();
		import('ORG.Util.Calendar');
		$calendar = new Calendar($year,$month,$day);
		$selectData = $calendar->weekSelect();
		$weekData = $calendar->weekData($start,$end);
		if(!empty($weekData)){
			foreach ($weekData as $key=>$data){
				$lessonList = D('WeixinVip')->get_lessonList($userInfo['sCode'],strtotime($data['day']),'','','',1);
				$weekData[$key]['lesson'] = $lessonList['list'];
				$totalHours += $lessonList['total_hours'];
				if(!empty($lessonList['student_arr'])){
					$studentArr = array_merge($lessonList['student_arr'],$studentArr);
				}
			}
		}
		$timeArr = C('timeArr');
		$dateData = $calendar->dateSelect();
		$totalStudents = count(array_unique($studentArr));
		$this->assign(get_defined_vars());
		$this->display();
	}

	public function wxAddImg(){
		$wxImgUrl = SysUtil::safeString(base64_decode($_GET['pic']));
		$openId = SysUtil::safeString($_GET['openid']);
		if(!empty($wxImgUrl) && !empty($openId)){
			/*$serviceUrl = $this->imgAdvanceSave($wxImgUrl);
			if(!empty($serviceUrl)){
			$return = D('WeixinVip')->add_wxImg($wxImgUrl,$serviceUrl,$openId);
			if($return!=false){
			$status = 1;
			}else{
			$status = 0;
			}
			}else{
			$status = 0;
			}*/
			$serviceUrl = '';
			$return = D('WeixinVip')->add_wxImg($wxImgUrl,$serviceUrl,$openId);
			if($return!=false){
				$status = 1;
			}else{
				$status = 0;
			}

		}else{
			$status = 0;
		}
		echo $status;
		die;
	}


	public function confirmImg(){
		$userInfo = $this->checkWeixinInfo();
		$wxImgList = D('WeixinVip')->get_wxImgList(SysUtil::safeString($userInfo['openId']));
		$this->assign(get_defined_vars());
		$this->display();
	}



	public function get_lessonList(){
		$selectAll = isset($_GET['selectAll'])?$_GET['selectAll']:0;
		$lessonHtml = '<option value="">请选择上课时间</option>';
		$lessonList = D('WeixinVip')->get_lessonList($_GET['teacherCode'],'',$_GET['studentCode'],$_GET['kechengCode'],date('Y-m-d H:i:s'),$selectAll);
		$lessonList = $lessonList['list'];
		if(!empty($lessonList)){
			foreach ($lessonList as $key=>$lesson){
				$lessonHtml .= '<option value="'.$lesson['id'].'">'.date("Y-m-d",strtotime($lesson["dtdatereal"])).' '.date("H:i",strtotime($lesson["dtlessonbeginreal"])).'~'.date("H:i",strtotime($lesson["dtlessonendreal"])).'</option>';
			}
		}
		echo $lessonHtml;
	}


	public function delWxImg(){
		$status = 0;
		$html = '';
		if(D('WeixinVip')->del_wxImg($_GET['id'])){
			$status = 1;
			$userInfo = $this->checkWeixinInfo();
			$wxImgList = D('WeixinVip')->get_wxImgList(SysUtil::safeString($userInfo['openId']));
			if(!empty($wxImgList)){
				foreach ($wxImgList as $key=>$img){
					$html .= '<li><a href="'.$img['serviceurl_show'].'" rel="lightbox" id="ShowLightBox"><img src="'.$img['serviceurl_show'].'" /></a><a href="javascript:void(0)" onclick="del_wxImg(\''. U('Vip/Weixin/delWxImg',array('id'=>$img['id'])).'\')">删除照片</a></li>';
				}
			}
		}
		echo json_encode(array('status'=>$status,'html'=>$html));
		exit;
	}


	public function doConfirmImg(){
		$weixinModel = D('WeixinVip');
		$userInfo = $this->checkWeixinInfo();
		$helu_id = abs($_POST['helu_id']);
		$return = !empty($_GET['return'])?$_GET['return']:'confirmImg';
		if(!empty($helu_id)){
			$heluInfo = $weixinModel->get_viewHeluInfo($helu_id);
			$wxImgList = $weixinModel->get_wxImgList(SysUtil::safeString($userInfo['openId']));
			if(!empty($wxImgList)){
				$imgUrlStr = '';
				foreach ($wxImgList as $key=>$wxImg){
					$weixinModel->update_wxImgStatus(1,$wxImg['id']);
					//$imgUrlStr .= str_replace('/vhost/apps/eap','',$wxImg['serviceurl']).'|';
					$imgUrlStr .= '/'.end(explode('/eap/',$wxImg['serviceurl'])).'|';
				}
				$is_helu = $weixinModel->check_isHelu($helu_id);
				$act = ($is_helu == 1)?'update':'add';
				if($weixinModel->add_heluFiles(array('helu_id'=>$helu_id,'title'=>$heluInfo['skechengname'].'_'.$heluInfo['steachername'].'_'.$heluInfo['sstudentname'].'_'.$heluInfo['nlessonno'].'_课程讲义_'.date('Y_m_d',strtotime($heluInfo['dtdatereal'])),'url'=>$imgUrlStr,'type'=>0))){
					$this->success('图片确认成功',U('Vip/Weixin/keChengHeLu',array('act'=>$act,'helu_id'=>$helu_id,'student_code'=>$heluInfo['sstudentcode'],'student_name'=>$heluInfo['sstudentname'],'kecheng_code'=>$heluInfo['skechengcode'],'lesson_no'=>$heluInfo['nlessonno'],'lesson_date'=>strtotime($heluInfo['dtdatereal']),'lesson_begin'=>strtotime($heluInfo['dtlessonbeginreal']),'lesson_end'=>strtotime($heluInfo['dtlessonendreal']))));
				}else{
					$this->error('图片确认失败',U('Vip/Weixin/'.$return));
				}
			}else{
				$this->error('您没有未确认上传的图片，请先进行图片上传',U('Vip/Weixin/'.$return));
			}
		}else{
			$this->error('请先选择上课时间',U('Vip/Weixin/'.$return));
		}
	}


	/*public function imgAdvanceSave($pic){
	$targetFolder = UPLOAD_PATH.date('Y-m-d').'/';
	if(!file_exists($targetFolder)){
	mkdir($targetFolder);
	}
	$filename = uniqid(mt_rand(), true)."_wx";
	$tmp = substr($pic,-10);//截取后10位，然后找是否有扩展名
	if (strstr($tmp,'.')){
	$extension = end(explode('.', $pic));
	$extension = '.'.$extension;
	}else{
	$extension = '.jpg';
	}
	$endurl= $targetFolder.$filename.$extension;
	$hander = curl_init();
	$fp = fopen($endurl,'wb');
	curl_setopt($hander,CURLOPT_URL,$pic);
	curl_setopt($hander,CURLOPT_FILE,$fp);
	curl_setopt($hander,CURLOPT_HEADER,0);
	curl_setopt($hander,CURLOPT_FOLLOWLOCATION,1);
	//curl_setopt($hander,CURLOPT_RETURNTRANSFER,false);//以数据流的方式返回数据,当为false是直接显示出来
	curl_setopt($hander,CURLOPT_TIMEOUT,60);
	curl_exec($hander);
	curl_close($hander);
	fclose($fp);
	if(file_exists($endurl)){
	return $endurl;
	}
	}*/


	public function getKechengList(){
		$kechengHtml = '<option value="">请选择课程</option>';
		$kechengList = D('WeixinVip')->get_kechengAll(array('is_jieke'=>0,'studentCode'=>$_GET['stuCode'],'teacherCode'=>$_GET['teacherCode']));
		if(!empty($kechengList)){
			foreach ($kechengList as $key=>$kecheng){
				$kechengHtml .= '<option value="'.$kecheng['skechengcode'].'">'.$kecheng['skechengname'].'('.$kecheng['skechengcode'].')</option>';
			}
		}
		echo $kechengHtml;
	}

	/**
	 * 上传测试卷
	 *
	 */
	public function confirmItembank(){
		$userInfo = $this->checkWeixinInfo();
		$wxImgList = D('WeixinVip')->get_wxImgList(SysUtil::safeString($userInfo['openId']));
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function doConfirmItembank(){
		$weixinModel = D('WeixinVip');
		$userInfo = $this->checkWeixinInfo();
		$helu_id = abs($_POST['helu_id']);
		if(!empty($helu_id)){
			$heluInfo = $weixinModel->get_viewHeluInfo($helu_id);
			$wxImgList = $weixinModel->get_wxImgList(SysUtil::safeString($userInfo['openId']));
			if(!empty($wxImgList)){
				$imgUrlStr = '';
				foreach ($wxImgList as $key=>$wxImg){
					$weixinModel->update_wxImgStatus(1,$wxImg['id']);
					//$imgUrlStr .= str_replace('/vhost/apps/eap','',$wxImg['serviceurl']).'|';
					$imgUrlStr .= '/'.end(explode('/eap/',$wxImg['serviceurl'])).'|';
				}
				$is_helu = $weixinModel->check_isHelu($helu_id);
				$act = ($is_helu == 1)?'update':'add';
				if($weixinModel->add_heluFiles(array('helu_id'=>$helu_id,'title'=>$heluInfo['skechengname'].'_'.$heluInfo['steachername'].'_'.$heluInfo['sstudentname'].'_'.$heluInfo['nlessonno'].'_测试卷_'.date('Y_m_d',strtotime($heluInfo['dtdatereal'])),'url'=>$imgUrlStr,'type'=>1,'itembank_score'=>$_POST['itembank_score'],'act'=>$act))){
					if(!empty($_POST['itembank_score'])){
						if($weixinModel->updateItembankScore(array('act'=>$act,'helu_id'=>$helu_id,'student_code'=>$heluInfo['sstudentcode'],'student_name'=>$heluInfo['sstudentname'],'kecheng_code'=>$heluInfo['skechengcode'],'lesson_no'=>$heluInfo['nlessonno'],'lesson_date'=>strtotime($heluInfo['dtdatereal']),'lesson_begin'=>strtotime($heluInfo['dtlessonbeginreal']),'lesson_end'=>strtotime($heluInfo['dtlessonendreal']),'itembank_score'=>$_POST['itembank_score']))){
							$act = 'update';
						}
					}
					$status = true;
					$msg = '您可以在教师系统PC端中查看刚上传的测试卷内容';
				}else{
					$status = false;
					$msg = '测试卷上传失败';
				}
			}else{
				$status = false;
				$msg = '您没有未确认上传的图片，请先进行图片上传';
			}
		}else{
			$status = false;
			$msg = '请先选择对应的学员及课次等信息';
		}
		echo json_encode(array('status'=>$status,'msg'=>$msg,'url'=>$url));
		die();
	}


	public function confirmProgram(){
		$userInfo = $this->checkWeixinInfo();
		$wxImgList = D('WeixinVip')->get_wxImgList(SysUtil::safeString($userInfo['openId']));
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function doConfirmProgram(){
		$weixinModel = D('WeixinVip');
		$userInfo = $this->checkWeixinInfo();

		if(!empty($_POST['student_code']) && !empty($_POST['kecheng_code']) ){
			$arr = $_POST;
			$wxImgList = $weixinModel->get_wxImgList(SysUtil::safeString($userInfo['openId']));
			if(!empty($wxImgList)){
				$imgUrlStr = '';
				foreach ($wxImgList as $key=>$wxImg){
					$weixinModel->update_wxImgStatus(1,$wxImg['id']);
					//$imgUrlStr .= str_replace('/vhost/apps/eap','',$wxImg['serviceurl']).'|';
					$imgUrlStr .= '/'.end(explode('/eap/',$wxImg['serviceurl'])).'|';
				}
				$arr['url'] = $imgUrlStr;
				$ip = $this->getClientIp();
				if($weixinModel->add_trainingProgram($arr,$userInfo['user_key'],$ip)){
					$status = 1;
					$msg = '辅导方案确认上传成功，您可以在教师系统PC端中查看刚才上传的辅导方案内容';
				}else{
					$status = 0;
					$msg = '辅导方案确认上传失败';
				}
			}else{
				$status = 0;
				$msg = '您没有未确认上传的图片，请先进行图片上传';
			}
		}else{
			$status = 0;
			$msg = '请选择正确的学员和课程信息';
		}
		echo json_encode(array('status'=>$status,'msg'=>$msg));
	}

	public function doOverdue(){
		$status = 0;
		$helu_id = abs($_GET['helu_id']);
		if(D('WeixinVip')->do_overdue($helu_id)){
			$status = 1;
		}
		echo json_encode(array('status'=>$status));
		exit;
	}




	/*教师系统改版0615==============================================================================================*/
	public function newWaitHelu(){
		$userInfo = $this->checkWeixinInfo();
		$weixinModel = D('WeixinVip');
		if($userInfo['sCode']){
			$waitHeluList = $weixinModel->get_waitHeluList(array('teacherCode'=>$userInfo['sCode'],'now'=>date('Y-m-d H:i:s'),'userTypeKey'=>$userInfo['user_type_key'],'overdue'=>1));
		}
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function newMyStudents(){
		$userInfo = $this->checkWeixinInfo();
		$weixinModel = D('WeixinVip');
		if($userInfo['sCode']){
			$key_name = isset($_GET['key_name'])?trim($_GET['key_name']):'';
			$order = isset($_GET['order'])?strtolower(trim($_GET['order'])):'asc';
			$myStudentList = $weixinModel->get_myStudentAll_new(array('teacherCode'=>$userInfo['sCode'],'key_name'=>$key_name,'order'=>$order,'now'=>date('Y-m-d H:i:s')));
		}
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function newStudentInfo(){
		$userInfo = $this->checkWeixinInfo();
		$student_code = isset($_GET['student_code'])?$_GET['student_code']:'';
		$kecheng_code = isset($_GET['kecheng_code'])?$_GET['kecheng_code']:'';
		$lesson = isset($_GET['lesson'])?abs($_GET['lesson']):0;
		if(!empty($student_code)){
			$weixinModel = D('WeixinVip');
			$studentInfo = $weixinModel->get_studentContractInfo($student_code);
		}
		$this->assign(get_defined_vars());
		$this->display();
	}

	public function newKechengList(){
		$userInfo = $this->checkWeixinInfo();
		$student_code = isset($_GET['student_code'])?$_GET['student_code']:'';
		$student_name = isset($_GET['student_name'])?urldecode($_GET['student_name']):'';
		$grade = isset($_GET['grade'])?urldecode($_GET['grade']):'';
		if(!empty($student_code)){
			$weixinModel = D('WeixinVip');
			$lessonList = $weixinModel->get_lessonAll(array('student_code'=>$student_code,'teacherCode'=>$userInfo['sCode']));
		}
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function recordLessonTrack(){
		$helu_id = abs($_GET['helu_id']);
		$heluInfo = $this->getHeluInfo($helu_id);
		if(empty($heluInfo['lecture_id'])){
			$this->error('请先进行备课',U('Vip/Weixin/newMyStudents'));
		}
		$weixinModel = D('WeixinVip');
		//上次课程id
		$last_helu_id = $weixinModel->get_lastHeluId($heluInfo);
		$last_lesson_heluInfo = $this->getHeluInfo($last_helu_id);

		//获取星级
		//$levelArr = D('VpSubject')->get_levelList();
		//$numberKey = C('NUMBER_KEY');
		//$optionKeyArr = C('OPTIONS_KEY');
		$this->assign(get_defined_vars());
		$this->display();
	}

	public function getHeluInfo($heluId){
		$key = md5($heluId);
		import('ORG.Util.NCache');
		$cache = NCache::getCache();
		//$heluInfo = $cache->get('heluInfo', $key);
		if(false == $heluInfo) {
			$weixinModel = D('WeixinVip');
			$heluInfo = $weixinModel->get_heluInfo_new($heluId);
			$cache->set('heluInfo', $key, $heluInfo);
		}
		return $heluInfo;
	}


	//保存答题情况
	public function savePartOne(){
		$status = 0;
		$msg = '';
		if(!empty($_POST['module_answer'])){
			$weixinModel = D('WeixinVip');
			$return = $weixinModel->recordLessonTrack($_POST);
			if($return){
				$status = 1;
				//更新缓存
				$key = md5($_POST['helu_id']);
				import('ORG.Util.NCache');
				$cache = NCache::getCache();
				/*$heluInfo = $weixinModel->get_heluInfo_new($_POST['helu_id']);
				$cache->set('heluInfo', $key, $heluInfo);

				$last_helu_id = $weixinModel->get_lastHeluId($heluInfo);
				$last_lesson_heluInfo = $weixinModel->get_heluInfo_new($last_helu_id);
				$last_key = md5($last_helu_id);
				$cache->set('heluInfo', $last_key, $last_lesson_heluInfo);*/
				$heluInfo = $this->getHeluInfo($_POST['helu_id']);
				$newHeluInfo = $weixinModel->get_baseHeluInfo($_POST['helu_id']);
				$newHeluInfo['lecture_info'] = $heluInfo['lecture_info'];
				$cache->set('heluInfo', $key, $newHeluInfo);
				
				$last_helu_id = $weixinModel->get_lastHeluId($heluInfo);
				$last_lesson_heluInfo = $this->getHeluInfo($last_helu_id);
				$new_last_lesson_heluInfo = $weixinModel->get_baseHeluInfo($last_helu_id);
				$new_last_lesson_heluInfo['lecture_info'] = $last_lesson_heluInfo['lecture_info'];
				$last_key = md5($last_helu_id);
				$cache->set('heluInfo', $last_key, $new_last_lesson_heluInfo);
				
				$msg = '课堂掌握情况保存成功';
			}else{
				$msg = '课堂掌握情况保存失败';
			}
		}else{
			$msg = '请填写课堂掌握情况';
		}

		echo json_encode(array('status'=>$status,'msg'=>$msg,'url'=>U('Vip/Weixin/recordLessonComment',array('helu_id'=>$_POST['helu_id']))));
	}



	public function recordLessonComment(){
		$userInfo = $this->checkWeixinInfo();
		$helu_id = abs($_GET['helu_id']);
		$heluInfo = $this->getHeluInfo($helu_id);
		$heluInfo['comment'] = str_replace("<br>","\r\n",$heluInfo['comment']);
		if(empty($heluInfo['lecture_id'])){
			$this->error('请先进行备课',U('Vip/Weixin/newMyStudents'));
		}

		//获取星级
		$levelArr = D('VpSubject')->get_levelList();
		$numberKey = C('NUMBER_KEY');
		$optionKeyArr = C('OPTIONS_KEY');

		$wxImgNum = D('WeixinVip')->get_heluFilesCount($_GET['helu_id'],0);
		$this->assign(get_defined_vars());
		$this->display();
	}



	//保存课堂评价
	public function savePartTwo(){
		$status = 0;
		$msg = '';
		if(!empty($_POST['helu_id']) && !empty($_POST['comment']) && !empty($_POST['dimension_id_str']) && !empty($_POST['level_str'])){
			$weixinModel = D('WeixinVip');
			$studentsModel = D('VpStudents');
			$return = $weixinModel->recordLessonComment($_POST);
			if($return){
				$userInfo = $this->checkWeixinInfo();
				if($_POST['is_send_sms']==1){
					//给家长发短信
					$studentInfo = $studentsModel->get_studentContractInfo($_POST['student_code']);
					$to_mobile = !empty($studentInfo['sparents1phone'])?$studentInfo['sparents1phone']:$studentInfo['sparents2phone'];
					if(!empty($to_mobile)){
						//$to_mobile = '18210424918';
						$smsObj = new SmsSender();
						$smsContent = "家长您好，您的孩子".$_POST['student_name']."本次上课时间".date('Y/m/d',strtotime($_POST['lesson_date']))." ".$_POST['lesson_begin']."-".$_POST['lesson_end']."。本讲内容是".$_POST['lesson_topic']."，".$userInfo['real_name']."老师课堂评价如下：“".$_POST['comment']."”。感谢您对高思1对1的支持！";
						$smsReturn = $smsObj->sendSms($to_mobile,$smsContent);
					}
				}

				//录入课评统计
				$arr['student_name'] = $_POST['student_name'];
				$arr['lesson_date'] = $_POST['lesson_date'];
				$arr['lesson_topic'] = $_POST['lesson_topic'];
				$arr['teacher_name'] = $userInfo['real_name'];
				$arr['comment'] = $_POST['comment'];
				$arr['helu_time'] = date('Y-m-d H:i:s');
				$arr['helu_type'] = ($_POST['act']=='add')?1:2;
				$arr['is_select_sendsms'] = !empty($_POST['is_send_sms'])?1:0;
				$arr['is_trigger_sendsms'] = ($smsReturn==true)?1:0;
				$arr['is_upload_handouts'] = 0;
				$arr['to_mobile'] = $to_mobile;
				$studentsModel->addHeluLog($arr);

				$status = 1;
				//更新缓存
				$key = md5($_POST['helu_id']);
				import('ORG.Util.NCache');
				$cache = NCache::getCache();
				//$heluInfo = $weixinModel->get_heluInfo_new($_POST['helu_id']);
				//$cache->set('heluInfo', $key, $heluInfo);
				$heluInfo = $this->getHeluInfo($_POST['helu_id']);
				$newHeluInfo = $weixinModel->get_baseHeluInfo($_POST['helu_id']);
				$newHeluInfo['lecture_info'] = $heluInfo['lecture_info'];
				$cache->set('heluInfo', $key, $newHeluInfo);
				$msg = '课堂评价保存成功';
			}else{
				$msg = '课堂评价保存失败';
			}
		}else{
			$msg = '请填写课堂评价';
		}

		echo json_encode(array('status'=>$status,'msg'=>$msg));
	}



	public function newMonthSchedule(){
		$userInfo = $this->checkWeixinInfo();
		$nowDate = date('Y-m-d');
		$year = !empty($_GET['key'])?reset(explode('_',$_GET['key'])):date('Y');
		$month = !empty($_GET['key'])?end(explode('_',$_GET['key'])):date('n');
		$day = date('j');
		$totalHours = 0;
		$oneToOne = 0;
		$oneToTwo = 0;
		$groupClass = 0;
		$groupClassMoney = 0;
		$studentArr = array();
		import('ORG.Util.Calendar');
		$calendar = new Calendar($year,$month,$day);
		$selectData = $calendar->monthSelect();
		$monthData = $calendar->monthData();
		if(!empty($monthData)){
			foreach ($monthData as $key=>$data){
				$lessonList = D('WeixinVip')->get_lessonList($userInfo['sCode'],strtotime($data['year'].'-'.$data['month'].'-'.$data['day']),'','','',5);
				$monthData[$key]['lesson'] = $lessonList['list'];
				$totalHours += $lessonList['total_hours'];
				$oneToOne += $lessonList['one_to_one'];
				$oneToTwo += $lessonList['one_to_two'];
				$groupClass += $lessonList['group_class'];
				$groupClassMoney += $lessonList['group_class_money'];
				if(!empty($lessonList['student_arr'])){
					$studentArr = array_merge($lessonList['student_arr'],$studentArr);
				}
			}
		}
		if($groupClassMoney*0.3 > $groupClass*45){
			$groupClassMoney = $groupClassMoney*0.3;
		}else{
			$groupClassMoney = $groupClass*45;
		}
		$oneToTwo = $oneToTwo*0.5;
		$timeArr = C('timeArr');
		$dateData = $calendar->dateSelect();
		$totalStudents = count(array_unique($studentArr));
		$this->assign(get_defined_vars());
		$this->display();
	}

	public function newWeekSchedule(){
		$userInfo = $this->checkWeixinInfo();
		$now = time();
		$nowDate = date('Y-m-d');
		$w=strftime('%u',$now);
		$start = !empty($_GET['key'])?reset(explode('_',$_GET['key'])):date('Y-m-d',$now-($w-1)*86400);
		$end = !empty($_GET['key'])?end(explode('_',$_GET['key'])):date('Y-m-d',$now+(7-$w)*86400);
		$totalHours = 0;
		$oneToOne = 0;
		$oneToTwo = 0;
		$groupClass = 0;
		$groupClassMoney = 0;
		$studentArr = array();
		import('ORG.Util.Calendar');
		$calendar = new Calendar($year,$month,$day);
		$selectData = $calendar->weekSelect();
		$weekData = $calendar->weekData($start,$end);
		if(!empty($weekData)){
			foreach ($weekData as $key=>$data){
				$lessonList = D('WeixinVip')->get_lessonList($userInfo['sCode'],strtotime($data['day']),'','','',5);
				$weekData[$key]['lesson'] = $lessonList['list'];
				$totalHours += $lessonList['total_hours'];
				$oneToOne += $lessonList['one_to_one'];
				$oneToTwo += $lessonList['one_to_two'];
				$groupClass += $lessonList['group_class'];
				$groupClassMoney += $lessonList['group_class_money'];
				if(!empty($lessonList['student_arr'])){
					$studentArr = array_merge($lessonList['student_arr'],$studentArr);
				}
			}
		}
		if($groupClassMoney*0.3 > $groupClass*45){
			$groupClassMoney = $groupClassMoney*0.3;
		}else{
			$groupClassMoney = $groupClass*45;
		}
		$oneToTwo = $oneToTwo*0.5;
		$timeArr = C('timeArr');
		$dateData = $calendar->dateSelect();
		$totalStudents = count(array_unique($studentArr));
		$this->assign(get_defined_vars());
		$this->display();
	}



	public function confirmRecordImg(){
		$userInfo = $this->checkWeixinInfo();
		$wxImgList = D('WeixinVip')->get_wxImgList(SysUtil::safeString($userInfo['openId']));
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function doConfirmRecordImg(){
		$weixinModel = D('WeixinVip');
		$userInfo = $this->checkWeixinInfo();
		$helu_id = abs($_POST['helu_id']);
		$return = !empty($_GET['return'])?$_GET['return']:'confirmRecordImg';
		if(!empty($helu_id)){
			$heluInfo = $weixinModel->get_viewHeluInfo($helu_id);
			$wxImgList = $weixinModel->get_wxImgList(SysUtil::safeString($userInfo['openId']));
			if(!empty($wxImgList)){
				$imgUrlStr = '';
				foreach ($wxImgList as $key=>$wxImg){
					$weixinModel->update_wxImgStatus(1,$wxImg['id']);
					//$imgUrlStr .= str_replace('/vhost/apps/eap','',$wxImg['serviceurl']).'|';
					$imgUrlStr .= '/'.end(explode('/eap/',$wxImg['serviceurl'])).'|';
				}
				$is_helu = $weixinModel->check_isHelu($helu_id);
				$act = ($is_helu == 1)?'update':'add';
				if($weixinModel->add_heluFiles(array('helu_id'=>$helu_id,'title'=>$heluInfo['skechengname'].'_'.$heluInfo['steachername'].'_'.$heluInfo['sstudentname'].'_'.$heluInfo['nlessonno'].'_课程讲义_'.date('Y_m_d',strtotime($heluInfo['dtdatereal'])),'url'=>$imgUrlStr,'type'=>3))){
					$this->success('轨照确认成功',U('Vip/Weixin/recordLessonTrack',array('helu_id'=>$helu_id)));
				}else{
					$this->error('轨照确认失败',U('Vip/Weixin/'.$return));
				}
			}else{
				$this->error('您没有未确认上传的轨照，请先进行轨照上传',U('Vip/Weixin/'.$return));
			}
		}else{
			$this->error('请先选择上课时间',U('Vip/Weixin/'.$return));
		}
	}



	//生成课节报告
	public function createLessonReport(){
		$helu_id = $_REQUEST['helu_id'];
		$status = 0;
		$report_url = '';
		if(!empty($helu_id)){
			$userInfo['real_name'] = $_POST['teacher_name'];
			$newStudentsModel = D('VpNewStudents');
			//$heluInfo = $newStudentsModel->get_heluInfo($helu_id);
			$heluInfo = $this->getHeluInfo($helu_id);

			//上次课程id
			$last_helu_id = $newStudentsModel->get_lastHeluId($heluInfo);
			//$last_lesson_heluInfo = $newStudentsModel->get_heluInfo($last_helu_id);
			$last_lesson_heluInfo = $this->getHeluInfo($last_helu_id);

			//获取星级
			$levelArr = D('VpSubject')->get_levelList();
			$numberKey = C('NUMBER_KEY');

			//$html = file_get_contents(HTML_PATH.C('report_demo'));
			//$wxHtml = file_get_contents(HTML_PATH.C('report_wx_demo'));
			$html = $this->getReportDemo('report_demo');
			$wxHtml = $this->getReportDemo('report_wx_demo');
			
			$wxHtml = str_replace('jsInfoUrl',"'".APP_URL."/Vip/Weixin/getSignaturePackage'",$wxHtml);
			$wxHtml = str_replace('share_title',$heluInfo['sstudentname'].'课节报告_'.date('Y.m.d',time()),$wxHtml);
			$wxHtml = str_replace('share_desc',"老师说：".mb_substr(str_replace("<br>","",$heluInfo['comment']),0,15,'utf-8')."...（点击查看报告）",$wxHtml);

			//替换模板数据=======start========================================================================
			$html = str_replace('{student_name}',$heluInfo['sstudentname'],$html);
			$html = str_replace('{lesson_topic}',$heluInfo['lesson_topic'],$html);
			$html = str_replace('{lesson_topic}',$heluInfo['lesson_topic'],$html);
			$html = str_replace('{lesson_time}',date('Y-m-d',strtotime($heluInfo['dtdatereal'])).' '.date('H:i',strtotime($heluInfo['dtlessonbeginreal'])).'至'.date('H:i',strtotime($heluInfo['dtlessonendreal'])),$html);
			$html = str_replace('{beike_time}',!empty($heluInfo['lecture_info']['created_time'])?date('Y-m-d H:i:s',$heluInfo['lecture_info']['created_time']):'',$html);
			$html = str_replace('{shangke_time}',date('Y-m-d',strtotime($heluInfo['dtdatereal'])).' '.date('H:i',strtotime($heluInfo['dtlessonbeginreal'])).'~'.date('H:i',strtotime($heluInfo['dtlessonendreal'])),$html);
			$html = str_replace('{record_time}',!empty($heluInfo['lesson_report_createtime'])?$heluInfo['lesson_report_createtime']:date('Y-m-d H:i:s'),$html);
			$html = str_replace('{comment_time}','',$html);


			$wxHtml = str_replace('{student_name}',$heluInfo['sstudentname'],$wxHtml);
			$wxHtml = str_replace('{lesson_topic}',$heluInfo['lesson_topic'],$wxHtml);
			$wxHtml = str_replace('{lesson_topic}',$heluInfo['lesson_topic'],$wxHtml);
			$wxHtml = str_replace('{lesson_time}',date('Y-m-d',strtotime($heluInfo['dtdatereal'])).' '.date('H:i',strtotime($heluInfo['dtlessonbeginreal'])).'至'.date('H:i',strtotime($heluInfo['dtlessonendreal'])),$wxHtml);
			$wxHtml = str_replace('{beike_time}',!empty($heluInfo['lecture_info']['created_time'])?date('Y-m-d H:i:s',$heluInfo['lecture_info']['created_time']):'',$wxHtml);
			$wxHtml = str_replace('{shangke_time}',date('Y-m-d',strtotime($heluInfo['dtdatereal'])).' '.date('H:i',strtotime($heluInfo['dtlessonbeginreal'])).'~'.date('H:i',strtotime($heluInfo['dtlessonendreal'])),$wxHtml);
			$wxHtml = str_replace('{record_time}',!empty($heluInfo['lesson_report_createtime'])?$heluInfo['lesson_report_createtime']:date('Y-m-d H:i:s'),$wxHtml);
			$wxHtml = str_replace('{comment_time}','',$wxHtml);


			//判断服务流程
			$style1 = '';
			$style2 = '';
			$style3 = '';
			$style4 = '';
			if(!empty($heluInfo['lecture_info'])){
				if($heluInfo['dtlessonbeginreal']<=date('Y-m-d H:i:s')){
					if(!empty($heluInfo['module_answer'])||!empty($heluInfo['practise_answer'])||!empty($heluInfo['work_answer'])||!empty($heluInfo['lesson_report_url'])||!empty($heluInfo['lesson_record_img'])){
						$style3 = 'on';
					}else{
						$style2 = 'on';
					}
				}else{
					$style1 = 'on';
				}
			}
			$html = str_replace('{style1}',$style1,$html);
			$html = str_replace('{style2}',$style2,$html);
			$html = str_replace('{style3}',$style3,$html);
			$html = str_replace('{style4}',$style4,$html);

			$wxHtml = str_replace('{style1}',$style1,$wxHtml);
			$wxHtml = str_replace('{style2}',$style2,$wxHtml);
			$wxHtml = str_replace('{style3}',$style3,$wxHtml);
			$wxHtml = str_replace('{style4}',$style4,$wxHtml);

			$html = str_replace('{kecheng_name}',$heluInfo['skechengname'],$html);
			$html = str_replace('{lesson_no}',$heluInfo['nlessonno'],$html);
			$html = str_replace('{teacher_name}',$userInfo['real_name'],$html);
			$html = str_replace('{classadviser_name}',$heluInfo['sclassadvisername'],$html);

			$wxHtml = str_replace('{kecheng_name}',$heluInfo['skechengname'],$wxHtml);
			$wxHtml = str_replace('{lesson_no}',$heluInfo['nlessonno'],$wxHtml);
			$wxHtml = str_replace('{teacher_name}',$userInfo['real_name'],$wxHtml);
			$wxHtml = str_replace('{classadviser_name}',$heluInfo['sclassadvisername'],$wxHtml);



			$dimensionCommentHtml = '';
			$dimensionCommentHtml_wx = '';
			$levelArr = D('VpSubject')->get_levelList();
			$tempLevel = array();
			if(!empty($heluInfo['dimension'])){
				foreach ($heluInfo['dimension'] as $key=>$dimension){
					$tempLevel[] = $dimension['level'];
					$dimensionCommentHtml .= '<li>'.$dimension['title'].'：<span style="display: inline-block;height: 32px;overflow: hidden;vertical-align: middle;width: 136px;">';
					$dimensionCommentHtml_wx .= '<li>'.$dimension['title'].'：<span style="display: inline-block;height: 32px;overflow: hidden;vertical-align: middle;width: 136px;">';
					$onNum = $dimension['level'];
					$offNum = count($levelArr)-$onNum;
					for($i=0;$i<$onNum;$i++){
						$dimensionCommentHtml .= '<img src="/static/images/star-on2.jpg">&nbsp;';
						$dimensionCommentHtml_wx .= '<img src="/static/images/star-on2.jpg">&nbsp;';
					}
					for($i=0;$i<$offNum;$i++){
						$dimensionCommentHtml_wx .= '<img src="/static/images/star-off2.jpg">&nbsp;';
					}
					$dimensionCommentHtml .= '</span></li>';
					$dimensionCommentHtml_wx .= '</span></li>';
				}
			}
			$html = str_replace('{dimension_level}',$dimensionCommentHtml,$html);
			$wxHtml = str_replace('{dimension_level}',$dimensionCommentHtml_wx,$wxHtml);


			//课堂评价话术转换
			$sid = $heluInfo['lecture_info']['subject_id'];
			$templateId = $newStudentsModel->getRandTemplateId(array('sid'=>$sid));
			$previewText = D('VpSubject')->get_templatePreview($templateId,$tempLevel);
			$previewText = str_replace('XXX',$heluInfo['sstudentname'],$previewText);
			$html = str_replace('{dimension_comment}',$previewText,$html);
			$wxHtml = str_replace('{dimension_comment}',$previewText,$wxHtml);


			//教师评价
			$html = str_replace('{comment}',$heluInfo['comment'],$html);
			$wxHtml = str_replace('{comment}',$heluInfo['comment'],$wxHtml);


			//错题记录
			$lastWorkErrorHtml = '';
			$moduleAnswerErrorHtml = '';
			$practiseAnswerErrorHtml = '';
			$right_question_arr = array();
			$used_question_arr = array();
			if(!empty($heluInfo['module_answer'])){
				foreach ($heluInfo['module_answer'] as $k=>$m){
					$moduleAnswerErrorHtml .= ($m === '1'||$m === '0')?'<li>'.($k+1).'</li>':'';
					if($m == '2'){
						$right_question_arr[] = $heluInfo['lecture_info']['question_list']['module_question'][$k];
					}
					if($m != -1){
						$used_question_arr[] = $heluInfo['lecture_info']['question_list']['module_question'][$k];
					}
				}
			}
			if(!empty($heluInfo['practise_answer'])){
				foreach ($heluInfo['practise_answer'] as $k=>$p){
					$practiseAnswerErrorHtml .= ($p === '1'||$p === '0')?'<li>'.($k+1).'</li>':'';
					if($p == '2'){
						$right_question_arr[] = $heluInfo['lecture_info']['question_list']['practise'][$k]['id'];
					}
					if($p != -1){
						$used_question_arr[] = $heluInfo['lecture_info']['question_list']['practise'][$k]['id'];
					}
				}
			}
			if(!empty($heluInfo['work_answer'])){
				foreach ($heluInfo['work_answer'] as $k=>$w){
					if($w == '2'){
						$right_question_arr[] = $heluInfo['lecture_info']['question_list']['work'][$k]['id'];
					}
					if($w != -1){
						$used_question_arr[] = $heluInfo['lecture_info']['question_list']['work'][$k]['id'];
					}
				}
			}

			if(!empty($last_lesson_heluInfo['work_answer'])){
				foreach ($last_lesson_heluInfo['work_answer'] as $k=>$w){
					$lastWorkErrorHtml .= ($w === '1'||$w === '0')?'<li>'.($k+1).'</li>':'';
				}
			}
			$html = str_replace('{last_work_error}',$lastWorkErrorHtml,$html);
			$html = str_replace('{module_answer_error}',$moduleAnswerErrorHtml,$html);
			$html = str_replace('{practise_answer}',$practiseAnswerErrorHtml,$html);

			$wxHtml = str_replace('{last_work_error}',$lastWorkErrorHtml,$wxHtml);
			$wxHtml = str_replace('{module_answer_error}',$moduleAnswerErrorHtml,$wxHtml);
			$wxHtml = str_replace('{practise_answer}',$practiseAnswerErrorHtml,$wxHtml);


			//本次课知识点和知识点解析
			$knowledgeListHtml = '';
			$knowledgeTipsHtml = '';
			if(!empty($heluInfo['lecture_info']['config']['struct']['body']['special']['types'])){
				$temp = 1;
				$knowledge_num = 0;
				foreach ($heluInfo['lecture_info']['config']['struct']['body']['special']['types'] as $key=>$knowledge){
					$total_num = 0;
					if(!empty($heluInfo['lecture_info']['question_list']['module'])){
						foreach ($heluInfo['lecture_info']['question_list']['module'] as $kk=>$type){
							if(!empty($type['question_list'])){
								foreach ($type['question_list'] as $k=>$q){
									if($q['knowledge_parent_id'] == $knowledge['id'] && in_array($q['id'],$used_question_arr)){
										$total_num++;
									}
								}
							}

						}
					}
					if(!empty($heluInfo['lecture_info']['question_list']['practise'])){
						foreach ($heluInfo['lecture_info']['question_list']['practise'] as $kk=>$q){
							if($q['knowledge_parent_id'] == $knowledge['id'] && in_array($q['id'],$used_question_arr)){
								$total_num++;
							}
						}
					}

					if(!empty($heluInfo['lecture_info']['question_list']['work'])){
						foreach ($heluInfo['lecture_info']['question_list']['work'] as $kk=>$q){
							if($q['knowledge_parent_id'] == $knowledge['id'] && in_array($q['id'],$used_question_arr)){
								$total_num++;
							}
						}
					}

					if($total_num>0){
						//$knowledgeListHtml .= '<i>'.($temp).'、</i>'.$knowledge['title'].'。';
						$knowledgeListHtml .= '<p class="neirong_lh"><span class="baogao_num"><i class="circle">'.$temp.'</i></span><span class="knowle">'.$knowledge['title'].'</span></p>';
						$knowledgeTipsHtml .= '<div class="font-family: "Microsoft YaHei","Arial Narrow";color: #333;">'.($numberKey[$temp-1]).'、'.$knowledge['title'].'<br>'.$knowledge['tips'].'</div><br><br><br>';
						$temp++;
						$knowledge_num++;
					}

				}
			}
			$html = str_replace('{knowledge_list}',$knowledgeListHtml,$html);
			$html = str_replace('{knowledge_tips_list}',$knowledgeTipsHtml,$html);
			$html = str_replace('{knowledge_num}',$knowledge_num,$html);

			$wxHtml = str_replace('{knowledge_list}',$knowledgeListHtml,$wxHtml);
			$wxHtml = str_replace('{knowledge_tips_list}',$knowledgeTipsHtml,$wxHtml);


			//知识点云图
			$knowledgeCloudHtml = '';
			$knowledgeCloudHtml_wx = '';
			$knowledgeCloudList = $newStudentsModel->getKnowledgeCloud($heluInfo);

			if(!empty($knowledgeCloudList)){

				foreach ($knowledgeCloudList as $key=>$knowledgeCloud){
					$cloud_right_question_arr = array();
					$cloud_used_question_arr = array();
					if(!empty($knowledgeCloud['module_answer'])){
						foreach ($knowledgeCloud['module_answer'] as $k=>$m){
							if($m !==''){
								if($m == 2){
									$cloud_right_question_arr[] = $knowledgeCloud['lecture_info']['question_list']['module_question'][$k];
								}
								if($m != -1){
									$cloud_used_question_arr[] = $knowledgeCloud['lecture_info']['question_list']['module_question'][$k];
								}
							}
						}
					}
					if(!empty($knowledgeCloud['practise_answer'])){
						foreach ($knowledgeCloud['practise_answer'] as $k=>$p){
							if($p !==''){
								if($p == 2){
									$cloud_right_question_arr[] = $knowledgeCloud['lecture_info']['question_list']['practise'][$k]['id'];
								}
								if($p != -1){
									$cloud_used_question_arr[] = $knowledgeCloud['lecture_info']['question_list']['practise'][$k]['id'];
								}
							}
						}
					}
					if(!empty($knowledgeCloud['work_answer'])){
						foreach ($knowledgeCloud['work_answer'] as $k=>$w){
							if($w !==''){
								if($w == 2){
									$cloud_right_question_arr[] = $knowledgeCloud['lecture_info']['question_list']['work'][$k]['id'];
								}
								if($w != -1){
									$cloud_used_question_arr[] = $knowledgeCloud['lecture_info']['question_list']['work'][$k]['id'];
								}
							}
						}
					}

					if(!empty($knowledgeCloud['dtdatereal'])){
						$knowledgeCloudHtml .= '<div class="conmapzsd-line';
						$knowledgeCloudHtml .= ($knowledgeCloud['helu_id'] == $heluInfo['helu_id'])?' conmapzsd-new ':'';
						$knowledgeCloudHtml .= '">
												<div class="fl w100 c-999">'.date('Y-m-d',strtotime($knowledgeCloud['dtdatereal'])).' '.date('H:i',strtotime($knowledgeCloud['dtlessonbeginreal'])).'-'.date('H:i',strtotime($knowledgeCloud['dtlessonendreal'])).'</div>
												<div class="fl  conmapdianimg baogao-icon"></div>
												<div class="fl">
												<ul>';

						$knowledgeCloudHtml_wx .= '<div class="conmapzsd-line';
						$knowledgeCloudHtml_wx .= ($knowledgeCloud['helu_id'] == $heluInfo['helu_id'])?' conmapzsd-now ':'';
						$knowledgeCloudHtml_wx .= '"><div class="fl conmapdianImg baogao-icon"></div><div class="fl conmapzsdList">
												<div class="conmapzsdTime c-999">'.date('Y-m-d',strtotime($knowledgeCloud['dtdatereal'])).' '.date('H:i',strtotime($knowledgeCloud['dtlessonbeginreal'])).'-'.date('H:i',strtotime($knowledgeCloud['dtlessonendreal'])).'</div>
												<ul>';

						if(!empty($knowledgeCloud['lecture_info']['config']['struct']['body']['special']['types'])){
							$temp = 1;
							foreach ($knowledgeCloud['lecture_info']['config']['struct']['body']['special']['types'] as $k=>$knowledge){
								$total_num = 0;
								$right_num = 0;
								if(!empty($knowledgeCloud['lecture_info']['question_list']['module'])){
									foreach ($knowledgeCloud['lecture_info']['question_list']['module'] as $kk=>$type){
										if(!empty($type['question_list'])){
											foreach ($type['question_list'] as $k=>$q){
												if($q['knowledge_parent_id'] == $knowledge['id'] && in_array($q['id'],$cloud_used_question_arr)){
													$total_num++;
													if(in_array($q['id'],$cloud_right_question_arr)){
														$right_num++;
													}
												}
											}
										}

									}
								}
								if(!empty($knowledgeCloud['lecture_info']['question_list']['practise'])){
									foreach ($knowledgeCloud['lecture_info']['question_list']['practise'] as $kk=>$q){
										if($q['knowledge_parent_id'] == $knowledge['id'] && in_array($q['id'],$cloud_used_question_arr)){
											$total_num++;
											if(in_array($q['id'],$cloud_right_question_arr)){
												$right_num++;
											}
										}
									}
								}
								if(!empty($knowledgeCloud['lecture_info']['question_list']['work'])){
									foreach ($knowledgeCloud['lecture_info']['question_list']['work'] as $kk=>$q){
										if($q['knowledge_parent_id'] == $knowledge['id'] && in_array($q['id'],$cloud_used_question_arr)){
											$total_num++;
											if(in_array($q['id'],$cloud_right_question_arr)){
												$right_num++;
											}
										}
									}
								}

								if($total_num>0){
									if($knowledgeCloud['helu_id'] == $heluInfo['helu_id']){

										$knowledgeCloudHtml .= '<li><p class="conmapdian-text">'.($temp).'、'.$knowledge['title'].'</p><p >总题'.$total_num.'道；做对'.$right_num.'道；正确率：'.round(($right_num/$total_num*100),2).'%</p></li>';

										$knowledgeCloudHtml_wx .= '<li><p class="conmapdian-text">'.($temp).'、'.$knowledge['title'].'</p><p c-orange2>总题'.$total_num.'道；做对'.$right_num.'道；正确率：'.round(($right_num/$total_num*100),2).'%</p></li>';
									}else{
										$knowledgeCloudHtml .= '<li>'.($temp).'、'.$knowledge['title'].'</li>';
										$knowledgeCloudHtml_wx .= '<li>'.($temp).'、'.$knowledge['title'].'</li>';
									}
									$temp++;
								}
							}
						}
						$knowledgeCloudHtml .= '		</ul>
			</div>
			<div class="clear"></div>
			</div>';
						$knowledgeCloudHtml_wx .= '		</ul>
			</div>
			<div class="clear"></div>
			</div>';
					}

				}
			}
			$html = str_replace('{knowledge_cloud}',$knowledgeCloudHtml,$html);
			$wxHtml = str_replace('{knowledge_cloud}',$knowledgeCloudHtml_wx,$wxHtml);


			//本次作业
			$work_num = count($heluInfo['lecture_info']['question_list']['work']);
			$workListHtml = '';
			if(!empty($heluInfo['lecture_info']['question_list']['work'])){
				foreach ($heluInfo['lecture_info']['question_list']['work'] as $k=>$q){
					$workListHtml .= '<tr>
										<td>'.($k+1).'</td>
										<td><i class="usertable-star">';
					if($q['difficulty']==1){
						$workListHtml .= '★';
					}else if($q['difficulty']==2){
						$workListHtml .= '★★';
					}else if($q['difficulty']==3){
						$workListHtml .= '★★★';
					}
					$workListHtml .= '</i></td>
										<td class="usertable-tleft">'.$q['knowledge_parent_name'].'</td>
									  </tr>';
				}
			}
			$html = str_replace('{work_num}',$work_num,$html);
			$html = str_replace('{work_list}',$workListHtml,$html);

			$wxHtml = str_replace('{work_num}',$work_num,$wxHtml);
			$wxHtml = str_replace('{work_list}',$workListHtml,$wxHtml);


			//上次作业
			$last_workListHtml = '';
			$last_workListHtml_wx = '';
			if(!empty($last_lesson_heluInfo['lecture_info']['question_list']['work'])){
				foreach ($last_lesson_heluInfo['lecture_info']['question_list']['work'] as $k=>$q){
					if(!empty($last_lesson_heluInfo['work_answer']) && $last_lesson_heluInfo['work_answer'][$k]!==''){
						if($last_lesson_heluInfo['work_answer'][$k]!=-1){
							$last_workListHtml .= '<tr>
											<td>'.($k+1).'</td>
											<td><i class="usertable-star">';
							$last_workListHtml_wx .= '<tr>
											<td>'.($k+1).'</td>
											<td><i class="usertable-star">';
							if($q['difficulty']==1){
								$last_workListHtml .= '★';
								$last_workListHtml_wx .= '★';
							}else if($q['difficulty']==2){
								$last_workListHtml .= '★★';
								$last_workListHtml_wx .= '★★';
							}else if($q['difficulty']==3){
								$last_workListHtml .= '★★★';
								$last_workListHtml_wx .= '★★★';
							}
							$last_workListHtml .= '</i></td>
											<td>'.$q['knowledge_parent_name'].'</td>
											<td><div class="baogao-icon ';
							$last_workListHtml_wx .= '</i></td>
											<td class="usertable-tleft">'.$q['knowledge_parent_name'].'</td>
											<td >';
							if($last_lesson_heluInfo['work_answer'][$k]==0){
								$last_workListHtml .= 'icon-3">做错了';
								$last_workListHtml_wx .= '<img src="/static/images/cuo.png">';
							}else if($last_lesson_heluInfo['work_answer'][$k]==1){
								$last_workListHtml .= 'icon-2">部分正确';
								$last_workListHtml_wx .= '<img src="/static/images/bfdui.png">';
							}else if($last_lesson_heluInfo['work_answer'][$k]==2){
								$last_workListHtml .= 'icon-1">做对了';
								$last_workListHtml_wx .= '<img src="/static/images/dui.png">';
							}
							$last_workListHtml .= '</div></td></tr>';
							$last_workListHtml_wx .= '</td></tr>';
						}
					}
				}
			}
			$html = str_replace('{last_work_list}',$last_workListHtml,$html);
			$wxHtml = str_replace('{last_work_list}',$last_workListHtml_wx,$wxHtml);



			//上次作业知识点云图
			$last_knowledgeCloudHtml = '';
			$last_knowledgeCloudHtml_wx = '';
			$last_knowledgeCloudList = $newStudentsModel->getKnowledgeCloud($last_lesson_heluInfo);
			if(!empty($last_knowledgeCloudList)){
				foreach ($last_knowledgeCloudList as $key=>$knowledgeCloud){
					$last_cloud_used_question_arr = array();
					$last_cloud_right_question_arr = array();
					if(!empty($knowledgeCloud['module_answer'])){
						foreach ($knowledgeCloud['module_answer'] as $k=>$m){
							if($m !==''){
								if($m == 2){
									$last_cloud_right_question_arr[] = $knowledgeCloud['lecture_info']['question_list']['module_question'][$k];
								}
								if($m != -1){
									$last_cloud_used_question_arr[] = $knowledgeCloud['lecture_info']['question_list']['module_question'][$k];
								}
							}
						}
					}
					if(!empty($knowledgeCloud['practise_answer'])){
						foreach ($knowledgeCloud['practise_answer'] as $k=>$p){
							if($p !==''){
								if($p == 2){
									$last_cloud_right_question_arr[] = $knowledgeCloud['lecture_info']['question_list']['practise'][$k]['id'];
								}
								if($p != -1){
									$last_cloud_used_question_arr[] = $knowledgeCloud['lecture_info']['question_list']['practise'][$k]['id'];
								}
							}
						}
					}
					if(!empty($knowledgeCloud['work_answer'])){
						foreach ($knowledgeCloud['work_answer'] as $k=>$w){
							if($w !=''){
								if($w == 2){
									$last_cloud_right_question_arr[] = $knowledgeCloud['lecture_info']['question_list']['work'][$k]['id'];
								}
								if($w != -1){
									$last_cloud_used_question_arr[] = $knowledgeCloud['lecture_info']['question_list']['work'][$k]['id'];
								}
							}

						}
					}


					if(!empty($knowledgeCloud['dtdatereal'])){
						$last_knowledgeCloudHtml .= '<div class="conmapzsd-line';
						$last_knowledgeCloudHtml .= ($knowledgeCloud['helu_id'] == $last_lesson_heluInfo['helu_id'])?' conmapzsd-new ':'';
						$last_knowledgeCloudHtml .= '">
												<div class="fl w100 c-999">'.date('Y-m-d',strtotime($knowledgeCloud['dtdatereal'])).' '.date('H:i',strtotime($knowledgeCloud['dtlessonbeginreal'])).'-'.date('H:i',strtotime($knowledgeCloud['dtlessonendreal'])).'</div>
												<div class="fl  conmapdianimg baogao-icon"></div>
												<div class="fl">
												<ul>';

						$last_knowledgeCloudHtml_wx .= '<div class="conmapzsd-line';
						$last_knowledgeCloudHtml_wx .= ($knowledgeCloud['helu_id'] == $last_lesson_heluInfo['helu_id'])?' conmapzsd-now ':'';
						$last_knowledgeCloudHtml_wx .= '"><div class="fl conmapdianImg baogao-icon"></div><div class="fl conmapzsdList">
												<div class="conmapzsdTime c-999">'.date('Y-m-d',strtotime($knowledgeCloud['dtdatereal'])).' '.date('H:i',strtotime($knowledgeCloud['dtlessonbeginreal'])).'-'.date('H:i',strtotime($knowledgeCloud['dtlessonendreal'])).'</div>
												<ul>';

						if(!empty($knowledgeCloud['lecture_info']['config']['struct']['body']['special']['types'])){
							$temp = 1;
							foreach ($knowledgeCloud['lecture_info']['config']['struct']['body']['special']['types'] as $k=>$knowledge){
								$total_num = 0;
								$right_num = 0;
								if(!empty($knowledgeCloud['lecture_info']['question_list']['module'])){
									foreach ($knowledgeCloud['lecture_info']['question_list']['module'] as $kk=>$type){
										if(!empty($type['question_list'])){
											foreach ($type['question_list'] as $k=>$q){
												if($q['knowledge_parent_id'] == $knowledge['id'] && in_array($q['id'],$last_cloud_used_question_arr)){
													$total_num++;
													if(in_array($q['id'],$last_cloud_right_question_arr)){
														$right_num++;
													}
												}
											}
										}
									}
								}
								if(!empty($knowledgeCloud['lecture_info']['question_list']['practise'])){
									foreach ($knowledgeCloud['lecture_info']['question_list']['practise'] as $kk=>$q){
										if($q['knowledge_parent_id'] == $knowledge['id'] && in_array($q['id'],$last_cloud_used_question_arr)){
											$total_num++;
											if(in_array($q['id'],$last_cloud_right_question_arr)){
												$right_num++;
											}
										}
									}
								}
								if(!empty($knowledgeCloud['lecture_info']['question_list']['work'])){
									foreach ($knowledgeCloud['lecture_info']['question_list']['work'] as $kk=>$q){
										if($q['knowledge_parent_id'] == $knowledge['id'] && in_array($q['id'],$last_cloud_used_question_arr)){
											$total_num++;
											if(in_array($q['id'],$last_cloud_right_question_arr)){
												$right_num++;
											}
										}
									}
								}

								if($total_num>0){
									if($knowledgeCloud['helu_id'] == $last_lesson_heluInfo['helu_id']){
										$last_knowledgeCloudHtml .= '<li><p class="conmapdian-text">'.$temp.'、'.$knowledge['title'].'</p><p>总题'.$total_num.'道；做对'.$right_num.'道；正确率：'.round(($right_num/$total_num*100),2).'%</p></li>';
										$last_knowledgeCloudHtml_wx .= '<li><p class="conmapdian-text">'.$temp.'、'.$knowledge['title'].'</p><p c-orange2>总题'.$total_num.'道；做对'.$right_num.'道；正确率：'.round(($right_num/$total_num*100),2).'%</p></li>';
									}else{
										$last_knowledgeCloudHtml .= '<li>'.$temp.'、'.$knowledge['title'].'</li>';
										$last_knowledgeCloudHtml_wx .= '<li>'.$temp.'、'.$knowledge['title'].'</li>';
									}
									$temp++;
								}

							}
						}

						$last_knowledgeCloudHtml .= '</ul>
													</div>
													<div class="clear"></div>
													</div>';
						$last_knowledgeCloudHtml_wx .= '</ul>
													</div>
													<div class="clear"></div>
													</div>';
					}
				}
			}
			$html = str_replace('{last_knowledge_cloud}',$last_knowledgeCloudHtml,$html);
			$wxHtml = str_replace('{last_knowledge_cloud}',$last_knowledgeCloudHtml_wx,$wxHtml);

			//轨照
			$reportImgHtml = '';
			$reportImgHtml_wx = '';
			$reportImgHtml_wx_title = '';
			if(!empty($heluInfo['lesson_record_img'])){
				$i=1;
				foreach ($heluInfo['lesson_record_img'] as $key=>$img){
					$reportImgHtml .= '<img src="'.str_replace('/Upload/','/upload/',$img).'" >';
					$reportImgHtml_wx .= '<li><a href="'.str_replace('/Upload/','/upload/',$img).'" target="_blank"><img src="'.str_replace('/Upload/','/upload/',$img).'" width="100%"></a></li>';
					if($i ==1 )
					{
						$reportImgHtml_wx_title .= '<li class="on"><a href="javascript:void(0);"></a></li>';
					}else
					{
						$reportImgHtml_wx_title .= '<li ><a href="javascript:void(0);"></a></li>';
					}
					$i++;
				}
			}
			$html = str_replace('{report_img_list}',$reportImgHtml,$html);
			$wxHtml = str_replace('{report_img_list}',$reportImgHtml_wx,$wxHtml);
			$wxHtml = str_replace('{report_img_title}',$reportImgHtml_wx_title,$wxHtml);
			//替换模板数据=======end=================================================================================


			if(!empty($html)){
				$reportFolder = UPLOAD_PATH.'report/';
				if(!file_exists($reportFolder)){
					mkdir($reportFolder,0777);
				}
				//pc端
				$report_file =  $reportFolder.$heluInfo['helu_id'].'.html';
				$file = fopen($report_file, "w+") or die("Unable to open file!");
				fwrite($file, $html);
				fclose($file);
				//移动端
				$report_file_wx =  $reportFolder.$heluInfo['helu_id'].'_wx.html';
				$file = fopen($report_file_wx, "w+") or die("Unable to open file!");
				fwrite($file, $wxHtml);
				fclose($file);

				if(file_exists($report_file)){
					$new_report_file = end(explode('/eap',$report_file));
					$new_report_file_wx = APP_URL.str_replace("/Upload/",'/upload/',end(explode('/eap',$report_file_wx)));
					$type = (!empty($heluInfo['lesson_report_url']))?1:0;
					if($newStudentsModel->recordReportUrl($helu_id,$new_report_file,$new_report_file_wx,$type)){
						$status = 1;
						$report_url = $new_report_file;
					}
				}
			}
		}
		echo json_encode(array('status'=>$status,'report_url'=>str_replace('/Upload/','/upload/',$report_url),'report_url_wx'=>$new_report_file_wx));
	}


	/**
	 * 上传讲义-新版（支持微信页面传图）
	 *
	 */
	public function confirmImg2(){
		$userInfo = $this->checkWeixinInfo();
		$wxImgList = D('WeixinVip')->get_wxImgList(SysUtil::safeString($userInfo['openId']));

		$jsInfoUrl = U('Vip/Weixin/getSignaturePackage');
		$saveImageUrl = U('Vip/Weixin/doSaveImage');
		$currentUrl = $this->getUrl();

		$arr = $_GET;
		$arr['kecheng_name'] = urldecode($_GET['kecheng_name']);
		$arr['timeStr'] = date('Y-m-d',$arr['lesson_date']).' '.date('H:i',$arr['lesson_start']).'~'.date('H:i',$arr['lesson_end']);

		$this->assign(get_defined_vars());
		$this->display();
	}


	/**
	 * 上传测试卷-新版（支持微信页面传图）
	 *
	 */
	public function confirmItembank2(){
		$userInfo = $this->checkWeixinInfo();
		$wxImgList = D('WeixinVip')->get_wxImgList(SysUtil::safeString($userInfo['openId']));

		$jsInfoUrl = U('Vip/Weixin/getSignaturePackage');
		$saveImageUrl = U('Vip/Weixin/doSaveImage');
		$currentUrl = $this->getUrl();

		$this->assign(get_defined_vars());
		$this->display();
	}

	/**
	 * 上传辅导方案-新版（支持微信页面传图）
	 *
	 */
	public function confirmProgram2(){
		$userInfo = $this->checkWeixinInfo();
		$wxImgList = D('WeixinVip')->get_wxImgList(SysUtil::safeString($userInfo['openId']));

		$jsInfoUrl = U('Vip/Weixin/getSignaturePackage');
		$saveImageUrl = U('Vip/Weixin/doSaveImage');
		$currentUrl = $this->getUrl();

		$this->assign(get_defined_vars());
		$this->display();
	}

	/**
	 * 上传轨照-新版（支持微信页面传图）
	 *
	 */
	public function confirmRecordImg2(){
		$userInfo = $this->checkWeixinInfo();
		$wxImgList = D('WeixinVip')->get_wxImgList(SysUtil::safeString($userInfo['openId']));

		$jsInfoUrl = U('Vip/Weixin/getSignaturePackage');
		$saveImageUrl = U('Vip/Weixin/doSaveImage');
		$currentUrl = $this->getUrl();

		$arr = $_GET;
		$arr['kecheng_name'] = urldecode($_GET['kecheng_name']);
		$arr['timeStr'] = date('Y-m-d',$arr['lesson_date']).' '.date('H:i',$arr['lesson_start']).'~'.date('H:i',$arr['lesson_end']);

		$this->assign(get_defined_vars());
		$this->display();
	}

	/**
     * 签名
     *
     * @param string $url
     * @param string $nonce
     * @param int    $timestamp
     *
     * @return array
     */
	public function getSignaturePackage()
	{
		$url       = $_POST['currentUrl'];
		$url = str_replace('&amp;', '&', $url);
		$nonce     = $this->getNonce();
		$timestamp = time();
		$ticket    = $this->getTicket();
		$accessToken = $this->getToken();
		$sign = array(
		'appId'     => C('appID'),
		'nonceStr'  => $nonce,
		'timestamp' => $timestamp,
		'url'       => $url,
		'signature' => $this->getSignature($ticket, $nonce, $timestamp, $url),
		'accessToken' => $accessToken,
		'ticket'=> $ticket,
		'string'=>"jsapi_ticket=$ticket&noncestr=$nonce&timestamp=$timestamp&url=$url"
		);

		echo json_encode($sign);
	}

	/**
     * 生成签名
     *
     * @param string $ticket
     * @param string $nonce
     * @param int    $timestamp
     * @param string $url
     *
     * @return string
     */
	public function getSignature($ticket, $nonce, $timestamp, $url)
	{
		return sha1("jsapi_ticket=$ticket&noncestr=$nonce&timestamp=$timestamp&url=$url");
	}


	/**
     * 获取当前URL
     *
     * @return string
     */
	public function getUrl()
	{
		if ($this->url) {
			return $this->url;
		}

		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] === 443) ? 'https://' : 'http://';

		return $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}


	/**
     * 获取jsticket
     *
     * @return string
     */
	public function getTicket(){
		import('ORG.Util.NCache');
		$cache = NCache::getCache();
		$accessToken = $this->getToken();
		$key = 'overtrue.wechat.jsapi_ticket'.C('appID');
		$ticket =  $cache->get('ticket', $key);
		if(!$ticket){
			$getTicketUrl = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$accessToken."&type=jsapi";
			$result = json_decode(file_get_contents($getTicketUrl),true);
			$ticket = $result['ticket'];
			$cache->set('ticket', $key, $result['ticket']);
		}

		return $ticket;
	}




	/**
     * 获取Token
     *
     * @return string
     */
	public function getToken(){
		import('ORG.Util.NCache');
		$cache = NCache::getCache();
		$key = C('appID');
		$accessToken = $cache->get('access_token', $key);
		if(false == $accessToken){
			$TokenUrl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".C('appID')."&secret=".C('appsecret');
			$TokenData = json_decode(file_get_contents($TokenUrl),true);
			$accessToken = $TokenData['access_token'];
			$cache->set('access_token', $key, $accessToken);
		}

		return $accessToken;
	}


	/**
     * 获取随机字符串
     *
     * @return string
     */
	public function getNonce(){
		return uniqid('rand_');
	}


	public  function downloadImage($imgServerId) {
		$targetFolder = UPLOAD_PATH.date('Y-m-d').'/';
		if(!file_exists($targetFolder)){
			@mkdir($targetFolder, 0777, true);
		}
		$filename = uniqid(mt_rand(), true)."_wx";
		$tmp = substr($pic,-10);//截取后10位，然后找是否有扩展名
		if (strstr($tmp,'.')){
			$extension = end(explode('.', $pic));
			$extension = '.'.$extension;
		}else{
			$extension = '.jpg';
		}
		$imgPath= $targetFolder.$filename.$extension;

		$accessToken = $this->getToken();

		$wxImgUrl = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=' . $accessToken . '&media_id=' . $imgServerId;

		$imgContents = file_get_contents($wxImgUrl);
		file_put_contents($imgPath, $imgContents);
		$imgLocalUrl = $imgPath;
		$userInfo = $this->checkWeixinInfo();
		$return = D('WeixinVip')->add_wxImg($wxImgUrl,$imgLocalUrl,$userInfo['openId'],1);

		return $imgLocalUrl;
	}


	public function doSaveImage() {
		$imgServerId = $_POST['imgServerId'];
		if(false == $imgServerId) {
			return array('errorMsg'=>'非法请求');
		}

		$imgLocalUrl = $this->downloadImage($imgServerId);
		$imgLocalUrl = 'http://' . $_SERVER['HTTP_HOST'] . end(explode('/eap',str_replace('/Upload','/upload',$imgLocalUrl)));
		echo json_encode(array(
		'success'=>true,
		'imgLocalUrl'=>$imgLocalUrl,
		));
	}
	
	
	//PIV4.0使用说明-微信
	public function piv4Instruction(){
		$jsInfoUrl = U('Vip/Weixin/getSignaturePackage');
		$currentUrl = $this->getUrl();
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	
	public function piv4Present(){
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	public function piv4Ans(){
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	public function piv4Class(){
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	public function piv4Tral(){
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	
	public function getReportDemo($type){
		$key = md5($type);
		import('ORG.Util.NCache');
		$cache = NCache::getCache();
		$reportDemo = $cache->get('reportDemo', $key);
		if(false == $reportDemo) {
			$reportDemo = file_get_contents(HTML_PATH.C($type));
			$cache->set('reportDemo', $key, $reportDemo);
		}
		return $reportDemo;
	}

	// 每天的21:30向明天有课的所有老师发放消息
	public function getSendNews() {
		$page = $_GET['page'];
		// 判断当前时间是否为晚上9:30-9:50
		$start = strtotime(date('Y-m-d 21:30:00',time()));
		$end = strtotime(date('Y-m-d 21:50:00',time()));
		$time  = time();
		if ( $time > $start && $time < $end )
		{

			// 获取微信的access_token
			$accessToken = $this->getToken();
			// 微信发送消息API接口链接
			$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$accessToken;
			// 获取明天有课的教师信息
			$baseUrl = 'http://vip.gaosiedu.com';
			$forward = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".C('appID')."&response_type=code&scope=snsapi_base&redirect_uri=";
			$fromUrl = $baseUrl.'/Vip/Weixin/fromUrl';
			$list = D('WeixinVip')->getTeacher($page);
			if ( !empty($list) ) 
			{
				$str = ''; // 循环学生上课时间信息
				foreach ($list as $key => $row)
				{
					if( !empty($row['list']) ){
						foreach ( $row['list'] as $value )
						{
							$str .= $value['sstudentname'].' '.substr($value['dtlessonbegin'],0,5).'-'.substr($value['dtlessonend'],0,5).'; ';
						}		
					}
					// 消息模板信息
					foreach ($row['openid'] as $result) {
						
						if ( $result['openid'] !== 'undefined' )
						{
							$json = '{
					           	"touser":"'.$result['openid'].'",
					           	"template_id":"51m3-Zzn6JkVeHTpt0qdBG90QXJ_aEgJcLhCZdnNCtU",
					           	"url":"'.$forward.urlencode($fromUrl).'&state=newWeekSchedule#wechat_redirect",            
					           	"topcolor":"#FF0000",
								"data":{
									"first": {
										"value":"'.$row['sname'].'老师您好，以下是您明天的课表，请查收：",
										"color":"#173177"
									},
									"keyword1":{
										"value":"'.$str.'",
										"color":"#173177"
									},
									"keyword2":{
										"value":"'.date('Y-m-d',time()+86400).'",
										"color":"#173177"
									},
									"remark":{
										"value":"如与实际上课时间不符，请务必在上课前登陆教师系统调整或联系班主任更正。",
										"color":"#173177"
									}
								}
					       	}';	

					       	$dataRes = json_decode($this->postData($url,$json));
					       	// if ($dataRes->errcode == 0) {
					        //     echo $key.'次：ok<br />';
					        // } 
						}
					}
					$str = '';
				}
			}

		}

	} //

	public function doRealtimeSend(){

		$list = D('WeixinVip')->getStudentNoPunch();
		// 获取微信的access_token
		$accessToken = $this->getToken();
		// 微信发送消息API接口链接
		$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$accessToken;

		if ( !empty($list) ) 
		{
			$str = ''; // 循环学生未打卡信息
			foreach ($list as $key => $row)
			{
				// 消息模板信息
				foreach ($row['openid'] as $result) {
					
					if ( $result['openid'] !== 'undefined')
					{
						$time = date('Y-m-d',time()).' '.substr($row['dtlessonbegin'],0,5).'-'.substr($row['dtlessonend'],0,5);

						$json = '{
				           	"touser":"'.$result['openid'].'",
				           	"template_id":"QkhoMNQntPWk4MG6J_I1uwwE0HYGiLDnxyMeSuF02mc",
				           	"url":"",            
				           	"topcolor":"#FF0000",
							"data":{
								"first": {
									"value":"'.$row['sname'].'老师您好，您本次课程还未打卡，请尽快带学员打指纹。",
									"color":"#173177"
								},
								"keyword1":{
									"value":"'.$row['sstudentname'].'",
									"color":"#173177"
								},
								"keyword2":{
									"value":"'.$time.'",
									"color":"#173177"
								},
								"remark":{
									"value":"请在课后48小时内核录，如果孩子缺勤或有其他变动请尽快与学管师沟通。",
									"color":"#173177"
								}
							}
				       	}';	

				       	$dataRes = json_decode($this->postData($url,$json));
					}
				}
				$str = '';
			}
			
		}
	}

	// 向学管师发送分配资源信息
	public function doEmployeeSend(){
		if(IS_POST){
			$data = json_decode($_POST['sContent']);
			$openidList = D('WeixinVip')->getOpenid($data);
			$count = count($openidList)- 1;
			if( !empty($openidList) ){
				foreach($openidList as $key=>$openid){
					$result = '';
					$param['accessToken'] = $this->getAccessToken();
					$param['openid'] = $openid['open_id']; 
					$param['template_id'] = '5NFV5A66DuD0wr_uagR4s15VuYNRfg3NkHibevortSU';
					$param['url'] = 'http://xgs.gaosivip.com/weixin/admin-student-list?appName='.$openid['app_name'].'&appLevel='.$$openid['app_level'];
					$param['teacher'] = $data->teacher.'您好，您有一条新资源，请及时跟进回访。';
					$param['keyword'] = array(
							1=>$data->sChannel,
							2=>$data->id.'-'.$data->student.'-'.$data->mobile,
							3=>$data->sBak
						);
					$param['ending'] = '具体信息请登录业务系统查看。'; 
					$result = $this->sendTemplate($param);
					if($key == $count){
						echo $this->encode_json($result);
					}
					
				}
			}else{
				$result = array(
	       			'ResultType'=>'-1',
	       			'Message'=>'该学管师未登录公众号'
	       		); 
				echo $this->encode_json($result);
			}
			
		}
	}

	public function getAccessToken(){
		import('ORG.Util.NCache');
		$cache = NCache::getCache();
		$AppID = 'wx56d996da61171488';
		$AppSecret = 'eb527008e15617e954e00f0048786d5d ';
		$accessToken = $cache->get('AccessToken', $AppID);
		if(false == $accessToken){
			$TokenUrl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$AppID."&secret=".$AppSecret;
			$TokenData = json_decode(file_get_contents($TokenUrl),true);
			$accessToken = $TokenData['access_token'];
			$cache->set('AccessToken', $AppID, $accessToken);
		}

		return $accessToken;
	}

	public function sendTemplate($param = array()){
		// 获取微信的access_token
		$accessToken = $param['accessToken'];
		// 微信发送消息API接口链接
		$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$accessToken;

		if ( !empty($param['openid']) )
		{
			$str = '';
			foreach($param['keyword'] as $key=>$keyword){
				$str .= '"keyword'.$key.'":{
									"value":"'.$keyword.'",
									"color":"#173177"
								},';
			}

			$json = '{
	           	"touser":"'.$param['openid'].'",
	           	"template_id":"'.$param['template_id'].'",
	           	"url":"'.$param['url'].'",            
	           	"topcolor":"#FF0000",
				"data":{
					"first": {
						"value":"'.$param['teacher'].'",
						"color":"#173177"
					},
					'.$str.'
					"remark":{
						"value":"'.$param['ending'].'",
						"color":"#173177"
					}
				}
	       	}';	

	       	$dataRes = json_decode($this->postData($url,$json));

	       	if($dataRes->errcode == 0){
	       		$result = array(
	       			'ResultType'=>0,
	       			'Message'=>'发送成功'
	       		);

	       	}else{
	       		$result = array(
	       			'ResultType'=>$dataRes->errcode,
	       			'Message'=>'发送失败'
	       		);   		
	       	}
	       	return $result;
		}

	}

	 // 格式化json中的汉字函数
    protected function encode_json($str) {
    	$url_str = $this->url_encode($str);
    	unset($url_str['AppendData']['PageSize+']);
    	unset($url_str['AppendData']['TotalPageCount+']);
        $strs = urldecode(json_encode($url_str));
        //$strs = ltrim($strs, '[');
        //$strs = rtrim($strs, ']');
        //return urldecode(json_encode($this->url_encode($str)));
        return $strs;
    }
    protected function url_encode($str) {
        if(is_array($str)) {
            foreach($str as $key=>$value) {
            	$str[urlencode($key)] = $this->url_encode($value);	
            }
        } else {
            $str = urlencode($str);
        }
        return $str;
    }



	/**
	 * 我的课时
	 * @return [type] [description]
	 */
	public function myLessonHours()
	{
		$userInfo = $this->checkWeixinInfo();
		$weixinModel = D('WeixinVip');
		if($userInfo['sCode']){
			$key_name = isset($_GET['key_name'])?trim($_GET['key_name']):'';
			$myStudentList = $weixinModel->getStudentLesson(array('teacherCode'=>$userInfo['sCode'],'key_name'=>$key_name));
		}
		$this->assign(get_defined_vars());
		$this->display();

	}



	/**
	 * 上传成绩
	 *
	 */
	public function saveScore(){
		$userInfo = $this->checkWeixinInfo();

		if($userInfo['sCode']){
			$weixinModel = D('WeixinVip');
			$studentList = $weixinModel->getStudentZhongKao($userInfo);
			$subjectList = $weixinModel->getSubjectList($userInfo);
		}
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function doSaveScore(){
		$status = 0;
		if($_POST){
			$weixinModel = D('WeixinVip');
			if($weixinModel->checkIsRecord($_POST)){
				$status = -1;
			}else{
				if($weixinModel->saveScore($_POST)){
					$status = 1;
				}
			}
			
		}
		echo json_encode(array('status'=>$status));
	}
}

?>