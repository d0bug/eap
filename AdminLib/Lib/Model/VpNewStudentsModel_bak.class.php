<?php

class VpNewStudentsModel extends Model {
	public  $dao = null;
	public  $dao2 = null;
	public function __construct(){
		$this->dao = Dao::getDao();
		$this->view_VB_Student = 'view_VB_Student';
		$this->view_VB_StudentContract = 'view_VB_StudentContract';
		$this->view_VB_StudentLessonHeLu = 'view_VB_StudentLessonHeLu';
		$this->view_VD_KeCheng = 'view_VD_KeCheng';
		$this->V_BS_StudentLessonHeLu = 'V_BS_StudentLessonHeLu';
		$this->V_D_Grade = 'V_D_Grade';
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

		$this->vp_comment_dimension = 'vp_comment_dimension';
		$this->vp_comment_level = 'vp_comment_level';
		$this->vp_comment_text = 'vp_comment_text';
		$this->vp_subject_dimension_rs = 'vp_subject_dimension_rs';
		$this->vp_words = 'vp_words';
		$this->vp_student_error_questions = 'vp_student_error_questions';
		$this->vp_program_dimension = 'vp_program_dimension';
		$this->vp_program_level = 'vp_program_level';
		$this->vp_program_text = 'vp_program_text';
		$this->vp_program_lesson = 'vp_program_lesson';

		$this->V_Teacher_TestCoachMBONew = 'V_Teacher_TestCoachMBONew';
		$this->V_D_Subject = 'V_D_Subject';



		$this->dao2 = Dao::getDao('MYSQL_CONN_KNOWLEDGE');
		$this->vip_question = 'vip_question';
		$this->vip_question_option = 'vip_question_option';
		$this->vip_question_answer = 'vip_question_answer';
		$this->vip_teacher_lecture = 'vip_teacher_lecture';
		$this->vip_dict_subject = 'vip_dict_subject';
		$this->vip_dict_grade = 'vip_dict_grade';
		$this->vip_knowledge = 'vip_knowledge';

	}
//							 (SELECT TOP 1 convert(varchar(20),[dtLessonBeginReal],120) as dtLessonBeginReal FROM '.$this->view_VB_StudentLessonHeLu.' WHERE nStatus != 3 AND sStudentCode = view_helu.[sStudentCode] AND steacherCode = '.$this->dao->quote($arr['teacherCode']).' AND dtLessonBeginReal> getdate() ORDER BY dtLessonBeginReal ASC  ) as next_lesson_begin,  
	public function get_myStudentList($arr,$currentPage=1, $pageSize=20){
		$count = $this->get_myStudentCount($arr);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'select
							 max(view_helu.[sStudentCode]) as sStudentCode, 
							 max(view_helu.[sStudentName]) as sStudentName,
							 max(view_helu.[nGrade]) as nGrade,
							 max(view_helu.[sClassAdviserCode]) as sClassAdviserCode,
							 max(view_helu.[sClassAdviserName]) as sClassAdviserName,
							 max(g.[sName]) as gradename, 
							 max(dept.[sName]) as deptname,
							 max(view_c.[CurrentGrade]) as CurrentGrade ,
							 (SELECT count(1) FROM '.$this->view_VB_StudentLessonHeLu.' WHERE sStudentCode = view_helu.[sStudentCode] AND steacherCode = '.$this->dao->quote($arr['teacherCode']).' AND dtLessonBeginReal > getdate() ) as nobegin_count,
							 (SELECT count(1) FROM '.$this->view_VB_StudentLessonHeLu.' WHERE sStudentCode = view_helu.[sStudentCode] AND steacherCode = '.$this->dao->quote($arr['teacherCode']).' AND dtLessonEndReal < getdate() ) as end_count,
							 (SELECT TOP 1 convert(varchar(20),[dtLessonBeginReal],120) as dtLessonBeginReal FROM '.$this->view_VB_StudentLessonHeLu.' WHERE nStatus != 3 AND sStudentCode = view_helu.[sStudentCode] AND steacherCode = '.$this->dao->quote($arr['teacherCode']).' AND dtLessonBeginReal> getdate() ORDER BY dtLessonBeginReal ASC  ) as next_lesson_begin,  
							 (SELECT TOP 1 fa.lecture_id FROM '.$this->view_VB_StudentLessonHeLu.' as v LEFT JOIN '.$this->vp_kechenghelu_files.' AS fa ON v.id = fa.helu_id AND fa.type = 0  WHERE v.nStatus != 3 AND v.sStudentCode = view_helu.[sStudentCode] AND v.steacherCode = '.$this->dao->quote($arr['teacherCode']).' AND v.dtLessonBeginReal> getdate() ORDER BY dtLessonBeginReal ASC  ) as next_lesson_lecture_id,
							 (SELECT TOP 1 helu.lesson_report_url FROM '.$this->view_VB_StudentLessonHeLu.' as v LEFT JOIN '.$this->vp_kechenghelu.' AS helu ON v.id = helu.helu_id WHERE v.nStatus != 3 AND v.sStudentCode = view_helu.[sStudentCode] AND v.steacherCode = '.$this->dao->quote($arr['teacherCode']).' AND v.dtLessonBeginReal> getdate() ORDER BY dtLessonBeginReal ASC  ) as next_lesson_report  
							 FROM '.$this->view_VB_StudentLessonHeLu.' as view_helu 
							 LEFT JOIN  '.$this->view_VB_StudentContract.' as view_c ON view_c.[sContractCode] = view_helu.[sContractCode] 
							 LEFT JOIN  '.$this->V_D_Grade.' as g ON g.[id] = view_c.[CurrentGrade] 
							 LEFT JOIN  '.$this->V_S_Dept.' as dept ON dept.[sCode] = view_helu.[sDeptCode] 
							 WHERE 1=1 ';
		if(!empty($arr['teacherCode'])) {
			$strQuery .=  ' AND view_helu.[steacherCode] = '.$this->dao->quote($arr['teacherCode']);
		}
		if(!empty($arr['start'])) {
			$strQuery .=  ' AND (SELECT TOP 1 convert(varchar(20),[dtLessonBeginReal],120) as dtLessonBeginReal FROM '.$this->view_VB_StudentLessonHeLu.' WHERE nStatus != 3 AND sStudentCode = view_helu.[sStudentCode] AND steacherCode = '.$this->dao->quote($arr['teacherCode']).' AND dtLessonBeginReal> getdate() ORDER BY dtLessonBeginReal ASC  ) >= '.$this->dao->quote($arr['start']);
		}
		if(!empty($arr['end'])) {
			$strQuery .=  ' AND (SELECT TOP 1 convert(varchar(20),[dtLessonBeginReal],120) as dtLessonBeginReal FROM '.$this->view_VB_StudentLessonHeLu.' WHERE nStatus != 3 AND sStudentCode = view_helu.[sStudentCode] AND steacherCode = '.$this->dao->quote($arr['teacherCode']).' AND dtLessonBeginReal> getdate() ORDER BY dtLessonBeginReal ASC  ) <= '.$this->dao->quote($arr['end'].' 23:59:59');
		}
		if(!empty($arr['dept_code'])) {
			$strQuery .=  ' AND dept.[sCode] = '.$this->dao->quote($arr['dept_code']);
		}
		if(!empty($arr['student_name'])) {
			$strQuery .=  ' AND view_helu.sStudentName = '.$this->dao->quote($arr['student_name']);
		}
		$strQuery .= ' GROUP BY view_helu.[sStudentCode] ';
		if(!empty($arr['key_name']) && !empty($arr['order'])){
			$order = ' ORDER BY '.$arr['key_name'].' '.$arr['order'];
		}else{
			//$order = ' ORDER BY nobegin_count DESC ';
			$order = ' ORDER BY case when next_lesson_begin is null then nobegin_count else cast(next_lesson_begin as datetime) end DESC';		//默认按照上次课时间倒序排序，如果没有上次课上课时间按照未上课次数倒序
		}

		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}


	public function get_myStudentCount($arr){
		$strQuery = 'SELECT
							 max(view_helu.[sStudentCode]) as sStudentCode, 
							 max(view_helu.[sStudentName]) as sStudentName,
							 max(dept.[sName]) as deptname,
							 (SELECT TOP 1 convert(varchar(20),[dtLessonBeginReal],120) as dtLessonBeginReal FROM '.$this->view_VB_StudentLessonHeLu.' WHERE nStatus != 3 AND sStudentCode = view_helu.[sStudentCode] AND steacherCode = '.$this->dao->quote($arr['teacherCode']).' AND dtLessonBeginReal> getdate() ORDER BY dtLessonBeginReal ASC  ) as next_lesson_begin  
							 FROM '.$this->view_VB_StudentLessonHeLu.' as view_helu 
							 LEFT JOIN  '.$this->V_D_Grade.' as g ON g.[id] = view_helu.[nGrade] 
							 LEFT JOIN  '.$this->V_S_Dept.' as dept ON dept.[sCode] = view_helu.[sDeptCode] 
							 WHERE 1=1 ';
		if($arr['teacherCode']) {
			$strQuery .=  ' AND view_helu.[steacherCode] = '.$this->dao->quote($arr['teacherCode']);
		}
		if(!empty($arr['start'])) {
			$strQuery .=  ' AND (SELECT TOP 1 convert(varchar(20),[dtLessonBeginReal],120) as dtLessonBeginReal FROM '.$this->view_VB_StudentLessonHeLu.' WHERE nStatus != 3 AND sStudentCode = view_helu.[sStudentCode] AND steacherCode = '.$this->dao->quote($arr['teacherCode']).' AND dtLessonBeginReal> getdate() ORDER BY dtLessonBeginReal ASC  ) >= '.$this->dao->quote($arr['start']);
		}
		if(!empty($arr['end'])) {
			$strQuery .=  ' AND (SELECT TOP 1 convert(varchar(20),[dtLessonBeginReal],120) as dtLessonBeginReal FROM '.$this->view_VB_StudentLessonHeLu.' WHERE nStatus != 3 AND sStudentCode = view_helu.[sStudentCode] AND steacherCode = '.$this->dao->quote($arr['teacherCode']).' AND dtLessonBeginReal> getdate() ORDER BY dtLessonBeginReal ASC  ) <= '.$this->dao->quote($arr['end'].' 23:59:59');
		}
		if(!empty($arr['dept_code'])) {
			$strQuery .=  ' AND dept.[sCode] = '.$this->dao->quote($arr['dept_code']);
		}
		if(!empty($arr['student_name'])) {
			$strQuery .=  ' AND sStudentName = '.$this->dao->quote($arr['student_name']);
		}
		$strQuery .= ' GROUP BY view_helu.[sStudentCode] ';
		$list = $this->dao->getAll($strQuery);
		return count($list);
	}


