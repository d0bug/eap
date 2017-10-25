<?php
class KnowledgeModel {
    public $dao = null;
    private $tableName = '';
    private $operator = null;
    private $moduleTable = '';
    
    public function __construct(){
        $this->dao = Dao::getDao();
        $this->tableName = 'ex_knowledges';
        $this->moduleTable = 'ex_modules';
        $this->operator = User::getLoginUser();
    }
    
    public function checkModule(&$moduleInfo) {
        $moduleInfo['module_subject'] = SysUtil::safeString($moduleInfo['module_subject']);
        $moduleInfo['module_caption'] = SysUtil::safeString($moduleInfo['module_caption']);
        $moduleInfo['module_code'] = preg_replace('/\s/', '', strtoupper(SysUtil::safeString($moduleInfo['module_code'])));
        if(false == $moduleInfo['module_subject'] || false == $moduleInfo['module_caption'] || false == $moduleInfo['module_code']) {
            return array('errorMsg'=>'知识模块信息不完整');
        }
        $strQuery = 'SELECT count(1) FROM ' . $this->moduleTable . '
                     WHERE (module_caption=' . $this->dao->quote($moduleInfo['module_caption']) . ' 
                         OR module_code=' . $this->dao->quote( $moduleInfo['module_code']) . ')
                        AND module_subject=' . $this->dao->quote($moduleInfo['module_subject']) . '
                        AND is_removed=0';
        if($moduleInfo['module_id']) {
            $moduleInfo['module_id'] = SysUtil::uuid($moduleInfo['module_id']);
            $strQuery .= ' AND module_id !=' . $this->dao->quote($moduleInfo['module_id']);
        }
        $exists = $this->dao->getOne($strQuery) > 0;
        if($exists) {
            return array('errorMsg'=>'选定学科存在同名模块');
        }
        return true;
    }
    
    public function saveModule($moduleInfo) {
        $checkResult = $this->checkModule($moduleInfo);
        if(is_array($checkResult)) return $checkResult;
        $userKey = $this->operator->getUserKey();
        $time = Date('Y-m-d H:i:s');
        if($moduleInfo['module_id']) {
            $strQuery = 'UPDATE ' . $this->moduleTable . '
                         SET update_user=' . $this->dao->quote($userKey) . ',
                             update_at=' . $this->dao->quote($time  ) . ',
                             module_caption=' . $this->dao->quote($moduleInfo['module_caption']) . ',
                             module_code=' . $this->dao->quote($moduleInfo['module_code']) . '
                         WHERE module_id=' . $this->dao->quote($moduleInfo['module_id']);
        } else {
            $strQuery = 'INSERT INTO ' . $this->moduleTable . '
                         (module_subject,module_caption,module_code,update_user,update_at)
                         VALUES(
                            ' . $this->dao->quote($moduleInfo['module_subject']) . ',
                            ' . $this->dao->quote($moduleInfo['module_caption']) . ',
                            ' . $this->dao->quote($moduleInfo['module_code']) . ',
                            ' . $this->dao->quote($userKey) . ',
                            ' . $this->dao->quote($time) . ')';
        }
        if(false == $this->dao->execute($strQuery)) {
            if($moduleInfo['module_id']) {
                return array('errorMsg'=>'模块信息修改失败');
            } else {
                return array('errorMsg'=>'模块信息添加失败');
            }
        }
        return true;
    }
    
    public function findModule($moduleId, $isCode=false) {
        $strQuery = 'SELECT * FROM ' . $this->moduleTable . '
                     WHERE 1=1 ';
        if($isCode) {
            $strQuery .= 'AND module_code=' . $this->dao->quote($moduleId);
        } else {
            $strQuery .= 'AND module_id=' . $this->dao->quote($moduleId);
        }
        $moduleInfo = $this->dao->getRow($strQuery);
        $subjectModel = D('Subject');
        $subjectArray = $subjectModel->getSubjectArray();
        $moduleInfo['subject_caption'] = $subjectArray[$moduleInfo['module_subject']];
        return $moduleInfo;
    }
    
