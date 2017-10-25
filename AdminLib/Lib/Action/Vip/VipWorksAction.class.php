<?php
import('COM.MsgSender.SmsSender');
/*我的工作*/
class VipWorksAction extends VipCommAction{
	protected function notNeedLogin() {
		return array('VIP-VIPWORKS-SENDMSGTOTEA','VIP-VIPWORKS-CREATESWF');
	}

	public function index(){
		$userInfo = VipCommAction::get_currentUserInfo();
		if($userInfo['user_key'] == 'Employee-wangyan'){
			$userInfo['user_key'] = 'Employee-guoluping';
			$userInfo['sCode'] = 'VP00022';
		}
		$userInfo['userTypeKey'] = reset(explode('-',$userInfo['user_key']));
		$studentsModel = D('VpStudents');
		if($userInfo['sCode']){
			$waitHeluList = $studentsModel->get_myStudentList(array('teacherCode'=>$userInfo['sCode'],'now'=>date('Y-m-d H:i:s'),'userTypeKey'=>$userInfo['userTypeKey'],'overdue'=>1),0);
		}

		$messageList = D('VpHandouts')->get_messageList($userInfo['user_key']);
		$is_jiaoyan = $this->checkUserRole();
		$is_jianzhi = $this->checkUserType();
		$is_admin = VipCommAction::checkIsAdmin();

		$vipSubjectModel = D('VpSubject');
		$subjectArr = $vipSubjectModel->get_subjectList('',$userInfo['user_key']);
		$subjectStr = '';
		foreach($subjectArr as $key=>$value){
			$subjectStr .= $value['sid'].',';
		}

		$vipGradeModel = D('VpGrade');

		$gradeArr = $vipGradeModel->get_gradeList('',$userInfo['user_key']);
		$gradeStr = '';
		foreach($gradeArr as $key=>$value){
			$gradeStr .= $value['gid'].',';
		}
		$startTime = strtotime(date("Y-m-d",time()-(date('w',time())-1)*3600*24));
		$endTime = $startTime+3600*24*7-1;
		$vipHandoutsModel = D('VpHandouts');
		$vipKnowledgeModel = D("VpKnowledge");

		if($is_jiaoyan == 0){
			$notStr = $vipKnowledgeModel->get_permission_subquery(array('sid'=>'','gid'=>'','kid'=>''));
			if(!empty($notStr)){
				$notStr = " AND vph.[hid] NOT IN (".$notStr.")";
			}
		}
		if(!$is_admin){
			if($is_jianzhi){
				$notStr .= " AND vph.is_parttime_visible = '1'";
			}
		}
		$uploadList = $vipHandoutsModel->get_alluploadOrDownloadList(trim($subjectStr,','),trim($gradeStr,','),'','',$startTime,$endTime,'my',$notStr);
		$jiangyiArray = array();
		$shitiArray = array();
		if(count($uploadList) != 0){
			$sgkStr = '';
			foreach($uploadList as $key=>$value){
				$sgkStr .= $value['sid'].$value['gid'].$value['kid'].",";
			}
			$courseuserArray = $vipKnowledgeModel->get_allcourseuser_by_handout(trim($sgkStr,','));
			$courseuserAllArray = array();
			foreach($courseuserArray as $key=>$value){
				$courseuserAllArray[$value['p']] = $value['courseuser']?$value['courseuser']:"暂无课程用途".str_repeat("&nbsp;",$key);
			}
			foreach($uploadList as $key=>$value){
				$uploadList[$key]['teacher_preview'] = (file_exists(APP_DIR.$value['teacher_version_preview']))?1:0;
				$uploadList[$key]['sname'] = $value['sname'];
				if($key > 1){
					if($uploadList[$key]['gname'] == trim($uploadList[$key-1]['gname'],"&nbsp;") && $uploadList[$key]['sname'] != $uploadList[$key-1]['sname']){
						$uploadList[$key]['gname'] = $uploadList[$key]['gname'].str_repeat("&nbsp;",$key%10+1);
					}else if($uploadList[$key]['gname'] == trim($uploadList[$key-1]['gname'],"&nbsp;") && $uploadList[$key]['sname'] == $uploadList[$key-1]['sname']){
						$uploadList[$key]['gname'] = $uploadList[$key-1]['gname'];
					}
					if($uploadList[$key]['kname'] == trim($uploadList[$key-1]['kname'],'&nbsp;') && ($uploadList[$key]['gname'] != trim($uploadList[$key-1]['gname'],'&nbsp;') || $uploadList[$key]['sname'] != $uploadList[$key-1]['sname'])){
						$uploadList[$key]['kname'] = $uploadList[$key]['kname'].str_repeat("&nbsp;",$key%10+1);
					}else if($uploadList[$key]['kname'] == trim($uploadList[$key-1]['kname'],'&nbsp;') && $uploadList[$key]['gname'] == trim($uploadList[$key-1]['gname'],'&nbsp;') && $uploadList[$key]['sname'] == $uploadList[$key-1]['sname']){
						$uploadList[$key]['kname'] = $uploadList[$key-1]['kname'];
					}
				}
				if($value['type'] == 0){
					$uploadList[$key]['courseuser'] = $courseuserAllArray[$value['sid'].$value['gid'].$value['kid']];
					$jiangyiArray[]=$uploadList[$key];
				}else if($value['type'] == 1){
					$shitiArray[]=$uploadList[$key];
				}
			}
		}
		$this->assign(get_defined_vars());
		$this->display();
	}

