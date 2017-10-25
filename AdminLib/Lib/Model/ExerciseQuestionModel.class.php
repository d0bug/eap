<?php

class ExerciseQuestionModel {

    private $questionTable = '';
    public $dao = null;
    public $operator = null;

    public function __construct() {
        $this->questionTable = 'ex_dou_question';



        $this->dao = Dao::getDao('MSSQL_CONN');
        $this->operator  = User::getLoginUser(C('USER_COOKIE_NAME'));
    }
    public function getQuestionListTotal() {
        $sql = 'select count(id) as num from '.$this->questionTable.' ';
        $condition = array( );


        if(isset($data['status'])) {
            $condition[] = ' status = '.(int)$data['status'];
        }

        if(!empty($condition)) {
            $sql .= ' WHERE '.implode(' and ', $condition);
        }

        return $this->dao->getOne($sql);
    }
    public function getQuestionList($data = array()) {
        $sql = 'select * from '.$this->questionTable.' ';
        $condition = array( );


        if(isset($data['status'])) {
            $condition[] = ' status = '.(int)$data['status'];
        }

        if(!empty($condition)) {
            $sql .= ' WHERE '.implode(' and ', $condition);
        }
        if(isset($data['limit'])) {
            list($currentPage,$pageSize) = explode(',', $data['limit']);
            return $this->dao->getLimit($sql, $currentPage, $pageSize, $data['order']);
        } else {
            $sql .= ' order by sort_order DESC';
            return $this->dao->getAll($sql);
        }



    }
    public function getQuestionInfo($data = array()) {

        $sql = 'select * from '.$this->questionTable.' ';
        $condition = array();
        if(!empty($data['id'])) {
            $condition[] = 'id = '.(int)$data['id'];
        }
        if(!empty($condition)) {
            $sql .= ' WHERE '.implode(' and ', $condition);
        }
        //echo $sql;
        $resutls = $this->dao->getAll($sql);
        if(isset($resutls[0])) {
            $result = $resutls[0];
            $content = $this->getContent($result['content']);
            if(empty($content)) {
                return $result;
            } else {
                return array_merge($result,$content);
            }


        } else {
            return array();
        }

    }
    public function updateQuestionInfo($data = array(),$id = 0) {
        //dump($data);
        if(count($data) == 0 || empty($id)) {
            return 0;
        }

        $data = $this ->beforehandProcess($data);

        //print_r($data);exit();
        $sql = ' UPDATE '.$this->questionTable.'  ';
        $chenge = array();
        if(isset($data['title'])) {
            $chenge[] = 'title=\''.addslashes(trim($data['title'])).'\'';
        }
        if(isset($data['mod_id'])) {
            $chenge[] = 'mod_id='.(int)$data['mod_id'];
        }
        if(isset($data['difficulty'])) {
            $chenge[] = 'difficulty='.(int)$data['difficulty'];
        }
        if(isset($data['category_id'])) {
            $chenge[] = 'category_id='.(int)$data['category_id'];
        }
        if(isset($data['knowledge_id'])) {
            $chenge[] = 'knowledge_id='.(int)$data['knowledge_id'];
        }
        if(isset($data['sort_order'])) {
            $chenge[] = 'sort_order='.(int)$data['sort_order'];
        }
        if(isset($data['status'])) {
            $chenge[] = 'status='.(int)$data['status'];
        }
        if(isset($data['content'])) {
            $chenge[] = 'content='.(int)$data['content'];
        }
        if(isset($data['solve_flash'])) {
            $chenge[] = 'solve_flash="'.trim($data['solve_flash']).'"';
        }




        if(!empty($chenge)) {
            $sql .= ' set '.implode(',', $chenge);
            $sql .= ' where id='.$id;
            //echo $sql;exit();
            $this->dao->execute($sql);
            return $this->dao->affectRows();
        } else {
            return 0;
        }


    }
    public function  changeStatus($data = array(),$id = 0) {
        if(count($data) == 0 || empty($id)) {
            return 0;
        }
        $sql = ' UPDATE '.$this->questionTable.'  ';
        $chenge = array();
        if(isset($data['title'])) {
            $chenge[] = 'title=\''.addslashes(trim($data['title'])).'\'';
        }
        if(isset($data['mod_id'])) {
            $chenge[] = 'mod_id='.(int)$data['mod_id'];
        }
        if(isset($data['difficulty'])) {
            $chenge[] = 'difficulty='.(int)$data['difficulty'];
        }
        if(isset($data['category_id'])) {
            $chenge[] = 'category_id='.(int)$data['category_id'];
        }
        if(isset($data['knowledge_id'])) {
            $chenge[] = 'knowledge_id='.(int)$data['knowledge_id'];
        }
        if(isset($data['sort_order'])) {
            $chenge[] = 'sort_order='.(int)$data['sort_order'];
        }
        if(isset($data['status'])) {
            $chenge[] = 'status='.(int)$data['status'];
        }
        if(isset($data['content'])) {
            $chenge[] = 'content='.(int)$data['content'];
        }
        if(isset($data['solve_flash'])) {
            $chenge[] = 'solve_flash="'.trim($data['solve_flash']).'"';
        }






        if(!empty($chenge)) {
            $sql .= ' set '.implode(',', $chenge);
            $sql .= ' where id='.$id;
            //echo $sql;exit();
            $this->dao->execute($sql);
            return $this->dao->affectRows();
        } else {
            return 0;
        }

    }
    public function insertQuestionInfo($data = array()) {
        //dump($data);
        if(count($data) == 0)  {
            return 0;
        }
        $data = $this ->beforehandProcess($data);
        $sql = ' INSERT INTO '.$this->questionTable.' (title,mod_id,difficulty,category_id,knowledge_id,status,sort_order,content,solve_flash) values ("%s",%d,%d,%d,%d,%d,%d,%d,"%s")  ';
        $sql = sprintf($sql,$data['title'],$data['mod_id'],$data['difficulty'],$data['category_id'],$data['knowledge_id'],$data['status'],$data['sort_order'],$data['content'],$data['solve_flash']);

        $this->dao->execute($sql);
        return $this->dao->affectRows();



    }
    private function beforehandProcess($post = array()) {
        if(count($post) == 0|| empty($post)) {
            return 0;
        }

        $data = array();
        if(isset($post['question'])) {
            $data['question'] = $post['question'];
            unset($post['question']);
        }
        if(isset($post['answer'])) {
            $data['answer'] = $post['answer'];
            unset($post['answer']);
        }
        if(isset($post['answers'])) {
            $data['answers'] = $post['answers'];
            unset($post['answers']);
        }
        $data = serialize($data);
        $data = base64_encode($data);
       // $data = htmlspecialchars($data,ENT_QUOTES);
        if(empty($post['content'])) {
            $sql = ' INSERT INTO ex_dou_question_storage ([content]) values (\''.$data.'\')  ';


            $this->dao->execute($sql);
            $post['content'] = $this->dao->lastInsertId();
        } else {
             $sql = ' UPDATE ex_dou_question_storage set [content] = \''.$data.'\' where id = '.$post['content'];


            $this->dao->execute($sql);

        }


        return $post;



    }
    public function getContent($id = 0) {
        if(empty($id)) {
            return array();
        }
         $sql = 'select content from ex_dou_question_storage where id='.$id;
         $content = $this->dao->getOne($sql);
         if(empty($content)) {
            return array(

                );
         }

         $content = base64_decode($content);
         $content = unserialize($content);
        // $content = htmlspecialchars_decode($content,ENT_QUOTES);
         //echo '<pre>';print_r(unserialize($content));exit;

         return $content;


    }
    public function getKnowledgeList() {
        $sql = 'select id,title from ex_dou_knowledge where status= 1';
        $results = $this->dao->getAll($sql);
        $data = array();
        foreach($results as $value) {
            $data[$value['id']] = $value['title'];
        }
        return $data;


    }




 }
