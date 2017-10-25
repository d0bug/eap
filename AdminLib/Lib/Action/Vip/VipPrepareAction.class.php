<?php
/*教师备课*/
class VipPrepareAction extends VipCommAction{
	protected function notNeedLogin() {
		return array();
	}
	
	
	/*我的学员新版*/
	public function newStudents(){
		$key_name = isset($_GET['key_name'])?trim($_GET['key_name']):'';
		$order = isset($_GET['order'])?strtolower(trim($_GET['order'])):'asc';
		$userInfo = VipCommAction::get_currentUserInfo();
		if($userInfo['user_key'] == 'Employee-wangyan'){
			$userInfo['sCode'] = 'VP00022';
		}
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$newStudentsModel = D('VpNewStudents');
		$condition = '';
		$start = trim($_POST['start']);
		$end = trim($_POST['end']);
		$dept_code = $_POST['dept_code'];
		$student_name = urldecode($_POST['student_name']);
		if($userInfo['sCode']){
			$now = date('Y-m-d H:i:s');
			$conditionArr = array('teacherCode'=>$userInfo['sCode'],'key_name'=>$key_name,'order'=>$order,'now'=>$now,'start'=>$start,'end'=>$end,'dept_code'=>$dept_code,'student_name'=>$student_name);
			$myStudentList = $newStudentsModel->get_myStudentList($conditionArr,$curPage,$pagesize);
			$count = $newStudentsModel->get_myStudentCount($conditionArr);
			$page = new page($count,$pagesize);
			$showPage = $page->show();
		}else{
			echo '您不是VIP教师,没有相应学员';die;
		}

		$deptList = $newStudentsModel->get_deptList();

		$this->assign(get_defined_vars());
		$this->display();
	}
	
	
	
}

?>