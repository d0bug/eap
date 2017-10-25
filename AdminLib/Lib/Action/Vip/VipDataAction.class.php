<?php
/*数据统计*/
class VipDataAction extends VipCommAction{
	
	/*上传统计*/
	public function uploadStatistic(){
		$is_jiaoyan = $this->checkUserRole();
		if($is_jiaoyan!=1){
			echo '<font size=3>抱歉，您无权限进行此操作</font>';exit;
		}
		$handoutsType = C('HANDOUTS_TYPE');
		$type = isset($_REQUEST['type'])?$_REQUEST['type']:'';
		$is_teaching_and_research = !empty($_REQUEST['is_teaching_and_research'])?$_REQUEST['is_teaching_and_research']:'';
		$startTime = isset($_REQUEST['starttime'])?trim($_REQUEST['starttime']):'';
		$endTime = isset($_REQUEST['endtime'])?trim($_REQUEST['endtime']):'';
		$username = isset($_REQUEST['username'])?$_REQUEST['username']:'';
		$handouts_subject = isset($_REQUEST['subject'])?intval($_REQUEST['subject']):'';
		$handouts_grade = isset($_REQUEST['grade'])?intval($_REQUEST['grade']):'';
		$vipGradeModel = D('VpGrade');
		if(!empty($handouts_grade)){
			$handouts_grade_name = $vipGradeModel->get_gradename_by_gid($handouts_grade);
		}
		$userKey = $this->loginUser->getUserKey();
		$vipSubjectModel = D('VpSubject');
		$subjectArr = $vipSubjectModel->get_subjectList('',$userKey);
		if(!empty($handouts_subject)){
			$gradeArr = $vipGradeModel->get_gradeList_by_subjectid($handouts_subject);
		}else{
			$gradeArr = $vipGradeModel->get_gradeList('',$userKey);
		}

		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$vipHandoutsModel = D('VpHandouts');
		$condition = ' AND is_delete = 0 ';
		if(!empty($handouts_subject)){
			$condition .= " AND sid = '$handouts_subject' ";
		}
		if(!empty($handouts_grade)){
			$condition .= " AND gid = '$handouts_grade' ";
		}
		if(!empty($type)){
			if($type == 1 || $type == 2){
				$condition .= " AND [type] = '".($type-1)."' AND [is_teaching_and_research] = '1'";
			}else{
				$condition .= " AND [is_share] = '1' AND [is_teaching_and_research] = '0'";
			}
		}
		if(!empty($is_teaching_and_research)){
			$condition .= " AND [is_teaching_and_research] = '".($is_teaching_and_research-1)."'";
		}
		if(!empty($startTime)){
			$condition .= " AND [instime] >= '".strtotime($startTime.' 00:00:00')."'";
		}
		if(!empty($endTime)){
			$condition .= " AND [instime] <= '".strtotime($endTime.' 23:59:59')."'";
		}
		if(!empty($username)){
			$dao = $vipHandoutsModel->dao;
			$condition .= " AND [user_key] LIKE ".$dao->quote('%-' . SysUtil::safeSearch($username) . '');
		}
		$uploadList = $vipHandoutsModel->get_uploadOrDownloadList($condition,$curPage,$pagesize,'upload',1);
		$userModel = D('Users');
		$page_total_num = 0;
		foreach ($uploadList as $key=>$val){
			$uploadList[$key]['user_realname'] = $userModel->get_userRealName_by_userKey($val['user_key']);
			$uploadList[$key]['user_name'] =end(explode('-',$val['user_key']));
			$page_total_num += $val['uploadnum'];
		}
		$count = $vipHandoutsModel->get_uploadOrDownloadCount($condition,'upload');
		$page = new Page($count,$pagesize);
		$showPage = $page->show();

		$this->assign(get_defined_vars());
		$this->display("uploadStatistic");
	}


