<?php
/*网校-营销管理*/
class VipschoolMarketingAction extends VipschoolCommAction{
	protected function notNeedLogin() {
		return array('');
	}


	/*学习卡管理*/
	public function studyCardList(){
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$condition = ' 1=1 ';
		$gsschoolModel = D('Vipschool');
		$dao = $gsschoolModel->dao;
		if($_REQUEST['card_code']!=''){
			$condition .= ' AND c.card_code = '.$dao->quote(SysUtil::safeSearch_vip(urldecode(trim($_REQUEST['card_code']))));
		}
		if(!empty($_REQUEST['type'])){
			if($_REQUEST['type'] == '1'){
				$condition .= ' AND c.use_time is not NULL ';
			}else{
				$condition .= ' AND c.use_time is NULL ';
			}
		}
		if(!empty($_REQUEST['username'])){
			$condition .= ' AND u.username = '.$dao->quote(trim($_REQUEST['username']));
		}
		$studyCardList = $gsschoolModel->get_studyCardList($condition,$curPage,$pagesize);
		$count = $gsschoolModel->get_studyCardCount($condition);
		$page = new page($count,$pagesize);
		$showPage = $page->show();
		$card_code = trim($_REQUEST['card_code']);
		$type = $_REQUEST['type'];
		$username = trim($_REQUEST['username']);
		$this->assign(get_defined_vars());
		$this->display();
	}


