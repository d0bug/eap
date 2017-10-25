<?php
/* 基础管理 */
class BasicAction extends QuestionCommAction {
	public function index() {
		$this->display ();
	}
	public function knowledge() {
		$this->display ();
	}
	public function dict() {
		$this->display ();
	}
	public function dict_add() {
		$params = $this->_get ();
		$cate = $params ['cate'];

		$this->assign ( 'cate', $cate );
		$this->display ();
	}
	public function add() {
		$params = $this->_get ();
		$cate = $params ['cate'];
		$gradeId = $params ['gid'];
		$subjectId = $params ['sid'];
		$knowledgeTypeId = $params ['ktid'];

		$this->assign ( get_defined_vars () );
		$this->display ();
	}
	public function dict_add_save() {
		$params = $this->_post ();

		$rs = D ( 'Basic' )->addDict ( $params );
		if ($rs) {
			$this->success ();
		}
		$this->error ();
	}
	public function add_save() {
		$params = $this->_post ();
		$rs = D ( 'Basic' )->add ( $params );
		if ($rs) {
			$this->success ();
		}
		$this->error ();
	}
	public function dict_edit() {
		$params = $this->_get ();
		$id = $params ['id'];

		$dict = D ( 'Basic' )->getDictByID ( $id );

		$this->assign ( get_defined_vars () );
		$this->display ();
	}
	public function dict_edit_save() {
		$params = $this->_post ();

		$rs = D ( 'Basic' )->updateDict ( $params );
		if ($rs) {
			$this->success ();
		}
		$this->error ();
	}
	public function edit() {
		$params = $this->_get ();
		$id = $params ['id'];
		$cate = $params ['cate'];
		$dict = D ( 'Basic' )->getDictDataByID ( $cate, $id );

		$this->assign ( get_defined_vars () );
		$this->display ();
	}
	public function edit_save() {
		$params = $this->_post ();

		$rs = D ( 'Basic' )->update ( $params );
		if ($rs) {
			$this->success ();
		}
		$this->error ();
	}
	public function dict_delete() {
		$params = $this->_post ();
		$id = $params ['id'];

		$rs = D ( 'Basic' )->deleteDictByID ( $id );
		if ($rs) {
			$this->success ();
		}
		$this->error ();
	}
	public function check_code() {
		ob_clean ();
		$post = $this->_post ();
		$params = $this->_get ();
		$code = $post ['code'];
		$category = $params ['cate'];
		$row = D ( 'Basic' )->getDictByCategoryAndCode ( $category, $code );
		if ($row) {
			echo 'false';
		} else {
			echo 'true';
		}
	}
	public function knowledge_add() {
		$params = $this->_get ();
		$parentId = !empty($params ['pid'])?$params ['pid']:0;
		$courseTypeId = $params ['coursetypeid'];
		$knowledgeTypeId = $params ['knowledgetypeid'];
		$level = $params ['level'];
		$path = '/';
		if (! empty ( $parentId )) {
			$path = arr2nav ( D ( 'Basic' )->getPath ( $parentId, 'knowledge' ) );
			$knowledge = D ( 'Basic' )->getKnowledgeByID ( $parentId );
			if ($knowledge) {
				$name = $knowledge ['name'];
				$this->assign ( 'name', $name );
			}
		}
		$nextKnowledgeId = D ( 'Basic' )->getNextKnowledgeId();
		$this->assign ( 'path', $path );
		$this->assign ( 'coursetypeid', $courseTypeId );
		$this->assign ( 'knowledgetypeid', $knowledgeTypeId );
		$this->assign ( 'level', $level );
		$this->assign ( 'parentId', $parentId );
		$this->assign ( 'nextKnowledgeId', $nextKnowledgeId );
		$this->display ();
	}
	public function knowledge_add_save() {
		$params = $this->_post ();
		$rs = D ( 'Basic' )->addKnowledge ( $params );
		if ($rs) {
			$this->success ();
		}
		$this->error ();
	}
	public function knowledge_delete() {
		$params = $this->_post ();
		$uid = $params ['uid'];

		$rs = D ( 'Basic' )->deleteKnowledgeByUID ( $uid );
		if ($rs) {
			$this->success ();
		}
		$this->error ();
	}
	public function knowledge_edit() {
		$params = $this->_get ();
		$id = $params ['id'];
		$courseTypeId = $params ['coursetypeid'];

		$knowledge = D ( 'Basic' )->getKnowledgeByID ( $id );
		$now = date('Ymd');
		$this->assign ( 'coursetypeid', $courseTypeId );
		$this->assign ( 'knowledge', $knowledge );
		$this->display ();
	}
	public function knowledge_edit_save() {
		$params = $this->_post ();

		$rs = D ( 'Basic' )->updateKnowledge ( $params );
		if ($rs) {
			$this->success ();
		}
		$this->error ();
	}
	public function getKnowledgeByIDs() {
		$params = $this->_post ();
		$ids = $params ['ids'];

		$knowledge = D ( 'Basic' )->getKnowledgeByIDs ( $ids );
		$this->outPut ( $knowledge );
	}
	public function getKnowledges() {
		$params = $this->_param ();

		$parentId = $params ['id'];
		$courseTypeId = $params ['coursetypeid'];

		$knowledges = D ( 'Basic' )->getKnowledgesByParentId ( $parentId, $courseTypeId );
		// print_r($knowledges);
		$this->outPut ( $knowledges );
	}
	public function getKnowledges1() {
		$params = $this->_param ();
		$parentId = $params ['id'];
		$courseTypeId = $params ['coursetypeid'];

		$knowledges = D ( 'Basic' )->getKnowledgesByParentId1 ( $parentId, $courseTypeId );
		// print_r($knowledges);
		$this->outPut ( $knowledges );
	}
	public function getKnowledges2() {
		$params = $this->_param ();

		$parentId = $params ['id'];
		$courseTypeId = $params ['coursetypeid'];
		$knowledgeTypeId = !empty($params ['knowledgetypeid'])?$params ['knowledgetypeid']:1;
		$knowledges = D ( 'Basic' )->getKnowledgesByParentId2 ( $parentId, $courseTypeId, $knowledgeTypeId );
		// print_r($knowledges);
		$this->outPut ( $knowledges );
	}
	public function getKnowledgesSearch() {
		$params = $this->_param ();

		$parentId = $params ['id'];
		$courseTypeId = $params ['coursetypeid'];
		$kw = $params ['kw'];

		$knowledges = D ( 'Basic' )->getKnowledgesByWhere ( $parentId, $courseTypeId, $kw );

		$this->outPut ( $knowledges );
	}
	public function getKnowledgesChilds() {
		$params = $this->_param ();
		$parentId = $params ['id'];
		$courseTypeId = $params ['coursetypeid'];
		$knowledges = D ( 'Basic' )->getKnowledgesByParentIdChild ( $parentId, $courseTypeId );
		$this->outPut ( $knowledges );
	}
	public function getKnowledgesChilds1() {
		$params = $this->_param ();
		$parentId = $params ['id'];
		$courseTypeId = $params ['coursetypeid'];
		$knowledges = D ( 'Basic' )->getKnowledgesByParentIdChild1 ( $parentId, $courseTypeId );
		$this->outPut ( $knowledges );
	}
	public function getComboTreeKnowledges() {
		$params = $this->_param ();
		$courseTypeId = $params ['coursetypeid'];

		$knowledges = D ( 'Basic' )->getKnowledgesByCourseTypeId ( $courseTypeId );

		$bca = new BuildEasyUIComboBoxArray ( $knowledges, 'id', 'parent_id', 0 );
		$this->outPut ( $bca->getTreeArray () );
	}
	public function question_type_add() {
		$params = $this->_get ();
		$subjectId = $params ['sid'];

		$this->assign ( 'subjectid', $subjectId );
		$this->display ();
	}
	public function getQuestionTypesFullBySubjectId() {
		$params = $this->_get ();
		$subjectId = $params ['subjectid'];

		$this->output ( D ( 'Basic' )->getQuestionTypesFullBySubjectId ( $subjectId ) );
	}
	public function question_type_add_save() {
		$params = $this->_post ();

		$rs = D ( 'Basic' )->addQuestionType ( $params );
		if ($rs) {
			$this->success ();
		}
		$this->error ();
	}
	public function question_type_edit() {
		$params = $this->_get ();
		$id = $params ['id'];

		$questionType = D ( 'Basic' )->getQuestionTypeByID ( $id );

		$this->assign ( 'questiontype', $questionType );
		$this->display ();
	}
	public function question_type_edit_save() {
		$params = $this->_post ();

		$rs = D ( 'Basic' )->updateQuestionType ( $params );
		if ($rs) {
			$this->success ();
		}
		$this->error ();
	}
	public function question_type_delete() {
		$params = $this->_post ();
		$id = $params ['id'];

		$rs = D ( 'Basic' )->deleteQuestionTypeByID ( $id );
		if ($rs) {
			$this->success ();
		}
		$this->error ();
	}
	public function getQuestionTypes() {
		$params = $this->_param ();

		$gradedept = $params ['gradedept'];
		$subject = $params ['subject'];

		$questionTypes = D ( 'Basic' )->getQuestionTypes ( $gradedept, $subject );
		if ($questionTypes) {
			$questionTypes [0] ['selected'] = true;
		}
		$this->outPut ( $questionTypes );
	}
	public function getDictTypes() {
	   
       
		$this->outPut ( $this->dictTypes );
	}
	public function getDictsByCategory() {
		$params = $this->_param ();

		$currentPage = $params ['page'];
		$pageSize = $params ['rows'];
		$sort = $params ['sort'];
		$order = $params ['order'];
		$category = $params ['cate'];

		$this->outPut ( D ( 'Basic' )->getDictsByCategory ( $category, $currentPage, $pageSize, $sort, $order ) );
	}
	public function getDictsAllByCategory() {
		$params = $this->_get ();
		if ($params ['limit_role']) {
			$limitType = ($params ['limit_role'] == 1) ? 0 : 1;
			$params = $this->get_roleCondition ( $params, $limitType );
		}
		$cate = $params ['cate'];
		$data = D ( 'Basic' )->getDictsAllByCategory ( $params );
		array_unshift ( $data, $this->getDropdownDefault ( $cate ) );

		$this->outPut ( $data );
	}
	public function getGradesByGroup() {
		$groups = array (
		array (
		'codes' => array (
		'GR1000',
		'GR1001'
		),
		'name' => '小学'
		)
		);
		$grades = D ( 'Basic' )->getDictsAllByCategory ( array (
		'cate' => 'GRADE'
		) );

		$this->outPut ( $grades );
	}
	public function getGrades() {
		$this->outPut ( D ( 'Basic' )->getGrades () );
	}
	public function getGradesFull() {
		$data = D ( 'Basic' )->getGrades ();

		array_unshift ( $data, array (
		'id' => '',
		'title' => '请选择年部...'
		) );

		$this->outPut ( $data );
	}
	public function getSubjectsByGradeId() {
		$params = $this->_post ();
		$gradeId = $params ['gradeid'];

		$this->outPut ( D ( 'Basic' )->getSubjectsByGradeId ( $gradeId ) );
	}
	public function getSubjectsFullByGradeId() {
		$params = $this->_get ();
		$gradeId = $params ['gradeid'];

		$data = array ();
		if (! empty ( $gradeId )) {
			$data = D ( 'Basic' )->getSubjectsByGradeId ( $gradeId );
		}
		array_unshift ( $data, array (
		'id' => '',
		'title' => '请选择学科...'
		) );

		$this->outPut ( $data );
	}
	public function getCourseTypesBySubjectId() {
		$params = $this->_post ();
		$subjectId = $params ['subjectid'];

		$this->outPut ( D ( 'Basic' )->getCourseTypesBySubjectId ( $subjectId ) );
	}
	public function getQuestionTypesBySubjectId() {
		$params = $this->_param ();
		$subjectId = $params ['subjectid'];

		$this->outPut ( D ( 'Basic' )->getQuestionTypesBySubjectId ( $subjectId ) );
	}
	public function knowledges() {
		$params = $this->_get ();
		$courseTypeId = $params ['coursetypeid'];

		$this->assign ( 'coursetypeid', $courseTypeId );
		$this->display ();
	}
	public function knowledges1() {
		$params = $this->_get ();
		$courseTypeId = $params ['coursetypeid'];

		$this->assign ( 'coursetypeid', $courseTypeId );
		$this->display ();
	}
	public function knowledges2() {
		$params = $this->_get ();
		$courseTypeId = $params ['coursetypeid'];

		$this->assign ( 'coursetypeid', $courseTypeId );
		$this->display ();
	}
	public function question_type() {
		$basicModel = D ( 'Basic' );
		$gradeDeptList = $basicModel->getComboboxData ( 'GRADE_DEPT' );
		$this->display ();
	}
	public function getPath() {
		$params = $this->_post ();
		$id = $params [id];
		$path = '/';
		if (! empty ( $id )) {
			$path = arr2nav ( D ( 'Basic' )->getPath ( $id ) );
		}

		$this->outPut ( $path );
	}

