<?php
/*后台VIP老师系统---管培部培训*/
class VipManagementAction extends VipCommAction{

    //考核
    public function trainTest(){
        $this->display();
    }
    //考核-列表
    public function getTestList(){
        $search_value = $_POST['search_value'];
        $search_value = "管培部".$search_value;
        $this->outPut(D('VipManagement')->getTrainTestList($search_value));
    }
    //考核-考核状态
    public function trainTestStatus(){
        $kid = $_GET['id'];
        $ktitle = $_GET['title'];
        if($_POST) {
            $arr = $_POST;
            $arr['status'] = implode(',',$arr['kaohe'] );

            if($arr['status'] != '' && $arr['kid'] != '') {

                if($arr['status'] == 1) {
                    $result = D('VipManagement')->addTrainTest($arr);
                    if($result){
                        $data['status'] = 1;
                        $data['msg'] = $ktitle.'开始考核！';
                    }else{
                        $data['status'] = 0;
                        $data['msg'] = $ktitle.'开始考核失败！';
                    }
                }elseif($arr['status'] == 2){

                    $result = D('VipManagement')->upTrainTest($arr);
                    if($result){
                        $data['status'] = 1;
                        $data['msg'] = $ktitle.'已结束考核！';
                    }else{
                        $data['status'] = 0;
                        $data['msg'] = $ktitle.'结束考核失败！';
                    }
                }
            }else{
                $data['status'] = 2;
                $data['msg'] = '失败！参数导常';
            }
            echo json_encode($data);

        }else {
            $teInfo = D('VipManagement')->getTrainTestRow($kid);
            //print_r($teInfo);exit;
            $this->assign(get_defined_vars());
            $this->display();
        }

    }

    //考核导出
    public function exportTestExcel(){
        $params = $this->_param ();
        $list = D('VipManagement')->getTrainTestExcel($params['id']);
        //print_r($list);exit;
        import("ORG.Util.Excel");
        $exceler = new Excel_Export();
        $fileTitle = $list['testName'].$list['testTime'].'——成绩单';
        $dotype_name = mb_convert_encoding($fileTitle,'gbk','utf-8');
        $exceler->setFileName($fileTitle.'.xls');
        //$exceler->setFileName($dotype_name.'.csv');
        $excel_title = '';
        $excel_title .= '培训名称-学管师姓名-校区-成绩-排名-';
        for($i = 1;$i<=$list['count'];$i++){
            $excel_title .= '第'.$i.'道题'.'-';
        }
        $excel_title = trim($excel_title,'-');
        $excel_title = explode('-',$excel_title);
        foreach ($excel_title as $key=>$title){
            $excel_title[$key] = mb_convert_encoding($title,'gbk','utf-8');
        }
        //print_r($excel_title);exit;
        $exceler->setTitle($excel_title);
        //print_r($list['dati']);exit;
        foreach ($list['dati'] as $key=>$val){
            $tmp_data = '';
            $tmp_data .= $val['peixunName'].'/*/'.$val['xueguanName'].'/*/'.$val['xiaoquName'].'/*/'.$val['chengji'].'/*/'.$val['paiming'].'/*/';
            //print_r($val['one']);exit;
            foreach($val['one'] as $keone=>$valone){
                if($valone == '未选'){
                    $valone = '未选';
                }else{
                    $valone = $this->zimu($valone);
                };
                $tmp_data .= $valone.'/*/';
            }
            foreach($val['duo'] as $keduo=>$valduo){
                //print_r($val['duo']);
                $duo = '';echo "<br>";
                foreach($valduo as $k=>$v){
                    if($v == '未选'){$v = '未选';}else{$v = $this->zimu($v);};
                    $duo .= $v.',';

                }
                $tmp_data .= $duo.'/*/';
            }
            foreach($val['text'] as $ketext=>$valtext){
                $tmp_data .= $valtext.'/*/';
            }
            $tmp_data = trim($tmp_data,'/*/');
            //print_r($tmp_data);exit;
            $exdata = explode('/*/',mb_convert_encoding($tmp_data,'gbk','utf-8'));
            $exceler->addRow($exdata);
        }
        $exceler->export();
    }

    public function zimu($xuan){
        if($xuan == 1)$xuan = 'A';
        if($xuan == 2)$xuan = 'B';
        if($xuan == 3)$xuan = 'C';
        if($xuan == 4)$xuan = 'D';
        return $xuan;
    }

    //查看试卷
    public function checkPaper(){
        $id = $_GET['id'];
        $result = D('VipManagement')->getTestLectureFilesInfo($id);
        //print_r($result);exit;
        $zimu=array('0'=>'A','1'=>'B','2'=>'C','3'=>'D');
        $this->assign(get_defined_vars());
        $this->display();
    }


    //视频
    public function trainVideo(){
        $this->assign(get_defined_vars());
        $this->display();
    }
    //视频-列表
    public function getTrainList(){
        $search_value = $_POST['search_value'];
        $test =  D('VipManagement')->getTrainVideoList($search_value);
        //print_r($test);exit;
        $this->outPut(D('VipManagement')->getTrainVideoList($search_value));
    }
    //-上传视频
    public function addVideo(){
        $user_key = $this->loginUser->getUserKey();
        #OSS配置
        $region = C('REGION');

        $accessKeyId = C('OSS_ACCESS_ID');
        $accessKeySecret = C('OSS_ACCESS_KEY');
        $bucket = C('BUCKET_YUNYING');
        $videoModel = D('VipManagement');
        //$videoTypeInfo = $videoModel->get_Opvideo_Type();
        if($_POST){
            $arr = $_POST;
            $arr['one_video_url'] = 'http://gaosiyunying.oss-cn-beijing.aliyuncs.com/'.$arr['video_url'];
            $userInfo = $this->loginUser->getInformation();
            $arr['create_name'] = $userInfo['real_name'];
            if($videoModel->add_video($arr,$user_key)){
                $this->success('视频上传成功');
            }else{
                $this->error('视频上传失败');
            }
        }else{
            //$attributeOneList = $videoModel->get_attributeList(array('pid'=>0));
            $this->assign(get_defined_vars());
            $this->display();
        }
    }

