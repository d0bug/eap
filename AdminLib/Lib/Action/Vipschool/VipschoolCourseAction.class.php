<?php
/*网校-课程管理*/
class VipschoolCourseAction extends VipschoolCommAction{
	protected function notNeedLogin() {
		return array('VIPSCHOOL-VIPSCHOOLCOURSE-UPLOADFILE');
	}

	/*课程列表*/
	public function courseList(){
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$course_name = isset($_POST['course_name'])?$_POST['course_name']:'';
		$pagesize = C('PAGESIZE');
		$condition = '';
		$gsschoolModel = D('Vipschool');
		$dao = $gsschoolModel->dao;
		if($course_name!=''){
			$condition .= " and course_name like '%".SysUtil::safeSearch_vip(urldecode($course_name))."%'";
		}

		$courseList = $gsschoolModel->get_courseList($condition,$curPage,$pagesize);
		$videoNum = $gsschoolModel->get_courseVideoNum($courseList);
		$handoutNum = $gsschoolModel->get_courseHandoutNum($courseList);
		$count = $gsschoolModel->get_courseCount($condition);
		$page = new page($count,$pagesize);
		$showPage = $page->show();
		$this->assign(get_defined_vars());
		$this->display('courseList');
	}
	public function courseInfo(){
		$gsschoolModel = D('Vipschool');
		$courseInfo = $gsschoolModel->get_courseInfo_by_id($_GET['id']);
		$this->assign(get_defined_vars());
		$this->display('courseInfo');
	}
	public function videoInfo(){
		$vid = isset($_GET['vid'])?$_GET['vid']:'';
		if(!empty($vid)){
			$gsschoolModel = D('Vipschool');
			$videoInfo = $gsschoolModel->get_video_by_id($vid);
			$courseInfo = $gsschoolModel->get_courseInfo_by_id($videoInfo['course_id']);
			$this->assign(get_defined_vars());
			$this->display('videoInfo');
		}
	}
	public function handoutInfo(){
		$hid = isset($_GET['hid'])?$_GET['hid']:'';
		if(!empty($hid)){
			$gsschoolModel = D('Vipschool');
			$handoutInfo = $gsschoolModel->get_handout_by_id($hid);
			$courseInfo = $gsschoolModel->get_courseInfo_by_id($handoutInfo['course_id']);
			$this->assign(get_defined_vars());
			$this->display('handoutInfo');
		}
	}
	public function videoManage(){
		$course_id = $_GET['id']?$_GET['id']:'';
		if(!empty($course_id)){
			$gsschoolModel = D('Vipschool');
			$videoList = $gsschoolModel->get_videoList($course_id);
			$handoutList = $gsschoolModel->get_handoutList($course_id);

			$this->assign(get_defined_vars());
			$this->display('videoManage');
		}
	}
	public function updateVideo(){
		$vid = $_GET['vid']?$_GET['vid']:'';
		$course_id = $_GET['course_id']?$_GET['course_id']:'';
		if(!empty($vid) && !empty($course_id)){
			$gsschoolModel = D('Vipschool');
			if($_POST){
				//if($gsschoolModel->checkCCVidIsExist($_POST['cc_vid'],$course_id)){
				//	$this->error('该CC视频ID已存在其他课程中，无法重复添加');
				//}else{
					$result = $gsschoolModel->update_video_by_id($vid,$course_id,$_POST);
					if($result){
						$this->success('修改视频内容成功',U('Vipschool/vipschool_course/videoManage',array('id'=>$course_id)));
					}
				//}

			}else{
				$videoInfo = $gsschoolModel->get_video_by_id($vid);
				$courseInfo = $gsschoolModel->get_courseInfo_by_id($course_id);
				$courseTypeArr = C("COURSE_TYPE_CONF");
				$courseType = 0;
				if(!empty($courseInfo['grade']) && !empty($courseInfo['subject_alias'])){
					foreach($courseTypeArr as $key=>$val){
						if($val['grade'] == $courseInfo['grade'] && $val['subject'] == $courseInfo['subject_alias']){
							$courseType = $val['course_type'];
							break;
						}
					}
				}

				$knowlegeObjectArr = json_decode(file_get_contents("http://klib.api.gaosiedu.com/api/basic/get_knowledges/cid/".$courseType));
				$knowlegeArr = array();
				foreach($knowlegeObjectArr as $key=>$value){
					$knowlegeArr[] = array(
					'kid'=>$value->id,
					'kname'=>$value->name	);
				}

				$this->assign(get_defined_vars());
				$this->display('updateVideo');
			}
		}
	}
	public function updateHandout(){
		$hid = $_GET['hid']?$_GET['hid']:'';
		$course_id = $_GET['course_id']?$_GET['course_id']:'';
		if(!empty($hid) && !empty($course_id)){
			$gsschoolModel = D('Vipschool');
			if($_POST){
				$result = $gsschoolModel->update_handout_by_id($hid,$course_id,$_POST);
				if($result){
					$this->success('修改视频内容成功',U('Vipschool/vipschool_course/videoManage',array('id'=>$course_id)));
				}
			}else{
				$handoutInfo = $gsschoolModel->get_handout_by_id($hid);
				$this->assign(get_defined_vars());
				$this->display('updateHandout');
			}
		}
	}
	public function updateCourse(){
		$id = $_GET['id']?abs($_GET['id']):'';
		if($id){
			$gsschoolModel = D('Vipschool');
			if($_POST){
				if($gsschoolModel->checkCourseIsExist($_POST['course_name'],$id)){
					$this->error('添加课程失败,该课程名已存在，请修改课程名称');
				}else{
					$result = $gsschoolModel->update_course($_POST,$id);
					if($result){
						$this->success('修改课程添加成功',U('Vipschool/vipschool_course/updateCourse',array('id'=>$id)));
					}else{
						$this->error('修改课程失败');
					}
				}

			}else{
				$courseInfo =  $gsschoolModel->get_course_by_id($id);
				$courseInfo['show_course_img'] = $courseInfo['course_img'] != ''?"http://".C('DEFAULT_OSS_HOST')."/".C('BUCKET')."/".$courseInfo['course_img']:'';
				if($courseInfo['sid']){
					$subjectList = $gsschoolModel->get_subject_by_gid($courseInfo['gid']);
				}
				if($courseInfo['cid']){
					$classifyList = $gsschoolModel->get_cate_by_sid($courseInfo['sid']);
				}
				if($courseInfo['cid_two']){
					$twoClassifyList = $gsschoolModel->get_twocate_by_sid($courseInfo['cid']);
				}
				if($courseInfo['cid_three']){
					$threeClassifyList = $gsschoolModel->get_threecate_by_sid($courseInfo['cid_two']);
				}
				if($courseInfo['cid_four']){
					$fourClassifyList = $gsschoolModel->get_fourcate_by_sid($courseInfo['cid_three']);
				}
				if($courseInfo['tid']){
					$teacherList = $gsschoolModel->get_teacher_by_gid_sid($courseInfo['gid'],$courseInfo['sid']);
				}
				$gradeList = $gsschoolModel->get_grade();
				$this->assign(get_defined_vars());
				$this->display('updateCourse');
			}
		}
	}
	public function courseContentManager(){
		$course_id = $_GET['course_id']?$_GET['course_id']:'';
		if(empty($course_id)){
			exit;
		}
		$gsschoolModel = D('Vipschool');
		if($_POST){
			if($_POST['cc_vid']){
				//if($gsschoolModel->checkCCVidIsExist($_POST['cc_vid'],$course_id)){
				//	$this->error('该CC视频ID已存在其他课程中，无法重复添加');
				//}
			}

			if($gsschoolModel->add_video_handout($course_id,$_POST)){
				$this->success('添加成功',U('Vipschool/vipschoolCourse/videoManage',array('id'=>$course_id)));
			}else{
				$this->error('添加失败');
			}


		}else{
			$video = isset($_GET['video']) == 1?1:0;
			$handout = isset($_GET['handout']) == 1?1:0;
			$courseInfo = $gsschoolModel->get_courseInfo_by_id($course_id);
			$courseTypeArr = C("COURSE_TYPE_CONF");
			$courseType = 0;
			if(!empty($courseInfo['grade']) && !empty($courseInfo['subject_alias'])){
				foreach($courseTypeArr as $key=>$val){
					if($val['grade'] == $courseInfo['grade'] && $val['subject'] == $courseInfo['subject_alias']){
						$courseType = $val['course_type'];
						break;
					}
				}
			}

			$knowlegeObjectArr = json_decode(file_get_contents("http://klib.api.gaosiedu.com/api/basic/get_knowledges/cid/".$courseType));
			$knowlegeArr = array();
			foreach($knowlegeObjectArr as $key=>$value){
				$knowlegeArr[] = array(
				'kid'=>$value->id,
				'kname'=>$value->name	);
			}
			$this->assign(get_defined_vars());
			$this->display('courseContentManager');
		}
	}
	public function uploadFile(){
		if (!empty($_FILES)) {
			$targetFolder = UPLOAD_PATH.'vipschool/'.date('Y-m-d').'/';
			if(!file_exists($targetFolder)){
				mkdir($targetFolder);
			}
			$tempFile = $_FILES['Filedata']['tmp_name'];
			$fileParts = pathinfo($_FILES['Filedata']['name']);
			$typeArr = array('doc','docx','pdf');
			$uniqidname = uniqid(mt_rand(),0);
			$newFilename = $uniqidname.".".strtolower($fileParts['extension']);
			$targetFile =$targetFolder.$newFilename ;
			$delUrl = U('Vipschool/VipschoolPage/deleteFile');

			if (in_array(strtolower($fileParts['extension']),$typeArr)) {
				if(move_uploaded_file($tempFile,$targetFile)){
					echo json_encode(array('success'=>'1','status'=>'<font color=#10d509>上传成功</font>','url'=>'/'.end(explode('/eap/','/'.$targetFile)),'del_url'=>$delUrl));
				}else{
					echo json_encode(array('success'=>'0','status'=>'<font color=red>上传失败</font>'));
				}
			} else {
				echo json_encode(array('success'=>'0','status'=>'<font color=red>不支持的文件类型</font>'));
			}
		}
	}

