<?php
/*后台VIP老师系统---我的圈子管理*/
class VipCircleAction extends VipCommAction{

	/*圈子管理*/
	public function VipCircleList(){
		$vipCircle = D('VipCircle');
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$condition = '';
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] :'';
		if(!empty($keyword)|| $keyword == 0){
			$condition .= " AND title like  '%".$keyword."%'";
		}
			
		$vipCircleList = $vipCircle->get_CircleList($condition,$curPage,$pagesize);
		$count = $vipCircle->get_CircleCount($condition);
		$page = new Page($count,$pagesize);
		$showPage = $page->show();
		$this->assign(get_defined_vars());
		$this->display('VipCircleList');
	}


	//圈子的添加或修改
	public function CircleOperate(){
		if(!empty($_GET['id'])){
			$CircleModel = D('VipCircle');
			$CircleInfo = $CircleModel->get_CircleInfo($_GET['id']);
		}
		$this->assign(get_defined_vars());
		$this->display();
	}

	//对圈子进行添加或编辑
	public function DoCircleOperate(){

		if($_POST){
			$arr = $_POST;
			$operate = '' ;
			$userInfo = VipCommAction::get_currentUserInfo();;
			$arr['username'] = $userInfo['user_name'];
			$arr['uid'] = $userInfo['user_key'];
			$arr['status'] = isset($arr['is_status']) ?  $arr['is_status'] :0;
			$arr['is_recommend'] = isset($arr['is_recommend']) ?  $arr['is_recommend'] :0;
			//print_r($arr);exit;
			$CircleModel = D('VipCircle');
			if($_POST['id']){
					$result = $CircleModel->editCircle($arr);
					$operate = '编辑';
				}else{
					$result = $CircleModel->addCircle($arr);
					$operate = '添加';
				}

				if($result==true){
					$this->success('圈子'.$operate.'成功');
				}else{
					$this->error('圈子'.$operate.'失败');
				}

		}else{
			$this->error('非法操作');
		}
	}

	//获取圈子详细信息
	public function vipCircleInfo($id){
		if(!empty($_GET['id'])){
			$vipCircle = D('VipCircle');
			$CircleInfo = $vipCircle->get_CircleInfo($_GET['id']);
		}
		$this->assign(get_defined_vars());
		$this->display();
	}


	//回复
	public function vipCircleReply(){
		$cid = isset($_GET['cid']) ? $_GET['cid'] :'';
		if(!empty($cid)){
			import("ORG.Util.Page");
			$curPage = isset($_GET['p'])?abs($_GET['p']):1;
			$pagesize = C('PAGESIZE');
			$condition = '';
			$condition = " AND circle_id = '$cid' ";
			$VipCircle = D('VipCircle');
			$one_Circleinfo = $VipCircle->get_oneCircle($cid);
			$ReplyCircleList = $VipCircle->get_comment($condition,$curPage,$pagesize);
			$count = $VipCircle->get_commentCount($condition);
			$page = new Page($count,$pagesize);
			$showPage = $page->show();
		}
		$this->assign(get_defined_vars());
		$this->display();
	}

	//添加回复内容
	public function Comment_content(){
		$content = $_POST['content'];
		$content = trim(strip_tags($content));
		$cid = $_POST['cid'];
		$formParam = $_post['formParam'];
		$userInfo = VipCommAction::get_currentUserInfo();
		$data = array();
		if(!empty($content) && !empty($cid)){
			$comment = D('VipCircle');
			$uname = $userInfo['user_name'];
			$uid = $userInfo['user_key'];
			$replycontent = $comment->insert_comment($cid,$content,$uid,$uname);
			if($replycontent){
				$askstatus = $comment->update_Circle($cid);
				$data['status'] = 1;
				$data['msg'] = '回复成功！';
			}else{
				$data['status'] = 0;
				$data['msg'] = '回复失败！';
			}
		}
		echo json_encode($data);
	}

	//对圈子进行删除
	public function DelCircle(){

		$cid = $_POST['cid'];
		if(!empty($cid)){
			$VipCircle = D('VipCircle');
			$status = $VipCircle->Del_circle($cid);
			if($status){
				$data['status'] = 1;
				$data['msg'] = '删除成功！';
			}else{
				$data['status'] = 0;
				$data['msg'] = '删除失败！';
			}
		}else{
				$data['status'] = 2;
				$data['msg'] = '失败！参数导常';
		}
			echo json_encode($data); 
	}

	public function setCircle(){
		$cid = isset($_GET['cid'])?intval($_GET['cid']):'';
		if(!empty($cid)){
			$vipCircle = D('VipCircle');
			$CircleInfo = $vipCircle->get_CircleInfo($cid);
		}

		$this->assign(get_defined_vars());
		$this->display();
	}

	public function DosetCircle(){
		$cid = isset($_POST['cid'])?intval($_POST['cid']):'';
		$status = isset($_POST['is_status'])?intval($_POST['is_status']):0;
		$is_top = isset($_POST['is_top'])?intval($_POST['is_top']):0;
		//echo $cid;exit;
		$is_recommend = isset($_POST['is_recommend'])?intval($_POST['is_recommend']):0;
		if(!empty($cid)){
			$VipCircle = D('VipCircle');
			$st = $VipCircle->setMyCircleStatus($cid,$status,$is_top,$is_recommend);
			if($st){
				$this->success('更新成功！');
				//$this->redirect('/Vip/VipCircle/VipCircList');
			}
			else{
				$this->success('更新失败！');
			}
		}else{
			$this->success('更新失败！没有获取到参数');
		}
	}

	//对圈子评论进行删除
	public function DelCiRply(){
		$rid = $_POST['rid'];
		if(!empty($rid)){
			$VipCircle = D('VipCircle');
			$status = $VipCircle->Del_circlereply($rid);
			if($status){
				$data['status'] = 1;
				$data['msg'] = '删除成功！';
			}else{
				$data['status'] = 0;
				$data['msg'] = '删除失败！';
			}
		}else{
				$data['status'] = 2;
				$data['msg'] = '失败！参数导常';
		}
			echo json_encode($data); 
	}


	/*投拆管理*/
	public function VipComplaintList(){
		$vipCircle = D('VipCircle');
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$condition = '';
		$vipComplaintList = $vipCircle->get_ComplaintList($condition,$curPage,$pagesize);
		$count = $vipCircle->get_ComplaintCount($condition);
		$page = new Page($count,$pagesize);
		$showPage = $page->show();
		$this->assign(get_defined_vars());
		$this->display('VipComplaintList');
	}
    
    //============ sofia
    
	//回复投诉
	public function vipComplainReply(){
		$cid = isset($_GET['cid']) ? $_GET['cid'] :'';
        
		if(!empty($cid)){
			import("ORG.Util.Page");
			$curPage = isset($_GET['p'])?abs($_GET['p']):1;
			$pagesize = C('PAGESIZE');
			$condition = '';
			$condition = " AND complain_id = '$cid' ";
			$VipCircle = D('VipCircle');
			$one_Circleinfo = $VipCircle->get_oneComplain($cid);
			$ReplyCircleList = $VipCircle->get_Complain_comment($condition,$curPage,$pagesize);
			$count = $VipCircle->get_ComplainCount($condition);
			$page = new Page($count,$pagesize);
			$showPage = $page->show();
		}
		$this->assign(get_defined_vars());
		$this->display('vipComplainReply');
	}
    //添加投诉回复内容
	public function Complain_content(){
		$content = $_POST['content'];
		$ucontent = trim(strip_tags($content));
		$cid = $_POST['cid'];
		$formParam = $_post['formParam'];
		$userInfo = VipCommAction::get_currentUserInfo();
		$data = array();
		if(!empty($ucontent) && !empty($cid)){
			$comment = D('VipCircle');
            $comuser = $comment->get_complainuser($cid);            
            $ccode =$comuser[0]['uid'];
            $cname =$comuser[0]['uname'];  
            $ccontent =$comuser[0]['content'];              
			$uname = $userInfo['real_name'];
			$uid = $userInfo['sCode'];
			$replycontent = $comment->insert_Complain_comment($cid,$ccode,$cname,$ccontent,$uid,$uname,$ucontent);
			if($replycontent){
				$askstatus = $comment->update_Complain($cid);
                $addpush = $comment->add_push_complain($cid);
				
				$data['status'] = 1;
				$data['msg'] = '回复成功！';
			}else{
				$data['status'] = 0;
				$data['msg'] = '回复失败！';
			}
		}
		echo json_encode($data);
	}
    
    
    
    
}
?>