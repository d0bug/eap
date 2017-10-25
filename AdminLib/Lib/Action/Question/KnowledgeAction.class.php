<?php
import ( 'ORG.Util.Session' );
Session::start ();

/* 知识库管理 */
class KnowledgeAction extends QuestionCommAction {
	public function question_simple_list() {
		$this->display ();
	}
	public function question_classic_list() {
		$this->display ();
	}
	public function getQuestionStatisticsByCourseTypeId() {
		$params = $this->_post ();
		$courseTypeId = $params ['coursetypeid'];
		$userInfo = $this->loginUser->getInformation ();
		
		$count = D ( 'Basic' )->getQuestionStatisticsByCourseTypeId ( $courseTypeId, $userInfo ['user_name'] );
		$this->outPut ( $count );
	}
	public function getQuestionStatisticsByCourseTypeId1() {
		$params = $this->_post ();
		$courseTypeId = $params ['coursetypeid'];
		$userInfo = $this->loginUser->getInformation ();
		
		$count = D ( 'Basic' )->getQuestionStatisticsByCourseTypeId1 ( $courseTypeId, $userInfo ['user_name'] );
		$this->outPut ( $count );
	}
	public function getQuestionStatisticsByCourseTypeId2() {
		$params = $this->_post ();
		$courseTypeId = $params ['coursetypeid'];
		$userInfo = $this->loginUser->getInformation ();
		
		$count = D ( 'Basic' )->getQuestionStatisticsByCourseTypeId2 ( $courseTypeId, $userInfo ['user_name'] );
		$this->outPut ( $count );
	}
	public function content() {
		echo '<div style="text-align: left;font-size:24px;"><a href="#" onclick="question_simple_list_search()">请载入试题</a></div>';
	}
	public function content1() {
		echo '<div style="text-align: left;font-size:24px;"><a href="#" onclick="question_edit_simple_list_search()">请载入试题</a></div>';
	}
	public function content2() {
		echo '<div style="text-align: left;font-size:24px;"><a href="#" onclick="question_classic_list_search()">请载入试题</a></div>';
	}
	public function question_list() {
		$basicModel = D ( 'Basic' );
		$condition = $this->get_roleCondition ();
		//$statistics = $basicModel->getQuestionStatistics ( $condition );
		//$total = $basicModel->getQuestionsCountByWhere ( $condition );
		$this->assign ( 'statistics', $statistics );
		$this->assign ( 'total', $total );
		$this->display ();
	}
	public function add_question() {
		// $this->assign ( 'uid', get_uuid () );
		// $this->assign ( 'date', get_upload_dir () );
		$this->display ();
	}
	public function add_sub_question() {
		$params = $this->_get ();
		$questionId = $params ['id'];
		$subjectId = $params ['subjectid'];
		
		$this->assign ( 'questionid', $questionId );
		$this->assign ( 'subjectid', $subjectId );
		$this->display ();
	}
	public function save_question() {
		$userInfo = $this->loginUser->getInformation ();
		$data = $this->_post ();
		$post = array_merge ( $data, array (
				'sub_knowledge_id' => arr2str ( $data ['sub_knowledge_id'], ',' ),
				'grades' => arr2str ( $data ['grades'], ',' ),
				'user_name' => $userInfo ['user_name'] 
		) );
		
		$result = D ( 'Basic' )->addQuestion ( $post );
		if ($result) {
			$this->success ();
		}
		$this->error ();
	}
	public function save_simple_question() {
		$userInfo = $this->loginUser->getInformation ();
		$data = $this->_post ();
		$data ['user_name'] = $userInfo ['user_name'];
		$result = D ( 'Basic' )->editSimpleQuestion ( $data );
		
		if ($result) {
			$this->success ();
		}
		$this->error ();
	}
	public function my_edit_questions() {
		$userInfo = $this->loginUser->getInformation ();
		$data ['user_name'] = $userInfo ['user_name'];
		$questions = D ( 'Basic' )->getMyEditSimpleQuestions ( $data ['user_name'] );
		$this->assign ( 'questions', $questions );
		$this->display ();
	}
	public function my_edit_simple_questions() {
		$userInfo = $this->loginUser->getInformation ();
		$data ['user_name'] = $userInfo ['user_name'];
		$questions = D ( 'Basic' )->getMyEditSimpleQuestions1 ( $data ['user_name'] );
		$this->assign ( 'questions', $questions );
		$this->display ();
	}
	public function skip_question() {
		$data = $this->_post ();
		$questionId = $data ['id'];
		$result = D ( 'Basic' )->skipQuestion ( $questionId );
		
		if ($result) {
			$this->success ();
		}
		$this->error ();
	}
	public function skip_question1() {
		$data = $this->_post ();
		$questionId = $data ['id'];
		$result = D ( 'Basic' )->skipQuestion1 ( $questionId );
		
		if ($result) {
			$this->success ();
		}
		$this->error ();
	}
	public function skip_question2() {
		$data = $this->_post ();
		$questionId = $data ['id'];
		$result = D ( 'Basic' )->skipQuestion2 ( $questionId );
		
		if ($result) {
			$this->success ();
		}
		$this->error ();
	}
	public function edit_question() {
		$params = $this->_get ();
		$questionId = $params ['id'];
		$question = D ( 'Basic' )->getQuestionByID ( $questionId );
		$this->assign ( 'question', $question );
		$this->display ();
	}
	public function edit_sub_question() {
		$params = $this->_get ();
		$code = $params ['code'];
		$id = $params ['id'];
		if (! empty ( $id )) {
			$question = D ( 'Basic' )->getQuestionByID ( $id );
			$this->assign ( array (
					'code' => $code,
					'question' => $question 
			) );
		}
		$this->display ();
	}
	public function delete_question() {
		$params = $this->_post ();
		$id = $params ['id'];
		
		$result = D ( 'Basic' )->deleteQuestionById ( $id );
		if ($result) {
			$this->success ();
		}
		$this->error ();
	}
	public function edit_save_question() {
		$userInfo = $this->loginUser->getInformation ();
		$data = $this->_post ();
		$post = array (
				'id' => $data ['id'],
				// 'sub_id' => $data ['sub_id'],
				'uid' => $data ['uid'],
				'course_type_id' => $data ['course_type_id'],
				'question_type_id' => $data ['question_type_id'],
				'difficulty' => $data ['score'],
				'knowledge_id' => $data ['knowledge_id'],
				'sub_knowledge_id' => arr2str ( $data ['sub_knowledge_id'], ',' ),
				'grades' => arr2str ( $data ['grades'], ',' ),
				'content' => $data ['content'],
				'content_text' => strip_tags ( $data ['content'] ),
				'analysis' => $data ['analysis'],
				'options' => $data ['options'],
				'euids' => $data ['euids'],
				'answers' => $data ['answers'],
				'oid' => $data ['oid'],
				'options_answer_flag' => $data ['options_answer_flag'],
				'user_name' => $userInfo ['user_name'],
				'user_id' => $userInfo ['user_id'] 
		);
		$question = D ( 'Basic' )->edit_save_Question ( $post );
		if (! empty ( $question )) {
			$this->success ();
		}
		$this->error ();
	}
	public function edit_save_simple_question() {
		$userInfo = $this->loginUser->getInformation ();
		$data = $this->_post ();
		$post = array (
				'id' => $data ['id'],
				'uid' => $data ['uid'],
				'course_type_id' => $data ['course_type_id'],
				'question_type_id' => $data ['question_type_id'],
				'content_error_types' => arr2str ( $data ['content_error_types'], ',' ),
				'difficulty' => $data ['score'],
				'knowledge_id' => $data ['knowledge_id'],
				'sub_knowledge_id' => arr2str ( $data ['sub_knowledge_id'], ',' ),
				'grades' => arr2str ( $data ['grades'], ',' ),
				'content' => $data ['content'],
				'content_text' => strip_tags ( $data ['content'] ),
				'analysis' => $data ['analysis'],
				'options' => $data ['options'],
				'euids' => $data ['euids'],
				'answers' => $data ['answers'],
				'oid' => $data ['oid'],
				'options_answer_flag' => $data ['options_answer_flag'],
				'user_name' => $userInfo ['user_name'],
				'user_id' => $userInfo ['user_id'] 
		);
		$question = D ( 'Basic' )->edit_save_simple_question ( $post );
		if (! empty ( $question )) {
			$this->success ();
		}
		$this->error ();
	}
	public function edit_save_classic_question() {
		$userInfo = $this->loginUser->getInformation ();
		$data = $this->_post ();
		$post = array (
				'id' => $data ['id'],
				'uid' => $data ['uid'],
				'course_type_id' => $data ['course_type_id'],
				'question_type_id' => $data ['question_type_id'],
				'content_error_types' => arr2str ( $data ['content_error_types'], ',' ),
				'difficulty' => $data ['score'],
				'knowledge_id' => $data ['knowledge_id'],
				'sub_knowledge_id' => arr2str ( $data ['sub_knowledge_id'], ',' ),
				'grades' => arr2str ( $data ['grades'], ',' ),
				'content' => $data ['content'],
				'content_text' => strip_tags ( $data ['content'] ),
				'analysis' => $data ['analysis'],
				'options' => $data ['options'],
				'euids' => $data ['euids'],
				'answers' => $data ['answers'],
				'oid' => $data ['oid'],
				'options_answer_flag' => $data ['options_answer_flag'],
				'user_name' => $userInfo ['user_name'],
				'user_id' => $userInfo ['user_id'] 
		);
		$question = D ( 'Basic' )->edit_save_classic_question ( $post );
		if (! empty ( $question )) {
			$this->success ();
		}
		$this->error ();
	}
	public function preview_question() {
		$data = $this->_post ();
		
		$this->display ();
	}
	public function render_question_list() {
		$params = $this->_param ();
		$currentPage = $params ['page'];
		$pageSize = $params ['rows'];
		$condition = array (
				'coursetypeid' => $params ['coursetypeid'],
				'department' => $params ['department'],
				'isclassic' => $params ['isclassic'],
				'iscontenterror' => $params ['iscontenterror'],
				'startdate' => $params ['startdate'],
				'enddate' => $params ['enddate'] 
		);
		$condition = $this->get_roleCondition ( $condition );
		$questions = D ( 'Basic' )->getQuestionsByWhere ( $condition, $currentPage, $pageSize );
		$this->assign ( 'questions', $questions );
		$this->display ();
	}
	public function render_question_simple_list() {
		$params = $this->_param ();
		$currentPage = $params ['page'];
		$pageSize = $params ['rows'];
		
		$userInfo = $this->loginUser->getInformation ();
		$condition = array (
				'coursetypeid' => $params ['coursetypeid'] 
		);
		$condition = $this->get_roleCondition ( $condition );
		$question = D ( 'Basic' )->getSimpleQuestionsByWhere ( $userInfo ['user_name'], $condition, $currentPage, $pageSize );
		
		$this->assign ( 'question', $question );
		$this->assign ( 'coursetypeid', $params ['coursetypeid'] );
		
		$this->display ();
	}
	public function render_edit_question_simple_list() {
		$params = $this->_param ();
		$currentPage = $params ['page'];
		$pageSize = $params ['rows'];
		
		$userInfo = $this->loginUser->getInformation ();
		$condition = array (
				'coursetypeid' => $params ['coursetypeid'] 
		);
		$condition = $this->get_roleCondition ( $condition );
		$question = D ( 'Basic' )->getSimpleQuestionsEditByWhere ( $userInfo ['user_name'], $condition, $currentPage, $pageSize );
		$this->assign ( 'question', $question );
		$this->assign ( 'coursetypeid', $params ['coursetypeid'] );
		
		$this->display ();
	}
	public function render_edit_question_classic_list() {
		$params = $this->_param ();
		$currentPage = $params ['page'];
		$pageSize = $params ['rows'];
		
		$userInfo = $this->loginUser->getInformation ();
		$condition = array (
				'coursetypeid' => $params ['coursetypeid'] 
		);
		$condition = $this->get_roleCondition ( $condition );
		$question = D ( 'Basic' )->getClassicQuestionsEditByWhere ( $userInfo ['user_name'], $condition, $currentPage, $pageSize );
		$this->assign ( 'question', $question );
		$this->assign ( 'coursetypeid', $params ['coursetypeid'] );
		
		$this->display ();
	}
	public function edit_question_simple_list() {
		$params = $this->_param ();
		$currentPage = $params ['page'];
		$pageSize = $params ['rows'];
		
		$userInfo = $this->loginUser->getInformation ();
		$condition = array (
				'coursetypeid' => $params ['coursetypeid'] 
		);
		$condition = $this->get_roleCondition ( $condition );
		$question = D ( 'Basic' )->getSimpleQuestionsByWhere ( $userInfo ['user_name'], $condition, $currentPage, $pageSize );
		
		$this->assign ( 'question', $question );
		$this->assign ( 'coursetypeid', $params ['coursetypeid'] );
		
		$this->display ();
	}
	public function get_question_list_count() {
		$params = $this->_param ();
		$condition = array (
				'coursetypeid' => $params ['coursetypeid'],
				'department' => $params ['department'],
				'isclassic' => $params ['isclassic'],
				'iscontenterror' => $params ['iscontenterror'] 
		);
		
		$total = D ( 'Basic' )->getQuestionsCountByWhere ( $condition );
		echo $total;
	}
	
