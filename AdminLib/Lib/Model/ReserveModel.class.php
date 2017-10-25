<?php
import('ORG.Util.NCache');
class ReserveModel {
    private $tableName = '';
    private $examTable = '';
    public $dao = null;
    public $operator = null;
    private $cache = null;
    private $stuTable = '';

    public function __construct() {
        $this->tableName = 'res_active_list';
        $this->kyTable = 'res_table_info';
        $this->infoTable = 'res_class_info';
        $this->stuTable = 'res_student_list';
        $this->cache = NCache::getCache();

        $this->dao = Dao::getDao();
        $this->operator  = User::getLoginUser(C('USER_COOKIE_NAME'));
    }


     public function getStudentsTotal($data = array()) {
        $sql = 'select count(id) as num from '.$this->stuTable.' ';
        $condition = array();
        if(!empty($data['stucode'])) {
            $condition[] = 'stucode = \''.addslashes($data['stucode']).'\'';
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
        if(isset($data['status_range'])) {
            $condition[] = $data['status_range'];
        }
        if(!empty($condition)) {
            $sql .= ' where '.implode(' and ', $condition);
        }
        return $this->dao->getOne($sql);
   }
     public function getStudentsList($data = array()) {
        $sql = 'select * from '.$this->stuTable.' ';
        $condition = array();
        if(!empty($data['stucode'])) {
            $condition[] = 'stucode = \''.addslashes($data['stucode']).'\'';
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
        if(isset($data['status_range'])) {
            $condition[] = $data['status_range'];
        }
        if(!empty($condition)) {
            $sql .= ' where '.implode(' and ', $condition);
        }
        if(isset($data['limit']) && is_numeric($data['limit'])) {

            return $this->dao->getLimit($sql,$data['limit'] , 20, 'order by id');
        } else {
           return $this->dao->getAll($sql);
        }

   }

   public function getStudentInfo($data) {
        if(empty($data['stucode'])) {
            return array();
        }
        $results = $this->getStudentsList($data);
        $data = array();
        foreach($results as $value) {
            $data[$value['class_id']][] = $value['week_id'];
        }
        return $data;


   }


    public function getReserveList($id = 1) {


        $sql = 'SELECT * FROM '.$this->tableName.' WHERE id ='.(int)$id .' and status = 1';
        $result = $this->dao->getRow($sql);
        if(empty($result['id'])) {
            return array();
        }
        $cache_key = 'active_list_'.$result['id'];
        $cache_resuls = $this->cache->get('reserve', $cache_key);

        if($cache_resuls) {

            //return $cache_resuls;
        }
        $results = $this->getTableInfo($result['id']);
       // dumps($result);
         //dumps($results);
        $result['week_name'] = array();
        $result['class_name'] = array();
        foreach ($results as  $value) {
            if(trim($value['key']) == 'week_name') {
                $result['week_name'][$value['sort']] = $value['value'];
            }
            if(trim($value['key']) == 'class_name') {
                $result['class_name'][$value['sort']]  = $value['value'];
           }
        }
        //dumps($result);exit;
        $this->cache->set('reserve', $cache_key,$result);

        return $result;

    }
    public function getTableInfo($list_id = 0) {
        $list_id = (int)$list_id;
        if(empty($list_id)) {
            return array();
        }
        $cache_key = 'getTableInfo_'.$list_id;
        $cache_resuls = $this->cache->get('reserve', $cache_key);

        if($cache_resuls) {

            //return $cache_resuls;
        }
        $sql = 'SELECT * FROM '.$this->kyTable.' WHERE list_id ='.$list_id;
        //dumps($this->dao->getAll($sql));exit;
        $results =  $this->dao->getAll($sql);
        $this->cache->set('reserve', $cache_key,$results);
        return $results;

    }
    public function getReserveInfo($list_id = 0) {
        $list_id = (int)$list_id;
        if(empty($list_id)) {
            return array();
        }
         $cache_key = 'getReserveInfo_'.$list_id;
        $cache_resuls = $this->cache->get('reserve', $cache_key);

        if($cache_resuls) {

            //return $cache_resuls;
        }
        $sql = 'SELECT * FROM '.$this->infoTable.' WHERE list_id ='.$list_id;

        $results = $this->dao->getAll($sql);
        $this->cache->set('reserve', $cache_key,$results);
        return $results;


    }
    public function insertReserveInfo($post,$list_id = 0) {
        if(empty($list_id)) {
            return 0;
        }

        $i = 0;

        foreach($post as $week_id => $arr1) {
            foreach( $arr1 as $class_id => $value) {
                $sql ='INSERT INTO '.$this->infoTable.' (date,begin_time,end_time,apply_num,allow_num,time_type,list_id,class_id,week_id) VALUES ';
                $date = strtotime($value['date']);
                $begin_time = strtotime($value[data].' '.$value['begin_time']);
                $end_time = strtotime($value[data].' '.$value['end_time']);
                $apply_num = (int)$value['apply_num'];
                $allow_num = (int)$value['allow_num'];
                $time_type = (int)$value['time_type'];
                $list_id = (int)$list_id;


                $sql .= sprintf(' (%d,%d,%d,%d,%d,%d,%d,%d,%d) ',$value['date'],$begin_time,$end_time,$apply_num,$allow_num,$time_type,$list_id,$class_id,$week_id);
                 $this->dao->execute($sql);
                 $i += $this->dao->affectRows();
            }
        }


        return $i;

    }
    public function updateReserveInfo($post,$list_id) {
        $sqlformat = 'UPDATE '.$this->infoTable.' SET date=%d, begin_time=\'%d\', end_time=\'%d\', apply_num=\'%d\', allow_num=\'%d\', time_type=\'%d\', list_id=\'%d\', class_id=\'%d\' , week_id=\'%d\' WHERE (id=\'%d\')';



        $num = 0;
        foreach($post as $week_id => $arr1) {
            foreach( $arr1 as $class_id => $value) {
                $sql = '';
                $id = $value['id'];
                $date = strtotime($value['date']);
                $begin_time = strtotime($value[data].' '.$value['begin_time']);
                $end_time = strtotime($value[data].' '.$value['end_time']);
                $apply_num = (int)$value['apply_num'];
                $allow_num = (int)$value['allow_num'];
                $time_type = (int)$value['time_type'];

                $sql = sprintf($sqlformat,$date,$begin_time,$end_time,$apply_num,$allow_num,$time_type,$list_id,$class_id,$week_id,$id);
                $this->dao->execute($sql);
                $num += $this->dao->affectRows();




            }
        }


        return $num;

    }
     public function insertStuInfo($data = array()) {
        if(count($data) == 0) {
            return 0;
        }
        $i = 0 ;
        foreach($data as $value) {
            $sql ='INSERT INTO '.$this->stuTable.' (list_id,class_id,week_id,stucode,status) VALUES (%d,%d,%d,"%s",%d) ';
            $sql = sprintf($sql,$value['list_id'],$value['class_id'],$value['week_id'],$value['stucode'],$value['status']);
            $this->dao->execute($sql);
            $i += $this->dao->affectRows();

        }
        $this->classStatistics($list_id);
        return $i;


    }
    public function updateStuInfo($list_id = 0,$stucode='',$data = array()) {
        if(count($data) == 0 || empty($list_id) || empty($stucode)) {
            return 0;
        }

        $sql = 'DELETE FROM '.$this->stuTable.'  WHERE list_id = %d and stucode = \'%s\'';
        //echo sprintf($sql,$list_id,$stucode);exit;
        $this->dao->execute(sprintf($sql,$list_id,$stucode));
        $result =  $this->insertStuInfo($data);
        $this->classStatistics($list_id);
        return $result;


    }
    /**
     * 局部更新
     * @return [type] [description]
     */
    public function partUpdateStu($list_id,$stucode,$data = array()) {
        if(empty($list_id) || empty($stucode) || count($data) == 0) {
            return 0;
        }
        $i = 0;
        $sql = 'UPDATE '.$this->stuTable.' SET [week_id]=%d,status = 2 WHERE ([list_id] = %d and [stucode] = "%s" and [class_id] = %d)';
        foreach($data as $class_id => $week_id) {
          $execute = sprintf($sql,$week_id,$list_id,$stucode,$class_id);
          //return $execute;
          //$arr[] = $execute.'<br>';
          $this->dao->execute($execute);
          $i += $this->dao->affectRows();

        }
       $this->classStatistics($list_id);
        return $i;

    }
    public function deleteStu($list_id,$stucode) {
        if(empty($list_id) || empty($stucode)) {
            return 0;
        }
        $i = 0;
        $sql = 'DELETE from '.$this->stuTable.'  WHERE ([list_id] = %d and [stucode] = "%s")';

          $execute = sprintf($sql,$list_id,$stucode);

          $this->dao->execute($execute);
          $i += $this->dao->affectRows();
          $this->classStatistics($list_id);


        return $i;

    }
    public function applyConfirm($list_id,$stucode,$data = array()) {
        //dumps($data);exit;
        if(empty($list_id) || empty($stucode) || count($data) == 0) {
            return 0;
        }

        $sql = 'UPDATE '.$this->stuTable.' SET status = %d WHERE ([list_id] = %d and [stucode] = "%s" and [class_id] = %d and [week_id]=%d)';

          $execute = sprintf($sql,$data['status'],$list_id,$stucode,$data['class_id'],$data['week_id']);
         // return $execute;
          //$arr[] = $execute.'<br>';
          $this->dao->execute($execute);
          $i = $this->dao->affectRows();
          $this->classStatistics($list_id);



        return $i;

    }
    public function getUserInfo($stu = '') {

        if(empty($stu)) return array();
        $cache_key = 'getUserInfo_'.$stu;
        $cache_result =  $this->cache->get($cache_key);
        if(!empty($cache_result)) {
            return $cache_result;
        }

        $sql = 'SELECT sName as name ,sParents1Phone as phone ,sCode as stucode  FROM BS_Student where sCode = \'%s\'';
        $sql = sprintf($sql,addslashes($stu));
        //return $sql;
        $result = $this->dao->getAll($sql);
        if($result) {
           $this->cache->set($cache_key,$result[0]);
        return $result[0];
        }

        else return array();
    }
     public function getStuInfoList($str) {
       $sql = 'SELECT sName as name ,sParents1Phone as phone ,sAliasCode as stuid,sCode as stucode  FROM BS_Student where '.$str;
      // return $sql;
       return $this->dao->getAll($sql);
   }

    /**
     * 更新后重新统计报名数，为了提升性能，只更新变化的课程
     * @param integer $list_id  活动id
     * @param [type]  $class_id 活动下的课程id
     * @return [type]  [description]
     */
    public function classStatistics($list_id=0,$class_id=0) {
        if(empty($list_id)) {
            return 0;
        }
        $class_list = $this->getReserveInfo($list_id);
        $sql1 = 'SELECT count(id) as num FROM '.$this->stuTable.' where list_id = %d and class_id = %d and week_id = %d and status > 0 and status < 3';
        $sql2 = 'UPDATE '.$this->infoTable.' SET apply_num=%d WHERE (list_id=%d and week_id = %d and class_id = %d)';
        foreach($class_list as $value) {

            $sql = sprintf($sql1,$list_id,$value['class_id'],$value['week_id']);
            $result =  $this->dao->getOne($sql);
            $sql = sprintf($sql2,$result,$list_id,$value['week_id'],$value['class_id']);
            $this->dao->execute($sql);

        }
// echo 'finish';
    }
      public function getAllStudentsList() {
         $cache_key = 'getAllStudentsList';
        $cache_resuls = $this->cache->get('reserve', $cache_key);

        if($cache_resuls) {

            //return $cache_resuls;
        }
        $sql = "SELECT  id,sAliasCode,sStudentCode,sStudentName,sParents1Phone  FROM viewRosterShrink where (sClassCode = 'BJ14C1580' or sClassCode = 'BJ14C1583' or sClassCode='BJ14C1586' or sClassCode='BJ14C1588' or sClassCode='BJ14C1590' or sClassCode='BJ14C1629' or sClassCode='BJ14C2026') and bValid = 1 ORDER BY sAliasCode ASC ";
        $results = $this->dao->getAll($sql);

        $this->cache->set('reserve', $cache_key,$results);
       // echo '<pre>';print_r($results);exit();
        return $results;
    }


}
?>