	protected function select_teacher_by_gid_sid(){
		$gid = isset($_POST['grade'])?abs($_POST['grade']):'';
		$sid = isset($_POST['subject'])?abs($_POST['subject']):'';
		if(!empty($gid) && !empty($sid)){
			$gsschoolModel = D('Vipschool');
			$teacherList = $gsschoolModel->get_teacher_by_gid_sid($gid,$sid);
			$returnStr= '';
			if(count($teacherList) > 0){
				$returnStr .= "<option value=\"\">请选择老师</option>";
				foreach($teacherList as $key=>$teacher){
					$returnStr .= "<option value=".$teacher['tid'].">".$teacher['realname']."</option>";
				}
			}else{
				$returnStr .= "<option value=\"\">该学科下暂无老师</option>";
			}
			echo $returnStr;
		}else{
			return false;
		}
	}

	public function courseClassify(){
		$VipschoolModel = D('Vipschool');
		$gradeArr = $VipschoolModel->get_grade();

		$this->assign(get_defined_vars());
		$this->display('courseClassify');
	}
	protected function get_add_form(){
		$type = isset($_GET['type'])?SysUtil::safeString($_GET['type']):'';
		$form_str = '';
		if(!empty($type)){
			$form_str .= '<div class="add_item" style="padding: 30px 40px;"><form id="add_item_form" name="add_item_form" method="POST" action="'.U('Vipschool/vipschool_course/addInfo',array('type'=>$type)).'" onsubmit="return check_add_item(\''.$type.'\')">';
			switch ($type){
				case 'grade':
					$form_str .= '<div><font color=red>*</font>学部名称：<input type="text" name="name" id="name" value="" size="30"><label id=name_msg class=error></label></div><br>';
					$form_str .= '<div><font color=red>*</font>英文全拼：<input type="text" name="alias" id="alias" value="" size="30"><label id=alias_msg class=error></label></div><br>';
					break;
				case 'subject':
					$gradeId = isset($_GET['grade'])?abs($_GET['grade']):'';
					$VipschoolModel = D('Vipschool');
					$gradeList = $VipschoolModel->get_grade();
					if(!empty($gradeList)){
						$form_str .= '<div><font color=red>*</font>学部：<select id="grade" name="grade"><option value="">请选择学部</option>';
						foreach ($gradeList as $key=>$grade){
							$form_str .= '<option value="'.$grade['gid'].'" ';
							$form_str .= ($grade['gid'] == $gradeId)?' selected="true" ':'';
							$form_str .= '>'.$grade['title'].'</option>';
						}
						$form_str .= '</select><label id=attribute_one_msg class=error></label></div><br>';
					}
					$form_str .= '<div><font color=red>*</font>学科名称：<input type="text" name="name" id="name" value="" size="30"><label id=name_msg class=error></label></div><br>';
					$form_str .= '<div><font color=red>*</font>英文全拼名称：<input type="text" name="alias" id="alias" value="" size="30"><label id=alias_msg class=error></label></div><br>';
					$form_str .= '<div><font color=red>*</font>学科别名：<select id="alias2" name="alias2" ><option value="">请选择学科别名</option>';
					$defaultSubject = C('DEFAULT_SUBJECT');
					foreach ($defaultSubject as $k=>$subject){
						$form_str .= '<option value="'.$subject.'">'.$subject.'</option>';
					}
					$form_str .= '</select><label id=alias2_msg class=error></label></div><br>';
					break;
				case 'classify':
					$gradeId = isset($_GET['grade'])?abs($_GET['grade']):'';
					$subjectId = isset($_GET['subject'])?abs($_GET['subject']):'';
					$VipschoolModel = D('Vipschool');
					$gradeList = $VipschoolModel->get_grade();
					if(!empty($gradeList)){
						$form_str .= '<div><font color=red>*</font>学部：<select id="gradeSelect" onchange="course_info_change(\'subject\',\'gradeSelect\',\'subjectSelect\',\'/Vipschool/vipschool_course/select_course_info\')" name="grade"><option value="">请选择学部</option>';
						foreach ($gradeList as $key=>$grade){
							$form_str .= '<option value="'.$grade['gid'].'" ';
							$form_str .= ($grade['gid'] == $gradeId)?' selected="true" ':'';
							$form_str .= '>'.$grade['title'].'</option>';
						}
						$form_str .= '</select><label id=attribute_one_msg class=error></label></div><br>';
					}

					if($gradeId != ''){
						$form_str .= '<div><font color=red>*</font>学科：<select id="subjectSelect" name="subject"><option value="">请选择学科</option>';
						$subjectList = $VipschoolModel->get_subject_by_gid($gradeId);
						foreach($subjectList as $key=>$value){
							$form_str .= '<option value="'.$value['id'].'" ';
							$form_str .= ($value['id'] == $subjectId)?' selected="true" ':'';
							$form_str .= '>'.$value['title'].'</option>';
						}
						$form_str .= '</select><label id=attribute_two_msg class=error></label></div><br>';
					}else{
						$form_str .= '<div><font color=red>*</font>学科：<select id="subjectSelect" name="subject"><option value="">请选择学科</option></select><label id=attribute_two_msg class=error></label></div><br>';
					}
					$form_str .= '<div><font color=red>*</font>分类名称：<input type="text" name="name" id="name" value="" size="30"><label id=name_msg class=error></label></div><br>';
					$form_str .= '<div><font color=red>*</font>关键字：<input type="text" name="alias" id="alias" value="" size="30"><label id=alias_msg class=error></label></div><br>';
					$form_str .= '<div><font color=red>*</font>描述：<textarea type="text" name="des" id="des" value="" colspan="60" rowspan="10"></textarea></div><br>';
					break;
				case 'twoClassify':
					$gradeId = isset($_GET['grade'])?abs($_GET['grade']):'';
					$subjectId = isset($_GET['subject'])?abs($_GET['subject']):'';
					$classifyId = isset($_GET['classify'])?abs($_GET['classify']):'';
					$VipschoolModel = D('Vipschool');
					$gradeList = $VipschoolModel->get_grade();
					if(!empty($gradeList)){
						$form_str .= '<div><font color=red>*</font>学部：<select id="gradeSelect" onchange="course_info_change(\'subject\',\'gradeSelect\',\'subjectSelect\',\'/Vipschool/vipschool_course/select_course_info\')" name="grade"><option value="">请选择学部</option>';
						foreach ($gradeList as $key=>$grade){
							$form_str .= '<option value="'.$grade['gid'].'" ';
							$form_str .= ($grade['gid'] == $gradeId)?' selected="true" ':'';
							$form_str .= '>'.$grade['title'].'</option>';
						}
						$form_str .= '</select><label id=attribute_one_msg class=error></label></div><br>';
					}

					if($gradeId != ''){
						$form_str .= '<div><font color=red>*</font>学科：<select onchange="course_info_change(\'classify\',\'subjectSelect\',\'classifySelect\',\'/Vipschool/vipschool_course/select_course_info\')" id="subjectSelect" name="subject"><option value="">请选择学科</option>';
						$subjectList = $VipschoolModel->get_subject_by_gid($gradeId);
						foreach($subjectList as $key=>$value){
							$form_str .= '<option value="'.$value['id'].'" ';
							$form_str .= ($value['id'] == $subjectId)?' selected="true" ':'';
							$form_str .= '>'.$value['title'].'</option>';
						}
						$form_str .= '</select><label id=attribute_two_msg class=error></label></div><br>';
					}else{
						$form_str .= '<div><font color=red>*</font>学科：<select onchange="course_info_change(\'classify\',\'subjectSelect\',\'classifySelect\',\'/Vipschool/vipschool_course/select_course_info\')" id="subjectSelect" name="subject"><option value="">请选择学科</option></select><label id=attribute_two_msg class=error></label></div><br>';
					}

					if($subjectId != ''){
						$form_str .= '<div><font color=red>*</font>一级分类：<select id="classifySelect" name="classify"><option value="">请选择分类</option>';
						$subjectList = $VipschoolModel->get_cate_by_sid($subjectId);
						foreach($subjectList as $key=>$value){
							$form_str .= '<option value="'.$value['id'].'" ';
							$form_str .= ($value['id'] == $classifyId)?' selected="true" ':'';
							$form_str .= '>'.$value['title'].'</option>';
						}
						$form_str .= '</select><label id=classifySelect_msg class=error></label></div><br>';
					}else{
						$form_str .= '<div><font color=red>*</font>一级分类：<select id="classifySelect" name="classify"><option value="">请选择一级分类</option></select><label id=classifySelect_msg class=error></label></div><br>';
					}

					$form_str .= '<div><font color=red>*</font>分类名称：<input type="text" name="name" id="name" value="" size="30"><label id=name_msg class=error></label></div><br>';
					$form_str .= '<div><font color=red>*</font>关键字：<input type="text" name="alias" id="alias" value="" size="30"><label id=alias_msg class=error></label></div><br>';
					$form_str .= '<div><font color=red>*</font>描述：<textarea type="text" name="des" id="des" value="" colspan="60" rowspan="10"></textarea></div><br>';
					break;
				case 'threeClassify':
					$gradeId = isset($_GET['grade'])?abs($_GET['grade']):'';
					$subjectId = isset($_GET['subject'])?abs($_GET['subject']):'';
					$classifyId = isset($_GET['classify'])?abs($_GET['classify']):'';
					$twoClassifyId = isset($_GET['twoClassify'])?abs($_GET['twoClassify']):'';
					$VipschoolModel = D('Vipschool');
					$gradeList = $VipschoolModel->get_grade();
					if(!empty($gradeList)){
						$form_str .= '<div><font color=red>*</font>学部：<select id="gradeSelect" onchange="course_info_change(\'subject\',\'gradeSelect\',\'subjectSelect\',\'/Vipschool/vipschool_course/select_course_info\')" name="grade"><option value="">请选择学部</option>';
						foreach ($gradeList as $key=>$grade){
							$form_str .= '<option value="'.$grade['gid'].'" ';
							$form_str .= ($grade['gid'] == $gradeId)?' selected="true" ':'';
							$form_str .= '>'.$grade['title'].'</option>';
						}
						$form_str .= '</select><label id=attribute_one_msg class=error></label></div><br>';
					}

					if($gradeId != ''){
						$form_str .= '<div><font color=red>*</font>学科：<select onchange="course_info_change(\'classify\',\'subjectSelect\',\'classifySelect\',\'/Vipschool/vipschool_course/select_course_info\')" id="subjectSelect" name="subject"><option value="">请选择学科</option>';
						$subjectList = $VipschoolModel->get_subject_by_gid($gradeId);
						foreach($subjectList as $key=>$value){
							$form_str .= '<option value="'.$value['id'].'" ';
							$form_str .= ($value['id'] == $subjectId)?' selected="true" ':'';
							$form_str .= '>'.$value['title'].'</option>';
						}
						$form_str .= '</select><label id=attribute_two_msg class=error></label></div><br>';
					}else{
						$form_str .= '<div><font color=red>*</font>学科：<select onchange="course_info_change(\'classify\',\'subjectSelect\',\'classifySelect\',\'/Vipschool/vipschool_course/select_course_info\')" id="subjectSelect" name="subject"><option value="">请选择学科</option></select><label id=attribute_two_msg class=error></label></div><br>';
					}

					if($subjectId != ''){
						$form_str .= '<div><font color=red>*</font>一级分类：<select onchange="course_info_change(\'twoClassify\',\'classifySelect\',\'twoClassifySelect\',\'/Vipschool/vipschool_course/select_course_info\')" id="classifySelect" name="classify"><option value="">请选择分类</option>';
						$subjectList = $VipschoolModel->get_cate_by_sid($subjectId);
						foreach($subjectList as $key=>$value){
							$form_str .= '<option value="'.$value['id'].'" ';
							$form_str .= ($value['id'] == $classifyId)?' selected="true" ':'';
							$form_str .= '>'.$value['title'].'</option>';
						}
						$form_str .= '</select><label id=classifySelect_msg class=error></label></div><br>';
					}else{
						$form_str .= '<div><font color=red>*</font>一级分类：<select onchange="course_info_change(\'twoClassify\',\'classifySelect\',\'twoClassifySelect\',\'/Vipschool/vipschool_course/select_course_info\')" id="classifySelect" name="classify"><option value="">请选择一级分类</option></select><label id=classifySelect_msg class=error></label></div><br>';
					}

					if($classifyId != ''){
						$form_str .= '<div><font color=red>*</font>二级分类：<select id="twoClassifySelect" name="twoClassify"><option value="">请选择分类</option>';
						$classifyList = $VipschoolModel->get_twocate_by_sid($classifyId);
						//echo "<pre>";var_dump($classifyList);var_dump($twoClassifyId);
						foreach($classifyList as $key=>$value){
							$form_str .= '<option value="'.$value['id'].'" ';
							$form_str .= ($value['id'] == $twoClassifyId)?' selected="true" ':'';
							$form_str .= '>'.$value['title'].'</option>';
						}
						$form_str .= '</select><label id=twoClassifySelect_msg class=error></label></div><br>';
					}else{
						$form_str .= '<div><font color=red>*</font>二级分类：<select id="twoClassifySelect" name="classify"><option value="">请选择二级分类</option></select><label id=twoClassifySelect_msg class=error></label></div><br>';
					}

					$form_str .= '<div><font color=red>*</font>分类名称：<input type="text" name="name" id="name" value="" size="30"><label id=name_msg class=error></label></div><br>';
					$form_str .= '<div><font color=red>*</font>关键字：<input type="text" name="alias" id="alias" value="" size="30"><label id=alias_msg class=error></label></div><br>';
					$form_str .= '<div><font color=red>*</font>描述：<textarea type="text" name="des" id="des" value="" colspan="60" rowspan="10"></textarea></div><br>';
					break;
				case 'fourClassify':
					$gradeId = isset($_GET['grade'])?abs($_GET['grade']):'';
					$subjectId = isset($_GET['subject'])?abs($_GET['subject']):'';
					$classifyId = isset($_GET['classify'])?abs($_GET['classify']):'';
					$twoClassifyId = isset($_GET['twoClassify'])?abs($_GET['twoClassify']):'';
					$threeClassifyId = isset($_GET['threeClassify'])?abs($_GET['threeClassify']):'';
					$VipschoolModel = D('Vipschool');
					$gradeList = $VipschoolModel->get_grade();
					if(!empty($gradeList)){
						$form_str .= '<div><font color=red>*</font>学部：<select id="gradeSelect" onchange="course_info_change(\'subject\',\'gradeSelect\',\'subjectSelect\',\'/Vipschool/vipschool_course/select_course_info\')" name="grade"><option value="">请选择学部</option>';
						foreach ($gradeList as $key=>$grade){
							$form_str .= '<option value="'.$grade['gid'].'" ';
							$form_str .= ($grade['gid'] == $gradeId)?' selected="true" ':'';
							$form_str .= '>'.$grade['title'].'</option>';
						}
						$form_str .= '</select><label id=attribute_one_msg class=error></label></div><br>';
					}

					if($gradeId != ''){
						$form_str .= '<div><font color=red>*</font>学科：<select onchange="course_info_change(\'classify\',\'subjectSelect\',\'classifySelect\',\'/Vipschool/vipschool_course/select_course_info\')" id="subjectSelect" name="subject"><option value="">请选择学科</option>';
						$subjectList = $VipschoolModel->get_subject_by_gid($gradeId);
						foreach($subjectList as $key=>$value){
							$form_str .= '<option value="'.$value['id'].'" ';
							$form_str .= ($value['id'] == $subjectId)?' selected="true" ':'';
							$form_str .= '>'.$value['title'].'</option>';
						}
						$form_str .= '</select><label id=attribute_two_msg class=error></label></div><br>';
					}else{
						$form_str .= '<div><font color=red>*</font>学科：<select onchange="course_info_change(\'classify\',\'subjectSelect\',\'classifySelect\',\'/Vipschool/vipschool_course/select_course_info\')" id="subjectSelect" name="subject"><option value="">请选择学科</option></select><label id=attribute_two_msg class=error></label></div><br>';
					}

					if($subjectId != ''){
						$form_str .= '<div><font color=red>*</font>一级分类：<select onchange="course_info_change(\'twoClassify\',\'classifySelect\',\'twoClassifySelect\',\'/Vipschool/vipschool_course/select_course_info\')" id="classifySelect" name="classify"><option value="">请选择分类</option>';
						$subjectList = $VipschoolModel->get_cate_by_sid($subjectId);
						foreach($subjectList as $key=>$value){
							$form_str .= '<option value="'.$value['id'].'" ';
							$form_str .= ($value['id'] == $classifyId)?' selected="true" ':'';
							$form_str .= '>'.$value['title'].'</option>';
						}
						$form_str .= '</select><label id=classifySelect_msg class=error></label></div><br>';
					}else{
						$form_str .= '<div><font color=red>*</font>一级分类：<select onchange="course_info_change(\'twoClassify\',\'classifySelect\',\'twoClassifySelect\',\'/Vipschool/vipschool_course/select_course_info\')" id="classifySelect" name="classify"><option value="">请选择一级分类</option></select><label id=classifySelect_msg class=error></label></div><br>';
					}

					if($classifyId != ''){
						$form_str .= '<div><font color=red>*</font>二级分类：<select onchange="course_info_change(\'threeClassify\',\'twoClassifySelect\',\'threeClassifySelect\',\'/Vipschool/vipschool_course/select_course_info\')" id="twoClassifySelect" name="twoClassify"><option value="">请选择分类</option>';
						$classifyList = $VipschoolModel->get_twocate_by_sid($classifyId);
						//echo "<pre>";var_dump($classifyList);var_dump($twoClassifyId);
						foreach($classifyList as $key=>$value){
							$form_str .= '<option value="'.$value['id'].'" ';
							$form_str .= ($value['id'] == $twoClassifyId)?' selected="true" ':'';
							$form_str .= '>'.$value['title'].'</option>';
						}
						$form_str .= '</select><label id=twoClassifySelect_msg class=error></label></div><br>';
					}else{
						$form_str .= '<div><font color=red>*</font>二级分类：<select onchange="course_info_change(\'threeClassify\',\'twoClassifySelect\',\'threeClassifySelect\',\'/Vipschool/vipschool_course/select_course_info\')" id="twoClassifySelect" name="twoClassify"><option value="">请选择二级分类</option></select><label id=twoClassifySelect_msg class=error></label></div><br>';
					}
					if($twoClassifyId != ''){
						$form_str .= '<div><font color=red>*</font>三级分类：<select id="threeClassifySelect" name="threeClassify"><option value="">请选择三级分类</option>';
						$classifyList = $VipschoolModel->get_twocate_by_sid($twoClassifyId);
						foreach($classifyList as $key=>$value){
							$form_str .= '<option value="'.$value['id'].'" ';
							$form_str .= ($value['id'] == $threeClassifyId)?' selected="true" ':'';
							$form_str .= '>'.$value['title'].'</option>';
						}
						$form_str .= '</select><label id=threeClassifySelect_msg class=error></label></div><br>';
					}else{
						$form_str .= '<div><font color=red>*</font>三级分类：<select id="threeClassifySelect" name="threeClassify"><option value="">请选择三级分类</option></select><label id=threeClassifySelect_msg class=error></label></div><br>';
					}

					$form_str .= '<div><font color=red>*</font>分类名称：<input type="text" name="name" id="name" value="" size="30"><label id=name_msg class=error></label></div><br>';
					$form_str .= '<div><font color=red>*</font>关键字：<input type="text" name="alias" id="alias" value="" size="30"><label id=alias_msg class=error></label></div><br>';
					$form_str .= '<div><font color=red>*</font>描述：<textarea type="text" name="des" id="des" value="" colspan="60" rowspan="10"></textarea></div><br>';
					break;
					break;
			}
			$form_str .= '<input type="submit" name="submit" value="确认添加" class="btn"></form></div>';
		}
		echo $form_str;
	}
	protected function select_course_info(){
		$type = $_POST['type']?$_POST['type']:'';
		if($type == ''){
			exit;
		}
		$vipSchoolModel = D('Vipschool');
		switch($type){
			case 'subject':
				$gradeId = $_POST['grade'];
				$name ='学科';
				$returnArr = $vipSchoolModel->get_subject_by_gid($gradeId);
				break;
			case 'classify':
				$subjectId = $_POST['subject'];
				$name ='分类';
				$returnArr = $vipSchoolModel->get_cate_by_sid($subjectId);
				break;
			case 'twoClassify':
				$classifyId = $_POST['classify'];
				$name ='分类';
				$returnArr = $vipSchoolModel->get_twocate_by_sid($classifyId);
				break;
			case 'threeClassify':
				$classifyId = $_POST['classify'];
				$name ='分类';
				$returnArr = $vipSchoolModel->get_threecate_by_sid($classifyId);
				break;
		}
		$option_str .= '<option value="'.$value['id'].'" >请选择'.$name.'</option>';

		foreach($returnArr as $key=>$value){
			$option_str .= '<option value="'.$value['id'].'" >'.$value['title'].'</option>';
		}
		echo $option_str;
	}
	protected function addInfo(){
		$type = isset($_GET['type'])?SysUtil::safeString($_GET['type']):'';
		switch($type){
			case 'grade';
			$title = '学部';
			break;
			case 'subject';
			$title = '学科';
			break;
			case 'classify';
			$title = '分类';
			break;
			case 'twoClassify';
			$title = '二级分类';
			break;
			case 'threeClassify';
			$title = '三级分类';
			break;
			case 'fourClassify';
			$title = '四级分类';
			break;
		}
		if(D('Vipschool')->addCourseInfo($type,$_POST)){
			$this->success($title.'添加成功',U('vipschool/vipschool_course/courseClassify'));
		}else{
			$this->error($title.'添加失败');
		}

	}