    public function getModuleList($condition='', $currentPage=1, $pageSize=20) {
        $recordCount = $this->countModule($condition);
        if(false == $condition) $condition = '1=1';
        $pageCount = ceil($recordCount / $pageSize);
        if($currentPage > $pageCount) $currentPage = $pageCount;
        if($currentPage < 1) $currentPage = 1;
        
        $strQuery = 'SELECT * FROM ' . $this->moduleTable . '
                     WHERE ' . $condition;
        $order = ' ORDER BY module_code';
        $strQuery .= $order;
        $moduleList = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
        return $moduleList;
    }
    
    public function countModule($condition='') {
        if(false == $condition) {
            $condition = '1=1';
        }
        $countKey = md5(serialize($condition));
        static $countArray = array();
        if(false == isset($countArray[$countKey])) {
            $strQuery = 'SELECT count(1) 
                         FROM ' . $this->moduleTable . '
                         WHERE ' . $condition;
            $countArray[$countKey] = $this->dao->getOne($strQuery);
        }
        return $countArray[$countKey];
    }
    
    public function countKnowledge($condition='') {
        if(false == $condition) {
            $condition = '1=1';
        }
        $strQuery = 'SELECT count(1) 
                     FROM ' . $this->tableName . ' knowledge,
                          ' . $this->moduleTable . ' module
                     WHERE module.module_code=knowledge.module_code 
                       AND ' . $condition;
        return $this->dao->getOne($strQuery);
    }
    
