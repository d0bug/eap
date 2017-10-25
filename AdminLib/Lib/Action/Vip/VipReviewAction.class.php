<?php
/*审核管理*/
class VipReviewAction extends VipCommAction{
	/*文档审核*/
	public function documentReview(){
		$userInfo = VipCommAction::get_currentUserInfo();
		$permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey());
		$userKey = $this->loginUser->getUserKey();
		$is_jiaoyan = $this->checkUserRole();
		$is_jianzhi = $this->checkUserType();
		$is_admin = VipCommAction::checkIsAdmin();
		
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$type = isset($_REQUEST['type'])?intval($_REQUEST['type']):'';
		$handouts_subject = isset($_REQUEST['subject'])?intval($_REQUEST['subject']):0;
		$handouts_grade = isset($_REQUEST['grade'])?intval($_REQUEST['grade']):0;
		$vipGradeModel = D('VpGrade');
		if(!empty($handouts_grade)){
			$handouts_grade_name = $vipGradeModel->get_gradename_by_gid($handouts_grade);
		}

		$vipKnowledgeModel = D('VpKnowledge');
		$handouts_knowledge = isset($_REQUEST['knowledge'])?intval($_REQUEST['knowledge']):0;
		if(!empty($handouts_knowledge)){
			$handouts_knowledge_name = $vipKnowledgeModel->get_knowledgename_by_kid($handouts_knowledge);
		}

		$vipSubjectModel = D('VpSubject');
		$subjectArr = $vipSubjectModel->get_subjectList('',$userKey);
		if(!empty($handouts_subject)){
			$gradeArr = $vipGradeModel->get_gradeList_by_subjectid($handouts_subject);
		}else{
			$gradeArr = $vipGradeModel->get_gradeList('',$userKey);
		}
		if(!empty($handouts_subject) && !empty($handouts_grade)){
			$knowledgeIdArr = $vipKnowledgeModel->get_knowledgeList_by_gradeid_and_subjectid(array('sid'=>$handouts_subject,'gid'=>$handouts_grade,'is_jiaoyan'=>$is_jiaoyan));
			$knowledgeIdArr = $this->unique_arr($knowledgeIdArr,'kid');
		}

		$nianjiArr = C('GRADES');
		$handouts_nianji = isset($_REQUEST['nianji'])?intval($_REQUEST['nianji']):0;
		$handouts_nianji_name = $nianjiArr[$handouts_nianji];

		$keyword = isset($_REQUEST['keyword'])?trim($_REQUEST['keyword']):'';
		if(!empty($knowledgeIdArr)){
			$knowledgeArr = array();
			foreach ($knowledgeIdArr as $key =>$knowledgeId){
				if($is_jiaoyan == 1 || $knowledgeId['permission'] == '0'){
					$knowledgeArr[$key]['kid'] = $knowledgeId['kid'];
					$knowledgeArr[$key]['name'] =  $vipKnowledgeModel->get_knowledgename_by_kid($knowledgeId['kid']);
				}
			}
		}

		$vipHandoutsModel = D('VpHandouts');
		$dao = $vipHandoutsModel->dao;
		$condition = " is_delete = '0' AND is_teaching_and_research = '1' ";
		if(!$is_admin){
			$thisUserSidsStr = $this->get_thisuser_subjectIdStr($userKey);
			$condition .= " AND sid IN ($thisUserSidsStr)";
			if($is_jianzhi){
				$condition .= " AND is_parttime_visible = '1'";
			}
		}
		if(!empty($type)){
			$condition .= " AND type = '".($type-1)."' ";
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
		if(!empty($keyword) && $keyword!='输入课程名称'){
			$condition .= ' AND title like '.$dao->quote('%' . SysUtil::safeSearch_vip(urldecode($keyword)) . '%');
		}
		//echo $condition;
		$handoutsList = $vipHandoutsModel->get_handoutsList($condition,$curPage,$pagesize,' ORDER BY status ASC,instime DESC');
		$handoutsList = $this->deal_file_url($handoutsList);
		if(!empty($handoutsList)){
			foreach ($handoutsList as $key =>$handouts){
				$handoutsList[$key]['user_realname'] = D('Users')->get_userRealName_by_userKey($handouts['user_key']);
				$handoutsList[$key]['sname'] = D('VpSubject')->get_subjectname_by_sid($handouts['sid']);
				$handoutsList[$key]['gname'] = D('VpGrade')->get_gradename_by_gid($handouts['gid']);
				$handoutsList[$key]['kname'] = $vipKnowledgeModel->get_knowledgename_by_kid($handouts['kid']);
				$handoutsList[$key]['nnames'] = '';
				if(!empty($handouts['nids'])){
					$nianjiArr = C('GRADES');
					foreach (explode(',',trim($handouts['nids'],',')) as $k=>$nid){
						$handoutsList[$key]['nnames'] .= $nianjiArr[$nid].',';
					}
				}
			}
		}
		$count = $vipHandoutsModel->get_handoutsCount($condition);
		$page = new Page($count,$pagesize);
		$showPage = $page->show();
		$this->assign(get_defined_vars());
		$this->display("documentReview");
	}

