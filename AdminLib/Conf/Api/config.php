<?php 
return array(
'DEFAULT_CONN'  => 'MSSQL_CONN',
'RANK'=> array('金牌教师','专家教师'),
'GRADES'=> array('1'=>'一年级','2'=>'二年级','3'=>'三年级','4'=>'四年级','5'=>'五年级','6'=>'六年级','7'=>'初一年级','8'=>'初二年级','9'=>'初三年级','10'=>'高一年级','11'=>'高二年级','12'=>'高三年级'),
'SUBJECT'=> array('语文','数学','英语','物理','化学','生物','地理','政治','历史'),
'ARTICLE_TYPE'=> array('important'=>'重要资讯','ordinary'=>'一般资讯'),
'HANDOUTS_TYPE'=> array('0'=>'课程讲义','1'=>'试题库'),

'KNOWLEDGE_POINT'=> array('0'=>'行程问题','1'=>'一元二次方程'),
'KNOWLEDGE_PERMISSION'=> array('0'=>'所有人可见','1'=>'仅教研人员可见'),
'PAGESIZE'=>'21',
'PAGESIZE_LIST'=>'50',
'DOWNLOAD_LIMIT'=>6,
'DEFAULT_LISTSTYLE'=>'img',

//调课加课接口
//'aspxWebService'=>'http://vip1.gaosiedu.com:8000/test/TeacherController.svc?wsdl',//测试
'aspxWebService'=>'http://vip1.gaosiedu.com:8000/TeacherController.svc?wsdl',//线上
'aspxWebService_wx'=>'http://vip1.gaosiedu.com:8000/test/TeacherController.svc?wsdl',//微信测试


//'pdf2swf'=>'/usr/local/swftools/bin/pdf2swf',//本地
'pdf2swf'=>'/usr/local/bin/pdf2swf',//线上
'BIOCLOCK_START'=>'2014-03-22 00:00:00',//VIP指纹考勤机开始使用时间
'DEPT'=>array('黄庄师资部','金源师资部','公主坟师资部','高思大厦师资部','兼职教师部门','教研中心','招聘培训部','玉泉路师资部'),
'timeArr'=>array('7:00','7:30','8:00','8:30','9:00','9:30','10:00','10:30','11:00','11:30','12:00','12:30','13:00','13:30','14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30','18:00','18:30','19:00','19:30','20:00','20:30','21:00','21:30','22:00','22:30','23:00','23:30'),

//微信版设置
//'appID'=>'wx4e20cb6e9d081c77',//测试appID
//'appsecret'=>'84e6e76551ed95efeb104fcd1f3d5814',//测试appsecret
'appID'=>'wx9ff9ceab1a56a431',//正式appID
'appsecret'=>'8755c0f99b61188ceb296ea181360465',//正式appsecret


//阿里云存储配置
'OSS_ACCESS_ID'=>'Jc7kCBArGDfPKIEC',
'OSS_ACCESS_KEY'=>'DTu0BUWAzRdTEgrsTGJwldUmgUGIEs',
'DEFAULT_OSS_HOST'=>'oss-cn-beijing.aliyuncs.com',
'DEFAULT_OSS_HOST_SHOW'=>'video.gaosiedu.com',
'OSS_video_PATH'=>'upload/video/',
'OSS_IMG_PATH'=>'upload/image/',
'BUCKET'=>'gaosivideo',

'OPTIONS_KEY'=>array('0'=>'A','1'=>'B','2'=>'C','3'=>'D','4'=>'E','5'=>'F','6'=>'G','7'=>'H','8'=>'I','9'=>'J'),
'NUMBER_KEY'=>array('0'=>'一','1'=>'二','2'=>'三','3'=>'四','4'=>'五','5'=>'六','6'=>'七','7'=>'八','8'=>'九','9'=>'十','10'=>'十一','11'=>'十二','12'=>'十三','13'=>'十四','14'=>'十五'),
'report_demo'=>'report_demo.html',
'report_wx_demo'=>'report_wx_demo.html',
'LECTURE_DOWNLOAD_URL' => 'http://123.56.149.94:81/Generate/Lecture.ashx',
'PIV_START'=>'2015-06-01 00:00:00',
'APP_DIR_NAME'=>'eap',
/*无界面浏览器安装目录*/
'PHANTOMJS_PATH' => '/usr/local/bin/phantomjs',//线上
'PHANTOMJS_SCRIPT'=>'/vhost/apps/eap/Static/phantomjs/',//线上
//'PHANTOMJS_PATH' => '/usr/local/bin/phantomjs',//本地
//'PHANTOMJS_SCRIPT'=>'/root/phantomjs/examples/',//本地
'APP_DIR_NAME'=>'eap',

//自动生成辅导方案接口
//'programWebService'=>'http://vip1.gaosiedu.com:8000/Test/TeacherController.svc?wsdl',//测试
'programWebService'=>'http://vip1.gaosiedu.com:8000/TeacherController.svc?wsdl',//线上

'program_demo'=>'program_demo.html',
'words_template'=>'×××同学，这段时间[评价:笔记习惯]课后[评价:作业情况]课堂表现方面：[评价:师生互动][评价:做题规范]'
);
?>