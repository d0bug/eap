<?php
/*
*Date 15-02-07
* APP前台家长圈接口
*/
class VipCircleAction extends ApiCommAction {
    //圈子列表
    public function Circlelist() {
        $data = array();
        $status = $msg = '';
        $vipCircle = D('VipCircle');
        import("ORG.Util.Page");
        $curPage = isset($_GET['p'])?abs($_GET['p']):1;
        $pagesize = 20;
        $condition = '';
        $keyword = isset($_POST['keyword']) ? $_POST['keyword'] :'';
        if(!empty($keyword) || $keyword == 0){
            $condition .= " AND title like  '%".$keyword."%'";
        }

        $condition .= " AND status =1";
        $data = $vipCircle->get_CircleList($condition,$curPage,$pagesize);
        //$data = preg_replace("#\\\u([0-9a-f]{4}+)#ie", "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))", $data);
        $count = $vipCircle->get_CircleCount($condition);
        if($data){
            $status = 1;
            $msg = 'success';
        }else{
            $status = 0;
            $msg ='err';
        }
      
        $ouput = array(
            'status'=>$status,
            'count' =>$count,
            'msg' =>$msg,
            'data'=> $data
        );
       echo $this->encode_json($ouput);die();
    }

    //查询评论内容
    public function get_CircleReply(){
        $data = array();
        $cid = $_GET['cid'];
        $status = $msg = '';
        if(!empty($cid)){
            $VipCircle = D('VipCircle');
            import("ORG.Util.Page");
            $curPage = isset($_GET['p'])?abs($_GET['p']):1;
            $pagesize =  20 ;
            $condition = '' ;
            $condition = " AND circle_id = '$cid' ";
            $data = $VipCircle->get_comment($condition,$curPage,$pagesize);
            $Inc = $VipCircle->update_IncNum($cid); //访问量动加1
            $status = 1;
            $msg = 'success';
        }
        else{
            $status = 0;
            $msg = 'err';
        }

        $ouput = array(
            'status'=>$status,
            'msg' =>$msg,
            'data'=> $data
            );
        echo $this->encode_json($ouput);die();
    }

    //对圈子进行评论
    public function vipCircleReply(){
        $data = array();
        $status = $msg = '';
        $cid = $_POST['cid'];
        $content = $_POST['content'];
        $content = trim(strip_tags($content));
        $uid = $_POST['uid'];
        $uname = $_POST['uname'];
        $ip = $_POST['ip'];
        if(!empty($content) && !empty($cid) && !empty($uid)){
           $VipCircle = D('VipCircle');
           $replycontent = $VipCircle->insert_comment($cid,$content,$uid,$uname);
           if($replycontent){
                $circlestatus = $VipCircle->update_Circle($cid);
                $status = 1;
                $msg = '评论成功！';
           }else{
                $status = 0;
                $msg = '评论失败，系统出现问题！';
           }
        }else{
            $status = 0;
            $msg = '参数出现问题！';
        }
        $ouput = array(
            'status'=>$status,
            'msg' =>$msg,
            'data'=> $data
            );
        echo $this->encode_json($ouput);die();
    }

    //建圈子接口
    public function addCircle(){
        $status = $msg = '';
        $arr['title'] = $_POST['title'];
        $arr['content'] = $_POST['intro'];
        $arr['uid'] = $_POST['uid'];
        $arr['username'] = $_POST['uname'];
        $arr['title'] = trim($arr['title']);
        $arr['content'] = trim($arr['content']);
        if(empty($arr['title']) || empty($arr['content'])){
            $status = 0;
            $err ='圈子标题或内容不能为空' ;
            $r = 'NULL' ;
        }else{
            $CircleModel = D('VipCircle');
            $r = $CircleModel->addCircle($arr);
          if($r){
              $status = 1;
              $msg = '建圈成功！';
          }else{
               $status = 0;
               $msg ='建圈失败！';
           }
        }

        $ouput = array(
                'status'=>$status,
                'msg' =>$msg,
                'data'=> $r
            );
       echo $this->encode_json($ouput);die();
     }


     //接口版本号更新
     public function VersionInfo(){
        $VersionName = '1.0版';
        $VersionCode = '1.0.0.1';
        $Intro ='1、此版本为大版本.
                 2、测试。
                 3、 ......';
       $Downloadurl = 'http://www.baidu.com/' ;
       $status = 1;
       $msg = 'sucess！';
       $ouput = array(
                'VersionName'=>$VersionName,
                'VersionCode'=>$VersionCode,
                'Intro' =>$Intro,
                'Download'=> $Downloadurl,
                'status'=>$status,
                'msg'=>$msg
            );
       echo $this->encode_json($ouput);die();
     }

     

    //投诉
    public function addComplaint(){
        $status = $msg = '';
        $content = $_POST['content'];
        $content = trim(strip_tags($content));
        $uid = $_POST['uid'];
        $uname = $_POST['uname'];
        $phone = $_POST['phone'];
        $CircleModel = D('VipCircle');

        if(empty($uid) || empty($content)){
             $status = 0;
             $msg ='投拆失败！没有获取到用户ID或内容';
        }else{
            $r = $CircleModel->insert_complaint($uid,$uname,$content);
            if($r){
                  import('COM.MsgSender.SmsSender');  //发短信              
                  $smsObj = new SmsSender();
                  $to_mobile = '13810757323';
                  $smsContent = '老师您好，学员'.$uname.'，家长电话:'.$phone.'通过学员系统进行意见投诉，内容为：'.$content.'，请您尽快跟家长取得联系！辛苦啦！';
                  $smsReturn = $smsObj->sendSms($to_mobile,$smsContent);
                  $status = 1;
                  $msg = '投拆成功！';
            }else{
                 $status = 0;
                 $msg ='投拆失败！';
             }
        }
    
        $ouput = array(
                'status'=>$status,
                'msg' =>$msg,
                'data'=> $r
            );
        echo $this->encode_json($ouput);die();
     }  



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
