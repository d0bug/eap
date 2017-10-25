<?php
class ExamGroupModel {
    private $tableName = '';
    private $examTable = '';
    public $dao = null;
    public $operator = null;
    
    public function __construct() {
        $this->tableName = 'ex_exam_groups';
        $this->examTable = 'ex_exams';
        $this->dao = Dao::getDao();
        $this->operator  = User::getLoginUser(C('USER_COOKIE_NAME'));
    }
    
    public function getGroupCount($condition='') {
        $countArray = array();
        if(false == $condition) $condition = '1=1';
        $key = md5(serialize($condition));
        if(false == isset($countArray[$key])) {
            $strQuery = 'SELECT count(1) FROM ex_exam_groups
                         WHERE is_remove=0 AND group_id IN (
                           SELECT distinct group_id FROM ' . $this->tableName . '
                           WHERE is_remove=0
                         ) AND ' . $condition;
            $groupCount = $this->dao->getOne($strQuery);
            $countArray[$key] = $groupCount;
        }
        return $countArray[$key];
    }
    
    public function getGroupList($condition='', $currentPage=1, $pageSize=20, $sort = array()) {
        if(false == $condition) $condition = '1=1';
        $groupCount = $this->getGroupCount($condition);
        $pageCount = ceil($groupCount / $pageSize);
        if($currentPage > $pageCount) $currentPage = $pageCount;
        if($currentPage < 1) $currentPage = 1;
        $strQuery = 'SELECT g.*,exam.cnt,exam.max_exam 
                     FROM ' . $this->tableName . ' g, 
                     (
                        SELECT g.group_id,
                               max(ex.exam_id) max_exam, 
                               count(ex.exam_id) cnt
                        FROM ' . $this->tableName . ' g
                        LEFT JOIN ' . $this->examTable . ' ex
                          ON ex.group_id=g.group_id AND ex.is_remove=0
                        GROUP BY g.group_id
                      ) exam
                      WHERE g.is_remove=0 
                        AND g.group_id=exam.group_id 
                        AND ' . $condition;
        if($sort) {
            $order = 'ORDER BY ' . $sort['sort'] . ' ' . $sort['order'];
        } else {
            $order = 'ORDER BY max_exam DESC,group_type ASC';
        }
        $order .= ',group_id DESC';
        
        $strQuery .= ' ' . $order;
        $groupList = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
        return $groupList;
    }
    
    private function checkInfo(&$groupInfo) {
        $groupInfo['group_caption'] = SysUtil::safeString($groupInfo['group_caption']);
        $groupInfo['group_type'] = SysUtil::safeString($groupInfo['group_type']);
        if(false == $groupInfo['group_caption']) {
            return array('errorMsg'=>'竞赛组名称不能为空');
        }
        if(isset($groupInfo['group_status'])) {
            $groupInfo['group_status'] = abs($groupInfo['group_status']);
        }
        $groupInfo['special_url'] = SysUtil::safeString($groupInfo['special_url']);
        $strQuery = 'SELECT count(1) FROM ' . $this->tableName . '
                     WHERE is_remove=0 
                       AND group_caption=' . $this->dao->quote($groupInfo['group_caption']);
        if($groupInfo['group_id']) {
            $strQuery .= ' AND group_id !=' . abs($groupInfo['group_id']);
        }
        $ifExists = $this->dao->getOne($strQuery) > 0;
        if($ifExists) {
            return array('errorMsg'=>'同名竞赛组已经存在');
        }
        return true;
    }
    
    public function save($groupInfo) {
        $checkResult = $this->checkInfo($groupInfo);
        if(is_array($checkResult)) return $checkResult;
        $userKey = $this->operator->getUserKey();
        $time = date('Y-m-d H:i:s');
        if($groupInfo['group_id']) {
            $strQuery = 'UPDATE ' . $this->tableName . '
                         SET group_caption=' . $this->dao->quote($groupInfo['group_caption']) . ',
                             group_type=' . $this->dao->quote($groupInfo['group_type']) . ',
                             group_status=' . abs($groupInfo['group_status']) . ',
                             special_url=' . $this->dao->quote($groupInfo['special_url']) . ',
                             group_intro=' . $this->dao->quote($groupInfo['group_intro']) . ',
                             update_user=' . $this->dao->quote($userKey) . ',
                             update_at=' . $this->dao->quote($time) . '
                         WHERE group_id=' . abs($groupInfo['group_id']);
        } else {
            $strQuery = 'INSERT INTO ' . $this->tableName . ' 
                         (group_caption,group_type,group_status,special_url,group_intro,update_user,update_at)
                         VALUES (
                         ' . $this->dao->quote($groupInfo['group_caption']) . ',
                         ' . $this->dao->quote($groupInfo['group_type']) . ',
                         ' . abs($groupInfo['group_status']) . ',
                         ' . $this->dao->quote($groupInfo['special_url']) . ',
                         ' . $this->dao->quote($groupInfo['group_intro']) . ',
                         ' . $this->dao->quote($userKey) . ',
                         ' . $this->dao->quote($time) . ')';
        }
        
        if(false == $this->dao->execute($strQuery)) {
            $errorMsg = $groupInfo['group_id'] ? '竞赛组信息修改失败' : '竞赛组信息添加失败';
            return array('errorMsg'=>$errorMsg);
        }
        return true;
    }
    
    public function delete($groupId) {
        $strQuery = 'UPDATE ' . $this->tableName . '
                     SET is_remove=' . time() . ',
                         update_user=' . $this->dao->quote($this->operator->getUserKey()) . ',
                         update_at=' . $this->dao->quote(date('Y-m-d H:i:s')) . '
                     WHERE group_id=' . $groupId;
        return $this->dao->execute($strQuery);
    }
    
    public function find($groupId) {
        $groupId = abs($groupId);
        $strQuery = 'SELECT * FROM ' . $this->tableName . ' 
                     WHERE group_id=' . $groupId . ' 
                       AND is_remove=0';
        $groupInfo = $this->dao->getRow($strQuery);
        $strQuery = 'SELECT exam_id,exam_caption 
                     FROM ' . $this->examTable . '
                     WHERE exam_status=1 
                       AND group_id=' . $groupId;
        $groupInfo['exam_list'] = $this->dao->getAll($strQuery);
        $groupInfo['exam_count'] = sizeof($groupInfo['exam_list']);
        return $groupInfo;
    }
}
?>