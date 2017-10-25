<?php
/*Vip入职视频库*/
class VipVideoAction extends VipCommAction{
	protected function notNeedLogin() {
		return array('VIP-VIPVIDEO-UPLOAD_FILE','VIP-VIPVIDEO-UPLOADOSS','VIP-VIPVIDEO-UPLOAD_HISTORY_VIDEO');
	}
	/*视频列表*/
	public function videoList(){
		$user_key = $this->loginUser->getUserKey();
		$list_style = isset($_GET['style'])?trim($_GET['style']):C('DEFAULT_LISTSTYLE');
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$condition = ' AND v.status =1 ';
		if(!empty($_REQUEST['attribute_one'])){
			$condition .= " AND v.attribute_one = '$_REQUEST[attribute_one]' ";
		}
		if(!empty($_REQUEST['attribute_two'])){
			$condition .= " AND v.attribute_two = '$_REQUEST[attribute_two]' ";
		}
		if(!empty($_REQUEST['keyword'])){
			$condition .= " AND v.title like '%".SysUtil::safeSearch(urldecode($_REQUEST['keyword']))."%'";
		}
		$videoModel = D('VpVideo');
		$videoList = $videoModel->get_videoList($user_key,$condition,$curPage,$pagesize,' ORDER BY instime DESC,vid DESC');
		$count = $videoModel->get_videoCount($condition);
		$page = new Page($count,$pagesize);
		$showPage = $page->show();

		$attribute_one = $_REQUEST['attribute_one'];
		$attribute_two = $_REQUEST['attribute_two'];
		$keyword = $_REQUEST['keyword'];
		$attributeOneList = $videoModel->get_attributeList(array('pid'=>0));
		$attributeTwoList = !empty($attribute_one)?$videoModel->get_attributeList(array('pid'=>$attribute_one)):array();

		$this->assign(get_defined_vars());
		$this->display('videoList');
	}


	public function playVideo(){
		$videoInfo = D('VpVideo')->get_videoInfo(array('vid'=>$_GET['vid']));
		$this->assign(get_defined_vars());
		$this->display('playVideo');
	}


	public function doFavorite(){
		if(!empty($_GET['vid'])){
			$videoModel = D('VpVideo');
			$operate = ($_GET['act']=='del')?'取消收藏':'收藏';
			if($videoModel->do_favorite(array('act'=>$_GET['act'],'vid'=>$_GET['vid'],'user_key'=>$this->loginUser->getUserKey()))){
				$this->success('视频'.$operate.'成功');
			}else{
				$this->error('视频'.$operate.'失败');
			}
		}else{
			$this->error('非法操作');
		}
	}


	/*我的收藏*/
	public function myFavorites(){
		$user_key = $this->loginUser->getUserKey();
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$condition = " AND f.user_key = '$user_key' ";
		if(!empty($_REQUEST['attribute_one'])){
			$condition .= " AND v.attribute_one = '$_REQUEST[attribute_one]' ";
		}
		if(!empty($_REQUEST['attribute_two'])){
			$condition .= " AND v.attribute_two = '$_REQUEST[attribute_two]' ";
		}
		if(!empty($_REQUEST['keyword'])){
			$condition .= " AND v.title like '%".SysUtil::safeSearch(urldecode($_REQUEST['keyword']))."%'";
		}
		$videoModel = D('VpVideo');
		$favoriteList = $videoModel->get_favoriteList($condition,$curPage,$pagesize,' ORDER BY instime DESC');
		$count = $videoModel->get_favoriteCount($condition);
		$page = new Page($count,$pagesize);
		$showPage = $page->show();

		$attribute_one = $_REQUEST['attribute_one'];
		$attribute_two = $_REQUEST['attribute_two'];
		$keyword = $_REQUEST['keyword'];
		$attributeOneList = $videoModel->get_attributeList(array('pid'=>0));
		$attributeTwoList = !empty($attribute_one)?$videoModel->get_attributeList(array('pid'=>$attribute_one)):array();

		$this->assign(get_defined_vars());
		$this->display();
	}


