<?php
/*课程管理*/
class VipHandoutsAction extends VipCommAction{
	/*课程讲义管理*/
	public function main(){
		$list_style = isset($_GET['style'])?trim($_GET['style']):C('DEFAULT_LISTSTYLE');
		$userInfo = $this->loginUser->getInformation();
		$is_jiaoyan = $this->checkUserRole();
		$is_jianzhi = $this->checkUserType();
		$is_admin = VipCommAction::checkIsAdmin();
		$handouts_type = 0;
		$handouts_subject = isset($_GET['subject'])?intval($_GET['subject']):'';
		$handouts_grade = isset($_GET['grade'])?intval($_GET['grade']):'';
		$starttime = isset($_GET['starttime'])?trim($_GET['starttime']):'';
		$endtime = isset($_GET['endtime'])?trim($_GET['endtime']):'';
		$canBack = isset($_GET['canBack'])?trim($_GET['canBack']):'';
		$vipGradeModel = D('VpGrade');
		if(!empty($handouts_grade)){
			$handouts_grade_name = $vipGradeModel->get_gradename_by_gid($handouts_grade);
		}
		$vipKnowledgeModel = D('VpKnowledge');
		$handouts_knowledge = isset($_GET['knowledge'])?intval($_GET['knowledge']):0;
		if(!empty($handouts_knowledge)){
			$handouts_knowledge_name = $vipKnowledgeModel->get_knowledgename_by_kid($handouts_knowledge);
		}
		$handouts_keyword = isset($_GET['keyword'])?$_GET['keyword']:'';
		$userKey = $this->loginUser->getUserKey();
		$vipSubjectModel = D('VpSubject');
		$subjectArr = $vipSubjectModel->get_subjectList($handouts_type,$userKey);
		if(!empty($handouts_subject)){
			$gradeArr = $vipGradeModel->get_gradeList_by_subjectid($handouts_subject);
		}else{
			$gradeArr = $vipGradeModel->get_gradeList($handouts_type,$userKey);
		}
		if(!empty($handouts_subject) && !empty($handouts_grade)){
			$knowledgeIdArr = $vipKnowledgeModel->get_knowledgeList_by_gradeid_and_subjectid(array('sid'=>$handouts_subject,'gid'=>$handouts_grade,'is_jiaoyan'=>$is_jiaoyan));
			$knowledgeIdArr = $this->unique_arr($knowledgeIdArr,'kid');
		}
		if(!empty($knowledgeIdArr)){
			$knowledgeArr = array();
			foreach ($knowledgeIdArr as $key =>$knowledgeId){
				if($is_jiaoyan == 1 || $knowledgeId['permission'] == '0'){
					$knowledgeArr[$key]['kid'] = $knowledgeId['kid'];
					$knowledgeArr[$key]['name'] =  $vipKnowledgeModel->get_knowledgename_by_kid($knowledgeId['kid']);
				}
			}
		}
		$handouts_nianji = isset($_GET['nid'])?$_GET['nid']:'';
		$nianjiArr = C('GRADES');

		$vipHandoutsModel = D('VpHandouts');
		$dao = $vipHandoutsModel->dao;
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = ($list_style=='list')?C('PAGESIZE_LIST'):C('PAGESIZE');
		$condition = " is_delete = '0' AND is_teaching_and_research = '1' AND [type]= '$handouts_type' AND [status] = '1' ";
		if(!$is_admin){
			$thisUserSidsStr = $this->get_thisuser_subjectIdStr($userKey);
			$condition .= " AND sid IN ($thisUserSidsStr)";
			if($is_jianzhi){
				$condition .= " AND is_parttime_visible = '1'";
			}
		}
		if(!empty($handouts_subject)){
			$condition .= " AND sid = '$handouts_subject' ";
		}
		if(!empty($handouts_grade)){
			$condition .= " AND gid = '$handouts_grade' ";
		}
		if(!empty($handouts_knowledge)){
			$condition .= " AND kid = '$handouts_knowledge' ";
		}
		if(!empty($handouts_nianji)){
			$condition .= " AND nids LIKE '%,$handouts_nianji,%' ";
		}
		if(!empty($_GET['keyword'])){
			$condition .= ' AND title like '.$dao->quote('%' . SysUtil::safeSearch_vip(urldecode($_GET['keyword'])) . '%');
		}
		if(!empty($starttime)){
			$condition .= " AND instime >= '".strtotime($starttime.' 00:00:00')."'";
		}
		if(!empty($endtime)){
			$condition .= " AND instime <= '".strtotime($endtime.' 23:59:59')."'";
		}

		$condition = $this->filter_permission($condition,array('sid'=>$handouts_subject,'gid'=>$handouts_grade,'kid'=>$handouts_knowledge),$is_jiaoyan);//权限筛选
		$handoutsList = $vipHandoutsModel->get_handoutsList($condition,$curPage,$pagesize);
		$handoutsList = $this->deal_file_url($handoutsList);
		$sgkid = '';
		foreach($handoutsList as $key=>$value){
			$sgkid .= $value['sid'].$value['gid'].$value['kid'].",";
		}
		$courseuserArray = $vipKnowledgeModel->get_allcourseuser_by_handout(trim($sgkid,','));
		$courseuserAllArray = array();
		foreach($courseuserArray as $key=>$value){
			$courseuserAllArray[$value['p']] = $value['courseuser'];
		}
		foreach($handoutsList as $key=>$value){
			$handoutsList[$key]['courseuser'] = $courseuserAllArray[$value['sid'].$value['gid'].$value['kid']]?$courseuserAllArray[$value['sid'].$value['gid'].$value['kid']]:'暂无课程用途说明';
		}
		$count = $vipHandoutsModel->get_handoutsCount($condition);
		$page = new Page($count,$pagesize);
		$showPage = $page->show();

		$this->assign(get_defined_vars());
		$this->display();
	}