	public function myworkhistory(){
		$userInfo = VipCommAction::get_currentUserInfo();
		$studentsModel = D('VpStudents');

		if($userInfo['sCode']){
			$waitHeluList = $studentsModel->get_myStudentList(array('teacherCode'=>$userInfo['sCode'],'now'=>date('Y-m-d H:i:s'),'userTypeKey'=>reset(explode('-',$userInfo['user_key'])),'overdue'=>1),0);
		}

		$messageList = D('VpHandouts')->get_messageList($userInfo['user_key']);

		$is_jiaoyan = $this->checkUserRole();
		$is_jianzhi = $this->checkUserType();
		$is_admin = VipCommAction::checkIsAdmin();

		$type = isset($_REQUEST['type'])?$_REQUEST['type']:'';
		$handouts_subject = isset($_REQUEST['subject'])?intval($_REQUEST['subject']):'';
		$handouts_grade = isset($_REQUEST['grade'])?intval($_REQUEST['grade']):'';
		$startTime = isset($_REQUEST['startTime'])?$_REQUEST['startTime']:'';
		$endTime = isset($_REQUEST['endTime'])?$_REQUEST['endTime']:'';

		$handoutsType = C('HANDOUTS_TYPE');
		$vipSubjectModel = D('VpSubject');
		$subjectArr = $vipSubjectModel->get_subjectList('',$userInfo['user_key']);
		$subjectStr = '';
		$subjectAllArr = array();
		foreach($subjectArr as $key=>$value){
			$subjectStr .= $value['sid'].',';
			$subjectAllArr[$value['sid']] = $value['name'];
		}
		if(!empty($handouts_subject)){
			$subjectStr = $handouts_subject;
		}
		$vipGradeModel = D('VpGrade');
		if(!empty($handouts_subject)){
			$gradeArr = $vipGradeModel->get_gradeList_by_subjectid($handouts_subject);
		}else{
			$gradeArr = $vipGradeModel->get_gradeList('',$userInfo['user_key']);
		}
		$gradeStr = '';
		$gradeAllArr = array();
		foreach($gradeArr as $key=>$value){
			$gradeStr .= $value['gid'].',';
			$gradeAllArr[$value['gid']] = $value['name'];
		}
		if(!empty($handouts_grade)){
			$gradeStr = $handouts_grade;
		}
		$vipKnowledgeModel = D("VpKnowledge");
		if($is_jiaoyan == 0){
			$notStr = $vipKnowledgeModel->get_permission_subquery(array('sid'=>$handouts_subject,'gid'=>$handouts_grade,'kid'=>''));
			if(!empty($notStr)){
				$notStr = " AND vph.[hid] NOT IN (".$notStr.")";
			}
		}
		if(!$is_admin){
			if($is_jianzhi){
				$notStr .= " AND vph.is_parttime_visible = '1'";
			}
		}
		$vipHandoutsModel = D('VpHandouts');
		$uploadList = $vipHandoutsModel->get_historyUploadCount(trim($subjectStr,','),trim($gradeStr,','),'',$type,strtotime($startTime),strtotime($endTime),'my',$notStr);
		$jiangyiHistory = array();
		$shitiArray = array();

		foreach($uploadList as $key=>$value){
			$uploadList[$key]['sname']	= $subjectAllArr[$value['sid']].str_repeat("&nbsp;",$value['sid']%10);
			$uploadList[$key]['gname']	= $gradeAllArr[$value['gid']];
			if($value['type'] == 0){
				$uploadList[$key]['typename'] = '课程讲义'.str_repeat("&nbsp;",$value['sid']%10);
				$jiangyiHistory[] = $uploadList[$key];
			}else if($value['type'] == 1){
				$uploadList[$key]['typename'] = '试题库'.str_repeat("&nbsp;",$value['sid']%10);
				$shitiArray[] = $uploadList[$key];
			}
		}

		$this->assign(get_defined_vars());
		$this->display();
	}

	public function dealData(){
		$heluListAll = D('VpStudents')->deal_get_heluListAll();
		if(!empty($heluListAll)){
			foreach ($heluListAll as $key=>$val){
				if(!empty($val['handouts_url'])){
					$title = $val['skechengname'].'_'.$val['steachername'].'_'.$val['student_name'].'_'.$val['lesson_no'].'_'.'课程讲义_'.date('Y_m_d_H_i_s',strtotime($val['lasttime']));
					if(!D('VpStudents')->deal_insertFile(array('helu_id'=>$val['helu_id'],'title'=>$title,'url'=>$val['handouts_url'],'type'=>0))){
						print_r(array('helu_id'=>$val['helu_id'],'title'=>$title,'url'=>$val['handouts_url'],'type'=>0));
					}
				}
				if(!empty($val['itembank_url'])){
					$title = $val['skechengname'].'_'.$val['steachername'].'_'.$val['student_name'].'_'.$val['lesson_no'].'_'.'测试卷_'.date('Y_m_d_H_i_s',strtotime($val['lasttime']));
					if(!D('VpStudents')->deal_insertFile(array('helu_id'=>$val['helu_id'],'title'=>$title,'url'=>$val['itembank_url'],'type'=>1))){
						print_r(array('helu_id'=>$val['helu_id'],'title'=>$title,'url'=>$val['itembank_url'],'type'=>1));
					}
				}
			}
		}
		echo '数据处理成功';die;
	}

