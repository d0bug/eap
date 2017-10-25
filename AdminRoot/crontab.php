#!/usr/bin/env php
<?php
$filename=dirname(dirname(__FILE__)).'/Upload/'.time().'.txt';
$fp=fopen($filename, "w+"); //打开文件指针，创建文件
if ( !is_writable($filename) ){
      die("文件:" .$filename. "不可写，请检查！");
}

define('APP_DIR', dirname(dirname(__FILE__)));
define('PDFTOSWF', '/usr/local/bin/pdf2swf');
$content = '';
try{
 	//$dao = new PDO('dblib:host=211.157.101.115:11533;dbname=GSTest', 'admin', 'hxj@)!*gsEdu');
 	$dao = new PDO('dblib:host=db.gaosiedu.com:11533;dbname=GS', 'vipsys', 'ydy20!%05)^');//线上
} catch (PDOException $e) {
 	$content .=  '无法访问业务系统!';
}
$query = $dao->query("SELECT [hid],[teacher_version],[student_version],[is_delete],[is_rename],[teacher_version_preview],[student_version_preview] FROM vp_handouts WHERE is_delete = 0 AND ((teacher_version !='' AND is_exist_teacher_preview = 0) OR (student_version !='' AND is_exist_student_preview = 0)) ORDER BY hid DESC");
$list = array();
while($row=$query->fetch()){
	$list[] = $row;
}

if(!empty($list)){
	foreach($list as $key=>$row){
		$content .=  $row['hid']."\r\n";
		if($row['is_rename'] == 1){
			if(!empty($row['teacher_version']) && $row['is_exist_teacher_preview'] == 0 && file_exists(APP_DIR.$row['teacher_version'])){
				if(!empty($row['teacher_version_preview']) && file_exists(APP_DIR.$row['teacher_version_preview'])){
					$content .= "UPDATE vp_handouts set is_exist_teacher_preview = '1' WHERE hid = '".$row['hid']."';";
					if($dao->exec("UPDATE vp_handouts set is_exist_teacher_preview = '1' WHERE hid = '".$row['hid']."'")){
						$content .= " success\r\n";
					}else{
						$content .= " failed\r\n";
					}
					
				}else{
					$content .= doConvert($row['teacher_version'],$row['hid'],'teacher_version',$row['is_rename']);
				}
			}
			if(!empty($row['student_version']) && $row['is_exist_student_preview'] == 0 && file_exists(APP_DIR.$row['student_version'])){
				if(!empty($row['student_version_preview']) && file_exists(APP_DIR.$row['student_version_preview'])){
					$content .=  "UPDATE vp_handouts set is_exist_student_preview = '1' WHERE hid = '".$row['hid']."'";
					if($dao->exec("UPDATE vp_handouts set is_exist_student_preview = '1' WHERE hid = '".$row['hid']."'")){
						$content .= " success\r\n";
					}else{
						$content .= " failed\r\n";
					}
				}else{
					$content .= doConvert($row['student_version'],$row['hid'],'student_version',$row['is_rename']);
				}
			}
		}else{
			if(!empty($row['teacher_version']) && $row['is_exist_teacher_preview'] == 0 && file_exists(APP_DIR.$row['teacher_version'])){
				$swfFile =  reset(explode('.',$row['teacher_version'])).'.swf';
				if(!file_exists(APP_DIR.$swfFile)){
					$content .= doConvert($row['teacher_version'],$row['hid'],'teacher_version',$row['is_rename']);
				}else{
					$content .=  "UPDATE vp_handouts set is_exist_teacher_preview = '1' ,teacher_version_preview = '".$swfFile."' WHERE hid = '".$row['hid']."'";
					if($dao->exec("UPDATE vp_handouts set is_exist_teacher_preview = '1' ,teacher_version_preview = '".$swfFile."' WHERE hid = '".$row['hid']."'")){
						$content .= " success\r\n";
					}else{
						$content .= " failed\r\n";
					}
				}
			}
			if(!empty($row['student_version']) && $row['is_exist_student_preview'] == 0 && file_exists(APP_DIR.$row['student_version'])){
				$swfFile =  reset(explode('.',$row['student_version'])).'.swf';
				if(!file_exists(APP_DIR.$swfFile)){
					$content .= doConvert($row['student_version'],$row['hid'],'student_version',$row['is_rename']);
				}else{
					$content .=  "UPDATE vp_handouts set is_exist_student_preview = '1' ,student_version_preview = '".$swfFile."' WHERE hid = '".$row['hid']."'";
					if($dao->exec("UPDATE vp_handouts set is_exist_student_preview = '1' ,student_version_preview = '".$swfFile."' WHERE hid = '".$row['hid']."'")){
						$content .= " success\r\n";
					}else{
						$content .= " failed\r\n";
					}
				}
			}
		}
	}
}
fwrite($fp,$content);
fclose($fp);  //关闭指针


