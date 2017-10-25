<?php
/*教师内容审查*/
class VipContentAction extends VipCommAction{

	/*讲义存档*/
	public function handoutsArchive(){
		$editPerm = Permission::getPermInfo($this->loginUser, $this->getAclKey());
		$permValue = $editPerm['permValue'];
		$menuTreeUrl = $this->getUrl('jsonMenuTree');
		$delFileUrl = $this->getUrl('deleteFiles');
		$moveFileUrl = $this->getUrl('moveFiles');
		$url = $this->getUrl('getContent');
		$this->assign(get_defined_vars());
		$this->display();
	}

	protected function jsonMenuTree(){
		$contentModel = D('VpContent');
		$menuTree = $contentModel->get_menuTree();
		echo json_encode($menuTree);
	}


	protected function getContent(){
		$currentPage = abs($_POST['page']);
		$pageSize = abs($_POST['rows']);
		$filesList = array();
		$total=0;
		$deptCode = isset($_GET['deptCode'])?SysUtil::uuid($_GET['deptCode']):'';
		$xuekeId = isset($_GET['xuekeId'])?SysUtil::uuid($_GET['xuekeId']):'';
		$teacherCode = isset($_GET['teacherCode'])?SysUtil::uuid($_GET['teacherCode']):'';
		$studentCode = isset($_GET['studentCode'])?SysUtil::uuid($_GET['studentCode']):'';
		$lessonNo = isset($_GET['lessonNo'])?SysUtil::uuid($_GET['lessonNo']):'';
		$keyword = isset($_GET['keyword'])?urldecode(SysUtil::uuid($_GET['keyword'])):'';
		$start = isset($_GET['start'])?trim(SysUtil::uuid($_GET['start'])):'';
		$end = isset($_GET['end'])?trim(SysUtil::uuid($_GET['end'])):'';
		if(!empty($deptCode)||!empty($xuekeId)||!empty($teacherCode)||!empty($studentCode)||!empty($keyword) ||!empty($start) || !empty($end) ){
			list($total, $filesList) = D('VpContent')->get_contents(array('deptCode'=>$deptCode,'xuekeId'=>$xuekeId,'teacherCode'=>$teacherCode,'studentCode'=>$studentCode,'lessonNo'=>$lessonNo,'keyword'=>$keyword,'start'=>$start,'end'=>$end),$currentPage, $pageSize);
		}
		echo json_encode(array('total'=>$total, 'rows'=>$filesList));
	}



	protected function deleteFiles(){
		if($_POST['is_batch'] == 1){
			$heluIdArr = explode('|',trim($_POST['idStr'],'|'));
		}else{
			$heluIdArr = array($_POST['idStr']);
		}
		$total = count($heluIdArr);
		$total_succ = 0;
		if(!empty($heluIdArr)){
			foreach ($heluIdArr as $key=>$helu){
				$helu_id = reset(explode('_',$helu));
				$type = end(explode('_',$helu));
				if(D('VpContent')->delete_contents(array('helu_id'=>$helu_id,'type'=>$type))){
					$total_succ++;
				}
			}
		}
		echo '共选择:'.$total.'，删除成功:'.$total_succ;
	}


	protected function moveFiles(){
		$idStr = trim($_GET['idStr'],'|');
		$doMoveUrl = $this->getUrl('doMoveFiles');
		$getKechengUrl = $this->getUrl('getKecheng');
		$getLessonNoUrl = $this->getUrl('getLessonNo');
		$menuTreeUrl = $this->getUrl('jsonMenuTree');
		$this->assign(get_defined_vars());
		$this->display();
	}


	protected function doMoveFiles(){
		$resultScript = true;
		$heluIdArr = explode('|',trim($_POST['idStr'],'|'));
		$lessonNo = abs($_POST['lessonNo']);
		$kechengCode = $_POST['kechengCode'];
		$to_xuekeId = isset($_GET['xuekeId'])?SysUtil::uuid($_GET['xuekeId']):'';
		$to_teacherCode = isset($_GET['teacherCode'])?SysUtil::uuid($_GET['teacherCode']):'';
		$to_studentCode = isset($_GET['studentCode'])?SysUtil::uuid($_GET['studentCode']):'';
		if(!empty($to_xuekeId) && !empty($to_teacherCode) && !empty($to_studentCode) && !empty($kechengCode) && !empty($lessonNo)){
			$succNum = D('VpContent')->do_moveFiles(array('heluIdArr'=>$heluIdArr,'kechengCode'=>$kechengCode,'lessonNo'=>$lessonNo,'xuekeId'=>$to_xuekeId,'teacherCode'=>$to_teacherCode,'studentCode'=>$to_studentCode));
			if($succNum>0){
				$status = 1;
				$msg = '文档移动成功：请求移动文档'.count($heluIdArr).'个;成功'.$succNum.'个';
			}else{
				$status = 0;
				$msg = '文档移动失败';
			}
		}else{
			$status = 0;
			$msg = '请选择完整的目标位置：校区+学科+教师+学员';
		}
		echo json_encode(array('status'=>$status,'msg'=>$msg));
	}


