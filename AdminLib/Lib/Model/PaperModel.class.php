<?php
import('COM.Dao.Dao');
class PaperModel {
    public $dao = null;
    private $userKey = '';
    private $operateTime = '';
    protected $tableName = 'ex_exam_papers';
    protected $paperTypeTable = 'ex_paper_types';
    protected $paperPartTable = 'ex_paper_parts';
    protected $paperQuesTable = 'ex_paper_questions';
    protected $quesTable = 'ex_exam_questions';
    protected $quesKnowledgeTable = 'ex_ques_knowledges';
    protected $quesBodyTable = 'ex_ques_bodys';
    protected $scoreTable = 'ex_exam_scores';
    protected $virtualTable = 'ex_exam_virtual';
    
    protected $sortedSubjects = array('math', 'chinese', 'english', 'physic','chemistry');
	protected $sortedPaperTypes = array('real', 'addon');
    
    
    public function __construct() {
        $this->dao = Dao::getDao();
        if (class_exists('User', false)) {
			$operator = User::getLoginUser();
			$this->userKey= $operator->getUserKey();
		}
        $this->operateTime = date('Y-m-d H:i:s');
    }
    
    public function getPaperTypes() {
        $strQuery = 'SELECT type_name,type_caption
                     FROM ' . $this->paperTypeTable . '
                     ORDER BY type_seq';
        $typeList = $this->dao->getAll($strQuery);
        $typeArray = array();
        foreach ($typeList as $type) {
            $typeArray[$type['type_name']] = $type['type_caption'];
        }
        return $typeArray;
    }
    
    private function getSubject($subjectCode) {
		$ar = explode('-', $subjectCode);
		return $ar[1];
	}
    
    public function getPaperCaptions($examId, $paperType='') {
		$strQuery = 'SELECT * FROM ' . $this->tableName . '
					 WHERE exam_id=' . abs($examId);
		if ($paperType) {
			$strQuery .= ' AND paper_type=' . $this->dao->quote($paperType);
		}
		$paperList = $this->dao->getAll($strQuery);
		$paperArray = array();
		foreach ($paperList as $paper) {
			$paperCaption = str_ireplace(array('a','b'), array('', ''), $paper['paper_caption']);
			$subject = $this->getSubject($paper['subject_code']);
			$paperArray[$subject . '_' . $paper['paper_type']] = array('pid'=>$paper['paper_id'], 'subject'=>$subject, 'type'=>$paper['paper_type'], 'caption'=>$paperCaption);
		}
		$sortedPapers = array();
		foreach ($this->sortedSubjects as $subject) {
			foreach ($this->sortedPaperTypes as $paperType) {
				$paperType = $subject . '_' .$paperType;
				if(isset($paperArray[$paperType])) {
					$sortedPapers[$paperType] = $paperArray[$paperType];
				}
			}
		}
		return $sortedPapers;
	}
    
    public function find($paperId) {
    	static $paperArray = array();
    	if(false == isset($paperArray[$paperId])) {
	    	$strQuery = 'SELECT * FROM ' . $this->tableName . '
	    				 WHERE paper_id=' . abs($paperId);
	    	$paperInfo = $this->dao->getRow($strQuery);
	    	$subjectInfo = explode('-', $paperInfo['subject_code']);
	    	$paperInfo['subject'] = $subjectInfo[1];
	    	$strQuery = 'SELECT sum(ques_score) paper_score 
	    				 FROM ' . $this->paperQuesTable . '
	    			     WHERE paper_id=' . abs($paperId) . '
	    			       AND is_remove=0';
	    	$paperInfo['paperScore'] = $this->dao->getOne($strQuery);
	    	$paperArray[$paperId] = $paperInfo;
    	}
    	return $paperArray[$paperId];
    }
    
    /**
     * 实体卷仅允许AB卷两张，附加卷仅一张，虚拟卷可多张,但不允许多张重复设置
     */
    public function ifEnableAdd($paperInfo) {
        if($paperInfo['paper_type'] == 'virtual') {
            return  array('success'=>true);
        }
        $SQL = 'SELECT count(1) FROM ' . $this->tableName . '
                     WHERE is_remove=0
                       AND exam_id=' . $paperInfo['exam_id'] . '
                       AND subject_code=' .  $this->dao->quote($paperInfo['subject_code']) . '
                       AND paper_type=' . $this->dao->quote($paperInfo['paper_type']);
        if($paperInfo['paper_type'] == 'real') {
            $paperInfo['paper_char'] = strtoupper($paperInfo['paper_char']);
            $strQuery = $SQL . ' AND paper_char=' . $this->dao->quote($paperInfo['paper_char']);
        }
        $cnt = $this->dao->getOne($strQuery);
        if($cnt > 0) {
            return array('errorMsg'=>'试卷已经存在，不允许重复添加');
        }
        if($paperInfo['paper_type'] == 'real' && $paperInfo['paper_char'] == 'B') {
            $strQuery = $SQL . ' AND paper_char=' . $this->dao->quote('A');
            $strQuery = str_replace('count(1)', '*', $strQuery);
            $paperInfo = $this->dao->getRow($strQuery);
            if(false == $paperInfo) {
                return array('errorMsg'=>'必须在添加A劵后才可以添加B卷');
            } else {
                $return = array('success'=>true);
                $strQuery = 'SELECT part_id,part_caption,part_ques_score 
                             FROM ' . $this->paperPartTable . ' 
                             WHERE paper_id=' . $paperInfo['paper_id'] . '
                             AND is_remove=0
                             ORDER BY part_id';
                $partList = $this->dao->getAll($strQuery);
                $return['part_list'] = $partList;
                $return['part_count'] = sizeof($partList);
                return $return;
            }
        }
        return array('success'=>true);
        
    }
    
