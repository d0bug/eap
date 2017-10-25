<?php
/*后台VIP老师系统---学员app收货地址*/
class VipAddressAction extends VipCommAction{
	
    //发货地址
	public function addressList(){
		$vipAddress = D('VipAddress');
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;
        
		$pagesize = C('PAGESIZE');
        $search = isset($_GET['search']) ? $_GET['search'] :'';
        if(!empty($search)){
            $condition = " and uname like '%".$search."%' or de_time like '%".$search."%' or logistics like '%".$search."%' and status =1 ";
        }else{
            $condition = " and status =1";
        }		
        
		$vipComplaintList = $vipAddress->get_AddressList($condition,$curPage,$pagesize);
        foreach($vipComplaintList as $key=>$val){
            if($val['address']){
                $addressInfo = $vipAddress->get_AddressInfo($val['address']);
                $vipComplaintList[$key]['address_info'] = $addressInfo['re_city'].$addressInfo['re_address'];    
            }
            
        }
        
        /*foreach($result as $key=>$val){
            $addressInfo = $this->dao2->getRow("select * from ".$this->vip_exchange_address." where id = ".$val['address']);
            $result[$key]['address_info'] = $addressInfo['re_city'].$addressInfo['re_address'];
        }
        return $result;*/
        
		$count = $vipAddress->get_AddressCount($condition);
		$page = new Page($count,$pagesize);
		$showPage = $page->show();
		$this->assign(get_defined_vars());
		$this->display();

	}
    
    public function editAddress(){        
        if(!empty($_GET['id'])){
			$vipAddress = D('VipAddress');
			$recordInfo = $vipAddress->get_record_one($_GET['id']);            
			$addressInfo = $vipAddress->get_address($recordInfo[0]['address']); 
			
            
		}
        
		$this->assign(get_defined_vars());
		$this->display();
    }
	
   public function doEditAddress(){
    	if($_POST){
    	    $vipAddress = D('VipAddress');
			$id = $_POST['id'];
            $delivery_status = $_POST['delivery_status'];
            $time = $_POST['time'];
            $danhao = $_POST['danhao'];
            $newup = $_POST['newup'];
            $newname = $_POST['newname'];
            $newphone = $_POST['newphone'];
            $newadress = $_POST['newadress'];		
		
			if(!empty($id) && !empty($time) && !empty($danhao) && !empty($delivery_status)){
				$result = $vipAddress->edit_Address_one($id,$delivery_status,$time,$danhao,$newup,$newname,$newphone,$newadress);
				$operate = '编辑';				
				if($result==true){
					//$this->success(''.$operate.'成功');                   
                    echo"<script>alert('成功');history.go(-1)</script>";
                    
				}else{
					$this->error(''.$operate.'失败');
				}

		}else{
			$this->error('非法操作');
		}
   }
  }
   
  //评价列表
  public function evaluationList(){
    
    	$vipAddress = D('VipAddress');
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;        
		$pagesize = C('PAGESIZE');
        $search = isset($_GET['search']) ? $_GET['search'] :'';
        
        if(!empty($search)){
            $condition = " and studentname like '%".$search."%' or create_time like '%".$search."%'  and status =1 ";
        }else{
            $condition = " and status =1";
        }        
		$vipEvaluationList = $vipAddress->get_EvaluationList($condition,$curPage,$pagesize);        
		$count = $vipAddress->get_EvaluationCount($condition);
		$page = new Page($count,$pagesize);
		$showPage = $page->show();
		$this->assign(get_defined_vars());
		$this->display();
  }
  
  //详细评价内容
  public function evaluationInfo(){
        $vipAddress = D('VipAddress');
        $arr = $_GET;
        if($arr['id']){
            $evalInfo = $vipAddress->getEvaluationInfo($arr['id']);
            $evalInfo['environment_img'] = $this->levelImg($evalInfo['environment']);
            $evalInfo['service_img'] = $this->levelImg($evalInfo['service']);
            $evalInfo['activity_img'] = $this->levelImg($evalInfo['activity']);
            $evalInfo['communication_img'] = $this->levelImg($evalInfo['communication']);
            $evalInfo['professional_img'] = $this->levelImg($evalInfo['professional']);
            $evalInfo['solve_img'] = $this->levelImg($evalInfo['solve']);
            
        }
        
        $this->assign(get_defined_vars());
        $this->display();
        
   }
   
