<?php
class GoodsMainAction extends GoodsCommAction{
	
	/*礼品列表*/
    public function goodsList(){
		$permValue = $this->permValue;	#角色分配
		
		import("ORG.Util.Page");	//导入分页类
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');  //获取环境变量 每页显示数，也可以在这里自己定义
		$pagesize = 15;
		//礼品列表
		$mgsGiftModel = D('MgsGift');  //MgsGift
		$goodsList = $mgsGiftModel->get_goodsList("", $curPage, $pagesize);
		
		$manager = $mgsGiftModel->get_manager();
		if(!empty($goodsList)){
			import("ORG.Util.String");
			$string = new String();
			/*foreach ($newsList as $key => $new){  //格式化相关 字段的格式
				$newsList[$key]['instime'] = date('Y-m-d H:i:s',$new['instime']);
				$newsList[$key]['title'] = $string->msubstr($new['title'],0,160,'utf-8');
			}*/
		}
		$count = $mgsGiftModel->get_goodsCount("");
		$page = new page($count,$pagesize);
		$showPage = $page->show();
		$this->assign(get_defined_vars());  //此函数的作用是可以便利次方法内所有的变量，不用再使用$this->assign（）
		$this->display();
	}
	
	
	/*礼品添加*/
	public function goodsAdd(){
		$action = $_GET['action'] ? $_GET['action'] : $_POST['action'];
	  	if($action == 'add'){
			if (!empty($_FILES)){  //处理图片上传
				$_POST['giftImage'] = $this->_upload();
        	}
			$formValues = array();
			$formValues['giftName'] 	= $_POST['giftName'];
			$formValues['giftCode'] 	= $_POST['giftCode'];
			$formValues['costValue']	= $_POST['costValue'];
			$formValues['isValid']		= $_POST['isValid'];
			$formValues['giftDetail']	= $_POST['giftDetail'];
			$formValues['giftImage']	= $_POST['giftImage'];
			if(empty($formValues['giftName'])){$this->error('礼品名称不能为空！请重新输入');}
			if(!is_numeric($formValues['costValue'])){$this->error('积分必须为数字!请重新输入');}
			$mgsGiftModel = D('MgsGift');
			if($mgsGiftModel->get_goodsCount("giftCode = '$formValues[giftCode]'")){$this->error('礼品编号已存在，请重新输入');}
			$mgsGiftModel = D('MgsGift'); //实例化这个对象模型
			if($mgsGiftModel->add_goods($formValues)){
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
	  	}else{	//默认状态显示 添加输入框
			$this->display(); 
		}
	}
	/* 礼品 修改 */
	public function goodsEdit(){
		$serial = $_GET['serial'] ? $_GET['serial'] : $_POST['serial'];
		
		if($_GET['action'] == 'info'){ // 在编辑界面调取这个商品的字段
			$goodsModel = D('MgsGift');
			$goodsArr = array();
			$goodsArr = $goodsModel->get_goodsOne($serial); 
			$imgurl = UPLOAD_PATH;  //获取图片的决定路径
			$this->assign(get_defined_vars());
			$this->display(); 
		
		}elseif($_POST['action'] == 'edit' and $serial > 0){
			
			if(!empty($_FILES['giftImage']['name'])){  //如果有新图片上传
				$giftImage = $this->_upload();
			}else{
				$giftImage = $_POST['giftImage_real'];  //如果没有提交新的图片，就用原来的图片	
			}
			
			$formValues = array();
			$formValues['giftName'] 	= $_POST['giftName'];
			$formValues['giftCode'] 	= $_POST['giftCode'];
			$formValues['costValue']	= $_POST['costValue'];
			$formValues['isValid']		= $_POST['isValid'];
			$formValues['giftDetail']	= $_POST['giftDetail'];
			$formValues['giftImage']	= $giftImage;
			if(empty($formValues['giftName'])){$this->error('礼品名称不能为空！请重新输入');}
			if(!is_numeric($formValues['costValue'])){$this->error('积分必须为数字!请重新输入');}
			$mgsGiftModel = D('MgsGift');
			if($mgsGiftModel->edit_goods($formValues, $serial)){
				$this->success('修改成功！');
			}else{
				$this->error('修改失败！');
			}
		}
	}
	
	/* 校区分配 之 礼品入库 修改 */
	public function goodsCount(){
		$permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey('goodsList'));
		$permValue = $permInfo['permValue'];
		
		$serial = $_GET['serial'] ? $_GET['serial'] : $_POST['serial'];
		$sid = $_GET['sid'] ? $_GET['sid'] : $_POST['sid']; 
		
		//通过传递过来的礼品id (serial) 获取礼品相关信息
		$mgsGiftModel = D('MgsGift');
		$goodsArr = array();
		$goodsArr = $mgsGiftModel->get_goodsOne($serial);  //$goodsArr[0]['giftcode']  礼品编号
		$goodsZongruku = $mgsGiftModel->get_goodsZongruku($goodsArr[0]['giftcode']);  //根据这个礼品编号，获取对应的总入库
		
		if($_GET['action'] == 'info'){ // 在编辑界面调取这个商品的字段
			/*  先根据礼品编号查询 MGS_StockPile表，看是否已经录入了，
				如果没有录入就初始化校区分配表 MGS_StockPile。 如果这个商品编号对应的 校区情况不存在，就把BS_Area校区表的校区编号和对应的商品编码批量插入值，
				初始值 各个分配至都为零 */
			$mgsStockPileModel = D('MgsStockPile');
			$goodsFenpei = $mgsStockPileModel->get_goodsFenpei($goodsArr[0]['giftcode']);  //根据这个礼品编号，获取校区分配情况
			if(!$goodsFenpei){  //如果没有值就初始化这个校区分配表，就把BS_Area校区表中的信息添加进去
				$mgsStockPileModel->AddGoodsArea($goodsArr[0]['giftcode']);
			}
			$goodsArea = array();
			$goodsArea = $mgsStockPileModel->get_goodsAreaList($goodsArr[0]['giftcode']);
		
			$this->assign(get_defined_vars());
			$this->display();
			 
		}elseif($_POST['action'] == 'add' and $serial > 0){ //执行 入库操作
			$stockQuantity = $_POST['stockQuantity'];
			if(!is_numeric($stockQuantity) || $stockQuantity == 0 ){$this->error('入库数量必须为数字且不为零!请重新输入');}  //过滤掉为零的数据 ，可以减少操作日志表的压力
			if($stockQuantity < 0){ 
				$abs = abs($stockQuantity); 
				if($abs > $goodsArr[0]['stockquantity']){$this->error('您要减少的数量超过了可用库存数!请重新输入');}
			}
			if($mgsGiftModel->update_stockQuantity($stockQuantity, $goodsArr[0]['giftcode'] )){
				$this->success('添加成功！');
			}else{
				$this->error('添加失败！');
			}
		}elseif($_POST['action'] == 'areaadd' and $sid > 0){ //执行各校区 商品入库;
			$keyong = intval($goodsArr[0]['stockquantity']); //获取可用库存数
			$goodsCount = $_POST['goodsCount'];		
			if(!is_numeric($goodsCount) || $goodsCount == 0){$this->error('入库数量必须为数字且不能为零!请重新输入');}
			
			if($goodsCount > 0){ //当为增加校区数量时，必须查看此礼品的可用库存是否满足 
				if($goodsCount <= $keyong){
					$mgsStockPileModel = D('MgsStockPile');
					if($mgsStockPileModel->update_totalQuantity($goodsCount, $serial, $sid)){
						$this->success('分配成功！');
					}else{
						$this->error('分配失败！');
					}

				}else{
					//$this->error('分配的数量超过了该礼品的可用库存!请重新输入');
					echo "<script language=javascript>alert('分配的数量超过了该礼品的可用库存!请重新输入');history.back(-1);</script>";
				}
			}elseif($goodsCount < 0){ //当为负数时，说明执行追加减少，必须查看此减少的数，是否小于此礼品在此校区的可用库存
				$mgsStockPileModel = D('MgsStockPile');
				$areaArr = $mgsStockPileModel->get_areaOne($sid);
				$abs = abs($goodsCount);
				if($abs <= $areaArr[0]['totalquantity']){
					if($mgsStockPileModel->update_totalQuantity($goodsCount, $serial, $sid)){
						$this->success('分配成功！');
					}else{
						$this->error('分配失败！');
					}		
				}else{
					//$this->error('您要减少的数量超过了总分配量!请重新输入');
					echo "<script language=javascript>alert('您要减少的数量超过了总分配量!请重新输入');history.back(-1);</script>";
				}
			}
		}elseif($_GET['action'] == 'down'){		//下载此礼品的校区分配情况
			$mgsStockPileModel = D('MgsStockPile');
			$goodsArea = array();
			$goodsArea = $mgsStockPileModel->get_goodsAreaList($goodsArr[0]['giftcode']);
			
			$filename = date("YmjHis");  #定义文件名
			header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			header("Content-Disposition: attachment;filename=$filename.xls ");
			header("Content-Transfer-Encoding: binary ");/**/
			//输出表头
			/*echo iconv("utf-8", "gb2312", "校区名称")."\t";
			echo iconv("utf-8", "gb2312", "总分配量")."\t";
			echo iconv("utf-8", "gb2312", "已领取")."\t";
			echo iconv("utf-8", "gb2312", "库存")."\t";
			echo iconv("utf-8", "gb2312", "已预订")."\t";
			echo iconv("utf-8", "gb2312", "实际库存")."\t";
			print "\n";	
			
			for($i=0; $i<count($goodsArea); $i++){
				echo iconv("utf-8", "gb2312",$goodsArea[$i]['sname'])."\t";
				echo iconv("utf-8", "gb2312",$goodsArea[$i]['totalquantity'])."\t";
				echo iconv("utf-8", "gb2312",$goodsArea[$i]['sellquantity'])."\t";
				echo iconv("utf-8", "gb2312",$goodsArea[$i]['totalquantity']-$goodsArea[$i]['sellquantity'])."\t";
				echo iconv("utf-8", "gb2312",$goodsArea[$i]['bookquantity'])."\t";
				echo iconv("utf-8", "gb2312",$goodsArea[$i]['realquantity'])."\t";
				print "\n";	
			}
*/
			//导出 html格式的 ，带有table格式化的数据
			$str  = '<table border="1">'."\n";
  			$str .= '<tr>';
    		$str .= '<th scope="col">校区名称</th>';
			$str .= '<th scope="col">总分配量</th>';
			$str .= '<th scope="col">已领取</th>';
			$str .= '<th scope="col">库存</th>';
			$str .= '<th scope="col">已预订</th>';
			$str .= '<th scope="col">实际库存</th>';
			$str .= '</tr>'."\n";
			$str .= '<tr>';
			$str .= '<th colspan="6" scope="col" bgcolor="#CCCCCC">'.$goodsArr[0]['giftname'].'</th>';
			$str .= '</tr>'."\n";
			for($i=0; $i<count($goodsArea); $i++){
				$str .= '<tr>';
				$str .= '<td>'.$goodsArea[$i]['sname'].'</td>';
				$str .= '<td>'.$goodsArea[$i]['totalquantity'].'</td>';
				$str .= '<td>'.$goodsArea[$i]['sellquantity'].'</td>';
				$str .= '<td>&nbsp;&nbsp;</td>';
				$str .= '<td>'.$goodsArea[$i]['bookquantity'].'</td>';
				$str .= '<td>'.$goodsArea[$i]['realquantity'].'</td>';
				$str .= '</tr>'."\n";;
			}
			$str .= '</table>';
			echo $str;
		}
		
	}
	
