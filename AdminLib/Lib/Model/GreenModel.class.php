<?php
class GreenModel {
	public $tableName = 'ex_green_students';
	protected $stuTable = 'bs_student';
	protected $examTable = 'ex_exams';
	protected $esTable = 'ex_exam_students';
	protected $areaTable = 'bs_area';
	protected $operator = '';
	
	public function __construct() {
		$this->dao = Dao::getDao();
		$operator = User::getLoginUser();
		$this->operator = $operator->getUserKey();
	}
	
	public function save($examId, $stuCode, $areaCode, $examCode='') {
		$examId = abs($examId);
		$stuCode = trim($stuCode);
		$strQuery = 'SELECT count(1) FROM ' . $this->tableName . '
					 WHERE exam_id=' . $examId . '
					   AND stu_code=' . $this->dao->quote($stuCode);
		$stuCnt = $this->dao->getOne($strQuery);
		if($stuCnt >0) {
			return array('errorMsg'=>'学生信息已存在，请不要重复添加！');
		} else {
			$examModel = D('Exam');
			$examInfo = $examModel->find($examId);
            $examSuperUsers = C('EXAM_SUPER_USERS');
            list($nil, $userName) = explode('-', $this->operator);
            $isSuper = false;
            if(in_array($userName, $examSuperUsers)) {
                $isSuper = true;
            }
			if(false == $isSuper && strtotime($examInfo['exam_signup_stop']) < time()) {
				return array('errorMsg'=>'竞赛报名已经停止，请不要继续添加报名资格');
			}
			$time = date('Y-m-d H:i:s');
			$strQuery = 'INSERT INTO ' . $this->tableName . '
						 (exam_id,stu_code,area_code,exam_code,operator,create_at)
						 VALUES (' . $examId . ',
						 		 ' . $this->dao->quote($stuCode) . ',
						 		 ' . $this->dao->quote($areaCode) . ',
						 		 ' . $this->dao->quote($examCode) . ',
						 		 ' . $this->dao->quote($this->operator) . ',
						 		 ' . $this->dao->quote($time) . ')';
			if($this->dao->execute($strQuery)) {
				return array('success'=>true);
			}
			return array('errorMsg'=>'绿色通道考生添加失败,请联系管理员');
		}
	}
	
	public function searchStudents($keyword) {
		$strQuery = 'SELECT * FROM ' . $this->stuTable . '
					 WHERE scode =' . $this->dao->quote($keyword) . '
					 	OR saliascode=' . $this->dao->quote($keyword) . '
					 	OR sname LIKE ' . $this->dao->quote('%' . $keyword . '%') . '
					 	OR sparents1phone LIKE ' . $this->dao->quote('%' . $keyword . '%') . '
					 	OR sparents2phone LIKE ' . $this->dao->quote('%' . $keyword . '%');
		$order = 'ORDER BY scode';
		$stuList = $this->dao->getLimit($strQuery, 1, 20, $order);
		$gradeModel = D('GradeYear');
		$gradeYears = $gradeModel->getGradeYears();
		foreach ($stuList as $key=>$stu) {
			$gender = '';
			if($stu['ngender'] == 1) {
				$gender = '男';
			} elseif ($stu['ngender'] == 2) {
				$gender = '女';
			}
			$stuList[$key]['gender'] = $gender;
			$stuList[$key]['grade_text'] = $gradeYears[abs($stu['ngrade1year'])];
			$stuList[$key]['stu_birth'] = SysUtil::datetime($stu['dtbirthday'], 'Y-m-d');
		}
		return $stuList;
	}
	
	public function getGreenCount($examId, $keyword = '') {
		static $count = null;
		if(null === $count) {
			if (is_array($examId)) {
				$tempArr = array();
				foreach ($examId as $id) {
					$tempArr[] = intval($id);
				}
				$examId = implode(',', $tempArr);
				$condition = ' exam_id IN (' . $examId . ')';
			}else{
				$condition = ' exam_id = ' . abs($examId);
			}
			if ($keyword) {
				$strQuery = 'SELECT COUNT(1)
					 FROM ' . $this->stuTable . ' stu,
					 	  ' . $this->tableName . ' green
					 LEFT JOIN ' . $this->areaTable . ' area
					   ON area.scode=green.area_code
					 WHERE stu.scode=green.stu_code
					   AND ' . $condition . '
					   AND (stu.scode = ' . $this->dao->quote($keyword) . '
					 	OR stu.saliascode=' . $this->dao->quote($keyword) . '
					 	OR stu.sname LIKE ' . $this->dao->quote('%' . $keyword . '%') . '
					 	OR stu.sparents1phone LIKE ' . $this->dao->quote('%' . $keyword . '%') . '
					 	OR stu.sparents2phone LIKE ' . $this->dao->quote('%' . $keyword . '%') . ') ';
			}else{
				$strQuery = 'SELECT COUNT(1) FROM ' . $this->tableName . ' WHERE ' . $condition;
			}
			$count = $this->dao->getOne($strQuery);
		}
		return $count;
	}
	
