<?php

class CampIndexAction extends ModularCommAction{
	protected $categoryModel = '';


    public function __construct() {
    	parent::__construct();
    	$this->categoryModel = D('ExerciseCategory');
    }
	public function index() {
        echo 'camp';
		//$this->getList();

	}


    protected function notNeedLogin() {
		return array('MODULAR-MODULARAPPLY-CREATE_JSFORM','MODULAR-MODULARAPPLY-AJAXSAVEFORMDATA','MODULAR-MODULARAPPLY-AJAX_ATTR_RELATION');
	}
	private function getList() {
		$List = $this->categoryModel->getCategoryList();

		$this->assign(get_defined_vars());
		$this->display('getList');
	}
	public function insert() {

		if(count($_POST) > 0) {
			$post = $_POST;
            $affect =  $this->categoryModel->insertCategoryInfo($post);
			$this->success(' you have upated ' .$affect.'rows successfully~!','/Modular/ExerciseCategory/index');
		}
		$this->getForm();
	}
	public function update() {
        $id = (int)$_GET['id'];
        if(empty($id)) {
        	$this->error(' must input a id ~!');
        }
		if(count($_POST) > 0) {
			$post = $_POST;
            $affect =  $this->categoryModel->updateCategoryInfo($post,$id);
			$this->success(' you have upated ' .$affect.'rows successfully~!','/Modular/ExerciseCategory/index');
		}
		$this->getForm();
	}

	private function getForm() {
		$id = (int)$_GET['id'];
		$aCategoryInfo = array();
		if(!empty($id)) {
			$aCategoryInfo = $this->categoryModel->getCategoryInfo(array('id'=>$id));
			$action = U('Modular/ExerciseCategory/update',array('id'=>$id),'');
		} else {
			$action = U('Modular/ExerciseCategory/insert','','');
		}
        $post = array();
		if(isset($_POST)) {
			$post = $_POST;
		}
		if(isset($post['title'])) {
			$title = addslashes(trim($post['title']));
		} elseif (isset($aCategoryInfo['title'])) {
			$title = $aCategoryInfo['title'];
		} else {
			$title = '';
		}
		if(isset($post['sort_order'])) {
			$sort_order = addslashes(trim($post['sort_order']));
		} elseif (isset($aCategoryInfo['sort_order'])) {
			$sort_order = $aCategoryInfo['sort_order'];
		} else {
			$sort_order = 0;
		}

		if(isset($post['status'])) {
			$status = addslashes(trim($post['status']));
		} elseif (isset($aCategoryInfo['status'])) {
			$status = $aCategoryInfo['status'];
		} else {
			$status = 1;
		}
		if(isset($post['is_show'])) {
			$is_show = addslashes(trim($post['is_show']));
		} elseif (isset($aCategoryInfo['is_show'])) {
			$is_show = $aCategoryInfo['is_show'];
		} else {
			$is_show = 1;
		}

		$this->assign(get_defined_vars());
		$this->display('getForm');

	}

}


