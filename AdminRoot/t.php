<?php
$pdo = new PDO('mysql:host=' . ATF_KMS_HOST . ';dbname=kms_gaosi', ATF_KMS_USER, ATF_KMS_PASS);
$pdo->exec('SET NAMES utf8');
$sql = 'select * from vip_lecture_archive where id=6107';

$row = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);


$str = $row['cart'];
#echo $str;exit;
#echo substr($str, 2113, 89110);exit;
#echo strlen($str);exit;


#echo date('Y-m-d H:i:s', 1479279886);exit;

print_r(unserialize($str));