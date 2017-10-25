<?php
class ExamStudentModel {
    public $dao = null;
    private $operatTime = '';
    private $userKey = '';
    private $tableName = 'ex_exam_students';
    private $stuTable = 'bs_student';
    private $examTable = 'ex_exams';
    private $posTable = 'ex_positions';
    private $cancelLogTbl = 'ex_cancel_logs';
    private $examPosTable = 'ex_exam_positions';
    private $scoreTable = 'ex_exam_scores';
    private $activeLogTable = 'ex_exam_active_logs';
    private $greenTable = 'ex_green_students';
    
    
    public function __construct() {
        $this->dao = Dao::getDao();
        $this->operatTime = date('Y-m-d H:i:s');
        if(class_exists('User', false)) {
        	$operator = User::getLoginUser();
        	$this->userKey = $operator->getUserKey();
        }
    }
    
    public function getEsInfo($examId, $stuCode) {
    	$strQuery = 'SELECT * FROM ' . $this->tableName . '
    				 WHERE exam_id=' . abs($examId) . '
    				   AND stu_code=' . $this->dao->quote($stuCode) . '
    				   AND is_cancel=0
    				   AND order_status!=1';
    	return $this->dao->getRow($strQuery);
    }
    
    public function getPositions($examId) {
        $strQuery = 'SELECT pos.pos_code,pos.pos_caption,ep.pos_code_pre,count(es.stu_code) pos_cnt
                     FROM ' . $this->posTable . ' pos,
                          ' . $this->examPosTable . ' ep
                     LEFT JOIN ' . $this->tableName . ' es 
        				ON es.exam_id=ep.exam_id 
        				AND es.pos_code=ep.pos_code
        				AND es.is_cancel=0
                     WHERE ep.exam_id=' . abs($examId) . '
                       AND pos.is_remove=0
                       AND ep.pos_code=pos.pos_code
                     GROUP BY pos.pos_code,pos.pos_caption,ep.pos_code_pre
                     ORDER BY pos.pos_code ASC';
        $posList = $this->dao->getAll($strQuery);
        $posArray = array();
        foreach ($posList as $key=>$pos) {
            $posArray[$pos['pos_code']] = $pos;
        }
        
        return $posArray;
    }
    
