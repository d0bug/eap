<?php
abstract class GoodsCommAction extends AppCommAction{
    protected $autoCheckPerm = true;
    public function __construct() {
        parent::__construct();
    }
    
    protected  function notNeedLogin() {
        return array();
    }
    
    
}

?>
