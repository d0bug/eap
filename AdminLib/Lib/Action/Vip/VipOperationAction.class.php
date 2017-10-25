<?php
/*Vip运营视频库*/
class VipOperationAction extends VipCommAction{
	protected function notNeedLogin() {
		return array('VIP-VIPOPERATION-UPLOAD_FILE','VIP-VIPOPERATION-UPLOADOSS','VIP-VIPOPERATION-UPLOAD_HISTORY_VIDEO');
	}   
    
	//-视频列表
    public function OperationList(){
   	    $videoModel = D('VipOperation');
		import("ORG.Util.Page");
		$pagesize = C('PAGESIZE');
        $videoTypeInfo = $videoModel->get_Opvideo_Type();
        $condition = '';
        $curPage = isset($_GET['p'])?abs($_GET['p']):1;        
        $video_type = isset($_GET['video_type']) ? $_GET['video_type'] :'';
        if(!empty($search)){
            $condition .= " and title like '%".$search."%' ";
        }
        if(!empty($video_type)){
            $seVideo_type = $videoModel->getVideoTypeRow($video_type);
            $condition .= " and video_type = '".$video_type."' ";
        }
        $condition .= " and status =1";
		$vipOperationList = $videoModel->get_OperationList($condition,$curPage,$pagesize);
        foreach($vipOperationList as $key=>$val){
         if(!empty($val['video_type'])){
             $videoTypeList = $videoModel->getVideoTypeRow($val['video_type']);
             $vipOperationList[$key]['video_type_name'] = $videoTypeList['type_name'];
         }
         $vipOperationList[$key]['up_type'] = substr($val['one_video_url'], -3);

        }
		$count = $videoModel->get_OperationCount($condition);
		$page = new Page($count,$pagesize);
		$showPage = $page->show();
		$this->assign(get_defined_vars());
		$this->display();

    }
    //-上传视频
    public function OperationUpload(){
        $user_key = $this->loginUser->getUserKey();        
        #OSS配置
        $region = C('REGION');
        $accessKeyId = C('OSS_ACCESS_ID');
        $accessKeySecret = C('OSS_ACCESS_KEY');
        $bucket = C('BUCKET_YUNYING');
		$videoModel = D('VipOperation');
		$videoTypeInfo = $videoModel->get_Opvideo_Type();
		if($_POST){
            $arr = $_POST;
            $arr['one_video_url'] = 'http://gaosiyunying.oss-cn-beijing.aliyuncs.com/'.$arr['video_url'];
            $userInfo = $this->loginUser->getInformation();
			$arr['create_name'] = $userInfo['real_name'];  
			if($videoModel->add_video($arr,$user_key)){
				$this->success('视频上传成功');
			}else{
				$this->error('视频上传失败');
			}
		}else{
			$attributeOneList = $videoModel->get_attributeList(array('pid'=>0));
			$this->assign(get_defined_vars());
			$this->display();
		}
    }
    
    //修改视频
   	public function updateVideo(){
   	    #OSS配置
        $region = C('REGION');
        $accessKeyId = C('OSS_ACCESS_ID');
        $accessKeySecret = C('OSS_ACCESS_KEY');
        $bucket = C('BUCKET_YUNYING');
		$videoModel = D('VipOperation'); 
		$vid = $_GET['id'];
		$videoModel = D('VipOperation');
		if($_POST){		  
		    $arr = $_POST;
		    $userInfo = $this->loginUser->getInformation();
            if($arr['video_url']){
                $arr['one_video_url'] = 'http://gaosiyunying.oss-cn-beijing.aliyuncs.com/'.$arr['video_url'];    
            }
			$arr['create_name'] = $userInfo['real_name'];
			if($videoModel->update_video($arr)){
				$this->success('视频修改成功');
			}else{
				$this->error('视频修改失败');
			}
		}else{
            $videoTypeInfo = $videoModel->get_Opvideo_Type();
			$videoInfo = $videoModel->get_videoInfo(array('id'=>$vid));            	
			$this->assign(get_defined_vars());
			$this->display();
		}

	}
    
