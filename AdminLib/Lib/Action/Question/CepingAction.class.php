<?php
class CepingAction extends QuestionCommAction {
	public function index() {
		$this->getKnowledgeList();
	}

	private function getKnowledgeList() {
		$this->getTree2();
	}



	public function settingIndex() {
		$this->getSettingList();
	}
	public function settingInsert() {
		//
	}
	private function getSettingList() {
		$model = D('Basic');
		$data = array();
		$data['status'] = 1;
		$data['type'] = 0;
		$button_list = $model->getSettingList($data);
		$this->assign('buttons',$button_list);
		if(empty($_GET['type'])) {
			$this->assign('list',$button_list);
		} else {
			$data['type'] = (int)$_GET['type'];
			$results = $model->getSettingList($data);
			$this->assign('list',$results);
		}
		$this->assign('type',$data['type']);

		$results = $model->getSettingList($data);

		$this->display('getSettingList');
	}
	private function getSettingForm() {
		$assign = array();
		if(isset($_GET['type'])) {
			$assign['type'] = (int)$_GET['type'];
		} else {
			$this->error('类型为空');
		}
		$model = D('Basic');
		if(empty($_GET['id'])) {
			$info = array();
		} else {
			$id = (int)$_GET['id'];
			$info = $model->getSettingInfo(array('id'=>$id,'status'=>1));
		}
	}

	private function getTree2() {
		$model = D('Basic');
		$results = $model->getTree5();
		$sql = '';
		foreach($results as $value) {
			$sql .= 'update vip_knowledge set level = 5 where id = '.$value['id'].";\n";
		}
		echo $sql;
	}




}
