<?php
/*VIP早培神测*/
abstract class ViptestCommAction extends AppCommAction{
	protected $autoCheckPerm = false;
	public function __construct() {
		parent::__construct();
	}

	protected  function notNeedLogin() {
		return array();
	}


	public  function uploadImg(){
		if(!empty($_FILES)){
			$targetFolder = UPLOAD_PATH.date('Y-m-d').'/';
			if(!file_exists($targetFolder)){
				mkdir($targetFolder);
			}
			$tempFile = $_FILES['Filedata']['tmp_name'];
			$fileParts = pathinfo($_FILES['Filedata']['name']);
			$fileTypes = array('jpg','jpeg','gif','png');
			$newFilename = time()."_img.".strtolower($fileParts['extension']);
			$targetFile =$targetFolder.$newFilename ;
			if(in_array(strtolower($fileParts['extension']),$fileTypes)){
				if(move_uploaded_file($tempFile,$targetFile)){
					$targetFile = AppCommAction::thumb_img($targetFile,C('IMG_WIDTH'),C('IMG_HEIGHT'));
					echo json_encode(array('status'=>'上传成功','url'=>end(explode('/eap',$targetFile)),'show_url'=>end(explode('Upload/',$targetFile)),'delimg_url'=>U('Viptest/ViptestPaper/delImg')));
				}else{
					echo json_encode(array('status'=>'上传失败'));
				}
			}else{
				echo json_encode(array('status'=>'不支持的文件类型'));
			}
		}
	}


	public function delImg(){
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
	}

}

?>
