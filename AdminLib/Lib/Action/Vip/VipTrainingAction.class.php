<?php
/*后台VIP老师系统---精英计划-培训管理*/
class VipTrainingAction extends VipCommAction{
	
    //人员管理
	public function people(){
		//echo "人员管理";exit;
          $dictInfo = D ( 'Training' )->get_dictList();//学科列表
        $this->assign(get_defined_vars());
         $this->display ();        
	}
    
    //PPT管理
    public function powerPoint(){
		//echo "PPT管理";exit;
		$this->display();
	}
    
    //排课管理
    public function arranging(){
		//echo "排课管理";exit;
        $year = date('Y');
		$month = date('n');
		$day = date('j');
		$now = date('Y-m-d H:i:s');
        $userInfo = $this->loginUser->getInformation();
		$this->assign(get_defined_vars());
		$this->display();
	}
    
    //测试管理
	public function test(){
		//echo "测试管理";exit;        
		$this->assign(get_defined_vars());
		$this->display();
	}
    
    //作业管理
    public function homework(){
        //作业管理
        $this->assign(get_defined_vars());
		$this->display();
	}    
    
    //签到管理
    public function signList(){
		//echo "签到管理";exit;        
        $this->assign(get_defined_vars());
		$this->display();
	}
    
    //排课管理列表
    public function signArrangingInfo(){        
        $this->outPut(D('Training')->getSignArrangingInfo());
    }
    
    //培训管理列表
    public function peopleList(){
        $this->outPut(D('Training')->getTraining());
    }
    
    //PPT管理列表
    public function powerPointInfo(){
         $this->outPut(D('Training')->getPoperPointList());
    }
    
    //测试管理列表
    public function testInfo(){        
         $this->outPut(D('Training')->getTestList());
    }
    
    //作业管理列表
    public function homeworkInfo(){
        $this->outPut(D('Training')->getHomeworkList());
    }
    
    //培训--添加
   	public function addPeople() {
   	    $paperInfo = array();
   	    $levelList = array();
        $TrainingModel = D('Training');
        if(!empty($_GET['id'])){
			$paperInfo = $TrainingModel->get_onepaper($_GET['id']);
			$levelList = unserialize($paperInfo['tr_audit_str']);
		}
		$this->assign(get_defined_vars());
		$this->display ();
	}
    //处理培训-添加-编辑
    public function dict_add_people() {          
      	 if($_POST){
			$arr = $this->_post ();
			for($i=0;$i<$_POST['level_num'];$i++){
				if(!empty($_POST['time'][$i])){
					$arr['level_arr'][$i]['time'] = SysUtil::safeString($_POST['time'][$i]);
					$arr['level_arr'][$i]['long'] = abs($_POST['long'][$i]);
					$arr['level_arr'][$i]['score'] = abs($_POST['score'][$i]);
					
				}
			}
			$arr['level_str'] = serialize($arr['level_arr']);
			unset($arr['time']);
			unset($arr['time']);
			unset($arr['long']);
			unset($arr['score']);
            $arr['create_time']= date('Y-m-d H:i:s',time());
            $userInfo = $this->loginUser->getInformation();            
	        $arr['create_name'] = $userInfo['user_name'];            
            $TrainingModel = D('Training');
			if($_POST['id']){
				$result = $TrainingModel->editPaper($arr);
				$operate = '编辑';
			}else{
				$result = $TrainingModel->addPeople($arr);
				$operate = '添加';
			}
			if($result==true){
				$this->success('培训'.$operate.'成功');
			}else{
				$this->error('培训'.$operate.'失败');
			}    
			
        }else{
			$this->error('非法操作');
		}
        
    }
    //培训-删除
    public function dict_delete() {
        
		$params = $this->_post ();		
        $id = $params ['id'];   
             
		$rs = D ( 'Training' )->deleteDictByID ( $id );
		if ($rs) {
			$this->success ($rs);
		}
		$this->error ();
	}
    //教师-科目筛选
    public function searchDict(){
        $id = $_GET['id'];

        $dictInfo = D ( 'Training' )->get_dictList();
        $this->assign(get_defined_vars());
        $this->display();
    }
    //添加-培训老师 
    public function addTeach(){
        $id = $_GET['id'];  
        $dictInfo = D ( 'Training' )->get_dictList();            
        $this->assign(get_defined_vars());
        $this->display ();
    }
    //处理添加培训老师
    public function dict_add_teach(){        
         if($_POST){
			$arr = $this->_post ();
			$arr['create_time']= date('Y-m-d H:i:s',time());
            $userInfo = $this->loginUser->getInformation();            
	        $arr['create_name'] = $userInfo['user_name']; 
            if(!empty($arr['contain_module'])){
				foreach($arr['contain_module'] as $k=>$v){
					$arr['module_name'] .= trim($v).',';
				}
			//	$arr['module_name'] = trim($arr['module_name'],',');
			}
			$arr['xueke'] = $arr['module_name'];  
                     
             
            $bir = str_replace('-','',substr($arr['birthday'],5));
            $pho = substr($arr['phone'],7);     
            $arr['password'] = $bir.$pho; 
            $TrainingModel = D('Training');			
			$result = $TrainingModel->addTeache($arr);
			$operate = '添加';		
			if($result==true){
				$this->success('老师'.$operate.'成功');
			}else{
				$this->error('老师'.$operate.'失败');
			}    
			
        }else{
			$this->error('非法操作');
		}
    }
    
