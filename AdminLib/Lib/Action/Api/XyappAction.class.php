<?php
/*
*Date 15-12-03
* 学员app接口
*/

  
class XyappAction extends ApiCommAction {

//===================sofiastart==============================================
   //eap-运营视频
    public function videoUrlShow(){
		/*
        $arr = $_GET;		
        $arr['video_url_show'] = 'http://gaosiyunying.oss-cn-beijing.aliyuncs.com/'.$arr['video_url'];        
       	*/
		$arr = $_GET;
        if(!empty($arr)){
            $videoInfo = D('VipOperation')->get_videoInfo($arr);
            if( preg_match('/\.mp4$/is', $videoInfo['video_url']) ) {
                 $videoName = $videoInfo['video_url']; 
            }elseif(preg_match('/\.mp3$/is', $videoInfo['video_url'])){
                $videoName = $videoInfo['video_url'];
            }else{
                 $videoName = $videoInfo['video_url'].".mp4";
            }   
        }
        
        $videoInfo['video_url_show'] = 'http://gaosiyunying.oss-cn-beijing.aliyuncs.com/'.$videoName;
		//echo $videoInfo['video_url_show'];exit;
		$this->assign(get_defined_vars());
		$this->display();
    }
    //获取当前用户
    public function getusers(){
 /*     $pu_time = strtotime("2016-04-12 14:10:00");
		echo $pu_time;
		exit;
*/
        $XyappModel = D('Xyapp');   
        $uid = $_GET['uid'];//当前学生id
        $sstudentcode = $_GET['ucode'];//当前学生编码
        $sstudentname = $_GET['uname'];//当前学生名称
        if(!empty($sstudentcode) && !empty($sstudentname) && !empty($uid)){
            
            //------start 手动推送 APP更新内容-------
             /*
             $upmessage = "test";
             $getpushgx =$XyappModel->get_gx_push_message($sstudentcode,$upmessage);
             if(empty($getpushgx)){
                $pu_time = strtotime("2016-03-31 17:00:00");

                $createtime = date('Y-m-d H:i:s',time());
                $setpushgx =$XyappModel->ins_gx_push_message($sstudentcode,$pu_time,$upmessage,$createtime);
             }
             */
            //------end-------
            
            
            $getuser = $XyappModel->get_userid($uid,$sstudentcode,$sstudentname);
            //dump($getuser);exit;
            if(empty($getuser)){
                $ingole = $XyappModel->in_usgold($uid,$sstudentcode,$sstudentname);//插入用户
                if(!empty($ingole)){
                   $ouput = array(
                        'status'=>1,
                        'msg' =>"ok"
                      ); 
                 
                }else{
                  $ouput = array(
                        'status'=>-1,
                        'msg' =>"eroor"
                      ); 
                   
                }
            
            }else{
                $ouput=array(
                    'status'=> 2,
                    'msg' =>'已存在'
                );
            }            
            
        }else{
           $ouput = array(
            'status'=>-2,
            'msg' =>"error"
          ); 
        }
        
       echo $this->encode_json($ouput);die();
        
    }
    
    //是否签到
    public function signor(){
        $XyappModel = D('Xyapp');   
        $uid = $_GET['uid'];//当前学生id
        $sstudentcode = $_GET['ucode'];//当前学生编码
        $sstudentname = $_GET['uname'];//当前学生名称                
        $scode = $XyappModel->ins_scode($uid,$sstudentcode,$sstudentname);
        if(!empty($scode)){
            $sctime = date('Y-m-d',strtotime($scode[0]['create_time']));
            $nowtime = date('Y-m-d',time());                
            if($nowtime==$sctime ){
                $qiandao = 1; //已签到
            }else{
                $qiandao = 2; //未签到
            }
            $ouput = array(
                'status'=>1,
                'msg' =>"ok",
                'qiandao'=>$qiandao
            );
        }else{
          $ouput = array(
                'status'=>-1,
                'msg' =>"error"
              ); 
           
        }        
       echo $this->encode_json($ouput);die();        
    }
    
    //课节报告---是否已点评--ios
    public function orreview(){
        $XyappModel = D('Xyapp'); 
        $uid = $_GET['uid'];//当前学生id
        $sstudentcode = $_GET['ucode'];//当前学生编码       
        $kid = $_GET['kid'];//当前课节id
        $skechengcode = $_GET['kcode'];//当前课程编码
        //echo $skechengcode;exit;
        if(!empty($sstudentcode) && !empty($skechengcode)){
           $getre = $XyappModel->getorreview($uid,$sstudentcode,$kid,$skechengcode);
           //dump($getre);exit;
           if(!empty($getre)){                
                $ouput = array(
                    'status'=>1,
                    'msg' => '已点评'
                           
                );
            }else{
                //获取数据失败
                $ouput = array(
                    'status'=>2,
                    'msg'=>'未点评'          
                );
            }
        }else{
            $ouput = array(
                'status'=>-1,
                'msg'=>'error'          
            );
        }
        echo $this->encode_json($ouput);die();
    }

    
    //课节报告===点评
    public function slComments(){
        $XyappModel = D('Xyapp'); 
        $uid = $_POST['uid'];//当前学生id
        $sstudentcode = $_POST['ucode'];//当前学生编码
        $sstudentname = $_POST['uname'];//当前学生名称
        $kid = $_POST['kid'];//当前课节id
        $skechengcode = $_POST['kcode'];//当前课程编码
        $teacher = $_POST['teacher'];//上课老师  
        $kechengname = $_POST['kechengname'];//当前课程名称 
        $kemu = $_POST['kemu'];//当前科目    
        $kechengtime = $_POST['kechengtime'];//课程时间
        $whole = $_POST['whole'];//整体点评
        $teaching = $_POST['teaching'];//授课效果
        $service = $_POST['service'];//教学服务
        //$teaching_com = $_POST['teaching_com'];//授课效果内容
        //$service_com = $_POST['service_com'];//教学服务内容
        $comments = $_POST['comments'];//其它评语
        $money = $_POST['money'];//消费金额
   
        if(!empty($sstudentcode) && !empty($skechengcode)){            
            $create_time = date('Y-m-d H:i:s',time());
            $getre = $XyappModel->getorreview($uid,$sstudentcode,$kid,$skechengcode);
            if(!empty($getre)){
                $comInfo = $XyappModel->ins_comments($uid,$sstudentcode,$kid,$skechengcode,$whole,$teaching,$service,$comments,$create_time,$teacher,$kechengname,$kemu,$kechengtime,$sstudentname);
                //echo "11111";
                if($comInfo == true){
                    $ouput = array(
                        'status'=>1,
                        'msg' =>"点评成功"       
                    );
                }else{
                    //插入数据库信息失败
                   $ouput = array(
                    'status'=>-1,
                    'msg' =>"error"           
                  );  
                } 
            }else{
              $comInfo = $XyappModel->ins_comments($uid,$sstudentcode,$kid,$skechengcode,$whole,$teaching,$service,$comments,$create_time,$teacher,$kechengname,$kemu,$kechengtime,$sstudentname);               
                if($comInfo == true){                
                    $scode = $XyappModel->ins_scode($uid,$sstudentcode,$sstudentname);  //查询高豆记录                            
                    if(!empty($scode)){                      
                        $goldqd = $scode[0]['gs_gold']+20;
                        $momn = $money/20;                       
                        if(strstr($momn,".")){
                            $goldmn = strstr($momn,".",true);
                        }else{
                            $goldmn=$momn;
                        }                        
                        $goldcon = $goldqd+$goldmn;                                             
                        $upgold = $XyappModel->up_usgold($sstudentcode,$goldcon); //更新高豆记录
                        if($upgold !== false){                            
                             $content ="课节报告点评";
                             $adgomo = $goldmn+20;
                             $goldlist = $XyappModel->add_gold_list($sstudentcode,$sstudentname,$adgomo,$content,$create_time);                 
                        }                      
                    }                    
                    $ouput = array(
                        'status'=>1,
                        'msg' =>"点评成功",
                        'mon'=>$goldmn         
                    ); 
                }else{
                    //插入数据库信息失败
                   $ouput = array(
                    'status'=>-1,
                    'msg' =>"error"           
                  );  
                }  
            }
            
        }else{
            //学员code和课程code获取失败
            $ouput = array(
                'status'=>-2,
                'msg' =>"error"
              ); 
        }
        
         
       echo $this->encode_json($ouput);die();
            
    }    
    