    public function getKnowledgeView($condition = '1=1') {
        $strQuery = '(SELECT knowledge.*, sub.sub_cnt FROM 
                     (
                        SELECT knowledge.*,module.module_subject,module.module_caption
                        FROM ' . $this->tableName . ' knowledge,
                             ' . $this->moduleTable . ' module
                        WHERE knowledge.module_code=module.module_code
                        AND knowledge.is_remove=0
                        AND ' . $condition . '
                      ) knowledge,
                      (
                      SELECT par.knowledge_code,count(sub.knowledge_code) sub_cnt
                      FROM ' . $this->tableName  . ' par
                      LEFT JOIN ' . $this->tableName . ' sub
                        ON par.knowledge_code=sub.parent_code
                        AND sub.is_remove=0
                      WHERE par.is_remove=0
                      GROUP BY par.knowledge_code) sub
                      WHERE knowledge.knowledge_code=sub.knowledge_code
                      ) knowledge';
        return $strQuery;
    }
    
    public function getList($condition) {
        if(false == $condition) $condition = '1=1';
        $knowledgeView = $this->getKnowledgeView($condition);
        $strQuery = 'SELECT * FROM ' . $knowledgeView . '
                     WHERE ' . $condition . '
                     ORDER BY knowledge_code';
        $knowledgeList = $this->dao->getAll($strQuery);
        if($knowledgeList) {
            $subjectModel = D('Subject');
            $subjectArray = $subjectModel->getSubjectArray();
            foreach ($knowledgeList as $key=>$knowledge) {
                $knowledgeList[$key]['subject_caption'] = $subjectArray[$knowledge['module_subject']];
            }
        }
        return $knowledgeList;
    }
    
    public function checkInfo(&$knowledgeInfo) {
        $fields = array('module_code', 'knowledge_code', 'knowledge_caption');
        foreach ($fields as $fieldName) {
            if(false == $knowledgeInfo[$fieldName]) {
                return array('errorMsg'=>'知识点信息不完整');
            } else {
                $knowledgeInfo[$fieldName] = SysUtil::safeString($knowledgeInfo[$fieldName]);
            }
        }
        $knowledgeInfo['knowledge_code'] = preg_replace('/\s/', '', $knowledgeInfo['knowledge_code']);
        $knowledgeInfo['knowledge_code'] = strtoupper($knowledgeInfo['knowledge_code']);
        if(false == $knowledgeInfo['parent_code']) {
            $knowledgeInfo['parent_code'] = 0;
        } else {
            $knowledgeInfo['parent_code'] = SysUtil::safeString($knowledgeInfo['parent_code']);
        }
        
        $strQuery = 'SELECT count(1) FROM ' . $this->tableName . '
                     WHERE is_remove=0 
                       AND (knowledge_code=' . $this->dao->quote($knowledgeInfo['knowledge_code']) . '
                        OR (
                             knowledge_caption=' . $this->dao->quote($knowledgeInfo['knowledge_caption']) . '
                             AND parent_code=' . $this->dao->quote($knowledgeInfo['parent_code']) . '
                             AND module_code=' . $this->dao->quote($knowledgeInfo['module_code']) . '
                            )
                       )';
        if($knowledgeInfo['knowledge_id']) {
            $knowledgeInfo['knowledge_id'] = SysUtil::uuid($knowledgeInfo['knowledge_id']);
            $strQuery .= ' AND knowledge_id !=' . $this->dao->quote($knowledgeInfo['knowledge_id']);
        }
        $isExists = $this->dao->getOne($strQuery) > 0;
        if($isExists) {
            return array('errorMsg'=>'知识点信息已存在或知识点编码冲突，请检查');
        }
        return true;
    }
    
    public function save($knowledgeInfo) {
        $checkResult = $this->checkInfo($knowledgeInfo);
        if(is_array($checkResult)) {
            return $checkResult;
        }
        $userKey = $this->operator->getUserKey();
        $time = date('Y-m-d H:i:s');
        if($knowledgeInfo['knowledge_id']) {
            $strQuery = 'UPDATE ' . $this->tableName . '
                         SET knowledge_code=' . $this->dao->quote($knowledgeInfo['knowledge_code']) . ',
                             knowledge_caption=' . $this->dao->quote($knowledgeInfo['knowledge_caption']) . ',
                             module_code=' . $this->dao->quote($knowledgeInfo['module_code']) . ',
                             parent_code=' . $this->dao->quote($knowledgeInfo['parent_code']) . ',
                             study_code=' . $this->dao->quote($knowledgeInfo['study_code']) . ',
                             update_user=' . $this->dao->quote($userKey) . ',
                             update_at=' . $this->dao->quote($time) . '
                         WHERE knowledge_id=' . $this->dao->quote($knowledgeInfo['knowledge_id']);
        } else {
            $strQuery = 'INSERT INTO ' . $this->tableName . '
                         (knowledge_code,knowledge_caption,module_code,parent_code,study_code,update_user,update_at)
                         VALUES (' . $this->dao->quote($knowledgeInfo['knowledge_code']) . ',
                                 ' . $this->dao->quote($knowledgeInfo['knowledge_caption']) . ',
                                 ' . $this->dao->quote($knowledgeInfo['module_code']) . ',
                                 ' . $this->dao->quote($knowledgeInfo['parent_code']) . ',
                                 ' . $this->dao->quote($knowledgeInfo['study_code']) . ',
                                 ' . $this->dao->quote($userKey) . ',
                                 ' . $this->dao->quote($time) . ')';
        }
        if(false == $this->dao->execute($strQuery)) {
            return array('errorMsg'=>'知识点信息保存失败');
        }
        return true;
    }
    
    public function find($knowledgeCode) {
        $strQuery = 'SELECT knowledge.*,module.module_subject,module.module_caption 
                     FROM ' . $this->tableName . ' knowledge,
                          ' . $this->moduleTable . ' module
                     WHERE knowledge.module_code=module.module_code
                       AND knowledge.knowledge_code=' . $this->dao->quote($knowledgeCode);
        $knowledgeInfo = $this->dao->getRow($strQuery);
        $subjectModel = D('Subject');
        $subjectArray = $subjectModel->getSubjectArray();
        $knowledgeInfo['subject_caption'] = $subjectArray[$knowledgeInfo['module_subject']];
        $strQuery = 'SELECT count(1) FROM ' . $this->tableName . '
                     WHERE parent_code=' . $this->dao->quote($knowledgeCode);
        $knowledgeInfo['sub_cnt'] = $this->dao->getOne($strQuery);
        return $knowledgeInfo;
    }
    
    public function delete($knowledgeId) {
        $time = time();
        $dTime = date('Y-m-d H:i:s');
        $userKey = $this->operator->getUserKey();
        $strQuery = 'UPDATE ' . $this->tableName . '
                     SET is_remove=' . $time . ',
                         update_user=' . $this->dao->quote($userKey) . ',
                         update_at=' . $this ->dao->quote($dTime) . '
                     WHERE knowledge_id=' . $this->dao->quote($knowledgeId);
        return $this->dao->execute($strQuery);
    }
    
    
}
?>