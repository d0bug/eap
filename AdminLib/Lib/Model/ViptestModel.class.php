<?php

class ViptestModel extends Model {
	public  $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao();
		$this->vip_test_users = 'vip_test_users';
		$this->vip_test_module = 'vip_test_module';
		$this->vip_test_paper = 'vip_test_paper';
		$this->vip_test_question = 'vip_test_question';
		$this->vip_test_level = 'vip_test_level';
		$this->vip_test_result = 'vip_test_result';
		$this->vip_test_record = 'vip_test_record';
	}


	public function get_userList($condition='', $currentPage=1, $pageSize=20){
		$count = $this->get_userCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT * FROM '.$this->vip_test_users.' WHERE 1 = 1 ';
		if(!empty($condition)){
			$strQuery .= ' AND '.$condition;
		}
		$order = ' ORDER BY instime DESC';
		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}


	public function get_userCount($condition=''){
		$strQuery = 'SELECT count(1) FROM '.$this->vip_test_users.' WHERE 1=1 ';
		if ($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		return $this->dao->getOne($strQuery);
	}

	public function get_userListAll($conditionArr){
		$strQuery = 'SELECT id,name,phone,instime FROM '.$this->vip_test_users.' WHERE 1=1 ';
		if ($conditionArr['keyword']) {
			$strQuery .= ' AND `name` like ' . $this->dao->quote('%'.SysUtil::safeString(urldecode($conditionArr['keyword'])).'%');
		}
		return $this->dao->getAll($strQuery);
	}

	public function get_paperList($arr){
		$strQuery = 'SELECT * FROM '.$this->vip_test_paper.' WHERE 1=1 ';
		if(!empty($arr['status'])){
			$strQuery .= ' AND status = '.$this->dao->quote($arr['status']);
		}
		return $this->dao->getAll($strQuery);
	}

	public function get_paperInfo($paper_id){
		$info = array();
		if(!empty($paper_id)){
			$info = $this->dao->getRow('SELECT * FROM '.$this->vip_test_paper.' WHERE id = '.$this->dao->quote($paper_id));
		}
		return $info;
	}

	public function editPaper($arr){
		$this->dao->execute('UPDATE '.$this->vip_test_paper.' SET title='.$this->dao->quote(SysUtil::safeString($arr['title'])).',question_num='.$this->dao->quote(abs($arr['question_num'])).',level_str='.$this->dao->quote($arr['level_str']).',is_accuracy='.$this->dao->quote(abs($arr['is_accuracy'])).',status = '.$this->dao->quote(abs($arr['status'])).' WHERE id= '.$this->dao->quote(abs($arr['id'])));
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}


	public function addPaper($arr){
		$this->dao->execute('INSERT INTO '.$this->vip_test_paper.' (title,question_num,level_str,is_accuracy,status) VALUES('.$this->dao->quote(SysUtil::safeString($arr['title'])).','.$this->dao->quote(abs($arr['question_num'])).','.$this->dao->quote($arr['level_str']).','.$this->dao->quote(abs($arr['is_accuracy'])).','.$this->dao->quote(abs($arr['status'])).')');
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}


	public function get_moduleList($arr){
		$strQuery = 'SELECT * FROM '.$this->vip_test_module.' WHERE 1=1 ';
		if(!empty($arr['paper_id'])){
			$strQuery .= ' AND paper_id = '.$this->dao->quote($arr['paper_id']);
		}
		return $this->dao->getAll($strQuery);
	}


	public function get_moduleInfo($module_id){
		$info = array();
		if(!empty($module_id)){
			$info = $this->dao->getRow('SELECT * FROM '.$this->vip_test_module.' WHERE id = '.$this->dao->quote($module_id));
		}
		return $info;
	}


	public function editModule($arr){
		$this->dao->execute('UPDATE '.$this->vip_test_module.' SET `name`='.$this->dao->quote(SysUtil::safeString($arr['name'])).',excellent_strong='.$this->dao->quote(abs($arr['excellent_strong'])).',excellent_weak='.$this->dao->quote(abs($arr['excellent_weak'])).',accuracy='.$this->dao->quote(abs($arr['accuracy'])).' WHERE id= '.$this->dao->quote(abs($arr['id'])).' AND paper_id = '.$this->dao->quote(abs($arr['paper_id'])));
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}


	public function addModule($arr){
		$this->dao->execute('INSERT INTO '.$this->vip_test_module.' (`name`,excellent_strong,excellent_weak,accuracy,paper_id) VALUES('.$this->dao->quote(SysUtil::safeString($arr['name'])).','.$this->dao->quote(abs($arr['excellent_strong'])).','.$this->dao->quote(abs($arr['excellent_weak'])).','.$this->dao->quote(abs($arr['accuracy'])).','.$this->dao->quote(abs($arr['paper_id'])).')');
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}



	public function get_questionList($arr, $currentPage=1, $pageSize=20){
		$count = $this->get_questionCount($arr);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT q.*,p.title as paper_title,m.name as module_name FROM '.$this->vip_test_question.' AS q LEFT JOIN '.$this->vip_test_paper.' AS p ON p.id=q.paper_id LEFT JOIN '.$this->vip_test_module.' AS m ON m.id = q.module_id WHERE 1 = 1 ';
		if(!empty($arr['paper_id'])){
			$strQuery .= ' AND q.paper_id = '.$this->dao->quote($arr['paper_id']);
		}
		if(!empty($arr['module_id'])){
			$strQuery .= ' AND q.module_id = '.$this->dao->quote($arr['module_id']);
		}
		$order = ' ORDER BY paper_id ASC,seq ASC';
		$list = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		if(!empty($list)){
			foreach ($list as $key=>$row){
				$list[$key]['show_url'] = str_replace('/Upload/','/upload/',$row['img']);
				list($list[$key]['img_width'],$list[$key]['img_height']) = getimagesize(APP_DIR.$row['img']);
				$answerArr = C('ANSWER');
				$list[$key]['answer'] = $answerArr[$row['answer']];
			}
		}
		return $list;
	}


	public function get_questionCount($arr){
		$strQuery = 'SELECT count(1) FROM '.$this->vip_test_question.' WHERE 1 = 1';
		if(!empty($arr['paper_id'])){
			$strQuery .= ' AND paper_id = '.$this->dao->quote($arr['paper_id']);
		}
		if(!empty($arr['module_id'])){
			$strQuery .= ' AND module_id = '.$this->dao->quote($arr['module_id']);
		}
		return $this->dao->getOne($strQuery);
	}



	public function get_questionInfo($question_id){
		$row = array();
		if(!empty($question_id)){
			$row = $this->dao->getRow('SELECT * FROM '.$this->vip_test_question.' WHERE id = '.$this->dao->quote($question_id));
			$row['show_url'] = str_replace('/Upload/','/upload/',$row['img']);
		}
		return $row;
	}


	public function addQuestion($arr){
		$this->dao->execute('INSERT INTO '.$this->vip_test_question.' (img,module_id,paper_id,option_num,answer,time_limit,accuracy,seq,instime) VALUES('.$this->dao->quote($arr['img']).','.$this->dao->quote(abs($arr['module_id'])).','.$this->dao->quote(abs($arr['paper_id'])).','.$this->dao->quote(abs($arr['option_num'])).','.$this->dao->quote($arr['answer']).','.$this->dao->quote(abs($arr['time_limit'])).','.$this->dao->quote(abs($arr['accuracy'])).','.$this->dao->quote(abs($arr['seq'])).','.$this->dao->quote(date('Y-m-d H:i:s')).')');
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}


	

	public function editQuestion($arr){
		$this->dao->execute('UPDATE '.$this->vip_test_question.' SET img = '.$this->dao->quote($arr['img']).',module_id = '.$this->dao->quote(abs($arr['module_id'])).',paper_id = '.$this->dao->quote(abs($arr['paper_id'])).',option_num = '.$this->dao->quote(abs($arr['option_num'])).',answer = '.$this->dao->quote($arr['answer']).',time_limit = '.$this->dao->quote(abs($arr['time_limit'])).',accuracy = '.$this->dao->quote(abs($arr['accuracy'])).',seq = '.$this->dao->quote(abs($arr['seq'])).',instime = '.$this->dao->quote(date('Y-m-d H:i:s')).' WHERE id = '.$this->dao->quote(abs($arr['id'])));
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}


	public function get_resultList($conditionArr, $currentPage=1, $pageSize=20){
		$count = $this->get_resultCount($conditionArr);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT r.*,u.name as uname,p.title,p.question_num,p.level_str,p.question_num  FROM '.$this->vip_test_result.' AS r
																		LEFT JOIN '.$this->vip_test_users.' AS u ON r.uid = u.id 
																		LEFT JOIN '.$this->vip_test_paper.' AS p ON r.paper_id = p.id 
																		WHERE 1 = 1 ';
		if (!empty($conditionArr['paper_id'])) {
			$strQuery .= ' AND r.paper_id = ' .$this->dao->quote($conditionArr['paper_id']);
		}
		$order = ' ORDER BY accuracy_count DESC';
		$list = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		$list = $this->get_resultLevelRank($list);
		return $list;
	}


	public function get_resultCount($conditionArr){
		$strQuery = 'SELECT count(1) FROM '.$this->vip_test_result.' WHERE 1 = 1';
		if (!empty($conditionArr['paper_id'])) {
			$strQuery .= ' AND paper_id = ' .$this->dao->quote($conditionArr['paper_id']);
		}
		return $this->dao->getOne($strQuery);
	}


	public function get_resultListAll($conditionArr){
		$strQuery = 'SELECT r.*,u.name as uname,p.question_num,p.level_str,p.question_num FROM '.$this->vip_test_result.' AS r
												LEFT JOIN '.$this->vip_test_users.' AS u ON r.uid = u.id 
												LEFT JOIN '.$this->vip_test_paper.' AS p ON r.paper_id = p.id 
												WHERE 1 = 1 ';
		if (!empty($conditionArr['paper_id'])) {
			$strQuery .= ' AND r.paper_id = ' .$this->dao->quote($conditionArr['paper_id']);
		}
		$strQuery .= ' ORDER BY accuracy_count DESC ';
		$list = $this->dao->getAll($strQuery);
		$list = $this->get_resultLevelRank($list);
		return $list;
	}


	public function get_resultInfo($result_id){
		$list = $this->dao->getAll('SELECT r.*,u.name as uname,p.title,p.level_str,p.question_num FROM '.$this->vip_test_result.' AS r
															  LEFT JOIN '.$this->vip_test_users.' AS u ON r.uid = u.id 
															  LEFT JOIN '.$this->vip_test_paper.' AS p ON r.paper_id = p.id 
															  WHERE r.id = '.$this->dao->quote(abs($result_id)));
		$list = $this->get_resultLevelRank($list);
		return $list[0];
	}


	public function get_resultLevelRank($arr){
		if(!empty($arr)){
			foreach ($arr as $key=>$row){
				$arr[$key]['rank'] = $key+1;
				$arr[$key]['level_arr'] = unserialize($row['level_str']);
				$arr[$key]['level_count'] = count($arr[$key]['level_arr']);
				if(!empty($arr[$key]['level_arr'])){
					foreach ($arr[$key]['level_arr'] as $kk=>$level){
						if($row['accuracy_count'] >= $level['low']){
							$arr[$key]['level'] = $level['name'];
							$arr[$key]['level_desc'] = $level['desc'];
							break;
						}
					}
				}
			}
		}
		return $arr;
	}

	public function get_recordList($arr){
		return $this->dao->getAll('SELECT * FROM '.$this->vip_test_record.' WHERE 1 = 1 AND question_id = ' .$this->dao->quote($arr['question_id']));
	}


	public function get_resultAccuracyAvg($conditionArr){
		$strQuery = 'SELECT AVG(accuracy_count) as accuracy_avg FROM '.$this->vip_test_result.' WHERE status = 1 ';
		if (!empty($conditionArr['paper_id'])) {
			$strQuery .= ' AND paper_id = ' .$this->dao->quote($conditionArr['paper_id']);
		}
		return $this->dao->getOne($strQuery);
	}


	public function get_moduleQuestion($paper_id){
		return $this->dao->getAll('select count(*) as question_num,module_id from '.$this->vip_test_question.' where paper_id = '.$this->dao->quote($paper_id).' group by module_id');
	}


	public function get_moduleRecord($arr){
		return $this->dao->getAll('select count(*) as num,r.is_correct from '.$this->vip_test_record.' as r left join  '.$this->vip_test_question.' as q on q.id = r.question_id where r.paper_id = '.$this->dao->quote($arr['paper_id']).' and q.module_id ='.$this->dao->quote($arr['module_id']).' group by r.is_correct');
	}


	public function deletePaper($paper_id){
		$this->dao->execute('begin');
		$success1 = (boolean)$this->dao->execute('DELETE FROM '.$this->vip_test_paper.' WHERE id = '.$this->dao->quote(abs($paper_id)));
		$success2 = (boolean)$this->dao->execute('DELETE FROM '.$this->vip_test_module.' WHERE paper_id = '.$this->dao->quote(abs($paper_id)));
		$success3 = (boolean)$this->dao->execute('DELETE FROM '.$this->vip_test_question.' WHERE paper_id = '.$this->dao->quote(abs($paper_id)));
		$success4 = (boolean)$this->dao->execute('DELETE FROM '.$this->vip_test_record.' WHERE paper_id = '.$this->dao->quote(abs($paper_id)));
		$success5 = (boolean)$this->dao->execute('DELETE FROM '.$this->vip_test_result.' WHERE paper_id = '.$this->dao->quote(abs($paper_id)));
		if($success1 == false || $success2 == false || $success3 == false || $success4 == false || $success5 == false){
			$this->dao->execute(' rollback');
			return false;
		}
		$this->dao->execute(' commit');
		return true;
	}


	public function deleteModule($module_id){
		$this->dao->execute('begin');
		$success1 = (boolean)$this->dao->execute('DELETE FROM '.$this->vip_test_module.' WHERE id = '.$this->dao->quote(abs($module_id)));
		$success2 = (boolean)$this->dao->execute('DELETE FROM '.$this->vip_test_question.' WHERE module_id = '.$this->dao->quote(abs($module_id)));
		if($success1 == false || $success2 == false){
			$this->dao->execute(' rollback');
			return false;
		}
		$this->dao->execute(' commit');
		return true;
	}


	public function deleteQuestion($question_id){
		$this->dao->execute('begin');
		$success1 = (boolean)$this->dao->execute('DELETE FROM '.$this->vip_test_question.' WHERE id = '.$this->dao->quote(abs($question_id)));
		$success2 = (boolean)$this->dao->execute('DELETE FROM '.$this->vip_test_record.' WHERE question_id = '.$this->dao->quote(abs($question_id)));
		if($success1 == false || $success2 == false){
			$this->dao->execute(' rollback');
			return false;
		}
		$this->dao->execute(' commit');
		return true;
	}


	public function get_moduleData($result_id){
		$resultInfo = $this->get_resultInfo($result_id);
		$moduleData = $this->get_moduleList(array('paper_id'=>$resultInfo['paper_id']));
		$moduleNameStr = "";
		$moduleAccuracyAvg = '';
		$moduleAccuracySelf = '';
		if(!empty($moduleData)){
			foreach ($moduleData as $key=>$module){
				$moduleData[$key]['question_num'] = $this->dao->getOne('SELECT count(*) as question_num FROM '.$this->vip_test_question.' WHERE module_id = '.$this->dao->quote($module['id']).' AND paper_id ='.$this->dao->quote($resultInfo['paper_id']));
				$moduleNameStr .= "'$module[name]',";
				$moduleData[$key]['correct_total'] = 0;
				$moduleData[$key]['error_total'] = 0;
				$moduleData[$key]['myself_correct_total'] = 0;
				$moduleData[$key]['myself_error_total'] = 0;
				$recordList = $this->dao->getAll('SELECT r.id,
														 r.uid,
														 r.question_id,
														 r.paper_id,
														 r.answer,
														 r.is_correct,
														 r.result_id,
														 q.img,
														 q.seq,
														 q.answer as correct_answer  
														 FROM '.$this->vip_test_record.' AS r 
														 LEFT JOIN '.$this->vip_test_question.' AS q ON q.id = r.question_id 
														 WHERE q.module_id = '.$this->dao->quote($module['id']).' AND r.paper_id = '.$this->dao->quote($resultInfo['paper_id']).' ORDER BY q.seq ASC');
				if(!empty($recordList)){
					foreach ($recordList as $kk=>$record){
						if($record['is_correct']==1){
							$moduleData[$key]['correct_total']++;
							if($resultInfo['uid'] == $record['uid']){
								$moduleData[$key]['myself_correct_total']++;
								$moduleData[$key]['myRecordList'][] = $recordList[$kk];
							}
						}else if($record['is_correct']==0){
							$moduleData[$key]['error_total']++;
							if($resultInfo['uid'] == $record['uid']){
								$moduleData[$key]['myself_error_total']++;
								$moduleData[$key]['myRecordList'][] = $recordList[$kk];
							}
						}
					}
				}
				$moduleData[$key]['correct_avg'] = ceil($moduleData[$key]['correct_total']/count($recordList));
				$moduleData[$key]['accuracy_avg'] = sprintf('%.2f',($moduleData[$key]['correct_total']/count($recordList))*100);
				$moduleData[$key]['accuracy_self'] = sprintf('%.2f',($moduleData[$key]['myself_correct_total']/($moduleData[$key]['myself_error_total']+$moduleData[$key]['myself_correct_total']))*100);
				$moduleAccuracyAvg .= $moduleData[$key]['accuracy_avg'].',';
				$moduleAccuracySelf .= $moduleData[$key]['accuracy_self'].',';
				if($moduleData[$key]['accuracy_self']>=$module['excellent_strong']){
					$moduleData[$key]['excellent_status'] = '较强，需要继续保持。';
				}else if($moduleData[$key]['accuracy_self']<$module['excellent_weak']){
					$moduleData[$key]['excellent_status'] = '较弱，需要重点加强。';
				}else{
					$moduleData[$key]['excellent_status'] = '中等，需要进一步提高。';
				}
			}

			foreach ($moduleData as $key=>$module){
				if(!empty($module['myRecordList'])){
					foreach ($module['myRecordList'] as $kk=>$record){
						$moduleData[$key]['myRecordList'][$kk]['img_url'] = str_replace('/Upload/','/upload/',$record['img']);
						$moduleData[$key]['myRecordList'][$kk]['correct_total'] = 0;
						$questionRecord = $this->dao->getAll('SELECT is_correct FROM '.$this->vip_test_record.' WHERE question_id = '.$this->dao->quote($record['question_id']).' AND paper_id='.$this->dao->quote($record['paper_id']));
						$moduleData[$key]['myRecordList'][$kk]['questionRecord'] = $questionRecord;
						$moduleData[$key]['myRecordList'][$kk]['total'] = count($questionRecord);
						if(!empty($questionRecord)){
							foreach ($questionRecord as $k=>$answer){
								if($answer['is_correct'] == 1){
									$moduleData[$key]['myRecordList'][$kk]['correct_total']++;
								}
							}
						}
						$moduleData[$key]['myRecordList'][$kk]['accuracy_avg'] = sprintf('%.0f',($moduleData[$key]['myRecordList'][$kk]['correct_total']/$moduleData[$key]['myRecordList'][$kk]['total'])*100);
					}
				}
			}
		}
		return array('data'=>$moduleData,'nameStr'=>$moduleNameStr,'AccuracyAvgStr'=>$moduleAccuracyAvg,'AccuracySelfStr'=>$moduleAccuracySelf);
	}
}
?>