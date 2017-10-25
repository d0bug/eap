<?php
/*试卷管理*/
class EvalPaperAction extends EvalCommAction{
	protected function notNeedLogin() {
		return array('EVAL-EVALPAPER-UPLOADIMG','EVAL-EVALPAPER-UPLOADDOCUMENT');
	}

	/*试卷列表*/
	public function paperList(){
		$EvalModel = D('Eval');
		$paperList = $EvalModel->get_paperList();
		$moduleList = $EvalModel->get_moduleList();
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$difficulty = C ( 'QUE_DIFFICULTY' ); // 试题难度
		$questionList = $EvalModel->get_questionList($_POST,$curPage,$pagesize);
		//print_r($questionList);
		$count = $EvalModel->get_questionCount($_POST);
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
		$EvalModel = D('Eval');
		$paperList = $EvalModel->get_paperList();
		if($_REQUEST['paper_id']){
			$paperInfo = $EvalModel->get_paperInfo($_REQUEST['paper_id']);
			$levelArr = unserialize($paperInfo['level_str']);
			//$levelArr[count($levelArr)] = array('name' => '未完成测试','low' => 0,'desc' => '未完成测试');
			$moduleArr = $EvalModel->get_papermoduleList(array('paper_id'=>$_REQUEST['paper_id']));
			$resultList = $EvalModel->get_resultListAll($_REQUEST);
			$total = count($resultList);
			$accuracy_count_avg = $EvalModel->get_resultAccuracyAvg($_REQUEST);
			$accuracy_avg = sprintf('%.2f',($accuracy_count_avg/($total*$paperInfo['question_num']))*100);
			if(!empty($resultList)){
				foreach ($resultList as $key=>$result){
					if($result['score']>0){
						foreach($levelArr as $kk=>$v){	
							if($result['score'] > $v['low'] && $result['score']<=$v['up']){
									$levelArr[$kk]['count']++;
								}
						}
					}else{
						$levelArr[count($levelArr)-1]['count']++;
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
				$moduleQuestionArr = $EvalModel->get_moduleQuestion($_REQUEST['paper_id']);
				foreach ($moduleArr as $key=>$module){
					
					$moduleArr[$key]['question_num'] = 0;
					if(!empty($moduleQuestionArr)){
						foreach ($moduleQuestionArr as $kk=>$moduleQuestion){
							//echo $moduleQuestion;
							if($moduleQuestion['module_id'] == $module['module_id']){
								$moduleArr[$key]['question_num'] = $moduleQuestion['question_num'];
								$moduleArr[$key]['name'] = $module['module_name'];
							}
						}
					}
					$moduleRecord = $EvalModel->get_moduleRecord(array('paper_id'=>$_REQUEST['paper_id'],'module_id'=>$module['module_id']));
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
	public function addPaper(){
		$paperForm = $chk = $checked = '';
		$EvalModel = D('Eval');
		$paper = array();
		$levelList = array();
		$moduleInfo = $EvalModel->get_moduleList();
		if(!empty($_GET['paper_id'])){
			$paperInfo = $EvalModel->get_onepaper($_GET['paper_id']);
			$paperInfo['show_url'] = str_replace('/Upload/','/upload/',$paperInfo['document']);
			$contain_module = explode(',',trim($paperInfo['contain_module']));
			$levelList = unserialize($paperInfo['level_str']);
		}
		$this->assign(get_defined_vars());
		$this->display();
	}


	/*执行添加or编辑 试卷*/
	public function doAddPaper(){
		if($_POST){
			$arr = $_POST;
			for($i=0;$i<$_POST['level_num'];$i++){
				if(!empty($_POST['name'][$i])){
					$arr['level_arr'][$i]['name'] = SysUtil::safeString($_POST['name'][$i]);
					$arr['level_arr'][$i]['up'] = abs($_POST['up'][$i]);
					$arr['level_arr'][$i]['low'] = abs($_POST['low'][$i]);
					$arr['level_arr'][$i]['desc'] = SysUtil::safeString($_POST['desc'][$i]);
				}
			}
			$arr['level_str'] = serialize($arr['level_arr']);
			unset($arr['name']);
			unset($arr['up']);
			unset($arr['low']);
			unset($arr['desc']);
			unset($arr['level_num']);

			if(!empty($arr['contain_module'])){
				foreach($arr['contain_module'] as $k=>$v){
					$arr['module_name'] .= trim($v).',';
				}

				$arr['module_name'] = trim($arr['module_name'],',');
			}

			$arr['contain_module'] = $arr['module_name'];
			//print_r($arr);exit;
			$EvalModel = D('Eval');
			if($_POST['id']){
				$result = $EvalModel->editPaper($arr);
				$operate = '编辑';
			}else{
				$result = $EvalModel->addPaper($arr);
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
			if(D('Eval')->deletePaper($_GET['id'])){
				$this->success('试卷删除成功');
			}else{
				$this->error('试卷删除失败');
			}
		}else{
			$this->error('非法操作');
		}
	}

	/**
	*设置关键题
	**/
	public function key_question(){
		if(!empty($_POST['question_id'])){
			if(D('Eval')->updatequestion($_POST['question_id'],$_POST['key'],$_POST['module_id'])){
				echo '1';
			}else{
				echo '0';
			}

		}else{
			echo '0';
		}
	}

	/*添加or编辑 模块*/
	public function addModule(){
		$moduleInfo = array();
		$EvalModel = D('Eval');
		if(!empty($_GET['module_id'])){
			$moduleInfo = $EvalModel->get_moduleInfo($_GET['module_id']);
			$moduleForm = '<script type="text/javascript" src="/static/js/eval.js"></script>
						  <form method="POST" action="'.U('Eval/EvalPaper/doAddModule').'" onsubmit="return check_addModule()">
							<p><input type="hidden" id="id" name="id" value="'.$moduleInfo['id'].'">
							</p>
							<p><font color=red>*</font>模块名称：<input type="text" id="name" name="name" value="'.$moduleInfo['name'].'"></p><br/>
							<p><font color=red>*</font>关键题数量：<input type="text" id="key_num" name="key_num" value="'.$moduleInfo['key_num'].'"></p><br/>
							<p><font color=red>*</font>评&nbsp;&nbsp;优&nbsp;&nbsp;率：较强：<input type="text" id="excellent_strong" name="excellent_strong" value="'.$moduleInfo['excellent_strong'].'" size="5">%&nbsp;&nbsp;　较弱：<input type="text" id="excellent_weak" name="excellent_weak" value="'.$moduleInfo['excellent_weak'].'" size="5">%</p><br/>
							<p><input type=submit value="保存" class="btn">';
			if(!empty($_GET['module_id'])) $moduleForm .= '　　　<a href="'.U('Eval/EvalPaper/deleteModule',array('id'=>$_GET['module_id'])).'" class="blue" onclick="return confirm(\'删除模块将同时删除相关试题，成绩，答题记录等，\n确认要删除该模块吗？\')">删除该模块</a>';
			$moduleForm .= '</p></form>';
		}else{

			$moduleForm = '<script type="text/javascript" src="/static/js/eval.js"></script>
					  <form method="POST" action="'.U('Eval/EvalPaper/doAddModule').'" onsubmit="return check_addModule()">
						<p><font color=red>*</font>模块名称：<input type="text" id="name" name="name" ></p><br/>
						<p><font color=red>*</font>关键题数量：<input type="text" id="key_num" name="key_num"></p><br/>
						<p><font color=red>*</font>评&nbsp;&nbsp;优&nbsp;&nbsp;率：较强：<input type="text" id="excellent_strong" name="excellent_strong" size="5">%&nbsp;&nbsp;　较弱：<input type="text" id="excellent_weak" name="excellent_weak"size="5">%</p><br/>
						<p><input type=submit value="保存" class="btn">';
			$moduleForm .= '</p></form>';		
		}


		echo $moduleForm;
	}


	/*执行添加or编辑 模块*/
	public function doAddModule(){
		if($_POST){
			$EvalModel = D('Eval');
			if($_POST['id']){
				$result = $EvalModel->editModule($_POST);
				$operate = '编辑';
			}else{
				$result = $EvalModel->addModule($_POST);
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
			if(D('Eval')->deleteModule($_GET['id'])){
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
		$EvalModel = D('Eval');
		$eval_difficulty = C ( 'QUE_DIFFICULTY' ); // 试题难度
		$questionInfo = $EvalModel->get_questionInfo($_GET['id']);

		$moudle_id = isset($questionInfo['module_id']) ?  $questionInfo['module_id'] : $_GET['module_id'];
		$paperList = $EvalModel->get_paperList();
		//'paper_id'=>$questionInfo['paper_id']
		$moduleList = $EvalModel->get_moduleList(array('paper_id'=>''));
		$answerArr = C('ANSWER');

		if($_POST){
			if($_POST['id']){
				$result = $EvalModel->editQuestion($_POST);
			}else{
				$result = $EvalModel->addQuestion($_POST);
			}
			if($result==true){
				echo 1;
			}else{
				echo 0;
			}
		}else{
			$paperInfo = $EvalModel->get_paperInfo($_GET['paper_id']);
			$questionNum = $EvalModel->get_questionCount(array('paper_id'=>$_GET['paper_id']));
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
				//$getModuleUrl = $this->getUrl('json_moduleList');
				$this->assign(get_defined_vars());
				$this->display();
			}
		}
	}





	/*删除试题*/
	public function deleteQuestion(){
		if(!empty($_GET['id'])){
			$EvalModel = D('Eval');
			$questionInfo = $EvalModel->get_questionInfo($_GET['id']);
			if($EvalModel->deleteQuestion($_GET['id'])){
				@unlink(APP_DIR.$questionInfo['img']);
				@unlink(APP_DIR.$questionInfo['analysis']);
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
			$EvalModel = D('Eval');
			$questionInfo = $EvalModel->get_questionInfo($_GET['id']);
			$answerArr = C('ANSWER');
			foreach ($answerArr as $key=>$answer){
				if($key < $questionInfo['option_num'] && $key != $questionInfo['answer']){
					$newAnswerArr[$key]['val'] = $answer;
					$newAnswerArr[$key]['count'] = 0;
				}
			}
			$newAnswerArr[$questionInfo['option_num']]['val'] = '未作答';
			$newAnswerArr[$questionInfo['option_num']]['count'] = 0;
			$recordList = $EvalModel->get_recordList(array('question_id'=>$_GET['id']));
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
		$EvalModel = D('Eval');
		//$_GET
		$moduleList = $EvalModel->get_moduleList();
		$moduleHtml = '<option value="">请选择模块</option>';
		if(!empty($moduleList)){
			foreach ($moduleList as $key=>$module){
				$moduleHtml .= '<option value="'.$module['id'].'">'.$module['name'].'</option>';
			}
		}
		$operate = '';
		if(!empty($_GET['paper_id'])){
			$operate .= '<a href="#" onclick="testMessageBox_paperForm(event,\'edit\',\''.U('Eval/EvalPaper/addPaper',array('paper_id'=>$_GET['paper_id'])).'\');" class="blue">编辑试卷</a>';
		}
		echo json_encode(array('moduleHtml'=>$moduleHtml,'operate'=>$operate));
	}

	/**
	*删除试题题干
	**/
	public function delImg(){
		if(!empty($_POST['url'])){
			@unlink(APP_DIR.$_POST['url']);
			if(!file_exists(APP_DIR.$_POST['url'])){
				echo '1';die;
			}else{
				echo '0';die;
			}
		}else{
			echo '0';die;
		}
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


	public function whole_level(){

			$EvalModel = D('Eval');
			$LevelInfo = $EvalModel->get_LevelInfo();
			$whole_level =  unserialize($LevelInfo['whole_level']);
			$level_num  = count($whole_level);
			if($_POST){
				$arr = $_POST;
				for($i=0;$i<count($_POST['name']);$i++){
					if(!empty($_POST['name'][$i])){
						$arr['whole_level'][$i]['name'] = SysUtil::safeString($_POST['name'][$i]);
						$arr['whole_level'][$i]['up'] = abs($_POST['up'][$i]);
						$arr['whole_level'][$i]['low'] = abs($_POST['low'][$i]);
						$arr['whole_level'][$i]['intro'] = trim($_POST['intro'][$i]);
					}
				}
			

			$arr['whole_level'] = serialize($arr['whole_level']);
			unset($arr['name']);
			unset($arr['up']);
			unset($arr['low']);
			unset($arr['desc']);
			unset($arr['level_num']);

			if(empty($_POST['id'])){
				$result = $EvalModel->AddWholeLevel($arr);
				$operate = '添加';
			}else{
				$result = $EvalModel->EditWholeLevel($arr);
				$operate = '编辑';
			}
			if($result==true){
				$this->success('层级'.$operate.'成功');
			}else{
				$this->error('层级'.$operate.'失败');
			}
		}

		$this->assign(get_defined_vars());
		$this->display();	
	}


	/*试卷PDF在线预览*/
	public function preview_pdf(){
		$paperId = isset($_GET['paper_id'])?intval($_GET['paper_id']):0;
		if(!empty($paperId)){
			$EvalModel = D('Eval');
			$paperInfo = $EvalModel->get_paperInfo($paperId);
			$source_url = !empty($paperInfo['document'])?$paperInfo['document']:'';
			$swf_url = APP_DIR.$source_url;
			$is_exists = 1;
			if(!file_exists($swf_url)){
				$is_exists = 0;
			}
			$swf_url = strtolower(end(explode('/eap',$swf_url)));
		}
		$swf_url  = str_replace('/upload', '/Upload', $swf_url);
		$swf_url = 'http://eap.local.com'.$swf_url;
		$this->assign(get_defined_vars());
		$this->display();
	}	

}
?>