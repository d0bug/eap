<?php
/*高思作文*/
class EssayAction extends EssayCommAction{
	protected function notNeedLogin() {
		return array('ESSAY-ESSAY-DO_UPLOAD_ESSAYIMG');
	}

	/*作文列表*/
	public function main(){
		$essayModel = D('Essay');
		$userInfo = $this->loginUser->getInformation();
		$classList = $essayModel->get_classList($userInfo,0);
		$classCodeStr = '';
		if(!empty($classList)){
			$classNameArr = array();
			foreach ($classList as $key => $class){
				$classCodeStr .= $class['s_class_code'].',';
				$classNameArr[] = $class['s_class_name'];
			}
			$classNameArr = array_unique($classNameArr);
		}
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$condition = '';
		if(!empty($classCodeStr)){
			$classCodeStr = "'".implode("','",explode(',',trim($classCodeStr,',')))."'";
			$condition .= " AND `class_code` IN ($classCodeStr)";
		}
		if(!empty($_GET['className'])){
			$condition .= " AND `class_name` = '$_GET[className]'";
		}
		if(!empty($_GET['classCode'])){
			$condition .= " AND `class_code` = '$_GET[classCode]'";
		}
		$essayList = $essayModel->get_essayList($condition,$curPage,$pagesize);
		$count = $essayModel->get_essayCount($condition);
		$page = new page($count,$pagesize);
		$showPage = $page->show();

		$this->assign(get_defined_vars());
		$this->display();
	}


	public function selectClass(){
		$userInfo = $this->loginUser->getInformation();
		$essayModel = D('Essay');
		$dtDate = $_GET['dtDate'];
		$islimit = !empty($dtDate)?0:1;
		$classList = $essayModel->get_classList($userInfo,$islimit,$dtDate);
		if($_POST){
			$classInfo = isset($_POST['classInfo'])?explode('|',trim($_POST['classInfo'],'|')):array();
			if($_POST['speakerNumber_'.$classInfo[1]]){
				$classInfo[] = abs($_POST['speakerNumber_'.$classInfo[1]]);
			}
			echo '<script>window.location.href="'.U('Essay/Essay/addEssay',array('classInfoStr'=>implode('|',$classInfo))).'"</script>';
		}else{
			$this->assign(get_defined_vars());
			$this->display();
		}
	}