    public function getPaperList($examId) {
        $sbjModel = D('Subject');
        $subjectArray = $sbjModel->getSubjectArray();
        $paperTypes = $this->getPaperTypes();
        $strQuery = 'SELECT *,
                      CASE paper_type WHEN \'real\' THEN 1 
                                      WHEN \'addon\' THEN 2 
                                      WHEN \'virtual\' THEN 3 END type_seq 
                     FROM ' . $this->tableName . '
                     WHERE exam_id=' . abs($examId) . '
                       AND is_remove=0
                     ORDER BY type_seq,paper_char,paper_id';
        $paperList = $this->dao->getAll($strQuery);
        foreach ($paperList as $key=>$paper) {
        	$subjectInfo = explode('-', $paper['subject_code']);
        	unset($paperList[$key]['paper_cfg']);
            $paperList[$key]['paper_score'] = $this->getPaperScore($paper);
            $paperList[$key]['type_caption'] = $paperTypes[$paper['paper_type']];
            $paperList[$key]['subject'] = $subjectInfo[1];
            $paperList[$key]['subject_caption'] = str_replace(array('小学','初中'), array('', ''), $subjectArray[$paper['subject_code']]);
        }
        return $paperList;
    }
    
    public function getPaperScore($paperInfo) {
        if($paperInfo['paper_type'] != 'virtual') {
            $strQuery = 'SELECT sum(ques_score) FROM ' . $this->paperQuesTable . ' 
                         WHERE paper_id=' . $paperInfo['paper_id'] . '
                         AND is_remove=0';
            return $this->dao->getOne($strQuery);
        }
    }
    
    /**
     * B卷时查找已存在的A卷，系统根据对应卷子查询每部分的小题数据
     *
     * @param Integer $examId
     * @param String $subjectCode
     * @param String $paperChar
     * @return Array
     */
    public function findPaperA($examId, $subjectCode, $paperChar) {
        $strQuery = 'SELECT * FROM ' . $this->tableName . '
                     WHERE exam_id=' . $examId . '
                       AND is_remove=0
                       AND paper_type=' . $this->dao->quote('real') . '
                       AND subject_code=' . $this->dao->quote($subjectCode) . '
                       AND paper_char != ' . $this->dao->quote($paperChar);
        $paperInfo = $this->dao->getRow($strQuery);
        return $paperInfo;
    }
    
    public function getSubjectQuestions($examId, $subjectCode) {
        $strQuery = 'SELECT distinct ques_id FROM ' . $this->paperQuesTable . '
                     WHERE exam_id=' . abs($examId) . '
                       AND subject_code=' . $this->dao->quote($subjectCode) . '
                       AND is_remove=0';
        $quesList = $this->dao->getAll($strQuery);
        $quesIdArray = array();
        foreach ($quesList as $ques) {
            $quesIdArray[] = $ques['ques_id'];
        }
        return $quesIdArray;
    }
    
    public function getQuesList($searchArgs) {
        $quesModel = D('Question');
        $quesTypeArray = $quesModel->getQuesTypeArray();
        $quesIdArray = array($this->dao->quote(-1));
        if(isset($searchArgs['quesIds'])) {
            $quesIds = explode(',', $searchArgs['quesIds']);
            foreach ($quesIds as $quesId){
                $quesIdArray[] = $this->dao->quote($quesId);
            }
            $strQuery = 'SELECT ques_id,ques_type,ques_sumary,body_id,ques_level,ques_seq,sub_seq,create_at
                         FROM ' . $this->quesTable . '
                         WHERE ques_id IN (' . implode(',', $quesIdArray) . ')
                         ORDER BY ques_seq,sub_seq,create_at';
            $quesList = $this->dao->getAll($strQuery);
        } else if(isset($searchArgs['paperId']) && isset($searchArgs['partIdx'])) {
            $strQuery = 'SELECT ques.ques_id,ques.ques_type,ques_sumary,ques.ques_id,ques.body_id,
                                pques.ques_score,pques.ques_level,pques.ques_seq,pques.ques_knowledge
                         FROM ' . $this->quesTable . ' ques, ' . $this->paperQuesTable . ' pques
                         WHERE pques.ques_id=ques.ques_id 
                           AND pques.is_remove=0 
                           AND ques.is_remove=0
                           AND pques.paper_id=' . abs($searchArgs['paperId']) . '
                           AND pques.part_id=' . abs($searchArgs['partIdx']) . '
                         ORDER BY pques.ques_seq';
            $quesList = $this->dao->getAll($strQuery);
            foreach ($quesList as $ques) {
                $quesIdArray[] = $this->dao->quote($ques['ques_id']);
            }
        }
        
        $strQuery = 'SELECT * FROM ' . $this->quesKnowledgeTable . '
                     WHERE ques_id IN (' . implode(',', $quesIdArray) . ')
                     ORDER BY ques_id,knowledge_code';
        $knowledgeList = $this->dao->getAll($strQuery);
        $knowledgeArray = array();
        foreach ($knowledgeList as $knowledge) {
        	$knowledgeArray[$knowledge['ques_id']][] = $knowledge;
        }
        $returnList = array();
        $bodyArray = array();
        foreach ($quesList as $ques) {
            $ques['knowledgeArray'] = $knowledgeArray[$ques['ques_id']];
            $ques['quesType_caption'] = $quesTypeArray[$ques['ques_type']];
        	if($ques['body_id']) {
        	    if(false == isset($bodyArray[$ques['body_id']])) {
        	        $bodyArray[$ques['body_id']] = true;
        	        $bodyInfo = $quesModel->findBody($ques['body_id']);
        	        $bodyInfo['ques_id'] = $bodyInfo['body_id'];
        	        $bodyInfo['ques_sumary'] = $bodyInfo['body_title'];
        	        $bodyInfo['state'] = 'open';
        	        $returnList[] = $bodyInfo;
        	    }
        	    $ques['_parentId'] = $ques['body_id'];
        	}
        	$returnList[] = $ques;
        }
        
        return $returnList;
    }
    