	public function get_deptList(){
		return $this->dao->getAll('SELECT sCode,sName FROM '.$this->V_S_Dept.' WHERE [bAreaDept] = 1 ');
	}



	public function get_lessonList($arr,$currentPage=1, $pageSize=20){
		$count = $this->get_lessonCount($arr);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'select  view_helu.[id] heluId,
							 view_helu.[nLessonNo],
							 view_helu.[sStudentCode],
							 view_helu.[sStudentName], 
							 view_helu.[sKeChengCode],
							 view_helu.[sKeChengName],  
							 convert(varchar(20),view_helu.[dtDateReal],120) as dtDateReal,
							 convert(varchar(20),view_helu.[dtLessonBeginReal],120) as dtLessonBeginReal,
							 convert(varchar(20),view_helu.[dtLessonEndReal],120) as dtLessonEndReal,
							 view_helu.[nStatus],
							 view_helu.[nAudit],
							 vp_helu.[id],						 
							 vp_helu.[lesson_topic],
							 vp_helu.[comment],
							 vp_helu.[lesson_report_url],
							 vp_helu.[lesson_report_img],
							 vp_file.[title] as handouts_title,
							 vp_file.[url] as handouts_url,
							 vp_file.[lecture_id],
							 vp_file2.[title] as itembank_title,
							 vp_file2.[url] as itembank_url,
							 o.[is_overdue]    
							 FROM '.$this->view_VB_StudentLessonHeLu.' as view_helu 
							 LEFT JOIN  '.$this->vp_kechenghelu.' as vp_helu ON vp_helu.[helu_id] = view_helu.[id] 
							 LEFT JOIN  '.$this->vp_kechenghelu_files.' as vp_file ON vp_file.[helu_id] = view_helu.[id] AND vp_file.type = 0 
							 LEFT JOIN  '.$this->vp_kechenghelu_files.' as vp_file2 ON vp_file2.[helu_id] = view_helu.[id] AND vp_file2.type = 1 
							 LEFT JOIN  '.$this->vp_kecheng_overdue.' as o ON o.[helu_id] = view_helu.[id] 
							 WHERE view_helu.nStatus != 3 AND view_helu.[dtLessonBeginReal] >= '.$this->dao->quote(C('BIOCLOCK_START'));
		if(!empty($arr['teacherCode'])) {
			$strQuery .=  ' AND view_helu.[steacherCode] = '.$this->dao->quote($arr['teacherCode']);
		}
		if(!empty($arr['student_code'])) {
			$strQuery .=  ' AND view_helu.[sStudentCode] = '.$this->dao->quote($arr['student_code']);
		}

		$order = ' ORDER BY [dtLessonBeginReal] DESC,heluId DESC ';
		$list = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);

