<?php
class GoodsIntegralAction extends GoodsCommAction{
	
	/*网课 学员情况 */
    public function wangkeList(){
		$action = $_POST['action'] ? $_POST['action'] : $_GET['action'];
		$saliascode = $_POST['saliascode'] ? $_POST['saliascode'] : $_GET['saliascode'];
		$act = $_POST['act'] ? $_POST['act'] : $_GET['act'];
		
		if($action == 'search'){
			$IntegralModel = D('MgsIntegral');
			$studentInfo = $IntegralModel->get_studentInfo($saliascode);
			if($studentInfo){
				$tags = 1;	//如果存在此记录  $tags标记为一
				$stuArr = array();  //获取学员信息
				$stuArr['scode']	= 	$studentInfo['0']['scode'];
				$stuArr['sname']	= 	$studentInfo['0']['sname'];
				$stuArr['saliascode']	= 	$studentInfo['0']['saliascode'];
				$stuArr['smobile']	= 	$studentInfo['0']['smobile'];
				
				$netclassArr =array();  //获取此学员的三类积分总和情况
				$netclassArr = $IntegralModel->get_netClass($stuArr['scode']); 
				$integralArr['cardintegral'] = $netclassArr[0]['cardintegral']; 
				$integralArr['lessonintegral'] = $netclassArr[0]['lessonintegral']; 
				$integralArr['netclassintegral'] = $netclassArr[0]['netclassintegral']; 
				
				//获取此会员的各种积分操作记录情况 
				import("ORG.Util.Page");	//导入分页类
				$curPage = isset($_GET['p'])?abs($_GET['p']):1;
				$pagesize = C('PAGESIZE');  //获取环境变量 每页显示数，也可以在这里自己定义
				$pagesize = 8;
				//记录列表
				$IntegralModel = D('MgsIntegral');
				$integralList = $IntegralModel->get_integralList($stuArr['scode'], $curPage, $pagesize); 
				if(!empty($integralList)){
					import("ORG.Util.String");
					$string = new String();
				}
				$count = $IntegralModel->get_integralCount($stuArr['scode']);
				$page = new page($count,$pagesize);
				$showPage = $page->show();/**/
				/*--------列表显示 end -------*/
				
			}else{
				$tags = 0;	//如果不存在此记录  $tags标记为0
			}
		}
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	// 添加 网课积分
	public function integralAdd(){
		$saliascode = $_POST['saliascode'] ? $_POST['saliascode'] : $_GET['saliascode'];
		$IntegralModel = D('MgsIntegral');
		$studentInfo = $IntegralModel->get_studentInfo($saliascode);	
		$stuArr = array();
		$stuArr['scode']	= 	$studentInfo['0']['scode'];
		$stuArr['sname']	= 	$studentInfo['0']['sname'];
		$stuArr['saliascode']	= 	$studentInfo['0']['saliascode'];
		$stuArr['smobile']	= 	$studentInfo['0']['smobile'];		
		// 执行充值操作
		$wangkejifen = $_POST['wangkejifen'] ? $_POST['wangkejifen'] : $_GET['wangkejifen'];
		if(!is_numeric($wangkejifen) || $wangkejifen == 0 ){$this->error('入库数量必须为数字且不为零!请重新输入');}
		$IntegralModel = D('MgsIntegral');
		if($wangkejifen > 0){
			if($IntegralModel->add_Integral($stuArr, $wangkejifen)){
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
		}elseif($wangkejifen < 0){
			$netclassArr = $IntegralModel->get_netClass($stuArr['scode']);  //查询这个学员的总共网课积分
			$abs = abs($wangkejifen);
			
			if($abs <= $netclassArr[0]['netclassintegral'] && $abs <= $netclassArr[0]['totalintegral'] ){    #
				if($IntegralModel->add_Integral($stuArr, $wangkejifen)){
					$this->success('操作成功');
				}else{
					$this->error('操作失败');
				}
			}else{
				//$this->error('您要减少的数量超过了总分配量!请重新输入');
				echo "<script language=javascript>alert('您要减少的数量超过了总量!请重新输入');history.back(-1);</script>";
			}
		}
	}
	
	
	
	
	#增加 --- 2013 12 03;
	#网课赠分批量导入
	public function execlAction(){
		#echo '网课赠分批量导入';	
		$this->display();
	}
	public function excelUpall(){
		import("ORG.Net.UploadFile");
		$upload = new UploadFile(); // 实例化上传类 
		$upload->maxSize  = 3145728 ; // 讴置附件上传大小 
		$upload->allowExts = array('xls','csv'); // 讴置附件上传类型 
		$folder = 'wkzf_'.date('Y-m-d');
		if(!file_exists(UPLOAD_PATH));
			mkdir(UPLOAD_PATH,0777);
		$upload->savePath = UPLOAD_PATH.$folder.'/';	#设置上传路径
		if(!file_exists($upload->savePath)){
			@mkdir($upload->savePath,0777);
		}     
		if(!$upload->upload()) {// 上传错诣 提示错诣信息 
			$this->error($upload->getErrorMsg()); 
		}else{ // 上传成功 获叏上传文件信息 
			$infos =  $upload->getUploadFileInfo(); 
		}
		$excelFile = $infos[0]['savepath'].$infos[0]['savename'];	#获取完整的文件名
		#$excelFile = "/data/wwwroot/eap/Upload/wkzf_2013-12-04/529eea1fdb874.xls";
		#从EXCEL中读取出来的数据
		
		$moduleForm = D('MgsIntegral');
		$excelArr    =    $moduleForm->readExcel($excelFile);	
		$excelArr = array_slice($excelArr,1); #从第二行开始取，把第一行表头去掉。
		
		
		$IntegralModel = D('MgsIntegral');
		$tag_code = true;
		$tag_jifen = true;
		
		#遍历序号是否全部存在
		foreach($excelArr as $arr){
			$sCode = $arr[0];  #学生编号
			$studentInfo = $IntegralModel->get_studentInfo_s($sCode);	
			if($studentInfo ){
				$tag_code = true;
			}else{
				$tag_code = false;
				break;
			}
		}
		#循环遍历积分是否全部合法。
		foreach($excelArr as $arr){
			$wangkejifen = intval($arr[1]);
			if($wangkejifen > 0){
				$tag_jifen = true;
			}else{
				$tag_jifen = false;
				break;
			}
		}
		
		
		#只有当学号与积分同时正确时才执行
		if($tag_code and $tag_jifen){
			foreach($excelArr as $arr){
				$sCode = $arr[0];  #学生编号
				$wangkejifen = intval($arr[1]); #积分  这里只处理积分大于零的， 也就是只处理积分增
				
				$studentInfo = $IntegralModel->get_studentInfo_s($sCode);
				$stuArr = array();
				$stuArr['scode']	= 	$studentInfo['0']['scode'];
				$stuArr['sname']	= 	$studentInfo['0']['sname'];
				$stuArr['saliascode']	= 	$studentInfo['0']['saliascode'];
				$stuArr['smobile']	= 	$studentInfo['0']['smobile'];
				$IntegralModel->add_Integral($stuArr, $wangkejifen);
			}
			$this->success('添加成功');	
				
		}else{
			$this->error('<h3>有错误！</h3>错误原因：<br>1、学号是否全部正确！<br>2、积分必须大于零');	
		}

	}
	

}
?>