  //推荐列表
  public function recommendedList(){
    $vipAddress = D('VipAddress');
		import("ORG.Util.Page");
		$curPage = isset($_GET['p'])?abs($_GET['p']):1;        
		$pagesize = C('PAGESIZE');
        $search = isset($_GET['search']) ? $_GET['search'] :'';
        if(!empty($search)){
            $condition = " and studentname like '%".$search."%'  and status =1 ";
        }else{
            $condition = " and status =1";
        }
		$vipRecommendedList = $vipAddress->get_RecommendedList($condition,$curPage,$pagesize);
		$count = $vipAddress->get_RecommendedCount($condition);
		$page = new Page($count,$pagesize);
		$showPage = $page->show();
		$this->assign(get_defined_vars());
		$this->display();
  }
  
   //导出评价列表
  public function explodevaluation(){
        $vipAddress  = D('VipAddress');
        import("ORG.Util.Excel");
        $exceler = new Excel_Export();
        $evalInfo = $vipAddress->getAllEvaluation();
        $fileTitle = "评价列表";
        $dotype_name = mb_convert_encoding($fileTitle,'gbk','utf8');
        $exceler->setFileName($dotype_name.date('Y-m-d',time()).'.csv');
        $excel_title= array('学生ID','学生CODE','学生姓名', '校区环境','校区服务','校区活动','沟通能力','专业知识','解决问题','评语','创建时间');
		foreach ($excel_title as $key=>$title){
			$excel_title[$key] = mb_convert_encoding($title,'gbk','utf8');
		}
		$exceler->setTitle($excel_title);
		foreach ($evalInfo as $key=>$val){		
			$tmp_data= array($val['uid'],$val['studentcode'],mb_convert_encoding($val['studentname'],'gbk','utf8'),$val['environment'],$val['service'],$val['activity'],$val['communication'],$val['professional'],$val['solve'],mb_convert_encoding($val['content'],'gbk','utf8'),$val['create_time']);
		
			$exceler->addRow($tmp_data);
		}
		$exceler->export();
        
  }
  
  //导出推荐列表
  public function explodrommended(){
        $vipAddress = D('VipAddress');		
		import("ORG.Util.Excel");
		$exceler = new Excel_Export();
		$recomInfo = $vipAddress->getAllRecommended();
		$fileTitle = '推荐列表';
		$dotype_name = mb_convert_encoding($fileTitle,'gbk','utf8');
		$exceler->setFileName($dotype_name.Date('Y-m-d',time()).'.csv');
		$excel_title= array('推荐人ID','推荐人CODE','推荐人姓名', '推荐人-推荐码','推荐时间','被推荐人姓名','被推荐人电话','被推荐人预辅导课程','被推荐人期望上课校区');
		foreach ($excel_title as $key=>$title){
			$excel_title[$key] = mb_convert_encoding($title,'gbk','utf8');
		}
		$exceler->setTitle($excel_title);
		foreach ($recomInfo as $key=>$val){		
			$tmp_data= array($val['uid'],$val['ucode'],mb_convert_encoding($val['uname'],'gbk','utf8'),$val['urecode'],$val['create_time'],mb_convert_encoding($val['sname'],'gbk','utf8'),$val['sphone'],mb_convert_encoding($val['scourse'],'gbk','utf8'),mb_convert_encoding($val['scampus'],'gbk','utf8'));
		
			$exceler->addRow($tmp_data);
		}
		$exceler->export();
  }
   
