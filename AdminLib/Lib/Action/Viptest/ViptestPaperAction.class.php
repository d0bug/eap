<?php
/*试卷管理*/
class ViptestPaperAction extends ViptestCommAction{
	protected function notNeedLogin() {
		return array('VIPTEST-VIPTESTPAPER-UPLOADIMG');
	}


	/*试卷列表*/
	public function paperList(){
		$vipTestModel = D('Viptest');
		$paperList = $vipTestModel->get_paperList();
		$moduleList = $vipTestModel->get_moduleList(array('paper_id'=>$_REQUEST['paper_id']));
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$questionList = $vipTestModel->get_questionList($_POST,$curPage,$pagesize);
		$count = $vipTestModel->get_questionCount($_POST);
		$page = new page($count,$pagesize);
		$showPage = $page->show();
		$getModuleUrl = $this->getUrl('json_moduleList');
		$getQuestionUrl = $this->getUrl('json_questionList');
		$addPaperUrl = $this->getUrl('addPaper');
		$addModuleUrl = $this->getUrl('addModule');
		$addQuestionUrl = $this->getUrl('addQuestion');

		$this->assign(get_defined_vars());
		$this->display();
	}


	/*统计数据*/
	public function statisticData(){
		$vipTestModel = D('Viptest');
		$paperList = $vipTestModel->get_paperList();
		if($_REQUEST['paper_id']){
			$paperInfo = $vipTestModel->get_paperInfo($_REQUEST['paper_id']);
			$levelArr = unserialize($paperInfo['level_str']);
			$levelArr[count($levelArr)] = array('name' => '未完成测试','low' => 0,'desc' => '未完成测试');
			$moduleArr = $vipTestModel->get_moduleList(array('paper_id'=>$_REQUEST['paper_id']));
			$resultList = $vipTestModel->get_resultListAll($_REQUEST);
			$total = count($resultList);
			$accuracy_count_avg = $vipTestModel->get_resultAccuracyAvg($_REQUEST);
			$accuracy_avg = sprintf('%.2f',($accuracy_count_avg/$paperInfo['question_num'])*100);
			if(!empty($resultList)){
				foreach ($resultList as $key=>$result){
					foreach ($levelArr as $kk=>$level){
						if($level['name'] == $result['level']){
							$levelArr[$kk]['count']++;
						}
						if($result['level'] == '' && ($kk == count($levelArr)-1)){
							$levelArr[$kk]['count']++;
						}
					}
				}
			}
			if(!empty($levelArr)){
				foreach ($levelArr as $key=>$level){
					if(!$level['count']){
						$levelArr[$key]['count'] = 0;
					}
					$levelArr[$key]['percentage'] = sprintf('%.2f',($levelArr[$key]['count']/$total)*100);
				}
			}
			if(!empty($moduleArr)){
				$moduleQuestionArr = $vipTestModel->get_moduleQuestion($_REQUEST['paper_id']);
				foreach ($moduleArr as $key=>$module){
					$moduleArr[$key]['question_num'] = 0;
					if(!empty($moduleQuestionArr)){
						foreach ($moduleQuestionArr as $kk=>$moduleQuestion){
							if($moduleQuestion['module_id'] == $module['id']){
								$moduleArr[$key]['question_num'] = $moduleQuestion['question_num'];
							}
						}
					}
					$moduleRecord = $vipTestModel->get_moduleRecord(array('paper_id'=>$_REQUEST['paper_id'],'module_id'=>$module['id']));
					if(!empty($moduleRecord)){
						foreach ($moduleRecord as $kk=>$record){
							if($record['is_correct']==1){
								$moduleArr[$key]['correct'] = $record['num'];
							}else{
								$moduleArr[$key]['error'] = $record['num'];
							}
						}
					}
				}
			}
		}
		$this->assign(get_defined_vars());
		$this->display();
	}


