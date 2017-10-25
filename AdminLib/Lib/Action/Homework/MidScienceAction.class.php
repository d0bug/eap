<?php
class MidScienceAction extends HomeworkCommAction {


	protected function notNeedLogin() {
		return array (
			'HOMEWORK-MIDSCIENCE-UPLOADSSOLUTIONPICS'
		);
	}

	// $this->writeCheck($this->getAclKey(''));Homework/MidScience

	/*
	 * 创建新作业
	 */
	public function create() {
		import ( 'ORG.Util.Input' );
		$deptcode = Input::getVar ( $_GET ['deptcode'] );
		$semesterid = Input::getVar ( $_GET ['semesterid'] );
		$classtypecode = Input::getVar ( $_GET ['classtypecode'] );

		$year = date ( 'Y' );

		$wxhwModel = D ( 'WeixinHomework' );

		$xueqi0Arr = $wxhwModel->getXueqi ();
		$xueqiArr = array ();
		foreach ( $xueqi0Arr as $xueqi ) {
			$xueqiArr [$xueqi ['id']] = $xueqi;
		}
		$dept0Arr = $wxhwModel->getDepts ();
		$deptArr = array ();
		foreach ( $dept0Arr as $dept ) {
			$deptArr [$dept ['sdeptcode']] = $dept;
		}

		$classtypeArr = array ();
		if (isset ( $deptArr [$deptcode] ) && $semesterid) {
			$oneDeptArr = $deptArr [$deptcode];
			$classtype0Arr = $wxhwModel->getClassType ( $year, $semesterid, $oneDeptArr ['nxuebu'], $oneDeptArr ['nxueke'] );
			foreach ( $classtype0Arr as $classtype ) {
				$classtypeArr [$classtype ['scode']] = $classtype;
			}
		}

		$jiangciArr = array ();
		if (isset ( $classtypeArr [$classtypecode] ['nlesson'] )) {
			$lesson = $classtypeArr [$classtypecode] ['nlesson'];
			$jiangciArr = range ( 1, $lesson );
		}

		$this->assign ( array (
				'deptcode' => $deptcode,
				'semesterid' => $semesterid,
				'classtypecode' => $classtypecode,
				'deptArr' => $deptArr,
				'xueqiArr' => $xueqiArr,
				'classtypeArr' => $classtypeArr,
				'jiangciArr' => $jiangciArr
		) );

		$this->display ();
	}

