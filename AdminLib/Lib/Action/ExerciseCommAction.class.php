<?php

class ExerciseCommAction extends ModularCommAction{
    protected $knowledgeModel = null;
    protected $questionModel = null;

    public function __construct() {
    	parent::__construct();
    	$this->knowledgeModel = D('ExerciseKnowledge');
    	$this->questionModel = D('ExerciseQuestion');
    }

    protected function notNeedLogin() {
		return array('MODULAR-MODULARAPPLY-CREATE_JSFORM','MODULAR-MODULARAPPLY-AJAXSAVEFORMDATA','MODULAR-MODULARAPPLY-AJAX_ATTR_RELATION');
	}
	private function getList() {
		//
	}

	private function getForm() {
		//
	}

}