    public function getStudentCount($searchArgs) {
        static $countArray = array();
        $key = md5(serialize($searchArgs));
        if(false == isset($countArray[$key])) {
            $strQuery = 'SELECT count(1) 
                         FROM ' . $this->tableName . ' es,
                              ' . $this->stuTable . ' stu
                         WHERE stu.scode=es.stu_code 
                           AND es.is_cancel=0 '
                           ;
            if(is_array($searchArgs)) {
            	$strQuery .= ' AND es.exam_id=' . abs($searchArgs['examId']);
	            if($searchArgs['stuName']) {
	                $strQuery .= ' AND (stu.sname LIKE ' . $this->dao->quote('%' . $searchArgs['stuName'] . '%') . '
	                				 OR stu.scode=' . $this->dao->quote($searchArgs['stuName']) . '
	                				 OR stu.saliascode=' . $this->dao->quote($searchArgs['stuName']) . '
	                )';
	            } else if ($searchArgs['posCode']) {
	                $strQuery .= ' AND es.pos_code=' . $this->dao->quote($searchArgs['posCode']);
	            }
            } elseif (is_string($searchArgs)) {
            	$strQuery .= ' AND ' . $searchArgs;
            }
            $countArray[$key] = $this->dao->getOne($strQuery);
        }
        return $countArray[$key];
    }
    
    public function getStudentList($searchArgs, $currentPage=0, $pageSize=0) {
        $strQuery = 'SELECT stu.sname stu_name,stu.saliascode,stu.ngender,stu.ngrade1year,
        					stu.sparents1phone,stu.sparents2phone,clog.card_num,es.* 
                     FROM ' . $this->stuTable . ' stu,
                          ' . $this->tableName . ' es
                     LEFT JOIN ' . $this->activeLogTable . ' clog
                       ON clog.stu_code=es.stu_code 
                      AND es.exam_id=clog.exam_id
                     WHERE es.stu_code=stu.scode
                       AND es.is_cancel=0';
        if(is_array($searchArgs)) {
        	$strQuery .= ' AND es.exam_id=' . abs($searchArgs['examId']);
	        if($currentPage && $pageSize && $searchArgs['stuName']) {
	            $strQuery .= ' AND (stu.sname LIKE ' . $this->dao->quote('%' . $searchArgs['stuName'] . '%') . '
	            				 OR stu.scode=' . $this->dao->quote($searchArgs['stuName']) . '
	                			 OR stu.saliascode=' . $this->dao->quote($searchArgs['stuName']) . '
	            			)';
	        } else if ($searchArgs['posCode']) {
	            $strQuery .= ' AND pos_code=' . $this->dao->quote($searchArgs['posCode']);
	        }
        } else if(is_string($searchArgs)) {
        	$strQuery .= ' AND ' . $searchArgs;
        }
        $order = 'ORDER BY exam_code';
        if($_POST['sort']) {
        	if($_POST['sort'] == 'signup_time') {
        		$order = 'ORDER BY create_at ' . $_POST['order'];
        	}
        }
        if($currentPage && $pageSize) {
            $recordCount = $this->getStudentCount($searchArgs);
            $pageCount = ceil($recordCount / $pageSize);
            if($pageCount == 0) {
                $pageCount = 1;
            }
            if($currentPage > $pageCount && is_array($searchArgs)) $currentPage = $pageCount;
            if($currentPage < 1) $currentPage = 1;
            $stuList = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
        } else {
            $stuList = $this->dao->getAll($strQuery . ' ' . $order);
        }
        
        $examId = abs($stuList[0]['exam_id']);
        $examPositions = $this->getPositions($examId);
        foreach ($stuList as $key=>$stu) {
            $stuList[$key]['stu_gender'] = $stu['ngender'] == 2 ? '女' : '男';
            $stuList[$key]['pos_caption'] = $examPositions[$stu['pos_code']]['pos_caption'];
            if($stu['real_exam_code']) {
            	$stuList[$key]['exam_code'] = $stu['real_exam_code'];
            }
        }
        return $stuList;
    }
    
    public function signupInfo($signupId) {
        $strQuery = 'SELECT * FROM ' .$this->tableName . '
                     WHERE is_cancel=0 AND id=' . $this->dao->quote($signupId);
        return $this->dao->getRow($strQuery);
    }
    
    public function cancelSignup($stuCode, $signupId) {
        $this->dao->begin();
        $signupInfo = $this->signupInfo($signupId);
        if(false == $signupInfo || strtoupper($signupInfo['stu_code']) != strtoupper($stuCode)) {
            return array('errorMsg'=>'报名信息不符，操作失败');
        }
        $strQuery = 'UPDATE  ' . $this->tableName . '
                     SET is_cancel=' . time() . ',
                         cancel_operator=' . $this->dao->quote($this->userKey) . '
                     WHERE id=' . $this->dao->quote($signupId) . '
                       AND is_cancel=0';
        if($this->dao->execute($strQuery) && $this->addCancelLog($signupInfo)) {
            $this->dao->commit();
            return array('success'=>true);
        }
        $this->rollback();
        return array('errorMsg'=>'取消报名失败，请联系管理员');
    }
    
    public function addCancelLog($signupInfo) {
        $time = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'];
        $strQuery = 'INSERT INTO ' . $this->cancelLogTbl . '
                     (signup_id,stu_code,exam_code,exam_id,pos_code,operator,signup_time,cancel_at,cancel_ip)
                     VALUES (
                        ' . $this->dao->quote($signupInfo['id']) . ',
                        ' . $this->dao->quote($signupInfo['stu_code']) . ',
                        ' . $this->dao->quote($signupInfo['exam_code']) . ',
                        ' . abs($signupInfo['exam_id']) . ',
                        ' . $this->dao->quote($signupInfo['pos_code']) . ',
                        ' . $this->dao->quote($this->userKey) . ',
                        ' . $this->dao->quote($signupInfo['signup_time']) . ',
                        ' . $this->dao->quote($time) . ',
                        ' . $this->dao->quote($ip) . ')';
        return $this->dao->execute($strQuery);
    }
    
    public function searchStudents($searchOptions) {
    	foreach ($searchOptions as $key=>$val) {
    		$searchOptions[$key] = SysUtil::safeString($val);
    	}
    	
    	switch ($searchOptions['type']) {
    		case 'code':
    			$stuList = $this->searchStudentsByCode($searchOptions);
    		break;
    		case 'name':
    			$stuList = $this->searchStudentsByName($searchOptions['exam_id'], $searchOptions['stu_name']);
    		break;
    		case 'mobile':
    			$stuList = $this->searchStudentsByMobile($searchOptions['exam_id'], $searchOptions['stu_mobile']);
    		break;
    	}
    	$gradeYearModel = D('GradeYear');
    	$gradeYears = $gradeYearModel->getGradeYears();
    	foreach ($stuList as $key=>$stu) {
    		$stuList[$key]['stu_grade'] = $gradeYears[$stu['ngrade1year']];
    	}
    	return $stuList;
    }
    
    private function searchStudentsByCode($searchOptions) {
    	if($searchOptions['exam_code']) {
    		$searchArgs = ' es.exam_id=' . abs($searchOptions['exam_id']) . '
    				   AND  es.exam_code=' . $this->dao->quote($searchOptions['exam_code']);
    	} else if(false == $searchOptions['code_suffix']) {
    		return $this->searchNextStudents($searchOptions['exam_id'], $searchOptions['subject_code'], $searchOptions['pos_code']);
    	} else if($searchOptions['code_pre'] && $searchOptions['code_suffix']) {
    		$examCode = $searchOptions['code_pre'] . sprintf('%05d', $searchOptions['code_suffix']);
    		$searchArgs = ' es.exam_id=' . abs($searchOptions['exam_id']) . '
    				   AND  es.exam_code=' . $this->dao->quote($examCode);
    	}
    	return $this->getStudentList($searchArgs);
    }
    
    private function searchNextStudents($examId, $subjectCode, $posCode) {
    	$strQuery = 'SELECT * FROM ' . $this->scoreTable . ' 
    				 WHERE exam_id=' . abs($examId) . '
    				   AND subject_code=' . $this->dao->quote($subjectCode) . '
    				   AND pos_code=' . $this->dao->quote($posCode);
    	$order = 'ORDER BY create_at DESC';
    	$stuList = $this->dao->getLimit($strQuery, 1, 1, $order);
    	if($stuList) {
    		$curStudent = $stuList[0];
			$examCode = $curStudent['exam_code'];
			$searchArgs = ' es.exam_id=' . abs($examId) . ' 
					AND es.pos_code=' . $this->dao->quote($posCode) . ' 
					AND es.exam_code >' . $this->dao->quote($examCode);
    	} else {
    		$searchArgs = ' es.exam_id=' . abs($examId) . '
    					AND es.pos_code=' . $this->dao->quote($posCode);
    	}
    	
		return $this->getStudentList($searchArgs, 1, 1);
    	
    }
    
    private function searchStudentsByName($examId, $stuName) {
    	$searchArgs = ' stu.bisvalid=1 AND stu.sname=' . $this->dao->quote($stuName);
    	$strQuery = 'SELECT stu.sname stu_name,stu.saliascode,stu.scode stu_code,stu.sparents1phone,stu.ngrade1year,
    						stu.sparents2phone,es.pos_code,es.exam_id,es.exam_code,pos.pos_caption
    				 FROM ' . $this->stuTable . ' stu
    				 LEFT JOIN ' . $this->tableName . ' es
    				 	ON es.stu_code=stu.scode 
    				 		AND es.is_cancel=0
    				 		AND es.exam_id=' . abs($examId) . ' 
    				 LEFT JOIN ' . $this->posTable . ' pos
    				 	ON pos.pos_code=es.pos_code
    				 WHERE ' . $searchArgs;
    	
    	return $this->dao->getAll($strQuery);
    }
    
    private function searchStudentsByMobile($examId, $mobile) {
    	$searchArgs = ' stu.bisvalid=1 AND (
    					stu.sphone=' . $this->dao->quote($mobile) . '
    				OR  stu.smobile=' . $this->dao->quote($mobile) . '
    				OR stu.sloginmobile=' . $this->dao->quote($mobile) . '
    				OR stu.sparents1phone=' . $this->dao->quote($mobile) . '
    				OR stu.sparents2phone=' . $this->dao->quote($mobile) . '
    	)';
    	$strQuery = 'SELECT stu.sname stu_name,stu.saliascode,stu.scode stu_code,stu.sparents1phone,stu.ngrade1year,
    						stu.sparents2phone,es.pos_code,es.exam_id,es.exam_code,pos.pos_caption 
    				 FROM ' . $this->stuTable . ' stu
    				 LEFT JOIN ' . $this->tableName . ' es
    				 	ON es.stu_code=stu.scode 
    				 		AND es.is_cancel=0
    				      	AND  es.exam_id=' . abs($examId) . ' 
    				 LEFT JOIN ' . $this->posTable . ' pos
    				 	ON pos.pos_code=es.pos_code
    				 WHERE ' . $searchArgs;
    	
    	return $this->dao->getAll($strQuery);
    }
    
    public function findNextStudent($examId, $examCode, $step=1) {
    	$strQuery = 'SELECT * FROM ' . $this->tableName . ' 
    				 WHERE exam_id=' . abs($examId) . ' 
    				   AND exam_code=' . $this->dao->quote($examCode);
    	$esInfo = $this->dao->getRow($strQuery);
    	$searchArgs = 'es.exam_id=' . abs($examId) . '
    				   AND es.pos_code=' . $this->dao->quote($esInfo['pos_code']) . '
    				   AND es.exam_code>' . $this->dao->quote($examCode);
    	$stuList = $this->getStudentList($searchArgs, $step, 1);
    	return $stuList[0];
    }
    
    private function getCancelLogView($examId) {
    	$strQuery = 'SELECT stu.sname,stu.scode,stu.saliascode,stu.dtbirthday,stu.ngender,stu.ngrade1year,stu.sparents1phone,stu.sparents2phone,
    				 log.exam_code,log.pos_code,log.signup_time,log.cancel_at,log.cancel_ip, log.operator
    				 FROM ' . $this->stuTable . ' stu,
    				 	  ' . $this->cancelLogTbl . ' log
    				 WHERE log.stu_code=stu.scode
    				   AND log.exam_id=' . abs($examId);
    	return $strQuery;
    }
    
    private function getCancelCondition($keyword) {
    	static $conditions = array();
    	if($keyword){
    		$key = md5($keyword);
    		if(false == isset($conditions[$key])) {
	    		$condition = '(
	    						sname=' . $this->dao->quote($keyword) . '
	    					OR scode=' . $this->dao->quote($keyword) . '
	    					OR saliascode=' . $this->dao->quote($keyword) . '
	    					OR sparents1phone LIKE ' . $this->dao->quote('%' . $keyword . '%') . '
	    					OR sparents2phone LIKE ' . $this->dao->quote('%' . $keyword . '%') . '
	    					OR exam_code=' . $this->dao->quote($keyword) . '
	    					OR pos_code=' . $this->dao->quote($keyword) . '
	    					OR operator LIKE ' . $this->dao->quote('%' . $keyword . '%') . ')';
	    		$conditions[$key] = $condition;
    		}
    		$condition = $conditions[$key];
    	} else {
    		$condition = ' 1=1 ';
    	}
    	return $condition;
    }
    
    public function countCancelLog($searchArgs) {
    	static $count = null;
    	if (null === $count) {
	    	$view = $this->getCancelLogView($searchArgs['examId']);
	    	$strQuery = 'SELECT count(1) FROM (' . $view . ') logs
	    				 WHERE 1=1 ';
	    	if ($searchArgs['keyword']) {
	    		$condition = $this->getCancelCondition($searchArgs['keyword']);
	    		$strQuery .= ' AND ' . $condition;
	    	}
	    	$count = $this->dao->getOne($strQuery);
    	}
    	return $count;
    }
    
    public function cancelLogList($searchArgs, $currentPage, $pageSize) {
    	$recordCount = $this->countCancelLog($searchArgs);
    	$pageCount = ceil($recordCount / $pageSize);
    	if($pageCount < 1) $pageCount = 1;
    	if($currentPage > $pageCount) $currentPage = $pageCount;
    	if($currentPage < 1) $currentPage = 1;
    	$view = $this->getCancelLogView($searchArgs['examId']);
    	$strQuery = 'SELECT * FROM (' . $view . ') logs
    				 WHERE 1=1 ';
    	if($searchArgs['keyword']) {
    		$condition = $this->getCancelCondition($searchArgs['keyword']);
    		$strQuery .= ' AND ' . $condition;
    	}
    	$order = 'ORDER BY cancel_at DESC';
    	return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
    }
    
    public function getStuInfo($examId, $stuCode) {
    	$strQuery = 'SELECT * FROM ' . $this->stuTable . '
    				 WHERE (scode=' . $this->dao->quote($stuCode) . '
    				   OR saliascode=' . $this->dao->quote($stuCode) . ')
    				   AND bisvalid=1';
    	$stuInfo = $this->dao->getRow($strQuery);
    	if(false == $stuInfo) {
    		return array('errorMsg'=>'学员信息不存在');
    	}
    	$esInfo = $this->getEsInfo($examId, $stuInfo['scode']);
    	if($esInfo) {
    		return array('errorMsg'=>'考生报名信息已存在，请不要重复报名');
    	}
    	if(false == $stuInfo['ngrade1year']) {
    		$gradeModel = D('GradeYear');
    		$gradeYears = $gradeModel->getGradeYears();
    		$grade = 0;
    		$stuInfo['currentgrade'] = abs($stuInfo['currentgrade']);
    		foreach ($gradeYears as $year=>$gradeText) {
    			if($grade == $stuInfo['currentgrade']) {
    				$strQuery = 'UPDATE ' . $this->stuTable . '
    							 SET ngrade1year=' . abs($year) . '
    							 WHERE scode=' . $this->dao->quote($stuInfo['scode']);
    				$this->dao->execute($strQuery);
    				$stuInfo['ngrade1year'] = $year;
    				break;
    			}
    			$grade ++;
    		}
    	}
    	$examModel = D('Exam');
    	$examInfo = $examModel->find($examId);
    	if(false == $examInfo['exam_skip_grade']) {
	    	if(false == in_array($stuInfo['ngrade1year'], $examInfo['exam_grade'])) {
	    		return array('errorMsg'=>'考生年级信息不符，无法报名本次考试');
	    	}
    	} else {
    		$greenModel = D('Green');
    		if(false == $greenModel->ifExists($examId, $stuInfo['scode'])) {
    			return array('errorMsg'=>'考生不具备本次考试的报名资格');
    		}
    	}
    	
    	return $stuInfo;
    }
    
    public function signupTemp($examId, $stuCode, $posCode, $stuMobile) {
    	$dbSignupInfo = $this->getSignupInfo($examId, $stuCode);
    	if($dbSignupInfo) {
    		if($dbSignupInfo['pos_code'] == $posCode) {
    			return $dbSignupInfo;
    		} else {
    			$scoreModel = D('Score');
    			$scoreCnt = $scoreModel->countStuScore($examId, $stuCode);
    			if($scoreCnt) {
    				return $dbSignupInfo;
    			} else {
    				$strQuery = 'UPDATE ' . $this->tableName . '
    							 SET is_cancel=' . time() . '
    							 WHERE id=' . $this->dao->quote($dbSignupInfo['id']);
    				$this->dao->execute($strQuery);
    			}
    		}
    	}
    	
    	$strQuery = 'SELECT max(seat_num) seat_num FROM ' . $this->tableName . '
    				 WHERE exam_id=' . abs($examId) . '
    				   AND pos_code=' . $this->dao->quote($posCode) . '
    				   AND room_num=99';
    	$maxSeat = abs($this->dao->getOne($strQuery));
    	$nextSeat = $maxSeat + 1;
    	$id = SysUtil::uuid();
    	$posModel = D('ExamPosition');
    	$posInfo = $posModel->getPosInfo($examId, $posCode);
    	$examCode= strtoupper($posInfo['pos_code_pre']) . '99' . sprintf('%03d', $nextSeat);
    	$strQuery = 'INSERT INTO ' . $this->tableName . '
    				 (id,exam_id,pos_code,stu_code,stu_mobile,exam_code,room_num,seat_num,channel_name,
    				  signup_date,signup_time,signup_ip,is_cancel,order_status,create_at,create_user_id)
    				  VALUES (' . $this->dao->quote($id) . ', 
    				  		  ' . abs($examId) . ',
    				  		  ' . $this->dao->quote($posCode) . ',
    				  		  ' . $this->dao->quote($stuCode) . ',
    				  		  ' . $this->dao->quote($stuMobile) . ',
    				  		  ' . $this->dao->quote($examCode) . ',99,
    				  		  ' . $nextSeat . ',
    				  		  ' . $this->dao->quote('TEMP') . ',
    				  		  ' . $this->dao->quote(date('Y-m-d')) . ',
    				  		  ' . $this->dao->quote(date('Y-m-d H:i:s')) . ',
    				  		  ' . $this->dao->quote($_SERVER['REMOTE_ADDR']) . ',0,0,
    				  		  ' . abs(time()) . ',
    				  		  ' . $this->dao->quote($this->userKey) . ')';
    	$this->dao->execute($strQuery);
    	return $this->getSignupInfo($examId, $stuCode);
    }
    
    public function getSignupInfo($examId, $stuCode) {
    	$strQuery = 'SELECT * FROM ' . $this->tableName . '
    				 WHERE exam_id=' . abs($examId) . '
    				   AND stu_code=' . $this->dao->quote($stuCode) . '
    				   AND is_cancel=0
    				   AND order_status!=1';
    	$signupInfo = $this->dao->getRow($strQuery);
    	return $signupInfo;
    }
    
    public function signup($examId, $stuCode, $posCode, $stuMobile, $channel='前台报名', $operator='') {
    	if($operator) {
    		$this->userKey = 'GS-' . $operator;
    		$examModel = D('Exam');
	    	$examInfo = $examModel->find($examId);
	    	if($examInfo['exam_skip_grade']) {
	    		return array('errorMsg'=>'收费考试禁止前台报名');
	    	}
    	}
    	
    	$posModel = D('ExamPosition');
    	$freePosArray = $posModel->getFreePosList($examId);
    	if(false == isset($freePosArray[$posCode])) {
    		return array('errorMsg'=>'选定考点已满，请选择其他考点');
    	}
    	$esInfo = $this->signupCancel($examId, $stuCode, $posCode, $stuMobile, $channel, $operator);
    	if($esInfo) {
    		return $esInfo;
    	}
    	$posInfo = $posModel->getPosInfo($examId, $posCode);
    	$strQuery = 'SELECT room_num,count(1) room_cnt 
    				 FROM ' . $this->tableName . '
    				 WHERE exam_id=' . abs($examId) . '
    				   AND pos_code=' . $this->dao->quote($posCode) . '
    				 GROUP BY room_num';
    	$cntList = $this->dao->getAll($strQuery);
    	$cntArray = array();
    	foreach ($cntList as $row) {
    		$cntArray[$row['room_num']] = $row['room_cnt'];
    	}
    	
    	$roomNumSetting = $posInfo['room_num_setting'];
    	$roomNumSetting = str_replace(array("'", "[", "]"), array('', '', ''), $roomNumSetting);
    	$roomNumbers = explode(',', $roomNumSetting);
    	foreach ($roomNumbers as $key=>$count) {
    		$roomNum = abs($key) + 1;
    		if($cntArray[$roomNum] < $count) {
    			$seatNum = $cntArray[$roomNum]+1;
    			$examCode = strtoupper($posInfo['pos_code_pre'] . sprintf('%02d', $roomNum) . sprintf('%03d', $seatNum));
    			$uid = SysUtil::uuid();
    			$strQuery = 'INSERT INTO ' . $this->tableName . '
    						 (id,exam_id,pos_code,stu_code,stu_mobile,exam_code,room_num,seat_num,channel_name,
    						  signup_date,signup_time,signup_ip,is_cancel,order_status,create_at,create_user_id,update_at,update_user_id)
    						  VALUES (' . $this->dao->quote($uid) . ', 
    						  		  ' . abs($examId) . ', 
    						  		  ' . $this->dao->quote($posCode) . ',
    						  		  ' . $this->dao->quote($stuCode) . ',
    						  		  ' . $this->dao->quote($stuMobile) . ',
    						  		  ' . $this->dao->quote($examCode) . ',
    						  		  ' . abs($roomNum) . ',
    						  		  ' . abs($seatNum) . ',
    						  		  ' . $this->dao->quote($channel) . ',
    						  		  ' . $this->dao->quote(date('Y-m-d')) . ',
    						  		  ' . $this->dao->quote(date('Y-m-d H:i:s')) . ',
    						  		  ' . $this->dao->quote($_SERVER['REMOTE_ADDR']) . ',0,0,
    						  		  ' . time() . ',
    						  		  ' . $this->dao->quote($this->userKey) . ',
    						  		  ' . time() . ',
    						  		  ' . $this->dao->quote($this->userKey) . ')';
    			$this->dao->execute($strQuery);
    			$esInfo = $this->getEsInfo($examId, $stuCode);
    			if($esInfo) {
    				$strQuery = 'SELECT * FROM ' . $this->greenTable . '
    							 WHERE exam_id=' . $examId . '
    							   AND stu_code=' . $this->dao->quote($stuCode);
    				$greenInfo = $this->dao->getRow($strQuery);
    				if($greenInfo['exam_code']) {
    					$strQuery = 'UPDATE ' . $this->tableName . '
    								 SET real_exam_code=' . $this->dao->quote($greenInfo['exam_code']) . '
    								 WHERE id=' . $this->dao->quote($esInfo['id']);
    					$this->dao->execute($strQuery);
    				}
    				return $esInfo;
    			}
    		}
    	}
    	return array('errorMsg'=>'考点已满,请选择其他考点');
    }
    
    private function signupCancel($examId, $stuCode, $posCode, $stuMobile, $channel='前台报名', $operator='') {
    	$strQuery = 'SELECT * FROM ' . $this->tableName . '
    				 WHERE exam_id=' . abs($examId) . '
    				   AND pos_code=' . $this->dao->quote($posCode) . '
    				   AND is_cancel !=0
    				 ORDER BY exam_code';
    	$cancelList = $this->dao->getAll($strQuery);
    	if($cancelList) {
    		foreach ($cancelList as $row) {
    			$strQuery = 'UPDATE ' . $this->tableName . '
    						 SET stu_code=' . $this->dao->quote($stuCode) . ',
    						     is_cancel=0,
    						     stu_mobile=' . $this->dao->quote($stuMobile) . ',
    						     signup_date=' . $this->dao->quote(date('Y-m-d')) . ',
    						     signup_time=' . $this->dao->quote(date('Y-m-d H:i:s')) . ',
    						     channel_name=' . $this->dao->quote($channel) . ',
    						     order_status=0,
    						     create_at=' . time() . ',
    						     create_user_id=' . $this->dao->quote($this->userKey) . ',
    						     update_at=' . time() . ',
    						     update_user_id=' . $this->dao->quote($this->userKey) . '
    						  WHERE id=' . $this->dao->quote($row['id']) . '
    						    AND is_cancel !=0';
    			
    			$this->dao->execute($strQuery);
    			$esInfo = $this->getEsInfo($examId, $stuCode);
    			if($esInfo) {
    				$strQuery = 'SELECT * FROM ' . $this->greenTable . '
    							 WHERE exam_id=' . $examId . '
    							   AND stu_code=' . $this->dao->quote($stuCode);
    				$greenInfo = $this->dao->getRow($strQuery);
    				if($greenInfo['exam_code']) {
    					$strQuery = 'UPDATE ' . $this->tableName . '
    								 SET real_exam_code=' . $this->dao->quote($greenInfo['exam_code']) . '
    								 WHERE id=' . $this->dao->quote($esInfo['id']);
    					$this->dao->execute($strQuery);
    				}
    				return $esInfo;
    			}
    		}
    	}
    	return false;
    }
    
    public function sendSms($esInfo) {
    	import('COM.MsgSender.SmsSender');
    	$examId = $esInfo['exam_id'];
    	$examModel = D('Exam');
    	$examInfo = $examModel->find($examId);
    	$stuModel = D('Student');
    	$stuInfo = $stuModel->getStuInfo($esInfo['stu_code']);
    	$stuPwd = $stuModel->getStuPasswd($stuInfo);
		$smsContents = '家长您好，您已成功报名竞赛“' . $examInfo['group_caption'] . '[' . $examInfo['exam_caption'] . ']' . "”,";
		$smsContents .= '准考证号为“' . $esInfo['exam_code'] . "”,考试时间：“" . $examInfo['exam_time_area'] . "”,请准时参加!";
    	if($stuPwd) {
    		$smsContents .= '竞赛结束后可通过学员身份登陆高思学员系统查询成绩信息，高思学号为：' . $stuInfo['saliascode'] . ',登陆密码为：' . $stuPwd;
    	}
    	
    	SmsSender::sendSms($esInfo['stu_mobile'], $smsContents);
    }
    
    public function getRoomCountArray($examId, $posCode) {
    	$strQuery = 'SELECT room_num, count(1) cnt 
    				 FROM ' . $this->tableName . '
    				 WHERE exam_id=' . abs($examId) . '
    				   AND pos_code=' . $this->dao->quote($posCode) . '
    				   AND is_cancel=0
    				   AND order_status !=1
    				 GROUP BY room_num
    				 ORDER BY room_num';
    	return $this->dao->getAll($strQuery);
    }
    
    public function getSignupCountArray($groupId) {
    	$examModel = D('Exam');
    	$strQuery = 'SELECT exam_id FROM ' . $this->examTable . '
				   	 WHERE exam_status=1 
				   	   AND is_remove=0 
				   	   AND group_id=' . abs($groupId) . '
				   	 ORDER BY exam_id';
    	$examList = $this->dao->getAll($strQuery);
    	$examArray = array();
    	$examIds = array(0);
    	foreach ($examList as $exam) {
    		$examArray[$exam['exam_id']] = $exam;
    		$examIds[] = $exam['exam_id'];
    	}
    	$idList = implode(',', $examIds);
    	
    	$strQuery = 'SELECT DISTINCT ep.pos_code,p.pos_caption 
    				 FROM ' . $this->posTable . ' p,
    				 ' . $this->examPosTable . ' ep
    				 WHERE ep.pos_code=p.pos_code 
    				   AND ep.is_deleted=' . $this->dao->quote('') . '
    				   AND ep.exam_id in (' . $idList . ')
    				   ORDER BY pos_code';
    	$posList = $this->dao->getAll($strQuery);
    	$posArray = array();
    	foreach ($posList as $pos) {
    		$posArray[$pos['pos_code']] = $pos;
    	}
    	foreach ($posArray as $posCode=>$pos) {
    		foreach ($examArray as $examId=>$exam) {
    			$posArray[$posCode]['exam_' . $examId] = 0;
    		}
    	}
    	
    	$strQuery = 'SELECT exam_id,pos_code,count(1) cnt
    				 FROM ' . $this->tableName . '
    				 WHERE is_cancel=0 
    				   AND order_status !=1 
    				   AND exam_id IN (' . $idList . ')
    				 GROUP BY exam_id,pos_code
    				 ORDER BY exam_id';
    	$rows = $this->dao->getAll($strQuery);
    	foreach ($rows as $row) {
    		$posArray[$row['pos_code']]['exam_' . $row['exam_id']] = $row['cnt'];
    	}

    	return $posArray;
    }
}
?>