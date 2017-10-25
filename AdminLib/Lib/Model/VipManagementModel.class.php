<?php

class VipManagementModel extends Model {
	public  $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao();
		$this->vp_video = 'vp_video';
		$this->vp_video_attribute = 'vp_video_attribute';
		$this->vp_video_favorite = 'vp_video_favorite';

		$this->sys_users = 'sys_users';
        //--------
        $this->dao2 = Dao::getDao('MYSQL_CONN_KNOWLEDGE');
        $this->px_management_video = 'px_management_video';//培训视频
        $this->px_management_test = 'px_management_test';//培训考核
        $this->px_management_test_detailed = 'px_management_test_detailed';//培训考核
        $this->vip_archive = 'vip_archive';//组卷表
        $this->px_management_video_browse = 'px_management_video_browse';//培训视频浏览表
        $this->vip_question = 'vip_question';//题目
        $this->vip_question_option = 'vip_question_option';//选择 多选
        $this->vip_question_answer = 'vip_question_answer';//简答,填空

        $this->dao3 = Dao::getDao('MYSQL_CONN_ENROLL');
        $this->atf_lecture_files = 'atf_lecture_files';
        
        
	}
    
    public function add_video($arr,$user_key){

		if(!empty($arr['video_url']) && !empty($arr['title'])){

			$sql = 'INSERT INTO '.$this->px_management_video.' (video_url,one_video_url,video_type,
													 title,
													 create_date,
													 create_time ,
													 create_name) 
											VALUES ('.$this->dao->quote($arr['video_url']).',
                                                    '.$this->dao->quote($arr['one_video_url']).',
                                                    '.$this->dao->quote($arr['video_type']).',
													'.$this->dao->quote(SysUtil::safeString($arr['title'])).',	
													'.$this->dao->quote(date('Y-m')).',
													'.$this->dao->quote(date('Y-m-d H:i:s')).',
                                                    '.$this->dao->quote($arr['create_name']).'
                                                    )';
			//echo $sql;exit;
			if($this->dao2->execute($sql)){
				return true;
			}
			return false;
		}
		return false;

	}


    public function getTrainVideoList($search_value){
        $where ='';
        if($search_value != ''){
            $where .= ' and title like "%'.$search_value.'%" ';
        }
        $result = $this->dao2->getAll('select * from '.$this->px_management_video.' where status =1 '.$where.' order by id desc');

        foreach($result as $key=>$val){
            $result[$key]['shanchu'] = '<input type="checkbox" name="shanchu" id="shanchu" value="'.$val['id'].'"';
            $result[$key]['bianji'] = '<a onclick="javascript: bianji('.$val['id'].')" style="color:blue;" >编辑</a>';
            $result[$key]['chakan'] = '<a onclick="javascript: xiangqing('.$val['id'].')" style="color:blue;" >查看</a>';
            $result[$key]['jilu'] = '<a onclick="javascript: jilu('.$val['id'].')" style="color:blue;" >浏览记录</a>';
        }
        if(!empty($result)){
            return $result;
        }else{
            return false;
        }
    }

    public function getTrainVideoRow($id){
        return $this->dao2->getRow("select * from ".$this->px_management_video." where id = ".$id." and status =1 ");
    }
    
    public function editTrainVideo($arr){
        if(!empty($arr['title']) ){
            $video_url = '';
            $one_video_url = '';
            if($arr['video_url']){
                $video_url .= $this->dao->quote(SysUtil::safeString($arr['video_url']));
                $one_video_url .= $this->dao->quote(SysUtil::safeString($arr['one_video_url']));
            }else{
                $video_url .= $this->dao->quote(SysUtil::safeString($arr['old_video']));
                $one_video_url .= $this->dao->quote(SysUtil::safeString($arr['old_one_video']));

            }
            $sql = 'UPDATE '.$this->px_management_video.' SET   video_url = '.$video_url.',
                                                     one_video_url = '.$one_video_url.',
                                                     video_type = '.$this->dao->quote($arr['video_type']).',
													 title = '.$this->dao->quote(SysUtil::safeString($arr['title'])).',
													 create_time = '.$this->dao->quote(date('Y-m-d H:i:s')).',
                                                     create_name = '.$this->dao->quote($arr['create_name']).'  
													 WHERE id = '.$this->dao->quote($arr['id']);

            if($this->dao2->execute($sql)){
                return true;
            }
            return false;
        }
        return false;
    }

    public function getVideoBrowse($id){
        return $this->dao2->getAll('select * from '.$this->px_management_video_browse.' where vi_id ='.$id.' and status = 1 ');
    }

	public function deleteManagementByID($id){
		return $this->dao2->execute('update '.$this->px_management_video.' set status = 0 where id  in ('.$id.')');
	}

    public function getTrainTestList($search_value){
        $where ='';
        if($search_value != ''){
            $where .= ' and lecture_file_name like "%'.$search_value.'%" ';
        }
        //$test = 'select id,lecture_file_name from '.$this->atf_lecture_files.' where is_removed =0 and lecture_type=\"TEACHER_PAPER\" and agency_id in(384,178) '.$where.' order by id desc';
        //echo $test;exit;

        $result = $this->dao3->getAll('select id,lecture_file_name from '.$this->atf_lecture_files.' where is_removed =0 and lecture_type="TEACHER_PAPER" and agency_id in(384,178,254) '.$where.' order by id desc');//
        //echo $this->dao3->getLastSql();
        //print_r($result);exit;
        foreach($result as $key=>$val){
            $teInfo = $this->dao2->getRow('select * from '.$this->px_management_test.' where status = 1 and ar_id ='.$val['id']);
            if($teInfo['kh_status'] == 1 ){
                $result[$key]['kaohe'] = '<a onclick="javascript: kaoheend('.$val['id'].')" style="color:red;" >结束考核</a>';
            }elseif($teInfo['kh_status'] == 2){
                $result[$key]['kaohe'] = '已结束';
            }else{
                $result[$key]['kaohe'] = '<a  onclick="javascript: kaohe('.$val['id'].')" style="color:blue;" >开始考核</a>';
            }
            $result[$key]['kaohetime'] = $teInfo['kh_start_time'].' - '.$teInfo['kh_end_time'];
            $result[$key]['daochu'] = '<a onclick="javascript: daochu('.$teInfo['id'].','.$teInfo['kh_status'].')" style="color:green;" >成绩单导出</a>';
            $result[$key]['chakan'] = '<a onclick="javascript:shijuan('.$val['id'].')" style="color:green;" target="_blank" >查看</a>';

        }
        //print_r($result);exit;
        if(!empty($result)){
            return $result;
        }else{
            return false;
        }
    }

    public function getTrainTestRow($kid){
        return $this->dao2->getRow('select *  from '.$this->px_management_test.' where ar_id ='.$kid.' and status = 1 ');
    }

    public function addTrainTest($arr){
        $archiveInfo = $this->dao3->getRow('select id,lecture_file_name from '.$this->atf_lecture_files.' where is_removed = 0 and id ='.$arr['kid']);

        $testInfo = $this->dao2->getRow('select *  from '.$this->px_management_test.' where ar_id ='.$archiveInfo['id'].' and status = 1 ');

        if(empty($testInfo)) {
            //print_r($testInfo);exit;
            $sql = 'INSERT INTO ' . $this->px_management_test . ' (ar_id,ar_name,kh_status,create_date,kh_start_time) 
											VALUES (' . $this->dao->quote($archiveInfo['id']) . ',
                                                    ' . $this->dao->quote($archiveInfo['lecture_file_name']) . ',
                                                    ' . $this->dao->quote(1) . ',
                                                    ' . $this->dao->quote(date('Y-m')) . ',
													' . $this->dao->quote(date('Y-m-d H:i:s')) . '
                                                    )';


            if ($this->dao2->execute($sql)) {
                return true;
            } else {
                return false;
            }
        }else{
            return true;
        }
    }
    public function upTrainTest($arr){
        $sql = 'UPDATE '.$this->px_management_test.' SET 
                                                     kh_status = '.$this->dao->quote(2).',
													 kh_end_time = '.$this->dao->quote(date('Y-m-d H:i:s')).'  
													 WHERE id = '.$this->dao->quote($arr['tid']);
        //echo $sql;exit;

        if($this->dao2->execute($sql)){
            return true;
        }
        return false;
    }

    public function getTrainTestExcel($id){
        //1 培训名称, 学管师信息, 题目和选项
        $testInfo = $this->dao2->getRow("select * from ".$this->px_management_test." where id=".$id." and status = 1");
        $excelInfo['testName'] = $testInfo['ar_name'];
        $excelInfo['testTime'] = $testInfo['kh_start_time'].'-'.$testInfo['kh_end_time'];
        $detailedInfo = $this->dao2->getAll("select * from ".$this->px_management_test_detailed." where ts_id=".$id." and status = 1 order by score desc");
        //print_r($detailedInfo);exit;
        foreach($detailedInfo as $key=>$detailedList){
            $datiInfo = unserialize($detailedList['dati']);
            //print_r($datiInfo);exit;
            foreach($datiInfo as $keInfo=>$valInfo) {
                $excelInfo['dati'][$key]['peixunName'] = $testInfo['ar_name'];
                $excelInfo['dati'][$key]['xueguanName'] = $detailedList['te_name'];
                $excelInfo['dati'][$key]['xiaoquName'] = $detailedList['user_school_name'];
                $excelInfo['dati'][$key]['chengji'] = $detailedList['score'];
                $excelInfo['dati'][$key]['paiming'] = $key+1;
                if ($keInfo == 'quone') {
                    unset($datiInfo['quone']['score']);
                    $qucount = count($datiInfo['quone']);
                    foreach ($datiInfo['quone'] as $keone => $oneval) {
                        if($oneval) {
                            $option = $this->questionOption($oneval);
                            $excelInfo['dati'][$key]['one'][$keone] = $option['sort'];
                        }else{
                            $excelInfo['dati'][$key]['one'][$keone] = '未选';
                        }
                    }
                }
                if ($keInfo == 'quduo') {
                    unset($datiInfo['quduo']['score']);
                    //print_r($datiInfo['quduo']);
                    $qucount += count($datiInfo['quduo']);
                    foreach ($datiInfo['quduo'] as $keduo => $duoval) {
                        if($duoval) {
                            foreach ($duoval as $kduo => $dval) {
                                $option = $this->questionOption($dval);
                                $excelInfo['dati'][$key]['duo'][$keduo][$kduo] = $option['sort'];
                            }
                        }else{
                            $excelInfo['dati'][$key]['duo'][$keduo][] ='未选';
                        }
                    }
                }
                if ($keInfo == 'text') {
                    unset($datiInfo['text']['score']);
                    $qucount += count($datiInfo['text']);
                    foreach ($datiInfo['text'] as $ketext => $textval) {
                        if($textval) {
                            $excelInfo['dati'][$key]['text'][$ketext] = $textval;
                        }else{
                            $excelInfo['dati'][$key]['text'][$ketext] = '未填';
                        }
                    }
                }
            }
        }
        $excelInfo['count'] = $qucount;
        return $excelInfo;
    }

    public function questionOption($id){
        return $this->dao2->getRow("select * from ".$this->vip_question_option." where id = ".$id."  and status =1 ");
    }

    public function questionAnswer($id){
        return $this->dao2->getRow("select * from ".$this->vip_question_answer." where id = ".$id."  and status =1 ");
    }

    public function getQuestionRow($id){
        return $this->dao2->getRow("select * from ".$this->vip_question." where id = ".$id."  and status =1 ");
    }

    public function getQuestionAnswer($Qid){
        return $this->dao2->getRow("select * from ".$this->vip_question_answer." where question_id = ".$Qid."  and status =1 ");
    }

    public function getQuestionOption($Qid){
        return $this->dao2->getAll("select * from ".$this->vip_question_option." where question_id = ".$Qid."  and status =1 ");
    }

    public function getTestLectureFilesInfo($id){
        $result =  $this->dao3->getRow('select * from '.$this->atf_lecture_files.' where is_removed =0 and id ='.$id.' order by id desc');
        $datiInfo = json_decode($result['lecture_file_detail'],TRUE);
        $questionInfo['testName'] = $datiInfo['title'];
        $questionInfo['testScore'] = $datiInfo['paperScore'];
        //print_r($questionInfo);exit;
        foreach($datiInfo as $datiKey=>$datiVal){
            if($datiKey == 'quesTypes'){
                foreach($datiVal as $queKey=>$queVal){
                    $questionInfo['dati'][$queKey]['code']=$queVal['code'];
                    $questionInfo['dati'][$queKey]['title']=$queVal['title'];
                    $questionInfo['dati'][$queKey]['quesScore']=$queVal['quesScore'];
                    $questionInfo['dati'][$queKey]['totalScore']=$queVal['totalScore'];
                   if($queVal['code'] == 'QU1000' || $queVal['code'] == 'QU1001') {
                       foreach ($queVal['questions'] as $key => $val) {
                           $quInfo = $this->getQuestionRow($val);
                           $quInfo['xuanxiang'] = $this->getQuestionOption($quInfo['id']);
                           $questionInfo['dati'][$queKey]['timu'][] = $quInfo;
                       }
                   }
                   if($queVal['code'] == 'QU1002'){
                       foreach ($queVal['questions'] as $key => $val) {
                           $quInfo = $this->getQuestionRow($val);
                           $quInfo['xuanxiang'] = $this->getQuestionAnswer($quInfo['id']);
                           $questionInfo['dati'][$queKey]['timu'][] = $quInfo;
                       }
                   }
                }
            }
        }
        //print_r($questionInfo);exit;
        return $questionInfo;
    }







}


?>