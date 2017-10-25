<?php
$dbConfig = array(
            'MYSQL_CONN2'      =>array('TYPE'=>'mysql', 'DSN'=>'mysql:host=' . RDS_MYSQL_HOST . ';dbname=gaosivip', 'USER'=>RDS_MYSQL_USER, 
                                      'PASSWORD'=>RDS_MYSQL_PASS, 'PREFIX'=>'', 'CHARSET'=>'utf8'),
            'MSSQL_CONN'      =>array('TYPE'=>'mssql', 'DSN'=>MS_VIP_DBDSN, 'USER'=>MS_VIP_DBUSER,
                                      'PASSWORD'=>MS_VIP_DBPASS, 'PREFIX'=>'', 'convertEncoding'=>false),
            'MSSQL_TEST'      =>array('TYPE'=>'mssql', 'DSN'=>MS_DEV_DBDSN, 'USER'=>MS_DEV_DBUSER,
                                      'PASSWORD'=>MS_DEV_DBPASS, 'PREFIX'=>'', 'convertEncoding'=>false),
            'MONGO_CONN'      =>array('TYPE'=>'mongodb', 'DSN'=>'mongodb://127.0.0.1:27017', 'DBNAME'=>'eap'),
			'MYSQL_CONN_KNOWLEDGE' => array('TYPE'=>'mysql', 'DSN'=>'mysql:host=' . ATF_KMS_HOST . ';dbname=kms_gaosi', 'USER'=>ATF_KMS_USER, 
                                      'PASSWORD'=>ATF_KMS_PASS, 'PREFIX'=>'', 'CHARSET'=>'utf8'),
            'MYSQL_CONN_VIPSCHOOL'      =>array(
                'TYPE'=>'mysql', 
                'DSN'=>'mysql:host=10.44.8.225;dbname=gaosischool', 
                'USER'=>'root',
                'PASSWORD'=>'gaosi.com', 
                'PREFIX'=>'', 
                'CHARSET'=>'utf8'),
            'MYSQL_CONN_WEIXIN'      =>array(
                'TYPE'=>'mysql', 
                'DSN'=>'mysql:host=10.44.8.225;dbname=gs_weixin', 
                'USER'=>'root',
                'PASSWORD'=>'gaosi.com', 
                'PREFIX'=>'', 
                'CHARSET'=>'utf8'),     
            'MYSQL_CONN_EVAL' => array (
				'TYPE' => 'mysql',
				'DSN' => 'mysql:host=' . RDS_MYSQL_HOST . ';dbname=gaosivip',
				'USER' => RDS_MYSQL_USER,
				'PASSWORD' => RDS_MYSQL_PASS,
				'PREFIX' => '',
				'CHARSET' => 'utf8' 
                ),
             'MSSQL_APP' =>array(
                'TYPE' => 'mssql',
                'DSN' => MS_VIP_DBDSN,
                'USER' => MS_VIP_DBUSER,
                'PASSWORD' => MS_VIP_DBPASS,
                'PREFIX' => '',
                'convertEncoding' => false 
            ),
             'MYSQL_CONN_ENROLL'      =>array(
                'TYPE'=>'mysql',
                'DSN'=>'mysql:host='.RDS_MYSQL_HOST.';dbname=atf_enroll',
                'USER'=>RDS_MYSQL_USER,
                'PASSWORD'=>RDS_MYSQL_PASS,
                'PREFIX'=>'',
                'CHARSET'=>'utf8'
           ),
    );
$smsConfig = array(
		'host'=>'59.108.107.155', //'111.207.176.102'
		'dbname'=>'mas',
		'username'=>'masadmin',
		'password'=>'5t6y7u8i',
		'charset'=>'gbk'
);
$adHost = 'ad1.bj.gaosiedu.com'; //'111.207.176.72'
$ldapAnonyUser = 'anonym';
$ldapAnonyPass = '13552474700';
$apiKey = md5('http://vip.gaosiedu.com');
$apiUrlPrefix = 'http://vip.gaosiedu.com/Api';
$apiGzip = false;
$exportPdfDir = '/vhost/apps/eap/Export';
$pdfDir = '/vhost/apps/eap/PdfDir';
$ngxSendFileMap = array(
	array('/vhost/apps/eap/Export', '/exportPdf'),
    array('/vhost/apps/eap/Download', '/downloadPdf'),
    array('/vhost/apps/eap', '/eap'),
);
$reportUrl = 'http://student.gaosiedu.com/Exam/Score/viewReport';
$printDomain = 'study.local.com';
$scoreEncryptKey = 'study.local.com';
$scanDir = '/vhost/apps/eap/Scan';
$examSuperUsers = array('huxianjun', 'liyao', 'zhaojiapeng', 'zhangpengwei');
$tikuUrl = 'http://gapi.gaosiedu.com/ti/api/';
$editUrl = 'http://stutest.gaosiedu.com/Homework/Index/ajaxpost';
$uformHandlerPre = 'http://student.gaosiedu.com/Util/Uform/';
?>