    protected function checkPaper(&$paperInfo) {
        $needFields = array('string'=>array('subject_code'=>'所属学科', 'paper_caption'=>'试卷名称', 'paper_type'=>'试卷类型'),
                            'number'=>array('exam_id'=>'竞赛ID'));
        foreach ($needFields as $type=>$fields) {
            if($type == 'string') {
                foreach ($fields as $fieldName=>$fieldMsg) {
                    $paperInfo[$fieldName] = SysUtil::safeString($paperInfo[$fieldName]);
                    if('' == $paperInfo[$fieldName]) {
                        return array('errorMsg'=>$fieldMsg . '必须设置值');
                    }
                }
            } else if($type == 'number') {
                foreach ($fields as $fieldName=>$fieldMsg) {
                	$paperInfo[$fieldName] = abs($paperInfo[$fieldName]);
                	if(false == $paperInfo[$fieldName]) {
                	   return array('errorMsg'=>$fieldMsg . '必须设置值');
                	}
                }
            }
        }
        $paperInfo['paper_type'] = strtolower($paperInfo['paper_type']);
        if($paperInfo['paper_type'] == 'real') {
            $paperInfo['paper_char'] = strtoupper($paperInfo['paper_char']);
            if(false == $paperInfo['paper_char']) {
                return array('errorMsg'=>'实体卷必须设置试卷标识');
            }
        }
        
        $paperType = $paperInfo['paper_type'];
        $strQuery = 'SELECT count(1) FROM ' . $this->tableName . '
                     WHERE is_remove=0
                       AND exam_id=' . $paperInfo['exam_id'] . '
                       AND subject_code=' . $this->dao->quote($paperInfo['subject_code']) . '
                       AND paper_type=' . $this->dao->quote($paperType);
        if ($paperType == 'real') {
            if(false == in_array($paperInfo['paper_char'], array('A', 'B'))) {
                return array('errorMsg'=>'实体卷最多只能包含AB两卷');
            }
            $strQuery .= ' AND paper_char=' . $this->dao->quote($paperInfo['paper_char']);
        }
        
        if($paperType == 'virtual') {
            //todo:虚拟卷可以有多张，判断标准待定
        }
        if($paperInfo['paper_id']) {
            $strQuery .= ' AND paper_id !=' . abs($paperInfo['paper_id']);
        }
        $paperExists = $this->dao->getOne($strQuery) > 0;
        if($paperExists) {
            return array('errorMsg'=>'试卷信息已经存在，请不要重复录入');
        }
        $methodName = 'check' . ucfirst($paperType);
        return $this->$methodName($paperInfo);
    }
    
    private function checkReal(&$paperInfo) {
        if(false == is_array($paperInfo['part_caption'])) {
            return array('errorMsg'=>'非法操作');
        }
        
        #大题名称列表从表单信息读取
        foreach ($paperInfo['part_caption'] as $partIdx=>$partCaption) {
        	$paperInfo['part_caption'][$partIdx] = SysUtil::safeString($partCaption);
        	if(false == $paperInfo['part_caption'][$partIdx]) {
        	   return array('errorMsg'=>'大题信息不完整，大题名称必须输入');
        	}
        	if(false == is_array($paperInfo['quesNumber'][$partIdx]) 
        	  || ($paperInfo['paper_char'] == 'A' 
        	      && (false == is_array($paperInfo['quesScore'][$partIdx])
        	          || false == is_array($paperInfo['quesLevel'][$partIdx])))) {
        	   return array('errorMsg'=>'大题明细设置不完整');
        	}
        	if(false == $paperInfo['quesKnowledge'][$partIdx]) {
        	   $paperInfo['quesKnowledge'][$partIdx] = array();
        	}
        }
        #小题题号从表单获取
        foreach ($paperInfo['quesNumber'] as $partIdx=>$questions) {
            foreach ($questions as $quesId=>$quesNum) {
                $paperInfo['quesNumber'][$partIdx][$quesId] = abs($quesNum);
                if($paperInfo['quesNumber'][$partIdx][$quesId] == 0){
                    return array('errorMsg'=>'小题题号设置不能为零');
                }
            }
        }
        
        if($paperInfo['paper_char'] == 'A') {
            #A卷等级，分数，知识点信息从表单获取
            foreach ($paperInfo['quesLevel'] as $partIdx=>$questions) {
                foreach ($questions as $quesId=>$quesLevel) {
                    $paperInfo['quesLevel'][$partIdx][$quesId] = abs($quesLevel);
                    if($paperInfo['quesLevel'][$partIdx][$quesId] < 1 || $paperInfo['quesLevel'][$partIdx][$quesId] > 5){
                        return array('errorMsg'=>'请正确设置小题难易级别');
                    }
                }
            }
            
            $paperInfo['paper_score'] = 0;
            foreach ($paperInfo['quesScore'] as $partIdx=>$questions) {
                $paperInfo['part_score'][$partIdx] = 0;
                foreach ($questions as $quesId=>$quesScore) {
                    $paperInfo['quesScore'][$partIdx][$quesId] = abs($quesScore);
                    if($paperInfo['quesScore'][$partIdx][$quesId] == 0) {
                        return array('errorMsg'=>'请正确设置小题分值');
                    }
                    $paperInfo['part_score'][$partIdx] += $paperInfo['quesScore'][$partIdx][$quesId];
                    $paperInfo['paper_score'] += $paperInfo['quesScore'][$partIdx][$quesId];
                }
            }
        } else {
            #B卷等级，分数，知识点信息从A卷继承试题信息
            $strQuery = 'SELECT * FROM ' . $this->paperQuesTable . '
                         WHERE paper_id IN (
                            SELECT paper_id FROM ' . $this->tableName . ' 
                            WHERE exam_id=' . $paperInfo['exam_id'] . '
                              AND subject_code=' . $this->dao->quote($paperInfo['subject_code']) . '
                              AND paper_type=' . $this->dao->quote('real') . '
                              AND paper_char=' . $this->dao->quote('A') . '
                              AND is_remove=0
                           )';
            $quesList = $this->dao->getAll($strQuery);
            $paperInfo['quesScore'] = $paperInfo['quesLevel'] = $paperInfo['quesKnowledge'] = array();
            $paperInfo['paper_score'] = 0;
            $paperInfo['part_score'] = array();
            foreach ($quesList as $ques) {
                $paperInfo['quesScore'][$ques['part_id']][$ques['ques_id']] = $ques['ques_score'];
                $paperInfo['part_score'][$ques['part_id']] += $ques['ques_score'];
                $paperInfo['paper_score'] += $ques['ques_score'];
                $paperInfo['quesLevel'][$ques['part_id']][$ques['ques_id']] = $ques['ques_level'];
                $paperInfo['quesKnowledge'][$ques['part_id']][$ques['ques_id']] = $ques['ques_knowledge'];
            }
        }
        $paperInfo['paper_cfg'] = '';
        return true;
    }
    
