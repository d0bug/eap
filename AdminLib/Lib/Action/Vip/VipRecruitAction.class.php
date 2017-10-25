<?php
import('ORG.Util.Overtrue.WeixinAuth');
implode('ORG.Util.Cookie');
/*VIP教师招聘简历投递（公众号）*/
class VipRecruitAction extends VipCommAction{
	protected function notNeedLogin() {
		return array(
		'VIP-VIPRECRUIT-SENDCV',
		'VIP-VIPRECRUIT-JOBLIST',
		'VIP-VIPRECRUIT-JOBINFO',
		'VIP-VIPRECRUIT-MYCV',
		'VIP-VIPRECRUIT-APPLYJOB',
		'VIP-VIPRECRUIT-SEARCHUNIVERSITY'
		);
	}


	//获取当前微信用户基本信息
	public function getWeixinUserInfo(){
		$userInfo = Cookie::get('userInfo');
		if(!$userInfo){
			if(false == $scope) {
				$scope = 'snsapi_userinfo';
			}
			$auth = new WeixinAuth(C('appID'), C('appsecret'));
			$auth->authorize(null, $scope);
			$userInfo = $auth->user()->toArray();
			if($userInfo){
				Cookie::set('userInfo',json_encode($userInfo),'3600*24');
			}
			$recruitModel = D('VpRecruit');
			$recruitModel->bindUser($userInfo);
		}else{
			$userInfo = json_decode($userInfo,true);
		}
		return $userInfo;
	}



	//填写简历
	public function sendCV(){
		$userInfo = $this->getWeixinUserInfo();
		$recruitModel = D('VpRecruit');
		if($_POST){
			$status = 0;//print_r($_POST);die;
			$result = $recruitModel->insert_recruit($_POST,$userInfo);
			if($result){
				$status = 1;
			}
			echo json_encode(array('status'=>$status));
		}else{
			$recruitmentInfo = $recruitModel->get_recruitmentInfo($userInfo);
			if($recruitmentInfo){
				switch ($recruitmentInfo['neducation']){
					case 1://大专
					case 2://本科
					$recruitmentInfo['school'] = $recruitmentInfo['sundergraduate'] ;
					$recruitmentInfo['major'] = $recruitmentInfo['sunderobject'];
					break;
					case 3://硕士
					$recruitmentInfo['school'] = $recruitmentInfo['smaster'] ;
					$recruitmentInfo['major'] = $recruitmentInfo['smasterobject'] ;
					break;
					case 4://博士
					$recruitmentInfo['school'] = $recruitmentInfo['sdoctor'];
					$recruitmentInfo['major'] = $recruitmentInfo['sdoctorobject'];
					break;
					default://其他
					$recruitmentInfo['school'] = $recruitmentInfo['sundergraduate'];
					$recruitmentInfo['major'] = $recruitmentInfo['sunderobject'];
				}
			}
			$generalList = $recruitModel->get_general();
			$universityList = $recruitModel->get_university();
			$postType = $recruitModel->get_postType();
			$subjectList = $recruitModel->get_subjectList();
			$this->assign(get_defined_vars());
			$this->display();
		}

	}


	//职位列表
	public function jobList(){
		$recruitModel = D('VpRecruit');
		$jobList = $recruitModel->get_jobList();
		$userInfo = $this->getWeixinUserInfo();
		$recruitmentInfo = $recruitModel->get_recruitmentInfo($userInfo);
		$this->assign(get_defined_vars());
		$this->display();
	}


	//职位介绍
	public function jobInfo(){
		$jobId = $_GET['id'];
		$userInfo = $this->getWeixinUserInfo();
		$recruitModel = D('VpRecruit');
		$jobInfo = $recruitModel->get_jobInfo($jobId);
		$recruitmentInfo = $recruitModel->get_recruitmentInfo($userInfo);
		$this->assign(get_defined_vars());
		$this->display();
	}


	//我的简历
	public function myCV(){
		$userInfo = $this->getWeixinUserInfo();
		$recruitModel = D('VpRecruit');
		$recruitmentInfo = $recruitModel->get_recruitmentInfo($userInfo);
		if($recruitmentInfo){
			switch ($recruitmentInfo['neducation']){
				case 1://大专
				case 2://本科
				$recruitmentInfo['school'] = $recruitmentInfo['sundergraduate'] ;
				$recruitmentInfo['major'] = $recruitmentInfo['sunderobject'];
				break;
				case 3://硕士
				$recruitmentInfo['school'] = $recruitmentInfo['smaster'] ;
				$recruitmentInfo['major'] = $recruitmentInfo['smasterobject'] ;
				break;
				case 4://博士
				$recruitmentInfo['school'] = $recruitmentInfo['sdoctor'];
				$recruitmentInfo['major'] = $recruitmentInfo['sdoctorobject'];
				break;
				default://其他
				$recruitmentInfo['school'] = $recruitmentInfo['sundergraduate'];
				$recruitmentInfo['major'] = $recruitmentInfo['sunderobject'];
			}
			$recruitmentInfo['schoolName'] = $recruitModel->get_universityNameById($recruitmentInfo['school']);
			$recruitmentInfo['educationName'] = $recruitModel->get_educationName($recruitmentInfo['neducation']);
			$recruitmentInfo['postTypeName'] = $recruitModel->get_postTypeName($recruitmentInfo['nposttype']);
		}
		$this->assign(get_defined_vars());
		$this->display();
	}


	//投递简历
	public function applyJob(){
		$status = 0;
		if(!empty($_POST['jobId']) && !empty($_POST['recruitmentId'])){
			$recruitModel = D('VpRecruit');
			if($recruitModel->apply_job($_POST['jobId'],$_POST['recruitmentId'])){
				$status = 1;
			}
		}
		echo json_encode(array('status'=>$status));
	}

	
	public function searchUniversity(){
		$recruitModel = D('VpRecruit');
		$universityList = $recruitModel->get_university($_GET['keyword']);
		$html = '';
		if($universityList){
			foreach ($universityList as $key=>$row){
				$html .= '<option value="'.$row['id'].'">'.$row['sname'].'</option>';
			}
		}
		echo $html;
	}



}

?>