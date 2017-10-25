<?php
import('ORG.Util.NCache');
class TaskModel {
    private $tableName = '';
    private $prefix = 'Task';

    public $dao = null;
    public $operator = null;
    private $cache = null;


    public function __construct() {
        $this->tableName = 'viewClassForEAP';

        $this->cache = NCache::getCache();

        $this->dao = Dao::getDao();
        $this->operator  = User::getLoginUser(C('USER_COOKIE_NAME'));
    }



     public function getClassTypeList($nClassYear=2014,$nSeason = 0) {
          if(empty($nClassYear)) {
            $nClassYear = (int)date('Y');
          }
          if(empty($nSeason)) {
            $nMonth = (int)date('m');

          $nSeason = season($nMonth);
          }

          $cache_key = 'getClassTypeList_'.$nClassYear.'_'.$nSeason;
          $cache_resuls = $this->cache->get($this->prefix, $cache_key);


          if(!empty($cache_resuls)) {
            return $cache_resuls;
          }





          $sql = 'SELECT sCode,nGrade,sName FROM BS_ClassType where scode in (SELECT  DISTINCT(sclasstypecode) FROM viewClassForEAP  WHERE sDeptCode = \'DPBJ002\' AND bIsEnd = 0 AND bVirtual=0 and nClassYear='.$nClassYear.' and nSemester = '.$nSeason.' ) and bLongterm=1 ORDER BY nGrade%100  ;';





           $results = $this->dao->getAll($sql);
           $this->cache->set($this->prefix, $cache_key,$results);
           return $results;


   }
   public function getClassList($data) {
   	    $cache_key = 'getClassList';
   		$sql = 'select sCode,sName FROM viewBS_Class where bIsEnd = 0';
   		if(isset($data['nClassYear'])) {
   			$sql .= ' and nClassYear = '.(int)$data['nClassYear'];
   			$cache_key .= '_'.(int)$data['nClassYear'];
   		} else {
   			$cache_key .= '_0';
   		}
   		if(isset($data['nSemester'])) {
   			$sql .= ' and nSemester = '.(int)$data['nSemester'];
   			$cache_key .= '_ '.(int)$data['nSemester'];
   		} else {
   			$cache_key .= '_ 0';
   		}
   		if(isset($data['sClassTypeCode'])) {
   			$sql .= ' and sClassTypeCode = \''.addslashes(trim($data['sClassTypeCode'])).'\'';
   			$cache_key .= '_ '.trim($data['sClassTypeCode']);
   		} else {
   			$cache_key .= '_ null';
   		}
   		//return $sql;
   		$cache_resuls = $this->cache->get($this->prefix, $cache_key);
	    if(!empty($cache_resuls)) {
	      //return $cache_resuls;
	    }

	    $results = $this->dao->getAll($sql);
        $this->cache->set($this->prefix, $cache_key,$results);
        return $results;
   }
   public function getTopic($data) {
      $sql = ' select distinct  replace(replace(bl.topic,\' \',\'\'), char(10),\'\') topic from  bs_lesson bl left  join viewclassforeap vf on(vf.sclasscode = bl.sclasscode) where vf.sclasstypecode = \''.$data['sClassTypeCode'].'\' and vf.nclassyear = '.$data['nClassYear'].' and nsemester ='.$data['nSemester'].' and bl.topic is not null';
      //echo $sql;exit();
       $results = $this->dao->getAll($sql);
       /*$text = '';
       foreach($results as $value) {
        $text .= $value['topic']."\n";
       }
       file_put_contents('/data/wwwroot/eap/', $text);*/
       return $results;
   }