	/*生成学习卡*/
	public function addStudyCard(){
		if($_POST){
			$gsschoolModel = D('Vipschool');
			$courseInfo = $gsschoolModel->getCourseInfoByName($_POST['course_name']);
			if(empty($courseInfo)){
				$this->error('指定课程不存在');
			}
			$_POST['endtime'] = date('Y-m-d H:i:s',strtotime($_POST['endtime'])+24*3600);
			$arr = $_POST;
			$arr['course_id'] = $courseInfo['id'];
			$studyCardList = $this->createStudyCard($_POST);
			$result = $gsschoolModel->add_studyCard($studyCardList,$arr);
			if($result){
				$this->success('学习卡生成成功',U('Vipschool/VipschoolMarketing/studyCardList'));
			}else{
				$this->error('学习卡生成失败');
			}
		}else{
			$this->assign(get_defined_vars());
			$this->display();
		}

	}
	
	
	public function createStudyCard($arr){
		$cardCode = array();
		$prefix =  date('Ymd');
		if($arr['num'] > 0){
			$gsschoolModel = D('Vipschool');
			$start = $gsschoolModel->get_studyCardCount();
			for ($i=$start+1;$i<=$arr['num']+$start;$i++){
				$cardCode[$i]['code'] = $prefix.str_pad($i, 6,"0",STR_PAD_LEFT);
				$cardCode[$i]['pwd'] = $this->getRandChar(6);
			}
		}
		return $cardCode;
	}
	
	
	public function export_studyCardList(){
		$gsschoolModel = D('Vipschool');
		$dao = $gsschoolModel->dao;
		$condition = ' 1=1 ';
		if(!empty($_REQUEST['card_code'])){
			$condition .= ' AND c.card_code = '.$dao->quote(SysUtil::safeSearch_vip(urldecode($_REQUEST['card_code'])));
		}
		if(!empty($_REQUEST['type'])){
			if($_REQUEST['type'] == '1'){
				$condition .= ' AND c.use_time is not NULL ';
			}else{
				$condition .= ' AND c.use_time is NULL ';
			}
		}
		if(!empty($_REQUEST['username'])){
			$condition .= ' AND u.username = '.$dao->quote(trim($_REQUEST['username']));
		}
		$studyCardList = $gsschoolModel->get_studyCardAll($condition);
		
		import("ORG.Util.Excel");
		$exceler = new Excel_Export();
		$preFilename = mb_convert_encoding('学习卡统计表','gbk','utf8');
		$exceler->setFileName($preFilename.date('Y-m-d').'.csv');
		$excel_title= array('学习卡号','密码','指定课程', '失效日期','限定时间(天)','生成时间','使用者', '使用时间');
		foreach ($excel_title as $key=>$title){
			$excel_title[$key] = mb_convert_encoding($title,'gbk','utf8');
		}
		$exceler->setTitle($excel_title);
		foreach ($studyCardList as $key=>$val){
			$tmp_data= array($val['card_code'],$val['card_pwd'],mb_convert_encoding($val['course_name'],'gbk','utf8'),$val['endtime'],$val['limit_day'],$val['instime'],mb_convert_encoding($val['username'],'gbk','utf8'),$val['use_time']);
			$exceler->addRow($tmp_data);
		}
		$exceler->export();
	}
	
	
	/*充值卡管理*/
	public function rechargeCardList(){
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$condition = ' 1=1 ';
		$gsschoolModel = D('Vipschool');
		$dao = $gsschoolModel->dao;
		if($_REQUEST['card_code']!=''){
			$condition .= ' AND c.card_code = '.$dao->quote(SysUtil::safeSearch_vip(urldecode($_REQUEST['card_code'])));
		}
		if(!empty($_REQUEST['type'])){
			if($_REQUEST['type'] == '1'){
				$condition .= ' AND c.use_time is not NULL ';
			}else{
				$condition .= ' AND c.use_time is NULL ';
			}
		}
		$rechargeCardList = $gsschoolModel->get_rechargeCardList($condition,$curPage,$pagesize);
		$count = $gsschoolModel->get_rechargeCardCount($condition);
		$page = new page($count,$pagesize);
		$showPage = $page->show();
		$card_code = $_REQUEST['card_code'];
		$type = $_REQUEST['type'];
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	
	/*生成充值卡*/
	public function addRechargeCard(){
		if($_POST){
			$gsschoolModel = D('Vipschool');
			$rechargeCardList = $this->createRechargeCard($_POST);
			$result = $gsschoolModel->add_rechargeCard($rechargeCardList,$_POST);
			if($result){
				$this->success('充值卡生成成功',U('Vipschool/VipschoolMarketing/rechargeCardList'));
			}else{
				$this->error('充值卡生成失败');
			}
		}else{
			$this->assign(get_defined_vars());
			$this->display();
		}
	}
	
	
	public function createRechargeCard($arr){
		$rechargeCode = array();
		$prefix =  date('Ymd');
		if($arr['num'] > 0){
			$gsschoolModel = D('Vipschool');
			$start = $gsschoolModel->get_rechargeCardCount('');
			for ($i=($start+1);$i<=$arr['num']+$start;$i++){
				$rechargeCode[$i]['code'] = $prefix.str_pad($i, 6,"0",STR_PAD_LEFT);
				$rechargeCode[$i]['pwd'] = $this->getRandChar(6);
			}
		}
		return $rechargeCode;
	}
	
	
	public function export_rechargeCardList(){
		$gsschoolModel = D('Vipschool');
		$dao = $gsschoolModel->dao;
		$condition = ' 1=1 ';
		if(!empty($_REQUEST['card_code'])){
			$condition .= ' AND c.card_code = '.$dao->quote(SysUtil::safeSearch_vip(urldecode($_REQUEST['card_code'])));
		}
		if(!empty($_REQUEST['type'])){
			if($_REQUEST['type'] == '1'){
				$condition .= ' AND c.use_time is not NULL ';
			}else{
				$condition .= ' AND c.use_time is NULL ';
			}
		}
		$rechargeCardList = $gsschoolModel->get_rechargeCardAll($condition);
		
		import("ORG.Util.Excel");
		$exceler = new Excel_Export();
		$preFilename = mb_convert_encoding('充值卡统计表','gbk','utf8');
		$exceler->setFileName($preFilename.date('Y-m-d').'.csv');
		$excel_title= array('充值卡号','密码','充值金额（元）', '失效日期','生成时间','使用者', '使用时间');
		foreach ($excel_title as $key=>$title){
			$excel_title[$key] = mb_convert_encoding($title,'gbk','utf8');
		}
		$exceler->setTitle($excel_title);
		foreach ($rechargeCardList as $key=>$val){
			$tmp_data= array($val['card_code'],$val['card_pwd'],$val['money'],$val['endtime'],$val['instime'],mb_convert_encoding($val['username'],'gbk','utf8'),$val['use_time']);
			$exceler->addRow($tmp_data);
		}
		$exceler->export();
	}
	
	
	public function expressList(){
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$condition = ' 1=1 ';
		$gsschoolModel = D('Vipschool');
		$dao = $gsschoolModel->dao;
		if(!empty($_REQUEST['starttime'])){
			$condition .= ' AND o.paytime >= '.$dao->quote($_REQUEST['starttime']);
		}
		if(!empty($_REQUEST['endtime'])){
			$condition .= ' AND o.paytime <= '.$dao->quote(date('Y-m-d H:i:s',strtotime($_REQUEST['endtime'])+(3600*24-1)));
		}
		if(!empty($_REQUEST['type'])){
			if($_REQUEST['type'] == '1'){
				$condition .= ' AND o.status = 1 ';
			}else{
				$condition .= ' AND o.status = 0 ';
			}
		}
		$expressList = $gsschoolModel->get_expressList($condition,$curPage,$pagesize);
		$count = $gsschoolModel->get_expressCount($condition);
		$page = new page($count,$pagesize);
		$showPage = $page->show();
		$starttime = $_REQUEST['starttime'];
		$endtime = $_REQUEST['endtime'];
		$type = $_REQUEST['type'];
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	
	public function sendExpress(){
		$eid = abs($_GET['eid']);
		$gsschoolModel = D('Vipschool');
		$status = 0;
		if($gsschoolModel->send_express($eid,$_POST)){
			$status = 1;
		}
		echo json_encode(array('status'=>$status));
	}
	
	
	public function expressInfo(){
		$gsschoolModel = D('Vipschool');
		$expressInfo = $gsschoolModel->get_expressInfo($_GET['eid']);
		$this->assign(get_defined_vars());
		$this->display();
	}

	
	public function export_expressList(){
		$gsschoolModel = D('Vipschool');
		$dao = $gsschoolModel->dao;
		$condition = ' 1=1 ';
		if(!empty($_REQUEST['starttime'])){
			$condition .= ' AND o.paytime >= '.$dao->quote($_REQUEST['starttime']);
		}
		if(!empty($_REQUEST['endtime'])){
			$condition .= ' AND o.paytime <= '.$dao->quote(date('Y-m-d H:i:s',strtotime($_REQUEST['endtime'])+(3600*24-1)));
		}
		if(!empty($_REQUEST['type'])){
			if($_REQUEST['type'] == '1'){
				$condition .= ' AND o.status = 1 ';
			}else{
				$condition .= ' AND o.status = 0 ';
			}
		}
		$expressList = $gsschoolModel->get_expressAll($condition);
		
		import("ORG.Util.Excel");
		$exceler = new Excel_Export();
		$preFilename = mb_convert_encoding('快递统计表','gbk','utf8');
		$exceler->setFileName($preFilename.date('Y-m-d').'.csv');
		$excel_title= array('订单编号','付款时间', '购买者','教材','收件人', '收件地址', '邮编', '联系方式');
		foreach ($excel_title as $key=>$title){
			$excel_title[$key] = mb_convert_encoding($title,'gbk','utf8');
		}
		$exceler->setTitle($excel_title);
		foreach ($expressList as $key=>$val){
			$tmp_data= array($val['order_number'],$val['paytime'],mb_convert_encoding($val['username'],'gbk','utf8'),mb_convert_encoding($val['textbook'],'gbk','utf8'),mb_convert_encoding($val['real_name'],'gbk','utf8'),mb_convert_encoding($val['address'],'gbk','utf8'),$val['postcode'],$val['phone']);
			$exceler->addRow($tmp_data);
		}
		$exceler->export();
	}
	
	
	public function orderList(){
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$condition = ' 1=1 ';
		$gsschoolModel = D('Vipschool');
		$dao = $gsschoolModel->dao;
		if($_REQUEST['order_number']!=''){
			$condition .= " AND o.order_number = '".trim($_REQUEST['order_number'])."' ";
		}
		if(!empty($_REQUEST['starttime'])){
			$condition .= ' AND o.instime >= '.$dao->quote(trim($_REQUEST['starttime']));
		}
		if(!empty($_REQUEST['endtime'])){
			$condition .= ' AND o.instime <= '.$dao->quote(date('Y-m-d H:i:s',strtotime(trim($_REQUEST['endtime']))+(3600*24-1)));
		}
		if(!empty($_REQUEST['type'])){
			if($_REQUEST['type'] == '1'){
				$condition .= ' AND o.status = 1 ';//已付款
			}else if($_REQUEST['type'] == '2'){
				$condition .= ' AND o.status = 0 ';//未付款
			}else{
				$condition .= ' AND o.status = 2 ';//已退款
			}
		}
		if(!empty($_REQUEST['username'])){
			$condition .= ' AND u.username = '.$dao->quote(trim($_REQUEST['username']));
		}
		$orderList = $gsschoolModel->get_orderList($condition,$curPage,$pagesize);
		$count = $gsschoolModel->get_orderCount($condition);
		$page = new page($count,$pagesize);
		$showPage = $page->show();
		$order_number = trim($_REQUEST['order_number']);
		$starttime = trim($_REQUEST['starttime']);
		$endtime = trim($_REQUEST['endtime']);
		$type = $_REQUEST['type'];
		$username = trim($_REQUEST['username']);
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	
	public function orderInfo(){
		$gsschoolModel = D('Vipschool');
		$orderInfo = $gsschoolModel->get_orderInfo($_GET['oid']);
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	
	public function export_orderList(){
		$gsschoolModel = D('Vipschool');
		$dao = $gsschoolModel->dao;
		$condition = ' 1=1 ';
		if(!empty($_REQUEST['starttime'])){
			$condition .= ' AND o.instime >= '.$dao->quote(trim($_REQUEST['starttime']));
		}
		if(!empty($_REQUEST['endtime'])){
			$condition .= ' AND o.instime <= '.$dao->quote(date('Y-m-d H:i:s',strtotime(trim($_REQUEST['endtime']))+(3600*24-1)));
		}
		if(!empty($_REQUEST['type'])){
			if($_REQUEST['type'] == '1'){
				$condition .= ' AND o.status = 1 ';//已付款
			}else if($_REQUEST['type'] == '2'){
				$condition .= ' AND o.status = 0 ';//未付款
			}else{
				$condition .= ' AND o.status = 2 ';//已退款
			}
		}
		if(!empty($_REQUEST['username'])){
			$condition .= ' AND u.username = '.$dao->quote(trim($_REQUEST['username']));
		}
		$orderList = $gsschoolModel->get_orderAll($condition);
		
		import("ORG.Util.Excel");
		$exceler = new Excel_Export();
		$preFilename = mb_convert_encoding('订单统计表','gbk','utf8');
		$exceler->setFileName($preFilename.date('Y-m-d').'.csv');
		$excel_title= array('订单编号','下单时间', '总计金额（元）','支付方式','订单内容', '购买者', '订单状态');
		foreach ($excel_title as $key=>$title){
			$excel_title[$key] = mb_convert_encoding($title,'gbk','utf8');
		}
		$exceler->setTitle($excel_title);
		foreach ($orderList as $key=>$val){
			if($order['real_amount']==$order['total_amount']){
				$paytype = '在线付款';
			}else if($order['real_amount']==0){
				$paytype = '学习卡';
			}else{
				$paytype = '学习卡+在线付款';
			}
			if($order['status']==1){
				$status = '已付款';
			}else if($order['status']==2){
				$status = '已退款';
			}else{
				$status = '未付款';
			}
			$tmp_data= array(mb_convert_encoding($val['order_number'],'gbk','utf8'),$val['instime'],$val['real_amount'],mb_convert_encoding($paytype,'gbk','utf8'),mb_convert_encoding($val['order_content'],'gbk','utf8'),mb_convert_encoding($val['username'],'gbk','utf8'),mb_convert_encoding($status,'gbk','utf8'));
			$exceler->addRow($tmp_data);
		}
		$exceler->export();
	}
	
	
	public function emailManage(){
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$condition = ' 1=1 ';
		$gsschoolModel = D('Vipschool');
		$dao = $gsschoolModel->dao;
		$emailList = $gsschoolModel->get_emailList($condition,$curPage,$pagesize);
		$count = $gsschoolModel->get_emailCount($condition);
		$page = new page($count,$pagesize);
		$showPage = $page->show();
		
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	
	public function addEmail(){
		$status = 0;
		$msg = '通知邮件地址添加失败';
		$email = trim($_POST['email']);
		if(D('Vipschool')->addEmail($email)){
			$status = 1;
			$msg = '通知邮件地址添加成功';
		}
		echo json_encode(array('status'=>$status,'msg'=>$msg));
	}
	
	
	public function deleteEmail(){
		$eid = abs($_GET['eid']);
		if(D('Vipschool')->deleteEmail($eid)){
			$this->success('通知邮件删除成功');
		}else{
			$this->error('通知邮件删除失败');
		}
	}

}
?>