<?php 
/**
 * 我的业绩
 */

class VipAchevementAction extends VipCommAction
{
	public function lesson()
	{
		$student_name = isset($_GET['student_name'])?trim($_GET['student_name']):'';
        $status=isset($_GET['status'])?$_GET['status']:'';
		$begin_time=isset($_GET['begin_time'])?$_GET['begin_time']:'';
		$end_time=isset($_GET['end_time'])?$_GET['end_time']:'';
		$userInfo = VipCommAction::get_currentUserInfo();
		if($userInfo['user_key'] == 'Employee-wangyan'){
			$userInfo['sCode'] = 'VP00022';
		}

		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');

		$studentsModel = D('VpStudents');
		$condition = '';

		if($userInfo['sCode']){
			$now = date('Y-m-d H:i:s');
			$myStudentList = $studentsModel->getStudentLesson(array('teacherCode'=>$userInfo['sCode'],'status'=>$status,'student_name'=>$student_name,'begin_time'=>$begin_time,'end_time'=>$end_time,'now'=>$now,'overdue'=>0),1,$curPage,$pagesize);
			$count = $studentsModel->get_myStudentLessonCount(array('teacherCode'=>$userInfo['sCode'],'status'=>$status,'student_name'=>$student_name,'now'=>$now,'overdue'=>0));
			$page = new page($count,$pagesize);
			$showPage = $page->show();
		}else{
			echo '您不是VIP教师,没有相应学员';die;
		}
        $statusList=array(
            '1'=>'正常',
            '2'=>'非正常',
            '3'=>'已结课',
            '4'=>'已退费'
            );
		$this->assign(get_defined_vars());
		$this->display();
	}


	/**
	 * 导出
	 * @return [type] [description]
	 */
	public function export()
	{
		$student_name = isset($_GET['student_name'])?trim($_GET['student_name']):'';
        $status=isset($_GET['status'])?$_GET['status']:'';
		$begin_time=isset($_GET['begin_time'])?$_GET['begin_time']:'';
		$end_time=isset($_GET['end_time'])?$_GET['end_time']:'';
		$userInfo = VipCommAction::get_currentUserInfo();
		if($userInfo['user_key'] == 'Employee-wangyan'){
			$userInfo['sCode'] = 'VP00022';
		}
		$studentsModel = D('VpStudents');

		if($userInfo['sCode']){
			$myStudentList = $studentsModel->getStudentLesson(array('teacherCode'=>$userInfo['sCode'],'status'=>$status,'student_name'=>$student_name,'begin_time'=>$begin_time,'end_time'=>$end_time,'now'=>$now,'overdue'=>0),0);
		}

		import("ORG.Util.PhpExcel");

		$objPHPExcel=new PHPExcel();
        $excel_title= array('学员姓名','高思学号','学员状态','查询时间内累计课时','距离结束日期累计课时','本月累计课时','总累计课时'); 
        //设置表头
        $key = ord("A");
        foreach($excel_title as $v){
            $colum = chr($key);
            $objPHPExcel->getActiveSheet()->getColumnDimension($colum)->setWidth(20);
            $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
            $key += 1;
        }

		$property='';
        $num=2;
        $objActSheet = $objPHPExcel->getActiveSheet();
        $litCount=count($myStudentList);
        if(!empty($myStudentList)){
            for($i=0;$i<=$litCount-1;$i++)
            {
            	if($myStudentList[$i]['nstudentproperty']==1)
            	{
            		$property='正常';
            	}elseif($myStudentList[$i]['nstudentproperty']==2)
            	{
            		$property='非正常';
            	}elseif($myStudentList[$i]['nstudentproperty']==3)
            	{
            		$property='已结课';
            	}else
            	{
            		$property='已退费';
            	}

                $objPHPExcel->getActiveSheet()
                    ->setCellValue('A'.$num, $myStudentList[$i]['sstudentname'])
                    ->setCellValue('B'.$num, $myStudentList[$i]['saliascode'])
                    ->setCellValue('C'.$num, $property)
                    ->setCellValue('D'.$num, $myStudentList[$i]['dhours'])
                    ->setCellValue('E'.$num, $myStudentList[$i]['dendsumhours'])
                    ->setCellValue('F'.$num, $myStudentList[$i]['dmonthsumhours'])
                    ->setCellValue('G'.$num, $myStudentList[$i]['dsumhours']);
                $num++;
            }
        }
        $fileName = iconv("utf-8", "gb2312", '累计课时记录列表'.date('YmdH:i:s').'.xls');

        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        ob_end_clean();
        //将输出重定向到一个客户端web浏览器(Excel5)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
	}
	
	
	
