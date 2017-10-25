<?php
class UFormModel extends CommModel {
	private $tableName = 'mdl_act_forms';
	private $attrTable = 'mdl_form_attrs';
	private $recordTable = 'uform_records';
	private $templateDir = '';
	private $handlerPre = '';
	
	public function __construct() {
		parent::__construct();
		$this->templateDir = dirname(APP_PATH) . '/FormTemplates/';
		$this->handlerPre = C('UFORM_HANDLER_PRE');
	}
	
	public function mongoDao() {
		static $mongoDao = null;
		if($mongoDao === null ) 
			$mongoDao = Dao::getDao('MONGO_CONN');
			
		return $mongoDao;
	}
	
	public function __get($propName) {
		static $propArray = array();
		if(isset($propArray[$propName])) {
			return $propArray[$propName];
		}
		if(method_exists($this, $propName)) {
			$propArray[$propName] = $this->$propName();
			return $propArray[$propName];
		}
		
		return null;
	}
	
	public function getFormCount() {
		$strQuery = 'SELECT COUNT(1) FROM ' . $this->tableName . '
					 WHERE is_remove=0';
		return $this->dao->getOne($strQuery);
	}
	
	public function getFormList($currentPage, $pageSize) {
		$strQuery = 'SELECT * FROM ' . $this->tableName . '
					 WHERE is_remove=0';
		$order = 'ORDER BY create_at DESC';
		$actList = $this->dao->getLimit($strQuery, $currentPage, $pageSize, $order);
		return $actList;
	}
	
	public function formInfo($actId) {
		static $fInfo = null;
		if(null !== $fInfo) return $fInfo;
		$actId = SysUtil::uuid($actId);
		$strQuery = 'SELECT * FROM ' . $this->tableName . '
					 WHERE act_id=' . $this->dao->quote($actId) . '
					   AND is_remove=0';
		$actInfo = $this->dao->getRow($strQuery);
		if($actInfo) {
			$actInfo['act_grades'] = explode(',', $actInfo['act_grades']);
			$strQuery = 'SELECT * FROM ' . $this->attrTable . '
						 WHERE act_id=' . $this->dao->quote($actId) . '
						 ORDER BY attr_seq';
			$attrList = $this->dao->getAll($strQuery);
			$attrArray = array();
			foreach ($attrList as $attr) {
				$attr['attr_opts'] = SysUtil::jsonDecode($attr['attr_opts']);
				$attrArray[$attr['attr_name']] = $attr;
			}
			
			$actInfo['attrList'] = $attrArray;
		}
		$fInfo = $actInfo;
		return $actInfo;
	}
	
	public function save($actInfo) {
		$this->dao->begin();
		$actId = SysUtil::uuid($actInfo['actId']);
		$dbActInfo = $this->formInfo($actId);
		$time = date('Y-m-d H:i:s');
		$postActInfo = array('act_title'=>SysUtil::safeString($actInfo['actTitle']),
							 'act_start'=>$actInfo['actStart'],
							 'act_end'=>$actInfo['actEnd'],
							 'act_grades'=>$actInfo['actGrades'],
							 'act_tpl'=>$actInfo['actTemplate'],
							 'act_content'=>SysUtil::safeString($actInfo['actContent']));
		if($dbActInfo) {
			$strQuery = 'DELETE FROM ' . $this->attrTable . '
						 WHERE act_id=' . $this->dao->quote($actId);
			$this->dao->execute($strQuery);
			$strQuery = 'UPDATE ' . $this->tableName . ' SET ';
			foreach ($postActInfo as $column=>$value) {
				$strQuery .= $column . '=' . $this->dao->quote($value) . ',';
			}
			$strQuery .= 'update_at=' . $this->dao->quote($time) . ',
						  update_user=' . $this->dao->quote($this->operator) . '
						 WHERE act_id=' . $this->dao->quote($actId);
		} else {
			$actId = SysUtil::uuid();
			$postActInfo['act_id'] = $actId;
			$strQuery = 'INSERT INTO ' . $this->tableName;
			$fieldsArray = array();
			$valueArray = array();
			foreach ($postActInfo as $column=>$value) {
				$fieldsArray[] = $column;
				$valueArray[] = $this->dao->quote($value);
			}
			$fieldsArray[] = 'create_at';
			$fieldsArray[] = 'update_at';
			$fieldsArray[] = 'create_user';
			$fieldsArray[] = 'update_user';
			$valueArray[] = $this->dao->quote($time);
			$valueArray[] = $this->dao->quote($time);
			$valueArray[] = $this->dao->quote($this->operator);
			$valueArray[] = $this->dao->quote($this->operator);
			$strQuery .= '(' . implode(',', $fieldsArray) . ') VALUES (' . implode(',', $valueArray) . ')';
		}
		
		if($this->dao->execute($strQuery)) {
			if(sizeof($actInfo['attrType']) > 0) {
				foreach ($actInfo['attrType'] as $attrName=>$attrType) {
					$attrSeq = abs($actInfo['attrSeq'][$attrName]);
					$attrCaption = $actInfo['attrCaption'][$attrName];
					if($actInfo[$attrName]) {
						$attrOpts = SysUtil::jsonEncode($actInfo[$attrName]);
					} else {
						$attrOpts = '{}';
					}
					$strQuery = 'INSERT INTO ' . $this->attrTable . '
								 (id,act_id,attr_name, attr_type,attr_caption,attr_seq, attr_opts)
								 VALUES (' . $this->dao->quote(SysUtil::uuid()) . ',
								 		 ' . $this->dao->quote($actId) . ',
								 		 ' . $this->dao->quote($attrName) . ',
								 		 ' . $this->dao->quote($attrType) . ',
								 		 ' . $this->dao->quote($attrCaption) . ',
								 		 ' . $this->dao->quote($attrSeq) . ',
								 		 ' . $this->dao->quote($attrOpts) . ')';
					if(false == $this->dao->execute($strQuery)) {
						$this->dao->rollback();
						return array('errorMsg'=>'活动属性设置失败');
					}
				}
			}
			$this->dao->commit();
			return array('success'=>true);
		} else {
			$this->dao->rollback();
			return array('errorMsg'=>'活动添加失败,请不要重复添加');
		}
	}
	
