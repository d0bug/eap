<?php
import('ORG.Util.NCache');
class ScoreModel {
	public $dao = null;
	protected $tableName = 'ex_exam_scores';
	protected $paperTable = 'ex_exam_papers';
	protected $partTable = 'ex_paper_parts';
	protected $quesTable = 'ex_paper_questions';
	protected $examQuesTable = 'ex_exam_questions';
	protected $quesRatioTable = 'ex_ques_ratios';
	protected $quesAnsTable = 'ex_student_answer';
	protected $quesKnowledgeTable = 'ex_ques_knowledges';
	protected $esTable = 'ex_exam_students';
	protected $stuTable = 'bs_student';
	protected $posTable = 'ex_positions';
	protected $userKey = '';
	
	public function __construct(){
		$this->dao = Dao::getDao();
		if (class_exists('User', false)) {
			$operator = User::getLoginUser();
			$this->userKey= $operator->getUserKey();
		}
	}
	
	public function getStuScore($examId, $subjectCode, $examCode) {
		$strQuery = 'SELECT * FROM ' . $this->tableName . '
					 WHERE exam_id=' . $examId . '
					   AND subject_code=' . $this->dao->quote($subjectCode) . '
					   AND exam_code=' . $this->dao->quote($examCode) . ' 
					 ORDER BY paper_id';
		$scoreList = $this->dao->getAll($strQuery);
		$stuScore = array();
		if($scoreList) {
			foreach ($scoreList as $score) {
				foreach ($score as $field=>$value) {
					if(preg_match('/^ques_score_(\d+)/i', $field, $ar)) {
						$stuScore['ques_' . $score['paper_id'] . '_' . $ar[1]] = abs($value);
					}
				}
			}
		}
		return $stuScore;
	}
	
	public function getRealPaperScore($examId, $stuCode, $subject) {
		static $stuScoreArray = array();
		$key = $stuCode . '-' . $examId . '-' . $subject;
		if(false == isset($stuScoreArray[$key])) {
			$strQuery = 'SELECT * FROM ' . $this->tableName . '
						 WHERE exam_id=' . abs($examId) . '
						   AND stu_code=' . $this->dao->quote($stuCode) . '
						   AND paper_subject=' . $this->dao->quote($subject) . '
						   AND paper_type=' . $this->dao->quote('real');
			$stuScoreArray[$key] = $this->dao->getRow($strQuery);
		}
		return $stuScoreArray[$key];
	}
	
	public function getStuQuesAnswer($paperId, $stuCode,$quesId) {
		$strQuery = 'SELECT * FROM ' . $this->quesAnsTable . '
					 WHERE paper_id=' . abs($paperId) . '
					   AND stu_code=' . $this->dao->quote($stuCode) . '
					   AND ques_id=' . $this->dao->quote($quesId);
		$stuQuesAnswer = $this->dao->getRow($strQuery);
		$stuQuesAnswer['stu_answer'] = SysUtil::jsonDecode($stuQuesAnswer['stu_answer']);
		return $stuQuesAnswer;
	}
	
