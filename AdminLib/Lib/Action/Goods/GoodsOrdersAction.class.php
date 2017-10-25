<?php
class GoodsOrdersAction extends GoodsCommAction{
	/*订单列表  niuxitong 2015 03 09*/
    public function orderList(){
		$permValue = $this->permValue;
		import("ORG.Util.Page");	//导入分页类
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE'); 

		//-------- 查询条件
		$status = $_POST['status'] ? $_POST['status'] : $_GET['status'];				//状态
		$giftcode = $_POST['giftcode'] ? $_POST['giftcode'] : $_GET['giftcode'];			//礼品编号
		$areacode = $_POST['areacode'] ? $_POST['areacode'] : $_GET['areacode'];			//校区编号
		$orderid = $_POST['orderid'] ? $_POST['orderid'] : $_GET['orderid'];			//订单状态
		$sname = $_POST['sname'] ? $_POST['sname'] : $_GET['sname'];				//收货人姓名（学生姓名）
		$saliascode = $_POST['saliascode'] ? $_POST['saliascode'] : $_GET['saliascode'];		//学号
		$stumobile = $_POST['stumobile'] ? $_POST['stumobile'] : $_GET['stumobile'];		//手机号
		
		$date_start = $_POST['status'] ? $_POST['status'] : $_GET['date_start'];		
		$date_end = $_POST['status'] ? $_POST['status'] : $_GET['date_end'];
		
		$condition ="1=1";
		if(!empty($status)){$condition = " status = '$status' ";}
		if(!empty($giftcode)){$condition .= " AND o.giftCode = '$giftcode' ";}
		if(!empty($areacode)){$condition .= " AND areaCode = '$areacode' ";}
		if(!empty($orderid)){$condition .= " AND orderId = '$orderid' ";}
		if(!empty($sname)){$condition .= " AND s.sName = '$sname' ";}
		if(!empty($saliascode)){$condition .= " AND s.sAliasCode = '$saliascode' ";}
		if(!empty($stumobile)){$condition .= " AND stuMobile = '$stumobile' ";}
		//---处理时间范围
		if(!empty($date_start)){
			$time_bg = $this->date_int($date_start);
			$time_end = $this->date_int($date_end);
			if($time_bg > $time_end){ //第一个时间 不能 大于第二个时间
				//$this->error('时间输入有误');
				echo "<script language=javascript>alert('日期输入有误，后面的必须大于前面的日期');history.go(-1);</script>";
			}else{
				$condition .= " AND beginTime  between '$time_bg' and '$time_end' ";	
			}
		}
		//$condition = $this->filter_permission($condition,array('sid'=>$handouts_subject,'gid'=>$handouts_grade),$is_jiaoyan);//权限筛选

		//订单列表 
		$mgsOrdersModel = D('MgsGiftOrder'); 
		$ordersList = $mgsOrdersModel->get_ordersList($condition, $curPage, $pagesize);
		$areaList = $mgsOrdersModel->get_areaList();  //获取所有校区供查询使用
		$giftList = $mgsOrdersModel->get_giftList();  //获取所有礼品供查询使用
		if(!empty($ordersList)){
			import("ORG.Util.String");
			$string = new String();
			/*foreach ($newsList as $key => $new){  //格式化相关 字段的格式
				$newsList[$key]['instime'] = date('Y-m-d H:i:s',$new['instime']);
				$newsList[$key]['title'] = $string->msubstr($new['title'],0,160,'utf-8');
			}*/
		}
		$count = $mgsOrdersModel->get_ordersCount($condition);
		$page = new page($count,$pagesize);
		$showPage = $page->show();
		
		/*---- 处理下载exces---*/
		/*$action = $_POST['action'] ? $_POST['action'] : $_GET['action'];
		if($action == 'down'){
			echo $condition;	
		}*/
		
		/*---- end ---*/
		
		$this->assign(get_defined_vars()); 
		$this->display();
    }
	