	/*下载统计*/
	public function downloadStatistic(){
		$is_jiaoyan = $this->checkUserRole();
		if($is_jiaoyan!=1){
			echo '<font size=3>抱歉，您无权限进行此操作</font>';exit;
		}
		$handoutsType = C('HANDOUTS_TYPE');
		
		$type = isset($_REQUEST['type'])?$_REQUEST['type']:'';
		$startTime = isset($_REQUEST['starttime'])?trim($_REQUEST['starttime']):'';
		$endTime = isset($_REQUEST['endtime'])?trim($_REQUEST['endtime']):'';
		$username = isset($_REQUEST['username'])?$_REQUEST['username']:'';
		$handouts_subject = isset($_REQUEST['subject'])?intval($_REQUEST['subject']):'';
		$handouts_grade = isset($_REQUEST['grade'])?intval($_REQUEST['grade']):'';
		$vipGradeModel = D('VpGrade');
		if(!empty($handouts_grade)){
			$handouts_grade_name = $vipGradeModel->get_gradename_by_gid($handouts_grade);
		}
		$userKey = $this->loginUser->getUserKey();
		$vipSubjectModel = D('VpSubject');
		$subjectArr = $vipSubjectModel->get_subjectList('',$userKey);
		if(!empty($handouts_subject)){
			$gradeArr = $vipGradeModel->get_gradeList_by_subjectid($handouts_subject);
		}else{
			$gradeArr = $vipGradeModel->get_gradeList('',$userKey);
		}
		
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$vipHandoutsModel = D('VpHandouts');
		$condition = ' AND h.is_delete = 0 ';
		if(!empty($handouts_subject)){
			$condition .= " AND h.sid = '$handouts_subject' ";
		}
		if(!empty($handouts_grade)){
			$condition .= " AND h.gid = '$handouts_grade' ";
		}
		if(!empty($type)){
			if($type == 1 || $type == 2){
				$condition .= " AND d.[htype] = '".($type-1)."' AND [is_teaching_and_research] = '1'";
			}else{
				$condition .= " AND h.[is_share] = '1' AND [is_teaching_and_research] = '0'";
			}
		}
		if(!empty($startTime)){
			$condition .= " AND d.[download_time] >= '".$startTime.' 00:00:00'."'";
		}
		if(!empty($endTime)){
			$condition .= " AND d.[download_time] <= '".$endTime.' 23:59:59'."'";
		}
		if(!empty($username)){
			$dao = $vipHandoutsModel->dao;
			$condition .= " AND d.[user_key] LIKE ".$dao->quote('%-' . SysUtil::safeSearch($username) . '');
		}
		$downloadList = $vipHandoutsModel->get_uploadOrDownloadList($condition,$curPage,$pagesize,'download',1);
		$userModel = D('Users');
		$page_total_num = 0;
		foreach ($downloadList as $key=>$val){
			$downloadList[$key]['user_realname'] = $userModel->get_userRealName_by_userKey($val['user_key']);
			$downloadList[$key]['user_name'] =end(explode('-',$val['user_key']));
			$page_total_num += $val['downloadnum'];
		}
		$count = $vipHandoutsModel->get_uploadOrDownloadCount($condition,'download');
		$page = new Page($count,$pagesize);
		$showPage = $page->show();

		$this->assign(get_defined_vars());
		$this->display("downloadStatistic");
	}
	
