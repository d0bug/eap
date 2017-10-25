<?php

class ExerciseKnowledgeModel {

    private $exerTable = '';
    public $dao = null;
    public $operator = null;

    public function __construct() {
        $this->exerTable = 'ex_dou_knowledge';



        $this->dao = Dao::getDao('MSSQL_CONN');
        $this->operator  = User::getLoginUser(C('USER_COOKIE_NAME'));
    }
    public function getKnowledgeList($data = array()) {
        $sql = 'select k.*,c.title as category_name from '.$this->exerTable.' k left join ex_dou_category c on(k.category_id = c.id) ';
        $condition = array();
        if(isset($data['status'])) {
            $condition[] = ' k.status = '.(int)$data['status'];
        }
        if(!empty($condition)) {
            $sql .= ' WHERE '.implode(' and ', $condition);
        }
        if(isset($data['order'])) {
            $sql .= ' '.$data['order'];
        } else {
            $sql .= ' order by k.sort_order ASC';
        }
        return $this->dao->getAll($sql);

    }
    public function getValue($id = 0,$key = '') {
        if(empty($id) || empty($key)) {
            return '';
        }
        $result = $this->getKnowledgeInfo(array('id'=>$id));
        if(isset($result[$key])) {
            return $result[$key];
        } else {
            return '';
        }
    }
    public function getKnowledgeInfo($data = array()) {
        $sql = 'select k.*,c.title as category_name from '.$this->exerTable.' k left join ex_dou_category c on(k.category_id = c.id) ';
        $condition = array();

        if(!empty($data['id'])) {
            $condition[] = 'k.id = '.(int)$data['id'];
        }
        if(!empty($condition)) {
            $sql .= ' WHERE '.implode(' and ', $condition);
        }

        $resutls = $this->dao->getAll($sql);
        if(isset($resutls[0])) {
            return $resutls[0];
        } else {
            return array();
        }

    }
    public function updateKnowledgeInfo($data = array(),$id = 0) {
        //dump($data);
        if(count($data) == 0 || empty($id)) {
            return 0;
        }
        $sql = ' UPDATE '.$this->exerTable.'  ';
        $chenge = array();
        foreach($data as $field => $value) {
            if(is_numeric($value)) {
                $chenge[] = $field.' = '.$value;
            } else {
                $chenge[] = $field.' = \''.$value.'\'';
            }
        }
        if(!empty($chenge)) {
            $sql .= ' set '.implode(',', $chenge);
            $sql .= ' where id='.$id;
            //return $sql;
            $this->dao->execute($sql);
            return $this->dao->affectRows();
        } else {
            return 0;
        }


    }
    public function insertKnowledgeInfo($data = array()) {
        //dump($data);
        if(count($data) == 0)  {
            return 0;
        }
        $sql = ' INSERT INTO '.$this->exerTable.' (title,category_id,mod_max,flash_url,status,sort_order) values ("%s",%d,%d,"%s",%d,%d)  ';
        $sql = sprintf($sql,$data['title'],$data['category_id'],$data['mod_max'],$data['flash_url'],$data['status'],$data['sort_order']);

        $this->dao->execute($sql);
        return $this->dao->affectRows();



    }




 }