	/*视频管理*/
	public function videoManage(){
		$permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey());
		$user_key = $this->loginUser->getUserKey();
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = C('PAGESIZE');
		$condition = '';
		if(!empty($_REQUEST['attribute_one'])){
			$condition .= " AND v.attribute_one = '$_REQUEST[attribute_one]' ";
		}
		if(!empty($_REQUEST['attribute_two'])){
			$condition .= " AND v.attribute_two = '$_REQUEST[attribute_two]' ";
		}
		if(!empty($_REQUEST['keyword'])){
			$condition .= " AND v.title like '%".SysUtil::safeSearch(urldecode($_REQUEST['keyword']))."%'";
		}
		$videoModel = D('VpVideo');
		$videoList = $videoModel->get_videoList($user_key,$condition,$curPage,$pagesize,' ORDER BY status ASC,instime DESC');
		$count = $videoModel->get_videoCount($condition);
		$page = new Page($count,$pagesize);
		$showPage = $page->show();


		$attribute_one = $_REQUEST['attribute_one'];
		$attribute_two = $_REQUEST['attribute_two'];
		$keyword = $_REQUEST['keyword'];
		$attributeOneList = $videoModel->get_attributeList(array('pid'=>0));
		$attributeTwoList = !empty($attribute_one)?$videoModel->get_attributeList(array('pid'=>$attribute_one)):array();

