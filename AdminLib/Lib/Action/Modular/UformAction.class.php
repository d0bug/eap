<?php
class UformAction extends ModularCommAction {	
	public function main() {
		$actListUrl = $this->getUrl('jsonFormList');
		$permInfo = Permission::getPermInfo($this->loginUser, $this->getAclKey());
		$permValue = $permInfo['permValue'];
		$addActUrl = $this->getUrl('add');
		$editActUrl = $this->getUrl('edit');
		$formUrl = $this->getUrl('form');
		$jsonActInfoUrl = $this->getUrl('jsonActInfo');
		$jsonRecordUrl = $this->getUrl('jsonRecordList');
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function jsonFormList() {
		if(false == $this->readCheck($this->getAclKey('main'))) {die('access denied');}
		$formModel = D('UForm');
		$currentPage = abs($_POST['page']);
		$pageSize = abs($_POST['rows']);
		echo json_encode(array('total'=>$formModel->getFormCount(), 'rows'=>$formModel->getFormList($currentPage, $pageSize)));
		exit;
	}
	
	protected function add() {
		if(false == $this->writeCheck($this->getAclKey('main'))) {die('access denied');}
		if($this->isPost()) {
			$formModel = new UFormModel();
			$saveResult = $formModel->save($_POST);
			echo json_encode($saveResult);
			exit;
		}
		$dialog = SysUtil::safeString($_GET['dlg']);
		$gradeModel = D('GradeYear');
		$gradeYears = $gradeModel->getGradeYears();
		$dTypeModel = D('DType');
		$dTypeArray = $dTypeModel->getDTypes();
		$addAttrUrl = $this->getUrl('addAttr');
		$addActUrl = $this->getUrl('add');
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function edit() {
		if(false == $this->writeCheck($this->getAclKey('main'))) {die('access denied');}
		$formModel = new UFormModel();
		if($this->isPost()) {
			$saveResult = $formModel->save($_POST);
			echo json_encode($saveResult);
			exit;
		}
		$gradeModel = D('GradeYear');
		$gradeYears = $gradeModel->getGradeYears();
		$dTypeModel = D('DType');
		$dTypeArray = $dTypeModel->getDTypes();
		
		$addAttrUrl = $this->getUrl('addAttr');
		$editActUrl = $this->getUrl();
		$actId = SysUtil::uuid($_GET['id']);
		$dialog = SysUtil::safeString($_GET['dlg']);
		$actInfo = $formModel->formInfo($actId);
		foreach ($actInfo['attrList'] as $attrName=>$attr) {
			$renderResult = $dTypeModel->renderOpts($attr['attr_type'], $attr['attr_name']);
			$actInfo['attrList'][$attrName]['opt_html'] = $renderResult['html'];
		}		
		
		$jsonAttr = json_encode($actInfo['attrList']);
		$this->assign(get_defined_vars());
		$this->display();
	}
	
	protected function addAttr() {
		$dTypeModel = D('DType');
		$typeName = trim($_POST['attrType']);
		$attrName = trim($_POST['attrName']);
		echo json_encode($dTypeModel->renderOpts($typeName, $attrName));
	}
	
	protected function del() {
		if(false == $this->writeCheck($this->getAclKey('main'))) {die('access denied');}
		
	}
	
	
	protected function form() {
		$formId = SysUtil::uuid($_GET['id']);
		$rpcClient = SysUtil::getRpcClient('Uform');
		$formInfo = $rpcClient->getFormHtml($formId);
		if(is_array($formInfo)) {
			if($formInfo['errorMsg']) {
				die($formInfo['errorMsg']);
			} else {
				echo '<textarea style="width:600px;height:300px;resize:none" readonly="true">';
				$formInfo = var_export($formInfo, true);
				echo $formInfo;
				echo '</textarea>';
			}
		} else if(is_string($formInfo)) {
			if(strtolower($_GET['renderType']) == 'js') {
				echo 'document.write(' . json_encode($formInfo) . ');';
			} else {
				echo $formInfo;
			}
		} else {
			die('活动已结束');
		}
	}
	
	protected function jsonActInfo() {
		$formId = SysUtil::uuid($_POST['actId']);
		$uFormModel = D('UForm');
		$formSearchHtml = $uFormModel->getFormSearchHtml($formId);
		$formColumns = $uFormModel->getFormColumns($formId);
		echo json_encode(array('searchForm'=>$formSearchHtml, 'columns'=>$formColumns));
	}
	
	protected function jsonRecordList() {
		$formId = SysUtil::uuid($_GET['id']);
		$uFormModel = D('UForm');
		$condition = $uFormModel->getSearchCondition($formId, $_GET);
		$recordCount = $uFormModel->getRecordCount($condition);
		$recordList = $uFormModel->getRecordList($condition, abs($_POST['page']), abs($_POST['rows']));
		echo json_encode(array('total'=>$recordCount, 'rows'=>$recordList));
	}
	
	protected function exportRecord() {
		$formId = SysUtil::uuid($_GET['id']);
		$uFormModel = D('UForm');
		$condition = $uFormModel->getSearchCondition($formId, array());
		$recordCount = $uFormModel->getRecordCount($condition);
		$recordCount = $uFormModel->getRecordList($condition, 1, $recordCount);
		
	}
}
?>