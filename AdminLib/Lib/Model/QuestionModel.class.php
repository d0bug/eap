<?php
class QuestionModel {
    public $dao = null;
    public $operator = null;
    private $tableName = 'ex_exam_questions';
    private $quesKnowledgeTable = 'ex_ques_knowledges';
    private $quesTypeTable = 'ex_ques_type';
    private $quesBodyTable = 'ex_ques_bodys';
    
    public function __construct() {
        $this->dao = Dao::getDao();
        if(class_exists('User', false)) {
        	$this->operator = User::getLoginUser();
        }
    }
    
    public function getQuestionCountBySubject($examId=0) {
        $strQuery = 'SELECT subject_code,COUNT(ques_id) cnt
                     FROM ' . $this->tableName . '
                     WHERE is_remove=0 ';
        if($examId) {
            $strQuery .= ' AND exam_id=' . abs($examId);
        }
        $strQuery .= ' GROUP BY subject_code';
        $sbjCntList = $this->dao->getAll($strQuery);
        $countArray = array();
        foreach ($sbjCntList as $sbj) {
            $countArray[$sbj['subject_code']] = $sbj['cnt'];
        }
        return $countArray;
    }
    
    public function getQuesTypeArray() {
        $strQuery = 'SELECT * FROM ' . $this->quesTypeTable;
        $quesTypeList = $this->dao->getAll($strQuery);
        $quesTypeArray = array();
        foreach ($quesTypeList as $quesType) {
            $quesTypeArray[$quesType['id']] = $quesType['caption'];
        }
        return $quesTypeArray;
    }
    
    public function getQuesBodyArray($examId, $subjectCode) {
        $strQuery = 'SELECT * FROM ' . $this->quesBodyTable . '
                     WHERE exam_id=' . abs($examId) . '
                       AND subject_code=' . $this->dao->quote($subjectCode);
        $bodyList = $this->dao->getAll($strQuery);
        $bodyArray = array();
        foreach ($bodyList as $body) {
            $bodyArray[$body['body_id']] = $body['body_title'];
        }
        return $bodyArray;
    }
    
    private function checkQuestion(&$quesInfo) {
        $quesInfo['exam_id'] = abs($quesInfo['exam_id']);
        $quesInfo['ques_type'] = SysUtil::safeString($quesInfo['ques_type']);
        $quesInfo['subject_code'] = SysUtil::safeString($quesInfo['subject_code']);
        $quesInfo['ques_sumary'] = SysUtil::safeString($quesInfo['ques_sumary']);
        if(false == $quesInfo['exam_id'] || false == $quesInfo['subject_code'] || false == $quesInfo['ques_type'] || false == $quesInfo['ques_sumary']) {
            return array('errorMsg'=>'数据不完整，请联系管理员');
        }
        
        $quesInfo['ques_content'] = SysUtil::safeString($quesInfo['ques_content']);
        $quesInfo['ques_analy_text'] = SysUtil::safeString($quesInfo['ques_analy_text']);
        
        if($quesInfo['ques_knowledge']) {
            $knowledgeCodes = array();
            foreach ($quesInfo['ques_knowledge'] as $key=>$knowledgeCode) {
                unset($quesInfo['ques_knowledge'][$key]);
                $knowledgeCodes[$knowledgeCode] = $this->dao->quote($knowledgeCode);
                $quesInfo['ques_knowledge'][$knowledgeCode] = array();
            }
            $knowledgeModel = D('Knowledge');
            $knowledgeList = $knowledgeModel->getList('knowledge_code IN (' . implode(',', $knowledgeCodes) . ')');
            foreach ($knowledgeList as $row) {
                $quesInfo['ques_knowledge'][$row['knowledge_code']] = array('module_code'=>$row['module_code'], 'module_caption'=>$row['module_caption'], 'knowledge_code'=>$row['knowledge_code'], 'knowledge_caption'=>$row['knowledge_caption'], 'study_code'=>$row['study_code']);
            }
        } else {
            $quesInfo['ques_knowledge'] = array();
        }
        
        $quesInfo['body_id'] = SysUtil::safeString($quesInfo['body_id']);
        if(strlen($quesInfo['body_id'])<10) {
            $quesInfo['body_id'] = '0';
        }
        
        $methodName = 'check' . ucfirst($quesInfo['ques_type']);
        $checkResult = $this->$methodName($quesInfo);
        if($checkResult['errorMsg']) {
            return $checkResult;
        }
        $strQuery = 'SELECT count(1) FROM ' . $this->tableName . '
                     WHERE is_remove=0
                       AND exam_id=' . $quesInfo['exam_id'] . '
                       AND subject_code=' . $this->dao->quote($quesInfo['subject_code']) . '
                       AND ques_sumary=' . $this->dao->quote($quesInfo['ques_sumary']);
        if($quesInfo['ques_id']) {
            $quesInfo['ques_id'] = SysUtil::uuid($quesInfo['ques_id']);
            $strQuery .= ' AND ques_id !=' . $this->dao->quote($quesInfo['ques_id']);
        }
        if($this->dao->getOne($strQuery) > 0) {
            return array('errorMsg'=>'试题已经存在，请不要重复添加');
        }
        return true;
    }
    