	/**
	 * 获取学科、课程类型、题型添加页面的可装载组合框数据
	 */
	public function getComboboxData() {
		$params = $this->_get ();
		$cate = $params ['cate'];
		$gradeId = $params ['grade_id'];
		$subjectId = $params ['subject_id'];

		$this->outPut ( D ( 'Basic' )->getComboboxData ( $cate, $gradeId, $subjectId ) );
	}
	/**
	 * 获取题型
	 */
	public function getQuestionTypeByID() {
		$params = $this->_post ();
		$id = $params ['id'];
		$this->outPut ( D ( 'Basic' )->getQuestionTypeByID ( $id ) );
	}

	// 获取难度
	public function getDifficulties() {
		$this->outPut ( array (
		array (
		'id' => '',
		'text' => '请选择...'
		),
		array (
		'id' => 1,
		'text' => '容易'
		),
		array (
		'id' => 2,
		'text' => '中等'
		),
		array (
		'id' => 3,
		'text' => '困难'
		)
		) );
	}
	// 获取难度
	public function getErrorTypes() {
		$this->outPut ( array (
		array (
		'id' => 1,
		'text' => '图片不显示'
		),
		array (
		'id' => 2,
		'text' => '图片其他问题'
		),
		array (
		'id' => 3,
		'text' => '题干缺失'
		),
		array (
		'id' => 4,
		'text' => '选项缺失'
		),
		array (
		'id' => 5,
		'text' => '编辑完成后仍为图片'
		)
		) );
	}
	public function view_knowledge_questions() {
		$params = $this->_get ();
		$knowledgeId = $params ['knowledge_id'];

		$questions = D ( 'Basic' )->getQuestionsByKnowledgeId ( $knowledgeId );
		$this->assign ( 'questions', $questions );
		$this->display ();
	}