	/*上传作文*/
	public function addEssay(){
		$userInfo = $this->loginUser->getInformation();
		$essayModel = D('Essay');
		if($_POST){
			$classInfo = isset($_POST['classInfo'])?explode('|',trim($_POST['classInfo'],'|')):array();
			if($_POST['speakerNumber_'.$classInfo[1]]){
				$classInfo[] = abs($_POST['speakerNumber_'.$classInfo[1]]);
			}
			$studentArr = explode('|',$_POST['studentInfo']);
			$arr = $this->deal_classInfo($classInfo);
			$arr['id'] = isset($_POST['id'])?abs($_POST['id']):'';
			$arr['student_code'] = $studentArr[0];
			$arr['student_name'] = $studentArr[1];
			if($essayModel->get_essayInfo($arr) && empty($_POST['id'])){
				echo "<script type='text/javascript'>alert('该学生已上传过作文照片，不能重复上传');window.location.href=\"".U('Essay/Essay/addEssay',array('id'=>$arr['id'],'classInfoStr'=>implode('|',$classInfo)))."\";</script>";
			}else{
				$arr['essay_imgs'] = isset($_POST['essayImgs'])?trim($_POST['essayImgs']):'';
				$arr['instime'] = date('Y-m-d H:i:s');
				$arr['create_user'] = $this->loginUser->getUserKey();
				if($essayId = $essayModel->add_essay($arr)){
					$essayId = (!empty($arr['id']))?$arr['id']:$essayId;
					echo "<script type='text/javascript'>alert('作文上传成功，进入编辑作文属性页面');window.location.href=\"".U('Essay/Essay/editEssayAttribute',array('id'=>$essayId))."\";</script>";
				}else{
					echo "<script type='text/javascript'>alert('保存失败');window.location.href=\"".U('Essay/Essay/addEssay',array('id'=>$arr['id'],'classInfoStr'=>implode('|',$classInfo)))."\";</script>";
				}
			}

		}else{
			$dtDate = $_GET['dtDate'];
			$islimit = !empty($dtDate)?0:1;
			$essayId = isset($_GET['id'])?abs($_GET['id']):0;
			$essayInfo = array();
			if(!empty($essayId)){
				$essayInfo = $essayModel->get_essayInfo(array('id'=>$essayId));
				$essayInfo['img_num'] = 0;
				if(!empty($essayInfo['essay_imgs'])){
					$essay_img_arr =  explode('|',trim($essayInfo['essay_imgs'],'|'));
					$essayInfo['img_num'] = count($essay_img_arr);
					foreach ($essay_img_arr as $key =>$essay_img){
						$show_img = str_replace('Upload/','upload/',$essay_img);
						$previewHtml .= '<li id="pre_'.abs($key).'"><img src="'.$show_img.'" width="200" height="200"><div class="img_name">'.end(explode('/',$essay_img)).'</div><div class="delete"><a href="javascript:void(0)" onclick="return del_img(\''.U('Essay/Essay/del_img').'\',\''.$essay_img.'\',\''.$key.'\',\''.$essayId.'\')">删除</a></div></li>';
					}
				}
			}else{
				$classInfo = isset($_GET['classInfoStr'])?explode('|',urldecode(trim($_GET['classInfoStr'],'|'))):array();
				$essayInfo = $this->deal_classInfo($classInfo);
				if(!empty($_GET['student_code']) && !empty($_GET['student_name'])){
					$essayInfo['student_code'] = $_GET['student_code'];
					$essayInfo['student_name'] = urldecode($_GET['student_name']);
				}
			}
			$studentArr = $essayModel->get_studentList(array('class_code'=>$essayInfo['class_code'],'speaker_number'=>$essayInfo['speaker_number']));
			$studentList = AppCommAction::array_sort($studentArr,'sname','asc');
			if(!empty($studentList)){
				foreach ($studentList as $key=>$student){
					if($essayInfo['student_code'] ==$student['sstudentcode'] ){
						$essayInfo['is_exist'] = 1;
					}
				}
			}
			//获取该老师此班级的所有讲次
			$userInfo = $this->loginUser->getInformation();
			if($userInfo['nkind'] == 1){
				$condition = " AND (case when les.[sAssistRealTeacherCode]<>'') then les.[sAssistRealTeacherCode] else les.[sAssistTeacherCode] end ) = '$userInfo[scode]'";
			}else{
				$condition = " AND (case when les.[sRealTeacherCode]<>'' then les.[sRealTeacherCode] else les.[sTeacherCode]  end )= '$userInfo[scode]'";
			}
			$speakerList = $essayModel->get_lessonList($essayInfo['class_code'],$condition);

			$this->assign(get_defined_vars());
			$this->display();
		}
	}



	/*编辑作文属性*/
	public function editEssayAttribute(){
		session_start();
		$essayId = isset($_GET['id'])?abs($_GET['id']):0;
		$essayModel = D('Essay');
		if($_POST){
			$_SESSION['essayLength'] = $_POST['essayLength'];
			$_SESSION['typeOne'] = $_POST['typeOne'];
			$_SESSION['typeTwo'] = $_POST['typeTwo'];
			$_SESSION['typeThree'] = $_POST['typeThree'];
			$_SESSION['typeFour'] = $_POST['typeFour'];
			if($essayModel->editEssayAttribute(array('essayId'=>$_POST['essayId'],'essayLength'=>$_POST['essayLength'],'typeOne'=>$_POST['typeOne'],'typeTwo'=>$_POST['typeTwo'],'typeThree'=>$_POST['typeThree'],'typeFour'=>$_POST['typeFour'],'themeName'=>$_POST['themeName']))){
				$this->success('作文属性编辑成功',U('Essay/Essay/addEssay',array('classInfoStr'=>$_POST['classInfo'])));
			}else{
				$this->error('作文属性编辑失败');
			}
		}else{
			$session = $_SESSION;
			$essayInfo = $essayModel->get_essayInfo(array('id'=>$essayId));
			$essayImgsArr = explode('|',trim($essayInfo['essay_imgs'],'|'));
			if(!empty($essayImgsArr)){
				foreach ($essayImgsArr as $key=>$essayImg){
					$tempArr[$key]['img'] = $essayImg;
				}
			}
			if(!empty($tempArr)){
				$essayImgsThumb = $this->deal_essayImg($tempArr);
			}
			$essayTypeList = $essayModel->get_essayTypeList();
			if(!empty($essayInfo['type_one']) || !empty($session['typeOne'])){
				$type_one = !empty($essayInfo['type_one'])?$essayInfo['type_one']:$session['typeOne'];
				$type_one_id = $essayModel->get_essayTypeId_by_name($type_one,0);
				$essayTypeTwoList = $essayModel->get_essayTypeList($type_one_id);
			}
			if(!empty($essayInfo['type_two'])|| !empty($session['typeTwo'])){
				$type_two = !empty($essayInfo['type_two'])?$essayInfo['type_two']:$session['typeTwo'];
				$type_two_id = $essayModel->get_essayTypeId_by_name($type_two,$type_one_id);
				$essayTypeThreeList = $essayModel->get_essayTypeList($type_two_id);
			}
			if(!empty($essayInfo['type_three'])|| !empty($session['typeThree'])){
				$type_three = !empty($essayInfo['type_three'])?$essayInfo['type_three']:$session['typeThree'];
				$type_three_id = $essayModel->get_essayTypeId_by_name($type_three,$type_two_id);
				$essayTypeFourList = $essayModel->get_essayTypeList($type_three_id);
			}
			if($essayInfo['type_one'] == '记事的' || $session['typeOne']=='记事的'){
				$essayThemeList = $essayModel->get_essayThemlList();
			}
			$essayLengthArr = C('ESSAY_LENGTH_ARR');

			$this->assign(get_defined_vars());
			$this->display();
		}
	}


