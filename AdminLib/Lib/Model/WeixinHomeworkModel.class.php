<?php
class WeixinHomeworkModel extends Model {
	public $dao = null;
	private $tbl_wxhw_dept = 'D_XueBuKe';
	private $tbl_wxhw_xueqi = 'D_Semester';
	private $tbl_wxhw_classtype = 'BS_ClassType';
	private $tbl_wxhw_mainsubject = 'MGS_HW_MainSubject';
	private $tbl_wxhw_smallsubject = 'MGS_HW_SmallSubject';
	private $tbl_wxhw_studentanswer = 'MGS_HW_StudentAnswer';
	private $tbl_wxhw_studentanswerscore = 'MGS_HW_StudentAnswerScore';
	private $operator = '';
	public function __construct() {
		$this->dao = Dao::getDao ();
		if (class_exists ( 'User', false )) {
			$operator = User::getLoginUser ();
			if ($operator)
				$this->userKey = $operator->getUserKey ();
		}
	}
	public function getDepts() {
		$strQuery = "SELECT * FROM $this->tbl_wxhw_dept where sDeptCode in ('DPBJ028', 'DPBJ025', 'DPBJ031', 'DPBJ037', 'DPBJ039', 'DPBJ040')";
		return $this->dao->getAll ( $strQuery );
	}
	public function getOneClassType($classtypecode) {
		$strQuery = "SELECT * FROM $this->tbl_wxhw_classtype where sCode = '{$classtypecode}'";
		return $this->dao->getRow ( $strQuery );
	}
	public function getXueqi() {
		$strQuery = "SELECT * FROM $this->tbl_wxhw_xueqi";
		return $this->dao->getAll ( $strQuery );
	}
	public function getClassType($year, $semester, $xuebu, $xueke) {
		$strQuery = "SELECT ct.*, c.nLesson FROM BS_ClassType as ct INNER JOIN (SELECT sClassTypeCode, nLesson FROM viewBS_Class WHERE nClassYear = '{$year}' AND nSemester = '{$semester}' GROUP BY sClassTypeCode, nLesson) AS c ON ct.sCode = c.sClassTypeCode WHERE ct.nXueBu = '{$xuebu}' AND ct.nXueKe = '{$xueke}' AND ct.bValid = 1";
		return $this->dao->getAll ( $strQuery );
	}
	public function checkMainSubject($year, $semester_id, $classtype, $lesson) {
		$strQuery = "SELECT count(1) as row_count FROM $this->tbl_wxhw_mainsubject WHERE classyear = '{$year}' AND semester_id = '{$semester_id}' AND classtype_code = '{$classtype}' AND lesson_no = '{$lesson}'";
		return $this->dao->getOne ( $strQuery );
	}
	public function addSubject($year, $semester_id, $classtype, $lesson, $homework_solution_pics, $smallsubjects) {
		$time = time ();
		$this->dao->begin ();
		$strQuery = "INSERT INTO $this->tbl_wxhw_mainsubject (classyear, semester_id, classtype_code, lesson_no, homework_solution_pics, homework_type, add_time, edit_time)
		VALUES
		('{$year}', '{$semester_id}', '{$classtype}', '{$lesson}', '{$homework_solution_pics}', '1', '{$time}', 0)";
		if (! $this->dao->execute ( $strQuery )) {
			$this->dao->rollback ();
			return false;
		}
		$main_subject_id = $this->dao->lastInsertId ();
		$exeState = 1;
		foreach ( $smallsubjects as $onesmallsubject ) {
			$strQuery = "INSERT INTO $this->tbl_wxhw_smallsubject (main_subject_id, subject_no, fullscore,type)
			VALUES
			('{$main_subject_id}', '{$onesmallsubject['subject_no']}', '{$onesmallsubject['fullscore']}', '{$onesmallsubject['type']}')";
			if (! $this->dao->execute ( $strQuery )) {
				$exeState = - 1;
				break;
			}
		}
		if ($exeState == -1){
			$this->dao->rollback ();
			return false;
		}
		$this->dao->commit ();
		return true;
	}
	public function getSubjects($year, $semester_id, $classtype, $lesson = null) {
		$strQuery = "SELECT * FROM $this->tbl_wxhw_mainsubject WHERE classyear = '{$year}' AND semester_id = '{$semester_id}' AND classtype_code = '{$classtype}'";
		if ($lesson) {
			$strQuery .= " AND lesson_no = '{$lesson}'";
		}
		return $this->dao->getAll ( $strQuery );
	}