    private function checkVirtual(&$paperInfo) {
        
    }
    
    private function checkAddon(&$paperInfo) {
        if(false == $paperInfo['quesKnowledge'][1]) {
    	   $paperInfo['quesKnowledge'][1] = array();
    	}
        foreach ($paperInfo['quesNumber'] as $partIdx=>$questions) {
            foreach ($questions as $quesId=>$quesNum) {
                $paperInfo['quesNumber'][$partIdx][$quesId] = abs($quesNum);
                if($paperInfo['quesNumber'][$partIdx][$quesId] == 0){
                    return array('errorMsg'=>'小题题号设置不能为零');
                }
            }
        }
        
        foreach ($paperInfo['quesLevel'] as $partIdx=>$questions) {
            foreach ($questions as $quesId=>$quesLevel) {
                $paperInfo['quesLevel'][$partIdx][$quesId] = abs($quesLevel);
                if($paperInfo['quesLevel'][$partIdx][$quesId] < 1 || $paperInfo['quesLevel'][$partIdx][$quesId] > 5){
                    return array('errorMsg'=>'请正确设置小题难易级别');
                }
            }
        }
        
        $paperInfo['paper_score'] = 0;
        foreach ($paperInfo['quesScore'] as $partIdx=>$questions) {
            foreach ($questions as $quesId=>$quesScore) {
                $paperInfo['quesScore'][$partIdx][$quesId] = abs($quesScore);
                if($paperInfo['quesScore'][$partIdx][$quesId] == 0) {
                    return array('errorMsg'=>'请正确设置小题分值');
                }
                $paperInfo['paper_score'] += $paperInfo['quesScore'][$partIdx][$quesId];
            }
        }
        $paperInfo['paper_cfg'] = '';
    }
    
    public function save($paperInfo) {
        $this->dao->begin();
        $checkResult = $this->checkPaper($paperInfo);
        if(is_array($checkResult)) {
            return $checkResult;
        }
        $method = 'save' . ucfirst($paperInfo['paper_type']);
        $result = $this->$method($paperInfo);
        if($result['errorMsg']) {
            $this->dao->rollback();
        }
        $this->dao->commit();
        return $result;
    }
    
    private function saveReal($paperInfo) {
        $strQuery = 'INSERT INTO ' . $this->tableName . '
                     (exam_id,subject_code,paper_type,paper_char,paper_caption,
                      paper_cfg,create_user,create_at,update_user,update_at)
                     VALUES (' . $paperInfo['exam_id'] . ',
                             ' . $this->dao->quote($paperInfo['subject_code']) . ',
                             ' . $this->dao->quote($paperInfo['paper_type']) . ',
                             ' . $this->dao->quote($paperInfo['paper_char']) . ',
                             ' . $this->dao->quote($paperInfo['paper_caption']) . ',
                             ' . $this->dao->quote($paperInfo['paper_cfg']) . ',
                             ' . $this->dao->quote($this->userKey) . ',
                             ' . $this->dao->quote($this->operateTime) . ',
                             ' . $this->dao->quote($this->userKey) . ',
                             ' . $this->dao->quote($this->operateTime) . ')';
        if($this->dao->execute($strQuery)) {
            $paperInfo['paper_id'] = $this->dao->lastInsertId();
            $this->setPartDetail($paperInfo);
            return array('success'=>true);
        }
        return array('errorMsg'=>'试卷保存失败，请检查试卷设置');
    }
    
    private function saveVirtual($paperInfo) {
        
    }
    
    private function saveAddon($paperInfo) {
        return $this->saveReal($paperInfo);
    }
    
    private function setPartDetail($paperInfo) {
        #清空试卷大题信息
        if($paperInfo['paper_type'] == 'real') {
            $strQuery = 'UPDATE ' . $this->paperPartTable . '
                         SET is_remove=' . time() . ',
                             update_user=' . $this->dao->quote($this->userKey) . ',
                             update_at=' . $this->dao->quote($this->operateTime) . '
                         WHERE paper_id=' . abs($paperInfo['paper_id']);
            $this->dao->execute($strQuery);
        }
        #清空试卷小题信息
        $strQuery = 'UPDATE ' . $this->paperQuesTable . '
                     SET is_remove=' . time() . ',
                         update_user=' . $this->dao->quote($this->userKey) . ',
                         update_at=' . $this->dao->quote($this->operateTime) . '
                     WHERE paper_id=' . abs($paperInfo['paper_id']);
        $this->dao->execute($strQuery);
        
        if($paperInfo['paper_type'] == 'real') {
            foreach ($paperInfo['part_caption'] as $partIdx=>$partCaption) {
                $strQuery = 'INSERT INTO ' . $this->paperPartTable . '
                             (exam_id, subject_code, paper_id,part_id,part_caption,part_ques_score, create_at,create_user, update_at,update_user)
                             VALUES (' . abs($paperInfo['exam_id']) . ',
                                     ' . $this->dao->quote($paperInfo['subject_code']) . ', 
                                     ' . abs($paperInfo['paper_id']) . ', 
                                     ' . abs($partIdx) . ', 
                                     ' . $this->dao->quote($partCaption) . ', 
                                     ' . abs($paperInfo['part_ques_score'][$partIdx]) . ',
                                     ' . $this->dao->quote($this->operateTime) . ', 
                                     ' . $this->dao->quote($this->userKey) . ',
                                     ' . $this->dao->quote($this->operateTime) . ', 
                                     ' . $this->dao->quote($this->userKey) . ')';
                $this->dao->execute($strQuery);
            }
        }
        
        foreach ($paperInfo['quesScore'] as $partIdx=>$questions) {
            foreach ($questions as $quesId=>$quesScore) {
                $strQuery = 'INSERT INTO ' . $this->paperQuesTable . '
                             (exam_id,subject_code,paper_id,part_id,ques_id,ques_seq,ques_level,ques_score,
                              ques_knowledge,create_at,create_user,update_at,update_user)
                             VALUES (' . abs($paperInfo['exam_id']) . ',
                                     ' . $this->dao->quote($paperInfo['subject_code']) . ',
                                     ' . abs($paperInfo['paper_id']) . ',
                                     ' . abs($partIdx) . ',
                                     ' . $this->dao->quote(trim($quesId)) . ',
                                     ' . abs($paperInfo['quesNumber'][$partIdx][$quesId]) . ',
                                     ' . abs($paperInfo['quesLevel'][$partIdx][$quesId]) . ',
                                     ' . abs($quesScore) . ',
                                     ' . $this->dao->quote($paperInfo['quesKnowledge'][$partIdx][$quesId]) . ',
                                     ' . $this->dao->quote($this->operateTime) . ',
                                     ' . $this->dao->quote($this->userKey) . ',
                                     ' . $this->dao->quote($this->operateTime) . ',
                                     ' . $this->dao->quote($this->userKey) . ')';
                $this->dao->execute($strQuery);
            }
        }
    }
    