	/*试题库管理*/
	public function test_paper(){
		$list_style = isset($_GET['style'])?trim($_GET['style']):C('DEFAULT_LISTSTYLE');
		$userInfo = $this->loginUser->getInformation();
		$is_jiaoyan = $this->checkUserRole();
		$is_jianzhi = $this->checkUserType();
		$is_admin = VipCommAction::checkIsAdmin();
		$userKey = $this->loginUser->getUserKey();
		$handouts_type = 1;
		$handouts_subject = isset($_GET['subject'])?intval($_GET['subject']):0;
		$handouts_grade = isset($_GET['grade'])?intval($_GET['grade']):0;
		$starttime = isset($_GET['starttime'])?trim($_GET['starttime']):'';
		$endtime = isset($_GET['endtime'])?trim($_GET['endtime']):'';
		$canBack = isset($_GET['canBack'])?trim($_GET['canBack']):'';
		$vipGradeModel = D('VpGrade');
		if(!empty($handouts_grade)){
			$handouts_grade_name = $vipGradeModel->get_gradename_by_gid($handouts_grade);
		}
		$handouts_knowledge = isset($_GET['knowledge'])?intval($_GET['knowledge']):0;
		$vipKnowledgeModel = D('VpKnowledge');
		if(!empty($handouts_knowledge)){
			$handouts_knowledge_name = $vipKnowledgeModel->get_knowledgename_by_kid($handouts_knowledge);
		}
		$handouts_keyword = isset($_GET['keyword'])?$_GET['keyword']:'';
		$vipSubjectModel = D('VpSubject');
		$subjectArr = $vipSubjectModel->get_subjectList($handouts_type,$userKey);

		if(!empty($handouts_subject)){
			$gradeArr = $vipGradeModel->get_gradeList_by_subjectid($handouts_subject);
		}else{
			$gradeArr = $vipGradeModel->get_gradeList($handouts_type,$userKey);
		}
		if(!empty($handouts_subject) && !empty($handouts_grade)){
			$knowledgeIdArr = $vipKnowledgeModel->get_knowledgeList_by_gradeid_and_subjectid(array('sid'=>$handouts_subject,'gid'=>$handouts_grade,'is_jiaoyan'=>$is_jiaoyan));
			$knowledgeIdArr = $this->unique_arr($knowledgeIdArr,'kid');
		}
		if(!empty($knowledgeIdArr)){
			$knowledgeArr = array();
			foreach ($knowledgeIdArr as $key =>$knowledgeId){
				if($is_jiaoyan == 1 || $knowledgeId['permission'] == '0'){
					$knowledgeArr[$key]['kid'] = $knowledgeId['kid'];
					$knowledgeArr[$key]['name'] =  $vipKnowledgeModel->get_knowledgename_by_kid($knowledgeId['kid']);
				}
			}
		}
		$handouts_nianji = isset($_GET['nid'])?$_GET['nid']:'';
		$nianjiArr = C('GRADES');

		$vipHandoutsModel = D('VpHandouts');
		$dao = $vipHandoutsModel->dao;
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = ($list_style=='list')?C('PAGESIZE_LIST'):C('PAGESIZE');
		$condition = " is_delete = '0' AND is_teaching_and_research = '1' AND [type]= '$handouts_type' AND [status] = '1' ";
		if(!$is_admin){
			$thisUserSidsStr = $this->get_thisuser_subjectIdStr($userKey);
			$condition .= " AND sid IN ($thisUserSidsStr)";
			if($is_jianzhi){
				$condition .= " AND is_parttime_visible = '1'";
			}
		}
		if(!empty($handouts_subject)){
			$condition .= " AND sid = '$handouts_subject' ";
		}
		if(!empty($handouts_grade)){
			$condition .= " AND gid = '$handouts_grade' ";
		}
		if(!empty($handouts_knowledge)){
			$condition .= " AND kid = '$handouts_knowledge' ";
		}
		if(!empty($handouts_nianji)){
			$condition .= " AND nids LIKE '%,$handouts_nianji,%' ";
		}
		if(!empty($_GET['keyword'])){
			$condition .= ' AND title like '.$dao->quote('%' . SysUtil::safeSearch_vip(urldecode($_GET['keyword'])) . '%');
		}
		if(!empty($starttime)){
			$condition .= " AND instime >= '".strtotime($starttime.' 00:00:00')."'";
		}
		if(!empty($endtime)){
			$condition .= " AND instime <= '".strtotime($endtime.' 23:59:59')."'";
		}
		$condition = $this->filter_permission($condition,array('sid'=>$handouts_subject,'gid'=>$handouts_grade),$is_jiaoyan);//权限筛选
		//echo $condition;
		$handoutsList = $vipHandoutsModel->get_handoutsList($condition,$curPage,$pagesize);
		$handoutsList = $this->deal_file_url($handoutsList);
		$count = $vipHandoutsModel->get_handoutsCount($condition);
		$page = new Page($count,$pagesize);
		$showPage = $page->show();

		$this->assign(get_defined_vars());
		$this->display();
	}


	public function filter_permission($condition,$arr,$is_jiaoyan){
		if($is_jiaoyan == 0){
			$vipKnowledgeModel = D('VpKnowledge');
			$subQuery = $vipKnowledgeModel->get_permission_subquery($arr);
			if(!empty($subQuery)){
				$condition .= " AND [hid] NOT IN ($subQuery) ";
			}
		}
		return $condition;
	}


	/*共享课程*/
	public function share_handouts(){
		$list_style = isset($_GET['style'])?trim($_GET['style']):C('DEFAULT_LISTSTYLE');
		$userInfo = $this->loginUser->getInformation();
		$is_jiaoyan = $this->checkUserRole();
		$vipHandoutsModel = D('VpHandouts');
		$dao = $vipHandoutsModel->dao;
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = ($list_style=='list')?C('PAGESIZE_LIST'):C('PAGESIZE');
		$condition = " is_delete = '0' AND status = '1' AND is_teaching_and_research = '0' AND is_share = '1' ";
		if(!empty($_GET['keyword'])){
			$condition .= ' AND title like '.$dao->quote('%' . SysUtil::safeSearch_vip(urldecode($_GET['keyword'])) . '%');
		}
		$handouts_keyword = $_GET['keyword'];
		$handoutsList = $vipHandoutsModel->get_handoutsList($condition,$curPage,$pagesize);
		$handoutsList = $this->deal_file_url($handoutsList);
		$count = $vipHandoutsModel->get_handoutsCount($condition);
		$page = new Page($count,$pagesize);
		$showPage = $page->show();

		$this->assign(get_defined_vars());
		$this->display();
	}