	/*
	 * 创建新作业结果页
	 */
	public function create_result() {

		//echo '<pre>';print_r($_POST);exit();
		$data = array();
		if(empty($_POST['form-semester'])) {
			$this->error('error1');
		} else {
			$data['semester_id'] = (int)$_POST['form-semester'];
		}
		if(empty($_POST['form-classtype'])) {
			$this->error('error2');
		} else {
			$data['classtype_code'] = trim($_POST['form-classtype']);
		}
		if(empty($_POST['form-classlesson'])) {
			$this->error('error2');
		} else {
			$data['lesson_no'] = (int)$_POST['form-classlesson'];
		}


		$data['classyear'] = (int)date('Y');
		$data['homework_solution_pics'] = '';
		$data['homework_type'] = 1;
		$data['add_time'] = time();
		$data['edit_time'] = $data['add_time'];
		$model = D ( 'WeixinHomework' );
		$id =$model-> _insert($data,'MGS_HW_MainSubject');
		if($id) {
			header("location: ".U('homework/mid_science/questionList/',array('id'=>$id),''));
			//$this->success('添加成功',U('homework/mid_science/questionList/',array('id'=>$id),''),0);
		} else {
			$this->error('添加失败');
		}





		$tempPath = APP_DIR . '/Upload/temp/';
		$realPath = APP_DIR . '/Upload/wx_hw/' . date ( 'Y-m-d' ) . '/';
		if (! is_dir ( $realPath )) {
			if (! mkdir ( $realPath, 0777, true )) {
				$this->ajaxReturn ( array (), '不能创建图片存储目录，请联系开发人员。', '-1' );
			}
		}

		import ( 'ORG.Util.Input' );
		$classtypecode = Input::getVar ( $_POST ['form-classtype'] );
		$classlesson = Input::getVar ( $_POST ['form-classlesson'] );
		$semesterid = Input::getVar ( $_POST ['form-semester'] );

		$uploadSecretStr = 'gaosiup.hw.pics';

		$wxhwModel = D ( 'WeixinHomework' );

		// 提交表单
		if ($this->isAjax ()) {
			if (empty ( $classtypecode ) || empty ( $classlesson )) {
				$this->ajaxReturn ( array (), '参数传送错误。', '-1' );
			}
			$year = date('Y');
			$mainSubjectInt = $wxhwModel->checkMainSubject ( $year, $semesterid, $classtypecode, $classlesson );
			if ($mainSubjectInt > 0) {
				$this->ajaxReturn ( array (), '该班型的该讲次已经创建了作业解答和题目。', '-1' );
			}
			$uploadPicsStr = Input::getVar ( $_POST ['upload_pics'] );
			$subjects = Input::getVar ( $_POST ['subjects'] );
			if (empty ( $uploadPicsStr ) || empty ( $subjects )) {
				$this->ajaxReturn ( array (), '提交的数据发生错误4。', '-1' );
			}

			$subjectsTmpArr = json_decode ( $subjects );
			$subjectsArr = array ();
			foreach ( $subjectsTmpArr as $subject ) {
				if (count ( $subject ) == 3) {
					list ( $subject_no, $fullscore,$type ) = $subject;
					if (intval ( $subject_no ) <= 0 || intval ( $fullscore ) <= 0 || intval ( $type ) <= 0) {
						$this->ajaxReturn ( array (), '提交的数据发生错误5。', '-1' );
					}
					$subjectsArr [$subject_no] = array (
							'subject_no' => $subject_no,
							'fullscore' => $fullscore,
							'type' => $type
					);
					ksort($subjectsArr);
				} else {
					$this->ajaxReturn ( array (), '提交的数据发生错误6。', '-1' );
				}
			}

			$uploadPicsArr = explode ( ',', $uploadPicsStr );
			$picNameArr = array ();
			foreach ( $uploadPicsArr as $uploadPic ) {
				$uploadPic = explode ( '/', $uploadPic );
				$picNameStr = end ( $uploadPic );
				$picNameArr [] = '/' . date ( 'Y-m-d' ) . '/' . $picNameStr;
				$movePicState = 1;
				if (! rename ( $tempPath . $picNameStr, $realPath . $picNameStr )) {
					$movePicState = - 1;
					break;
				}
			}
			if ($movePicState == - 1) {
				$this->ajaxReturn ( array (), '提交过程中发生文件移动错误，请联系开发人员。', '-1' );
			}
			$homework_solution_pics = implode ( ',', $picNameArr );
			if ($wxhwModel->addSubject ( $year, $semesterid, $classtypecode, $classlesson, $homework_solution_pics, $subjectsArr )) {
				$this->ajaxReturn ( array (), '添加成功，请选择左侧的菜单进行其他操作。', '1' );
			}
			$this->ajaxReturn ( array (), '存储过程中发生错误，请联系开发人员。', '-1' );
		}

		$classtypeArr = $wxhwModel->getOneClassType ( $classtypecode );
		$this->assign ( array (
				'uploadSecretStr' => $uploadSecretStr,
				'classtypeArr' => $classtypeArr,
				'classtypecode' => $classtypecode,
				'classlesson' => $classlesson,
				'semesterid' => $semesterid
		) );
		$this->display ();
	}

	/*
	 * 上传作业解答文件
	 */
	public function uploadSsolutionPics() {
		import ( 'ORG.Util.Input' );

		$path = APP_DIR . '/Upload/temp/';
		$uploadSecretStr = 'gaosiup.hw.pics';

		$file = $_FILES ['Filedata'];

		$timestamp = Input::getVar ( $_POST ['timestamp'] );
		$token = Input::getVar ( $_POST ['token'] );
		if ($token != md5 ( $timestamp . $uploadSecretStr )) {
			$this->ajaxReturn ( '', '非法上传', '-1' );
		}

		import ( 'ORG.Net.UploadFile' );
		$upload = new UploadFile ();
		$upload->maxSize = 5242800; // 最大5MB
		$upload->allowExts = array (
				'jpg',
				'gif',
				'png',
				'jpeg'
		);
		$upload->savePath = $path;
		$upload->saveRule = 'uniqid';
		$upload->thumb = true;
		$upload->thumbPrefix = 'solution_';
		$upload->thumbMaxWidth = '1000';
		$upload->thumbMaxHeight = '1000';
		$upload->thumbRemoveOrigin = true;

		$fileinfo = $upload->uploadOne ( $file );
		if (! $fileinfo) {
			$result = array (
					'',
					'上传失败' . $upload->getErrorMsg (),
					'-1'
			);
		} else {
			$result = array (
					$fileinfo [0],
					'上传成功',
					'1'
			);
		}
		$this->ajaxReturn ( $result [0], $result [1], $result [2] );
	}

