<?php
class DialogAction extends UtilCommAction {
	public function subjects() {
		if ($this->isPost()) {
			import('COM.Gaosi.Subject');
			$subjects = Subject::getSubjectList();
			echo json_encode(array('rows'=>$subjects));
			exit;
		} else {
			extract($_GET);
			$url = $this->getUrl('subjects');
			$this->assign(get_defined_vars());
			$this->display();
		}
    }
    
    public function projects() {
    	if($this->isPost()) {
    		import('COM.Gaosi.Project');
    		$formData = $_POST['formData'];
    		parse_str($formData, $args);
    		$projectList = Project::getProjectList($args);
    		echo json_encode(array('rows'=>$projectList));
    	} else {
    		extract($_GET);
    		$url = $this->getUrl('projects');
			$this->assign(get_defined_vars());
			$this->display();
    	}
    }
    
    public function classTypes() {
    	if($this->isPost()) {
    		import('COM.Gaosi.ClassType');
    		$formData = $_POST['formData'];
    		parse_str($formData, $args);
    		$classTypeList = ClassType::getClassTypeList($args);
    		echo json_encode(array('rows'=>$classTypeList));
    	} else {
    		extract($_GET);
    		$url = $this->getUrl('classTypes');
			$this->assign(get_defined_vars());
			$this->display();
    	}
    }
}
?>