	/*我的课程*/
	public function my_handouts(){
		$list_style = isset($_GET['style'])?trim($_GET['style']):C('DEFAULT_LISTSTYLE');
		$vipHandoutsModel = D('VpHandouts');
		$dao = $vipHandoutsModel->dao;
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = ($list_style=='list')?C('PAGESIZE_LIST'):C('PAGESIZE');
		$this_user_key = $this->loginUser->getUserKey();
		$condition = " is_delete = '0' AND status = '1' AND user_key = '$this_user_key' AND is_teaching_and_research='0' ";//权限筛选
		if(!empty($_GET['keyword'])){
			$condition .= ' AND title like '.$dao->quote('%' . SysUtil::safeSearch_vip(urldecode($_GET['keyword'])) . '%');
		}
		$handouts_keyword = $_GET['keyword'];
		//echo $condition;
		$handoutsList = $vipHandoutsModel->get_handoutsList($condition,$curPage,$pagesize);
		$handoutsList = $this->deal_file_url($handoutsList);
		$count = $vipHandoutsModel->get_handoutsCount($condition);
		$page = new Page($count,$pagesize);
		$showPage = $page->show();

		$this->assign(get_defined_vars());
		$this->display();
	}


	/*教师版讲义在线预览*/
	public function view_handouts_pdf(){
		$hid = isset($_GET['hid'])?intval($_GET['hid']):0;
		$type = isset($_GET['type'])?trim($_GET['type']):'teacher';
		if(!empty($hid)){
			$vipHandoutsModel = D('VpHandouts');
			$handoutsInfo = $vipHandoutsModel->get_handoutsInfo_by_hid($hid);
			//$source_url = ($type=='teacher')?$handoutsInfo['teacher_version_preview']:$handoutsInfo['student_version_preview'];
			if($type=='teacher'){
				$source_url = !empty($handoutsInfo['teacher_version_preview'])?$handoutsInfo['teacher_version_preview']:str_replace(end(explode('.',$handoutsInfo['teacher_version'])),'swf',$handoutsInfo['teacher_version']);
			}else{
				$source_url = !empty($handoutsInfo['student_version_preview'])?$handoutsInfo['student_version_preview']:str_replace(end(explode('.',$handoutsInfo['student_version'])),'swf',$handoutsInfo['student_version']);
			}
			$swf_url = APP_DIR.$source_url;
			$is_exists = 1;
			if(!file_exists($swf_url)){
				$is_exists = 0;
			}
			$swf_url = strtolower(end(explode('/eap',$swf_url)));
		}
		
		$this->assign(get_defined_vars());
		$this->display();
	}


	/*课程共享*/
	public function do_share_handouts(){
		$hid = isset($_GET['hid'])?intval($_GET['hid']):0;
		$type = isset($_GET['type'])?SysUtil::safeString($_GET['type']):'';
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		if(!empty($hid) && !empty($type)){
			$share_status = 1;
			$msg_pre = '';
			if($type == 'cancel'){
				$share_status = 0;
				$msg_pre = '取消';
			}
			$vipHandoutsModel = D('VpHandouts');
			if($vipHandoutsModel->do_share_handouts($hid,$share_status)){
				$this->success($msg_pre.'讲义共享成功',U('Vip/VipHandouts/my_handouts',array('p'=>$curPage,'style'=>$_GET['style'])));
			}else{
				$this->error($msg_pre.'讲义共享失败');
			}
		}else{
			$this->error('非法操作');
		}
	}


	/*删除讲义*/
	public function delete_handouts(){
		$hid = isset($_GET['hid'])?intval($_GET['hid']):0;
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$returnAction = isset($_GET['returnAction'])?$_GET['returnAction']:'';
		$returnFunction = isset($_GET['returnFunction'])?$_GET['returnFunction']:'';
		if(!empty($hid)){
			$vipHandoutsModel = D('VpHandouts');
			if($vipHandoutsModel->delete_handouts_by_hid($hid)){
				if(!empty($returnUrl)){
					$this->success('讲义删除成功',U('Vip/'.$returnAction.'/'.$returnFunction,array('p'=>$curPage,'style'=>$_GET['style'])));
				}else{
					$this->success('讲义删除成功',U('Vip/'.$returnAction.'/'.$returnFunction,array('p'=>$curPage,'style'=>$_GET['style'])));
				}
			}else{
				$this->error('讲义删除失败');
			}
		}else{
			$this->error('非法操作');
		}
	}


