<?php
class StuGroupModel {
	protected $dao = null;
	protected $tableName = 'ex_stu_groups';
	protected $stuTable = 'bs_student';
	protected $operator = '';
	protected $groupExpire = 300;
	
	public function __construct() {
		$this->dao = Dao::getDao();
		if(class_exists('User', false)) {
			$operator = User::getLoginUser();
			$this->operator = $operator->getUserKey();
		}
	}
	
	public function getGroupCount() {
		static $count = null;
		if(null  === $count) {
			$strQuery = 'SELECT COUNT(1) FROM ' . $this->tableName . '
						 WHERE is_remove=0';
			$count = $this->dao->getOne($strQuery);
		}
		return $count;
	}
	
	public function getGroupList($currentPage, $pageSize) {
		$recordCount = $this->getGroupCount();
		$pageSize = abs($pageSize);
		$pageSize = $pageSize > 0 ? $pageSize : 20;
		$pageCount = ceil($recordCount / $pageSize);
		$currentPage = abs($currentPage);
		$currentPage = $currentPage > $pageCount ? $pageCount : $currentPage;
		$currentPage = $currentPage > 0 ? $currentPage : 1;
		$strQuery = 'SELECT * FROM ' . $this->tableName . '
					 WHERE is_remove=0';
		$order = ' ORDER BY create_at DESC';
		$groupList = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		return $groupList;
	}
	
	public function addGroup($groupData, $groupType, $groupCaption) {
		if ($groupType != 'static') {
			return $this->addSqlGroup($groupData, $groupType, $groupCaption);
		} else {
			return $this->addStaticGroup($groupData, $groupType, $groupCaption);
		}
	}
	
	private function addSqlGroup($sql, $groupType, $groupCaption) {
		$sql = stripslashes($sql);
		$row = $this->dao->getRow($sql);
		if(isset($row['scode'])) {
			$sql = preg_replace('/^SELECT (?:DISTINCT )?.* FROM /i', 'SELECT scode FROM ', $sql);
		} else if (isset($row['stu_code'])) {
			$sql = preg_replace('/^SELECT (?:DISTINCT )?.* FROM /i', 'SELECT stu_code scode FROM ', $sql);
		} else if (isset($row['sstudentcode'])) {
			$sql = preg_replace('/^SELECT (?:DISTINCT )?.* FROM /i', 'SELECT sstudentcode scode FROM ', $sql);
		}
		$md5 = md5($sql);
		$groupInfo = $this->findGroupInfoByMd5($md5);
		if($groupInfo) {
			#return array('errorMsg'=>'筛选组已经存在，请不要重复添加', 'groupInfo'=>$groupInfo);
		}
		$groupId = SysUtil::uuid();
		$time = date('Y-m-d H:i:s');
		$strQuery = 'INSERT INTO ' . $this->tableName . '
					 (group_id,group_title, group_type, group_sql,group_sql_md5,is_remove,create_user,create_at,update_user,update_at)
					 VALUES (' . $this->dao->quote($groupId) . ',
					 	     ' . $this->dao->quote($groupCaption) . ',
					 	     ' . $this->dao->quote($groupType) . ',
					 	     ' . $this->dao->quote($sql) . ',
					 	     ' . $this->dao->quote($md5) . ',0,
					 	     ' . $this->dao->quote($this->operator) . ',
					 	     ' . $this->dao->quote($time) . ',
					 	     ' . $this->dao->quote($this->operator) . ',
					 	     ' . $this->dao->quote($time) . ')';
		if($this->dao->execute($strQuery)) {
			#动态组添加完毕自动更新学员列表字段
			$this->findGroupById($groupId);
			return array('success'=>true);
		}
		return array('errorMsg'=>'学员筛选组添加失败');
	}
	
	private function addStaticGroup($groupData, $groupType, $groupCaption) {
		$groupId = SysUtil::uuid();
		$time = date('Y-m-d H:i:s');
		$stuCodeArray = preg_split('/[^a-z0-9]+/i', $groupData);
		$groupSqlCodes = array("'-1'");
		foreach ($stuCodeArray as $stuCode) {
			if($stuCode) {
				$groupSqlCodes[$stuCode] = $this->dao->quote($stuCode);
			}
		}
		
		$strQuery = 'INSERT INTO ' . $this->tableName . '
					 (group_id, group_title, group_type, group_sql, group_students, is_remove, create_user, create_at, update_user,update_at)
					 VALUES (' . $this->dao->quote($groupId) . ',
					 		 ' . $this->dao->quote($groupCaption) . ',
					 		 ' . $this->dao->quote('static') . ',
					 		 ' . $this->dao->quote(implode(',', $groupSqlCodes)) . ',
					 		 ' . $this->dao->quote(implode(',', $groupSqlCodes)) . ',0,
					 		 ' . $this->dao->quote($this->operator) . ',
					 		 ' . $this->dao->quote($time) . ',
					 		 ' . $this->dao->quote($this->operator) . ',
					 		 ' . $this->dao->quote($time) . ')';
		if ($this->dao->execute($strQuery)) {
			$this->saveGroupStudents($groupId, implode(',', $groupStudents));
			return array('success'=>true);
		}
		return array('errorMsg'=>'学员筛选组添加失败');
	}
	
