<?php
abstract class ExamCommAction extends AppCommAction{
    public function __construct() {
        parent::__construct();
    }
    
    protected  function notNeedLogin() {
        return array();
    }
}

?>