	public function getGreenList($examId, $currentPage=1, $pageSize=20, $keyword = '') {
		$condition = '';
		if (is_array($examId)) {
			$tempArr = array();
			foreach ($examId as $id) {
				$tempArr[] = intval($id);
			}
			$examId = implode(',', $tempArr);
			$condition = ' green.exam_id IN (' . $examId . ')';
		}else{
			$examId = abs($examId);
			$condition = ' green.exam_id = ' . $examId;
		}
		$recordCount = $this->getGreenCount($examId, $keyword);
		$pageCount = ceil($recordCount / $pageSize);
		if($pageCount <1) $pageCount = 1;
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT green.id,stu.sname,stu.scode,stu.saliascode,stu.ngender,stu.ngrade1year,
							stu.dtbirthday,stu.sparents1phone,stu.sparents2phone,green.exam_id,
							green.exam_code rcode,green.operator,green.create_at,area.sname area_name,area.scode area_code, exam.exam_caption exam_caption
					 FROM ' . $this->stuTable . ' stu,
					 	  ' . $this->tableName . ' green
					 LEFT JOIN ' . $this->areaTable . ' area
					   ON area.scode=green.area_code
					 LEFT JOIN ' . $this->examTable . ' exam
					   ON green.exam_id = exam.exam_id
					 WHERE stu.scode=green.stu_code
					   AND ' . $condition;
		if ($keyword) {
			$strQuery = $strQuery . ' AND (stu.scode = ' . $this->dao->quote($keyword) . '
					 	OR stu.saliascode=' . $this->dao->quote($keyword) . '
					 	OR stu.sname LIKE ' . $this->dao->quote('%' . $keyword . '%') . '
					 	OR stu.sparents1phone LIKE ' . $this->dao->quote('%' . $keyword . '%') . '
					 	OR stu.sparents2phone LIKE ' . $this->dao->quote('%' . $keyword . '%') . ') ';
		}
					   
		$order = 'ORDER BY create_at DESC';
		$greenList = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		$gradeModel = D('GradeYear');
		$gradeYears = $gradeModel->getGradeYears();
		$stuCodeArray = array($this->dao->quote(0));
		foreach ($greenList as $key=>$stu) {
			$stuCodeArray[] = $this->dao->quote($stu['scode']);
			$greenList[$key]['grade_text'] = $gradeYears[abs($stu['ngrade1year'])];
			$greenList[$key]['stu_birth'] = SysUtil::datetime($stu['dtbrithday'], 'Y-m-d');
		}
		$strQuery = 'SELECT stu_code,exam_code,pos_code,room_num,seat_num,signup_time 
					 FROM ' . $this->esTable . ' green
					 WHERE ' . $condition . '
					   AND stu_code IN (' . implode(',', $stuCodeArray) . ')
					   AND is_cancel=0';
		
		$signupList = $this->dao->getAll($strQuery);
		$signupArray = array();
		foreach ($signupList as $key=>$signup) {
			$signupArray[$signup['stu_code']] = $signup;
		}
		foreach ($greenList as $key=>$stu) {
			$signupInfo = $signupArray[$stu['scode']] ? $signupArray[$stu['scode']] : array('signup_time'=>'');
			$greenList[$key] = array_merge($stu, $signupInfo);
			$greenList[$key]['exam_code'] = $greenList[$key]['rcode'] && $greenList[$key]['exam_code'] ? $greenList[$key]['rcode'] : $greenList[$key]['exam_code'];
		}
		
