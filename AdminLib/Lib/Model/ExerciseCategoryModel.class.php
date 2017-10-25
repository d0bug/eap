<?php

class ExerciseCategoryModel {

    private $exerTable = '';
    public $dao = null;
    public $operator = null;

    public function __construct() {
        $this->exerTable = 'ex_dou_category';



        $this->dao = Dao::getDao('MSSQL_CONN');
        $this->operator  = User::getLoginUser(C('USER_COOKIE_NAME'));
    }
    public function getCategoryList($data = array()) {
        $sql = 'select * from '.$this->exerTable.' ';
        $condition = array();
        if(isset($data['status'])) {
            $condition[] = ' status = '.(int)$data['status'];
        }
        if(isset($data['is_show'])) {
            $condition[] = ' is_show = '.(int)$data['is_show'];
        }
        if(!empty($condition)) {
            $sql .= ' WHERE '.implode(' and ', $condition);
        }
        if(isset($data['order'])) {
            $sql .= ' '.$data['order'];
        } else {
            $sql .= ' order by sort_order ASC';
        }
        return $this->dao->getAll($sql);

    }
    public function getValue($id = 0,$key = '') {
        if(empty($id) || empty($key)) {
            return '';
        }
        $result = $this->getCategoryInfo(array('id'=>$id));
        if(isset($result[$key])) {
            return $result[$key];
        } else {
            return '';
        }
    }
    public function getCategoryInfo($data = array()) {
        $sql = 'select * from '.$this->exerTable.' ';
        $condition = array();
        if(!empty($data['id'])) {
                    $condition[] = 'id = '.(int)$data['id'];
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
    public function updateCategoryInfo($data = array(),$id = 0) {
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
    public function insertCategoryInfo($data = array()) {
        //dump($data);
        if(count($data) == 0)  {
            return 0;
        }
        $sql = ' INSERT INTO '.$this->exerTable.' (title,status,sort_order,is_show) values ("%s",%d,%d,%d)  ';
        $sql = sprintf($sql,$data['title'],$data['status'],$data['sort_order'],$data['is_show']);

        $this->dao->execute($sql);
        return $this->dao->affectRows();



    }




 }
