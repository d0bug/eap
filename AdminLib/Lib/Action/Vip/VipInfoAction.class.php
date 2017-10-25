<?php
/*我的信息*/
class VipInfoAction extends VipCommAction{
	protected function notNeedLogin() {
		return array('VIP-VIPINFO-UPLOAD_IMG','VIP-VIPINFO-TEACHERINFO');
	}


	/*我的信息*/
	public function index(){
		$userInfo = $this->loginUser->getInformation();
		$userInfo['user_key'] = $this->loginUser->getUserKey();
		$userInfo['real_name'] = D('Users')->get_userRealName_by_userKey($userInfo['user_key']);
		$is_jiaoyan = $this->checkUserRole();
		$is_jianzhi = $this->checkUserType();
		$is_admin = VipCommAction::checkIsAdmin();

		if(!$userInfo['user_type']){
			$userInfo['user_type'] = $this->loginUser->getUserType();
		}
		$userInfo = $this->getUserOtherInfo($userInfo);
		$usersModel = D('Users');
		$db_userInfo = $usersModel->get_userInfo($userInfo['user_key']);
		$vpSubjectModel =  D('VpSubject');
		$subjectIdStr = $vpSubjectModel->get_thisuser_sidsStr($userInfo['user_key']);
		$subjectNameStr = $vpSubjectModel->get_subjectNameList_by_sids($subjectIdStr);
		$userInfo['roles'] = $usersModel->get_userRoles($userInfo['user_key'],'Vip',APP_NAME);
		if($is_admin){
			$userInfo['roles'] = "超级管理员, ".$userInfo['roles'];
		}
		$this->assign(get_defined_vars());
		$this->display();
	}

	/*更新所属部门*/
	protected function update_department(){
		$userInfo = $this->loginUser->getInformation();
		$userInfo['user_key'] = $this->loginUser->getUserKey();
		if(!$userInfo['user_type']){
			$userInfo['user_type'] = $this->loginUser->getUserType();
		}
		$userInfo = $this->getUserOtherInfo($userInfo);
		if(D('Users')->update_department($userInfo)){
			echo '更新成功';
		}
	}

	/*社会兼职教师修改密码*/
	public function updatePassword(){
		$usersModel = D('Users');
		$userInfo = $usersModel->get_userInfo($_POST['user_key']);
		if($userInfo['user_passwd'] == md5(trim($_POST['oldpasswd']))){
			if($usersModel->updatePassword(trim($_POST['newpasswd']),$_POST['user_key'])){
				$this->success('密码修改成功');
			}else{
				$this->error('密码修改失败');
			}
		}else{
			$this->error('原密码错误');
		}
	}


	/*获取教师网络宣传信息*/
	public function publicity(){
		$userKey = $this->loginUser->getUserKey();
		$rankArr = C('RANK');
		$gradesArr = C('GRADES');
		$subjectArr = C('SUBJECT');
		$is_ajax = isset($_GET['is_ajax'])?$_GET['is_ajax']:'0';
		$vippublicityModel = D('VpPublicity');
		$publicityInfo = $vippublicityModel->get_publicity_info($userKey);
		if(!file_exists(UPLOAD_PATH.str_replace('/Upload/','',$publicityInfo['avatar']))){
			$publicityInfo['avatar'] = '';
		}else{
			$publicityInfo['avatar_show'] = end(explode('eap',str_replace('Upload/','upload/',$publicityInfo['avatar'])));
		}
		if(!file_exists(UPLOAD_PATH.str_replace('/Upload/','',$publicityInfo['intro_img']))){
			$publicityInfo['intro_img'] = '';
		}else{
			$publicityInfo['intro_img_show'] = end(explode('eap',str_replace('Upload/','upload/',$publicityInfo['intro_img'])));
		}
		if(!file_exists(UPLOAD_PATH.str_replace('/Upload/','',$publicityInfo['teach_img']))){
			$publicityInfo['teach_img'] = '';
		}else{
			$publicityInfo['teach_img_show'] = end(explode('eap',str_replace('Upload/','upload/',$publicityInfo['teach_img'])));
		}
		if(!file_exists(UPLOAD_PATH.str_replace('/Upload/','',$publicityInfo['experience_img']))){
			$publicityInfo['experience_img'] = '';
		}else{
			$publicityInfo['experience_img_show'] = end(explode('eap',str_replace('Upload/','upload/',$publicityInfo['experience_img'])));
		}

		//获取授课校区、授课风格、教师资质
		$schoolList = $vippublicityModel->getVipSchoolList();
		$educationList = $vippublicityModel->getVipEducationList();
		$styleList = $vippublicityModel->getVipStyleList();

		$this->assign(get_defined_vars());
		$this->display();
	}


