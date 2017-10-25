<?php
class ExamModel {
	private $tableName = '';
	private $groupTable = '';
	private $studentTable = '';
	private $scoreTable = '';
	private $applyTable = 'ex_exam_applys';

	private $examPosTable = 'ex_exam_positions';
	private $positionTable = 'ex_positions';
	private $paperTable = 'ex_exam_papers';
	private $examStudentTable = 'ex_exam_students';

	private $operator = null;
	public $dao = null;

	private $numberFields = array('group_id', 'exam_status', 'exam_show_rank', 'exam_show_award', 'exam_money', 'exam_card_group', 'exam_card_len', 'require_xueji_code', 'exam_skip_grade');
	private $stringFields = array('exam_caption', 'exam_card_caption', 'exam_time_area', 'exam_intro', 'exam_sumary', 'exam_class', 'exam_notice', 'exam_special_tip');
	private $arrayFields = array('exam_grade');
	private $needFields = array('group_id'=>'竞赛组',
	'exam_caption'=>'竞赛名称',
	'exam_grade'=>'参赛年级',
	'exam_time_area'=>'考试起止时间',
	'exam_time_start_date'=>'考试开始日期',
	'exam_time_start_time'=>'考试开始时间',
	'exam_signup_start_date'=>'报名开始日期',
	'exam_signup_start_time'=>'报名开始时间',
	'exam_signup_stop_date'=>'报名结束日期',
	'exam_signup_stop_time'=>'报名结束时间',
	'exam_intro'=>'竞赛介绍',
	'exam_sumary'=>'竞赛摘要',
	'exam_card_caption'=>'准考证标题',
	'exam_notice'=>'参赛须知',
	);
	private $dateFields = array(
	'exam_time_start'=>array('exam_time_start_date', 'exam_time_start_time'),
	'exam_signup_start'=>array('exam_signup_start_date', 'exam_signup_start_time'),
	'exam_signup_stop'=>array('exam_signup_stop_date', 'exam_signup_stop_time'),
	'exam_score_at'=>array('exam_score_date', 'exam_score_time'));

	public function __construct() {
		$this->tableName = 'ex_exams';
		$this->groupTable = 'ex_exam_groups';
		$this->studentTable = 'ex_exam_students';
		$this->scoreTable = 'ex_exam_scores';
		$this->dao = Dao::getDao();
		if(class_exists('User', false)) {
			$this->operator = User::getLoginUser();
		}
	}