    /*uploadify结合oss视频上传*/
    public function upload_file(){
        set_time_limit(0);
        if (!empty($_FILES)){
            //set_time_limit(0);
            import('ORG.Util.OssSdk');
            $oss_sdk_service = new ALIOSS();
            //设置是否打开curl调试模式
            $oss_sdk_service->set_debug_mode(TRUE);//FALSE
            $bucket = C('BUCKET');
            $tempFile = $_FILES['Filedata']['tmp_name'];
            $fileParts = pathinfo($_FILES['Filedata']['name']);
            $imgTypeArr = array('jpg','jpeg','gif','png');
            if(in_array(strtolower($fileParts['extension']),$imgTypeArr)){
                $fileTypes = $imgTypeArr;
                $isFileType = 1; //图片
            }else{
                $fileTypes = array('flv','flv');
                $isFileType = 2; //视频
            }
            $uniqidname = uniqid(mt_rand(), true);
            if($_POST['is_realname'] == 1){
                //		$newFilename = time().$uniqidname.".".strtolower($fileParts['extension']);
                $newFilename = time().".".strtolower($fileParts['extension']);
            }else{
                $newFilename = $uniqidname.".".strtolower($fileParts['extension']);
            }
            if($isFileType == 2){
                $object = C('OSS_video_PATH').date('Y-m-d').'/'.$newFilename;
            }else if($isFileType == 1){
                $object = C('OSS_IMG_PATH').date('Y-m-d').'/'.$newFilename;
            }

            if (in_array(strtolower($fileParts['extension']),$fileTypes)){
                $content = '';
                $length = 0;
                $fp = fopen($tempFile,'r');
                if($fp){
                    $f = fstat($fp);
                    $length = $f['size'];
                    while(!feof($fp)){
                        $content .= fgets($fp);
                    }
                }
                $upload_file_options = array('content' => $content, 'length' => $length);
                $upload_file_by_content = $oss_sdk_service->upload_file_by_content($bucket,$object,$upload_file_options);
                if($upload_file_by_content->status == 200){
                    if($isFileType == 2){
                        echo json_encode(array('status'=>'上传成功','url'=>$object,'show_url'=>$object));
                    }else if($isFileType == 1){
                        echo json_encode(array('status'=>'上传成功','url'=>$object,'show_url'=>"http://".C('DEFAULT_OSS_HOST')."/".C('BUCKET')."/".$object));
                    }
                }else{
                    echo json_encode(array('status'=>'上传失败'));
                }
            } else {
                echo json_encode(array('status'=>'不支持的文件类型'));
            }
        }
    }

    public function trainVideoRow(){
        //$url = $this->getUrl('serviceReviewRow');
        $id = $_GET['id'];
        $result = D('VipManagement')->getTrainVideoRow($id);
        $result['list_arr'] = urldecode($result['list']);
        $result['list_arr'] = json_decode($result['list'],true);
        $this->assign(get_defined_vars());
        $this->display();
    }

    //修改视频
    public function trainVideoEdit(){
        #OSS配置
        $region = C('REGION');
        $accessKeyId = C('OSS_ACCESS_ID');
        $accessKeySecret = C('OSS_ACCESS_KEY');
        $bucket = C('BUCKET_YUNYING');
        $videoModel = D('VipManagement');
        $vid = $_GET['id'];
        if($_POST){
            $arr = $_POST;
            $userInfo = $this->loginUser->getInformation();
            if($arr['video_url']){
                $arr['one_video_url'] = 'http://gaosiyunying.oss-cn-beijing.aliyuncs.com/'.$arr['video_url'];
            }
            $arr['create_name'] = $userInfo['real_name'];
            if($videoModel->editTrainVideo($arr)){
                $this->success('视频修改成功');
            }else{
                $this->error('视频修改失败');
            }
        }else{
            //$videoTypeInfo = $videoModel->get_Opvideo_Type();
            $videoInfo = $videoModel->getTrainVideoRow($vid);
            //print_r($videoInfo);exit;
            $this->assign(get_defined_vars());
            $this->display();
        }
    }

    //删除视频
    public function delManagement(){
        $id = implode(',',$_POST['id'] );
        if(!empty($id)){
            $status = D ( 'VipManagement' )->deleteManagementByID ( $id );
            if($status){
                $data['status'] = 1;
                $data['msg'] = '删除成功！';
            }else{
                $data['status'] = 0;
                $data['msg'] = '删除失败！';
            }
        }else{
            $data['status'] = 2;
            $data['msg'] = '失败！参数导常';
        }
        echo json_encode($data);
    }
    //视频浏览记录
    public function videoBrowse(){
        if($_GET){
            $vid = $_GET['id'];
            $videoList = D ( 'VipManagement' )->getVideoBrowse( $vid );
        }

        $this->assign(get_defined_vars());
        $this->display();
    }




}
?>