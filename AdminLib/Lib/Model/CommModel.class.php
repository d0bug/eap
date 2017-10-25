<?php
import('COM.Dao.Dao');
class CommModel {
	public $dao = null;
	protected $operator = '';
	public function __construct() {
		$this->dao = Dao::getDao('MYSQL_CONN');
		if(class_exists('User', false)) {
			$user = User::getLoginUser();
			$this->operator = $user->getUserKey();
		}
	}
}
?>