	/**
	 * 不同题型模板调用同样的参数
	 */
	protected function question_inssign() {
		$params = $this->_get ();
		
		if (! empty ( $params ['id'] )) {
			$questionId = $params ['id'];
			$question = D ( 'Basic' )->getQuestionByID ( $questionId );
			$options = D ( 'Basic' )->getOptionsByID ( $questionId );
			$count = count ( $options );
			$answer = D ( 'Basic' )->getAnswerByID ( $questionId );
			$this->assign ( array (
					'question' => $question,
					'options' => isset ( $options ) ? $options : '',
					'answer' => $answer 
			) );
		}
		
		$this->assign ( 'count', isset ( $count ) ? $count : 0 );
		$this->assign ( 'suffix', rand ( 10000, 99999 ) );
		return true;
	}
	public function question_type_1() {
		$this->question_inssign ();
		$this->display ();
	}
	public function question_type_2() {
		$this->question_inssign ();
		$this->display ();
	}
	public function question_type_3() {
		$this->question_inssign ();
		$this->display ();
	}
	public function question_type_4() {
		$this->question_inssign ();
		$this->display ();
	}
	public function question_type_5() {
		$this->question_inssign ();
		$this->display ();
	}
	
	// 用来测试word文档导入试题
	public function question_test() {
		// echo APP_DIR.'/AdminLib/Runtime/data.txt';exit;
		$content = unserialize ( file_get_contents ( APP_DIR . '/data6.txt' ) );
		// print_r($content);exit;
		$new_content = ( array ) ($content);
		$title = $new_content ['docid'];
		$new_content = $new_content ['records'];
		// var_dump($new_content['docid']);
		// print_r($new_content);exit;
		foreach ( $new_content as $key => $con ) {
			$new_content [$key] = ( array ) ($con);
			foreach ( $new_content [$key] ['fields'] as $k => $c ) {
				$new_content [$key] ['fields'] [$k] = ( array ) ($c);
				$new_content [$key] ['fields'] [$k] ['schema'] = ( array ) ($new_content [$key] ['fields'] [$k] ['schema']);
				$new_content [$key] ['fields'] [$k] ['fieldData'] = ( array ) ($new_content [$key] ['fields'] [$k] ['fieldData']);
			}
		}
		// print_r($new_content);die;
		$arr = array ();
		foreach ( $new_content as $key => $content ) {
			$arr [$key] ['uid'] = $content ['uid'];
			$arr [$key] ['sdate'] = $content ['sdate'];
			foreach ( $content ['fields'] as $k => $con ) {
				if (empty ( $con ['htmls'] )) {
					$con ['htmls'] = $con ['bodytexts'];
				}
				$is_text = 0;
				switch ($con ['schema'] ['fieldName']) {
					case 'question' :
						$filed = 'content';
						$is_text = 1;
						break;
					case 'analysis' :
						$filed = 'analysis';
						$is_text = 1;
						break;
					case 'answers' :
						$filed = 'answers';
						$is_text = 1;
						break;
					case 'dept' :
						$filed = 'gradeDeptName';
						break;
					case 'subject' :
						$filed = 'subjectName';
						break;
					case 'classtype' :
						$filed = 'courseTypeName';
						break;
					case 'qtype' :
						$filed = 'questionTypeName';
						break;
					case 'mainkp' :
						$filed = 'knowledgeName';
						break;
					case 'subkp' :
						$filed = 'subKnowledgeName';
						break;
					case 'diff' :
						$filed = 'difficulty';
						break;
					case 'grades' :
						$filed = 'gradesName';
						break;
					case 'choices' :
						$filed = 'options';
						$is_text = 1;
						break;
					case 'question_number' :
						$filed = 'question_number';
						break;
					case 'source' :
						$filed = 'source';
						break;
					case 'year' :
						$filed = 'year';
						break;
					case 'city' :
						$filed = 'city';
						break;
					case 'county' :
						$filed = 'county';
						break;
					case 'school' :
						$filed = 'school';
						break;
					case 'paper_grades' :
						$filed = 'paper_grades';
						break;
					case 'term' :
						$filed = 'term';
						break;
					case 'name' :
						$filed = 'name';
						break;
					case 'curr_dept' :
						$filed = 'curr_dept';
						break;
					case 'score' :
						$filed = 'score';
						break;
				}
				if ($filed != 'options') {
					$arr [$key] [$filed] = $con ['htmls'] [0];
					if ($is_text == 1)
						$arr [$key] [$filed . '_text'] = $con ['bodytexts'] [0];
				} else {
					if (! empty ( $con ['htmls'] )) {
						$optionsArr = array (
								'A',
								'B',
								'C',
								'D',
								'E',
								'F',
								'G',
								'H',
								'I',
								'J' 
						);
						foreach ( $con ['htmls'] as $k => $v ) {
							$arr [$key] [$filed] [$k] ['title'] = $optionsArr [$k];
							$arr [$key] [$filed] [$k] ['content'] = $v;
							$arr [$key] [$filed] [$k] ['content_text'] = $con ['bodytexts'] [$k];
						}
					}
				}
			}
		}
		// var_dump($arr);die();
		if (! empty ( $arr )) {
			$return = D ( 'Basic' )->importQuestion ( $arr, $title );
		}
		
		var_dump ( $return );
		die ();
	}
	public function getPapersDetail() {
		$params = $this->_param ();
		$currentPage = $params ['page'];
		$pageSize = $params ['rows'];
		$sort = $params ['sort'];
		$order = $params ['order'];
		$kw = $params ['kw'];
		$gradeId = $params ['grade_id'];
		$subjectId = $params ['subject_id'];
        $cityId = $params['city_id'];
        $countryId = $params['country_id'];
        $seTime = $params['se_time'];
        $sourceId = $params['source_id'];
        //print_r($params);exit;
		$where = '';
		if (! empty ( $kw )) {
			$where .= ' and a.file_name LIKE \'%' . $kw . '%\'';
		}
		if (! empty ( $gradeId )) {
			$where .= ' and a.grade_id = \'' . $gradeId . '\'';
		}
		if (! empty ( $subjectId )) {
			$where .= ' and a.subject_id = \'' . $subjectId . '\'';
		}
        if (! empty ( $cityId)) {
            $cityInfo = D('Basic')->getCityNameOne($cityId);
            $where .= ' and a.province = "'.$cityInfo.'" ';
        }
        if (! empty ($countryId)) {
            $cityInfo = D('Basic')->getCityNameOne($countryId);
            $where .= ' and a.city = "'.$cityInfo.'"';
        }
        if (! empty ($seTime)) {
            $where .= ' and a.year = "'.$seTime.'"';
        }

        if (! empty ($sourceId)) {
            $typeName = $this->source();
            foreach($typeName as  $k=>$v)
            {
                if($v['id'] == $sourceId)
                {
                    $sourceName = $v['name'];
                    break;
                }
            }

            $where .= ' and a.name = "'.$sourceName.'"';  //按来源查询错误 来源不对

            //echo $where;exit;
        }

		$papers = D ( 'Basic' )->getPapersDetailByWhere ( $where, $currentPage, $pageSize, $sort, $order );

 		$tstjpPageSize = '100000000';
        $tstjInfo = D ( 'Basic' )->getPapersDetailByWhere ( $where, $currentPage, $tstjpPageSize, $sort, $order );
        $tishu = '';
        foreach($tstjInfo['rows'] as $key=>$val ){
           $tishu += $val['question_count'];

        }
        $taojuanshu = count($tstjInfo['rows']);      


        array_unshift ( $papers['rows'], array (
            'id' => '',
            'tishu' => $tishu,
            'taojuanshu'=>$taojuanshu
        ) );




		$this->outPut ( $papers );
	}
	public function paper_detail_list() {
		$this->display ();
	}
	public function paper_detail_edit() {
		$params = $this->_param ();
		$info = D ( 'Basic' )->getPaperInfoById( $params['id'] );
		//过滤来源 
		switch ($info['source']) {
			case '期末考试真题':
				$info['source']="期末考试";
				break;
			case '期中考试真题':
				$info['source']="期中考试";
				break;
			case '中考一模真题':
				$info['source']="中考一模";
				break;
			case '中考二模真题':
				$info['source']="中考二模";
				break;
			case '高考一模真题':
				$info['source']="高考一模";
				break;
			case '高考二模真题':
				$info['source']="高考二模";
				break;
			case '其他模拟':
				$info['source']="其他";
				break;
		}
		$this->assign ( 'info', $info );
		$this->display ();
	}
	public function paper_detail_save() {
		$params = $this->_param ();

		$userInfo = $this->loginUser->getInformation ();
		$data = array();
		$data['id'] = $params['id'];
		$data['file_name'] = $params['file_name'];
		$data['show_name'] = $params['show_name'];
		$data['grade_id'] = $params['grade_id'];
		$data['subject_id'] = $params['subject_id'];
		$data['province'] = $params['city_id'];
		$data['city'] = $params['country_id'];
		$data['test_name'] = $params['test_name'];
		$data['year'] = $params['year'];
		$data['school'] = $params['school'];
		$data['term'] = $params['term'];
		$data['source'] = $params['name'];
		$data['grades']=$params['grades'];
		$data['name'] = $params['name'];
		$data['score'] = $params['score'];
		$data['duration'] = $params['duration'];
		$data['question_number'] = $params['question_number'];
		$data['last_updated_user_name']=$userInfo['user_name'];
		$data['last_updated_time']=time();
        $data['show_start'] = $params['show_start'];
        //print_r($params);exit;
		$result = D ( 'Basic' )->setPaperInfoByData( $data );
		
		if ($result) {
			$this->success ();
		}
		$this->error ();
	}
	public function getQuestionCurrentEdit() {
		$userInfo = $this->loginUser->getInformation ();
		$params = $this->_param ();
		$row = D ( 'Basic' )->getQuestionCurrentEditByUserName ( $userInfo ['user_name'], $params ['coursetypeid'] );
		if ($row) {
			echo 1;
		} else {
			echo 0;
		}
	}
	public function getQuestionCurrentEdit1() {
		$userInfo = $this->loginUser->getInformation ();
		$params = $this->_param ();
		$row = D ( 'Basic' )->getQuestionCurrentEditByUserName1 ( $userInfo ['user_name'], $params ['coursetypeid'] );
		if ($row) {
			echo 1;
		} else {
			echo 0;
		}
	}
	public function getQuestionCurrentEdit2() {
		$userInfo = $this->loginUser->getInformation ();
		$params = $this->_param ();
		$row = D ( 'Basic' )->getQuestionCurrentEditByUserName2 ( $userInfo ['user_name'], $params ['coursetypeid'] );
		if ($row) {
			echo 1;
		} else {
			echo 0;
		}
	}
	public function set_question_classic() {
		$params = $this->_param ();
		$questionId = $params ['question_id'];
		
		$result = D ( 'Basic' )->setQuestionClassic ( $questionId );
		
		if ($result) {
			$this->success ();
		}
		$this->error ();
	}
	public function question_single_view() {
		$this->display ();
	}
	public function render_question_single() {
		$params = $this->_get ();
		$questionId = $params ['id'];
		$question = D ( 'Basic' )->getPaperQuestionFullByID ( $questionId );
		$this->assign ( 'question', $question );
		
		$this->display ();
	}
	public function updateQuestion() {
		$params = $this->_post ();
		$questionId = $params ['question_id'];
		$questionTypeId = $params ['question_type_id'];
		$difficulty = $params ['difficulty'];
		$grades = arr2str ( $params ['grades'], ',' );
		$knowledgeId = $params ['knowledge_id'];
		$subKnowledgeId = $params ['sub_knowledge_id'];
		$result = D ( 'Basic' )->updateQuestion ( $questionId, $questionTypeId, $difficulty, $grades, $knowledgeId, $subKnowledgeId );
		
		if ($result) {
			$this->success ();
		}
		$this->error ();
	}
	public function get_question_unique() {
		$this->outPut ( array (
				'uid' => get_uuid (),
				'path' => get_upload_dir () 
		) );
	}