	/*批量更新讲义（试题库）文档数据标题*/
	public function updateFileTitle(){
		$studentsModel = D('VpStudents');
		$fileListAll = $studentsModel->deal_get_heluFilesAll();
		if(!empty($fileListAll)){
			foreach ($fileListAll as $key=>$val){
				if($val['type'] == 0){
					$type = '课程讲义';
				}else{
					$type = '测试卷';
				}
				$studentName = !empty($val['sstudentname'])?$val['sstudentname']:$val['student_name'];
				$lessonNo = !empty($val['nlessonno'])?$val['nlessonno']:$val['lesson_no'];
				$newTitle = $val['skechengname'].'_'.$val['steachername'].'_'.$studentName.'_'.$lessonNo.'_'.$type.'_'.date('Y_m_d',strtotime($val['dtdatereal']));
				if(!$studentsModel->deal_updateFileTitle(array('helu_id'=>$val['helu_id'],'new_title'=>$newTitle,'id'=>$val['id']))){
					echo 'fid:'.$val['id'].';helu_id:'.$val['helu_id'].'<br>';
				}
			}
		}
		echo '文档标题更新成功';die;
	}
	//向未进行核录的学员发送信息
	public function sendMsgToTea(){
		$studentsModel = D('VpStudents');
		$allStudents = $studentsModel->get_sended_students();
		if(empty($allStudents)){
			return false;
		}
		$sendMsgTea=array();
		foreach($allStudents as $key=>$value){
			if(!empty($value['sphone'])){
				$sendMsgTea[str_replace(" ",'',$value['sphone'])] .= "老师您好！您的学员".$value['sname']."（上课时间：".date("Y-m-d H:i",strtotime($value['dtlessonbegin']))."-".date("Y-m-d H:i",strtotime($value['dtlessonend']))."）已超过24小时未核录，请及时核录。祝：工作愉快！高思1对1\n";
			}
		}
		$smsObj = new SmsSender();
		foreach($sendMsgTea as $key=>$value){
			$smsContent = trim($value,"\n");
			$smsReturn = $smsObj->sendSms($key,$smsContent);
		}
	}

	//删除信息
	protected function deleteMsg(){
		$msgId = $_REQUEST['msgId'];
		$vipHandoutsModel = D('VpHandouts');
		if($vipHandoutsModel->delete_message_by_id($msgId)){
			echo 1;
		}else{
			echo 0;
		}
	}

	/*历史讲义（试题库）文件批量重命名*/
	/*public function renameFile(){
	$handoutsModel = D('VpHandouts');
	$handoutsList = $handoutsModel->getHandoutsAll();
	if(!empty($handoutsList)){
	foreach ($handoutsList as $key=>$row){
	$set = ' ';
	$prename = $row['sname'].'_'.$row['gname'].'_'.$row['kname'].'_';
	if(!empty($row['teacher_version'])){
	//获取文件类型
	$fileType = end(explode('.',$row['teacher_version']));
	$set .= " teacher_version_preview = '".str_replace('.'.$fileType,'.swf',$row['teacher_version'])."',";

	$lowerFileType = strtolower($fileType);
	$temp_arr = explode('/',$row['teacher_version']);
	$temp_arr[count($temp_arr)-1] = $prename.$row['title'].'_'.pathinfo($temp_arr[count($temp_arr)-1], PATHINFO_FILENAME);
	$handoutsList[$key]['new_teacher_version'] = implode('/',$temp_arr);
	if(file_exists(APP_DIR.$row['teacher_version'])){
	//@rename(APP_DIR.str_replace('.'.$fileType,'.swf',$row['teacher_version']),APP_DIR.$handoutsList[$key]['new_teacher_version'].'.swf');//swf
	//if($lowerFileType =='doc'|| $lowerFileType == 'docx'){
	//	@rename(APP_DIR.str_replace('.'.$fileType,'.pdf',$row['teacher_version']),APP_DIR.$handoutsList[$key]['new_teacher_version'].'.pdf');//pdf
	//}
	@rename(APP_DIR.$row['teacher_version'],APP_DIR.$handoutsList[$key]['new_teacher_version'].'.'.$fileType);
	$set .= " teacher_version = '".$handoutsList[$key]['new_teacher_version'].'.'.$fileType."',";
	}
	}
	if(!empty($row['student_version'])){
	//获取文件类型
	$fileType = end(explode('.',$row['student_version']));
	$set .= " student_version_preview = '".str_replace('.'.$fileType,'.swf',$row['student_version'])."',";

	$lowerFileType = strtolower($fileType);
	$temp_arr = explode('/',$row['student_version']);
	$temp_arr[count($temp_arr)-1] = $prename.$row['title'].'_'.pathinfo($temp_arr[count($temp_arr)-1], PATHINFO_FILENAME);
	$handoutsList[$key]['new_student_version'] = implode('/',$temp_arr);
	if(file_exists(APP_DIR.$row['student_version'])){
	//@rename(APP_DIR.str_replace('.'.$fileType,'.swf',$row['student_version']),APP_DIR.$handoutsList[$key]['new_student_version'].'.swf');//swf
	//if($lowerFileType =='doc'|| $lowerFileType == 'docx'){
	//	@rename(APP_DIR.str_replace('.'.$fileType,'.pdf',$row['student_version']),APP_DIR.$handoutsList[$key]['new_student_version'].'.pdf');//pdf
	//}
	@rename(APP_DIR.$row['student_version'],APP_DIR.$handoutsList[$key]['new_student_version'].'.'.$fileType);
	$set .= " student_version = '".$handoutsList[$key]['new_student_version'].'.'.$fileType."',";
	}
	}
	if(!empty($set)){
	$handoutsModel->updateHandoutsUrl($set,$row['hid']);
	}
	}

	}else{
	echo '暂时没有需要批量重命名的讲义';die;
	}

	}*/