	protected function getKecheng(){
		$to_xuekeId = isset($_GET['xuekeId'])?SysUtil::uuid($_GET['xuekeId']):'';
		$to_teacherCode = isset($_GET['teacherCode'])?SysUtil::uuid($_GET['teacherCode']):'';
		$to_studentCode = isset($_GET['studentCode'])?SysUtil::uuid($_GET['studentCode']):'';
		$html = '';
		if(!empty($to_xuekeId) && !empty($to_teacherCode) && !empty($to_studentCode)){
			$kechengArr = D('VpStudents')->get_kechengAll(array('is_jieke'=>0,'teacherCode'=>$to_teacherCode,'studentCode'=>$to_studentCode));
			$html .= '<option value="">请选择目标课程</option>';
			if(!empty($kechengArr)){
				foreach ($kechengArr as $key=>$kecheng){
					$html .= '<option value="'.$kecheng['skechengcode'].'">'.$kecheng['skechengname'].'('.$kecheng['skechengcode'].')</option>';
				}
			}
			$status = 1;
		}else{
			$status = 0;
		}
		echo json_encode(array('status'=>$status,'html'=>$html));
	}


	protected function getLessonNo(){
		$to_xuekeId = isset($_GET['xuekeId'])?SysUtil::uuid($_GET['xuekeId']):'';
		$to_teacherCode = isset($_GET['teacherCode'])?SysUtil::uuid($_GET['teacherCode']):'';
		$to_studentCode = isset($_GET['studentCode'])?SysUtil::uuid($_GET['studentCode']):'';
		$to_kechengCode = isset($_GET['kechengCode'])?SysUtil::uuid($_GET['kechengCode']):'';
		$html = '';
		if(!empty($to_xuekeId) && !empty($to_teacherCode) && !empty($to_studentCode) && !empty($to_kechengCode)){
			$lessonNoArr = D('VpContent')->get_lessonNo(array('teacherCode'=>$to_teacherCode,'studentCode'=>$to_studentCode,'kechengCode'=>$to_kechengCode));
			$html .= '<option value="">请选择目标讲次</option>';
			if(!empty($lessonNoArr)){
				foreach ($lessonNoArr as $key=>$lessonNo){
					$html .= '<option value="'.$lessonNo['nlessonno'].'">'.$lessonNo['nlessonno'];
					if(!empty($lessonNo['lesson_topic'])){
						$html .= '('.$lessonNo['lesson_topic'].')';
					}
					$html .= '</option>';
				}
			}
			$status = 1;
		}else{
			$status = 0;
		}
		echo json_encode(array('status'=>$status,'html'=>$html));
	}


	/*课节审查*/
	public function lessonReview(){
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$deptCode = isset($_REQUEST['deptCode'])?$_REQUEST['deptCode']:'';
		$teacherCode = isset($_REQUEST['teacherCode'])?$_REQUEST['teacherCode']:'';
		$studentCode = isset($_REQUEST['studentCode'])?$_REQUEST['studentCode']:'';
		$startTime = isset($_REQUEST['starttime'])?trim($_REQUEST['starttime']):'';
		$endTime = isset($_REQUEST['endtime'])?trim($_REQUEST['endtime']):'';
		$is_upload = isset($_REQUEST['is_upload'])?$_REQUEST['is_upload']:'';
		$teacherName = isset($_REQUEST['teacherName'])?$_REQUEST['teacherName']:'';
		$studentName = isset($_REQUEST['studentName'])?$_REQUEST['studentName']:'';
		$conditionArr = array('deptCode'=>$deptCode,'teacherCode'=>$teacherCode,'studentCode'=>$studentCode,'startTime'=>$startTime,'endTime'=>$endTime,'is_upload'=>$is_upload,'teacherName'=>urldecode($teacherName),'studentName'=>urldecode($studentName));
		$contentModel = D('VpContent');
		$lessonHeluList = $contentModel->get_lessonHeLu($conditionArr,$curPage,$pagesize);
		$count = $contentModel->get_lessonHeLuCount($conditionArr);
		$page = new page($count,$pagesize);
		$showPage = $page->show();

		$deptList = $contentModel->get_deptList();
		$teacherList = $contentModel->get_vipTeacherList(array('deptCode'=>$deptCode));
		$studentList = D('VpStudents')->get_allStudents(array('is_jieke'=>0,'teacherCode'=>$teacherCode));

		$this->assign(get_defined_vars());
		$this->display();
	}