	/*更新教师宣传资料*/
	protected  function update_publicity(){
		$userKey = isset($_GET['userKey'])?$_GET['userKey']:$this->loginUser->getUserKey();
		$publicity_info = array();
		$publicity_info['avatar'] = isset($_POST['avatar'])?SysUtil::safeString($_POST['avatar']):'';
		$publicity_info['teacher_name'] = isset($_POST['teacher_name'])?SysUtil::safeString($_POST['teacher_name']):'';
		$publicity_info['gender'] = isset($_POST['gender'])?SysUtil::safeString($_POST['gender']):'';
		$publicity_info['rank'] = isset($_POST['rank'])?SysUtil::safeString($_POST['rank']):'';
		$publicity_info['subject'] = isset($_POST['subject'])?SysUtil::safeString($_POST['subject']):'';
		$publicity_info['grades'] = isset($_POST['grade'])?$_POST['grade']:'';
		$publicity_info['send_word'] = isset($_POST['send_word'])?str_replace(" ","&nbsp;",SysUtil::safeString($_POST['send_word'])):'';
		$publicity_info['intro_img'] = isset($_POST['intro_img'])?SysUtil::safeString($_POST['intro_img']):'';
		$publicity_info['intro_content'] = isset($_POST['intro_content'])?str_replace(" ","&nbsp;",SysUtil::safeString($_POST['intro_content'])):'';
		$publicity_info['teach_img'] = isset($_POST['teach_img'])?SysUtil::safeString($_POST['teach_img']):'';
		$publicity_info['teach_content'] = isset($_POST['teach_content'])?str_replace(" ","&nbsp;",SysUtil::safeString($_POST['teach_content'])):'';
		$publicity_info['achievement_content'] = isset($_POST['achievement_content'])?str_replace(" ","&nbsp;",SysUtil::safeString($_POST['achievement_content'])):'';
		$publicity_info['experience_img'] = isset($_POST['experience_img'])?SysUtil::safeString($_POST['experience_img']):'';
		$publicity_info['experience_content'] = isset($_POST['experience_content'])?str_replace(" ","&nbsp;",SysUtil::safeString($_POST['experience_content'])):'';
		$publicity_info['comment'] = isset($_POST['comment'])?str_replace(" ","&nbsp;",SysUtil::safeString($_POST['comment'])):'';
		$publicity_info['status'] = isset($_POST['status'])?abs($_POST['status']):0;
		$publicity_info['edu_id_list'] = isset($_POST['education'])?','.$_POST['education']:'';
		$publicity_info['style_id_list'] = isset($_POST['style'])?','.$_POST['style']:'';
		$publicity_info['school_id_list'] = isset($_POST['school'])?','.$_POST['school']:'';
		if(!empty($publicity_info['avatar']) && !empty($publicity_info['teacher_name']) && !empty($publicity_info['gender']) && !empty($publicity_info['subject']) && !empty($publicity_info['grades']) && !empty($publicity_info['intro_img']) && !empty($publicity_info['intro_content']) && !empty($publicity_info['teach_img']) && !empty($publicity_info['teach_content']) && !empty($publicity_info['achievement_content'])&& !empty($publicity_info['experience_img']) && !empty($publicity_info['experience_content']) && !empty($publicity_info['comment'])){
			$vippublicityModel = D('VpPublicity');
			$sqlAction = 'UPDATE';
			if($vippublicityModel->get_publicity_info($userKey) == false){
				$sqlAction = 'INSERT';
			}
			if($vippublicityModel->update_publicity($publicity_info,$userKey,$sqlAction)){
				//删除预览文件
				@unlink($_POST['preview_url']);
				echo '1';
			}
		}else{
			echo '0';
		}
	}