	/*edit by xcp===================start==========================================================*/

	public function getKnowledgeTypes(){
		$params = $this->_post ();

		$this->outPut ( D ( 'Basic' )->getKnowledgeTypes ($params ) );
	}


	public function getCourseTypesBySubjectIdAndKnowledgeTypeId() {
		$params = $this->_post ();
		$subjectId = $params ['subjectid'];
		$knowledgeTypeId = $params ['knowledgeTypeId'];
		$this->outPut ( D ( 'Basic' )->getCourseTypesBySubjectIdAndKnowledgeTypeId ( $subjectId , $knowledgeTypeId ) );
	}
	


	//非北京版匹配五级知识点
	public function knowledge_match(){
		$params = $this->_get ();
		$parentId = $params ['pid'];
		$courseTypeId = $params ['coursetypeid'];
		$knowledgeTypeId = $params ['knowledgetypeid'];
		$level = $params ['level'];
		$path = '/';
		if (! empty ( $parentId )) {
			$path = arr2nav ( D ( 'Basic' )->getPath ( $parentId, 'knowledge' ) );
			$knowledge = D ( 'Basic' )->getKnowledgeByID ( $parentId );
			if ($knowledge) {
				$name = $knowledge ['name'];
				$this->assign ( 'name', $name );
			}
		}
		$nextKnowledgeId = D ( 'Basic' )->getNextKnowledgeId();
		$this->assign ( 'path', $path );
		$this->assign ( 'coursetypeid', $courseTypeId );
		$this->assign ( 'knowledgetypeid', $knowledgeTypeId );
		$this->assign ( 'level', $level );
		$this->assign ( 'parentId', $parentId );
		$this->assign ( 'nextKnowledgeId', $nextKnowledgeId );
		$this->display ();
	}


