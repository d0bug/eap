<?php
class ReserveStudentModel {
    private $tableName = '';
    private $examTable = '';
    public $dao = null;
    public $operator = null;

    public function __construct() {
        $this->tableName = 'res_student_list';

        $this->dao = Dao::getDao('MSSQL_CONN');
        $this->operator  = User::getLoginUser(C('USER_COOKIE_NAME'));
    }

   public function insertData() {

   }
   public function getStudentsList($data = array()) {
   	    $sql = 'select * from res_student_list ';
   		$condition = array();
   		if(!empty($data['stucode'])) {
   			$condition[] = 'stucode = \''.(int)$data['stucode'].'\'';
   		}
   		if(isset($data['week_id'])) {
   			$condition[] = 'week_id = \''.(int)$data['week_id'].'\'';
   		}
   		if(isset($data['class_id'])) {
   			$condition[] = 'class_id = \''.(int)$data['class_id'].'\'';
   		}
      if(isset($data['list_id'])) {
        $condition[] = 'list_id = \''.(int)$data['list_id'].'\'';
      }
   		if(isset($data['status'])) {
   			$condition[] = 'status = \''.(int)$data['status'].'\'';
   		}
   		if(!empty($condition)) {
   			$sql .= ' where '.implode(' and ', $condition);
   		}
   		return $this->dao->getAll($sql);
   }

   public function formatStuList($data = array(),$formInfo = array()) {
       if(count($data) == 0 || count($formInfo) == 0) {
        return array();
       }
       $results = array();
       foreach($data as $value) {
          $results[$value['stucode']] =
       }

   }




}
?>
