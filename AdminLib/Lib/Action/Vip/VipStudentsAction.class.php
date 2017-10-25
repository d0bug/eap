<?php
/*我的学员*/
import('ORG.Util.Session');
Session::start ();
import('COM.MsgSender.SmsSender');
import('ORG.Util.NCache');
class VipStudentsAction extends VipCommAction{
	protected function notNeedLogin() {
		return array('VIP-VIPSTUDENTS-UPLOADRECORD');
	}

	public function index(){
		$key_name = isset($_GET['key_name'])?trim($_GET['key_name']):'';
		$order = isset($_GET['order'])?strtolower(trim($_GET['order'])):'asc';
		$userInfo = VipCommAction::get_currentUserInfo();
		if($userInfo['user_key'] == 'Employee-wangyan'){
			$userInfo['sCode'] = 'VP00022';
		}
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$studentsModel = D('VpStudents');
		$condition = '';

		if($userInfo['sCode']){
			$now = date('Y-m-d H:i:s');
			$myStudentList = $studentsModel->get_myStudentList(array('teacherCode'=>$userInfo['sCode'],'key_name'=>$key_name,'order'=>$order,'now'=>$now,'overdue'=>0),1,$curPage,$pagesize);
			$count = $studentsModel->get_myStudentCount(array('teacherCode'=>$userInfo['sCode'],'now'=>$now,'overdue'=>0));
			$page = new page($count,$pagesize);
			$showPage = $page->show();
		}else{
			echo '您不是VIP教师,没有相应学员';die;
		}

		$this->assign(get_defined_vars());
		$this->display();
	}

	function object_array($array) {
		if(is_object($array)) {
			$array = (array)$array;
		} if(is_array($array)) {
			foreach($array as $key=>$value) {
				$array[$key] = object_array($value);
			}
		}
		return $array;
	}

