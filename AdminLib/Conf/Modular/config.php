<?php
return array(
	'DEFAULT_CONN'  => 'MYSQL_CONN',

	'ITEM'=>array('1'=>'姓名','2'=>'性别','3'=>'学校','4'=>'年级','5'=>'学科','6'=>'Email','7'=>'手机号','8'=>'留言'),
	'CHANNEL'		=>array('1'=>'幼教频道','2'=>'小学频道','3'=>'中学频道','4'=>'VIP','5'=>'其他'),
	'GRADES'=> array('1'=>'幼儿园','2'=>'一年级','3'=>'二年级','4'=>'三年级','5'=>'四年级','6'=>'五年级','7'=>'六年级','8'=>'初一','9'=>'初二','10'=>'初三','11'=>'高一','12'=>'高二','13'=>'高三'),
	'SUBJECTS'=>array('1'=>'语文','2'=>'数学','3'=>'英语','4'=>'物理','5'=>'化学'),
	'SEX'=>array('0'=>'男','1'=>'女'),
	'PAGESIZE'=>'20',
	'MARKED_WORDS'=>array('1'=>'已有#人预约','2'=>'已有#人报名'),

	'REPLACES'=>array('0'=>array('title'=>'姓名','replace'=>'#name'),
					 '1'=>array('title'=>'性别','replace'=>'#sex'),
					 '2'=>array('title'=>'学校','replace'=>'#school'),
					 '3'=>array('title'=>'年级','replace'=>'#grade'),
					 '4'=>array('title'=>'学科','replace'=>'#subject'),
					 '5'=>array('title'=>'Email','replace'=>'#email'),
					 '6'=>array('title'=>'手机号','replace'=>'#phone'),
					 '7'=>array('title'=>'留言','replace'=>'#message')),
	'EXERCISE_CATEGORY' => array(
					'0' =>'请选择',
					'1' =>'语文',
					'2' =>'数学',
					'3' =>'英语',
					'4' =>'物理'



		),


);
?>
