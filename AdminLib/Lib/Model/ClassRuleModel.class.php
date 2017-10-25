<?php
class ClassRuleModel {
	protected $dao = null;
	protected $tableName = 'ex_class_rules';
	protected $groupTable = 'ex_stu_groups';
	protected $scoreTable = 'ex_exam_scores';
	protected $operator = '';
	
	public function __construct() {
		$this->dao = Dao::getDao();
		if(class_exists('User', false)) {
			$operator = User::getLoginUser();
			$this->operator = $operator->getUserKey();
		}
	}
	
	public function findRule($id) {
		$strQuery = 'SELECT * FROM ' . $this->tableName . '
					 WHERE id=' . $this->dao->quote($id);
		return $this->dao->getRow($strQuery);
	}
	
	public function getRuleCount($examId, $subjectCode) {
		static $ruleCount = null;
		if(null === $ruleCount) {
			$strQuery = 'SELECT count(1) FROM ' . $this->tableName . '
						 WHERE exam_id=' . abs($examId) . '
						   AND subject_code=' . $this->dao->quote($subjectCode) . '
						   AND is_remove=0';
			$ruleCount = $this->dao->getOne($strQuery);
		}
		return $ruleCount;
	}
	
	public function getRuleList($examId, $subjectCode, $currentPage=1, $pageSize=20) {
		$recordCount = $this->getRuleCount($examId, $subjectCode);
		$pageSize = abs($pageSize);
		$currentPage = abs($currentPage);
		$pageSize = $pageSize > 0 ? $pageSize : 20;
		$pageCount = ceil($recordCount / $pageSize);
		$currentPage = $currentPage > $pageCount ? $pageCount : $currentPage;
		$currentPage = $currentPage > 0 ? $currentPage : 1;
		$strQuery = 'SELECT r.*,g.group_title
					 FROM ' . $this->tableName . ' r
					 LEFT JOIN ' . $this->groupTable . ' g
					   ON r.stu_group_id=g.group_id
					 WHERE r.exam_id=' . abs($examId) . '
					   AND r.subject_code=' . $this->dao->quote($subjectCode) . '
					   AND r.is_remove=0';
		$order = 'ORDER BY class_level,class_weight,create_at';
		$ruleList = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		$subjectArray = C('EXAM_SUBJECTS');
		foreach ($ruleList as $key=>$rule) {
			$ruleList[$key]['subject_name'] = $subjectArray[$rule['subject_code']];
		}
		return $ruleList;
	}
	
	public function saveRule($ruleInfo) {
		$needed = array('exam_id', 'subject_code', 'class_semester', 'class_level', 'class_type', 'class_name', 'class_weight');
		foreach ($needed as $field) {
			if('' === $ruleInfo[$field]) {
				return array('errorMsg'=>'分班规则信息不完整');
			}
		}
		if('0' == $ruleInfo['stu_group_id']) {
			return array('errorMsg'=>'请选择学员筛选组');
		}
		$ruleInfo['class_semester'] = strtoupper($ruleInfo['class_semester']);
		$ruleInfo['class_search_link'] = 'http://' . preg_replace('/^http:\/\//i', '', $ruleInfo['class_search_link']);
		$ruleInfo['class_info_link'] = 'http://' . preg_replace('/^http:\/\//i', '', $ruleInfo['class_info_link']);
		$ruleInfo['class_desc'] = nl2br(SysUtil::safeString($ruleInfo['class_desc']));
		$time = date('Y-m-d H:i:s');
		if($ruleInfo['id']) {
			$strQuery = 'UPDATE ' . $this->tableName . '
						 SET class_codepre=' . $this->dao->quote($ruleInfo['class_semester']) . ',
						 	 class_level=' . abs($ruleInfo['class_level']) . ',
						 	 class_type=' . $this->dao->quote($ruleInfo['class_type']) . ',
						 	 class_name=' . $this->dao->quote($ruleInfo['class_name']) . ',
						 	 class_weight=' . abs($ruleInfo['class_weight']) . ',
						 	 stu_group_id=' . $this->dao->quote($ruleInfo['stu_group_id']) . ',
						 	 class_search_link=' . $this->dao->quote($ruleInfo['class_search_link']) . ',
						 	 class_info_link=' . $this->dao->quote($ruleInfo['class_info_link']) . ',
						 	 class_desc=' . $this->dao->quote($ruleInfo['class_desc']) . ',
						 	 update_user=' . $this->dao->quote($this->operator) . ',
						 	 update_at=' . $this->dao->quote($time) . '
						  WHERE id=' . $this->dao->quote($ruleInfo['id']);
		} else {
			$ruleId = SysUtil::uuid();
			$strQuery = 'INSERT INTO ' . $this->tableName . '
						 (id,exam_id,subject_code,class_codepre,class_level,class_type,class_name,
						  class_weight,stu_group_id,class_search_link,class_info_link,class_desc,
						  is_remove,create_user,create_at,update_user,update_at)
						  VALUES (' . $this->dao->quote($ruleId) . ',
						  		  ' . abs($ruleInfo['exam_id']) . ',
						  		  ' . $this->dao->quote($ruleInfo['subject_code']) . ',
						  		  ' . $this->dao->quote($ruleInfo['class_semester']) . ',
						  		  ' . abs($ruleInfo['class_level']) . ',
						  		  ' . $this->dao->quote($ruleInfo['class_type']) . ',
						  		  ' . $this->dao->quote($ruleInfo['class_name']) . ',
						  		  ' . abs($ruleInfo['class_weight']) . ',
						  		  ' . $this->dao->quote($ruleInfo['stu_group_id']) . ',
						  		  ' . $this->dao->quote($ruleInfo['class_search_link']) . ',
						  		  ' . $this->dao->quote($ruleInfo['class_info_link']) . ',
						  		  ' . $this->dao->quote($ruleInfo['class_desc']) . ',0,
						  		  ' . $this->dao->quote($this->operator) . ',
						  		  ' . $this->dao->quote($time) . ',
						  		  ' . $this->dao->quote($this->operator) . ',
						  		  ' . $this->dao->quote($time) . ')';
		}
		if($this->dao->execute($strQuery)) {
			return array('success'=>true);
		}
		return array('errorMsg'=>'分班规则保存失败');
	}
	