    //课节报告点评记录
    public function commentsList(){
        $XyappModel = D('Xyapp'); 
        //$uid = $_POST['uid'];//当前学生id
        $sstudentcode = trim($_GET['ucode']);//当前学生编码        
        if(!empty($sstudentcode)){
            $selview = $XyappModel->get_review($sstudentcode);
            
            if(!empty($selview)){
               
                
                $ouput = array(
                    'status'=>1,
                    'msg' => '成功',
                    'selview'=>$selview          
                );
            }else{
                //获取数据失败
                $ouput = array(
                    'status'=>-1,
                    'msg'=>'error'          
                );
            }
        }else{
            //code码失败
            $ouput = array(
                    'status'=>-2,
                    'msg'=>'error'          
            );
        }
        
        echo $this->encode_json($ouput);die();
        
    }
    
    //用户在课节报告点评记录单个用户课程
    public function kcComments(){
        $XyappModel = D('Xyapp');
        $uid =trim($_GET['uid']);
        $sstudentcode =trim($_GET['ucode']);
        $sstudentname =trim($_GET['uname']);
        if(!empty($sstudentcode)){
            $getkccomm = $XyappModel->getkcComments($uid,$sstudentcode,$sstudentname);           
            $ouput=array(
                'status'=>1,
                'msg'=>$getkccomm
            );            
        }else{
            $ouput=array(
                'status'=>-2,
                'msg'=>'error'
            );
        }
        echo $this->encode_json($ouput);die();
        
    }
    
    
    
    //签到
    public function signgold(){
        $XyappModel = D('Xyapp'); 
        $uid = trim($_GET['uid']);//当前学生名字
        $sstudentcode = trim($_GET['ucode']);//当前学生编码
        $sstudentname = trim($_GET['uname']);//当前学生名字
        $create_time = date('Y-m-d H:i:s',time());
        if(!empty($sstudentcode)){
            $scode = $XyappModel->ins_scode($uid,$sstudentcode,$sstudentname);                    
            if(!empty($scode)){
                $sctime = date('Y-m-d',strtotime($scode[0]['create_time']));
                $nowtime = date('Y-m-d',time());                
                if($nowtime==$sctime ){
                    $ouput = array(
                        'status'=>4,
                        'nowmsg'=>'今日已签到成功',
                        'nextmsg'=>'明日记得签到哦！'          
                    );
                }elseif($nowtime>$sctime){                    
                    if($scode[0]['nowgold'] == ''){
                        $goldcon = $scode[0]['gs_gold']+5;
                        $nowgold = 5;
                    }elseif($scode[0]['nowgold'] == 5){
                        $goldcon = $scode[0]['gs_gold']+10;
                        $nowgold = 10;
                    }elseif($scode[0]['nowgold'] == 10){
                        $goldcon = $scode[0]['gs_gold']+15;
                        $nowgold = 15;
                    }elseif($scode[0]['nowgold'] == 15){
                        $goldcon = $scode[0]['gs_gold']+20;
                        $nowgold = 20;
                    }elseif($scode[0]['nowgold'] == 20){
                        $goldcon = $scode[0]['gs_gold']+25;
                        $nowgold = 25;
                    }elseif($scode[0]['nowgold'] == 25){
                        $goldcon = $scode[0]['gs_gold']+30;
                        $nowgold = 30;
                    }elseif($scode[0]['nowgold'] == 30){                    
                        $goldcon = $scode[0]['gs_gold']+5;
                        $nowgold = 5;                        
                    }                    
                    $upgold = $XyappModel->up_signgold($sstudentcode,$goldcon,$nowgold,$create_time); //更新高豆记录  
                    if($upgold !== false){
                        $nowcom = $nowgold;
                        if($nowcom == 30 ){
                            $nextcom = 5; 
                        }else{
                            $nextcom = $nowcom+5;    
                        }                            
                        if(!empty($nowcom) && !empty($nextcom)){
                            $nowmsg ="恭喜您，签到成功，领取".$nowcom."高豆";
                            $nextmsg ="明日可领取".$nextcom."高豆";
                            $content="签到";
                            $goldlist = $XyappModel->add_gold_list($sstudentcode,$sstudentname,$nowcom,$content,$create_time); 
                           
                            $ouput = array(
                                'status'=>1,
                                'msg'=>'成功',
                                'nowmsg' =>$nowmsg,
                                'nextmsg' =>$nextmsg                              
                            ); 
                        }                             
                    }else{
                        //领取失败
                        $ouput = array(
                            'status'=>-1,
                            'msg'=>'error'          
                        ); 
                    } 
                }
            }else{
                //获取高豆失败
               $ouput = array(
                    'status'=>-2,
                    'msg'=>'error'          
               );
            }
            
        }else{
            //code码失败
            $ouput = array(
                    'status'=>-3,
                    'msg'=>'error'          
            );
        }
        
        echo $this->encode_json($ouput);die();
    }
    
    
    //用户高豆数量
    public function goldnum(){
        $XyappModel = D('Xyapp'); 
        //$uid = trim($_GET['uid']);//当前学生编码
        //$ucode = trim($_GET['ucode']);//当前学生编码
        //$uname = trim($_GET['uname']);//当前学生编码
        $arr = $_GET;
        if(!empty($arr)){
            $goldlist = $XyappModel->getgoldnum($arr);
            //dump($goldlist);exit;
            if(!empty($goldlist)){
                $ouput = array(
                    'status'=>1,
                    'msg'=>'成功',
                    'goldnum'=>$goldlist
                              
                );
            }
        }else{
            //code码失败
            $ouput = array(
                    'status'=>-2,
                    'msg'=>'error'          
            );
        }
        
        echo $this->encode_json($ouput);die();     
        
    }
    
    //我的测辅---测试卷
    public function getlesson(){
        $XyappModel = D('Xyapp');
        import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = 20;
        $sstudentcode = trim($_GET['ucode']);//当前学生编码
        $skechengcode = trim($_GET['kcode']);//搜索课程编码        
        if(!empty($sstudentcode)){
            $kechengList = $XyappModel->getkechengnamelist($sstudentcode); 
            $lessonList = $XyappModel->getlessonList($sstudentcode,$skechengcode,$curPage,$pagesize);
            //dump($lessonList);exit;
            if(!empty($lessonList)&&!empty($kechengList)){
                $ouput = array(
                    'status'=>1,
                    'msg'=>'成功',
                    'kechenglist'=>$kechengList,
                    'lessonlist'=>$lessonList          
                );
            }else{
                $ouput = array(
                    'status'=>-1,
                    'msg'=>'error'          
                );
            }              
        }else{
            //code码失败
            $ouput = array(
                    'status'=>-2,
                    'msg'=>'error'          
            );
        }
        
        echo $this->encode_json($ouput);die();        
        
    }
    
    //我的测辅--辅导方案
    public function getprogram(){
        $XyappModel = D('Xyapp');
        import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
		$pagesize = 20;
        $sstudentcode = trim($_GET['ucode']);//当前学生编码
        $skechengcode = trim($_GET['kcode']);//搜索课程编码
        if(!empty($sstudentcode)){
            $programName = $XyappModel->getprogramname($sstudentcode);
            
            $programList = $XyappModel->getprogramList($sstudentcode,$skechengcode);
            
            if(!empty($programList)){
                
                $ouput = array(
                    'status'=>1,
                    'msg'=>'成功',
                    'programname'=>$programName,
                    'programlist'=>$programList
                              
                );
            }else{
                $ouput = array(
                    'status'=>-1,
                    'msg'=>'error'          
                );
            }
            
        }else{
            //code码失败
            $ouput = array(
                    'status'=>-2,
                    'msg'=>'error'          
            );
        }
        
        echo $this->encode_json($ouput);die(); 
        
        
    }
    
