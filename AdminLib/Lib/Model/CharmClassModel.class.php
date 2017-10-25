<?php
class CharmClassModel {	
	private $groupTable = 'charm_group';
	private $cClassTable = 'charm_class';
	private $cStuTable = 'charm_students';
	private $classTable = 'bs_class';
	private $classTypeTable = 'bs_classtype';
	private $projectTable = 'bs_project';
	private $xuekeTable = 'd_xueke';
	private $dao = null;
	private $operator = '';
	
	public function __construct() {
		$this->dao = Dao::getDao();
		if (class_exists('User', false)) {
			$user = User::getLoginUser();
			$this->operator = $user->getUserKey();
		}
	}
	
	public function findGroup($groupId) {
		$strQuery = 'SELECT * FROM ' . $this->groupTable . '
					 WHERE group_id=' . abs($groupId);
		return $this->dao->getRow($strQuery);
	}
	
	public function getGroupCount() {
		static $groupCount = null;
		if(null == $groupCount) {
			$strQuery = 'SELECT count(1) FROM ' . $this->groupTable;
			$groupCount = $this->dao->getOne($strQuery);
		}
		return $groupCount;
	}
	public function getGroupList($currentPage=0, $pageSize=20) {
		$strQuery = 'SELECT * FROM ' . $this->groupTable;
		if($currentPage >0) {
			$recordCount = $this->getGroupCount();
			$pageSize = abs($pageSize) > 0 ? abs($pageSize) : 20;
			$pageCount = ceil($recordCount / $pageSize);
			$currentPage = abs($currentPage);
			if($currentPage > $pageCount)  {
				$currentPage = $pageCount;
			}
			if($currentPage < 1) $currentPage = 1;
			$order = 'ORDER BY group_id DESC';
		} else {
			$currentPage = 1;
			$pageSize = 20;
			$order = 'ORDER BY group_id DESC';
		}
		$groupList = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		return $groupList;
	}
	
	public function saveGroup($groupInfo) {
		$groupId = intval($groupInfo['group_id']);
		$time = date('Y-m-d H:i:s');
		if($groupId >0) {
			$strQuery = 'UPDATE ' . $this->groupTable . '
						 SET group_title=' . $this->dao->quote($groupInfo['group_title']) . ',
						 	 min_lesson=' . abs($groupInfo['min_lesson']) . ',
						 	 max_lesson=' . abs($groupInfo['max_lesson']) . ',
						 	 update_user=' . $this->dao->quote($this->dao->quote($this->operator)) . ',
						 	 update_at=' . $this->dao->quote($time) . '
						 WHERE group_id=' . $groupId;
		} else {
			$strQuery = 'INSERT INTO ' . $this->groupTable . '
						 (group_title,charm_type,min_lesson,max_lesson,create_at,create_user,update_at,update_user)
						 VALUES (' . $this->dao->quote($groupInfo['group_title']) . ',
						 		 ' . $this->dao->quote('primary') . ',
						 		 ' . abs($groupInfo['min_lesson']) . ',
						 		 ' . abs($groupInfo['max_lesson']) . ',
						 		 ' . $this->dao->quote($time) . ',
						 		 ' . $this->dao->quote($this->operator) . ',
						 		 ' . $this->dao->quote($time) . ',
						 		 ' . $this->dao->quote($this->operator) . ')';
		}
		if($this->dao->execute($strQuery)) {
			return array('success'=>true);
		} else {
			return array('errorMsg'=>'试听组添加失败');
		}
	}
	
	public function delGroup($groupId) {
		$groupId = abs($groupId);
		$strQuery = 'DELETE FROM ' . $this->groupTable . '
					 WHERE group_id=' . $groupId;
		if ($this->dao->execute($strQuery)) {
			return array('success'=>true);
		} else {
			return array('errorMsg'=>'试听组删除失败');
		}
	}
	
