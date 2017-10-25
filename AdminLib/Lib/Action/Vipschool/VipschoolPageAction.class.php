<?php
/*网校-页面管理*/
class VipschoolPageAction extends VipschoolCommAction{
	protected function notNeedLogin() {
		return array('VIPSCHOOL-VIPSCHOOLPAGE-UPLOADFILE','VIPSCHOOL-VIPSCHOOLPAGE-DELETEOBJECT');
	}


	/*首页管理*/
	public function index(){
		$gsschoolModel = D('Vipschool');
		if($_POST){
			if(!empty($_POST['focus'])){
				$msg = '';
				$status = 0;
				$gsschoolModel->empty_focus();
				foreach ($_POST['focus'] as $key=>$val){
					if(!empty($val) && !empty($_POST['link'][$key])){
						//if(!empty($_POST['fid'][$key])){
						//	$result = $gsschoolModel->update_focus(array('id'=>$_POST['fid'][$key],'url'=>$val,'link'=>$_POST['link'][$key]));
						//}else{
						$result = $gsschoolModel->add_focus($val,$_POST['link'][$key],$_POST['bg_color'][$key]);
						//}
						$msg .= '焦点图'.($key+1).'添加';
						//$msg .= (!empty($_POST['fid'][$key]))?'修改':'添加';
						$msg .= ($result)?'成功<br>':'失败<br>';
						$status = ($result)?1:0;
					}
				}
				if($status == 1){
					$this->success($msg);
				}else{
					$this->error($msg);
				}
			}
		}else{
			$focusList = $gsschoolModel->get_focusList();
			$focusCount = count($focusList);
			if($focusCount==0){
				$focusCount = 3;
			}

			$this->assign(get_defined_vars());
			$this->display();
		}

	}


	/*
	public function uploadFile(){
	if (!empty($_FILES)) {
	$targetFolder = UPLOAD_PATH.'vipschool/'.date('Y-m-d').'/';
	if(!file_exists($targetFolder)){
	mkdir($targetFolder);
	}
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	$imgTypeArr = array('jpg','jpeg','gif','png');
	if(in_array(strtolower($fileParts['extension']),$imgTypeArr)){
	$fileTypes = $imgTypeArr;
	}else{
	$fileTypes = array('doc','docx','ppt','pptx','pdf','xls');
	}
	$uniqidname = uniqid(mt_rand(), true);
	//if($_POST['is_realname'] == 1){
	//	$newFilename = pathinfo(str_replace("(","（",str_replace(")","）",$_FILES['Filedata']['name'])), PATHINFO_FILENAME).'_'.$uniqidname.".".strtolower($fileParts['extension']);
	//}else{
	$newFilename = $uniqidname.".".strtolower($fileParts['extension']);
	//}
	$targetFile =$targetFolder.$newFilename ;
	$delUrl = U('Vipschool/VipschoolPage/delFile');
	if (in_array(strtolower($fileParts['extension']),$fileTypes)) {
	if(move_uploaded_file($tempFile,$targetFile)){
	if(in_array(strtolower($fileParts['extension']),$imgTypeArr)){
	$autocut = isset($_POST['autocut'])?$_POST['autocut']:0;
	$thumb_file = AppCommAction::thumb_img($targetFile,$_POST['width'],$_POST['height'],$autocut);
	echo json_encode(array('num'=>$_POST['num'],'status'=>'<font color=#10d509>上传成功</font>','url'=>'/'.end(explode('/eap/','/'.$thumb_file)),'show_url'=>'/'.end(explode('/eap/',str_replace('Upload/','upload/',$thumb_file))),'del_url'=>$delUrl));
	}else{
	echo json_encode(array('num'=>$_POST['num'],'status'=>'<font color=#10d509>上传成功</font>','url'=>'/'.end(explode('/eap/','/'.$targetFile)),'show_url'=>'/'.end(explode('/eap/',str_replace('Upload/','upload/',$targetFile))),'del_url'=>$delUrl));
	}
	}else{
	echo json_encode(array('num'=>$_POST['num'],'status'=>'<font color=red>上传失败</font>'));
	}
	} else {
	echo json_encode(array('num'=>$_POST['num'],'status'=>'<font color=red>不支持的文件类型</font>'));
	}
	}

	}



	public function deleteFile(){
	$status = 0;
	if(!empty($_POST['url'])){
	@unlink(APP_DIR.$_POST['url']);
	if(!file_exists(APP_DIR.$_POST['url'])){
	$status = 1;
	}
	}
	echo json_encode(array('status'=>$status));
	}*/


