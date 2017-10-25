<?php

class VpStudentsModel extends Model {
	public  $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao();
		$this->view_VB_Student = 'view_VB_Student';
		$this->view_VB_StudentContract = 'view_VB_StudentContract';
		$this->view_VB_StudentLessonHeLu = 'view_VB_StudentLessonHeLu';
		$this->view_VD_KeCheng = 'view_VD_KeCheng';
		$this->V_BS_StudentLessonHeLu = 'V_BS_StudentLessonHeLu';
		$this->V_D_Grade = 'V_D_Grade';
		$this->V_D_Subject = 'V_D_Subject';
		$this->V_S_Dept = 'V_S_Dept';//vip校区表
		$this->V_BS_Roster = 'V_BS_Roster';
		$this->V_BS_RosterInfo = 'V_BS_RosterInfo';//学员科目情况明细
		$this->view_VB_StudentContract = 'view_VB_StudentContract';
		$this->V_Biz_Contract = 'V_Biz_Contract';
		$this->BS_Student = 'BS_Student';
		$this->BS_Teacher = 'BS_Teacher';
		$this->vp_kechenghelu = 'vp_kechenghelu';
		$this->vp_kechenghelu_files = 'vp_kechenghelu_files';
		$this->vp_training_program = 'vp_training_program';
		$this->vp_handouts = 'vp_handouts';
		$this->vp_kechenghelu_log = 'vp_kechenghelu_log';
		$this->vp_kecheng_overdue = 'vp_kecheng_overdue';
		$this->V_View_StuSumHours='V_View_StuSumHours';
	}

	public function get_myStudentList($arr,$type=1,$currentPage=1, $pageSize=20){
		$count = $this->get_myStudentCount($arr);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'select  max(view_helu.[id]) as heluId,
							 max(view_helu.[nLessonNo]) as nLessonNo,
							 max(view_helu.[sStudentCode]) as sStudentCode, 
							 max(view_helu.[sKeChengCode]) as sKeChengCode, 
							 max(convert(varchar(20),view_helu.[dtDateReal],120)) as dtDateReal,
							 max(convert(varchar(20),view_helu.[dtLessonBeginReal],120)) as dtLessonBeginReal,
							 max(convert(varchar(20),view_helu.[dtLessonEndReal],120)) as dtLessonEndReal,
							 max(view_helu.[nHours]) as nHours,
							 max(view_helu.[nStatus]) as nStatus,
							 min(view_helu.[nAudit]) as nAudit,
							 max(view_helu.[sStudentName]) as sStudentName,
							 max(view_helu.[nGrade]) as nGrade,
							 max(view_helu.[sClassAdviserCode]) as sClassAdviserCode,
							 max(view_helu.[sClassAdviserName]) as sClassAdviserName,
							 max(g.[sName]) as gradename,
							 max(vp_helu.[lesson_topic]) as lesson_topic,
							 max(vp_helu.[comment]) as comment,
							 max(vp_file.[url]) as url,
							 max(o.[is_overdue]) as is_overdue   
							 FROM '.$this->view_VB_StudentLessonHeLu.' as view_helu 
							 LEFT JOIN  '.$this->V_D_Grade.' as g ON g.[id] = view_helu.[nGrade] 
							 LEFT JOIN  '.$this->vp_kechenghelu.' as vp_helu ON vp_helu.[helu_id] = view_helu.[id] 
							 LEFT JOIN  '.$this->vp_kechenghelu_files.' as vp_file ON vp_file.[helu_id] = view_helu.[id] AND vp_file.type = 0 
							 LEFT JOIN  '.$this->vp_kecheng_overdue.' as o ON o.[helu_id] = view_helu.[id] 
							 WHERE view_helu.[nStatus] = 2 AND view_helu.[dtLessonBeginReal] >= '.$this->dao->quote(C('BIOCLOCK_START')).' AND view_helu.[dtLessonBeginReal] <= '.$this->dao->quote($arr['now']);
		if(!empty($arr['teacherCode'])) {
			$strQuery .=  ' AND view_helu.[steacherCode] = '.$this->dao->quote($arr['teacherCode']);
		}
		if($type == 0){
			if($arr['userTypeKey'] == 'VTeacher'){//VIP社会兼职教师
				$strQuery .= " AND (vp_helu.[lesson_topic] IS NULL  OR (vp_helu.[comment] IS NULL AND dtLessonBeginReal >= '2015-04-15 00:00:00')) ";
			}else{
				$strQuery .= " AND (vp_helu.[lesson_topic] IS NULL OR vp_helu.[comment] IS NULL) ";
			}
		}
		if(!empty($arr['overdue'])){
			$strQuery .= " AND o.[is_overdue] IS NULL ";
		}
		$strQuery .= ' GROUP BY view_helu.[id] ';
		if(!empty($arr['key_name']) && !empty($arr['order'])){
			$order = ' ORDER BY '.$arr['key_name'].' '.$arr['order'];
		}else{
			$order = ' ORDER BY [dtLessonBeginReal] DESC,[nAudit] ASC,[nStatus] ASC ';
		}
		if($type == 0){
			$list = $this->dao->getAll($strQuery.$order);
		}else{
			$list = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		}

		if(!empty($list)){
			foreach ($list as $key=>$val){
				$list[$key]['nobegin_count'] = $this->get_kechengNoBeginCount(array('kecheng_code'=>$val['skechengcode'],'student_code'=>$val['sstudentcode'],'time'=>$now,'teacher_code'=>$teacher_code));
				$list[$key]['dtdatereal'] = date('Y-m-d',strtotime($val['dtdatereal']));
				$list[$key]['dtlessonbeginreal'] = date('H:i',strtotime($val['dtlessonbeginreal']));
				$list[$key]['dtlessonendreal'] = date('H:i',strtotime($val['dtlessonendreal']));

				//判断课次核录是否逾期（48小时）
				$list[$key]['overdue'] = 0;
				if((strtotime($val['dtlessonendreal'])+48*3600)<time()){
					$list[$key]['overdue'] = 1;
				}
			}
		}
		return $list;
	}



	public function get_myStudentCount($arr){
		$strQuery = 'SELECT COUNT(1) FROM '.$this->view_VB_StudentLessonHeLu.' as view_helu
							 		 LEFT JOIN  '.$this->V_D_Grade.' as g ON g.[id] = view_helu.[nGrade] 
							 		 LEFT JOIN  '.$this->vp_kechenghelu.' as vp_helu ON vp_helu.[helu_id] = view_helu.[id] 
							 		 LEFT JOIN  '.$this->vp_kecheng_overdue.' as o ON o.[helu_id] = view_helu.[id] 
							 		 WHERE view_helu.[nStatus] = 2 AND view_helu.[dtLessonBeginReal] >= '.$this->dao->quote(C('BIOCLOCK_START')).' AND view_helu.[dtLessonBeginReal] <= '.$this->dao->quote($arr['now']);
		if($arr['teacherCode']) {
			$strQuery .=  ' AND view_helu.[steacherCode] = '.$this->dao->quote($arr['teacherCode']);
		}
		if(!empty($arr['overdue'])){
			$strQuery .= " AND o.[is_overdue] IS NULL ";
		}
		return $this->dao->getOne($strQuery);
	}



	public function get_kechengNoBeginCount($arr){
		$strQuery = 'select count(*) FROM '.$this->view_VB_StudentLessonHeLu.' WHERE 1=1 ';
		if(!empty($arr['kecheng_code'])){
			$strQuery .= ' AND [sKeChengCode] = '.$this->dao->quote($arr['kecheng_code']);
		}
		if(!empty($arr['student_code'])){
			$strQuery .= ' AND [sStudentCode] = '.$this->dao->quote($arr['student_code']);
		}
		if(!empty($arr['time'])){
			$strQuery .= ' AND [dtLessonBeginReal] > '.$this->dao->quote($arr['time']);
		}
		if(!empty($arr['teacher_code'])){
			$strQuery .= ' AND [steacherCode] = '.$this->dao->quote($arr['teacher_code']);
		}
		return $this->dao->getOne($strQuery);
	}



	public function get_studentInfo($student_code){
		return $this->dao->getRow('SELECT [sCode],[sAliasCode],[sName],[nGender],[nGrade] FROM '.$this->view_VB_Student.' WHERE [sCode] = '.$this->dao->quote($student_code));
	}


	public function get_studentContractInfo($student_code){
		return $this->dao->getRow('SELECT sc.[sContractCode],
										  sc.[sStudentCode],
										  sc.[sAliasCode],
										  sc.[sStudentName],
										  sc.[nGender],
										  sc.[CurrentGrade],
										  convert(varchar(20),sc.[dtBirthday],111) as dtBirthday,
										  sc.[sParents1Name],
										  sc.[nParents1Relation],
										  sc.[sParents1Phone],
										  sc.[sParents2Name],
										  sc.[nParents2Relation],
										  sc.[sParents2Phone],
										  sc.[sOperatorName],
										  convert(varchar(20),sc.[dtdate],120) as dtdate,
										  sc.[sDeptCode],
										  sc.[sClassAdviserCode],
										  sc.[sClassAdviserName],
										  sc.[sCharacter],
										  sc.[sHobby],
										  sc.[nRank],
										  sc.[sFeeTime],
									      r.[sKeChengCode],
									      view_kc.[sSubjectName] as sKeChengName,
									      r.[nHoursReg],
									      ri.[sTextbookVersion],
									      ri.[sCurrentlyLearning],
									      ri.[sTestScores],
									      ri.[sParentsSuggested],
									      ri.[sParentsExpect],
									      ri.[sParentsRequest],
									      ri.[dtFristLessonDate],
									      ri.[sFristLessonTime1],
									      ri.[sFristLessonTime2],
									      ri.[sFristLessonSuggested],
									      d.[sCode] as sDeptCode,
									      d.[sName] as sDeptName,
									      s.[sSchool],
									      s.[sEmail],
									      g.[sName] as gradename 
									      FROM '.$this->view_VB_StudentContract.' as sc 
									      LEFT JOIN '.$this->V_BS_Roster.' AS r ON r.[sContractCode] = sc.[sContractCode] 
									      LEFT JOIN '.$this->V_BS_RosterInfo.' AS ri ON ri.[sContractCode] = sc.[sContractCode]  
									      LEFT JOIN '.$this->V_S_Dept.' AS d ON sc.[sDeptCode] = d.[sCode] 
									      LEFT JOIN '.$this->view_VB_Student.' AS s ON s.[sCode] = sc.[sStudentCode] 
									      LEFT JOIN '.$this->view_VD_KeCheng.' AS view_kc ON view_kc.[sCode] = r.[sKeChengCode] 
									      LEFT JOIN  '.$this->V_D_Grade.' as g ON g.[id] = sc.[CurrentGrade] 
									      WHERE sc.[sStudentCode] = '.$this->dao->quote($student_code).' ORDER BY [dtdate] DESC');
	}


	public function do_helu($userInfo){

		$is_sendsms = $_POST['is_sendsms'];
		if(!empty($_POST['is_send_sms'])){
			$is_sendsms = 1;
		}
		if(!empty($_POST['student_code']) && !empty($_POST['student_name']) && !empty($_POST['kecheng_code']) && !empty($_POST['lesson_no']) && !empty($_POST['lesson_date']) && !empty($_POST['lesson_begin'])
		&& !empty($_POST['lesson_end']) && !empty($_POST['lesson_topic']) && !empty($_POST['comment'])){
			$now = time();
			if(!empty($_POST['id'])){
				$strQuery = 'UPDATE '.$this->vp_kechenghelu.' SET [lesson_begin] ='.$this->dao->quote($_POST['lesson_begin']).',
																  [lesson_end]='.$this->dao->quote($_POST['lesson_end']).',
																  [lesson_topic] = '.$this->dao->quote($_POST['lesson_topic']).',
																  [comment]='.$this->dao->quote(str_replace("'","’",$_POST['comment'])).',
																  [itembank_score]='.$this->dao->quote(abs($_POST['itembank_score'])).',[lasttime]='.$this->dao->quote(date('Y-m-d H:i:s')).',
																  [is_send_sms] = '.$this->dao->quote($is_sendsms).'   
																  WHERE [id] = '.$this->dao->quote($_POST['id']);
			}else{
				$strQuery = 'INSERT INTO '.$this->vp_kechenghelu.' ([helu_id],
																	[kecheng_code],
																	[lesson_no],
																	[student_code],
																	[student_name],
																	[lesson_date],
																	[lesson_begin],
																	[lesson_end],
																	[lesson_topic],
																	[comment],
																	[itembank_score],
																	[lasttime],
																	[is_send_sms]) 
															VALUES('.$this->dao->quote($_POST['helu_id']).',
																   '.$this->dao->quote($_POST['kecheng_code']).',
																   '.$this->dao->quote(abs($_POST['lesson_no'])).',
																   '.$this->dao->quote($_POST['student_code']).',
																   '.$this->dao->quote($_POST['student_name']).',
																   '.$this->dao->quote($_POST['lesson_date']).',
																   '.$this->dao->quote($_POST['lesson_begin']).',
																   '.$this->dao->quote($_POST['lesson_end']).',
																   '.$this->dao->quote($_POST['lesson_topic']).',
																   '.$this->dao->quote(str_replace("'","’",$_POST['comment'])).',
																   '.$this->dao->quote(abs($_POST['itembank_score'])).',
																   '.$this->dao->quote(date('Y-m-d H:i:s',$now)).',
																   '.$this->dao->quote($is_sendsms).')';
			}

			$this->dao->begin();
			$success1 = (boolean)$this->dao->execute($strQuery);
			if($success1 == false){
				$this->dao->rollback();
				return false;
			}
			$kecheng_name = $this->dao->getOne('SELECT [sSubjectName] FROM '.$this->view_VD_KeCheng.' WHERE [sCode] ='.$this->dao->quote($_POST['kecheng_code']));
			$is_handouts_url = $this->dao->getOne('SELECT COUNT(*) FROM '.$this->vp_kechenghelu_files.' WHERE type = 0 AND helu_id = '.$this->dao->quote($_POST['helu_id']));
			if(!empty($_POST['handouts_url'])){
				$title = $kecheng_name.'_'.$userInfo['real_name'].'_'.$_POST['student_name'].'_'.$_POST['lesson_no'].'_课程讲义_'.date('Y_m_d',strtotime($_POST['lesson_date']));
				if(!empty($is_handouts_url)){
					$strQuery2 = 'UPDATE '.$this->vp_kechenghelu_files.' SET [from_type] = 0,[url]='.$this->dao->quote(SysUtil::safeString($_POST['handouts_url'])).',[title]='.$this->dao->quote($title).' WHERE [type]= 0 AND [helu_id] = '.$this->dao->quote($_POST['helu_id']);
				}else{
					$strQuery2 = 'INSERT INTO '.$this->vp_kechenghelu_files.' ([helu_id],[title],[url],[type],[from_type]) VALUES('.$this->dao->quote($_POST['helu_id']).','.$this->dao->quote($title).','.$this->dao->quote(SysUtil::safeString($_POST['handouts_url'])).',0,0)';
				}
				$success2 = (boolean)$this->dao->execute($strQuery2);
				if($success2 == false){
					$this->dao->rollback();
					return false;
				}
			}/*else{
			if(!empty($is_handouts_url)){
			$strQuery2 = 'DELETE FROM '.$this->vp_kechenghelu_files.' WHERE helu_id = '.$this->dao->quote($_POST['helu_id']).' AND type = 0 ';
			$success2 = (boolean)$this->dao->execute($strQuery2);
			if($success2 == false){
			$this->dao->rollback();
			return false;
			}
			}
			}*/
			$is_itembank_url = $this->dao->getOne('SELECT COUNT(*) FROM '.$this->vp_kechenghelu_files.' WHERE type = 1 AND helu_id = '.$this->dao->quote($_POST['helu_id']));
			if(!empty($_POST['itembank_url'])){
				$title = $kecheng_name.'_'.$userInfo['real_name'].'_'.$_POST['student_name'].'_'.$_POST['lesson_no'].'_测试卷_'.date('Y_m_d',strtotime($_POST['lesson_date']));
				if(!empty($is_itembank_url)){
					$strQuery3 = 'UPDATE '.$this->vp_kechenghelu_files.' SET [from_type] = 0,[url]='.$this->dao->quote(SysUtil::safeString($_POST['itembank_url'])).',[title]='.$this->dao->quote($title).' WHERE [type]= 1 AND [helu_id] = '.$this->dao->quote($_POST['helu_id']);
				}else{
					$strQuery3 = 'INSERT INTO '.$this->vp_kechenghelu_files.' ([helu_id],[title],[url],[type],[from_type]) VALUES('.$this->dao->quote($_POST['helu_id']).','.$this->dao->quote($title).','.$this->dao->quote(SysUtil::safeString($_POST['itembank_url'])).',1,0)';
				}
				$success3 = (boolean)$this->dao->execute($strQuery3);
				if($success3 == false){
					$this->dao->rollback();
					return false;
				}
			}/*else{
			if(!empty($is_itembank_url)){
			$strQuery3 = 'DELETE FROM '.$this->vp_kechenghelu_files.' WHERE helu_id = '.$this->dao->quote($_POST['helu_id']).' AND type = 1 ';
			$success3 = (boolean)$this->dao->execute($strQuery3);
			if($success3 == false){
			$this->dao->rollback();
			return false;
			}
			}
			}*/
			$this->dao->commit();
			return true;
		}
		return false;
	}


	public function get_heluInfo($arr){
		$strQuery = 'SELECT TOP 1 helu.[id],
								  helu.[helu_id],
								  helu.[kecheng_code],
								  helu.[lesson_no],
								  helu.[student_code],
								  helu.[student_name],
								  convert(varchar(20),helu.[lesson_date],111) as lesson_date,
								  helu.[lesson_begin],
								  helu.[lesson_end],
								  helu.[lesson_topic],
								  helu.[comment],
								  a.[url] as handouts_url,
								  a.[title] as handouts_title,
								  b.[url] as itembank_url,
								  b.[title] as itembank_title,
								  helu.[itembank_score],
								  view_helu.[sTeacherName] as sTeacherName,
								  convert(varchar(20),helu.[lasttime],120) as lasttime,
								  helu.[is_send_sms]  
								  FROM '.$this->vp_kechenghelu.' AS helu 
								  LEFT JOIN '.$this->view_VB_StudentLessonHeLu.' view_helu ON helu.helu_id = view_helu.id
								  LEFT JOIN '.$this->vp_kechenghelu_files.' AS a ON helu.helu_id = a.helu_id AND a.type = 0 
								  LEFT JOIN '.$this->vp_kechenghelu_files.' AS b ON helu.helu_id = b.helu_id AND b.type = 1  
								  WHERE 1=1 ';
		if(!empty($arr['id'])){
			$strQuery .= ' AND helu.id ='.$this->dao->quote($arr['id']);
		}
		if(!empty($arr['helu_id'])){
			$strQuery .= ' AND (helu.helu_id ='.$this->dao->quote($arr['helu_id']).' OR a.helu_id = '.$this->dao->quote($arr['helu_id']).')';
		}
		if(!empty($arr['kecheng_code'])){
			$strQuery .= ' AND helu.kecheng_code ='.$this->dao->quote($arr['kecheng_code']);
		}
		if(!empty($arr['lesson_no'])){
			$strQuery .= ' AND helu.lesson_no ='.$this->dao->quote($arr['lesson_no']);
		}
		if(!empty($arr['student_code'])){
			$strQuery .= ' AND helu.student_code ='.$this->dao->quote($arr['student_code']);
		}
		$row = $this->dao->getRow($strQuery);
		if(!empty($row)){
			$row['lesson_date'] = (!empty($row['lesson_date']))?str_replace('/','-',$row['lesson_date']):'';

		}else{
			$strQuery2 = 'SELECT  [url] as handouts_url,
								  [title] as handouts_title 
	 							  FROM '.$this->vp_kechenghelu_files.'   
								  WHERE helu_id = '.$this->dao->quote($arr['helu_id']);
			$strQuery3 = 'SELECT [url] as itembank_url,
								 [title] as itembank_title 
								 FROM '.$this->vp_kechenghelu_files.' 
								 WHERE helu_id = '.$this->dao->quote($arr['helu_id']);
			$tempOne = $this->dao->getRow($strQuery2);
			if(!empty($tempOne)){
				$row['handouts_url'] = $tempOne['handouts_url'];
				$row['handouts_title'] = $tempOne['handouts_title'];
			}
			$tempTwo = $this->dao->getRow($strQuery2);
			if(!empty($tempTwo)){
				$row['itembank_url'] = $tempOne['itembank_url'];
				$row['itembank_title'] = $tempOne['itembank_title'];
			}
		}
		if(!empty($row)){
			$row['itembank_url_show'] = (!empty($row['itembank_url']))?str_replace('/Upload/','/upload/',$row['itembank_url']):'';
			$row['handouts_count'] = 0;
			$row['handouts'] = array();
			if(!empty($row['handouts_url'])){
				foreach (explode('|',trim($row['handouts_url'],'|')) as $kk=>$v){
					$row['handouts_count']++;
					$row['handouts'][$kk]['url'] = $v;
					$row['handouts'][$kk]['url_show'] = str_replace('/Upload/','/upload/',$v);
					$row['handouts'][$kk]['is_exist'] = !file_exists(APP_DIR.$v)?0:1;
					$row['handouts'][$kk]['filetype'] =  strtolower(end(explode('.',$v)));
				}
			}
			$row['itembank_count'] = 0;
			$row['itembank'] = array();
			if(!empty($row['itembank_url'])){
				foreach (explode('|',trim($row['itembank_url'],'|')) as $kk=>$v){
					$row['itembank_count']++;
					$row['itembank'][$kk]['url'] = $v;
					$row['itembank'][$kk]['url_show'] = str_replace('/Upload/','/upload/',$v);
					$row['itembank'][$kk]['is_exist'] = !file_exists(APP_DIR.$v)?0:1;
					$row['itembank'][$kk]['filetype'] =  strtolower(end(explode('.',$v)));
				}
			}
		}
		return $row;
	}


	public function get_heluList($condition='', $currentPage=1, $pageSize=20){
		$count = $this->get_heluListCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT helu.[id],
							helu.[helu_id],
							helu.[lesson_no],
							convert(varchar(20),helu.[lesson_date],111) as lesson_date,
							helu.[lesson_end],
							helu.[lesson_topic],
							helu.[comment],
							a.[url] as handouts_url,
							a.[title] as handouts_title,
							b.[url] as itembank_url,
							b.[title] as itembank_title,
							helu.[itembank_score],
							helu.[lasttime] 
							FROM '.$this->vp_kechenghelu.' as helu 
							LEFT JOIN '.$this->vp_kechenghelu_files.' AS a ON helu.helu_id = a.helu_id AND a.type = 0 
							LEFT JOIN '.$this->vp_kechenghelu_files.' AS b ON helu.helu_id = b.helu_id AND b.type = 1  
							WHERE 1=1 ';
		if(!empty($condition)){
			$strQuery .= ' AND '.$condition;
		}
		$order = ' ORDER BY [lasttime] DESC';
		$list = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		if(!empty($list)){
			foreach ($list as $key=>$val){
				$list[$key]['lesson_date'] = (!empty($val['lesson_date']))?str_replace('/','-',$val['lesson_date']):'';
				$list[$key]['handouts_url_show'] = !empty($val['handouts_url'])?explode('|',trim((!empty($val['handouts_url']))?str_replace('/Upload/','/upload/',$val['handouts_url']):'','|')):'';
				$list[$key]['handouts_url'] = !empty($val['handouts_url'])?explode('|',trim($val['handouts_url'],'|')):'';
				$list[$key]['itembank_url_show'] = !empty($val['itembank_url'])?explode('|',trim((!empty($val['itembank_url']))?str_replace('/Upload/','/upload/',$val['itembank_url']):'','|')):'';
				$list[$key]['itembank_url'] = !empty($val['itembank_url'])?explode('|',trim($val['itembank_url'],'|')):'';
				$list[$key]['is_exist_handouts'] = !file_exists(APP_DIR.$val['handouts_url'])?0:1;
				$list[$key]['is_exist_itembank'] = !file_exists(APP_DIR.$val['itembank_url'])?0:1;
				$list[$key]['handouts_type'] = strtolower(end(explode('.',$val['handouts_url'])));
				$list[$key]['itembank_type'] = strtolower(end(explode('.',$val['itembank_url'])));
				$list[$key]['handouts_count'] = 0;
				if(!empty($list[$key]['handouts_url'])){
					foreach ($list[$key]['handouts_url'] as $kk=>$v){
						$list[$key]['handouts_count']++;
						$list[$key]['handouts'][$kk]['url'] = $v;
						$list[$key]['handouts'][$kk]['is_exist'] = !file_exists(APP_DIR.$v)?0:1;
						$list[$key]['handouts'][$kk]['filetype'] =  strtolower(end(explode('.',$v)));
					}
				}
				$list[$key]['itembank_count'] = 0;
				if(!empty($list[$key]['itembank_url'])){
					foreach ($list[$key]['itembank_url'] as $kk=>$v){
						$list[$key]['itembank_count']++;
						$list[$key]['itembank'][$kk]['url'] = $v;
						$list[$key]['itembank'][$kk]['is_exist'] = !file_exists(APP_DIR.$v)?0:1;
						$list[$key]['itembank'][$kk]['filetype'] =  strtolower(end(explode('.',$v)));
					}
				}
			}
		}
		return $list;
	}


	public function get_heluListCount($condition=''){
		$strQuery = 'SELECT count(1) FROM '.$this->vp_kechenghelu.' as helu WHERE 1=1 ';
		if(!empty($condition)){
			$strQuery .= ' AND '.$condition;
		}
		return $this->dao->getOne($strQuery);
	}



	public function get_lessonAuditStatus($arr){
		return $this->dao->getOne('SELECT [nAudit] FROM '.$this->V_BS_StudentLessonHeLu.' WHERE [nLessonNo]='.$this->dao->quote($arr['lesson_no']).' AND [sKeChengCode] = '.$this->dao->quote($arr['kecheng_code']).' AND [sStudentCode] = '.$this->dao->quote($arr['student_code']));
	}


	public function add_trainingProgram($arr,$user_key,$ip){
		//获取教师所属校区
		$deptInfo = $this->dao->getRow('SELECT t.[sDeptCode] as dept_code,sdp.[sName] as dept_name FROM '.$this->BS_Teacher.' t LEFT JOIN '.$this->V_S_Dept.' sdp ON t.[sDeptCode] = sdp.[sCode] WHERE t.[sCode] = '.$this->dao->quote($arr['teacher_code']));
		$strQuery = 'INSERT INTO '.$this->vp_training_program.' ([student_code],
																 [student_name],
																 [program_url],
																 [instime],
																 [kecheng_code],
																 [kecheng_name],
																 [teacher_code],
																 [teacher_name],
																 [dept_code],
																 [dept_name]) 
														 VALUES('.$this->dao->quote($arr['student_code']).',
																'.$this->dao->quote($arr['student_name']).',
																'.$this->dao->quote($arr['url']).',
																'.$this->dao->quote(date('Y-m-d H:i:s')).',
																'.$this->dao->quote($arr['kecheng_code']).',
																'.$this->dao->quote($arr['kecheng_name']).',
																'.$this->dao->quote($arr['teacher_code']).',
																'.$this->dao->quote($arr['teacher_name']).',
																'.$this->dao->quote($deptInfo['dept_code']).',
																'.$this->dao->quote($deptInfo['dept_name']).' 
																) ';
		$strQuery2 = 'INSERT INTO '.$this->vp_handouts." ([type],
														  [title],
														  [introduce],
														  [teacher_version],
														  [instime],
														  [user_key],
														  [is_teaching_and_research],
														  [IP],
														  [status])
							 					   VALUES('0',
							 					   		  '$arr[student_name]的培养方案".date('Y-m-d H:i:s')."',
							 					   		  '$arr[student_name]的培养方案',
							 					   		  '$arr[url]',
							 					   		  '".time()."',
							 					   		  '$user_key',
							 					   		  '0',
							 					   		  '$ip',
							 					   		  '1')";
		$this->dao->begin();
		$success = (boolean)$this->dao->execute($strQuery);
		$success2 = (boolean)$this->dao->execute($strQuery2);
		if($success == true && $success2 == true){
			$this->dao->commit();
			return true;
		}else{
			$this->dao->rollback();
			return false;
		}
	}



	public function get_programList($student_code,$teacher_code){
		if(!empty($student_code)){
			$strQuery = 'SELECT id,
								student_code,
								student_name,
								program_url,
								convert(varchar(20),instime,120) as instime ,
								convert(varchar(20),updatetime,120) as updatetime ,
								kecheng_code,
								kecheng_name,
								from_type,
								teacher_code,
								teacher_name,
								dept_code,
								dept_name,
								program_html,
								program_img,
								dimension_level,
								testCoachId,
								subject_code  
								FROM '.$this->vp_training_program.' 
							    WHERE [student_code] = '.$this->dao->quote($student_code);
			if(!empty($teacher_code)){
				$strQuery .= ' AND teacher_code = '.$this->dao->quote($teacher_code);
			}
			$list = $this->dao->getAll($strQuery);
			if(!empty($list)){
				foreach ($list as $key=>$val){
					$list[$key]['subject_name'] = $this->dao->getOne('SELECT sName FROM '.$this->V_D_Subject.' WHERE [sCode] = '.$this->dao->quote($val['subject_code']));
					$list[$key]['program_arr'] = array();
					if(!empty($val['program_url'])){
						$temp_program_arr = explode('|',trim($val['program_url'],'|'));
						foreach ($temp_program_arr as $k=>$v){
							$list[$key]['program_arr'][$k]['url'] = $v;
							$list[$key]['program_arr'][$k]['file_url'] = APP_DIR.$v;
							$temp_file_type = end(explode('.',$v));
							if(in_array(strtolower($temp_file_type),array('jpg','jpeg','gif','png'))){
								$list[$key]['program_arr'][$k]['preview_url'] = APP_DIR.end(explode('_',$v));
							}else{
								$tempFileUrlArr = explode('/',$v);
								$list[$key]['program_arr'][$k]['preview_url'] = APP_DIR.$tempFileUrlArr[0].'/'.$tempFileUrlArr[1].'/'.$tempFileUrlArr[2].'/'.str_replace('.'.$temp_file_type,'.swf',end(explode("_",end($tempFileUrlArr))));
							}

							$list[$key]['program_arr'][$k]['is_download'] = 0;
							if(file_exists($list[$key]['program_arr'][$k]['file_url'])){
								$list[$key]['program_arr'][$k]['is_download'] = 1;
							}

							$list[$key]['program_arr'][$k]['is_preview'] = 0;
							if(file_exists($list[$key]['program_arr'][$k]['preview_url'])){
								$list[$key]['program_arr'][$k]['is_preview'] = 1;
							}
						}
					}


					if(!empty($val['program_html'])){
						$list[$key]['program_html_is_exist'] = !file_exists(APP_DIR.$val['program_html'])?0:1;
						$list[$key]['program_html_show'] = str_replace('/Upload/','/upload/',$val['program_html']);
					}
					if(!empty($val['program_img'])){
						$list[$key]['program_img_is_exist'] = !file_exists(APP_DIR.$val['program_img'])?0:1;
						$list[$key]['program_img_show'] = str_replace('/Upload/','/upload/',$val['program_img']);
					}


				}
			}
			return $list;
		}
		return false;
	}


	public function get_programInfo($arr){
		$strQuery ='SELECT  [id],[student_code],[student_name],[program_url],[program_html],[program_img],convert(varchar(20),[instime],120) as instime,kecheng_code,kecheng_name,from_type,teacher_code,teacher_name,dept_code,dept_name FROM '.$this->vp_training_program.' WHERE 1=1 ';
		if(!empty($arr['id'])){
			$strQuery .= ' AND [id] = '.$this->dao->quote($arr['id']);
		}
		$row = $this->dao->getRow($strQuery);
		$row = $this->dao->getRow($strQuery);
		$row['program_count'] = 0;
		if(!empty($row)){
			if(!empty($row['program_url'])){
				foreach (explode('|',trim($row['program_url'],'|')) as $kk=>$v){
					$row['program_count']++;
					$row['program'][$kk]['url'] = $v;
					$row['program'][$kk]['url_show'] = str_replace('/Upload/','/upload/',$v);
					$row['program'][$kk]['is_exist'] = !file_exists(APP_DIR.$v)?0:1;
					$row['program'][$kk]['filetype'] =  strtolower(end(explode('.',$v)));
				}
			}
		}
		return $row;
	}


	public function del_program($id){
		$programInfo = $this->get_programInfo(array('id'=>$id));
		$this->dao->execute('DELETE FROM '.$this->vp_training_program.' WHERE [id] = '.$this->dao->quote(abs($id)));
		if($this->dao->affectRows()){
			@unlink(APP_DIR.$programInfo['program_url']);
			return true;
		}
		return false;
	}


	public function get_scheduleList($arr){
		if(!empty($arr['teacher_code'])){
			$strQuery = 'select  helu.[id],
								 helu.[nLessonNo],
								 helu.[sStudentCode], 
								 helu.[sKeChengCode], 
								 convert(varchar(20),helu.[dtDateReal],120) as dtDateReal,
								 convert(varchar(20),helu.[dtLessonBeginReal],120) as dtLessonBeginReal,
								 convert(varchar(20),helu.[dtLessonEndReal],120) as dtLessonEndReal,
								 helu.[nHoursReal],
								 cast(datediff(mi,helu.dtLessonBeginReal,helu.dtLessonEndReal)/60.0 as numeric(9,1)) as nHoursReal,
								 helu.[nStatus],
								 stu.[sName] as sStudentName,
								 d.[sName] as sAreaName FROM '.$this->view_VB_StudentLessonHeLu.' as helu 
								 LEFT JOIN '.$this->view_VB_Student.' as stu ON stu.[sCode] = helu.[sStudentCode] 
								 INNER JOIN '.$this->V_Biz_Contract.' as c ON c.[sCode] =  helu.[sContractCode] AND c.[sStudentCode] = helu.[sStudentCode]  
								 LEFT JOIN '.$this->V_S_Dept.' as d ON d.[sCode] =  c.[sDeptCode] 
								 WHERE helu.[nStatus] != 3 ';
			if(!empty($arr['teacher_code'])) {
				$strQuery .=  ' AND helu.[steacherCode] = '.$this->dao->quote($arr['teacher_code']);
			}
			if(!empty($arr['start'])) {
				$strQuery .=  ' AND helu.[dtDateReal] >= '.$this->dao->quote(date('Y-m-d H:i:s',$arr['start']));
			}
			if(!empty($arr['end'])) {
				$strQuery .=  ' AND helu.[dtDateReal] <= '.$this->dao->quote(date('Y-m-d H:i:s',$arr['end']));
			}
			$list = $this->dao->getAll($strQuery);
			if(!empty($list)){
				$total_hours = 0;
				$student_arr = array();
				foreach ($list as $key=>$val){
					if(strtotime($val['dtlessonendreal']) < time()){
						$list[$key]['is_end'] = 1;
					}
					if(strtotime($val['dtlessonbeginreal']) < time()){
						$list[$key]['is_begin'] = 1;
					}
					$total_hours += $val['nhoursreal'];
					$student_arr[] = $val['sstudentcode'];
				}
				$total_students = count(array_unique($student_arr));
			}
		}else{
			$list = array();
		}
		return array('list'=>$list,'total_hours'=>abs($total_hours),'total_students'=>abs($total_students));
	}




	public function get_allStudents($arr){
		$strQuery = 'select  max(view_helu.[sStudentCode]) as sStudentCode,
							 max(view_helu.[sStudentName]) as sStudentName,
							 max(view_helu.[id]) as id,
							 max(view_helu.[sKeChengCode]) as sKeChengCode,
							 max(view_helu.[nLessonNo]) as  nLessonNo
							 FROM '.$this->view_VB_StudentLessonHeLu.' as view_helu 
							 LEFT JOIN '.$this->V_BS_Roster.' as r ON view_helu.[sCardCode] = r.[sCardCode] 
							 WHERE view_helu.[dtLessonBeginReal] >= '.$this->dao->quote(C('BIOCLOCK_START'));
		if(!empty($arr['teacherCode'])){
			$strQuery .= ' AND view_helu.[steacherCode] = '.$this->dao->quote($arr['teacherCode']);
		}
		if($arr['is_jieke'] == 1){
			$strQuery .= ' AND r.[nHoursRegHas] < r.[nHoursPay] ';
		}
		$strQuery .= ' group by view_helu.[sStudentCode] ';
		return $this->dao->getAll($strQuery);
	}



	public function get_kechengAll($arr){
		$strQuery = 'select  max(view_helu.[sKeChengCode]) as sKeChengCode,
							 max(view_helu.[sKeChengName]) as sKeChengName,
							 max(view_helu.[nLessonNo]) as  nLessonNo 
							 FROM '.$this->view_VB_StudentLessonHeLu.' as view_helu 
							 LEFT JOIN '.$this->V_BS_Roster.' as r ON view_helu.[sCardCode] = r.[sCardCode] 
							 WHERE  1=1 ';
		if($arr['is_jieke']){
			$strQuery .= ' AND r.[nHoursRegHas] < r.[nHoursPay] ';
		}
		if(!empty($arr['teacherCode'])){
			$strQuery .= ' AND view_helu.[steacherCode] = '.$this->dao->quote($arr['teacherCode']);
		}
		if(!empty($arr['studentCode'])){
			$strQuery .= 'AND view_helu.[sStudentCode] = '.$this->dao->quote($arr['studentCode']);
		}
		$strQuery .= ' group by view_helu.[sKeChengCode] ';
		return $this->dao->getAll($strQuery);
	}



	public function get_kechengInfo($arr){
		$strQuery = 'SELECT [id]
					      ,[nLessonNo]
					      ,[nRosterInfoId]
					      ,[sContractCode]
					      ,[sStudentCode]
					      ,[sKeChengCode]
					      ,convert(varchar(20),[dtDate],120) as dtDate
					      ,convert(varchar(20),[dtLessonBegin],120) as dtLessonBegin
					      ,convert(varchar(20),[dtLessonEnd],120) as dtLessonEnd
					      ,convert(varchar(20),[dtDateReal],120) as dtDateReal
					      ,convert(varchar(20),[dtLessonBeginReal],120) as dtLessonBeginReal
					      ,convert(varchar(20),[dtLessonEndReal],120) as dtLessonEndReal
					      ,[nHours]
					      ,[sRoom]
					      ,[sLessonMemo]
					      ,[dCurrentExpend]
					      ,[sTeacherCode]
					      ,[nStatus]
					      ,[nAbsenceReasion]
					      ,[sMemo]
					      ,[nPerPKOpType]
					      ,[sPerPKOperatorCode]
					      ,[nTeacherAudit]
					      ,[nAudit]
					      ,[bUseHoursGiftExtra]
					      ,[sAuditOpCode] 
					      FROM '.$this->V_BS_StudentLessonHeLu.' WHERE 1=1 ';
		if(!empty($arr['id'])){
			$strQuery .= ' AND [id] = '.$this->dao->quote(abs($arr['id']));
		}
		if(!empty($arr['kecheng_code'])){
			$strQuery .= ' AND [sKeChengCode] = '.$this->dao->quote(SysUtil::safeString($arr['kecheng_code']));
		}
		if(!empty($arr['student_code'])){
			$strQuery .= ' AND [sStudentCode] = '.$this->dao->quote(SysUtil::safeString($arr['student_code']));
		}
		if(!empty($arr['teacher_code'])){
			$strQuery .= ' AND [sTeacherCode] = '.$this->dao->quote(SysUtil::safeString($arr['teacher_code']));
		}
		if(!empty($arr['max_lesson'])){
			$strQuery .= ' AND [nLessonNo] = '.$this->dao->quote(abs($arr['max_lesson']));
		}
		return $this->dao->getRow($strQuery);
	}





	/*检查是否能进行加课、调课、核录、标记缺勤操作，学员上课时间是否存在交叉则返回false
	$type=0时为核录、标记缺勤课时，$type=1时为调课、加课
	*/
	public function checkIsCanOperate($arr,$type){
		$strQuery = 'select [sKeChengCode],
							[dtDate],
							[dtLessonBegin],
							[dtLessonEnd],
							[nRosterInfoId],
							[sTeacherCode],
							[sStudentCode]  
							FROM '.$this->V_BS_StudentLessonHeLu.' as helu 
							WHERE helu.nStatus!=3 AND id<>'.$this->dao->quote($arr['id']).' 
							and dtDateReal='.$this->dao->quote($arr['dtdatereal']).' 
							and ( (dtLessonBeginReal>='.$this->dao->quote($arr['dtlessonbeginreal']).' and dtLessonBeginReal< '.$this->dao->quote($arr['dtlessonendreal']).') 
								   or (dtLessonEndReal>'.$this->dao->quote($arr['dtlessonbeginreal']).' and dtLessonEndReal<= '.$this->dao->quote($arr['dtlessonendreal']).') 
								   or (dtLessonBeginReal<='.$this->dao->quote($arr['dtlessonbeginreal']).' and dtLessonEndReal>= '.$this->dao->quote($arr['dtlessonendreal']).') 
								   or (dtLessonBeginReal>='.$this->dao->quote($arr['dtlessonbeginreal']).' and dtLessonEndReal<= '.$this->dao->quote($arr['dtlessonendreal']).') 
								 )';
		$list = $this->dao->getAll($strQuery);
		if(!empty($list)){
			foreach ($list as $key=>$val){
				if($type == 0 && $val['nrosterinfoid']== $arr['nrosterinfoid'] ){
					return false;
					break;
				}
				if($type == 1 && $val['steachercode'] ==$arr['steachercode'] && ($val['skechengcode'] != $arr['skechengcode'] || ($val['skechengcode'] == $arr['skechengcode'] && ($val['dtlessonbegin']!=$arr['dtlessonbegin']||$val['dtlessonend']!=$arr['dtlessonend'])))){
					return false;
					break;
				}
				if($type == 1 && ($val['steachercode'] !=$arr['steachercode']) && ($val['sstudentcode'] ==$arr['sstudentcode'])){
					return false;
					break;
				}
			}
		}
		return true;
	}


	public function deal_get_heluListAll(){
		return $this->dao->getAll("SELECT helu.*,convert(varchar(20),helu.[lasttime],120) as lasttime,view_helu.[sKeChengName],view_helu.[sTeacherName] FROM ".$this->vp_kechenghelu." as helu
									LEFT JOIN ".$this->view_VB_StudentLessonHeLu." as view_helu ON helu.[helu_id]= view_helu.[id] 
									WHERE helu.handouts_url !='' OR helu.itembank_url!='' ");
	}

	public function deal_insertFile($arr){
		$this->dao->execute('INSERT INTO '.$this->vp_kechenghelu_files.' ([helu_id],[title],[url],[type]) VALUES('.$this->dao->quote($arr['helu_id']).','.$this->dao->quote($arr['title']).','.$this->dao->quote($arr['url']).','.$this->dao->quote($arr['type']).')');
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}

	public function deal_get_heluFilesAll(){
		return $this->dao->getAll("SELECT f.[id],
										  f.[helu_id],
										  f.[type],
										  h.[lesson_no],
										  h.[student_name],
										  v.[nLessonNo],
										  v.[sStudentName],
										  convert(varchar(20),v.[dtDateReal],120) as dtdatereal,
										  v.[sKeChengName],
										  v.[sTeacherName]  
								  		  FROM ".$this->vp_kechenghelu_files." as f 
								  		  LEFT JOIN ".$this->vp_kechenghelu." as h ON f.[helu_id]= h.[helu_id] 
								  		  LEFT JOIN ".$this->view_VB_StudentLessonHeLu." as v ON f.[helu_id]= v.[id] ");
	}

	public function deal_updateFileTitle($arr){
		$this->dao->execute('UPDATE '.$this->vp_kechenghelu_files.' SET [title] = '.$this->dao->quote($arr['new_title']).' WHERE id = '.$this->dao->quote($arr['id']).' AND helu_id ='.$this->dao->quote($arr['helu_id']));
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}
	public function get_sended_students(){
		$strQuery="select vbs.id,vbs.[sTeacherCode],vbs.[dtHeLuDate],vbs.[dtLessonBegin],vbs.[dtLessonEnd],vbs.[id],vbs.[sStudentCode],stu.[sName],tea.[sPhone]
						from ".$this->V_BS_StudentLessonHeLu." as vbs
						left join ".$this->BS_Student." as stu on vbs.[sStudentCode] = stu.[sCode]  
						left join ".$this->BS_Teacher." as tea on vbs.[sTeacherCode] = tea.[sCode]  
						where vbs.[nStatus] = 2 
							and vbs.[nAutoHeLuStatus] = 1 
							and vbs.[dtHeLuDate] > '2014-10-20' 
							and dateadd(day,1,vbs.[dtHeLuDate]) < getdate() 
							and  not exists(select ke.[helu_id] from ".$this->vp_kechenghelu." as ke where vbs.id = ke.helu_id)";
		return $this->dao->getAll($strQuery);
	}


	/*添加课评统计记录*/
	public function addHeluLog($arr){

		$this->dao->execute('INSERT INTO '.$this->vp_kechenghelu_log.' ([student_name],
																		[lesson_date],
																		[lesson_topic],
																		[teacher_name],
																		[comment],
																		[helu_time],
																		[is_select_sendsms],
																		[is_trigger_sendsms],
																		[is_upload_handouts],
																		[helu_type],
																		[to_mobile]) 
																VALUES ('.$this->dao->quote($arr['student_name']).',
																		'.$this->dao->quote($arr['lesson_date']).',
																		'.$this->dao->quote($arr['lesson_topic']).',
																		'.$this->dao->quote($arr['teacher_name']).',
																		'.$this->dao->quote(str_replace("'","’",$arr['comment'])).',
																		'.$this->dao->quote($arr['helu_time']).',
																		'.$this->dao->quote($arr['is_select_sendsms']).',
																		'.$this->dao->quote($arr['is_trigger_sendsms']).',
																		'.$this->dao->quote($arr['is_upload_handouts']).',
																		'.$this->dao->quote($arr['helu_type']).',
																		'.$this->dao->quote($arr['to_mobile']).' )');
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}


	public function do_overdue($helu_id){
		if(!empty($helu_id)){
			$count = $this->dao->getOne('SELECT COUNT(1) FROM '.$this->vp_kecheng_overdue.' WHERE helu_id = '.$this->dao->quote($helu_id));
			if($count>0){
				return true;
			}else{
				if($this->dao->execute('INSERT INTO '.$this->vp_kecheng_overdue.' (helu_id, is_overdue, instime) VALUES ('.$this->dao->quote($helu_id).','.$this->dao->quote(1).','.$this->dao->quote(date('Y-m-d H:i:s')).')')){
					return true;
				}
				return false;
			}
		}
		return false;
	}



	public function get_waitHeluList($arr){
		$strQuery = 'select   view_helu.[id] as heluId,
							  convert(varchar(20),view_helu.[dtDateReal],120) as dtDateReal, 
							  convert(varchar(20),view_helu.[dtLessonBeginReal],120) as dtLessonBeginReal, 
							  convert(varchar(20),view_helu.[dtLessonEndReal],120) as dtLessonEndReal, 
							  view_helu.[sStudentCode], 
							  view_helu.[sStudentName], 
							  vp_helu.[lesson_topic], 
							  vp_file.[lecture_id], 
							  o.[is_overdue]  
							  FROM '.$this->view_VB_StudentLessonHeLu.' as view_helu 
							  LEFT JOIN  '.$this->V_D_Grade.' as g ON g.[id] = view_helu.[nGrade] 
							  LEFT JOIN  '.$this->vp_kechenghelu.' as vp_helu ON vp_helu.[helu_id] = view_helu.[id] 
							  LEFT JOIN  '.$this->vp_kechenghelu_files.' as vp_file ON vp_file.[helu_id] = view_helu.[id] AND vp_file.type = 0 
							  LEFT JOIN  '.$this->vp_kecheng_overdue.' as o ON o.[helu_id] = view_helu.[id] 
							  WHERE view_helu.[nStatus] = 2 
							  AND view_helu.[dtLessonBeginReal] >= '.$this->dao->quote(C('BIOCLOCK_START')).' 
							  AND view_helu.[dtLessonBeginReal] <= '.$this->dao->quote($arr['now'])." 
							  AND (((vp_helu.[lesson_topic] IS NULL OR vp_helu.[module_answer] IS NULL OR vp_helu.[practise_answer] IS NULL OR vp_helu.[dimension_level] IS NULL OR vp_helu.[lesson_report_url_wx] IS NULL) AND view_helu.[dtLessonBeginReal] >= ".$this->dao->quote(C('PIV_START'))." AND vp_file.[lecture_id] IS NOT NULL) OR ((vp_helu.[lesson_topic] IS NULL OR vp_file.[url] IS NULL) AND view_helu.[dtLessonBeginReal] < ".$this->dao->quote(C('PIV_START')).")) ";

		if(!empty($arr['teacherCode'])) {
			$strQuery .=  ' AND view_helu.[steacherCode] = '.$this->dao->quote($arr['teacherCode']);
		}
		if(!empty($arr['overdue'])){
			$strQuery .= " AND o.[is_overdue] IS NULL ";
		}
		$strQuery .= ' ORDER BY [dtLessonBeginReal] DESC,heluId DESC ';
		$list = $this->dao->getAll($strQuery);
		if(!empty($list)){
			foreach ($list as $key=>$val){
				$list[$key]['dtdatereal'] = date('Y-m-d',strtotime($val['dtdatereal']));
				$list[$key]['dtlessonbeginreal'] = date('H:i',strtotime($val['dtlessonbeginreal']));
				$list[$key]['dtlessonendreal'] = date('H:i',strtotime($val['dtlessonendreal']));

				//判断课次核录是否逾期（48小时）
				$list[$key]['overdue'] = 0;
				if((strtotime($val['dtlessonendreal'])+48*3600)<time()){
					$list[$key]['overdue'] = 1;
				}
			}
		}
		return $list;
	}




	public function get_waitPrepareAll($arr){
		$strQuery = 'select   view_helu.[id] as heluId,
							  convert(varchar(20),view_helu.[dtDateReal],120) as dtDateReal, 
							  convert(varchar(20),view_helu.[dtLessonBeginReal],120) as dtLessonBeginReal, 
							  convert(varchar(20),view_helu.[dtLessonEndReal],120) as dtLessonEndReal, 
							  view_helu.[sStudentCode], 
							  view_helu.[sStudentName],
							  vp_file.[lecture_id] 
							  FROM '.$this->view_VB_StudentLessonHeLu.' as view_helu 
							  LEFT JOIN  '.$this->vp_kechenghelu_files.' as vp_file ON vp_file.[helu_id] = view_helu.[id] AND vp_file.type = 0 
							  WHERE view_helu.[nStatus] IN (0,1) 
							  AND view_helu.[dtLessonBeginReal] >= '.$this->dao->quote($arr['now']).' 
							  AND view_helu.[dtLessonBeginReal] <= '.$this->dao->quote(date('Y-m-d H:i:s',time()+3600*24*7))." 
							  AND  vp_file.[lecture_id] IS NULL ";

		if(!empty($arr['teacherCode'])) {
			$strQuery .=  ' AND view_helu.[steacherCode] = '.$this->dao->quote($arr['teacherCode']);
		}
		$strQuery .= ' ORDER BY [dtLessonBeginReal] ASC,heluId DESC ';
		$list = $this->dao->getAll($strQuery);
		if(!empty($list)){
			foreach ($list as $key=>$val){
				$list[$key]['dtdatereal'] = date('Y-m-d',strtotime($val['dtdatereal']));
				$list[$key]['dtlessonbeginreal'] = date('H:i',strtotime($val['dtlessonbeginreal']));
				$list[$key]['dtlessonendreal'] = date('H:i',strtotime($val['dtlessonendreal']));
			}
		}
		return $list;
	}

	/********* 新增 ************/


	/**
	 * 我的学员课时
	 * @param  [type]  $arr         [description]
	 * @param  integer $type        [description]
	 * @param  integer $currentPage [description]
	 * @param  integer $pageSize    [description]
	 * @return [type]               [description]
	 */
	public function getStudentLesson($arr,$type=1,$currentPage=1, $pageSize=20)
	{
		$count = $this->get_myStudentLessonCount($arr);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery=' select view_hours.[id],view_hours.[sAliasCode],view_hours.[sStudentName],view_hours.[sStudentCode],view_hours.[dSumHours],view_hours.[dHours],view_hours.[dMonthSumHours],view_hours.[dEndSumHours],view_hours.[nStudentProperty] from '.$this->V_View_StuSumHours.' as view_hours where 1=1';
		if(!empty($arr['teacherCode'])) {
			$strQuery .=  ' AND view_hours.[sTeacherCode] = '.$this->dao->quote($arr['teacherCode']);
		}

		if(!empty($arr['student_name']))
		{
			$strQuery.=' AND view_hours.[sStudentName]='.$this->dao->quote($arr['student_name']);
		}
		if(!empty($arr['status']))
		{
			$strQuery.='AND view_hours.[nStudentProperty]='.$this->dao->quote($arr['status']);
		}

		$order='ORDER BY [nStudentProperty] ASC, [dSumHours] desc ';

		if($type == 0){
			$list = $this->dao->getAll($strQuery.$order);
		}else{
			$list = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		}
		//获取查询时间内数据
		foreach($list as $k=>$v)
		{
			if(!empty($arr['begin_time']) && !empty($arr['end_time']))
			{
				$data = $this->dao->getRow("exec V_Proc_StuSumHours ".$this->dao->quote($arr['begin_time']).",".$this->dao->quote($arr['end_time']).",".$this->dao->quote($arr['teacherCode']).",".$this->dao->quote($v['sstudentcode']));
				$list[$k]['dhours']=$data['dhours'];
				$list[$k]['dendsumhours']=$data['dendsumhours'];
			}
		}
		return $list;
	}

/*	declare @start_time date set @start_time='2016-11-12' 
  declare @end_time date set @end_time='2016-11-20'
	declare @teacher_code varchar(50) set @teacher_code='VP01857'
  exec V_Proc_StuSumHours @start_time,@end_time,@teacher_code*/

	/**
	 * 我的学员课时统计
	 * @param  [type] $arr [description]
	 * @return [type]      [description]
	 */
	public function get_myStudentLessonCount($arr){
	$strQuery = 'SELECT count(1) FROM '.$this->V_View_StuSumHours.' as view_hours where 1=1';
	
	if($arr['teacherCode']) {
		$strQuery .=  ' AND view_hours.[sTeacherCode] = '.$this->dao->quote($arr['teacherCode']);
	}

	if($arr['student_name'])
	{
		$strQuery.=' AND view_hours.[sStudentName]='.$this->dao->quote($arr['student_name']);
	}

	if(!empty($arr['status']))
	{
		$strQuery.='AND view_hours.[nStudentProperty]='.$this->dao->quote($arr['status']);
	}

	return $this->dao->getOne($strQuery);
	}	
}
?>