		if(!empty($list)){
			foreach ($list as $key=>$val){
				$list[$key]['show_lesson_report_url'] = str_replace('/Upload/','/upload/',$val['lesson_report_url']);
				$list[$key]['show_lesson_report_img'] = str_replace('/Upload/','/upload/',$val['lesson_report_img']);
				$list[$key]['lesson_report_img_is_download'] = 0;
				if(file_exists(APP_DIR.$val['lesson_report_img'])){
					$list[$key]['lesson_report_img_is_download'] = 1;
				}
				$list[$key]['dtdatereal'] = date('Y-m-d',strtotime($val['dtdatereal']));
				$list[$key]['dtlessonbeginreal'] = date('H:i',strtotime($val['dtlessonbeginreal']));
				$list[$key]['dtlessonendreal'] = date('H:i',strtotime($val['dtlessonendreal']));

				//判断课次核录是否逾期（48小时）
				$list[$key]['overdue'] = 0;
				if((strtotime($val['dtlessonendreal'])+48*3600)<time()){
					$list[$key]['overdue'] = 1;
				}

				$list[$key]['handouts_count'] = 0;
				if(!empty($val['handouts_url'])){
					foreach (explode('|',trim($val['handouts_url'],'|')) as $kk=>$v){
						$list[$key]['handouts_count']++;
						$list[$key]['handouts'][$kk]['url'] = $v;
						$list[$key]['handouts'][$kk]['url_show'] = str_replace('/Upload/','/upload/',$v);
						$list[$key]['handouts'][$kk]['is_exist'] = !file_exists(APP_DIR.$v)?0:1;
						$list[$key]['handouts'][$kk]['filetype'] =  strtolower(end(explode('.',$v)));
					}
				}
				$list[$key]['itembank_count'] = 0;
				if(!empty($val['itembank_url'])){
					foreach (explode('|',trim($val['itembank_url'],'|')) as $kk=>$v){
						$list[$key]['itembank_count']++;
						$list[$key]['itembank'][$kk]['url'] = $v;
						$list[$key]['itembank'][$kk]['url_show'] = str_replace('/Upload/','/upload/',$v);
						$list[$key]['itembank'][$kk]['is_exist'] = !file_exists(APP_DIR.$v)?0:1;
						$list[$key]['itembank'][$kk]['filetype'] =  strtolower(end(explode('.',$v)));
					}
				}

				if(!empty($val['lecture_id'])){
					$list[$key]['lecture_title'] = $this->dao2->getOne('SELECT title FROM '.$this->vip_teacher_lecture.' WHERE id = '.$this->dao->quote($val['lecture_id']));
				}
			}
		}
		return $list;
	}


	public function get_lessonCount($arr){
		$strQuery = 'SELECT COUNT(1) FROM '.$this->view_VB_StudentLessonHeLu.' as view_helu
							 		 LEFT JOIN  '.$this->vp_kechenghelu.' as vp_helu ON vp_helu.[helu_id] = view_helu.[id] 
							 		 LEFT JOIN  '.$this->vp_kecheng_overdue.' as o ON o.[helu_id] = view_helu.[id] 
							 		 WHERE view_helu.nStatus != 3 AND view_helu.[dtLessonBeginReal] >= '.$this->dao->quote(C('BIOCLOCK_START'));
		if($arr['teacherCode']) {
			$strQuery .=  ' AND view_helu.[steacherCode] = '.$this->dao->quote($arr['teacherCode']);
		}
		if(!empty($arr['student_code'])) {
			$strQuery .=  ' AND view_helu.[sStudentCode] = '.$this->dao->quote($arr['student_code']);
		}
		return $this->dao->getOne($strQuery);
	}


	public function  get_errorQuestionList($arr, $type=0, $currentPage=1, $pageSize=20){
		$optionKeyArr = C('OPTIONS_KEY');
		$count = $this->get_errorQuestionCount($arr);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'select  eq.[id],
							 eq.[student_code],
							 eq.[helu_id],
							 eq.[question_id],
							 eq.[type],
							 view_helu.[sKeChengCode],
							 view_helu.[sKeChengName],  
							 convert(varchar(20),view_helu.[dtDateReal],111) as dtDateReal,
							 convert(varchar(20),view_helu.[dtLessonBeginReal],120) as dtLessonBeginReal,
							 convert(varchar(20),view_helu.[dtLessonEndReal],120) as dtLessonEndReal,
							 vp_helu.[lesson_topic] 
							 FROM '.$this->vp_student_error_questions.' as eq  
							 LEFT JOIN  '.$this->view_VB_StudentLessonHeLu.' as view_helu ON eq.[helu_id] = view_helu.[id] 
							 LEFT JOIN  '.$this->vp_kechenghelu.' as vp_helu ON eq.[helu_id] = vp_helu.[helu_id]  
							 WHERE  eq.question_id > 0 ';
		if(!empty($arr['teacherCode'])) {
			$strQuery .=  ' AND view_helu.[steacherCode] = '.$this->dao->quote($arr['teacherCode']);
		}
		if(!empty($arr['student_code'])) {
			$strQuery .=  ' AND eq.[student_code] = '.$this->dao->quote($arr['student_code']);
		}
		if(!empty($arr['start'])) {
			$strQuery .=  ' AND view_helu.dtLessonBeginReal >= '.$this->dao->quote($arr['start']);
		}
		if(!empty($arr['end'])) {
			$strQuery .=  ' AND view_helu.dtLessonBeginReal <= '.$this->dao->quote(date('Y-m-d ',strtotime($arr['end'])).'23:59:59');
		}
		if(!empty($arr['lesson_topic'])) {
			$strQuery .=  ' AND vp_helu.[lesson_topic] LIKE  '.$this->dao->quote("%".$arr['lesson_topic']."%");
		}
		if(!empty($arr['helu_id'])) {
			$strQuery .=  ' AND eq.[helu_id] ='.$this->dao->quote($arr['helu_id']);
		}
		if(!empty($arr['type'])) {
			$strQuery .=  ' AND eq.[type] = '.$this->dao->quote($arr['type']);
		}
		$order = ' ORDER BY [id] DESC ';
		if($type == 1){
			$list = $this->dao->getAll($strQuery.$order);
		}else{
			$list = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		}

		if(!empty($list)){
			foreach ($list as $key=>$val){
				if(!empty($val['question_id'])){
					$list[$key]['question_desc'] = $this->dao2->getRow('SELECT q.content,
																		   q.analysis,
																		   a.content as answer_content 
																		   FROM '.$this->vip_question.' q 
																		   LEFT JOIN '.$this->vip_question_answer.' a ON q.id = a.question_id AND a.`status` = 1  
																		   WHERE q.parent_id = 0 AND q.id = '.$this->dao->quote($val['question_id']));
					$list[$key]['question_option'] = $this->dao2->getAll('SELECT id,
																			 content,
																			 is_answer,
																			 sort  
																		   	 FROM '.$this->vip_question_option.'  
																		     WHERE `status` = 1 AND question_id = '.$this->dao->quote($val['question_id']).' ORDER BY sort ASC');

					if(!empty($list[$key]['question_option'])){
						$list[$key]['question_desc']['answer_content'] = '';
						foreach ($list[$key]['question_option'] as $k=>$v){
							if($v['is_answer'] == 1){
								$list[$key]['question_desc']['answer_content'] .= $optionKeyArr[$k];
							}
						}
					}
				}else{
					unset($list[$key]);
				}
			}
		}
		return $list;
	}


	public function get_errorQuestionCount($arr){
		$strQuery = 'select  count(1)
							 FROM '.$this->vp_student_error_questions.' as eq  
							 LEFT JOIN  '.$this->view_VB_StudentLessonHeLu.' as view_helu ON eq.[helu_id] = view_helu.[id] 
							 LEFT JOIN  '.$this->vp_kechenghelu.' as vp_helu ON eq.[helu_id] = vp_helu.[helu_id]  
							 WHERE  eq.question_id > 0 ';
		if(!empty($arr['teacherCode'])) {
			$strQuery .=  ' AND view_helu.[steacherCode] = '.$this->dao->quote($arr['teacherCode']);
		}
		if(!empty($arr['student_code'])) {
			$strQuery .=  ' AND eq.[student_code] = '.$this->dao->quote($arr['student_code']);
		}
		if(!empty($arr['start'])) {
			$strQuery .=  ' AND view_helu.dtDateReal >= '.$this->dao->quote($arr['start']);
		}
		if(!empty($arr['end'])) {
			$strQuery .=  ' AND view_helu.dtDateReal <= '.$this->dao->quote(date('Y-m-d ',strtotime($arr['end'])).'23:59:59');
		}
		if(!empty($arr['lesson_topic'])) {
			$strQuery .=  ' AND vp_helu.[lesson_topic] LIKE  '.$this->dao->quote("%".$arr['lesson_topic']."%");
		}
		return $this->dao->getOne($strQuery);
	}



	public function delete_errorQuestion($error_id){
		if($this->dao->execute('DELETE FROM '.$this->vp_student_error_questions.' WHERE id = '.$this->dao->quote($error_id))){
			return true;
		}
		return false;
	}


	public function get_baseHeluInfo($helu_id){
		$row = $this->dao->getRow('SELECT helu.*,
										 convert(varchar(20),helu.lesson_report_createtime,120) as lesson_report_createtime,
										 view_helu.id as helu_id,
										 view_helu.[sKeChengCode] as sKeChengCode,
										 view_helu.[sStudentCode] as sStudentCode,
										 view_helu.[sTeacherCode] as sTeacherCode,
										 view_helu.[nLessonNo] as nLessonNo,
										 view_helu.[sStudentName] as sStudentName,
										 view_helu.[sKeChengName] as sKeChengName,
										 view_helu.[sClassAdviserName] as sClassAdviserName,
										 convert(varchar(20),view_helu.[dtDateReal],120) as dtDateReal,
										 convert(varchar(20),view_helu.[dtLessonBeginReal],120) as dtLessonBeginReal,
							 			 convert(varchar(20),view_helu.[dtLessonEndReal],120) as dtLessonEndReal 
										 FROM '.$this->view_VB_StudentLessonHeLu.' view_helu 
										 LEFT JOIN '.$this->vp_kechenghelu.' helu ON helu.helu_id = view_helu.id 
										 WHERE view_helu.id = '.$this->dao->quote($helu_id));
		$row['lecture_id'] = $this->dao->getOne('SELECT lecture_id FROM '.$this->vp_kechenghelu_files.' WHERE type = 0 AND  helu_id = '.$this->dao->quote($helu_id));
		if(!empty($row['module_answer'])){
			$row['module_answer'] = unserialize($row['module_answer']);
		}

		if(!empty($row['practise_answer'])){
			$row['practise_answer'] = unserialize($row['practise_answer']);
		}

		if(!empty($row['work_answer'])){
			$row['work_answer'] = unserialize($row['work_answer']);
		}

		if(!empty($row['dimension_level'])){
			$row['dimension'] = unserialize($row['dimension_level']);
		}else{
			if(!empty($row['lecture_info']['eap_subject_id'])){
				$row['dimension'] = $this->dao->getAll('SELECT d.id,d.title FROM '.$this->vp_subject_dimension_rs.' rs LEFT JOIN '.$this->vp_comment_dimension.' d ON rs.dimension_id = d.id WHERE rs.sid = '.$this->dao->quote($row['lecture_info']['eap_subject_id']));//获取课堂评价维度
			}
			if(empty($row['lecture_info']['dimension'])){
				$row['dimension'] = $this->dao->getAll('SELECT id,title FROM '.$this->vp_comment_dimension);//获取课堂评价维度
			}
		}


		if(!empty($row['lesson_record_img'])){
			$row['lesson_record_img'] = explode('|',trim($row['lesson_record_img'],'|'));
		}
		return $row;
	}

	public function get_heluInfo($helu_id,$type = 1 ){
		$row = $this->dao->getRow('SELECT helu.*,
										 convert(varchar(20),helu.lesson_report_createtime,120) as lesson_report_createtime,
										 view_helu.id as helu_id,
										 view_helu.[sKeChengCode] as sKeChengCode,
										 view_helu.[sStudentCode] as sStudentCode,
										 view_helu.[sTeacherCode] as sTeacherCode,
										 view_helu.[nLessonNo] as nLessonNo,
										 view_helu.[sStudentName] as sStudentName,
										 view_helu.[sKeChengName] as sKeChengName,
										 view_helu.[sClassAdviserName] as sClassAdviserName,
										 convert(varchar(20),view_helu.[dtDateReal],120) as dtDateReal,
										 convert(varchar(20),view_helu.[dtLessonBeginReal],120) as dtLessonBeginReal,
							 			 convert(varchar(20),view_helu.[dtLessonEndReal],120) as dtLessonEndReal 
										 FROM '.$this->view_VB_StudentLessonHeLu.' view_helu 
										 LEFT JOIN '.$this->vp_kechenghelu.' helu ON helu.helu_id = view_helu.id 
										 WHERE view_helu.id = '.$this->dao->quote($helu_id));
		$row['lecture_id'] = $this->dao->getOne('SELECT lecture_id FROM '.$this->vp_kechenghelu_files.' WHERE type = 0 AND  helu_id = '.$this->dao->quote($helu_id));

		if(!empty($row) && !empty($row['lecture_id']) && $type==1){
			$row['lecture_info'] = $this->dao2->getRow('SELECT le.*,s.title as subject_name,s.eap_subject_id as eap_subject_id,g.title as grade_name,le.created_time as created_time FROM '.$this->vip_teacher_lecture.' le LEFT JOIN '.$this->vip_dict_subject.' s ON le.subject_id = s.id LEFT JOIN '.$this->vip_dict_grade.' g ON s.grade_id = g.id WHERE le.id = '.$this->dao2->quote($row['lecture_id']));
			if(!empty($row['lecture_info'])){
				$row['lecture_info']['cart'] = unserialize($row['lecture_info']['cart']);
				$row['lecture_info']['config'] = unserialize($row['lecture_info']['config']);
				$row['lecture_info']['question_list']['module'] = $row['lecture_info']['config']['struct']['body'][$row['lecture_info']['cart']['cart']['sort']]['types'];
				if(!empty($row['lecture_info']['question_list']['module'])){
					foreach ($row['lecture_info']['question_list']['module'] as $key=>$type){
						if(!empty($row['lecture_info']['cart']['cart']['question_rs'][$row['lecture_info']['cart']['cart']['sort']])){
							foreach ($row['lecture_info']['cart']['cart']['question_rs'][$row['lecture_info']['cart']['cart']['sort']] as $k=>$v){
								if($k == $type['id']){
									$row['lecture_info']['question_list']['module'][$key]['question_list'] = $this->get_questionList2($v);
									foreach ($v as $kk=>$val){
										$row['lecture_info']['question_list']['module_question'][] = $val;
									}
								}
							}
						}
					}
				}
				/*$module_question_id_arr = array();
				if(!empty($row['lecture_info']['cart']['cart']['question_rs']['module'])){
				foreach ($row['lecture_info']['cart']['cart']['question_rs']['module'] as $key=>$val){
				$module_question_id_arr = $row['lecture_info']['cart']['cart']['question_rs']['module'][$key];
				}
				}*/


				$practise_question_id_arr = array();
				if(!empty($row['lecture_info']['cart']['cart']['question_rs']['practise'])){
					foreach ($row['lecture_info']['cart']['cart']['question_rs']['practise'] as $key=>$val){
						$practise_question_id_arr = $row['lecture_info']['cart']['cart']['question_rs']['practise'][$key];
					}
				}


				$work_question_id_arr = array();
				if(!empty($row['lecture_info']['cart']['cart']['question_rs']['work'])){
					foreach ($row['lecture_info']['cart']['cart']['question_rs']['work'] as $key=>$val){
						$work_question_id_arr= $row['lecture_info']['cart']['cart']['question_rs']['work'][$key];
					}
				}

				//$row['lecture_info']['question_list']['module'] = $this->get_questionList( $module_question_id_arr);
				$row['lecture_info']['question_list']['practise'] = $this->get_questionList2( $practise_question_id_arr);
				$row['lecture_info']['question_list']['work'] = $this->get_questionList2( $work_question_id_arr);
			}

			if(!empty($row['module_answer'])){
				$row['module_answer'] = unserialize($row['module_answer']);
			}

			if(!empty($row['practise_answer'])){
				$row['practise_answer'] = unserialize($row['practise_answer']);
			}

			if(!empty($row['work_answer'])){
				$row['work_answer'] = unserialize($row['work_answer']);
			}

			if(!empty($row['dimension_level'])){
				$row['dimension'] = unserialize($row['dimension_level']);
			}else{
				if(!empty($row['lecture_info']['eap_subject_id'])){
					$row['dimension'] = $this->dao->getAll('SELECT d.id,d.title FROM '.$this->vp_subject_dimension_rs.' rs LEFT JOIN '.$this->vp_comment_dimension.' d ON rs.dimension_id = d.id WHERE rs.sid = '.$this->dao->quote($row['lecture_info']['eap_subject_id']));//获取课堂评价维度
				}
				if(empty($row['lecture_info']['dimension'])){
					$row['dimension'] = $this->dao->getAll('SELECT id,title FROM '.$this->vp_comment_dimension);//获取课堂评价维度
				}
			}


			if(!empty($row['lesson_record_img'])){
				$row['lesson_record_img'] = explode('|',trim($row['lesson_record_img'],'|'));
			}
		}
		return $row;
	}


	public function get_questionList($question_id_arr){
		$optionKeyArr = C('OPTIONS_KEY');
		$list = array();
		if(!empty($question_id_arr)){
			foreach ($question_id_arr as $key=>$question_id){
				$list[] = $this->dao2->getRow('SELECT q.id,
											q.content,
											q.analysis,
											q.knowledge_id,
											q.difficulty,
											k.name as knowledge_name,
											a.content as answer_content,
											k.parent_id as knowledge_parent_id,
											p.name as knowledge_parent_name   
											FROM '.$this->vip_question.' q 
											LEFT JOIN '.$this->vip_question_answer.' a ON q.id = a.question_id AND a.`status` = 1  
											LEFT JOIN '.$this->vip_knowledge.' k ON k.id = q.knowledge_id 
											LEFT JOIN '.$this->vip_knowledge.' p ON k.parent_id = p.id  
											WHERE q.parent_id = 0 AND q.id = '.$this->dao->quote($question_id));
			}
			if(!empty($list)){
				$options = $this->getOptionsByQuestionIds(VipCommAction::arr2str($question_id_arr));
				foreach ($list as $key=>$row){
					$questionOptions = array ();
					$answerContent = '';
					foreach ( $options as $k=>$option ) {
						if ($option ['question_id'] == $list [$key] ['id']) {
							$option ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $option ['content'] );
							$questionOptions [] = $option;
						}
					}
					$list [$key] ['question_option'] = $questionOptions;
					if(!empty($list [$key] ['question_option'])){
						foreach ($list [$key] ['question_option'] as $k=>$option){
							if($option['is_answer']==1){
								$list[$key]['answer_content'] .= $optionKeyArr[$k];
							}
						}
					}

				}
			}
		}

		return $list;

	}


	public function get_questionList2($question_id_arr){
		$optionKeyArr = C('OPTIONS_KEY');
		$list = array();
		$questionIdStr = VipCommAction::arr2str($question_id_arr);
		if(!empty($question_id_arr)){
			$questionList = $this->dao2->getAll('SELECT q.id,
												q.content,
												q.analysis,
												q.knowledge_id,
												q.difficulty,
												k.name as knowledge_name,
												a.content as answer_content,
												k.parent_id as knowledge_parent_id,
												p.name as knowledge_parent_name   
												FROM '.$this->vip_question.' q 
												LEFT JOIN '.$this->vip_question_answer.' a ON q.id = a.question_id AND a.`status` = 1  
												LEFT JOIN '.$this->vip_knowledge.' k ON k.id = q.knowledge_id 
												LEFT JOIN '.$this->vip_knowledge.' p ON k.parent_id = p.id  
												WHERE q.parent_id = 0 AND q.id IN ('.$questionIdStr.')');
			foreach ($question_id_arr as $key=>$question_id){
				foreach ($questionList as $k=>$question){
					if($question_id == $question['id']){
						$list[] = $question;
					}
				}
			}
			if(!empty($list)){
				$options = $this->getOptionsByQuestionIds($questionIdStr);
				foreach ($list as $key=>$row){
					$questionOptions = array ();
					$answerContent = '';
					foreach ( $options as $k=>$option ) {
						if ($option ['question_id'] == $list [$key] ['id']) {
							$option ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $option ['content'] );
							$questionOptions [] = $option;
						}
					}
					$list [$key] ['question_option'] = $questionOptions;
					if(!empty($list [$key] ['question_option'])){
						foreach ($list [$key] ['question_option'] as $k=>$option){
							if($option['is_answer']==1){
								$list[$key]['answer_content'] .= $optionKeyArr[$k];
							}
						}
					}
				}
			}
		}
		return $list;
	}


	public function getOptionsByQuestionIds($question_id_str){
		return $this->dao2->getAll('SELECT id,content, is_answer,sort,question_id  FROM '.$this->vip_question_option.'  WHERE `status` = 1 AND question_id IN  ('.$question_id_str.') ORDER BY question_id ASC,sort ASC');
	}


	public function get_lastHeluId($helu_info){
		return $this->dao->getOne('SELECT TOP 1 id FROM '.$this->view_VB_StudentLessonHeLu.' WHERE nStatus!=3 AND sStudentCode = '.$this->dao->quote($helu_info['sstudentcode']).' AND sKeChengCode = '.$this->dao->quote($helu_info['skechengcode']).' AND sTeacherCode = '.$this->dao->quote($helu_info['steachercode']).' AND dtLessonBeginReal < '.$this->dao->quote($helu_info['dtlessonbeginreal']).' ORDER BY dtLessonBeginReal DESC ');
	}




	public function recordLessonTrack($arr){
		if(!empty($arr)){
			$count = $this->getHeluCount($arr['helu_id']);;
			$count2 = $this->getHeluCount($arr['last_helu_id']);
			if($count > 0){
				$strQuery = 'UPDATE '.$this->vp_kechenghelu.' SET student_code = '.$this->dao->quote($arr['student_code']).',
															  student_name = '.$this->dao->quote($arr['student_name']).',
															  kecheng_code = '.$this->dao->quote($arr['kecheng_code']).',
															  lesson_no = '.$this->dao->quote($arr['lesson_no']).',
															  lesson_date = '.$this->dao->quote($arr['lesson_date']).',
															  lesson_begin = '.$this->dao->quote($arr['lesson_begin']).',
															  lesson_end = '.$this->dao->quote($arr['lesson_end']).',
															  lesson_topic = '.$this->dao->quote($arr['lesson_topic']).',
															  lasttime = '.$this->dao->quote(date('Y-m-d H:i:s')).', 
															  module_answer = '.$this->dao->quote(serialize(explode('|',trim($arr['module_answer'],'|')))).', 
															  practise_answer = '.$this->dao->quote(serialize(explode('|',trim($arr['practise_answer'],'|')))).'  
															  WHERE helu_id = '.$this->dao->quote($arr['helu_id']);
			}else{
				$strQuery = 'INSERT INTO '.$this->vp_kechenghelu.' (helu_id,
																	kecheng_code,
																	lesson_no,
																	student_code,
																	student_name,
																	lesson_date,
																	lesson_begin,
																	lesson_end,
																	lesson_topic,
																	lasttime,
																	module_answer,
																	practise_answer 
																) 
														VALUES( '.$this->dao->quote($arr['helu_id']).',
																'.$this->dao->quote($arr['kecheng_code']).',
																'.$this->dao->quote($arr['lesson_no']).',
																'.$this->dao->quote($arr['student_code']).',
																'.$this->dao->quote($arr['student_name']).',
																'.$this->dao->quote($arr['lesson_date']).',
																'.$this->dao->quote($arr['lesson_begin']).',
																'.$this->dao->quote($arr['lesson_end']).',
																'.$this->dao->quote($arr['lesson_topic']).',
																'.$this->dao->quote(date('Y-m-d H:i:s')).',
																'.$this->dao->quote(serialize(explode('|',trim($arr['module_answer'],'|')))).',
																'.$this->dao->quote(serialize(explode('|',trim($arr['practise_answer'],'|')))).'  
														       )';
			}
			if($count2 > 0){
				$strQuery2 = 'UPDATE '.$this->vp_kechenghelu.' SET work_answer = '.$this->dao->quote(serialize(explode('|',trim($arr['lastwork_answer'],'|')))).' WHERE helu_id = '.$this->dao->quote($arr['last_helu_id']);
			}else{
				$lastLessonInfo = $this->dao->getRow('SELECT [id] as helu_id,
															 [sKeChengCode] as sKeChengCode,
															 [sStudentCode] as sStudentCode,
															 [nLessonNo] as nLessonNo,
															 [sStudentName] as sStudentName,
															 [sKeChengName] as sKeChengName,
															 convert(varchar(20),[dtDateReal],120) as dtDateReal,
															 convert(varchar(20),[dtLessonBeginReal],120) as dtLessonBeginReal,
												 			 convert(varchar(20),[dtLessonEndReal],120) as dtLessonEndReal 
												 			 FROM '.$this->view_VB_StudentLessonHeLu.' WHERE id = '.$this->dao->quote($arr['last_helu_id']));
				$strQuery2 = 'INSERT INTO '.$this->vp_kechenghelu.' (helu_id,
																	 kecheng_code,
																	 lesson_no,
																	 student_code,
																	 student_name,
																	 lesson_date,
																	 lesson_begin,
																	 lesson_end,
																	 lasttime,
																	 work_answer) 
															VALUES ('.$this->dao->quote($arr['last_helu_id']).',
																	'.$this->dao->quote($lastLessonInfo['skechengcode']).',
																	'.$this->dao->quote($lastLessonInfo['nlessonno']).',
																	'.$this->dao->quote($lastLessonInfo['sstudentcode']).',
																	'.$this->dao->quote($lastLessonInfo['sstudentname']).',
																	'.$this->dao->quote($lastLessonInfo['dtdatereal']).',
																	'.$this->dao->quote(date('H:i',strtotime($lastLessonInfo['dtlessonbeginreal']))).',
																	'.$this->dao->quote(date('H:i',strtotime($lastLessonInfo['dtlessonendreal']))).',
																	'.$this->dao->quote(date('Y-m-d H:i:s')).',
																	'.$this->dao->quote(serialize(explode('|',trim($arr['lastwork_answer'],'|')))).')';
			}

			//记录错题进书包
			//$heluInfo = $this->get_heluInfo($arr['helu_id']);
			//$lastHeluInfo = $this->get_heluInfo($arr['last_helu_id']);
			$heluInfo = VipStudentsAction::getHeluInfo($arr['helu_id']);
			$lastHeluInfo = VipStudentsAction::getHeluInfo($arr['last_helu_id']);
			$count4 = $this->dao->getOne('SELECT COUNT(1) FROM '.$this->vp_student_error_questions.' WHERE helu_id = '.$this->dao->quote($arr['helu_id']));
			if($count4>0){
				$strQuery4 = 'DELETE FROM '.$this->vp_student_error_questions.' WHERE helu_id = '.$this->dao->quote($arr['helu_id']).';';
			}else{
				$strQuery4 ='';
			}
			if(!empty($heluInfo['lecture_id'])){
				$module_answer = explode('|',trim($arr['module_answer'],'|'));
				if(!empty($module_answer)){
					foreach ($module_answer as $key=>$m){
						if($m === '0' || $m === '1'){
							/*$tempCount = $this->dao->getOne('SELECT COUNT(1) FROM '.$this->vp_student_error_questions.'
							WHERE student_code = '.$this->dao->quote($heluInfo['sstudentcode']).'
							AND helu_id = '.$this->dao->quote($arr['helu_id']).'
							AND question_id = '.$this->dao->quote($heluInfo['lecture_info']['question_list']['module_question'][$key]).'
							AND type = 1 ');*/
							//if($tempCount==0){
							$strQuery3 .= 'INSERT  INTO '.$this->vp_student_error_questions.' (student_code,
																						  helu_id, 
																						  question_id,
																						  type) 
																				  VALUES ('.$this->dao->quote($heluInfo['sstudentcode']).',
																				          '.$this->dao->quote($arr['helu_id']).',
																				          '.$this->dao->quote($heluInfo['lecture_info']['question_list']['module_question'][$key]).',
																				          1);';
							//}

						}
					}
				}
				$practise_answer = explode('|',trim($arr['practise_answer'],'|'));
				if(!empty($practise_answer)){
					foreach ($practise_answer as $key=>$p){
						if($p === '0' || $p === '1'){
							/*$tempCount = $this->dao->getOne('SELECT COUNT(1) FROM '.$this->vp_student_error_questions.'
							WHERE student_code = '.$this->dao->quote($heluInfo['sstudentcode']).'
							AND helu_id = '.$this->dao->quote($arr['helu_id']).'
							AND question_id = '.$this->dao->quote($heluInfo['lecture_info']['question_list']['practise'][$key]['id']).'
							AND type = 2 ');*/
							//if($tempCount==0){
							$strQuery3 .= 'INSERT  INTO '.$this->vp_student_error_questions.' (student_code,
																						  helu_id, 
																						  question_id,
																						  type) 
																				  VALUES ('.$this->dao->quote($heluInfo['sstudentcode']).',
																				          '.$this->dao->quote($arr['helu_id']).',
																				          '.$this->dao->quote($heluInfo['lecture_info']['question_list']['practise'][$key]['id']).',
																				          2);';
							//}

						}
					}
				}
			}

			if(!empty($lastHeluInfo['lecture_id'])){
				//$strQuery3 .= 'DELETE FROM '.$this->vp_student_error_questions.' WHERE helu_id = '.$this->dao->quote($arr['last_helu_id']).';';
				$last_work_answer = explode('|',trim($arr['lastwork_answer'],'|'));
				if(!empty($last_work_answer)){
					foreach ($last_work_answer as $key=>$w){
						if($w === '0' || $w === '1'){
							/*$tempCount = $this->dao->getOne('SELECT COUNT(1) FROM '.$this->vp_student_error_questions.'
							WHERE student_code = '.$this->dao->quote($lastHeluInfo['sstudentcode']).'
							AND helu_id = '.$this->dao->quote($arr['last_helu_id']).'
							AND question_id = '.$this->dao->quote($lastHeluInfo['lecture_info']['question_list']['work'][$key]['id']).'
							AND type = 3 ');*/
							//if($tempCount==0){
							$strQuery3 .= 'INSERT  INTO '.$this->vp_student_error_questions.' (student_code,
																						  helu_id, 
																						  question_id,
																						  type) 
																				  VALUES ('.$this->dao->quote($lastHeluInfo['sstudentcode']).',
																				          '.$this->dao->quote($arr['last_helu_id']).',
																				          '.$this->dao->quote($lastHeluInfo['lecture_info']['question_list']['work'][$key]['id']).',
																				          3);';
							//}

						}
					}
				}
			}

			$this->dao->begin();
			$success = (boolean)$this->dao->execute($strQuery);
			$success2 = (boolean)$this->dao->execute($strQuery2);
			if(!empty($strQuery4)){
				$success4 = (boolean)$this->dao->execute($strQuery4);
			}else{
				$success4 =  true;
			}
			if(!empty($strQuery3)){
				$success3 = (boolean)$this->dao->execute($strQuery3);
			}else{
				$success3 =  true;
			}

			if($success == true && $success2 == true && $success3 == true && $success4 == true){
				$this->dao->commit();
				return true;
			}
			$this->dao->rollback();
			return false;

		}
		return false;
	}


	public function getHeluCount($helu_id){
		return $this->dao->getOne('SELECT COUNT(1) FROM '.$this->vp_kechenghelu.' WHERE helu_id = '.$this->dao->quote($helu_id));
	}


	public function recordLessonComment($arr){
		$count = $this->getHeluCount($arr['helu_id']);
		$dimension_arr = explode('|',trim($_POST['dimension_id_str'],'|'));
		$dimension_title_arr = explode('|',trim($_POST['dimension_title_str'],'|'));
		$level_arr = explode('|',trim($_POST['level_str'],'|'));
		$dimension_level_arr = array();
		if(!empty($dimension_arr) && !empty($level_arr) ){
			foreach ($dimension_arr as $key=>$dimension){
				$dimension_level_arr[$key]['id'] = $dimension;
				$dimension_level_arr[$key]['level'] = $level_arr[$key];
				$dimension_level_arr[$key]['title'] = $dimension_title_arr[$key];
			}

			if($count >0){
				$strQuery = 'UPDATE '.$this->vp_kechenghelu.' SET student_code = '.$this->dao->quote($arr['student_code']).',
															  student_name = '.$this->dao->quote($arr['student_name']).',
															  kecheng_code = '.$this->dao->quote($arr['kecheng_code']).',
															  lesson_no = '.$this->dao->quote($arr['lesson_no']).',
															  lesson_date = '.$this->dao->quote($arr['lesson_date']).',
															  lesson_begin = '.$this->dao->quote($arr['lesson_begin']).',
															  lesson_end = '.$this->dao->quote($arr['lesson_end']).',
															  comment = '.$this->dao->quote($arr['comment']).',
															  dimension_level = '.$this->dao->quote(serialize($dimension_level_arr)).', '; 
				if($arr['is_send_sms'] == 1){
					$strQuery .= ' is_send_sms = '.$this->dao->quote($arr['is_send_sms']).', ';
				}
				$strQuery .= 'lasttime = '.$this->dao->quote(date('Y-m-d H:i:s')).'  WHERE helu_id = '.$this->dao->quote($arr['helu_id']);
			}else{
				$strQuery = 'INSERT INTO '.$this->vp_kechenghelu.' (helu_id,
																	kecheng_code,
																	lesson_no,
																	student_code,
																	student_name,
																	lesson_date,
																	lesson_begin,
																	lesson_end,
																	comment,
																	dimension_level,
																	is_send_sms,
																	lasttime 
																) 
														VALUES( '.$this->dao->quote($arr['helu_id']).',
																'.$this->dao->quote($arr['kecheng_code']).',
																'.$this->dao->quote($arr['lesson_no']).',
																'.$this->dao->quote($arr['student_code']).',
																'.$this->dao->quote($arr['student_name']).',
																'.$this->dao->quote($arr['lesson_date']).',
																'.$this->dao->quote($arr['lesson_begin']).',
																'.$this->dao->quote($arr['lesson_end']).',
																'.$this->dao->quote($arr['comment']).',
																'.$this->dao->quote(serialize($dimension_level_arr)).',
																'.$this->dao->quote($arr['is_send_sms']).',
																'.$this->dao->quote(date('Y-m-d H:i:s')).' 
																  
														       )';
			}

			if($this->dao->execute($strQuery)){
				return true;
			}
			return false;
		}
		return false;
	}




	public function uploadRecordImg($helu_id, $imgUrl){
		if(!empty($helu_id) && !empty($imgUrl)){
			$helu_imgUrl = $this->dao->getOne('SELECT lesson_record_img FROM '.$this->vp_kechenghelu.' WHERE helu_id = '.$this->dao->quote($helu_id));
			$helu_fileUrl = $this->dao->getOne('SELECT url FROM '.$this->vp_kechenghelu_files.' WHERE type = 0 AND helu_id = '.$this->dao->quote($helu_id));
			$new_imgUrl = $helu_imgUrl.$imgUrl.'|';
			$new_fileUrl = $helu_fileUrl.$imgUrl.'|';
			$strQuery = 'UPDATE '.$this->vp_kechenghelu.' SET lesson_record_img = '.$this->dao->quote($new_imgUrl).' WHERE helu_id = '.$this->dao->quote($helu_id);
			$strQuery2 = 'UPDATE '.$this->vp_kechenghelu_files.' SET url = '.$this->dao->quote($new_fileUrl).' WHERE type=0 AND helu_id = '.$this->dao->quote($helu_id);
			$this->dao->begin();
			$success = (boolean)$this->dao->execute($strQuery);
			$success2 = (boolean)$this->dao->execute($strQuery2);
			if($success == true && $success2 == true){
				$this->dao->commit();
				return true;
			}
			$this->dao->rollback();
			return false;
		}
		return false;
	}



	public function deleteRecordImg($helu_id, $imgUrl){
		if(!empty($helu_id) && !empty($imgUrl)){
			$helu_imgUrl = $this->dao->getOne('SELECT lesson_record_img FROM '.$this->vp_kechenghelu.' WHERE helu_id = '.$this->dao->quote($helu_id));
			$new_imgUrl = str_replace($imgUrl.'|','',$helu_imgUrl);
			if($this->dao->execute('UPDATE '.$this->vp_kechenghelu.' SET lesson_record_img = '.$this->dao->quote($new_imgUrl).' WHERE helu_id = '.$this->dao->quote($helu_id))){
				return true;
			}
			return false;
		}
		return false;
	}



	public function getRandTemplateId($arr){
		if(!empty($arr['sid'])){
			return $this->dao->getOne('SELECT TOP 1 *  FROM  '.$this->vp_words.' WHERE sid = '.$this->dao->quote($arr['sid']).' ORDER BY NewID()');
		}
		return false;
	}


	public function getKnowledgeCloud($heluInfo){
		$before_heluIdArr = $this->dao->getAll('SELECT TOP 3 id FROM '.$this->view_VB_StudentLessonHeLu.' WHERE nStatus!=3 AND dtLessonBeginReal < '.$this->dao->quote($heluInfo['dtlessonbeginreal']).' AND sStudentCode = '.$this->dao->quote($heluInfo['sstudentcode']).' AND sKeChengCode = '.$this->dao->quote($heluInfo['skechengcode']).' AND steacherCode = '.$this->dao->quote($heluInfo['steachercode']).' ORDER BY [dtLessonBeginReal] DESC');
		$after_heluIdArr = $this->dao->getAll('SELECT TOP 1 id FROM '.$this->view_VB_StudentLessonHeLu.' WHERE nStatus!=3 AND dtLessonBeginReal > '.$this->dao->quote($heluInfo['dtlessonbeginreal']).' AND sStudentCode = '.$this->dao->quote($heluInfo['sstudentcode']).' AND sKeChengCode = '.$this->dao->quote($heluInfo['skechengcode']).' AND steacherCode = '.$this->dao->quote($heluInfo['steachercode']).' ORDER BY dtLessonBeginReal ASC');
		$heluIdArr = array();
		if(!empty($before_heluIdArr)){
			foreach ($before_heluIdArr as $key=>$before){
				$heluIdArr[] = $before['id'];
			}
		}
		krsort($heluIdArr);
		$heluIdArr[] = $heluInfo['helu_id'];
		if(!empty($after_heluIdArr)){
			foreach ($after_heluIdArr as $key=>$after){
				$heluIdArr[] = $after['id'];
			}
		}

		$cloudArr = array();
		if(!empty($heluIdArr)){
			foreach ($heluIdArr as $key=>$heluId){
				if($heluId == $heluInfo['helu_id']){
					$cloudArr[] = $heluInfo;
				}else{
					//$cloudArr[] = $this->get_heluInfo($heluId);
					$cloudArr[] = VipStudentsAction::getHeluInfo($heluId);
				}

			}
		}

		return $cloudArr;
	}


	public function recordReportUrl($helu_id, $report_url,$report_url_wx,$report_img,$type){
		if(!empty($helu_id) && !empty($report_url)){
			$strQuery = 'UPDATE '.$this->vp_kechenghelu.' SET lesson_report_url = '.$this->dao->quote($report_url).',lesson_report_url_wx = '.$this->dao->quote($report_url_wx).',lesson_report_img = '.$this->dao->quote($report_img);
			if($type == 0){
				$strQuery .= ' ,lesson_report_createtime='.$this->dao->quote(date('Y-m-d H:i:s'));
			}
			$strQuery .= ' WHERE helu_id = '.$this->dao->quote($helu_id);
			if($this->dao->execute($strQuery)){
				return true;
			}
			return false;
		}
		return false;
	}


	public function clearLecture($helu_id){
		if(!empty($helu_id)){
			$heluInfo = $this->get_heluInfo($helu_id,0);
			$last_helu_id = $this->get_lastHeluId($heluInfo);
			//$last_heluInfo = $this->get_heluInfo($last_helu_id);
			$last_heluInfo = VipStudentsAction::getHeluInfo($last_helu_id);
			$count = $this->dao->getOne('SELECT COUNT(1) FROM '.$this->vp_student_error_questions.' WHERE helu_id = '.$this->dao->quote($helu_id));
			$strQuery = 'UPDATE '.$this->vp_kechenghelu_files.' SET url = NULL,lecture_id = NULL WHERE type = 0 AND helu_id = '.$this->dao->quote($helu_id);
			$strQuery2 = 'UPDATE '.$this->vp_kechenghelu.' SET lesson_topic=NULL,comment=NULL,[is_send_sms] = 0,lesson_report_url=NULL,lesson_report_createtime= NULL,module_answer=NULL,practise_answer=NULL,work_answer=NULL,dimension_level=NULL,lesson_record_img=NULL WHERE helu_id = '.$this->dao->quote($helu_id);
			$strQuery3 = 'DELETE FROM '.$this->vp_student_error_questions.' WHERE helu_id = '.$this->dao->quote($helu_id);
			if(!empty($last_heluInfo['work_answer'])){
				$strQuery4 = 'UPDATE '.$this->vp_kechenghelu.' SET work_answer=NULL WHERE helu_id = '.$this->dao->quote($last_helu_id);
				$strQuery5 = '';
				$last_work_question = $last_heluInfo['lecture_info']['question_list']['work'];
				foreach ($last_heluInfo['work_answer'] as $k=>$answer){
					if($answer == '1' || $answer == '0'){
						$strQuery5 .= 'DELETE FROM '.$this->vp_student_error_questions.' WHERE helu_id = '.$this->dao->quote($last_helu_id).' AND question_id = '.$this->dao->quote($last_work_question[$k]['id']).';';
					}
				}
			}

			$this->dao->begin();
			$success = (boolean)$this->dao->execute($strQuery);
			$success2 = (boolean)$this->dao->execute($strQuery2);
			if($count>0){
				$success3 = (boolean)$this->dao->execute($strQuery3);
			}else{
				$success3 =  true;
			}
			if($strQuery4){
				$success4 = (boolean)$this->dao->execute($strQuery4);
			}else{
				$success4 =  true;
			}
			if($strQuery5){
				$success5 = (boolean)$this->dao->execute($strQuery5);
			}else{
				$success5 =  true;
			}
			if($success == true && $success2 == true && $success3 == true && $success4 == true && $success5 == true){
				$this->dao->commit();
				//删除课节报告单文件
				if(file_exists(APP_DIR.$heluInfo['lesson_report_url'])){
					@unlink(APP_DIR.$heluInfo['lesson_report_url']);
				}
				//删除课节轨照
				if(!empty($heluInfo['lesson_record_img'])){
					$temp_file_arr = explode('|',$heluInfo['lesson_record_img']);
					if(!empty($temp_file_arr)){
						foreach ($temp_file_arr as $k=>$file){
							if(file_exists(APP_DIR.$file)){
								@unlink(APP_DIR.$file);
							}
						}
					}
				}

				return true;
			}
			$this->dao->rollback();
			return false;
		}
		return false;
	}



	public function get_lectureInfo($lectureId){
		return $this->dao2->getRow('SELECT  id,
															sid, 
															subject_id, 
															course_type_id, 
															knowledge_id, 
															title, 
															difficulty, 
															grades, 
															cart, 
															config, 
															status, 
															user_name, 
															FROM_UNIXTIME( created_time, \'%Y-%m-%d\' ) AS created_date, 
															fn_vip_get_grade_name(grades) AS grade_names, 
															last_updated_user_name, 
															FROM_UNIXTIME( last_updated_date, \'%Y-%m-%d\' ) AS last_updated_date,
															remark,
															visit_num,
															is_public 
															FROM '.$this->vip_teacher_lecture.' 
															WHERE id = '.$this->dao2->quote($lectureId));
	}



	public function get_kechengAll($arr){
		if(!empty($arr['student_code'])){
			$strQuery = 'select [sKeChengCode],
								max([sKeChengName]) as sKeChengName 
								FROM '.$this->view_VB_StudentLessonHeLu.'  
								WHERE [nStatus] != 3 AND [dtLessonBeginReal] > '.$this->dao->quote(C('PIV_START')).' AND [sStudentCode] = '.$this->dao->quote($arr['student_code']).' GROUP BY [sKeChengCode]';

			return $this->dao->getAll($strQuery);
		}
		return array();
	}

	public function get_lessonAll($arr){
		if(!empty($arr['student_code']) && !empty($arr['kecheng_code'])){
			$strQuery = 'select [id],
								convert(varchar(20),[dtDateReal],120) as dtDateReal,
								convert(varchar(20),[dtLessonBeginReal],120) as dtLessonBeginReal,
								convert(varchar(20),[dtLessonEndReal],120) as dtLessonEndReal 
								FROM '.$this->view_VB_StudentLessonHeLu.' 
								WHERE [nStatus] != 3  AND [dtLessonBeginReal] > '.$this->dao->quote(C('PIV_START')).' AND [dtLessonBeginReal] <= '.$this->dao->quote(date('Y-m-d H:i:s')).' AND [sStudentCode] = '.$this->dao->quote($arr['student_code']).' AND [sKeChengCode] = '.$this->dao->quote($arr['kecheng_code']).' ORDER BY dtLessonBeginReal ASC';

			return $this->dao->getAll($strQuery);
		}
		return array();
	}




	//学员app端错题包接口
	public function  get_errorQuestionList_api($arr, $type=0, $currentPage=1, $pageSize=20){
		$optionKeyArr = C('OPTIONS_KEY');
		if($type == 0){
			$count = $this->get_errorQuestionCount($arr);
			$pageCount = ceil($count / $pageSize);
			if($currentPage > $pageCount) $currentPage = $pageCount;
			if($currentPage < 1) $currentPage = 1;
		}
		$strQuery = 'select  eq.[id],
							 eq.[student_code],
							 eq.[helu_id],
							 eq.[question_id],
							 eq.[type],
							 view_helu.[sKeChengCode],
							 view_helu.[sKeChengName],  
							 convert(varchar(20),view_helu.[dtDateReal],111) as dtDateReal,
							 convert(varchar(20),view_helu.[dtLessonBeginReal],120) as dtLessonBeginReal,
							 convert(varchar(20),view_helu.[dtLessonEndReal],120) as dtLessonEndReal,
							 vp_helu.[lesson_topic] 
							 FROM '.$this->vp_student_error_questions.' as eq  
							 LEFT JOIN  '.$this->view_VB_StudentLessonHeLu.' as view_helu ON eq.[helu_id] = view_helu.[id] 
							 LEFT JOIN  '.$this->vp_kechenghelu.' as vp_helu ON eq.[helu_id] = vp_helu.[helu_id]  
							 WHERE  eq.question_id > 0 ';
		if(!empty($arr['teacherCode'])) {
			$strQuery .=  ' AND view_helu.[steacherCode] = '.$this->dao->quote($arr['teacherCode']);
		}
		if(!empty($arr['student_code'])) {
			$strQuery .=  ' AND eq.[student_code] = '.$this->dao->quote($arr['student_code']);
		}
		if(!empty($arr['start'])) {
			$strQuery .=  ' AND view_helu.dtLessonBeginReal >= '.$this->dao->quote($arr['start']);
		}
		if(!empty($arr['end'])) {
			$strQuery .=  ' AND view_helu.dtLessonBeginReal <= '.$this->dao->quote(date('Y-m-d ',strtotime($arr['end'])).'23:59:59');
		}
		if(!empty($arr['lesson_topic'])) {
			$strQuery .=  ' AND vp_helu.[lesson_topic] LIKE  '.$this->dao->quote("%".$arr['lesson_topic']."%");
		}
		if(!empty($arr['helu_id'])) {
			$strQuery .=  ' AND eq.[helu_id] ='.$this->dao->quote($arr['helu_id']);
		}
		$order = ' ORDER BY [id] DESC ';
		if($type == 1){
			$list = $this->dao->getAll($strQuery.$order);
		}else{
			$list = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		}

		if(!empty($list)){
			foreach ($list as $key=>$val){
				if(!empty($val['question_id'])){
					$list[$key]['question_desc'] = $this->dao2->getRow('SELECT q.content,
																		   q.analysis,
																		   a.content as answer_content 
																		   FROM '.$this->vip_question.' q 
																		   LEFT JOIN '.$this->vip_question_answer.' a ON q.id = a.question_id AND a.`status` = 1  
																		   WHERE q.parent_id = 0 AND q.id = '.$this->dao->quote($val['question_id']));
					if(empty($list[$key]['question_desc'])){
						$list[$key]['question_desc']['content'] = '';
						$list[$key]['question_desc']['analysis'] = '';
						$list[$key]['question_desc']['answer_content'] = '';
					}
					$list[$key]['question_option'] = $this->dao2->getAll('SELECT id,
																			 content,
																			 is_answer,
																			 sort  
																		   	 FROM '.$this->vip_question_option.'  
																		     WHERE `status` = 1 AND question_id = '.$this->dao->quote($val['question_id']).' ORDER BY sort ASC');

					if(!empty($list[$key]['question_option'])){
						$list[$key]['question_desc']['answer_content'] = '';
						foreach ($list[$key]['question_option'] as $k=>$v){
							$list[$key]['question_option'][$k]['content'] = str_replace("\r\n"," ",$v['content']);
							if($v['is_answer'] == 1){
								$list[$key]['question_desc']['answer_content'] .= $optionKeyArr[$k];
							}
						}
					}
					$list[$key]['question_desc']['content'] = str_replace("\r\n"," ",$list[$key]['question_desc']['content']);
					$list[$key]['question_desc']['analysis'] = str_replace("\r\n"," ",$list[$key]['question_desc']['analysis']);
					$list[$key]['question_desc']['answer_content'] = str_replace("\r\n"," ",$list[$key]['question_desc']['answer_content']);
				}else{
					unset($list[$key]);
				}
			}
		}
		return $list;
	}



	public function getReportImgByHeluId($helu_id){
		if(!empty($helu_id)){
			return $this->dao->getOne('SELECT lesson_report_img FROM '.$this->vp_kechenghelu.' WHERE helu_id = '.$this->dao->quote($helu_id));
		}
		return false;
	}





	/*获取教师下某学员未提交的测辅记录*/
	public function get_testCoachAll($arr){
		if(!empty($arr['teacher_code'])){
			$strQuery = 'SELECT tc_mbo.*,sub.sName as subjectname,s.sName as studentname FROM '.$this->V_Teacher_TestCoachMBONew.' tc_mbo LEFT JOIN '.$this->V_D_Subject.' sub ON tc_mbo.sSubjectCode =  sub.sCode LEFT JOIN '.$this->BS_Student.' s ON tc_mbo.sStudentCode =  s.sCode WHERE tc_mbo.bValid = 1 AND tc_mbo.nAudit = 1 AND tc_mbo.nTestCoachType = 2  AND tc_mbo.sTeacherCode = '.$this->dao->quote($arr['teacher_code']).' AND tc_mbo.nIsSubmit = '.$this->dao->quote($arr['submit']);
			if(!empty($arr['student_code'])){
				$strQuery .= ' AND tc_mbo.sStudentCode = '.$this->dao->quote($arr['student_code']);
			}
			return $this->dao->getAll($strQuery);
		}
		return false;
	}



	public function get_testCoachInfo($testCoachId){
		if(!empty($testCoachId)){
			$strQuery = 'SELECT tc_mbo.*,sub.sName as subjectname FROM '.$this->V_Teacher_TestCoachMBONew.' tc_mbo LEFT JOIN '.$this->V_D_Subject.' sub ON tc_mbo.sSubjectCode =  sub.sCode WHERE tc_mbo.id = '.$this->dao->quote($testCoachId);
			return $this->dao->getRow($strQuery);
		}
		return false;
	}


	public function getLessonCloudHeluId($arr){
		$strQuery = 'SELECT id FROM '.$this->view_VB_StudentLessonHeLu.' WHERE nStatus !=3 AND dtLessonBeginReal >= '.$this->dao->quote($arr['start']).' AND dtLessonBeginReal <= '.$this->dao->quote($arr['end']).' AND sStudentCode = '.$this->dao->quote($arr['student_code']).' AND sSubjectCode = '.$this->dao->quote($arr['subject_code']).' AND steacherCode = '.$this->dao->quote($arr['teacher_code']).' ORDER BY [dtLessonBeginReal] DESC';
		return $this->dao->getAll($strQuery);
	}


	public function get_programLevelList(){
		return $this->dao->getAll('SELECT * FROM '.$this->vp_program_level);
	}


	public function get_programDimensionList(){
		$list = $this->dao->getAll('SELECT * FROM '.$this->vp_program_dimension);
		if(!empty($list)){
			foreach ($list as $key=>$row){
				$list[$key]['text'] = $this->get_dimensionTextByDimensionId($row['id']);
			}
		}
		return $list;
	}

	public function get_dimensionTextByDimensionId($dimension_id){
		return $this->dao->getAll('SELECT text FROM '.$this->vp_program_text.' WHERE dimension_id = '.$this->dao->quote($dimension_id).' ORDER BY level_id ASC');
	}

	public function get_simpleHeluInfo($helu_id,$type = 1 ){
		$row = $this->dao->getRow('SELECT helu.*,
										 convert(varchar(20),helu.lesson_report_createtime,120) as lesson_report_createtime,
										 f.lecture_id,
										 view_helu.id as helu_id,
										 view_helu.[sKeChengCode] as sKeChengCode,
										 view_helu.[sStudentCode] as sStudentCode,
										 view_helu.[sTeacherCode] as sTeacherCode,
										 view_helu.[nLessonNo] as nLessonNo,
										 view_helu.[sStudentName] as sStudentName,
										 view_helu.[sKeChengName] as sKeChengName,
										 view_helu.[sClassAdviserName] as sClassAdviserName,
										 convert(varchar(20),view_helu.[dtDateReal],120) as dtDateReal,
										 convert(varchar(20),view_helu.[dtLessonBeginReal],120) as dtLessonBeginReal,
							 			 convert(varchar(20),view_helu.[dtLessonEndReal],120) as dtLessonEndReal 
										 FROM '.$this->view_VB_StudentLessonHeLu.' view_helu 
										 LEFT JOIN '.$this->vp_kechenghelu.' helu ON helu.helu_id = view_helu.id  
										 LEFT JOIN '.$this->vp_kechenghelu_files.' f ON  f.helu_id = view_helu.id AND f.type = 0 
										 WHERE view_helu.id = '.$this->dao->quote($helu_id));

		if(!empty($row) && !empty($row['lecture_id']) && $type==1){
			$row['lecture_info'] = $this->dao2->getRow('SELECT le.*,s.title as subject_name,s.eap_subject_id as eap_subject_id,g.title as grade_name,le.created_time as created_time FROM '.$this->vip_teacher_lecture.' le LEFT JOIN '.$this->vip_dict_subject.' s ON le.subject_id = s.id LEFT JOIN '.$this->vip_dict_grade.' g ON s.grade_id = g.id WHERE le.id = '.$this->dao2->quote($row['lecture_id']));
			if(!empty($row['lecture_info'])){
				$row['lecture_info']['cart'] = unserialize($row['lecture_info']['cart']);
				$row['lecture_info']['config'] = unserialize($row['lecture_info']['config']);
				$row['lecture_info']['question_list']['module'] = $row['lecture_info']['config']['struct']['body'][$row['lecture_info']['cart']['cart']['sort']]['types'];
				if(!empty($row['lecture_info']['question_list']['module'])){
					foreach ($row['lecture_info']['question_list']['module'] as $key=>$type){
						if(!empty($row['lecture_info']['cart']['cart']['question_rs'][$row['lecture_info']['cart']['cart']['sort']])){
							foreach ($row['lecture_info']['cart']['cart']['question_rs'][$row['lecture_info']['cart']['cart']['sort']] as $k=>$v){
								if($k == $type['id']){
									//$row['lecture_info']['question_list']['module'][$key]['question_list'] = $this->get_questionList($v);
									$row['lecture_info']['question_list']['module'][$key]['question_list'] = $this->get_simpleQuestionList($v);
									foreach ($v as $kk=>$val){
										$row['lecture_info']['question_list']['module_question'][] = $val;
									}
								}
							}
						}
					}
				}

				$practise_question_id_arr = array();
				if(!empty($row['lecture_info']['cart']['cart']['question_rs']['practise'])){
					foreach ($row['lecture_info']['cart']['cart']['question_rs']['practise'] as $key=>$val){
						$practise_question_id_arr = $row['lecture_info']['cart']['cart']['question_rs']['practise'][$key];
					}
				}


				$work_question_id_arr = array();
				if(!empty($row['lecture_info']['cart']['cart']['question_rs']['work'])){
					foreach ($row['lecture_info']['cart']['cart']['question_rs']['work'] as $key=>$val){
						$work_question_id_arr= $row['lecture_info']['cart']['cart']['question_rs']['work'][$key];
					}
				}

				$row['lecture_info']['question_list']['practise'] = $this->get_simpleQuestionList( $practise_question_id_arr);
				$row['lecture_info']['question_list']['work'] = $this->get_simpleQuestionList( $work_question_id_arr);
			}

			if(!empty($row['module_answer'])){
				$row['module_answer'] = unserialize($row['module_answer']);
			}

			if(!empty($row['practise_answer'])){
				$row['practise_answer'] = unserialize($row['practise_answer']);
			}

			if(!empty($row['work_answer'])){
				$row['work_answer'] = unserialize($row['work_answer']);
			}

			if(!empty($row['dimension_level'])){
				$row['dimension'] = unserialize($row['dimension_level']);
			}else{
				if(!empty($row['lecture_info']['eap_subject_id'])){
					$row['dimension'] = $this->dao->getAll('SELECT d.id,d.title FROM '.$this->vp_subject_dimension_rs.' rs LEFT JOIN '.$this->vp_comment_dimension.' d ON rs.dimension_id = d.id WHERE rs.sid = '.$this->dao->quote($row['lecture_info']['eap_subject_id']));//获取课堂评价维度
				}
				if(empty($row['lecture_info']['dimension'])){
					$row['dimension'] = $this->dao->getAll('SELECT id,title FROM '.$this->vp_comment_dimension);//获取课堂评价维度
				}
			}


			if(!empty($row['lesson_record_img'])){
				$row['lesson_record_img'] = explode('|',trim($row['lesson_record_img'],'|'));
			}
		}
		return $row;
	}



	public function get_simpleQuestionList($question_id_arr){
		$optionKeyArr = C('OPTIONS_KEY');
		$list = array();
		if(!empty($question_id_arr)){
			foreach ($question_id_arr as $key=>$question_id){
				$list[] = $this->dao2->getRow('SELECT q.id,
											q.knowledge_id,
											k.name as knowledge_name,
											k.parent_id as knowledge_parent_id,
											p.name as knowledge_parent_name   
											FROM '.$this->vip_question.' q  
											LEFT JOIN '.$this->vip_knowledge.' k ON k.id = q.knowledge_id 
											LEFT JOIN '.$this->vip_knowledge.' p ON k.parent_id = p.id  
											WHERE q.parent_id = 0 AND q.id = '.$this->dao->quote($question_id));
			}
		}
		return $list;
	}


	public function addProgram($arr){
		$now = date('Y-m-d H:i:s');
		$strQuery = 'INSERT INTO '.$this->vp_training_program.' ( [student_code],
															      [student_name],
															      [program_url],
															      [instime],
															      [kecheng_code],
															      [kecheng_name],
															      [from_type],
															      [teacher_code],
															      [teacher_name],
															      [dept_code],
															      [dept_name],
															      [program_html],
															      [program_img],
															      [dimension_level],
															      [testCoachId],
															      [starttime],
															      [endtime],
															      [subject_code]) 
														VALUES ('.$this->dao->quote($arr['student_code']).',
																'.$this->dao->quote($arr['student_name']).',
																'.$this->dao->quote($arr['new_program_img']).',
																'.$this->dao->quote($now).',
																'.$this->dao->quote($arr['kecheng_code']).',
																'.$this->dao->quote($arr['kecheng_name']).',
																0,
																'.$this->dao->quote($arr['teacher_code']).',
																'.$this->dao->quote($arr['teacher_name']).',
																'.$this->dao->quote($arr['dept_code']).',
																'.$this->dao->quote($arr['dept_name']).',
																'.$this->dao->quote($arr['new_program_file']).',
																'.$this->dao->quote($arr['new_program_img']).',
																'.$this->dao->quote(serialize($arr['dimension_level_arr'])).',
																'.$this->dao->quote($arr['testCoachId']).',
																'.$this->dao->quote($arr['starttime']).',
																'.$this->dao->quote($arr['endtime']).',
																'.$this->dao->quote($arr['subject_code']).')';
		$this->dao->begin();
		$success = (boolean)$this->dao->execute($strQuery);
		$new_program_id = $this->dao->getOne('SELECT TOP 1 id FROM '.$this->vp_training_program.' ORDER BY id DESC ');
		$strQuery2 = '';
		foreach ($arr['programLesson'] as $key=>$lesson){
			$strQuery2 .= 'INSERT INTO '.$this->vp_program_lesson.' ([program_id],
																	 [lesson_no],
																	 [lesson_difficulty],
																	 [lesson_topic],
																	 [lesson_major],
																	 [instime]) 
															VALUES ('.$this->dao->quote($new_program_id).',
																	'.$this->dao->quote($lesson['lesson_no']).',
																	'.$this->dao->quote($lesson['lesson_difficulty']).',
																	'.$this->dao->quote($lesson['lesson_topic']).',
																	'.$this->dao->quote($lesson['lesson_major']).',
																	'.$this->dao->quote($now).');';
		}
		$success2 = (boolean)$this->dao->execute($strQuery2);
		if($success == true && $success2 == true){
			$this->dao->commit();
			return true;
		}
		$this->dao->rollback();
		return false;
	}


	public function getProgramText($dimension_id,$level_id){
		return $this->dao->getOne('SELECT text FROM '.$this->vp_program_text.' WHERE dimension_id = '.$this->dao->quote($dimension_id).' AND level_id = '.$this->dao->quote($level_id));
	}


	public function get_programInfoById($program_id){
		if(!empty($program_id)){
			$row = $this->dao->getRow('SELECT p.[id],
											  p.[student_code],
											  p.[kecheng_code],
											  p.[kecheng_name],
											  p.[teacher_code],
											  p.[teacher_name],
											  p.[dimension_level],
											  p.[testCoachId],
											  p.[subject_code],
											  convert(varchar(20),p.[starttime],120) as starttime,
											  convert(varchar(20),p.[endtime],120) as endtime,
											  p.[program_html],
											  s.sName as subject_name 
											  FROM '.$this->vp_training_program.' p LEFT JOIN '.$this->V_D_Subject.' s ON p.subject_code = s.sCode WHERE p.id = '.$this->dao->quote($program_id));
			if(!empty($row)){
				$row['program_lesson'] = $this->dao->getAll('SELECT [lesson_no],[lesson_topic],[lesson_difficulty],[lesson_major] FROM '.$this->vp_program_lesson.' WHERE program_id = '.$this->dao->quote($program_id));
			}
			return $row;
		}
		return false;
	}



	public function editProgram($program_id,$arr){
		if(!empty($program_id) && !empty($arr)){
			$now = date('Y-m-d H:i:s');
			$strQuery = 'UPDATE '.$this->vp_training_program.' SET [program_url] = '.$this->dao->quote($arr['new_program_img']).',
																   [program_html] = '.$this->dao->quote($arr['new_program_file']).',
																   [program_img] = '.$this->dao->quote($arr['new_program_img']).',
																   [updatetime] = '.$this->dao->quote($now).',
																   [dimension_level] = '.$this->dao->quote(serialize($arr['dimension_level_arr'])).' 
														     	   WHERE id = '.$this->dao->quote($program_id);
			$strQuery2 = 'DELETE FROM '.$this->vp_program_lesson.' WHERE program_id = '.$this->dao->quote($program_id);
			$strQuery3 = '';
			foreach ($arr['programLesson'] as $key=>$lesson){
				$strQuery3 .= 'INSERT INTO '.$this->vp_program_lesson.' ([program_id],
																	 	[lesson_no],
																	 	[lesson_difficulty],
																	 	[lesson_topic],
																	 	[lesson_major],
																	 	[instime]) 
																VALUES ('.$this->dao->quote($program_id).',
																		'.$this->dao->quote($lesson['lesson_no']).',
																		'.$this->dao->quote($lesson['lesson_difficulty']).',
																		'.$this->dao->quote($lesson['lesson_topic']).',
																		'.$this->dao->quote($lesson['lesson_major']).',
																		'.$this->dao->quote($now).');';
			}

			//$this->dao->begin();
			$success = (boolean)$this->dao->execute($strQuery);

			//$success3 = (boolean)$this->dao->execute($strQuery3);

			if($success == true){
				//$this->dao->commit();
				$success2 = (boolean)$this->dao->execute($strQuery2.$strQuery3);
				return true;
			}
			//$this->dao->rollback();
			return false;
		}
		return false;
	}



}

?>