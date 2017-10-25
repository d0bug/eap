<?php

class ApplyAction extends ReserveCommAction{

    public function index() {
		$this->getList();


	}
	private function getList() {
		$this->studentList();exit();
		isset($_GET['id']) && $id = (int)$_GET['id'];
		$page = (int)$_GET['page'];
		if(empty($page)) {
			$page = 1 ;
		}
		$id = 1;//临时测试使用
		if(empty($id)) {
			$this->error('must chose a active');
		}


		$data = array();
		$formInfoArr = array();
		$condition = array('list_id' =>$id);





		$results =  $this->reserveModel->getStudentsList($condition);
		foreach($results as $value) {
			$data[$value['stucode']][$value['class_id']] = $value['week_id'];
			$stuInfoArr[$value['stucode']] = $this->getUserInfo($value['stucode']);
		}
        $formFormatArr = $this->reserveModel->getReserveList($id);
		$results = $this->reserveModel->getReserveInfo($id);

		foreach($results as $value) {
			$formInfoArr[$value['class_id']][$value['week_id']] = $value;
		}
		//dump($formInfoArr);exit;

		$this->assign(get_defined_vars());
		$this->display('getList');
	}



	public function studentList($type = 0) {
		isset($_GET['id']) && $id = (int)$_GET['id'];
		$page = (int)$_GET['page'];

		$id = 1;//临时测试使用
		if(empty($id)) {
			$this->error('must chose a active');
		}
		//echo '<pre>';
		$formFormatArr = $this->reserveModel->getReserveList($id);
		$results = $this->reserveModel->getAllStudentsList();

		foreach( $results as $value) {
			$stuInfoArr[$value['sstudentcode']] = $value;
		}
		$condition = array('list_id' =>$id);
		$condition['status_range'] = 'status >0 and status < 3';
		$results =  $this->reserveModel->getStudentsList($condition);
		/*echo '<pre>';
		print_r($results);exit();*/
		foreach($results as $value) {
			$data[trim($value['stucode'])][$value['class_id']] = $value['week_id'];

		}
		$results = $this->reserveModel->getReserveInfo($id);

		foreach($results as $value) {
			$formInfoArr[$value['class_id']][$value['week_id']] = $value;
		}

		$this->assign(get_defined_vars());
		if($type == 1) {
			$this->display('export');
		} else {
			$this->display('studentList');
		}


	}
	public function export() {
		header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=tongjibiao.xls");
		$this->studentList(1);
	}
	public function edit() {
		$this->getForm();
	}
	public function add() {
		$this->getForm('add');
	}

	public function update() {
		//dumps($_POST);exit;
		  $list_id = (int)$_GET['id'];
		  $stucode = trim($_GET['scode']);

		  if(empty($list_id) || empty($stucode)) {
			$this->error('must input list_id and stucode');
		  }
		  if(count($_POST) > 0) {
		  	 $post = $_POST;
		  	 $i = $this->reserveModel->partUpdateStu($list_id,$stucode,$post['selects']);
		  	 $this->success('更新'.$i.'成功');
		  }

	}
	public function delete() {
		//dumps($_POST);exit;
		  $list_id = (int)$_GET['id'];
		  $stucode = trim($_GET['scode']);

		  if(empty($list_id) || empty($stucode)) {
			$this->error('must input list_id and stucode');
		  }

		  $i = $this->reserveModel->deleteStu($list_id,$stucode);
		  $this->success('更新'.$i.'成功');
		  }


