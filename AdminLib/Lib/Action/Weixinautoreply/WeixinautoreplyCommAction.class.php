<?php
abstract class WeixinautoreplyCommAction extends AppCommAction{
    protected $autoCheckPerm = false;
    public function __construct() {
        parent::__construct();
    }
    
    protected  function notNeedLogin() {
        return array();
    }
}
?>