    public function getPaperQuestion($paperId) {
        $paperId = abs($paperId);
        $strQuery = 'SELECT * FROM ' . $this->tableName . '
                     WHERE paper_id=' . $paperId;
        $paperInfo = $this->dao->getRow($strQuery);
        $quesModel = D('Question');
        $quesTypeArray = $quesModel->getQuesTypeArray();
        $strQuery = 'SELECT * FROM ' . $this->quesKnowledgeTable . '
                     WHERE ques_id IN (
                        SELECT ques_id FROM ' . $this->paperQuesTable . '
                        WHERE is_remove=0 
                          AND paper_id=' . $paperId . '
                     )';
        $knowledgeList = $this->dao->getAll($strQuery);
        $knowledgeArray = array();
        foreach ($knowledgeList as $knowledge) {
            $knowledgeArray[$knowledge['ques_id']][$knowledge['knowledge_code']] = $knowledge;
        }
        
        $strQuery = 'SELECT pques.*,ques.body_id,ques.ques_sumary,ques.ques_type
                     FROM ' . $this->paperQuesTable . ' pques,
                     ' . $this->quesTable . ' ques
                     WHERE ques.ques_id=pques.ques_id 
                       AND pques.paper_id=' . $paperId . '
                       AND pques.is_remove=0
                     ORDER BY pques.part_id,pques.ques_seq';
        $quesList = $this->dao->getAll($strQuery);
        $partArray = array();
        $bodyArray = array();
        $returnList = array(array('ques_id'=>'paper_' . $paperId, 'paper_id'=>$paperId, 'is_paper'=>true, 'ques_sumary'=>$paperInfo['paper_caption']));
        $partCaptions = array('', '一', '二', '三', '四', '五', '六', '七', '八', '九', '十');
        foreach ($quesList as $ques) {
            if(false == $partArray[$ques['part_id']] && $paperInfo['paper_type'] == 'real') {
                $partArray[$ques['part_id']] = true;
                $strQuery = 'SELECT part_id ques_id,part_id,part_caption
                             FROM ' . $this->paperPartTable . '
                             WHERE paper_id=' . $paperId . '
                               AND is_remove=0
                               AND part_id=' . abs($ques['part_id']);
                $partInfo = $this->dao->getRow($strQuery);
                $partInfo['paper_id'] = $paperId;
                $partInfo['state'] = 'open';
                $partInfo['is_part'] = true;
                $partInfo['_parentId'] = 'paper_' . $paperId;
                $partInfo['part_prefix'] = '第' . $partCaptions[$ques['part_id']] . '大题：';
                $partInfo['ques_sumary'] = $partInfo['part_caption'];
                $returnList[] = $partInfo;
            }
            $ques['_parentId'] = $paperInfo['paper_type'] == 'real' ? $ques['part_id'] : 'paper_' . $paperId;
            $ques['quesType_caption'] = $quesTypeArray[$ques['ques_type']];
            $ques['knowledge_array'] = $knowledgeArray[$ques['ques_id']];
            if($ques['body_id']) {
                if(false == $bodyArray[$ques['body_id']]) {
                    $bodyArray[$ques['body_id']] = true;
        	        $bodyInfo = $quesModel->findBody($ques['body_id']);
        	        $bodyInfo['ques_id'] = $bodyInfo['body_id'];
        	        $bodyInfo['ques_sumary'] = $bodyInfo['body_title'];
        	        $bodyInfo['state'] = 'open';
        	        $bodyInfo['_parentId'] = $paperInfo['paper_type'] == 'real' ? $ques['part_id'] : 'paper_' . $paperId;
        	        $returnList[] = $bodyInfo;
                }
                $ques['_parentId'] = $ques['body_id'];
            }
            $returnList[] = $ques;
        }
        return $returnList;
    }
    
