<?php
class ActiveAction extends AppCommAction{


	protected $modelReserve = null;
	public function index() {
       // $this->test();exit();

			$this->modelReserve->classStatistics(1);

		$this->getForm();

	}
	public function __construct() {
		parent::__construct();
        $this->modelReserve = D('Reserve');

	}

	public function test() {
			$rpcClient = SysUtil::getRpcClient('Employee');
			$empList = $rpcClient->getDeptEmps('互联网产品部');
			//echo json_encode($empList);
			dumps($empList);

	}

	/**
	 * 预约科目添加
	 * @return [type] [description]
	 */
	public function insert() {
		if(count($_POST) == 0) {
			$this->error('非法操作');
		}
		$id = (int)$_GET['id'];

		if(empty($id)) {
			$this->error('没有传入课程id，无法插入');
		}

		$post = $this->arr2arr(($_POST));


		$this->modelReserve->insertReserveInfo($post,$id);
		$this->success('添加成功');

	}
	/**
	 * 修改
	 * @return [type] [description]
	 */
	public function update() {
		$id = (int)$_GET['id'];
		if(count($_POST) == 0) {
			$this->error('非法操作');
		}
		if(empty($id)) {
			$this->error('没有传入id');
		}

		$post = $this->arr2arr($_POST);
		$this->modelReserve->updateReserveInfo($post,$id);
		$this->success('更新成功');
	}

	/**
	 * 添加和修改都通用的方法
	 * @return [type] [description]
	 */
	private function getForm() {

		$id = (int)$_GET['id'];
        if(empty($id)) {
        	$id = 1;
        }
		if(empty($id)) {
			$this->error('请先添加课程,然后再添加或修改');

		} else {

			$data = array();
			$active_info =  $this->modelReserve->getReserveList($id);
			//dumps($active_info);exit;
			if(empty($active_info)) {
				$this->error('该课程不存在或已经关闭');
			}
			$files = $this->modelReserve->getReserveInfo($id);

		    if(count($files) == 0) {
		    	//添加数据
		    	$action = '/Reserve/Active/insert/id/'.$id;
		    } else {
		    	$action = '/Reserve/Active/update/id/'.$id;
		    }
		    for($i =1;$i<=$active_info['week_num'];$i++) {
		    	for($j =1;$j<=$active_info['class_num'];$j++) {
		    		$data[$i][$j] = array();
		    	}
		    }
		    //dumps($files);exit;
			foreach($files as $value) {
				$data[$value['week_id']][$value['class_id']] = $value;
			    }
		    $this->assign(get_defined_vars());
		    $this->display('getForm');
		}


		exit;

	}




	/**
     * 不需要登录的方法名称数组，名称需大写
     * @return Array
     */
	protected function notNeedLogin(){

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


}