   //我的测辅--评论
     public function prComments(){
        $XyappModel = D('Xyapp');
        $pid = $_POST['pid'];//当前测辅id
        $uid = $_POST['uid'];
        $sstudentcode = $_POST['ucode'];//当前学生编码
        $sstudentname = $_POST['uname'];//当前学生名字
        $skechengcode = $_POST['kcode'];//当前课程编码
        $kechengname = $_POST['kechengname'];//当前课程名称
        
        $whole = $_POST['whole'];//整体点评
        $teaching = $_POST['teaching'];//针对性
        $service = $_POST['service'];//内容
        //$teaching_com = $_POST['teaching_com'];//针对性点评
        //$service_com = $_POST['service_com'];//内容点评
        $comments = $_POST['comments'];//其它点评        
        
        if(!empty($sstudentcode) && !empty($skechengcode)){
            
            $create_time = date('Y-m-d H:i:s',time());        
            $comInfo = $XyappModel->prcomments($sstudentcode,$pid,$skechengcode,$whole,$teaching,$service,$comments,$create_time,$kechengname,$sstudentname);
            if($comInfo == true){                
               $scode = $XyappModel->ins_scode($uid,$sstudentcode,$sstudentname);  //查询高豆记录                        
                if(!empty($scode)){                
                    $goldcon = $scode[0]['gs_gold']+20;
                    $upgold = $XyappModel->up_usgold($sstudentcode,$goldcon); //更新高豆记录
                    if($upgold !== false){
                        $content = "测辅点评";
                        $goldlist = $XyappModel->add_gold_list($sstudentcode,$sstudentname,'20',$content,$create_time);
                    }
                     $ouput = array(
                        'status'=>1,
                        'msg' =>"点评成功"       
                      );      
                }else{
                    $ouput = array(
                        'status'=>-3,
                        'msg' =>"高豆error"
                      );
                }              
            }else{                
               $ouput = array(
                'status'=>-1,
                'msg' =>"error"           
              );  
            }
        }else{
            
            $ouput = array(
                'status'=>-2,
                'msg' =>"error"
              ); 
        }
        
         
       echo $this->encode_json($ouput);die();
            
    }    
    

    //辅导方案--查看评论
    public function getcommentslist(){
        $XyappModel = D('Xyapp'); 
        $pid = trim($_GET['pid']);//当前辅导方案id 
        $sstudentcode = trim($_GET['ucode']);//当前学生编码    
        $skechengcode = trim($_GET['kcode']);//当前课程编码    
        if(!empty($sstudentcode)&& !empty($skechengcode) && !empty($pid)){
            $selview = $XyappModel->get_review_fdfa($pid,$sstudentcode,$skechengcode);
            //dump($selview);exit;
            if(!empty($selview)){                
                $ouput = array(
                    'status'=>1,
                    'msg' => '成功',
                    'selview'=>$selview          
                );  
            }else{                
                $ouput = array(
                    'status'=>-1,
                    'msg'=>'error'          
                );
            }
        }else{           
            $ouput = array(
                    'status'=>-2,
                    'msg'=>'error'          
            );
        }
        
        echo $this->encode_json($ouput);die();
    }  
    
    
    //课程报名
    public function kechengsign($body){
        $XyappModel = D('Xyapp'); 
        $uid= trim($_GET['uid']);//当前操作id
        $studentname= trim($_GET['uname']);//当前操作人名字
        $studentcode= trim($_GET['ucode']);//当前操作人编码        
        $kid= trim($_GET['kid']);//当前课程id               
        $kechengname= trim($_GET['kname']);//当前课程名称
        $sname = trim($_GET['sname']);//姓名
        $sphone = trim($_GET['sphone']);//电话
        $smail = trim($_GET['smail']);//邮箱
        $sgrade = trim($_GET['sgrade']);//年级
        $smessage = trim($_GET['smessage']);//留言        
        $create_time = date('Y-m-d H:i:s',time());
        if(!empty($kechengname) && !empty($studentname) && !empty($sname) && !empty($sphone)){
            $insign = $XyappModel->in_kcsign($uid,$studentcode,$studentname,$kid,$kechengname,$sname,$sphone,$smail,$sgrade,$smessage,$create_time);
            if($insign == true){
               //echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
               $subjecttitle ="91好课系列课报名通知";
                $tomail ="honglian@gaosiedu.com";
                //$tomail ="liujingxian@gaosiedu.com";               
                $body .='<p><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></p>';
                $body .='<p><body style="margin: 10px;"></p>';
                $body .= '<div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 14px; ">';
                $body .= '<p>老师您好:</p>';
                $body .= '<p>'.$sname.'学员，电话号码：'.$sphone.'，邮箱：“'.$smail.'” ，年级：'.$sgrade.'，留言:'.$smessage.'，想报名：'.$kechengname.' ，请及时联系！';        
                $body .= '<p>辛苦啦！</p>';
                $body .= '<p><br><br><br><br><br><br>[ 高思一对一 APP ]</p>';
                $body .= '<p></div></body></p>'; 
                //echo $body;exit;
               $mailResult = $this->send_mail('gs1vs1@163.com',$tomail,$body,$subjecttitle);
               if($mailResult == true ){
                    $ouput = array(
                    'status'=>1,
                    'msg'=>'ok'          
                    ); 
               }else{
                    $ouput = array(
                        'status'=>-1,
                        'msg'=>'error'          
                    );
               }
               
            }else{
                 $ouput = array(
                    'status'=>-2,
                    'msg'=>'error'
                );
            }
            
        }else{
           
            $ouput = array(
                    'status'=>-3,
                    'msg'=>'error'          
            );
        }
        
        echo $this->encode_json($ouput);die();
         
    }
    

    
   //我的课表--讲义
   public function schedule_jy(){
        $XyappModel = D('Xyapp'); 
        $hid= trim($_GET['hid']);//当前操作id
        $ucode= trim($_GET['ucode']);//当前用户编码
        $kcode= trim($_GET['kcode']);//当前课程编码        
                
		if(!empty($ucode) && !empty($hid)){
		    
            $heluList = $XyappModel->get_helujylist($hid,$ucode);
           
            if(!empty($heluList)){
                $ouput = array(
                        'status'=>1,
                        'msg' => '成功',
                        'heluList'=>$heluList          
                    );
                }else{
                    //获取不到数据
                    $ouput = array(
                        'status'=>2, 
                        'msg'=>'无讲义'          
                    );
                }
                    
        }else{
            //code码失败
            $ouput = array(
                    'status'=>-2,
                    'msg'=>'error'          
            );
        }
        
        echo $this->encode_json($ouput);die();
        
   }
   
   
    //我的课表--新版讲义
   	public function previewLecture(){  
		$helu_id = $_GET['helu_id'];
		if(!empty($helu_id)){
			$newStudentsModel = D('Xyapp');//VpNewStudents
			$numberKey = C('NUMBER_KEY');
			$optionKeyArr = C('OPTIONS_KEY');
			$heluInfo = $this->getHeluInfo($helu_id);
		}            
       if($heluInfo['lecture_id'] == ''){
			$ouput = array(
                    'status'=>-1,
                    'msg'=>'暂无'          
            );
		    echo $this->encode_json($ouput);die();
		}else{
			$this->assign(get_defined_vars());
		    $this->display();
		}
	}
    
   	public function getHeluInfo($heluId){
		$key = md5($heluId);
		import('ORG.Util.NCache');
		$cache = NCache::getCache();
		$heluInfo = $cache->get('heluInfo', $key);
		if(false == $heluInfo) {
			$newStudentsModel = D('Xyapp');
			$heluInfo = $newStudentsModel->get_heluInfo($heluId);
			$cache->set('heluInfo', $key, $heluInfo);
		}
		return $heluInfo;
	} 
   
   
   