	/*手动批量生成预览版讲义*/
	public function createPreview(){
		$userKey = $this->loginUser->getUserKey();
		if($userKey == 'Employee-xiecuiping'){
			//$filename=APP_DIR.'/Upload/'.time().'.txt';
			//$fp=fopen($filename, "w+"); //打开文件指针，创建文件
			//if ( !is_writable($filename) ){
			//	die("文件:" .$filename. "不可写，请检查！");
			//}

			//define('APP_DIR', APP_DIR);
			define('PDFTOSWF', '/usr/local/bin/pdf2swf');
			$content = '';
			try{
				//$dao = new PDO('dblib:host=211.157.101.115:11533;dbname=GSTest', 'admin', 'hxj@)!*gsEdu');
				$dao = new PDO('dblib:host=db.gaosiedu.com:11533;dbname=GS', 'admin', 'hxj@)!*gsEdu');//线上
			} catch (PDOException $e) {
				$content .=  '无法访问业务系统!';
			}
			$query = $dao->query("SELECT * FROM vp_handouts WHERE is_delete = 0 and hid = '".$_GET['hid']."' ORDER BY hid DESC");
			$list = array();
			while($row=$query->fetch()){
				$list[] = $row;
			}
			print_r($list);
			if(!empty($list)){
				foreach($list as $key=>$row){
					$content .=  $row['hid']."\r\n";
					if($row['is_rename'] == 1){
						if(!empty($row['teacher_version']) && file_exists(APP_DIR.$row['teacher_version'])){
							if(!empty($row['teacher_version_preview']) && file_exists(APP_DIR.$row['teacher_version_preview'])){
								$content .= "UPDATE vp_handouts set is_exist_teacher_preview = '1' WHERE hid = '".$row['hid']."';";
								if($dao->exec("UPDATE vp_handouts set is_exist_teacher_preview = '1' WHERE hid = '".$row['hid']."'")){
									$content .= " success\r\n";
								}else{
									$content .= " failed\r\n";
								}

							}else{
								$content .= $this->doConvert($row['teacher_version'],$row['hid'],'teacher_version',$row['is_rename']);
							}
						}
						if(!empty($row['student_version']) && file_exists(APP_DIR.$row['student_version'])){
							if(!empty($row['student_version_preview']) && file_exists(APP_DIR.$row['student_version_preview'])){
								$content .=  "UPDATE vp_handouts set is_exist_student_preview = '1' WHERE hid = '".$row['hid']."'";
								if($dao->exec("UPDATE vp_handouts set is_exist_student_preview = '1' WHERE hid = '".$row['hid']."'")){
									$content .= " success\r\n";
								}else{
									$content .= " failed\r\n";
								}
							}else{
								$content .= $this->doConvert($row['student_version'],$row['hid'],'student_version',$row['is_rename']);
							}
						}
					}else{
						if(!empty($row['teacher_version']) && file_exists(APP_DIR.$row['teacher_version'])){
							$swfFile =  reset(explode('.',$row['teacher_version'])).'.swf';
							if(!file_exists(APP_DIR.$swfFile)){
								$content .= $this->doConvert($row['teacher_version'],$row['hid'],'teacher_version',$row['is_rename']);
							}else{
								$content .=  "UPDATE vp_handouts set is_exist_teacher_preview = '1' ,teacher_version_preview = '".$swfFile."' WHERE hid = '".$row['hid']."'";
								if($dao->exec("UPDATE vp_handouts set is_exist_teacher_preview = '1' ,teacher_version_preview = '".$swfFile."' WHERE hid = '".$row['hid']."'")){
									$content .= " success\r\n";
								}else{
									$content .= " failed\r\n";
								}
							}
						}
						if(!empty($row['student_version'])  && file_exists(APP_DIR.$row['student_version'])){
							$swfFile =  reset(explode('.',$row['student_version'])).'.swf';
							if(!file_exists(APP_DIR.$swfFile)){
								$content .= $this->doConvert($row['student_version'],$row['hid'],'student_version',$row['is_rename']);
							}else{
								$content .=  "UPDATE vp_handouts set is_exist_student_preview = '1' ,student_version_preview = '".$swfFile."' WHERE hid = '".$row['hid']."'";
								if($dao->exec("UPDATE vp_handouts set is_exist_student_preview = '1' ,student_version_preview = '".$swfFile."' WHERE hid = '".$row['hid']."'")){
									$content .= " success\r\n";
								}else{
									$content .= " failed\r\n";
								}
							}
						}
					}
				}
			}
			echo $content;
			exit;
		}
	}