	public function uploadFile(){
		set_time_limit(0);
		if (!empty($_FILES)) {
			//set_time_limit(0);
			import('ORG.Util.OssSdk');
			$oss_sdk_service = new ALIOSS();
			//设置是否打开curl调试模式
			$oss_sdk_service->set_debug_mode(FALSE);
			$bucket = C('BUCKET');

			$tempFile = $_FILES['Filedata']['tmp_name'];
			$fileParts = pathinfo($_FILES['Filedata']['name']);

			$imgTypeArr = array('jpg','jpeg','gif','png');
			if(in_array(strtolower($fileParts['extension']),$imgTypeArr)){
				$fileTypes = $imgTypeArr;
				$isFileType = 1; //图片
			}else{
				$fileTypes = array('flv','docx','doc','pdf','xlsx','xls','txt');
				$isFileType = 2; //视频、文件
			}
			$uniqidname = uniqid(mt_rand(), true);
			if($_POST['is_realname'] == 1){
				//		$newFilename = time().$uniqidname.".".strtolower($fileParts['extension']);
				$newFilename = time().".".strtolower($fileParts['extension']);
			}else{
				$newFilename = $uniqidname.".".strtolower($fileParts['extension']);
			}
			if($isFileType == 2){
				$object = C('OSS_video_PATH').date('Y-m-d').'/'.$newFilename;
			}else if($isFileType == 1){
				$object = C('OSS_IMG_PATH').date('Y-m-d').'/'.$newFilename;
			}
			$delUrl = U('Vipschool/VipschoolPage/deleteObject');
			if($_POST['autocut'] == 1 && $isFileType == 1){
				$targetFolder = UPLOAD_PATH.'vipschool/'.date('Y-m-d').'/';
				if(!file_exists($targetFolder)){
					mkdir($targetFolder);
				}
				
				if(move_uploaded_file($tempFile,$targetFolder.$newFilename)){
					$target_file = AppCommAction::thumb_img($targetFolder.$newFilename,$_POST['width'],$_POST['height'],$_POST['autocut']);
					$thumb = 1;
				}else{
					$target_file = $tempFile;
				}
			}else{
				$target_file = $tempFile;
			}
			if (in_array(strtolower($fileParts['extension']),$fileTypes)){
				$content = '';
				$length = 0;
				$fp = fopen($target_file,'r');
				if($fp){
					$f = fstat($fp);
					$length = $f['size'];
					while(!feof($fp)){
						$content .= fgets($fp);
					}
				}
				$upload_file_options = array('content' => $content, 'length' => $length);
				$upload_file_by_content = $oss_sdk_service->upload_file_by_content($bucket,$object,$upload_file_options);
				if($upload_file_by_content->status == 200){
					if($isFileType == 2){
						echo json_encode(array('num'=>$_POST['num'],'status'=>'<font color=green>上传成功</font>','url'=>$object,'show_url'=>$object,'del_url'=>$delUrl));
					}else if($isFileType == 1){
						if($thumb == 1){
							unlink($targetFolder.$newFilename);
						}
						echo json_encode(array('num'=>$_POST['num'],'status'=>'<font color=green>上传成功</font>','url'=>$object,'show_url'=>"http://".C('DEFAULT_OSS_HOST')."/".C('BUCKET')."/".$object,'del_url'=>$delUrl));
					}
				}else{
					echo json_encode(array('num'=>$_POST['num'],'status'=>'<font color=red>上传失败</font>'));
				}
			} else {
				echo json_encode(array('num'=>$_POST['num'],'status'=>'<font color=red>不支持的文件类型</font>'));
			}
		}
	}


