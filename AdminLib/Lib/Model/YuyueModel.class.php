<?php
class YuyueModel {
	protected $dao = null;
	protected $operator = '';
	protected $stuTable = 'bs_student';
	protected $eGroupTable = 'ex_exam_groups';
	protected $ePosTable = 'ex_exam_positions';
	protected $esTable = 'ex_exam_students';
	protected $posTable = 'ex_positions';
	protected $examTable = 'ex_exams';
	protected $scoreTable = 'ex_exam_scores';
	protected $yGroupTable = 'ex_yuyue_groups';
	protected $yExamGroupTable = 'ex_yuyue_exam_group';
	protected $yTimeTable = 'ex_yuyue_times';
	protected $yPosTable = 'ex_yuyue_positions';
	protected $yBatchTable = 'ex_yuyue_batches';
	protected $yStudentTable = 'ex_yuyue_students';
	protected $rosterTable = 'viewbs_roster';
	protected $printTable = 'ex_yuyue_print';
	
	public function __construct() {
		$this->dao = Dao::getDao();
		$operator = User::getLoginUser();
		$this->operator = $operator->getUserKey();
	}
	
	public function getYGroupList($eGroupId) {
		$strQuery = 'SELECT * FROM ' . $this->yGroupTable . '
					 WHERE exam_group_id=' . abs($eGroupId) . '
					   AND is_remove=0
					 ORDER BY create_at';
		$yGroupList = $this->dao->getAll($strQuery);
		return $yGroupList;
	}
	
	public function getYFreeExams($eGroupId, $yGroupId='') {
		$strQuery = 'SELECT e.*,g.group_caption 
					 FROM ' . $this->examTable . ' e,
					 	  ' . $this->eGroupTable .' g
					 WHERE e.group_id=g.group_id
					   AND e.group_id=' . abs($eGroupId) . '
					   AND e.is_remove=0
					   AND e.exam_status=1
					   AND g.is_remove=0
					   AND g.group_status=1
					 ORDER BY e.exam_grade';
		$groupExams = $this->dao->getAll($strQuery);
		$freeExams = array();
		$eidArray = array(0);
		foreach ($groupExams as $exam) {
			$freeExams[$exam['exam_id']] = $exam;
			$eidArray[] =$exam['exam_id'];
		}
		unset($groupExams);
		$strQuery = 'SELECT exam_id FROM ' . $this->yExamGroupTable . '
					 WHERE exam_id IN (' . implode(',', $eidArray) . ')';
		if($yGroupId) {
			$strQuery .= ' AND ygroup_id !=' . $this->dao->quote($yGroupId);
		}
		$existsExams = $this->dao->getAll($strQuery);
		foreach ($existsExams as $exam) {
			unset($freeExams[$exam['exam_id']]);
		}
		return $freeExams;
	}
	
	public function findGroup($yGroupId) {
		$strQuery = 'SELECT * FROM ' . $this->yGroupTable . '
					 WHERE is_remove=0 
					   AND ygroup_id=' . $this->dao->quote($yGroupId);
		$yGroupInfo = $this->dao->getRow($strQuery);
		return $yGroupInfo;
	}
	
	public function saveYGroup($groupInfo) {
		$exams = explode(',', $groupInfo['eids']);
		$yGroupCaption = SysUtil::safeString($groupInfo['yGroupCaption']);
		$eGroupId = abs($groupInfo['eGroupId']);
		$startTime = date('Y-m-d H:i:s', strtotime($groupInfo['startTime']));
		$endTime = date('Y-m-d H:i:s', strtotime($groupInfo['endTime']));
		$time = date('Y-m-d H:i:s');
		$this->dao->begin();
		if($groupInfo['stuFilter']) {
			$studyDateStart = date('Y-m-d', strtotime($groupInfo['studyDateStart']));
			$studyDateEnd = date('Y-m-d', strtotime($groupInfo['studyDateEnd']));
		} else {
			$studyDateStart = $studyDateEnd = '';
		}
		if ($groupInfo['yGroupId']) {
			$yGroupId = $groupInfo['yGroupId'];
			$strQuery = 'DELETE FROM ' . $this->yExamGroupTable . '
						 WHERE ygroup_id=' . $this->dao->quote($yGroupId);
			$this->dao->execute($strQuery);
			$strQuery = 'UPDATE ' . $this->yGroupTable . '
						 SET ygroup_caption=' . $this->dao->quote($yGroupCaption) . ',
						 	 ygroup_time_start=' . $this->dao->quote($startTime)  . ',
						 	 ygroup_time_end=' . $this->dao->quote($endTime) . ',
						 	 exam_list=' . $this->dao->quote($groupInfo['eids']) . ',
						 	 stu_filter=' . abs($groupInfo['stuFilter']) . ',
							 study_date_start=' . $this->dao->quote($studyDateStart) . ',
							 study_date_end=' . $this->dao->quote($studyDateEnd) . ',
						 	 update_user=' . $this->dao->quote($this->operator) . ',
						 	 update_at=' . $this->dao->quote($time) . '
						 WHERE ygroup_id=' . $this->dao->quote($yGroupId);
		} else {
			$yGroupId = SysUtil::uuid();
			$strQuery = 'INSERT INTO ' . $this->yGroupTable . '
						 (ygroup_id,exam_group_id,ygroup_caption,ygroup_time_start,ygroup_time_end,exam_list,
						  stu_filter,study_date_start,study_date_end,is_remove,create_user,create_at,update_user,update_at)
						 VALUES (' . $this->dao->quote($yGroupId) . ',
						 		 ' . abs($eGroupId) . ',
						 		 ' . $this->dao->quote($yGroupCaption) . ',
						 		 ' . $this->dao->quote($startTime) . ',
						 		 ' . $this->dao->quote($endTime) . ',
						 		 ' . $this->dao->quote($groupInfo['eids']) . ',
						 		 ' . abs($groupInfo['stuFilter']) . ',
						 		 ' . $this->dao->quote($studyDateStart) . ',
						 		 ' . $this->dao->quote($studyDateEnd) . ',0,
						 		 ' . $this->dao->quote($this->operator) . ',
						 		 ' . $this->dao->quote($time) . ',
						 		 ' . $this->dao->quote($this->operator) . ',
						 		 ' . $this->dao->quote($time) . ')';
			
		}
		if($this->dao->execute($strQuery)) {
			$result = true;
			foreach ($exams as $examId) {
				$strQuery = 'INSERT INTO ' . $this->yExamGroupTable . '
							 (exam_id,ygroup_id,create_user,create_at)
							 VALUES (' . abs($examId) . ',
							 		 ' . $this->dao->quote($yGroupId) . ',
							 		 ' . $this->dao->quote($this->operator) . ',
							 		 ' . $this->dao->quote($time) . ')';
				$result &= $this->dao->execute($strQuery);
				if(false == $result) {
					$this->dao->rollback();
					return array('errorMsg'=>'预约组保存失败');
				}
			}
		}
		$this->dao->commit();
		return array('success'=>true);
	}
	