	public function deal_classInfo($arr){
		$new_arr = array();
		$new_arr['class_name'] = $arr[0];
		$new_arr['class_code'] = $arr[1];
		$new_arr['campus_name'] = $arr[2];
		$new_arr['teacher_name'] = $arr[3];
		$new_arr['dtbegindate'] = $arr[4];
		$new_arr['dtenddate'] = $arr[5];
		$new_arr['sprinttime'] = $arr[6];
		$new_arr['speaker_number'] = $arr[7];
		return $new_arr;
	}



	protected function del_img(){
		$imgurl = isset($_GET['url'])?trim($_GET['url']):'';
		$essayId = isset($_GET['essayId'])?abs($_GET['essayId']):'';
		$essayImgs = isset($_GET['essayImgs'])?trim($_GET['essayImgs']):'';
		//删除原有作文照片
		@unlink(APP_DIR.$imgurl);
		$new_essayImgs =  str_replace($imgurl.'|','',$essayImgs);
		echo json_encode(array('essay_imgs'=>$new_essayImgs,'status'=>1));
	}


	protected function getChildren(){
		$pid = isset($_GET['pid'])?abs($_GET['pid']):'';
		$topId = isset($_GET['topId'])?abs($_GET['topId']):'';
		$deep = isset($_GET['deep'])?abs($_GET['deep']):1;
		$essayModel = D('Essay');
		$typeHtml = '';
		if($deep == 1){
			if($topId==2 && $pid==2){//记事的
				$typeHtml .= '<div style="width:120px;float:left;font-weight:bold">A、按内容分类：</div>';
			}else{
				$typeHtml .= '<div style="width:120px;float:left">&nbsp;</div>';
			}
		}
		if(!empty($pid)){
			$oneLevelArr =  $essayModel->get_essayTypeList();
			$oneLevelIdStr = '';
			if(!empty($oneLevelArr)){
				foreach ($oneLevelArr as $key=>$type){
					$oneLevelIdStr .= $type['id'].',';
				}
			}
			$essayList = $essayModel->get_essayTypeList($pid);
			if(!empty($essayList)){
				foreach ($essayList as $key=>$essay){
					$isChild = $essayModel->get_essayTypeList($essay['id']);
					if(strpos(','.$oneLevelIdStr,','.$essay['pid'].',')!==false){
						$typeHtml .= "<li onclick=\"getChildren(this,'".$essay['id']."','#grandson','".$essay['top_id']."','".U('Essay/Essay/getChildren')."',".$essay['deep'].");selectType(".$essay['deep'].",'".$essay['name']."');$(this).addClass('bgcolor');\">".$essay['name']."</li>";
					}else if(!empty($isChild)){
						$typeHtml .= "<li onclick=\"getChildren(this,'".$essay['id']."','#four','".$essay['top_id']."','".U('Essay/Essay/getChildren')."',".$essay['deep'].");selectType(".$essay['deep'].",'".$essay['name']."');$(this).addClass('bgcolor');\">".$essay['name']."</li>";
					}else{
						$typeHtml .= "<li onclick=\"selectType(".$essay['deep'].",'".$essay['name']."');$(this).addClass('bgcolor');\">".$essay['name']."</li>";
					}
				}
			}
		}
		$themeHtml = '';
		if($topId==2 ){//记事的
			$themeHtml .= '<div style="width:120px;float:left;font-weight:bold">B、按主题分类：</div><div style="margin-left:50px;">';
			$themeList = $essayModel->get_essayThemlList();
			if(!empty($themeList)){
				foreach ($themeList as $key=>$theme){
					$themeHtml .= "<li onclick=\"selectType(9,'".$theme['name']."');$(this).addClass('bgcolor');\">".$theme['name']."</li>";
				}
			}
			$themeHtml .= '</div>';
		}
		echo json_encode(array('typeHtml'=>$typeHtml,'themeHtml'=>$themeHtml));
	}



