<?php
class StudentModel extends CommModel {
	public $dao = null;
	protected $tableName = 'bs_student';
	protected $primaryTable = 'd_primaryschool';
	protected $middleTable = 'd_middleschool';
	protected $vrsTable = 'viewrostershrink';

	public function __construct() {
		$this->dao = Dao::getDao();
	}

	public function getSchoolName($studyYear, $primarySchoolId, $middleSchoolId) {
		static $gradeArray = array();
		if(false == $gradeArray) {
			$gradeModel = D('GradeYear');
			$gradeArray = $gradeModel->getGradeYears();
		}
		$i = 0;
		foreach ($gradeArray as $gradeYear=>$gradeName) {
			if($studyYear != $gradeYear) {
				$i++;
			} else {
				break;
			}
		}

		if($i <= 6) {
			return $this->getPrimarySchoolName($primarySchoolId);
		} else {
			return $this->getMiddleSchoolName($middleSchoolId);
		}

	}

	public function getPrimarySchoolName($schoolId) {
		$schoolId = abs($schoolId);
		if($schoolId == 0) $schoolId = 1;
		static $schoolArray = array();
		if(false == isset($schoolArray[$schoolId])) {
			$offset = ceil($schoolId / 100);
			if($offset == 0) $offset = 1;
			$strQuery = 'SELECT * FROM ' . $this->primaryTable . '
						 WHERE id >=' . ($offset - 1) * 100 . '
						   AND id <=' . $offset * 100;
			$schoolList = $this->dao->getAll($strQuery);
			foreach ($schoolList as $school) {
				if($school['sdistrict']) {
					$schoolArray[$school['id']] = '[' . $school['sdistrict'] . '] ' . $school['sname'];
				} else {
					$schoolArray[$school['id']] = $school['sname'];
				}
			}
		}
		return $schoolArray[$schoolId];
	}

	public function getMiddleSchoolName($schoolId) {
		$schoolId = abs($schoolId);;
		static $schoolArray = array();
		if(false == isset($schoolArray[$schoolId])) {
			$offset = ceil($schoolId / 100);
			if($offset == 0) $offset = 1;
			$strQuery = 'SELECT * FROM ' . $this->middleTable . '
						 WHERE id >=' . ($offset - 1) * 100 . '
						   AND id <=' . $offset * 100;
			$schoolList = $this->dao->getAll($strQuery);
			foreach ($schoolList as $school) {
				if($school['sdistrict']) {
					$schoolArray[$school['id']] = '[' . $school['sdistrict'] . '] ' . $school['sname'];
				} else {
					$schoolArray[$school['id']] = $school['sname'];
				}
			}
		}
		return $schoolArray[$schoolId];
	}

	public function getStuInfo($stuCode) {
		$strQuery = 'SELECT * FROM ' . $this->tableName . '
					 WHERE scode=' . $this->dao->quote($stuCode);
		$stuInfo = $this->dao->getRow($strQuery);
		$stuInfo = $this->_allInfo($stuInfo);
		return $stuInfo;
	}

	public function getStudentInfo($data = array()) {
		$condition = array();
		$sql = 'select * from '.$this->vrsTable.' ';
		if(!empty($data['sStudentCode'])) {
			$condition[] = sprintf("sStudentCode='%s'",preg_replace('|[^\d\w]|i','', $data['sStudentCode']));
		}
		if(!empty($data['sStudentName'])) {
			$condition[] = sprintf("sStudentName='%s'",preg_replace('|[^\w]|i','', $data['sStudentName']));
		}
		if(!empty($data['nClassYear'])) {
			$condition[] = sprintf("nClassYear=%d",(int)$data['nClassYear']);
		}
		if(empty($condition)) {
			return array();
		}
		$sql .= ' where '.implode(' and ', $condition);
		return $this->dao->getRow($sql);
	}

	public function allInfo($student, $isSingle=false) {
		if($isSingle) {
			return $this->_allInfo($student);
		}
		foreach ($student as $key=>$stu) {
			$student[$key] = $this->_allInfo($stu);
		}

		return $student;
	}

