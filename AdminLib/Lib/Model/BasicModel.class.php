<?php
class BasicModel extends Model {
	public $dao = null;
	// protected $vip_dict = 'vip_dict';
	// protected $vip_dict_grade = 'vip_dict_grade';
	// protected $vip_dict_label = 'vip_dict_label';
	protected $vip_knowledge = 'vip_knowledge';
	protected $vip_knowledge_course_type_rs = 'vip_knowledge_course_type_rs';
	protected $vip_view_knowledge = 'vip_view_knowledge';
	// protected $vip_question_type_attr = 'vip_question_type_attr';
	// protected $vip_view_question_type = 'vip_view_question_type';
	// protected $vip_label = 'vip_label';
	// protected $vip_label_course_type_rs = 'vip_label_course_type_rs';
	// protected $vip_view_label = 'vip_view_label';
	// protected $vip_paper = 'vip_paper';
	protected $vip_question = 'vip_question';
	protected $vip_paper = 'vip_paper';
	protected $vip_question_answer = 'vip_question_answer';
	protected $vip_question_option = 'vip_question_option';
	// protected $vip_paper_question = 'vip_paper_question';

	// 变动后数据表===========================================================
	protected $vip_dict = 'vip_dict';
	protected $vip_dict_grade = 'vip_dict_grade';
	protected $vip_dict_subject = 'vip_dict_subject';
	protected $vip_dict_course_type = 'vip_dict_course_type';
	protected $vip_dict_question_type = 'vip_dict_question_type';

	/*edit by xcp*/
	protected $vip_dict_knowledge_type = 'vip_dict_knowledge_type';
	protected $vip_dict_subject_knowledgetype_rs = 'vip_dict_subject_knowledgetype_rs';

	/**/
	protected $atf_citys="atf_citys";

	//新加表
	protected $vip_test="vip_test";
	protected $vip_grades_subject="vip_grades_subject";
	protected $vip_fourlevel_system = "vip_fourlevel_system";
	protected $vip_view_fourlevel = "vip_view_fourlevel";
	protected $vip_fourlevel_subject_rs = "vip_fourlevel_subject_rs";