	public function renderFormInfo($formId, $signuped=false, $isSearch=false) {
		$formInfo = $this->formInfo($formId);
		if(false == $formInfo) return array();
		$formAttrId = 'form_' . md5($formInfo['act_id']);
		$formAction = $this->handlerPre . 'save';
		$returnFormInfo = array('formId'=>$formInfo['act_id'],
								'formAttrId'=>$formAttrId,
								'formAction'=>$formAction,
								'formTitle'=>$formInfo['act_title'],
								'formContent'=>$formInfo['act_content'],
								'formSignuped'=>$signuped,
								'formItems' => array(),
								'formScript'=>'<script type="text/javascript">
								jQuery(function(){
									jQuery("#form_' . md5($formInfo['act_id']) . '").submit(function(){
										var submitable = true;
										jQuery(this).find(".uform_required").each(function(){
											if(submitable) {
												if(this.type == "text" || this.type == "tel" || this.tagName == "textarea") {
													if("" == jQuery.trim(jQuery(this).val())) {
														submitable = false;
														alert("表单数据不完整");
													}
												}
											}
										})
										if(submitable) {
											jQuery(this).find("select").each(function(){
												if(this.value == "-9999") {
													submitable = false;
													alert("表单数据不完整");
												}
											})
										}
										if(submitable) {
											var itemNames = {};
											jQuery(this).find("input:checkbox, input:radio").each(function(){
												itemName = jQuery(this).attr("itemName");
												if(!itemNames[itemName]) {
													itemNames[itemName] = 0;
												}
												if(this.checked) {
													itemNames[itemName] += 1;
												}
											})
											jQuery.each(itemNames, function(name, cnt){
												if(submitable)  {
													if(cnt == 0) {
														submitable = false;
														alert("表单数据不完整");
													}
												}
											})
										}
										jQuery(this).find(".uform_telephone").each(function(){
											if(submitable &&this.value && false == /^1\d{10}$/.test(this.value)) {
												alert(this.name)
												submitable = false;
												alert("手机号码格式不正确，请重新输入");
												this.value=""
											}
										})
										
										if(submitable) {
											/*jQuery.post("' . $formAction . '", jQuery("#' . $formAttrId . '").serialize(), function(data){
												if(data.errorMsg) {
													alert(data.errorMsg);
												} else {
													alert("活动报名成功");
													location.reload();
												}
											}, "json");
											*/
										}
										return submitable;
									})
								})
								</script>',
								);
		$date = date('Y-m-d');
		if($date < $formInfo['act_start']){
			 $returnFormInfo['form_body'] = '活动尚未开始';
		} else if($date >$formInfo['act_end']) {
			$returnFormInfo['form_body'] = '活动已经结束';
		} else {
			$dtypeModel = D('DType');
			$idItem = array('attr_type'=>'hidden', 'attr_name'=>'form_id', 'attr_opts'=>array('value'=>$formInfo['act_id']));
			$returnFormInfo['formItems']['form_id'] = $dtypeModel->renderItem($idItem);
			foreach ($formInfo['attrList'] as $attr) {
				if($attr['attr_type'] == 'grade') {
					$attr['attr_opts']['data_source'] = $formInfo['act_grades'];
					$attr['attr_opts']['render_type'] = 'select';
				}
				$returnFormInfo['formItems'][$attr['attr_name']] = $dtypeModel->renderItem($attr);
				if($returnFormInfo['formItems'][$attr['attr_name']] === false) {
					$this->stopAct($formId);
					return null;
				}
			}
		}
		
		return $returnFormInfo;
	}
	
