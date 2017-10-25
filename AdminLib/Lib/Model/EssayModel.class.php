<?php

class EssayModel extends Model {
	public  $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao('MYSQL_CONN');
		$this->essay_type = 'essay_type';
		$this->essay_theme = 'essay_theme';
		$this->essay_list = 'essay_list';
		$this->essay_excellent = 'essay_excellent';
		$this->essay_extra_student = 'essay_extra_student';
		$this->essay_extra_student_relationship = 'essay_extra_student_relationship';
		$this->essay_avatar = 'essay_avatar';
		$this->dao2 = Dao::getDao('MSSQL_CONN');
		$this->bs_teacher = 'BS_Teacher';
		$this->bs_lesson = 'BS_Lesson';
		$this->bs_area = 'BS_Area';
		$this->view_class = 'view_Class';
		$this->bs_classType = 'BS_ClassType';
		$this->d_xueke = 'D_XueKe';
		$this->bs_roster = 'viewBS_Roster';
		$this->bs_student = 'BS_Student';
	}

	public function get_essayList($condition='', $currentPage=1, $pageSize=20){
		$count = $this->get_essayCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT class_code,max(class_name) as class_name,count(speaker_number) as count_num FROM ' . $this->essay_list . ' WHERE 1=1 ';
		if($condition) {
			$strQuery .= $condition;
		}
		$strQuery .= ' group by class_code ';
		$order = ' ORDER BY instime DESC';
		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}

	public function get_essayCount($condition='') {
		$strQuery = 'SELECT class_code FROM ' . $this->essay_list . ' WHERE 1=1 ';
		if ($condition) {
			$strQuery .=  $condition;
		}
		$strQuery .= ' group by class_code ';
		$arr = $this->dao->getAll($strQuery);
		return count($arr);
	}

	public function get_essayInfo($arr){
		$strQuery = 'SELECT * FROM '.$this->essay_list.' WHERE 1=1 ';
		if(!empty($arr['id'])){
			$strQuery .= ' AND `id` = '.$this->dao->quote(abs($arr['id']));
		}
		if(!empty($arr['class_code'])){
			$strQuery .= ' AND `class_code` = '.$this->dao->quote($arr['class_code']);
		}
		if(!empty($arr['speaker_number'])){
			$strQuery .= ' AND `speaker_number` = '.$this->dao->quote($arr['speaker_number']);
		}
		if(!empty($arr['student_code'])){
			$strQuery .= ' AND `student_code` = '.$this->dao->quote($arr['student_code']);
		}
		if(!empty($arr['student_name'])){
			$strQuery .= ' AND `student_name` = '.$this->dao->quote($arr['student_name']);
		}
		if($arr['key_name']=='instime' && !empty($arr['order'])){
			$strQuery .= ' order by '.$arr['key_name'].' '.$arr['order'];
		}
		return $this->dao->getRow($strQuery);
	}


	public function get_essayTypeList($pid=0){
		return $this->dao->getAll("SELECT `id`,`name`,`pid`,`top_id`,`deep` FROM ".$this->essay_type." WHERE `pid` = '$pid'");
	}

	public function get_essayTypeId_by_name($name,$pid =0){
		return $this->dao->getOne("SELECT `id` FROM ".$this->essay_type." WHERE `name` = '$name' AND `pid` = '$pid'");
	}

	public function get_essayThemlList(){
		return $this->dao->getAll("SELECT `id`,`name` FROM ".$this->essay_theme);
	}

	public function add_essay($arr){
		if(!empty($arr['class_name']) && !empty($arr['class_code']) && !empty($arr['campus_name']) &&!empty($arr['teacher_name']) &&!empty($arr['speaker_number']) &&!empty($arr['essay_imgs']) && !empty($arr['student_name'])){
			if(!empty($arr['id'])){
				$strQuery = "UPDATE ".$this->essay_list."
								SET `class_name` = '$arr[class_name]',
									`class_code`='$arr[class_code]',
									`campus_name`='$arr[campus_name]',
									`teacher_name`='$arr[teacher_name]',
									`speaker_number`='$arr[speaker_number]',
									`essay_imgs`= '$arr[essay_imgs]',
									`dtbegindate`='$arr[dtbegindate]',
									`dtenddate`='$arr[dtenddate]',
									`sprinttime`='$arr[sprinttime]',
									`student_name`='".SysUtil::safeString($arr['student_name'])."',
									`student_code`='$arr[student_code]' WHERE `id` = '$arr[id]' ";
			}else{
				$strQuery = "INSERT INTO ".$this->essay_list."
								(`class_name`,`class_code`,`campus_name`,`teacher_name`,`speaker_number`,`essay_imgs`,`instime`,`create_user`,`dtbegindate`,`dtenddate`,`sprinttime`,`student_name`,`student_code`) 
							VALUES('$arr[class_name]','$arr[class_code]','$arr[campus_name]','$arr[teacher_name]','$arr[speaker_number]','$arr[essay_imgs]','$arr[instime]','$arr[create_user]','$arr[dtbegindate]','$arr[dtenddate]','$arr[sprinttime]','".SysUtil::safeString($arr['student_name'])."','$arr[student_code]')";
			}
			$this->dao->execute($strQuery);
			if($this->dao->affectRows()){
				$new_insert_id = $this->dao->getOne("SELECT id FROM ".$this->essay_list." ORDER BY id DESC LIMIT 1 ");
				return $new_insert_id;
			}
			return false;
		}
		return false;
	}

	public function update_essayImgs($essayId,$new_essayImgs){
		if(!empty($essayId)){
			$this->dao->execute('UPDATE '.$this->essay_list.' SET `essay_imgs` = '.$this->dao->quote($new_essayImgs).' WHERE id = '.$this->dao->quote($essayId));
			if($this->dao->affectRows()){
				return true;
			}
			return false;
		}
		return false;
	}

	public function editEssayAttribute($arr){
		if(!empty($arr['essayId']) && !empty($arr['typeOne']) && !empty($arr['typeTwo'])){
			$this->dao->execute("UPDATE ".$this->essay_list." SET `essay_length`='$arr[essayLength]', `type_one` = '$arr[typeOne]',`type_two` = '$arr[typeTwo]',`type_three` = '$arr[typeThree]',`type_four` = '$arr[typeFour]',`theme_name` = '$arr[themeName]' WHERE id = '$arr[essayId]'");
			if($this->dao->affectRows()){
				return true;
			}
			return false;
		}
		return false;
	}

	/*获取班级讲次*/
	public function get_lessonList($class_code,$condition){
		return $this->dao2->getAll('SELECT les.[nLessonNo] FROM '.$this->bs_lesson.' AS les WHERE les.[sClassCode] = '.$this->dao->quote($class_code).$condition.' order by les.[nLessonNo] ASC');
	}


	/*获取当前用户的班级信息列表$dtDate筛选课程日期*/
	public function get_classList($userInfo,$islimit=0,$dtDate=''){
		$xueKeId = $this->dao2->getOne('SELECT [id] FROM '.$this->d_xueke.' WHERE [sName]='.$this->dao->quote('语文'));//获取语文学科id
		$classTypeCodeArr = $this->dao2->getAll('SELECT [sCode] FROM '.$this->bs_classType.' WHERE [nXueKe] = '.$this->dao->quote($xueKeId));//获取语文学科的所有类型编码
		$classTypeCodeStr = '';
		if(!empty($classTypeCodeArr)){
			foreach ($classTypeCodeArr as $key=>$classTypeCode){
				$classTypeCodeStr .= $classTypeCode['scode'].',';
			}
			$classTypeCodeStr = "'".implode("','",explode(',',trim($classTypeCodeStr,',')))."'";
		}
		$strQuery = 'SELECT les.[sClassCode] as s_class_code ,count(les.[nLessonNo]) as lesson_num ,max(les.[sTeacherCode]) as s_teacher_code,max(t.[sName]) as s_teacher_name,max(v.[sClassName]) as s_class_name,max(v.[sAreaCode]) as s_area_code,max(a.sName) as s_area_name,max(v.[dtBeginDate]) as dtbegindate,max(v.[dtEndDate]) as dtenddate,max(v.[sPrintTime]) as sprinttime
					FROM '.$this->bs_lesson.' as les  
					LEFT JOIN '.$this->view_class.' AS v ON les.[sClassCode] = v.[sClassCode] 
					LEFT JOIN '.$this->bs_area.' as a ON a.sCode = v.sAreaCode 
					LEFT JOIN '.$this->bs_teacher.' AS t ON les.sTeacherCode = t.sCode 
					WHERE v.[sClassTypeCode] IN ('.$classTypeCodeStr.') ';
		if($islimit==1){
			$dateLimit = C('DATALIMIT');
			$strQuery .= ' AND v.[dtBeginDate] >='.$this->dao->quote(date('Y-m-d',time()-$dateLimit*24*3600).' 00:00:00');
		}
		if($userInfo['nkind'] == 1){
			$condition = ' AND (case when les.[sAssistRealTeacherCode]<>'.$this->dao->quote().') then les.[sAssistRealTeacherCode] else les.[sAssistTeacherCode] end ) = '.$this->dao->quote($userInfo['scode']);
		}else{
			$condition = ' AND (case when les.[sRealTeacherCode]<>'.$this->dao->quote().' then les.[sRealTeacherCode] else les.[sTeacherCode]  end )= '.$this->dao->quote($userInfo['scode']);
		}
		if(!empty($dtDate)){
			$condition .= ' AND les.dtDate = '.$this->dao->quote($dtDate.' 00:00:00.000');
		}
		$groupBy .= ' GROUP BY les.[sClassCode] ';
		$classList = $this->dao2->getAll($strQuery.$condition.$groupBy);
		if(!empty($classList)){
			foreach ($classList as $key=>$class){
				$classList[$key]['dtbegindate'] = date('Y-m-d',$this->dao->msStrtotime($class['dtbegindate']));
				$classList[$key]['dtenddate'] = date('Y-m-d',$this->dao->msStrtotime($class['dtenddate']));
				$classList[$key]['n_lesson_no'] = $this->get_lessonList($class['s_class_code'],$condition);
			}
		}
		return $classList;
	}


	/*获取某班级详细信息*/
	public function get_classInfo($class_code,$speaker_number=0,$userInfo){
		$strQuery = 'SELECT class_name,class_code,campus_name,teacher_name,dtbegindate,dtenddate,sprinttime FROM '.$this->essay_list.' WHERE class_code = '.$this->dao->quote($class_code);
		if(!empty($speaker_number)){
			$strQuery .= ' AND speaker_number = '.$this->dao->quote($speaker_number);
		}
		$classInfo = $this->dao->getRow($strQuery);
		if(!empty($classInfo)){
			if($userInfo['nkind'] == 1){
				$condition = ' AND (case when les.[sAssistRealTeacherCode]<>'.$this->dao->quote().') then les.[sAssistRealTeacherCode] else les.[sAssistTeacherCode] end ) = '.$this->dao->quote($userInfo['scode']);
			}else{
				$condition = ' AND (case when les.[sRealTeacherCode]<>'.$this->dao->quote().' then les.[sRealTeacherCode] else les.[sTeacherCode]  end )= '.$this->dao->quote($userInfo['scode']);
			}
			$classInfo['n_lesson_no'] = $this->get_lessonList($class_code,$condition);
		}
		return $classInfo;
	}



	public function get_classCodeList($userInfo,$className=''){
		$strQuery = 'SELECT les.[sClassCode] FROM '.$this->bs_lesson.' as les  LEFT JOIN '.$this->view_class.' AS v ON les.[sClassCode] = v.[sClassCode] WHERE 1=1 ';
		if($userInfo['nkind'] == 1){
			$strQuery .= ' AND les.[sAssistTeacherCode] = '.$this->dao->quote($userInfo['scode']);
		}else{
			$strQuery .= ' AND les.[sTeacherCode]= '.$this->dao->quote($userInfo['scode']);
		}
		if(!empty($className)){
			$strQuery .= ' AND v.sClassName = '.$this->dao->quote($className);
		}
		$groupBy .= ' GROUP BY les.[sClassCode] ';
		return $this->dao2->getAll($strQuery.$groupBy);
	}


	public function do_excellent($arr){
		$instime = date('Y-m-d H:i:s');
		if($arr['operate'] == 'add'){
			$this->dao->execute("REPLACE INTO ".$this->essay_excellent." (class_code,speaker_number,essay_id,instime,operator) VALUES('$arr[class_code]','$arr[speaker_number]','$arr[essay_id]','$instime','$arr[user_key]')");
			return true;
		}else{
			if(!empty($arr['essay_id_str'])){
				$essay_id_str = "'".implode("','",explode('|',trim($arr['essay_id_str'],'|')))."'";
				$this->dao->execute("DELETE FROM ".$this->essay_excellent." WHERE essay_id IN ($essay_id_str) ");
				if($this->dao->affectRows()){
					return true;
				}
				return false;
			}
			return false;
		}
		return false;
	}


	public function get_excellentList($user_key,$order,$currentPage=1, $pageSize=20){
		$count = $this->get_excellentCount($user_key);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT ex.`id`,ex.`essay_id`,ex.`instime`,es.student_code,es.student_name,es.class_code,es.class_name,es.speaker_number,es.essay_imgs,a.avatar FROM '.$this->essay_excellent.' as ex '.
						' LEFT JOIN  '.$this->essay_list. ' as es ON ex.essay_id = es.id '.
						' LEFT JOIN  '.$this->essay_avatar. ' as a ON es.student_code = a.student_code '.
						' where ex.operator = '.$this->dao->quote($user_key);
		$order = ' order by '.$order;
		$list = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		if(!empty($list)){
			foreach ($list as $key=>$val){
				$list[$key]['avatar'] = str_replace('/Upload/','/upload/',end(explode('|',trim($val['essay_imgs'],'|'))));
			}
		}
		return $list;
	}


	public function get_excellentCount($user_key){
		return $this->dao->getOne("SELECT count(1) FROM ".$this->essay_excellent." WHERE operator = ".$this->dao->quote($user_key));
	}


	public function get_students_by_classCode($classCode){
		return $this->dao2->getAll('SELECT r.[sStudentCode],s.[sName] FROM '.$this->bs_roster.' as r LEFT JOIN '.$this->bs_student.' as s ON r.[sStudentCode] = s.[sCode] WHERE r.[bValid] = 1 AND r.[sClassCode] = '.$this->dao->quote($classCode));
	}

	public function get_extraStudent($arr){
		$strQuery = 'SELECT sid,sname FROM '.$this->essay_extra_student_relationship.' WHERE 1=1 ';
		if(!empty($arr['class_code'])){
			$strQuery .= ' AND class_code = '.$this->dao->quote($arr['class_code']);
		}
		if(!empty($arr['speaker_number'])){
			$strQuery .= ' AND speaker_number = '.$this->dao->quote($arr['speaker_number']);
		}
		if(!empty($arr['student_name'])){
			$strQuery .= ' AND sname = '.$this->dao->quote($arr['student_name']);
		}
		return $this->dao->getAll($strQuery);
	}

	public function get_extraStudentSid($student_name){
		return $this->dao->getOne('SELECT `sid` FROM '.$this->essay_extra_student.' WHERE `sname` = '.$this->dao->quote($student_name));
	}

	public function add_student($arr){
		if($sid = $this->get_extraStudentSid($arr['student_name'])){
			$this->dao->execute("INSERT INTO ".$this->essay_extra_student_relationship." (sid,sname,class_code,speaker_number,instime) VALUES('$sid','$arr[student_name]','$arr[class_code]','$arr[speaker_number]','".date('Y-m-d H:i:s')."')");
			if($this->dao->affectRows()){
				return $sid;
			}
			return false;
		}else{
			$this->dao->execute("INSERT INTO ".$this->essay_extra_student." (sname,instime) VALUES('$arr[student_name]','".date('Y-m-d H:i:s')."')");
			if($sid = $this->get_extraStudentSid($arr['student_name'])){
				$this->dao->execute("INSERT INTO ".$this->essay_extra_student_relationship." (sid,sname,class_code,speaker_number,instime) VALUES('$sid','$arr[student_name]','$arr[class_code]','$arr[speaker_number]','".date('Y-m-d H:i:s')."')");
				if($this->dao->affectRows()){
					return $sid;
				}
				return false;
			}
		}

	}

	public function get_studentList($arr){
		$extr_student = $this->get_extraStudent($arr);
		$gs_student = $this->get_students_by_classCode($arr['class_code']);
		if(!empty($extr_student)){
			foreach ($extr_student as $key=>$extra){
				$gs_student[] = array('sname'=>$extra['sname'],'sstudentcode'=>$extra['sid']);
			}
		}
		if(!empty($gs_student)){
			foreach ($gs_student as $key=>$student){
				if($this->get_essayInfo(array('class_code'=>$arr['class_code'],'speaker_number'=>$arr['speaker_number'],'student_code'=>$student['sstudentcode'],'student_name'=>$student['sname']))){
					$gs_student[$key]['is_upload'] = 1;
				}
			}
		}
		return $gs_student;
	}

	public function get_studentAvatar($student_code){
		$avatar = $this->dao->getOne('SELECT `avatar` FROM '.$this->essay_avatar.' WHERE `student_code`='.$this->dao->quote($student_code));
		if(!empty($avatar)){
			$avatar =  str_replace('/Upload/','/upload/',$avatar);
			$is_default = 0;
		}else{
			$avatar = C('DEFAULT_AVATAR');
			$is_default = 1;
		}
		return array('avatar'=>$avatar,'is_default'=>$is_default);
	}
	
	
	public function update_studentAvatar($arr){
		if($arr['act'] == 'update'){
			$this->dao->execute('UPDATE '.$this->essay_avatar.' SET `avatar` = '.$this->dao->quote($arr['avatar']).' WHERE `student_code`='.$this->dao->quote($arr['student_code']));
		}else{
			$this->dao->execute('INSERT INTO '.$this->essay_avatar.' (`student_code`,`avatar`) VALUES( '.$this->dao->quote($arr['student_code']).','.$this->dao->quote($arr['avatar']).')');
		}
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}
}
?>
