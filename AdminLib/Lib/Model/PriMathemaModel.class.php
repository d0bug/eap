<?php
class PriMathemaModel extends Model {
	public $dao = null;
	public $user = '';
	public function __construct() {
		$this->dao = Dao::getDao ();
		if (class_exists ( 'User', false )) {
			$operator = User::getLoginUser ();
			if ($operator)
				$this->userKey = $operator->getUserKey ();
		}
		$userInfo = $operator->getInformation(); 
		$this->user = $userInfo['user_name'];
	}
	//当前的季度
	public function getSemester(){
   		$sql = "select id,sName,nCurrentClassYear as nClassYear from D_Semester where bCurrentSemester = 1";
   		$result = $this->dao->getRow($sql);
   		if ($result){
   			return $result;
   		}
   	}
   	//获取班级类型
   	public function getClassType($nClassYear='',$nSemester='',$xuebu='1',$xueke='1',$sDeptCode='DPBJ001'){
   		if (empty($nClassYear) || empty($nSemester)){
   			$semester = $this->getSemester();
   			$nClassYear = $nClassYear ? $nClassYear : $semester['nclassyear'];
   			$nSemester = $nSemester ? $nSemester : $semester['id'];
   		}
   		$sql = "select scode,sname from bs_classtype a where nXuebu=$xuebu and nXueKe=$xueke and bvalid=1 and exists
   				(select 1 from view_classname where sDeptCode='".$sDeptCode."' and nclassyear=$nClassYear and 
   				nsemester=$nSemester and bLongterm=1 and nXuebu=$xuebu and nXueKe=$xueke 
   				and sclasstypeCode =a.scode )order by sname";
   		return $this->dao->getAll($sql);
   	}
   	
