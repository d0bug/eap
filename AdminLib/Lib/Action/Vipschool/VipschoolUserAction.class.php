<?php
/*网校-账号管理*/
class VipschoolUserAction extends VipschoolCommAction{
	protected function notNeedLogin() {
		return array('');
	}


	/*用户列表*/
	public function userList(){
		$userKey = $this->loginUser->getUserKey();
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$condition = ' 1=1 ';
		$gsschoolModel = D('Vipschool');
		$dao = $gsschoolModel->dao;
		if($_REQUEST['username']!=''){
			$condition .= ' AND username = '.$dao->quote(SysUtil::safeSearch_vip(urldecode(trim($_REQUEST['username']))));
		}
		if($_REQUEST['student_name']!=''){
			$condition .= ' AND student_name = '.$dao->quote(SysUtil::safeSearch_vip(urldecode(trim($_REQUEST['student_name']))));
		}
		if($_REQUEST['phone']!=''){
			$condition .= ' AND phone = '.$dao->quote(SysUtil::safeSearch_vip(trim($_REQUEST['phone'])));
		}
		$userList = $gsschoolModel->get_userList($condition,$curPage,$pagesize);
		$count = $gsschoolModel->get_userCount($condition);
		$page = new page($count,$pagesize);
		$showPage = $page->show();
		$username = trim($_REQUEST['username']);
		$student_name = trim($_REQUEST['student_name']);
		$phone = trim($_REQUEST['phone']);
		$this->assign(get_defined_vars());
		$this->display('userList');
	}


	public function userInfo(){
		$userInfo = D('Vipschool')->get_userInfo(array('uid'=>$_GET['uid']));
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function openCourse(){
		$userKey = $this->loginUser->getUserKey();
		if($_POST){
			$gsschoolModel = D('Vipschool');
			$courseInfo = $gsschoolModel->get_course_by_id($_POST['course_id']);
			if(empty($courseInfo)){
				$this->error('课程不存在',U('Vipschool/VipschoolUser/openCourse'));
			}
			$studentInfo = $gsschoolModel->get_userInfo($_POST['uid']);
			if(empty($studentInfo)){
				$this->error('学员不存在',U('Vipschool/VipschoolUser/openCourse'));
			}
			if($gsschoolModel->openCourse($_POST,$userKey)){
				$this->success('课程开通成功',U('Vipschool/VipschoolUser/openCourse'));
			}else{
				$this->error('课程开通失败',U('Vipschool/VipschoolUser/openCourse'));
			}
		}else{
			$this->assign(get_defined_vars());
			$this->display();
		}
	}

	/*导入历史学员*/
	public function importUser(){
		$userKey = $this->loginUser->getUserKey();
		if($userKey == 'Employee-xiecuiping'){
			$file = APP_DIR.'/SchoolRoot/user2.csv';
			if(file_exists($file)){
				import("ORG.Util.Csv");
				$csv = new CSV();
				$data = $csv->loadToArray($file);
				if(!empty($data)){
					unset($data[0]);
					$gsschoolModel = D('Vipschool');
					foreach ($data as $key=>$row){
						$result = $gsschoolModel->importUser($row);
						if(!$result){
							echo $key.'__'.$row[$key][0].' error <br>';
						}
					}
				}
			}else{
				echo '不存在';die;
			}
		}else{
			echo '非法操作';die;
		}
	}


	/*导入学员账户金额*/
	public function importAccount(){
		$userKey = $this->loginUser->getUserKey();
		if($userKey == 'Employee-xiecuiping'){
			$file = APP_DIR.'/SchoolRoot/account2.csv';
			if(file_exists($file)){
				import("ORG.Util.Csv");
				$csv = new CSV();
				$data = $csv->loadToArray($file);
				if(!empty($data)){
					unset($data[0]);
					$gsschoolModel = D('Vipschool');
					foreach ($data as $key=>$row){
						if($row[3] != 0){
							$result = $gsschoolModel->importAccount($row);
							if(!$result){
								echo $key.'__'.$data[$key][0].' error <br>';
							}
						}
					}
				}
			}else{
				echo '不存在';die;
			}
		}else{
			echo '非法操作';die;
		}
	}



	/*导入课程名称、价格、发布时间*/
	public function importCourse(){
		$userKey = $this->loginUser->getUserKey();
		if($userKey == 'Employee-xiecuiping'){
			$file = APP_DIR.'/SchoolRoot/course.csv';
			if(file_exists($file)){
				import("ORG.Util.Csv");
				$csv = new CSV();
				$data = $csv->loadToArray($file);
				if(!empty($data)){
					unset($data[0]);
					$gsschoolModel = D('Vipschool');
					foreach ($data as $key=>$row){
						$arr = array();
						$arr['course_name'] = mb_convert_encoding($row[0],'utf8','gbk');
						$arr['price'] = mb_convert_encoding($row[1],'utf8','gbk');
						$arr['instime'] = date('Y-m-d H:i:s',strtotime($row[2]));
						$result = $gsschoolModel->importCourse($arr);
						if(!$result){
							echo $key.'__'.$arr['course_name'].' error <br>';
						}
					}

				}
			}else{
				echo '不存在';die;
			}
		}else{
			echo '非法操作';die;
		}
	}


	/*导入教师信息*/
	public function importTeacher(){
		$userKey = $this->loginUser->getUserKey();
		if($userKey == 'Employee-xiecuiping'){
			$file = APP_DIR.'/SchoolRoot/teacher.csv';
			if(file_exists($file)){
				import("ORG.Util.Csv");
				$csv = new CSV();
				$data = $csv->loadToArray($file);
				if(!empty($data)){
					unset($data[0]);
					$gsschoolModel = D('Vipschool');
					foreach ($data as $key=>$row){
						$arr = array();
						if(!empty($row[0])){
							$arr['realname'] = mb_convert_encoding($row[0],'utf8','gbk');
							$arr['grade'] = mb_convert_encoding($row[1],'utf8','gbk');
							$arr['subject'] = mb_convert_encoding($row[2],'utf8','gbk');
							$arr['send_word'] = mb_convert_encoding($row[3],'utf8','gbk');
							$arr['of_educate_age'] = $row[4];
							$arr['intro_content'] = mb_convert_encoding($row[5],'utf8','gbk');
							$arr['teaching_style'] = mb_convert_encoding($row[6],'utf8','gbk');
							$arr['experience_content'] = mb_convert_encoding($row[7],'utf8','gbk');
							$arr['comment'] = mb_convert_encoding($row[8],'utf8','gbk');
							$arr['instime'] = date('Y-m-d H:i:s');
							$result = $gsschoolModel->importTeacher($arr);
							if(!$result){
								echo $key.'__'.$arr['teacher_name'].' error <br>';
							}
						}
					}
				}
			}else{
				echo '不存在';die;
			}
		}else{
			echo '非法操作';die;
		}
	}


}
?>