	/*
	 * 作业管理页面
	 */
	public function manage() {
		import ( 'ORG.Util.Input' );
		$year = abs ( Input::getVar ( $_GET ['year'] ) );
		$deptcode = Input::getVar ( $_GET ['deptcode'] );
		$semesterid = Input::getVar ( $_GET ['semesterid'] );
		$classtypecode = Input::getVar ( $_GET ['classtypecode'] );
		$classlesson = Input::getVar ( $_GET ['classlesson'] );

		$wxhwModel = D ( 'WeixinHomework' );

		$xueqi0Arr = $wxhwModel->getXueqi ();
		$xueqiArr = array ();
		foreach ( $xueqi0Arr as $xueqi ) {
			$xueqiArr [$xueqi ['id']] = $xueqi;
		}
		$dept0Arr = $wxhwModel->getDepts ();
		$deptArr = array ();
		foreach ( $dept0Arr as $dept ) {
			$deptArr [$dept ['sdeptcode']] = $dept;
		}

		$classtypeArr = array ();
		if (isset ( $deptArr [$deptcode] ) && $semesterid) {
			$oneDeptArr = $deptArr [$deptcode];
			$classtype0Arr = $wxhwModel->getClassType ( $year, $semesterid, $oneDeptArr ['nxuebu'], $oneDeptArr ['nxueke'] );
			foreach ( $classtype0Arr as $classtype ) {
				$classtypeArr [$classtype ['scode']] = $classtype;
			}
		}

		$jiangciArr = array ();
		if (isset ( $classtypeArr [$classtypecode] ['nlesson'] )) {
			$lesson = $classtypeArr [$classtypecode] ['nlesson'];
			$jiangciArr = range ( 1, $lesson );
		}

		$listArr = array();
		if (! empty ( $classtypecode )) {
			$classlesson = empty ( $classlesson ) ? null : $classlesson;
			$listArr = $wxhwModel->getSubjects ( $year, $semesterid, $classtypecode, $classlesson );
		}
		$this->assign ( array (
				'year' => $year,
				'deptcode' => $deptcode,
				'semesterid' => $semesterid,
				'classtypecode' => $classtypecode,
				'classlesson' => $classlesson,
				'deptArr' => $deptArr,
				'xueqiArr' => $xueqiArr,
				'classtypeArr' => $classtypeArr,
				'jiangciArr' => $jiangciArr,
				'listArr' => $listArr
		) );
		$this->display ();
	}

