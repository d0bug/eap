<?php

class ExerciseAction {


	public function index() {
		echo 'index';
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