	protected function getStudentList(){
		$teacherCode = isset($_GET['teacherCode'])?$_GET['teacherCode']:'';
		$studentList = D('VpStudents')->get_allStudents(array('is_jieke'=>0,'teacherCode'=>$teacherCode));
		$html = '<option value="">请选择学员</option>';
		if(!empty($studentList)){
			foreach ($studentList as $key=>$val){
				$html .= '<option value="'.$val['sstudentcode'].'">'.$val['sstudentname'].'</option>';
			}
		}
		echo $html;
	}


	protected function getTeacherList(){
		$deptCode = isset($_GET['deptCode'])?$_GET['deptCode']:'';
		$teacherList = D('VpContent')->get_vipTeacherList(array('deptCode'=>$deptCode));
		$html = '<option value="">请选择教师</option>';
		if(!empty($teacherList)){
			foreach ($teacherList as $key=>$val){
				$html .= '<option value="'.$val['scode'].'">'.$val['sname'].'</option>';
			}
		}
		echo $html;
	}



	public function viewFile(){
		$helu_id = isset($_GET['helu_id'])?abs($_GET['helu_id']):'';
		$type = isset($_GET['type'])?abs($_GET['type']):0;
		$source_url = isset($_GET['url'])?base64_decode($_GET['url']):'';
		if(!empty($helu_id)){
			$heluInfo = D('VpStudents')->get_heluInfo(array('helu_id'=>$helu_id));
			if(!empty($_GET['url'])){
				$source_url = base64_decode($_GET['url']);
			}else{
				$source_url = ($type==0)?$heluInfo['handouts_url']:$heluInfo['itembank_url'];
			}
			$previewFileType = strtolower(end(explode('.',$source_url)));
			if($previewFileType == 'jpg' || $previewFileType == 'jpeg' || $previewFileType == 'gif' || $previewFileType == 'png'){
				echo '<img src="'.APP_URL.str_replace("/Upload/","/upload/",$source_url).'">';
			}else{
				$title = ($type==0)?$heluInfo['handouts_title']:$heluInfo['itembank_title'];
				$swf_url = APP_DIR.str_replace(".".end(explode('.',$source_url)),".swf",$source_url);
				$is_exists = 1;
				if(!file_exists($swf_url)){
					$is_exists = 0;
				}
				$swf_url = strtolower(end(explode('/eap',$swf_url)));
				$this->assign(get_defined_vars());
				$this->display();
			}
		}else{
			echo '非法操作';
			exit;
		}
	}


