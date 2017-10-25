<?php
class GoodsStockPileAction extends GoodsCommAction{
	
	// 把商品各校区分别插入 校区表中
	public function goodsToArea($giftCode){
		//echo $giftCode;
		$mgsStockPileModel = D('MgsStockPile');
		$xiaoquList = $mgsStockPileModel->AddGoodsArea();
		print_r($xiaoquList);
		$i = 0;
		
		for($i=0; $i<count($xiaoquList); $i++){
			echo $xiaoquList[$i]['scode'] .'--'. $xiaoquList[$i]['sname'] .'<br>';
		}
	
		//print_r($xiaoquList);

	}
	
	/*礼品添加*/
	public function goodsAdd(){
		
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
			//if($mgsGiftModel->get_goodsCount("giftCode = '$formValues[giftCode]'")){$this->error('礼品编号已存在，请重新输入');}
			if($mgsGiftModel->edit_goods($formValues, $serial)){
				$this->success('修改成功！');
			}else{
				$this->error('修改失败！');
			}
		}
	}
	
	/* 校区分配 之 礼品入库 修改 */
	public function goodsCount(){
		$serial = $_GET['serial'] ? $_GET['serial'] : $_POST['serial'];
		$mgsGiftModel = D('MgsGift');
		$goodsArr = array();
		$goodsArr = $mgsGiftModel->get_goodsOne($serial);  //$goodsArr[0]['giftcode']  礼品编号
		$goodsZongruku = $mgsGiftModel->get_goodsZongruku($goodsArr[0]['giftcode']);  //根据这个礼品编号，获取对应的总入库
		
		//获取校区分配表中是否存在此商品（也就是一个商品对应的23个校区是否存在）
		$mgsStockPileModel = D('MgsStockPile');
		$goodsFenpei = $mgsStockPileModel->get_goodsFenpei($goodsArr[0]['giftcode']);  //根据这个礼品编号，获取校区分配情况
		
		
		
		
		if($_GET['action'] == 'info'){ // 在编辑界面调取这个商品的字段
			/*  要根据这个礼品的编号 来查询 MGS_GiftPurHistory  来求和  求总数  总库存数，*/
			
			/*  要根据这个 商量来求 校区分配表 MGS_StockPile 里 是否有这个商品 的分配情况，如果有，就执行更改，如果没有就执行插入（循环插入16个校区） */
			$this->assign(get_defined_vars());
			$this->display();
			 
		}elseif($_POST['action'] == 'add' and $serial > 0){ //执行 入库操作
			$stockQuantity = $_POST['stockQuantity'];
			if(!is_numeric($stockQuantity)){$this->error('入库数量必须为数字!请重新输入');}
			if($stockQuantity < 0){ 
				$abs = abs($stockQuantity); 
				if($abs > $goodsArr[0]['stockquantity']){$this->error('您要减少的数量超过了可用库存数!请重新输入');}
			}
			if($mgsGiftModel->update_stockQuantity($stockQuantity, $goodsArr[0]['giftcode'] )){
				$this->success('添加成功！');
			}else{
				$this->error('修改失败！');
			}	
		}
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
	
    
}

?>