function doConvert($file, $hid, $type, $is_rename){
		$content = '';
		try{
			//$dao = new PDO('dblib:host=211.157.101.115:11533;dbname=GSTest', 'admin', 'hxj@)!*gsEdu');
			$dao = new PDO('dblib:host=db.gaosiedu.com:11533;dbname=GS', 'vipsys', 'ydy20!%05)^');//线上
		} catch (PDOException $e) {
			$content .=  '无法访问业务系统!';
		}
		if(strpos('a'.$file,"(")){
			$sourceFile = str_replace('(','（',str_replace(')','）',$file));
		}else{		
			$sourceFile = $file;
		}
		if(file_exists(APP_DIR.$sourceFile)){
			$fileType = strtolower(end(explode('.',$sourceFile)));
			//$filesize = filesize($sourceFile);
			if($fileType != 'pdf'){
				if($is_rename == 1){
					$fileNameArr = explode('/',trim($sourceFile,'/'));
					$tempFileNameArr = explode('.',end(explode('_',$fileNameArr[2])));
					$tempFile = APP_DIR.'/'.$fileNameArr[0].'/'.$fileNameArr[1].'/'.$tempFileNameArr[0].'.'.$tempFileNameArr[1].'.pdf';
					$swfFile = APP_DIR.'/'.$fileNameArr[0].'/'.$fileNameArr[1].'/'.$tempFileNameArr[0].'.'.$tempFileNameArr[1].'.swf';
				}else{
					$tempFile = APP_DIR.reset(explode('.',$sourceFile)).'.pdf';
					$swfFile = APP_DIR.reset(explode('.',$sourceFile)).'.swf';
				}
				
				$commond = "unoconv -f pdf -o $tempFile  ".APP_DIR.$sourceFile;
				$commond2 = PDFTOSWF." -T 9 -s poly2bitmap ".$tempFile." ".$swfFile;
				if(file_exists(APP_DIR.$sourceFile)){
					exec($commond);
				}
				if(file_exists($tempFile)){
					exec($commond2);
				}
				$content .= $commond."\r\n".$commond2."\r\n";
				if(file_exists($swfFile)){
					if($type == 'teacher_version'){
						$updateQuery = "UPDATE vp_handouts set teacher_version_preview = '".end(explode('/eap',$swfFile))."',is_exist_teacher_preview = 1 where hid = '".$hid."'";
					}else{
						$updateQuery = "UPDATE vp_handouts set student_version_preview = '".end(explode('/eap',$swfFile))."',is_exist_student_preview = 1 where hid = '".$hid."'";
					}
					$content .= $hid.$type." preview success\r\n";
					$dao->exec($updateQuery);
				}else{
					$content .= $hid.'-'.$type." preview failed\r\n";
				}
				
			}
			if($fileType == 'pdf'){
				if($is_rename == 1){
					$fileNameArr = explode('/',trim($sourceFile,'/'));
					$tempFileNameArr = explode('.',end(explode('_',$fileNameArr[2])));
					$swfFile = APP_DIR.'/'.$fileNameArr[0].'/'.$fileNameArr[1].'/'.$tempFileNameArr[0].'.'.$tempFileNameArr[1].'.swf';
				}else{
					$tempFile = APP_DIR.reset(explode('.',$sourceFile)).'.pdf';
					$swfFile = APP_DIR.reset(explode('.',$sourceFile)).'.swf';
				}
				$commond = PDFTOSWF." -T 9 -s poly2bitmap ".APP_DIR.$sourceFile." ".$swfFile;
				exec($commond);
				$content .= $commond."\r\n";
				if(file_exists($swfFile)){
					if($type == 'teacher_version'){
						$updateQuery = "UPDATE vp_handouts set teacher_version_preview = '".end(explode('/eap',$swfFile))."',is_exist_teacher_preview = 1 where hid = '".$hid."'";
					}else{
						$updateQuery = "UPDATE vp_handouts set student_version_preview = '".end(explode('/eap',$swfFile))."',is_exist_student_preview = 1 where hid = '".$hid."'";
					}
					$content .= $hid.$type." preview success\r\n";
					$dao->exec($updateQuery);
				}else{
					$content .= $hid.'-'.$type." preview failed\r\n";
				}
				
			}
		}else{
			$content .= $hid.'-'.$type." file not exist\r\n";
		}
		return $content;
}
exit;
?>
