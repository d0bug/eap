<?php
import('ORG.Util.NCache');
class CampModel {
    private $tableName = '';
    private $prefix = 'Camp';

    public $dao = null;
    public $operator = null;
    private $cache = null;


    public function __construct() {


        $this->cache = NCache::getCache();

        $this->dao = Dao::getDao();
        $this->operator  = User::getLoginUser(C('USER_COOKIE_NAME'));
    }




   public function getClassList($data) {

   	  $cache_key = 'getClassList';
   		$sql = 'select cs.*,bs.scode,bs.sname,bs.nclassyear,bs.nsemester,bs.sprintteachers FROM Camp_class cs left join viewBS_Class bs on (bs.sCode = cs.sClassCode) where 1=1';
   		if(!empty($data['nClassYear'])) {
   			$sql .= ' and bs.nClassYear = '.(int)$data['nClassYear'];
   			$cache_key .= '_'.(int)$data['nClassYear'];
   		} else {
   			$cache_key .= '_0';
   		}
   		if(!empty($data['nSemester'])) {
   			$sql .= ' and bs.nSemester = '.(int)$data['nSemester'];
   			$cache_key .= '_ '.(int)$data['nSemester'];
   		} else {
   			$cache_key .= '_ 0';
   		}
   		//echo $sql;
   		//return $sql;
   		$cache_resuls = $this->cache->get($this->prefix, $cache_key);
	    if(!empty($cache_resuls)) {
	     //return $cache_resuls;
	    }

	    $results = $this->dao->getAll($sql);
       // $this->cache->set($this->prefix, $cache_key,$results);
        return $results;
   }


   public function getClassInfo($sClassCode,$nLessonid) {
   		$sql = 'select bs.sName,bl.Topic  from bs_lesson bl left join viewBS_Class bs on (bl.sClassCode = bs.sCode) where bl.sClassCode=\''.$sClassCode.'\' and bl.nLessonNo = '.$nLessonid;
   		$result = $this->dao->getRow($sql);
   		return $result;

   }

   public function getPaperList($data) {
   		$sql = 'select * from Camp_question ';
   		$condition = array();
   		if(isset($data['sClassCode'])) {
   			$condition[] = 'sClassCode =\''.$data['sClassCode'].'\'';
   		}
   		if(isset($data['nLessonid'])) {
   			$condition[] = 'nLessonid =\''.(int)$data['nLessonid'].'\'';
   		}
   		if(!empty($condition)) {
   			$sql .= ' where '.implode(' and ', $condition);
   		}
   		$sql .= ' order by nSign asc , nSort asc,nSubSort asc';
   		$results = $this->dao->getAll($sql);
   		return $results;



   }

   public function getQuestionInfo($id) {
   		$sql = 'select * from Camp_question where id ='.$id;
   		//echo $sql;
   		$result = $this->dao->getRow($sql);
   		return $result;
   }

   public function getQueationNumber() {
      $sql = ' select count(q.id) as num ,q.sClassCode,q.nLessonid  from Camp_question q  group by q.sClassCode,q.nLessonid';
      $results =  $this->dao->getAll($sql);

      $return = array();
      foreach($results as $value) {
        $return[trim($value['sclasscode'])][$value['nlessonid']] = $value['num'];
      }
      //print_r($return);
      return $return;
   }


   public function _insert($data,$table) {
      $sql = 'insert into '.$table.' ';
      $field = $condition = array();
      foreach($data as $k => $v) {
        $field[] = $k;

          $condition[] = '\''.$v.'\'';

      }
      $sql .= '('.implode(',', $field).') values ('.implode(',', $condition).');';
      //echo $sql;exit();
     return $this->dao->execute($sql);
    }

      public  function _update($data,$case,$table) {
	      $condition = array();
	      foreach($data as $field => $value) {

	          $condition[] = $field.' = \''.trim($value).'\'';


	      }
	      if(empty($condition)) {
	        return 0;
	      }
	      $sql = 'update '.$table.' SET '.implode(' , ', $condition).' ';
	      $where = array();
	      foreach($case as $field => $value) {

	          $where[] = $field.' = \''.trim($value).'\'';

	      }
	      if(!empty($where)) {
	      	$sql .= ' where '.implode(' and ', $where);
	      }
	     // echo $sql;exit();

	      return $this->dao->execute($sql);

    }


    public function _delete($data = array(),$table = '') {
      if(empty($data) || empty($table)) {
        return 0;
      }
      $sql = 'delete from '.$table.' ';
      $condition = array();
      foreach($data as $key => $value) {
        $condition[] = $key.'=\''.$value.'\'';
      }
      if(empty($condition)) {
        return 0;
      }
      $sql .= ' where '.implode(' and ', $condition);
     /// return $sql;
      return $this->dao->execute($sql);

    }


}