	private function _allInfo($student) {
		static $gradeYears = array();

		if (isset($student['ngrade'])) {
			$student['sgender'] = $genders[$ngender];
			$ngender = abs($student['ngender']);
			$genders = array('未知', '男', '女');
		}
		if(isset($student['dtbirthday'])) {
			$student['sbirthday'] = date('Y-m-d', Dao::msStrtotime($student['dtbirthday']));
		}
		if(isset($student['ngrade1year'])) {
			if(false == $gradeYears) {
				$gradeYearModel = D('GradeYear');
				$gradeYears = $gradeYearModel->getGradeYears();
			}
			$student['sgrade'] = $gradeYears[$student['ngrade1year']];
		}
		if(isset($student['sgrade'])) {
			if(false !== strpos($student['sgrade'], '小学')) {
				if($student['primaryschool']) {
					$student['school_name'] =  $this->getPrimarySchoolName($student['primaryschool']);
				} else {
					$student['school_name'] = '——';
				}
			} else if (false !== strpos($student['sgrade'], '初中') || false !== strpos($student['sgrade'], '高中')) {
				if($student['middleschool']) {
					$student['school_name'] =  $this->getMiddleSchoolName($student['middleschool']);
				} else {
					$student['school_name'] = '——';
				}
			} else if ($student['sgrade'] == '幼儿园') {
				$student['school_name'] = '幼儿园';
			}
		}
		return $student;
	}

	public function getStuCount($args) {

	}

	public function searchStudent($args) {

	}

	public function getSearchQuery($args) {

	}

	public function resetPwd($stuCode) {
		$strQuery = 'SELECT * FROM ' . $this->tableName . '
					 WHERE scode=' . $this->dao->quote($stuCode);
		$stuInfo = $this->dao->getRow($strQuery);
		$pwd = substr($stuInfo['saliascode'], 0, 6) . substr($stuInfo['sparents1phone'], -4);
		$strQuery = 'UPDATE ' . $this->tableName . '
					 SET spassword=' . $this->dao->quote(md5($pwd)) . '
					 WHERE scode=' . $this->dao->quote($stuCode);
		if($this->dao->execute($strQuery)) {
			return array('success'=>true, 'pwd'=>$pwd);
		}
		return array('errorMsg'=>'密码重置失败');
	}

	public function findConflict($stuName, $stuBirth, $stuMobile='') {
		$strQuery = 'SELECT * FROM ' . $this->tableName . '
					 WHERE sname=' . $this->dao->quote($stuName) . '
					   AND bisvalid=1 AND (
					   		(dtbirthday between ' . $this->dao->quote($stuBirth . ' 00:00:00') . ' AND ' . $this->dao->quote($stuBirth . ' 23:59:59') . ')';
		if($stuMobile) {
			$strQuery .= ' OR (sphone=' . $this->dao->quote($stuMobile) . ')
					       OR (smobile=' . $this->dao->quote($stuMobile) . ')
					       OR (sloginmobile=' . $this->dao->quote($stuMobile) . ')
					       OR (sparents1phone=' . $this->dao->quote($stuMobile) . ')
					       OR (sparents2phone=' . $this->dao->quote($stuMobile) . ')';
		}
		$strQuery .= ')';
		return $this->dao->getAll($strQuery);
	}

	public function getStuPasswd($stuInfo) {
		$initPwd = substr($stuInfo['saliascode'], 0, 6) . substr($stuInfo['sparents1phone'], -4);;
		if(false == $stuInfo['spassword']) {
			$stuQuery = 'UPDATE ' . $this->tableName . '
					     SET spassword=' . $this->dao->quote(md5($initPwd)) . '
					     WHERE scode=' . $this->dao->quote($stuInfo['scode']);
			$this->dao->execute($strQuery);
			return $initPwd;
		}
		if($stuInfo['spassword'] == md5($initPwd)) {
			return $initPwd;
		}
		return '';
	}
}
?>
