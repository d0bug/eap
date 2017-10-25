<?php
/**
 * 语文作业管理
 *
 */
class IndexAction extends AppCommAction{

	public function index() {
		//echo 'camp';
		/*$this->assign(get_defined_vars());
		$this->display('getList');*/
		$this->getClassList();
	}
	private function getClassList() {
		$model = D('Camp');

		if(!empty($_GET['nYear'])) {
			$data['nClassYear'] = (int)$_GET['nYear'];
		} else {
			$data['nClassYear'] = 0;
		}
		if(!empty($_GET['nSeason'])) {
			$data['nSemester'] = (int)$_GET['nSeason'];
		} else {
			$data['nSemester'] = 0;
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
		//print_r($data);
		$list = $model->getClassList($data);
		$numberList = $model->getQueationNumber();
		//echo '<pre>';
		/*print_r($list);*/
		//echo $numberList['BJ14C1467'][1];
		//print_r($numberList);
		//print_r(get_defined_vars());


		$this->assign(get_defined_vars());
		$this->display('getClassList');
	}

	public function paperList() {
		$this->getList();
	}

	public function getList() {
		if(empty($_GET['sClassCode'])) {
				$this->error('you must input a classcode');
		} else {
			$sClassCode = trim($_GET['sClassCode']);
		}

		if(empty($_GET['nLessonid']) && (int)$_GET['nLessonid'] <= 0) {
			$this->error('you must input a nLessonid');
		} else {
			$nLessonid = (int)$_GET['nLessonid'];
		}
		$model = D('Camp');
		//echo '<pre>';
		$aClassInfo = $model->getClassInfo($sClassCode,$nLessonid);
		//print_r($aClassInfo);


		$results = $model->getPaperList(array('sClassCode'=>$sClassCode,'nLessonid'=>$nLessonid));
		$aQuestionList = array();
		foreach($results as $value) {
			$temp = $value;
			$temp['sQuestion'] = unserialize(base64_decode($value['squestion'])) ;
			$aQuestionList[] = $temp;

		}
		//echo '<pre>';

		//print_r($aQuestionList);
		$this->assign(get_defined_vars());
		$this->display('getList');
	}
	public function add() {
		$this->getForm();
	}
	public function insert() {
		$data = array();
		if(!empty($_GET['sClassCode'])) {
			$data['sClassCode'] = trim($_GET['sClassCode']);

		} else {
			$this->error('plz input the classcode');
		}
		if(!empty($_GET['nLessonid'])) {
			$data['nLessonid'] = (int)$_GET['nLessonid'];

		} else {
			$this->error('plz input lessonid');
		}
		//echo '<pre>';
		$post = $_POST;



		if(!empty($post['nType'])) {
			$data['nType'] = $post['nType'];
		} else {
			$data['nType'] = '';
		}
		if(!empty($post['question'])) {
			$sQuestion['question'] = base64_encode($post['question']);
		} else {
			$sQuestion['question'] = '';
		}
		if(!empty($post['answers'])) {
			$answers = $post['answers'];
			foreach($answers as $key =>$value) {
				$sQuestion['answers'][$key] = base64_encode($value);
			}
		} else {
			$sQuestion['answers'][] = base64_encode('没有填写选项');
		}
		if(!empty($post['answer'])) {
			$sQuestion['answer'] = ($data['nType'] == 1)?$post['answer']:base64_encode($post['answer']);
		} else {
			$sQuestion['answer'] = '';
		}
		if(!empty($post['sPoint'])) {
			$data['sPoint'] = base64_encode($post['sPoint']);
		} else {
			$data['sPoint'] = '';
		}
		if(!empty($post['nSort'])) {
			$data['nSort'] = $post['nSort'];
		} else {
			$data['nSort'] = 0;
		}
		if(!empty($post['nSubSort'])) {
			$data['nSubSort'] = $post['nSubSort'];
		} else {
			$data['nSubSort'] = 0;
		}
		if(!empty($post['nSign'])) {
			$data['nSign'] = $post['nSign'];
		} else {
			$data['nSign'] = 1;
		}
		$data['sQuestion'] = base64_encode(serialize($sQuestion));
		//print_r($data);
		$model = D('Camp');
		$model->_insert($data,'Camp_question');
		$this->success('添加成功',U('/Camp/Index/paperList',array('sClassCode'=>$data['sClassCode'],'nLessonid'=>$data['nLessonid']),''));

	}
	public function edit() {
		$this->getForm();
	}
	public function update() {
		$data = array();
		if(!empty($_GET['sClassCode'])) {
			$data['sClassCode'] = trim($_GET['sClassCode']);

		} else {
			$this->error('plz input the classcode');
		}
		if(!empty($_GET['nLessonid'])) {
			$data['nLessonid'] = (int)$_GET['nLessonid'];

		} else {
			$this->error('plz input lessonid');
		}
		if(!empty($_GET['id'])) {
			$id = (int)$_GET['id'];

		} else {
			$this->error('plz input id');
		}
		//echo '<pre>';
		$post = $_POST;



		if(!empty($post['nType'])) {
			$data['nType'] = $post['nType'];
		} else {
			$data['nType'] = '';
		}
		if(!empty($post['question'])) {
			$sQuestion['question'] = base64_encode($post['question']);
		} else {
			$sQuestion['question'] = '';
		}
		if(!empty($post['answers'])) {
			$answers = $post['answers'];
			foreach($answers as $key =>$value) {
				$sQuestion['answers'][$key] = base64_encode($value);
			}
		} else {
			$sQuestion['answers'][] = base64_encode('没有填写选项');
		}
		if(empty($post['answer'])) {
			$sQuestion['answer'] = '';
		} elseif($data['nType'] >1) {
			$sQuestion['answer'] = base64_encode($post['answer']);

		} else {
			$sQuestion['answer'] = $post['answer'];
		}


		if(!empty($post['sPoint'])) {
			$data['sPoint'] = base64_encode($post['sPoint']);
		} else {
			$data['sPoint'] = '';
		}
		if(!empty($post['nSort'])) {
			$data['nSort'] = $post['nSort'];
		} else {
			$data['nSort'] = 0;
		}
		if(!empty($post['nSubSort'])) {
			$data['nSubSort'] = $post['nSubSort'];
		} else {
			$data['nSubSort'] = 0;
		}
		if(!empty($post['nSign'])) {
			$data['nSign'] = $post['nSign'];
		} else {
			$data['nSign'] = 1;
		}
		$data['sQuestion'] = base64_encode(serialize($sQuestion));
		//print_r($data);
		$model = D('Camp');
		//echo '<pre>';print_r($data);exit();
		//$model->_insert($data,'Camp_question');
		$model->_update($data,array('id'=>$id),'Camp_question');
		$this->success('修改成功',U('/Camp/Index/paperList',array('sClassCode'=>$data['sClassCode'],'nLessonid'=>$data['nLessonid']),''));

	}
	public function getForm() {
		$info = array();
		$model = D('Camp');
		//echo $_GET['id'];
		if(!empty($_GET['id']) && (int)$_GET['id'] > 0) {
			$id = (int)$_GET['id'];
			$info = $model->getQuestionInfo($id);
			$action = '/Camp/Index/update/id/'.$id;
			$url = '/Camp/Index/edit/id/'.$id;

		} else {
			//echo '1111111111111';
			$action = '/Camp/Index/insert';
		}
		//echo $id;
		//echo '<pre>';print_r($info);exit();
		$token = time().rand(1,100);
		if(!empty($_GET['sClassCode'])) {
			$sClassCode = trim($_GET['sClassCode']);
			$action .= '/sClassCode/'.$sClassCode;
		} else {
			$this->error('plz input the classcode');
		}
		if(!empty($_GET['nLessonid'])) {
			$nLessonid = (int)$_GET['nLessonid'];
			$action .= '/nLessonid/'.$nLessonid;
		} else {
			$this->error('plz input lessonid');
		}


		if(!empty($_POST['sQuestion'])) {
			$sQuestion = $_POST['sQuestion'];
		} elseif(!empty($info['squestion'])) {
			$sQuestion = unserialize(base64_decode($info['squestion']));
		} else {
			$sQuestion = array(
				'question'=>'',
				'answers'=> array('','','',''),
				'answer' => '100'
				);
		}
		if(!empty($_POST['sPoint'])) {
			$sPoint = htmlspecialchars($_POST['sPoint']);
		} elseif(!empty($info['spoint'])) {
			$sPoint = base64_decode($info['spoint']);
		} else {
			$sPoint = '';
		}

		if(!empty($_POST['nSort'])) {
			$nSort = (int)$_POST['nSort'];
		} elseif(!empty($info['nsort'])) {
			$nSort = $info['nsort'];
		} else {
			$nSort = '';
		}

		if(!empty($_POST['nSubSort'])) {
			$nSubSort = (int)$_POST['nSubSort'];
		} elseif(!empty($info['nsubsort'])) {
			$nSubSort = $info['nsubsort'];
		} else {
			$nSubSort = 0;
		}

		if(!empty($_POST['nSign'])) {
			$nSign = (int)$_POST['nSign'];
		} elseif(!empty($info['nsign'])) {
			$nSign = $info['nsign'];
		} else {
			$nSign = 1;
		}

		if(!empty($_POST['nType'])) {
			$nType = (int)$_POST['nType'];
		} elseif(!empty($info['ntype'])) {
			$nType = $info['ntype'];
		} else {
			$nType = 1;
		}
		//echo $nType;
		$this->assign(get_defined_vars());
		//echo '<pre>';print_r(get_defined_vars());
		$this->display('getForm');


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
			return '/Camp/index/index';
		}
		return '/Camp/index/index/'.implode('/', $arr);
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