	public function knowledge_match_save() {
		$params = $this->_post ();
		$params['analysis'] = $params['content'];
		$basicModel = D ( 'Basic' );
		$isExist = $basicModel->checkKnowledgeIsExist( $params );
		if($isExist == true){
			$this->outPut ( array (
				'status' => false,
				'message' => '该知识点已存在'  
			) );
		}else{
			$rs = $basicModel->matchKnowledge ( $params );
			if ($rs) {
				$this->success ();
			}
			$this->error ();
		}

	}


	public function knowledges3() {
		$params = $this->_get ();
		$basicModel = D ( 'Basic' );
		$courseTypeId = $params ['coursetypeid'];
		$is_gaosi = $params ['is_gaosi'];
		$subjectId = $basicModel->getSubjectIdByCourseTypeId ( $courseTypeId );

		$this->assign ( 'basecoursetypes', $baseCourseTypes );
		$this->assign ( 'subjectid', $subjectId );
		$this->display ();
	}

	
	public function getCourseTypesBySubjectId2() {
		$params = $this->_post ();
		$subjectId = $params ['subjectid'];
		$is_gaosi = $params ['is_gaosi'];
		$this->outPut ( D ( 'Basic' )->getCourseTypesBySubjectId2 ( $subjectId , $is_gaosi ) );
	}

