<?php
/**
 * 语文作业管理
 *
 */
class IndexAction extends AppCommAction{

	public function index() {
		$this->getList();
	}
	private function getList() {
		$model = D('Task');
		import('ORG.Util.Page');
		if(!empty($_GET['nYear'])) {
			$data['nYear'] = (int)$_GET['nYear'];
		} else {
			$data['nYear'] = 0;
		}
		if(!empty($_GET['nSeason'])) {
			$data['nSeason'] = (int)$_GET['nSeason'];
		} else {
			$data['nSeason'] = 0;
		}
		if(!empty($_GET['nType'])) {
			$data['nType'] = (int)$_GET['nType'];
		} else {
			$data['nType'] = 0;
		}
		if(!empty($_GET['sClassTypeCode'])) {
			$data['sClassTypeCode'] = preg_replace('|[^a-zA-Z0-9]|i', '', $_GET['sClassTypeCode']) ;
		} else {
			$data['sClassTypeCode'] = '';
		}
		$nCurrYear = (int)date('Y');

		$years = array(0=>'年');
		for($i=$nCurrYear-1;$i<=$nCurrYear+1;$i++) {
			$years[$i] = $i;
		}
		$seasons = array(
			0=>'学期',
			3=>'春',
			4=>'夏',
			1=>'秋',
			2=>'冬'
			);
		$types = array(
			0=>'题型',


			1=>'选择题',
			2=>'主观题',
			3=>'填空题'
			);

		$results = $model->getClassTypeList($data['nYear'],$data['nSeason']);
		$classTypeCodes = array(0=>'班型');
		foreach($results as $value) {
			$classTypeCodes[$value['scode']] = $value['sname'];
		}
		//url($array,$key,$value)












		$total = $model->getQuestionListTotal();
		$Page       = new Page($total);
		//$Page->setConfig('theme','"%totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %downPage% %first% %prePage% %linkPage% %nextPage% %end%"');
		$page = $Page->show();


		$data['page'] = empty($_GET['p'])?'1':$_GET['p'];
		$aQuestionList = $model->getQuestionList($data);
		//dumps($aQuestionList);


		$this->assign(get_defined_vars());
		$this->display('getList');
	}

	public function getClassList() {
		$model = D('Task');

		$aClassList = $model->getClassTypeList();
		dumps($aClassList);

	}
	public function classTypeList() {
		$model = D('Task');

		$aClassList = $model->getClassTypeList();
		dumps($aClassList);
	}
	public function multyAdd() {
		    if(empty($_GET['nQuestionid'])) {
		    	$this->error('you must choose a nQuestionid~!');
		    }
		    $nQuestionid = (int)$_GET['nQuestionid'];
		    if(empty($nQuestionid)) {
		    	$this->error('you must choose a nQuestionid~!');
		    }


		    $model = D('Task');
		    $aQustionInfo = $model->getQuestionInfoByCode($nQuestionid);
		    //dumps($aQustionInfo);
		    if($aQustionInfo['ntype'] == 1) {
                $aOptionList = $model->getOptionList($nQuestionid);
                $template = 'objectiveAdd';
		    } elseif($aQustionInfo['ntype'] == 2) {
		    	$aOptionList = $model->getOptionSubjectList($nQuestionid);
		    	$template = 'subjectiveAdd';
		    } elseif($aQustionInfo['ntype'] == 3){
		    	$aOptionList= $model->getList(array('nQuestionid'=>$nQuestionid),'TASK_question_fillin');
		    	$template = 'fillinAdd';
		    }


		    //echo $nQuestionid;
		  // dump($aOptionList);exit();



		$this->assign(get_defined_vars());
		$this->display($template);
	}
	public function multyUpdate() {
		//echo 0;exit();
		$data = array();
		if(empty($_POST['nQuestionid'])) {
			echo -2;exit();
		}
		$nQuestionid = (int)$_POST['nQuestionid'];

		$model = D('Task');
		$aQustionInfo = $model->getQuestionInfoByCode($nQuestionid);
		if($aQustionInfo['ntype'] == 3) {
			$this->multyUpdateFillin($nQuestionid,$aQustionInfo);
		}
		$post = $_POST['options'];
		foreach($post as $sort =>$v){
			$data[$sort]['sort'] = $sort;

			if(empty($v['question'])) {
				//$data[$sort]['question'];
			} else {
				$data[$sort]['question'] = base64_encode(trim($v['question']));
			}
			if(empty($v['sPoint'])) {
				//$data[$sort]['sPoint'];
			} else {
				$data[$sort]['sPoint'] = base64_encode(trim($v['sPoint']));
			}


			if(empty($v['option1'])) {
				//$data[$sort]['option1'] = '';
			} else {
				$data[$sort]['option1'] = base64_encode(trim($v['option1']));
			}
			if(empty($v['option2'])) {
				//$data[$sort]['option2'] = '';
			} else {
				$data[$sort]['option2'] = base64_encode(trim($v['option2']));
			}
			if(empty($v['option3'])) {
				//$data[$sort]['option3'] = '';
			} else {
				$data[$sort]['option3'] = base64_encode(trim($v['option3']));
			}
			if(empty($v['option4'])) {
				//$data[$sort]['option4'] = '';
			} else {
				$data[$sort]['option4'] = base64_encode(trim($v['option4']));
			}
			if(empty($v['nValue'])) {
				//$data[$sort]['nValue'] = 0;
			} else {
				$data[$sort]['nValue'] = (int)$v['nValue'];
			}
			if(empty($v['answer'])) {
				//$data[$sort]['answer'] = '';
			} else {
				$data[$sort]['answer'] = implode('|', $v['answer']);
			}
	    }
	    //echo '<pre>';print_r($data);exit();
	    if($aQustionInfo['ntype'] == 1) {
	    	$table = 'TASK_question_objective';
	    } elseif($aQustionInfo['ntype'] == 2) {
	    	$table = 'TASK_question_subjective';
	    }
		$result = $model->multyUpdateOption($data,$nQuestionid,$table);
		$result = $model->_update(array('nSign'=>1),array('nQuestionid'=>$nQuestionid),'TASK_question_list');
		$this->getList();

	}

