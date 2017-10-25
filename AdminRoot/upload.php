<?php
	
	$ip = $_SERVER['REMOTE_ADDR'];
	$ipAllow = array('211.157.101.208','59.108.61.54');
	if( !in_array($ip, $ipAllow) ){
		if(0 !== strpos($ip, '172.16') ){
			echo '403';
			die();
		}
	}
	
	
    $uploadroot = dirname(__FILE__).'../../d.klib.gaosiedu.com/'; // Relative Upload Location of data file
    if (is_uploaded_file($_FILES['file']['tmp_name'])) 
    {
		$uploadfile = $uploadroot . '/' . str_replace('_', '/', $_FILES['file']['name']);
		$uploaddir = dirname($uploadfile);
		$filename = basename($uploadfile);
		file_put_contents('debug', $uploadfile . ' - ' . $uploaddir . ' - ' . $filename . "\n", FILE_APPEND);
		echo "uploadfile:" . $uploadfile ;
		echo "uploaddir:" . $uploaddir ;
		echo "filename:" . $filename ;
		
		//目录不存在，创建
		if(!file_exists($uploaddir))
		{
			//create the folder
			$rs =	mkdir($uploaddir, 0777, true);
			chmod($uploaddir, 0777); 
			//file_put_contents('debug', var_export($rs, true) . "\n", FILE_APPEND);
		}
		
		//文件已存在，先删除，以便更新
		if(file_exists(	$uploadfile ))
		{
			unlink($uploadfile);
		}
			
        //echo "File ". $_FILES['file']['name'] . " uploaded successfully. ";
        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) 
        {
	    	chmod($uploadfile, 0777);
            echo "File is valid, and was successfully moved. " . $uploadfile ;
        }
		else{
			echo "error: move_uploaded_file";
			//print_r($_FILES);
		}
    }
    else 
    {
        echo "error: Upload Failed!!!";
        //print_r($_FILES);
    }
?>