    private function checkDanxuan(&$quesInfo) {
        $quesAnswer = trim($quesInfo['ques_answer']);
        $quesInfo['ques_answer'] = strtoupper($quesAnswer[0]);
        $quesInfo['item_num'] = abs($quesInfo['item_num']);
        foreach ($quesInfo['quesAns'] as $key=>$val) {
            $quesInfo['quesAns'][strtoupper($key)] = SysUtil::safeString($val);
        }
        foreach ($quesInfo['ansAnaly'] as $key=>$val) {
            $quesInfo['ansAnaly'][strtoupper($key)] = SysUtil::safeString($val);
        }
        $quesInfo['quesAns'] = SysUtil::jsonEncode($quesInfo['quesAns']);
        unset($quesInfo['ansAnaly'][$quesInfo['ques_answer']]);
        $quesInfo['ansAnaly'] = SysUtil::jsonEncode($quesInfo['ansAnaly']);
    }
    
    private function checkDuoxuan(&$quesInfo) {
        
    }
    
    private function checkPanduan(&$quesInfo) {
    
    } 
    
    private function checkJieda(&$quesInfo) {
        $quesInfo['ques_answer'] = SysUtil::safeString($quesInfo['ques_answer']);
        if(false == $quesInfo['ques_answer']) $quesInfo['ques_answer'] = '&nbsp;';
        $quesInfo['quesAns'] = '';
        $quesInfo['ansAnaly'] = SysUtil::safeString($quesInfo['ques_analy']);
    }
    