	/*礼品 入库 操作 日志列表 */
	public function giftPurHistory(){
		$giftcode = $_GET['giftcode'] ? $_GET['giftcode'] : $_POST['giftcode'];
		$serial = $_GET['serial'] ? $_GET['serial'] : $_POST['serial'];
		$mgsGiftModel = D('MgsGift');
		$goodsArr = array();
		$goodsArr = $mgsGiftModel->get_goodsOne($serial);  //$goodsArr[0]['giftcode']  礼品编号
		 
		import("ORG.Util.Page");	//导入分页类
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');  //获取环境变量 每页显示数，也可以在这里自己定义
		$pagesize = 10;
		//礼品列表
		$mgsStockPileModel = D('MgsStockPile');
		$giftPurList = $mgsStockPileModel->get_giftPurHistory($giftcode, $curPage, $pagesize);
		if(!empty($$giftPurList)){
			import("ORG.Util.String");
			$string = new String();
		}
		$count = $mgsStockPileModel->get_giftPurCount($giftcode);
		$page = new page($count,$pagesize);
		$showPage = $page->show();
		$this->assign(get_defined_vars()); 
		$this->display();
	}
	
	/*礼品 - 校区 - 日志列表*/
    public function goodsAreaList(){
		$sid = $_GET['sid'] ? $_GET['sid'] : $_POST['sid']; 
		import("ORG.Util.Page");	//导入分页类
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');  //获取环境变量 每页显示数，也可以在这里自己定义
		$pagesize = 10;
		//礼品列表
		$mgsStockPileModel = D('MgsStockPile');
		$goodsAreaList = $mgsStockPileModel->get_AreaHistoryList($sid, $curPage, $pagesize);
		if(!empty($goodsAreaList)){
			import("ORG.Util.String");
			$string = new String();
		}
		$count = $mgsStockPileModel->get_areaCount($sid);
		$page = new page($count,$pagesize);
		$showPage = $page->show();
		$this->assign(get_defined_vars()); 
		$this->display();
	}
	
	
	//删除记录
	public function goodsDel(){
		$serial = isset($_GET['serial'])?intval($_GET['serial']):0;	
		$mgsGiftModel = D('MgsGift');
		if($mgsGiftModel->del_goods($serial)){
			$this->success('删除成功！');
		}else{
			$this->error('修改失败！');
		}
	}
	
