<?php
/**
 * 语文作业管理
 *
 */
class NewsAction extends AppCommAction{

	public function index() {
		$this->getList();
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

	public function getList() {

		$model = D('CampNews');




		$results = $model->getNewsList();
		//print_r($results);exit();
		$list = & $results;
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
		if(!empty($_POST['sTitle'])) {
			$data['sTitle'] = htmlspecialchars($_POST['sTitle']);
		}
		if(!empty($_POST['sContent'])) {
			$data['sContent'] = base64_encode($_POST['sContent']);
		}
		if(!empty($_POST['nId'])) {
			$data['nId'] = (int)$_POST['nId'];
			$nId = $data['nId'];
		} else {
			echo 'nid error!';
		}
		$data['nTime'] = time();


		$model = D('CampNews');
		$model->_insert($data,'Camp_news');
		$data = array();
		if(!empty($_POST['sClassCode'])) {
			foreach($_POST['sClassCode'] as $value) {
				$data['sClassCode'] = trim($value);
				$data['nId'] = $nId;
				$model->_insert($data,'Camp_news_class');


			}
		}
		$this->success('添加成功',U('/Camp/News/index','',''));

	}
	public function edit() {
		$this->getForm();
	}
	public function update() {
		$data = array();

		if(!empty($_GET['id'])) {
			$id = (int)$_GET['id'];

		} else {
			$this->error('plz input id');
		}
		if(!empty($_POST['sTitle'])) {
			$data['sTitle'] = htmlspecialchars($_POST['sTitle']);
		}
		if(!empty($_POST['sContent'])) {
			$data['sContent'] = base64_encode($_POST['sContent']);
		}
		if(!empty($_POST['nId'])) {
			$data['nId'] = (int)$_POST['nId'];
			$nId = $data['nId'];
		} else {
			echo 'nid error!';
		}


		$model = D('CampNews');
		$model->_delete(array('nId'=>$nId),'Camp_news_class');
		$model->_update($data,array('id'=>$id),'Camp_news');
		$data = array();
		if(!empty($_POST['sClassCode'])) {
			foreach($_POST['sClassCode'] as $value) {
				$data['sClassCode'] = trim($value);
				$data['nId'] = $nId;
				$model->_insert($data,'Camp_news_class');//


			}
		}
		$this->success('添加成功',U('/Camp/News/index','',''));

	}
	public function getForm() {
		$info = array();
		$model = D('CampNews');
		//echo $_GET['id'];
		if(!empty($_GET['id']) && (int)$_GET['id'] > 0) {
			$id = (int)$_GET['id'];
			$info = $model->getNewsInfo(array('id'=>$id));
			$action = '/Camp/News/update/id/'.$id;
			$url = '/Camp/News/edit/id/'.$id;

		} else {

			$action = '/Camp/News/insert';
		}
		//echo $id;
		//echo '<pre>';print_r($info);exit();
		if(!empty($_POST['sTitle'])) {
			$sTitle= trim($_POST['sTitle']);
		} elseif(!empty($info['stitle'])) {
			$sTitle = trim($info['stitle']);
		} else {
			$sTitle = '';
		}
		if(!empty($_POST['sContent'])) {
			$sContent = base64_decode($_POST['sContent']);
		} elseif(!empty($info['scontent'])) {
			$sContent= base64_decode($info['scontent']);
		} else {
			$sContent = '';
		}
		if(!empty($_POST['nId'])) {
			$nId = (int)$_POST['nId'];
		} elseif(!empty($info['nid'])) {
			$nId= $info['nid'];
		} else {
			$nId = time();
		}

		if(!empty($_POST['sClassCode'])) {
			$sClassCode = (int)$_POST['sClassCode'];
		} elseif(!empty($info['sclasscode'])) {
			$sClassCode= $info['sclasscode'];
		} else {
			$sClassCode = array();
		}
		$classList = $model->getClassList();
	    //echo '<pre>';print_r($sClassCode);





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
			return '/Camp/News/index';
		}
		return '/Camp/News/index/'.implode('/', $arr);
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
	
	
	/**
	 +------------------------------------------------------------------------------
	 * 删除咨询
	 +------------------------------------------------------------------------------
	 */
	public function del(){
		$newId = abs($_POST['id']);
		
		$rs = D('CampNews')->delNewsById($newId);
		echo json_encode($rs);
	}

}