	/*获取某位老师上传讲义列表*/
	protected function get_uploadOrDownloadList(){
		$userKey = isset($_GET['userKey'])?trim($_GET['userKey']):'';
		$type = isset($_GET['type'])?$_GET['type']:'';
		$startTime = isset($_GET['startTime'])?trim($_GET['startTime']):'';
		$endTime = isset($_GET['endTime'])?trim($_GET['endTime']):'';
		$getType = isset($_GET['getType'])?trim($_GET['getType']):'upload';
		$is_teaching_and_research = isset($_GET['is_teaching_and_research'])?$_GET['is_teaching_and_research']:'';
		$sid = isset($_GET['sid'])?trim($_GET['sid']):'';
		$gid = isset($_GET['gid'])?trim($_GET['gid']):'';
		if(!empty($userKey)){
			$condition = ' AND is_delete = 0 ';
			$condition .= ($getType=='upload')?" AND user_key = '$userKey'":" AND d.user_key = '$userKey'";
			if(!empty($type)){
				if($type == 1 || $type == 2){
					$condition .= ($getType=='upload')?" AND type = '".($type-1)."' AND [is_teaching_and_research] = '1'":" AND d.htype = '".($type-1)."' AND [is_teaching_and_research] = '1'";
				}else{
					$condition .= " AND is_share = '1' AND [is_teaching_and_research] = '0'";
				}
			}
			if(!empty($startTime)){
				$condition .= ($getType=='upload')?" AND instime >= '".strtotime($startTime.' 00:00:00')."'":" AND d.download_time >= '".$startTime.' 00:00:00'."'";
			}
			if(!empty($endTime)){
				$condition .= ($getType=='upload')?" AND instime <= '".strtotime($endTime.' 23:59:59')."'":"AND d.download_time <= '".$endTime.' 23:59:59'."'";
			}
			if(!empty($is_teaching_and_research) && $getType=='upload'){
				$condition .= " AND is_teaching_and_research = '".($is_teaching_and_research-1)."'";
			}
			if(!empty($sid)){
				$condition .= " AND sid = '".$sid."'";
			}
			if(!empty($gid)){
				$condition .= " AND gid = '".$gid."'";
			}
			$teacherUploadOrDownloadList = $this->get_teacherUploadOrDownloadList($condition,$getType);
			if(!empty($teacherUploadOrDownloadList)){
				$listHtml = "<a href=\"".U('Vip/VipData/export_teacher_uploadOrDownloadList',array('userKey'=>$userKey,'type'=>$type,'startTime'=>$startTime,'endTime'=>$endTime,'getType'=>$getType,'sid'=>$sid,'gid'=>$gid))."\" class=blue>导出Excle表</a><table width='95%' border=1><tr bgcolor='#dddddd' height=25><td>标题</td><td>所属科目</td>";
				$listHtml .= ($type == 2)?"<td>所属题库属性</td><td>所属试题属性</td><td>":"<td>所属课程属性</td><td>所属讲义属性</td><td>";
				if($getType =='upload'){
					$listHtml .= '上传时间</td><td>下载次数</td>';
				}else{
					$listHtml .= '下载时间</td>';
				}
				$listHtml .= "<td>IP</td></tr>";
				foreach ($teacherUploadOrDownloadList as $key=>$upload){
					$listHtml .= "<tr height=25><td>$upload[title]";
					$listHtml .= "</td><td>$upload[sname]</td><td>$upload[gname]</td><td>$upload[kname]</td><td>";
					if($getType =='upload'){
						$listHtml .= date('Y-m-d H:i:s',$upload['instime']).'</td><td>'.$upload['downloadNum'].'</td>';
					}else{
						$listHtml .= $upload['download_time'].'</td>';
					}
					$listHtml .= "<td>".$upload['ip']."</td></tr>";
				}
				$listHtml .= "</table>";
			}else{
				$listHtml = "暂无相关信息";
			}
			echo $listHtml;
		}else{
			echo '非法操作，教师信息丢失';
		}
	}


	protected  function get_teacherUploadOrDownloadList($condition,$getType){
		$vipHandoutsModel = D('VpHandouts');
		$vipSubjectModel = D('VpSubject');
		$vipGradeModel = D('VpGrade');
		$vipKnowledgeModel = D('VpKnowledge');
		$teacherUploadOrDownloadList = $vipHandoutsModel->get_teacherUploadOrDownloadListAll($condition,$getType);
		foreach ($teacherUploadOrDownloadList as $key=>$row){
			$teacherUploadOrDownloadList[$key]['sname'] = $vipSubjectModel->get_subjectname_by_sid($row['sid']);
			$teacherUploadOrDownloadList[$key]['gname'] = $vipGradeModel->get_gradename_by_gid($row['gid']);
			$teacherUploadOrDownloadList[$key]['kname'] = $vipKnowledgeModel->get_knowledgename_by_kid($row['kid']);
			if($getType =='upload'){
				$teacherUploadOrDownloadList[$key]['downloadNum'] = $vipHandoutsModel->get_downloadNum(array('hid'=>$row['hid'],'distinct'=>0));
			}
		}
		return $teacherUploadOrDownloadList;
	}