    private function checkTiankong(&$quesInfo) {
        $quesInfo['ques_answer'] = $quesInfo['ques_answer_items'];
        foreach ($quesInfo['ques_answer'] as $blankIdx=>$ansItems) {
            foreach ($ansItems as $key=>$val) {
                $quesInfo['ques_answer'][$blankIdx][$key] = SysUtil::safeString($val);
            }
        }
        
        if(false == $quesInfo['ques_wrong_items']) {
            $quesInfo['ques_wrong_items'] = array();
        }
        if(false == $quesInfo['ques_analy_items']) {
            $quesInfo['ques_analy_items'] = array();
        }
        $quesInfo['ansAnaly'] = array();
        foreach ($quesInfo['ques_answer'] as $blankIdx=>$null) {
            $items = $quesInfo['ques_wrong_items'][$blankIdx];
            $analys = $quesInfo['ques_analy_items'][$blankIdx];
            if(false == $items) $items = array();
            if(false == $analys) $analys = array();
            if($items) {
                foreach ($items as $key=>$val) {
                    $quesInfo['ansAnaly'][$blankIdx][$key] = array('item'=>SysUtil::safeString($val), 'analy'=>SysUtil::safeString($analys[$key]));
                }
            } else {
                $quesInfo['ansAnaly'][$blankIdx] = array();
            }
        }
        $quesInfo['quesAns'] = SysUtil::jsonEncode($quesInfo['quesAns']);
        $quesInfo['ansAnaly'] = SysUtil::jsonEncode($quesInfo['ansAnaly']);
        $quesInfo['ques_answer'] = SysUtil::jsonEncode($quesInfo['ques_answer']);
        $quesInfo['quesAns'] = $quesInfo['ansAnaly'];
    }
    
    
    public function save($quesInfo) {
        $checkResult = $this->checkQuestion($quesInfo);
        if($checkResult['errorMsg']) {
            return $checkResult;
        }
        $time = date('Y-m-d H:i:s');
        $userKey = $this->operator->getUserKey();
        if($quesInfo['ques_id']) {
            $errorMsg = '试题信息修改失败';
            $strQuery = 'UPDATE ' . $this->tableName . '
                         SET body_id=' . $this->dao->quote($quesInfo['body_id']) . ',
                             ques_sumary=' . $this->dao->quote($quesInfo['ques_sumary']) . ',
                             ques_content=' . $this->dao->quote($quesInfo['ques_content']) . ',
                             ques_answer=' . $this->dao->quote($quesInfo['ques_answer']) . ',
                             ques_answer_items=' . $this->dao->quote($quesInfo['quesAns']) . ',
                             ques_analy_items=' . $this->dao->quote($quesInfo['ansAnaly']) . ',
                             ques_analy_text=' . $this->dao->quote($quesInfo['ques_analy_text']) . ',
                             ques_level=' . abs($quesInfo['ques_level']) . ',
                             is_remove=0,
                             update_user=' . $this->dao->quote($userKey) . ',
                             update_at=' . $this->dao->quote($time) . '
                          WHERE ques_id=' . $this->dao->quote($quesInfo['ques_id']);
        } else {
            $errorMsg = '试题信息添加失败';
            $quesInfo['ques_id'] = SysUtil::uuid();
            $strQuery = 'INSERT INTO ' . $this->tableName . '
                        (ques_id,exam_id,subject_code,ques_type,body_id,ques_sumary,ques_content,ques_answer,ques_answer_items, ques_analy_items,ques_analy_text,ques_level,is_remove,update_user,create_at,update_at)
                        VALUES (
                            ' . $this->dao->quote($quesInfo['ques_id']) . ',
                            ' . abs($quesInfo['exam_id']) . ',
                            ' . $this->dao->quote($quesInfo['subject_code']) . ',
                            ' . $this->dao->quote($quesInfo['ques_type']) . ',
                            ' . $this->dao->quote($quesInfo['body_id']) . ',
                            ' . $this->dao->quote($quesInfo['ques_sumary']) . ',
                            ' . $this->dao->quote($quesInfo['ques_content']) . ',
                            ' . $this->dao->quote($quesInfo['ques_answer']) . ',
                            ' . $this->dao->quote($quesInfo['quesAns']) . ',
                            ' . $this->dao->quote($quesInfo['ansAnaly']) . ',
                            ' . $this->dao->quote($quesInfo['ques_analy_text']) . ',
                            ' . abs($quesInfo['ques_level']) . ',0,
                            ' . $this->dao->quote($userKey) . ',
                            ' . $this->dao->quote($time) . ',
                            ' . $this->dao->quote($time) . ')';
        }
        if($this->dao->execute($strQuery)) {
            if($quesInfo['ques_knowledge']) {
                $this->saveQuesKnowledge($quesInfo);
            }
            return array('success'=>true);
        }
        return array('errorMsg'=>$errorMsg);
    }
    
    public function delQuestion($quesId) {
    	$strQuery = 'UPDATE ' . $this->tableName . '
    				 SET is_remove=' . time() . ',
    				 	 update_user=' . $this->dao->quote($this->operator->getUserKey()) . ',
    				 	 update_at=' . $this->dao->quote(date('Y-m-d H:i:s')) .'
    				 WHERE ques_id=' . $this->dao->quote($quesId) ;
    	$this->dao->execute($strQuery);
    	return true;
    }
    
    private function saveQuesKnowledge($quesInfo) {
        $strQuery = 'DELETE FROM ' . $this->quesKnowledgeTable . ' 
                     WHERE ques_id=' . $this->dao->quote($quesInfo['ques_id']);
        $this->dao->execute($strQuery);
        foreach ($quesInfo['ques_knowledge'] as $knowledge) {
            $strQuery = 'INSERT INTO ' . $this->quesKnowledgeTable . '
                         (module_code,module_caption, knowledge_code, knowledge_caption, study_code, ques_id)
                         VALUES (
                            ' . $this->dao->quote($knowledge['module_code']) . ',
                            ' . $this->dao->quote($knowledge['module_caption']) . ',
                            ' . $this->dao->quote($knowledge['knowledge_code']) . ',
                            ' . $this->dao->quote($knowledge['knowledge_caption']) . ',
                            ' . $this->dao->quote($knowledge['study_code']) . ',
                            ' . $this->dao->quote($quesInfo['ques_id']) . ')';
            $this->dao->execute($strQuery);
        }
    }
    
    
    private function checkBody(&$bodyInfo) {
        $bodyInfo['exam_id'] = abs($bodyInfo['exam_id']);
        $bodyInfo['subject_code'] = SysUtil::safeString($bodyInfo['subject_code']);
        $bodyInfo['body_title'] = SysUtil::safeString($bodyInfo['body_title']);
        $bodyInfo['body_content'] = SysUtil::safeString($bodyInfo['body_content']);
        foreach ($bodyInfo as $key=>$val) {
            if(false == $val) {
                return array('errorMsg'=>'大题题干信息不全');
            }
        }
        $strQuery = 'SELECT COUNT(1) FROM ' . $this->quesBodyTable . '
                     WHERE exam_id=' . $bodyInfo['exam_id'] . '
                       AND subject_code=' . $this->dao->quote($bodyInfo['subject_code']) . '
                       AND body_title=' . $this->dao->quote($bodyInfo['body_title']);
        if($bodyInfo['body_id']) {
            $strQuery .= ' AND body_id != ' . $this->dao->quote($bodyInfo['body_id']);
        }
        
        if($this->dao->getOne($strQuery) > 0) {
            return array('errorMsg'=>'相同大题题干已存在，请不要重复添加');
        }
        return true;
    }
    