	private function stopAct($actId) {
		$strQuery = 'UPDATE ' . $this->tableName . '
					 SET act_status=0
					 WHERE act_id=' . $this->dao->quote($actId);
		$this->dao->execute($strQuery);
	}
	
	public function renderFormHtml($formId, $signuped=false) {
		$formInfo = $this->renderFormInfo($formId, $signuped);
		if (null === $formInfo) return null;
		$tplFile = $this->templateDir . '/' . $formInfo['act_tpl'];
		if($formInfo['act_tpl'] && file_exists($tplFile)) {
			ob_clean();
			ob_start();
			include($tplFile);
			$html = ob_get_contents();
			ob_clean();
			return $html;
		}
		return $formInfo;
	}
	
	public function findLevelAct($dept, $level, $filter) {
		$gradeYearModel = D('GradeYear');
		$gradeYears = $gradeYearModel->getGradeYears();
		$levelYears = array();
		$maxYear = null;
		$minYear = null;
		foreach ($gradeYears as $year=>$grade) {
			$minYear = $year;
			if($maxYear === null) $maxYear = $year;
			if(preg_match('/初中/', $grade)) {
				$levelGrades[2][] = $year;
			} else if (preg_match('/高中/', $grade)) {
				$levelGrades[3][] = $year;
			} else {
				$levelGrades[1][] = $year;
			}
		}
		$levelGrades = $levelGrades[$level];
		$date = date('Y-m-d');
		$strQuery = 'SELECT * FROM ' . $this->tableName . '
					 WHERE is_remove=0 
					   AND act_status=1
					   AND act_start <=' . $this->dao->quote($date) . '
					   AND act_end >=' . $this->dao->quote($date) . '
					   AND (';
		$condArr = array();
		foreach ($levelGrades as $grade) {
			$condArr[] = 'act_grades LIKE ' . $this->dao->quote('%' . $grade . '%');
		}
		$strQuery .= implode(' OR ', $condArr);
		$strQuery .= ')';
		
		$order = 'ORDER BY create_at';
		$actList = $this->dao->getLimit($strQuery, 1, 20, $order);
		if($actList) {
			$actIdArray = array();
			foreach ($actList as $act) {
				$actIdArray[$act['act_id']] = 0;
			}
			$filterActArray = $this->filterActArray($actIdArray, $filter);
			if($filterActArray) {
				foreach ($filterActArray as $actId=>$signuped){break;}
				return $this->renderFormHtml($actId, $signuped);
			} 
		}
		return array();
	}
	
	public function findGradeAct($dept, $grade, $filter) {
		$date = date('Y-m-d');
		$strQuery = 'SELECT * FROM ' . $this->tableName . '
					 WHERE is_remove=0
					   AND act_start <=' . $this->dao->quote($date) . '
					   AND act_end >=' . $this->dao->quote($date) . '
					   AND act_status=1
					   AND act_grades LIKE ' . $this->dao->quote('%' . $grade . '%');
		$order = 'ORDER BY create_at';
		
		$actList = $this->dao->getLimit($strQuery, 1, 20, $order);
		if($actList) {
			$actIdArray = array();
			foreach ($actList as $act) {
				$actIdArray[$act['act_id']] = 0;
			}
			$filterActArray = $this->filterActArray($actIdArray, $filter);
			if($filterActArray) {
				foreach ($filterActArray as $actId=>$signuped){break;}
				return $this->renderFormHtml($actId, $signuped);
			}
		}
		return array();
	}
	
	private function filterActArray($actIdArray, $filter) {
		$filter['form_id'] = array('$in'=>array_keys($actIdArray));
		$userActList = $this->mongoDao->getAll($this->recordTable, $filter);
		foreach ($userActList as $record) {
			$actIdArray[$record['form_id']] = 1;
		}
		return $actIdArray;
	}
	
	public function saveRecord($record) {
		if($record['filter']) {
			$condition = array('form_id'=>trim($record['form_id']),
						  	   'filter'=>trim($record['filter']));
			$count = $this->mongoDao->count($this->recordTable, $condition);
			if($count > 0) {
				return array('errorMsg'=>'已存在报名记录，请不要重复报名');
			}
		}
		$validResult = $this->validRecord($record);
		if($validResult === true) {
			return $this->mongoDao->save($this->recordTable, $record);
		} else {
			if(is_array($validResult)) return $validResult;
			return array('errorMsg'=>'报名失败,表单数据不完整');
		}
		return array('errorMsg'=>'报名失败，请联系管理员');
	}
	
	private function validRecord($record) {
		$formId = trim($record['form_id']);
		$formInfo = $this->formInfo($formId);
		foreach ($formInfo['attrList'] as $attr) {
			$opts = $attr['attr_opts'];
			$attrName = $attr['attr_name'];
			$attrType = $attr['attr_type'];
			if($opts['required']) {
				if(false == isset($record[$attrName])) {
					return false;
				} else {
					switch ($attrType) {
						case 'options':
							if($record[$attrName] == '-9999') {
								return false;
							}
						break;
						case 'telephone':
							if(false == preg_match('/^1\d{10}$/', $record[$attrName])) {
								return array('errorMsg'=>'手机号码格式不正确');
							}
						break;
						default:
							if(false == trim($record[$attrName])) {
								return false;
							}
						break;
					}
				}
			}
		}
		return true;
	}
	
	public function getFormSearchHtml($formId) {
		$formInfo = $this->renderFormInfo($formId);
		return '自定义表单搜索功能暂未开发';
		return $formInfo;
	}
	
	public function getFormColumns($formId) {
		$formInfo = $this->formInfo($formId);
		$columns = array();
		foreach ($formInfo['attrList'] as $attr) {
			$columns[] = array('field'=>$attr['attr_name'] . '__value', 'title'=>$attr['attr_caption']);
		}
		return $columns;
	}
	
	public function getSearchCondition($formId, $searchArgs) {
		$searchCondition = array();
		$searchCondition['form_id'] = $formId;
		return $searchCondition;
	}
	
	public function getRecordCount($searchCondition) {
		static $condCounts = array();
		$key = md5(serialize($searchCondition));
		if(false == isset($condCounts[$key])) {
			$condCounts[$key] = $this->mongoDao->count($this->recordTable, $searchCondition);
		}
		return $condCounts[$key];
	}
	
	public function getRecordList($searchCondtion, $currentPage=1, $pageSize=20) {
		$recordCount = $this->getRecordCount($searchCondtion);
		$currentPage = abs($currentPage);
		$currentPage = $currentPage > 0 ? $currentPage : 1;
		$pageCount = ceil($recordCount / $pageSize);
		if($currentPage > $pageCount) $currentPage = $pageCount;
		$currentPage = $currentPage > 0 ? $currentPage : 1;
		$recordList =  $this->mongoDao->getLimit($this->recordTable, $searchCondtion, $currentPage, $pageSize);
		foreach ($recordList as $key=>$record) {
			$recordList[$key] = $this->renderValue($record);
		}
		return $recordList;
	}
	
	private function renderValue($record) {
		$formAttrs = $this->getFormAttrs($record['form_id']);
		foreach ($record as $attrName=>$attrValue) {
			if(isset($formAttrs[$attrName])) {
				switch ($formAttrs[$attrName]['attr_type']) {
					case 'grade':
					case 'options':
						if($formAttrs[$attrName]['attr_type'] == 'options') {
						$items = explode("\n", $formAttrs[$attrName]['attr_opts']['data_source']);
						$dataSource = array();
						foreach ($items as $item) {
							list($key, $value) = explode('|', $item);
							$dataSource[$key] = $value;
						}
						} else {
							$dataSource = $formAttrs[$attrName]['attr_opts']['data_source'];
						}
						$record[$attrName . '__value'] = $dataSource[$attrValue];
					break;
					default:
						$record[$attrName . '__value'] = $attrValue;
					break;
				}
			}
		}
		return $record;
	}
	
	private function getFormAttrs($formId) {
		static $formAttrs = array();
		if(false == isset($formAttrs[$formId])) {
			$formInfo = $this->formInfo($formId);
			foreach ($formInfo['attrList'] as $attr) {
				if($attr['attr_type'] == 'grade') {
					$gradeModel = D('GradeYear');
					$attr['attr_opts']['data_source'] = $gradeModel->getGradeYears();
				}
				$formAttrs[$formId][$attr['attr_name']] = $attr;
			}
		}
		
		return $formAttrs[$formId];
	}
	
	public function getFreeOptions($item, $opts) {
		$dataSource = $opts['data_source'];
		$sourceArray = array();
		foreach ($dataSource as $value=>$text) {
			$sourceArray[$value] = array($value, $text);
		}
		$limitArray = $opts['data_limit'];
		$formId = $item['act_id'];
		$mongoDao = $this->mongoDao();
		foreach ($limitArray as $value=>$limit) {
			$condition = array('form_id'=>$formId, $item['attr_name']=>strval($value));
			$count = $mongoDao->count($this->recordTable, $condition);
			if($count >= abs($limit)) {
				unset($sourceArray[$value]);
			}
		}
		$dataSource = array();
		foreach ($sourceArray as $source) {
			$dataSource[$source[0]] = $source[1];
		}
		return $dataSource;
	}
	
	
}
?>