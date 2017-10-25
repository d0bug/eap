<?php
class ApiAction extends StudentCommAction {
	public function __construct() {
		parent::__construct();

	}

	public function notNeedLogin() {
		$arr = array();
		$arr[] = strtoupper('student-api-index');
		return $arr;

	}
	public function index() {
		$json = array('error'=>1,'msg' => '获取学生信息失败');
		$data = array();
		if(empty($_GET['scode'])) {
			$json['msg'] = '学号为空';
		} else {
			$data['sStudentCode'] = urldecode($_GET['scode']);
		}
		if(empty($_GET['sname'])) {
			$json['msg'] = '姓名为空';
		} else {
			$data['sStudentName'] = urldecode($_GET['sname']);
		}
		if(empty($_GET['nclassyear'])) {
			$json['msg'] = '年代为空';
		} else {
			$data['nClassYear'] = (int)($_GET['nclassyear']);
		}





		$model = D('Student');

		$results = $model->getStudentInfo($data);
		echo json_encode($results);
		exit();
	}



}
?>