   //兑换-列表
   public function exchange_list(){
        $XyappModel = D('Xyapp'); 
        $uid= trim($_GET['uid']);//当前操作id
        $tid= trim($_GET['tid']);//分类id
        if(!empty($uid)){
            $extype=$XyappModel->getextype();
            
            if(!empty($tid)){
                $condition =' t.id='.$tid.'';   
            }
            $exchangelist=$XyappModel->getexchangelist($condition);
            
            $ouput = array(
                    'status'=>1,
                    'msg'=>$exchangelist         
            );
            
        }else{
            
            $ouput = array(
                    'status'=>-2,
                    'msg'=>'error'          
            );
        }
        
        echo $this->encode_json($ouput);die();
        
   }
   //兑换---确定兑换
   public function exch_record(){
        $XyappModel = D('Xyapp'); 
        $uid= trim($_GET['uid']);//当前操作id
        $ucode= trim($_GET['ucode']);//当前操作code
        $uname= trim($_GET['uname']);//当前操作姓名
        $pr_id= trim($_GET['pid']);//礼品id
        $pr_name= trim($_GET['pname']);//礼品名称
        $pgold= trim($_GET['pgold']);//所需高豆
        $address = trim($_GET['address']);//收货地址id               
        if(!empty($uid) && !empty($pr_id) && !empty($pgold) && !empty($address))
        {
            $create_time = date('Y-m-d H:i:s',time());          
            $selgold = $XyappModel->ins_scode($uid,$ucode,$uname);
            if($selgold[0]['gs_gold'] > $pgold){
                $order_num= 'GS-DH'.rand(1000000,9999999);//订单号 
                //dump($order_num);exit;                
                $recordeclist = $XyappModel->recordeclist($uid,$uname,$ucode,$pr_id,$pr_name,$pgold,$create_time,$order_num,$address);                
                if($recordeclist == true){ 
                        $ingold = $selgold[0]['gs_gold'] - $pgold;
                        $insql = $XyappModel->up_usgold($ucode,$ingold);                        
                        $con =' order_num = "'.$order_num.'" and  ucode = "'.$ucode.'" and pr_id = '.$pr_id.' and re_xgs = 0 order by id desc limit 1';
                        $selcontent = $XyappModel->selrecorde($con);
                        $content ="兑换礼品(".$pr_name.")消耗";
                        $sgold="-".$pgold;
                        $goldlist = $XyappModel->add_gold_list($ucode,$uname,$sgold,$content,$create_time);                        
                        $subjecttitle ="学员app兑换积分商品信息";               
                        //$tomail ="honglian@gaosiedu.com";
                        $tomail ="yangxuan@gaosiedu.com"; 
                        $body .='<p><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></p>';
                        $body .='<p><body style="margin: 10px;"></p>';
                        $body .= '<div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 14px; ">';
                        $body .= '<p> 老师您好:</p>';
                        $body .= '<p>'.$uname.'  学员，于'.$create_time.' 在学员APP里用高豆购买了    '.$pr_name.'，记得及时发货！';        
                        $body .= '<p>辛苦啦！</p>';
                        $body .= '<p><br><br><br><br><br><br>[ 高思一对一 APP ]</p>';
                        $body .= '<p></div></body></p>';                                   
                        $mailResult = $this->send_mail('gs1vs1@163.com',$tomail,$body,$subjecttitle);                       
                        if(!empty($insql)){                            
                            $ouput = array(
                                'status'=>1,
                                'msg'=>'兑换成功',
                                'rid'=>$selcontent[0]['id']          
                            );
                        }                  
                    
                }else{
                   
                    $ouput = array(
                            'status'=>-1,
                            'msg'=>'error'          
                    );
                }
            }else{
                $ouput = array(
                    'status'=>-3,
                    'msg'=>'对不起，您的高豆不足无法兑换！'    
                );
            }
           
        }else{
           
            $ouput = array(
                    'status'=>-2,
                    'msg'=>'error'          
            );
        }
        
        echo $this->encode_json($ouput);die();
        
   }
   
   
   //兑换成功-发送邮件给学管师
   public function mail_learn(){
        $XyappModel = D('Xyapp'); 
        $rid= trim($_GET['rid']);//兑换记录id
        $xname = trim($_GET['xname']);//学管师名称
        $xmail = trim($_GET['xmail']);//学管师email
        if(!empty($rid)){
            $con ='id = '.$rid.' and status =1';    
            $secon = $XyappModel->selrecorde($con); 
            $dhmail = $XyappModel->upexre($rid,$xname,$xmail); 
            if($dhmail== true){
               $subjecttitle ="学员app兑换积分商品信息";               
                //$tomail ="honglian@gaosiedu.com";
                $tomail ="yangxuan@gaosiedu.com"; 
                $body .='<p><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></p>';
                $body .='<p><body style="margin: 10px;"></p>';
                $body .= '<div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 14px; ">';
                $body .= '<p> 老师您好:</p>';
                $body .= '<p>'.$secon[0]['uname'].'  学员，于'.$secon[0]['re_time'].' 在学员APP里用高豆购买了    '.$secon[0]['pr_name'].'，订单号为    '.$secon[0]['order_num'].'，需要您帮忙领取！';        
                $body .= '<p>辛苦啦！</p>';
                $body .= '<p><br><br><br><br><br><br>[ 高思一对一 APP ]</p>';
                $body .= '<p></div></body></p>'; 
                          
               $mailResult = $this->send_mail('gs1vs1@163.com',$tomail,$body,$subjecttitle);
               if($mailResult == true ){
                    $ouput = array(
                        'status'=>1,
                        'msg'=>'ok'          
                    ); 
               }else{
                    $ouput = array(
                        'status'=>-1,
                        'msg'=>'error'          
                    );
               }
            }else{            
                $ouput = array(
                    'status'=>-2,
                    'msg'=>'error'          
                );
            } 
        }         
            
        echo $this->encode_json($ouput);die();
        
   }
   
   //兑换记录
   public function record_list(){
        $XyappModel = D('Xyapp'); 
        $uid= trim($_GET['uid']);//uid
        $ucode = trim($_GET['ucode']);//ucode
        if(!empty($uid) && !empty($ucode)){
            $secont = $XyappModel->selrecorde($uid,$ucode);
           // dump($secont);exit;
            if(!empty($secont)){
                $ouput = array(
                    'status'=>1,
                    'msg'=>$secont          
                );
            }else{
                $ouput = array(
                    'status'=>-1,
                    'msg'=>'error'          
                );
            }
        }else{
            $ouput = array(
                'status'=>-2,
                'msg'=>'error'          
            );
        }
        echo $this->encode_json($ouput);die();
   }
   
   
   //兑换记录里-确定兑换
   public function record_confirm(){
        $XyappModel = D('Xyapp'); 
        $rid =trim($_GET['rid']);//记录id
        $ucode= trim($_GET['ucode']);//当前操作code
        $create_time = date('Y-m-d H:i:s',time());  
        if(!empty($rid) && !empty($ucode) ){
            $secont = $XyappModel->con_record($rid,$ucode,$create_time);
             
            if($secont == true){
               $ouput = array(
                    'status'=>1,
                    'msg'=>'OK'          
                ); 
            }else{
                $ouput = array(
                    'status'=>-1,
                    'msg'=>'error'          
                );
            }  
        }else{
            $ouput = array(
                'status'=>-2,
                'msg'=>'error'          
            );
        }
        echo $this->encode_json($ouput);die();
   }
   
   
   //邮件发送
    function send_mail($frommail,$tomail,$body,$subjecttitle) {
        import('ORG.Util.Phpmailer');
        $mail = new PHPMailer(); 
        $mail->IsSMTP();                            // 经smtp发送  
        $mail->Host     = "smtp.163.com";           // SMTP 服务器  
        $mail->SMTPAuth = true;                     // 打开SMTP 认证  
        $mail->Username = "gs1vs1@163.com";     	// 用户名  
        $mail->Password = "gaosivip2016";              // 密码  
        $mail->From     = $frommail;                // 发信人  
        $mail->FromName = "高思教育";                  // 发信人别名  
        $mail->AddAddress($tomail);                 // 收信人  
       // $mail->AddAddress2($tomail2);               // 收信人2 
        $mail->CharSet = "utf-8"; 
        if(!empty($ccmail)){  
            $mail->AddCC($ccmail);                  // cc收信人  
        }  
        if(!empty($bccmail)){  
            $mail->AddCC($bccmail);                 // bcc收信人  
        }  
        $mail->WordWrap = 50;  
        $mail->IsHTML(true);                        // 以html方式发送  
        $mail->Subject  = $subjecttitle;            // 邮件标题 
        
        $mail->Body     = $body;                    // 邮件内容  
        $mail->AltBody  =  "请使用HTML方式查看邮件。";  
        return $mail->Send();  
    }
    
