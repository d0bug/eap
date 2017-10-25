<?php
/*
*Date 15-02-07
* APP前台提问问题接口
*/
class MyaskAction extends ApiCommAction {
    public function asklist() { 
        $data = array();
    	$vipmyask = D('Vipmyask');
        import("ORG.Util.Page");
        $curPage = isset($_GET['p'])?abs($_GET['p']):1;
        $pagesize =  20 ;
        $condition = $status = '' ;
        $condition = " AND status = 1";
        $data = $vipmyask->get_myaskList($condition,$curPage,$pagesize);
        $count = $vipmyask->get_myaskCount($condition);
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

    public function Myasklist() { 
        $data = array();
        $vipmyask = D('Vipmyask');
        import("ORG.Util.Page");
        $curPage = isset($_GET['p'])?abs($_GET['p']):1;
        $pagesize =  20 ;
        $condition = $status = '' ;
        $uid = $_GET['uid'];
        if(!empty($uid))
            $condition = " AND uid = '$uid' ";
        $data = $vipmyask->get_myaskList($condition,$curPage,$pagesize);
        if($data){
            $status = 1;
            $msg = 'success';
        }else{
            $status = 0;
            $msg ='err';
        }
      
        $ouput = array(
            'status'=>$status,
            'msg' =>$msg,
            'data'=> $data
            );
     echo $this->encode_json($ouput);die();
    }

    //查询回复内容

    public function get_reply(){
        $data = array();
        $askid = $_GET['askid'];
        $msg = $status = '';
        if(!empty($askid)){
            $vipmyask = D('Vipmyask');
            import("ORG.Util.Page");
            $curPage = isset($_GET['p'])?abs($_GET['p']):1;
            $pagesize =  20 ;
            $condition = '' ;
            $condition = " AND askid = '$askid' AND  status =1 ";
            $data = $vipmyask->get_reply($condition,$curPage,$pagesize);
            $Inc = $vipmyask->update_IncNum($askid); //访问量动加1
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

    //提问问题
    public function ask_question(){
        $data = array();
        $title = $_POST['title'];
        $title = trim(strip_tags($title));
        $uid = $_POST['uid'];
        $uname = $_POST['uname'];
        $ip = $_POST['ip'];
        $grade = $_POST['grade'];
        $status = $msg = '' ;
        // $title = '测试2';$uid=1;$uname='李罡1';$ip = '127.0.0.0.1';$grade = 2;
        if(!empty($title) && !empty($uid)){
           $vipmyask = D('Vipmyask');
           $data =  $vipmyask->insert_ask($title,$uid,$uname,$ip,$grade);
           if($data){
                $status = 1;
                $msg = '提问成功，请您等待老师的回复！';
           }else{
                $status = 0;
                $msg = '提问失败，系统出现问题！';
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
