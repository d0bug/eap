<?php
/**
 * VIP知识库API
 * 用途：试题批量导入
 */
class QuestionAction extends ApiCommAction {
	public function test() {
		
		$file = APP_DIR . '/AdminLib/Runtime/data.txt';
		$data = file_get_contents ( $file );
		$data = unserialize ( $data );
		
		$newData = $this->formatData ( $data );
		if (empty ( $newData [2] )) {
			return - 1;
		}
		$basicModel = D ( 'Basic' );
		if (! empty ( $newData )) {
			$result = $basicModel->importQuestion ( $newData [0], $newData [1] );
		}
		
		// return $data;
		return 0;
	}
	
	/**
	 * 批量导入试题
	 * author:xiecuiping
	 * date:2014-08-12
	 */
	protected function importQuestion($data) {
		$file = APP_DIR . '/AdminLib/Runtime/data.txt';
		
		file_put_contents ( $file, serialize ( $data ) );
		 //$data = fopen("/data/wwwroot/eap/AdminLib/Runtime/data.txt","r");
		$newData = $this->formatData ( $data );
		if (empty ( $newData [2] )) {
			return - 1;
		}
		$basicModel = D ( 'Basic' );
		if (! empty ( $newData )) {
			$result = $basicModel->importQuestion ( $newData [0], $newData [1] );
		}
		
		return $data;
	}
	
	/**
	 * 处理读取的文档内容
	 *
	 * @param array $content        	
	 * @return array
	 */
	protected function formatData($content) {
		$new_content = ( array ) ($content);
		$title = $new_content ['docid'];
		$userId = $new_content ['userid'];
		$new_content = $new_content ['records'];
		foreach ( $new_content as $key => $con ) {
			$new_content [$key] = ( array ) ($con);
			foreach ( $new_content [$key] ['fields'] as $k => $c ) {
				$new_content [$key] ['fields'] [$k] = ( array ) ($c);
				$new_content [$key] ['fields'] [$k] ['schema'] = ( array ) ($new_content [$key] ['fields'] [$k] ['schema']);
				$new_content [$key] ['fields'] [$k] ['fieldData'] = ( array ) ($new_content [$key] ['fields'] [$k] ['fieldData']);
			}
		}
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
					case 'content' : // 题干
						$filed = 'content';
						$is_text = 1;
						break;
					case 'analysis' :
						$filed = 'analysis'; // 解析
						$is_text = 1;
						break;
					case 'answers' : // 答案
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
					case 'qtype' : // 题型
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
					case 'grades' : // 使用年级
						$filed = 'gradesName';
						break;
					case 'choices' : // 选项
						$filed = 'options';
						$is_text = 1;
						break;
					case 'grades' :
						$filed = 'gradesName';
						break;
					case 'question_number' : // 题号
						$filed = 'question_number';
						break;
					case 'source' :
						$filed = 'source';
						break;
					case 'source1' :
						$filed = 'source1';
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
					case 'score' : // 分值
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
							$arr [$key] [$filed] [$k] ['uid'] = $con ['uids'] [$k];
						}
					}
				}
			}
		}
		return array (
				$arr,
				$title,
				$userId 
		);
	}


	public function getQuestionsForXgsWx(){
		if($_GET['key']==md5('aitifen2016')){
			$questionArr = array();
			$radioselectNum = !empty($_GET['radioselect'])?$_GET['radioselect']:10;//单选
			$multiselectNum = !empty($_GET['multiselect'])?$_GET['multiselect']:5;//多选
			$fillblanksNum = !empty($_GET['fillblanks'])?$_GET['fillblanks']:5;//填空
			$model = new BasicModel ();
			$questionArr['radioselect'] = $model->getQuestionsByCondition('单选题','业务部',$radioselectNum);
			$questionArr['multiselect'] = $model->getQuestionsByCondition('多选题','业务部',$multiselectNum);
			$questionArr['fillblanks'] = $model->getQuestionsByCondition('填空题','业务部',$fillblanksNum);
			echo json_encode($questionArr);
		}else{
			echo '非法操作';
		}

	}
}
?>