<?php
class WeixinVipModel extends Model {
	public  $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao('MSSQL_CONN');
		$this->view_VB_StudentLessonHeLu = 'view_VB_StudentLessonHeLu';
		$this->view_VB_Student = 'view_VB_Student';
		$this->V_BS_StudentLessonHeLu = 'V_BS_StudentLessonHeLu';
		$this->V_Biz_Contract = 'V_Biz_Contract';
		$this->V_S_Dept = 'V_S_Dept';//
		$this->V_BS_Roster = 'V_BS_Roster';
		$this->BS_Teacher = 'BS_Teacher';
		$this->V_D_Grade = 'V_D_Grade';
		$this->vp_kechenghelu = 'vp_kechenghelu';
		$this->vp_kechenghelu_files = 'vp_kechenghelu_files';
		$this->vp_weixin_attention = 'vp_weixin_attention';
		$this->vp_weixin_attentionLog = 'vp_weixin_attentionLog';
		$this->vp_weixin_bind = 'vp_weixin_bind';
		$this->vp_weixin_img = 'vp_weixin_img';
		$this->view_VB_StudentContract = 'view_VB_StudentContract';
		$this->V_BS_RosterInfo = 'V_BS_RosterInfo';//学员科目情况明细
		$this->view_VD_KeCheng = 'view_VD_KeCheng';
		$this->vp_training_program = 'vp_training_program';
		$this->vp_handouts = 'vp_handouts';
		$this->vp_kecheng_overdue = 'vp_kecheng_overdue';
		$this->vp_comment_dimension = 'vp_comment_dimension';
		$this->vp_comment_level = 'vp_comment_level';
		$this->vp_comment_text = 'vp_comment_text';
		$this->vp_subject_dimension_rs = 'vp_subject_dimension_rs';
		$this->vp_words = 'vp_words';
		$this->vp_student_error_questions = 'vp_student_error_questions';
		$this->V_View_StuSumHours="V_View_StuSumHours";

		$this->dao3 = Dao::getDao('MYSQL_CONN2');
		$this->vip_news = 'vip_news';
		$this->vip_huodong = 'vip_huodong';
		

		$this->dao2 = Dao::getDao('MYSQL_CONN_KNOWLEDGE');
		$this->vip_question = 'vip_question';
		$this->vip_question_option = 'vip_question_option';
		$this->vip_question_answer = 'vip_question_answer';
		$this->vip_teacher_lecture = 'vip_teacher_lecture';
		$this->vip_dict_subject = 'vip_dict_subject';
		$this->vip_dict_grade = 'vip_dict_grade';
		$this->vip_knowledge = 'vip_knowledge';

