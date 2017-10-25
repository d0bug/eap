<?php
class PriMathemaAction extends HomeworkCommAction {
	private $model;
	private $userInfo;
	public function __construct(){
		parent::__construct();
		$this->model = new PriMathemaModel();
		$this->userInfo = $this->loginUser->getInformation();
	}
	protected function notNeedLogin() {}

    private function getEmpDept(){
        $userKey = $this->loginUser->getUserKey();
        list($userType, $userName) = explode('-', $userKey);
        $userName = strtolower($userName);
        $hwDeptArray =  C('HW_DEPT_ARRAY');
        $deptUserArray = C('HW_DEPT_USERS');

        $deptArray = array();
        if(in_array($userName, $deptUserArray['penglish'])) {
            $deptArray = array('penglish'=>$hwDeptArray['penglish']);
        } else if(in_array($userName, $deptUserArray['menglish'])){
            $deptArray = array('menglish'=>$hwDeptArray['menglish']);
        }else if(in_array($userName, $deptUserArray['henglish'])) {
            $deptArray = array('henglish'=>$hwDeptArray['henglish']);
        } else if(in_array($userName, $deptUserArray['both'])) {
            $deptArray = array('math'=>$hwDeptArray['math'],
                               'menglish'=>$hwDeptArray['menglish'],
                               'henglish'=>$hwDeptArray['henglish']);
        } else {
            $deptArray = array('math'=>$hwDeptArray['math']);
        }

        return $deptArray;
    }
	/**
	 * 创建作业
	 */
	public function create() {
        //当前管理员可维护的学科数组
        $empDeptArray = $this->getEmpDept();

        //优先从url中获取学科
        $deptName = trim($_GET['dept']);
        if($deptName && isset($empDeptArray[$deptName])) {
            //url中包含学科时取相对设置
            $deptCfg = $empDeptArray[$deptName];
        } else {
            //url不包含学科时取第一个学科
            foreach($empDeptArray as $deptName=>$deptCfg) {
                break;
            }
        }

        //构造学科知识点数据
        $params = $deptCfg['tkDeptId'];
		$apiModel = new TiKuApiModel();
		$knowledgeArr = json_decode($apiModel->linkTiApi('knodes',$params));

		$i = 1;
		$tree = '';
		unset($knowledgeArr[0]);
		unset($knowledgeArr[1]);
		/*unset($knowledgeArr[2]);*/
		foreach ($knowledgeArr as $v){
			if ($i == 1){
				$tree .= '{id:'.$v->id.', pId:'.$v->pid.', name:"'.str_replace("\r","",$v->caption).'"}';
			}else{
				$tree .= ',{id:'.$v->id.', pId:'.$v->pid.', name:"'.str_replace("\r","",$v->caption).'"}';
			}
			$i++;
		}
		if ($_POST){
			$status = $this->model->addQRI($_POST['ClassType'],$_POST['ClassName'],$_POST['ExplainNo'],$_POST['codeSel'],$_POST['knowledgeName'],$_POST['ClassNo'],$_POST['nClassYear'],$_POST['nSemester']);
			if ($status == true){
				$this->redirect('PriMathema/operate', array('dept' => $_POST['dept']));
			}
		}
		$weixin = new WeixinHomeworkModel();
		$xueqiArr = $weixin->getXueqi();
		$nSemester = $this->model->getSemester();
		$s = intval($_GET['s']);
		$y = intval($_GET['y']);



		$classType = $this->model->getClassType($y,$s, $deptCfg['nxuebu'], $deptCfg['nxueke'], $deptCfg['deptCode']);
		$this->assign(get_defined_vars());
		$this->display ();
	}
	//添加作业 1是客观，2是主观
	public function add(){
        $empDeptArray = $this->getEmpDept();

        $deptName = trim($_GET['dept']);

        if($deptName && isset($empDeptArray[$deptName])) {
            $deptCfg = $empDeptArray[$deptName];
        } else {
            foreach($empDeptArray as $deptName=>$deptCfg) {
                break;
            }
        }
        $tkKwId = $deptCfg['tkKwId'];
		$type = intval($_GET['type']);
		$title = $type == 1 ? '客观' : '主观';
		$infoid = intval($_GET['infoid']);
		$info = $this->model->getOneRecord('NCS_New_QuestionRollInfo',array('ID'=>$infoid),'knowledge');
		if ($info && $type > 0 && $type <= 2){
			if ($_POST){
				if ($_POST['infoid'] == $_GET['infoid']){
					$result = $this->model->addQL(intval($_POST['infoid']),$_POST['tikuId'],$type);
				}
				if ($result === true){
					$this->redirect('PriMathema/edit',array('type'=>$type,'infoid'=>$infoid, 'dept'=>$deptName));
				}
			}
			$apiModel = new TiKuApiModel();
			$question = array();
			if (strpos($info['knowledge'],',')){
				$knoges = explode(',', $info['knowledge']);
				foreach ($knoges as $one){
					$k = '';
					$k = json_decode($apiModel->linkTiApi('knodeQues',$one.'/200/1/' . $tkKwId));
					#$question = array_merge($question,$k->list);
                    $kList = $k->list;
                    foreach($kList as $k) {
                        $question[$k->qsn_id] = $k;
                    }
				}
			}else{
				$k = json_decode($apiModel->linkTiApi('knodeQues',$info['knowledge'].'/200/1/' . $tkKwId));
                $kList = $k->list;
                foreach($kList as $k) {
                    $question[$k->qsn_id] = $k;
                }
			}
            ksort($question);
			$this->assign(get_defined_vars());
			$this->display();
		}else{
			$this->error('参数错误');
		}
	}
	/**
	 * 编辑作业
	 * $type 1客观 2主观
	 */
	public function edit(){
        $empDeptArray = $this->getEmpDept();

        $deptName = trim($_GET['dept']);

        if($deptName && isset($empDeptArray[$deptName])) {
            $deptCfg = $empDeptArray[$deptName];
        } else {
            foreach($empDeptArray as $deptName=>$deptCfg) {
                break;
            }
        }

        $isEnglish = preg_match('/english/i', $deptName);

		$infoid = intval($_GET['infoid']);
		$type = intval($_GET['type']) ? intval($_GET['type']) : 1;
		$info = $this->model->getOneRecord('NCS_New_QuestionRollInfo',array('ID'=>$infoid),'1');
		if ($info){
			if ($type == 1){
				$student = $this->model->getOneRecord('NCS_New_Student_AnwerInfo',array('nQuestionRollInfoID'=>$infoid,'Objective'=>1));
				if ($student){
					$this->error('该作业已经有学生在做，不可继续修改');
				}
				$tb = 'NCS_New_QuestionList';
				$w = 'nQuestionRollInfoID';
			}elseif ($type == 2){
				$tb = 'NCS_New_Subjective';
				$w = 'infoid';
			}else{
				$this->error('参数错误');
			}
			$array_questionlist = $this->model->getQuestionList($infoid,$type);
			$apiModel = new TiKuApiModel();
			foreach ($array_questionlist as $k=>$v){
				$r = '';
				$r = json_decode($apiModel->linkTiApi('ques',$v['tikuid']));
				$rel = $r[0];
				$array_questionlist[$k]['contents'] = $rel->qsn_content;
				$alternative = array() ;
				foreach ($rel->answers as $key=>$val){
					if ($type == 1){
						if (!empty($val->qsn_answer_text)){
							$alternative[$val->flag] = $val->qsn_answer_text;
						}
					}else{
						if (!empty($val->qsn_answer)){
							$alternative[$val->flag] = $val->qsn_answer;
						}	
					}
					if ($rel->qsn_type == 1){//单选题需要正确答案
						if ($val->is_answer == 1 && empty($v['answer'])){
							$array_questionlist[$k]['answer'] = $val->flag;
						}
						$array_questionlist[$k]['ischoice'] = 1;
						$array_questionlist[$k]['alternative'] = $alternative;
					}elseif ($rel->qsn_type == 2){//多选题
						if (empty($v['answer'])){
							$tempAnswerArr = '';
							$tmpIndex = 1;
							foreach ($rel->answers_right as $one){
								if ($tmpIndex == 1){
									$tempAnswerArr = $one->flag;
								}else{
									$tempAnswerArr .= ','.$one->flag;
								}
								$tmpIndex++;
							}
							$array_questionlist[$k]['answer'] = $tempAnswerArr;
						}
						$array_questionlist[$k]['ischoice'] = 1;
						$array_questionlist[$k]['alternative'] = $alternative;
					}else{
						if (empty($array_questionlist[$k]['answer'])){
							$array_questionlist[$k]['answer'] = $alternative;
						}
					}
				}
			}
			$this->assign(get_defined_vars());
			$this->display();
		}else{
			$this->error('该作业不存在');
		}
		
	}
	//作业列表
	public function operate(){
		import('ORG.Util.Page');
        $empDeptArray = $this->getEmpDept();

        $deptName = trim($_GET['dept']);
        if($deptName && isset($empDeptArray[$deptName])) {
            $deptCfg = $empDeptArray[$deptName];
        } else {
            foreach($empDeptArray as $deptName=>$deptCfg) {
                break;
            }
        }

		$condition = '';
		$ClassName = trim(!empty($_GET['ClassName'])) ? urldecode(trim($_GET['ClassName'])) : '';
		$nClassYear = trim(!empty($_GET['nClassYear'])) ? trim($_GET['nClassYear']) : '';
		$nSemester = trim(!empty($_GET['nSemester'])) ? trim($_GET['nSemester']) : '';
		$Explain = intval(trim(!empty($_GET['Explain']))) ? intval(trim($_GET['Explain'])) : '';

        $strCtCodes = $this->model->getStrCtCodes($deptCfg['deptCode']);
        $condition .= ' and classtype in (' . $strCtCodes . ')';

		if ($Explain){
			$condition .= " and ExplainNo = ".abs($Explain);
		}
		if (!empty($nClassYear)){
			$condition .= " and nClassYear = ".abs($nClassYear);
		}
		if (!empty($nSemester)){
			$condition .= " and nSemester = ".abs($nSemester);
		}
		if ($ClassName){
			$condition .= " and ClassName = '".$ClassName."'";
			$Explains = '';
			$Explains = $this->model->getExplain(array('ClassName'=>$ClassName),$nClassYear,$nSemester);
		}
		$count = $this->model->countQRI($condition);
		$Page = new Page($count,10);
		$show = $Page->show();
		$varPage = C ( 'VAR_PAGE' ) ? C ( 'VAR_PAGE' ) : 'p';
		$nowPage = ! empty ( $_GET [$varPage] ) ? intval ( $_GET [$varPage] ) : 1;
		$list = $this->model->getQRI($condition,$nowPage,10);
		$weixin = new WeixinHomeworkModel();
		$xueqiArr = $weixin->getXueqi();

        //选定学科所有作业班型
        $AllClass = $this->model->getAllClass(null, $deptCfg['deptCode'], $nClassYear, $nSemester);

		$this->assign(get_defined_vars());
		$this->display('list'); 
	}
	/**
	 * 客观题列表
	 * 已经有学生做题
	 */
	public function objList(){
		$this->checkPermission(ACTION_NAME);
		$infoid = SysUtil::safeString($_GET['infoid']);
		if ($infoid){
			$info = $this->model->getOneRecord('NCS_New_QuestionRollInfo',array('ID'=>$infoid),'classname,explainno');
			$list = $this->model->getQL($infoid);
			$count = $this->model->getAllRecord('NCS_New_Student_AnwerInfo',array('nQuestionRollInfoID'=>$infoid),'1');
			if ($count === false){
				$count = 0; 
			}else{
				$count = count($count);
			}
			$this->assign(get_defined_vars());
			$this->display('subList');
		}else{
			$this->error('参数错误');
		}
	}
	public function editObjtive(){
		$id = $_GET['id'];
		$question = $this->model->getQL($id,'ID');
		$question = $question[0];
		$apiModel = new TiKuApiModel();
		$r = json_decode($apiModel->linkTiApi('ques',$question['tikuid']));
		$alternative = array();
		$rel = $r[0];
		$question['contents'] = $rel->qsn_content;
		foreach ($rel->answers as $key=>$val){
			if (!empty($val->qsn_answer_text)){
				$alternative[$val->flag] = $val->qsn_answer_text;
			}
			if ($rel->qsn_type == 1){//选择题需要正确答案
				if ($val->is_answer == 1 && empty($question['answer'])){
					$question['answer'] = $val->flag;
				}
				$question['ischoice'] = 1;
				$question['alternative'] = $alternative;
			}elseif ($rel->qsn_type == 2){//多选题
						if (empty($question['answer'])){
							$tempAnswerArr = '';
							$tmpIndex = 1;
							foreach ($rel->answers_right as $one){
								if ($tmpIndex == 1){
									$tempAnswerArr = $one->flag;
								}else{
									$tempAnswerArr .= ','.$one->flag;
								}
								$tmpIndex++;
							}
							$question['answer'] = $tempAnswerArr;
						}
						$question['ischoice'] = 1;
						$question['alternative'] = $alternative;
			}else{
				if (empty($question['answer'])){
					$question['answer'] = $alternative;
				}
			}
		}
		if ($question){
			$this->assign('one',$question);
			$this->display();
		}
	}
	/**
	 * 添加口述题
	 */
	public function addNuncupate(){
		$infoid = intval($_GET['infoid']);
		$info = $this->model->getOneRecord('NCS_New_QuestionRollInfo',array('ID'=>$infoid),'ClassName,ExplainNo');
		if ($infoid){
			if ($_POST){
				$qindex = intval($_POST['qindex']);
				$score = floatval($_POST['score']);
				$result = $this->model->addNumcupate($infoid,$qindex,$score);
				if ($result === true){
					$this->redirect('PriMathema/operate');
				}else{
					$this->error('添加失败，请重试');
				}
			}
			$this->assign(get_defined_vars());
			$this->display('nuncupate');
		}else{
			$this->error('参数错误');
		}
	}
	/**
	 * 学生作业列表
	 */
	public function stuWorkList(){
		import('ORG.Util.Page');

        $empDeptArray = $this->getEmpDept();

        $deptName = trim($_GET['dept']);
        if($deptName && isset($empDeptArray[$deptName])) {
            $deptCfg = $empDeptArray[$deptName];
        } else {
            foreach($empDeptArray as $deptName=>$deptCfg) {
                break;
            }
        }

		$condition = '';
		$classTypeCode = !empty($_GET['classTypeCode']) ? trim($_GET['classTypeCode']) : '';
		$nClassYear = !empty($_GET['nClassYear']) ? trim($_GET['nClassYear']) : '';
		$nSemester = !empty($_GET['nSemester']) ? trim($_GET['nSemester']) : '';
		$nLessonNo = !empty($_GET['nLessonNo']) ? intval(trim($_GET['nLessonNo'])) : '';
		$username = !empty($_GET['username']) ? urldecode(trim($_GET['username'])) : '';

        $strCtCodes = $this->model->getStrCtCodes($deptCfg['deptCode']);
        $condition .= ' and a.sclasstypecode in (' . $strCtCodes . ')';

		if ($nLessonNo){
			$condition .= " and a.nLessonNo = ".$nLessonNo;
		}
		if (!empty($nClassYear)){
			$condition .= " and b.nClassYear = ".$nClassYear;
		}
		if (!empty($nSemester)){
			$condition .= " and b.nSemester = ".$nSemester;
		}
		if ($classTypeCode){
			$condition .= " and a.sClassTypeCode = '".$classTypeCode."'";
			$nLessonNos = $this->model->getLessonNo(array('a.sClassTypeCode'=>$classTypeCode),$nClassYear,$nSemester);
		}
		if ($username){
			$condition .= " and c.sName like '%".$username."%'";
		}
		$count = $this->model->countStuList($condition);
		$Page = new Page($count,10);
		$show = $Page->show();
		$varPage = C ( 'VAR_PAGE' ) ? C ( 'VAR_PAGE' ) : 'p';
		$nowPage = ! empty ( $_GET [$varPage] ) ? intval ( $_GET [$varPage] ) : 1;
		$list = $this->model->getStuList($condition,$nowPage);
		$weixin = new WeixinHomeworkModel();
		$xueqiArr = $weixin->getXueqi();

        //选定学科所有作业班型
        $AllClass = $this->model->getAllClass(null, $deptCfg['deptCode'], $nClassYear, $nSemester);

		$this->assign(get_defined_vars());
		$this->display('stuList');
	}
	/**
	 * 编辑学生客观作业
	 */
	public function editStuWork(){
		$id = intval($_GET['infoid']);
		if ($id){
			$studentInfo = $this->model->getStudentInfo(array('a.id'=>$id),'b.sName,a.*');
			if ($_POST){
				if (intval($_POST['id']) > 0 && (!empty($_POST['studentanswer']) || intval($_POST['studentanswer']) === 0)){
					$submit = $this->model->editStuWork($_POST,$studentInfo);
					if ($submit === true){
						$success = 1;
					}
				}
			}
			$stuWork = $this->model->getStuWork($id);
			$this->assign(get_defined_vars());
			$this->display();
		}else{
			$this->error('访问错误');
		}
	}
	//删除学生作业
	public function ajaxDeleteStuWork(){
		$id = intval($_GET['infoid']);
		$type = intval($_GET['type']);
		if ($id){
			$r = $this->model->getOneRecord('NCS_New_Student_AnwerInfo',array('id'=>$id));
			if ($r){
				$result = $this->model->deleteStuWrok($id,$type,$r);
				if ($result){
					echo 1; 
				}
			}
		}
	}
	/**
	 * 编辑题目
	 */
	public function ajaxEditQuestion(){
		if ($_POST){
			if ($_POST['soType'] == 1){//客观
				$r = $this->model->saveQuestion($_POST,1);
				if ($r && !empty($_POST['key']) && $_POST['key'] == md5($_POST['id'])){
					unset($_POST['key']);
					$tmpStr = '';
					foreach ($_POST as $k=>$v){
						$tmpStr .= '/'.$k.'/'.$v;
					}
					$url = C('EDITURL').$tmpStr.'/pwd/wzlajax';
					echo SysUtil::curlRequest($url,'','GET');exit;
				}
			}else{//主观
				$r = $this->model->saveQuestion($_POST,2);
			}
			if ($r === true){
				echo 1;
			}else{
				echo 0;
			}
		}
	}
	/**
	 * 删除整套作业
	 */
	public function ajaxdeleteInfo(){
		if ($_POST){
			$infoid = SysUtil::safeString($_POST['infoid']);
			$r = $this->model->deleteQRI($infoid);
			if ($r === true){
				echo 1;
			}else{
				echo 0;
			}
		}
	}
	/**
	 * 单题删除
	 */
	public function ajaxdeleteList(){
		if ($_POST){
			$id = SysUtil::safeString($_POST['id']);
			$type = SysUtil::safeString($_POST['type']);
			$r = $this->model->deleteList($id,$type);
			if ($r === true){
				echo 1;
			}else{
				echo 0;
			}
		}
	}
	/**
	 * 检查作业是否创建
	 */
	public function ajaxCheckQuestion(){
		/* $_POST = array(
				'ClassNo'=>'二年级快乐思维尖子B班||二年级快乐思维尖子A+班',
				'ExplainNo'=>9,'className'=>'二年级快乐思维尖子班',
				'classType'=>'CTBJ001001016',
				'nClassYear'=>2014,
				'nSemester'=>3
		); */
		if ($_POST){
			if (!empty($_POST['ClassNo'])){
				$_POST['ClassNo'] = explode('||',$_POST['ClassNo']);
			}
			$r = $this->model->checkQuestion($_POST['classType'],$_POST['className'],$_POST['ExplainNo'],$_POST['ClassNo'],$_POST['nClassYear'],$_POST['nSemester']);
			if ($r === true){
				echo 1;
			}else{
				echo 0;
			}
		}
	}
	
	/**
	 * 获取尖子班号
	 */
	public function ajaxGetClassNo(){
		$k = trim($_POST['_k']) ? trim($_POST['_k']) : '';
		$rel = $this->model->getClassNo($k);
		echo json_encode($rel);
	}
	
	/**
	 * 获取讲次
	 */
	public function ajaxGetExplainNo(){
		$k = trim($_POST['_k']) ? trim($_POST['_k']) : '';
		$y = trim($_POST['_y']) ? trim($_POST['_y']) : '';
		$s = trim($_POST['_s']) ? trim($_POST['_s']) : '';
        $d = trim($_POST['_d']) ? trim($_POST['_d']) : '';
        $empDeptArray = $this->getEmpDept();
        if(false == isset($empDeptArray[$d])) {
            echo json_encode(array());
            exit;
        }
        $nxuebu = $empDeptArray[$d]['nxuebu'];
        $nxueke = $empDeptArray[$d]['nxueke'];
		$rel = $this->model->getExplainNo($k,$y,$s, $nxuebu, $nxueke);
		echo json_encode($rel);
        exit;
	}
}
?>