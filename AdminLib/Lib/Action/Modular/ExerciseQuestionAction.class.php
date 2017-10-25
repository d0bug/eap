<?php

class ExerciseQuestionAction extends ExerciseCommAction{
    protected $categoryModel = '';

	public function index() {

       //print_r($aCategory);
		$this->getList();

	}


    protected function notNeedLogin() {
		return array('MODULAR-EXERCISEQUESTION-UPLOAD');
	}
	private function getList() {
		import('ORG.Util.Page');
		$total = $this->questionModel->getQuestionListTotal(array('status'=>1));
		$Page       = new Page($total);
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
		$List = $this->questionModel->getQuestionList(array('status'=>1,'limit'=>$nowPage.',20','order'=>'order by id DESC'));
		$page = $Page->show();

		$this->assign(get_defined_vars());
		$this->display('getList');
	}
	public function insert() {

		if(count($_POST) > 0) {
			$post = $_POST;
            $affect =  $this->questionModel->insertQuestionInfo($post);
			$this->success(' you have upated ' .$affect.'rows successfully~!','/Modular/ExerciseQuestion/index');
		}
		$this->getForm();
	}
	public function update() {

        $id = (int)$_GET['id'];
        if(empty($id)) {
        	$this->error(' must input a id ~!');
        }
		if(count($_POST) > 0) {
			//echo '<pre>';print_r($_POST);exit;


			$post = $_POST;
            $affect =  $this->questionModel->updateQuestionInfo($post,$id);
			$this->success(' you have upated ' .$affect.'rows successfully~!','/Modular/ExerciseQuestion/index');
		}
		$this->getForm();
	}

	public function delete() {
        //print_r($_POST['selects']);exit();

		if(empty($_POST['selects'])) {
			$this->error('删除为空');
		}
		$i = 0;
		foreach($_POST['selects'] as $value) {
			$i += $this->questionModel->changeStatus(array('status'=>0),$value);

		}
		$this->success('you have delete'.$i.'rows data successfully','/Modular/ExerciseQuestion/index');
	}

	private function getForm() {
		$id = (int)$_GET['id'];
		$aQuestionInfo = array();
		if(!empty($id)) {
			$aQuestionInfo = $this->questionModel->getQuestionInfo(array('id'=>$id));
			$action = U('Modular/ExerciseQuestion/update',array('id'=>$id),'');
		} else {
			$action = U('Modular/ExerciseQuestion/insert','','');
		}
		$this->categoryModel = D('ExerciseCategory');
		$aCategoryList = $this->categoryModel->getCategoryList(array('status'=>1));
		$aKnowledgeList = $this->questionModel->getKnowledgeList(array('status'=>1));
        $post = array();
        //echo '<pre>';print_r($aQuestionInfo);exit();
		if(isset($_POST)) {
			$post = $_POST;
		}
		if(isset($post['title'])) {
			$title = addslashes(trim($post['title']));
		} elseif (isset($aQuestionInfo['title'])) {
			$title = $aQuestionInfo['title'];
		} else {
			$title = '';
		}
		$token = time();
		if(isset($post['sort_order'])) {
			$sort_order = $post['sort_order'];
		} elseif (isset($aQuestionInfo['sort_order'])) {
			$sort_order = $aQuestionInfo['sort_order'];
		} else {
			$sort_order = 0;
		}
		if(isset($post['flash_url'])) {
			$flash_url = addslashes(trim($post['flash_url']));
		} elseif (isset($aQuestionInfo['flash_url'])) {
			$flash_url = $aQuestionInfo['flash_url'];
		} else {
			$flash_url = '';
		}
		if(isset($post['mod_id'])) {
			$mod_id = $post['mod_id'];
		} elseif (isset($aQuestionInfo['mod_id'])) {
			$mod_id = $aQuestionInfo['mod_id'];
		} else {
			$mod_id = 0;
		}
		if(isset($post['difficulty'])) {
			$difficulty = $post['difficulty'];
		} elseif (isset($aQuestionInfo['difficulty'])) {
			$difficulty = $aQuestionInfo['difficulty'];
		} else {
			$difficulty = 0;
		}
		if(isset($post['category_id'])) {
			$category_id = $post['category_id'];
		} elseif (isset($aQuestionInfo['category_id'])) {
			$category_id = $aQuestionInfo['category_id'];
		} else {
			$category_id = 0;
		}
		if(isset($post['knowledge_id'])) {
			$knowledge_id = $post['knowledge_id'];
		} elseif (isset($aQuestionInfo['knowledge_id'])) {
			$knowledge_id = $aQuestionInfo['knowledge_id'];
		} else {
			$knowledge_id = 0;
		}

		if(isset($post['status'])) {
			$status = $post['status'];
		} elseif (isset($aQuestionInfo['status'])) {
			$status = $aQuestionInfo['status'];
		} else {
			$status = 1;
		}
		if(isset($post['content'])) {
			$content = (int)$post['content'];
		} elseif (isset($aQuestionInfo['content'])) {
			$content = $aQuestionInfo['content'];
		} else {
			$content = 0;
		}
		if(isset($post['solve_flash'])) {
			$solve_flash = trim($post['solve_flash']);
		} elseif (isset($aQuestionInfo['solve_flash'])) {
			$solve_flash = $aQuestionInfo['solve_flash'];
		} else {
			$solve_flash = 0;
		}
		if(isset($post['question'])) {
			$question = trim($post['question']);
		} elseif (isset($aQuestionInfo['question'])) {
			$question = $aQuestionInfo['question'];
		} else {
			$question = '';
		}
		if(isset($post['answer'])) {
			$answer = trim($post['answer']);
		} elseif (isset($aQuestionInfo['answer'])) {
			$answer = $aQuestionInfo['answer'];
		} else {
			$answer = '';
		}
		if(isset($post['answers'])) {
			$answers = trim($post['answers']);
		} elseif (isset($aQuestionInfo['answers'])) {
			$answers = $aQuestionInfo['answers'];
		} else {
			$answers = array(
				'A' => '',
				'B' => '',
				'C' => '',
				'D' => ''

				);
		}

		$this->assign(get_defined_vars());
		$this->display('getForm');
	}








	public function upload() {
        //echo UPLOAD_PATH;
		import('ORG.Net.UploadFile');



		$upload = new UploadFile();// 实例化上传类
		$upload->maxSize  = 3145728 ;// 设置附件上传大小
		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->savePath =  UPLOAD_PATH.date('y').'/'.date('m').'/'.date('d').'/';

        if(!is_dir($upload->savePath)) {
        	if(false === mkdir($upload->savePath,0777,true)) {
        		echo 0;exit();
        	}

        }

		if(!$upload->upload()) {// 上传错误提示错误信息
		  // $this->error($upload->getErrorMsg());
		  echo json_encode($upload->getErrorMsg());
		} else {// 上传成功 获取上传文件信息
		   $info =  $upload->getUploadFileInfo();
		   echo json_encode($info[0]);
		}
		//echo json_encode(array('status'=>'上传成功','url'=>'abc.com','show_url'=>'bbb.com','delimg_url'=>U('Vip/VipInfo/del_img')));


	}


}


