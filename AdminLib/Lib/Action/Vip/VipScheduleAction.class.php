<?php
/*我的课表*/
class VipScheduleAction extends VipCommAction{
	protected function notNeedLogin() {
		return array('VIP-VIPSCHEDULE-GETKECHENGLIST');
	}

	public function index(){
		$year = date('Y');
		$month = date('n');
		$day = date('j');
		$now = date('Y-m-d H:i:s');
		$userInfo = VipCommAction::get_currentUserInfo();
		if($userInfo['user_key'] == 'Employee-wangyan'){
			$userInfo['sCode'] = 'VP00022';
		}
		$this->assign(get_defined_vars());
		$this->display();
	}


	public function monthSchedule(){
		$year = date('Y');
		$month = date('n');
		$day = date('j');
		$now = date('Y-m-d H:i:s');
		$userInfo = VipCommAction::get_currentUserInfo();
		if($userInfo['user_key'] == 'Employee-wangyan'){
			$userInfo['sCode'] = 'VP00022';
		}
		$this->assign(get_defined_vars());
		$this->display("monthSchedule");
	}

	//获取课程数据
	public function getSchedule(){
		$userInfo = VipCommAction::get_currentUserInfo();
		if($userInfo['user_key'] == 'Employee-wangyan'){
			$userInfo['sCode'] = 'VP00022';
		}
		$start = intval($_GET['start']);
		$end = intval($_GET['end']);
		$date = SysUtil::safeString ( $_POST ['date'] );
		if(!empty($date)){
			$start = strtotime($date);
			$end = $start + 3600 * 24;
		}
		if (strlen($start) < 10 || $end < $start) {
			die (json_encode(array()));
		}
		if($_GET['schedule']=='month'){
			$year = date('Y',$start+3600*24*10);
			$month = date('m',$start+3600*24*10);
			$start = strtotime($year."-".$month."-01");
			$end = strtotime($year."-".$month."-".date('t',$start+3600*24*10)." 23:59:59");
		}
		$studentsModel = D('VpStudents');
		$scheduleResult = $studentsModel->get_scheduleList(array('teacher_code'=>$userInfo['sCode'],'start'=>$start,'end'=>$end));
		$scheduleList = $scheduleResult['list'];
		$eventsText = '';
		if(!empty($scheduleList)){
			foreach ($scheduleList as $key=>$schedule){
				if($schedule){
					$array = array (
					'helu_id'=>$schedule ['id'],
					'title' => $schedule ['sstudentname'],
					'sAreaName' => $schedule ['sareaname'],
					'nHours' => $schedule ['nhoursreal'],
					'start' => $schedule ['dtlessonbeginreal'],
					'end' => $schedule ['dtlessonendreal'],
					'allDay' => false,
					'bgcolor' => ($schedule['is_end']==1)?'bg-blue':'bg-yellow',
					'total_hours' => $scheduleResult['total_hours'],
					'total_students' => $scheduleResult['total_students'],
					'stuCode' => $schedule['sstudentcode'],
					'keCode' => $schedule ['skechengcode'],
					'lesson' => $schedule ['nlessonno'],
					'dateReal'=>date('Y-m-d',strtotime($schedule['dtlessonbeginreal'])),
					'is_begin'=>$schedule['is_begin'],
					'is_end'=>$schedule['is_end']
					);
					$array ['classTimeCir'] = date('H:i',strtotime($schedule ['dtlessonbeginreal'])).'~'.date('H:i',strtotime($schedule['dtlessonendreal']));
					$array ['now'] = date('Y-m-d H:i',time());
					$array ['max'] = date('Y-m-d 23:00:00');
				}
				$arr_lessonSchedule [] = $array;
			}
		}		
		die  (json_encode($arr_lessonSchedule));
	}


	/*调课*/
	protected function adjustKecheng(){
		$studentsModel = D('VpStudents');
		$userInfo = VipCommAction::get_currentUserInfo();
		$kechengInfo = $studentsModel->get_kechengInfo(array('id'=>$_POST['helu_id']));
		$kechengInfo['dtdatereal'] = date('Y-m-d 00:00:00',strtotime($_POST['start']));
		$kechengInfo['dtlessonbeginreal'] = $_POST['start'];
		$kechengInfo['dtlessonendreal'] = $_POST['end'];
		if($_POST['start']<=date('Y-m-d H:i:s')){
			$status = 0;
			$msg = '选择的时间已过期，请选择有效的时间段';
		}else{
			if(!$studentsModel->checkIsCanOperate($kechengInfo,1)){
				$status = 0;
				$msg = '学员上课时间存在交叉，调课失败！';
			}else{
				ini_set("soap.wsdl_cache_enabled", "0");
				$param = array('id'=>abs($_POST['helu_id']),
				'sTeacherCode'=>$userInfo['sCode'],
				'dtDateReal'=>strtotime(date('Y-m-d',strtotime($_POST['start'])).' 00:00:00'),
				'dtLessonBeginReal'=>strtotime($_POST['start']),
				'dtLessonEndReal'=>strtotime($_POST['end'])
				);
				try {
					$soap = new SoapClient(C('aspxWebService'));
					$result = $soap->doTeacherUpdateLessonDate($param);
					$resultArr = VipCommAction::object2array($result);
					if(empty($resultArr['doTeacherPerPKResult'])){
						$status = 1;
						$msg = '调课成功';
					}else{
						$status = 0;
						$msg = '调课失败'.$resultArr['doTeacherPerPKResult'];
					}
				}catch(SoapFault $fault){
					$status = 0;
					$msg= "调课失败: ".$fault->faultstring."(".$fault->faultcode.")";
				}catch(Exception $e){
					$status = 0;
					$msg= "调课失败: ".$e->getMessage();
				}
			}
		}
		echo json_encode(array('status'=>$status,'msg'=>$msg));
	}


