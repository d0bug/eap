<?php
/*普通接口类*/
class VipApiAction extends VipCommAction{
	protected function notNeedLogin() {
		return array('VIP-VIPAPI-GETERRORQUESTION','VIP-VIPAPI-GETLESSONHELUINFO');
	}
	public function getErrorQuestion(){
		$conditionArr = array();
		if(!empty($_GET['id'])){
			$conditionArr['helu_id'] = $_GET['id'];
		}
		if(!empty($_GET['scode'])){
			$conditionArr['student_code'] = $_GET['scode'];
		}
		if(!empty($_GET['kcode'])){
			$conditionArr['kecheng_code'] = $_GET['kcode'];
		}
		$newStudentsModel = D('VpNewStudents');
		$kechengList = array();
		$lessonList = array();
		$kechengList = $newStudentsModel->get_kechengAll($conditionArr);
		$lessonList = $newStudentsModel->get_lessonAll($conditionArr);
	
		$errorQuestionList = $newStudentsModel->get_errorQuestionList_api($conditionArr,1);
		if(!empty($errorQuestionList)){
			foreach ($errorQuestionList as $key=>$question){
				$errorQuestionList[$key]['question_desc']['question_option'] = $question['question_option'];
				unset($errorQuestionList[$key]['question_option']);
			}
		}
		$errorQuestionList = array_values($errorQuestionList);
		//echo $this->encode_json(array('current_kcode'=>$_GET['kcode'],'current_id'=>$_GET['id'],'kechengList'=>$kechengList,'lessonList'=>$lessonList,'errorQuestion'=>$errorQuestionList));
		echo json_encode(array('current_kcode'=>$_GET['kcode'],'current_id'=>$_GET['id'],'kechengList'=>$kechengList,'lessonList'=>$lessonList,'errorQuestion'=>$errorQuestionList));
	}
	
	
	// 格式化json中的汉字函数
	protected function encode_json($str) {
		$strs = urldecode(json_encode($this->url_encode($str)));
		return $strs;
	}
	protected function url_encode($str) {
		if(is_array($str)) {
			foreach($str as $key=>$value) {
				$str[urlencode($key)] = $this->url_encode($value);
			}
		} else {
			$str = urlencode($str);
		}
		return $str;
	}

	
	
	public function getLessonHeluInfo(){
		$helu_id = intval($_GET['id']);
		$status = 0;
		if(!empty($helu_id)){
			$status = 1;
			$studentsModel = D('VpStudents');
			$heluInfo = $studentsModel->get_heluInfo(array('helu_id'=>$helu_id));
			if(!$heluInfo){
				$heluInfo = "";
			}
		}
		
		echo json_encode(array('status'=>$status,'heluInfo'=>$heluInfo));
	}
}
?>