	//通过学部获取年级
	public function getGradeSubject()
	{
		$params=$this->_get();
		$this->output(D('Basic')->getGradeSubjects($params['grade_id'],$params['subject_id']));
	}

	//获取类型
	public function getSource()
	{
		$source=array(
			array('source'=>'真题'),
			array('source'=>'模拟题'),
			array('source'=>'原创题')
			);
		$this->output($source);
	}

	//获取来源
	public function getTypeName()
	{
        $name = $this->source();
		$params=$this->_get();
		$data=array();
		if(!empty($params['grade_id']) && !empty($params['subject_id']) && !in_array($params['grade_id'],['2','3']))
		{
			//根据学部和学科获取来源
			foreach($name as $k =>$v)
			{
				if($v['grade_id'] == $params['grade_id'] && $v['subject_id'] == $params['subject_id'])
				{
					$data[]=$v;
				}
			}
		}elseif(!empty($params['grade_id']) && !empty($params['subject_id']) && in_array($params['grade_id'],['2','3']))
		{
			//根据学部和学科获取来源
			foreach($name as $k =>$v)
			{
				if($v['grade_id'] == $params['grade_id'])
				{
					$data[]=$v;
				}
			}
		}else{
			//如果没有学科，只有学部
			foreach($name as  $k=>$v)
			{
				if($v['grade_id'] == $params['grade_id'] &&  !isset($v['subject_id']))
				{
					$data[]=$v;
				}
			}
		}
        //print_r($data);exit;
        array_unshift ( $data, array (
            'id' => '',
            'name' => '请选择来源...'
        ) );
		$this->output($data);
	}
    public function source(){
        $name=array(
            array('id'=>1,'grade_id'=>1,'subject_id'=>2,'name'=>'小升初试题'),
            array('id'=>2,'grade_id'=>1,'subject_id'=>2,'name'=>'杯赛真题'),
            array('id'=>3,'grade_id'=>1,'subject_id'=>2,'name'=>'期中期末',),
            array('id'=>4,'grade_id'=>1,'subject_id'=>2,'name'=>'月考卷'),
            array('id'=>5,'grade_id'=>1,'subject_id'=>2,'name'=>'分班试题'),
            array('id'=>6,'grade_id'=>1,'subject_id'=>1,'name'=>'小升初试题'),
            array('id'=>7,'grade_id'=>1,'subject_id'=>1,'name'=>'龙校'),
            array('id'=>8,'grade_id'=>1,'subject_id'=>1,'name'=>'金帆'),
            array('id'=>9,'grade_id'=>1,'subject_id'=>1,'name'=>'迎春杯'),
            array('id'=>10,'grade_id'=>1,'subject_id'=>1,'name'=>'华杯赛'),
            array('id'=>11,'grade_id'=>1,'subject_id'=>1,'name'=>'希望杯'),
            array('id'=>12,'grade_id'=>1,'subject_id'=>1,'name'=>'走美杯'),
            array('id'=>13,'grade_id'=>1,'subject_id'=>1,'name'=>'高思导引'),
            array('id'=>14,'grade_id'=>1,'subject_id'=>1,'name'=>'分班试题'),
            array('id'=>15,'grade_id'=>1,'subject_id'=>1,'name'=>'其它'),
            array('id'=>16,'grade_id'=>1,'subject_id'=>3,'name'=>'小升初试题'),
            array('id'=>17,'grade_id'=>1,'subject_id'=>3,'name'=>'杯赛真题'),
            array('id'=>18,'grade_id'=>1,'subject_id'=>3,'name'=>'期中考试'),
            array('id'=>19,'grade_id'=>1,'subject_id'=>3,'name'=>'期末考试'),
            array('id'=>20,'grade_id'=>1,'subject_id'=>3,'name'=>'其他'),
            array('id'=>21,'grade_id'=>1,'subject_id'=>3,'name'=>'分班试题'),
            array('id'=>22,'grade_id'=>2,'name'=>'中考真题'),
            array('id'=>23,'grade_id'=>2,'name'=>'中考一模'),
            array('id'=>24,'grade_id'=>2,'name'=>'中考二模'),
            array('id'=>25,'grade_id'=>2,'name'=>'期中考试'),
            array('id'=>26,'grade_id'=>2,'name'=>'期末考试'),
            array('id'=>27,'grade_id'=>2,'name'=>'其他'),
            array('id'=>38,'grade_id'=>3,'name'=>'高考真题'),
            array('id'=>39,'grade_id'=>3,'name'=>'高考一模'),
            array('id'=>30,'grade_id'=>3,'name'=>'高考二模'),
            array('id'=>31,'grade_id'=>3,'name'=>'期中考试'),
            array('id'=>32,'grade_id'=>3,'name'=>'期末考试'),
            array('id'=>33,'grade_id'=>3,'name'=>'其他'),

            array('id'=>34,'grade_id'=>6,'subject_id'=>44,'name'=>'教材同步'),
            array('id'=>35,'grade_id'=>6,'subject_id'=>44,'name'=>'AMC真题'),
            array('id'=>36,'grade_id'=>6,'subject_id'=>44,'name'=>'其他竞赛'),
            array('id'=>37,'grade_id'=>6,'subject_id'=>44,'name'=>'IB真题'),
            array('id'=>38,'grade_id'=>6,'subject_id'=>44,'name'=>'A-Level真题'),
            array('id'=>39,'grade_id'=>6,'subject_id'=>44,'name'=>'AP真题'),
            array('id'=>39,'grade_id'=>6,'subject_id'=>44,'name'=>'其他'),

            array('id'=>40,'grade_id'=>6,'subject_id'=>45,'name'=>'教材同步'),
            array('id'=>41,'grade_id'=>6,'subject_id'=>45,'name'=>'IB真题'),
            array('id'=>42,'grade_id'=>6,'subject_id'=>45,'name'=>'A-Level真题'),
            array('id'=>43,'grade_id'=>6,'subject_id'=>45,'name'=>'AP真题'),
            array('id'=>45,'grade_id'=>6,'subject_id'=>45,'name'=>'其他'),

            array('id'=>46,'grade_id'=>6,'subject_id'=>46,'name'=>'教材同步'),
            array('id'=>47,'grade_id'=>6,'subject_id'=>46,'name'=>'IB真题'),
            array('id'=>48,'grade_id'=>6,'subject_id'=>46,'name'=>'A-Level真题'),
            array('id'=>49,'grade_id'=>6,'subject_id'=>46,'name'=>'AP真题'),
            array('id'=>50,'grade_id'=>6,'subject_id'=>46,'name'=>'其他'),

            array('id'=>46,'grade_id'=>7,'subject_id'=>51,'name'=>'自主招生真题'),
            array('id'=>47,'grade_id'=>7,'subject_id'=>51,'name'=>'联赛预赛'),
            array('id'=>48,'grade_id'=>7,'subject_id'=>51,'name'=>'联赛一试'),
            array('id'=>49,'grade_id'=>7,'subject_id'=>51,'name'=>'联赛二试'),
            array('id'=>49,'grade_id'=>7,'subject_id'=>51,'name'=>'奥林匹克竞赛'),
            array('id'=>50,'grade_id'=>7,'subject_id'=>51,'name'=>'其他'),     

            array('id'=>46,'grade_id'=>7,'subject_id'=>52,'name'=>'自主招生真题'),
            array('id'=>47,'grade_id'=>7,'subject_id'=>52,'name'=>'联赛预赛'),
            array('id'=>48,'grade_id'=>7,'subject_id'=>52,'name'=>'联赛一试'),
            array('id'=>49,'grade_id'=>7,'subject_id'=>52,'name'=>'联赛二试'),
            array('id'=>49,'grade_id'=>7,'subject_id'=>52,'name'=>'奥林匹克竞赛'),
            array('id'=>50,'grade_id'=>7,'subject_id'=>52,'name'=>'其他'), 

            array('id'=>46,'grade_id'=>7,'subject_id'=>53,'name'=>'自主招生真题'),
            array('id'=>47,'grade_id'=>7,'subject_id'=>53,'name'=>'联赛预赛'),
            array('id'=>48,'grade_id'=>7,'subject_id'=>53,'name'=>'联赛一试'),
            array('id'=>49,'grade_id'=>7,'subject_id'=>53,'name'=>'联赛二试'),
            array('id'=>49,'grade_id'=>7,'subject_id'=>53,'name'=>'奥林匹克竞赛'),
            array('id'=>50,'grade_id'=>7,'subject_id'=>53,'name'=>'其他'), 

            array('id'=>46,'grade_id'=>7,'subject_id'=>54,'name'=>'自主招生真题'),
            array('id'=>47,'grade_id'=>7,'subject_id'=>54,'name'=>'联赛预赛'),
            array('id'=>48,'grade_id'=>7,'subject_id'=>54,'name'=>'联赛一试'),
            array('id'=>49,'grade_id'=>7,'subject_id'=>54,'name'=>'联赛二试'),
            array('id'=>49,'grade_id'=>7,'subject_id'=>54,'name'=>'奥林匹克竞赛'),
            array('id'=>50,'grade_id'=>7,'subject_id'=>54,'name'=>'其他'), 

            array('id'=>46,'grade_id'=>7,'subject_id'=>55,'name'=>'自主招生真题'),
            array('id'=>47,'grade_id'=>7,'subject_id'=>55,'name'=>'联赛预赛'),
            array('id'=>48,'grade_id'=>7,'subject_id'=>55,'name'=>'联赛一试'),
            array('id'=>49,'grade_id'=>7,'subject_id'=>55,'name'=>'联赛二试'),
            array('id'=>49,'grade_id'=>7,'subject_id'=>55,'name'=>'奥林匹克竞赛'),
            array('id'=>50,'grade_id'=>7,'subject_id'=>55,'name'=>'其他'), 
        );
        return $name;
    }

