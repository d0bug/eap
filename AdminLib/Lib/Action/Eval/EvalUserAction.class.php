<?php
/*用户管理*/
class EvalUserAction extends EvalCommAction{

	/*用户列表*/
	public function userList(){
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$condition = '';
		if(!empty($_REQUEST['keyword'])){
			$condition .= " `name` like '%".SysUtil::safeString(urldecode($_REQUEST['keyword']))."%' ";
		}
		$EvalModel = D('Eval');
		$userList = $EvalModel->get_userList($condition,$curPage,$pagesize);
		$count = $EvalModel->get_userCount($condition);
		$page = new page($count,$pagesize);
		$showPage = $page->show();
		$this->assign(get_defined_vars());
		$this->display();
	}


	/*成绩列表*/
	public function resultList(){
		$EvalModel = D('Eval');
		$name = $_REQUEST['name'];
		$mobile = $_REQUEST['mobile'];
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$condition = '';
		$user = array();
		$resultList = $EvalModel->get_resultList($_REQUEST,$curPage,$pagesize);
		foreach($resultList['uname'] as $k=>$v){
			$user[$k]['id'] = $v['id'] ;
			$user[$k]['name'] = $v['name'] ;
			$user[$k]['phone'] = $v['phone'] ;
			$r = $EvalModel->getResultBYuidRateID();//获得名次
			foreach($r as $rank_k=>$rank_v){
				if($rank_v['uid'] ==$v['id']){
					$user[$k]['rank'] = $rank_k +1 ;
				}
			}

			$moduleQuestionTotalcount = $myCorrectQuestionTotalCount = 0;
			foreach($resultList[$k] as $kk =>$vv){
				$moduleQuestionTotalcount  =  $moduleQuestionTotalcount + $vv['module_question_count'];//总数
				$myCorrectQuestionTotalCount = $myCorrectQuestionTotalCount + $vv['module_my_correct_question_count'];//个人正确数
				$user[$k]['modulename'][$kk]['modulecount'] = $vv['module_question_count'];
				$user[$k]['modulename'][$kk]['moduleCorrectcount'] = $vv['module_my_correct_question_count'];
			}
			$user[$k]['total'] = $moduleQuestionTotalcount;
			$user[$k]['my_correct'] = $myCorrectQuestionTotalCount;
		}
		
		$count = $EvalModel->get_userCount($condition);
		$moduleList = $EvalModel->get_moduleList();
		$page = new page($count,$pagesize);
		$showPage = $page->show();
		//$paperList = $EvalModel->get_paperList();

		$this->assign(get_defined_vars());
		$this->display();
	}


	/*导出excel*/
	public function exportExcel(){
		$EvalModel = D('Eval');
		if($_REQUEST['type']==0){
			$list = $EvalModel->get_userListAll($_REQUEST);
		}else{
			$list = $EvalModel->get_resultListAll($_REQUEST);
		}
		import("ORG.Util.Excel");
		$exceler = new Excel_Export();
		$paperInfo = $EvalModel->get_paperInfo($_REQUEST['paper_id']);
		$fileTitle = ($_REQUEST['type']==0)?'竞赛测评_用户列表':'竞赛测评_'.$paperInfo['title'].'_成绩列表';
		$dotype_name = mb_convert_encoding($fileTitle,'gbk','utf8');
		$exceler->setFileName($dotype_name.time().'.csv');
		$excel_title= ($_GET['type']==0)?array('序号','学生姓名','手机号码', '年级','学校','注册时间'):array('序号','学生姓名','正确率','评级','排名','完成时间');
		foreach ($excel_title as $key=>$title){
			$excel_title[$key] = mb_convert_encoding($title,'gbk','utf8');
		}
		$exceler->setTitle($excel_title);
		foreach ($list as $key=>$val){
			if($_REQUEST['type']==0){
				$tmp_data= array($val['id'],mb_convert_encoding($val['name'],'gbk','utf8'),$val['phone'],mb_convert_encoding($val['grade'],'gbk','utf8'),mb_convert_encoding($val['school'],'gbk','utf8'),$val['instime']);
			}else{
				$tmp_data= array($val['id'],mb_convert_encoding($val['uname'],'gbk','utf8'),sprintf('%.2f',($val['accuracy_count']/$val['question_num'])*100).'%',mb_convert_encoding($val['level'],'gbk','utf8'),$val['rank'],$val['instime']);
			}
			$exceler->addRow($tmp_data);
		}
		$exceler->export();
	}

	
	public function reportInfo(){
		if(!empty($_REQUEST['result_id'])){
			$EvalModel = D('Eval');
			$resultInfo = $EvalModel->get_resultInfo($_REQUEST['result_id']);
			$moduleList = $EvalModel->get_moduleData($_REQUEST['result_id']);
			$answerArr = C('ANSWER');
		}
		$this->assign(get_defined_vars());
		$this->display();
	}


	/*预约用户列表*/
	public function bookuserList(){
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$condition = '';
		$name = trim($_REQUEST['name']);
		$mobile = trim($_REQUEST['mobile']);
		if(!empty($name)){
			$condition .= " `name` like '%".$name."%' ";
		}
		if(!empty($mobile)){
			$condition .= " `phone` like '%".$mobile."%' ";
		}		
		$EvalModel = D('Eval');
		$bookuserList = $EvalModel->get_bookuserList($condition,$curPage,$pagesize);
		$count = $EvalModel->get_bookuserCount($condition);
		$page = new page($count,$pagesize);
		$showPage = $page->show();

		$this->assign(get_defined_vars());
		$this->display();
	}	



	/*导出预约用户excel*/
	public function bookexportExcel(){
		$EvalModel = D('Eval');
		if($_REQUEST['type']==0){
			$list = $EvalModel->get_bookuserListAll($_REQUEST);
		}
		import("ORG.Util.Excel");
		$exceler = new Excel_Export();
		
		$fileTitle = '竞赛测评_预约用户列表';
		$dotype_name = mb_convert_encoding($fileTitle,'gbk','utf8');
		$exceler->setFileName($dotype_name.time().'.csv');
		$excel_title= ($_GET['type']==0)?array('序号','学生姓名','手机号码', '年级','email','注册时间'):array('序号','学生姓名','正确率','评级','排名','完成时间');
		foreach ($excel_title as $key=>$title){
			$excel_title[$key] = mb_convert_encoding($title,'gbk','utf8');
		}
		$exceler->setTitle($excel_title);
		foreach ($list as $key=>$val){
				$tmp_data= array($val['id'],mb_convert_encoding($val['name'],'gbk','utf8'),$val['phone'],mb_convert_encoding($val['grade'],'gbk','utf8'),$val['email'],$val['instime']);
			
			$exceler->addRow($tmp_data);
		}
		$exceler->export();
	}	

}
?>