   	//获取尖子班号
   	public function getClassNo($k){
   		$sql = "select distinct sName from viewBS_Class where sclasstypecode = '$k'";
   		$result = $this->dao->getAll($sql);
   		return $result;
   	}
   	//获取可创建作业的讲次
   	public function getExplainNo($k,$nClassYear='',$nSemester='',$xuebu='1',$xueke='1'){
   		if (empty($nClassYear) || empty($nSemester)){
   			$semester = $this->getSemester();
   			$nClassYear = $nClassYear ? $nClassYear : $semester['nclassyear'];
   			$nSemester = $nSemester ? $nSemester : $semester['id'];
   		}
   		$sql = "select distinct nlessonno from bs_lesson l where exists(select 1 from view_classname where 
   				nclassyear=$nClassYear and nsemester=".$nSemester." and bLongterm=1 and nXuebu=$xuebu and 
   				nXueKe=$xueke and sCode=l.sClassCode and sClassTypeCode='".$k."') order by nlessonno";
   		$result = $this->dao->getAll($sql);
   		return $result;
   	}
   	//获取已创建作业的讲次
   	public function getExplain($class = array(),$nClassYear,$nSemester){
   		$where = '';
   		if ($nClassYear){
   			$where .= " and nClassYear = $nClassYear";
   		}
   		if ($nSemester){
   			$where .= " and nSemester = $nSemester";
   		}
   		$sql = "select distinct(ExplainNo) from NCS_New_QuestionRollInfo where ".key($class)." = '".$class[key($class)]."'".$where;
   		return $this->dao->getAll($sql);
   	}
   	//获取学生讲次
   	public function getLessonNo($class=array(),$nClassYear,$nSemester){
   		$where = '';
   		if ($nClassYear){
   			$where .= " and b.nClassYear = $nClassYear";
   		}
   		if ($nSemester){
   			$where .= " and b.nSemester = $nSemester";
   		}
   		$sql = "select distinct(nLessonNo) from NCS_New_Student_AnwerInfo a left join viewBS_Class b on a.sClassCode = b.sCode
   				where ".key($class)." = '".$class[key($class)]."'".$where;
   		return $this->dao->getAll($sql);
   	}
   	/**
   	 * 添加作业info
   	 * 解释：QRI = Question Roll Info
   	 */
   	public function addQRI($classType,$className,$ExplainNo,$knowledge,$knowledgeName,$ClassNo='',$nClassYear='',$nSemester=''){
   		if (empty($nClassYear) || empty($nSemester)){
   			$semester = $this->getSemester();
   			$nClassYear = $nClassYear ? $nClassYear : $semester['nclassyear'];
   			$nSemester = $nSemester ? $nSemester : $semester['id'];
   		}
   		$status = $this->checkQuestion($classType,$className,$ExplainNo,$ClassNo,$nClassYear,$nSemester);
   		if ($status === true){
	   		if (is_array($ClassNo)){
	   			$ClassNo = implode(',',$ClassNo);
	   		}
	   		$sql = "insert into NCS_New_QuestionRollInfo values('$classType','$className','$ClassNo',$ExplainNo,'".date('Y-m-d H:i:s',time())."',1,$nClassYear,$nSemester,0,0,'$knowledge','$knowledgeName','$this->user')";
	   		$result = $this->dao->execute($sql);
	   		if ($result){
	   			return true;
	   		}
   		}
   	}
   	/**
   	 *添加作业
   	 *解释：QL = Question List
   	 *$type = 1为客观 2是主观  
   	 */
   	public function addQL($infoid,$tikuIds=array(),$type=1){
   		if ($type == 1){
   			$tbName = "NCS_New_QuestionList";
   			$column = "nQuestionRollInfoID,nQuestionIndex,type,tikuId,addUser,addTime";
   			$t = 'objective';
   			$qtype = 0;
            $infoCol = 'nQuestionRollInfoID';
   		}else{
   			$tbName = "NCS_New_Subjective";
   			$qtype = 1;
   			$t = 'subjective';
   			$column = "infoid,qindex,qtype,tikuId,addUser,addTime";
            $infoCol = 'infoid';
   		}
   		$status = true;
   		$this->dao->begin();
   		foreach ($tikuIds as $one){
            $sql = 'SELECT count(1) FROM ' . $tbName . ' WHERE ' . $infoCol . '=' . abs($infoid) . ' AND tikuid=' . abs($one);
            if($this->dao->getOne($sql) == 0) {
                $sql = "insert into $tbName($column) values($infoid,0,$qtype,$one,'$this->user',".time().")";
                if (!$this->dao->execute($sql)){
                    $status = false;
                }
            }
   		}
   		$sql = "update NCS_New_QuestionRollInfo set $t = 1 where ID = ".$infoid;
   		$trel = $this->dao->execute($sql);
   		if ($status == true && $trel){
   			$this->dao->commit();
   		}else{
   			$this->dao->rollback();
   		}
   		return $status;
   	}
   	/**
   	 * 获取客观题列表
   	 * $column 按哪个字段
   	 */
   	public function getQL($id,$column= 'nQuestionRollInfoID'){
   		$sql = "select a.id,a.nQuestionRollInfoID as infoid,nQuestionIndex as qindex,type as qtype,nScore as score, 
					case when Answer = '' or Answer is null then sOption else Answer end as answer,example,tikuId,lockType
					from NCS_New_QuestionList a left join NCS_New_QuestionList_Anwer b on a.id = b.QuestionListID 
   					where a.$column = ".$id;
   		return $result = $this->dao->getAll($sql);
   	}
   	//检查作业是否已创建
   	public function checkQuestion($classType,$className,$ExplainNo,$ClassNo='',$nClassYear='',$nSemester=''){
   		$status = true;
   		$sql = "SELECT * FROM NCS_New_QuestionRollInfo WHERE nClassYear = ".$nClassYear." and nSemester = ".$nSemester.
   				" and ClassType='".$classType."'  and ExplainNo='".$ExplainNo."'";
   		$s = $this->dao->getAll($sql);
   		if (count($s)>0){
   			if (strpos($className,'尖子')){
   				if (is_array($ClassNo)){
   					foreach ($s as $one){
   						if (!empty($one['classno'])){
   							$arr = '';
   							$arr = explode(',',$one['classno']);
   							if (is_array($arr)){
   								$r = array_intersect($arr,$ClassNo);
   								if (count($r) > 0){
   									$status = false;
   									break;
   								}
   							}
   						}
   					}
   				}else{
   					$status = false;
   				}
   			}else{
   				$status = false;
   			}
   		}
   		return $status;
   	}
   	/**
   	 * 添加口述题
   	 */
   	public function addNumcupate($infoid,$qindex=1,$score=0){
   		if ($infoid){
   			$status = true;
   			$ImgUrl = 'noImg';
   			$this->dao->begin();
   			$sql = "insert NCS_New_QuestionList(nQuestionRollInfoID,nQuestionIndex,sQuestionImgUrl,type) values($infoid,$qindex,'$ImgUrl',3)";
   			$r = $this->dao->execute($sql);
   			if ($r){
   				$lastId = $this->dao->lastInsertId();
   				$s = "insert into NCS_New_QuestionList_Anwer(QuestionListID,nScore,Answer) values($lastId,$score,'口述题')";
   				$rr = $this->dao->execute($s);
   				if (!$rr){
   					$status = false;
   				}else{
   					$ms = "update NCS_New_QuestionRollInfo set objective = 1 where ID = ".$infoid;
   					$msr = $this->dao->execute($ms);
   					if (!$msr){
   						$status = false;
   					}
   				}
   			}else{
   				$status = false;
   			}
   			if ($status == true){
   				$this->dao->commit();
   			}else{
   				$this->dao->rollback();
   			}
   			return $status;
   		}else{
   			return false;
   		}
   	}

    public function getDeptClassTypes($deptCode){
        static $deptCtCodeList = array();

        if(false == $deptCtCodeList) {
            $strQuery = 'SELECT scode FROM BS_CLASSTYPE WHERE sprojectcode IN (
                SELECT scode from bs_project
                WHERE sdeptcode =' . $this->dao->quote($deptCode) . '
            )';
            
            $deptCtCodeList = $this->dao->getAll($strQuery);
        }

        return $deptCtCodeList;
    }

    public function getStrCtCodes($deptCode) {
        static $strCtCodes = null;

        if(null === $strCtCodes) {
            $deptCtCodeList = $this->getDeptClassTypes($deptCode);
            if($deptCtCodeList) {
                $quoteCodeArray = array();
                foreach($deptCtCodeList as $dept) {
                    $quoteCodeArray[] = $this->dao->quote($dept['scode']);
                }
                $strCtCodes = implode(',', $quoteCodeArray);
            } else {
                $strCtCodes = $this->dao->quote('0');
            }
        }
        return $strCtCodes;
    }

   	public function getAllClass($column = 'classtype', $deptCode='', $nClassYear='', $nSemester=''){
        if($column == null){
            $column = 'classtype';
        }
   		$sql = "select distinct($column) from NCS_New_QuestionRollInfo WHERE 1=1 ";

        if($deptCode) {
            $strCtCodes = $this->getStrCtCodes($deptCode);
            if($strCtCodes) {
                $sql .= " AND classtype IN (" . $strCtCodes . ")";
            }
        }

        if(false != $nClassYear) {
            $sql .= " AND nclassyear=" . abs($nClassYear);
        }

        if(false != $nSemester) {
            $sql .= " AND nsemester=" . $this->dao->quote($nSemester);
        }

   		$rel = $this->dao->getAll($sql);
   		if ($rel){
   			$str = '';
   			foreach ($rel as $k=>$one){
   				if ($k == 0){
   					$str = "'".$one['classtype']."'";
   				}else{
   					$str .= ",'".$one['classtype']."'";
   				}
   			}
   		}
        if(false == $str) {
            return array();
        }
   		$strSql = "select sCode as classTypeCode,sName as classTypeName from BS_ClassType where scode in ($str)";
   		return  $this->dao->getAll($strSql);
   	}
   	//获取指定卷子的所有题目答案信息
   	//1是客观，2是主观
   	public function getQuestionList($infoid,$type){
   		if ($type == 1){
   			$sql = "select a.id,a.nQuestionRollInfoID as infoid,nQuestionIndex as qindex,type as qtype,nScore as score, 
					case when Answer = '' or Answer is null then sOption else Answer end as answer,example,tikuId,lockType
					from NCS_New_QuestionList a left join NCS_New_QuestionList_Anwer b on a.id = b.QuestionListID 
   					where nQuestionRollInfoID = $infoid
   					ORDER BY nquestionIndex,tikuid";
   		}else{
   			$sql = "select * from NCS_New_Subjective where infoid = $infoid
   			        ORDER BY qindex,tikuid";
   		}
   		return $this->dao->getAll($sql);
   	}
   	/**
   	 *保存题目编辑状态
   	 *$type = 1 客观
   	 *$type = 2 主观 
   	 */
   	public function saveQuestion($arr,$type=1){
   		if ($type == 1){
   			$Question = $this->getOneRecord('NCS_New_QuestionList',array('id'=>$arr['id']));
   			if ($Question){
   				$arr['answer'] = $this->make_semiangle($arr['answer']);
   				$s = $this->getOneRecord('NCS_New_Student_AnwerInfo',array('nQuestionRollInfoID'=>$Question['nquestionrollinfoid']));
   				if ($s == false || $Question['nquestionrollinfoid'] == 0 || ($s && $arr['key']==md5($arr['id'])) ){
	   				$status = true;
	   				$this->dao->begin();
	   				$sql1 = "update NCS_New_QuestionList set nQuestionIndex=".$arr['qindex'].",type=".$arr['type'].",
	   				lastUpdateUser='$this->user',lastUpdateTime=".time()." where id = ".$arr['id'];
	   				$rel1 = $this->dao->execute($sql1);
	   				$this->addHistory('NCS_New_QuestionList','update',$arr['id'].'|'.$Question['tikuid']);
	   				if (!$rel1){
	   					$status = false;
	   				}
	   				$r2 = $this->getOneRecord('NCS_New_QuestionList_Anwer',array('QuestionListID'=>$arr['id']));
	   				if ($arr['type'] == 0){
	   					$column = 'sOption';
	   					$nullColumn = 'Answer';
	   				}else{
	   					$column = 'Answer';
	   					$nullColumn = 'sOption';
	   				}
	   				if ($r2){
	   					$sql2 = "update NCS_New_QuestionList_Anwer set nScore = '".$arr['score']."',$column = '".$arr['answer']."',
	   							".$nullColumn." = null ,example = '".$arr['example']."',lockType= '".$arr['lockType']."' where QuestionListID = ".$arr['id'];
	   				}else{
	   					$sql2 = "insert into NCS_New_QuestionList_Anwer(QuestionListID,nScore,$column,example,lockType) 
	   					values(".$arr['id'].",'".$arr['score']."','".$arr['answer']."','".$arr['example']."','".$arr['lockType']."')";
	   				}
	   				$rel2 = $this->dao->execute($sql2);
	   				if (!$rel2){
	   					$status = false;
	   				}
	   				if ($status == true){
	   					$this->dao->commit();
	   				}else{
	   					$this->dao->rollback();
	   				}
	   				return $status;
	   			}
   			}
   		}else{
   			$Question = $this->getOneRecord('NCS_New_Subjective',array('id'=>$arr['id']));
   			if ($Question){
   				$sql = "update NCS_New_Subjective set qindex = '".$arr['qindex']."',score='".$arr['score']."',
   						answer='". str_replace("'","''",$arr['answer'])."',example='".$arr['example']."',qtype=".$arr['type'].",
	   					lastUpdateUser='$this->user',lastUpdateTime=".time()." where id = ".$arr['id'];
   				$rel = $this->dao->execute($sql);
   				$this->addHistory('NCS_New_Subjective','update',$arr['id'].'|'.$Question['tikuid']);
   				if ($rel){
   					return true;
   				}
   			}
   		}
   	}
   	//添加操作记录
   	private function addHistory($table,$action,$content){
   		$h = "insert into Ncs_New_OperateHistory values('$this->user',".time().",'$action','$table','$content')";
   		$this->dao->execute($h);
   	}
   	//获取卷子总数
   	public function countQRI($condition){
   		$strQuery = "SELECT count(1) FROM NCS_New_QuestionRollInfo WHERE 1=1 ".$condition;
   		return $this->dao->getOne ( $strQuery );
   	}
   	//题目管理分页显示
   	public function getQRI($condition='', $currentPage=1, $pageSize=10) {
   		$sql = "SELECT * FROM NCS_New_QuestionRollInfo WHERE 1=1";
   		$order ='ORDER BY id DESC';
   		$fun = 'countQRI'; //调用countQRI函数
   		return $this->getPage($sql,$order,$condition,$fun,$currentPage,$pageSize);
   	}
   	//获取学生答卷总数
   	public function countStuList($condition){
   		$strQuery = "SELECT count(1) FROM NCS_New_Student_AnwerInfo a left join viewBS_Class b
   					on a.sClassCode = b.sCode left join bs_student c on a.StudentCode = c.sCode WHERE 1=1 ".$condition;
   		return $this->dao->getOne ( $strQuery );
   	}
   	//获取学生作业列表
   	public function getStuList($condition,$currentPage=1,$pageSize=10){
   		$sql = "SELECT a.id,a.sClassCode,a.sClassName,a.nlessonno,a.objective,a.subjective,a.score,a.subScore,b.nClassYear,
   				b.nSemester,c.sName FROM NCS_New_Student_AnwerInfo a left join viewBS_Class b on a.sClassCode = b.sCode 
   				left join bs_student c on a.StudentCode = c.sCode WHERE 1=1 ".$condition;
   		$order ='ORDER BY id DESC';
   		$fun = 'countStuList'; //调用countQRI函数
   		return $this->getPage($sql,$order,$condition,$fun,$currentPage,$pageSize);
   	}
   	//获取学生信息
   	public function getStudentInfo($array=array(),$column="*"){
   		$where = '';
   		foreach ($array as $key=>$val){
   			$where .= ' AND '.$key." = '$val'";
   		}
   		$sql = "select $column FROM NCS_New_Student_AnwerInfo a left join BS_Student b 
   				on a.StudentCode = b.sCode where 1=1".$where;
   		return $this->dao->getRow($sql);
   	}
   	public function getStuWork($infoid){
   		$sql = "select a.*,b.nScore from NCS_New_Student_AnwerList a left join NCS_New_QuestionList_Anwer b on a.qid = b.QuestionListID where a.infoid = $infoid";
   		return $this->dao->getAll($sql);
   	}
   	//编辑学生作业
   	public function editStuWork($array,$studentInfo){
   		$r = $this->getOneRecord('NCS_New_Student_AnwerList',array('id'=>$array['id']),'1');
   		if($r){
   			$status = false;
   			$this->dao->begin();
   			$updateSql = "update NCS_New_Student_AnwerList set StudentAnswer = '".$array['studentanswer']."',
   					lscore = '".$array['lscore']."',title=".$array['title']." where id = ".$array['id'];
   			$updateRel = $this->dao->execute($updateSql);
   			if ($updateRel != false){
   				$strSql = "select lscore from NCS_New_Student_AnwerList where infoid = ".$array['infoid'];
   				$result = $this->dao->getAll($strSql);
   				$sumScore = 0;
   				foreach ($result as $one){
   					$sumScore += $one['lscore'];
   				}
   				$upInfoSql = "update NCS_New_Student_AnwerInfo set score = ".$sumScore." where id = ".$studentInfo['id'];
   				$upInfoRel = $this->dao->execute($upInfoSql);
   				if ($upInfoRel != false){
   					$sqlupdate = "exec WCF_S_ZY_UpdateClassLessonScore '".$studentInfo['studentcode']."','".$studentInfo['sclasscode']."','".$studentInfo['scardcode']."','".$studentInfo['nlessoncode']."','".$studentInfo['nlessonno']."','".$sumScore."',''";
   					$this->dao->execute($sqlupdate);
   					$status = true;
   				}
   			}
   			if ($status === true){
   				$this->dao->commit();
   				$this->addHistory('NCS_New_Student_AnwerList','update','修改id为'.$array['id'].'的信息');
   			}else{
   				$this->dao->rollback();
   			}
   			return $status;
   		}
   	}
   	//删除学生作业 1客观 2主观 3全部
   	public function deleteStuWrok($id,$type=1,$info){
   		$sql = array();
   		if ($type == 1){
   			$message = $info['studentcode'].'|'.$info['sclasscode'].'|第'.$info['nlessonno'].'讲|删除客观';
   			$sql[] = "update NCS_New_Student_AnwerInfo set objective = 0,score = 0,objFrom = 0,CreateTime = null,
   						stuOver = 0,subLock = 0 where id = ".$id;
   			$sql[] = "delete from NCS_New_Student_AnwerList where InfoId = ".$id;
   		}elseif ($type == 2){
   			$message = $info['studentcode'].'|'.$info['sclasscode'].'|第'.$info['nlessonno'].'讲|删除主观';
   			$sql[] = "update NCS_New_Student_AnwerInfo set subjective = 0,subScore = 0,subFrom = 0,markingStatus = 0,
   						markingOver = 0,stuOver = 0,subTime = null,subLock = 0,smsStatus = 0 where id = ".$id;
   			$sql[] = "delete from NCS_New_SubjectivePicList where infoid = ".$id;
   		}elseif ($type == 3){
   			$message = $info['studentcode'].'|'.$info['sclasscode'].'|第'.$info['nlessonno'].'讲|全部删除';
   			$sql[] = "delete from NCS_New_Student_AnwerInfo where id = ".$id;
   			$sql[] = "delete from NCS_New_Student_AnwerList where InfoId = ".$id;
   			$sql[] = "delete from NCS_New_SubjectivePicList where infoid = ".$id;
   		}
   		$this->dao->begin();
   		$status = true;
   		foreach ($sql as $one){
   			$r = $this->dao->execute($one);
   			if (!$r){
   				$status = false;
   				break;
   			}
   		}
   		if ($status === true){
   			$this->dao->commit();
   			$this->addHistory('NCS_New_Student_AnwerInfo','delete',$message);
   		}else{
   			$this->dao->rollback();
   		}
   		return $status;
   	}
   	//分页显示
   	public function getPage($sql,$order='',$condition='',$fun,$currentPage=1,$pageSize=10){
   		$count = $this->$fun($condition);
   		$pageCount = ceil($count / $pageSize);
   		if($currentPage > $pageCount) $currentPage = $pageCount;
   		if($currentPage < 1) $currentPage = 1;
   		$strSql = $sql;
   		if($condition){
   			$strSql = $sql.$condition;
   		}
   		return $this->dao->getLimit($strSql, $currentPage, $pageSize, $order);
   	}
   	
   	//查询一个表的单条记录
   	public function getOneRecord($table,$where=array(),$column='*'){
   		if ($where){
   			$w = '';
   			$index = 1;
   			foreach ($where as $k=>$v){
   				if ($index == 1){
   					$w = " $k = '".$v."'";
   				}else{
   					$w .= " AND $k = '".$v."'";
   				}
   				$index++;
   			}
   			$sql = "select  $column from $table where $w";
   		}else{
   			$sql = "select  $column from $table";
   		}
   		//echo $sql.'<br>';
   		$result = $this->dao->getRow($sql);
   		if ($result){
   			return $result;
   		}else{
   			return false;
   		}
   	}
   	
   	//查询一个表的多条记录
   	public function getAllRecord($table,$where=array(),$column='*',$limit='',$order=''){
   		if ($limit){
   			$limit = "top ".intval($limit);
   		}
   		if ($order){
   			$order = 'order by '.$order;
   		}
   		if ($where){
   			$w = '';
   			$index = 1;
   			foreach ($where as $k=>$v){
   				if ($index == 1){
   					$w = "$k = '".$v."'";
   				}else{
   					$w = " AND $k = '".$v."'";
   				}
   				$index++;
   			}
   			$sql = "select $limit $column from $table where $w $order";
   		}else{
   			$sql = "select $limit $column from $table $order";
   		}
   		$result = $this->dao->getAll($sql);
   		if ($result){
   			return $result;
   		}else{
   			return false;
   		}
   	}
   	//更新一张表数据
   	public function updateRecord($table,$s,$w){
   		if (empty($table) || empty($w) || !is_array($w) || empty($s) || !is_array($s)){
   			return false;exit;
   		}
   		$i = 0;
   		foreach ($w as $k=>$v){
   			if ($i==0){
   				$where = " $k = '".$v."'";
   			}else{
   				$where .= " AND $k = '".$v."'";
   			}
   			$i++;
   		}
   		$m = 0;
   		foreach ($s as $kk=>$vv){
   			if ($m==0){
   				$set = " $kk = '".$vv."'";
   			}else{
   				$set .= ",$kk = '".$vv."'";
   			}
   			$m++;
   		}
   		 
   		$sql = "update $table SET $set WHERE $where";
   		$result = $this->dao->execute($sql);
   		if ($result) {
   			return true;
   		}else{
   			return false;
   		}
   	}
   	//删除整套题目
   	public function deleteQRI($infoid){
   		$this->dao->begin();
   		$status = true;
   		$s = "select * from NCS_New_QuestionRollInfo where ID = ".$infoid;
   		$s = $this->dao->getRow($s);
   		$sql1 = "delete from NCS_New_QuestionList where nQuestionRollInfoID = ".$infoid;
   		$sql2 = "delete from NCS_New_Subjective where infoid = ".$infoid;
   		$sql3 = "delete from NCS_New_QuestionRollInfo where ID = ".$infoid;
   		$r1 = $this->dao->execute($sql1);
   		$r2 = $this->dao->execute($sql2);
   		$r3 = $this->dao->execute($sql3);
   		$this->addHistory('NCS_New_QuestionRollInfo','delete',"(".$s['nclassyear'].$s['nsemester'].')'.$s['classname'].':第'.$s['explainno'].'讲');
   		if (!$r1 || !$r2 || !$r3){
   			$status = false; 
   		}
   		if ($status === true){
   			$this->dao->commit();
   		}else{
   			$this->dao->rollback();
   		}
   		return $status;
   	}
   	//删单条题目 1客观 2主观
   	public function deleteList($id,$type=1){
   		$status = true;
   		if ($type == 1){
   			$this->dao->begin();
   			$sql1 = "delete from NCS_New_QuestionList_Anwer where QuestionListID = ".$id;
   			$sql2 = "delete from NCS_New_QuestionList where ID = ".$id;
   			$r1 = $this->dao->execute($sql1);
   			$r2 = $this->dao->execute($sql2);
   			$this->addHistory('NCS_New_QuestionList','delete',$id);
   			if (!$r1 || !$r2){
   				$status = false;
   			}
	   		if ($status === true){
	   			$this->dao->commit();
	   		}else{
	   			$this->dao->rollback();
	   		}
   		}elseif ($type == 2){
   			$sql = "delete from NCS_New_Subjective where id = ".$id;
   			$r = $this->dao->execute($sql);
   			$this->addHistory('NCS_New_Subjective','delete',$id);
   			if (!$r){
   				$status = false;
   			}
   		}
   		return $status;
   	}
   	//字符加密
   	public function str_md5($str){
   		return md5('gs'.$str.'wzl');
   	}
   	//全角转半角
   	public function make_semiangle($str){
   		$arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
   				'５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
   				'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
   				'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
   				'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
   				'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
   				'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
   				'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
   				'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
   				'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
   				'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
   				'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
   				'ｙ' => 'y', 'ｚ' => 'z',
   				'（' => '(', '）' => ')', '［' => '[', '］' => ']', '【' => '[',
   				'】' => ']', '〖' => '[', '〗' => ']', '“' => '"', '”' => '"',
   				'｛' => '{', '｝' => '}', '《' => '<',
   				'》' => '>','／'=>'/','＼'=>'\\','＝'=>'=','＞'=>'>','＜'=>'<',
   				'％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-',
   				'：' => ':', '。' => '.', '、' => ',', '，' => ',',
   				'；' => ';', '？' => '?', '！' => '!', '……' => '……', '‖' => '|',
   				'”' => '"', '’' => '`', '‘' => '`', '｜' => '|', '〃' => '"',
   				'　' => ' ','＄'=>'$','＠'=>'@','＃'=>'#','＾'=>'^','＆'=>'&','＊'=>'*',
   				'＂'=>'"');
   		 
   		return strtr($str, $arr);
   	}  
}
?>