<?php
import('ORG.Util.NCache');
class CampNewsModel {
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


	/**
	 * 2014/8/29 13:50 添加 删除条件
	 */
    public function getNewsList() {
      $sql = 'select * from Camp_news WHERE nIsDeleted = "0" ';
      $results = $this->dao->getAll($sql);
      return $results;

    }
    public function getNewsInfo($data) {
      $sql = 'select top 1 * from camp_news where id = '.$data['id'];
      $result = $this->dao->getRow($sql);
      $result['sclasscode'] = array();
      if(!empty($result['nid'])) {
        $sql = 'select cnc.* from camp_news_class cnc  where cnc.nId='.(int)$result['nid'];
        //echo $sql;
        $results = $this->dao->getAll($sql);
        foreach($results as $value) {
          $result['sclasscode'][] = trim($value['sclasscode']);
        }
      }
      return $result;
    }


    public function getSmsList() {
      $sql = 'select * from Camp_sms ';
      $results = $this->dao->getAll($sql);
      return $results;


    }

    public function getPhoneList($sClassCode) {
        $sql = 'select sParents1Phone,sstudentname from viewrostershrink where bValid = 1 and sClassCode=\''.$sClassCode.'\'';
        $results = $this->dao->getAll($sql);
        return $results;
    }




   public function getClassList($data = array()) {

   	  $cache_key = 'getClassList';
   		$sql = 'select cs.sClassCode,bs.sClassName FROM Camp_class cs left join viewclassforeap bs on (bs.sCode = cs.sClassCode) where 1=1';
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
        $this->cache->set($this->prefix, $cache_key,$results);
        return $results;
   }


   public function getClassInfo($sClassCode,$nLessonid) {
   		$sql = 'select bs.sName,bl.Topic  from bs_lesson bl left join viewBS_Class bs on (bl.sClassCode = bs.sCode) where bl.sClassCode=\''.$sClassCode.'\' and bl.nLessonNo = '.$nLessonid;
   		$result = $this->dao->getRow($sql);
   		return $result;

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

    public function _delete($where,$table) {
      if(empty($where)) return 0;
      $condition = array();
      foreach($where as $field=>$value) {
        $condition[] = $field.' = \''.trim($value).'\'';
      }
      $sql = 'delete from '.$table.' where '.implode(' and ', $condition);
      return $this->dao->execute($sql);

    }


	/**
	 * 删除记录
	 */
	 public function delNewsById($id){
	 	$sql = '
	 		UPDATE camp_news
	 		SET nIsDeleted = "1"
	 		WHERE id = "' . $id . '"
	 	';


	 	$rs  = $this->dao->execute($sql);
	 	if($rs === false){
	 		return array('error' => true ,'message' => '删除失败');
	 	}

	 	return array('error' => false ,'message' => '删除成功');

	 }


}