	public function saveScore($scoreInfo) {
		$examId = abs($scoreInfo['exam_id']);
		$stuCode = $scoreInfo['stu_code'];
		$examCode = $scoreInfo['exam_code'];
		$subjectCode = SysUtil::safeString($scoreInfo['subject_code']);
		$strQuery = 'SELECT * FROM ' . $this->esTable . '
					 WHERE exam_id=' . abs($scoreInfo['exam_id']) . '
					   AND stu_code=' . $this->dao->quote($stuCode) . '
					   AND exam_code=' . $this->dao->quote($examCode);
		
		$esInfo = $this->dao->getRow($strQuery);
		
		if (false == $esInfo) {
			return array('errorMsg'=>'考生信息不符');
		}
		$this->dao->begin();
		$successs = true;
		$cTime = date('Y-m-d H:i:s');
		$paperModel = D('Paper');
		
		foreach ($scoreInfo['ques'] as $paperId=>$questions){
			$paperChar = $scoreInfo['paper_char'] ? $scoreInfo['paper_char'] : 'A';
			#如果为AB卷，首先清空互斥试卷的成绩
			$paperInfo = $paperModel->find($paperId);
			if($paperInfo['paper_type'] == 'real') {
				$strQuery = 'SELECT paper_id FROM ' . $this->paperTable . '
							 WHERE paper_type=' . $this->dao->quote('real') . '
							   AND exam_id=' . abs($examId) . '
							   AND paper_id !=' . $paperId . '
							   AND subject_code=' . $this->dao->quote($subjectCode) . '
							   AND is_remove=0';
				$otherPaper = $this->dao->getRow($strQuery);
				if($otherPaper) {
					$strQuery = 'DELETE FROM ' . $this->tableName . '
								 WHERE paper_id=' . $otherPaper['paper_id'] . '
								   AND stu_code=' . $this->dao->quote($stuCode);
					$this->dao->execute($strQuery);
				}
			}
			
			#判断是否存在成绩，若存在，则更新，否则添加
			$strQuery = 'SELECT * FROM ' . $this->tableName . '
						 WHERE paper_id=' . abs($paperId) . '
						   AND stu_code=' . $this->dao->quote($stuCode);
			
			$stuScore = $this->dao->getRow($strQuery);
			if($stuScore) {
				$strQuery = 'UPDATE ' . $this->tableName . '
							 SET exam_code=' . $this->dao->quote($examCode) . ',
							 	 paper_char=' . $this->dao->quote($paperChar) . ',
							 	 update_at=' . $this->dao->quote($cTime) . ',
							 	 update_user=' . $this->dao->quote($this->userKey) . ',';
				$totalScore = 0.0;
				foreach ($questions as $quesNum=>$quesScore) {
					$totalScore += $quesScore;
					$strQuery .= 'ques_score_' . abs($quesNum) . '=' . abs($quesScore) . ',';
				}
				$strQuery .= 'paper_total_score=' . abs($totalScore) . '
					WHERE paper_id=' . abs($paperId) . '
					  AND stu_code=' . $this->dao->quote($stuCode);
				$successs &= (boolean)$this->dao->execute($strQuery);
				if(false == $successs) {
					$this->dao->rollback();
					return array('errorMsg'=>'成绩保存失败');
				}
			} else {
				$totalScore = 0.0;
				$columns = array('exam_id', 'stu_code', 'exam_code', 'subject_code','pos_code','paper_id',
								 'paper_char','paper_subject', 'paper_type', 'create_at','update_at','update_user');
				$values = array($examId, 
								$this->dao->quote($stuCode), 
								$this->dao->quote($examCode), 
								$this->dao->quote($subjectCode), 
								$this->dao->quote($scoreInfo['pos_code']), 
								abs($paperId),
								$this->dao->quote($paperChar),
								$this->dao->quote($paperInfo['subject']),
								$this->dao->quote($paperInfo['paper_type']),
								$this->dao->quote($cTime),
								$this->dao->quote($cTime),
								$this->dao->quote($this->userKey));
				foreach ($questions as $quesNum=>$quesScore) {
					$columns[] = 'ques_score_' . abs($quesNum);
					$values[] = abs($quesScore);
					$totalScore+= abs($quesScore);
				}
				$columns[] = 'paper_total_score';
				$values[] = $totalScore;
				$strQuery = 'INSERT INTO ' . $this->tableName . '
							 (' . implode(',', $columns) . ') 
							 VALUES 
							 (' . implode(',', $values) . ')';
			}
			$successs &= (boolean)$this->dao->execute($strQuery);
			if(false == $successs) {
				$this->dao->rollback();
				return array('errorMsg'=>'成绩保存失败');
			}
		}
		$this->dao->commit();
		return true;
		
	}
	
