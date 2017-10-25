<?php
/**
 * 语文作业管理
 *
 */
class TestAction extends AppCommAction{

	public function index() {

		$this->getList();
	}

	private function getList() {
		$i = 1;
		if(!empty($_GET['sAliasCode'])) {
			 $sAliasCode = trim($_GET['sAliasCode']);
		} else {
			$i = 0;
		}
		if(!empty($_GET['nType'])) {
			$nType = (int)$_GET['nType'];
		} else {
			$i = 0;
		}
		$types = array(
			0=>'题型',


			1=>'选择题',
			2=>'主观题',
			3=>'填空题'
			);

		if(empty($i)) {
			$list = array();
		} else {
			$model = D('Task');
			$list = $model->getTestData($sAliasCode,$nType);
			//print_r($list);exit();
		}
		$this->assign(get_defined_vars());
		$this->display('getList');


	}
	public function ajax_delete() {
		if(!empty($_GET['sAliasCode'])) {
			 $sAliasCode = trim($_GET['sAliasCode']);
		} else {
			echo 0;exit();
		}
		if(!empty($_GET['nType'])) {
			$nType = (int)$_GET['nType'];
		} else {
			echo -1;exit();
		}
		if(!empty($_GET['nQuestionid'])) {
			$nQuestionid = (int)$_GET['nQuestionid'];
		} else {
			echo -2;exit();
		}
		$model = D('Task');
		echo $model->getTestData($nQuestionid,$sAliasCode,$nType);


	}





	protected function notNeedLogin(){
		return array('UPLOADER');
	}

}