	//修改库存数量
	public function show_goodsCount(){
		$serial = isset($_GET['serial'])?intval($_GET['serial']):0;
		echo $serial;
			
	}
	//弹出窗口展示的内容  //修改库存数量  --有时间整合
	/*public function show_goodsCount(){
		$serial = isset($_GET['serial'])?intval($_GET['serial']):0;
		$hid = isset($_GET['hide'])?intval($_GET['hide']):0;
		echo $hid;
	
		//$this->display();
			
	}*/
	/*public function show_detail_handouts(){
		$hid = isset($_GET['hid'])?intval($_GET['hid']):0;
		$return_str = '';
		if(!empty($hid)){
			$vipHandoutsModel = D('VpHandouts');
			$handoutsInfo = $vipHandoutsModel->get_handoutsInfo_by_hid($hid);
			$vipSubjectModel = D('VpSubject');
			$handoutsInfo['subject_name'] = $vipSubjectModel->get_subjectname_by_sid($handoutsInfo['sid']);
			$vipGradeModel = D('VpGrade');
			$handoutsInfo['grades_name'] = $vipGradeModel->get_gradenames_by_gids($handoutsInfo['gid']);
			$vipKnowledgeModel = D('VpKnowledge');
			$handoutsInfo['knowledge_name'] = $vipKnowledgeModel->get_knowledgename_by_kid($handoutsInfo['kid']);
			$HANDOUTS_TYPE = C('HANDOUTS_TYPE');
			$return_str .= '<div class="mwTitle">'.$handoutsInfo['title'].'</div><div class="mwContent"><table width="100%" border="0" cellpadding="0" cellspacing="0" class="mwTable"><tr valign="top"><td class="alt">讲义类型:</td><td>'.$HANDOUTS_TYPE[$handoutsInfo['type']].'</td></tr><tr valign="top"><td class="alt">学&nbsp;&nbsp;&nbsp;&nbsp;科:</td><td>'.$handoutsInfo['subject_name'].'</td></tr><tr valign="top"><td class="alt">年&nbsp;&nbsp;&nbsp;&nbsp;级:</td><td>'.$handoutsInfo['grades_name'].'</td></tr><tr valign="top"><td class="alt">知&nbsp;识&nbsp;点:</td><td>'.$handoutsInfo['knowledge_name'].'</td></tr><tr valign="top"><td class="alt">讲义介绍:</td><td>'.$handoutsInfo['introduce'].'</td></tr><tr valign="top"><td class="alt"></td><td>';
			if($handoutsInfo['user_key'] == $this->loginUser->getUserKey()){
				$return_str .= '<a href="'.U('Vip/VipHandouts/add_handouts',array('hid'=>$hid)).'"><input type=button value="修改" class="btn"></a>';
			}
			$return_str .= '</td></tr></table></div>';
		}else{
			$return_str .= '非法操作，无法获取讲义信息';
		}
		echo $return_str;
	}*/
	
	
	//图片上传类
	protected function _upload() {
		import('ORG.Net.UploadFile');; //导入上传类
        $upload = new UploadFile(); 
		$upload->maxSize            = 3292200;//设置上传文件大小
        $upload->allowExts          = explode(',', 'jpg,gif,png,jpeg');//设置上传文件类型
		$imgurl = date('Y-m-d',time());
		//$upload->savePath           = UPLOAD_PATH.'/'.$imgurl.'/';//设置附件上传目录   上传图片目录按时间戳设置子目录
		$upload->savePath           = UPLOAD_PATH;//设置附件上传目录   上传图片目录按时间戳设置子目录
		//设置需要生成缩略图，仅对图像文件有效
        $upload->thumb              = true;
        // 设置引用图片类库包路径
        $upload->imageClassPath     = 'ORG.Util.Image';
        //设置需要生成缩略图的文件后缀
        $upload->thumbPrefix        = 'm_,s_';  //生产2张缩略图
        //设置缩略图最大宽度
        $upload->thumbMaxWidth      = '400,100';
        //设置缩略图最大高度
        $upload->thumbMaxHeight     = '400,100';
        //设置上传文件规则
        $upload->saveRule           = 'uniqid';
        //删除原图
        $upload->thumbRemoveOrigin  = true;
        if (!$upload->upload()) {
        //捕获上传异常
           	 $this->error($upload->getErrorMsg());
        } else {
            			//取得成功上传的文件信息
            $uploadList = $upload->getUploadFileInfo();
            import('ORG.Util.Image');
            //给m_缩略图添加水印, Image::water('原文件名','水印图片地址')
            Image::water($uploadList[0]['savepath'] . 'm_' . $uploadList[0]['savename'], APP_PATH.'Tpl/Public/Images/logo.png');
            		//$_POST['giftImage'] = $uploadList[0]['savename'];
					$upfileurl = $uploadList[0]['savename'];
        }
		return $upfileurl;		
	}
	