	public function exportFileData(){
		$deptCode = isset($_REQUEST['deptCode'])?$_REQUEST['deptCode']:'';
		$teacherCode = isset($_REQUEST['teacherCode'])?$_REQUEST['teacherCode']:'';
		$studentCode = isset($_REQUEST['studentCode'])?$_REQUEST['studentCode']:'';
		$startTime = isset($_REQUEST['starttime'])?trim($_REQUEST['starttime']):'';
		$endTime = isset($_REQUEST['endtime'])?trim($_REQUEST['endtime']):'';
		$is_upload = isset($_REQUEST['is_upload'])?$_REQUEST['is_upload']:'';
		$teacherName = isset($_REQUEST['teacherName'])?$_REQUEST['teacherName']:'';
		$studentName = isset($_REQUEST['studentName'])?$_REQUEST['studentName']:'';
		$condition = '';
		if(!empty($deptCode)){
			$condition.= " AND view_helu.[sDeptCode] = '$deptCode' ";
		}
		if(!empty($teacherCode)){
			$condition.= " AND view_helu.[steacherCode] = '$teacherCode' ";
		}
		if(!empty($studentCode)){
			$condition.= " AND view_helu.[sStudentCode] = '$studentCode' ";
		}
		if(!empty($startTime)){
			$condition.= " AND view_helu.[dtLessonBeginReal] >= '$startTime' ";
		}
		if(!empty($endTime)){
			$condition.= " AND view_helu.[dtLessonBeginReal] <= '".date('Y-m-d',strtotime($endTime)+3600*24)."' ";
		}
		if(!empty($is_upload)){
			$condition.= ($is_upload==1)?" AND a.[url] IS NOT NULL ":" AND a.[url] IS NULL ";
		}
		if(!empty($teacherName)){
			$condition.= " AND view_helu.[sTeacherName] = '".urldecode($teacherName)."'";
		}
		if(!empty($studentName)){
			$condition.= " AND view_helu.[sStudentName] = '".urldecode($studentName)."'";
		}
		$contentModel = D('VpContent');
		$lessonHeluList = $contentModel->get_lessonHeLuAll($condition);

		import("ORG.Util.Excel");
		$exceler = new Excel_Export();
		$dotype_name = mb_convert_encoding('课节审查表','gbk','utf8');
		$exceler->setFileName($dotype_name.time().'.csv');
		$excel_title= array('上课时间','教师所属校区','教师','学员','课堂主题','课堂评价','讲义数量','讲义上传方式','讲义预览','测试卷数量','测试卷上传方式','测试卷预览','课节报告');
		foreach ($excel_title as $key=>$title){
			$excel_title[$key] = mb_convert_encoding($title,'gbk','utf8');
		}
		$exceler->setTitle($excel_title);
		$userModel = D('Users');
		if(!empty($lessonHeluList)){
			foreach ($lessonHeluList as $key=>$val){
				$tmp_data= array($val['dtdatereal'].' '.$val['dtlessonbeginreal'].'-'.$val['dtlessonendreal'],
				mb_convert_encoding($val['sareaname'],'gbk','utf8'),
				mb_convert_encoding($val['steachername'],'gbk','utf8'),
				mb_convert_encoding($val['sstudentname'],'gbk','utf8'),
				mb_convert_encoding($val['lesson_topic'],'gbk','utf8'),
				mb_convert_encoding($val['comment'],'gbk','utf8'),
				mb_convert_encoding($val['handouts_count'],'gbk','utf8'),
				mb_convert_encoding(($val['handouts_from_type'] == '1')?'微信':'PC','gbk','utf8'),
				mb_convert_encoding($val['handouts_url'],'gbk','utf8'),
				mb_convert_encoding($val['itembank_count'],'gbk','utf8'),
				mb_convert_encoding(($val['itembank_from_type'] == '1')?'微信':'PC','gbk','utf8'),
				mb_convert_encoding($val['itembank_url'],'gbk','utf8'),
				mb_convert_encoding($val['lesson_report_url'],'gbk','utf8')
				);
				$exceler->addRow($tmp_data);
			}
		}

		$exceler->export();

	}
	/*课评统计*/
	public function heluStatistics(){
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$student_name = isset($_REQUEST['student_name'])?$_REQUEST['student_name']:'';
		$lesson_date_start = isset($_REQUEST['lesson_date_start'])?$_REQUEST['lesson_date_start']:'';
		$lesson_date_end = isset($_REQUEST['lesson_date_end'])?$_REQUEST['lesson_date_end']:'';
		$is_select_sendsms = isset($_REQUEST['is_select_sendsms'])?$_REQUEST['is_select_sendsms']:0;
		$teacher_name = isset($_REQUEST['teacher_name'])?$_REQUEST['teacher_name']:'';

		$is_select_sendsms_array = array(0=>'全部','1'=>'否','2'=>'是');
		$conditionArr = array('student_name'=>$student_name,'is_select_sendsms'=>$is_select_sendsms,'lesson_date_start'=>$lesson_date_start,'lesson_date_end'=>$lesson_date_end,'teacher_name'=>$teacher_name);
		$contentModel = D('VpContent');
		$heluLogList = $contentModel->get_heluLogList($conditionArr,$curPage,$pagesize);
		$count = $contentModel->get_heluLogCount($conditionArr);
		$page = new page($count,$pagesize);
		$showPage = $page->show();
		$this->assign(get_defined_vars());
		$this->display('heluStatistics');
	}


