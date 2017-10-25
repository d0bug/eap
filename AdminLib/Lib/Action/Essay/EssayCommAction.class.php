<?php
/*高思作文*/
abstract class EssayCommAction extends AppCommAction{
	protected $autoCheckPerm = false;
	public function __construct() {
		parent::__construct();
	}

	protected  function notNeedLogin() {
		return array();
	}

}

?>