	private function findGroupInfoByMd5($md5) {
		$strQuery = 'SELECT * FROM ' . $this->tableName . '
					 WHERE group_sql_md5=' . $this->dao->quote($md5) . '
					   AND is_remove=0';
		return $this->dao->getRow($strQuery);
	}
	
	public function findGroupById($groupId) {
		static $groupInfo = null;
		if(null == $groupInfo) {
			$strQuery = 'SELECT * FROM ' . $this->tableName . '
					 WHERE group_id=' . $this->dao->quote($groupId);
			$groupInfo = $this->dao->getRow($strQuery);
			if('static' != $groupInfo['group_type']) {
				if(time() - $groupInfo['group_last_update'] > $this->groupExpire) {
					$stuCodeList = $this->dao->getAll($groupInfo['group_sql']);
					$stuCodes = array();
					foreach ($stuCodeList as $row) {
						$stuCode = $row['scode'];
						$stuCodes[$stuCode] = $this->dao->quote($stuCode);
					}
					$strQuery = 'UPDATE ' . $this->tableName . '
								 SET group_students =' . $this->dao->quote(implode(',', $stuCodes)) . ',
								 	 group_last_update=' . $this->dao->quote(time()) . '
								 WHERE group_id=' . $this->dao->quote($groupId);
					$this->dao->execute($strQuery);
				}
			} else {
				$groupInfo['group_students'] = $this->readGroupStudents($groupInfo['group_id']);
			}
		}
		return $groupInfo;
	}
	
	private function getGroupFilePath($groupId) {
		$stuGroupPath = DATA_PATH . 'GroupStudent';
		$uidParts = explode('-', $groupId);
		$filePath = $stuGroupPath . '/' . $uidParts[0] . '/' . $uidParts[1] . '/' . $groupId;
		$dirname = dirname($filePath);
		@mkdir($dirname, 0777, true);
		return $filePath;
	}
	
	private function saveGroupStudents($groupId, $groupStudents) {
		$filePath = $this->getGroupFilePath($groupId);
		file_put_contents($filePath, $groupStudents);
	}
	
	private function readGroupStudents($groupId) {
		$filePath = $this->getGroupFilePath($groupId);
		return file_get_contents($filePath);
	}
	
	public function getGroupStuCount($groupId) {
		static $stuCount = null;
		if(null === $stuCount) {
			$groupInfo = $this->findGroupById($groupId);
			$strQuery = 'SELECT count(1) FROM ' . $this->stuTable . '
						 WHERE scode IN (' . $groupInfo['group_sql'] . ')';
			$stuCount = $this->dao->getOne($strQuery);
		}
		return $stuCount;
	}
	
	public function getGroupStuList($groupId, $currentPage, $pageSize=20) {
		$groupInfo = $this->findGroupById($groupId);
		$recordCount = $this->getGroupStuCount($groupId);
		$pageSize = abs($pageSize);
		$pageSize = $pageSize > 0 ? $pageSize : 20;
		$pageCount = ceil($recordCount / $pageSize);
		$currentPage = abs($currentPage);
		$currentPage = $currentPage > $pageCount ? $pageCount : $currentPage;
		$currentPage = $currentPage > 0 ? $currentPage : 1;
		$strQuery = 'SELECT * FROM ' . $this->stuTable . '
					 WHERE scode IN (' . $groupInfo['group_sql'] . ')';
		$order = ' ORDER BY scode';
		$stuList = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		$stuModel = D('Student');
		$stuList = $stuModel->allInfo($stuList, false);
		return $stuList;
	}
	
	public function delGroup($groupId) {
		$strQuery = 'UPDATE ' . $this->tableName . '
					 SET is_remove=' . time() . '
					 WHERE group_id=' . $this->dao->quote($groupId);
		if($this->dao->execute($strQuery)) {
			return array('success'=>true);
		}
		return array('errorMsg'=>'筛选组删除失败');
	}
}
?>