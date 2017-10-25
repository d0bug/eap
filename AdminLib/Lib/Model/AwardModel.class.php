<?php
class AwardModel {
	public $dao = null;
	public $tableName = 'ex_exam_awards';
	private $scoreTable = 'ex_exam_scores';
	private $typeNameTable = 'ex_award_names';
	private $awardCfgTable = 'ex_award_cfgs';
	private $operator = '';
	
	public function __construct() {
		$this->dao = Dao::getDao();
		if (class_exists('User', false)) {
			$operator = User::getLoginUser();
			$this->userKey= $operator->getUserKey();
		}
	}
	
	public function find($id) {
		$strQuery = 'SELECT * FROM ' . $this->tableName . '
					 WHERE id=' . $this->dao->quote($id);
		return $this->dao->getRow($strQuery);
	}
	
	public function getAwardTypes($examId) {
		$paperModel = D('Paper');
		$paperList = $paperModel->getPaperList($examId);
		$typeArray = array();
		foreach ($paperList as $paper) {
			if($paper['paper_type'] != 'virtual') {
				$typeKey = $paper['subject'] . '_' . $paper['paper_type'];
			} else {
				$typeKey = $paper['subject'] . '_' . $paper['paper_type'] .'_' . $paper['paper_id'];
			}
			$typeArray[$typeKey] = $this->getAwardType($examId, $typeKey);
		}
		$typeArray['total'] = $this->getAwardType($examId, 'total');
		return $typeArray;
	}
	
	public function getAwardCount($examId) {
		static $countArray = array();
		if(false == isset($countArray[$examId])) {
			$strQuery = 'SELECT count(1) FROM ' . $this->tableName . '
						 WHERE exam_id=' . abs($examId);
			$countArray[$examId] = $this->dao->getOne($strQuery);
		}
		return $countArray[$examId];
	}
	
	public function getAwardList($examId, $currentPage, $pageSize) {
		if($pageSize < 1) $pageSize = 20;
		$recordCount = $this->getAwardCount($examId);
		$pageCount = ceil($recordCount / $paperSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage <1) $currentPage = 1;
		$strQuery = 'SELECT * FROM ' . $this->tableName . '
					 WHERE exam_id=' . abs($examId);
		$order = 'ORDER BY award_type,award_score DESC';
		$awardList = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		foreach ($awardList as $key=>$award) {
			$awardType = $award['award_type'];
			$awardList[$key]['stu_count'] = $this->getAwardStuCount($award);
			$awardList[$key]['type_name'] = $this->getAwardType($examId, $awardType);
		}
		return $awardList;
	}
	
