<?php
import('COM.Acl.Role');
import('ORG.Crypt.Xxtea');
/*教研管理*/
class VipJiaoyanAction extends VipCommAction{
	
	protected function notNeedLogin() {
		return array('VIP-VIPJIAOYAN-SENDMAIL');
	}
	/*课程讲义属性管理*/
	public function handouts_attribute(){
		$type = 0;
		$permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey());
		$permissionList = isset($_GET['is_permissionList'])?$_GET['is_permissionList']:'';
		$is_jiaoyan = $this->checkUserRole();
		if($is_jiaoyan!=1){
			echo '<font size=3>抱歉，您无权限进行此操作</font>';exit;
		}
		$vipSubjectModel = D('VpSubject');
		$subjectArr = $vipSubjectModel->get_subjectList($type);

		$vipGradeModel = D('VpGrade');
		$gradeArr = $vipGradeModel->get_gradeList($type);

		$vipKnowledgeModel = D('VpKnowledge');
		$knowledgeArr = $vipKnowledgeModel->get_knowledgeList($type);

		$nianjiArr = C('GRADES');
		$permissionArr = C('KNOWLEDGE_PERMISSION');

		$permission_condition = ' WHERE s.type = '.$type;
		if(!empty($_GET['sid'])){
			$permission_condition .= " AND p.[sid] = '".intval($_GET['sid'])."'";
		}
		if(!empty($_GET['gid'])){
			$permission_condition .= " AND p.[gid] = '".intval($_GET['gid'])."'";
		}
		if(!empty($_GET['kid'])){
			$permission_condition .= " AND p.[kid] = '".intval($_GET['kid'])."'";
		}
		if(!empty($_GET['nid'])){
			$permission_condition .= " AND p.[nids] LIKE '%,".intval($_GET['nid']).",%'";
		}
		$knowledgePermissionArr = $vipKnowledgeModel->get_knowledgePermissionList($permission_condition);
		if(!empty($knowledgePermissionArr)){
			foreach ($knowledgePermissionArr as $key =>$permission){
				$knowledgePermissionArr[$key]['sname'] = $vipSubjectModel->get_subjectname_by_sid($permission['sid']);
				$knowledgePermissionArr[$key]['gname'] = $vipGradeModel->get_gradename_by_gid($permission['gid']);
				$knowledgePermissionArr[$key]['kname'] = $vipKnowledgeModel->get_knowledgename_by_kid($permission['kid']);
				$knowledgePermissionArr[$key]['nnames'] = '';
				if(!empty($permission['nids'])){
					foreach (explode(',',trim($permission['nids'],',')) as $k =>$nid){
						$knowledgePermissionArr[$key]['nnames'] .= $nianjiArr[$nid].',';
					}
				}else{
					$knowledgePermissionArr[$key]['nnames'] .= '无';
				}
			}
		}
		$this->assign(get_defined_vars());
		$this->display();
	}


	/*试题库属性管理*/
	public function itembank_attribute(){
		$type = 1;
		$permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey());
		$permissionList = isset($_GET['is_permissionList'])?$_GET['is_permissionList']:'';
		$is_jiaoyan = $this->checkUserRole();
		if($is_jiaoyan!=1){
			echo '<font size=3>抱歉，您无权限进行此操作</font>';exit;
		}
		$vipSubjectModel = D('VpSubject');
		$subjectArr = $vipSubjectModel->get_subjectList($type);
		$vipGradeModel = D('VpGrade');
		$gradeArr = $vipGradeModel->get_gradeList($type);
		$vipKnowledgeModel = D('VpKnowledge');
		$knowledgeArr = $vipKnowledgeModel->get_knowledgeList($type);
		$nianjiArr = C('GRADES');
		$permissionArr = C('KNOWLEDGE_PERMISSION');
		$permission_condition = ' WHERE s.type = '.$type;
		if(!empty($_GET['sid'])){
			$permission_condition .= " AND p.[sid] = '".intval($_GET['sid'])."'";
		}
		if(!empty($_GET['gid'])){
			$permission_condition .= " AND p.[gid] = '".intval($_GET['gid'])."'";
		}
		if(!empty($_GET['kid'])){
			$permission_condition .= " AND p.[kid] = '".intval($_GET['kid'])."'";
		}
		if(!empty($_GET['nid'])){
			$permission_condition .= " AND p.[nids] LIKE '%,".intval($_GET['nid']).",%'";
		}
		$knowledgePermissionArr = $vipKnowledgeModel->get_knowledgePermissionList($permission_condition);
		if(!empty($knowledgePermissionArr)){
			foreach ($knowledgePermissionArr as $key =>$permission){
				$knowledgePermissionArr[$key]['sname'] = $vipSubjectModel->get_subjectname_by_sid($permission['sid']);
				$knowledgePermissionArr[$key]['gname'] = $vipGradeModel->get_gradename_by_gid($permission['gid']);
				$knowledgePermissionArr[$key]['kname'] = $vipKnowledgeModel->get_knowledgename_by_kid($permission['kid']);
				$knowledgePermissionArr[$key]['nnames'] = '';
				if(!empty($permission['nids'])){
					foreach (explode(',',trim($permission['nids'],',')) as $k =>$nid){
						$knowledgePermissionArr[$key]['nnames'] .= $nianjiArr[$nid].',';
					}
				}else{
					$knowledgePermissionArr[$key]['nnames'] .= '无';
				}
			}
		}

		$this->assign(get_defined_vars());
		$this->display();
	}

	/*上传讲义*/
	public function add_handouts(){
		$permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey());
		$is_jiaoyan = $this->checkUserRole();
		if($is_jiaoyan!=1){
			echo '<font size=3>抱歉，您无权限进行此操作</font>';exit;
		}
		$hid = isset($_REQUEST['hid'])?intval($_REQUEST['hid']):0;
		if($_POST){
			$handoutsInfo = array();
			$handoutsInfo['type'] = isset($_POST['type'])?intval($_POST['type']):0;
			$handoutsInfo['title'] = isset($_POST['title'])?SysUtil::safeString($_POST['title']):'';
			$handoutsInfo['picture'] = isset($_POST['picture'])?SysUtil::safeString($_POST['picture']):'';
			$handoutsInfo['sid'] = isset($_POST['subject'])?intval($_POST['subject']):0;
			$handoutsInfo['gid'] = isset($_POST['grade'])?intval($_POST['grade']):'';
			$handoutsInfo['kid'] = isset($_POST['knowledge'])?intval($_POST['knowledge']):0;
			$handoutsInfo['nids'] = isset($_POST['nianji'])?','.implode(',',$_POST['nianji']).',':'';
			$handoutsInfo['is_parttime_visible'] = isset($_POST['is_parttime_visible'])?intval($_POST['is_parttime_visible']):0;
			$handoutsInfo['introduce']= isset($_POST['introduce'])?str_replace("\r\n","<br>",str_replace(" ","&nbsp;",SysUtil::safeString($_POST['introduce']))):'';
			//$handoutsInfo['teacher_version']= isset($_POST['teacher_version'])?SysUtil::safeString($_POST['teacher_version']):'';
			//$handoutsInfo['student_version']= isset($_POST['student_version'])?SysUtil::safeString($_POST['student_version']):'';
			$handoutsInfo['IP'] = $this->getClientIp();
			$returnUrl = U('Vip/VipJiaoyan/add_handouts',array('title'=>$_POST['title'],'picture'=>str_replace('/','__',$_POST['picture']),'introduce'=>$_POST['introduce'],'teacher_version'=>str_replace('/','__',reset(explode('.',$_POST['teacher_version']))),'teacher_version_preview'=>str_replace('/','__',reset(explode('.',$_POST['teacher_version_preview']))),'teacher_version_type'=>end(explode('.',$_POST['teacher_version'])),'student_version'=>str_replace('/','__',reset(explode('.',$_POST['student_version']))),'student_version_preview'=>str_replace('/','__',reset(explode('.',$_POST['student_version_preview']))),'student_version_type'=>end(explode('.',$_POST['student_version']))));
			if(!empty($_POST['student_version_realname']) && !empty($handoutsInfo['sid']) && !empty($handoutsInfo['gid']) && !empty($handoutsInfo['kid']) && !empty($handoutsInfo['introduce']) && !empty($_POST['teacher_version']) && !empty($_POST['student_version'])){
				$vipHandoutsModel = D('VpHandouts');
				$dao = $vipHandoutsModel->dao;
				$handoutsInfo['user_key'] = $this->loginUser->getUserKey();
				$userInfo = $this->loginUser->getInformation();
				$handoutsInfo['is_teaching_and_research'] = 0;
				$is_jiaoyan = $this->checkUserRole();
				if($is_jiaoyan == 1){
					$handoutsInfo['is_teaching_and_research'] = 1;
				}
				$handoutsInfo['status'] = 0;
				if($_POST['action'] == 'update'){
					if(empty($handoutsInfo['title'])){
						$this->error('讲义修改失败，讲义标题不能为空');
					}
					$handoutsInfo['verifier'] = NULL;
					$auto_close_js = '<script type="text/javascript">
											function close_popup(){
												window.parent.location.reload();
												window.parent.document.getElementById("back").style.display = "none";
												window.parent.document.getElementById("mesWindow").style.display = "none";
											}
											window.setTimeout(close_popup, 1200);
										  </script>';
					$handoutsInfo['teacher_version'] = SysUtil::safeString($_POST['teacher_version'][0]);
					$handoutsInfo['student_version'] = SysUtil::safeString($_POST['student_version'][0]);
					$handoutsInfo['teacher_version_preview'] = SysUtil::safeString($_POST['teacher_version_preview'][0]);
					$handoutsInfo['student_version_preview'] = SysUtil::safeString($_POST['student_version_preview'][0]);
					if($vipHandoutsModel->update_handouts($handoutsInfo,$hid)){
						$returnMsg = '<h2>讲义信息修改成功,<font color=red>讲义需要重新审核</font></h2>';
						if($_GET['auto_close'] == 1){
							echo $returnMsg.$auto_close_js;
						}else{
							$this->success($returnMsg,U('Vip/VipJiaoyan/add_handouts',array('hid'=>$hid)));
						}
					}else{
						$returnMsg = '<h2>讲义信息修改失败</h2>';
						if($_GET['auto_close'] == 1){
							echo $returnMsg.$auto_close_js;
						}else{
							$this->error($returnMsg);
						}
					}
				}else if($_POST['action'] == 'insert'){
					if(!empty($_POST['teacher_version'])){
						$new = 0;
						foreach ($_POST['teacher_version'] as $key=>$val){
							$handoutsInfo['teacher_version'] = SysUtil::safeString($val);
							$handoutsInfo['student_version'] = SysUtil::safeString($_POST['student_version'][$key]);
							$handoutsInfo['teacher_version_preview'] = SysUtil::safeString($_POST['teacher_version_preview'][$key]);
							$handoutsInfo['student_version_preview'] = SysUtil::safeString($_POST['student_version_preview'][$key]);
							$handoutsInfo['title'] = SysUtil::safeString($_POST['student_version_realname'][$key]);
							if($vipHandoutsModel->get_handoutsCount(" is_delete = 0 AND title = ".$dao->quote($handoutsInfo['title']))){
								$handoutsInfo['title'] .= date('Y-m-d H:i:s');
							}
							if($newHid = $vipHandoutsModel->add_handouts($handoutsInfo)){
								$new++;
							}
						}
					}
					if($new == count($_POST['teacher_version'])){
						$this->success('讲义添加成功,请耐心等待审核',U('Vip/VipJiaoyan/add_handouts',array('hid'=>$newHid)));
					}else{
						$this->error('讲义添加失败',$returnUrl);
					}
				}
			}else{
				$this->error('请填写完整的讲义信息',$returnUrl);
			}
		}else{
			$userKey = $this->loginUser->getUserKey();
			$handoutsType = C('HANDOUTS_TYPE');
			$vipHandoutsModel = D('VpHandouts');
			$type = 0;
			if(!empty($hid)){
				$handoutsInfo = $vipHandoutsModel->get_handoutsInfo_by_hid($hid);
				if(!empty($handoutsInfo)){
					$handoutsInfo['picture_show'] = end(explode('eap',str_replace('Upload/','upload/',$handoutsInfo['picture'])));
					$handoutsInfo['teacher_version_show'] = end(explode('eap',str_replace('Upload/','upload/',$handoutsInfo['teacher_version'])));
					$handoutsInfo['student_version_show'] = end(explode('eap',str_replace('Upload/','upload/',$handoutsInfo['student_version'])));
					$handoutsInfo['introduce'] = str_replace("<br>","\r\n",str_replace("&nbsp;"," ",$handoutsInfo['introduce']));
					$vipKnowledgeModel = D('VpKnowledge');
					$handoutsInfo['knowledge_name'] = $vipKnowledgeModel->get_knowledgename_by_kid($handoutsInfo['kid']);
					$nianjiTemp = C('GRADES');
					$thisOptionNidsStr = $vipKnowledgeModel->get_nianjiList(array('sid'=>$handoutsInfo['sid'],'gid'=>$handoutsInfo['gid'],'kid'=>$handoutsInfo['kid']));
					if(!empty($thisOptionNidsStr)){
						foreach (explode(',',trim($thisOptionNidsStr,',')) as $key=>$nianji){
							$nianjiArr[$nianji] = $nianjiTemp[$nianji];
						}
					}
					unset($thisOptionNidsStr);
				}
			}else{
				$handoutsInfo = VipCommAction::getCacheHandoutsInfo();
			}
			$vipSubjectModel = D('VpSubject');
			$subjectArr = $vipSubjectModel->get_subjectList($type,$userKey);
			$vipGradeModel = D('VpGrade');
			$gradeArr = $vipGradeModel->get_gradeList($type,$userKey);
			$auto_close = $_GET['auto_close'];

			$this->assign(get_defined_vars());
			$this->display();
		}
	}


	/*上传试题库*/
	public function add_itembank(){
		$permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey());
		$is_jiaoyan = $this->checkUserRole();
		if($is_jiaoyan!=1){
			echo '<font size=3>抱歉，您无权限进行此操作</font>';exit;
		}
		$hid = isset($_REQUEST['hid'])?intval($_REQUEST['hid']):0;
		if($_POST){
			$handoutsInfo = array();
			$handoutsInfo['type'] = isset($_POST['type'])?intval($_POST['type']):0;
			$handoutsInfo['title'] = isset($_POST['title'])?SysUtil::safeString($_POST['title']):'';
			$handoutsInfo['picture'] = isset($_POST['picture'])?SysUtil::safeString($_POST['picture']):'';
			$handoutsInfo['sid'] = isset($_POST['subject'])?intval($_POST['subject']):0;
			$handoutsInfo['gid'] = isset($_POST['grade'])?intval($_POST['grade']):'';
			$handoutsInfo['kid'] = isset($_POST['knowledge'])?intval($_POST['knowledge']):0;
			$handoutsInfo['nids'] = isset($_POST['nianji'])?','.implode(',',$_POST['nianji']).',':'';
			$handoutsInfo['is_parttime_visible'] = isset($_POST['is_parttime_visible'])?intval($_POST['is_parttime_visible']):0;
			$handoutsInfo['introduce']= isset($_POST['introduce'])?str_replace("\r\n","<br>",str_replace(" ","&nbsp;",SysUtil::safeString($_POST['introduce']))):'';
			$handoutsInfo['teacher_version']= isset($_POST['teacher_version'])?SysUtil::safeString($_POST['teacher_version']):'';
			$handoutsInfo['teacher_version_preview']= isset($_POST['teacher_version_preview'])?SysUtil::safeString($_POST['teacher_version_preview']):'';
			$handoutsInfo['IP'] = $this->getClientIp();
			$returnUrl = U('Vip/VipJiaoyan/add_itembank',array('title'=>$_POST['title'],'picture'=>str_replace('/','__',$_POST['picture']),'introduce'=>$_POST['introduce'],'teacher_version'=>str_replace('/','__',reset(explode('.',$_POST['teacher_version']))),'teacher_version_type'=>end(explode('.',$_POST['teacher_version']))));
			if(!empty($handoutsInfo['title']) && !empty($handoutsInfo['sid']) && !empty($handoutsInfo['gid']) && !empty($handoutsInfo['kid']) && !empty($handoutsInfo['introduce']) && !empty($handoutsInfo['teacher_version']) ){
				$vipHandoutsModel = D('VpHandouts');
				$dao = $vipHandoutsModel->dao;
				if($_POST['action'] == 'insert' && $vipHandoutsModel->get_handoutsCount(" is_delete = 0 AND title = ".$dao->quote($handoutsInfo['title']))){
					$this->error('该试题标题已存在，讲义上传失败',$returnUrl);
				}
				$handoutsInfo['user_key'] = $this->loginUser->getUserKey();
				$userInfo = $this->loginUser->getInformation();
				$handoutsInfo['is_teaching_and_research'] = 0;
				$is_jiaoyan = $this->checkUserRole();
				if($is_jiaoyan == 1){
					$handoutsInfo['is_teaching_and_research'] = 1;
				}
				$handoutsInfo['status'] = 0;
				if($_POST['action'] == 'update'){
					$handoutsInfo['verifier'] = NULL;
					$auto_close_js = '<script type="text/javascript">
											function close_popup(){
												window.parent.location.reload();
												window.parent.document.getElementById("back").style.display = "none";
												window.parent.document.getElementById("mesWindow").style.display = "none";
											}
											window.setTimeout(close_popup, 1200);
									  </script>';
					if($vipHandoutsModel->update_handouts($handoutsInfo,$hid)){
						$returnMsg = '<h4>试题信息修改成功,<font color=red>试题需要重新审核</font></h4>';
						if($_GET['auto_close'] == 1){
							echo $returnMsg.$auto_close_js;
						}else{
							$this->success($returnMsg,U('Vip/VipJiaoyan/add_itembank',array('hid'=>$hid)));
						}
					}else{
						$returnMsg = '<h1>试题信息修改失败</h1>';
						if($_GET['auto_close'] == 1){
							echo $returnMsg.$auto_close_js;
						}else{
							$this->error($returnMsg);
						}
					}
				}else if($_POST['action'] == 'insert'){
					if($newHid = $vipHandoutsModel->add_handouts($handoutsInfo)){
						$this->success('试题添加成功',U('Vip/VipJiaoyan/add_itembank',array('hid'=>$newHid)));
					}else{
						$this->error('试题添加失败',$returnUrl);
					}
				}
			}else{
				$this->error('请填写完整的试题信息',$returnUrl);
			}
		}else{
			$userKey = $this->loginUser->getUserKey();
			$handoutsType = C('HANDOUTS_TYPE');
			$vipHandoutsModel = D('VpHandouts');
			$type = 1;
			if(!empty($hid)){
				$handoutsInfo = $vipHandoutsModel->get_handoutsInfo_by_hid($hid);
				if(!empty($handoutsInfo)){
					$handoutsInfo['picture_show'] = end(explode('eap',str_replace('Upload/','upload/',$handoutsInfo['picture'])));
					$handoutsInfo['teacher_version_show'] = end(explode('eap',str_replace('Upload/','upload/',$handoutsInfo['teacher_version'])));
					$handoutsInfo['introduce'] = str_replace("<br>","\r\n",str_replace("&nbsp;"," ",$handoutsInfo['introduce']));
					$vipKnowledgeModel = D('VpKnowledge');
					$handoutsInfo['knowledge_name'] = $vipKnowledgeModel->get_knowledgename_by_kid($handoutsInfo['kid']);
					$nianjiTemp = C('GRADES');
					$thisOptionNidsStr = $vipKnowledgeModel->get_nianjiList(array('sid'=>$handoutsInfo['sid'],'gid'=>$handoutsInfo['gid'],'kid'=>$handoutsInfo['kid']));
					if(!empty($thisOptionNidsStr)){
						foreach (explode(',',trim($thisOptionNidsStr,',')) as $key=>$nianji){
							$nianjiArr[$nianji] = $nianjiTemp[$nianji];
						}
					}
					unset($thisOptionNidsStr);
				}
			}else{
				$handoutsInfo = VipCommAction::getCacheHandoutsInfo();
			}
			$vipSubjectModel = D('VpSubject');
			$subjectArr = $vipSubjectModel->get_subjectList($type,$userKey);
			$vipGradeModel = D('VpGrade');
			$gradeArr = $vipGradeModel->get_gradeList($type,$userKey);
			$auto_close = $_GET['auto_close'];
			$this->assign(get_defined_vars());
			$this->display();
		}
	}


	/*文档管理*/
	public function documents_manage(){
		$permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey());
		$is_jiaoyan = $this->checkUserRole();
		$is_jianzhi = $this->checkUserType();
		$is_admin = VipCommAction::checkIsAdmin();
		if($is_jiaoyan!=1 && !$is_admin){
			echo '<font size=3>抱歉，您无权限进行此操作</font>';exit;
		}
		$userKey = $this->loginUser->getUserKey();
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
			$condition .= ' AND title like '.$dao->quote('%' . SysUtil::safeSearch(urldecode($keyword)) . '%');
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
		$this->display();
	}


	protected  function get_form(){
		$type = isset($_GET['type'])?SysUtil::safeString($_GET['type']):'';
		$ntype = isset($_GET['ntype'])?SysUtil::safeString($_GET['ntype']):0;
		$form_str = '';
		if(!empty($type)){
			$form_str .= '<div class="add_item"><form id="add_item_form" name="add_item_form" method="POST" action="'.U('Vip/VipJiaoyan/add_item',array('type'=>$type,'ntype'=>$ntype)).'" onsubmit="return check_add_item(\''.$type.'\',\''.$ntype.'\')">';
			switch ($type){
				case 'subject':
					$form_str .= '<div><font color=red>*</font>科目名称：<input type="text" name="name" id="name" value=""><label id=name_msg class=error></label></div>';
					break;
				case 'grade':
					$sid = isset($_GET['sid'])?SysUtil::safeString($_GET['sid']):'';
					$vipSubjectModel = D('VpSubject');
					$subjectList = $vipSubjectModel->get_subjectList($ntype);
					if(!empty($subjectList)){
						$form_str .= '<font color=red>*</font>所属科目：<div id="subject_temp">';
						foreach ($subjectList as $key=>$subject){
							$form_str .= '<input type="radio" name="subject_id" id="subject_id" value="'.$subject['sid'].'" ';
							$form_str .= ($subject['sid'] == $sid)?' checked="checked" ':'';
							$form_str .= '>'.$subject['name'].'&nbsp;&nbsp;';
						}
						$form_str .= '<label id=subject_id_msg class=error></label></div>';
					}
					$ntype_msg = ($ntype==0)?'课程':'题库';
					$form_str .= '<div><font color=red>*</font>'.$ntype_msg.'属性名称：<input type="text" name="name" id="name" value=""><label id=name_msg class=error></label></div>';
					break;
				case 'knowledge':
					$sid = isset($_GET['sid'])?abs($_GET['sid']):'';
					$gidStr = isset($_GET['gidStr'])?$_GET['gidStr']:'';
					$vipSubjectModel = D('VpSubject');
					$subjectList = $vipSubjectModel->get_subjectList($ntype);
					if(!empty($subjectList)){
						$form_str .= '<font color=red>*</font>所属科目：<div id="subject_temp">';
						foreach ($subjectList as $key=>$subject){
							$form_str .= '<input type="radio" name="subject_id" id="subject_id" value="'.$subject['sid'].'" onclick="select_grades(this.value,\'#grades_temp\',\'\')" ';
							$form_str .= ($subject['sid'] == $sid)?' checked="checked" ':'';
							$form_str .= '>'.$subject['name'].'&nbsp;&nbsp;';
						}
						$form_str .= '<label id=subject_id_msg class=error></label></div>';
					}
					$vipGradeModel = D('VpGrade');
					if(!empty($sid)){
						$gradeList = $vipGradeModel->get_gradeList_by_subjectid($sid);
					}else{
						$gradeList = $vipGradeModel->get_gradeList($ntype);
					}
					if(!empty($gradeList)){
						$ntype_msg = ($ntype==0)?'课程':'题库';
						$form_str .= '<font color=red>*</font>所属'.$ntype_msg.'属性：<div id="grades_temp">';
						foreach ($gradeList as $key=>$grade){
							$form_str .= '<input type="checkbox" name="grade[]" id="grade_'.$grade['gid'].'" value="'.$grade['gid'].'" ';
							$form_str .= (strpos('_'.$gidStr,'_'.$grade['gid'].'_')!==false)?' checked ="checked" ':'';
							$form_str .= '>'.$grade['name'].'&nbsp;&nbsp;';
						}
						$form_str .= '<label id=grade_id_msg class=error></label></div>';
					}
					$ntype_msg = ($ntype==0)?'讲义':'试题';
					$form_str .= '<div><font color=red>*</font>'.$ntype_msg.'属性名称：<input type="text" name="name" id="name" value=""><label id=name_msg class=error></label></div>';
					break;
			}
			$form_str .= '<input type="submit" name="submit" value="确认添加" ></form></div>';
		}
		echo $form_str;die;
	}


	/*添加属性*/
	public function add_item(){
		$type = isset($_GET['type'])?SysUtil::safeString($_GET['type']):'';
		$ntype = isset($_GET['ntype'])?SysUtil::safeString($_GET['ntype']):0;
		if(!empty($type)){
			switch ($type){
				case 'subject':
					$operate = '科目';
					if(empty($_POST['name'])){
						$this->error('科目名称不能为空');
					}
					$vipSubjectModel = D('VpSubject');
					$dao = $vipSubjectModel->dao;
					if($vipSubjectModel->get_count("[name] = ".$dao->quote($_POST[name]))){
						$this->error('该科目名称已存在，科目添加失败');
					}
					$result = $vipSubjectModel->add_subject(array('name'=>trim($_POST['name']),'type'=>$ntype));
					break;
				case 'grade':
					$operate = ($type==0)?'课程属性':'题库属性';
					if(empty($_POST['subject_id'])){
						$this->error('所属科目不能为空');
					}
					if(empty($_POST['name'])){
						$this->error($operate.'名称不能为空');
					}
					$vipGradeModel = D('VpGrade');
					//判断要添加的课程属性名称是否存在，若存在只添加学科和课程属性的关系即可，若不存在两者都要加
					$this_gradeCount = $vipGradeModel->get_grade($_POST['name']);
					if($this_gradeCount > 0){
						$result = $vipGradeModel->add_relationship(array('subject_id'=>intval($_POST['subject_id']),'name'=>SysUtil::safeString($_POST['name'])));
						if($result === '0'){
							$this->error('所选科目中已存在要添加的'.$operate.'名称,'.$operate.'添加失败');
						}
					}else{
						$result = $vipGradeModel->add_grade(array('subject_id'=>intval($_POST['subject_id']),'name'=>SysUtil::safeString($_POST['name']),'type'=>$ntype));
					}
					break;
				case 'knowledge':
					$operate = ($type==0)?'讲义属性':'试题属性';
					if(empty($_POST['subject_id'])){
						$this->error('所属科目不能为空');
					}
					if(empty($_POST['grade'])){
						$error_msg = ($type==0)?'课程属性':'题库属性';
						$this->error('所属'.$error_msg.'不能为空');
					}
					if(empty($_POST['name'])){
						$this->error($operate.'名称不能为空');
					}
					$vipKnowledgeModel = D('VpKnowledge');
					$kid  = $vipKnowledgeModel->get_knowledgeid_by_name(SysUtil::safeString($_POST['name']));
					if(!$kid){
						$kid = $vipKnowledgeModel->add_knowledge(array('name'=>SysUtil::safeString($_POST['name']),'type'=>$ntype));
					}
					$result = $vipKnowledgeModel->add_knowledge_permission(array('subject_id'=>intval($_POST['subject_id']),'grade_id'=>$_POST['grade'],'kid'=>$kid));
					break;
			}
			if($result === false){
				$this->error($operate.'添加失败');
			}
			if($result === true){
				if($ntype == 0){
					$returnUrl = U('/Vip/VipJiaoyan/handouts_attribute');
				}else{
					$returnUrl =U('/Vip/VipJiaoyan/itembank_attribute');
				}
				$this->success($operate.'添加成功',$returnUrl);
			}
		}else{
			$this->error('非法操作，请明确要添加属性的类型');
		}
	}


	/*讲义属性设置*/
	public function manage_attribute(){
		$subject_id = isset($_POST['subject'])?intval($_POST['subject']):'';
		$grade_id_arr = isset($_POST['grade'])?$_POST['grade']:'';
		$knowledge_id_arr = isset($_POST['knowledge'])?$_POST['knowledge']:'';
		$nianji_id_str = isset($_POST['nianji'])?','.implode(',',$_POST['nianji']).',':'';
		$permission = isset($_POST['permission'])?intval($_POST['permission']):0;

		$course_user = isset($_POST['course_user'])?htmlspecialchars(trim($_POST['course_user'])):'';

		if(!empty($subject_id) && !empty($grade_id_arr) &&!empty($knowledge_id_arr)){
			$vipKnowledgeModel = D('VpKnowledge');
			if(!empty($grade_id_arr)){
				foreach ($grade_id_arr as $key =>$gradeid){
					if(!empty($knowledge_id_arr)){
						foreach ($knowledge_id_arr as $kk =>$knowledgeid){
							//判断该讲义属性是否存在，如果存在在更改权限，不存在则不做操作
							$vipKnowledgeModel->update_permission(array('sid'=>$subject_id,'gid'=>$gradeid,'kid'=>$knowledgeid,'nids'=>$nianji_id_str),$permission,$course_user);
						}
					}
				}
			}
			$this->success('讲义目录设置成功');
		}else{
			$this->error('请选择完整的信息');
		}

	}


	/*删除属性*/
	protected function delete_attribute(){
		$return = array();
		$idArr = explode('_',trim($_POST['idStr'],'_'));
		if(!empty($idArr) && !empty($_POST['type'])){
			$thisDeleteModel = D('VpSubject');
			switch ($_POST['type']){
				case 'subject':
					$return['status'] = $thisDeleteModel->delete_subject($idArr);
					$msg_type = '科目';
					break;
				case 'grade':
					$sid = $_POST['sid'];
					$return['status'] = $thisDeleteModel->delete_grade($idArr,$sid);
					$msg_type = '课程属性';
					break;
				case 'knowledge':
					$sid = $_POST['sid'];
					$gidArr = !empty($_POST['gidStr'])?explode('_',trim($_POST['gidStr'],'_')):'';
					$return['status'] = $thisDeleteModel->delete_knowledge($idArr,$sid,$gidArr);
					$msg_type = '讲义属性';
					break;
			}
			$return['msg'] = ($return['status']==1)?$msg_type.'删除成功':$msg_type.'删除失败';
			if($return['status'] == 1){
				$return['url'] = ($_POST['ntype']==1)?U('Vip/VipJiaoyan/itembank_attribute'):U('Vip/VipJiaoyan/handouts_attribute');
			}
		}else{
			$return['status'] = 0;
			$return['msg'] = '您没有选中任何选项，无法进行删除操作';
		}
		echo json_encode($return);
	}


	/*编辑属性*/
	public function edit_attribute(){
		$thisEditModel = D('VpSubject');
		switch($_POST['type']){
			case 'subject':
				$typeName = '科目';
				break;
			case 'grade':
				$typeName = '课程属性';
				break;
			case 'knowledge':
				$typeName = '讲义属性';
				break;
		}
		if($thisEditModel->editName($_POST['type'],$_POST['name'],$_POST['id'])){
			$returnUrl = ($_GET['ntype'] == 1)?U('Vip/VipJiaoyan/itembank_attribute'):U('Vip/VipJiaoyan/handouts_attribute');
			$this->success($typeName.'名称修改成功',$returnUrl);
		}else{
			$this->error($typeName.'名称修改失败');
		}
	}


	/*获取讲义修改表单*/
	protected function get_edithandouts_form(){
		$returnHtml = '';
		if(!empty($_GET['hid'])){
			$action = ($_GET['type'] == 0)?'add_handouts':'add_itembank';
			$returnHtml .= '<iframe src="'.U('Vip/VipJiaoyan/'.$action,array('hid'=>$_GET['hid'],'auto_close'=>1)).'" width="760" height="750" style="border:0px;"></iframe>';
		}else{
			$returnHtml .= '非法操作';
		}
		echo $returnHtml;
	}



	protected function changeSubjectList(){
		$type = isset($_GET['type'])?abs($_GET['type']):0;
		$subjectHtml = '';
		$subjectList = D('VpSubject')->get_subjectList($type);
		if(!empty($subjectList)){
			$subjectHtml .= '<option value="">请选择科目</option>';
			foreach ($subjectList as $key=>$subject){
				$subjectHtml.= '<option value="'.$subject['sid'].'">'.$subject['name'].'</option>';
			}
		}
		$gradeHtml = '';
		$gradeList = D('VpGrade')->get_gradeList($type);
		if(!empty($gradeList)){
			$gradeHtml .= '<option value="">请选择课程属性</option>';
			foreach ($gradeList as $key=>$grade){
				$gradeHtml.= '<option value="'.$grade['gid'].'">'.$grade['name'].'</option>';
			}
		}
		$knowledgeHtml = '<option value="">请选择讲义属性</option>';
		echo json_encode(array('subjectHtml'=>$subjectHtml,'gradeHtml'=>$gradeHtml,'knowledgeHtml'=>$knowledgeHtml));
	}



	//话术管理========================================================================================
	public function wordsManage(){
		$vipSubjectModel = D('VpSubject');
		$subjectArr = $vipSubjectModel->get_subjectList(0);

		//$dimensionArr = $vipSubjectModel->get_dimensionList();

		$levelArr = $vipSubjectModel->get_levelList();
		$this->assign(get_defined_vars());
		$this->display();
	}



	public function getDimesionList(){
		$html = '';
		$dimensionArr = D('VpSubject')->get_dimensionList(array('sid'=>$_GET['sid']));
		if(!empty($dimensionArr)){
			foreach ($dimensionArr as $key=>$dimension){
				$html .= '<input type="radio" id="dimension_id'.$dimension['dimension_id'].'" name="dimension_id" value="'.$dimension['dimension_id'].'" onclick="clear_level()" title="'.$dimension['dimension_name'].'">'.$dimension['dimension_name'].'&nbsp;&nbsp;&nbsp;&nbsp;';
			}
		}else{
			$html = '<font color=red>暂无相关维度</font>';
		}
		echo json_encode(array('html'=>$html));
	}


	public function getCommentText(){
		$html = '';
		if(!empty($_GET['sid']) && !empty($_GET['dimension_id']) && !empty($_GET['level_id'])){
			$commentArr = D('VpSubject')->get_commentTextList(array('sid'=>$_GET['sid'],'dimension_id'=>$_GET['dimension_id'],'level_id'=>$_GET['level_id']));
			if(!empty($commentArr)){
				foreach ($commentArr as $key=>$comment){
					$html .= '<p><input type="checkbox" id="comment_id" name="comment_id[]" value="'.$comment['id'].'" >&nbsp;&nbsp;'.$comment['text'].'</p>';
				}
			}
		}
		echo json_encode(array('html'=>$html));
	}


	public function delete_commentText(){
		$status = 0;
		$html = '';
		$vipSubjectModel = D('VpSubject');
		if($vipSubjectModel->delete_commentText()){
			$status = 1;
			$commentArr = $vipSubjectModel->get_commentTextList(array('sid'=>$_POST['sid'],'dimension_id'=>$_POST['dimension_id'],'level_id'=>$_POST['level_id']));
			if(!empty($commentArr)){
				foreach ($commentArr as $key=>$comment){
					$html .= '<p><input type="checkbox" id="comment_id" name="comment_id[]" value="'.$comment['id'].'" >&nbsp;&nbsp;'.$comment['text'].'</p>';
				}
			}
		}

		echo json_encode(array('status'=>$status,'html'=>$html));
	}


	public function add_commentText(){
		$status = 0;
		$html = '';
		$vipSubjectModel = D('VpSubject');
		if($vipSubjectModel->add_commentText()){
			$status = 1;
			$commentArr = $vipSubjectModel->get_commentTextList(array('sid'=>$_POST['sid'],'dimension_id'=>$_POST['dimension_id'],'level_id'=>$_POST['level_id']));
			if(!empty($commentArr)){
				foreach ($commentArr as $key=>$comment){
					$html .= '<p><input type="checkbox" id="comment_id" name="comment_id[]" value="'.$comment['id'].'" >&nbsp;&nbsp;'.$comment['text'].'</p>';
				}
			}
		}

		echo json_encode(array('status'=>$status,'html'=>$html));
	}


	public function edit_CommentType(){
		switch($_POST['type']){
			case 'dimension':
				$typeName = '维度';
				break;
			case 'level':
				$typeName = '级别';
				break;
		}
		if(D('VpSubject')->edit_CommentType()){
			$this->success($typeName.'修改成功');
		}else{
			$this->error($typeName.'修改失败');
		}
	}



	public function commentTemplate(){
		$vipSubjectModel = D('VpSubject');
		$subjectArr = $vipSubjectModel->get_subjectList(0);
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function getTemplateList(){
		$html = '';
		$templateArr = D('VpSubject')->get_templateList();
		if(!empty($templateArr)){
			foreach ($templateArr as $key=>$template){
				$html .= '<table width="100%"><tr><td width="40%">'.$template['text'].'</td><td><a href="'.U('Vip/VipJiaoyan/deleteCommentTemplate',array('template_id'=>$template['id'])).'" class="blue">删除</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="return testMessageBox_editCommentTemplate(event,\''.U("Vip/VipJiaoyan/editCommentTemplate",array("template_id"=>$template["id"])).'\',\''.$template["text"].'\')" class="blue">编辑</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button onclick="return testMessageBox_viewCommentTemplate(event,\''.U("Vip/VipJiaoyan/previewCommentTemplate",array("template_id"=>$template["id"])).'\')" value=" 预览 " class="btn"></td></tr></table>';
			}
		}
		echo json_encode(array('html'=>$html));
	}
	
	
	public function deleteCommentTemplate(){
		if(D('VpSubject')->delete_commentTemplate($_GET['template_id'])){
			$this->success('课堂评价话术删除成功');
		}else{
			$this->error('课堂评价话术删除失败');
		}
	}
	
	
	public function add_commentTemplate(){
		if(D('VpSubject')->add_commentTemplate()){
			$this->success('课堂评价话术添加成功');
		}else{
			$this->error('课堂评价话术添加失败');
		}
	}
	
	
	public function editCommentTemplate(){
		if(D('VpSubject')->edit_commentTemplate($_GET['template_id'],$_POST['text'])){
			$this->success('课堂评价话术修改成功');
		}else{
			$this->error('课堂评价话术修改失败');
		}
	}
	
	
	public function previewCommentTemplate(){
		$vipSubjectModel = D('VpSubject');
		$previewText = $vipSubjectModel->get_templatePreview($_GET['template_id']);
		echo json_encode(array('text'=>$previewText));
	}
	
	
	public function add_CommentType(){
		if(!empty($_POST['sid']) && !empty($_POST['title'])){
			if(D('VpSubject')->add_commentType($_POST)){
				$this->success('维度添加成功');
			}else{
				$this->error('维度添加失败，请检查维度名称是否符合规范或该科目下已存在要添加的维度');
			}
		}else{
			$this->error('请填写完整的维度信息');
		}
	}
	
	
	
	
	public function addLevel(){
		if(!empty($_POST['title']) && !empty($_POST['rank'])){
			if(D('VpSubject')->add_level($_POST)){
				$this->success('级别添加成功');
			}else{
				$this->error('级别添加失败,请检查级别名称是否符合规范或该级别已存在');
			}
		}else{
			$this->error('请填写完整的级别信息');
		}
	}
	
	
	
	public function delete_CommentType(){
		$status = 0;
		if(D('VpSubject')->delete_commentType($_POST)){
			$status = 1;
		}
		echo json_encode(array('status'=>$status));
	
	}
	
	
	
	
	//piv4.0新课程体系=======start=====================================================================================================
	public function courseTypeTreeManage(){
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	
	public function course_add(){
		$params = $this->_get ();
		$parentId = abs($params ['pid']);
		$knowledgetypeid = $params ['knowledgetypeid'];
		$level = $params ['level'];
		$path = '/';
		if (! empty ( $parentId )) {
			$vipSubjectModel = D('VpSubject');
			$path = arr2nav ( $vipSubjectModel->getPath ( $parentId, 'course' ) );
			$course = $vipSubjectModel->getCourseByID ( $parentId );
			if ($course) {
				$name = $course ['name'];
				//$this->assign ( 'name', $name );
			}
		}
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	
	public function course_edit(){
		$params = $this->_get ();
		$id = $params ['id'];
		$knowledgetypeid = $params ['knowledgetypeid'];
		$course =  D('VpSubject')->getCourseByID ( $id );
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	
	public function course_delete(){
		$id = $_POST ['id'];
		$knowledgetypeid = $_POST ['knowledgetypeid'];
		$rs =  D('VpSubject')->deleteCourseByID ( $id, $knowledgetypeid);
		$status = 0;
		if($rs){
			$status = 1;
		}
		echo $this->outPut (array('status'=>$status) );
	}
	
	public function courses(){
		$params = $this->_get ();
		$knowledgeTypeId = $params ['knowledgetypeid'];

		$this->assign(get_defined_vars());
		$this->display ();
	}
	
	
	public function getCourses() {
		$params = $this->_get ();

		$parentId = $params ['id'];
		$knowledgeTypeId = $params ['knowledgetypeid'];

		$courses = D('VpSubject')->getCoursesByParentId ( $parentId, $knowledgeTypeId );
		
		$this->outPut ( $courses );
	}
	
	
	public function getCourses1() {
		$params = $this->_param ();
		$parentId = $params ['id'];
		$knowledgeTypeId = $params ['knowledgetypeid'];

		$courses = D('VpSubject')->getCoursesByParentId1 ( $parentId, $knowledgeTypeId );
		
		$this->outPut ( $courses );
	}
	
	
	public function course_add_save() {
		$status = false;
		$message = '';
		$rs = D('VpSubject')->addCourse ( $_POST );
		if ($rs) {
			$status = true;
		}else{
			$message = '添加失败';
		}
		echo $this->outPut (array('status'=>$status,'message'=>$message) );
	}
	
	
	public function getCourseChilds(){
		$params = $this->_param ();
		$parentId = $params ['id'];
		$knowledgetypeid = $params ['knowledgetypeid'];
		$courses = D('VpSubject')->getCoursesByParentIdChild ( $parentId, $knowledgetypeid );
		$this->outPut ( $courses );
	}
	
	
	public function course_edit_save() {
		$params = $this->_post ();
		$status = false;
		$message = '';
		$rs = D('VpSubject')->updateCourse ( $params );
		if ($rs) {
			$status = true;
		}else{
			$message = '添加失败';
		}
		echo $this->outPut (array('status'=>$status,'message'=>$message) );
	}
	
	
	public function getPath() {
		$params = $this->_post ();
		$id = $params [id];
		$path = '/';
		if (! empty ( $id )) {
			$path = arr2nav ( D ( 'VpSubject' )->getPath ( $id ) );
		}

		$this->outPut ( $path );
	}



	/**
	 * 教研日报
	 */
	public function dailyManage()
	{
		$vipTargetModel=D('VipTarget');
		//接收日期时间处理为日期
		$date=!empty($_GET['date'])?date('Y-m',strtotime($_GET['date'])):'';
		//接收当前日期时间
		$dateTime=!empty($_GET['date'])?$_GET['date']:'';
		//接收当前页
		$curPage=isset($_GET['p'])?abs($_GET['p']):1;
		//接收教研员姓名
		//$user_realname=isset($_GET['user_realname'])?$_GET['user_realname']:'';
		//引入分页类
		import("ORG.Util.Page");
		//取得配置中设置的每页显示条数
		$pagesize = C('PAGESIZE');

		//取得系统角色管理-角色用户管理-VIP教师系统-普通教研员中状态为是的数据
		$userList=$vipTargetModel->getTargetStausList($date,'',$curPage,$pagesize);
		if(!empty($userList)){
		$user_key_str='';
		$user_email_str='';
		if (!empty($userList)) {
			foreach ($userList as $key => $value) {
				$user_key_str.="'".$value['user_key']."',";
				$user_email_str.="'".$value['user_email']."',";
			}
		}
		//如果没有查询到则教研员字符串为空
		if (empty($user_key_str)) {
				$user_key_str="''";
		}
		//科目授权
		$vipSubjectModel = D('VpSubject');
		//查询授课科目
		$subjectAccredit = $vipSubjectModel->get_all_subjectName(trim($user_key_str,','));

		//查询当日说课视频数
		$sayCount=$vipTargetModel->getAtfVideoTypeNum($dateTime,$type=0,trim($user_email_str,','));

		//查询当日导课视频数
		$explodeCount=$vipTargetModel->getAtfVideoTypeNum($dateTime,$type=1,trim($user_email_str,','));

		//查询指定月说导视频
		$vipVoideNum=$vipTargetModel->getAtfVideoNum($date,trim($user_email_str,','));

		//查询当日新搭建的讲义
		$lecture=$vipTargetModel->getLectureArchive($dateTime,trim($user_email_str,','));

		foreach ($sayCount as $key => $value) {
			$say_count[$value['user_key']]=$value['num'];
		}

		foreach ($explodeCount as $key => $value) {
			$explode_count[$value['user_key']]=$value['num'];
		}

		foreach ($vipVoideNum as $key => $value) {
			$vip_voide[$value['user_key']]=$value['num'];
		}

		foreach ($lecture as $key => $value) {
			$vip_lecture[$value['user_key']]=$value['num'];
		}
		//把授课科目、导说视频数、导说完成率合并到列表数据中
		foreach ($userList as $key1 => $value1) {
			if(VipCommAction::checkIsAdmin($value1['user_key'],GROUP_NAME)){
				$userList[$key1]['subjectAccredit'] = "全部科目(管理员无需授权)";
			}else{
				$userList[$key1]['subjectAccredit'] = trim($subjectAccredit[$value1['user_key']],',')?trim($subjectAccredit[$value1['user_key']],','):'暂未授权';
			}
			$userList[$key1]['saynum'] =$say_count[$value1['user_key']]?$say_count[$value1['user_key']]:0;
			$userList[$key1]['explodenum']=$explode_count[$value1['user_key']]?$explode_count[$value1['user_key']]:0;
			$userList[$key1]['num']=$vip_voide[$value1['user_key']]?$vip_voide[$value1['user_key']]:0;
			$userList[$key1]['lecturenum']=$vip_lecture[$value1['user_key']]?$vip_lecture[$value1['user_key']]:0;
			$userList[$key1]['rate']=round(($vip_voide[$value1['user_key']]/$value1['target']*100)).'%';
			$userList[$key1]['dateTime']=!empty($dateTime)?$dateTime:date('Y-m-d');
		}
		}
		//获取总条数
		$count=$vipTargetModel->getTargetStatuscount($date,$user_realname);
		//实例化分页
		$page = new Page($count,$pagesize);
		//显示分页
		$showPage = $page->show();
		$this->assign(get_defined_vars());
		$this->display();
	}


	/**
	 * 教研目标
	 */
	public function dailyTarget()
	{
		$vipTargetModel=D('VipTarget');
		//引入分页类
		import("ORG.Util.Page");
		//接收日期
		$date=!empty($_GET['date'])?$_GET['date']:'';
		$dateTime=!empty($_GET['date'])?$_GET['date']:'';
		//接收当前页
		$curPage=isset($_GET['p'])?abs($_GET['p']):1;
		//接收教研员姓名
		$user_realname=isset($_GET['user_realname'])?$_GET['user_realname']:'';

		//取得配置中设置的每页显示条数
		$pagesize = C('PAGESIZE');
		//取得系统角色管理-角色用户管理-VIP教师系统-普通教研员中状态为是的数据
		$userList=$vipTargetModel->getTargetStausList($date,$user_realname,$curPage,$pagesize);
		if(!empty($userList)){
		$user_key_str='';
		$user_email_str='';
		if (!empty($userList)) {
			foreach ($userList as $key => $value) {
				$user_key_str.="'".$value['user_key']."',";
				$user_email_str.="'".$value['user_email']."',";
			}
		}
		//如果没有查询到则教研员字符串为空
		if (empty($user_key_str)) {
				$user_key_str="''";
		}
		//科目授权
		$vipSubjectModel = D('VpSubject');
		//查询授课科目
		$subjectAccredit = $vipSubjectModel->get_all_subjectName(trim($user_key_str,','));
		//查询指定月说导视频
		$vipVoideNum=$vipTargetModel->getAtfVideoNum($date,trim($user_email_str,','));

		foreach ($vipVoideNum as $key => $value) {
			$vip_video_data[$value['user_key']]=$value['num'];
		}

		//把授课科目、导说视频数、导说完成率合并到列表数据中
		foreach ($userList as $key1 => $value1) {
			if(VipCommAction::checkIsAdmin($value1['user_key'],GROUP_NAME)){
				$userList[$key1]['subjectAccredit'] = "全部科目(管理员无需授权)";
			}else{
				$userList[$key1]['subjectAccredit'] = trim($subjectAccredit[$value1['user_key']],',')?trim($subjectAccredit[$value1['user_key']],','):'暂未授权';
			}
			$userList[$key1]['num']=$vip_video_data[$value1['user_key']]?$vip_video_data[$value1['user_key']]:0;
			$userList[$key1]['rate']=round(($vip_video_data[$value1['user_key']]/$value1['target']*100)).'%';
		}
		}
		//获取总条数
		$count=$vipTargetModel->getTargetStatuscount($date,$user_realname);
		//实例化分页
		$page = new Page($count,$pagesize);
		//显示分页
		$showPage = $page->show();
		$this->assign(get_defined_vars());
		$this->display();
	}


	/**
	 * 添加教研目标
	 */
	public function dailyTarget_add()
	{
		// 普通教研员角色id
		$role_id="815C9212-0032-4C41-8F9C-F458BDB29FAA";
		//引入分页类
		import("ORG.Util.Page");
		//接收当前日期
		$date=!empty($_GET['date'])?$_GET['date']:date('Y-m');
		//接收当前页，默认为第一页
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		//获取配置中设置的每页显示条数
		$pagesize = 10;
		//取得系统角色管理-角色用户管理-VIP教师系统-普通教研员中所有数据
		$userList=Role::getUsers(GROUP_NAME,$role_id , '' ,$curPage , $pagesize);
		$vipTargetModel=D('VipTarget');
		//查询设置目标的列表
		$targetList=$vipTargetModel->getTargetParamsAll($date);
		//合并目标到教研员数据
		foreach ($userList[1] as $key => $value) {
			$userList[1][$key]['targetList']=$targetList[$value['user_key']];
		}
		//实例化分页
		$page = new Page($userList[0],$pagesize);
		//显示分页
		$showPage = $page->show();
		$this->assign(get_defined_vars());
		$this->display();
	}

	/**
	 * 保存教研目标
	 * @return [type] [description]
	 */
	public function dailyTarget_save()
	{
		//var_dump($_POST);exit;
		//接收年月份
		$date=isset($_POST['date'])?$_POST['date']:date('Y-m');
		for ($i=1; $i <= 10 ; $i++) { 
			$data[$i]['date']=$date;
			$data[$i]['user_email']=isset($_POST['user_email'.$i])?$_POST['user_email'.$i]:'';
			$data[$i]['user_key']=isset($_POST['user_key'.$i])?$_POST['user_key'.$i]:'';
			$data[$i]['username']=isset($_POST['username'.$i])?$_POST['username'.$i]:'';
			$data[$i]['user_realname']=isset($_POST['user_realname'.$i])?$_POST['user_realname'.$i]:'';
			$data[$i]['target']=isset($_POST['target'.$i])?$_POST['target'.$i]:0;
			$data[$i]['status']=isset($_POST['status'.$i])?$_POST['status'.$i]:1;
		}
		//实例化
		$vipTargetModel=D('VipTarget');
		//执行添加操作
		$result=$vipTargetModel->addTarget($data);
		if ($result) {
			echo json_encode(array('status'=>1));
		}else
		{
			echo json_encode(array('status'=>0));
		}
	}

	/**
	 * 发送邮件
	 * @return [type] [description]
	 */
	public function sendMail ()
	{
		$vipTargetModel=D('VipTarget');
		//接收日期时间处理为日期
		$date=date('Y-m');
		//接收当前日期时间
		$dateTime=date('Y-m-d');
		//取得系统角色管理-角色用户管理-VIP教师系统-普通教研员中状态为是的数据
		$userList=$vipTargetModel->getTargetAll($date);
		if(!empty($userList)){
		$user_key_str='';
		$user_email_str='';
		if (!empty($userList)) {
			foreach ($userList as $key => $value) {
				$user_key_str.="'".$value['user_key']."',";
				$user_email_str.="'".$value['user_email']."',";
			}
		}
		//如果没有查询到则教研员字符串为空
		if (empty($user_key_str)) {
				$user_key_str="''";
		}
		//科目授权
		$vipSubjectModel = D('VpSubject');
		//查询授课科目
		$subjectAccredit = $vipSubjectModel->get_all_subjectName(trim($user_key_str,','));

		//查询当日说课视频数
		$sayCount=$vipTargetModel->getAtfVideoTypeNum($dateTime,$type=0,trim($user_email_str,','));

		//查询当日导课视频数
		$explodeCount=$vipTargetModel->getAtfVideoTypeNum($dateTime,$type=1,trim($user_email_str,','));

		//查询指定月说导视频
		$vipVoideNum=$vipTargetModel->getAtfVideoNum($date,trim($user_email_str,','));

		//查询当日新搭建的讲义
		$lecture=$vipTargetModel->getLectureArchive($dateTime,trim($user_email_str,','));

		//把授课科目、导说视频数、导说完成率合并到列表数据中
		foreach ($sayCount as $key => $value) {
			$say_count[$value['user_key']]=$value['num'];
		}

		foreach ($explodeCount as $key => $value) {
			$explode_count[$value['user_key']]=$value['num'];
		}

		foreach ($vipVoideNum as $key => $value) {
			$vip_voide[$value['user_key']]=$value['num'];
		}

		foreach ($lecture as $key => $value) {
			$vip_lecture[$value['user_key']]=$value['num'];
		}

		//把授课科目、导说视频数、导说完成率合并到列表数据中
		foreach ($userList as $key1 => $value1) {
			/*if(VipCommAction::checkIsAdmin($value1['user_key'],GROUP_NAME)){
				$userList[$key1]['subjectAccredit'] = "全部科目(管理员无需授权)";
			}else{
				$userList[$key1]['subjectAccredit'] = trim($subjectAccredit[$value1['user_key']],',')?trim($subjectAccredit[$value1['user_key']],','):'暂未授权';
			}*/
			$userList[$key1]['saynum'] =$say_count[$value1['user_key']]?$say_count[$value1['user_key']]:0;
			$userList[$key1]['explodenum']=$explode_count[$value1['user_key']]?$explode_count[$value1['user_key']]:0;
			$userList[$key1]['num']=$vip_voide[$value1['user_key']]?$vip_voide[$value1['user_key']]:0;
			$userList[$key1]['lecturenum']=$vip_lecture[$value1['user_key']]?$vip_lecture[$value1['user_key']]:0;
			$userList[$key1]['rate']=round(($vip_voide[$value1['user_key']]/$value1['target']*100)).'%';
			//$userList[$key1]['dateTime']=$dateTime;

		}
		}
		$html='';
		$html.='<table width="100%" border="1" style="text-align: center;">
				<tr bgcolor="#dddddd" height=30>
				<td width="10%">姓名</td>
				<td>当日新搭建讲义</td>
				<td>当日导课视频</td>
				<td>当日说课视频</td>
				<td>本月说导视频</td>
				<td>本月说导目标</td>
				<td>本月说导完成率</td>
			</tr>';
		foreach($userList as $key=>$handouts){
			$html.='<tr height=30>';
			$html.='<td>'.$handouts['user_realname'].'</td>';
			$html.='<td>'.$handouts['lecturenum'].'</td>';
			$html.='<td>'.$handouts['explodenum'].'</td>';
			$html.='<td>'.$handouts['saynum'].'</td>';
			$html.='<td>'.$handouts['num'].'</td>';
			$html.='<td>'.$handouts['target'].'</td>';
			$html.='<td>'.$handouts['rate'].'</td>';
			$html.='</tr>';
		}
		$html.='</table>';
		//发送人
		$sendEmailToUser=array('guoaijuan@gaosiedu.com','litao@gaosiedu.com','yinlianghui@gaosiedu.com','yuxiaojie@gaosiedu.com','xueshichao@gaosiedu.com','jiangdawei@gaosiedu.com');
		//$sendEmailToUser=array('zhaobin@gaosiedu.com');
		//抄送人
		$ccEmailToUser=array('matingting@gaosiedu.com','zhangchizhao@gaosiedu.com','wangyan@gaosiedu.com','maoxuesong@gaosiedu.com');
		//调用发送邮件方法
		sendMail('gaosi1vs1@163.com',$sendEmailToUser,$ccEmailToUser,$dateTime.' 教研日报',$html);
	}
	
	
	/*知识元管理*/
	public function unitManage(){
		$userKey = $this->loginUser->getUserKey();
		$key = C('xxtea_str');
		$xxtea = new Xxtea();
		$newKey = $xxtea->encrypt($userKey,$key);
		header('Location:http://yewu.aitifen.com/klib/knowledge-unit-index?userKey='.$newKey);
	}
	
	/*题模讲义管理*/
	public function textbookManage(){
		$userKey = $this->loginUser->getUserKey();
		$key = C('xxtea_str');
		$xxtea = new Xxtea();
		$newKey = $xxtea->encrypt($userKey,$key);
		header('Location:http://yewu.aitifen.com/klib/knowledge-textbook-index?userKey='.$newKey);
	}
}

?>
