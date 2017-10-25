<?php
class SubjectModel {
    private $tableName = 'ex_subjects';
    public $dao = null;
    private $operator = null;
    private $examSubjectTable = 'ex_exam_subjects';
    
    public function __construct() {
        $this->dao = Dao::getDao();
        if(class_exists('User', false)) {
        	$this->operator = User::getLoginUser();
        }
    }
    
    public function getSubjectNames() {
    	return array('math'=>'数学', 'chinese'=>'语文', 'english'=>'英语', 'physic'=>'物理', 'chemistry'=>'化学');
    }
    
    public function getSubjectArray() {
        $subjectList = $this->getSubjectList();
        $subjectArray = array();
        foreach ($subjectList as $subject)  {
            $subjectArray[$subject['subject_code']] = $subject['subject_name'];
        }
        return $subjectArray;
    }
    
    public function find($subjectCode) {
        $strQuery = 'SELECT * FROM ' . $this->tableName . ' 
                     WHERE subject_code=' . $this->dao->quote($subjectCode);
        return $this->dao->getRow($strQuery);
    }
    
    public function getSubjectList() {
        $strQuery = 'SELECT * FROM ' . $this->tableName . ' ORDER BY subject_code';
        $subjectList = $this->dao->getAll($strQuery);
        return $subjectList;
    }
    
    public function getExamSubjects($examId) {
        $quesModel = D('Question');
        $quesCntArray = $quesModel->getQuestionCountBySubject($examId);
        $strQuery = 'SELECT sbj.*,esbj.score_url,esbj.update_user,esbj.update_at 
                     FROM ' . $this->tableName . ' sbj,' . $this->examSubjectTable . ' esbj
                     WHERE sbj.subject_code=esbj.subject_code
                       AND esbj.is_remove=0
                       AND esbj.exam_id=' . abs($examId) . '
                     ORDER BY sbj.subject_code';
        $subjectList = $this->dao->getAll($strQuery);
        foreach ($subjectList as $key=>$subject) {
            $subjectList[$key]['ques_cnt'] = abs($quesCntArray[$subject['subject_code']]);
        }
        return $subjectList;
    }
    
    public function saveExamSubject($examId, $subjectCode) {
        $userKey = $this->operator->getUserKey();
        $time = Date('Y-m-d H:i:s');
        $strQuery = 'INSERT INTO ' . $this->examSubjectTable . ' 
                     (exam_id,subject_code,is_remove,update_user,update_at)
                     VALUES (
                        ' . $examId . ',
                        ' . $this->dao->quote($subjectCode) . ',0,
                        ' . $this->dao->quote($userKey). ',
                        ' . $this->dao->quote($time) . ')';
        if($this->dao->execute($strQuery)) {
            return true;
        }
        return array('errorMsg'=>'竞赛科目添加失败');
    }
    
    public function delExamSubject($examId, $subjectCode) {
        $userKey = $this->operator->getUserKey();
        $time = Date('Y-m-d H:i:s');
        $strQuery = 'UPDATE ' . $this->examSubjectTable . '
                     SET is_remove=' . time() . ',
                         update_user=' . $this->dao->quote($userKey) . ',
                         update_at=' . $this->dao->quote($time) . '
                     WHERE exam_id=' . $examId . '
                       AND is_remove=0
                       AND subject_code=' . $this->dao->quote($subjectCode);
        if($this->dao->execute($strQuery)) {
            return true;
        }
        return array('errorMsg'=>'竞赛科目删除失败');
    }
    
    public function examSubjectInfo($examId, $subjectCode) {
    	$strQuery = 'SELECT * FROM ' . $this->examSubjectTable . '
    				 WHERE exam_id=' . abs($examId) . '
    				   AND subject_code=' . $this->dao->quote($subjectCode) . '
    				   AND is_remove=0'; 
    	return $this->dao->getRow($strQuery);
    }
    
    public function setScoreUrl($examId, $subjectCode, $scoreUrl) {
    	$strQuery = 'UPDATE ' . $this->examSubjectTable . '
    				 SET score_url=' . $this->dao->quote($scoreUrl) . '
    				 WHERE exam_id=' . abs($examId) . '
    				   AND subject_code=' . $this->dao->quote($subjectCode) . '
    				   AND is_remove=0';
    	if($this->dao->execute($strQuery)) {
    		return array('success'=>true);
    	}
    	return array('errorMsg'=>'查询地址设置失败');
    }
    
    public function getScoreUrl($paperId, $scoreKey) {
    	$paperModel = D('Paper');
    	$paperInfo = $paperModel->find($paperId);
    	$subjectInfo = $this->examSubjectInfo($paperInfo['exam_id'], $paperInfo['subject_code']);
    	$scoreUrl = $subjectInfo['score_url'];
    	if($scoreUrl) {
    		$scoreUrl = str_replace('{key}', $scoreKey, $scoreUrl);
    	}
    	return $scoreUrl;
    }
}
?>