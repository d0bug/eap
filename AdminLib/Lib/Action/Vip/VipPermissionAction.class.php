<?php
/*权限管理*/
import('COM.SysUtil');
class VipPermissionAction extends VipCommAction{

	/*用户管理*/
	public function userManage(){
		$permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey());
		$is_admin = $this->checkIsAdmin();
		if(!$is_admin){
			echo '<font size=3>抱歉，您无权限进行此操作</font>';exit;
		}
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		//查询所有VIP用户（包括高思教师、社会兼职）
		$userModel = D('Users');
		$dao = $userModel->dao;
		$condition = ' 1=1 ';
		$vipPrimaryRolesId = $userModel->get_roleId(array('roleName'=>'VIP初级用户','app_name'=>'EAP','group_name'=>'Vip'));
		if(!empty($vipPrimaryRolesId)){
			$condition .= " AND r.[role_id] = '$vipPrimaryRolesId' ";
		}
		$selectField = "";
		$selectValue = "";
		if($_REQUEST){
			switch ($_REQUEST['selectType']){
				case 1:
					$condition .= " AND u.user_name = ".$dao->quote($_REQUEST['username']);
					$selectField = 1;
					$selectValue = $dao->quote($_REQUEST['username']);
					break;
				case 2:
					$condition .= ($_REQUEST['teacherType'] == 0)?" AND u.is_employee = '1'":" AND u.is_teacher = '1'";
					$selectField = 2;
					$selectValue = $_REQUEST['teacherType'];
					break;
				case 3:
					$condition .= " AND u.is_teaching_and_research = '$_REQUEST[teacherPower]' AND u.is_employee = '1'";
					$selectField = 3;
					$selectValue = $_REQUEST['teacherPower'];
					break;
				case 4:
					$condition .= " AND u.user_realname = ".$dao->quote(trim($_REQUEST['user_realname']))."";
					$selectField = 4;
					$selectValue = trim($_REQUEST['user_realname']);
					break;
			}
		}
		
