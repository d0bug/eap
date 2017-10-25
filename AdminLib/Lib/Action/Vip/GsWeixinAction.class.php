<?php 

/*
	2016-7-27
	dengjun
	高思1对1微信系统
*/

import('COM.WxPayPubHelper.WxPayPubHelper');
class GsWeixinAction extends GsWeixinCommAction{

	public function __construct(){
		// 接口URL
		$this->apiUrl = 'http://vipapi.gaosiedu.com';
		// app的接口URL 	
		$this->appUrl = 'http://www.gaosivip.com';
		// 微信appid
		$this->appid = 'wx2915360cda60ae97';
		// 微信AppSecret
		$this->appsecret = 'd8a3f2d5f19ee73592b991c0b400be2d';
		// 微信退款链接
		$this->refundUrl = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
		// 获取当前的url
		$this->weiUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		// 缓存配置
		$this->conf =  array(
							'cacheType'=>'Memcache', 
							'host'=>'127.0.0.1', 
							'port'=>11211, 
							'expire'=>0
						);
	}

	// 微信系统首页
	public function index(){
		//$this->valid();
		$this->display();
	}

	// 拒绝定位生成cookie
	public function noLocation(){
		cookie('Location','no');
		echo $_COOKIE['Location'];
	}

	// 微信小班课列表
	public function smallClass(){
		$weixin = D('GsWeixin');
		$weekarray=array("日","一","二","三","四","五","六");
		if(empty($_POST['ClassName']) && empty($_GET['DeptName']) && empty($_GET['FitClass']) && empty($_GET['SubjectName'])){
			$latitude = empty($_GET['latitude'])?$_SESSION['latitude']:$_GET['latitude'];
			$longitude = empty($_GET['longitude'])?$_SESSION['longitude']:$_GET['longitude'];
			if($latitude >0 && $longitude >0){
				if($_SESSION['latitude'] > 0 || $_SESSION['longitude'] > 0){
					$_SESSION['latitude'] = $_GET['latitude'];
					$_SESSION['longitude'] = $_GET['longitude'];
					$latitude = $_GET['latitude'];
					$longitude = $_GET['longitude'];
				}
				$schoolList = $weixin->getLatelySchool($latitude,$longitude);
				if(!empty($schoolList)){
					foreach ($schoolList as $school) {
						$canshu = array(
							'[Equal]DeptName'=>$school['title'],
							'[Equal]IsWeChat'=>'true',
							'[GreaterThanOrEqual]BeginOn'=>date('Y/m/d',time()),
							'pageindex'=>1,
							'pagesize'=>100,
							'paging'=>1,
							'partner'=>10010,
							'sort'=>'beginon asc'
						);
						$result = $this->sign($canshu);
				        $latelyUrl = $this->apiUrl.'/api/Class/GetList?'.$result['url'].'&sign='.$result['sign']; 

						$latelyClassList = json_decode($this->http($latelyUrl,'','GET',array()));
						
						if(!empty($latelyClassList->AppendData->PagedList)){
							$school = $school['title'];
							$latelyClassList->AppendData = $latelyClassList->AppendData->PagedList;
							break;
						}
					}
					
					if($latelyClassList->AppendData) {
						import('ORG.Util.NCache');
						$cache = NCache::getCache($this->conf);
						$cookie = cookie('getOpenId');
						$wxid = $cache->get('openid', $cookie);
						$user = $weixin->findBindUserInfo($wxid);
						foreach($latelyClassList->AppendData as $key=>$class){
							$class->week = '周'.$weekarray[date("w",strtotime($class->BeginOn))];
							$latelyClassList->AppendData[$key] = $class;
							$classId = $class->Id;
							$num = abs($class->LimitNum) - abs($class->NowNum);
							
							$orderNum = $weixin->getClassNum($classId);
							$latelyClassList->AppendData[$key]->orderNum = $orderNum; 
							if($orderNum < $num && $num > 0){
								$latelyClassList->AppendData[$key]->is_sign = 0;
							}else{
								$latelyClassList->AppendData[$key]->is_sign = 1;
							}
							if(!empty($user)){
								$orderFind = $weixin->getClassFind($user['user_id'],$classId);

								if($orderFind['order_status'] == 1){
									$latelyClassList->AppendData[$key]->is_order = 1;
								}	
							}
						}
						$latelyClassList->school = $school;
						$this->assign('latelyClassList',$latelyClassList);
					}
					
				}
			}
			if($latelyCount < 20){
				$param = array(
					'[Equal]IsWeChat'=>'true',
					'[GreaterThanOrEqual]BeginOn'=>date('Y/m/d',time()),
					'[NotEqual]DeptName'=>$school,
					'pageindex'=>1,
					'pageSize'=>20,
					'paging'=>1,
					'partner'=>10010,
					'sort'=>'beginon asc'
				);
				$ret = $this->sign($param);
		        $fromUrl = $this->apiUrl.'/api/Class/GetList?'.$ret['url'].'&sign='.$ret['sign']; 

				$classList = json_decode($this->http($fromUrl,'','GET',array()));
				import('ORG.Util.NCache');
				$cache = NCache::getCache($this->conf);
				$cookie = cookie('getOpenId');
				$wxid = $cache->get('openid', $cookie);
				$user = $weixin->findBindUserInfo($wxid);
				foreach($classList->AppendData->PagedList as $key=>$class){
					
					$class->week = '周'.$weekarray[date("w",strtotime($class->BeginOn))];
					$classList->AppendData->PagedList[$key] = $class;
					$classId = $class->Id;
					$num = abs($class->LimitNum) - abs($class->NowNum);
					
					$orderNum = $weixin->getClassNum($classId);
					$classList->AppendData->PagedList[$key]->orderNum = $orderNum; 
					if($orderNum < $num && $num > 0){
						$classList->AppendData->PagedList[$key]->is_sign = 0;
					}else{
						$classList->AppendData->PagedList[$key]->is_sign = 1;
					}
					if(!empty($user)){
						$orderFind = $weixin->getClassFind($user['user_id'],$classId);

						if($orderFind['order_status'] == 1){
							$classList->AppendData->PagedList[$key]->is_order = 1;
						}	
					}
				}
			
			}
			
		}else{
			$param = array(
				'[Contains](p-1-0-0)ClassName'=>$_POST['ClassName'],
				'[Contains](p-1-0-0)TeacherName'=>$_POST['ClassName'],
				'[Contains]DeptName'=>$_GET['DeptName'],
				'[Equal]FitClass'=>$_GET['FitClass'],
				'[Equal]IsWeChat'=>'true',
				'[Equal]XueKe'=>$_GET['SubjectName'],
				'[GreaterThanOrEqual]BeginOn'=>date('Y/m/d',time()),
				'pageindex'=>1,
				'pageSize'=>20,
				'paging'=>1,
				'partner'=>10010,
				'sort'=>'beginon asc'
			);
			$ret = $this->sign($param);
	        $fromUrl = $this->apiUrl.'/api/Class/GetList?'.$ret['url'].'&sign='.$ret['sign']; 

			$classList = json_decode($this->http($fromUrl,'','GET',array()));

			import('ORG.Util.NCache');
			$cache = NCache::getCache($this->conf);
			$cookie = cookie('getOpenId');
			$wxid = $cache->get('openid', $cookie);
			$user = $weixin->findBindUserInfo($wxid);
			foreach($classList->AppendData->PagedList as $key=>$class){
				$class->week = '周'.$weekarray[date("w",strtotime($class->BeginOn))];
				$classList->AppendData->PagedList[$key] = $class;
				$classId = $class->Id;
				$num = abs($class->LimitNum) - abs($class->NowNum);
				
				$orderNum = $weixin->getClassNum($classId);
				$classList->AppendData->PagedList[$key]->orderNum = $orderNum; 
				if($orderNum < $num && $num > 0){
					$classList->AppendData->PagedList[$key]->is_sign = 0;
				}else{
					$classList->AppendData->PagedList[$key]->is_sign = 1;
				}
				if(!empty($user)){
					$orderFind = $weixin->getClassFind($user['user_id'],$classId);

					if($orderFind['order_status'] == 1){
						$classList->AppendData->PagedList[$key]->is_order = 1;
					}	
				}
			}
		}


		if(!empty($classList->AppendData)){
			$weixin = D('GsWeixin');
			$gradeList = $weixin->listDictByCate('Grade', 'id, caption, sort_id', ' is_hidden = "0" AND is_deleted = "0"');
			$subjectList = $weixin->wechatSubject();
			$this->assign('gradeList',$gradeList);
			$this->assign('subjectList',$subjectList);
			$this->assign('classList',$classList->AppendData);
			$this->assign('latitude',$latitude);
			$this->assign('longitude',$longitude);
			$str = "23456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVW";
			$code = '';
			for ($i=0; $i<16; $i++){
					$code.= $str[mt_rand(0, strlen($str)-1)];
			}
			$lang = array(
						'appid'=>$this->appid,
						'jsapi_ticket'=>$this->getJsApiSign($this->getAccessToken()),
						'nonceStr'=>$code,
						'url'=>'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']
					);
			$this->assign('lang',$lang);
		}