    //删除视频
    public function delOperation(){
        $id = $_POST['id'];
        if(!empty($id)){ 
    		$status = D ( 'VipOperation' )->deleteOperationByID ( $id );
    		if($status){
				$data['status'] = 1;
				$data['msg'] = '删除成功！';
			}else{
				$data['status'] = 0;
				$data['msg'] = '删除失败！';
			}
    
        }else{
				$data['status'] = 2;
				$data['msg'] = '失败！参数导常';
		}
		echo json_encode($data);     
    }
    
    /*uploadify结合oss视频上传*/
	public function upload_file(){
		set_time_limit(0);
		if (!empty($_FILES)){		  
			//set_time_limit(0);
			import('ORG.Util.OssSdk');
			$oss_sdk_service = new ALIOSS();
			//设置是否打开curl调试模式
			$oss_sdk_service->set_debug_mode(TRUE);//FALSE
			$bucket = C('BUCKET');			
			$tempFile = $_FILES['Filedata']['tmp_name'];
			$fileParts = pathinfo($_FILES['Filedata']['name']);			
			$imgTypeArr = array('jpg','jpeg','gif','png');
			if(in_array(strtolower($fileParts['extension']),$imgTypeArr)){
				$fileTypes = $imgTypeArr;
				$isFileType = 1; //图片
			}else{
				$fileTypes = array('flv','flv');
				$isFileType = 2; //视频
			}
			$uniqidname = uniqid(mt_rand(), true);
			if($_POST['is_realname'] == 1){
		//		$newFilename = time().$uniqidname.".".strtolower($fileParts['extension']);
				$newFilename = time().".".strtolower($fileParts['extension']);
			}else{
				$newFilename = $uniqidname.".".strtolower($fileParts['extension']);
			}
			if($isFileType == 2){
				$object = C('OSS_video_PATH').date('Y-m-d').'/'.$newFilename;
			}else if($isFileType == 1){
				$object = C('OSS_IMG_PATH').date('Y-m-d').'/'.$newFilename;
			}
		      
			if (in_array(strtolower($fileParts['extension']),$fileTypes)){
				$content = '';
				$length = 0;
				$fp = fopen($tempFile,'r');
				if($fp){
					$f = fstat($fp);
					$length = $f['size'];
					while(!feof($fp)){
						$content .= fgets($fp);
					}
				}
				$upload_file_options = array('content' => $content, 'length' => $length);
				$upload_file_by_content = $oss_sdk_service->upload_file_by_content($bucket,$object,$upload_file_options);
				if($upload_file_by_content->status == 200){
					if($isFileType == 2){					   
						echo json_encode(array('status'=>'上传成功','url'=>$object,'show_url'=>$object));
					}else if($isFileType == 1){
						echo json_encode(array('status'=>'上传成功','url'=>$object,'show_url'=>"http://".C('DEFAULT_OSS_HOST')."/".C('BUCKET')."/".$object));
					}
				}else{
					echo json_encode(array('status'=>'上传失败'));
				}
			} else {
				echo json_encode(array('status'=>'不支持的文件类型'));
			}
		}
	}


   //生成二维码
   public function erweima(){ 
        import('ORG.Util.Phpqrcode');  
        $arr = $_GET;
        if(!empty($arr)){
            $videoInfo = D('VipOperation')->get_videoInfo($arr);            
            $value = 'http://vip.gaosiedu.com/api/xyapp/videoUrlShow?id='.$videoInfo['id'];//二维码内容            
            $errorCorrectionLevel = "L"; // 纠错级别：L、M、Q、H  
            $matrixPointSize = "4"; // 点的大小：1到10  
            $qrcode = new QRcode();
            ob_clean();
            $qr = $qrcode->png($value, false, $errorCorrectionLevel, $matrixPointSize);
            ImagePng($qr);
            header("Content-Disposition: attachment;filename=".$videoInfo['title'].".png");
            imagedestroy($qr);
        }else{
       	    $this->error('二维码下载失败');
        }
        
   }


    

}
?>