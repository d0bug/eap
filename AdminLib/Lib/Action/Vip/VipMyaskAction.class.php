<?php
/*后台VIP老师系统---我的提问管理*/
class VipMyaskAction extends VipCommAction{

	/*提问管理*/
	public function myask(){
		$vipmyask = D('Vipmyask');
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$condition = '';
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] :'';
		if(!empty($keyword)){
			$condition .= " AND title like  '%".$keyword."%'";
		}
		$vipmyaskList = $vipmyask->get_myaskList($condition,$curPage,$pagesize);
		$count = $vipmyask->get_myaskCount($condition);
		$page = new Page($count,$pagesize);
		$showPage = $page->show();
		$this->assign(get_defined_vars());
		$this->display('myask');
	}

	//获取问题详细信息
	public function vipaskInfo($id){
		if(!empty($_GET['id'])){
			$vipmyask = D('Vipmyask');
			$askInfo = $vipmyask->get_askInfo($_GET['id']);
		}
		$this->assign(get_defined_vars());
		$this->display();
	}


	//回复
	public function vipaskreply(){
		$askid = isset($_GET['id']) ? $_GET['id'] :'';
		if(!empty($askid)){
			import("ORG.Util.Page");
			$curPage = isset($_GET['p'])?abs($_GET['p']):1;
			$pagesize = C('PAGESIZE');
			$condition = '';
			$condition = " AND askid = '$askid' ";
			$replyList = D('Vipmyask');
			$one_askinfo = $replyList->get_oneInfo($askid);
			$replyListInfo = $replyList->get_reply($condition,$curPage,$pagesize);
			$count = $replyList->get_myreplyCount($condition);
			$page = new Page($count,$pagesize);
			$showPage = $page->show();
		}
		$this->assign(get_defined_vars());
		$this->display();
	}

	//添加回复内容
	public function replycontent(){
		$content = $_POST['content'];
		$askid = $_POST['askid'];
		$formParam = $_post['formParam'];
		$userInfo = VipCommAction::get_currentUserInfo();
		$data = array();
		if(!empty($content) && !empty($askid)){
			$reply = D('Vipmyask');
			$uname = $userInfo['user_name'];
			$uid = $userInfo['user_key'];
			$replycontent = $reply->insert_reply($askid,$content,$uid,$uname);
			if($replycontent){
				$askstatus = $reply->update_askstatus($askid);
				$data['status'] = 1;
				$data['msg'] = '回复成功！';
			}else{
				$data['status'] = 0;
				$data['msg'] = '回复失败！';
			}
		}
		echo json_encode($data);
	}

	public function setMyaskStatus(){
		$id = isset($_POST['askid'])?intval($_POST['askid']):0;
		$status = isset($_POST['status'])?intval($_POST['status']):0;
		if(!empty($id)){
			$Vipmyask = D('Vipmyask');
			$st = $Vipmyask->setMyaskStatus($id,$status);
			if($st){
				$this->success('审核成功');
			}
			else{
				$this->success('审核失败！');
			}
		}else{
			$this->success('审核失败！');
		}
	}


	//回复修改
	public function replyOperate(){
		if(!empty($_GET['id'])){
			$Vipmyask = D('Vipmyask');
			$replyInfo = $Vipmyask->get_OneReply($_GET['id']);
		}
		//print_r($replyInfo);
		$this->assign(get_defined_vars());
		$this->display();
	}



	//对圈子进行添加或编辑
	public function DoreplyOperate(){

		if($_POST){
			$arr = $_POST;
			$operate = '' ;
			
			$userInfo = VipCommAction::get_currentUserInfo();
			$arr['uname'] = $userInfo['user_name'];
			$arr['uid'] = $userInfo['user_key'];
			//print_r($arr);exit;
			$MyaskModel = D('Vipmyask');

			if($_POST['id']){
					$result = $MyaskModel->update_reply($arr);
					$operate = '编辑';
				}
				if($result==true){
					$this->success($operate.'成功');
				}else{
					$this->error($operate.'失败');
				}

		}else{
			$this->error('非法操作');
		}
	}


	//对回复内容进行删除
	public function DelReply(){

		$rid = $_POST['rid'];
		$askid = $_POST['askid'];
		if(!empty($rid)){
			$Vipmyask = D('Vipmyask');
			$status = $Vipmyask->Del_Reply($rid);
			if($status){
				$rep = $Vipmyask->get_MyRep($askid);
				if(empty($rep)){
					$Vipmyask->update_replystatus($askid);
				}
	
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

	//我的回答列表
	public function AskReplyList(){
		$userInfo = VipCommAction::get_currentUserInfo();
		$uid = $userInfo['user_key'];
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$condition = ' AND r.reply_uid ='."'".$uid."'";
		$Vipmyask = D('Vipmyask');
		$myReplyInfo = $Vipmyask->MyAskReplyInfo($condition,$curPage,$pagesize);
		$count = $Vipmyask->get_MyAskReplyCount($condition);
		$page = new Page($count,$pagesize);
		$showPage = $page->show();
		$this->assign(get_defined_vars());
		$this->display();
	}

	public function setMyask(){
		$askid = isset($_GET['askid'])?intval($_GET['askid']):'';
		if(!empty($askid)){
			$Vipmyask = D('Vipmyask');
			$AskInfo = $Vipmyask->get_askInfo($askid);
		}

		$this->assign(get_defined_vars());
		$this->display();
	}
}
?>