	/*导出中考成绩*/
	public function exportScore(){
		$userInfo = VipCommAction::get_currentUserInfo();
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	
	public function doExportScore(){
		$weixinModel = D('WeixinVip');
		$studentList = $weixinModel->getScoreStudent();
		$subjectList = $weixinModel->getSubjectList($userInfo);
		
		if($studentList){
			foreach ($studentList as $key=>$student){
				foreach ($subjectList as $k=>$subject){
					$studentList[$key][$subject.'score'] = 0;
					$studentList[$key][$subject.'up_score'] = 0;
					$studentList[$key][$subject.'total_score'] = 0;
					//$studentList[$key][$subject.'teacher_name'] = '';
				}
				
			}
		}
		foreach ($studentList as $key=>$student){
			$scoreList = $weixinModel->getScoreByStudent($student['student_code']);
			foreach ($scoreList as $k=>$score){
				if($score['student_code'] = $student['student_code'] ){
					$studentList[$key][$score['subject_name'].'score'] = $score['score'];
					$studentList[$key][$score['subject_name'].'up_score'] = $score['up_score'];
					$studentList[$key][$score['subject_name'].'total_score'] = $score['total_score'];
					//$studentList[$key][$score['subject_name'].'teacher_name'] = $score['teacher_name'];
				}
			}
		}
		
		import("ORG.Util.PhpExcel");

		$objPHPExcel=new PHPExcel();
        $excel_title= array('学员姓名','语文成绩','提分','总分1','教师','数学成绩','提分','总分2','教师','英语成绩','提分','总分3','教师','物理成绩','提分','总分4','教师','化学成绩','提分','总分5','教师'); 
        //设置表头
        $key = ord("A");
        foreach($excel_title as $v){
            $colum = chr($key);
            $objPHPExcel->getActiveSheet()->getColumnDimension($colum)->setWidth(10);
            $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
            $key += 1;
        }

		$property='';
        $num=2;
        $objActSheet = $objPHPExcel->getActiveSheet();
        $litCount=count($studentList);
        if(!empty($studentList)){
            for($i=0;$i<=$litCount-1;$i++){
                $objPHPExcel->getActiveSheet()
                    ->setCellValue('A'.$num, $studentList[$i]['student_name'])
                    ->setCellValue('B'.$num, $studentList[$i]['语文score'])
                    ->setCellValue('C'.$num, $studentList[$i]['语文up_score'])
                    ->setCellValue('D'.$num, $studentList[$i]['语文total_score'])
                    ->setCellValue('E'.$num, $studentList[$i]['语文teacher_name'][0])
                    ->setCellValue('F'.$num, $studentList[$i]['数学score'])
                    ->setCellValue('G'.$num, $studentList[$i]['数学up_score'])
                    ->setCellValue('H'.$num, $studentList[$i]['数学total_score'])
                    ->setCellValue('I'.$num, $studentList[$i]['数学teacher_name'][0])
                    ->setCellValue('J'.$num, $studentList[$i]['英语score'])
                    ->setCellValue('K'.$num, $studentList[$i]['英语up_score'])
                    ->setCellValue('L'.$num, $studentList[$i]['英语total_score'])
                    ->setCellValue('M'.$num, $studentList[$i]['英语teacher_name'][0])
                    ->setCellValue('N'.$num, $studentList[$i]['物理score'])
                    ->setCellValue('O'.$num, $studentList[$i]['物理up_score'])
                    ->setCellValue('P'.$num, $studentList[$i]['物理total_score'])
                    ->setCellValue('Q'.$num, $studentList[$i]['物理teacher_name'][0])
                    ->setCellValue('R'.$num, $studentList[$i]['化学score'])
                    ->setCellValue('S'.$num, $studentList[$i]['化学up_score'])
                    ->setCellValue('T'.$num, $studentList[$i]['化学total_score'])
                    ->setCellValue('U'.$num, $studentList[$i]['化学teacher_name'][0]);
                    
                //获取老师
                $objActSheet->getCell("E".$num,$studentList[$i]['语文teacher_name'][0])->getDataValidation()
	                -> setType(\PHPExcel_Cell_DataValidation::TYPE_LIST)
	                -> setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION)
	                -> setAllowBlank(false)
	                -> setShowInputMessage(true)
	                -> setShowErrorMessage(true)
	                -> setShowDropDown(true)
	                //-> setErrorTitle('输入的值有误')
	                //-> setError('您输入的值不在下拉框列表内.')
	                -> setPromptTitle('语文教师')
	                -> setFormula1('"'.implode(',',$studentList[$i]['语文teacher_name']).'"');    
	            $objActSheet->getCell("I".$num,$studentList[$i]['数学teacher_name'][0])->getDataValidation()
	                -> setType(\PHPExcel_Cell_DataValidation::TYPE_LIST)
	                -> setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION)
	                -> setAllowBlank(false)
	                -> setShowInputMessage(true)
	                -> setShowErrorMessage(true)
	                -> setShowDropDown(true)
	                -> setErrorTitle('输入的值有误')
	                -> setError('您输入的值不在下拉框列表内.')
	                -> setPromptTitle('数学教师')
	                -> setFormula1('"'.implode(',',$studentList[$i]['数学teacher_name']).'"');    
	            $objActSheet->getCell("M".$num,$studentList[$i]['英语teacher_name'][0])->getDataValidation()
	                -> setType(\PHPExcel_Cell_DataValidation::TYPE_LIST)
	                -> setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION)
	                -> setAllowBlank(false)
	                -> setShowInputMessage(true)
	                -> setShowErrorMessage(true)
	                -> setShowDropDown(true)
	                -> setErrorTitle('输入的值有误')
	                -> setError('您输入的值不在下拉框列表内.')
	                -> setPromptTitle('英语教师')
	                -> setFormula1('"'.implode(',',$studentList[$i]['英语teacher_name']).'"');    
	            $objActSheet->getCell("Q".$num,$studentList[$i]['物理teacher_name'][0])->getDataValidation()
	                -> setType(\PHPExcel_Cell_DataValidation::TYPE_LIST)
	                -> setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION)
	                -> setAllowBlank(false)
	                -> setShowInputMessage(true)
	                -> setShowErrorMessage(true)
	                -> setShowDropDown(true)
	                -> setErrorTitle('输入的值有误')
	                -> setError('您输入的值不在下拉框列表内.')
	                -> setPromptTitle('物理教师')
	                -> setFormula1('"'.implode(',',$studentList[$i]['物理teacher_name']).'"');  
	            $objActSheet->getCell("U".$num,$studentList[$i]['化学teacher_name'][0])->getDataValidation()
	                -> setType(\PHPExcel_Cell_DataValidation::TYPE_LIST)
	                -> setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION)
	                -> setAllowBlank(false)
	                -> setShowInputMessage(true)
	                -> setShowErrorMessage(true)
	                -> setShowDropDown(true)
	                -> setErrorTitle('输入的值有误')
	                -> setError('您输入的值不在下拉框列表内.')
	                -> setPromptTitle('化学教师')
	                -> setFormula1('"'.implode(',',$studentList[$i]['化学teacher_name']).'"');    
                $num++;
            }
        }
        $fileName = iconv("utf-8", "gb2312", '中考成绩列表'.date('YmdH:i:s').'.xls');

        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        ob_end_clean();
        //将输出重定向到一个客户端web浏览器(Excel5)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
		
	}
}

?>