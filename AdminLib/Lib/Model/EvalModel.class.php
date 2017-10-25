<?php

class EvalModel extends Model {
	public  $dao = null;
	public function __construct(){
		$this->dao = Dao::getDao();
		$this->vip_eval_users = 'vip_eval_users';
		$this->vip_eval_module = 'vip_eval_module';
		$this->vip_eval_paper = 'vip_eval_paper';
		$this->vip_eval_question = 'vip_eval_question';
		$this->vip_eval_result = 'vip_eval_result';
		$this->vip_eval_record = 'vip_eval_record';
		$this->vip_book_users = 'vip_book_users';
		$this->vip_eval_level = 'vip_eval_level';
		
	}


	public function get_userList($condition='', $currentPage=1, $pageSize=20){
		$count = $this->get_userCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT * FROM '.$this->vip_eval_users.' WHERE 1 = 1 ';
		if(!empty($condition)){
			$strQuery .= ' AND '.$condition;
		}
		$order = ' ORDER BY instime DESC';
		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}


	public function get_userCount($condition=''){
		$strQuery = 'SELECT count(1) FROM '.$this->vip_eval_users.' WHERE 1=1 ';
		if ($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		return $this->dao->getOne($strQuery);
	}

	//--start 预约用户--

	public function get_bookuserList($condition='', $currentPage=1, $pageSize=20){
		$count = $this->get_bookuserCount($condition);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT * FROM '.$this->vip_book_users.' WHERE 1 = 1 ';
		if(!empty($condition)){
			$strQuery .= ' AND '.$condition;
		}
		$order = ' ORDER BY instime DESC';
		return $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
	}


	public function get_bookuserCount($condition=''){
		$strQuery = 'SELECT count(1) FROM '.$this->vip_book_users.' WHERE 1=1 ';
		if ($condition) {
			$strQuery .= ' AND ' . $condition;
		}
		return $this->dao->getOne($strQuery);
	}

	//--end 预约用户--


	public function get_userListAll($conditionArr){
		$strQuery = 'SELECT id,name,phone,grade,school,instime FROM '.$this->vip_eval_users.' WHERE 1=1 ';
		if ($conditionArr['keyword']) {
			$strQuery .= ' AND `name` like ' . $this->dao->quote('%'.SysUtil::safeString(urldecode($conditionArr['keyword'])).'%');
		}
		return $this->dao->getAll($strQuery);
	}

	public function get_bookuserListAll($conditionArr){
		$strQuery = 'SELECT id,name,phone,grade,email,instime FROM '.$this->vip_book_users.' WHERE 1=1 ';
		if ($conditionArr['name']) {
			$strQuery .= ' AND `name` like ' . $this->dao->quote('%'.SysUtil::safeString(urldecode($conditionArr['name'])).'%');
		}
		if ($conditionArr['mobile']) {
			$strQuery .= ' AND `phone` like ' . $this->dao->quote('%'.SysUtil::safeString(urldecode($conditionArr['mobile'])).'%');
		}

		return $this->dao->getAll($strQuery);
	}

	public function get_onepaper($paper_id){
		$strQuery = 'SELECT * FROM '.$this->vip_eval_paper.' WHERE 1=1 ';
		if(!empty($paper_id)){
			$strQuery .= ' AND id = '.$this->dao->quote($paper_id);
		}
		return $this->dao->getRow($strQuery);
	}

	public function get_paperList($arr){
		$strQuery = 'SELECT * FROM '.$this->vip_eval_paper.' WHERE 1=1 ';
		if(!empty($arr['status'])){
			$strQuery .= ' AND status = '.$this->dao->quote($arr['status']);
		}
		return $this->dao->getAll($strQuery);
	}

	public function get_paperInfo($paper_id){
		$info = array();
		if(!empty($paper_id)){
			//echo 'SELECT * FROM '.$this->vip_eval_paper.' WHERE id = '.$this->dao->quote($paper_id);
			$info = $this->dao->getRow('SELECT * FROM '.$this->vip_eval_paper.' WHERE id = '.$this->dao->quote($paper_id));
		}
		return $info;
	}

	public function editPaper($arr){
		//echo 'UPDATE '.$this->vip_eval_paper.' SET title='.$this->dao->quote(SysUtil::safeString($arr['title'])).',question_num='.$this->dao->quote(abs($arr['question_num'])).',answer_time='.$this->dao->quote(abs($arr['answer_time'])).',full_mark='.$this->dao->quote(abs($arr['full_mark'])).',grade='.$this->dao->quote(SysUtil::safeString($arr['grade'])).',level_str='.$this->dao->quote($arr['level_str']).',document='.$this->dao->quote($arr['document']).',contain_module='.$this->dao->quote(trim($arr['contain_module'])).',is_download='.$this->dao->quote(abs($arr['is_download'])).',status = '.$this->dao->quote(abs($arr['status'])).' WHERE id= '.$this->dao->quote(abs($arr['id']));exit;
		$this->dao->execute('UPDATE '.$this->vip_eval_paper.' SET title='.$this->dao->quote(SysUtil::safeString($arr['title'])).',question_num='.$this->dao->quote(abs($arr['question_num'])).',answer_time='.$this->dao->quote(abs($arr['answer_time'])).',full_mark='.$this->dao->quote(abs($arr['full_mark'])).',grade='.$this->dao->quote(SysUtil::safeString($arr['grade'])).',level_str='.$this->dao->quote($arr['level_str']).',document='.$this->dao->quote($arr['document']).',contain_module='.$this->dao->quote(trim($arr['contain_module'])).',is_download='.$this->dao->quote(abs($arr['is_download'])).',status = '.$this->dao->quote(abs($arr['status'])).' WHERE id= '.$this->dao->quote(abs($arr['id'])));
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}

	public function AddWholeLevel($arr){
		$this->dao->execute('INSERT INTO '.$this->vip_eval_level.' SET whole_level='.$this->dao->quote(SysUtil::safeString($arr['whole_level'])));
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}

	public function EditWholeLevel($arr){
		$this->dao->execute('UPDATE '.$this->vip_eval_level.' SET whole_level='.$this->dao->quote(trim($arr['whole_level'])).' WHERE id= '.$this->dao->quote(($arr['id'])));
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}

	public function addPaper($arr){
		//echo 'INSERT INTO '.$this->vip_eval_paper.' (title,question_num,answer_time,full_mark,grade,contain_module,is_download,status,document,level_str) VALUES('.$this->dao->quote(SysUtil::safeString($arr['title'])).','.$this->dao->quote(abs($arr['question_num'])).','.$this->dao->quote(abs($arr['answer_time'])).','.$this->dao->quote(abs($arr['full_mark'])).','.$this->dao->quote(SysUtil::safeString($arr['grade'])).','.$this->dao->quote(trim($arr['contain_module'])).','.$this->dao->quote(abs($arr['is_download'])).','.$this->dao->quote(abs($arr['status'])).','.$this->dao->quote(trim($arr['document'])).','.$this->dao->quote($arr['level_str']).')';exit;
		$this->dao->execute('INSERT INTO '.$this->vip_eval_paper.' (title,question_num,answer_time,full_mark,grade,contain_module,is_download,status,document,level_str) VALUES('.$this->dao->quote(SysUtil::safeString($arr['title'])).','.$this->dao->quote(abs($arr['question_num'])).','.$this->dao->quote(abs($arr['answer_time'])).','.$this->dao->quote(abs($arr['full_mark'])).','.$this->dao->quote(SysUtil::safeString($arr['grade'])).','.$this->dao->quote(trim($arr['contain_module'])).','.$this->dao->quote(abs($arr['is_download'])).','.$this->dao->quote(abs($arr['status'])).','.$this->dao->quote(trim($arr['document'])).','.$this->dao->quote($arr['level_str']).')');
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}


	public function get_moduleList($arr){
		$strQuery = 'SELECT * FROM '.$this->vip_eval_module.' WHERE 1=1 ';
		if(!empty($arr['module_id'])){
			$strQuery .= ' AND id = '.$this->dao->quote($arr['module_id']);
		}
		return $this->dao->getAll($strQuery);
	}

	public function get_papermoduleList($arr){
		$list = array();
		$strQuery = 'SELECT q.module_id as module_id,m.name as  module_name FROM '.$this->vip_eval_question.' q
					LEFT JOIN '.$this->vip_eval_module.' m ON q.module_id = m.id
					WHERE 1=1 ';
		if(!empty($arr['paper_id'])){
			$strQuery .= ' AND q.paper_id = '.$this->dao->quote($arr['paper_id']);
		}
			$strQuery .='GROUP BY q.module_id ASC';
		return  $this->dao->getAll($strQuery);	
	}



	public function get_moduleInfo($module_id){
		$info = array();
		$strQuery = 'SELECT * FROM '.$this->vip_eval_module.' WHERE 1=1 ';
		if(!empty($module_id))
			$strQuery .= ' AND id = '.$this->dao->quote($module_id);
		$info = $this->dao->getRow($strQuery);
		return $info;
	}


	public function editModule($arr){
		$this->dao->execute('UPDATE '.$this->vip_eval_module.' SET `name`='.$this->dao->quote(SysUtil::safeString($arr['name'])).',excellent_strong='.$this->dao->quote(abs($arr['excellent_strong'])).',excellent_weak='.$this->dao->quote(abs($arr['excellent_weak'])).',key_num='.$this->dao->quote(abs($arr['key_num'])).' WHERE id= '.$this->dao->quote(abs($arr['id'])));
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}


	public function addModule($arr){
		$this->dao->execute('INSERT INTO '.$this->vip_eval_module.' (`name`,excellent_strong,excellent_weak,key_num) VALUES('.$this->dao->quote(SysUtil::safeString($arr['name'])).','.$this->dao->quote(abs($arr['excellent_strong'])).','.$this->dao->quote(abs($arr['excellent_weak'])).','.$this->dao->quote(abs($arr['key_num'])).')');
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}



	public function get_questionList($arr, $currentPage=1, $pageSize=20){
		$count = $this->get_questionCount($arr);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT q.*,p.title as paper_title,m.name as module_name FROM '.$this->vip_eval_question.' AS q LEFT JOIN '.$this->vip_eval_paper.' AS p ON p.id=q.paper_id LEFT JOIN '.$this->vip_eval_module.' AS m ON m.id = q.module_id WHERE 1 = 1 ';
		if(!empty($arr['paper_id'])){
			$strQuery .= ' AND q.paper_id = '.$this->dao->quote($arr['paper_id']);
		}
		if(!empty($arr['module_id'])){
			$strQuery .= ' AND q.module_id = '.$this->dao->quote($arr['module_id']);
		}
		$order = ' ORDER BY paper_id DESC,seq DESC';
		$list = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		if(!empty($list)){
			foreach ($list as $key=>$row){
				$list[$key]['show_url'] = str_replace('/Upload/','/upload/',$row['img']);
				$list[$key]['show_analysis'] = str_replace('/Upload/','/upload/',$row['analysis']);
				list($list[$key]['img_width'],$list[$key]['img_height']) = getimagesize(APP_DIR.$row['img']);
				list($list[$key]['analysis_width'],$list[$key]['analysis_height']) = getimagesize(APP_DIR.$row['analysis']);
				//$answerArr = C('ANSWER');$answerArr[$row['answer']];
				$list[$key]['answer'] =$row['answer'];
			}
		}
		return $list;
	}


	public function get_questionCount($arr){
		$strQuery = 'SELECT count(1) FROM '.$this->vip_eval_question.' WHERE 1 = 1';
		if(!empty($arr['paper_id'])){
			$strQuery .= ' AND paper_id = '.$this->dao->quote($arr['paper_id']);
		}
		if(!empty($arr['module_id'])){
			$strQuery .= ' AND module_id = '.$this->dao->quote($arr['module_id']);
		}
		return $this->dao->getOne($strQuery);
	}

	/**
	*获取整体层极
	**/
	public function get_LevelInfo(){
		$strQuery = 'SELECT * FROM '.$this->vip_eval_level.' WHERE 1 = 1';
		return $this->dao->getRow($strQuery);
	}
	public function get_questionInfo($question_id){
		$row = array();
		if(!empty($question_id)){
			$row = $this->dao->getRow('SELECT * FROM '.$this->vip_eval_question.' WHERE id = '.$this->dao->quote($question_id));
			$row['show_url'] = str_replace('/Upload/','/upload/',$row['img']);
			$row['show_analysis'] = str_replace('/Upload/','/upload/',$row['analysis']);
		}
		return $row;
	}


	public function addQuestion($arr){
		//echo 'INSERT INTO '.$this->vip_eval_question.' SET img = '.$this->dao->quote($arr['img']).',analysis = '.$this->dao->quote($arr['analysis']).',module_id='.$this->dao->quote(abs($arr['module_id'])).',paper_id = '.$this->dao->quote(abs($arr['paper_id'])).',option_num='.$this->dao->quote(abs($arr['option_num'])).',answer ='.$this->dao->quote(strtoupper(trim($arr['answer']))).',time_limit ='.$this->dao->quote(abs($arr['time_limit'])).',accuracy = '.$this->dao->quote(abs($arr['accuracy'])).',seq =' .$this->dao->quote(abs($arr['seq'])).',instime = '.$this->dao->quote(date('Y-m-d H:i:s')).',difficulty = ' .$this->dao->quote($arr['difficulty']).',score ='.$this->dao->quote($arr['score']);exit;
		$this->dao->execute('INSERT INTO '.$this->vip_eval_question.' SET img = '.$this->dao->quote($arr['img']).',analysis = '.$this->dao->quote($arr['analysis']).',module_id='.$this->dao->quote(abs($arr['module_id'])).',paper_id = '.$this->dao->quote(abs($arr['paper_id'])).',answer ='.$this->dao->quote(strtoupper(trim($arr['answer']))).',seq =' .$this->dao->quote(abs($arr['seq'])).',instime = '.$this->dao->quote(date('Y-m-d H:i:s')).',difficulty = ' .$this->dao->quote($arr['difficulty']).',score ='.$this->dao->quote($arr['score']));
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}


	

	public function editQuestion($arr){
		//echo 'UPDATE '.$this->vip_eval_question.' SET img = '.$this->dao->quote($arr['img']).', analysis = '.$this->dao->quote($arr['analysis']).',module_id = '.$this->dao->quote(abs($arr['module_id'])).',difficulty = '.$this->dao->quote(abs($arr['difficulty'])).',option_num = '.$this->dao->quote(abs($arr['option_num'])).',answer = '.$this->dao->quote(strtoupper($arr['answer'])).',score = '.$this->dao->quote(abs($arr['score'])).',accuracy = '.$this->dao->quote(abs($arr['accuracy'])).',seq = '.$this->dao->quote(abs($arr['seq'])).',instime = '.$this->dao->quote(date('Y-m-d H:i:s')).' WHERE id = '.$this->dao->quote(abs($arr['id']));exit;
		$this->dao->execute('UPDATE '.$this->vip_eval_question.' SET img = '.$this->dao->quote($arr['img']).', analysis = '.$this->dao->quote($arr['analysis']).',module_id = '.$this->dao->quote(abs($arr['module_id'])).',difficulty = '.$this->dao->quote(abs($arr['difficulty'])).',answer = '.$this->dao->quote(strtoupper($arr['answer'])).',score = '.$this->dao->quote(abs($arr['score'])).',seq = '.$this->dao->quote(abs($arr['seq'])).',instime = '.$this->dao->quote(date('Y-m-d H:i:s')).' WHERE id = '.$this->dao->quote(abs($arr['id'])));
		if($this->dao->affectRows()){
			return true;
		}
		return false;
	}

	public function updatequestion($question_id,$is_key,$module_id){

		if(!empty($module_id) && $is_key == 1){
			$q = $this->dao->getOne('SELECT key_num FROM '.$this->vip_eval_module.' WHERE  id= '.$module_id);
			$key_num = $q['key_num'];
			$strQuery = 'SELECT count(1) FROM '.$this->vip_eval_question.' WHERE module_id = '.$module_id.' and is_key=1';
			$module_num = $this->dao->getOne($strQuery);
			if($module_num >= $key_num)//如果设置的关键题数量 > 限定的数量则返回假
				return false;
		}
		
		if(!empty($question_id) && !empty($module_id)){
			$this->dao->execute('UPDATE '.$this->vip_eval_question.' SET is_key = '.$this->dao->quote($is_key).' WHERE id = '.$this->dao->quote($question_id));
			if($this->dao->affectRows()){
				return true;
			}
			return false;
		}else
			return false;
	}	


	/*public function get_resultList($conditionArr, $currentPage=1, $pageSize=20){
		$count = $this->get_usernameCount($conditionArr);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$strQuery = 'SELECT r.*,u.name as uname,u.phone as mobile,p.title,p.question_num,
							p.contain_module,p.question_num,p.title as paper
					 FROM '.$this->vip_eval_result.' AS r
					LEFT JOIN '.$this->vip_eval_users.' AS u ON r.uid = u.id 
					LEFT JOIN '.$this->vip_eval_paper.' AS p ON r.paper_id = p.id 
					WHERE 1 = 1 ';
		if (!empty($conditionArr['name'])) {
			$strQuery .= ' AND u.name = ' .$this->dao->quote($conditionArr['name']);
		}

		if (!empty($conditionArr['mobile'])) {
			$strQuery .= ' AND u.phone = ' .$this->dao->quote($conditionArr['mobile']);
		}		
		$order = ' ORDER BY r.accuracy_count DESC';
		$list = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		$list = $this->get_resultLevelRank($list);
		return $list;
	}*/

	/**
	*获取用户成绩列表
	**/
	public function get_resultList($conditionArr, $currentPage=1, $pageSize=20){
		$result = array();
		$r = array();
		$list = array();
		$count = $this->get_usernameCount($conditionArr);
		$pageCount = ceil($count / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		
		$strQuery = 'SELECT  *  FROM  '.$this->vip_eval_users.' AS u WHERE 1 = 1 ';

		if (!empty($conditionArr['name'])) {
			$strQuery .= ' AND u.name = ' .$this->dao->quote($conditionArr['name']);
		}

		if (!empty($conditionArr['mobile'])) {
			$strQuery .= ' AND u.phone = ' .$this->dao->quote($conditionArr['mobile']);
		}	
	
		$order = ' ORDER BY u.id DESC';
		$list['uname']= $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		if(!empty($list['uname'])){
			 foreach ($list['uname'] as $k => $v) {
				$list[$k]= $this->getModulesFullByTotalPaperIdAndUID($v['id']);
			 }	 
		}
		return $list;
	}

	public function getModulesFullByTotalPaperIdAndUID($uid) {
		$sql = "SELECT A.id,
					   A.name,
					   ifnull(B.module_question_count, 0) AS module_question_count,
					   ifnull(C.module_my_correct_question_count, 0) AS module_my_correct_question_count
					   
				FROM $this->vip_eval_module A
				LEFT JOIN (
					SELECT module_id, COUNT(module_id) AS module_question_count
					FROM $this->vip_eval_question D
					LEFT JOIN $this->vip_eval_record E ON D.id = E.question_id
					WHERE  E.uid = '$uid'
					GROUP BY module_id
				) B ON A.id = B.module_id
				LEFT JOIN (
					SELECT module_id, COUNT(module_id) AS module_my_correct_question_count
					FROM $this->vip_eval_question D
					LEFT JOIN $this->vip_eval_record E ON D.id = E.question_id
					WHERE E.is_correct = 1 AND E.uid = '$uid'
					GROUP BY module_id
				) C ON A.id = C.module_id";
				//WHERE a.paper_id = '$paperId'
		return $this->dao->getAll ( $sql );
	}

	/**
	* 计算总正确率所处的排名
	**/
	public function getResultBYuidRateID(){

		$sql = "SELECT a.uid,a.status,a.score,sum(accuracy_count) as correct_num,
					sum(question_num) as num,(sum(accuracy_count) / sum(question_num)) as rate
				FROM $this->vip_eval_result a
				INNER JOIN $this->vip_eval_paper p ON p.id = a.paper_id
				WHERE a.status=1
				GROUP BY uid ORDER BY rate desc,score desc,uid ASC";
				//echo $sql;
		return $this->dao->getAll ( $sql );
	}

	/**
	*获取用户对应的各模块正确率
	**/
	public function get_moduleRank($r){
			$p = array();
			$m = $this->get_moduleList();
			foreach($m as $k=>$v){
				$i = $j = 0;
				foreach($r as $kk=>$vv){
					
					if($vv['mid'] == $v['id']){
						if($vv['is_correct'] == 1)
							$j++;
						$i++;
					}
				}

				$p[$v['id']]['total'] = $i;
				$p[$v['id']]['correct']= $j;
			}
			return $p;
	}

	public function get_resultCount($conditionArr){
		$strQuery = 'SELECT count(1) FROM '.$this->vip_eval_result.' WHERE 1 = 1';
		//echo $strQuery;exit;
		if (!empty($conditionArr['paper_id'])) {
			$strQuery .= ' AND paper_id = ' .$this->dao->quote($conditionArr['paper_id']);
		}
		return $this->dao->getOne($strQuery);
	}

	public function get_usernameCount($conditionArr){
		$strQuery = 'SELECT count(1) FROM '.$this->vip_eval_users.' WHERE 1 = 1';
		if (!empty($conditionArr['name'])) {
			$strQuery .= ' AND name = ' .$this->dao->quote($conditionArr['name']);
		}

		if (!empty($conditionArr['mobile'])) {
			$strQuery .= ' AND phone = ' .$this->dao->quote($conditionArr['mobile']);
		}	

		return $this->dao->getOne($strQuery);
	}

	public function get_resultListAll($conditionArr){

		$strQuery = 'SELECT r.id,r.score,r.accuracy_count,r.paper_id,r.uid,p.question_num FROM '.$this->vip_eval_result.' r
					LEFT JOIN '.$this->vip_eval_paper.' p ON r.paper_id = p.id
					LEFT JOIN '.$this->vip_eval_users.' u ON r.uid = u.id
					WHERE 1=1 and r.status=1' ;
		if (!empty($conditionArr['paper_id'])) {
			$strQuery .= ' AND r.paper_id = ' .$this->dao->quote($conditionArr['paper_id']);
		}			
		/*	LEFT JOIN (
					SELECT D.result_id
					FROM '.$this->vip_eval_record.' D
					LEFT JOIN '.$this->vip_eval_question.' E ON D.question_id = E.id
					WHERE  D.is_correct = 1 and D.paper_id = '.$this->dao->quote($conditionArr['paper_id']).'				
				) B ON r.id = B.result_id*/
		/*$strQuery = 'SELECT r.*,u.name as uname,p.question_num,p.answer_time,p.full_mark,
					p.title as paper,p.contain_module,p.is_download,p.level_str,
 					ifnull(B.record_score, 0) AS score
					FROM '.$this->vip_eval_result.' AS r
					LEFT JOIN '.$this->vip_eval_users.' AS u ON r.uid = u.id 
					LEFT JOIN '.$this->vip_eval_paper.' AS p ON r.paper_id = p.id 
				WHERE 1 = 1 and p.status=1';
		if (!empty($conditionArr['paper_id'])) {
			$strQuery .= ' AND r.paper_id = ' .$this->dao->quote($conditionArr['paper_id']);
		}
		$strQuery .= ' ORDER BY accuracy_count DESC ';
		$list = $this->dao->getAll($strQuery);
		$list = $this->get_resultTJLevelRank($list);*/
		//print_R($list);exit;
	
		$list = $this->dao->getAll($strQuery);
		return $list;
	}


	public function get_resultInfo($result_id){
		$list = $this->dao->getAll('SELECT r.*,u.name as uname,p.title,p.level_str,p.question_num FROM '.$this->vip_eval_result.' AS r
															  LEFT JOIN '.$this->vip_eval_users.' AS u ON r.uid = u.id 
															  LEFT JOIN '.$this->vip_eval_paper.' AS p ON r.paper_id = p.id 
															  WHERE r.id = '.$this->dao->quote(abs($result_id)));
		$list = $this->get_resultLevelRank($list);
		return $list[0];
	}



	public function get_resultTJLevelRank($arr){
		if(!empty($arr)){
			foreach ($arr as $key=>$row){
				$arr[$key]['rank'] = $key+1;
				$arr[$key]['level_arr'] = unserialize($row['level_str']);
				$arr[$key]['level_count'] = count($arr[$key]['level_arr']);

				if(!empty($arr[$key]['level_arr'])){
					foreach ($arr[$key]['level_arr'] as $kk=>$level){
						if($row['score'] !='0'){
							if($row['score'] > $level['low'] && $row['score'] <= $level['up']){
								$arr[$key]['level'] = $level['name'];
								$arr[$key]['level_desc'] = $level['desc'];
								break;
							}
						}else{
							$arr[$key]['level'] = $level['name'];
							$arr[$key]['level_desc'] = $level['desc'];
						}

					}
				}
			}
		}
		return $arr;
	}


	public function get_resultLevelRank($arr){
		if(!empty($arr)){
			foreach ($arr as $key=>$row){
				$arr[$key]['rank'] = $key+1;
				$arr[$key]['level_arr'] = unserialize($row['level_str']);
				$arr[$key]['level_count'] = count($arr[$key]['level_arr']);
				if(!empty($arr[$key]['level_arr'])){
					foreach ($arr[$key]['level_arr'] as $kk=>$level){
						if($row['accuracy_count'] >= $level['low'] && $row['accuracy_count'] <= $level['up']){
							$arr[$key]['level'] = $level['name'];
							$arr[$key]['level_desc'] = $level['desc'];
							break;
						}
					}
				}
			}
		}
		return $arr;
	}


	public function get_recordList($arr){
		return $this->dao->getAll('SELECT * FROM '.$this->vip_eval_record.' WHERE 1 = 1 AND question_id = ' .$this->dao->quote($arr['question_id']));
	}


	public function get_resultAccuracyAvg($conditionArr){
		$strQuery = 'SELECT sum(accuracy_count) as correct_num FROM '.$this->vip_eval_result.' WHERE status = 1';
		if (!empty($conditionArr['paper_id'])) {
			$strQuery .= ' AND paper_id = ' .$this->dao->quote($conditionArr['paper_id']);
		}
		return $this->dao->getOne($strQuery);
	}


	public function get_moduleQuestion($paper_id){
		//echo 'select count(*) as question_num,module_id from '.$this->vip_eval_question.' where paper_id = '.$this->dao->quote($paper_id).' group by module_id';exit;
		return $this->dao->getAll('select count(*) as question_num,module_id from '.$this->vip_eval_question.' where paper_id = '.$this->dao->quote($paper_id).' group by module_id');
	}


	public function get_moduleRecord($arr){
		//echo 'select count(*) as num,r.is_correct from '.$this->vip_eval_record.' as r left join  '.$this->vip_eval_question.' as q on q.id = r.question_id where r.paper_id = '.$this->dao->quote($arr['paper_id']).' and q.module_id ='.$this->dao->quote($arr['module_id']).' group by r.is_correct';exit;
		return $this->dao->getAll('select count(*) as num,r.is_correct from '.$this->vip_eval_record.' as r left join  '.$this->vip_eval_question.' as q on q.id = r.question_id where r.paper_id = '.$this->dao->quote($arr['paper_id']).' and q.module_id ='.$this->dao->quote($arr['module_id']).' group by r.is_correct');
	}


	public function deletePaper($paper_id){
		$this->dao->execute('begin');
		$success1 = (boolean)$this->dao->execute('DELETE FROM '.$this->vip_eval_paper.' WHERE id = '.$this->dao->quote(abs($paper_id)));
		//$success2 = (boolean)$this->dao->execute('DELETE FROM '.$this->vip_eval_module.' WHERE paper_id = '.$this->dao->quote(abs($paper_id)));
		$success3 = (boolean)$this->dao->execute('DELETE FROM '.$this->vip_eval_question.' WHERE paper_id = '.$this->dao->quote(abs($paper_id)));
		$success4 = (boolean)$this->dao->execute('DELETE FROM '.$this->vip_eval_record.' WHERE paper_id = '.$this->dao->quote(abs($paper_id)));
		$success5 = (boolean)$this->dao->execute('DELETE FROM '.$this->vip_eval_result.' WHERE paper_id = '.$this->dao->quote(abs($paper_id)));
		if($success1 == false ||  $success3 == false || $success4 == false || $success5 == false){
			$this->dao->execute(' rollback');
			return false;
		}
		$this->dao->execute(' commit');
		return true;
	}


	public function deleteModule($module_id){
		$this->dao->execute('begin');
		$success1 = (boolean)$this->dao->execute('DELETE FROM '.$this->vip_eval_module.' WHERE id = '.$this->dao->quote(abs($module_id)));
		$success2 = (boolean)$this->dao->execute('DELETE FROM '.$this->vip_eval_question.' WHERE module_id = '.$this->dao->quote(abs($module_id)));
		if($success1 == false || $success2 == false){
			$this->dao->execute(' rollback');
			return false;
		}
		$this->dao->execute(' commit');
		return true;
	}


	public function deleteQuestion($question_id){
		$this->dao->execute('begin');
		$success1 = (boolean)$this->dao->execute('DELETE FROM '.$this->vip_eval_question.' WHERE id = '.$this->dao->quote(abs($question_id)));
		$success2 = (boolean)$this->dao->execute('DELETE FROM '.$this->vip_eval_record.' WHERE question_id = '.$this->dao->quote(abs($question_id)));
		if($success1 == false || $success2 == false){
			$this->dao->execute(' rollback');
			return false;
		}
		$this->dao->execute(' commit');
		return true;
	}


	public function get_moduleData($result_id){
		$resultInfo = $this->get_resultInfo($result_id);
		$moduleData = $this->get_moduleList(array('paper_id'=>$resultInfo['paper_id']));
		$moduleNameStr = "";
		$moduleAccuracyAvg = '';
		$moduleAccuracySelf = '';
		if(!empty($moduleData)){
			foreach ($moduleData as $key=>$module){
				$moduleData[$key]['question_num'] = $this->dao->getOne('SELECT count(*) as question_num FROM '.$this->vip_eval_question.' WHERE module_id = '.$this->dao->quote($module['id']).' AND paper_id ='.$this->dao->quote($resultInfo['paper_id']));
				$moduleNameStr .= "'$module[name]',";
				$moduleData[$key]['correct_total'] = 0;
				$moduleData[$key]['error_total'] = 0;
				$moduleData[$key]['myself_correct_total'] = 0;
				$moduleData[$key]['myself_error_total'] = 0;
				$recordList = $this->dao->getAll('SELECT r.id,
														 r.uid,
														 r.question_id,
														 r.paper_id,
														 r.answer,
														 r.is_correct,
														 r.result_id,
														 q.img,
														 q.seq,
														 q.answer as correct_answer  
														 FROM '.$this->vip_eval_record.' AS r 
														 LEFT JOIN '.$this->vip_eval_question.' AS q ON q.id = r.question_id 
														 WHERE q.module_id = '.$this->dao->quote($module['id']).' AND r.paper_id = '.$this->dao->quote($resultInfo['paper_id']).' ORDER BY q.seq ASC');
				if(!empty($recordList)){
					foreach ($recordList as $kk=>$record){
						if($record['is_correct']==1){
							$moduleData[$key]['correct_total']++;
							if($resultInfo['uid'] == $record['uid']){
								$moduleData[$key]['myself_correct_total']++;
								$moduleData[$key]['myRecordList'][] = $recordList[$kk];
							}
						}else if($record['is_correct']==0){
							$moduleData[$key]['error_total']++;
							if($resultInfo['uid'] == $record['uid']){
								$moduleData[$key]['myself_error_total']++;
								$moduleData[$key]['myRecordList'][] = $recordList[$kk];
							}
						}
					}
				}
				$moduleData[$key]['correct_avg'] = ceil($moduleData[$key]['correct_total']/count($recordList));
				$moduleData[$key]['accuracy_avg'] = sprintf('%.2f',($moduleData[$key]['correct_total']/count($recordList))*100);
				$moduleData[$key]['accuracy_self'] = sprintf('%.2f',($moduleData[$key]['myself_correct_total']/($moduleData[$key]['myself_error_total']+$moduleData[$key]['myself_correct_total']))*100);
				$moduleAccuracyAvg .= $moduleData[$key]['accuracy_avg'].',';
				$moduleAccuracySelf .= $moduleData[$key]['accuracy_self'].',';
				if($moduleData[$key]['accuracy_self']>=$module['excellent_strong']){
					$moduleData[$key]['excellent_status'] = '较强，需要继续保持。';
				}else if($moduleData[$key]['accuracy_self']<$module['excellent_weak']){
					$moduleData[$key]['excellent_status'] = '较弱，需要重点加强。';
				}else{
					$moduleData[$key]['excellent_status'] = '中等，需要进一步提高。';
				}
			}

			foreach ($moduleData as $key=>$module){
				if(!empty($module['myRecordList'])){
					foreach ($module['myRecordList'] as $kk=>$record){
						$moduleData[$key]['myRecordList'][$kk]['img_url'] = str_replace('/Upload/','/upload/',$record['img']);
						$moduleData[$key]['myRecordList'][$kk]['correct_total'] = 0;
						$questionRecord = $this->dao->getAll('SELECT is_correct FROM '.$this->vip_eval_record.' WHERE question_id = '.$this->dao->quote($record['question_id']).' AND paper_id='.$this->dao->quote($record['paper_id']));
						$moduleData[$key]['myRecordList'][$kk]['questionRecord'] = $questionRecord;
						$moduleData[$key]['myRecordList'][$kk]['total'] = count($questionRecord);
						if(!empty($questionRecord)){
							foreach ($questionRecord as $k=>$answer){
								if($answer['is_correct'] == 1){
									$moduleData[$key]['myRecordList'][$kk]['correct_total']++;
								}
							}
						}
						$moduleData[$key]['myRecordList'][$kk]['accuracy_avg'] = sprintf('%.0f',($moduleData[$key]['myRecordList'][$kk]['correct_total']/$moduleData[$key]['myRecordList'][$kk]['total'])*100);
					}
				}
			}
		}
		return array('data'=>$moduleData,'nameStr'=>$moduleNameStr,'AccuracyAvgStr'=>$moduleAccuracyAvg,'AccuracySelfStr'=>$moduleAccuracySelf);
	}
}
?>