	/*添加or编辑 试卷*/
	protected function addPaper(){
		$paperInfo = array();
		$vipTestModel = D('Viptest');
		$levelList = array();
		if(!empty($_GET['paper_id'])){
			$paperInfo = $vipTestModel->get_paperInfo($_GET['paper_id']);
			$levelList = unserialize($paperInfo['level_str']);
		}
		$paperForm = '<script type="text/javascript" src="/static/js/viptest.js"></script>
					  <form method="POST" action="'.U('Viptest/ViptestPaper/doAddPaper').'" onsubmit="return check_addPaper()">
						<p><input type="hidden" id="id" name="id" value="'.$paperInfo['id'].'"></p>
						<p><font color=red>*</font>试卷名称：<input type="text" id="title" name="title" value="'.$paperInfo['title'].'" size="50"></p>
						<p><font color=red>*</font>试题数量：<input type="text" id="question_num" name="question_num" value="'.$paperInfo['question_num'].'"></p>
						<p><font color=red>*</font>评级标准：<input type=hidden id="level_num" name="level_num" value="'.count($levelList).'">
						<a href="#" onclick="add_level(\'#levelSpan\')"><img src="/static/images/add.png"></a><div id="levelSpan" style="margin-left:50px;">';
		if(!empty($levelList)){
			foreach ($levelList as $key=>$level){
				$paperForm .= '<span id="span_level_'.($key+1).'">
								   名称：<input type="text" id="level_'.($key+1).'" name="name[]" value="'.$level['name'].'" size="10">&nbsp;&nbsp;
								   下限：<input type="text" id="level_'.($key+1).'" name="low[]" value="'.$level['low'].'" size="5">&nbsp;&nbsp;
								   说明：<input type="text" id="level_'.($key+1).'" name="desc[]" value="'.$level['desc'].'" size="20">&nbsp;&nbsp;
								   <a href="#" onclick="del_level(\'#span_level_'.($key+1).'\')"><img src="/static/images/delete.png"></a><br></span>';
			}
		}
		$paperForm .= '</div></p>
						<p><font color=red>*</font>采用正确率：<input type="radio" id="is_accuracy" name="is_accuracy" value="1" ';
		if($paperInfo['is_accuracy'] == 1) $paperForm .= ' checked ';
		$paperForm .= '>虚拟正确率&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="is_accuracy" name="is_accuracy" value="0" ';
		if($paperInfo['is_accuracy'] == 0) $paperForm .= ' checked ';
		$paperForm .= '>真实正确率
						</p>
						<p><font color=red>*</font>试卷状态：<input type="radio" id="status" name="status" value="1" ';
		if((!empty($paperInfo) && $paperInfo['status'] == 1 )||empty($_GET['paper_id'])) $paperForm .= ' checked ';
		$paperForm .='>启用&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="status" name="status" value="0" ';
		if(!empty($paperInfo) && $paperInfo['status'] == 0) $paperForm .= ' checked ';
		$paperForm .='>停用</p>
						<p><input type=submit value="保存" class="btn">';
		if(!empty($paperInfo['id'])) $paperForm .= '　　　<a href="'.U('Viptest/ViptestPaper/deletePaper',array('id'=>$paperInfo['id'])).'" class="blue" onclick="return confirm(\'删除试卷将同时删除相关模块、试题，成绩，答题记录等，\n确认要删除该试卷吗？\')">删除该试卷</a>';
		$paperForm .= '</p>
					  </form>';
		echo $paperForm;
	}


	/*执行添加or编辑 试卷*/
	public function doAddPaper(){
		if($_POST){
			$arr = $_POST;
			for($i=0;$i<$_POST['level_num'];$i++){
				if(!empty($_POST['name'][$i])){
					$arr['level_arr'][$i]['name'] = SysUtil::safeString($_POST['name'][$i]);
					$arr['level_arr'][$i]['low'] = abs($_POST['low'][$i]);
					$arr['level_arr'][$i]['desc'] = SysUtil::safeString($_POST['desc'][$i]);
				}
			}
			$arr['level_str'] = serialize($arr['level_arr']);
			unset($arr['name']);
			unset($arr['low']);
			unset($arr['desc']);
			unset($arr['level_num']);
			$vipTestModel = D('Viptest');
			if($_POST['id']){
				$result = $vipTestModel->editPaper($arr);
				$operate = '编辑';
			}else{
				$result = $vipTestModel->addPaper($arr);
				$operate = '添加';
			}
			if($result==true){
				$this->success('试卷'.$operate.'成功');
			}else{
				$this->error('试卷'.$operate.'失败');
			}
		}else{
			$this->error('非法操作');
		}
	}