	public function saveClass($classInfo) {
		$time = date('Y-m-d H:i:s');
		$strQuery = 'INSERT INTO ' . $this->cClassTable . '
					 (sclasscode,group_id,charm_type,sclassname,sprintteachers,sprinttime,sprintaddr,
					  sareacode,sprintarea,sdeptcode,sclasstypecode,dfee,create_user,create_at)
					 VALUES (' . $this->dao->quote($classInfo['sclasscode']) . ',
					 		 ' . abs($classInfo['charm_group']) . ',
					 		 ' . $this->dao->quote('primary') . ',
					 		 ' . $this->dao->quote($classInfo['sclassname']) . ',
					 		 ' . $this->dao->quote($classInfo['sprintteachers']) . ',
					 		 ' . $this->dao->quote($classInfo['sprinttime']) . ',
					 		 ' . $this->dao->quote($classInfo['sprintaddress']) . ',
					 		 ' . $this->dao->quote($classInfo['sareacode']) . ',
					 		 ' . $this->dao->quote($classInfo['sareaname']) . ',
					 		 ' . $this->dao->quote($classInfo['sdeptcode']) . ',
					 		 ' . $this->dao->quote($classInfo['sclasstypecode']) . ',
					 		 ' . $this->dao->quote($classInfo['dfee']) . ',
					 		 ' . $this->dao->quote($this->operator) . ',
					 		 ' . $this->dao->quote($time) . ')';
		if($this->dao->execute($strQuery)) {
			return array('success'=>true);
		} else {
			return array('errorMsg'=>'试听班级添加失败');
		}
	}
	
	private function getSearchCondition($searchArgs) {
		$condition = '';
		if($searchArgs['keyword']) {
			$condition .= 'AND (';
			$condition .= 'sclassname LIKE ' . $this->dao->quote('%' . $searchArgs['keyword'] . '%') . '
			 		    OR sclasscode=' . $this->dao->quote($searchArgs['keyword']) . '
			 		    OR sprintteachers LIKE ' . $this->dao->quote('%' . $searchArgs['keyword'] . '%') . '
			 		    OR sprinttime LIKE ' . $this->dao->quote('%' . $searchArgs['keyword'] . '%') . '
			 		    OR sprintaddr LIKE ' . $this->dao->quote('%' . $searchArgs['keyword'] . '%') . '
			 		    OR sprintarea LIKE ' . $this->dao->quote('%' . $searchArgs['keyword'] . '%');
			$condition .= ')';
		}
		
		return $condition;
	}
	
	public function getClassCount($searchArgs) {
		static $clsCount = null;
		if(null === $clsCount) {
			$strQuery = 'SELECT count(1) FROM ' . $this->cClassTable . '
						 WHERE group_id=' . abs($searchArgs['groupId']);
			$strQuery .= $this->getSearchCondition($searchArgs);
			$clsCount = $this->dao->getOne($strQuery);
		}
		return $clsCount;
	}
	
	public function getClassList($searchArgs, $currentPage, $pageSize) {
		$recordCount = $this->getClassCount($searchArgs);
		$pageSize = abs($pageSize);
		$pageSize = $pageSize > 0 ? $pageSize : 20;
		$pageCount = ceil($recordCount/$pageSize);
		$currentPage = abs($currentPage);
		$currentPage = $currentPage > $pageCount ? $pageCount : $currentPage;
		$currentPage = $currentPage > 0 ? $currentPage : 1;
		$strQuery = 'SELECT * FROM ' . $this->cClassTable . '
					 WHERE group_id=' . abs($searchArgs['groupId']);
		$strQuery .= $this->getSearchCondition($searchArgs);
		$order = 'ORDER BY create_at DESC';
		$classList = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		foreach ($classList as $key=>$class) {
			$classList[$key]['create_at'] = date('Y-m-d H:i:s', Dao::msStrtotime($class['create_at']));
		}
		return $classList;
	}
	
	public function delClass($classCodes) {
		if(preg_match('/,/', $classCodes)) {
			$clsCodeArray = explode(',', $classCodes);
			$clsCodeList = "'" . implode("','", $clsCodeArray) . "'";
		} else {
			$clsCodeList = $this->dao->quote($classCodes);
		}
		$strQuery = 'DELETE FROM ' . $this->cClassTable . '
					 WHERE sclasscode IN (' . $clsCodeList . ')';
		if($this->dao->execute($strQuery)) {
			return array('success'=>true);
		}
		return array('errorMsg'=>'选定试听班级删除失败');
	}
	
	public function getAreas($groupId) {
		$strQuery = 'SELECT DISTINCT sareacode,sprintarea 
					 FROM ' . $this->cClassTable . '
					 WHERE group_id=' . abs($groupId) . '
					 ORDER BY sareacode';
		$areaList = $this->dao->getAll($strQuery);
		return $areaList;
	}
	
	public function getSubjects($groupId) {
		$strQuery = 'SELECT * FROM ' . $this->xuekeTable . ' WHERE id IN (
						SELECT DISTINCT nxueke FROM ' . $this->classTypeTable . '
					 	WHERE scode IN (
					 		SELECT sclasstypecode FROM ' . $this->cClassTable . '
					 		WHERE group_id=' . abs($groupId) . '
					 	)
					 )
					 ORDER BY id';
		
		$xuekeList = $this->dao->getAll($strQuery);
		return $xuekeList;
	}
}
?>