	/*删除oss的object*/
	public  function deleteObject(){
		if(empty($_POST['url'])){
			echo 0;
			exit;
		}
		$bucket = C('BUCKET');
		$object = $_POST['url'];
		$status = 0;
		if($this->delete_oss_object($bucket,$object)){
			$status = 1;
		}
		echo json_encode(array('status'=>$status));
	}


	public function delete_oss_object($bucket,$object,$uid){
		import('ORG.Util.OssSdk');
		$oss_sdk_service = new ALIOSS();
		//设置是否打开curl调试模式
		$oss_sdk_service->set_debug_mode(FALSE);
		$response = $oss_sdk_service->delete_object($bucket,$object);
		$is_object_exist = $oss_sdk_service->is_object_exist($bucket,$object);
		if($is_object_exist->status == 404){
			return true;
		}else{
			return false;
		}
	}


	/*活动广告管理*/
	public function announcementList(){
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$condition = ' 1=1 ';
		$gsschoolModel = D('Vipschool');
		$dao = $gsschoolModel->dao;
		if($_REQUEST['keyword']!=''){
			$condition .= " AND title like '%".SysUtil::safeSearch(urldecode($_REQUEST['keyword']))."%' ";
		}
		
		$announcementList = $gsschoolModel->get_announcementList($condition,$curPage,$pagesize);
		$count = $gsschoolModel->get_announcementCount($condition);
		$page = new page($count,$pagesize);
		$showPage = $page->show();
		$keyword = $_REQUEST['keyword'];
		$this->assign(get_defined_vars());
		$this->display();
	}


	/*添加公告*/
	public function addAnnouncement(){
		if($_POST){
			$gsschoolModel = D('Vipschool');
			$result = $gsschoolModel->add_announcement($_POST);
			if($result){
				$this->success('活动公告添加成功',U('Vipschool/VipschoolPage/announcementList'));
			}else{
				$this->error('活动公告添加失败');
			}
		}else{
			$this->assign(get_defined_vars());
			$this->display();
		}

	}


	/*公告详情*/
	public function announcementInfo(){
		$aid = abs($_GET['aid']);
		$gsschoolModel = D('Vipschool');
		$announcementInfo = $gsschoolModel->get_announcementInfo($aid);
		$this->assign(get_defined_vars());
		$this->display();
	}


	/*编辑公告*/
	public function updateAnnouncement(){
		$aid = abs($_REQUEST['aid']);
		$gsschoolModel = D('Vipschool');
		if($_POST){
			$result = $gsschoolModel->update_announcement($_POST);
			if($result){
				$this->success('活动公告修改成功',U('Vipschool/VipschoolPage/announcementInfo',array('aid'=>$aid)));
			}else{
				$this->error('活动公告修改失败');
			}
		}else{
			$announcementInfo = $gsschoolModel->get_announcementInfo($aid);
			$this->assign(get_defined_vars());
			$this->display();
		}
	}


	/*教师管理*/
	public function teacherList(){
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$condition = ' 1=1 ';
		$gsschoolModel = D('Vipschool');
		$dao = $gsschoolModel->dao;
		if($_REQUEST['keyword']!=''){
			$condition .= ' AND realname = '.$dao->quote(SysUtil::safeSearch(urldecode($_REQUEST['keyword'])));
		}
		
		$teacherList = $gsschoolModel->get_teacherList($condition,$curPage,$pagesize);
		$count = $gsschoolModel->get_teacherCount($condition);
		$page = new page($count,$pagesize);
		$showPage = $page->show();
		$keyword = $_REQUEST['keyword'];
		$this->assign(get_defined_vars());
		$this->display();
	}