   public function getQuestionListTotal() {
   		$sql = 'select count(id) as num FROM TASK_question_list';
      $condition = array();
      if(!empty($data['nYear'])) {
        $condition[] = 'nYear='.(int)$data['nYear'];
      }
      if(!empty($data['nSeason'])) {
        $condition[] = 'nSeason='.(int)$data['nSeason'];
      }
      if(!empty($data['nType'])) {
        $condition[] = 'nType='.(int)$data['nType'];
      }
      if(!empty($data['sClassTypeCode'])) {
        $condition[] = 'sClassTypeCode=\''.addslashes($data['sClassTypeCode']).'\'';
      }
      if(!empty($condition)) {
        $sql .= ' where '.implode(' and ', $condition);
      }
   		return $this->dao->getOne($sql);
   }
    /**
    * default:object
    * @param [array] $data
    * @return [type] [description]
    */
   public function getOptionList($nQuestionid) {
   		$sql = 'SELECT  * FROM TASK_question_objective where nQuestionid = '.(int)$nQuestionid;
   		return $this->dao->getAll($sql);

   }
   public function getOptionSubjectList($nQuestionid) {
      $sql = 'SELECT  * FROM TASK_question_subjective where nQuestionid = '.(int)$nQuestionid;
      return $this->dao->getAll($sql);
   }

   public function getQuestionList($data) {
   		$sql = 'select tql.*,ct.sName as sClassTypeName FROM TASK_question_list tql left join BS_ClassType ct on (tql.sClassTypeCode = ct.sCode )';
      $condition = array();
      if(!empty($data['nYear'])) {
        $condition[] = 'tql.nYear='.(int)$data['nYear'];
      }
      if(!empty($data['nSeason'])) {
        $condition[] = 'tql.nSeason='.(int)$data['nSeason'];
      }
      if(!empty($data['nType'])) {
        $condition[] = 'tql.nType='.(int)$data['nType'];
      }
      if(!empty($data['sClassTypeCode'])) {
        $condition[] = 'tql.sClassTypeCode=\''.addslashes($data['sClassTypeCode']).'\'';
      }
      if(!empty($condition)) {
        $sql .= ' where '.implode(' and ', $condition);
      }
   		return $this->dao->getLimit($sql, $data['page'], 20, 'order by id DESC');
   		//return $results = $this->dao->getAll($sql);

   }

   public function getQuestionInfo($id) {
   		$sql = 'select top 1 tql.*,vcfe.sName FROM TASK_question_list tql left join bs_classtype vcfe on (tql.sClassTypeCode = vcfe.sCode ) where tql.id='.$id;
   		return $this->dao->getRow($sql);
   }
    public function getQuestionInfoByCode($nQuestionid) {
      $cache_key = 'getQuestionInfoByCode_'.$nQuestionid;
      $cache_resuls = $this->cache->get($this->prefix, $cache_key);
      if(!empty($cache_resuls)) {
        return $cache_resuls;
      }
      $sql = 'select top 1 tql.*,vcfe.sName FROM TASK_question_list tql left join bs_classtype vcfe on (tql.sClassTypeCode = vcfe.sCode ) where tql.nQuestionid='.$nQuestionid;
      $result = $this->dao->getRow($sql);
      $this->cache->set($this->prefix, $cache_key,$result);
      return $result;
   }




   public function insertQuestion($data,$n = 1) {
   	    $data['dCreateTime'] = $data['dModifyTime'] = date('Y-m-d H:i:s');
   		$field = $vlues = array();
   		foreach($data as $key => $value) {
   			$field[] = $key;
   			if(is_numeric($value)) {
   				$values[] = $value;
   			} else {
   				$values[] = '\''.$value.'\'';
   			}
   		}
   		if($data['nType'] == 1 && $n == 1) {
   			$option['nQuestionid'] = $data['nQuestionid'];
   			$this->multyInsertOption($data,$data['nQuestionNum']);
   		}
      if($data['nType'] == 2 && $n == 1) {
        $option['nQuestionid'] = $data['nQuestionid'];
        $this->multyInsertOption($data,$data['nQuestionNum'],'TASK_question_subjective');
      }
       if($data['nType'] == 3 && $n == 1) {
        $option['nQuestionid'] = $data['nQuestionid'];
        $this->multyInsertOption($data,$data['nQuestionNum'],'TASK_question_fillin');
      }




   		$sql = 'insert into TASK_question_list ('.implode(',', $field).') values ('.implode(',', $values).')';
   		return $this->dao->execute($sql);


   }
   public function multyInsertOption($data,$n,$table = 'TASK_question_objective') {

      $sql = '';
      for($i = 1;$i<=$n;$i++) {
        $sql .= 'insert into '.$table.' (nQuestionid,sort) values ('.$data['nQuestionid'].','.$i.');';
      }

    return $this->dao->execute($sql);

   }
   public function insertOption($data,$table = 'TASK_question_objective') {
   		$field = $vlues = array();
   		foreach($data as $key => $value) {
   			$field[] = $key;
   			if(is_numeric($value)) {
   				$values[] = $value;
   			} else {
   				$values[] = '\''.$value.'\'';
   			}
   		}


   		$sql = 'insert into '.$table.' ('.implode(',', $field).') values ('.implode(',', $values).')';
   		$this->dao->execute($sql);
      return 1;
   }
   public function multyUpdateOption($data,$nQuestionid,$table = 'TASK_question_objective') {

      $sql = '';
   		foreach($data as $sort => $v) {
        $condition = array();
          foreach($v as $key => $value){


       				$condition[] = $key.'= \''.$value.'\'';

         }
       $sql .= 'update '.$table.' set '.implode(',', $condition).' where nQuestionid='.(int)$nQuestionid.' and sort='.(int)$sort.';';
   		}


     // echo $sql,'<br>';exit();

   		return $this->dao->execute($sql);
   }