	public function saveQues($ansInfo) {
		$quesModel = D('Question');
		$strQuery = 'SELECT * FROM ' . $this->quesAnsTable . '
					 WHERE exam_id=' . abs($ansInfo['exam_id']) . '
					   AND stu_code=' . $this->dao->quote($ansInfo['stu_code']) . '
					   AND ques_id=' . $this->dao->quote($ansInfo['ques_id']);
		$stuAnswer = $this->dao->getRow($strQuery);
		$ansInfo['stu_answer'] = SysUtil::jsonEncode($ansInfo['stu_answer']);
		$time = date('Y-m-d H:i:s');
		if($stuAnswer) {
			$strQuery = 'UPDATE ' . $this->quesAnsTable . '
						 SET stu_answer=' . $this->dao->quote($ansInfo['stu_answer']) . ',
						  	 update_at=' . $this->dao->quote($time) . '
						 WHERE exam_id=' . abs($ansInfo['exam_id']) . '
						   AND stu_code=' . $this->dao->quote($ansInfo['stu_code']) . '
						   AND ques_id=' . $this->dao->quote($ansInfo['ques_id']);
		} else {
			$strQuery = 'INSERT INTO ' . $this->quesAnsTable . '
						 (exam_id,stu_code,ques_id,stu_answer,create_at,update_at)
						 VALUES (
						 	' . abs($ansInfo['exam_id']) . ',
						 	' . $this->dao->quote($ansInfo['stu_code']) . ',
						 	' . $this->dao->quote($ansInfo['ques_id']) . ',
						 	' . $this->dao->quote($ansInfo['stu_answer']) . ',
						 	' . $this->dao->quote($time) . ',
						 	' . $this->dao->quote($time) . '
						 )';
		}
		if(false == $this->dao->execute($strQuery)) {
			return array('errorMsg'=>'考生试题答案信息保存失败');
		}
		return true;
	}
	
	public function getSubjectScoreCount($examId, $subjectCode, $posCode) {
		$key = $examId . '-' . $subjectCode;
		static $countArray = array();
		if (false == isset($countArray[$key])) {
			$strQuery = 'SELECT count(DISTINCT exam_code ) FROM ' . $this->tableName . '
						 WHERE exam_id=' . $examId . '
						   AND subject_code=' . $this->dao->quote($subjectCode);
			if($posCode) {
				$strQuery .= ' AND pos_code=' . $this->dao->quote($posCode);
			}
			$countArray[$key] = $this->dao->getOne($strQuery);
		}
		return $countArray[$key];
	}
	
	public function getSubjectScoreList($examId, $subjectCode, $posCode,$currentPage, $pageSize, $sort, $order) {
		$recordCount = $this->getSubjectScoreCount($examId, $subjectCode);
		$pageCount = ceil($recordCount / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if ($currentPage < 1) $currentPage = 1;
		$paperModel = D('Paper');
		$paperList = $paperModel->getPaperList($examId);
		$paperArray = array();
		foreach($paperList as $paper) {
			if($paper['subject_code'] == $subjectCode) {
				$paperArray[$paper['paper_type']][] = $paper['paper_id'];
			}
		}
		$strQuery = 'SELECT stu_code,exam_code,paper_char,update_user,update_at,';
		foreach ($paperArray as $paperType=>$paperIds) {
			$strQuery .= 'SUM(CASE WHEN paper_id IN (' . implode(',', $paperIds) . ') THEN paper_total_score ELSE 0 END) ' . $paperType . '_score,';
		}
		$strQuery = substr($strQuery, 0, -1);
		$strQuery .= ' FROM ' . $this->tableName . '
					   WHERE exam_id=' . $examId . '
					   	 AND subject_code=' . $this->dao->quote($subjectCode);
		if($posCode) {
			$strQuery .= ' AND pos_code=' . $this->dao->quote($posCode);
		}
		$strQuery  .= '
					   GROUP BY stu_code,exam_code,paper_char,update_user,update_at';
		$strQuery = 'SELECT stu.sname,stu.saliascode,es.pos_code,pos.pos_caption,score.* 
					 FROM (' . $strQuery . ') score
					 LEFT JOIN ' . $this->stuTable . ' stu
					   ON stu.scode=score.stu_code
					 LEFT JOIN ' . $this->esTable . ' es
					   ON es.exam_id=' . $examId . ' AND es.exam_code=score.exam_code
					 LEFT JOIN ' . $this->posTable . ' pos
					   ON pos.pos_code=es.pos_code';
		if(false == $sort) {
			$order = 'ORDER BY exam_code DESC';
		} else {
			$order = 'ORDER BY ' . $sort . ' ' . $order;
		}
		$scoreList = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		return $scoreList;
	}
	
	private function getScoreCondition($args) {
		$key = md5(serialize($args));
		static $conditions = array();
		if(false == isset($conditions[$key])) {
			if($args['type'] && $args['keyword']) {
				switch ($args['type']) {
					case 'stu_name':
						$conditions[$key] = ' AND score.sname LIKE ' . $this->dao->quote('%' . $args['keyword'] . '%');
					break;
					case 'stu_code':
						$conditions[$key] = ' AND (score.scode=' . $this->dao->quote($args['keyword']) . '
									 	OR score.saliascode=' . $this->dao->quote($args['keyword']) . ')';
					break;
					case 'mobile':
						$conditions[$key] = ' AND score.smobile=' . $this->dao->quote($args['keyword']) . '
									 	OR score.sloginmobile=' . $this->dao->quote($args['keyword']) . '
									    OR score.sparents1phone=' . $this->dao->quote($args['keyword']) . '
									    OR score.sparents2phone=' . $this->dao->quote($args['keyword']);
					break;
					case 'exam_code':
						$conditions[$key] = ' AND score.exam_code=' . $this->dao->quote($args['keyword']);
					break;
					case 'award':
						$awardModel = D('Award');
						$awardInfo = $awardModel->find($args['keyword']);
						$strQuery = 'SELECT * FROM ' . $awardModel->tableName . '
									 WHERE exam_id=' . $awardInfo['exam_id'] . '
									   AND award_type=' . $this->dao->quote($awardInfo['award_type']) . '
									   AND award_score>' . abs($awardInfo['award_score']) . '
									 ORDER BY award_score ASC';
						$maxScore = 9999;
						$prevAward = $this->dao->getRow($strQuery);
						if($prevAward) $maxScore = $prevAward['award_score'] - 0.1;
						$conditions[$key] = $this->getScoreCondition(array('type'=>'between', 'keyword'=>$awardInfo['award_type'] . '_score^' . $awardInfo['award_score'] . '^' . $maxScore));
					break;
					case 'between':
						list($paperType, $min, $max) = explode('^', $args['keyword']);
						$condition = ' AND ' . $paperType . ' BETWEEN ' . $min . ' AND ' . $max;
						$conditions[$key] = $condition;
					break;
					default:
						return '';
					break;
				}
			}
		}
		return $conditions[$key];
	}
	