	//修改订单状态
	public function ordersUpdate(){
		$serial = isset($_GET['serial'])?intval($_GET['serial']):0;	
		$status = isset($_GET['status'])?intval($_GET['status']):0;	
		$giftquantity = isset($_GET['giftquantity'])?intval($_GET['giftquantity']):0;
		
		$giftcode = $_GET['giftcode'];
		$areacode = $_GET['areacode'];  //通过这两个参数来获取 MGS_StockPile表中的对应的一个礼品在一个校区的分配情况
		
		$mgsOrdersModel = D('MgsGiftOrder');
		if($mgsOrdersModel->ordersUpdate($serial, $status, $giftquantity, $giftcode, $areacode)){
			$this->success('修改成功！');
		}else{
			$this->error('修改失败！');
		}
	}
	
	//撤销订单	#2014 05 23
	public function ordersDel(){
		$serial = isset($_GET['serial'])?intval($_GET['serial']):0;			#订单id
		$status = isset($_GET['status'])?intval($_GET['status']):0;			#订单状态
		$giftquantity = isset($_GET['giftquantity'])?intval($_GET['giftquantity']):0;		#礼品数量
		
		$giftcode = $_GET['giftcode'];			#礼品编号
		$areacode = $_GET['areacode'];  //通过这两个参数来获取 MGS_StockPile表中的对应的一个礼品在一个校区的分配情况
		$stucode  = $_GET['stucode'];  #学员编号
		$mgsOrdersModel = D('MgsGiftOrder');
		if($mgsOrdersModel->ordersDel($serial, $status, $giftquantity, $giftcode, $areacode, $stucode)){
			$this->success('撤销成功！');
		}else{
			$this->error('撤销失败！');
		}
	}
	
	//将 '2013-10-20' 转换成时间戳  
	public function date_int($ymd,$h=0,$i=0,$s=0){
			$YMD = explode('-', $ymd);
			return mktime($h, $i, $s, $YMD[1], $YMD[2],$YMD[0]);
	}
	
	// 下载报表
	public function orderDown(){
		$mgsOrdersModel = D('MgsGiftOrder'); 
		$orderDownList = $mgsOrdersModel->get_orderDownList();
		$filename = date("YmjHis");
		header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Disposition: attachment;filename=$filename.xls ");
		header("Content-Transfer-Encoding: binary ");
		//输出表头
		echo iconv("utf-8", "gb2312", "预约单号")."\t";
		echo iconv("utf-8", "gb2312", "下单时间")."\t";
		echo iconv("utf-8", "gb2312", "礼品名称")."\t";
		echo iconv("utf-8", "gb2312", "数量")."\t";
		echo iconv("utf-8", "gb2312", "收货人")."\t";
		echo iconv("utf-8", "gb2312", "学号")."\t";
		echo iconv("utf-8", "gb2312", "联系电话")."\t";
		echo iconv("utf-8", "gb2312", "校区")."\t";
		echo iconv("utf-8", "gb2312", "订单状态")."\t";
		echo iconv("utf-8", "gb2312", "领取时间")."\t";
		print "\n";	
		for($i=0; $i<count($orderDownList); $i++){
			// iconv("原编码", "新编码", "输出内容")  -- 如果系统不支持此函数
			echo iconv("utf-8", "gb2312",$orderDownList[$i]['orderid'])."\t";
			echo iconv("utf-8", "gb2312",date('Y-m-d H:i:s',$orderDownList[$i]['begintime']))."\t";
			echo iconv("utf-8", "gb2312",$orderDownList[$i]['giftname'])."\t";
			echo iconv("utf-8", "gb2312",$orderDownList[$i]['giftquantity'])."\t";
			echo iconv("utf-8", "gb2312",$orderDownList[$i]['sname'])."\t";
			echo iconv("utf-8", "gb2312",$orderDownList[$i]['saliascode'])."\t";
			echo iconv("utf-8", "gb2312",$orderDownList[$i]['stumobile'])."\t";
			echo iconv("utf-8", "gb2312",$orderDownList[$i]['aname'])."\t";
			echo iconv("utf-8", "gb2312",$orderDownList[$i]['status'] == 1 ?  '未领': '已领')."\t";
			echo iconv("utf-8", "gb2312",$orderDownList[$i]['status'] == 1 ?  '未领': date('Y-m-d H:i:s', $orderDownList[$i]['endtime']))."\t";
			print "\n";	
		}
	}
	
	
    
}


?>