	/*图片上传*/
	public  function do_upload_essayImg(){
		if (!empty($_FILES)) {
			$targetFolder = UPLOAD_PATH.date('Y-m-d').'/';
			if(!file_exists($targetFolder)){
				mkdir($targetFolder);
			}
			$tempFile = $_FILES['Filedata']['tmp_name'];
			$fileParts = pathinfo($_FILES['Filedata']['name']);
			$fileTypes = array('jpg','jpeg','gif','png'); //允许的文件后缀
			$targetFile =$targetFolder.uniqid(mt_rand(), true).$_FILES['Filedata']['name'] ;//上传后的图片路径
			if (in_array(strtolower($fileParts['extension']),$fileTypes)) {
				if(move_uploaded_file($tempFile,$targetFile)){
					$thumb_file = AppCommAction::thumb_img($targetFile,$_POST['width'],$_POST['height']);
					echo json_encode(array('status'=>'上传成功','url'=>end(explode('/eap',$thumb_file)),'show_url'=>end(explode('Upload/',$thumb_file)),'delimg_url'=>U('Essay/Essay/del_img'),'real'=>end(explode('/',$thumb_file))));
				}else{
					echo json_encode(array('status'=>'上传失败'));
				}
			} else {
				echo json_encode(array('status'=>'不支持的文件类型'));
			}
		}
	}


	protected function getClassCodeList(){
		$className = isset($_GET['className'])?$_GET['className']:'';
		$userInfo = $this->loginUser->getInformation();
		$essayModel = D('Essay');
		$classCodeList = $essayModel->get_classCodeList($userInfo,$className);
		$classCodeHtml = '<option value="">请选择班级编码</option>';
		if(!empty($classCodeList)){
			foreach ($classCodeList as $key =>$classCode){
				$classCodeHtml .= '<option value="'.$classCode['sclasscode'].'">'.$classCode['sclasscode'].'</option>';
			}
		}
		echo $classCodeHtml;
	}


	public function essayDetail(){
		$class_code = isset($_GET['class_code'])?trim($_GET['class_code']):'';
		if(!empty($class_code)){
			$essayModel = D('Essay');
			$userInfo = $this->loginUser->getInformation();
			$essayInfo = $essayModel->get_classInfo($class_code,0,$userInfo);
		}
		$this->assign(get_defined_vars());
		$this->display();
	}