	private function getExamView() {
		$strQuery = '(SELECT exam.*,signup.signup_cnt,score.score_cnt
                      FROM (SELECT grp.group_caption,grp.group_type,ex.* 
                            FROM ' . $this->tableName . ' ex,' . $this->groupTable . ' grp
                            WHERE ex.is_remove=0 
                              AND grp.is_remove=0
                              AND ex.group_id=grp.group_id) exam
                      LEFT JOIN (
                            SELECT exam_id,count(stu_code) signup_cnt
                            FROM ' . $this->studentTable . '
                            WHERE is_cancel=' . $this->dao->quote(0) . '
                            GROUP BY exam_id
                       ) signup
                      ON exam.exam_id=signup.exam_id
                      LEFT JOIN (
                            SELECT exam_id,count(distinct stu_code) score_cnt
                            FROM ' . $this->scoreTable . '
                            WHERE paper_total_score*1 >0
                            GROUP BY exam_id
                       ) score
                       ON exam.exam_id=score.exam_id
                       ) examView ';
		return $strQuery;
	}

	public function count($condition='') {
		static $countArray = array();
		$condition = $condition ? $condition : '1=1';
		$key = md5(serialize($condition));
		if(false == isset($countArray[$key])) {
			$examView = $this->getExamView();
			$strQuery = 'SELECT COUNT(1)
                         FROM ' . $examView . '
                         WHERE ' . $condition;

			$countArray[$key] = $this->dao->getOne($strQuery);
		}
		return $countArray[$key];
	}

	public function getExamList($condition='', $currentPage=1, $pageSize=20) {
		$condition = $condition ? $condition : '1=1';
		$examCount = $this->count($condition);
		$pageCount = ceil($examCount, $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage <1) $currentPage = 1;
		$examView = $this->getExamView();
		$strQuery = 'SELECT * FROM ' . $examView . '
                     WHERE ' . $condition;
		
		$order = 'ORDER BY group_id desc,exam_grade desc';
		$strQuery .= $order;
		$examList = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		return $examList;
	}

	private function checkInfo(&$examInfo) {
		foreach ($this->needFields as $field=>$fieldCaption) {
			if(false == $examInfo[$field]) {
				return  array('errorMsg'=>'竞赛信息不完整,“' . $fieldCaption . '”必须设置值');
			}
		}
		foreach ($this->numberFields as $field) {
			$examInfo[$field] = abs($examInfo[$field]);
		}
		foreach ($this->stringFields as $field) {
			$examInfo[$field] = SysUtil::safeString($examInfo[$field]);
		}
		foreach ($this->arrayFields as $field) {
			$examInfo[$field] = implode(',', $examInfo[$field]);
		}
		foreach ($this->dateFields as $fieldName=>$fieldCfg) {
			$date = SysUtil::safeString($examInfo[$fieldCfg[0]]);
			$time = SysUtil::safeString($examInfo[$fieldCfg[1]]);
			unset($examInfo[$fieldCfg[0]]);
			unset($examInfo[$fieldCfg[1]]);
			if($date) {
				$date = date('Y-m-d', strtotime($date));
				$time = date('H:i', strtotime($date . ' ' . $time));
				$examInfo[$fieldName] = $date . ' ' . $time;
			} else {
				$examInfo[$fieldName] = '';
			}
		}
		$examInfo['exam_card_caption'] = strip_tags($examInfo['exam_card_caption']);
		$strQuery = 'SELECT count(1) FROM ' . $this->tableName . '
                     WHERE group_id=' . abs($examInfo['group_id']) . '
                       AND exam_caption=' . $this->dao->quote($examInfo['exam_caption']);
		if($examInfo['exam_id']) {
			$strQuery .= ' AND exam_id !=' . abs($examInfo['exam_id']);
		}
		$exists = $this->dao->getOne($strQuery) > 0;
		if($exists) {
			return array('errorMsg'=>'竞赛组中已存在同名考试,请不要重复添加');
		}
		return true;
	}

	public function save($examInfo) {
		$checkResult = $this->checkInfo($examInfo);
		if(is_array($checkResult)) return $checkResult;
		$userKey = $this->operator->getUserKey();
		$time = date('Y-m-d H:i:s');
		if($examInfo['exam_id']) {
			$strQuery = 'UPDATE ' . $this->tableName . '
                         SET update_user=' . $this->dao->quote($userKey) . ',
                             update_at=' . $this->dao->quote($time);
			foreach ($this->numberFields as $field) {
				$strQuery .= ',' . $field . '=' . $examInfo[$field];
			}
			foreach ($this->stringFields as $field) {
				$strQuery .= ',' . $field . '=' . $this->dao->quote($examInfo[$field]);
			}
			foreach ($this->dateFields as $field=>$fieldCfg) {
				$strQuery .= ',' . $field .  '=' . $this->dao->quote($examInfo[$field]);
			}

			foreach ($this->arrayFields as $field) {
				$strQuery .= ',' . $field . '=' . $this->dao->quote($examInfo[$field]);
			}
			$strQuery .= ' WHERE exam_id=' . $examInfo['exam_id'] . ' AND group_id=' . $examInfo['group_id'];
		} else {
			$strQuery = 'INSERT INTO ' . $this->tableName . '
                        (' . implode(',', array_merge($this->numberFields, $this->stringFields, array_keys($this->dateFields), $this->arrayFields)) .',update_user,update_at)
                         VALUES (
                         ';
			foreach ($this->numberFields as $field) {
				$values[] = $examInfo[$field];
			}
			foreach ($this->stringFields as $field) {
				$values[] = $this->dao->quote($examInfo[$field]);
			}
			foreach ($this->dateFields as $field=>$fieldCfg) {
				$values[] = $this->dao->quote($examInfo[$field]);
			}
			foreach ($this->arrayFields as $field) {
				$values[] = $this->dao->quote($examInfo[$field]);
			}
			$strQuery .= implode(',', $values);
			$strQuery .= ',' . $this->dao->quote($userKey) . ',
                         ' . $this->dao->quote($time);
			$strQuery .= ')';
		}
		if($this->dao->execute($strQuery)) {
			return true;
		}
		if($examInfo['exam_id']) {
			return array('errorMsg'=>'竞赛信息修改失败');
		} else {
			return  array('errorMsg'=>'竞赛信息添加失败');
		}
	}

	public function find($examId) {
		static $examArray = array();
		if(isset($examArray[$examId])) return $examArray[$examId];
		$examView = $this->getExamView();
		$strQuery = 'SELECT * FROM ' . $examView . '
                     WHERE exam_id=' . abs($examId);
		$examInfo = $this->dao->getRow($strQuery);
		foreach ($this->arrayFields as $field) {
			$examInfo[$field] = explode(',', $examInfo[$field]);
		}
		foreach ($this->dateFields as $field=>$fieldCfg) {
			if($examInfo[$field]) {
				$examInfo[$fieldCfg[0]] = date('Y-m-d', strtotime($examInfo[$field]));
				$examInfo[$fieldCfg[1]] = date('H:i', strtotime($examInfo[$field]));
			}
		}
		$examArray[$examId] = $examInfo;
		return $examInfo;
	}

	public function delete($examId) {
		$strQuery = 'SELECT count(1) FROM ' . $this->studentTable . '
                     WHERE exam_id=' . abs($examId) . ' 
                       AND is_cancel=0';
		$signupCnt = $this->dao->getOne($strQuery);
		$strQuery = 'SELECT count(1) FROM ' . $this->scoreTable . '
                     WHERE exam_id=' . abs($examId);
		$scoreCnt = $this->dao->getOne($strQuery);
		if ($signupCnt || $scoreCnt) {
			return array('errorMsg'=>'竞赛已有考生信息，不可删除');
		}
		$userKey = $this->operator->getUserKey();
		$time = date('Y-m-d H:i:s');
		$strQuery = 'UPDATE ' . $this->tableName . '
                     SET is_remove=' . time() . ',
                         update_user=' . $this->dao->quote($userKey) . ',
                         update_at=' . $this->dao->quote($time) . '
                     WHERE exam_id=' . abs($examId);
		if(false == $this->dao->execute($strQuery)) {
			return array('errorMsg'=>'竞赛删除失败');
		}
		return true;
	}


	public function arrayExamGroup($limit){
		$sql = "
    		SELECT group_id, group_caption
    			FROM $this->groupTable
    		WHERE is_remove = '0'
    	";

		$order = ' ORDER BY group_id DESC';
		$list =  $this->dao->getLimit($sql, 1, $limit, $order);
		$data =array();

		foreach ($list as $group){
			$data[$group['group_id']] = $group['group_caption'];
		}
		return $data;
	}

	public function saveExamPosition($para){
		$posIds = trim($para['posIds']);
		$ids = explode(',', $posIds);
 
		if (!$ids[0] || '' == $posIds) {
			return array('error' => true, 'message' => '请选择要选择的考点', 'data' => array());
		}
		
		$posArray = D('Position')->arrayPosition();

		$examId = abs($para['examId']);
		$data = array();

		$pdo = $this->dao->pdo;
		$sql = "
			INSERT INTO $this->examPosTable(exam_id, pos_id, pos_code, pos_caption, create_at, create_user_id) values(?,?, ?,?,?,?);
		";

		$st = $pdo->prepare($sql);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$created = date('Y-m-d H:i:s');
		$create_user_id = $this->operator->getUserKey();

		#$pdo->beginTransaction();
		$this->dao->begin();

		for($i = 0, $n = count($ids); $i < $n; $i++ ){
			$posId = trim($ids[$i]);
			$posCode = $posArray[$posId]['pos_code'];
			$posCaption = $posArray[$posId]['pos_caption'];
			$data = array($examId, $posId, $posCode, $posCaption, $created, $create_user_id);
			//$data = array($examId, $posId,$posCode);
			$rs = $st->execute($data);
			if(!$rs){
				#$pdo->rollBack();
				$this->dao->rollback();
				return array('error' => true, 'message' => '操作失败', 'data' => array());
			}
		}
		#$pdo->commit();
		$this->dao->commit();
		return array('error' => false, 'message' => '操作成功', 'data' => array());
	}

	public function listExamPosition($para){
		$examId = abs($para['exam_id']) ;

		$sql = "
			SELECT p.id,p.pos_id,p.exam_id,p.pos_code,p.pos_caption,p.status,
				   p.pos_code_pre,p.pos_room_count,p.pos_total_count,p.is_show_num,
				   p.is_show_caption,count(stu.id) stu_count
			FROM $this->examPosTable p
			LEFT JOIN $this->studentTable stu
			  ON stu.exam_id=p.exam_id 
			 AND p.pos_code=stu.pos_code 
			 AND stu.is_cancel=0 
			 AND stu.order_status!=1
			WHERE p.is_deleted = '' 
			  AND p.exam_id = '$examId'
			GROUP BY p.id,p.pos_id,p.exam_id,p.pos_code,p.pos_caption,p.status,p.pos_code_pre,
					 p.pos_room_count,p.pos_total_count,p.is_show_num,p.is_show_caption
		";	
		
		$st = $this->dao->pdo->prepare($sql);
		$st->execute();
		$list = $st->fetchAll(2);
		$data = array();
		$data['rows'] = $list;
		$data['total'] = count($list);

		return $data;
	}

	public function listUnSelectPosition($para){
		$examId = abs($para['exam_id']) ;

		$sql = "
			SELECT * FROM $this->positionTable p WHERE pos_id NOT IN(
				SELECT pos_id 
				FROM $this->examPosTable 
				WHERE is_deleted = '' AND exam_id = '$examId'
			)
		";	
		$st = $this->dao->pdo->prepare($sql);
		$st->execute();
		$list = $st->fetchAll(2);
		$data = array();
		$data['rows'] = $list;
		$data['total'] = count($list);

		return $data;
	}

	public function getExamPositionById($para){
		$id = abs($para['id']);
		$sql = "
			SELECT * FROM $this->examPosTable WHERE id = '$id' 
		";

		return $this->dao->getRow($sql);
	}

	public function saveRoomSetting($para){
		$id = abs($para['id']);
		if (0 == $id) {
			return array('error' => true, 'message' => '无法获取记录id', 'data' => array());
		}

		if (isset($para['status'])) {
			$data[':status'] = abs($para['status']);
		}

		$data[':pos_code_pre'] = SysUtil::safeString($para['pos_code_pre']);
		if ('' == $data[':pos_code_pre']) {
			return array('error' => true, 'message' => '请给出准考证前缀', 'data' => array());
		}

		if (isset($para['is_show_num'])) {
			$data[':is_show_num'] = abs($para['is_show_num']);
		}
		if (isset($para['is_show_caption'])) {
			$data[':is_show_caption'] = abs($para['is_show_caption']);
		}

		$data[':pos_room_count'] = abs($para['pos_room_count']);
		if ('' === $data[':pos_room_count']) {
			return array('error' => true, 'message' => '请给出考场数', 'data' => array());
		}

		if($para['room_num']){
			$total = 0;
			foreach ($para['room_num'] as $roomNum){
				$total += abs($roomNum);
			}
			$data[':pos_total_count'] = $total;
			$data[':room_num_setting'] = '[\'' .implode("','", $para['room_num']) . '\']';
		}

		if($para['room_caption']){
			$para['room_caption'] = array_map('trim', $para['room_caption']);
			$data[':room_name_setting'] = '[\'' . implode("','", $para['room_caption']) . '\']' ;
		}

		$data[':update_at'] = date('Y-m-d H:i:s');
		$data[':update_user_id'] = $this->operator->getUserKey();

		$updateField = array_keys($data);

		$updateFieldArray = array();
		foreach($updateField as $field ){
			$field = trim($field,':');
			$updateFieldArray[] = "$field = :$field";
		}
		$fieldList = implode(',', $updateFieldArray);

		$sql = "
			UPDATE $this->examPosTable
			SET 
				$fieldList
			WHERE id = '$id'
		";

		//$this->dao->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$st = $this->dao->pdo->prepare($sql);
		$rs = $st->execute($data);
		if (false === $rs) {
			return array('error' => true, 'message' => '操作失败', 'data' => array());
		}
		return array('error' => false, 'message' => '操作成功', 'data' => array('total' =>$total));
	}

	public function delExamPosition($para){
		$id = abs($para['id']);
		$deleted = date('Y-m-d H:i:s');
		$delete_user_id = $this->operator->getUserKey();

		$mark = md5(uniqid(rand(), true));
		$sql = "
			UPDATE $this->examPosTable 
			SET 
				is_deleted = '$mark',
				delete_at = '$deleted',
				delete_user_id = '$delete_user_id'
			WHERE  id = '$id'
		";

		$rs = $this->dao->execute($sql);
		if (false === $rs) {
			return array('error' => true, 'message' => '操作失败', 'data' => array());
		}
		return array('error' => false, 'message' => '操作成功', 'data' => array());
	
	}
	
	public function getScoreTimes($groupId) {
		$strQuery = 'SELECT * FROM ' . $this->tableName . '
					 WHERE group_id=' . abs($groupId) . '
					   AND is_remove=0
					 ORDER BY exam_grade DESC';
		$examList = $this->dao->getAll($strQuery);
		$examArray = array();
		$examIdArray = array(0);
		foreach ($examList as $exam) {
			$examIdArray[] = $exam['exam_id'];
			if($exam['exam_score_at']) {
				$examArray['exam_' . $exam['exam_id']] = array('score_at'=>$exam['exam_score_at'],
															   'subjects'=>array());
			} else {
				$examArray['exam_' . $exam['exam_id']] = array('score_at'=>date('Y-m-d H:i:s', strtotime('+1 year')),
															   'subjects'=>array());
			}
		}
		$examSubjects = C('EXAM_SUBJECTS');
		$strQuery = 'SELECT exam_id,subject_code FROM ' . $this->paperTable . '
					 WHERE exam_id IN (' . implode(',', $examIdArray) . ')';
		$paperList = $this->dao->getAll($strQuery);
		$subjectArray = array();
		foreach ($paperList as $paper) {
			$subjectCode = preg_replace('/\d{4}\-/', '', $paper['subject_code']);
			$subjectArray[$paper['exam_id']][$subjectCode] = str_replace(array('小学', '初中', '高中'), array('', '', ''), $examSubjects[$paper['subject_code']]);
		}
		foreach ($subjectArray as $examId=>$sbjArray) {
			$examArray['exam_' . $examId]['subjects'] = $sbjArray;
		}
		return $examArray;
	}
	
	public function getStuLeapExams($stuCode) {
		$strQuery = 'SELECT exam_id FROM ' . $this->applyTable . '
					 WHERE stu_code=' . $this->dao->quote($stuCode) . '
					   AND status=1';
		$applyList = $this->dao->getAll($strQuery);
		$idArray = array();
		foreach ($applyList as $apply) {
			$idArray[$apply['exam_id']] = $apply['exam_id'];
		}
		return $idArray;
	}
	
	public function getStuExams($stuInfo, $examType='running') {
		$stuGrade = $stuInfo['ngrade1year'];
		if($examType == 'running') {
			$time = date('Y-m-d H:i:s');
			$applyExams = $this->getStuLeapExams($stuInfo['scode']);
			$strQuery = 'SELECT * FROM ' . $this->tableName . ' e,
						 ' . $this->groupTable . ' g
						 WHERE g.group_id=e.group_id
						   AND g.group_status=1 
						   AND g.is_remove=0
						   AND exam_signup_start <=' . $this->dao->quote($time) . '
						   AND exam_signup_stop >=' . $this->dao->quote($time) . '
						   AND e.exam_skip_grade=0
						   AND exam_status=1
						   AND e.is_remove=0
						 ORDER BY exam_grade DESC';
			$examList = $this->dao->getAll($strQuery);
			$idArray = array(0);
			$stuExams = array();
			foreach ($examList as $exam) {
				if(in_array($exam['exam_id'], $applyExams) || preg_match('/' . $stuGrade . '/', $exam['exam_grade'])) {
					$exam['exam_caption'] = $exam['group_caption'] . ' - ' . $exam['exam_caption'];
					$exam['signup_status'] = '<b style="color:red">未报名</b>';
					$stuExams[$exam['exam_id']] = $exam;
					$idArray[] = $exam['exam_id'];
				}
			}
			$strQuery = 'SELECT * FROM ' . $this->examStudentTable . '
						 WHERE exam_id IN (' . implode(',', $idArray) . ')
						   AND is_cancel=0
						   AND stu_code=' . $this->dao->quote($stuInfo['scode']);
			
			$signupExams = $this->dao->getAll($strQuery);
			foreach ($signupExams as $exam) {
				$stuExams[$exam['exam_id']]['signup_status'] = '<span style="color:green">已报名</span>';
			}
			return $stuExams;
		}
		die('未开发');
	}

}
?>