		$this->display();
	}

	// 获取小班课的分页信息

	public function ajaxClass(){
		$high = $_POST['high'];
		$tops = $_POST['tops'];
		$tops = intval($tops);
		$count = $_GET['TotalPageCount'];
		if(($tops-$high) >= 0){
			$page = ceil($tops/$high)+1;
			if( $page > 0 && $page <= $count)
			{
				if(empty($_GET['ClassName']) && empty($_GET['DeptName']) && empty($_GET['FitClass']) && empty($_GET['SubjectName'])){
					if(!empty($_GET['latitude']) && !empty($_GET['longitude'])){
						$param = array(
							'[Equal]IsWeChat'=>'true',
							'[GreaterThanOrEqual]BeginOn'=>date('Y/m/d',time()),
							'[NotEqual]DeptName'=>$_GET['school'],
							'pageindex'=>$page,
							'pagesize'=>20,
							'paging'=>1,
							'partner'=>10010,
							'sort'=>'beginon asc'
						);
					}
				}else{
					$param = array(
						'[Contains](p-1-0-0)ClassName'=>$_GET['ClassName'],
						'[Contains](p-1-0-0)TeacherName'=>$_GET['ClassName'],
						'[Contains]DeptName'=>$_GET['DeptName'],
						'[Equal]FitClass'=>$_GET['FitClass'],
						'[Equal]IsWeChat'=>'true',
						'[Equal]XueKe'=>$_GET['SubjectName'],
						'[GreaterThanOrEqual]BeginOn'=>date('Y/m/d',time()),
						'pageindex'=>$page,
						'pagesize'=>20,
						'paging'=>1,
						'partner'=>10010,
						'sort'=>'beginon asc'
					);
				}
				
			}
		
			$ret = $this->sign($param);
        	$fromUrl = $this->apiUrl.'/api/Class/GetList?'.$ret['url'].'&sign='.$ret['sign']; 
			$classList = json_decode($this->http($fromUrl,'','GET',array()));
			import('ORG.Util.NCache');
			$cache = NCache::getCache($this->conf);
			$cookie = cookie('getOpenId');
			$wxid = $cache->get('openid', $cookie);
			$weixin = D('GsWeixin');
			$user = $weixin->findBindUserInfo($wxid);
			
			foreach($classList->AppendData->PagedList as $key=>$class){
				$classList->AppendData->PagedList[$key] = $class;
				$classId = $class->Id;
				$num = abs($class->LimitNum) - abs($class->NowNum);
				
				$orderNum = $weixin->getClassNum($classId);
				$classList->AppendData->PagedList[$key]->orderNum = $orderNum; 
				if($orderNum < $num && $num > 0){
					$classList->AppendData->PagedList[$key]->is_sign = 0;
				}else{
					$classList->AppendData->PagedList[$key]->is_sign = 1;
				}
				if(!empty($user)){
					$orderFind = $weixin->getClassFind($user['user_id'],$classId);
				
					if($orderFind['order_status'] == 1){
						$classList->AppendData->PagedList[$key]->is_order = 1;
					}		
				}
			}
			if(!empty($classList->AppendData))
			{
				$this->assign('classList',$classList->AppendData);
			}
		}
		$this->display();
	}

	// 获取小班课的信息详情
	public function classDetails(){
		$classId = abs($_GET['id']);
		if($classId > 0){
			$param = array(
				'id'=>$classId,
				'partner'=>10010
			);
			$ret = $this->sign($param);
			//$this->apiUrl = 'http://172.16.5.149:81/';
			$fromUrl = $this->apiUrl.'/api/Class/GetDetail?'.$ret['url'].'&sign='.$ret['sign'];

			$details = json_decode($this->http($fromUrl,'','GET',array()));
			// echo '<pre>';print_r($details);die();
			if(!empty($details->AppendData)){

				$teacherName = $details->AppendData->TeacherName;
				if(!empty($teacherName)){

					$url = $this->appUrl.'/So/Kecheng-getTeacherInfo-teacher-'.$teacherName;

					$teacherInfo = json_decode($this->http($url,'','GET',array()));
					$thumb = substr($teacherInfo->AppendData->thumb,0,4);
					
					if($thumb !== 'http'){

						$teacherInfo->AppendData->thumb = 'http://www.gaosivip.com'.$teacherInfo->AppendData->thumb;
					}

					$this->assign('teacherInfo',$teacherInfo->AppendData);
				}

				$classname = mb_substr($details->AppendData->ClassName, 0,5);
				$parameter = array(
					'[Contains]classname'=>$classname,
					'[Equal]DeptName'=>$details->AppendData->DeptName,
					'[Equal]FitClass'=>$details->AppendData->FitClass,
					'[GreaterThanOrEqual]BeginOn'=>date('Y/m/d',time()),
					'pageindex'=>1,
					'pagesize'=>20,
					'paging'=>1,
					'partner'=>10010,
					'sort'=>'beginon desc'
				);

				$classMd5 = $this->sign($parameter);

				$classUrl = $this->apiUrl.'/api/Class/GetList?'.$classMd5['url'].'&sign='.$classMd5['sign']; 
				
				$relevant = json_decode($this->http($classUrl,'','GET',array()));
				import('ORG.Util.NCache');
				$cache = NCache::getCache($this->conf);
				$cookie = cookie('getOpenId');
				$wxid = $cache->get('openid', $cookie);
				$weixin = D('GsWeixin');
				$classId = $details->AppendData->Id;
				$num = abs($details->AppendData->LimitNum) - abs($details->AppendData->NowNum);
				$orderNum = $weixin->getClassNum($classId);

				$details->AppendData->orderNum = $orderNum;

				if($orderNum < $num && $num > 0){
					$details->AppendData->is_sign = 0;
				}else{
					$details->AppendData->is_sign = 1;
				}
				if(!empty($wxid)){

					$user = $weixin->findBindUserInfo($wxid);
					if(!empty($user)){

						$params['user_id'] = $user['user_id'];
						$params['info_id'] = $classId;
						$params['type'] = 1;
						$result = $weixin->getCollectFind($params);

						if($result === false){
							$details->AppendData->is_store = 1;
						}else{
							$details->AppendData->is_store = 0;
						}

						$orderFind = $weixin->getClassFind($user['user_id'],$classId);

						if($orderFind['order_status'] == 1){
							$details->AppendData->is_order = 1;
						}
						foreach($relevant->AppendData->PagedList as $key=>$class){
							$classId = $class->Id;
							$num = abs($class->LimitNum) - abs($class->NowNum);
							
							$orderNum = $weixin->getClassNum($classId);
							
							$orderFind = $weixin->getClassFind($user['user_id'],$classId);

							if($orderNum < $num && $num > 0 && $orderFind['order_status'] == 0){
								if($classId != $_GET['id']){
									$relevantClass[$classId] = $class;	
								}
							}
							
						}
						$count = count($relevantClass);
						if($count < 6){
							$parameter = array(
								'[Equal]DeptName'=>$details->AppendData->DeptName,
								'[Equal]FitClass'=>$details->AppendData->FitClass,
								'[GreaterThanOrEqual]BeginOn'=>date('Y/m/d',time()),
								'pageindex'=>1,
								'pagesize'=>20,
								'paging'=>1,
								'partner'=>10010,
								'sort'=>'beginon desc'
							);

							$classMd5 = $this->sign($parameter);

							$classUrl = $this->apiUrl.'/api/Class/GetList?'.$classMd5['url'].'&sign='.$classMd5['sign']; 
							
							$relevant = json_decode($this->http($classUrl,'','GET',array()));
							foreach($relevant->AppendData->PagedList as $key=>$class){
								$classId = $class->Id;
								$num = abs($class->LimitNum) - abs($class->NowNum);
								
								$orderNum = $weixin->getClassNum($classId);
								
								$orderFind = $weixin->getClassFind($user['user_id'],$classId);

								if($orderNum < $num && $num > 0 && $orderFind['order_status'] == 0){
									if($classId != $_GET['id']){	
										$relevantClass[$classId] = $class;
									}
								}
								
							}
						}
						$relevantClass = array_slice($relevantClass,0,6);
						$this->assign('relevantClass',$relevantClass);
					}
				}else{
					
					foreach($relevant->AppendData->PagedList as $class){
						$classId = $class->Id;
						$num = abs($class->LimitNum) - abs($class->NowNum);
						
						$orderNum = $weixin->getClassNum($classId);

						if($orderNum < $num && $num > 0 ){
							if($class->Id != $_GET['id']){
								$classLists[$class->Id] = $class;
							}
						}	
						
					}
					$count = count($classLists);

					if($count < 6){
						$parameter = array(
							'[Equal]DeptName'=>$details->AppendData->DeptName,
							'[Equal]FitClass'=>$details->AppendData->FitClass,
							'[GreaterThanOrEqual]BeginOn'=>date('Y/m/d',time()),
							'pageindex'=>1,
							'pagesize'=>20,
							'paging'=>1,
							'partner'=>10010,
							'sort'=>'beginon desc'
						);

						$classMd5 = $this->sign($parameter);

						$classUrl = $this->apiUrl.'/api/Class/GetList?'.$classMd5['url'].'&sign='.$classMd5['sign']; 
						
						$relevant = json_decode($this->http($classUrl,'','GET',array()));

						foreach($relevant->AppendData->PagedList as $class){
							$classId = $class->Id;
							$num = abs($class->LimitNum) - abs($class->NowNum);
							
							$orderNum = $weixin->getClassNum($classId);
							if($orderNum < $num && $num > 0){
								if($class->Id != $_GET['id']){
									$classLists[$class->Id] = $class;
								}
							}	
						}
						
					}
					$classLists = array_slice($classLists,0,6);
					$this->assign('relevantClass',$classLists);
				}
			
				$this->assign('details',$details->AppendData);

			}
		}else{
			$this->error('课程参数错误，请重新选择',U('Vip/GsWeixin/smallClass'));
		}
		
		$this->display();
	}

	// 课程报名的信息
	public function classEnroll(){
		$user = $this->checkWeixinInfo();
		$classId = abs($_GET['id']);
		if($classId > 0){
			// 获取小班课的信息
			$param = array(
				'id'=>$classId,
				'partner'=>10010
			);
			$ret = $this->sign($param);

			$fromUrl = $this->apiUrl.'/api/Class/GetDetail?'.$ret['url'].'&sign='.$ret['sign'];

			$details = json_decode($this->http($fromUrl,'','GET',array()));

			$classData = $details->AppendData;

			$weixin = D('GsWeixin');
			// 查看该班课剩余的人数
			$num = abs($classData->LimitNum) - abs($classData->NowNum);
			
			$orderNum = $weixin->getClassNum($classId);

			if($orderNum < $num && $num > 0){

				$orderFind = $weixin->getClassFind($user['user_id'],$classData->Id);

				if(!empty($classData) && empty($orderFind)){
					$order['openid'] = $user['openid'];
					$order['user_id'] = $user['user_id'];
					$order['class_id'] = $classData->Id;
					$order['order_sn'] = $this->order_sn();
					$order['class_name'] = $classData->ClassName;
					$order['teacher_name'] = empty($classData->TeacherName)?"未知":$classData->TeacherName;
					$order['begin_on'] = $classData->BeginOn;
					$order['dept_name'] = $classData->DeptName;
					$order['subject_name'] = $classData->SubjectName;
					$order['order_price'] = $classData->RealPrice;
					$order['order_time'] = time();
					$result = $weixin->getAddOrder($order);
					
					if($result){
						$this->assign('orderFind',$result);
						$this->assign('class',$classData);
					}else{
						$this->error('报名失败，请重新报名',U('/Vip/GsWeixin/classDetails/id/'.$classId));	
					}
				}else{
					if($orderFind['order_status'] == 1){
						echo '<script>alert("该班程已报名,请选择其他班课~");
							location.href = "/vip/gs_weixin/smallClass"</script>';
						exit;
					}else{
						$this->assign('orderFind',$orderFind);
					}
				}
				$this->assign('user',$user);
				$this->assign('class',$classData);
			}else{
				echo '<script>alert("该班程报名人数已满,请选择其他班课~");
					location.href = "/vip/gs_weixin/classDetails/id/'.$classId.'"</script>';
				exit;
			}
			
		}
		$this->display();
	}

	// 获取小班课的订单信息
	public function classOrder(){
		$user = $this->checkWeixinInfo();
		$weixin = D('GsWeixin');
		$uid = $user['user_id'];
		$orderList = $weixin->getClassOrder($uid,$_GET);
		$this->assign('user',$user);
		$this->assign('orderList',$orderList);
		$this->display();
	}

	// 删除用户订单
	public function delOrder(){
		$order_id = $_POST['order_id'];
		if($order_id > 0){
			$weixin = D('GsWeixin');
			$result = $weixin->getDelOrder($order_id);
			if($result){
				echo 1;
				exit;
			}else{
				echo 0;
				exit;
			}
		}
	}

	// 获取用户订单分页
	public function ajaxOrder(){
		$high = $_POST['high'];
		$tops = $_POST['tops'];
		$tops = intval($tops);
		if(($tops-$high) >= 0){
			$page = ceil($tops/$high)+1;
			$_GET['page'] = $page;
			$uid = $_GET['user_id'];
			$weixin = D('GsWeixin');
			$orderList = $weixin->getClassOrder($uid,$_GET);
			$this->assign('orderList',$orderList);
		}
		$this->display();	
	}

	// 订单微信支付
	public function wechatPay(){
		$this->checkWeixinInfo();
		$id = $_GET['id'];
		if($id > 0){
			$weixin = D('GsWeixin');
			$orderInfo = $weixin->getOrderInfo($id);
		}
		$this->assign('orderInfo',$orderInfo);
		$this->display();
	}

	// 验证订单信息进行支付操作
	public function pay(){
		//使用jsapi接口
	    $jsApi = new \JsApi_pub();

	    // 判断用户是否登录
	   	$user = $this->checkWeixinInfo();

	   	$openid = $user['openid'];

	    //获取订单号
	    $order_id = $_GET['order_id'];	

	    $weixin = D('GsWeixin');
	     
	    $order_info = $weixin->getOrderInfo($order_id);

	    if(empty($order_info)) {
	        $this->ajaxReturn(array('error_msg' => '该订单不存在'));
	    }
	    
	    if(1 == $order_info['order_status']) {
	        $this->ajaxReturn(array('error_msg' => '该订单已付款，请不要重复付款'));
	    }

	    if(2 == $order_info['order_status']) {
	        $this->ajaxReturn(array('error_msg' => '该订单退款，请不要重复付款'));
	    }
	    
	    
	    //使用统一支付接口，获取prepay_id
	    $unifiedOrder = new \UnifiedOrder_pub();
	    //设置统一支付接口参数
	    
	    //设置必填参数
	    $total_fee = $order_info['order_price']*100;
	    
	    $body = $order_info['class_name'];
	    $unifiedOrder->setParameter("openid", "$openid");//用户标识
	    $unifiedOrder->setParameter("body", "$body");//商品描述
	    //自定义订单号，此处仅作举例
	    $out_trade_no = $order_info['order_sn'].time();
	    $unifiedOrder->setParameter("out_trade_no", "$out_trade_no");//商户订单号
	    $unifiedOrder->setParameter("total_fee", "$total_fee");//总金额
	    $unifiedOrder->setParameter("notify_url", \WxPayConf_pub::NOTIFY_URL);//通知地址
	    $unifiedOrder->setParameter("trade_type", "JSAPI");//交易类型
	    
	    
	    $prepay_id = $unifiedOrder->getPrepayId();
	    //通过prepay_id获取调起微信支付所需的参数
	    $jsApi->setPrepayId($prepay_id);
	    $jsApiParameters = $jsApi->getParameters();
	    
	    $wxconf = json_decode($jsApiParameters, true);

	    if ($wxconf['package'] == 'prepay_id=') {
	        $this->ajaxReturn(array('error_msg' => '当前订单存在异常，不能使用支付'));
	    }
	    if(IS_AJAX){
	        $this->ajaxReturn(array(
	            'status' => 'ok',
	            'wxconf' => $wxconf,
	        ));
	    }
	    
	    $this->display('wechatPay');	
	}

	public function notify(){
        $data = $_POST;
        //支付成功后的逻辑操作
        
        //订单号
        $order_id         = $data['out_trade_no'];
        //微信交易号
        $transaction_id   = $data['transaction_id'];
        
        //openid
        $openid   = $data['openid'];
        
        //交易金额，单位分
        $price            = $data['pay_price'];
        
        //交易完成时间
        $finish_time      = $data['time_end'];
        $finish_time      = date('Y-m-d H:i:s', strtotime($finish_time));
        $data['time_end'] = strtotime($finish_time);
        //验证订单合法性
        if(!$order_id) {
            //mail_helper::mail('245629560@qq.com', '付款通知异常(weixin_h5)', 'order_id is empty'.$order_id);
            exit('缺少必要参数');
        }
        
        $weixin = D('GsWeixin');

        $order_info = $weixin->getOrderInfo($order_id);
        if(empty($order_info)) {
            //mail_helper::mail('245629560@qq.com', '付款通知异常(weixin_h5)', 'order_info is empty'.$order_id);
            exit('该订单不存在');
        }
        
        // 订单已取消
        if($order_info['order_status'] == 2) {
            //mail_helper::mail('245629560@qq.com', '付款通知异常(weixin_h5)', '订单已取消，不能再支付'.$order_id);
            exit('该订单已退款');
        }
        
        // 已经支付，无需再次支付
        if($order_info['order_status'] == 1) {
            //mail_helper::mail('245629560@qq.com', '付款通知异常(weixin_h5)', '订单'.$order_id.'已经支付，无需再次支付');
            exit('该订单已经支付');
        }
  
        $modify = $weixin->updateOrder($data);
        
        if($modify){
			$userInfo = $weixin->findBindUserInfo($openid);
        	$temp['openid'] = $openid;
			$temp['mould'] = '0wtMFBUSBHAOSQKKlODzYK_6ZeG4c8CA587gfGNFaoE';
			$temp['url'] = 'http://vip.gaosiedu.com/vip/gs_weixin/classOrder/status/1';
			$temp['content'] = '亲爱的同学您好，您的课程报名成功啦！';
			$temp['keyword'] = array(
									1=>$order_info['class_name'],
									2=>$order_info['teacher_name'],
									3=>date('Y-m-d H:i:s',$data['time_end']),
									4=>$order_info['dept_name']
								);
			$temp['ending'] = '我们将尽快跟您取得联系，祝您在高思学习愉快！';
			$isOk = $this->getSendNews($temp);
		
			$temp['openid'] = 'o7tKUw4G7rDbSJhvIMWOk6fYziY0';
			$temp['mould'] = 'KFz0V5NIGqJqHT0nYKWjDVnmyYgQKAsYHcnReWzC9yw';
			$temp['url'] = '';
			$temp['content'] = '老师您好，小班课有学员报名啦！';
			$temp['keyword'] = array(
									1=>$order_info['pay_number'],
									2=>$order_info['class_name'],
									3=>$order_info['order_price'],
									4=>$userInfo['user_mobile'],
									5=>date('Y-m-d H:i:s',$data['time_end'])
								);
			$temp['ending'] = '上课校区为：'.$order_info['dept_name'].'，请注意查看！';
			$this->getSendNews($temp);			
			
        	return true;
        }else{
        	return false;
        }
	           
	}

	// 退款操作
	public function weChatRefundApi() {
		header("Content-Type:text/html; charset=utf-8");
    	$accid = $_GET['accid'];
	    $secret = $_GET['secret'];
	    if($accid != '2915360' && $secret != $this->appsecret) {
			$data= array(
	            'ResultType' => 0,
	            'Message' => '账户与秘钥不正确',
	        );
	    	echo $this->encode_json($data);
			exit;
		}
		$pay_number = empty($_POST['pay_number'])?'':$_POST['pay_number'];
		$refund_money = empty($_POST['refund_money'])?'':$_POST['refund_money'];
		$isTradingComplete = abs($_POST['isTradingComplete']);

		$weixin = D('GsWeixin');
		if(empty($pay_number) && empty($refund_money)) {
			$data= array(
	            'ResultType' => 0,
	            'Message' => '微信订单号和金额不能为空!'
	        );
		} else {
			$orderInfo = $weixin->payOrderInfo($pay_number);
			$transaction_id = $pay_number;
			$order_sn = $orderInfo['order_sn'];
			$refund_fee = $refund_money*100;

			//商户退款单号，商户自定义，
			$out_refund_no = $this->order_sn();
			//总金额需与订单号out_trade_no对应，demo中的所有订单的总金额为1分
			$total_fee = $orderInfo['pay_price']*100;
		
			//使用退款接口
			$refund = new \Refund_pub();
			//设置必填参数
			//appid已填,商户无需重复填写
			//mch_id已填,商户无需重复填写
			//noncestr已填,商户无需重复填写
			//sign已填,商户无需重复填写
			$refund->setParameter("transaction_id","$transaction_id");//商户订单号
			$refund->setParameter("out_refund_no","$out_refund_no");//商户退款单号
			$refund->setParameter("total_fee","$total_fee");//总金额
			$refund->setParameter("refund_fee","$refund_fee");//退款金额
			$refund->setParameter("op_user_id",WxPayConf_pub::MCHID);//操作员
			$refund->setParameter("refund_account","REFUND_SOURCE_RECHARGE_FUNDS");//退款资金来源	
			
			//调用结果
			$refundResult = $refund->getResult();
			//echo '<pre>';print_r($refundResult);die();
			//商户根据实际情况设置相应的处理流程,此处仅作举例
			if ($refundResult["return_code"] == "SUCCESS") {
				if($refundResult["err_code"] == ''){
					$canRefundMoney = $orderInfo['can_refund_money']-$refund_money;
					if($isTradingComplete > 0) {
						$weixin->updateOrderRefund($order_sn,2,$canRefundMoney);
					}else{
						$weixin->updateOrderRefund($order_sn,1,$canRefundMoney);
					}
					$data= array(
						'ResultType' => 1,
						'Message' => '退款已完成'
					);
					$temp['openid'] = $orderInfo['opentid'];
					$temp['mould'] = 'z9xdGqiNQ48HEkAOqS7Pkl7UFBfZ-q-69DbxPzAln_M';
					$temp['url'] = '';
					$temp['content'] = '亲爱的学员您好，您的课程退款确认';
					$temp['keyword'] = array(
											1=>$pay_number,
											2=>$total_fee,
											3=>$refund_fee
										);
					$temp['ending'] = '预计到账时间为0-7个工作日，请注意查收！';
					$this->getSendNews($temp);
				} else {
					$data= array(
						'ResultType' => 0,
						'Message' => '返回结果：'.$refundResult['err_code_des']
					);
				}
				

			}
			else{
				$data= array(
					'ResultType' => 0,
					'Message' => '返回结果：'.$refundResult['return_msg']
				);
			}
			
		}
		
		echo $this->encode_json($data);

	}

	// 生成随机订单号
	public function order_sn(){

		return 'wx'.date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 6);

	}

	// 获取.net那边的签名进行md5加密
	public function sign($parameter = array()){
		$str = '';
		if(!empty($parameter)){
			foreach ($parameter as  $key=>$value) {
				if(!empty($value)){
					$str .= $key.'='.$value.'&';
				}
			}
		}
		$result['url'] = trim(str_replace(' ','%20',$str),'&');
		$result['sign'] = md5(trim(strtolower($str),'&').'gaosiedu');
		return $result;
	}

	// 微信1对1课程列表
	public function oneToOne()
	{
		// api的参数设置
		$url ='';
		if( !empty($_GET['grade']) )
		{
			$url .='-grade-'.$_GET['grade'];
		}
		if( !empty($_GET['subject']) )
		{
			$url .='-subject-'.$_GET['subject'];
		}
		$fromUrl = $this->appUrl.'/So/Kecheng-index'.$url;
		
		$kcList = json_decode($this->http($fromUrl,'','GET',array()));
		if($kcList->Message == '成功')
		{
			$this->assign('kcList',$kcList);
		}

		$this->display();
	}

	// ajax获取1对1课程分页数据
	public function ajaxCourse(){
		$high = $_POST['high'];
		$tops = $_POST['tops'];
		$tops = intval($tops);
		if(($tops-$high) >= 0){
			$page = ceil($tops/$high)+1;
			if( !empty($_GET['grade']) )
			{
				$url .='-grade-'.$_GET['grade'];
			}
			if( !empty($_GET['subject']) )
			{
				$url .='-subject-'.$_GET['subject'];
			}
			if( $page >0)
			{
				$url .='-page-'.$page;
			}
			$fromUrl = $this->appUrl.'/So/Kecheng-index'.$url;

			$kcList = json_decode($this->http($fromUrl,'','GET',array()));
			if($kcList->Message == '成功')
			{
				$this->assign('kcList',$kcList);
			}
		}
		$this->display();
	}
	
	// 查看预约的课程详情
	public function courseDetail(){
		$id = empty($_GET['id'])?0:trim($_GET['id']);
		if( !empty($id) ){	

			$fromUrl = $this->appUrl.'/So/Kecheng-wxKecheng-uid-'.$id;
			
			$detail = json_decode($this->http($fromUrl,'','GET',array()));			
			
			import('ORG.Util.NCache');
			$cache = NCache::getCache($this->conf);
			$cookie = cookie('getOpenId');
			$wxid = $cache->get('openid', $cookie);
			if(!empty($wxid)){
				$weixin = D('GsWeixin');
				$user = $weixin->findBindUserInfo($wxid);
				if(!empty($user)){

					$params['user_id'] = $user['user_id'];
					$params['info_id'] = $detail->AppendData->uid;
					$params['type'] = 2;
					$result = $weixin->getCollectFind($params);

					if($result === false){
						$detail->AppendData->is_store = 1;
					}else{
						$detail->AppendData->is_store = 0;
					}
				}
			}
			if($detail->Message == '成功'){
				$releUrl = $this->appUrl.'/So/Kecheng-index-grade-'.$detail->AppendData->grade_id;
				$relevant = json_decode($this->http($releUrl,'','GET',array()));
				$this->assign('relevant',$relevant->AppendData);
				$this->assign('detail',$detail->AppendData);
			}

		}
		$this->display();
	}

	// 1对1课程报名信息
	public function courseMake(){
		$user = $this->checkWeixinInfo();
		if( !empty($_POST) ){
			$_POST['userid'] = $user['user_id'];
			$makeUrl = $this->appUrl.'/So/Kecheng-kechengBaomingTo';
			$_POST['name'] = '(微信)'.$_POST['name'];
			$json = $this->http($makeUrl,$_POST,'POST',array());
			if($json){
				$temp['openid'] = $user['openid'];
				$temp['mould'] = 'vcOLzk20yNbwVd2ONGRzk2lKdFoc3k40eJjIHHulsQg';
				$temp['url'] = 'http://vip.gaosiedu.com/vip/gs_weixin/courseSign';
				$temp['content'] = '亲爱的同学您好，您的课程报名成功！';
				$temp['keyword'] = array(
										1=>$_POST['kctitle'],
										2=>date('Y-m-d',time())
									);
				$temp['ending'] = '我们将尽快跟您取得联系，祝您在高思学习愉快！';
				$this->getSendNews($temp);
				echo json_encode(array('ResultType'=>1,'Message'=>'预约成功'));
			}else{
				echo json_encode(array('ResultType'=>0,'Message'=>'预约失败'));
			}
			exit;
		}
		$id = trim($_GET['id']);
		$fromUrl = $this->appUrl.'/So/Kecheng-wxKecheng-uid-'.$id;
		$details = json_decode($this->http($fromUrl,'','GET',array()));
		$this->assign('kctitle',$details->AppendData->title);
		$this->display();		
	}

	// 获取用户报名的课程列表
	public  function courseSign(){
		$user = $this->checkWeixinInfo();
		$fromUrl = $this->appUrl.'/So/Kecheng-getkechengSign-uid-'.$user['user_id'];
		$signList = json_decode($this->http($fromUrl,'','GET',array()));
		if($signList->Message == '成功'){
			$this->assign('signList',$signList->AppendData);
		}
		$this->display();
	}

	// 活动列表信息
	public function doActivity(){
		$fromUrl = $this->appUrl.'/app/index.php/Home/Index/huodongList';
		$activity = json_decode($this->http($fromUrl,'','GET',array()));
		if($activity->Message == '成功'){
			$this->assign('activity',$activity->AppendData);
		}
		$this->display();
	}

	// 活动详情信息
	public function activityList(){
		$id = abs($_GET['id']);
		if( !empty($id) ){
			$fromUrl = $this->appUrl.'/app/index.php/Home/Index/huodongShowTo/id/'.$id;
			$details = json_decode($this->http($fromUrl,'','GET',array()));
			if($details->Message == '成功'){
				$url = $this->appUrl.'/app/index.php/Home/Index/huodongList/keyword/'.substr($details->AppendData->title,0,14);
				$activity = json_decode($this->http($url,'','GET',array()));
				$this->assign('activity',$activity->AppendData);
				$this->assign('details',$details->AppendData);
			}
		}
		$this->display();
	}

	// 活动报名提交
	public function activitySign(){
		$user = $this->checkWeixinInfo();
		if(IS_POST){
			$_POST['userid'] = $user['user_id'];
			$_POST['name'] = '(微信)'.$_POST['name'];
			$signUrl = $this->appUrl.'/app/index.php/Home/Index/wxHuodongBaoming';
			$json = $this->http($signUrl,$_POST,'POST',array());
			if($json){
				$temp['openid'] = $user['openid'];
				$temp['mould'] = 'vcOLzk20yNbwVd2ONGRzk2lKdFoc3k40eJjIHHulsQg';
				$temp['url'] = 'http://vip.gaosiedu.com/vip/gs_weixin/hdSignList';
				$temp['content'] = '亲爱的同学您好，您的课程报名成功！';
				$temp['keyword'] = array(
										1=>$_POST['hdtitle'],
										2=>date('Y-m-d',time())
									);
				$temp['ending'] = '我们将尽快跟您取得联系，祝您在高思学习愉快！';
				$this->getSendNews($temp);
				echo json_encode(array('ResultType'=>1,'Message'=>'报名成功'));
			}else{
				echo json_encode(array('ResultType'=>0,'Message'=>'报名失败'));
			}
			exit;
		}
		$id = abs($_GET['id']);
		$fromUrl = $this->appUrl.'/app/index.php/Home/Index/huodongShowTo/id/'.$id;
		$details = json_decode($this->http($fromUrl,'','GET',array()));
		$campus = explode(',', trim($details->AppendData->xiaoqu,','));
		$this->assign('hdtitle',$details->AppendData);
		$this->assign('campus',$campus);
		$this->display();
	}

	// 获取用户报名的活动列表
	public  function hdSignList(){
		$user = $this->checkWeixinInfo();
		$fromUrl = $this->appUrl.'/app/index.php/Home/Index/getActivitySign/uid/'.$user['user_id'];
		$signList = json_decode($this->http($fromUrl,'','GET',array()));
		if($signList->Message == '成功'){
			$this->assign('signList',$signList->AppendData);
		}
		$this->display();
	}

	// 预约诊断信息
	public function makeDiagnosis(){
		$user = $this->checkWeixinInfo();
		$weixin = D('GsWeixin');
		if(IS_POST){
			$_POST['userId'] = $user['user_id'];
			$_POST['addTime'] = time();
			$_POST['userName'] = '(微信)'.$_POST['userName'];
			$result = $weixin->getAddDiagnosis($_POST);
			if($result){
				echo json_encode(array('ResultType'=>1,'Message'=>'预约成功'));
			}else{
				echo json_encode(array('ResultType'=>0,'Message'=>'预约失败'));
			}
			exit;
		}		
		$campusList = $weixin->getDeptList();
		$this->assign(get_defined_vars());
		$this->display();
	}

	// 预约成功提示页面
	public function prompt(){
		$user = $this->checkWeixinInfo();
		unset($_GET['_URL_']);
		$find = $_GET;
		$this->assign(get_defined_vars());
		$this->display();
	}

	// 用户收藏操作
	public function ajaxCollect(){
		$weixin = D('GsWeixin');
		import('ORG.Util.NCache');
		$cache = NCache::getCache($this->conf);
		$cookie = cookie('getOpenId');
		$wxid = $cache->get('openid', $cookie);
		$user = $weixin->findBindUserInfo($wxid);
		if(!empty($wxid) && !empty($user)){
			$param['user_id'] = $user['user_id'];
			$param['openid'] = $user['openid'];
			$param['info_id'] = $_POST['info_id'];
			$param['type'] = $_POST['type'];
			$param['add_time'] = time();
			$result = $weixin->getCollect($param);
			if($result == true){
				echo json_encode(array('status'=>1,'Message'=>'收藏成功'));
				exit;
			}else{
				echo json_encode(array('status'=>0,'Message'=>$result));
				exit;
			}
		}else{
			echo json_encode(array('status'=>0,'Message'=>'请先登录'));
			exit;
		}
		
	}

	// 收藏夹列表
	public function collectList(){

		$user = $this->checkWeixinInfo();

		$weixin = D('GsWeixin');
		
		$list = $weixin->getCollectList($user['user_id']);
		$collectRow = array();
		foreach($list as $key=>$info){
			if(is_numeric($info['info_id'])){

				$param = array(
					'id'=>$info['info_id'],
					'partner'=>10010
				);
				$ret = $this->sign($param);

				$fromUrl = $this->apiUrl.'/api/Class/GetDetail?'.$ret['url'].'&sign='.$ret['sign'];

				$details = json_decode($this->http($fromUrl,'','GET',array()));
				if(!empty($details->AppendData)){							

					$collectRow['class'][$key] = $details->AppendData;

				}

			}else{

				$fromUrl = $this->appUrl.'/So/Kecheng-wxKecheng-uid-'.$info['info_id'];
			
				$details = json_decode($this->http($fromUrl,'','GET',array()));			
			
				if($details->Message == '成功'){
					$collectRow['oneToOne'][$key] = $details->AppendData;
				}
			}

		}
		$this->assign('collectRow',$collectRow);
		$this->display();
	}

	public function ajaxCancel(){
		if(IS_POST){
			$user = $this->checkWeixinInfo();

			$weixin = D('GsWeixin');
			$_POST['user_id'] = $user['user_id'];
			$ajax = $weixin->getCancel($_POST);
			if($ajax == true){
				echo json_encode(array('status'=>1,'successMsg'=>'已取消'));
				die();
			}else{
				echo json_encode(array('status'=>0,'errorMsg'=>'未取消'));
				die();
			}	
		}
	}

	// 用户中心
	public function user(){

		$user = $this->checkWeixinInfo();

		$this->assign(get_defined_vars());
		
		$this->display();
	}

	// 用户登录
	public function login(){
		$openid = empty($_GET['openId'])?$_POST['openid']:$_GET['openId'];

		if(empty($openid)){
			$wechat = $this->getOpend($this->weiUrl);
			$openid = $wechat['openid'];
		}
		if(IS_POST){
			$_POST['openid'] = $openid;
			$_POST['login_time'] = time();
			$weixin = D('GsWeixin');
			$result = $weixin->getLogin($_POST);
			echo $result;die();
		}
		import('ORG.Util.NCache');
		$cache = NCache::getCache($this->conf);
		$cookie = cookie('getOpenId');
		$wxid = $cache->get('openid', $cookie);
		$result = D('GsWeixin')->delBind(array('openid'=>$wxid));
		$this->assign('openid',$openid);
		$this->display();
	}

	// 找回密码
	public function backPwd(){
		if(IS_POST){
			$code = cookie('backCode');

			if($code !== $_POST['code']){
				echo json_encode(array('status'=>0,'errorMsg'=>'验证码不正确'));
				die();
			}else{
				$weixin = D('GsWeixin');
				$modify = $weixin->getUpdateUser($_POST);
				if($modify){
					echo json_encode(array('status'=>1,'successMsg'=>'修改成功'));
					die();
				}else{
					echo json_encode(array('status'=>0,'errorMsg'=>'没有此用户'));
					die();
				}	
			}
		}else{
			$this->display();	
		}
	}

	// 用户注册
	public function register(){

		if(IS_POST){
			$code = cookie('noteCode');	
			if($code !== $_POST['code']){
				echo json_encode(array('status'=>0,'errorMsg'=>'验证码不正确'));
				exit;
			}else{
				if(!empty($_POST['openid']) && !empty($_POST['wx_name'])){
					unset($_POST['code']);
					$_POST['user_pwd'] = md5($_POST['user_pwd']);
					$_POST['user_time'] = time();
					$_POST['is_sign'] = 1;
					$weixin = D('GsWeixin');
					$result = $weixin->getRegister($_POST);
					if($result > 0){
						$data['user_id'] = $result;
						$data['openid'] = $_POST['openid'];
						$data['wx_name'] = $_POST['wx_name'];
						$data['login_time'] = time();
						$login = $weixin->getLogin($data);
						if($login > 0){
							$temp['openid'] = $_POST['openid'];
							$temp['mould'] = 'TLVBTIIiwE9ssZk3BSQzCkOMS1Wuf9Sb6WXDkVIQdDM';
							$temp['url'] = 'http://vip.gaosiedu.com/vip/gs_weixin/user';
							$temp['content'] = '亲爱的同学恭喜您注册成功';
							$temp['keyword'] = array(
													1=>$_POST['user_name'],
													2=>$_POST['user_mobile']
												);
							$temp['ending'] = '祝您在高思学习愉快！';
							$this->getSendNews($temp);
							echo json_encode(array('status'=>1,'successMsg'=>'注册成功'));
							exit;
						}else{
							echo json_encode(array('status'=>0,'errorMsg'=>'注册失败,重新注册'));
							exit;
						}
					}else{
						echo json_encode(array('status'=>0,'errorMsg'=>$result));
						exit;
					}
				}				
			}
		}else{
			$wechat = $this->getOpend($this->weiUrl);
			$weixin = D('GsWeixin');
			$deptlist = $weixin->getDeptList();
			$this->assign('wechat',$wechat);
			$this->assign('deptlist',$deptlist);	
		}
		
		$this->display();
	}

	// 发送手机验证码
	public function phoneCode(){
		$cookieCode = empty(cookie('noteCode'))?cookie('backCode'):cookie('noteCode');	
		if(empty($cookieCode)){
			$ramd = mt_rand(111111,999999);
			$str = '【北京爱提分】此验证码用于注册或修改,验证码为'.$ramd.'。再次提醒，请勿转发';
			$user = 'bjatf';
			$pwd = 'bjatf2016';
			$phone = $_POST['phone'];
			$smsUrl = "http://116.213.72.20/SMSHttpService/send.aspx?username=".$user."&password=".$pwd."&mobile=".$phone."&content=".$str;

			$code = $this->http($smsUrl,'','GET',array());

			if($code == 0){
				if(empty($_POST['action'])){
					cookie('noteCode',$ramd,60);	
				}else{
					cookie('backCode',$ramd,60);
				}
			}
		}

		echo $code;
	}

	// 用户退出
	public function logout(){
		import('ORG.Util.NCache');
		$cache = NCache::getCache($this->conf);
		$cookie = cookie('getOpenId');
		$wxid = $cache->get('openid', $cookie);
		$result = D('GsWeixin')->delBind(array('openid'=>$wxid));
		if($result!=false){
			$cache->delete('openid', $cookie);
			cookie('getOpenId',null);
			$this->redirect(U('/Vip/GsWeixin/login'));
		}else{
			$this->redirect(U('/Vip/GsWeixin/index'));
		}
	}

	// 每天为用户发送上课信息
	public function getSendUser(){
		$weixin = D('GsWeixin');
		$para['begin_on'] = date('Y-m-d',time());
		$orderList = $weixin->getClassOrder($uid = 0,$para);
		foreach($orderList as $order){
			$temp['openid'] = $order['openid'];
			$temp['mould'] = 'e_ZFiGg6EQpEi2Cbu40KfhDJOiESzzwLq708pcYuIrI';
			$temp['url'] = 'http://vip.gaosiedu.com/vip/gs_weixin/user';
			$temp['content'] = '亲爱的同学您好，您有一个课程即将上课啦！';
			$temp['keyword'] = array(
									1=>$order['class_name'],
									2=>$order['begin_on']
								);
			$temp['ending'] = '赶紧准备去上课吧！';
			$this->getSendNews($temp);
		}
	}

	// 获取appid
	public function getOpend($thisurl){
		import('ORG.Util.NCache');
		$cache = NCache::getCache($this->conf);
		$cookie = cookie('getOpenId');
		$wxid = $cache->get('openid', $cookie);
		if(false ==  $wxid){
			if(empty($_GET['code'])){
				$this->get_user_code($thisurl);
			}else{
				$appid = $this->appid;
				$secret = $this->appsecret; 
				$code = $_GET['code']; 
				$get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
				
				$json_obj = json_decode($this->http($get_token_url,'','GET',array()));
				
				if(!empty($json_obj->openid)){
					$cookie = md5(rand(10,10000).date('Y-m-d H:i:s',time()));
					$time = 365*24*3600;
					cookie('getOpenId',$cookie,$time);
					$cache->set('openid', $cookie, $json_obj->openid);
					$wxid = $json_obj->openid;
				} 
			}
		}
		//根据openid和access_token查询用户信息 
		$access_token = $this->getAccessToken();
		$openid = $wxid;
		$get_user_info_url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN'; 

		$user_obj = json_decode($this->http($get_user_info_url,'','GET',array()),true); 
		return $user_obj;
	}
	// 获取code码
	public function get_user_code($thisurl){
		
		$appid = $this->appid;
 		if(empty($_GET['code'])){
 			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&response_type=code&scope=snsapi_base&redirect_uri=".urlencode($thisurl);

			header("Location:".$url);
 		}
		
	}

	// 用户操作发放模板信息
	public function getSendNews($temp  = array()) {

			// 获取微信的access_token
			$accessToken = $this->getAccessToken();
			// 微信发送消息API接口链接
			$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$accessToken;
						
			if ( $temp['openid'] !== 'undefined' )
			{
				$str = '';
				foreach($temp['keyword'] as $key=>$keyword){
					$str .= '"keyword'.$key.'":{
										"value":"'.$keyword.'",
										"color":"#173177"
									},';
				}
				
				$json = '{
		           	"touser":"'.$temp['openid'].'",
		           	"template_id":"'.$temp['mould'].'",
		           	"url":"'.$temp['url'].'",            
		           	"topcolor":"#FF0000",
					"data":{
						"first": {
							"value":"'.$temp['content'].'",
							"color":"#173177"
						},
						'.$str.'
						"remark":{
							"value":"'.$temp['ending'].'",
							"color":"#173177"
						}
					}
		       	}';	

		       	$dataRes = json_decode($this->postData($url,$json));

			}


	} 

	// 获取微信access_token
	public function getAccessToken(){
		import('ORG.Util.NCache');
		$cache = NCache::getCache();
		$appid = $this->appid;
		$appsecret = $this->appsecret;
		$accessToken = $cache->get('access_token', $appid);
		if(false == $accessToken){
			$TokenUrl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
			$TokenData = json_decode(file_get_contents($TokenUrl),true);
			$accessToken = $TokenData['access_token'];
			$cache->set('access_token', $appid, $accessToken);
		}

		return $accessToken;
	}

	public function getJsApiSign($access_token = ''){
		import('ORG.Util.NCache');
		$cache = NCache::getCache();
		$appid = $this->appid;
		$jsapisign = $cache->get('jsapisign', $appid);
		if(false == $jsapisign){
			$signUrl = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$access_token."&type=jsapi";
			$signData = json_decode(file_get_contents($signUrl),true);
			$jsapisign = $signData['ticket'];
			$cache->set('jsapisign', $appid, $jsapisign);
		}

		return $jsapisign;
	}


	// http调取
	public function http($url, $params, $method = 'GET', $header = array(), $multi = false){

	    $opts = array(
	            CURLOPT_TIMEOUT        => 30,
	            CURLOPT_RETURNTRANSFER => 1,
	            CURLOPT_SSL_VERIFYPEER => false,
	            CURLOPT_SSL_VERIFYHOST => false,
	            CURLOPT_HTTPHEADER     => $header
	    );
	    /* 根据请求类型设置特定参数 */
	    switch(strtoupper($method)){
	        case 'GET':
	            $opts[CURLOPT_URL] = $url ;
	            break;
	        case 'POST':
	            //判断是否传输文件
	            $params = $multi ? $params : http_build_query($params);
	            $opts[CURLOPT_URL] = $url;
	            $opts[CURLOPT_POST] = 1;
	            $opts[CURLOPT_POSTFIELDS] = $params;
	            break;
	        default:
	            throw new Exception('不支持的请求方式！');
	    }

	    /* 初始化并执行curl请求 */
	    $ch = curl_init();
	    curl_setopt_array($ch, $opts);
	    $data  = curl_exec($ch);
	    $error = curl_error($ch);
	    curl_close($ch);
	    if($error) throw new Exception('请求发生错误：' . $error);
	    return  $data;
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


	// 微信支付的订单接口
    public function wechatClassApi(){
    	header("Content-Type:text/html; charset=utf-8");
    	$accid = $_GET['accid'];
	    $secret = $_GET['secret'];
	    if($accid == '2915360' && $secret == $this->appsecret){
	    	$weixin = D('GsWeixin');
	    	$orderList = $weixin->getOrderList($_GET);
	    	if(empty($orderList['PagedList'])){
                $status     = 0;
                $message    =   '暂无数据';
            }else{
                $status     = 1;
                $message    =   '成功';
            }
            $data= array(
	            'ResultType' => $status,
	            'Message' => $message,
	            'AppendData'=>$orderList 
	        );
	    }else{
	    	$data= array(
	            'ResultType' => 0,
	            'Message' => '账户与秘钥不正确',
	            'AppendData'=>'' 
	        );
	    }
	    echo $this->encode_json($data);
    }

    // 录入业务系统改变微信的记录
    public function setWechatApi(){
    	header("Content-Type:text/html; charset=utf-8");
    	$accid = $_GET['accid'];
	    $secret = $_GET['secret'];
	    if($accid == '2915360' && $secret == $this->appsecret){
	    	if(IS_POST){
	    		$weixin = D('GsWeixin');	    	
		    	$setOrder = $weixin->setWechatOrder($_POST);
		    	if($setOrder == false){
	                $status     = 0;
	                $message    =   '暂无数据';
	            }else{
	                $status     = 1;
	                $message    =   '成功';
	            }
	            $data= array(
		            'ResultType' => $status,
		            'Message' => $message,
		        );
	    	}
	    }else{
	    	$data= array(
	            'ResultType' => 0,
	            'Message' => '账户与秘钥不正确',
	        );
	    }
	    echo $this->encode_json($data);
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


}

?>