	public function studentInfo(){
		$student_code = isset($_GET['student_code'])?$_GET['student_code']:'';
		$kecheng_code = isset($_GET['kecheng_code'])?$_GET['kecheng_code']:'';
		$lesson = isset($_GET['lesson'])?abs($_GET['lesson']):0;
		if(!empty($student_code)){
			$userInfo = $this->loginUser->getInformation();
			$userInfo['user_key'] = $this->loginUser->getUserKey();
			if(!$userInfo['user_type']){
				$userInfo['user_type'] = $this->loginUser->getUserType();
			}
			$userInfo = $this->getUserOtherInfo($userInfo);
			if($userInfo['user_key'] == 'Employee-wangyan'){
				$userInfo['sCode'] = 'VP00022';
			}
			$studentsModel = D('VpStudents');
			$studentInfo = $studentsModel->get_studentContractInfo($student_code);
			//留言板
			$heluInfo = $studentsModel->get_heluInfo(array('kecheng_code'=>$kecheng_code,'lesson_no'=>$lesson,'student_code'=>$student_code));

			//已上课程
			import("ORG.Util.Page");
			$curPage = isset($_GET['p'])?abs($_GET['p']):1;
			$pagesize = C('PAGESIZE');
			$condition = " helu.[student_code] = '$student_code' ";
			if(!empty($kecheng_code)){
				$condition .= " AND helu.[kecheng_code] = '$kecheng_code'";
			}
			$heluList = $studentsModel->get_heluList($condition,$curPage,$pagesize);//print_r($heluList);
			$count = $studentsModel->get_heluListCount($condition);
			$page = new page($count,$pagesize);
			$showPage = $page->show();

			//培养方案
			$programList = $studentsModel->get_programList($student_code,$userInfo['sCode']);
			$kechengList = $studentsModel->get_kechengAll(array('is_jieke'=>0,'studentCode'=>$student_code,'teacherCode'=>$userInfo['sCode']));

		}
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function keChengHeLu(){
		$userInfo = $this->loginUser->getInformation();
		$studentsModel = D('VpStudents');
		$act = $_GET['act'];
		if($act == 'add'){
			if(!empty($_GET['helu_id'])){
				$heluInfo = $studentsModel->get_heluInfo(array('helu_id'=>abs($_GET['helu_id'])));
			}
			$heluInfo['helu_id'] = abs($_GET['helu_id']);
			$heluInfo['student_code'] = trim($_GET['student_code']);
			$heluInfo['student_name'] = urldecode($_GET['student_name']);
			$heluInfo['kecheng_code'] = trim($_GET['kecheng_code']);
			$heluInfo['lesson_no'] = abs($_GET['lesson_no']);
			$heluInfo['lesson_date'] = trim($_GET['lesson_date']);
			$heluInfo['lesson_begin'] = trim(urldecode($_GET['lesson_begin']));
			$heluInfo['lesson_end'] = trim(urldecode($_GET['lesson_end']));
			//$heluInfo['handouts_count'] = 0;
		}else{
			$heluInfo = $studentsModel->get_heluInfo(array('helu_id'=>abs($_GET['helu_id']),'kecheng_code'=>$_GET['kecheng_code'],'lesson_no'=>$_GET['lesson_no'],'student_code'=>$_GET['student_code']));
			$heluInfo['comment'] = str_replace("<br>","\n",str_replace("&nbsp;"," ",$heluInfo['comment']));
		}
		$this->assign(get_defined_vars());
		$this->display("keChengHeLu");
	}


	protected function doHelu(){
		$studentsModel = D('VpStudents');
		$heluInfo = $studentsModel->get_heluInfo(array('helu_id'=>$_POST['helu_id'],'kecheng_code'=>$_POST['kecheng_code'],'lesson_no'=>$_POST['lesson_no'],'student_code'=>$_POST['student_code']));
		$userInfo = VipCommAction::get_currentUserInfo();
		if($studentsModel->do_helu($userInfo)){
			$status = 1;
			$msg = ($_POST['act']=='again'||$_POST['act']=='add')?'课时核录成功':'课时核录信息修改成功';
			if($_POST['is_sendsms'] == 1 && empty($_POST['is_send_sms'])){
				//给家长发短信
				$studentInfo = $studentsModel->get_studentContractInfo($_POST['student_code']);
				$to_mobile = !empty($studentInfo['sparents1phone'])?$studentInfo['sparents1phone']:$studentInfo['sparents2phone'];
				if(!empty($to_mobile)){
					//$to_mobile = '18210424918';
					$smsObj = new SmsSender();
					$smsContent = "家长您好，您的孩子".$_POST['student_name']."本次上课时间".date('Y/m/d',strtotime($_POST['lesson_date']))." ".$_POST['lesson_begin']."-".$_POST['lesson_end']."。本讲内容是".$_POST['lesson_topic']."，".$userInfo['real_name']."老师课堂评价如下：“".$_POST['comment']."”。感谢您对高思1对1的支持！";
					$smsReturn = $smsObj->sendSms($to_mobile,$smsContent);
				}
			}
			//录入课评统计
			$arr['student_name'] = $_POST['student_name'];
			$arr['lesson_date'] = $_POST['lesson_date'];
			$arr['lesson_topic'] = $_POST['lesson_topic'];
			$arr['teacher_name'] = $userInfo['real_name'];
			$arr['comment'] = $_POST['comment'];
			$arr['helu_time'] = date('Y-m-d H:i:s');
			$arr['helu_type'] = ($_POST['act']=='again'||$_POST['act']=='add')?1:2;
			$arr['is_select_sendsms'] = !empty($_POST['is_sendsms'])?1:0;
			$arr['is_trigger_sendsms'] = ($smsReturn==true)?1:0;
			$arr['is_upload_handouts'] = !empty($_POST['handouts_url'])?1:0;
			$arr['to_mobile'] = $to_mobile;
			$studentsModel->addHeluLog($arr);
		}else{
			$status = 0;
			$msg = ($_POST['act']=='again'||$_POST['act']=='add')?'课时核录失败':'课时核录信息修改失败';
		}
		echo json_encode(array('status'=>$status,'msg'=>$msg));
	}

	public function heLuInfo(){
		$heluInfo = D('VpStudents')->get_heluInfo(array('kecheng_code'=>$_GET['kecheng_code'],'lesson_no'=>$_GET['lesson_no'],'student_code'=>$_GET['student_code']));
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function download(){
		if(!empty($_GET['id'])){
			$studentsModel = D('VpStudents');
			if($_GET['type'] == 3){//下载培养方案
				$programInfo = $studentsModel->get_programInfo(array('id'=>abs($_GET['id'])));
				$source_url = $programInfo['program'][$_GET['order']]['url'];
				$toDownloadFile = str_replace("'","",str_replace(' ','_',$programInfo['student_name'].$programInfo['kecheng_name'].'培养方案'.$programInfo['instime'].'('.($_GET['order']+1).')'.'.'.end(explode('.',$source_url))));
				$returnUrl = U('Vip/VipStudents/index');
			}else{//下载讲义和习题
				$heluInfo = $studentsModel->get_heluInfo(array('id'=>abs($_GET['id'])));
				if($_GET['type'] == 0){
					$source_url = $heluInfo['handouts'][$_GET['order']]['url'];
					$fileName = $heluInfo['handouts_title'];
				}else{
					$source_url = $heluInfo['itembank'][$_GET['order']]['url'];
					$fileName = $heluInfo['itembank_title'];
				}
				$toDownloadFile = str_replace("'","",str_replace(' ','_',$fileName.'('.$_GET['order'].').'.end(explode('.',$source_url))));
				$returnUrl = U('Vip/VipStudents/studentInfo',array('student_code'=>$heluInfo['student_code'],'kecheng_code'=>$heluInfo['kecheng_code'],'lesson'=>$heluInfo['lesson_no']));
			}
			if(!empty($source_url)){
				if(file_exists(APP_DIR.$source_url)){
					VipCommAction::download_file(APP_DIR.$source_url,$toDownloadFile);
				}else{
					$this->error('文件不存在');
				}
			}
		}else{
			$this->error('非法操作');
		}
	}



	public function downloadProgramImg(){
		$programId = $_GET['id'];
		$programInfo = D('VpStudents')->get_programInfo(array('id'=>abs($_GET['id'])));
		$source_url = $programInfo['program_img'];
		$toDownloadFile = str_replace("'","",str_replace(' ','_',$programInfo['student_name'].$programInfo['kecheng_name'].'培养方案'.$programInfo['instime'].'.'.end(explode('.',$source_url))));
		if(!empty($source_url)){
			if(file_exists(APP_DIR.$source_url)){
				VipCommAction::download_file(APP_DIR.$source_url,$toDownloadFile);
			}else{
				$this->error('文件不存在');
			}
		}
	}


	public function addTrainingProgram(){
		$status = 0;
		$html = '';
		if(!empty($_POST['kecheng_code']) &&!empty($_POST['kecheng_name']) &&!empty($_POST['student_code']) && !empty($_POST['student_name']) && !empty($_POST['url'])){
			$studentsModel = D('VpStudents');
			$userKey = $this->loginUser->getUserKey();
			$userInfo = $this->getUserInfoFull();
			$ip = VipCommAction::getClientIp();
			if($studentsModel->add_trainingProgram($_POST,$userKey,$ip)){
				$msg = '培养方案上传成功';
				$status = 1;
				$programList = $studentsModel->get_programList($_POST['student_code'],$userInfo['sCode']);
				if(!empty($programList)){
					$html .= '<table border="1" width="80%">
								<tr>
									<th align="center">上传时间</th>
									<th align="center">所属课程</th>
									<th align="center">辅导方案</th>
									<th align="center">上传方式</th>
									<th align="center">操作</th>
								</tr>';
					foreach ($programList as $key=>$program){
						$html .= '<tr id="p_'.$key.'">
									  <td>'.$program['instime'].'</td>
									  <td>'.$program['kecheng_name'].'</td>
									  <td>
									  	<img src="/static/images/';
						switch ($program['file_type']){
							case 'doc':
							case 'docx':
								$html .= 'doc.gif';
								break;
							case 'ppt':
							case 'pptx':
								$html .= 'ppt.png';
								break;
							case 'xls':
							case 'xlsx':
								$html .= 'xls.png';
								break;
							case 'pdf':
								$html .= 'pdf.gif';
								break;
							default:
								$html .= 'file.png';
						}
						$html .= '"><a href="';
						$html .= ($program['is_exist']==1)?U('Vip/VipStudents/download',array('id'=>$program['id'],'type'=>'3')):'#none';
						$html .= '" ';
						$html .= ($program['is_exist']==0)?'title="文件不存在" onclick="javascript:alert(\'文件不存在\');">':'>';
						$html .= $program['student_name'].'培养方案'.$program['instime'].'</a> </td><td>';
						if($program['from_type']==1){
							$html .= '微信';
						}else{
							$html .= 'PC';
						}
						$html .= '</td><td><a href="#none" class="blue" onclick="del_program(\'#p_'.$key.'\',\''.U('Vip/VipStudents/del_program',array('id'=>$program['id'])).'\')">删除</a></td>  </tr>';
					}
					$html .= '</table>';
				}
			}else{
				$msg = '培养方案上传失败';
			}
		}else{
			$msg = '非法操作';
		}
		echo json_encode(array('msg'=>$msg,'status'=>$status,'html'=>$html));
	}


	protected function del_program(){
		if(!empty($_GET['id'])){
			if(D('VpStudents')->del_program($_GET['id'])){
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 0;
		}
	}


	/****我的学员（新版）******************start*****************************************************************************************************/



	//学员详情
	public function newStudentInfo(){
		$student_code = trim($_GET['student_code']);
		if(!empty($student_code)){
			$userInfo = $this->getUserInfoFull();

			$studentsModel = D('VpStudents');
			$newStudentsModel = D('VpNewStudents');

			//学员详情
			$studentInfo = $studentsModel->get_studentContractInfo($student_code);

		}
		$this->assign(get_defined_vars());
		$this->display();
	}


	//学员课程
	public function newStudentLesson(){
		$student_code = trim($_GET['student_code']);
		$userInfo = $this->getUserInfoFull();

		$studentsModel = D('VpStudents');
		$studentInfo = $studentsModel->get_studentContractInfo($student_code);
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$conditionArr =  array('student_code'=>$student_code,'teacherCode'=>$userInfo['sCode']);
		$newStudentsModel = D('VpNewStudents');
		$lessonList = $newStudentsModel->get_lessonList($conditionArr,$curPage,$pagesize);
		$count = $newStudentsModel->get_lessonCount($conditionArr);
		$page = new page($count,$pagesize);
		$showPage = $page->show();

		$this->assign(get_defined_vars());
		$this->display();
	}


	//阶段培养方案
	public function newStudentProgram(){
		$student_code = trim($_GET['student_code']);
		$userInfo = $this->getUserInfoFull();

		$studentsModel = D('VpStudents');
		$studentInfo = $studentsModel->get_studentContractInfo($student_code);

		$programList = $studentsModel->get_programList($student_code,$userInfo['sCode']);
		$kechengList = $studentsModel->get_kechengAll(array('is_jieke'=>0,'studentCode'=>$student_code,'teacherCode'=>$userInfo['sCode']));

		$this->assign(get_defined_vars());
		$this->display();
	}



	//留言板
	public function newStudentMessage(){
		$student_code = trim($_GET['student_code']);
		$userInfo = $this->getUserInfoFull();

		$studentsModel = D('VpStudents');
		$studentInfo = $studentsModel->get_studentContractInfo($student_code);

		$heluInfo = $studentsModel->get_heluInfo(array('student_code'=>$student_code));

		$this->assign(get_defined_vars());
		$this->display();
	}



	//错题书包
	public function newStudentErrorQuestion(){
		$student_code = trim($_GET['student_code']);
		$userInfo = $this->getUserInfoFull();

		$studentsModel = D('VpStudents');
		$studentInfo = $studentsModel->get_studentContractInfo($student_code);

		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$conditionArr =  array('student_code'=>$student_code,'teacherCode'=>$userInfo['sCode']);
		$start = trim($_GET['start']);
		$end = trim($_GET['end']);
		$lesson_topic = trim($_GET['lesson_topic']);
		$type = $_GET['type'];
		if(!empty($start)){
			$conditionArr['start'] = $start;
		}
		if(!empty($end)){
			$conditionArr['end'] = $end;
		}
		if(!empty($lesson_topic)){
			$conditionArr['lesson_topic'] = $lesson_topic;
		}
		if(!empty($type)){
			$conditionArr['type'] = $type;
		}
		$newStudentsModel = D('VpNewStudents');
		$errorQuestionList = $newStudentsModel->get_errorQuestionList($conditionArr,0,$curPage,$pagesize);
		$count = $newStudentsModel->get_errorQuestionCount($conditionArr);
		$page = new page($count,$pagesize);
		$showPage = $page->show();

		$optionKeyArr = C('OPTIONS_KEY');
		$this->assign(get_defined_vars());
		$this->display();
	}



	//删除错题记录
	public function deleteErrorQuestion(){
		$error_id = $_POST['eid'];
		$status = 0;
		$msg = '错题记录删除失败';
		if(!empty($error_id)){
			if(D('VpNewStudents')->delete_errorQuestion($error_id)){
				$status = 1;
				$msg = '错题记录删除成功';
			}
		}
		echo json_encode(array('status'=>$status,'msg'=>$msg));
	}



	//导出错题书包
	public function exportErrorQuestion(){
		ob_start();
		$student_code =  trim($_GET['student_code']);
		$userInfo = $this->getUserInfoFull();
		$conditionArr =  array('student_code'=>$student_code,'teacherCode'=>$userInfo['sCode']);
		if(!empty($_GET['start'])){
			$conditionArr['start'] = trim($_GET['start']);
		}
		if(!empty($_GET['end'])){
			$conditionArr['end'] = trim($_GET['end']);
		}
		if(!empty($_GET['lesson_topic'])){
			$conditionArr['lesson_topic'] = trim($_GET['lesson_topic']);
		}
		$newStudentsModel = D('VpNewStudents');
		$errorQuestionList = $newStudentsModel->get_errorQuestionList($conditionArr,1);
		$optionKeyArr = C('OPTIONS_KEY');
		$html = '<div id="list" class="clearfix" style="padding:20px 30px">';
		if(!empty($errorQuestionList)){
			foreach ($errorQuestionList as $key=>$errorQuestion){
				if($errorQuestion['type']==1){
					$type = '例题';
				}elseif ($errorQuestion['type']==2){
					$type = '随堂练习';
				}elseif ($errorQuestion['type']==3){
					$type = '作业';
				}
				$html .= '<div class="title" style="margin-top:50px">
								<span>上课日期：'.$errorQuestion['dtdatereal'].'</span>
								<span style="margin-left:80px">　　　课次主题：'.$errorQuestion['lesson_topic'].'</span>
								<span style="margin-left:80px">　　　课程名称：'.$errorQuestion['skechengname'].'（'. $errorQuestion['skechengcode'].'）</span>
								<span style="margin-left:80px">　　　错题类型：'.$type.'</span>
						  </div>
						  <div id="question_'.$key.'" style="cursor:pointer" class="bd clearfix">
						  <div class="con">
								<table>
									<tbody>
										<tr>
											<td valign="top" >
												<dl class="opt">
													<dt>'.($key+1).'</dt>
													<dd>、</dd>
												</dl>
											</td>
											<td>'.$errorQuestion['question_desc']['content'].'</td>
										</tr>
									</tbody>
								</table>';
				if(!empty($errorQuestion['question_option'])){
					$html .= '<table >';
					foreach ($errorQuestion['question_option'] as $k=>$option){
						$html .= '<tbody>
													<tr>
														<td style="padding-left:40px">
															<dl class="opt">
																<dt>'.$optionKeyArr[$option['sort']].'</dt>
																<dd>．</dd>
															</dl>
														</td>
														<td>'.$option['content'].'</td>
													</tr>
												</tbody>';
					}
					$html .= '</table>';
				}
				$html .= '<div id="analysis_'.$key.'" class="answer pointer" >
										<div class="box">
											<table>
												<tbody>
													<tr><td valign="middle" style="height: 28px;">【答案】</td></tr>
													<tr><td style="padding-left:40px">'.$errorQuestion['question_desc']['answer_content'].'</td></tr>
													<tr><td valign="middle" style="height: 28px;">【解析】</td></tr>
													<tr><td style="padding-left:40px">'.$errorQuestion['question_desc']['analysis'].'</td></tr>
												</tbody>
											</table>
										</div>
									  </div>
								</div>
							</div>';
			}

		}

		ob_start();
		echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">'.$html.'</html>';
		$data = ob_get_contents();
		ob_end_clean();

		$targetFolder = UPLOAD_PATH.date('Y-m-d').'/';
		if(!file_exists($targetFolder)){
			umask(0);
			mkdir($targetFolder,0777);
		}
		$docPath = $targetFolder.urldecode($_GET['student_name']).'的错题书包.doc';
		$fp = fopen($docPath, "wb");
		fwrite($fp, $data);
		fclose($fp);
		header("location:".str_replace('/Upload/','/upload/',end(explode('/eap',$docPath))));
	}



	//记录上课轨迹
	public function recordLessonTrack(){
		$userInfo = $this->loginUser->getInformation();
		$helu_id = abs($_GET['helu_id']);
		$heluInfo = $this->getHeluInfo($helu_id);
		$heluInfo['comment'] = str_replace("<br>","\r\n",$heluInfo['comment']);
		if(empty($heluInfo['lecture_id'])){
			$this->error('请先进行备课',U('Vip/VipStudents/newStudentLesson',array('student_code'=>$heluInfo['sstudentcode'])));
		}
		$newStudentsModel = D('VpNewStudents');
		//上次课程id
		$last_helu_id = $this->getLastHeluId($heluInfo);
		$last_lesson_heluInfo = $this->getHeluInfo($last_helu_id);

		//获取星级
		$levelArr = D('VpSubject')->get_levelList();
		$numberKey = C('NUMBER_KEY');
		$optionKeyArr = C('OPTIONS_KEY');

		$module_count = 0;
		$practise_count = 0;
		$work_count = 0;


		$this->assign(get_defined_vars());
		$this->display();
	}


	public function getHeluInfo($heluId){
		$key = md5($heluId);
		$cache = NCache::getCache();
		$heluInfo = $cache->get('heluInfo', $key);
		if(false == $heluInfo || empty($heluInfo['lecture_id'])) {
			$newStudentsModel = D('VpNewStudents');
			$heluInfo = $newStudentsModel->get_heluInfo($heluId);
			$cache->set('heluInfo', $key, $heluInfo);
		}
		return $heluInfo;
	}


	//保存上课主题和答题情况
	public function savePartOne(){
		$status = 0;
		$msg = '';
		if(!empty($_POST['lesson_topic'])){
			$newStudentsModel = D('VpNewStudents');
			$return = $newStudentsModel->recordLessonTrack($_POST);
			if($return){
				$status = 1;
				//更新缓存
				$key = md5($_POST['helu_id']);
				$cache = NCache::getCache();
				$heluInfo = $this->getHeluInfo($_POST['helu_id']);
				$newHeluInfo = $newStudentsModel->get_baseHeluInfo($_POST['helu_id']);
				$newHeluInfo['lecture_info'] = $heluInfo['lecture_info'];

				//$newStudentsModel = D('VpNewStudents');
				//$heluInfo = $newStudentsModel->get_heluInfo($_POST['helu_id']);
				$cache->set('heluInfo', $key, $newHeluInfo);

				$last_helu_id = $this->getLastHeluId($heluInfo);
				$last_lesson_heluInfo = $this->getHeluInfo($last_helu_id);
				$new_last_lesson_heluInfo = $newStudentsModel->get_baseHeluInfo($last_helu_id);
				$new_last_lesson_heluInfo['lecture_info'] = $last_lesson_heluInfo['lecture_info'];
				//$last_lesson_heluInfo = $newStudentsModel->get_heluInfo($last_helu_id);
				$last_key = md5($last_helu_id);
				$cache->set('heluInfo', $last_key, $new_last_lesson_heluInfo);
				$msg = '课次主题和课堂掌握情况保存成功';
			}else{
				$msg = '课次主题和课堂掌握情况保存失败';
			}
		}else{
			$msg = '请填写课次主题';
		}

		echo json_encode(array('status'=>$status,'msg'=>$msg));
	}



	//保存课堂评价
	public function savePartTwo(){
		$status = 0;
		$msg = '';
		if(!empty($_POST['helu_id']) && !empty($_POST['comment']) && !empty($_POST['dimension_id_str']) && !empty($_POST['level_str'])){
			$newStudentsModel = D('VpNewStudents');
			$studentsModel = D('VpStudents');
			$return = $newStudentsModel->recordLessonComment($_POST);
			if($return){
				if($_POST['is_send_sms']==1){
					//给家长发短信
					$userInfo = $this->getUserInfoFull();
					$studentInfo = D('VpStudents')->get_studentContractInfo($_POST['student_code']);
					$to_mobile = !empty($studentInfo['sparents1phone'])?$studentInfo['sparents1phone']:$studentInfo['sparents2phone'];
					if(!empty($to_mobile)){
						//$to_mobile = '13810410955';
						$smsObj = new SmsSender();
						$smsContent = "家长您好，您的孩子".$_POST['student_name']."本次上课时间".date('Y/m/d',strtotime($_POST['lesson_date']))." ".$_POST['lesson_begin']."-".$_POST['lesson_end']."。本讲内容是".$_POST['lesson_topic']."，".$userInfo['real_name']."老师课堂评价如下：“".$_POST['comment']."”。感谢您对高思1对1的支持！";
						$smsReturn = $smsObj->sendSms($to_mobile,$smsContent);
					}
				}
				//录入课评统计
				$arr['student_name'] = $_POST['student_name'];
				$arr['lesson_date'] = $_POST['lesson_date'];
				$arr['lesson_topic'] = $_POST['lesson_topic'];
				$arr['teacher_name'] = $userInfo['real_name'];
				$arr['comment'] = $_POST['comment'];
				$arr['helu_time'] = date('Y-m-d H:i:s');
				$arr['helu_type'] = ($_POST['act']=='add')?1:2;
				$arr['is_select_sendsms'] = !empty($_POST['is_send_sms'])?1:0;
				$arr['is_trigger_sendsms'] = ($smsReturn==true)?1:0;
				$arr['is_upload_handouts'] = 0;
				$arr['to_mobile'] = $to_mobile;
				$studentsModel->addHeluLog($arr);


				$status = 1;
				//更新缓存
				$key = md5($_POST['helu_id']);
				$cache = NCache::getCache();
				$newStudentsModel = D('VpNewStudents');
				$heluInfo = $this->getHeluInfo($_POST['helu_id']);
				$newHeluInfo = $newStudentsModel->get_baseHeluInfo($_POST['helu_id']);
				$newHeluInfo['lecture_info'] = $heluInfo['lecture_info'];
				$cache->set('heluInfo', $key, $newHeluInfo);
				$msg = '课堂评价保存成功';
			}else{
				$msg = '课堂评价保存失败';
			}
		}else{
			$msg = '请填写课堂评价';
		}

		echo json_encode(array('status'=>$status,'msg'=>$msg));
	}


	public function uploadRecord(){
		if (!empty($_FILES) && !empty($_POST['helu_id'])) {
			$targetFolder = UPLOAD_PATH.date('Y-m-d').'/';
			if(!file_exists($targetFolder)){
				mkdir($targetFolder,0777);
			}
			$tempFile = $_FILES['Filedata']['tmp_name'];
			$fileParts = pathinfo($_FILES['Filedata']['name']);
			$imgTypeArr = array('jpg','jpeg','gif','png');

			$prename = trim($_POST['prename'],'_');
			if(!empty($prename)) $prename .= '_';
			$uniqidname = uniqid(mt_rand(), true);
			if($_POST['is_realname'] == 1){
				$newFilename = $prename.pathinfo(str_replace("(","（",str_replace(")","）",$_FILES['Filedata']['name'])), PATHINFO_FILENAME).'_'.$uniqidname.".".strtolower($fileParts['extension']);
			}else{
				$newFilename = $uniqidname.".".strtolower($fileParts['extension']);
			}
			$targetFile =$targetFolder.$newFilename ;
			if (in_array(strtolower($fileParts['extension']),$imgTypeArr)) {
				if(move_uploaded_file($tempFile,$targetFile)){
					$autocut = isset($_POST['autocut'])?$_POST['autocut']:0;
					list($imgWidth,$imgHeight) = getimagesize($targetFile);
					if($imgWidth<$_POST['width']){
						$thumb_file = $targetFile;//返回原尺寸
					}else{
						$thumb_file = AppCommAction::thumb_img($targetFile,$_POST['width'],$_POST['height'],$autocut);
					}

					if(D('VpNewStudents')->uploadRecordImg($_POST['helu_id'],'/'.end(explode('/eap/','/'.$thumb_file)))){
						//更新缓存
						$key = md5($_POST['helu_id']);
						$cache = NCache::getCache();
						$newStudentsModel = D('VpNewStudents');

						/*$heluInfo = $newStudentsModel->get_heluInfo($_POST['helu_id']);
						$cache->set('heluInfo', $key, $heluInfo);
						$last_helu_id = $this->getLastHeluId($heluInfo);
						$last_lesson_heluInfo = $newStudentsModel->get_heluInfo($last_helu_id);
						$last_key = md5($last_helu_id);
						$cache->set('heluInfo', $last_key, $last_lesson_heluInfo);*/

						$heluInfo = $this->getHeluInfo($_POST['helu_id']);
						$newHeluInfo = $newStudentsModel->get_baseHeluInfo($_POST['helu_id']);
						$newHeluInfo['lecture_info'] = $heluInfo['lecture_info'];
						$cache->set('heluInfo', $key, $newHeluInfo);

						echo json_encode(array('status'=>'上传成功','url'=>'/'.end(explode('/eap/','/'.$thumb_file)),'show_url'=>'/'.end(explode('Upload/',$thumb_file)),'delimg_url'=>U('Vip/VipStudents/del_img')));
					}else{
						@unlink($thumb_file);
						echo json_encode(array('status'=>'上传失败'));
					}

				}else{
					echo json_encode(array('status'=>'上传失败'));
				}
			} else {
				echo json_encode(array('status'=>'不支持的文件类型'));
			}
		}
	}



	/*删除图片*/
	public function del_img(){
		if(!empty($_POST['url'])){
			$file_type = end(explode(".",$_POST['url']));
			$newStudentsModel = D('VpNewStudents');
			if($newStudentsModel->deleteRecordImg($_POST['helu_id'],$_POST['url'])){
				@unlink(APP_DIR.$_POST['url']);

				//更新缓存
				$key = md5($_POST['helu_id']);
				$cache = NCache::getCache();

				/*$heluInfo = $newStudentsModel->get_heluInfo($_POST['helu_id']);
				$cache->set('heluInfo', $key, $heluInfo);*/
				$heluInfo = $this->getHeluInfo($_POST['helu_id']);
				$newHeluInfo = $newStudentsModel->get_baseHeluInfo($_POST['helu_id']);
				$newHeluInfo['lecture_info'] = $heluInfo['lecture_info'];
				$cache->set('heluInfo', $key, $newHeluInfo);
				echo '1';
			}else{
				echo '0';
			}
		}else{
			echo '0';
		}
	}





	public function createLessonReport(){
		$helu_id = $_REQUEST['helu_id'];
		$status = 0;
		$report_url = '';
		if(!empty($helu_id)){
			if($_POST['from']=='wx'){
				$userInfo['real_name'] = $_POST['teacher_name'];
			}else{
				$userInfo = $this->getUserInfoFull();
			}
			$newStudentsModel = D('VpNewStudents');
			$heluInfo = $this->getHeluInfo($helu_id);
			$now = date('Y-m-d H:i:s');
			$lessonTime = date('Y-m-d',strtotime($heluInfo['dtdatereal'])).' '.date('H:i',strtotime($heluInfo['dtlessonbeginreal'])).'至'.date('H:i',strtotime($heluInfo['dtlessonendreal']));
			$beike_time = !empty($heluInfo['lecture_info']['created_time'])?date('Y-m-d H:i:s',$heluInfo['lecture_info']['created_time']):'';
			$shangke_time = date('Y-m-d',strtotime($heluInfo['dtdatereal'])).' '.date('H:i',strtotime($heluInfo['dtlessonbeginreal'])).'~'.date('H:i',strtotime($heluInfo['dtlessonendreal']));
			$record_time = !empty($heluInfo['lesson_report_createtime'])?$heluInfo['lesson_report_createtime']:$now;
			$comment_time = '';

			//上次课程id
			$last_helu_id = $this->getLastHeluId($heluInfo);
			$last_lesson_heluInfo = $this->getHeluInfo($last_helu_id);

			//获取星级
			$levelArr = D('VpSubject')->get_levelList();
			$levelCount = count($levelArr);
			$numberKey = C('NUMBER_KEY');
			$jsInfoUrl = "'".APP_URL."/Vip/Weixin/getSignaturePackage'";
			$share_title = $heluInfo['sstudentname'].'课节报告_'.date('Y.m.d',time());
			$share_desc = "老师说：".mb_substr(str_replace("<br>","",$heluInfo['comment']),0,15,'utf-8')."...（点击查看报告）";

			//判断服务流程
			$style1 = '';
			$style2 = '';
			$style3 = '';
			$style4 = '';
			if(!empty($heluInfo['lecture_info'])){
				if($heluInfo['dtlessonbeginreal']<=$now){
					if(!empty($heluInfo['module_answer'])||!empty($heluInfo['practise_answer'])||!empty($heluInfo['work_answer'])||!empty($heluInfo['lesson_report_url'])||!empty($heluInfo['lesson_record_img'])){
						$style3 = 'on';
					}else{
						$style2 = 'on';
					}
				}else{
					$style1 = 'on';
				}
			}
			
			$dimensionCommentHtml = '';
			$dimensionCommentHtml_wx = '';
			//$levelArr = D('VpSubject')->get_levelList();
			$tempLevel = array();
			if(!empty($heluInfo['dimension'])){
				foreach ($heluInfo['dimension'] as $key=>$dimension){
					$tempLevel[] = $dimension['level'];
					$dimensionCommentHtml .= '<li>'.$dimension['title'].'：<span style="display: inline-block;height: 32px;overflow: hidden;vertical-align: middle;width: 136px;">';
					$dimensionCommentHtml_wx .= '<li>'.$dimension['title'].'：<span style="display: inline-block;height: 32px;overflow: hidden;vertical-align: middle;width: 136px;">';
					$onNum = $dimension['level'];
					$offNum = $levelCount-$onNum;
					for($i=0;$i<$onNum;$i++){
						$dimensionCommentHtml .= '<img src="/static/images/star-on2.jpg">&nbsp;';
						$dimensionCommentHtml_wx .= '<img src="/static/images/star-on2.jpg">&nbsp;';
					}
					for($i=0;$i<$offNum;$i++){
						$dimensionCommentHtml_wx .= '<img src="/static/images/star-off2.jpg">&nbsp;';
					}
					$dimensionCommentHtml .= '</span></li>';
					$dimensionCommentHtml_wx .= '</span></li>';
				}
			}

			//课堂评价话术转换
			$sid = $heluInfo['lecture_info']['subject_id'];
			$templateId = $newStudentsModel->getRandTemplateId(array('sid'=>$sid));
			$previewText = D('VpSubject')->get_templatePreview($templateId,$tempLevel);
			$previewText = str_replace('XXX',$heluInfo['sstudentname'],$previewText);

			//错题记录
			$lastWorkErrorHtml = '';
			$moduleAnswerErrorHtml = '';
			$practiseAnswerErrorHtml = '';
			$right_question_arr = array();
			$used_question_arr = array();
			if(!empty($heluInfo['module_answer'])){
				foreach ($heluInfo['module_answer'] as $k=>$m){
					$moduleAnswerErrorHtml .= ($m === '1'||$m === '0')?'<li>'.($k+1).'</li>':'';
					if($m == '2'){
						$right_question_arr[] = $heluInfo['lecture_info']['question_list']['module_question'][$k];
					}
					if($m != -1){
						$used_question_arr[] = $heluInfo['lecture_info']['question_list']['module_question'][$k];
					}
				}
			}
			if(!empty($heluInfo['practise_answer'])){
				foreach ($heluInfo['practise_answer'] as $k=>$p){
					$practiseAnswerErrorHtml .= ($p === '1'||$p === '0')?'<li>'.($k+1).'</li>':'';
					if($p == '2'){
						$right_question_arr[] = $heluInfo['lecture_info']['question_list']['practise'][$k]['id'];
					}
					if($p != -1){
						$used_question_arr[] = $heluInfo['lecture_info']['question_list']['practise'][$k]['id'];
					}
				}
			}
			if(!empty($heluInfo['work_answer'])){
				foreach ($heluInfo['work_answer'] as $k=>$w){
					if($w == '2'){
						$right_question_arr[] = $heluInfo['lecture_info']['question_list']['work'][$k]['id'];
					}
					if($w != -1){
						$used_question_arr[] = $heluInfo['lecture_info']['question_list']['work'][$k]['id'];
					}
				}
			}

			if(!empty($last_lesson_heluInfo['work_answer'])){
				foreach ($last_lesson_heluInfo['work_answer'] as $k=>$w){
					$lastWorkErrorHtml .= ($w === '1'||$w === '0')?'<li>'.($k+1).'</li>':'';
				}
			}

			//本次课知识点和知识点解析
			$knowledgeListHtml1 = '';
			$knowledgeListHtml2 = '';
			$knowledgeTipsHtml = '';
			if(!empty($heluInfo['lecture_info']['config']['struct']['body']['special']['types'])){
				$temp = 1;
				$knowledge_num = 0;
				foreach ($heluInfo['lecture_info']['config']['struct']['body']['special']['types'] as $key=>$knowledge){
					$total_num = 0;
					if(!empty($heluInfo['lecture_info']['question_list']['module'])){
						foreach ($heluInfo['lecture_info']['question_list']['module'] as $kk=>$type){
							if(!empty($type['question_list'])){
								foreach ($type['question_list'] as $k=>$q){
									if($q['knowledge_parent_id'] == $knowledge['id'] && in_array($q['id'],$used_question_arr)){
										$total_num++;
									}
								}
							}

						}
					}
					if(!empty($heluInfo['lecture_info']['question_list']['practise'])){
						foreach ($heluInfo['lecture_info']['question_list']['practise'] as $kk=>$q){
							if($q['knowledge_parent_id'] == $knowledge['id'] && in_array($q['id'],$used_question_arr)){
								$total_num++;
							}
						}
					}

					if(!empty($heluInfo['lecture_info']['question_list']['work'])){
						foreach ($heluInfo['lecture_info']['question_list']['work'] as $kk=>$q){
							if($q['knowledge_parent_id'] == $knowledge['id'] && in_array($q['id'],$used_question_arr)){
								$total_num++;
							}
						}
					}

					if($total_num>0){
						if($temp % 2 ==0)
						{
							$knowledgeListHtml2 .= '<li><i>'.$temp.'、</i>'.$knowledge['title'].'</li>';
						}else
						{
							$knowledgeListHtml1 .= '<li><i>'.$temp.'、</i>'.$knowledge['title'].'</li>';
						}
						$knowledgeTipsHtml .= '<div class="font-family: "Microsoft YaHei","Arial Narrow";color: #333;">'.($numberKey[$temp-1]).'、'.$knowledge['title'].'<br>'.$knowledge['tips'].'</div><br><br><br>';
						$temp++;
						$knowledge_num++;
					}

				}
			}

			//知识点云图
			$knowledgeCloudHtml = '';
			$knowledgeCloudHtml_wx = '';
			$knowledgeCloudList = $newStudentsModel->getKnowledgeCloud($heluInfo);

			if(!empty($knowledgeCloudList)){

				foreach ($knowledgeCloudList as $key=>$knowledgeCloud){
					$cloud_right_question_arr = array();
					$cloud_used_question_arr = array();
					if(!empty($knowledgeCloud['module_answer'])){
						foreach ($knowledgeCloud['module_answer'] as $k=>$m){
							if($m !==''){
								if($m == 2){
									$cloud_right_question_arr[] = $knowledgeCloud['lecture_info']['question_list']['module_question'][$k];
								}
								if($m != -1){
									$cloud_used_question_arr[] = $knowledgeCloud['lecture_info']['question_list']['module_question'][$k];
								}
							}
						}
					}
					if(!empty($knowledgeCloud['practise_answer'])){
						foreach ($knowledgeCloud['practise_answer'] as $k=>$p){
							if($p !==''){
								if($p == 2){
									$cloud_right_question_arr[] = $knowledgeCloud['lecture_info']['question_list']['practise'][$k]['id'];
								}
								if($p != -1){
									$cloud_used_question_arr[] = $knowledgeCloud['lecture_info']['question_list']['practise'][$k]['id'];
								}
							}
						}
					}
					if(!empty($knowledgeCloud['work_answer'])){
						foreach ($knowledgeCloud['work_answer'] as $k=>$w){
							if($w !==''){
								if($w == 2){
									$cloud_right_question_arr[] = $knowledgeCloud['lecture_info']['question_list']['work'][$k]['id'];
								}
								if($w != -1){
									$cloud_used_question_arr[] = $knowledgeCloud['lecture_info']['question_list']['work'][$k]['id'];
								}
							}
						}
					}

					if(!empty($knowledgeCloud['dtdatereal'])){
						$knowledgeCloudHtml .= '<div class="conmapzsd-line';
						$knowledgeCloudHtml .= ($knowledgeCloud['helu_id'] == $heluInfo['helu_id'])?' conmapzsd-new ':'';
						$knowledgeCloudHtml .= '">
												<div class="fl w100 c-999">'.date('Y-m-d',strtotime($knowledgeCloud['dtdatereal'])).' '.date('H:i',strtotime($knowledgeCloud['dtlessonbeginreal'])).'-'.date('H:i',strtotime($knowledgeCloud['dtlessonendreal'])).'</div>
												<div class="fl  conmapdianimg baogao-icon"></div>
												<div class="fl">
												<ul>';

						$knowledgeCloudHtml_wx .= '<div class="conmapzsd-line';
						$knowledgeCloudHtml_wx .= ($knowledgeCloud['helu_id'] == $heluInfo['helu_id'])?' conmapzsd-now ':'';
						$knowledgeCloudHtml_wx .= '"><div class="fl conmapdianImg baogao-icon"></div><div class="fl conmapzsdList">
												<div class="conmapzsdTime c-999">'.date('Y-m-d',strtotime($knowledgeCloud['dtdatereal'])).' '.date('H:i',strtotime($knowledgeCloud['dtlessonbeginreal'])).'-'.date('H:i',strtotime($knowledgeCloud['dtlessonendreal'])).'</div>
												<ul>';

						if(!empty($knowledgeCloud['lecture_info']['config']['struct']['body']['special']['types'])){
							$temp = 1;
							foreach ($knowledgeCloud['lecture_info']['config']['struct']['body']['special']['types'] as $k=>$knowledge){
								$total_num = 0;
								$right_num = 0;
								if(!empty($knowledgeCloud['lecture_info']['question_list']['module'])){
									foreach ($knowledgeCloud['lecture_info']['question_list']['module'] as $kk=>$type){
										if(!empty($type['question_list'])){
											foreach ($type['question_list'] as $k=>$q){
												if($q['knowledge_parent_id'] == $knowledge['id'] && in_array($q['id'],$cloud_used_question_arr)){
													$total_num++;
													if(in_array($q['id'],$cloud_right_question_arr)){
														$right_num++;
													}
												}
											}
										}

									}
								}
								if(!empty($knowledgeCloud['lecture_info']['question_list']['practise'])){
									foreach ($knowledgeCloud['lecture_info']['question_list']['practise'] as $kk=>$q){
										if($q['knowledge_parent_id'] == $knowledge['id'] && in_array($q['id'],$cloud_used_question_arr)){
											$total_num++;
											if(in_array($q['id'],$cloud_right_question_arr)){
												$right_num++;
											}
										}
									}
								}
								if(!empty($knowledgeCloud['lecture_info']['question_list']['work'])){
									foreach ($knowledgeCloud['lecture_info']['question_list']['work'] as $kk=>$q){
										if($q['knowledge_parent_id'] == $knowledge['id'] && in_array($q['id'],$cloud_used_question_arr)){
											$total_num++;
											if(in_array($q['id'],$cloud_right_question_arr)){
												$right_num++;
											}
										}
									}
								}

								if($total_num>0){
									if($knowledgeCloud['helu_id'] == $heluInfo['helu_id']){

										$knowledgeCloudHtml .= '<li><p class="conmapdian-text">'.($temp).'、'.$knowledge['title'].'</p><p >总题'.$total_num.'道；做对'.$right_num.'道；正确率：'.round(($right_num/$total_num*100),2).'%</p></li>';

										$knowledgeCloudHtml_wx .= '<li><p class="conmapdian-text">'.($temp).'、'.$knowledge['title'].'</p><p c-orange2>总题'.$total_num.'道；做对'.$right_num.'道；正确率：'.round(($right_num/$total_num*100),2).'%</p></li>';
									}else{
										$knowledgeCloudHtml .= '<li>'.($temp).'、'.$knowledge['title'].'</li>';
										$knowledgeCloudHtml_wx .= '<li>'.($temp).'、'.$knowledge['title'].'</li>';
									}
									$temp++;
								}
							}
						}
						$knowledgeCloudHtml .= '		</ul>
			</div>
			<div class="clear"></div>
			</div>';
						$knowledgeCloudHtml_wx .= '		</ul>
			</div>
			<div class="clear"></div>
			</div>';
					}

				}
			}


			//本次作业
			$work_num = count($heluInfo['lecture_info']['question_list']['work']);
			$workListHtml = '';
			if(!empty($heluInfo['lecture_info']['question_list']['work'])){
				foreach ($heluInfo['lecture_info']['question_list']['work'] as $k=>$q){
					$workListHtml .= '<tr>
										<td>'.($k+1).'</td>
										<td><i class="usertable-star">';
					if($q['difficulty']==1){
						$workListHtml .= '★';
					}else if($q['difficulty']==2){
						$workListHtml .= '★★';
					}else if($q['difficulty']==3){
						$workListHtml .= '★★★';
					}
					$workListHtml .= '</i></td>
										<td class="usertable-tleft">'.$q['knowledge_parent_name'].'</td>
									  </tr>';
				}
			}

			//上次作业
			$last_workListHtml = '';
			$last_workListHtml_wx = '';
			if(!empty($last_lesson_heluInfo['lecture_info']['question_list']['work'])){
				foreach ($last_lesson_heluInfo['lecture_info']['question_list']['work'] as $k=>$q){
					if(!empty($last_lesson_heluInfo['work_answer']) && $last_lesson_heluInfo['work_answer'][$k]!==''){
						if($last_lesson_heluInfo['work_answer'][$k]!=-1){
							$last_workListHtml .= '<tr>
											<td>'.($k+1).'</td>
											<td><i class="usertable-star">';
							$last_workListHtml_wx .= '<tr>
											<td>'.($k+1).'</td>
											<td><i class="usertable-star">';
							if($q['difficulty']==1){
								$last_workListHtml .= '★';
								$last_workListHtml_wx .= '★';
							}else if($q['difficulty']==2){
								$last_workListHtml .= '★★';
								$last_workListHtml_wx .= '★★';
							}else if($q['difficulty']==3){
								$last_workListHtml .= '★★★';
								$last_workListHtml_wx .= '★★★';
							}
							$last_workListHtml .= '</i></td>
											<td>'.$q['knowledge_parent_name'].'</td>
											<td><div class="baogao-icon ';
							$last_workListHtml_wx .= '</i></td>
											<td class="usertable-tleft">'.$q['knowledge_parent_name'].'</td>
											<td >';
							if($last_lesson_heluInfo['work_answer'][$k]==0){
								$last_workListHtml .= 'icon-3">做错了';
								$last_workListHtml_wx .= '<img src="/static/images/cuo.png">';
							}else if($last_lesson_heluInfo['work_answer'][$k]==1){
								$last_workListHtml .= 'icon-2">部分正确';
								$last_workListHtml_wx .= '<img src="/static/images/bfdui.png">';
							}else if($last_lesson_heluInfo['work_answer'][$k]==2){
								$last_workListHtml .= 'icon-1">做对了';
								$last_workListHtml_wx .= '<img src="/static/images/dui.png">';
							}
							$last_workListHtml .= '</div></td></tr>';
							$last_workListHtml_wx .= '</td></tr>';
						}
					}
				}
			}


			//上次作业知识点云图
			$last_knowledgeCloudHtml = '';
			$last_knowledgeCloudHtml_wx = '';
			$last_knowledgeCloudList = $newStudentsModel->getKnowledgeCloud($last_lesson_heluInfo);
			if(!empty($last_knowledgeCloudList)){
				foreach ($last_knowledgeCloudList as $key=>$knowledgeCloud){
					$last_cloud_used_question_arr = array();
					$last_cloud_right_question_arr = array();
					if(!empty($knowledgeCloud['module_answer'])){
						foreach ($knowledgeCloud['module_answer'] as $k=>$m){
							if($m !==''){
								if($m == 2){
									$last_cloud_right_question_arr[] = $knowledgeCloud['lecture_info']['question_list']['module_question'][$k];
								}
								if($m != -1){
									$last_cloud_used_question_arr[] = $knowledgeCloud['lecture_info']['question_list']['module_question'][$k];
								}
							}
						}
					}
					if(!empty($knowledgeCloud['practise_answer'])){
						foreach ($knowledgeCloud['practise_answer'] as $k=>$p){
							if($p !==''){
								if($p == 2){
									$last_cloud_right_question_arr[] = $knowledgeCloud['lecture_info']['question_list']['practise'][$k]['id'];
								}
								if($p != -1){
									$last_cloud_used_question_arr[] = $knowledgeCloud['lecture_info']['question_list']['practise'][$k]['id'];
								}
							}
						}
					}
					if(!empty($knowledgeCloud['work_answer'])){
						foreach ($knowledgeCloud['work_answer'] as $k=>$w){
							if($w !=''){
								if($w == 2){
									$last_cloud_right_question_arr[] = $knowledgeCloud['lecture_info']['question_list']['work'][$k]['id'];
								}
								if($w != -1){
									$last_cloud_used_question_arr[] = $knowledgeCloud['lecture_info']['question_list']['work'][$k]['id'];
								}
							}

						}
					}


					if(!empty($knowledgeCloud['dtdatereal'])){
						$last_knowledgeCloudHtml .= '<div class="conmapzsd-line';
						$last_knowledgeCloudHtml .= ($knowledgeCloud['helu_id'] == $last_lesson_heluInfo['helu_id'])?' conmapzsd-new ':'';
						$last_knowledgeCloudHtml .= '">
												<div class="fl w100 c-999">'.date('Y-m-d',strtotime($knowledgeCloud['dtdatereal'])).' '.date('H:i',strtotime($knowledgeCloud['dtlessonbeginreal'])).'-'.date('H:i',strtotime($knowledgeCloud['dtlessonendreal'])).'</div>
												<div class="fl  conmapdianimg baogao-icon"></div>
												<div class="fl">
												<ul>';

						$last_knowledgeCloudHtml_wx .= '<div class="conmapzsd-line';
						$last_knowledgeCloudHtml_wx .= ($knowledgeCloud['helu_id'] == $last_lesson_heluInfo['helu_id'])?' conmapzsd-now ':'';
						$last_knowledgeCloudHtml_wx .= '"><div class="fl conmapdianImg baogao-icon"></div><div class="fl conmapzsdList">
												<div class="conmapzsdTime c-999">'.date('Y-m-d',strtotime($knowledgeCloud['dtdatereal'])).' '.date('H:i',strtotime($knowledgeCloud['dtlessonbeginreal'])).'-'.date('H:i',strtotime($knowledgeCloud['dtlessonendreal'])).'</div>
												<ul>';

						if(!empty($knowledgeCloud['lecture_info']['config']['struct']['body']['special']['types'])){
							$temp = 1;
							foreach ($knowledgeCloud['lecture_info']['config']['struct']['body']['special']['types'] as $k=>$knowledge){
								$total_num = 0;
								$right_num = 0;
								if(!empty($knowledgeCloud['lecture_info']['question_list']['module'])){
									foreach ($knowledgeCloud['lecture_info']['question_list']['module'] as $kk=>$type){
										if(!empty($type['question_list'])){
											foreach ($type['question_list'] as $k=>$q){
												if($q['knowledge_parent_id'] == $knowledge['id'] && in_array($q['id'],$last_cloud_used_question_arr)){
													$total_num++;
													if(in_array($q['id'],$last_cloud_right_question_arr)){
														$right_num++;
													}
												}
											}
										}
									}
								}
								if(!empty($knowledgeCloud['lecture_info']['question_list']['practise'])){
									foreach ($knowledgeCloud['lecture_info']['question_list']['practise'] as $kk=>$q){
										if($q['knowledge_parent_id'] == $knowledge['id'] && in_array($q['id'],$last_cloud_used_question_arr)){
											$total_num++;
											if(in_array($q['id'],$last_cloud_right_question_arr)){
												$right_num++;
											}
										}
									}
								}
								if(!empty($knowledgeCloud['lecture_info']['question_list']['work'])){
									foreach ($knowledgeCloud['lecture_info']['question_list']['work'] as $kk=>$q){
										if($q['knowledge_parent_id'] == $knowledge['id'] && in_array($q['id'],$last_cloud_used_question_arr)){
											$total_num++;
											if(in_array($q['id'],$last_cloud_right_question_arr)){
												$right_num++;
											}
										}
									}
								}

								if($total_num>0){
									if($knowledgeCloud['helu_id'] == $last_lesson_heluInfo['helu_id']){
										$last_knowledgeCloudHtml .= '<li><p class="conmapdian-text">'.$temp.'、'.$knowledge['title'].'</p><p>总题'.$total_num.'道；做对'.$right_num.'道；正确率：'.round(($right_num/$total_num*100),2).'%</p></li>';
										$last_knowledgeCloudHtml_wx .= '<li><p class="conmapdian-text">'.$temp.'、'.$knowledge['title'].'</p><p c-orange2>总题'.$total_num.'道；做对'.$right_num.'道；正确率：'.round(($right_num/$total_num*100),2).'%</p></li>';
									}else{
										$last_knowledgeCloudHtml .= '<li>'.$temp.'、'.$knowledge['title'].'</li>';
										$last_knowledgeCloudHtml_wx .= '<li>'.$temp.'、'.$knowledge['title'].'</li>';
									}
									$temp++;
								}

							}
						}

						$last_knowledgeCloudHtml .= '</ul>
													</div>
													<div class="clear"></div>
													</div>';
						$last_knowledgeCloudHtml_wx .= '</ul>
													</div>
													<div class="clear"></div>
													</div>';
					}
				}
			}

			//轨照
			$reportImgHtml = '';
			$reportImgHtml_wx = '';
			$reportImgHtml_wx_title = '';
			if(!empty($heluInfo['lesson_record_img'])){
				foreach ($heluInfo['lesson_record_img'] as $key=>$img){
					$reportImgHtml .= '<img src="'.str_replace('/Upload/','/upload/',$img).'" >';
					$reportImgHtml_wx .= '<li><a href="'.str_replace('/Upload/','/upload/',$img).'" target="_blank"><img src="'.str_replace('/Upload/','/upload/',$img).'" width="100%"></a></li>';
					$reportImgHtml_wx_title .= '<li ><a href="javascript:void(0);"></a></li>';
				}
			}

			$this->assign(get_defined_vars());
			$html = $this->fetch('report_demo');
			$wxHtml = $this->fetch('report_wx_demo');

			if(!empty($html)){
				$reportFolder = UPLOAD_PATH.'report/';
				if(!file_exists($reportFolder)){
					mkdir($reportFolder,0777);
				}
				//pc端
				$report_file =  $reportFolder.$heluInfo['helu_id'].'.html';
				$report_img =  $reportFolder.$heluInfo['helu_id'].'.jpg';
				$file = fopen($report_file, "w+") or die("Unable to open file!");
				fwrite($file, $html);
				fclose($file);
				//移动端
				$report_file_wx =  $reportFolder.$heluInfo['helu_id'].'_wx.html';
				$file = fopen($report_file_wx, "w+") or die("Unable to open file!");
				fwrite($file, $wxHtml);
				fclose($file);

				if(file_exists($report_file)){
					//生成PC端课节报告网页截图
					//$source_html_url = APP_URL.str_replace("/Upload/",'/upload/',end(explode('/'.C('APP_DIR_NAME'),$report_file)));
					//$to_img_url = $report_img;
					//exec(C('PHANTOMJS_PATH')." ".C('PHANTOMJS_SCRIPT')."rasterize.js ".$source_html_url." ".$to_img_url);

					$new_report_file = end(explode('/eap',$report_file));
					//$new_report_img = end(explode('/'.C('APP_DIR_NAME'),$report_img));
					$new_report_img = '';
					$new_report_file_wx = APP_URL.str_replace("/Upload/",'/upload/',end(explode('/eap',$report_file_wx)));
					$type = (!empty($heluInfo['lesson_report_url']))?1:0;
					if($newStudentsModel->recordReportUrl($helu_id,$new_report_file,$new_report_file_wx,$new_report_img,$type)){
						$status = 1;
						$report_url = $new_report_file;
					}
				}
			}
		}
		echo json_encode(array('status'=>$status,'report_url'=>str_replace('/Upload/','/upload/',$report_url),'report_url_wx'=>$new_report_file_wx));
	}



	public function get_knowledgeCloudHtml($knowledgeCloudList,$heluInfo,$right_question_arr){
		$knowledgeCloudHtml = '';
		if(!empty($knowledgeCloudList)){
			foreach ($knowledgeCloudList as $key=>$knowledgeCloud){
				if(!empty($knowledgeCloud['dtdatereal'])){
					$knowledgeCloudHtml .= '<div class="conmapzsd-line';
					$knowledgeCloudHtml .= ($knowledgeCloud['helu_id'] == $heluInfo['helu_id'])?' conmapzsd-new ':'';
					$knowledgeCloudHtml .= '">
												<div class="fl w100 c-999">'.date('Y-m-d',strtotime($knowledgeCloud['dtdatereal'])).' '.date('H:i',strtotime($knowledgeCloud['dtlessonbeginreal'])).'-'.date('H:i',strtotime($knowledgeCloud['dtlessonendreal'])).'</div>
												<div class="fl  conmapdianimg baogao-icon"></div>
												<div class="fl">
													<ul>';
					if(!empty($knowledgeCloud['lecture_info']['config']['struct']['body']['special']['types'])){
						foreach ($knowledgeCloud['lecture_info']['config']['struct']['body']['special']['types'] as $k=>$knowledge){
							$total_num = 0;
							$right_num = 0;
							if(!empty($knowledgeCloud['lecture_info']['question_list']['module'])){
								foreach ($knowledgeCloud['lecture_info']['question_list']['module'] as $kk=>$q){
									if($q['knowledge_parent_id'] == $knowledge['id']){
										$total_num++;
										if(in_array($q['id'],$right_question_arr)){
											$right_num++;
										}
									}
								}
							}
							if(!empty($knowledgeCloud['lecture_info']['question_list']['practise'])){
								foreach ($knowledgeCloud['lecture_info']['question_list']['practise'] as $kk=>$q){
									if($q['knowledge_parent_id'] == $knowledge['id']){
										$total_num++;
										if(in_array($q['id'],$right_question_arr)){
											$right_num++;
										}
									}
								}
							}
							if(!empty($knowledgeCloud['lecture_info']['question_list']['work'])){
								foreach ($knowledgeCloud['lecture_info']['question_list']['practise'] as $kk=>$q){
									if($q['knowledge_parent_id'] == $knowledge['id']){
										$total_num++;
										if(in_array($q['id'],$right_question_arr)){
											$right_num++;
										}
									}
								}
							}
							if($knowledgeCloud['helu_id'] == $heluInfo['helu_id']){
								$knowledgeCloudHtml .= '<li><p class="conmapdian-text">'.($k+1).'、'.$knowledge['title'].'</p><p>总题'.$total_num.'道；做对'.$right_num.'道；正确率：'.($right_num/$total_num*100).'%</p></li>';
							}else{
								$knowledgeCloudHtml .= '<li>'.($k+1).'、'.$knowledge['title'].'</li>';
							}

						}
					}
					$knowledgeCloudHtml .= '		</ul>
												</div>
												<div class="clear"></div>
											</div>';
				}

			}
		}

		return $knowledgeCloudHtml;
	}



	/*预览讲义-新版讲义*/
	public function previewLecture(){
		$helu_id = $_GET['helu_id'];
		if(!empty($helu_id)){
			$newStudentsModel = D('VpNewStudents');
			$numberKey = C('NUMBER_KEY');
			$optionKeyArr = C('OPTIONS_KEY');
			$heluInfo = $this->getHeluInfo($helu_id);
			//$heluInfo = $newStudentsModel->get_heluInfo($helu_id);
		}
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function clearLecture(){
		$newStudentsModel = D('VpNewStudents');
		$status = 0;
		if($newStudentsModel->clearLecture($_POST['helu_id'])){
			$status = 1;
			//更新缓存
			$key = md5($_POST['helu_id']);
			$cache = NCache::getCache();
			$cache->delete('heluInfo', $key);

			$heluInfo = $newStudentsModel->get_heluInfo($_POST['helu_id']);
			$last_helu_id = $this->getLastHeluId($heluInfo);
			$last_key = md5($last_helu_id);
			$cache->delete('heluInfo', $last_key);
		}
		echo json_encode(array('status'=>$status));

	}



	/*下载讲义-新版讲义*/
	public function downloadLecture(){
		$lectureId = $_POST['aid'];
		$paperVersion = $_POST['paper_version'];
		$paperSize = $_POST['paper_size'];
		$paperType = $_POST['paper_type'];
		$archive = D('VpNewStudents')->get_lectureInfo($lectureId);
		$subjectId = 0;

		$sid = $archive ['sid'];
		$cart = unserialize ( $archive ['cart'] );
		$config = unserialize ( $archive ['config'] );

		$title = $config ['struct'] ['header'] ['title'];
		$subtitle = $config ['struct'] ['header'] ['subtitle'];
		$knowledge = $config ['struct'] ['header'] ['knowledge'];
		$introduction = $config ['struct'] ['header'] ['introduction'];
		$summary = $config ['struct'] ['header'] ['summary'];
		$other = $config ['struct'] ['header'] ['other'];

		$ids = array ();
		foreach ( $config ['struct'] ['body'] as $k => $v ) {
			if ($k == 'practise' || $k == 'work') {
				$config ['struct'] ['body'] [$k] ['ids'] = $cart ['cart'] ['question_rs'] [$k] ['-1'];
				if (! empty ( $cart ['cart'] ['question_rs'] [$k] ['-1'] )) {
					$ids [] = VipCommAction::arr2str ( $cart ['cart'] ['question_rs'] [$k] ['-1'], ',' );
				}
			} else {
				foreach ( $config ['struct'] ['body'] [$k] ['types'] as $k1 => $v1 ) {
					$config ['struct'] ['body'] [$k] ['types'] [$k1] ['ids'] = $cart ['cart'] ['question_rs'] [$k] [$v1 ['id']];

					if (! empty ( $cart ['cart'] ['question_rs'] [$k] [$v1 ['id']] )) {
						$ids [] = VipCommAction::arr2str ( $cart ['cart'] ['question_rs'] [$k] [$v1 ['id']], ',' );
					}
				}
			}
		}
		$ids = array_values ( array_unique ( VipCommAction::str2arr ( VipCommAction::arr2str ( $ids, ',' ), ',' ) ) );
		$post = array (
		'title' => array (
		'text' => $title ['text'],
		'visible' => $title ['visible']
		),
		'subtitle' => array (
		'text' => $subtitle ['text'],
		'visible' => $subtitle ['visible']
		),
		'knowledge' => array (
		'text' => $knowledge ['text'],
		'visible' => $knowledge ['visible']
		),
		'introduction' => array (
		'text' => $introduction ['text'],
		'content' => $introduction ['content'],
		'visible' => $introduction ['visible']
		),
		'summary' => array (
		'text' => $summary ['text'],
		'content' => $summary ['content'],
		'visible' => $summary ['visible']
		),
		'other' => array (
		'text' => $other ['text'],
		'content' => $other ['content'],
		'visible' => $other ['visible']
		),
		'sid' => $sid,
		'questionids' => $ids,
		'sort' => $cart ['cart'] ['sort'],
		'sections' => $config ['struct'] ['body'], // 结构
		'paperversion' => $paperVersion, // Word版本
		'papersize' => $paperSize, // 纸张大小
		'papertype' => $paperType  // 试卷类型
		);

		$url = C ( 'LECTURE_DOWNLOAD_URL' );
		import('ORG.Util.Remote');
		$remote = new Remote();
		$content = $remote->get ( $url, array (
		CURLOPT_POST => TRUE,
		CURLOPT_POSTFIELDS => http_build_query ( array (
		'data' => json_encode ( $post )
		) )
		) );

		if ($content == false) {
			$this->error ( '下载讲义失败！' );
		}
		$paperTitle = $archive ['title'];
		$paperTitle = empty ( $paperTitle ) ? '未命名' : $paperTitle;
		$fileName = iconv ( 'utf-8', 'GB2312', $paperTitle );

		header ( 'Cache-Control: no-cache, must-revalidate' );
		header ( 'Pragma: no-cache' );
		if ($paperVersion == 'docx') {
			$ext = '.docx';
			header ( 'content-type:application/vnd.openxmlformats-officedocument.wordprocessingml.document' );
		} else {
			$ext = '.doc';
			header ( 'content-type:application/msword' );
		}
		header ( 'content-disposition:attachment;filename=' . $fileName . $ext );

		ob_clean ();

		print_r ( $content );
	}




	public function downloadReport(){
		$helu_id = $_GET['helu_id'];
		$source_url = D('VpNewStudents')->getReportImgByHeluId($helu_id);
		if(!empty($source_url)){
			VipCommAction::download_file(APP_DIR.$source_url,end(explode('/',$source_url)));
		}
	}


	//阶段培养方案-2第二版新-报告形式
	public function newStudentProgram2(){
		$student_code = trim($_GET['student_code']);
		$userInfo = $this->getUserInfoFull();
		$studentsModel = D('VpStudents');
		$studentInfo = $studentsModel->get_studentContractInfo($student_code);
		$programList = $studentsModel->get_programList($student_code,$userInfo['sCode']);

		$newStudentsModel = D('VpNewStudents');
		//获取业务系统当前教师下该学员未提交的测辅数据
		$testCoachList = $newStudentsModel->get_testCoachAll(array('student_code'=>$student_code,'teacher_code'=>$userInfo['sCode'],'submit'=>0));
		
		$this->assign(get_defined_vars());
		$this->display();
	}



	public function programUseLess(){
		$id = $_GET['id'];
		$status = 0;
		$msg = '无效判定失败0';
		if(!empty($id) && !empty($_POST)){
			$reason = ($_POST['reason'] == '其他')?$_POST['remark']:$_POST['reason'];

			ini_set("soap.wsdl_cache_enabled", "0");
			$param = array( 'id'=>$id,
			'isValid'=>false,
			'invalidJudge'=>1,
			'invalidMemo'=>$reason,
			'isSubmit'=>false,
			'submitDate'=>'2015-09-15'
			);
			try {
				$soap = new SoapClient(C('programWebService'));
				$result = $soap->SaveTeacherTestCoastMBO($param);
				if(!empty($result)){
					$status = 1;
					$msg = '无效判定成功';
				}else{
					$msg = '无效判定失败';
				}
			}catch(SoapFault $fault){
				$msg= "无效判定失败: ".$fault->faultstring."(".$fault->faultcode.")";
			}catch(Exception $e){
				$msg= "无效判定失败: ".$e->getMessage();
			}
		}
		echo json_encode(array('status'=>$status,'msg'=>$msg));

	}



	public function createProgram(){
		if(!empty($_GET['id']) && !empty($_POST['start']) && !empty($_POST['end'])){
			$date = date('Y-m-d');
			$userInfo = $this->getUserInfoFull();
			$newStudentsModel = D('VpNewStudents');
			$testCoachInfo = $newStudentsModel->get_testCoachInfo($_GET['id']);
			if(!empty($testCoachInfo['sstudentcode'])){
				$studentsModel = D('VpStudents');
				$studentInfo = $studentsModel->get_studentContractInfo($testCoachInfo['sstudentcode']);
			}

			$lessonCloud = $this->getLessonCloud(array('teacher_code'=>$userInfo['sCode'],'student_code'=>$testCoachInfo['sstudentcode'],'start'=>$_POST['start'],'end'=>$_POST['end'].' 23:59:59','subject_code'=>$testCoachInfo['ssubjectcode']));
			$result = $this->getLessonCloudHtml($lessonCloud);
			$cloudHtml = $result['html'];
			$accuracyLowKnowledge = $result['accuracy_low_knowledge'];

			//获取评价维度和星级
			$dimensionArr = $newStudentsModel->get_programDimensionList();
			$levelArr = $newStudentsModel->get_programLevelList();
			$numberKey = C('NUMBER_KEY');
			$optionKeyArr = C('OPTIONS_KEY');

		}else{
			$this->error('请填写完整的信息');
		}

		$testCoachId = $_GET['id'];
		$programLesson = Session::get('programLesson');
		$level = Session::get('level');
		$start = $_POST['start'];
		$end = $_POST['end'];
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function getLessonCloud($arr){
		$newStudentsModel = D('VpNewStudents');
		$heluIdArr = $newStudentsModel->getLessonCloudHeluId($arr);
		$lessonCloud = array();
		if(!empty($heluIdArr)){
			foreach ($heluIdArr as $key=>$helu){
				$lessonCloud[] = $this->getSimpleHeluInfo($helu['id']);
			}
		}
		return $lessonCloud;
	}


	public function getLessonCloudHtml($arr){
		$html = '';
		$accuracy_low = array();
		if(!empty($arr)){
			foreach ($arr as $key=>$row){
				$cloud_right_question_arr = array();
				$cloud_used_question_arr = array();
				if(!empty($row['module_answer'])){
					foreach ($row['module_answer'] as $k=>$m){
						if($m !==''){
							if($m == 2){
								$cloud_right_question_arr[] = $row['lecture_info']['question_list']['module_question'][$k];
							}
							if($m != -1){
								$cloud_used_question_arr[] = $row['lecture_info']['question_list']['module_question'][$k];
							}
						}
					}
				}
				if(!empty($row['practise_answer'])){
					foreach ($row['practise_answer'] as $k=>$p){
						if($p !==''){
							if($p == 2){
								$cloud_right_question_arr[] = $row['lecture_info']['question_list']['practise'][$k]['id'];
							}
							if($p != -1){
								$cloud_used_question_arr[] = $row['lecture_info']['question_list']['practise'][$k]['id'];
							}
						}
					}
				}
				if(!empty($row['work_answer'])){
					foreach ($row['work_answer'] as $k=>$w){
						if($w !==''){
							if($w == 2){
								$cloud_right_question_arr[] = $row['lecture_info']['question_list']['work'][$k]['id'];
							}
							if($w != -1){
								$cloud_used_question_arr[] = $row['lecture_info']['question_list']['work'][$k]['id'];
							}
						}
					}
				}

				if(!empty($row['dtdatereal'])){
					if(empty($row['lesson_topic'])){
						$row['lesson_topic'] = '数据正在努力生成……';
					}
					$html .= '<div class="fd-con-classinfo">
									  <h4><i>◆</i><span>'.date('Y-m-d',strtotime($row['dtdatereal'])).' '.date('H:i',strtotime($row['dtlessonbeginreal'])).'-'.date('H:i',strtotime($row['dtlessonendreal'])).'</span>'.$row['lesson_topic'].'</h4>
									  <ul class="clearfix">';

					if(!empty($row['lecture_info']['config']['struct']['body']['special']['types'])){
						$temp = 1;
						foreach ($row['lecture_info']['config']['struct']['body']['special']['types'] as $k=>$knowledge){
							$total_num = 0;
							$right_num = 0;
							if(!empty($row['lecture_info']['question_list']['module'])){
								foreach ($row['lecture_info']['question_list']['module'] as $kk=>$type){
									if(!empty($type['question_list'])){
										foreach ($type['question_list'] as $k=>$q){
											if($q['knowledge_parent_id'] == $knowledge['id'] && in_array($q['id'],$cloud_used_question_arr)){
												$total_num++;
												if(in_array($q['id'],$cloud_right_question_arr)){
													$right_num++;
												}
											}
										}
									}

								}
							}
							if(!empty($row['lecture_info']['question_list']['practise'])){
								foreach ($row['lecture_info']['question_list']['practise'] as $kk=>$q){
									if($q['knowledge_parent_id'] == $knowledge['id'] && in_array($q['id'],$cloud_used_question_arr)){
										$total_num++;
										if(in_array($q['id'],$cloud_right_question_arr)){
											$right_num++;
										}
									}
								}
							}
							if(!empty($row['lecture_info']['question_list']['work'])){
								foreach ($row['lecture_info']['question_list']['work'] as $kk=>$q){
									if($q['knowledge_parent_id'] == $knowledge['id'] && in_array($q['id'],$cloud_used_question_arr)){
										$total_num++;
										if(in_array($q['id'],$cloud_right_question_arr)){
											$right_num++;
										}
									}
								}
							}

							if($total_num>0){
								$accuracy = round(($right_num/$total_num*100),2);
								if($accuracy<60){
									$accuracy_low[]= $knowledge['title'];
								}
								$html .= ($accuracy<60)?'<li class="fd-con-orange">':'<li>';
								$html .= '<p class="fd-con-classtitle">'.($temp).'、'.$knowledge['title'].'</p><p class="fd-c888 fd-f15">总题'.$total_num.'道；做对'.$right_num.'道；正确率：'.$accuracy.'%</p></li>';
								$temp++;
							}
						}
					}
					$html .= '		</ul>
								</div>';
				}

			}
		}
		return array('html'=>$html,'accuracy_low_knowledge'=>$accuracy_low);
	}



	public function getSimpleHeluInfo($heluId){
		$key = md5($heluId.'_simple');
		$cache = NCache::getCache();
		$heluInfo = $cache->get('simple_heluInfo', $key);
		if(false == $heluInfo) {
			$newStudentsModel = D('VpNewStudents');
			$heluInfo = $newStudentsModel->get_simpleHeluInfo($heluId);
			$cache->set('simple_heluInfo', $key, $heluInfo);
		}
		return $heluInfo;
	}

	public function saveRaty(){
		Session::set('level',explode('|',trim($_POST['level_str'],'|')));
		echo '课堂表现保存成功';
	}

	public function addProgramLesson(){
		$new_key = $_GET['new_key'];
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function doAddProgramLesson(){
		$msg = '';
		$status = 0;
		$html = '';
		$programLesson = Session::get('programLesson');
		if(!empty($_POST)){
			$programLesson[] = $_POST;
			$length = count($programLesson);
			Session::set('programLesson',$programLesson);
			$status = 1;
			if($_POST['lesson_difficulty']==1){
				$difficulty ='★';
			}elseif ($_POST['lesson_difficulty']==2){
				$difficulty ='★★';
			}else{
				$difficulty ='★★★';
			}
			$new_key = count($programLesson)-1;
			$html .= '<li class="clearfix" id="lesson_'.$new_key.'">
				<div class="fd-con-geihua-left">
					<p class="fd-c222" id="no_'.$new_key.'">第'.$_POST['lesson_no'].'次课：</p>
					<p>难易成度：</p>
					<p>重  难 点：</p>
				</div>
				<div class="fd-con-geihua-right fd-con-delete">
					<p id="topic_'.$new_key.'">'.$_POST['lesson_topic'].'</p>
					<p class="fd-con-geihua-rightxing" id="difficulty_'.$new_key.'">'.$difficulty.'</p>
					<p class="fd-c888" id="major_'.$new_key.'">'.$_POST['lesson_major'].'</p>
				</div>
				<div class="fd-fl">
					<bottom class="fd-con-teacher-edit" onclick="testMessageBox_edit_programLesson(event,\''.U('Vip/VipStudents/editProgramLesson',array('key'=>$new_key)).'\',\''.U('Vip/VipStudents/saveRaty').'\')">编辑</bottom>
					<bottom class="fd-con-teacher-delete" onclick="delete_programLesson(\''.$new_key.'\',\''.U('Vip/VipStudents/deleteProgramLesson').'\',\''.U('Vip/VipStudents/saveRaty').'\')">删除</bottom>
				</div>
			</li>';

		}

		echo json_encode(array('msg'=>'添加成功','status'=>$status,'html'=>$html));
	}


	public function deleteProgramLesson(){
		$key = $_POST['key'];
		$programLesson = Session::get('programLesson');
		unset($programLesson[$key]);
		Session::set('programLesson',$programLesson);
		echo json_encode(array('msg'=>'删除成功'));
	}


	public function editProgramLesson(){
		$key = $_GET['key'];
		$programLesson = Session::get('programLesson');
		$programLesson[$key]['lesson_major'] = $this->to_textarea_content($programLesson[$key]['lesson_major']);
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function doEditProgramLesson(){
		$key = $_GET['key'];
		$msg = '';
		$status = 0;
		$programLesson = Session::get('programLesson');
		if(!empty($_POST)){
			$programLesson[$key] = $_POST;
			Session::set('programLesson',$programLesson);
			$status = 1;
		}
		echo json_encode(array('msg'=>'编辑成功','status'=>$status,'arr'=>$_POST));
	}




	public function createProgramHtml(){
		$status = 0;
		$msg = '';
		$programLesson = Session::get('programLesson');
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
		}
		if(!empty($programLesson) && !empty($dimension_level_arr)){
			$testCoachId = $_GET['testCoachId'];
			$lessonCloud = $this->getLessonCloud(array('teacher_code'=>$_POST['teacher_code'],
			'student_code'=>$_POST['student_code'],
			'start'=>$_POST['start'],
			'end'=>$_POST['end'].' 23:59:59',
			'subject_code'=>$_POST['subject_code'])
			);
			$result = $this->getLessonCloudHtml($lessonCloud);
			$cloudHtml = $result['html'];
			$accuracyLowKnowledge = $result['accuracy_low_knowledge'];
			$knowledgeHeaderHtml = !empty($accuracyLowKnowledge)?'以下知识点还没有完全消化吸收，点滴铸就辉煌，不要放过任何一个知识点：':'近期学习的知识点掌握的很好，再接再厉，保持住哟！';
			$knowledgeLowHtml = $this->getKnowledgeHtml($accuracyLowKnowledge);
			$dimension_html = $this->getDimensionHtml($dimension_level_arr);
			$comment_html = $this->getCommentHtml($dimension_level_arr,$_POST['student_name']);
			$lesson_html = $this->getLessonHtml($programLesson);
			$today = date('Y-m-d');

			$this->assign(get_defined_vars());
			$html = $this->fetch('program_demo');

			if(!empty($html)){
				$programFolder = UPLOAD_PATH.'program/';
				if(!file_exists($programFolder)){
					mkdir($programFolder,0777);
				}

				$program_file =  $programFolder.$testCoachId.'.html';
				$program_img =  $programFolder.$testCoachId.'.jpg';
				$file = fopen($program_file, "w+") or die("Unable to open file!");
				fwrite($file, $html);
				fclose($file);

				if(file_exists($program_file)){
					//生成PC端课节报告网页截图
					//$source_html_url = APP_URL.str_replace("/Upload/",'/upload/',end(explode('/'.C('APP_DIR_NAME'),$program_file)));
					//$to_img_url = $program_img;
					//exec(C('PHANTOMJS_PATH')." ".C('PHANTOMJS_SCRIPT')."rasterize.js ".$source_html_url." ".$to_img_url);

					$new_program_file = end(explode('/eap',$program_file));
					//$new_program_img = end(explode('/'.C('APP_DIR_NAME'),$program_img));
					$new_program_img = '';

					$arr = array('student_code'=>$_POST['student_code'],
					'student_name'=>$_POST['student_name'],
					'grade_name'=>$_POST['grade_name'],
					'teacher_code'=>$_POST['teacher_code'],
					'teacher_name'=>$_POST['teacher_name'],
					'classadviser_name'=>$_POST['classadviser_name'],
					'dept_code'=>$_POST['dept_code'],
					'dept_name'=>$_POST['dept_name'],
					'subject_code'=>$_POST['subject_code'],
					'subject_name'=>$_POST['subject_name'],
					'kecheng_code'=>$_POST['kecheng_code'],
					'kecheng_name'=>$_POST['kecheng_name'],
					'testCoachId'=>$testCoachId,
					'new_program_file'=>$new_program_file,
					'new_program_img'=>$new_program_img,
					'programLesson'=>$programLesson,
					'dimension_level_arr'=>$dimension_level_arr,
					'testCoachId'=>$_GET['testCoachId'],
					'starttime'=>$_POST['start'],
					'endtime'=>$_POST['end'].' 23:59:59',
					'subject_code'=>$_POST['subject_code']);
					$newStudentsModel = D('VpNewStudents');
					if($_GET['act'] == 'edit'){
						if($newStudentsModel->editProgram($_GET['program_id'],$arr)){
							$msg = '辅导方案更新成功';
							$status = 1;
						}else{
							$msg .= '辅导方案更新失败';
						}
						$programLesson = Session::set('programLesson',array());
						$level = Session::set('level',array());
					}else{
						if($newStudentsModel->addProgram($arr)){
							$msg = '辅导方案生成成功；';
							$programLesson = Session::set('programLesson',array());
							$level = Session::set('level',array());
							//更新业务系统V_Teacher_TestCoachMBONew表start======
							ini_set("soap.wsdl_cache_enabled", "0");
							$param = array( 'id'=>$testCoachId,
							'isValid'=>true,
							'invalidJudge'=>'',
							'invalidMemo'=>'',
							'isSubmit'=>true,
							'submitDate'=>date('Y-m-d')
							);
							try {
								$soap = new SoapClient(C('programWebService'));
								$result = $soap->SaveTeacherTestCoastMBO($param);
								if(!empty($result)){
									$status = 1;
									$msg .= '测辅状态更新成功；';
								}else{
									$msg .= '测辅状态更新失败；';
								}
							}catch(SoapFault $fault){
								$msg .= "测辅状态更新失败1: ".$fault->faultstring."(".$fault->faultcode.")；";
							}catch(Exception $e){
								$msg .= "测辅状态更新失败2: ".$e->getMessage();
							}
							//end===========================================

						}else{
							$msg = '辅导方案生成失败；';
						}

					}
				}
			}else{
				$msg = '辅导方案模板不存在';
			}
		}else{
			$msg = '请评价学员课堂表现，并添加规划课程';
		}

		echo json_encode(array('status'=>$status,'msg'=>$msg,'program_url'=>str_replace('/Upload/','/upload/',$new_program_file),'commond'=>C('PHANTOMJS_PATH')." ".C('PHANTOMJS_SCRIPT')."rasterize.js ".$source_html_url." ".$to_img_url));
	}



	public function getLessonHtml($lessonArr){
		$html = '';
		if(!empty($lessonArr)){
			foreach ($lessonArr as $key=>$lesson){
				if($lesson['lesson_difficulty']==1){
					$difficity = '★';
				}else if($lesson['lesson_difficulty']==2){
					$difficity = '★★';
				}else{
					$difficity = '★★★';
				}
				$html .= '<li class="clearfix">
							<div class="fd-con-geihua-left">
								<p class="fd-c222">第'.$lesson['lesson_no'].'次课：</p>
								<p>难易成度：</p>
								<p>重  难 点：</p>
							</div>
							<div class="fd-con-geihua-right">
								<p>'.$lesson['lesson_topic'].'</p>
								<p class="fd-con-geihua-rightxing">'.$difficity.'</p>
								<p class="fd-c888">'.$lesson['lesson_major'].'</p>
							</div>
						</li>';
			}
		}
		return $html;
	}


	public function getDimensionHtml($dimensionLevelArr){
		$html = '';
		if(!empty($dimensionLevelArr)){
			foreach ($dimensionLevelArr as $key=>$dimensionLevel){
				switch ($dimensionLevel['level']){
					case 1:
						$level_img = '<img src="/static/images/star-on-big.png">';
						break;
					case 2:
						$level_img = '<img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png">';
						break;
					case 3:
						$level_img = '<img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png">';
						break;
					case 4:
						$level_img = '<img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png">';
						break;
					case 5:
						$level_img = '<img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png">';
						break;
					default:
						$level_img = '<img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png">';
				}

				$html .= '<li><span>'.$dimensionLevel['title'].'：</span>'.$level_img.'</li>';
			}
		}
		return $html;
	}


	public function getCommentHtml($dimensionLevelArr,$student_name){
		$html = C('words_template');
		$html = str_replace('×××',$student_name,$html);
		if(!empty($dimensionLevelArr)){
			$newStudentsModel = D('VpNewStudents');
			foreach ($dimensionLevelArr as $key=>$dimensionLevel){
				$text = $newStudentsModel->getProgramText($dimensionLevel['id'],$dimensionLevel['level']);
				$html = str_replace('[评价:'.$dimensionLevel['title'].']',$text,$html);
			}
		}
		return $html;
	}


	public function getKnowledgeHtml($knowledgeArr){
		$html = '';
		if($knowledgeArr){
			foreach ($knowledgeArr as $key=>$knowledge){
				$html .= ($key+1).'、'.$knowledge.'　';
			}
		}
		return $html;
	}


	public function editProgram(){
		if(!empty($_GET['id'])){
			$programId = $_GET['id'];
			$date = date('Y-m-d');
			//$userInfo = $this->getUserInfoFull();
			$newStudentsModel = D('VpNewStudents');
			$programInfo = $newStudentsModel->get_programInfoById($programId);
			if(!empty($programInfo['student_code'])){
				$studentsModel = D('VpStudents');
				$studentInfo = $studentsModel->get_studentContractInfo($programInfo['student_code']);
			}
			$conditionArr = array('teacher_code'=>$programInfo['teacher_code'],
			'student_code'=>$programInfo['student_code'],
			'start'=>$programInfo['starttime'],
			'end'=>$programInfo['endtime'],
			'subject_code'=>$programInfo['subject_code']);
			$lessonCloud = $this->getLessonCloud($conditionArr);
			$result = $this->getLessonCloudHtml($lessonCloud);
			$cloudHtml = $result['html'];
			$accuracyLowKnowledge = $result['accuracy_low_knowledge'];

			//获取评价维度和星级
			$dimensionArr = $newStudentsModel->get_programDimensionList();
			$levelArr = $newStudentsModel->get_programLevelList();
			$numberKey = C('NUMBER_KEY');
			$optionKeyArr = C('OPTIONS_KEY');

		}else{
			$this->error('非法操作');
		}

		$testCoachId = $programInfo['testcoachid'];
		if(Session::get('programLesson')){
			$programLesson = Session::get('programLesson');
		}else{
			$programLesson = $programInfo['program_lesson'];
			Session::set('programLesson',$programLesson);
		}

		$dimensionLevel = unserialize($programInfo['dimension_level']);
		if(!empty($dimensionLevel)){
			foreach($dimensionLevel as $key=>$dimension){
				$dimensionLevel[$key]['text'] = $newStudentsModel->get_dimensionTextByDimensionId($dimension['id']);
			}
		}
		$start = $programInfo['starttime'];
		$end = $programInfo['endtime'];

		$this->assign(get_defined_vars());
		$this->display();
	}


	//查看辅导方案
	public function showProgram(){
		if(!empty($_GET['id'])){
			$newStudentsModel = D('VpNewStudents');
			$programInfo = $newStudentsModel->get_programInfoById($_GET['id']);
			$programUrl = APP_URL.str_replace('Upload','upload',$programInfo['program_html']).'?time='.time();
			$html = file_get_contents($programUrl);
		}
		$this->assign(get_defined_vars());
		$this->display();
	}


	//PIV4.0使用说明-PC
	public function piv4Instruction(){

		$this->assign(get_defined_vars());
		$this->display();
	}

	
	public function getLastHeluId($heluInfo){
		$key = md5($heluInfo['helu_id']);
		$cache = NCache::getCache();
		$last_helu_id = $cache->get('lastHeluId', $key);
		if(false == $last_helu_id) {
			$newStudentsModel = D('VpNewStudents');
			$last_helu_id = $newStudentsModel->get_lastHeluId($heluInfo);
			$cache->set('lastHeluId', $key, $last_helu_id);
		}
		return $last_helu_id;
	}


	/***********  改版辅导方案  ***************/

	/**
	 * 创建辅导方案
	 * @return [type] [description]
	 */
	public function createProgramNew(){
		if(!empty($_GET['id'])){
			$date = date('Y-m-d');
			$userInfo = $this->getUserInfoFull();
			$newStudentsModel = D('VpNewStudents');
			$testCoachInfo = $newStudentsModel->get_testCoachInfo($_GET['id']);
			if(!empty($testCoachInfo['sstudentcode'])){
				$studentsModel = D('VpStudents');
				$studentInfo = $studentsModel->get_studentContractInfo($testCoachInfo['sstudentcode']);
			}

			//$lessonCloud = $this->getLessonCloud(array('teacher_code'=>$userInfo['sCode'],'student_code'=>$testCoachInfo['sstudentcode'],'start'=>$_POST['start'],'end'=>$_POST['end'].' 23:59:59','subject_code'=>$testCoachInfo['ssubjectcode']));
			//$result = $this->getLessonCloudHtml($lessonCloud);
			//$cloudHtml = $result['html'];
			//$accuracyLowKnowledge = $result['accuracy_low_knowledge'];

			//获取评价维度和星级
			//$dimensionArr = $newStudentsModel->get_programDimensionList();
			//$levelArr = $newStudentsModel->get_programLevelList();
			//$numberKey = C('NUMBER_KEY');
			//$optionKeyArr = C('OPTIONS_KEY');

		}else{
			$this->error('请填写完整的信息');
		}

		$testCoachId = $_GET['id'];
		$programLesson = Session::get('programLesson');
		$level = Session::get('level');
		$start = $_POST['start'];
		$end = $_POST['end'];
		$this->assign(get_defined_vars());
		$this->display();
	}


	/**
	 * 新辅导方案
	 * @return [type] [description]
	 */
	public function editProgramNew(){
		if(!empty($_GET['id'])){
			$programId = $_GET['id'];
			$date = date('Y-m-d');
			//$userInfo = $this->getUserInfoFull();
			$newStudentsModel = D('VpNewStudents');
			$programInfo = $newStudentsModel->get_programInfoById($programId);
			if(!empty($programInfo['student_code'])){
				$studentsModel = D('VpStudents');
				$studentInfo = $studentsModel->get_studentContractInfo($programInfo['student_code']);
			}
			// $conditionArr = array('teacher_code'=>$programInfo['teacher_code'],
			// 'student_code'=>$programInfo['student_code'],
			// 'start'=>$programInfo['starttime'],
			// 'end'=>$programInfo['endtime'],
			// 'subject_code'=>$programInfo['subject_code']);
			// $lessonCloud = $this->getLessonCloud($conditionArr);
			// $result = $this->getLessonCloudHtml($lessonCloud);
			// $cloudHtml = $result['html'];
			//$accuracyLowKnowledge = $result['accuracy_low_knowledge'];

			//获取评价维度和星级
			//$dimensionArr = $newStudentsModel->get_programDimensionList();
			//$levelArr = $newStudentsModel->get_programLevelList();
			//$numberKey = C('NUMBER_KEY');
			//$optionKeyArr = C('OPTIONS_KEY');

		}else{
			$this->error('非法操作');
		}

		$testCoachId = $programInfo['testcoachid'];
		// if(Session::get('programLesson')){
		// 	$programLesson = Session::get('programLesson');
		// }else{
		// 	$programLesson = $programInfo['program_lesson'];
		// 	Session::set('programLesson',$programLesson);
		// }

		// $dimensionLevel = unserialize($programInfo['dimension_level']);
		// if(!empty($dimensionLevel)){
		// 	foreach($dimensionLevel as $key=>$dimension){
		// 		$dimensionLevel[$key]['text'] = $newStudentsModel->get_dimensionTextByDimensionId($dimension['id']);
		// 	}
		// }
		$start = $programInfo['starttime'];
		$end = $programInfo['endtime'];

		$this->assign(get_defined_vars());
		$this->display();
	}
 

	/**
	 * 生成辅导方案-新
	 * @return [type] [description]
	 */
	public function createProgramHtmlNew(){
		$status = 0;
		$msg = '';
		if(IS_POST)
		{
			$lesson_no=explode('|',trim($_POST['lesson_no'],'|'));	//课次

			$level_arr = explode('|',trim($_POST['level_str'],'|'));	//困难程度

			$lesson_topic=explode('|',trim($_POST['lesson_topic'],'|'));//主题

			$lesson_major=explode('|',trim($_POST['lesson_major'],'|'));//内容
			$programLesson=array();
			foreach($lesson_no as $k =>$v)
			{
				$programLesson[$k]['lesson_no']=$v;
			}

			foreach($level_arr as $k => $v)
			{
				$programLesson[$k]['lesson_difficulty']=$v;
			}

			foreach($lesson_topic as $k=>$v)
			{
				$programLesson[$k]['lesson_topic']=$v;
			}

			foreach($lesson_major as $k=>$v)
			{
				$programLesson[$k]['lesson_major']=$v;
			}

			$testCoachId = $_GET['testCoachId'];

			$lesson_html = $this->getLessonHtmlNew($programLesson);	//辅导方案显示内容
			$today = date('Y-m-d');
			$teacher_say_html=$_POST['comment'];
			$this->assign(get_defined_vars());
			$html = $this->fetch('program_demo_new');

			if(!empty($html)){
				$programFolder = UPLOAD_PATH.'program/';
				if(!file_exists($programFolder)){
					mkdir($programFolder,0777);
				}

				$program_file =  $programFolder.$testCoachId.'.html';
				$program_img =  $programFolder.$testCoachId.'.jpg';
				$file = fopen($program_file, "w+") or die("Unable to open file!");
				fwrite($file, $html);
				fclose($file);

				if(file_exists($program_file)){
					//生成PC端课节报告网页截图
					//$source_html_url = APP_URL.str_replace("/Upload/",'/upload/',end(explode('/'.C('APP_DIR_NAME'),$program_file)));
					//$to_img_url = $program_img;
					//exec(C('PHANTOMJS_PATH')." ".C('PHANTOMJS_SCRIPT')."rasterize.js ".$source_html_url." ".$to_img_url);

					$new_program_file = end(explode('/eap',$program_file));
					//$new_program_img = end(explode('/'.C('APP_DIR_NAME'),$program_img));
					$new_program_img = '';

					$arr = array('student_code'=>$_POST['student_code'],
					'student_name'=>$_POST['student_name'],
					'grade_name'=>$_POST['grade_name'],
					'teacher_code'=>$_POST['teacher_code'],
					'teacher_name'=>$_POST['teacher_name'],
					'classadviser_name'=>$_POST['classadviser_name'],
					'dept_code'=>$_POST['dept_code'],
					'dept_name'=>$_POST['dept_name'],
					'subject_code'=>$_POST['subject_code'],
					'subject_name'=>$_POST['subject_name'],
					'kecheng_code'=>$_POST['kecheng_code'],
					'kecheng_name'=>$_POST['kecheng_name'],
					'testCoachId'=>$testCoachId,
					'new_program_file'=>$new_program_file,
					'new_program_img'=>$new_program_img,
					'programLesson'=>$programLesson,
					'comment'=>htmlspecialchars($_POST['comment']),
					//'dimension_level_arr'=>$dimension_level_arr,
					//'testCoachId'=>$_GET['testCoachId'],
					'starttime'=>$_POST['start'],
					'endtime'=>$_POST['end'].' 23:59:59',
					'subject_code'=>$_POST['subject_code']);
					$newStudentsModel = D('VpNewStudents');
					if($_GET['act'] == 'edit'){
						if($newStudentsModel->editProgram($_GET['program_id'],$arr)){
							$msg = '辅导方案更新成功';
							$status = 1;
						}else{
							$msg .= '辅导方案更新失败';
						}
						$programLesson = Session::set('programLesson',array());
						$level = Session::set('level',array());
					}else{
						if($newStudentsModel->addProgram($arr)){
							$msg = '辅导方案生成成功；';
							$programLesson = Session::set('programLesson',array());
							$level = Session::set('level',array());
							//更新业务系统V_Teacher_TestCoachMBONew表start======
							ini_set("soap.wsdl_cache_enabled", "0");
							$param = array( 'id'=>$testCoachId,
							'isValid'=>true,
							'invalidJudge'=>'',
							'invalidMemo'=>'',
							'isSubmit'=>true,
							'submitDate'=>date('Y-m-d')
							);
							try {
								$soap = new SoapClient(C('programWebService'));
								$result = $soap->SaveTeacherTestCoastMBO($param);
								if(!empty($result)){
									$status = 1;
									$msg .= '测辅状态更新成功；';
								}else{
									$msg .= '测辅状态更新失败；';
								}
							}catch(SoapFault $fault){
								$msg .= "测辅状态更新失败1: ".$fault->faultstring."(".$fault->faultcode.")；";
							}catch(Exception $e){
								$msg .= "测辅状态更新失败2: ".$e->getMessage();
							}
							//end===========================================

						}else{
							$msg = '辅导方案生成失败；';
						}

					}
				}
			}else{
				$msg = '辅导方案模板不存在';
			}
		}

		// $lessonCloud = $this->getLessonCloud(array('teacher_code'=>$_POST['teacher_code'],
		// 'student_code'=>$_POST['student_code'],
		// 'start'=>$_POST['start'],
		// 'end'=>$_POST['end'].' 23:59:59',
		// 'subject_code'=>$_POST['subject_code'])
		// );
		// $result = $this->getLessonCloudHtml($lessonCloud);
		// $cloudHtml = $result['html'];
		// $accuracyLowKnowledge = $result['accuracy_low_knowledge'];
		// $knowledgeHeaderHtml = !empty($accuracyLowKnowledge)?'以下知识点还没有完全消化吸收，点滴铸就辉煌，不要放过任何一个知识点：':'近期学习的知识点掌握的很好，再接再厉，保持住哟！';
		// $knowledgeLowHtml = $this->getKnowledgeHtml($accuracyLowKnowledge);

		//$dimension_html = $this->getDimensionHtml($dimension_level_arr);
		//$comment_html = $this->getCommentHtml($dimension_level_arr,$_POST['student_name']);
		

		echo json_encode(array('status'=>$status,'msg'=>$msg,'program_url'=>str_replace('/Upload/','/upload/',$new_program_file),'commond'=>C('PHANTOMJS_PATH')." ".C('PHANTOMJS_SCRIPT')."rasterize.js ".$source_html_url." ".$to_img_url));
	}


	/**
	 * 课程规划
	 * @param  [type] $lessonArr [description]
	 * @return [type]            [description]
	 */
	public function getLessonHtmlNew($lessonArr){
	$html = '';
	if(!empty($lessonArr)){
		foreach ($lessonArr as $key=>$lesson){
			if($lesson['lesson_difficulty']==1){
				$difficity = '★';
			}else if($lesson['lesson_difficulty']==2){
				$difficity = '★★';
			}else{
				$difficity = '★★★';
			}
			$html .= '<div class="class">
				<div class="classtitle">
					<h3 class="classnum">第'.$lesson['lesson_no'].'次课</h3>
					<p>难易程度&nbsp;&nbsp;&nbsp;&nbsp;'.$difficity.'</p>
				</div>
				<div class="classcon">
					<p style="margin-bottom:10px;"><label class="c-gray">课次主题</label><span>'.$lesson['lesson_topic'].'</span></p>
					<p><label class="c-gray">重难点</label><span>'.$lesson['lesson_major'].'</span></p>
				</div>
			</div>';
		}
	}
	return $html;
	}

	/**
	 * 课程规划列表
	 * @return [type] [description]
	 */
	public function vipProgramList()
	{
		$student_code = trim($_GET['student_code']);
		$userInfo = $this->getUserInfoFull();
		$studentsModel = D('VpStudents');
		$studentInfo = $studentsModel->get_studentContractInfo($student_code);
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$conditionArr =  array('student_code'=>$student_code,'teacherCode'=>$userInfo['sCode']);
		$newStudentsModel = D('VpNewStudents');
		$programList = $newStudentsModel->getProgramList($conditionArr,$curPage,$pagesize);
		$count = $newStudentsModel->getProgramCount($conditionArr);
		$page = new page($count,$pagesize);
		$showPage = $page->show();
		$this->assign(get_defined_vars());
		$this->display();


	}

	/**
	 * 修改课程规划
	 * @return [type] [description]
	 */
	public function editVipProgram()
	{
		if(!empty($_GET['id'])){
			$programId = $_GET['id'];
			$date = date('Y-m-d');
			//$userInfo = $this->getUserInfoFull();
			$newStudentsModel = D('VpNewStudents');
			$programInfo = $newStudentsModel->getprogramInfoById($programId);
		}else{
			$this->error('非法操作');
		}

		if(IS_POST)
		{
			//接收课次
			$lesson_no=explode('|',trim($_POST['lesson_no'],'|'));	//课次
			$lesson_topic=explode('|',trim($_POST['lesson_topic'],'|'));//主题

			$lesson_major=explode('|',trim($_POST['lesson_major'],'|'));//内容
			$programLesson=array();
			foreach($lesson_no as $k =>$v)
			{
				$programLesson[$k]['lesson_no']=$v;
			}

			foreach($lesson_topic as $k=>$v)
			{
				if(empty($v))
				{
					echo json_encode(array('status'=>$status,'msg'=>'最少填写10次课程计划'));exit;
				}
				$programLesson[$k]['lesson_topic']=$v;
			}

			foreach($lesson_major as $k=>$v)
			{
				$programLesson[$k]['lesson_major']=$v;
			}

			$lesson_html = $this->getVipProgramLesson($programLesson);	//辅导方案显示内容
			$today = date('Y-m-d');
			$this->assign(get_defined_vars());
			$html = $this->fetch('vip_program_demo');

			if(!empty($html)){
				$programFolder = UPLOAD_PATH.'program/';
				if(!file_exists($programFolder)){
					mkdir($programFolder,0777);
				}

			$program_file =  $programFolder.time().'.html';
			$program_img =  $programFolder.time().'.jpg';
			$file = fopen($program_file, "w+") or die("Unable to open file!");
			fwrite($file, $html);
			fclose($file);

			if(file_exists($program_file)){
					//生成PC端课节报告网页截图
					//$source_html_url = APP_URL.str_replace("/Upload/",'/upload/',end(explode('/'.C('APP_DIR_NAME'),$program_file)));
					//$to_img_url = $program_img;
					//exec(C('PHANTOMJS_PATH')." ".C('PHANTOMJS_SCRIPT')."rasterize.js ".$source_html_url." ".$to_img_url);

					$new_program_file = end(explode('/eap',$program_file));
					//$new_program_img = end(explode('/'.C('APP_DIR_NAME'),$program_img));
					$new_program_img = '';

			}

			$arr = array(
					'student_code'=>$_POST['student_code'],
					'student_name'=>$_POST['student_name'],
					'grade_name'=>$_POST['grade_name'],
					'teacher_code'=>$_POST['teacher_code'],
					'teacher_name'=>$_POST['teacher_name'],
					'classadviser_name'=>$_POST['classadviser_name'],
					'dept_code'=>$_POST['dept_code'],
					'dept_name'=>$_POST['dept_name'],
					'kecheng_code'=>$_POST['kecheng_code'],
					'kecheng_name'=>$_POST['kecheng_name'],
					'new_program_file'=>$new_program_file,
					'new_program_img'=>$new_program_img,
					'programLesson'=>$programLesson
					//'dimension_level_arr'=>$dimension_level_arr,
				);
			$newStudentsModel = D('VpNewStudents');
			if($newStudentsModel->editVipProgram($_GET['id'],$arr))
			{
				$status = 1;
				$msg .= '保存成功';
			}else{
				$msg .= '保存失败';
			}
		}else
		{
			$msg = '课程规划模板不存在';
		}
			echo json_encode(array('status'=>$status,'msg'=>$msg));exit;	
		}

		$this->assign(get_defined_vars());
		$this->display();
	}

	/**
	 * 添加课程规划
	 */
	public function addVipProgram()
	{
		$student_code = trim($_GET['student_code']);
		$userInfo = $this->getUserInfoFull();
		$studentsModel = D('VpStudents');
		$studentInfo = $studentsModel->get_studentContractInfo($student_code);
		$status = 0;
		$msg = '';
		if(IS_POST)
		{
			//接收课次
			$lesson_no=explode('|',trim($_POST['lesson_no'],'|'));	//课次
			$lesson_topic=explode('|',trim($_POST['lesson_topic'],'|'));//主题

			$lesson_major=explode('|',trim($_POST['lesson_major'],'|'));//内容
			$programLesson=array();
			foreach($lesson_no as $k =>$v)
			{
				$programLesson[$k]['lesson_no']=$v;
			}

			foreach($lesson_topic as $k=>$v)
			{
				if(empty($v))
				{
					echo json_encode(array('status'=>$status,'msg'=>'最少填写10次课程计划'));exit;
				}
				$programLesson[$k]['lesson_topic']=$v;
			}

			foreach($lesson_major as $k=>$v)
			{
				$programLesson[$k]['lesson_major']=$v;
			}

			$lesson_html = $this->getVipProgramLesson($programLesson);	//辅导方案显示内容
			$today = date('Y-m-d');
			$this->assign(get_defined_vars());
			$html = $this->fetch('vip_program_demo');

			if(!empty($html)){
				$programFolder = UPLOAD_PATH.'program/';
				if(!file_exists($programFolder)){
					mkdir($programFolder,0777);
				}

			$program_file =  $programFolder.time().'.html';
			$program_img =  $programFolder.time().'.jpg';
			$file = fopen($program_file, "w+") or die("Unable to open file!");
			fwrite($file, $html);
			fclose($file);

			if(file_exists($program_file)){
					//生成PC端课节报告网页截图
					//$source_html_url = APP_URL.str_replace("/Upload/",'/upload/',end(explode('/'.C('APP_DIR_NAME'),$program_file)));
					//$to_img_url = $program_img;
					//exec(C('PHANTOMJS_PATH')." ".C('PHANTOMJS_SCRIPT')."rasterize.js ".$source_html_url." ".$to_img_url);

					$new_program_file = end(explode('/eap',$program_file));
					//$new_program_img = end(explode('/'.C('APP_DIR_NAME'),$program_img));
					$new_program_img = '';

			}

			$arr = array(
					'student_code'=>$_POST['student_code'],
					'student_name'=>$_POST['student_name'],
					'grade_name'=>$_POST['grade_name'],
					'teacher_code'=>$_POST['teacher_code'],
					'teacher_name'=>$_POST['teacher_name'],
					'classadviser_name'=>$_POST['classadviser_name'],
					'dept_code'=>$_POST['dept_code'],
					'dept_name'=>$_POST['dept_name'],
					'kecheng_code'=>$_POST['kecheng_code'],
					'kecheng_name'=>$_POST['kecheng_name'],
					'new_program_file'=>$new_program_file,
					'new_program_img'=>$new_program_img,
					'programLesson'=>$programLesson
					//'dimension_level_arr'=>$dimension_level_arr,
				);
			$newStudentsModel = D('VpNewStudents');
			if($newStudentsModel->addVipProgram($arr))
			{
				$status = 1;
				$msg .= '保存成功';
			}else{
				$msg .= '保存失败';
			}
		}else
		{
			$msg = '课程规划模板不存在';
		}
		echo json_encode(array('status'=>$status,'msg'=>$msg));exit;	
		}
		$this->assign(get_defined_vars());
		$this->display();
	}

	/**
	 * 课程规划
	 * @param  [type] $lessonArr [description]
	 * @return [type]            [description]
	 */
	public function getVipProgramLesson($lessonArr){
	$html = '';
	if(!empty($lessonArr)){
		foreach ($lessonArr as $key=>$lesson){
		$html .= '<div class="class_containover">
			<div class="class_nameover">
				<p class="c18 blod">第<span class="green">'.$lesson['lesson_no'].'</span>次课</p>
			</div>
			<div class="fromover">
				<p class="info blod c18">'.$lesson['lesson_topic'].'</p>
				<p class="info">'.$lesson['lesson_major'].'</p>
			</div>
		</div>';
		}
	}
	return $html;
	}

	/**
	 * 查看课程规划
	 * @return [type] [description]
	 */
	public function showVipProgram(){
		if(!empty($_GET['id'])){
			$newStudentsModel = D('VpNewStudents');
			$programInfo = $newStudentsModel->getprogramInfoById($_GET['id']);
			$programUrl = APP_URL.str_replace('Upload','upload',$programInfo['program_html']).'?time='.time();
			
			$html = file_get_contents($programUrl);

		}
		$this->assign(get_defined_vars());
		$this->display();
	}
	
}

?>