	public function getAwardStuList($award, $currentPage=1, $pageSize=20) {
		if(is_string($award)) {
			$award = $this->find($award);
		}
		$recordCount = $this->getAwardStuCount($award);
		$pageCount = ceil($recordCount / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		$examId = $award['exam_id'];
		$awardType = $award['award_type'];
		$typeAwards = $this->getTypeScoreArray($examId, $awardType);
		$minScore = $award['award_score'];
		$maxScore = 9999;
		foreach ($typeAwards as $award) {
			if($award['award_score'] == $minScore) {
				break;
			} else {
				$maxScore = $award['award_score'] - 0.1;
			}
		}
		$scoreModel = D('Score');
		$strQuery = $scoreModel->getScoreQuery($examId);
		
		$strQuery = 'SELECT * FROM (' . $strQuery . ') score
					 WHERE ' . $awardType . '_score BETWEEN ' . $minScore . ' AND ' . $maxScore;
		$order = 'ORDER BY ' . $awardType . '_score DESC';
		$stuList = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		return $stuList;
	}
	
	public function getAwardStuCount($award) {
		static $awardArray = array();
		static $countArray = array();
		if(is_string($award)) {
			if(isset($countArray[$award])) {
				return $countArray[$award];
			}
			$award = $this->find($award);
		}
		$awardId = $award['id'];
		$examId = $award['exam_id'];
		$awardType = $award['award_type'];
		$key = $examId . '-' . $awardType;
		if(false == isset($awardArray[$key])) {
			$awardArray[$key] = $this->getTypeScoreArray($examId, $awardType);
		}
		$typeAwards = $awardArray[$key];
		$minScore = $award['award_score'];
		$maxScore = 9999;
		foreach ($typeAwards as $award) {
			if($award['award_score'] == $minScore) {
				break;
			} else {
				$maxScore = $award['award_score'] - 0.1;
			}
		}
		$scoreModel = D('Score');
		$strQuery = $scoreModel->getScoreQuery($examId);
		$strQuery = 'SELECT COUNT(1) FROM (' . $strQuery . ') score
					 WHERE ' . $awardType . '_score BETWEEN ' . $minScore . ' AND ' . $maxScore;
		$countArray[$awardId] = $this->dao->getOne($strQuery);
		return $countArray[$awardId];
	}
	
	private function getTypeScoreArray($examId, $awardType) {
		$strQuery = 'SELECT * FROM ' . $this->tableName . '
					 WHERE exam_id=' . abs($examId) . '
					   AND award_type=' . $this->dao->quote($awardType) . '
					 ORDER BY award_score DESC';
		$awardArray = $this->dao->getAll($strQuery);
		return $awardArray;
	}
	
	public function setTypeName($nameInfo) {
		$time = date('Y-m-d H:i:s');
		$condition = 'exam_id=' . abs($nameInfo['examId']) . ' 
				 	   AND award_type=' . $this->dao->quote(SysUtil::safeSearch($nameInfo['awardType']));
		$strQuery = 'SELECT * FROM ' . $this->typeNameTable . '
				 	 WHERE ' . $condition;
		$dbTypeName = $this->dao->getRow($strQuery);
		if($dbTypeName) {
			$strQuery = 'UPDATE ' . $this->typeNameTable . '
						 SET award_name=' . $this->dao->quote(SysUtil::safeString($nameInfo['typeCaption'])) . ',
						 	 update_user=' . $this->dao->quote($this->operator) . ',
						 	 update_at=' . $this->dao->quote($time) . '
						 WHERE ' . $condition;
		} else {
			$strQuery = 'INSERT INTO ' . $this->typeNameTable . '
						 (exam_id,award_type,award_name,create_user,create_at,update_user,update_at)
						 VALUES (
						 ' . abs($nameInfo['examId']) .',
						 ' . $this->dao->quote(SysUtil::safeString($nameInfo['awardType'])) . ',
						 ' . $this->dao->quote(SysUtil::safeString($nameInfo['typeCaption'])) . ',
						 ' . $this->dao->quote($this->operator) . ',
						 ' . $this->dao->quote($time) . ',
						 ' . $this->dao->quote($this->operator) . ',
						 ' . $this->dao->quote($time) . ')';
		}
		if($this->dao->execute($strQuery)) {
			return true;
		}
		
		return false;
	}
	
	private function getAwardName($examId, $awardType) {
		static $awardNames = null;
		if(null === $awardNames) {
			$strQuery = 'SELECT award_type,award_name FROM ' . $this->typeNameTable . '
						 WHERE exam_id=' . abs($examId);
			$nameList = $this->dao->getAll($strQuery);
			$awardNames = array();
			foreach ($nameList as $row) {
				$awardNames[$row['award_type']] = $row['award_name'];
			}
		}
		if($awardNames) {
			return $awardNames[$awardType];
		}
		return '';
	}
	
	public function getAwardType($examId, $awardType) {
		$awardName = $this->getAwardName($examId, $awardType);
		if($awardName) return $awardName;
		
		static $types = array();
		$key = md5($examId . '-' . $awardType);
		if(false == isset($types[$key])) {
			if('total' == $awardType) {
				$types[$key] = '竞赛综合奖项';
			} else if (false == preg_match('/virtual/', $awardType)) {
				$subjectModel = D('Subject');
		    	$subjectNames = $subjectModel->getSubjectNames();
		    	$paperTypeCaptions = array('real'=>'卷', 'addon'=>'附加卷');
		    	list($subject,$paperType) = explode('_', $awardType);
		    	$types[$key] = $subjectNames[$subject] . $paperTypeCaptions[$paperType] . '奖项';
			} else {
				$paperModel = D('Paper');
				list($subject, $type, $paperId) = explode('_', $awardType);
				$paperInfo = $paperModel->find($paperId);
				$types[$key] = $paperInfo['paper_caption'] . '奖项';
			}
		}
		
		return $types[$key];
	}
	
	public function save($awardInfo) {
		$time = date('Y-m-d H:i:s');
		$strQuery = 'SELECT * FROM ' . $this->tableName . '
					 WHERE exam_id=' . abs($awardInfo['exam_id']) . '
					   AND award_type=' . $this->dao->quote(SysUtil::safeString($awardInfo['award_type'])) . '
					   AND (award_caption=' . $this->dao->quote(SysUtil::safeString($awardInfo['award_caption'])) . '
					     OR award_score=' . abs($this->dao->quote($awardInfo['award_score'])) . ')';
		if($awardInfo['id']) {
			$strQuery .= ' AND id !=' . $this->dao->quote(SysUtil::uuid($awardInfo['id']));
		}
		$existsAward = $this->dao->getRow($strQuery);
		if($existsAward) {
			return array('errorMsg'=>'分数或奖项名称存在冲突，请检查');
		}
		if($awardInfo['id']) {
			$type = '修改';
			$strQuery = 'UPDATE ' . $this->tableName . '
						 SET award_type=' . $this->dao->quote(SysUtil::safeString($awardInfo['award_type'])) . ',
						 	 award_caption=' . $this->dao->quote(SysUtil::safeString($awardInfo['award_caption'])) . ',
						 	 award_score=' . abs($awardInfo['award_score']) . ',
						 	 update_user=' . $this->dao->quote($this->operator) . ',
						 	 update_at=' . $this->dao->quote($time) . '
						 WHERE id=' . $this->dao->quote($awardInfo['id']);
		} else {
			$type = '添加';
			$strQuery = 'INSERT INTO ' . $this->tableName . '
						 (exam_id, award_type,award_caption,award_score,create_user,create_at,update_user,update_at)
						 VALUES (
						 	' . abs($awardInfo['exam_id']) . ',
						 	' . $this->dao->quote(SysUtil::safeString($awardInfo['award_type'])) . ',
						 	' . $this->dao->quote(SysUtil::safeString($awardInfo['award_caption'])) . ',
						 	' . abs($awardInfo['award_score']) . ',
						 	' . $this->dao->quote($this->operator) . ',
						 	' . $this->dao->quote($time) . ',
						 	' . $this->dao->quote($this->operator) . ',
						 	' . $this->dao->quote($time) . ')';
		}
		if($this->dao->execute($strQuery)) {
			return array('success'=>true, 'message'=>'奖项信息' . $type . '成功');
		}
		return array('errorMsg'=>'奖项信息' . $type . '失败');
	}
	
	public function delete($awardId) {
		$strQuery = 'DELETE FROM ' . $this->tableName . '
					 WHERE id=' . $this->dao->quote($awardId);
		$this->dao->execute($strQuery);
		return true;
	}
	
	public function getAwardCaption($examId, $awardType, $paperScore) {
		static $awardArrays = array();
		if(false == isset($awardArrays[$examId])) {
			$strQuery = 'SELECT * FROM ' . $this->tableName . '
						 WHERE exam_id=' . abs($examId) . '
						 ORDER BY award_type,award_score DESC';
			$awardList = $this->dao->getAll($strQuery);
			$awardArray = array();
			foreach ($awardList as $award) {
				$awardArray[$award['award_type']][] = $award;
			}
			$awardArrays[$examId] = $awardArray;
		}
		$awardArray = $awardArrays[$examId];
		$typeAwards = $awardArray[$awardType];
		foreach ($typeAwards as $award) {			
			if(floatval($paperScore) >= floatval($award['award_score'])) {
				return $award['award_caption'];
			}
		}
		return '';
	}
	
	
	public function getAwardItems() {
		return  array('type_caption'=>'类别名称（例：数学单项）', 
					   'award_caption'=>'奖项名称（例：一等奖）',
					   'stu_name'=>'考生姓名',
					   'exam_code'=>'准考证号',
					   'addon_text'=>'附加文字（例：四年级组）');
	}
	
	public function uploadTpl($examId, $tplInfo) {
		import('ORG.Net.UploadFile');
		$examId = abs($examId);
		$uploadConfig = C('AWARD_UPLOAD_CONFIG');
		$thumbPrefix = 'award_' . sprintf('%03d', $examId) . '_';
		$uploadConfig['thumbPrefix'] = $thumbPrefix;
		$savePath = C('AWARD_TPL_DIR');
		$uploader = new UploadFile($uploadConfig);
		$uploadResult = $uploader->uploadOne($tplInfo, $savePath);
		if(false == $uploadResult) {
			$errorMsg = $uploader->getErrorMsg();
			return array('errorMsg'=>$errorMsg);
		}
		$fileInfo = $uploadResult[0];
		$fileName = $fileInfo['savename'];
		
		$time = date('Y-m-d H:i:s');
		$cfgInfo = $this->findCfg($examId);
		if($cfgInfo) {
			@unlink($savePath . '/' . $cfgInfo['award_file']);
			@unlink($savePath . '/' . $thumbPrefix . $cfgInfo['award_file']);
			$strQuery = 'UPDATE ' . $this->awardCfgTable . '
						 SET cfg_status=0,
						 	 award_file=' . $this->dao->quote($fileName) . ',
						 	 update_user=' . $this->dao->quote($this->operator) . ',
						 	 update_at=' . $this->dao->quote($time) . '
						 WHERE exam_id=' . $examId;
		} else {
			$strQuery = 'INSERT INTO ' . $this->awardCfgTable . '
						(exam_id,award_file,award_cfg,cfg_status,create_user,create_at,update_user,update_at)
						VALUES (
						' . $examId . ',
						' . $this->dao->quote($fileName) . ',
						' . $this->dao->quote('{}') . ',0,
						' . $this->dao->quote($this->operator) . ',
						' . $this->dao->quote($time) . ',
						' . $this->dao->quote($this->operator) . ',
						' . $this->dao->quote($time) . ')';
		}
		if(false == $this->dao->execute($strQuery)) {
			return array('errorMsg'=>'奖项保存失败');
		}
		return true;
	}
	
	public function findCfg($examId) {
		$strQuery = 'SELECT * FROM ' . $this->awardCfgTable . '
					 WHERE exam_id=' . $examId;
		$cfgInfo = $this->dao->getRow($strQuery);
		if($cfgInfo) {
			$cfgInfo['award_cfg'] = SysUtil::jsonDecode($cfgInfo['award_cfg']);
		}
		return $cfgInfo;
	}
	
	public function saveTplCfg($cfgInfo) {
		$examId = abs($cfgInfo['exam_id']);
		$dbCfgInfo = $this->findCfg($examId);
		$awardItems = $this->getAwardItems();
		$cfgArray = array();
		foreach ($awardItems as $itemKey=>$item) {
			if($cfgInfo[$itemKey]) {
				if(abs($cfgInfo['left'][$itemKey]) > 0 && abs($cfgInfo['top'][$itemKey]) > 0 && abs($cfgInfo['fontSize'][$itemKey]) > 0) {
					$itemCfg = array('fontFamily'=>$cfgInfo['fontFamily'][$itemKey],
								     'fontSize'=>$cfgInfo['fontSize'][$itemKey],
								     'fontColor'=>$cfgInfo['fontColor'][$itemKey],
								     'top'=>$cfgInfo['top'][$itemKey],
								     'blankNum'=>$cfgInfo['blankNum'][$itemKey],
								     'left'=>$cfgInfo['left'][$itemKey]);
					if($itemKey == 'addon_text') {
						if(false == $cfgInfo['addonText']) return array('errorMsg'=>'证书设置不完整');
						$itemCfg['text'] = $cfgInfo['addonText'];
					} else if($itemKey == 'stu_name') {
						$itemCfg['center'] = abs($cfgInfo['center']);
					}
					$cfgArray[$itemKey] = $itemCfg;
				} else {
					return array('errorMsg'=>'证书设置不完整');
				}
			}
		}
		
		$cfgData = SysUtil::jsonEncode($cfgArray);
		$time = date('Y-m-d H:i:s');
		if($dbCfgInfo) {
			$strQuery = 'UPDATE ' . $this->awardCfgTable . '
						 SET award_cfg=' . $this->dao->quote($cfgData) . ',
						 	 cfg_status=' . abs($cfgInfo['saveType']) . ',
						 	 update_user=' . $this->dao->quote($this->operator) . ',
						 	 update_at=' . $this->dao->quote($time) . '
						 WHERE exam_id=' . $examId;
			if($this->dao->execute($strQuery)) {
				return array('success'=>true);
			} else {
				return array('errorMsg'=>'证书设置失败');
			}
		} else {
			return array('errorMsg'=>'还没有设置图片模板');
		}
	}
	
	public function switchStatus($examId) {
		$examId = abs($examId);
		$awardCfg = $this->findCfg($examId);
		if($awardCfg) {
			$strQuery = 'UPDATE ' . $this->awardCfgTable . '
						 SET cfg_status=' . abs($awardCfg['cfg_status'] - 1) . ',
						 	 update_user=' . $this->dao->quote($this->operator) . ',
						 	 update_at=' . $this->dao->quote(date('Y-m-d H:i:s')) . '
						 WHERE exam_id=' . abs($examId);
			if($this->dao->execute($strQuery)) {
				$status = $awardCfg['cfg_status'] ? '启用' : '停用';
				return array('success'=>true, 'status'=>$status);
			} else {
				return array('errorMsg'=>'奖项状态设置失败');
			}
		} else {
			return array('errorMsg'=>'暂未设置奖项模板');
		}
		exit;
	}
	
	public function getAwardData($examId, $scoreData) {
		$examId = abs($examId);
		$strQuery = 'SELECT * FROM ' . $this->awardCfgTable . '
					 WHERE exam_id=' . abs($examId);
		$awardCfg = $this->dao->getRow($strQuery);
		$strQuery = 'SELECT a.*,an.award_name 
					 FROM ' . $this->tableName . ' a
					 LEFT JOIN ' . $this->typeNameTable . ' an
					    ON a.award_type=an.award_type 
					    AND a.exam_id=an.exam_id
					 WHERE a.exam_id=' . abs($examId) . '
					   ORDER BY a.award_type,a.award_score DESC';
		$awardList = $this->dao->getAll($strQuery);
		$awardArray = array();
		foreach ($awardList as $award) {
			$awardArray[$award['award_type']][] = array('name'=>$award['award_name'], 
														'score'=>$award['award_score'], 
														'level'=>$award['award_caption']);
		}
		
		$awardData = array();
		foreach ($scoreData as $awardType=>$stuScore) {
			if (isset($awardArray[$awardType])) {
				$awardData[$awardType] = array('award'=>false, 'level'=>'未获奖','link'=>false);
				foreach ($awardArray[$awardType] as $award) {
					if($stuScore >= $award['score']) {
						$awardData[$awardType] = array('award'=>true, 'level'=>$award['level']);
						if ($awardCfg) {
							$awardData[$awardType]['link'] = true;
						}
						break;
					}
				}
			}
		}
		return $awardData;
	}
	
	public function awardVCount($examId) {
		import('ORG.Util.NCache');
		$cache = NCache::getCache();
		$nameSpace = 'AwardVCount';
		$key = 'AwardVCount_' . $examId;
		$awardVCount = $cache->get($nameSpace, $key);
		if(1 || false == $awardVCount) {
			$strQuery = 'SELECT award_type,award_caption,award_score 
						 FROM ' . $this->tableName . '
						 WHERE exam_id=' . abs($examId) . '
						 ORDER BY award_type,award_score DESC';
			$awardList = $this->dao->getAll($strQuery);
			if(false == $awardList) return array();
			$awardTypeArray = array();
			foreach ($awardList as $award) {
				$key = $award['award_type'] . '_' . intval($award['award_score']);
				$awardTypeArray[$award['award_type']][$key] = $award;
			}
			$scoreModel = D('Score');
			$scoreQury = $scoreModel->simpleQuery($examId);
			
			$maxScores = array();
			$strQuery = 'SELECT ';
			foreach ($awardTypeArray as $awardType=>$awards) {
				if(false == isset($maxScores[$awardType])) {
					$maxScores[$awardType] = 9999;
				}
				foreach ($awards as $awardKey=>$award) {
					$strQuery .= 'SUM(CASE WHEN ' . $awardType . '_score >=' . $award['award_score'] . ' AND ' . $awardType . '_score <' . $maxScores[$awardType] . ' THEN 1 ELSE 0 END )' . $awardKey . ',';
					$maxScores[$awardType] = $award['award_score'];
				}
			}
			$strQuery = substr($strQuery, 0, -1);
			$strQuery .= ' FROM (' . $scoreQury . ') score';
			$countRow = $this->dao->getRow($strQuery);
			foreach ($awardTypeArray  as $awardType=>$awards) {
				foreach ($awards as $awardKey=>$award) {
					$awardTypeArray[$awardType][$awardKey]['count'] = $countRow[$awardKey];
				}
			}
			
			$strQuery = 'SELECT virtual_type,score,score_cnt 
						 FROM ex_exam_virtual WHERE exam_id=' . $examId . ' 
						 ORDER BY virtual_type,score DESC';
			$virtualList = $this->dao->getAll($strQuery);
			
			$vTypeCount = array();
			foreach ($virtualList as $v) {
				$vTypeCount[$v['virtual_type']][$v['score']] = $v['score_cnt'];
			}
			
			foreach ($awardTypeArray as $awardType=>$awardArray) {
				$maxScore = 9999;
				foreach($awardArray as $key=>$award) {
					$awardScore = $award['score'];
					foreach ($vTypeCount[$awardType] as $score=>$scoreCnt) {
						if($score >= $award['award_score'] && $score < $maxScore) {
							$awardTypeArray[$awardType][$key]['count'] += $scoreCnt;
						} else  if($score < $award['award_score']) {
							$maxScore = $award['award_score'];
							break;
						}
					}
				}
			}
			$awardVCount = $awardTypeArray;
			$cache->set($nameSpace, $key, $awardVCount);
		}
		return $awardVCount;
	}
}
?>