	/*添加教师*/
	public function addTeacher(){
		$gsschoolModel = D('Vipschool');
		$gradeList = $gsschoolModel->get_gradeList();
		if($_POST){
			$result = $gsschoolModel->add_teacher($_POST);
			if($result){
				$this->success('教师添加成功',U('Vipschool/VipschoolPage/teacherList'));
			}else{
				$this->error('教师添加失败');
			}
		}else{
			$this->assign(get_defined_vars());
			$this->display();
		}

	}


	public function getSubjectList(){
		$gsschoolModel = D('Vipschool');
		$subjectList = $gsschoolModel->get_subjectList(abs($_GET['gid']));
		$html = '';
		$status = 0;
		if(!empty($subjectList)){
			foreach ($subjectList as $key=>$subject){
				$html .= '<input type="radio" id="subject" name="subject" value="'.$subject['sid'].'">'.$subject['title'].'&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			$status = 1;
		}else{
			$html .= '<span class="error">此年部下暂无学科！请选择其他学部或立即去课程分类中添加。 请选择学科</span>';
		}
		echo json_encode(array('status'=>$status,'html'=>$html));
	}


	public function recommendTeacher(){
		$status = 0;
		if(!empty($_POST['tid'])){
			$gsschoolModel = D('Vipschool');
			if($gsschoolModel->recommend_teacher($_POST['tid'])){
				$status = 1;
				$msg = '教师推荐成功';
			}else{
				$msg = '教师推荐失败';
			}
		}else{
			$msg = '非法操作';
		}
		echo json_encode(array('status'=>$status,'msg'=>$msg));
	}


	public function teacherInfo(){
		$gsschoolModel = D('Vipschool');
		$teacherInfo = $gsschoolModel->get_teacherInfo($_GET['tid']);
		$teacherInfo['intro_content'] = $this->textarea_content_to($teacherInfo['teaching_style']);
		$teacherInfo['teaching_style'] = $this->textarea_content_to($teacherInfo['teaching_style']);
		$teacherInfo['experience_content'] = $this->textarea_content_to($teacherInfo['experience_content']);
		$teacherInfo['comment'] = $this->textarea_content_to($teacherInfo['comment']);
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function updateTeacher(){
		$gsschoolModel = D('Vipschool');
		$teacherInfo = $gsschoolModel->get_teacherInfo($_GET['tid']);
		$gradeList = $gsschoolModel->get_gradeList();
		if($_POST){
			$result = $gsschoolModel->update_teacher($_POST);
			if($result){
				$this->success('教师修改成功',U('Vipschool/VipschoolPage/teacherList'));
			}else{
				$this->error('教师修改失败');
			}
		}else{
			$this->assign(get_defined_vars());
			$this->display();
		}

	}



	public function helpList(){
		$gsschoolModel = D('Vipschool');
		$helpList = $gsschoolModel->getHelpList();
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function updateHelp(){
		$hid = abs($_GET['hid']);
		$gsschoolModel = D('Vipschool');
		if($_POST){
			if($gsschoolModel->updateHelpInfo($_POST)){
				$this->success('修改成功',U('Vipschool/VipschoolPage/helpList'));
			}else{
				$this->error('修改失败');
			}
		}else{
			$helpInfo = $gsschoolModel->getHelpInfo($hid);
			
			$this->assign(get_defined_vars());
			$this->display();
		}

	}
	
	
	
	public function dimissionTeacher(){
		$status = 0;
		if(!empty($_POST['tid'])){
			$gsschoolModel = D('Vipschool');
			if($gsschoolModel->dimission_teacher($_POST['tid'])){
				$status = 1;
				$msg = '离职操作成功';
			}else{
				$msg = '离职操作失败';
			}
		}else{
			$msg = '非法操作';
		}
		echo json_encode(array('status'=>$status,'msg'=>$msg));
	}
}
?>