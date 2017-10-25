<?php
/*教师列表*/
class ModularTeacherAction extends ModularCommAction{

	public function main(){
		$this->assign(get_defined_vars());
		$this->display();
	}

	/*上传csv教师信息列表*/
	public function uploadTeacher(){
		if(!empty($_FILES)){
			$html_source = isset($_POST['html_source'])?$_POST['html_source']:'';
			if(!empty($html_source)){
				$fileType = strtolower(end(explode('.',$_FILES['teacherlist']['name'])));
				if($fileType!='xls'){
					$this->error('文件格式错误，请上传GBK编码的xls文件');
				}
				$folder = date('Y-m-d');
				$targetFolder = UPLOAD_PATH.$folder.'/';
				if(!file_exists($targetFolder)){
					mkdir($targetFolder);
				}
				$tempFile = $_FILES['teacherlist']['tmp_name'];
				$newFilename = 'teacherlist.'.$fileType;
				$targetFile =$targetFolder.$newFilename ;//上传后的图片路径
				if(move_uploaded_file($tempFile,$targetFile)){
					$arrTeacherData = $this->get_processTeacherData($targetFile);
					$count = count($arrTeacherData[0]);
					$teacherListHtml = '';
					foreach ($arrTeacherData as $key=>$val){
						//html代码组合
						if($key != 0 && !empty($val)){
							$every_source = $html_source;
							for ($temp =0;$temp<$count;$temp++){
								$val[$temp] = $val[$temp];
								switch ($arrTeacherData[0][$temp]){
									case '姓名':
										$every_source = str_replace('#thumb',$arrTeacherData[$key][$count-2],str_replace('#url',$arrTeacherData[$key][$count-1],str_replace('#name',$val[$temp],$every_source)));
										break;
									case '简介':
										$every_source = str_replace('#desc',$val[$temp],$every_source);
										break;
									case '学科':
										$every_source = str_replace('#subject',$val[$temp],$every_source);
										break;
									case '年级':
										$every_source = str_replace('#grade',$val[$temp],$every_source);
										break;
									case '上课地点':
										$every_source = str_replace('#address',$val[$temp],$every_source);
										break;
								}
							}
							$teacherListHtml .= $every_source;
						}
					}
					$teacherListHtml = stripslashes($teacherListHtml);
					$this->assign(get_defined_vars());
					$this->display();
				}else{
					$this->error('文件上传失败');
				}
			}else{
				$this->error('请提交模块代码结构');
			}

		}else{
			$this->error('教师资料文件不能为空');
		}
	}




	/*导出教师信息列表*/
	public function export_excel(){
		$filename = isset($_GET['filename'])?$_GET['filename']:'';
		$folder = isset($_GET['folder'])?$_GET['folder']:'';
		$targetFile = UPLOAD_PATH.$folder.'/'.$filename;
		if(!empty($targetFile)){
			import("ORG.Util.Excel");
			$exceler = new Excel_Export();
			$exceler->setFileName(time().'.csv');
			$arrTeacherData = $this->get_processTeacherData($targetFile);
			foreach ($arrTeacherData as $key=>$val){
				$count = count($val);
				foreach ($val as $k=>$v){
					$arrTeacherData[$key][$k] = mb_convert_encoding($v,'GBK','UTF-8');
				}
			}
			$excel_title= $arrTeacherData[0];
			$exceler->setTitle($excel_title);
			if(!empty($arrTeacherData)){
				foreach($arrTeacherData as $key=>$val){
					if(!empty($val) && $key!=0){
						$exceler->addRow($val);
					}
				}
			}
			$exceler->export();
		}else{
			$this->error('非法操作');
		}
	}

	/*获取csv教师数据*/
	protected function get_processTeacherData($targetFile){
		$arrTeacherData = array();
		import("ORG.Util.PhpExcel");
		$objReader = new PHPExcel_Reader_Excel5(); //use excel2007
		$objPHPExcel = $objReader->load($targetFile); //指定的文件
		$sheet = $objPHPExcel->getSheet(0);
		$highestRow = $sheet->getHighestRow(); // 取得总行数
		$highestColumn = $sheet->getHighestColumn(); // 取得总列数
		$columnArr = array('A','B','C','D','E','F','G','H','I','J','K');
		$defaultColumnCount = count($columnArr);
		for($j=1;$j<=$highestRow;$j++){
			$code = $objPHPExcel->getActiveSheet()->getCell("A".$j)->getValue();//第一列学号
			//echo $code.'<br>';
			for ($i= 1;$i<=$defaultColumnCount-1;$i++){
				$arrTeacherData[$j-1][] = $objPHPExcel->getActiveSheet()->getCell($columnArr[$i-1].$j)->getValue();
				if($columnArr[$i-1] == $highestColumn){
					break;
				}
			}
		}

		$count = count($arrTeacherData[0]);
		$modelTeacher = D('ModelTeacher');
		$teacherListHtml = '';
		foreach ($arrTeacherData as $key=>$val){
			$teacherOtherInfo = array();
			if(!empty($val[0]) && $key !=0){
				$getTeacherContent = file_get_contents("http://www.gaosiedu.com/api/teacherapi.php?code=".$val[0]);//通过接口获取教师图片地址和链接地址
				$teacherOtherInfo = json_decode((str_replace("var tData = ","",$getTeacherContent)),true);
				if(!empty($teacherOtherInfo)){
					$arrTeacherData[$key][$count-2] .= $teacherOtherInfo[mb_convert_encoding($val[0],'UTF8','GBK')]['thumb'];
					$arrTeacherData[$key][$count-1] .= $teacherOtherInfo[mb_convert_encoding($val[0],'UTF8','GBK')]['url'];
				}
			}
		}

		return $arrTeacherData;
	}
}

?>