    public function saveBody($bodyInfo) {
        $checkResult = $this->checkBody($bodyInfo);
        if(is_array($checkResult)) return $checkResult;
        $time = date('Y-m-d H:i:s');
        $userKey = $this->operator->getUserKey();
        if($bodyInfo['body_id']) {
            $strQuery = 'UPDATE ' . $this->quesBodyTable . '
                         SET body_title=' . $this->dao->quote($bodyInfo['body_title']) . ',
                             body_content=' . $this->dao->quote($bodyInfo['body_content']) . ',
                             update_user=' . $this->dao->quote($userKey) . ',
                             update_at=' . $this->dao->quote($time) . '
                         WHERE body_id=' . $this->dao->quote($bodyInfo['body_id']);
        } else {
            $strQuery = 'INSERT INTO ' . $this->quesBodyTable . '
                         (exam_id,subject_code,body_title,body_content,update_user,update_at)
                         VALUES (
                         ' . abs($bodyInfo['exam_id']) . ',
                         ' . $this->dao->quote($bodyInfo['subject_code']) . ',
                         ' . $this->dao->quote($bodyInfo['body_title']) . ',
                         ' . $this->dao->quote($bodyInfo['body_content']) . ',
                         ' . $this->dao->quote($userKey) . ',
                         ' . $this->dao->quote($time) . ')';
        }
        if(false == $this->dao->execute($strQuery)) {
            return array('errorMsg'=>'大题题干保存失败，请联系管理员');
        }
        return array('success'=>true);
    }
    
    public function findBody($bodyId) {
        $strQuery = 'SELECT * FROM ' . $this->quesBodyTable . '
                     WHERE body_id=' . $this->dao->quote($bodyId);
        $bodyInfo = $this->dao->getRow($strQuery);
        $bodyInfo['body_content'] = str_replace('<x>', '', $bodyInfo['body_content']);
        return $bodyInfo;
    }
    