	/*导出课评统计*/
	public function exportHeluLog(){
		$student_name = isset($_REQUEST['student_name'])?$_REQUEST['student_name']:'';
		$lesson_date_start = isset($_REQUEST['lesson_date_start'])?$_REQUEST['lesson_date_start']:'';
		$lesson_date_end = isset($_REQUEST['lesson_date_end'])?$_REQUEST['lesson_date_end']:'';
		$is_select_sendsms = isset($_REQUEST['is_select_sendsms'])?$_REQUEST['is_select_sendsms']:0;
		$teacher_name = isset($_REQUEST['teacher_name'])?$_REQUEST['teacher_name']:'';
		ini_set("max_execution_time", 600);
		ini_set("memory_limit", 1048576000);
		$contentModel = D('VpContent');
		$heluLogCount = $contentModel->get_heluLogAllCount(array('student_name'=>urldecode($student_name),'is_select_sendsms'=>$is_select_sendsms,'lesson_date_start'=>$lesson_date_start,'lesson_date_end'=>$lesson_date_end,'teacher_name'=>urldecode($teacher_name)));
		$page_size = 2000;
		$page = ceil($heluLogCount/$page_size);
		
		for($i=1;$i<=$page;$i++){
			$heluLogList[] = $contentModel->get_heluLogAll(array('student_name'=>urldecode($student_name),'is_select_sendsms'=>$is_select_sendsms,'lesson_date_start'=>$lesson_date_start,'lesson_date_end'=>$lesson_date_end,'teacher_name'=>urldecode($teacher_name),'cur_page'=>$i,'page_size'=>$page_size));
		}
		
		import("ORG.Util.Excel");
		$exceler = new Excel_Export();
		$dotype_name = mb_convert_encoding('课评统计表','gbk','utf8');
		$exceler->setFileName($dotype_name.time().'.csv');
		$excel_title= array('学员姓名','上课日期','教师姓名','核录时间','是否勾选给家长发短信','是否触发短信','是否上传讲义','家长手机号','上课主题','课堂评价','核录课时状态');
		foreach ($excel_title as $key=>$title){
			$excel_title[$key] = mb_convert_encoding($title,'gbk','utf8');
		}
		$exceler->setTitle($excel_title);
		$userModel = D('Users');
		if(!empty($heluLogList)){
			foreach ($heluLogList as $key=>$row){
				foreach ($row as $k=>$log){
					$tmp_data= array(mb_convert_encoding($log['student_name'],'gbk','utf8'),
					$log['lesson_date'],
					mb_convert_encoding($log['teacher_name'],'gbk','utf8'),
					$log['helu_time'],
					mb_convert_encoding(($log['is_select_sendsms']==1)?'是':'否','gbk','utf8'),
					mb_convert_encoding(($log['is_trigger_sendsms']==1)?'是':'否','gbk','utf8'),
					mb_convert_encoding(($log['is_upload_handouts']==1)?'是':'否','gbk','utf8'),
					mb_convert_encoding($log['to_mobile'],'gbk','utf8'),
					mb_convert_encoding($log['lesson_topic'],'gbk','utf8'),
					mb_convert_encoding($log['comment'],'gbk','utf8'),
					mb_convert_encoding(($log['helu_type']==1)?'核录':'修改','gbk','utf8'));
					$exceler->addRow($tmp_data);
				}
			}
		}

		$exceler->export();
	}