    //转盘抽奖
    public function rotary(){
        $XyappModel = D('Xyapp'); 
        $uid= trim($_GET['uid']);//uid
        $ucode = trim($_GET['ucode']);//ucode
        $uname = trim($_GET['uname']);//uname
        if(!empty($uid) && !empty($ucode) && !empty($uname)){
            
            $scode = $XyappModel->ins_scode($uid,$ucode,$uname);
            
            if($scode[0]['gs_gold'] >= 100 ){
                
                $prize_arr = array(   
                    '0' => array('id'=>1,'prize'=>'高豆100个','v'=>1),   
                    '1' => array('id'=>2,'prize'=>'高豆300个','v'=>1),   
                    '2' => array('id'=>3,'prize'=>'魔方一个','v'=>0.1),   
                    '3' => array('id'=>4,'prize'=>'精美抱枕一个','v'=>0.1),   
                    '4' => array('id'=>5,'prize'=>'电子阅读器一个','v'=>0.1),   
                    '5' => array('id'=>6,'prize'=>'精美水杯一个','v'=>0.1),   
                    '6' => array('id'=>7,'prize'=>'苹果ipad一个','v'=>0), 
                    '7' => array('id'=>8,'prize'=>'谢谢参与','v'=>97.3),   
                );   
                 
                foreach ($prize_arr as $key => $val) {   
                    $arr[$val['id']] = $val['v'];   
                }   
                $rid = $this->get_rand($arr); //根据概率获取奖项id   
                  
                //$res['yes'] = $prize_arr[$rid-1]['prize']; //中奖项            
                $zjyes = $prize_arr[$rid-1]['prize']; //中奖项  
                
                unset($prize_arr[$rid-1]); //将中奖项从数组中剔除，剩下未中奖项   
                shuffle($prize_arr); //打乱数组顺序   
                for($i=0;$i<count($prize_arr);$i++){   
                    $pr[] = $prize_arr[$i]['prize'];   
                }   
                $res['no'] = $pr;//未中奖
                            
                $create_time = date('Y-m-d H:i:s',time());
                $order_num= 'GS-CJ'.rand(1000000,9999999);//订单号
                if($zjyes === '谢谢参与'){
                    //未抽中只扣积分                    
                    $goldcon = $scode[0]['gs_gold']-100;
                    $content ="抽奖消耗";
                    $sgold="-100";                        
                    $goldlist = $XyappModel->add_gold_list($ucode,$uname,$sgold,$content,$create_time);    
                    $upgold = $XyappModel->up_usgold($ucode,$goldcon); //更新高豆记录
                    
                    if($upgold == true){
                      
                        $ouput = array(
                            'status'=>1,
                            'msg'=>'谢谢参与'          
                        );
                       
                    }else{
                        $ouput = array(
                            'status'=>-1,
                            'msg'=>'error'          
                        );
                    } 
                  
                }else{
                    $incj=$XyappModel->rotaryadd($uid,$ucode,$uname,$zjyes,$create_time,$order_num);
                    if($incj == true){
                        //抽中扣积分
                        $goldcon = $scode[0]['gs_gold']-100;
                        $content ="抽奖消耗";
                        $sgold="-100";
                        $goldlist = $XyappModel->add_gold_list($ucode,$uname,$sgold,$content,$create_time);
                        $upgold = $XyappModel->up_usgold($ucode,$goldcon); //更新高豆记录
                        if($upgold == true){
                          
                            if($zjyes =='高豆100个' || $zjyes == '高豆300个' ){ 
                                $content ="抽奖";                             
                                $sgold = mb_substr($zjyes,2,3,'utf-8'); 
                               
                                $scode = $XyappModel->ins_scode($uid,$ucode,$name);
                                $goldcon = $scode[0]['gs_gold']+$sgold;
                                $upgold = $XyappModel->up_usgold($ucode,$goldcon); //更新高豆记录                              
                                $goldlist = $XyappModel->add_gold_list($ucode,$uname,$sgold,$content,$create_time);                                
                            }
                            $ouput = array(
                                'status'=>2,
                                'msg'=>'恭喜您获得'.$zjyes."，您可以到兑换记录里查看哦"          
                            );
                           
                        }else{
                            $ouput = array(
                                'status'=>-2,
                                'msg'=>'error'          
                            );
                        }
                        
                    }else{
                        $ouput = array(
                            'status'=>-3,
                            'msg'=>'error'          
                        );
                    }    
                }
                 
            }else{
                $ouput = array(
                    'status'=>-4,
                    'msg'=>'积分不够'          
                );
            }
            
        }else{
            $ouput = array(
                'status'=>5,
                'msg'=>'error'          
            );
        }
        echo $this->encode_json($ouput);die();          
            
           
    }
   
    
    function get_rand($proArr) {   
        $result = '';    
        //概率数组的总概率精度   
        $proSum = array_sum($proArr);    
        //概率数组循环   
        foreach ($proArr as $key => $proCur) {   
            $randNum = mt_rand(1, $proSum);   
            if ($randNum <= $proCur) {   
                $result = $key;   
                break;   
            } else {   
                $proSum -= $proCur;   
            }         
        }   
        unset ($proArr);    
        return $result;   
    } 

   //高豆获取记录   
   public function gold_list(){
        $XyappModel = D('Xyapp'); 
        $ucode = trim($_GET['ucode']);
        if(!empty($ucode)){
            $gocont = $XyappModel->get_gold_list($ucode);
            
            $ouput = array(
                'status'=>1,
                'msg'=>$gocont        
            );
        }else{
            $ouput = array(
                'status'=>-1,
                'msg'=>'error'          
            );
        }
        
        echo $this->encode_json($ouput);die();   
   }

   //存入推送消息
   public function add_push(){
     $XyappModel=D('Xyapp');
     $ucode = trim($_POST['ucode']);
     $pu_time = trim($_POST['utime']);
     $pu_message = trim($_POST['umessage']);
     $create_time = date('Y-m-d H:i:s',time());
     if(!empty($ucode) && !empty($pu_time) && !empty($pu_message)){
        $addpush = $XyappModel->add_push_message($ucode,$pu_time,$pu_message,$create_time);
        if($addpush == true){
            $ouput = array(
                'status'=>1,
                'msg'=>'ok'          
            );
        }else{
            $ouput = array(
                'status'=>-1,
                'msg'=>'error'          
            );
         }
        
     }else{
            $ouput = array(
                'status'=>-2,
                'msg'=>'error'          
            );
     } 
       echo $this->encode_json($ouput);die(); 
   }
   
   //读取推送消息
   public function get_push(){
     $XyappModel=D('Xyapp');
     $ucode = trim($_GET['ucode']);
     if(!empty($ucode)){
        $getpush = $XyappModel->get_push_message($ucode);
        
        if(!empty($getpush)){
            $ouput = array(
                'status'=>1,
                'msg'=>$getpush
            );
        }else{
            $ouput=array(
                'status'=>-1,
                'msg'=>'error'
            );
        }
        
     }else{
        $ouput=array(
            'status'=>-2,
            'msg'=>'error'
        );
     }
     echo $this->encode_json($ouput);die();
   }
   
   //删除推送消息
   public function del_push(){
    $XyappModel=D('Xyapp');
    $pid=trim($_GET['pid']);
    if(!empty($pid)){
        $delpush=$XyappModel->del_push_message($pid);
        if($delpush==true){
            $ouput =array(
                'status'=>1,
                'msg'=>'ok'
            );
        }else{
            $ouput=array(
                'status'=>-1,
                'msg'=>'error'
            );
        }
    }else{
        $ouput=array(
            'status'=>-2,
            'msg'=>'error'
        );
        
    }
    echo $this->encode_json($ouput);die();
    
   }
   
   
    //导出兑换名单excel
	public function exportExcel(){
		$XyappModel=D('Xyapp');	
        $list = $XyappModel->excelAll();
		import("ORG.Util.Excel");
		$exceler = new Excel_Export();
		$fileTitle = '学员app礼品兑换列表';
		$dotype_name = mb_convert_encoding($fileTitle,'gbk','utf8');
		$exceler->setFileName($dotype_name.time().'.csv');
		$excel_title= array('编号','学生编号','学员姓名', '礼品','是否领取（1未领取，2已领取）');
		foreach ($excel_title as $key=>$title){
			$excel_title[$key] = mb_convert_encoding($title,'gbk','utf8');
		}
		$exceler->setTitle($excel_title);
		foreach ($list as $key=>$val){
		
				$tmp_data= array($val['id'],mb_convert_encoding($val['ucode'],'gbk','utf8'),mb_convert_encoding($val['uname'],'gbk','utf8'),mb_convert_encoding($val['pr_name'],'gbk','utf8'),mb_convert_encoding($val['re_type'],'gbk','utf8'));
			
			$exceler->addRow($tmp_data);
		}
		$exceler->export();
	}