	public function doConvert($file, $hid, $type, $is_rename){
		$content = '';
		try{
			//$dao = new PDO('dblib:host=211.157.101.115:11533;dbname=GSTest', 'admin', 'hxj@)!*gsEdu');
			$dao = new PDO('dblib:host=db.gaosiedu.com:11533;dbname=GS', 'admin', 'hxj@)!*gsEdu');//线上
		} catch (PDOException $e) {
			$content .=  '无法访问业务系统!';
		}
		if(strpos('a'.$file,"(")){
			$sourceFile = str_replace('(','（',str_replace(')','）',$file));
		}else{
			$sourceFile = $file;
		}
		if(file_exists(APP_DIR.$sourceFile)){
			$fileType = strtolower(end(explode('.',$sourceFile)));
			//$filesize = filesize($sourceFile);
			if($fileType != 'pdf'){
				if($is_rename == 1){echo '非pdf<br>';
				$fileNameArr = explode('/',trim($sourceFile,'/'));
				$tempFileNameArr = explode('.',end(explode('_',$fileNameArr[2])));
				$tempFile = APP_DIR.'/'.$fileNameArr[0].'/'.$fileNameArr[1].'/'.$tempFileNameArr[0].'.'.$tempFileNameArr[1].'.pdf';
				$swfFile = APP_DIR.'/'.$fileNameArr[0].'/'.$fileNameArr[1].'/'.$tempFileNameArr[0].'.'.$tempFileNameArr[1].'.swf';
				}else{
					$tempFile = APP_DIR.reset(explode('.',$sourceFile)).'.pdf';
					$swfFile = APP_DIR.reset(explode('.',$sourceFile)).'.swf';
				}

				$commond = "unoconv -f pdf -o $tempFile  ".APP_DIR.$sourceFile;
				$commond2 = PDFTOSWF." -T 9 -s poly2bitmap ".$tempFile." ".$swfFile;
				if(file_exists(APP_DIR.$sourceFile)){
					exec($commond);
				}
				if(file_exists($tempFile)){
					exec($commond2);
				}
				$content .= $commond."\r\n".$commond2."\r\n";
				if(file_exists($swfFile)){
					if($type == 'teacher_version'){
						$updateQuery = "UPDATE vp_handouts set teacher_version_preview = '".end(explode('/eap',$swfFile))."',is_exist_teacher_preview = 1 where hid = '".$hid."'";
					}else{
						$updateQuery = "UPDATE vp_handouts set student_version_preview = '".end(explode('/eap',$swfFile))."',is_exist_student_preview = 1 where hid = '".$hid."'";
					}
					$content .= $hid.$type." preview success\r\n";
					$dao->exec($updateQuery);
				}else{
					$content .= $hid.'-'.$type." preview failed\r\n";
				}

			}
			if($fileType == 'pdf'){
				if($is_rename == 1){
					$fileNameArr = explode('/',trim($sourceFile,'/'));
					$tempFileNameArr = explode('.',end(explode('_',$fileNameArr[2])));
					$swfFile = APP_DIR.'/'.$fileNameArr[0].'/'.$fileNameArr[1].'/'.$tempFileNameArr[0].'.'.$tempFileNameArr[1].'.swf';
				}else{
					$tempFile = APP_DIR.reset(explode('.',$sourceFile)).'.pdf';
					$swfFile = APP_DIR.reset(explode('.',$sourceFile)).'.swf';
				}
				$commond = PDFTOSWF." -T 9 -s poly2bitmap ".APP_DIR.$sourceFile." ".$swfFile;
				exec($commond);
				$content .= $commond."\r\n";
				if(file_exists($swfFile)){
					if($type == 'teacher_version'){
						$updateQuery = "UPDATE vp_handouts set teacher_version_preview = '".end(explode('/eap',$swfFile))."',is_exist_teacher_preview = 1 where hid = '".$hid."'";
					}else{
						$updateQuery = "UPDATE vp_handouts set student_version_preview = '".end(explode('/eap',$swfFile))."',is_exist_student_preview = 1 where hid = '".$hid."'";
					}
					$content .= $hid.$type." preview success\r\n";
					$dao->exec($updateQuery);
				}else{
					$content .= $hid.'-'.$type." preview failed\r\n";
				}

			}
		}else{
			$content .= $hid.'-'.$type." file not exist\r\n";
		}
		return $content;
	}



	public function doOverdue(){
		$status = 0;
		$helu_id = abs($_GET['helu_id']);
		if(D('VpStudents')->do_overdue($helu_id)){
			$status = 1;
		}
		echo json_encode(array('status'=>$status));
		exit;
	}