	public function delYGroup($yGroupId) {
		$strQuery = 'SELECT * FROM ' . $this->yGroupTable . '
					 WHERE ygroup_id=' . $this->dao->quote($yGroupId);
		$yGroupInfo = $this->dao->getRow($strQuery);
		if(false == $yGroupInfo) {
			return array('errorMsg'=>'不存在的诊断组');
		}
		$this->dao->begin();
		$result = true;
		$strQuery = 'DELETE FROM ' . $this->yExamGroupTable . '
					 WHERE exam_id IN (' . $yGroupInfo['exam_list'] . ')';
		$result &= $this->dao->execute($strQuery);
		if($result) {
			$time = date('Y-m-d H:i:s');
			#$strQuery = 'DELETE FROM ' . $this->yGroupTable . '
			#			 WHERE ygroup_id=' . $this->dao->quote($yGroupId);
			$strQuery = 'UPDATE ' . $this->yGroupTable . '
						 SET is_remove=1,
						 	 update_user=' . $this->dao->quote($this->operator) . ',
						 	 update_at=' . $this->dao->quote('time') . '
						 WHERE ygroup_id=' . $this->dao->quote($yGroupId);
			$result &= $this->dao->execute($strQuery);
		}
		if($result) {
			$this->dao->commit();
			return array('success'=>true);
		} 
		$this->dao->rollback();
		return array('errorMsg'=>'诊断组删除失败');
	}
	
	public function getEPosList($yGroupId){
		$yGroupInfo = $this->findGroup($yGroupId);
		$strQuery = 'SELECT * FROM ' . $this->posTable . '
					 WHERE is_remove=0 
					   and pos_code IN(
					   	 SELECT pos_code FROM ' . $this->ePosTable . '
					   	 WHERE is_deleted=' . $this->dao->quote('') . '
					   	   AND exam_id IN (' . $yGroupInfo['exam_list'] . ')
					   )';
		$posList = $this->dao->getAll($strQuery);
		return $posList;
	}
	
	public function getYPosList($yGroupId) {
		$strQuery = 'SELECT * FROM ' . $this->yPosTable . '
					 WHERE ygroup_id=' . $this->dao->quote($yGroupId) . '
					   AND is_remove=0
					 ORDER BY create_at';
		$posList = $this->dao->getAll($strQuery);
		$posCodes = array($this->dao->quote(''));
		foreach ($posList as $key=>$pos) {
			$pCodes = explode(',', $pos['pos_epos_list']);
			$pos['pos_code_array']  = $pCodes;
			$posList[$key] = $pos;
			foreach ($pCodes as $pcode){
				$posCodes[] = $this->dao->quote($pcode);
			}
		}
		$strQuery = 'SELECT pos_code,pos_caption FROM ' . $this->posTable . '
					 WHERE pos_code IN (' . implode(',', $posCodes) . ')';
		$ePosList = $this->dao->getAll($strQuery);
		$posCaptions = array();
		foreach ($ePosList as $pos){
			$posCaptions[$pos['pos_code']] = $pos['pos_caption'];
		}
		foreach ($posList as $key=>$pos) {
			$pCaptions = array();
			foreach ($pos['pos_code_array'] as $pcode) {
				$pCaptions[] = $posCaptions[$pcode];
			}
			$posList[$key]['pos_captions'] = implode(',', $pCaptions);
		}
		return $posList;
	}
	
	public function savePosition($posInfo) {
		$yGroupId = SysUtil::uuid($posInfo['yGroupId']);
		$posCaption = SysUtil::safeString($posInfo['posCaption']);
		$posTelephone = SysUtil::safeString($posInfo['posTelephone']);
		$posAddr = SysUtil::safeString($posInfo['posAddr']);
		$ePosList = SysUtil::safeString($posInfo['posList']);
		$time = date('Y-m-d H:i:s');
		if($posInfo['pos_id']) {
			$posId = SysUtil::uuid($posInfo['pos_id']);
			$strQuery = 'UPDATE ' . $this->yPosTable . '
						 SET pos_caption=' . $this->dao->quote($posCaption) . ',
						 	 pos_telephone=' . $this->dao->quote($posTelephone) . ',
						 	 pos_addr=' . $this->dao->quote($posAddr) . ',
						 	 pos_epos_list=' . $this->dao->quote($ePosList) . ',
						 	 update_user=' . $this->dao->quote($this->operator) . ',
						 	 update_at=' . $this->dao->quote($time) . '
						 WHERE pos_id=' . $this->dao->quote($posId);
			if (false == $this->dao->execute($strQuery)) {
				return array('errorMsg'=>'诊断地点信息修改失败');
			}
		} else {
			$posId = SysUtil::uuid();
			$strQuery = 'INSERT INTO ' . $this->yPosTable . '
						 (pos_id,ygroup_id,pos_caption,pos_telephone,pos_addr,pos_epos_list,create_user,create_at,update_user,update_at)
						 VALUES (' . $this->dao->quote($posId) . ',
						 		 ' . $this->dao->quote($yGroupId) . ',
						 		 ' . $this->dao->quote($posCaption) . ',
						 		 ' . $this->dao->quote($posTelephone) . ',
						 		 ' . $this->dao->quote($posAddr) . ',
						 		 ' . $this->dao->quote($ePosList) . ',
						 		 ' . $this->dao->quote($this->operator) . ',
						 		 ' . $this->dao->quote($time) . ',
						 		 ' . $this->dao->quote($this->operator) . ',
						 		 ' . $this->dao->quote($time) . ')';
			if (false == $this->dao->execute($strQuery)) {
				return array('errorMsg'=>'诊断地点信息添加成功');
			}
		}
		return array('success'=>true);
		
	}
	
	public function findPos($pid) {
		$strQuery = 'SELECT * FROM ' . $this->yPosTable . '
					 WHERE pos_id=' . $this->dao->quote($pid);
		$posInfo = $this->dao->getRow($strQuery);
		return $posInfo;
	}
	