	public function getExamScoreCount($examId, $args) {
		static $countArray = array();
		$key = md5($examId . '-' .  $args['type'] . '-' . $args['keyword']);
		if(false == isset($countArray[$key])) {
			$condition = $this->getScoreCondition($args);
			$scoreQuery = $this->getScoreQuery($examId);
			$strQuery = 'SELECT count(1) 
						 FROM (' . $scoreQuery . ') score 
						 WHERE score.exam_id=' . $examId . $condition;
			
			$countArray[$key] = $this->dao->getOne($strQuery);
		}
		return $countArray[$key];
	}
	
	public function getExamScoreList($examId, $args, $currentPage, $pageSize, $sort, $order) {
		$recordCout = $this->getExamScoreCount($examId, $args);
		$pageCount = ceil($recordCout / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		if($currentPage < 1) $currentPage = 1;
		
		$strQuery = $this->getSearchQuery($examId, $args);
		$order = 'ORDER BY ' . $sort . ' ' . $order;
		$scoreList = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		$stuModel = D('Student');
		$awardModel = D('Award');
		$rankModel = D('Rank');
		$scoreFields = array();
		if($scoreList) {
			foreach ($scoreList[0] as $field=>$val) {
				if(preg_match('/_score$/', $field)) {
					$scoreFields[] = $field;
				}
			}
		}
		$scanDir = C('SCAN_DIR');
		foreach ($scoreList as $key=>$score) {
			$scoreList[$key]['school_name'] = $stuModel->getSchoolName($score['ngrade1year'], $score['primaryschool'], $score['middleschool']);
			foreach ($scoreFields as $field) {
				$fieldPre = preg_replace('/_score$/', '', $field);
				$awardField =  $fieldPre . '_award';
				$scoreList[$key][$awardField] = $awardModel->getAwardCaption($examId, $fieldPre, $score[$field]);
				$scoreRank = $rankModel->getScoreRank($examId, $fieldPre, $score[$field]);
				$scoreList[$key][$fieldPre . '_rank'] = $scoreRank['real'];
				$scoreList[$key][$fieldPre . '_vrank'] = $scoreRank['virtual'];
				$fileList = glob($scanDir . '/' . $score['exam_id'] . '/*/*' . $score['exam_code'] . '*');
				if(sizeof($fileList) > 0) {
					$scoreList[$key]['scan_files'] = true;
				} else {
					$scoreList[$key]['scan_files'] = false;
				}
			}
		}
		return $scoreList;
	}
	
	public function getScoreQuery($examId) {
		$examId = abs($examId);
		static $queryArray = array();
		if(false == isset($queryArray[$examId])) {
			$paperModel = D('Paper');
			$paperList = $paperModel->getPaperList($examId);
			$paperTypes = array();
			foreach ($paperList as $paper) {
				if($paper['paper_type'] != 'virtual') {
					$paperTypes[$paper['subject'] .'_' . $paper['paper_type']][] = $paper['paper_id'];
				} else {
					#$paperTypes[$paper['subject'] .'_' . $paper['paper_type']][] = $paper['paper_id'];
				}
			}
			$strQuery = 'SELECT stu.sname, stu.scode,stu.saliascode,score.exam_code,stu.smobile,stu.sloginmobile,stu.ngrade1year,
								stu.sparents1phone,stu.sparents2phone,stu.primaryschool,stu.middleschool,
								es.stu_mobile,score.stu_code, score.pos_code,score.exam_id,';
			foreach ($paperTypes as $paperType=>$paperIdArray) {
				if(preg_match('/virtual/i', $paperType)){
				
				} else {
					$strQuery .= 'SUM(CASE WHEN paper_id IN (' . implode(',', $paperIdArray) . ') THEN paper_total_score ELSE 0 END) ' . $paperType . '_score,';
				}
			}
			//附加卷是否参与排名通过此处修改总成绩计算方式
			$strQuery .= 'SUM(CASE WHEN paper_type =' . $this->dao->quote('real') . ' THEN paper_total_score ELSE 0 END) total_score 
						 FROM ' . $this->tableName . ' score
						 LEFT JOIN ' . $this->stuTable . ' stu
						 	ON stu.scode=score.stu_code 
						 	AND stu.bisvalid=1
						 LEFT JOIN ' . $this->esTable . ' es
						 	ON es.exam_id=score.exam_id 
						 	AND es.stu_code=score.stu_code
						 	AND es.is_cancel=0
						 	AND es.order_status !=1
						 WHERE score.exam_id=' . $examId . '
						 GROUP BY score.stu_code,stu.scode,stu.sname,stu.saliascode,score.exam_code,score.stu_code,stu.ngrade1year,
						 		  score.pos_code,stu.smobile,stu.sloginmobile,stu.sparents1phone,stu.sparents2phone,
						 		  stu.primaryschool,stu.middleschool,es.stu_mobile,score.exam_id
						 HAVING SUM(CASE WHEN paper_type =' . $this->dao->quote('real') . ' THEN paper_total_score ELSE 0 END) >0';
			
			$queryArray[$examId] = $strQuery;
		}
		#echo $queryArray[$examId];exit;
		return $queryArray[$examId];
	}
	
	public function simpleQuery($examId) {
		$paperModel = D('Paper');
		$paperList = $paperModel->getPaperList($examId);
		$paperTypes = array();
		foreach ($paperList as $paper) {
			if($paper['paper_type'] != 'virtual') {
				$paperTypes[$paper['subject'] .'_' . $paper['paper_type']][] = $paper['paper_id'];
			} else {
				#$paperTypes[$paper['subject'] .'_' . $paper['paper_type']][] = $paper['paper_id'];
			}
		}
		$strQuery = 'SELECT exam_code,stu_code, pos_code, exam_id,';
		foreach ($paperTypes as $paperType=>$paperIdArray) {
			if(preg_match('/virtual/i', $paperType)){
			
			} else {
				$strQuery .= 'SUM(CASE WHEN paper_id IN (' . implode(',', $paperIdArray) . ') THEN paper_total_score ELSE 0 END) ' . $paperType . '_score,';
			}
		}
		//附加卷是否参与排名通过此处修改总成绩计算方式
		$strQuery .= 'SUM(CASE WHEN paper_type =' . $this->dao->quote('real') . ' THEN paper_total_score ELSE 0 END) total_score 
					 FROM ' . $this->tableName . '
					 WHERE exam_id=' . $examId . '
					 GROUP BY stu_code,exam_code,pos_code,exam_id
					 HAVING SUM(CASE WHEN paper_type =' . $this->dao->quote('real') . ' THEN paper_total_score ELSE 0 END) >0';
		return $strQuery;
	}
	
	public function getSearchQuery($examId, $args) {
		static $queryArray = array();
		$key = md5($examId . '-' . $args['type'] . '-' . $args['keyword']);
		if(false == isset($queryArray[$key])) {
			$condition = $this->getScoreCondition($args);
			$scoreQuery = $this->getScoreQuery($examId);
			$strQuery = 'SELECT * FROM (' . $scoreQuery . ') score WHERE 1=1 ' . $condition;
			$queryArray[$key] = $strQuery;
		}
		
		return $queryArray[$key];
	}
	
	public function getExportHtml($scoreList) {
		
		$columnMap = array('sname'=>'考生姓名',
						   'saliascode'=>array('考生学号', 'vnd.ms-excel.numberformat:@'),
						   'stu_code'=>'考生编码',
						   'exam_code'=>'准考证号',
						   'pos_code'=>'考点编码',
						   'stu_mobile'=>'联系电话',
						   'school_name'=>'所在学校');
		$paperModel = D('Paper');
		$examId = $scoreList[0]['exam_id'];
		$examModel = D('Exam');
		$examInfo = $examModel->find($examId);
		
		foreach ($scoreList[0] as $column=>$value) {
			if(preg_match('/score/', $column)){
				$scoreType = str_replace('_score','', $column);
				$scoreTypeCaption = $paperModel->getVTypeCaption($examId, $scoreType);
				$columnMap[$column] = $scoreTypeCaption;
				$columnMap[$scoreType . '_rank'] = '排名';
				$columnMap[$scoreType . '_vrank'] = '虚排名';
				$columnMap[$scoreType . '_award'] = '获奖';
			}
		}
		
		$table = '<table>
					<caption>
					<b style="font-weight:bold;font-size:15px;color:blue">' . $examInfo['group_caption'] . '-' . $examInfo['exam_caption'] . ' - 成绩导出表</b>
					</caption>
				  <tr>';
		foreach ($columnMap as $column=>$name) {
			if(is_array($name)) {
				$name = $name[0];
			}
			$table .= '<td><b style="color:blue;font-size:12px;text-align:center">' . $name . '</b></td>';
		}
		$table .= '</tr>';
		foreach ($scoreList as $row) {
			$table .= '<tr>';
			foreach ($columnMap as $column=>$name) {
				$style = '';
				if(is_array($name)) {
					$style = $name[1];
				}
				$table .= '<td style="font-size:12px;' . $style . '">' . $row[$column] . '</td>';
			}
			$table .= '</tr>';
		}
		$table .= '</table>';
		$html = file_get_contents(TPL_INCLUDE_PATH . '/export_excel.php');
		$html = str_replace('{{table}}', $table, $html);
		return $html;
	}
	
	public function getScoreData($examId, $stuCode, $subjectCode='') {
		$strQuery = 'SELECT paper_subject,paper_type,paper_total_score
					 FROM ' . $this->tableName . '
					 WHERE exam_id =' . abs($examId) . '
					   AND stu_code=' . $this->dao->quote($stuCode);
		if($subjectCode){
			$strQuery .= ' AND subject_code=' . $this->dao->quote($subjectCode);
		}
		$strQuery .= ' ORDER BY paper_subject,paper_type';
		$scoreList = $this->dao->getAll($strQuery);
		$scoreData = array();
		foreach ($scoreList as $score) {
			$scoreData[$score['paper_subject'] . '_' . $score['paper_type']] = str_replace('.00', '', $score['paper_total_score']);
			if(false == $subjectCode) {
				if($score['paper_type'] != 'addon') {
					$scoreData['total'] = abs($scoreData['total']) + $score['paper_total_score'];
					$scoreData['total'] = str_replace('.00', '', $scoreData['total']);
				}
			}
		}
		return $scoreData;
	}
	
	public function getScoreInfo($paperInfo, $stuCode) {
		$paperArray = array();
		$paperModel = D('Paper');
		$paperArray[$paperInfo['paper_id']] = $paperInfo;
		$examId = abs($paperInfo['exam_id']);
		$strQuery = 'SELECT * FROM ' . $this->tableName . '
					 WHERE exam_id=' . $examId . '
					   AND paper_subject=' . $this->dao->quote($paperInfo['subject']) . '
					   AND stu_code=' . $this->dao->quote($stuCode);
		$scoreList = $this->dao->getAll($strQuery);
		$scoreArray = array();
		foreach ($scoreList as $score) {
			$paperId = $score['paper_id'];
			if(false == isset($paperArray[$paperId])) {
				$paperArray[$paperId] = $paperModel->find($paperId);
			}
			$paperArray[$paperId]['score_info'] = $score;
			$paperInfo = $paperArray[$paperId];
			if($paperInfo['paper_type'] == 'real') {
				$paperArray[$paperId]['ques_ratios'] = $this->getQuesRatios($paperInfo['exam_id'], $paperInfo['subject_code'], true);
			}
		}
		return $paperArray;
	}
	
	public function getQuesRatios($examId, $subjectCode, $isFront=false) {
		$examId = abs($examId);
		$subjectCode = SysUtil::safeString($subjectCode);
		$strQuery = 'SELECT * FROM ' . $this->quesRatioTable . '
					 WHERE exam_id=' . abs($examId) . '
					   AND subject_code=' . $this->dao->quote($subjectCode);
		$dbQuesRatio = $this->dao->getRow($strQuery);
		if($dbQuesRatio && $isFront) {
			return unserialize($dbQuesRatio['ques_ratios']);
		}
		$quesList = $this->getQuesList($examId, $subjectCode);
		$quesArray = array();
		$quesRatios = array();
		foreach ($quesList as $ques) {
			$quesKey = md5($ques['ques_id']);
			$quesArray[$quesKey][$ques['paper_id']][] = 'ques_score_' . $ques['ques_seq'];
			if(false == isset($quesRatios[$quesKey])) {
				$quesRatios[$quesKey] = array('ques_id'=>$ques['ques_id'], 
											  'paper_type'=>$ques['paper_type'], 
											  'part_id'=>$ques['part_id'],
											  'part_caption'=>$ques['part_caption'],
											  'ques_score'=>$ques['ques_score'], 
											  'ques_sumary'=>$ques['ques_sumary'],
											  'module_caption'=>$ques['module_caption'],
											  'knowledge_caption'=>$ques['knowledge_caption'],
											  'ques_level'=>$ques['ques_level'],
											  'level_caption'=>str_repeat('★', abs($ques['ques_level'])),
											  'ques_caption'=>$ques['ques_caption'],
											  'ques_average'=>'0.00',
											  'ques_ratio'=>'0.00');
			}
			
			if($ques['paper_type'] == 'real') {
				$quesRatios[$quesKey]['ques_num'][] = $ques['paper_char'] . '(' . $ques['ques_seq'] . ')';
			} else {
				$quesRatios[$quesKey]['ques_num'][] = $ques['ques_seq'];
			}
			$quesRatios[$quesKey]['ques_num_text'] = implode(',' , $quesRatios[$quesKey]['ques_num']);
		}
		
		$strQuery = 'SELECT COUNT(DISTINCT stu_code) stu_cnt';
		$paperIds = array(0);
		foreach ($quesArray as $quesKey=>$paperQuestions) {
			$strQuery .= ',SUM(CASE ';
			foreach ($paperQuestions as $paperId=>$questions) {
				foreach ($questions as $ques) {
					$strQuery .= ' WHEN paper_id=' . $paperId . ' THEN ' . $ques;
				}
				$paperIds[$paperId] = $paperId;
			}
			$strQuery .= ' END) ques_' . $quesKey;
		}
		
		$strQuery .= ' FROM ' . $this->tableName . '
					 WHERE exam_id=' . $examId . '
					   AND paper_id IN (' . implode(',', $paperIds) . ')';
		$quesScoreInfo = $this->dao->getRow($strQuery);
		if(false == $quesScoreInfo['stu_cnt']) return array_values($quesRatios);
		foreach ($quesRatios as $quesKey=>$quesCfg) {
			$quesRatios[$quesKey]['ques_average'] = sprintf('%.2f', $quesScoreInfo['ques_' . $quesKey] / $quesScoreInfo['stu_cnt']);
			$quesRatios[$quesKey]['ques_ratio'] = sprintf('%.2f', $quesScoreInfo['ques_' . $quesKey] / ($quesCfg['ques_score'] * $quesScoreInfo['stu_cnt']) * 100);
		}
		$time = date('Y-m-d H:i:s');
		if($dbQuesRatio) {
			$strQuery = 'UPDATE ' . $this->quesRatioTable . '
						 SET ques_ratios=' . $this->dao->quote(serialize($quesRatios)) . ',
						     update_at=' . $this->dao->quote($time) . '
						 WHERE exam_id=' . $examId . '
						   AND subject_code=' . $this->dao->quote($subjectCode);
		} else {
			$strQuery = 'INSERT INTO ' . $this->quesRatioTable . '
						 (exam_id, subject_code, ques_ratios, create_at, update_at)
						 VALUES (' . $examId . ',
						 		 ' . $this->dao->quote($subjectCode) . ',
						 		 ' . $this->dao->quote(serialize($quesRatios)) . ',
						 		 ' . $this->dao->quote($time) . ',
						 		 ' . $this->dao->quote($time) . ')';
		}
		$this->dao->execute($strQuery);
		return $quesRatios;
	}
	
	public function getQuesList($examId, $subjectCode) {
		$strQuery = 'SELECT DISTINCT paper.paper_type,paper.paper_char,part.part_caption,ques.paper_id,ques.part_id,ques.ques_id,
							ques.ques_seq,ques.ques_score,eques.ques_sumary,knowledge.module_caption,knowledge.knowledge_caption,
							part.part_id,part.part_caption,ques.ques_level
					 FROM ' . $this->paperTable . ' paper,
					 	  ' . $this->examQuesTable . ' eques,
						  ' . $this->quesTable . ' ques
					 LEFT JOIN ' . $this->partTable . ' part
					   ON  part.part_id=ques.part_id 
					   AND ques.paper_id=part.paper_id
					 LEFT JOIN ' . $this->quesKnowledgeTable . ' knowledge
					   ON  knowledge.ques_id=ques.ques_id 
					   AND ques.ques_knowledge=knowledge.knowledge_code
					 WHERE paper.paper_id=ques.paper_id
					   AND eques.ques_id=ques.ques_id
					   AND paper.exam_id=' . $examId . '
					   AND paper.subject_code=' . $this->dao->quote($subjectCode) .  '
					   AND ques.is_remove=0
					 ORDER BY paper.paper_type DESC,paper.paper_char ASC,ques.ques_seq';
		$quesList = $this->dao->getAll($strQuery);
		return $quesList;
	}
	
	public function getStuAnswers($stuCode, $quesIdArray) {
		$strQuery = 'SELECT ques_id,stu_answer FROM ' . $this->quesAnsTable . '
					 WHERE stu_code=' . $this->dao->quote($stuCode) . '
					   AND ques_id IN (\'' . implode("','", $quesIdArray) . '\')';
		$ansList = $this->dao->getAll($strQuery);
		$ansArray = array();
		foreach ($ansList as $ans) {
			$ansArray[$ans['ques_id']] = SysUtil::jsonDecode($ans['stu_answer']);
		}
		return $ansArray;
	}
	
	public function countStuScore($examId, $stuCode) {
		$strQuery = 'SELECT COUNT(1) cnt FROM ' . $this->tableName . '
					 WHERE exam_id=' . abs($examId) . '
					   AND stu_code=' . $this->dao->quote($stuCode);
		return $this->dao->getOne($strQuery);
	}
	
	public function delScore($examId, $stuCode, $subjectCode) {
		$strQuery = 'DELETE FROM ' . $this->tableName . '
					 WHERE exam_id=' . abs($examId) . '
					   AND stu_code=' . $this->dao->quote($stuCode) . '
					   AND subject_code=' . $this->dao->quote($subjectCode);
		
		return $this->dao->execute($strQuery);
	}
	
	public function updateCache() {
		import('ORG.Util.NCache');
		$cache = NCache::getCache();
		$cache->delete('PaperAnalys');
		$cache->delete('ExamInfo');
		$cache->delete('ScoreData');
		$cache->delete('StuScoreClass');
		$cache->delete('examScore');
		$cache->delete('ExamRank');
		$cache->delete('paperQuestions');
		$cache->delete('PaperExamCount');
		$cache->delete('AwardVCount');
		$cache->delete('ScoreDist');
		$cache->delete('fScoreData');
		$tables = array(
			'ex_ques_ratios',
			'ex_module_cfg',
			'ex_knowledge_cfg',
			'ex_part_cfg',
			'ex_step_cfg',
			'ex_level_cfg',
			'ex_report_html',
			'ex_chart_json',
		);
		foreach ($tables as $table) {
			$strQuery = 'truncate table ' . $table;
			$this->dao->execute($strQuery);
		}
		return true;
	}
	
}
?>