	public function view_file(){
		$url = str_replace('|','/',$_GET['url']);
		if(!empty($url)){
			$type = end(explode('.',$url));
			if(in_array($type,array('jpg','jpeg','gif','png'))){
				$file_type = 'img';
				$file_url = '/upload/'.end(explode('/Upload/',$url));
			}else{
				$file_type = 'file';
				$file_url = strtolower(end(explode('/eap',$url)));
			}

		}else{
			echo '预览文件为空';
			exit;
		}
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function downloadFile(){
		$name = urldecode($_GET['name']);
		$url = str_replace('|','/',str_replace('=','.',$_GET['url']));
		if(!empty($url)){
			$fileType = $_GET['file_type'];
			$targetFile = $name.'.'.$fileType;
			$this->download_file($url,$targetFile);
		}else{
			echo '下载文件为空';
			exit;
		}

	}


	/*辅导方案审查*/
	public function programReview(){
		$contentModel = D('VpContent');
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$deptCode = isset($_REQUEST['deptCode'])?$_REQUEST['deptCode']:'';
		$teacherCode = isset($_REQUEST['teacherCode'])?$_REQUEST['teacherCode']:'';
		$studentCode = isset($_REQUEST['studentCode'])?$_REQUEST['studentCode']:'';
		$startTime = isset($_REQUEST['starttime'])?trim($_REQUEST['starttime']):'';
		$endTime = isset($_REQUEST['endtime'])?trim($_REQUEST['endtime']):'';
		$teacherName = isset($_REQUEST['teacherName'])?$_REQUEST['teacherName']:'';
		$condition = " 1=1 ";
		if(!empty($deptCode)){
			$condition.= " AND [dept_code] = '$deptCode' ";
		}
		if(!empty($teacherCode)){
			$condition.= " AND [teacher_code] = '$teacherCode' ";
		}
		if(!empty($studentCode)){
			$condition.= " AND [student_code] = '$studentCode'";
		}
		if(!empty($startTime)){
			$condition.= " AND [instime] >= '$startTime'";
		}
		if(!empty($endTime)){
			$condition.= " AND [instime] <= '".date('Y-m-d',strtotime($endTime)+3600*24)."' ";
		}
		if(!empty($teacherName)){
			$condition.= " AND [teacher_name] = '".urldecode($teacherName)."' ";
		}
		$programList = $contentModel->get_programReviewList($condition,$curPage,$pagesize);
		$count = $contentModel->get_programReviewCount($condition);
		$page = new page($count,$pagesize);
		$showPage = $page->show();

		$deptList = $contentModel->get_deptList();
		$teacherList = $contentModel->get_vipTeacherList(array('deptCode'=>$deptCode));
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function exportProgramData(){
		$deptCode = isset($_REQUEST['deptCode'])?$_REQUEST['deptCode']:'';
		$teacherCode = isset($_REQUEST['teacherCode'])?$_REQUEST['teacherCode']:'';
		$studentCode = isset($_REQUEST['studentCode'])?$_REQUEST['studentCode']:'';
		$startTime = isset($_REQUEST['starttime'])?trim($_REQUEST['starttime']):'';
		$endTime = isset($_REQUEST['endtime'])?trim($_REQUEST['endtime']):'';
		$teacherName = isset($_REQUEST['teacherName'])?$_REQUEST['teacherName']:'';
		$condition = " 1=1 ";
		if(!empty($deptCode)){
			$condition.= " AND [dept_code] = '$deptCode' ";
		}
		if(!empty($teacherCode)){
			$condition.= " AND [teacher_code] = '$teacherCode' ";
		}
		if(!empty($studentCode)){
			$condition.= " AND [student_code] = '$studentCode'";
		}
		if(!empty($startTime)){
			$condition.= " AND [instime] >= '$startTime'";
		}
		if(!empty($endTime)){
			$condition.= " AND [instime] <= '".date('Y-m-d',strtotime($endTime)+3600*24)."' ";
		}
		if(!empty($teacherName)){
			$condition.= " AND [teacher_name] = '".urldecode($teacherName)."' ";
		}
		$contentModel = D('VpContent');
		$lessonHeluList = $contentModel->get_programReviewAll($condition);

		import("ORG.Util.Excel");
		$exceler = new Excel_Export();
		$dotype_name = mb_convert_encoding('辅导方案审查表','gbk','utf8');
		$exceler->setFileName($dotype_name.time().'.csv');
		$excel_title= array('上传时间','教师所属校区','教师','学员','课程编码','课程名称','上传方式','辅导方案','文件数量');
		foreach ($excel_title as $key=>$title){
			$excel_title[$key] = mb_convert_encoding($title,'gbk','utf8');
		}
		$exceler->setTitle($excel_title);
		$userModel = D('Users');
		if(!empty($lessonHeluList)){
			foreach ($lessonHeluList as $key=>$val){
				$tmp_data= array($val['instime2'],
				mb_convert_encoding($val['dept_name'],'gbk','utf8'),
				mb_convert_encoding($val['teacher_name'],'gbk','utf8'),
				mb_convert_encoding($val['student_name'],'gbk','utf8'),
				mb_convert_encoding($val['kecheng_code'],'gbk','utf8'),
				mb_convert_encoding($val['kecheng_name'],'gbk','utf8'),
				mb_convert_encoding(($val['from_type'] == '1')?'微信':'PC','gbk','utf8'),
				mb_convert_encoding($val['program_url'],'gbk','utf8'),
				mb_convert_encoding(count($val['program_arr']),'gbk','utf8')
				);
				$exceler->addRow($tmp_data);
			}
		}

		$exceler->export();
	}



	public function downloadReport(){
		$heluId = $_GET['id'];
		if(!empty($heluId)){

		}
	}
}

?>