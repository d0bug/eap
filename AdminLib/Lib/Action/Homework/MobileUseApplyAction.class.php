<?php
class MobileUseApplyAction extends HomeworkCommAction {
	private $userInfo;
	public function __construct(){
		parent::__construct();
		$this->model = new TestMobileModel();
		$this->userInfo = $this->loginUser->getInformation();
	}
	protected function notNeedLogin() {}
	
	public function apply(){
		if ($_POST){
			$_POST['startTime'] = strtotime($_POST['startTime']);
			$_POST['endTime'] = strtotime($_POST['endTime']);
			$_POST['mobile'] = intval($_POST['mobile']);
			$r = $this->model->addApply($_POST);
			if($r === true){
				$this->redirect('MobileUseApply/myApply');
			}
		}
		$mobileType = $this->model->getAllRecord('NCS_EAP_mobileType');
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	public function myApply(){
		$myApply = $this->model->getMyApply();
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	public function applyList(){
		$user = $this->model->getUser();
		if($user == 'zhangweili' || $user == 'wangzhilong'){
			
			import('ORG.Util.Page');
			$condition = '';
			if (isset($_GET['type'])){
				if ($_GET['type'] == 0){
					$condition = ' and markingStatus = 0';
				}elseif ($_GET['type'] == 1){
					$condition = ' and markingStatus = 1 and gobackStatus = 0';
				}
			}
			$count = $this->model->countQRI($condition);
			$Page = new Page($count,5);
			$show = $Page->show();
			$varPage = C ( 'VAR_PAGE' ) ? C ( 'VAR_PAGE' ) : 'p';
			$nowPage = ! empty ( $_GET [$varPage] ) ? intval ( $_GET [$varPage] ) : 1;
			$applyList = $this->model->getQRI($condition,$nowPage,5);
			
			$this->assign(get_defined_vars());
			$this->display();
		}else{
			$this->error('您没有权限喔！');
		}
	}
	public function mobileList(){
		$user = $this->model->getUser();
		if($user == 'zhangweili' || $user == 'wangzhilong'){
			$mobileList = $this->model->getMobileList();
			$this->assign(get_defined_vars());
			$this->display();
		}
	}
	public function ajaxUpdateMarking(){
		$user = $this->model->getUser();
		if($user == 'zhangweili' || $user == 'wangzhilong'){
			$id = intval($_POST['id']);
			$val = intval($_POST['val']);
			$r = $this->model->updateMarking($id,$val);
			echo json_encode($r);
		}
	}
	public function ajaxUpdateGoBack(){
		$user = $this->model->getUser();
		if($user == 'zhangweili' || $user == 'wangzhilong'){
			$id = intval($_POST['id']);
			$val = intval($_POST['val']);
			$r = $this->model->updateGoBack($id,$val);
			echo $r;
		}
	}
	public function ajaxUpdateSay(){
		$user = $this->model->getUser();
		if($user == 'zhangweili' || $user == 'wangzhilong'){
			$id = intval($_POST['id']);
			$val = SysUtil::safeString($_POST['say']);
			$this->model->updateRecord('NCS_EAP_applyList',array('weilisay'=>$val),array('id'=>$id));
		}
	}
}