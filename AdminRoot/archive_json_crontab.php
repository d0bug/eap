#!/usr/local/bin/php56
<?php
$filename=dirname(dirname(__FILE__)).'/json_'.time().'.txt';
$fp=fopen($filename, "w+"); //打开文件指针，创建文件
if ( !is_writable($filename) ){
      die("文件:" .$filename. "不可写，请检查！");
}
try{
 	//$dao = new PDO('dblib:host=211.157.101.115:11533;dbname=GSTest', 'admin', 'hxj@)!*gsEdu');
 	$dao = new PDO("mysql:host=' . ATF_KMS_HOST . ';dbname=kms_gaosi",ATF_KMS_USER,ATF_KMS_PASS );//线上
	//$dao = new PDO("mysql:host=192.168.1.250;dbname=kms4_gaosi","root","123456" );//本地
} catch (PDOException $e) {
 	$content .=  '无法访问!';
}
$maxId = 0;
$limit = 500;
do{
	$count = 0;
	$sql = "SELECT id,title,config,cart,config_json,cart_json FROM vip_lecture_archive where status != -1 and id >".$maxId." ORDER BY id ASC limit ".$limit;
	#$sql = 'select id,title,config,cart,config_json,cart_json from vip_lecture_archive where id=6242';
	echo $sql."\n";
	$query = $dao->query($sql);
	$list = array();
	while($row=$query->fetch()){
		if(!$row['cart_json'] || !$row['config_json'] ){
			$config = unserialize($row['config']);
			$cart = unserialize($row['cart']);
			if($config && $cart){
				$config_json = json_encode($config);
				$cart_json = json_encode($cart);
				$dao->exec("UPDATE vip_lecture_archive set config_json = ".$dao->quote($config_json)." ,cart_json = ".$dao->quote($cart_json)." WHERE id = ".abs($row['id']));
			}else{
				$content .= $row['id']."-".$row['title']."-failed\r\n";
			}
			
		}
		$count++;
		$maxId=$row['id'];
		
	}
}while($count==$limit);
fwrite($fp,$content);
fclose($fp);  //关闭指针

?>
