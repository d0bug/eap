<?php
/**
 * 语文作业管理
 *
 */
class QuestionAction extends AppCommAction{

	public function index() {

	}
	private function getList() {




	}
	public function stepOne() {
		$model = D('Task');
		$aClassType = $model->getClassTypeList();
		$nYear = (int)date('Y');
		$aSeasons = array(3=>'春',4=>'夏',1=>'秋',2=>'冬');
		$nMonth = (int)date('m');

		$nSeason = season($nMonth);
		$sClassCode = '';
		$sClassTypeCode = '';
		$nType = 1;

		$nQuestionid = time();
		//$url = '/Task/Question/ajax_stepThree/nQuestionid/'.$nQuestionid;
		$this->assign(get_defined_vars());
		$this->display();
	}
	public function multyAdd() {
		$model = D('Task');
		$aClassType = $model->getClassTypeList();
		$nYear = (int)date('Y');
		$aSeasons = array(3=>'春',4=>'夏',1=>'秋',2=>'冬');
		$nMonth = (int)date('m');

		$nSeason = season($nMonth);
		$sClassCode = '';
		$sClassTypeCode = '';
		$nType = 1;

		$nQuestionid = time();
		//$url = '/Task/Question/ajax_stepThree/nQuestionid/'.$nQuestionid;
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function ajax_stepTwo() {
		$data = array();
		if(isset($_GET['n'])) {
			$n = 2;
		} else {
			$n = 0;
		}
		if(empty($_POST['nQuestionid'])) {
			echo -1;exit();
		} else {
			$data['nQuestionid'] = (int)$_POST['nQuestionid'];
		}
		if(empty($_POST['nSeason'])) {
			echo -2;exit();
		} else {
			$data['nSeason'] = (int)$_POST['nSeason'];
		}
		if(empty($_POST['nType'])) {
			echo -4;exit();
		} else {
			$data['nType'] = (int)$_POST['nType'];
		}
		if(empty($_POST['nYear'])) {
			echo -4;exit();
		} else {
			$data['nYear'] = (int)$_POST['nYear'];
		}
		if(empty($_POST['sClassTypeCode'])) {
			echo -5;exit();
		} else {
			$data['sClassTypeCode'] = trim($_POST['sClassTypeCode']);
			$data['sClassTypeCode'] = preg_replace('/[^A-Z0-9]/', '', strtoupper($data['sClassTypeCode']));

		}
		if(empty($_POST['sTopic'])) {
			echo -6;exit();
		} else {
			$data['sTopic'] = htmlspecialchars($_POST['sTopic']);
		}
		if(empty($_POST['nQuestionNum'])) {
			echo -7;exit();
		} else {
			$data['nQuestionNum'] = (int)$_POST['nQuestionNum'];
		}
		if($data['nQuestionNum'] <1 ) {
			echo -7;exit();
		}
		$model = D('Task');
		$result = $model->insertQuestion($data);
		if(empty($result)) {
			echo 0;exit();
		}
		if($n) {
			echo 2;exit();
		}


		echo 1 ;exit();

	}
	public function ajax_getClass() {

		if(empty($_GET['sClassTypeCode'])) {
			echo 0; exit();
		} else {
			 $sClassTypeCode = $_GET['sClassTypeCode'];
			 $sClassTypeCode = preg_replace('/[^A-Z0-9]/', '', strtoupper($sClassTypeCode));
		}

		$model = D('Task');

		$conditon = array();
		if(empty($_POST['nYear'])) {
			$conditon['nClassYear'] = (int)$_POST['nYear'];
		} else {
			$conditon['nClassYear'] = (int)date('Y');
		}
		if(!empty($_POST['nSeason'])) {
			$conditon['nSemester'] = (int)$_POST['nSeason'];
		} else {
			$nMonth = (int)date('m');
		    $conditon['nSemester'] = season($nMonth);
		}


		$conditon['sClassTypeCode'] = $sClassTypeCode;



		//$aClassList = $model->getClassList($conditon);
		$aClassList = $model->getTopic($conditon);

		if(empty($aClassList)) {
			exit(-1);
		}
		echo  json_encode($aClassList);exit();
	}
	public function ajax_stepThree() {
		if(empty($_GET['nType'])) {
			echo 0;exit();
		} else {
			$nType = (int)$_GET['nType'];
		}
		if(empty($_GET['nQuestionid'])) {
			echo -1;exit();
		} else {
			$nQuestionid = (int)$_GET['nQuestionid'];
		}
		if(1 == $nType) {
			$model = D('Task');
		    $aOptionInfo = $model->getOptionInfo($nQuestionid);

		    if(empty($aOptionInfo['answer'])) {
		    	$aOptionInfo['answer'] = array();
		    } else {
		    	$aOptionInfo['answer'] = explode('|', $aOptionInfo['answer']);
		    }

			$this->assign(get_defined_vars());
			$this->display('selection');
			exit();
		}

	}

	public function ajax_stepFour() {
		$data = array();
		if(empty($_POST['nQuestionid'])) {
			echo -2;exit();
		} else {
			$data['nQuestionid'] = (int)$_POST['nQuestionid'];
		}

		if(empty($_POST['question'])) {
			echo -1;exit();
		} else {
			$data['question'] = addslashes(trim($_POST['question']));
		}
		$model = D('Task');
		if(empty($_POST['option1'])) {
			$data['option1'] = '';
		} else {
			$data['option1'] = addslashes(trim($_POST['option1']));
		}
		if(empty($_POST['option2'])) {
			$data['option2'] = '';
		} else {
			$data['option2'] = addslashes(trim($_POST['option2']));
		}
		if(empty($_POST['option3'])) {
			$data['option3'] = '';
		} else {
			$data['option3'] = addslashes(trim($_POST['option3']));
		}
		if(empty($_POST['option4'])) {
			$data['option4'] = '';
		} else {
			$data['option4'] = addslashes(trim($_POST['option4']));
		}
		if(empty($_POST['answer'])) {
			$data['answer'] = '';
		} else {
			$data['answer'] = implode('|', $_POST['answer']);
		}

		$result = $model->updateOption($data['nQuestionid'],$data);
		if($result) {
			echo 1 ;exit();
		} else {
			echo 0;exit();
		}




	}

	public function questionEdit() {
		if(empty($_GET['id'])) {
			echo 0;exit();
		}
		$id = (int)$_GET['id'];
		if(empty($id)) {
			echo 0;exit();
		}
		$model = D('Task');
		$aQuestionInfo = $model->getQuestionInfo($id);
		//print_r($aQuestionInfo);
		$aClassType = $model->getClassTypeList();
		$aSeasons = array(3=>'春',4=>'夏',1=>'秋',2=>'冬');
		if(isset($aQuestionInfo['nyear'])) {
			$nYear = (int)$aQuestionInfo['nyear'];
		} else {
			$nYear = (int)date('Y');
		}

		if(isset($aQuestionInfo['nseason'])) {
			$nSeason = $aQuestionInfo['nseason'];
		} else {
			$nMonth = (int)date('m');
			$nSeason = season($nMonth);
		}
		$nQuestionid = (int)$aQuestionInfo['nquestionid'];

		if(isset($aQuestionInfo['sclasscode'])) {
			$sClassCode = trim($aQuestionInfo['sclasscode']);
		} else {
			$sClassCode = '';
		}
		if(isset($aQuestionInfo['sclasstypecode'])) {
			$sClassTypeCode = trim($aQuestionInfo['sclasstypecode']);
		} else {
			$sClassTypeCode = '';
		}
		$nType = $aQuestionInfo['ntype'];
		$this->assign(get_defined_vars());
		$this->display('stepOne');
	}

	public function ajax_delete() {
		if(empty($_GET['id'])) {
			echo 0;exit();
		} else {
			$id = (int)$_GET['id'];
		}
		if(empty($id)) {
			echo -1;exit();
		}

		$model = D('Task');
		$status = $model->delete($id);
		if($status) {
			echo 1;
		} else {
			echo 0;
		}
		exit();
	}


	public function ajax_addOption() {

		if(empty($_GET['nQuestionid'])) {
			echo -1;exit();
		} else {
			$nQuestionid = (int)$_GET['nQuestionid'];
		}
		if(empty($nQuestionid)) {
			echo -2;exit();
		}

		if(empty($_GET['sort'])) {
			echo -3;exit();
		} else {
			$sort = (int)$_GET['sort'];
		}
		if(empty($sort)) {
			echo -4;exit();
		}
		$data = array(
			'nQuestionid' => $nQuestionid,
			'sort' => $sort
			);
		$model = D('Task');
		echo $model->insertOption($data);exit();
	}



	/**
     * 不需要登录的方法名称数组，名称需大写
     * @return Array
     */
	protected function notNeedLogin(){
		$arr = array('STEPONE');
		$arr[] = strtoupper('ajax_getClass');
		return $arr;
	}

}