	/*删除试卷*/
	public function deletePaper(){
		if(!empty($_GET['id'])){
			if(D('Viptest')->deletePaper($_GET['id'])){
				$this->success('试卷删除成功');
			}else{
				$this->error('试卷删除失败');
			}
		}else{
			$this->error('非法操作');
		}
	}


	/*添加or编辑 模块*/
	public function addModule(){
		$moduleInfo = array();
		$vipTestModel = D('Viptest');
		$moduleInfo = $vipTestModel->get_moduleInfo($_GET['module_id']);
		$moduleForm = '<script type="text/javascript" src="/static/js/viptest.js"></script>
					  <form method="POST" action="'.U('Viptest/ViptestPaper/doAddModule').'" onsubmit="return check_addModule()">
						<p><input type="hidden" id="id" name="id" value="'.$moduleInfo['id'].'">
						   <input type="hidden" id="paper_id" name="paper_id" value="'.$_GET['paper_id'].'">
						</p>
						<p><font color=red>*</font>模块名称：<input type="text" id="name" name="name" value="'.$moduleInfo['name'].'"></p>
						<p><font color=red>*</font>评&nbsp;&nbsp;优&nbsp;&nbsp;率：较强：<input type="text" id="excellent_strong" name="excellent_strong" value="'.$moduleInfo['excellent_strong'].'" size="5">%&nbsp;&nbsp;　较弱：<input type="text" id="excellent_weak" name="excellent_weak" value="'.$moduleInfo['excellent_weak'].'" size="5">%</p>
						<p><font color=red>*</font>虚拟正确率：<input type="text" id="accuracy" name="accuracy" value="'.$moduleInfo['accuracy'].'">%</p>
						<p><input type=submit value="保存" class="btn">';
		if(!empty($_GET['module_id'])) $moduleForm .= '　　　<a href="'.U('Viptest/ViptestPaper/deleteModule',array('id'=>$_GET['module_id'])).'" class="blue" onclick="return confirm(\'删除模块将同时删除相关试题，成绩，答题记录等，\n确认要删除该模块吗？\')">删除该模块</a>';
		$moduleForm .= '</p>
					  </form>';
		echo $moduleForm;
	}


	/*执行添加or编辑 模块*/
	public function doAddModule(){
		if($_POST){
			$vipTestModel = D('Viptest');
			if($_POST['id']){
				$result = $vipTestModel->editModule($_POST);
				$operate = '编辑';
			}else{
				$result = $vipTestModel->addModule($_POST);
				$operate = '添加';
			}
			if($result==true){
				$this->success('模块'.$operate.'成功');
			}else{
				$this->error('模块'.$operate.'失败');
			}
		}else{
			$this->error('非法操作');
		}
	}


	/*删除模块*/
	public function deleteModule(){
		if(!empty($_GET['id'])){
			if(D('Viptest')->deleteModule($_GET['id'])){
				$this->success('模块删除成功');
			}else{
				$this->error('模块删除失败');
			}
		}else{
			$this->error('非法操作');
		}
	}