    public function check_code() {
		ob_clean ();
		$post = $this->_post ();
		$params = $this->_get ();
		$tr_name = $post ['tr_name'];
		$row = D ( 'Training' )->getDictByCategoryAndCode ( $tr_name );
		if ($row) {
			echo 'false';
		} else {
			echo 'true';
		}
	}
    
    public function check_phone(){
        ob_clean ();
		$post = $this->_post ();
		$params = $this->_get ();
        $phone = $post ['phone'];
		$row = D ( 'Training' )->getDictByTeachAndPhone ( $phone );
		if ($row) {
			echo 'false';
		} else {
			echo 'true';
		}
    }
    
     public function getDictsByCategory() {
		$params = $this->_param ();
         if($_POST){
             $arr = $this->_post ();
             $category = $arr ['tr_id'];
         }else {
             $xuekeid = $params['xkid'];
             $category = $params ['id'];
         }
         $currentPage = $params ['page'];
         $pageSize = $params ['rows'];
         $sort = $params ['sort'];
         $order = $params ['order'];
		$this->outPut ( D ( 'Training' )->getDictsByCategory ( $category, $currentPage, $pageSize, $sort, $order ,$xuekeid) );
	}
    //修改-培训-老师
    public function editTeach() {
		$params = $this->_get ();
		$id = $params ['id'];
        $dictInfo = D ( 'Training' )->get_dictList();
        
		$teachInfo = D ( 'Training' )->getTeachByID ( $id );
        $contain_module = explode(',',trim($teachInfo['xueke']));
		$this->assign ( get_defined_vars () );
		$this->display ();
	}
    //处理修改培训老师
	public function dict_edit_teach() {
		if($_POST){
		  $arr = $this->_post ();
            $arr['update_time']= date('Y-m-d H:i:s',time());                
            if(!empty($arr['contain_module'])){
    			foreach($arr['contain_module'] as $k=>$v){
    					$arr['module_name'] .= trim($v).',';
    			}

				//$arr['module_name'] = trim($arr['module_name'],',');
			}

			$arr['xueke'] = $arr['module_name'];  
            $bir = str_replace('-','',substr($arr['birthday'],5));
            $pho = substr($arr['phone'],7);     
            $arr['password'] = $bir.$pho;            
    		$result = D ( 'Training' )->editTeach ( $arr );
    		$operate = '编辑';		
			if($result==true){
                $teInfo = D('Training')->getTeachInfo($arr['te_id']);               
                if($arr['through'] != '' && $teInfo[0]['through'] != 0 ){                    
                    //审核是否通过-调用微信推送接口===start=============================
                    $url = "http://jyjh.gaosiedu.com/weixin/jingying-Push-examine?appName=JingYing&appLevel=production";
                    //$url = "http://wxhr.tunnel.qydev.com/weixin/weixin/jingying-Push-examine?appName=JingYing&appLevel=production";
                    $post_data = array('te_id'=>$arr['te_id'],'te_name'=>$teInfo[0]['te_name'],'tr_time'=>$teInfo[0]['trInfo']['tr_start_time']." — ".$teInfo[0]['trInfo']['tr_end_time'],'through'=>$teInfo[0]['through']);
                    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 1);// post数据
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);// post的变量
                    $output = curl_exec($ch);                    
                    curl_close($ch);
                    //微信推送接口===end==============================
                }else{
                    //echo $arr['through'].'---*--'.$teInfo[0]['through'];exit;
                }


				$this->success('老师'.$operate.'成功');
			}else{
				$this->error('老师'.$operate.'失败');
			}
        }else{
			$this->error('非法操作');
		}
	}
    //培训-老师-删除
    public function delTeach() {
        
		$params = $this->_post ();		
        $id = $params ['id'];
        $trid = $params['trid'];                
		$rs = D ( 'Training' )->deleteTeachByID ( $id ,$trid);
		if ($rs) {
			$this->success ();
		}
		$this->error ();
	}
    
    //培训-老师-批量评语
    public function addTraKaoping(){
        $params = $this->_get ();
		$id = $params ['id'];       
                 
		$trInfo = D('Training')->get_onepaper($id);
        $dictInfo = D ( 'Training' )->get_dictList();//学科列表
		
        $this->assign ( get_defined_vars () );
		$this->display ();
    }
    //处理批量评语
    public function dict_add_trakaoping(){
        if($_POST){
		  $arr = $this->_post ();          
            $arr['create_time']= date('Y-m-d H:i:s',time());
            $userInfo = $this->loginUser->getInformation();
			$arr['create_name'] = $userInfo['user_name'];
            if(!empty($arr['contain_module'])){
    			foreach($arr['contain_module'] as $k=>$v){
    					$arr['module_name'] .= trim($v).',';
    			}
				//$arr['module_name'] = trim($arr['module_name'],',');
			}
			$arr['xueke'] = $arr['module_name'];            
    		$result = D ( 'Training' )->addTraKaoPing ( $arr );
            $operate = '添加';
            if($result == 'wu'){
                $this->error('此次培训没有此学科老师！');
            }elseif($result == 'ok'){
				$this->success('点评'.$operate.'成功');
			}elseif($result == false){
				$this->error('点评'.$operate.'失败');
			}
        }else{
			$this->error('非法操作');
		}
    }
    
    //老师--考评记录
    public function addKaoPing(){

        $te_id = $_GET['id'];     
        //echo $te_id;exit;   
        $teInfo = D('Training')->getKaopingOneList($te_id);
       // var_dump($teInfo);exit;
        $levelList = unserialize($teInfo['tr_audit_str']);
        $xuekeList = D('Training')->getXueKeInfo($teInfo['xueke']);
        $this->assign ( get_defined_vars () );
		$this->display ();
    }
    //处理考评记录
    public function dict_add_kaoping(){
       // var_dump($_POST);exit;
        if($_POST){
		  $arr = $this->_post ();
            $arr['create_time']= date('Y-m-d H:i:s',time());
            $userInfo = $this->loginUser->getInformation();
			$arr['create_name'] = $userInfo['user_name'];  
            $timeci = explode('_',$arr['time']);
            $arr['time'] = $timeci[0];
            $arr['ci'] = $timeci[1];
    		$result = D ( 'Training' )->addReviewRecords ( $arr );
    		$operate = '添加';		
			if($result==true){
                //print_r($teInfo);exit;
                if($arr['create_name'] != '' && $arr['time'] != '' && $arr['ci'] != '' && $arr['fenshu'] != '' && $arr['paiming'] != '' && $arr['fen_pingyu'] != ''){
                    //审核是否通过-调用微信推送接口===start=============================
                    $url = "http://jyjh.gaosiedu.com/weixin/jingying-Push-Examination?appName=JingYing&appLevel=production";
                    //$url = "http://wxhr.tunnel.qydev.com/weixin/weixin/jingying-Push-Examination?appName=JingYing&appLevel=production";
                    $trInfo = D('Training')->getTrainingId($arr['tr_id']);
                    $post_data = array(
                        'te_id'=>$arr['id'],
                        'tr_name'=>$trInfo,
                        'time'=>$arr['time'],
                        'ci'=>$arr['ci'],
                        'fenshu'=>$arr['fenshu'],
                        'paiming'=>$arr['paiming'],
                        'fen_pingyu'=>$arr['fen_pingyu']
                    );
                    //print_r($post_data);exit;
                    $ch = curl_init();                    
                    curl_setopt($ch, CURLOPT_URL, $url);                    
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);                    
                    curl_setopt($ch, CURLOPT_POST, 1);// post数据                   
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);// post的变量 
                    $output = curl_exec($ch);                   
                    curl_close($ch);
                    //微信推送接口===end==============================
                }else{
                    //echo $arr['through'].'---*--'.$teInfo[0]['through'];exit;
                }


				$this->success('点评'.$operate.'成功');
			}else{
				$this->error('点评'.$operate.'失败');
			}
        }else{
			$this->error('非法操作');
		}
    }
    //老师--详情
    public function detailsList(){
        
        $te_id = $_GET['id'];        
        $teachInfo = D('Training')->getTeachInfo($te_id);//老师信息
        $fenInfo =D('Training')->getFenInfo($te_id);//分数
        $pingjiaInfo = D('Training')->getReviewRecords($te_id);//评语 
        //var_dump($pingjiaInfo);exit;
        $this->assign ( get_defined_vars () );
		$this->display ();
        
    }
    
    //PPT管理-添加-编辑
    public function addPowerPoint(){       
        $powerPointInfo = array();
        $contain_module = array();
        $trNameInfo = D('Training')->getTrNameInfo();//培训名称列表
        $dictInfo = D ( 'Training' )->get_dictList();//学科列表
        $id = $_GET['id'];        
        if(!empty($_GET['id'])){
            $powerPointInfo =D('Training')->getPoperPointList($_GET['id']); 
            $pptFile = D('Training')->getPptFile($powerPointInfo[0]['id']); 
            $contain_module = explode(',',trim($powerPointInfo[0]['xueke']));
        }
        
        $this->assign ( get_defined_vars () );
		$this->display ();
    }
    //处理添加编辑PPT
    public function dict_add_powerpoint(){
        
        if($_POST){
            $arr = $_POST;
            //var_dump($arr);exit;            
            $arr['create_time']= date('Y-m-d H:i:s',time());
            $userInfo = $this->loginUser->getInformation();            
	        $arr['create_name'] = $userInfo['user_name']; 
            $tr_name = explode('_U_',$arr['tr_name']);
            $arr['tr_id'] = $tr_name[1];
            if(!empty($arr['contain_module'])){
    			foreach($arr['contain_module'] as $k=>$v){
    					$arr['module_name'] .= trim($v).',';
    			}

				//$arr['module_name'] = trim($arr['module_name'],',');
			}
			$arr['xueke'] = $arr['module_name']; 
            //批量上传图片            
            $filelist=$_FILES["filelist"]; 
            if ($filelist){ 
                $Path="../Upload/".date('Y-m-d')."/"; 
                if (!is_dir($Path)){ //创建路径 
                    mkdir($Path,0777);                    
                } 
                 $upFileNameList = '';
                 for ($i=0;$i<count($filelist);$i++){ 
                    //$_FILES["filelist"]["size"][$i]的排列顺序不可以变，因为fileist是一个二维数组 
                     if ($_FILES["filelist"]["size"][$i]!=0){ 
                         $File=$Path.time()."_".$arr['id']."_".$_FILES["filelist"]["name"][$i]; 
                         if (move_uploaded_file($_FILES["filelist"]["tmp_name"][$i],$File)){
                            $upFileNameList .= $File.',';
                            //echo "文件上传成功 文件类型:".$_FILES["filelist"]["type"][$i]." "."文件名:" 
                            //.$_FILES["filelist"]["name"][$i]."<br>"; 
                         }else{ 
                            $this->error("文件名:".$_FILES["filelist"]["name"][$i]."上传失败</br>"); 
                         } 
                     } 
                 }
                 
                 $arr['upfile_name'] = $upFileNameList;                    
                      
                  
               }
                
                if($_POST['id']){                
                    
    				$result = D('Training')->editPowerPoint($arr);
    				$operate = '编辑';
    			}else{    			 
    				$result = D('Training')->addPowerPoint($arr);
    				$operate = '添加';
    			}
    			if($result==true){
    				$this->success('PPT图片'.$operate.'成功');
    			}else{
    				$this->error('PPT图片'.$operate.'失败');
    			}  
            
            
        }else{
			$this->error('非法操作');
		}
    }
    //PPT管理-删除
    public function delPowerPoint(){
        $params = $this->_post ();		
        $id = $params ['id'];        
		$rs = D ( 'Training' )->deletePowerPointByID ( $id );
		if ($rs) {
			$this->success ();
		}
		$this->error ();
    }
    
    //PPT管理-删除单个图片
    public function delPptFile(){        
        if($_POST){
            $arr = $_POST;
            $result = D('Training')->delPptFileId($arr);
            if($result){
                $status = 1;
                $msg = '删除成功';
            }else{
                $status= -1;
                $msg='删除失败';
            }
            
        }
       	echo json_encode(array('status'=>$status,'msg'=>$msg)); 
    }
    
    //PPT管理-批量上传图片
    public function addPowerPointImg(){
        $params = $this->_get ();		
        $id = $params ['id'];
        $this->assign ( get_defined_vars () );
		$this->display ();
    }
    //处理上传图片PPT
    public function dict_add_powerpoint_img(){
        $id = $_POST['id'];        
        $filelist=$_FILES["filelist"];
         
        if ($_POST["submitfile"]!=""){ 
            $Path="../Upload/".date('Ymh')."/"; 
            if (!is_dir($Path)){ //创建路径 
                mkdir($Path); 
            } 
             $upFileNameList = '';
             for ($i=0;$i<count($filelist);$i++){ 
                //$_FILES["filelist"]["size"][$i]的排列顺序不可以变，因为fileist是一个二维数组 
                 if ($_FILES["filelist"]["size"][$i]!=0){ 
                     $File=$Path.time()."_".$id."_".$_FILES["filelist"]["name"][$i]; 
                     if (move_uploaded_file($_FILES["filelist"]["tmp_name"][$i],$File)){
                        $upFileNameList .= $File.',';
                        //echo "文件上传成功 文件类型:".$_FILES["filelist"]["type"][$i]." "."文件名:" 
                        //.$_FILES["filelist"]["name"][$i]."<br>"; 
                     }else{ 
                        $this->error("文件名:".$_FILES["filelist"]["name"][$i]."上传失败</br>"); 
                     } 
                 } 
             }
             echo $upFileNameList;exit;               
         }  
    }
    

     //获取文件类型后缀 
    function extend($file_name){ 
         $extend = pathinfo($file_name); 
         $extend = strtolower($extend["extension"]); 
         return $extend; 
     } 
    //排课-列表
    public function getArrangingList(){        
        $userInfo = $this->loginUser->getInformation();
		$start = intval($_GET['start']);
		$end = intval($_GET['end']);
		$date = SysUtil::safeString ( $_POST ['date'] );
		if(!empty($date)){
			$start = strtotime($date);
			$end = $start + 3600 * 24;
		}
		if (strlen($start) < 10 || $end < $start) {
			die (json_encode(array()));
		}
		if($_GET['aranging']=='month'){
			$year = date('Y',$start+3600*24*10);
			$month = date('m',$start+3600*24*10);
			$start = strtotime($year."-".$month."-01");
			$end = strtotime($year."-".$month."-".date('t',$start+3600*24*10)." 23:59:59");
		}
        
        $eventsText = '';
        $arrangingResult = D('Training')->getArranging($userInfo['user_name'],$start,$end); 
               
        if(!empty($arrangingResult)){
            foreach($arrangingResult as $key=>$val){
                if($val){
                    $array = array(
                        'id'=>$val['id'],
                        'title' => $val['ar_name'],
                        'ar_teacher' => $val['ar_teacher'],
                        'class_address' =>$val['class_address'],
                        'bgcolor'=>'bg-yellow',
                        'start' => $val['ar_start_time'],
	                    'end' => $val['ar_end_time'],
                        'allDay' => false,
                    );
                    $array ['dateReal'] = date('Y-m-d',strtotime($val['ar_start_time'])); 
                    $array ['classTimeCir'] = date('H:i',strtotime($val['ar_start_time'])).'~'.date('H:i',strtotime($val['ar_end_time']));
                   	$array ['now'] = date('Y-m-d H:i',time());
					$array ['max'] = date('Y-m-d 23:00:00');
                    
                    $xuekeList = D('Training')->getXueKeInfo($val['xueke']);
                    $array ['xueke'] = rtrim($xuekeList,',');
                    
                    $trNameInfo = D('Training')->getTrainingId($val['tr_id']);
                    $array ['tr_name'] = $trNameInfo;     
                    $array ['tr_id'] = $val['tr_id'];
                    $array['week_start'] = $start;
                    $array['week_end'] = $end;
                }
                
                $arr_lessonSchedule [] = $array;
                
            }            
           
        }
        die  (json_encode($arr_lessonSchedule));
       
    }
    
    //排课-我的课表-添加课表
    public function getArrTraList(){
       
        $trainingHtml = '';//培训名称 
        $xuekeHtml = '';
        $trNameInfo = D('Training')->getTrNameInfo();//培训名称列表
        $trainingHtml .= '<select id="tr_name" name="tr_name" class="w300"><option value="">请选择培训期</option>';
        foreach($trNameInfo as $key=>$val){
            $trainingHtml .= '<option id="trname" value="'.$val['tr_name'].'_U_'.$val['id'].'">'.$val['tr_name'].'</option>';
        }
        $trainingHtml .= '</select>';
        
        $dictInfo = D ( 'Training' )->get_dictList();//学科列表
        $xuekeHtml .='<input type="checkbox" id="selAll" onclick="selectAll();" />全选&nbsp;&nbsp;<span style="color: red;">如果选择全部学科，请一定要勾选全选按钮！</span><br>';
        foreach($dictInfo as $key=>$val){
            $xuekeHtml.='<span id = "span_module_'.$val['id'].'"><input id="module_'.$val['id'].'" type="checkbox" name ="contain_module[]" value="'.$val['id'].'" size="10">'.$val['nianji'].$val['title'].'</span>';           
        }
       
        //var_dump($trainingHtml);exit;
         echo json_encode(array('trainingHtml'=>$trainingHtml,'xuekeHtml'=>$xuekeHtml));
         
         
    }
    //排课-处理添加课表
    public function addKecheng(){        
        if($_POST){
            $arr = $this->_post();            
            $arr['create_time']= date('Y-m-d H:i:s',time()); 
            $userInfo = $this->loginUser->getInformation();            
            if($arr['start']<=date('Y-m-d H:i:s')){
                $status = 0;
                $this->error('添加失败，选择的时间已过期，请选择有效的时间段');
            }elseif($arr['tr_name'] == ''){
                $status = 0;
                $this->error('添加失败，请选择培训期');
            }elseif($arr['contain_module'] == ''){
                $status = 0;
                $this->error('添加失败，请选择学科');
            }else{
                if(date('Y-m-d',strtotime($arr['start']))==date('Y-m-d',strtotime($arr['end']))){
                    if(!empty($arr['contain_module'])){
                        
            		      foreach($arr['contain_module'] as $k=>$v){
            					$arr['module_name'] .= trim($v).',';
            		      }
        			}        
        			$arr['xueke'] = $arr['module_name'];
                    $arr['create_name'] = $userInfo['user_name'];
                    $trInfo = explode('_U_',$arr['tr_name']);
                    $arr['tr_id'] = $trInfo[1]; 
                    $result = D('Training')->addArranging($arr);                    
                    $operate = '添加';		
        			if($result==true){
        				$this->success('课程'.$operate.'成功');
        			}else{
        				$this->error('课程'.$operate.'失败');
        			}  
                }else{
					$status = 0;
					$this->error('上课时间不能跨日期');
				}
            }
            
        }else{
			$this->error('非法操作');
		}
        
          
    }
    
    //排课--调课
	protected function adjustKecheng(){
	    //var_dump($_POST);        
        $userInfo = VipCommAction::get_currentUserInfo();
        //var_dump($userInfo);exit;
        $kechengInfo = D('Training')->getArrangingInfoId($_POST['id']);        
		$kechengInfo['ar_start_time'] = $_POST['start'];
		$kechengInfo['ar_end_time'] = $_POST['end'];
		if($_POST['start']<=date('Y-m-d H:i:s')){
			$status = 0;
			$msg = '选择的时间已过期，请选择有效的时间段';
		}else{                
            $param = $_POST;
            $param['create_name']=$userInfo['user_name'];
            $param['create_time']=date('Y-m-d H:i:s',time());
			
			$result = D('Training')->editArrangingId($param);
			
			if($result){
				$status = 1;
				$msg = '调课成功';
			}else{
				$status = 0;
				$msg = '调课失败';
			}
			
		}
        
       	echo json_encode(array('status'=>$status,'msg'=>$msg));        
        
	}
    
    //排课--删除课程    
    protected function deljust_kecheng(){
        if($_POST){
            $arr = $_POST;
            $result = D('Training')->delArrangingId($arr);
            if($result){
                $status = 1;
                $msg = '删除课程成功';
            }else{
                $status = 0;
                $msg = '删除课程失败';
            }
        }
        echo json_encode(array('status'=>$status,'msg'=>$msg));
    }
    
    
    //培训-测试管理-添加-修改
    public function addTest(){
        
        $powerPointInfo = array();
        $contain_module = array();
        $trNameInfo = D('Training')->getTrNameInfo();//培训名称列表
        $dictInfo = D ( 'Training' )->get_dictList();//学科列表
        $zujuanInfo = D('Training')->getZuJuanList();//组卷        
        if(!empty($_GET['id'])){
            $testInfo =D('Training')->getTestOneList($_GET['id']);
            $contain_module = explode(',',trim($testInfo['xueke']));
            $zujuan_module = explode(',',trim($testInfo['zujuan']));
            
        }
        $this->assign ( get_defined_vars () );
		$this->display ();
    }
    //处理添加修改测试
    public function dict_add_test(){
        if($_POST){
            $arr = $this->_post();            
            if(!empty($arr['contain_module'])){                        
		      foreach($arr['contain_module'] as $k=>$v){
					$arr['module_name'] .= trim($v).',';
		      }        
		      //$arr['module_name'] = trim($arr['module_name'],',');
        	}
            $arr['xueke'] = $arr['module_name'];
            if(!empty($arr['zujuan_module'])){                        
		      foreach($arr['zujuan_module'] as $k=>$v){
					$arr['zujuan_name'] .= trim($v).',';
		      }        
		      $arr['zujuan_name'] = trim($arr['zujuan_name'],',');
        	}
            $arr['zujuan'] = $arr['zujuan_name'];
            
            $arr['create_time']= date('Y-m-d H:i:s',time()); 
            $userInfo = $this->loginUser->getInformation();
            $arr['create_name'] = $userInfo['user_name'];
            $trInfo = explode('_U_',$arr['tr_name']);
            $arr['tr_id'] = $trInfo[1]; 
            if($_POST['id']){
                
				$result = D('Training')->editTestManagement($arr);
				$operate = '编辑';
			}else{
				$result = D('Training')->addTestManagement($arr);
				$operate = '添加';
			}
			if($result==true){
				$this->success('测试'.$operate.'成功');
			}else{
				$this->error('测试'.$operate.'失败');
			} 
        }
    }
    //培训-测试管理-删除
    public function delTest(){
        $params = $this->_post ();		
        $id = $params ['id'];        
		$rs = D ( 'Training' )->deleteTestByID ( $id );
		if ($rs) {
			$this->success ();
		}
		$this->error ();
    }
    //培训-测试管理-详细查看
    public function testDetailedList(){
        
        $id = $_GET['id'];        
        $teachInfo = D('Training')->getTestDetailedInfo($id);//老师信息
        
        $this->assign ( get_defined_vars () );
		$this->display ();
        
    }
    //培训-作业管理-添加-修改
    public function addHomework(){
        $powerPointInfo = array();
        $contain_module = array();
        $trNameInfo = D('Training')->getTrNameInfo();//培训名称列表
        $dictInfo = D ( 'Training' )->get_dictList();//学科列表
        $zujuanInfo = D('Training')->getZuJuanList();//组卷        
        if(!empty($_GET['id'])){
            $homeworkInfo =D('Training')->getHomeworkOneList($_GET['id']);
            $contain_module = explode(',',trim($homeworkInfo['xueke']));
            $zujuan_module = explode(',',trim($homeworkInfo['zujuan']));
            
        }
        $this->assign ( get_defined_vars () );
		$this->display ();
    }
    //处理添加编辑作业
    public function dict_add_homework(){
        if($_POST){
            $arr = $this->_post();            
            if(!empty($arr['contain_module'])){                        
		      foreach($arr['contain_module'] as $k=>$v){
					$arr['module_name'] .= trim($v).',';
		      }        
		      //$arr['module_name'] = trim($arr['module_name'],',');
        	}
            $arr['xueke'] = $arr['module_name'];
            if(!empty($arr['zujuan_module'])){                        
		      foreach($arr['zujuan_module'] as $k=>$v){
					$arr['zujuan_name'] .= trim($v).',';
		      }        
		      $arr['zujuan_name'] = trim($arr['zujuan_name'],',');
        	}
            $arr['zujuan'] = $arr['zujuan_name'];
            
            $arr['create_time']= date('Y-m-d H:i:s',time()); 
            $userInfo = $this->loginUser->getInformation();
            $arr['create_name'] = $userInfo['user_name'];
            $trInfo = explode('_U_',$arr['tr_name']);
            $arr['tr_id'] = $trInfo[1]; 
            if($_POST['id']){
				$result = D('Training')->editHomework($arr);
				$operate = '编辑';
			}else{
				$result = D('Training')->addHomework($arr);
				$operate = '添加';
			}
			if($result==true){
				$this->success('作业'.$operate.'成功');
			}else{
				$this->error('作业'.$operate.'失败');
			} 
        }
    }
    //培训-作业管理-删除
    public function delHomework(){
        $params = $this->_post ();		
        $id = $params ['id'];        
		$rs = D ( 'Training' )->deleteHomeworkByID ( $id );
		if ($rs) {
			$this->success ();
		}
		$this->error ();
    }
    //培训-作业管理-详细查看
    public function homeworkDetailedList(){        
        $id = $_GET['id'];        
        $teachInfo = D('Training')->getHomeworkDetailedInfo($id);//作业答题信息        
        $this->assign ( get_defined_vars () );
		$this->display ();
        
    }
    
    //签到列表
    /*public function getTeachSignList($id){
        $params = $this->_param ();
		$currentPage = $params ['page'];
		$pageSize = $params ['rows'];
		$sort = $params ['sort'];
		$order = $params ['order'];
		$category = $params ['id'];

		$this->outPut ( D ( 'Training' )->getTeachSignOneInfo ( $category, $currentPage, $pageSize, $sort, $order ) );
    }*/
    public function getTeachSignList($id,$kename='',$tename='',$tetime=''){        
        $params = $this->_param ();
		$currentPage = $params ['page'];
		$pageSize = $params ['rows'];
		$sort = $params ['sort'];
		$order = $params ['order'];
		$category = $params ['id'];
        
        $this->outPut (  D ( 'Training' )->getTeachSignOneInfo ( $category, $currentPage, $pageSize, $sort, $order ,$params) );    
        
		
    }
    
    // 签到--查询
    public function signSearch(){
        $arr = $_POST;
        if($arr){
            $result = D('Training')->getSignSearchNameDate($arr);
            if($result){
                $status = 1;
                $msg = $result;
            }
            
        }  
        
         echo json_encode(array('status'=>$status,'msg'=>$msg)); 
    }
    
    
    //导出培训期老师信息名单
	public function exportTeachListExcel(){
	    $params = $this->_param ();
        $trInfo = D('Training')->get_onepaper($params['id']);        			
        $list = D('Training')->getExportTeachList($params['id']);
        //var_dump($list);exit;
		import("ORG.Util.Excel");
		$exceler = new Excel_Export();
		$fileTitle = $trInfo['tr_name'].'——培训老师列表';
		$dotype_name = mb_convert_encoding($fileTitle,'gbk','utf8');
		$exceler->setFileName($dotype_name.date('Y-m-d',time()).'.csv');
		$excel_title= array('编号','姓名', '性别','生日','毕业学校','专业','最高学历','毕业年份','电话','邮箱','学科','性质','是否通过','关闭账号');        
		foreach ($excel_title as $key=>$title){
			$excel_title[$key] = mb_convert_encoding($title,'gbk','utf8');
		}
		$exceler->setTitle($excel_title);
		foreach ($list as $key=>$val){		
				$tmp_data= array($val['id'],mb_convert_encoding($val['te_name'],'gbk','utf8'),mb_convert_encoding($val['sex_name'],'gbk','utf8'),mb_convert_encoding($val['birthday'],'gbk','utf8'),mb_convert_encoding($val['school'],'gbk','utf8'),mb_convert_encoding($val['professional'],'gbk','utf8'),mb_convert_encoding($val['level_school'],'gbk','utf8'),mb_convert_encoding($val['graduation'],'gbk','utf8'),mb_convert_encoding($val['phone'],'gbk','utf8'),mb_convert_encoding($val['mail'],'gbk','utf8'),mb_convert_encoding($val['xueke_name'],'gbk','utf8'),mb_convert_encoding($val['formal_name'],'gbk','utf8'),mb_convert_encoding($val['through_name'],'gbk','utf8'),mb_convert_encoding($val['status_name'],'gbk','utf8'));
			
			$exceler->addRow($tmp_data);
		}
		$exceler->export();
	}
    //导出签到表
    public function exportSignListExcel(){
        $params = $this->_param ();        
        $trInfo = D('Training')->get_onepaper($params['id']);        			
        $list = D('Training')->getExportSignList($params['id']);
        //var_dump($list);exit;
		import("ORG.Util.Excel");
		$exceler = new Excel_Export();
		$fileTitle = $trInfo['tr_name'].'——老师签到表';
		$dotype_name = mb_convert_encoding($fileTitle,'gbk','utf8');
		$exceler->setFileName($dotype_name.date('Y-m-d',time()).'.csv');
		$excel_title= array('编号','培训期名称', '姓名','状态','签到日期','签到时间','上课时间');        
		foreach ($excel_title as $key=>$title){
			$excel_title[$key] = mb_convert_encoding($title,'gbk','utf8');
		}        
		$exceler->setTitle($excel_title);
		foreach ($list as $key=>$val){		
				$tmp_data= array($val['id'],mb_convert_encoding($val['tr_name'],'gbk','utf8'),mb_convert_encoding($val['te_name'],'gbk','utf8'),mb_convert_encoding($val['recommended_name'],'gbk','utf8'),mb_convert_encoding($val['create_date'],'gbk','utf8'),mb_convert_encoding($val['create_time'],'gbk','utf8'),mb_convert_encoding($val['shangke'],'gbk','utf8'));
			
			$exceler->addRow($tmp_data);
		}
		$exceler->export();
    }
    
    
	//导出错题书包
	public function exportTeachListWord(){
	   
		ob_start();
        $te_id = $_GET['id'];  
        //echo $te_id;exit;      
        $teachInfo = D('Training')->getTeachInfo($te_id);//老师信息
        $fenInfo =D('Training')->getFenInfo($te_id);//分数
        $pingjiaInfo = D('Training')->getReviewRecords($te_id);//评语 
        
		$html = '<div id="list" class="clearfix" style="padding:20px 30px">';
		if(!empty($teachInfo)){
		  $html .='<table>';
			foreach ($teachInfo as $key=>$val){
				
				$html .= '<tr>
					<td class="alt left">姓名：</td>
					<td>
					<span>'.$val['te_name'].'</span>
					</td>
                    	<td class="alt left">性别：</td>
					<td>
						<span>'.$val['sex_name'].'</span>
					</td>
				</tr>			
				<tr>
					<td class="alt left">毕业学校：</td>
					<td>
						<span>'.$val['school'].'</span>
					</td>
                    <td class="alt left">专业：</td>
					<td>
						<span>'.$val['professional'].'</span>
					</td>
				</tr>               
               		<tr>
					<td class="alt left">最高学历：</td>
					<td>
						<span>'.$val['level_school'].'</span>
					</td>
                    <td class="alt left">毕业年份：</td>
					<td>
						<span>'.$val['graduation'].'</span>
					</td>
				</tr> 
                	<tr>
					<td class="alt left">电话：</td>
					<td>
						<span>'.$val['phone'].'</span>
					</td>
                    <td class="alt left">邮箱：</td>
					<td>
						<span>'.$val['mail'].'</span>
					</td>
				</tr> 
                	<tr>
					<td class="alt left">学科：</td>
					<td>
						<span>'.$val['xueke_name'].'</span>
					</td>
                    <td class="alt left">性质：</td>
					<td>
						<span>'.$val['formal_name'].'</span>
					</td>
				</tr> ';
			}
            $html .='</table>';
		}
        
        if(!empty($fenInfo)){
            $html .='<p style="font-size: 14px; padding-top: 15px; padding-bottom: 15px;color:blue;">线下考试情况</p>
            <table width="95%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">                
				 <tr style="border: 1px solid #000040; background: silver;">
					<td class="alt right" style="width: 20px;">次数：</td>
					<td class="alt right" style="width: 50px;">考试时间：</td>
                    <td class="alt right" style="width: 50px;">考试时长：</td>
                    <td class="alt right" style="width: 20px;">满分：</td>
                    <td class="alt right" style="width: 20px;">得分：</td>
                    <td class="alt right" style="width: 20px;">本次考试排名：</td>
				</tr>
                <tr style="border: 1px solid #000040;">	';
            foreach($fenInfo as $key=>$val){
                foreach($val as $k=>$v){
                    $html .='<td class="alt right" style="width: 20px;">第'.$v['test_ci'].'次</td>
				    	<td class="alt right" style="width: 50px;">'.$v['test_time'].'</td>
                        <td class="alt right" style="width: 50px;">'.$v['shichang'].'</td>
                        <td class="alt right" style="width: 20px;">'.$v['zongfen'].'</td>
                        <td class="alt right" style="width: 20px;">'.$v['test_score'].'</td>
                        <td class="alt right" style="width: 20px;">'.$v['test_level'].'</td>';
                }
            }
            $html .='</table>';
        }
        
        if(!empty($pingjiaInfo)){
            $html .='<p style="font-size: 14px; padding-top: 15px; padding-bottom: 15px;color:blue;">点评</p>
             <div style="overflow-y: auto; height: 200px; width:95%"> ';
            foreach($pingjiaInfo as $key=>$val){
                $html .='<div style="border:1px solid #808040;">
                        <div>'.$val['create_time'].'</div>
                        <div>'.$val['test_comments'].'                        
                        </div>
                        <div>点评人：'.$val['create_name'].'</div>                    
                     </div>';
            }
            $html .= '</div> ';
        }
        $html .='</div>';
	    ob_start();
		echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">'.$html.'</html>';
		$data = ob_get_contents();
		ob_end_clean();
		$targetFolder = UPLOAD_PATH.date('Y-m-d').'/';
		if(!file_exists($targetFolder)){
			umask(0);
			mkdir($targetFolder,0777);
		}
        $time = time();
		$docPath = $targetFolder.$time.'.doc';        
		$fp = fopen($docPath, "wb");        
		fwrite($fp, $data);        
		fclose($fp);
		header("location:".str_replace('/Upload/','/upload/',end(explode('/eap',$docPath))));
	}

    //关闭多个账号
    public function closeNumber(){
        $id = $_GET['id'];
        $trNameInfo = D('Training')->getTrNameInfo();//培训名称列表
        $dictInfo = D ( 'Training' )->get_dictList();//学科列表
        $arrPost = $_POST;
        $arrGet = $_GET;
        if($arrGet['peixun_name'] != '' && $arrGet['xueke_name'] != ''){
            $seTrTeachInfo = D('Training')->getTrTeachInfo($arrGet);
        }
        if(!empty($arrPost['teach_arr'])){

            $result = D('Training')->upTrTeachStatus($arrPost['teach_arr']);
            if($result !== false){
                echo "<script>alert('关闭成功')</script>";
                //print_r($arrPost);exit;
                header("Location:".$_SERVER['REQUEST_URI']."?peixun_name=".$arrPost['px_name']."&xueke_name=".$arrPost['xk_name']);
            }
        }
        $this->assign ( get_defined_vars () );
        $this->display ();

    }
    
    
    public function uptest(){
        //$te_id='228';
       // $subid = '4,';
        $result = D('Training')->getArrSign($subid);
        //print_r($result);exit;
        foreach($result as $key=>$val){
            if($val['xueke'] == '8,14,'){
                $id .= '"'.$val['id'].'",';
            }
        }
        //echo $id;exit;
        $id = rtrim($id, ",");
        //$result2 =  D('Training')->upSign($id);
        print_r($result2);exit;
    }
    
    
    
    
    //-----------------------ends------------------
    
    public  function editPeople() {
        $this->writeCheck();
        $aclKey = SysUtil::safeString($_GET['action']);
        $url = $_SERVER['REQUEST_URI'];
        if($this->isPost()) {
            $resultScript = true;
            $actionInfo = $_POST;
            $actionInfo['acl_icon'] = $_FILES['acl_icon'];
            $actionInfo['user'] = $this->loginUser;
            if($aclKey != $actionInfo['acl_key']) {
                $errorMsg = '非法提交';
            } else {
                $editResult = AppGroup::saveAction($actionInfo);
                $errorMsg = $editResult['errorMsg'];
            }
        }
        if(false == $resultScript)
            $actionInfo = AppGroup::getActionInfo($aclKey);
        $this->assign(get_defined_vars());
        $this->display('editPeople');
    }
    
    public function delPeople() {
        $url = $this->getUrl('delPeople');
        $moduleKey = SysUtil::safeString($_GET['module']);
        $this->writeCheck();
        if($this->isPost()) {
            $aclKey = SysUtil::safeString($_POST['module_key']) . '-' . SysUtil::safeString($_POST['action_name']);
            $_GET['action'] = $aclKey;
            $_POST['acl_key'] = $aclKey;
            return $this->editAction();
        }
        $this->assign(get_defined_vars());
        $this->display('delPeople');
    }
    

   
    
    public function dict_add_save() {
        
		$params = $this->_post ();

		$rs = D ( 'Basic' )->addDict ( $params );
		if ($rs) {
			$this->success ();
		}
		$this->error ();
	}
}
?>