		return $greenList;
	}
	
	function ifExists($examId, $stuCode) {
		$strQuery = 'SELECT count(1) FROM ' . $this->tableName . '
					 WHERE exam_id=' . abs($examId) . '
					   AND stu_code=' . $this->dao->quote($stuCode);
		return $this->dao->getOne($strQuery);
	}
	
	public function getGreenStat($groupId) {
		$strQuery = 'SELECT exam_id,exam_caption,exam_money 
					 FROM ' . $this->examTable . '
					 WHERE group_id=' . abs($groupId) . '
					   AND is_remove=0
					 ORDER BY exam_id';
		$examList = $this->dao->getAll($strQuery);
		$examIds = array(0);
		foreach ($examList as $exam) {
			$examIds[] = $exam['exam_id'];
		}
		$strQuery = 'SELECT sname area_name,scode area_code 
				 	 FROM ' . $this->areaTable . '
				 	 WHERE scode IN (
				 	 	SELECT DISTINCT area_code FROM ' . $this->tableName . '
					 	WHERE exam_id IN (' . implode(',', $examIds) . ')
					 )
					 ORDER BY scode';
		$areaList = $this->dao->getAll($strQuery);
		
		$statArray = array();
		$statArray[0] = array('area_name'=>'考点');
		foreach ($examList as $exam) {
			$statArray[0]['exam_' . $exam['exam_id']] = $exam['exam_caption'] . '(' . abs($exam['exam_money']) . '元)';
		}
		$statArray[0]['money'] = '收费金额';
		
		$strQuery = 'SELECT ' . $this->dao->concatFields(array('exam_id', "'_'", 'area_code')) . ' exam_area, count(1) cnt
					 FROM ' . $this->tableName . '
					 WHERE exam_id IN (' . implode(',', $examIds) . ')
					   AND create_at > ' . $this->dao->quote(date('Y-m-d')) . '
					 GROUP BY ' . $this->dao->concatFields(array('exam_id', "'_'", 'area_code'));
		$todayStatList = $this->dao->getAll($strQuery);
		$todayArray = array();
		foreach ($todayStatList as $stat) {
			$todayArray[$stat['exam_area']] = $stat['cnt'];
		}
		
		$strQuery = 'SELECT ' . $this->dao->concatFields(array('exam_id', "'_'", 'area_code')) . ' exam_area, count(1) cnt
					 FROM ' . $this->tableName . '
					 WHERE exam_id IN (' . implode(',', $examIds) . ')
					 GROUP BY ' . $this->dao->concatFields(array('exam_id', "'_'", 'area_code'));
		$totalStatList = $this->dao->getAll($strQuery);
		$totalArray = array();
		foreach ($totalStatList as $stat) {
			$totalArray[$stat['exam_area']] = $stat['cnt'];
		}
		foreach ($areaList as $area) {
			$statArray[$area['area_code']]['area_name'] = $area['area_name'];
			$areaToday = 0;
			$areaTotal = 0;
			foreach ($examList as $exam) {
				$key = $exam['exam_id'] . '_' . $area['area_code'];
				$statArray[$area['area_code']]['exam_today_' . $exam['exam_id']] = abs($todayArray[$key]);
				if(abs($todayArray[$key]) > 0)
					$areaToday += abs($exam['exam_money']) * abs($todayArray[$key]);
					
				$statArray[$area['area_code']]['exam_total_' . $exam['exam_id']] = abs($totalArray[$key]);
				if(abs($totalArray[$key]) > 0)
					$areaTotal += abs($exam['exam_money']) * abs($totalArray[$key]);
			}
			$statArray[$area['area_code']]['today_money'] = $areaToday;
			$statArray[$area['area_code']]['total_money'] = $areaTotal;
		}
		return $statArray;
	}
	
	public function delGreen($gid) {
		$gid = SysUtil::uuid($gid);
		$strQuery = 'SELECT * FROM ' . $this->tableName . '
					 WHERE id=' . $this->dao->quote($gid);
		$greenInfo = $this->dao->getRow($strQuery);
		$strQuery = 'SELECT count(1) FROM ' . $this->esTable . '
					 WHERE exam_id=' . abs($greenInfo['exam_id']) . '
					   AND stu_code=' . $this->dao->quote($greenInfo['stu_code']) . '
					   AND is_cancel=0';
		$esCnt = $this->dao->getOne($strQuery);
		if($esCnt) {
			return array('errorMsg'=>'考生已经报名，请先执行取消报名操作');
		}
		$strQuery = 'DELETE FROM ' . $this->tableName . ' WHERE id=' . $this->dao->quote($gid);
		$this->dao->execute($strQuery);
		return true;
	}
}
?>