	/*添加or编辑 试题*/
	public function addQuestion(){
		$vipTestModel = D('Viptest');
		$questionInfo = $vipTestModel->get_questionInfo($_GET['id']);
		$paperList = $vipTestModel->get_paperList();
		$moduleList = $vipTestModel->get_moduleList(array('paper_id'=>$questionInfo['paper_id']));
		$answerArr = C('ANSWER');
		if($_POST){
			if($_POST['id']){
				$result = $vipTestModel->editQuestion($_POST);
			}else{
				$result = $vipTestModel->addQuestion($_POST);
			}
			if($result==true){
				echo 1;
			}else{
				echo 0;
			}
		}else{
			$paperInfo = $vipTestModel->get_paperInfo($_GET['paper_id']);
			$questionNum = $vipTestModel->get_questionCount(array('paper_id'=>$_GET['paper_id']));
			if(empty($questionInfo) && $questionNum >= $paperInfo['question_num']){
				echo '所属试卷试题已满，不能继续添加';die;
			}else{
				$defaultSeq = !empty($questionInfo['seq'])?$questionInfo['seq']:$questionNum+1;
				$option_num = !empty($questionInfo['option_num'])?$questionInfo['option_num']:4;
				foreach ($answerArr as $key=>$answer){
					if($key >= $option_num){
						unset($answerArr[$key]);
					}
				}
				$getModuleUrl = $this->getUrl('json_moduleList');
				$this->assign(get_defined_vars());
				$this->display();
			}
		}
	}





	/*删除试题*/
	public function deleteQuestion(){
		if(!empty($_GET['id'])){
			$vipTestModel = D('Viptest');
			$questionInfo = $vipTestModel->get_questionInfo($_GET['id']);
			if($vipTestModel->deleteQuestion($_GET['id'])){
				@unlink(APP_DIR.$questionInfo['img']);
				$this->success('试题删除成功');
			}else{
				$this->error('试题删除失败');
			}
		}else{
			$this->error('非法操作');
		}
	}


	/*查看统计*/
	public function statisticInfo(){
		if(!empty($_GET['id'])){
			$vipTestModel = D('Viptest');
			$questionInfo = $vipTestModel->get_questionInfo($_GET['id']);
			$answerArr = C('ANSWER');
			foreach ($answerArr as $key=>$answer){
				if($key < $questionInfo['option_num'] && $key != $questionInfo['answer']){
					$newAnswerArr[$key]['val'] = $answer;
					$newAnswerArr[$key]['count'] = 0;
				}
			}
			$newAnswerArr[$questionInfo['option_num']]['val'] = '未作答';
			$newAnswerArr[$questionInfo['option_num']]['count'] = 0;
			$recordList = $vipTestModel->get_recordList(array('question_id'=>$_GET['id']));
			$total = count($recordList);
			$correct_num = $error_num = $correct_percentage = $error_percentage = 0;
			if(!empty($recordList)){
				foreach ($recordList as $key=>$record){
					if($record['answer'] == $questionInfo['answer']){
						$correct_num++;
					}else{
						$error_num++;
					}
					if($record['answer'] == -1) $newAnswerArr[$questionInfo['option_num']]['count']++;
					foreach ($newAnswerArr as $kk=>$answer){
						if($answer['val'] == $answerArr[$record['answer']]) $newAnswerArr[$kk]['count']++;
					}
				}
			}
			$correct_percentage = round(($correct_num/$total)*100);
			$error_percentage = round(($error_num/$total)*100);
		}
		$this->assign(get_defined_vars());
		$this->display();
	}


	protected  function json_moduleList(){
		$vipTestModel = D('Viptest');
		$moduleList = $vipTestModel->get_moduleList($_GET);
		$moduleHtml = '<option value="">请选择模块</option>';
		if(!empty($moduleList)){
			foreach ($moduleList as $key=>$module){
				$moduleHtml .= '<option value="'.$module['id'].'">'.$module['name'].'</option>';
			}
		}
		$operate = '';
		if(!empty($_GET['paper_id'])){
			$operate .= '<a href="#" onclick="testMessageBox_paperForm(event,\'edit\',\''.U('Viptest/ViptestPaper/addPaper',array('paper_id'=>$_GET['paper_id'])).'\');" class="blue">编辑试卷</a>';
		}
		echo json_encode(array('moduleHtml'=>$moduleHtml,'operate'=>$operate));
	}


	protected function json_answerArr(){
		$answerHtml = '';
		$answerArr = C('ANSWER');
		foreach ($answerArr as $key=>$answer){
			if($key < $_GET['option_num']){
				$answerHtml .= '<option value="'.$key.'">'.$answer.'</option>';
			}
		}
		echo $answerHtml;
	}
}
?>