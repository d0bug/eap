<?php
class GsWeixinModel extends Model {

	public  $dao = null;

	public function __construct(){

		$this->dao = Dao::getDao('MSSQL_CONN');
		$this->dao3 = Dao::getDao('MYSQL_CONN2');
		// 微信端小班课报名表
		$this->vip_wxOrder = 'vip_wxOrder';
		// 校区表
		$this->vip_school = 'vip_school';
		// 微信端注册表
		$this->vip_wxUser = 'vip_wxUser';
		// 用户登录信息
		$this->vip_wxUserInfo = 'vip_wxUserInfo';
		// 预约诊断报名表
		$this->vip_form_wap_order = 'vip_form_wap_order';
		// 收藏表
		$this->vip_wxCollect = 'vip_wxCollect';
		// 课程报名
		$this->vip_form_kechengbaoming = 'vip_form_kechengbaoming';
		// 活动报名
		$this->vip_form_huodongbaoming = 'vip_form_huodongbaoming';

		$this->so_vip_dicts = 'so_vip_dicts';

		$this->vip_wxSubject = 'vip_wxSubject';
	}

	// 微信端小班课报名  dengjun
	public function getAddOrder($order=array()){
		if(!empty($order)){
			$success = (boolean)$this->dao3->execute(
					'INSERT INTO '.$this->vip_wxOrder.'
						(
							openid,
							user_id,
							class_id,
							order_sn,
							class_name,
							teacher_name,
							begin_on,
							dept_name,
							subject_name,
							order_price,
							order_time
						) 
						VALUES
						(
							'.$this->dao->quote($order['openid']).',
							'.$this->dao->quote($order['user_id']).',
							'.$this->dao->quote($order['class_id']).',
							'.$this->dao->quote($order['order_sn']).',
							'.$this->dao->quote($order['class_name']).',
							'.$this->dao->quote($order['teacher_name']).',
							'.$this->dao->quote($order['begin_on']).',
							'.$this->dao->quote($order['dept_name']).',
							'.$this->dao->quote($order['subject_name']).',
							'.$this->dao->quote($order['order_price']).',
							'.$this->dao->quote($order['order_time']).'
						)');
			if($success > 0){
				$sql = "SELECT * FROM ".$this->vip_wxOrder." WHERE order_sn ='".$order['order_sn']."' AND order_status = 0 ";
				$orderFind = $this->dao3->getRow($sql);
				return $orderFind;
			}else{
				return false;
			}
		}
	}

	// 获取微信端的订单信息
	public function getClassOrder($uid = 0,$para = array()){
		$id = abs($uid);
		if($id > 0){
			$page = isset($para['page']) ? abs($para['page']) : 1;
			$pageSize = isset($para['pagesize']) ? abs($para['pagesize']) : 10;
			$start = ($page - 1) * $pageSize;
			$status = empty($para['status'])?0:$para['status'];
			$class_name = empty($para['class_name'])?'':$para['class_name'];
			if(!empty($class_name)){
				$w = ' AND class_name like "%'.$class_name.'%"';
			}

			$sql = "SELECT * FROM ".$this->vip_wxOrder." WHERE user_id =".$id." AND order_status = ".$status." $w ORDER BY order_time DESC LIMIT   $start, $pageSize";

			$orderList = $this->dao3->getAll($sql);
		}else{
			if(!empty($para['begin_on'])){
				$sql = "SELECT * FROM ".$this->vip_wxOrder." WHERE begin_on like '%".$para['begin_on']."%' AND order_status = 1 ORDER BY order_time DESC ";

				$orderList = $this->dao3->getAll($sql);
			}
		}
		
		return $orderList;
	}

	// 删除微信端的订单信息
	public function getDelOrder($order_id = 0){
		if($order_id > 0){
			$sql = "DELETE FROM ".$this->vip_wxOrder." WHERE id ='".$order_id."'";
			$del = $this->dao3->execute($sql);
			if($del){
				return true;
			}else{
				return false;
			}
		}
	}

	// 获取微信端的用户是否下过此订单
	public function getClassFind($uid = 0,$classId=0){
		$uid = abs($uid);
		$classId = abs($classId);
		if($uid > 0 && $classId > 0){
			$sql = "SELECT * FROM ".$this->vip_wxOrder." WHERE user_id =".$uid." AND class_id =".$classId;
			$orderFind = $this->dao3->getRow($sql);
		}
		return $orderFind;
	}

	// 获取班课的支付订单数
	public function getClassNum($classId=0){
		$classId = abs($classId);
		if($classId > 0){
			$sql = "SELECT COUNT(*) FROM ".$this->vip_wxOrder." WHERE class_id =".$classId." AND order_status = 1 AND is_record = 1";
			$orderNum = $this->dao3->getOne($sql);
		}
		return $orderNum;
	}

	// 获取微信端的某条订单
	public function getOrderInfo($orderId = 0){
		if($orderId > 0 ){
			$sql = "SELECT * FROM ".$this->vip_wxOrder." WHERE id =".$orderId." AND order_status = 0";
			$orderInfo = $this->dao3->getRow($sql);
		}else{
			$sql = "SELECT * FROM ".$this->vip_wxOrder." WHERE order_sn ='".$orderId."' AND order_status = 0";
			$orderInfo = $this->dao3->getRow($sql);
		}
		return $orderInfo;
	}

	// 通过微信订单号获取订单信息 
	public function payOrderInfo($pay_number) {
	
		if(!empty($pay_number)){
			$sql = "SELECT * FROM ".$this->vip_wxOrder." WHERE  pay_number='".$pay_number."' AND order_status = 1";
			$orderInfo = $this->dao3->getRow($sql);
		}
		return $orderInfo;
	}

	// 退款后改变状态
	public function updateOrderRefund($order_sn,$status,$canRefundMoney) {
		if(!empty($order_sn)) {
			$uTime = date('Y-m-d H:i:s',time());
			$modify = $this->dao3->execute(
				'UPDATE '.$this->vip_wxOrder.' 
				SET order_status = '.$this->dao->quote($status).',
				can_refund_money = '.$this->dao->quote($canRefundMoney).',
				update_time ='.$this->dao->quote($uTime).'
				WHERE order_sn = '.$this->dao->quote($order_sn)
			);
			if($modify){
				return true;
			}else{
				return false;
			}
		}	
	}

	// 微信端支付完返回的支付状态
	public function updateOrder($order=array()){
		$order_sn = $order['out_trade_no'];
		if(!empty($order_sn)){

			if($order['bank_type'] == 'CFT'){
				$order['bank_type'] = '零钱支付';	
			}else{
				$order['bank_type'] = $this->BankCode($order['bank_type']);
			}
			$modify = $this->dao3->execute(
				'UPDATE '.$this->vip_wxOrder.' 
				SET pay_price = '.$this->dao->quote($order['pay_price']/100).',
				pay_time = '.$this->dao->quote($order['time_end']).',
				pay_mode = '.$this->dao->quote($order['bank_type']).',
				pay_number = '.$this->dao->quote($order['transaction_id']).',
				order_status = 1,
				is_record = 1 ,
				pay_trade_no='.$this->dao->quote($order['pay_trade_no']).',
				can_refund_money = '.$this->dao->quote($order['pay_price']/100).'
				WHERE order_sn = '.$this->dao->quote($order_sn)
			);
			if($modify){
				return true;
			}else{
				return false;
			}
		}
	}

	// 获取全部订单
	public function getOrderList($param=array()){
		unset($param['_URL_']);
		unset($param['accid']);
		unset($param['secret']);
		$where = '';
		foreach($param as $key=>$w){
			if(($key != 'pageIndex' || $key != 'pageSize' || $key != 'sort') && $w !=''){
				$str = explode(')', trim($key,'('));
				if($str[1] == 'user_name' || $str[1] == 'user_mobile'){
					if($str[0] == 'Contains'){
						$where .= ' AND u.'.$str[1].' like "%'.$w.'%"';
					}elseif($str[0] == 'Equal'){
						$where .= ' AND u.'.$str[1].' = "'.$w.'"';
					}elseif($str[0] == 'LessThan'){
						$where .= ' AND u.'.$str[1].' < "'.$w.'"';
					}elseif($str[0] == 'GreaterThan'){
						$where .= ' AND u.'.$str[1].' > "'.$w.'"';
					}elseif($str[0] == 'LessThanOrEqual'){
						$where .= ' AND u.'.$str[1].' <= "'.$w.'"';
					}elseif($str[0] == 'GreaterThanOrEqual'){
						$where .= ' AND u.'.$str[1].' >= "'.$w.'"';
					}elseif($str[0] == 'NotEqual'){
						$where .= ' AND u.'.$str[1].' != "'.$w.'"';
					}elseif($str[0] == 'StartsWith'){
						$where .= ' AND u.'.$str[1].' like "%'.$w.'"';
					}elseif($str[0] == 'EndsWith'){
						$where .= ' AND u.'.$str[1].' like "'.$w.'%"';
					}elseif($str[0] == 'Null'){
						$where .= ' AND u.'.$str[1].' = ""';
					}elseif($str[0] == 'NotNull'){
						$where .= ' AND u.'.$str[1].' !== ""';
					}
				}else{
					if($str[1] == 'order_time' || $str[1] == 'pay_time'){
						$w = strtotime($w.' 23:59:59');
					}
					if($str[0] == 'Contains'){
						$where .= ' AND o.'.$str[1].' like "%'.$w.'%"';
					}elseif($str[0] == 'Equal'){
						$where .= ' AND o.'.$str[1].' = "'.$w.'"';
					}elseif($str[0] == 'LessThan'){
						$where .= ' AND o.'.$str[1].' < "'.$w.'"';
					}elseif($str[0] == 'GreaterThan'){
						$where .= ' AND o.'.$str[1].' > "'.$w.'"';
					}elseif($str[0] == 'LessThanOrEqual'){
						$where .= ' AND o.'.$str[1].' <= "'.$w.'"';
					}elseif($str[0] == 'GreaterThanOrEqual'){
						$where .= ' AND o.'.$str[1].' >= "'.$w.'"';
					}elseif($str[0] == 'NotEqual'){
						$where .= ' AND o.'.$str[1].' != "'.$w.'"';
					}elseif($str[0] == 'StartsWith'){
						$where .= ' AND o.'.$str[1].' like "%'.$w.'"';
					}elseif($str[0] == 'EndsWith'){
						$where .= ' AND o.'.$str[1].' like "'.$w.'%"';
					}elseif($str[0] == 'Null'){
						$where .= ' AND o.'.$str[1].' = ""';
					}elseif($str[0] == 'NotNull'){
						$where .= ' AND o.'.$str[1].' !== ""';
					}
				}
				
			}			 
		}

		if(!empty($param['sort'])){
			$sub = substr($param['sort'],0,1);
			if($sub == 'u'){
				$sort = ' u.'.$param['sort'];
			}else{
				$sort = ' o.'.$param['sort'];
			}
		}else{
			$sort = ' o.order_time desc';
		}
		$currentPage = $_GET['pageIndex'];
		$pageSize  = $_GET['pageSize'];	
		$currentPage = empty ( $currentPage ) ? 1 : ($currentPage - 1) * $pageSize;
		$pageSize = empty ( $pageSize ) ? 20 : $pageSize;
		$sql = " SELECT
					u.user_name,
					u.user_mobile,
					o.class_id, 
					o.class_name,
					o.teacher_name,
					o.begin_on,
					o.dept_name,
					o.subject_name,
					o.order_price,
					from_unixtime(o.order_time,'%Y-%m-%d %H:%i:%s') as order_time,
					o.pay_price,
					from_unixtime(o.pay_time,'%Y-%m-%d %H:%i:%s') as pay_time,
					o.pay_mode,
					o.pay_number,
					o.order_status,
					o.can_refund_money 
				FROM ".$this->vip_wxOrder." AS o LEFT JOIN ".$this->vip_wxUser." AS u ON u.id= o.user_id 
				WHERE o.order_price > 0 ".$where.' ORDER BY ' . $sort . '
									 LIMIT ' . $currentPage . ', ' . $pageSize;
		$orderList['PagedList'] = $this->dao3->getAll($sql);	
		$orderList['CurrentPageIndex'] = empty($_GET['pageIndex'])?1:$_GET['pageIndex'];
		$orderList['PageSize '] = $pageSize;
		$sql = " SELECT COUNT(o.id) AS count FROM ".$this->vip_wxOrder." AS o LEFT JOIN ".$this->vip_wxUser." AS u ON u.id= o.user_id 
				WHERE o.order_price > 0 ".$where;
		$count = $this->dao3->getOne($sql);	
		$orderList['TotalPageCount '] = ceil($count/$pageSize);
		$orderList['TotalItemCount'] = $count;
		return $orderList;	
	}

	// 获取校区信息
	public function getDeptList(){
		$sql = "SELECT id,title FROM ".$this->vip_school;
	
		$deptList = $this->dao3->getAll($sql);
		return $deptList;
	}


	// 微信端注册接口
	public function getRegister($user=array()){

		$sql = "SELECT id FROM ".$this->vip_wxUser." WHERE user_mobile = ".$this->dao->quote($user['user_mobile']);
		$userId = $this->dao3->getOne($sql);
		if(empty($userId)){
			$success = (boolean)$this->dao3->execute(
				'INSERT INTO '.$this->vip_wxUser.'
					(
						openid,
						wx_name,
						user_name,
						headimgurl,
						user_mobile,
						user_pwd,
						user_dept,
						user_time,
						is_sign
					) 
					VALUES
					(
						'.$this->dao->quote($user['openid']).',
						'.$this->dao->quote($user['wx_name']).',
						'.$this->dao->quote($user['user_name']).',
						'.$this->dao->quote($user['headimgurl']).',
						'.$this->dao->quote($user['user_mobile']).',
						'.$this->dao->quote($user['user_pwd']).',
						'.$this->dao->quote($user['user_dept']).',
						'.$this->dao->quote($user['user_time']).',
						'.$this->dao->quote($user['is_sign']).'
					)');
			if($success >0){
				$lastId = $this->dao3->getOne('SELECT id FROM '.$this->vip_wxUser.' WHERE openid = "'.$user['openid'].'" ORDER BY id DESC');
				return $lastId;
			}else{
				return false;
			}
		}
		else
		{
			return '该手机号已注册';
		}
		
	}

	public function getLogin($user = array()){
		if(!empty($user)){
			if(!empty($_POST['action'])){
				$sql = "SELECT id FROM ".$this->vip_wxUser." 
						WHERE user_mobile = '".$user['user_mobile']."' 
						AND user_pwd = '".md5($user['user_pwd'])."'";
				$userId = $this->dao3->getOne($sql);
				if($userId > 0){
					$sql = "DELETE FROM ".$this->vip_wxUserInfo." WHERE openid ='".$user['openid']."'";
					$del = $this->dao3->execute($sql);
					$success = $this->dao3->execute(
					'INSERT INTO '.$this->vip_wxUserInfo.'
						(
							user_id,
							openid,
							wx_name,
							login_time
						) 
						VALUES
						(
							'.$this->dao->quote($userId).',
							'.$this->dao->quote($user['openid']).',
							'.$this->dao->quote($user['wx_name']).',
							'.$this->dao->quote($user['login_time']).'
						)');
					if($success){
						return true;
					}else{
						return false;
					}
				}else{
					return false;
				}			
			}else{
				$sql = "DELETE FROM ".$this->vip_wxUserInfo." WHERE openid ='".$user['openid']."'";
				$del = $this->dao3->execute($sql);
				$success = $this->dao3->execute(
					'INSERT INTO '.$this->vip_wxUserInfo.'
						(
							user_id,
							openid,
							wx_name,
							login_time
						) 
						VALUES
						(
							'.$this->dao->quote($user['user_id']).',
							'.$this->dao->quote($user['openid']).',
							'.$this->dao->quote($user['wx_name']).',
							'.$this->dao->quote($user['login_time']).'
						)');
				if($success){
					return true;
				}else{
					return false;
				}	
			}
		}
	}

	public function delBind($data = array()){
		$sql = "DELETE FROM ".$this->vip_wxUserInfo." WHERE openid ='".$data['openid']."'";
		$del = $this->dao3->execute($sql);
		if($del){
			return true;
		}else{
			return false;
		}
	}

	public function findBindUserInfo($openId){
		$sql = 
			"SELECT u.user_name,u.user_mobile,u.headimgurl,i.* FROM ".$this->vip_wxUser." AS u 
			LEFT JOIN ".$this->vip_wxUserInfo." AS i ON u.id = i.user_id WHERE i.openid = ".$this->dao->quote($openId);
		return $this->dao3->getRow($sql);
	}

	public function getUpdateUser($user = array()){
		$modifyUser = $this->dao3->execute(
				'UPDATE '.$this->vip_wxUser.' 
				SET user_pwd = '.$this->dao->quote(md5($user['user_pwd'])).' 
				WHERE user_mobile = '.$this->dao->quote($user['user_mobile'])
			);
		if($modifyUser){
			return true;
		}else{
			return false;
		}
	}

	// 获取当前年级信息
	public function listDictByCate($cate ='', $field = '*',$where=' 1=1 '){
				$sql = "
			SELECT  $field 
			FROM ".$this->so_vip_dicts."
			WHERE  cate = '$cate' AND $where
			ORDER BY sort_id ASC
		";
		
		return $this->dao3->getAll($sql);
	}

	// 微信端添加预约诊断报名
	public function getAddDiagnosis($data = array()){
		$success = (boolean)$this->dao3->execute(
					'INSERT INTO '.$this->vip_form_wap_order.'
						(
							userid,
							name,
							tel,
							nianji,
							ip,
							datetime						
						) 
						VALUES
						(
							'.$this->dao->quote($data['userId']).',
							'.$this->dao->quote($data['userName']).',
							'.$this->dao->quote($data['userMobile']).',
							'.$this->dao->quote($data['userGrade']).',
							'.$this->dao->quote($_SERVER["REMOTE_ADDR"]).',
							'.$this->dao->quote($data['addTime']).'
						)');

			if($success){
				return true;
			}else{
				return false;
			}
	}

	// 微信端添加收藏操作
	public function getCollect($param = array()){
		$w = "user_id =".$param['user_id']." AND info_id ='".$param['info_id']."' AND type =".$param['type'];
		
		$Collect_info = $this->dao3->getRow("SELECT * FROM ".$this->vip_wxCollect." WHERE ".$w);
		if(empty($Collect_info)){
			$success = (boolean)$this->dao3->execute(
					'INSERT INTO '.$this->vip_wxCollect.'
						(
							user_id,
							openid,
							info_id,
							type,
							add_time
						) 
						VALUES
						(
							'.$this->dao->quote($param['user_id']).',
							'.$this->dao->quote($param['openid']).',
							'.$this->dao->quote($param['info_id']).',
							'.$this->dao->quote($param['type']).',
							'.$this->dao->quote($param['add_time']).'
						)');
			if($success){
				return true;
			}else{
				return false;
			}

		}else{
			return '该信息已加入收藏';
		}
	}

	// 获取用户是否已收藏
	public function getCollectFind($param = array()){
		$w = "user_id =".$param['user_id']." AND info_id ='".$param['info_id']."' AND type =".$param['type'];
		
		$Collect_info = $this->dao3->getRow("SELECT * FROM ".$this->vip_wxCollect." WHERE ".$w);
		if(empty($Collect_info)){		
			return true;
		}else{
			return false;
		}
	}

	// 获取用户收藏信息
	public function getCollectList($user_id = 0){
		if(!empty($user_id)){
			$sql = "SELECT * FROM ".$this->vip_wxCollect." WHERE user_id=".$user_id." ORDER BY add_time DESC";
	
			$collectList = $this->dao3->getAll($sql);
			return $collectList;
		}
	}
	// 取消用户收藏信息
	public function getCancel($param=array()){
		if(!empty($param)){
			$sql = "DELETE FROM ".$this->vip_wxCollect." WHERE user_id=".$param['user_id']." AND info_id = '".$param['info_id']."' AND type=".$param['type'];
	
			$cancel = (boolean)$this->dao3->execute($sql);
			if($cancel){		
				return true;
			}else{
				return false;
			}
		}
	}

	// 录入业务系统改变状态
	public function setWechatOrder($data = array() ){
		if( !empty($data) ){
			$modifyUser = $this->dao3->execute(
				'UPDATE '.$this->vip_wxOrder.' 
				SET is_record = 0 
				WHERE pay_number = '.$this->dao->quote($data['pay_number'])
			);

			if($modifyUser){
				return true;
			}else{
				return false;
			}
		}	
	}

	// 获取学科
	public function wechatSubject(){
		$sql = "
			SELECT  * 
			FROM ".$this->vip_wxSubject."
			WHERE status = 1
			ORDER BY id ASC
		";
		
		return $this->dao3->getAll($sql);
	}

	// 获取附近的校区
	public function getLatelySchool($latitude,$longitude){
		$jd=round($longitude,6);
		$wd=round($latitude,6);
		$jl=round(10*(10/1110),6);
		$sql = 'SELECT title FROM '.$this->vip_school.' WHERE
			latitude > '.$wd.'-'.$jl.'and
			latitude <='.$wd.'+'.$jl.' and
			longitude > '.$jd.'-'.$jl.' and
			longitude < '.$jd.'+'.$jl.'
			ORDER BY ACOS(SIN(('.$wd.' * 3.1415) / 180 ) *SIN((latitude * 3.1415) / 180 ) +COS(('.$wd.' * 3.1415) / 180 ) * COS((latitude * 3.1415) / 180 ) *COS(('.$jd.' * 3.1415) / 180 - (longitude * 3.1415) / 180 ) ) * 6380 ASC';
		$schoolList = $this->dao3->getAll($sql);
		return $schoolList;
	}


	// 微信支付的银行编码
	public function BankCode($bank){
		$code = Array
		(
		    'ICBC_DEBIT' => '工商银行(借记卡)',
		    'ICBC_CREDIT' => '工商银行(信用卡)',
		    'ABC_DEBIT' => '农业银行(借记卡)',
		    'ABC_CREDIT' => '农业银行(信用卡)',
		    'PSBC_DEBIT' => '邮政储蓄银行(借记卡)',
		    'PSBC_CREDIT' => '邮政储蓄银行(信用卡)',
		    'CCB_DEBIT' => '建设银行(借记卡)',
		    'CCB_CREDIT' => '建设银行(信用卡)',
		    'CMB_DEBIT' => '招商银行(借记卡)',
		    'CMB_CREDIT' => '招商银行(信用卡)',
		    'BOC_DEBIT' => '中国银行(借记卡)',
		    'BOC_CREDIT' => '中国银行(信用卡)',
		    'COMM_DEBIT' => '交通银行(借记卡)',
		    'SPDB_DEBIT' => '浦发银行(借记卡)',
		    'SPDB_CREDIT' => '浦发银行(信用卡)',
		    'GDB_DEBIT' => '广发银行(借记卡)',
		    'GDB_CREDIT' => '广发银行(信用卡)',
		    'CMBC_DEBIT' => '民生银行(借记卡)',
		    'CMBC_CREDIT' => '民生银行(信用卡)',
		    'PAB_DEBIT' => '平安银行(借记卡)',
		    'PAB_CREDIT' => '平安银行(信用卡)',
		    'CEB_DEBIT' => '光大银行(借记卡)',
		    'CEB_CREDIT' => '光大银行(信用卡)',
		    'CIB_DEBIT' => '兴业银行(借记卡)',
		    'CIB_CREDIT' => '兴业银行(信用卡)',
		    'CITIC_DEBIT' => '中信银行(借记卡)',
		    'CITIC_CREDIT' => '中信银行(信用卡)',
		    'BOSH_DEBIT' => '上海银行(借记卡)',
		    'BOSH_CREDIT' => '上海银行(信用卡)',
		    'CRB_DEBIT' => '华润银行(借记卡)',
		    'HZB_DEBIT' => '杭州银行(借记卡)',
		    'HZB_CREDIT' => '杭州银行(信用卡)',
		    'BSB_DEBIT' => '包商银行(借记卡)',
		    'BSB_CREDIT' => '包商银行(信用卡)',
		    'CQB_DEBIT' => '重庆银行(借记卡)',
		    'SDEB_DEBIT' => '顺德农商行(借记卡)',
		    'SZRCB_DEBIT' => '深圳农商银行(借记卡)',
		    'HRBB_DEBIT' => '哈尔滨银行(借记卡)',
		    'BOCD_DEBIT' => '成都银行(借记卡)',
		    'GDNYB_DEBIT' => '南粤银行(借记卡)',
		    'GDNYB_CREDIT' => '南粤银行(信用卡)',
		    'GZCB_DEBIT' => '广州银行(借记卡)',
		    'GZCB_CREDIT' => '广州银行(信用卡)',
		    'JSB_DEBIT' => '江苏银行(借记卡)',
		    'JSB_CREDIT' => '江苏银行(信用卡)',
		    'NBCB_DEBIT' => '宁波银行(借记卡)',
		    'NBCB_CREDIT' => '宁波银行(信用卡)',
		    'NJCB_DEBIT' => '南京银行(借记卡)',
		    'JZB_DEBIT' => '晋中银行(借记卡)',
		    'KRCB_DEBIT' => '昆山农商(借记卡)',
		    'LJB_DEBIT' => '龙江银行(借记卡)',
		    'LNNX_DEBIT' => '辽宁农信(借记卡)',
		    'LZB_DEBIT' => '兰州银行(借记卡)',
		    'WRCB_DEBIT' => '无锡农商(借记卡)',
		    'ZYB_DEBIT' => '中原银行(借记卡)',
		    'ZJRCUB_DEBIT' => '浙江农信(借记卡)',
		    'WZB_DEBIT' => '温州银行(借记卡)',
		    'XAB_DEBIT' => '西安银行(借记卡)',
		    'JXNXB_DEBIT' => '江西农信(借记卡)',
		    'NCB_DEBIT' => '宁波通商银行(借记卡)',
		    'NYCCB_DEBIT' => '南阳村镇银行(借记卡)',
		    'NMGNX_DEBIT' => '内蒙古农信(借记卡)',
		    'SXXH_DEBIT' => '陕西信合(借记卡)',
		    'SRCB_CREDIT' => '上海农商银行(信用卡)',
		    'SJB_DEBIT' => '盛京银行(借记卡)',
		    'SDRCU_DEBIT' => '山东农信(借记卡)',
		    'SRCB_DEBIT' => '上海农商银行(借记卡)',
		    'SCNX_DEBIT' => '四川农信(借记卡)',
		    'QLB_DEBIT' => '齐鲁银行(借记卡)',
		    'QDCCB_DEBIT' => '青岛银行(借记卡)',
		    'PZHCCB_DEBIT' => '攀枝花银行(借记卡)',
		    'ZJTLCB_DEBIT' => '浙江泰隆银行(借记卡)',
		    'TJBHB_DEBIT' => '天津滨海农商行(借记卡)',
		    'WEB_DEBIT' => '微众银行(借记卡)',
		    'YNRCCB_DEBIT' => '云南农信(借记卡)',
		    'WFB_DEBIT' => '潍坊银行(借记卡)',
		    'WHRC_DEBIT' => '武汉农商行(借记卡)',
		    'ORDOSB_DEBIT' => '鄂尔多斯银行(借记卡)',
		    'XJRCCB_DEBIT' => '新疆农信银行(借记卡)',
		    'ORDOSB_CREDIT' => '鄂尔多斯银行(信用卡)',
		    'CSRCB_DEBIT' => '常熟农商银行(借记卡)',
		    'JSNX_DEBIT' => '江苏农商行(借记卡)',
		    'GRCB_CREDIT' => '广州农商银行(信用卡)',
		    'GLB_DEBIT' => '桂林银行(借记卡)',
		    'GDRCU_DEBIT' => '广东农信银行(借记卡)',
		    'GDHX_DEBIT' => '广东华兴银行(借记卡)',
		    'FJNX_DEBIT' => '福建农信银行(借记卡)',
		    'DYCCB_DEBIT' => '德阳银行(借记卡)',
		    'DRCB_DEBIT' => '东莞农商行(借记卡)',
		    'CZCB_DEBIT' => '稠州银行(借记卡)',
		    'CZB_DEBIT' => '浙商银行(借记卡)',
		    'CZB_CREDIT' => '浙商银行(信用卡)',
		    'GRCB_DEBIT' => '广州农商银行(借记卡)',
		    'CSCB_DEBIT' => '长沙银行(借记卡)',
		    'CQRCB_DEBIT' => '重庆农商银行(借记卡)',
		    'CBHB_DEBIT' => '渤海银行(借记卡)',
		    'BOIMCB_DEBIT' => '内蒙古银行(借记卡)',
		    'BOD_DEBIT' => '东莞银行(借记卡)',
		    'BOD_CREDIT' => '东莞银行(信用卡)',
		    'BOB_DEBIT' => '北京银行(借记卡)',
		    'BNC_DEBIT' => '江西银行(借记卡)',
		    'BJRCB_DEBIT' => '北京农商行(借记卡)',
		    'AE_CREDIT' => 'AE(信用卡)',
		    'GYCB_CREDIT' => '贵阳银行(信用卡)',
		    'JSHB_DEBIT' => '晋商银行(借记卡)',
		    'JRCB_DEBIT' => '江阴农商行(借记卡)',
		    'JNRCB_DEBIT' => '江南农商(借记卡)',
		    'JLNX_DEBIT' => '吉林农信(借记卡)',
		    'JLB_DEBIT' => '吉林银行(借记卡)',
		    'JJCCB_DEBIT' => '九江银行(借记卡)',
		    'HXB_DEBIT' => '华夏银行(借记卡)',
		    'HXB_CREDIT' => '华夏银行(信用卡)',
		    'HUNNX_DEBIT' => '湖南农信(借记卡)',
		    'HSB_DEBIT' => '徽商银行(借记卡)',
		    'HSBC_DEBIT' => '恒生银行(借记卡)',
		    'HRXJB_DEBIT' => '华融湘江银行(借记卡)',
		    'HNNX_DEBIT' => '河南农信(借记卡)',
		    'HKBEA_DEBIT' => '东亚银行(借记卡)',
		    'HEBNX_DEBIT' => '河北农信(借记卡)',
		    'HBNX_DEBIT' => '湖北农信(借记卡)',
		    'HBNX_CREDIT' => '湖北农信(信用卡)',
		    'GYCB_DEBIT' => '贵阳银行(借记卡)',
		    'GSNX_DEBIT' => '甘肃农信(借记卡)',
		    'JCB_CREDIT' => 'JCB(信用卡)',
		    'MASTERCARD_CREDIT' => 'MASTERCARD(信用卡)',
		    'VISA_CREDIT' => 'VISA(信用卡)'
		);
		return $code[$bank];
	}

}
?>