	/*网络信息预览*/
	public function preview(){
		$publicity_info = array();
		$publicity_info['avatar'] = isset($_POST['avatar'])?str_replace('Upload/','upload/',SysUtil::safeString($_POST['avatar'])):'';
		$publicity_info['teacher_name'] = isset($_POST['teacher_name'])?SysUtil::safeString($_POST['teacher_name']):'';
		$publicity_info['gender'] = isset($_POST['gender'])?trim($_POST['gender']):'';
		$publicity_info['subject'] = isset($_POST['subject'])?$_POST['subject']:'';
		$publicity_info['grades'] = isset($_POST['grade'])?trim($_POST['grade'],','):'';
		$publicity_info['send_word'] = isset($_POST['send_word'])?str_replace(" ","&nbsp;",SysUtil::safeString($_POST['send_word'])):'';
		$publicity_info['intro_img'] = isset($_POST['intro_img'])?str_replace('Upload/','upload/',SysUtil::safeString($_POST['intro_img'])):'';
		$publicity_info['intro_content'] = isset($_POST['intro_content'])?str_replace(" ","&nbsp;",SysUtil::safeString($_POST['intro_content'])):'';
		$publicity_info['teach_img'] = isset($_POST['teach_img'])?str_replace('Upload/','upload/',SysUtil::safeString($_POST['teach_img'])):'';
		$publicity_info['teach_content'] = isset($_POST['teach_content'])?str_replace(" ","&nbsp;",SysUtil::safeString($_POST['teach_content'])):'';
		$publicity_info['achievement_content'] = isset($_POST['achievement_content'])?str_replace(" ","&nbsp;",SysUtil::safeString($_POST['achievement_content'])):'';
		$publicity_info['experience_img'] = isset($_POST['experience_img'])?str_replace('Upload/','upload/',SysUtil::safeString($_POST['experience_img'])):'';
		$publicity_info['experience_content'] = isset($_POST['experience_content'])?str_replace(" ","&nbsp;",SysUtil::safeString($_POST['experience_content'])):'';
		$publicity_info['comment'] = isset($_POST['comment'])?str_replace(" ","&nbsp;",SysUtil::safeString($_POST['comment'])):'';

		//处理数据
		$grade_ids_arr = explode(',',$publicity_info['grades']);
		$gradeArr = C('GRADES');
		$show_grades = '';
		foreach ($grade_ids_arr as $key=>$gid){
			if(in_array($gradeArr[$gid],array('一年级','二年级','三年级','四年级','五年级','六年级')) && strpos($show_grades,'小学')===false){
				$show_grades .= '小学,';
				continue;
			}
			if(in_array($gradeArr[$gid],array('初一年级','初二年级','初三年级'))&& strpos($show_grades,'初中')===false){
				$show_grades .= '初中,';
				continue;
			}
			if(in_array($gradeArr[$gid],array('高一年级','高二年级','高三年级'))&& strpos($show_grades,'高中')===false){
				$show_grades .= '高中,';
				continue;
			}
			if(in_array($gradeArr[$gid],array('初中竞赛','高中竞赛','国际课程','自主招生','AMC'))){
				$show_grades .= $gradeArr[$gid].',';
				continue;
			}
		
		}

		$preview_folder = APP_DIR."/Html/";
		$preview_tpl = APP_DIR.'/Static/preview/preview.html';
		$userInfo = $this->loginUser->getInformation();
		$this_preview_html = $userInfo['user_name'].'_preview.html';
		$content = file_get_contents($preview_tpl);
		$content = str_replace("{avatar}",$publicity_info['avatar'],$content);
		$content = str_replace("{teacher_name}",$publicity_info['teacher_name'],$content);
		$content = str_replace("{gender}",($publicity_info['gender'] == 'm')?'女':'男',$content);
		$content = str_replace("{subject}",trim($publicity_info['subject'],','),$content);
		$content = str_replace("{grade}",$show_grades,$content);
		$content = str_replace("{send_word}",$publicity_info['send_word'],$content);
		$content = str_replace("{intro_img}",$publicity_info['intro_img'],$content);
		$content = str_replace("{intro_content}",$publicity_info['intro_content'],$content);
		$content = str_replace("{teach_img}",$publicity_info['teach_img'],$content);
		$content = str_replace("{teach_content}",$publicity_info['teach_content'],$content);
		$content = str_replace("{achievement_content}",$publicity_info['achievement_content'],$content);
		$content = str_replace("{experience_img}",$publicity_info['experience_img'],$content);
		$content = str_replace("{experience_content}",$publicity_info['experience_content'],$content);
		$content = str_replace("{comment}",$publicity_info['comment'],$content);
		$this->to_html($preview_folder.$this_preview_html,$content);
		echo json_encode(array('show_url'=>APP_URL.'/html/'.$this_preview_html,'url'=>$preview_folder.$this_preview_html));
	}


	public function to_html($file,$content){
		$fp=fopen($file,"w+");
		fwrite($fp,$content);
		fclose($fp);
	}