		$this->dao4 = Dao::getDao('MYSQL_CONN_WEIXIN');
		$this->wx_user_binding = 'wx_user_binding';
		
	}

	public function get_myStudentAll($arr){
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
							 max(vp_helu.[lesson_topic]) as lesson_topic   
							 FROM '.$this->view_VB_StudentLessonHeLu.' as view_helu 
							 LEFT JOIN  '.$this->V_D_Grade.' as g ON g.[id] = view_helu.[nGrade] 
							 LEFT JOIN  '.$this->vp_kechenghelu.' as vp_helu ON vp_helu.[helu_id] = view_helu.[id] 
							 LEFT JOIN  '.$this->vp_kecheng_overdue.' as o ON o.[helu_id] = view_helu.[id] 
							 WHERE view_helu.[nStatus] = 2 AND view_helu.[dtLessonBeginReal] >= '.$this->dao->quote(C('BIOCLOCK_START')).' AND view_helu.[dtLessonBeginReal] <= '.$this->dao->quote($arr['now']);
		if(!empty($arr['teacherCode'])) {
			$strQuery .=  ' AND view_helu.[steacherCode] = '.$this->dao->quote($arr['teacherCode']);
		}
		$strQuery .= ' GROUP BY view_helu.[id] ';
		if(!empty($arr['key_name']) && !empty($arr['order'])){
			$order = ' ORDER BY '.$arr['key_name'].' '.$arr['order'];
		}else{
			$order = ' ORDER BY [nAudit] ASC,[nStatus] ASC,[dtLessonBeginReal] DESC ';
		}
		$list = $this->dao->getAll($strQuery.$order);
		if(!empty($list)){
			foreach ($list as $key =>$row){
				$list[$key]['gradename'] = $this->dao->getOne('SELECT g.[sName] as gradename 
									      FROM '.$this->view_VB_StudentContract.' as sc 
									      LEFT JOIN  '.$this->V_D_Grade.' as g ON g.[id] = sc.[CurrentGrade] 
									      WHERE sc.[sStudentCode] = '.$this->dao->quote($row['sstudentcode']));
			}
		}
		return $list;
	}


	public function get_teacherCode($userInfo){
		$strQuery = 'SELECT TOP 1 sCode FROM '.$this->BS_Teacher.' WHERE bValid = 1 AND [nKind] = 3 AND (sName like '.$this->dao->quote($userInfo['real_name'].'%').' OR sRealName like '.$this->dao->quote($userInfo['real_name'].'%').')';
		if($userInfo['user_type'] == '内部员工'){
			$strQuery .= ' AND [nType] = 1 ';
		}else if($userInfo['user_type'] == 'VIP社会兼职教师'){//VIP社会兼职教师
			$strQuery .= ' AND ([nType] = 3 OR [nType] = 4 )';
		}
		return $this->dao->getOne($strQuery);
	}


	public function get_newsList(){
		return $this->dao3->getAll('SELECT title,url,FROM_UNIXTIME(inputtime,"%Y-%m-%d") as inputtime FROM '.$this->vip_huodong.' where catid = 6 order by inputtime desc limit 20');
	}


	public function get_hotList(){
		return $this->dao3->getAll('SELECT title,url,FROM_UNIXTIME(inputtime,"%Y-%m-%d") as inputtime FROM '.$this->vip_news.' where catid = 7 order by inputtime desc limit 20');
	}


	public function addAttention($openId,$key='',$time=''){
		$is_attention = $this->dao->getOne('SELECT count(1) FROM '.$this->vp_weixin_attention.' WHERE openId = '.$this->dao->quote($openId));
		$this->dao->begin();
		$success1 = true;
		$success2 = true;
		if($is_attention == 0){
			$success1 = (boolean)$this->dao->execute('INSERT INTO '.$this->vp_weixin_attention.' (openId,gsKey) VALUES('.$this->dao->quote($openId).','.$this->dao->quote($key).')');
		}
		$attentionLog = $this->dao->getRow('SELECT top 1 createTime FROM '.$this->vp_weixin_attentionLog.' WHERE openId = '.$this->dao->quote($openId).' ORDER BY createTime DESC');
		if(empty($attentionLog) || $time-$attentionLog['createTime']>=2){
			$success2 = (boolean)$this->dao->execute('INSERT INTO '.$this->vp_weixin_attentionLog.' (openId,gsKey,createTime) VALUES('.$this->dao->quote($openId).','.$this->dao->quote($key).','.$this->dao->quote($time).')');
		}
		if($success1 && $success2){
			$this->dao->commit();
		}else{
			$this->dao->rollback();
		}
		return true;
	}

	public function deleteAttention($openId,$key='',$time=''){
		$is_attention = $this->dao->getOne('SELECT count(1) FROM '.$this->vp_weixin_attention.' WHERE openId = '.$this->dao->quote($openId));
		if($is_attention != 0){
			$this->dao->execute('DELETE FROM '.$this->vp_weixin_attention.' WHERE openId = '.$this->dao->quote($openId).' AND gsKey = '.$this->dao->quote($key));
		}
		return true;
	}


	public function findBindUserInfo($appId,$openId){
		return $this->dao->getRow('SELECT * FROM '.$this->vp_weixin_bind.' WHERE appId = '.$this->dao->quote($appId).' AND openId = '.$this->dao->quote($openId));
	}


	public function bindUser($arr){
		if(!empty($arr['appId'])&&!empty($arr['openId'])&&!empty($arr['user_key'])){
			$is_bind = $this->dao->getOne('SELECT COUNT(1) FROM '.$this->vp_weixin_bind.' WHERE appId = '.$this->dao->quote($arr['appId']).' AND openId = '.$this->dao->quote($arr['openId']).' AND user_key = '.$this->dao->quote($arr['user_key']));
			if($is_bind == 0){
				$this->dao->execute('INSERT INTO '.$this->vp_weixin_bind.' (appId,openId,user_key,teacherCode,is_valid,bindTime) VALUES('.$this->dao->quote($arr['appId']).','.$this->dao->quote($arr['openId']).','.$this->dao->quote($arr['user_key']).','.$this->dao->quote($arr['teacherCode']).',1,'.$this->dao->quote(date('Y-m-d H:i:s')).')');
				if($this->dao->affectRows()){
					return true;
				}
				return false;
			}else{
				return true;
			}
		}
		return false;
	}


	public function delBind($arr){
		$this->dao->execute('DELETE FROM '.$this->vp_weixin_bind.' WHERE openId = '.$this->dao->quote($arr['openId']));
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}


	public function get_heluListAll($arr){
		/*$strQuery = 'SELECT helu.[id] AS id,
							helu.[helu_id] AS helu_id,
							convert(varchar(20),helu.[lesson_date],111) as lesson_date,
							helu.[lesson_end],
							helu.[lesson_topic],
							helu.[comment],
							o.[is_overdue]  
							FROM '.$this->vp_kechenghelu.' as helu 
							LEFT JOIN  '.$this->vp_kecheng_overdue.' as o ON o.[helu_id] = helu.[helu_id] 
							LEFT JOIN  '.$this->V_BS_StudentLessonHeLu.' as lesson ON lesson.[id] = helu.[helu_id] 
							WHERE lesson.[nStatus] != 3 ';*/
		$strQuery = 'SELECT helu.[id] AS id,
							helu.[helu_id] AS helu_id,
							convert(varchar(20),helu.[lesson_date],111) as lesson_date,
							helu.[lesson_end],
							helu.[lesson_topic],
							helu.[comment],
							o.[is_overdue]  
							FROM '.$this->V_BS_StudentLessonHeLu.' as lesson 
							LEFT JOIN  '.$this->vp_kecheng_overdue.' as o ON o.[helu_id] = lesson.[id] 
							LEFT JOIN  '.$this->vp_kechenghelu.' as helu ON lesson.[id] = helu.[helu_id] 
							WHERE lesson.[nStatus] != 3 ';
		if(!empty($arr['student_code'])){
			$strQuery .= ' AND helu.[student_code] = '.$this->dao->quote($arr['student_code']);
		}
		if(!empty($arr['kecheng_code'])){
			$strQuery .= ' AND helu.[kecheng_code] = '.$this->dao->quote($arr['kecheng_code']);
		}
		$strQuery .= ' ORDER BY id DESC';
		return $this->dao->getAll($strQuery);
	}


	public function add_wxImg($imgUrl,$serviceUrl,$openid,$is_success=0){
		$exists = $this->dao->getOne('select count(1) from '.$this->vp_weixin_img.' where wxImgUrl = '.$this->dao->quote($imgUrl).' and openId = '.$this->dao->quote($openid));
		if($exists){
			return true;
		}else{
			$this->dao->execute('INSERT INTO '.$this->vp_weixin_img.' ([openId],[wxImgUrl],[serviceUrl],[createTime],[is_success]) VALUES('.$this->dao->quote($openid).','.$this->dao->quote($imgUrl).','.$this->dao->quote($serviceUrl).','.$this->dao->quote(time()).','.$this->dao->quote($is_success).')');
			$exists = $this->dao->getOne('select count(1) from '.$this->vp_weixin_img.' where serviceUrl='.$this->dao->quote($serviceUrl).' and wxImgUrl = '.$this->dao->quote($imgUrl).' and openId = '.$this->dao->quote($openid));
			if($exists){
				return true;
			}
			return false;
		}
	}


	public function get_wxImgList($openid){
		$list = $this->dao->getAll('select id,wxImgUrl,serviceUrl from '.$this->vp_weixin_img.' where openId = '.$this->dao->quote($openid).' and useStatus = 0 and is_success = 1 ');
		if(!empty($list)){
			foreach ($list as $key=>$row){
				if(!empty($row['serviceurl'])){
					$list[$key]['serviceurl_show'] = APP_URL.end(explode('/eap',str_replace('/Upload/','/upload/',$row['serviceurl'])));
				}
			}
		}
		return $list;
	}


	/*获取微信端已上传图片数量*/
	public function get_wxImgCount($openId){
		return $this->dao->getOne('SELECT COUNT(1) FROM '.$this->vp_weixin_img.' WHERE [openId] = '.$this->dao->quote($openId));
	}

	public function get_lessonList($teacherCode,$time='',$studentCode='',$kechengCode='',$maxTime='',$selectAll=1){
		$oneToOne = 0;
		$oneToTwo = 0;
		$groupClass = 0;
		$groupClassMoney = 0;
		if(!empty($teacherCode)){
			$strQuery = 'select  helu.[id],
								 helu.[nLessonNo],
								 helu.[sStudentCode], 
								 helu.[sKeChengCode], 
								 helu.[sKeChengName],
								 helu.[nTutorType],
								 helu.[dRealExpend],
								 convert(varchar(20),helu.[dtDateReal],120) as dtDateReal,
								 convert(varchar(20),helu.[dtLessonBeginReal],120) as dtLessonBeginReal,
								 convert(varchar(20),helu.[dtLessonEndReal],120) as dtLessonEndReal,
								 helu.[nHoursReal],
								 cast(datediff(mi,helu.dtLessonBeginReal,helu.dtLessonEndReal)/60.0 as numeric(9,1)) as nHoursReal,
								 helu.[nStatus],
								 stu.[sName] as sStudentName,
								 d.[sName] as sAreaName, 
								 h.[lesson_topic] as lesson_topic,
								 h.[comment] as comment,
								 h.[module_answer] as module_answer,
								 h.[practise_answer] as practise_answer,
								 h.[dimension_level] as dimension_level,
								 h.[lesson_record_img] as lesson_record_img,
								 h.[lesson_report_url_wx] as lesson_report_url_wx,
								 f.[lecture_id] as lecture_id,
								 f.[url] as url , 
								 f2.[url] as itembank_url,
								 o.is_overdue  
								 FROM '.$this->view_VB_StudentLessonHeLu.' as helu 
								 LEFT JOIN '.$this->view_VB_Student.' as stu ON stu.[sCode] = helu.[sStudentCode] 
								 INNER JOIN '.$this->V_Biz_Contract.' as c ON c.[sCode] =  helu.[sContractCode] AND c.[sStudentCode] = helu.[sStudentCode]  
								 LEFT JOIN '.$this->V_S_Dept.' as d ON d.[sCode] =  c.[sDeptCode] 
								 LEFT JOIN '.$this->vp_kechenghelu.' as h ON helu.[id] =  h.[helu_id] 
								 LEFT JOIN '.$this->vp_kechenghelu_files.' as f ON helu.[id] =  f.[helu_id] AND f.type =0 
								 LEFT JOIN '.$this->vp_kechenghelu_files.' as f2 ON helu.[id] =  f2.[helu_id] AND f2.type =1 
								 LEFT JOIN '.$this->vp_kecheng_overdue.' as o ON helu.[id] =  o.[helu_id] 
								 WHERE helu.[nStatus] != 3 ';
			if(!empty($teacherCode)) {
				$strQuery .=  ' AND helu.[steacherCode] = '.$this->dao->quote($teacherCode);
			}
			if(!empty($time)) {
				$strQuery .=  ' AND helu.[dtDateReal] = '.$this->dao->quote(date('Y-m-d 00:00:00.000',$time));
			}
			if(!empty($studentCode)) {
				$strQuery .=  ' AND helu.[sStudentCode] = '.$this->dao->quote($studentCode);
			}
			if(!empty($kechengCode)) {
				$strQuery .=  ' AND helu.[sKeChengCode] = '.$this->dao->quote($kechengCode);
			}
			if(!empty($maxTime)){
				$strQuery .=  ' AND helu.[dtLessonEndReal] < '.$this->dao->quote($maxTime);
			}
			if($selectAll==0){
				$strQuery .=  ' AND (h.lesson_topic IS NULL OR f.[url] IS NULL) AND helu.[dtLessonEndReal] > '.$this->dao->quote(date('Y-m-d H:i:s',time()-48*3600));
			}
			if($selectAll==2){
				$strQuery .=  ' AND  f2.[url] IS NULL ';
			}
			if($selectAll==3){
				$strQuery .=  ' AND (((h.lesson_topic IS NULL OR f.[url] IS NULL) AND helu.[dtLessonEndReal] < '.$this->dao->quote(C('PIV_START')).') OR ( helu.[dtLessonEndReal] >= '.$this->dao->quote(C('PIV_START')).')) AND helu.[dtLessonEndReal] > '.$this->dao->quote(date('Y-m-d H:i:s',time()-48*3600));//新版本xcp
			}
			$list = $this->dao->getAll($strQuery);
			if(!empty($time)) {
				if(!empty($list)){
					foreach ($list as $key=>$val){
						$list[$key]['timeStr'] = date('H:i',strtotime($val['dtlessonbeginreal'])).'~'.date('H:i',strtotime($val['dtlessonendreal']));
						if(strtotime($val['dtlessonendreal']) < time()){
							$list[$key]['is_end'] = 1;
						}
						if(strtotime($val['dtlessonbeginreal']) < time()){
							$list[$key]['is_begin'] = 1;
						}
						$total_hours += $val['nhoursreal'];
						$student_arr[] = $val['sstudentcode'];

						//判断课次核录是否逾期（48小时）
						$list[$key]['overdue'] = 0;
						if((strtotime($val['dtlessonendreal'])+48*3600)<time()){
							$list[$key]['overdue'] = 1;
						}
						
						switch ($val['ntutortype']){
							case 1:
								$oneToOne += $val['nhoursreal'];
								break;
							case 2:
								$oneToTwo += $val['nhoursreal'];
								break;
							case 7:
								$groupClass += $val['nhoursreal'];
								$groupClassMoney += $val['drealexpend'];
								break;
						}

					}
				}
			}
		}else{
			$list = array();
		}
		return array('list'=>$list,'total_hours'=>abs($total_hours),'student_arr'=>$student_arr,'one_to_one'=>$oneToOne,'one_to_two'=>$oneToTwo,'group_class'=>$groupClass,'group_class_money'=>$groupClassMoney);
	}

	public function del_wxImg($id){
		if(!empty($id)){
			$serviceUrl = $this->dao->getOne('SELECT serviceUrl FROM '.$this->vp_weixin_img.' WHERE id = '.$this->dao->quote($id));
			$this->dao->execute('DELETE FROM '.$this->vp_weixin_img.' WHERE id = '.$this->dao->quote($id));
			if($this->dao->affectRows()){
				if(!empty($serviceUrl)){
					@unlink($serviceUrl);
				}
				return true;
			}
			return false;
		}
		return false;
	}

	public function update_wxImgStatus($status,$wxId){
		$this->dao->execute('UPDATE '.$this->vp_weixin_img.' SET useStatus = '.$status.' WHERE id = '.$this->dao->quote($wxId));
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}

	public function get_viewHeluInfo($helu_id){
		return $this->dao->getRow('SELECT sKeChengCode,sKeChengName,steacherCode,sTeacherName,sStudentCode,sStudentName,nLessonNo,convert(varchar(20),[dtDateReal],120) as dtDateReal,convert(varchar(20),[dtLessonBeginReal],120) as dtLessonBeginReal,convert(varchar(20),[dtLessonEndReal],120) as dtLessonEndReal FROM '.$this->view_VB_StudentLessonHeLu.' WHERE id='.$this->dao->quote($helu_id));
	}

	public function add_heluFiles($arr){
		$type = ($arr['type']==3)?0:$arr['type'];
		$heluFileInfo = $this->dao->getRow('SELECT * FROM '.$this->vp_kechenghelu_files.' WHERE helu_id = '.$this->dao->quote($arr['helu_id']).' and type = '.$this->dao->quote($type));
		if(!empty($heluFileInfo)){
			$strQuery = 'UPDATE '.$this->vp_kechenghelu_files.' SET url = '.$this->dao->quote(trim($heluFileInfo['url'],'|').'|'.$arr['url']).',from_type=1 WHERE helu_id = '.$this->dao->quote($arr['helu_id']).' AND type = '.$this->dao->quote($type);
		}else{
			$strQuery = 'INSERT INTO '.$this->vp_kechenghelu_files.' ([helu_id],[title],[url],[type],[from_type]) VALUES('.$this->dao->quote($arr['helu_id']).','.$this->dao->quote($arr['title']).','.$this->dao->quote($arr['url']).','.$this->dao->quote($type).',1)';
		}

		if($arr['type'] == 3){
			$count = $this->dao->getOne('SELECT count(1) FROM '.$this->vp_kechenghelu.' WHERE helu_id = '.$this->dao->quote($arr['helu_id']));
			if($count > 0){
				$strQuery2 = 'UPDATE '.$this->vp_kechenghelu.' SET lesson_record_img = '.$this->dao->quote($arr['url']).',lesson_record_img_from_type=1 WHERE helu_id = '.$this->dao->quote($arr['helu_id']);
			}else{
				$heluInfo = $this->dao->getRow('SELECT sStudentCode,
												   sKeChengCode,
												   sStudentName,
												   nLessonNo,
												   convert(varchar(20),[dtDateReal],120) as dtDateReal,
												   convert(varchar(20),[dtLessonBeginReal],120) as dtLessonBeginReal,
												   convert(varchar(20),[dtLessonEndReal],120) as dtLessonEndReal
												   FROM '.$this->view_VB_StudentLessonHeLu.' 
												   WHERE id = '.$this->dao->quote($arr['helu_id']));
				$strQuery2 = 'INSERT INTO '.$this->vp_kechenghelu.' (helu_id,
																 kecheng_code,
																 lesson_no,
																 student_code,
																 student_name,
																 lesson_date,
																 lesson_begin,
																 lesson_end,
																 lasttime,
																 lesson_record_img,
												                 lesson_record_img_from_type
																 ) 
														VALUES('.$this->dao->quote($arr['helu_id']).',
															   '.$this->dao->quote($heluInfo['skechengcode']).',
															   '.$this->dao->quote($heluInfo['nlessonno']).',
															   '.$this->dao->quote($heluInfo['sstudentcode']).',
															   '.$this->dao->quote($heluInfo['sstudentname']).',
															   '.$this->dao->quote($heluInfo['dtdatereal']).',
															   '.$this->dao->quote(date('H:i',strtotime($heluInfo['dtlessonbeginreal']))).',
															   '.$this->dao->quote(date('H:i',strtotime($heluInfo['dtlessonendreal']))).',
															   '.$this->dao->quote(date('Y-m-d H:i:s')).',
															   '.$this->dao->quote($arr['url']).',
															   '.$this->dao->quote(1).'
															   )';
			}
		}else{
			$strQuery2 = '';
		}

		$this->dao->begin();
		$success = (boolean)$this->dao->execute($strQuery);
		if(!empty($strQuery2)){
			$success2 = (boolean)$this->dao->execute($strQuery2);
		}else{
			$success2 = true;
		}

		if($success == true && $success2 == true){
			$this->dao->commit();
			return true;
		}else{
			$this->dao->rollback();
			return false;
		}
	}

	public function check_isHelu($helu_id){
		return $this->dao->getOne('SELECT COUNT(1) FROM '.$this->vp_kechenghelu.' WHERE helu_id = '.$this->dao->quote($helu_id));
	}

	public function get_heluFiles($helu_id,$type){
		return $this->dao->getRow('SELECT * FROM '.$this->vp_kechenghelu_files.' WHERE helu_id ='.$this->dao->quote($helu_id).' AND type = '.$this->dao->quote($type));
	}

	public function get_heluFilesCount($helu_id,$type){
		$fileInfo = $this->get_heluFiles($helu_id,$type);
		if(!empty($fileInfo['url'])){
			$fileArr = explode('|',$fileInfo['url']);
			if(!empty($fileArr)){
				foreach ($fileArr as $k=>$file){
					if(empty($file)) unset($fileArr[$k]);
				}
			}
		}
		return count($fileArr);
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
																  [lesson_topic] = '.$this->dao->quote(SysUtil::safeString($_POST['lesson_topic'])).',
																  [comment]='.$this->dao->quote(SysUtil::safeString(SysUtil::safeString($_POST['comment']))).',
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
																   '.$this->dao->quote(SysUtil::safeString($_POST['lesson_topic'])).',
																   '.$this->dao->quote(SysUtil::safeString($_POST['comment'])).',
																   '.$this->dao->quote(abs($_POST['itembank_score'])).',
																   '.$this->dao->quote(date('Y-m-d H:i:s',$now)).',
																   '.$this->dao->quote($is_sendsms).')';
			}
			$success1 = (boolean)$this->dao->execute($strQuery);
			if($success1 == true){
				return true;
			}
			return false;
		}
		return false;
	}

	/*studentModel同方法start===================================================================================*/
	public function get_myStudentList($arr,$type=1,$currentPage=1, $pageSize=20){
		$count = $this->get_myStudentCount($arr);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'select  max(view_helu.[id]) as heluId,
							 max(view_helu.[nLessonNo]) as nLessonNo,
							 max(view_helu.[sStudentCode]) as sStudentCode, 
							 max(view_helu.[sKeChengCode]) as sKeChengCode,
							 max(view_helu.[sKeChengName]) as sKeChengName, 
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
				$strQuery .= " AND (vp_helu.[lesson_topic] IS NULL OR (vp_file.[url] IS NULL AND view_helu.dtDateReal >= '2015-04-15 00:00:00'))  ";
			}else{
				$strQuery .= " AND (vp_helu.[lesson_topic] IS NULL OR vp_file.[url] IS NULL) ";
			}
		}
		if(!empty($arr['overdue'])){
			$strQuery .= " AND o.[is_overdue] IS NULL ";
		}
		$strQuery .= ' GROUP BY view_helu.[id] ';
		if(!empty($arr['key_name']) && !empty($arr['order'])){
			$order = ' ORDER BY '.$arr['key_name'].' '.$arr['order'];
		}else{
			$order = ' ORDER BY [nAudit] ASC,[nStatus] ASC,[dtLessonBeginReal] DESC ';
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
							 		 WHERE view_helu.[nStatus] = 2 AND view_helu.[dtLessonBeginReal] >= '.$this->dao->quote(C('BIOCLOCK_START')).' AND view_helu.[dtLessonBeginReal] <= '.$this->dao->quote($arr['now']);
		if($arr['teacherCode']) {
			$strQuery .=  ' AND view_helu.[steacherCode] = '.$this->dao->quote($arr['teacherCode']);
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
									      WHERE sc.[sStudentCode] = '.$this->dao->quote($student_code));
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
								  convert(varchar(20),helu.[lasttime],120) as lasttime,
								  helu.[is_send_sms]  
								  FROM '.$this->vp_kechenghelu.' AS helu 
								  LEFT JOIN '.$this->vp_kechenghelu_files.' AS a ON helu.helu_id = a.helu_id AND a.type = 0 
								  LEFT JOIN '.$this->vp_kechenghelu_files.' AS b ON helu.helu_id = b.helu_id AND b.type = 1  
								  WHERE 1=1 ';
		if(!empty($arr['id'])){
			$strQuery .= ' AND helu.id ='.$this->dao->quote($arr['id']);
		}
		if(!empty($arr['helu_id'])){
			$strQuery .= ' AND helu.helu_id ='.$this->dao->quote($arr['helu_id']);
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
			$row['itembank_url_show'] = (!empty($row['itembank_url']))?str_replace('/Upload/','/upload/',$row['itembank_url']):'';
			$row['handouts_count'] = 0;
			if(!empty($row['handouts_url'])){
				foreach (explode('|',trim($row['handouts_url'],'|')) as $kk=>$v){
					$row['handouts_count']++;
					$row['handouts'][$kk]['url'] = $v;
					$row['handouts'][$kk]['url_show'] = str_replace('/Upload/','/upload/',$v);
					$row['handouts'][$kk]['is_exist'] = !file_exists(APP_DIR.$v)?0:1;
					$row['handouts'][$kk]['filetype'] =  strtolower(end(explode('.',$v)));
				}
			}
		}
		return $row;
	}


	public function getAllStudents($arr){
		$strQuery = 'select  max(view_helu.[sStudentCode]) as sStudentCode,
							 max(view_helu.[sStudentName]) as sStudentName,
							 min(o.[is_overdue]) as is_overdue 
							 FROM '.$this->view_VB_StudentLessonHeLu.' as view_helu 
							 LEFT JOIN '.$this->V_BS_Roster.' as r ON view_helu.[sCardCode] = r.[sCardCode] 
							 LEFT JOIN '.$this->vp_kechenghelu.' as h ON view_helu.[id] = h.[helu_id] 
							 LEFT JOIN '.$this->vp_kechenghelu_files.' as f ON view_helu.[id] = f.[helu_id] AND f.type=0 
							 LEFT JOIN '.$this->vp_kechenghelu_files.' as f2 ON view_helu.[id] = f2.[helu_id] AND f2.type=1 
							 LEFT JOIN  '.$this->vp_kecheng_overdue.' as o ON o.[helu_id] = view_helu.[id] 
							 WHERE view_helu.[nStatus] = 2 AND view_helu.[dtLessonBeginReal] >= '.$this->dao->quote(C('BIOCLOCK_START')) .' AND view_helu.[dtLessonBeginReal] <= '.$this->dao->quote(date('Y-m-d H:i:s')).' AND o.[is_overdue] IS NULL ';
		if(!empty($arr['teacherCode'])){
			$strQuery .= ' AND view_helu.[steacherCode] = '.$this->dao->quote($arr['teacherCode']);
		}
		if($arr['is_jieke'] == 1){
			$strQuery .= ' AND r.[nHoursRegHas] < r.[nHoursPay] ';
		}
		if($arr['type'] == 1){//上传讲义
			$strQuery .=  ' AND (h.lesson_topic IS NULL OR f.[url] IS NULL) ';
			//$strQuery .=  ' AND ((((h.lesson_topic IS NULL OR f.[url] IS NULL ) AND view_helu.[dtLessonBeginReal] < '.$this->dao->quote(C('PIV_START')).') OR (h.lesson_record_img IS NULL AND view_helu.[dtLessonBeginReal] >= '.$this->dao->quote(C('PIV_START')).'))) ';//新版本xcp
		}
		if($arr['type'] == 2){//上传测试卷
			$strQuery .=  ' AND  f2.[url] IS NULL AND view_helu.[dtDateReal] >= '.$this->dao->quote(date('Y-m-d H:i:s',time()-3600*24*60));
		}
		if($arr['type'] == 3){//上传轨照
			$strQuery .=  ' AND ((((h.lesson_topic IS NULL OR f.[url] IS NULL ) AND view_helu.[dtLessonBeginReal] < '.$this->dao->quote(C('PIV_START')).') OR (h.lesson_record_img IS NULL AND view_helu.[dtLessonBeginReal] >= '.$this->dao->quote(C('PIV_START')).'))) ';//新版本xcp
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
							 WHERE 1=1 ';
		if($arr['is_jieke']==1){
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

	public function updateItembankScore($arr){
		if($arr['act'] == 'update'){
			$this->dao->execute('UPDATE '.$this->vp_kechenghelu.' SET itembank_score = '.$this->dao->quote(abs($arr['itembank_score'])).' WHERE helu_id = '.$this->dao->quote($arr['helu_id']));
		}else{
			$this->dao->execute('INSERT INTO '.$this->vp_kechenghelu.' ([helu_id],
																		[kecheng_code],
																		[lesson_no],
																		[student_code],
																		[student_name],
																		[lesson_date],
																		[lesson_begin],
																		[lesson_end],
																		[itembank_score],
																		[lasttime]) 
																VALUES('.$this->dao->quote($arr['helu_id']).',
																	   '.$this->dao->quote($arr['kecheng_code']).',
																	   '.$this->dao->quote(abs($arr['lesson_no'])).',
																	   '.$this->dao->quote($arr['student_code']).',
																	   '.$this->dao->quote($arr['student_name']).',
																	   '.$this->dao->quote(date('Y-m-d',$arr['lesson_date'])).',
																	   '.$this->dao->quote(date('H:i',$arr['lesson_begin'])).',
																	   '.$this->dao->quote(date('H:i',$arr['lesson_end'])).',
																	   '.$this->dao->quote(abs($arr['itembank_score'])).',
																	   '.$this->dao->quote(date('Y-m-d H:i:s')).')');
		}
		if($this->dao->affectRows()){
			return true;
		}
		return false;
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
																 [dept_name],
																 [from_type]) 
														 VALUES('.$this->dao->quote($arr['student_code']).',
																'.$this->dao->quote($arr['student_name']).',
																'.$this->dao->quote($arr['url']).',
																'.$this->dao->quote(date('Y-m-d H:i:s')).',
																'.$this->dao->quote($arr['kecheng_code']).',
																'.$this->dao->quote($arr['kecheng_name']).',
																'.$this->dao->quote($arr['teacher_code']).',
																'.$this->dao->quote($arr['teacher_name']).',
																'.$this->dao->quote($deptInfo['dept_code']).',
																'.$this->dao->quote($deptInfo['dept_name']).', 
																'.$this->dao->quote($arr['from_type']).' 
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
																		'.$this->dao->quote(SysUtil::safeString($arr['lesson_topic'])).',
																		'.$this->dao->quote($arr['teacher_name']).',
																		'.$this->dao->quote(SysUtil::safeString($arr['comment'])).',
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




	/*教师系统微信改版0615===========================================================================*/
	public function get_myStudentAll_new($arr){
		$strQuery = 'select
							 max(view_helu.[sStudentCode]) as sStudentCode, 
							 max(view_helu.[sStudentName]) as sStudentName,
							 max(view_helu.[nGrade]) as nGrade,
							 max(view_helu.[sClassAdviserCode]) as sClassAdviserCode,
							 max(view_helu.[sClassAdviserName]) as sClassAdviserName,
							 max(g.[sName]) as gradename, 
							 max(dept.[sName]) as deptname,
							 (SELECT count(1) FROM '.$this->view_VB_StudentLessonHeLu.' WHERE sStudentCode = view_helu.[sStudentCode] AND steacherCode = '.$this->dao->quote($arr['teacherCode']).' AND dtLessonBeginReal > getdate() ) as nobegin_count,
							 (SELECT count(1) FROM '.$this->view_VB_StudentLessonHeLu.' WHERE sStudentCode = view_helu.[sStudentCode] AND steacherCode = '.$this->dao->quote($arr['teacherCode']).' AND dtLessonEndReal < getdate() ) as end_count 
							 FROM '.$this->view_VB_StudentLessonHeLu.' as view_helu 
							 LEFT JOIN  '.$this->V_D_Grade.' as g ON g.[id] = view_helu.[nGrade] 
							 LEFT JOIN  '.$this->V_S_Dept.' as dept ON dept.[sCode] = view_helu.[sDeptCode] 
							 WHERE 1=1 ';
		if(!empty($arr['teacherCode'])) {
			$strQuery .=  ' AND view_helu.[steacherCode] = '.$this->dao->quote($arr['teacherCode']);
		}
		$strQuery .= ' GROUP BY view_helu.[sStudentCode] ';
		if(!empty($arr['key_name']) && !empty($arr['order'])){
			$order = ' ORDER BY '.$arr['key_name'].' '.$arr['order'];
		}else{
			$order = ' ORDER BY nobegin_count DESC ';
		}

		$list = $this->dao->getAll($strQuery.$order);
		if(!empty($list)){
			foreach ($list as $key =>$row){
				$list[$key]['gradename'] = $this->dao->getOne('SELECT g.[sName] as gradename 
									      FROM '.$this->view_VB_StudentContract.' as sc 
									      LEFT JOIN  '.$this->V_D_Grade.' as g ON g.[id] = sc.[CurrentGrade] 
									      WHERE sc.[sStudentCode] = '.$this->dao->quote($row['sstudentcode']));
			}
		}
		return $list;
	}



	public function get_lessonAll($arr){
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
							 vp_helu.[lesson_report_url_wx],
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
		$list = $this->dao->getAll($strQuery.$order);

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

	public function get_heluInfo_new($helu_id,$type = 1 ){
		$row = $this->dao->getRow('SELECT helu.*,
										 convert(varchar(20),helu.lesson_report_createtime,120) as lesson_report_createtime,
										 view_helu.id as helu_id,
										 view_helu.[sKeChengCode] as sKeChengCode,
										 view_helu.[sStudentCode] as sStudentCode,
										 view_helu.[sTeacherCode] as sTeacherCode,
										 view_helu.[sTeacherName] as sTeacherName,
										 view_helu.[nLessonNo] as nLessonNo,
										 view_helu.[sStudentName] as sStudentName,
										 view_helu.[sKeChengName] as sKeChengName,
										 view_helu.[sTeacherName] as sTeacherName,
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
			$heluInfo = $this->get_heluInfo_new($arr['helu_id']);
			$lastHeluInfo = $this->get_heluInfo_new($arr['last_helu_id']);
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
							AND type = 1');*/
							//if($tempCount==0){
							$strQuery3 .= 'INSERT  INTO '.$this->vp_student_error_questions.' (student_code,
																						  helu_id, 
																						  question_id,type) 
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
							AND type = 2');*/
							//if($tempCount==0){
							$strQuery3 .= 'INSERT  INTO '.$this->vp_student_error_questions.' (student_code,
																						  helu_id, 
																						  question_id,type) 
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
							AND type =3 ');*/
							//if($tempCount==0){
							$strQuery3 .= 'INSERT  INTO '.$this->vp_student_error_questions.' (student_code,
																						  helu_id, 
																						  question_id,type) 
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
															  lesson_topic = '.$this->dao->quote($arr['lesson_topic']).',
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
																	lesson_topic,
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
																'.$this->dao->quote($arr['lesson_topic']).',
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



	public function get_waitHeluList($arr){
		$strQuery = 'select   view_helu.[id] as heluId,
							  convert(varchar(20),view_helu.[dtDateReal],120) as dtDateReal, 
							  convert(varchar(20),view_helu.[dtLessonBeginReal],120) as dtLessonBeginReal, 
							  convert(varchar(20),view_helu.[dtLessonEndReal],120) as dtLessonEndReal, 
							  view_helu.[sStudentName],
							  view_helu.[sStudentCode],
							  view_helu.[sKeChengName],
							  view_helu.[sKeChengCode],
							  vp_helu.[lesson_topic],
							  vp_helu.[lesson_record_img],  
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
	

	public function getTeacher($page) {
		
		$strQuery = 'select distinct helu.[steachercode],cher.[sName]
					FROM '.$this->view_VB_StudentLessonHeLu.' as helu 
					LEFT JOIN '.$this->BS_Teacher.' as cher
					on helu.[steachercode] = cher.[scode] 
					WHERE helu.[nStatus] != 3 AND helu.[dtDateReal] = '.$this->dao->quote(date('Y-m-d 00:00:00.000',time()+86400));
		$list = $this->dao->getAll($strQuery);
		$count = ceil(count($list)/5);
		$start = ($page-1)*$count;
		$end = $page*$count;
		if ( $page <= 5 )
		{
			if( !empty($list) )
			{
				$arr = array();
				foreach ( $list as $key=>$val )
				{	
					if ( $key >= $start && $key < $end )
					{
						$sql = "SELECT 
							convert(varchar(10),helu.[dtLessonBeginReal],108) AS dtlessonbegin,
							convert(varchar(10),helu.[dtLessonEndReal],108) AS dtlessonend,
							stu.[sName] AS sStudentName 
							FROM ".$this->view_VB_StudentLessonHeLu." AS helu 
							LEFT JOIN ".$this->view_VB_Student." AS stu ON stu.[sCode] = helu.[sStudentCode]  
							WHERE helu.[nStatus] != 3 
							AND helu.[dtDateReal] = ".$this->dao->quote(date('Y-m-d 00:00:00.000',time()+86400))." 
							AND helu.[steachercode]= '".$val['steachercode']."' ORDER BY helu.[dtLessonBeginReal] ASC";
						$student = $this->dao->getAll($sql);
						if( !empty($student) )
						{
							$val['list'] = $student;
						}						
						$val['openid'] = $this->dao->getAll("SELECT openid FROM ".$this->vp_weixin_bind." WHERE teacherCode ='".$val['steachercode']."'");
						$arr[$key] = $val;
					}
				}
			}
		}
		return $arr;
	}

	// 获取学员未打卡的信息
	public function getStudentNoPunch(){
		$start = date('Y-m-d H:i:00.000',time()-(60*60));
		$end   = date('Y-m-d H:i:00.000',time()-(50*60));
		$sql = "SELECT 
					convert(varchar(10),helu.[dtLessonBeginReal],108) AS dtlessonbegin,
					convert(varchar(10),helu.[dtLessonEndReal],108) AS dtlessonend,
					helu.[steachercode],
					stu.[sName] AS sStudentName 
					FROM ".$this->view_VB_StudentLessonHeLu." AS helu 
					LEFT JOIN ".$this->view_VB_Student." AS stu ON stu.[sCode] = helu.[sStudentCode]  
					WHERE helu.[nStatus] = 1 
					AND helu.[dtLessonBeginReal] > ".$this->dao->quote($start)."
					AND helu.[dtLessonBeginReal] <= ".$this->dao->quote($end)."
					ORDER BY helu.[dtLessonBeginReal] ASC";
		$list = $this->dao->getAll($sql);
		if( !empty($list) ){
			foreach($list as $key=>$student){
				$student['sname'] = $this->dao->getOne("SELECT cher.[sName] FROM ".$this->BS_Teacher." AS cher WHERE cher.[scode]= '".$student['steachercode']."'");
				$student['openid'] = $this->dao->getAll("SELECT openid FROM ".$this->vp_weixin_bind." WHERE teacherCode ='".$student['steachercode']."'");
				$arr[$key] = $student;
			}	
		}
		return $arr;
	}

	// 查询学管师微信openid
	public function getOpenid($data = array()){
		if( !empty($data) ){
			$user_id = $data->sAssigneeCode;
			
			if( !empty($user_id) ){
				
				$sql = "SELECT open_id,app_name,app_level FROM ".$this->wx_user_binding." WHERE user_id = '".$user_id."' AND bind_status = 1";
				$openidList = $this->dao4->getAll($sql);
			}
		
			return $openidList;
		}
	}


	/**
	 * 我的学员累计课时
	 * @param  [type] $arr [description]
	 * @return [type]      [description]
	 */
	public function getStudentLesson($arr)
	{
		$strQuery=' select view_hours.[id],view_hours.[sAliasCode],view_hours.[sStudentCode],view_hours.[sStudentName],view_hours.[dSumHours],view_hours.[dHours],view_hours.[dMonthSumHours],view_hours.[dEndSumHours],view_hours.[nStudentProperty] from '.$this->V_View_StuSumHours.' as view_hours where 1=1 ';

		if(!empty($arr['teacherCode'])) {
			$strQuery .=  ' AND view_hours.[sTeacherCode] = '.$this->dao->quote($arr['teacherCode']);
		}

		if(!empty($arr['key_name']))
		{
			$strQuery.=' AND view_hours.[sStudentName]='.$this->dao->quote($arr['key_name']);
		}

		$order=' ORDER BY [nStudentProperty] ASC, [dSumHours] desc ';

		return 	$list = $this->dao->getAll($strQuery.$order);
	}
	
	
	
	public function getSubjectList($userInfo){
		//$grade = 9;
		//$strQuery = 'SELECT sSubjectName FROM [GS].[dbo].[view_VB_Roster] where sStudentCode = 'BJ39226' and nGrade='.$grade.' group by sSubjectName ';
		return C('ZHONGKAO_SUBJECT');
		/*if(in_array($userInfo['user_name'],C('SUPER_USERS'))){
			 return $this->dao->getAll('SELECT sid,name FROM vp_subject WHERE type=0 ');
		}else{
			$sids = $this->dao->getOne('select sids from vp_user_subjects where user_key='.$this->dao->quote($userInfo['user_key']));
			if($sids){
				return $this->dao->getAll('SELECT sid,name FROM vp_subject WHERE type=0 AND sid IN ('.$sids.')');
			}
		}
	
		return false;*/
	}
	
	
	public function getStudentZhongKao($userInfo){
		$grade = 10;
		$strQuery = 'select
							 max(view_helu.[sStudentCode]) as sStudentCode, 
							 max(view_helu.[sStudentName]) as sStudentName 
							 FROM '.$this->view_VB_StudentLessonHeLu.' as view_helu 
							 LEFT JOIN view_VB_StudentContract as c ON view_helu.sContractCode = c.sContractCode  
							 WHERE c.CurrentGrade = '.$grade.' AND view_helu.[steacherCode] = '.$this->dao->quote($userInfo['sCode']);
		
		$strQuery .= ' GROUP BY view_helu.[sStudentCode] ';
		$stuCodeArr = array();
		$list = $this->dao->getAll($strQuery);
		return $list;
	}
	
	
	public function saveScore($data){
		$strQuery = 'INSERT INTO vp_zhongkao_score (student_code,
													student_name,
													subject_name,
													score,
													up_score,
													total_score,
													user_key,
													teacher_name,
													instime) 
												VALUES (
													'.$this->dao->quote($data['student_code']).',
													'.$this->dao->quote($data['student_name']).',
													'.$this->dao->quote($data['subject_name']).',
													'.$this->dao->quote($data['score']).',
													'.$this->dao->quote($data['up_score']).',
													'.$this->dao->quote($data['total_score']).',
													'.$this->dao->quote($data['user_key']).',
													'.$this->dao->quote($data['teacher_name']).',
													'.$this->dao->quote(date('Y-m-d H:i:s')).'
												)';
		if($this->dao->execute($strQuery)){
			return true;
		}
		return false;
	}
	
	
	public function checkIsRecord($data){
	
		$count = $this->dao->getOne('SELECT COUNT(1) FROM vp_zhongkao_score WHERE student_code = '.$this->dao->quote($data['student_code']).' AND subject_name ='.$this->dao->quote($data['subject_name']));
		
		if($count){
			return true;
		}
		return false;
	}
	
	
	public function getScoreByStudent($stuCode){
		return $this->dao->getAll('SELECT * FROM vp_zhongkao_score where student_code ='.$this->dao->quote($stuCode));
	}
	
	public function getScoreStudent(){
		$studentList = $this->dao->getAll('SELECT max(student_code) student_code,max(student_name) student_name,max(total_score) total_score FROM vp_zhongkao_score group by student_code');
		if($studentList){
			$subjectList = C('ZHONGKAO_SUBJECT');
			foreach ($studentList as $key=>$student){
				foreach ($subjectList as $subject){
					$teacherAll = $this->dao->getAll('SELECT max(sTeacherName) teachername FROM '.$this->view_VB_StudentLessonHeLu.' view_helu LEFT JOIN V_D_Subject s ON view_helu.sSubjectCode = s.sCode AND s.nXueBu =2 where s.sName like \'%'.$subject.'%\' and  view_helu.sStudentCode = '.$this->dao->quote($student['student_code']).' and view_helu.sTeacherName is not NULL group by sTeacherName');
					$studentList[$key][$subject.'teacher_name'] = array();
					if($teacherAll){
						foreach ($teacherAll as $k=>$teacher){
							$studentList[$key][$subject.'teacher_name'][] = $teacher['teachername'];
						}
					}
				
				}
			}
		}
		return $studentList;
	}
}
?>