	protected function get_rel_courseinfo(){
		$type = abs($_GET['type']);

		$gsschoolModel = D('Vipschool');
		if($type == 2){
			$gid = $_GET['grade']?$_GET['grade']:'';
			$sid='';
			$radioName = 'subject';
			$selectName='classify';
			$returnArr = $gsschoolModel->get_subject_by_gid($gid);

		}else if($type == 3){
			$sid = $_GET['subject']?$_GET['subject']:'';
			$gid = '';
			$radioName = 'classify';
			$selectName='twoClassify';
			$returnArr = $gsschoolModel->get_cate_by_sid($sid);
		}else if($type == 4){
			$cid = $_GET['classify']?$_GET['classify']:'';
			$gid = '';
			$radioName = 'twoClassify';
			$selectName='threeClassify';

			$returnArr = $gsschoolModel->get_twocate_by_sid($cid);
		}
		else if($type == 5){
			$cid = $_GET['twoClassify']?$_GET['twoClassify']:'';
			$gid = '';
			$radioName = 'threeClassify';
			$selectName='fourClassify';

			$returnArr = $gsschoolModel->get_twocate_by_sid($cid);
		}
		else if($type == 6){
			$cid = $_GET['threeClassify']?$_GET['threeClassify']:'';
			$gid = '';
			$radioName = 'fourClassify';
			$selectName='fourClassify';

			$returnArr = $gsschoolModel->get_threecate_by_sid($cid);
		}
		$returnStr = '';
		foreach($returnArr as $key=>$val){
			if($type == 2){
				$returnStr .= "<input onclick=\"select_course_info('".$selectName."','','".$val['id']."','','#".$selectName."_div','".U('Vipschool/vipschool_course/get_rel_courseinfo')."');change_teacher(this.value,'".U('Vipschool/vipschool_course/get_teacher_option')."')\" type='radio' value='".$val['id']."' id='".$radioName.$val['id']."' name='".$radioName."' title='".$val['title']."'>".$val['title']."&nbsp;&nbsp;&nbsp;&nbsp;";
			}else if($type == 3){
				$returnStr .= "<input onclick=\"select_course_info('".$selectName."','','".$sid."','".$val['id']."','#".$selectName."_div','".U('Vipschool/vipschool_course/get_rel_courseinfo')."')\" type='radio' value='".$val['id']."' id='".$radioName.$val['id']."' name='".$radioName."' title='".$val['title']."'>".$val['title']."&nbsp;&nbsp;&nbsp;&nbsp;";
			}else if($type == 4){
				$returnStr .= "<input onclick=\"select_course_info('".$selectName."','','".$sid."','".$val['id']."','#".$selectName."_div','".U('Vipschool/vipschool_course/get_rel_courseinfo')."')\" type='radio' value='".$val['id']."' id='".$radioName.$val['id']."' name='".$radioName."' title='".$val['title']."'>".$val['title']."&nbsp;&nbsp;&nbsp;&nbsp;";
			}else if($type == 5){
				$returnStr .= "<input onclick=\"select_course_info('".$selectName."','','".$sid."','".$val['id']."','#".$selectName."_div','".U('Vipschool/vipschool_course/get_rel_courseinfo')."')\" type='radio' value='".$val['id']."' id='".$radioName.$val['id']."' name='".$radioName."' title='".$val['title']."'>".$val['title']."&nbsp;&nbsp;&nbsp;&nbsp;";
			}else if($type == 6){
				$returnStr .= "<input type='radio' value='".$val['id']."' id='".$radioName.$val['id']."' name='".$radioName."' title='".$val['title']."'>".$val['title']."&nbsp;&nbsp;&nbsp;&nbsp;";
			}
		}
		echo $returnStr;
	}