	/*
	 * 作业编辑页面
	 */
	public function edit() {
		$tempPath = APP_DIR . '/Upload/temp/';
		$realPath = APP_DIR . '/Upload/wx_hw/' . date ( 'Y-m-d' ) . '/';
		if (! is_dir ( $realPath )) {
			if (! mkdir ( $realPath, 0777, true )) {
				$this->ajaxReturn ( array (), '不能创建图片存储目录，请联系开发人员。', '-1' );
			}
		}

		import ( 'ORG.Util.Input' );
		$main_subject_id = abs ( Input::getVar ( $_REQUEST ['main_subject_id'] ) );

		if (empty ( $main_subject_id )) {
			$this->ajaxReturn ( array (), '参数传送错误。', '-1' );
		}

		$wxhwModel = D ( 'WeixinHomework' );

		$mainSubjectArr = $wxhwModel->getOneMainSubject($main_subject_id);
		$classtypecode = $mainSubjectArr['classtype_code'];
		$classlesson = $mainSubjectArr['lesson_no'];

		$uploadSecretStr = 'gaosiup.hw.pics';

		$hwPicArr = explode(',', $mainSubjectArr['homework_solution_pics']);
		$hwSmallSubjectArr = $wxhwModel->getSmallSubject ( $main_subject_id );

		// 提交表单
		if ($this->isAjax ()) {
			if (empty ( $classtypecode ) || empty ( $classlesson )) {
				$this->ajaxReturn ( array (), '参数传送错误。', '-1' );
			}

			$uploadPicsStr = Input::getVar ( $_POST ['upload_pics'] );
			$subjects = Input::getVar ( $_POST ['subjects'] );
			if (empty ( $uploadPicsStr ) || empty ( $subjects )) {
				$this->ajaxReturn ( array (), '提交的数据发生错误1。', '-1' );
			}

			$subjectsTmpArr = json_decode ( $subjects );
			$subjectsArr = array ();
			foreach ( $subjectsTmpArr as $subject ) {
				if (count ( $subject ) == 2) {
					list ( $subject_no, $fullscore ) = $subject;
					if (intval ( $subject_no ) <= 0 || intval ( $fullscore ) <= 0) {
						$this->ajaxReturn ( array (), '提交的数据发生错误2。', '-1' );
					}
					$subjectsArr [$subject_no] = array (
							'subject_no' => $subject_no,
							'fullscore' => $fullscore
					);
					ksort($subjectsArr);
				} else {
					$this->ajaxReturn ( array (), '提交的数据发生错误3。', '-1' );
				}
			}

			$uploadPicsArr = explode ( ',', $uploadPicsStr );
			$picNameArr = array ();
			foreach ( $uploadPicsArr as $uploadPic ) {
				$uploadPic = explode ( '/', $uploadPic );
				$picNameStr = end ( $uploadPic );
				$picNameArr [] = '/' . date ( 'Y-m-d' ) . '/' . $picNameStr;

				$movePicState = 1;

				if (strpos ( $mainSubjectArr ['homework_solution_pics'], $picNameStr ) === false) {
					if (! rename ( $tempPath . $picNameStr, $realPath . $picNameStr )) {
						$movePicState = - 1;
						break;
					}
				}

			}
			if ($movePicState == - 1) {
				$this->ajaxReturn ( array (), '提交过程中发生文件移动错误，请联系开发人员。', '-1' );
			}
			$homework_solution_pics = implode ( ',', $picNameArr );
			if ($wxhwModel->editSubject ( $main_subject_id, $homework_solution_pics, $subjectsArr )) {
				$this->ajaxReturn ( array (), '编辑成功，请选择左侧的菜单进行其他操作。', '1' );
			}
			$this->ajaxReturn ( array (), '存储过程中发生错误，请联系开发人员。', '-1' );
		}

		$classtypeArr = $wxhwModel->getOneClassType ( $classtypecode );
		$this->assign ( array (
				'main_subject_id' => $main_subject_id,
				'uploadSecretStr' => $uploadSecretStr,
				'classtypeArr' => $classtypeArr,
				'classtypecode' => $classtypecode,
				'classlesson' => $classlesson,
				'hwPicArr' => $hwPicArr,
				'hwSmallSubjectArr' => $hwSmallSubjectArr
		) );
		$this->display ();
	}
	protected function ajax_return($json) {
		echo json_encode($json);
		exit();
	}

	//-----------------------------------------------add by tiyee at 2014.8.20-----------------------------------------------------------
	public function lessonList() {
		$model = D( 'WeixinHomework' );
		import('ORG.Util.Page');
		$data = array();

		$years = array(0=>'-=年=-');
		$sems = array(0=>'-=学期=-');
		$ctypes = array(0=>'-=班型名称=-');
		$deps = array(0=>'-=学科=-');

		if(!empty($_GET['classyear'])) {
			$classyear = $data['classyear'] = (int)$_GET['classyear'];
		} else {
			$classyear = 0;
		}
		if(!empty($_GET['semester_id'])) {
			$semester_id =  $data['semester_id'] = (int)$_GET['semester_id'];
		} else {
			$semester_id = 0;
		}
		if(!empty($_GET['deps'])) {
			$deptcode = (int)$_GET['deps'];
			$data['nXueBu'] = floor($deptcode/10);
			$data['nXueKe'] = $deptcode%10;
		} else {
			$deptcode = 0;
		}
		if(!empty($_GET['classtype_code'])) {
			$classtype_code = $data['classtype_code'] = trim($_GET['classtype_code']);
		} else {
			$classtype_code = '';
		}

		//print_r($data);




		$total = $model->getLessonNum($data) ;
		$Page       = new Page($total);

		$page = $Page->show();


		$data['page'] = empty($_GET['p'])?'1':$_GET['p'];
        $list = array();



		$results = $model->getLessonAll($data);
		//echo '<pre>';print_r($results);exit();
		foreach($results as $value){
			$years[$value['classyear']] = $value['classyear'].'年';
			$sems[$value['semester_id']] = seasonName($value['semester_id']);
			$ctypes[$value['classtype_code']] = $value['sname'];
			$deps[10*$value['nxuebu']+$value['nxueke']] = $value['depname'];
		}
		$results = $model->getLessonList($data);
		$list = & $results;
		$this->assign(get_defined_vars());
		$this->display('getList');
	}