    //收货地址列表
    public function shAddressList(){
        $XyappModel=D('Xyapp');
        $uid=trim($_GET['uid']);
        $ucode = trim($_GET['ucode']);
        //$uname = trim($_GET['uname']);
        if(!empty($uid) && !empty($ucode)){
            $addressList=$XyappModel->get_address_list($uid,$ucode);            
            if(!empty($addressList)){
                $ouput = array(
                    'status' => 1,
                    'msg' => $addressList
                );
            }else{
                $ouput = array(
                    'status' => -1,
                    'msg' => '暂无'
                );
            }
        }else{
            $ouput=array(
                'status'=>-2,
                'msg'=>'error'
            );
        }
        echo $this->encode_json($ouput);die();
    }
    
    //添加收货地址
    public function insShAddress(){   
        $XyappModel=D('Xyapp');
        $uid = trim($_GET['uid']);
        $ucode = trim($_GET['ucode']);
        $uname = trim($_GET['uname']);
        $shname= trim($_GET['s_name']);//收件人
        $shphone = trim($_GET['s_phone']);//收件人电话
        $city = trim($_GET['city']);//城市
       // $street = trim($_GET['street']);//街道
        $address = trim($_GET['address']);//详细地址
        $top = trim($_GET['top']);//是否是默认地址
        $create_time = date('Y-m-d H:i:s',time());
        if(!empty($uid) && !empty($ucode) && !empty($uname) && !empty($shname) && !empty($shphone) && !empty($city)  && !empty($address)){
            $arcount = $XyappModel->get_address_count($ucode,$uname);
            if($arcount >= 3){
                 $ouput = array(
                    'status' => 2,
                    'msg' => '您的收货地址已经超过三个，请删除原有地址进行添加。'
                );
            }else{
                $result = $XyappModel->ins_exchange_address($uid,$ucode,$uname,$shname,$shphone,$city,$address,$create_time,$top);
                if($result == true){
                    $ouput = array(
                        'status'=> 1,
                        'msg'=> 'ok'
                    );
                }else{
                    $ouput = array(
                        'status' => -1,
                        'msg' => 'error'
                    );
                }    
            }           
            
        }else{
            $ouput = array(
                'status'=>-2,
                'msg'=>'error'
            );
        }
        echo $this->encode_json($ouput);die();   
    }
    //修改收货地址
    public function upShAddress(){  
        $XyappModel=D('Xyapp');
        $aid = trim($_GET['aid']);
        $uid = trim($_GET['uid']);
        $ucode = trim($_GET['ucode']);
        $uname = trim($_GET['uname']);
        $shname= trim($_GET['s_name']);//收件人
        $shphone = trim($_GET['s_phone']);//收件人电话
        $city = trim($_GET['city']);//城市
        //$street = trim($_GET['street']);//街道
        $address = trim($_GET['address']);//详细地址
        $top = trim($_GET['top']);//是否是默认地址
        $update_time = date('Y-m-d H:i:s',time());
        if(!empty($aid) && !empty($uid) && !empty($ucode) && !empty($uname) && !empty($shname) && !empty($shphone) && !empty($city)  && !empty($address)){
            $result = $XyappModel->up_exchange_address($aid,$uid,$ucode,$uname,$shname,$shphone,$city,$address,$update_time,$top);
            if($result == true){
                $ouput = array(
                    'status'=> 1,
                    'msg'=> 'ok'
                );
            }else{
                $ouput = array(
                    'status' => -1,
                    'msg' => 'error'
                );
            }
        }else{
            $ouput = array(
                'status'=>-2,
                'msg'=>'error'
            );
        }
        echo $this->encode_json($ouput);die();   
    }
    //删除收货地址
    public function delShAddress(){
        $XyappModel=D('Xyapp');
        $aid = trim($_GET['aid']);
        $uid = trim($_GET['uid']);
        if(!empty($aid) && !empty($uid)){
            $result = $XyappModel->del_exchange_address($aid,$uid);
            if($result == true){
                $ouput = array(
                    'status'=> 1,
                    'msg'=> 'ok'
                );
            }else{
                $ouput = array(
                    'status' => -1,
                    'msg' => 'error'
                );
            }
        }else{
            $ouput = array(
                'status' => -2,
                'msg' => 'error'
            );
        }
        echo $this->encode_json($ouput);die(); 
    }
    //设置默认地址
    public function setAddressTop(){
        $XyappModel=D('Xyapp');
        $aid = trim($_GET['aid']);
        $uid = trim($_GET['uid']);
        $ucode = trim($_GET['ucode']);
        $top = trim($_GET['top']);
        $update_time = date('Y-m-d H:i:s',time());        
        if(!empty($aid) && !empty($uid) && !empty($ucode)){            
            $result = $XyappModel->set_exaddress_top($aid,$uid,$ucode,$top,$update_time);
            if($result == true){
                $ouput = array(
                    'status'=> 1,
                    'msg'=> 'ok'
                );
            }else{
                $ouput = array(
                    'status' => -1,
                    'msg' => 'error'
                );
            }
         }else{
            $ouput = array(
                'status' => -2,
                'msg' => 'error'
            );
        }
        echo $this->encode_json($ouput);die(); 
    }
    
    //统计-兑换 抽奖 签到
    public function statisticalGold(){
        $XyappModel = D('Xyapp');
        $ucode = trim($_GET['ucode']);
        $search_name = trim($_GET['search_name']);//兑换，抽奖，签到
        if(!empty($ucode) && !empty($search_name) ){
            $result = $XyappModel->get_statistical_gold($ucode,$search_name);
            $ouput = array(
                'status' => 1,
                'msg' => $result
            );
           
        }else{
            $ouput = array(
                'status' => -2,
                'msg' => 'error'
            );
        }
        echo $this->encode_json($ouput);die();
        
    }
    
    //统计-课评
    public function statisticalKep(){
        $XyappModel = D('Xyapp');
        $uid = trim($_GET['uid']);
        $ucode = trim($_GET['ucode']);
        if(!empty($uid) && !empty($ucode)){
            $result =$XyappModel->get_statistical_kep($uid,$ucode);
            
            $ouput = array(
                'status' => 1,
                'msg' => $result
            );
            
        }else{
            $ouput = array(
                'status' => -2,
                'msg' => 'error'
            );
        }
        echo $this->encode_json($ouput);die();
        
    }
    
    //记录课节报告和讲义点击量
    public function setClick(){
        $XyappModel = D('Xyapp');
        $uid = trim($_GET['uid']);
        $ucode = trim($_GET['ucode']);
        $click = trim($_GET['click']);//kejie  jiangyi 
        if(!empty($uid) && !empty($ucode)&&!empty($click)){
            $result = $XyappModel->set_kjjy_click($uid,$ucode,$click);
            if(!empty($result)){
                $ouput = array(
                    'status' => 1,
                    'msg' => 'ok'
                );
            }else{
                $ouput = array(
                    'status' => -1,
                    'msg' => 'error'
                );
            }
        }else{
            $ouput = array(
                'status' => -2,
                'msg' => 'error'
            );
        }
        echo $this->encode_json($ouput);die();
    }
    
    //统计--讲义、课节报告、课节报告点评
    public function statisticalJike(){
        $XyappModel = D('Xyapp');
        $uid = trim($_GET['uid']);
        $ucode = trim($_GET['ucode']);
        $click = trim($_GET['click']);//kejie  jiangyi dianping
        if(!empty($uid) && !empty($ucode) && !empty($click)){
            $result = $XyappModel->get_statistical_jike($uid,$ucode,$click); 
            
            $ouput = array(
                'status' => 1,
                'msg' => $result
            );
            
        }else{
            $ouput = array(
                'status' => -2,
                'msg' => 'error'                 
            );
        }
        echo $this->encode_json($ouput);die();
    }
    