	protected function delete_course_category(){
		$return = array('status'=>0);
		$type = $_POST['type']?$_POST['type']:'';
		if($type == ''){
			exit;
		}
		$gsschoolModel = D('Vipschool');
		switch($type){
			case 'grade':
				$msg_type = '学部';
				$returnMsg = $gsschoolModel->delete_grade_by_id($_POST['grade']);
				break;
			case 'subject':
				$msg_type = '学科';
				$returnMsg = $gsschoolModel->delete_subject_by_id($_POST['subject']);
				break;
			case 'classify':
				$msg_type = '分类';
				$returnMsg = $gsschoolModel->delete_classify_by_id($_POST['classify']);
				break;
			case 'twoClassify':
				$msg_type = '分类';
				$returnMsg = $gsschoolModel->delete_twoClassify_by_id($_POST['twoClassify']);
				break;
			case 'threeClassify':
				$msg_type = '分类';
				$returnMsg = $gsschoolModel->delete_threeClassify_by_id($_POST['threeClassify']);
				break;
			case 'fourClassify':
				$msg_type = '分类';
				$returnMsg = $gsschoolModel->delete_fourClassify_by_id($_POST['fourClassify']);
				break;
		}

		if($returnMsg){
			$return['status'] = 1;
			$return['msg'] = $msg_type.'删除成功';
		}else{
			$return['msg'] = $msg_type.'删除失败';
		}
		echo json_encode($return);
	}