	/*获取该教师的所有学生*/
	protected function getAllStudents(){
		$studentHtml = '';
		$userInfo = VipCommAction::get_currentUserInfo();
		if(!empty($userInfo['sCode'])){
			$students = D('VpStudents')->get_allStudents(array('is_jieke'=>1,'teacherCode'=>$userInfo['sCode']));
			if(!empty($students)){
				foreach ($students as $key=>$stu){
					$studentHtml .= '<a href="#none" onclick="javascript:$(\'#student_code\').val(\''.$stu['sstudentcode'].'\');get_kechengList(\''.$stu['sstudentcode'].'\',\''.$userInfo['sCode'].'\',\''.U('Vip/VipSchedule/getKechengList').'\');$(\'#studentHtml a\').each(function(i,val){$(this).removeClass(\'orange\');});$(this).addClass(\'orange\')">'.$stu['sstudentname'].'</a>&nbsp;&nbsp;';
				}
			}
		}else{
			$studentHtml .='对不起，您不是VIP教师，查询不到您的学员';
		}
		echo $studentHtml;
	}


	public function getKechengList(){
		$kechengHtml = '';
		$kechengList = D('VpStudents')->get_kechengAll(array('is_jieke'=>1,'studentCode'=>$_GET['stuCode'],'teacherCode'=>$_GET['teacherCode']));
		if(!empty($kechengList)){
			foreach ($kechengList as $key=>$kecheng){
				$kechengHtml .= '<a href="#none" onclick="javascript:$(\'#kecheng_code\').val(\''.$kecheng['skechengcode'].'\');$(\'#max_lesson\').val(\''.$kecheng['nlessonno'].'\');$(\'#kechengHtml a\').each(function(i,val){$(this).removeClass(\'orange\');});$(this).addClass(\'orange\')">'.$kecheng['skechengname'].'('.$kecheng['skechengcode'].')</a>&nbsp;&nbsp;';
			}
		}
		echo $kechengHtml;
	}

	/*加课*/
	protected function addKecheng(){
		$studentsModel = D('VpStudents');
		$userInfo = VipCommAction::get_currentUserInfo();
		$kechengInfo = $studentsModel->get_kechengInfo(array('kecheng_code'=>$_POST['kecheng_code'],'student_code'=>$_POST['student_code'],'teacher_code'=>$userInfo['sCode']));
		$kechengInfo['dtdatereal'] = date('Y-m-d 00:00:00',strtotime($_POST['start']));
		$kechengInfo['dtlessonbeginreal'] = $_POST['start'];
		$kechengInfo['dtlessonendreal'] = $_POST['end'];
		if($_POST['start']<=date('Y-m-d H:i:s')){
			$status = 0;
			$msg = '选择的时间已过期，请选择有效的时间段';
		}else{
			if(!$studentsModel->checkIsCanOperate($kechengInfo,1)){
				$status = 0;
				$msg = '学员上课时间存在交叉，加课失败';
			}else{
				ini_set("soap.wsdl_cache_enabled", "0");
				$param = array('nRosterInfoId'=>abs($kechengInfo['nrosterinfoid']),
				'sTeacherCode'=>$userInfo['sCode'],
				'dtDateReal'=>strtotime(date('Y-m-d',strtotime($_POST['start'])).' 00:00:00'),
				'dtLessonBeginReal'=>strtotime($_POST['start']),
				'dtLessonEndReal'=>strtotime($_POST['end']),
				'nPrePKNum'=>1
				);
				try {
					$soap = new SoapClient(C('aspxWebService'));
					$result = $soap->doTeacherPerPK($param);
					$resultArr = VipCommAction::object2array($result);
					if(empty($resultArr['doTeacherPerPKResult'])){
						$status = 1;
						$msg = '加课成功';
					}else{
						$status = 0;
						$msg = '加课失败：'.$resultArr['doTeacherPerPKResult'];
					}
				}catch(SoapFault $fault) {
					$status = 0;
					$msg= "加课失败: ".$fault->faultstring."(".$fault->faultcode.")";
				}catch(Exception $e) {
					$status = 0;
					$msg= "加课失败: ".$e->getMessage();
				}
			}
		}

		echo json_encode(array('status'=>$status,'msg'=>$msg));
	}

}

?>