    public function delete($examId, $paperId, $paperType) {
        $this->dao->begin();
        $strQuery = 'SELECT * FROM ' . $this->tableName . '
                     WHERE exam_id=' . abs($examId) . ' 
                       AND paper_id=' . abs($paperId) . '
                       AND is_remove=0';
        $paperInfo = $this->dao->getRow($strQuery);
        if(false == $paperInfo) {
            $this->dao->rollback();
            return array('errorMsg'=>'非法操作');
        }
        if($paperType == 'real' && $paperInfo['paper_char'] == 'A') {
            $strQuery = 'SELECT paper_id FROM ' . $this->tableName . '
                            WHERE exam_id=' . abs($examId) . '
                              AND subject_code=' . $this->dao->quote($paperInfo['subject_code']) . '
                              AND paper_type=' . $this->dao->quote('real') . '
                              AND is_remove=0';
            $paperList = $this->dao->getAll($strQuery);
            $paperIds = array(-1);
            foreach ($paperList as $paper) {
                $paperIds[] = $paper['paper_id'];
            }
            $condition .= 'paper_id IN (' . implode(',', $paperIds) . ')';
        } else {
            $condition = 'paper_id=' . abs($paperId);
        }
        $strQuery = 'UPDATE ' . $this->paperQuesTable . '
                     SET is_remove=' . time() . ',
                         update_user=' . $this->dao->quote($this->userKey) . ',
                         update_at=' . $this->dao->quote($this->operateTime) . '
                     WHERE ' . $condition;
        if(false == $this->dao->execute($strQuery)) {
            $this->dao->rollback();
            return array('errorMsg'=>'试卷删除失败');
        }
        $strQuery = 'UPDATE ' . $this->paperPartTable . '
                     SET is_remove=' . time() . ',
                         update_user=' . $this->dao->quote($this->userKey) . ',
                         update_at=' . $this->dao->quote($this->operateTime) . '
                     WHERE ' . $condition;
        if(false == $this->dao->execute($strQuery)) {
            $this->dao->rollback();
            return array('errorMsg'=>'试卷删除失败');
        }
        $strQuery = 'UPDATE ' . $this->tableName . '
                     SET is_remove=' . time() . ',
                         update_user=' . $this->dao->quote($this->userKey) . ',
                         update_at=' . $this->dao->quote($this->operateTime) . '
                     WHERE ' . $condition;
        if(false == $this->dao->execute($strQuery)) {
            $this->dao->rollback();
            return array('errorMsg'=>'试卷删除失败');
        }
        $this->dao->commit();
        return array('success'=>true);
    }
    