	/*导出上传下载统计数据*/
	public function export_uploadOrDownloadList(){
		$vipHandoutsModel = D('VpHandouts');
		$condition = ' AND is_delete = 0 ';
		$dotype = isset($_GET['dotype'])?$_GET['dotype']:'';
		$type = isset($_GET['type'])?$_GET['type']:'';
		$is_teaching_and_research = !empty($_GET['is_teaching_and_research'])?$_GET['is_teaching_and_research']:'';
		$startTime = isset($_GET['starttime'])?trim($_GET['starttime']):'';
		$endTime = isset($_GET['endtime'])?trim($_GET['endtime']):'';
		$username = isset($_GET['username'])?$_GET['username']:'';
		$sid = isset($_GET['sid'])?$_GET['sid']:'';
		$gid = isset($_GET['gid'])?$_GET['gid']:'';
		if(!empty($type)){
			if($type == 1 || $type == 2){
				$condition .= " AND [type] = '".($type-1)."' AND [is_teaching_and_research] = '1'";
			}else{
				$condition .= " AND [is_share] = '1' AND [is_teaching_and_research] = '0'";
			}
		}
		if(!empty($is_teaching_and_research)){
			$condition .= " AND [is_teaching_and_research] = '".($is_teaching_and_research-1)."'";
		}
		if(!empty($startTime)){
			$condition .= " AND [instime] >= '".strtotime($startTime.' 00:00:00')."'";
		}
		if(!empty($endTime)){
			$condition .= " AND [instime] <= '".strtotime($endTime.' 23:59:59')."'";
		}
		if(!empty($username)){
			$dao = $vipHandoutsModel->dao;
			$condition .= " AND [user_key] LIKE ".$dao->quote('%-' . SysUtil::safeSearch($username) . '');
		}
		if(!empty($sid)){
			$condition .= " AND [sid] = '".$sid."'";
		}
		if(!empty($gid)){
			$condition .= " AND [gid] = '".$gid."'";
		}
		$uploadList = $vipHandoutsModel->get_uploadOrDownloadList($condition,$curPage,$pagesize,$dotype,0);

		import("ORG.Util.Excel");
		$exceler = new Excel_Export();
		$dotype_name = mb_convert_encoding(($dotype == 'upload')?'上传统计':'下载统计','gbk','utf8');
		$exceler->setFileName($dotype_name.time().'.csv');
		$excel_title= ($dotype == 'upload')?array('教师姓名','登录名', '上传数量'):array('教师姓名','登录名', '下载数量');
		foreach ($excel_title as $key=>$title){
			$excel_title[$key] = mb_convert_encoding($title,'gbk','utf8');
		}
		$exceler->setTitle($excel_title);
		$userModel = D('Users');
		foreach ($uploadList as $key=>$val){
			$temp_user_realname = $userModel->get_userRealName_by_userKey($val['user_key']);
			$temp_user_name =end(explode('-',$val['user_key']));
			$num = ($dotype == 'upload')?$val['uploadnum']:$val['downloadnum'];
			$tmp_data= array(mb_convert_encoding($temp_user_realname,'gbk','utf8'),$temp_user_name,$num);
			$exceler->addRow($tmp_data);
		}
		$exceler->export();
	}