	public function questionList() {
		if(empty($_GET['id'])) {
			$this->error('error');
		} else {
			$id = (int)$_GET['id'];
		}
		if($id < 1) {
			$this->error('error');
		} else {
			$main_subject_id = $id;
		}
		if(empty($id)) {
			$this->error('error');
		}
		$actionList = array(
			1 => '单选',
			2 => '多选',
			3 => '主观'



			);
		$model = D( 'WeixinHomework' );

		$lessonInfo = $model->getLessonInfo(array('id'=>$id));
		$results = $model->getQuestionList($id);
		$questionList = array();
		$i = -1;
		foreach ($results as $value) {
			$i++;
			$questionList[$i] = $value;
			if(empty($value['squestion'])) {
				$questionList[$i]['aQuestion'] = array();
				continue;
			}
			$questionList[$i]['aQuestion'] = unserialize(base64_decode($value['squestion']));

		}
		//echo '<pre>';print_r($questionList);
		$this->assign(get_defined_vars());
		$this->display('questionlist');
	}
   public function update() {
   		$json = array(
   			'error' => 1,
   			'msg' => '添加失败'
   			);
   		$data = $sQuestion = array();
   		if(empty($_GET['id'])) {
   			$this->ajax_return($json);
   		} else {
   			$id = (int)$_GET['id'];
   		}
   		if(empty($_POST['question'])) {
   			$json['msg'] = '问题为空';
   			$this->ajax_return($json);
   		} else {
   			$sQuestion['question'] = trim($_POST['question']);
   			$sQuestion['question'] = str_replace(chr(13), '<br/>', $sQuestion['question']);
   		}
   		if(empty($_POST['options']) || count($_POST['options']) < 1) {
   			/*$json['msg'] = '选项为空';
   			$this->ajax_return($json);*/
   		} else {
   			$sQuestion['options'] = $_POST['options'];
   		}
   		if(!empty($_POST['point'])) {
   			$sQuestion['point'] = trim($_POST['point']);
   			$sQuestion['point'] = str_replace(chr(13), '<br/>', $sQuestion['point']);
   		} else {
   			$sQuestion['point'] = '';
   		}
   		if(empty($_POST['subject_no'])) {
   			$json['msg'] = '题号为空';
   			$this->ajax_return($json);
   		} else {
   			$data['subject_no'] = (int)$_POST['subject_no'];
   		}
   		if($data['subject_no'] < 0) {
   			$json['msg'] = '题号为负数';
   			$this->ajax_return($json);
   		}
   		if(empty($_POST['fullscore'])) {
   			$json['msg'] = '分值为空';
   			$this->ajax_return($json);
   		} else {
   			$data['fullscore'] = (int)$_POST['fullscore'];
   		}
   		if($data['fullscore'] < 0) {
   			$json['msg'] = '分值为负数';
   			$this->ajax_return($json);
   		}
   		if(!isset($_POST['corrent_answer'])) {
   			$json['msg'] = '答案为空';
   			$this->ajax_return($json);
   		}
   		if(is_array($_POST['corrent_answer'])) {
   			$data['corrent_answer'] = implode('|', $_POST['corrent_answer']);
   		} else {
   			$data['corrent_answer'] = $_POST['corrent_answer'];
   			$data['corrent_answer'] = str_replace(chr(13), '<br/>', $data['corrent_answer']);
   		}


   		$data['sQuestion'] = base64_encode(serialize($sQuestion));
   		$model = D( 'WeixinHomework' );
   		$result = $model->_update($data,array('id'=>$id),'MGS_HW_SmallSubject');
   		if(empty($result)) {
   			$this->ajax_return($json);
   		} else {
   			$json['error'] = 0;
   			$json['msg'] = '插入成功';
   			$this->ajax_return($json);
   		}





   }
   public function insert() {
   		$json = array(
   			'error' => 1,
   			'msg' => '添加失败'
   			);
   		$data = $sQuestion = array();
   		if(empty($_POST['main_subject_id'])) {
   			$this->ajax_return($json);
   		} else {
   			$data['main_subject_id'] = (int)$_POST['main_subject_id'];
   		}
   		if(empty($_POST['type'])) {
   			$this->ajax_return($json);
   		} else {
   			$data['type'] = (int)$_POST['type'];
   		}
   		if(empty($_POST['question'])) {
   			$json['msg'] = '问题为空';
   			$this->ajax_return($json);
   		} else {
   			$sQuestion['question'] = trim($_POST['question']);
   			$sQuestion['question'] = str_replace(chr(13), '<br/>', $sQuestion['question']);
   		}
   		if(empty($_POST['options']) || count($_POST['options']) < 1) {
   			/*$json['msg'] = '选项为空';
   			$this->ajax_return($json);*/
   		} else {
   			$sQuestion['options'] = $_POST['options'];
   		}
   		if(!empty($_POST['point'])) {
   			$sQuestion['point'] = trim($_POST['point']);
   			$sQuestion['point'] = str_replace(chr(13), '<br/>', $sQuestion['point']);
   		} else {
   			$sQuestion['point'] = '';
   		}
   		if(empty($_POST['subject_no'])) {
   			$json['msg'] = '题号为空';
   			$this->ajax_return($json);
   		} else {
   			$data['subject_no'] = (int)$_POST['subject_no'];
   		}
   		if($data['subject_no'] < 0) {
   			$json['msg'] = '题号为负数';
   			$this->ajax_return($json);
   		}
   		if(empty($_POST['fullscore'])) {
   			$json['msg'] = '分值为空';
   			$this->ajax_return($json);
   		} else {
   			$data['fullscore'] = (int)$_POST['fullscore'];
   		}
   		if($data['fullscore'] < 0) {
   			$json['msg'] = '分值为负数';
   			$this->ajax_return($json);
   		}
   		if(!isset($_POST['corrent_answer'])) {
   			$json['msg'] = '答案为空';
   			$this->ajax_return($json);
   		}
   		if(is_array($_POST['corrent_answer'])) {
   			$data['corrent_answer'] = implode('|', $_POST['corrent_answer']);
   		} else {

   			$data['corrent_answer'] = $_POST['corrent_answer'];


   			$data['corrent_answer'] = str_replace(chr(13), '<br/>', $data['corrent_answer']);
   			$data['corrent_answer'] = str_replace('"<br/>', '" ', $data['corrent_answer']);


   		}
   		$data['sQuestion'] = base64_encode(serialize($sQuestion));
   		//$this->ajax_return($data);
   		$model = D( 'WeixinHomework' );
   		$result = $model->_insert($data,'MGS_HW_SmallSubject');
   		if(empty($result)) {
   			$this->ajax_return($json);
   		} else {
   			$json['error'] = 0;
   			$json['msg'] = '插入成功';
   			$this->ajax_return($json);
   		}





   }
	public function editQuestion() {
		if(empty($_GET['id'])) {
			echo 'error';
		} else {
			$id = (int)$_GET['id'];
		}
		$action = '修改试题';
		$url = U('/Homework/MidScience/update',array('id'=>$id),'');

		$model = D( 'WeixinHomework' );
		$aQuestionInfo = $model->getQuestionInfo(array('id'=>$id));

		$aPaperInfo = $model->getLessonInfo(array('id'=>$aQuestionInfo['main_subject_id']));
		//print_r($aQuestionInfo);
		if(empty($aQuestionInfo['squestion'])) {
			$aQuestionInfo['sQuestion']['question'] = '';

			if($aQuestionInfo['type'] == 1 || $aQuestionInfo['type'] == 2 ) {
				$aQuestionInfo['sQuestion']['options'] = array(
				0 => '',
				1 => '',
				2 => '',
				3 => ''
				);





				}
			$aQuestionInfo['corrent_answer'] = '';
			$aQuestionInfo['sQuestion']['point'] = '';
		} else {
			$aQuestionInfo['sQuestion'] = unserialize(base64_decode($aQuestionInfo['squestion']));

		}
		//print_r($aQuestionInfo);
		$token = time();
		$this->assign(get_defined_vars());
		switch ($aQuestionInfo['type']) {
			case 1:
				$template = 'getRadioForm';
				break;
			case 2:
				$template = 'getCheckboxForm';
				break;

			default:
				$template = 'getForm';
				break;
		}




		$this->display($template);
	}
	public function addQuestion() {
		if(empty($_GET['id'])) {
			echo 'error';
		} else {
			$type = (int)$_GET['id'];
		}

		if(empty($_GET['main_subject_id'])) {
			echo 'error';
		} else {
			$main_subject_id = (int)$_GET['main_subject_id'];
			$token = $main_subject_id;
		}
		$action = '添加试题';
		$url = U('/Homework/MidScience/insert','','');

		$model = D( 'WeixinHomework' );
		$aQuestionInfo = array();

		$aPaperInfo = $model->getLessonInfo(array('id'=>$main_subject_id));
		//print_r($aQuestionInfo);

		$aQuestionInfo['sQuestion']['question'] = '';

		if($type == 1 || $type == 2 ) {
			$aQuestionInfo['sQuestion']['options'] = array(
			0 => '',
			1 => '',
			2 => '',
			3 => ''
			);





			}
		$token = time();
		$aQuestionInfo['type'] = $type;
		$aQuestionInfo['main_subject_id'] = $main_subject_id;
		$aQuestionInfo['corrent_answer'] = '';
		$aQuestionInfo['sQuestion']['point'] = '';

		//print_r($aQuestionInfo);

		$this->assign(get_defined_vars());
		switch ($type) {
			case 1:
				$template = 'getRadioForm';
				break;
			case 2:
				$template = 'getCheckboxForm';
				break;

			default:
				$template = 'getForm';
				break;
		}




		$this->display($template);
	}
	public function deleteQuestion() {
		$json = array(
   			'error' => 1,
   			'msg' => '删除失败'
   			);
		if(empty($_POST['id'])) {
			$json['msg'] = '您没有传入id';
			$this->ajax_return($json);
		} else {
			$id = (int)$_POST['id'];
		}
		$model = D( 'WeixinHomework' );
		$result = $model->deleteQuestion(array('id'=>$id));
		if(empty($result)) {
   			$this->ajax_return($json);
   		} else {
   			$json['error'] = 0;
   			$json['msg'] = '删除成功';
   			$this->ajax_return($json);
   		}
	}

