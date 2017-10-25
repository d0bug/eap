<?php
/**
 * 语文作业管理
 *
 */
class CampAction extends AppCommAction{

	public function index() {
		//echo 'camp';
		/*$this->assign(get_defined_vars());
		$this->display('getList');*/
		$this->getList();
	}
	private function getList() {
		$model = D('Camp');

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


		$list = $model->getClassList($data);

		//url($array,$key,$value)

		//dumps($list);



		$this->assign(get_defined_vars());
		$this->display('getList');
	}

	public function add() {
		$this->assign(get_defined_vars());
		$this->display('getForm');
	}
	public function insert() {

		if(empty($_POST['sClassCode'])) {
			$this->error('你什么也没填写');
		} else {
			$sClassCode = trim($_POST['sClassCode']);
			$sClassCode = preg_replace('|[^a-zA-Z0-9]|', '', $sClassCode);
		}
		$model = D('Camp');
		$result = $model->_insert(array('sClassCode'=>$sClassCode,'eStatus'=>1),'Camp_class');
		if(!empty($result)) {
			$this->success('添加成功','/Camp/Camp/index');
		}

	}

	public function delete() {
		$json = array(
			'error' => 1,
			'msg' => '删除失败'
			);
		if(empty($_GET['id'])) {
			echo json_encode($json);exit();
		} else {
			$id = (int)$_GET['id'];
		}
		if($id < 1) {
			echo json_encode($json);exit();
		}
		$model = D('Camp');
		$result = $model->_delete(array('id'=>$id),'Camp_class');
		if($result) {
			$json['error'] = 0;
			$json['msg'] = '删除成功';
			echo json_encode($json);exit();
		} else {
			echo json_encode($json);exit();
		}

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

