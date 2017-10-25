<?php
class TrainingModel extends Model {
	public $dao = null;
    public  $dao2 = null;
    public function __construct() {
        $this->dao = Dao::getDao ( 'MYSQL_CONN_KNOWLEDGE' );
        $this->dao2 = Dao::getDao('MSSQL_CONN');

        // protected $vip_dict = 'vip_dict';
        // protected $vip_dict_grade = 'vip_dict_grade';
        // protected $vip_dict_label = 'vip_dict_label';
        $this->vip_knowledge = 'vip_knowledge';
        $this->vip_knowledge_course_type_rs = 'vip_knowledge_course_type_rs';
        $this->vip_view_knowledge = 'vip_view_knowledge';
        // protected $vip_question_type_attr = 'vip_question_type_attr';
        // protected $vip_view_question_type = 'vip_view_question_type';
        // protected $vip_label = 'vip_label';
        // protected $vip_label_course_type_rs = 'vip_label_course_type_rs';
        // protected $vip_view_label = 'vip_view_label';
        // protected $vip_paper = 'vip_paper';
        $this->vip_question = 'vip_question';
        $this->vip_paper = 'vip_paper';
        $this->vip_question_answer = 'vip_question_answer';
        $this->vip_question_option = 'vip_question_option';
        // protected $vip_paper_question = 'vip_paper_question';

        // 变动后数据表===========================================================

        $this->vip_dict = 'vip_dict';//字典表
        $this->vip_dict_course_type = 'vip_dict_course_type';
        $this->vip_dict_question_type = 'vip_dict_question_type';

        /*edit by xcp*/
        $this->vip_dict_knowledge_type = 'vip_dict_knowledge_type';
        $this->vip_dict_subject_knowledgetype_rs = 'vip_dict_subject_knowledgetype_rs';

        //--------精英培训------
        $this->jy_training = 'jy_training';//培训表
        $this->jy_tra_teach = 'jy_tra_teach';//培训--老师表
        $this->jy_review_records = 'jy_review_records';//-培训-老师-点评记录表
        $this->jy_ppt_management = 'jy_ppt_management';//培训-PPT管理
        $this->jy_ppt_fileurl = 'jy_ppt_fileurl';//培训-PPT管理-图片
        $this->jy_ppt_detailed = 'jy_ppt_detailed';//培训-PPT管理-用户列表
        $this->jy_arranging = 'jy_arranging';//培训-我的课表
        $this->jy_test_management = 'jy_test_management';//培训-测试管理
        $this->vip_archive = 'vip_archive';//组卷表
        $this->jy_test_detailed = 'jy_test_detailed';//培训-测试管理-答题详细信息
        $this->jy_homework = 'jy_homework';//培训-作业管理
        $this->jy_homework_detailed = 'jy_homework_detailed';//培训-作业管理-答题详细信息
        $this->jy_tra_sign = 'jy_tra_sign';//培训-签到管理
        $this->jy_trajectory = 'jy_trajectory';//培训-成长轨迹
        $this->vip_dict_grade = 'vip_dict_grade';
        $this->vip_dict_subject = 'vip_dict_subject';



        $this->vp_user_subjects = 'vp_user_subjects';
    }


    //-------培训-------------
    public function getTraining(){
        $sql = "select * from ".$this->jy_training." where status =1 ";     
       	$result = $this->dao->getAll ( $sql );
        //var_dump($result);exit;
        foreach($result as $key=>$val){            
            $result[$key]['tr_time'] = $val['tr_start_time']." — ".$val['tr_end_time'];
            $zong = $this->dao->getRow('select count(*) as con from '.$this->jy_tra_teach.' where tr_id = '.$val['id'].' and status != -1');
            $result[$key]['zongrenshu'] = $zong['con'];   
            $tong = $this->dao->getRow('select count(*) as con from '.$this->jy_tra_teach.' where tr_id = '.$val['id'].' and status != -1 and through = 1  ');
            $result[$key]['tongguo'] = $tong['con'];
            $result[$key]['tr_audit_num'] = $result[$key]['tr_audit_num'];


        }
                
        return $result;
    }
    
    public function getTrainingId($id){
        return  $this->dao->getOne('select tr_name from '.$this->jy_training.' where status =1 and id = '.$id);
    }
    