	public function getKnowledgesSearch3() {
		$params = $this->_param ();

		$parentId = $params ['id'];
		$courseTypeId = $params ['coursetypeid'];
		$kw = urldecode($params ['kw']);
		
		$knowledges = D ( 'Basic' )->getKnowledgesByWhere3 ( $parentId, $courseTypeId, $kw );

		$this->outPut ( $knowledges );
	}
	//获取所有省
	public function getByCity()
	{
		$data = D('Basic')->getCitys();

		array_unshift ( $data, array (
			'id' => '',
			'city' => '请选择省份...'
		) );

		$this->outPut ( $data );


		//$this->output(D('Basic')->getCitys());
	}
	//通过省id获取市
	public function getCountryByCityId()
	{
		$params = $this->_param ();
		$cityid=$params['cityid'];
        if($cityid != '') {
            $data = D('Basic')->getCountryByCityId($cityid);
            array_unshift($data, array(
                'id' => '',
                'city' => '请选择地区...'
            ));
            $this->outPut($data);
            //$this->output(D('Basic')->getCountryByCityId($cityid));
        }
	}

	//get方式获取市
	public function getCountryNameByCityId()
	{
		$params = $this->_get ();
		$cityid=$params['cityid'];
		$this->output(D('Basic')->getCountryByCityId($cityid));
	}
	//通过省获取市
	public function getCountryIdByName()
	{
		$params = $this->_get ();
		$city=$params['city'];
		$this->output(D('Basic')->getCountry($city));
	}

	public function getByCountry()
	{
		$params = $this->_param();
		//市
		$this->output(D('Basic')->getCountry($params['city']));
	}

	//考区
	public function test()
	{
		$this->display ();
	}

	//通过条件获取考区
	public function getTestByParam()
	{
		$params=$this->_param();
		$this->output(D('Basic')->getTest($params));
	}