	public function delPos($pid) {
		#$strQuery = 'DELETE FROM ' . $this->yPosTable . '
		#			 WHERE pos_id=' . $this->dao->quote($pid);
		$time = date('Y-m-d H:i:s');
		$strQuery = 'UPDATE ' . $this->yPosTable . '
					 SET is_remove=1,
						 update_user=' . $this->dao->quote($this->operator) . ',
						 update_at=' . $this->dao->quote($time) . '
					 WHERE pos_id=' . $this->dao->quote($pid);
		if($this->dao->execute($strQuery)) {
			return array('success'=>true);
		}
		return array('errorMsg'=>'诊断地点删除失败');
	}	
	
	public function getTimeList($yGroupId) {
		$strQuery = 'SELECT * FROM ' . $this->yTimeTable . '
					 WHERE ygroup_id=' . $this->dao->quote($yGroupId) . '
					   AND is_remove=0
					 ORDER BY is_weekend,time_start';
		return $this->dao->getAll($strQuery);
	}
	
	public function saveTime($timeInfo) {
		$time = date('Y-m-d H:i:s');
		$yGroupId = SysUtil::uuid($timeInfo['ygroup_id']);
		$timeStart = trim($timeInfo['time_start']);
		$timeEnd = trim($timeInfo['time_end']);
		$timeText = $timeStart . ' — ' . $timeEnd;
		
		$timeId = trim($timeInfo['time_id']);
		if($timeId) {
			$strQuery = 'UPDATE ' . $this->yTimeTable . '
						 SET time_text=' . $this->dao->quote($timeText) . ',
						 	 time_start=' . $this->dao->quote($timeStart) . ',
						 	 time_end=' . $this->dao->quote($timeEnd) . ',
						 	 is_weekend=' . abs($timeInfo['is_weekend']) . ',
						 	 update_user=' . $this->dao->quote($this->operator) . ',
						 	 update_at=' . $this->dao->quote($time) . '
						 WHERE time_id=' . $this->dao->quote($timeId);
		} else {
			$timeId = SysUtil::uuid();
			$strQuery = 'INSERT INTO ' . $this->yTimeTable . '
						 (time_id,ygroup_id,time_text,time_start,time_end,is_weekend,create_user,create_at,update_user,update_at)
						 VALUES(' . $this->dao->quote($timeId) . ',
						 		' . $this->dao->quote($yGroupId) . ',
						 		' . $this->dao->quote($timeText) . ',
						 		' . $this->dao->quote($timeStart) . ',
						 		' . $this->dao->quote($timeEnd) . ',
						 		' . abs($timeInfo['is_weekend']) . ',
						 		' . $this->dao->quote($this->operator) . ',
						 		' . $this->dao->quote($time) . ',
						 		' . $this->dao->quote($this->operator) . ',
						 		' . $this->dao->quote($time) . ')';
		}
		if($this->dao->execute($strQuery)) {
			return array('success'=>true);
		}
		return array('errorMsg'=>'诊断时间信息保存成功');
	}
	
	public function delTime($tid) {
		#$strQuery = 'DELETE FROM ' . $this->yTimeTable . '
		#			 WHERE time_id=' . $this->dao->quote($tid);
		$time = date('Y-m-d H:i:s');
		$strQuery = 'UPDATE ' . $this->yTimeTable . '
					 SET is_remove=1,
					 	 update_user=' . $this->dao->quote($this->operator) . ',
					 	 update_at=' . $this->dao->quote($time) . '
					 WHERE time_id=' . $this->dao->quote($tid);
		return $this->dao->execute($strQuery);
	}
	
	public function getPosList($groupInfo) {
		$strQuery = 'SELECT pos_id,pos_caption FROM ' . $this->yPosTable . ' 
					 WHERE pos_id IN (
					 	SELECT distinct pos_id FROM ' . $this->yBatchTable . '
					 	WHERE ygroup_id=' . $this->dao->quote($groupInfo['ygroup_id']) . '
					 )';
		
		$posList = $this->dao->getAll($strQuery);
		return $posList;
	}
	
	public function getDateArray($groupInfo) {
		$weekArray = array('日','一','二','三','四','五','六');
		$startTime = strtotime(date('Y-m-d', strtotime($groupInfo['ygroup_time_start'])) . ' 00:00:00');
		$endTime = strtotime(date('Y-m-d', strtotime($groupInfo['ygroup_time_end'])) . ' 23:59:59');
		$dateArray = array();
		for ($t = $startTime; $t<$endTime;$t+=86400) {
			$week = $weekArray[date('w', $t)];
			$dateArray[date('Y-m-d', $t)] = date('n月j日（星期' . $week . '）', $t);
		}
		return $dateArray;
	}
	
	public function getBatchCount($yGroupId) {
		static $count = null;
		if(null === $count) {
			$strQuery = 'SELECT count(1) FROM ' . $this->yBatchTable . '
						 WHERE ygroup_id=' . $this->dao->quote($yGroupId) . '
						   AND is_remove=0';
			$count = $this->dao->getOne($strQuery);
		}
		return $count;
	}
	
	public function getBatchList($yGroupId, $currentPage=1, $pageSize=20) {
		$recordCount = $this->getBatchCount($yGroupId);
		$pageSize = abs($pageSize) > 0 ? $pageSize : 20;
		$pageCount = ceil($recordCount /$pageSize);
		$currentPage = abs($currentPage);
		if($currentPage> $pageCount) $currentPage = $pageCount;
		if($currentPage <1) $currentPage = 1;
		$strQuery = 'SELECT b.*,p.pos_caption,t.time_text,g.ygroup_caption,
							g.stu_filter,stu.cur_total, stu.cur_new,stu.cur_old
					 FROM ' . $this->yBatchTable . ' b 
					 LEFT JOIN ' . $this->yGroupTable . ' g
					 	ON g.ygroup_id=b.ygroup_id
					 LEFT JOIN ' . $this->yPosTable . ' p 
					 	ON b.pos_id=p.pos_id
					 LEFT JOIN ' . $this->yTimeTable . ' t
					 	ON b.time_id=t.time_id
					 LEFT JOIN (
					 	SELECT b.bid,count(stu.id) cur_total,
					 		   SUM(case WHEN stu.is_new=1 THEN 1 ELSE 0 END) cur_new,
					 		   SUM(case WHEN stu.is_new=0 THEN 1 ELSE 0 END) cur_old
					 	FROM ' . $this->yBatchTable . ' b
					 	LEFT JOIN ' . $this->yStudentTable . ' stu
					 	  ON b.bid=stu.batch_id
					 	WHERE b.ygroup_id=' . $this->dao->quote($yGroupId) . '
					 	GROUP BY b.bid
					 ) stu
					 	ON stu.bid=b.bid
					 WHERE b.is_remove=0 
					   AND g.is_remove=0
					   AND p.is_remove=0
					   AND t.is_remove=0
					   AND b.ygroup_id=' . $this->dao->quote($yGroupId);
		$order = 'ORDER BY pos_id,date,time_text';
		$batchList = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		return $batchList;
	}
	