	/*查看讲义信息*/
	public function show_detail_handouts(){
		$hid = isset($_GET['hid'])?intval($_GET['hid']):0;
		$return_str = '';
		if(!empty($hid)){
			$vipHandoutsModel = D('VpHandouts');
			$handoutsInfo = $vipHandoutsModel->get_handoutsInfo_by_hid($hid);
			$vipSubjectModel = D('VpSubject');
			$handoutsInfo['subject_name'] = $vipSubjectModel->get_subjectname_by_sid($handoutsInfo['sid']);
			$vipGradeModel = D('VpGrade');
			$handoutsInfo['grades_name'] = $vipGradeModel->get_gradenames_by_gids($handoutsInfo['gid']);
			$vipKnowledgeModel = D('VpKnowledge');
			$handoutsInfo['knowledge_name'] = $vipKnowledgeModel->get_knowledgename_by_kid($handoutsInfo['kid']);
			$handoutsInfo['nianjis_name'] = '';
			if(!empty($handoutsInfo['nids'])){
				$nianjiArr = C('GRADES');
				foreach (explode(',',trim($handoutsInfo['nids'],',')) as $key=>$nid){
					$handoutsInfo['nianjis_name'] .= $nianjiArr[$nid].',';
				}
			}else{
				$handoutsInfo['nianjis_name'] .= '无';
			}
			//课程的用途
			$handoutsInfo['courseuser'] = $vipKnowledgeModel->get_courseuser_by_handout($handoutsInfo['sid'],$handoutsInfo['gid'],$handoutsInfo['kid']);
			$handoutsInfo['courseuser'] = $handoutsInfo['courseuser']?$handoutsInfo['courseuser']:'无';
			$handoutsInfo['owner'] = D('Users')->get_userRealName_by_userKey($handoutsInfo['user_key']);
			$handoutsInfo['is_parttime_visible'] = ($handoutsInfo['is_parttime_visible']==1)?'是':'否';
			$HANDOUTS_TYPE = C('HANDOUTS_TYPE');
			$return_str .= '<div class="mwTitle">'.$handoutsInfo['title'].'</div><div class="mwContent">
							<table width="100%" border="0" cellpadding="0" cellspacing="0" class="mwTable">
								<tr valign="top"><td class="alt">讲义类型:</td><td>'.$HANDOUTS_TYPE[$handoutsInfo['type']].'</td></tr>';
			if($handoutsInfo['is_teaching_and_research']==1){
				$show_gname = '课程属性';
				$show_kname = '讲义属性';
				if($handoutsInfo['type'] == 1){
					$show_gname = '题库属性';
					$show_kname = '试题属性';
				}
				$return_str .= '<tr valign="top"><td class="alt">科目:</td><td>'.$handoutsInfo['subject_name'].'</td></tr>
								<tr valign="top"><td class="alt">'.$show_gname.':</td><td>'.$handoutsInfo['grades_name'].'</td></tr>
								<tr valign="top"><td class="alt">'.$show_kname.':</td><td>'.$handoutsInfo['knowledge_name'].'</td></tr>
								<tr valign="top"><td class="alt">年级:</td><td>'.$handoutsInfo['nianjis_name'].'</td></tr>
								<tr valign="top"><td class="alt">是否兼职教师可见:</td><td>'.$handoutsInfo['is_parttime_visible'].'</td></tr>';
			}
			$return_str .= '<tr valign="top"><td class="alt">讲义介绍:</td><td>'.$handoutsInfo['introduce'].'</td></tr>
							<tr valign="top"><td class="alt">课程用途:</td><td>'.$handoutsInfo['courseuser'].'</td></tr>
							<tr valign="top"><td class="alt">上传人:</td><td>'.$handoutsInfo['owner'].'</td></tr>
							<tr valign="top"><td class="alt">上传时间:</td><td>'.date('Y-m-d H:i:s',$handoutsInfo['instime']).'</td></tr>
							<tr valign="top"><td class="alt">文档下载:</td><td>';
			$download_url = U('Vip/VipHandouts/download',array('hid'=>$handoutsInfo['hid'],'type'=>$handoutsInfo['type']));
			if($handoutsInfo['type'] == 0){
				$handoutsInfo['type'] = ($handoutsInfo['is_teaching_and_research']==0)?1:0;
				$return_str .= '<a href="'.U('Vip/VipHandouts/download',array('hid'=>$handoutsInfo['hid'],'type'=>$handoutsInfo['type'])).'" class="orange">';
				$return_str .= ($handoutsInfo['is_teaching_and_research']==1)?'下载学生版讲义</a>':'下载讲义</a>';
			}else{
				if(!empty($handoutsInfo['student_version'])){
					$return_str .= '<a href="'.U('Vip/VipHandouts/download',array('hid'=>$handoutsInfo['hid'],'type'=>$handoutsInfo['type'])).'" class="orange">下载教师版试题</a>&nbsp;&nbsp;&nbsp;
									<a href="'.U('Vip/VipHandouts/download',array('hid'=>$handoutsInfo['hid'],'type'=>$handoutsInfo['type'],'ntype'=>'student')).'" class="orange">下载学生版试题</a>';
				}else{
					$return_str .= '<a href="'.U('Vip/VipHandouts/download',array('hid'=>$handoutsInfo['hid'],'type'=>$handoutsInfo['type'])).'" class="orange">下载试题</a>&nbsp;&nbsp;&nbsp;';
				}
			}
			$return_str .= '</td><tr valign="top"><td class="alt"></td><td>';
			if($handoutsInfo['user_key'] == $this->loginUser->getUserKey() && $_GET['is_modify']){
				if($handoutsInfo['is_teaching_and_research']==1){
					$url = ($handoutsInfo['type']==1)?U('Vip/VipJiaoyan/add_itembank',array('hid'=>$hid)):U('Vip/VipJiaoyan/add_handouts',array('hid'=>$hid));
				}else{
					$url = U('Vip/VipHandouts/add_handouts',array('hid'=>$hid));
				}
				$return_str .= '<input type=button value="修改" class="btn" onclick="javascript:window.location.href=\''.$url.'\';"></a>';
			}
			$return_str .= '</td></tr></table></div>';
		}else{
			$return_str .= '非法操作，无法获取讲义信息';
		}
		echo $return_str;
	}