	protected function multyUpdateFillin($nQuestionid,$aQustionInfo) {
		$post = $_POST['options'];
		$data = array();

		foreach($post as $sort => $v) {
			$data[$sort]['sort'] = $sort;
			if(empty($v['question'])) {
				//$data[$sort]['question'];
			} else {
				$question = preg_replace('|<span style="text-decoration:line-through;">[^<]+</span>|i', '__', trim($v['question']));

				$data[$sort]['question'] = base64_encode($question);
			}
			if(empty($v['sPoint'])) {
				//$data[$sort]['sPoint'];
			} else {
				$data[$sort]['sPoint'] = base64_encode(trim($v['sPoint']));
			}

			if(empty($v['nValue'])) {
				//$data[$sort]['nValue'] = 0;
			} else {
				$data[$sort]['nValue'] = (int)$v['nValue'];
			}
			if(empty($v['answer'])) {
				//$data[$sort]['answer'] = '';
			} else {
				$data[$sort]['answer'] =  $v['answer'];
			}
			if(empty($v['options'])) {
				//$data[$sort]['answer'] = '';
			} else {
				$data[$sort]['options'] =  $v['options'];
			}



		}//end foreach
		$model = D('Task');
		$result = $model->multyUpdateOption($data,$nQuestionid,'TASK_question_fillin');
		$this->getList();

	}

	public function url($array,$key,$value) {
		$array['sClassTypeCode'] = '';
		$array[$key] = $value;
		$arr = array();
		foreach($array as $k=>$v) {
			if(empty($v)) continue;
			$arr[$k] = $k.'='.$v;
		}

		if(empty($arr)) {
			return '/task/index/index';
		}
		return '/task/index/index/'.implode('/', $arr);
	}


	/**
     * 不需要登录的方法名称数组，名称需大写
     * @return Array
     */
	protected function notNeedLogin(){
		return array('UPLOADER');
	}


	public function upLoader() {
	import('ORG.Net.Uploader');

    //上传配置
    $config = array(
        "savePath" => "upload/" ,             //存储文件夹
        "maxSize" => 1000 ,                   //允许的文件最大尺寸，单位KB
        'maxWidth' => 400,
        "allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp" )  //允许的文件格式
    );
    //上传文件目录
    $Path = UPLOAD_PATH;

    //背景保存在临时目录中
    $config[ "savePath" ] = $Path;
    $up = new Uploader( "upfile" , $config );
    $type = $_REQUEST['type'];
    $callback=$_GET['callback'];

    $info = $up->getFileInfo();
    //file_put_contents($Path.'/a', json_encode($info));
    $info['url'] = '/'.date('Ymd').'/'.$info['name'];
    /**
     * 返回数据
     */
    if($callback) {
        echo '<script>'.$callback.'('.json_encode($info).')</script>';
    } else {
        echo json_encode($info);
    }
	}

}

