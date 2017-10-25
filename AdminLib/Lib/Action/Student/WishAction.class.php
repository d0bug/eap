<?php
class WishAction extends StudentCommAction {
	public function __construct() {
		parent::__construct();
	}
	
	public function xsc() {
		$wishModel = new WishModel();
		$this->display();
	}
	
	public function csg() {
		$this->display();
	}
	
	
}
?>