	//通过条件获取考区
	public function getTestByparams()
	{
		$params=$this->_get();
		//把省和市转成id
		$city=D('Basic')->getCityIdByNameOne($params['city_id']);
		$country=D('Basic')->getCityIdByNameOne($params['country_id']);
		$data=array('gradeid'=>$params['gradeid']);
		if($city)
		{
			$data['cityid']=$city;
		}else
		{
			return false;
		}
		if($country)
		{
			$data['countryid']=$country;
		}else
		{
			return false;
		}
		$this->output(D('Basic')->getTest($data));
	}

	//添加考区
	public function test_add()
	{
		$params = $this->_get ();
		$gradeId = $params ['grade_id'];
		$cityId = $params ['city_id'];
		$countryId = $params ['country_id'];
		$this->assign ( get_defined_vars () );
		$this->display ();
	}

	//添加考区
	public function test_add_save() {
		$params = $this->_post ();
		$rs = D ( 'Basic' )->addTest ( $params );
		if ($rs) {
			$this->success ();
		}
		$this->error ();
	}

	//修改考区
	public function test_edit()
	{
		$params = $this->_get ();
		$id = $params ['id'];
		$dict = D ( 'Basic' )->getTestByID ($id );

		$this->assign ( get_defined_vars () );
		$this->display ();
	}

	//保存修改
	public function test_edit_save()
	{
		$params = $this->_post ();

		$rs = D ( 'Basic' )->updateTestSave ( $params );
		if ($rs) {
			$this->success ();
		}
		$this->error ();
	}
	
	
	//四级体系-add by xcp 20161014
	public function fourlevel_system(){
		$this->assign ( get_defined_vars () );
		$this->display ();
	}
	
	
	//添加四级体系
	public function fourlevel_add() {
		$params = $this->_get ();
		$parentId = !empty($params ['pid'])?$params ['pid']:0;
		$subjectId = $params ['subjectid'];
		$level = $params ['level'];
		$path = '/';
		if (! empty ( $parentId )) {
			$path = arr2nav ( D ( 'Basic' )->getPathForFourlevel ( $parentId, 'fourlevel' ) );
			$fourlevelInfo = D ( 'Basic' )->getFourlevelByID ( $parentId );
			if ($fourlevelInfo) {
				$name = $fourlevelInfo ['name'];
				$this->assign ( 'name', $name );
			}
		}
		$nextFourlevelId = D ( 'Basic' )->getNextFourlevelId();
		$this->assign ( 'path', $path );
		$this->assign ( 'subjectid', $subjectId );
		$this->assign ( 'level', $level );
		$this->assign ( 'parentId', $parentId );
		$this->assign ( 'nextFourlevelId', $nextFourlevelId );
		$this->display ();
	}
	
	public function fourlevel_add_save() {
		$params = $this->_post ();
		$rs = D ( 'Basic' )->addFourLevel ( $params );
		if ($rs) {
			$this->success ();
		}
		$this->error ();
	}
	
	public function fourlevels() {
		$params = $this->_get ();
		$subjectId = $params ['subjectid'];

		$this->assign ( 'subjectid', $subjectId );
		$this->display ();
	}
	
	
	public function getFourlevels() {
		$params = $this->_param ();
		$parentId = $params ['id'];
		$subjectId = $params ['subjectid'];
		$fourlevels = D ( 'Basic' )->getFourlevesByParentId ( $parentId, $subjectId );
		$this->outPut ( $fourlevels );
	}
	