	/*上传图片*/
	public function upload_img(){
		if (!empty($_FILES)) {
			$targetFolder = UPLOAD_PATH.date('Y-m-d').'/';
			if(!file_exists($targetFolder)){
				mkdir($targetFolder,0777);
			}
			$tempFile = $_FILES['Filedata']['tmp_name'];
			$fileParts = pathinfo($_FILES['Filedata']['name']);
			$imgTypeArr = array('jpg','jpeg','gif','png');
			if(in_array(strtolower($fileParts['extension']),$imgTypeArr)){
				$fileTypes = $imgTypeArr;
				//$newFilename = $_POST['prename'].'_'.pathinfo($_FILES['Filedata']['name'], PATHINFO_FILENAME).'_'.uniqid(mt_rand(), true).".".strtolower($fileParts['extension']);
			}else{
				$fileTypes = array('doc','docx','ppt','pptx','pdf','xls');
				$vipHandoutsModel = D('VpHandouts');
				//$newFilename = uniqid(mt_rand(), true).'_file.'.strtolower($fileParts['extension']);
			}
			$prename = trim($_POST['prename'],'_');
			if(!empty($prename)) $prename .= '_';
			$uniqidname = uniqid(mt_rand(), true);
			if($_POST['is_realname'] == 1){
				$newFilename = $prename.pathinfo(str_replace("(","（",str_replace(")","）",$_FILES['Filedata']['name'])), PATHINFO_FILENAME).'_'.$uniqidname.".".strtolower($fileParts['extension']);
			}else{
				$newFilename = $uniqidname.".".strtolower($fileParts['extension']);
			}
			//$newFilename = $prename.pathinfo($_FILES['Filedata']['name'], PATHINFO_FILENAME).'_'.$uniqidname.".".strtolower($fileParts['extension']);
			$targetFile =$targetFolder.$newFilename ;
			if (in_array(strtolower($fileParts['extension']),$fileTypes)) {
				if(move_uploaded_file($tempFile,$targetFile)){
					if(in_array(strtolower($fileParts['extension']),$imgTypeArr)){
						$autocut = isset($_POST['autocut'])?$_POST['autocut']:0;
						$thumb_file = AppCommAction::thumb_img($targetFile,$_POST['width'],$_POST['height'],$autocut);
						echo json_encode(array('status'=>'上传成功','url'=>'/'.end(explode('/eap/','/'.$thumb_file)),'show_url'=>'/'.end(explode('Upload/',$thumb_file)),'delimg_url'=>U('Vip/VipInfo/del_img')));
					}else{
						$previewFile = '';
						if(in_array(strtolower($fileParts['extension']),$fileTypes) && $_POST['preview'] == 1){
							$shellFileDir = APP_DIR.'/sh';
							if(!file_exists($shellFileDir)){
								mkdir($shellFileDir);
							}
							$shellFileName = time().'.'.uniqid(mt_rand(), true).'.txt';
							$filesize = filesize($targetFile);
							$previewFile = $targetFolder.$uniqidname.'.swf';
							if($fileParts['extension'] != 'pdf'){
								$to_pdf = "unoconv -f pdf -o ".$targetFolder.$uniqidname.".pdf  ".$targetFile;//本地and线上
								if($filesize>1024*10240){//文件大于10M时
									$to_swf = C('pdf2swf')." -T 9 -s poly2bitmap ";
								}else{
									$to_swf = C('pdf2swf')." -T 9 ";//本地
								}
								$to_swf .= $targetFolder.$uniqidname.".pdf ".$targetFolder.$uniqidname.".swf";
								$command = $to_pdf."\n".$to_swf;
							}else{
								if($filesize>1024*10240){//文件大于10M时
									$command = C('pdf2swf')." -T 9 -s poly2bitmap ";//本地转swf
								}else{
									$command = C('pdf2swf')." -T 9 ";//本地转swf
								}
								$command .= $targetFile." ".$targetFolder.$uniqidname.".swf";
							}
							if(false == file_put_contents($shellFileDir.'/'.$shellFileName,$command)){
								$status = '上传失败';
							}else{
								$status = '上传成功';
							}
						}
						echo json_encode(array('status'=>$status,'url'=>'/'.end(explode('/eap/','/'.$targetFile)),'preview_url'=>'/'.end(explode('/eap/','/'.$previewFile)),'show_url'=>'/'.end(explode('/eap/',str_replace('Upload/','upload/',$targetFile))),'delimg_url'=>U('Vip/VipInfo/del_img'),'realname'=>$prename.reset(explode('.',$_FILES['Filedata']['name']))));
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
			if($file_type == 'pdf'){//删除对应的swf文件
				@unlink(str_replace(".pdf",".swf",APP_DIR.$_POST['url']));
			}
			if($file_type == 'doc' || $file_type == 'docx'){
				@unlink(str_replace(".".$file_type,".pdf",APP_DIR.$_POST['url']));
				@unlink(str_replace(".".$file_type,".swf",APP_DIR.$_POST['url']));
			}
			@unlink(APP_DIR.$_POST['url']);
			if(!file_exists(APP_DIR.$_POST['url'])){
				echo '1';
			}else{
				echo '0';
			}
		}else{
			echo '0';
		}
	}


	/*设置手机号码*/
	public function set_mobile(){
		$phone = isset($_GET['phone'])?SysUtil::safeString($_GET['phone']):'';
		if(!empty($phone)){
			if(SysUtil::isMobile($phone)){
				$userInfo = $this->loginUser->getInformation();
				$usersModel = D('Users');
				if($usersModel->set_mobile($phone,$this->loginUser->getUserKey(),$userInfo)){
					echo '1';
				}else{
					echo '0';
				}
			}else{
				echo '0';
			}
		}else{
			echo '0';
		}
	}


	/*修改手机号码*/
	public function modify_mobile(){
		$newphone = isset($_POST['newphone'])?SysUtil::safeString($_POST['newphone']):'';
		if(!empty($newphone)){
			if(SysUtil::isMobile($newphone)){
				$userInfo = $this->loginUser->getInformation();
				$usersModel = D('Users');
				if($usersModel->set_mobile($newphone,$this->loginUser->getUserKey(),$userInfo)){
					$this->success('手机号码修改成功',U('/Vip/VipInfo/index'));
				}else{
					$this->error('手机号码修改失败');
				}
			}else{
				$this->error('手机号码格式错误');
			}
		}
	}


	/*我的收藏*/
	public function my_favorite(){
		$user_key = $this->loginUser->getUserKey();
		$handoutsType = C('HANDOUTS_TYPE');
		$vipHandoutsModel = D('VpHandouts');
		$dao = $vipHandoutsModel->dao;

		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$condition = " f.[user_key] = '$user_key'";
		if(isset($_GET['type']) && $_GET['type']!==''){
			$condition .= " AND f.[htype] = '$_GET[type]' ";
		}
		if(!empty($_GET['keyword'])){
			$condition .= ' AND h.[title] like '.$dao->quote('%' . SysUtil::safeSearch_vip($_GET['keyword']) . '%');
		}
		$favoriteList = $vipHandoutsModel->get_favoriteList($condition,$curPage,$pagesize);
		$count = $vipHandoutsModel->get_favoriteListCount($condition);
		$page = new page($count,$pagesize);
		$showPage = $page->show();
		$htype = isset($_GET['type'])?$_GET['type']:'';
		$keyword = isset($_GET['keyword'])?$_GET['keyword']:'';

		$this->assign(get_defined_vars());
		$this->display();
	}


	public function not_passReview(){
		$pid = isset($_GET['pid'])?abs($_GET['pid']):0;
		if(D('VpPublicity')->update_passStatus(array('status'=>2,'pid'=>$pid))){
			D('VpHandouts')->add_message(array('user_key'=>trim($_GET['user_key']),'type'=>2,'source_id'=>$pid,'message'=>'您的<font class=blue>个人宣传资料</font>未通过审核，请及时修改~','instime'=>date('Y-m-d H:i:s')));
			$this->success('审核不通过操作成功',U('/Vip/VipReview/publicityReview'));
		}else{
			$this->error('操作失败');
		}
	}


	public function teacherInfo(){
		ob_clean();
		$status = 0;
		$publicityInfo =  array();
		$teacherCode = trim($_REQUEST['code']);
		if(!empty($teacherCode)){
			$vippublicityModel = D('VpPublicity');
			$userKey = $vippublicityModel->getuserKeyByTeacherCode($teacherCode);
			$publicityInfo = $vippublicityModel->get_publicity_info($userKey);
			if(!empty($publicityInfo['intro_img'])){
				$publicityInfo['intro_img'] = 'http://vip.gaosiedu.com'.str_replace('/Upload/','/upload/',$publicityInfo['intro_img']);
			}
			$publicityInfo['gender'] = (trim($publicityInfo['gender'])=='m')?'女':'男';
			unset($publicityInfo['pid']);
			unset($publicityInfo['user_key']);
			unset($publicityInfo['edu_id_list']);
			unset($publicityInfo['style_id_list']);
			unset($publicityInfo['school_id_list']);
			unset($publicityInfo['avatar']);
			unset($publicityInfo['subject']);
			unset($publicityInfo['grades']);
			unset($publicityInfo['send_word']);
			unset($publicityInfo['rank']);
			$status = 1;
		}
		echo json_encode(array('status'=>$status,'data'=>$publicityInfo));
		
	}
}
?>