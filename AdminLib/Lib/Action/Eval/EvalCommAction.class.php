<?php
/*VIP竞赛测评*/
abstract class EvalCommAction extends AppCommAction{
	protected $autoCheckPerm = false;
	public function __construct() {
		parent::__construct();
	}

	protected  function notNeedLogin() {
		return array();
	}


	public  function uploadImg(){
		if(!empty($_FILES)){
			$t = UPLOAD_PATH.'eval/';
			if(!file_exists($t)){
				mkdir($t);
			}

			$targetFolder = $t.date('Y-m-d').'/';		
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
					echo json_encode(array('status'=>'上传成功','url'=>end(explode('/eap',$targetFile)),'show_url'=>end(explode('Upload/',$targetFile)),'delimg_url'=>U('Eval/EvalPaper/delImg')));
				}else{
					echo json_encode(array('status'=>'上传失败'));
				}
			}else{
				echo json_encode(array('status'=>'不支持的文件类型'));
			}
		}
	}


	/**
	* 上传PDF文档
	**/
	public  function uploadDocument(){
		if(!empty($_FILES)){
			$p = UPLOAD_PATH.'eval/';
			if(!file_exists($p)){
				mkdir($p);
			}
			$pt = $p.'pdf/';
			if(!file_exists($pt)){
				mkdir($pt);
			}
			$targetFolder = $pt.date('Y-m-d').'/';		
			if(!file_exists($targetFolder)){
				mkdir($targetFolder);
			}
			$tempFile = $_FILES['Filedata']['tmp_name'];
			$fileParts = pathinfo($_FILES['Filedata']['name']);
			$fileTypes = array('PDF','pdf');
			$newFilename = time().'.'.strtolower($fileParts['extension']);
			$targetFile =$targetFolder.$newFilename ;
			if(in_array(strtolower($fileParts['extension']),$fileTypes)){
				if(move_uploaded_file($tempFile,$targetFile)){
					//$targetFile = AppCommAction::thumb_img($targetFile,C('IMG_WIDTH'),C('IMG_HEIGHT'));
					echo json_encode(array('status'=>'上传成功','url'=>end(explode('/eap',$targetFile)),'show_url'=>end(explode('Upload/',$targetFile)),'delimg_url'=>U('Eval/EvalPaper/delImg')));
				}else{
					echo json_encode(array('status'=>'上传失败'));
				}
			}else{
				echo json_encode(array('status'=>'不支持的文件类型'));
			}
		}
	}
}
?>