	public function __construct() {
		$this->dao = Dao::getDao ( 'MYSQL_CONN_KNOWLEDGE' );
	}
	public function addKnowledge($knowledge = array()) {
		$parentId = $knowledge ['parent_id'];
		$flag = true;
		$this->dao->execute ( 'begin' ); // 事务开启
		if (! empty ( $parentId )) {
			$sql = 'UPDATE ' . $this->vip_knowledge . ' SET is_leaf = 0 WHERE id = ' . $this->dao->quote ( $parentId ) . ' AND is_leaf = 1';
			if ($this->dao->execute ( $sql ))
			$flag = true;
			else
			$flag = false;
		}

		if ($flag == true) {
			$is_gaosi = $this->getKnowledgeTypeIsGaosiById($knowledge ['knowledgetypeid']);
			$sql2 = 'INSERT INTO ' . $this->vip_knowledge . ' (name, remark, parent_id, analysis, analysis3, sort, is_leaf, level, is_gaosi) VALUES (' . $this->dao->quote ( $knowledge ['name'] ) . ', ' . $this->dao->quote ( $knowledge ['remark'] ) . ', ' . $this->dao->quote ( $parentId ) . ', ' . $this->dao->quote ( $knowledge ['analysis'] ) . ', ' . $this->dao->quote ( $knowledge ['analysis3'] ) . ', ' . $this->dao->quote ( $knowledge ['sort'] ) . ', 1, ' . $this->dao->quote ( $knowledge ['level'] ) . ', ' . $this->dao->quote ( $is_gaosi ) . ')';
			if ($this->dao->execute ( $sql2 )) {
				$id = $this->dao->lastInsertId ();
				$flag = true;
			} else
			$flag = false;
		}

		// 如为父节点则插入知识点属性表
		if (empty ( $parentId ) && $flag == true) {
			$sql3 = 'INSERT INTO ' . $this->vip_knowledge_course_type_rs . ' (knowledge_id, course_type_id) VALUES (' . $this->dao->quote ( $id ) . ', ' . $this->dao->quote ( $knowledge ['coursetypeid'] ) . ')';

			if ($this->dao->execute ( $sql3 ))
			$flag = true;
			else
			$flag = false;
		}

		if ($flag === false)
		$this->dao->execute ( 'rollback' ); // 事务回滚
		else
		$this->dao->execute ( 'commit' ); // 事务提交

		return $flag;
	}
	public function updateKnowledge($knowledge = array()) {
		$knowledgeId = $knowledge ['id'];
		$parentId = $knowledge ['parent_id'];
		$row = $this->dao->getRow ( 'SELECT fn_vip_get_knowledge_child_list(' . $this->dao->quote ( $knowledgeId ) . ') AS sub_knowledge_ids' );

		if ($row) {
			if (in_array ( $parentId, str2arr ( $row ['sub_knowledge_ids'], ',' ) )) {
				return false;
			}
		}

		if (! empty ( $knowledgeId )) {
			$before_parentId = $this->dao->getRow ( 'SELECT `id`,`parent_id` FROM ' . $this->vip_knowledge . ' WHERE id = ' . $this->dao->quote ( $knowledgeId ) ); // 查找修改前的父目录的ID
			if (! empty ( $before_parentId ['parent_id'] )) {
				if ($before_parentId ['parent_id'] != $parentId) {
					$row = $this->dao->getRow ( 'SELECT `id`,`parent_id` FROM ' . $this->vip_knowledge . ' WHERE id != ' . $this->dao->quote ( $knowledgeId ) . ' AND  parent_id = ' . $this->dao->quote ( $before_parentId ['parent_id'] ) );
					if (empty ( $row )) { // 查找对应的父节点除了此节点之外还有没有子节点，如果无则修改叶子节点
						$sql = 'UPDATE ' . $this->vip_knowledge . ' SET is_leaf = 1 WHERE id = ' . $this->dao->quote ( $before_parentId ['parent_id'] );
						$this->dao->execute ( $sql );
					}
					if(!empty($parentId)){
						$parentLevel = $this->dao->getOne('SELECT level FROM '.$this->vip_knowledge.' WHERE id = '.$this->dao->quote($parentId));
						$knowledge['level'] = $parentLevel+1;
					}else{
						$knowledge['level'] = 1;
					}

				}
			}
		}

		if (! empty ( $parentId )) {
			$sql = 'UPDATE ' . $this->vip_knowledge . ' SET is_leaf = 0 WHERE id = ' . $this->dao->quote ( $parentId ) . ' AND is_leaf = 1';
			$this->dao->execute ( $sql );
		}

		return $this->dao->execute ( 'UPDATE ' . $this->vip_knowledge . ' SET name = ' . $this->dao->quote ( $knowledge ['name'] ) . ', remark = ' . $this->dao->quote ( $knowledge ['remark'] ) . ', sort = ' . $this->dao->quote ( $knowledge ['sort'] ) . ', parent_id = ' . $this->dao->quote ( $knowledge ['parent_id'] ) . ', analysis = ' . $this->dao->quote ( $knowledge ['analysis'] ) . ',analysis3 = ' . $this->dao->quote ( $knowledge ['analysis3'] ) . ',level='.$this->dao->quote ( $knowledge ['level'] ).' WHERE id = ' . $this->dao->quote ( $knowledge ['id'] ) );
	}
	public function getKnowledgeByID($id) {
		return $this->dao->getRow ( 'SELECT a.`id`,
											a.`name`,
											a.`remark`,
											a.`sort`,
											a.`parent_id`,
											a.`analysis`,
											a.`status`,
											a.`level`,
											a.`analysis3`,
											b.name as parent_name 
									FROM ' . $this->vip_knowledge . ' a 
									LEFT JOIN ' . $this->vip_knowledge . ' b ON a.parent_id = b.id 
									WHERE a.id = ' . $this->dao->quote ( $id ) );
	}
	public function getKnowledgeByIDs($ids) {
		if (empty ( $ids ))
		return array ();

		return $this->dao->getAll ( 'SELECT `id`,
											`name`, 
											`remark`, 
											`sort`,
											`parent_id`,
											`analysis`,
											`analysis3`,
											`status`
									FROM ' . $this->vip_knowledge . ' WHERE id IN ( ' . $ids . ')' );
	}
	public function getKnowledgesByCourseTypeId($courseTypeId) {
		$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND a.course_type_id = ' . $this->dao->quote ( $courseTypeId ) . ' ORDER BY a.sort' );

		$rootIds = arr2nav ( $rows, ',', 'id' );

		$row = $this->dao->getRow ( 'SELECT fn_vip_get_knowledge_child_list(\'' . $rootIds . '\') AS ids' );
		if ($row) {
			$ids = $row ['ids'];
			if ($ids != '$,') {
				$ids = str_replace ( ',', "','", $ids );
				return $this->dao->getAll ( 'SELECT a.`id`,
											a.`name`,
											a.`remark`,
											a.`sort`,
											a.`parent_id`,
											a.`analysis`,
											a.`status`
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE id IN(\'' . $ids . '\') ORDER BY a.sort, a.id' );
			}
			return array ();
		}
		return array ();
	}
	public function deleteKnowledgeByID($id) {
		return $this->dao->execute ( 'UPDATE ' . $this->vip_knowledge . ' SET status = -1 WHERE id = ' . $this->dao->quote ( $id ) );
	}
	public function addQuestionType($data = array()) {
		$flag = false;
		$subjectId = $data ['subjectid'];
		if ($subjectId && ! empty ( $data ['title'] ) && ! empty ( $data ['code'] )) {
			$typeCodeArr = array_filter ( $data ['code'] );
			$titleArr = array_filter ( $data ['title'] );
			$sortArr = array_filter ( $data ['sort'] );
			$flag = true;
			$this->dao->execute ( 'begin' ); // 事务开启
			foreach ( $data ['title'] as $key => $title ) {
				$sql = 'INSERT INTO ' . $this->vip_dict_question_type . ' (subject_id, title, question_type_code, sort) VALUES(' . $this->dao->quote ( $subjectId ) . ',' . $this->dao->quote ( SysUtil::safeString ( $title ) ) . ',' . $this->dao->quote ( $typeCodeArr [$key] ) . ',' . $this->dao->quote ( $sortArr [$key] ) . ')';
				if ($this->dao->execute ( $sql )) {
					$flag = true;
				} else {
					$flag = false;
				}
			}
			if ($flag === false) {
				$this->dao->execute ( 'rollback' ); // 事务回滚
			} else {
				$this->dao->execute ( 'commit' ); // 事务提交
			}
		}
		return $flag;
	}
	public function getQuestionTypeByID($id) {
		return $this->dao->getRow ( 'SELECT  id, subject_id, title, status, sort,question_type_code
									 FROM ' . $this->vip_dict_question_type . '
									 WHERE id = ' . $this->dao->quote ( $id ) );
	}
	public function getQuestionTypesFullBySubjectId($subjectId) {
		return $this->dao->getAll ( 'SELECT a.id, a.code, a.title AS origin_title, CASE WHEN b.title = \'\' THEN a.title ELSE b.title END AS title, CASE WHEN b.id IS NULL THEN 0 ELSE 1 END is_choose
									 FROM ' . $this->vip_dict . ' a
									 LEFT JOIN ( SELECT id, subject_id, question_type_code, title, status, sort FROM ' . $this->vip_dict_question_type . ' WHERE subject_id = ' . $this->dao->quote ( $subjectId ) . ') b ON a.code = b.question_type_code
									 WHERE a.category = \'QUESTION_TYPE\' AND a.status = 1 ORDER BY a.sort, a.id' );
	}
	public function updateQuestionType($questionType = array()) {
		return $this->dao->execute ( 'UPDATE ' . $this->vip_dict_question_type . ' SET title = ' . $this->dao->quote ( $questionType ['title'] ) . ', sort = ' . $this->dao->quote ( $questionType ['sort'] ) . ' WHERE id = ' . $this->dao->quote ( $questionType ['id'] ) );
	}
	public function deleteQuestionTypeByID($id) {
		return $this->dao->execute ( 'DELETE FROM ' . $this->vip_question_type_attr . ' WHERE id = ' . $this->dao->quote ( $id ) );
	}
	public function getDictsAllByCategory($condition) {
		$where = '';
		if ($condition ['cate'] == 'subject' && ! empty ( $condition ['xueke'] )) {
			$where .= ' AND code IN (' . $condition ['xueke'] . ') ';
		}
		if ($condition ['cate'] == 'grade_dept' && ! empty ( $condition ['xuebu'] )) {
			$where .= ' AND code IN (' . $condition ['xuebu'] . ') ';
		}
		return $this->dao->getAll ( 'SELECT `id`,
											`category`,
											`code`,
											`title`,
											`description`,
											`sort`,
											`status`
									 FROM ' . $this->vip_dict . '
									 WHERE category = ' . $this->dao->quote ( $condition ['cate'] ) . ' AND status = 1 ' . $where . ' ORDER BY sort, id' );
	}
	public function getDictsByCategory($category, $currentPage, $pageSize, $sort, $order) {
		$currentPage = empty ( $currentPage ) ? 1 : $currentPage;
		$pageSize = empty ( $pageSize ) ? 20 : $pageSize;

		$list = $this->dao->getAll ( 'SELECT `id`,
											`category`,
											`code`,
											`title`,
											`description`,
											`sort`,
											`status`
									 FROM ' . $this->vip_dict . '
									 WHERE category = ' . $this->dao->quote ( $category ) . ' AND status = 1
									 ORDER BY sort
									 limit ' . ($currentPage - 1) * $pageSize . ', ' . $pageSize );

		$total = 0;
		if ($list) {
			$row = $this->dao->getRow ( 'SELECT COUNT(*) AS cnt
									 FROM ' . $this->vip_dict . '
									 WHERE category = ' . $this->dao->quote ( $category ) . ' AND status = 1' );
			if ($row) {
				$total = $row ['cnt'];
			}
		}
		return array (
		'total' => $total,
		'rows' => $list
		);
	}
	public function addDict($dict = array()) {
		return $this->dao->execute ( 'INSERT INTO ' . $this->vip_dict . ' (category, code, title, description, sort) VALUES (' . $this->dao->quote ( $dict ['category'] ) . ', ' . $this->dao->quote ( $dict ['code'] ) . ', ' . $this->dao->quote ( $dict ['title'] ) . ', ' . $this->dao->quote ( $dict ['description'] ) . ', ' . $this->dao->quote ( $dict ['sort'] ) . ')' );
	}
	public function add($dict = array()) {
		switch ($dict ['category']) {
			case 'SUBJECT' :
				$result = $this->dao->execute ( 'INSERT INTO ' . $this->vip_dict_subject . ' (grade_id, title, sort) VALUES(' . $this->dao->quote ( abs ( $dict ['grade_id'] ) ) . ',' . $this->dao->quote ( SysUtil::safeString ( $dict ['title'] ) ) . ',' . $this->dao->quote ( abs ( $dict ['sort'] ) ) . ')' );
				break;
			case 'QUESTION_TYPE' :
				$result = $this->dao->execute ( 'INSERT INTO ' . $this->vip_dict_question_type . ' (subject_id, title, sort) VALUES(' . $this->dao->quote ( abs ( $dict ['subject_id'] ) ) . ',' . $this->dao->quote ( SysUtil::safeString ( $dict ['title'] ) ) . ',' . $this->dao->quote ( abs ( $dict ['sort'] ) ) . ')' );
				break;
			case 'KNOWLEDGE_TYPE' :
				$result = $this->dao->execute('INSERT INTO '. $this->vip_dict_knowledge_type . ' (subject_id, title, sort) VALUES (' . $this->dao->quote ( abs ( $dict ['subject_id'] ) ) . ',' .$this->dao->quote ( SysUtil::safeString ( $dict ['title'] ) ). ' , ' . $this->dao->quote(abs ( $dict ['sort'] )) . ')');
				break;
			case 'COURSE_TYPE' :
				$result = $this->dao->execute ( 'INSERT INTO ' . $this->vip_dict_course_type . ' (subject_id, title, sort, knowledge_type_id) VALUES(' . $this->dao->quote ( abs ( $dict ['subject_id'] ) ) . ',' . $this->dao->quote ( SysUtil::safeString ( $dict ['title'] ) ) . ',' . $this->dao->quote ( abs ( $dict ['sort'] ) ) . ',' . $this->dao->quote ( abs ( $dict ['knowledge_type_id'] ) ) . ')' );
				break;
			default :
				$result = $this->dao->execute ( 'INSERT INTO ' . $this->vip_dict_grade . ' (title,sort) VALUES(' . $this->dao->quote ( SysUtil::safeString ( $dict ['title'] ) ) . ',' . $this->dao->quote ( abs ( $dict ['sort'] ) ) . ')' );
		}

		return $result;
	}
	public function getDictByID($id) {
		return $this->dao->getRow ( 'SELECT id, category, code, title, description, sort, status FROM ' . $this->vip_dict . ' WHERE id = ' . $this->dao->quote ( $id ) );
	}
	public function getDictDataByID($cate, $id) {
		switch ($cate) {
			case 'GRADE_DEPT' :
				return $this->dao->getRow ( 'SELECT id,title,sort FROM ' . $this->vip_dict_grade . ' WHERE id = ' . $this->dao->quote ( abs ( $id ) ) );
				break;
			case 'SUBJECT' :
				return $this->dao->getRow ( 'SELECT id,grade_id,title,sort FROM ' . $this->vip_dict_subject . ' WHERE id = ' . $this->dao->quote ( abs ( $id ) ) );
				break;
			case 'COURSE_TYPE' :
				return $this->dao->getRow ( 'SELECT c.id,c.subject_id,c.title,c.sort,s.grade_id FROM ' . $this->vip_dict_course_type . ' c LEFT JOIN ' . $this->vip_dict_subject . ' s ON c.subject_id = s.id WHERE c.id = ' . $this->dao->quote ( abs ( $id ) ) );
				break;
			case 'QUESTION_TYPE' :
				return $this->dao->getRow ( 'SELECT q.id,q.subject_id,q.title,q.sort,s.grade_id FROM ' . $this->vip_dict_question_type . ' q LEFT JOIN ' . $this->vip_dict_subject . ' s ON q.subject_id = s.id WHERE q.id = ' . $this->dao->quote ( abs ( $id ) ) );
				break;
			case 'KNOWLEDGE_TYPE' :
				return $this->dao->getRow ( 'SELECT kt.id, kt.title, kt.sort FROM ' . $this->vip_dict_knowledge_type . ' kt WHERE kt.id = ' . $this->dao->quote ( abs ( $id ) ) );
				break;
		}
	}
	public function updateDict($dict = array()) {
		return $this->dao->execute ( 'UPDATE ' . $this->vip_dict . ' SET title = ' . $this->dao->quote ( $dict ['title'] ) . ', description = ' . $this->dao->quote ( $dict ['description'] ) . ', sort = ' . $this->dao->quote ( $dict ['sort'] ) . ' WHERE id = ' . $this->dao->quote ( $dict ['id'] ) );
	}
	public function update($dict = array()) {
		$setValues = '';
		switch ($dict ['category']) {
			case 'GRADE_DEPT' :
				$tempTable = $this->vip_dict_grade;
				break;
			case 'SUBJECT' :
				$tempTable = $this->vip_dict_subject;
				$setValues .= ',grade_id = ' . $this->dao->quote ( $dict ['grade_id'] );
				break;
			case 'COURSE_TYPE' :
				$tempTable = $this->vip_dict_course_type;
				$setValues .= ',subject_id = ' . $this->dao->quote ( $dict ['subject_id'] );
				break;
			case 'QUESTION_TYPE' :
				$tempTable = $this->vip_dict_question_type;
				$setValues .= ',subject_id = ' . $this->dao->quote ( $dict ['subject_id'] );
				break;
			case 'KNOWLEDGE_TYPE' :
				$tempTable = $this->vip_dict_knowledge_type;
				break;
		}
		return $this->dao->execute ( 'UPDATE ' . $tempTable . ' SET title = ' . $this->dao->quote ( $dict ['title'] ) . ', sort = ' . $this->dao->quote ( $dict ['sort'] ) . $setValues . ' WHERE id = ' . $this->dao->quote ( $dict ['id'] ) );
	}
	public function deleteDictByID($id) {
		return $this->dao->execute ( 'UPDATE ' . $this->vip_dict . ' SET status = -1 WHERE id = ' . $this->dao->quote ( $id ) );
	}
	public function getDictByCategoryAndCode($category, $code) {
		return $this->dao->getRow ( 'SELECT id, category, code, title, description, sort, status
									 FROM ' . $this->vip_dict . '
									 WHERE category = ' . $this->dao->quote ( $category ) . ' AND code = ' . $this->dao->quote ( $code ) . ' AND status = 1' );
	}
	public function addQuestion($model = array()) {
		/*
		* $uid = String::uuid (); $uid = str_replace ( '{', '', $uid ); $uid = str_replace ( '}', '', $uid ); $uid = str_replace ( '-', '', $uid );
		*/
		$this->dao->execute ( 'INSERT INTO ' . $this->vip_question . ' (
													`uid`,
													`course_type_id`,
													`question_type_id`,
													`difficulty`,
													`knowledge_id`,
													`sub_knowledge_id`,
													`grades`,
													`content`,
													`content_text`,
													`analysis`,
													`parent_id`,
													`status`,
													`created_user_name`,
													`created_time`,
													`last_updated_time`,
													`sdate`) VALUES (' . $this->dao->quote ( $model ['uid'] ) . ', ' . $this->dao->quote ( $model ['course_type_id'] ) . ', ' . $this->dao->quote ( $model ['question_type_id'] ) . ', ' . $this->dao->quote ( $model ['score'][0] ) . ', ' . $this->dao->quote ( $model ['knowledge_id'] ) . ', ' . $this->dao->quote ( $model ['sub_knowledge_id'] ) . ', ' . $this->dao->quote ( $model ['grades'] ) . ', ' . $this->dao->quote ( $model ['content'] ) . ', ' . $this->dao->quote ( strip_tags ( $model ['content'] ) ) . ', ' . $this->dao->quote ( $model ['analysis'] ) . ', ' . $this->dao->quote ( $model ['parent_id'] ) . ', 1' . ', ' . $this->dao->quote ( $model ['user_name'] ) . ', ' . $this->dao->quote ( strtotime ( date ( "Y-m-d H:i:s" ) ) ) . ', ' . $this->dao->quote ( strtotime ( date ( "Y-m-d H:i:s" ) ) ) . ',' . $this->dao->quote ( $model ['sdate'] ) . ')' );

		$questionId = $this->dao->lastInsertId ();
		// 选项
		$options = $model ['options'];
		$euids = $model ['euids'];
		if (! empty ( $options )) {
			$answers = $model ['options_answer_flag'];
			for($i = 0; $i < count ( $options ); $i ++) {
				$this->dao->execute ( 'INSERT INTO ' . $this->vip_question_option . ' (
														`uid`,
														`question_id`,
														`content`,
														`sort`,
														`is_answer`) VALUES (' . $this->dao->quote ( $euids [$i] ) . ', ' . $this->dao->quote ( $questionId ) . ', ' . $this->dao->quote ( $options [$i] ) . ', ' . $this->dao->quote ( ($i + 1) ) . ', ' . $this->dao->quote ( in_array ( $i, $answers ) ? 1 : 0 ) . ')' );
			}
		}
		// 答案
		$answers = $model ['answers'];
		if (! empty ( $answers )) {
			for($i = 0; $i < count ( $answers ); $i ++) {
				$this->dao->execute ( 'INSERT INTO ' . $this->vip_question_answer . ' (
														`question_id`,
														`content`,
														`sort`) VALUES (' . $this->dao->quote ( $questionId ) . ', ' . $this->dao->quote ( $answers [$i] ) . ', ' . $this->dao->quote ( $answers [$i] ['sort'] ) . ')' );
			}
		}

		return array (
		'id' => $questionId
		);
	}
	public function deleteQuestionById($id) {
		return $this->dao->execute ( 'UPDATE ' . $this->vip_question . ' SET status = -1 WHERE id = ' . $this->dao->quote ( $id ) );
	}
	public function getQuestionByID($id) {
		return $this->dao->getRow ( 'SELECT `id`,
										    `uid`,
										    `course_type_id`,
										    `question_type_id`,
										    `difficulty`,
										    `knowledge_id`,
										    `sub_knowledge_id`,
										    `grades`,
										    `content`,
										    `analysis`,
										    `parent_id`,
										    `status`,
										    `created_user_name`,
										    `created_time`,
										    `last_updated_user_name`,
										    `last_updated_time`,
										    `sdate`
										     FROM ' . $this->vip_question . ' WHERE id = ' . $this->dao->quote ( $id ) );
	}
	public function getQuestionFullByID($id) {
		$row = $this->dao->getRow ( 'SELECT `id`,
										    `course_type_id`,
										    `question_type_id`,
										    `difficulty`,
										    `knowledge_id`,
										    `sub_knowledge_id`,
										    `grades`,
										    `content`,
										    `analysis`,
										    `parent_id`,
										    `status`,
										    `created_user_name`,
										    `created_time`,
										    `last_updated_user_name`,
										    `last_updated_time`
										     FROM ' . $this->vip_question . ' WHERE id = ' . $this->dao->quote ( $id ) );

		$subs = $this->getSubQuestionsByQuestionIds ( $id );
		if ($subs) {
			$row ['subs'] = $subs;
		}

		return $row;
	}
	public function getPaperQuestionFullByID($id) {
		$row = $this->dao->getRow ( ' SELECT a.*,
											 b.`name` AS `knowledge_name`,
											 fn_vip_get_sub_knowledge_name(a.sub_knowledge_id) AS `sub_knowledge_names`,
											 d.`title` AS `course_type_name`,
											 d.`subject_id`,
											 e.`title` AS `subject_name`,
											 f.`title` AS `grade_name`,
											 g.`question_type_code`,
											 h.`title` AS `question_type_name`,
											 fn_vip_get_grade_name(a.grades) AS `grade_names`,
										     aa.file_name
										FROM (SELECT * FROM ' . $this->vip_question . ' a
															WHERE a.status = 1 
															AND a.parent_id = 0 
															and a.id = ' . $this->dao->quote ( $id ) . ') a
										LEFT JOIN ' . $this->vip_paper . ' aa ON a.paper_id = aa.id
										LEFT JOIN ' . $this->vip_knowledge . ' b ON a.knowledge_id = b.id
										LEFT JOIN ' . $this->vip_dict_course_type . ' d ON a.course_type_id = d.id
										LEFT JOIN ' . $this->vip_dict_subject . ' e ON d.subject_id = e.id
										LEFT JOIN ' . $this->vip_dict_grade . ' f ON e.grade_id = f.id
										LEFT JOIN ' . $this->vip_dict_question_type . ' g ON g.id = a.question_type_id
										LEFT JOIN ' . $this->vip_dict . ' h ON g.question_type_code = h.code AND h.category = \'QUESTION_TYPE\'');
	
		if ($row) {
			$questionIds = array ();
			$questionIds [] = $row ['id'];

			$options = $this->getOptionsByQuestionIds ( arr2str ( $questionIds ) );
			$answers = $this->getAnswersByQuestionIds ( arr2str ( $questionIds ) );

			$questionOptions = array ();
			$questionAnswers = array ();

			foreach ( $options as $option ) {
				if ($option ['question_id'] == $row ['id']) {
					$option ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $option ['content'] );
					$questionOptions [] = $option;
				}
			}
			foreach ( $answers as $answer ) {
				if ($answer ['question_id'] == $row ['id']) {
					$answer ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $answer ['content'] );
					$questionAnswers [] = $answer;
				}
			}

			$row ['content'] = preg_replace ( '/(top:.*pt).*/iU', '', $row ['content'] );
			$row ['analysis'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $row ['analysis'] );
			$row ['options'] = $questionOptions;
			$row ['answers'] = $questionAnswers;
		}

		return $row;
	}

	/**
	 * 获取试题的选项
	 */
	public function getOptionsByID($id) {
		return $this->dao->getAll ( 'SELECT o.`uid`,
											o.`id` AS `oid`,
										    o.`content` AS `ocontent`,
										    o.`sort`,
										    o.`is_answer`
									FROM ' . $this->vip_question_option . ' o
									WHERE o.status=1 and o.question_id = ' . $this->dao->quote ( $id ) . ' ORDER BY sort ASC' );
	}

	/**
	 * 获取试题的答案
	 */
	public function getAnswerByID($id) {
		return $this->dao->getAll ( 'SELECT `id`,
			 								`question_id`,
										    `content`,
										    `sort`
									FROM ' . $this->vip_question_answer . '
									WHERE status=1 and question_id = ' . $this->dao->quote ( $id ) . ' ORDER BY sort ASC' );
	}
	public function getQuestionsByWhere($condition = array(), $currentPage, $pageSize) {
		$currentPage = empty ( $currentPage ) ? 1 : ($currentPage - 1) * $pageSize;
		$pageSize = empty ( $pageSize ) ? 20 : $pageSize;

		//$condition ['id']  = '31304';

		$where = '';
		//$condition ['ts'] = '选择题' ;
		//$where .= ' AND h.title = ' . $this->dao->quote ( $condition ['ts'] );

		//$where .= ' AND a.id = ' . $this->dao->quote ( $condition ['id'] );
		if (! empty ( $condition ['coursetypeid'] )) {
			$where .= ' AND a.course_type_id = ' . $this->dao->quote ( $condition ['coursetypeid'] );
		}
		if (! empty ( $condition ['department'] )) {
			$where .= ' AND a.department = ' . $this->dao->quote ( $condition ['department'] );
		}
		if (! empty ( $condition ['isclassic'] )) {
			$where .= ' AND a.is_classic = ' . $this->dao->quote ( $condition ['isclassic'] );
		}
		if (! empty ( $condition ['iscontenterror'] )) {
			$where .= ' AND a.is_content_error = ' . $this->dao->quote ( $condition ['iscontenterror'] );
		}
		// if (! empty ( $condition ['startdate'] ) && ! empty ( $condition ['enddate'] )) {
		// $where .= ' AND a.create_time BETWEEN ' . $this->dao->quote ( date ( "Y-m-d 00:00:00", strtotime ( $condition ['startdate'] ) ) ) . ' AND ' . $this->dao->quote ( date ( "Y-m-d 23:59:59", strtotime ( $condition ['enddate'] ) ) );
		// }
		if (isset ( $condition ['status'] )) {
			$where .= ' AND a.status = ' . $this->dao->quote ( $condition ['status'] );
		}
		if (! empty ( $condition ['grade'] )) {
			$where .= ' AND f.`id` IN (' . $condition ['grade'] . ') ';
		}
		if (! empty ( $condition ['subject'] )) {
			$where .= ' AND e.`id` IN (' . $condition ['subject'] . ') ';
		}

		
		$list = $this->dao->getAll ( ' SELECT a.*,
											 b.`name` AS `knowledge_name`,
											 d.`title` AS `course_type_name`,
											 e.`title` AS `subject_name`,
											 f.`title` AS `grade_name`,
											 g.`question_type_code`,
											 h.`title` AS `question_type_name`,
											 fn_vip_get_grade_name(a.grades) AS `grade_names`,
										     aa.file_name
										FROM ' . $this->vip_question . ' a
										LEFT JOIN ' . $this->vip_paper . ' aa ON a.paper_id = aa.id
										LEFT JOIN ' . $this->vip_knowledge . ' b ON a.knowledge_id = b.id
										LEFT JOIN ' . $this->vip_dict_course_type . ' d ON a.course_type_id = d.id
										LEFT JOIN ' . $this->vip_dict_subject . ' e ON d.subject_id = e.id
										LEFT JOIN ' . $this->vip_dict_grade . ' f ON e.grade_id = f.id
										LEFT JOIN ' . $this->vip_dict_question_type . ' g ON g.id = a.question_type_id
										LEFT JOIN ' . $this->vip_dict . ' h ON g.question_type_code = h.code AND h.category = \'QUESTION_TYPE\'
									 	WHERE a.status = 1 AND a.parent_id = 0 ' . $where . ' ORDER BY id DESC
									 	LIMIT ' . $currentPage . ', ' . $pageSize );

		if ($list) {
			$count=count ( $list );
			$questionIds = array ();
			for($i = 0, $n = $count; $i < $n; $i ++) {
				$questionIds [] = $list [$i] ['id'];
			}
			$options = $this->getOptionsByQuestionIds ( arr2str ( $questionIds ) );
			$answers = $this->getAnswersByQuestionIds ( arr2str ( $questionIds ) );
			// $subs = $this->getSubQuestionsByQuestionIds ( arr2str ( $questionIds ) );

			for($i = 0, $n = $count; $i < $n; $i ++) {
				$questionOptions = array ();
				$questionAnswers = array ();

				foreach ( $options as $option ) {
					if ($option ['question_id'] == $list [$i] ['id']) {
						$option ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $option ['content'] );
						$questionOptions [] = $option;
					}
				}
				foreach ( $answers as $answer ) {
					if ($answer ['question_id'] == $list [$i] ['id']) {
						$answer ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $answer ['content'] );
						$questionAnswers [] = $answer;
					}
				}

				$list [$i] ['content'] = preg_replace ( '/(top:.*pt).*/iU', '', $list [$i] ['content'] );
				$list [$i] ['analysis'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $list [$i] ['analysis'] );
				$list [$i] ['options'] = $questionOptions;
				$list [$i] ['answers'] = $questionAnswers;
			}
		}

		// $total = 0;
		// if ($list) {
		// 	$total = $this->getQuestionsCountByWhere ( $condition );
		// }
		return array (
		// 'total' => $total,
		'rows' => $list
		);
	}
	public function getMyEditSimpleQuestions($userName, $currentPage, $pageSize) {
		$currentPage = empty ( $currentPage ) ? 1 : $currentPage;
		$pageSize = empty ( $pageSize ) ? 20 : $pageSize;

		// 先获取已被当前用户锁定的数据
		$list = $this->dao->getAll ( ' SELECT	a.`file_name`,
												  b.`id` AS `question_id`,
												  b.`uid`,
												  b.`knowledge_id`,
												  b.`sub_knowledge_id`,
												  b.`difficulty`,
											      b.`sdate`,
											      b.`number`,
												  b.`content`,
												  b.`is_content_error`,
												  b.`is_classic`,
												  b.`lock_row_time`,
												  from_unixtime(b.`knode_last_updated_time`) as `knode_last_updated_time`,
												  c.name as knowledge_name
										FROM ' . $this->vip_paper . ' a
										LEFT JOIN ' . $this->vip_question . ' b ON a.id = b.paper_id
										LEFT JOIN ' . $this->vip_knowledge . ' c ON b.knowledge_id = c.id
									 	WHERE a.`status` = 1 AND b.`status` = 1 AND b.department = \'CLASS\' AND b.`is_edit` = 1 AND current_used_user_name = ' . $this->dao->quote ( $userName ) . ' ORDER BY knode_last_updated_time DESC' );
		if ($list) {
			$questionIds = array ();
			for($i = 0, $n = count ( $list ); $i < $n; $i ++) {
				$questionIds [] = $list [$i] ['question_id'];
			}

			$options = $this->getOptionsByQuestionIds ( arr2str ( $questionIds ) );
			for($i = 0, $n = count ( $list ); $i < $n; $i ++) {
				$questionOptions = array ();
				foreach ( $options as $option ) {
					if ($option ['question_id'] == $list [$i] ['question_id']) {
						$option ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $option ['content'] );
						$questionOptions [] = $option;
					}
				}

				$list [$i] ['content'] = preg_replace ( '/(top:.*pt).*/iU', '', $list [$i] ['content'] );
				// $list [$i] ['analysis'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $list [$i] ['analysis'] );

				$list [$i] ['options'] = $questionOptions;
			}

			$row = $this->dao->getRow ( 'SELECT COUNT(b.id) as cnt
										FROM ' . $this->vip_paper . ' a
										LEFT JOIN ' . $this->vip_question . ' b ON a.id = b.paper_id
									 	WHERE a.`status` = 1 AND b.`status` = 1 AND  b.`is_edit` = 1 AND current_used_user_name = ' . $this->dao->quote ( $userName ) );
			if ($row) {
				$total = $row ['cnt'];
			}
		}
		return array (
		'total' => $total,
		'rows' => $list
		);

		return $list;
	}
	public function getQuestionsByKnowledgeId($knowledgeId) {
		$currentPage = empty ( $currentPage ) ? 1 : $currentPage;
		$pageSize = empty ( $pageSize ) ? 20 : $pageSize;

		// 先获取已被当前用户锁定的数据
		$list = $this->dao->getAll ( ' SELECT	  a.`file_name`,
												  b.`id` AS `question_id`,
												  b.`uid`,
												  b.`knowledge_id`,
												  b.`sub_knowledge_id`,
												  b.`difficulty`,
											      b.`sdate`,
											      b.`number`,
												  b.`content`,
												  b.`is_content_error`,
												  b.`is_classic`,
												  b.`lock_row_time`,
												  from_unixtime(b.`knode_last_updated_time`) as `knode_last_updated_time`,
												  c.name as knowledge_name
										FROM ' . $this->vip_question . ' b
										LEFT JOIN ' . $this->vip_paper . ' a ON a.id = b.paper_id
										LEFT JOIN ' . $this->vip_knowledge . ' c ON b.knowledge_id = c.id
									 	WHERE b.`status` = 1 AND b.department = \'CLASS\' AND b.knowledge_id = ' . $this->dao->quote ( $knowledgeId ) . ' ORDER BY b.`is_classic` DESC, knode_last_updated_time DESC' );

		if ($list) {
			$questionIds = array ();
			for($i = 0, $n = count ( $list ); $i < $n; $i ++) {
				$questionIds [] = $list [$i] ['question_id'];
			}

			$options = $this->getOptionsByQuestionIds ( arr2str ( $questionIds ) );
			for($i = 0, $n = count ( $list ); $i < $n; $i ++) {
				$questionOptions = array ();
				foreach ( $options as $option ) {
					if ($option ['question_id'] == $list [$i] ['question_id']) {
						$option ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $option ['content'] );
						$questionOptions [] = $option;
					}
				}

				$list [$i] ['content'] = preg_replace ( '/(top:.*pt).*/iU', '', $list [$i] ['content'] );
				// $list [$i] ['analysis'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $list [$i] ['analysis'] );

				$list [$i] ['options'] = $questionOptions;
			}

			/*
			* $row = $this->dao->getRow ( 'SELECT COUNT(b.id) FROM ' . $this->vip_paper . ' a LEFT JOIN ' . $this->vip_question . ' b ON a.id = b.paper_id WHERE a.`status` = 1 AND b.`status` = 1 AND b.`is_edit` = 1 AND current_used_user_name = ' . $this->dao->quote ( $userName ) ); if ($row) { $total = $row ['cnt']; }
			*/
		}
		return array (
		'rows' => $list
		);

		return $list;
	}
	public function getMyEditSimpleQuestions1($userName, $currentPage, $pageSize) {
		$currentPage = empty ( $currentPage ) ? 1 : $currentPage;
		$pageSize = empty ( $pageSize ) ? 20 : $pageSize;

		// 先获取已被当前用户锁定的数据
		$list = $this->dao->getAll ( ' SELECT	a.`file_name`,
												  b.`id` AS `question_id`,
												  b.`uid`,
												  b.`knowledge_id`,
												  b.`sub_knowledge_id`,
												  b.`content_error_types`,
											      b.`sdate`,
											      b.`number`,
												  b.`content`,
												  b.`is_content_error`,
												  b.`is_classic`,
												  b.`lock_row_time`,
												  from_unixtime(b.`content_last_updated_time`) as `content_last_updated_time`,
												  c.name as knowledge_name
										FROM ' . $this->vip_paper . ' a
										LEFT JOIN ' . $this->vip_question . ' b ON a.id = b.paper_id
										LEFT JOIN ' . $this->vip_knowledge . ' c ON b.knowledge_id = c.id
									 	WHERE a.`status` = 1 AND b.`status` = 1 AND  b.`is_edit1` = 1 AND current_used_user_name1 = ' . $this->dao->quote ( $userName ) . ' ORDER BY content_last_updated_time DESC' );

		if ($list) {
			$questionIds = array ();
			for($i = 0, $n = count ( $list ); $i < $n; $i ++) {
				$questionIds [] = $list [$i] ['question_id'];
			}

			$options = $this->getOptionsByQuestionIds ( arr2str ( $questionIds ) );
			$answers = $this->getAnswersByQuestionIds ( arr2str ( $questionIds ) );
			for($i = 0, $n = count ( $list ); $i < $n; $i ++) {
				$questionOptions = array ();
				foreach ( $options as $option ) {
					if ($option ['question_id'] == $list [$i] ['question_id']) {
						$option ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $option ['content'] );
						$questionOptions [] = $option;
					}
				}
				foreach ( $answers as $answer ) {
					if ($answer ['question_id'] == $list [$i] ['question_id']) {
						$answer ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $answer ['content'] );
						$questionAnswers [] = $answer;
					}
				}

				$list [$i] ['content'] = preg_replace ( '/(top:.*pt).*/iU', '', $list [$i] ['content'] );
				$list [$i] ['analysis'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $list [$i] ['analysis'] );

				$list [$i] ['options'] = $questionOptions;
				$list [$i] ['answers'] = $questionAnswers;
			}

			$row = $this->dao->getRow ( 'SELECT COUNT(b.id) as cnt
										FROM ' . $this->vip_paper . ' a
										LEFT JOIN ' . $this->vip_question . ' b ON a.id = b.paper_id
									 	WHERE a.`status` = 1 AND b.`status` = 1 AND  b.`is_edit1` = 1 AND current_used_user_name1 = ' . $this->dao->quote ( $userName ) );
			if ($row) {
				$total = $row ['cnt'];
			}
		}
		return array (
		'total' => $total,
		'rows' => $list
		);

		return $list;
	}
	// 目前只取一条记录
	public function getSimpleQuestionsByWhere($userName, $condition = array(), $currentPage, $pageSize) {
		$currentPage = empty ( $currentPage ) ? 1 : $currentPage;
		$pageSize = empty ( $pageSize ) ? 20 : $pageSize;

		$where = $where1 = '';
		if (! empty ( $condition ['coursetypeid'] )) {
			$where .= ' and b.department=\'VIP\' and b.course_type_id = ' . $this->dao->quote ( $condition ['coursetypeid'] );
			$where1 = ' department=\'VIP\' and status = 1 AND  is_edit = 0 and in_used = 0 and course_type_id = ' . $this->dao->quote ( $condition ['coursetypeid'] );
		} else {
			return array ();
		}

		// 先获取已被当前用户锁定的数据
		$row = $this->dao->getRow ( ' SELECT  a.`file_name`,
											  b.`id` AS `question_id`,
											  b.`uid`,
											  b.`number`,
											  b.`knowledge_id`,
											  b.`sub_knowledge_id`,
											  b.`difficulty`,
										      b.`sdate`,
											  b.`department`,
											  b.`content`,
											  b.`analysis`,
											  b.`is_content_error`,
											  b.`lock_row_time`
										FROM ' . $this->vip_question . ' b
										LEFT JOIN ' . $this->vip_paper . ' a ON a.id = b.paper_id
									 	WHERE b.`status` = 1 AND  b.`is_edit` = 0 AND in_used = 1 AND current_used_user_name = \'' . $userName . '\' ' . $where . '
									 	LIMIT 0, 1' );

		if (! $row) {
			$row = $this->dao->getRow ( 'SELECT   a.`file_name`,
												  b.`id` AS `question_id`,
												  b.`uid`,
											  	  b.`number`,
												  b.`knowledge_id`,
												  b.`sub_knowledge_id`,
												  b.`difficulty`,
												  b.`sdate`,
											  	  b.`department`,
												  b.`content`,
											  	  b.`analysis`,
												  b.`is_content_error`,
												  b.`lock_row_time`,
												  b.is_classic
										FROM ' . $this->vip_question . ' b
										LEFT JOIN ' . $this->vip_paper . ' a ON a.id = b.paper_id
									 	WHERE b.`status` = 1 AND  b.`is_edit` = 0 and b.in_used = 0' . $where . '
										ORDER BY RAND()
										LIMIT 0, 1' );
			if ($row) {
				$time = time ();
				if ($this->dao->execute ( 'UPDATE ' . $this->vip_question . ' SET in_used = 1, current_used_user_name = \'' . $userName . '\', lock_row_time = \'' . $time . '\' WHERE id = ' . $row ['question_id'] )) {
					$row ['lock_row_time'] = $time;
				}
			}
		}
		if ($row) {
			$row ['content'] = preg_replace ( '/(top:.*pt).*/iU', '', $row ['content'] );
			$row ['analysis'] = preg_replace ( '/(top:.*pt).*/iU', '', $row ['analysis'] );
			// $row ['content'] = preg_replace ( '/(position:relative;)([\s\S]*)(top:.*pt)([\'\"]{1}\>[\s\S]*\<img){1}/iU', '$1-----$4', $row ['content'] );
			$options = $this->getOptionsByQuestionIds ( $row ['question_id'] );
			$answers = $this->getAnswersByQuestionIds ( $row ['question_id'] );

			foreach ( $options as $option ) {
				$option ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $option ['content'] );
				$questionOptions [] = $option;
			}
			foreach ( $answers as $answer ) {
				$answer ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $answer ['content'] );
				$questionAnswers [] = $answer;
			}
			$row ['options'] = $questionOptions;
			$row ['answers'] = $questionAnswers;
		}

		return $row;
	}
	// 目前只取一条记录
	public function getSimpleQuestionsEditByWhere($userName, $condition = array(), $currentPage, $pageSize) {
		$currentPage = empty ( $currentPage ) ? 1 : $currentPage;
		$pageSize = empty ( $pageSize ) ? 20 : $pageSize;

		$where = $where1 = '';
		if (! empty ( $condition ['coursetypeid'] )) {
			$where .= ' AND b.department=\'VIP\' and b.course_type_id = ' . $this->dao->quote ( $condition ['coursetypeid'] );
			$where1 = ' department=\'VIP\' AND status = 1 and is_edit1 = 0 and in_used1 = 0 and course_type_id = ' . $this->dao->quote ( $condition ['coursetypeid'] );
		} else {
			return array ();
		}

		// 先获取已被当前用户锁定的数据
		$row = $this->dao->getRow ( ' SELECT  a.`file_name`,
											  b.`id` AS `question_id`,
											  b.`uid`,
											  b.`number`,
											  b.`course_type_id`,
											  b.`question_type_id`,
										      b.`sdate`,
											  b.`content`,
											  b.`lock_row_time1`,
											  c.`question_type_code`
										FROM ' . $this->vip_question . ' b
										LEFT JOIN ' . $this->vip_paper . ' a ON a.id = b.paper_id
										LEFT JOIN ' . $this->vip_dict_question_type . ' c ON b.question_type_id = c.id
									 	WHERE b.`status` = 1 AND  b.`is_edit1` = 0 AND in_used1 = 1 AND current_used_user_name1 = \'' . $userName . '\' ' . $where . '
									 	LIMIT 0, 1' );
		if (! $row) {
			$row = $this->dao->getRow ( 'SELECT  a.`file_name`,
												  b.`id` AS `question_id`,
												  b.`uid`,
											  	  b.`number`,
												  b.`course_type_id`,
												  b.`question_type_id`,
												  b.`sdate`,
												  b.`content`,
											  	  b.`lock_row_time1`,
											  	  c.`question_type_code`
										FROM ' . $this->vip_question . ' b
										LEFT JOIN ' . $this->vip_paper . ' a ON a.id = b.paper_id
										LEFT JOIN ' . $this->vip_dict_question_type . ' c ON b.question_type_id = c.id
									 	WHERE b.`status` = 1 AND  b.`is_edit1` = 0 and b.in_used1 = 0' . $where . '
										ORDER BY RAND()
										LIMIT 0, 1' );
			if ($row) {
				$time = time ();
				if ($this->dao->execute ( 'UPDATE ' . $this->vip_question . ' SET in_used1 = 1, current_used_user_name1 = \'' . $userName . '\', lock_row_time1 = \'' . $time . '\' WHERE id = ' . $row ['question_id'] )) {
					$row ['lock_row_time1'] = $time;
				}
			}
		}
		if ($row) {
			$row ['content'] = preg_replace ( '/(top:.*pt).*/iU', '', $row ['content'] );
			// $row ['content'] = preg_replace ( '/(position:relative;)([\s\S]*)(top:.*pt)([\'\"]{1}\>[\s\S]*\<img){1}/iU', '$1-----$4', $row ['content'] );
			$options = $this->getOptionsByQuestionIds ( $row ['question_id'] );
			foreach ( $options as $option ) {
				// $option ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $option ['content'] );
				$questionOptions [] = $option;
			}
			$row ['options'] = $questionOptions;
		}

		return $row;
	}
	// 目前只取一条记录
	public function getClassicQuestionsEditByWhere($userName, $condition = array(), $currentPage, $pageSize) {
		$currentPage = empty ( $currentPage ) ? 1 : $currentPage;
		$pageSize = empty ( $pageSize ) ? 20 : $pageSize;

		$where = $where1 = '';
		if (! empty ( $condition ['coursetypeid'] )) {
			$where .= ' AND b.department=\'CLASS\' and b.course_type_id = ' . $this->dao->quote ( $condition ['coursetypeid'] );
			$where1 = ' department=\'CLASS\' AND status = 1 and is_edit2 = 0 and in_used2 = 0 and course_type_id = ' . $this->dao->quote ( $condition ['coursetypeid'] );
		} else {
			return array ();
		}

		// 先获取已被当前用户锁定的数据
		$row = $this->dao->getRow ( ' SELECT  a.`file_name`,
											  b.`id` AS `question_id`,
											  b.`uid`,
											  b.`number`,
											  b.`course_type_id`,
											  b.`question_type_id`,
										      b.`sdate`,
											  b.`content`,
											  b.`lock_row_time1`,
											  c.`question_type_code`
										FROM ' . $this->vip_question . ' b
										LEFT JOIN ' . $this->vip_paper . ' a ON a.id = b.paper_id
										LEFT JOIN ' . $this->vip_dict_question_type . ' c ON b.question_type_id = c.id
									 	WHERE b.`status` = 1 AND b.`is_edit2` = 0 AND in_used2 = 1 AND current_used_user_name2 = \'' . $userName . '\' ' . $where . '
									 	LIMIT 0, 1' );
		if (! $row) {
			$row = $this->dao->getRow ( 'SELECT  a.`file_name`,
												  b.`id` AS `question_id`,
												  b.`uid`,
											  	  b.`number`,
												  b.`course_type_id`,
												  b.`question_type_id`,
												  b.`sdate`,
												  b.`content`,
											  	  b.`lock_row_time1`,
											  	  c.`question_type_code`
										FROM ' . $this->vip_question . ' b
										LEFT JOIN ' . $this->vip_paper . ' a ON a.id = b.paper_id
										LEFT JOIN ' . $this->vip_dict_question_type . ' c ON b.question_type_id = c.id
									 	WHERE b.`status` = 1 AND b.`is_classic` = 1 AND b.`is_edit2` = 0 and b.in_used2 = 0' . $where . '
										ORDER BY RAND()
										LIMIT 0, 1' );
			if ($row) {
				$time = time ();
				if ($this->dao->execute ( 'UPDATE ' . $this->vip_question . ' SET in_used2 = 1, current_used_user_name2 = \'' . $userName . '\', lock_row_time2 = \'' . $time . '\' WHERE id = ' . $row ['question_id'] )) {
					$row ['lock_row_time1'] = $time;
				}
			}
		}
		if ($row) {
			$row ['content'] = preg_replace ( '/(top:.*pt).*/iU', '', $row ['content'] );
			// $row ['content'] = preg_replace ( '/(position:relative;)([\s\S]*)(top:.*pt)([\'\"]{1}\>[\s\S]*\<img){1}/iU', '$1-----$4', $row ['content'] );
			$options = $this->getOptionsByQuestionIds ( $row ['question_id'] );
			foreach ( $options as $option ) {
				// $option ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $option ['content'] );
				$questionOptions [] = $option;
			}
			$row ['options'] = $questionOptions;
		}

		return $row;
	}
	protected function getSubQuestionsByQuestionIds($questionIds) {
		$sql = ' SELECT a.*, b.question_type_code FROM ' . $this->vip_question . ' a LEFT JOIN ' . $this->vip_dict_question_type . ' b ON a.question_type_id = b.id WHERE parent_id IN (' . $questionIds . ')';

		return $this->dao->getAll ( $sql );
	}
	public function getOptionsByQuestionIds($ids) {
		$sql = 'SELECT  `id`,
						`question_id`,
						`content`,
						`sort`,
						`is_answer`,
						`status`
				FROM ' . $this->vip_question_option . '
				WHERE status = 1 AND question_id IN (' . $ids . ')
				ORDER BY sort, id';
		// echo $sql;exit;
		return $this->dao->getAll ( $sql );
	}
	public function getAnswersByQuestionIds($ids) {
		$sql = 'SELECT  `id`,
						`question_id`,
						`content`,
						`sort`,
						`status`
				FROM ' . $this->vip_question_answer . '
				WHERE status = 1 AND question_id IN (' . $ids . ')
				ORDER BY sort, id';

		return $this->dao->getAll ( $sql );
	}
	public function getQuestionsCountByWhere($condition = array()) {
		$where = '';
		if (isset ( $condition ['status'] )) {
			$where .= ' AND a.status = ' . $this->dao->quote ( $condition ['status'] );
		}
		if (! empty ( $condition ['coursetypeid'] )) {
			$where .= ' AND a.course_type_id = ' . $this->dao->quote ( $condition ['coursetypeid'] );
		}
		if (! empty ( $condition ['department'] )) {
			$where .= ' AND a.department = ' . $this->dao->quote ( $condition ['department'] );
		}
		if (! empty ( $condition ['isclassic'] )) {
			$where .= ' AND a.is_classic = ' . $this->dao->quote ( $condition ['isclassic'] );
		}
		if (! empty ( $condition ['iscontenterror'] )) {
			$where .= ' AND a.is_content_error = ' . $this->dao->quote ( $condition ['iscontenterror'] );
		}
		// if (! empty ( $condition ['startdate'] ) && ! empty ( $condition ['enddate'] )) {
		// $where .= ' AND a.create_time BETWEEN ' . $this->dao->quote ( date ( "Y-m-d 00:00:00", strtotime ( $condition ['startdate'] ) ) ) . ' AND ' . $this->dao->quote ( date ( "Y-m-d 23:59:59", strtotime ( $condition ['enddate'] ) ) );
		// }
		if (! empty ( $condition ['grade'] )) {
			$where .= ' AND a.`grade_id` IN (' . $condition ['grade'] . ') ';
		}
		if (! empty ( $condition ['subject'] )) {
			$where .= ' AND a.`subject_id` IN (' . $condition ['subject'] . ') ';
		}
		//原sql语句
		// $row = $this->dao->getRow ( 'SELECT COUNT(*) AS cnt
		// 								FROM ' . $this->vip_question . ' a
		// 							 	LEFT JOIN ' . $this->vip_knowledge . ' b ON a.knowledge_id = b.id
		// 								LEFT JOIN ' . $this->vip_dict_course_type . ' d ON a.course_type_id = d.id
		// 								LEFT JOIN ' . $this->vip_dict_subject . ' e ON d.subject_id = e.id
		// 								LEFT JOIN ' . $this->vip_dict_grade . ' f ON e.grade_id = f.id
		// 								LEFT JOIN ' . $this->vip_dict . ' g ON a.question_type_id = g.id AND g.category = \'QUESTION_TYPE\'
		// 							 	WHERE a.status = 1 AND a.parent_id = 0 ' . $where );
		$row = $this->dao->getRow ( ' SELECT COUNT(1) AS cnt
										FROM ' . $this->vip_question . ' a
										LEFT JOIN ' . $this->vip_dict . ' g ON a.question_type_id = g.id  AND g.category = \'QUESTION_TYPE\' 
									 	WHERE a.status = 1 AND a.parent_id = 0 ' . $where );
		$total = 0;
		if ($row) {
			$total = $row ['cnt'];
		}
		return $total;
	}
	public function getQuestionCurrentEditByUserName($userName, $courseTypeId) {
		return $this->dao->getAll ( ' SELECT  id
										FROM ' . $this->vip_question . '
									 	WHERE department=\'CLASS\' and status = 1 AND  is_edit = 0 AND in_used = 1 and course_type_id = ' . $this->dao->quote ( $courseTypeId ) . ' and current_used_user_name = ' . $this->dao->quote ( $userName ) );
	}
	public function getQuestionCurrentEditByUserName1($userName, $courseTypeId) {
		return $this->dao->getAll ( ' SELECT  id
										FROM ' . $this->vip_question . '
									 	WHERE department=\'CLASS\' and status = 1 AND  is_edit1 = 0 AND in_used1 = 1 and course_type_id = ' . $this->dao->quote ( $courseTypeId ) . ' and current_used_user_name1 = ' . $this->dao->quote ( $userName ) );
	}
	public function getQuestionCurrentEditByUserName2($userName, $courseTypeId) {
		return $this->dao->getAll ( ' SELECT  id
										FROM ' . $this->vip_question . '
									 	WHERE department=\'CLASS\' and status = 1 AND  is_edit2 = 0 AND in_used2 = 1 and course_type_id = ' . $this->dao->quote ( $courseTypeId ) . ' and current_used_user_name2 = ' . $this->dao->quote ( $userName ) );
	}
	public function getQuestionStatistics($condition = array()) {
		$where = ' q.status = 1 AND q.parent_id = 0 ';
		if (isset ( $condition ['status'] )) {
			$where .= ' AND q.status = ' . $this->dao->quote ( $condition ['status'] );
		}
		/*
		* if (isset ( $condition ['is_paper'] )) { $where .= ' AND q.is_paper = ' . $this->dao->quote ( $condition ['is_paper'] ); } if (! empty ( $condition ['xuebu'] )) { $where .= ' AND b.`code` IN (' . $condition ['xuebu'] . ') '; } if (! empty ( $condition ['xueke'] )) { $where .= ' AND c.`code` IN (' . $condition ['xueke'] . ') '; }
		*/

		// return $this->dao->getRow ( 'SELECT (
		// 							 	SELECT COUNT(*) AS today FROM ' . $this->vip_question . ' AS q
		// 							 	WHERE TO_DAYS(FROM_UNIXTIME(created_time)) = TO_DAYS(NOW()) AND ' . $where.'
		// 							 ) AS today, (
		// 							 	SELECT COUNT(*) AS week FROM ' . $this->vip_question . ' AS q
		// 							 	WHERE YEARWEEK(date_format(FROM_UNIXTIME(created_time), \'%Y-%m-%d\')) = YEARWEEK(now()) AND ' . $where.'
		// 							 ) AS week, (
		// 							 	SELECT COUNT(*) AS month FROM ' . $this->vip_question . ' AS q
		// 							 	WHERE date_format(FROM_UNIXTIME(created_time), \'%Y-%m\') = date_format(now(), \'%Y-%m\') AND ' . $where.'
		// 							 ) AS month, (
		// 							 	SELECT COUNT(*) AS total FROM ' . $this->vip_question . ' AS q
		// 							 	WHERE ' . $where . '
		// 							 ) AS total, (
		// 								SELECT COUNT(*) AS paper_count FROM ' . $this->vip_paper . ' AS q WHERE status = 1
		// 							 ) AS paper_count' );
		//
//		$data['today']= $this->dao->getOne( ' SELECT COUNT(1) AS today FROM ' . $this->vip_question . ' AS q WHERE TO_DAYS(FROM_UNIXTIME(created_time)) = TO_DAYS(NOW()) and ' . $where);
//		$data['week']= $this->dao->getOne ( ' SELECT COUNT(1) AS week FROM ' . $this->vip_question . ' AS q WHERE YEARWEEK(date_format(FROM_UNIXTIME(created_time), \'%Y-%m-%d\')) = YEARWEEK(now()) and' . $where);
//
//		$data['month']= $this->dao->getOne ( ' SELECT COUNT(1) AS month FROM ' . $this->vip_question . ' AS q WHERE date_format(FROM_UNIXTIME(created_time), \'%Y-%m\') = date_format(now(), \'%Y-%m\') and ' . $where );
//
//		$data['total']= $this->dao->getOne( ' SELECT COUNT(1) AS total FROM ' . $this->vip_question . ' AS q WHERE ' . $where);
		$sql = "select sum(
					case when TO_DAYS(FROM_UNIXTIME(created_time)) = TO_DAYS(NOW()) then 1 else 0 end
				) as today,
				
				sum(
				case when YEARWEEK(date_format(FROM_UNIXTIME(created_time), '%Y-%m-%d')) = YEARWEEK(now()) then 1 else 0 end
				) as week,
				
				sum(
				case when date_format(FROM_UNIXTIME(created_time), '%Y-%m') = date_format(now(), '%Y-%m') then 1 else 0 end
				) as month,
				
				sum(1) as total
				
				from vip_question q where " . $where;
		$data = $this->dao->getRow($sql);

		$data['paper_count']= $this->dao->getOne ( ' SELECT COUNT(1) AS paper_count FROM ' . $this->vip_paper . ' AS q WHERE status = 1');
		return $data;

	}
	public function getQuestionStatisticsByCourseTypeId($courseTypeId, $userName) {
		return $this->dao->getRow ( 'SELECT (
									 	SELECT COUNT(*) AS lock_question_count FROM ' . $this->vip_question . ' AS q
									 	WHERE status = 1 and department = \'VIP\' and is_edit = 0 and in_used = 1 and course_type_id = \'' . $courseTypeId . '\'
									 ) AS lock_question_count, (
									 	SELECT COUNT(*) AS left_non_edit_question_count FROM ' . $this->vip_question . ' AS q
									 	WHERE status = 1 and department = \'VIP\' and is_edit = 0 and course_type_id = \'' . $courseTypeId . '\'
									 ) AS left_non_edit_question_count, (
									 	SELECT COUNT(*) AS total_question_count FROM ' . $this->vip_question . ' AS q
									 	WHERE status = 1 and department = \'VIP\' and course_type_id = \'' . $courseTypeId . '\'
									 ) AS total_question_count, (
									 	SELECT COUNT(*) AS my_op_question_count FROM ' . $this->vip_question . ' AS q
									 	WHERE status = 1 and department = \'VIP\' and is_edit = 1 and current_used_user_name = \'' . $userName . '\' and course_type_id = \'' . $courseTypeId . '\'
									 ) AS my_op_question_count' );
	}
	public function getQuestionStatisticsByCourseTypeId1($courseTypeId, $userName) {
		return $this->dao->getRow ( 'SELECT (
									 	SELECT COUNT(*) AS lock_question_count FROM ' . $this->vip_question . ' AS q
									 	WHERE status = 1 and department = \'CLASS\' and is_edit1 = 0 and in_used1 = 1 and course_type_id = \'' . $courseTypeId . '\'
									 ) AS lock_question_count, (
									 	SELECT COUNT(*) AS left_non_edit_question_count FROM ' . $this->vip_question . ' AS q
									 	WHERE status = 1 and department = \'CLASS\' and is_edit1 = 0 and course_type_id = \'' . $courseTypeId . '\'
									 ) AS left_non_edit_question_count, (
									 	SELECT COUNT(*) AS total_question_count FROM ' . $this->vip_question . ' AS q
									 	WHERE status = 1 and department = \'CLASS\' and course_type_id = \'' . $courseTypeId . '\'
									 ) AS total_question_count, (
									 	SELECT COUNT(*) AS my_op_question_count FROM ' . $this->vip_question . ' AS q
									 	WHERE status = 1 and department = \'CLASS\' and is_edit1 = 1 and current_used_user_name1 = \'' . $userName . '\' and course_type_id = \'' . $courseTypeId . '\'
									 ) AS my_op_question_count' );
	}
	public function getQuestionStatisticsByCourseTypeId2($courseTypeId, $userName) {
		return $this->dao->getRow ( 'SELECT (
									 	SELECT COUNT(*) AS left_non_edit_question_count FROM ' . $this->vip_question . ' AS q
									 	WHERE status = 1 and department = \'CLASS\' and is_classic = 1 and is_edit2 = 0 and course_type_id = \'' . $courseTypeId . '\'
									 ) AS left_non_edit_question_count, (
									 	SELECT COUNT(*) AS total_question_count FROM ' . $this->vip_question . ' AS q
									 	WHERE status = 1 and department = \'CLASS\' and is_classic = 1 and course_type_id = \'' . $courseTypeId . '\'
									 ) AS total_question_count, (
									 	SELECT COUNT(*) AS my_op_question_count FROM ' . $this->vip_question . ' AS q
									 	WHERE status = 1 and department = \'CLASS\' and is_classic = 1 and is_edit2 = 1 and current_used_user_name2 = \'' . $userName . '\' and course_type_id = \'' . $courseTypeId . '\'
									 ) AS my_op_question_count' );
	}
	public function getGradeNameById($grade_id) {
		return $this->dao->getOne ( 'SELECT title FROM ' . $this->vip_dict_grade . ' WHERE id = ' . $this->dao->quote ( abs ( $grade_id ) ) );
	}
	public function getSubjectNameById($subject_id) {
		return $this->dao->getOne ( 'SELECT title FROM ' . $this->vip_dict_subject . ' WHERE id = ' . $this->dao->quote ( abs ( $subject_id ) ) );
	}
	public function getGrades() {
		return $this->dao->getAll ( 'SELECT `id`,
										    `title`,
										    `status`,
										    `sort`
									FROM ' . $this->vip_dict_grade . '
								 	WHERE status = 1 ORDER BY sort' );
	}
	public function getSubjectsByGradeId($gradeId) {
		$gradeIds=$this->dao->quote ( $gradeId ) ;

		return $this->dao->getAll ( 'SELECT `id`,
										    `title`,
										    `status`,
										    `sort`
									FROM ' . $this->vip_dict_subject . '
								 	WHERE status = 1 AND grade_id = ' .$gradeIds . ' ORDER BY sort' );
	}
	public function getCourseTypesBySubjectId($subjectId) {
		$subjectIds=$this->dao->quote ( $subjectId );
		return $this->dao->getAll ( 'SELECT `id`,
										    `title`,
										    `status`,
										    `sort`
									FROM ' . $this->vip_dict_course_type . '
								 	WHERE status = 1 AND subject_id = ' . $subjectIds . ' ORDER BY sort' );
	}

	//查询课程属性
	public function getCourseTypesByKonwledge($id,$subjectId) {
		return $this->dao->getAll ( 'SELECT `id`,
										    `title`,
										    `status`,
										    `sort`
									FROM ' . $this->vip_dict_course_type . '
								 	WHERE status = 1 AND subject_id = ' . $this->dao->quote ( $subjectId ) . ' and knowledge_type_id ='.$this->dao->quote($id).' ORDER BY sort' );
	}

	//查询讲义属性
	public function getCourseTypesBySubjectAll($courseId)
	{
		return $this->dao->getAll("select b.id,b.name from ".$this->vip_knowledge_course_type_rs." a left join ".$this->vip_knowledge." b on a.knowledge_id = b.id where a.course_type_id =".$courseId." order by b.sort asc");
	}

	public function getQuestionTypesBySubjectId($subjectId) {
		return $this->dao->getAll ( 'SELECT a.`id`,
											CASE WHEN a.title = \'\' THEN b.title ELSE a.title END AS title,
										    b.title AS origin_title,
										    a.`status`,
										    a.`sort`,
										    a.`question_type_code`
									FROM ' . $this->vip_dict_question_type . ' a
									LEFT JOIN ' . $this->vip_dict . ' b ON a.question_type_code = b.code
								 	WHERE a.status = 1 AND a.subject_id = ' . $this->dao->quote ( $subjectId ) . ' ORDER BY a.sort, a.id' );
	}
	public function getPath($id, $type = 'knowledge') {
		$path = array ();
		if ($type == 'knowledge') {
			$nav = $this->getKnowledgeByID ( $id );
		}else {
			$nav = $this->getLabelByID ( $id );
		}
		$path [] = $nav;
		if ($nav ['parent_id'] > 0) {
			$path = array_merge ( $this->getPath ( $nav ['parent_id'] ), $path );
		}

		return $path;
	}
	public function getComboboxData($cate, $gradeId = 0, $subjectId = 0) {
		$where = '';
		switch ($cate) {
			case 'GRADE_DEPT' :
				$tempTable = $this->vip_dict_grade;
				$tempFiled = 'grade_id';
				break;
			case 'SUBJECT' :
				$tempTable = $this->vip_dict_subject;
				$tempFiled = 'subject_id';
				if (! empty ( $gradeId )) {
					$where .= ' AND grade_id = ' . $this->dao->quote ( $gradeId );
				}
				break;
			case 'QUESTION_TYPE' :
				$tempTable = $this->vip_dict_question_type;
				$tempFiled = 'question_type_id';
				if (! empty ( $subjectId )) {
					$where .= ' AND subject_id = ' . $this->dao->quote ( $subjectId );
				}
				break;
			case 'KNOWLEDGE_TYPE' :
				$tempTable = $this->vip_dict_knowledge_type;
				$tempFiled = 'knowledge_type_id';
				break;
		}
		return $this->dao->getAll ( 'SELECT id as ' . $tempFiled . ',title FROM ' . $tempTable . ' WHERE status = 1 ' . $where . ' order by sort' );
	}

	// 批量导入试题
	public function importQuestion($arr, $title) {
		$time = time ();

		if (! empty ( $title )) {
			$taojue = explode ( '-', $title );
			
		}

		$gradeSubjectCourseTypeRS = array (
		'小学思维' => '22',
		'小学语文' => '14',
		'小学英语' => '23',
		'小学测试学科' => '29',
		'小学业务部' => '41',
		'小学数学' => '50',
		'小学管培部' => '47',

		'初中数学' => '1',
		'初中语文' => '5',
		'初中英语' => '24',
		'初中物理' => '4',
		'初中化学' => '3',
		'初中业务部' => '42',
			'初中生物'=>'46','初中历史'=>'51','初中管培部'=>'54',

		'高中数学' => '28',
		'高中数学（理）' => '28',
		'高中数学（文）' => '28',
		'高中语文' => '15',
		'高中英语' => '26',
		'高中物理' => '10',
		'高中化学' => '13',
		'高中业务部' => '40',
		'高中管培部' => '55',

		'国际课程Mathematics' => '61',
		'国际课程Physics' => '62',
		'国际课程Chemistry' => '64',

		'竞赛高中数学' => '65',
		'竞赛初中数学' => '66',
		'竞赛物理' => '67',
		'竞赛化学' => '68',
		'竞赛生物' => '69'

		);

		$gradeDeptName = $subjectName = '';
		$taojueQuestionCourseTypeId = '';

		// 判断是否为套卷
		if ($taojue [0] == '套卷' && ! empty ( $taojue [5] ) && ! empty ( $taojue [6] )) {
			$gradeDeptName = $taojue [5];
			$subjectName = $taojue [6];
		}

		// 判断是否为VIP套卷
		if ($taojue [0] == '套卷VIP' && ! empty ( $taojue [1] ) && ! empty ( $taojue [2] )) {
			$gradeDeptName = $taojue [1];
			$subjectName = $taojue [2];
		}

		// 判断是否为试题
		if ($taojue [0] == '试题' && ! empty ( $taojue [1] ) && ! empty ( $taojue [2] )) {
			$gradeDeptName = $taojue [1];
			$subjectName = $taojue [2];
		}

		// 获取同步课程ID
		if (! empty ( $gradeDeptName ) && ! empty ( $subjectName )) {
			foreach ( $gradeSubjectCourseTypeRS as $key => $value ) {
				if (trim ( $gradeDeptName ) . trim ( $subjectName ) == $key) {
					$taojueQuestionCourseTypeId = $value;
					break;
				}
			}
			
		}
		if (! empty ( $arr )) {
			// 查询套卷信息
			$paper_row_id = $this->dao->getOne ( 'SELECT id FROM ' . $this->vip_paper . ' WHERE status = 1 AND file_name_md5 = \'' . md5 ( $title ) . '\'' );
			if (! empty ( $paper_row_id )) { // 该套卷信息已存在
				$sql10 = 'UPDATE ' . $this->vip_paper . ' SET status = -1 WHERE file_name_md5 = \'' . md5 ( $title ) . '\'';
				if (! $this->dao->execute ( $sql10 )) {
					// $flag == false;
				} else {
					// 删除试题
					$sql11 = 'UPDATE ' . $this->vip_question . ' SET status = -1 WHERE paper_id = \'' . $paper_row_id . '\'';
					if (! $this->dao->execute ( $sql11 )) {
						// $flag == false;
					} else {
						// 删除文件【只移动，不实际删除】
						$question_rows = $this->dao->getAll ( 'SELECT id, uid, sdate FROM ' . $this->vip_question . ' WHERE paper_id = \'' . $paper_row_id . '\'' );
						foreach ( $question_rows as $q ) {
							// 删除文件
							// $path = $q ['sdate'] + '/' + $q ['uid'];
							// file_get_contents ( 'http://ksrc2.gaosiedu.com/move.php?path=' + $path );
						}
					}
				}
			}

			$paper_id = '';
			foreach ( $arr as $k => $data ) {
				// 内容中指定年部
				// if (! empty ( $data ['gradeDeptName'] )) {
				// $gradeDeptName = trim ( $data ['gradeDeptName'] );
				// }
				// 内容中指定学科
				// if (! empty ( $data ['subjectName'] )) {
				// $subjectName = trim ( $data ['subjectName'] );
				// }

				// print_r($data);
				$sql = $sql1 = $sql2 = $sql3 = $sql4 = $sql5 = $sql6 = '';

				//
				if (! empty ( $gradeDeptName )) { // 年部
					$gradeIdRS = array (
					'小学' => '1',
					'小学部' => '1',

					'初中' => '2',
					'初中部' => '2',

					'高中' => '3',
					'高中部' => '3'
					);
					foreach ( $gradeIdRS as $key => $value ) {
						if (trim ( $gradeDeptName ) == $key) {
							$data ['grade_dept_id'] = $value;
							break;
						}
					}
					// $data ['grade_dept_id'] = $this->dao->getOne ( 'SELECT id FROM ' . $this->vip_dict_grade . ' WHERE title like ' . $this->dao->quote ( '%' . trim ( $gradeDeptName ) . '%' ) );
					// if (empty ( $data ['grade_dept_id'] )) {
					// if ($this->dao->execute ( 'INSERT INTO ' . $this->vip_dict_grade . ' (title) VALUES(' . $this->dao->quote ( trim ( $gradeDeptName ) ) . ')' )) {
					// $data ['grade_dept_id'] = $this->dao->lastInsertId ();
					// }
					// }
				}

				if (! empty ( $subjectName )) { // 学科
					$gradeSubjectRS = array (
					'小学思维' => '1',
					'小学语文' => '2',
					'小学英语' => '3',
					'小学测试学科' => '27',
					'小学数学' => '38',
					'小学业务部' => '30',
					'小学培训部' => '32',
					'小学管培部' => '36',

					'初中数学' => '4',
					'初中语文' => '5',
					'初中英语' => '6',
					'初中物理' => '7',
					'初中化学' => '8',
						'初中生物' => '35','初中历史'=>'39','初中业务部' => '31','初中管培部' => '42',

					'高中数学' => '25',
					'高中数学（理）' => '25',
					'高中数学（文）' => '25',
					'高中语文' => '11',
					'高中英语' => '12',
					'高中物理' => '13',
					'高中化学' => '14',
					'高中业务部' => '43',
					'高中管培部' => '29',

					'国际课程Mathematics' => '44',
					'国际课程Physics' => '45',
					'国际课程Chemistry' => '46',

					'竞赛高中数学' => '52',
					'竞赛初中数学' => '51',
					'竞赛物理' => '53',
					'竞赛化学' => '54',
					'竞赛生物' => '55',


					);

					foreach ( $gradeSubjectRS as $key => $value ) {
						if (trim ( $gradeDeptName ) . trim ( $subjectName ) == $key) {
							$data ['subject_id'] = $value;
							break;
						}
					}

					/*
					* $sql = 'SELECT id FROM ' . $this->vip_dict_subject . ' WHERE title like ' . $this->dao->quote ( '%' . trim ( $subjectName ) . '%' ); if (! empty ( $data ['grade_dept_id'] )) $sql .= ' AND grade_id = ' . $this->dao->quote ( $data ['grade_dept_id'] ); $data ['subject_id'] = $this->dao->getOne ( $sql . ' LIMIT 1' ); if (empty ( $data ['subject_id'] )) { if ($this->dao->execute ( 'INSERT INTO ' . $this->vip_dict_subject . ' (grade_id,title) VALUES(' . $this->dao->quote ( $data ['grade_dept_id'] ) . ',' . $this->dao->quote ( trim ( $subjectName ) ) . ')' )) { $data ['subject_id'] = $this->dao->lastInsertId (); } }
					*/
				}
				// 课程类型
				/*
				* if (! empty ( $data ['courseTypeName'] )) { $sql2 = 'SELECT id FROM ' . $this->vip_dict_course_type . ' WHERE title like ' . $this->dao->quote ( '%' . trim ( $data ['courseTypeName'] ) . '%' ); if (! empty ( $data ['subject_id'] )) $sql2 .= ' AND subject_id = ' . $this->dao->quote ( $data ['subject_id'] ); $taojueQuestionCourseTypeId = $this->dao->getOne ( $sql2 . ' LIMIT 1' ); if (empty ( $taojueQuestionCourseTypeId )) { if ($this->dao->execute ( 'INSERT INTO ' . $this->vip_dict_course_type . ' (subject_id,title) VALUES(' . $this->dao->quote ( $data ['subject_id'] ) . ',' . $this->dao->quote ( trim ( $data ['courseTypeName'] ) ) . ')' )) { $taojueQuestionCourseTypeId = $this->dao->lastInsertId (); } } }
				*/

				if (! empty ( $data ['questionTypeName'] )) { // 题型
					/*
					* $sql3 = 'SELECT id FROM ' . $this->vip_dict_question_type . ' WHERE title = ' . $this->dao->quote ( trim ( $data ['questionTypeName'] ) ); if (! empty ( $data ['subject_id'] )) $sql3 .= ' AND subject_id = ' . $this->dao->quote ( $data ['subject_id'] ); $data ['question_type_id'] = $this->dao->getOne ( $sql3 . ' LIMIT 1' ); if (empty ( $data ['question_type_id'] )) { $code = $this->dao->getOne ( "SELECT code from " . $this->vip_dict . " where category = 'QUESTION_TYPE' AND title = '" . trim ( $data ['questionTypeName'] ) . "'" ); if ($code) { if ($this->dao->execute ( 'INSERT INTO ' . $this->vip_dict_question_type . ' (subject_id,question_type_code,title) VALUES(' . $this->dao->quote ( $data ['subject_id'] ) . ',' . $this->dao->quote ( $code ) . ',' . $this->dao->quote ( trim ( $data ['questionTypeName'] ) ) . ')' )) { $data ['question_type_id'] = $this->dao->lastInsertId (); } } }
					*/

					if (! empty ( $data ['subject_id'] )) {
						$sql3 = 'SELECT a.id
	  						 FROM ' . $this->vip_dict_question_type . ' a
	  						 LEFT JOIN ' . $this->vip_dict . ' b ON a.question_type_code = b.code AND b.category = \'QUESTION_TYPE\'
							 WHERE b.title = ' . $this->dao->quote ( trim ( $data ['questionTypeName'] ) );

						$sql3 .= ' AND a.subject_id = ' . $this->dao->quote ( $data ['subject_id'] );
						$data ['question_type_id'] = $this->dao->getOne ( $sql3 . ' LIMIT 1' );
					}

					// 插入题型
					/*
					* if (empty ( $data ['question_type_id'] )) { $code = $this->dao->getOne ( "SELECT code from " . $this->vip_dict . " where category = 'QUESTION_TYPE' AND title = '" . trim ( $data ['questionTypeName'] ) . "'" ); if ($code) { if ($this->dao->execute ( 'INSERT INTO ' . $this->vip_dict_question_type . ' (subject_id,question_type_code,title) VALUES(' . $this->dao->quote ( $data ['subject_id'] ) . ',' . $this->dao->quote ( $code ) . ',' . $this->dao->quote ( trim ( $data ['questionTypeName'] ) ) . ')' )) { $data ['question_type_id'] = $this->dao->lastInsertId (); } } }
					*/
				}

				// ligang start 14-09-16
				// 主知识点
				if (! empty ( $data ['knowledgeName'] )) {
					$data ['knowledge_id'] = '';
					$sql4 = 'SELECT id, parent_id, name FROM ' . $this->vip_knowledge . ' WHERE is_leaf = 1 AND name = ' . $this->dao->quote ( trim ( $data ['knowledgeName'] ) ) . ' limit 0, 1';
					$row = $this->dao->getRow ( $sql4 ); // 查找读取的主知点在知识点表中是否有
					if ($row) {
						$data ['knowledge_id'] = $row ['id'];
					}

					/*
					* if (! empty ( $data ['main_knowledge_id'] )) { $data ['main_root_id_arr'] = $this->dao->getAll ( 'SELECT knowledge_id FROM ' . $this->vip_knowledge_course_type_rs . ' WHERE course_type_id =' . $this->dao->quote ( $taojueQuestionCourseTypeId ) ); // 查找此课程下的所有根节点 $data ['main'] = ''; if (! empty ( $data ['main_root_id_arr'] )) { foreach ( $data ['main_root_id_arr'] as $main_id => $main_knowledge_knowledge_id ) { $data ['main'] .= $main_knowledge_knowledge_id ['knowledge_id'] . ','; } $data ['main'] = explode ( ',', trim ( $data ['main'], ',' ) ); } foreach ( $data ['main_knowledge_id'] as $kkk => $vvv ) { if ($vvv ['parent_id'] == 0) { // 查找父节点是否为根节点如为根节点则存储到数组 $data ['main_root_knowledge_id'] .= $vvv ['knowledge_id'] . ','; // 知识点的根节点 $data ['main_knowledge_knowledge_id'] .= $vvv ['knowledge_id'] . ','; // 知识点的ID } else { $root_main_id = $this->getRootKnowledgeId ( $vvv ['knowledge_id'] ); // 查找到根节点则存储到数组 $data ['main_root_knowledge_id'] .= $root_main_id ['id'] . ','; // 知识点的根节点 $data ['main_knowledge_knowledge_id'] .= $vvv ['knowledge_id'] . ','; // 知识点的ID } } $main_knowledge_id_array = $main_knowledge_name_array = $main_knowledge_combine_array = ''; $main_root_knowledge_id = explode ( ',', trim ( $data ['main_root_knowledge_id'], ',' ) ); // 根节点 $main_knowledge_knowledge_id = explode ( ',', trim ( $data ['main_knowledge_knowledge_id'], ',' ) ); // 知识ID $main_knowledge_combine_array = array_combine ( $main_root_knowledge_id, $main_knowledge_knowledge_id ); $biaozhi = false; foreach ( $main_knowledge_combine_array as $main_kk => $main_vv ) { if (in_array ( $main_kk, $data ['main'] )) { // 如果数组中的任意根节点在此课程类型下的根节点中出现过则为真 $data ['knowledge_id'] = $main_vv; $biaozhi = true; } } if ($biaozhi == false) { if ($this->dao->execute ( 'INSERT INTO ' . $this->vip_knowledge . ' (name,remark,is_leaf) VALUES(' . $this->dao->quote ( $data ['knowledgeName'] ) . ',' . $this->dao->quote ( $data ['knowledgeName'] ) . ',' . 1 . ')' )) { $temp_main_combine_knowledge_id = ''; $temp_main_combine_knowledge_id = $this->dao->lastInsertId (); $data ['knowledge_id'] = $temp_main_combine_knowledge_id; if (! empty ( $temp_main_combine_knowledge_id )) { $this->dao->execute ( 'INSERT INTO ' . $this->vip_knowledge_course_type_rs . ' (knowledge_id,course_type_id) VALUES(' . $this->dao->quote ( $data ['knowledge_id'] ) . ',' . $this->dao->quote ( $taojueQuestionCourseTypeId ) . ')' ); } } } } else { // 如果没有则插入此知识点 if ($this->dao->execute ( 'INSERT INTO ' . $this->vip_knowledge . ' (name,remark,is_leaf) VALUES(' . $this->dao->quote ( $data ['knowledgeName'] ) . ',' . $this->dao->quote ( $data ['knowledgeName'] ) . ',' . 1 . ')' )) { $temp2_main_combine_knowledge_id = ''; $temp2_main_combine_knowledge_id = $this->dao->lastInsertId (); $data ['knowledge_id'] = $temp2_main_combine_knowledge_id; if (! empty ( $temp2_main_combine_knowledge_id )) { $this->dao->execute ( 'INSERT INTO ' . $this->vip_knowledge_course_type_rs . ' (knowledge_id,course_type_id) VALUES(' . $this->dao->quote ( $temp2_main_combine_knowledge_id ) . ',' . $this->dao->quote ( $taojueQuestionCourseTypeId ) . ')' ); } } }
					 */
				}
				// ligang end 14-09-16
				
				// 副知识点
				if (! empty ( $data ['subKnowledgeName'] )) {
					$subKnowledgeIdArr = array ();
					// 可用中文，
					$subKnowledgeNameArr = explode ( ',', trim ( trim ( str_replace ( '，', ',', $data ['subKnowledgeName'] ), ',' ) ) );
					
					foreach ( $subKnowledgeNameArr as $key => $value ) {
						$sql4 = 'SELECT id, parent_id, name FROM ' . $this->vip_knowledge . ' WHERE is_leaf = 1 AND name = ' . $this->dao->quote ( trim ( $value ) ) . ' limit 0, 1';
						$row = $this->dao->getRow ( $sql4 ); // 查找读取的主知点在知识点表中是否有
						if ($row) {
							$subKnowledgeIdArr [] = $row ['id'];
						}
					}
					
					$data ['sub_knowledge_id'] = implode ( ',', $subKnowledgeIdArr );
					
					/*
					 * $data ['sub_knowledge_id'] = $data ['root_id_arr'] = ''; $data ['sub_root_knowledge_id'] = $data ['sub_knowledge_knowledge_id'] = $data ['sub_knowledge_knowledge_name'] = $data ['root_id_str'] = ''; $knowledge_count_array = array (); $root_id_str_array = array (); $sub_root_knowledge_id = array (); $sub_knowledge_knowledge_id = array (); $sub_knowledge_combine_array = array (); $new_combine_knowledge_name_count = array (); $sub_knowledge_knowledge_name = array (); $new_sub_root_knowledge = array (); $new_sub_knowledge_Id_name = array (); $data ['sub_knowledge_id_arr'] = $data ['subKnowledgeNameArr'] = ''; $data ['subKnowledgeNameArr'] = explode ( ',', trim ( trim ( str_replace ( '，', ',', $data ['subKnowledgeName'] ), ',' ) ) ); // $data ['subKnowledgeNameStr'] = "'" . implode ( "','", $data ['subKnowledgeNameArr'] ) . "'"; if (! empty ( $data ['subKnowledgeNameArr'] )) { $data ['root_id_arr'] = $this->dao->getAll ( "SELECT knowledge_id FROM " . $this->vip_knowledge_course_type_rs . " WHERE course_type_id = '$data[course_type_id]'" ); // 查找根节点 if (! empty ( $data ['root_id_arr'] )) { // 查找此课程类型下有哪些根节点 foreach ( $data ['root_id_arr'] as $kk => $root ) { $data ['root_id_str'] .= $root ['knowledge_id'] . ','; } $root_id_str_array = explode ( ',', trim ( $data ['root_id_str'], ',' ) ); // 此课程类型下的所有根节点 } foreach ( $data ['subKnowledgeNameArr'] as $sub_key => $sub_val ) { $data ['sub_knowledge_id_arr'] = $this->dao->getAll ( 'SELECT id as knowledge_id,parent_id,name FROM ' . $this->vip_knowledge . " WHERE name = '$sub_val'" ); if (! empty ( $data ['sub_knowledge_id_arr'] )) { // 根据副知识点名称查找副知点ID与父节点 foreach ( $data ['sub_knowledge_id_arr'] as $key => $row ) { if ($row ['parent_id'] == 0) { // 查找父节点是否为根节点如为根节点则存储到数组 $data ['sub_root_knowledge_id'] .= $row ['knowledge_id'] . ','; // 根节点 $data ['sub_knowledge_knowledge_id'] .= $row ['knowledge_id'] . ','; // 副知识点ID $data ['sub_knowledge_knowledge_name'] .= $row ['name'] . ','; // 副知识点名称 } else { $root_id = $this->getRootKnowledgeId ( $row ['knowledge_id'] ); // 查找到根节点则存储到数组 $data ['sub_root_knowledge_id'] .= $root_id ['id'] . ','; // 根节点 $data ['sub_knowledge_knowledge_id'] .= $row ['knowledge_id'] . ','; // 副知识点ID $data ['sub_knowledge_knowledge_name'] .= $row ['name'] . ','; // 副知识点名称 } } // ligang 14-09-16 start $sub_root_knowledge_id = explode ( ',', trim ( $data ['sub_root_knowledge_id'], ',' ) ); // 根节点 $sub_knowledge_knowledge_id = explode ( ',', trim ( $data ['sub_knowledge_knowledge_id'], ',' ) ); // 副知识点ID $sub_knowledge_knowledge_name = explode ( ',', trim ( $data ['sub_knowledge_knowledge_name'], ',' ) ); // 副知识点名称 $new_sub_root_knowledge = array_combine ( $sub_root_knowledge_id, $sub_knowledge_knowledge_id ); // 根节点与知识点ID结合 $new_sub_root_knowledge_re = array_flip ( $new_sub_root_knowledge ); // 反转根节点与知识点 $new_sub_knowledge_Id_name = array_combine ( $new_sub_root_knowledge_re, $sub_knowledge_knowledge_name ); // 知识点ID与 知识点名称结合 $new_combine_knowledge_name_count = array_keys ( array_count_values ( $new_sub_knowledge_Id_name ) ); // 组合起来数组，组合为一个子知识点名称对应多个根节点与知识点ID foreach ( $new_combine_knowledge_name_count as $ek => $ev ) { foreach ( $new_sub_knowledge_Id_name as $k => $v ) { if ($v == $ev) { $knowledge_count_array [$ev] [$k] = $new_sub_root_knowledge [$k]; } } } $knowledge_count_array = array_filter ( $knowledge_count_array ); $root_id_str_array = array_filter ( $root_id_str_array ); if (! empty ( $knowledge_count_array )) { foreach ( $knowledge_count_array as $sub_knowledge_k => $sub_knowledge_val ) { $sub_biaozhi = false; foreach ( $sub_knowledge_val as $sub_knowledge_val_k => $sub_knowledge_val_val ) { if (in_array ( $sub_knowledge_val_k, $root_id_str_array )) { // 如果同名名称中的一个根节点属于此课程类型下的根节点，则记录下来此知识点ID $sub_biaozhi = true; $data ['sub_knowledge_id'] .= $sub_knowledge_val_val . ','; } } if ($sub_biaozhi == false) { if ($this->dao->execute ( 'INSERT INTO ' . $this->vip_knowledge . ' SET name =' . $this->dao->quote ( $sub_knowledge_k ) . ',' . ' remark = ' . $this->dao->quote ( $sub_knowledge_k ) . ',' . ' is_leaf=1' )) { $temp_combine_knowledge_id = ''; $temp_combine_knowledge_id = $this->dao->lastInsertId (); $data ['sub_knowledge_id'] .= $temp_combine_knowledge_id . ','; if (! empty ( $temp_combine_knowledge_id )) { $this->dao->execute ( 'INSERT INTO ' . $this->vip_knowledge_course_type_rs . ' (knowledge_id,course_type_id) VALUES(' . $this->dao->quote ( $temp_combine_knowledge_id ) . ',' . $this->dao->quote ( $taojueQuestionCourseTypeId ) . ')' ); } } } } } // liang end } else { // 如未查到则进行插入--要想插入只能当根节点进行插入且插入当前课程类型下 $temp_knowledge_id = ''; if ($this->dao->execute ( 'INSERT INTO ' . $this->vip_knowledge . ' SET name =' . $this->dao->quote ( $sub_val ) . ',' . ' remark = ' . $this->dao->quote ( $sub_val ) . ',' . ' is_leaf=1' )) { $temp_knowledge_id = $this->dao->lastInsertId (); $data ['sub_knowledge_id'] .= $temp_knowledge_id . ','; } if (! empty ( $temp_knowledge_id )) { $this->dao->execute ( 'INSERT INTO ' . $this->vip_knowledge_course_type_rs . ' (knowledge_id,course_type_id) VALUES(' . $this->dao->quote ( $temp_knowledge_id ) . ',' . $this->dao->quote ( $taojueQuestionCourseTypeId ) . ')' ); } } } $data ['sub_knowledge_id'] = array_unique ( explode ( ',', trim ( $data ['sub_knowledge_id'], ',' ) ) ); $data ['sub_knowledge_id'] = implode ( ',', $data ['sub_knowledge_id'] ); }
					 */
				}
				/*
				 * $sql5 = 'SELECT id as knowledge_id,parent_id,name FROM ' . $this->vip_knowledge . " WHERE name IN (" . trim ( $data ['subKnowledgeNameStr'] ) . ") "; $data ['sub_knowledge_id_arr'] = $this->dao->getAll ( $sql5 ); $data ['sub_knowledge_id'] = ''; if (! empty ( $data ['sub_knowledge_id_arr'] )) {//根据副知识点名称查找副知点ID与父节点 foreach ( $data ['sub_knowledge_id_arr'] as $key => $row ) { if ($row ['parent_id'] == 0) {//查找父节点是否为根节点如为根节点则存储到数组 $data ['sub_root_knowledge_id'] .= $row ['knowledge_id'] . ',';//根节点 $data ['sub_knowledge_knowledge_id'] .= $row ['knowledge_id'] . ',';//副知识点ID $data ['sub_knowledge_knowledge_name'] .= $row ['name'] . ',';//副知识点名称 } else { $root_id = $this->getRootKnowledgeId ( $row ['knowledge_id'] );//查找到根节点则存储到数组 $data ['sub_root_knowledge_id'] .= $root_id ['id'] . ',';//根节点 $data ['sub_knowledge_knowledge_id'] .= $row ['knowledge_id'] . ',';//副知识点ID $data ['sub_knowledge_knowledge_name'] .= $row ['name'] . ',';//副知识点名称 } } $data ['root_id_arr'] = $this->dao->getAll ( "SELECT knowledge_id FROM " . $this->vip_knowledge_course_type_rs . " WHERE course_type_id = '$data[course_type_id]'" );//查找根节点 $data ['root_id_str'] = ''; if (! empty ( $data ['root_id_arr'] )) {//查找此课程类型下有哪些根节点 foreach ( $data ['root_id_arr'] as $kk => $root ) { $data ['root_id_str'] .= $root ['knowledge_id'] . ','; } } //ligang 14-09-16 start $sub_root_knowledge_id = $root_id_str_array = $sub_knowledge_knowledge_id = $sub_knowledge_combine_array =''; $root_id_str_array = explode(',',trim ( $data ['root_id_str'], ',' ));//此课程类型下的所有根节点 $sub_root_knowledge_id = explode(',',trim ( $data ['sub_root_knowledge_id'], ',' ));//根节点 $sub_knowledge_knowledge_id = explode(',',trim ( $data ['sub_knowledge_knowledge_id'], ',' ));//副知识点ID $sub_knowledge_knowledge_name = explode(',',trim ( $data ['sub_knowledge_knowledge_name'], ',' ));//副知识点名称 $new_sub_root_knowledge = array_combine($sub_root_knowledge_id, $sub_knowledge_knowledge_id);//根节点与知识点ID结合 $new_sub_root_knowledge_re = array_flip($new_sub_root_knowledge);//反转根节点与知识点 $new_sub_knowledge_Id_name = array_combine($new_sub_root_knowledge_re,$sub_knowledge_knowledge_name);//知识点ID与 知识点名称结合 $new_combine_knowledge_name_count = array_keys(array_count_values ($new_sub_knowledge_Id_name)); $knowledge_count_array = array(); //组合起来数组，组合为一个子知识的名称对应多个根节点与知识点ID foreach ($new_combine_knowledge_name_count as $ek=>$ev){ foreach ($new_sub_knowledge_Id_name as $k=>$v){ if($v==$ev){ $knowledge_count_array[$ev][$k] = $new_sub_root_knowledge[$k]; } } } foreach($knowledge_count_array as $sub_knowledge_k=>$sub_knowledge_val){ $sub_biaozhi = false; foreach($sub_knowledge_val as $sub_knowledge_val_k=>$sub_knowledge_val_val){ if(in_array($sub_knowledge_val_k,$root_id_str_array)){//如果同名名称中的一个根节点属于此课程类型下的根节点，则记录下来此知识点ID $sub_biaozhi = true; $data['sub_knowledge_id'] .= $sub_knowledge_val_val. ','; } } if($sub_biaozhi == false){ if ($this->dao->execute ('INSERT INTO ' . $this->vip_knowledge . ' SET name ='.$this->dao->quote ( $sub_knowledge_k ).','.	' remark = '.$this->dao->quote ( $sub_knowledge_k ).','.' is_leaf=1')) { $temp_combine_knowledge_id = ''; $temp_combine_knowledge_id = $this->dao->lastInsertId (); $data ['sub_knowledge_id'] .= $temp_combine_knowledge_id . ','; if (! empty ( $temp_combine_knowledge_id )) { $this->dao->execute ( 'INSERT INTO ' . $this->vip_knowledge_course_type_rs . ' (knowledge_id,course_type_id) VALUES(' . $this->dao->quote ( $temp_combine_knowledge_id ) . ',' . $this->dao->quote ( $taojueQuestionCourseTypeId ) . ')' ); } } } } //liang end } else {//如未查到则进行插入--要想插入只能当根节点进行插入 if (! empty ( $data ['subKnowledgeNameArr'] )) { foreach ( $data ['subKnowledgeNameArr'] as $kk => $subKnowledgeName ) { //echo 'SELECT id FROM ' . $this->vip_knowledge . ' WHERE name = ' . $this->dao->quote ( $subKnowledgeName ) . ' LIMIT 1';exit; $temp_knowledge_id = $this->dao->getOne ( 'SELECT id FROM ' . $this->vip_knowledge . ' WHERE name = ' . $this->dao->quote ( $subKnowledgeName ) . ' LIMIT 1' ); if (empty ( $temp_knowledge_id )) { //echo 'INSERT INTO ' . $this->vip_knowledge . ' SET name ='.$this->dao->quote ( $subKnowledgeName ).','.	' remark = '.$this->dao->quote ( $subKnowledgeName ).','.' is_leaf=1';exit; if ($this->dao->execute ('INSERT INTO ' . $this->vip_knowledge . ' SET name ='.$this->dao->quote ( $subKnowledgeName ).','.	' remark = '.$this->dao->quote ( $subKnowledgeName ).','.' is_leaf=1')) { $temp_knowledge_id = $this->dao->lastInsertId (); $data ['sub_knowledge_id'] .= $temp_knowledge_id . ','; } } if (! empty ( $temp_knowledge_id )) { $this->dao->execute ( 'INSERT INTO ' . $this->vip_knowledge_course_type_rs . ' (knowledge_id,course_type_id) VALUES(' . $this->dao->quote ( $temp_knowledge_id ) . ',' . $this->dao->quote ( $taojueQuestionCourseTypeId ) . ')' ); } } } } $data ['sub_knowledge_id'] = trim ( $data ['sub_knowledge_id'], ',' );
				 */
				
				if (! empty ( $data ['gradesName'] )) { // 适用年级
					$data ['gradesNameArr'] = explode ( ',', trim ( trim ( str_replace ( '，', ',', $data ['gradesName'] ), ',' ) ) );
					$data ['gradesNameStr'] = "'" . implode ( "','", $data ['gradesNameArr'] ) . "'";
					$sql6 = 'SELECT  id FROM ' . $this->vip_dict . " WHERE category = 'GRADE' AND title IN (" . $data ['gradesNameStr'] . ')';
					$data ['grades_id_arr'] = $this->dao->getAll ( $sql6 );
					$data ['grades'] = '';
					if (! empty ( $data ['grades_id_arr'] )) {
						foreach ( $data ['grades_id_arr'] as $key => $row ) {
							$data ['grades'] .= $row ['id'] . ',';
						}
					}
					/*
					 * else { if (! empty ( $data ['gradesNameArr'] )) { foreach ( $data ['gradesNameArr'] as $kk => $gradeName ) { $last_grade_id = $this->dao->getOne ( "SELECT id FROM " . $this->vip_dict . " WHERE category = 'GRADE' ORDER BY id DESC" ); $new_code = str_pad ( 'GR2', 6, $last_grade_id ); if ($this->dao->execute ( 'INSERT INTO ' . $this->vip_dict . ' (category,code,title) VALUES(' . $this->dao->quote ( 'GRADE' ) . ',' . $this->dao->quote ( $new_code ) . ',' . $this->dao->quote ( $gradeName ) . ')' )) { $new_grade_id = $this->dao->lastInsertId (); $data ['grades'] .= $new_grade_id . ','; } } } }
					 */
					$data ['grades'] = trim ( $data ['grades'], ',' );
				}
				
				$paper_flag = true;
				$flag = true;
				$this->dao->execute ( 'begin' ); // 事务开启
				                                 // 套卷
				                                 // if (isset ( $data ['source'] ) || isset ( $data ['year'] ) || isset ( $data ['city'] ) || isset ( $data ['country'] ) || isset ( $data ['school'] ) && isset ( $data ['paper_grades'] ) && isset ( $data ['term'] ) && isset ( $data ['name'] ) && isset ( $data ['curr_dept'] )) {
				                                 // dumps ( $data );
				                                 
				// 分割文件名：套卷-2011-北京市-昌平区--初中-物理-初三-下学期-中考二模-班课-真题-总时长-总分数-总题数.docx
				$paper = array ();
				if (! empty ( $title )) {
					$title1 = substr ( $title, 0, strrpos ( $title, '.' ) );
					$taojue = $createUserName = '';
					$taojue = explode ( '-', $title1 );
					$count = count($taojue);
					$paper ['filename'] = $taojue[0];
					if(trim ( $paper ['filename'] ) == '套卷'){	
						$paper ['filename'] = $taojue [0];
						$paper ['year'] = $taojue [1];
						$paper ['city'] = $taojue [2];
						$paper ['country'] = $taojue [3];
						$paper ['school'] = $taojue [4];
						// $paper['grade_dept_id'] = $taojue[5];
						$paper ['subject_id'] = $taojue [6];
						$paper ['paper_grades'] = $taojue [7];
						$paper ['term'] = $taojue [8];
						$paper ['name'] = $taojue [9];
						$paper ['curr_dept'] = $taojue [10];
						$paper ['source'] = $taojue [11];
						$paper ['duration'] = $taojue [12];
						$paper ['score'] = $taojue [13];
						$paper ['question_number'] = $taojue [14];
						// $createUserName = explode ( '.', $taojue [15] );
						// $paper ['created_user_name'] = $createUserName [0];
						$paper ['created_user_name'] = $taojue [15];

						
					}else  if (trim ( $paper ['filename'] ) == '套卷VIP'){
						$paper ['filename'] = $taojue [0];
						$paper ['year'] = $taojue [3];
						$paper ['subject_name'] = $taojue [2];
						$paper ['province'] = $taojue [4];
						$paper ['city'] = $taojue [5];
						$paper ['country'] = $taojue [6];
						$paper ['school'] = $taojue [7];
						$paper ['grades'] = $taojue [8];
						$paper ['term'] = $taojue [9];
						$paper ['name'] = $taojue [10];
						$paper ['source'] = $taojue [10].$taojue [11];
						$paper ['other1'] = $taojue [12];
						$paper ['other2'] = $taojue [13];
						$paper ['duration'] = $taojue [$count-3];
						$paper ['score'] = $taojue [$count-2];
						$paper ['question_number'] = $taojue [$count-1];
						
					}

					
				}
				
				// 套卷
				if (trim ( $paper ['filename'] ) == '套卷') {
					if (empty ( $paper_id )) {
						$sql8 = 'INSERT INTO ' . $this->vip_paper . '(	grade_id,
																		subject_id,
																		name,
																		source,
																		year,
																		city,
																		country,
																		school,
																		grades,
																		term,
																		department,
																		created_time,
																		created_user_name,
																		approve_user_name,
																		duration,
																		score,
																		question_number,
																		file_name,
																		file_name_md5)
														VALUES (' . $this->dao->quote ( $data ['grade_dept_id'] ) . ',
																' . $this->dao->quote ( $data ['subject_id'] ) . ',
																' . $this->dao->quote ( $paper ['name'] ) . ',
																' . $this->dao->quote ( $paper ['source'] ) . ',
																' . $this->dao->quote ( $paper ['year'] ) . ',
																' . $this->dao->quote ( $paper ['city'] ) . ',
																' . $this->dao->quote ( $paper ['country'] ) . ',
																' . $this->dao->quote ( $paper ['school'] ) . ',
																' . $this->dao->quote ( $paper ['paper_grades'] ) . ',
																' . $this->dao->quote ( $paper ['term'] ) . ',
																' . $this->dao->quote ( $paper ['curr_dept'] ) . ',
																' . $this->dao->quote ( strtotime ( date ( "Y-m-d H:i:s" ) ) ) . ',
																' . $this->dao->quote ( $paper ['created_user_name'] ) . ' ,
																' . $this->dao->quote ( '' ) . ' ,
																' . $this->dao->quote ( $paper ['duration'] ) . ',
																' . $this->dao->quote ( $paper ['score'] ) . ',
																' . $this->dao->quote ( $paper ['question_number'] ) . ',
																' . $this->dao->quote ( $title ) . ',
																' . $this->dao->quote ( md5 ( $title ) ) . ')';
						
						if (! $this->dao->execute ( $sql8 )) {
							$flag == false;
						} else {
							$paper_id = $this->dao->lastInsertId ();
						}
					}
					$question_source = 'CLASS';
				} else if (trim ( $paper ['filename'] ) == '套卷VIP') {
					if (empty ( $paper_id )) {
						$sql8 = 'INSERT INTO ' . $this->vip_paper . '(	grade_id,
																		subject_id,
																		name,
																		source,
																		year,
																		province,
																		city,
																		country,
																		school,
																		grades,
																		term,
																		department,
																		other1,
																		other2,
																		created_time,
																		duration,
																		score,
																		question_number,
																		file_name,
																		file_name_md5)
														VALUES (' . $this->dao->quote ( $data ['grade_dept_id'] ) . ',
																' . $this->dao->quote ( $data ['subject_id'] ) . ',
																' . $this->dao->quote ( $paper ['name'] ) . ',
																' . $this->dao->quote ( $paper ['source'] ) . ',
																' . $this->dao->quote ( $paper ['year'] ) . ',
																' . $this->dao->quote ( $paper ['province'] ) . ',
																' . $this->dao->quote ( $paper ['city'] ) . ',
																' . $this->dao->quote ( $paper ['country'] ) . ',
																' . $this->dao->quote ( $paper ['school'] ) . ',
																' . $this->dao->quote ( $paper ['grades'] ) . ',
																' . $this->dao->quote ( $paper ['term'] ) . ',
																' . $this->dao->quote ( $paper ['department'] ) . ',
																' . $this->dao->quote ( $paper ['other1'] ) . ',
																' . $this->dao->quote ( $paper ['other2'] ) . ',
																' . $this->dao->quote (  strtotime ( date ( "Y-m-d H:i:s" ) ) ) . ',
																' . $this->dao->quote ( $paper ['duration'] ) . ',
																' . $this->dao->quote ( $paper ['score'] ) . ',
																' . $this->dao->quote ( $paper ['question_number'] ) . ',
																' . $this->dao->quote ( $title ) . ',
																' . $this->dao->quote ( md5 ( $title ) ) . ')';
																
						
						if (! $this->dao->execute ( $sql8 )) {
							$flag == false;
						} else {
							$paper_id = $this->dao->lastInsertId ();
						}
					}
					$question_source = 'VIP';
				} else if (trim ( $paper ['filename'] ) == '试题') {
					$title12 = explode ( '-', $title );
					if ($title12 [3] == '班课') {
						$question_source = 'CLASS';
					} else if ($title12 [3] == 'VIP') {
						$question_source = 'VIP';
					} else if ($title12 [3] == '竞赛') {
						$question_source = 'MATCH';
					}
				}
				
				// $data['content'] = strip_tags($data['content'] ,'<img>');
				// $data['analysis'] = strip_tags($data['analysis'] ,'<img>');
				if (! $this->dao->execute ( 'INSERT INTO ' . $this->vip_question . ' (uid,
																					  course_type_id,
																					  question_type_id,
																					  number,
																					  score,
																					  difficulty,
																					  knowledge_id,
																					  sub_knowledge_id,
																					  grades,
																					  content,
																					  content_text,
																					  analysis,
																					  analysis_text,
																	 				  paper_id,
																					  created_time,
																					  created_answer_user_name,
																					  last_updated_time,
																					  sdate,
																					  department,
																					  source,
																					  status) 
														VALUES (' . $this->dao->quote ( $data ['uid'] ) . ',
																' . $this->dao->quote ( $taojueQuestionCourseTypeId ) . ',
																' . $this->dao->quote ( $data ['question_type_id'] ) . ',
																' . $this->dao->quote ( $data ['question_number'] ) . ',
																' . $this->dao->quote ( $data ['score'] ) . ',
																' . $this->dao->quote ( $data ['difficulty'] ) . ',
																' . $this->dao->quote ( $data ['knowledge_id'] ) . ',
																' . $this->dao->quote ( $data ['sub_knowledge_id'] ) . ',
																' . $this->dao->quote ( $data ['grades'] ) . ',
																' . $this->dao->quote ( $data ['content'] ) . ',
																' . $this->dao->quote ( $data ['content_text'] ) . ',
																' . $this->dao->quote ( $data ['analysis'] ) . ',
																' . $this->dao->quote ( $data ['analysis_text'] ) . ',
																' . $this->dao->quote ( $paper_id ) . ',
																' . $this->dao->quote ( $time ) . ' ,
																' . $this->dao->quote ( $paper ['created_user_name'] ) . ',
																' . $this->dao->quote ( $time ) . ' ,
																' . $this->dao->quote ( $data ['sdate'] ) . ' ,
																' . $this->dao->quote ( $question_source ) . ' ,
																' . $this->dao->quote ( $data ['source1'] ) . ' ,
																1
														)' )) {
					$flag == false;
				}
				$new_question_id = $this->dao->lastInsertId ();
				
				// 选项
				if (! empty ( $data ['options'] )) {
					$sort = 0;
					foreach ( $data ['options'] as $key => $option ) {
						$option ['is_answer'] = 0;
						if (strpos ( '0' . $data ['answers_text'] . '0', $option ['title'] ))
							$option ['is_answer'] = 1;
						$sort ++;
						if (! $this->dao->execute ( 'INSERT INTO ' . $this->vip_question_option . ' (
																					uid,
																					question_id,
																					content,
																					content_text,
																					sort,
																					is_answer)
								VALUES (' . $this->dao->quote ( $option ['uid'] ) . ',
										' . $this->dao->quote ( $new_question_id ) . ',
										' . $this->dao->quote ( $option ['content'] ) . ',
										' . $this->dao->quote ( $option ['content_text'] ) . ',
										' . $this->dao->quote ( $sort ) . ',
										' . $this->dao->quote ( $option ['is_answer'] ) . ')' )) {
							$flag == false;
							break;
						}
					}
				}
				// 答案
				if (! empty ( $data ['answers'] ) && (trim ( $data ['questionTypeName'] ) != '单选题' && trim ( $data ['questionTypeName'] ) != '多选题' && trim ( $data ['questionTypeName'] ) != '选择题' && trim ( $data ['questionTypeName'] ) != '不定项选择题')) {
					// $data ['answers'] = strip_tags($data ['answers'] ,'<img>');
					if ($this->dao->execute ( 'INSERT INTO ' . $this->vip_question_answer . ' (
																					question_id,
																					content,
																					content_text)
								VALUES (' . $this->dao->quote ( $new_question_id ) . ',
										' . $this->dao->quote ( $data ['answers'] ) . ',
										' . $this->dao->quote ( $data ['answers_text'] ) . ')' )) {
						$flag == false;
					}
				}
				
				// if (! empty ( $data ['childQuestion'] )) { // 导入子题
				// $flag = $this->importQuestion ( $data ['childQuestion'] );
				// }
				if ($flag === false)
					$this->dao->execute ( 'rollback' ); // 事务回滚
				else
					$this->dao->execute ( 'commit' ); // 事务提交
			}
		}
		return $flag;
	}
	
	/**
	 * *存储修改后的试题
	 * *这个暂且测试，添加试题成功后改为事务提交
	 * *
	 */
	public function edit_save_Question($model = array()) {
		$time = time ();
		if ($model ['id']) {
			$strQuery = 'UPDATE ' . $this->vip_question . '
	                     SET 	 content=' . $this->dao->quote ( $model ['content'] ) . ',
	                             content_text=' . $this->dao->quote ( strip_tags ( $model ['content'] ) ) . ',
	                             analysis=' . $this->dao->quote ( $model ['analysis'] ) . ',
	                             last_updated_user_name=' . $this->dao->quote ( $model ['user_name'] ) . ',
	                             last_updated_time=' . $this->dao->quote ( $time ) . '
	                    WHERE 	id=' . $this->dao->quote ( $model ['id'] );
			
			if (false == $this->dao->execute ( $strQuery ))
				return array (
						'errorMsg' => '试题修改保存失败' 
				);
				
				// 选项
			$options = $model ['options'];
			$euids = $model ['euids'];
			
			if (! empty ( $options )) {
				// 删除掉以前的选项
				$strOptions_del = ' DELETE FROM ' . $this->vip_question_option . '	WHERE  question_id = ' . $this->dao->quote ( $model ['id'] );
				if (false == $this->dao->execute ( $strOptions_del ))
					return array (
							'errorMsg' => '试题修改保存失败' 
					);
					// 添加新的选项
				$answers = $model ['options_answer_flag'];
				for($i = 0; $i < count ( $options ); $i ++) {
					$strQ = 'INSERT INTO ' . $this->vip_question_option . '
							SET 			uid =	' . $this->dao->quote ( $euids [$i] ) . ',
											question_id =	' . $this->dao->quote ( $model ['id'] ) . ',
											content =	' . $this->dao->quote ( $options [$i] ) . ',
											sort =	' . $this->dao->quote ( $i ) . ',
											is_answer=	' . $this->dao->quote ( in_array ( $i, $answers ) ? 1 : 0 );
					if (false == $this->dao->execute ( $strQ ))
						return array (
								'errorMsg' => '选项信息保存失败' 
						);
				}
			}
			
			// 答案
			$answers = $model ['answers'];
			if (! empty ( $answers )) {
				
				// 删除掉以前的答案
				$answer_del = ' DELETE FROM ' . $this->vip_question_answer . '	WHERE  question_id = ' . $this->dao->quote ( $model ['id'] );
				if (false == $this->dao->execute ( $answer_del ))
					return array (
							'errorMsg' => '答案修改保存失败' 
					);
				for($i = 0; $i < count ( $answers ); $i ++) {
					
					$strA = 'INSERT INTO ' . $this->vip_question_answer . '
							SET 			question_id =	' . $this->dao->quote ( $model ['id'] ) . ',
											content =	' . $this->dao->quote ( $answers [$i] ) . ',
											sort =	' . $this->dao->quote ( $answers [$i] ['sort'] );
					if (false == $this->dao->execute ( $strA ))
						return array (
								'errorMsg' => '答案信息保存失败' 
						);
				}
			}
			
			// 答案
			$answers = $model ['answers'];
			if (! empty ( $answers )) {
				for($i = 0; $i < count ( $answers ); $i ++) {
					
					$strAnswers = 'UPDATE ' . $this->vip_question_answer . '
							SET content=' . $this->dao->quote ( $answers [$i] ['content'] ) . ',
								sort=' . $this->dao->quote ( $answers [$i] ['sort'] ) . '
							WHERE id=' . $this->dao->quote ( $model ['id'] );
					if (false == $this->dao->execute ( $strAnswers ))
						return array (
								'errorMsg' => '答案保存失败' 
						);
				}
			}
			return true;
		} else
			return false;
	}
	public function edit_save_simple_question($model = array()) {
		$time = time ();
		if ($model ['id']) {
			$strQuery = 'UPDATE ' . $this->vip_question . '
	                     SET 	 content=' . $this->dao->quote ( $model ['content'] ) . ',
	                             content_text=' . $this->dao->quote ( strip_tags ( $model ['content'] ) ) . ',
	                             analysis=' . $this->dao->quote ( $model ['analysis'] ) . ',
	                             analysis_text=' . $this->dao->quote ( strip_tags ( $model ['analysis'] ) ) . ',
	                             content_error_types=' . $this->dao->quote ( strip_tags ( $model ['content_error_types'] ) ) . ',
	                             is_edit1 = 1,
								 in_used1 = 0,
	                             lock_row_time1 = NULL,
	                             last_updated_user_name=' . $this->dao->quote ( $model ['user_name'] ) . ',
	                             last_updated_time=' . $this->dao->quote ( $time ) . ',
						 		 content_last_updated_time = ' . $this->dao->quote ( time () ) . '
	                     WHERE	 id=' . $this->dao->quote ( $model ['id'] );
			
			if (false == $this->dao->execute ( $strQuery ))
				return array (
						'errorMsg' => '试题修改保存失败' 
				);
				
				// 选项
			$options = $model ['options'];
			$euids = $model ['euids'];
			if (! empty ( $options )) {
				// 删除掉以前的选项
				$strOptions_del = ' DELETE FROM ' . $this->vip_question_option . '	WHERE  question_id = ' . $this->dao->quote ( $model ['id'] );
				if (false == $this->dao->execute ( $strOptions_del ))
					return array (
							'errorMsg' => '试题修改保存失败' 
					);
					// 添加新的选项
				$answers = $model ['options_answer_flag'];
				for($i = 0; $i < count ( $options ); $i ++) {
					$strQ = 'INSERT INTO ' . $this->vip_question_option . '
							 SET 			uid =	' . $this->dao->quote ( $euids [$i] ) . ',
											question_id =	' . $this->dao->quote ( $model ['id'] ) . ',
											content =	' . $this->dao->quote ( $options [$i] ) . ',
											content_text =	' . $this->dao->quote ( strip_tags ( $options [$i] ) ) . ',
											sort =	' . $this->dao->quote ( $i + 1 ) . ',
											is_answer=	' . $this->dao->quote ( in_array ( $i, $answers ) ? 1 : 0 );
					if (false == $this->dao->execute ( $strQ ))
						return array (
								'errorMsg' => '选项信息保存失败' 
						);
				}
			}
			
			// 答案
			$answers = $model ['answers'];
			if (! empty ( $answers )) {
				
				// 删除掉以前的答案
				$answer_del = ' DELETE FROM ' . $this->vip_question_answer . '	WHERE  question_id = ' . $this->dao->quote ( $model ['id'] );
				if (false == $this->dao->execute ( $answer_del ))
					return array (
							'errorMsg' => '答案修改保存失败' 
					);
				for($i = 0; $i < count ( $answers ); $i ++) {
					
					$strA = 'INSERT INTO ' . $this->vip_question_answer . '
							SET 			question_id =	' . $this->dao->quote ( $model ['id'] ) . ',
											content =	' . $this->dao->quote ( $answers [$i] ) . ',
											sort =	' . $this->dao->quote ( $answers [$i] ['sort'] );
					if (false == $this->dao->execute ( $strA ))
						return array (
								'errorMsg' => '答案信息保存失败' 
						);
				}
			}
			
			// 答案
			$answers = $model ['answers'];
			if (! empty ( $answers )) {
				for($i = 0; $i < count ( $answers ); $i ++) {
					
					$strAnswers = 'UPDATE ' . $this->vip_question_answer . '
							SET content=' . $this->dao->quote ( $answers [$i] ['content'] ) . ',
								sort=' . $this->dao->quote ( $answers [$i] ['sort'] ) . '
							WHERE id=' . $this->dao->quote ( $model ['id'] );
					if (false == $this->dao->execute ( $strAnswers ))
						return array (
								'errorMsg' => '答案保存失败' 
						);
				}
			}
			return true;
		} else
			return false;
	}
	public function edit_save_classic_question($model = array()) {
		$time = time ();
		if ($model ['id']) {
			$strQuery = 'UPDATE ' . $this->vip_question . '
	                     SET 	 content=' . $this->dao->quote ( $model ['content'] ) . ',
	                             content_text=' . $this->dao->quote ( strip_tags ( $model ['content'] ) ) . ',
	                             analysis=' . $this->dao->quote ( $model ['analysis'] ) . ',
	                             analysis_text=' . $this->dao->quote ( strip_tags ( $model ['analysis'] ) ) . ',
	                             content_error_types=' . $this->dao->quote ( strip_tags ( $model ['content_error_types'] ) ) . ',
	                             is_edit2 = 1,
								 in_used2 = 0,
	                             lock_row_time2 = NULL,
	                             last_updated_user_name=' . $this->dao->quote ( $model ['user_name'] ) . ',
	                             last_updated_time=' . $this->dao->quote ( $time ) . ',
						 		 content_last_updated_time = ' . $this->dao->quote ( time () ) . '
	                     WHERE	 id=' . $this->dao->quote ( $model ['id'] );
			
			if (false == $this->dao->execute ( $strQuery ))
				return array (
						'errorMsg' => '试题修改保存失败' 
				);
				
				// 选项
			$options = $model ['options'];
			$euids = $model ['euids'];
			if (! empty ( $options )) {
				// 删除掉以前的选项
				$strOptions_del = ' DELETE FROM ' . $this->vip_question_option . '	WHERE  question_id = ' . $this->dao->quote ( $model ['id'] );
				if (false == $this->dao->execute ( $strOptions_del ))
					return array (
							'errorMsg' => '试题修改保存失败' 
					);
					// 添加新的选项
				$answers = $model ['options_answer_flag'];
				for($i = 0; $i < count ( $options ); $i ++) {
					$strQ = 'INSERT INTO ' . $this->vip_question_option . '
							 SET 			uid =	' . $this->dao->quote ( $euids [$i] ) . ',
											question_id =	' . $this->dao->quote ( $model ['id'] ) . ',
											content =	' . $this->dao->quote ( $options [$i] ) . ',
											content_text =	' . $this->dao->quote ( strip_tags ( $options [$i] ) ) . ',
											sort =	' . $this->dao->quote ( $i + 1 ) . ',
											is_answer=	' . $this->dao->quote ( in_array ( $i, $answers ) ? 1 : 0 );
					if (false == $this->dao->execute ( $strQ ))
						return array (
								'errorMsg' => '选项信息保存失败' 
						);
				}
			}
			
			// 答案
			$answers = $model ['answers'];
			if (! empty ( $answers )) {
				
				// 删除掉以前的答案
				$answer_del = ' DELETE FROM ' . $this->vip_question_answer . '	WHERE  question_id = ' . $this->dao->quote ( $model ['id'] );
				if (false == $this->dao->execute ( $answer_del ))
					return array (
							'errorMsg' => '答案修改保存失败' 
					);
				for($i = 0; $i < count ( $answers ); $i ++) {
					
					$strA = 'INSERT INTO ' . $this->vip_question_answer . '
							SET 			question_id =	' . $this->dao->quote ( $model ['id'] ) . ',
											content =	' . $this->dao->quote ( $answers [$i] ) . ',
											sort =	' . $this->dao->quote ( $answers [$i] ['sort'] );
					if (false == $this->dao->execute ( $strA ))
						return array (
								'errorMsg' => '答案信息保存失败' 
						);
				}
			}
			
			// 答案
			$answers = $model ['answers'];
			if (! empty ( $answers )) {
				for($i = 0; $i < count ( $answers ); $i ++) {
					
					$strAnswers = 'UPDATE ' . $this->vip_question_answer . '
							SET content=' . $this->dao->quote ( $answers [$i] ['content'] ) . ',
								sort=' . $this->dao->quote ( $answers [$i] ['sort'] ) . '
							WHERE id=' . $this->dao->quote ( $model ['id'] );
					if (false == $this->dao->execute ( $strAnswers ))
						return array (
								'errorMsg' => '答案保存失败' 
						);
				}
			}
			return true;
		} else
			return false;
	}
	public function editSimpleQuestion($data) {
		return $this->dao->execute ( 'UPDATE ' . $this->vip_question . '
	                 SET difficulty=' . $this->dao->quote ( $data ['difficulty'] ) . ',
                         knowledge_id = ' . $this->dao->quote ( $data ['knowledge_id'] ) . ',
                         sub_knowledge_id = ' . $this->dao->quote ( $data ['sub_knowledge_id'] ) . ',
                         is_content_error = ' . $this->dao->quote ( $data ['is_content_error'] ) . ',
						 is_classic = ' . $this->dao->quote ( $data ['is_classic'] ) . ',
						 is_edit = 1,
						 in_used = 0,
						 lock_row_time = NULL,
						 last_updated_user_name = ' . $this->dao->quote ( $data ['user_name'] ) . ',
                         last_updated_time = ' . $this->dao->quote ( time () ) . ',
						 knode_last_updated_time = ' . $this->dao->quote ( time () ) . '
	                 WHERE id=' . $this->dao->quote ( $data ['id'] ) );
	}
	public function skipQuestion($questionId) {
		return $this->dao->execute ( 'UPDATE ' . $this->vip_question . '
									  SET in_used = 0,
										  lock_row_time = NULL,
										  current_used_user_name = \'\'
                 					  WHERE id=' . $this->dao->quote ( $questionId ) );
	}
	public function skipQuestion1($questionId) {
		return $this->dao->execute ( 'UPDATE ' . $this->vip_question . '
									  SET in_used1 = 0,
										  lock_row_time1 = NULL,
										  current_used_user_name1 = \'\'
                 					  WHERE id=' . $this->dao->quote ( $questionId ) );
	}
	public function skipQuestion2($questionId) {
		return $this->dao->execute ( 'UPDATE ' . $this->vip_question . '
									  SET in_used2 = 0,
										  lock_row_time2 = NULL,
										  current_used_user_name2 = \'\'
                 					  WHERE id=' . $this->dao->quote ( $questionId ) );
	}
	public function getRootKnowledgeId($knowledgeId) {
		$knowledgeInfo = $this->dao->getRow ( 'SELECT id,parent_id,name FROM ' . $this->vip_knowledge . ' WHERE id = ' . $this->dao->quote ( $knowledgeId ) );
		if ($knowledgeInfo ['parent_id'] == 0) {
			// return $knowledgeInfo ['id'];
			return $knowledgeInfo;
		} else {
			return $this->getRootKnowledgeId ( $knowledgeInfo ['parent_id'] );
		}
	}
	public function getKnowledgesByParentId($parentId, $courseTypeId) {
		$where = '';
		if (empty ( $parentId )) {
			$where = ' AND a.parent_id = 0';
			if (! empty ( $courseTypeId )) {
				$where .= ' AND a.course_type_id = ' . $this->dao->quote ( $courseTypeId );
			} else {
				return array ();
			}
		} else {
			$where = ' AND a.parent_id = ' . $this->dao->quote ( $parentId );
		}
		
		return $this->dao->getAll ( 'SELECT a.`id`,
											a.`name`,
											a.`remark`,
											a.`sort`,
											a.`parent_id`,
											a.`analysis`,
											a.`status`,
											a.`state`,
											a.`is_leaf`,
											a.`is_gaosi`,
											a.`origin_knowledge_id`,
											a.`level` 
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 ' . $where . ' ORDER BY a.sort, a.id' );
	}
	public function getKnowledgesByParentId1($parentId, $courseTypeId) {
		$where = '';
		if (empty ( $parentId )) {
			$where = ' AND a.parent_id = 0';
			if (! empty ( $courseTypeId )) {
				$where .= ' AND a.course_type_id = ' . $this->dao->quote ( $courseTypeId );
			} else {
				return array ();
			}
		} else {
			$where = ' AND a.parent_id = ' . $this->dao->quote ( $parentId );
		}
	
		
		return $this->dao->getAll ( 'SELECT a.`id`,
											a.`name`,
											a.`remark`,
											a.`sort`,
											a.`parent_id`,
											a.`analysis`,
											a.`status`,
											a.`state`,
											a.is_leaf,
											a.level,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.id and b.status = 1 and department = \'CLASS\' and b.is_classic = 1) end as knode_classic_question_num,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.id and b.status = 1 and department = \'CLASS\') end as knode_question_num
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 ' . $where . ' ORDER BY a.sort, a.id' );
	}
	public function getKnowledgesByParentId2($parentId, $courseTypeId, $knowledgeTypeId=1) {
		$where = '';
		if (empty ( $parentId )) {
			if (! empty ( $courseTypeId )) {
				// 第一级
				$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND a.parent_id = 0 AND a.course_type_id = ' . $this->dao->quote ( $courseTypeId ) );
				$knowledgeIds = arr2nav ( $rows, ',', 'id' );
				
				// 第二级
				if(!empty($knowledgeIds)){
					$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_knowledge . ' a
									WHERE a.status = 1 AND a.parent_id IN (' . $knowledgeIds . ') ' );
					$knowledgeIds = arr2nav ( $rows, ',', 'id' );
					// 第三级
					if(!empty($knowledgeIds)){
						$rows = $this->dao->getAll ( 'SELECT a.`id`
										FROM ' . $this->vip_view_knowledge . ' a
										WHERE a.status = 1 AND a.parent_id IN (' . $knowledgeIds . ') ' );
						$knowledgeIds = arr2nav ( $rows, ',', 'id' );
						//第四级
						if(!empty($knowledgeIds)){
							$where .= ' AND a.parent_id IN (' . $knowledgeIds . ') ';
						}else{
							return array ();
						}

					}else{
						return array ();
					}
					
				}else{
					return array ();
				}
				
			} else {
				return array ();
			}
		} else {
			$where = ' AND a.parent_id = ' . $this->dao->quote ( $parentId );
		}
		
		return $this->dao->getAll ( 'SELECT a.`id`,
											a.`name`,
											a.`remark`,
											a.`sort`,
											a.`parent_id`,
											a.`analysis`,
											a.`status`,
											a.`state`,
											a.is_leaf,
											a.level,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.id and b.status = 1 and department = \'CLASS\' and b.is_classic = 1) end as knode_classic_question_num,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.id and b.status = 1 and department = \'CLASS\') end as knode_question_num
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 ' . $where . ' ORDER BY a.sort, a.id' );
	}
	public function getKnowledgesByWhere($parentId, $courseTypeId, $kw) {
		$where = '';
		if (empty ( $parentId )) {
			// 第一级
			$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND a.course_type_id = ' . $this->dao->quote ( $courseTypeId ) );
			$knowledgeIds = arr2nav ( $rows, ',', 'id' );
			
			// 第二级
			$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_knowledge . ' a
									WHERE a.status = 1 AND a.parent_id IN (' . $knowledgeIds . ')' );
			$knowledgeIds = arr2nav ( $rows, ',', 'id' );
			// 第三级
			$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND a.parent_id IN (' . $knowledgeIds . ')' );
			$knowledgeIds = arr2nav ( $rows, ',', 'id' );
			// 第四级
			$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND a.parent_id IN (' . $knowledgeIds . ')' );
			$knowledgeIds = arr2nav ( $rows, ',', 'id' );
			
			// 第五级
			$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND a.parent_id IN (' . $knowledgeIds . ')' );
			$knowledgeIds1 = arr2nav ( $rows, ',', 'id' );
			
			// $row = $this->dao->getRow ( 'SELECT fn_vip_get_knowledge_child_list(\'' . $rootIds . '\') AS ids' );
			if ($knowledgeIds) {
				// $ids = $row ['ids'];
				// if ($ids != '$,') {
				$knowledgeIds = str_replace ( ',', "','", $knowledgeIds );
				return $this->dao->getAll ( 'SELECT a.`id`,
													a.`name` as text,
													a.`remark`,
													a.`sort`,
													a.`parent_id`,
													a.`analysis`,
													a.`status`,
													a.`state`,
													a.is_leaf
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND ((a.id IN(\'' . $knowledgeIds . '\') AND a.name LIKE \'%' . $kw . '%\') OR a.id IN (SELECT parent_id FROM ' . $this->vip_view_knowledge . ' WHERE a.id IN(\'' . $knowledgeIds . '\') AND name LIKE \'%' . $kw . '%\')) ORDER BY a.sort, a.id' );
				// }
				// return array ();
			}
			return array ();
		} else {
			return $this->dao->getAll ( 'SELECT a.`id`,
											a.`name` as text,
											a.`remark`,
											a.`sort`,
											a.`parent_id`,
											a.`analysis`,
											a.`status`,
											a.`state`,
											a.is_leaf,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.id and b.status = 1 and department = \'CLASS\' and b.is_classic = 1) end as knode_classic_question_num,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.id and b.status = 1 and department = \'CLASS\') end as knode_question_num
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND a.parent_id = ' . $parentId . ' ORDER BY a.sort, a.id' );
		}
	}
	public function getKnowledgesByParentIdChild($parentId, $courseTypeId) {
		$where = '';
		if (empty ( $parentId )) {
			$where = ' AND a.parent_id = 0';
			if (! empty ( $courseTypeId )) {
				$where .= ' AND a.course_type_id = ' . $this->dao->quote ( $courseTypeId );
			} else {
				return array ();
			}
		} else {
			$where = ' AND a.parent_id = ' . $this->dao->quote ( $parentId );
		}
		
		return $this->dao->getAll ( 'SELECT a.`id`,
											a.`name` as text,
											a.`status`,
											a.`state`,
											a.`is_leaf`
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 ' . $where . ' ORDER BY a.sort, a.id' );
	}
	public function getKnowledgesByParentIdChild1($parentId, $courseTypeId) {
		$where = '';
		/*if (empty ( $parentId )) {
			$where = ' AND a.parent_id = 0';
			if (! empty ( $courseTypeId )) {
				$where .= ' AND a.course_type_id = ' . $this->dao->quote ( $courseTypeId );
			} else {
				return array ();
			}
		} else {
			$where = ' AND a.parent_id = ' . $this->dao->quote ( $parentId );
		}
		
		return $this->dao->getAll ( 'SELECT a.`id`,
											a.`name` as text,
											a.`status`,
											a.`state`,
											a.`is_leaf`,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.id and b.status = 1 and department = \'CLASS\' and b.is_classic = 1) end as knode_classic_question_num,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.id and b.status = 1 and department = \'CLASS\') end as knode_question_num
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 ' . $where . ' ORDER BY a.sort, a.id' );*/
		if(empty ( $parentId )){
			$where = ' AND a.parent_id = 0';
			if (! empty ( $courseTypeId )) {
				$where .= ' AND a.id IN (SELECT knowledge_id FROM vip_knowledge_course_type_rs WHERE course_type_id = '. $this->dao->quote ( $courseTypeId ).') ' ;
			} else {
				return array ();
			}
		}else{
			$where = ' AND a.parent_id = ' . $this->dao->quote ( $parentId );
		}
		return $this->dao->getAll ( 'SELECT a.`id`,
											a.`name` as text,
											a.`status`,
											case `a`.`is_leaf` when 0 then \'closed\' else \'\' end AS state,
											a.`is_leaf`,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.id and b.status = 1 and department = \'CLASS\' and b.is_classic = 1) end as knode_classic_question_num,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.id and b.status = 1 and department = \'CLASS\') end as knode_question_num
									FROM ' . $this->vip_knowledge . ' a
									WHERE a.status = 1 ' . $where . ' ORDER BY a.sort, a.id' );
	}
	public function getPapersDetailByWhere($where = '', $currentPage, $pageSize, $sort, $order) {
		$currentPage = empty ( $currentPage ) ? 1 : ($currentPage - 1) * $pageSize;
		$pageSize = empty ( $pageSize ) ? 20 : $pageSize;
		
		$sort = empty ( $sort ) ? 'a.created_time' : $sort;
		$order = empty ( $order ) ? 'DESC' : $order;
		$sql = 'SELECT a.`id`,a.`grade_id`,a.`subject_id`,a.`name`,
											 a.`file_name`,
											 FROM_UNIXTIME(a.`created_time`) AS `created_time`,
											 a.last_updated_user_name,
											 FROM_UNIXTIME(a.`last_updated_time`) as `last_updated_time`,
											 a.last_updated_user_name,
											 b.title AS grade_name,
											 c.title AS subject_name 
									 FROM ' . $this->vip_paper . ' a
									 LEFT JOIN ' . $this->vip_dict_grade . ' b ON a.grade_id = b.id
									 LEFT JOIN ' . $this->vip_dict_subject . ' c ON a.subject_id = c.id
									 LEFT JOIN (select COUNT(*) question_count, paper_id from vip_question where status = 1 group by paper_id) d ON a.id = d.paper_id
									 WHERE a.status = 1 ' . $where . '
									 ORDER BY ' . $sort . ' ' . $order . '
									 limit ' . $currentPage . ', ' . $pageSize  ;

		$sql = 'SELECT a.`id`,a.`grade_id`,a.`subject_id`,a.`name`,
											 a.`file_name`,
											 FROM_UNIXTIME(a.`created_time`) AS `created_time`,
											 a.last_updated_user_name,
											 FROM_UNIXTIME(a.`last_updated_time`) as `last_updated_time`,
											 a.last_updated_user_name,
											 b.title AS grade_name,
											 c.title AS subject_name 
									 FROM ' . $this->vip_paper . ' a
									 LEFT JOIN ' . $this->vip_dict_grade . ' b ON a.grade_id = b.id
									 LEFT JOIN ' . $this->vip_dict_subject . ' c ON a.subject_id = c.id
									 WHERE a.status = 1 ' . $where . '
									 ORDER BY ' . $sort . ' ' . $order . '
									 limit ' . $currentPage . ', ' . $pageSize  ;
		
		
		$list = $this->dao->getAll ($sql);
		$paperIdArr = array();
		if($list){
			foreach ($list as $key=>$row){
				$paperIdArr[] = $row['id'];
				
			}
			$paperIdStr = implode(',',$paperIdArr);
		}
		$where2 = ' AND 1=1 ';
		if($paperIdStr){
			$where2 = ' and  paper_id IN ('.$paperIdStr.')';
		}
		$sql2 = 'select COUNT(*) question_count, paper_id from vip_question where status = 1 '.$where2.' group by paper_id';
		$list2 = $this->dao->getAll ($sql2);
		$countArr = array();
		if($list2){
			foreach ($list2 as $key=>$row){
				$countArr[$row['paper_id']] = $row['question_count'];
			}
		}
		if($list){
			foreach ($list as $key=>$row){
				$list[$key]['question_count'] = $countArr[$row['id']];
			}
		}
		
		/*$list = $this->dao->getAll ( 'SELECT a.`id`,
											 a.`file_name`,
											 FROM_UNIXTIME(a.`created_time`) AS `created_time`,
											 a.last_updated_user_name,
											 FROM_UNIXTIME(a.`last_updated_time`) as `last_updated_time`,
											 a.last_updated_user_name,
											 b.title AS grade_name,
											 c.title AS subject_name,
											 d.question_count
									 FROM ' . $this->vip_paper . ' a
									 LEFT JOIN ' . $this->vip_dict_grade . ' b ON a.grade_id = b.id
									 LEFT JOIN ' . $this->vip_dict_subject . ' c ON a.subject_id = c.id
									 LEFT JOIN (select COUNT(*) question_count, paper_id from vip_question where status = 1 group by paper_id) d ON a.id = d.paper_id
									 WHERE a.status = 1 ' . $where . '
									 ORDER BY ' . $sort . ' ' . $order . '
									 limit ' . $currentPage . ', ' . $pageSize );*/
		$total = 0;
		if ($list) {
			$row = $this->dao->getRow ( ' SELECT COUNT(*) AS cnt
									 FROM ' . $this->vip_paper . ' a
									 WHERE a.status = 1 ' . $where );
			if ($row) {
				$total = $row ['cnt'];
			}
		}
		return array (
				'total' => $total,
				'rows' => $list 
		);
	}
	public function getPaperInfoById($id) {
		$id=$this->dao->quote ( abs ( $id ) );
		return $this->dao->getRow( 'SELECT * FROM ' . $this->vip_paper . ' WHERE id = ' .$id  );
	}
	public function setPaperInfoByData($data) {
        $show_start=$this->dao->quote($data['show_start']);
		$show_name=$this->dao->quote($data['show_name']);
		$file_name=$this->dao->quote ( $data ['file_name'] );
		$grade_id=$this->dao->quote($data['grade_id']);
		$subject_id=$this->dao->quote($data['subject_id']);
		$province=$this->dao->quote($data['province']);
		$city=$this->dao->quote($data['city']);
		$country=$this->dao->quote($data['country']);
		$test_name=$this->dao->quote($data['test_name']);
		$year=$this->dao->quote($data['year']);
		$school=$this->dao->quote($data['school']);
		$term=$this->dao->quote($data['term']);
		$source=$this->dao->quote($data['source']);
		$name=$this->dao->quote($data['name']);
		$score=$this->dao->quote($data['score']);
		$grades=$this->dao->quote($data['grades']);
		$duration=$this->dao->quote($data['duration']);
		$question_number=$this->dao->quote($data['question_number']);
		$id=$this->dao->quote ( $data ['id'] ) ;
		$last_updated_user_name=$this->dao->quote($data['last_updated_user_name']);
		$last_updated_time=$this->dao->quote($data['last_updated_time']);
		$this->dao->execute('begin');

		$paperResult=$this->dao->execute ( 'UPDATE ' . $this->vip_paper . ' 
									  SET file_name = ' . $file_name. '
									  ,show_name='.$show_name.'
									  ,show_start='.$show_start.'
									  ,grade_id='.$grade_id.'
									  ,subject_id='.$subject_id.'
									  ,province='.$province.'
									  ,city='.$city.'
									  ,country='.$country.'
									  ,test_name='.$test_name.'
									  ,year='.$year.'
									  ,school='.$school.'
									  ,term='.$term.'
									  ,source='.$source.'
									  ,name='.$name.'
									  ,score='.$score.'
									  ,duration='.$duration.'
									  ,grades='.$grades.'
									  ,question_number='.$question_number.'
									  ,last_updated_user_name='.$last_updated_user_name.'
									  ,last_updated_time='.$last_updated_time.'
									  WHERE id = ' .$id );
		$data=array();
		if(!$paperResult)
		{
			$this->dao->execute('rollback');
			return false;
		}
		//查询该套卷下是否存在试题
		$question=$this->dao->getRow("select * from ".$this->vip_question." where paper_id=".$id);
		//判断$question是否是数组
		if(is_array($question) && !empty($question))
		{
			//更新试题库数据
			$result=$this->dao->execute("update ".$this->vip_question." set grade_id=".$grade_id.",subject_id=".$subject_id.",city=".$city.",country=".$country.",test_name=".$test_name.",year=".$year.",school=".$school.",term=".$term.",source=".$source.",name=".$name.",last_updated_user_name=".$last_updated_user_name.",last_updated_time=".$last_updated_time." where paper_id=".$id);
			if(!$result)
			{
				$this->dao->execute("rollback");
				return false;
			}
		}

		$this->dao->execute('commit');
		return true;
	}
	public function setQuestionClassic($questionId) {
		return $this->dao->execute ( 'UPDATE ' . $this->vip_question . '
									  SET is_classic = (is_classic + 1) % 2
                 					  WHERE id=' . $this->dao->quote ( $questionId ) );
	}
	public function updateQuestion($questionId, $questionTypeId, $difficulty, $grades, $knowledgeId, $subKnowledgeId) {
		return $this->dao->execute ( 'UPDATE ' . $this->vip_question . ' SET question_type_id = ' . $this->dao->quote ( $questionTypeId ) . ', difficulty = ' . $this->dao->quote ( $difficulty ) . ', grades = ' . $this->dao->quote ( $grades ) . ', knowledge_id = ' . $this->dao->quote ( $knowledgeId ) . ', sub_knowledge_id = ' . $this->dao->quote ( $subKnowledgeId ) . ' WHERE id = ' . $this->dao->quote ( $questionId ) );
	}
	
	
	
	
	
	/*edit by xcp ===============================================================================================================*/
	public function getKnowledgeTypes($params){
		if($params['is_gaosi'] == 1){
			$where = ' AND is_gaosi = '.$this->dao->quote($params['is_gaosi']);
		}
		return $this->dao->getAll ( 'SELECT id, title, sort FROM '.$this->vip_dict_knowledge_type.' WHERE status = 1 AND subject_id =  ('.$params['subjectid'].') '.$where.' ORDER BY sort ASC' );
	}
	
	public function getCourseTypesBySubjectIdAndKnowledgeTypeId($subjectId,$knowledgeTypeId) {
		$subjectIds=$this->dao->quote ( $subjectId );
		$knowledgeTypeIds=$this->dao->quote ( $knowledgeTypeId );
		return $this->dao->getAll ( 'SELECT `id`,
										    `title`,
										    `status`,
										    `sort`
									FROM ' . $this->vip_dict_course_type . '
								 	WHERE status = 1 AND subject_id = ' . $subjectIds . ' AND knowledge_type_id = ' . $knowledgeTypeIds . ' ORDER BY sort' );
	}
	
	public function getSubjectIdByCourseTypeId($courseTypeId){
		return $this->dao->getOne('SELECT subject_id FROM '.$this->vip_dict_course_type.' WHERE id = '.$this->dao->quote($courseTypeId));
	}
	
	public function matchKnowledge($knowledge = array()) {
		$parentId = $knowledge ['parent_id'];
		$flag = true;
		$this->dao->execute ( 'begin' ); // 事务开启
		if (! empty ( $parentId )) {
			$sql = 'UPDATE ' . $this->vip_knowledge . ' SET is_leaf = 0 WHERE id = ' . $this->dao->quote ( $parentId ) . ' AND is_leaf = 1';
			if ($this->dao->execute ( $sql ))
			$flag = true;
			else
			$flag = false;
		}
			
		if ($flag == true) {
			$sql2 = 'INSERT INTO ' . $this->vip_knowledge . ' (name, remark, parent_id, analysis, sort, is_leaf, level, is_gaosi, origin_knowledge_id) VALUES (' . $this->dao->quote ( $knowledge ['name'] ) . ', ' . $this->dao->quote ( $knowledge ['remark'] ) . ', ' . $this->dao->quote ( $parentId ) . ', ' . $this->dao->quote ( $knowledge ['analysis'] ) . ', ' . $this->dao->quote ( $knowledge ['sort'] ) . ', 1, ' . $this->dao->quote ( $knowledge ['level'] ) . ', ' . $this->dao->quote ( $knowledge ['is_gaosi'] ) . ', ' . $this->dao->quote ( $knowledge ['origin_knowledge_id'] ) . ')';
			if ($this->dao->execute ( $sql2 )) {
				$id = $this->dao->lastInsertId ();
				$flag = true;
			} else
			$flag = false;
		}

		// 如为父节点则插入知识点属性表
		/*if (empty ( $parentId ) && $flag == true) {
			$sql3 = 'INSERT INTO ' . $this->vip_knowledge_course_type_rs . ' (knowledge_id, course_type_id) VALUES (' . $this->dao->quote ( $id ) . ', ' . $this->dao->quote ( $knowledge ['coursetypeid'] ) . ')';

			if ($this->dao->execute ( $sql3 ))
			$flag = true;
			else
			$flag = false;
		}*/

		if ($flag === false)
		$this->dao->execute ( 'rollback' ); // 事务回滚
		else
		$this->dao->execute ( 'commit' ); // 事务提交

		return $flag;
	}
	
	
	public function getKnowledgeTypeByTitle( $title ){
		return $this->dao->getRow('SELECT id,title FROM '.$this->vip_dict_knowledge_type.' WHERE status = 1 AND title = '. $this->dao->quote ( $title ) );
	}
	
	
	public function checkKnowledgeIsExist($knowledge = array()){
		$count = $this->dao->getOne('SELECT COUNT(1) FROM '.$this->vip_knowledge.' WHERE name = '. $this->dao->quote ( $knowledge ['name'] ) .' AND parent_id = '. $this->dao->quote ( $knowledge ['parent_id'] ) .' AND is_gaosi = '.$this->dao->quote ( $knowledge ['is_gaosi'] ));
		if($count == 0)
			return false;
		else 
			return true;
	}
	
	
	public function getKnowledgesByWhere3($parentId, $courseTypeId, $kw) {
		$where = '';
		if (empty ( $parentId )) {
			// 第一级
			$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND a.course_type_id = ' . $this->dao->quote ( $courseTypeId ) );
			$knowledgeIds = arr2nav ( $rows, ',', 'id' );
			
			// 第二级
			$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_knowledge . ' a
									WHERE a.status = 1 AND a.parent_id IN (' . $knowledgeIds . ')' );
			$knowledgeIds = arr2nav ( $rows, ',', 'id' );
			// 第三级
			$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND a.parent_id IN (' . $knowledgeIds . ')' );
			$knowledgeIds = arr2nav ( $rows, ',', 'id' );
			// 第四级
			$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND a.parent_id IN (' . $knowledgeIds . ')' );
			$knowledgeIds = arr2nav ( $rows, ',', 'id' );
			
			// 第五级
			$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND a.parent_id IN (' . $knowledgeIds . ')' );
			$knowledgeIds1 = arr2nav ( $rows, ',', 'id' );
			
			// $row = $this->dao->getRow ( 'SELECT fn_vip_get_knowledge_child_list(\'' . $rootIds . '\') AS ids' );
			if ($knowledgeIds) {
				// $ids = $row ['ids'];
				// if ($ids != '$,') {
				$knowledgeIds = str_replace ( ',', "','", $knowledgeIds );
				return $this->dao->getAll ( 'SELECT a.`id`,
													a.`name`,
													a.`remark`,
													a.`sort`,
													a.`parent_id`,
													a.`analysis`,
													a.`status`,
													a.`state`,
													a.is_leaf
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND ((a.id IN(\'' . $knowledgeIds . '\') AND a.name LIKE \'%' . $kw . '%\') OR a.id IN (SELECT parent_id FROM ' . $this->vip_view_knowledge . ' WHERE a.id IN(\'' . $knowledgeIds . '\') AND name LIKE \'%' . $kw . '%\')) ORDER BY a.sort, a.id' );
				// }
				// return array ();
			}
			return array ();
		} else {
			return $this->dao->getAll ( 'SELECT a.`id`,
											a.`name`,
											a.`remark`,
											a.`sort`,
											a.`parent_id`,
											a.`analysis`,
											a.`status`,
											a.`state`,
											a.is_leaf,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.id and b.status = 1 and department = \'CLASS\' and b.is_classic = 1) end as knode_classic_question_num,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.id and b.status = 1 and department = \'CLASS\') end as knode_question_num
									FROM ' . $this->vip_view_knowledge . ' a
									WHERE a.status = 1 AND a.parent_id = ' . $parentId . ' ORDER BY a.sort, a.id' );
		}
	}
	
	public function getKnowledgeTypeIsGaosiById($knowledgeTypeId){
		return $this->dao->getOne('SELECT is_gaosi FROM '.$this->vip_dict_knowledge_type.' WHERE id = ' .$this->dao->quote($knowledgeTypeId));
	}
	
	
	public function getCourseTypesBySubjectId2($subjectId, $is_gaosi = 1) {
		$knowledgeTypeIdStr = '';
		$knowledgeTypeArr = $this->getKnowledgeTypes(array('subjectid'=>$subjectId,'is_gaosi'=>$is_gaosi));
		if(!empty($knowledgeTypeArr)){
			foreach ($knowledgeTypeArr as $key=>$knowledgeType){
				$knowledgeTypeIdStr .= $knowledgeType['id'].',';
			}
		}
		
		return $this->dao->getAll ( 'SELECT `id`,
										    `title`,
										    `status`,
										    `sort`
									FROM ' . $this->vip_dict_course_type . '
								 	WHERE status = 1 AND subject_id = ' . $this->dao->quote ( $subjectId ) . ' AND knowledge_type_id IN ('.$this->dao->quote($knowledgeTypeIdStr).') ORDER BY sort' );
	}
	
	//获取省
	public function getCitys()
	{
		return $this->dao->getAll("select id,city from ".$this->atf_citys." where oneid >0 and twoid=0 and threeid=0");
	}

	//通过city名称获取id
	public function getCityIdByNameOne($city)
	{
		return $this->dao->getOne("select id from ".$this->atf_citys." where city='".$city."'");
	}

    //通过city id获取名称
    public function getCityNameOne($cityId)
    {
        return $this->dao->getOne("select city from ".$this->atf_citys." where id='".$cityId."'");
    }

	//获取市
	public function getCountry($city)
	{
		$cityid=$this->getCityIdByNameOne($city);
		return $this->dao->getAll("select id,city from ".$this->atf_citys." where oneid=$cityid and id !=$cityid and threeid=0");
	}

	//通过省id获取市
	public function getCountryByCityId($cityid)
	{
		return $this->dao->getAll("select id , city from ".$this->atf_citys." where id <>".$cityid." and oneid=".$cityid." and threeid=0");
	}

	//获取考区
	public function getTest($condition)
	{
		return $this->dao->getAll("select id,test_name from ".$this->vip_test." where grade_id=".$condition['gradeid']." and city_id=".$condition['cityid']." and country_id=".$condition['countryid']);
	}

	//添加考区
	public function addTest($data)
	{
		if(empty($data))
		{
			return false;
		}
		return $this->dao->execute("insert into ".$this->vip_test."(city_id,country_id,grade_id,test_name,sort) values (".$this->dao->quote($data['city_id']).",".$this->dao->quote($data['country_id']).",".$this->dao->quote($data['grade_id']).",".$this->dao->quote($data['test_name']).",".$this->dao->quote($data['sort']).")");
	}

	//修改考区
	public function getTestByID($id)
	{
		return $this->dao->getRow ( 'SELECT id,grade_id,city_id,country_id,test_name ,sort FROM ' . $this->vip_test . ' WHERE id = ' . $this->dao->quote ( abs ( $id ) ) );
	}

	//更新考区
	public function updateTestSave($data)
	{
		if(!is_array($data)||empty($data))
		{
			return false;
		}
		return $this->dao->execute('update '.$this->vip_test.' set grade_id='.$data['grade_id'].', city_id='.$data['city_id'].', country_id='.$data['country_id'].',test_name="'.$data['test_name'].'",sort='.$data['sort'].' where id='.$data['id']);
	}


	//通过学部获取年级
	public function getGradeSubject($grade_id)
	{
		return $this->dao->getAll("select id,title,status,sort from ".$this->vip_grades_subject." where status=1 and grade_id=".$grade_id);
	}
	
	
	//获取下一个知识点id
	public function getNextKnowledgeId(){
		$lastId = $this->dao->getOne("select id from ".$this->vip_knowledge." where status>0 order by id DESC LIMIT 1");
		if(!$lastId){
			$lastId = 0;
		}
		return $lastId+1;
	}
	
	
	
	public function getFourlevelByID($id) {
		return $this->dao->getRow ( 'SELECT a.`id`,
											a.`name`,
											a.`remark`,
											a.`sort`,
											a.`parent_id`,
											a.`analysis`,
											a.`status`,
											a.`level`,
											a.`analysis3`,
											b.name as parent_name 
									FROM ' . $this->vip_fourlevel_system . ' a 
									LEFT JOIN ' . $this->vip_fourlevel_system . ' b ON a.parent_id = b.id 
									WHERE a.id = ' . $this->dao->quote ( $id ) );
	}
	
	
	//获取下一个四级体系id
	public function getNextFourlevelId(){
		$lastId = $this->dao->getOne("select id from ".$this->vip_fourlevel_system." where status>0 order by id DESC LIMIT 1");
		if(!$lastId){
			$lastId = 0;
		}
		return $lastId+1;
	}
	
	//添加四级知识体系
	public function addFourlevel($fourlevel = array()) {
		$parentId = $fourlevel ['parent_id'];
		$flag = true;
		$this->dao->execute ( 'begin' ); // 事务开启
		if (! empty ( $parentId )) {
			$sql = 'UPDATE ' . $this->vip_fourlevel_system . ' SET is_leaf = 0 WHERE id = ' . $this->dao->quote ( $parentId ) . ' AND is_leaf = 1';
			if ($this->dao->execute ( $sql ))
			$flag = true;
			else
			$flag = false;
		}

		if ($flag == true) {
			$sql2 = 'INSERT INTO ' . $this->vip_fourlevel_system . ' (name, remark, parent_id, analysis, analysis3, sort, is_leaf, level) VALUES (' . $this->dao->quote ( $fourlevel ['name'] ) . ', ' . $this->dao->quote ( $fourlevel ['remark'] ) . ', ' . $this->dao->quote ( $parentId ) . ', ' . $this->dao->quote ( $fourlevel ['analysis'] ) . ', ' . $this->dao->quote ( $fourlevel ['analysis3'] ) . ', ' . $this->dao->quote ( $fourlevel ['sort'] ) . ', 1, ' . $this->dao->quote ( $fourlevel ['level'] ) . ')';
			if ($this->dao->execute ( $sql2 )) {
				$id = $this->dao->lastInsertId ();
				$flag = true;
			} else
			$flag = false;
		}

		// 如为父节点则插入知识点属性表
		if (empty ( $parentId ) && $flag == true) {
			$sql3 = 'INSERT INTO ' . $this->vip_fourlevel_subject_rs . ' (fourlevel_id, subject_id) VALUES (' . $this->dao->quote ( $id ) . ', ' . $this->dao->quote ( $fourlevel ['subjectid'] ) . ')';

			if ($this->dao->execute ( $sql3 ))
			$flag = true;
			else
			$flag = false;
		}

		if ($flag === false)
		$this->dao->execute ( 'rollback' ); // 事务回滚
		else
		$this->dao->execute ( 'commit' ); // 事务提交

		return $flag;
	}
	
	
	public function getPathForFourlevel($id, $type = 'fourlevel') {
		$path = array ();
		if ($type == 'fourlevel') {
			$nav = $this->getFourlevelByID ( $id );
		}else {
			$nav = $this->getLabelByID ( $id );
		}
		$path [] = $nav;
		if ($nav ['parent_id'] > 0) {
			$path = array_merge ( $this->getPathForFourlevel ( $nav ['parent_id'] ), $path );
		}

		return $path;
	}
	
	
	
	public function getFourlevesByParentId($parentId, $subjectId) {
		$where = '';
		if (empty ( $parentId )) {
			$where = ' AND a.parent_id = 0';
			if (! empty ( $subjectId )) {
				$where .= ' AND a.subject_id = ' . $this->dao->quote ( $subjectId );
			} else {
				return array ();
			}
		} else {
			$where = ' AND a.parent_id = ' . $this->dao->quote ( $parentId );
		}
		
		return $this->dao->getAll ( 'SELECT a.`id`,
											a.`name`,
											a.`remark`,
											a.`sort`,
											a.`parent_id`,
											a.`analysis`,
											a.`status`,
											a.`state`,
											a.`is_leaf`,
											a.`origin_knowledge_id`,
											a.`level`,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.origin_knowledge_id and b.status = 1 and department = \'CLASS\' and b.is_classic = 1) end as knode_classic_question_num,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.origin_knowledge_id and b.status = 1 and department = \'CLASS\') end as knode_question_num 
									FROM ' . $this->vip_view_fourlevel . ' a
									WHERE a.status = 1 ' . $where . ' ORDER BY a.sort, a.id' );
	}
	
	
	public function getFourlevelsByParentIdChild($parentId, $subejctId) {
		$where = '';
		if (empty ( $parentId )) {
			$where = ' AND a.parent_id = 0';
			if (! empty ( $subejctId )) {
				$where .= ' AND a.subject_id = ' . $this->dao->quote ( $subejctId );
			} else {
				return array ();
			}
		} else {
			$where = ' AND a.parent_id = ' . $this->dao->quote ( $parentId );
		}
		
		return $this->dao->getAll ( 'SELECT a.`id`,
											a.`name` as text,
											a.`status`,
											a.`state`,
											a.`is_leaf`
									FROM ' . $this->vip_view_fourlevel . ' a
									WHERE a.status = 1 ' . $where . ' ORDER BY a.sort, a.id' );
	}
	
	
	public function updateFourlevel($fourlevel = array()) {
		$fourlevelId = $fourlevel ['id'];
		$parentId = $fourlevel ['parent_id'];
		$shParentInfo = $this->dao->getRow('select name,level from '.$this->vip_fourlevel_system.' where id ='.$fourlevel['parent_name'].' and status = 1 ');
		$before_parentName = $this->dao->getOne ( 'SELECT `name` FROM ' . $this->vip_fourlevel_system . ' WHERE id = ' . $this->dao->quote ( $parentId ) );
		if($before_parentName != $fourlevel ['parent_name']){
			$parentId = $fourlevel ['parent_name'];
		}
		$row = $this->dao->getRow ( 'SELECT fn_vip_get_fourlevel_child_list(' . $this->dao->quote ( $fourlevelId ) . ') AS sub_fourlevel_ids' );
		
		if ($row) {
			if (in_array ( $parentId, str2arr ( $row ['sub_fourlevel_ids'], ',' ) )) {
				return false;
			}
		}
		
		if (! empty ( $fourlevelId )) {
			$before_parentId = $this->dao->getRow ( 'SELECT `id`,`parent_id` FROM ' . $this->vip_fourlevel_system . ' WHERE id = ' . $this->dao->quote ( $fourlevelId ) ); // 查找修改前的父目录的ID
			if (! empty ( $before_parentId ['parent_id'] )) {
				if ($before_parentId ['parent_id'] != $parentId) {
					$row = $this->dao->getRow ( 'SELECT `id`,`parent_id` FROM ' . $this->vip_fourlevel_system . ' WHERE id != ' . $this->dao->quote ( $fourlevelId ) . ' AND  parent_id = ' . $this->dao->quote ( $before_parentId ['parent_id'] ) );
					if (empty ( $row )) { // 查找对应的父节点除了此节点之外还有没有子节点，如果无则修改叶子节点
						$sql = 'UPDATE ' . $this->vip_fourlevel_system . ' SET is_leaf = 1 WHERE id = ' . $this->dao->quote ( $before_parentId ['parent_id'] );
						$this->dao->execute ( $sql );
					}
					if(!empty($parentId)){
						$parentLevel = $this->dao->getOne('SELECT level FROM '.$this->vip_fourlevel_system.' WHERE id = '.$this->dao->quote($parentId));
						$fourlevel['level'] = $parentLevel+1;
					}else{
						$fourlevel['level'] = 1;
					}
					
				}
			}
		}

		if (! empty ( $parentId )) {
			//$sql = 'UPDATE ' . $this->vip_fourlevel_system . ' SET is_leaf = 0 WHERE id = ' . $this->dao->quote ( $parentId ) . ' AND is_leaf = 1';
			$sql = 'UPDATE ' . $this->vip_fourlevel_system . ' SET is_leaf = 0 WHERE id = ' . $this->dao->quote ( $parentId ) ;
			$this->dao->execute ( $sql );
		}

		return $this->dao->execute ( 'UPDATE ' . $this->vip_fourlevel_system . ' SET name = ' . $this->dao->quote ( $fourlevel ['name'] ) . ', remark = ' . $this->dao->quote ( $fourlevel ['remark'] ) . ', sort = ' . $this->dao->quote ( $fourlevel ['sort'] ) . ', parent_id = ' . $this->dao->quote ( $parentId ) . ', analysis = ' . $this->dao->quote ( $fourlevel ['analysis'] ) . ',analysis3 = ' . $this->dao->quote ( $fourlevel ['analysis3'] ) . ',level='.$this->dao->quote ( $shParentInfo['level']+1 ).' WHERE id = ' . $this->dao->quote ( $fourlevel ['id'] ) );
	}
	
	
	
	public function getFourlevelsByParentId2($parentId, $subjectId) {
		$where = '';
		if (empty ( $parentId )) {
			if (! empty ( $subjectId )) {
				// 第一级
				$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_view_fourlevel . ' a
									WHERE a.status = 1 AND a.parent_id = 0 AND a.subject_id = ' . $this->dao->quote ( $subjectId ) );
				$fourlevelIds = arr2nav ( $rows, ',', 'id' );
				
				// 第二级
				if(!empty($fourlevelIds)){
					$rows = $this->dao->getAll ( 'SELECT a.`id`
									FROM ' . $this->vip_fourlevel_system . ' a
									WHERE a.status = 1 AND a.parent_id IN (' . $fourlevelIds . ') ' );
					$fourlevelIds = arr2nav ( $rows, ',', 'id' );
					// 第三级
					if(!empty($fourlevelIds)){
						$rows = $this->dao->getAll ( 'SELECT a.`id`
										FROM ' . $this->vip_view_fourlevel . ' a
										WHERE a.status = 1 AND a.parent_id IN (' . $fourlevelIds . ') ' );
						$fourlevelIds = arr2nav ( $rows, ',', 'id' );
						//第四级
						if(!empty($fourlevelIds)){
							$where .= ' AND a.parent_id IN (' . $fourlevelIds . ') ';
						}else{
							return array ();
						}

					}else{
						return array ();
					}
					
				}else{
					return array ();
				}
				
			} else {
				return array ();
			}
		} else {
			$where = ' AND a.parent_id = ' . $this->dao->quote ( $parentId );
		}
		
		return $this->dao->getAll ( 'SELECT a.`id`,
											a.`name`,
											a.`remark`,
											a.`sort`,
											a.`parent_id`,
											a.`analysis`,
											a.`status`,
											a.`state`,
											a.is_leaf,
											a.level,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.origin_knowledge_id and b.status = 1 and department = \'CLASS\' and b.is_classic = 1) end as knode_classic_question_num,
											case is_leaf when 0 then \'\' else (select count(*) from vip_question b where b.knowledge_id = a.origin_knowledge_id and b.status = 1 and department = \'CLASS\') end as knode_question_num 
									FROM ' . $this->vip_view_fourlevel . ' a
									WHERE a.status = 1 ' . $where . ' ORDER BY a.sort, a.id' );
	}
	
	
	public function getKnowledgeTypeBySubjectIdAndIsGaosi($params){
		if($params['is_gaosi'] == 1){
			$where = ' AND is_gaosi = '.$this->dao->quote($params['is_gaosi']);
		}
		return $this->dao->getRow ( 'SELECT id, title, sort FROM '.$this->vip_dict_knowledge_type.' WHERE status = 1 AND subject_id =  ('.$params['subjectid'].') '.$where.' ORDER BY sort ASC' );
	}

	
	public function checkFourlevelIsExist($fourlevel = array(),$pid){
		$count = $this->dao->getOne('SELECT COUNT(1) FROM '.$this->vip_fourlevel_system.' WHERE status = 1 and name = '. $this->dao->quote ( $fourlevel ['name'] ) .' AND parent_id = '. $this->dao->quote ( $pid ) );
		if($count == 0)
			return false;
		else 
			return true;
	}
	
	
	public function matchFourlevel($fourlevel = array()) {
		$parentId = $fourlevel ['pid'];
		$flag = true;
		$this->dao->execute ( 'begin' ); // 事务开启
		if (! empty ( $parentId )) {
			$sql = 'UPDATE ' . $this->vip_fourlevel_system . ' SET is_leaf = 0 WHERE id = ' . $this->dao->quote ( $parentId ) . ' AND is_leaf = 1';
			if ($this->dao->execute ( $sql ))
			$flag = true;
			else
			$flag = false;
		}
			
		if ($flag == true) {
			$knowledgeInfo = $this->getKnowledgeByID($fourlevel['origin_knowledge_id']);
			$sql2 = 'INSERT INTO ' . $this->vip_fourlevel_system . ' (name, remark, parent_id, analysis,analysis3, sort, is_leaf, level, origin_knowledge_id) VALUES (' . $this->dao->quote ( $knowledgeInfo ['name'] ) . ', ' . $this->dao->quote ( $knowledgeInfo ['remark'] ) . ', ' . $this->dao->quote ( $parentId ) . ', ' . $this->dao->quote ( $knowledgeInfo ['analysis'] ) . ', ' . $this->dao->quote ( $knowledgeInfo ['analysis3'] ) . ', ' . $this->dao->quote ( $knowledgeInfo ['sort'] ) . ', 1, ' . $this->dao->quote ( $fourlevel ['level'] ) . ', ' . $this->dao->quote ( $fourlevel ['origin_knowledge_id'] ) . ')';
			if ($this->dao->execute ( $sql2 )) {
				$id = $this->dao->lastInsertId ();
				$flag = true;
			} else
			$flag = false;
		}

		// 如为父节点则插入知识点属性表
		/*if (empty ( $parentId ) && $flag == true) {
			$sql3 = 'INSERT INTO ' . $this->vip_knowledge_course_type_rs . ' (knowledge_id, course_type_id) VALUES (' . $this->dao->quote ( $id ) . ', ' . $this->dao->quote ( $knowledge ['coursetypeid'] ) . ')';

			if ($this->dao->execute ( $sql3 ))
			$flag = true;
			else
			$flag = false;
		}*/

		if ($flag === false)
		$this->dao->execute ( 'rollback' ); // 事务回滚
		else
		$this->dao->execute ( 'commit' ); // 事务提交

		return $flag;
	}
	
	
	
	public function getOriginKnowledgeIdByFourlevelId($fourlevelId){
		$originKnowledgeId = 0;
		if(!empty($fourlevelId)){
			$originKnowledgeId = $this->dao->getOne('SELECT origin_knowledge_id FROM '.$this->vip_fourlevel_system.' WHERE id = '.$this->dao->quote ($fourlevelId));
		}
		return $originKnowledgeId;
	}
	
	
	/*add by xcp 20161020*/
	public function getQuestionsByCondition($questionTypeTitle,$subjectTitle,$limit){
		$list = array();
		if(!empty($questionTypeTitle) && !empty($subjectTitle) && !empty($limit)){
			$list = $this->dao->getAll('SELECT q.id,q.uid,q.content,q.analysis FROM '.$this->vip_question.' q LEFT JOIN '.$this->vip_dict_question_type.' qt ON qt.id = q.question_type_id LEFT JOIN '.$this->vip_dict.' d ON qt.question_type_code = d.code LEFT JOIN '.$this->vip_dict_subject.' s ON s.id = qt.subject_id   WHERE s.title = '.$this->dao->quote($subjectTitle).' AND d.title = '.$this->dao->quote($questionTypeTitle).' ORDER BY RAND() LIMIT '.$limit);
			if(!empty($list)){
				$questionIds = array ();
				$questionCount=count ( $list );
				for($i = 0, $n = $questionCount; $i < $n; $i ++) {
					$quesKnowledgeId = $list [$i]['knowledge_id'];
					if(isset($specialMapping[$quesKnowledgeId])) {
						//对于选择了教材版本的试题, 更新4级知识点信息
						$list [$i] ['knowledge_level_4_id'] = $specialMapping[$quesKnowledgeId];
					}
					$questionIds [] = $list [$i] ['id'];
				}
				$options = $this->getOptionsByQuestionIds ( implode (',', $questionIds ) );
				$answers = $this->getAnswersByQuestionIds ( implode (',', $questionIds ) );
				
				for($i = 0, $n = $questionCount; $i < $n; $i ++) {
					$questionOptions = array ();
					$questionAnswers = array ();
					
					foreach ( $options as $option ) {
						if ($option ['question_id'] == $list [$i] ['id']) {
							$option ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $option ['content'] );
							$questionOptions [] = $option;
						}
					}
					foreach ( $answers as $answer ) {
						if ($answer ['question_id'] == $list [$i] ['id']) {
							$answer ['content'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $answer ['content'] );
							$questionAnswers [] = $answer;
						}
					}
					$list [$i] ['content'] = preg_replace ( '/(top:.*pt).*/iU', '', $list [$i] ['content'] );
					$list [$i] ['analysis'] = preg_replace ( '/(top:[ ]*.*pt).*/iU', '', $list [$i] ['analysis'] );
					$list [$i] ['options'] = $questionOptions;
					$list [$i] ['answers'] = $questionAnswers;
	
				}
			}
		}
		return $list;
	}
	
	
	public function deleteFourlevel($arr){
		if($arr['id']){
			$fourlevelInfo = $this->getFourlevelByID($arr['id']);		
			if($this->dao->execute('UPDATE '.$this->vip_fourlevel_system.' set status = -1 WHERE id = '.$this->dao->quote($arr['id']))){
				$childs = $this->getChildFourLevel($arr['id']);
				$childIdStr = '';
				if(!empty($childs)){
					$childIdArr = array();
					foreach ($childs as $key=>$row){
						$childIdArr[] = $row['id'];
					}
					$childIdStr = implode(',',$childIdArr);
				}
				if($childIdStr!=''){
					$this->dao->execute('UPDATE '.$this->vip_fourlevel_system.' set status = -1 WHERE id IN  ('.$this->dao->quote($arr['id']).')');
				}
				$parentChilds =  $this->getChildFourLevel($fourlevelInfo['parent_id']);
				if(empty($parentChilds)){
					$this->dao->execute('UPDATE '.$this->vip_fourlevel_system.' SET is_leaf = 1 WHERE id = '.$this->dao->quote($fourlevelInfo['parent_id']));
				}
				return true;
			}
			return false;
		}
		return false;
	}
	
	
	public function getChildFourLevel($id){
		return $this->dao->getAll('SELECT * FROM '.$this->vip_fourlevel_system.' WHERE status = 1 and parent_id = '.$this->dao->quote($id));
	}
	


	//通过年部 学科 获取年级
	public function getGradeSubjects($grade_id,$subject_id)
	{
		$sql="select id,title,status,sort from ".$this->vip_grades_subject." where status=1 and grade_id=".$grade_id;
		if($grade_id == 7)
		{
			$sql.=" and subject_id=".$subject_id;
		}
		return $this->dao->getAll($sql);
	}
	
	/******补全数据*****/
	// public function getAlleses()
	// {
	// 	return $this->dao->getAll("select * from vip_paper where province IS NULL and name = '' ");
	// } 

	// public function updateQuestions($data)
	// {
	// 	return $this->dao->execute("update vip_paper set source='期中考试' where source='期中考试真题'");
	// }
	
	
}

?>
