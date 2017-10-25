<?php
/*VIP教师系统计划任务*/
class VipCrontabAction extends VipCommAction{
	protected function notNeedLogin() {
		return array('VIP-VIPCRONTAB-UPDATELECTUREARCHIVE');
	}
	
	/*标准化讲义批量更新*/
	public function updateLectureArchive(){
		$crontabModel = D('VpCrontab');
		$this->updateLecture(1,1);//小学数学测试
		/*$subjectArr = $crontabModel->getSubjectAll();
		if(!empty($subjectArr)){
			foreach ($subjectArr as $key=>$subject){
				$this->updateLecture($subject['id'],1);//标准化讲义库1,2教师讲义
			}
		}*/
		
	}
	
	
	
	public function updateLecture($sid,$type){
		$crontabModel = D('VpCrontab');
		$lectureList = $crontabModel->getLectureAll($sid,$type);
		$resultStr = '';
		if(!empty($lectureList)){
			foreach ($lectureList as $key=>$lecture){
				$knowledgeIdArr = array();
				if(!empty($lecture['cart']['cart']['special_list'])){
					foreach ($lecture['cart']['cart']['special_list'] as $k=>$special){
						$knowledgeIdArr[] = $special['id'];
					}
				}
				if(!empty($knowledgeIdArr)){
					$knowledgeList = $crontabModel->getKnowledge($knowledgeIdArr);
					foreach ($lecture['cart']['cart']['special_list'] as $k=>$special){
						$lecture['cart']['cart']['special_list'][$k]['title'] = $knowledgeList[$special['id']];
					}
					foreach ($lecture['config']['struct']['body']['special']['types'] as $k=>$special){
						$lecture['config']['struct']['body']['special']['types'][$k]['tips'] = $knowledgeList[$special['id']];
					}
				}
				$result = $crontabModel->updateLectureCartAndConfig($lecture['cart'],$lecture['config'],$lecture['id'],$type);
				if($result){
					$resultStr.= $lecture['id'].'-'.$lecture['title'].'-成功<br>';
				}else{
					$resultStr.= $lecture['id'].'-'.$lecture['title'].'-失败<br>';
				}
			}
		}
		
		echo $resultStr;die;
		
	}

	
}

?>