	protected function edit_course_category(){
		$gsschoolModel = D('Vipschool');
		switch($_POST['type']){
			case 'grade':
				$typeName = '学部';
				break;
			case 'subject':
				$typeName = '学科';
				break;
			case 'classify':
				$typeName = '分类';
				break;
			case 'twoClassify':
				$typeName = '二级分类';
				break;
			case 'threeClassify':
				$typeName = '三级分类';
				break;
			case 'fourClassify':
				$typeName = '四级分类';
				break;
		}
		if($gsschoolModel->edit_classify($_POST)){
			$this->success($typeName.'名称修改成功', U('vipschool/vipschool_course/courseClassify'));
		}else{
			$this->error($typeName.'名称修改失败');
		}
	}

	public function uploadCourse(){
		if($_POST){
			$gsschoolModel = D('Vipschool');
			if($gsschoolModel->checkCourseIsExist($_POST['course_name'])){
				$this->error('添加课程失败,该课程名已存在，请修改课程名称');
			}else{
				$result = $gsschoolModel->add_course($_POST);
				if($result){
					$this->success('添加课程添加成功',U('Vipschool/vipschoolCourse/courseContentManager',array('course_id'=>$result)));
				}else{
					$this->error('添加课程失败');
				}
			}

		}else{

			$gsschoolModel = D('Vipschool');
			$gradeList = $gsschoolModel->get_grade();
			$this->assign(get_defined_vars());
			$this->display('uploadCourse');
		}
	}
	public function addCoursePack(){
		if($_POST){
			$gsschoolModel = D('Vipschool');
			if($gsschoolModel->checkPackIsExist($_POST['pname'])){
				$this->error('该课程包已存在，请修改课程包名称');
			}else{
				$last_price = ($_POST['coupon_type'] == 0)?$_POST['price']-$_POST['coupon_value'][$_POST['coupon_type']]:$_POST['price']*$_POST['coupon_value'][$_POST['coupon_type']]/100;
				if($last_price<=0){
					$this->error('课程包原价和价格优惠填写不合理，课程包优惠后价格必须大于0');
				}else{

					$result = $gsschoolModel->add_course_pack($_POST);
					if($result){
						$this->success('添加课程添加成功',U('Vipschool/vipschool_course/packCourseManage'));
					}else{
						$this->error('添加课程失败');
					}
				}
			}


		}else{
			$gsschoolModel = D('Vipschool');
			$courseList = $gsschoolModel->get_course_list_order_name();
			$classifyList = $gsschoolModel->get_cate_list();
			$this->assign(get_defined_vars());
			$this->display('addCoursePack');
		}
	}
	public function packCourseManage(){
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pname = isset($_POST['pname'])?$_POST['pname']:'';
		$condition = '';
		if($pname!=''){
			$condition = " and pname like '%$pname%' ";
		}
		$pagesize = C('PAGESIZE');

		$gsschoolModel = D('Vipschool');
		$dao = $gsschoolModel->dao;
		$packList = $gsschoolModel->get_packList($condition,$curPage,$pagesize);
		$count = $gsschoolModel->get_packCount($condition);
		$page = new page($count,$pagesize);
		$showPage = $page->show();

		$this->assign(get_defined_vars());
		$this->display('packCourseManage');
	}
	public function packInfo(){
		$pid = $_GET['pid']?$_GET['pid']:'';
		if(!empty($pid)){
			$gsschoolModel = D('Vipschool');
			$packInfo = $gsschoolModel->get_pack_by_pid($pid);
			$courseList = $gsschoolModel->get_course_list_by_idStr($packInfo['course_id_str']);
			$this->assign(get_defined_vars());
			$this->display('packInfo');
		}

	}
	public function updatePack(){
		$pid = $_GET['pid']?$_GET['pid']:'';
		if(!empty($pid)){
			$gsschoolModel = D('Vipschool');
			if($_POST){
				if($gsschoolModel->checkPackIsExist($_POST['pname'],$_GET['pid'])){
					$this->error('该课程包已存在，请修改课程包名称');
				}else{
					$last_price = ($_POST['coupon_type'] == 0)?$_POST['price']-$_POST['coupon_value'][$_POST['coupon_type']]:$_POST['price']*$_POST['coupon_value'][$_POST['coupon_type']]/100;
					if($last_price<=0){
						$this->error('课程包原价和价格优惠填写不合理，课程包优惠后价格必须大于0');
					}else{
						$result = $gsschoolModel->update_course_pack($_POST,$pid);
						if($result){
							$this->success('修改课程包添加成功',U('Vipschool/vipschool_course/updatePack',array('pid'=>$pid)));
						}else{
							$this->error('添加课程失败');
						}
					}

				}

			}else{
				$packInfo = $gsschoolModel->get_pack_by_pid($pid);
				$selectCourseList = $gsschoolModel->get_course_list_by_idStr($packInfo['course_id_str']);
				$notSelectCourseList = $gsschoolModel->get_course_list_by_idStr($packInfo['course_id_str'],1,$packInfo['cid']);
				$classifyList = $gsschoolModel->get_cate_list();
				$this->assign(get_defined_vars());
				$this->display('updatePack');
			}
		}
	}