	public function newIndex(){
		$userInfo = VipCommAction::get_currentUserInfo();
		if($userInfo['user_key'] == 'Employee-wangyan'){
			$userInfo['user_key'] = 'Employee-guoluping';
			$userInfo['sCode'] = 'VP00022';
		}
		$userInfo['userTypeKey'] = reset(explode('-',$userInfo['user_key']));
		$studentsModel = D('VpStudents');
		if($userInfo['sCode']){
			$waitHeluList = $this->getWaitHelu(array('teacherCode'=>$userInfo['sCode'],'now'=>date('Y-m-d H:i:s'),'userTypeKey'=>$userInfo['userTypeKey'],'overdue'=>1));
		}

		$messageList = D('VpHandouts')->get_messageList($userInfo['user_key']);
		$is_jiaoyan = $this->checkUserRole();
		$is_jianzhi = $this->checkUserType();
		$is_admin = VipCommAction::checkIsAdmin();

		$vipSubjectModel = D('VpSubject');
		$subjectArr = $vipSubjectModel->get_subjectList('',$userInfo['user_key']);
		$subjectStr = '';
		foreach($subjectArr as $key=>$value){
			$subjectStr .= $value['sid'].',';
		}

		$vipGradeModel = D('VpGrade');

		$gradeArr = $vipGradeModel->get_gradeList('',$userInfo['user_key']);
		$gradeStr = '';
		foreach($gradeArr as $key=>$value){
			$gradeStr .= $value['gid'].',';
		}
		$startTime = strtotime(date("Y-m-d",time()-(date('w',time())-1)*3600*24));
		$endTime = $startTime+3600*24*7-1;
		$vipHandoutsModel = D('VpHandouts');
		$vipKnowledgeModel = D("VpKnowledge");

		//待备课学员
		if($userInfo['sCode']){
			$waitPrepareList = $this->getWaitPrepare(array('teacherCode'=>$userInfo['sCode'],'now'=>date('Y-m-d H:i:s'),'userTypeKey'=>$userInfo['userTypeKey']));
		}

		$lectureList = $vipHandoutsModel->get_allCreateLecture(trim($subjectStr,','),trim($gradeStr,','),'','',$startTime,$endTime,'my');
		foreach($lectureList as $key=>$value){
			$lectureList[$key]['sname'] = $value['sname'];
			if($key > 1){
				if($lectureList[$key]['gname'] == trim($lectureList[$key-1]['gname'],"&nbsp;") && $lectureList[$key]['sname'] != $lectureList[$key-1]['sname']){
					$lectureList[$key]['gname'] = $lectureList[$key]['gname'].str_repeat("&nbsp;",$key%10+1);
				}else if($lectureList[$key]['gname'] == trim($lectureList[$key-1]['gname'],"&nbsp;") && $lectureList[$key]['sname'] == $lectureList[$key-1]['sname']){
					$lectureList[$key]['gname'] = $lectureList[$key-1]['gname'];
				}
				if($lectureList[$key]['kname'] == trim($lectureList[$key-1]['kname'],'&nbsp;') && ($lectureList[$key]['gname'] != trim($lectureList[$key-1]['gname'],'&nbsp;') || $lectureList[$key]['sname'] != $lectureList[$key-1]['sname'])){
					$lectureList[$key]['kname'] = $lectureList[$key]['kname'].str_repeat("&nbsp;",$key%10+1);
				}else if($lectureList[$key]['kname'] == trim($lectureList[$key-1]['kname'],'&nbsp;') && $lectureList[$key]['gname'] == trim($lectureList[$key-1]['gname'],'&nbsp;') && $lectureList[$key]['sname'] == $uploadList[$key-1]['sname']){
					$lectureList[$key]['kname'] = $lectureList[$key-1]['kname'];
				}
			}

		}
		
		
		//查询待生成辅导方案学员
		$newStudentsModel = D('VpNewStudents');
		$testCoachList = $newStudentsModel->get_testCoachAll(array('teacher_code'=>$userInfo['sCode'],'submit'=>0));
		
		$this->assign(get_defined_vars());
		$this->display();
	}

	public function newMyworkhistory()
	{
		$userInfo = VipCommAction::get_currentUserInfo();
		$studentsModel = D('VpStudents');
		if($userInfo['user_key'] == 'Employee-wangyan'){
			$userInfo['user_key'] = 'Employee-guoluping';
			$userInfo['sCode'] = 'VP00022';
		}
		if($userInfo['sCode']){
			$waitHeluList = $this->getWaitHelu(array('teacherCode'=>$userInfo['sCode'],'now'=>date('Y-m-d H:i:s'),'userTypeKey'=>reset(explode('-',$userInfo['user_key'])),'overdue'=>1));
		}

		$messageList = D('VpHandouts')->get_messageList($userInfo['user_key']);

		$is_jiaoyan = $this->checkUserRole();
		$is_jianzhi = $this->checkUserType();
		$is_admin = VipCommAction::checkIsAdmin();


		//$type = isset($_REQUEST['type'])?$_REQUEST['type']:'';
		$gradeId= isset($_REQUEST['grade_id'])?intval($_REQUEST['grade_id']):'';
		$subjectId= isset($_REQUEST['knowledge_id'])?intval($_REQUEST['knowledge_id']):'';
		$handouts_subject = isset($_REQUEST['course_id_one'])?intval($_REQUEST['course_id_one']):'';
		$handouts_grade = isset($_REQUEST['course_id_two'])?intval($_REQUEST['course_id_two']):'';
		$handouts_knowledge = isset($_REQUEST['course_id_three'])?intval($_REQUEST['course_id_three']):'';
		$startTime = isset($_REQUEST['startTime'])?$_REQUEST['startTime']:'';
		$endTime = isset($_REQUEST['endTime'])?$_REQUEST['endTime']:'';
		$vipBasic=D ( 'Basic' );
		//学部列表
		$gradeArr=$vipBasic->getGrades();
		
		$vipSubjectModel = D('VpSubject');

		//根据授权学科查询
		$subjectsArr = $vipSubjectModel->get_subjectLists($gradeId,$userInfo['user_key']);
		$subjectStr = '';
		$subjectAllArr = array();
		foreach($subjectsArr as $key=>$value){
			$subjectStr .= $value['id'].',';
			$subjectAllArr[$value['id']] = $value['title'];
		}
		
		if(!empty($handouts_subject)){
			$subjectStr = $handouts_subject;
		}

		if(!empty($handouts_grade)){
			$gradeStr = $handouts_grade;
		}

		if(!empty($handouts_knowledge)){
			$knowledgeStr = $handouts_knowledge;
		}

		//如果学科id不为空，查询课程属性
		/*if(!empty($handouts_subject)){
			$gradesArr = $vipBasic->getCourseTypesBySubjectId($handouts_subject);
		}

		$gradeStr = '';
		$gradeAllArr = array();
		foreach($gradesArr as $key=>$value){
			$gradeStr .= $value['id'].',';
			$gradeAllArr[$value['id']] = $value['title'];
		}*/
		

		//判断如果课程属性id 不为空，查询讲义属性
		/*if(!empty($handouts_subject)){
			if(!empty($handouts_grade)){
				$knowledgeArr = $vipBasic->getCourseTypesBySubjectAll($handouts_grade);
			}
		}else{
			$knowledgeArr = array();
		}
		$knowledgeStr = '';
		$knowledgeAllArr = array();
		if(!empty($knowledgeArr)){
			foreach($knowledgeArr as $key=>$value){
				$knowledgeStr .= $value['id'].',';
				$knowledgeAllArr[$value['id']] = $value['name'];
			}
		}*/

		$vipHandoutsModel = D('VpHandouts');


		//待备课学员
		if($userInfo['sCode']){
			$waitPrepareList = $this->getWaitPrepare(array('teacherCode'=>$userInfo['sCode'],'now'=>date('Y-m-d H:i:s'),'userTypeKey'=>$userInfo['userTypeKey']));
		}

		$lectureList = array();
		if(!empty($_POST)){
			$lectureList = $vipHandoutsModel->get_allCreateLectures(trim($subjectStr,','),trim($gradeStr,','),trim($knowledgeStr,','),'','','','my');
			foreach($lectureList as $key=>$value){
				$lectureList[$key]['sname'] = $value['sname'];
				if($key > 1){
					if($lectureList[$key]['gname'] == trim($lectureList[$key-1]['gname'],"&nbsp;") && $lectureList[$key]['sname'] != $lectureList[$key-1]['sname']){
						$lectureList[$key]['gname'] = $lectureList[$key]['gname'].str_repeat("&nbsp;",$key%10+1);
					}else if($lectureList[$key]['gname'] == trim($lectureList[$key-1]['gname'],"&nbsp;") && $lectureList[$key]['sname'] == $lectureList[$key-1]['sname']){
						$lectureList[$key]['gname'] = $lectureList[$key-1]['gname'];
					}
					if(($lectureList[$key]['kname'] == trim($lectureList[$key-1]['kname'],'&nbsp;') && ($lectureList[$key]['gname'] != trim($lectureList[$key-1]['gname'],'&nbsp;') || $lectureList[$key]['sname'] != $lectureList[$key-1]['sname']))||$lectureList[$key]['kname'] != trim($lectureList[$key-1]['kname'],'&nbsp;')){
						$lectureList[$key]['kname'] = $lectureList[$key]['kname'].str_repeat("&nbsp;",$key%10+1);
					}else{
						$lectureList[$key]['kname'] = $lectureList[$key-1]['kname'];
					}
				}

			}
		}

		//查询待生成辅导方案学员
		$newStudentsModel = D('VpNewStudents');
		$testCoachList = $newStudentsModel->get_testCoachAll(array('teacher_code'=>$userInfo['sCode'],'submit'=>0));//获取业务系统当前教师下该学员未提交的测辅数据
		
		$this->assign(get_defined_vars());
		$this->display();


	}