	public function addBatch($batchInfo) {
		$time = date('Y-m-d H:i:s');
		$yGroupId = SysUtil::uuid($batchInfo['yGroupId']);
		$posId = SysUtil::uuid($batchInfo['pos_id']);
		$dateArray = explode(',', $batchInfo['date']);
		$timeArray = explode(',', $batchInfo['time']);
		$result = true;
		$this->dao->begin();
		foreach ($dateArray as $date) {
			foreach ($timeArray as $tid) {
				$strQuery = 'SELECT bid FROM ' . $this->yBatchTable . '
							 WHERE ygroup_id=' . $this->dao->quote($yGroupId) . '
							   AND pos_id=' . $this->dao->quote($posId) . '
							   AND date=' . $this->dao->quote($date) . '
							   AND time_id=' . $this->dao->quote($tid) . '
							   AND is_remove=0';
				$bInfo = $this->dao->getRow($strQuery);
				if($bInfo) {
					$strQuery = 'UPDATE ' . $this->yBatchTable . '
								 SET update_user=' . $this->dao->quote($this->operator) . ',
								 	 update_at=' . $this->dao->quote($time) . ',
								 	 total_cnt=' . abs($batchInfo['total_cnt']) . ',
								 	 new_cnt=' . abs($batchInfo['new_cnt']) . ',
								 	 old_cnt=' . abs($batchInfo['old_cnt']) . '
								 WHERE bid=' . $this->dao->quote($bInfo['bid']);
				} else {
					$bid = SysUtil::uuid();
					$strQuery = 'INSERT INTO ' . $this->yBatchTable . '
								 (ygroup_id,pos_id,date,time_id,total_cnt,new_cnt,old_cnt,create_user,create_at,update_user,update_at)
								 VALUES (' . $this->dao->quote($yGroupId) . ',
								 		 ' . $this->dao->quote($posId) . ',
								 		 ' . $this->dao->quote($date) . ',
								 		 ' . $this->dao->quote($tid) . ',
								 		 ' . abs($batchInfo['total_cnt']) . ',
								 		 ' . abs($batchInfo['new_cnt']) . ',
								 		 ' . abs($batchInfo['old_cnt']) . ',
								 		 ' . $this->dao->quote($this->operator) . ',
								 		 ' . $this->dao->quote($time) . ',
								 		 ' . $this->dao->quote($this->operator) . ',
								 		 ' . $this->dao->quote($time) . ')';
				}
				if($result){
					$result &= $this->dao->execute($strQuery);
				}
			}
		}
		if($result) {
			$this->dao->commit();
			return array('success'=>true);
		} else {
			$this->dao->rollback();
			return array('errorMsg'=>'诊断场次保存失败');
		}
	}
	
	public function editBatch($batchId, $batchInfo) {
		
	}
	
	public function delBatch($batchId) {
		$strQuery = 'UPDATE ' . $this->yBatchTable . '
					 SET is_remove=1,
					 	 update_user=' . $this->dao->quote($this->operator) . ',
					 	 update_at=' . $this->dao->quote(date('Y-m-d H:i:s')) . '
					 WHERE bid=' . $this->dao->quote($batchId);
		if($this->dao->execute($strQuery)) {
			return array('success'=>true);
		} else {
			return array('errorMsg'=>'诊断场次删除失败');
		}
	}
	