	public function insert() {
		//dumps($_POST);exit;
		  $list_id = (int)$_GET['id'];
		  $stucode = trim($_POST['scode']);

		  if(empty($list_id) || empty($stucode)) {
			$this->error('must input list_id and stucode');
		  }
		  $post = array();
		  foreach($_POST['selects'] as $class_id => $week_id) {
		  	if($week_id == 0) {
		  		$this->error('课程必须填完整');
		  	}
		  	$post[] = array(
    			'list_id'  => $list_id,
    			'stucode'  => $stucode,
    			'class_id' => $class_id,
    			'week_id'  => $week_id,
    			'status'   => 2
    		);
		  }
		  if(count($post) > 0) {

		  	 $i = $this->reserveModel->insertStuInfo($post);
		  	 $this->success('更新'.$i.'成功');
		  }

	}
	private function getForm($behavior = 'update') {
		$list_id = (int)$_GET['id'];
		if(empty($list_id)) {
			$this->error('must input list_id and stucode');
		 }
		if(!empty($_GET['scode'])) {
           $stucode = trim($_GET['scode']);
           $stuInfoArr = $this->getUserInfo($stucode);
		} else {
			$stucode = '';
		    $stuInfoArr = array();
		}



		 $id = $list_id;
		$data = array();
		$aFormStruct = $this->getFormStruct($list_id);
		$aFormInfo = $this->getFormInfo($list_id);
        //dumps($aFormInfo);
		$action = '';
		if($behavior == 'update') {
			$data =  $this->reserveModel->getStudentInfo(array('list_id' =>1,'stucode'=>$stucode));
			$action = U('/Reserve/Apply/update',array('id'=>$list_id,'scode'=>$stucode));

		} else {
			$data =  array();
			$action = U('/Reserve/Apply/insert',array('id'=>$list_id));
		}

		$this->assign(get_defined_vars());
		$this->display('getForm');
	}
    public function verify() {
       $id = (int)$_GET['id'];
       if(empty($id)) {
       	 $this->error('you must input a id ~');
       }
       $aFormStruct = $this->getFormStruct($id);
       $aFormInfo = $this->getFormInfo($id);
       $condition = array();
       $condition['status_range'] = '( status = 0 or status = 2 or status = 3 ) ';
       $condition['list_id'] = $id;
       $results =  $this->reserveModel->getStudentsList($condition);
       $aStuList = array();
       $aStuInfo = array();
       $aStatus = array();
       foreach($results as $value) {
       	    $sStucode = addslashes(trim($value['stucode']));
       		$aStuList[$sStucode][$value['class_id']] = $value['week_id'];
       		$aStatus[$sStucode][$value['class_id']] = $value['status'];
       		if(!isset($aStuInfo[$sStucode])) {
       			$aStuInfo[$sStucode] = $this->getUserInfo($sStucode);
       		}

       }
       $this->assign(get_defined_vars());
	   $this->display();

    }
    public function confirm() {
    	$json = array();
    	$list_id = (int)$_POST['id'];
    	$stucode = addslashes(trim($_POST['stucode']));
    	$class_id = (int)$_POST['class_id'];
    	$week_id = (int)$_POST['week_id'];

    	$status = (int)$_POST['status'];
    	if(empty($list_id) || empty($stucode) || empty($class_id) || empty($week_id) || $status < 2 || $status >3) {
            $json['error'] = 1 ;
            echo json_encode($json);exit;
    	}
    	$data = array(
    			'list_id'  => $list_id,
    			'stucode'  => $stucode,
    			'class_id' => $class_id,
    			'week_id'  => $week_id,
    			'status'   => $status
    		);
    	$i = $this->reserveModel->applyConfirm($list_id,$stucode,$data);
    	if($i) {
    		echo 1;exit();
    	} else {
    		echo 0;exit;
    	}

    }





	/**
	 * 将提交过来的数组转换键
	 * @param [type] $arr 需要转换的数组
	 * @return [type] [description]
	 */
	private function arr2arr($arr) {
		$newarr = array();
		foreach($arr as $field => $arr1)
			foreach($arr1 as $x => $arr2)
				foreach($arr2 as $y => $value) {
					$newarr[$x][$y][$field] = $value;
				}
		return $newarr;
	}




	public function searchStuInfo() {
		$list_id = (int)$_GET['id'];
		if(empty($list_id)) {
			$json['error'] = 1;
			$json['msg'] = '活动获取失败!';
			echo json_encode($json);exit();

		}
		$condition = trim($_POST['condition']);

		$where = '';

	    $where = 'sAliasCode ="'.$condition.'" ';

		//exit($where);
		$results = $this->reserveModel->getStuInfoList($where);
		if(!empty($results[0])) {

			$result = $this->reserveModel->getStudentInfo(array('stucode'=>$results[0]['stucode']));
		    if(!empty($result)) {
				$json['error'] = 1;
				$json['msg'] = '该同学已经报名过了!';
				echo json_encode($json);exit();
		    }

			$results[0]['error'] = 0;
			echo json_encode($results[0]);exit();
		} else {
			$json['error'] = 1;
			$json['msg'] = '没找到该同学!';
			echo json_encode($json);exit();
		}

	}


}