   public function delete($id) {
      $result = $this->getQuestionInfo($id);
      if($result['ntype'] == 1) {
        $sql = 'delete FROM TASK_question_objective where nQuestionid='.(int)$result['nquestionid'];
        $this->dao->execute($sql);


      }
      if($result['ntype'] == 2) {
         $sql = 'delete FROM TASK_question_subjective where nQuestionid='.(int)$result['nquestionid'];
        $this->dao->execute($sql);
      }
       if($result['ntype'] == 3) {
         $sql = 'delete FROM TASK_question_fillin where nQuestionid='.(int)$result['nquestionid'];
        $this->dao->execute($sql);
      }
      $sql = 'delete FROM TASK_question_list where id='.(int)$id;
        return $this->dao->execute($sql);


      return false;
   }


   public function getList($data,$table) {
      $top = '';

      $condition = array();
      foreach($data as $field => $value){
        $condition[] = $field.'= \''.$value.'\'';
      }
      if(empty($condition)) {
        $top = ' 100 ';
      }
      $sql = 'select '.$top.' * from '.$table.' where '.implode(' and ', $condition);


      return $this->dao->getAll($sql);
   }
/**
 * --------------------------------------------------------------------
 *
 */
   public function getAllobjectiveList() {
     $sql = 'select  top 1000 m.id as mid, m.saliascode,l.* from task_student_main m left join task_question_list l on(l.nquestionid = m.nquestionid) where l.nQuestionid > 1 and m.sclasscode =\'\'' ;
      return $this->dao->getAll($sql);

   }

  public function getClassCode($sAliascode,$topic) {
    $cache_key = 'getClassCode_'.$sAliascode.'_'.base64_encode($topic);
    $cache_resuls = $this->cache->get($this->prefix, $cache_key);
      if(!empty($cache_resuls)) {
        return $cache_resuls;
      }

    $sql = '  select n.sclasscode from bs_lesson n left join viewrostershrink k on (k.sclasscode = n.sclasscode) left join viewBS_Class s on (n.sclasscode = s.scode)
 where k.saliascode = \''.$sAliascode.'\'  and replace(replace(n.topic, \' \', \'\'),CHAR (10),\'\') = \''.$topic.'\'';
 $result =  $this->dao->getOne($sql);


        $this->cache->set($this->prefix, $cache_key,$result);
        return $result;

  }

  public function updateClassCode($id,$sclasscode) {
    $sql = 'update task_student_main set sClassCode =\''.$sclasscode.'\' where id ='.$id;
      $this->dao->execute($sql);
  }

