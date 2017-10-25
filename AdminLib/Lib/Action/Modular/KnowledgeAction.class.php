<?php

class KnowledgeAction extends ExerciseCommAction{

    protected $categoryModel = '';
	public function index() {

		$this->getList();

	}


    protected function notNeedLogin() {
		return array('MODULAR-MODULARAPPLY-CREATE_JSFORM','MODULAR-MODULARAPPLY-AJAXSAVEFORMDATA','MODULAR-MODULARAPPLY-AJAX_ATTR_RELATION');
	}
	private function getList() {
		$List = $this->knowledgeModel->getKnowledgeList();
		//echo '<pre>';print_r($List);

		$this->assign(get_defined_vars());
		$this->display('getList');
	}
	public function insert() {

		if(count($_POST) > 0) {
			$post = $_POST;
            $affect =  $this->knowledgeModel->insertKnowledgeInfo($post);
			$this->success(' you have upated ' .$affect.'rows successfully~!','/Modular/Knowledge/index');
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
            $affect =  $this->knowledgeModel->updateKnowledgeInfo($post,$id);
			$this->success(' you have upated ' .$affect.'rows successfully~!','/Modular/Knowledge/index');
		}
		$this->getForm();
	}

	private function getForm() {
		$id = (int)$_GET['id'];
		$aKnowledgeInfo = array();
		if(!empty($id)) {
			$aKnowledgeInfo = $this->knowledgeModel->getKnowledgeInfo(array('id'=>$id));
			$action = U('Modular/Knowledge/update',array('id'=>$id),'');
		} else {
			$action = U('Modular/Knowledge/insert','','');
		}
		$this->categoryModel = D('ExerciseCategory');
		$aCategoryList = $this->categoryModel->getCategoryList(array('status'=>1));
        $post = array();
		if(isset($_POST)) {
			$post = $_POST;
		}
		if(isset($post['title'])) {
			$title = addslashes(trim($post['title']));
		} elseif (isset($aKnowledgeInfo['title'])) {
			$title = $aKnowledgeInfo['title'];
		} else {
			$title = '';
		}
		if(isset($post['category_id'])) {
			$category_id = addslashes(trim($post['category_id']));
		} elseif (isset($aKnowledgeInfo['category_id'])) {
			$category_id = $aKnowledgeInfo['category_id'];
		} else {
			$category_id = 0;
		}
		if(isset($post['mod_max'])) {
			$mod_max = addslashes(trim($post['mod_max']));
		} elseif (isset($aKnowledgeInfo['mod_max'])) {
			$mod_max = $aKnowledgeInfo['mod_max'];
		} else {
			$category_id = 4;
		}
		if(isset($post['sort_order'])) {
			$sort_order = addslashes(trim($post['sort_order']));
		} elseif (isset($aKnowledgeInfo['sort_order'])) {
			$sort_order = $aKnowledgeInfo['sort_order'];
		} else {
			$sort_order = 0;
		}
		if(isset($post['flash_url'])) {
			$flash_url = addslashes(trim($post['flash_url']));
		} elseif (isset($aKnowledgeInfo['flash_url'])) {
			$flash_url = $aKnowledgeInfo['flash_url'];
		} else {
			$flash_url = '';
		}
		if(isset($post['status'])) {
			$status = addslashes(trim($post['status']));
		} elseif (isset($aKnowledgeInfo['status'])) {
			$status = $aKnowledgeInfo['status'];
		} else {
			$status = 1;
		}

		$this->assign(get_defined_vars());
		$this->display('getForm');

	}

}


