<?php

class ExerciseModel {

    private $examTable = '';
    public $dao = null;
    public $operator = null;

    public function __construct() {
        $this->examTable = 'ex_dou_knowledge';



        $this->dao = Dao::getDao();
        $this->operator  = User::getLoginUser(C('USER_COOKIE_NAME'));
    }
    public function getKnowledgeList($data = array()) {
        $sql = 'select * from '.$this->examTable.' ';
        $condition = array();

    }




 }