    public function delArrangingId($arr){
        $arrangingResult = $this->dao->execute('update '.$this->jy_arranging.' set status = -1 where id ='.$arr['id']);
        if($arrangingResult){
            $traSignResult = $this->dao->execute('update '.$this->jy_tra_sign.' set status = -1 where ar_id = '.$arr['id'].' and tr_id = '.$arr['tr_id']);
            if($traSignResult){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
	public function getDictsByCategory($category, $currentPage, $pageSize, $sort, $order,$xuekeid='') {

    	$currentPage = empty ( $currentPage ) ? 1 : $currentPage;
    	$pageSize = empty ( $pageSize ) ? 20 : $pageSize;
        $xuekeWhere = '';

        if($xuekeid !=''){
            $xuekeWhere .= " and xueke like '%".$xuekeid.",%'  ";
        }

        $list = $this->dao->getAll('SELECT *
    								 FROM ' . $this->jy_tra_teach . '
    								 WHERE tr_id = ' . $this->dao->quote($category) . ' AND status != -1 '.$xuekeWhere.'
    								 ORDER BY id
    								 limit ' . ($currentPage - 1) * $pageSize . ', ' . $pageSize);

    //print_r($list);exit;
    	$total = 0;
    	if ($list) {
    		$row = $this->dao->getRow ( 'SELECT COUNT(*) AS cnt
    								 FROM ' . $this->jy_tra_teach . '
    								 WHERE tr_id = ' . $this->dao->quote ( $category ) . ' AND status != -1 '.$xuekeWhere );
    		if ($row) {
    			$total = $row ['cnt'];
    		}
    	}        
        
        foreach($list as  $key=>$val){
            if($val['sex'] == 1 ){
                $list[$key]['sex_name']= '男';    
            }else{
                $list[$key]['sex_name']= '女';
            }
            
            /*if($val['xueke']){
                $val['xueke'] = rtrim($val['xueke'],',');
                $xuekeInfo = $this->dao->getAll('select dg.title as ninaji, ds.title from '.$this->vip_dict_grade.' dg, '.$this->vip_dict_subject.' ds where ds.status =1 and dg.id=ds.grade_id and ds.id in('.$val['xueke'].') ');                
                foreach($xuekeInfo as $k=>$v){                  
                    $list[$key]['xueke_name'] .= $v['ninaji'].$v['title'].",";
                }
                
            }*/

            if($val['xueke']){
                $list[$key]['xueke_name'] = rtrim($this->getXuekeName($val['xueke']),',');    
            }             
            
            
            if($val['formal'] == 1){
                $list[$key]['formal_name'] = '全职';
            }elseif($val['formal'] == 2){
                $list[$key]['formal_name'] = '兼职';
            }
            
            if($val['through'] == 1 ){
                $list[$key]['through_name'] = '是';
            }elseif($val['through'] == 2){
                $list[$key]['through_name'] ='否';
            } 
            
            if($val['status'] == 0 ){
                $list[$key]['status_name'] = '是';
            }elseif($val['status'] == 1){
                $list[$key]['status_name'] ='否';
            }
            
            
            $list[$key]['kaoping'] = '<a onclick="javascript: kaoping('.$val['id'].')" >编辑</a>';    
            $list[$key]['xiangqing'] = '<a onclick="javascript: xiangqing('.$val['id'].')" >查看</a>'; 
            $list[$key]['daochu'] = '<a onclick="javascript: export_teach('.$val['id'].')">导出</a>';        
                
        }
        
    	return array (
    	'total' => $total,
    	'rows' => $list
    	);
	}
    
    public function getTeachByID($id) {
		return $this->dao->getRow ( 'SELECT * FROM ' . $this->jy_tra_teach . ' WHERE id = ' . $this->dao->quote ( $id ) );
	}
    
    public function getDictByCategoryAndCode($tr_name) {
		return $this->dao->getRow ( 'SELECT *
									 FROM ' . $this->jy_training . '
									 WHERE tr_name = ' . $this->dao->quote ( $tr_name ) . ' AND status = 1' );
	}
    public function getDictByTeachAndPhone($phone){
        return $this->dao->getRow ( 'SELECT *
									 FROM ' . $this->jy_tra_teach . '
									 WHERE phone = ' . $this->dao->quote ( $phone ) . ' AND status = 1' );
    }
    
    public function addPeople($arr){
       
        $this->dao->execute('INSERT INTO '.$this->jy_training.' (tr_name,tr_start_time,exam_tr_start_time,tr_end_time,exam_tr_end_time,tr_audit_num,tr_audit_str,create_time,create_name) VALUES(
        '.$this->dao->quote(SysUtil::safeString($arr['tr_name'])).',
        '.$this->dao->quote(trim($arr['tr_start_time'])).',
        '.$this->dao->quote(trim($arr['exam_tr_start_time'])).',
        '.$this->dao->quote(trim($arr['tr_end_time'])).',
        '.$this->dao->quote(trim($arr['exam_tr_end_time'])).',
        '.$this->dao->quote(abs($arr['tr_audit_num'])).',
        '.$this->dao->quote(SysUtil::safeString($arr['level_str'])).',
        '.$this->dao->quote(trim($arr['create_time'])).',
        '.$this->dao->quote(SysUtil::safeString($arr['create_name'])).')');
        if($this->dao->affectRows()){
			return true;
		}
		return false; 
        
   	}
    
    public function editPaper($arr){
		
		$this->dao->execute('UPDATE '.$this->jy_training.' SET 
        tr_name='.$this->dao->quote(SysUtil::safeString($arr['tr_name'])).',
        tr_start_time='.$this->dao->quote(SysUtil::safeString($arr['tr_start_time'])).',
        exam_tr_start_time='.$this->dao->quote(SysUtil::safeString($arr['exam_tr_start_time'])).',
        tr_end_time='.$this->dao->quote(trim($arr['tr_end_time'])).',
        exam_tr_end_time='.$this->dao->quote(trim($arr['exam_tr_end_time'])).',
        tr_audit_num='.$this->dao->quote(abs($arr['tr_audit_num'])).',
        tr_audit_str='.$this->dao->quote(SysUtil::safeString($arr['level_str'])).',
        create_time='.$this->dao->quote(trim($arr['create_time'])).'
         WHERE id = '.$this->dao->quote(abs($arr['id'])));
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}
    
    public function get_onepaper($id){
		$strQuery = 'SELECT * FROM '.$this->jy_training.' WHERE 1=1 ';
		if(!empty($id)){
			$strQuery .= ' AND id = '.$this->dao->quote($id);
		}
		return $this->dao->getRow($strQuery);
	}
    
    public function addTeache($arr){
        $xueke_name = rtrim($this->getXuekeName($arr['xueke']),',');
        $this->dao->execute('insert into '.$this->jy_tra_teach.' (tr_id,te_name,password,sex,birthday,school,professional,level_school,graduation,phone,mail,formal,create_time,create_name,xueke,xueke_name)values(
        '.$this->dao->quote(SysUtil::safeString($arr['tr_id'])).',
        '.$this->dao->quote(SysUtil::safeString($arr['te_name'])).',
        '.$this->dao->quote(trim($arr['password'])).',
        '.$this->dao->quote(trim($arr['sex'])).',
        '.$this->dao->quote(trim($arr['birthday'])).',
        '.$this->dao->quote(SysUtil::safeString($arr['school'])).',
        '.$this->dao->quote(SysUtil::safeString($arr['professional'])).',
        '.$this->dao->quote(SysUtil::safeString($arr['level_school'])).',
        '.$this->dao->quote(SysUtil::safeString($arr['graduation'])).',
        '.$this->dao->quote(SysUtil::safeString($arr['phone'])).',
        '.$this->dao->quote(SysUtil::safeString($arr['mail'])).',
        '.$this->dao->quote(trim($arr['formal'])).',
        '.$this->dao->quote(trim($arr['create_time'])).',
        '.$this->dao->quote(SysUtil::safeString($arr['create_name'])).',
        '.$this->dao->quote(trim($arr['xueke'])).',
        '.$this->dao->quote(trim($xueke_name)).'
        )');
        if($this->dao->affectRows()){            
            $teaInfo = $this->dao->getRow('select id,tr_id,te_name from '.$this->jy_tra_teach.' where status =1 order by id desc limit 0,1  ');            
            $text = "欢迎来到“爱提分精英训练营”接下来的一段时间将由帅哥美女培训师们跟大家一起度过。
我们的口号是“不要等到迟暮之时，才后悔没有使出洪荒之力！”";
             $this->dao->execute('insert into '.$this->jy_trajectory.' (te_id,content,type,create_time) values("'.$teaInfo['id'].'","'.$text.'","induction",'.$this->dao->quote(trim($arr['create_time'])).')');  
             //PPT 排课 测试 作业
            
            //$arr['xueke'] = rtrim($arr['xueke'],',');

            $xuekeInfoss = array_filter(explode(',',$arr['xueke']));
            $ands = '';
            foreach($xuekeInfoss as $val){
                //$ands .= " and xueke like '%".$val.",%'";
                $ands .= " and xueke like '%,".$val.",%' or xueke = '".$val.",'";
            }


             //$pptQuery = $this->dao->getAll("select * from ".$this->jy_ppt_management." where tr_id = ".$arr['tr_id']." and xueke in (". $arr['xueke'].") and status = 1 ");
            $pptQuery = $this->dao->getAll("select * from ".$this->jy_ppt_management." where tr_id = ".$arr['tr_id']." ".$ands." and status = 1 ");
             if($pptQuery){
                foreach($pptQuery as $key=>$val){                
                    $pptdeQuery = $this->dao->getAll("select * from ".$this->jy_ppt_detailed." where ppt_id = ".$val['id']." and tr_id = ".$arr['tr_id']." and te_id = ".$teaInfo['id']." and status = 1 ");                
                    if(!$pptdeQuery){
                        if($pptQuery['recommended'] == 1){
                            $status = 1;
                        }elseif($pptQuery['recommended'] == 2){
                            $status = -1;
                        }
                        $this->addPptDetailed($val['id'],$val['tr_id'],$teaInfo['id'],$teaInfo['te_name'],$arr['create_time'],$status);
                    }
                }
             }
             
             //作业
             $homeQuery = $this->dao->getAll("select * from ".$this->jy_homework." where tr_id  = ".$arr['tr_id']." ".$ands." and status = 1 ");
             
             if($homeQuery){
                foreach($homeQuery as $key=>$val){                
                    $homedeQuery = $this->dao->getAll("select * from ".$this->jy_homework_detailed." where hw_id = ".$val['id']." and tr_id = ".$arr['tr_id']." and te_id = ".$teaInfo['id']." and status = 1 ");
                    if(!$homedeQuery){
                        if($homedeQuery['recommended'] == 1){
                            $status = 1;
                        }elseif($homedeQuery['recommended'] == 2){
                            $status = -1;
                        }else{
                            $status = 1;
                        }
                        $this->addHomeDetailed($val['id'],$val['tr_id'],$teaInfo['id'],$teaInfo['te_name'],$arr['create_time'],$status);
                    }
                }
             }
            
             //排课 
             //echo "select * from ".$this->jy_arranging." where tr_id  = ".$arr['tr_id']." and xueke  like '%".$arr['xueke']."%' and status = 1 ";exit;
             $arrangingQuery = $this->dao->getAll("select * from ".$this->jy_arranging." where tr_id  = ".$arr['tr_id']." ".$ands." and status = 1 ");   
             //var_dump($arrangingQuery);exit;          
             if($arrangingQuery){
                foreach($arrangingQuery as $key=>$val){                
                    $signdeQuery = $this->dao->getAll("select * from ".$this->jy_tra_sign." where ar_id = ".$val['id']." and tr_id = ".$arr['tr_id']." and te_id = ".$teaInfo['id']." and status = 1 ");
                    if(!$signdeQuery){
                        if($signdeQuery['recommended'] == 1){
                            $status = 1;
                        }elseif($signdeQuery['recommended'] == 2){
                            $status = -1;
                        }else{
                            $status = 1;
                        }
                        $this->addSignDetailed($val['tr_id'],$val['id'],$teaInfo['id'],$teaInfo['te_name'],$val['ar_start_time'],$val['ar_end_time'],$status);
                    }
                }
             }
             //测试
             $testQuery = $this->dao->getAll("select * from ".$this->jy_test_management." where tr_id  = ".$arr['tr_id']." ".$ands."  and status = 1 ");
             if($testQuery){
                foreach($testQuery as $key=>$val){                
                    $testdeQuery = $this->dao->getAll("select * from ".$this->jy_test_detailed." where ts_id = ".$val['id']." and tr_id = ".$arr['tr_id']." and te_id = ".$teaInfo['id']." and status = 1 ");
                    if(!$testdeQuery){ 
                        if($testdeQuery['recommended'] == 1){
                            $status = 1;
                        }elseif($testdeQuery['recommended'] == 2){
                            $status = -1;
                        }else{
                            $status = 1;
                        }
                        $this->addTestDetailed($val['id'],$val['tr_id'],$teaInfo['id'],$teaInfo['te_name'],$arr['create_time'],$status);
                    }
                }
             }
                 
            
            return true;
        }
        return false;
    }
    //PPT
    public function addPptDetailed($ppt_id,$tr_id,$te_id,$te_name,$create_time,$status){
       return  $this->dao->execute('insert into '.$this->jy_ppt_detailed.' (ppt_id,tr_id,te_id,te_name,create_time,status) value (
                                        '.$this->dao->quote(SysUtil::safeString($ppt_id)).',
                                        '.$this->dao->quote(SysUtil::safeString($tr_id)).',
                                        '.$this->dao->quote(SysUtil::safeString($te_id)).',
                                        '.$this->dao->quote(SysUtil::safeString($te_name)).',
                                        '.$this->dao->quote(SysUtil::safeString($create_time)).',
                                        '.$this->dao->quote(SysUtil::safeString($status)).'
                        )');
    }
    //作业-答题
    public function addHomeDetailed($hw_id,$tr_id,$te_id,$te_name,$create_time,$status){
       return $this->dao->execute('insert into '.$this->jy_homework_detailed.' (hw_id,tr_id,te_id,te_name,create_time,status) value (
                                '.$this->dao->quote(SysUtil::safeString($hw_id)).',
                                '.$this->dao->quote(SysUtil::safeString($tr_id)).',
                                '.$this->dao->quote(SysUtil::safeString($te_id)).',
                                '.$this->dao->quote(SysUtil::safeString($te_name)).',
                                '.$this->dao->quote(SysUtil::safeString($create_time)).',
                                '.$this->dao->quote(SysUtil::safeString($status)).'                                
                )');
    }
    //排课-上课
    public function addSignDetailed($tr_id,$ar_id,$te_id,$te_name,$shangke_time,$xiake_time,$status){
       return $this->dao->execute('insert into '.$this->jy_tra_sign.' (tr_id,ar_id,te_id,te_name,shangke_time,xiake_time,status) values (
                                   "'.$tr_id.'",
                                   "'.$ar_id.'",
                                   "'.$te_id.'",
                                   "'.$te_name.'",
                                   "'.$shangke_time.'",
                                   "'.$xiake_time.'",
                                   "'.$status.'")'
       ); 
    }
    //测试-答题
    public function addTestDetailed($ts_id,$tr_id,$te_id,$te_name,$create_time,$status){
        return $this->dao->execute('insert into '.$this->jy_test_detailed.' (ts_id,tr_id,te_id,te_name,create_time,status) value (
                                        '.$this->dao->quote(SysUtil::safeString($ts_id)).',
                                        '.$this->dao->quote(SysUtil::safeString($tr_id)).',
                                        '.$this->dao->quote(SysUtil::safeString($te_id)).',
                                        '.$this->dao->quote(SysUtil::safeString($te_name)).',
                                        '.$this->dao->quote(SysUtil::safeString($create_time)).',
                                        '.$this->dao->quote(SysUtil::safeString($status)).'
                                        
                        )');
    }
    
    
    
    public function editTeach($arr){
        $xueke_name = rtrim($this->getXuekeName($arr['xueke']),',');
       $this->dao->execute('update '.$this->jy_tra_teach.' set 
                        te_name ='.$this->dao->quote(SysUtil::safeString($arr['te_name'])).',
                        password='.$this->dao->quote(SysUtil::safeString($arr['password'])).',
                        sex = '.$this->dao->quote(trim($arr['sex'])).',
                        birthday = '.$this->dao->quote(trim($arr['birthday'])).',
                        school = '.$this->dao->quote(SysUtil::safeString($arr['school'])).',
                        professional = '.$this->dao->quote(SysUtil::safeString($arr['professional'])).',
                        level_school = '.$this->dao->quote(SysUtil::safeString($arr['level_school'])).',
                        graduation = '.$this->dao->quote(SysUtil::safeString($arr['graduation'])).',
                        phone = '.$this->dao->quote(SysUtil::safeString($arr['phone'])).',
                        mail = '.$this->dao->quote(SysUtil::safeString($arr['mail'])).',
                        formal = '.$this->dao->quote(trim($arr['formal'])).',
                        update_time = '.$this->dao->quote(trim($arr['update_time'])).',
                        xueke = '.$this->dao->quote(trim($arr['xueke'])).',
                        through = '.$this->dao->quote(trim($arr['through'])).',
                        status = '.$this->dao->quote(trim($arr['status'])).',
                        xueke_name = '.$this->dao->quote(trim($xueke_name)).' 
                        where id = '.$arr['te_id'].'');
		if($this->dao->affectRows()){
		    if($arr['through'] == 1 || $arr['through'] == 2 ){
                //$teaInfo = $this->dao->getRow('select id,tr_id,te_name from '.$this->jy_tra_teach.' where status =1 order by id desc limit 0,1  ');                
                if($arr['through'] == 1){
                    $text = "恭喜您！经过不懈努力，成功通过了“爱提分精英训练营”的重重考核，成为爱提分一员！还记得当初的那句话吗“不要等到迟暮之时，才后悔没有使出洪荒之力！” ";
                }elseif($arr['through'] == 2){               
                    $text = "很遗憾，您没有通过本次“爱提分精英训练营”的考核！条条大路通罗马相信属于你的路还有很多条不要气馁加油！";
                }            
                $this->dao->execute('insert into '.$this->jy_trajectory.' (te_id,content,type,create_time) values("'.$arr['te_id'].'","'.$text.'","through",'.$this->dao->quote(trim($arr['update_time'])).')');           
            
		    }
            
            return true;
		}
		return false;
    }
    
    public function get_OneTeacht($id){
        $strQuery = 'select * from '.$this->jy_tra_teach.' where 1=1';
        if(!empty($id)){
            $strQuery .=' and id = '.$this->dao->quote($id);
        }
        return $this->dao->getRow($strQuery);
    }
    
    public function deleteDictByID($id) {
        $traTeeachInfo = $this->dao->getAll('select * from '.$this->jy_tra_teach.' where tr_id = '.$id.' and status = 1');
        if(!empty($traTeeachInfo)){
            return 'number';
        }else {
            return $this->dao->execute('UPDATE ' . $this->jy_training . ' SET status = -1 WHERE id = ' . $this->dao->quote($id));
        }
	}
    
    public function deleteTeachByID($id,$trid){
       $result =  $this->dao->execute ( 'UPDATE ' . $this->jy_tra_teach . ' SET status = -1 WHERE id = ' . $this->dao->quote ( $id ) );
       if($result){
            $this->dao->execute ( 'UPDATE ' . $this->jy_tra_sign . ' SET status = -1 WHERE te_id = ' . $this->dao->quote ( $id ));
            $this->dao->execute ('UPDATE ' . $this->jy_homework_detailed . ' SET status = -1 WHERE te_id = ' . $this->dao->quote ( $id ));
            $this->dao->execute ('UPDATE ' . $this->jy_ppt_detailed . ' SET status = -1 WHERE te_id = ' . $this->dao->quote ( $id ));            
            $this->dao->execute ('UPDATE ' . $this->jy_test_detailed . ' SET status = -1 WHERE te_id = ' . $this->dao->quote ( $id ));
            return true;            
       }else{
            return false;
       }
    }
    
    public function get_dictList($arr){
        //$vip_dict_grade  $vip_dict_subject
		$strQuery = 'SELECT dg.title as nianji,ds.id,ds.grade_id,ds.title FROM '.$this->vip_dict_grade.' dg, '.$this->vip_dict_subject.' ds  WHERE ds.status =1 and dg.id=ds.grade_id order by ds.id ';
		return $this->dao->getAll($strQuery);
	}
    
    public function getKaopingList(){
        $strQuery = 'select tt.id,tt.te_name,tt.xueke,tr.id as tr_id,tr.tr_name,tr.tr_audit_str from '.$this->jy_training.' tr, '.$this->jy_tra_teach.' tt where tr.id = tt.tr_id and tr.status = 1 and tt.status =1 ';
        return $this->dao->getRow($strQuery);
    }
    public function getKaopingOneList($te_id){
        $strQuery = 'select tt.id,tt.te_name,tt.xueke,tr.id as tr_id,tr.tr_name,tr.tr_audit_str from '.$this->jy_training.' tr, '.$this->jy_tra_teach.' tt where tr.id = tt.tr_id and tr.status = 1 and tt.id='.$te_id.' and tt.status =1 ';
        return $this->dao->getRow($strQuery);
    }
    
    public function getXueKeInfo($xuekeid)
    {
        $xuekeid = rtrim($xuekeid,',');
        $xuekeInfo = $this->dao->getAll('select dg.title as ninaji, ds.title from '.$this->vip_dict_grade.' dg, '.$this->vip_dict_subject.' ds where ds.status =1 and dg.id=ds.grade_id and ds.id in('.$xuekeid.') ');
        $xuekeName = '';
        foreach($xuekeInfo as $k=>$v){
            $xuekeName .= $v['ninaji'].$v['title'].",";
        }
        return $xuekeName;
    }
    
    public function addReviewRecords($arr){
        if($arr['fenshu'] == '' && $arr['paiming'] == ''){
           $strQuery = 'insert into '.$this->jy_review_records.' (tr_id,tt_id,comments,create_time,create_name) values(
                    '.$this->dao->quote(trim($arr['tr_id'])).',
                    '.$this->dao->quote(trim($arr['id'])).',
                    '.$this->dao->quote(trim($arr['pingyu'])).',
                    '.$this->dao->quote(trim($arr['create_time'])).',
                    '.$this->dao->quote(trim($arr['create_name'])).'
            )'; 
            $text = $arr['pingyu'];
            
        }else{
            $strQuery = 'insert into '.$this->jy_review_records.' (tr_id,tt_id,comments,test_ci,test_time,test_score,test_level,test_comments,create_time,create_name) values(
                    '.$this->dao->quote(trim($arr['tr_id'])).',
                    '.$this->dao->quote(trim($arr['id'])).',
                    '.$this->dao->quote(SysUtil::safeString($arr['pingyu'])).',
                    '.$this->dao->quote(SysUtil::safeString($arr['ci'])).',
                    '.$this->dao->quote(SysUtil::safeString($arr['time'])).',
                    '.$this->dao->quote(trim($arr['fenshu'])).',
                    '.$this->dao->quote(trim($arr['paiming'])).',
                    '.$this->dao->quote(SysUtil::safeString($arr['fen_pingyu'])).',
                    '.$this->dao->quote(trim($arr['create_time'])).',
                    '.$this->dao->quote(trim($arr['create_name'])).'
            )';
            $text = $arr['fen_pingyu'];
        }
        
        $this->dao->execute($strQuery);
        if($this->dao->affectRows()){
             $this->dao->execute('insert into '.$this->jy_trajectory.' (te_id,content,type,create_time) values("'.$arr['id'].'","'.$text.'","dianping",'.$this->dao->quote(trim($arr['create_time'])).')');
            
            return true;
        }
        return false;   
        
    }    
    
    public function getTeachInfo($id){
        $list =  $this->dao->getAll('select * from '.$this->jy_tra_teach.' where id = '.$id.' and status =1 ');
        foreach($list as  $key=>$val){
            if($val['sex'] == 1 ){
                $list[$key]['sex_name']= '男';    
            }elseif($val['sex'] == 2){
                $list[$key]['sex_name']= '女';
            }
            
            if($val['xueke']){
                $list[$key]['xueke_name'] = rtrim($this->getXuekeName($val['xueke']),',');    
            } 
            
            if($val['formal'] == 1){
                $list[$key]['formal_name'] = '全职';
            }elseif($val['formal'] == 2){
                $list[$key]['formal_name'] = '兼职';
            }
            
            if($val['through'] == 1 ){
                $list[$key]['through_name'] = '是';
            }elseif($val['through'] == 2){
                $list[$key]['through_name'] ='否';
            } 
            
            if($val['status'] == 0 ){
                $list[$key]['status_name'] = '是';
            }elseif($val['status'] == 1){
                $list[$key]['status_name'] ='否';
            }
            $list[$key]['trInfo'] = $this->getTrainingInfo($val['tr_id']);
            
        }
        return $list;
    }
    public function getTrainingInfo($id){
        return  $this->dao->getRow('select * from '.$this->jy_training.' where status =1 and id = '.$id);
    }
    public function getFenInfo($id){        
        
        $result = $this->dao->getAll('select tr.id,tr.tr_audit_str from '.$this->jy_training.' tr,'.$this->jy_tra_teach.' tt  where tt.id='.$id.' and tt.tr_id=tr.id  and  tr.status = 1 ');  
        //var_dump($result);exit;      
        $levelList = unserialize($result[0]['tr_audit_str']);
        
        foreach($levelList as $key=>$val){
            $sql = 'select id,test_ci,test_time,test_score,test_level from '.$this->jy_review_records.' where  test_time = "'.$val['time'].'" and status =1 order by id desc limit 0,1 ';
            $fenInfo[] = $this->dao->getAll($sql);                       
        }
        foreach($fenInfo as $key=>$val){
            foreach($val as $k=>$v){
                foreach($levelList as $t=>$s){                  
                  if($v['test_time'] == $s['time']){
                      $fenInfo[$key][$k]['shichang'] = $s['long'];  
                      $fenInfo[$key][$k]['zongfen'] = $s['score'];  
                  }  
                }
            }
        }
        
        return $fenInfo;
    }
    
    public function getReviewRecords($id){
        return $this->dao->getAll('select * from '.$this->jy_review_records.' where tt_id = '.$id.' and status =1 ');
    }
    
    public function getPoperPointList($id = ''){
        
        if(!empty($id)){
            $result =  $this->dao->getAll('select * from '.$this->jy_ppt_management.' where id = '.$id.' and status = 1  and tr_id != "" ');    
        }else{
            $result =  $this->dao->getAll('select * from '.$this->jy_ppt_management.' where status = 1 and tr_id != ""  ');
        }        
        
        foreach($result as $key=>$val){
            $resu = $this->dao->getOne('select tr_name from '.$this->jy_training.' where id = '.$val['tr_id'].' and status = 1 ');
            
            $result[$key]['tr_name'] = $resu;
            
            $pptFile = $this->dao->getAll('select id from '.$this->jy_ppt_fileurl.' where ppt_id = '.$val['id'].' and status =1 ');
            
            if(empty($pptFile)){
                $result[$key]['ppt_url_name'] ='<span style="color:red" >未上传</span>';
            }else{
                $result[$key]['ppt_url_name'] ='<span style="color:green" >已上传</span>';
            }
            
            
            $val['xueke']=rtrim($val['xueke'],',');
           
            if($val['xueke']){
                $result[$key]['xueke_name'] = rtrim($this->getXuekeName($val['xueke']),',');    
            } 
            
            
            if($val['recommended'] == 1 ){
                $result[$key]['recommended_name'] = '是';
            }elseif($val['recommended'] == 2){
                $result[$key]['recommended_name'] ='否';
            }
            
            //$result[$key]['upimg'] = '<a onclick="javascript: upimg('.$val['id'].')" >上传图片</a>';  
            //$result[$key]['upimg'] = '<a href="/vip/vipTraining/addPowerPointImg?id='.$val['id'].'" >上传图片</a>';
        }
        
        return $result;
    }
    
    public function getPptFile($id){
        return $this->dao->getAll('select * from '.$this->jy_ppt_fileurl.' where ppt_id = '.$id.' and status = 1 ');
    }
    
    public function getTrNameInfo(){
        return $this->dao->getAll('select * from '.$this->jy_training.' where status =1 ');    
    }
    
    public function addPowerPoint($arr){        
        if($arr['upfile_name'] != ''){
            $strQuery = 'insert into '.$this->jy_ppt_management.' (pt_name,tr_id,xueke,ppt_url,recommended,create_time,create_name) values(
                    '.$this->dao->quote(SysUtil::safeString($arr['pt_name'])).',
                    '.$this->dao->quote(trim($arr['tr_id'])).',
                    '.$this->dao->quote(SysUtil::safeString($arr['xueke'])).',
                    '.$this->dao->quote(SysUtil::safeString($arr['upfile_name'])).',
                    '.$this->dao->quote(SysUtil::safeString($arr['recommended'])).',
                    '.$this->dao->quote(SysUtil::safeString($arr['create_time'])).',
                    '.$this->dao->quote(SysUtil::safeString($arr['create_name'])).'
            )';
        }else{           
            $strQuery = 'insert into '.$this->jy_ppt_management.' (pt_name,tr_id,xueke,recommended,create_time,create_name) values(
                    '.$this->dao->quote(SysUtil::safeString($arr['pt_name'])).',
                    '.$this->dao->quote(trim($arr['tr_id'])).',
                    '.$this->dao->quote(SysUtil::safeString($arr['xueke'])).',
                    '.$this->dao->quote(SysUtil::safeString($arr['recommended'])).',
                    '.$this->dao->quote(SysUtil::safeString($arr['create_time'])).',
                    '.$this->dao->quote(SysUtil::safeString($arr['create_name'])).'
            )';
            
        }
         
       // echo $strQuery;exit;
        $this->dao->execute($strQuery);
        if($this->dao->affectRows()){
            $lastOne = $this->dao->getOne('select id from '.$this->jy_ppt_management.' where status = 1 order by id desc limit 0,1');            
            $fileName =explode(',',$arr['upfile_name']);
            $fileName = array_filter($fileName);
            foreach($fileName as $key=>$val){
                $this->dao->execute('insert into '.$this->jy_ppt_fileurl.' (ppt_id,fileurl,create_time) values(
                    '.$this->dao->quote(SysUtil::safeString($lastOne)).',
                    '.$this->dao->quote(trim($val)).',
                    '.$this->dao->quote(SysUtil::safeString($arr['create_time'])).'
                )');            
            }
            
            if($arr['recommended'] == 1){  
                if($arr['selAll'] == 'on'){
                    $teachInfo = $this->dao->getAll('select id,tr_id,te_name from '.$this->jy_tra_teach.' where status =1 and tr_id = '.$arr['tr_id']);    
                }else{
                    $arr['xueke'] = rtrim($arr['xueke'],',');
                    $teachInfo = $this->dao->getAll('select id,tr_id,te_name from '.$this->jy_tra_teach.' where status =1 and tr_id = '.$arr['tr_id']." and xueke in (".$arr['xueke'].")");     
                }
                 foreach($teachInfo as $key=>$val){
                    $this->addPptDetailed($lastOne,$val['tr_id'],$val['id'],$val['te_name'],$arr['create_time']);
                 }    
                   
            }
            
            
            return true;
        }
        return false; 
    }
    
    public function editPowerPoint($arr){        
        
        if(!empty($arr['id']) && !empty($arr['trid']) && !empty($arr['recommended'])){
            $oldResult = $this->dao->getRow("select * from ".$this->jy_ppt_management." where id = ".$arr['id']." and status = 1" );
            $result = $this->dao->execute('update '.$this->jy_ppt_management.' set  recommended = '.$arr['recommended'].',pt_name = "'.$arr['pt_name'].'" where id ='.$arr['id']);            
            if($oldResult['recommended'] == 1 && $arr['recommended'] == 2){                
                $updetResult = $this->dao->execute('update '.$this->jy_ppt_detailed.' set status = -1 where ppt_id ='.$arr['id'].' and tr_id='.$arr['trid']);
            }elseif($oldResult['recommended'] == 2 && $arr['recommended'] == 1){
                $updetResult = $this->dao->execute('update '.$this->jy_ppt_detailed.' set status = 1 where ppt_id ='.$arr['id'].' and tr_id='.$arr['trid']);
            }
            if($arr['upfile_name']){
                $fileName =explode(',',$arr['upfile_name']);
                $fileName = array_filter($fileName);
                foreach($fileName as $key=>$val){
                    $this->dao->execute('insert into '.$this->jy_ppt_fileurl.' (ppt_id,fileurl,create_time) values(
                        '.$this->dao->quote(SysUtil::safeString($arr['id'])).',
                        '.$this->dao->quote(trim($val)).',
                        '.$this->dao->quote(SysUtil::safeString($arr['create_time'])).'
                    )');            
                }
            }                     
            if($updetResult){
                return true;
            }else{
                return false;
            }            
        }else{
            return false;
        }
       
    }
    
    public function deletePowerPointByID($id){
        $result = $this->dao->execute ( 'UPDATE ' . $this->jy_ppt_management . ' SET status = -1 WHERE id = ' . $this->dao->quote ( $id ) );
        if($result){
            $updetResult = $this->dao->execute('update '.$this->jy_ppt_detailed.' set status = -1 where ppt_id ='.$id);
            if($updetResult){
                return true;
            }else{
                return false;
            }
         }
        
        
    }
    
    public function delPptFileId($arr){
        return $this->dao->execute('update '.$this->jy_ppt_fileurl.' set status = -1  where id = '.$this->dao->quote($arr['id']).' and ppt_id = '.$this->dao->quote($arr['ppt_id']) );
    }
    
    public function addArranging($arr){
        $this->dao->execute('insert into '.$this->jy_arranging.' (ar_name,ar_teacher,tr_id,xueke,ar_start_time,ar_end_time,class_address,create_time,create_name) values(
                            '.$this->dao->quote(SysUtil::safeString($arr['keName'])).',
                            '.$this->dao->quote(SysUtil::safeString($arr['teac_name'])).',
                            '.$this->dao->quote(SysUtil::safeString($arr['tr_id'])).',
                            '.$this->dao->quote(SysUtil::safeString($arr['xueke'])).',
                            '.$this->dao->quote(SysUtil::safeString($arr['start'])).',
                            '.$this->dao->quote(SysUtil::safeString($arr['end'])).',
                            '.$this->dao->quote(SysUtil::safeString($arr['cl_address'])).',
                            '.$this->dao->quote(SysUtil::safeString($arr['create_time'])).',
                            '.$this->dao->quote(SysUtil::safeString($arr['create_name'])).'
                            )' 
                            );
        if($this->dao->affectRows()){         
            $arInfo = $this->dao->getRow('select id from '.$this->jy_arranging.' where status =1 order by id desc limit 0,1');            
            $xuekeName = explode(',',$arr['xueke']);      
            $xuekeName = array_filter($xuekeName);                  
            //$teaInfo = array();
            foreach($xuekeName as $val){
                //echo 'select id,tr_id,te_name from '.$this->jy_tra_teach.' where status =1 and tr_id='.$this->dao->quote(SysUtil::safeString($arr['tr_id'])).' and xueke like "%'.$val.',%" order by id ';
                $teaInfo[] = $this->dao->getAll('select id,tr_id,te_name from '.$this->jy_tra_teach.' where status =1 and tr_id='.$this->dao->quote(SysUtil::safeString($arr['tr_id'])).' and xueke like "%'.$val.',%" order by id ');                
            }
            $new_array = array();
            foreach($teaInfo as $v){
                foreach($v as $s){                                                
                    $new_array[$s['te_name']]=1;
                }
            }
                      
            foreach($new_array as $u=>$v){                
            $te[] = $this->dao->getRow("select tr_id,id,te_name from ".$this->jy_tra_teach." where status =1 and te_name ='".$u."' and  tr_id = ".$arr['tr_id']);
            }           
            if($te){
                $status = 1;
                foreach($te as $key=>$val){                
                    $this->addSignDetailed($val['tr_id'],$arInfo['id'],$val['id'],$val['te_name'],$arr['start'],$arr['end'],$status);
                                          
                }                    
            }
            return true;            
            
        }
        return false;                           
        
        
    }
    
    public function getArranging($user_name,$start,$end){ 
        //$userSubjectsInfo = $this->dao2->getRow('select * from '.$this->vp_user_subjects.' where user_key = "Employee-'.$user_name.'"');
        //print_r($userSubjectsInfo);exit;
      return $this->dao->getAll('select * from '.$this->jy_arranging.' where status = 1 and  ar_start_time >='.$this->dao->quote(date('Y-m-d H:i:s',$start)).' and ar_end_time < '.$this->dao->quote(date('Y-m-d H:i:s',$end)).' and  create_name = "'.$user_name.'"  or ar_name  like "%公共%"   and status =1  ');


    }
    
    public function getSignArrangingInfo(){
        $result = $this->dao->getAll('select * from '.$this->jy_arranging.' where status =1 and tr_id != "" order by create_time DESC ');
        foreach($result as $key=>$val){
            $trInfo = $this->dao->getALL('select * from '.$this->jy_training.' where id ='.$val['tr_id'].' and status = 1 ');
            
            $result[$key]['tr_name'] = $trInfo[0]['tr_name'];
            
            $result[$key]['tr_time'] = $trInfo[0]['tr_start_time']." ".$trInfo[0]['exam_tr_start_time']." — ".$trInfo[0]['tr_end_time']." ".$trInfo[0]['exam_tr_end_time'];
            $zong = $this->dao->getRow('select count(*) as con from '.$this->jy_tra_teach.' where tr_id = '.$trInfo[0]['id'].' and status = 1');
            $result[$key]['zongrenshu'] = $zong['con'];   
            //$tong = $this->dao->getRow('select count(*) as con from '.$this->jy_tra_teach.' where tr_id = '.$trInfo[0]['id'].' and status =1 and through = 1  ');
            //$result[$key]['tongguo'] = $tong['con'];
            //$result[$key]['tr_audit_num'] = '<span style="color:red;">'.$trInfo[0]['tr_audit_num'].'</span>'; 
            
        }
      // var_dump($result);exit;
        return $result;
    }
    
    public function getArrangingInfoId($id){
        //echo 'select * from '.$this->jy_arranging.' where status = 1 and id='.$id;exit;
        return $this->dao->getRow('select * from '.$this->jy_arranging.' where status = 1 and id='.$id);
    }
    public function editArrangingId($arr){
        //var_dump($arr);exit;
         $this->dao->execute('update '.$this->jy_arranging.' set 
                        ar_start_time ='.$this->dao->quote(SysUtil::safeString($arr['start'])).',
                        ar_end_time = '.$this->dao->quote(SysUtil::safeString($arr['end'])).',
                        ar_name = '.$this->dao->quote(SysUtil::safeString($arr['title'])).',
                        ar_teacher = '.$this->dao->quote(SysUtil::safeString($arr['ar_teacher'])).',
                        class_address = '.$this->dao->quote(SysUtil::safeString($arr['class_address'])).',
                        create_time = '.$this->dao->quote(SysUtil::safeString($arr['create_time'])).',
                        create_name = '.$this->dao->quote(SysUtil::safeString($arr['create_name'])).'
                        where id = '.$arr['id'].'');
		if($this->dao->affectRows()){
		      $this->dao->execute('update '.$this->jy_tra_sign.' set shangke_time= "'.$arr['start'].'", xiake_time ="'.$arr['end'].'" where ar_id = '.$arr[id].' and status = 1');
			return true;
		}
		return false;
        
    }
    
    public function getTestList(){        
        $result = $this->dao->getAll('select * from '.$this->jy_test_management.' where status =1 ');
        
        foreach($result as $key=>$val){
            $val['xueke'] = rtrim($val['xueke'], ",");
            if($val['xueke']){
                $result[$key]['xueke_name'] = rtrim($this->getXuekeName($val['xueke']),',');    
            } 
           
            if($val['recommended'] == 1 ){
                $result[$key]['recommended_name'] = '是';
            }elseif($val['recommended'] == 2){
                $result[$key]['recommended_name'] ='否';
            }
            if($val['tr_id']){
               $result[$key]['tr_name'] = $this->dao->getOne('select tr_name from '.$this->jy_training.' where id = '.$val['tr_id'].' and status =1 ');
               $result[$key]['zongrenshu'] = $this->dao->getOne('select count(*) from '.$this->jy_tra_teach.' where tr_id ='.$val['tr_id'].' and status = 1 ');                
            }
            if(!empty($val['zujuan'])){
                $result[$key]['zujuan'] = '<span style="color:green">已组卷</span>';
            }else{
                $result[$key]['zujuan'] = '<span style="color:red">未组卷</span>';
            }
            $wan =  $this->dao->getRow('select count(*) as con from '.$this->jy_test_detailed.' where recommended = 1 and  ts_id = '.$val['id'].' and tr_id ='.$val['tr_id'].'  and status =1 ');            
            $result[$key]['wan'] = $wan['con'];
            $wei = $this->dao->getRow('select count(*) as con from '.$this->jy_test_detailed.' where recommended = 2 and   ts_id = '.$val['id'].'  and tr_id ='.$val['tr_id'].' and status =1 ');
            $result[$key]['wei'] = $wei['con'];
            
            
           $result[$key]['chakan'] = '<a onclick="javascript: xiangqing('.$val['id'].')" >查看</a>';    
            
        }
        
        //var_dump($result);exit;
        return $result;
    }
    
    public function addTestManagement($arr){
        //var_dump($arr);exit;
        if($arr['recommended'] == ''){
            $arr['recommended'] = '2';
        }
        $this->dao->execute('insert into '.$this->jy_test_management.' (ts_name,tr_id,zujuan,xueke,recommended,create_time,create_name) values(
                            '.$this->dao->quote(SysUtil::safeString($arr['ts_name'])).',                            
                            '.$this->dao->quote(SysUtil::safeString($arr['tr_id'])).',
                            '.$this->dao->quote(SysUtil::safeString($arr['zujuan'])).',
                            '.$this->dao->quote(SysUtil::safeString($arr['xueke'])).',
                            '.$this->dao->quote(SysUtil::safeString($arr['recommended'])).',
                            '.$this->dao->quote(SysUtil::safeString($arr['create_time'])).',
                            '.$this->dao->quote(SysUtil::safeString($arr['create_name'])).'
                            )' 
                            );           

        if($this->dao->affectRows()){        
                    $status = '';
                    if($arr['recommended'] == 1){
                        $status .= '1';
                    }elseif($arr['recommended'] == 2){
                        $status .= '-1';
                    }elseif($arr['recommended'] == ''){
                        $status .= '-1';
                    }     
                    $tsInfo = $this->dao->getRow('select id from '.$this->jy_test_management.' where status =1 order by id desc limit 0,1');     
                    if($arr['selAll'] == 'on'){
                        $teachInfo = $this->dao->getAll('select id,tr_id,te_name from '.$this->jy_tra_teach.' where status =1 and tr_id = '.$arr['tr_id']);    
                    }else{
                        $arr['xueke'] = rtrim($arr['xueke'],',');
                        $teachInfo = $this->dao->getAll('select id,tr_id,te_name from '.$this->jy_tra_teach.' where status =1 and tr_id = '.$arr['tr_id']." and xueke in (".$arr['xueke'].")");
                    }
                                           
                                      
                    foreach($teachInfo as $key=>$val){
                        $this->dao->execute('insert into '.$this->jy_test_detailed.' (ts_id,tr_id,te_id,te_name,create_time,status) value (
                                        '.$this->dao->quote(SysUtil::safeString($tsInfo['id'])).',
                                        '.$this->dao->quote(SysUtil::safeString($val['tr_id'])).',
                                        '.$this->dao->quote(SysUtil::safeString($val['id'])).',
                                        '.$this->dao->quote(SysUtil::safeString($val['te_name'])).',
                                        '.$this->dao->quote(SysUtil::safeString($arr['create_time'])).',
                                        '.$status.'
                        )');
                
            }
            
            return true;
        }
        return false;  
    }
    
    
    public function getTestOneList($id){
        $result =  $this->dao->getRow('select * from '.$this->jy_test_management.' where id = '.$id.' and status = 1 ');
        if($result['zujuan'] != ''){
            $test = ' select *  from '.$this->vip_archive.' where  id = '.$result['zujuan'].' and  status =1 ';            
            $arInfo = $this->dao->getRow($test);            
            $result['zujuan_name'] .= $arInfo['title'];
            $trInfo = $this->dao->getRow('select tr_name from '.$this->jy_training.' where id='.$result['tr_id'].' and status = 1 ');
            $result['tr_name'] .= $trInfo['tr_name'];   
            $result['xueke_name'] = rtrim($this->getXuekeName($result['xueke']),',');
        }
        return $result;
    }
    
    public function getXuekeName($xueke){
        if($xueke){
                $xueke = rtrim($xueke,',');
                $xuekeInfo = $this->dao->getAll('select dg.title as ninaji, ds.title from '.$this->vip_dict_grade.' dg, '.$this->vip_dict_subject.' ds where ds.status =1 and dg.id=ds.grade_id and ds.id in('.$xueke.') ');
                $xuekeName = '';
                foreach($xuekeInfo as $k=>$v){
                       $xuekeName .= $v['ninaji'].$v['title'].",";
                    }

                    return $xuekeName;

                }
        
    }
   
    public function editTestManagement($arr){       
        if(!empty($arr['id']) && !empty($arr['trid']) && !empty($arr['recommended'])){
            $oldResult = $this->dao->getRow("select * from ".$this->jy_test_management." where id = ".$arr['id']." and status = 1" );
            $result = $this->dao->execute('update '.$this->jy_test_management.' set  recommended = '.$arr['recommended'].',ts_name = "'.$arr['ts_name'].'" where id ='.$arr['id']);
            if($oldResult['recommended'] == 1 && $arr['recommended'] == 2){                
                $updetResult = $this->dao->execute('update '.$this->jy_test_detailed.' set status = -1 where ts_id ='.$arr['id'].' and tr_id='.$arr['trid']);
            }elseif($oldResult['recommended'] == 2 && $arr['recommended'] == 1){
                $updetResult = $this->dao->execute('update '.$this->jy_test_detailed.' set status = 1 where ts_id ='.$arr['id'].' and tr_id='.$arr['trid']);
            }
            if($updetResult){
                return true;
            }else{
                return false;
            }            
        }else{
            return false;
        }
        
            
    }
    
    public function deleteTestByID($id){
         $result =  $this->dao->execute ( 'UPDATE ' . $this->jy_test_management . ' SET status = -1 WHERE id = ' . $this->dao->quote ( $id ) );
         if($result){
            $updetResult = $this->dao->execute('update '.$this->jy_test_detailed.' set status = -1 where ts_id ='.$id);
            if($updetResult){
                return true;
            }else{
                return false;
            }
         }
    }
    
    public function getZuJuanList(){
        $strQuery = 'SELECT * FROM '.$this->vip_archive.'  WHERE status =1 and  title like "%精英计划%" order by id ';//subject_id = 32
        
		return $this->dao->getAll($strQuery);
    }
    
    public function getTestDetailedInfo($id){
        $result = $this->dao->getAll('select * from '.$this->jy_test_detailed.' where ts_id='.$id.' and status =1 ');
        
        foreach($result as $key=>$val){
            if($val['recommended'] == 1 ){
                $result[$key]['recommended_name'] = '<span style="color:green;">已完成</span>';
            }elseif($val['recommended'] == 2 && $val['answer_num'] == 0 ){
                $result[$key]['recommended_name'] ='<span style="color:red;">未开始</span>';
            }elseif($val['recommended'] == 2 && $val['answer_num'] != 0 ){
                $result[$key]['recommended_name'] ='<span style="color:red;">未完成</span>';
            }
        }
        //var_dump($result);exit;
        return $result;
    }
    
    public function getHomeworkList(){
        $result = $this->dao->getAll('select * from '.$this->jy_homework.' where status =1 ');
        
        foreach($result as $key=>$val){
            $val['xueke']=rtrim($val['xueke'],',');
            if($val['xueke']){
                $result[$key]['xueke_name'] = rtrim($this->getXuekeName($val['xueke']),',');    
            } 
           
            if($val['recommended'] == 1 ){
                $result[$key]['recommended_name'] = '是';
            }elseif($val['recommended'] == 2){
                $result[$key]['recommended_name'] ='否';
            }
            if($val['tr_id']){
               $result[$key]['tr_name'] = $this->dao->getOne('select tr_name from '.$this->jy_training.' where id = '.$val['tr_id'].' and status =1 ');
               $result[$key]['zongrenshu'] = $this->dao->getOne('select count(*) from '.$this->jy_tra_teach.' where tr_id ='.$val['tr_id'].' and status = 1 ');                
            }
            if(!empty($val['zujuan'])){
                $result[$key]['zujuan'] = '<span style="color:green">已组卷</span>';
            }else{
                $result[$key]['zujuan'] = '<span style="color:red">未组卷</span>';
            }
            $wan =  $this->dao->getRow('select count(*) as con from '.$this->jy_homework_detailed.' where recommended = 1 and  hw_id = '.$val['id'].' and tr_id ='.$val['tr_id'].'  and status =1 ');            
            $result[$key]['wan'] = $wan['con'];
            $wei = $this->dao->getRow('select count(*) as con from '.$this->jy_homework_detailed.' where recommended = 2 and   hw_id = '.$val['id'].'  and tr_id ='.$val['tr_id'].' and status =1 ');
            
            $result[$key]['wei'] = $wei['con'];            
            $result[$key]['chakan'] = '<a onclick="javascript: xiangqing('.$val['id'].')" >查看</a>';    
            
        }
                
        return $result;
    }   
    
    public function getHomeworkOneList($id){
        $result =  $this->dao->getRow('select * from '.$this->jy_homework.' where id = '.$id.' and status = 1 ');
        if($result['zujuan'] != ''){
            $test = ' select *  from '.$this->vip_archive.' where  id = '.$result['zujuan'].' and  status =1 ';            
            $arInfo = $this->dao->getRow($test);            
            $result['zujuan_name'] .= $arInfo['title'];
            $trInfo = $this->dao->getRow('select tr_name from '.$this->jy_training.' where id ='.$result['tr_id']);
            $result['tr_name'] .=  $trInfo['tr_name']; 
            $result['xueke_name'] = rtrim($this->getXuekeName($result['xueke']),','); 
                      
        }
        return $result;
    }
    
    public function addHomework($arr){
        if($arr['recommended'] == ''){
            $arr['recommended'] = '2';
        }
        $this->dao->execute('insert into '.$this->jy_homework.' (hw_name,tr_id,zujuan,xueke,recommended,create_time,create_name) values(
                            '.$this->dao->quote(SysUtil::safeString($arr['hw_name'])).',                            
                            '.$this->dao->quote(SysUtil::safeString($arr['tr_id'])).',
                            '.$this->dao->quote(SysUtil::safeString($arr['zujuan'])).',
                            '.$this->dao->quote(SysUtil::safeString($arr['xueke'])).',
                            '.$this->dao->quote(SysUtil::safeString($arr['recommended'])).',
                            '.$this->dao->quote(SysUtil::safeString($arr['create_time'])).',
                            '.$this->dao->quote(SysUtil::safeString($arr['create_name'])).'
                            )' 
                            );           

        if($this->dao->affectRows()){     
            $status = '';
            if($arr['recommended'] == 1){
                $status .= '1';
            }elseif($arr['recommended'] == 2){
                $status .= '-1';
            }elseif($arr['recommended'] == ''){
                $status .= '-1';
            }
            $hwInfo = $this->dao->getRow('select id from '.$this->jy_homework.' where status =1 order by id desc limit 0,1');                            
           // $teachInfo = $this->dao->getAll('select id,tr_id,te_name from '.$this->jy_tra_teach.' where status =1 and tr_id = '.$arr['tr_id']);
            
             if($arr['selAll'] == 'on'){
                    $teachInfo = $this->dao->getAll('select id,tr_id,te_name from '.$this->jy_tra_teach.' where status =1 and tr_id = '.$arr['tr_id']);    
             }else{
                    $arr['xueke'] = rtrim($arr['xueke'],',');
                    $teachInfo = $this->dao->getAll('select id,tr_id,te_name from '.$this->jy_tra_teach.' where status =1 and tr_id = '.$arr['tr_id']." and xueke in (".$arr['xueke'].")");
             }       
            foreach($teachInfo as $key=>$val){
                $this->dao->execute('insert into '.$this->jy_homework_detailed.' (hw_id,tr_id,te_id,te_name,create_time,status) value (
                                '.$this->dao->quote(SysUtil::safeString($hwInfo['id'])).',
                                '.$this->dao->quote(SysUtil::safeString($val['tr_id'])).',
                                '.$this->dao->quote(SysUtil::safeString($val['id'])).',
                                '.$this->dao->quote(SysUtil::safeString($val['te_name'])).',
                                '.$this->dao->quote(SysUtil::safeString($arr['create_time'])).',
                                '.$status.'
                )');
            } 
            
            return true;
        }
        return false; 
    }
    
    public function editHomework($arr){
         if(!empty($arr['id']) && !empty($arr['trid']) && !empty($arr['recommended'])){
            $oldResult = $this->dao->getRow("select * from ".$this->jy_homework." where id = ".$arr['id']." and status = 1" );
            $result = $this->dao->execute('update '.$this->jy_homework.' set  recommended = '.$arr['recommended'].',hw_name = "'.$arr['hw_name'].'" where id ='.$arr['id']);
            if($oldResult['recommended'] == 1 && $arr['recommended'] == 2){                
                $updetResult = $this->dao->execute('update '.$this->jy_homework_detailed.' set status = -1 where hw_id ='.$arr['id'].' and tr_id='.$arr['trid']);
            }elseif($oldResult['recommended'] == 2 && $arr['recommended'] == 1){
                $updetResult = $this->dao->execute('update '.$this->jy_homework_detailed.' set status = 1 where hw_id ='.$arr['id'].' and tr_id='.$arr['trid']);
            }
            if($updetResult){
                return true;
            }else{
                return false;
            }            
        }else{
            return false;
        }
        
    } 
    
    public function deleteHomeworkByID($id){
         $result =  $this->dao->execute ( 'UPDATE ' . $this->jy_homework . ' SET status = -1 WHERE id = ' . $this->dao->quote ( $id ) );
         if($result){
            return $this->dao->execute( 'update '.$this->jy_homework_detailed.' set status= -1 where hw_id ='.$this->dao->quote($id));
            
         }
    }
    
    public function getHomeworkDetailedInfo($id){
        $result = $this->dao->getAll('select * from '.$this->jy_homework_detailed.' where hw_id='.$id.' and status =1 ');        
        foreach($result as $key=>$val){
                        
            if($val['recommended'] == 1 ){
                $result[$key]['recommended_name'] = '<span style="color:green;">已完成</span>';
            }elseif($val['recommended'] == 2 && $val['answer_num'] == 0 ){
                $result[$key]['recommended_name'] ='<span style="color:red;">未开始</span>';
            }elseif($val['recommended'] == 2 && $val['answer_num'] != 0 ){
                $result[$key]['recommended_name'] ='<span style="color:red;">未完成</span>';
            }
            
        }        
        return $result;
    }
    
    /*public function getTeachSignOneInfo($category, $currentPage, $pageSize, $sort, $order) {
    	$currentPage = empty ( $currentPage ) ? 1 : $currentPage;
    	$pageSize = empty ( $pageSize ) ? 20 : $pageSize;
    
    	$list = $this->dao->getAll ( 'SELECT *
    								 FROM ' . $this->jy_tra_sign . '
    								 WHERE ar_id = ' . $this->dao->quote ( $category ) . ' AND status = 1
    								 ORDER BY id
    								 limit ' . ($currentPage - 1) * $pageSize . ', ' . $pageSize );
        
    
    	$total = 0;
    	if ($list) {
    		$row = $this->dao->getRow ( 'SELECT COUNT(*) AS cnt
    								 FROM ' . $this->jy_tra_sign . '
    								 WHERE ar_id = ' . $this->dao->quote ( $category ) . ' AND status = 1' );
    		if ($row) {
    			$total = $row ['cnt'];
    		}
    	}        
        
        foreach($list as  $key=>$val){
            if($val['recommended'] == 1 ){
                $list[$key]['recommended_name'] = '<span style="color:green;">已签到</span>';
            }elseif($val['recommended'] == 2){
                $list[$key]['recommended_name'] = '<span style="color:red;">未签到</span>';
            } 
            $trInfo = $this->dao->getRow('select id,tr_name from '.$this->jy_training.' where id ='.$val['tr_id'].' and status =1 ');
            $list[$key]['tr_name'] = $trInfo['tr_name'];
            $teInfo = $this->dao->getRow('select id,te_name from '.$this->jy_tra_teach.' where id ='.$val['te_id'].' and status =1 ');
            $list[$key]['te_name'] = $teInfo['te_name'];
                  
                
        }
    	return array (
    	'total' => $total,
    	'rows' => $list
    	);
	}*/
    
    public function getTeachSignOneInfo($category, $currentPage, $pageSize, $sort, $order,$params) {
    	$currentPage = empty ( $currentPage ) ? 1 : $currentPage;
    	$pageSize = empty ( $pageSize ) ? 20 : $pageSize;
    
        $where = '';
        if($params['kename']){
            //echo 'select tr_id,id,ar_name from '.$this->jy_arranging.' where status=1 and ar_name like "%'.$params['kename'].'%"';
            $arInfo = $this->dao->getAll('select tr_id,id,ar_name from '.$this->jy_arranging.' where status=1 and ar_name like "%'.$params['kename'].'%"');
            $arid= '';
            foreach($arInfo as $key=>$val){
                $arid .= $val['id'].',';
            }
            $arid = rtrim($arid,',');
            if($arid){
                $where .= ' and ar_id in ('.$arid.')';    
            }else{
                $errmsg = true;
            }            
        }
        if($params['tename']){
           $teInfo = $this->dao->getRow('select tr_id,id,te_name from '.$this->jy_tra_teach.' where status=1 and te_name = "'.$params['tename'].'"');          
           if($teInfo['id']){
                 $where .= ' and te_id = '.$teInfo['id'];  
            }else{
                $errmsg = true;
            }                 
        }
        if($errmsg){
            $list[0]['tr_name']  = "<span style='width:200px;color:red'>暂无此内容，请输入其他条件进行查询</span>";
                return array (
            	'total' => 1,
            	'rows' =>  $list   
            	);
        }
        if($params['tetime']){
            $where .= ' and shangke_time like "%'.$params['tetime'].'%"';
            
        }        
      
                                     
    	$list = $this->dao->getAll ( 'SELECT *
    								 FROM ' . $this->jy_tra_sign . '
    								 WHERE tr_id = ' . $this->dao->quote ( $category ) . ' AND status = 1 '.$where.'
    								 ORDER BY ar_id
    								 limit ' . ($currentPage - 1) * $pageSize . ', ' . $pageSize );
        
       
    	$total = 0;
    	if ($list) {
    		$row = $this->dao->getRow ( 'SELECT COUNT(*) AS cnt
    								 FROM ' . $this->jy_tra_sign . '
    								 WHERE tr_id = ' . $this->dao->quote ( $category ) . ' AND status = 1'.$where );
    		if ($row) {
    			$total = $row ['cnt'];
    		}
    	}        
        
        foreach($list as  $key=>$val){
            if($val['recommended'] == 1 ){
                $list[$key]['recommended_name'] = '<span style="color:green;">已签到</span>';
            }elseif($val['recommended'] == 2){
                $list[$key]['recommended_name'] = '<span style="color:red;">未签到</span>';
            } 
            $trInfo = $this->dao->getRow('select id,tr_name from '.$this->jy_training.' where id ='.$val['tr_id'].' and status =1 ');
            $list[$key]['tr_name'] = $trInfo['tr_name'];
            $teInfo = $this->dao->getRow('select id,te_name from '.$this->jy_tra_teach.' where id ='.$val['te_id'].' and status =1 ');
            $list[$key]['te_name'] = $teInfo['te_name'];
            $arInfo = $this->dao->getRow('select id,ar_name,ar_start_time,ar_end_time from '.$this->jy_arranging.' where id = '.$val['ar_id'].' and status = 1');
            $list[$key]['ar_name'] = $arInfo['ar_name'];
            $list[$key]['ar_start_time'] = $arInfo['ar_start_time'];
            $list[$key]['ar_end_time'] = $arInfo['ar_end_time'];
            $list[$key]['shangke'] = $arInfo['ar_start_time'].' ~ '.date('H:i:s',strtotime($arInfo['ar_end_time']));
            $list[$key]['wu'] = $errmsg;     
                
        }
    	return array (
    	'total' => $total,
    	'rows' => $list
    	);
	}
    
    public function getSignSearchNameDate($arr){
        if($arr['tename'] != ''){
            $where .= ' and t.te_name = "'.$arr['tename'].'"';
        }
        if($arr['tetime'] != ''){
            $where .= ' and s.create_date = "'.$arr['tetime'].'"';
        }        
        $result = $this->dao->getAll('select * from '.$this->jy_tra_sign.' s, '.$this->jy_tra_teach.' t where t.status =1 and s.status =1 and s.te_id = t.id and s.tr_id = '.$arr['tr_id'].$where);
        return $result;
    }
    
    public function addTraKaoPing($arr){
       if($arr['selAll'] == 'on'){
            $teachInfo = $this->dao->getAll('select id,tr_id,te_name from '.$this->jy_tra_teach.' where status =1 and tr_id = '.$arr['tr_id']);    
        }else{
            $arr['xueke'] = rtrim($arr['xueke'],',');
                    $teachInfo = $this->dao->getAll('select id,tr_id,te_name from '.$this->jy_tra_teach.' where status =1 and tr_id = '.$arr['tr_id']." and xueke in (".$arr['xueke'].")");
        }
        //$teachInfo = $this->dao->getAll('select id,te_name from '.$this->jy_tra_teach.' where tr_id='.$arr['tr_id'].' and xueke like "%'.$arr['xueke'].'%"');
        if(!empty($teachInfo)){            
            foreach($teachInfo as $key=>$val){
                $this->dao->execute('insert into '.$this->jy_review_records.' (tr_id,tt_id,comments,create_time,create_name) value (
                                '.$this->dao->quote(SysUtil::safeString($arr['tr_id'])).',
                                '.$this->dao->quote(SysUtil::safeString($val['id'])).',
                                '.$this->dao->quote(SysUtil::safeString($arr['pingyu'])).',
                                '.$this->dao->quote(SysUtil::safeString($arr['create_time'])).',
                                '.$this->dao->quote(SysUtil::safeString($arr['create_name'])).'
                )');
                $this->dao->execute('insert into '.$this->jy_trajectory.' (te_id,content,type,create_time) values("'.$val['id'].'","'.$arr['pingyu'].'","dianping",'.$this->dao->quote(trim($arr['create_time'])).')');
                
            }            
            return 'ok';
        }else{
            return 'wu';
        }
    }
    
    public function getExportTeachList($id){
        $list =  $this->dao->getAll('select * from '.$this->jy_tra_teach.' where tr_id ='.$this->dao->quote ( $id ).' and status =1 ');
        foreach($list as  $key=>$val){
            if($val['sex'] == 1 ){
                $list[$key]['sex_name']= '男';    
            }else{
                $list[$key]['sex_name']= '女';
            }
            
            if($val['xueke']){
                $list[$key]['xueke_name'] = rtrim($this->getXuekeName($val['xueke']),',');    
            } 
                    
            
            if($val['formal'] == 1){
                $list[$key]['formal_name'] = '全职';
            }elseif($val['formal'] == 2){
                $list[$key]['formal_name'] = '兼职';
            }
            
            if($val['through'] == 1 ){
                $list[$key]['through_name'] = '是';
            }elseif($val['through'] == 2){
                $list[$key]['through_name'] ='否';
            } 
            
            if($val['status'] == 0 ){
                $list[$key]['status_name'] = '是';
            }elseif($val['status'] == 1){
                $list[$key]['status_name'] ='否';
            }            
          
        }
        return $list;
    }
    
    public function getExportSignList($id){
        $list =  $this->dao->getAll('select * from '.$this->jy_tra_sign.' where tr_id ='.$this->dao->quote ( $id ).' and status =1 ');
        foreach($list as  $key=>$val){
            if($val['recommended'] == 1 ){
                $list[$key]['recommended_name'] = '已签到';
            }elseif($val['recommended'] == 2){
                $list[$key]['recommended_name'] = '未签到';
            } 
            $trInfo = $this->dao->getRow('select id,tr_name from '.$this->jy_training.' where id ='.$val['tr_id'].' and status =1 ');
            $list[$key]['tr_name'] = $trInfo['tr_name'];
            $teInfo = $this->dao->getRow('select id,te_name from '.$this->jy_tra_teach.' where id ='.$val['te_id'].' and status =1 ');
            $list[$key]['te_name'] = $teInfo['te_name'];
            $arInfo = $this->dao->getRow('select id,ar_start_time,ar_end_time from '.$this->jy_arranging.' where id ='.$val['ar_id'].' and status =1 ');                       
            $list[$key]['shangke'] = $arInfo['ar_start_time'].' ~ '.date('H:i:s',strtotime($arInfo['ar_end_time']));
        }
        return $list;
    }

    public function getTrTeachInfo($arrPost){
        $list = $this->dao->getAll('select * from '.$this->jy_tra_teach.' where tr_id = '.$this->dao->quote($arrPost['peixun_name']).' and xueke like "%'.$arrPost['xueke_name'].',%" and status = 1');
        foreach($list as $key=>$val){
            if($val['xueke']){
                $list[$key]['xueke_name'] = rtrim($this->getXuekeName($val['xueke']),',');
            }
        }
        return $list;
    }
    
    public function upTrTeachStatus($teach_arr){
        $where = '';
        foreach($teach_arr as $val){
            $where .=''.$val.',';
        }
        $result = $this->dao->execute('update '.$this->jy_tra_teach.' set status = 0 where status = 1 and id in ('.rtrim($where,',').')');
        if($result){
            return true;
        }else{
            return false;
        }


    }
    
    
     public function getArrSign($subid){    
         $result1 = $this->dao->getAll("select * from ".$this->jy_tra_teach." where tr_id=18 and  xueke='".$subid."'");
           foreach($result1 as $key=>$val){
                
               $teid .= '"'.$val['id'].'",';
               
            }
            $teid = rtrim($teid, ",");

        $result2 = $this->dao->getAll("select * from ".$this->jy_arranging."  a, ".$this->jy_tra_sign." s
where a.id= s.ar_id and s.te_id in (".$teid.")
        and a.tr_id= 18 and a.status =1 and s.status =1");

        return $result2;

    }

    public function upSign($id){
        //echo "update ".$this->jy_tra_sign." set status = -1 where id in (".$id.")";exit;
        $result = $this->dao->execute("update ".$this->jy_tra_sign." set status = -1 where id in (".$id.")");
        return $result;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    //-----------------ends--------------------
    
	public function addKnowledge($knowledge = array()) {
		$parentId = $knowledge ['parent_id'];
		$flag = true;
		$this->dao->execute ( 'begin' ); // 事务开启
		if (! empty ( $parentId )) {
			$sql = 'UPDATE ' . $this->vip_knowledge . ' SET is_leaf = 0 WHERE id = ' . $this->dao->quote ( $parentId ) . ' AND is_leaf = 1';
			if ($this->dao->execute ( $sql ))
			$flag = true;
			else
			$flag = false;
		}

		if ($flag == true) {
			$is_gaosi = $this->getKnowledgeTypeIsGaosiById($knowledge ['knowledgetypeid']);
			$sql2 = 'INSERT INTO ' . $this->vip_knowledge . ' (name, remark, parent_id, analysis, sort, is_leaf, level, is_gaosi) VALUES (' . $this->dao->quote ( $knowledge ['name'] ) . ', ' . $this->dao->quote ( $knowledge ['remark'] ) . ', ' . $this->dao->quote ( $parentId ) . ', ' . $this->dao->quote ( $knowledge ['analysis'] ) . ', ' . $this->dao->quote ( $knowledge ['sort'] ) . ', 1, ' . $this->dao->quote ( $knowledge ['level'] ) . ', ' . $this->dao->quote ( $is_gaosi ) . ')';
			if ($this->dao->execute ( $sql2 )) {
				$id = $this->dao->lastInsertId ();
				$flag = true;
			} else
			$flag = false;
		}

		// 如为父节点则插入知识点属性表
		if (empty ( $parentId ) && $flag == true) {
			$sql3 = 'INSERT INTO ' . $this->vip_knowledge_course_type_rs . ' (knowledge_id, course_type_id) VALUES (' . $this->dao->quote ( $id ) . ', ' . $this->dao->quote ( $knowledge ['coursetypeid'] ) . ')';

			if ($this->dao->execute ( $sql3 ))
			$flag = true;
			else
			$flag = false;
		}

		if ($flag === false)
		$this->dao->execute ( 'rollback' ); // 事务回滚
		else
		$this->dao->execute ( 'commit' ); // 事务提交

		return $flag;
	}
	public function updateKnowledge($knowledge = array()) {
		$knowledgeId = $knowledge ['id'];
		$parentId = $knowledge ['parent_id'];
		$row = $this->dao->getRow ( 'SELECT fn_vip_get_knowledge_child_list(' . $this->dao->quote ( $knowledgeId ) . ') AS sub_knowledge_ids' );

		if ($row) {
			if (in_array ( $parentId, str2arr ( $row ['sub_knowledge_ids'], ',' ) )) {
				return false;
			}
		}

		if (! empty ( $knowledgeId )) {
			$before_parentId = $this->dao->getRow ( 'SELECT `id`,`parent_id` FROM ' . $this->vip_knowledge . ' WHERE id = ' . $this->dao->quote ( $knowledgeId ) ); // 查找修改前的父目录的ID
			if (! empty ( $before_parentId ['parent_id'] )) {
				if ($before_parentId ['parent_id'] != $parentId) {
					$row = $this->dao->getRow ( 'SELECT `id`,`parent_id` FROM ' . $this->vip_knowledge . ' WHERE id != ' . $this->dao->quote ( $knowledgeId ) . ' AND  parent_id = ' . $this->dao->quote ( $before_parentId ['parent_id'] ) );
					if (empty ( $row )) { // 查找对应的父节点除了此节点之外还有没有子节点，如果无则修改叶子节点
						$sql = 'UPDATE ' . $this->vip_knowledge . ' SET is_leaf = 1 WHERE id = ' . $this->dao->quote ( $before_parentId ['parent_id'] );
						$this->dao->execute ( $sql );
					}
					if(!empty($parentId)){
						$parentLevel = $this->dao->getOne('SELECT level FROM '.$this->vip_knowledge.' WHERE id = '.$this->dao->quote($parentId));
						$knowledge['level'] = $parentLevel+1;
					}else{
						$knowledge['level'] = 1;
					}
					
				}
			}
		}

		if (! empty ( $parentId )) {
			$sql = 'UPDATE ' . $this->vip_knowledge . ' SET is_leaf = 0 WHERE id = ' . $this->dao->quote ( $parentId ) . ' AND is_leaf = 1';
			$this->dao->execute ( $sql );
		}

		return $this->dao->execute ( 'UPDATE ' . $this->vip_knowledge . ' SET name = ' . $this->dao->quote ( $knowledge ['name'] ) . ', remark = ' . $this->dao->quote ( $knowledge ['remark'] ) . ', sort = ' . $this->dao->quote ( $knowledge ['sort'] ) . ', parent_id = ' . $this->dao->quote ( $knowledge ['parent_id'] ) . ', analysis = ' . $this->dao->quote ( $knowledge ['analysis'] ) . ',level='.$this->dao->quote ( $knowledge ['level'] ).' WHERE id = ' . $this->dao->quote ( $knowledge ['id'] ) );
	}
	public function getKnowledgeByID($id) {
		return $this->dao->getRow ( 'SELECT a.`id`,
											a.`name`,
											a.`remark`,
											a.`sort`,
											a.`parent_id`,
											a.`analysis`,
											a.`status`,
											a.`level`,
											b.name as parent_name 
									FROM ' . $this->vip_knowledge . ' a 
									LEFT JOIN ' . $this->vip_knowledge . ' b ON a.parent_id = b.id 
									WHERE a.id = ' . $this->dao->quote ( $id ) );
	}
	public function getKnowledgeByIDs($ids) {
		if (empty ( $ids ))
		return array ();

		return $this->dao->getAll ( 'SELECT `id`,
											`name`, 
											`remark`, 
											`sort`,
											`parent_id`,
											`analysis`,
											`status`
									FROM ' . $this->vip_knowledge . ' WHERE id IN ( ' . $ids . ')' );
	}
	public function getKnowledgesByCourseTypeId($courseTypeId) {
		$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND a.course_type_id = ' . $this->dao->quote ( $courseTypeId ) . ' ORDER BY a.sort' );

		$rootIds = arr2nav ( $rows, ',', 'id' );

		$row = $this->dao->getRow ( 'SELECT fn_vip_get_knowledge_child_list(\'' . $rootIds . '\') AS ids' );
		if ($row) {
			$ids = $row ['ids'];
			if ($ids != '$,') {
				$ids = str_replace ( ',', "','", $ids );
				return $this->dao->getAll ( 'SELECT a.`id`,
											a.`name`,
											a.`remark`,
											a.`sort`,
											a.`parent_id`,
											a.`analysis`,
											a.`status`
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE id IN(\'' . $ids . '\') ORDER BY a.sort, a.id' );
			}
			return array ();
		}
		return array ();
	}
	public function deleteKnowledgeByID($id) {
		return $this->dao->execute ( 'UPDATE ' . $this->vip_knowledge . ' SET status = -1 WHERE id = ' . $this->dao->quote ( $id ) );
	}
	public function addQuestionType($data = array()) {
		$flag = false;
		$subjectId = $data ['subjectid'];
		if ($subjectId && ! empty ( $data ['title'] ) && ! empty ( $data ['code'] )) {
			$typeCodeArr = array_filter ( $data ['code'] );
			$titleArr = array_filter ( $data ['title'] );
			$sortArr = array_filter ( $data ['sort'] );
			$flag = true;
			$this->dao->execute ( 'begin' ); // 事务开启
			foreach ( $data ['title'] as $key => $title ) {
				$sql = 'INSERT INTO ' . $this->vip_dict_question_type . ' (subject_id, title, question_type_code, sort) VALUES(' . $this->dao->quote ( $subjectId ) . ',' . $this->dao->quote ( SysUtil::safeString ( $title ) ) . ',' . $this->dao->quote ( $typeCodeArr [$key] ) . ',' . $this->dao->quote ( $sortArr [$key] ) . ')';
				if ($this->dao->execute ( $sql )) {
					$flag = true;
				} else {
					$flag = false;
				}
			}
			if ($flag === false) {
				$this->dao->execute ( 'rollback' ); // 事务回滚
			} else {
				$this->dao->execute ( 'commit' ); // 事务提交
			}
		}
		return $flag;
	}
	public function getQuestionTypeByID($id) {
		return $this->dao->getRow ( 'SELECT  id, subject_id, title, status, sort,question_type_code
									 FROM ' . $this->vip_dict_question_type . '
									 WHERE id = ' . $this->dao->quote ( $id ) );
	}
	public function getQuestionTypesFullBySubjectId($subjectId) {
		return $this->dao->getAll ( 'SELECT a.id, a.code, a.title AS origin_title, CASE WHEN b.title = \'\' THEN a.title ELSE b.title END AS title, CASE WHEN b.id IS NULL THEN 0 ELSE 1 END is_choose
									 FROM ' . $this->vip_dict . ' a
									 LEFT JOIN ( SELECT id, subject_id, question_type_code, title, status, sort FROM ' . $this->vip_dict_question_type . ' WHERE subject_id = ' . $this->dao->quote ( $subjectId ) . ') b ON a.code = b.question_type_code
									 WHERE a.category = \'QUESTION_TYPE\' AND a.status = 1 ORDER BY a.sort, a.id' );
	}
	public function updateQuestionType($questionType = array()) {
		return $this->dao->execute ( 'UPDATE ' . $this->vip_dict_question_type . ' SET title = ' . $this->dao->quote ( $questionType ['title'] ) . ', sort = ' . $this->dao->quote ( $questionType ['sort'] ) . ' WHERE id = ' . $this->dao->quote ( $questionType ['id'] ) );
	}
	public function deleteQuestionTypeByID($id) {
		return $this->dao->execute ( 'DELETE FROM ' . $this->vip_question_type_attr . ' WHERE id = ' . $this->dao->quote ( $id ) );
	}
	public function getDictsAllByCategory($condition) {
		$where = '';
		if ($condition ['cate'] == 'subject' && ! empty ( $condition ['xueke'] )) {
			$where .= ' AND code IN (' . $condition ['xueke'] . ') ';
		}
		if ($condition ['cate'] == 'grade_dept' && ! empty ( $condition ['xuebu'] )) {
			$where .= ' AND code IN (' . $condition ['xuebu'] . ') ';
		}
		return $this->dao->getAll ( 'SELECT `id`,
											`category`,
											`code`,
											`title`,
											`description`,
											`sort`,
											`status`
									 FROM ' . $this->vip_dict . '
									 WHERE category = ' . $this->dao->quote ( $condition ['cate'] ) . ' AND status = 1 ' . $where . ' ORDER BY sort, id' );
	}
	
	public function addDict($dict = array()) {
		return $this->dao->execute ( 'INSERT INTO ' . $this->vip_dict . ' (category, code, title, description, sort) VALUES (' . $this->dao->quote ( $dict ['category'] ) . ', ' . $this->dao->quote ( $dict ['code'] ) . ', ' . $this->dao->quote ( $dict ['title'] ) . ', ' . $this->dao->quote ( $dict ['description'] ) . ', ' . $this->dao->quote ( $dict ['sort'] ) . ')' );
	}
	public function add($dict = array()) {
		switch ($dict ['category']) {
			case 'SUBJECT' :
				$result = $this->dao->execute ( 'INSERT INTO ' . $this->vip_dict_subject . ' (grade_id, title, sort) VALUES(' . $this->dao->quote ( abs ( $dict ['grade_id'] ) ) . ',' . $this->dao->quote ( SysUtil::safeString ( $dict ['title'] ) ) . ',' . $this->dao->quote ( abs ( $dict ['sort'] ) ) . ')' );
				break;
			case 'QUESTION_TYPE' :
				$result = $this->dao->execute ( 'INSERT INTO ' . $this->vip_dict_question_type . ' (subject_id, title, sort) VALUES(' . $this->dao->quote ( abs ( $dict ['subject_id'] ) ) . ',' . $this->dao->quote ( SysUtil::safeString ( $dict ['title'] ) ) . ',' . $this->dao->quote ( abs ( $dict ['sort'] ) ) . ')' );
				break;
			case 'KNOWLEDGE_TYPE' :
				$result = $this->dao->execute('INSERT INTO '. $this->vip_dict_knowledge_type . ' (subject_id, title, sort) VALUES (' . $this->dao->quote ( abs ( $dict ['subject_id'] ) ) . ',' .$this->dao->quote ( SysUtil::safeString ( $dict ['title'] ) ). ' , ' . $this->dao->quote(abs ( $dict ['sort'] )) . ')');
				break;
			case 'COURSE_TYPE' :
				$result = $this->dao->execute ( 'INSERT INTO ' . $this->vip_dict_course_type . ' (subject_id, title, sort, knowledge_type_id) VALUES(' . $this->dao->quote ( abs ( $dict ['subject_id'] ) ) . ',' . $this->dao->quote ( SysUtil::safeString ( $dict ['title'] ) ) . ',' . $this->dao->quote ( abs ( $dict ['sort'] ) ) . ',' . $this->dao->quote ( abs ( $dict ['knowledge_type_id'] ) ) . ')' );
				break;
			default :
				$result = $this->dao->execute ( 'INSERT INTO ' . $this->vip_dict_grade . ' (title,sort) VALUES(' . $this->dao->quote ( SysUtil::safeString ( $dict ['title'] ) ) . ',' . $this->dao->quote ( abs ( $dict ['sort'] ) ) . ')' );
		}

		return $result;
	}
	public function getDictByID($id) {
		return $this->dao->getRow ( 'SELECT id, category, code, title, description, sort, status FROM ' . $this->vip_dict . ' WHERE id = ' . $this->dao->quote ( $id ) );
	}
	public function getDictDataByID($cate, $id) {
		switch ($cate) {
			case 'GRADE_DEPT' :
				return $this->dao->getRow ( 'SELECT id,title,sort FROM ' . $this->vip_dict_grade . ' WHERE id = ' . $this->dao->quote ( abs ( $id ) ) );
				break;
			case 'SUBJECT' :
				return $this->dao->getRow ( 'SELECT id,grade_id,title,sort FROM ' . $this->vip_dict_subject . ' WHERE id = ' . $this->dao->quote ( abs ( $id ) ) );
				break;
			case 'COURSE_TYPE' :
				return $this->dao->getRow ( 'SELECT c.id,c.subject_id,c.title,c.sort,s.grade_id FROM ' . $this->vip_dict_course_type . ' c LEFT JOIN ' . $this->vip_dict_subject . ' s ON c.subject_id = s.id WHERE c.id = ' . $this->dao->quote ( abs ( $id ) ) );
				break;
			case 'QUESTION_TYPE' :
				return $this->dao->getRow ( 'SELECT q.id,q.subject_id,q.title,q.sort,s.grade_id FROM ' . $this->vip_dict_question_type . ' q LEFT JOIN ' . $this->vip_dict_subject . ' s ON q.subject_id = s.id WHERE q.id = ' . $this->dao->quote ( abs ( $id ) ) );
				break;
			case 'KNOWLEDGE_TYPE' :
				return $this->dao->getRow ( 'SELECT kt.id, kt.title, kt.sort FROM ' . $this->vip_dict_knowledge_type . ' kt WHERE kt.id = ' . $this->dao->quote ( abs ( $id ) ) );
				break;
		}
	}
	public function updateDict($dict = array()) {
		return $this->dao->execute ( 'UPDATE ' . $this->vip_dict . ' SET title = ' . $this->dao->quote ( $dict ['title'] ) . ', description = ' . $this->dao->quote ( $dict ['description'] ) . ', sort = ' . $this->dao->quote ( $dict ['sort'] ) . ' WHERE id = ' . $this->dao->quote ( $dict ['id'] ) );
	}
	public function update($dict = array()) {
		$setValues = '';
		switch ($dict ['category']) {
			case 'GRADE_DEPT' :
				$tempTable = $this->vip_dict_grade;
				break;
			case 'SUBJECT' :
				$tempTable = $this->vip_dict_subject;
				$setValues .= ',grade_id = ' . $this->dao->quote ( $dict ['grade_id'] );
				break;
			case 'COURSE_TYPE' :
				$tempTable = $this->vip_dict_course_type;
				$setValues .= ',subject_id = ' . $this->dao->quote ( $dict ['subject_id'] );
				break;
			case 'QUESTION_TYPE' :
				$tempTable = $this->vip_dict_question_type;
				$setValues .= ',subject_id = ' . $this->dao->quote ( $dict ['subject_id'] );
				break;
			case 'KNOWLEDGE_TYPE' :
				$tempTable = $this->vip_dict_knowledge_type;
				break;
		}
		return $this->dao->execute ( 'UPDATE ' . $tempTable . ' SET title = ' . $this->dao->quote ( $dict ['title'] ) . ', sort = ' . $this->dao->quote ( $dict ['sort'] ) . $setValues . ' WHERE id = ' . $this->dao->quote ( $dict ['id'] ) );
	}
	
	
	public function addQuestion($model = array()) {
		/*
		* $uid = String::uuid (); $uid = str_replace ( '{', '', $uid ); $uid = str_replace ( '}', '', $uid ); $uid = str_replace ( '-', '', $uid );
		*/
		$this->dao->execute ( 'INSERT INTO ' . $this->vip_question . ' (
													`uid`,
													`course_type_id`,
													`question_type_id`,
													`difficulty`,
													`knowledge_id`,
													`sub_knowledge_id`,
													`grades`,
													`content`,
													`content_text`,
													`analysis`,
													`parent_id`,
													`status`,
													`created_user_name`,
													`created_time`,
													`last_updated_time`,
													`sdate`) VALUES (' . $this->dao->quote ( $model ['uid'] ) . ', ' . $this->dao->quote ( $model ['course_type_id'] ) . ', ' . $this->dao->quote ( $model ['question_type_id'] ) . ', ' . $this->dao->quote ( $model ['score'] ) . ', ' . $this->dao->quote ( $model ['knowledge_id'] ) . ', ' . $this->dao->quote ( $model ['sub_knowledge_id'] ) . ', ' . $this->dao->quote ( $model ['grades'] ) . ', ' . $this->dao->quote ( $model ['content'] ) . ', ' . $this->dao->quote ( strip_tags ( $model ['content'] ) ) . ', ' . $this->dao->quote ( $model ['analysis'] ) . ', ' . $this->dao->quote ( $model ['parent_id'] ) . ', 1' . ', ' . $this->dao->quote ( $model ['user_name'] ) . ', ' . $this->dao->quote ( strtotime ( date ( "Y-m-d H:i:s" ) ) ) . ', ' . $this->dao->quote ( strtotime ( date ( "Y-m-d H:i:s" ) ) ) . ',' . $this->dao->quote ( $model ['sdate'] ) . ')' );

		$questionId = $this->dao->lastInsertId ();
		// 选项
		$options = $model ['options'];
		$euids = $model ['euids'];
		if (! empty ( $options )) {
			$answers = $model ['options_answer_flag'];
			for($i = 0; $i < count ( $options ); $i ++) {
				$this->dao->execute ( 'INSERT INTO ' . $this->vip_question_option . ' (
														`uid`,
														`question_id`,
														`content`,
														`sort`,
														`is_answer`) VALUES (' . $this->dao->quote ( $euids [$i] ) . ', ' . $this->dao->quote ( $questionId ) . ', ' . $this->dao->quote ( $options [$i] ) . ', ' . $this->dao->quote ( ($i + 1) ) . ', ' . $this->dao->quote ( in_array ( $i, $answers ) ? 1 : 0 ) . ')' );
			}
		}
		// 答案
		$answers = $model ['answers'];
		if (! empty ( $answers )) {
			for($i = 0; $i < count ( $answers ); $i ++) {
				$this->dao->execute ( 'INSERT INTO ' . $this->vip_question_answer . ' (
														`question_id`,
														`content`,
														`sort`) VALUES (' . $this->dao->quote ( $questionId ) . ', ' . $this->dao->quote ( $answers [$i] ) . ', ' . $this->dao->quote ( $answers [$i] ['sort'] ) . ')' );
			}
		}

		return array (
		'id' => $questionId
		);
	}
	public function deleteQuestionById($id) {
		return $this->dao->execute ( 'UPDATE ' . $this->vip_question . ' SET status = -1 WHERE id = ' . $this->dao->quote ( $id ) );
	}
	public function getQuestionByID($id) {
		return $this->dao->getRow ( 'SELECT `id`,
										    `uid`,
										    `course_type_id`,
										    `question_type_id`,
										    `difficulty`,
										    `knowledge_id`,
										    `sub_knowledge_id`,
										    `grades`,
										    `content`,
										    `analysis`,
										    `parent_id`,
										    `status`,
										    `created_user_name`,
										    `created_time`,
										    `last_updated_user_name`,
										    `last_updated_time`,
										    `sdate`
										     FROM ' . $this->vip_question . ' WHERE id = ' . $this->dao->quote ( $id ) );
	}
	public function getQuestionFullByID($id) {
		$row = $this->dao->getRow ( 'SELECT `id`,
										    `course_type_id`,
										    `question_type_id`,
										    `difficulty`,
										    `knowledge_id`,
										    `sub_knowledge_id`,
										    `grades`,
										    `content`,
										    `analysis`,
										    `parent_id`,
										    `status`,
										    `created_user_name`,
										    `created_time`,
										    `last_updated_user_name`,
										    `last_updated_time`
										     FROM ' . $this->vip_question . ' WHERE id = ' . $this->dao->quote ( $id ) );

		$subs = $this->getSubQuestionsByQuestionIds ( $id );
		if ($subs) {
			$row ['subs'] = $subs;
		}

		return $row;
	}
	public function getPaperQuestionFullByID($id) {
		$row = $this->dao->getRow ( ' SELECT a.*,
											 b.`name` AS `knowledge_name`,
											 fn_vip_get_sub_knowledge_name(a.sub_knowledge_id) AS `sub_knowledge_names`,
											 d.`title` AS `course_type_name`,
											 d.`subject_id`,
											 e.`title` AS `subject_name`,
											 f.`title` AS `grade_name`,
											 g.`question_type_code`,
											 h.`title` AS `question_type_name`,
											 fn_vip_get_grade_name(a.grades) AS `grade_names`,
										     aa.file_name
										FROM ' . $this->vip_question . ' a
										LEFT JOIN ' . $this->vip_paper . ' aa ON a.paper_id = aa.id
										LEFT JOIN ' . $this->vip_knowledge . ' b ON a.knowledge_id = b.id
										LEFT JOIN ' . $this->vip_dict_course_type . ' d ON a.course_type_id = d.id
										LEFT JOIN ' . $this->vip_dict_subject . ' e ON d.subject_id = e.id
										LEFT JOIN ' . $this->vip_dict_grade . ' f ON e.grade_id = f.id
										LEFT JOIN ' . $this->vip_dict_question_type . ' g ON g.id = a.question_type_id
										LEFT JOIN ' . $this->vip_dict . ' h ON g.question_type_code = h.code AND h.category = \'QUESTION_TYPE\'
									 	WHERE a.status = 1 AND a.parent_id = 0 and a.id = ' . $this->dao->quote ( $id ) );

		if ($row) {
			$questionIds = array ();
			$questionIds [] = $row ['id'];

			$options = $this->getOptionsByQuestionIds ( arr2str ( $questionIds ) );
			$answers = $this->getAnswersByQuestionIds ( arr2str ( $questionIds ) );

			$questionOptions = array ();
			$questionAnswers = array ();

			foreach ( $options as $option ) {
				if ($option ['question_id'] == $row ['id']) {
					$option ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $option ['content'] );
					$questionOptions [] = $option;
				}
			}
			foreach ( $answers as $answer ) {
				if ($answer ['question_id'] == $row ['id']) {
					$answer ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $answer ['content'] );
					$questionAnswers [] = $answer;
				}
			}

			$row ['content'] = preg_replace ( '/(top:.*pt).*/iU', '', $row ['content'] );
			$row ['analysis'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $row ['analysis'] );
			$row ['options'] = $questionOptions;
			$row ['answers'] = $questionAnswers;
		}

		return $row;
	}

	/**
	 * 获取试题的选项
	 */
	public function getOptionsByID($id) {
		return $this->dao->getAll ( 'SELECT o.`uid`,
											o.`id` AS `oid`,
										    o.`content` AS `ocontent`,
										    o.`sort`,
										    o.`is_answer`
									FROM ' . $this->vip_question_option . ' o
									WHERE o.status=1 and o.question_id = ' . $this->dao->quote ( $id ) . ' ORDER BY sort ASC' );
	}

	/**
	 * 获取试题的答案
	 */
	public function getAnswerByID($id) {
		return $this->dao->getAll ( 'SELECT `id`,
			 								`question_id`,
										    `content`,
										    `sort`
									FROM ' . $this->vip_question_answer . '
									WHERE status=1 and question_id = ' . $this->dao->quote ( $id ) . ' ORDER BY sort ASC' );
	}
	public function getQuestionsByWhere($condition = array(), $currentPage, $pageSize) {
		$currentPage = empty ( $currentPage ) ? 1 : $currentPage;
		$pageSize = empty ( $pageSize ) ? 20 : $pageSize;

		//$condition ['id']  = '31304';

		$where = '';
		//$condition ['ts'] = '选择题' ;
		//$where .= ' AND h.title = ' . $this->dao->quote ( $condition ['ts'] );

		//$where .= ' AND a.id = ' . $this->dao->quote ( $condition ['id'] );
		if (! empty ( $condition ['coursetypeid'] )) {
			$where .= ' AND a.course_type_id = ' . $this->dao->quote ( $condition ['coursetypeid'] );
		}
		if (! empty ( $condition ['department'] )) {
			$where .= ' AND a.department = ' . $this->dao->quote ( $condition ['department'] );
		}
		if (! empty ( $condition ['isclassic'] )) {
			$where .= ' AND a.is_classic = ' . $this->dao->quote ( $condition ['isclassic'] );
		}
		if (! empty ( $condition ['iscontenterror'] )) {
			$where .= ' AND a.is_content_error = ' . $this->dao->quote ( $condition ['iscontenterror'] );
		}
		// if (! empty ( $condition ['startdate'] ) && ! empty ( $condition ['enddate'] )) {
		// $where .= ' AND a.create_time BETWEEN ' . $this->dao->quote ( date ( "Y-m-d 00:00:00", strtotime ( $condition ['startdate'] ) ) ) . ' AND ' . $this->dao->quote ( date ( "Y-m-d 23:59:59", strtotime ( $condition ['enddate'] ) ) );
		// }
		if (isset ( $condition ['status'] )) {
			$where .= ' AND a.status = ' . $this->dao->quote ( $condition ['status'] );
		}
		if (! empty ( $condition ['grade'] )) {
			$where .= ' AND f.`id` IN (' . $condition ['grade'] . ') ';
		}
		if (! empty ( $condition ['subject'] )) {
			$where .= ' AND e.`id` IN (' . $condition ['subject'] . ') ';
		}


		$list = $this->dao->getAll ( ' SELECT a.*,
											 b.`name` AS `knowledge_name`,
											 d.`title` AS `course_type_name`,
											 e.`title` AS `subject_name`,
											 f.`title` AS `grade_name`,
											 g.`question_type_code`,
											 h.`title` AS `question_type_name`,
											 fn_vip_get_grade_name(a.grades) AS `grade_names`,
										     aa.file_name
										FROM ' . $this->vip_question . ' a
										LEFT JOIN ' . $this->vip_paper . ' aa ON a.paper_id = aa.id
										LEFT JOIN ' . $this->vip_knowledge . ' b ON a.knowledge_id = b.id
										LEFT JOIN ' . $this->vip_dict_course_type . ' d ON a.course_type_id = d.id
										LEFT JOIN ' . $this->vip_dict_subject . ' e ON d.subject_id = e.id
										LEFT JOIN ' . $this->vip_dict_grade . ' f ON e.grade_id = f.id
										LEFT JOIN ' . $this->vip_dict_question_type . ' g ON g.id = a.question_type_id
										LEFT JOIN ' . $this->vip_dict . ' h ON g.question_type_code = h.code AND h.category = \'QUESTION_TYPE\'
									 	WHERE a.status = 1 AND a.parent_id = 0 ' . $where . ' ORDER BY id DESC
									 	LIMIT ' . ($currentPage - 1) * $pageSize . ', ' . $pageSize );

		if ($list) {
			$questionIds = array ();
			for($i = 0, $n = count ( $list ); $i < $n; $i ++) {
				$questionIds [] = $list [$i] ['id'];
			}
			$options = $this->getOptionsByQuestionIds ( arr2str ( $questionIds ) );
			$answers = $this->getAnswersByQuestionIds ( arr2str ( $questionIds ) );
			// $subs = $this->getSubQuestionsByQuestionIds ( arr2str ( $questionIds ) );

			for($i = 0, $n = count ( $list ); $i < $n; $i ++) {
				$questionOptions = array ();
				$questionAnswers = array ();

				foreach ( $options as $option ) {
					if ($option ['question_id'] == $list [$i] ['id']) {
						$option ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $option ['content'] );
						$questionOptions [] = $option;
					}
				}
				foreach ( $answers as $answer ) {
					if ($answer ['question_id'] == $list [$i] ['id']) {
						$answer ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $answer ['content'] );
						$questionAnswers [] = $answer;
					}
				}

				$list [$i] ['content'] = preg_replace ( '/(top:.*pt).*/iU', '', $list [$i] ['content'] );
				$list [$i] ['analysis'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $list [$i] ['analysis'] );
				$list [$i] ['options'] = $questionOptions;
				$list [$i] ['answers'] = $questionAnswers;
			}
		}

		$total = 0;
		if ($list) {
			$total = $this->getQuestionsCountByWhere ( $condition );
		}

		return array (
		'total' => $total,
		'rows' => $list
		);
	}
	public function getMyEditSimpleQuestions($userName, $currentPage, $pageSize) {
		$currentPage = empty ( $currentPage ) ? 1 : $currentPage;
		$pageSize = empty ( $pageSize ) ? 20 : $pageSize;

		// 先获取已被当前用户锁定的数据
		$list = $this->dao->getAll ( ' SELECT	a.`file_name`,
												  b.`id` AS `question_id`,
												  b.`uid`,
												  b.`knowledge_id`,
												  b.`sub_knowledge_id`,
												  b.`difficulty`,
											      b.`sdate`,
											      b.`number`,
												  b.`content`,
												  b.`is_content_error`,
												  b.`is_classic`,
												  b.`lock_row_time`,
												  from_unixtime(b.`knode_last_updated_time`) as `knode_last_updated_time`,
												  c.name as knowledge_name
										FROM ' . $this->vip_paper . ' a
										LEFT JOIN ' . $this->vip_question . ' b ON a.id = b.paper_id
										LEFT JOIN ' . $this->vip_knowledge . ' c ON b.knowledge_id = c.id
									 	WHERE a.`status` = 1 AND b.`status` = 1 AND b.department = \'CLASS\' AND b.`is_edit` = 1 AND current_used_user_name = ' . $this->dao->quote ( $userName ) . ' ORDER BY knode_last_updated_time DESC' );
		if ($list) {
			$questionIds = array ();
			for($i = 0, $n = count ( $list ); $i < $n; $i ++) {
				$questionIds [] = $list [$i] ['question_id'];
			}

			$options = $this->getOptionsByQuestionIds ( arr2str ( $questionIds ) );
			for($i = 0, $n = count ( $list ); $i < $n; $i ++) {
				$questionOptions = array ();
				foreach ( $options as $option ) {
					if ($option ['question_id'] == $list [$i] ['question_id']) {
						$option ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $option ['content'] );
						$questionOptions [] = $option;
					}
				}

				$list [$i] ['content'] = preg_replace ( '/(top:.*pt).*/iU', '', $list [$i] ['content'] );
				// $list [$i] ['analysis'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $list [$i] ['analysis'] );

				$list [$i] ['options'] = $questionOptions;
			}

			$row = $this->dao->getRow ( 'SELECT COUNT(b.id)
										FROM ' . $this->vip_paper . ' a
										LEFT JOIN ' . $this->vip_question . ' b ON a.id = b.paper_id
									 	WHERE a.`status` = 1 AND b.`status` = 1 AND  b.`is_edit` = 1 AND current_used_user_name = ' . $this->dao->quote ( $userName ) );
			if ($row) {
				$total = $row ['cnt'];
			}
		}
		return array (
		'total' => $total,
		'rows' => $list
		);

		return $list;
	}
	public function getQuestionsByKnowledgeId($knowledgeId) {
		$currentPage = empty ( $currentPage ) ? 1 : $currentPage;
		$pageSize = empty ( $pageSize ) ? 20 : $pageSize;

		// 先获取已被当前用户锁定的数据
		$list = $this->dao->getAll ( ' SELECT	  a.`file_name`,
												  b.`id` AS `question_id`,
												  b.`uid`,
												  b.`knowledge_id`,
												  b.`sub_knowledge_id`,
												  b.`difficulty`,
											      b.`sdate`,
											      b.`number`,
												  b.`content`,
												  b.`is_content_error`,
												  b.`is_classic`,
												  b.`lock_row_time`,
												  from_unixtime(b.`knode_last_updated_time`) as `knode_last_updated_time`,
												  c.name as knowledge_name
										FROM ' . $this->vip_question . ' b
										LEFT JOIN ' . $this->vip_paper . ' a ON a.id = b.paper_id
										LEFT JOIN ' . $this->vip_knowledge . ' c ON b.knowledge_id = c.id
									 	WHERE b.`status` = 1 AND b.department = \'CLASS\' AND b.knowledge_id = ' . $this->dao->quote ( $knowledgeId ) . ' ORDER BY b.`is_classic` DESC, knode_last_updated_time DESC' );

		if ($list) {
			$questionIds = array ();
			for($i = 0, $n = count ( $list ); $i < $n; $i ++) {
				$questionIds [] = $list [$i] ['question_id'];
			}

			$options = $this->getOptionsByQuestionIds ( arr2str ( $questionIds ) );
			for($i = 0, $n = count ( $list ); $i < $n; $i ++) {
				$questionOptions = array ();
				foreach ( $options as $option ) {
					if ($option ['question_id'] == $list [$i] ['question_id']) {
						$option ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $option ['content'] );
						$questionOptions [] = $option;
					}
				}

				$list [$i] ['content'] = preg_replace ( '/(top:.*pt).*/iU', '', $list [$i] ['content'] );
				// $list [$i] ['analysis'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $list [$i] ['analysis'] );

				$list [$i] ['options'] = $questionOptions;
			}

			/*
			* $row = $this->dao->getRow ( 'SELECT COUNT(b.id) FROM ' . $this->vip_paper . ' a LEFT JOIN ' . $this->vip_question . ' b ON a.id = b.paper_id WHERE a.`status` = 1 AND b.`status` = 1 AND b.`is_edit` = 1 AND current_used_user_name = ' . $this->dao->quote ( $userName ) ); if ($row) { $total = $row ['cnt']; }
			*/
		}
		return array (
		'rows' => $list
		);

		return $list;
	}
	public function getMyEditSimpleQuestions1($userName, $currentPage, $pageSize) {
		$currentPage = empty ( $currentPage ) ? 1 : $currentPage;
		$pageSize = empty ( $pageSize ) ? 20 : $pageSize;

		// 先获取已被当前用户锁定的数据
		$list = $this->dao->getAll ( ' SELECT	a.`file_name`,
												  b.`id` AS `question_id`,
												  b.`uid`,
												  b.`knowledge_id`,
												  b.`sub_knowledge_id`,
												  b.`content_error_types`,
											      b.`sdate`,
											      b.`number`,
												  b.`content`,
												  b.`is_content_error`,
												  b.`is_classic`,
												  b.`lock_row_time`,
												  from_unixtime(b.`content_last_updated_time`) as `content_last_updated_time`,
												  c.name as knowledge_name
										FROM ' . $this->vip_paper . ' a
										LEFT JOIN ' . $this->vip_question . ' b ON a.id = b.paper_id
										LEFT JOIN ' . $this->vip_knowledge . ' c ON b.knowledge_id = c.id
									 	WHERE a.`status` = 1 AND b.`status` = 1 AND  b.`is_edit1` = 1 AND current_used_user_name1 = ' . $this->dao->quote ( $userName ) . ' ORDER BY content_last_updated_time DESC' );

		if ($list) {
			$questionIds = array ();
			for($i = 0, $n = count ( $list ); $i < $n; $i ++) {
				$questionIds [] = $list [$i] ['question_id'];
			}

			$options = $this->getOptionsByQuestionIds ( arr2str ( $questionIds ) );
			$answers = $this->getAnswersByQuestionIds ( arr2str ( $questionIds ) );
			for($i = 0, $n = count ( $list ); $i < $n; $i ++) {
				$questionOptions = array ();
				foreach ( $options as $option ) {
					if ($option ['question_id'] == $list [$i] ['question_id']) {
						$option ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $option ['content'] );
						$questionOptions [] = $option;
					}
				}
				foreach ( $answers as $answer ) {
					if ($answer ['question_id'] == $list [$i] ['question_id']) {
						$answer ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $answer ['content'] );
						$questionAnswers [] = $answer;
					}
				}

				$list [$i] ['content'] = preg_replace ( '/(top:.*pt).*/iU', '', $list [$i] ['content'] );
				$list [$i] ['analysis'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $list [$i] ['analysis'] );

				$list [$i] ['options'] = $questionOptions;
				$list [$i] ['answers'] = $questionAnswers;
			}

			$row = $this->dao->getRow ( 'SELECT COUNT(b.id)
										FROM ' . $this->vip_paper . ' a
										LEFT JOIN ' . $this->vip_question . ' b ON a.id = b.paper_id
									 	WHERE a.`status` = 1 AND b.`status` = 1 AND  b.`is_edit1` = 1 AND current_used_user_name1 = ' . $this->dao->quote ( $userName ) );
			if ($row) {
				$total = $row ['cnt'];
			}
		}
		return array (
		'total' => $total,
		'rows' => $list
		);

		return $list;
	}
	// 目前只取一条记录
	public function getSimpleQuestionsByWhere($userName, $condition = array(), $currentPage, $pageSize) {
		$currentPage = empty ( $currentPage ) ? 1 : $currentPage;
		$pageSize = empty ( $pageSize ) ? 20 : $pageSize;

		$where = $where1 = '';
		if (! empty ( $condition ['coursetypeid'] )) {
			$where .= ' and b.department=\'VIP\' and b.course_type_id = ' . $this->dao->quote ( $condition ['coursetypeid'] );
			$where1 = ' department=\'VIP\' and status = 1 AND  is_edit = 0 and in_used = 0 and course_type_id = ' . $this->dao->quote ( $condition ['coursetypeid'] );
		} else {
			return array ();
		}

		// 先获取已被当前用户锁定的数据
		$row = $this->dao->getRow ( ' SELECT  a.`file_name`,
											  b.`id` AS `question_id`,
											  b.`uid`,
											  b.`number`,
											  b.`knowledge_id`,
											  b.`sub_knowledge_id`,
											  b.`difficulty`,
										      b.`sdate`,
											  b.`department`,
											  b.`content`,
											  b.`analysis`,
											  b.`is_content_error`,
											  b.`lock_row_time`
										FROM ' . $this->vip_question . ' b
										LEFT JOIN ' . $this->vip_paper . ' a ON a.id = b.paper_id
									 	WHERE b.`status` = 1 AND  b.`is_edit` = 0 AND in_used = 1 AND current_used_user_name = \'' . $userName . '\' ' . $where . '
									 	LIMIT 0, 1' );

		if (! $row) {
			$row = $this->dao->getRow ( 'SELECT   a.`file_name`,
												  b.`id` AS `question_id`,
												  b.`uid`,
											  	  b.`number`,
												  b.`knowledge_id`,
												  b.`sub_knowledge_id`,
												  b.`difficulty`,
												  b.`sdate`,
											  	  b.`department`,
												  b.`content`,
											  	  b.`analysis`,
												  b.`is_content_error`,
												  b.`lock_row_time`,
												  b.is_classic
										FROM ' . $this->vip_question . ' b
										LEFT JOIN ' . $this->vip_paper . ' a ON a.id = b.paper_id
									 	WHERE b.`status` = 1 AND  b.`is_edit` = 0 and b.in_used = 0' . $where . '
										ORDER BY RAND()
										LIMIT 0, 1' );
			if ($row) {
				$time = time ();
				if ($this->dao->execute ( 'UPDATE ' . $this->vip_question . ' SET in_used = 1, current_used_user_name = \'' . $userName . '\', lock_row_time = \'' . $time . '\' WHERE id = ' . $row ['question_id'] )) {
					$row ['lock_row_time'] = $time;
				}
			}
		}
		if ($row) {
			$row ['content'] = preg_replace ( '/(top:.*pt).*/iU', '', $row ['content'] );
			$row ['analysis'] = preg_replace ( '/(top:.*pt).*/iU', '', $row ['analysis'] );
			// $row ['content'] = preg_replace ( '/(position:relative;)([\s\S]*)(top:.*pt)([\'\"]{1}\>[\s\S]*\<img){1}/iU', '$1-----$4', $row ['content'] );
			$options = $this->getOptionsByQuestionIds ( $row ['question_id'] );
			$answers = $this->getAnswersByQuestionIds ( $row ['question_id'] );

			foreach ( $options as $option ) {
				$option ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $option ['content'] );
				$questionOptions [] = $option;
			}
			foreach ( $answers as $answer ) {
				$answer ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $answer ['content'] );
				$questionAnswers [] = $answer;
			}
			$row ['options'] = $questionOptions;
			$row ['answers'] = $questionAnswers;
		}

		return $row;
	}
	// 目前只取一条记录
	public function getSimpleQuestionsEditByWhere($userName, $condition = array(), $currentPage, $pageSize) {
		$currentPage = empty ( $currentPage ) ? 1 : $currentPage;
		$pageSize = empty ( $pageSize ) ? 20 : $pageSize;

		$where = $where1 = '';
		if (! empty ( $condition ['coursetypeid'] )) {
			$where .= ' AND b.department=\'VIP\' and b.course_type_id = ' . $this->dao->quote ( $condition ['coursetypeid'] );
			$where1 = ' department=\'VIP\' AND status = 1 and is_edit1 = 0 and in_used1 = 0 and course_type_id = ' . $this->dao->quote ( $condition ['coursetypeid'] );
		} else {
			return array ();
		}

		// 先获取已被当前用户锁定的数据
		$row = $this->dao->getRow ( ' SELECT  a.`file_name`,
											  b.`id` AS `question_id`,
											  b.`uid`,
											  b.`number`,
											  b.`course_type_id`,
											  b.`question_type_id`,
										      b.`sdate`,
											  b.`content`,
											  b.`lock_row_time1`,
											  c.`question_type_code`
										FROM ' . $this->vip_question . ' b
										LEFT JOIN ' . $this->vip_paper . ' a ON a.id = b.paper_id
										LEFT JOIN ' . $this->vip_dict_question_type . ' c ON b.question_type_id = c.id
									 	WHERE b.`status` = 1 AND  b.`is_edit1` = 0 AND in_used1 = 1 AND current_used_user_name1 = \'' . $userName . '\' ' . $where . '
									 	LIMIT 0, 1' );
		if (! $row) {
			$row = $this->dao->getRow ( 'SELECT  a.`file_name`,
												  b.`id` AS `question_id`,
												  b.`uid`,
											  	  b.`number`,
												  b.`course_type_id`,
												  b.`question_type_id`,
												  b.`sdate`,
												  b.`content`,
											  	  b.`lock_row_time1`,
											  	  c.`question_type_code`
										FROM ' . $this->vip_question . ' b
										LEFT JOIN ' . $this->vip_paper . ' a ON a.id = b.paper_id
										LEFT JOIN ' . $this->vip_dict_question_type . ' c ON b.question_type_id = c.id
									 	WHERE b.`status` = 1 AND  b.`is_edit1` = 0 and b.in_used1 = 0' . $where . '
										ORDER BY RAND()
										LIMIT 0, 1' );
			if ($row) {
				$time = time ();
				if ($this->dao->execute ( 'UPDATE ' . $this->vip_question . ' SET in_used1 = 1, current_used_user_name1 = \'' . $userName . '\', lock_row_time1 = \'' . $time . '\' WHERE id = ' . $row ['question_id'] )) {
					$row ['lock_row_time1'] = $time;
				}
			}
		}
		if ($row) {
			$row ['content'] = preg_replace ( '/(top:.*pt).*/iU', '', $row ['content'] );
			// $row ['content'] = preg_replace ( '/(position:relative;)([\s\S]*)(top:.*pt)([\'\"]{1}\>[\s\S]*\<img){1}/iU', '$1-----$4', $row ['content'] );
			$options = $this->getOptionsByQuestionIds ( $row ['question_id'] );
			foreach ( $options as $option ) {
				// $option ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $option ['content'] );
				$questionOptions [] = $option;
			}
			$row ['options'] = $questionOptions;
		}

		return $row;
	}
	// 目前只取一条记录
	public function getClassicQuestionsEditByWhere($userName, $condition = array(), $currentPage, $pageSize) {
		$currentPage = empty ( $currentPage ) ? 1 : $currentPage;
		$pageSize = empty ( $pageSize ) ? 20 : $pageSize;

		$where = $where1 = '';
		if (! empty ( $condition ['coursetypeid'] )) {
			$where .= ' AND b.department=\'CLASS\' and b.course_type_id = ' . $this->dao->quote ( $condition ['coursetypeid'] );
			$where1 = ' department=\'CLASS\' AND status = 1 and is_edit2 = 0 and in_used2 = 0 and course_type_id = ' . $this->dao->quote ( $condition ['coursetypeid'] );
		} else {
			return array ();
		}

		// 先获取已被当前用户锁定的数据
		$row = $this->dao->getRow ( ' SELECT  a.`file_name`,
											  b.`id` AS `question_id`,
											  b.`uid`,
											  b.`number`,
											  b.`course_type_id`,
											  b.`question_type_id`,
										      b.`sdate`,
											  b.`content`,
											  b.`lock_row_time1`,
											  c.`question_type_code`
										FROM ' . $this->vip_question . ' b
										LEFT JOIN ' . $this->vip_paper . ' a ON a.id = b.paper_id
										LEFT JOIN ' . $this->vip_dict_question_type . ' c ON b.question_type_id = c.id
									 	WHERE b.`status` = 1 AND b.`is_edit2` = 0 AND in_used2 = 1 AND current_used_user_name2 = \'' . $userName . '\' ' . $where . '
									 	LIMIT 0, 1' );
		if (! $row) {
			$row = $this->dao->getRow ( 'SELECT  a.`file_name`,
												  b.`id` AS `question_id`,
												  b.`uid`,
											  	  b.`number`,
												  b.`course_type_id`,
												  b.`question_type_id`,
												  b.`sdate`,
												  b.`content`,
											  	  b.`lock_row_time1`,
											  	  c.`question_type_code`
										FROM ' . $this->vip_question . ' b
										LEFT JOIN ' . $this->vip_paper . ' a ON a.id = b.paper_id
										LEFT JOIN ' . $this->vip_dict_question_type . ' c ON b.question_type_id = c.id
									 	WHERE b.`status` = 1 AND b.`is_classic` = 1 AND b.`is_edit2` = 0 and b.in_used2 = 0' . $where . '
										ORDER BY RAND()
										LIMIT 0, 1' );
			if ($row) {
				$time = time ();
				if ($this->dao->execute ( 'UPDATE ' . $this->vip_question . ' SET in_used2 = 1, current_used_user_name2 = \'' . $userName . '\', lock_row_time2 = \'' . $time . '\' WHERE id = ' . $row ['question_id'] )) {
					$row ['lock_row_time1'] = $time;
				}
			}
		}
		if ($row) {
			$row ['content'] = preg_replace ( '/(top:.*pt).*/iU', '', $row ['content'] );
			// $row ['content'] = preg_replace ( '/(position:relative;)([\s\S]*)(top:.*pt)([\'\"]{1}\>[\s\S]*\<img){1}/iU', '$1-----$4', $row ['content'] );
			$options = $this->getOptionsByQuestionIds ( $row ['question_id'] );
			foreach ( $options as $option ) {
				// $option ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $option ['content'] );
				$questionOptions [] = $option;
			}
			$row ['options'] = $questionOptions;
		}

		return $row;
	}
	protected function getSubQuestionsByQuestionIds($questionIds) {
		$sql = ' SELECT a.*, b.question_type_code FROM ' . $this->vip_question . ' a LEFT JOIN ' . $this->vip_dict_question_type . ' b ON a.question_type_id = b.id WHERE parent_id IN (' . $questionIds . ')';

		return $this->dao->getAll ( $sql );
	}
	public function getOptionsByQuestionIds($ids) {
		$sql = 'SELECT  `id`,
						`question_id`,
						`content`,
						`sort`,
						`is_answer`,
						`status`
				FROM ' . $this->vip_question_option . '
				WHERE status = 1 AND question_id IN (' . $ids . ')
				ORDER BY sort, id';
		// echo $sql;exit;
		return $this->dao->getAll ( $sql );
	}
	public function getAnswersByQuestionIds($ids) {
		$sql = 'SELECT  `id`,
						`question_id`,
						`content`,
						`sort`,
						`status`
				FROM ' . $this->vip_question_answer . '
				WHERE status = 1 AND question_id IN (' . $ids . ')
				ORDER BY sort, id';

		return $this->dao->getAll ( $sql );
	}
	public function getQuestionsCountByWhere($condition = array()) {
		$where = '';
		if (! empty ( $condition ['coursetypeid'] )) {
			$where .= ' AND a.course_type_id = ' . $this->dao->quote ( $condition ['coursetypeid'] );
		}
		if (! empty ( $condition ['department'] )) {
			$where .= ' AND a.department = ' . $this->dao->quote ( $condition ['department'] );
		}
		if (! empty ( $condition ['isclassic'] )) {
			$where .= ' AND a.is_classic = ' . $this->dao->quote ( $condition ['isclassic'] );
		}
		if (! empty ( $condition ['iscontenterror'] )) {
			$where .= ' AND a.is_content_error = ' . $this->dao->quote ( $condition ['iscontenterror'] );
		}
		// if (! empty ( $condition ['startdate'] ) && ! empty ( $condition ['enddate'] )) {
		// $where .= ' AND a.create_time BETWEEN ' . $this->dao->quote ( date ( "Y-m-d 00:00:00", strtotime ( $condition ['startdate'] ) ) ) . ' AND ' . $this->dao->quote ( date ( "Y-m-d 23:59:59", strtotime ( $condition ['enddate'] ) ) );
		// }
		if (isset ( $condition ['status'] )) {
			$where .= ' AND a.status = ' . $this->dao->quote ( $condition ['status'] );
		}
		if (! empty ( $condition ['grade'] )) {
			$where .= ' AND f.`id` IN (' . $condition ['grade'] . ') ';
		}
		if (! empty ( $condition ['subject'] )) {
			$where .= ' AND e.`id` IN (' . $condition ['subject'] . ') ';
		}

		$row = $this->dao->getRow ( 'SELECT COUNT(*) AS cnt
										FROM ' . $this->vip_question . ' a
									 	LEFT JOIN ' . $this->vip_knowledge . ' b ON a.knowledge_id = b.id
										LEFT JOIN ' . $this->vip_dict_course_type . ' d ON a.course_type_id = d.id
										LEFT JOIN ' . $this->vip_dict_subject . ' e ON d.subject_id = e.id
										LEFT JOIN ' . $this->vip_dict_grade . ' f ON e.grade_id = f.id
										LEFT JOIN ' . $this->vip_dict . ' g ON a.question_type_id = g.id AND g.category = \'QUESTION_TYPE\'
									 	WHERE a.status = 1 AND a.parent_id = 0 ' . $where );
		$total = 0;
		if ($row) {
			$total = $row ['cnt'];
		}

		return $total;
	}
	public function getQuestionCurrentEditByUserName($userName, $courseTypeId) {
		return $this->dao->getAll ( ' SELECT  id
										FROM ' . $this->vip_question . '
									 	WHERE department=\'CLASS\' and status = 1 AND  is_edit = 0 AND in_used = 1 and course_type_id = ' . $this->dao->quote ( $courseTypeId ) . ' and current_used_user_name = ' . $this->dao->quote ( $userName ) );
	}
	public function getQuestionCurrentEditByUserName1($userName, $courseTypeId) {
		return $this->dao->getAll ( ' SELECT  id
										FROM ' . $this->vip_question . '
									 	WHERE department=\'CLASS\' and status = 1 AND  is_edit1 = 0 AND in_used1 = 1 and course_type_id = ' . $this->dao->quote ( $courseTypeId ) . ' and current_used_user_name1 = ' . $this->dao->quote ( $userName ) );
	}
	public function getQuestionCurrentEditByUserName2($userName, $courseTypeId) {
		return $this->dao->getAll ( ' SELECT  id
										FROM ' . $this->vip_question . '
									 	WHERE department=\'CLASS\' and status = 1 AND  is_edit2 = 0 AND in_used2 = 1 and course_type_id = ' . $this->dao->quote ( $courseTypeId ) . ' and current_used_user_name2 = ' . $this->dao->quote ( $userName ) );
	}
	public function getQuestionStatistics($condition = array()) {
		$where = ' q.parent_id = 0 AND q.status = 1 ';
		if (isset ( $condition ['status'] )) {
			$where .= ' AND q.status = ' . $this->dao->quote ( $condition ['status'] );
		}
		/*
		* if (isset ( $condition ['is_paper'] )) { $where .= ' AND q.is_paper = ' . $this->dao->quote ( $condition ['is_paper'] ); } if (! empty ( $condition ['xuebu'] )) { $where .= ' AND b.`code` IN (' . $condition ['xuebu'] . ') '; } if (! empty ( $condition ['xueke'] )) { $where .= ' AND c.`code` IN (' . $condition ['xueke'] . ') '; }
		*/
		return $this->dao->getRow ( 'SELECT (
									 	SELECT COUNT(*) AS today FROM ' . $this->vip_question . ' AS q
									 	WHERE ' . $where . ' AND TO_DAYS(FROM_UNIXTIME(created_time)) = TO_DAYS(NOW())
									 ) AS today, (
									 	SELECT COUNT(*) AS week FROM ' . $this->vip_question . ' AS q 
									 	WHERE ' . $where . ' AND YEARWEEK(date_format(FROM_UNIXTIME(created_time), \'%Y-%m-%d\')) = YEARWEEK(now())
									 ) AS week, (
									 	SELECT COUNT(*) AS month FROM ' . $this->vip_question . ' AS q 
									 	WHERE ' . $where . ' AND date_format(FROM_UNIXTIME(created_time), \'%Y-%m\') = date_format(now(), \'%Y-%m\')
									 ) AS month, (
									 	SELECT COUNT(*) AS total FROM ' . $this->vip_question . ' AS q 
									 	WHERE ' . $where . '
									 ) AS total, (
										SELECT COUNT(*) AS paper_count FROM ' . $this->vip_paper . ' AS q WHERE status = 1
									 ) AS paper_count' );
	}
	public function getQuestionStatisticsByCourseTypeId($courseTypeId, $userName) {
		return $this->dao->getRow ( 'SELECT (
									 	SELECT COUNT(*) AS lock_question_count FROM ' . $this->vip_question . ' AS q
									 	WHERE status = 1 and department = \'VIP\' and is_edit = 0 and in_used = 1 and course_type_id = \'' . $courseTypeId . '\'
									 ) AS lock_question_count, (
									 	SELECT COUNT(*) AS left_non_edit_question_count FROM ' . $this->vip_question . ' AS q
									 	WHERE status = 1 and department = \'VIP\' and is_edit = 0 and course_type_id = \'' . $courseTypeId . '\'
									 ) AS left_non_edit_question_count, (
									 	SELECT COUNT(*) AS total_question_count FROM ' . $this->vip_question . ' AS q
									 	WHERE status = 1 and department = \'VIP\' and course_type_id = \'' . $courseTypeId . '\'
									 ) AS total_question_count, (
									 	SELECT COUNT(*) AS my_op_question_count FROM ' . $this->vip_question . ' AS q
									 	WHERE status = 1 and department = \'VIP\' and is_edit = 1 and current_used_user_name = \'' . $userName . '\' and course_type_id = \'' . $courseTypeId . '\'
									 ) AS my_op_question_count' );
	}
	public function getQuestionStatisticsByCourseTypeId1($courseTypeId, $userName) {
		return $this->dao->getRow ( 'SELECT (
									 	SELECT COUNT(*) AS lock_question_count FROM ' . $this->vip_question . ' AS q
									 	WHERE status = 1 and department = \'CLASS\' and is_edit1 = 0 and in_used1 = 1 and course_type_id = \'' . $courseTypeId . '\'
									 ) AS lock_question_count, (
									 	SELECT COUNT(*) AS left_non_edit_question_count FROM ' . $this->vip_question . ' AS q
									 	WHERE status = 1 and department = \'CLASS\' and is_edit1 = 0 and course_type_id = \'' . $courseTypeId . '\'
									 ) AS left_non_edit_question_count, (
									 	SELECT COUNT(*) AS total_question_count FROM ' . $this->vip_question . ' AS q
									 	WHERE status = 1 and department = \'CLASS\' and course_type_id = \'' . $courseTypeId . '\'
									 ) AS total_question_count, (
									 	SELECT COUNT(*) AS my_op_question_count FROM ' . $this->vip_question . ' AS q
									 	WHERE status = 1 and department = \'CLASS\' and is_edit1 = 1 and current_used_user_name1 = \'' . $userName . '\' and course_type_id = \'' . $courseTypeId . '\'
									 ) AS my_op_question_count' );
	}
	public function getQuestionStatisticsByCourseTypeId2($courseTypeId, $userName) {
		return $this->dao->getRow ( 'SELECT (
									 	SELECT COUNT(*) AS left_non_edit_question_count FROM ' . $this->vip_question . ' AS q
									 	WHERE status = 1 and department = \'CLASS\' and is_classic = 1 and is_edit2 = 0 and course_type_id = \'' . $courseTypeId . '\'
									 ) AS left_non_edit_question_count, (
									 	SELECT COUNT(*) AS total_question_count FROM ' . $this->vip_question . ' AS q
									 	WHERE status = 1 and department = \'CLASS\' and is_classic = 1 and course_type_id = \'' . $courseTypeId . '\'
									 ) AS total_question_count, (
									 	SELECT COUNT(*) AS my_op_question_count FROM ' . $this->vip_question . ' AS q
									 	WHERE status = 1 and department = \'CLASS\' and is_classic = 1 and is_edit2 = 1 and current_used_user_name2 = \'' . $userName . '\' and course_type_id = \'' . $courseTypeId . '\'
									 ) AS my_op_question_count' );
	}
	public function getGradeNameById($grade_id) {
		return $this->dao->getOne ( 'SELECT title FROM ' . $this->vip_dict_grade . ' WHERE id = ' . $this->dao->quote ( abs ( $grade_id ) ) );
	}
	public function getSubjectNameById($subject_id) {
		return $this->dao->getOne ( 'SELECT title FROM ' . $this->vip_dict_subject . ' WHERE id = ' . $this->dao->quote ( abs ( $subject_id ) ) );
	}
	public function getGrades() {
		return $this->dao->getAll ( 'SELECT `id`,
										    `title`,
										    `status`,
										    `sort`
									FROM ' . $this->vip_dict_grade . '
								 	WHERE status = 1 ORDER BY sort' );
	}
	public function getSubjectsByGradeId($gradeId) {
		return $this->dao->getAll ( 'SELECT `id`,
										    `title`,
										    `status`,
										    `sort`
									FROM ' . $this->vip_dict_subject . '
								 	WHERE status = 1 AND grade_id = ' . $this->dao->quote ( $gradeId ) . ' ORDER BY sort' );
	}
	public function getCourseTypesBySubjectId($subjectId) {
		return $this->dao->getAll ( 'SELECT `id`,
										    `title`,
										    `status`,
										    `sort`
									FROM ' . $this->vip_dict_course_type . '
								 	WHERE status = 1 AND subject_id = ' . $this->dao->quote ( $subjectId ) . ' ORDER BY sort' );
	}
	public function getQuestionTypesBySubjectId($subjectId) {
		return $this->dao->getAll ( 'SELECT a.`id`,
											CASE WHEN a.title = \'\' THEN b.title ELSE a.title END AS title,
										    b.title AS origin_title,
										    a.`status`,
										    a.`sort`,
										    a.`question_type_code`
									FROM ' . $this->vip_dict_question_type . ' a
									LEFT JOIN ' . $this->vip_dict . ' b ON a.question_type_code = b.code
								 	WHERE a.status = 1 AND a.subject_id = ' . $this->dao->quote ( $subjectId ) . ' ORDER BY a.sort, a.id' );
	}
	public function getPath($id, $type = 'knowledge') {
		$path = array ();
		if ($type == 'knowledge') {
			$nav = $this->getKnowledgeByID ( $id );
		} else {
			$nav = $this->getLabelByID ( $id );
		}
		$path [] = $nav;
		if ($nav ['parent_id'] > 0) {
			$path = array_merge ( $this->getPath ( $nav ['parent_id'] ), $path );
		}

		return $path;
	}
	public function getComboboxData($cate, $gradeId = 0, $subjectId = 0) {
		$where = '';
		switch ($cate) {
			case 'GRADE_DEPT' :
				$tempTable = $this->vip_dict_grade;
				$tempFiled = 'grade_id';
				break;
			case 'SUBJECT' :
				$tempTable = $this->vip_dict_subject;
				$tempFiled = 'subject_id';
				if (! empty ( $gradeId )) {
					$where .= ' AND grade_id = ' . $this->dao->quote ( $gradeId );
				}
				break;
			case 'QUESTION_TYPE' :
				$tempTable = $this->vip_dict_question_type;
				$tempFiled = 'question_type_id';
				if (! empty ( $subjectId )) {
					$where .= ' AND subject_id = ' . $this->dao->quote ( $subjectId );
				}
				break;
			case 'KNOWLEDGE_TYPE' :
				$tempTable = $this->vip_dict_knowledge_type;
				$tempFiled = 'knowledge_type_id';
				break;
		}
		return $this->dao->getAll ( 'SELECT id as ' . $tempFiled . ',title FROM ' . $tempTable . ' WHERE status = 1 ' . $where . ' order by sort' );
	}

	// 批量导入试题
	public function importQuestion($arr, $title) {
		$time = time ();

		if (! empty ( $title )) {
			$taojue = explode ( '-', $title );
		}

		$gradeSubjectCourseTypeRS = array (
		'小学数学' => '22',
		'小学语文' => '14',
		'小学英语' => '23',
		'小学测试学科' => '29',
		'小学业务部' => '41',

		'初中数学' => '1',
		'初中语文' => '5',
		'初中英语' => '24',
		'初中物理' => '4',
		'初中化学' => '3',
		'初中业务部' => '42',

		'高中数学' => '28',
		'高中数学（理）' => '28',
		'高中数学（文）' => '28',
		'高中语文' => '15',
		'高中英语' => '26',
		'高中物理' => '10',
		'高中化学' => '13',
		'高中业务部' => '40'
		);

		$gradeDeptName = $subjectName = '';
		$taojueQuestionCourseTypeId = '';

		// 判断是否为套卷
		if ($taojue [0] == '套卷' && ! empty ( $taojue [5] ) && ! empty ( $taojue [6] )) {
			$gradeDeptName = $taojue [5];
			$subjectName = $taojue [6];
		}

		// 判断是否为VIP套卷
		if ($taojue [0] == '套卷VIP' && ! empty ( $taojue [1] ) && ! empty ( $taojue [2] )) {
			$gradeDeptName = $taojue [1];
			$subjectName = $taojue [2];
		}

		// 判断是否为试题
		if ($taojue [0] == '试题' && ! empty ( $taojue [1] ) && ! empty ( $taojue [2] )) {
			$gradeDeptName = $taojue [1];
			$subjectName = $taojue [2];
		}

		// 获取同步课程ID
		if (! empty ( $gradeDeptName ) && ! empty ( $subjectName )) {
			foreach ( $gradeSubjectCourseTypeRS as $key => $value ) {
				if (trim ( $gradeDeptName ) . trim ( $subjectName ) == $key) {
					$taojueQuestionCourseTypeId = $value;
					break;
				}
			}
		}
		if (! empty ( $arr )) {
			// 查询套卷信息
			$paper_row_id = $this->dao->getOne ( 'SELECT id FROM ' . $this->vip_paper . ' WHERE status = 1 AND file_name_md5 = \'' . md5 ( $title ) . '\'' );
			if (! empty ( $paper_row_id )) { // 该套卷信息已存在
				$sql10 = 'UPDATE ' . $this->vip_paper . ' SET status = -1 WHERE file_name_md5 = \'' . md5 ( $title ) . '\'';
				if (! $this->dao->execute ( $sql10 )) {
					// $flag == false;
				} else {
					// 删除试题
					$sql11 = 'UPDATE ' . $this->vip_question . ' SET status = -1 WHERE paper_id = \'' . $paper_row_id . '\'';
					if (! $this->dao->execute ( $sql11 )) {
						// $flag == false;
					} else {
						// 删除文件【只移动，不实际删除】
						$question_rows = $this->dao->getAll ( 'SELECT id, uid, sdate FROM ' . $this->vip_question . ' WHERE paper_id = \'' . $paper_row_id . '\'' );
						foreach ( $question_rows as $q ) {
							// 删除文件
							// $path = $q ['sdate'] + '/' + $q ['uid'];
							// file_get_contents ( 'http://ksrc2.gaosiedu.com/move.php?path=' + $path );
						}
					}
				}
			}

			$paper_id = '';
			foreach ( $arr as $k => $data ) {
				// 内容中指定年部
				// if (! empty ( $data ['gradeDeptName'] )) {
				// $gradeDeptName = trim ( $data ['gradeDeptName'] );
				// }
				// 内容中指定学科
				// if (! empty ( $data ['subjectName'] )) {
				// $subjectName = trim ( $data ['subjectName'] );
				// }

				// print_r($data);
				$sql = $sql1 = $sql2 = $sql3 = $sql4 = $sql5 = $sql6 = '';

				//
				if (! empty ( $gradeDeptName )) { // 年部
					$gradeIdRS = array (
					'小学' => '1',
					'小学部' => '1',

					'初中' => '2',
					'初中部' => '2',

					'高中' => '3',
					'高中部' => '3'
					);
					foreach ( $gradeIdRS as $key => $value ) {
						if (trim ( $gradeDeptName ) == $key) {
							$data ['grade_dept_id'] = $value;
							break;
						}
					}
					// $data ['grade_dept_id'] = $this->dao->getOne ( 'SELECT id FROM ' . $this->vip_dict_grade . ' WHERE title like ' . $this->dao->quote ( '%' . trim ( $gradeDeptName ) . '%' ) );
					// if (empty ( $data ['grade_dept_id'] )) {
					// if ($this->dao->execute ( 'INSERT INTO ' . $this->vip_dict_grade . ' (title) VALUES(' . $this->dao->quote ( trim ( $gradeDeptName ) ) . ')' )) {
					// $data ['grade_dept_id'] = $this->dao->lastInsertId ();
					// }
					// }
				}

				if (! empty ( $subjectName )) { // 学科
					$gradeSubjectRS = array (
					'小学数学' => '1',
					'小学语文' => '2',
					'小学英语' => '3',
					'小学测试学科' => '27',

					'初中数学' => '4',
					'初中语文' => '5',
					'初中英语' => '6',
					'初中物理' => '7',
					'初中化学' => '8',

					'高中数学' => '25',
					'高中数学（理）' => '25',
					'高中数学（文）' => '25',
					'高中语文' => '11',
					'高中英语' => '12',
					'高中物理' => '13',
					'高中化学' => '14'
					);

					foreach ( $gradeSubjectRS as $key => $value ) {
						if (trim ( $gradeDeptName ) . trim ( $subjectName ) == $key) {
							$data ['subject_id'] = $value;
							break;
						}
					}

					/*
					* $sql = 'SELECT id FROM ' . $this->vip_dict_subject . ' WHERE title like ' . $this->dao->quote ( '%' . trim ( $subjectName ) . '%' ); if (! empty ( $data ['grade_dept_id'] )) $sql .= ' AND grade_id = ' . $this->dao->quote ( $data ['grade_dept_id'] ); $data ['subject_id'] = $this->dao->getOne ( $sql . ' LIMIT 1' ); if (empty ( $data ['subject_id'] )) { if ($this->dao->execute ( 'INSERT INTO ' . $this->vip_dict_subject . ' (grade_id,title) VALUES(' . $this->dao->quote ( $data ['grade_dept_id'] ) . ',' . $this->dao->quote ( trim ( $subjectName ) ) . ')' )) { $data ['subject_id'] = $this->dao->lastInsertId (); } }
					*/
				}
				// 课程类型
				/*
				* if (! empty ( $data ['courseTypeName'] )) { $sql2 = 'SELECT id FROM ' . $this->vip_dict_course_type . ' WHERE title like ' . $this->dao->quote ( '%' . trim ( $data ['courseTypeName'] ) . '%' ); if (! empty ( $data ['subject_id'] )) $sql2 .= ' AND subject_id = ' . $this->dao->quote ( $data ['subject_id'] ); $taojueQuestionCourseTypeId = $this->dao->getOne ( $sql2 . ' LIMIT 1' ); if (empty ( $taojueQuestionCourseTypeId )) { if ($this->dao->execute ( 'INSERT INTO ' . $this->vip_dict_course_type . ' (subject_id,title) VALUES(' . $this->dao->quote ( $data ['subject_id'] ) . ',' . $this->dao->quote ( trim ( $data ['courseTypeName'] ) ) . ')' )) { $taojueQuestionCourseTypeId = $this->dao->lastInsertId (); } } }
				*/

				if (! empty ( $data ['questionTypeName'] )) { // 题型
					/*
					* $sql3 = 'SELECT id FROM ' . $this->vip_dict_question_type . ' WHERE title = ' . $this->dao->quote ( trim ( $data ['questionTypeName'] ) ); if (! empty ( $data ['subject_id'] )) $sql3 .= ' AND subject_id = ' . $this->dao->quote ( $data ['subject_id'] ); $data ['question_type_id'] = $this->dao->getOne ( $sql3 . ' LIMIT 1' ); if (empty ( $data ['question_type_id'] )) { $code = $this->dao->getOne ( "SELECT code from " . $this->vip_dict . " where category = 'QUESTION_TYPE' AND title = '" . trim ( $data ['questionTypeName'] ) . "'" ); if ($code) { if ($this->dao->execute ( 'INSERT INTO ' . $this->vip_dict_question_type . ' (subject_id,question_type_code,title) VALUES(' . $this->dao->quote ( $data ['subject_id'] ) . ',' . $this->dao->quote ( $code ) . ',' . $this->dao->quote ( trim ( $data ['questionTypeName'] ) ) . ')' )) { $data ['question_type_id'] = $this->dao->lastInsertId (); } } }
					*/

					if (! empty ( $data ['subject_id'] )) {
						$sql3 = 'SELECT a.id
	  						 FROM ' . $this->vip_dict_question_type . ' a
	  						 LEFT JOIN ' . $this->vip_dict . ' b ON a.question_type_code = b.code AND b.category = \'QUESTION_TYPE\'
							 WHERE b.title = ' . $this->dao->quote ( trim ( $data ['questionTypeName'] ) );

						$sql3 .= ' AND a.subject_id = ' . $this->dao->quote ( $data ['subject_id'] );
						$data ['question_type_id'] = $this->dao->getOne ( $sql3 . ' LIMIT 1' );
					}

					// 插入题型
					/*
					* if (empty ( $data ['question_type_id'] )) { $code = $this->dao->getOne ( "SELECT code from " . $this->vip_dict . " where category = 'QUESTION_TYPE' AND title = '" . trim ( $data ['questionTypeName'] ) . "'" ); if ($code) { if ($this->dao->execute ( 'INSERT INTO ' . $this->vip_dict_question_type . ' (subject_id,question_type_code,title) VALUES(' . $this->dao->quote ( $data ['subject_id'] ) . ',' . $this->dao->quote ( $code ) . ',' . $this->dao->quote ( trim ( $data ['questionTypeName'] ) ) . ')' )) { $data ['question_type_id'] = $this->dao->lastInsertId (); } } }
					*/
				}

				// ligang start 14-09-16
				// 主知识点
				if (! empty ( $data ['knowledgeName'] )) {
					$data ['knowledge_id'] = '';
					$sql4 = 'SELECT id, parent_id, name FROM ' . $this->vip_knowledge . ' WHERE is_leaf = 1 AND name = ' . $this->dao->quote ( trim ( $data ['knowledgeName'] ) ) . ' limit 0, 1';
					$row = $this->dao->getRow ( $sql4 ); // 查找读取的主知点在知识点表中是否有
					if ($row) {
						$data ['knowledge_id'] = $row ['id'];
					}

					/*
					* if (! empty ( $data ['main_knowledge_id'] )) { $data ['main_root_id_arr'] = $this->dao->getAll ( 'SELECT knowledge_id FROM ' . $this->vip_knowledge_course_type_rs . ' WHERE course_type_id =' . $this->dao->quote ( $taojueQuestionCourseTypeId ) ); // 查找此课程下的所有根节点 $data ['main'] = ''; if (! empty ( $data ['main_root_id_arr'] )) { foreach ( $data ['main_root_id_arr'] as $main_id => $main_knowledge_knowledge_id ) { $data ['main'] .= $main_knowledge_knowledge_id ['knowledge_id'] . ','; } $data ['main'] = explode ( ',', trim ( $data ['main'], ',' ) ); } foreach ( $data ['main_knowledge_id'] as $kkk => $vvv ) { if ($vvv ['parent_id'] == 0) { // 查找父节点是否为根节点如为根节点则存储到数组 $data ['main_root_knowledge_id'] .= $vvv ['knowledge_id'] . ','; // 知识点的根节点 $data ['main_knowledge_knowledge_id'] .= $vvv ['knowledge_id'] . ','; // 知识点的ID } else { $root_main_id = $this->getRootKnowledgeId ( $vvv ['knowledge_id'] ); // 查找到根节点则存储到数组 $data ['main_root_knowledge_id'] .= $root_main_id ['id'] . ','; // 知识点的根节点 $data ['main_knowledge_knowledge_id'] .= $vvv ['knowledge_id'] . ','; // 知识点的ID } } $main_knowledge_id_array = $main_knowledge_name_array = $main_knowledge_combine_array = ''; $main_root_knowledge_id = explode ( ',', trim ( $data ['main_root_knowledge_id'], ',' ) ); // 根节点 $main_knowledge_knowledge_id = explode ( ',', trim ( $data ['main_knowledge_knowledge_id'], ',' ) ); // 知识ID $main_knowledge_combine_array = array_combine ( $main_root_knowledge_id, $main_knowledge_knowledge_id ); $biaozhi = false; foreach ( $main_knowledge_combine_array as $main_kk => $main_vv ) { if (in_array ( $main_kk, $data ['main'] )) { // 如果数组中的任意根节点在此课程类型下的根节点中出现过则为真 $data ['knowledge_id'] = $main_vv; $biaozhi = true; } } if ($biaozhi == false) { if ($this->dao->execute ( 'INSERT INTO ' . $this->vip_knowledge . ' (name,remark,is_leaf) VALUES(' . $this->dao->quote ( $data ['knowledgeName'] ) . ',' . $this->dao->quote ( $data ['knowledgeName'] ) . ',' . 1 . ')' )) { $temp_main_combine_knowledge_id = ''; $temp_main_combine_knowledge_id = $this->dao->lastInsertId (); $data ['knowledge_id'] = $temp_main_combine_knowledge_id; if (! empty ( $temp_main_combine_knowledge_id )) { $this->dao->execute ( 'INSERT INTO ' . $this->vip_knowledge_course_type_rs . ' (knowledge_id,course_type_id) VALUES(' . $this->dao->quote ( $data ['knowledge_id'] ) . ',' . $this->dao->quote ( $taojueQuestionCourseTypeId ) . ')' ); } } } } else { // 如果没有则插入此知识点 if ($this->dao->execute ( 'INSERT INTO ' . $this->vip_knowledge . ' (name,remark,is_leaf) VALUES(' . $this->dao->quote ( $data ['knowledgeName'] ) . ',' . $this->dao->quote ( $data ['knowledgeName'] ) . ',' . 1 . ')' )) { $temp2_main_combine_knowledge_id = ''; $temp2_main_combine_knowledge_id = $this->dao->lastInsertId (); $data ['knowledge_id'] = $temp2_main_combine_knowledge_id; if (! empty ( $temp2_main_combine_knowledge_id )) { $this->dao->execute ( 'INSERT INTO ' . $this->vip_knowledge_course_type_rs . ' (knowledge_id,course_type_id) VALUES(' . $this->dao->quote ( $temp2_main_combine_knowledge_id ) . ',' . $this->dao->quote ( $taojueQuestionCourseTypeId ) . ')' ); } } }
					 */
				}
				// ligang end 14-09-16
				
				// 副知识点
				if (! empty ( $data ['subKnowledgeName'] )) {
					$subKnowledgeIdArr = array ();
					// 可用中文，
					$subKnowledgeNameArr = explode ( ',', trim ( trim ( str_replace ( '，', ',', $data ['subKnowledgeName'] ), ',' ) ) );
					
					foreach ( $subKnowledgeNameArr as $key => $value ) {
						$sql4 = 'SELECT id, parent_id, name FROM ' . $this->vip_knowledge . ' WHERE is_leaf = 1 AND name = ' . $this->dao->quote ( trim ( $value ) ) . ' limit 0, 1';
						$row = $this->dao->getRow ( $sql4 ); // 查找读取的主知点在知识点表中是否有
						if ($row) {
							$subKnowledgeIdArr [] = $row ['id'];
						}
					}
					
					$data ['sub_knowledge_id'] = implode ( ',', $subKnowledgeIdArr );
					
					/*
					 * $data ['sub_knowledge_id'] = $data ['root_id_arr'] = ''; $data ['sub_root_knowledge_id'] = $data ['sub_knowledge_knowledge_id'] = $data ['sub_knowledge_knowledge_name'] = $data ['root_id_str'] = ''; $knowledge_count_array = array (); $root_id_str_array = array (); $sub_root_knowledge_id = array (); $sub_knowledge_knowledge_id = array (); $sub_knowledge_combine_array = array (); $new_combine_knowledge_name_count = array (); $sub_knowledge_knowledge_name = array (); $new_sub_root_knowledge = array (); $new_sub_knowledge_Id_name = array (); $data ['sub_knowledge_id_arr'] = $data ['subKnowledgeNameArr'] = ''; $data ['subKnowledgeNameArr'] = explode ( ',', trim ( trim ( str_replace ( '，', ',', $data ['subKnowledgeName'] ), ',' ) ) ); // $data ['subKnowledgeNameStr'] = "'" . implode ( "','", $data ['subKnowledgeNameArr'] ) . "'"; if (! empty ( $data ['subKnowledgeNameArr'] )) { $data ['root_id_arr'] = $this->dao->getAll ( "SELECT knowledge_id FROM " . $this->vip_knowledge_course_type_rs . " WHERE course_type_id = '$data[course_type_id]'" ); // 查找根节点 if (! empty ( $data ['root_id_arr'] )) { // 查找此课程类型下有哪些根节点 foreach ( $data ['root_id_arr'] as $kk => $root ) { $data ['root_id_str'] .= $root ['knowledge_id'] . ','; } $root_id_str_array = explode ( ',', trim ( $data ['root_id_str'], ',' ) ); // 此课程类型下的所有根节点 } foreach ( $data ['subKnowledgeNameArr'] as $sub_key => $sub_val ) { $data ['sub_knowledge_id_arr'] = $this->dao->getAll ( 'SELECT id as knowledge_id,parent_id,name FROM ' . $this->vip_knowledge . " WHERE name = '$sub_val'" ); if (! empty ( $data ['sub_knowledge_id_arr'] )) { // 根据副知识点名称查找副知点ID与父节点 foreach ( $data ['sub_knowledge_id_arr'] as $key => $row ) { if ($row ['parent_id'] == 0) { // 查找父节点是否为根节点如为根节点则存储到数组 $data ['sub_root_knowledge_id'] .= $row ['knowledge_id'] . ','; // 根节点 $data ['sub_knowledge_knowledge_id'] .= $row ['knowledge_id'] . ','; // 副知识点ID $data ['sub_knowledge_knowledge_name'] .= $row ['name'] . ','; // 副知识点名称 } else { $root_id = $this->getRootKnowledgeId ( $row ['knowledge_id'] ); // 查找到根节点则存储到数组 $data ['sub_root_knowledge_id'] .= $root_id ['id'] . ','; // 根节点 $data ['sub_knowledge_knowledge_id'] .= $row ['knowledge_id'] . ','; // 副知识点ID $data ['sub_knowledge_knowledge_name'] .= $row ['name'] . ','; // 副知识点名称 } } // ligang 14-09-16 start $sub_root_knowledge_id = explode ( ',', trim ( $data ['sub_root_knowledge_id'], ',' ) ); // 根节点 $sub_knowledge_knowledge_id = explode ( ',', trim ( $data ['sub_knowledge_knowledge_id'], ',' ) ); // 副知识点ID $sub_knowledge_knowledge_name = explode ( ',', trim ( $data ['sub_knowledge_knowledge_name'], ',' ) ); // 副知识点名称 $new_sub_root_knowledge = array_combine ( $sub_root_knowledge_id, $sub_knowledge_knowledge_id ); // 根节点与知识点ID结合 $new_sub_root_knowledge_re = array_flip ( $new_sub_root_knowledge ); // 反转根节点与知识点 $new_sub_knowledge_Id_name = array_combine ( $new_sub_root_knowledge_re, $sub_knowledge_knowledge_name ); // 知识点ID与 知识点名称结合 $new_combine_knowledge_name_count = array_keys ( array_count_values ( $new_sub_knowledge_Id_name ) ); // 组合起来数组，组合为一个子知识点名称对应多个根节点与知识点ID foreach ( $new_combine_knowledge_name_count as $ek => $ev ) { foreach ( $new_sub_knowledge_Id_name as $k => $v ) { if ($v == $ev) { $knowledge_count_array [$ev] [$k] = $new_sub_root_knowledge [$k]; } } } $knowledge_count_array = array_filter ( $knowledge_count_array ); $root_id_str_array = array_filter ( $root_id_str_array ); if (! empty ( $knowledge_count_array )) { foreach ( $knowledge_count_array as $sub_knowledge_k => $sub_knowledge_val ) { $sub_biaozhi = false; foreach ( $sub_knowledge_val as $sub_knowledge_val_k => $sub_knowledge_val_val ) { if (in_array ( $sub_knowledge_val_k, $root_id_str_array )) { // 如果同名名称中的一个根节点属于此课程类型下的根节点，则记录下来此知识点ID $sub_biaozhi = true; $data ['sub_knowledge_id'] .= $sub_knowledge_val_val . ','; } } if ($sub_biaozhi == false) { if ($this->dao->execute ( 'INSERT INTO ' . $this->vip_knowledge . ' SET name =' . $this->dao->quote ( $sub_knowledge_k ) . ',' . ' remark = ' . $this->dao->quote ( $sub_knowledge_k ) . ',' . ' is_leaf=1' )) { $temp_combine_knowledge_id = ''; $temp_combine_knowledge_id = $this->dao->lastInsertId (); $data ['sub_knowledge_id'] .= $temp_combine_knowledge_id . ','; if (! empty ( $temp_combine_knowledge_id )) { $this->dao->execute ( 'INSERT INTO ' . $this->vip_knowledge_course_type_rs . ' (knowledge_id,course_type_id) VALUES(' . $this->dao->quote ( $temp_combine_knowledge_id ) . ',' . $this->dao->quote ( $taojueQuestionCourseTypeId ) . ')' ); } } } } } // liang end } else { // 如未查到则进行插入--要想插入只能当根节点进行插入且插入当前课程类型下 $temp_knowledge_id = ''; if ($this->dao->execute ( 'INSERT INTO ' . $this->vip_knowledge . ' SET name =' . $this->dao->quote ( $sub_val ) . ',' . ' remark = ' . $this->dao->quote ( $sub_val ) . ',' . ' is_leaf=1' )) { $temp_knowledge_id = $this->dao->lastInsertId (); $data ['sub_knowledge_id'] .= $temp_knowledge_id . ','; } if (! empty ( $temp_knowledge_id )) { $this->dao->execute ( 'INSERT INTO ' . $this->vip_knowledge_course_type_rs . ' (knowledge_id,course_type_id) VALUES(' . $this->dao->quote ( $temp_knowledge_id ) . ',' . $this->dao->quote ( $taojueQuestionCourseTypeId ) . ')' ); } } } $data ['sub_knowledge_id'] = array_unique ( explode ( ',', trim ( $data ['sub_knowledge_id'], ',' ) ) ); $data ['sub_knowledge_id'] = implode ( ',', $data ['sub_knowledge_id'] ); }
					 */
				}
				/*
				 * $sql5 = 'SELECT id as knowledge_id,parent_id,name FROM ' . $this->vip_knowledge . " WHERE name IN (" . trim ( $data ['subKnowledgeNameStr'] ) . ") "; $data ['sub_knowledge_id_arr'] = $this->dao->getAll ( $sql5 ); $data ['sub_knowledge_id'] = ''; if (! empty ( $data ['sub_knowledge_id_arr'] )) {//根据副知识点名称查找副知点ID与父节点 foreach ( $data ['sub_knowledge_id_arr'] as $key => $row ) { if ($row ['parent_id'] == 0) {//查找父节点是否为根节点如为根节点则存储到数组 $data ['sub_root_knowledge_id'] .= $row ['knowledge_id'] . ',';//根节点 $data ['sub_knowledge_knowledge_id'] .= $row ['knowledge_id'] . ',';//副知识点ID $data ['sub_knowledge_knowledge_name'] .= $row ['name'] . ',';//副知识点名称 } else { $root_id = $this->getRootKnowledgeId ( $row ['knowledge_id'] );//查找到根节点则存储到数组 $data ['sub_root_knowledge_id'] .= $root_id ['id'] . ',';//根节点 $data ['sub_knowledge_knowledge_id'] .= $row ['knowledge_id'] . ',';//副知识点ID $data ['sub_knowledge_knowledge_name'] .= $row ['name'] . ',';//副知识点名称 } } $data ['root_id_arr'] = $this->dao->getAll ( "SELECT knowledge_id FROM " . $this->vip_knowledge_course_type_rs . " WHERE course_type_id = '$data[course_type_id]'" );//查找根节点 $data ['root_id_str'] = ''; if (! empty ( $data ['root_id_arr'] )) {//查找此课程类型下有哪些根节点 foreach ( $data ['root_id_arr'] as $kk => $root ) { $data ['root_id_str'] .= $root ['knowledge_id'] . ','; } } //ligang 14-09-16 start $sub_root_knowledge_id = $root_id_str_array = $sub_knowledge_knowledge_id = $sub_knowledge_combine_array =''; $root_id_str_array = explode(',',trim ( $data ['root_id_str'], ',' ));//此课程类型下的所有根节点 $sub_root_knowledge_id = explode(',',trim ( $data ['sub_root_knowledge_id'], ',' ));//根节点 $sub_knowledge_knowledge_id = explode(',',trim ( $data ['sub_knowledge_knowledge_id'], ',' ));//副知识点ID $sub_knowledge_knowledge_name = explode(',',trim ( $data ['sub_knowledge_knowledge_name'], ',' ));//副知识点名称 $new_sub_root_knowledge = array_combine($sub_root_knowledge_id, $sub_knowledge_knowledge_id);//根节点与知识点ID结合 $new_sub_root_knowledge_re = array_flip($new_sub_root_knowledge);//反转根节点与知识点 $new_sub_knowledge_Id_name = array_combine($new_sub_root_knowledge_re,$sub_knowledge_knowledge_name);//知识点ID与 知识点名称结合 $new_combine_knowledge_name_count = array_keys(array_count_values ($new_sub_knowledge_Id_name)); $knowledge_count_array = array(); //组合起来数组，组合为一个子知识的名称对应多个根节点与知识点ID foreach ($new_combine_knowledge_name_count as $ek=>$ev){ foreach ($new_sub_knowledge_Id_name as $k=>$v){ if($v==$ev){ $knowledge_count_array[$ev][$k] = $new_sub_root_knowledge[$k]; } } } foreach($knowledge_count_array as $sub_knowledge_k=>$sub_knowledge_val){ $sub_biaozhi = false; foreach($sub_knowledge_val as $sub_knowledge_val_k=>$sub_knowledge_val_val){ if(in_array($sub_knowledge_val_k,$root_id_str_array)){//如果同名名称中的一个根节点属于此课程类型下的根节点，则记录下来此知识点ID $sub_biaozhi = true; $data['sub_knowledge_id'] .= $sub_knowledge_val_val. ','; } } if($sub_biaozhi == false){ if ($this->dao->execute ('INSERT INTO ' . $this->vip_knowledge . ' SET name ='.$this->dao->quote ( $sub_knowledge_k ).','.	' remark = '.$this->dao->quote ( $sub_knowledge_k ).','.' is_leaf=1')) { $temp_combine_knowledge_id = ''; $temp_combine_knowledge_id = $this->dao->lastInsertId (); $data ['sub_knowledge_id'] .= $temp_combine_knowledge_id . ','; if (! empty ( $temp_combine_knowledge_id )) { $this->dao->execute ( 'INSERT INTO ' . $this->vip_knowledge_course_type_rs . ' (knowledge_id,course_type_id) VALUES(' . $this->dao->quote ( $temp_combine_knowledge_id ) . ',' . $this->dao->quote ( $taojueQuestionCourseTypeId ) . ')' ); } } } } //liang end } else {//如未查到则进行插入--要想插入只能当根节点进行插入 if (! empty ( $data ['subKnowledgeNameArr'] )) { foreach ( $data ['subKnowledgeNameArr'] as $kk => $subKnowledgeName ) { //echo 'SELECT id FROM ' . $this->vip_knowledge . ' WHERE name = ' . $this->dao->quote ( $subKnowledgeName ) . ' LIMIT 1';exit; $temp_knowledge_id = $this->dao->getOne ( 'SELECT id FROM ' . $this->vip_knowledge . ' WHERE name = ' . $this->dao->quote ( $subKnowledgeName ) . ' LIMIT 1' ); if (empty ( $temp_knowledge_id )) { //echo 'INSERT INTO ' . $this->vip_knowledge . ' SET name ='.$this->dao->quote ( $subKnowledgeName ).','.	' remark = '.$this->dao->quote ( $subKnowledgeName ).','.' is_leaf=1';exit; if ($this->dao->execute ('INSERT INTO ' . $this->vip_knowledge . ' SET name ='.$this->dao->quote ( $subKnowledgeName ).','.	' remark = '.$this->dao->quote ( $subKnowledgeName ).','.' is_leaf=1')) { $temp_knowledge_id = $this->dao->lastInsertId (); $data ['sub_knowledge_id'] .= $temp_knowledge_id . ','; } } if (! empty ( $temp_knowledge_id )) { $this->dao->execute ( 'INSERT INTO ' . $this->vip_knowledge_course_type_rs . ' (knowledge_id,course_type_id) VALUES(' . $this->dao->quote ( $temp_knowledge_id ) . ',' . $this->dao->quote ( $taojueQuestionCourseTypeId ) . ')' ); } } } } $data ['sub_knowledge_id'] = trim ( $data ['sub_knowledge_id'], ',' );
				 */
				
				if (! empty ( $data ['gradesName'] )) { // 适用年级
					$data ['gradesNameArr'] = explode ( ',', trim ( trim ( str_replace ( '，', ',', $data ['gradesName'] ), ',' ) ) );
					$data ['gradesNameStr'] = "'" . implode ( "','", $data ['gradesNameArr'] ) . "'";
					$sql6 = 'SELECT  id FROM ' . $this->vip_dict . " WHERE category = 'GRADE' AND title IN (" . $data ['gradesNameStr'] . ')';
					$data ['grades_id_arr'] = $this->dao->getAll ( $sql6 );
					$data ['grades'] = '';
					if (! empty ( $data ['grades_id_arr'] )) {
						foreach ( $data ['grades_id_arr'] as $key => $row ) {
							$data ['grades'] .= $row ['id'] . ',';
						}
					}
					/*
					 * else { if (! empty ( $data ['gradesNameArr'] )) { foreach ( $data ['gradesNameArr'] as $kk => $gradeName ) { $last_grade_id = $this->dao->getOne ( "SELECT id FROM " . $this->vip_dict . " WHERE category = 'GRADE' ORDER BY id DESC" ); $new_code = str_pad ( 'GR2', 6, $last_grade_id ); if ($this->dao->execute ( 'INSERT INTO ' . $this->vip_dict . ' (category,code,title) VALUES(' . $this->dao->quote ( 'GRADE' ) . ',' . $this->dao->quote ( $new_code ) . ',' . $this->dao->quote ( $gradeName ) . ')' )) { $new_grade_id = $this->dao->lastInsertId (); $data ['grades'] .= $new_grade_id . ','; } } } }
					 */
					$data ['grades'] = trim ( $data ['grades'], ',' );
				}
				
				$paper_flag = true;
				$flag = true;
				$this->dao->execute ( 'begin' ); // 事务开启
				                                 // 套卷
				                                 // if (isset ( $data ['source'] ) || isset ( $data ['year'] ) || isset ( $data ['city'] ) || isset ( $data ['country'] ) || isset ( $data ['school'] ) && isset ( $data ['paper_grades'] ) && isset ( $data ['term'] ) && isset ( $data ['name'] ) && isset ( $data ['curr_dept'] )) {
				                                 // dumps ( $data );
				                                 
				// 分割文件名：套卷-2011-北京市-昌平区--初中-物理-初三-下学期-中考二模-班课-真题-总时长-总分数-总题数.docx
				$paper = array ();
				if (! empty ( $title )) {
					$title1 = substr ( $title, 0, strrpos ( $title, '.' ) );
					$taojue = $createUserName = '';
					$taojue = explode ( '-', $title1 );
					$paper ['filename'] = $taojue [0];
					$paper ['year'] = $taojue [1];
					$paper ['city'] = $taojue [2];
					$paper ['country'] = $taojue [3];
					$paper ['school'] = $taojue [4];
					// $paper['grade_dept_id'] = $taojue[5];
					$paper ['subject_id'] = $taojue [6];
					$paper ['paper_grades'] = $taojue [7];
					$paper ['term'] = $taojue [8];
					$paper ['name'] = $taojue [9];
					$paper ['curr_dept'] = $taojue [10];
					$paper ['source'] = $taojue [11];
					$paper ['duration'] = $taojue [12];
					$paper ['score'] = $taojue [13];
					$paper ['question_number'] = $taojue [14];
					// $createUserName = explode ( '.', $taojue [15] );
					// $paper ['created_user_name'] = $createUserName [0];
					$paper ['created_user_name'] = $taojue [15];
				}
				
				// 套卷
				if (trim ( $paper ['filename'] ) == '套卷') {
					if (empty ( $paper_id )) {
						$sql8 = 'INSERT INTO ' . $this->vip_paper . '(	grade_id,
																		subject_id,
																		name,
																		source,
																		year,
																		city,
																		country,
																		school,
																		grades,
																		term,
																		department,
																		created_time,
																		created_user_name,
																		approve_user_name,
																		duration,
																		score,
																		question_number,
																		file_name,
																		file_name_md5)
														VALUES (' . $this->dao->quote ( $data ['grade_dept_id'] ) . ',
																' . $this->dao->quote ( $data ['subject_id'] ) . ',
																' . $this->dao->quote ( $paper ['name'] ) . ',
																' . $this->dao->quote ( $paper ['source'] ) . ',
																' . $this->dao->quote ( $paper ['year'] ) . ',
																' . $this->dao->quote ( $paper ['city'] ) . ',
																' . $this->dao->quote ( $paper ['country'] ) . ',
																' . $this->dao->quote ( $paper ['school'] ) . ',
																' . $this->dao->quote ( $paper ['paper_grades'] ) . ',
																' . $this->dao->quote ( $paper ['term'] ) . ',
																' . $this->dao->quote ( $paper ['curr_dept'] ) . ',
																' . $this->dao->quote ( strtotime ( date ( "Y-m-d H:i:s" ) ) ) . ',
																' . $this->dao->quote ( $paper ['created_user_name'] ) . ' ,
																' . $this->dao->quote ( '' ) . ' ,
																' . $this->dao->quote ( $paper ['duration'] ) . ',
																' . $this->dao->quote ( $paper ['score'] ) . ',
																' . $this->dao->quote ( $paper ['question_number'] ) . ',
																' . $this->dao->quote ( $title ) . ',
																' . $this->dao->quote ( md5 ( $title ) ) . ')';
						
						if (! $this->dao->execute ( $sql8 )) {
							$flag == false;
						} else {
							$paper_id = $this->dao->lastInsertId ();
						}
					}
					$question_source = 'CLASS';
				} else if (trim ( $paper ['filename'] ) == '套卷VIP') {
					if (empty ( $paper_id )) {
						$sql8 = 'INSERT INTO ' . $this->vip_paper . '(	grade_id,
																		subject_id,
																		file_name,
																		file_name_md5)
														VALUES (' . $this->dao->quote ( $data ['grade_dept_id'] ) . ',
																' . $this->dao->quote ( $data ['subject_id'] ) . ',
																' . $this->dao->quote ( $title ) . ',
																' . $this->dao->quote ( md5 ( $title ) ) . ')';
						
						if (! $this->dao->execute ( $sql8 )) {
							$flag == false;
						} else {
							$paper_id = $this->dao->lastInsertId ();
						}
					}
					$question_source = 'VIP';
				} else if (trim ( $paper ['filename'] ) == '试题') {
					$title12 = explode ( '-', $title );
					if ($title12 [3] == '班课') {
						$question_source = 'CLASS';
					} else if ($title12 [3] == 'VIP') {
						$question_source = 'VIP';
					} else if ($title12 [3] == '竞赛') {
						$question_source = 'MATCH';
					}
				}
				
				// $data['content'] = strip_tags($data['content'] ,'<img>');
				// $data['analysis'] = strip_tags($data['analysis'] ,'<img>');
				if (! $this->dao->execute ( 'INSERT INTO ' . $this->vip_question . ' (uid,
																					  course_type_id,
																					  question_type_id,
																					  number,
																					  score,
																					  difficulty,
																					  knowledge_id,
																					  sub_knowledge_id,
																					  grades,
																					  content,
																					  content_text,
																					  analysis,
																					  analysis_text,
																	 				  paper_id,
																					  created_time,
																					  created_answer_user_name,
																					  last_updated_time,
																					  sdate,
																					  department,
																					  source,
																					  status) 
														VALUES (' . $this->dao->quote ( $data ['uid'] ) . ',
																' . $this->dao->quote ( $taojueQuestionCourseTypeId ) . ',
																' . $this->dao->quote ( $data ['question_type_id'] ) . ',
																' . $this->dao->quote ( $data ['question_number'] ) . ',
																' . $this->dao->quote ( $data ['score'] ) . ',
																' . $this->dao->quote ( $data ['difficulty'] ) . ',
																' . $this->dao->quote ( $data ['knowledge_id'] ) . ',
																' . $this->dao->quote ( $data ['sub_knowledge_id'] ) . ',
																' . $this->dao->quote ( $data ['grades'] ) . ',
																' . $this->dao->quote ( $data ['content'] ) . ',
																' . $this->dao->quote ( $data ['content_text'] ) . ',
																' . $this->dao->quote ( $data ['analysis'] ) . ',
																' . $this->dao->quote ( $data ['analysis_text'] ) . ',
																' . $this->dao->quote ( $paper_id ) . ',
																' . $this->dao->quote ( $time ) . ' ,
																' . $this->dao->quote ( $paper ['created_user_name'] ) . ',
																' . $this->dao->quote ( $time ) . ' ,
																' . $this->dao->quote ( $data ['sdate'] ) . ' ,
																' . $this->dao->quote ( $question_source ) . ' ,
																' . $this->dao->quote ( $data ['source1'] ) . ' ,
																1
														)' )) {
					$flag == false;
				}
				$new_question_id = $this->dao->lastInsertId ();
				
				// 选项
				if (! empty ( $data ['options'] )) {
					$sort = 0;
					foreach ( $data ['options'] as $key => $option ) {
						$option ['is_answer'] = 0;
						if (strpos ( '0' . $data ['answers_text'] . '0', $option ['title'] ))
							$option ['is_answer'] = 1;
						$sort ++;
						if (! $this->dao->execute ( 'INSERT INTO ' . $this->vip_question_option . ' (
																					uid,
																					question_id,
																					content,
																					content_text,
																					sort,
																					is_answer)
								VALUES (' . $this->dao->quote ( $option ['uid'] ) . ',
										' . $this->dao->quote ( $new_question_id ) . ',
										' . $this->dao->quote ( $option ['content'] ) . ',
										' . $this->dao->quote ( $option ['content_text'] ) . ',
										' . $this->dao->quote ( $sort ) . ',
										' . $this->dao->quote ( $option ['is_answer'] ) . ')' )) {
							$flag == false;
							break;
						}
					}
				}
				// 答案
				if (! empty ( $data ['answers'] ) && (trim ( $data ['questionTypeName'] ) != '单选题' && trim ( $data ['questionTypeName'] ) != '多选题' && trim ( $data ['questionTypeName'] ) != '选择题' && trim ( $data ['questionTypeName'] ) != '不定项选择题')) {
					// $data ['answers'] = strip_tags($data ['answers'] ,'<img>');
					if ($this->dao->execute ( 'INSERT INTO ' . $this->vip_question_answer . ' (
																					question_id,
																					content,
																					content_text)
								VALUES (' . $this->dao->quote ( $new_question_id ) . ',
										' . $this->dao->quote ( $data ['answers'] ) . ',
										' . $this->dao->quote ( $data ['answers_text'] ) . ')' )) {
						$flag == false;
					}
				}
				
				// if (! empty ( $data ['childQuestion'] )) { // 导入子题
				// $flag = $this->importQuestion ( $data ['childQuestion'] );
				// }
				if ($flag === false)
					$this->dao->execute ( 'rollback' ); // 事务回滚
				else
					$this->dao->execute ( 'commit' ); // 事务提交
			}
		}
		return $flag;
	}
	
	/**
	 * *存储修改后的试题
	 * *这个暂且测试，添加试题成功后改为事务提交
	 * *
	 */
	public function edit_save_Question($model = array()) {
		$time = time ();
		if ($model ['id']) {
			$strQuery = 'UPDATE ' . $this->vip_question . '
	                     SET 	 content=' . $this->dao->quote ( $model ['content'] ) . ',
	                             content_text=' . $this->dao->quote ( strip_tags ( $model ['content'] ) ) . ',
	                             analysis=' . $this->dao->quote ( $model ['analysis'] ) . ',
	                             last_updated_user_name=' . $this->dao->quote ( $model ['user_name'] ) . ',
	                             last_updated_time=' . $this->dao->quote ( $time ) . '
	                    WHERE 	id=' . $this->dao->quote ( $model ['id'] );
			
			if (false == $this->dao->execute ( $strQuery ))
				return array (
						'errorMsg' => '试题修改保存失败' 
				);
				
				// 选项
			$options = $model ['options'];
			$euids = $model ['euids'];
			
			if (! empty ( $options )) {
				// 删除掉以前的选项
				$strOptions_del = ' DELETE FROM ' . $this->vip_question_option . '	WHERE  question_id = ' . $this->dao->quote ( $model ['id'] );
				if (false == $this->dao->execute ( $strOptions_del ))
					return array (
							'errorMsg' => '试题修改保存失败' 
					);
					// 添加新的选项
				$answers = $model ['options_answer_flag'];
				for($i = 0; $i < count ( $options ); $i ++) {
					$strQ = 'INSERT INTO ' . $this->vip_question_option . '
							SET 			uid =	' . $this->dao->quote ( $euids [$i] ) . ',
											question_id =	' . $this->dao->quote ( $model ['id'] ) . ',
											content =	' . $this->dao->quote ( $options [$i] ) . ',
											sort =	' . $this->dao->quote ( $i ) . ',
											is_answer=	' . $this->dao->quote ( in_array ( $i, $answers ) ? 1 : 0 );
					if (false == $this->dao->execute ( $strQ ))
						return array (
								'errorMsg' => '选项信息保存失败' 
						);
				}
			}
			
			// 答案
			$answers = $model ['answers'];
			if (! empty ( $answers )) {
				
				// 删除掉以前的答案
				$answer_del = ' DELETE FROM ' . $this->vip_question_answer . '	WHERE  question_id = ' . $this->dao->quote ( $model ['id'] );
				if (false == $this->dao->execute ( $answer_del ))
					return array (
							'errorMsg' => '答案修改保存失败' 
					);
				for($i = 0; $i < count ( $answers ); $i ++) {
					
					$strA = 'INSERT INTO ' . $this->vip_question_answer . '
							SET 			question_id =	' . $this->dao->quote ( $model ['id'] ) . ',
											content =	' . $this->dao->quote ( $answers [$i] ) . ',
											sort =	' . $this->dao->quote ( $answers [$i] ['sort'] );
					if (false == $this->dao->execute ( $strA ))
						return array (
								'errorMsg' => '答案信息保存失败' 
						);
				}
			}
			
			// 答案
			$answers = $model ['answers'];
			if (! empty ( $answers )) {
				for($i = 0; $i < count ( $answers ); $i ++) {
					
					$strAnswers = 'UPDATE ' . $this->vip_question_answer . '
							SET content=' . $this->dao->quote ( $answers [$i] ['content'] ) . ',
								sort=' . $this->dao->quote ( $answers [$i] ['sort'] ) . '
							WHERE id=' . $this->dao->quote ( $model ['id'] );
					if (false == $this->dao->execute ( $strAnswers ))
						return array (
								'errorMsg' => '答案保存失败' 
						);
				}
			}
			return true;
		} else
			return false;
	}
	public function edit_save_simple_question($model = array()) {
		$time = time ();
		if ($model ['id']) {
			$strQuery = 'UPDATE ' . $this->vip_question . '
	                     SET 	 content=' . $this->dao->quote ( $model ['content'] ) . ',
	                             content_text=' . $this->dao->quote ( strip_tags ( $model ['content'] ) ) . ',
	                             analysis=' . $this->dao->quote ( $model ['analysis'] ) . ',
	                             analysis_text=' . $this->dao->quote ( strip_tags ( $model ['analysis'] ) ) . ',
	                             content_error_types=' . $this->dao->quote ( strip_tags ( $model ['content_error_types'] ) ) . ',
	                             is_edit1 = 1,
								 in_used1 = 0,
	                             lock_row_time1 = NULL,
	                             last_updated_user_name=' . $this->dao->quote ( $model ['user_name'] ) . ',
	                             last_updated_time=' . $this->dao->quote ( $time ) . ',
						 		 content_last_updated_time = ' . $this->dao->quote ( time () ) . '
	                     WHERE	 id=' . $this->dao->quote ( $model ['id'] );
			
			if (false == $this->dao->execute ( $strQuery ))
				return array (
						'errorMsg' => '试题修改保存失败' 
				);
				
				// 选项
			$options = $model ['options'];
			$euids = $model ['euids'];
			if (! empty ( $options )) {
				// 删除掉以前的选项
				$strOptions_del = ' DELETE FROM ' . $this->vip_question_option . '	WHERE  question_id = ' . $this->dao->quote ( $model ['id'] );
				if (false == $this->dao->execute ( $strOptions_del ))
					return array (
							'errorMsg' => '试题修改保存失败' 
					);
					// 添加新的选项
				$answers = $model ['options_answer_flag'];
				for($i = 0; $i < count ( $options ); $i ++) {
					$strQ = 'INSERT INTO ' . $this->vip_question_option . '
							 SET 			uid =	' . $this->dao->quote ( $euids [$i] ) . ',
											question_id =	' . $this->dao->quote ( $model ['id'] ) . ',
											content =	' . $this->dao->quote ( $options [$i] ) . ',
											content_text =	' . $this->dao->quote ( strip_tags ( $options [$i] ) ) . ',
											sort =	' . $this->dao->quote ( $i + 1 ) . ',
											is_answer=	' . $this->dao->quote ( in_array ( $i, $answers ) ? 1 : 0 );
					if (false == $this->dao->execute ( $strQ ))
						return array (
								'errorMsg' => '选项信息保存失败' 
						);
				}
			}
			
			// 答案
			$answers = $model ['answers'];
			if (! empty ( $answers )) {
				
				// 删除掉以前的答案
				$answer_del = ' DELETE FROM ' . $this->vip_question_answer . '	WHERE  question_id = ' . $this->dao->quote ( $model ['id'] );
				if (false == $this->dao->execute ( $answer_del ))
					return array (
							'errorMsg' => '答案修改保存失败' 
					);
				for($i = 0; $i < count ( $answers ); $i ++) {
					
					$strA = 'INSERT INTO ' . $this->vip_question_answer . '
							SET 			question_id =	' . $this->dao->quote ( $model ['id'] ) . ',
											content =	' . $this->dao->quote ( $answers [$i] ) . ',
											sort =	' . $this->dao->quote ( $answers [$i] ['sort'] );
					if (false == $this->dao->execute ( $strA ))
						return array (
								'errorMsg' => '答案信息保存失败' 
						);
				}
			}
			
			// 答案
			$answers = $model ['answers'];
			if (! empty ( $answers )) {
				for($i = 0; $i < count ( $answers ); $i ++) {
					
					$strAnswers = 'UPDATE ' . $this->vip_question_answer . '
							SET content=' . $this->dao->quote ( $answers [$i] ['content'] ) . ',
								sort=' . $this->dao->quote ( $answers [$i] ['sort'] ) . '
							WHERE id=' . $this->dao->quote ( $model ['id'] );
					if (false == $this->dao->execute ( $strAnswers ))
						return array (
								'errorMsg' => '答案保存失败' 
						);
				}
			}
			return true;
		} else
			return false;
	}
	public function edit_save_classic_question($model = array()) {
		$time = time ();
		if ($model ['id']) {
			$strQuery = 'UPDATE ' . $this->vip_question . '
	                     SET 	 content=' . $this->dao->quote ( $model ['content'] ) . ',
	                             content_text=' . $this->dao->quote ( strip_tags ( $model ['content'] ) ) . ',
	                             analysis=' . $this->dao->quote ( $model ['analysis'] ) . ',
	                             analysis_text=' . $this->dao->quote ( strip_tags ( $model ['analysis'] ) ) . ',
	                             content_error_types=' . $this->dao->quote ( strip_tags ( $model ['content_error_types'] ) ) . ',
	                             is_edit2 = 1,
								 in_used2 = 0,
	                             lock_row_time2 = NULL,
	                             last_updated_user_name=' . $this->dao->quote ( $model ['user_name'] ) . ',
	                             last_updated_time=' . $this->dao->quote ( $time ) . ',
						 		 content_last_updated_time = ' . $this->dao->quote ( time () ) . '
	                     WHERE	 id=' . $this->dao->quote ( $model ['id'] );
			
			if (false == $this->dao->execute ( $strQuery ))
				return array (
						'errorMsg' => '试题修改保存失败' 
				);
				
				// 选项
			$options = $model ['options'];
			$euids = $model ['euids'];
			if (! empty ( $options )) {
				// 删除掉以前的选项
				$strOptions_del = ' DELETE FROM ' . $this->vip_question_option . '	WHERE  question_id = ' . $this->dao->quote ( $model ['id'] );
				if (false == $this->dao->execute ( $strOptions_del ))
					return array (
							'errorMsg' => '试题修改保存失败' 
					);
					// 添加新的选项
				$answers = $model ['options_answer_flag'];
				for($i = 0; $i < count ( $options ); $i ++) {
					$strQ = 'INSERT INTO ' . $this->vip_question_option . '
							 SET 			uid =	' . $this->dao->quote ( $euids [$i] ) . ',
											question_id =	' . $this->dao->quote ( $model ['id'] ) . ',
											content =	' . $this->dao->quote ( $options [$i] ) . ',
											content_text =	' . $this->dao->quote ( strip_tags ( $options [$i] ) ) . ',
											sort =	' . $this->dao->quote ( $i + 1 ) . ',
											is_answer=	' . $this->dao->quote ( in_array ( $i, $answers ) ? 1 : 0 );
					if (false == $this->dao->execute ( $strQ ))
						return array (
								'errorMsg' => '选项信息保存失败' 
						);
				}
			}
			
			// 答案
			$answers = $model ['answers'];
			if (! empty ( $answers )) {
				
				// 删除掉以前的答案
				$answer_del = ' DELETE FROM ' . $this->vip_question_answer . '	WHERE  question_id = ' . $this->dao->quote ( $model ['id'] );
				if (false == $this->dao->execute ( $answer_del ))
					return array (
							'errorMsg' => '答案修改保存失败' 
					);
				for($i = 0; $i < count ( $answers ); $i ++) {
					
					$strA = 'INSERT INTO ' . $this->vip_question_answer . '
							SET 			question_id =	' . $this->dao->quote ( $model ['id'] ) . ',
											content =	' . $this->dao->quote ( $answers [$i] ) . ',
											sort =	' . $this->dao->quote ( $answers [$i] ['sort'] );
					if (false == $this->dao->execute ( $strA ))
						return array (
								'errorMsg' => '答案信息保存失败' 
						);
				}
			}
			
			// 答案
			$answers = $model ['answers'];
			if (! empty ( $answers )) {
				for($i = 0; $i < count ( $answers ); $i ++) {
					
					$strAnswers = 'UPDATE ' . $this->vip_question_answer . '
							SET content=' . $this->dao->quote ( $answers [$i] ['content'] ) . ',
								sort=' . $this->dao->quote ( $answers [$i] ['sort'] ) . '
							WHERE id=' . $this->dao->quote ( $model ['id'] );
					if (false == $this->dao->execute ( $strAnswers ))
						return array (
								'errorMsg' => '答案保存失败' 
						);
				}
			}
			return true;
		} else
			return false;
	}
	public function editSimpleQuestion($data) {
		return $this->dao->execute ( 'UPDATE ' . $this->vip_question . '
	                 SET difficulty=' . $this->dao->quote ( $data ['difficulty'] ) . ',
                         knowledge_id = ' . $this->dao->quote ( $data ['knowledge_id'] ) . ',
                         sub_knowledge_id = ' . $this->dao->quote ( $data ['sub_knowledge_id'] ) . ',
                         is_content_error = ' . $this->dao->quote ( $data ['is_content_error'] ) . ',
						 is_classic = ' . $this->dao->quote ( $data ['is_classic'] ) . ',
						 is_edit = 1,
						 in_used = 0,
						 lock_row_time = NULL,
						 last_updated_user_name = ' . $this->dao->quote ( $data ['user_name'] ) . ',
                         last_updated_time = ' . $this->dao->quote ( time () ) . ',
						 knode_last_updated_time = ' . $this->dao->quote ( time () ) . '
	                 WHERE id=' . $this->dao->quote ( $data ['id'] ) );
	}
	public function skipQuestion($questionId) {
		return $this->dao->execute ( 'UPDATE ' . $this->vip_question . '
									  SET in_used = 0,
										  lock_row_time = NULL,
										  current_used_user_name = \'\'
                 					  WHERE id=' . $this->dao->quote ( $questionId ) );
	}
	public function skipQuestion1($questionId) {
		return $this->dao->execute ( 'UPDATE ' . $this->vip_question . '
									  SET in_used1 = 0,
										  lock_row_time1 = NULL,
										  current_used_user_name1 = \'\'
                 					  WHERE id=' . $this->dao->quote ( $questionId ) );
	}
	public function skipQuestion2($questionId) {
		return $this->dao->execute ( 'UPDATE ' . $this->vip_question . '
									  SET in_used2 = 0,
										  lock_row_time2 = NULL,
										  current_used_user_name2 = \'\'
                 					  WHERE id=' . $this->dao->quote ( $questionId ) );
	}
	public function getRootKnowledgeId($knowledgeId) {
		$knowledgeInfo = $this->dao->getRow ( 'SELECT id,parent_id,name FROM ' . $this->vip_knowledge . ' WHERE id = ' . $this->dao->quote ( $knowledgeId ) );
		if ($knowledgeInfo ['parent_id'] == 0) {
			// return $knowledgeInfo ['id'];
			return $knowledgeInfo;
		} else {
			return $this->getRootKnowledgeId ( $knowledgeInfo ['parent_id'] );
		}
	}
	public function getKnowledgesByParentId($parentId, $courseTypeId) {
		$where = '';
		if (empty ( $parentId )) {
			$where = ' AND a.parent_id = 0';
			if (! empty ( $courseTypeId )) {
				$where .= ' AND a.course_type_id = ' . $this->dao->quote ( $courseTypeId );
			} else {
				return array ();
			}
		} else {
			$where = ' AND a.parent_id = ' . $this->dao->quote ( $parentId );
		}
		
		return $this->dao->getAll ( 'SELECT a.`id`,
											a.`name`,
											a.`remark`,
											a.`sort`,
											a.`parent_id`,
											a.`analysis`,
											a.`status`,
											a.`state`,
											a.`is_leaf`,
											a.`is_gaosi`,
											a.`origin_knowledge_id`,
											a.`level` 
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 ' . $where . ' ORDER BY a.sort, a.id' );
	}
	public function getKnowledgesByParentId1($parentId, $courseTypeId) {
		$where = '';
		if (empty ( $parentId )) {
			$where = ' AND a.parent_id = 0';
			if (! empty ( $courseTypeId )) {
				$where .= ' AND a.course_type_id = ' . $this->dao->quote ( $courseTypeId );
			} else {
				return array ();
			}
		} else {
			$where = ' AND a.parent_id = ' . $this->dao->quote ( $parentId );
		}
	
		
		return $this->dao->getAll ( 'SELECT a.`id`,
											a.`name`,
											a.`remark`,
											a.`sort`,
											a.`parent_id`,
											a.`analysis`,
											a.`status`,
											a.`state`,
											a.is_leaf,
											a.level,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.id and b.status = 1 and department = \'CLASS\' and b.is_classic = 1) end as knode_classic_question_num,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.id and b.status = 1 and department = \'CLASS\') end as knode_question_num
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 ' . $where . ' ORDER BY a.sort, a.id' );
	}
	public function getKnowledgesByParentId2($parentId, $courseTypeId, $knowledgeTypeId=1) {
		$where = '';
		if (empty ( $parentId )) {
			if (! empty ( $courseTypeId )) {
				// 第一级
				$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND a.parent_id = 0 AND a.course_type_id = ' . $this->dao->quote ( $courseTypeId ) );
				$knowledgeIds = arr2nav ( $rows, ',', 'id' );
				
				// 第二级
				if(!empty($knowledgeIds)){
					$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_knowledge . ' a
									WHERE a.status = 1 AND a.parent_id IN (' . $knowledgeIds . ') ' );
					$knowledgeIds = arr2nav ( $rows, ',', 'id' );
					// 第三级
					if(!empty($knowledgeIds)){
						$rows = $this->dao->getAll ( 'SELECT a.`id`
										FROM ' . $this->vip_view_knowledge . ' a
										WHERE a.status = 1 AND a.parent_id IN (' . $knowledgeIds . ') ' );
						$knowledgeIds = arr2nav ( $rows, ',', 'id' );
						//第四级
						if(!empty($knowledgeIds)){
							$where .= ' AND a.parent_id IN (' . $knowledgeIds . ') ';
						}else{
							return array ();
						}

					}else{
						return array ();
					}
					
				}else{
					return array ();
				}
				
			} else {
				return array ();
			}
		} else {
			$where = ' AND a.parent_id = ' . $this->dao->quote ( $parentId );
		}
		
		return $this->dao->getAll ( 'SELECT a.`id`,
											a.`name`,
											a.`remark`,
											a.`sort`,
											a.`parent_id`,
											a.`analysis`,
											a.`status`,
											a.`state`,
											a.is_leaf,
											a.level,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.id and b.status = 1 and department = \'CLASS\' and b.is_classic = 1) end as knode_classic_question_num,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.id and b.status = 1 and department = \'CLASS\') end as knode_question_num
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 ' . $where . ' ORDER BY a.sort, a.id' );
	}
	public function getKnowledgesByWhere($parentId, $courseTypeId, $kw) {
		$where = '';
		if (empty ( $parentId )) {
			// 第一级
			$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND a.course_type_id = ' . $this->dao->quote ( $courseTypeId ) );
			$knowledgeIds = arr2nav ( $rows, ',', 'id' );
			
			// 第二级
			$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_knowledge . ' a
									WHERE a.status = 1 AND a.parent_id IN (' . $knowledgeIds . ')' );
			$knowledgeIds = arr2nav ( $rows, ',', 'id' );
			// 第三级
			$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND a.parent_id IN (' . $knowledgeIds . ')' );
			$knowledgeIds = arr2nav ( $rows, ',', 'id' );
			// 第四级
			$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND a.parent_id IN (' . $knowledgeIds . ')' );
			$knowledgeIds = arr2nav ( $rows, ',', 'id' );
			
			// 第五级
			$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND a.parent_id IN (' . $knowledgeIds . ')' );
			$knowledgeIds1 = arr2nav ( $rows, ',', 'id' );
			
			// $row = $this->dao->getRow ( 'SELECT fn_vip_get_knowledge_child_list(\'' . $rootIds . '\') AS ids' );
			if ($knowledgeIds) {
				// $ids = $row ['ids'];
				// if ($ids != '$,') {
				$knowledgeIds = str_replace ( ',', "','", $knowledgeIds );
				return $this->dao->getAll ( 'SELECT a.`id`,
													a.`name` as text,
													a.`remark`,
													a.`sort`,
													a.`parent_id`,
													a.`analysis`,
													a.`status`,
													a.`state`,
													a.is_leaf
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND ((a.id IN(\'' . $knowledgeIds . '\') AND a.name LIKE \'%' . $kw . '%\') OR a.id IN (SELECT parent_id FROM ' . $this->vip_view_knowledge . ' WHERE a.id IN(\'' . $knowledgeIds . '\') AND name LIKE \'%' . $kw . '%\')) ORDER BY a.sort, a.id' );
				// }
				// return array ();
			}
			return array ();
		} else {
			return $this->dao->getAll ( 'SELECT a.`id`,
											a.`name` as text,
											a.`remark`,
											a.`sort`,
											a.`parent_id`,
											a.`analysis`,
											a.`status`,
											a.`state`,
											a.is_leaf,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.id and b.status = 1 and department = \'CLASS\' and b.is_classic = 1) end as knode_classic_question_num,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.id and b.status = 1 and department = \'CLASS\') end as knode_question_num
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND a.parent_id = ' . $parentId . ' ORDER BY a.sort, a.id' );
		}
	}
	public function getKnowledgesByParentIdChild($parentId, $courseTypeId) {
		$where = '';
		if (empty ( $parentId )) {
			$where = ' AND a.parent_id = 0';
			if (! empty ( $courseTypeId )) {
				$where .= ' AND a.course_type_id = ' . $this->dao->quote ( $courseTypeId );
			} else {
				return array ();
			}
		} else {
			$where = ' AND a.parent_id = ' . $this->dao->quote ( $parentId );
		}
		
		return $this->dao->getAll ( 'SELECT a.`id`,
											a.`name` as text,
											a.`status`,
											a.`state`,
											a.`is_leaf`
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 ' . $where . ' ORDER BY a.sort, a.id' );
	}
	public function getKnowledgesByParentIdChild1($parentId, $courseTypeId) {
		$where = '';
		if (empty ( $parentId )) {
			$where = ' AND a.parent_id = 0';
			if (! empty ( $courseTypeId )) {
				$where .= ' AND a.course_type_id = ' . $this->dao->quote ( $courseTypeId );
			} else {
				return array ();
			}
		} else {
			$where = ' AND a.parent_id = ' . $this->dao->quote ( $parentId );
		}
		
		return $this->dao->getAll ( 'SELECT a.`id`,
											a.`name` as text,
											a.`status`,
											a.`state`,
											a.`is_leaf`,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.id and b.status = 1 and department = \'CLASS\' and b.is_classic = 1) end as knode_classic_question_num,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.id and b.status = 1 and department = \'CLASS\') end as knode_question_num
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 ' . $where . ' ORDER BY a.sort, a.id' );
	}
	public function getPapersDetailByWhere($where = '', $currentPage, $pageSize, $sort, $order) {
		$currentPage = empty ( $currentPage ) ? 1 : $currentPage;
		$pageSize = empty ( $pageSize ) ? 20 : $pageSize;
		
		$sort = empty ( $sort ) ? 'a.created_time' : $sort;
		$order = empty ( $order ) ? 'DESC' : $order;
		
		$list = $this->dao->getAll ( 'SELECT a.`id`,
											 a.`file_name`,
											 FROM_UNIXTIME(a.`created_time`) AS `created_time`,
											 b.title AS grade_name,
											 c.title AS subject_name,
											 d.question_count
									 FROM ' . $this->vip_paper . ' a
									 LEFT JOIN ' . $this->vip_dict_grade . ' b ON a.grade_id = b.id
									 LEFT JOIN ' . $this->vip_dict_subject . ' c ON a.subject_id = c.id
									 LEFT JOIN (select COUNT(*) question_count, paper_id from vip_question where status = 1 group by paper_id ) d ON a.id = d.paper_id
									 WHERE a.status = 1 ' . $where . '
									 ORDER BY ' . $sort . ' ' . $order . '
									 limit ' . ($currentPage - 1) * $pageSize . ', ' . $pageSize );
		
		$total = 0;
		if ($list) {
			$row = $this->dao->getRow ( 'SELECT COUNT(*) AS cnt
									 FROM ' . $this->vip_paper . ' a
									 WHERE a.status = 1 ' . $where );
			if ($row) {
				$total = $row ['cnt'];
			}
		}
		return array (
				'total' => $total,
				'rows' => $list 
		);
	}
	public function getPaperInfoById($id) {
		return $this->dao->getRow( 'SELECT * FROM ' . $this->vip_paper . ' WHERE id = ' . $this->dao->quote ( abs ( $id ) ) );
	}
	public function setPaperInfoByData($data) {
		return $this->dao->execute ( 'UPDATE ' . $this->vip_paper . ' 
									  SET file_name = ' . $this->dao->quote ( $data ['file_name'] ) . ' 
									  WHERE id = ' . $this->dao->quote ( $data ['id'] ) );
	}
	public function setQuestionClassic($questionId) {
		return $this->dao->execute ( 'UPDATE ' . $this->vip_question . '
									  SET is_classic = (is_classic + 1) % 2
                 					  WHERE id=' . $this->dao->quote ( $questionId ) );
	}
	public function updateQuestion($questionId, $questionTypeId, $difficulty, $grades, $knowledgeId, $subKnowledgeId) {
		return $this->dao->execute ( 'UPDATE ' . $this->vip_question . ' SET question_type_id = ' . $this->dao->quote ( $questionTypeId ) . ', difficulty = ' . $this->dao->quote ( $difficulty ) . ', grades = ' . $this->dao->quote ( $grades ) . ', knowledge_id = ' . $this->dao->quote ( $knowledgeId ) . ', sub_knowledge_id = ' . $this->dao->quote ( $subKnowledgeId ) . ' WHERE id = ' . $this->dao->quote ( $questionId ) );
	}
	
	
	
	
	
	/*edit by xcp ===============================================================================================================*/
	public function getKnowledgeTypes($params){
		if($params['is_gaosi'] == 1){
			$where = ' AND is_gaosi = '.$this->dao->quote($params['is_gaosi']);
		}
		return $this->dao->getAll ( 'SELECT id, title, sort FROM '.$this->vip_dict_knowledge_type.' WHERE status = 1 AND subject_id =  ('.$params['subjectid'].') '.$where.' ORDER BY sort ASC' );
	}
	
	public function getCourseTypesBySubjectIdAndKnowledgeTypeId($subjectId,$knowledgeTypeId) {
		return $this->dao->getAll ( 'SELECT `id`,
										    `title`,
										    `status`,
										    `sort`
									FROM ' . $this->vip_dict_course_type . '
								 	WHERE status = 1 AND subject_id = ' . $this->dao->quote ( $subjectId ) . ' AND knowledge_type_id = ' . $this->dao->quote ( $knowledgeTypeId ) . ' ORDER BY sort' );
	}
	
	public function getSubjectIdByCourseTypeId($courseTypeId){
		return $this->dao->getOne('SELECT subject_id FROM '.$this->vip_dict_course_type.' WHERE id = '.$this->dao->quote($courseTypeId));
	}
	
	public function matchKnowledge($knowledge = array()) {
		$parentId = $knowledge ['parent_id'];
		$flag = true;
		$this->dao->execute ( 'begin' ); // 事务开启
		if (! empty ( $parentId )) {
			$sql = 'UPDATE ' . $this->vip_knowledge . ' SET is_leaf = 0 WHERE id = ' . $this->dao->quote ( $parentId ) . ' AND is_leaf = 1';
			if ($this->dao->execute ( $sql ))
			$flag = true;
			else
			$flag = false;
		}
			
		if ($flag == true) {
			$sql2 = 'INSERT INTO ' . $this->vip_knowledge . ' (name, remark, parent_id, analysis, sort, is_leaf, level, is_gaosi, origin_knowledge_id) VALUES (' . $this->dao->quote ( $knowledge ['name'] ) . ', ' . $this->dao->quote ( $knowledge ['remark'] ) . ', ' . $this->dao->quote ( $parentId ) . ', ' . $this->dao->quote ( $knowledge ['analysis'] ) . ', ' . $this->dao->quote ( $knowledge ['sort'] ) . ', 1, ' . $this->dao->quote ( $knowledge ['level'] ) . ', ' . $this->dao->quote ( $knowledge ['is_gaosi'] ) . ', ' . $this->dao->quote ( $knowledge ['origin_knowledge_id'] ) . ')';
			if ($this->dao->execute ( $sql2 )) {
				$id = $this->dao->lastInsertId ();
				$flag = true;
			} else
			$flag = false;
		}

		// 如为父节点则插入知识点属性表
		/*if (empty ( $parentId ) && $flag == true) {
			$sql3 = 'INSERT INTO ' . $this->vip_knowledge_course_type_rs . ' (knowledge_id, course_type_id) VALUES (' . $this->dao->quote ( $id ) . ', ' . $this->dao->quote ( $knowledge ['coursetypeid'] ) . ')';

			if ($this->dao->execute ( $sql3 ))
			$flag = true;
			else
			$flag = false;
		}*/

		if ($flag === false)
		$this->dao->execute ( 'rollback' ); // 事务回滚
		else
		$this->dao->execute ( 'commit' ); // 事务提交

		return $flag;
	}
	
	
	public function getKnowledgeTypeByTitle( $title ){
		return $this->dao->getRow('SELECT id,title FROM '.$this->vip_dict_knowledge_type.' WHERE status = 1 AND title = '. $this->dao->quote ( $title ) );
	}
	
	
	public function checkKnowledgeIsExist($knowledge = array()){
		$count = $this->dao->getOne('SELECT COUNT(1) FROM '.$this->vip_knowledge.' WHERE name = '. $this->dao->quote ( $knowledge ['name'] ) .' AND parent_id = '. $this->dao->quote ( $knowledge ['parent_id'] ) .' AND is_gaosi = '.$this->dao->quote ( $knowledge ['is_gaosi'] ));
		if($count == 0)
			return false;
		else 
			return true;
	}
	
	
	public function getKnowledgesByWhere3($parentId, $courseTypeId, $kw) {
		$where = '';
		if (empty ( $parentId )) {
			// 第一级
			$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND a.course_type_id = ' . $this->dao->quote ( $courseTypeId ) );
			$knowledgeIds = arr2nav ( $rows, ',', 'id' );
			
			// 第二级
			$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_knowledge . ' a
									WHERE a.status = 1 AND a.parent_id IN (' . $knowledgeIds . ')' );
			$knowledgeIds = arr2nav ( $rows, ',', 'id' );
			// 第三级
			$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND a.parent_id IN (' . $knowledgeIds . ')' );
			$knowledgeIds = arr2nav ( $rows, ',', 'id' );
			// 第四级
			$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND a.parent_id IN (' . $knowledgeIds . ')' );
			$knowledgeIds = arr2nav ( $rows, ',', 'id' );
			
			// 第五级
			$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND a.parent_id IN (' . $knowledgeIds . ')' );
			$knowledgeIds1 = arr2nav ( $rows, ',', 'id' );
			
			// $row = $this->dao->getRow ( 'SELECT fn_vip_get_knowledge_child_list(\'' . $rootIds . '\') AS ids' );
			if ($knowledgeIds) {
				// $ids = $row ['ids'];
				// if ($ids != '$,') {
				$knowledgeIds = str_replace ( ',', "','", $knowledgeIds );
				return $this->dao->getAll ( 'SELECT a.`id`,
													a.`name`,
													a.`remark`,
													a.`sort`,
													a.`parent_id`,
													a.`analysis`,
													a.`status`,
													a.`state`,
													a.is_leaf
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND ((a.id IN(\'' . $knowledgeIds . '\') AND a.name LIKE \'%' . $kw . '%\') OR a.id IN (SELECT parent_id FROM ' . $this->vip_view_knowledge . ' WHERE a.id IN(\'' . $knowledgeIds . '\') AND name LIKE \'%' . $kw . '%\')) ORDER BY a.sort, a.id' );
				// }
				// return array ();
			}
			return array ();
		} else {
			return $this->dao->getAll ( 'SELECT a.`id`,
											a.`name`,
											a.`remark`,
											a.`sort`,
											a.`parent_id`,
											a.`analysis`,
											a.`status`,
											a.`state`,
											a.is_leaf,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.id and b.status = 1 and department = \'CLASS\' and b.is_classic = 1) end as knode_classic_question_num,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.id and b.status = 1 and department = \'CLASS\') end as knode_question_num
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND a.parent_id = ' . $parentId . ' ORDER BY a.sort, a.id' );
		}
	}
	
	public function getKnowledgeTypeIsGaosiById($knowledgeTypeId){
		return $this->dao->getOne('SELECT is_gaosi FROM '.$this->vip_dict_knowledge_type.' WHERE id = ' .$this->dao->quote($knowledgeTypeId));
	}
	
	
	public function getCourseTypesBySubjectId2($subjectId, $is_gaosi = 1) {
		$knowledgeTypeIdStr = '';
		$knowledgeTypeArr = $this->getKnowledgeTypes(array('subjectid'=>$subjectId,'is_gaosi'=>$is_gaosi));
		if(!empty($knowledgeTypeArr)){
			foreach ($knowledgeTypeArr as $key=>$knowledgeType){
				$knowledgeTypeIdStr .= $knowledgeType['id'].',';
			}
		}
		
		return $this->dao->getAll ( 'SELECT `id`,
										    `title`,
										    `status`,
										    `sort`
									FROM ' . $this->vip_dict_course_type . '
								 	WHERE status = 1 AND subject_id = ' . $this->dao->quote ( $subjectId ) . ' AND knowledge_type_id IN ('.$this->dao->quote($knowledgeTypeIdStr).') ORDER BY sort' );
	}
	
	
}

?>