	public function export_teacher_uploadOrDownloadList(){
		$userKey = isset($_GET['userKey'])?$_GET['userKey']:'';
		$type = isset($_GET['type'])?$_GET['type']:'';
		$startTime = isset($_GET['startTime'])?trim($_GET['startTime']):'';
		$endTime = isset($_GET['endTime'])?trim($_GET['endTime']):'';
		$getType = isset($_GET['getType'])?trim($_GET['getType']):'upload';
		$is_teaching_and_research = isset($_GET['is_teaching_and_research'])?$_GET['is_teaching_and_research']:'';
		$sid = isset($_GET['sid'])?$_GET['sid']:'';
		$gid = isset($_GET['gid'])?$_GET['gid']:'';
		if(!empty($userKey)){
			$condition = ' AND is_delete = 0 ';
			$condition .= ($getType=='upload')?" AND user_key = '$userKey'":" AND d.user_key = '$userKey'";
			if(!empty($type)){
				if($type == 1 || $type == 2){
					$condition .= ($getType=='upload')?" AND type = '".($type-1)."'":" AND d.htype = '".($type-1)."'";
				}else{
					$condition .= " AND is_share = '1'";
				}
			}
			if(!empty($startTime)){
				$condition .= ($getType=='upload')?" AND instime >= '".strtotime($startTime.' 00:00:00')."'":" AND d.download_time >= '".$startTime.' 00:00:00'."'";
			}
			if(!empty($endTime)){
				$condition .= ($getType=='upload')?" AND instime <= '".strtotime($endTime.' 23:59:59')."'":"AND d.download_time <= '".$endTime.' 23:59:59'."'";
			}
			if(!empty($is_teaching_and_research) && $getType=='upload'){
				$condition .= " AND is_teaching_and_research = '".($is_teaching_and_research-1)."'";
			}
			if(!empty($sid)){
				$condition .= " AND [sid] = '".$sid."'";
			}
			if(!empty($gid)){
				$condition .= " AND [gid] = '".$gid."'";
			}
			$teacherUploadOrDownloadList = $this->get_teacherUploadOrDownloadList($condition,$getType);
			import("ORG.Util.Excel");
			$exceler = new Excel_Export();
			$dotype_name = ($getType == 'upload')?'上传统计':'下载统计';
			$user_realname = D('Users')->get_userRealName_by_userKey($userKey);
			$exceler->setFileName($user_realname.'的'.$dotype_name.'列表'.'.csv');
			$excel_title= ($getType == 'upload')?array('讲义标题','所属科目','所属课程属性','所属讲义属性','上传时间','下载次数','IP'):array('讲义标题','所属科目','所属课程属性','所属讲义属性','下载时间','IP');
			foreach ($excel_title as $key=>$title){
				$excel_title[$key] = mb_convert_encoding($title,'gbk','utf8');
			}
			$exceler->setTitle($excel_title);
			foreach ($teacherUploadOrDownloadList as $key=>$upload){
				$listHtml .= "</td><td>$upload[sname]</td><td>$upload[gname]</td><td>$upload[kname]</td><td>";
				if($getType =='upload'){
					$listHtml .= date('Y-m-d H:i:s',$upload['instime']).'</td><td>'.$upload['downloadNum'].'</td>';
				}else{
					$listHtml .= $upload['download_time'].'</td>';
				}
				$listHtml .= "<td>".$upload['ip']."</td></tr>";
				$upload['title'] = mb_convert_encoding($upload['title'],'gbk','utf8');
				$upload['sname'] = mb_convert_encoding($upload['sname'],'gbk','utf8');
				$upload['gname'] = mb_convert_encoding($upload['gname'],'gbk','utf8');
				$upload['kname'] = mb_convert_encoding($upload['kname'],'gbk','utf8');
				$tmp_data= ($getType =='upload')?
				array($upload['title'],
				$upload['sname'],
				$upload['gname'],
				$upload['kname'],
				date('Y-m-d H:i:s',$upload['instime']),
				$upload['downloadNum'],
				$upload['ip']):
				array($upload['title'],
				$upload['sname'],
				$upload['gname'],
				$upload['kname'],
				$upload['download_time'],
				$upload['ip']);
				$exceler->addRow($tmp_data);
			}
			$exceler->export();
		}else{
			$this->error('非法操作');
		}
	}
	/*教研上传统计 本周更新*/
	public function jiaoyanUploadStatistic(){
		$is_jiaoyan = $this->checkUserRole();
		if($is_jiaoyan!=1){
			echo '<font size=3>抱歉，您无权限进行此操作</font>';exit;
		}
		$userKey = $this->loginUser->getUserKey();
		$handoutsType = C('HANDOUTS_TYPE');
		$type = isset($_REQUEST['type'])?$_REQUEST['type']:'';
		$handouts_subject = isset($_REQUEST['subject'])?intval($_REQUEST['subject']):'';
		$handouts_grade = isset($_REQUEST['grade'])?intval($_REQUEST['grade']):'';
		$handouts_knowledge = isset($_REQUEST['knowledge'])?intval($_REQUEST['knowledge']):'';
		$handouts_searchPerion = isset($_REQUEST['searchPerion'])?$_REQUEST['searchPerion']:date("Y-m-d",time());
		$startTime = strtotime(date("Y-m-d",strtotime($handouts_searchPerion)-(date('w',strtotime($handouts_searchPerion))-1)*3600*24));
		$endTime = $startTime+3600*24*7-1;
	
		
		$vipGradeModel = D('VpGrade');
		if(!empty($handouts_grade)){
			$handouts_grade_name = $vipGradeModel->get_gradename_by_gid($handouts_grade);
		}
		$userKey = $this->loginUser->getUserKey();
		$vipSubjectModel = D('VpSubject');
		$subjectArr = $vipSubjectModel->get_subjectList('',$userKey);
		if(!empty($handouts_subject)){
			$gradeArr = $vipGradeModel->get_gradeList_by_subjectid($handouts_subject);
		}else{
			$gradeArr = $vipGradeModel->get_gradeList('',$userKey);
		}
		$vipKnowledgeModel = D("VpKnowledge");
		if(!empty($handouts_knowledge)){
			$knowledgeName = $vipKnowledgeModel->get_knowledgename_by_kid($handouts_knowledge);
		}
		
		$vipSubjectModel = D('VpSubject');
		$subjectArr = $vipSubjectModel->get_subjectList('',$userKey);
		$vipGradeModel = D('VpGrade');
		if(!empty($handouts_subject)){
			$gradeArr = $vipGradeModel->get_gradeList_by_subjectid($handouts_subject);
		}else{
			$gradeArr = $vipGradeModel->get_gradeList('',$userKey);
		}
		$vipHandoutsModel = D('VpHandouts');
		$uploadList = $vipHandoutsModel->get_alluploadOrDownloadList($handouts_subject,$handouts_grade,$handouts_knowledge,$type,$startTime,$endTime);
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
		$this->display("jiaoyanUploadStatistic");
	}
	/*导出本周的数据*/
	public function export_benzhouData(){
		$dirPath = explode('AdminLib',dirname(__FILE__));
		include_once($dirPath[0]."Static/PHPExcel-1.7.7/Classes/PHPExcel.php");
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("gaosi")
									 ->setLastModifiedBy("zhao")
									 ->setTitle("Office 2007 XLSX Test Document")
									 ->setSubject("Office 2007 XLSX Test Document")
									 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test result file");
									 
		$is_jiaoyan = $this->checkUserRole();
		if($is_jiaoyan!=1){
			echo '<font size=3>抱歉，您无权限进行此操作</font>';exit;
		}
		$userKey = $this->loginUser->getUserKey();
		$handoutsType = C('HANDOUTS_TYPE');
		$type = isset($_REQUEST['type'])?$_REQUEST['type']:'';
		$handouts_subject = isset($_REQUEST['subject'])?intval($_REQUEST['subject']):'';
		$handouts_grade = isset($_REQUEST['grade'])?intval($_REQUEST['grade']):'';
		$handouts_knowledge = isset($_REQUEST['knowledge'])?intval($_REQUEST['knowledge']):'';
		$handouts_searchPerion = isset($_REQUEST['searchPerion'])?$_REQUEST['searchPerion']:date("Y-m-d",time());
		$startTime = strtotime(date("Y-m-d",strtotime($handouts_searchPerion)-(date('w',strtotime($handouts_searchPerion))-1)*3600*24));
		$endTime = $startTime+3600*24*7-1;
		$vipHandoutsModel = D('VpHandouts');
		$vipKnowledgeModel = D("VpKnowledge");
		$uploadList = $vipHandoutsModel->get_alluploadOrDownloadList($handouts_subject,$handouts_grade,$handouts_knowledge,$type,$startTime,$endTime);
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
				$uploadList[$key]['sname'] = $value['sname'];
				if($value['type'] == 0){
					$jiangyiArray[$key]['sname'] = $value['sname'];
					$jiangyiArray[$key]['gname'] = $value['gname'];
					$jiangyiArray[$key]['kname'] = $value['kname'];
					$jiangyiArray[$key]['courseuser'] = $courseuserAllArray[$value['sid'].$value['gid'].$value['kid']];
					$jiangyiArray[$key]['title'] = $value['title'];
				}else if($value['type'] == 1){
					$shitiArray[$key]['sname'] = $value['sname'];
					$shitiArray[$key]['gname'] = $value['gname'];
					$shitiArray[$key]['kname'] = $value['kname'];
					$shitiArray[$key]['title'] = $value['title'];
				}
			}
		}
		$jiangyiTitile = array(
			'sname'=>'学科',
			'gname'=>'课程属性',
			'kname'=>'讲义属性',
			'courseuser'=>'课程用途',
			'title'=>'讲义名称'
		);
		$shitiTitile = array(
			'sname'=>'学科',
			'gname'=>'题库属性',
			'kname'=>'试题属性',
			'title'=>'试题名称'
		);
		array_unshift($jiangyiArray,array_values($jiangyiTitile));
		$objPHPExcel->getActiveSheet(0)->setTitle('课程讲义','试题库');
		for($i=0;$i<count($jiangyiArray);$i++){
			$j=0;
			foreach($jiangyiArray[$i] as $k=>$v){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($j+65).($i+1),$v);
				$j++;
			}
		}
		$objPHPExcel->setActiveSheetIndex(0);    
		$objActSheet = $objPHPExcel->getActiveSheet(); 
		$objActSheet->getColumnDimension('A')->setWidth(13);
		$objActSheet->getColumnDimension('B')->setWidth(20);
		$objActSheet->getColumnDimension('C')->setWidth(20);
		$objActSheet->getColumnDimension('D')->setWidth(25);
		$objActSheet->getColumnDimension('E')->setWidth(40);
		
		$msgWorkSheet = new PHPExcel_Worksheet($objPHPExcel, '试题库'); //创建一个工作表
        $objPHPExcel->addSheet($msgWorkSheet); //插入工作表
		array_unshift($shitiArray,array_values($shitiTitile));
		for($i=0;$i<count($shitiArray);$i++){
			$j=0;
			foreach($shitiArray[$i] as $k=>$v){
				$objPHPExcel->setActiveSheetIndex(1)->setCellValue(chr($j+65).($i+1),$v);
				$j++;
			}
		}
		$objPHPExcel->setActiveSheetIndex(1);    
		$objActSheet = $objPHPExcel->getActiveSheet(); 
		$objActSheet->getColumnDimension('A')->setWidth(13);
		$objActSheet->getColumnDimension('B')->setWidth(20);
		$objActSheet->getColumnDimension('C')->setWidth(20);
		$objActSheet->getColumnDimension('D')->setWidth(40); 
		
		ob_end_clean();
		header ('Pragma: public'); // HTTP/1.0
		header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
		header("Content-Disposition: attachment;filename=本周更新数据统计$handouts_searchPerion.xls");
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
	
	/*教研上传统计 历史统计*/
	public function jiaoyanHistoryUploadStatistic(){
		$is_jiaoyan = $this->checkUserRole();
		if($is_jiaoyan!=1){
			echo '<font size=3>抱歉，您无权限进行此操作</font>';exit;
		}
		$userKey = $this->loginUser->getUserKey();
		$handoutsType = C('HANDOUTS_TYPE');
		$type = isset($_REQUEST['type'])?$_REQUEST['type']:'';
		$handouts_subject = isset($_REQUEST['subject'])?intval($_REQUEST['subject']):'';
		$handouts_grade = isset($_REQUEST['grade'])?intval($_REQUEST['grade']):'';
		$handouts_knowledge = isset($_REQUEST['knowledge'])?intval($_REQUEST['knowledge']):'';
		$handouts_searchPerion = isset($_REQUEST['searchPerion'])?$_REQUEST['searchPerion']:date("Y-m-d",time());
		$startTime = isset($_REQUEST['startTime'])?$_REQUEST['startTime']:'';
		$endTime = isset($_REQUEST['endTime'])?$_REQUEST['endTime']:'';
		
		
		$vipGradeModel = D('VpGrade');
		if(!empty($handouts_grade)){
			$handouts_grade_name = $vipGradeModel->get_gradename_by_gid($handouts_grade);
		}
		$userKey = $this->loginUser->getUserKey();
		$vipSubjectModel = D('VpSubject');
		$subjectArr = $vipSubjectModel->get_subjectList('',$userKey);
		
		$vipSubjectModel = D('VpSubject');
		$subjectArr = $vipSubjectModel->get_subjectList('',$userKey);
		$subjectAllArr = array();
		foreach($subjectArr as $key=>$value){
			$subjectAllArr[$value['sid']] = $value['name'];
		}
		$vipGradeModel = D('VpGrade');
		if(!empty($handouts_subject)){
			$gradeArr = $vipGradeModel->get_gradeList_by_subjectid($handouts_subject);
		}else{
			$gradeArr = $vipGradeModel->get_gradeList('',$userKey);
		}
		$gradeAllArr = array();
		foreach($gradeArr as $key=>$value){
			$gradeAllArr[$value['gid']] = $value['name'];
		}
		
		$vipHandoutsModel = D('VpHandouts');
		$uploadList = $vipHandoutsModel->get_historyUploadCount($handouts_subject,$handouts_grade,'',$type,strtotime($startTime),strtotime($endTime));
		$jiangyiHistory = array();
		$shitiArray = array();
	
		foreach($uploadList as $key=>$value){
			$uploadList[$key]['sname']	= $subjectAllArr[$value['sid']];
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
		$this->display("jiaoyanHistoryUploadStatistic");
	}
	
	public function export_historyData(){
		$dirPath = explode('AdminLib',dirname(__FILE__));
		include_once($dirPath[0]."Static/PHPExcel-1.7.7/Classes/PHPExcel.php");
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("gaosi")
									 ->setLastModifiedBy("zhao")
									 ->setTitle("Office 2007 XLSX Test Document")
									 ->setSubject("Office 2007 XLSX Test Document")
									 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test result file");
									 
		$is_jiaoyan = $this->checkUserRole();
		if($is_jiaoyan!=1){
			echo '<font size=3>抱歉，您无权限进行此操作</font>';exit;
		}
		$userKey = $this->loginUser->getUserKey();
		$handoutsType = C('HANDOUTS_TYPE');
		$type = isset($_REQUEST['type'])?$_REQUEST['type']:'';
		$handouts_subject = isset($_REQUEST['subject'])?intval($_REQUEST['subject']):'';
		$handouts_grade = isset($_REQUEST['grade'])?intval($_REQUEST['grade']):'';
		$handouts_knowledge = isset($_REQUEST['knowledge'])?intval($_REQUEST['knowledge']):'';
		$handouts_searchPerion = isset($_REQUEST['searchPerion'])?$_REQUEST['searchPerion']:date("Y-m-d",time());
		$startTime = isset($_REQUEST['startTime'])?$_REQUEST['startTime']:'';
		$endTime = isset($_REQUEST['endTime'])?$_REQUEST['endTime']:'';
		$vipGradeModel = D('VpGrade');
		if(!empty($handouts_grade)){
			$handouts_grade_name = $vipGradeModel->get_gradename_by_gid($handouts_grade);
		}
		$userKey = $this->loginUser->getUserKey();
		$vipSubjectModel = D('VpSubject');
		$subjectArr = $vipSubjectModel->get_subjectList('',$userKey);
		
		$vipSubjectModel = D('VpSubject');
		$subjectArr = $vipSubjectModel->get_subjectList('',$userKey);
		$subjectAllArr = array();
		foreach($subjectArr as $key=>$value){
			$subjectAllArr[$value['sid']] = $value['name'];
		}
		$vipGradeModel = D('VpGrade');
		if(!empty($handouts_subject)){
			$gradeArr = $vipGradeModel->get_gradeList_by_subjectid($handouts_subject);
		}else{
			$gradeArr = $vipGradeModel->get_gradeList('',$userKey);
		}
		$gradeAllArr = array();
		foreach($gradeArr as $key=>$value){
			$gradeAllArr[$value['gid']] = $value['name'];
		}
		
		$vipHandoutsModel = D('VpHandouts');
		$uploadList = $vipHandoutsModel->get_historyUploadCount($handouts_subject,$handouts_grade,'',$type,strtotime($startTime),strtotime($endTime));
		$jiangyiHistory = array();
		$shitiArray = array();
	
		foreach($uploadList as $key=>$value){
			$uploadList[$key]['sname']	= $subjectAllArr[$value['sid']];
			$uploadList[$key]['gname']	= $gradeAllArr[$value['gid']];
			if($value['type'] == 0){
				$jiangyiHistory[$key]['sname'] = $uploadList[$key]['sname'];
				$jiangyiHistory[$key]['typename'] = '课程讲义';
				$jiangyiHistory[$key]['gname'] = $uploadList[$key]['gname'];
				$jiangyiHistory[$key]['jiangyinum'] = $uploadList[$key]['totalnum'];
			}else if($value['type'] == 1){
				$shitiArray[$key]['sname'] = $uploadList[$key]['sname'];
				$shitiArray[$key]['typename'] = '试题库';
				$shitiArray[$key]['gname'] = $uploadList[$key]['gname'];
				$shitiArray[$key]['shitinum'] = $uploadList[$key]['totalnum'];
			}
		}
		
		$jiangyiTitile = array(
			'sname'=>'学科',
			'typename'=>'讲义类型',
			'gname'=>'课程属性',
			'jiangyinum'=>'已有讲义数'
		);
		$shitiTitile = array(
			'sname'=>'学科',
			'typename'=>'讲义类型',
			'gname'=>'题库属性',
			'shitinum'=>'已有试题数'
		);
		array_unshift($jiangyiHistory,array_values($jiangyiTitile));
		$objPHPExcel->getActiveSheet(0)->setTitle('课程讲义','试题库');
		for($i=0;$i<count($jiangyiHistory);$i++){
			$j=0;
			foreach($jiangyiHistory[$i] as $k=>$v){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($j+65).($i+1),$v);
				$j++;
			}
		}
		$objPHPExcel->setActiveSheetIndex(0);    
		$objActSheet = $objPHPExcel->getActiveSheet(); 
		$objActSheet->getColumnDimension('A')->setWidth(15);
		$objActSheet->getColumnDimension('B')->setWidth(20);
		$objActSheet->getColumnDimension('C')->setWidth(20);
		$objActSheet->getColumnDimension('D')->setWidth(20);
		
		$msgWorkSheet = new PHPExcel_Worksheet($objPHPExcel, '试题库'); //创建一个工作表
        $objPHPExcel->addSheet($msgWorkSheet); //插入工作表
		array_unshift($shitiArray,array_values($shitiTitile));
		for($i=0;$i<count($shitiArray);$i++){
			$j=0;
			foreach($shitiArray[$i] as $k=>$v){
				$objPHPExcel->setActiveSheetIndex(1)->setCellValue(chr($j+65).($i+1),$v);
				$j++;
			}
		}
		$objPHPExcel->setActiveSheetIndex(1);    
		$objActSheet = $objPHPExcel->getActiveSheet(); 
		$objActSheet->getColumnDimension('A')->setWidth(15);
		$objActSheet->getColumnDimension('B')->setWidth(20);
		$objActSheet->getColumnDimension('C')->setWidth(20);
		$objActSheet->getColumnDimension('D')->setWidth(20); 
		
		ob_end_clean();
		header ('Pragma: public'); // HTTP/1.0
		header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
		header('Content-Disposition: attachment;filename=历史更新数据统计.xls');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
	
	
}

?>