    //月评价
    public function monthEvaluation(){        
        $XyappModel = D('Xyapp');
        $arr = $_GET;
        
        if($arr){            
            if(!empty($arr['uid']) && !empty($arr['ucode']) && !empty($arr['uname'])){
                $result = $XyappModel->add_month_eval($arr);
                
                if(!empty($result)){
                    $ouput = array(
                        'status' => 1,
                        'msg' => $result
                    );
                }else{
                    $ouput = array(
                        'status' => -1,
                        'msg' => '添加失败'
                    );
                }
            }else{
                 $ouput = array(
                        'status' => -2,
                        'msg' => '参数不对'
                    );
            }
        }else{
            echo "wu";
        }
        echo $this->encode_json($ouput);die();
    }
    
    //月评价--详细
    public function monthEvaluationDetails(){
        $XyappModel = D('Xyapp');
        $arr = $_GET;
        if($arr){            
            if(!empty($arr['uid']) && !empty($arr['ucode']) && !empty($arr['uname'])){
                $result = $XyappModel->get_month_eval_one_list($arr);
                if(!empty($result)){
                    $ouput = array(
                        'status' => 1,
                        'msg' => $result
                    );
                }else{
                    $ouput = array(
                        'status' => -1,
                        'msg' => '内容失败'
                    );
                }
            }else{
                 $ouput = array(
                        'status' => -2,
                        'msg' => '参数不对'
                    );
            }
        }
         echo $this->encode_json($ouput);die();
    }
    
    //月评价-历史评价列表
    public function monthEvaluationList(){
        $XyappModel = D('Xyapp');
        $arr = $_GET;
        if($arr){            
            if(!empty($arr['uid']) && !empty($arr['ucode']) && !empty($arr['uname'])){
                $result = $XyappModel->get_month_eval_list($arr);
                if(!empty($result)){
                    $ouput = array(
                        'status' => 1,
                        'msg' => $result
                    );
                }else{
                    $ouput = array(
                        'status' => -1,
                        'msg' => '内容失败'
                    );
                }
            }else{
                 $ouput = array(
                        'status' => -2,
                        'msg' => '参数不对'
                    );
            }
        }
         echo $this->encode_json($ouput);die();
    }
    
    //月评价--本月份是否评价
    public function getMonEvalStatus(){
        $XyappModel = D('Xyapp');
        $arr = $_GET;
        if($arr){            
            if(!empty($arr['uid']) && !empty($arr['ucode']) && !empty($arr['uname'])){
                $arr['now_time'] = date('Y-m',time());
                $result = $XyappModel->get_mon_eval_status($arr);
                if(!empty($result)){
                    $ouput = array(
                        'status' => 1,
                        'msg' => '有'
                    );
                }else{
                    $ouput = array(
                        'status' => 2,
                        'msg' => '没有'
                    );
                }
            }else{
                 $ouput = array(
                        'status' => -1,
                        'msg' => '参数不对'
                    );
            }
        }
         echo $this->encode_json($ouput);die();
    }
    
    //推荐码
    public function recommendedCode(){
        $XyappModel = D('Xyapp');
        $arr = $_GET;
        if($arr){            
            if(!empty($arr['uid']) && !empty($arr['ucode']) && !empty($arr['uname'])){
                //TJGS000000
                $arr['re_code'] = 'TJGS'.mt_rand(1000000,9999999);                
                $result = $XyappModel->get_recommended_code($arr);
                
                if($result != false){
                    $ouput = array(
                        'status' => 1,
                        'msg' => $result
                    );
                }else{
                    $ouput = array(
                        'status' => -1,
                        'msg' => '内容失败'
                    );
                }
            }else{
                 $ouput = array(
                        'status' => -2,
                        'msg' => '参数不对'
                    );
            }
        }
         echo $this->encode_json($ouput);die();
    }
    
    //填写推荐学员信息    
    public function addRecommendedCode(){
        import ( "COM.MsgSender.XySmsSender" );
        $XyappModel = D('Xyapp');
        $arr = $_GET;
        if($arr){
            if(!empty($arr['uid']) && !empty($arr['ucode']) && !empty($arr['uname'])){
                $arr['create_time'] = date('Y-m-d H:i:s',time());
                $result = $XyappModel->addRecomCode($arr);                
                if($result != false){
                    $mtel = $arr['sphone'];
                    $content = '童鞋您好，您的小伙伴'.$arr['uname'].'在高思1对1推荐您来高思学习，推荐码为'.$arr['urecode'].'，成功报名后有大礼等您哦！具体详情可联系010-56639988进行咨询';                    
		            $sendRs = XySmsSender::sendSms($mtel, $content);                    
                    if (!$sendRs) {
                    	$ouput = (array('status' => -1, 'msg' => '发送失败，请稍后重试！'));
                    }else{                   
                        $ouput = array(
                        'status' => 1,
                        'msg' => 'ok'
                        );                  
                    }
                }else{
                    $ouput = array(#FFFFFF
                        'status' => -2,
                        'msg' => '内容失败'
                    );
                }
            }else{
                 $ouput = array(
                    'status' => -3,
                    'msg' => '参数不对'
                 );
            }
            
        }
        echo $this->encode_json($ouput);die();       
        
    }
    
    //推荐码领取奖励--匹配推荐码是否正确
    public function getRecommendedCode(){
        //recode 推荐码
        $XyappModel = D('Xyapp');
        $arr = $_GET;
        if($arr){
            if(!empty($arr['recode']) ){
                $arr['create_time'] = date('Y-m-d H:i:s',time());
                $result = $XyappModel->getRecomUserInfo($arr);
                if($result != false){
                    $ouput = array(
                        'status' => 1,
                        'msg' => $result
                    );
                }else{
                    $ouput = array(
                        'status' => -1,
                        'msg' => '内容失败'
                    );
                }
            }else{
                $ouput = array(
                    'status' => -2,
                    'msg' => '参数不对'
                );
            }
            
        }
        echo $this->encode_json($ouput);die(); 
    }
    
    //推荐码领取奖励-推荐码正确领取奖励
     public function getRecommendedCodeInfo(){
        //recode 推荐码
        $XyappModel = D('Xyapp');
        $arr = $_GET;
        if($arr){
            if(!empty($arr['uid']) && !empty($arr['ucode']) && !empty($arr['uname']) && !empty($arr['recode'])){
                $arr['create_time'] = date('Y-m-d H:i:s',time());
                $result = $XyappModel->getRecomCodeInfo($arr);                
                if($result == 'ok'){
                    $ouput = array(
                        'status' => 1,
                        'msg' => 'ok'
                    );
                }elseif($result == 'there'){
                    $ouput = array(
                        'status' => 2,
                        'msg' => '已领取过'
                    );
                }else{
                    $ouput = array(
                        'status' => -1,
                        'msg' => '内容失败'
                    );
                }
            }else{
                $ouput = array(
                    'status' => -2,
                    'msg' => '参数不对'
                );
            }
            
        }
        echo $this->encode_json($ouput);die(); 
    }
    
   //李俊强接口 推荐码下所有人员和被推荐人的推荐码
    public function getReCodeList(){
        $XyappModel = D('Xyapp');
        $arr = $_GET;
        if($arr){
            if(!empty($arr['uid']) && !empty($arr['ucode']) && !empty($arr['uname'])){
                $result = $XyappModel->get_re_code_list($arr);
                //var_dump($result);exit;
                if($result != false){
                    $ouput = array(
                        'status' => 1,
                        'msg' => $result
                    ); 
                }else{
                    $ouput = array(
                        'status' => 2,
                        'msg' => '暂无内容'
                    );
                }
            }else{
                $ouput = array(
                    'status' => -2,
                    'msg' => '参数不对'
                );
            }
        }
        echo $this->encode_json($ouput);die();
    }

    //个人推荐记录
    public function getPersonalRecommendedCodeList(){
        $XyappModel = D('Xyapp');
        $arr = $_GET;
        if($arr){            
            if(!empty($arr['uid']) && !empty($arr['ucode']) && !empty($arr['uname'])){
                $result = $XyappModel->get_personal_recommended_code($arr);
                //var_dump($result);exit;
                if($result != false){
                    $ouput = array(
                        'status' => 1,
                        'msg' => $result
                    );
                }else{
                    $ouput = array(
                        'status' => -1,
                        'msg' => '内容失败'
                    );
                }
            }else{
                 $ouput = array(
                        'status' => -2,
                        'msg' => '参数不对'
                 );
            }
        }
         echo $this->encode_json($ouput);die();
    }
    