    public function modify($type, $data) {
        $type = strtolower($type);
        $data = $data[$type];
        set_time_limit(0);
        $this->dao->begin();
        foreach ($data as $paperId=>$questions) {break;}
        if(false == $paperId) {
            return array('errorMsg'=>'非法操作');
        }
        $strQuery = 'SELECT * FROM ' . $this->tableName . '
                     WHERE paper_id=' . $paperId . '
                       AND is_remove=0';
        $paperInfo = $this->dao->getRow($strQuery);
        if(false == $paperInfo) {
            return array('errorMsg'=>'非法操作');
        }
        if($type == 'seq' || $paperInfo['paper_type'] != 'real') {
            $condition = 'paper_id=' . $paperInfo['paper_id'];
        } else {
            $condition = 'paper_id IN (
                SELECT paper_id FROM ' . $this->tableName . '
                WHERE exam_id=' . abs($paperInfo['exam_id']) . '
                  AND subject_code=' . $this->dao->quote($paperInfo['subject_code']) . '
                  AND paper_type=' . $this->dao->quote('real') . '
                  AND is_remove=0
            )';
        }
        if(false == $questions) {
            $questions = array();
        }
        $SQL = 'UPDATE ' . $this->paperQuesTable . '
                     SET update_user=' . $this->dao->quote($this->userKey) . ',
                         update_at=' . $this->dao->quote($this->operateTime);
        $columns = array('seq'=>'ques_seq', 'score'=>'ques_score', 'level'=>'ques_level', 'knowledge'=>'ques_knowledge');
        foreach ($questions as $quesId =>$val) {
            if($type == 'knowledge') {
                $val = $this->dao->quote($val);
            } else {
                $val = abs($val);
            }
            $strQuery = $SQL . ',' . $columns[$type] . '=' . $val;
            $strQuery .= ' WHERE ' . $condition . ' 
                              AND ques_id=' . $this->dao->quote($quesId) . '
                              AND ' . $columns[$type] . '!=' . $val;
            if(false == $this->dao->execute($strQuery)) {
                $this->dao->rollback();
                return array('errorMsg'=>'试题信息更新失败');
            }
        }
        $this->dao->commit();
        return array('success'=>true);
    }
    
    public function modifyCaption($type, $data) {
        $type = strtolower($type);
        $data = $data[$type];
        if($type == 'paper_caption') {
            foreach ($data as $paperId=>$paperCaption) {break;}
            $strQuery = 'UPDATE ' . $this->tableName . ' 
                         SET update_user=' . $this->dao->quote($this->userKey) . ',
                             update_at=' . $this->dao->quote($this->operateTime) . ',
                             paper_caption=' . $this->dao->quote($paperCaption) . '
                         WHERE paper_id=' . abs($paperId) . '
                           AND paper_caption !=' . $this->dao->quote($paperCaption);
            if(false == $this->dao->execute($strQuery)) {
                return array('errorMsg'=>'试卷标题修改失败');
            }
        } else if ($type == 'part_caption') {
            $this->dao->begin();
            foreach ($data as $paperId=>$parts) {
                foreach ($parts as $partId=>$partCaption) {
                    $strQuery = 'UPDATE ' . $this->paperPartTable . '
                                 SET update_user=' . $this->dao->quote($this->userKey) . ',
                                     update_at=' . $this->dao->quote($this->operateTime) . ',
                                     part_caption=' . $this->dao->quote($partCaption) . '
                                 WHERE paper_id=' . abs($paperId) . '
                                   AND part_id=' . abs($partId) . '
                                   AND part_caption !=' . $this->dao->quote($partCaption);
                    if (false == $this->dao->execute($strQuery)) {
                        $this->dao->rollback();
                        return array('errorMsg'=>'大题标题修改失败');
                    }
                }
            }
            $this->dao->commit();
        }
        return array('success'=>true);
    }
    
    public function getScorePapers($examId, $subjectCode) {
    	#查询所有实体试卷
        $strQuery = 'SELECT paper_id,paper_char,paper_type,paper_caption 
                     FROM ' . $this->tableName . '
                     WHERE paper_type !=' . $this->dao->quote('virtual') . '
                       AND exam_id=' . abs($examId) . '
                       AND subject_code = ' . $this->dao->quote($subjectCode) . '
                       AND is_remove=0
                     ORDER BY paper_type DESC,paper_char,paper_id';
        $paperList = $this->dao->getAll($strQuery);
        $paperArray = array();
        $pidArray = array();
        
        foreach ($paperList as $paper) {
            $paperChar = $paper['paper_char'] == 'B' ? 'B' : 'A';
            #构造试卷数组，附加卷按A卷处理
            $paperArray[$paper['paper_type']][$paperChar] = $paper;
            $pidArray[] = $paper['paper_id'];
        }
        if(false == $pidArray) {
            return $paperArray;
        }
        $pids = implode(',', $pidArray);
        #构造大题信息数组,附加卷不含大题信息
        $strQuery = 'SELECT paper_id,part_id,part_caption 
                     FROM ' . $this->paperPartTable . '
                     WHERE paper_id IN (' . $pids . ')
                       AND is_remove=0
                     ORDER BY paper_id,part_id';
        $partList = $this->dao->getAll($strQuery);
        $partArray = array();
        foreach ($partList as $part) {
            $partArray[$part['paper_id']][$part['part_id']] = $part;
        }
        
        #构造试题信息数组，并创建试题于知识点对应关系
        $strQuery = 'SELECT * FROM ' . $this->paperQuesTable . '
                     WHERE paper_id IN (' . $pids . ')
                       AND is_remove=0
                     ORDER BY paper_id,part_id,ques_seq';
        $quesList = $this->dao->getAll($strQuery);
        
        $quesArray = array();
        $quesIdArray = array($this->dao->quote('-1'));
        $quesKnowledge = array();
        foreach ($quesList as $ques) {
            $quesArray[$ques['paper_id']][$ques['part_id']][$ques['ques_id']] = $ques;
            $quesIdArray[] = $this->dao->quote($ques['ques_id']);
            if($ques['ques_knowledge']) {
            	$quesKnowledge[$ques['ques_id']] = $ques['ques_knowledge'];
            }
        }
        #构造知识点信息数组
        $strQuery = 'SELECT distinct knowledge_code, knowledge_caption FROM ' . $this->quesKnowledgeTable . ' 
        			 WHERE ques_id IN (' . implode(',', $quesIdArray) . ')';
        $knowledgeList = $this->dao->getAll($strQuery);
        $knowledgeArray = array();
        foreach ($knowledgeList as $knowledge) {
        	$knowledgeArray[$knowledge['knowledge_code']] = $knowledge['knowledge_caption'];
        }
        
        #构造试题详细信息数组
        $strQuery = 'SELECT ques_id,ques_type,ques_sumary,ques_answer,ques_answer_items
                     FROM ' . $this->quesTable . '
                     WHERE ques_id IN (' . implode(',', $quesIdArray) . ')';
        $quesList = $this->dao->getAll($strQuery);
        $quesDetails = array();
        foreach ($quesList as $ques) {
            $quesDetails[$ques['ques_id']] = $ques;
            $quesDetails[$ques['ques_id']]['knowledge_caption'] = $knowledgeArray[$quesKnowledge[$ques['ques_id']]];
        }
        
        #重组试卷信息数组
        foreach ($paperArray as $paperType=>$papers) {
        	foreach ($papers as $paperChar=>$paper) {
        		if($paperType == 'addon') {
        	   		$partArray[$paper['paper_id']] = array(1=>array());
        	   	}
        	   	foreach ($partArray[$paper['paper_id']] as $partId=>$part) {
        	       $paper['parts'][$partId] = $part;
        	       foreach ($quesArray[$paper['paper_id']][$partId] as $quesId=>$ques) {
        	           $ques = array_merge($ques, $quesDetails[$ques['ques_id']]);
        	           if (in_array($ques['ques_type'], array('tiankong', 'duoxuan'))) {
        	               $ques['ques_answer'] = SysUtil::jsonDecode($ques['ques_answer']);
        	           }
        	           $ques['ques_answer_items'] = SysUtil::jsonDecode($ques['ques_answer_items']);
        	           $paper['parts'][$partId]['questions'][$quesId] = $ques;
        	       }
        	       $paperArray[$paperType][$paperChar] = $paper;
        	   	}
        	}
        }
        return $paperArray;
    }
    
    public function getVTypeCaption($examId, $virtualType) {
    	static $papeArray = array();
    	$subjectModel = D('Subject');
    	$subjectNames = $subjectModel->getSubjectNames();
    	$paperTypeCaptions = array('real'=>'卷', 'addon'=>'附加卷');
    	if($virtualType == 'total') {
    		return '竞赛总成绩';
    	}
    	if(false == preg_match('/virtual/i', $virtualType)) {
    		list($subject,$paperType) = preg_split('/[^a-z]/', $virtualType);
    		return $subjectNames[$subject] . $paperTypeCaptions[$paperType];
    	} else {
	    	if(false == isset($paperArray[$examId])) {
	    		$paperArray[$examId] = $this->getPaperList($examId);
	    	}
    	}
    }
    
    public function getPaperQuestions($paperInfo) {
    	$strQuery = 'SELECT * FROM ' . $this->paperQuesTable . '
    				 WHERE paper_id=' . abs($paperInfo['paper_id']) . '
    				   AND is_remove=0
    				 ORDER BY part_id,ques_seq';
    	$quesList = $this->dao->getAll($strQuery);
    	$quesArray = array();
    	foreach ($quesList as $ques) {
    		$quesArray['real'][$ques['part_id']][] = $ques;
    	}
    	$strQuery = 'SELECT * FROM ' . $this->paperQuesTable . '
    				 WHERE paper_id IN (
    				 	SELECT paper_id FROM ' . $this->tableName . '
    				 	WHERE exam_id=' . abs($paperInfo['exam_id']) . '
    				 	  AND paper_type=' . $this->dao->quote('addon') . '
    				 	  AND subject_code=' . $this->dao->quote($paperInfo['subject_code']) . '
    				 	  AND is_remove=0
    				 	)
    				   AND is_remove=0
    				 ORDER BY ques_seq';
    	$quesList = $this->dao->getAll($strQuery);
    	foreach ($quesList as $ques) {
    		$quesArray['addon'][$ques['part_id']][] = $ques;
    	}
    	return $quesArray;
    }
    
    /**
     * 竞赛试卷满分和以及单科试卷满分成绩
     */
    public function getPaperScores($examId) {
    	$strQuery = 'SELECT subject_code,SUM(ques_score) paper_score
					 FROM ' . $this->paperQuesTable . '
					 WHERE paper_id IN (
						SELECT paper_id 
						FROM ex_exam_papers 
						WHERE exam_id=' . abs($examId) . '
						  AND paper_type=' . $this->dao->quote('real') . '
						  AND paper_char=' . $this->dao->quote('A') . '
					)
					GROUP BY subject_code';
    	$paperScoreList = $this->dao->getAll($strQuery);
    	$scoreArray = array('total'=>0);
    	foreach ($paperScoreList as $paper) {
    		$subject = preg_replace('/^\d+\-/', '', $paper['subject_code']);
    		$scoreArray[$subject] = $paper['paper_score'];
    		$scoreArray['total'] += $paper['paper_score'];
    	}
    	return $scoreArray;
    }
    
    public function getStuPaper($examId, $stuCode, $subject) {
    	$scoreModel = D('Score');
    	$stuScore = $scoreModel->getRealPaperScore($examId, $stuCode, $subject);
    	$paperId = $stuScore['paper_id'];
    	return $this->find($paperId);
    }
    
    public function getPaperExamCount($examId, $subject) {
    	import('ORG.Util.NCache');
    	$cache = NCache::getCache();
    	$key = 'paperExamCount_' . $examId . '_' . $subject;
    	$paperExamCount = $cache->get('PaperExamCount', $key);
    	if(null === $paperExamCount) {
	    	$strQuery = 'SELECT count(distinct stu_code) exam_count 
	    				 FROM ' . $this->scoreTable . '
	    				 WHERE exam_id=' . abs($examId) . '
	    				   AND paper_subject=' . $this->dao->quote($subject);
	    	$examCount = $this->dao->getOne($strQuery);
	    	$strQuery = 'SELECT sum(score_cnt) virtual_cnt 
	    				 FROM ' . $this->virtualTable . '
	    				 WHERE exam_id=' . abs($examId) . '
	    				   AND virtual_type=' . $this->dao->quote($subject . '_real');
	    	$virtualCount = $this->dao->getOne($strQuery);
	    	$paperExamCount = $examCount + $virtualCount;
	    	$cache->set('PaperExamCount', $key, $paperExamCount);
    	}
    	return $paperExamCount;
    }
    
    public function getStepModuleRatios($paperInfo) {
    	import('ORG.Util.NCache');
    	$cache = NCache::getCache();
    	$nameSpace = 'StepModuleRatio';
    	$key = 'smRatio_' . $paperInfo['exam_id'] . '_' . $paperInfo['subject'];
    	$stepModuleRatios = $cache->get($nameSpace, $key);
    	if(false == $stepModuleRatios) {
	    	$questions = $this->getPaperQuestions($paperInfo);
	    	$knowledgeIdArray = array($this->dao->quote('0'));
	    	$knowledgeScoreArray = array();
	    	$stepModuleRatios = array();
	    	$knowledgeStepArray = array();
	    	$stepArray = array(1=>1, 2=>1, 3=>2, 4=>2, 5=>3, 6=>3);
	    	foreach ($questions as $paperParts){
	    		foreach ($paperParts as $partId=>$questions) {
	    			foreach ($questions as $ques) {
	    				$step = $stepArray[$ques['ques_level']];
	    				$knowledgeIdArray[] = $this->dao->quote($ques['ques_knowledge']);
	    				if(false == isset($knowledgeScoreArray[$ques['ques_knowledge']])) {
	    					$knowledgeScoreArray[$ques['ques_knowledge']] = 0;
	    				}
	    				if(false == isset($stepModuleRatios[$step])) {
	    					$stepModuleRatios[$step]['totalScore'] = 0;
	    					$stepModuleRatios[$step]['quesCount'] = 0;
	    					$stepModuleRatios[$step]['modules'] = array();
	    				}
	    				$stepModuleRatios[$step]['totalScore'] += $ques['ques_score'];
	    				$stepModuleRatios[$step]['quesCount'] += 1;
	    				$knowledgeScoreArray[$ques['ques_knowledge']] += $ques['ques_score'];
	    				$knowledgeStepArray[md5($ques['ques_knowledge'] . '_' . $ques['ques_id'])] = $step;
	    			}
	    		}
	    	}
	    	
	    	$strQuery = 'SELECT * FROM ' . $this->quesKnowledgeTable . '
	    				 WHERE knowledge_code IN (' . implode(',', $knowledgeIdArray) . ')';
	    	$knowledgeList = $this->dao->getAll($strQuery);
	    	
	    	foreach ($knowledgeList as $knowledge) {
	    		$step = $knowledgeStepArray[md5($knowledge['knowledge_code'] . '_' . $knowledge['ques_id'])];
	    		if($step) {
		    		if(false == isset($stepModuleRatios[$step]['modules'][$knowledge['module_code']])) {
		    			$stepModuleRatios[$step]['modules'][$knowledge['module_code']]['caption'] = $knowledge['module_caption'];
		    			$stepModuleRatios[$step]['modules'][$knowledge['module_code']]['moduleScore'] = 0;
		    			$stepModuleRatios[$step]['modules'][$knowledge['module_code']]['quesCount'] = 0;
		    		}
		    		$stepModuleRatios[$step]['modules'][$knowledge['module_code']]['moduleScore'] += $knowledgeScoreArray[$knowledge['knowledge_code']];
		    		$stepModuleRatios[$step]['modules'][$knowledge['module_code']]['quesCount'] += 1;
	    		}
	    	}
	    	foreach ($stepModuleRatios as $step=>$stepCfg) {
	    		foreach ($stepCfg['modules'] as $moduleCode=>$moduleCfg) {
	    			$stepModuleRatios[$step]['modules'][$moduleCode]['scoreRatio'] = sprintf('%.2f', $moduleCfg['moduleScore'] / $stepCfg['totalScore'] * 100);
	    			$stepModuleRatios[$step]['modules'][$moduleCode]['countRatio'] = sprintf('%.2f', $moduleCfg['quesCount'] / $stepCfg['quesCount'] * 100);
	    		}
	    	}
	    	
	    	$cache->set($nameSpace, $key, $stepModuleRatios);
    	}
    	return $stepModuleRatios;
    }
}
?>