	public function getOneMainSubject($id) {
		$strQuery = "SELECT * FROM $this->tbl_wxhw_mainsubject WHERE id = '{$id}'";
		return $this->dao->getRow ( $strQuery );
	}
	public function getSmallSubject($main_subject_id){
		$strQuery = "SELECT * FROM $this->tbl_wxhw_smallsubject WHERE main_subject_id = '{$main_subject_id}' ORDER BY subject_no";
		return $this->dao->getAll ( $strQuery );
	}
	public function editSubject($main_subject_id, $homework_solution_pics, $smallsubjects) {
		$time = time ();
		$this->dao->begin ();
		$strQuery = "UPDATE $this->tbl_wxhw_mainsubject SET homework_solution_pics = '{$homework_solution_pics}', edit_time = '{$time}' WHERE id = '{$main_subject_id}'";
		if (! $this->dao->execute ( $strQuery )) {
			$this->dao->rollback ();
			return false;
		}
		$exeState = 1;
		$strQuery = "DELETE FROM $this->tbl_wxhw_smallsubject WHERE main_subject_id = '{$main_subject_id}'";
		if (! $this->dao->execute ( $strQuery )) {
			$this->dao->rollback ();
			return false;
		}
		foreach ( $smallsubjects as $onesmallsubject ) {
			$strQuery = "INSERT INTO $this->tbl_wxhw_smallsubject (main_subject_id, subject_no, fullscore)
			VALUES
			('{$main_subject_id}', '{$onesmallsubject['subject_no']}', '{$onesmallsubject['fullscore']}')";
			if (! $this->dao->execute ( $strQuery )) {
				$exeState = - 1;
				break;
			}
		}
		if ($exeState == - 1) {
			$this->dao->rollback ();
			return false;
		}
		$this->dao->commit ();
		return true;
	}



	//---------------------------------add by tiyee ---------------------------
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
	      //echo $sql;exit();