  //用户统计-用户活跃量统计
	public function visitCountList(){
        $vipAddress = D('VipAddress');
        import("ORG.Util.Page");
        $curPage = isset($_GET['p'])?abs($_GET['p']):1;
        $pagesize = C('PAGESIZE');
        $formData = $_GET;
        $formData['type'] = ($formData['type'])?:'';     

        if($formData['submit2'] == '导出'){
            $Bdate = $formData['beginDate'];
            $Edate = $formData['endDate'];
            import("ORG.Util.Excel");
            $exceler = new Excel_Export();
            $result = $vipAddress->export_visit_json($Bdate,$Edate);
            //print_r($result);exit;
            $dotype_name = '学员活跃量记录'.$Bdate.'__'.$Edate;
            //print_r($dotype_name);exit;
            $exceler->setFileName($dotype_name.'.xls');
            $excel_title= array('学号','学员姓名', '学管师姓名','所属校区','是否为新签');
            foreach ($excel_title as $key=>$title){
                $excel_title[$key] = mb_convert_encoding($title,'gbk','utf-8');
            }
            $exceler->setTitle($excel_title);
            foreach ($result as $key=>$val){
                $tmp_data= array(
                    "'". mb_convert_encoding($val['uid'],'gbk','utf8'),
                    mb_convert_encoding($val['sstudentname'],'gbk','utf8'),
                    mb_convert_encoding($val['manage_teacher'],'gbk','utf8'),
                    mb_convert_encoding($val['attribute_school'],'gbk','utf8'),
                    mb_convert_encoding($val['sign_status'],'gbk','utf8'),);
                $exceler->addRow($tmp_data);
            }
            $exceler->export();exit;


        }
           
        if($formData['type']){
            switch($formData['type']){
                case 1:
                    $formData['beginDate']= date("Y-m-d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")));
                    $formData['endDate']= date("Y-m-d",mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y")));
                    break;
                case 2:
                    $formData['beginDate'] = date("Y-m-d",mktime(0, 0 , 0,date("m"),1,date("Y")));
                    $formData['endDate'] =  date("Y-m-d",mktime(23,59,59,date("m"),date("t"),date("Y")));
                    break;
            }
            if(!empty($formData['beginDate']) && !empty($formData['endDate'])){
                $formData['beginDate'] = $formData['beginDate'];
                $formData['endDate'] = $formData['endDate'];
            }
        }else{

            if(!empty($formData['beginDate']) && !empty($formData['endDate'])){
                $formData['beginDate'] = $formData['beginDate'];
                $formData['endDate'] = $formData['endDate'];
            }else{
                $formData['beginDate'] = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - date("w") + 1, date("Y")));
                $formData['endDate'] = date("Y-m-d", mktime(23, 59, 59, date("m"), date("d") - date("w") + 7, date("Y")));
            }
        }

        if(!empty($formData['beginDate']) && !empty($formData['endDate'])){
            $condition = " and create_date >= '".$formData['beginDate']."' and create_date <= '".$formData['endDate']."' and status =1 ";
        }else{
            $condition = " and status =1";
        }

        $visitCountLists = $vipAddress->getVisitCountList($condition,$curPage,$pagesize);

        $container = array();
        $result = array();
        foreach ($visitCountLists as $item) {
            $key = $item['uid'] . '_' . $item['sstudentcode'] . '_' . $item['sstudentname']. '_' . $item['attribute_school']. '_' . $item['manage_teacher']. '_' . $item['sign_status'];
            $item['number'] = 1;
            if (empty($container[$key])) {
                $container[$key] = $item['number'];
            }
            else {
                $container[$key] += $item['number'];
            }
        }
        foreach ($container as $key => $item) {
            list($uid, $sstudentcode ,$sstudentname,$attribute_school,$manage_teacher,$sign_status) = explode('_', $key);
            $visitCountList[] = array('uid' => $uid, 'sstudentcode' => $sstudentcode, 'sstudentname' => $sstudentname,'attribute_school' => $attribute_school,'manage_teacher' => $manage_teacher,'sign_status' => $sign_status, 'number' => $item);
        }
        $count = $vipAddress->getVisitCount($condition);
        $page = new Page($count,$pagesize);
        $showPage = $page->show();

        $this->assign(get_defined_vars());
        $this->display();


	}

    //用户统计-历史数据
    public function oldVisitList(){
        //历史数据
        $vipAddress = D('VipAddress');
        import("ORG.Util.Page");
        $curPage = isset($_GET['p'])?abs($_GET['p']):1;
        $pagesize = C('PAGESIZE');
        $formData = $_GET;
        $formData['type'] = ($formData['type'])?:'';

        $oldVisitList = $vipAddress->getAllVisitList('',$curPage,$pagesize);
        $counts = $vipAddress->getAllVisitCount('');
        $pages = new Page($counts,$pagesize);
        $showPages = $pages->show();
        $this->assign(get_defined_vars());
        $this->display();
    }

    //用户统计-历史数据下载
    public function exportVisit(){
        $vipAddress = D('VipAddress');
        import("ORG.Util.Excel");
        $exceler = new Excel_Export();
        $visitJsonId = $_GET['id'];
        $result = $vipAddress->get_visit_json($visitJsonId);
        //$result['visit_list'] = json_decode($result['visit_json'],true);
        $result['visit_list'] = urldecode($result['visit_json']);
        $result['visit_list'] = json_decode($result['visit_list'],true);
        $dotype_name = $result['title'];
        //print_r($dotype_name);exit;
        $exceler->setFileName($dotype_name.'.xls');
        $excel_title= array('学号','学员姓名', '学管师姓名','所属校区','是否为新签','活跃量(去重)');
        foreach ($excel_title as $key=>$title){
          $excel_title[$key] = mb_convert_encoding($title,'gbk','utf-8');
        }
        $exceler->setTitle($excel_title);
        foreach ($result['visit_list'] as $key=>$val){
          $tmp_data= array(
              "'". mb_convert_encoding($val['uid'],'gbk','utf8'),
              mb_convert_encoding($val['sstudentname'],'gbk','utf8'),
              mb_convert_encoding($val['manage_teacher'],'gbk','utf8'),
              mb_convert_encoding($val['attribute_school'],'gbk','utf8'),
              mb_convert_encoding($val['sign_status'],'gbk','utf8'),
              mb_convert_encoding($val['number'],'gbk','utf8'));
          $exceler->addRow($tmp_data);
        }
        $exceler->export();

    }

    //用户统计-预览
    public function previewVisit(){
        $vipAddress = D('VipAddress');
        $visitJsonId = $_GET['id'];
        $result = $vipAddress->get_visit_json($visitJsonId);
        //print_r($result);exit;
        $result['visit_list'] = urldecode($result['visit_json']);
       //print_r($result['visit_list']);exit; 
        $result['visit_list'] = json_decode($result['visit_list'],true);
       
        $this->assign(get_defined_vars());
        $this->display();
    }


    //服务点评
    public function serviceReviewList(){
        //$serviceReviewInfo = D('VipAddress')->getServiceReviewList();
        $serviceReviewRowUrl = $this->getUrl('serviceReviewRow');
        $serviceReviewInfoUrl = $this->getUrl('serviceReviewInfo');
        $this->assign(get_defined_vars());
        $this->display();
    }
    public function serviceReviewInfo(){
        $search_value = $_POST['search_value'];
        $this->outPut(D('VipAddress')->getServiceReviewList($search_value));
    }

    //服务点评--查看
    public function serviceReviewRow(){
        $url = $this->getUrl('serviceReviewRow');
        $id = $_GET['id'];
        $result = D('VipAddress')->getServiceReviewRow($id);
        $result['list_arr'] = urldecode($result['list']);
        $result['list_arr'] = json_decode($result['list'],true);
        $this->assign(get_defined_vars());
        $this->display();
    }

    //服务点评--下载
    public function serviceExport(){
        $vipAddress = D('VipAddress');
        import("ORG.Util.Excel");
        $exceler = new Excel_Export();
        $result = $vipAddress->getServiceReviewList();
        $XyappModel = D('Xyapp');
        $seviceInfo =  $XyappModel->getServiceReviewArr();
        $dotype_name = '服务点评学员填写统计表'.date('Y-m-d',time());
        //print_r($dotype_name);exit;
        $exceler->setFileName($dotype_name.'.xls');
        $excel_title= array('学号','学员姓名', '电话','评价时间');
        foreach($seviceInfo as $key=>$val){
            $excel_title[] = $val['title'];
        }
        foreach ($excel_title as $key=>$title){
            $excel_title[$key] = mb_convert_encoding($title,'gbk','utf-8');
        }
        $exceler->setTitle($excel_title);

        foreach ($result as $key=>$val){
            $list_arr = urldecode($val['list']);
            $list_arr = json_decode($list_arr,true);
            $tmp_data = array(
                "'". mb_convert_encoding($val['uid'],'gbk','utf8'),
                mb_convert_encoding($val['uname'],'gbk','utf8'),
                mb_convert_encoding($val['phone'],'gbk','utf8'),
                mb_convert_encoding($val['create_time'],'gbk','utf8'),
            );
            foreach($list_arr as $k=>$l){
                if($l['msg'] == '1'){
                    $msg = mb_convert_encoding('√','gbk','utf8');
                }elseif($l['msg'] == '0'){
                    $msg = 'X';
                }
                $tmp_data[] = $msg;
            }
            $exceler->addRow($tmp_data);
        }
        $exceler->export();

    }



    
   
  public function levelImg($level){
     switch($level){
		case 1:
			$level_img = '<img src="/static/images/star-on-big.png">';
			break;
		case 2:
			$level_img = '<img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png">';
			break;
		case 3:
			$level_img = '<img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png">';
			break;
		case 4:
			$level_img = '<img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png">';
			break;
		case 5:
			$level_img = '<img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png">';
			break;
		default:
			$level_img = '<img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png"><img src="/static/images/star-on-big.png">';
    }
                
    return $level_img;
				
  }
   
   
   
   
   
    
}
?>