	private function getStuQuery($searchArgs) {
		$condition = ' yg.ygroup_id=' . $this->dao->quote($searchArgs['ygid']);
		if($searchArgs['keyword']) {
			$condition .= ' AND stu.bisvalid=1 AND (
						   stu.scode =' . $this->dao->quote($searchArgs['keyword']) . '
						   OR stu.saliascode=' . $this->dao->quote($searchArgs['keyword']) . '
						   OR stu.sname=' . $this->dao->quote($searchArgs['keyword']) . '
						   OR stu.smobile=' . $this->dao->quote($searchArgs['keyword']) . '
						   OR stu.sphone=' . $this->dao->quote($searchArgs['keyword']) . '
						   OR stu.sparents1phone=' . $this->dao->quote($searchArgs['keyword']) . '
						   OR stu.sparents2phone=' . $this->dao->quote($searchArgs['keyword']) . ')';
		} else {
			if($searchArgs['yuyue_pos']) {
				$condition .= ' AND yp.pos_id=' . $this->dao->quote($searchArgs['yuyue_pos']);
			}
			if($searchArgs['yuyue_date']) {
				$condition .= ' AND b.date=' . $this->dao->quote($searchArgs['yuyue_date']);
			}
			if($searchArgs['ytime_start']) {
				$condition .= ' AND ystu.yuyue_time >=' . $this->dao->quote($searchArgs['ytime_start']);
			}
			if($searchArgs['ytime_end']) {
				$condition .= ' AND ystu.yuyue_time <=' . $this->dao->quote($searchArgs['ytime_end']);
			}
		}
		$strQuery = 'SELECT {{placeholder}}
					 FROM ' . $this->yStudentTable . ' ystu,
					 	  ' . $this->stuTable . ' stu,
					 	  ' . $this->examTable . ' ex,
					 	  ' . $this->eGroupTable . ' eg,
					 	  ' . $this->esTable . ' es,
					 	  ' . $this->yBatchTable . ' b,
					 	  ' . $this->yGroupTable . ' yg,
					 	  ' . $this->yPosTable . ' yp,
					 	  ' . $this->yTimeTable . ' yt
					 WHERE ystu.stu_code=stu.scode
					   AND ystu.batch_id=b.bid
					   AND ystu.exam_id=ex.exam_id
					   AND ex.group_id=eg.group_id
					   AND ex.exam_id=es.exam_id
					   AND ystu.stu_code=es.stu_code
					   AND es.order_status !=1
					   AND es.is_cancel =0
					   AND ystu.ygroup_id=yg.ygroup_id
					   AND yp.pos_id=b.pos_id
					   AND yt.time_id=b.time_id
					   AND yg.is_remove=0
					   AND ' . $condition;
		return $strQuery;
	}
	
	public function getStuCount($searchArgs) {
		static $recordCount = null;
		if(null === $recordCount) {
			$placeHolder = ' count(1) ';
			$strQuery = $this->getStuQuery($searchArgs);
			$strQuery = str_replace('{{placeholder}}', $placeHolder, $strQuery);
			$recordCount = $this->dao->getOne($strQuery);
		}
		return $recordCount;
	}
	
	private function savePrintGroup($searchArgs, $searchQuery, $recordCount) {
		$queryMd5 = md5($searchQuery);
		$strQuery = 'SELECT * FROM ' . $this->printTable . '
					 WHERE query_md5=' . $this->dao->quote($queryMd5);
		$groupInfo = $this->dao->getRow($strQuery);
		if($groupInfo) {
			$groupId = $groupInfo['group_id'];
			if($recordCount > $groupInfo['total_cnt']) {
				$strQuery = 'UPDATE ' . $this->printTable . '
							 SET total_cnt=' . abs($recordCount) . ',
							 	 update_at=' . $this->dao->quote(date('Y-m-d H:i:s')) . '
							 WHERE query_md5=' . $this->dao->quote($queryMd5);
				$this->dao->execute($strQuery);
			}
		} else {
			$groupCaption = '';
			$groupId = SysUtil::uuid();
			if($searchArgs['yuyue_pos']) {
				$posInfo = $this->findPos($searchArgs['yuyue_pos']);
				$groupCaption .= $posInfo['pos_caption'];
			}
			if($searchArgs['yuyue_date']) {
				$groupCaption .= '[' . $searchArgs['yuyue_date'] . ']';
			}
			if(false == $groupCaption) {
				$groupCaption = '不限日期地点';
			}
			$strQuery = 'INSERT INTO ' . $this->printTable . '
						 (ygroup_id,group_caption,search_query,query_md5,total_cnt,print_cnt,ytime_start,ytime_end,create_at,update_at)
						 VALUES (' . $this->dao->quote($searchArgs['ygid']) . ',
						 		 ' . $this->dao->quote($groupCaption) . ',
						 		 ' . $this->dao->quote($searchQuery) . ',
						 		 ' . $this->dao->quote($queryMd5) . ',
						 		 ' . abs($recordCount) . ',0,
						 		 ' . $this->dao->quote($searchArgs['ytime_start']) . ',
						 		 ' . $this->dao->quote($searchArgs['ytime_end']) . ',
						 		 ' . $this->dao->quote(date('Y-m-d H:i:s')) . ',
						 		 ' . $this->dao->quote(date('Y-m-d H:i:s')) . ')';
			$this->dao->execute($strQuery);
		}
		$strQuery = 'UPDATE ' . $this->yStudentTable . '
					 SET print_cnt=0
					 WHERE id IN (
						SELECT id FROM (' . $searchQuery . ') tbl
					 )';
		$this->dao->execute($strQuery);
		return $groupId;
	}
	
	public function getStuList($searchArgs, $currentPage, $pageSize) {
		$columns = 'ystu.id,stu.sname,stu.scode,stu.saliascode,stu.sparents1phone,ex.exam_id,ex.exam_caption,eg.group_caption,es.exam_code,
					yg.ygroup_id,yg.ygroup_caption,b.date,yt.time_text,yp.pos_caption,ystu.yuyue_time,ystu.yuyue_ip,ystu.pdf_cnt,ystu.print_cnt';
		$recordCount = $this->getStuCount($searchArgs);
		$pageCount = ceil($recordCount / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = $this->getStuQuery($searchArgs);
		$strQuery = str_replace('{{placeholder}}', $columns, $strQuery);
		if($searchArgs['print']) {
			$printGroup = $this->savePrintGroup($searchArgs, $strQuery, $recordCount);
		}
		$order = ' ORDER BY yuyue_time';
		$stuList = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		$time = date('Y-m-d H:i');
		foreach ($stuList as $key=>$stu) {
			if($searchArgs['print']){
				$stuList[$key]['print_group'] = $printGroup;
			}
			$stuList[$key]['time_now'] = $time;
		}
		return $stuList;
	}
	
	public function searchStudents($searchArgs) {
		$strQuery = 'SELECT stu.sname,stu.scode,stu.saliascode,stu.ngrade1year,stu.sparents1phone,stu.sparents2phone,
							ex.exam_id,ex.exam_caption,es.exam_code
					 FROM ' . $this->stuTable . ' stu,
					 	  ' . $this->examTable . ' ex,
					 	  ' . $this->esTable . ' es
					 WHERE stu.scode=es.stu_code
					   AND ex.exam_id=es.exam_id
					   AND ex.group_id=' . abs($searchArgs['egid']) . '
					   AND stu.bisvalid=1 
					   AND es.order_status !=1
					   AND es.is_cancel=0
					   AND (
						   stu.scode =' . $this->dao->quote($searchArgs['keyword']) . '
						   OR stu.saliascode=' . $this->dao->quote($searchArgs['keyword']) . '
						   OR stu.sname=' . $this->dao->quote($searchArgs['keyword']) . '
						   OR stu.smobile=' . $this->dao->quote($searchArgs['keyword']) . '
						   OR stu.sphone=' . $this->dao->quote($searchArgs['keyword']) . '
						   OR stu.sparents1phone=' . $this->dao->quote($searchArgs['keyword']) . '
						   OR stu.sparents2phone=' . $this->dao->quote($searchArgs['keyword']) . ')';
		$stuList = $this->dao->getAll($strQuery);
		$stuModel = D('Student');
		$stuList = $stuModel->allInfo($stuList);
		$strQuery = 'SELECT yg.ygroup_id,yg.ygroup_caption,yeg.exam_id,
						 	yg.stu_filter,yg.study_date_start,yg.study_date_end
					 FROM ' . $this->yExamGroupTable . ' yeg,
						  ' . $this->yGroupTable . ' yg,
						  ' . $this->examTable . ' ex
					 WHERE yeg.ygroup_id=yg.ygroup_id
					   AND ex.exam_id=yeg.exam_id
					   AND yg.is_remove=0
					   AND ex.group_id =' . abs($searchArgs['egid']);
		$yGroupList = $this->dao->getAll($strQuery);
		$yGroupArray = array();
		foreach ($yGroupList as $yGroup) {
			$yGroupArray[$yGroup['exam_id']] = $yGroup;
		}
		foreach ($stuList as $key=>$stu) {
			$stuList[$key]['ygroup_id'] = $yGroupArray[$stu['exam_id']]['ygroup_id'];
			$stuList[$key]['ygroup_caption'] = $yGroupArray[$stu['exam_id']]['ygroup_caption'];
			$stuList[$key]['is_new'] = $this->chkIfNew($stu, $yGroupArray[$stu['exam_id']]);
		}
		return $stuList;
	}
	
	public function chkIfNew($student, $yGroup) {
		if($yGroup['stu_filter']) {
			$strQuery = 'SELECT count(1) FROM ' . $this->rosterTable . '
						 WHERE sstudentcode=' . $this->dao->quote($student['scode']) . '
						   AND dtindate >=' . $this->dao->quote($yGroup['study_date_start']) . '
						   AND dtindate <=' . $this->dao->quote($yGroup['study_date_end']) . '
						   AND bvalid=1';
			if($this->dao->getOne($strQuery) > 0) {
				return 0;
			} else {
				return 1;
			}
		} else {
			return -1;
		}
	}
	
	public function getYGroupInfo($searchArgs) {
		$yGroupId = SysUtil::uuid($searchArgs['ygid']);
		if(false == $searchArgs['pos_id']) {
			$posList = $this->getFreePosList($yGroupId, $searchArgs['scode']);
			if($posList) {
				$posId = $posList[0]['pos_id'];
			} else {
				return array('errorMsg'=>'没有剩余名额');
			}
		} else {
			$posId = SysUtil::uuid($searchArgs['pos_id']);
		}
		if($posId) {
			$dateList = $this->getFreeDateList($posId);
			if(false == $dateList) {
				return array('errorMsg'=>'没有剩余名额');
			}
			if($searchArgs['yuyue_date']) {
				if(isset($dateList[$searchArgs['yuyue_date']])) {
					$yuyueDate = $searchArgs['yuyue_date'];
				}
			}
			if($dateList && false == $yuyueDate) {
				foreach ($dateList as $yuyueDate=>$null) {
					break;
				}
			}
		}
		if($yuyueDate) {
			$timeList = $this->getFreeTimeList($posId, $yuyueDate,$stuCode);
			if(false == $timeList) {
				return array('errorMsg'=>'没有剩余名额');
			}
		}
		unset($searchArgs);
		unset($null);
		return get_defined_vars();
	}
	
	private function getFreePosList($yGroupId,$stuCode) {
		$yGroupInfo = $this->findGroup($yGroupId);
		$student = array('stu_code'=>$stuCode);
		if($yGroupInfo['stu_filter']) {
			if($this->chkIfNew($student, $yGroupInfo['study_date_tart'], $yGroupInfo['study_date_end'])) {
				$column = 'new';
			} else {
				$column = 'old';
			}
		} else {
			$column = 'total';
		}
		$column = 'total';
		$posView = 'SELECT p.pos_id, p.pos_caption,p.pos_addr,p.pos_telephone,p.pos_epos_list,
							sum(b.total_cnt) total_cnt,
							sum(b.new_cnt) new_cnt,
							sum(b.old_cnt) old_cnt
					 FROM ' . $this->yBatchTable . ' b,
					 	  ' . $this->yPosTable . ' p
					 WHERE b.pos_id=p.pos_id 
					   AND b.is_remove=0
					   AND p.is_remove=0
					   AND b.ygroup_id=' . $this->dao->quote($yGroupId) . '
					 GROUP BY p.pos_id,p.pos_caption,p.pos_addr,p.pos_telephone,p.pos_epos_list';
		$stuView = 'SELECT b.pos_id,count(stu.stu_code) cur_total,
							SUM(CASE WHEN stu.is_new=1 THEN 1 ELSE 0 END) cur_new,
							SUM(CASE WHEN stu.is_new=0 THEN 1 ELSE 0 END) cur_old
					 FROM ' . $this->yBatchTable . ' b
					 LEFT JOIN ' . $this->yStudentTable . ' stu
					   ON b.bid=stu.batch_id
					 WHERE b.is_remove=0 
					   AND b.ygroup_id=' . $this->dao->quote($yGroupId) . '
					 GROUP BY b.pos_id';
		$strQuery = 'SELECT p.pos_id,p.pos_caption,p.pos_addr,p.pos_telephone
					 FROM ( ' . $posView . ' ) p
						  ,(' . $stuView . ') stu
					 WHERE p.pos_id=stu.pos_id
			 		   AND p.' . $column . '_cnt >stu.cur_' . $column . '
			 		 ORDER BY p.pos_epos_list';
		$posList = $this->dao->getAll($strQuery);
		return $posList;
	}
	
	public function getFreeDateList($posId, $stuCode) {
		$yGroupInfo = $this->findGroupByPos($posId);
		$student = array('stu_code'=>$stuCode);
		if($yGroupInfo['stu_filter']) {
			if($this->chkIfNew($student, $yGroupInfo['study_date_tart'], $yGroupInfo['study_date_end'])) {
				$column = 'new';
			} else {
				$column = 'old';
			}
		} else {
			$column = 'total';
		}
		$column = 'total';
		$dateView = 'SELECT date,SUM(total_cnt) total_cnt,SUM(new_cnt) new_cnt,SUM(old_cnt) old_cnt
					 FROM ' . $this->yBatchTable . '
					 WHERE is_remove=0 
					   AND pos_id=' . $this->dao->quote($posId) . '
					 GROUP BY date';
		$stuView = 'SELECT b.date,COUNT(stu.stu_code) cur_total,
						   SUM(CASE WHEN stu.is_new=1 THEN 1 ELSE 0 END) cur_new,
						   SUM(CASE WHEN stu.is_new=0 THEN 1 ELSE 0 END) cur_old
					FROM ' . $this->yBatchTable . ' b
					LEFT JOIN ' . $this->yStudentTable . ' stu
					  ON stu.batch_id=b.bid 
					WHERE b.is_remove=0 
					  AND b.bid IN (
						SELECT bid FROM ' . $this->yBatchTable . '
						WHERE pos_id=' . $this->dao->quote($posId) . '
						  AND is_remove=0
						)
					GROUP BY b.date';
		$strQuery = 'SELECT d.date 
					 FROM (' . $dateView . ' ) d,
					 	  (' . $stuView . ') stu
					 WHERE d.date=stu.date
					 AND d.' . $column . '_cnt > stu.cur_' . $column . '
					 ORDER BY d.date';
		$dateList = $this->dao->getAll($strQuery);
		$dateArray = array();
		$weekArrray = array('日', '一', '二', '三', '四', '五', '六');
		foreach ($dateList as $row) {
			$time = strtotime($row['date']);
			$dateArray[$row['date']] = date('n月j日(星期' . $weekArrray[date('w', $time)] . ')', $time);
		}
		return $dateArray;
	}
	
	public function getFreeTimeList($posId, $date, $stuCode) {
		$groupInfo = $this->findGroupByPos($posId);
		$student = array('stu_code'=>$stuCode);
		if($groupInfo['stu_filter']) {
			if ($this->chkIfNew($student, $groupInfo['study_date_start'], $groupInfo['study_date_end'])) {
				$column = 'new';
			} else {
				$column = 'old';
			}
		} else {
			$column = 'total';
		}
		$column = 'total';
		$strQuery = 'SELECT b.bid,t.time_id,t.time_text,
							b.total_cnt total_cnt,
							b.new_cnt new_cnt,
							b.old_cnt old_cnt,
							COUNT(stu.stu_code) cur_total,
							SUM(case WHEN stu.is_new=1 THEN 1 ELSE 0 END) cur_new,
							SUM(case WHEN stu.is_new=0 THEN 1 ELSE 0 END) cur_old
					 FROM ' . $this->yTimeTable . ' t,
					 	  ' . $this->yBatchTable . ' b
					 LEFT JOIN ' . $this->yStudentTable . ' stu
					   ON stu.batch_id=b.bid
					 WHERE b.is_remove=0
						AND b.time_id=t.time_id
						AND t.is_remove=0
					 	AND b.pos_id=' . $this->dao->quote($posId) . '
					 	AND b.date=' . $this->dao->quote($date) . '
					 GROUP BY b.bid,t.time_id,t.time_text,b.total_cnt,b.new_cnt,b.old_cnt';
		$strQuery = 'SELECT bid,time_text 
					 FROM (' . $strQuery . ') t
					 WHERE ' . $column . '_cnt > cur_' . $column . '
					 ORDER BY time_text';
		$timeList = $this->dao->getAll($strQuery);
		return $timeList;
	}
	
	private function findGroupByPos($posId) {
		$strQuery = 'SELECT * FROM ' . $this->yGroupTable . '
					 WHERE ygroup_id IN (
					 	SELECT ygroup_id FROM ' . $this->yPosTable . '
					 	WHERE pos_id=' . $this->dao->quote($posId) . '
					 )';
		return $this->dao->getRow($strQuery);
	}
	
	public function saveYuyueInfo($yuyueInfo) {
		$strQuery = 'DELETE FROM ' . $this->yStudentTable . '
					 WHERE ygroup_id=' . $this->dao->quote($yuyueInfo['ygid']) . '
					   AND stu_code=' . $this->dao->quote($yuyueInfo['stu_code']);
		$this->dao->execute($strQuery);
		$strQuery = 'INSERT INTO ' . $this->yStudentTable . '
					 (id,ygroup_id,exam_id,stu_code,batch_id,is_new,yuyue_time,yuyue_ip)
					 VALUES (' . $this->dao->quote(SysUtil::uuid()) . ',
					 		 ' . $this->dao->quote(SysUtil::uuid($yuyueInfo['ygid'])) . ',
					 		 ' . abs($yuyueInfo['exam_id']) . ',
					 		 ' . $this->dao->quote($yuyueInfo['stu_code']) . ',
					 		 ' . $this->dao->quote(SysUtil::uuid($yuyueInfo['batch_id'])) . ',
					 		 ' . abs($yuyueInfo['is_new'] == 1) . ',
					 		 ' . $this->dao->quote(date('Y-m-d H:i:s')) . ',
					 		 ' . $this->dao->quote($_SERVER['REMOTE_ADDR']) . ')';
		if ($this->dao->execute($strQuery)) {
			return array('success'=>true);
		}
		return array('errorMsg'=>'预约信息添加失败');
	}
	
	public function getYuyueSms($yuyueInfo, $stuInfo) {
		$batchId = $yuyueInfo['batch_id'];
		$batchInfo = $this->getBatchInfo($batchId);
		$smsContent = "家长您好，您预约了" . date('n月j日', strtotime($batchInfo['date'])) . ", ". $batchInfo['time_text'] . '的竞赛诊断，诊断地点为' . $batchInfo['pos_caption'] . ',请准时参加！';
		return $smsContent;
	}
	
	public function getBatchInfo($batchId) {
		$batchId = SysUtil::uuid($batchId);
		$strQuery = 'SELECT ygroup_caption,pos_caption,pos_addr,date,time_text
					 FROM ' . $this->yBatchTable . ' yb,
					 	  ' . $this->yGroupTable . ' yg,
					 	  ' . $this->yPosTable . ' yp,
					 	  ' . $this->yTimeTable . ' yt
					 WHERE yb.bid=' . $this->dao->quote($batchId) . '
					   AND yb.ygroup_id=yg.ygroup_id
					   AND yb.time_id=yt.time_id
					   AND yp.pos_id=yb.pos_id';
		$batchInfo = $this->dao->getRow($strQuery);
		return $batchInfo;
	}
	
	public function doPrint($args) {
		extract($args);
		set_time_limit(0);
		if($stu && $subject) {
			$strQuery = 'SELECT * FROM ' . $this->yStudentTable . '
						 WHERE id=' . $this->dao->quote($stu);
			$stuInfo = $this->dao->getRow($strQuery);
			$subjects = unserialize(Xxtea::decrypt($subject, C('SCORE_ENCRYPT_KEY')));
			$cnt = 0;
			foreach ($subjects as $sbj) {
				$fileName = $stuInfo['exam_id'] . '-' . $stuInfo['stu_code'] . '-' . $sbj . '.pdf';
				if(file_exists(APP_DIR . '/Report/' . $stuInfo['stu_code'] . '/' . $fileName)) {
					$cnt++;
				} else {
					$reportKey = Xxtea::encrypt($stuInfo['exam_id'] . '_' . $sbj . '_' . $stuInfo['stu_code'], C('SCORE_ENCRYPT_KEY'));
					file_get_contents('http://' . C('PRINT_DOMAIN') . '/Exam/Score/report/key/' . $reportKey);
					echo $fileName . '<script type="text/javascript">location.reload();//1</script>';
					exit;
				}
			}
			if($cnt == $stuInfo['pdf_cnt']) {
				$strQuery = 'UPDATE ' . $this->yStudentTable . '
							 SET print_cnt=pdf_cnt
							 WHERE id=' . $this->dao->quote($stu);
				$this->dao->execute($strQuery);
				$strQuery = 'UPDATE ' . $this->printTable . '
							 SET print_cnt = print_cnt+1
							 WHERE group_id=' . $this->dao->quote($gid);
				$this->dao->execute($strQuery);
				echo '<script type="text/javascript">location="/Exam/Yuyue/printGroup/gid/' . $gid . '";</script>';
				exit;
			}
		}
		$strQuery = 'SELECT * FROM ' . $this->printTable . '
					 WHERE group_id=' . $this->dao->quote($gid);
		$groupInfo = $this->dao->getRow($strQuery);
		$strQuery = $groupInfo['search_query'];
		if(false == $strQuery) {
			echo '请再次点击打印按钮开始打印进程';
			exit;
		}
		$strQuery .= ' AND (ystu.pdf_cnt IS NULL OR ystu.print_cnt IS NULL OR ystu.pdf_cnt > ystu.print_cnt)';
		
		$stuInfo = $this->dao->getRow($strQuery);
		if($stuInfo) {
			$strQuery = 'SELECT * FROM ' . $this->scoreTable . '
						 WHERE exam_id=' . abs($stuInfo['exam_id']) . '
						   AND paper_total_score >0
						   AND stu_code=' . $this->dao->quote($stuInfo['scode']) . '
						   AND paper_type=' . $this->dao->quote('real');
			$scoreList = $this->dao->getAll($strQuery);
			if($scoreList) {
				$subjects = array();
				foreach ($scoreList as $score) {
					$subjects[$score['paper_subject']] = $score['paper_subject'];
				}
				$subjects = array_values($subjects);
				$subject = $subjects[0];
				if(false == $stuInfo['pdf_cnt']) {
					$strQuery = 'UPDATE ' . $this->yStudentTable . '
								 SET pdf_cnt=' . sizeof($subjects) . ',
								 	print_cnt=0
								 WHERE id=' . $this->dao->quote($stuInfo['id']);
					$this->dao->execute($strQuery);
				} else {
					
				}
				$stuName = $stuInfo['sname'];
				$sbjKey = Xxtea::encrypt($subjects, C('SCORE_ENCRYPT_KEY'));
				echo $stuName . '<script type="text/javascript">location="/Exam/Yuyue/printGroup/gid/' . $gid . '/stu/' . $stuInfo['id'] . '/subject/' . $sbjKey . '"</script>';
				exit;
			} else {
				$strQuery = 'UPDATE ' . $this->yStudentTable . '
							 SET pdf_cnt=0,print_cnt=0
							 WHERE id=' . $this->dao->quote($stuInfo['id']);
				$this->dao->execute($strQuery);
				echo '缺考<script type="text/javascript">location.reload();//3</script>';
				exit;
			}
		} else {
			$strQuery = 'UPDATE ' . $this->printTable . '
						 SET print_cnt=total_cnt
						 WHERE group_id=' . $this->dao->quote($gid);
			$this->dao->execute($strQuery);
			echo '打印完毕<script type="text/javascript">parent.finishPrint()</script>';
		}
	}
	
	public function findPrintGroup($gid) {
		$strQuery = 'SELECT * FROM ' . $this->printTable . '
					 WHERE group_id=' . $this->dao->quote($gid);
		return $this->dao->getRow($strQuery);
	}
	
	public function getPrintGroups() {
		$strQuery = 'SELECT pg.*,yg.ygroup_caption 
					 FROM ' . $this->printTable . ' pg,
					 	  ' . $this->yGroupTable . ' yg
					 WHERE yg.ygroup_id=pg.ygroup_id ';
		$order = 'ORDER BY create_at DESC';
		$groupList = $this->dao->getLimit($strQuery, 1, 100, $order);
		return $groupList;
	}
	
	public function zipGroup($groupInfo) {
		set_time_limit(0);
		$strQuery = $groupInfo['search_query'];
		$stuList = $this->dao->getAll($strQuery);
		$srcDir = APP_DIR . '/Report/';
        $gid = md5(serialize($groupInfo));
		$targetDir = APP_DIR . '/Download/' . $groupInfo['group_caption'] . '_' . $gid . '/';
		if(false == is_dir($targetDir)) {
			@mkdir($targetDir, 0777, true);
		}
		foreach ($stuList as $stu) {
			$strQuery = 'SELECT * FROM ' . $this->esTable . '
						 WHERE exam_id=' . ($stu['exam_id']) . '
						   AND stu_code=' . $this->dao->quote($stu['scode']) . '
						   AND is_cancel=0
						   AND order_status !=1';
			$esInfo = $this->dao->getRow($strQuery);
			$fileList = glob($srcDir . '/' . $stu['scode'] . '/' . $stu['exam_id'] . '-' . $stu['scode'] . '-*.pdf');
			foreach ($fileList as $file) {
				$fileName = basename($file);
				copy($file, $targetDir . '/' . mb_convert_encoding($stu['sname'], 'CP936', 'UTF-8') . '-' . $fileName);
				preg_match('/\d+\-[^\-]+\-([^\.]+)\.pdf/i', $fileName, $ar);
				$subject = $ar[1];
				$scanFiles = glob(APP_DIR . '/Scan/' . $stu['exam_id'] . '/' . $subject . '/*' . $esInfo['exam_code'] . '*');
				$i=1;
				foreach($scanFiles as $file) {
					copy($file, $targetDir . '/' . mb_convert_encoding($stu['sname'], 'CP936', 'UTF-8') . '-' . $stu['exam_id'] . '-' . $stu['scode'] . '-' . $subject . '-' . $i . '.jpg');
					$i++;
				}
			}
		}
        sleep(2);
		
		@chdir($targetDir);
		exec('tar -cvf ' . $groupInfo['group_caption'] . '_' . $gid . '.tar *.pdf *.jpg');
		exec('unlink *.pdf');
		$zipFile = $targetDir . '/' . $groupInfo['group_caption'] . '_' . $gid . '.tar';
		return $zipFile;
	}
}
?>