		$this->assign(get_defined_vars());
		$this->display('videoManage');
	}


	/*上传视频*/
	public  function videoUpload(){
		$user_key = $this->loginUser->getUserKey();
		$videoModel = D('VpVideo');
		if($_POST){
			if($videoModel->add_video($_POST,$user_key)){
				$this->success('视频上传成功');
			}else{
				$this->error('视频上传失败');
			}
		}else{
			$attributeOneList = $videoModel->get_attributeList(array('pid'=>0));
			$this->assign(get_defined_vars());
			$this->display('videoUpload');
		}
	}


	public function updateVideo(){
		$vid = $_GET['vid'];
		if(empty($vid)){
			$this->error('非法操作');
		}
		$videoModel = D('VpVideo');
		if($_POST){
			if($videoModel->update_video($_POST,$vid)){
				$this->success('视频修改成功');
			}else{
				$this->error('视频修改失败');
			}
		}else{
			$videoInfo = $videoModel->get_videoInfo(array('vid'=>$vid));
			$attributeOneList = $videoModel->get_attributeList(array('pid'=>0));
			$attributeTwoList = $videoModel->get_attributeList(array('pid'=>$videoInfo['attribute_one']));
			$this->assign(get_defined_vars());
			$this->display('updateVideo');
		}

	}


	public function reviewVideo(){
		$permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey());
		$status = 0;
		if($permInfo['permValue'] == 3){
			$vid = $_GET['vid'];
			$videoModel = D('VpVideo');
			if($videoModel->review_video($_GET['status'],$vid)){
				$status = 1;
			}
		}
		echo $status;
	}


	public function deleteVideo(){
		$permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey());
		if(empty($_GET['vid']) || $permInfo['permValue']!=3){
			$this->error('非法操作');
		}
		if(D('VpVideo')->delete_video($_GET['vid'])){
			$this->success('视频删除成功');
		}else{
			$this->error('视频删除失败');
		}
	}


	public function getAttributeList(){
		$html = '';
		if($_GET['pid']!==''){
			$attributeList = D('VpVideo')->get_attributeList(array('pid'=>$_GET['pid']));
			if(!empty($attributeList)){
				foreach ($attributeList as $key=>$attribute){
					$html .= '<option value="'.$attribute['aid'].'">'.$attribute['name'].'</option>';
				}
			}
		}else{
			$html .= '<option value="">请选择视频类别</option>';
		}

		echo $html;
	}

	/*视频属性管理*/
	public function attributeManage(){
		$permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey());
		$videoModel = D('VpVideo');
		$attributeOneArr = $videoModel->get_attributeList(array('pid'=>0));
		$this->assign(get_defined_vars());
		$this->display('attributeManage');
	}


	public function getAttributeTwo(){
		$attributeTwoHtml = '';
		$attributeOne = abs($_GET['attribute_one']);
		if(!empty($attributeOne)){
			$attributeTwoArr = D('VpVideo')->get_attributeList(array('pid'=>$attributeOne));
			if(!empty($attributeTwoArr)){
				foreach ($attributeTwoArr as $key=>$attributeTwo){
					$attributeTwoHtml .= '<input type="radio" name="attribute_two" id="attribute_two'.$attributeTwo['aid'].'" value="'.$attributeTwo['aid'].'" title="'.$attributeTwo['name'].'" >'.$attributeTwo['name'].'&nbsp;&nbsp;&nbsp;&nbsp;';
				}
			}
		}
		echo $attributeTwoHtml;
	}


	public function get_add_form(){
		$type = isset($_GET['type'])?SysUtil::safeString($_GET['type']):'';
		$form_str = '';
		if(!empty($type)){
			$form_str .= '<div class="add_item" style="padding: 30px 40px;"><form id="add_item_form" name="add_item_form" method="POST" action="'.U('Vip/VipVideo/addAttribute',array('type'=>$type)).'" onsubmit="return check_add_item(\''.$type.'\')">';
			switch ($type){
				case 'attribute_one':
					$form_str .= '<div><font color=red>*</font>视频属性名称：<input type="text" name="name" id="name" value="" size="30"><label id=name_msg class=error></label></div><br>';
					break;
				case 'attribute_two':
					$attributeOne = isset($_GET['attribute_one'])?abs($_GET['attribute_one']):'';
					$videoModel = D('VpVideo');
					$attributeOneList = $videoModel->get_attributeList(array('pid'=>0));
					if(!empty($attributeOneList)){
						$form_str .= '<div><font color=red>*</font>视频属性：<select id="parent_id" name="parent_id"><option value="">请选择视频属性</option>';
						foreach ($attributeOneList as $key=>$attribute){
							$form_str .= '<option value="'.$attribute['aid'].'" ';
							$form_str .= ($attribute['aid'] == $attributeOne)?' selected="true" ':'';
							$form_str .= '>'.$attribute['name'].'</option>';
						}
						$form_str .= '</select><label id=attribute_one_msg class=error></label></div><br>';
					}
					$form_str .= '<div><font color=red>*</font>视频类别名称：<input type="text" name="name" id="name" value="" size="30"><label id=name_msg class=error></label></div><br>';
					break;
			}
			$form_str .= '<input type="submit" name="submit" value="确认添加" class="btn"></form></div>';
		}
		echo $form_str;
	}


	public function addAttribute(){
		$type = isset($_GET['type'])?SysUtil::safeString($_GET['type']):'';
		$title = ($type == 'attribute_one')?'视频属性':'视频类别';
		if(D('VpVideo')->add_attribute($_POST, $type)){
			$this->success($title.'添加成功',U('Vip/VipVideo/attributeManage'));
		}else{
			$this->error($title.'添加失败');
		}
	}


	/*删除视频属性/视频类别*/
	protected function deleteAttribute(){
		$return = array('status'=>0);
		if(!empty($_POST['type'])){
			$videoModel = D('VpVideo');
			switch ($_POST['type']){
				case 'attribute_one':
					$attributeId = $_POST['attribute_one'];
					$msg_type = '视频属性';
					break;
				case 'attribute_two':
					$attributeId = $_POST['attribute_two'];
					$msg_type = '视频类别';
					break;
			}
			if($videoModel->delete_attribute($_POST['type'],$attributeId)){
				$return['status'] = 1;
				$return['msg'] = $msg_type.'删除成功';
			}else{
				$return['msg'] = $msg_type.'删除失败';
			}
		}else{
			$return['msg'] = '您没有选中任何选项，无法进行删除操作';
		}
		echo json_encode($return);
	}


	public function editAttribute(){
		$videoModel = D('VpVideo');
		switch($_POST['type']){
			case 'attribute_one':
				$typeName = '视频属性';
				break;
			case 'attribute_two':
				$typeName = '视频类别';
				break;
		}
		if($videoModel->edit_attribute($_POST)){
			$this->success($typeName.'名称修改成功', U('Vip/VipVideo/attributeManage'));
		}else{
			$this->error($typeName.'名称修改失败');
		}
	}


	/*uploadify视频上传*/
	public function upload_file_back(){
		set_time_limit(0);
		if (!empty($_FILES)) {
			$targetFolder = UPLOAD_PATH.date('Y-m-d').'/';
			if(!file_exists($targetFolder)){
				mkdir($targetFolder,0777);
			}
			$tempFile = $_FILES['Filedata']['tmp_name'];
			$fileParts = pathinfo($_FILES['Filedata']['name']);
			$imgTypeArr = array('jpg','jpeg','gif','png');
			if(in_array(strtolower($fileParts['extension']),$imgTypeArr)){
				$fileTypes = $imgTypeArr;
			}else{
				$fileTypes = array('flv','flv');
			}
			//echo json_encode(array('status'=>$_FILES['Filedata']['name']));die;
			$uniqidname = uniqid(mt_rand(), true);
			if($_POST['is_realname'] == 1){
				//	$newFilename = pathinfo($_FILES['Filedata']['name'], PATHINFO_FILENAME).'_'.$uniqidname.".".strtolower($fileParts['extension']);
				$newFilename = time().$uniqidname.".".strtolower($fileParts['extension']);
			}else{
				$newFilename = $uniqidname.".".strtolower($fileParts['extension']);
			}

			$targetFile =$targetFolder.$newFilename ;
			if (in_array(strtolower($fileParts['extension']),$fileTypes)) {
				if(move_uploaded_file($tempFile,$targetFile)){
					if(in_array(strtolower($fileParts['extension']),$imgTypeArr)){
						$autocut = isset($_POST['autocut'])?$_POST['autocut']:1;
						$thumb_file = AppCommAction::thumb_img($targetFile,$_POST['width'],$_POST['height'],$autocut);
						echo json_encode(array('status'=>'上传成功','url'=>end(explode('/eap',$thumb_file)),'show_url'=>end(explode('Upload/',$thumb_file))));
					}else{
						echo json_encode(array('status'=>'上传成功','url'=>end(explode('/eap',$targetFile)),'show_url'=>end(explode('/eap',str_replace('Upload/','upload/',$targetFile)))));
					}
				}else{
					echo json_encode(array('status'=>'上传失败'));
				}
			} else {
				echo json_encode(array('status'=>'不支持的文件类型'));
			}
		}
	}
	/*uploadify结合oss视频上传*/
	public function upload_file(){
		set_time_limit(0);
		if (!empty($_FILES)) {
			//set_time_limit(0);
			import('ORG.Util.OssSdk');
			$oss_sdk_service = new ALIOSS();
			//设置是否打开curl调试模式
			$oss_sdk_service->set_debug_mode(FALSE);
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

	/*删除oss的object*/
	protected function del_object(){
		if(empty($_POST['url'])){
			echo 0;
			exit;
		}
		$bucket = C('BUCKET');
		$object = $_POST['url'];
		$videoModel = D('VpVideo');
		if($videoModel->delete_oss_object($bucket,$object)){
			echo '1';
		}else{
			echo '0';
		}
	}
	/*删除视频*/
	public function del_file(){
		if(!empty($_POST['url'])){
			@unlink(APP_DIR.$_POST['url']);
			if(!file_exists(APP_DIR.$_POST['url'])){
				echo '1';
			}else{
				echo '0';
			}
		}else{
			echo '0';
		}
		exit;
	}
	
	//历史数据上传到oss
	public function upload_history_video(){
		$videoModel = D('VpVideo');
		$videoModel->upload_history_video();
	}

}
?>