	public function delPaper() {
		$json = array(
   			'error' => 1,
   			'msg' => '删除失败'
   			);
		if(empty($_GET['id'])) {
			$json['msg'] = '您没有传入id';
			$this->ajax_return($json);
		} else {
			$id = (int)$_GET['id'];
		}
		$model = D('WeixinHomework');
		$result = $model->deletePaper(array('id'=>$id));
		if(empty($result)) {
   			$this->ajax_return($json);
   		} else {
   			$json['error'] = 0;
   			$json['msg'] = '删除成功';
   			$this->ajax_return($json);
   		}

	}
	/*
	* ADD BY LIUYUAN AT 2014.10.31
	* EXPLODE THE STUDENT LESSON SCORE
	*/
	public function explodeStuScore(){
    	/** Error reporting */
		// error_reporting(E_ALL);
		// ini_set('display_errors', TRUE);
		// ini_set('display_startup_errors', TRUE);
		// date_default_timezone_set('Asia/Shanghai');

		// if (PHP_SAPI == 'cli')
		// 	die('This example should only be run from a Web Browser');
		/** Include PHPExcel */
		require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/Static/PHPExcel-1.7.7/Classes/PHPExcel.php';
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("LiuYuan")
									 // ->setLastModifiedBy("")
									 ->setTitle("中学理科微信交作业学员成绩列表")
									 // ->setSubject("Office 2007 XLSX Test Document")
									 ->setDescription("中学理科微信交作业学员成绩列表（所有）")
									 // ->setKeywords("office 2007 openxml php")
									 ->setCategory("file");


		// Add some data
		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A1', '班型编码')
		            ->setCellValue('B1', '班型名称')
		            ->setCellValue('C1', '班级编码')
		            ->setCellValue('D1', '班级名称')
		            ->setCellValue('E1', '学号')
		            ->setCellValue('F1', '学员姓名')
		            ->setCellValue('G1', '第1讲成绩')
		            ->setCellValue('H1', '第2讲成绩')
		            ->setCellValue('I1', '第3讲成绩')
		            ->setCellValue('J1', '第4讲成绩')
		            ->setCellValue('K1', '第5讲成绩')
		            ->setCellValue('L1', '第6讲成绩')
		            ->setCellValue('M1', '第7讲成绩')
		            ->setCellValue('N1', '第8讲成绩')
		            ->setCellValue('O1', '第9讲成绩')
		            ->setCellValue('P1', '第10讲成绩')
		            ->setCellValue('Q1', '第11讲成绩')
		            ->setCellValue('R1', '第12讲成绩')
		            ->setCellValue('S1', '第13讲成绩')
		            ->setCellValue('T1', '第14讲成绩')
		            ->setCellValue('U1', '第15讲成绩');

		$sql = "SELECT TOP 2000 ct.sCode AS classTypeCode, ct.sName AS classTypeName, c.sCode AS classCode, c.sName AS className, s.sAliasCode AS stuAliasCode, s.sName AS stuName, mhsa.lesson_no, mhsa.total_score FROM MGS_HW_StudentAnswer AS mhsa INNER JOIN viewBS_Class AS c ON mhsa.classcode = c.sCode INNER JOIN BS_ClassType AS ct ON mhsa.classtype_code = ct.sCode INNER JOIN BS_Student AS s ON mhsa.stu_code = s.sCode WHERE mhsa.classcode IN ('BJ14Q2274','BJ14Q2275','BJ14Q2278','BJ14Q2280','BJ14Q2281','BJ14Q2282','BJ14Q2283','BJ14Q2285','BJ14Q2287','BJ14Q2289','BJ14Q2291','BJ14Q2294','BJ14Q2295','BJ14Q2296','BJ14Q2300','BJ14Q2301','BJ14Q2302','BJ14Q2303','BJ14Q2308','BJ14Q2309','BJ14Q2312','BJ14Q2313','BJ14Q2314','BJ14Q2317','BJ14Q2323','BJ14Q2324','BJ14Q2328','BJ14Q2332','BJ14Q2333','BJ14Q2334','BJ14Q2336','BJ14Q2339','BJ14Q2340','BJ14Q2341','BJ14Q2345','BJ14Q2346','BJ14Q2347','BJ14Q2348','BJ14Q2349','BJ14Q2350','BJ14Q2352','BJ14Q2353','BJ14Q2354','BJ14Q2366','BJ14Q2457','BJ14Q2468','BJ14Q2640') and mhsa.is_locked = 0 and mhsa.homework_status = 3 GROUP BY ct.sCode, ct.sName, c.sCode, c.sName, s.sAliasCode, s.sName, mhsa.lesson_no, mhsa.total_score";//, mhsa.last_answer_time ORDER BY s.sName,mhsa.last_answer_time DESC
		$model = new Model();
		$result = $model->query($sql);
		$i = 2;
		$score_all = range(1, 15);
		$str = 'score';
		foreach ($result as $k => $v) {
			foreach($score_all as $num){
				$ques_no = $str . $num;
				if($num == $v['lesson_no']){
					$v['total_score'] = empty($v['total_score']) ? '0' : $v['total_score'];
					$$ques_no = $v['total_score'];
				}
			}
			if (isset($result[$k+1]) && ($v['stuAliasCode'] == $result[$k+1]['stuAliasCode'])) {
				continue;
			}
			// Miscellaneous glyphs, UTF-8
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i, $v['classTypeCode'])
		    ->setCellValue('B'.$i, $v['classTypeName'])
	      	->setCellValue('C'.$i, $v['classCode'])
			->setCellValue('D'.$i, $v['className'])
			->setCellValue('E'.$i, $v['stuAliasCode'])
			->setCellValue('F'.$i, $v['stuName'])
			->setCellValue('G'.$i, $score1)
			->setCellValue('H'.$i, $score2)
			->setCellValue('I'.$i, $score3)
			->setCellValue('J'.$i, $score4)
			->setCellValue('K'.$i, $score5)
			->setCellValue('L'.$i, $score6)
			->setCellValue('M'.$i, $score7)
			->setCellValue('N'.$i, $score8)
			->setCellValue('O'.$i, $score9)
			->setCellValue('P'.$i, $score10)
			->setCellValue('Q'.$i, $score11)
			->setCellValue('R'.$i, $score12)
			->setCellValue('S'.$i, $score13)
			->setCellValue('T'.$i, $score14)
			->setCellValue('U'.$i, $score15);
	   		$i++;

   		foreach($score_all as $num){
				$ques_no = $str . $num;
				$$ques_no = '';
			}
		}
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('default');

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);

		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="中学理科微信交作业学员成绩列表.xls"');
		ob_end_clean();
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
    }


}