		$vipUserList = $userModel->get_rolesUserList($condition,$curPage,$pagesize);
		//拥有的角色
		$user_key_str = '';
		foreach($vipUserList as $key=>$value){
			$user_key_str .= "'".$value['user_key']."',";
		}
		if($user_key_str == ''){
			header("location:/vip/vip_permission/userManage");
		}
		$roles = $userModel->get_alluserRoles(trim($user_key_str,','),'Vip',APP_NAME);
		//科目授权
		$vipSubjectModel = D('VpSubject');
		$subjectAccredit = $vipSubjectModel->get_all_subjectName(trim($user_key_str,','));
		foreach($vipUserList as $key=>$value){
			if(VipCommAction::checkIsAdmin($value['user_key'],GROUP_NAME)){
				$vipUserList[$key]['roles'] = "超级管理员, ".trim($roles[$value['user_key']],',');
				$vipUserList[$key]['subjectAccredit'] = "全部科目(管理员无需授权)";
			}else{
				$vipUserList[$key]['roles'] = trim($roles[$value['user_key']],',');
				$vipUserList[$key]['subjectAccredit'] = trim($subjectAccredit[$value['user_key']],',')?trim($subjectAccredit[$value['user_key']],','):'暂未授权';
			}
		}
		$count = $userModel->get_rolesUserCount($condition);
		$page = new Page($count,$pagesize);
		$showPage = $page->show();
		$this->assign(get_defined_vars());
		$this->display('userManage');
	}


	/*检索邮箱或用户名是否存在*/
	protected function checkThisUserIsExist(){
		$userName = isset($_GET['uname'])?trim($_GET['uname']):'';
		$userType = ($_GET['utype']==1)?USER_TYPE_EMPLOYEE:USER_TYPE_VTEACHER;
		if(!empty($userName)){
			if($userType == USER_TYPE_EMPLOYEE){
				$returnInfo = User::findUser($userType,$userName);
				$userInfo = $returnInfo[0];
				if(!empty($userInfo)){
					$userInfo['user_key'] = $userType.'-'.$userName;
					$userInfo = AppCommAction::getUserOtherInfo($userInfo);
					$userInfo['status'] = 1;
				}else{
					$userInfo['status'] = 0;
					$userInfo['msg'] = '<font color=red>没有检索到此邮箱</font>';
				}
			}else{
				$userModel = D('Users');
				$userInfo['user_key'] = $userModel->get_userKey_by_username($userName);
				if(!empty($userInfo['user_key'])){
					$userInfo['status'] = 0;
					$userInfo['msg'] = '<font color=red>用户登录名重复</font>';
				}else{
					$userInfo['status'] = 1;
					$userInfo['msg'] = '<font color=green>用户登录名可用</font>';
					$userInfo['user_key'] = $userType.'-'.$userName;
				}
			}
		}else{
			$userInfo['status'] = 0;
			$userInfo['msg'] = '<font color=red>请正确填写用户登录名</font>';
		}
		echo json_encode($userInfo);
	}


	/*添加教师*/
	protected function doAddTeacher(){
		$userInfo['user_name'] = isset($_POST['username'])?trim($_POST['username']):'';
		$userInfo['real_name'] = isset($_POST['realname'])?trim($_POST['realname']):'';
		$userInfo['user_type'] = isset($_POST['type'])?intval($_POST['type']):0;
		if($userInfo['user_type'] == 0){
			$userInfo['user_key']= 'Employee-'.$userInfo['user_name'];
		}else{
			$userInfo['user_key']= 'VTeacher-'.$userInfo['user_name'];;
		}
		$userInfo['is_removed'] = isset($_POST['isRemoved'])?intval($_POST['isRemoved']):0;
		$userInfo['user_power'] = isset($_POST['power'])?$_POST['power']:NULL;
		$userInfo['user_passwd'] = isset($_POST['passwd'])?md5(trim($_POST['passwd'])):NULL;
		$userInfo['create_user'] = $this->loginUser->getUserKey();
		$userInfo['department'] = isset($_POST['department'])?trim($_POST['department']):NULL;
		$userModel = D('Users');
		$return = array();
		$roleId = $userModel->get_roleId(array('roleName'=>'VIP初级用户','app_name'=>APP_NAME,'group_name'=>'Vip'));//VIP初级用户角色ID
		$isTeacherExist = $userModel->get_teacher_by_userInfo($userInfo,$roleId);
		$getSysUser = $userModel->get_userInfo($userInfo['user_key']);
		if($isTeacherExist==1){
			$return['status'] = 0;
			$return['msg'] = '教师已存在，不能重复添加';
		}else{
			if(!empty($getSysUser)){
				$return['status'] = 1;
				$return['msg'] = '教师添加成功';
				$return['url'] = U('Vip/VipPermission/userManage');
			}else{
				if($userModel->addTeacherUser($userInfo)){
					$return['status'] = 1;
					$return['msg'] = '教师添加成功';
					$return['url'] = U('Vip/VipPermission/userManage');
				}else{
					$return['status'] = 0;
					$return['msg'] = '教师添加失败';
				}
			}
			if($return['status']==1){
				$userModel->update_teacherPower($userInfo['user_key'],$userInfo['user_power']);
				$userModel->add_sys_user_roles(array('app_name'=>APP_NAME,'user_key'=>$userInfo['user_key'],'role_id'=>$roleId,'create_user'=>$userInfo['create_user'],'create_at'=>date('Y-m-d H:i:s')));
			}
		}
		echo json_encode($return);
	}


	/*获取教师信息*/
	protected function getTeacherInfo(){
		$userInfo = D('Users')->get_userInfo(trim($_POST['userKey']));
		$userInfo['status'] = 0;
		if(!empty($userInfo)){
			if(empty($userInfo['department'])) $userInfo['department']='无';
			if(empty($userInfo['is_teaching_and_research'])) $userInfo['is_teaching_and_research']='无';
			$userInfo['status'] = 1;
		}
		echo json_encode($userInfo);

	}


	/*编辑教师信息*/
	protected function doEditTeacher(){
		$return = array();
		$return['status'] =0;
		$return['msg'] = '教师信息修改失败';
		if(D('Users')->editUser($_POST)){
			//更新教师宣传信息表中的教师账号启用状态
			D('VpPublicity')->update_status($_POST['isRemoved'],$_POST['userKey']);
			$return['status'] =1;
			$return['msg'] = '教师信息修改成功';
			$return['url'] = U('Vip/VipPermission/userManage',array('p'=>$_POST['p']));
		}
		echo json_encode($return);
	}


	/*删除教师*/
	protected function deleteTeacher(){
		$userKeyArr =explode("_", trim($_POST['userKeyStr'],"_"));
		if(!empty($userKeyArr)){
			$userKeyStr = "'".implode("','",$userKeyArr)."'";
			$userModel = D('Users');
			$vipRolesId = $userModel->get_RolesIdByGroupName('Vip');
			foreach ($vipRolesId as $key=>$role){
				$vipRolesIdArr[] = $role['role_id'];
			}
			$vipRolesIdStr = "'".implode("','",$vipRolesIdArr)."'";
			if($userModel->delete_vipUser($userKeyStr,$vipRolesIdStr)){
				$return['status'] = 1;
				$return['url'] = U('Vip/VipPermission/userManage',array('p'=>$_GET['p']));
			}else{
				$return['status'] = 0;
			}
		}else{
			$return['status'] = 0;
		}
		echo json_encode($return);
	}


	/*科目授权*/
	protected function subjectAccredit(){
		if($_POST['submit']){
			$userKeyArr = explode('_',$_POST['userKeyStr']);
			$sidStr = implode(",",$_POST['sid']);
			if(D('VpSubject')->add_userSubject($userKeyArr,$sidStr)){
				$this->success('科目授权成功',U('Vip/VipPermission/userManage',array('p'=>$_GET['p'])));
			}else{
				$this->error('科目授权失败');
			}
		}else{
			$userKeyStr = trim($_POST['userKeyStr'],'_');
			$subjectList['handouts'] = D('VpSubject')->get_subjectList(0);
			$subjectList['itembank'] = D('VpSubject')->get_subjectList(1);
			$returnHtml = '';
			if(!empty($subjectList)){
				$returnHtml .= '<table width="90%" border=1><tr height=30 bgcolor="#dddddd"><td>科目名称</td><td>是否授权&nbsp;&nbsp;<input type="checkbox" name="checkAllSid" id="checkAllSid" value="1" onclick="checkAll(this.id)">全选</td></tr>';
				$returnHtml .= '<tr height=30 bgcolor="#dddddd"><td colspan=2><b>课程讲义科目</b></td></tr>';
				foreach ($subjectList['handouts'] as $key=>$subject){
					$returnHtml .= '<tr height=30><td>'.$subject['name'].'</td><td><input type="checkbox" name="sid[]" id="sid_'.$subject['sid'].'" value="'.$subject['sid'].'"></td></tr>';
				}
				/*$returnHtml .= '<tr height=30 bgcolor="#dddddd"><td colspan=2><b>试题库科目</b></td></tr>';
				foreach ($subjectList['itembank'] as $key=>$subject){
					$returnHtml .= '<tr height=30><td>'.$subject['name'].'</td><td><input type="checkbox" name="sid[]" id="sid_'.$subject['sid'].'" value="'.$subject['sid'].'"></td></tr>';
				}*/
				$returnHtml .= '</table><input type="hidden" id="userKeyStr" name="userKeyStr" value="'.$userKeyStr.'">';
			}
			echo $returnHtml;
		}
	}


	/*获取教师详细信息*/
	protected function vipUserInfo(){
		$return = '';
		if(!empty($_GET['user_key'])){
			$userModel = D('Users');
			$vipUserInfo = $userModel->get_userInfo($_GET['user_key']);
			if(!$vipUserInfo['department']) $vipUserInfo['department'] = '无';
			switch($vipUserInfo['is_teaching_and_research']){
				case '0':
					$vipUserInfo['teacherPower'] = '校区教师';
					break;
				case '1':
					$vipUserInfo['teacherPower'] = '教研教师';
					break;
				default:
					$vipUserInfo['teacherPower'] = '无';
			}
			$vipUserInfo['teacherType'] = ($vipUserInfo['is_employee'] == 1)?'全职教师':'社会兼职教师';
			$vipUserInfo['roles'] = $userModel->get_userRoles($_GET['user_key'],'Vip',APP_NAME);
			$is_admin = VipCommAction::checkIsAdmin($_GET['user_key'],GROUP_NAME);
			if($is_admin){
				$vipUserInfo['roles'] = "超级管理员, ".$vipUserInfo['roles'];
				$vipUserInfo['subjectAccredit'] = "全部科目(管理员无需授权)";
			}else{
				//获取教师授权的科目
				$vipSubjectModel = D('VpSubject');
				$userSubjectIdStr = $vipSubjectModel->get_thisuser_sidsStr($_GET['user_key']);
				$vipUserInfo['subjectAccredit'] = $vipSubjectModel->get_subjectNameList_by_sids($userSubjectIdStr);
			}
			echo json_encode($vipUserInfo);
		}
		echo $return;
	}
	
	/*导出Excel的信息*/
	public function exportExcel(){
		$dirPath = explode('AdminLib',dirname(__FILE__));
		include_once($dirPath[0]."Static/PHPExcel-1.7.7/Classes/PHPExcel.php");
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("zhao")
									 ->setLastModifiedBy("zhao")
									 ->setTitle("Office 2007 XLSX Test Document")
									 ->setSubject("Office 2007 XLSX Test Document")
									 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test result file");
									 
		$exportTitleInfo =array(
				'num'=>'序号',
				'login_name'=>'用户登录名',
				'user_name'=>'用户姓名',
				'teacher_type'=>'教师类型',
				'teacher_status'=>'教师身份',
				'have_role'=>'拥有身份',
				'subject_power'=>'科目权限',
				'start_status'=>'账号启用状态'
			);	
		$userModel = D('Users');
		$dao = $userModel->dao;
		$condition = ' 1=1 ';
		$vipPrimaryRolesId = $userModel->get_roleId(array('roleName'=>'VIP初级用户','app_name'=>'EAP','group_name'=>'Vip'));
		if(!empty($vipPrimaryRolesId)){
			$condition .= " AND r.[role_id] = '$vipPrimaryRolesId' ";
		}
		if($_REQUEST){
			switch ($_REQUEST['selectField']){
				case 1:
					$condition .= " AND u.user_name = ".$_REQUEST['selectValue'];
					break;
				case 2:
					$condition .= ($_REQUEST['selectValue'] == 0)?" AND u.is_employee = '1'":" AND u.is_teacher = '1'";
					break;
				case 3:
					$condition .= " AND u.is_teaching_and_research = '$_REQUEST[selectValue]' AND u.is_employee = '1'";
					break;
				case 4:
					$condition .= " AND u.user_realname = '".trim(urldecode($_REQUEST['selectValue']))."'";
					break;
			}
		}
		$vipUserList = $userModel->get_exportRolesUserList($condition);
		//拥有的角色
		$user_key_str = '';
		foreach($vipUserList as $key=>$value){
			$user_key_str .= "'".$value['user_key']."',";
		}
		$roles = $userModel->get_alluserRoles(trim($user_key_str,','),'Vip',APP_NAME);
		//科目授权
		$vipSubjectModel = D('VpSubject');
		$subjectAccredit = $vipSubjectModel->get_all_subjectName(trim($user_key_str,','));
		$exeportArray = array();
		foreach($vipUserList as $key=>$value){
			$exeportArray[$key]['num'] = $key+1;
			$exeportArray[$key]['login_name'] = $value['user_name'];
			$exeportArray[$key]['user_name'] = $value['user_realname'];
			if($value['is_employee']){
				$exeportArray[$key]['teacher_type'] = '全职教师';
			}
			if($value['is_teacher']){
				$exeportArray[$key]['teacher_type'] = '兼职教师';
			}
			if($value['is_teaching_and_research'] === '1'){
				$exeportArray[$key]['teacher_status'] = '教研教师';
			}else if($value['is_teaching_and_research']==='0'){
				$exeportArray[$key]['teacher_status'] = '校区教师';
			}else{
				$exeportArray[$key]['teacher_status'] = '无';
			}
			
			if(VipCommAction::checkIsAdmin($value['user_key'],GROUP_NAME)){
				$exeportArray[$key]['have_role'] = "超级管理员, ".trim($roles[$value['user_key']],',');
				$exeportArray[$key]['subject_power'] = "全部科目(管理员无需授权)";
			}else{
				$exeportArray[$key]['have_role'] = trim($roles[$value['user_key']],',');
				$exeportArray[$key]['subject_power'] = trim($subjectAccredit[$value['user_key']],',')?trim($subjectAccredit[$value['user_key']],','):'暂未授权';
			}
			if($value['is_removed']==1){
				$exeportArray[$key]['start_status'] = "已禁用";
			}else{
				$exeportArray[$key]['start_status'] = "已启用";
			}
		}
		array_unshift($exeportArray,array_values($exportTitleInfo));
		$objPHPExcel->getActiveSheet(0)->setTitle('用户管理信息');
		for($i=0;$i<count($exeportArray);$i++){
			$j=0;
			foreach($exeportArray[$i] as $k=>$v){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($j+65).($i+1),$v);
				$j++;
			}
		}
		$objPHPExcel->setActiveSheetIndex(0);    
		$objActSheet = $objPHPExcel->getActiveSheet(); 
		$objActSheet->getColumnDimension('A')->setWidth(10);
		$objActSheet->getColumnDimension('B')->setWidth(13);
		$objActSheet->getColumnDimension('C')->setWidth(10);
		$objActSheet->getColumnDimension('D')->setWidth(10);
		$objActSheet->getColumnDimension('E')->setWidth(10);
		$objActSheet->getColumnDimension('F')->setWidth(30);
		$objActSheet->getColumnDimension('G')->setWidth(40);
		$objActSheet->getColumnDimension('H')->setWidth(10);

		ob_end_clean();
		header ('Pragma: public'); // HTTP/1.0
		header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
		header('Content-Disposition: attachment;filename=用户管理信息表.xls');
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