   public function getAnalysisList($data,$table='task_student_main') {
      $sql = 'select count(m.id) as num ,m.sClassCode,m.nQuestionid from '.$table.' m left join task_question_list tql on(m.nquestionid=tql.nquestionid) ';
      $condition = array();
      $condition[] = 'tql.nQuestionid > 0';
      if(!empty($data['nYear'])) {
        $condition[] = 'tql.nYear='.(int)$data['nYear'];
      }
      if(!empty($data['nSeason'])) {
        $condition[] = 'tql.nSeason='.(int)$data['nSeason'];
      }
      if(!empty($data['nType'])) {
        $condition[] = 'tql.nType='.(int)$data['nType'];
      }
      if(!empty($data['sClassTypeCode'])) {
        $condition[] = 'tql.sClassTypeCode=\''.addslashes($data['sClassTypeCode']).'\'';
      }
      if(!empty($data['sClassCode'])) {
        $condition[] = 'm.sClassCode=\''.addslashes($data['sClassCode']).'\'';
      }
      if(!empty($condition)) {
        $sql .= ' where '.implode(' and ', $condition);
      }
      $sql .= ' group by m.sClassCode,m.nQuestionid';
      //echo $sql;exit();
      //return $this->dao->getLimit($sql, $data['page'], 20, 'order by id DESC');
      return $results = $this->dao->getAll($sql);

   }


   public function getClassInfoByCode($sClassCode,$topic) {
    $cache_key = 'getClassInfoByCode_'.$sClassCode.'_'.base64_encode($topic);
     $cache_resuls = $this->cache->get($this->prefix, $cache_key);
    if(!empty($cache_resuls)) {
        return $cache_resuls;
      }
    $sql = ' select count(scls.id) as totalnum from BS_StudentClassLessonScore scls left join bs_lesson l on (l.id = scls.lessonid)  where l.sClassCode=\''.$sClassCode.'\' and  replace(replace(l.topic, \' \', \'\'),CHAR (10),\'\') = \''.$topic.'\'';
   // echo $sql;exit();
    $result = $this->dao->getRow($sql);
      $this->cache->set($this->prefix, $cache_key,$result['totalnum']);
      //print_r($result);echo '<br>';
      return $result['totalnum'];

   }

   public function getTeacher($sClassCode,$topic) {
    $cache_key = 'getTeacher_'.$sClassCode.'_'.base64_encode($topic);
     $cache_resuls = $this->cache->get($this->prefix, $cache_key);
    if(!empty($cache_resuls)) {
        return $cache_resuls;
      }
      $sql = 'select  t.sName from bs_lesson l left join bs_teacher t on (l.sTeacherCode = t.sCode) where l.sClassCode=\''.$sClassCode.'\' and  replace(replace(l.topic, \' \', \'\'),CHAR (10),\'\') = \''.$topic.'\'';
       $result = $this->dao->getRow($sql);
      $this->cache->set($this->prefix, $cache_key,$result['sname']);
      //print_r($result);echo '<br>';
      return $result['sname'];

   }



   //-----------------------------------------------------------------
   public function getTestData($sAliasCode,$nType) {
      if($nType == 1) {
        $table = 'task_student_main';
      } elseif($nType == 2) {
        $table = 'TASK_student_main_subjective';
      } elseif($nType ==3) {
        $table = 'task_student_main_fillin';
      }
      $sql = ' select l.*,m.sAliasCode from task_question_list l left join '.$table.' m on (m.nQuestionid = l.nQuestionid) where m.sAliascode=\''.$sAliasCode.'\'';
      //echo $sql;exit();
      return $this->dao->getAll($sql);

   }
   public function deleteTestData($nQuestionid,$sAliasCode,$nType) {
      if($nType == 1) {
        $table1 = 'task_student_main';
        $table2 = 'task_student_sub';
      } elseif($nType == 2) {
        $table1 = 'TASK_student_main_subjective';
        $table2 = 'task_student_sub_pic';
      } elseif($nType ==3) {
        $table1 = 'task_student_main_fillin';
        $table2 = 'task_student_sub_fillin';
      }
     $sql1 = 'delete from '.$table1.' where sAliasCode=\''.$sAliasCode.'\' and nQuestionid='.$nQuestionid.' ';
     $sql1 = 'delete from '.$table2.' where sAliasCode=\''.$sAliasCode.'\' and nQuestionid='.$nQuestionid.' ';
      //echo $sql;exit();
      $this->dao->execute($sql);
      return 1;

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


}