     //添加用户访问统计
    public function addVisit(){
        $XyappModel = D('Xyapp');
        $arr = $_GET;
        if($arr){
           if(!empty($arr['uid']) && !empty($arr['ucode']) && !empty($arr['uname'])&& !empty($arr['aschool'])&& !empty($arr['mteacher'])&& !empty($arr['sstatus'])){
                $arr['date'] = date('Y-m-d',time());
                $arr['time'] = date('Y-m-d H:i:s',time());
                $result = $XyappModel->add_Visit($arr);
                if($result == '2'){
                    $ouput = array(
                        'status' => 2,
                        'msg' => '已有记录'
                    );
                }else if($result == '3'){
                    $ouput = array(
                        'status' => -1,
                        'msg' => '添加失败'
                    );
                }else if($result == '1'){
                    $ouput = array(
                        'status' => 1,
                        'msg' => '添加成功'
                    );
                }
            }
        }
        echo $this->encode_json($ouput);die();
    }

    //记录每月统计json数据(每月第一天的零点零一分执行记录上月数据)
    public function AddVisitJson(){
        $XyappModel=D('Xyapp');
        //$beginDate = date("Y-m-d",mktime(0, 0 , 0,date("m"),1,date("Y")));
        //$endDate =  date("Y-m-d",mktime(23,59,59,date("m"),date("t"),date("Y")));
        
        $beginDate = date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m")-1,1,date("Y")));
        $endDate = date("Y-m-d H:i:s",mktime(23,59,59,date("m") ,0,date("Y")));
          
        $visitCountLists = $XyappModel->excelVisitInfo($beginDate,$endDate);
        $container = array();
        foreach ($visitCountLists as $item) {
           $key = $item['uid'] . '_' . $item['sstudentcode'] . '_' . $item['sstudentname'] . '_' . $item['manage_teacher'] . '_' . $item['attribute_school'] . '_' . $item['sign_status'];
            $item['number'] = 1;
            if (empty($container[$key])) {
                $container[$key] = $item['number'];
            }
            else {
                $container[$key] += $item['number'];
            }
        }
        foreach ($container as $key => $item) {
            list($uid, $sstudentcode ,$sstudentname,$manage_teacher,$attribute_school,$sign_status) = explode('_', $key);
            $visitCountList[] = array('uid' => $uid, 'sstudentcode' => urlencode($sstudentcode), 'sstudentname' => urlencode($sstudentname), 'manage_teacher' => urlencode($manage_teacher), 'attribute_school' => urlencode($attribute_school), 'sign_status' => urlencode($sign_status), 'number' => $item);
        }
        // 把PHP数组转成JSON字符串
        $year = date("Y",mktime(0, 0 , 0,date("m")-1,1,date("Y")));
        $yue = date("m",mktime(0, 0 , 0,date("m")-1,1,date("Y")));       
        $arr = array();
        $arr['visit_json']= json_encode($visitCountList);
        $arr['file_title'] = $year.'年'.$yue.'月学员app活跃量统计';
        $arr['create_date'] = date('Y-m-d H:i:s',time());
        $result = $XyappModel->add_visit_json($arr);
        return $result;
    }

   public function testVisitJson(){
        $XyappModel=D('Xyapp');
        //$beginDate = date("Y-m-d",mktime(0, 0 , 0,date("m"),1,date("Y")));
        //$endDate =  date("Y-m-d",mktime(23,59,59,date("m"),date("t"),date("Y")));
        //$beginDate = date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m")-1,1,date("Y")));
        //$endDate = date("Y-m-d H:i:s",mktime(23,59,59,date("m") ,0,date("Y")));

        $beginDate = $_GET['start'];
        $endDate = $_GET['end'];
        $visitCountLists = $XyappModel->excelVisitInfo($beginDate,$endDate);
        $container = array();
        foreach ($visitCountLists as $item) {
            $key = $item['uid'] . '_' . $item['sstudentcode'] . '_' . $item['sstudentname'];
            $item['number'] = 1;
            if (empty($container[$key])) {
                $container[$key] = $item['number'];
            }
            else {
                $container[$key] += $item['number'];
            }
        }
        foreach ($container as $key => $item) {
            list($uid, $sstudentcode ,$sstudentname) = explode('_', $key);
            $visitCountList[] = array('uid' => $uid, 'sstudentcode' => urlencode($sstudentcode), 'sstudentname' => urlencode($sstudentname), 'number' => $item);
        }
        // 把PHP数组转成JSON字符串
        $year = date("Y",mktime(0, 0 , 0,date("m")-1,1,date("Y")));
        $yue = date("m",mktime(0, 0 , 0,date("m")-1,1,date("Y")));       
        $arr = array();
        $arr['visit_json']= json_encode($visitCountList);
        $arr['file_title'] = $year.'年'.$yue.'月test学员app活跃量统计';
        $arr['create_date'] = date('Y-m-d H:i:s',time());
        $result = $XyappModel->add_visit_json($arr);
        return $result;
    }

    //用户访问统计列表
    public function getVisitList(){
        $XyappModel = D('Xyapp');
        $arr = $_GET;
        if($arr['start_date'] != '' && $arr['end_date'] != ''){
            $result = $XyappModel->get_visit_list($arr);
            if($result){
                $ouput = array(
                    'status' => 1,
                    'msg' => $result
                );
            }else{
                $ouput = array(
                    'status' => -1,
                    'msg' => '暂无数据'
                );
            }

        }else{
            $ouput = array(
                'status' => -2,
                'msg' => '参数错误'
            );
        }
        echo $this->encode_json($ouput);die();
    }
    
   
    //服务点评
    public function serviceReview(){        
        $XyappModel = D('Xyapp');
        $arr = $_GET;
        if(!empty($arr)){
            $list = $XyappModel->getServiceReviewRow($arr);
            $list_arr = urldecode($list['list']);
            $list_arr = json_decode($list_arr,true);
            if(!empty($list_arr)){
                echo $this->encode_json($list_arr);die();
            }else{
                $name = $XyappModel->getServiceReviewArr();
                echo $this->encode_json($name);die();
            }

        }else{
            $ouput = array(
                'status' => -1,
                'msg' => '参数错误'
            );
            echo $this->encode_json($ouput);die();
        }

    }

    //服务评价添加
    public function addServiceReview(){
        $postArr = $_POST;
        //id,姓名,code,list
        $XyappModel = D('Xyapp');
        if(!empty($postArr)){
            $postArr['create_time'] = date('Y-m-d H:i:s',time());
            $result = $XyappModel->add_service_review($postArr);
            if($result){
                $ouput = array(
                    'status' => 1,
                    'msg' => 'ok'
                );
            }else{
                $ouput = array(
                    'status' => -1,
                    'msg' => '添加失败'
                );
            }
        }else{
            $ouput = array(
                'status' => -2,
                'msg' => '参数错误'
            );
        }
        echo $this->encode_json($ouput);die();

    }

     //微信分享 推荐码地址
    public function getStuReCode(){
        
        $XyappModel = D('Xyapp');
        if(!empty($_GET['uid']) && !empty($_GET['uname']) && !empty($_GET['ucode'])){
            $stuReInfo = $XyappModel->ins_scode($_GET['uid'],$_GET['ucode'],$_GET['uname']);
            $recommended_code = $stuReInfo[0]['recommended_code'];
            //print_r($stuReInfo);exit;
        }else{
            $recommended_code = '暂无推荐码';
        }

        $this->assign(get_defined_vars());
        $this->display();


    }

        
    
 //=====================sofiaend========================================   
 


     protected function encode_json($str) {  
        return urldecode(json_encode($this->url_encode($str)));      
    }


    protected function url_encode($str) {  
     if(is_array($str)) {  
        foreach($str as $key=>$value) {  
            $str[urlencode($key)] = $this->url_encode($value);  
            }  
        } else {  
            $str = urlencode($str);  
        }  
      
     return $str;  
    }    
}
?>