	public function newMyworkhistory_old(){
		$userInfo = VipCommAction::get_currentUserInfo();
		$studentsModel = D('VpStudents');
		if($userInfo['user_key'] == 'Employee-wangyan'){
			$userInfo['user_key'] = 'Employee-guoluping';
			$userInfo['sCode'] = 'VP00022';
		}
		if($userInfo['sCode']){
			$waitHeluList = $this->getWaitHelu(array('teacherCode'=>$userInfo['sCode'],'now'=>date('Y-m-d H:i:s'),'userTypeKey'=>reset(explode('-',$userInfo['user_key'])),'overdue'=>1));
		}

		$messageList = D('VpHandouts')->get_messageList($userInfo['user_key']);

		$is_jiaoyan = $this->checkUserRole();
		$is_jianzhi = $this->checkUserType();
		$is_admin = VipCommAction::checkIsAdmin();

		$type = isset($_REQUEST['type'])?$_REQUEST['type']:'';
		$handouts_subject = isset($_REQUEST['subject'])?intval($_REQUEST['subject']):'';
		$handouts_grade = isset($_REQUEST['grade'])?intval($_REQUEST['grade']):'';
		$handouts_knowledge = isset($_REQUEST['knowledge'])?intval($_REQUEST['knowledge']):'';
		$startTime = isset($_REQUEST['startTime'])?$_REQUEST['startTime']:'';
		$endTime = isset($_REQUEST['endTime'])?$_REQUEST['endTime']:'';

		$handoutsType = C('HANDOUTS_TYPE');
		$vipSubjectModel = D('VpSubject');
		$subjectArr = $vipSubjectModel->get_subjectList('',$userInfo['user_key']);
		$subjectStr = '';
		$subjectAllArr = array();
		foreach($subjectArr as $key=>$value){
			$subjectStr .= $value['sid'].',';
			$subjectAllArr[$value['sid']] = $value['name'];
		}
		if(!empty($handouts_subject)){
			$subjectStr = $handouts_subject;
		}
		$vipGradeModel = D('VpGrade');
		if(!empty($handouts_subject)){
			$gradeArr = $vipGradeModel->get_gradeList_by_subjectid($handouts_subject);
		}else{
			$gradeArr = $vipGradeModel->get_gradeList('',$userInfo['user_key']);
		}
		$gradeStr = '';
		$gradeAllArr = array();
		foreach($gradeArr as $key=>$value){
			$gradeStr .= $value['gid'].',';
			$gradeAllArr[$value['gid']] = $value['name'];
		}
		if(!empty($handouts_grade)){
			$gradeStr = $handouts_grade;
		}
		$vipKnowledgeModel = D('VpKnowledge');
		if(!empty($handouts_subject)){
			if(!empty($handouts_grade)){
				$knowledgeArr = $vipKnowledgeModel->get_knowledgeList_by_gradeid_and_subjectid(array('sid'=>$handouts_subject,'gid'=>$handouts_grade,'is_jiaoyan'=>1));
			}else{
				$knowledgeArr = $vipKnowledgeModel->get_knowledgeList_by_subjectid($handouts_subject);
			}
		}else{
			$knowledgeArr = array();
		}
		$knowledgeStr = '';
		$knowledgeAllArr = array();
		if(!empty($knowledgeArr)){
			foreach($knowledgeArr as $key=>$value){
				$knowledgeStr .= $value['kid'].',';
				$knowledgeAllArr[$value['kid']] = $value['name'];
			}
		}

		if(!empty($handouts_knowledge)){
			$knowledgeStr = $handouts_knowledge;
		}
		
		$vipHandoutsModel = D('VpHandouts');


		//待备课学员
		if($userInfo['sCode']){
			$waitPrepareList = $this->getWaitPrepare(array('teacherCode'=>$userInfo['sCode'],'now'=>date('Y-m-d H:i:s'),'userTypeKey'=>$userInfo['userTypeKey']));
		}


		/*$lectureList = $vipHandoutsModel->get_allHistoryLectureCount(trim($subjectStr,','),trim($gradeStr,','),'','',$startTime,$endTime,'my');
		foreach($lectureList as $key=>$value){
		$lectureList[$key]['sname']	= $subjectAllArr[$value['subject_id']].str_repeat("&nbsp;",$value['subject_id']%10);
		if($key > 1){
		if($lectureList[$key]['gname'] == trim($lectureList[$key-1]['gname'],"&nbsp;") && $lectureList[$key]['sname'] != $lectureList[$key-1]['sname']){
		$lectureList[$key]['gname'] = $lectureList[$key]['gname'].str_repeat("&nbsp;",$key%10+1);
		}else if($lectureList[$key]['gname'] == trim($lectureList[$key-1]['gname'],"&nbsp;") && $lectureList[$key]['sname'] == $lectureList[$key-1]['sname']){
		$lectureList[$key]['gname'] = $lectureList[$key-1]['gname'];
		}

		}
		}*/
		$lectureList = array();
		if(!empty($_POST)){
			$lectureList = $vipHandoutsModel->get_allCreateLecture(trim($subjectStr,','),trim($gradeStr,','),trim($knowledgeStr,','),'','','','my');
			var_dump($lectureList);exit;
			foreach($lectureList as $key=>$value){
				$lectureList[$key]['sname'] = $value['sname'];
				if($key > 1){
					if($lectureList[$key]['gname'] == trim($lectureList[$key-1]['gname'],"&nbsp;") && $lectureList[$key]['sname'] != $lectureList[$key-1]['sname']){
						$lectureList[$key]['gname'] = $lectureList[$key]['gname'].str_repeat("&nbsp;",$key%10+1);
					}else if($lectureList[$key]['gname'] == trim($lectureList[$key-1]['gname'],"&nbsp;") && $lectureList[$key]['sname'] == $lectureList[$key-1]['sname']){
						$lectureList[$key]['gname'] = $lectureList[$key-1]['gname'];
					}
					if(($lectureList[$key]['kname'] == trim($lectureList[$key-1]['kname'],'&nbsp;') && ($lectureList[$key]['gname'] != trim($lectureList[$key-1]['gname'],'&nbsp;') || $lectureList[$key]['sname'] != $lectureList[$key-1]['sname']))||$lectureList[$key]['kname'] != trim($lectureList[$key-1]['kname'],'&nbsp;')){
						$lectureList[$key]['kname'] = $lectureList[$key]['kname'].str_repeat("&nbsp;",$key%10+1);
					}else{
						$lectureList[$key]['kname'] = $lectureList[$key-1]['kname'];
					}
				}

			}
		}

		//查询待生成辅导方案学员
		$newStudentsModel = D('VpNewStudents');
		$testCoachList = $newStudentsModel->get_testCoachAll(array('teacher_code'=>$userInfo['sCode'],'submit'=>0));//获取业务系统当前教师下该学员未提交的测辅数据
		
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function getWaitHelu($arr){
		return	D('VpStudents')->get_waitHeluList($arr);
	}


	public function getWaitPrepare($arr){
		return	D('VpStudents')->get_waitPrepareAll($arr);
	}

	public function previewLecture(){
		$lecture_id= $_GET['lecture_id'];
		if(!empty($lecture_id)){
			$newStudentsModel = D('VpNewStudents');
			$numberKey = C('NUMBER_KEY');
			$optionKeyArr = C('OPTIONS_KEY');
			$heluInfo = $this->getHeluInfo($helu_id);
			//$heluInfo = $newStudentsModel->get_heluInfo($helu_id);
		}
		$this->assign(get_defined_vars());
		$this->display();
	}

	public function getLectureInfo($lectureId){
		$key = md5($heluId);
		import('ORG.Util.NCache');
		$cache = NCache::getCache();
		$heluInfo = $cache->get('heluInfo', $key);
		if(false == $heluInfo) {
			$newStudentsModel = D('VpNewStudents');
			$heluInfo = $newStudentsModel->get_heluInfo($heluId);
			$cache->set('heluInfo', $key, $heluInfo);
		}
		return $heluInfo;
	}

}

?>