	public function deleteRule($id) {
		$strQuery = 'UPDATE ' . $this->tableName . '
					 SET is_remove=' . time() . ',
					 	 update_user=' . $this->dao->quote($this->operator) . ',
					 	 update_at=' . $this->dao->quote(date('Y-m-d H:i:s')) . '
					 WHERE id=' . $this->dao->quote($id);
		if($this->dao->execute($strQuery)) {
			return array('success'=>true);
		}
		return array('errorMsg'=>'分班规则删除失败');
	}
	
	public function getStuClass($examId, $stuCode, $scoreType) {
		$groupModel = D('StuGroup');
		#根据成绩类型取得学科编码
		list($subject,$paperType) = explode('_', $scoreType);
		$strQuery = 'SELECT * FROM ' . $this->scoreTable . '
					 WHERE exam_id=' . abs($examId) . '
					   AND paper_subject=' . $this->dao->quote($subject) . '
					   AND paper_type=' . $this->dao->quote($paperType);
		$order = 'ORDER BY id';
		$rows = $this->dao->getLimit($strQuery, 1, 1, $order);
		$scoreInfo = $rows[0];
		if(!$scoreInfo) return array();
		$subjectCode = $scoreInfo['subject_code'];
		#根据竞赛ID及学科编码取得该学科分班规则用到的所有筛选组，并过滤掉不匹配学员的组
		$ruleList = $this->getRuleList($examId, $subjectCode, 1, 999);
		$rules = array($this->dao->quote(-1));
		foreach ($ruleList as $rule) {
			$rules[] = $this->dao->quote($rule['stu_group_id']);
		}
		$strQuery = 'SELECT group_id FROM ' . $this->groupTable . '
					 WHERE group_id IN (' . implode(',', $rules) . ')
					   AND group_students LIKE ' . $this->dao->quote('%' . $stuCode . '%');
		$groupList = $this->dao->getAll($strQuery);
		$groupArray = array();
		foreach ($groupList as $group) {
			$groupArray[$group['group_id']] = 1;
		}
		#根据筛选组过滤分班规则，只匹配第一级的规则（同级，同权重）
		$lastKey = array();
		$stuClass = array();
		foreach ($ruleList as $rule) {
			$key = $rule['class_level'] . '_' . $rule['class_weight'];
			if (isset($groupArray[$rule['stu_group_id']])) {
				if(false == $lastKey[$rule['class_codepre']] || $lastKey[$rule['class_codepre']] == $key) {
					$stuClass[] = $rule;
					$lastKey[$rule['class_codepre']] = $key;
				}
			}
		}
		return $stuClass;
	}
}
?>