	//获取学期
	public function getGradeTerm()
	{
		$term=array(
			array('term'=>'上学期'),
			array('term'=>'下学期')
			);
		$this->output($term);
	}
	
	
	/*试题统计*/
	public function questionStatistic(){
		$basicModel = D ( 'Basic' );
		$condition = $this->get_roleCondition ();
		$statistics = $basicModel->getQuestionStatistics ( $condition );
		$this->assign ( 'statistics', $statistics );
		$this->display ();
	}

	//获取所有省
	public function getByCity()
	{
		$this->output(D('Basic')->getCitys());
	}
	//通过省id获取市
	public function getCountryByCityId()
	{
		$params = $this->_param ();

		$cityid=$params['cityid'];
        if($cityid != '') {
            $this->output(D('Basic')->getCountryByCityId($cityid));
        }
	}

    //年份
    public function getSearchYear(){
        $years = array();
        $currentYear = date('Y',strtotime('2008-01-01'));
        for ($i=0; $i<15; $i++)
        {
            $years[$i]['id'] = $i;
            $years[$i]['time'] = $currentYear + $i;
        }

        array_unshift ( $years, array (
            'id' => '',
            'time' => '请选择年份...'
        ) );
        $this->outPut($years);
    }



	/******补全数据*****/
    // public function doUpdate()
    // {
    // 	$model=D('Basic');
    // 	$model->updateQuestions($val);
    // }
}
?>