	public function fourlevel_edit() {
		$params = $this->_get ();
		$id = $params ['id'];
		$subjectId = $params ['subjectid'];

		$fourlevelInfo = D ( 'Basic' )->getFourlevelByID ( $id );
		$now = date('Ymd');
		$this->assign ( 'subjectid', $subjectId );
		$this->assign ( 'fourlevelInfo', $fourlevelInfo );
		$this->display ();
	}
	public function fourlevel_edit_save() {
		$params = $this->_post ();

		$rs = D ( 'Basic' )->updateFourlevel ( $params );
		if ($rs) {
			$this->success ();
		}
		$this->error ();
	}
	
	
	public function fourlevel_delete(){
		$params = $this->_post ();

		$rs = D ( 'Basic' )->deleteFourlevel ( $params );
		if ($rs) {
			$this->success ();
		}
		$this->error ();
	}
	
	
	public function getFourlevelsChilds() {
		$params = $this->_param ();
		$parentId = $params ['id'];
		$subjectId = $params ['subjectid'];
		$fourlevels = D ( 'Basic' )->getFourlevelsByParentIdChild ( $parentId, $subjectId );
		$this->outPut ( $fourlevels );
	}
	
	
	public function getFourlevels2() {
		$params = $this->_param ();

		$parentId = $params ['id'];
		$subjectId = $params ['subjectid'];
		$fourlevels = D ( 'Basic' )->getFourlevelsByParentId2 ( $parentId, $subjectId );
		$this->outPut ( $fourlevels );
	}
	
	
	//四级体系匹配五级知识点
	public function fourlevel_match(){
		$params = $this->_get ();
		$parentId = $params ['pid'];
		$subjectId = $params ['subjectid'];
		$level = $params ['level'];
		$path = '/';
		if (! empty ( $parentId )) {
			$path = arr2nav ( D ( 'Basic' )->getPathForFourlevel ( $parentId, 'fourlevel' ) );
			$fourlevel = D ( 'Basic' )->getFourlevelByID ( $parentId );
			if ($fourlevel) {
				$name = $fourlevel ['name'];
				$this->assign ( 'name', $name );
			}
		}
		$nextFourlevelId = D ( 'Basic' )->getNextFourlevelId();
		$this->assign ( 'path', $path );
		$this->assign ( 'subjectid', $subjectId );
		$this->assign ( 'level', $level );
		$this->assign ( 'parentId', $parentId );
		$this->assign ( 'nextFourlevelId', $nextFourlevelId );
		$this->display ();
	}


	public function fourlevel_match_save() {
		$params = $this->_post ();
		$basicModel = D ( 'Basic' );
		$knowledgeInfo = $basicModel->getKnowledgeByID($params['origin_knowledge_id']);
		$isExist = $basicModel->checkFourlevelIsExist( $knowledgeInfo,$params['pid'] );
		if($isExist == true){
			$this->outPut ( array (
				'status' => false,
				'message' => '该名称已存在'  
			) );
		}else{
			$rs = $basicModel->matchFourlevel ( $params );
			if ($rs) {
				$this->success ();
			}
			$this->error ();
		}

	}
	
	
	public function fourlevels3() {
		$params = $this->_get ();
		$basicModel = D ( 'Basic' );
		$subjectId = $params ['subjectid'];
		$is_gaosi = $params ['is_gaosi'];
		$pid = $params ['pid'];
		$level = $params ['level'];
		$knowledgeType = $basicModel->getKnowledgeTypeBySubjectIdAndIsGaosi($params);
		$basecoursetypes = $basicModel->getCourseTypesBySubjectIdAndKnowledgeTypeId ( $subjectId, $knowledgeType['id'] );
		$this->assign ( 'subjectid', $subjectId );
		$this->assign ( 'knowledgetypeid', $knowledgeType['id'] );
		$this->assign ( 'basecoursetypes', $basecoursetypes );
		$this->assign ( 'pid', $pid );
		$this->assign ( 'level', $level );
		$this->display ();
	}
	
	
	public function getKnowledgeTypeBySubjectIdAndIsGaosi($params){
		$knowledgeType =  D ( 'Basic' )->getKnowledgeTypeBySubjectIdAndIsGaosi ($params ) ;
	}
	
	
	
	public function view_fourlevel_questions() {
		$params = $this->_get ();
		$fourlevelId = $params ['fourlevel_id'];
		$basicModel = D ( 'Basic' );
		$originKnowledgeId = $basicModel->getOriginKnowledgeIdByFourlevelId($fourlevelId);
		$questions = $basicModel->getQuestionsByKnowledgeId ( $originKnowledgeId );
		$this->assign ( 'questions', $questions );
		$this->display ();
	}


	


}
?>