	#下载所有 商品 对应的校区分配情况
	public function downAll(){
			//礼品列表
		$mgsGiftModel = D('MgsGift');  //MgsGift
		$goodsListAll = $mgsGiftModel->get_goodsListAll();
		$mgsStockPileModel = D('MgsStockPile');
		$goodsArea = array();
		
		$filename = date("YmjHis").'_all';  #定义文件名
		header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Disposition: attachment;filename=$filename.xls ");
		header("Content-Transfer-Encoding: binary ");/**/
		
		//输出整个表头
		$str  = '<table border="1">'."\n";
  			$str .= '<tr>';
    		$str .= '<th scope="col">校区名称</th>';
			$str .= '<th scope="col">总分配量</th>';
			$str .= '<th scope="col">已领取</th>';
			$str .= '<th scope="col">库存</th>';
			$str .= '<th scope="col">已预订</th>';
			$str .= '<th scope="col">实际库存</th>';
			$str .= '</tr>'."\n";
		
		for($i = 0; $i<count($goodsListAll); $i++ ){
			$str .= '<tr>';
			$str .= '<th colspan="6" scope="col" bgcolor="#CCCCCC">'.$goodsListAll[$i]['giftname'].'</th>';
			$str .= '</tr>'."\n";
			/*--内部循环 start--*/
			$goodsArea = $mgsStockPileModel->get_goodsAreaList($goodsListAll[$i]['giftcode']);
			if(!empty($goodsArea)){	
				for($n = 0; $n<count($goodsArea); $n++){
					$str .= '<tr>';
					$str .= '<td>'.$goodsArea[$n]['sname'].'</td>';
					$str .= '<td>'.$goodsArea[$n]['totalquantity'].'</td>';
					$str .= '<td>'.$goodsArea[$n]['sellquantity'].'</td>';
					$str .= '<td>&nbsp;&nbsp;</td>';
					$str .= '<td>'.$goodsArea[$n]['bookquantity'].'</td>';
					$str .= '<td>'.$goodsArea[$n]['realquantity'].'</td>';
					$str .= '</tr>'."\n";
				}
			}else{
				$str .= '<tr>';
				$str .= '<td colspan="6" height="50">未分配</td>';
				$str .= '</tr>'."\n";		
			}
			/*--内部循环 end--*/
		}
		$str .= '</table>';
		echo $str;		
	}

    
}

?>