	/*班级作文照片预览*/
	public function essayImgList(){
		$key_name = isset($_GET['key_name'])?SysUtil::safeString(trim($_GET['key_name'])):'instime';
		$order = isset($_GET['order'])?strtolower(trim($_GET['order'])):'desc';
		if(isset($_GET['class_info'])){
			$arr = $this->deal_classInfo(explode('|',urldecode($_GET['class_info'])));
		}else{
			$arr['class_code'] = isset($_GET['class_code'])?SysUtil::safeString(trim($_GET['class_code'])):'';
		}
		$arr['speaker_number'] = isset($_GET['speaker_number'])?abs($_GET['speaker_number']):0;
		$arr['student_code'] = isset($_GET['student_code'])?SysUtil::safeString(trim($_GET['student_code'])):'';
		$arr['student_name'] = isset($_GET['student_name'])?SysUtil::safeString(urldecode($_GET['student_name'])):'';
		if(!empty($arr['class_code']) && !empty($arr['speaker_number'])){
			$essayModel = D('Essay');
			$essayInfo = $essayModel->get_essayInfo(array('class_code'=>$arr['class_code'],'speaker_number'=>$arr['speaker_number'],'student_code'=>$arr['student_code'],'student_name'=>$arr['student_name'],'key_name'=>$key_name,'order'=>$order));
			if(empty($essayInfo)){
				$essayInfo = $arr;
				$essayInfo['essay_imgs'] = array();
			}else{
				$essay_imgs_str = '';
				if($order == 'desc'){
					$tempArr = explode('|',trim($essayInfo['essay_imgs'],'|'));
					krsort($tempArr);
					$essayInfo['essay_imgs'] = implode("|",$tempArr).'|';
				}
				$essay_imgs_str .= $essayInfo['essay_imgs'];
				$essayImgsArr = explode('|',trim($essay_imgs_str,'|'));
				if(!empty($essayImgsArr)){
					foreach ($essayImgsArr as $key=>$essayImg){
						$tempArr2[$key]['img'] = $essayImg;
					}
				}
				if(!empty($tempArr2)){
					$essayImgsThumb = $this->deal_essayImg($tempArr2);
					$essayImgsList = ($key_name == 'thumb_name')?AppCommAction::array_sort($essayImgsThumb,$key_name,$order):$essayImgsThumb;
				}
			}
			$avatarArr = $essayModel->get_studentAvatar($arr['student_code']);
			$essayInfo['avatar'] = $avatarArr['avatar'];
			$essayInfo['is_extra_student'] = (!preg_match('/^[A-Z]/',$arr['student_code']))?1:0;
			//获取该老师此班级的所有讲次
			$userInfo = $this->loginUser->getInformation();
			if($userInfo['nkind'] == 1){
				$condition = " AND (case when les.[sAssistRealTeacherCode]<>'') then les.[sAssistRealTeacherCode] else les.[sAssistTeacherCode] end ) = '$userInfo[scode]'";
			}else{
				$condition = " AND (case when les.[sRealTeacherCode]<>'' then les.[sRealTeacherCode] else les.[sTeacherCode]  end )= '$userInfo[scode]'";
			}
			$speakerList = $essayModel->get_lessonList($essayInfo['class_code'],$condition);
		}
		$this->assign(get_defined_vars());
		$this->display();
	}