    public function getQuesList($searchArgs, $excludes='') {
        $subjectModel = D('Subject');
        $quesTypeArray = $this->getQuesTypeArray();
        $subjectArray = $subjectModel->getSubjectArray();
        $excludeArray = array($this->dao->quote(-1));
        
        if($excludes) {
            $quesIdArray = explode(',', $excludes);
            foreach ($quesIdArray as $quesId) {
                $excludeArray[] = $this->dao->quote($quesId);
            }
        }
        $condition = 'is_remove=0 
                      AND exam_id=' . abs($searchArgs['examId']) . '
                      AND ques_id NOT IN (' . implode(',', $excludeArray) . ')';
        if($searchArgs['subject']) {
            $condition .= ' AND subject_code=' . $this->dao->quote($searchArgs['subject']);
        }
        
        $strQuery = 'SELECT * FROM ' . $this->tableName . '
                     WHERE ' . $condition;
        $order = ' ORDER BY subject_code,ques_seq,sub_seq,create_at';
        if(false == $searchArgs['subject']) {
            $strQuery = 'SELECT * FROM (' . $strQuery . ') quesview ';
            $quesList = $this->dao->getLimit($strQuery, 1, 50, $order);
        } else {
            $strQuery .= ' ' . $order;
            $quesList = $this->dao->getAll($strQuery);
        }
        
        $bodyArray = array();
        $idArray = array($this->dao->quote('-1'));
        foreach ($quesList as $key=>$ques) {
        	$idArray[] = $this->dao->quote($ques['ques_id']);
            if($ques['body_id']) {
                if(false == isset($bodyArray[$ques['body_id']])) {
                    $bodyInfo = $this->findBody($ques['body_id']);
                    $bodyInfo['ques_sumary'] = $bodyInfo['body_title'];
                    $bodyInfo['ques_id'] = $bodyInfo['body_id'];
                    $bodyInfo['subject_caption'] = $subjectArray[$bodyInfo['subject_code']];
                    $bodyInfo['ques_seq'] = $ques['ques_seq'];
                    $bodyInfo['state'] = 'open';
                    $quesList[$key] = $bodyInfo;
                    $bodyArray[$ques['body_id']] = $this->dao->quote($ques['body_id']);
                } else {
                    unset($quesList[$key]);
                }
            } else {
                $quesList[$key]['subject_caption'] = $subjectArray[$ques['subject_code']];
                $quesList[$key]['quesType_caption'] = $quesTypeArray[$ques['ques_type']];
            }
        }
        $strQuery = 'SELECT * FROM ' . $this->quesKnowledgeTable . '
        			 WHERE ques_id IN (' . implode(',', $idArray) . ')
        			 ORDER BY ques_id,knowledge_code';
        $knowledgeList = $this->dao->getAll($strQuery);
        foreach ($knowledgeList as $knowledge) {
        	$knowledgeArray[$knowledge['ques_id']] = '[' . $knowledge['module_caption'] . ']' . $knowledge['knowledge_caption'];
        }
        if($searchArgs['subject']) {
            $quesList = array_values($quesList);
            foreach ($quesList as $key=>$ques) {
                $quesList[$key]['ques_seq'] = $key + 1;
            }
        }
        
        if($bodyArray) {
            $strQuery = 'SELECT * FROM ' . $this->tableName . '
                         WHERE is_remove=0 
                           AND body_id IN (' . implode(',', $bodyArray) . ')
                           AND ques_id NOT IN (' . implode(',', $excludeArray) . ')
                         ORDER BY body_id,sub_seq,create_at';
            $subList = $this->dao->getAll($strQuery);
            $bodyId = '';
            foreach ($subList as $key=>$ques) {
                if($ques['body_id'] != $bodyId) {
                    $bodyId = $ques['body_id'];
                    $seq = 1;
                } else {
                    $seq ++;
                }
                $ques['subject_caption'] = $subjectArray[$ques['subject_code']];
                $ques['quesType_caption'] = $quesTypeArray[$ques['ques_type']];
                $ques['ques_seq'] = $seq;
                $ques['_parentId'] = $ques['body_id'];
                $quesList[] = $ques;
            }
        }
        foreach ($quesList as $key=>$ques) {
        	$quesList[$key]['knowledge_caption'] = $knowledgeArray[$ques['ques_id']];
        }
        
        return array_values($quesList);
    }
    
    public function find($quesId) {
        $strQuery = 'SELECT * FROM ' . $this->tableName . '
                     WHERE ques_id=' . $this->dao->quote($quesId);
        $quesInfo = $this->dao->getRow($strQuery);
        switch ($quesInfo['ques_type']) {
            case 'danxuan':
                $quesInfo['ques_answer_items'] = SysUtil::jsonDecode($quesInfo['ques_answer_items']);
                $quesInfo['ques_analy_items'] = SysUtil::jsonDecode($quesInfo['ques_analy_items']);
                $quesInfo['itemNum'] = sizeof($quesInfo['ques_answer_items']);
            break;
            case 'tiankong':
                $quesInfo['ques_answer'] = SysUtil::jsonDecode($quesInfo['ques_answer']);
                $quesInfo['ques_answer_items'] = SysUtil::jsonDecode($quesInfo['ques_answer_items']);
                $quesInfo['ques_analy_items'] = SysUtil::jsonDecode($quesInfo['ques_analy_items']);
                $quesInfo['blankCnt'] = sizeof($quesInfo['ques_answer']);
            break;
            case 'jieda':
                $quesInfo['ques_analy'] = $quesInfo['ques_analy_items'];
            break;    
        }
        
        $strQuery = 'SELECT * FROM ' . $this->quesKnowledgeTable . '
                     WHERE ques_id=' . $this->dao->quote($quesId);
        $knowledgeList = $this->dao->getAll($strQuery);
        $knowledgeArray = array();
        foreach ($knowledgeList as $knowledge) {
        	$knowledgeArray[$knowledge['knowledge_code']] = $knowledge;
        }
        $quesInfo['ques_knowledge'] = $knowledgeArray;
        return $quesInfo;
    }
}
?>