	      return $this->dao->execute($sql);

    }
	public function getLessonList($data) {
		$sql = '  select top 100 hwm.*,bct.sName,bp.sName as projectname,xk.sName as depname,bct.nXueBu,bct.nXueke FROM MGS_HW_MainSubject hwm
		left join bs_classtype bct on (hwm.classtype_code = bct.sCode)
		left join bs_project bp on (bct.sProjectCode = bp.sCode)
		left join D_XueBuKe xk on (bct.nXueke = xk.nXueke and bct.nXueBu = xk.nXueBu)
		';
		$condition = array();
		if(!empty($data['classyear'])) {
			$condition[] = 'hwm.classyear='.(int)$data['classyear'];
		}
		if(!empty($data['semester_id'])) {
			$condition[] = 'hwm.semester_id='.(int)$data['semester_id'];
		}
		if(!empty($data['classtype_code'])) {
			$condition[] = 'hwm.classtype_code='.$this->dao->quote($data['classtype_code']);
		}
		if(!empty($data['nXueBu'])) {
			$condition[] = 'bct.nXueBu='.(int)$data['nXueBu'];
		}
		if(!empty($data['nXueKe'])) {
			$condition[] = 'bct.nXueKe='.(int)$data['nXueKe'];
		}
		if(!empty($condition)) {
			$sql .= ' where '.implode(' and ', $condition) ;
		}
		$sql .= ' order by hwm.id DESC';
		return $this->dao->getLimit($sql, $data['page'], 20, 'order by id DESC');

	}
	public function getLessonAll($data) {
		$sql = '  select top 100 hwm.*,bct.sName,bp.sName as projectname,xk.sName as depname,bct.nXueBu,bct.nXueke FROM MGS_HW_MainSubject hwm
		left join bs_classtype bct on (hwm.classtype_code = bct.sCode)
		left join bs_project bp on (bct.sProjectCode = bp.sCode)
		left join D_XueBuKe xk on (bct.nXueke = xk.nXueke and bct.nXueBu = xk.nXueBu)
		';
		$condition = array();
		if(!empty($data['classyear'])) {
			$condition[] = 'hwm.classyear='.(int)$data['classyear'];
		}
		if(!empty($data['semester_id'])) {
			$condition[] = 'hwm.semester_id='.(int)$data['semester_id'];
		}
		if(!empty($data['classtype_code'])) {
			$condition[] = 'hwm.classtype_code='.$this->dao->quote($data['classtype_code']);
		}
		if(!empty($data['nXueBu'])) {
			$condition[] = 'bct.nXueBu='.(int)$data['nXueBu'];
		}
		if(!empty($data['nXueKe'])) {
			$condition[] = 'bct.nXueKe='.(int)$data['nXueKe'];
		}
		if(!empty($condition)) {
			$sql .= ' where '.implode(' and ', $condition);
		}
		//echo $sql;
		return $this->dao->getAll($sql);

	}
	public function getLessonInfo($data) {
		$sql = '  select top 1 hwm.*,bct.sName,bp.sName as projectname FROM MGS_HW_MainSubject hwm
		left join bs_classtype bct on (hwm.classtype_code = bct.sCode)
		left join bs_project bp on (bct.sProjectCode = bp.sCode) where hwm.id = '.$data['id'];
		return $this->dao->getRow($sql);

	}

	public function getLessonNum($data) {
		$sql = 'select count(hwm.id)FROM MGS_HW_MainSubject hwm
		left join bs_classtype bct on (hwm.classtype_code = bct.sCode)
		left join bs_project bp on (bct.sProjectCode = bp.sCode)
		left join D_XueBuKe xk on (bct.nXueke = xk.nXueke and bct.nXueBu = xk.nXueBu)';
		$condition = array();
		if(!empty($data['classyear'])) {
			$condition[] = 'hwm.classyear='.(int)$data['classyear'];
		}
		if(!empty($data['semester_id'])) {
			$condition[] = 'hwm.semester_id='.(int)$data['semester_id'];
		}
		if(!empty($data['classtype_code'])) {
			$condition[] = 'hwm.classtype_code='.$this->dao->quote($data['classtype_code']);
		}
		if(!empty($data['nXueBu'])) {
			$condition[] = 'bct.nXueBu='.(int)$data['nXueBu'];
		}
		if(!empty($data['nXueKe'])) {
			$condition[] = 'bct.nXueKe='.(int)$data['nXueKe'];
		}
		if(!empty($condition)) {
			$sql .= ' where '.implode(' and ', $condition);
		}
		return $this->dao->getOne($sql);
	}

	public function getQuestionList($id) {
		$sql = 'SELECT TOP 1000 [id]
				      ,[main_subject_id]
				      ,[subject_no]
				      ,[fullscore]
				      ,[corrent_answer]
				      ,[sQuestion]
				      ,[type]
				      ,[sQuestion]
                FROM [GS].[dbo].[MGS_HW_SmallSubject] where main_subject_id = '.(int)$id .' order by subject_no asc';
        return $this->dao->getAll($sql);
	}
	public function getQuestionInfo($data) {
		$sql = 'SELECT TOP 1 [id]
				      ,[main_subject_id]
				      ,[subject_no]
				      ,[fullscore]
				      ,[corrent_answer]
				      ,[type]
				      ,[sQuestion]
                FROM [GS].[dbo].[MGS_HW_SmallSubject] where id = '.(int)$data['id'] .' ';
        return $this->dao->getRow($sql);

	}

	public function deleteQuestion($data) {
		$sql = 'delete from MGS_HW_SmallSubject where id = '.(int)$data['id'] .' ';
		return $this->dao->execute($sql);


	}
	public function deletePaper($data) {
		$this->dao->begin ();
		$sql = 'delete from MGS_HW_SmallSubject where main_subject_id = '.(int)$data['id'] .' ';
		if (! $this->dao->execute ( $sql )) {
			$this->dao->rollback ();
			return false;
		}
		$sql = 'delete  FROM MGS_HW_MainSubject where id = '.(int)$data['id'] .' ';
		if (! $this->dao->execute ( $sql )) {
			$this->dao->rollback ();
			return false;
		}
		$this->dao->commit ();
		return true;


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
     $result =  $this->dao->execute($sql);
     if($result) {
     	return $this->dao->lastInsertId();
     } else {
     	return 0;
     }
    }
}
?>