	/*优秀作文选*/
	public function excellentList(){
		$key_name = isset($_GET['key_name'])?trim($_GET['key_name']):'thumb_name';
		$order = isset($_GET['order'])?strtolower(trim($_GET['order'])):'asc';
		$userInfo = $this->loginUser->getInformation();
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$essayModel = D('Essay');
		$excellentList = $essayModel->get_excellentList($userInfo['user_key'],' ex.instime '.$order,$curPage,$pagesize);
		$count = $essayModel->get_excellentCount($userInfo['user_key']);
		$page = new Page($count,$pagesize);
		$showPage = $page->show();
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function deal_essayImg($arr){
		$new_arr = array();
		if(!empty($arr)){
			foreach ($arr as $key=>$essayImg){
				list($width,$height) = getimagesize(UPLOAD_PATH.str_replace('/Upload/','',$essayImg['img']));
				$new_arr[$key]['thumb_name'] = end(explode('/',$essayImg['img']));
				$new_arr[$key]['url'] = str_replace('/Upload/','/upload/',$essayImg['img']);
				$new_arr[$key]['show_width'] = 1000;
				$new_arr[$key]['show_height'] = ceil(($new_arr[$key]['show_width']/$width)*$height);
				$new_arr[$key]['thumb_height'] = 200;
				$new_arr[$key]['thumb_width'] = 200;
				if($essayImg['instime']){
					$new_arr[$key]['instime'] = $essayImg['instime'];
				}
			}
		}
		return $new_arr;
	}


	/*添加到优秀作文选*/
	protected function do_excellent(){
		$operate = isset($_GET['act'])?trim($_GET['act']):'';
		$essay_id = isset($_GET['essay_id'])?abs($_GET['essay_id']):'';
		$essay_id_str = isset($_GET['essay_id_str'])?trim($_GET['essay_id_str']):'';
		$class_code = isset($_GET['class_code'])?trim($_GET['class_code']):'';
		$speaker_number = isset($_GET['speaker_number'])?abs($_GET['speaker_number']):0;
		$userInfo = $this->loginUser->getInformation();
		if(D('Essay')->do_excellent(array('operate'=>$operate,'class_code'=>$class_code,'speaker_number'=>$speaker_number,'essay_id'=>$essay_id,'essay_id_str'=>$essay_id_str,'user_key'=>$userInfo['user_key']))){
			echo '操作成功';
		}else{
			echo '操作失败';
		}
	}

	/*展示作文图片*/
	public function show_essayImg(){
		if(!empty($_GET['essay_id'])){
			$essayInfo = D('Essay')->get_essayInfo(array('id'=>abs($_GET['essay_id'])));
			$essayImgArr = array();
			if(!empty($essayInfo['essay_imgs'])){
				$essayInfo['essay_imgs'] = str_replace('/Upload/','/upload/',$essayInfo['essay_imgs']);
				$essayImgArr = explode('|',trim($essayInfo['essay_imgs'],'|'));
				krsort($essayImgArr);
			}
			$count = count($essayImgArr);
			$this->assign(get_defined_vars());
			$this->display();
		}else{
			echo '非法操作';
		}
	}


	/*添加学生*/
	protected function addStudent(){
		if(empty($_POST['class_code']) || empty($_POST['speaker_number'])){
			$arr['status'] = 0;
			$arr['msg'] = '非法操作';
		}else{
			$essayModel = D('Essay');
			$studentList = $essayModel->get_studentList(array('class_code'=>$_POST['class_code'],'speaker_number'=>$_POST['speaker_number']));
			if(!empty($studentList)){
				foreach ($studentList as $key=>$student){
					$studentName[] = $student['sname'];
				}
			}
			if(in_array($_POST['student_name'],$studentName)){
				$arr['status'] = 0;
				$arr['msg'] = '该学生已存在';
			}else{
				if($new_studentId = $essayModel->add_student(array('class_code'=>$_POST['class_code'],'speaker_number'=>$_POST['speaker_number'],'student_name'=>SysUtil::safeString($_POST['student_name'])))){
					$arr['status'] = 1;
					$arr['msg'] = '学生添加成功';
					$arr['student_code'] = $new_studentId;
				}
			}
		}
		echo json_encode($arr);
	}


	protected function showStudents(){
		$studentHtml = '';
		if(empty($_GET['class_code']) || empty($_GET['speaker_number'])){
			$studentHtml .= '非法操作';
		}else{
			$essayModel = D('Essay');
			$studentArr = $essayModel->get_studentList(array('class_code'=>$_GET['class_code'],'speaker_number'=>$_GET['speaker_number']));
			$studentList = AppCommAction::array_sort($studentArr,'sname','asc');
			if(!empty($studentList)){
				foreach ($studentList as $key=>$student){
					$essayInfo = $essayModel->get_essayInfo(array('class_code'=>$_GET['class_code'],'speaker_number'=>$_GET['speaker_number'],'student_code'=>$student['sstudentcode'],'student_name'=>$student['sname']));
					$studentHtml .= '<li><a href="'.U('Essay/Essay/essayImgList',array('class_info'=>$_GET['class_info'],'class_code'=>$_GET['class_code'],'speaker_number'=>$_GET['speaker_number'],'student_name'=>$student['sname'],'student_code'=>$student['sstudentcode'])).'" '.$css.'>';
					if(!empty($student['is_upload'])){
						$studentHtml .= ' <font color="#ff8400"> '.$student['sname'].'</font>';
					}else{
						$studentHtml .= $student['sname'];
					}
					$studentHtml .= '</a></li>';
				}
			}
		}
		echo $studentHtml;
	}


	public function changeAvatar(){
		$student_code = trim($_GET['student_code']);
		if(!empty($student_code)){
			$essayModel = D('Essay');
			$avatar = $essayModel->get_studentAvatar($student_code);
			if($_POST){
				if($essayModel->update_studentAvatar(array('student_code'=>$student_code,'avatar'=>SysUtil::safeString($_POST['avatar']),'act'=>trim($_POST['act'])))){
					$this->success('头像上传/修改成功');
				}else{
					$this->error('头像上传/修改失败');
				}
			}
			$this->assign(get_defined_vars());
			$this->display();
		}else{
			echo '非法操作';
		}
	}


	protected function saveAvatar(){
		$status = 0;
		if($_POST['avatar'] || $_POST['student_code']){
			$student_code = SysUtil::safeString($_POST['student_code']);
			$essayModel = D('Essay');
			$avatar = $essayModel->get_studentAvatar($student_code);
			if($essayModel->update_studentAvatar(array('student_code'=>$student_code,'avatar'=>SysUtil::safeString($_POST['avatar']),'act'=>trim($_POST['act'])))){
				$status= 1;
				$url = U('Essay/Essay/changeAvatar',array('student_code'=>$student_code));
			}
		}
		echo json_encode(array('status'=>$status,'url'=>$url));
	}

}

?>