	/*上传讲义*/
	public function add_handouts(){
		$permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey());
		$hid = isset($_REQUEST['hid'])?intval($_REQUEST['hid']):0;
		if($_POST){
			$handoutsInfo = array();
			$handoutsInfo['type'] = isset($_POST['type'])?intval($_POST['type']):0;
			$handoutsInfo['title'] = isset($_POST['title'])?SysUtil::safeString($_POST['title']):'';
			$handoutsInfo['picture'] = isset($_POST['picture'])?SysUtil::safeString($_POST['picture']):'';
			$handoutsInfo['sid'] = 0;
			$handoutsInfo['gid'] = 0;
			$handoutsInfo['kid'] = 0;
			$handoutsInfo['nids'] = NULL;
			$handoutsInfo['is_parttime_visible'] = 1;
			$handoutsInfo['introduce']= isset($_POST['introduce'])?str_replace("\r\n","<br>",str_replace(" ","&nbsp;",SysUtil::safeString($_POST['introduce']))):'';
			$handoutsInfo['teacher_version']= isset($_POST['teacher_version'])?SysUtil::safeString($_POST['teacher_version']):'';
			$handoutsInfo['teacher_version_preview']= isset($_POST['teacher_version_preview'])?SysUtil::safeString($_POST['teacher_version_preview']):'';
			$handoutsInfo['IP'] = $this->getClientIp();
			$returnUrl = U('Vip/VipHandouts/add_handouts',array('title'=>$_POST['title'],'picture'=>str_replace('/','__',$_POST['picture']),'introduce'=>$_POST['introduce'],'teacher_version'=>str_replace('/','__',reset(explode('.',$_POST['teacher_version']))),'teacher_version_type'=>end(explode('.',$_POST['teacher_version'])),'teacher_version_preview'=>str_replace('/','__',reset(explode('.',$_POST['teacher_version_preview'])))));
			if(!empty($handoutsInfo['title']) && !empty($handoutsInfo['teacher_version'])){
				$vipHandoutsModel = D('VpHandouts');
				$dao = $vipHandoutsModel->dao;
				if($_POST['action'] == 'insert' && $vipHandoutsModel->get_handoutsCount(" is_delete = 0 AND title = ".$dao->quote($handoutsInfo['title']))){
					$this->error('该讲义标题已存在，讲义上传失败',$returnUrl);
				}
				$handoutsInfo['user_key'] = $this->loginUser->getUserKey();
				$userInfo = $this->loginUser->getInformation();
				$handoutsInfo['is_teaching_and_research'] = 0;
				$handoutsInfo['status'] = 1;
				if($_POST['action'] == 'update'){
					if($vipHandoutsModel->update_handouts($handoutsInfo,$hid)){
						$this->success('讲义信息修改成功',U('Vip/VipHandouts/add_handouts',array('hid'=>$hid)));
					}else{
						$this->error('讲义信息修改失败',$returnUrl);
					}
				}else if($_POST['action'] == 'insert'){
					if($newHid = $vipHandoutsModel->add_handouts($handoutsInfo)){
						$this->success('讲义添加成功',U('Vip/VipHandouts/add_handouts',array('hid'=>$newHid)));
					}else{
						$this->error('讲义添加失败',$returnUrl);
					}
				}
			}else{
				$this->error('请填写完整的讲义信息',$returnUrl);
			}
		}else{
			$handoutsType = C('HANDOUTS_TYPE');
			$vipSubjectModel = D('VpSubject');
			$subjectArr = $vipSubjectModel->get_subjectList();

			$vipGradeModel = D('VpGrade');
			$gradeArr = $vipGradeModel->get_gradeList();

			$vipHandoutsModel = D('VpHandouts');
			if(!empty($hid)){
				$handoutsInfo = $vipHandoutsModel->get_handoutsInfo_by_hid($hid);
				if(!empty($handoutsInfo)){
					$handoutsInfo['picture_show'] = end(explode('eap',str_replace('Upload/','upload/',$handoutsInfo['picture'])));
					$handoutsInfo['teacher_version_show'] = end(explode('eap',str_replace('Upload/','upload/',$handoutsInfo['teacher_version'])));
					$handoutsInfo['introduce'] = str_replace("<br>","\r\n",str_replace("&nbsp;"," ",$handoutsInfo['introduce']));
					$vipKnowledgeModel = D('VpKnowledge');
					$handoutsInfo['knowledge_name'] = $vipKnowledgeModel->get_knowledgename_by_kid($handoutsInfo['kid']);
				}
			}else{
				$handoutsInfo = VipCommAction::getCacheHandoutsInfo();
			}
			$sessionKey = md5($this->loginUser->getUserKey());
			$this->assign(get_defined_vars());
			$this->display();
		}
	}

	/**
	 * 获取科目列表
	 * @return [type] [description]
	 */
	protected function get_grade_option()
	{
		$id=isset($_GET['id'])?intval($_GET['id']):0;
		$userInfo = VipCommAction::get_currentUserInfo();
		$studentsModel = D('VpStudents');
		if($userInfo['user_key'] == 'Employee-wangyan'){
			$userInfo['user_key'] = 'Employee-guoluping';
			$userInfo['sCode'] = 'VP00022';
		}
		$vipBasic=D('Basic');
		$dictSubjectRow=$vipBasic->getDictDataByID('SUBJECT',$id);
		$vipSubjectModel = D('VpSubject');
		$ntype = isset($_GET['ntype'])?intval($_GET['ntype']):$vipSubjectModel->get_subjectType_by_sid($dictSubjectRow['eap_subject_id']);
		$ntype_name = ($ntype==0)?'科目':'题库';
		$return_type = isset($_GET['return_type'])?SysUtil::safeString($_GET['return_type']):'checkbox';
		if($return_type == 'checkbox'){
			$grades_option_str = '';
		}else{
			$grades_option_str = '<option value="">请选择'.$ntype_name.'属性</option>';
		}
		if(!empty($id)){
			$gradeList = $vipSubjectModel->get_subjectLists($id,$userInfo['user_key']);
		}
		if(!empty($gradeList)){
			foreach ($gradeList as $key=>$grade){
				if($return_type == 'select'){
					$grades_option_str .= '<option value="'.$grade['id'].'">'.$grade['title'].'</option>';
				}else{
					$grades_option_str .= '<input type=checkbox id=grade_'.$grade['id'].' name=grade value="'.$grade['id'].'" onclick="get_knowledge_options(this.value)">'.$grade['title'].'&nbsp;&nbsp;&nbsp;&nbsp;';
				}
			}
		}
		echo $grades_option_str;
	}

	/**
	 * 获取教材列表
	 * @return [type] [description]
	 */
	protected function get_course_one_option()
	{
		$id=isset($_GET['id'])?intval($_GET['id']):0;
		$vipBasic=D('Basic');
		$dictSubjectRow=$vipBasic->getDictDataByID('SUBJECT',$id);
		$vipSubjectModel = D('VpSubject');
		$ntype = isset($_GET['ntype'])?intval($_GET['ntype']):$vipSubjectModel->get_subjectType_by_sid($dictSubjectRow['eap_subject_id']);
		$ntype_name = ($ntype==0)?'教材':'题库';
		$return_type = isset($_GET['return_type'])?SysUtil::safeString($_GET['return_type']):'checkbox';
		if($return_type == 'checkbox'){
			$grades_option_str = '';
		}else{
			$grades_option_str = '<option value="">请选择'.$ntype_name.'属性</option>';
		}
		/*
		$vipGradeModel = D('VpGrade');
		if(!empty($sid)){
			$gradeList = $vipGradeModel->get_gradeList_by_subjectid($sid);
		}else{
			$gradeList = $vipGradeModel->get_gradeList();
		}*/
		if (!empty($id)) {
			$gradeList=$vipBasic->getKnowledgeTypes(array('is_gaosi'=>1,'subjectid'=>$id));
		}
		if(!empty($gradeList)){
			foreach ($gradeList as $key=>$grade){
				if($return_type == 'select'){
					$grades_option_str .= '<option value="'.$grade['id'].'">'.$grade['title'].'</option>';
				}else{
					$grades_option_str .= '<input type=checkbox id=grade_'.$grade['id'].' name=grade value="'.$grade['id'].'" onclick="get_knowledge_options(this.value)">'.$grade['title'].'&nbsp;&nbsp;&nbsp;&nbsp;';
				}
			}
		}
		echo $grades_option_str;

	}

	/**
	 * 获取课程属性列表
	 * @return [type] [description]
	 */
	public function get_knowledge_type_option()
	{
		//接收素材id
		$id = isset($_GET['id'])?intval($_GET['id']):0;
		//接收科目id
		$subject_id = isset($_GET['subject_id'])?intval($_GET['subject_id']):0;
		$type = isset($_GET['type'])?$_GET['type']:'list';
		$vipBasic=D('Basic');
		$dictSubjectRow=$vipBasic->getDictDataByID('SUBJECT',$subject_id);
		$vipSubjectModel = D('VpSubject');
		$ntype = isset($_GET['ntype'])?intval($_GET['ntype']):$vipSubjectModel->get_subjectType_by_sid($dictSubjectRow['eap_subject_id']);
		$ntype_name = ($ntype==0)?'课程':'题库';
		$return_type = isset($_GET['return_type'])?SysUtil::safeString($_GET['return_type']):'checkbox';
		if($return_type == 'checkbox'){
			$grades_option_str = '';
		}else{
			$grades_option_str = '<option value="">请选择'.$ntype_name.'属性</option>';
		}
		if (!empty($id) && !empty($subject_id)) {
			$gradeList=$vipBasic->getCourseTypesByKonwledge($id , $subject_id);
		}
		if(!empty($gradeList)){
			foreach ($gradeList as $key=>$grade){
				if($return_type == 'select'){
					$grades_option_str .= '<option value="'.$grade['id'].'">'.$grade['title'].'</option>';
				}else{
					$grades_option_str .= '<input type=checkbox id=grade_'.$grade['id'].' name=grade value="'.$grade['id'].'" onclick="get_knowledge_options(this.value)">'.$grade['title'].'&nbsp;&nbsp;&nbsp;&nbsp;';
				}
			}
		}
		echo $grades_option_str;
	}

	/**
	 * 获取讲义列表
	 * @return [type] [description]
	 */
	public function get_knowledge_options(){
		$gid = isset($_GET['gid'])?trim($_GET['gid'],','):0;
		$sid = isset($_GET['sid'])?intval($_GET['sid']):0;
		$type = isset($_GET['type'])?$_GET['type']:'list';
		$vipBasic=D('Basic');
		$dictSubjectRow=$vipBasic->getDictDataByID('SUBJECT',$id);
		$vipSubjectModel = D('VpSubject');
		$ntype = isset($_GET['ntype'])?intval($_GET['ntype']):$vipSubjectModel->get_subjectType_by_sid($dictSubjectRow['eap_subject_id']);
		$ntype_name = ($ntype==0)?'讲义':'试题';
		$knowledge_option_str = '<option value="">请选择'.$ntype_name.'属性</option>';
		if(!empty($gid) && !empty($sid)){
			$is_jiaoyan = $this->checkUserRole();
			if($type == 'add'){
				$is_jiaoyan = 1;
			}
			$knowledgeList = $vipBasic->getCourseTypesBySubjectAll ( $gid );

			if(!empty($knowledgeList)){
				foreach ($knowledgeList as $key=>$knowledge){
					//if(($knowledge['permission'] == 0 || ($knowledge['permission'] == 1 && $is_jiaoyan == 1)) || $type == 'add'){
					if (!empty($knowledge['id'])) {
						$knowledge_option_str .= '<option value="'.$knowledge['id'].'">'.$knowledge['name'].'</option>';
					}
					
					//}
				}
			}
		}
		echo $knowledge_option_str;
	}


	/********** 之前筛选  **************/

	protected  function get_subject_option(){
		$ntype = isset($_GET['type'])?$_GET['type']:'';
		$ntype_name = '科目';
		$ntype_name_grade = ($ntype == 2)?'题库':'课程';
		$return_type = isset($_GET['return_type'])?SysUtil::safeString($_GET['return_type']):'checkbox';
		if($return_type == 'checkbox'){
			$subjects_option_str = '';
			$grades_option_str = '';
		}else{
			$subjects_option_str = '<option value="">请选择'.$ntype_name.'</option>';
			$grades_option_str = '<option value="">请选择'.$ntype_name_grade.'属性</option>';
		}
		if($ntype != 3){
			$ntype = !empty($ntype)?intval($ntype)-1:$ntype;
			$vipSubjectModel = D('VpSubject');
			$subjectList = $vipSubjectModel->get_subjectList($ntype);
			if(!empty($subjectList)){
				foreach ($subjectList as $key=>$subject){
					if($return_type == 'select'){
						$subjects_option_str .= '<option value="'.$subject['sid'].'">'.$subject['name'].'</option>';
					}else{
						$subjects_option_str .= '<input type=checkbox id=subject_'.$subject['sid'].' name=subject value="'.$subject['sid'].'" onclick="get_grades_option(this.value)">'.$grade['name'].'&nbsp;&nbsp;&nbsp;&nbsp;';
					}
				}
			}
			$vipGradeModel = D('VpGrade');
			$gradeList = $vipGradeModel->get_gradeList($ntype);
			if(!empty($gradeList)){
				foreach ($gradeList as $key=>$grade){
					if($return_type == 'select'){
						$grades_option_str .= '<option value="'.$grade['gid'].'">'.$grade['name'].'</option>';
					}else{
						$grades_option_str .= '<input type=checkbox id=grade_'.$grade['gid'].' name=grade value="'.$grade['gid'].'" onclick="get_knowledge_option(this.value)">'.$grade['name'].'&nbsp;&nbsp;&nbsp;&nbsp;';
					}
				}
			}
		}
		echo json_encode(array('subjectHtml'=>$subjects_option_str,'gradeHtml'=>$grades_option_str));
	}

	protected  function get_grades_option(){
		$sid = isset($_GET['sid'])?intval($_GET['sid']):0;
		$vipSubjectModel = D('VpSubject');
		$ntype = isset($_GET['ntype'])?intval($_GET['ntype']):$vipSubjectModel->get_subjectType_by_sid($sid);
		$ntype_name = ($ntype==0)?'课程':'题库';
		$return_type = isset($_GET['return_type'])?SysUtil::safeString($_GET['return_type']):'checkbox';
		if($return_type == 'checkbox'){
			$grades_option_str = '';
		}else{
			$grades_option_str = '<option value="">请选择'.$ntype_name.'属性</option>';
		}
		$vipGradeModel = D('VpGrade');
		if(!empty($sid)){
			$gradeList = $vipGradeModel->get_gradeList_by_subjectid($sid);
		}else{
			$gradeList = $vipGradeModel->get_gradeList();
		}
		if(!empty($gradeList)){
			foreach ($gradeList as $key=>$grade){
				if($return_type == 'select'){
					$grades_option_str .= '<option value="'.$grade['gid'].'">'.$grade['name'].'</option>';
				}else{
					$grades_option_str .= '<input type=checkbox id=grade_'.$grade['gid'].' name=grade value="'.$grade['gid'].'" onclick="get_knowledge_option(this.value)">'.$grade['name'].'&nbsp;&nbsp;&nbsp;&nbsp;';
				}
			}
		}
		echo $grades_option_str;
	}


	public function get_knowledge_option(){
		$gid = isset($_GET['gid'])?trim($_GET['gid'],','):0;
		$sid = isset($_GET['sid'])?intval($_GET['sid']):0;
		$type = isset($_GET['type'])?$_GET['type']:'list';
		$vipSubjectModel = D('VpSubject');
		$ntype = isset($_GET['ntype'])?intval($_GET['ntype']):$vipSubjectModel->get_subjectType_by_sid($sid);
		$ntype_name = ($ntype==0)?'讲义':'试题';
		$knowledge_option_str = '<option value="">请选择'.$ntype_name.'属性</option>';
		if(!empty($gid) && !empty($sid)){
			$is_jiaoyan = $this->checkUserRole();
			if($type == 'add'){
				$is_jiaoyan = 1;
			}
			$vipKnowledgeModel = D('VpKnowledge');
			$knowledgeList = $vipKnowledgeModel->get_knowledgeList_by_gradeid_and_subjectid(array('sid'=>$sid,'gid'=>$gid,'is_jiaoyan'=>$is_jiaoyan));
			if(!empty($knowledgeList)){
				$knowledgeList = $this->unique_arr($knowledgeList,'kid');
				foreach ($knowledgeList as $key=>$knowledge){
					$this_name = $vipKnowledgeModel->get_knowledgename_by_kid($knowledge['kid']);
					//if(($knowledge['permission'] == 0 || ($knowledge['permission'] == 1 && $is_jiaoyan == 1)) || $type == 'add'){
					$knowledge_option_str .= '<option value="'.$knowledge['kid'].'">'.$this_name.'</option>';
					//}
				}
			}
		}
		echo $knowledge_option_str;
	}


	protected function get_nianji_option(){
		$gid = isset($_GET['gid'])?trim($_GET['gid'],','):0;
		$sid = isset($_GET['sid'])?intval($_GET['sid']):0;
		$kid = isset($_GET['kid'])?intval($_GET['kid']):0;
		$nianji_option_str = '';
		if(!empty($gid) && !empty($sid) && !empty($kid)){
			$nianjiArr = C('GRADES');
			$nianjiStr =D('VpKnowledge')->get_nianjiList(array('sid'=>$sid,'gid'=>$gid,'kid'=>$kid));
			if(!empty($nianjiStr)){
				foreach (explode(',',trim($nianjiStr,',')) as $key=>$nid){
					$nianji_option_str .= '<input type="checkbox" id="nianji_'.$nid.'" name="nianji[]" value="'.$nid.'">'.$nianjiArr[$nid].'&nbsp;&nbsp;';
				}
			}
		}
		echo $nianji_option_str;
	}


	public function change_grades_view(){
		$subject_id = isset($_GET['subjectid'])?intval($_GET['subjectid']):0;
		$kdiv = isset($_GET['kdiv'])?$_GET['kdiv']:'';
		if(!empty($subject_id)){
			//获取相应课程属性信息
			$vipGradeModel = D('VpGrade');
			$gradeList = $vipGradeModel->get_gradeList_by_subjectid($subject_id);
			$grade_str = '';
			if(!empty($gradeList)){
				foreach ($gradeList as $key=>$grade){
					$grade_str .= '<input type="checkbox" name="grade[]" id="grade_'.$grade['gid'].'" value="'.$grade['gid'].'" title="'.$grade['name'].'"';
					if(!empty($kdiv)){
						$grade_str .= ' onclick="select_knowledge(this.value,\'#knowledge_div\')" ';
					}
					$grade_str .= '>'.$grade['name'].'&nbsp;&nbsp;';
				}
				$grade_str .= '<label id=grade_id_msg class=error></label>';
			}

			//获取讲义属性信息
			$vipKnowledgeModel = D('VpKnowledge');
			$knowledge_permissionList = $vipKnowledgeModel->get_knowledgeList_by_subjectid($subject_id);
			if(!empty($knowledge_permissionList)){
				foreach ($knowledge_permissionList as $key => $permission){
					$knowledgeList[] = $permission['kid'];
				}
				$knowledgeList = array_unique($knowledgeList);
				$knowledge_str = '';
				if(!empty($knowledgeList)){
					foreach ($knowledgeList as $key=>$knowledge){
						$temp_knowledge_name = $vipKnowledgeModel->get_knowledgename_by_kid($knowledge);
						$knowledge_str .= '<input type="checkbox" name="knowledge[]" id="knowledge_'.$knowledge.'" value="'.$knowledge.'" title="'.$temp_knowledge_name.'">'.$temp_knowledge_name.'&nbsp;&nbsp;';
					}
				}
			}
		}
		echo json_encode(array('grade_str'=>$grade_str,'knowledge_str'=>$knowledge_str));
	}


	public function change_knowledge_view(){
		$grade_str = isset($_GET['grade_str'])?trim($_GET['grade_str']):'';
		$sid = isset($_GET['sid'])?trim($_GET['sid']):'';
		$knowledge_str = '';
		if(!empty($grade_str)){
			$vipKnowledgeModel = D('VpKnowledge');
			$knowledge_permissionList = $vipKnowledgeModel->get_knowledgeList_by_gradeids_and_subjectid("'".implode("','",explode(',',$grade_str))."'",$sid);
			if(!empty($knowledge_permissionList)){
				foreach ($knowledge_permissionList as $key => $permission){
					$knowledgeList[] = $permission['kid'];
				}
				$knowledgeList = array_unique($knowledgeList);
				$knowledge_str = '';
				if(!empty($knowledgeList)){
					foreach ($knowledgeList as $key=>$knowledge){
						$temp_knowledge_name = $vipKnowledgeModel->get_knowledgename_by_kid($knowledge);
						$knowledge_str .= '<input type="checkbox" name="knowledge[]" id="knowledge_'.$knowledge.'" value="'.$knowledge.'" title="'.$temp_knowledge_name.'">'.$temp_knowledge_name.'&nbsp;&nbsp;';
					}
				}
			}
		}
		echo $knowledge_str;
	}


	/*讲义下载*/
	protected function download(){
		$hid = isset($_GET['hid'])?intval($_GET['hid']):'';
		$type = isset($_GET['type'])?intval($_GET['type']):0;
		$ntype = isset($_GET['ntype'])?trim($_GET['ntype']):'';
		if(!empty($hid)){
			$vipHandoutsModel = D('VpHandouts');
			//判断当前用户是否可下载
			$userModel = D('Users');
			$user_key = $this->loginUser->getUserKey();
			$limit_role_id = $userModel->get_roleId(array('roleName'=>'VIP普通教师','app_name'=>APP_NAME,'group_name'=>'Vip'));
			$is_common_teacher = $userModel->get_userInfoFromUserRoles(array('role_id'=>$limit_role_id,'user_key'=>$user_key));
			
			//VIP普通教研员-限制下载次数
			if(empty($is_common_teacher)){
				$limit_role_id = $userModel->get_roleId(array('roleName'=>'VIP普通教研员','app_name'=>APP_NAME,'group_name'=>'Vip'));
				$is_common_teacher = $userModel->get_userInfoFromUserRoles(array('role_id'=>$limit_role_id,'user_key'=>$user_key));
			}
			
			$is_admin = VipCommAction::checkIsRealAdmin();
			if(!$is_admin){
				$download_count_today = $vipHandoutsModel->get_downloadNum(array('user_key'=>$user_key,'starttime'=>date('Y-m-d').' 00:00:00','distinct'=>1));
				if($download_count_today >= C('DOWNLOAD_LIMIT')){
					$this->error('您今日下次次数已达上限，明天再来吧！');
				}
			}
			$handoutInfo = $vipHandoutsModel->get_handoutsInfo_by_hid($hid);
			if(!empty($ntype)){
				$source_url = $handoutInfo['student_version'];
			}else{
				$source_url = ($type == 1)?$handoutInfo['teacher_version']:$handoutInfo['student_version'];
				$source_url = (empty($source_url) && $type == 1)?$handoutInfo['student_version']:$source_url;
			}
			if(!empty($source_url)){
				$targetFolder = UPLOAD_PATH.'temp/';
				if(!file_exists($targetFolder)){
					mkdir($targetFolder,0777);
				}
				if(strpos($_SERVER["HTTP_USER_AGENT"],"MSIE")) $handoutInfo['title'] = urlencode($handoutInfo['title']);
				if($handoutInfo['is_rename'] == 1){
				//	$toDownloadFile = str_replace("'","",str_replace(' ','_',end(explode('/',$source_url))));
					$toDownloadFile = str_replace("'","",str_replace(' ','_',$handoutInfo['title'].'.'.end(explode('.',$source_url))));
				}else{
					$toDownloadFile = str_replace("'","",str_replace(' ','_',$handoutInfo['title'].'.'.end(explode('.',$source_url))));
				}
				if(file_exists(APP_DIR.$source_url)){
					if($vipHandoutsModel->add_downloadLog(array('user_key'=>$this->loginUser->getUserKey(),'hid'=>$hid,'htype'=>$type,'download_time'=>date('Y-m-d H:i:s'),'IP'=>$this->getClientIp()))){
						VipCommAction::download_file(APP_DIR.$source_url,$toDownloadFile);
					}else{
						$this->error('下载失败');
					}
				}else{
					$this->error('文件不存在');
				}
			}else{
				$this->error('文件不存在');
			}
		}else{
			$this->error('非法操作');
		}
	}

	/*添加收藏*/
	public function do_favorite(){
		$action = isset($_GET['act'])?trim($_GET['act']):'';
		if(empty($action)){
			$this->error('非法操作');
		}
		$hid = isset($_GET['hid'])?intval($_GET['hid']):0;
		$user_key = $this->loginUser->getUserKey();
		$vipHandoutsModel = D('VpHandouts');
		if($action == 'add'){
			$htype = isset($_GET['type'])?intval($_GET['type']):'';
			if($vipHandoutsModel->get_favorite_info(array('hid'=>$hid,'user_key'=>$user_key))){
				$htype_name = ($htype == 1)?'试题库':'课程讲义';
				$this->error('您已经收藏过此'.$htype_name.'，不能重复收藏');
			}else{
				if($vipHandoutsModel->add_favorite(array('hid'=>$hid,'htype'=>$htype,'user_key'=>$user_key))){
					$this->success('收藏成功');
				}else{
					$this->error('收藏失败');
				}
			}
		}else{
			$fid = isset($_GET['fid'])?intval($_GET['fid']):0;
			if($vipHandoutsModel->delete_favorite(array('fid'=>$fid,'user_key'=>$user_key))){
				$this->success('取消收藏成功');
			}else{
				$this->error('取消收藏失败');
			}
		}
	}
	
	
	/*我的下载历史记录*/
	public function my_download(){
		$user_key = $this->loginUser->getUserKey();
		$handoutsType = C('HANDOUTS_TYPE');
		$vipHandoutsModel = D('VpHandouts');
		$dao = $vipHandoutsModel->dao;

		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$condition = " AND d.[user_key] = '$user_key'";
		if(isset($_GET['type']) && $_GET['type']!==''){
			$condition .= " AND d.[htype] = '$_GET[type]' ";
		}
		if(!empty($_GET['keyword'])){
			$condition .= ' AND h.[title] like '.$dao->quote('%' . SysUtil::safeSearch_vip($_GET['keyword']) . '%');
		}
		$myDownloadList = $vipHandoutsModel->get_teacherUploadOrDownloadList($condition,$curPage,$pagesize,'download');
		$count = $vipHandoutsModel->get_teacherUploadOrDownloadCount($condition,'download');
		$page = new page($count,$pagesize);
		$showPage = $page->show();
		$htype = isset($_GET['type'])?$_GET['type']:'';
		$keyword = isset($_GET['keyword'])?$_GET['keyword']:'';

		$this->assign(get_defined_vars());
		$this->display();
	}

}

?>
