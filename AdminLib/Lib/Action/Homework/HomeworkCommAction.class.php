<?php
abstract class HomeworkCommAction extends AppCommAction{
    protected $autoCheckPerm = false;
    public function __construct() {
        parent::__construct();
    }
    
    protected  function notNeedLogin() {
        return array();
    }
    public function checkPermission($actionName){
    	$r = $this->writeCheck($this->getAclKey($actionName));
    	if ($r !== true){
    		$this->error('您没有权限执行此操作');
    	}
    }
}
?>
