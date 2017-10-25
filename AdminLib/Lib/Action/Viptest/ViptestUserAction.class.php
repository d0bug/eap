<?php
/*用户管理*/
class ViptestUserAction extends ViptestCommAction{

	/*试卷列表*/
	public function userList(){
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$condition = '';
		if(!empty($_REQUEST['keyword'])){
			$condition .= " `name` like '%".SysUtil::safeString(urldecode($_REQUEST['keyword']))."%' ";
		}
		$vipTestModel = D('Viptest');
		$userList = $vipTestModel->get_userList($condition,$curPage,$pagesize);
		$count = $vipTestModel->get_userCount($condition);
		$page = new page($count,$pagesize);
		$showPage = $page->show();

		$this->assign(get_defined_vars());
		$this->display();
	}


	/*成绩列表*/
	public function resultList(){
		$vipTestModel = D('Viptest');
		if($_REQUEST['paper_id']){
			import("ORG.Util.Page");
			$curPage = isset($_GET['p'])?abs($_GET['p']):1;
			$pagesize = C('PAGESIZE');
			$condition = '';
			
			$resultList = $vipTestModel->get_resultList($_REQUEST,$curPage,$pagesize);
			$count = $vipTestModel->get_resultCount($_REQUEST);
			$page = new page($count,$pagesize);
			$showPage = $page->show();
		}
		$paperList = $vipTestModel->get_paperList();

		$this->assign(get_defined_vars());
		$this->display();
	}


	/*导出excel*/
	public function exportExcel(){
		$vipTestModel = D('Viptest');
		if($_REQUEST['type']==0){
			$list = $vipTestModel->get_userListAll($_REQUEST);
		}else{
			$list = $vipTestModel->get_resultListAll($_REQUEST);
		}
		import("ORG.Util.Excel");
		$exceler = new Excel_Export();
		$paperInfo = $vipTestModel->get_paperInfo($_REQUEST['paper_id']);
		$fileTitle = ($_REQUEST['type']==0)?'早培神测_用户列表':'早培神测_'.$paperInfo['title'].'_成绩列表';
		$dotype_name = mb_convert_encoding($fileTitle,'gbk','utf8');
		$exceler->setFileName($dotype_name.time().'.csv');
		$excel_title= ($_GET['type']==0)?array('序号','学生姓名','手机号码', '注册时间'):array('序号','学生姓名','正确率','评级','排名','完成时间');
		foreach ($excel_title as $key=>$title){
			$excel_title[$key] = mb_convert_encoding($title,'gbk','utf8');
		}
		$exceler->setTitle($excel_title);
		foreach ($list as $key=>$val){
			if($_REQUEST['type']==0){
				$tmp_data= array($val['id'],mb_convert_encoding($val['name'],'gbk','utf8'),$val['phone'],$val['instime']);
			}else{
				$tmp_data= array($val['id'],mb_convert_encoding($val['uname'],'gbk','utf8'),sprintf('%.2f',($val['accuracy_count']/$val['question_num'])*100).'%',mb_convert_encoding($val['level'],'gbk','utf8'),$val['rank'],$val['instime']);
			}
			$exceler->addRow($tmp_data);
		}
		$exceler->export();
	}

	
	public function reportInfo(){
		if(!empty($_REQUEST['result_id'])){
			$vipTestModel = D('Viptest');
			$resultInfo = $vipTestModel->get_resultInfo($_REQUEST['result_id']);
			$moduleList = $vipTestModel->get_moduleData($_REQUEST['result_id']);
			$answerArr = C('ANSWER');
		}
		$this->assign(get_defined_vars());
		$this->display();
	}

}
?>