	public function changeCourseList(){
		$cid = $_GET['cid'];
		$html = '';
		$courseList = D('Vipschool')->get_course_list_by_cid($cid);
		if(!empty($courseList)){
			foreach ($courseList as $key=>$course){
				$html .= '<option value="'.$course['id'].'">'.$course['course_name'];
				if($course['price']){
					$html .= '&nbsp;&nbsp;-&nbsp;&nbsp;'.$course['price'].'元';
				}
				$html .= '</option>';
			}
		}else{
			$html .= '<option value="">此分类暂无课程</option>';
		}
		echo json_encode(array('html'=>$html));
	}


	public function get_teacher_option(){
		$gid = isset($_POST['grade'])?abs($_POST['grade']):'';
		$sid = isset($_POST['subject'])?abs($_POST['subject']):'';
		if(!empty($gid) && !empty($sid)){
			$gsschoolModel = D('Vipschool');
			$teacherList = $gsschoolModel->get_teacher_by_gid_sid($gid,$sid);
			$returnStr= '';
			if(count($teacherList) > 0){
				$returnStr .= "<option value=\"\">请选择老师</option>";
				foreach($teacherList as $key=>$teacher){
					$returnStr .= "<option value=".$teacher['tid'].">".$teacher['realname']."</option>";
				}
			}else{
				$returnStr .= "<option value=\"\">该学科下暂无老师</option>";
			}
			echo $returnStr;
		}else{
			return false;
		}
	}


	public function deleteVideo(){
		$vid = abs($_GET['vid']);
		if(D('Vipschool')->delete_video($vid)){
			$this->success('视频删除成功');
		}else{
			$this->error('视频删除失败');
		}
	}


	public function deleteHandout(){
		$hid = abs($_GET['hid']);
		if(!empty($hid)){
			$gsschoolModel = D('Vipschool');
			$handoutInfo = $gsschoolModel->get_handout_by_id($hid);
			if(!empty($handoutInfo)){
				if($gsschoolModel->delete_handout($hid)){
					if(!empty($handoutInfo['handout_url'])){
						//删除讲义文件
						VipschoolPageAction::delete_oss_object(C('BUCKET'),$handoutInfo['handout_url']);
					}
					$this->success('讲义删除成功');
				}else{
					$this->error('讲义删除失败');
				}
			}
		}else{
			$this->error('非法操作');
		}
	}
	
}
?>