	public function reviewHandouts(){
		$hid = isset($_GET['hid'])?intval($_GET['hid']):0;
		$status = isset($_GET['status'])?intval($_GET['status']):0;
		$source_type = isset($_GET['source_type'])?intval($_GET['source_type']):0;
		$title = isset($_GET['title'])?trim($_GET['title']):'';
		if(!empty($hid)){
			$userInfo = $this->loginUser->getInformation();
			if(D('VpHandouts')->reviewHandout($hid,$status,$userInfo['real_name'])){
				$message = '您的';
				switch ($source_type){
					case 0:
						$message.='课程讲义';
						break;
					case 1:
						$message.='试题库资料';
						break;
				}
				$message.= '<font class="blue">《'.$title.'》</font> 未通过审核，请及时修改~ ';
				D('VpHandouts')->add_message(array('user_key'=>trim($_GET['user_key']),'type'=>$source_type,'source_id'=>$hid,'message'=>$message,'instime'=>date('Y-m-d H:i:s')));
				echo '1';
			}else{
				echo '0';
			}
		}else{
			echo '0';
		}
		die;
	}

	/*网络宣传信息审核*/
	public function publicityReview(){
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$vpPublicityModel = D('VpPublicity');
		$dao = $vpPublicityModel->dao;
		$condition = " [status] = '0'";
		$user_name = trim($_POST['user_name']);
		if(!empty($user_name)){
			$condition .= ' AND [user_key] LIKE '.$dao->quote('%-' . SysUtil::safeSearch($_POST['user_name']) );
		}
		$publicityList = $vpPublicityModel->get_publicity_list($condition,$curPage,$pagesize);
		$count = $vpPublicityModel->get_publicity_count($condition);
		$page = new Page($count,$pagesize);
		$showPage = $page->show();

		$this->assign(get_defined_vars());
		$this->display("publicityReview");
	}
	

	public function do_publicityReview(){
		$permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey());
		$is_admin = VipCommAction::checkIsAdmin();
		$is_jiaoyan = $this->checkUserRole();
		$userPublicityInfo = array();
		$thisLoginUserKey = $this->loginUser->getUserKey();
		$user_name = SysUtil::safeString($_POST['user_name']);
		if($user_name){
			$userKey = D('Users')->get_userKey_by_username($user_name);
		}else{
			$userKey = trim($_GET['user_key']);
		}
		$vippublicityModel = D('VpPublicity');
		if($userKey){
			$rankArr = C('RANK');
			$gradesArr = C('GRADES');
			$subjectArr = C('SUBJECT');
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
		}
		
		//获取授课校区、授课风格、教师资质
		$schoolList = $vippublicityModel->getVipSchoolList();
		$educationList = $vippublicityModel->getVipEducationList();
		$styleList = $vippublicityModel->getVipStyleList();
		
		$this->assign(get_defined_vars());
		$this->display();
	}
	/*删除讲义*/
	protected function delete_handouts(){
		$hid = isset($_GET['hid'])?intval($_GET['hid']):0;
		$user_key = isset($_GET['user_key'])?$_GET['user_key']:'';
		$source_type = isset($_GET['source_type'])?$_GET['source_type']:'';
		$title = isset($_GET['title'])?$_GET['title']:'';
		$real_name = isset($_GET['real_name'])?$_GET['real_name']:'';
		
		if(!empty($hid)){
			$vipHandoutsModel = D('VpHandouts');
			if($vipHandoutsModel->delete_handouts_by_hid($hid)){
				$message = '您的';
				switch ($source_type){
					case 0:
						$message.='课程讲义';
						break;
					case 1:
						$message.='试题库资料';
						break;
				}
				$message .= '<font class="blue">《'.$title.'》</font>由于特殊原因已被'.$real_name.'老师删除，请确定~';
				if($vipHandoutsModel->add_message(array('user_key'=>trim($user_key),'type'=>$source_type,'source_id'=>$hid,'message'=>$message,'instime'=>date('Y-m-d H:i:s'),'is_delete'=>'1'))){
					echo 1;
				}
			}else{
				echo  0;
			}
		}else{
			echo  0; 
		}			
				
			
	}
}

?>
